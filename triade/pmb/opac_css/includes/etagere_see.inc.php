<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: etagere_see.inc.php,v 1.79.2.2 2024/09/17 09:15:06 pmallambic Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $base_path, $include_path, $msg, $begin_result_liste, $page, $add_cart_link, $alert_see_mc_values;
global $opac_nb_aut_rec_per_page, $opac_search_allow_refinement, $opac_notices_depliable;
global $opac_allow_bannette_priv, $allow_dsi_priv;
global $gestion_acces_active, $gestion_acces_empr_notice;
global $id, $nb_per_page_custom;
global $charset, $opac_rgaa_active;

require_once($class_path."/etagere.class.php");
require_once($class_path.'/etagere_caddies.class.php');
require_once($class_path."/suggest.class.php");
require_once($class_path."/sort.class.php");

// affichage du contenu d'une étagère

print "<div id='aut_details'>\n";

if ($id) {
	//enregistrement de l'endroit actuel dans la session
	rec_last_authorities();
	//Récupération des infos de l'étagère
	$id = intval($id);
	$etagere = new etagere($id);

	print pmb_bidi(($etagere->thumbnail_url?"<img src='".$etagere->thumbnail_url."' class='thumbnail_etagere' alt='".$etagere->get_translated_name()."'>":""));
	print common::format_title($etagere->get_translated_name());
	print "<div id='aut_details_container'>\n";
	if ($etagere->get_translated_comment()){
		print "<div id='aut_see'>\n";
		print pmb_bidi("<strong>".$etagere->get_translated_comment()."</strong><br /><br />");
		print "	</div><!-- fermeture #aut_see -->\n";
	}

	print "<div id='aut_details_liste'>\n";

	$etagere_caddies = new etagere_caddies($id);

	$nbr_lignes=$etagere_caddies->get_notices_count();

	//Recherche des types doc
	$t_typdoc = $etagere_caddies->get_typdocs();
	$l_typdoc=implode(",",$t_typdoc);

	// Ouverture du div resultatrech_liste
	print "<div id='resultatrech_liste'>";
	if($opac_rgaa_active){
	    // ouverture div pour contenir toutes les fonctionnalités
	    print "<div id='resultatrech_tools' class='result_tools'>";
	}

	/**
	 * 19/10/2021 : Désactivation de la création/modification d'alerte à partir de l'étagère
	 * 11/02/2022 : Réactivation de la création/modification d'alerte à partir de l'étagère
	 */
	// pour la DSI - création d'une alerte
	
	if ($nbr_lignes && $opac_allow_bannette_priv && $allow_dsi_priv && ((isset($_SESSION['abon_cree_bannette_priv']) && $_SESSION['abon_cree_bannette_priv']==1) || $opac_allow_bannette_priv==2)) {
	    if ($opac_rgaa_active) {
	        print "<a href='".$base_path."./empr.php?lvl=bannette_creer' class='bouton btn_dsi btn_dsi_add' onClick=\"document.mc_values.action='./empr.php?lvl=bannette_creer'; document.mc_values.submit();\">$msg[dsi_bt_bannette_priv]</a>";
	    }else{
	        print "<input role='link' type='button' class='bouton btn_dsi_add' name='dsi_priv' value='".htmlspecialchars($msg['dsi_bt_bannette_priv'], ENT_QUOTES, $charset)."' onClick=\"document.mc_values.action='./empr.php?lvl=bannette_creer'; document.mc_values.submit();\">";
	    }
	    print "<span class=\"espaceResultSearch\">&nbsp;</span>";
	}

	// pour la DSI - Modification d'une alerte
	if(!empty($_SESSION['abon_edit_bannette_priv']) && !empty($_SESSION['abon_edit_bannette_priv_visibility_until']) && $_SESSION['abon_edit_bannette_priv_visibility_until'] < time()) {
		unset($_SESSION['abon_edit_bannette_priv']);
	}
	if ($nbr_lignes && $opac_allow_bannette_priv && $allow_dsi_priv && (isset($_SESSION['abon_edit_bannette_priv']) && $_SESSION['abon_edit_bannette_priv']==1)) {
	    if ($opac_rgaa_active) {
	        print "<a href='".$base_path."./empr.php?lvl=bannette_edit&id_bannette=".$_SESSION['abon_edit_bannette_id']."' class='bouton btn_dsi btn_dsi_edit' onClick=\"document.mc_values.action='./empr.php?lvl=bannette_edit&id_bannette=".$_SESSION['abon_edit_bannette_id']."'; document.mc_values.submit();\">$msg[dsi_bannette_edit]</a>";
	    }else{
	        print "<input role='link' type='button' class='bouton btn_dsi btn_dsi_edit' name='dsi_priv' value='".htmlspecialchars($msg['dsi_bannette_edit'], ENT_QUOTES, $charset)."' onClick=\"document.mc_values.action='./empr.php?lvl=bannette_edit&id_bannette=".$_SESSION['abon_edit_bannette_id']."'; document.mc_values.submit();\">";
	    }
	    print "<span class=\"espaceResultSearch\">&nbsp;</span>";
	}

	if(!$page) $page=1;
	$debut =($page-1)*$opac_nb_aut_rec_per_page;

	if($nbr_lignes) {
		$notices = $etagere_caddies->get_notices($debut, $opac_nb_aut_rec_per_page);

		if ($opac_notices_depliable) print $begin_result_liste;

		print "<span class=\"printEtagere\">
				<a href='#' onClick=\"openPopUp('".$base_path."/print.php?lvl=etagere&id_etagere=".$id."','print'); w.focus(); return false;\" title=\"".$msg["etagere_print"]."\">
					<img src='".get_url_icon('print.gif')."' class='align_bottom' alt=\"".$msg["etagere_print"]."\"/>
				</a>
			</span>";

		//gestion du tri
		//est géré dans index_includes.inc.php car il faut le gérer avant l'affichage du sélecteur de tri
		print $etagere_caddies->show_tris();

		print $add_cart_link;

		// Gestion des alertes à partir de la recherche simple
 		include_once($include_path."/alert_see.inc.php");
 		print $alert_see_mc_values;

		//affichage
 		if($opac_search_allow_refinement){
			print "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"affiner_recherche\"><a href='$base_path/index.php?search_type_asked=extended_search&mode_aff=aff_module' title='".$msg["affiner_recherche"]."'>".$msg["affiner_recherche"]."</a></span>";
 		}
		//fin affinage
 		if($opac_rgaa_active){
 		    // fermeture div fonctionnalités
 		    print "</div>";
 		}
		print "<blockquote role='presentation'>\n";
		print aff_notice(-1);

		$nb=0;
		$recherche_ajax_mode=0;
		foreach ($notices as $notice_id) {
			if($nb>4)$recherche_ajax_mode=1;
			$nb++;
			print pmb_bidi(aff_notice($notice_id, 0, 1, 0, "", "", 0, 1, $recherche_ajax_mode));
		}
		print aff_notice(-2);
		print "	</blockquote>\n";
		print "</div><!-- fermeture #resultatrech_liste -->\n";
		print "</div><!-- fermeture #aut_details_liste -->\n";
		print "<div id='navbar'><hr /><div style='text-align:center'>".printnavbar($page, $nbr_lignes, $opac_nb_aut_rec_per_page, "./index.php?lvl=etagere_see&id=".$id."&page=!!page!!&nbr_lignes=".$nbr_lignes.($nb_per_page_custom ? "&nb_per_page_custom=".$nb_per_page_custom : ''))."</div></div>\n";
	} else {
    	    if($opac_rgaa_active){
    	        // fermeture div fonctionnalités
    	        print "</div>";
    	    }
    	    print "<p id='no_result_paragraph'>". $msg['no_document_found'] ."</p>";
    	    print "</div><!-- fermeture #resultatrech_liste -->\n";
			print "</div><!-- fermeture #aut_details_liste -->\n";
	}
	print "</div><!-- fermeture #aut_details_container -->\n";
}

print "</div><!-- fermeture #aut_details -->\n";

//FACETTES
$facettes_tpl = '';
//comparateur de facettes : on ré-initialise
$_SESSION['facette']=array();
if($nbr_lignes){
	require_once($base_path.'/classes/facette_search.class.php');
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
		$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
	}
	if($_SESSION["opac_view"] && $_SESSION["opac_view_query"] ){
		$opac_view_restrict=" notice_id in (select opac_view_num_notice from  opac_view_notices_".$_SESSION["opac_view"].") ";
		$statut_r.=" and ".$opac_view_restrict;
	}
	$query = "select distinct notice_id from caddie_content, etagere_caddie, notices $acces_j $statut_j ";
	$query .= "where etagere_id=$id and caddie_content.caddie_id=etagere_caddie.caddie_id and notice_id=object_id $statut_r ";
	$facettes_tpl .= facettes::get_display_list_from_query($query);
}
?>