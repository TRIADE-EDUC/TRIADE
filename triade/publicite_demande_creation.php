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
include("librairie_php/lib_admin.php");
include("librairie_php/lib_ecole.php");

if ( ! empty($_POST[saisie_description])) {
 $today= date ("j M, Y");
 $fichier=fopen("./".REPADMIN."/data/publicite_demande.txt","a+");
 flock($fichier, 1);
 fwrite($fichier,"<font color=red>Le $today : </font> $saisie_description <br><br>");
 fwrite($fichier,"<u>Ecole :</u> ".REPECOLE." <br><br>");
 fwrite($fichier,"<u>Contact :</u> $_SESSION[nom] $_SESSION[prenom] <br><br>");
 fwrite($fichier,"<u>Description :</u> $saisie_description <br><br>");
 flock($fichier, 3);
 fclose($fichier);
 print "<script>alert('un responsable clientèle traitera votre demande sous 48H -- Service Triade')</script>";
 print "<script>parent.window.close();</script>";
}
?>
<!-- /************************************************************
Last updated: 08.07.2002    par Taesch  Eric
*************************************************************/ -->
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<title>Triade</title>
</head>
<body bgcolor='#FCE4BA' text='#000000' >
<?php include("./librairie_php/lib_licence.php"); ?>
<form name=formulaire method=post>
<U>Description de la bannière</U> <BR><BR><textarea name=saisie_description cols=80 rows=5> </textarea>
<br>
<BR><BR><input type=submit value="Enregistrer la demande"> 
<input type=button onclick="parent.window.close()" value="Quitter sans enregistrer">
</form>
</BODY>
</HTML>
