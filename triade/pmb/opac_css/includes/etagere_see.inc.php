<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: etagere_see.inc.php,v 1.67 2019-01-16 17:02:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/etagere.class.php");
require_once($class_path."/suggest.class.php");
require_once($class_path."/sort.class.php");

// affichage du contenu d'une étagère

print "<div id='aut_details'>\n";

if ($id) {
	//enregistrement de l'endroit actuel dans la session
	rec_last_authorities();
	//Récupération des infos de l'étagère
	$id+=0;
	$etagere = new etagere($id);
	
	print pmb_bidi(($etagere->thumbnail_url?"<img src='".$etagere->thumbnail_url."' border='0' class='thumbnail_etagere' alt='".$etagere->get_translated_name()."'>":"")."<h3><span>".$etagere->get_translated_name()."</span></h3>\n");
	print "<div id='aut_details_container'>\n";
	if ($etagere->get_translated_comment()){
			print "<div id='aut_see'>\n";
			print pmb_bidi("<strong>".$etagere->get_translated_comment()."</strong><br /><br />");
			print "	</div><!-- fermeture #aut_see -->\n";			
		}

	print "<div id='aut_details_liste'>\n";

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
	//$requete = "select count(distinct object_id) from caddie_content, etagere_caddie, notices, notice_statut where etagere_id=$id and caddie_content.caddie_id=etagere_caddie.caddie_id and notice_id=object_id ";
	//$requete.= " and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
	$requete = "select count(distinct object_id) from caddie_content, etagere_caddie, notices $acces_j $statut_j ";
	$requete.= "where etagere_id=$id and caddie_content.caddie_id=etagere_caddie.caddie_id and notice_id=object_id $statut_r ";
	$resultat=pmb_mysql_query($requete);
	$nbr_lignes=pmb_mysql_result($resultat,0,0);
	
	//Recherche des types doc
	//$requete="select distinct notices.typdoc FROM caddie_content, etagere_caddie, notices, notice_statut where etagere_id=$id and caddie_content.caddie_id=etagere_caddie.caddie_id and notice_id=object_id ";
	//$requete .= " and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
	$requete = "select distinct typdoc FROM caddie_content, etagere_caddie, notices $acces_j $statut_j ";
	$requete.= "where etagere_id=$id and caddie_content.caddie_id=etagere_caddie.caddie_id and notice_id=object_id $statut_r ";
	$res = pmb_mysql_query($requete, $dbh);

	
	$t_typdoc=array();
	if ($res) {
		while ($tpd=pmb_mysql_fetch_object($res)) {
			$t_typdoc[]=$tpd->typdoc;
		}
	}
	$l_typdoc=implode(",",$t_typdoc);

	// pour la DSI - création d'une alerte
	if ($nbr_lignes && $opac_allow_bannette_priv && $allow_dsi_priv && ((isset($_SESSION['abon_cree_bannette_priv']) && $_SESSION['abon_cree_bannette_priv']==1) || $opac_allow_bannette_priv==2)) {
		print "<input type='button' class='bouton' name='dsi_priv' value=\"$msg[dsi_bt_bannette_priv]\" onClick=\"document.mc_values.action='./empr.php?lvl=bannette_creer'; document.mc_values.submit();\"><span class=\"espaceResultSearch\">&nbsp;</span>";
	}
	
	// pour la DSI - Modification d'une alerte
	if ($nbr_lignes && $opac_allow_bannette_priv && $allow_dsi_priv && (isset($_SESSION['abon_edit_bannette_priv']) && $_SESSION['abon_edit_bannette_priv']==1)) {
		print "<input type='button' class='bouton' name='dsi_priv' value=\"".$msg['dsi_bannette_edit']."\" onClick=\"document.mc_values.action='./empr.php?lvl=bannette_edit&id_bannette=".$_SESSION['abon_edit_bannette_id']."'; document.mc_values.submit();\"><span class=\"espaceResultSearch\">&nbsp;</span>";
	}

	// Ouverture du div resultatrech_liste
	print "<div id='resultatrech_liste'>";
	
	if(!$page) $page=1;
	$debut =($page-1)*$opac_nb_aut_rec_per_page;
		
	if($nbr_lignes) {
		// on lance la vraie requête
		$requete = "select distinct notice_id from caddie_content, etagere_caddie, notices $acces_j $statut_j ";
		$requete.= "where etagere_id=$id and caddie_content.caddie_id=etagere_caddie.caddie_id and notice_id=object_id $statut_r ";
		//gestion du tri
		$requete = sort::get_sort_etagere_query($requete, $nbr_lignes, $debut);
		
		if ($opac_notices_depliable) print $begin_result_liste;

		print "<span class=\"printEtagere\">
				<a href='#' onClick=\"openPopUp('".$base_path."/print.php?lvl=etagere&id_etagere=".$id."','print'); w.focus(); return false;\" title=\"".$msg["etagere_print"]."\">
					<img src='".get_url_icon('print.gif')."' border='0' class='align_bottom' alt=\"".$msg["etagere_print"]."\"/>
				</a>
			</span>";
		
		//gestion du tri
		//est géré dans index_includes.inc.php car il faut le gérer avant l'affichage du sélecteur de tri
		print sort::show_tris_in_result_list($nbr_lignes);
		
		print $add_cart_link;
		
		//affinage
		//enregistrement de l'endroit actuel dans la session
		if(empty($_SESSION["last_module_search"])) {
		    $_SESSION["last_module_search"] = array();
		}
		$_SESSION["last_module_search"]["search_mod"]="etagere_see";
		$_SESSION["last_module_search"]["search_id"]=$id;
		$_SESSION["last_module_search"]["search_page"]=$page;
		
		// Gestion des alertes à partir de la recherche simple
 		include_once($include_path."/alert_see.inc.php");
 		print $alert_see_mc_values;
			
		//affichage
 		if($opac_search_allow_refinement){
			print "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"affiner_recherche\"><a href='$base_path/index.php?search_type_asked=extended_search&mode_aff=aff_module' title='".$msg["affiner_recherche"]."'>".$msg["affiner_recherche"]."</a></span>";
 		}	
		//fin affinage
		
		print "<blockquote>\n";
		print aff_notice(-1);
		
		$res = pmb_mysql_query($requete, $dbh);
		$nb=0;
		$recherche_ajax_mode=0;
		while(($obj=pmb_mysql_fetch_object($res))) {
			if($nb>4)$recherche_ajax_mode=1;
			$nb++;
			print pmb_bidi(aff_notice($obj->notice_id, 0, 1, 0, "", "", 0, 1, $recherche_ajax_mode));
		}
		print aff_notice(-2);
		pmb_mysql_free_result($res);
		print "	</blockquote>\n";
		print "</div><!-- fermeture #resultatrech_liste -->\n";
		print "</div><!-- fermeture #aut_details_liste -->\n";
		print "<div id='navbar'><hr /><div style='text-align:center'>".printnavbar($page, $nbr_lignes, $opac_nb_aut_rec_per_page, "./index.php?lvl=etagere_see&id=".$id."&page=!!page!!&nbr_lignes=".$nbr_lignes.($nb_per_page_custom ? "&nb_per_page_custom=".$nb_per_page_custom : ''))."</div></div>\n";
	} else {
			print $msg['no_document_found'];
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
	$query = "select distinct notice_id from caddie_content, etagere_caddie, notices $acces_j $statut_j ";
	$query .= "where etagere_id=$id and caddie_content.caddie_id=etagere_caddie.caddie_id and notice_id=object_id $statut_r ";
	$facettes_tpl .= facettes::get_display_list_from_query($query);
}
?>