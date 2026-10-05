<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: zbib.inc.php,v 1.16 2021/03/23 08:43:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg;
global $id;

require_once($class_path.'/z_bib.class.php');
require_once($class_path."/configuration/configuration_controller.class.php");

// gestion des attributs de recherche z3950
?>

<script type="text/javascript">
function test_form(form)
{
	if( (form.form_nom.value.length == 0) || (form.form_base.value.length == 0) || (form.form_url.value.length == 0) || (form.form_format.value.length == 0) || (form.form_search_type.value.length == 0) || (form.form_port.value.length == 0) ) {
		alert("<?php echo $msg['zbib_renseign_valeurs'] ?>");
		return false;
		}
	if(isNaN(form.form_port.value))
	{
		alert("<?php echo $msg['zbib_error_port_no_num'] ?>");
		return false;
	}

	return true;
}
</script>

<?php
configuration_controller::set_model_class_name('z_bib');
configuration_controller::set_list_ui_class_name('list_configuration_z3950_zbib_ui');
configuration_controller::proceed($id);