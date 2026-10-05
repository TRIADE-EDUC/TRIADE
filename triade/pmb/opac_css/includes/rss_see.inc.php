<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rss_see.inc.php,v 1.9 2023/08/04 12:26:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $msg, $logo_rss_si_rss, $opac_url_base, $id;

// affichage des infos d'un RSS
require_once($base_path."/classes/rss_flux.class.php");

print "<div id='aut_details'>\n";
$id = intval($id);
if ($id) {
	//Récupération des infos du RSS
    $rss_flux = new rss_flux($id);
    print $logo_rss_si_rss."&nbsp;<span style='display:inline-block;'>".common::format_title($rss_flux->nom_rss_flux)."</span>";
	print "<div id='main_rss_item'><div id='aut_details_container'>\n";
	print "<div id='aut_see'>";
	print genere_page_rss($id);
	print $rss_flux->descr_rss_flux ;
	print "	</div><!-- fermeture #aut_see -->\n
			<div id='aut_details_liste'>\n";
	print affiche_rss_from_url($opac_url_base."/rss.php?id=$id") ;
	print "\n
				</div><!-- fermeture #aut_details_liste -->\n";
	print "</div><!-- fermeture #aut_details_container --></div><!-- fermeture #main_rss_item -->\n";
} else {
    print $logo_rss_si_rss."&nbsp;<span style='display:inline-block;'>".common::format_title($msg['show_rss_dispo'])."</span>";
	print genere_page_rss();
}
print "</div><!-- fermeture #aut_details -->\n";	
