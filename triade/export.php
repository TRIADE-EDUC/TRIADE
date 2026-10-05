<?php
session_start();
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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("2");
$cnx = cnx();

$nameConflict    = false;
$nom_structure   = '';
$structure_saved = '';

if (isset($_POST["savestructure"])) {
    $structure_saved = stripslashes($_POST["structure"]);
    $nom_structure   = $_POST["nom_structure"];
    $libelle         = "##struct##$nom_structure";
    $data            = aff_enr_parametrage($libelle);
    if (countTriade($data) > 0) {
        $nameConflict = true;
    } else {
        enr_parametrage($libelle, $structure_saved);
    }
}

if (isset($_GET['supp'])) { supp_parametrage('##struct##'.$_GET['supp']); }

$structures = aff_structure("##struct##");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'><?php print LANGMESS234 ?></font></b>
</td></tr>
<tr id='cadreCentral0'>
<td>
<!-- // debut -->

<div style="display:flex;flex-direction:column;gap:12px;padding:10px 6px">

  <!-- Exports disponibles -->
  <div class="card">
    <div class="card-header" style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:#080A66">
      <i class="bi bi-download"></i> <?php print LANGMESS235 ?>
    </div>
    <div class="card-body" style="display:flex;flex-direction:column;gap:8px;padding:10px 14px">
      <a href="./export_eleve.php" style="display:inline-flex;align-items:center;gap:8px;background:#080A66;color:#fff;border-radius:6px;padding:8px 16px;font-size:12px;font-weight:700;text-decoration:none;width:fit-content">
        <i class="bi bi-person-lines-fill"></i>
        <?php
          $affich_intitule_eleve = "";
          if(grapheme_substr(INTITULEELEVE, 0, 1) == "é"){ // ucfirst ne rend pas les caractères accentués
            $affich_intitule_eleve = "É" . grapheme_substr(INTITULEELEVE, 1);
          } else {
            $affich_intitule_eleve = ucfirst(INTITULEELEVE);
          }
          echo $affich_intitule_eleve;
        ?>
        <span style="font-weight:400;opacity:.75;font-size:11px">(format Excel)</span>
      </a>
      <a href="./export_siecle_bee.php" style="display:inline-flex;align-items:center;gap:8px;background:#1565c0;color:#fff;border-radius:6px;padding:8px 16px;font-size:12px;font-weight:700;text-decoration:none;width:fit-content">
        <i class="bi bi-file-earmark-zip"></i>
        <?php print defined('LANG_SIECLE_EXPORT_TITRE') ? LANG_SIECLE_EXPORT_TITRE : "SIECLE-BEE"; ?>
        <span style="font-weight:400;opacity:.75;font-size:11px">(XML / ZIP - Format MENJ)</span>
      </a>
      <a href="./export_personnel.php" style="display:inline-flex;align-items:center;gap:8px;background:#3949ab;color:#fff;border-radius:6px;padding:8px 16px;font-size:12px;font-weight:700;text-decoration:none;width:fit-content">
        <i class="bi bi-people-fill"></i>
        <?php print LANGMESS236 ?>
        <span style="font-weight:400;opacity:.75;font-size:11px">(Ens., Vie scolaire, Dir., etc.)</span>
      </a>
    </div>
  </div>

  <!-- Structures sauvegardées -->
  <div class="card">
    <div class="card-header" style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:#080A66">
      <i class="bi bi-bookmark-star"></i> <?php print LANGMESS237 ?>
    </div>
    <div class="card-body" style="padding:10px 14px">

      <?php if ($nameConflict): ?>
      <div style="background:#fff3e0;border:1px solid #ffcc80;border-radius:6px;padding:8px 12px;margin-bottom:10px">
        <form method='post' action='export.php' style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
          <input type="hidden" name="structure" value="<?php print htmlspecialchars($structure_saved) ?>">
          <span style="color:#e65100;font-size:12px;font-weight:600"><?php print LANGTMESS431 ?> :</span>
          <input type="text" name="nom_structure" value="<?php print htmlspecialchars($nom_structure) ?>"
                 style="border:1px solid #ffcc80;border-radius:4px;padding:4px 8px;font-size:12px;flex:1;min-width:120px">
          <button type="submit" name="savestructure" class="btn btn-primary" style="padding:4px 14px;font-size:12px"><?php print VALIDE ?></button>
        </form>
      </div>
      <?php endif; ?>

      <?php if (countTriade($structures) > 0): ?>
      <table class="table" style="margin:0">
        <tbody>
        <?php for($i = 0; $i < countTriade($structures); $i++):
          $lib = preg_replace('/##struct##/', '', $structures[$i][0]);
        ?>
          <tr class="cc-tr-data">
            <td>
              <i class="bi bi-file-earmark-spreadsheet" style="color:#2e7d32;font-size:13px"></i>
              <?php print LANGTMESS432 ?> :
              <a href="export_eleve_3.php?libelle=<?php print urlencode($lib) ?>" style="color:#080A66;font-weight:600"><?php print htmlspecialchars($lib) ?></a>
            </td>
            <td style="width:40px;text-align:center">
              <a href="./export.php?supp=<?php print urlencode($lib) ?>" title="Supprimer" style="color:#c62828;font-size:15px">
                <i class="bi bi-trash"></i>
              </a>
            </td>
          </tr>
        <?php endfor; ?>
        </tbody>
      </table>
      <?php else: ?>
      <p style="font-size:12px;color:#888;font-style:italic;margin:0">Aucune structure sauvegardée.</p>
      <?php endif; ?>

    </div>
  </div>

  <div>
    <script language=JavaScript>buttonMagicRetour("acces2.php","_self")</script>
  </div>

</div>

<!-- // fin -->
<?php Pgclose(); ?>
</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
