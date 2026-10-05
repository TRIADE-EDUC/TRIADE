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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4-2.css">
<?php include("librairie_php/lib_licence.php"); ?>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade — Code d'accès</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="./librairie_js/menudepart.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Code d'accès</font></b></td></tr>
<tr id='cadreCentral0'><td valign="top">

<?php
if (file_exists('../common/productId.php')) {
    include_once('../common/productId.php');
    $productid = PRODUCTID;
}
?>

<div class="na-card" style="margin-top:12px;text-align:center;">
    <div style="margin-bottom:14px;">
        <form method="post" action="https://www.triade-educ.org/fr/keyonline.php?productid=<?php print $productid ?>" target="_blank" style="display:inline;">
            <span style="display:inline-flex;"><script language=JavaScript>buttonMagicSubmit("<?php print langbta1?>","Submit"); //text,nomInput</script></span>
        </form>
    </div>

    <div style="border-top:1px solid #e8eaf6;padding-top:16px;">
        <form method="post" action="key3.php">
            <div class="na-row" style="justify-content:center;gap:4px;">
                <input type="text" maxlength="8" name="pw1" size="9" class="cc-select" style="text-align:center;letter-spacing:2px;">
                <span style="font-weight:700;color:#080A66;font-size:16px;">–</span>
                <input type="text" maxlength="4" name="pw2" size="5" class="cc-select" style="text-align:center;letter-spacing:2px;">
                <span style="font-weight:700;color:#080A66;font-size:16px;">–</span>
                <input type="text" maxlength="3" name="pw3" size="4" class="cc-select" style="text-align:center;letter-spacing:2px;">
            </div>
            <div style="margin-top:14px;">
                <span style="display:inline-flex;"><script language=JavaScript>buttonMagicSubmit("<?php print langbta2?>","Submit"); //text,nomInput</script></span>
            </div>
            <div style="margin-top:12px;font-size:11px;color:#555;font-style:italic;">
                <?php print langkey7 ?>
            </div>
        </form>
    </div>
</div>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
