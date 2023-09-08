<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vig_num.php,v 1.23 2018-09-28 12:21:35 dgoron Exp $

$base_path=".";
require_once($base_path."/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");
session_write_close();//Fermeture de la session dès que possible

if ($css=="") $css=1;

require_once($include_path."/explnum.inc.php");

//gestion des droits
require_once($class_path."/acces.class.php");

require_once($class_path."/bannette.class.php");

$explnum_id = $explnum_id+0;
$resultat = pmb_mysql_query("SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_mimetype, explnum_vignette,explnum_extfichier FROM explnum WHERE explnum_id = '$explnum_id' ", $dbh);
$nb_res = pmb_mysql_num_rows($resultat) ;

if (!$nb_res) {
	exit ;
} 

$ligne = pmb_mysql_fetch_object($resultat);

$id_for_rigths = $ligne->explnum_notice;
if($ligne->explnum_bulletin != 0){
	//si bulletin, les droits sont rattachés à la notice du pério...
	$req = "select bulletin_notice,num_notice from bulletins where bulletin_id =".$ligne->explnum_bulletin;
	$res = pmb_mysql_query($req,$dbh);
	if(pmb_mysql_num_rows($res)){
		$row = pmb_mysql_fetch_object($res);
		$id_for_rigths = $row->num_notice;
		if(!$id_for_rigths){
			$id_for_rigths = $row->bulletin_notice;
		}
	}
}
//droits d'acces emprunteur/notice
if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
	$ac= new acces();
	$dom_2= $ac->setDomain(2);
	$rights= $dom_2->getRights($_SESSION['id_empr_session'],$id_for_rigths);
}

//Accessibilité des documents numériques aux abonnés en opac
$req_restriction_abo = "SELECT explnum_visible_opac, explnum_visible_opac_abon FROM notices,notice_statut WHERE notice_id='".$id_for_rigths."' AND statut=id_notice_statut ";

$result=pmb_mysql_query($req_restriction_abo,$dbh);
$expl_num=pmb_mysql_fetch_object($result);

//droits d'acces emprunteur/document numérique
if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
	$ac= new acces();
	$dom_3= $ac->setDomain(3);
	$docnum_rights= $dom_3->getRights($_SESSION['id_empr_session'],$explnum_id);
}

//Accessibilité sur le document numérique aux abonnés en opac
$req_restriction_docnum_abo = "SELECT explnum_visible_opac, explnum_visible_opac_abon, explnum_thumbnail_visible_opac_override FROM explnum,explnum_statut WHERE explnum_id='".$explnum_id."' AND explnum_docnum_statut=id_explnum_statut ";

$result_docnum=pmb_mysql_query($req_restriction_docnum_abo,$dbh);
$docnum_expl_num=pmb_mysql_fetch_object($result_docnum);

$statut_not_account = false;
if ($context_dsi_id_bannette) {
	$bannette = new bannette($context_dsi_id_bannette);
	$statut_not_account = $bannette->statut_not_account;
}

if($opac_show_links_invisible_docnums || (($rights & 16 || (is_null($dom_2) && $expl_num->explnum_visible_opac && (!$expl_num->explnum_visible_opac_abon || ($expl_num->explnum_visible_opac_abon && $_SESSION["user_code"])||($expl_num->explnum_visible_opac_abon && $statut_not_account))))
	&& ($docnum_expl_num->explnum_thumbnail_visible_opac_override || $docnum_rights & 16 || (is_null($dom_3) && $docnum_expl_num->explnum_visible_opac && (!$docnum_expl_num->explnum_visible_opac_abon || ($docnum_expl_num->explnum_visible_opac_abon && $_SESSION["user_code"])))))){
	if ($ligne->explnum_vignette) {
		print $ligne->explnum_vignette;
		exit ;
	} else {
		create_tableau_mimetype();
		$iconname=icone_mimetype ($ligne->explnum_mimetype, $ligne->explnum_extfichier);
		$fp = fopen("./images/mimetype/$iconname" , "r" ) ;
		$contenu_vignette = fread ($fp, filesize("./images/mimetype/$iconname"));
		fclose ($fp) ;
		print $contenu_vignette ;
	}
}
