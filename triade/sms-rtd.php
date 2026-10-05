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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print $_SESSION['nom']." ".$_SESSION['prenom'] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();
if (isset($_POST["joursms"])) {
	$datesms=dateDMY();
	$datesms=datemoinsn($datesms,$_POST["joursms"]);
}else{
	$datesms=dateDMY2();
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>
<?php print LANGSMS1 ?> <?php print dateForm($datesms) ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign="top">

<form method="post" name="formulaire">
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGEDIT19 ?> :</span>
    <select name="joursms" class="cc-select" onchange="document.forms.formulaire.submit()">
      <option value="0"><?php print LANGCHOIX ?></option>
      <option value="0"><?php print LANGEDIT14 ?></option>
      <option value="1"><?php print LANGEDIT15 ?></option>
      <option value="2"><?php print LANGEDIT16 ?></option>
      <option value="3"><?php print LANGEDIT17 ?></option>
      <option value="4"><?php print LANGEDIT18 ?></option>
      <option value="5">Depuis 5 jours</option>
      <option value="6">Depuis 6 jours</option>
      <option value="7">Depuis 7 jours</option>
      <option value="8">Depuis 8 jours</option>
      <option value="9">Depuis 9 jours</option>
      <option value="10">Depuis 10 jours</option>
      <option value="11">Depuis 11 jours</option>
      <option value="12">Depuis 12 jours</option>
      <option value="13">Depuis 13 jours</option>
      <option value="14">Depuis 14 jours</option>
    </select>
  </div>
</div>
</form>

<form method="post" action="sms-envoi.php" name="formulaire2" onSubmit="document.formulaire2.create.disabled=true">
<input type="hidden" value="en retard" name="type">
<table border="1" bordercolor="#dde0f0" width="100%" style="border-collapse:collapse;">
<tr>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;"><?php print LANGNA1 ?> <?php print LANGNA2 ?> / <?php print LANGABS22 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;width:5%;text-align:center;">SMS</td>
</tr>
<?php
$nb=0;
$data_2=affRetardNonJustifieSms($datesms);
// elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, smsenvoye, heure_saisie
for($j=0;$j<countTriade($data_2);$j++) {
	$couleur="class=\"tabnormal\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\"";
	$ideleve=$data_2[$j][0];
	$idmatiere=$data_2[$j][7];
	$smsenvoye=$data_2[$j][8];
	$date_ret=$data_2[$j][2];
	$time=$data_2[$j][9];
	$etude="";
	if ($idmatiere < 0) { $etude=" / Etude "; }
	if ($idmatiere != null) { $nomMatiere=chercheMatiereNom($idmatiere); }
	if (($data_2[$j][6] != "inconnu") && ($data_2[$j][5] != 0)) { $couleur="bgcolor='#FFFF99'"; }
	if ($smsenvoye == '1') { $smsenvoye="<br><b><i>SMS envoyé !</i></b>"; }else{ $smsenvoye=""; }
	if ($nomMatiere == "") { $nomMatiere=LANGSMS2; }
	$motiftext=$data_2[$j][6];
	if (($data_2[$j][6] == "inconnu") || ($data_2[$j][6] == "0")) { $motiftext=LANGINCONNU; }
	$motiftext=preg_replace('/"/',"",$motiftext);
	$motiftext=preg_replace("/'/","\'",$motiftext);
?>
<tr <?php print $couleur ?>>
<td valign="top" style="padding:6px;">
<img src="image_trombi.php?idE=<?php print $ideleve ?>" align="left" border="0">
<b><?php print strtoupper(recherche_eleve_nom($ideleve)) ?></b>
<?php print ucwords(strtolower(trunchaine(recherche_eleve_prenom($ideleve),10))) ?>
<br><br>
En retard <?php print $etude ?> (<?php print trunchaine($nomMatiere,50) ?>)<br><br>
Motif : <?php print $motiftext ?>
</td>
<td valign="top" style="padding:6px;">
<?php
$filtreSMS=config_param_visu('smsfiltre');
$filtreSMS=$filtreSMS[0][0];
$telok=0;
$phones=[
	['label'=>'Principal','fn'=>'cherchetel'],
	['label'=>'Tel Prof. Père','fn'=>'cherchetelpere'],
	['label'=>'Tel Prof. Mère','fn'=>'cherchetelmere'],
	['label'=>'Portable 1','fn'=>'cherchetelportable1'],
	['label'=>'Portable 2','fn'=>'cherchetelportable2'],
	['label'=>'Port Elève','fn'=>'cherchetelEleve'],
];
foreach($phones as $p) {
	$telsms=trim($p['fn']($ideleve));
	if (preg_match("/^$filtreSMS/",$telsms)) {
		$telsms=preg_replace('/ |\.|\/|-|_/',"",$telsms);
		if (is_numeric($telsms)) {
			$nb++;
			print "<u>".$p['label'].":</u><br>".$telsms."&nbsp;<input type='checkbox' name='sms[]' value='$ideleve#$telsms#$date_ret#$time'><br>";
			$telok=1;
		}
	}
}
print "<center>$smsenvoye</center>";
if ($telok==0) { print "<i>Aucun numéro</i>"; }
?>
</td>
</tr>
<?php
}
print "</table><br>";
print "<input type='hidden' name='nb' value='$nb'>";
?>
<div style="display:flex;margin:0 5px;">
  <script language=JavaScript>buttonMagicSubmit("ENVOI SMS","create");</script>
</div>
<br><br>
</form>

</td></tr></table>
<?php
if ($_SESSION["membre"] == "menuadmin") :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
Pgclose();
?>
<SCRIPT language="JavaScript">InitBulle("#FFFFFF","#009999","#FFFFFF",1);</SCRIPT>
</BODY></HTML>
