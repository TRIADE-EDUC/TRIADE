<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: visionneuse.php,v 1.51.2.1 2024/06/18 14:31:44 qvarin Exp $

use Pmb\Common\Library\RGAA\RGAABuilder;

$base_path = ".";
$include_path = "$base_path/includes";
$class_path = "$base_path/classes";
$visionneuse_path = "$base_path/visionneuse";

require_once ($include_path . "/apache_functions.inc.php");

$headers = getallheaders();
if (isset($headers['If-Modified-Since'])) {
    if (isset($_GET['lvl']) && isset($_GET['explnum_id']) && isset($_GET['method']) && isset($_GET['page'])) {
        $lvl = $_GET['lvl'];
        $explnum_id = $_GET['explnum_id'] * 1;
        $method = $_GET['method'];
        $page = $_GET['page'] * 1;
        if ($lvl == 'ajax' && $method == 'getPage') {
            $tmp_file = $visionneuse_path . '/temp/pmb_page_' . $explnum_id . '-' . $page;
            if (file_exists($tmp_file) && filemtime($tmp_file) <= strtotime($headers['If-Modified-Since'])) {
                header('Last-Modified: ' . $headers['If-Modified-Since'], true, 304);
                return;
            }
        }
    }
}

// y a plein de trucs à récup...
require_once ($base_path . "/includes/init.inc.php");

// fichiers nécessaires au bon fonctionnement de l'environnement
require_once ($base_path . "/includes/common_includes.inc.php");

require_once ($include_path . '/templates/common.tpl.php');
require_once ($base_path . "/includes/includes_rss.inc.php");
require_once ($class_path . "/cms/cms_cache.class.php");
// c'est bon, on peut commencer...
require_once ($include_path . '/plugins.inc.php');
// si paramétrage authentification particulière et pour la re-authentification ntlm
if (file_exists($base_path . '/includes/ext_auth.inc.php'))
    require_once ($base_path . '/includes/ext_auth.inc.php');

require_once ($visionneuse_path . "/classes/visionneuse.class.php");

session_write_close();

global $opac_parse_html, $cms_active, $opac_rgaa_active;
if ($opac_parse_html || $cms_active || $opac_rgaa_active) {
    ob_start();
}

if (! empty($explnum_id)) {
    $explnum_id = intval($explnum_id);
} else {
    $explnum_id = 0;
}

// Pour les epubs
if (! empty($_SERVER["PATH_INFO"])) {
    $myPage = '';
    $tmpEpub = explode("/", trim($_SERVER["PATH_INFO"], "/"));
    $lvl = 'afficheur';
    $driver = array_shift($tmpEpub);
    $explnum = array_shift($tmpEpub);
    $myPage = implode("/", $tmpEpub);
} else {
    if (! isset($myPage) || ! $myPage)
        $myPage = '';
    if (! isset($driver))
        $driver = '';
}

