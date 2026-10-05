<?php
/**
 * Auto-initialise le compte MSN depuis la session TRIADE.
 * Inclure APRÈS config.php (connexion $conn déjà établie).
 */
if (isset($_SESSION['unique_id'])) {
    // Déjà en session : mettre à jour id_pers/membre si manquants (migration comptes existants)
    $_idPers = intval($_SESSION['id_pers'] ?? 0);
    $_membre = mysqli_real_escape_string($conn, $_SESSION['membre'] ?? '');
    if ($_idPers > 0 && $_membre !== '') {
        $uid = intval($_SESSION['unique_id']);
        mysqli_query($conn, "UPDATE ".PREFIXE."users SET id_pers='$_idPers', membre='$_membre' WHERE unique_id='$uid' AND (id_pers IS NULL OR id_pers=0)");
    }
    return;
}

$_idPers = intval($_SESSION['id_pers']  ?? 0);
$_membre = $_SESSION['membre']  ?? '';
$_prenom = trim($_SESSION['prenom'] ?? '');
$_nom    = trim($_SESSION['nom']    ?? '');

if (!$_idPers || !$_membre || !$_prenom || !$_nom) {
    $_SESSION['msn_debug'] = 'ABORT : variables manquantes — id_pers=' . $_idPers
        . ' membre=' . $_membre . ' prenom=' . $_prenom . ' nom=' . $_nom;
    return;
}

$_email    = $_idPers . '_' . $_membre . '@msn.triade';
$_emailDb  = mysqli_real_escape_string($conn, $_email);
$_membreDb = mysqli_real_escape_string($conn, $_membre);
$_prenomDb = mysqli_real_escape_string($conn, $_prenom);
$_nomDb    = mysqli_real_escape_string($conn, $_nom);

// 1. Recherche par id_pers + membre (identifiant sûr et unique)
$_q = mysqli_query($conn, "SELECT unique_id FROM ".PREFIXE."users WHERE id_pers='$_idPers' AND membre='$_membreDb' LIMIT 1");
if ($_q && mysqli_num_rows($_q) > 0) {
    $_uid = mysqli_fetch_assoc($_q)['unique_id'];
    mysqli_query($conn, "UPDATE ".PREFIXE."users SET email='$_emailDb' WHERE unique_id='$_uid'");
    $_SESSION['unique_id'] = $_uid;
    $_SESSION['msn_debug'] = 'CHEMIN 1 (id_pers+membre trouvé) → unique_id=' . $_uid;
    return;
}

// 2. Backward compat : compte existant avec email @msn.triade
$_q2 = mysqli_query($conn, "SELECT unique_id FROM ".PREFIXE."users WHERE email='$_emailDb' LIMIT 1");
if ($_q2 && mysqli_num_rows($_q2) > 0) {
    $_uid = mysqli_fetch_assoc($_q2)['unique_id'];
    mysqli_query($conn, "UPDATE ".PREFIXE."users SET id_pers='$_idPers', membre='$_membreDb' WHERE unique_id='$_uid'");
    $_SESSION['unique_id'] = $_uid;
    $_SESSION['msn_debug'] = 'CHEMIN 2 (email msn.triade trouvé) → unique_id=' . $_uid;
    return;
}

// 3. Création automatique
$_now = time();
$_uid = abs(crc32($_email)) % 9000000 + 1000000;
$_chk = mysqli_query($conn, "SELECT unique_id FROM ".PREFIXE."users WHERE unique_id='$_uid'");
if ($_chk && mysqli_num_rows($_chk) > 0) {
    $_uid = rand(10000000, 99999999);
    $_SESSION['msn_debug'] = 'CHEMIN 3 (CRC32 collision → rand) → unique_id=' . $_uid;
} else {
    $_SESSION['msn_debug'] = 'CHEMIN 3 (nouveau compte CRC32) → unique_id=' . $_uid;
}
mysqli_query($conn, "INSERT INTO ".PREFIXE."users
    (unique_id, fname, lname, email, password, img, status, update_sync, id_pers, membre)
    VALUES ('$_uid', '$_prenomDb', '$_nomDb', '$_emailDb', '', 'default.png', 'Offline now', '$_now', '$_idPers', '$_membreDb')");
$_SESSION['unique_id'] = $_uid;
