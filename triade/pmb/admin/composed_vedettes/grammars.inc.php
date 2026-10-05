<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: grammars.inc.php,v 1.1 2019/03/26 14:05:19 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/vedette/vedette_grammars.class.php");

$vedette_grammars = new vedette_grammars();
switch ($action) {
	case 'save_grammars_by_entity' :
		print '<div class="row"><div class="msg-perio">'.$msg['sauv_misc_running'].'</div></div>';
		$vedette_grammars->set_grammars_by_entity_from_form();
		$vedette_grammars->save_grammars_by_entity();
		print "<script type='text/javascript'>window.location.href='./admin.php?categ=composed_vedettes&sub=grammars&action=grammars_by_entity_form'</script>";
		break;
	case 'grammars_by_entity_form' :
	default :
		print $vedette_grammars->get_grammars_by_entity_form();
		break;
}