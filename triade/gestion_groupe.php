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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGGRP25bis ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<!-- // debut form  -->
<center>
<br>
<table border=0 align=center width="100%">
<tr>
<form action='creat_groupe.php'  method="post">
<td align=right><font class="T2"><?php print LANGMESS154 ?> / Supprimer groupe :</font></td>
<td align=left ><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","rien"); //text,nomInput</script></td>
</tr>
</form>
<tr><td></td></tr>
<tr><td></td></tr>
<tr>
<form action='liste_groupe.php'  method="post">
<td align=right><font class="T2"><?php print LANGGRP26 ?> :</font></td>
<td align=left ><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","rien"); //text,nomInput</script></td>
</tr>
</form>
<tr><td></td></tr>
<tr><td></td></tr>

<tr>
<form action='liste_eleve_groupe.php' method="post">
<td align=right><font class="T2"><?php print LANGGRP48 ?> :</font></td>
<td align=left ><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","rien"); //text,nomInput</script></td>
</tr>
<tr><td></td></tr>
<tr><td></td></tr>
</form>

<tr>
<form action='liste_prof_groupe.php' method="post">
<td align=right><font class="T2"><?php print LANGMESS155 ?> :</font></td>
<td align=left ><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","rien"); //text,nomInput</script></td>
</tr>
<tr><td></td></tr>
<tr><td></td></tr>
</form>


<tr>
<form action='modif_groupe_ajout.php'  method="post">
<td align=right><font class="T2"><?php print LANGGRP27 ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGPER30?>","rien"); //text,nomInput</script></td>
</tr>
</form>

<tr><td></td></tr>
<tr><td></td></tr>

<tr>
<form action='modif_groupes_ajout_multi.php'  method="post">
<td align=right><font class="T2"><?php print LANGGRP27bis ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGPER30?>","rien"); //text,nomInput</script></td>
</tr>
</form>

<tr><td></td></tr>
<tr><td></td></tr>


<tr>
<form action='modif_groupe.php'  method="post">
<td align=right><font class="T2"><?php print LANGGRP28 ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT50?>","rien"); //text,nomInput</script></td>
</tr>
<tr><td></td></tr>
<tr><td></td></tr>
</form>

<tr><td></td></tr>
<tr><td></td></tr>

<tr>
<form action='check_groupe.php'  method="post">
<td align=right><font class="T2"><?php print LANGTMESS407 ?> :</font></td>
<td align=left><script language=JavaScript>buttonMagicSubmit("<?php print LANGTMESS406 ?>","rien"); //text,nomInput</script></td>
</tr>
</form>


</table>
<br><br>
</center>
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY>
</HTML>
