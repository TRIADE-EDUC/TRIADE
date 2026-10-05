<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: statut.inc.php,v 1.34 2021/01/21 08:52:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $id;

require_once($class_path."/docs_statut.class.php");
require_once($class_path."/configuration/configuration_controller.class.php");

// gestion des codes statut exemplaires
?>
<script type="text/javascript">
function test_check(form){
	if(form.form_pret.value < 1)
		form.form_pret.value = '1';
	else
		form.form_pret.value = '0';
	return true;
}
function test_check_trans(form){
	if(form.form_trans.value < 1)
		form.form_trans.value = '1';
	else
		form.form_trans.value = '0';
	return true;
}
function test_check_visible_opac(form){
	if(form.form_visible_opac.value < 1)
		form.form_visible_opac.value = '1';
	else
		form.form_visible_opac.value = '0';
	return true;
}

</script>

<?php
configuration_controller::set_model_class_name('docs_statut');
configuration_controller::set_list_ui_class_name('list_configuration_docs_statut_ui');
configuration_controller::proceed($id);