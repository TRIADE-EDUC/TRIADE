<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collection.class.php,v 1.108.4.1 2025/02/28 13:38:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Ark\Entities\ArkEntityPmb;
// définition de la classe de gestion des collections

if ( ! defined( 'COLLECTION_CLASS' ) ) {
  define( 'COLLECTION_CLASS', 1 );

  global $class_path, $include_path;
  
require_once($class_path."/notice.class.php");
require_once("$class_path/aut_link.class.php");
require_once("$class_path/aut_pperso.class.php");
require_once($class_path."/editor.class.php");
require_once($class_path."/subcollection.class.php");
require_once("$class_path/audit.class.php");
require_once($class_path."/index_concept.class.php");
require_once($class_path."/vedette/vedette_composee.class.php");
require_once($class_path.'/authorities_statuts.class.php');
require_once($class_path."/indexation_authority.class.php");
require_once($class_path."/authority.class.php");
require_once ($class_path.'/indexations_collection.class.php');
require_once ($class_path.'/authorities_collection.class.php');
require_once ($class_path.'/indexation_stack.class.php');
require_once ($class_path.'/interface/entity/interface_entity_collection_form.class.php');

class collection {

	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------

	public $id;		// MySQL id in table 'collections'
	public $name;		// collection name
	public $parent;	// MySQL id of parent publisher
	public $editeur;	// name of parent publisher
	public $editor_isbd; // isbd form of publisher
	public $display;	// usable form for displaying	( _name_ (_editeur_) )
	public $isbd_entry = ''; // isbd form
	public $issn;		// ISSN of collection
	public $isbd_entry_lien_gestion ; // lien sur le nom vers la gestion
	public $collection_web;		// web de collection
	public $collection_web_link;	// lien web de collection
	public $num_statut = 1; //Statut de la collection
	public $cp_error_message = ''; //Messages d'erreur de l'enregistrement des champs persos
	public $comment = '';
	protected static $long_maxi_name;
	protected static $controller;
	
	// ---------------------------------------------------------------
	//		collection($id) : constructeur
	// ---------------------------------------------------------------
	public function __construct($id=0) {
	    $this->id = intval($id);
		$this->getData();
	}
	
	// ---------------------------------------------------------------
	//		getData() : récupération infos collection
	// ---------------------------------------------------------------
	public function getData() {
		global $charset;
		$this->name		=	'';
		$this->parent	=	0;
		$this->editeur	=	'';
		$this->editor_isbd = '';
		$this->display	=	'';
		$this->issn		=	'';
		$this->collection_web = '';
		$this->collection_web_link = "" ;
		$this->comment = "" ;
		$this->num_statut = 1;
		if($this->id) {
			$requete = "SELECT * FROM collections WHERE collection_id='".$this->id."'";
			$result = @pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				pmb_mysql_free_result($result);
				
				$this->id = $row->collection_id;
				$this->name = $row->collection_name;
				$this->parent = $row->collection_parent;
				$this->issn = $row->collection_issn;
				$this->collection_web	= $row->collection_web;
				$this->comment	= $row->collection_comment;
				$authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [ 'num_object' => $this->id, 'type_object' => AUT_TABLE_COLLECTIONS]);
				$this->num_statut = $authority->get_num_statut();
				if($row->collection_web) 
					$this->collection_web_link = " <a href='$row->collection_web' target=_blank title='".htmlentities($row->collection_web,ENT_QUOTES,$charset)."' alt='".htmlentities($row->collection_web,ENT_QUOTES,$charset)."'><img src='".get_url_icon("globe.gif")."' style='border:0px;' /></a>";
				$editeur = authorities_collection::get_authority(AUT_TABLE_PUBLISHERS, $row->collection_parent);
				$this->editor_isbd = $editeur->get_isbd();
				$this->issn ? $this->isbd_entry = $this->name.', ISSN '.$this->issn : $this->isbd_entry = $this->name;
				$this->editeur = $editeur->name;
				$this->display = $this->name.' ('.$this->editeur.')';
				if($this->editeur) {
					$this->isbd_entry .= ' ('.$this->editeur.')';
				}
				
				// Ajoute un lien sur la fiche collection si l'utilisateur à accès aux autorités
				// defined('SESSrights') dans le cas de l'indexation il 'y a pas de AUTH ni de session
				if (defined('SESSrights') && ( intval(SESSrights) & AUTORITES_AUTH) ){
				    $this->isbd_entry_lien_gestion = "<a href='./autorites.php?categ=see&sub=collection&id=".$this->id."' class='lien_gestion'>".$this->name."</a>";
				} else {
				    $this->isbd_entry_lien_gestion = $this->name;
				}
			}
		}
	}
	
	public function build_header_to_export() {
	    global $msg;
	    
	    $data = array(
	        $msg[67],	        
	        $msg['isbd_editeur'],
	        $msg[165],
	        $msg[147],	        
	        $msg[707],
	        $msg[4019],
	    );
	    return $data;
	}
	
	public function build_data_to_export() {
	    $data = array(
	        $this->name,	        
	        $this->editor_isbd,
	        $this->issn,
	        $this->collection_web,	        
	        $this->comment,
	        $this->num_statut,
	    );
	    return $data;
	}
	
	// ---------------------------------------------------------------
	//		delete() : suppression de la collection
	// ---------------------------------------------------------------
	public function delete() {
		global $msg;
	
		if(!$this->id)
			// impossible d'accéder à cette notice de collection
			return $msg[406];

		if(($usage=aut_pperso::delete_pperso(AUT_TABLE_COLLECTIONS, $this->id,0) )){
			// Cette autorité est utilisée dans des champs perso, impossible de supprimer
			return '<strong>'.$this->display.'</strong><br />'.$msg['autority_delete_error'].'<br /><br />'.$usage['display'];
		}
		
		// récupération du nombre de notices affectées
		$requete = "SELECT COUNT(1) FROM notices WHERE ";
		$requete .= "coll_id=$this->id";
		$res = pmb_mysql_query($requete);
		$nbr_lignes = pmb_mysql_result($res, 0, 0);
		if(!$nbr_lignes) {
			// on regarde si la collection a des collections enfants 
			$requete = "SELECT COUNT(1) FROM sub_collections WHERE ";
			$requete .= "sub_coll_parent=".$this->id;
			$res = pmb_mysql_query($requete);
			$nbr_lignes = pmb_mysql_result($res, 0, 0);
			if(!$nbr_lignes) {

				// On regarde si l'autorité est utilisée dans des vedettes composées
				$attached_vedettes = vedette_composee::get_vedettes_built_with_element($this->id, TYPE_COLLECTION);
				if (count($attached_vedettes)) {
					// Cette autorité est utilisée dans des vedettes composées, impossible de la supprimer
					return '<strong>'.$this->display."</strong><br />".$msg["vedette_dont_del_autority"].'<br/>'.vedette_composee::get_vedettes_display($attached_vedettes);
				}
				
				// effacement dans la table des collections
				$requete = "DELETE FROM collections WHERE collection_id=".$this->id;
				pmb_mysql_query($requete);
				//Import d'autorité
				collection::delete_autority_sources($this->id);
				// liens entre autorités
				$aut_link= new aut_link(AUT_TABLE_COLLECTIONS,$this->id);
				$aut_link->delete();
				$aut_pperso= new aut_pperso("collection",$this->id);
				$aut_pperso->delete();
				
				// nettoyage indexation concepts
				$index_concept = new index_concept($this->id, TYPE_COLLECTION);
				$index_concept->delete();
				
				// nettoyage indexation
				indexation_authority::delete_all_index($this->id, "authorities", "id_authority", AUT_TABLE_COLLECTIONS);
				
				// effacement de l'identifiant unique d'autorité
				$authority = new authority(0, $this->id, AUT_TABLE_COLLECTIONS);
				$authority->delete();
				
				audit::delete_audit(AUDIT_COLLECTION,$this->id);
				return false;
			} else {
				// Cet collection a des sous-collections, impossible de la supprimer
				return '<strong>'.$this->display."</strong><br />{$msg[408]}";
			}
		} else {
			// Cette collection est utilisé dans des notices, impossible de la supprimer
			return '<strong>'.$this->display."</strong><br />{$msg[407]}";
		}
	}
	
	// ---------------------------------------------------------------
	//		delete_autority_sources($idcol=0) : Suppression des informations d'import d'autorité
	// ---------------------------------------------------------------
	public static function delete_autority_sources($idcol=0){
		$tabl_id=array();
		if(!$idcol){
			$requete="SELECT DISTINCT num_authority FROM authorities_sources LEFT JOIN collections ON num_authority=collection_id  WHERE authority_type = 'collection' AND collection_id IS NULL";
			$res=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res)){
				while ($ligne = pmb_mysql_fetch_object($res)) {
					$tabl_id[]=$ligne->num_authority;
				}
			}
		}else{
			$tabl_id[]=$idcol;
		}
		foreach ( $tabl_id as $value ) {
	       //suppression dans la table de stockage des numéros d'autorités...
			$query = "select id_authority_source from authorities_sources where num_authority = ".$value." and authority_type = 'collection'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while ($ligne = pmb_mysql_fetch_object($result)) {
					$query = "delete from notices_authorities_sources where num_authority_source = ".$ligne->id_authority_source;
					pmb_mysql_query($query);
				}
			}
			$query = "delete from authorities_sources where num_authority = ".$value." and authority_type = 'collection'";
			pmb_mysql_query($query);
		}
	}
	
	// ---------------------------------------------------------------
	//		replace($by) : remplacement de la collection
	// ---------------------------------------------------------------
	public function replace($by,$link_save=0) {
		global $msg;
		global $pmb_ark_activate;
	
		if(!$by) {
			// pas de valeur de remplacement !!!
			return "serious error occured, please contact admin...";
		}
	
		if (($this->id == $by) || (!$this->id))  {
			// impossible de remplacer une collection par elle-même
			return $msg[226];
		}
		// a) remplacement dans les notices
		// on obtient les infos de la nouvelle collection
		$n_collection = new collection($by);
		if(!$n_collection->parent) {
			// la nouvelle collection est foireuse
			return $msg[406];
		}
		
		$aut_link= new aut_link(AUT_TABLE_COLLECTIONS,$this->id);
		// "Conserver les liens entre autorités" est demandé
		if($link_save) {
			// liens entre autorités
			$aut_link->add_link_to(AUT_TABLE_COLLECTIONS,$by);		
		}
		$aut_link->delete();

		vedette_composee::replace(TYPE_COLLECTION, $this->id, $by);
		
		$requete = "UPDATE notices SET ed1_id=".$n_collection->parent.", coll_id=$by WHERE coll_id=".$this->id;
		pmb_mysql_query($requete);
	
		// b) remplacement dans la table des sous-collections
		$requete = "UPDATE sub_collections SET sub_coll_parent=$by WHERE sub_coll_parent=".$this->id;
		pmb_mysql_query($requete);
			
		//nettoyage d'autorities_sources
		$query = "select * from authorities_sources where num_authority = ".$this->id." and authority_type = 'collection'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				if($row->authority_favorite == 1){
					//on suprime les références si l'autorité a été importée...
					$query = "delete from notices_authorities_sources where num_authority_source = ".$row->id_authority_source;
					pmb_mysql_query($query);
					$query = "delete from authorities_sources where id_authority_source = ".$row->id_authority_source;
					pmb_mysql_query($query);
				}else{
					//on fait suivre le reste
					$query = "update authorities_sources set num_authority = ".$by." where id_authority_source = ".$row->id_authority_source;
					pmb_mysql_query($query);
				}
			}
		}		
		// nettoyage indexation concepts
		$index_concept = new index_concept($this->id, TYPE_COLLECTION);
		$index_concept->delete();
		
		//Remplacement dans les champs persos sélecteur d'autorité
		aut_pperso::replace_pperso(AUT_TABLE_COLLECTIONS, $this->id, $by);
		
		audit::delete_audit (AUDIT_COLLECTION, $this->id);
		
		// nettoyage indexation
		indexation_authority::delete_all_index($this->id, "authorities", "id_authority", AUT_TABLE_COLLECTIONS);
		if ($pmb_ark_activate) {
		    $idReplaced = authority::get_authority_id_from_entity($this->id, AUT_TABLE_COLLECTIONS);
		    $idReplacing = authority::get_authority_id_from_entity($by, AUT_TABLE_COLLECTIONS);
		    if ($idReplaced && $idReplacing) {
		        $arkEntityReplaced = ArkEntityPmb::getEntityClassFromType(TYPE_AUTHORITY, $idReplaced);
		        $arkEntityReplacing = ArkEntityPmb::getEntityClassFromType(TYPE_AUTHORITY, $idReplacing);
		        $arkEntityReplaced->markAsReplaced($arkEntityReplacing);
		    }
		}
		// effacement de l'identifiant unique d'autorité
		$authority = new authority(0, $this->id, AUT_TABLE_COLLECTIONS);
		$authority->delete();
		
		// c) suppression de la collection
		$requete = "DELETE FROM collections WHERE collection_id=".$this->id;
		pmb_mysql_query($requete);
		
		collection::update_index($by);
	
		return false;
	}
	
	protected function get_content_form() {
		global $charset, $thesaurus_concepts_active;
		global $collection_content_form;
		
		$content_form = $collection_content_form;
		
		//Nom
		$element = interface_entity_element::get_instance('el0Child_0_a', 'collection_nom', '714');
		$element->set_class('colonne2');
		$element->add_input_node('text', $this->name, ['data-pmb-deb-rech' => '1'])
		->set_class('saisie-30em');
		$content_form = str_replace('!!element_collection_nom!!', $element->get_display(), $content_form);
		
		//ISSN
		$element = interface_entity_element::get_instance('el0Child_0_b', 'issn', '165');
		$element->set_class('colonne2');
		$element->add_input_node('text', $this->issn)
		->set_class('saisie-20em')
		->set_maxlength(50);
		$content_form = str_replace('!!element_issn!!', $element->get_display(), $content_form);
		
		//Editeur
		$content_form = str_replace('!!ed_libelle!!', htmlentities($this->editeur,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!ed_id!!', $this->parent, $content_form);
		
		// Web
		$element = interface_entity_element::get_instance('el0Child_2', 'collection_web', '147');
		$element->add_input_node('url', $this->collection_web)
		->set_maxlength(255);
		$content_form = str_replace('!!element_collection_web!!', $element->get_display(), $content_form);
		
		//Commentaire
		$element = interface_entity_element::get_instance('el0Child_3', 'comment', 'collection_comment');
		$element->add_textarea_node($this->comment, 62, 4)
		->set_class('saisie-80em')
		->set_attributes(array('wrap' => 'virtual'));
		$content_form = str_replace('!!element_comment!!', $element->get_display(), $content_form);

		$content_form = str_replace('!!id!!', $this->id, $content_form);
		
		$aut_link= new aut_link(AUT_TABLE_COLLECTIONS,$this->id);
		$content_form = str_replace('<!-- aut_link -->', $aut_link->get_form('saisie_collection') , $content_form);
		
		$aut_pperso= new aut_pperso("collection",$this->id);
		$content_form = str_replace('!!aut_pperso!!', $aut_pperso->get_form(), $content_form);
		
		if($thesaurus_concepts_active == 1){
			$index_concept = new index_concept($this->id, TYPE_COLLECTION);
			$content_form = str_replace('!!concept_form!!',	$index_concept->get_form('saisie_collection'),$content_form);
		}else{
			$content_form = str_replace('!!concept_form!!', "", $content_form);
		}
		$authority = new authority(0, $this->id, AUT_TABLE_COLLECTIONS);
		$content_form = str_replace('!!thumbnail_url_form!!', thumbnail::get_form('authority', $authority->get_thumbnail_url()), $content_form);
		
		return $content_form;
	}
	
	public function get_form($duplicate = false) {
		global $msg;
		global $user_input, $nbr_lignes, $page;
		
		$interface_form = new interface_entity_collection_form('saisie_collection');
		if(isset(static::$controller) && is_object(static::$controller)) {
			$interface_form->set_controller(static::$controller);
		}
		$interface_form->set_enctype('multipart/form-data');
		if($this->id && !$duplicate) {
			$interface_form->set_label($msg['168']);
			$interface_form->set_document_title($this->name.' - '.$msg['168']);
		} else {
			$interface_form->set_label($msg['167']);
			$interface_form->set_document_title($msg['167']);
		}
		$interface_form->set_object_id($this->id)
		->set_num_statut($this->num_statut)
		->set_content_form($this->get_content_form())
		->set_table_name('collections')
		->set_field_focus('collection_nom')
		->set_url_base(static::format_url());
		
		$interface_form->set_page($page)
		->set_nbr_lignes($nbr_lignes)
		->set_user_input($user_input);
		return $interface_form->get_display();
	}
	
	// ---------------------------------------------------------------
	//		show_form : affichage du formulaire de saisie
	// ---------------------------------------------------------------
	public function show_form($duplicate = false) {
		print $this->get_form($duplicate);
	}
	
	// ---------------------------------------------------------------
	//		replace_form : affichage du formulaire de remplacement
	// ---------------------------------------------------------------
	public function replace_form()	{
		global $collection_replace_content_form;
		global $msg;
		global $include_path;
	
		if(!$this->id || !$this->name) {
			require_once("$include_path/user_error.inc.php"); 
			error_message($msg[161], $msg[162], 1, static::format_url('&sub=&id='));
			return false;
		}
	
		$content_form = $collection_replace_content_form;
		$content_form = str_replace('!!id!!', $this->id, $content_form);
		
		$interface_form = new interface_autorites_replace_form('coll_replace');
		$interface_form->set_object_id($this->id)
		->set_label($msg["159"]." ".$this->name." (".$this->editeur.")")
		->set_content_form($content_form)
		->set_table_name('collections')
		->set_field_focus('coll_libelle')
		->set_url_base(static::format_url());
		print $interface_form->get_display();
	}

	/**
	 * Initialisation du tableau de valeurs pour update et import
	 */
	protected static function get_default_data() {
		return array(
				'name' => '',
				'issn' => '',
				'parent' => 0,
				'publisher' => 0,
				'collection_web' => '',
				'comment' => '',
				'subcollections' => array(),
				'statut' => 1,
				'thumbnail_url' => ''
		);	
	}
	
	// ---------------------------------------------------------------
	//		update($value) : mise à jour de la collection
	// ---------------------------------------------------------------
	public function update($value,$force_creation = false) {
		global $msg,$charset;
		global $include_path;
		global $thesaurus_concepts_active;
		
		$value = array_merge(static::get_default_data(), $value);
		
		// nettoyage des valeurs en entrée
		$value['name'] = clean_string($value['name']);
		$value['issn'] = clean_string($value['issn']);
		
		if(!$value['parent']){
			if($value['publisher']){
				//on les a, on crée l'éditeur
				$value['publisher']=stripslashes_array($value['publisher']);//La fonction d'import fait les addslashes contrairement à l'update
				$value['parent'] = editeur::import($value['publisher']);
			}
		}
		
		if ((!$value['name']) || (!$value['parent'])) 
			return false;
		
		// construction de la requête
		$requete = 'SET collection_name="'.$value['name'].'", ';
		$requete .= 'collection_parent="'.$value['parent'].'", ';
		$requete .= 'collection_issn="'.$value['issn'].'", ';
		$requete .= 'collection_web="'.$value['collection_web'].'", ';
		$requete .= 'collection_comment="'.$value['comment'].'", ';
		$requete .= 'index_coll=" '.strip_empty_words($value['name']).' '.strip_empty_words($value['issn']).' "';
	
		if($this->id) {
			// update
			$requete = 'UPDATE collections '.$requete;
			$requete .= ' WHERE collection_id='.$this->id.' ;';
			if(pmb_mysql_query($requete)) {
				$requete = "update notices set ed1_id='".$value['parent']."' WHERE coll_id='".$this->id."' ";
				pmb_mysql_query($requete) ;
				
				audit::insert_modif (AUDIT_COLLECTION, $this->id) ;
				
				// liens entre autorités
				$aut_link= new aut_link(AUT_TABLE_COLLECTIONS,$this->id);
				$aut_link->save_form();			
				$aut_pperso= new aut_pperso("collection",$this->id);
				if($aut_pperso->save_form()){
					$this->cp_error_message = $aut_pperso->error_message;
					return false;
				}
			} else {
				require_once("$include_path/user_error.inc.php");
				warning($msg[167],htmlentities($msg[169]." -> ".$this->display,ENT_QUOTES, $charset));
				return FALSE;
			}
		} else {
			if(!$force_creation){
				// création : s'assurer que la collection n'existe pas déjà
				if ($id_collection_exists = collection::check_if_exists($value, 1)) {
					$collection_exists = new collection($id_collection_exists);
	 				require_once("$include_path/user_error.inc.php");
					print $this->warning_already_exist($msg[167], $msg[171]." -> ".$collection_exists->display, $value);
					return FALSE;
				}
			}
			$requete = 'INSERT INTO collections '.$requete.';';
			if(pmb_mysql_query($requete)) {
				$this->id=pmb_mysql_insert_id();
				
				audit::insert_creation (AUDIT_COLLECTION, $this->id) ;
				
				// liens entre autorités
				$aut_link= new aut_link(AUT_TABLE_COLLECTIONS,$this->id);
				$aut_link->save_form();
				$aut_pperso= new aut_pperso("collection",$this->id);
				if($aut_pperso->save_form()){
					$this->cp_error_message = $aut_pperso->error_message;
					return false;
				}
			} else {
				require_once("$include_path/user_error.inc.php");
				warning($msg[167],htmlentities($msg[170]." -> ".$requete,ENT_QUOTES, $charset));
				return FALSE;
			}
		}
		// Indexation concepts
		if($thesaurus_concepts_active == 1){
			$index_concept = new index_concept($this->id, TYPE_COLLECTION);
			$index_concept->save();
		}

		// Mise à jour des vedettes composées contenant cette autorité
		vedette_composee::update_vedettes_built_with_element($this->id, TYPE_COLLECTION);
		
		if(isset($value['subcollections']) && is_array($value['subcollections'])){
			for ( $i=0 ; $i<count($value['subcollections']) ; $i++){
				$subcoll=stripslashes_array($value['subcollections'][$i]);//La fonction d'import fait les addslashes contrairement à l'update
				$subcoll['coll_parent'] = $this->id;
				subcollection::import($subcoll);
			}
		}
		
		//update authority informations
		$authority = new authority(0, $this->id, AUT_TABLE_COLLECTIONS);
		$authority->set_num_statut($value['statut']);
		$authority->set_thumbnail_url($value['thumbnail_url']);
		$authority->update();
		
		collection::update_index($this->id);
		
		return true;
	}
	
	// ---------------------------------------------------------------
	//		import() : import d'une collection
	// ---------------------------------------------------------------
	
	// fonction d'import de collection (membre de la classe 'collection');
	
	public static function import($data) {
		// cette méthode prend en entrée un tableau constitué des informations éditeurs suivantes :
		//	$data['name'] 	Nom de la collection
		//	$data['parent']	id de l'éditeur parent de la collection
		//	$data['issn']	numéro ISSN de la collection
	
		// check sur le type de  la variable passée en paramètre
		if ((empty($data) && !is_array($data)) || !is_array($data)) {
			// si ce n'est pas un tableau ou un tableau vide, on retourne 0
			return 0;
		}
	
		$data = array_merge(static::get_default_data(), $data);
		
		// check sur les éléments du tableau (data['name'] est requis).
		if (!isset(static::$long_maxi_name)) {
			static::$long_maxi_name = pmb_mysql_field_len(pmb_mysql_query("SELECT collection_name FROM collections limit 1"), 0);
		}
		$data['name'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['name']))), 0, static::$long_maxi_name));
	
		//si on a pas d'id, on peut avoir les infos de l'éditeur 
		if (empty($data['parent'])) {
			if (!empty($data['publisher'])) {
				//on les a, on crée l'éditeur
				$data['parent'] = editeur::import($data['publisher']);
			}
		}
		
		if ($data['name'] == "" || $data['parent'] == 0) { /* il nous faut impérativement un éditeur */
			return 0;
		}
	
		// préparation de la requête
		$key0 = addslashes($data['name']);
		$key1 = $data['parent'];
		$key2 = addslashes($data['issn']);
		
		/* vérification que l'éditeur existe bien ! */
		$query = "SELECT ed_id FROM publishers WHERE ed_id='$key1' LIMIT 1 ";
		$result = @pmb_mysql_query($query);
		if (empty($result)) {
			die("can't SELECT publishers $query");
		}
		if (pmb_mysql_num_rows($result) == 0) {
			return 0;
		}
	
		/* vérification que la collection existe */
		$query = "SELECT collection_id FROM collections WHERE collection_name='$key0' AND collection_parent='$key1' LIMIT 1 ";
		$result = @pmb_mysql_query($query);
		if (empty($result)) {
		    die("can't SELECT collections $query");
		}
		$collection = pmb_mysql_fetch_object($result);
	
		/* la collection existe, on retourne l'ID */
		if (!empty($collection->collection_id)) {
			return $collection->collection_id;
		}
	
		// id non-récupérée, il faut créer la forme.
		$query = "INSERT INTO collections SET collection_name='$key0', ";
		$query .= "collection_parent='$key1', ";
		$query .= "collection_issn='$key2', ";
		$query .= "index_coll='".strip_empty_words($key0)." ".strip_empty_words($key2)."', ";
		$query .= "collection_comment = '".addslashes($data['comment'])."'";
		$result = @pmb_mysql_query($query);
		if (empty($result)) {
		    die("can't INSERT into database");
		}
		
		$id = pmb_mysql_insert_id();
		
		if (!empty($data['subcollections'])) {
		    $nb_subcollections = count($data['subcollections']);
		    for ($i = 0; $i < $nb_subcollections; $i++) {
				$subcoll = $data['subcollections'][$i];
				$subcoll['coll_parent'] = $id;
				subcollection::import($subcoll);
			}
		}
		
		audit::insert_creation (AUDIT_COLLECTION, $id) ;
	
		//update authority informations
		$authority = new authority(0, $id, AUT_TABLE_COLLECTIONS);
		$authority->set_num_statut($data['statut']);
		$authority->set_thumbnail_url($data['thumbnail_url']);
		$authority->update();
		
		collection::update_index($id);
		
		return $id;
	}
		
	// ---------------------------------------------------------------
	//		search_form() : affichage du form de recherche
	// ---------------------------------------------------------------
	
	public static function search_form() {
		global $user_query, $user_input;
		global $msg,$charset;
	    global $authority_statut;
	
		$user_query = str_replace ('!!user_query_title!!', $msg[357]." : ".$msg[136] , $user_query);
		$user_query = str_replace ('!!action!!', static::format_url('&sub=reach&id='), $user_query);
		$user_query = str_replace ('!!add_auth_msg!!', $msg[163] , $user_query);
		$user_query = str_replace ('!!add_auth_act!!', static::format_url('&sub=collection_form'), $user_query);
		$user_query = str_replace('<!-- sel_authority_statuts -->', authorities_statuts::get_form_for(AUT_TABLE_COLLECTIONS, $authority_statut, true), $user_query);
		$user_query = str_replace ('<!-- lien_derniers -->', "<a href='".static::format_url("&sub=collection_last")."'>$msg[1312]</a>", $user_query);
		$user_query = str_replace("!!user_input!!",htmlentities(stripslashes($user_input),ENT_QUOTES, $charset),$user_query);
		print pmb_bidi($user_query) ;
	}
	
	//---------------------------------------------------------------
	// update_index($id) : maj des index	
	//---------------------------------------------------------------
	public static function update_index($id, $datatype = 'all') {
		indexation_stack::push($id, TYPE_COLLECTION, $datatype);
		
		// On cherche tous les n-uplet de la table notice correspondant à cette collection.
		$query = "select distinct notice_id from notices where coll_id='".$id."'";
		authority::update_records_index($query, 'collection');
	}
	
	//---------------------------------------------------------------
	// get_informations_from_unimarc : ressort les infos d'une collection depuis une notice unimarc
	//---------------------------------------------------------------
	public static function get_informations_from_unimarc($fields,$from_subcollection=false,$import_subcoll=false){
		$data = array();
		
		if(!$from_subcollection){
			$data['name'] = $fields['200'][0]['a'][0];
			if(count($fields['200'][0]['i'])){
				foreach ( $fields['200'][0]['i'] as $value ) {
	       			$data['name'].= ". ".$value;
				}
			}
			if(count($fields['200'][0]['e'])){
				foreach ( $fields['200'][0]['e'] as $value ) {
	       			$data['name'].= " : ".$value;
				}
			}
			$data['issn'] = $fields['011'][0]['a'][0];
			if($fields['312']){
				for($i=0 ; $i<count($fields['312']) ; $i++){
					for($j=0 ; $j<count($fields['312'][$i]['a']) ; $j++){
						if($data['comment']!= "") $data['comment'] .= "\n";
						$data['comment'].=$fields['312'][$i]['a'][$j];
					}
				}
			}
			$data['publisher'] = editeur::get_informations_from_unimarc($fields);
			if($import_subcoll){
				$data['subcollections'] = subcollection::get_informations_from_unimarc($fields,true);
			}
		}else{
			$data['name'] = $fields['410'][0]['t'][0];
			$data['issn'] = $fields['410'][0]['x'][0];
			$data['authority_number'] = $fields['410'][0]['3'][0];
			$data['publisher'] = editeur::get_informations_from_unimarc($fields);
		}
		return $data;
	}
	
	public static function check_if_exists($data, $from_form = 0){
		//si on a pas d'id, on peut avoir les infos de l'éditeur 
		if(!$data['parent']){
			if($data['publisher']){
				//on les a, on crée l'éditeur
				$data['parent'] = editeur::check_if_exists($data['publisher']);
			}
		}
	
		// préparation de la requête
		if ($from_form) {
    		$key0 = $data['name'];
    		$key1 = $data['parent'];
    		$key2 = $data['issn'];
		} else {		    
		    $key0 = addslashes($data['name']);
		    $key1 = $data['parent'];
		    $key2 = addslashes($data['issn']);
		}
		
		/* vérification que la collection existe */
		$query = "SELECT collection_id FROM collections WHERE collection_name='{$key0}' AND collection_parent='{$key1}' LIMIT 1 ";
		$result = @pmb_mysql_query($query);
		if(!$result) die("can't SELECT collections ".$query);
		if(pmb_mysql_num_rows($result)) {
			$collection  = pmb_mysql_fetch_object($result);
		
			/* la collection existe, on retourne l'ID */
			if($collection->collection_id)
				return $collection->collection_id;
		}
			
		return 0;
	}
	
	public function get_header() {
		return $this->display;
	}
	
	public function get_cp_error_message(){
		return $this->cp_error_message;
	}

	public function get_gestion_link(){
		return './autorites.php?categ=see&sub=collection&id='.$this->id;
	}
	
	public function get_isbd() {
		return $this->isbd_entry;
	}
	
	public static function get_format_data_structure($antiloop = false) {
		global $msg;
		
		$main_fields = array();
		$main_fields[] = array(
				'var' => "name",
				'desc' => $msg['714']
		);
		$main_fields[] = array(
				'var' => "issn",
				'desc' => $msg['165']
		);
		$main_fields[] = array(
				'var' => "parent",
				'desc' => $msg['164'],
				'children' => authority::prefix_var_tree(editeur::get_format_data_structure(),"parent")
		);
		
		$main_fields[] = array(
				'var' => "web",
				'desc' => $msg['147']
		);
		
		$main_fields[] = array(
				'var' => "comment",
				'desc' => $msg['collection_comment']
		);
		$authority = new authority(0, 0, AUT_TABLE_COLLECTIONS);
		$main_fields = array_merge($authority->get_format_data_structure(), $main_fields);
		return $main_fields;
	}
	
	public function format_datas($antiloop = false){
		$parent_datas = array();
		if(!$antiloop) {
			if($this->editeur) {
				$parent = new editeur($this->editeur);
				$parent_datas = $parent->format_datas(true);
			}
		}
		$formatted_data = array(
				'name' => $this->name,
				'issn' => $this->issn,
				'publisher' => $parent_datas,
				'web' => $this->collection_web,
				'comment' => $this->comment
		);
		$authority = new authority(0, $this->id, AUT_TABLE_COLLECTIONS);
		$formatted_data = array_merge($authority->format_datas(), $formatted_data);
		return $formatted_data;
	}
	
	public static function set_controller($controller) {
		static::$controller = $controller;
	}
	
	protected static function format_url($url='') {
		global $base_path;
		
		if(isset(static::$controller) && is_object(static::$controller)) {
			return 	static::$controller->get_url_base().$url;
		} else {
			return $base_path.'/autorites.php?categ=collections'.$url;
		}
	}
	
	protected static function format_back_url() {
		if(isset(static::$controller) && is_object(static::$controller)) {
			return 	static::$controller->get_back_url();
		} else {
			return "history.go(-1)";
		}
	}
	
	protected static function format_delete_url($url='') {
		if(isset(static::$controller) && is_object(static::$controller)) {
			return 	static::$controller->get_delete_url();
		} else {
			return static::format_url("&sub=delete".$url);
		}
	}
	
	protected function warning_already_exist($error_title, $error_message, $values=array())  {
		global $msg;
		
		$authority = new authority(0, $this->id, AUT_TABLE_COLLECTIONS);
		$display = $authority->get_display_authority_already_exist($error_title, $error_message, $values);
		$display = str_replace("!!action!!", static::format_url('&sub=update&id='.$this->id.'&forcing=1'), $display);
		$label = (empty($this->id) ? $msg[287] : $msg['force_modification']);
		$display = str_replace("!!forcing_button!!", $authority->get_display_forcing_button($label) , $display);
		$hidden_specific_values = $authority->put_global_in_hidden_field("collection_nom");
		$hidden_specific_values .= $authority->put_global_in_hidden_field("ed_id");
		$display = str_replace('!!hidden_specific_values!!', $hidden_specific_values, $display);
		return $display;
	}
} # fin de définition de la classe collection

} # fin de délaration
