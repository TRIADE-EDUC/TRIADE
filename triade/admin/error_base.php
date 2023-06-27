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
Last updated: 12.10.2002    par Taesch  Eric
*************************************************************/ -->
        <HTML>
        <HEAD>
        <META http-equiv="CacheControl" content = "no-cache">
        <META http-equiv="pragma" content = "no-cache">
        <META http-equiv="expires" content = -1>
        <meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
        <script language="JavaScript" src="librairie_js/lib_defil.js"></script>
        <script language="JavaScript" src="librairie_js/clickdroit.js"></script>
        <script language="JavaScript" src="librairie_js/function.js"></script>
        <title>Triade - administrateur</title>
        </head>
        <body bgcolor="#FAEBD7" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
        <?php include("./librairie_php/lib_licence.php"); ?>
        <SCRIPT language="JavaScript" src='librairie_js/menudepart.js'></SCRIPT>

             <!-- // texte du menu qui defile   -->
               <?php include("./librairie_php/lib_defilement.php"); ?>
             <!-- // fin du texte   -->

             </TD><td width="472" valign="middle" rowspan="3" align="center">

     <!--   -->
     <div align='center'><?php top_h(); ?>
     <!--  -->

             <SCRIPT language="JavaScript" src='librairie_js/menudepart1.js'></SCRIPT>

     <table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
     <tr bgcolor="#666666">
<td height="2"> <b><font  color="red"><font  color="#FFFFFF"><FONT color=red>&nbsp;&nbsp;&nbsp;Error d'accès à la base</font> </font></b></td>
     </tr>
     <tr bgcolor="#CCCCCC">
     <td >
         <!-- // fin  -->

      <TABLE border=0 width=100% >
      <TR>
      <TD valign=top>
<BR><UL><font color=red>ATTENTION Action IMPOSSIBLE !!!<br><br>
Le problème peut venir des informations saisies <br>
(Vérifier les différents champs avant de valider).<BR>  <BR>
Ou l'information est déjà enregistrée OU non accessible.</font><BR>  <BR>

Accès impossible à la base pour cette action . <BR><BR>Un mail a été envoyé automatiquement à l'auto-support TRIADE.
      </TD>
     </TR>
     </TABLE><BR><BR>
     <!-- // fin  -->
     </td></tr></table>

     <?php
     $date = date("d/m/Y \à G:i");
     // $station_serveur initialiser dans le fichier lib_licence.php et lib_licence2.php
     $fp=fopen("./data/error.txt","a+");
     fwrite($fp, "Visité le $date par ".$_SERVER[REMOTE_ADDR]." <BR>avec ".$_SERVER[HTTP_USER_AGENT]." <BR>via le compte de <I>$_SESSION[nom] $_SESSION[prenom]</I> <BR><BR> <font color=red>Erreur Type : Accès à la base </font><BR>\n");
     fclose($fp);
     ?>

     <BR><bR>

<SCRIPT language="JavaScript">InitBulle("#000000","#CCCCFF","red",1);</SCRIPT>

<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
   </BODY></HTML>
