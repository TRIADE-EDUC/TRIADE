<?php
session_start();
include_once("./librairie_php/lib_get_init.php");
$id = php_ini_get("safe_mode");
if ($id != 1) { set_time_limit(0); }
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
 *   Site                 : http://www.triade-educ.com
 *
 ***************************************************************************/
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/lib_attente.php");
include_once("./librairie_php/timezone.php");
?>
<script>AfficheAttente()</script>
<?php
if (empty($_SESSION["adminplus"])) {
    print "<script>location.href='./base_de_donne_importation.php'</script>";
    exit;
}

include_once("./librairie_php/db_triade.php");

$anneeScolaire = trim(preg_replace('/ /', '', $_POST["annee_scolaire"]));
$rep           = "./data/archive/bulletin/$anneeScolaire";
$success       = false;
$fichier       = '';

if ((is_dir($rep)) && ($anneeScolaire != "")) {

    @mkdir("./data/archive/tmp/");
    @mkdir("./data/archive/tmp/$anneeScolaire");

    $cnx = cnx();

    $dir = opendir("$rep");
    while ($file = readdir($dir)) {
        $file = trim($file);
        if (($file != ".triade") && ($file != ".htaccess") && ($file != ".") && ($file != "..")) {
            $repanalyse = "$rep/$file";
            $file = preg_replace('/_/', '', $file);
            $nomEtudiantPrenom = recherche_eleve_nom($file)." ".recherche_eleve_prenom($file);
            if (trim($nomEtudiantPrenom) == "") continue;
            $nomEtudiantPrenom = preg_replace('/ /', '_', $nomEtudiantPrenom);
            $destination = "./data/archive/tmp/$anneeScolaire/$nomEtudiantPrenom/";
            @mkdir("$destination");
            if ((is_dir($repanalyse)) && (trim($file) != "")) {
                $dir2 = opendir("$repanalyse");
                while ($file2 = readdir($dir2)) {
                    if (($file2 != ".triade") && ($file2 != ".htaccess") && ($file2 != ".") && ($file2 != "..")) {
                        @copy("$repanalyse/$file2", "$destination/$file2");
                    }
                }
                closedir($dir2);
            }
        }
    }
    closedir($dir);
    pgClose();

    include_once('./librairie_php/pclzip.lib.php');
    @unlink('./data/archive/bulletin/'.$anneeScolaire.'.zip');
    $archive = new PclZip('./data/archive/bulletin/'.$anneeScolaire.'.zip');
    $archive->create('./data/archive/tmp/'.$anneeScolaire, PCLZIP_OPT_REMOVE_PATH, 'data/archive/tmp');
    $fichier  = './data/archive/bulletin/'.$anneeScolaire.'.zip';
    $success  = true;

    recursive_delete("./data/archive/tmp/$anneeScolaire");
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'>Archive des bulletins</font></b>
</td></tr>
<tr id='cadreCentral0'>
<td>
<!-- // debut -->

<div style="display:flex;flex-direction:column;gap:12px;padding:10px 6px">

  <?php if ($success): ?>
  <div class="card" style="border-top:3px solid #2e7d32">
    <div class="card-header" style="display:flex;align-items:center;gap:7px;font-size:12px;font-weight:700;color:#2e7d32">
      <i class="bi bi-check-circle-fill"></i> Archive générée — <?php print htmlspecialchars($anneeScolaire) ?>
    </div>
    <div class="card-body" style="padding:12px 16px;display:flex;flex-direction:column;gap:12px">
      <p style="margin:0;font-size:12px;color:#555">
        L'archive ZIP des bulletins a été générée avec succès. Cliquez pour la télécharger.
      </p>
      <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
        <button type="button"
                onclick="open('telecharger.php?fichier=<?php print urlencode($fichier) ?>','_blank','')"
                class="btn btn-primary" style="background:#2e7d32;border-color:#2e7d32;display:inline-flex;align-items:center;gap:7px">
          <i class="bi bi-file-earmark-zip"></i> Télécharger le fichier ZIP
        </button>
        <script language=JavaScript>buttonMagic('<?php print LANGSTAGE73 ?>','archivage2.php','_self','','')</script>
      </div>
    </div>
  </div>

  <?php else: ?>
  <div class="card" style="border-top:3px solid #c62828">
    <div class="card-header" style="display:flex;align-items:center;gap:7px;font-size:12px;font-weight:700;color:#c62828">
      <i class="bi bi-exclamation-triangle-fill"></i> Aucun bulletin trouvé
    </div>
    <div class="card-body" style="padding:12px 16px;display:flex;flex-direction:column;gap:12px">
      <p style="margin:0;font-size:12px;color:#555">
        Pas de bulletin pour l'année scolaire : <strong><?php print htmlspecialchars($_POST["annee_scolaire"] ?? '') ?></strong>
      </p>
      <div>
        <script language=JavaScript>buttonMagic('<?php print LANGSTAGE73 ?>','archivage2.php','_self','','')</script>
      </div>
    </div>
  </div>
  <?php endif; ?>

</div>

<!-- // fin -->
</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
