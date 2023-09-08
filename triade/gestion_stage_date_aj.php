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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<?php include("./librairie_php/lib_licence.php"); ?>
<script language="JavaScript">

// variable globale signalant une erreur
var errfound = false;
//fonction de validation d'après la longueur de la chaîne
function Validlongueur(item,len) {
   return (item.length >= len);
}

// affiche un message d'alerte
function error9(elem, text) {
// abandon si erreur déjà signalée
   if (errfound) return;
   window.alert(text);
   elem.select();
   elem.focus();
   errfound = true;
}

function Validdate(nom) {
        var dernier = nom.lenght ;
        var slach1  = nom.charAt(2);
        var slach2  = nom.charAt(5);
        var jour = nom.substring(0,2);
        var mois = nom.substring(3,5);
        var caractere= nom.charAt(6);
        if (isNaN(caractere)) { return false }
        var annee = nom.substring(6,10);
        if (isNaN(jour)) { return false }
        if (isNaN(mois)) { return false }
        if (isNaN(annee)) { return false }
        if ((annee > 9999) || (jour > 31) || (mois > 12) || (slach1 != '/') || (slach2 != '/') ){
                return false
        }
        else {

                return true
        }
}
// affiche un message d'alerte
function error2(text) {
// abandon si erreur déjà signalée
   if (errfound) return;
   window.alert(text);
   errfound = true;
}


// validation d'un champ de select
function Validselect(item){
 if (item == 0) {
        return (false) ;
 }else {
        return (true) ;
        }
}

function ValiddatePeriode(datedebut,datefin) {
	var jour = datedebut.substring(0,2);
        var mois = datedebut.substring(3,5);
        var annee = datedebut.substring(6,10);
	var datedebut=annee+""+mois+""+jour;
 	var jour = datefin.substring(0,2);
        var mois = datefin.substring(3,5);
        var annee = datefin.substring(6,10);
	var datefin=annee+""+mois+""+jour;     
	if (datedebut > datefin) {
		return(false)
	}else{
		return(true);
	}
}

function validedatestage() {
errfound = false;
if (document.formulaire.num.value.length < 1) {
	error9(document.formulaire.num,"<?php print LANGSTAGE97 ?>  \n\n Service Triade ");
}
if (isNaN(document.formulaire.num.value)) {
	error9(document.formulaire.num,"<?php print LANGSTAGE97 ?>    \n\n Service TRIADE ");
}
if (!Validdate(document.formulaire.debutdate.value)) {
	error9(document.formulaire.debutdate,"<?php print LANGSTAGE98 ?> \n\n Service TRIADE"); }
if (!Validdate(document.formulaire.findate.value)) {
	error9(document.formulaire.findate,"<?php print LANGSTAGE99 ?> \n\n Service TRIADE"); }
if (!ValiddatePeriode(document.formulaire.debutdate.value,document.formulaire.findate.value)) {
	error2("<?php print "La date de fin de stage ne peut être avant la date de début" ?> \n\n Service TRIADE"); }
if (!Validselect(document.formulaire.saisie_classe.options.selectedIndex)) {
      error2(langfunc11);
}
return !errfound; /* vrai si il ya pas d'erreur */
}
</script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGSTAGE44 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
validerequete("2");
?>
<br>
<ul>
<font class=T2>
<form method=post onsubmit="return validedatestage()" name="formulaire">
<?php print LANGSTAGE48 ?>: <select name="num">
<?php if ($numstage != '') {
	print "<option value='$numstage' id='select0'>$numstage</option>";
} ?>
			<option value='' id='select0'></option>		
			<?php 
			for ($i=0;$i<=30;$i++) {
			    print "<option value='$i' id='select1'>$i</option>";
			}
			?>
</select><br><br>
<?php print "Nom du stage" ?>: <input type=text name="nom_stage" size=30 value='<?php print $nomstage; ?>' maxlength="50" ><br><br>
<?php print LANGSTAGE45 ?> : <input type=text name="debutdate" size=12 value='<?php print $datedebut; ?>' class=bouton2 onKeyPress="onlyChar(event)" maxlength='10' > </font>
<?php
 include_once("librairie_php/calendar.php");
 calendar("id1","document.formulaire.debutdate",$_SESSION["langue"],"0");
?>
<br><br>
<font class='T2'>
<?php print LANGSTAGE46 ?> : <input type=text name="findate" size=12 value='<?php print $datefin; ?>' class=bouton2 onKeyPress="onlyChar(event)" maxlength='10' > </font>
<?php
 include_once("librairie_php/calendar.php");
 calendar("id2","document.formulaire.findate",$_SESSION["langue"],"0");
?>
<br><br>
<font class='T2'>
<?php print LANGELE4?> : </font><select name="saisie_classe">
<option STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
select_classe(); // creation des options
?>
</select><br />

<br>
<font class='T2'>
<?php print LANGTMESS521 ?> </font>
<input type=checkbox name="jourstage[]" value="1"  id="j1"  checked='checked' /> L / M
<input type=checkbox name="jourstage[]" value="2"  id="j2"  checked='checked' /> M / T
<input type=checkbox name="jourstage[]" value="3"  id="j3"  checked='checked' /> M / W
<input type=checkbox name="jourstage[]" value="4"  id="j4"  checked='checked' /> J / T
<input type=checkbox name="jourstage[]" value="5"  id="j5"  checked='checked' /> V / F
<input type=checkbox name="jourstage[]" value="6"  id="j6"  checked='checked' /> S / S
<input type=checkbox name="jourstage[]" value="7"  id="j7"  checked='checked' /> D / S

<br /><br /><br />
<script language=JavaScript>buttonMagicSubmit("<?php print LANGSTAGE47 ?>","create"); //text,nomInput</script>
<script language=JavaScript>buttonMagicRetour("gestion_stage.php","_parent"); //text,nomInput</script>
<br><br>
</form>
</ul>
<?php
if (isset($_POST["create"])) {
	$cr=stage_ajout($_POST["num"],$_POST["debutdate"],$_POST["findate"],$_POST["saisie_classe"],$_POST["nom_stage"],$_POST["jourstage"]);
	if($cr){
        history_cmd($_SESSION["nom"],"CREATION","date de stage");
        print "<font color='red' class='T2' ><br><br><center>Le stage du ".$_POST["debutdate"]." au ";
		print $_POST["findate"]." <br> pour la classe de ".chercheClasse_nom($_POST["saisie_classe"]) ;
		print " est enregistré.";
		print "</center></font><br><br>";
	}
}
?>


</font>
<!-- // fin  -->
</td></tr></table>


<?php
       // Test du membre pour savoir quel fichier JS je dois executer
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

       endif ;
// deconnexion en fin de fichier
	Pgclose();
?>
</BODY></HTML>
