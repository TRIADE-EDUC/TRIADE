<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: recall.php,v 1.18 2021/02/09 11:09:52 qvarin Exp $

header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: post-check=0, pre-check=0",false);
session_cache_limiter('must-revalidate');
session_name("pmb".$_COOKIE['PhpMyBibli-SESSID']);
session_start();

$base_path = ".";
$include_path = $base_path."/includes";

$_SESSION["last_required"]=$_GET["t"];
if (!isset($_GET["current"]) || $_GET["current"]!==false) $_SESSION["CURRENT"]=$_GET["current"];
$_SESSION["MAP_CURRENT"]=$_SESSION["CURRENT"];

// On enregistre le tri
$sort_type = "tri";
if ('authorities' == $_GET["reference"]) {
    // On applique un tri à une authorités
    $sort_type .= "_" . $_GET["type_tri"];
}

if (isset($_GET["tri"])) {
	if ($_GET["tri"] == -1) {
		unset($_SESSION[$sort_type]);
	} else {
	    $_SESSION[$sort_type] = $_GET["tri"];
	}
}


// On definis la route selon la reférence
if ($_GET["reference"] == "authorities") {
    $preferenceKey = "id_authority";
    $base_url = "autorites.php";
} else {
    $preferenceKey = "notice_id";
    $base_url = "catalog.php";
}

$url = $base_url;

//Appel du mode recherche externe
if (isset($_GET["external"]) && $_GET["external"]==1) {
	$_SESSION["last_required"]="";
	if ($_SESSION["session_history"][$_SESSION["CURRENT"]][$_GET["t"]]["GET"]["mode"]!==false) {
	    $from_mode = $_SESSION["session_history"][$_SESSION["CURRENT"]][$_GET["t"]]["GET"]["mode"];
	    switch ($from_mode) {
            case 6:
                // recherche multi-critères
                $external_type="multi";
                break;
            case 7:
                // recherche externe
                $external_type = $_SESSION["ext_type"] ?? "simple";
                break;
            
            default:
                if ($from_mode < 6) {
                    $external_type="simple";
                } else {
                    $external_type="multi";
                }
            break;
        }
        $url = $base_url."?categ=search&mode=7&from_mode=".$from_mode."&external_type=".$external_type."&sub=launch&".$preferenceKey."=";
	}
} else if ($_SESSION["session_history"][$_SESSION["CURRENT"]][$_GET["t"]]["URI"]) {
    //Sinon appel normal
    $url = $_SESSION["session_history"][$_SESSION["CURRENT"]][$_GET["t"]]["URI"];
}

if (isset($_GET["ajax"]) && $_GET["ajax"]) {
    require_once $include_path.'/ajax.inc.php';
    ajax_http_send_response(json_encode(['success' => true, 'url' => $url]));
} else {
    echo "<script>document.location='".$url."';</script>";
}

?>