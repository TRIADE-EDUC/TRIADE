<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reindex_cms.inc.php,v 1.9 2019-03-19 07:56:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($base_path.'/classes/cms/cms_article.class.php');
require_once($base_path.'/classes/cms/cms_section.class.php');

// la taille d'un paquet de notices
$lot = REINDEX_PAQUET_SIZE; // defini dans ./params.inc.php

// taille de la jauge pour affichage
$jauge_size = GAUGE_SIZE;
$jauge_size .= "px";

// initialisation de la borne de départ
if (empty($start) && empty($start_2)) {
	$start=$start_2=0;
	//remise a zero de la table au début
	pmb_mysql_query("TRUNCATE cms_editorial_words_global_index",$dbh);
	pmb_mysql_query("ALTER TABLE cms_editorial_words_global_index DISABLE KEYS",$dbh);
	
	pmb_mysql_query("TRUNCATE cms_editorial_fields_global_index",$dbh);
	pmb_mysql_query("ALTER TABLE cms_editorial_fields_global_index DISABLE KEYS",$dbh);
}

$v_state=urldecode($v_state);

if (!$count) {
	$notices = pmb_mysql_query("SELECT count(1) FROM cms_articles", $dbh);
	$count = pmb_mysql_result($notices, 0, 0);
	$notices = pmb_mysql_query("SELECT count(1) FROM cms_sections", $dbh);
	$count+= pmb_mysql_result($notices, 0, 0);
}
	
print "<br /><br /><h2 class='center'>".htmlentities($msg["nettoyage_reindex_cms"], ENT_QUOTES, $charset)."</h2>";

$NoIndex = 1;

$query = pmb_mysql_query("select id_article from cms_articles order by id_article LIMIT $start, $lot");
if(pmb_mysql_num_rows($query)) {
		
	// définition de l'état de la jauge
	$state = floor(($start+$start_2) / ($count / $jauge_size));
	if(($start+$start_2)>$count){
		$state = floor(($count/2)/($count/$jauge_size));
	}
	$state .= "px";
	// mise à jour de l'affichage de la jauge
	print "<table border='0' class='' width='$jauge_size' cellpadding='0'><tr><td class='jauge' width='100%'>";
	print "<div class='jauge'><img src='".get_url_icon('jauge.png')."' width='$state' height='16px'></div></td></tr></table>";
		
	// calcul pourcentage avancement
	$percent = floor((($start+$start_2)/$count)*100);
	if($percent>100) $percent = 50;
	// affichage du % d'avancement et de l'état
	print "<div class='center'>$percent%</div>";
	while($row = pmb_mysql_fetch_assoc($query)) {		
		// permet de charger la bonne langue, mot vide...
		$article = new cms_article($row['id_article']);
		$info=$article->maj_indexation();
		}
	pmb_mysql_free_result($query);
	
	$next = $start + $lot;
	print "
	<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
	<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
	<input type='hidden' name='spec' value=\"$spec\">
	<input type='hidden' name='start' value=\"$next\">
	<input type='hidden' name='start_2' value=\"$start_2\">
	<input type='hidden' name='count' value=\"$count\">
	</form>
	<script type=\"text/javascript\"><!-- 
	setTimeout(\"document.forms['current_state'].submit()\",1000); 
	-->
	</script>";
} else {
	$query = pmb_mysql_query("select id_section from cms_sections order by id_section LIMIT $start_2, $lot");
	if(pmb_mysql_num_rows($query)) {
	
		// définition de l'état de la jauge
		$state = floor(($start+$start_2) / ($count / $jauge_size));
		if(($start+$start_2)>$count){
			$state = floor(($count/2)/($count/$jauge_size));
		}
		$state .= "px";
		// mise à jour de l'affichage de la jauge
		print "<table border='0' class='' width='$jauge_size' cellpadding='0'><tr><td class='jauge' width='100%'>";
		print "<div class='jauge'><img src='".get_url_icon('jauge.png')."' width='$state' height='16px'></div></td></tr></table>";
	
		// calcul pourcentage avancement
		$percent = floor((($start+$start_2)/$count)*100);
	
		if($percent>100) $percent = 50;
		// affichage du % d'avancement et de l'état
		print "<div class='center'>$percent%</div>";
	
		while($row = pmb_mysql_fetch_assoc($query)) {
			// permet de charger la bonne langue, mot vide...
			$section = new cms_section($row['id_section']);
			$info=$section->maj_indexation();
		}
		pmb_mysql_free_result($query);
	
		$next = $start_2 + $lot;
		print "
		<form class='form-$current_module' name='current_state' action='./clean.php' method='post'>
		<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
		<input type='hidden' name='spec' value=\"$spec\">
		<input type='hidden' name='start' value=\"$start\">
		<input type='hidden' name='start_2' value=\"$next\">
		<input type='hidden' name='count' value=\"$count\">
		</form>
		<script type=\"text/javascript\"><!--
		setTimeout(\"document.forms['current_state'].submit()\",1000);
		-->
		</script>";
	}else {
	
		$spec = $spec - INDEX_CMS;
		$not = pmb_mysql_query("SELECT 1 FROM cms_editorial_words_global_index group by num_obj,type", $dbh);
		$compte = pmb_mysql_num_rows($not);
		$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>".htmlentities($msg["nettoyage_reindex_cms"], ENT_QUOTES, $charset)." :";
		$v_state .= $compte." ".htmlentities($msg["nettoyage_res_reindex_cms"], ENT_QUOTES, $charset);
		print netbase::get_process_state_form($v_state, $spec);
		pmb_mysql_query("ALTER TABLE cms_editorial_words_global_index ENABLE KEYS",$dbh);
		pmb_mysql_query("ALTER TABLE cms_editorial_fields_global_index ENABLE KEYS",$dbh);
	}
}