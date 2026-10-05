<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_records_caddie_list_ui.class.php,v 1.4 2023/07/26 15:07:59 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/elements_list/elements_records_list_ui.class.php');

/**
 * Classe d'affichage d'un onglet qui affiche une liste de notices d'un panier
 * @author dgoron
 *
 */
class elements_records_caddie_list_ui extends elements_records_list_ui {
	
	protected static $url_base;
	protected static $idcaddie;
	protected static $no_del;
	protected static $no_point;
	
	protected function generate_elements_list(){
		global $class_path;
		global $msg;
		global $nb_per_page_search;
		global $page;
		
		// nombre de références par pages
		if ($nb_per_page_search != "") $nb_per_page = $nb_per_page_search ;
		else $nb_per_page = 10;
		
		//Calcul des variables pour la suppression d'items
		$nbr_lignes = count($this->contents);
		$modulo = $nbr_lignes%$nb_per_page;
		if($modulo == 1){
			$page_suppr = (!$page ? 1 : $page-1);
		} else {
			$page_suppr = $page;
		}
		$nb_after_suppr = ($nbr_lignes ? $nbr_lignes-1 : 0);
		
		$elements_list = '';
		$recherche_ajax_mode = 0;
		$nb = 0;
		foreach($this->contents as $content){
			if(!$recherche_ajax_mode && ($nb++>5)) $recherche_ajax_mode=1;
			if ($content['content']=="") {
				if (!static::$no_point) {
					if ($content['flag']) $marque_flag ="<img src='".get_url_icon('depointer.png')."' id='caddie_".static::$idcaddie."_item_".$content['object_id']."' title=\"".$msg['caddie_item_depointer']."\" onClick='del_pointage_item(".static::$idcaddie.",".$content['object_id'].");' style='cursor: pointer'/>" ;
					else $marque_flag ="<img src='".get_url_icon('pointer.png')."' id='caddie_".static::$idcaddie."_item_".$content['object_id']."' title=\"".$msg['caddie_item_pointer']."\" onClick='add_pointage_item(".static::$idcaddie.",".$content['object_id'].");' style='cursor: pointer'/>" ;
				} else {
					if ($content['flag']) $marque_flag ="<img src='".get_url_icon('tick.gif')."'/>" ;
					else $marque_flag ="" ;
				}
				if (!static::$no_del) $lien_suppr_cart = "<a href='".static::$url_base."&action=del_item&object_type=NOTI&item=".$content['object_id']."&page=$page_suppr&nbr_lignes=$nb_after_suppr&nb_per_page=$nb_per_page'><img src='".get_url_icon('basket_empty_20x20.gif')."' alt='basket' title=\"".$msg['caddie_icone_suppr_elt']."\" /></a> $marque_flag";
				else $lien_suppr_cart = $marque_flag ;
				static::set_link_delete_cart($lien_suppr_cart);
				$elements_list.= $this->generate_element($content['object_id'], $recherche_ajax_mode);
			} else {
				if ($content['flag']) $marque_flag ="<img src='".get_url_icon('tick.gif')."'/>" ;
				else $marque_flag ="" ;
				if (!$no_del) $lien_suppr_cart = "<a href='".static::$url_base."&action=del_item&object_type=EXPL_CB&item=".$content['content']."&page=$page_suppr&nbr_lignes=$nb_after_suppr&nb_per_page=$nb_per_page'><img src='".get_url_icon('basket_empty_20x20.gif')."' alt='basket' title=\"".$msg['caddie_icone_suppr_elt']."\" /></a> $marque_flag";
				else $lien_suppr_cart = $marque_flag ;
				
				$elements_list.= "
				<div id=\"el!!id!!Parent\" class=\"notice-parent\">
					<span class=\"notice-heada\"><strong>$lien_suppr_cart ".$msg["4014"]." : ".$content['content']."&nbsp;: {$msg[395]}</strong></span>
					<br />
				</div>";
			}
			
		}
		return $elements_list;
	}
	
	public static function set_url_base($url_base) {
		static::$url_base = $url_base;
	}
	
	public static function set_idcaddie($idcaddie) {
		static::$idcaddie = $idcaddie;
	}
	
	public static function set_no_del($no_del) {
		static::$no_del = $no_del;
	}
	
	public static function set_no_point($no_point) {
		static::$no_point = $no_point;
	}
	
}