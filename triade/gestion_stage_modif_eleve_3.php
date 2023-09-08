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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajaxStage.js"></script>
<script type="text/javascript" src="./librairie_js/xorax_serialize.js" ></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("3");
$cnx=cnx();
error($cnx);
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1'><?php print LANGSTAGE86 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top><br>

<?php
if (isset($_GET["id"])) {
	$prenom=recherche_eleve_prenom($_GET["id"]);
	$nom=recherche_eleve_nom($_GET["id"]);

}

if (isset($_GET["idstage"])) {
	$data=recherche_stage_eleve_par_id($_GET["idstage"]);
//id_eleve,id_entreprise,lieu_stage,ville_stage,id_prof_visite,date_visite_prof,loger,nourri,passage_x_service,raison,info_plus,num_stage,code_p,id,tuteur_stage,tel,compte_tuteur_stage,alternance,jour_alternance,dateDebutAlternance,dateFinAlternance,horairedebutjournalier,horairefinjournalier,date_visite_prof2,,service,indemnitestage,pays_stage
	for($i=0;$i<count($data);$i++) {
		$id_entreprise=$data[$i][1];
		$lieu=$data[$i][2];
		$ville=$data[$i][3];
		$pays=$data[$i][27];
		$service=$data[$i][25];
		$indemnitestage=$data[$i][26];
		$id_prof_visite=$data[$i][4];

		$date=$data[$i][5];
		$date=dateForm($date);
		$loger=$data[$i][6];
		$nourri=$data[$i][7];
		$passage_x_service=$data[$i][8];
		$raison=$data[$i][9];
		$info=$data[$i][10];
		$num_stage=$data[$i][11];
		$codep=$data[$i][12];
		$id=$data[$i][13];
		$tuteur=$data[$i][14];
		$tel=$data[$i][15];
		$alternance=$data[$i][17];
		$dateDebutAlternance=$data[$i][19];
		$dateFinAlternance=$data[$i][20];
		$jour_alternance=$data[$i][18];
		$compte_tuteur_stage=$data[$i][16];
		$horairedebutjournalier=$data[$i][21];
		$horairefinjournalier=$data[$i][22];
		$date2=dateForm($data[$i][23]);
		$id_prof_visite2=$data[$i][24];

		if (DBTYPE == "mysql") {
			if ($loger == 1) {
				$checklogoui="checked";
				$checklognon="";
			}else {
				$checklogoui="";
				$checklognon="checked";
			}

			if ($nourri == 1) {
				$checknourrioui="checked";
				$checknourrinon="";
			}else {
				$checknourrinon="checked";
				$checknourrioui="";
			}

			if ($passage_x_service == 1) {
				$checkservoui="checked";
				$checkservnon="";
			}else {
				$checkservnon="checked";
				$checkservoui="";
			}
		}

	}
}
?>
<form method=post action="gestion_stage_modif_eleve_4.php" name="formulaire" onsubmit="return validestageelevemodif()">
<input type=hidden name=ideleve value="<?php print $_GET["id"]?>" >
<input type=hidden name=saisie_classe value="<?php print $_GET["idclasse"]?>" >
<input type=hidden name=id value="<?php print $id?>" >

<table border=0 align=center width=100%>
<tr>
	<td align=right width=45%><font class="T2"><?php print LANGNA1 ?> :</font></td>
<td align=left><input type=text size=30 readonly value="<?php print $nom?>"></td>
</tr>
<tr>
<td align=right width=45%><font class="T2"><?php print LANGNA2 ?> :</font></td>
<td align=left><input type=text size=30 readonly value="<?php print $prenom?>"></td>
</tr>
<tr>
<td align=right width=45%><font class="T2"><?php print LANGSTAGE48 ?> :</font></td>
<td align=left>
<select name=idstage>
<option value="<?php print $num_stage?>" ><?php print rechercheNumStage($num_stage) ?></option>
<?php
select_stage($_GET["idclasse"]);
?>
</select>
</td>
</tr>


<?php
if ($alternance == 1) {
	$checkalternance="checked='checked'";
}else{
	$checkalternance="";
}

