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
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("menuadmin");
?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'.js'?>"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>
<?php top_h(); ?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'1.js'?>"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGbasededoni31 ?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td valign=top>
<br />
<ul><font class="T2"><?php print LANGbasededoni32 ?></font></ul>
<br />
<ul><ul>
<table height=150  >

<form action='./base_de_donne_importation21.php'  method="post">
<tr>
<td align=right><font class="T2"><?php print LANGbasededoni33 ?></font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print CLICKICI ?>","rien");</script></td>
</tr>
</form>
<tr><td height='10' ></td></tr>

<form action='./base_de_donne_importation22.php'  method="get">
<input type='hidden' name='id' value='profxls' /> 
<tr>
<td align=right><font class="T2"><?php print LANGbasededoni34 ?></font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print CLICKICI ?>","rien");</script></td>
</tr>
</form>
<tr><td height='10' ></td></tr>

<form action='./base_de_donne_importation22.php'  method="get">
<input type='hidden' name='id' value='scolairexls' /> 
<tr>
<td align=right><font class="T2"><?php print LANGbasededoni35 ?></font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print CLICKICI ?>","rien");</script></td>
</tr>
</form>
<tr><td height='10' ></td></tr>

<form action='./base_de_donne_importation22.php'  method="get">
<input type='hidden' name='id' value='administrationxls' /> 
<tr>
<td align=right><font class="T2"><?php print LANGTMESS492 ?></font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print CLICKICI ?>","rien");</script></td>
</tr>
</form>
<tr><td height='10' ></td></tr>


<form action='./base_de_donne_importation22.php'  method="get">
<input type='hidden' name='id' value='personnelxls' /> 
<tr>
<td align=right><font class="T2"><?php print LANGTMESS493 ?></font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print CLICKICI ?>","rien");</script></td>
</tr>
</form>
<tr><td height='10' ></td></tr>


<form action='./base_de_donne_importation23.php'  method="get">
<input type='hidden' name='id' value='matierexls' /> 
<tr>
<td align=right><font class="T2"><?php print LANGTMESS496 ?></font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print CLICKICI ?>","rien");</script></td>
</tr>
</form>
<tr><td height='10' ></td></tr>



<form action='./base_de_donne_importation72.php'  method="get">
<input type='hidden' name='id' value='entreprisexls' /> 
<tr>
<td align=right><font class="T2"><?php print LANGTMESS494 ?></font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print CLICKICI ?>","rien");</script></td>
</tr>
</form>

<tr><td height='10' ></td></tr>

<form action='./base_de_donne_importation22.php'  method="get">
<input type='hidden' name='id' value='tuteurstagexls' />
<tr>
<td align=right><font class="T2"><?php print "Import tuteur de stage" ?></font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print CLICKICI ?>","rien");</script></td>
</tr>
</form>




<tr><td height='10' ></td></tr>

<form action='./base_de_donne_importation82.php'  method="get">
<input type='hidden' name='id' value='entreprisexls' /> 
<tr>
<td align=right><font class="T2"><?php print LANGTMESS495 ?></font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print CLICKICI ?>","rien");</script></td>
</tr>
</form>
<tr><td height='10' ></td></tr>

</table>
</ul></ul>
<br><br>
<!-- // fin  -->
</td></tr></table>
<BR>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'2.js'?>"> </SCRIPT>
</BODY></HTML>
