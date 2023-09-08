<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: show_sur_location.inc.php,v 1.10 2019-05-29 10:07:20 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/map/map_location_home_page_controler.class.php");

if (!$opac_nb_localisations_per_line) $opac_nb_localisations_per_line=6;
print "<div id=\"location\">";
print "<h3><span>".$msg["l_browse_title"]."</span></h3>";
print "<div id='location-container'>";

if (!empty($surloc)) {
    $surloc = intval($surloc);
	if($opac_view_filter_class){
		// =surloc_num and idlocation
		$requete="select idlocation, location_libelle, location_pic, css_style from docs_location where location_visible_opac=1 and surloc_num='$surloc'
		and idlocation in(". implode(",",$opac_view_filter_class->params["nav_sections"]).")  order by location_libelle ";
	} else {
		$requete="select idlocation, location_libelle, location_pic, css_style from docs_location where location_visible_opac=1 and surloc_num='$surloc' order by location_libelle ";
	}
	$resultat=pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($resultat)>1) {
		print "<table class='center' style='width:100%'>";
		$npl=0;
		$ids = array();
		$tab_locations = array();
		while ($r=pmb_mysql_fetch_object($resultat)) {
			if($opac_map_activate==1 || $opac_map_activate==3) {
				$ids[] = $r->idlocation;
				$tab_locations[$r->idlocation]["id"] = $r->idlocation;
				$tab_locations[$r->idlocation]['libelle'] = $r->location_libelle;
				$tab_locations[$r->idlocation]['code_champ'] = 90;
				$tab_locations[$r->idlocation]['code_ss_champ'] = 4;
				$tab_locations[$r->idlocation]['url'] = "./index.php?lvl=section_see";
				$tab_locations[$r->idlocation]['param'] = "&location=" . $r->idlocation . ($r->css_style?"&opac_css=" . $r->css_style:"");
				$tab_locations[$r->idlocation]['flag_home_page'] = true;
			} else {
				if ($npl==0) {
					print "<tr>";
				}
				if ($r->location_pic) {
					$image_src = $r->location_pic;
				} else {
					$image_src = "images/bibli-small.png";
				}
				print "<td class='center'>
                            <a href='./index.php?lvl=section_see&location=".$r->idlocation.($r->css_style?"&opac_css=".$r->css_style:"")."'><img src='$image_src' style='border:0px' alt='".$r->location_libelle."' title='".$r->location_libelle."'/></a>
                            <br /><a href='./index.php?lvl=section_see&location=".$r->idlocation.($r->css_style?"&opac_css=".$r->css_style:"")."'><b>".$r->location_libelle."</b></a></td>";
				$npl++;
				if ($npl==$opac_nb_localisations_per_line) {
					print "</tr>";
					$npl=0;
				}
			}
		}
		if($opac_map_activate==1 || $opac_map_activate==3) {
			print '<tr><td>' . map_location_home_page_controler::get_map_location_home_page( $ids, $tab_locations, array(), array()) . '</td></tr>';
		}
		if ($npl!=0) {
			while ($npl<$opac_nb_localisations_per_line) {
				print "<td></td>";
				$npl++;
			}
			print "</tr>";
		}
		print "</table>";
	} else{
		if (pmb_mysql_num_rows($resultat)) {
			$location=pmb_mysql_result($resultat,0,0);
			$requete="select idsection, section_libelle, section_pic from docs_section, exemplaires where expl_location=$location and section_visible_opac=1 and expl_section=idsection group by idsection order by section_libelle ";
			$resultat=pmb_mysql_query($requete);
			print "<table class='center' style='width:100%'>";
			$npl=0;
			while ($r=pmb_mysql_fetch_object($resultat)) {
				if ($npl==0) print "<tr>";
				if ($r->section_pic) $image_src = $r->section_pic ;
				else  $image_src = get_url_icon("rayonnage-small.png");
				print "<td class='center'>
                                <a href='./index.php?lvl=section_see&location=".$location."&id=".$r->idsection."'><img src='$image_src' style='border:0px' alt='".$r->section_libelle."' title='".$r->section_libelle."'/></a>
                                <br /><a href='./index.php?lvl=section_see&location=".$location."&id=".$r->idsection."'><b>".$r->section_libelle."</b></a></td>";
				$npl++;
				if ($npl==$opac_nb_localisations_per_line) {
					print "</tr>";
					$npl=0;
				}
			}
			if ($npl!=0) {
				while ($npl<$opac_nb_localisations_per_line) {
					print "<td></td>";
					$npl++;
				}
				print "</tr>";
			}
			print "</table>";
		}
	}
}else { // On affiche les toutes les surloc

	if($opac_view_filter_class){
		$requete="select distinct surloc_id, surloc_libelle, surloc_pic, surloc_css_style from sur_location,docs_location  where surloc_visible_opac=1
             and surloc_id=surloc_num and idlocation in(". implode(",",$opac_view_filter_class->params["nav_sections"]). ") order by surloc_libelle";
	} else {
		$requete="select surloc_id, surloc_libelle, surloc_pic, surloc_css_style from sur_location where surloc_visible_opac=1 order by surloc_libelle ";
	}
	$resultat=pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($resultat)>1) {
		print "<table class='center' style='width:100%'>";
		$npl=0;
		$surlocations_ids = array();
		$tab_surlocations = array();
		while ($r=pmb_mysql_fetch_object($resultat)) {
			if($opac_map_activate==1 || $opac_map_activate==3) {
				$surlocations_ids[] = $r->surloc_id;
				$tab_surlocations[$r->surloc_id]["id"] = $r->surloc_id;
				$tab_surlocations[$r->surloc_id]['libelle'] = $r->surloc_libelle;
				$tab_surlocations[$r->surloc_id]['code_champ'] = 90;
				$tab_surlocations[$r->surloc_id]['code_ss_champ'] = 9;
				$tab_surlocations[$r->surloc_id]['url'] = "./index.php?lvl=section_see";
				$tab_surlocations[$r->surloc_id]['param'] = "&surloc=" . $r->surloc_id . ($r->surloc_css_style?"&opac_css=" . $r->surloc_css_style:"");
				$tab_surlocations[$r->surloc_id]['flag_home_page'] = true;
			} else {
				if ($npl==0) print "<tr>";
				if ($r->surloc_pic) {
					$image_src = $r->surloc_pic ;
				} else {
					$image_src = "images/bibli-small.png" ;
				}
				print "<td class='center'>
                                <a href='./index.php?lvl=section_see&surloc=".$r->surloc_id.($r->surloc_css_style?"&opac_css=".$r->surloc_css_style:"")."'><img src='$image_src' style='border:0px' alt='".$r->surloc_libelle."' title='".$r->surloc_libelle."'/></a>
                                <br /><a href='./index.php?lvl=section_see&surloc=".$r->surloc_id.($r->surloc_css_style?"&opac_css=".$r->surloc_css_style:"")."'><b>".$r->surloc_libelle."</b></a></td>";
				$npl++;
				if ($npl==$opac_nb_localisations_per_line) {
					print "</tr>";
					$npl=0;
				}
			}
		}
		if($opac_map_activate==1 || $opac_map_activate==3) {
			print '<tr><td>' . map_location_home_page_controler::get_map_location_home_page(array(), array(), $surlocations_ids, $tab_surlocations) . '</td></tr>';
		}
		if ($npl!=0) {
			while ($npl<$opac_nb_localisations_per_line) {
				print "<td></td>";
				$npl++;
			}
			print "</tr>";
		}
		print "</table>";
	}
}
print "</div>";
print "</div>";