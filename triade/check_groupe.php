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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGGRP14?></font></b></td></tr>
<tr id='cadreCentral0'  >
<td valign=top>
<!-- // debut form  -->
<?php
$sql="SELECT group_id,libelle,liste_elev FROM ${prefixe}groupes ORDER BY libelle";
$res=execSql($sql);
$liste_gid=chargeMat($res);
for($cpt=0;$cpt<count($liste_gid);$cpt++) {
	if ($liste_gid[$cpt][0] != 0) {
		$groupeID=$liste_gid[$cpt][0];
		$listeEleveFinal="{";
		$listeEleve=$liste_gid[$cpt][2];
		$listeEleve=preg_replace("/\{/","",$listeEleve);
		$listeEleve=preg_replace("/\}/","",$listeEleve);
		$tabEleve=explode(",",$listeEleve);	
		foreach($tabEleve as $key=>$value) {
			$cr=verifEleveExistViaId($value);
			if ($cr) {
				$listeEleveFinal.="${value},";
			}
		}
		$listeEleveFinal.="}";
		$listeEleveFinal=preg_replace('/,\}/',"}",$listeEleveFinal);
		$sql="UPDATE ${prefixe}groupes SET liste_elev='$listeEleveFinal' WHERE group_id='$groupeID' ";
		execSql($sql);
		 
	}
}

verif_table_groupe();

?><br>
<center><font class="T2">Groupes vérifiés</font></center>

</td></tr></table>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if ($_SESSION["membre"] == "menuadmin") :
       print "<SCRIPT language='JavaScript' ";
       print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
       print "</SCRIPT>";
   else :
      print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
      print "</SCRIPT>";

      top_d();

      print "<SCRIPT language='JavaScript' ";
     print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
     print "</SCRIPT>";

       endif ;
     ?>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
