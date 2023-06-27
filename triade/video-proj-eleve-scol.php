<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<script language="JavaScript" src="librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/scriptaculous.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<title>Triade Vidéo-Projecteur</title>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<br>
<table border='0' width=95% align=center  cellspacing=2>
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
if ($_SESSION["membre"] == "menupersonnel") {
	if ((!verifDroit($_SESSION["id_pers"],"ficheeleve")) && (!verifDroit($_SESSION["id_pers"],"videoprojo"))) {
		Pgclose();
		accesNonReserveFen();
		exit();
	}
}else{
	validerequete("3");
}



$ideleve=$_GET["saisie_eleve"];
$idclasse=$_GET["saisie_classe"];
$trim_en_cours=$_GET["trimestre"];

$dateRecup=recupDateTrimByIdclasse($trim_en_cours,$idclasse);
for($j=0;$j<count($dateRecup);$j++) {
        $dateDebut=$dateRecup[$j][0];
        $dateFin=$dateRecup[$j][1];
}


if ($ideleve == "") {
	print "<tr><td align=center><br><br><b><font size=3>".LANGPROJ6."</font></b></td></tr>";

}else {


	$data_2=nombre_abs($ideleve,$dateDebut,$dateFin);
	// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure
	$cumulabs=0;
	$cumulabsheure=0;
	$listing="";
	for($j=0;$j<count($data_2);$j++) {
		$listing.="- ".dateForm($data_2[$j][1])." saisie par ".$data_2[$j][3]." / motif : ".$data_2[$j][6]."<br>";
		if ($data_2[$j][4] > 0) {
			$cumulabs=$cumulabs + $data_2[$j][4];
		}else {
			$cumulabsheure= $cumulabsheure + $data_2[$j][7];
		}
	}
?>
<div id="affabs" style="position:absolute;top:100px;left:50px;display:none;width:350px;height:180px;padding:20px;border:1px #666 solid;background-color:#ddd;z-index:1000;overflow:auto">
<font class=T2><b>Listing des absences de cette période.</b></font><br /><br />
<?php print $listing ?><br><br>
<input type='button' value='<?php print LANGFERMERFEN ?>' class='button' onclick="document.getElementById('affabs').style.display='none';" /><br><br><br>
</div>


<tr><td colspan=3> <font size=3><b><u><?php print "Absentéisme"?></u></b></font><br><br></td></tr>
	<tr>
	<td width=43% align=right><font size=3><?php print "Nbr&nbsp;d'absences&nbsp;"?>&nbsp;:&nbsp;</font></td>
	<td align=left><font size=3><b>&nbsp;&nbsp;<?php print count($data_2)?></b></font>
	<?php 
	if (count($data_2)) {
		print "&nbsp;&nbsp;[ <a href='#' onclick=\"document.getElementById('affabs').style.display='block'; return false;\" >Listing</a> ]";
	}
	?>
	</td></tr>
	<tr>
	<td width=9% align=right><font size=3>&nbsp;Cumul&nbsp;:&nbsp;</font></td>
	<td width=30% align=left><font size=3><b>&nbsp; <?php print $cumulabs?></b> <?php print LANGPROJ18 ?> / <b><?php print $cumulabsheure?></b> <?php print LANGPROJ18bis ?> </font></td>

	</tr>
	<?php


	$data_3=nombre_retard($ideleve,$dateDebut,$dateFin);
	// elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif
	$cumulrtds=0;
	$listing="";
	for($j=0;$j<count($data_3);$j++) {
		$listing.="- ".dateForm($data_3[$j][2])." saisie par ".$data_3[$j][4]." / motif : ".$data_3[$j][6]."<br>";
		$nbminute=preg_replace('/mn/','',$data_3[$j][5]);
		if (preg_match('/[0-9]h/',$data_3[$j][5])) {
			$minute=0;
			list($heure,$minute) =preg_split('/h/', $data_3[$j][5], 2 );
			$nbminute=$heure * 60 + $minute;
		}
		$cumulrtds=$cumulrtds + $nbminute ;
	}
?>
<div id="affrtd" style="position:absolute;top:100px;left:50px;display:none;width:350px;height:180px;padding:20px;border:1px #666 solid;background-color:#ddd;z-index:1000;overflow:auto;">
<font class=T2><b>Listing des retards de cette période.</b></font><br /><br />
<?php print $listing ?><br>
<input type='button' value='<?php print LANGFERMERFEN ?>' class='button' onclick="document.getElementById('affrtd').style.display='none';" /><br><br><br>
</div>

<tr><td height='20'></td></tr>
<tr>
<td align=right><font size=3><?php print LANGPROJ7?> : </font></td>
<td align=left><font size=3><b>&nbsp; <?php print count($data_3)?></b></font> <?php 
	if (count($data_3)) {
		print "&nbsp;&nbsp;[ <a href='#' onclick=\"document.getElementById('affrtd').style.display='block'; return false;\" >Listing</a> ]";
	}
?>
</td></tr>
<tr>
<td align=right><font size=3> <?php print LANGPROJ8?>&nbsp;: </font></td>
<td align=left><font size=3><b>&nbsp; <?php print $cumulrtds?></b> <?php print LANGPROJ10?></font></td>
</tr>

	<?php
}
?>
</table>

<br><br>
<table border=0 width=90% align=center>
<tr><td colspan=3> <font size=3><b><u><?php print LANGPROJ9?></u></b></font><br><br></td></tr>
<?php
$nb_retenue="0";
$data=affRetenuTotal_par_eleve_trimestre($ideleve,$dateDebut,$dateFin);
if (count($data) > 0) { $nb_retenue=count($data); }
?>
<tr><td align=right><font size=3><?php print LANGPROJ11?>&nbsp;:&nbsp;</font><td colspan=2 align=left><font size=3><b><?php print $nb_retenue?></b></font></td></tr>
<?php
$affiche="";
$sanction=array();
$sanctionqui=array();
$data=affSanction_par_eleve_trimestre($ideleve,$dateDebut,$dateFin);
for($j=0;$j<count($data);$j++) {
	$sanction[$data[$j][3]]++;
	$affiche="<option>".$data[$j][7]."</option>";
	$sanctionqui[$data[$j][3]]=$affiche;
}
ksort($sanction);
ksort($sanctionqui);
foreach($sanction as $cle => $value) {
	foreach($sanctionqui as $clequi => $valuequi){
		if ($cle == $clequi) {
			$cat=rechercheCategory($cle);
			print "<tr><td align=right title=\"$cat\" ><font size=3>".trunchaine($cat,15)."&nbsp;:&nbsp;</font></td>";
			print "<td><font size=3  ><b>".$value."</b></font></td>";
			print "<td>".LANGPROJ12."&nbsp;:&nbsp;<select><option title=\"$valuequi\" STYLE='color:#000066;background-color:#FCE4BA' >".LANGPROJ13."</option>".trunchaine($valuequi,25)."</select></td></tr>";
		}
	}
}
?>
</td>
</tr>
</table>
<?php Pgclose(); ?>
</body>
</html>
