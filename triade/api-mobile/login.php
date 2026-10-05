<?php
session_start();

include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");
include_once("../librairie_php/timezone.php");
$cnx=cnx();

$_SESSION=array();
session_unset();

global $gestionMDP;
$gestionMDP=GESTIONMDP;

if (isset($_POST["profil"]) and isset($_POST["nom"]) and isset($_POST["prenom"]) and isset($_POST["mdp"])){
    $profil = $_POST["profil"];
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $motdepasse = $_POST["mdp"];
} else {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["code" => "400", "message" => "Les identifiants n'ont pas été fournis"]);
    session_unset();
    session_destroy();
    die();
}

// Rate limiting : 10 tentatives max par IP en 15 minutes
$_rl_dir  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'triade_rl';
@mkdir($_rl_dir, 0700, true);
$_rl_ip   = preg_replace('/[^a-zA-Z0-9.\-]/', '_', $_SERVER['REMOTE_ADDR'] ?? 'unknown');
$_rl_file = $_rl_dir . DIRECTORY_SEPARATOR . $_rl_ip . '.json';
$_rl_now  = time();
$_rl_fp   = @fopen($_rl_file, 'c+');
if ($_rl_fp) {
    flock($_rl_fp, LOCK_EX);
    $_rl_data = json_decode(stream_get_contents($_rl_fp), true) ?? [];
    $_rl_data = array_values(array_filter($_rl_data, fn($t) => ($_rl_now - $t) < 900));
    if (count($_rl_data) >= 10) {
        flock($_rl_fp, LOCK_UN); fclose($_rl_fp);
        http_response_code(429);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(["code" => "429", "message" => "Trop de tentatives. Réessayez dans 15 minutes."]);
        session_unset(); session_destroy(); die();
    }
    ftruncate($_rl_fp, 0); rewind($_rl_fp);
    fwrite($_rl_fp, json_encode($_rl_data));
    flock($_rl_fp, LOCK_UN); fclose($_rl_fp);
}

$mode_ete = false;

$nomsPostVar=array('profil','membre','nom','nom','prenom','prenom','mdp','pwd');
$hashPostVar=hashPostVar($nomsPostVar);

$_profilToMembre = [
    '1' => 'eleve',
    '2' => 'parent',
    '3' => 'enseignant',
    '4' => 'administrateur',
    '5' => 'vie scolaire',
];
if (isset($_profilToMembre[$profil])) {
    $hashPostVar['membre'] = $_profilToMembre[$profil];
}

// Vérification comptes désactivés avant acces() car acces() bloque lui-même les comptes inactifs sans message spécifique
if (in_array($profil, ['3', '4', '5'])) {
    $nom_check   = trim(ucwords($hashPostVar['nom']));
    $prenom_check = trim($hashPostVar['prenom']);
    $nom_check_safe    = addslashes(strtolower($nom_check));
    $prenom_check_safe = addslashes(strtolower($prenom_check));
    $res_p = execSql("SELECT pers_id, offline FROM " . PREFIXE . "personnel WHERE lower(trim(nom))='" . $nom_check_safe . "' AND lower(trim(prenom))='" . $prenom_check_safe . "' LIMIT 1");
    $d_p = chargeMat($res_p);
    if (!empty($d_p) && ($d_p[0][1] == '1' || $d_p[0][1] === 't')) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(["code" => "403", "message" => "Votre compte est désactivé. Veuillez contacter l'administration de votre établissement."]);
        session_unset(); session_destroy(); die();
    }
}
if (($profil == '1') || ($profil == '2')) {
    $nom_check   = trim(ucwords($hashPostVar['nom']));
    $prenom_check = trim($hashPostVar['prenom']);
    $id_check = chercheIdEleve(strtolower($nom_check), strtolower($prenom_check));
    if (is_numeric($id_check)) {
        $res_off = execSql("SELECT compte_inactif FROM " . PREFIXE . "eleves WHERE elev_id='" . intval($id_check) . "' LIMIT 1");
        $d_off = chargeMat($res_off);
        if (!empty($d_off) && ($d_off[0][0] == '1' || $d_off[0][0] === 't')) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["code" => "403", "message" => "Votre compte est désactivé. Veuillez contacter l'administration de votre établissement."]);
            session_unset(); session_destroy(); die();
        }
    }
}

