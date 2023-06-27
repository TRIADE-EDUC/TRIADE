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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php");
                include_once('librairie_php/db_triade.php');
		$cnx=cnx();
if (isset($_POST["supp"])) {
	$cr=reglementSup($_POST["saisie_id"]) ;
        if($cr) {
		$nomfichier=$_POST["saisie_nom_fic"];
		@unlink ("./data/circulaire/".trim($_POST["saisie_nom_fic"]));
        // alertJs("Circulaire supprimée --  Service Triade");
	    // reload_page('circulaire_supp.php');
        }else{
                error(0);
        }
}
?>
     <SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
     <?php include("./librairie_php/lib_defilement.php"); ?>
     </TD><td width="472" valign="middle" rowspan="3" align="center">
     <div align='center'><?php top_h(); ?>
     <SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
     <table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGCIRCU19?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->


<table bgcolor=#FFFFFF border=1 bordercolor="#CCCCCC" width=100%>
<?php

if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
	$data=reglementAffAdmin();
?>
	<tr>
	<td bgcolor="yellow" width=5%><?php print LANGTE7 ?></td>
	<td bgcolor="yellow"><?php print LANGFORUM12?></td>
	<td bgcolor="yellow"><?php print LANGCIRCU20 ?></td>
	<td bgcolor="yellow" align=center width=5%><?php print LANGBT50?></td>
	</tr>
<?php
	for($i=0;$i<count($data);$i++)
	{
?>
	<form method=post>
	<tr  class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
	<td valign=top><?php print dateForm($data[$i][4])?></td>
	<td valign=top>
<A href='#' onMouseOver="AffBulle('<font face=Verdana size=1><B> <?php print LANGCIRCU21 ?>:</font> <font color=blue><?php print $data[$i][2]?></font></FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'>
<?php print $data[$i][1]?>
</A>
	</td>
	<td valign=top width=15>&nbsp;[&nbsp;<a href="visu_document.php?fichier=./data/circulaire/<?php print $data[$i][3]?>" title="accès au fichier" target="_blank">Visualiser</a>&nbsp;]&nbsp;</td>
	<td valign=top>
	<input type=submit name=supp value="<?php print LANGBT50?>" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" onclick="this.value='<?php print LANGattente222 ?>'">
	<input type=hidden name="saisie_id" value="<?php print $data[$i][0]?>">
	<input type=hidden name="saisie_nom_fic" value="<?php print $data[$i][3]?>">
	</td>
	</tr>
	</form>
<?php
	}
}
?>

</table>
     <!-- // fin  -->
     </td></tr></table>
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
     Pgclose();
     ?>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
   </BODY></HTML>
