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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGETUDE45 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
validerequete("2");
$id_etude=$_POST["saisie_etude"];
if (isset($_GET["id"])) {
	$id_etude=$_GET["id_etude"];
	supp_etude_eleve($_GET["id"],$id_etude);
}
?>
<table width=95% bordercolor='#000000' border=1 align=center>
<tr><td bgcolor=yellow><b>Nom</b></td><td bgcolor=yellow><b>Prénom</b></td><td bgcolor=yellow><b>Classe</b></td><td bgcolor=yellow><b>Supprimer</b></td><tr>
<?php
$data=liste_eleve_etude($id_etude);
for($i=0;$i<count($data);$i++) {
?>
<tr class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'"   bordercolor='#FFFFFF'>
<?php
print "<td>".strtoupper(recherche_eleve_nom($data[$i][0]))."</td><td>".strtolower(recherche_eleve_prenom($data[$i][0]))."</td>";
?>
<td><?php $idclasse=chercheIdClasseDunEleve($data[$i][0]); $nomclasse=chercheClasse($idclasse); print $nomclasse[0][1]?> </td>
<td align=right width=5><input type=button onclick="open('gestion_etude_eleve_supp2.php?id=<?php print $data[$i][0]?>&id_etude=<?php print $id_etude?>','_parent','')"  class=bouton2 value="Supprimer">
</td>
</tr>
<?php } ?>
</table>
<br><br>




<br><br>
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
</BODY></HTML>
