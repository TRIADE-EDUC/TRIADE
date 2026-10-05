<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *   Module de sécurité et d'authentification administrateur TRIADE
 *   Compatible PHP 7.4 et PHP 8.3
 ***************************************************************************/

if (!function_exists('admin_get_ip')) {
    function admin_get_ip() {
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}

/**
 * Journalise une action de sécurité admin dans access.log
 */
function admin_log_security_event($action, $statut, $details = '') {
    $ip = admin_get_ip();
    $date = date('Y-m-d H:i:s');
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown UA';
    $logDir = __DIR__ . '/../../data/install_log';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    $logFile = $logDir . '/access.log';
    $line = sprintf("[%s] [ADMIN_AUTH] [%s] [STATUT:%s] [IP:%s] [UA:%s] %s\n", $date, $action, $statut, $ip, $ua, $details);
    @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}

/**
 * Vérifie le mot de passe administrateur
 * Supporte :
 * 1. Hash moderne password_hash() (BCRYPT / Argon2 / SHA-512)
 * 2. Hash historique DES Unix (T2...)
 * 3. Mot de passe en clair (posé par FTP)
 */
function admin_verify_password($inputPassword, $storedPassword) {
    if ($inputPassword === null || $storedPassword === null) {
        return false;
    }
    $inputPassword = (string)$inputPassword;
    $storedPassword = (string)$storedPassword;

    // 1. Format password_hash() moderne ($2y$, $argon2, $6$, etc.)
    if (strpos($storedPassword, '$') === 0) {
        return password_verify($inputPassword, $storedPassword);
    }

    // 2. Format historique DES Unix avec salt 'T2' et md5 préalable
    if (strpos($storedPassword, 'T2') === 0) {
        $legacyHash = crypt(md5($inputPassword), "T2");
        if (hash_equals($storedPassword, $legacyHash)) {
            return true;
        }
    }

    // 3. Cas de secours : mot de passe en clair déposé par FTP
    if (hash_equals($storedPassword, $inputPassword)) {
        return true;
    }

    return false;
}

/**
 * Détermine si le mot de passe stocké doit être ré-encodé vers password_hash()
 */
function admin_password_needs_rehash($storedPassword) {
    $storedPassword = (string)$storedPassword;
    if (strpos($storedPassword, '$') !== 0) {
        return true;
    }
    return password_needs_rehash($storedPassword, PASSWORD_DEFAULT);
}

/**
 * Sauvegarde les identifiants administrateur dans common/lib_acces_inc.php
 */
function admin_save_credentials($login, $passwordHash, $pinHash = null) {
    $filePath = __DIR__ . '/../../common/lib_acces_inc.php';
    
    $loginEsc = addcslashes($login, "'\\");
    $passEsc  = addcslashes($passwordHash, "'\\");
    
    $content = "<?php\n";
    $content .= "\$LOGIN = '$loginEsc';\n";
    $content .= "\$PASSWORD = '$passEsc';\n";
    if ($pinHash !== null && $pinHash !== '') {
        $pinEsc = addcslashes($pinHash, "'\\");
        $content .= "\$ADMIN_PIN = '$pinEsc';\n";
    }
    $content .= "?>\n";

    $res = @file_put_contents($filePath, $content, LOCK_EX);
    return ($res !== false);
}

/**
 * Vérifie le code PIN administrateur (6 chiffres)
 */
function admin_verify_pin($inputPin, $storedPinHash) {
    if (empty($storedPinHash)) {
        return false;
    }
    $inputPin = (string)$inputPin;
    $storedPinHash = (string)$storedPinHash;

    if (!preg_match('/^[0-9]{6}$/', $inputPin)) {
        return false;
    }

    // Format password_hash()
    if (strpos($storedPinHash, '$') === 0) {
        return password_verify($inputPin, $storedPinHash);
    }

    // Cas de secours : PIN en clair déposé par FTP
    return hash_equals($storedPinHash, $inputPin);
}

/**
 * Chemin vers le fichier de rate limiting (verrouillage force brute)
 */
function admin_get_lock_file_path() {
    $dir = __DIR__ . '/../../data/securite';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
        @file_put_contents($dir . '/.htaccess', "Require all denied\n");
    }
    return $dir . '/admin_rate_limit.json';
}