if ($dateDebutAlternance != "") { $dateDebutAlternance=dateForm($dateDebutAlternance); }
if ($dateFinAlternance != "") { $dateFinAlternance=dateForm($dateFinAlternance); }
$horairedebutjournalier=timeForm($horairedebutjournalier);
$horairefinjournalier=timeForm($horairefinjournalier);
if ($dateDebutAlternance == "00/00/0000") $dateDebutAlternance="";
if ($dateFinAlternance == "00/00/0000") $dateFinAlternance="";
if ($horairedebutjournalier == "00:00") $horairedebutjournalier="hh:mm"; 
if ($horairefinjournalier == "00:00") $horairefinjournalier="hh:mm"; 
$checkj1='';
$checkj2='';
$checkj3='';
$checkj4='';
$checkj5='';
$checkj6='';
$checkj7='';
$tab=explode(',',$jour_alternance);
foreach($tab as $key=>$value) {
	if (preg_match('/1/',$value)) { $checkj1='checked="checked"'; } 
	if (preg_match('/2/',$value)) { $checkj2='checked="checked"'; } 
	if (preg_match('/3/',$value)) { $checkj3='checked="checked"'; } 
	if (preg_match('/4/',$value)) { $checkj4='checked="checked"'; } 
	if (preg_match('/5/',$value)) { $checkj5='checked="checked"'; } 
	if (preg_match('/6/',$value)) { $checkj6='checked="checked"'; } 
	if (preg_match('/7/',$value)) { $checkj7='checked="checked"'; } 
}
?>
<tr>
<td align=right width=45% valign="top" ><font class="T2"><?php print "Stage en alternance" ?> :</font></td>
<td align=left valign="top">
<input type=checkbox name="alternance" value="1" disabled id="newstage" <?php print $checkalternance ?> > oui  ( du <input type='text' name="dateDebutAlternance" size='10' id="debutstage"  readonly='readonly' value="<?php print $dateDebutAlternance ?>" /> au <input type='text' name="dateFinAlternance" id="finstage" size=10  readonly='readonly' value="<?php print $dateFinAlternance ?>" />)
<br>
<input type=checkbox name="jourstage[]" value="1" id="j1" <?php print $checkj1 ?> disabled='disabled' > L 
<input type=checkbox name="jourstage[]" value="2" id="j2" <?php print $checkj2 ?> disabled='disabled' > M 
<input type=checkbox name="jourstage[]" value="3" id="j3" <?php print $checkj3 ?> disabled='disabled' > M 
<input type=checkbox name="jourstage[]" value="4" id="j4" <?php print $checkj4 ?> disabled='disabled' > J 
<input type=checkbox name="jourstage[]" value="5" id="j5" <?php print $checkj5 ?> disabled='disabled' > V 
<input type=checkbox name="jourstage[]" value="6" id="j6" <?php print $checkj6 ?> disabled='disabled' > S 
<input type=checkbox name="jourstage[]" value="7" id="j7" <?php print $checkj7 ?> disabled='disabled' > D 
</td>
</tr>

<tr>
<td align=right width=45%><font class="T2"><?php print LANGSTAGE74 ?> :</font></td>
<td align=left>
<select name=ident onchange="checkList2(this.value)">
<option value="<?php print $id_entreprise?>" ><?php print recherche_entr_nom_via_id($id_entreprise) ?></option>
<?php
select_entreprise();
?>
</select>
</td>
</tr>
<tr>
<td align=right width=45%><font class="T2"><?php print LANGSTAGE76 ?> :</font></td>
<td align=left><input type=text size=30 name=lieu value="<?php print $lieu?>" id='lieu' ></td>
</tr>
<tr>
<td align=right width=45%><font class="T2"><?php print LANGSTAGE30 ?> :</font></td>
<td align=left><input type=text size=30 name=ville value="<?php print $ville?>" id='ville' ></td>
</tr>
<tr>
<td align=right width=45%><font class="T2"><?php print LANGSTAGE29 ?> :</font></td>
<td align=left><input type=text size=15 name=postal value="<?php print $codep?>" id='postal' ></td>
</tr>
<tr>
<td align=right width=45%><font class="T2"><?php print "Pays" ?> :</font></td>
<td align=left><input type=text size=30 name='pays' id='pays' value="<?php print $pays ?>" ></td>
</tr>
<tr>
<td align=right width=45%><font class="T2"><?php print LANGSTAGE77 ?> :</font></td>
<td align=left><input type=text size=30 name=responsable value="<?php print $tuteur?>" id='responsable' ></td>
</tr>
<tr>
<td align=right width=45%><font class="T2"><?php print LANGABS38 ?> :</font></td>
<td align=left><input type=text size=30 name=tel value="<?php print $tel?>" id='tel' ></td>
</tr>
<tr>
<td align=right width=45%><font class="T2"><?php print LANGSTAGE78 ?> 1 :</font></td>
<td align=left>
<select name='idprof' id='idprof' >
<?php
if ($id_prof_visite == "") {
	print "<option id='select0' >".LANGCHOIX."</option>";
}else {
?>
<option value="<?php print $id_prof_visite?>" ><?php print recherche_personne($id_prof_visite) ?></option>
<option id='select0' value='0' >aucun</option>
<?php
}
select_personne('ENS');
?>
</select>
</td>
</tr>


<tr>
<td align=right width=45%><font class="T2">Date de la visite 1 :</font></td>
<?php
	if (($date == "//") || ($date == "00/00/0000" )){
		$date="";
	}
?>
<td align=left><input type=text size=15 name="date" value="<?php print $date?>" class=bouton2  readonly='readonly'  >
<?php
 include_once("librairie_php/calendar.php");
 calendar("id1","document.formulaire.date",$_SESSION["langue"],"0");
?>


</td>
</tr>

<?php
	if (($date2 == "//") || ($date2 == "00/00/0000" )){
		$date2="";
	}
?>

