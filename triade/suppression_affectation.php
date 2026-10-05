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
 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg) { alertify.error(msg); };</script>
<title>Triade - Compte de <?php echo $_SESSION['nom'].' '.$_SESSION['prenom']; ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<script>
function suite() {
    alertify.confirm(
        '<?php echo addslashes(LANGTITRE21); ?>',
        '<?php echo addslashes(LANGAFF9); ?>',
        function() { location.href = './base_de_donne_key.php?base=suppression'; },
        function() {}
    );
}
</script>
<SCRIPT language="JavaScript" src="<?php echo './librairie_js/'.$_SESSION['membre'].'.js'; ?>"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="<?php echo './librairie_js/'.$_SESSION['membre'].'1.js'; ?>"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php echo LANGTITRE21; ?></font></b></td></tr>
<tr id='cadreCentral0'><td valign="top">

<div style="margin:14px 8px;">

    <div style="padding:12px 16px;background:#e8eaf6;border-radius:8px;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;color:#333;margin-bottom:12px;">
        <?php echo LANGAFF7; ?>
    </div>

    <div style="padding:12px 16px;background:#fff3f3;border:1px solid #f5c6c6;border-radius:8px;font-size:12px;font-weight:700;font-family:Electrolize,Trebuchet MS,Arial;color:#c0392b;margin-bottom:16px;">
        &#9888; <?php echo LANGAFF8; ?>
    </div>

    <div style="text-align:center;">
        <input type="button" value="<?php echo LANGBTS; ?>" onclick="suite();" class="btn-enr">
    </div>

</div>

</td></tr></table>

<SCRIPT language="JavaScript" src="<?php echo './librairie_js/'.$_SESSION['membre'].'2.js'; ?>"></SCRIPT>
</body></html>
