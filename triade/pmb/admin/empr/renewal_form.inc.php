<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: renewal_form.inc.php,v 1.2 2019-03-08 08:42:14 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/empr_renewal.class.php");

$empr_renewal = new empr_renewal();
switch ($action) {
	case "save" :
		print '<div class="row"><div class="msg-perio">'.$msg['sauv_misc_running'].'</div></div>';
		$empr_renewal->get_from_form();
		$empr_renewal->save();
		print "<script type='text/javascript'>window.location.href='./admin.php?categ=empr&sub=renewal_form&action=get_form'</script>";
		break;
	case "get_form":
	default :
		print $empr_renewal->get_form();
		break;
}