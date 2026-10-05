<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: periodicite.inc.php,v 1.14 2021/02/19 13:53:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $id;

require_once($class_path.'/abts_periodicite.class.php');
require_once($class_path."/configuration/configuration_controller.class.php");

?>
<script type="text/javascript">
function test_form(form) {
	if(form.libelle.value.length == 0) {
		alert("<?php echo $msg[98] ?>");
		return false;
	}
	if((isNaN(form.duree.value)) || (form.duree.value == 0)) {
		alert("<?php echo $msg['abonnements_duree_erreur_saisie'] ?>");
		return false;
	}
	return true;
}
</script>

<?php
configuration_controller::set_model_class_name('abts_periodicite');
configuration_controller::set_list_ui_class_name('list_configuration_abonnements_periodicite_ui');
configuration_controller::proceed($id);
