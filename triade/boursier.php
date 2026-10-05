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
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print htmlspecialchars($_SESSION["nom"])." ".htmlspecialchars($_SESSION["prenom"]) ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Boursiers</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<?php
$data = visu_affectation();
$nbsomme = 0; $somme = 0; $nbBoursierTotal = 0;
?>

<table class="brs-table">
  <thead>
    <tr>
      <th class="brs-th">Classe</th>
      <th class="brs-th brs-th-num">Boursiers</th>
      <th class="brs-th brs-th-num">Taux</th>
    </tr>
  </thead>
  <tbody>
  <?php for ($i = 0; $i < countTriade($data); $i++) {
      $nbEleve    = nb_eleve($data[$i][0]);
      $nbBoursier = nbBoursier($data[$i][0]);
      $taux       = tauxBoursier($data[$i][0], $nbEleve);
      $val        = number_format($taux, 2, '.', '');
      if ($val > 0) $nbsomme++;
      $somme          += $val;
      $nbBoursierTotal += $nbBoursier;
      $tauxClass = ($val > 0) ? " brs-td-nonzero" : "";
  ?>
    <tr class="brs-tr">
      <td class="brs-td"><?php print htmlspecialchars($data[$i][1]) ?></td>
      <td class="brs-td brs-td-num<?php print $tauxClass ?>"><?php print $nbBoursier ?></td>
      <td class="brs-td brs-td-num<?php print $tauxClass ?>"><?php print $val ?>&nbsp;%</td>
    </tr>
  <?php } ?>
  </tbody>
  <tfoot>
    <tr class="brs-tr-total">
      <td class="brs-td brs-td-total-lbl">Moyenne</td>
      <?php
      $moyenne = ($nbsomme > 0) ? $somme / $nbsomme : 0;
      $valMoy  = number_format($moyenne, 2, ',', '');
      ?>
      <td class="brs-td brs-td-num brs-td-total"><?php print $nbBoursierTotal ?></td>
      <td class="brs-td brs-td-num brs-td-total"><?php print $valMoy ?>&nbsp;%</td>
    </tr>
  </tfoot>
</table>

</td></tr></table>
<?php
if (in_array($_SESSION["membre"], ["menuadmin","menuscolaire"])) :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
Pgclose();
?>
</BODY></HTML>
