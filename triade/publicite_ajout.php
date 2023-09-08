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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body bgcolor="#FAEBD7" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" onunload="attente_close();">
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
<td height="2"> <b><font  color="red"><font  color="#FFFFFF">Ajout d'une bannière de publicité</font></b></td>
</tr>
<tr bgcolor="#CCCCCC">
<td >
<!-- // fin  -->
<UL><BR>
<form name=formulaire method=post action="publicite_ajout2.php"  ENCTYPE="multipart/form-data">
Nom de la bannière : <input type=text name="saisie_nom_banniere" size=20 maxlength=30 ><BR>
<br />
Date de mise en service : <input type=text name="saisie_date_debut" size=12 value="jj/mm/aaaa" onclick="this.value=''"> jusqu'au <input type=text name="saisie_date_fin" size=12  value="jj/mm/aaaa" onclick="this.value=''"><BR>
<br />
Fréquence d'apparution : Souvent <input type=radio name=saisie_frequence value=souvent style="background-color:#CCCCCC"> Normal <input type=radio name=saisie_frequence checked value=normal style="background-color:#CCCCCC"> peu <input type=radio name=saisie_frequence value=peu style="background-color:#CCCCCC"><BR>
<BR>
Lien de la bannière : <input type=text name="saisie_lien" size=30 value="http://" maxlength=50 >
<BR />
<br />
Bannière à transmettre : <input type="file" name="saisie_fichier" size=20 >
<A href='#' onMouseOver="AffBulle('<font face=Verdana size=1> <B><font color=red>I</font></B>mage au format <b>gif</b> ou <b>jpg</b> <BR>  <B><font color=red>T</font></B>aille max <b>30 ko</b></FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='./image/help.gif' align=center border=0></A>
<br />
<br>
Demande de création d'une bannière par Triade : <input type=button onclick="PopupCentrer('./publicite_demande_creation.php','400','200','','pub_demande')" value="Cliquez ici" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"><BR>
<BR><BR>
Exemple de bannière : <input type=button onclick="PopupCentrer('./publicite_exemple.php','500','200','','pub_demande')" value="Cliquez ici" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"><BR>
<BR><BR>
<input type=submit onclick="attente();" value="Enregistrer nouvelle bannière">  
<input type=button onclick="open('acces2.php','_parent','')" value="Quitter sans enregistrer">
</form>
</UL>
<!-- // fin  -->
</td></tr></table>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
<script language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</script>
</BODY></HTML>
