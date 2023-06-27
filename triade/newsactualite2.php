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
// <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
?>

<html xml:lang="fr" lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
   <meta http-equiv="Content-type" content = "text/html; charset=iso-8859-1" />
   <meta name="MSSmartTagsPreventParsing" content="TRUE" />
   <meta http-equiv="CacheControl" content = "no-cache" />
   <meta http-equiv="pragma" content = "no-cache" />
   <meta http-equiv="expires" content = -1 />
   <meta name="Copyright" content="Triade©, 2001" />
   <link rel="shortcut icon" href="./favicon.ico" type="image/icon" />
   <LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
   <script type="text/javascript" src="./librairie_js/lib_defil.js"></script>
   <script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
   <script type="text/javascript" src="./librairie_js/function.js"></script>
   <script type="text/javascript" src="./librairie_js/lib_css.js"></script>
   <script type="text/javascript" src="./ckeditor/ckeditor.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include_once("./librairie_php/lib_licence.php"); ?>
<?php verifplus("menudeux",$_SESSION["id_pers"],$_SESSION["membre"]); ?>
<SCRIPT type="text/javascript" <?php print "src='librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include_once("./librairie_php/lib_defilement.php"); ?>
<?php  $today= dateDMY();  ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h();?>
<SCRIPT type="text/javascript" <?php print "src='librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<FORM method=POST action="newsactualite2.php">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<?php
$today=dateDMY(); 
?>
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE2 ?></font> <font color="#FFFFFF"><?php print LANGTE2?> <?php print $today?> </b></td></tr>
<tr id='cadreCentral0'>
<td >
<?php

$cnx=cnx();
validerequete("2");
$cr=create_news_page_1($_POST["saisie_titre_news"],$_POST["resultat"]);
history_cmd($_SESSION["nom"],"ACTUALITE",$_POST['saisie_titre_news']);

if (isset($_POST["Supp"])) {
        @unlink("./data/fic_news_page_contenu.txt");
        @unlink("./data/fic_news_page_titre.txt");
        @unlink("./data/fic_news_page_date.txt");
}

Pgclose();
?>
<!-- // debut de la saisie -->
<br><br>
<blockquote>
<p align="center"><font color="#000000" class=T2> <?php print LANGMESS7?> </font></p>
</blockquote>

<?php brmozilla($_SESSION["navigateur"]); ?>
<!-- // fin  -->
</td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT type='text/javascript' ";
            print "src='librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT type='text/javascript' ";
            print "src='librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";
	    top_d();
            print "<SCRIPT type='text/javascript' ";
            print "src='librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       endif ;
     ?>
   </BODY></HTML>
