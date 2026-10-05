<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fiches_controller.class.php,v 1.3 2020/11/05 12:26:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/search.class.php");
require_once($class_path."/parametres_perso.class.php");

class fiches_controller extends lists_controller {

	protected static $list_ui_class_name = 'list_fiches_ui';
	
	public static function proceed($id=0) {
		global $include_path;
		global $mode, $sub;
		
		$sc = new search(false,"search_fields",$include_path."/fichier/");
		$sc->isfichier=true;
		
		switch($mode){
			case 'search':
				$fiche = new fiche($id);
				$sc->limited_search = true;
				switch($sub){
					case 'view':
						print $fiche->show_fiche_form();
						break;
					case 'edit':
						print $fiche->show_edit_form();
						break;
					case 'update':
						$fiche->save();
						print $fiche->show_fiche_form();
						break;
					case 'del':
						$fiche->delete();
						
						$list_ui_instance = static::get_list_ui_instance();
						print $list_ui_instance->get_display_list();
						break;
					default:
						parent::proceed($id);
						break;
				}
				break;
				
			case 'search_multi':
				switch($sub){
					case 'launch':
						$sc->show_results_fichier("./fichier.php?categ=consult&mode=search_multi&sub=launch","./fichier.php?categ=consult&mode=search_multi", true, '', true );
						break;
					default:
						print $sc->show_form("./fichier.php?categ=consult&mode=search_multi","./fichier.php?categ=consult&mode=search_multi&sub=launch");
						break;
				}
				break;
		}
	}
}