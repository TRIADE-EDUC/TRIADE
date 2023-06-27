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
<script language="JavaScript" src="./librairie_js/lib_histo_heure.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<SCRIPT language="JavaScript" src="./<?php print REPADMIN?>/librairie_js/menudepart.js"></SCRIPT>
<?php include("./".REPADMIN."/librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./<?php print REPADMIN?>/librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Statistique par connection et par heure.</font></b></td></tr>
<tr id='cadreCentral0'><td > <p align="left"><font color="#000000">
<!-- // debut de la saisie -->
<?php
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
$data=affStatConxParHeure();
// $data :
// mise à zero
for ($i=0;$i<=12;$i++) {
	$heure[$i]=0;
}
for ($i=13;$i<=24;$i++) {
	$heure2[$i]=0;
}
// initialisation
// jusqu'a 12 heure
for($i=0;$i<count($data);$i++)
{
	for ($j=0;$j<=12;$j++) {
		if ($j == $data[$i][0]) {
			$heure[$j]= $data[$i][1];
		}
	}
}
foreach($heure as $key => $value) {
	$heure_script.="'".$key."'";
	$nb_script.=$value;
	if ($key < 12 ) {
		$heure_script.=",";
		$nb_script.=",";
	}
}
// initialisation
// de 13 jusqu'a 24 heure
for($i=0;$i<count($data);$i++)
{
	for ($t=13;$t<=24;$t++) {
		if ($t == $data[$i][0]) {
			$heure2[$t]= $data[$i][1];
		}
	}
}
foreach($heure2 as $key => $value) {
	$heure_script2.="'".$key."'";
	$nb_script2.=$value;
	if ($key < 24 ) {
		$heure_script2.=",";
		$nb_script2.=",";
	}
}
?>
<script language=JavaScript>
var tabX=new MakeTab(<?php print $heure_script?>);  // heure
var tabY=new MakeTab(<?php print $nb_script?>);   // nb_fois
AffHisto(tabX,tabY,350,100," stat du matin");
</script>
<br /><br />
<script language=JavaScript>
var tabW=new MakeTab(<?php print $heure_script2?>);  // heure
var tabZ=new MakeTab(<?php print $nb_script2?>);   // nb_fois
AffHisto(tabW,tabZ,350,100,"stat de l'apres midi");
</script>
<ul>
<br><input type=button value="Autres statistiques " onclick="history.go(-1);"  class='bouton2'>
</ul>
<!-- // fin de la saisie -->
</blockquote> </td></tr></table>
<SCRIPT language="JavaScript" src="./<?php print REPADMIN?>/librairie_js/menudepart2.js"></SCRIPT>
</body>
</html>
