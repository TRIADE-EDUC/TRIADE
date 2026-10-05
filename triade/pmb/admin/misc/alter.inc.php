<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alter.inc.php,v 1.16.6.2 2025/01/15 15:46:23 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg, $base_path, $pmb_version_brut, $pmb_verif_on_line, $pmb_version_web, $pmb_version_patch;

print "
<div class='row'>".
	$msg['alter_version_pmb']." ".$pmb_version_brut.
	"<br />".
	$msg['alter_version_patch']." ".$pmb_version_patch;

if ($pmb_verif_on_line) {
	if($pmb_version_web) {
		$fp=@fopen($pmb_version_web, "rb");
		if ($fp) {
			$buffer = fgets($fp, 4096);
			fclose($fp) ;
			if ($buffer!=$pmb_version_brut) {
				$mess_version_web = str_replace("!!version_web!!", $buffer, $msg['alter_version_pmb_dispo']) ;
				print " <br /><label class='etiquette'>$mess_version_web</label>";
			}
		}
	}
}

$rqt = "show tables like 'empr_passwords'";
if (pmb_mysql_num_rows(pmb_mysql_query($rqt))) {
	print "<br /><br /><b><a href='".$base_path."/admin.php?categ=netbase'>".$msg['need_to_clean_empr_passwords']."</a></b><br /><br />";
}

print "
	</div>
<div class='row'>
	<iframe name='alter' frameborder='0' scrolling='yes' width='800' height='600' src='./admin/misc/alter.php'>
	</div>
<noframes></noframes>" ;
