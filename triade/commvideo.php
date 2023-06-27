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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./framaplayer/framaplayer.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title> </head>
<body  id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" onunload="attente_close()" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "News vidéo" ?></font></b></td>
<?php
include_once("./librairie_php/db_triade.php");
validerequete("2");
$lienvideo="http://";
?>
<tr id='cadreCentral0'>
<td >

<br />
<form method="post"   name="formulaire" action="acces2.php" >
<table  width=100%  border="0" align="center" >
<tr  >
<td align="right"><font class="T2"><?php print LANGMESST702 ?> : </font></TD>
<TD align="left"><input type="text" name="saisie_titre" size=50 maxlength=30 value=""></td>
</tr>
<tr>
<td align="right"  valign=top ><font class="T2"><?php print LANGMESS301 ?></font></TD>
<TD  align="left" ><input type="text" name="saisie_lien" size=40  value="<?php print $lienvideo ?>" onclick="this.value=''; this.form.saisie_lien_youtube.value='';" > <i>(format mp4 ou webm)</i></td>
</td>
</tr>
<tr>
<td align="right"  valign=top ><font class="T2"><?php print LANGMESS302 ?></font></TD>
<TD  align="left" ><input type="text" name="saisie_lien_youtube" size=40  onclick="this.value=''; this.form.saisie_lien.value='';" value="<?php print $lienvideo ?>" > <i></i> <A href='#' onMouseOver="AffBulle3('INFORMATION','image/commun/info.jpg','<font face=Verdana size=1><?php print LANGMESST703." YouTube (http://www.youtube.com/watch?v=xxxxxx)" ?></FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='./image/help.gif' align=center border=0></A></td>
</tr>
</table>
<br /><br />
<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit3("<?php print LANGENR ?>","createvideo","onclick='attente();'");</script>
<br /><br />
</td></tr></table>
</form>

</td>
</tr></table>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
            print "</SCRIPT>";

       endif ;
     ?>
</body>
</html>
