<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa_caddie_controller.class.php,v 1.4 2019/08/01 13:16:34 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/caddie/caddie_controller.class.php");

class resa_caddie_controller extends caddie_controller {
	
	protected static $categ;
	
	protected static $id_empr;
	
	protected static $groupID;
	
	public static function get_aff_editable_paniers($idcaddie) {
		global $msg;
	
		return aff_paniers($idcaddie, "NOTI", static::get_constructed_link(), "add_item", $msg["caddie_select_afficher"], "", 0, 1, 1);
	}
	
	public static function set_categ($categ) {
		static::$categ = $categ;
	}
	
	public static function set_id_empr($id_empr) {
		static::$id_empr = (int) $id_empr;
	}
	
	public static function set_groupID($groupID) {
	    static::$groupID = (int) $groupID;
	}
	
	public static function get_constructed_link($sub='', $sub_categ='', $action='', $idcaddie=0, $args_others='') {
		global $base_path;
		
		if(!isset(static::$categ)) {
			static::$categ = 'resa';
		}
		$link = $base_path."/circ.php?categ=".static::$categ."&mode=3&unq=".md5(microtime())."&id_empr=".static::$id_empr."&groupID=".static::$groupID;
		if($action) $link .= "&action=".$action;
		if($args_others) $link .= $args_others;
		if($idcaddie) $link .= "&idcaddie=".$idcaddie;
		return $link;
	}
	
	public static function display_cart_objects($idcaddie) {
		$myCart = static::get_object_instance($idcaddie);
		print pmb_bidi("<div class=\"row\"><b>Panier&nbsp;: ".$myCart->name.' ('.$myCart->type.')</b></div>');
		static::aff_cart_notices($myCart->get_cart(), $myCart->type, $idcaddie);
	}
	
	// affichage du contenu du caddie à partir de $liste qui contient les object_id
	protected static function aff_cart_notices($liste, $caddie_type="", $idcaddie=0) {
		global $msg;
		global $begin_result_liste, $end_result_liste;
		global $end_result_list;
		global $id_empr;
		global $groupID;
	
		if ((empty($liste) && !is_array($liste)) || !is_array($liste)) {
			print $msg[399];
			return;
		} else {
			// boucle de parcours des notices trouvées
			// inclusion du javascript de gestion des listes dépliables
			// début de liste
			print $begin_result_liste;
	
			$elements_records_list_ui = new elements_records_list_ui($liste, count($liste), false);
			elements_records_list_ui::set_link("./circ.php?categ=".static::$categ."&id_empr=$id_empr&groupID=$groupID&id_notice=!!id!!");
			elements_records_list_ui::set_link_expl("");
			elements_records_list_ui::set_link_explnum("");
			elements_records_list_ui::set_link_serial("./circ.php?categ=".static::$categ."&id_empr=$id_empr&groupID=$groupID&mode=view_serial&serial_id=!!id!!");
			elements_records_list_ui::set_link_analysis("");
			elements_records_list_ui::set_link_bulletin("./circ.php?categ=".static::$categ."&id_empr=$id_empr&groupID=$groupID&id_bulletin=!!id!!");
			elements_records_list_ui::set_link_notice_bulletin("./circ.php?categ=".static::$categ."&id_empr=$id_empr&groupID=$groupID&id_bulletin=!!id!!");
			$elements_records_list_ui->set_show_statut(0);
			$elements_records_list_ui->set_draggable(0);
			$elements_records_list_ui->set_ajax_mode(0);
			print $elements_records_list_ui->get_elements_list();
	
			print $end_result_liste;
		}
	}
} // fin de déclaration de la classe resa_caddie_controller
