<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authorities_caddie.class.php,v 1.61.4.3 2025/04/15 13:14:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des paniers
global $class_path, $include_path;
require_once ($class_path."/caddie_root.class.php");
require_once ($include_path."/templates/authorities_cart.tpl.php");
require_once ($include_path."/templates/cart.tpl.php");
require_once($class_path.'/event/events/event_users_group.class.php');

class authorities_caddie extends caddie_root {
	// propriétés
	public $idcaddie ;
	public $type = ''			;	// Type de panier (AUTHORS = auteurs, CATEGORIES = categories, PUBLISHERS = éditeurs,...)
	public static $table_name = 'authorities_caddie';
	public static $field_name = 'idcaddie';
	public static $table_content_name = 'authorities_caddie_content';
	public static $field_content_name = 'caddie_id';
	
	/**
	 * 
	 * @var onto_handler
	 */
	public static $handler;
	
	// ---------------------------------------------------------------
	//		caddie($id) : constructeur
	// ---------------------------------------------------------------
	public function __construct($caddie_id=0) {
		$this->idcaddie = intval($caddie_id);
		$this->getData();
	}
	
	// ---------------------------------------------------------------
	//		getData() : récupération infos caddie
	// ---------------------------------------------------------------
	protected function getData() {
		parent::getData();
		$this->type = '';
		if($this->idcaddie) {
			$requete = "SELECT * FROM authorities_caddie WHERE idcaddie='$this->idcaddie' ";
			$result = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
				pmb_mysql_free_result($result);
				$this->idcaddie = $temp->idcaddie;
				$this->type = $temp->type;
				$this->name = $temp->name;
				$this->comment = $temp->comment;
				$this->autorisations = $temp->autorisations;
				$this->autorisations_all = $temp->autorisations_all;
				$this->classementGen = $temp->caddie_classement;
				$this->acces_rapide = $temp->acces_rapide;
				$this->favorite_color = $temp->favorite_color;
				$this->creation_user_name = $temp->creation_user_name;
				$this->creation_date = $temp->creation_date;
			
				//liaisons
				
			}
			$this->compte_items();
		}
	}
	
	protected function get_warning_delete() {
		global $msg;
		
		$message_delete_warning = $msg["caddie_used_in_warning"];
		foreach ($this->liaisons as $type => $values){
			if(count($values)){
				switch ($type){
					default://On ne doit pas passer par là
						break;//On sort aussi du foreach
				}
			}
		}
		$message_delete_warning .= "\\n";
		return $message_delete_warning;
	}
	
	public static function get_types() {
		return array('MIXED', 'AUTHORS', 'CATEGORIES', 'PUBLISHERS', 'COLLECTIONS', 'SUBCOLLECTIONS', 'SERIES', 'TITRES_UNIFORMES', 'INDEXINT', 'CONCEPTS', 'AUTHPERSO');
	}
	
	// Liaisons pour le panier
	protected function get_links_form() {
		global $charset;
			
		$links_form = "";
		$end = false;
		foreach ( $this->liaisons as $type => $values ) {
			if (count ( $values )) {
				$links_form .= "<br>";
				switch ($type) {
					default : // On ne doit pas passer par là
						$links_form = "";
						//break 2; // On sort aussi du foreach
						$end = true;
						break;
				}
				if($end) break;
				foreach ( $values as $infos ) {
					$links_form .= str_replace ( array (
							"!!id!!",
							"!!name!!"
					), array (
							$infos ["id"],
							htmlentities ( $infos ["lib"], ENT_QUOTES, $charset )
					), $link );
				}
				$links_form .= "</div>";
			}
		}
		return $links_form;
	}
	
	public function set_properties_from_form() {
		global $cart_type;
		global $classementGen_authorities_caddie;

		parent::set_properties_from_form();
		if(!$this->idcaddie || ($this->idcaddie && !$this->nb_item && $cart_type)) {
			$this->type = $cart_type;
		}
		$this->classementGen = stripslashes($classementGen_authorities_caddie);
	}
	
	protected static function get_order_cart_list() {
		return " order by type, name, comment ";
	}
	
	static public function get_cart_data($temp) {
		$nb_item = 0 ;
		$nb_item_pointe = 0 ;
		$rqt_nb_item="select count(1) from authorities_caddie_content where caddie_id='".$temp->idcaddie."' ";
		$nb_item = pmb_mysql_result(pmb_mysql_query($rqt_nb_item), 0, 0);
		$rqt_nb_item_pointe = "select count(1) from authorities_caddie_content where caddie_id='".$temp->idcaddie."' and (flag is not null and flag!='') ";
		$nb_item_pointe = pmb_mysql_result(pmb_mysql_query($rqt_nb_item_pointe), 0, 0);
		
		return array(
				'idcaddie' => $temp->idcaddie,
				'name' => $temp->name,
				'type' => $temp->type,
				'comment' => $temp->comment,
				'autorisations' => $temp->autorisations,
				'autorisations_all' => $temp->autorisations_all,
				'caddie_classement' => $temp->caddie_classement,
				'acces_rapide' => $temp->acces_rapide,
				'favorite_color' => $temp->favorite_color,
				'nb_item' => $nb_item,
				'nb_item_pointe' => $nb_item_pointe,
		);
	}
	
	// liste des paniers disponibles
	static public function get_cart_list($restriction_panier="",$acces_rapide = 0) {
		if($restriction_panier == 'MIXED') {
			$caddies = parent::get_cart_list("", $acces_rapide);
		} else {
			$caddies = array_merge(
				parent::get_cart_list($restriction_panier, $acces_rapide),
				parent::get_cart_list("MIXED", $acces_rapide)
			);
		}
		//Dédoublonnage 
		return array_map("unserialize", array_unique(array_map("serialize", $caddies)));
	}
	
	// création d'un panier vide
	public function create_cart() {
		$requete = "insert into authorities_caddie set name='".addslashes($this->name)."', type='".$this->type."', comment='".addslashes($this->comment)."', autorisations='".$this->autorisations."', autorisations_all='".$this->autorisations_all."', caddie_classement='".addslashes($this->classementGen)."', acces_rapide='".$this->acces_rapide."', favorite_color='".addslashes($this->favorite_color)."' ";
		$user = $this->get_info_user();
		if(is_object($user)) {
			$requete .= ", creation_user_name='".addslashes($user->name)."', creation_date='".date("Y-m-d H:i:s")."'";
		}
		pmb_mysql_query($requete);
		$this->idcaddie = pmb_mysql_insert_id();
		$this->compte_items();
		return $this->idcaddie;
	}
	
	// sauvegarde du panier
	public function save_cart() {
		$query = "update authorities_caddie set name='".addslashes($this->name)."', type='".$this->type."', comment='".addslashes($this->comment)."', autorisations='".$this->autorisations."', autorisations_all='".$this->autorisations_all."', caddie_classement='".addslashes($this->classementGen)."', acces_rapide='".$this->acces_rapide."', favorite_color='".addslashes($this->favorite_color)."' where ".static::get_field_name()."='".$this->get_idcaddie()."'";
		pmb_mysql_query($query);
		return true;
	}

	protected function get_type_object_from_item($item=0) {
		$authority = new authority($item);
		$object_instance = $authority->get_object_instance();
		return $authority->get_type_object();
	}
	
	// ajout d'un item
	public function add_item($item=0, $object_type="AUTHORS") {
		if (!$item) return CADDIE_ITEM_NULL ;
		
		// les objets sont cohérents
		if ($object_type==$this->type || $this->type == "MIXED") {
			$requete_compte = "select count(1) from authorities_caddie_content where caddie_id='".$this->get_idcaddie()."' AND object_id='".$item."' ";
			$result_compte = pmb_mysql_query($requete_compte);
			$deja_item=pmb_mysql_result($result_compte, 0, 0);
			if (!$deja_item) {
				$requete= "insert into authorities_caddie_content set caddie_id='".$this->get_idcaddie()."', object_id='".$item."' ";
				pmb_mysql_query($requete);
			}
		} elseif($object_type == "MIXED" && $this->get_type_object_from_item($item) == static::get_const_from_type($this->type)) {
			$requete_compte = "select count(1) from authorities_caddie_content where caddie_id='".$this->get_idcaddie()."' AND object_id='".$item."' ";
			$result_compte = pmb_mysql_query($requete_compte);
			$deja_item=pmb_mysql_result($result_compte, 0, 0);
			if (!$deja_item) {
				$requete= "insert into authorities_caddie_content set caddie_id='".$this->get_idcaddie()."', object_id='".$item."' ";
				pmb_mysql_query($requete);
			}
		}
	}
	
	public function del_item_base($item=0,$forcage=array()) {
		if (!$item) return CADDIE_ITEM_NULL ;
		
		$authority = new authority($item);
		$object_instance = $authority->get_object_instance();
		
		$entity_type = authority::aut_const_to_type_const($authority->get_type_object());
		if($this->has_del_item_base_rights($item, $entity_type)){
			switch ($authority->get_type_object()) {
				case AUT_TABLE_INDEX_CONCEPT :
				case AUT_TABLE_CONCEPT :
					global $class_path;
					
					$params = new stdClass();
					$params->action = 'delete_from_cart';
					$params->categ = 'concepts';
					$params->sub = 'concept';
					$params->id = $object_instance->get_id();
					
					static::get_handler();
					
					$onto_skos_controler = new onto_skos_controler(static::$handler, $params);
					//$response = $onto_skos_controler->proceed();
					
					$skos_concept = new skos_concept($object_instance->get_id());
					$response = $skos_concept->delete();
					
					if ($response === false) {
						return CADDIE_ITEM_AUT_USED;
					} else {
						return CADDIE_ITEM_SUPPR_BASE_OK;
					}
					break;
				case AUT_TABLE_AUTHPERSO :
					$authperso = new authperso(0, $object_instance->id);
					if ($authperso->delete($object_instance->id) === false) {
						return CADDIE_ITEM_SUPPR_BASE_OK;
					} else  {
						return CADDIE_ITEM_AUT_USED;
					}
					break;
				default :
					if ($object_instance->delete() === false) {
						return CADDIE_ITEM_SUPPR_BASE_OK;
					} else  {
						return CADDIE_ITEM_AUT_USED;
					}
					break;
			}
		} else {
			return CADDIE_ITEM_NO_DELETION_RIGHTS;
		}
		/* Appeler la methode delete pour chacun des types d'autorités
		 * Faire attention au retour de chacune des méthodes pour retourner la bonne constante : CADDIE_ITEM_SUPPR_BASE_OK - CADDIE_ITEM_AUTHORITY_USED - CADDIE_ITEM_OK
		 */
		return CADDIE_ITEM_OK ;
	}
	
	// suppression d'un item de tous les caddies
	public function del_item_all_caddies($item, $type) {
		$requete_suppr = "delete from authorities_caddie_content where object_id='".$item."'";
		pmb_mysql_query($requete_suppr);
	}

	public function del_item_flag() {
		$requete = "delete FROM authorities_caddie_content where caddie_id='".$this->idcaddie."' and (flag is not null and flag!='') ";
		pmb_mysql_query($requete);
		$this->compte_items();
	}

	public function del_item_no_flag() {
		$requete = "delete FROM authorities_caddie_content where caddie_id='".$this->idcaddie."' and (flag is null or flag='') ";
		pmb_mysql_query($requete);
		$this->compte_items();
	}

	public function pointe_item($item=0, $object_type="", $blob="", $blob_type="") {
		$requete = "update authorities_caddie_content set flag='1' where caddie_id='".$this->idcaddie."' and object_id='".$item."' ";
		pmb_mysql_query($requete);
		$this->compte_items();
		return CADDIE_ITEM_OK ;
	}
	
	// suppression d'un panier
	public function delete() {
		parent::delete();
	}

	// get_cart() : ouvre un panier et récupère le contenu
	public function get_cart($flag="") {
		$cart_list=array();
		switch ($flag) {
			case "FLAG" :
				$requete = "SELECT * FROM authorities_caddie_content where caddie_id='".$this->idcaddie."' and (flag is not null and flag!='') ";
				break ;
			case "NOFLAG" :
				$requete = "SELECT * FROM authorities_caddie_content where caddie_id='".$this->idcaddie."' and (flag is null or flag='') ";
				break ;
			case "ALL" :
			default :
				$requete = "SELECT * FROM authorities_caddie_content where caddie_id='".$this->idcaddie."' ";
				break ;
			}
		$result = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($result)) {
			while ($temp = pmb_mysql_fetch_object($result)) {
				$cart_list[] = $temp->object_id;
			}
		} 
		return $cart_list;
	}

	// compte_items 
	public function compte_items() {
		parent::compte_items();
	}
	
	public static function get_array_actions($id_caddie = 0, $type_caddie = '', $actions_to_remove = array()) {
		global $msg;
		$array_actions = array();
		if (empty($actions_to_remove['edit_cart'])) {
			$array_actions[] = array('msg' => $msg["caddie_menu_action_edit_panier"], 'location' => static::get_constructed_link('gestion', 'panier', 'edit_cart', $id_caddie, '&item=0'));
		}
		if (empty($actions_to_remove['pointage_raz'])) {
			$array_actions[] = array('msg' => $msg["caddie_menu_pointage_raz"], 'location' => static::get_constructed_link('pointage', 'raz', '', $id_caddie));
		}
		if (empty($actions_to_remove['supprpanier'])) {
			$array_actions[] = array('msg' => $msg["caddie_menu_action_suppr_panier"], 'location' => static::get_constructed_link('action', 'supprpanier', 'choix_quoi', $id_caddie, '&item=0'));
		}
// 		$array_actions[] = array('msg' => $msg["caddie_menu_action_transfert"], 'location' => static::get_constructed_link('action', 'transfert', 'transfert', $id_caddie, '&item=0'));
		if (empty($actions_to_remove['edition'])) {
			$array_actions[] = array('msg' => $msg["caddie_menu_action_edition"], 'location' => static::get_constructed_link('action', 'edition', 'choix_quoi', $id_caddie, '&item=0'));
		}
		//$array_actions[] = array('msg' => $msg["caddie_menu_action_export"], 'location' => static::get_constructed_link('action', 'export', 'choix_quoi', $id_caddie, '&item=0'));
		if (empty($actions_to_remove['selection'])) {
			$array_actions[] = array('msg' => $msg["caddie_menu_action_selection"], 'location' => static::get_constructed_link('action', 'selection', '', $id_caddie, '&item=0'));
		}
		$evt_handler = events_handler::get_instance();
		$event = new event_users_group("users_group", "get_autorisation_del_base");
		$evt_handler->send($event);
		if(!$event->get_error_message() && empty($actions_to_remove['supprbase'])){
			$array_actions[] = array('msg' => $msg["caddie_menu_action_suppr_base"], 'location' => static::get_constructed_link('action', 'supprbase', 'choix_quoi', $id_caddie, '&object_type='.$type_caddie.'&item=0'));
		}
		if (empty($actions_to_remove['reindex'])) {
			$array_actions[] = array('msg' => $msg["caddie_menu_action_reindex"], 'location' => static::get_constructed_link('action', 'reindex', 'choix_quoi', $id_caddie, '&item=0'));
		}
		return $array_actions;
	}
	
	protected function replace_in_action_query($query, $by) {
// 		$final_query=str_replace("CADDIE(MIXED)",$by,$final_query);
		$final_query = preg_replace("/CADDIE\(((.*,)?MIXED(,[^\)]*)?|(.*,)?AUTHORS(,[^\)]*)?|(.*,)?CATEGORIES(,[^\)]*)?|(.*,)?PUBLISHERS(,[^\)]*)?|(.*,)?COLLECTIONS(,[^\)]*)?|(.*,)?SUBCOLLECTIONS(,[^\)]*)?|(.*,)?SERIES(,[^\)]*)?|(.*,)?TITRES_UNIFORMES(,[^\)]*)?|(.*,)?INDEXINT(,[^\)]*)?|(.*,)?AUTHPERSO(,[^\)]*)?|(.*,)?CONCEPTS(,[^\)]*)?)\)/", $by, $query);
		return $final_query;
	}
	
	protected function get_edition_template_form() {
		global $cart_choix_quoi_edition;
		return $cart_choix_quoi_edition;
	}
	
	public function get_list_caddie_content_ui() {
		global $show_list;
		
		list_authorities_caddie_content_ui::set_id_caddie($this->idcaddie);
		list_authorities_caddie_content_ui::set_object_type($this->type);
		if($show_list) {
			list_authorities_caddie_content_ui::set_show_list(true);
		}
		return new list_authorities_caddie_content_ui();
	}
	
	public function get_edition_form($action="", $action_cancel="") {
		if(!$action) $action = "./autorites/caddie/action/edit.php?idcaddie=".$this->get_idcaddie();
		if(!$action_cancel) $action_cancel = static::get_constructed_link('action', 'edition');
		$form = parent::get_edition_form($action, $action_cancel);
		$form = str_replace('<!-- !!boutons_supp!! -->', '', $form);
		$form = str_replace('<!-- notice_template -->', '', $form);
		return $form;
	}
	
	private function generate_authority($authority){
		global $include_path;
		$template_path = $authority->find_template("list");
		if(file_exists($template_path)){
			$h2o = H2o_collection::get_instance($template_path);
			$context = array('list_element' => $authority);
			return $h2o->render($context);
		}
		return '';
	}
	
	// affichage du contenu complet d'un caddie
	public function aff_cart_objects($url_base="./autorites.php?categ=caddie&sub=gestion&quoi=panier&idcaddie=0", $no_del=false,$rec_history=0, $no_point=false ) {
		global $msg, $begin_result_liste;
		global $nbr_lignes, $page, $nb_per_page_search ;
		
		// nombre de références par pages
		if ($nb_per_page_search != "") $nb_per_page = $nb_per_page_search ;
		else $nb_per_page = 10;
		
		// on récupére le nombre de lignes
		if(!$nbr_lignes) {
			$requete = "SELECT count(1) FROM authorities_caddie_content where caddie_id='".$this->get_idcaddie()."' ".static::get_query_filters();
			$res = pmb_mysql_query($requete);
			$nbr_lignes = pmb_mysql_result($res, 0, 0);
		}
		
		if(!$page) $page=1;
		$debut =($page-1)*$nb_per_page;
		
		//Calcul des variables pour la suppression d'items
		$modulo = $nbr_lignes%$nb_per_page;
		if($modulo == 1){
			$page_suppr = (!$page ? 1 : $page-1);
		} else {
			$page_suppr = $page;
		}
		$nb_after_suppr = ($nbr_lignes ? $nbr_lignes-1 : 0);
		
		if($nbr_lignes) {
			$requete = "SELECT object_id, flag FROM authorities_caddie_content where caddie_id='".$this->get_idcaddie()."' ".static::get_query_filters();
			$requete.= " LIMIT $debut,$nb_per_page ";
		} else {
			print $msg[399];
			return;
		}
		
		$liste=array();
		$result = pmb_mysql_query($requete);
		if ($result) {
			if(pmb_mysql_num_rows($result)) {
				while ($temp = pmb_mysql_fetch_object($result)) {
					$liste[] = array('object_id' => $temp->object_id, 'flag' => $temp->flag ) ;
				}
			}
		}
		if ((empty($liste) && !is_array($liste)) || !is_array($liste)) {
			print $msg[399];
			return;
		} else {
			print $this->get_js_script_cart_objects('autorites');
			print $begin_result_liste;
			print authorities_caddie::show_actions($this->get_idcaddie(), $this->type);
			foreach ($liste as $object) {
				$authority = new authority($object['object_id']);
				if (!$no_del) {
					$lien_suppr_cart = "<a href='$url_base&action=del_item&item=".$object['object_id']."&page=$page_suppr&nbr_lignes=$nb_after_suppr&nb_per_page=$nb_per_page'><img src='".get_url_icon('basket_empty_20x20.gif')."' alt='basket' title=\"".$msg['caddie_icone_suppr_elt']."\" /></a>";
					$authority->set_icon_del_in_cart($lien_suppr_cart);
				}
				if (!$no_point) {
					if ($object['flag']) $marque_flag ="<img src='".get_url_icon('depointer.png')."' id='caddie_".$this->get_idcaddie()."_item_".$object['object_id']."' title=\"".$msg['caddie_item_depointer']."\" onClick='del_pointage_item(".$this->get_idcaddie().",".$object['object_id'].");' style='cursor: pointer'/>" ;
					else $marque_flag ="<img src='".get_url_icon('pointer.png')."' id='caddie_".$this->get_idcaddie()."_item_".$object['object_id']."' title=\"".$msg['caddie_item_pointer']."\" onClick='add_pointage_item(".$this->get_idcaddie().",".$object['object_id'].");' style='cursor: pointer'/>" ;
				} else {
					if ($object['flag']) $marque_flag ="<img src='".get_url_icon('tick.gif')."'/>" ;
					else $marque_flag ="" ;
				}
				$authority->set_icon_pointe_in_cart($marque_flag);
				print $this->generate_authority($authority);
			}
			print "<br />".aff_pagination ($url_base, $nbr_lignes, $nb_per_page, $page, 10, false, true);
		}
		return;
	}
	
	public function aff_cart_titre() {
		global $msg;
		
		$link = static::get_constructed_link('gestion', 'panier', '', $this->idcaddie, '&object_type='.$this->type.'&item=0');
		return "
			<div class='titre-panier'>
				<h3>
					<a href='".$link."'>".$this->name.($this->comment ? " - ".$this->comment : "")."</a> <i><small>(".$msg["caddie_de_".$this->type].")</small></i>
				</h3>
			</div>";
	}
	
	protected function get_choix_quoi_template_form() {
		global $authorities_cart_choix_quoi;
		return $authorities_cart_choix_quoi;
	}
	
	public function reindex_from_list($liste=array()) {
		global $msg;
		
		$pb = new progress_bar($msg['caddie_situation_reindex_encours'], count($liste), 5);
		foreach ($liste as $object) {
		    $authority = new authority($object);
		    switch ($authority->get_type_object()) {
		        case AUT_TABLE_CONCEPT :
		            $concept = new concept($authority->get_num_object());
		            $concept->update_index();
		        	break;
		        case AUT_TABLE_AUTHPERSO :
		            $authperso = new authperso(0, $authority->get_num_object());
		            $authperso->update_global_index($authority->get_num_object());
		        	break;
		        default :
    			$indexation_authority = indexations_collection::get_indexation($authority->get_type_object());
    			$indexation_authority->maj($authority->get_num_object());
		    }
		    $pb->progress();
		}
		$pb->hide();
	}
	
	public function del_items_base_from_list($liste=array()) {
		$res_aff_suppr_base = array();
		foreach ($liste as $object) {
			$del_item_base = $this->del_item_base($object);
			if ($del_item_base == CADDIE_ITEM_SUPPR_BASE_OK) {
				$this->del_item_all_caddies($object, $this->type) ;
			} else {
				$authority = new authority($object);
				if(empty($res_aff_suppr_base[$del_item_base])) {
					$res_aff_suppr_base[$del_item_base] = array();
				}
				$res_aff_suppr_base[$del_item_base][] = $this->generate_authority($authority);
			}
		}
		return $res_aff_suppr_base;
	}
	
	protected function write_header_tableau($authority_type = 0) {
	    global $charset;
		global $msg;
		global $worksheet;
		
		$worksheet->write_string(2, 0, $msg['caddie_action_marque']);
		$col = 1;			
		$worksheet->write_string(2,$col++, $msg[1601]);
		$worksheet->write_string(2,$col++, $msg['cms_authority_format_data_isbd']);
		$data_to_export = array();		
		if ($this->type == 'AUTHPERSO') {
		    $list = $this->get_tab_list();
		    foreach ($list as $object) {
		        $authority_instance = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, $object['object_id'], [ 'type_object' => $this->get_const_from_type($this->type)]);
		        break;
		    }
		} else {
		    $type_object = $authority_type ?? $this->get_const_from_type($this->type);
		    $authority_instance = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, ['type_object' => $type_object]);
		}
		$object_instance = $authority_instance->get_object_instance();
		if (is_object($object_instance) && method_exists($object_instance, 'build_header_to_export')) {
		    $data_to_export = $object_instance->build_header_to_export();
		    foreach ($data_to_export as $data) {
		        $worksheet->write_string(2,$col++,$data);
		    }
		}
		//Statut
		$worksheet->write_string(2,$col++, $msg[4019]);
		$worksheet->write_string(2,$col++, $msg['aut_link']);
		$p_perso = $authority_instance->get_p_perso();
		foreach ($p_perso as $data) {
		    $worksheet->write_string(2, $col++, html_entity_decode(strip_tags($data['TITRE_CLEAN']),ENT_QUOTES, $charset));
		}
	}
	
	protected function write_content_tableau($authority_type = 0) {
	    global $charset;
	    global $worksheet;
	    
		$list = $this->get_tab_list();
		$debligne_excel = 4;
		foreach ($list as $cle => $object) {
		    if (empty($authority_type) || $authority_type == $object['type_object']) {
    		    $authority_instance = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, $object['object_id'], [ 'type_object' => $this->get_const_from_type($this->type)]);
    			
    			if ($object['flag']) $worksheet->write_string(($cle+$debligne_excel),0,"X");
    			$col = 1;
    			$worksheet->write_string(($cle+$debligne_excel),$col++,$authority_instance->get_id());
    			$worksheet->write_string(($cle+$debligne_excel),$col++,$authority_instance->get_isbd());
    			$data_to_export = array();
    			$object_instance = $authority_instance->get_object_instance();
    			if (is_object($object_instance) && method_exists($object_instance, 'build_data_to_export')) {
    			    $data_to_export = $object_instance->build_data_to_export();
    			    foreach ($data_to_export as $data) {
    			        $worksheet->write_string(($cle+$debligne_excel),$col++,$data);
    			    }
    			}
    			$worksheet->write_string(($cle+$debligne_excel),$col++,$authority_instance->get_statut_label());
    			$worksheet->write_string(($cle+$debligne_excel),$col++,strip_tags($authority_instance->init_autlink_class()->get_display()));
    			$p_perso = $authority_instance->get_p_perso();
    			foreach ($p_perso as $data) {
    			    $worksheet->write_string(($cle + $debligne_excel), $col++, html_entity_decode(strip_tags($data['AFF']),ENT_QUOTES, $charset));
    			}
		    }
		}
	}
	
	protected function get_display_header_tableauhtml($authority_type = 0) {
		global $msg;
		
		$display = '';
		$display .= "\n<tr>";
		$display .= "<th class='align_left'>".$msg['caddie_action_marque']."</th>";
		
		$display .= "<th class='align_left'>ID</th>";
		$display .= "<th class='align_left'>ISBD</th>";
		
		if ($this->type == 'AUTHPERSO') {
    		$list = $this->get_tab_list(); 
    		foreach ($list as $object) {
    		    $authority_instance = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, $object['object_id'], [ 'type_object' => $this->get_const_from_type($this->type)]);
    		    break;
    		}
		} else {
		    $type_object = $authority_type ?? $this->get_const_from_type($this->type);
		    $authority_instance = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, ['type_object' => $type_object]);
		}
		$object_instance = $authority_instance->get_object_instance();
		if (is_object($object_instance) && method_exists($object_instance, 'build_header_to_export')) {
		    $data_to_export = $object_instance->build_header_to_export();
		    foreach ($data_to_export as $data) {
		        $display .= "<th>".$data."</th>";
		    }
		}
		//Statut
		$display .= "<th>".$msg[4019]."</th>";
		$display .= "<th>".$msg['aut_link']."</th>";
		$p_perso = $authority_instance->get_p_perso();
		foreach ($p_perso as $data) {
		    $display .= "<th>".$data['TITRE_CLEAN']."</th>";
		}
		$display .= "</tr>";
		return $display;
	}
	
	protected function get_display_content_tableauhtml($authority_type = 0) {
	    
		$list = $this->get_tab_list();		
		$display = '';
		foreach ($list as $object) {
		    if (empty($authority_type) || $authority_type == $object['type_object']) {
    			$display .= "<tr>";
    			if ($object['flag']) $display .= "<td class='center'>X</td>";
    			else $display .= "<td class='center'></td>";
    			
    			$authority_instance = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, $object['object_id'], [ 'type_object' => $this->get_const_from_type($this->type)]);	
    			
    			$display .= "<td class='center'>".$authority_instance->get_id()."</td>";
    			$display .= "<td>".$authority_instance->get_isbd()."</td>";
    			
    			$object_instance = $authority_instance->get_object_instance();
    			if (is_object($object_instance) && method_exists($object_instance, 'build_data_to_export')) {
    			    $data_to_export = $object_instance->build_data_to_export();
    			    foreach ($data_to_export as $data) {
    			        $display .= "<td>".$data."</td>";
    			    }
    			}
    			$display .= "<td>".$authority_instance->get_statut_label()."</td>";
    			$display .= "<td>".strip_tags($authority_instance->init_autlink_class()->get_display())."</td>";
    			
    			$p_perso = $authority_instance->get_p_perso();
    			foreach ($p_perso as $data) {
    			    $display .= "<td>".$data['AFF']."</td>";
    			}
    			$display .= "</tr>";
		    }
		}
		return $display;
	}
	
	public function get_idcaddie() {
		return $this->idcaddie;
	}
	
	public function get_id() {
	    return $this->idcaddie;
	}
	
	public function set_idcaddie($idcaddie) {
	    $this->idcaddie = intval($idcaddie);
	}
	
	public static function get_type_from_const($const) {
		switch($const){
			case AUT_TABLE_AUTHORS :
				return "AUTHORS";
			case AUT_TABLE_PUBLISHERS :
				return "PUBLISHERS";
			case AUT_TABLE_COLLECTIONS :
				return "COLLECTIONS";
			case AUT_TABLE_SUB_COLLECTIONS :
				return "SUBCOLLECTIONS";
			case AUT_TABLE_SERIES :
				return "SERIES";
			case AUT_TABLE_INDEXINT :
				return "INDEXINT";
			case AUT_TABLE_TITRES_UNIFORMES :
				return "TITRES_UNIFORMES";
			case AUT_TABLE_CATEG :
				return "CATEGORIES";
			case AUT_TABLE_CONCEPT :
				return "CONCEPTS";
			case AUT_TABLE_AUTHPERSO:
				return "AUTHPERSO";
		}
	}
	
	public static function get_const_from_type($const) {
	    switch($const){
	        case "AUTHORS" :
	            return AUT_TABLE_AUTHORS;
	        case "PUBLISHERS" :
	            return AUT_TABLE_PUBLISHERS;
	        case "COLLECTIONS" :
	            return AUT_TABLE_COLLECTIONS;
	        case "SUBCOLLECTIONS" :
	            return AUT_TABLE_SUB_COLLECTIONS;
	        case  "SERIES" :
	            return AUT_TABLE_SERIES;
	        case  "INDEXINT" :
	            return AUT_TABLE_INDEXINT;
	        case "TITRES_UNIFORMES" :
	            return AUT_TABLE_TITRES_UNIFORMES;
	        case "CATEGORIES" :
	            return AUT_TABLE_CATEG;
	        case "CONCEPTS" :
	            return AUT_TABLE_CONCEPT;
	        case "AUTHPERSO":
	            return AUT_TABLE_AUTHPERSO;
	    }
	}
	
	protected static function get_handler() {
		if(!isset(static::$handler)) {
			static::$handler = new onto_handler('', skos_onto::get_store(), array(), skos_datastore::get_store(), array(), array(), 'http://www.w3.org/2004/02/skos/core#prefLabel');
			static::$handler->get_ontology();
		}
		return static::$handler;
	}
	
	public function get_tab_list() {
	    global $elt_flag, $elt_no_flag;
	    
	    $list = array();
	    
	    if (($elt_flag=="") && ($elt_no_flag=="")) {
	        $elt_no_flag = 1;
	        $elt_flag = 1;
	    }
	    $query = "SELECT " . static::$table_content_name . ".*, authorities.type_object 
        FROM " . static::$table_content_name." 
        JOIN authorities ON object_id = id_authority 
        WHERE ".static::$field_content_name."='".$this->get_idcaddie()."' ";
	    
	    if ($elt_flag && $elt_no_flag ) $complement_clause = "";
	    if (!$elt_flag && $elt_no_flag ) $complement_clause = " and (flag is null or flag = '') ";
	    if ($elt_flag && !$elt_no_flag ) $complement_clause = " and flag is not null ";
	    $query .= $complement_clause." order by object_id";
	    $result = pmb_mysql_query($query);
	    if(pmb_mysql_num_rows($result)) {
	        while ($row = pmb_mysql_fetch_object($result)) {
	            $list[] = ['object_id' => $row->object_id, 'flag' => $row->flag, 'type_object' => $row->type_object];
	        }
	    }
	    return $list;
	}
	
	public function get_authorities_types_from_mixed_caddie() {
	    $query = "SELECT DISTINCT type_object
        FROM authorities
        JOIN " . static::$table_content_name . " ON object_id = id_authority
        WHERE " . static::$field_content_name . " = '" . $this->get_idcaddie() . "'";
	    
	    $types = [];
	    $res = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($res)) {
	        while ($row = pmb_mysql_fetch_assoc($res)) {
    	        $types[] = $row['type_object'];
	        }
	    }
	    
	    return $types;
	}
	
	public function get_display_tableauhtml() {
	    if ($this->type == 'MIXED') {
	        $display = "";
	        $authorities_types = $this->get_authorities_types_from_mixed_caddie();
	        foreach ($authorities_types as $authority_type) {
    	        $display .= "<table>";
    	        $display .= $this->get_display_header_tableauhtml($authority_type);
    	        $display .= $this->get_display_content_tableauhtml($authority_type);
    	        $display .= "</table>";
	        }
	        return $display;
	    }
	    
	    return parent::get_display_tableauhtml();
	}
	
	public function write_tableau() {
	    if ($this->type == 'MIXED') {
	        $display = "";
	        $authorities_types = $this->get_authorities_types_from_mixed_caddie();
	        foreach ($authorities_types as $authority_type) {
	            $display .= $this->write_header_tableau($authority_type);
	            $display .= $this->write_content_tableau($authority_type);
	        }
    	    
    	    return $display;
	    }
	    
	    return parent::write_tableau();
	}
	
	public static function get_constructed_link($sub = '', $sub_categ = '', $action = '', $idcaddie = 0, $args_others = '') {
		global $base_path;
		
		$link = $base_path . "/autorites.php?categ=caddie&sub=" . $sub;
		if ($sub_categ) {
			switch ($sub) {
				case 'gestion':
					$link .= "&quoi=" . $sub_categ;
					break;
				case 'collecte':
				case 'pointage':
					$link .= "&moyen=" . $sub_categ;
					break;
				case 'action':
					$link .= "&quelle=" . $sub_categ;
					break;
			}
		}
		if ($action) {
			$link .= "&action=" . $action;
		}
		if ($args_others) {
			$link .= $args_others;
		}
		if ($idcaddie) {
			$link .= "&idcaddie=" . $idcaddie;
		}
		return $link;
	}
} // fin de déclaration de la classe caddie