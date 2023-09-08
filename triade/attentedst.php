<?php
session_start();
include_once("./common/config2.inc.php");
if ((DSTPROF == "oui" ) && ($_SESSION["membre"] == "menuprof" )) {
        print "<script>location.href='calendrier_config_dst1.php';</script>";
        exit();
}
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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuprof");
$cnx=cnx();
error($cnx);
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPARENT23 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td valign=top>
<!-- // fin  -->
<table border=1 width=100% bordercolor="#000000" >
<tr>
<td bgcolor=yellow align=center width=5%  id='bordure' >&nbsp;Date&nbsp;</td>
<td bgcolor=yellow  id='bordure' >&nbsp;Classe</td>
<td bgcolor=yellow  id='bordure' >&nbsp;Devoir</td>
<td bgcolor=yellow align=center width=5% id='bordure' >&nbsp;Horaire&nbsp;</td>
<td bgcolor=yellow align=center width=5% id='bordure'>&nbsp;Supprimer&nbsp;</td>
<?php

if (isset($_GET["supp"])) { supp_dem_dst_by_prof($_GET["supp"],$_SESSION["id_pers"]); }

$data=attenteValidDST($_SESSION["id_pers"]); //id_dem,id_pers,date_dem,classe,mat_text,heure,duree
for($i=0;$i<count($data);$i++) {
	print "<tr class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >";
	print "<td id='bordure'>&nbsp;".dateForm($data[$i][2])."&nbsp;</td>";
	print "<td id='bordure'>&nbsp;".$data[$i][3]."&nbsp;</td>";
	print "<td id='bordure'>&nbsp;".$data[$i][4]." H&nbsp;</td>";
	print "<td id='bordure'>&nbsp;".$data[$i][5]."&nbsp;(".timeForm($data[$i][6])."h)</td>";
	print "<td id='bordure'>";
	print "<input type=button onclick=\"open('attentedst.php?supp=".$data[$i][0]."','_parent','')\" name=create value='Supprimer' class='bouton2' />";
	print "</td>";
	print "</tr>";
}


?>
</table>

     <!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION["membre"] == "menuadmin") :
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
     <SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
   </BODY></HTML>
