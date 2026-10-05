<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_view.php,v 1.24 2023/08/17 09:47:54 dbellamy Exp $

$base_path=".";
//Affichage d'une notice
require_once($base_path."/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

require_once($base_path.'/includes/templates/common.tpl.php');

require_once($base_path.'/classes/common.class.php');

// classe de gestion des catégories
require_once($base_path.'/classes/categorie.class.php');
require_once($base_path.'/classes/notice.class.php');
require_once($base_path.'/classes/notice_display.class.php');

// classe indexation interne
require_once($base_path.'/classes/indexint.class.php');

// classe d'affichage des tags
require_once($base_path.'/classes/tags.class.php');

// classe de gestion des réservations
require_once($base_path.'/classes/resa.class.php');

// pour l'affichage correct des notices
require_once($base_path."/includes/templates/notice.tpl.php");
require_once($base_path."/includes/navbar.inc.php");
require_once($base_path."/includes/explnum.inc.php");
require_once($base_path."/includes/notice_affichage.inc.php");

// si paramétrage authentification particulière et pour la re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');

global $charset;
global $opac_notice_enrichment, $opac_parse_html, $cms_active, $stylescsscodehtml, $opac_rgaa_active;

// paramétrage de base
$templates = "
	<html xmlns='http://www.w3.org/1999/xhtml' charset='".$charset."'>
		<head>
			<meta http-equiv='content-type' content='text/html; charset=".$charset."' />
			!!styles!!
			!!scripts!!
			".common::get_dojo_configuration()."
        </head>
		<body>";
if ($opac_notice_enrichment == 0) {
	$templates .= common::get_js_function_record_display();
}
$templates .= "<!--<div id='bouton_fermer_notice_preview' class='right'><a href='#' class='panel-close' onClick='parent.kill_frame();return false;'><i alt='".$msg["notice_preview_close"]."' class='fa fa-times' aria-hidden='true'></i></a></div>//-->
			<div id='notice'>
				#FILES
			</div>
		</body>
	</html>";

$liens_opac=0;
$opac_notices_depliable=0;

// paramétrages avancés dans fichier si existe
if (file_exists($base_path."/includes/notice_view_param.inc.php")) 
	include($base_path."/includes/notice_view_param.inc.php");

$templates=str_replace("!!styles!!",$stylescsscodehtml,$templates);

//Enrichissement OPAC
if($opac_notice_enrichment){
	require_once($base_path."/classes/enrichment.class.php");
	$enrichment = new enrichment();
	$templates=str_replace("!!scripts!!",
		"<script src='includes/javascript/http_request.js'></script>".$enrichment->getHeaders(),
	$templates);
} else $templates=str_replace("!!scripts!!","",$templates);

$id= $_GET["id"];

if($opac_parse_html || $cms_active || $opac_rgaa_active){
	ob_start();
}

//Affichage d'une notice
$notice=aff_notice($id,1);
print str_replace("#FILES",$notice,$templates);

if($opac_parse_html || $cms_active){
	if($opac_parse_html){
		$htmltoparse= parseHTML(ob_get_contents());
	}else{
		$htmltoparse= ob_get_contents();
	}

	ob_end_clean();
	if ($cms_active) {
		require_once($base_path."/classes/cms/cms_build.class.php");
		$cms=new cms_build();
		$htmltoparse = $cms->transform_html($htmltoparse);
	}
	print $htmltoparse;
}
?>