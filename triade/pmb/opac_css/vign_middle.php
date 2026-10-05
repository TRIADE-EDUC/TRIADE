<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vign_middle.php,v 1.17 2022/01/03 10:35:09 dgoron Exp $

$base_path=".";
require_once($base_path."/includes/init.inc.php");

global $class_path, $include_path, $gestion_acces_active, $gestion_acces_empr_notice, $gestion_acces_empr_docnum, $explnum_id;
global $opac_show_links_invisible_docnums;

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

//Fonctions exemplaires numériques
require_once($include_path."/explnum.inc.php");
require_once($class_path."/upload_folder.class.php");
//gestion des droits
require_once($class_path."/acces.class.php");

$explnum_id = intval($explnum_id);
$resultat = pmb_mysql_query("SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_mimetype, explnum_data, explnum_nom as nom, explnum_repertoire, explnum_path, explnum_nomfichier FROM explnum WHERE explnum_id = '$explnum_id' ");
$nb_res = pmb_mysql_num_rows($resultat) ;

if (!$nb_res) {
	exit ;
} 

$ligne = pmb_mysql_fetch_object($resultat);

$id_for_rigths = $ligne->explnum_notice;
if($ligne->explnum_bulletin != 0){
	//si bulletin, les droits sont rattachés à la notice du pério...
	$req = "select bulletin_notice,num_notice from bulletins where bulletin_id =".$ligne->explnum_bulletin;
	$res = pmb_mysql_query($req);
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

$result=pmb_mysql_query($req_restriction_abo);
$expl_num=pmb_mysql_fetch_object($result);

//droits d'acces emprunteur/document numérique
if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
	$ac= new acces();
	$dom_3= $ac->setDomain(3);
	$docnum_rights= $dom_3->getRights($_SESSION['id_empr_session'],$explnum_id);
}

//Accessibilité sur le document numérique aux abonnés en opac
$req_restriction_docnum_abo = "SELECT explnum_visible_opac, explnum_visible_opac_abon, explnum_thumbnail_visible_opac_override FROM explnum,explnum_statut WHERE explnum_id='".$explnum_id."' AND explnum_docnum_statut=id_explnum_statut ";

$result_docnum=pmb_mysql_query($req_restriction_docnum_abo);
$docnum_expl_num=pmb_mysql_fetch_object($result_docnum);

if($opac_show_links_invisible_docnums || (($rights & 16 || (is_null($dom_2) && $expl_num->explnum_visible_opac && (!$expl_num->explnum_visible_opac_abon || ($expl_num->explnum_visible_opac_abon && $_SESSION["user_code"]))))
	&& ($docnum_expl_num->explnum_thumbnail_visible_opac_override || $docnum_rights & 16 || (is_null($dom_3) && $docnum_expl_num->explnum_visible_opac && (!$docnum_expl_num->explnum_visible_opac_abon || ($docnum_expl_num->explnum_visible_opac_abon && $_SESSION["user_code"])))))){
	if ($ligne->explnum_data) {
		if($ligne->explnum_mimetype == 'application/pdf'){
			$contenu_vignette = $ligne->explnum_data;
			header('Content-type: application/pdf');
		}else $contenu_vignette=reduire_image_middle($ligne->explnum_data);	
		if ($contenu_vignette) {
			header('Content-type: image/png');		
		}else {
			$contenu_vignette = file_get_contents("./images/mimetype/unknown.gif");
			header('Content-type: image/gif');
		}
	} elseif($ligne->explnum_repertoire != 0){
		$rep = new upload_folder($ligne->explnum_repertoire);
		$filepath =  $rep->repertoire_path.$ligne->explnum_path.$ligne->explnum_nomfichier;
		$filepath = str_replace("//","/",$filepath);
		$contenu_vignette = file_get_contents($filepath);
		if($ligne->explnum_mimetype == 'application/pdf'){
			header('Content-type: application/pdf');
		}else{
			$contenu_vignette=reduire_image_middle($contenu_vignette);
			header('Content-type: image/png');		
		}
	} else{
		$contenu_vignette = file_get_contents("./images/mimetype/unknown.gif");
		header('Content-type: image/gif');
	}
	print $contenu_vignette ;
}
?>