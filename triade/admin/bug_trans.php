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
	include("./librairie_php/lib_licence.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade Admin</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Bug Transmission.</font></b></td></tr>
<tr id='cadreCentral0'><td valign=top >
<form method=post action="bug_trans2.php" name="formulaire" ><br>
&nbsp;&nbsp;<font class="T2">Ajouter un commentaire :</font>
<br><br>
&nbsp;&nbsp;<textarea  name="message" cols=90 rows=7 onkeypress="compter(this,'200',this.form.CharRestant)" ></textarea>
<input type=text name='CharRestant' size=3 disabled='disabled' value='' />
<br>
<?php
include_once('librairie_php/db_triade_admin.php');
$cnx=cnx();
$data=aff_bug();
// $data : tab bidim - soustab 3 champs
$nb=0;
for($i=0;$i<count($data);$i++)
{
	$nb++;
	if (trim($data[$i][5]) == "Choix ...." ) {$data[$i][5]="&nbsp;"; }
	if (trim($data[$i][6]) == "Choix ...." ) {$data[$i][6]="&nbsp;"; }
	$com=str_replace("\"","'",$data[$i][7]);
?>
	<input type=hidden name='bug[]' value="<?php print $data[$i][4]?>">
	<input type=hidden name='bug[]' value="<?php print $data[$i][6]?>">
	<input type=hidden name='bug[]' value="<?php print $data[$i][5]?>">
	<input type=hidden name='bug[]' value="<?php print $com?>">
<?php
}
Pgclose($cnx);
?>
<br /><br />
<input type=hidden name=nb value='<?php print $nb?>'>
&nbsp;&nbsp;<font class="T2">Email :</font> <input type=text name=mail maxlength=50 size=40><br><br><br>
<table border=0 align=center>
<tr><td>
<script language=JavaScript>buttonMagicSubmit3("Confirmation de l envoi","bug_trans2.php","_parent","",""); //text,nomInput</script>
</td></tr></table>
</form>
<br />
<!-- // fin de la saisie -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
