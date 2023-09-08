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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
if ($_SESSION["membre"] == "menupersonnel") {
        if (!verifDroit($_SESSION["id_pers"],"edt")) {
                accesNonReserveFen();
                exit;
        }
}else{
	validerequete("2");
}
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Remplacer un enseignant dans l'EDT" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<br />


<form name='form2' method='post' action='edt_remplace_enseignant.php ' >
<font class='T2'>&nbsp;&nbsp;&nbsp;Période pour le changement : du <input type="text" name="datedebut"  readonly='readonly' size=12 class="bouton2" onKeyPress="onlyChar(event)" />&nbsp;
<?php
include_once("librairie_php/calendar.php");
calendar("id111","document.form2.datedebut",$_SESSION["langue"],"0","0");
?>

au &nbsp; <input type="text" name="datefin"  readonly='readonly' size=12 class="bouton2" onKeyPress="onlyChar(event)" />&nbsp;
<?php
calendar("id111","document.form2.datefin",$_SESSION["langue"],"0","0");
?>
<br /><br /><br />

&nbsp;&nbsp;&nbsp;Enseignant à remplacer : <select name="idpersold">
             <option  id='select0'><?php print LANGCHOIX?></option>
<?php
select_personne('ENS'); // creation des options
?>
</select>
<br /><br /><br />

&nbsp;&nbsp;&nbsp;Enseignant remplaçant : <select name="idpersnew">
             <option  id='select0'><?php print LANGCHOIX?></option>
<?php
select_personne('ENS'); // creation des options
?>
</select>
<br /><br /><br />
&nbsp;&nbsp;&nbsp;En classe de : <select name="idclasse">
         <option   STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
select_classe(); // creation des options
?>
</select>


<br /><br /><br />
<script language=JavaScript>buttonMagicSubmit("<?php print VALIDER ?>","create"); //text,nomInput</script>


<br /><br /><br />

</font>
</form>

<?php 
if (isset($_POST["create"])) {
	$dateDebut=$_POST["datedebut"];
	$dateFin=$_POST["datefin"];
	$idpersold=$_POST["idpersold"];
	$idpersnew=$_POST["idpersnew"];
	$idclasse=$_POST["idclasse"];
	$cr=changementIdProfEdt($dateDebut,$dateFin,$idpersold,$idpersnew,$idclasse);
	if ($cr) {
		print "<center><font class=T2>Changement effectu&eacute;</font></center><br><br>";
	}else{
		print "<center><font class=T2>Erreur de changement</font></center><br><br>";
	}
}
?>

<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php PgClose();  ?>
</BODY></HTML>
