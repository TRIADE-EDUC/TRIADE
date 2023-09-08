<?php
      session_start();
/***************************************************************************
 *                              T.R.I.A.D.E 
 *                            ---------------
 *
 *   begin                : Janvier 2000 
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - F. ORY
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
<!-- /************************************************************
Last updated: 28.06.2002    par Taesch  Eric
*************************************************************/ -->
        <HTML>
        <HEAD>
        <META http-equiv="CacheControl" content = "no-cache">
        <META http-equiv="pragma" content = "no-cache">
        <META http-equiv="expires" content = -1>
        <meta name="Copyright" content="Triade©, 2001">
        <LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
        <script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
        <script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
        <script language="JavaScript" src="./librairie_js/calendrier.js"></script>
        <script language="JavaScript" src="./librairie_js/function.js"></script>
        <title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
        </head>
        <body bgcolor="#FAEBD7" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
        <?php include("./librairie_php/lib_licence.php"); ?>
        <SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>

             <!-- // texte du menu qui defile   -->
               <?php include("./librairie_php/lib_defilement.php"); ?>
             <!-- // fin du texte   -->

             </TD><td width="472" valign="middle" rowspan="3" align="center">

             <!--   -->
             <div align='center'><?php top_h(); ?>
             <!--  -->

             <SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
     <table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
     <tr bgcolor="#666666">
     <td height="2"> <b><font  color="red"><font  color="#FFFFFF">Calendrier des évenements</font></b></td>
     </tr>
     <tr bgcolor="#CCCCCC">
     <td >
     <!-- // fin  -->

     <SCRIPT LANGUAGE="JavaScript"><!--
        // On passe en paramètre le numéro du mois et l'année
        annee(2002);

     //--></SCRIPT>

       <center> <A href="./calendrier_parent.php?saisie_annee=2003" ><?php print LANCALED1 ?></A> <------> <A href="./calendrier_parent.php?saisie_annee=2003" ><?php print LANCALED2 ?></A></center>

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
   </BODY></HTML>
