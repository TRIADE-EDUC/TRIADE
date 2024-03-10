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
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");

if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
	$profpclasse=$_SESSION["profpclasse"];
	validerequete("menuprof");
}else{
	validerequete("2");
}

$filtreABS="NonJustifie";
if (isset($_POST["ABSNJ"])) {
	$filtreABS="NonJustifie";
}

if (isset($_POST["ABSJ"])) {
	$filtreABS="Justifie";
}

$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php 
if ($filtreABS == "NonJustifie") { 
	print LANGABS5 ;
}else{ 
	print "Absences justifi&eacute;es";  
} ?>
</font></b></td></tr>
<tr id='cadreCentral0'>
<td ><br>
<!-- // fin  -->
<ul><font class="T2"><?php print "Indiquer la liste des personnes qui recevront un email" ?>.</font><br><br></ul>

<form method="post">
<table>
<tr>
<td><script language=JavaScript>buttonMagicSubmit("<?php print "Trier par nom" ?>","trie_nom"); //text,nomInput</script></td>
<td><script language=JavaScript>buttonMagicSubmit("<?php print "Trier par date" ?>","trie_date"); //text,nomInput</script></td>

<?php
if (isset($_POST["ABSJ"])) { ?>
	<td><script language=JavaScript>buttonMagicSubmit("<?php print "Absences non justifi&eacute;es" ?>","ABSNJ"); //text,nomInput</script></td>
<?php }else{ ?>
	<td><script language=JavaScript>buttonMagicSubmit("<?php print "Absences justifi&eacute;es" ?>","ABSJ"); //text,nomInput</script></td>
<?php } ?>

<!-- <td><script language=JavaScript>buttonMagicSubmit("<?php print "Trier par classe" ?>","trie_classe"); //text,nomInput</script></td> -->
</tr>
<tr><td height='15'></td></tr>
<tr>
<td>&nbsp;&nbsp;<font class='T2'>Indiquer la classe : </td><td  colspan=2><select name="saisie_classe" onchange='this.form.submit()'>
	<option value='' id='select0' ><?php print LANGCHOIX ?></option>
<?php  if ( (isset($_POST["saisie_classe"])) && ($_POST["saisie_classe"] != 'tous')) {
	print "<option  value='".$_POST["saisie_classe"]."' selected  id='select1' >".trunchaine(chercheClasse_nom($_POST["saisie_classe"]),35)."</option>";
} 
?>
<!-- <option  value='tous' id='select0' ><?php print 'Toutes les classes' ?></option> -->
<?php select_classe2(35);?>
</select>
</font>
</form>

</tr></table>
<br>

<?php


if (isset($_POST["trie_nom"])) {
	$trie='nom';
}

if (isset($_POST["trie_date"])) {
	$trie='date';
}

if (isset($_POST["trie_classe"])) {
	$trie='classe';
}
if (isset($_POST["saisie_classe"])) {
	$idclasse=$_POST["saisie_classe"];
}



?>


