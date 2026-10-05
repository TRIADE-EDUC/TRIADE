<?php
define('LK_CENTRAL_API', 'https://www.triade-educ.org/visio/lk_api.php');

if (!function_exists('_visioCodeEcole')) {
    include_once(__DIR__ . '/../librairie_php/db_visio.php');
}

function _lkPost($params) {
    if (!function_exists('curl_init')) return array();
    $params['key']   = VISIO_API_KEY;
    $params['ecole'] = _visioCodeEcole();
    $ch = curl_init(LK_CENTRAL_API);
    curl_setopt($ch, CURLOPT_POST,           1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,     http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,        3);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $res = curl_exec($ch);
    curl_close($ch);
    $d = json_decode($res, true);
    return is_array($d) ? $d : array();
}

function lkEnabled() {
    if (isset($_SESSION['_lk_enabled'])) return $_SESSION['_lk_enabled'] === '1';
    $r = _lkPost(array('action' => 'status'));
    $v = (!empty($r['enabled']) && $r['enabled'] === '1') ? '1' : '0';
    $_SESSION['_lk_enabled'] = $v;
    return $v === '1';
}

function lkToken($room, $identity, $nom = '') {
    $r = _lkPost(array(
        'action'   => 'token',
        'room'     => $room,
        'identity' => $identity,
        'nom'      => $nom
    ));
    return isset($r['token']) ? $r['token'] : '';
}

function lkWssUrl() {
    $r = _lkPost(array('action' => 'status'));
    $host = isset($r['host']) ? $r['host'] : '';
    if (!$host) return '';
    return 'wss://' . preg_replace('/^wss?:\/\//', '', $host);
}

function visioEngine($maxPart) {
    if ($maxPart <= 6) return 'mesh';
    if (!lkEnabled()) return 'mesh';
    return 'livekit';
}
