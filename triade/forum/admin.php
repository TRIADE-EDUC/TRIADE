<html>
<head>
<title>Administration du forum Triade</title>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4-2.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css">
<script language="JavaScript" src="../librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="../librairie_js/function.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<style>
* { box-sizing: border-box; }
body { background: #f0f2fa; margin: 0; padding: 16px; font-family: Electrolize, Arial, sans-serif; font-size: 13px; }
.fa-wrap { max-width: 720px; margin: 0 auto; display: flex; flex-direction: column; gap: 10px; }
.fa-title { font-size: 15px; font-weight: 700; color: #080A66; margin-bottom: 4px; }
.fa-sub { font-size: 12px; color: #666; }
.fa-error { color: #c62828; font-weight: 700; font-size: 12px; margin: 6px 0; }
.fa-success { color: #2e7d32; font-weight: 700; font-size: 12px; margin: 6px 0; }
.fa-btn-row { display: flex; gap: 8px; margin-top: 12px; flex-wrap: wrap; }
.fa-msg-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.fa-msg-table th { background: #f0f2fa; color: #080A66; font-weight: 700; padding: 7px 10px; border-bottom: 2px solid #dde0f0; text-align: left; }
.fa-msg-table td { padding: 6px 10px; border-bottom: 1px solid #eef0f8; vertical-align: middle; }
.fa-msg-table tr:hover td { background: #f5f7ff; }
.fa-msg-id { font-weight: 700; color: #080A66; text-align: center; }
.fa-indent { display: inline-block; }
</style>
</head>
<body>

<?php

error_reporting(0);

global $repForum;

if (isset($_GET["repforum"]))  $repForum = $_GET["repforum"];
if (isset($_POST["repforum"])) $repForum = $_POST["repforum"];

if (!file_exists("../data/forum")) {
    @mkdir("../data/forum", 0755);
    $text = "<Files \"*\">\nOrder Deny,Allow\nDeny from all\n</Files>";
    $fp = fopen("../data/forum/.htaccess", "w"); fwrite($fp, $text); fclose($fp);
}

$reperForum = "../data/forum/$repForum";

if (!file_exists($reperForum)) {
    @mkdir("$reperForum", 0755);
    $text = "<Files \"*\">\nOrder Deny,Allow\nDeny from all\n</Files>";
    $fp = fopen("${reperForum}/.htaccess", "w"); fwrite($fp, $text); fclose($fp);
}

if (!file_exists("${reperForum}/index.dat")) {
    $crfic = fopen("${reperForum}/index.dat", "w+");
    fputs($crfic, "Fichier Index. Ne pas éditer !");
    fclose($crfic);
}

$mdputil    = isset($_POST["mdputil"])    ? $_POST["mdputil"]    : "";
$idaction   = isset($_POST["idaction"])   ? $_POST["idaction"]   : "";
$pass       = isset($_POST["pass"])       ? $_POST["pass"]       : "";
$idmsgsup   = isset($_POST["idmsgsup"])   ? $_POST["idmsgsup"]   : "";
$rangsupmin = isset($_POST["rangsupmin"]) ? $_POST["rangsupmin"] : "";
$rangsupmax = isset($_POST["rangsupmax"]) ? $_POST["rangsupmax"] : "";

function ROT13($chaine) {
    $chaine = strtolower($chaine);
    $chainecod = "";
    for ($compt = 0; $compt < strlen($chaine); $compt++) {
        $codecaract1 = ord(substr($chaine, $compt, 1));
        if ($codecaract1 >= 97 && $codecaract1 <= 122)
            $codecaract2 = ($codecaract1 <= 109) ? $codecaract1 + 13 : $codecaract1 - 13;
        else
            $codecaract2 = $codecaract1;
        $chainecod .= chr($codecaract2);
    }
    return $chainecod;
}

function tabulation($n = 1) {
    return (30 * ($n - 1) + 20);
}

function hiddenPassRepforum($pass, $repForum) {
    return "<input type='hidden' name='pass' value='$pass'><input type='hidden' name='repforum' value='$repForum'>";
}

function renderMsgTable($index, $from, $to) {
    echo "<table class='fa-msg-table'>";
    echo "<thead><tr><th style='width:60px;text-align:center;'>N°</th><th>Intitulé du message</th></tr></thead><tbody>";
    for ($c = $from; $c <= $to; $c++) {
        $prefix = ($index[$c][2] == 1) ? '<span style="color:#080A66;font-weight:700;">#</span>' : '<span style="color:#888;">&rsaquo;</span>';
        $indent = tabulation($index[$c][2] - 1);
        echo "<tr>";
        echo "<td class='fa-msg-id'>" . $index[$c][1] . "</td>";
        echo "<td><span class='fa-indent' style='width:{$indent}px;'></span> {$prefix} <strong>" . stripslashes(htmlentities(strip_tags($index[$c][5]))) . "</strong> &mdash; " . stripslashes(htmlentities(strip_tags($index[$c][4]))) . " <span style='color:#888;'>(" . $index[$c][3] . ")</span></td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
}

if (file_exists("../common/mdep.php")) include("../common/mdep.php");

$forumLabels = [
    'menuadmin'     => 'Direction',
    'menuscolaire'  => 'Vie Scolaire',
    'menuprof'      => 'Enseignant',
    'menueleve'     => 'Élève',
    'menuparent'    => 'Parent d\'élève',
];
$forumLabel = $forumLabels[$repForum] ?? $repForum;
?>

<div class="fa-wrap">

<!-- Header -->
<div class="card">
  <div class="card-header card-header-primary">
    <span><i class="bi bi-chat-dots-fill" style="margin-right:6px;"></i>Administration du forum — <?php print htmlspecialchars($forumLabel) ?></span>
  </div>
</div>

<?php

// ============================================================
// MODULE : idaction = "" (premier lancement)
// ============================================================
if ($idaction == "") {
    if (!file_exists("../common/mdep.php")): ?>
    <div class="card">
      <div class="card-header card-header-primary"><span><i class="bi bi-lock-fill" style="margin-right:6px;"></i>Définir le mot de passe administrateur</span></div>
      <div class="card-body">
        <p class="fa-sub" style="margin:0 0 12px;">Ce mot de passe vous sera demandé à chaque connexion pour gérer les messages.</p>
        <form method="POST" action="admin.php">
          <div class="form-row">
            <label class="form-lbl">Mot de passe :</label>
            <input type="password" name="mdputil1" size="20" class="bouton2">
          </div>
          <div class="form-row" style="margin-top:6px;">
            <label class="form-lbl">Confirmation :</label>
            <input type="password" name="mdputil2" size="20" class="bouton2">
          </div>
          <input type="hidden" name="idaction" value="testChoixMDP">
          <?php print hiddenPassRepforum($pass, $repForum) ?>
          <div class="fa-btn-row">
            <button type="submit" name="A1" class="btn btn-primary">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
    <?php else: ?>
    <div class="card">
      <div class="card-header card-header-primary"><span><i class="bi bi-shield-lock-fill" style="margin-right:6px;"></i>Identification</span></div>
      <div class="card-body">
        <form method="POST" action="admin.php">
          <div class="form-row">
            <label class="form-lbl">Mot de passe :</label>
            <input type="password" name="mdputil" size="20" class="bouton2" autofocus>
          </div>
          <input type="hidden" name="idaction" value="verifMDP">
          <?php print hiddenPassRepforum($pass, $repForum) ?>
          <div class="fa-btn-row">
            <button type="submit" name="A1" class="btn btn-primary">Se connecter</button>
          </div>
        </form>
      </div>
    </div>
    <?php endif;
}

// ============================================================
// MODULE : testChoixMDP
// ============================================================
if ($idaction == "testChoixMDP") {
    $mdputil1 = strtolower($_POST["mdputil1"] ?? "");
    $mdputil2 = strtolower($_POST["mdputil2"] ?? "");

    if (file_exists("../common/mdep.php") && $pass != ROT13($MDP)) {
        echo "<div class='card'><div class='card-body'><p class='fa-error'><i class='bi bi-exclamation-triangle-fill'></i> Erreur : <a href='admin.php?repforum={$repForum}'>identifiez-vous</a> à nouveau.</p></div></div>";
    } elseif ($mdputil1 == "") { ?>
    <div class="card">
      <div class="card-body">
        <p class="fa-error"><i class="bi bi-exclamation-triangle-fill"></i> Veuillez saisir un mot de passe.</p>
        <form method="POST" action="admin.php">
          <div class="form-row"><label class="form-lbl">Mot de passe :</label><input type="password" name="mdputil1" size="20" class="bouton2"></div>
          <div class="form-row" style="margin-top:6px;"><label class="form-lbl">Confirmation :</label><input type="password" name="mdputil2" size="20" class="bouton2"></div>
          <input type="hidden" name="idaction" value="testChoixMDP">
          <?php print hiddenPassRepforum($pass, $repForum) ?>
          <div class="fa-btn-row"><button type="submit" name="A1" class="btn btn-primary">Enregistrer</button></div>
        </form>
      </div>
    </div>
    <?php } elseif ($mdputil1 != $mdputil2) { ?>
    <div class="card">
      <div class="card-body">
        <p class="fa-error"><i class="bi bi-exclamation-triangle-fill"></i> Les deux mots de passe ne correspondent pas.</p>
        <form method="POST" action="admin.php">
          <div class="form-row"><label class="form-lbl">Mot de passe :</label><input type="password" name="mdputil1" size="20" class="bouton2"></div>
          <div class="form-row" style="margin-top:6px;"><label class="form-lbl">Confirmation :</label><input type="password" name="mdputil2" size="20" class="bouton2"></div>
          <input type="hidden" name="idaction" value="testChoixMDP">
          <?php print hiddenPassRepforum($pass, $repForum) ?>
          <div class="fa-btn-row"><button type="submit" name="A1" class="btn btn-primary">Enregistrer</button></div>
        </form>
      </div>
    </div>
    <?php } else {
        $ficmdep = fopen("../common/mdep.php", "w+");
        fputs($ficmdep, "<?php \n\$MDP=\"$mdputil1\"; \n?>");
        fclose($ficmdep);
        $MDP     = $mdputil1;
        $MDPcode = ROT13($MDP); ?>
    <div class="card">
      <div class="card-body">
        <p class="fa-success"><i class="bi bi-check-circle-fill"></i> Mot de passe enregistré.</p>
        <form method="POST" action="admin.php">
          <input type="hidden" name="idaction" value="menuGen">
          <input type="hidden" name="pass" value="<?php print $MDPcode ?>">
          <input type="hidden" name="repforum" value="<?php print $repForum ?>">
          <div class="fa-btn-row"><button type="submit" name="A1" class="btn btn-primary">Accéder au menu</button></div>
        </form>
      </div>
    </div>
    <?php }
}

// ============================================================
// MODULE : verifMDP
// ============================================================
if ($idaction == "verifMDP") {
    $mdputil = strtolower($_POST["mdputil"] ?? "");
    if (crypt(md5($mdputil), "T2") != $MDP) { ?>
    <div class="card">
      <div class="card-body">
        <p class="fa-error"><i class="bi bi-exclamation-triangle-fill"></i> Mot de passe incorrect.</p>
        <form method="POST" action="admin.php">
          <div class="form-row"><label class="form-lbl">Mot de passe :</label><input type="password" name="mdputil" size="20" class="bouton2" autofocus></div>
          <input type="hidden" name="idaction" value="verifMDP">
          <?php print hiddenPassRepforum($pass, $repForum) ?>
          <div class="fa-btn-row"><button type="submit" name="A1" class="btn btn-primary">Se connecter</button></div>
        </form>
      </div>
    </div>
    <?php } else {
        $idaction = "menuGen";
        $MDPcode  = ROT13($MDP);
        $pass     = $MDPcode;
    }
}

// ============================================================
// MODULE : menuGen
// ============================================================
if ($idaction == "menuGen") {
    if ($pass != ROT13($MDP)) {
        echo "<div class='card'><div class='card-body'><p class='fa-error'><i class='bi bi-exclamation-triangle-fill'></i> Session expirée. <a href='admin.php?repforum={$repForum}'>Se reconnecter</a></p></div></div>";
    } else { ?>
    <div class="card">
      <div class="card-header card-header-primary"><span><i class="bi bi-grid-fill" style="margin-right:6px;"></i>Menu général</span></div>
      <div class="card-body">
        <div class="fa-btn-row">
          <form method="POST" action="admin.php" style="margin:0;">
            <input type="hidden" name="idaction" value="menuSupMsgs">
            <?php print hiddenPassRepforum($pass, $repForum) ?>
            <button type="submit" name="A1" class="btn btn-primary"><i class="bi bi-trash3" style="margin-right:5px;"></i>Supprimer des messages</button>
          </form>
          <button type="button" class="btn btn-secondary" onclick="parent.window.close()"><i class="bi bi-box-arrow-right" style="margin-right:5px;"></i>Quitter</button>
        </div>
      </div>
    </div>
    <?php }
}

// ============================================================
// MODULE : menuSupMsgs
// ============================================================
if ($idaction == "menuSupMsgs") {
    if ($pass != ROT13($MDP)) {
        echo "<div class='card'><div class='card-body'><p class='fa-error'><i class='bi bi-exclamation-triangle-fill'></i> Session expirée. <a href='admin.php?repforum={$repForum}'>Se reconnecter</a></p></div></div>";
    } else {
        $tabindex   = file("${reperForum}/index.dat");
        $nombremsgs = count($tabindex) - 1;
        for ($compt = 1; $compt <= $nombremsgs; $compt++) {
            $index[$compt][1] = strtok($tabindex[$compt], "#");
            $index[$compt][2] = strtok("#");
            $chainetemp       = strtok("#");
            $index[$compt][3] = strtok($chainetemp, "|");
            $index[$compt][4] = strtok("|");
            $index[$compt][5] = strtok("|");
        }

        if ($nombremsgs < 1) { ?>
        <div class="card">
          <div class="card-body">
            <p class="fa-sub" style="color:#888;font-style:italic;">Aucun message dans ce forum.</p>
            <form method="POST" action="admin.php" style="margin:0;">
              <input type="hidden" name="idaction" value="menuGen">
              <?php print hiddenPassRepforum($pass, $repForum) ?>
              <button type="submit" name="A1" class="btn btn-secondary">Retour au menu</button>
            </form>
          </div>
        </div>
        <?php } else { ?>
        <div class="card">
          <div class="card-header card-header-primary"><span><i class="bi bi-list-ul" style="margin-right:6px;"></i>Messages du forum (<?php print $nombremsgs ?>)</span></div>
          <?php renderMsgTable($index, 1, $nombremsgs); ?>
        </div>

        <div class="card">
          <div class="card-header card-header-primary"><span><i class="bi bi-trash3-fill" style="margin-right:6px;"></i>Supprimer un message</span></div>
          <div class="card-body">
            <div class="alert alert-danger" style="font-size:11px;margin-bottom:12px;">
              <i class="bi bi-exclamation-triangle-fill" style="margin-right:5px;"></i>
              La suppression d'un message entraîne automatiquement la suppression des réponses associées.
            </div>
            <form method="POST" action="admin.php">
              <div class="form-row">
                <label class="form-lbl">N° du message :</label>
                <input type="text" name="idmsgsup" size="6" class="bouton2" style="width:70px;text-align:center;" placeholder="ex: 3">
              </div>
              <input type="hidden" name="idaction" value="demandConfirmSuppMsg">
              <?php print hiddenPassRepforum($pass, $repForum) ?>
              <div class="fa-btn-row">
                <button type="submit" name="A1" class="btn btn-danger">Supprimer</button>
                <form method="POST" action="admin.php" style="margin:0;">
                  <input type="hidden" name="idaction" value="menuGen">
                  <?php print hiddenPassRepforum($pass, $repForum) ?>
                  <button type="submit" name="A1" class="btn btn-secondary">Retour au menu</button>
                </form>
              </div>
            </form>
          </div>
        </div>
        <?php }
    }
}

// ============================================================
// MODULE : demandConfirmSuppMsg
// ============================================================
if ($idaction == "demandConfirmSuppMsg") {
    if ($pass != ROT13($MDP)) {
        echo "<div class='card'><div class='card-body'><p class='fa-error'><i class='bi bi-exclamation-triangle-fill'></i> Session expirée. <a href='admin.php?repforum={$repForum}'>Se reconnecter</a></p></div></div>";
    } else {
        $tabindex   = file("${reperForum}/index.dat");
        $nombremsgs = count($tabindex) - 1;
        for ($compt = 1; $compt <= $nombremsgs; $compt++) {
            $index[$compt][1] = strtok($tabindex[$compt], "#");
            $index[$compt][2] = strtok("#");
            $chainetemp       = strtok("#");
            $index[$compt][3] = strtok($chainetemp, "|");
            $index[$compt][4] = strtok("|");
            $index[$compt][5] = strtok("|");
        }

        if ($idmsgsup == "") { ?>
        <div class="card"><div class="card-body">
          <p class="fa-error"><i class="bi bi-exclamation-triangle-fill"></i> Aucun numéro saisi.</p>
          <form method="POST" action="admin.php" style="margin:0;"><input type="hidden" name="idaction" value="menuSupMsgs"><?php print hiddenPassRepforum($pass, $repForum) ?><button type="submit" name="A1" class="btn btn-secondary">Retour</button></form>
        </div></div>
        <?php exit; }

        if ($idmsgsup < 0 || !file_exists("${reperForum}/msg".$idmsgsup.".dat")) { ?>
        <div class="card"><div class="card-body">
          <p class="fa-error"><i class="bi bi-exclamation-triangle-fill"></i> Ce message n'existe pas ou a déjà été supprimé.</p>
          <form method="POST" action="admin.php" style="margin:0;"><input type="hidden" name="idaction" value="menuSupMsgs"><?php print hiddenPassRepforum($pass, $repForum) ?><button type="submit" name="A1" class="btn btn-secondary">Retour</button></form>
        </div></div>
        <?php exit; }

        $rangMsgSupP = 1;
        while (@$index[$rangMsgSupP][1] != $idmsgsup) $rangMsgSupP++;
        $rangMsgSupD = $rangMsgSupP;
        while (@$index[$rangMsgSupD + 1][2] > $index[$rangMsgSupP][2]) $rangMsgSupD++;
        ?>
        <div class="card">
          <div class="card-header card-header-primary"><span><i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>Confirmer la suppression</span></div>
          <div class="card-body">
            <p class="fa-sub" style="margin-bottom:10px;">Vous êtes sur le point de supprimer le(s) message(s) suivant(s) :</p>
            <?php renderMsgTable($index, $rangMsgSupP, $rangMsgSupD); ?>
            <div class="fa-btn-row" style="margin-top:14px;">
              <form method="POST" action="admin.php" style="margin:0;">
                <input type="hidden" name="idaction" value="suppresMsgs">
                <?php print hiddenPassRepforum($pass, $repForum) ?>
                <input type="hidden" name="rangsupmin" value="<?php print $rangMsgSupP ?>">
                <input type="hidden" name="rangsupmax" value="<?php print $rangMsgSupD ?>">
                <button type="submit" name="A1" class="btn btn-danger"><i class="bi bi-trash3" style="margin-right:5px;"></i>Confirmer la suppression</button>
              </form>
              <form method="POST" action="admin.php" style="margin:0;">
                <input type="hidden" name="idaction" value="menuGen">
                <?php print hiddenPassRepforum($pass, $repForum) ?>
                <button type="submit" name="A1" class="btn btn-secondary">Annuler</button>
              </form>
            </div>
          </div>
        </div>
    <?php }
}

// ============================================================
// MODULE : suppresMsgs
// ============================================================
if ($idaction == "suppresMsgs") {
    if ($pass != ROT13($MDP)) {
        echo "<div class='card'><div class='card-body'><p class='fa-error'><i class='bi bi-exclamation-triangle-fill'></i> Session expirée. <a href='admin.php?repforum={$repForum}'>Se reconnecter</a></p></div></div>";
    } else {
        $tabindex   = file("${reperForum}/index.dat");
        $nombremsgs = count($tabindex) - 1;
        for ($compt = 1; $compt <= $nombremsgs; $compt++) {
            $index[$compt][1] = strtok($tabindex[$compt], "#");
            $index[$compt][2] = strtok("#");
            $chainetemp       = strtok("#");
            $index[$compt][3] = strtok($chainetemp, "|");
            $index[$compt][4] = strtok("|");
            $index[$compt][5] = strtok("|");
        }

        if ($rangsupmax > $nombremsgs) { ?>
        <div class="card"><div class="card-body">
          <p class="fa-error"><i class="bi bi-exclamation-triangle-fill"></i> Actualisation détectée — opération annulée pour éviter d'endommager la structure du forum.</p>
          <form method="POST" action="admin.php" style="margin:0;"><input type="hidden" name="idaction" value="menuGen"><?php print hiddenPassRepforum($pass, $repForum) ?><button type="submit" name="A1" class="btn btn-secondary">Retour au menu</button></form>
        </div></div>
        <?php exit; }

        $erreur = false;
        $msgs   = []; ?>
        <div class="card">
          <div class="card-header card-header-primary"><span><i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>Résultat de la suppression</span></div>
          <div class="card-body">
        <?php
        for ($compt = $rangsupmin; $compt <= $rangsupmax; $compt++) {
            $testsup = unlink("${reperForum}/msg".$index[$compt][1].".dat");
            if ($testsup) {
                echo "<p class='fa-success'><i class='bi bi-check-circle-fill'></i> Message n° " . $index[$compt][1] . " supprimé.</p>";
            } else {
                echo "<p class='fa-error'><i class='bi bi-x-circle-fill'></i> Impossible de supprimer le message n° " . $index[$compt][1] . ".</p>";
                $erreur = true;
            }
        }

        if (!$erreur) {
            $ficindex = fopen("${reperForum}/index.dat", "w+");
            fputs($ficindex, "Fichier Index. Ne pas éditer !\n");
            for ($compt = 1; $compt <= $rangsupmin - 1; $compt++) fputs($ficindex, $tabindex[$compt]);
            for ($compt = $rangsupmax + 1; $compt <= $nombremsgs; $compt++) fputs($ficindex, $tabindex[$compt]);
            fclose($ficindex);

            $tabindverif    = file("${reperForum}/index.dat");
            $nombremsgsnouv = count($tabindverif) - 1;

            if ($nombremsgsnouv != ($nombremsgs - ($rangsupmax - $rangsupmin + 1))) {
                echo "<p class='fa-error'><i class='bi bi-exclamation-triangle-fill'></i> Erreur lors de la mise à jour de l'index.</p>";
            } else {
                echo "<p class='fa-success'><i class='bi bi-check-circle-fill'></i> Index mis à jour.</p>";
            }
        }
        ?>
            <div class="fa-btn-row" style="margin-top:10px;">
              <form method="POST" action="admin.php" style="margin:0;"><input type="hidden" name="idaction" value="menuGen"><?php print hiddenPassRepforum($pass, $repForum) ?><button type="submit" name="A1" class="btn btn-primary">Retour au menu</button></form>
              <form method="POST" action="admin.php" style="margin:0;"><input type="hidden" name="idaction" value="menuSupMsgs"><?php print hiddenPassRepforum($pass, $repForum) ?><button type="submit" name="A1" class="btn btn-secondary">Autres suppressions</button></form>
            </div>
          </div>
        </div>
    <?php }
}

// ============================================================
// MODULE : menuChangeMDP
// ============================================================
if ($idaction == "menuChangeMDP") {
    if ($pass != ROT13($MDP)) {
        echo "<div class='card'><div class='card-body'><p class='fa-error'>Session expirée. <a href='admin.php?repforum={$repForum}'>Se reconnecter</a></p></div></div>";
    } else { ?>
    <div class="card">
      <div class="card-header card-header-primary"><span><i class="bi bi-key-fill" style="margin-right:6px;"></i>Changer le mot de passe</span></div>
      <div class="card-body">
        <form method="POST" action="admin.php">
          <div class="form-row"><label class="form-lbl">Nouveau mot de passe :</label><input type="password" name="mdputil1" size="20" class="bouton2"></div>
          <div class="form-row" style="margin-top:6px;"><label class="form-lbl">Confirmation :</label><input type="password" name="mdputil2" size="20" class="bouton2"></div>
          <input type="hidden" name="idaction" value="testChoixMDP">
          <?php print hiddenPassRepforum($pass, $repForum) ?>
          <div class="fa-btn-row"><button type="submit" name="A1" class="btn btn-primary">Enregistrer</button></div>
        </form>
      </div>
    </div>
    <?php }
}
?>

</div><!-- /.fa-wrap -->
</body>
</html>
