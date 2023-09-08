<?php
error_reporting(0);
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET 
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
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
</head>
<body id='cadreCentral0' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php 
include_once("./librairie_php/db_triade.php"); 
?>
<br><br>
<table border=0 align=center >
<tr> 
<form action='gestion_central_stage_visu.php' method='post' target="_top">
<td align=right><font class="T2"><?php print "Souhaits en cours" ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print "Accès"?>","rien"); //text,nomInput</script></td>
</form>
</tr>
<tr><td height='10' ></td></tr>
<?php  if (file_exists("./common/config.centralStage.php")) { ?>
	<tr>
	<form action='gestion_central_stage_ajout.php' method='post' target="_top">
	<td align=right><font class="T2"><?php print "Ajouter un souhait" ?> :</font></td>
	<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print "Accès"?>","rien"); //text,nomInput</script></td>
	</form>
	</tr>
	<tr><td height='10' ></td></tr>
	<tr>
	<form action='gestion_central_stage_config_mail.php' method='post' target="_top">
	<td align=right><font class="T2"><?php print "Config. pour l'envoi d'email aux entreprises" ?> :</font></td>
	<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print "Accès"?>","rien"); //text,nomInput</script></td>
	</form>
	</tr>
	<tr><td height='10' ></td></tr>

<?php } ?>
</table>
<br><br>
</BODY></HTML>
