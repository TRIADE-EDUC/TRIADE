<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: gen_phonetique.inc.php,v 1.7 2024/04/17 13:55:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $charset;
global $start, $v_state, $spec, $count;

require_once($class_path."/double_metaphone.class.php");
require_once($class_path."/stemming.class.php");

// la taille d'un paquet de notices
$lot = REINDEX_PAQUET_SIZE*10; // defini dans ./params.inc.php
// initialisation de la borne de départ
if(!isset($start)) $start=0;
$v_state=urldecode($v_state);

if(!$count) {
	$notices = pmb_mysql_query("SELECT count(1) FROM words");
	$count = pmb_mysql_result($notices, 0, 0);
}

print netbase::get_display_progress_title($msg["gen_phonetique_en_cours"]);

$query = pmb_mysql_query("select id_word,word from words LIMIT $start, $lot");
if(pmb_mysql_num_rows($query)) {
	print netbase::get_display_progress($start, $count);
   	while($row = pmb_mysql_fetch_object($query)){
		$dmeta = new DoubleMetaPhone($row->word);
		$stemming = new stemming($row->word);
		$element_to_update = "";
		if($dmeta->primary || $dmeta->secondary){
			$element_to_update.="
			double_metaphone = '".$dmeta->primary." ".$dmeta->secondary."'";
		}
		if($element_to_update) $element_to_update.=",";
		$element_to_update.="stem = '".$stemming->stem."'";
		
		if ($element_to_update){
			pmb_mysql_query("update words set ".$element_to_update." where id_word = '".$row->id_word."'");
		}
	}
   	pmb_mysql_free_result($query);
	$next = $start + $lot;
 	print netbase::get_current_state_form($v_state, $spec, '', $next, $count);
} else {
	$spec = $spec - GEN_PHONETIQUE;
	$v_state .= "<br /><img src='".get_url_icon('d.gif')."' hspace=3>";
	$v_state .= $count." ".htmlentities($msg["gen_phonetique_end"], ENT_QUOTES, $charset);
	pmb_mysql_query('OPTIMIZE TABLE words');
	// mise à jour de l'affichage de la jauge
	print netbase::get_display_final_progress();

	print netbase::get_process_state_form($v_state, $spec);
}	