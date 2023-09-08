<?php
session_start();
error_reporting(0);
include_once("./common/config2.inc.php");
if ((CALPROF == "oui") && ($_SESSION[membre] == "menuprof") ) {
        print "<script>location.href='calendrier_config_evenement1.php';</script>";
        exit();
}
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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>

        <HTML>
        <HEAD>
        <META http-equiv="CacheControl" content = "no-cache">
        <META http-equiv="pragma" content = "no-cache">
        <META http-equiv="expires" content = -1>
        <meta name="Copyright" content="Triade©, 2001">
        <LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
        <script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
       <script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
       <script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
        <script language="JavaScript" src="./librairie_js/function.js"></script>
        <title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]  "?></title>
        </head>
        <body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
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
     <tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGPARENT22 ?></font></b></td>
     </tr>
     <tr id='cadreCentral0'>
     <td >
     <!-- // fin  -->
<?php
$saisie_annee="2002";
$saisie_annee_choix=date(Y);
include_once("./librairie_php/lib_calendrier_evenement_visu_readonly.php");
?>
<SCRIPT LANGUAGE="JavaScript"><!--
        // On passe en paramètre le numéro du mois et l'année
        annee(<?php print "$saisie_annee_choix"?>);
      //--></SCRIPT>
<?php
$saisie_annee_plus=date(Y);
$saisie_annee_plus++;
$saisie_annee_moin=date(Y);
$saisie_annee_moin--;
?>
     <center> <A href="./calendrier_evenement_visu11_readonly.php?saisie_annee_choix=<?php print $saisie_annee_moin?>" ><?php print LANCALED1?></A> <------> <A href="./calendrier_evenement_visu11_readonly.php?saisie_annee_choix=<?php print $saisie_annee_plus?>" ><?php print LANCALED2 ?></A></center>

     <!-- // fin  -->
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
     <SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
<?php include_once("./librairie_php/finbody.php"); ?>
   </BODY></HTML>