/**
 * Vérifie l'état du Rate Limiting pour l'IP courante
 * Max 5 tentatives infructueuses, verrouillage 15 minutes (900s)
 */
function admin_check_rate_limit($ip = null) {
    if ($ip === null) $ip = admin_get_ip();
    $lockFile = admin_get_lock_file_path();
    
    if (!file_exists($lockFile)) {
        return array('blocked' => false, 'remaining_seconds' => 0, 'attempts' => 0);
    }

    $data = @json_decode(@file_get_contents($lockFile), true);
    if (!is_array($data) || !isset($data[$ip])) {
        return array('blocked' => false, 'remaining_seconds' => 0, 'attempts' => 0);
    }

    $entry = $data[$ip];
    $now = time();

    // Vérifier si un blocage est actif
    if (isset($entry['blocked_until']) && $entry['blocked_until'] > $now) {
        $remaining = $entry['blocked_until'] - $now;
        return array('blocked' => true, 'remaining_seconds' => $remaining, 'attempts' => $entry['attempts'] ?? 5);
    }

    // Si la période de blocage ou d'inactivité est passée (> 15 min), réinitialiser
    if (isset($entry['last_attempt']) && ($now - $entry['last_attempt']) > 900) {
        unset($data[$ip]);
        @file_put_contents($lockFile, json_encode($data), LOCK_EX);
        return array('blocked' => false, 'remaining_seconds' => 0, 'attempts' => 0);
    }

    $attempts = $entry['attempts'] ?? 0;
    return array('blocked' => false, 'remaining_seconds' => 0, 'attempts' => $attempts);
}

/**
 * Enregistre une tentative d'authentification admin infructueuse
 */
function admin_record_failed_attempt($ip = null, $reason = '') {
    if ($ip === null) $ip = admin_get_ip();
    $lockFile = admin_get_lock_file_path();
    $data = array();
    if (file_exists($lockFile)) {
        $data = @json_decode(@file_get_contents($lockFile), true);
        if (!is_array($data)) $data = array();
    }

    $now = time();
    $entry = $data[$ip] ?? array('attempts' => 0, 'last_attempt' => $now);
    
    // Si la dernière tentative date de plus de 15 min, on repart à zéro
    if (isset($entry['last_attempt']) && ($now - $entry['last_attempt']) > 900) {
        $entry['attempts'] = 0;
    }

    $entry['attempts'] = ($entry['attempts'] ?? 0) + 1;
    $entry['last_attempt'] = $now;

    $isBlocked = false;
    if ($entry['attempts'] >= 5) {
        $entry['blocked_until'] = $now + 900; // 15 minutes
        $isBlocked = true;
        admin_log_security_event('LOCKOUT', 'BLOCKED', "IP $ip bloquée 15 min suite à 5 tentatives infructueuses ($reason)");
    } else {
        admin_log_security_event('LOGIN_FAIL', 'FAILED', "Tentative $entry[attempts]/5 échouée pour IP $ip ($reason)");
    }

    $data[$ip] = $entry;
    @file_put_contents($lockFile, json_encode($data), LOCK_EX);

    return array('blocked' => $isBlocked, 'remaining_seconds' => ($isBlocked ? 900 : 0), 'attempts' => $entry['attempts']);
}

/**
 * Réinitialise le compteur d'échecs après une connexion réussie
 */
function admin_reset_rate_limit($ip = null) {
    if ($ip === null) $ip = admin_get_ip();
    $lockFile = admin_get_lock_file_path();
    if (file_exists($lockFile)) {
        $data = @json_decode(@file_get_contents($lockFile), true);
        if (is_array($data) && isset($data[$ip])) {
            unset($data[$ip]);
            @file_put_contents($lockFile, json_encode($data), LOCK_EX);
        }
    }
}

/**
 * Chemin vers le fichier des appareils de confiance
 */
function admin_get_trusted_devices_file_path() {
    $dir = __DIR__ . '/../../data/securite';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
        @file_put_contents($dir . '/.htaccess', "Require all denied\n");
    }
    return $dir . '/admin_trusted_devices.php';
}

