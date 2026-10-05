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
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/prevwong/drooltip.js@master/package/css/drooltip.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<style>
@media print {
  .no-print { display:none !important; }
  #coulBar0 { background:none !important; color:#000 !important; }
}
</style>
<script src="https://cdn.jsdelivr.net/gh/prevwong/drooltip.js@master/package/js/build/drooltip.js"></script>
<script language="JavaScript" src="./librairie_js/lib_affectation.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"]?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
$cnx=cnx();

$saisie_tri    = isset($_POST["saisie_tri"])    ? $_POST["saisie_tri"]    : '';
$anneeScolaire = isset($_POST["anneeScolaire"]) ? $_POST["anneeScolaire"] : '';
$saisie_classe = isset($_POST["saisie_classe"]) ? $_POST["saisie_classe"] : '';

if (isset($_GET["saisie_tri"]))    { $saisie_tri    = $_GET["saisie_tri"]; }
if (isset($_GET["annee_scolaire"])){ $anneeScolaire = $_GET["annee_scolaire"]; }
if (isset($_GET["saisie_classe"])) { $saisie_classe = $_GET["saisie_classe"]; }

$classe  = chercheClasse($saisie_classe);
$libelle = libelleTrimestre($saisie_tri);
$data    = visu_affectation_detail_2($saisie_classe,$saisie_tri,$anneeScolaire);

$urlBase = "affectation_visu2.php?saisie_tri=".urlencode($saisie_tri)."&saisie_classe=".urlencode($saisie_classe)."&annee_scolaire=".urlencode($anneeScolaire);
$urlVisu = $urlBase."&visu";
$urlImpr = $urlVisu."&imprime";
?>

<?php if (!isset($_GET["visu"])): ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<?php endif; ?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85" style="table-layout:fixed">
<tr id='coulBar0'>
  <td height="2">
    <div style="display:flex;justify-content:space-between;align-items:center">
      <b><font id='menumodule1'><?php print LANGPER28 ?>
        <font id="color2"><?php print $classe[0][1] ?></font>
      </font></b>
      <?php if (!isset($_GET["visu"])): ?>
      <a href="<?php print $urlBase ?>"
         onclick="open('<?php print $urlVisu ?>','visu','width=800,height=600,resizable=yes,scrollbars=yes'); return false;"
         style="color:#fce4ba;font-size:16px;text-decoration:none;margin-right:4px" title="Agrandir">
        <i class="bi bi-arrows-fullscreen"></i>
      </a>
      <?php endif; ?>
    </div>
  </td>
</tr>
<tr id='cadreCentral0'>
<td>
<div style="overflow:hidden">
<!-- //  debut -->

<div style="display:flex;flex-direction:column;gap:12px;padding:10px 6px">

<?php if (!isset($_GET["imprime"])): ?>
<div class="toolbar no-print" style="display:flex;align-items:center;flex-wrap:wrap;gap:8px">
  <script>buttonMagic("Imprimer",'<?php print $urlImpr ?>','imp','width=800,height=600,resizable=yes,scrollbars=yes','')</script>
  <script>buttonMagicRetour("affectation_visu.php","_self")</script>
  <span class="form-label" style="margin:0;font-weight:600">
    Ann&eacute;e Scolaire : <span style="color:#080A66"><?php print $anneeScolaire ?></span>
  </span>
</div>
<?php endif; ?>

<?php if (($saisie_tri != "tous") && ($saisie_tri != "")): ?>
<div style="color:#3949ab;font-size:12px;font-style:italic"><?php print $libelle ?></div>
<?php endif; ?>

<div style="overflow-x:auto;min-width:0">
<table class="table" style="min-width:900px;margin:0">
  <thead>
    <tr>
      <th class="cc-th"><?php print LANGPER17 ?></th>
      <th class="cc-th"><?php print LANGPER18 ?></th>
      <th class="cc-th" style="text-align:center"><?php print LANGPER19 ?></th>
      <th class="cc-th"><?php print LANGPER20 ?></th>
      <th class="cc-th" style="text-align:center">Lang.</th>
      <th class="cc-th" style="text-align:center"><?php print LANGMESS363 ?>&nbsp;1<i>*</i></th>
      <th class="cc-th" style="text-align:center">Visu&nbsp;2<i>**</i></th>
      <th class="cc-th" style="text-align:center">Nb&nbsp;H.</th>
      <th class="cc-th" style="text-align:center">ECTS</th>
      <th class="cc-th"><?php print LANGMESS364 ?></th>
      <th class="cc-th" style="text-align:center"><?php print LANGTMESS470 ?></th>
      <th class="cc-th" style="text-align:center">Info Sem. <i class="bi bi-info-circle dtt" title="Numéro de semestre de la matière (1 à 10)" style="font-size:12px;color:#3949ab;cursor:pointer"></i></th>
      <th class="cc-th" style="text-align:center">Coef<br>certif.</th>
      <th class="cc-th" style="text-align:center">Note<br>plancher</th>
    </tr>
  </thead>
  <tbody>
  <?php for($i=0;$i<countTriade($data);$i++):
    $ue=$data[$i][11];
    $visuOk   = '<i class="bi bi-check-circle-fill" style="color:#2e7d32;font-size:14px"></i>';
    $visuNon  = '<span style="color:#aaa;font-size:11px">non</span>';
  ?>
    <tr class="cc-tr-data">
      <td><?php print stripslashes(ucwords(chercheMatiereNom($data[$i][1]))) ?></td>
      <td><?php print recherche_personne($data[$i][2]) ?></td>
      <td style="text-align:center"><?php print (trim($data[$i][4]) == "") ? "&nbsp;" : $data[$i][4] ?></td>
      <td><?php print (trim($data[$i][5]) == "") ? "&nbsp;" : $data[$i][5] ?></td>
      <td style="text-align:center"><?php print preg_replace('/^0$/','',$data[$i][6]) ?></td>
      <td style="text-align:center"><?php print ($data[$i][8]  == 1) ? $visuOk : $visuNon ?></td>
      <td style="text-align:center"><?php print ($data[$i][14] == 1) ? $visuOk : $visuNon ?></td>
      <td style="text-align:center"><?php print (trim($data[$i][9])  == "") ? "&nbsp;" : $data[$i][9] ?></td>
      <td style="text-align:center"><?php print trim($data[$i][10]) ?></td>
      <td><?php
        if ($ue > 0) {
          $tab=$tab=recupNomUE($ue);
          $nom_ue=trunchaine($tab[0][0],40);
          print stripslashes($nom_ue);
        } else { print "&nbsp;"; }
      ?></td>
      <td style="text-align:center"><?php if ($data[$i][12] == "etudedecasipac") print LANGTMESS471 ?></td>
      <td style="text-align:center"><?php print trim($data[$i][15]) ?></td>
      <td style="text-align:center"><?php print trim($data[$i][17]) ?></td>
      <td style="text-align:center"><?php print trim($data[$i][18]) ?></td>
    </tr>
  <?php endfor; ?>
  </tbody>
</table>
</div>

<div style="font-style:italic;font-size:11px;color:#888">
  <?php print LANGTMESS472 ?> / ** Visu 2 : pour Config BTS Blanc et Pigier Partiel Paris
</div>

</div>
</div>
<!-- // fin  -->
<?php Pgclose(); ?>
</td></tr></table>

<?php if (!isset($_GET["visu"])): ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php endif; ?>
<?php if (isset($_GET["imprime"])): ?>
<script>window.print();</script>
<?php endif; ?>
<script>new Drooltip({element:".dtt",position:"top",animation:"fade"});</script>

</BODY></HTML>
