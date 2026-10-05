<?php
/*
 * Fonctions client pour l'API centrale Triade-Visio (triade-educ.org/visio/api.php)
 */

define('VISIO_API_URL', 'https://triade-educ.org/visio/api.php');
define('VISIO_API_KEY', 'tv-2026-xK9mQpR4');

// Clé unique de l'établissement (common/config_key.php, auto-générée si absente)
$_visioCfgKey = __DIR__ . '/../common/config_key.php';
if (!file_exists($_visioCfgKey)) {
    $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0,0xffff), mt_rand(0,0xffff), mt_rand(0,0xffff),
        mt_rand(0,0x0fff)|0x4000, mt_rand(0,0x3fff)|0x8000,
        mt_rand(0,0xffff), mt_rand(0,0xffff), mt_rand(0,0xffff));
    file_put_contents($_visioCfgKey,
        "<?php\ndefine('KEY_CODE_ECOLE', '" . $uuid . "');\n");
}
if (!defined('KEY_CODE_ECOLE')) {
    include_once($_visioCfgKey);
}
unset($_visioCfgKey, $uuid);

function _visioCodeEcole() {
    if (defined('KEY_CODE_ECOLE')) return KEY_CODE_ECOLE;
    if (defined('REPECOLE'))       return REPECOLE;
    if (defined('REPECOLE_VATEL')) return REPECOLE_VATEL;
    return 'triade';
}

function _visioCurl($url, $post = null) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    if ($post !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    $json = curl_exec($ch);
    curl_close($ch);
    return $json ?: null;
}

function _visioGet($action) {
    $url = VISIO_API_URL . '?action=' . urlencode($action)
         . '&ecole=' . urlencode(_visioCodeEcole())
         . '&key='   . urlencode(VISIO_API_KEY);
    $json = _visioCurl($url);
    if (!$json) return null;
    return json_decode($json, true);
}

function _visioPost($action, $data) {
    $data['action'] = $action;
    $data['ecole']  = _visioCodeEcole();
    $data['key']    = VISIO_API_KEY;
    $json = _visioCurl(VISIO_API_URL, $data);
    if (!$json) return false;
    $r = json_decode($json, true);
    return !empty($r['ok']);
}

function getAbonnementVisio() {
    $r = _visioGet('get');
    if (!$r || empty($r['plan'])) return null;
    return [
        'plan'                => $r['plan'],
        'statut'              => $r['statut']              ?? 'inactif',
        'date_debut'          => $r['date_debut']          ?? null,
        'date_fin'            => $r['date_fin']            ?? null,
        'prix_mois'           => $r['prix_mois']           ?? 0,
        'nb_max_participants' => intval($r['nb_max_participants'] ?? 6),
        'nb_max_salles'       => intval($r['nb_max_salles']      ?? 2),
        'contact_facturation' => $r['contact_facturation'] ?? '',
        'notes'               => $r['notes']               ?? '',
    ];
}

function verifAbonnementVisio() {
    $r = _visioGet('check');
    if (!empty($r['actif'])) return true;
    // Fallback : si check retourne false mais que le statut brut indique actif/actif-resi
    $abo = getAbonnementVisio();
    if ($abo && in_array($abo['statut'], array('actif', 'actif-resi'))) {
        $fin = $abo['date_fin'];
        if (!$fin || $fin >= date('Y-m-d')) return true;
    }
    return false;
}

function getAbonnementVisioLimites() {
    $r = _visioGet('check');
    return [
        'actif'               => !empty($r['actif']),
        'nb_max_participants' => intval($r['nb_max_participants'] ?? 6),
        'nb_max_salles'       => intval($r['nb_max_salles']      ?? 2),
    ];
}

function sauvegarderAbonnement($plan, $statut, $date_debut, $date_fin, $prix_mois,
                               $nb_max_participants, $nb_max_salles, $contact, $notes) {
    $err = '';
    return sauvegarderAbonnementDetail($plan, $statut, $date_debut, $date_fin, $prix_mois,
                                       $nb_max_participants, $nb_max_salles, $contact, $notes, $err);
}

function sauvegarderAbonnementDetail($plan, $statut, $date_debut, $date_fin, $prix_mois,
                                     $nb_max_participants, $nb_max_salles, $contact, $notes, &$errMsg) {
    $data = [
        'plan'                => $plan,
        'statut'              => $statut,
        'date_debut'          => $date_debut,
        'date_fin'            => $date_fin,
        'prix_mois'           => $prix_mois,
        'nb_max_participants' => $nb_max_participants,
        'nb_max_salles'       => $nb_max_salles,
        'contact'             => $contact,
        'notes'               => $notes,
    ];
    $data['action'] = 'save';
    $data['ecole']  = _visioCodeEcole();
    $data['key']    = VISIO_API_KEY;

    $ch = curl_init(VISIO_API_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($data),
    ]);
    $json  = curl_exec($ch);
    $errno = curl_errno($ch);
    $errMsg = $errno ? curl_error($ch) : '';
    curl_close($ch);

    if (!$json) return false;
    $r = json_decode($json, true);
    if (!empty($r['erreur'])) { $errMsg = $r['erreur']; return false; }
    return !empty($r['ok']);
}
