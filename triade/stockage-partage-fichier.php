<?php
session_start();
error_reporting(0);

if (empty($_SESSION["nom"])) {
    header('Location: acces_refuse.php');
    exit;
}

if (file_exists("./common/config.inc.php")) include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/lib_licence.php");

function mimetype($fichier, $quoi) {
    global $mess, $HTTP_USER_AGENT;
    if (!preg_match("/MSIE/i", $HTTP_USER_AGENT)) { $client = "netscape.gif"; } else { $client = "html.gif"; }
    if (is_dir($fichier))                              { $image = "dossier.gif"; $nom_type = $mess[8]; }
    elseif (preg_match('/\.mid$/i', $fichier))         { $image = "mid.gif";    $nom_type = $mess[9]; }
    elseif (preg_match('/\.txt$/i', $fichier))         { $image = "txt.gif";    $nom_type = $mess[10]; }
    elseif (preg_match('/\.sql$/i', $fichier))         { $image = "txt.gif";    $nom_type = $mess[10]; }
    elseif (preg_match('/\.js$/i', $fichier))          { $image = "js.gif";     $nom_type = $mess[11]; }
    elseif (preg_match('/\.gif$/i', $fichier))         { $image = "gif.gif";    $nom_type = $mess[12]; }
    elseif (preg_match('/\.jpg$/i', $fichier))         { $image = "jpg.gif";    $nom_type = $mess[13]; }
    elseif (preg_match('/\.html$/i', $fichier))        { $image = $client;      $nom_type = $mess[14]; }
    elseif (preg_match('/\.htm$/i', $fichier))         { $image = $client;      $nom_type = $mess[15]; }
    elseif (preg_match('/\.rar$/i', $fichier))         { $image = "rar.gif";    $nom_type = $mess[60]; }
    elseif (preg_match('/\.gz$/i', $fichier))          { $image = "zip.gif";    $nom_type = $mess[61]; }
    elseif (preg_match('/\.tgz$/i', $fichier))         { $image = "zip.gif";    $nom_type = $mess[61]; }
    elseif (preg_match('/\.z$/i', $fichier))           { $image = "zip.gif";    $nom_type = $mess[61]; }
    elseif (preg_match('/\.ra$/i', $fichier))          { $image = "ram.gif";    $nom_type = $mess[16]; }
    elseif (preg_match('/\.ram$/i', $fichier))         { $image = "ram.gif";    $nom_type = $mess[17]; }
    elseif (preg_match('/\.rm$/i', $fichier))          { $image = "ram.gif";    $nom_type = $mess[17]; }
    elseif (preg_match('/\.pl$/i', $fichier))          { $image = "pl.gif";     $nom_type = $mess[18]; }
    elseif (preg_match('/\.zip$/i', $fichier))         { $image = "zip.gif";    $nom_type = $mess[19]; }
    elseif (preg_match('/\.wav$/i', $fichier))         { $image = "wav.gif";    $nom_type = $mess[20]; }
    elseif (preg_match('/\.php$/i', $fichier))         { $image = "php.gif";    $nom_type = $mess[21]; }
    elseif (preg_match('/\.phtml$/i', $fichier))       { $image = "php.gif";    $nom_type = $mess[22]; }
    elseif (preg_match('/\.exe$/i', $fichier))         { $image = "exe.gif";    $nom_type = $mess[50]; }
    elseif (preg_match('/\.bmp$/i', $fichier))         { $image = "bmp.gif";    $nom_type = $mess[56]; }
    elseif (preg_match('/\.png$/i', $fichier))         { $image = "gif.gif";    $nom_type = $mess[57]; }
    elseif (preg_match('/\.css$/i', $fichier))         { $image = "css.gif";    $nom_type = $mess[58]; }
    elseif (preg_match('/\.mp3$/i', $fichier))         { $image = "mp3.gif";    $nom_type = $mess[59]; }
    elseif (preg_match('/\.xls$/i', $fichier))         { $image = "xls.gif";    $nom_type = $mess[64]; }
    elseif (preg_match('/\.doc$/i', $fichier))         { $image = "doc.gif";    $nom_type = $mess[65]; }
    elseif (preg_match('/\.pdf$/i', $fichier))         { $image = "pdf.gif";    $nom_type = $mess[79]; }
    elseif (preg_match('/\.mov$/i', $fichier))         { $image = "mov.gif";    $nom_type = $mess[80]; }
    elseif (preg_match('/\.avi$/i', $fichier))         { $image = "avi.gif";    $nom_type = $mess[81]; }
    elseif (preg_match('/\.mpg$/i', $fichier))         { $image = "mpg.gif";    $nom_type = $mess[82]; }
    elseif (preg_match('/\.mpeg$/i', $fichier))        { $image = "mpeg.gif";   $nom_type = $mess[83]; }
    elseif (preg_match('/\.swf$/i', $fichier))         { $image = "flash.gif";  $nom_type = $mess[91]; }
    else                                               { $image = "defaut.gif"; $nom_type = $mess[23]; }
    if ($quoi == "image") { return $image; } else { return $nom_type; }
}

