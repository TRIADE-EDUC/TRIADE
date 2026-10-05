<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: subcollection.class.php,v 1.106.4.1 2025/02/28 13:38:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des 'sous-collections'
use Pmb\Ark\Entities\ArkEntityPmb;

if ( ! defined( 'SUB_COLLECTION_CLASS' ) ) {
  define( 'SUB_COLLECTION_CLASS', 1 );

  global $class_path;
  
require_once($class_path."/notice.class.php");
require_once("$class_path/aut_link.class.php");
require_once($class_path."/subcollection.class.php");
require_once("$class_path/aut_pperso.class.php");
require_once("$class_path/audit.class.php");
require_once($class_path."/index_concept.class.php");
require_once($class_path."/vedette/vedette_composee.class.php");
require_once($class_path.'/authorities_statuts.class.php');
require_once($class_path."/indexation_authority.class.php");
require_once($class_path."/authority.class.php");
require_once ($class_path.'/indexations_collection.class.php');
require_once ($class_path.'/indexation_stack.class.php');
require_once ($class_path.'/interface/entity/interface_entity_subcollection_form.class.php');

class subcollection {

// ---------------------------------------------------------------
//		propriétés de la classe
// ---------------------------------------------------------------
	public $id;				// MySQL id in table 'collections'
	public $name;				// collection name
	public $parent;			// MySQL id of parent collection
	public $parent_libelle;	// name of parent collection
	public $editeur;			// MySQL id of publisher
	public $editeur_libelle;	// name of parent publisher
	public $editor_isbd;		// isbd form of publisher
	public $display;			// usable form for displaying	( _collection_. _name_ (_editeur_) )
	public $isbd_entry;		// ISBD form ( _collection_. _name_ )
	public $issn;				// ISSN of sub collection
	public $isbd_entry_lien_gestion ; // lien sur le nom vers la gestion
	public $subcollection_web;			// web de sous-collection
	public $subcollection_web_link;	// lien web de sous-collection
	public $comment;			//Sub collection comment
	public $num_statut = 1;
	public $cp_error_message = '';
	protected static $long_maxi_name;
	protected static $controller;
	
	// ---------------------------------------------------------------
	//		subcollection($id) : constructeur
	// ---------------------------------------------------------------
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->getData();
	}
	
	// ---------------------------------------------------------------
	//		getData() : récupération infos sous collection
	// ---------------------------------------------------------------
	public function getData() {
		$this->name				=	'';
		$this->parent			=	0;
		$this->parent_libelle	=	'';
		$this->editeur			=	0;
		$this->editeur_libelle	=	'';
		$this->display			=	'';
		$this->isbd_entry		=	'';
		$this->issn				=	'';
		$this->subcollection_web = '';
		$this->comment = '';
		$this->subcollection_web_link = "" ;
		$this->num_statut = 1;
		if($this->id) {
			$requete = "SELECT * FROM sub_collections WHERE sub_coll_id='".$this->id."' ";
			$result = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				pmb_mysql_free_result($result);
				
				$this->id = $row->sub_coll_id;
				$this->name = $row->sub_coll_name;
				$this->parent = $row->sub_coll_parent;
				$this->issn = $row->sub_coll_issn;
				$this->subcollection_web	= $row->subcollection_web;
				$this->comment = $row->subcollection_comment;
				$authority = new authority(0, $this->id, AUT_TABLE_SUB_COLLECTIONS);
				$this->num_statut = $authority->get_num_statut();
				if($row->subcollection_web) $this->subcollection_web_link = " <a href='$row->subcollection_web' target=_blank><img src='".get_url_icon('globe.gif')."' style='border:0px;' /></a>";
				else $this->subcollection_web_link = "" ;
				$parent = authorities_collection::get_authority(AUT_TABLE_COLLECTIONS, $row->sub_coll_parent);
				$this->parent_libelle = $parent->name;
				$parent_libelle_lien_gestion = $parent->isbd_entry_lien_gestion ;
				$this->editeur = $parent->parent;
				$editeur = authorities_collection::get_authority(AUT_TABLE_PUBLISHERS, $parent->parent);
				$this->editeur_libelle = $editeur->name;
				$this->editor_isbd = $editeur->get_isbd();
				$this->issn ? $this->isbd_entry = $this->parent_libelle.'. '.$this->name.', ISSN '.$this->issn : $this->isbd_entry = $this->parent_libelle.'. '.$this->name ;
				$this->display = $this->parent_libelle.'. '.$this->name.' ('.$this->editeur_libelle.')';
				// Ajoute un lien sur la fiche sous-collection si l'utilisateur à accès aux autorités
				if (defined('SESSrights') && (SESSrights & AUTORITES_AUTH)) {
					if ($this->issn){
						$lien_lib = $this->name.', ISSN '.$this->issn ;
					}else{ 
						$lien_lib = $this->name ;
					}
					$this->isbd_entry_lien_gestion = $parent_libelle_lien_gestion.".&nbsp;<a href='./autorites.php?categ=see&sub=subcollection&id=".$this->id."' class='lien_gestion'>".$lien_lib."</a>";
				} else {
					$this->isbd_entry_lien_gestion = $this->isbd_entry;
				}
					
			}
		}
	}
		
	public function build_header_to_export() {
	    global $msg;
	    
	    $data = array(
	        $msg[67],
	        $msg[250],
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
	        $this->parent_libelle,
	        $this->editor_isbd,
	        $this->issn,
	        $this->subcollection_web,
	        $this->comment,
	        $this->num_statut,
	    );
	    return $data;
	}
	
	// ---------------------------------------------------------------
	//		delete() : suppression de la sous collection
	// ---------------------------------------------------------------
	public function delete() {
		global $msg;
		
		if(!$this->id)
			// impossible d'accéder à cette notice de sous-collection
			return $msg[406];

		if(($usage=aut_pperso::delete_pperso(AUT_TABLE_SUB_COLLECTIONS, $this->id,0) )){
			// Cette autorité est utilisée dans des champs perso, impossible de supprimer
			return '<strong>'.$this->display.'</strong><br />'.$msg['autority_delete_error'].'<br /><br />'.$usage['display'];
		}
		
		// récupération du nombre de notices affectées
		$requete = "SELECT COUNT(1) FROM notices WHERE ";
		$requete .= "subcoll_id=".$this->id;
		$res = pmb_mysql_query($requete);
		$nbr_lignes = pmb_mysql_result($res, 0, 0);
		if(!$nbr_lignes) {

			// On regarde si l'autorité est utilisée dans des vedettes composées
			$attached_vedettes = vedette_composee::get_vedettes_built_with_element($this->id, TYPE_SUBCOLLECTION);
			if (count($attached_vedettes)) {
				// Cette autorité est utilisée dans des vedettes composées, impossible de la supprimer
				return '<strong>'.$this->display."</strong><br />".$msg["vedette_dont_del_autority"].'<br/>'.vedette_composee::get_vedettes_display($attached_vedettes);
			}
			
			// sous collection non-utilisée dans des notices : Suppression OK
			// effacement dans la table des collections
			$requete = "DELETE FROM sub_collections WHERE sub_coll_id=".$this->id;
			pmb_mysql_query($requete);
			//suppression dans la table de stockage des numéros d'autorités...
			//Import d'autorité
			subcollection::delete_autority_sources($this->id);
			// liens entre autorités
			$aut_link= new aut_link(AUT_TABLE_SUB_COLLECTIONS,$this->id);
			$aut_link->delete();
			$aut_pperso= new aut_pperso("subcollection",$this->id);
			$aut_pperso->delete();
			
			// nettoyage indexation concepts
			$index_concept = new index_concept($this->id, TYPE_SUBCOLLECTION);
			$index_concept->delete();
			
			// nettoyage indexation
			indexation_authority::delete_all_index($this->id, "authorities", "id_authority", AUT_TABLE_SUB_COLLECTIONS);
			
			// effacement de l'identifiant unique d'autorité
			$authority = new authority(0, $this->id, AUT_TABLE_SUB_COLLECTIONS);
			$authority->delete();
			
			audit::delete_audit(AUDIT_SUB_COLLECTION,$this->id);
			return false;
		} else {
			// Cette collection est utilisé dans des notices, impossible de la supprimer
			return '<strong>'.$this->display."</strong><br />{$msg[407]}";
		}
	}
	
	// ---------------------------------------------------------------
	//		delete_autority_sources($idcol=0) : Suppression des informations d'import d'autorité
	// ---------------------------------------------------------------
	public static function delete_autority_sources($idsubcol=0){
		$tabl_id=array();
		if(!$idsubcol){
			$requete="SELECT DISTINCT num_authority FROM authorities_sources LEFT JOIN sub_collections ON num_authority=sub_coll_id  WHERE authority_type = 'subcollection' AND sub_coll_id IS NULL";
			$res=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res)){
				while ($ligne = pmb_mysql_fetch_object($res)) {
					$tabl_id[]=$ligne->num_authority;
				}
			}
		}else{
			$tabl_id[]=$idsubcol;
		}
		foreach ( $tabl_id as $value ) {
	       //suppression dans la table de stockage des numéros d'autorités...
			$query = "select id_authority_source from authorities_sources where num_authority = ".$value." and authority_type = 'subcollection'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while ($ligne = pmb_mysql_fetch_object($result)) {
					$query = "delete from notices_authorities_sources where num_authority_source = ".$ligne->id_authority_source;
					pmb_mysql_query($query);
				}
			}
			$query = "delete from authorities_sources where num_authority = ".$value." and authority_type = 'subcollection'";
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
	
		$n_collection = new subcollection($by);
		if(!$n_collection->parent) {
			// la nouvelle collection est foireuse
			return $msg[406];
		}
		
		$aut_link= new aut_link(AUT_TABLE_SUB_COLLECTIONS,$this->id);
		// "Conserver les liens entre autorités" est demandé
		if($link_save) {
			// liens entre autorités
			$aut_link->add_link_to(AUT_TABLE_SUB_COLLECTIONS,$by);		
		}
		$aut_link->delete();

		vedette_composee::replace(TYPE_SUBCOLLECTION, $this->id, $by);
		
		$requete = "UPDATE notices SET ed1_id=".$n_collection->editeur;
		$requete .= ", coll_id=".$n_collection->parent;
		$requete .= ", subcoll_id=$by WHERE subcoll_id=".$this->id;
		pmb_mysql_query($requete);
	
		//nettoyage d'autorities_sources
		$query = "select * from authorities_sources where num_authority = ".$this->id." and authority_type = 'subcollection'";
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
		$index_concept = new index_concept($this->id, TYPE_SUBCOLLECTION);
		$index_concept->delete();
		
		//Remplacement dans les champs persos sélecteur d'autorité
		aut_pperso::replace_pperso(AUT_TABLE_SUB_COLLECTIONS, $this->id, $by);
		
		audit::delete_audit (AUDIT_SUB_COLLECTION, $this->id);
		
		// nettoyage indexation
		indexation_authority::delete_all_index($this->id, "authorities", "id_authority", AUT_TABLE_SUB_COLLECTIONS);
		if ($pmb_ark_activate) {
		    $idReplaced = authority::get_authority_id_from_entity($this->id, AUT_TABLE_SUB_COLLECTIONS);
		    $idReplacing = authority::get_authority_id_from_entity($by, AUT_TABLE_SUB_COLLECTIONS);
		    if ($idReplaced && $idReplacing) {
		        $arkEntityReplaced = ArkEntityPmb::getEntityClassFromType(TYPE_AUTHORITY, $idReplaced);
		        $arkEntityReplacing = ArkEntityPmb::getEntityClassFromType(TYPE_AUTHORITY, $idReplacing);
		        $arkEntityReplaced->markAsReplaced($arkEntityReplacing);
		    }
		}
		// effacement de l'identifiant unique d'autorité
		$authority = new authority(0, $this->id, AUT_TABLE_SUB_COLLECTIONS);
		$authority->delete();
		
		// b) suppression de la collection
		$requete = "DELETE FROM sub_collections WHERE sub_coll_id=".$this->id;
		pmb_mysql_query($requete);
		
		subcollection::update_index($by);
	
		return FALSE;
	}
	
	protected function get_content_form() {
		global $charset, $thesaurus_concepts_active;
		global $sub_collection_content_form;
		
		$content_form = $sub_collection_content_form;
		
		//Nom
		$element = interface_entity_element::get_instance('el0Child_0_a', 'collection_nom', '67');
		$element->set_class('colonne2');
		$element->add_input_node('text', $this->name, ['data-pmb-deb-rech' => '1'])
		->set_class('saisie-30em');
// 		->set_size(40);
		$content_form = str_replace('!!element_collection_nom!!', $element->get_display(), $content_form);
		
		//ISSN 
		$element = interface_entity_element::get_instance('el0Child_0_b', 'issn', '165');
		$element->set_class('colonne2');
		$element->add_input_node('text', $this->issn)
		->set_class('saisie-20em')
		->set_maxlength(50);
		$content_form = str_replace('!!element_issn!!', $element->get_display(), $content_form);
		
		//Sous-collection de
		$content_form = str_replace('!!coll_id!!', $this->parent, $content_form);
		$content_form = str_replace('!!coll_libelle!!', htmlentities($this->parent_libelle,ENT_QUOTES, $charset), $content_form);
		
		//Editeur
		$content_form = str_replace('!!ed_libelle!!', htmlentities($this->editeur_libelle,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!ed_id!!', $this->editeur, $content_form);
		
		// Web
		$element = interface_entity_element::get_instance('el0Child_2', 'subcollection_web', '147');
		$element->add_input_node('url', $this->subcollection_web);
		$content_form = str_replace('!!element_subcollection_web!!', $element->get_display(), $content_form);
		
		//Commentaire
		$element = interface_entity_element::get_instance('el0Child_3', 'comment', 'subcollection_comment');
		$element->add_textarea_node($this->comment, 62, 4)
		->set_class('saisie-80em')
		->set_attributes(array('wrap' => 'virtual'));
		$content_form = str_replace('!!element_comment!!', $element->get_display(), $content_form);

		$aut_link= new aut_link(AUT_TABLE_SUB_COLLECTIONS,$this->id);
		$content_form = str_replace('<!-- aut_link -->', $aut_link->get_form('saisie_sub_collection') , $content_form);
		
		$aut_pperso= new aut_pperso("subcollection",$this->id);
		$content_form = str_replace('!!aut_pperso!!',	$aut_pperso->get_form(), $content_form);
		
		if( $thesaurus_concepts_active == 1){
			$index_concept = new index_concept($this->id, TYPE_SUBCOLLECTION);
			$content_form = str_replace('!!concept_form!!',			$index_concept->get_form('saisie_sub_collection'),	$content_form);
		}else{
			$content_form = str_replace('!!concept_form!!',			"",	$content_form);
		}
		$authority = new authority(0, $this->id, AUT_TABLE_SUB_COLLECTIONS);
		$content_form = str_replace('!!thumbnail_url_form!!', thumbnail::get_form('authority', $authority->get_thumbnail_url()), $content_form);
		
		return $content_form;
	}
	
	public function get_form($duplicate = false) {
		global $msg;
		global $user_input, $nbr_lignes, $page ;
		
		$interface_form = new interface_entity_subcollection_form('saisie_sub_collection');
		if(isset(static::$controller) && is_object(static::$controller)) {
			$interface_form->set_controller(static::$controller);
		}
		$interface_form->set_enctype('multipart/form-data');
		if($this->id && !$duplicate) {
			$interface_form->set_label($msg['178']);
			$interface_form->set_document_title($this->name.' - '.$msg['178']);
		} else {
			$interface_form->set_label($msg['177']);
			$interface_form->set_document_title($msg['177']);
		}
		$interface_form->set_object_id($this->id)
		->set_num_statut($this->num_statut)
		->set_content_form($this->get_content_form())
		->set_table_name('sub_collections')
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
	public function replace_form() {
		global $sub_coll_rep_content_form;
		global $msg;
		global $include_path;
		
		if(!$this->id || !$this->name) {
			require_once("$include_path/user_error.inc.php");
			error_message($msg[161], $msg[162], 1, './autorites.php?categ=collections&sub=&id=');
			return false;
		}
	
		$content_form = $sub_coll_rep_content_form;
		$content_form = str_replace('!!id!!', $this->id, $content_form);
		
		$interface_form = new interface_autorites_replace_form('saisie_sub_collection');
		$interface_form->set_object_id($this->id)
		->set_label($msg["159"]." ".$this->display)
		->set_content_form($content_form)
		->set_table_name('sub_collections')
		->set_field_focus('sub_coll_nom')
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
				'coll_parent' => 0,
				'collection' => '',
				'subcollection_web' => '',
				'comment' => '',
				'statut' => 1,
				'thumbnail_url' => ''
		);
	}
	
	// ---------------------------------------------------------------
	//		?? update($value) : mise à jour de la collection
	// ---------------------------------------------------------------
	public function update($value,$force_creation = false) {
		global $msg,$charset;
		global $include_path;
		global $thesaurus_concepts_active;
		
		$value = array_merge(static::get_default_data(), $value);
		
		//si on a pas d'id, on peut avoir les infos de la collection 
		if(!$value['parent']){
			if($value['collection']){
				//on les a, on crée l'éditeur
				$value['collection']=stripslashes_array($value['collection']);//La fonction d'import fait les addslashes contrairement à l'update
				$value['parent'] = collection::import($value['collection']);
			}
		}
		
		if(!$value['name'] || !$value['parent'])
			return false;
	
		// nettoyage des valeurs en entrée
		$value['name'] = clean_string($value['name']);
	
		// construction de la requête
		$requete = 'SET sub_coll_name="'.$value['name'].'", ';
		$requete .= 'sub_coll_parent="'.$value['parent'].'", ';
		$requete .= 'sub_coll_issn="'.$value["issn"].'", ';
		$requete .= 'subcollection_web="'.$value['subcollection_web'].'", ';
		$requete .= 'subcollection_comment="'.$value['comment'].'", ';
		$requete .= 'index_sub_coll=" '.strip_empty_words($value['name']).' '.strip_empty_words($value['issn']).' " ';
	
		if($this->id) {
			// update
			$requete = 'UPDATE sub_collections '.$requete;
			$requete .= ' WHERE sub_coll_id='.$this->id.' ';
			if(pmb_mysql_query($requete)) {
				$requete = "select collection_parent from collections WHERE collection_id='".$value['parent']."' ";
				$res = pmb_mysql_query($requete) ;
				$ed_parent = pmb_mysql_result($res, 0, 0);
				$requete = "update notices set ed1_id='$ed_parent', coll_id='".$value['parent']."' WHERE subcoll_id='".$this->id."' ";
				$res = pmb_mysql_query($requete) ;
				
				audit::insert_modif (AUDIT_SUB_COLLECTION, $this->id) ;
				
				$aut_link= new aut_link(AUT_TABLE_SUB_COLLECTIONS,$this->id);
				$aut_link->save_form();
				$aut_pperso= new aut_pperso("subcollection",$this->id);
				if($aut_pperso->save_form()){
					$this->cp_error_message = $aut_pperso->error_message;
					return false;
				}
			} else {
				require_once("$include_path/user_error.inc.php");
				warning($msg[178],htmlentities($msg[182]." -> ".$this->display,ENT_QUOTES, $charset));
				return FALSE;
			}
		} else {
			if(!$force_creation){
				// création : s'assurer que la sous-collection n'existe pas déjà
				if ($id_subcollection_exists = subcollection::check_if_exists($value)) {
					$subcollection_exists = new subcollection($id_subcollection_exists);
					require_once("$include_path/user_error.inc.php");
					print $this->warning_already_exist($msg[177], $msg[219]." -> ".$subcollection_exists->display, $value);
					return FALSE;
				}
			}
			$requete = 'INSERT INTO sub_collections '.$requete.';';
			if(pmb_mysql_query($requete)) {
				$this->id=pmb_mysql_insert_id();

				audit::insert_creation (AUDIT_SUB_COLLECTION, $this->id) ;
				
				$aut_link= new aut_link(AUT_TABLE_SUB_COLLECTIONS,$this->id);
				$aut_link->save_form();			
				$aut_pperso= new aut_pperso("subcollection",$this->id);
				if($aut_pperso->save_form()){
					$this->cp_error_message = $aut_pperso->error_message;
					return false;
				}
			} else {
				require_once("$include_path/user_error.inc.php");
				warning($msg[177],htmlentities($msg[182]." -> ".$requete,ENT_QUOTES, $charset));
				return FALSE;
			}
		}
		//update authority informations
		$authority = new authority(0, $this->id, AUT_TABLE_SUB_COLLECTIONS);
		$authority->set_num_statut($value['statut']);
		$authority->set_thumbnail_url($value['thumbnail_url']);
		$authority->update();
		
		// Indexation concepts
		if( $thesaurus_concepts_active == 1){
			$index_concept = new index_concept($this->id, TYPE_SUBCOLLECTION);
			$index_concept->save();
		}

		// Mise à jour des vedettes composées contenant cette autorité
		vedette_composee::update_vedettes_built_with_element($this->id, TYPE_SUBCOLLECTION);
		
		subcollection::update_index($this->id);
		
		return TRUE;
	}
	
	// ---------------------------------------------------------------
	//		import() : import d'une sous-collection
	// ---------------------------------------------------------------
	// fonction d'import de sous-collection (membre de la classe 'subcollection');
	public static function import($data) {
		// cette méthode prend en entrée un tableau constitué des informations éditeurs suivantes :
		//	$data['name'] 	Nom de la collection
		//	$data['coll_parent']	id de l'éditeur parent de la collection
		//	$data['issn']	numéro ISSN de la collection
		//	$data['statut']	statut de la collection
	
		// check sur le type de  la variable passée en paramètre
		if (!is_array($data) || empty($data)) {
			// si ce n'est pas un tableau ou un tableau vide, on retourne 0
			return 0;
		}
	
		$data = array_merge(static::get_default_data(), $data);
		
		// check sur les éléments du tableau (data['name'] est requis).
		if(!isset(static::$long_maxi_name)) {
			static::$long_maxi_name = pmb_mysql_field_len(pmb_mysql_query("SELECT sub_coll_name FROM sub_collections limit 1"),0);
		}
		$data['name'] = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['name']))),0,static::$long_maxi_name));
	
		//si on a pas d'id, on peut avoir les infos de la collection 
		if(!$data['coll_parent']){
			if($data['collection']){
				//on les a, on crée l'éditeur
				$data['coll_parent'] = collection::import($data['collection']);
			}
		}	
		
		if($data['name']=="" || $data['coll_parent']==0) /* il nous faut impérativement une collection parente */
			return 0;
	
		// préparation de la requête
		$key0 = addslashes($data['name']);
		$key1 = $data['coll_parent'];
		$key2 = addslashes($data['issn']);
		
		/* vérification que la collection existe bien ! */
		$query = "SELECT collection_id FROM collections WHERE collection_id='{$key1}' LIMIT 1 ";
		$result = pmb_mysql_query($query);
		if(!$result) die("can't SELECT colections ".$query);
		if (pmb_mysql_num_rows($result)==0) 
			return 0;
	
		/* vérification que la sous-collection existe */
		$query = "SELECT sub_coll_id FROM sub_collections WHERE sub_coll_name='{$key0}' AND sub_coll_parent='{$key1}' LIMIT 1 ";
		$result = pmb_mysql_query($query);
		if(!$result) die("can't SELECT sub_collections ".$query);
		$subcollection  = pmb_mysql_fetch_object($result);
	
		/* la sous-collection existe, on retourne l'ID */
		if($subcollection->sub_coll_id)
			return $subcollection->sub_coll_id;
	
		// id non-récupérée, il faut créer la forme.
		$query = 'INSERT INTO sub_collections SET sub_coll_name="'.$key0.'", ';
		$query .= 'sub_coll_parent="'.$key1.'", ';
		$query .= 'sub_coll_issn="'.$key2.'", ';
		$query .= 'subcollection_web="'.addslashes($data['subcollection_web']).'", ';
		$query .= 'subcollection_comment="'.addslashes($data['comment']).'", ';
		$query .= 'index_sub_coll=" '.strip_empty_words($key0).' '.strip_empty_words($key2).' " ';
		$result = @pmb_mysql_query($query);
		if(!$result) die("can't INSERT into sub_collections".$query);
		$id=pmb_mysql_insert_id();
		
		audit::insert_creation (AUDIT_SUB_COLLECTION, $id) ;
		
		//update authority informations
		$authority = new authority(0, $id, AUT_TABLE_SUB_COLLECTIONS);
		$authority->set_num_statut($data['statut']);
		$authority->set_thumbnail_url($data['thumbnail_url']);
		$authority->update();
		
		subcollection::update_index($id);
		return $id;
	}
		
	// ---------------------------------------------------------------
	//		search_form() : affichage du form de recherche
	// ---------------------------------------------------------------
	public static function search_form() {
		global $user_query, $user_input;
		global $msg, $charset;
		global $authority_statut;
	
		$user_query = str_replace ('!!user_query_title!!', $msg[357]." : ".$msg[137] , $user_query);
		$user_query = str_replace ('!!action!!', static::format_url('&sub=reach&id='), $user_query);
		$user_query = str_replace ('!!add_auth_msg!!', $msg[176] , $user_query);
		$user_query = str_replace ('!!add_auth_act!!', static::format_url('&sub=collection_form'), $user_query);
		$user_query = str_replace('<!-- sel_authority_statuts -->', authorities_statuts::get_form_for(AUT_TABLE_SUB_COLLECTIONS, $authority_statut, true), $user_query);
		$user_query = str_replace ('<!-- lien_derniers -->', "<a href='".static::format_url('&sub=collection_last')."'>$msg[1313]</a>", $user_query);
		$user_query = str_replace("!!user_input!!",htmlentities(stripslashes($user_input),ENT_QUOTES, $charset),$user_query);
		print pmb_bidi($user_query) ;
	}
	
	//---------------------------------------------------------------
	// update_index($id) : maj des index	
	//---------------------------------------------------------------
	public static function update_index($id, $datatype = 'all') {
		indexation_stack::push($id, TYPE_SUBCOLLECTION, $datatype);
		
		// On cherche tous les n-uplet de la table notice correspondant à cette sous-collection.
		$query = "select distinct notice_id from notices where subcoll_id='".$id."'";
		authority::update_records_index($query, 'subcollection');
	}
	
	public static function get_informations_from_unimarc($fields,$from_collection=false){
		$data = array();
		
		if($from_collection){
			for($i=0 ; $i<count($fields['411']) ; $i++){
				$sub = array();
				$sub['authority_number'] = $fields['411'][$i]['0'][0];
				$sub['issn'] = $fields['411'][$i]['x'][0];
				$sub['name'] = $fields['411'][$i]['t'][0];	
				$data[] = $sub;
			}
		}else{
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
			$data['collection'] = collection::get_informations_from_unimarc($fields,true);
			
		}
		return $data;
	}
	
	public static function check_if_exists($data){
		if (!$data['coll_parent'] && $data['parent']) $data['coll_parent'] = $data['parent'];
		//si on a pas d'id, on peut avoir les infos de la collection 
		if(!$data['coll_parent']){
			if($data['collection']){
				//on les a, on crée l'éditeur
				$data['coll_parent'] = collection::check_if_exists($data['collection']);
			}
		}	
	
		// préparation de la requête
		$key0 = addslashes($data['name']);
		$key1 = $data['coll_parent'];
		//$key2 = addslashes($data['issn']);
		
		/* vérification que la sous-collection existe */
		$query = "SELECT sub_coll_id FROM sub_collections WHERE sub_coll_name='{$key0}' AND sub_coll_parent='{$key1}' LIMIT 1 ";
		$result = pmb_mysql_query($query);
		if(!$result) die("can't SELECT sub_collections ".$query);
		if(pmb_mysql_num_rows($result)) {
			$subcollection  = pmb_mysql_fetch_object($result);
		
			/* la sous-collection existe, on retourne l'ID */
			if($subcollection->sub_coll_id)
				return $subcollection->sub_coll_id;
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
		return './autorites.php?categ=see&sub=subcollection&id='.$this->id;
	}
	
	public function get_isbd() {
		return $this->isbd_entry;
	}
	
	public static function get_format_data_structure($antiloop = false) {
		global $msg;
		
		$main_fields = array();
		$main_fields[] = array(
				'var' => "name",
				'desc' => $msg['67']
		);
		$main_fields[] = array(
				'var' => "issn",
				'desc' => $msg['165']
		);
		$main_fields[] = array(
				'var' => "parent",
				'desc' => $msg['179'],
				'children' => authority::prefix_var_tree(collection::get_format_data_structure(),"parent")
		);
		$main_fields[] = array(
				'var' => "web",
				'desc' => $msg['147']
		);
		$main_fields[] = array(
				'var' => "comment",
				'desc' => $msg['subcollection_comment']
		);
		$authority = new authority(0, 0, AUT_TABLE_SUB_COLLECTIONS);
		$main_fields = array_merge($authority->get_format_data_structure(), $main_fields);
		return $main_fields;
	}
	
	public function format_datas($antiloop = false){
		$parent_datas = array();
		if(!$antiloop) {
			if($this->parent) {
				$parent = new collection($this->parent);
				$parent_datas = $parent->format_datas(true);
			}
		}
		$formatted_data = array(
				'name' => $this->name,
				'issn' => $this->issn,
				'parent' => $parent_datas,
				'web' => $this->subcollection_web,
				'comment' => $this->comment
		);
		$authority = new authority(0, $this->id, AUT_TABLE_SUB_COLLECTIONS);
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
			return $base_path.'/autorites.php?categ=souscollections'.$url;
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
		
		$authority = new authority(0, $this->id, AUT_TABLE_SUB_COLLECTIONS);
		$display = $authority->get_display_authority_already_exist($error_title, $error_message, $values);
		$display = str_replace("!!action!!", static::format_url('&sub=update&id='.$this->id.'&forcing=1'), $display);
		$label = (empty($this->id) ? $msg[287] : $msg['force_modification']);
		$display = str_replace("!!forcing_button!!", $authority->get_display_forcing_button($label) , $display);
		$display = str_replace('!!hidden_specific_values!!', '', $display);
		return $display;
	}
} # fin de définition de la classe subcollection

} # fin de délaration
