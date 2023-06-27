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
<script type="text/javascript" src="./librairie_js/clickdroit.js"></script>
<script type="text/javascript" src="./librairie_js/function.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT type="text/javascript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT type="text/javascript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="1" bordercolor="#000000" cellpadding="3" cellspacing="1" width="100%" bgcolor="#FFFFFF" height="85">
<tr bgcolor="#FFFFFF" bordercolor='#FFFFFF'><td > <p align="left"><font color="#000000" class=T0>
<!-- // debut de la saisie -->
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='./image/logo_triade_licence.gif'>
<ul>
<?php 
include_once("../common/version.php");
if (file_exists("../common/lib_patch.php")){
	include_once('../common/lib_patch.php');
	include_once('../common/config-md5.php');
	$rev="<br>Rev : <i>".VERSIONPATCH."</i>  - <i>".VERSIONMD5."</i>";
}

?>

<BR><BR>Version : <b><?php print VERSION?></b>
<?php print $rev ?>
<BR> Tous droits réservés <BR>
     Licence d'utilisation accordée à : ADMINISTRATION<BR>
     Product ID = <b> <?php print PRODUCTID?> </b>
<BR><BR>
<textarea cols=60 rows=8 STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;' readonly="readonly">
<?php droit(); ?>
</textarea>
<br><br>
T.R.I.A.D.E. ©, <?php print DATEOUT ?><br />
http://www.triade-educ.org
</ul>
<!-- // fin de la saisie -->
</TD></TR></table>
<SCRIPT type="text/javascript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT type="text/javascript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
