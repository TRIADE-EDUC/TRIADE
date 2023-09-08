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
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Gestion Mot de passe</font></b></td></tr>
<tr id='cadreCentral0' > <td > <p align="left"><font color="#000000">
<TABLE  bordercolor="#000000" border=0 width=100%>
<TR><TD>
<!-- // debut de la saisie -->
<BR><DIV align=center>
<input type=button Value="Modifier votre mot de passe d'accès " onclick="open('pass_admin.php','','width=400,height=200')" class='button' >
<br><br><br>
<input type=button Value="Modifier le mot de passe pour la gestion du Forum " onclick="open('pass_forum.php','','width=400,height=200')" class='button' >
<br><br><br>
<input type=button Value="Changer les mots de passe des parents / élèves" onclick="open('pass_total.php','','width=500,height=400')" class='button' >
<br><br><br>
<input type=button Value="Changer les mots de passe des enseignants" onclick="open('pass_total_ens.php','','width=500,height=350')" class='button' >
<br><br><br>
<input type=button Value="Modifier le mot de passe pour la gestion de Intra-MSN" onclick="open('pass_intra-msn.php','_parent','')" class='button' >
<br><br><br>
<input type=button Value="Modifier le mot de passe pour la gestion de Moodle" onclick="open('pass_moodle.php','_parent','')" class='button' >
<br><br><br>

</td></tr></table>
<!-- // fin de la saisie -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
