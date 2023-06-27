<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: renewal.inc.php,v 1.2 2019-03-15 15:21:45 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
global $pmb_relance_adhesion;
if (!$empr_active_opac_renewal || !(strtotime($empr_date_expiration) <= (time() + ($pmb_relance_adhesion* 86400)))) {
	echo '<script>window.location = "./empr.php";</script>'; 
	die;
}

$empr_temp = new emprunteur($id_empr, '', FALSE, 0);

$rqt="select duree_adhesion from empr_categ where id_categ_empr='$empr_temp->categ'";
$res_dur_adhesion = pmb_mysql_query($rqt, $dbh);
$row = pmb_mysql_fetch_row($res_dur_adhesion);
$nb_jour_adhesion_categ = $row[0];

$query = 'update empr set empr_date_expiration = date_add("'.$empr_temp->date_expiration.'",INTERVAL '.$nb_jour_adhesion_categ.' DAY) WHERE id_empr = '.$id_empr;

pmb_mysql_query($query);

echo '<p>'.sprintf($msg['empr_renewal_success'], date_format(date_add(date_create($empr_temp->date_expiration), date_interval_create_from_date_string($nb_jour_adhesion_categ.' days')), $msg['date_format'])).'</p>';

echo '<script>setTimeout(function() {
	window.location = "./empr.php";
}, 5000);</script>';