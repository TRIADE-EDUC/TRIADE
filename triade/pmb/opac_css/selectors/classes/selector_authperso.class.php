<?PHP
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_authperso.class.php,v 1.6 2018-07-26 15:25:52 tsamson Exp $
  
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path."/selectors/classes/selector_authorities.class.php");
require($base_path."/selectors/templates/sel_authperso.tpl.php");

class selector_authperso extends selector_authorities {
	
	public function __construct($user_input=''){
		parent::__construct($user_input);
		$this->objects_type = 'authperso';
	}

	protected function get_form() {
		global $authperso_id;
		
		$authperso = new authperso($authperso_id);
		return $authperso->get_form_select(0,static::get_base_url());
	}
	
	protected function get_advanced_form() {
		global $form_display_mode;
		global $authperso_id;
		
		$entities_controller = $this->get_entities_controller_instance();
		$entities_controller->set_id_authperso($authperso_id);
		$entities_controller->set_url_base(static::get_base_url()."&action=update&form_display_mode=".$form_display_mode);
		$entities_controller->proceed_form();
	}
	
	protected function get_add_label() {
		global $msg;
	
		return $msg['authperso_sel_add'];
	}
	
	protected function save() {
		global $authperso_id;
		
		$authperso = new authperso($authperso_id);
		$id=$authperso->update_from_form();
		if($authperso->get_cp_error_message()){
			print '<span class="erreur">'.$authperso->get_cp_error_message().'</span>';
		}
		return $id;
	}
	
	protected function get_authority_instance($authority_id=0, $object_id=0) {
		//return new authority($authority_id, $object_id, AUT_TABLE_AUTHPERSO);
		return authorities_collection::get_authority('authority', $authority_id, ['num_object' => $object_id, 'type_object' => AUT_TABLE_AUTHPERSO]);
	}
	
	protected function get_display_list() {
		global $authperso_id;
		global $id;
		global $base_url;
		global $type_autorite;
		
		$authperso_id += 0;
		$id += 0;
		$type_autorite += 0;
		$base_url = static::get_base_url()."&rech_regexp=$rech_regexp&user_input=".rawurlencode($this->user_input)."&type_autorite=".$type_autorite;
		
		$authperso=new authperso($authperso_id);
		$display_list = $authperso->get_list_selector($id);
		return $display_list;
	}
	
	public function get_title() {
		global $msg;
		return $msg["authperso_sel_title"];
	}
	
	public static function get_params_url() {
		global $p3, $p4, $p5, $p6, $authperso_id, $perso_id;
	
		$params_url = parent::get_params_url();
		$params_url .= ($p3 ? "&p3=".$p3 : "").($p4 ? "&p4=".$p4 : "").($p5 ? "&p5=".$p5 : "").($p6 ? "&p6=".$p6 : "").($authperso_id ? "&authperso_id=".$authperso_id : "").($perso_id ? "&perso_id=".$perso_id : "");
		return $params_url;
	}
}
?>