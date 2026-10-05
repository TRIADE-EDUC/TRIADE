<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: constitution_controller.class.php,v 1.5.10.1 2025/04/10 06:14:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once($class_path."/etagere.class.php");

class constitution_controller extends lists_controller {
	
	protected static $model_class_name = 'etagere_caddies';
	protected static $list_ui_class_name = 'list_etageres_ui';
	
	public static function proceed($id=0) {
		global $action, $msg;
		global $etagere_constitution_content_form;
		global $idcaddie, $filters;
		
		switch ($action) {
			case 'edit_etagere':
				if (etagere::check_rights($id)) {
					$model_instance = static::get_model_instance($id);
					$interface_form = new interface_catalog_form('etagere_constitution_form');
					$interface_form->set_label($msg['etagere_constitution_de']." ".$model_instance->etagere->name);
					
					$content_form = $etagere_constitution_content_form;
					$content_form = str_replace('!!idetagere!!', $id, $content_form);
					$content_form = str_replace('!!constitution!!', $model_instance->constitution(1), $content_form);
					$interface_form->set_content_form($content_form)
					->set_table_name('etagere_caddie');
					print $interface_form->get_display();
				}
				break;
			case 'save_etagere':
				if (etagere::check_rights($id)) {
					$model_instance = static::get_model_instance($id);
					// suppression
					$rqt = "delete from etagere_caddie where etagere_id='$id' ";
					pmb_mysql_query($rqt);
					if (is_countable($idcaddie)) {
    					$nb_caddies = count($idcaddie);
    					for ($i = 0; $i < $nb_caddies; $i++) {
    						if (caddie::check_rights($idcaddie[$i])) {
    							if(!empty($filters[$idcaddie[$i]]) && is_array($filters[$idcaddie[$i]])) {
    								$caddie_filters = $filters[$idcaddie[$i]];
    							} else {
    								$caddie_filters = array();
    							}
    							$model_instance->add_panier($idcaddie[$i], $caddie_filters);
    						}
    					}
					}
				}
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			default:
				parent::proceed($id);
				break;
		}
	}
}
