<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette_func.inc.php,v 1.68.2.3.2.1 2025/01/30 09:08:06 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path;
global $gestion_acces_active, $gestion_acces_empr_notice;

require_once($base_path.'/includes/templates/notice_display.tpl.php');
require_once($base_path.'/includes/explnum.inc.php');
require_once($class_path.'/bannette.class.php');
require_once($class_path.'/record_display.class.php');
if($gestion_acces_active && $gestion_acces_empr_notice) {
	require_once($class_path.'/acces.class.php');
}

// tableau des notices d'un caddie
function notices_bannette($id_bannette, &$notices,$date_diff='') {
	global $opac_bannette_notices_order ;
	global $gestion_acces_active, $gestion_acces_empr_notice;

	if (!$opac_bannette_notices_order) {
		$opac_bannette_notices_order =" index_serie, tnvol, index_sew ";
	}

	$acces_j='';
	if($gestion_acces_active && $gestion_acces_empr_notice) {
		$ac = new acces();
		$dom_2 = $ac->setDomain(2);
		$acces_j = $dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
	}

	if(!$date_diff){
		// on constitue un tableau avec les notices de la bannette
		$query_notice = "select distinct notice_id, niveau_biblio from bannette_contenu, notices $acces_j where num_bannette='".$id_bannette."' and num_notice=notice_id order by $opac_bannette_notices_order ";
		$result_notice = pmb_mysql_query($query_notice);
		if (pmb_mysql_num_rows($result_notice)) {
			while (($notice=pmb_mysql_fetch_object($result_notice))) {
				$notices[$notice->notice_id]= $notice->niveau_biblio ;
			}
		}
	}else{

		// on constitue un tableau avec les notices des archives de diffusion
		$query_notice = "select distinct num_notice_arc as notice_id, niveau_biblio from dsi_archive, notices $acces_j where num_banette_arc='".$id_bannette."' and num_notice_arc=notice_id
		 and date_diff_arc = '$date_diff' order by ".$opac_bannette_notices_order;

		$result_notice = pmb_mysql_query($query_notice);
		if (pmb_mysql_num_rows($result_notice)) {
			while (($notice=pmb_mysql_fetch_object($result_notice))) {
				$notices[$notice->notice_id]= $notice->niveau_biblio ;
			}
		}
	}

}

$affiche_bannette_tpl="
	<div class='bannette' id='banette_!!id_bannette!!'>
		<div class='colonne2' style='width: 20%;'>
			!!historique!!
		</div>
		<div class='colonne_suite' style='width: 80%;'>
			!!diffusion!!
		</div>
		<div class='row'></div>
	</div>
";

// function affiche_bannette : affiche les bannettes et leur contenu pour l'abonné
// paramètres :
//	$bannettes : les numéros des bannettes séparés par les ',' toutes si vides
//	$aff_notices_nb : nombres de notices affichées : toutes = 0
//	$link_to_bannette : lien pour afficher le contenu de la bannette
//	$htmldiv_id="etagere-container", $htmldiv_class="etagere-container", $htmldiv_zindex="" : les id, class et zindex du <DIV > englobant le résultat de la fonction
function affiche_bannette($bannettes_ids="", $aff_notices_nb=0, $link_to_bannette="", $htmldiv_id="bannette-container", $htmldiv_class="bannette-container",$home=false ) {
	$retour_aff = "";
	// bannettes publiques
	$bannettes = tableau_bannette($bannettes_ids, $home, 'PRIV');
	if(count($bannettes)) {
		$retour_aff .= "<div id='".$htmldiv_id."-pub' class='$htmldiv_class' >";
		foreach ($bannettes as $bannette_info) {
			$bannette = new bannette($bannette_info['id_bannette']);
			$retour_aff.= $bannette->get_display($aff_notices_nb, $link_to_bannette, $home);
		}
		$retour_aff .= "</div>";
	}

	// bannettes privées
	$bannettes = tableau_bannette($bannettes_ids, $home, 'PRIV', $_SESSION['id_empr_session']);
	if(count($bannettes)) {
		$retour_aff .= "<div id='".$htmldiv_id."-priv' class='$htmldiv_class' >";
		foreach ($bannettes as $bannette_info) {
			$bannette = new bannette($bannette_info['id_bannette']);
			$retour_aff.= $bannette->get_display($aff_notices_nb, $link_to_bannette, $home);
		}
		$retour_aff .= "</div>";
	}
	return $retour_aff;
}

