<?php
/*
 * Fonctions client pour l'API centrale TRIADE-COPILOT (triade-educ.org/ia/api.php)
 */

define('IA_API_URL', 'https://triade-educ.org/ia/api.php');
define('IA_API_KEY', 'ia-2026-xJ8kPmR5');

function _iaCurl($url, $post = null) {
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_FOLLOWLOCATION => true,
    ));
    if ($post !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    $json = curl_exec($ch);
    curl_close($ch);
    return $json ?: null;
}

function getIABalance($iakey) {
    $json = _iaCurl(IA_API_URL . '?action=get_balance&key=' . urlencode(IA_API_KEY) . '&iakey=' . urlencode($iakey));
    if (!$json) return null;
    $r = json_decode($json, true);
    return (is_array($r) && isset($r['nbtokencredit'])) ? $r : null;
}

function getIAInfo($iakey) {
    $json = _iaCurl(IA_API_URL . '?action=get_info&key=' . urlencode(IA_API_KEY) . '&iakey=' . urlencode($iakey));
    if (!$json) return null;
    $r = json_decode($json, true);
    return (is_array($r) && !empty($r['found'])) ? $r : null;
}

function updateIAInfo($iakey, $fields) {
    $post = array_merge($fields, array('action' => 'update_info', 'key' => IA_API_KEY, 'iakey' => $iakey));
    $json = _iaCurl(IA_API_URL, $post);
    if (!$json) return false;
    $r = json_decode($json, true);
    return is_array($r) && !empty($r['ok']);
}

function getIATarifs() {
    $json = _iaCurl(IA_API_URL . '?action=get_tarifs&key=' . urlencode(IA_API_KEY));
    if (!$json) return null;
    $r = json_decode($json, true);
    return (is_array($r) && !empty($r['ok'])) ? $r : null;
}
