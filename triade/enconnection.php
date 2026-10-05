<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TYPE="text/css" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TYPE="text/css" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_chargement_image_cache.js"></script>
<script type="text/javascript" src="./librairie_js/logo.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib-opaque.js"></script>
<LINK REL="SHORTCUT ICON" href="./favicon.ico">
<title>Triade</title>
<?php
include_once("./librairie_php/lib_netscape.php");
include_once("./librairie_php/lib_licence2.php");
include_once("./common/lib_ecole.php");
include_once("./common/config2.inc.php");
if ($_COOKIE["langue-triade"] == "fr") {
        include_once("./librairie_php/langue-text-fr.php");
        include_once("librairie_php/langue-depart-text-fr.php");
        print "<script language=JavaScript src='librairie_js/languefrmenu-depart.js'></script>";
        print "<script language=JavaScript src='librairie_js/languefrfunction-depart.js'></script>";

}elseif ($_COOKIE["langue-triade"] == "en") {
        print "<script language=JavaScript src='librairie_js/langueenmenu-depart.js'></script>";
        print "<script language=JavaScript src='librairie_js/langueenfunction-depart.js'></script>";
        include_once("./librairie_php/langue-text-en.php");
        include_once("librairie_php/langue-depart-text-en.php");

}elseif ($_COOKIE["langue-triade"] == "es") {
        print "<script language=JavaScript src='librairie_js/langueesmenu-depart.js'></script>";
        print "<script language=JavaScript src='librairie_js/langueesfunction-depart.js'></script>";
        include_once("./librairie_php/langue-text-es.php");
        include_once("librairie_php/langue-depart-text-es.php");
}else {
        print "<script language=JavaScript src='librairie_js/languefrmenu-depart.js'></script>";
        print "<script language=JavaScript src='librairie_js/languefrfunction-depart.js'></script>";
        include_once("./librairie_php/langue-text-fr.php");
        include_once("./librairie_php/langue-text-fr.php");
}
?>
<style>
.enc-wrap {
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  padding:40px 20px;
  gap:20px;
}
.enc-card {
  background:#fff;
  border-radius:10px;
  box-shadow:0 4px 20px rgba(8,10,102,.12);
  padding:36px 48px 28px;
  text-align:center;
  max-width:360px;
  width:100%;
}
.enc-spinner {
  display:inline-block;
  width:48px;
  height:48px;
  border:4px solid #e8eaf6;
  border-top-color:#080A66;
  border-radius:50%;
  animation:enc-spin .8s linear infinite;
  margin-bottom:18px;
}
@keyframes enc-spin { to { transform:rotate(360deg); } }
.enc-msg {
  font-family:Electrolize,Arial,sans-serif;
  font-size:15px;
  font-weight:700;
  color:#080A66;
  margin-bottom:6px;
}
.enc-sub {
  font-size:12px;
  color:#9099b0;
  margin-bottom:22px;
}
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="preload_image()" onUnload="open('deconnection.php','quitte','width=250,height=100')" >
<script type="text/javascript" >var mailcontact="<?php if (MAILCONTACT != "") { print MAILCONTACT; }else{ print ""; } ?>"; </script>
<script type="text/javascript" >var urlcontact="<?php if (URLCONTACT != "") { print URLCONTACT; }else{ print ""; }  ?>"; </script>
<script type="text/javascript" >var urlnomcontact="<?php if (URLNOMCONTACT != "") { print URLNOMCONTACT; }else{ print ""; } ?>"; </script>

<?php
include_once("./librairie_php/lib_netscape.php");
include_once("../common/config2.inc.php");
if ((defined("POPUP")) && (POPUP == "non")) {
        print "<script language='JavaScript'>var popup='non';</script>";
}else {
        print "<script language='JavaScript'>var popup='oui';</script>";
}
?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGTTITRE3 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<div class="enc-wrap">
  <div class="enc-card">
    <div class="enc-spinner"></div>
    <div class="enc-msg"><i class="bi bi-box-arrow-in-right"></i>&nbsp;<?php print LANGTCONNECCOURS ?></div>
    <div class="enc-sub">Veuillez patienter&hellip;</div>
    <script language=JavaScript>buttonMagic("<?php print LANGTFERMCONNEC ?>","deconnection.php","quitte","width=250,height=100",";location.href='./index1.php';");</script>
  </div>
</div>
<?php
if (LAN == "oui") {
	include_once('librairie_php/xiti.php');
	print "<script type='text/javascript' src='https://support.triade-educ.org/support/info.php'></script>";
}
?>
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>

<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
statConecParHeure(date("G"));
?>
</BODY>
</HTML>
