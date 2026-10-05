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
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_discipline.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION['nom']." ".$_SESSION['prenom'] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();

if(isset($_POST["creat_creneau"])):
	$cr=create_creneau($_POST["saisie_intitule"],$_POST["saisie_depH"],$_POST["saisie_finH"]);
	if($cr != 1){ alertJs("Créneau non créé, déjà en place -- Service Triade"); }
endif;

if(isset($_POST["creat_supp"])):
	$cr2=supp_creneau($_POST["saisie_int_supp"]);
	if($cr2 == 1){ alertJs("Créneau supprimé -- Service Triade"); }
endif;

if (isset($_POST["creat_default"])) {
	if ($_POST["saisie_int_default"] != "aucun") { config_param_ajout($_POST["saisie_int_default"],"creneau"); }
	if ($_POST["saisie_int_default"] == "aucun") { supp_param_creneaux(); }
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Config. créneaux horaires</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<!-- Form créer créneau -->
<form method="post">
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl">Intitulé :</span>
    <input type="text" name="saisie_intitule" maxlength="20" class="cc-select" style="width:140px;">
  </div>
  <div class="na-row">
    <span class="na-lbl">De :</span>
    <input type="text" name="saisie_depH" maxlength="8" class="cc-select" style="width:70px;" onKeyPress="onlyChar2(event)">
    <span style="font-size:11px;color:#888;font-style:italic;">(hh:mm)</span>
  </div>
  <div class="na-row">
    <span class="na-lbl">À :</span>
    <input type="text" name="saisie_finH" maxlength="8" class="cc-select" style="width:70px;" onKeyPress="onlyChar2(event)">
    <span style="font-size:11px;color:#888;font-style:italic;">(hh:mm)</span>
  </div>
</div>
<br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","creat_creneau");</script>
<br><br>
</form>

<div style="border-top:2px solid #c5cae9;margin:10px 5px;"></div>

<!-- Form supprimer créneau -->
<form method="post">
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl">Supprimer :</span>
    <select name="saisie_int_supp" class="cc-select">
      <option style='color:#000066;background-color:#FCE4BA'><?php print LANGPROJ13 ?></option>
      <?php select_creneaux(); ?>
    </select>
  </div>
</div>
<br>
<button type="submit" name="creat_supp" class="btn-enr" style="background:#c62828;margin:0 5px;">Supprimer</button>
<br><br>
</form>

<div style="border-top:2px solid #c5cae9;margin:10px 5px;"></div>

<!-- Form créneau par défaut -->
<form method="post">
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl">Par défaut :</span>
    <select name="saisie_int_default" class="cc-select">
      <?php
      $data=recupCreneauDefault("creneau");
      if (countTriade($data) > 0) {
          print "<option value='".$data[0][1]."'>".$data[0][1]."</option>";
          print "<option value='aucun'>Aucun</option>";
      }else{
          print "<option value='aucun'>Aucun</option>";
      }
      select_creneaux();
      ?>
    </select>
  </div>
</div>
<br>
<button type="submit" name="creat_default" class="btn-enr" style="margin:0 5px;"><?php print VALIDER ?></button>
<br><br>
</form>

<div style="border-top:2px solid #c5cae9;margin:10px 5px;"></div>

<!-- Tableau des créneaux -->
<table class="cc-data-table" style="margin:8px 0;">
<thead>
<tr class="cc-thead-row">
  <th class="cc-th">Nom du créneau</th>
  <th class="cc-th">Heure de départ</th>
  <th class="cc-th">Heure de fin</th>
</tr>
</thead>
<tbody>
<?php
$data=affCreneaux();
for($i=0;$i<countTriade($data);$i++) {
	print "<tr class='cc-tr-data'>";
	print "<td class='cc-td'>".$data[$i][0]."</td>";
	print "<td class='cc-td cc-td-center'>".timeForm($data[$i][1])."</td>";
	print "<td class='cc-td cc-td-center'>".timeForm($data[$i][2])."</td>";
	print "</tr>";
}
?>
</tbody>
</table>
<br>

</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
Pgclose();
?>
</BODY></HTML>
