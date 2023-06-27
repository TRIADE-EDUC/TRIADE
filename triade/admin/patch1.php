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
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");
$cnx=cnx();
?>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Gestion des patchs </font></b></td></tr>
<tr id='cadreCentral0' ><td >
<!-- // debut de la saisie -->
	<br />
	<ul>

	<form method="post" name="formulaire" action="patch2.php"  onSubmit="document.formulaire.rien.disabled=true" >
	<font class=T2><b>Mise à jour de Triade </b></font> <br><br>

	<br>
	<i>Pour les envois par ftp, vous devez tranférer les patchs via votre client ftp dans le répertoire : 
	
	<b><?php print "/".ECOLE."/".ADMIN."/patch_ftp/" ?></b></i>
	<br>
	<br>
	Actuellement, il reste <strong><?php print human_readable(diskfreespace("../")); ?></strong> 
                               <i><?php print filesize_format(diskfreespace("../")); ?></i> d'espace libre <br>sur votre serveur.
	<br>
	<br>
	</ul>

	<font class='T2'>&nbsp;&nbsp;Liste des patchs pouvant être installés : </font><br><br>
	<table border='1' width='95%'  bordercolor='#000000' align='center'  >
	<tr>
	<td id='bordure' bgcolor="yellow" >&nbsp;Nom du patch&nbsp;</td>
	<td id='bordure' width='10%' bgcolor="yellow" colspan='2' >&nbsp;Choix&nbsp;</td>
	</tr>
<?php

if (isset($_GET["idsupp"])) {
	$ficsupp="./patch_ftp/".$_GET["idsupp"].".zip";
	if (file_exists($ficsupp)) { @unlink($ficsupp); }
}

$dirname = './patch_ftp/';
$dir = opendir($dirname); 
while($file = readdir($dir)) {  $files[] = $file; }
sort($files);
foreach($files as $key=>$file){
	if($file != '.' && $file != '..'  && $file != '.htaccess'  && !is_dir($dirname.$file))	{
		echo "<tr class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >";
		print "<td id='bordure' >&nbsp;$file</td>";
		print "<td id='bordure' align='center' ><input type=radio name='patch_ftp' value=\"$file\" title='Sélectionné' onclick=\"document.formulaire.rien.disabled=false;\" />";
		if (verifpatchinstall($file)) {
			print "<a href='#'  onMouseOver=\"AffBulle('Déjà Installé !! ');\"  onMouseOut='HideBulle()';><img src='../image/commun/important.png' border='0' alt='' /></a>";
		}
		$fic=preg_replace('/\.zip/','',$file);
		print "</td><td id='bordure' ><a href='patch1.php?idsupp=$fic'><img src='../image/commun/trash.png' border='0' alt='Supprimer' /></a>";
		print "</td>";
		print "</tr>";
	}
}
closedir($dir);
?>	
</table>
<?php
print "<br><br>&nbsp;&nbsp;<input type='submit' value='Valider le patch' name='rien' class='BUTTON' disabled='disabled' >";
print "</form>";

PgClose();
?>
<br><br><br>
<!-- // fin de la saisie -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</body>
</html>
