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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1'><?php print LANGSTAGE65 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<br><br>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
validerequete("2");

$recherche=$_POST["recherche"];
print "<font class=T2><ul>";
print LANGASS19." : <b> $recherche </b><br><br><br>";
$data=recherche_entreprise_nom($recherche);
//id_serial,nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus
	if (count($data) > 0 ) {
		for($i=0;$i<count($data);$i++) {
			if ($data[$i][12] == null) {
				$bonus="";
			}else{
				$bonus=$data[$i][12];
			}
?>
			<table bgcolor="#FFFFFF" border=1 bordercolor="#000000" width=80% >
			<tr><td id=bordure><font class='T2'>
			<?php print LANGSTAGE39 ?> : <font color=red><?php print $data[$i][1] ?></font> <br />
			<?php print LANGSTAGE40 ?> : <?php print  $data[$i][7] ?><br>
			<?php print LANGSTAGE30 ?> : <?php print $data[$i][5] ?>  <?php print $data[$i][4] ?> <br>
			Nbre d'élèves ayant effectué un stage : <b><?php print $bonus ?>
			</font>
			<br><br>
			<div align=right>
			<input type=button onclick="open('gestion_stage_ent_supp.php?id=<?php print $data[$i][0] ?>','_parent','')" value="<?php print LANGBT50?>" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">&nbsp;&nbsp;
			[ <a href="#" onclick="open('gestion_stage_ent_info.php?id=<?php print $data[$i][0] ?>','','width=400,height=450,scrollbars=yes')"><?php print LANGSTAGE62 ?> +</a> ]&nbsp;&nbsp;&nbsp;</div>
			<br>
			</td></tr></table><br><br>
<?php
		}
	}else {
		 print "<font class=T2>".LANGSTAGE68.".</font><br><br>";
	}
print "<br>";
print "</font>[<a href='gestion_stage_ent_supp.php'>".LANGSTAGE41."</a>]<br><br> ";
print "</ul>";
?>

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
?>
</BODY></HTML>
