<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: enter_localisation.inc.php,v 1.31 2024/01/12 16:00:45 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg;
global $opac_view_filter_class, $opac_map_activate;

require_once($class_path."/show_localisation.class.php");
require_once($class_path."/map/map_location_home_page_controler.class.php");

print "<div id=\"location\">";
print common::format_title($msg["l_browse_title"]);
print "<div id='location-container'>";

$requete="";
if($opac_view_filter_class){
	if(!empty($opac_view_filter_class->params["nav_sections"])) {
		$requete="select idlocation, location_libelle, location_pic, css_style from docs_location where location_visible_opac=1 
		  and idlocation in(". implode(",",$opac_view_filter_class->params["nav_sections"]).")  order by location_libelle ";
	}
} else {
	$requete="select idlocation, location_libelle, location_pic, css_style from docs_location where location_visible_opac=1 order by location_libelle ";
}
if ($requete) {
	$resultat=pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($resultat)>1) {
	    if($opac_map_activate==1 || $opac_map_activate==3) {
	        print "<table class='center' style='width:100%' role='presentation'>";
	        $ids=array();
	        $tab_locations = array();
	        while ($r=pmb_mysql_fetch_object($resultat)) {
	            $ids[] = $r->idlocation;
	            $tab_locations[$r->idlocation]=array();
	            $tab_locations[$r->idlocation]["id"] = $r->idlocation;
	            $tab_locations[$r->idlocation]['libelle'] = translation::get_translated_text($r->idlocation, "docs_location", "location_libelle", $r->location_libelle);
	            $tab_locations[$r->idlocation]['code_champ'] = 90;
	            $tab_locations[$r->idlocation]['code_ss_champ'] = 4;
	            $tab_locations[$r->idlocation]['url'] = "./index.php?lvl=section_see";
	            $tab_locations[$r->idlocation]['param'] = "&location=" . $r->idlocation . ($r->css_style?"&opac_css=" . $r->css_style:"");
	            $tab_locations[$r->idlocation]['flag_home_page'] = true;
	        }
	        print '<tr><td>' . map_location_home_page_controler::get_map_location_home_page( $ids, $tab_locations, array(), array()) . '</td></tr>';
	        print "</table>";
	    } else {
	        print list_opac_locations_ui::get_instance()->get_display_list();
	    }
	} else {
		if (pmb_mysql_num_rows($resultat)) {
			$location=pmb_mysql_result($resultat,0,0);
			show_localisation::set_num_location($location);
			print show_localisation::get_display_sections_list();
		}
	}
}
print "</div>";
print "</div>";