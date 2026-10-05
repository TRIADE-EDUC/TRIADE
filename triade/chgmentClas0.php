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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include("./librairie_php/lib_licence.php");
if (empty($_SESSION["adminplus"])) {
    print "<script>location.href='./base_de_donne_key.php'</script>";
    exit;
}
include_once("librairie_php/db_triade.php");
$cnx = cnx();

$data = array();
$cl   = '';
if (isset($_POST["consult"])) {
    $saisie_classe = $_POST["saisie_classe"];
    $annee         = $_POST["annee"];
    $sql = "SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM {$prefixe}eleves e,{$prefixe}classes c WHERE e.classe='$saisie_classe' AND c.code_class='$saisie_classe' AND (e.annee_scolaire='$annee' OR e.annee_scolaire IS NULL) ORDER BY e.nom,e.prenom";
    $res  = execSql($sql);
    $data = chargeMat($res);
    $cl   = isset($data[0][0]) ? $data[0][0] : '';
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'><?php print LANGBASE23 ?></font></b>
</td></tr>
<tr id='cadreCentral0'>
<td>
<!-- // debut -->

<div style="display:flex;flex-direction:column;gap:12px;padding:10px 6px">

  <!-- Sélection classe par classe -->
  <div class="card">
    <div class="card-header">
      <span class="card-title"><i class="bi bi-person-lines-fill" style="margin-right:5px"></i><?php print LANGELE4 ?></span>
    </div>
    <div class="card-body">
      <form method="post" name="formulaire">
        <div style="display:flex;flex-direction:column;gap:8px">
          <div class="form-row">
            <label class="form-label"><?php print "Année scolaire en cours" ?> :</label>
            <select name="annee" class="form-control" style="width:auto">
              <?php filtreAnneeScolaireSelectNote('', 4) ?>
              <option value=""><?php print LANGMESS379 ?></option>
            </select>
          </div>
          <div class="form-row" style="border-bottom:none">
            <label class="form-label"><?php print LANGELE4 ?> :</label>
            <select name="saisie_classe" class="form-control" style="width:auto">
              <option style="color:#000066;background:#FCE4BA"><?php print LANGCHOIX ?></option>
              <?php select_classe(); ?>
            </select>
          </div>
        </div>
        <div style="margin-top:10px">
          <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","consult")</script>
        </div>
      </form>
    </div>
  </div>

  <?php if (isset($_POST["consult"])): ?>
  <!-- Résultats classe par classe -->
  <?php if (countTriade($data) <= 0): ?>
  <div class="alert" style="background:#fff3e0;border:1px solid #ffb74d;border-radius:6px;padding:10px 14px;font-size:12px;color:#e65100;font-weight:600">
    <i class="bi bi-info-circle"></i> <?php print LANGPROJ6 ?>
  </div>
  <?php else: ?>
  <div class="card">
    <div class="card-header" style="justify-content:space-between">
      <span class="card-title">
        <i class="bi bi-arrow-left-right" style="margin-right:5px"></i>
        <?php print LANGBASE26 ?> <span style="color:#3949ab;text-transform:none"><?php print $cl ?></span>
      </span>
      <a href="#" onclick="open('chgmentClashelp.php','help','width=450,height=340');return false"
         style="font-size:11px;color:#3949ab;display:flex;align-items:center;gap:4px;text-decoration:none">
        <i class="bi bi-question-circle"></i> <?php print LANGBASE25 ?>
      </a>
    </div>
    <div class="card-body" style="padding:10px 12px">
      <form method="post" action="chgmentClas1.php" name="formulairec">
        <script>
        function valideSelect(choix) {
          for (var i = 0; i < <?php print countTriade($data) ?>; i++) {
            var el = document.getElementById('new_classe_' + i);
            if (choix != "") {
              el.disabled = false;
            } else {
              el.disabled = true;
              el.selectedIndex = 0;
            }
          }
        }
        </script>

        <div style="margin-bottom:10px;display:flex;align-items:center;gap:8px;flex-wrap:wrap">
          <label style="font-size:12px;font-weight:600;color:#080A66;white-space:nowrap">
            <?php print "Année scolaire future" ?> :
          </label>
          <select name="anneefutur" class="form-control" style="width:auto"
                  onchange="valideSelect(this.value)">
            <?php filtreAnneeScolaireSelectNote('', 2) ?>
          </select>
        </div>

        <div style="overflow-x:auto">
          <table class="table" style="font-size:11px">
            <thead>
              <tr>
                <th class="cc-th"><?php print LANGNA1 ?></th>
                <th class="cc-th"><?php print LANGNA2 ?></th>
                <th class="cc-th"><?php print LANGBASE36 ?></th>
              </tr>
            </thead>
            <tbody>
              <?php for ($i = 0; $i < countTriade($data); $i++): ?>
              <tr class="cc-tr-data">
                <td>
                  <?php print infoBulleEleveSansLoupe($data[$i][1], strtoupper($data[$i][2])) ?>
                  <input type="hidden" name="idEleve_<?php print $i ?>" value="<?php print $data[$i][1] ?>">
                </td>
                <td><?php print ucwords($data[$i][3]) ?></td>
                <td>
                  <select id="new_classe_<?php print $i ?>" name="new_classe_<?php print $i ?>" disabled="disabled"
                          style="border:1px solid #c5cae9;border-radius:4px;font-size:11px;padding:2px 4px;width:100%">
                    <option value="rien"  style="color:#000066;background:#FCE4BA"><?php print LANGCHOIX ?></option>
                    <option value="quit"  style="color:red;background:#fff"><?php print LANGBASE37 ?></option>
                    <option value="sansclasse" style="color:#000066;background:#fffde7"><?php print "Sans Classe" ?></option>
                    <?php select_classe2('10'); ?>
                  </select>
                </td>
              </tr>
              <?php endfor; ?>
            </tbody>
          </table>
        </div>

        <input type="hidden" name="nbEleve" value="<?php print countTriade($data) ?>">
        <div style="margin-top:10px">
          <script language=JavaScript>buttonMagicSubmit("<?php print LANGBASE38 ?>","rien")</script>
        </div>
      </form>
    </div>
  </div>
  <?php endif; ?>
  <?php endif; ?>

  <!-- Sélection multi-classes -->
  <div class="card">
    <div class="card-header">
      <span class="card-title"><i class="bi bi-people-fill" style="margin-right:5px"></i><?php print LANGMESS381 ?></span>
    </div>
    <div class="card-body">
      <form method="post" action="chgmentClas00.php">
        <div class="form-row">
          <label class="form-label"><?php print "Année scolaire en cours" ?> :</label>
          <select name="annee" class="form-control" style="width:auto">
            <?php filtreAnneeScolaireSelectNote('', 4) ?>
            <option value=""><?php print LANGMESS379 ?></option>
          </select>
        </div>
        <div style="margin:10px 0;font-size:12px;font-weight:600;color:#080A66"><?php print LANGMESS381 ?></div>
        <div style="margin-bottom:10px">
          <?php checkbox_classe2() ?>
        </div>
        <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","consult")</script>
      </form>
    </div>
  </div>

  <div>
    <script language=JavaScript>buttonMagicRetour("chgmentClas.php","_self")</script>
  </div>

</div>

<!-- // fin -->
<?php Pgclose(); ?>
</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<script language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</script>
</BODY></HTML>
