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
$anneeScolaire=$_POST["anneeScolaire"];
setcookie("anneeScolaire",$anneeScolaire,time()+3600*24*30);
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css-v4-2.css">
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script type='text/javascript' src='./librairie_js/ajax-moyenne.js'></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGPROFB1 ?> </font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("profadmin");
include_once('librairie_php/recupnoteperiode.php');

$tri=$_POST["choix_trimestre"];
$idclasse=$_POST["sClasseGrp"];

$listTmp=explode(":",$idclasse);
unset($HPV['cgrp']);
$idclasse=$listTmp[0];
$HPV['gid']=$listTmp[1];
unset($listTmp);

if (isset($_POST["valide"])) {
	$saisie_text=$_POST["saisie_text"];
	$saisie_matiere=$_POST["saisie_matiere"];
	$tri=$_POST["saisie_trimestre"];
	$idclasse=$_POST["saisie_classe"];
	$anneeScolaire=$_POST["anneeScolaire"];
	$nb=$_POST["nb"];

	$listTmp=explode(":",$idclasse);
	unset($HPV['cgrp']);
	$idclasse=$listTmp[0];
	$HPV['gid']=$listTmp[1];
	unset($listTmp);

	for($i=0;$i<$nb;$i++) {
		if (!array_key_exists("saisie_text_$i",$_POST)) continue;
		$value=$_POST["saisie_text_$i"];
		$saisie_matiere=$_POST["saisie_matiere_$i"];
		enr_commentaire_classe($value,$saisie_matiere,$tri,$idclasse,$anneeScolaire);
	}
	$message=LANGABS28;
}

$dateRecup=recupDateTrimByIdclasse("trimestre1",$idclasse,$anneeScolaire);
for($j=0;$j<countTriade($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebutT1=dateForm($dateDebut);
$dateFinT1=dateForm($dateFin);

$dateRecup=recupDateTrimByIdclasse("trimestre2",$idclasse,$anneeScolaire);
for($j=0;$j<countTriade($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebutT2=dateForm($dateDebut);
$dateFinT2=dateForm($dateFin);

$dateRecup=recupDateTrimByIdclasse("trimestre3",$idclasse,$anneeScolaire);
for($j=0;$j<countTriade($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebutT3=dateForm($dateDebut);
$dateFinT3=dateForm($dateFin);
?>

<?php if ($message): ?>
<div style="text-align:center;font-size:13px;font-weight:700;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;margin:8px 0;">
  <?php print $message ?>
</div>
<?php endif ?>

<div class="na-card" style="margin-bottom:8px;">
  <div class="na-row">
    <span class="na-lbl">Trimestre / Semestre :</span>
    <b><?php print preg_replace('/trimestre/','',$tri) ?></b>
    &nbsp;&mdash;&nbsp;
    <span class="na-lbl"><?php print LANGBULL3 ?> :</span>
    <b><?php print $anneeScolaire ?></b>
  </div>
  <div class="na-row" style="gap:16px;flex-wrap:wrap;">
    <span style="font-size:12px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;color:#333;">
      Moy. classe :
      <b><span id="m1"></span></b> <span style="color:#888;">(1er trim.)</span>
      &nbsp;
      <b><span id="m2"></span></b> <span style="color:#888;">(2e trim.)</span>
      &nbsp;
      <b><span id="m3"></span></b> <span style="color:#888;">(3e trim.)</span>
    </span>
  </div>
</div>

<form method=post name="form">
<div class="na-card">
<table border='1' width='100%' style="border-collapse:collapse;">
<?php

include_once('librairie_php/recupnoteperiode.php');

$ordre=ordre_matiere_visubull($idclasse);
$idEleve=$ideleve;
$idClasse=$idclasse;

for($i=0;$i<countTriade($ordre);$i++) {
	$matiere=chercheMatiereNom($ordre[$i][0]);
	$nomprof=recherche_personne($ordre[$i][1]);
	$idMatiere=$ordre[$i][0];
	$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
	$profAff=recherche_personne($ordre[$i][1]);

	if (verifsousmatierebull($idMatiere)) { continue; }

	print "<tr>";
	print "<td style='padding:6px;vertical-align:top;'><input type=text readonly value='".trunchaine(strtoupper($matiere),50)."' size=40 title=\"$matiere\" class='bouton2'>";
	print "<br><br><i style='font-size:11px;color:#555;'> ".trunchaine(trim($profAff),50)." </i></td>";

	$commentaire=cherche_com_classe_matiere($idMatiere,$tri,$idclasse,$anneeScolaire);
	$commentaire=preg_replace('/"/',"&rdquo;",$commentaire);

	print "<td style='padding:6px;'>";
	print "<input type=hidden name='saisie_matiere_$i' value='$idMatiere' >";

	if (defined("NBCARBULL")) { $nbcar=NBCARBULL; }else{ $nbcar=400; }
	if ($typecom > 0) { $nbcar=150; }
	print "<input type='text' name='CharRestant_$i' size='2' disabled='disabled'> ($nbcar caractères maximum)<br>";
	$disabled="";
	if ($idprof != $_SESSION["id_pers"]) $disabled="disabled='disabled'";
	print "<textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestant_$i)\" rows='5' name='saisie_text_$i' $disabled style='width:100%;box-sizing:border-box;'>$commentaire</textarea></td>";
	print "</tr>";
}

?>
</table>

<input type='hidden' name="saisie_classe" value="<?php print $idclasse ?>">
<input type='hidden' name="anneeScolaire" value="<?php print $anneeScolaire ?>">
<input type='hidden' name="saisie_trimestre" value="<?php print $tri ?>">
<input type='hidden' name="nb" value="<?php print countTriade($ordre) ?>">

<div class="na-foot">
  <button type="submit" name="valide" value="1" class="btn-enr" onclick="this.value='Veuillez patienter'">Enregistrer</button>
</div>
</div>
<br>
<script language="JavaScript">buttonMagicRetour('bulletin_comm_classe.php','_self')</script>
<br>
</form>

<img src="image/commun/indicator.gif" style="visibility:hidden">
<?php Pgclose(); ?>
<script>RecupMoyenne('<?php print "trimestre1" ?>','<?php print $idclasse?>','m1','<?php print $anneeScolaire ?>')</script>
<script>RecupMoyenne('<?php print "trimestre2"?>','<?php print $idclasse?>','m2','<?php print $anneeScolaire ?>')</script>
<script>RecupMoyenne('<?php print "trimestre3"?>','<?php print $idclasse?>','m3','<?php print $anneeScolaire ?>')</script>
<?php
if ($okenr == 1) {
	alertJs(LANGDONENR);
}
?>

<br>
<!-- // fin  -->
</td></tr></table>
<?php
       if ($_SESSION['membre'] == "menuadmin") :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
            print "</SCRIPT>";

       endif;
?>
</BODY>
</HTML>
