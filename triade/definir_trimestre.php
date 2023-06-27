<?php
session_start();
if (isset($_COOKIE["anneeScolaire"])) $anneeScolaire=$_COOKIE["anneeScolaire"];
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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
if (isset($_POST["create"])) {
	$ok=1;
	$nbT=$_POST["semestre"];
	if ($nbT) {
		$nbT=3;
	}else{
		$nbT=4;
	}
	$nb=1;

	$anneeScolaire=$_POST["annee_scolaire"];

	$idclasse=$_POST["saisie_classe"];
	if ($idclasse == "tous") { 
		$idclasse=0; 
		$sql="DELETE FROM ${prefixe}date_trimestrielle WHERE annee_scolaire='$anneeScolaire' ";
		execSql($sql);
		history_cmd($_SESSION["nom"],"SUPPRESSION","Suppression de toutes les dates trimestres pour l'année $annee_scolaire");
	}

	$idclasse=$_POST["saisie_classe"];
	if ((is_numeric($idclasse)) && ($anneeScolaire != "")) {
		$sql="DELETE FROM ${prefixe}date_trimestrielle WHERE idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' ";
		execSql($sql);
		history_cmd($_SESSION["nom"],"SUPPRESSION","Suppression dates trimestres pour l'année $anneeScolaire de la classe $idclasse");
	}
		
	$sql="SELECT code_class FROM ${prefixe}classes WHERE offline='0'";
	$res=execSql($sql);
	$listeIdClasse=chargeMat($res);

	while ($nb < $nbT ) {
		$valeur="trimestre".$nb;
	      	$debut="trimestre".$nb."_debut";
		$fin="trimestre".$nb."_fin";
		$date_form_debut=$_POST[$debut];
		$date_form_fin=$_POST[$fin];
	        if ((trim($date_form_debut) != "") && (trim($date_form_fin) != "")) {
	          	$date_form_debut=dateFormBase($date_form_debut);
	          	$date_form_fin=dateFormBase($date_form_fin);
			if ($idclasse == "tous") {
				for($O=0;$O<count($listeIdClasse);$O++) {
					$idclasse2=$listeIdClasse[$O][0];
					$cr=def_trimestre($valeur,$date_form_debut,$date_form_fin,$idclasse2,$anneeScolaire);
					history_cmd($_SESSION["nom"],"CREATION","Mise en place dates trimestres pour $idclasse2 en $annee_scolaire");
				}
			}else{
		        	$cr=def_trimestre($valeur,$date_form_debut,$date_form_fin,$idclasse,$anneeScolaire);
				history_cmd($_SESSION["nom"],"CREATION","Mise en place dates trimestres pour $idclasse en $annee_scolaire");
			}
	          	if($cr != 1){
	               		alertJs(LANGPARAM26);
	                	$ok=0;
	                	break;
	        	}
	        }
        	$nb=$nb+1;
      	}

	if ($idclasse2 > 0) $idclasse=$idclasse2;

     // verification des trimestres
     	$data=affDateTrimByIdclasse("trimestre1",$idclasse,$anneeScolaire);
	 if (count($data)) {
	 	$date1_debut=dateNonForm($data[0][0]);
	 	$date1_fin=dateNonForm($data[0][1]);
	 }

	 $data=affDateTrimByIdclasse("trimestre2",$idclasse,$anneeScolaire);
	 if (count($data)) {
	 	$date2_debut=dateNonForm($data[0][0]);
	 	$date2_fin=dateNonForm($data[0][1]);
	 }

	 $data=affDateTrimByIdclasse("trimestre3",$idclasse,$anneeScolaire);
	 if (count($data)) {
	 	$date3_debut=dateNonForm($data[0][0]);
	 	$date3_fin=dateNonForm($data[0][1]);

	 }


     if (($date1_debut < $date1_fin) && ($date1_fin < $date2_debut) &&  ($date2_debut < $date2_fin)) {
		// rien
     }else{
     	$ok=0;
     	alertJs(LANGPARAM26);
     	$sql="DELETE FROM ${prefixe}date_trimestrielle WHERE idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' ";
	execSql($sql);
	history_cmd($_SESSION["nom"],"SUPPRESSION","Suppression date trimestre $annee_scolaire de la classe $idclasse");
     }


     if ($ok) { alertJs(LANGPARAM27); }

}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form name="formulaire" method="post" action="definir_trimestre.php" >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPARAM17 ?></font></b></td></tr>
<tr id='cadreCentral0'><td >
<!-- // fin  -->
<?php