/**
 * Charge les appareils de confiance enregistrés
 */
function admin_load_trusted_devices() {
    $file = admin_get_trusted_devices_file_path();
    if (!file_exists($file)) {
        return array();
    }
    $devices = array();
    include($file);
    return is_array($devices) ? $devices : array();
}

/**
 * Sauvegarde la liste des appareils de confiance
 */
function admin_save_trusted_devices($devices) {
    $file = admin_get_trusted_devices_file_path();
    $export = var_export($devices, true);
    $content = "<?php\n// Fichier sécurisé des appareils de confiance admin TRIADE\n\$devices = " . $export . ";\n?>";
    @file_put_contents($file, $content, LOCK_EX);
}

/**
 * Vérifie si le client actuel possède un cookie d'appareil de confiance valide (30 jours)
 */
function admin_verify_trusted_device() {
    $cookieName = 'triade_admin_device';
    if (!isset($_COOKIE[$cookieName]) || empty($_COOKIE[$cookieName])) {
        return false;
    }

    $rawCookie = $_COOKIE[$cookieName];
    $parts = explode(':', $rawCookie, 2);
    if (count($parts) !== 2) {
        return false;
    }

    $selector = $parts[0];
    $validator = $parts[1];
    $validatorHash = hash('sha256', $validator);

    $devices = admin_load_trusted_devices();
    if (!isset($devices[$selector])) {
        return false;
    }

    $device = $devices[$selector];
    $now = time();

    // Vérifier l'expiration (30 jours)
    if (!isset($device['expires']) || $device['expires'] < $now) {
        unset($devices[$selector]);
        admin_save_trusted_devices($devices);
        return false;
    }

    // Vérifier le hash du jeton
    if (!hash_equals($device['token_hash'], $validatorHash)) {
        return false;
    }

    return true;
}

/**
 * Crée un jeton d'appareil de confiance (durée 30 jours) et dépose le cookie sécurisé
 */
function admin_create_trusted_device() {
    $selector = bin2hex(random_bytes(16));
    $validator = bin2hex(random_bytes(32));
    $validatorHash = hash('sha256', $validator);
    
    $expires = time() + (30 * 86400); // 30 jours

    $devices = admin_load_trusted_devices();
    
    // Nettoyer les vieux tokens expirés
    $now = time();
    foreach ($devices as $sel => $d) {
        if (!isset($d['expires']) || $d['expires'] < $now) {
            unset($devices[$sel]);
        }
    }

    $devices[$selector] = array(
        'token_hash' => $validatorHash,
        'created_at' => $now,
        'expires'    => $expires,
        'ip'         => admin_get_ip(),
        'ua'         => $_SERVER['HTTP_USER_AGENT'] ?? ''
    );

    admin_save_trusted_devices($devices);

    $cookieValue = $selector . ':' . $validator;
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 0) == 443;

    // Cookie compatible PHP 7.4 et 8.3
    if (PHP_VERSION_ID >= 70300) {
        setcookie('triade_admin_device', $cookieValue, array(
            'expires'  => $expires,
            'path'     => '/',
            'secure'   => $isHttps,
            'httponly' => true,
            'samesite' => 'Strict'
        ));
    } else {
        setcookie('triade_admin_device', $cookieValue, $expires, '/; samesite=Strict', '', $isHttps, true);
    }

    admin_log_security_event('TRUSTED_DEVICE', 'CREATED', "Nouvel appareil mémorisé pour 30 jours (sélecteur: $selector)");
}

/**
 * Révoque tous les appareils de confiance
 */
function admin_revoke_all_trusted_devices() {
    admin_save_trusted_devices(array());
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 0) == 443;
    if (PHP_VERSION_ID >= 70300) {
        setcookie('triade_admin_device', '', array(
            'expires'  => time() - 3600,
            'path'     => '/',
            'secure'   => $isHttps,
            'httponly' => true,
            'samesite' => 'Strict'
        ));
    } else {
        setcookie('triade_admin_device', '', time() - 3600, '/; samesite=Strict', '', $isHttps, true);
    }
    admin_log_security_event('TRUSTED_DEVICE', 'REVOKED_ALL', 'Tous les appareils de confiance ont été révoqués');
}
