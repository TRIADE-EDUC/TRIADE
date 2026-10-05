<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// | creator : Eric ROBERT                                                    |
// | modified : ...                                                           |
// +-------------------------------------------------+
// $Id: func_lvm.inc.php,v 1.4 2023/10/11 10:09:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function z_recup_noticeunimarc_suite($notice) {
	global $base_path;
	require_once($base_path."/admin/import/func_lvm.inc.php");
	recup_noticeunimarc_suite($notice);
} 
	
function z_import_new_notice_suite() {
	global $base_path;
	require_once($base_path."/admin/import/func_lvm.inc.php");
	import_new_notice_suite();
} 
