<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rss.php,v 1.17 2023/06/28 10:15:04 qvarin Exp $

use Pmb\DSI\Models\Channel\RSS\RssChannel;
use Pmb\DSI\Models\Diffusion;
use Pmb\DSI\Orm\DiffusionOrm;

$base_path=".";
$base_nocheck = 1;
$base_noheader = 1;
$base_nobody = 1;

require_once($base_path."/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");
require_once($base_path."/includes/includes_rss.inc.php");

// pour affichage de liens sur les éléments affichés : (voir la class UrlEntities)

// paramètres :
//	$accueil : filtres les étagères de l'accueil uniquement si 1
//	$etageres : les numéros des étagères séparés par les ',' toutes si vide
//	$commentaire : affiche ou non le commentaire
//	$aff_notices_nb : nombres de notices affichées : toutes = 0
//	$mode_aff_notice : mode d'affichage des notices, REDUIT (titre+auteur principal) ou ISBD ou PMB ou les deux : dans ce cas : (titre + auteur) en entête du truc, à faire dans notice_display.class.php
//	$depliable : affichage des notices une par ligne avec le bouton de dépliable
//	$link_to_etagere : lien pour afficher le contenu de l'étagère
//	$htmldiv_id="etagere-container", $htmldiv_class="etagere-container", $htmldiv_zindex="" : les id, class et zindex du <DIV > englobant le résultat de la fonction
//	$liens_opac : tableau contenant les url destinatrices des liens si voulu
// function affiche_etagere($accueil=0, $etageres="", $aff_commentaire=0, $aff_notices_nb=0, $mode_aff_notice=AFF_ETA_NOTICES_BOTH, $depliable=AFF_ETA_NOTICES_DEPLIABLES_OUI, $link_to_etagere="", $htmldiv_id="etagere-container", $htmldiv_class="etagere-container", $htmldiv_zindex="", $liens_opac=array() ) {

switch ($lvl) {

    case "dsi":
		$id = intval($id);
		if (DiffusionOrm::exist($id)) {
			$diffusion = new Diffusion($id);
		    $history = $diffusion->getLastHistorySent(RssChannel::class);
		    if (!empty($history)) {
			    $history->send();
            }
		}
        die();
        break;

    default:
        $flux = new rss_flux($id);
        if (!$flux->contenu_du_flux) {
            $flux->items_notices();
            $flux->xmlfile();
            $flux->contenu_du_flux = str_replace(
				"!!items!!",
				$flux->notices,
				$flux->envoi
			);

            if ($charset=='utf-8') {
                $flux->contenu_du_flux = preg_replace(
                    '/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'.
                    '|[\x00-\x7F][\x80-\xBF]+'.
                    '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'.
                    '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'.
                    '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/',
                    '',
                    $flux->contenu_du_flux
                );
            } else {
                $flux->contenu_du_flux = preg_replace(
                    '/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]/',
                    '',
                    $flux->contenu_du_flux
                );
            }
            $flux->stocke_cache();
            $content = $flux->contenu_du_flux;

            @header("Content-type: text/xml; charset=".$charset);
            echo $content;
        }
        break;
}