function taille($fichier) {
    global $size_unit;
    $taille = filesize($fichier);
    if ($taille >= 1073741824)     { $taille = round($taille / 1073741824 * 100) / 100 . " G" . $size_unit; }
    elseif ($taille >= 1048576)    { $taille = round($taille / 1048576 * 100)    / 100 . " M" . $size_unit; }
    elseif ($taille >= 1024)       { $taille = round($taille / 1024 * 100)       / 100 . " K" . $size_unit; }
    else                           { $taille = $taille . " " . $size_unit; }
    if ($taille == 0) { $taille = "-"; }
    return $taille;
}

function getNomBeneficiaire($membreIdAutorise) {
    global $prefixe;
    if (!preg_match('/^(.*?)(\d+)$/', $membreIdAutorise, $m)) return htmlspecialchars($membreIdAutorise);
    $id = $m[2];
    $sql = ($m[1] === 'menueleve')
        ? "SELECT nom, prenom FROM {$prefixe}eleves WHERE elev_id='$id'"
        : "SELECT nom, prenom FROM {$prefixe}personnel WHERE pers_id='$id'";
    $res  = execSql($sql);
    $data = chargeMat($res);
    return (countTriade($data) > 0)
        ? htmlspecialchars(strtoupper($data[0][0]) . ' ' . ucfirst($data[0][1]))
        : htmlspecialchars($membreIdAutorise);
}

$fic     = $_GET["fic"]     ?? '';
$fichier = $_GET["fichier"] ?? '';
$idpersS = $_SESSION["id_pers"];
$membreS = $_SESSION["membre"];
$saisie_classe = 0;
$infoEnr = "";

if (isset($_POST['saisie_classe'])) {
    $fic           = $_POST["fic"];
    $fichier       = $_POST["fichier"];
    $idclasse      = $_POST['saisie_classe'];
    $saisie_classe = $_POST['saisie_classe'];
}

if (isset($_POST['create2'])) {
    $idpers      = $_POST['saisie_pers'];
    $fic         = $_POST["fic"];
    $fichier     = $_POST["fichier"];
    $idpersdest  = $_SESSION['membre'] . $idpers;
    $membre      = $_SESSION['membre'];
    $membrerep   = $_SESSION['membre'];
    $idpersrep   = $_SESSION["id_pers"];
    ajoutPartage($fic, $fichier, "$membre$idpersS", "$idpersdest", '-1', "$membrerep", "$idpersrep");
    $infoEnr = LANGABS28;
}

if (isset($_POST['create'])) {
    $fic         = $_POST["fic"];
    $idclasse    = $_POST['idclasse'];
    $saisie_classe = $_POST['idclasse'];
    $fichier     = $_POST["fichier"];
    $nb          = $_POST['saisie_nb'];
    $membrerep   = $_POST['membrerep'];
    $idpersrep   = $_POST['idpersrep'];
    suppPartage($fic, $fichier, "$membreS$idpersS", "$idpersdest", $idclasse);
    for ($i = 0; $i < $nb; $i++) {
        $idpersdest = $_POST['ideleve'][$i];
        if (trim($idpersdest) != "") ajoutPartage($fic, $fichier, "$membreS$idpersS", "$idpersdest", $idclasse, $membrerep, $idpersrep);
    }
    $infoEnr = LANGABS28;
}

