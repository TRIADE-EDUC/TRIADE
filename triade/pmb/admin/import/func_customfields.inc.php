<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_customfields.inc.php,v 1.6 2018/01/09 08:54:31 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path; //Nécessaire pour certaines inclusions
include_once $base_path.'/admin/import/lib_func_customfields.inc.php';

function recup_noticeunimarc_suite($notice) {
	func_customfields_recup_noticeunimarc_suite($notice);
} // fin recup_noticeunimarc_suite 
	
function import_new_notice_suite() {
	func_customfields_import_new_notice_suite();
} 