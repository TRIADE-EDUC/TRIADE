<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_caddies_ui.class.php,v 1.6 2023/04/04 06:53:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/caddie.class.php");

class list_caddies_ui extends list_caddies_root_ui {
		
	protected static $model_class_name = 'caddie';
	
	protected static $field_name = 'idcaddie';
	
	protected function get_cell_content_link_name($object) {
		global $action;
		
		$content = '';
		if($this->item && $action!="save_cart" && $action!="del_cart") {
			$content .= (!$this->nocheck?"<input type='checkbox' id='id_".$object->get_idcaddie()."' name='caddie[".$object->get_idcaddie()."]' value='".$object->get_idcaddie()."'>":"")."&nbsp;";
			if(!$this->nocheck){
				$content.=  "<a href='#' onclick='javascript:document.getElementById(\"id_".$object->get_idcaddie()."\").checked=true;document.forms[\"print_options\"].submit();'>";
			} else {
				if ($this->lien_pointage) {
					$content.=  "<a href='#' onclick='javascript:document.getElementById(\"idcaddie\").value=".$this->item.";document.getElementById(\"idcaddie_selected\").value=".$object->get_idcaddie().";document.forms[\"print_options\"].submit();'>";
				} else {
					$content.=  "<a href='#' onclick='javascript:document.getElementById(\"idcaddie\").value=".$object->get_idcaddie().";document.forms[\"print_options\"].submit();'>";
				}
			}
		} else {
			//spécifique paniers de notices ????
			$link = $this->lien_origine."&action=".$this->action_click."&object_type=".$object->type."&idcaddie=".$object->get_idcaddie()."&item=".$this->item;
			
			$content.= "<a href='$link'>";
		}
		return $content;
	}
	
	public static function get_controller_url_base() {
		global $base_path, $sub, $moyen;
		
		return $base_path.'/catalog.php?categ=caddie&sub='.$sub.'&moyen='.$moyen;
	}
}