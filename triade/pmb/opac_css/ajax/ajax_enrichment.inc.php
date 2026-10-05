<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_enrichment.inc.php,v 1.9.4.1 2025/01/29 14:09:21 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

global $include_path;
require_once $include_path . '/apache_functions.inc.php';

if ('gettype' == $action) {
	// Mise en cache des messages
	// Le cache dure 1 jour
	$offset = 60 * 60 * 24;

	// On ajoute des entêtes qui autorisent le navigateur à faire du cache...
	$headers = getallheaders();
	if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) <= time())) {
		header('Last-Modified: '.$headers['If-Modified-Since'], true, 304);
        session_write_close();
		return;
	} else {
		header('Expired: '.gmdate("D, d M Y H:i:s", time() + $offset).' GMT', true);
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT', true, 200);
	}
}

global $class_path, $enrichPage, $enrich_params, $action, $type, $id, $debug;
require_once $class_path . '/enrichment.class.php';
require_once $class_path . '/parametres_perso.class.php';

$enrichPage = $enrichPage ?? 1;
$enrich_params = $enrich_params ?? [];
$action = $action ?? '';
$type = $type ?? '';

$id = intval($id);
$return = [
    'state' => 0,
    'notice_id' => $id,
    'result' => [],
    'error' => "",
];

if (!$id) {
    $return['error'] = "no input";
} else {
    $rqt = "select niveau_biblio, typdoc from notices where notice_id='".$id."'";
    $res = pmb_mysql_query($rqt);
    if (pmb_mysql_num_rows($res)) {
        $r = pmb_mysql_fetch_assoc($res);
        $enrichment = new enrichment($r['niveau_biblio'], $r['typdoc']);

        switch ($action) {
            case "gettype":
                $typeofenrichment = $enrichment->getTypeOfEnrichment($id);
                $return["result"] = $typeofenrichment;
                break;

			default:
                if ($enrichPage) {
                    $enhance = $enrichment->getEnrichment($id, $type, $enrich_params, $enrichPage);
                } else {
                    $enhance = $enrichment->getEnrichment($id, $type, $enrich_params);
                }
                $return["result"] = $enhance;
                break;
        }

        $return["state"] = 1;
    }
}

// Plus besoin de la session
session_write_close();

//On renvoie du JSON dans le charset de PMB...
if (empty($debug)) {
    header("Content-Type:application/json; charset=$charset");
    $return = charset_pmb_normalize($return);
    print json_encode($return);
}

function charset_pmb_normalize($mixed) {
    global $charset;
    $is_array = is_array($mixed);
    $is_object = is_object($mixed);
    if($is_array || $is_object) {
        foreach($mixed as $key => $value) {
            if($is_array) {
                $mixed[$key] = charset_pmb_normalize($value);
            } else {
                $mixed->$key = charset_pmb_normalize($value);
            }
        }
    } elseif ($charset != "utf-8") {
		$mixed =encoding_normalize::utf8_normalize($mixed);	
    }
    return $mixed;
}
