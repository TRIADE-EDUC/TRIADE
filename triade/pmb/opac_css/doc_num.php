<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: doc_num.php,v 1.73.4.2 2025/03/11 07:59:34 dgoron Exp $

use Pmb\Digitalsignature\Models\DocnumCertifier;

$base_path = ".";
require_once($base_path."/includes/init.inc.php");

global $class_path, $include_path, $css, $msg, $charset;
global $opac_opac_view_activate, $opac_view, $current_opac_view, $pmb_logs_activate;
global $gestion_acces_active, $gestion_acces_empr_notice, $gestion_acces_empr_docnum;
global $explnum_id, $_mimetypes_bymimetype_, $opac_photo_watermark;
global $pmb_digital_signature_activate, $get_sign;
global $short_header;

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

if ($css == "") {
    $css = 1;
}

require_once($include_path.'/plugins.inc.php');

require_once("./includes/explnum.inc.php");

require_once($class_path."/explnum.class.php");

//si les vues sont activées (à laisser après le calcul des mots vides)
if ($opac_opac_view_activate) {
    $opac_view = intval($opac_view);
    if ($opac_view) {
        if ($current_opac_view != $opac_view) {
            //on change de vue donc :
            //on stocke le tri en cours pour la vue en cours
            $_SESSION["last_sortnotices_view_".$current_opac_view] = $_SESSION["last_sortnotices"];
            if (isset($_SESSION["last_sortnotices_view_".$opac_view])) {
                //on a déjà un tri pour la nouvelle vue, on l'applique
                $_SESSION["last_sortnotices"] = $_SESSION["last_sortnotices_view_".$opac_view];
            } else {
                unset($_SESSION["last_sortnotices"]);
            }
            //comparateur de facettes : on ré-initialise
            require_once($base_path.'/classes/facette_search_compare.class.php');
            facette_search_compare::session_facette_compare(null, true);
            //comparateur de facettes externes : on ré-initialise
            require_once($base_path.'/classes/facettes_external_search_compare.class.php');
            facettes_external_search_compare::session_facette_compare(null, true);
        }
    }
}

//gestion des droits
require_once($class_path."/acces.class.php");

//si paramétrage authentification particulière et pour la re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) {
    require_once($base_path.'/includes/ext_auth.inc.php');
}

// Si l'on vient d'une DSI avec une connexion auto
if (isset($code)) {
    // pour fonction de vérification de connexion
    require_once($base_path.'/includes/empr_func.inc.php');
    $log_ok = connexion_empr();
    if ($log_ok) {
        $_SESSION["connexion_empr_auto"] = 1;
    }
}

$explnum_id = intval($explnum_id);
if (!$explnum_id) {
    header("Location: images/mimetype/unknown.gif");
    exit ;
}
$explnum = new explnum($explnum_id);

$id_for_rights = $explnum->explnum_notice;
if ($explnum->explnum_bulletin != 0) {
    //si bulletin, les droits sont rattachés à la notice du bulletin, à défaut du pério...
    $req = "select bulletin_notice,num_notice from bulletins where bulletin_id =".$explnum->explnum_bulletin;
    $res = pmb_mysql_query($req);
    if (pmb_mysql_num_rows($res)) {
        $row = pmb_mysql_fetch_object($res);
        $id_for_rights = $row->num_notice;
        if (!$id_for_rights) {
            $id_for_rights = $row->bulletin_notice;
        }
    }
    $type = "" ;
}


//droits d'acces emprunteur/notice
$rights = 0;
$dom_2 = null;
if ($gestion_acces_active == 1 && $gestion_acces_empr_notice == 1) {
    $ac = new acces();
    $dom_2 = $ac->setDomain(2);
    $rights = $dom_2->getRights($_SESSION['id_empr_session'], $id_for_rights);
}

//Accessibilité des documents numériques aux abonnés en opac
$req_restriction_abo = "SELECT explnum_visible_opac, explnum_visible_opac_abon FROM notices,notice_statut WHERE notice_id='".$id_for_rights."' AND statut=id_notice_statut ";
$result = pmb_mysql_query($req_restriction_abo);
$expl_num = pmb_mysql_fetch_array($result, PMB_MYSQL_ASSOC);

//droits d'acces emprunteur/document numérique
$docnum_rights = 0;
$dom_3 = null;
if ($gestion_acces_active == 1 && $gestion_acces_empr_docnum == 1) {
    $ac = new acces();
    $dom_3 = $ac->setDomain(3);
    $docnum_rights = $dom_3->getRights($_SESSION['id_empr_session'], $explnum_id);
}

//Accessibilité (Consultation/Téléchargement) sur le document numérique aux abonnés en opac
$req_restriction_docnum_abo = "SELECT explnum_download_opac, explnum_download_opac_abon FROM explnum,explnum_statut WHERE explnum_id='".$explnum_id."' AND explnum_docnum_statut=id_explnum_statut ";
$result_docnum = pmb_mysql_query($req_restriction_docnum_abo);
$docnum_expl_num = pmb_mysql_fetch_array($result_docnum, PMB_MYSQL_ASSOC);


