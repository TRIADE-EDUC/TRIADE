<?php
session_start();
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
if (isset($_GET["saisie_efface"]) && $_GET["saisie_efface"] == "oui") {
    @unlink("../data/erreurs.log");
}
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4-2.css">
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<title>Triade — Warning code</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Gestion warning code</font></b></td></tr>
<tr id='cadreCentral0'><td>

<?php
$fic = "../data/erreurs.log";
if (file_exists($fic)) { ?>
<div style="margin:10px 8px 10px;height:560px;border:1px solid #c5cae9;background:#fafafa;border-radius:8px;overflow:auto;padding:12px;font-size:11px;font-family:monospace;color:#333;overflow-wrap:break-word;word-break:break-word;">
<?php readfile($fic); ?>
</div>
<?php } else { ?>
<div style="margin:14px 8px;padding:10px 14px;background:#e8f5e9;border:1px solid #a5d6a7;border-radius:6px;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;color:#2e7d32;">
    Aucun warning enregistré.
</div>
<?php } ?>

<div style="margin:0 8px 14px;display:flex;gap:10px;flex-wrap:wrap;">
    <a href="#" onclick="open('trans-erreur-code.php','_parent','')" class="btn-nav" style="text-decoration:none;">Transmettre au support Triade</a>
    <a href="erreur.php?saisie_efface=oui" class="btn-retour" style="text-decoration:none;">Effacer les warnings</a>
</div>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
