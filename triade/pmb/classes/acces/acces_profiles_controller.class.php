<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: acces_profiles_controller.class.php,v 1.2 2022/12/26 13:19:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/acces/acces_controller.class.php");

class acces_profiles_controller extends acces_controller {
	
	protected static $list_ui_class_name = 'list_acces_profiles_ui';
	
	protected static $profile_type;
	
	public static function proceed($id=0) {
		global $msg;
		global $action;
		global $chk_prop;
		
		switch ($action) {
			case 'calc' :
				if (count($chk_prop)) {
					print static::get_display_calc_profiles_list($id);
				} else {
					error_form_message(addslashes($msg['dom_prop_chx_err']));
				}
				break;
			case 'list_save':
				$list_ui_instance = static::get_list_ui_instance();
				$list_ui_instance->save_objects();
				print static::get_display_profiles_list($id);
				break;
			case 'list':
			default:
				print static::get_display_profiles_list($id);
				break;
		}
	}
	
	public static function get_display_table_links($id=0) {
		global $charset;
		global $dom;
		
		$id = intval($id);
		
		//affichage lien roles utilisateurs
		$txt = htmlentities($dom->getComment('user_prf_lib'),ENT_QUOTES,$charset);
		$row = "<tr style=\"cursor: pointer;\" onmousedown=\"document.location='./admin.php?categ=acces&sub=user_prf&action=list&id=$id';\" ";
		$row.= "onmouseout=\"this.className='even'\" onmouseover=\"this.className='surbrillance'\" class=\"even\"><td><strong>$txt</strong></td></tr>";
		//affichage lien profils ressources
		$txt = htmlentities($dom->getComment('res_prf_lib'),ENT_QUOTES,$charset);
		$row.= "<tr style=\"cursor: pointer;\" onmousedown=\"document.location='./admin.php?categ=acces&sub=res_prf&action=list&id=$id';\" ";
		$row.= "onmouseout=\"this.className='odd'\" onmouseover=\"this.className='surbrillance'\" class=\"odd\"><td><strong>$txt</strong></td></tr>";
		return $row;
	}
	
	protected static function get_template_profiles_list() {
		return '';
	}
	
	public static function get_display_profiles_list($id,$maj=false) {
		global $charset;
		global $dom;
		global $maj_form;
		
		$form = static::get_template_profiles_list();
		$form = str_replace('!!form_title!!', htmlentities($dom->getComment(static::$profile_type.'_prf_lib'),ENT_QUOTES,$charset), $form);
		$form = str_replace ('<!-- rows -->', static::get_display_table_links($id), $form);
		
		$used_list_form="
		<div class='row'>
			<label class='etiquette'>".htmlentities($dom->getComment(static::$profile_type.'_prf_used_list_lib'),ENT_QUOTES,$charset)."</label>
		</div>";
		$list_ui_class_name = static::$list_ui_class_name;
		$list_ui_class_name::set_domain($id);
		$used_list_form .= $list_ui_class_name::get_instance(array('domain' => $id))->get_display_list();
		$form = str_replace('<!-- used_list_form -->',$used_list_form,$form);
		
		$bt_calc = "<input type='button' onclick=\"
		this.form.action='".static::get_url_base()."&action=calc&id=$id';
		this.form.submit();return false;\"
		value=\"".$dom->getComment(static::$profile_type.'_prf_bt_calc')."\" class='bouton' />";
		$form = str_replace('<!-- bt_calc -->', $bt_calc,$form);
		
		if ($maj) {
			$form = str_replace('<!-- maj -->',$maj_form,$form);
		}
		return $form;
	}
	
	//Affiche la liste des profils apres calcul
	public static function get_display_calc_profiles_list($id) {
		global $charset;
		global $dom;
		
		$form = static::get_template_profiles_list();
		$form = str_replace('!!form_title!!', htmlentities($dom->getComment(static::$profile_type.'_prf_lib'), ENT_QUOTES, $charset), $form);
		
		$form = str_replace ('<!-- rows -->', static::get_display_table_links($id), $form);
		
		$calc_list_form="
		<div class='row'>
			<label class='etiquette'>".htmlentities($dom->getComment(static::$profile_type.'_prf_calc_list_lib'),ENT_QUOTES,$charset)."</label>
		</div>";
		
		switch (static::$list_ui_class_name) {
			case 'list_acces_profiles_resources_ui':
				list_acces_profiles_calculated_resources_ui::set_domain($id);
				$calc_list_form .= list_acces_profiles_calculated_resources_ui::get_instance()->get_display_list();
				break;
			case 'list_acces_profiles_users_ui':
				list_acces_profiles_calculated_users_ui::set_domain($id);
				$calc_list_form .= list_acces_profiles_calculated_users_ui::get_instance()->get_display_list();
				break;
		}
		
		$form = str_replace('<!-- calc_list_form -->', $calc_list_form,$form);
		
		$bt_calc = "<input type='button' onclick=\"
		this.form.action='".static::get_url_base()."&action=calc&id=$id';
		this.form.submit();return false;\"
		value=\"".$dom->getComment(static::$profile_type.'_prf_bt_calc')."\" class='bouton' />";
		$form = str_replace('<!-- bt_calc -->', $bt_calc,$form);
		
		return $form;
	}
}