if (isset($_GET['id'])) {
	$date11_debut="";
	$date11_fin="";
	$date22_debut="";
	$date22_fin="";
	$date33_debut="";
	$date33_fin="";
	$anneeScolaire=$_GET["annee_scolaire"];

	$data=affDateTrimByIdclasse(trimestre1,$_GET['id'],$anneeScolaire);
	if (count($data)) {
		$date11_debut=dateForm($data[0][0]);
		$date11_fin=dateForm($data[0][1]);
	}

	$data=affDateTrimByIdclasse(trimestre2,$_GET['id'],$anneeScolaire);
	if (count($data)) {
		$date22_debut=dateForm($data[0][0]);
		$date22_fin=dateForm($data[0][1]);
	}

	$data=affDateTrimByIdclasse(trimestre3,$_GET['id'],$anneeScolaire);
	if (count($data)) {
		$date33_debut=dateForm($data[0][0]);
		$date33_fin=dateForm($data[0][1]);

	}

	if (($date33_debut == "") && ($date11_debut != "") && ($date22_debut != "")) {
		$checked="checked='checked'";
	}
}


if (isset($_GET["suppid"])) {
	$idsupp=$_GET["suppid"];
	$sql="DELETE FROM ${prefixe}date_trimestrielle WHERE idclasse='$idsupp'";
	execSql($sql);
}

?>

<br><br>
&nbsp;&nbsp;<font class=T2><?php print LANGPROFG?> :</font> 
<select name="saisie_classe">
<?php
if ((isset($_GET["id"])) && ($_GET["id"] > 0) ) {
	print "<option id='select1' value='".$_GET["id"]."' >".chercheClasse_nom($_GET["id"])."</option>";
}
?>
<option id='select0' value='tous' ><?php print LANGMESS147 ?></option>
<?php
select_classe(); // creation des options
?>
</select> 
<BR><br>

&nbsp;&nbsp;<font class=T2><?php print "Ann&eacute;e scolaire" ?> : </font>
<select name="annee_scolaire">
<option id='select0' value='inconnu' ><?php print LANGCHOIX?></option>
<?php
filtreAnneeScolaireSelect($anneeScolaire);
?>
</select>


