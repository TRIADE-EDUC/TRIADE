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
 ***************************************************************************
 ************************************************************
 Last updated: 20/07/2004   par Taesch  Eric
 *************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./FCKeditor/editor/css/fck_editorarea.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<?php
include_once("common/config.inc.php"); // futur : auto_prepend_file
include_once("librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);
?>
<title>Triade - Compte de <?php print ucwords($mySession[Sp])." ".strtoupper($mySession[Sn])?></title>
</head>
<body bgcolor="#FAEBD7" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menuparent.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/menuparent1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr bgcolor="#666666">
<td height="2"> <b><font color="#FFFFFF"><?php print LANGPROFP3?></font></td>
</tr>
<tr bgcolor="#CCCCCC">
<td valign=top>
<!-- // fin  -->
<table width=100% border=0>
<?php
$idclasse=chercheIdClasseDunEleve($mySession[Spid]);
$nomclasse=chercheClasse($idclasse);
// id,idclasse,commentaire,date_saisie
$data=aff_news_prof_p($idclasse);
if (count($data) > 0 ) { 
?>
<table align=center border=1 bordercolor="#CCCCCC" width=100%>
<tr><td  bgcolor="#FFFFFF" bordercolor="#000000">
<table align=center border=0 width=97%>
<?php
for($i=0;$i<count($data);$i++) {
?>
<tr><td>
<br>
&nbsp;<?php print $data[$i][2]?>
<br><br>
<div align=right><?php print dateForm($data[$i][3])?>&nbsp;&nbsp;</div>
</td>
</tr>
<tr><td><hr width=97%></td></tr>
<?php
}
?>
</table>
<?php

}else {
?>
<br><br>
<center><font size=2><?php print LANGPARENT1?></font></center>


<?php
}
?>
</tr></td></table>
     <!-- // fin  -->
     </td></tr></table>

     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION[membre] == "menuadmin") :
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
   </BODY>
   </HTML>
   <?php @Pgclose() ?>