<form method="post" action='liste_abs_impr2.php' name='formulaire' >
<table border=1 bgcolor='#FFFFFF' border=1 align="center" width='100%' >
<?php
if ($idclasse != "") { 
	
	
	if ($filtreABS == "NonJustifie") { 
		$data_2=affAbsNonJustif2bis($trie,$idclasse);
	}else{
		$data_2=affAbsJustif2bis($trie,$idclasse);
	}

	
	// $data : tab bidim - soustab 3 champs
	// a.elev_id, a.date_ab, a.date_saisie, a.origin_saisie, a.duree_ab , a.date_fin, a.motif, a.duree_heure, a.id_matiere, a.time, e.nom, e.elev_id, e.classe, a.courrierenvoyer,e.email,e.email_resp_2, email_eleve
	for($j=0;$j<count($data_2);$j++) {
		$ideleve=$data_2[$j][0];
		$idmatiere=$data_2[$j][7];
		$duree=$data_2[$j][4];
		$time=$data_2[$j][9];
		$courrierenvoyer=$data_2[$j][13];
		$emailP1=$data_2[$j][14];
		$emailP2=$data_2[$j][15];
		$email_eleve=$data_2[$j][16];
	

		if ($duree == "-1") {
			$duree=$data_2[$j][7]." heure(s)";
		}else{
			$duree.=" jour(s)";
		}
	
		if ($data_2[$j][0] == "-4") { continue; }
		if ($data_2[$j][4] == "0" ) { $duree="???"; }
		$datedebut=$data_2[$j][1];
		$datefin=$data_2[$j][5];
		$classe=chercheClasse_nom($data_2[$j][12]);
		
		if (trim($email_eleve) == "") {
			$disabledE="disabled='disabled'";
			$titleE=" title=\"Aucun email &eacute;tudiant indiqu&eacute;\" ";
			$imgE="<img src='image/commun/alerte.png' $titleE  />";
		}else{
			$titleE="title=\"$email_eleve\"";
			$disabledE="";
			$imgE="";
		}


		if ((trim($emailP1) == "") && (trim($emailP2) == "")) {
                        $disabledT="disabled='disabled'";
                        $titleT=" title=\"Aucun email tuteur indiqu&eacute;\" ";
                        $imgT="<img src='image/commun/alerte.png' $titleT  />";
                }else{
                        $titleT="title=\"$emailP1 / $emailP2\"";
                        $disabledT="";
                        $imgT="";
                }


		$emailtuteurstage=recupEmailTuteurStage($ideleve);	
		if ($emailtuteurstage == "") {
                        $disabledTT="disabled='disabled'";
                        $titleTT=" title=\"Aucun email tuteur indiqu&eacute;\" ";
                        $imgTT="<img src='image/commun/alerte.png' $titleT  />";
		}else{
                        $disabledTT="";
                        $titleTT=" title=\"$emailtuteurstage\" ";
                        $imgTT="";
		}
	
		print "<tr id='tr$j' class='tabnormal2' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >";
		print "<td id='bordure' valign='top' ><font class='T2'>".trunchaine(strtoupper(recherche_eleve_nom($ideleve))." ".ucwords(strtolower(recherche_eleve_prenom($ideleve))),20)."</font></td>";
		print "<td id='bordure' valign='top' ><font class='T1'>$classe</font></td>";
		print "<td id='bordure' valign='top' ><font class='T1'>absent le ".dateForm($data_2[$j][1])." durant ".$duree." </font></td>";
		print "<td id='bordure' valign='top' ><table><tr>";
		print "<td id='bordure' valign='top' >Email&nbsp;Etudiant&nbsp;<input $disabledE type='checkbox' name='liste[]' $titleE value='$ideleve:$datedebut:$datefin:$duree:$time:eleve'  onClick=\"DisplayLigne('tr$j');\" id='check$j' >";
		$j++;
		print "$imgE</td></tr><tr>";
		print "<td id='bordure' valign='top' >Email&nbsp;Parent&nbsp;<input $disabledT type='checkbox' name='liste[]' $titleT value='$ideleve:$datedebut:$datefin:$duree:$time:tuteur'  onClick=\"DisplayLigne('tr$j');\" id='check$j' >";
		$j++;
		print "$imgT</td></tr><tr>";
		$j++;
		print "$imgTT</td></tr></table>";
		print "</tr>";
	}

	if (count($data_2) == "0") {
		print "<tr><td id='bordure' align='center'><font class='T2'>aucune donnée</font></td></tr>";
	}else{
		print "<tr><td id='bordure'></td><td id='bordure'></td><td id='bordure' align='right'>Toutes les cases :</td><td id='bordure' ><input type='checkbox' onClick=\"cocheCase()\" name='allcase' /></td></tr>";
	}

?>
	</table>
	<input type=hidden name=nb value="<?php print count($data_2) ?>" />
	<input type=hidden name=filtreABS value="<?php print $filtreABS ?>" />
	<br><br>
	<font class='T2'>&nbsp;&nbsp;&nbsp;Mettre en copie chaque email avec l'email suivante : <input type='text' name='emailcc' size='40' value="<?php print $_COOKIE['emailcc'] ?>"  /></font>
	<br><br><br>
	<table align=center><tr><td><script>buttonMagicRetour2('gestion_abs_retard.php','_self','Retour')</script> 
		<?php
		if (count($data_2) != "0") { 
			print "<script language=JavaScript>buttonMagicSubmit(\"Envoyer email\",'rien');</script>";
		}
		?>
	</form>

	<script>
	function cocheCase() {
		if (document.formulaire.allcase.checked == true) {
			for(var i=0;i<<?php print $j ?>;i++) {
				if (document.getElementById('check'+i).disabled == false) document.getElementById('check'+i).checked=true;
			}
		}else{
			for(var i=0;i<<?php print $j ?>;i++) {
				document.getElementById('check'+i).checked=false;
			}
		}
	}

	</script>

<?php } ?>
	<br /><br />
	</td></tr></table>
	
<!-- // fin  -->
</td></tr></table>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")):
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
</BODY></HTML>