<BR><br>
<table width="100%" border="1" align="center" bordercolor="#000000" style="border-collapse: collapse;" >
<tr><td align=center bgcolor="yellow"><b><?php print LANGPARAM18?></b></td><td bgcolor="yellow" align=center><b><?php print LANGPARAM19?></b></td><td bgcolor="yellow" align=center><b><?php print LANGPARAM20?></b></td></tr>
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
<td align=center><font color="red"><?php print LANGPARAM21?></font></td>
<td align=center><input type="text" name="trimestre1_debut" value="<?php print $date11_debut ?>" size=12 readonly> <?php include_once("librairie_php/calendar.php");calendarDim('id1','document.formulaire.trimestre1_debut',$_SESSION["langue"],"0","0");?></td>
<td align=center><input type="text" name="trimestre1_fin"  value="<?php print $date11_fin ?>" size=12 readonly> <?php include_once("librairie_php/calendar.php");calendarDim('id2','document.formulaire.trimestre1_fin',$_SESSION["langue"],"0","0");?></td>
</tr>
<tr  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
<td align=center><font color="red"><?php print LANGPARAM22?></font></td>
<td align=center><input type="text" name="trimestre2_debut" value="<?php print $date22_debut ?>" size=12 readonly> <?php include_once("librairie_php/calendar.php");calendarDim('id3','document.formulaire.trimestre2_debut',$_SESSION["langue"],"0","0");?></td>
<td align=center><input type="text" name="trimestre2_fin"  value="<?php print $date22_fin ?>" size=12  readonly> <?php include_once("librairie_php/calendar.php");calendarDim('id4','document.formulaire.trimestre2_fin',$_SESSION["langue"],"0","0");?></td>
</tr>
<tr  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
<td align=center><font color="red"><?php print LANGPARAM23?> *</font></td>
<td align=center><input type="text" name="trimestre3_debut" value="<?php print $date33_debut ?>" size=12 readonly> <?php include_once("librairie_php/calendar.php");calendarDim('id5','document.formulaire.trimestre3_debut',$_SESSION["langue"],"0","0");?></td>
<td align=center><input type="text" name="trimestre3_fin" value="<?php print $date33_fin ?>" size=12  readonly> <?php include_once("librairie_php/calendar.php");calendarDim('id6','document.formulaire.trimestre3_fin',$_SESSION["langue"],"0","0");?></td>
</tr>
</table>




<table width="100%" border="0" align="center">
<tr><td  align="center">
<br>
<input type='checkbox' name='semestre' value="1" class='btradio1' <?php print $checked ?> >
<?php print LANGMESS146 ?><br>
<table align=center border=0><tr><td>
<br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGPARAM24?>","create"); //text,nomInput</script>
<br><br>
</td></tr></table>
<font class=T1>* <?php print LANGPARAM25?></font>
</td></tr>
</table>

</table>
</form>

<br><br>
<?php
if (isset($_POST["annee_scolaire"])) $anneeScolaire=$_POST["annee_scolaire"];
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS148 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<form method='post' action='definir_trimestre.php' >
<br>
<ul>
&nbsp;&nbsp;<font class=T2><?php print "Indiquer l'ann&eacute;e scolaire" ?> : </font>
<select name="annee_scolaire" onChange="this.form.submit()" >
<option id='select0' value='' ><?php print LANGCHOIX?></option>
<?php
filtreAnneeScolaireSelect($anneeScolaire);
?>
</select>
</ul>
</form>
<br>


<?php
$idclasse=0;


$data=recupDateTrimIdclasse($idclasse,$anneeScolaire); //date_debut,date_fin,trim_choix,idclasse
$dateDebutT1="";
$dateFinT1="";
$dateDebutT2="";
$dateFinT2="";
$dateDebutT3="";
$dateFinT3="";
for ($i=0;$i<count($data);$i++) {
	$trim=$data[$i][2];
	if ($trim == "trimestre1") { 
		$dateDebutT1=$data[$i][0];
		$dateFinT1=$data[$i][1];
	}
	if ($trim == "trimestre2") { 
		$dateDebutT2=trim($data[$i][0]);
		$dateFinT2=trim($data[$i][1]);
	}
	if ($trim == "trimestre3") { 
		$dateDebutT3=trim($data[$i][0]);
		$dateFinT3=$data[$i][1];
	}
	if (($dateDebutT3 == "") && ($dateDebutT1 != "") && ($dateDebutT2 != "")) {
		$semestriel="oui";
	}	
	if ($dateDebutT1 != "") { $dateDebutT1=dateForm($dateDebutT1); 	}
	if ($dateFinT1 	 != "")	{ $dateFinT1=dateForm($dateFinT1); 	}
	if ($dateDebutT2 != "") { $dateDebutT2=dateForm($dateDebutT2); 	}
	if ($dateFinT2 	 != "")	{ $dateFinT2=dateForm($dateFinT2); 	}
	if ($dateDebutT3 != "") { $dateDebutT3=dateForm($dateDebutT3); 	}
	if ($dateFinT3   != "")	{ $dateFinT3=dateForm($dateFinT3); 	}
	 
	if ($idclasse == 0) { $nomClasse="Toutes les classes"; }	
}

