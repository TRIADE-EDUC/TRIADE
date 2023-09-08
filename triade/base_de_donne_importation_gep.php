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
Last updated: 15.08.2004    par Taesch  Eric
*************************************************************/ -->
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
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'.js'?>"></SCRIPT>
<?php include_once("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>
<?php top_h(); ?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'1.js'?>"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGBASE5?> </font></b></td>
</tr>
<tr id='cadreCentral0' >
<td valign=top>
<ul>
<br />
<font class=T2><?php print LANGBASE4?></font>
<BR><BR>
<font class=T2><?php print LANGBASE3_2?></font><br>
<BR>

<form method=get action="base_de_donne_key.php" onsubmit="return suite()" >
<table  border="1" bgcolor="#FCE4BA" bordercolor=#000000 >
<tr bgcolor="#FFCC00"><td id='bordure'><input type="radio" name="dbf_name" value="F_ELE.DBF"  style="background-color:#FFCC00" checked="checked" > F_ELE.DBF - (fichier élève)</td></tr>
<tr bgcolor="#FFCC00"><td id='bordure'><input type="radio" name="dbf_name" value="F_ERE.DBF"  style="background-color:#FFCC00"  > F_ERE.DBF - (fichier élève)</td></tr>
<tr bgcolor="#FFCC00"><td id='bordure'><input type="radio" name="dbf_name" value="F_WIND.DBF" style="background-color:#FFCC00" > F_WIND.DBF - (fichier enseignant)</td></tr>
<tr bgcolor="#FFCC00"><td id='bordure'><input type="radio" name="dbf_name" value="F_TMT.DBF"  style="background-color:#FFCC00"  > F_TMT.DBF - (fichier matière)</td></tr>


</table>
</ul>
<script language=JavaScript>
function suite() {
	var confirmation=confirm('<?php print LANGbasededoni21_2 ?>','')
    if (confirmation) {
       return true;
    }else{
    	return false;
    }
}
</script>
<BR><div align="center"> <input type=submit  class="BUTTON" value='<?php print LANGCHER9 ?> >'> </div><br />
<br>
<input type=hidden name="base" value="gep"
</form>
<!-- // fin  -->
</td></tr></table>
<BR>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'2.js'?>"> </SCRIPT>
</BODY></HTML>
