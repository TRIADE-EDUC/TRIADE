<?PHP
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_codepostal.class.php,v 1.3 2017-10-10 09:47:00 dgoron Exp $
  
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path."/selectors/classes/selector.class.php");
require($base_path."/selectors/templates/sel_codepostal.tpl.php");

class selector_codepostal extends selector {
	
	public function __construct($user_input=''){
		parent::__construct($user_input);
	}
	
	public function get_sel_search_form_template() {
		global $base_url;
		global $msg, $charset;
	
		$sel_search_form ="
			<script type='text/javascript'>
			<!--
			function test_form(form){
				if(form.f_user_input.value.length == 0){
					return false;
				}
				return true;
			}
			-->
			</script>
			<form name='".$this->get_sel_search_form_name()."' method='post' action='".$base_url."'>
				<input type='text' name='f_user_input' value=\"".htmlentities($this->user_input,ENT_QUOTES,$charset)."\">
				&nbsp;
				<input type='submit' class='bouton_small' value='".$msg[142]."' onclick='return test_form(this.form)'/>
			</form>
			<script type='text/javascript'>
				<!--
				document.forms['".$this->get_sel_search_form_name()."'].elements['f_user_input'].focus();
				-->
			</script>
			<hr />
		";
		return $sel_search_form;
	}
}
?>