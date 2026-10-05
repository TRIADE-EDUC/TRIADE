<?php
session_start();
error_reporting(0);
if (empty($_SESSION["admin1"])) {
    print "<script>location.href='./acces_refuse.php'</script>"; exit;
}
include_once("../common/lib_admin.php");
include_once("../common/lib_ecole.php");
include_once("../common/config2.inc.php");
include_once("../common/config.inc.php");
include_once("../librairie_php/db_triade.php");
include_once("./librairie_php/langue-text-admin-fr.php");
$cnx = cnx();

$done    = false;
$message = "";
$erreurPersonne = "";

if (isset($_POST["create"])) {
    $modifParent1  = $_POST["parent1"]       ?? "";
    $modifeleve1   = $_POST["eleve1"]        ?? "";
    $emailenvoie   = $_POST["emailenvoie"]   ?? "";
    $idclasse      = $_POST["idclasse"]      ?? "tous";
    $anneeScolaire = $_POST["annee_scolaire"]?? "";

    if ($_POST["initia"] == "2") {
        $fichier  = $_FILES['fichier']['name']     ?? "";
        $type     = $_FILES['fichier']['type']     ?? "";
        $tmp_name = $_FILES['fichier']['tmp_name'] ?? "";
        $size     = $_FILES['fichier']['size']     ?? 0;
        if (!empty($fichier) && $size <= 2000000 && preg_match('/csv$/', $fichier)) {
            $dest = "../data/parametrage/passe_import.txt";
            @unlink($dest); @unlink("../data/fic_pass.txt");
            move_uploaded_file($tmp_name, $dest);
            foreach (file($dest) as $line) {
                list($nomP,$prenomP,$dateNaissanceP,$passwdParent,$passwdEleve) = preg_split("/;/", $line, 6);
                $cr = modifPassword(addslashes($nomP), addslashes($prenomP), $dateNaissanceP, $passwdParent, $passwdEleve);
                if ($cr == "-1") $erreurPersonne .= "$nomP;$prenomP;$dateNaissanceP\n";
                elseif ($emailenvoie == 1) envoiMailPassElPa($nomP, $prenomP, $dateNaissanceP, $passwdParent, $passwdEleve);
            }
            @unlink($dest);
            $message = "Les mots de passe sont réinitialisés.";
        } else {
            $message = "Fichier non conforme — format CSV attendu.";
        }
    } elseif ($_POST["initia"] == "0") {
        $nb = initialisePasswordEleveParent($modifeleve1, $modifParent1, $emailenvoie, $idclasse, $anneeScolaire);
        $message = ($nb > 0) ? "Les mots de passe sont réinitialisés." : "Aucun mot de passe modifié.";
    } elseif ($_POST["initia"] == "1") {
        $nb = initialisePasswordDefinieEleveParent($_POST["passeldef"], $_POST["passpardef"], $emailenvoie, $idclasse, $anneeScolaire);
        $message = ($nb > 0) ? "Les mots de passe sont réinitialisés." : "Aucun mot de passe modifié.";
    }
    history_cmdAdmin("Admin Triade", "MODIF", "Réinitialisation mot de passe Eleve / Parent");
    $done = true;
}
Pgclose($cnx);
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4-2.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css">
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<style>
* { box-sizing:border-box; }
body { margin:0; padding:12px; background:#f0f2fa; font-family:Electrolize,Arial,sans-serif; }
.pw-wrap { max-width:480px; margin:0 auto; display:flex; flex-direction:column; gap:8px; }
.pw-radio-group { display:flex; flex-direction:column; gap:6px; }
.pw-radio-group label { display:flex; align-items:center; gap:7px; font-size:12px; cursor:pointer; padding:5px 8px; border-radius:6px; border:1px solid #dde0f0; }
.pw-radio-group label:has(input:checked) { background:#e8ebff; border-color:#080A66; font-weight:600; }
.pw-sub-field { margin-left:24px; margin-top:4px; padding:8px 10px; background:#f5f7ff; border-radius:6px; border:1px solid #dde0f0; font-size:11px; }
</style>
<title>Triade — Mots de passe parents / élèves</title>
</HEAD>
<body>
<div class="pw-wrap">

<?php if ($done): ?>
  <div class="card">
    <div class="card-header card-header-primary"><span><i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>Résultat</span></div>
    <div class="card-body">
      <?php if ($erreurPersonne): ?>
        <div class="alert alert-danger" style="margin-bottom:8px;font-size:11px;"><i class="bi bi-exclamation-triangle-fill"></i> Élèves non trouvés :</div>
        <textarea rows="6" style="width:100%;font-size:11px;border:1px solid #dde0f0;border-radius:6px;padding:6px;">Élève non trouvé — vérifier nom, prénom et date de naissance.
<?php print $erreurPersonne ?></textarea>
      <?php else: ?>
        <p style="color:#2e7d32;font-weight:700;"><i class="bi bi-check-circle-fill"></i> <?php print htmlspecialchars($message) ?></p>
      <?php endif; ?>
      <?php if (file_exists("../data/fic_pass.txt")): ?>
      <div style="margin-top:10px;">
        <button type="button" class="btn btn-primary" onclick="open('recupepw.php','_blank','')">
          <i class="bi bi-download" style="margin-right:5px;"></i>Récupérer les mots de passe
        </button>
      </div>
      <?php endif; ?>
      <div style="margin-top:12px;">
        <script language="JavaScript">buttonMagicFermeture();</script>
      </div>
    </div>
  </div>

<?php else: ?>

  <div class="card">
    <div class="card-header card-header-primary"><span><i class="bi bi-people-fill" style="margin-right:6px;"></i>Réinitialisation — parents &amp; élèves</span></div>
    <div class="card-body">
      <form name="formulaire" method="post" enctype="multipart/form-data"
            onsubmit="document.formulaire.rien.disabled=true">

        <div class="form-row">
          <label class="form-lbl">Année scolaire :</label>
          <select name="annee_scolaire" class="cc-select">
            <?php filtreAnneeScolaireSelectNote($anneeScolaire ?? '', 3); ?>
          </select>
        </div>

        <div class="form-row" style="margin-top:6px;">
          <label class="form-lbl">Classe :</label>
          <select name="idclasse" class="cc-select">
            <option value="tous">Toutes les classes</option>
            <?php select_classe(); ?>
          </select>
        </div>

        <?php if ((LAN == "oui") && ValideMail(MAILREPLY)): ?>
        <div style="margin-top:8px;">
          <label style="display:flex;align-items:center;gap:7px;font-size:12px;cursor:pointer;">
            <input type="checkbox" name="emailenvoie" value="1" onclick="envoiMailP()">
            Envoyer un email avec le nouveau mot de passe
          </label>
          <div id="infourl" style="display:none;" class="alert alert-danger" style="margin-top:6px;font-size:11px;">
            <i class="bi bi-exclamation-triangle-fill"></i> Vérifiez l'adresse internet du site Triade dans Config. Général avant validation !
          </div>
        </div>
        <?php endif; ?>

        <div style="margin-top:10px;">
          <div class="pw-radio-group">
            <label>
              <input type="radio" name="initia" value="0" checked
                onclick="document.getElementById('zone-imp').style.display='none';document.getElementById('zone-par').style.display='none';document.getElementById('zone-el').style.display='none';">
              Mot de passe aléatoire &nbsp;
              <span style="font-size:11px;color:#555;">
                (<input type="checkbox" name="parent1" value="1"> Parents &nbsp;
                <input type="checkbox" name="eleve1" value="1"> Élèves)
              </span>
            </label>
            <label>
              <input type="radio" name="initia" value="1"
                onclick="document.getElementById('zone-imp').style.display='none';document.getElementById('zone-par').style.display='block';document.getElementById('zone-el').style.display='block';">
              Mot de passe défini
            </label>
            <div id="zone-el" style="display:none;" class="pw-sub-field">
              Mot de passe élèves : <input type="text" name="passeldef" class="bouton2" style="width:140px;"> <em>(vide = pas de modification)</em>
            </div>
            <div id="zone-par" style="display:none;" class="pw-sub-field">
              Mot de passe parents : <input type="text" name="passpardef" class="bouton2" style="width:140px;"> <em>(vide = pas de modification)</em>
            </div>
            <label>
              <input type="radio" name="initia" value="2"
                onclick="document.getElementById('zone-par').style.display='none';document.getElementById('zone-el').style.display='none';document.getElementById('zone-imp').style.display='block';">
              Importer depuis CSV <span style="font-size:11px;color:#555;">(sans notion d'année)</span>
            </label>
            <div id="zone-imp" style="display:none;" class="pw-sub-field">
              Fichier CSV : <input type="file" name="fichier" accept=".csv"><br>
              <span style="color:#555;font-size:10px;margin-top:4px;display:block;">Format : Nom<b>;</b>Prénom<b>;</b>Date naissance (jj/mm/aaaa)<b>;</b>MDP parents<b>;</b>MDP élèves<b>;</b></span>
            </div>
          </div>
        </div>

        <div style="margin-top:14px;display:flex;gap:8px;">
          <script language="JavaScript">buttonMagicSubmit("Confirmer","rien");</script>
          <script language="JavaScript">buttonMagicFermeture();</script>
        </div>
        <input type="hidden" name="create">
      </form>
    </div>
  </div>

<?php endif; ?>

</div>
<script>
function envoiMailP() {
    var el = document.getElementById('infourl');
    el.style.display = (el.style.display === 'none') ? 'block' : 'none';
}
</script>
</body>
</HTML>