switch ($driver) {
    case "pmb_document":
        require_once ($visionneuse_path . "/api/pmb/pmb_document.class.php");
        if ($lvl == "" || $lvl == "visionneuse") {
            $lvl = "visionneuse";
            $short_header = str_replace("!!liens_rss!!", "", $short_header);
            print $short_header;
        }
        if ($lvl != "ajax") {
            $params = array(
                'lvl' => $lvl,
                'type' => $cms_type,
                'num_type' => $num_type ?? '',
                'id' => $id,
                'explnum' => $explnum ?? '',
                'explnum_id' => $explnum_id ?? '',
                'user_query' => $user_query ?? '',
                'position' => $position ?? '',
                "page" => $myPage ?? 1
            );
        } else {
            $params = array(
                'lvl' => $lvl,
                'explnum_id' => $explnum_id,
                'start' => true,
                'action' => $action,
                'method' => $method
            );
        }
        $visionneuse = new visionneuse($driver, $visionneuse_path, $lvl, $lang, $params);
        break;
    case "pmb":
    default:
        require_once ($visionneuse_path . "/api/pmb/pmb.class.php");
        if ($lvl == "" || $lvl == "visionneuse") {
            $lvl = "visionneuse";
            $short_header = str_replace("!!liens_rss!!", "", $short_header);
            print $short_header;
            $opac_allow_simili_search = 0;
            $opac_notice_enrichment = 0;
            print "<script src='$include_path/javascript/tablist.js'></script>";
        }
        if (isset($_POST["position"])) {
            $position = $_POST["position"];
            if ($lvl == "visionneuse") {
                $start = false;
            } else {
                $start = true;
            }
        } else {
            $position = 0;
            $start = true;
        }
        if ($lvl == "afficheur" || $lvl == "visionneuse") {
            if (! isset($search))
                $search = '';
            $params = array(
                "mode" => (isset($mode) ? $mode : ''),
                "user_query" => (isset($user_query) ? $user_query : ''),
                "pert" => (isset($pert) ? $pert : ''),
                "join" => (isset($join) ? $join : ''),
                "clause" => (isset($clause) ? $clause : ''),
                "clause_bull" => (isset($clause_bull) ? $clause_bull : ''),
                "clause_bull_num_notice" => (isset($clause_bull_num_notice) ? $clause_bull_num_notice : ''),
                "tri" => (isset($tri) ? $tri : ''),
                "table" => (isset($table) ? $table : ''),
                "user_code" => $_SESSION["user_code"],
                "idautorite" => (isset($idautorite) ? $idautorite : ''),
                "id" => (isset($id) ? $id : ''),
                "idperio" => (isset($idperio) ? $idperio : ''),
                "search" => (! is_array($search) ? $search : $serialized_search), // A vérifier, mais à mon avis ca sert à rien ce test
                "bulletin" => (isset($bulletin) ? $bulletin : ''),
                "explnum_id" => $explnum_id,
                "position" => (isset($position) ? $position : ''),
                "start" => (isset($start) ? $start : ''),
                "lvl" => $lvl,
                "explnum" => (isset($explnum) ? $explnum : ''),
                "page" => (isset($myPage) ? $myPage : ''),
                "bull_only" => (isset($bull_only) ? $bull_only : ''),
                "serialized_search" => (isset($serialized_search) ? $serialized_search : '')
            );
        } else {
            $params = array(
                'explnum_id' => $explnum_id,
                'start' => true,
                'action' => $action,
                'method' => $method
            );
        }

        $visionneuse = new visionneuse("pmb", $visionneuse_path, $lvl, $lang, $params);
        break;
}

if ($lvl == "" || $lvl == "visionneuse") {
    if ($opac_visionneuse_alert) {
        $confirm_alert = false;
        if ($opac_visionneuse_alert_doctype) {
            $t_opac_visionneuse_alert_doctype = explode(',', $opac_visionneuse_alert_doctype);
            $q = 'select typdoc from explnum join notices on explnum_notice=notice_id and explnum_id=' . $explnum_id . ' ';
            $q .= 'union ';
            $q .= 'select typdoc from explnum join bulletins on explnum_bulletin=bulletin_id and explnum_id=' . $explnum_id . ' join notices on num_notice=notice_id ';
            $q .= 'union ';
            $q .= 'select typdoc from explnum join bulletins on explnum_bulletin=bulletin_id and explnum_id=' . $explnum_id . ' join notices on bulletin_notice=notice_id';
            $r = pmb_mysql_query($q, $dbh);
            if (pmb_mysql_num_rows($r)) {
                $typdoc = pmb_mysql_result($r, 0, 0);
                if (is_array($t_opac_visionneuse_alert_doctype) && in_array($typdoc, $t_opac_visionneuse_alert_doctype)) {
                    $confirm_alert = true;
                }
            }
        }
        if ($confirm_alert) {
            print "<script>window.parent.open_alertbox('" . addslashes(trim($opac_visionneuse_alert)) . "');</script>";
        }
    }
    print $short_footer;
}

if (($opac_parse_html) && ($lvl != "afficheur") && ($lvl != "ajax")) {
    $htmltoparse = parseHTML(ob_get_contents());
    ob_end_clean();

    $htmltoparse = RGAABuilder::transform($htmltoparse);

    print $htmltoparse;
}

/*
 * if ($cms_active) {
 * require_once($base_path."/classes/cms/cms_build.class.php");
 * $cms=new cms_build();
 * $htmltoparse = $cms->transform_html($htmltoparse);
 * }
 */

if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
}
