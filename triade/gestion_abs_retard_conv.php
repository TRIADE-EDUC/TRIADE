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
<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
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
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Convertir absence ou retard</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

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
    <div style="font-size:12px;color:#555;"><?php print LANGABS62 ?></div>
  </div>
</div>

<!-- Retards à convertir -->
<table border="1" bordercolor="#dde0f0" width="100%" style="border-collapse:collapse;">
<tr>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:20%;"><?php print LANGABS13 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:20%;"><?php print LANGPARENT17 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:15%;"><?php print LANGABS60 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:20%;"><?php print LANGABS12 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:10%;">Convertir</td>
</tr>
<?php
$data_2=affRetard($data[$i][3]);
// elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, justifier, heure_saisie
for($j=0;$j<countTriade($data_2);$j++) {
	$matiere=chercheMatiereNom($data_2[$j][7]);
	if (($matiere == "") || ($matiere < 0)) { $matiere=""; }
?>
<TR class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
<form method="POST" name="formulaire_<?php print $i.$j ?>" action="gestion_abs_retard_conv2.php">
<td align="center" valign="top" style="padding:4px 6px;"><?php print date_jour(dateForm($data_2[$j][2])) ?><br><?php print dateForm($data_2[$j][2]) ?></td>
<td align="center" valign="top" style="padding:4px 6px;"><?php print $data_2[$j][1] ?><br>(<?php print trunchaine($matiere,11) ?>)</td>
<td align="center" valign="top" style="padding:4px 6px;">
<select name="saisie_duree_<?php print $i ?>" onChange="chargement_pendant('','<?php print $i ?>','<?php print $i.$j ?>')">
<option STYLE='color:#000066;background-color:#FCE4BA'></option>
</select>
<input type="hidden" onfocus="this.blur()" name="saisie_duree_retourner_<?php print $i ?>" value="<?php print $data_2[$j][5] ?>">
<?php $yy=$data_2[$j][5]; if ($data_2[$j][5] == 0) { $yy="???"; } ?>
<script langage=Javascript>chargement_pendant('<?php print trim($yy) ?>','<?php print $i ?>','<?php print $i.$j ?>');</script>
</td>
<?php
$motiftext=$data_2[$j][6];
if ($data_2[$j][6] == "inconnu") { $motiftext=LANGINCONNU; }
$motiftext=preg_replace('/"/'," ",$motiftext);
?>
<td valign="top" style="padding:4px 6px;">
<input type="text" readonly name="saisie_modif_<?php print $i ?>" value="<?php print $motiftext ?>" class="cc-select" style="width:140px;">
<label style="font-size:11px;"><input type="checkbox" <?php if ($data_2[$j][8] == 1) { print "checked='checked'"; } ?> disabled> Justifié</label>
</td>
<td align="center" valign="top" style="padding:4px 6px;">
<button type="submit" name="supp_retard" class="btn-enr">Convertir</button>
<input type="hidden" name="saisie_justifier_<?php print $i ?>" value="<?php print $data_2[$j][8] ?>">
<input type="hidden" name="saisie_eleve_id" value="<?php print $data[$i][3] ?>">
<input type="hidden" name="saisie_heure_ret" value="<?php print $data_2[$j][1] ?>">
<input type="hidden" name="saisie_date_ret" value="<?php print $data_2[$j][2] ?>">
<input type="hidden" name="saisie_id_champ" value="<?php print $i ?>">
<input type="hidden" name="saisie_nom_eleve" value="<?php print $data[$i][1] ?>">
<input type="hidden" name="conversion" value="abs">
</td>
</form>
</TR>
<?php
}
?>
</table>
<br><br>

<!-- Absences à convertir -->
<table border="1" bordercolor="#dde0f0" width="100%" style="border-collapse:collapse;">
<tr>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:20%;"><?php print LANGPARENT8 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:15%;"><?php print LANGABS60 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:15%;"><?php print LANGGRP29bis ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:20%;"><?php print LANGABS12 ?></td>
<td style="background:#ffffd5;font-weight:700;padding:4px 6px;text-align:center;width:10%;">Convertir</td>
</tr>
<?php
$data_3=affAbsence($data[$i][3]);
// elev_id, date_ab, date_saisie, origin_saisie, duree_ab, date_fin, motif, duree_heure, id_matiere, time, justifier, heure_saisie
for($j=0;$j<countTriade($data_3);$j++) {
?>
<TR class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
<form method="POST" name="formulaire_3_<?php print $i.$j ?>" action="gestion_abs_retard_conv2.php">
<td align="center" valign="top" style="padding:4px 6px;"><?php print date_jour(dateForm($data_3[$j][1])) ?><br><?php print dateForm($data_3[$j][1]) ?></td>
<td align="center" valign="top" style="padding:4px 6px;">
<select name="saisie_duree_<?php print $i ?>" onChange="chargement_pendant_jour('','<?php print $i ?>','<?php print $i.$j ?>')">
<option STYLE='color:#000066;background-color:#FCE4BA'></option>
</select>
<input type="hidden" onfocus="this.blur()" name="saisie_duree_retourner_<?php print $i ?>" value="<?php print $data_3[$j][4] ?>">
<?php
$yy=$data_3[$j][4]." J";
if ($data_3[$j][4] == 0) { $yy="???"; }
if ($data_3[$j][4] == -1) { $yy=preg_replace('/\./','H',$data_3[$j][7]); }
?>
<script langage=Javascript>chargement_pendant_jour('<?php print trim($yy) ?>','<?php print $i ?>','<?php print $i.$j ?>');</script>
</td>
<td align="center" valign="top" style="padding:4px 6px;"><?php print dateForm($data_3[$j][2]) ?><br><?php if (($data_3[$j][11] != "") && ($data_3[$j][11] != "00:00:00")) { print timeForm($data_3[$j][11]); } ?></td>
<?php $motiftext=$data_3[$j][6]; if ($data_3[$j][6] == "inconnu") { $motiftext=LANGINCONNU; } $motiftext=preg_replace('/"/'," ",$motiftext); ?>
<td valign="top" style="padding:4px 6px;">
<input type="text" name="saisie_modif_<?php print $i ?>" value="<?php print $motiftext ?>" class="cc-select" style="width:140px;">
<label style="font-size:11px;"><input type="checkbox" name="saisie_justifier_<?php print $i ?>" value="1" <?php if ($data_3[$j][10] == 1) { print "checked='checked'"; } ?> disabled> Justifié</label>
</td>
<td align="center" valign="top" style="padding:4px 6px;">
<button type="submit" class="btn-enr">Convertir</button>
<input type="hidden" name="saisie_eleve_id" value="<?php print $data[$i][3] ?>">
<input type="hidden" name="saisie_date_ret" value="<?php print $data_3[$j][1] ?>">
<input type="hidden" name="saisie_nom_eleve" value="<?php print $data[$i][1] ?>">
<input type="hidden" name="saisie_id_champ" value="<?php print $i ?>">
<input type="hidden" name="saisie_time" value="<?php print $data_3[$j][9] ?>">
<input type="hidden" name="saisie_matiere" value="<?php print $data_3[$j][8] ?>">
<input type="hidden" name="origine_saisie" value="<?php print $data_3[$j][3] ?>">
<input type="hidden" name="conversion" value="rtd">
</td>
</form>
</TR>
<?php
}
?>
</table>
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
