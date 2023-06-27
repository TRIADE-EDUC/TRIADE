<?php
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
error_reporting(0);
$date=date("d/m/Y \à G.i:s");
$fp=fopen("../data/error.log","a+");
fwrite($fp, "<font color=red>Erreur Type : Accès non autorisé sur le compte Administrateur Triade</font><BR>Visité le $date par $_SERVER[REMOTE_ADDR] <BR>avec $_SERVER[HTTP_USER_AGENT] <BR><hr><br>\r\n");
fclose($fp);
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<title>Accès IMPOSSIBLE</title>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include_once("../librairie_php/langue-text-fr.php"); ?>
<BR><BR><BR><BR><br>
<center>
<table width="57%" border="0" align="center" >
<tr >
<td colspan=2>
<div align="center"><b><font color="red" class=T2 ><?php print LANGacce_ref2  ?><br>
</font></b>
<p><font color="#000000" class=T2><?php print LANGacce_ref3  ?></p>
<BR>
</font>
</div>
</td>
</tr>
</table>
<br><br>
<?php  print LANGPIEDPAGE ?>
<br /><br />
<img src='../image/commun/triade-xhtml.jpg' alt='XHTML' />  <img src='../image/commun/triade-w3C.jpg' alt='w3C' /> <img src='../image/commun/triade-css.png' alt='css' /> <a href='http://www.triade-educ.com/accueil/don-triade.php' target='_blank' ><img border='0' src='../image/commun/triade_paypal.png' alt='Paypal' /></a><br /><br />
<BR><BR>
</center></BODY></HTML>

