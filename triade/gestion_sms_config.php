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
<script language="JavaScript" src="./librairie_js/lib_discipline.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
// connexion P
$cnx=cnx();

if (isset($_POST["create"])){
        $cr=config_param_ajout($_POST["texte"],"sms-message");
        if($cr == 1){
                alertJs("Message enregistré -- Equipe Triade");
        }
}

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Configuration Message SMS</font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<BR>
<blockquote>
<B>Enregistrement du message SMS envoyé aux parents d'élèves.</B>
<form method=post name=form >
<table border=0><tr><td>
Message : (140 caractères maximum) <br>
<?php
$message1=config_param_visu("sms-message");
$message=$message1[0][0];
if (trim($message) == ""){$message="Nous vous signalons que votre enfant ELEVE est absent(e) aujourd'hui (DATE)";}
?>
<textarea cols=70 rows=4 name=texte onkeypress="compter(this,'140', this.form.CharRestant)" ><?php print $message ?></textarea>
<input type='text' name='CharRestant' size='2' disabled='disabled' value='<?php print strlen($message) ?>' > 
<br><br>
<i>
Utilisation du mot ELEVE pour afficher dans le SMS le prenom et le nom de l'élève. <br>
Utilisation du mot NOM pour afficher le nom d'élève.<br>
Utilisation du mot PRENOM pour afficher le prénom de l'élève.<br>
Utilisation du mot DATE pour afficher la date du jour dans le SMS.<br>
Utilisation du mot CLASSE pour afficher la classe de l'élève.<br>
Utilisation du mot TYPE pour afficher la nature (absent(e) ou en retard).

<br></i>
<br><br>
<script language=JavaScript>buttonMagicSubmit("Enregistrer","create"); //text,nomInput</script>
</form>
<br /><br /><br /><hr><br />
<?php
if (isset($_POST["smsfiltre"])) {
	config_param_ajout($_POST["numsms"],"SMSFILTRE");	
}
$filtreSMS=config_param_visu('smsfiltre');
?>

<form method="post">
<font class="T2"><b>Filtre SMS</b><br>
Les numéros SMS de votre pays commence par :  </font><br> (ex : 06) <input type="text" name="numsms" size="2" value="<?php print $filtreSMS[0][0] ?>" /> <br /><br />


<script language=JavaScript>buttonMagicSubmit("Valider","smsfiltre"); //text,nomInput</script>

</form>

<br /><br /><br /><hr><br />

<form method="post">
<font class="T2"><b>AUTO-SMS</b><br>
<?php
if (isset($_POST["smsauto"])) {
	config_param_ajout($_POST["validesmsauto"],"SMSAUTO");	
	config_param_ajout($_POST["nbabssms"],"SMSNBABS");
	config_param_ajout($_POST["smsautotel"],'SMSAUTOTEL');
	config_param_ajout($_POST["smsautotelport1"],'SMSAUTOTELPORT1');
	config_param_ajout($_POST["smsautotelport2"],'SMSAUTOTELPORT2');
	config_param_ajout($_POST["smsautoteleleveport"],'SMSAUTOTELELEVEPORT');
	config_param_ajout($_POST["smsautotelevefixe"],'SMSAUTOTELELEVEFIXE');
	config_param_ajout($_POST["smsautojustifier"],'SMSAUTOJUSTIFIER');
}

$SMSAUTO=config_param_visu('SMSAUTO'); $checkedSMSAUTO="";
if ($SMSAUTO[0][0] == "1") {$checkedSMSAUTO="checked='checked'"; }
$a=config_param_visu('SMSNBABS');
$nbsmsauto=$a[0][0];
if ($nbsmsauto == "") $nbsmsauto=2;

$SMSAUTO=config_param_visu('SMSAUTOTEL'); $checkedSMSAUTOTEL="";
if ($SMSAUTO[0][0] == "1") {$checkedSMSAUTOTEL="checked='checked'"; }

$SMSAUTO=config_param_visu('SMSAUTOTELPORT1'); $checkedSMSAUTOTELPORT1="";
if ($SMSAUTO[0][0] == "1") {$checkedSMSAUTOTELPORT1="checked='checked'"; }

$SMSAUTO=config_param_visu('SMSAUTOTELPORT2'); $checkedSMSAUTOTELPORT2="";
if ($SMSAUTO[0][0] == "1") {$checkedSMSAUTOTELPORT2="checked='checked'"; }

$SMSAUTO=config_param_visu('SMSAUTOTELELEVEPORT'); $checkedSMSAUTOTELELEVEPORT="";
if ($SMSAUTO[0][0] == "1") {$checkedSMSAUTOTELELEVEPORT="checked='checked'"; }

$SMSAUTO=config_param_visu('SMSAUTOTELELEVEFIXE'); $checkedSMSAUTOTELELEVEFIXE="";
if ($SMSAUTO[0][0] == "1") {$checkedSMSAUTOTELELEVEFIXE="checked='checked'"; }

$SMSAUTO=config_param_visu('SMSAUTOJUSTIFIER'); $checkedSMSAUTOJUSTIFIER="";
if ($SMSAUTO[0][0] == "1") {$checkedSMSAUTOJUSTIFIER="checked='checked'"; }

?>
Activer l'envoi SMS automatique : <input type="checkbox" name="validesmsauto" value="1" <?php print $checkedSMSAUTO ?> /> (<i>oui</i>)<br />
Nbr d'<b>absences</b> <u>cumulées</u> <b>non justifiées</b> : <input type='text' name="nbabssms" value="<?php print $nbsmsauto ?>" size='1' /> </font><br>
<ul>
<li>Envoi sur téléphone principal (fixe) : <input type="checkbox" name="smsautotel" value="1" <?php print $checkedSMSAUTOTEL ?> /> (<i>oui</i>) </li><br />
<li>Envoi sur portable tuteur 1 : <input type="checkbox" name="smsautotelport1" value="1" <?php print $checkedSMSAUTOTELPORT1 ?> /> (<i>oui</i>) </li><br />
<li>Envoi sur portable tuteur 2 : <input type="checkbox" name="smsautotelport2" value="1" <?php print $checkedSMSAUTOTELPORT2 ?> /> (<i>oui</i>) </li><br />
<li>Envoi sur téléphone Etudiant (fixe) : <input type="checkbox" name="smsautotelevefixe" value="1" <?php print $checkedSMSAUTOTELELEVEFIXE ?> /> (<i>oui</i>) </li><br />
<li>Envoi sur Portable Etudiant : <input type="checkbox" name="smsautoteleleveport" value="1" <?php print $checkedSMSAUTOTELELEVEPORT ?> /> (<i>oui</i>) <br /></li>
<li>Valider comme "justifier" l'absence : <input type="checkbox" name="smsautojustifier" value="1" <?php print $checkedSMSAUTOJUSTIFIER ?> /> (<i>oui</i>) <br /></li>
</ul>
<br />
<br />

<script language=JavaScript>buttonMagicSubmit("Valider","smsauto"); //text,nomInput</script>

</form>

</td></tr></table>








<BR>
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

   Pgclose();
     ?>
</BODY></HTML>
