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
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
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
if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
	$profpclasse=$_SESSION["profpclasse"];
	validerequete("menuprof");
}else{
	validerequete("2");
}
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGABS58 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<?php
if(isset($_POST["supp_retard"])) {
	$cr=suppression_retard($_POST["saisie_eleve_id"],$_POST["saisie_heure_ret"],$_POST["saisie_date_ret"]);
	if(!$cr){ error(0); }
}
if(isset($_POST["supp_absence"])) {
	$cr=suppression_absence($_POST["saisie_eleve_id_2"],$_POST["saisie_date_ret_2"],$_POST["saisie_time"],$_POST["saisie_matiere"]);
	if(!$cr){ error(0); }
}
?>

<?php
$motif=strtolower(trim($_POST["saisie_nom_eleve"]));
$sql=<<<EOF
SELECT c.libelle,e.nom,e.prenom,e.elev_id
FROM {$prefixe}eleves e, {$prefixe}classes c
WHERE lower(e.nom) LIKE '%$motif%'
AND c.code_class = e.classe
ORDER BY c.libelle, e.nom, e.prenom
EOF;
$res=execSql($sql);
$data=chargeMat($res);

if(countTriade($data) <= 0) {
	print("<br><center><b>".LANGDISP1."</b></center><br>");
}else{
for($i=0;$i<countTriade($data);$i++) {
?>

<div class="na-card" style="margin:8px 0 4px;">
  <div style="display:flex;gap:16px;flex-wrap:wrap;align-items:center;">
    <div><span class="na-lbl"><?php print LANGTP1 ?> :</span> <b><?php print ucwords(trim($data[$i][1])) ?></b> <span style="color:#c62828;font-size:12px;">(<?php print LANGCALEN7 ?> : <?php print trim($data[$i][0]) ?>)</span></div>
    <div><span class="na-lbl"><?php print LANGTP2 ?> :</span> <b><?php print ucwords(trim($data[$i][2])) ?></b></div>
    <div style="font-size:12px;color:#555;"><?php print LANGABS64 ?></div>
  </div>
</div>

<table border="1" bordercolor="#dde0f0" width="100%" style="border-collapse:collapse;">
<tr>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:20%;"><?php print LANGABS13 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:20%;"><?php print LANGPARENT17 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:15%;"><?php print LANGABS60 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;"><?php print LANGDISP2 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:15%;"><?php print LANGBT50 ?></td>
</tr>
<?php
$data_2=affRetard($data[$i][3]);
for($j=0;$j<countTriade($data_2);$j++) {
	$matiere=chercheMatiereNom($data_2[$j][7]);
	if (($matiere == "") || ($matiere < 0)) { $matiere=""; }
?>
<TR class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
<form method="POST">
<td align="center" valign="top" style="padding:4px 6px;"><?php print date_jour(dateForm($data_2[$j][2])) ?><br><?php print dateForm($data_2[$j][2]) ?></td>
<td align="center" valign="top" style="padding:4px 6px;"><?php print $data_2[$j][1] ?><br>(<?php print trunchaine($matiere,11) ?>)</td>
<td align="center" valign="top" style="padding:4px 6px;"><?php if ($data_2[$j][5] == 0) { print "???"; }else{ print $data_2[$j][5]; } ?></td>
<?php $motiftext=$data_2[$j][6]; if ($data_2[$j][6] == "inconnu") { $motiftext=LANGINCONNU; } if (trim($data_2[$j][6]) == "0") { $motiftext=LANGINCONNU; } ?>
<td valign="top" style="padding:4px 6px;"><?php print $motiftext ?></td>
<td align="center" valign="top" style="padding:4px 6px;">
<button type="submit" name="supp_retard" class="btn-enr" style="background:#c62828;">Supprimer</button>
<input type="hidden" name="saisie_eleve_id" value="<?php print $data[$i][3] ?>">
<input type="hidden" name="saisie_heure_ret" value="<?php print $data_2[$j][1] ?>">
<input type="hidden" name="saisie_date_ret" value="<?php print $data_2[$j][2] ?>">
<input type="hidden" name="saisie_nom_eleve" value="<?php print $data[$i][1] ?>">
</td>
</form>
</TR>
<?php
	if ($j == 4) { break; }
}
?>
</table>
<br>
<form method="post" action="gestion_abs_retard_liste.php">
<input type="hidden" name="saisie_nom_eleve" value="<?php print $data[$i][3] ?>">
<span style="display:flex;"><script language=JavaScript>buttonMagicSubmit("Visualiser le(s) <?php print countTriade($data_2) ?> retard(s)","rien");</script></span>
<br><br>
</form>

<table border="1" bordercolor="#dde0f0" width="100%" style="border-collapse:collapse;">
<tr>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:20%;"><?php print LANGPARENT8 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:20%;"><?php print LANGABS60 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:15%;"><?php print LANGABS63 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;"><?php print LANGABS12 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:15%;"><?php print LANGBT50 ?></td>
</tr>
<?php
$data_3=affAbsence($data[$i][3]);
for($j=0;$j<countTriade($data_3);$j++) {
?>
<TR class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
<form method="POST">
<td align="center" valign="top" style="padding:4px 6px;"><?php print date_jour(dateForm($data_3[$j][1])) ?><br><?php print dateForm($data_3[$j][1]) ?></td>
<td align="center" valign="top" style="padding:4px 6px;">
<?php if ($data_3[$j][4] >= 0) { print $data_3[$j][4]."  Jour(s)"; }else{ print $data_3[$j][7]."h"; } ?>
</td>
<td align="center" valign="top" style="padding:4px 6px;"><?php print dateForm($data_3[$j][2]) ?></td>
<?php $motiftext=$data_3[$j][6]; if ($data_3[$j][6] == "inconnu") { $motiftext=LANGINCONNU; } if ($data_3[$j][6] == "0") { $motiftext=LANGINCONNU; } ?>
<td valign="top" style="padding:4px 6px;"><?php print $motiftext ?></td>
<td align="center" valign="top" style="padding:4px 6px;">
<button type="submit" name="supp_absence" class="btn-enr" style="background:#c62828;"><?php print LANGBT50 ?></button>
<input type="hidden" name="saisie_eleve_id_2" value="<?php print $data[$i][3] ?>">
<input type="hidden" name="saisie_date_ret_2" value="<?php print $data_3[$j][1] ?>">
<input type="hidden" name="saisie_nom_eleve" value="<?php print $data[$i][1] ?>">
<input type="hidden" name="saisie_time" value="<?php print $data_3[$j][9] ?>">
<input type="hidden" name="saisie_matiere" value="<?php print $data_3[$j][8] ?>">
</td>
</form>
</TR>
<?php
	if ($j == 4) { break; }
}
?>
</table>
<br>
<form method="post" action="gestion_abs_absence_liste.php">
<input type="hidden" name="saisie_nom_eleve" value="<?php print $data[$i][3] ?>">
<span style="display:flex;"><script language=JavaScript>buttonMagicSubmit("Visualiser le(s) <?php print countTriade($data_3) ?> absence(s)","rien");</script></span>
</form>
<br>
<form method="post" action="gestion_abs_retard_impr.php">
<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:0;">
  <button type="submit" class="btn-enr">Imprimer Rtd/Abs de <?php print ucwords(trim($data[$i][1]))." ".ucwords(trim($data[$i][2])) ?></button>
  <button type="button" class="btn-retour" onclick="open('gestion_abs_retard.php','_self','')">Retour menu</button>
</div>
<input type="hidden" name="idEleve" value="<?php print $data[$i][3] ?>">
</form>
<hr style="border:none;border-top:1px solid #c5cae9;margin:16px 0;">
<br>
<?php
}
}
?>

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
