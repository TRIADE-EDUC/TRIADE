<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_acces_profiles_calculated_resources_ui.class.php,v 1.1 2022/12/21 08:25:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_acces_profiles_calculated_resources_ui extends list_acces_profiles_calculated_ui {
	
	protected $profile_type = 'res';
	
	protected function get_array_calc() {
		global $chk_prop;
		
		return $this->get_dom()->calcResourceProfiles($chk_prop);
	}
	
	public function get_display_objects_list() {
		global $charset;
		
		$display = parent::get_display_objects_list();
		list_acces_profiles_unused_resources_ui::set_domain(static::$domain);
		list_acces_profiles_unused_resources_ui::set_t_reused($this->t_reused);
		if(count(list_acces_profiles_unused_resources_ui::get_instance()->get_objects())) {
			$display .= "
			<div class='row'>
				<label class='etiquette'>".htmlentities($this->get_dom()->getComment('res_prf_unused_list_lib'),ENT_QUOTES,$charset)."</label>
			</div>";
			$display .= list_acces_profiles_unused_resources_ui::get_instance()->get_display_objects_list();
		}
		return $display;
	}
}