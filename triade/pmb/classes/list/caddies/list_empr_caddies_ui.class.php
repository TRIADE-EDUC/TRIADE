<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_empr_caddies_ui.class.php,v 1.9.4.1 2025/02/20 15:31:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once($class_path."/empr_caddie.class.php");

class list_empr_caddies_ui extends list_caddies_root_ui {
	
	protected static $model_class_name = 'empr_caddie';
	
	protected static $field_name = 'idemprcaddie';
	
	protected function init_default_applied_group() {
		$this->applied_group = array(0 => 'classement_label');
	}
	
	protected function get_cell_content_link_name($object) {
		global $action, $sub;
		global $list_ui_objects_type;
		
		$content = '';
		$link = $this->lien_origine."&action=".$this->action_click.($list_ui_objects_type ? "&list_ui_objects_type=".$list_ui_objects_type : "")."&idemprcaddie=".$object->get_idcaddie()."&item=".$this->item;
		
		if($this->item && $action!="save_cart" && $action!="del_cart") {
			if($action != "transfert" && $action != "del_cart" && $action!="save_cart") {
				$content .= "<input type='checkbox' id='id_".$object->get_idcaddie()."' name='caddie[".$object->get_idcaddie()."]' value='".$object->get_idcaddie()."'>&nbsp;";
				$content .= "<a href='#' onClick='javascript:document.getElementById(\"id_".$object->get_idcaddie()."\").checked=true; document.forms[\"print_options\"].submit();'>";
			} else {
				$content .= "<a href='$link'>";
			}
			
		} else {
			if($sub!='gestion' && $sub!='action'  && $action!="save_cart") {
				$content.= "<input type='checkbox' id='id_".$object->get_idcaddie()."' name='caddie[".$object->get_idcaddie()."]' value='".$object->get_idcaddie()."'>&nbsp;";
				$content .= "<a href='#' onClick='javascript:document.getElementById(\"id_".$object->get_idcaddie()."\").checked=true; document.forms[\"print_options\"].submit();'>";
			} else {
				$content.= "<a href='$link'>";
			}
		}
		return $content;
	}
	
	protected function get_cell_content_edition_and_actions($object) {
		global $msg;
		global $sub, $quoi, $action;
		global $list_ui_objects_type;
		
		if (static::$lien_edition) {
			$aff_lien = "<input type=button class=bouton value='$msg[caddie_editer]' onclick=\"document.location='".$this->lien_origine."&action=edit_cart".($list_ui_objects_type ? "&list_ui_objects_type=".$list_ui_objects_type : "")."&idemprcaddie=".$object->get_idcaddie()."';\" />";
		} else {
			$aff_lien = "";
		}
		
		if(($sub=="gestion" && $quoi=="panier") || ($action=="del_cart")){
			$model_class_name = static::$model_class_name;
			return $aff_lien."&nbsp;".$model_class_name::show_actions($object->get_idcaddie()).($object->acces_rapide ? " <img src='".get_url_icon('chrono.png')."' title='".$msg['caddie_fast_access']."'>":"");
		} else {
			return $aff_lien;
		}
	}
	
	protected function get_cell_content($object, $property) {
	    global $base_path, $msg, $charset;
	    
	    $content = '';
	    switch($property) {
	        case 'pointed_unpointed':
	            $content .= "<b>".$object->nb_item_pointe."</b>". $msg['caddie_contient_pointes']." / <b>".$object->nb_item."</b>";
	            $number_not_sended = $object->has_flag_not_sended();
	            if ($number_not_sended) {
	                $infobulle = $msg['caddie_has_used_by_scheduler']." ";
	                if($number_not_sended == 1) {
	                    $infobulle .= $msg['caddie_has_one_flag_not_sended'];
	                } else {
	                    $infobulle .= str_replace('!!number!!', $number_not_sended, $msg['caddie_has_several_flag_not_sended']);
	                }
	                $link = $base_path."/circ.php?categ=caddie&sub=action&quelle=edition&action=choix_quoi&object_type=&item=0&idemprcaddie=".$object->get_id()."&mode=advanced&show_list=1&elt_flag_not_sended=1";
	                $content .= " 
                        <a href='".$link."' target='_blank'>
                            <img src='".get_url_icon('mail_not_sended.png')."' title='".htmlentities($infobulle, ENT_QUOTES, $charset)."' alt='".htmlentities($infobulle, ENT_QUOTES, $charset)."' />
                        </a>";
				}
	            break;
	        default :
	            $content .= parent::get_cell_content($object, $property);
	            break;
	    }
	    return $content;
	}
	
	protected function get_button_add() {
		global $msg;
		global $list_ui_objects_type;
		
		return $this->get_interface_button($msg['new_cart'], ['location' => $this->lien_origine."&action=new_cart".($list_ui_objects_type ? "&list_ui_objects_type=".$list_ui_objects_type : "")."&item=".$this->item]);
	}
	
	public static function get_controller_url_base() {
		global $base_path, $sub, $quelle;
		
		return $base_path.'/circ.php?categ=caddie&sub='.$sub.'&quelle='.$quelle;
	}
}