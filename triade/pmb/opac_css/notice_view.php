<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_view.php,v 1.19 2018-11-19 16:25:17 dgoron Exp $

$base_path=".";
//Affichage d'une notice
require_once($base_path."/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

require_once($base_path.'/includes/templates/common.tpl.php');

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

// paramétrage de base
$templates = "
	<html xmlns='http://www.w3.org/1999/xhtml' charset='".$charset."'>
		<head>
			<meta http-equiv='content-type' content='text/html; charset=".$charset."' />
			!!styles!!
			!!scripts!!
		</head>
		<body>";
if ($opac_notice_enrichment == 0) {
	$templates .= "<script type='text/javascript'>
		function findNoticeElement(id){
			var ul=null;
			//cas des notices classiques
			var domNotice = document.getElementById('el'+id+'Child');
			//notice_display
			if(!domNotice) domNotice = document.getElementById('notice');
			if(domNotice){
				var uls = domNotice.getElementsByTagName('ul');
				for (var i=0 ; i<uls.length ; i++){
					if(uls[i].getAttribute('id') == 'onglets_isbd_public'+id){
						var ul = uls[i];
						break;
					}
				}
			} else{
				var li = document.getElementById('onglet_isbd'+id);
				if(!li) var li = document.getElementById('onglet_public'+id);
				if(!li) var li = document.getElementById('onglet_detail'+id);
				if(li) var ul = li.parentNode;
			}
			return ul;
		}
		function show_what(quoi, id) {
			switch(quoi){
				case 'EXPL_LOC' :
					document.getElementById('div_expl_loc' + id).style.display = 'block';
					document.getElementById('div_expl' + id).style.display = 'none';		
					document.getElementById('onglet_expl' + id).className = 'isbd_public_inactive';		
					document.getElementById('onglet_expl_loc' + id).className = 'isbd_public_active';
					break;
				case 'EXPL' :
					document.getElementById('div_expl_loc' + id).style.display = 'none';
					document.getElementById('div_expl' + id).style.display = 'block';
					document.getElementById('onglet_expl' + id).className = 'isbd_public_active';
					document.getElementById('onglet_expl_loc' + id).className = 'isbd_public_inactive';
					break;
					default :
						quoi= quoi.toLowerCase();
						var ul = findNoticeElement(id);
						if (ul) {
							var items  = ul.getElementsByTagName('li');
							for (var i=0 ; i<items.length ; i++){
								if(items[i].getAttribute('id')){
									if(items[i].getAttribute('id') == 'onglet_'+quoi+id){
										items[i].className = 'isbd_public_active';
										document.getElementById('div_'+quoi+id).style.display = 'block';
									}else{
										if(items[i].className != 'onglet_tags' && items[i].className != 'onglet_avis' && items[i].className != 'onglet_sugg' && items[i].className != 'onglet_basket' && items[i].className != 'onglet_liste_lecture'){
											items[i].className = 'isbd_public_inactive';	
											document.getElementById(items[i].getAttribute('id').replace('onglet','div')).style.display = 'none';
										}
									}
								}
							}			
						}
						break;
				}
			}	  	
		</script>";
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
		"<script type='text/javascript' src='includes/javascript/http_request.js'></script>".$enrichment->getHeaders(),
	$templates);
} else $templates=str_replace("!!scripts!!","",$templates);

$id= $_GET["id"];

if($opac_parse_html || $cms_active){
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