$code=acces($hashPostVar,'1');

if ($code == 1) {
    if (($profil == '1') || ($profil == '2')) {
        $nom   = trim(ucwords($hashPostVar['nom']));
        $prenom = trim($hashPostVar['prenom']);
        $id_pers = chercheIdEleve(strtolower($nom), strtolower($prenom));
        if (!is_numeric($id_pers)) {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["code" => "400", "message" => "Identifiants introuvables"]);
            session_unset(); session_destroy(); die();
        }
        $idClasse = chercheIdClasseDunEleve($id_pers);
        $code = chercherOfflineClasse($idClasse);
        if ($code == 1) { $code=0; } else { $code=1; }
    } else if (in_array($profil, ['3', '4', '5'])) {
        $nom   = trim(ucwords($hashPostVar['nom']));
        $prenom = trim($hashPostVar['prenom']);
        $labels = ['3' => 'Enseignant', '4' => 'Personnel de direction', '5' => 'Personnel vie scolaire'];
        $nom_safe    = addslashes(strtolower($nom));
        $prenom_safe = addslashes(strtolower($prenom));
        $sql  = "SELECT pers_id, offline FROM " . PREFIXE . "personnel WHERE lower(trim(nom))='" . $nom_safe . "' AND lower(trim(prenom))='" . $prenom_safe . "' LIMIT 1";
        $res  = execSql($sql);
        $data = chargeMat($res);
        $id_pers      = $data[0][0] ?? null;
        $pers_offline = $data[0][1] ?? null;
        $idClasse = 0;
        if (!is_numeric($id_pers)) {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["code" => "400", "message" => ($labels[$profil] ?? 'Personnel') . " introuvable"]);
            session_unset(); session_destroy(); die();
        }
        if ($pers_offline == '1') {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["code" => "403", "message" => "Votre compte est désactivé. Veuillez contacter l'administration de votre établissement."]);
            session_unset(); session_destroy(); die();
        }
    }
}

// test si compte blacklister
$nom=trim(ucwords($hashPostVar['nom']));
$prenom=trim($hashPostVar['prenom']);
$membre=trim($hashPostVar['membre']);
$data=verifblacklist(strtolower($nom),strtolower($prenom),strtolower($membre));
if (count($data) > 0) { $code=0; }


if ($mode_ete == true and $profil == 1){
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["code" => "403", "message" => "Vous n'êtes pas autorisé à vous connecter"]);
    session_unset();
    session_destroy();
    die();
}

if (!isset($idparent)) $idparent = '';

