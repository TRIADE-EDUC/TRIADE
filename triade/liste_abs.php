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
<script language="JavaScript" src="./librairie_js/lib_absrtd2.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print $_SESSION['nom']." ".$_SESSION['prenom'] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once("librairie_php/db_triade.php");

if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
	$profpclasse=$_SESSION["profpclasse"];
	validerequete("menuprof");
}else{
	validerequete("2");
}


if (isset($_GET["filtre"])) {
	$filtreCLasse=$_GET["filtre"];
	$dateDebut=dateForm($_GET["dateDebut"]);
	$dateFin=dateForm($_GET["dateFin"]);
	if ($dateFin == "//") { $dateFin=""; }
	if ($dateDebut == "//") { $dateDebut=""; }
}else{
	if (isset($_POST["sClasseGrp"])) { $filtreCLasse=$_POST["sClasseGrp"]; }else{ $filtreCLasse="tous"; }
	if (isset($_POST["saisie_date_debut"])) { $dateDebut=$_POST["saisie_date_debut"]; }else{ $dateDebut=""; }
	if (isset($_POST["saisie_date_fin"])) { $dateFin=$_POST["saisie_date_fin"]; }else{ $dateFin=""; }
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGABS5 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td><br>
<form method="post" name="formulaire0" action="liste_abs.php">
&nbsp;&nbsp;Filtrer sur : <select name="sClasseGrp" size="1" class="cc-select" onChange="this.form.submit();">
<?php
if ($filtreCLasse != "tous") {
	$classeS=chercheClasse($filtreCLasse);
	print "<option value='$filtreCLasse' id='select0' >".$classeS[0][1]."</option>";
	print "<option value='tous' id='select0' >Aucun</option>";
}else{
	print "<option id='select0' value='tous' >".LANGCHOIX."</option>";
}
select_classe();
$nbabsNonJustifierTotal=nbAbsNonJustifier($filtreCLasse,$dateDebut,$dateFin);
?>
</select> (<?php print $nbabsNonJustifierTotal ?> absence(s) non justifiée(s))
<br><br>
&nbsp;&nbsp;Absences du <input type=text name="saisie_date_debut" value="<?php print $dateDebut ?>" onclick="this.value=''" size=10 class="cc-select" onKeyPress="onlyChar(event)">
<?php
include_once("librairie_php/calendar.php");
calendar("idZ1","document.formulaire0.saisie_date_debut",$_SESSION["langue"],"0");
?>
 au <input type=text name="saisie_date_fin" value="<?php print $dateFin ?>" onclick="this.value=''" size=10 class="cc-select" onKeyPress="onlyChar(event)">
<?php calendar("idZ2","document.formulaire0.saisie_date_fin",$_SESSION["langue"],"0"); ?>
<input type="submit" name="modif_date" value="<?php print LANGBT28 ?>" class="btn-enr" style="padding:4px 10px;">

</form>

<!-- // fin  -->
<?php
$date_du_jour=dateDMY2();
$afficbt="1";
?>
<form method="post" name="formulaire" action="liste_abs2.php">
<table border="1" bordercolor="#000000" width="100%" style="border-collapse: collapse;">
<tr>
<TD align=center style="background:#ffffd5;font-weight:700;padding:4px 6px;" width=5%><?php print LANGEL1 ?>&nbsp;<?php print LANGEL2 ?></TD>
<TD align=center style="background:#ffffd5;font-weight:700;padding:4px 6px;" width=5%><?php print "SMS" ?></TD>
<TD align=center style="background:#ffffd5;font-weight:700;padding:4px 6px;" width=5%><?php print LANGEL3 ?></TD>
<TD align=center colspan=2 style="background:#ffffd5;font-weight:700;padding:4px 6px;"><?php print LANGABS11 ?></TD>
<TD align=center style="background:#ffffd5;font-weight:700;padding:4px 6px;"><?php print LANGABS12 ?> / <?php print LANGRTDJUS." <i>(".LANGOUI.")</i>" ?></TD>
<TD align=center style="background:#ffffd5;font-weight:700;padding:4px 6px;"><?php print "Abs le" ?></TD>
</tr>
<?php
$fichier="liste_abs.php";
$table="absences";
$nbaff=20;
if ((isset($_GET["nba"])) && (isset($deb) && $deb != 1)){
	$depart=$_GET["limit"];
}else {
	$depart=0;
}

$a=0;
$data_2=affAbsNonJustif22Limit($filtreCLasse,$depart,$nbaff,$dateDebut,$dateFin);
	// elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure, id_matiere, time, justifier, heure_saisie, creneaux,smsenvoye
	for($j=0;$j<countTriade($data_2);$j++) {
		$ideleve=$data_2[$j][0];
		if ($ideleve == "-4") { continue; }
		$classe=chercheIdClasseDunEleve($ideleve);
		$tabclasse=chercheClasse($classe); // code_class,trim(libelle),trim(desclong)
		$classe=$tabclasse[0][1];
		$classelong=$tabclasse[0][2];
		$classe=trunchaine($classe,10);
		$classe=preg_replace('/ /',"&nbsp;",$classe[0][1]);
		$checkedJustifier=($data_2[$j][10] == 1) ? "checked='checked'" : "";

		$creData=isset($data_2[$j][12]) ? $data_2[$j][12] : "#";
		$cre=""; $dC=""; $fC="";
		list($cre,$dC,$fC)=array_pad(preg_split('/#/',$creData),3,"");

		if ($data_2[$j][13] == 1) { $imgsms="<img src='./image/commun/sms.gif' title='SMS ENVOYE' width='20' height='18' align='center'/>"; }else{ $imgsms=""; }
?>
	<TR id="tr<?php print $j?>" class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<td ><?php infoBulleEleveSansLoupe($ideleve,"<input type='text' size='15' readonly='readonly' value=\"".strtoupper(recherche_eleve_nom($ideleve))." ".ucwords(strtolower(recherche_eleve_prenom($ideleve)))."\" />") ?></td>
	<td valign='center' >&nbsp;<?php print $imgsms ?>&nbsp;</td>
	<td title="<?php print $classe ?>" >&nbsp;<?php print trunchaine($classe,10) ?>&nbsp;</td>
	<td align=center  >
	<?php $val="'".$i.$j."','".dateHI()."','".dateDMY()."'"; ?>
	<select name="saisie_<?php print $i.$j?>" onChange="abs2(<?php print $val?>)">
	<option value="absent" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGABS15 ?></option>
	<option value="retard" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGABS14 ?></option>
	<option value="100" STYLE='color:#000066;background-color:yellow'><?php print LANGEDIT20bis ?></option>
	</select></td>
	<td  bgcolor="#FFFFFF" align=center>
	<select name="saisie_duree_<?php print $i.$j?>" >
	<?php
		$dureee=$data_2[$j][4]." J";
		if ($data_2[$j][4] == 0) { $dureee="???"; }
		if ($data_2[$j][4] == -1) { $dureee=preg_replace('/\./','H',$data_2[$j][7]); }
	?>
	<option STYLE='color:#000066;background-color:#CCCCFF'><?php print $dureee?></option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>1H00</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>1H30</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>2H00</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>2H30</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>3H00</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>3H30</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>4H00</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>0.5 J</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>1 J</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>2 J</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>3 J</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>4 J</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>5 J</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>6 J</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>7 J</option>
	</select></td>
	<td >
<?php 	$text=$data_2[$j][6]; $value=$data_2[$j][6];
	if ( $data_2[$j][6] == "inconnu") { $text=LANGINCONNU; $value=0; }
	if ( trim($data_2[$j][6]) == "0") { $text=LANGINCONNU; $value=0; }
	?>
	<input type=hidden name="saisie_motif_<?php print $i.$j?>" value="<?php print $data_2[$j][6]?>" size=10>
	<select name="saisie_motifs_<?php print $i.$j?>" onChange="motifabsretad('<?php print $i.$j?>',this.value)" >
	<option value="<?php print $value ?>"  STYLE="color:#000066;background-color:#FCE4BA" ><?php print trunchaine($text,10) ?></option>
	<?php affSelecMotif() ?>
	<option value="1" STYLE='color:#000066;background-color:#CCCCFF' ><?php print "autre" ?></option>
	</select><input type="checkbox" name="saisie_justifier_<?php print $i.$j ?>" value="1" onClick="DisplayLigne('tr<?php print $j ?>');" <?php print $checkedJustifier  ?> />
	</td>

	<td align=center width='5'>
	<a href="#" onMouseOver="AffBulle('Créneau : <?php print $cre." (".timeForm($dC)."-".timeForm($fC).") " ?>'); window.status=''; return true;" onMouseOut='HideBulle()'><?php print dateForm($data_2[$j][1])?></a>
	<input type=hidden name="saisie_heure_<?php print $i.$j?>" readonly value="<?php print dateForm($data_2[$j][1])?>">
	<input type=hidden name="saisie_info[]" value="<?php print $i.$j?>">
	<input type=hidden name="departideleve[]" value="<?php print $ideleve ?>">
	<input type=hidden name=departdatesaisie[]  readonly value="<?php print $data_2[$j][1]?>">
	<input type=hidden name=departdateabs[]  readonly value="<?php print $data_2[$j][2]?>">
	<input type=hidden name="heurederetard_<?php print $i.$j?>" >
	<input type=hidden name="time_<?php print $i.$j?>" value="<?php print $data_2[$j][9]?>"  >
	<input type=hidden name="idmatiere_<?php print $i.$j?>" value="<?php print $data_2[$j][8]?>"  >
	<input type=hidden name="heuredatesaisie[]"  readonly value="<?php print $data_2[$j][11] ?>" >
	<input type=hidden name="dateorigineret[]"  readonly value="<?php print $data_2[$j][1]?>" >
	<input type=hidden name="heuredabsence[]"  readonly value="<?php print $dC?>" >

	<?php $a++; ?>
	</td>
	</TR>

<?php
}
print "</table>";
?>

<br>
	<input type=hidden name=saisie_nb value="<?php print $a?>">
	<input type=hidden name="filtre" value="<?php print $filtreCLasse ?>">
<?php
if ($afficbt) {
?>
<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:4px 0;">
  <button type="button" class="btn-retour" onclick="open('gestion_abs_retard.php','_self','')">Retour menu</button>
  <span style="display:flex;"><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT30?>","rien");</script></span>
  <a href='liste_abs_imp.php?filtre=<?php print $filtreCLasse ?>&depart=<?php print $depart ?>&nbaff=<?php print $nbaff ?>&dateDebut=<?php print $dateDebut ?>&dateFin=<?php print $dateFin ?>' target="_blank" ><img src="image/commun/print.gif" border="0" align=center alt='Imprimer cette page'></a>
  <a href='liste_abs_imp.php?filtre=<?php print "tous" ?>&depart=<?php print "0" ?>&nbaff=<?php print $nbabsNonJustifierTotal ?>&dateDebut=<?php print $dateDebut ?>&dateFin=<?php print $dateFin ?>' target="_blank" ><img src="image/commun/print2.gif" border="0" align=center alt='Imprimer toutes les absences non justifiées'></a>
</div>
<br><br>
<?php
}
?>
</form>

<table width=100% border=0 >
<tr><td align=left width=33%><br>&nbsp;<?php precedent4($fichier,$table,$depart,$nbaff,$filtreCLasse,$dateDebut,$dateFin); ?><br><br></td>
<td align=right width=33%><br><?php suivant4($fichier,$table,$depart,$nbaff,$filtreCLasse,$dateDebut,$dateFin); ?>&nbsp;<br><br></td>
</tr></table>

     <!-- // fin  -->
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
<SCRIPT language="JavaScript">InitBulle("#FFFFFF","#009999","#FFFFFF",1);</SCRIPT>
</BODY></HTML>
