<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
        $anneeScolaire=$_POST["anneeScolaire"];
        setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}
if (is_dir('./AIimg')) nettoyage_repertoire('./AIimg');
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
include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<script language="JavaScript" src="./librairie_js/lib_affectation.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"]?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'><?php print LANGTITRE18?></font></b>
  <span id='nbeleve'></span>
</td></tr>
<tr id='cadreCentral0'>
<td>
<!-- //  debut -->

<?php
$tri='tous';
include_once('librairie_php/db_triade.php');
$cnx=cnx();
if (isset($_POST["saisie_tri"])) {
	$libelle=libelleTrimestre($_POST["saisie_tri"]);
	$tri=$_POST["saisie_tri"];
	$anneeScolaire=$_POST["anneeScolaire"];
}
?>

<div style="display:flex;flex-direction:column;gap:16px;padding:12px 8px">

<!-- Filtre -->
<div class="toolbar">
  <form method='post' action='affectation_visu.php' style="display:flex;align-items:center;flex-wrap:wrap;gap:8px">
    <span class="form-label" style="margin:0"><?php print LANGMESS340 ?> :</span>
    <select name="saisie_tri" class="form-control" style="width:auto">
      <?php if (isset($_POST["saisie_tri"])): ?>
        <option value='<?php print $_POST["saisie_tri"] ?>'><?php print $libelle ?></option>
      <?php endif; ?>
      <option value='tous'><?php print LANGMESS341 ?></option>
      <option value='trimestre1'><?php print LANGMESS342 ?></option>
      <option value='trimestre2'><?php print LANGMESS343 ?></option>
      <option value='trimestre3'><?php print LANGMESS344 ?></option>
    </select>
    <span class="form-label" style="margin:0"><?php print LANGBULL3 ?> :</span>
    <select name="anneeScolaire" class="form-control" style="width:auto">
      <?php filtreAnneeScolaireSelect($anneeScolaire); ?>
    </select>
    <button type="submit" class="btn btn-primary" style="padding:4px 14px;font-size:12px">
      <i class="bi bi-funnel"></i> <?php print VALIDER ?>
    </button>
  </form>
</div>

<!-- Tableau des classes -->
<div class="card">
  <table class="table" style="margin:0">
    <thead>
      <tr>
        <th class="cc-th"><?php print ucwords(LANGPER25) ?></th>
        <th class="cc-th" style="text-align:center;width:120px"><?php print LANGPER16."&nbsp;".ucwords(LANGBULL32) ?></th>
        <th class="cc-th" style="text-align:center;width:120px"><?php print LANGPER16."&nbsp;".ucwords(LANGBULL31) ?></th>
        <th class="cc-th" style="text-align:center;width:100px"><?php print ucwords(LANGPER26) ?></th>
      </tr>
    </thead>
    <tbody>
    <?php
    $nbeleveTotal=0;
    verif_table_classe();
    verif_table_groupe();
    $data=visu_affectation_2($tri,$anneeScolaire);
    for($i=0;$i<countTriade($data);$i++) {
        $nbeleve=nbEleve($data[$i][0],$anneeScolaire);
        $nbeleveTotal+=$nbeleve;
        $classe=chercheClasse($data[$i][0]);
    ?>
      <tr class="cc-tr-data">
        <td><?php print ucwords($classe[0][1]); ?></td>
        <td style="text-align:center"><?php print nbMatiere2($data[$i][0],$anneeScolaire); ?></td>
        <td style="text-align:center"><?php print $nbeleve; ?></td>
        <td style="text-align:center">
          <form method="post" name="formulaire<?php print $i?>" action="affectation_visu2.php" style="margin:0">
            <input type="hidden" name="saisie_classe" value="<?php print $data[$i][0]?>">
            <input type="hidden" name="saisie_tri"    value="<?php print $tri ?>">
            <input type="hidden" name="anneeScolaire" value="<?php print $anneeScolaire ?>">
            <button type="submit" style="background:none;border:1px solid #3949ab;border-radius:4px;color:#3949ab;padding:3px 10px;font-size:11px;font-weight:600;cursor:pointer;white-space:nowrap">
              <i class="bi bi-eye"></i> <?php print LANGPER27?>
            </button>
          </form>
        </td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
</div>

<div>
  <script language=JavaScript>buttonMagicRetour("javascript:history.back()");</script>
</div>

</div>

<script>
document.getElementById('nbeleve').innerHTML =
  " <font id='color2'><b><?php print $nbeleveTotal ?></b></font>" +
  "<font id='menumodule1'> <?php print LANGTMESS464 ?></font>";
</script>

<?php unset($data); Pgclose(); ?>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
