<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: renewal.inc.php,v 1.6 2022/10/04 14:16:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg, $pmb_relance_adhesion;
global $id_empr, $empr_active_opac_renewal, $empr_date_expiration, $opac_empr_renewal_delay;

$pmb_relance_adhesion = intval($pmb_relance_adhesion);
$opac_empr_renewal_delay = intval($opac_empr_renewal_delay);
if (!$empr_active_opac_renewal) {
	echo '<script>window.location = "./empr.php";</script>';
	die;
}
//Affichage de la prolongation si on est dans l'intervalle de relance
$datetime_empr_renewal_delay = new DateTime($empr_date_expiration);
//Autorise-t-on un delai après expiration
if($opac_empr_renewal_delay) {
	$datetime_empr_renewal_delay->modify('+'.$opac_empr_renewal_delay.' days');
}
$datetime_empr_renewal_delay_format = $datetime_empr_renewal_delay->format('Y-m-d');

$datetime_today = new DateTime(date('Y-m-d'));
$datetime_today_format = $datetime_today->format('Y-m-d');
$datetime_today->modify('+'.$pmb_relance_adhesion.' days');
$datetime_relance_delay_format = $datetime_today->format('Y-m-d');
if (!(strtotime($datetime_empr_renewal_delay_format) >= strtotime($datetime_today_format) && strtotime($empr_date_expiration) <= strtotime($datetime_relance_delay_format))) {
	echo '<script>window.location = "./empr.php";</script>';
	die;
}

$empr_temp = new emprunteur($id_empr, '', FALSE, 0);

$rqt="select duree_adhesion from empr_categ where id_categ_empr='$empr_temp->categ'";
$res_dur_adhesion = pmb_mysql_query($rqt);
$row = pmb_mysql_fetch_row($res_dur_adhesion);
$nb_jour_adhesion_categ = $row[0];

$query = 'update empr set empr_date_expiration = date_add("'.$empr_temp->date_expiration.'",INTERVAL '.$nb_jour_adhesion_categ.' DAY) WHERE id_empr = '.$id_empr;

pmb_mysql_query($query);

echo '<p>'.sprintf($msg['empr_renewal_success'], date_format(date_add(date_create($empr_temp->date_expiration), date_interval_create_from_date_string($nb_jour_adhesion_categ.' days')), $msg['date_format'])).'</p>';

echo '<script>setTimeout(function() {
	window.location = "./empr.php";
}, 5000);</script>';