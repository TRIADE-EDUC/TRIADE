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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="setTimeout('timer("formulaire")',100)" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
// connexion P
$cnx=cnx();

if(isset($_POST["creat_sanction"])):
	if (trim($_POST["saisie_intitule"]) != "") {
		$cr=create_com_bulletin($_POST["saisie_intitule"],$_SESSION["id_pers"]);
	}
endif;

if(isset($_POST["creat_supp"])) {
	if ($_POST["saisie_int_supp"] == "tous") {
		supp_com_bulletin2($_SESSION["id_pers"]);
	}else{
        	supp_com_bulletin($_POST["saisie_int_supp"]);
	}
}

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPROFB4?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<BR>
<blockquote>
<B><font class=T2><?php print LANGPROFB5?>.</font></B><br><br>
<form method=post name="formulaire">
<table border=0><tr><td width=5>
<?php print LANGPROFB6?> :
<?php 	if (defined("NBCARBULL")) { $nbcar=NBCARBULL; }else{ $nbcar=400; } ?>
<textarea cols=50 rows=4 name=saisie_intitule maxlength='25'  onkeyup="Compter(this,this.form.CharRestant,'<?php print $nbcar ?>');"
onkeypress="compter(this,'<?php print $nbcar ?>')" >
</textarea>
</td><td align=left>
<table align=left><tr><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGENR?>","creat_sanction"); //text,nomInput</script>
<br>
<br><center>
<input type=text size=2 name='CharRestant' disabled="disabled">
</center>
</td></tr></table>
<br><br>
</td></tr></table>
</form>
<BR>
<form method=POST>
<?php print LANGPROFB7?> :
<select name="saisie_int_supp">
<option STYLE='color:#000066;background-color:#FCE4BA'>Liste</option>
<option value='tous' STYLE='color:#000066;background-color:red'>Tous les commentaires</option>
<?php
select_com_bulletin($_SESSION["id_pers"],45);
?>
</select> <input type=submit name="creat_supp" value="<?php print LANGBT50?>" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
</form>
</blockquote>
<BR><BR>
</td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if ($_SESSION[membre] == "menuadmin") :
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
