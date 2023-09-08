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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");

if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
	$profpclasse=$_SESSION["profpclasse"];
	validerequete("menuprof");
}else{
	validerequete("2");
}

$cnx=cnx();
if (isset($_GET["filtre"])) {
	$filtreCLasse=$_GET["filtre"];
	$dateDebut=dateForm($_GET["dateDebut"]);
	$dateFin=dateForm($_GET["dateFin"]);
	if ($dateFin == "//") {
		$dateFin="";
	}

	if ($dateDebut == "//") {
		$dateDebut="";
	}

}else{
	if (isset($_POST["sClasseGrp"])) {
		$filtreCLasse=$_POST["sClasseGrp"];
	}else{
		$filtreCLasse="tous";
	}
	
	if (isset($_POST["saisie_date_debut"])) {
		$dateDebut=$_POST["saisie_date_debut"];
	}else{
		$dateDebut="";
	}


	if (isset($_POST["saisie_date_fin"])) {
		$dateFin=$_POST["saisie_date_fin"];
	}else{
		$dateFin="";
	}
}

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGTITRE23?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<br>
<form method="post" action="liste_rtd.php" name="formulaire0" >
&nbsp;&nbsp;<font class=T2>Filtrer sur : </font><select name="sClasseGrp" size="1" onChange="this.form.submit();" >
<?php 
if ($filtreCLasse != "tous") {
	$classeS=chercheClasse($filtreCLasse);
	print "<option value='$filtreCLasse' id='select0' >".$classeS[0][1]."</option>";
	print "<option value='tous' id='select0' >Aucun</option>";
}else{
	print "<option id='select0' value='tous' >".LANGCHOIX."</option>";
}
select_classe(); // creation des options 
$nbRtdNonJustifier=nbRtdNonJustifier($filtreCLasse,$dateDebut,$dateFin);
?>
</select>  (<?php print $nbRtdNonJustifier ?> retard(s) non justifié(s))
<br><br>
&nbsp;&nbsp;<font class=T2>Absences du <input type=text name="saisie_date_debut" value="<?php print $dateDebut?>"  onclick="this.value=''" size=12 class="bouton2" onKeyPress="onlyChar(event)">
<?php
include_once("librairie_php/calendar.php");
calendar("idZ1","document.formulaire0.saisie_date_debut",$_SESSION["langue"],"0");
?>
 au <input type=text name="saisie_date_fin" value="<?php print $dateFin ?>"  onclick="this.value=''" size=12 class="bouton2" onKeyPress="onlyChar(event)">

<?php
calendar("idZ2","document.formulaire0.saisie_date_fin",$_SESSION["langue"],"0");
?>
<input type="submit" name="modif_date" value="<?php print LANGBT28 ?>"  class="bouton2" >
</font>

</form> 
     <!-- // fin  -->
<?php
// affichage de la liste d'élèves trouvées
$date_du_jour=dateYMD();
$affibt=1;
?>
<form method=post name=formulaire action="liste_rtd2.php">
<table border="1" bordercolor="#000000" width="100%"  style="border-collapse: collapse;" >
<tr>
<TD align=center bgcolor=yellow width=5%><?php print ucwords(LANGELE2)?>&nbsp;<?php print ucwords(LANGELE3)?></TD>
<TD align=center bgcolor=yellow width=5%><?php print ucwords(LANGELE4)?></TD>
<TD align=center colspan=2 bgcolor=yellow ><?php print LANGABS11?></TD>
<TD align=center bgcolor=yellow  ><?php print LANGABS12?> / <?php print LANGRTDJUS." <i>(".LANGOUI.")</i>"?></TD>
<TD align=center bgcolor=yellow  ><?php print LANGABS13?></TD>
<?php
$fichier="liste_rtd.php";
$table="retards";
$nbaff=20;
if ((isset($_GET["nba"])) && ($deb != 1)){
	$depart=$_GET["limit"];
}else {
	$depart=0;
}

