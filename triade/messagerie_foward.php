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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS17 ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<?php
include_once("librairie_php/db_triade.php");
// connexion P
$cnx=cnx();

if ($_SESSION["membre"] == "menuprof"){
	$idpers=$_SESSION["id_suppleant"];
}else{
	$idpers=$_SESSION["id_pers"];
}

if ((isset($_POST["create"])) && (trim($_POST["adress_mail"]) != "") && (ValideMail($_POST["adress_mail"]) == 1) ) {
	$cr=mess_forward(trim($_POST["adress_mail"]),$_POST["valid"],$_SESSION["nom"],$_SESSION["prenom"],$idpers,$_SESSION["membre"]);
	if($cr == 1){
		history_cmd($_SESSION["nom"],"FORWARD","messagerie");
		alertJs(LANGMESS20);
	}
}

if (isset($_POST["adress_mail"])) {
	if (ValideMail($_POST["adress_mail"]) == 0) {
		alertJs("Votre email n'est pas validé");
	}
}	



$checked="";
if (isset($_SESSION["idparent"])) {
	$mail=mess_mail_forward_parent($idpers,$_SESSION["idparent"]);
	$valide=check_mail_forward_parent($_SESSION["nom"],$_SESSION["prenom"],$idpers,$_SESSION["idparent"]);
        if ($valide) {
                $checked="checked";
        }
}else{
	$mail=mess_mail_forward($_SESSION["nom"],$_SESSION["prenom"],$idpers,$_SESSION["membre"]);
	$valide=check_mail_forward($_SESSION["nom"],$_SESSION["prenom"],$idpers,$_SESSION["membre"]);
	if ($valide) {
		$checked="checked";
	}
}


if (isset($_POST["supp"])) {
	list($jour,$mois,$annee)=preg_split('/\//',$_POST["datesupp"]);
	$date=dateFormBase($_POST["datesupp"]);
	if (checkdate($mois,$jour,$annee)) { 
		supprMessageTous($idpers,$date);
		history_cmd($_SESSION["nom"],"SUPPRESSION","Tous les messages jusqu'au ".$_POST["datesupp"]." pour ".$_SESSION["nom"]);
		print "<br><center><font class='T2' id='color2' >Messages supprimés</font></center>";
	}
}

Pgclose();
?>
<form method=post>
<ul>
<br>
<?php print LANGMESS21 ?> : <br><br>
<?php if ((EMAILCHANGEELEVE == "non") && ($_SESSION["membre"] == "menueleve")) { 
	$readonly="readonly='readonly'";
}else{
	$readonly="";
}


if (VERIFEMAIL != "non") { $onblur=" onblur='verifEmail(this)' "; }

?>
<img src="./image/commun/email.gif" align=center> <input type="text" name="adress_mail" size="50" value="<?php print trim($mail)?>" <?php print $readonly ?> <?php print $onblur ?> />
<br><br>
<input type=checkbox name="valid" class="btradio1" <?php print $checked ?> > <?php print LANGMESS19?>
<br><br><br>
<script language=JavaScript>buttonMagicSubmit("<?php print VALIDER ?>","create"); //text,nomInput</script>
</ul>
</form>
<br><br>
<hr>
<br><br>
<form method='post' >
<font class='T2'>&nbsp;&nbsp;Supprimer tous vos messages reçus jusqu'au <input type='text' name='datesupp' value='<?php print dateDMY() ?>' size='12' onKeyPress="onlyChar(event)"  /> 
<input type='submit' name='supp' value='OK' class='BUTTON' /> </font>
</form><br><br>
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
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
