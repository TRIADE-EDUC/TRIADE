<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: schemes.inc.php,v 1.1 2019/03/26 14:05:19 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/vedette/vedette_schemes.class.php");

$vedette_schemes = new vedette_schemes();
switch ($action) {
	case 'save_schemes_by_entity' :
		print '<div class="row"><div class="msg-perio">'.$msg['sauv_misc_running'].'</div></div>';
		$vedette_schemes->set_scheme_by_entity_from_form();
		$vedette_schemes->save_scheme_by_entity();
		print "<script type='text/javascript'>window.location.href='./admin.php?categ=composed_vedettes&sub=schemes&action=grammars_by_entity_form'</script>";
		break;
	case 'schemes_by_entity_form' :
	default :
		print $vedette_schemes->get_scheme_by_entity_form();
		break;
}