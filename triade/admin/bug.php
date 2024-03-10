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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("../common/lib_ecole.php"); 
include_once("../common/lib_admin.php"); 
?>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
             </TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
             <SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
             <FORM method=POST>
              <table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Assistance / Besoin d'aide</font></b></td></tr>
<tr id='cadreCentral0'><td valign=top >
                   <!-- // fin de la saisie -->
<br>
<table><tr><td><img src="../image/commun/assisante.gif" /></td><td><font class=T2><?php print "Disposer d'un service d'assistance en ligne." ?></font></td></tr></table>
<br><br>
<table align='center' ><tr><td align='center'>
<script language=JavaScript>buttonMagic2("TRIADE-CLIENT",'http://www.triade-educ.org/accueil/acces_client.php','_blank','','0')</script>
<script language=JavaScript>buttonMagic2("TRIADE-FORUM",'http://forum.triade-educ.org','_blank','','0')</script>
<script language=JavaScript>buttonMagic2("TRIADE-DOC",'http://doc.triade-educ.org','_blank','','0')</script>
<script language=JavaScript>buttonMagic2("TRIADE-DISCORD",'https://www.triade-educ.org/accueil/discord.php','_blank','','0')</script>&nbsp;&nbsp;</td></tr></table>
<br><br>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
