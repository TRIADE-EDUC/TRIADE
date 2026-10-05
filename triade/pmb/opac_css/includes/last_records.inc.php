<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: last_records.inc.php,v 1.28 2023/08/02 14:04:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $msg, $opac_show_dernieresnotices_nb, $last_records, $plus;
global $gestion_acces_active, $gestion_acces_empr_notice, $opac_notices_depliable;
global $begin_result_liste;

require_once($base_path.'/classes/notice_display.class.php');

if (!$last_records) $last_records=$opac_show_dernieresnotices_nb;
$plus = intval($plus);
if ($plus) $last_records = $last_records + $plus;

if(isset($_SESSION["opac_view"]) && $_SESSION["opac_view"] && $_SESSION["opac_view_query"] ){
	$opac_view_restrict=" notice_id in (select opac_view_num_notice from  opac_view_notices_".$_SESSION["opac_view"].") ";
} else {
	$opac_view_restrict="";
}

//droits d'acces emprunteur/notice
$acces_j='';
if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_2= $ac->setDomain(2);
	$acces_j = $dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
}
	
if($acces_j) {
	$statut_j='';
	$statut_r='';
} else {
	$statut_j=',notice_statut';
	$statut_r="where statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
}
if($opac_view_restrict)  $statut_r.=" and ".$opac_view_restrict;
		
$requete = "select notice_id from notices $acces_j $statut_j $statut_r ";
$requete.= "order by create_date desc, notice_id desc limit $last_records";
$result = pmb_mysql_query($requete);

if(pmb_mysql_num_rows($result)) {
    print "<div id='last_entries'>";
    print common::format_title($msg['last_entries']);
    print "<div id='last_entries-container'>".$msg['last_records_intro']."<br />";
//	print "<blockquote>\n";
	if ($opac_notices_depliable) print $begin_result_liste;
	while($notice = pmb_mysql_fetch_object($result)) {
    	print pmb_bidi(aff_notice($notice->notice_id, 0, 1, 0, '', '', 0, 1, 1));
	}
//	print "</blockquote>\n";
	$plus = $plus + 10;
	print "</div>
	</div>";
}