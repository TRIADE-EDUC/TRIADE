<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bul_del_notice.inc.php,v 1.2 2016-05-06 12:44:27 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//verification des droits de modification notice
$acces_m=1;
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_1= $ac->setDomain(1);
	if (!$bul_id) {
		$acces_m = $dom_1->getRights($PMBuserid,$serial_id,8);
	} else {
		$acces_j = $dom_1->getJoin($PMBuserid,8,'bulletin_notice');
		$q= "select count(1) from bulletins $acces_j where bulletin_id=".$bul_id;
		$r = pmb_mysql_query($q, $dbh);
		if ($r) {
			if(pmb_mysql_result($r,0,0)==0) {
				$acces_m=0;
			}
		} else {
			$acces_m=0;
		}
	}
}

if ($acces_m==0) {

	if (!$bul_id) {
		error_message('', htmlentities($dom_1->getComment('mod_seri_error'), ENT_QUOTES, $charset), 1, '');
	} else {
		error_message('', htmlentities($dom_1->getComment('mod_bull_error'), ENT_QUOTES, $charset), 1, '');
	}

} else {

	$myBulletinage = new bulletinage($bul_id, $serial_id);
	$notice_id = $myBulletinage->bull_num_notice;
	
	pmb_mysql_query("UPDATE bulletins SET num_notice=0 WHERE notice_id=".$notice_id);
	notice::del_notice($notice_id);
	
	print "<div class='row'><div class='msg-perio'>".$msg["maj_encours"]."</div></div>";
	$retour = "./catalog.php?categ=serials&sub=view&sub=bulletinage&action=view&bul_id=$bul_id";
	print "
		<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour\" style=\"display:none\">
			<input type=\"hidden\" name=\"id_form\" value=\"$id_form\">
		</form>
		<script type=\"text/javascript\">document.dummy.submit();</script>
		";

}
?>