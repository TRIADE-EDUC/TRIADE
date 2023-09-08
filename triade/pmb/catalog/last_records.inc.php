<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: last_records.inc.php,v 1.27 2018-06-29 09:03:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/elements_list/elements_records_list_ui.class.php');

// affichage de l'entête de page
print "<h1>${msg[938]}</h1>";

// affichage des notices
print "<div class=\"row\">";

// javascript gestion de liste
print $begin_result_liste;

if (!isset($last_records)) $last_records=$pmb_nb_lastnotices;
if (!isset($plus)) $plus = 0;
if ($plus) $last_records = $last_records + $plus; 

//gestion des acces en lecture
$acces_j='';
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_1= $ac->setDomain(1);
	$acces_j = $dom_1->getJoin($PMBuserid,4,'notice_id');
} 

if (!$pmb_latest_order) $pmb_latest_order="create_date desc, notice_id desc";
$requete = "SELECT * FROM notices ";
$requete.= $acces_j;
$requete.= "ORDER BY $pmb_latest_order LIMIT $last_records";

$result = pmb_mysql_query($requete, $dbh);
if (pmb_mysql_num_rows($result)) {
	$records = array();
	while(($notice = pmb_mysql_fetch_object($result))) {
		$records[] = $notice->notice_id;
	}
	$elements_records_list_ui = new elements_records_list_ui($records, count($records), false);
	print $elements_records_list_ui->get_elements_list();
	$plus = $plus + $pmb_nb_lastnotices;
	print "<a href='./catalog.php?categ=last_records&plus=$plus'>...</a>";
} else {
   	print $msg[939];
}

print $end_result_list;
print "</div>";
