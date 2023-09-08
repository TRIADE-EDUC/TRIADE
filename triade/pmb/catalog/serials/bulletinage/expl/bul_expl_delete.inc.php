<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bul_expl_delete.inc.php,v 1.24 2017-08-04 07:24:04 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/index_concept.class.php");
require_once($class_path.'/audit.class.php');

// suppression d'un exemplaire de bulletinage
echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg[313], $serial_header);


//verification des droits de modification notice
$acces_m=1;
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_1= $ac->setDomain(1);
	$acces_j = $dom_1->getJoin($PMBuserid,8,'bulletin_notice');
	$q = "select count(1) from bulletins $acces_j where bulletin_id=".$bul_id;
	$r = pmb_mysql_query($q, $dbh);
	if(pmb_mysql_result($r,0,0)==0) {
		$acces_m=0;
	}
}

if ($acces_m==0) {

		error_message('', htmlentities($dom_1->getComment('mod_expl_error'), ENT_QUOTES, $charset), 1, '');

} else {
	
	print "<div class=\"row\"><div class=\"msg-perio\">".$msg['catalog_notices_suppression']."</div></div>";
	
	$sql_circ = pmb_mysql_query("select 1 from serialcirc_expl where num_serialcirc_expl_id ='$expl_id' ") ;
	if (pmb_mysql_num_rows($sql_circ)) {	
		error_message($msg[416], $msg["serialcirc_expl_no_del"], 1, "./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=$bul_id");
	}else{
	
		$requete = "select 1 from pret where pret_idexpl='$expl_id' ";
		$result=@pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($result)) {
			// gestion erreur prêt en cours
			error_message($msg[416], $msg['impossible_expl_del_pret'], 1, "./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=$bul_id");
		} else {
			exemplaire::del_expl($expl_id);
		
			$retour = "./catalog.php?categ=serials&sub=view&sub=bulletinage&action=view&bul_id=$bul_id";
			print "<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour\" style=\"display:none\">
				<input type=\"hidden\" name=\"id_form\" value=\"$id_form\">
				</form>
				<script type=\"text/javascript\">document.dummy.submit();</script>";
		}
	}	

}
?>
		