if ($code == 1) {

    if ($profil == 1) $membre="menueleve";
    if ($profil == 2) $membre="menuparent";
    if ($profil == 3) $membre="menuprof";
    if ($profil == 4) $membre="menuadmin";
    if ($profil == 5) $membre="menuscolaire";

    if (!is_numeric($id_pers)) {
        http_response_code(400);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(["code" => "400", "message" => "Les identifiants n'ont pas été fournis"]);
        session_unset(); session_destroy(); die();
    }

    $codebarre=recupIdCodeBar($id_pers,$membre);
    session_regenerate_id(true);
    if (isset($_rl_file)) @unlink($_rl_file);
    $id_session=session_id();
    $_SESSION["connecte"] = true;
    $_SESSION["profil"] = $profil;
    $_SESSION["nom"] = $nom;
    $_SESSION["prenom"] = $prenom;
    $_SESSION["id"] = $id_pers;
    $_SESSION["id_pers"] = $id_pers;
    $_SESSION["codebarre"] = "$codebarre";
    $_SESSION["id_session"]="$id_session";
    $_SESSION["idClasse"]="$idClasse";
    $_SESSION["membre"]="$membre";
    $_SESSION["idparent"]="$idparent";
    header('Content-Type: application/json; charset=utf-8');
    $message = ["code" => "200", "message" => "Authentification réussie", "id" => "$id_pers", "idclasse" => "$idClasse", "membre" => "$membre"];

    // A2F avec Google Authenticator
    $a2f_required = false;
    if ($membre === 'menueleve') {
        $res_ga = execSql("SELECT googleAuthenEleve FROM " . PREFIXE . "eleves WHERE elev_id='" . intval($id_pers) . "' LIMIT 1");
        if ($res_ga !== null && !DB::isError($res_ga)) {
            $d = chargeMat($res_ga);
            $a2f_required = !empty($d) && intval($d[0][0]) === 1;
        }
    } elseif ($membre === 'menuparent') {
        $res_ga = execSql("SELECT googleAuthenTuteur1 FROM " . PREFIXE . "eleves WHERE elev_id='" . intval($id_pers) . "' LIMIT 1");
        if ($res_ga !== null && !DB::isError($res_ga)) {
            $d = chargeMat($res_ga);
            $a2f_required = !empty($d) && intval($d[0][0]) === 1;
        }
    } elseif (in_array($membre, ['menuprof', 'menuadmin', 'menuscolaire'])) {
        $res_ga = execSql("SELECT googleAuthen FROM " . PREFIXE . "personnel WHERE pers_id='" . intval($id_pers) . "' LIMIT 1");
        if ($res_ga !== null && !DB::isError($res_ga)) {
            $d = chargeMat($res_ga);
            $a2f_required = !empty($d) && intval($d[0][0]) === 1;
        }
    }

    if ($a2f_required) {
        $jetonmobile = recupJetonMobile($id_pers, $membre, $idparent);
        $currentIp   = getClientIP();

        if (isset($_POST["a2f"]) && trim($_POST["a2f"]) !== '' && hash_equals((string)$jetonmobile, (string)$_POST["a2f"])) {
            if (!verifierIpMobile($id_pers, $membre, $idparent, $currentIp)) {
                // IP inconnue ou expirée (> 60 j) : réinitialise le jeton et force l'A2F
                deleteJetonMobile($id_pers, $membre, $idparent);
                $_SESSION["connecte"] = false;
                http_response_code(401);
                $message = ["code" => "401-A", "message" => "Nouvelle adresse IP détectée, veuillez re-confirmer l'A2F"];
            } else {
                // IP de confiance : renouvelle la date d'accès et connecte
                ajouterOuMajIpMobile($id_pers, $membre, $idparent, $currentIp);
                $_SESSION["connecte"] = true;
                $message = ["code" => "200", "message" => "Authentification réussie", "id" => "$id_pers", "idclasse" => "$idClasse", "membre" => "$membre"];
            }
        } else {
            $_SESSION["connecte"] = false;
            http_response_code(401);
            $message = ["code" => "401-A", "message" => "Authentification supplémentaire nécessaire"];
        }
    }
    if (!$a2f_required) {
        ajouterOuMajIpMobile($id_pers, $membre, $idparent, getClientIP());
    }
    echo json_encode($message);
} else {
    if (isset($_rl_file)) {
        $_rl_inc = @fopen($_rl_file, 'c+');
        if ($_rl_inc) {
            flock($_rl_inc, LOCK_EX);
            $_rl_d = json_decode(stream_get_contents($_rl_inc), true) ?? [];
            $_rl_d[] = time();
            ftruncate($_rl_inc, 0); rewind($_rl_inc);
            fwrite($_rl_inc, json_encode(array_values($_rl_d)));
            flock($_rl_inc, LOCK_UN); fclose($_rl_inc);
        }
    }
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["code" => "401", "message" => "Erreur de connexion"]);
    session_unset();
    session_destroy();
    die();
}
Pgclose();
?>
