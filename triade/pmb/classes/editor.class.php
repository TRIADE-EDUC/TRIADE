<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: editor.class.php,v 1.117.2.1.2.1 2025/02/27 16:00:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Ark\Entities\ArkEntityPmb;
// definition de la classe de gestion des 'editeurs'
if ( ! defined( 'PUBLISHER_CLASS' ) ) {
  define( 'PUBLISHER_CLASS', 1 );

  global $class_path;
  
require_once($class_path."/notice.class.php");
require_once("$class_path/aut_link.class.php");
require_once("$class_path/aut_pperso.class.php");
require_once("$class_path/audit.class.php");
require_once($class_path."/synchro_rdf.class.php");
require_once($class_path."/index_concept.class.php");
require_once($class_path."/vedette/vedette_composee.class.php");
require_once($class_path.'/authorities_statuts.class.php');
require_once($class_path."/indexation_authority.class.php");
require_once($class_path."/authority.class.php");
require_once ($class_path.'/indexations_collection.class.php');
require_once ($class_path.'/indexation_stack.class.php');
require_once ($class_path.'/interface/entity/interface_entity_publisher_form.class.php');

class editeur {

	// ---------------------------------------------------------------
	//		proprietes de la classe
	// ---------------------------------------------------------------

	public $id;			// MySQL id in table 'publishers'
	public $name;			// publisher name
	public $adr1;			// adress line 1
	public $adr2;			// adress line 2
	public $cp;			// zip code
	public $ville;			// city
	public $pays;			// country
	public $web;			// url of web site
	public $link;			// url of web site (clickable)
	public $display;		// usable form for displaying ( _name_ (_ville_) or just _name_ )
	public $isbd_entry;		// isbd like version ( _ville_ (_country ?_) : _name_ )
	public $isbd_entry_lien_gestion ; // lien sur le nom vers la gestion
	public $ed_comment ; // Commentaire, peut contenir du HTML
	public $num_statut = 1;    //Identifiant du statut affecté à l'éditeur
	public $supplier;		// Instance du fournisseur
	public $authority;	// Instance de authority
	public $cp_error_message = "";
	protected static $long_maxi;
	protected static $controller;
	
	// ---------------------------------------------------------------
	//		editeur($id) : constructeur
	// ---------------------------------------------------------------
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->getData();
	}

	// ---------------------------------------------------------------
	//		getData() : recuperation infos editeurs
	// ---------------------------------------------------------------
	public function getData() {
		$this->name			=	'';
		$this->adr1			=	'';
		$this->adr2			=	'';
		$this->cp			=	'';
		$this->ville		=	'';
		$this->pays			=	'';
		$this->web			=	'';
		$this->link			=	'';
		$this->display		=	'';
		$this->isbd_entry	=	'';
		$this->ed_comment	=	'';
		$this->num_statut   =   1;
		$this->supplier = new entites();
		$this->authority = '';
		if($this->id) {
			$requete = "SELECT * FROM publishers WHERE ed_id='".$this->id."'";
			$result = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				pmb_mysql_free_result($result);
				
				$this->id 	= $row->ed_id;
				$this->name = $row->ed_name;
				$this->adr1 = $row->ed_adr1;
				$this->adr2 = $row->ed_adr2;
				$this->cp 	= $row->ed_cp;
				$this->ville = $row->ed_ville;
				$this->pays = $row->ed_pays;
				$this->web = $row->ed_web;
				$this->ed_comment = $row->ed_comment;
				$this->supplier = new entites($row->ed_num_entite);
				
				$result = entites::get_coordonnees($row->ed_num_entite,1);
				if(pmb_mysql_num_rows($result)){
					$row = pmb_mysql_fetch_object($result);
					pmb_mysql_free_result($result);
					$this->supplier->coords_invoice = $row;
				}
				
				$this->authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [ 'num_object' => $this->id, 'type_object' => AUT_TABLE_PUBLISHERS]);
				$this->num_statut = $this->authority->get_num_statut();
				if($this->web) {
					$this->link = "<a href='".$this->web."' target='_new'>".$this->web."</a>";
				}
				// Determine le lieu de publication
				$l = '';
				if ($this->adr1)  $l = $this->adr1;
				if ($this->adr2)  $l = ($l=='') ? $this->adr2 : $l.', '.$this->adr2;
				if ($this->cp)    $l = ($l=='') ? $this->cp   : $l.', '.$this->cp;
				if ($this->pays)  $l = ($l=='') ? $this->pays : $l.', '.$this->pays;
				if ($this->ville) $l = ($l=='') ? $this->ville : $this->ville.' ('.$l.')';
				if ($l=='')       $l = '[S.l.]';
					
				// Determine le nom de l'editeur
				if ($this->name) $n = $this->name; else $n = '[S.n.]';
					
				// Constitue l'ISBD pour le coupe lieu/editeur
				if ($l == '[S.l.]' AND $n == '[S.n.]') $this->isbd_entry = '[S.l.&nbsp;: s.n.]';
				else $this->isbd_entry = $l.'&nbsp;: '.$n;
				//On fait en sorte que le &nbsp; ne nous embête pas à l'affichage
				global $charset;
				$this->isbd_entry = html_entity_decode($this->isbd_entry,ENT_QUOTES, $charset);
					
				if ($this->ville) {
					if ($this->pays) $this->display = "$this->ville [$this->pays] : $this->name";
					else $this->display = "$this->ville : $this->name";
				} else {
					if ($this->pays) $this->display = "[$this->pays] : $this->name";
					else $this->display = $this->name;
				}
				
				// Ajoute un lien sur la fiche editeur si l'utilisateur a acces aux autorites
				// defined('SESSrights') dans le cas de l'indexation il 'y a pas de AUTH ni de session
				if (defined('SESSrights') && (intval(SESSrights) & AUTORITES_AUTH)) {
				    $this->isbd_entry_lien_gestion = "<a href='./autorites.php?categ=see&sub=publisher&id=".$this->id."' class='lien_gestion'>".$this->display."</a>";
				} else {
				    $this->isbd_entry_lien_gestion = $this->display; 
				}
					
			}
		}
	}
	
	public function build_header_to_export() {
	    global $msg;
	    
	    $data = array(
	        $msg[67],
	        $msg[69],
	        $msg[70],	        
	        $msg[71],
	        $msg['congres_ville_libelle'],
	        $msg['congres_pays_libelle'],
	        $msg[147],
	        $msg[707],
	        $msg[4019],
	    );
	    return $data;
	}
	
	public function build_data_to_export() {
	    $data = array(
	        $this->name,	        
	        $this->adr1,
	        $this->adr2,
	        $this->cp,
	        $this->ville,
	        $this->pays,
	        $this->web,
	        $this->ed_comment,
	        $this->num_statut,
	    );
	    return $data;
	}
	
	protected function get_content_form() {
		global $thesaurus_concepts_active;
		global $publisher_content_form;
		global $collections_list_tpl;
		
		$content_form = $publisher_content_form;
		
		// Nom
		$element = interface_entity_element::get_instance('el0Child_0', 'ed_nom', 'editeur_nom');
		$element->add_input_node('text', $this->name, ['data-pmb-deb-rech' => '1']);
		$content_form = str_replace('!!element_ed_nom!!', $element->get_display(), $content_form);
		
		// Adr1
		$element = interface_entity_element::get_instance('el0Child_1', 'ed_adr1', 'editeur_adr1');
		$element->add_input_node('text', $this->adr1);
		$content_form = str_replace('!!element_ed_adr1!!', $element->get_display(), $content_form);
		
		// Adr2
		$element = interface_entity_element::get_instance('el0Child_2', 'ed_adr2', 'editeur_adr2');
		$element->add_input_node('text', $this->adr2);
		$content_form = str_replace('!!element_ed_adr2!!', $element->get_display(), $content_form);
		
		// CP
		$element = interface_entity_element::get_instance('el0Child_3_a', 'ed_cp', 'editeur_cp');
		$element->set_class('colonne2');
		$element->add_input_node('integer', $this->cp)
		->set_maxlength(10);
		$content_form = str_replace('!!element_ed_cp!!', $element->get_display(), $content_form);
		
		// Ville
		$element = interface_entity_element::get_instance('el0Child_3_b', 'ed_ville', 'editeur_ville');
		$element->set_class('colonne2');
		$element->add_input_node('text', $this->ville)
		->set_class('saisie-20em');
		$content_form = str_replace('!!element_ed_ville!!', $element->get_display(), $content_form);
		
		// Pays
		$element = interface_entity_element::get_instance('el0Child_4', 'ed_pays', '146');
		$element->add_input_node('text', $this->pays)
		->set_class('saisie-20em');
		$content_form = str_replace('!!element_ed_pays!!', $element->get_display(), $content_form);
		
		// Web
		$element = interface_entity_element::get_instance('el0Child_5', 'ed_web', 'editeur_web');
		$element->add_input_node('url', $this->web);
		$content_form = str_replace('!!element_ed_web!!', $element->get_display(), $content_form);
		
		//Fournisseur
		$element = interface_entity_element::get_instance('el0Child_7', 'lib_fou', 'acquisition_ach_fou2');
		$element->add_authority_node($this->supplier->raison_sociale, 'fournisseur')
		->set_class('saisie-30emr')
	    ->set_hidden_name('id_fou')
	    ->set_hidden_value($this->supplier->id_entite)
	    ->set_openPopUpUrl("select.php?what=fournisseur&caller=saisie_editeur&param1=id_fou&param2=lib_fou&param3=adr_fou&id_bibli=&deb_rech=+".pmb_escape()."(this.form.lib_fou.value)");
		$content_form = str_replace('!!element_lib_fou!!', $element->get_display(), $content_form);
		
		//Commentaire
		$element = interface_entity_element::get_instance('el0Child_6', 'ed_comment', 'ed_comment');
		$element->add_textarea_node($this->ed_comment, 62, 4)
		->set_class('saisie-80em')
		->set_attributes(array('wrap' => 'virtual'));
		$content_form = str_replace('!!element_ed_comment!!', $element->get_display(), $content_form);
		
		$aut_link= new aut_link(AUT_TABLE_PUBLISHERS,$this->id);
		$content_form = str_replace('<!-- aut_link -->', $aut_link->get_form('saisie_editeur') , $content_form);
		
		$aut_pperso= new aut_pperso("publisher",$this->id);
		$content_form = str_replace('!!aut_pperso!!', $aut_pperso->get_form(), $content_form);
		
		$content_form = str_replace('!!id!!', $this->id, $content_form);
		
		if($thesaurus_concepts_active == 1){
			$index_concept = new index_concept($this->id, TYPE_PUBLISHER);
			$content_form = str_replace('!!concept_form!!', $index_concept->get_form('saisie_editeur'), $content_form);
		}else{
			$content_form = str_replace('!!concept_form!!',	"", $content_form);
		}
		$authority = new authority(0, $this->id, AUT_TABLE_PUBLISHERS);
		$content_form = str_replace('!!thumbnail_url_form!!', thumbnail::get_form('authority', $authority->get_thumbnail_url()), $content_form);
		
		//Collections
		$collections_content = '';
		$collections = $this->get_collections();
		if (count($collections)) {
			$odd_even=1;
			$collections_content .= "
			<div class='row'>
			<table>";
			foreach ($collections as $collection) {
				$collections_content .= "	<tr class='even'>";
				if ($odd_even==0) {
					$collections_content .= "	<tr class='odd'>";
					$odd_even=1;
				} else if ($odd_even==1) {
					$collections_content .= "	<tr class='even'>";
					$odd_even=0;
				}
				$collections_content .= "<td class='colonne80'>";
				$collections_content .= "<a href='./autorites.php?categ=collections&sub=collection_form&id=".$collection['collection_id']."'>";
				$collections_content .= $collection['collection_name'];
				$collections_content .= '</a></td>';
				$collections_content .='</tr>';
			}
			$collections_content .= "</table>
			</div>";
			$collections_content = str_replace("<!-- collections_list -->",$collections_content,$collections_list_tpl);
		}
		$content_form=str_replace("!!liaisons_collections!!",$collections_content,$content_form);
		
		return $content_form;
	}
	
	public function get_form($duplicate = false) {
		global $msg;
		global $user_input, $nbr_lignes, $page ;
		
		$interface_form = new interface_entity_publisher_form('saisie_editeur');
		if(isset(static::$controller) && is_object(static::$controller)) {
			$interface_form->set_controller(static::$controller);
		}
		$interface_form->set_enctype('multipart/form-data');
		if($this->id && !$duplicate) {
			$interface_form->set_label($msg['148']);
			$interface_form->set_document_title($this->name.' - '.$msg['148']);
		} else {
			$interface_form->set_label($msg['145']);
			$interface_form->set_document_title($msg['145']);
		}
		$interface_form->set_object_id($this->id)
		->set_num_statut($this->num_statut)
		->set_content_form($this->get_content_form())
		->set_table_name('publishers')
		->set_field_focus('ed_nom')
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
		global $publisher_replace_content_form;
		global $msg;
		global $include_path;
		
		if(!$this->id || !$this->name) {
			require_once("$include_path/user_error.inc.php");
			error_message($msg[161], $msg[162], 1, static::format_url('&sub=&id='));
			return false;
		}
	
		$content_form = $publisher_replace_content_form;
		$content_form = str_replace('!!id!!', $this->id, $content_form);
		
		$interface_form = new interface_autorites_replace_form('publisher_replace');
		$interface_form->set_object_id($this->id)
		->set_label($msg["159"]." ".$this->name)
		->set_content_form($content_form)
		->set_table_name('publishers')
		->set_field_focus('ed_libelle')
		->set_url_base(static::format_url());
		print $interface_form->get_display();
	}

	// ---------------------------------------------------------------
	//		get_collections : renvoie toutes les collections liées
	// ---------------------------------------------------------------
	public function get_collections() {
		$collections = array();
		
		$requete = "SELECT * FROM collections WHERE collection_parent=".$this->id;
		$res = pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($res)) {
			while ($row = pmb_mysql_fetch_object($res)) {
				$collection = array();
				$collection['collection_id'] = $row->collection_id;
				$collection['collection_name'] = $row->collection_name;
				$collections[] = $collection;
			}
		}
		
		return $collections;
	}

	// ---------------------------------------------------------------
	//		delete() : suppression de l'editeur
	// ---------------------------------------------------------------
	public function delete() {
		global $msg;
		
		if(!$this->id)
			// impossible d'acceder a cette notice auteur
			return $msg[403]; 

		if(($usage=aut_pperso::delete_pperso(AUT_TABLE_PUBLISHERS, $this->id,0) )){
			// Cette autorité est utilisée dans des champs perso, impossible de supprimer
			return '<strong>'.$this->display.'</strong><br />'.$msg['autority_delete_error'].'<br /><br />'.$usage['display'];
		}
		// effacement dans les notices
		// recuperation du nombre de notices affectees
		$requete = "SELECT COUNT(1) FROM notices WHERE ";
		$requete .= "ed1_id=$this->id OR ";
		$requete .= "ed2_id=$this->id";
		$res = pmb_mysql_query($requete);
		$nbr_lignes = pmb_mysql_result($res, 0, 0);
		if(!$nbr_lignes) {
			// on regarde si l'editeur a des collections enfants 
			$requete = "SELECT COUNT(1) FROM collections WHERE ";
			$requete .= "collection_parent=".$this->id;
			$res = pmb_mysql_query($requete);
			$nbr_lignes = pmb_mysql_result($res, 0, 0);
			if(!$nbr_lignes) {
				
				// On regarde si l'autorité est utilisée dans des vedettes composées
				$attached_vedettes = vedette_composee::get_vedettes_built_with_element($this->id, TYPE_PUBLISHER);
				if (count($attached_vedettes)) {
					// Cette autorité est utilisée dans des vedettes composées, impossible de la supprimer
					return '<strong>'.$this->name."</strong><br />".$msg["vedette_dont_del_autority"].'<br/>'.vedette_composee::get_vedettes_display($attached_vedettes);
				}
				
				// effacement dans la table des editeurs
				$requete = "DELETE FROM publishers WHERE ed_id=".$this->id;
				pmb_mysql_query($requete);
				// liens entre autorités
				$aut_link= new aut_link(AUT_TABLE_PUBLISHERS,$this->id);
				$aut_link->delete();
				
				$aut_pperso= new aut_pperso("publisher",$this->id);
				$aut_pperso->delete();
				
				// nettoyage indexation concepts
				$index_concept = new index_concept($this->id, TYPE_PUBLISHER);
				$index_concept->delete();
				
				// nettoyage indexation
				indexation_authority::delete_all_index($this->id, "authorities", "id_authority", AUT_TABLE_PUBLISHERS);
				
				// effacement de l'identifiant unique d'autorité
				$authority = new authority(0, $this->id, AUT_TABLE_PUBLISHERS);
				$authority->delete();
				
				audit::delete_audit(AUDIT_PUBLISHER,$this->id);
				return false;
			} else {
				// Cet editeur a des collections, impossible de le supprimer
				return '<strong>'.$this->name."</strong><br />{$msg[405]}";
			}
		} else {
			// Cet editeur est utilise dans des notices, impossible de le supprimer
			return '<strong>'.$this->name."</strong><br />{$msg[404]}";
		}
	}

	// ---------------------------------------------------------------
	//		replace($by) : remplacement de l'editeur
	// ---------------------------------------------------------------
	public function replace($by,$link_save=0) {
		global $msg;
		global $pmb_synchro_rdf;
		global $pmb_ark_activate;
	
		if((!$by)||(!$this->id)) {
			// pas de valeur de remplacement !!!
			return "L'identifiant editeur est vide ou l'editeur de remplacement est meme que celui d'origine !";
		}
	
		if($this->id == $by) {
			// impossible de remplacer un editeur par lui-meme
			return $msg[228];
		}
			
		$aut_link= new aut_link(AUT_TABLE_PUBLISHERS,$this->id);
		// "Conserver les liens entre autorités" est demandé
		if($link_save) {
			// liens entre autorités
			$aut_link->add_link_to(AUT_TABLE_PUBLISHERS,$by);		
		}	
		$aut_link->delete();

		vedette_composee::replace(TYPE_PUBLISHER, $this->id, $by);
		
		// a) remplacement dans les notices
		$requete = "UPDATE notices SET ed1_id=$by WHERE ed1_id=".$this->id;
		pmb_mysql_query($requete);
		$requete = "UPDATE notices SET ed2_id=$by WHERE ed2_id=".$this->id;
		pmb_mysql_query($requete);
	
		// b) remplacement dans la table des collections
		$requete = "UPDATE collections SET collection_parent=$by WHERE collection_parent=".$this->id;
		pmb_mysql_query($requete);
		
		// nettoyage indexation concepts
		$index_concept = new index_concept($this->id, TYPE_PUBLISHER);
		$index_concept->delete();
		
		// c) suppression de l'editeur a remplacer
		$requete = "DELETE FROM publishers WHERE ed_id=".$this->id;
		pmb_mysql_query($requete);
		
		//Remplacement dans les champs persos sélecteur d'autorité
		aut_pperso::replace_pperso(AUT_TABLE_PUBLISHERS, $this->id, $by);
		
		audit::delete_audit (AUDIT_PUBLISHER, $this->id) ;
	
		// nettoyage indexation
		indexation_authority::delete_all_index($this->id, "authorities", "id_authority", AUT_TABLE_PUBLISHERS);
		if ($pmb_ark_activate) {
		    $idReplaced = authority::get_authority_id_from_entity($this->id, AUT_TABLE_PUBLISHERS);
		    $idReplacing = authority::get_authority_id_from_entity($by, AUT_TABLE_PUBLISHERS);
		    if ($idReplaced && $idReplacing) {
		        $arkEntityReplaced = ArkEntityPmb::getEntityClassFromType(TYPE_AUTHORITY, $idReplaced);
		        $arkEntityReplacing = ArkEntityPmb::getEntityClassFromType(TYPE_AUTHORITY, $idReplacing);
		        $arkEntityReplaced->markAsReplaced($arkEntityReplacing);
		    }
		}
		// effacement de l'identifiant unique d'autorité
		$authority = new authority(0, $this->id, AUT_TABLE_PUBLISHERS);
		$authority->delete();
		
		editeur::update_index($by);
		
		//mise à jour de l'oeuvre rdf
		if($pmb_synchro_rdf){
			$synchro_rdf = new synchro_rdf();
			$synchro_rdf->replaceAuthority($this->id,$by,'editeur');
		}
		
		return FALSE;
	}

	/**
	 * Initialisation du tableau de valeurs pour update et import
	 */
	protected static function get_default_data() {
		return array(
				'name' => '',
				'adr1' => '',
				'adr2' => '',
				'cp' => '',
				'ville' => '',
				'pays' => '',
				'web' => '',
				'ed_comment' => '',
				'id_fou' => 0,
				'statut' => 1,
				'thumbnail_url' => ''
		);
	}
	
	// ---------------------------------------------------------------
	//		update($value) : mise a jour de l'editeur
	// ---------------------------------------------------------------
	public function update($value) {
		global $msg;
		global $include_path;
		global $pmb_synchro_rdf;
		global $thesaurus_concepts_active;
		
		$value = array_merge(static::get_default_data(), $value);
		
		if(!$value['name'])
			return false;
	
		// nettoyage des valeurs en entree
		$value['name'] = clean_string($value['name']); 
		$value['adr1'] = clean_string($value['adr1']);
		$value['adr2'] = clean_string($value['adr2']);
		$value['cp']   = clean_string($value['cp']);
		$value['ville'] = clean_string($value['ville']);
		$value['pays']  = clean_string($value['pays']);
		$value['web']   = clean_string($value['web']);
								
		// construction de la requete
		$requete = 'SET ed_name="'.$value['name'].'", ';
		$requete .= 'ed_adr1="'.$value['adr1'].'", ';
		$requete .= 'ed_adr2="'.$value['adr2'].'", ';
		$requete .= 'ed_cp="'.$value['cp'].'", ';
		$requete .= 'ed_ville="'.$value['ville'].'", ';
		$requete .= 'ed_pays="'.$value['pays'].'", ';
		$requete .= 'ed_web="'.$value['web'].'", ';
		$requete .= 'ed_comment="'.$value['ed_comment'].'", ';
		$requete .= 'ed_num_entite="'.$value['id_fou'].'", ';
		$requete .= 'index_publisher=" '.strip_empty_chars($value['name'].' '.$value['ville'].' '.$value['pays']).' "';
		if($this->id) {
			// update
			$requete = 'UPDATE publishers '.$requete;
			$requete .= ' WHERE ed_id='.$this->id.' LIMIT 1;';
			if(pmb_mysql_query($requete)) {
				
				audit::insert_modif (AUDIT_PUBLISHER, $this->id) ;
				
				$aut_link= new aut_link(AUT_TABLE_PUBLISHERS,$this->id);
				$aut_link->save_form();
				$aut_pperso= new aut_pperso("publisher",$this->id);
				if($aut_pperso->save_form()){
					$this->cp_error_message = $aut_pperso->error_message;			
					return false;
				}
				
				//mise à jour de l'éditeur dans la base rdf
				if($pmb_synchro_rdf){
					$synchro_rdf = new synchro_rdf();
					$synchro_rdf->updateAuthority($this->id,'editeur');
				}
			}else {
				require_once("$include_path/user_error.inc.php");
				warning($msg[145], $msg[150]);
				return FALSE;
			}
		} else {
			// s'assurer que l'editeur n'existe pas deja
			// on teste sur le nom et la ville seulement. voir a l'usage si necessaire de tester plus
		    $id_doublon = editeur::check_if_exists($value, 1);
		    if ($id_doublon) {
				require_once("$include_path/user_error.inc.php");
				print $this->warning_already_exist($msg[145], $msg[149] . "<a href='./autorites.php?categ=see&sub=publisher&id=$id_doublon'>" . stripslashes(" ({$value['name']}).</a>"));
				return FALSE;
			}
			$requete = 'INSERT INTO publishers '.$requete.';';
			if(pmb_mysql_query($requete)) {
				$this->id=pmb_mysql_insert_id();
				
				audit::insert_creation (AUDIT_PUBLISHER, $this->id) ;
				
				$aut_link= new aut_link(AUT_TABLE_PUBLISHERS,$this->id);
				$aut_link->save_form();
				$aut_pperso= new aut_pperso("publisher",$this->id);
				if($aut_pperso->save_form()){
					$this->cp_error_message = $aut_pperso->error_message;			
					return false;
				}
			} else {
				require_once("$include_path/user_error.inc.php");
				warning($msg[145], $msg[151]);
				return FALSE;
			}
		}
		//update authority informations
		$authority = new authority(0, $this->id, AUT_TABLE_PUBLISHERS);
		$authority->set_num_statut($value['statut']);
		$authority->set_thumbnail_url($value['thumbnail_url']);
		$authority->update();
		
		if($thesaurus_concepts_active == 1){
			$index_concept = new index_concept($this->id, TYPE_PUBLISHER);
			$index_concept->save();
		}

		// Mise à jour des vedettes composées contenant cette autorité
		vedette_composee::update_vedettes_built_with_element($this->id, TYPE_PUBLISHER);
		
		editeur::update_index($this->id);
		
		return TRUE;
	}
	
	// ---------------------------------------------------------------
	//		import($value) : import editeur
	// ---------------------------------------------------------------
	public static function import($data) {
		global $pmb_controle_doublons_diacrit;
	
		// check sur le type de  la variable passee en parametre
		if ((empty($data) && !is_array($data)) || !is_array($data)) {
			// si ce n'est pas un tableau ou un tableau vide, on retourne 0
			return 0;
		}
		
		$data = array_merge(static::get_default_data(), $data);
	
		// tentative de recuperer l'id associee dans la base (implique que l'autorite existe)
		// preparation de la requeªte
		if(!isset(static::$long_maxi)) {
			static::$long_maxi = pmb_mysql_field_len(pmb_mysql_query("SELECT ed_name FROM publishers limit 1"),0);
		}
		
		$key = addslashes(rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['name']))),0,static::$long_maxi)));
		$ville=(isset($data['ville']) ? addslashes(trim($data['ville'])) : '');
		$adr=(isset($data['adr']) ? addslashes(trim($data['adr'])) : '');
		$adr2=(isset($data['adr2']) ? addslashes(trim($data['adr2'])) : '');
		$cp=(isset($data['cp']) ? addslashes(trim($data['cp'])) : '');
		$pays=(isset($data['pays']) ? addslashes(trim($data['pays'])) : '');
		$web=(isset($data['web']) ? addslashes(trim($data['web'])) : '');
		$ed_comment=(isset($data['ed_comment']) ? addslashes(trim($data['ed_comment'])) : '');
		
		if ($key=="") return 0; /* on laisse tomber les editeurs sans nom !!! exact. FL*/
		
		$binary = '';
		if ($pmb_controle_doublons_diacrit) {
		    $binary = 'BINARY';
		} 
		$query = "SELECT ed_id FROM publishers WHERE " . $binary . " ed_name='{$key}' and ed_ville = '{$ville}' ";
		$result = pmb_mysql_query($query);
		if(!$result) die("can't SELECT publisher ".$query);
		// resultat
	
		// recuperation du resultat de la recherche
		if(pmb_mysql_num_rows($result)) {
			$tediteur  = pmb_mysql_fetch_object($result);
			// et recuperation eventuelle de l'id
			if($tediteur->ed_id) {
				return $tediteur->ed_id;
			}
		}
	
		// id non-recuperee, il faut creer la forme.
		$query = 'INSERT INTO publishers SET ed_name="'.$key.'", ed_ville = "'.$ville.'", ed_adr1 = "'.$adr.'", ed_comment="'.$ed_comment.'", ed_adr2="'.$adr2.
		'", ed_cp="'.$cp.'", ed_pays="'.$pays.'", ed_web="'.$web.'", index_publisher=" '.strip_empty_chars($key).' " ';
	
		$result = pmb_mysql_query($query);
		if(!$result) die("can't INSERT into publisher : ".$query);
		$id=pmb_mysql_insert_id();
		
		audit::insert_creation (AUDIT_PUBLISHER, $id) ;
		
		//update authority informations
		$authority = new authority(0, $id, AUT_TABLE_PUBLISHERS);
		$authority->set_num_statut($data['statut']);
		$authority->set_thumbnail_url($data['thumbnail_url']);
		$authority->update();
		
		editeur::update_index($id);
		
		return $id;
	}

	// ---------------------------------------------------------------
	//		search_form() : affichage du form de recherche
	// ---------------------------------------------------------------
	public static function search_form() {
		global $user_query, $user_input;
		global $msg, $charset;
		global $authority_statut;
		
		$user_query = str_replace ('!!user_query_title!!', $msg[357]." : ".$msg[135] , $user_query);
		$user_query = str_replace ('!!action!!', static::format_url('&sub=reach&id='), $user_query);
		$user_query = str_replace ('!!add_auth_msg!!', $msg[143] , $user_query);
		$user_query = str_replace ('!!add_auth_act!!', static::format_url('&sub=editeur_form'), $user_query);
		$user_query = str_replace ('<!-- lien_derniers -->', "<a href='".static::format_url('&sub=editeur_last')."'>$msg[1311]</a>", $user_query);
		$user_query = str_replace('<!-- sel_authority_statuts -->', authorities_statuts::get_form_for(AUT_TABLE_PUBLISHERS, $authority_statut, true), $user_query);
		$user_query = str_replace("!!user_input!!",htmlentities(stripslashes($user_input),ENT_QUOTES, $charset),$user_query);
		
		print pmb_bidi($user_query) ;
	//	print "<br />
	//		<input class='bouton' type='button' value='$msg[143]' onClick=\"document.location='./autorites.php?categ=editeurs&sub=editeur_form'\" />
	//		";
	}
	//---------------------------------------------------------------
	// update_index($id) : maj des index	
	//---------------------------------------------------------------
	public static function update_index($id, $datatype = 'all') {
		indexation_stack::push($id, TYPE_PUBLISHER, $datatype);
		
		// On cherche tous les n-uplet de la table notice correspondant a cet éditeur.
		$query = "select distinct notice_id from notices where ed1_id='".$id."' OR ed2_id='".$id."'";
		authority::update_records_index($query, 'publisher');
	}

	//---------------------------------------------------------------
	// get_informations_from_unimarc : ressort les infos d'un éditeur depuis une notice unimarc
	//---------------------------------------------------------------
	public static function get_informations_from_unimarc($fields){
		$data = array();
		if($fields['214']){
			$data['name'] = $fields['214'][0]['c'][0];
			if($fields['214'][0]['a'][0]) $data['ville'] = clean_string($fields['214'][0]['a'][0]);
			if($fields['214'][0]['b'][0]) $data['adr1'] = clean_string($fields['214'][0]['b'][0]);
			if($fields['214'][0]['d'][0]) $data['year'] = clean_string($fields['214'][0]['d'][0]);
		} elseif($fields['210']){
			$data['name'] = $fields['210'][0]['c'][0];
			if($fields['210'][0]['a'][0]) $data['ville'] = clean_string($fields['210'][0]['a'][0]);
			if($fields['210'][0]['b'][0]) $data['adr1'] = clean_string($fields['210'][0]['b'][0]);
			if($fields['210'][0]['d'][0]) $data['year'] = clean_string($fields['210'][0]['d'][0]);
		}
		if($fields['219']){
		    $data['name'] = $fields['210'][0]['c'][0];
		    if($fields['219'][0]['a'][0]) $data['ville'] = clean_string($fields['219'][0]['a'][0]);
		    if($fields['219'][0]['b'][0]) $data['adr1'] = clean_string($fields['219'][0]['b'][0]);
		    if($fields['219'][0]['d'][0]) $data['year'] = clean_string($fields['219'][0]['d'][0]);
		}
		return $data;
	}	

	public static function check_if_exists($data, $from_form = 0){
	    global $pmb_controle_doublons_diacrit;
		
		if (!isset(static::$long_maxi)) {
			static::$long_maxi = pmb_mysql_field_len(pmb_mysql_query("SELECT ed_name FROM publishers limit 1"),0);
		}
		$ville = '';
		if ($from_form) {
    		$key = rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['name']))),0,static::$long_maxi));
    		if (isset($data['ville'])) {
                $ville = trim($data['ville']);
    		}
		} else  {		    
		    $key = addslashes(rtrim(substr(preg_replace('/\[|\]/', '', rtrim(ltrim($data['name']))),0,static::$long_maxi)));
		    if (isset($data['ville'])) {
                $ville = addslashes(trim($data['ville']));
		    }
		}
		
		$binary = '';
		if ($pmb_controle_doublons_diacrit) {
		    $binary = 'BINARY';
		} 
		$query = "SELECT ed_id FROM publishers WHERE " . $binary . " ed_name='{$key}' and ed_ville = '{$ville}' ";
		$result = @pmb_mysql_query($query);
		if(!$result) die("can't SELECT publisher ".$query);
		// resultat
		if (pmb_mysql_num_rows($result)) {
			// recuperation du resultat de la recherche
			$tediteur  = pmb_mysql_fetch_object($result);
			// et recuperation eventuelle de l'id
			if ($tediteur->ed_id)
				return $tediteur->ed_id;
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
		return './autorites.php?categ=see&sub=publisher&id='.$this->id;
	}

	public function get_isbd() {
		return $this->isbd_entry;
	}
	
	public static function get_format_data_structure($antiloop = false) {
		global $msg;
	
		$main_fields = array();
		$main_fields[] = array(
				'var' => "name",
				'desc' => $msg['editeur_nom']
		);
		$main_fields[] = array(
				'var' => "adr1",
				'desc' => $msg['editeur_adr1']
		);
		$main_fields[] = array(
				'var' => "adr2",
				'desc' => $msg['editeur_adr2']
		);
		$main_fields[] = array(
				'var' => "cp",
				'desc' => $msg['editeur_cp']
		);
		$main_fields[] = array(
				'var' => "ville",
				'desc' => $msg['editeur_ville']
		);
		$main_fields[] = array(
				'var' => "pays",
				'desc' => $msg['146']
		);
		$main_fields[] = array(
				'var' => "web",
				'desc' => $msg['editeur_web']
		);
		$main_fields[] = array(
				'var' => "comment",
				'desc' => $msg['ed_comment']
		);
		$authority = new authority(0, 0, AUT_TABLE_PUBLISHERS);
		$main_fields = array_merge($authority->get_format_data_structure(), $main_fields);
		return $main_fields;
	}
	
	public function format_datas($antiloop = false){
		$formatted_data = array(
				'name' => $this->name,
				'adr1' => $this->adr1,
				'adr2' => $this->adr2,
				'cp' => $this->cp,
				'ville' => $this->ville,
				'pays' => $this->pays,
				'web' => $this->web,
				'comment' => $this->ed_comment
		);
		$authority = new authority(0, $this->id, AUT_TABLE_PUBLISHERS);
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
			return $base_path.'/autorites.php?categ=editeurs'.$url;
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
		$authority = new authority(0, $this->id, AUT_TABLE_PUBLISHERS);
		$display = $authority->get_display_authority_already_exist($error_title, $error_message, $values);
		$display = str_replace("!!action!!", static::format_url(), $display);
		$display = str_replace("!!forcing_button!!", '', $display);
		$display = str_replace('!!hidden_specific_values!!', '', $display);
		return $display;
	}
} # fin de definition de la classe editeur

} # fin de declaration