// function affiche_bannettes : affiche les bannettes et leur contenu pour l'abonné
// paramètres :
//	$aff_notices_nb : nombres de notices affichées : toutes = 0
//	$link_to_bannette : lien pour afficher le contenu de la bannette
//	$htmldiv_id="etagere-container", $htmldiv_class="etagere-container", $htmldiv_zindex="" : les id, class et zindex du <DIV > englobant le résultat de la fonction
function affiche_bannettes($aff_notices_nb=0, $link_to_bannette="", $htmldiv_id="bannette-container", $htmldiv_class="bannette-container",$home=false ) {
    global $msg, $opac_rgaa_active;

	$retour_aff = "";
	// bannettes publiques
	$bannettes = tableau_bannette(0, $home, 'PRIV');

	if ($opac_rgaa_active) {
	    $retour_aff .="<h2 id='title_bannette_pub' class='title_bannette' ><span>".$msg['dsi_bannette_pub']."</span></h2>";
	} else {
	    $retour_aff .="<h3 id='title_bannette_pub' class='title_bannette'><span>".$msg['dsi_bannette_pub']."</span></h3>";
	}
	$retour_aff .= "<div id='".$htmldiv_id."-pub' class='$htmldiv_class' >";
	if(count($bannettes)) {
		foreach ($bannettes as $bannette_info) {
			$bannette = new bannette($bannette_info['id_bannette']);
			$retour_aff.= $bannette->get_display($aff_notices_nb, $link_to_bannette, $home);
		}
	} else {
		$retour_aff.= $msg['dsi_bannette_no_newrecord'];
	}
	$retour_aff .= "</div>";

	// bannettes privées
	$bannettes = tableau_bannette(0, $home, 'PRIV', $_SESSION['id_empr_session']);

	if ($opac_rgaa_active) {
	    $retour_aff .="<h2 id='title_bannette_priv' class='title_bannette'><span>".$msg['dsi_bannette_priv']."</span></h2>";
	} else {
	    $retour_aff .="<h3 id='title_bannette_priv' class='title_bannette'><span>".$msg['dsi_bannette_priv']."</span></h3>";
	}

	$retour_aff .= "<div id='".$htmldiv_id."-priv' class='$htmldiv_class' >";
	if(count($bannettes)) {
		foreach ($bannettes as $bannette_info) {
			$bannette = new bannette($bannette_info['id_bannette']);
			$retour_aff.= $bannette->get_display($aff_notices_nb, $link_to_bannette, $home);
		}
	} else {
		$retour_aff.= "<p>" . $msg['dsi_bannette_no_newrecord'] . "</p>";
	}
	$retour_aff .= "</div>";
	return $retour_aff;
}

// retourne un tableau des bannettes de l'abonné
function tableau_bannette($id_bannette, $home=false, $priv_pub="PUB", $proprio_bannette=0) {
	global $msg ;
	global $id_empr ;
	global $opac_show_subscribed_bannettes;

	$tableau_bannette = array();
	if ($id_bannette) $clause = " and id_bannette in ('$id_bannette') ";
	else $clause = "";
	//Récupération des infos des bannettes
	$query = "select distinct id_bannette,comment_public, date_format(date_last_envoi, '".$msg['format_date']."') as aff_date_last_envoi, proprio_bannette from bannettes ";
	if($home) {
		$query .= " where bannette_opac_accueil=1 ";
	} else {
		if($priv_pub == "PRIV") {
			$query .= " join bannette_abon on num_bannette=id_bannette where num_empr='".$id_empr."' ";
			$query .= " and proprio_bannette='".$proprio_bannette."' ";
		} else {
			$query .= " where proprio_bannette = 0 ";
		}
	}
	$query .= " $clause order by date_last_envoi DESC ";

	$resultat=pmb_mysql_query($query);
	while ($r=pmb_mysql_fetch_object($resultat)) {
		$abon = 0;
		if($home) {
			if($id_empr && $opac_show_subscribed_bannettes){
				$query = "select count(1) from bannette_abon where num_bannette=".$r->id_bannette." and num_empr=".$id_empr;
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$abon = pmb_mysql_result($result,0,0);
				}
			}
		}
		if(!$abon) {
			$requete="select count(1) as compte from bannette_contenu where num_bannette='$r->id_bannette'";
			$resnb=pmb_mysql_query($requete);
			$nb=pmb_mysql_fetch_object($resnb) ;
			if ($nb->compte) {
				$bannette_instance = bannette::get_instance($r->id_bannette);
				$tableau_bannette[] = array (
					'id_bannette' => $r->id_bannette,
					'comment_public' => $bannette_instance->get_render_comment_public(),
					'aff_date_last_envoi' => $r->aff_date_last_envoi,
					'nb_contenu' => $nb->compte
				);
			}
		}
	}
	return $tableau_bannette ;
}

//Récupérer le human query de la bannette
function get_bannette_human_query($id_bannette = 0) {
	$bannette_human_query = '';

	$requete="select * from bannette_equation, equations where num_equation=id_equation and num_bannette=".$id_bannette;
	$resultat=pmb_mysql_query($requete);
	if (($r=pmb_mysql_fetch_object($resultat))) {
		$recherche =  $r->requete;
		$equ = new equation ($r->num_equation);
		if(!isset($search) || !is_object($search)) $search = new search();
		$search->unserialize_search($equ->requete);
		$bannette_human_query = $search->make_human_query();
	}
	return $bannette_human_query;
}

function affiche_public_bannette($bannettes="", $aff_notices_nb=0, $link_to_bannette="", $htmldiv_id="bannette-container", $htmldiv_class="bannette-container") {
	// récupération des bannettes
	$tableau_bannettes = tableau_bannette($bannettes);

	if (empty($tableau_bannettes))
		return "" ;

	// préparation du div comme il faut
	$retour_aff = "<div id='$htmldiv_id' class='$htmldiv_class' >";
	if (is_countable($tableau_bannettes)) {
		for ($i=0; $i<sizeof($tableau_bannettes); $i++ ) {
			$bannette = new bannette($tableau_bannettes[$i]['id_bannette']);
			$retour_aff.= $bannette->get_display($aff_notices_nb, $link_to_bannette, true);
		}
	}
	// fermeture du DIV
	$retour_aff .= "</div>";
	return $retour_aff ;
}