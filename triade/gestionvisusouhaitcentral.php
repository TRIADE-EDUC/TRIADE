<?php
session_start();
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
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/langue.php");
include_once("./librairie_php/db_triade.php");
$productid=$_GET["id"];
$p=$_GET["p"];
$cnx=cnx();
if (!file_exists("./common/config.centralStage.php")) {
	verifAccesCentrale("$productid","$p");
}
$_SESSION["productidstage"]=$productid;

?>
<!-- // fin  -->
<br>
<form method=post name=formulaire action="gestion_central_stage_visu2.php" target="_blank">
<input type=hidden name="productid" value="<?php print $productid ?>" />
<input type=hidden name="p" value="<?php print $p ?>" />
<table width="100%" border="0" align="center">
<tr>
<td align="right"><font class='T2'><?php print "Période désirée" ?> :</font></td>
<td    align="left">
<select name='periode' >
<option id='select0' value='0' ><?php print LANGCHOIX ?></option>
	<?php
	$data=periodeStageCentralDate();
	for($i=0;$i<count($data);$i++) {
		print "<option id='select1' value='".$data[$i][2]."' >(".$data[$i][3].") ".dateForm($data[$i][0])." - ".dateForm($data[$i][1])."</option>";
	}
?>
</select>
</td></tr>
</table>
<br><br>
<table border=0 align=center>
<tr>
	<td><script language=JavaScript>buttonMagicSubmit3("<?php print LANGPER27 ?>","rien","");</script></td>
</tr></table>
</form>
<?php Pgclose(); ?>
</BODY>
</HTML>