$a=0;
$data_2=affRetardNonJustifie22Limit($filtreCLasse,$depart,$nbaff,$dateDebut,$dateFin);
	// $data : tab bidim - soustab 3 champs
	// elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, justifier, heure_saisie
	for($j=0;$j<count($data_2);$j++) {
		$checkedJustifier=($data_2[$j][8] == 1) ? "checked='checked'" : "";
        	$ideleve=$data_2[$j][0];
		$idmatiere=$data_2[$j][7];
		if ($idmatiere != null) {
			$nomMatiere=chercheMatiereNom($idmatiere);
		}

		$classe=chercheIdClasseDunEleve($ideleve);
		$classe=trunchaine(chercheClasse($classe),10);
		$classe=preg_replace('/ /',"&nbsp;",$classe[0][1]);
		list($cre,$dC,$fC)=preg_split('/#/',$data_2[$j][10]);

?>

<TR id='tr<?php print $j ?>' class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
<td><?php infoBulleEleveSansLoupe($ideleve,"<input type='text' size='15' readonly='readonly' value=\"".strtoupper(recherche_eleve_nom($ideleve))." ".ucwords(strtolower(recherche_eleve_prenom($ideleve)))."\" />") ?></td>
<td title="<?php print $classe ?>">&nbsp;<?php print $classe ; ?>&nbsp;</td>
<td align=center >
<?php $val="'".$i.$j."','".dateHI()."','".dateDMY()."'"; ?>
<select name="saisie_<?php print $i.$j?>" onChange="abs2(<?php print $val?>)">
<option value=retard STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGABS14?></option>
<option value=absent STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGABS15?></option>
<option value=100 STYLE='color:#000066;background-color:yellow'><?php print "Supp" ?></option>
</select></td>
<td  bgcolor="#FFFFFF" align=center>
<?php
		$dureee=$data_2[$j][5];
		if ($data_2[$j][5] == 0) { $dureee="???"; }
	?>
	<select name="saisie_duree_<?php print $i.$j?>" >
	<option STYLE='color:#000066;background-color:#CCCCFF'><?php print $dureee?></option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>5 mn</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>10 mn</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>15 mn</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>25 mn</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>30 mn</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>40 mn</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>45 mn</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>1h</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>1h15</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>1h30</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>1h45</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>2h00</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>2h30</option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>3h00</option>
	</select></td>
<?php $text=$data_2[$j][6]; $value=$data_2[$j][6]; if ( $data_2[$j][6] == "inconnu") { $text=LANGINCONNU; $value=0;  }  if ( trim($data_2[$j][6]) == "0") { $text=LANGINCONNU; $value=0;  }
?>
<td>
<input type=hidden name="saisie_motif_<?php print $i.$j?>" value="<?php print $text ?>" size=10>
<select name="saisie_motifs_<?php print $i.$j?>" onChange="motifabsretad('<?php print $i.$j ?>',this.value)" >
<option value="<?php print $value ?>"  STYLE="color:#000066;background-color:#FCE4BA" ><?php print trunchaine($text,10) ?></option>
<?php affSelecMotif() ?>
<option value="1" STYLE='color:#000066;background-color:#CCCCFF' ><?php print "autre" ?></option>
</select><input type="checkbox" name="saisie_justifier_<?php print $i.$j ?>" value="1"  onClick="DisplayLigne('tr<?php print $j ?>');"  <?php print $checkedJustifier ?> />
</td>
<td align=center><a href="#" onMouseOver="AffBulle('Créneau : <?php print $cre." (".timeForm($dC)."-".timeForm($fC).") " ?>'); window.status=''; return true;" onMouseOut='HideBulle()'><font class='T1' size=2><?php print dateForm($data_2[$j][2])?><br /><?php print timeForm($data_2[$j][1])?></font></a>
<input type=hidden name="saisie_heure_<?php print $i.$j?>" readonly value="<?php print dateForm($data_2[$j][2])?> <?php print timeForm($data_2[$j][1])?>">
<input type=hidden name=saisie_info[] value="<?php print $i.$j?>">
<input type=hidden name=departideleve[] value="<?php print $ideleve ?>">
<input type=hidden name=departdatesaisie[]  readonly value="<?php print $data_2[$j][3]?>">
<input type=hidden name=departdatertd[]  readonly value="<?php print $data_2[$j][2]?>">
<input type=hidden name=departheurertd[]  readonly value="<?php print $data_2[$j][1]?>">
<input type=hidden name="heuredatesaisie[]"  readonly value="<?php print $data_2[$j][9] ?>" >
<input type=hidden name="dateorigineret[]"  readonly value="<?php print $data_2[$j][1]?>" >

<?php $a++; ?>
</td>
</TR>
<?php
        }
print "</table>";
?>
<br>
	<input type=hidden name=saisie_nb value="<?php print $a?>">
<?php
if ($affibt) {
?>
<table align=center><tr><td>
<script>buttonMagicRetour2('gestion_abs_retard.php','_self','Retour menu')</script>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT30?>","rien"); //text,nomInput</script>
	<a href='liste_rtd_imp.php?filtre=<?php print $filtreCLasse ?>&depart=<?php print $depart ?>&nbaff=<?php print $nbaff ?>&dateDebut=<?php print $dateDebut ?>&dateFin=<?php print $dateFin ?>' target="_blank" ><img src="image/commun/print.gif" border="0" align=center alt='Imprimer'></a>
	<a href='liste_rtd_imp.php?filtre=<?php print "tous" ?>&depart=<?php print $depart ?>&nbaff=<?php print $nbRtdNonJustifier ?>&dateDebut=<?php print $dateDebut ?>&dateFin=<?php print $dateFin ?>' target="_blank" ><img src="image/commun/print2.gif" border="0" align=center alt='Imprimer'></a>
</td></tr></table>
<?php } ?>
</form>

<table width=100% border=0 >
<tr><td align=left width=33%><br>&nbsp;<?php precedent5($fichier,$table,$depart,$nbaff,$filtreCLasse,$dateDebut,$dateFin); ?><br><br></td>
<td align=right width=33%><br><?php suivant5($fichier,$table,$depart,$nbaff,$filtreCLasse,$dateDebut,$dateFin); ?>&nbsp;<br><br></td>
</tr></table>


     <!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
      if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
       print "<SCRIPT language='JavaScript' ";
       print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
       print "</SCRIPT>";
   else :
      print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
      print "</SCRIPT>";

      top_d();

      print "<SCRIPT language='JavaScript' ";
     print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
     print "</SCRIPT>";

       endif ;
     ?>
   <?php
// deconnexion en fin de fichier
Pgclose();
?>
<SCRIPT language="JavaScript">InitBulle("#FFFFFF","#009999","#FFFFFF",1);</SCRIPT>
</BODY></HTML>
