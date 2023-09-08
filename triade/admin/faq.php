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

Last updated: 15.07.2002    par Taesch  Eric
************************************************************/  -->
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="librairie_js/acces.js"></script>
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="librairie_js/info-bulle.js"></script>
<title>Triade admin</title>
</head>
<body bgcolor="#FAEBD7" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="/admin_triade1/librairie_js/menudepart.js"></SCRIPT>

             <!-- // texte du menu qui defile   -->
              <?php include("librairie_php/lib_defilement.php"); ?>
             <!--      // fin du texte    -->

             </TD><td width="472" valign="middle" rowspan="3" align="center">

             <!--   -->
             <div align='center'><?php top_h(); ?>
             <!--  -->

<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr bgcolor="#666666">
<td height="2"> <b><font  color="#FFFFFF">F.A.Q proposé</font></b></td>
</tr>
<tr bgcolor="#CCCCCC"> <td valign=top>
<TABLE width="100%" border="1" bordercolor="#000000">
<TR>
<TD bgcolor="yellow" align=center width=10%>Date</TD>
<TD bgcolor="yellow" align=center>Détail</TD>
<TD bgcolor="yellow" align=center width=10%>Supprimer</TD>
</TR>
<?php
include_once("librairie_php/db_triade_admin.php");
// connexion P
$cnx=cnx();
error($cnx);
if ($_GET[id]) {
	efface_faq($_GET[id]);
}

$data=affiche_faq();
for($i=0;$i<count($data);$i++)
{
?>
<TR>
<TD bgcolor="#ffffff" valign=top>&nbsp;<?php print dateForm($data[$i][1])?></TD>
<TD bgcolor="#ffffff" valign=top>&nbsp;<?php print $data[$i][0]?></TD>
<TD bgcolor="#ffffff" valign=top><input type=button STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" value="Supprimer" onclick="open('faq.php?id=<?php print $data[$i][2]?>','_parent','')"></TD>
</TR>
<?php
}
?>

</table>

</td></tr></table>


<SCRIPT language="JavaScript">InitBulle("#000000","#CCCCFF","red",1);</SCRIPT>

<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
<?php
Pgclose($cnx);
?>
 </body>
</html>
