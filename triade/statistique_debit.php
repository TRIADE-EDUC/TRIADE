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

Last updated: 28.06.2002    par Taesch  Eric
*************************************************************/  -->
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<?php
include_once("./common/lib_ecole.php");
include_once("./common/lib_admin.php");
include_once("./".REPADMIN."/librairie_php/lib_error.php");
include_once("./".REPADMIN."/librairie_php/mactu.php");
if (empty($_SESSION["admin1"])) {
    print "<script language='javascript'>";
    print "location.href='/".REPECOLE."/".REPADMIN."/acces_refuse.php'";
    print "</script>";
    exit;
}
?>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./<?php print REPADMIN?>/librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<title>Triade</title>
</head>
        <body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
        <SCRIPT language="JavaScript" src="./<?php print REPADMIN?>/librairie_js/menudepart.js"></SCRIPT>

             <!-- // texte du menu qui defile   -->
               <?php include("./".REPADMIN."/librairie_php/lib_defilement.php"); ?>
             <!-- // fin du texte   -->

             </TD><td width="472" valign="middle" rowspan="3" align="center">

             <!--   -->
             <div align='center'><?php top_h(); ?>
             <!--  -->

            <SCRIPT language="JavaScript" src="./<?php print REPADMIN?>/librairie_js/menudepart1.js"></SCRIPT>

             <table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
             <tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Statistique par débit.</font></b></td></tr>
             <tr id='cadreCentral0'><td > <p align="left"><font color="#000000">
	     <br />
<ul><input type=button value="Autres statistiques " onclick="history.go(-1);"  class='bouton2'></ul><br />
               <!-- // debut de la saisie -->
<blockquote>

<?php
include_once("./common/config.inc.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
$data=affStatDebit();
// $data :
$j=count($data);
for($i=0;$i<count($data);$i++)
{
	?>
	<table border=1 bgcolor="#ffffff" width=50%>
	<tr><td colspan=4>Type de connexion : <b><?php print $data[$i][0]?></b></td></tr>
	<tr>
		<td>Nombre de connexion : <b><?php print $data[$i][1]?></b></td>

	</tr>
	</table>
	<br/> <br />
	<?php

}
?>
              </blockquote>
<ul><input type=button value="Autres statistiques " onclick="history.go(-1);"  class='bouton2'></ul>
<br /><br />
</td></tr></table>
                   <!-- // fin de la saisie -->
<SCRIPT language="JavaScript" src="./<?php print REPADMIN?>/librairie_js/menudepart2.js"></SCRIPT>
</body>
</html>