$access_is_allowed = false;
if ((
    ($rights & 16)
    || (
        is_null($dom_2)
        && $expl_num["explnum_visible_opac"]
        && (
            !$expl_num["explnum_visible_opac_abon"]
            || ($expl_num["explnum_visible_opac_abon"] && $_SESSION["user_code"])
        )
    )
)
    && (
        ($docnum_rights & 8)
        || (
            is_null($dom_3)
            && $docnum_expl_num["explnum_download_opac"]
            && (
                !$docnum_expl_num["explnum_download_opac_abon"]
                || ($docnum_expl_num["explnum_download_opac_abon"] && $_SESSION["user_code"])
            )
        )
    )
) {
    $access_is_allowed = true;
}

//Si le document est accessible
if ($access_is_allowed) {

    if ($pmb_digital_signature_activate && $get_sign) {
        $certifier = new DocnumCertifier($explnum);
        if ($certifier->checkSignExists()) {
            $path = $certifier->getCmsFilePath();
            header("Content-Disposition: attachment; filename=sign_docnum_" . $explnum->explnum_id . ".cms");
            print file_get_contents($path);
            exit;
        }
    }

    if (!($file_loc = $explnum->get_is_file())) {
        $content = $explnum->get_file_content();
    } else {
        $content = '';
    }
    if ($file_loc || $content) {
        if ($pmb_logs_activate) {
            // Enregistrement du log
            $infos_restriction_abo = [];
            foreach ($expl_num as $key => $value) {
                $infos_restriction_abo[$key] = $value;
            }

            $infos_restriction_docnum_abo = [];
            foreach ($docnum_expl_num as $key => $value) {
                $infos_restriction_docnum_abo[$key] = $value;
            }

            generate_log('doc_num_file', [
                'explnum' => $explnum->get_explnum_infos(),
                'restriction_abo' => $infos_restriction_abo,
                'restriction_docnum_abo' => $infos_restriction_docnum_abo,
            ], true);
        }

        create_tableau_mimetype() ;
        $name = $_mimetypes_bymimetype_[$explnum->explnum_mimetype]["plugin"] ;
        if ($name) {
            $type = "" ;
            // width='700' height='525'
            $name = " name='$name' ";
        }
        $type = "type='$explnum->explnum_mimetype'" ;

        if ($_mimetypes_bymimetype_[$explnum->explnum_mimetype]["embeded"] == "yes") {
            print "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body><EMBED src=\"./doc_num_data.php?explnum_id=$explnum_id\" $type $name controls='console' ></EMBED></body></html>" ;
            exit ;
        }

        $file_name = $explnum->get_file_name();

        if ($file_name) {
            header('Content-Disposition: inline; filename="'.$file_name.'"');
        }

        if ((substr($explnum->explnum_mimetype, 0, 5) == "image") && ($opac_photo_watermark)) {
            if (!$content) {
                $content = $explnum->get_file_content();
            }
            $content_image = reduire_image_middle($content);
            session_write_close();
            pmb_mysql_close();
            if ($content_image) {
                print header("Content-Type: image/png");
                print $content_image;
            } else {
                header("Content-Type: ".$explnum->explnum_mimetype);
                print $content;
            }
        } else {
            if ($explnum->explnum_mimetype == 'text/html') {
                header("Content-Type: ".$explnum->explnum_mimetype." charset=".$charset);
            } else {
                header("Content-Type: ".$explnum->explnum_mimetype);
            }
            if ($file_loc) {
                session_write_close();
                pmb_mysql_close();
                readfile($file_loc);
            } else {
                if (!$content) {
                    $content = $explnum->get_file_content();
                }
                session_write_close();
                pmb_mysql_close();
                print $content;
            }
        }
        exit ;
    }

    if ($explnum->explnum_mimetype == "URL") {
        if ($explnum->explnum_url) {
            $explnum_url = $explnum->explnum_url;
            // CAIRN
            if (strpos($explnum_url, "cairn.info") !== false) {
                require_once($base_path."/admin/connecteurs/in/cairn/cairn.class.php");
                $cairn_connector = new cairn();
                $cairn_sso_params = $cairn_connector->get_sso_params();
                if ($cairn_sso_params && (strpos($explnum_url, "?") === false)) {
                    $explnum_url .= "?";
                    $cairn_sso_params = substr($cairn_sso_params, 1);
                }
                $explnum_url .= $cairn_sso_params;
            }

            if ($pmb_logs_activate) {
                generate_log('doc_num_url', [
                    'explnum' => $explnum->get_explnum_infos(),
                    'called_url' => $explnum_url,
                    'type_url' => 'external_url_docnum',
                ], true);
            }
            header("Location: ".$explnum_url);
        }
        exit ;
    }
}

// Si le document n'est pas accessible et que l'on est pas connecté, on propose de se connecter
if (!$access_is_allowed  && !$_SESSION['id_empr_session']) {
    require_once($base_path."/includes/templates/common.tpl.php");
    $short_header = str_replace("!!liens_rss!!", "", $short_header);
    print $short_header;
    require_once($class_path."/auth_popup.class.php");
    print "<div id='att'></div>
			<script src='".$include_path."/javascript/auth_popup.js'></script>";
    print "<script>
			auth_popup('./ajax.php?module=ajax&categ=auth&callback_func=docnum_refresh', true);
			function docnum_refresh(){
				window.location.reload();
			}
		</script>";
// Sinon on indique que le document n'est pas accessible
} else {
    http_response_code(403);
    print $msg['forbidden_docnum'];
}