if (isset($_POST['delete_share'])) {
    $fic     = $_POST["fic"];
    $fichier = $_POST["fichier"];
    $sid     = intval($_POST['share_id_del']);
    if ($sid > 0) suppFichierPartager($sid);
    $infoEnr = "Partage supprim&eacute;";
}

// Partages actifs pour ce fichier (cr&eacute;&eacute;s par l'utilisateur courant)
$cnx = cnx();
$sql_actifs = "SELECT id, membreIdAutorise, idclasse FROM {$prefixe}stockage_partage "
            . "WHERE fichier='" . addslashes($fichier) . "' AND chemin='" . addslashes($fic) . "' "
            . "AND membreIdProprio='" . addslashes("$membreS$idpersS") . "' "
            . "ORDER BY idclasse DESC, id";
$res_actifs  = execSql($sql_actifs);
$data_actifs = chargeMat($res_actifs);
$nb_actifs   = countTriade($data_actifs);

$chemin = $fic;
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="CacheControl" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<title>TRIADE-BOX &mdash; Partage</title>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<?php include_once("./librairie_php/langue.php"); ?>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg) { alertify.error(msg); };</script>
<style>
body { background:#f0f2fa; margin:0; padding:16px; font-family:'Trebuchet MS',Arial,sans-serif; }
.spf-wrap { max-width:700px; margin:0 auto; }

.spf-file-card {
    background:linear-gradient(135deg,#0d1b4a 0%,#1e4d8c 100%);
    border-radius:12px; padding:16px 20px;
    display:flex; align-items:center; justify-content:space-between;
    gap:12px; margin-bottom:18px;
    box-shadow:0 4px 16px rgba(0,0,0,.18);
}
.spf-file-info { display:flex; align-items:center; gap:14px; }
.spf-file-info img { width:32px; height:32px; }
.spf-file-name { color:#fff; font-weight:700; font-size:13px; }
.spf-file-meta { color:#b0c4de; font-size:11px; margin-top:3px; }
.spf-btn-retour {
    background:#fff; color:#0d1b4a; border:none; border-radius:8px;
    padding:7px 16px; font-size:12px; font-weight:700; cursor:pointer;
    white-space:nowrap; text-decoration:none;
}
.spf-btn-retour:hover { background:#e8eaf6; }

.spf-section {
    background:#fff; border-radius:12px;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
    margin-bottom:16px; overflow:hidden;
}
.spf-section-header {
    background:linear-gradient(90deg,#080A66,#1e4d8c);
    color:#fff; padding:10px 16px;
    font-size:12px; font-weight:700; font-family:Electrolize,'Trebuchet MS',Arial;
    letter-spacing:.5px; text-transform:uppercase;
}
.spf-section-body { padding:14px 16px; }

.spf-filter-row { display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:4px; }
.spf-filter-row label { font-size:12px; font-weight:700; color:#080A66; }

.spf-btn {
    background:linear-gradient(135deg,#080A66,#1e4d8c);
    color:#fff; border:none; border-radius:8px;
    padding:7px 18px; font-size:12px; font-weight:700; cursor:pointer;
}
.spf-btn:hover { background:linear-gradient(135deg,#1e4d8c,#080A66); }

.spf-success { color:#2e7d32; font-weight:700; font-size:12px; margin-top:8px; }
</style>
</head>
<body>
<?php if ($infoEnr): ?>
<script>document.addEventListener('DOMContentLoaded',function(){ alertify.success('<?php echo addslashes($infoEnr); ?>'); });</script>
<?php endif; ?>

<div class="spf-wrap">

  <!-- ── Info fichier ── -->
  <div class="spf-file-card">
    <div class="spf-file-info">
      <img src="image/stockage/<?php echo mimetype("./data/stockage/$membreS/$idpersS/$chemin", "image"); ?>" alt="">
      <div>
        <div class="spf-file-name"><?php echo htmlspecialchars(trunchaine($fichier, 35)); ?></div>
        <div class="spf-file-meta">
          <?php echo taille("./data/stockage/$membreS/$idpersS/$chemin"); ?>
          &nbsp;&bull;&nbsp;
          Modifi&eacute; le <?php $tmp = filemtime("./data/stockage/$membreS/$idpersS/$chemin"); echo date("d/m/Y H:i", $tmp); ?>
        </div>
      </div>
    </div>
    <button class="spf-btn-retour" onclick="open('stockage.php','_self','')">
      &larr; <?php echo LANGSTAGE73; ?>
    </button>
  </div>

  <!-- ── Partage par classe ── -->
  <div class="spf-section">
    <div class="spf-section-header">Partager avec une classe</div>
    <div class="spf-section-body">
      <form method="post" name="form0">
        <div class="spf-filter-row">
          <label><?php echo LANGPROFG; ?> :</label>
          <select id="saisie_classe" name="saisie_classe" class="ca-select" onchange="this.form.submit()">
            <?php
            if ($saisie_classe > 0) {
                echo "<option value='$saisie_classe'>".chercheClasse_nom($saisie_classe)."</option>";
            }
            echo "<option value=''>".LANGCHOIX."</option>";
            select_classe();
            ?>
          </select>
        </div>
        <input type="hidden" name="fic"     value="<?php echo htmlspecialchars($fic); ?>">
        <input type="hidden" name="fichier" value="<?php echo htmlspecialchars($fichier); ?>">
      </form>

      <?php if ($saisie_classe > 0): ?>
      <form method="post" name="form1" style="margin-top:12px;">
        <table class="cc-data-table">
          <thead>
            <tr class="cc-thead-row">
              <th class="cc-th"><?php echo LANGNA1; ?></th>
              <th class="cc-th"><?php echo LANGELE3; ?></th>
              <th class="cc-th cc-th-center" style="width:90px;">
                Autoris&eacute;
                <input type="checkbox" onclick="validecase();" name="tous" value="1" style="margin-left:6px;vertical-align:middle;">
              </th>
            </tr>
          </thead>
          <tbody>
          <?php
          $sql  = "SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves ,{$prefixe}classes ";
          $sql .= "WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
          $res  = execSql($sql);
          $data = chargeMat($res);
          $membre = "menueleve";
          $ii = 3;
          for ($i = 0; $i < countTriade($data); $i++):
              $idperseleve  = $data[$i][1];
              $nomeleve     = $data[$i][2];
              $prenomeleve  = $data[$i][3];
              $checked = (verifSIPartage($chemin, $fichier, "$membreS$idpersS", "$membre$idperseleve") > 0) ? "checked='checked'" : "";
          ?>
            <tr id="tr<?php echo $ii; ?>" class="cc-tr-data">
              <td class="cc-td"><?php echo htmlspecialchars(strtoupper($nomeleve)); ?></td>
              <td class="cc-td"><?php echo htmlspecialchars(ucfirst($prenomeleve)); ?></td>
              <td class="cc-td cc-td-center">
                <input type="checkbox" name="ideleve[]" value="<?php echo "$membre$idperseleve"; ?>" <?php echo $checked; ?>>
              </td>
            </tr>
          <?php $ii++; endfor; ?>
          </tbody>
        </table>
        <div style="margin-top:10px;">
          <script language="JavaScript">buttonMagicSubmit("Partager","create");</script>
          <br>
        </div>
        <input type="hidden" name="saisie_nb"  value="<?php echo countTriade($data); ?>">
        <input type="hidden" name="fichier"    value="<?php echo htmlspecialchars($fichier); ?>">
        <input type="hidden" name="fic"        value="<?php echo htmlspecialchars($fic); ?>">
        <input type="hidden" name="idclasse"   value="<?php echo htmlspecialchars($idclasse); ?>">
        <input type="hidden" name="membrerep"  value="<?php echo $membreS; ?>">
        <input type="hidden" name="idpersrep"  value="<?php echo $idpersS; ?>">
      </form>
      <?php endif; ?>
    </div>
  </div>

  <!-- ── Partage individuel ── -->
  <div class="spf-section">
    <div class="spf-section-header">Partager avec une personne</div>
    <div class="spf-section-body">
      <form method="post">
        <div class="spf-filter-row">
          <label>Partager avec :</label>
          <select name="saisie_pers" class="ca-select">
            <option value=""><?php echo LANGCHOIX; ?></option>
            <optgroup label="Direction"><?php select_personne('ADM'); ?></optgroup>
            <optgroup label="Enseignant"><?php select_personne('ENS'); ?></optgroup>
            <optgroup label="Vie Scolaire"><?php select_personne('MVS'); ?></optgroup>
            <optgroup label="Personnel"><?php select_personne('PER'); ?></optgroup>
          </select>
          <script language="JavaScript">buttonMagicSubmit("Partager","create2");</script>
          <br>
        </div>
        <input type="hidden" name="fic"     value="<?php echo htmlspecialchars($fic); ?>">
        <input type="hidden" name="fichier" value="<?php echo htmlspecialchars($fichier); ?>">
      </form>
    </div>
  </div>

  <!-- ── Partages actifs ── -->
  <div class="spf-section">
    <div class="spf-section-header" style="display:flex;align-items:center;justify-content:space-between;">
      Partages actifs
      <span style="background:rgba(255,255,255,.25);border-radius:20px;padding:2px 10px;font-size:11px;font-weight:700;">
        <?php echo $nb_actifs; ?> partage<?php echo $nb_actifs > 1 ? 's' : ''; ?>
      </span>
    </div>
    <div class="spf-section-body">
      <?php if ($nb_actifs == 0): ?>
      <div style="text-align:center;padding:20px;color:#888;font-size:13px;">
        &#128274; Aucun partage actif pour ce fichier.
      </div>
      <?php else: ?>
      <table class="cc-data-table">
        <thead>
          <tr class="cc-thead-row">
            <th class="cc-th">B&eacute;n&eacute;ficiaire</th>
            <th class="cc-th" style="width:120px;">Type</th>
            <th class="cc-th cc-th-center" style="width:80px;">Supprimer</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $last_classe = null;
        for ($i = 0; $i < $nb_actifs; $i++):
            $sh_id      = $data_actifs[$i][0];
            $sh_dest    = $data_actifs[$i][1];
            $sh_classe  = $data_actifs[$i][2];
            if ($sh_classe != -1 && $sh_classe !== '-1') {
                $type_label = 'Classe';
                $nom_label  = htmlspecialchars(chercheClasse_nom($sh_classe));
                // skip duplicates (same classe = one row per student, show class once)
                if ($sh_classe === $last_classe) continue;
                $last_classe = $sh_classe;
            } else {
                $type_label = 'Individuel';
                $nom_label  = getNomBeneficiaire($sh_dest);
            }
        ?>
          <tr class="cc-tr-data">
            <td class="cc-td"><?php echo $nom_label; ?></td>
            <td class="cc-td">
              <?php if ($type_label === 'Classe'): ?>
              <span style="background:#e8eaf6;color:#080A66;border-radius:12px;padding:2px 10px;font-size:11px;font-weight:700;">
                &#128101; Classe
              </span>
              <?php else: ?>
              <span style="background:#fff3e0;color:#e65100;border-radius:12px;padding:2px 10px;font-size:11px;font-weight:700;">
                &#128100; Individuel
              </span>
              <?php endif; ?>
            </td>
            <td class="cc-td cc-td-center">
              <form method="post" style="display:inline;" onsubmit="return confirm('Supprimer ce partage ?');">
                <input type="hidden" name="share_id_del" value="<?php echo $sh_id; ?>">
                <input type="hidden" name="fic"          value="<?php echo htmlspecialchars($fic); ?>">
                <input type="hidden" name="fichier"      value="<?php echo htmlspecialchars($fichier); ?>">
                <button type="submit" name="delete_share" value="1"
                        style="background:none;border:none;cursor:pointer;color:#c0392b;font-size:16px;"
                        title="Supprimer">&#128465;</button>
              </form>
            </td>
          </tr>
        <?php endfor; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </div>

</div>

<script>
function validecase() {
    var nb = parseInt(document.form1.saisie_nb.value) + 3;
    var j = 0;
    var checked = document.form1.tous.checked;
    for (var i = 3; i < nb; i++) {
        document.form1.elements[j].checked = checked;
        j++;
    }
}
</script>
<?php Pgclose(); ?>
</body>
</html>