<tr>
<td align=right width=45%><font class="T2"><?php print LANGSTAGE78 ?> 2 :</font></td>
<td align=left>
<select name='idprof2' id='idprof2' >
<?php
if (($id_prof_visite2 == "") || ($id_prof_visite2 == "0")) {
	print "<option id='select0' value='0' >".LANGCHOIX."</option>";
}else {
?>
<option value="<?php print $id_prof_visite2?>" ><?php print recherche_personne($id_prof_visite2) ?></option>
<option id='select0' value='0' >aucun</option>
<?php
}
select_personne('ENS');
?>
</select>
</td>
</tr>


<tr>
<td align=right width=45%><font class="T2">Date de la visite 2 :</font></td>
<td align=left><input type=text size=15 name="date2" class=bouton2  value="<?php print $date2?>" readonly='readonly' >
<?php
 calendarDim("id2","document.formulaire.date2",$_SESSION["langue"],"0");
?>
</td>
</tr>


<tr>
<td align=right width=45%><font class="T2"><?php print "Intitulé du service" ?> :</font></td>
<td align=left><input type=text size=30 name='service' id='service' maxlength='200' value="<?php print $service ?>" ></td>
</tr>
<tr>
<td align=right width=45%><font class="T2"><?php print "Indemnités de stage" ?> :</font></td>
<td align=left><input type=text size=30 name='indemnitestage' id='indemnitestage' maxlength='200' value="<?php print $indemnitestage ?>" ></td>
</tr>



<tr>
<td align=right width=45%><font class="T2"><?php print "Tuteur de stage" ?> :</font></td>
<td align=left>
<select name='idtuteur' id='idtuteur'>
</select>
</td>
</tr>

<tr>
<td align=right width=45%><font class='T2'><?php print LANGSTAGE79 ?> :</font></td>
<td align=left><input type=radio name="loge" value=1 class=btradio1 <?php print $checklogoui ?> > <?php print LANGOUI ?>  / <input type=radio name="loge" value=0 class=btradio1 <?php print $checklognon ?> > <?php print LANGNON ?> </td>
</tr>
<tr>
<td align=right width=45%><font class='T2'><?php print LANGSTAGE80 ?> :</font></td>
<td align=left><input type=radio value=1 name="nourri" class=btradio1 <?php print $checknourrioui ?> > <?php print LANGOUI ?>  / <input type=radio  name="nourri" value=0 class=btradio1 <?php print $checknourrinon ?> > <?php print LANGNON ?> </td>
</tr>
<tr>
<td align=right width=45%><font class='T2'><?php print LANGSTAGE81 ?> :</font></td>
<td align=left><input type=radio name="xservice" value=1 class=btradio1 <?php print $checkservoui ?>  > <?php print LANGOUI ?>  / <input type=radio name="xservice" value=0 class=btradio1 <?php print $checkservnon ?>  > <?php print LANGNON ?> </td>
</tr>


<tr>
<td align=right width=45%><font class="T2"><?php print "Horaires journaliers" ?> :</font></td>
<td align=left>Début : <input type=text name="horairedebutjournalier" size=4  value='<?php print $horairedebutjournalier ?>' onKeyPress="onlyChar2(event)" >  / Fin <input type=text name="horairefinjournalier" size=4   value='<?php print $horairefinjournalier ?>' onKeyPress="onlyChar2(event)"  ></td>
</tr>

<tr>
<td align=right width=45% valign=top><font class='T2'><?php print LANGSTAGE82 ?> :</font></td>
<td align=left><textarea cols=40 name=raison ><?php print $raison?> </textarea></td>
</tr>
<tr>
<td align=right width=45% valign=top  ><font class='T2'><?php print LANGSTAGE83 ?> :</font></td>
<td align=left><textarea cols=40 name=info><?php print $info?></textarea></td>
</tr>
<tr>
<td colspan=2 align=center><br><br><table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT7?>","create"); //text,nomInput</script>
</td>
<td>
<script language=JavaScript>buttonMagic("<?php print LANGSTAGE73?>","gestion_stage_modif_eleve_2.php?id=<?php print $_GET["id"]?>","_parent","","") </script>
</td>
</td></tr></table>
</td>
</tr>
</table>
</form>
</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
     	print "<SCRIPT type='text/javascript' ";
       	print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
       	print "</SCRIPT>";
}else{
       	print "<SCRIPT type='text/javascript' ";
      	print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
      	print "</SCRIPT>";
      	top_d();
      	print "<SCRIPT type='text/javascript' ";
      	print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
	print "</SCRIPT>";
}
?>
<script>
checkListModif('<?php print $id_entreprise?>','<?php print $_GET["id"] ?>');
<?php 
if ($compte_tuteur_stage != '0') { ?>
var newOption = document.createElement("option");
newOption.setAttribute("value",'<?php print $compte_tuteur_stage ?>');
newOption.setAttribute("id","select1");
newOption.setAttribute("selected","selected");
newOption.innerHTML="<?php print strtoupper(recherche_personne_nom($compte_tuteur_stage))." ".ucwords(recherche_personne_prenom($compte_tuteur_stage)) ?>";
document.getElementById('idtuteur').appendChild(newOption);
<?php } ?>
</script>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
