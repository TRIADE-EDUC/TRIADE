<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: includes_rss.inc.php,v 1.28 2018-08-24 08:44:59 plmrozowski Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!isset($lang)) $lang='fr_FR' ;

require_once($base_path."/includes/init.inc.php");
include_once($base_path."/includes/error_report.inc.php") ;
include_once($base_path."/includes/global_vars.inc.php");

// récupération paramètres MySQL et connection á la base
require_once($base_path.'/includes/opac_config.inc.php');
require_once($base_path."/includes/opac_db_param.inc.php");
require_once($base_path."/includes/opac_mysql_connect.inc.php");
if(!isset($dbh) || !$dbh){
    $dbh = connection_mysql();
}
require_once($base_path."/includes/start.inc.php");
require_once($base_path."/includes/marc_tables/".$pmb_indexation_lang."/empty_words");

// récupération localisation
require_once($base_path.'/includes/localisation.inc.php');

require_once($base_path."/classes/rss_flux.class.php");
require_once($base_path."/classes/notice_affichage.class.php");
require_once($base_path."/includes/notice_categories.inc.php");
require_once($base_path."/includes/misc.inc.php");
require_once($base_path."/includes/explnum.inc.php");
require_once($base_path."/classes/collection.class.php");
require_once($base_path."/classes/subcollection.class.php");
require_once($base_path."/classes/indexint.class.php");

// classe d'affichage des tags
require_once($base_path.'/classes/tags.class.php');

//pour la gestion des tris
require_once($base_path."/classes/sort.class.php");

$liens_opac['lien_rech_notice'] 		= $opac_url_base."index.php?lvl=notice_display&id=!!id!!";
$liens_opac['lien_rech_bulletin'] 		= $opac_url_base."index.php?lvl=bulletin_display&id=!!id!!";

function genere_link_rss() {
	global $dbh, $opac_url_base, $charset, $logo_rss_si_rss, $msg ;
	global $opac_view_filter_class;
	
	$liens = '';
	$rqt = "select id_rss_flux, nom_rss_flux, descr_rss_flux from rss_flux order by 2 ";
	$res = pmb_mysql_query($rqt,$dbh);
	while ($obj=pmb_mysql_fetch_object($res)) {
		if($opac_view_filter_class){
			if(!$opac_view_filter_class->is_selected("flux_rss", $obj->id_rss_flux))  continue; 
		}
		$liens .= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"".htmlentities($obj->nom_rss_flux,ENT_QUOTES, $charset)."\" href=\"".$opac_url_base."rss.php?id=".$obj->id_rss_flux."\" />" ;
		}
	if ($liens) $logo_rss_si_rss = "<a href='index.php?lvl=rss_see&id=' title=\"".$msg['show_rss_dispo']."\"><img id=\"rss_logo\" alt='rss' src='".get_url_icon('rss.png', 1)."' style='vertical-align:middle;border:0px' /></a>" ;
	return $liens ;
}

function genere_page_rss($id=0) {
	global $dbh, $opac_url_base, $charset, $msg ;
	global $opac_view_filter_class;
	
	if ($id) $clause = " where id_rss_flux='$id' ";  
	$rqt = "select id_rss_flux, nom_rss_flux, img_url_rss_flux from rss_flux $clause order by 2 ";
	$res = pmb_mysql_query($rqt,$dbh);
	while ($obj=pmb_mysql_fetch_object($res)) {
		if($opac_view_filter_class){
			if(!$opac_view_filter_class->is_selected("flux_rss", $obj->id_rss_flux))  continue; 
		}		
		$liens .= "
		<tr>
			<td style='width:10%'>";
		if ($obj->img_url_rss_flux) $liens .= "<a href=\"index.php?lvl=rss_see&id=".$obj->id_rss_flux."\"><img src='".$obj->img_url_rss_flux."' border=none /></a>";
		$liens .= "</td><td style='width:50%'><a href=\"index.php?lvl=rss_see&id=".$obj->id_rss_flux."\">".htmlentities($obj->nom_rss_flux,ENT_QUOTES, $charset)."</a>
			</td><td><a href=\"".$opac_url_base."rss.php?id=".$obj->id_rss_flux."\" title=\"".$msg['abonne_rss_dispo']."\"><img id=\"rss_logo\" alt='rss' src='".get_url_icon('rss.png', 1)."' border=0 /></a>
			".htmlentities($opac_url_base."rss.php?id=".$obj->id_rss_flux,ENT_QUOTES, $charset)."
				</td></tr>" ;
		}
	if ($liens) $liens = "<table class='rss_list_table'> $liens </table>" ;
	return $liens ;
}

