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

Last updated: 21.01.2004    par Taesch  Eric
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
     <td height="2"> <font  color="#FFFFFF">&nbsp;&nbsp;&nbsp;Erreur d'accès à la base </font></td>
     </tr>
     <tr bgcolor="#CCCCCC">
     <td >
         <!-- // fin  -->

      <TABLE border=0 width=100% >
      <TR>
      <TD valign=top>
           <BR><UL><font color=red>ATTENTION la classe ne peut être supprimée.</font><BR>  <BR>
           Un élève appartient à la classe sélectionnée. <BR>
      </TD>
     </TR>
     </TABLE><BR><BR>
     <!-- // fin  -->
     </td></tr></table>

     <?php
     $date = date("d/m/Y \à G:i");
     // $station_serveur initialiser dans le fichier lib_licence.php et lib_licence2.php
     $fp=fopen("./".REPADMIN."/data/error.txt","a+");
     fwrite($fp, "Visité le $date par $_SERVER[REMOTE_ADDR] <BR>avec $_SERVER[HTTP_USER_AGENT] <BR>via le compte de <I>$_SESSION[nom] $_SESSION[prenom]</I> <BR><BR> <font color=red>Erreur Type : Accès à la base </font><BR>\n");
     fclose($fp);
     ?>

     <BR><bR>


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
   <SCRIPT language="JavaScript">InitBulle("#000000","#CCCCFF","red",1);</SCRIPT>
   </BODY></HTML>