if ($nomClasse != "") {
	print "&nbsp;&nbsp;<img src='image/on10.gif' /> <font class=T2>".LANGBULL33." : $nomClasse</font> / [ <a href='definir_trimestre.php?id=0&annee_scolaire=$anneeScolaire'><font color=green><b>".LANGMESS149."</b></font></a> ]&nbsp;&nbsp;";
	print "[ <a href='definir_trimestre.php?suppid=0'><font color=green><b>".LANGMESS150."</b></font></a> ]<br>";	
	print "<ul>";
	print LANGMESS157." 1 : $dateDebutT1 - $dateFinT1 <br>";
	print LANGMESS157." 2 : $dateDebutT2 - $dateFinT2 <br>";
	print LANGMESS157." 3 : $dateDebutT3 - $dateFinT3 <br>";
	print "</ul>";
}

$dataC=affClasse(); //code_class,libelle
if (count($dataC)) {
	for ($j=0;$j<count($dataC);$j++) {
		$idclasse=$dataC[$j][0];
		$nomClasse=ucwords($dataC[$j][1]);
		$data=recupDateTrimIdclasse($idclasse,$anneeScolaire); //date_debut,date_fin,trim_choix,idclasse
		$dateDebutT1="";
		$dateFinT1="";
		$dateDebutT2="";
		$dateFinT2="";
		$dateDebutT3="";
		$dateFinT3="";
		for ($i=0;$i<count($data);$i++) {
			$trim=$data[$i][2];
			if ($trim == "trimestre1") { 
				$dateDebutT1=$data[$i][0];
				$dateFinT1=$data[$i][1];
			}
			if ($trim == "trimestre2") { 
				$dateDebutT2=$data[$i][0];
				$dateFinT2=$data[$i][1];
			}
			if ($trim == "trimestre3") { 
				$dateDebutT3=$data[$i][0];
				$dateFinT3=$data[$i][1];
			}
			if (($dateDebutT3 == "") && ($dateDebutT1 != "") && ($dateDebutT2 != "")) {
				$semestriel="oui";
			}

			if ($dateDebutT1 != "") { $dateDebutT1=dateForm($dateDebutT1); 	}
			if ($dateFinT1 	 != "")	{ $dateFinT1=dateForm($dateFinT1); 	}
			if ($dateDebutT2 != "") { $dateDebutT2=dateForm($dateDebutT2); 	}
			if ($dateFinT2 	 != "")	{ $dateFinT2=dateForm($dateFinT2); 	}
			if ($dateDebutT3 != "") { $dateDebutT3=dateForm($dateDebutT3); 	}
			if ($dateFinT3   != "")	{ $dateFinT3=dateForm($dateFinT3); 	}
		}

		if (trim($dateDebutT1) == "") continue; 
		if (trim($nomClasse) == "") continue; 
		print "&nbsp;&nbsp;<img src='image/on10.gif' /> <font class=T2>".LANGASS17." : $nomClasse</font> / [ <a href='definir_trimestre.php?id=$idclasse&annee_scolaire=$anneeScolaire'><font color=green><b>".LANGPER30."</b></font></a> ]";
		print "&nbsp;&nbsp;[ <a href='definir_trimestre.php?suppid=$idclasse'><font color=green><b>".LANGacce21."</b></font></a> ]<br>";	
		print "<br>";	
		print "<ul> ".LANGMESS157." 1 : $dateDebutT1 - $dateFinT1 <br>";
		print LANGMESS157." 2 : $dateDebutT2 - $dateFinT2 <br>";
		print LANGMESS157." 3 : $dateDebutT3 - $dateFinT3 <br></ul>";

	}
}
print "<br>";


?>



<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
Pgclose();
?>
</BODY></HTML>
