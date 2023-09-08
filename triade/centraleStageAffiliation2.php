<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET
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
<title>Triade - Affiliation </title>
</head>
<?php
include_once('common/config.inc.php');
include_once('common/config2.inc.php');
include_once('common/productId.php');
include_once('librairie_php/db_triade.php');
$cnx=cnx();
if (isset($_POST["rien"])) {
	$contact=$_POST["contact"];
	$email=$_POST["email"];
	$etablissement=$_POST["etablissement"];
	$ville=$_POST["ville"];
	$pays=$_POST["pays"];
	$productidclient=$_POST["productidclient"];
	enrDemandeAffiliation($contact,$email,$etablissement,$ville,$pays,$productidclient);
	print "<script language='JavaScript' src='https://support.triade-educ.org/centralestage/demandeaffiliation.php?productidclient=$productidclient&productidcentral=".PRODUCTID."'></script>";

}
?>
<body id='cadreCentral0' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<br>
<center>
<br><br>
<font class=T2><b>Demande enregistrée.</b></font><br><br>
</center>
</BODY>
</HTML>
