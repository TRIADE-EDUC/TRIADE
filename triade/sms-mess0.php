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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<?php include("./librairie_php/googleanalyse.php"); ?>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGSMS7 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<!-- // debut form  -->
<?php
include_once("librairie_php/db_triade.php");
validerequete("2");

if (LAN == "oui") {
	if (file_exists("./common/config-sms.php")) {
?>
		<table align="center">
		<tr><td align=right><font class="T2"><?php print LANGSMS10 ." : " ?></font></td><td> <input type=button onclick="open('sms-mess-classe.php','_parent','')" value="<?php print CLICKICI?>" class='bouton2' ></tr>
		<tr><td height=10></td></tr>
		<tr><td align=right><font class="T2"><?php print "Envoyer un sms à un parent d'étudiant " ." : " ?></font></td><td> <input type=button onclick="open('sms-mess-parent.php','_parent','')" value="<?php print CLICKICI?>" class='bouton2'  ></tr>
		<tr><td height=10></td></tr>
<tr><td align=right><font class="T2"><?php print LANGSMS12 ?> : </font></td><td> <input type=button onclick="open('sms-mess.php?pid','_parent','')" value="<?php print CLICKICI?>" class='bouton2' ></tr>
		<tr><td height=10></td></tr>
		<tr><td align=right><font class="T2"><?php print LANGSMS13 ." : " ?></font></td><td> <input type=button onclick="open('sms-mess.php','_parent','')" value="<?php print CLICKICI?>" class='bouton2' ></tr>
		<tr><td height=10></td></tr>
		</table>		
<?php
	}else{
		print "<center><font color=red class='T2' >".LANGMESS37.".</font></center>";
	}
}else{
	print "<br><center><font class=T2>".ERREUR1."</font> <br><br> <i>".ERREUR3."</i></center>";
}

?>


<!-- // fin form -->
</td></tr></table>
<br /><br />
<script type="text/JavaScript">InitBulle('#000000','#CCCCFF','red',1);</script>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
            print "</SCRIPT>";
       }else{
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
            print "</SCRIPT>";

       }
?>
</BODY>
</HTML>
