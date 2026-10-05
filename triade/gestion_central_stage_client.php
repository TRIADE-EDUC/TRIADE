<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -
 *   Site                 : http://www.triade-educ.com
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
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<?php include_once("./common/productId.php"); ?>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="185">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Accréditation à une centrale de stage</font></b></td></tr>
<tr id='cadreCentral0'>
<td valign="top">

<?php
$productid=PRODUCTID;
$option=file_get_contents("https://support.triade-educ.org/centralestage/ajaxclientcentrale.php?productid=$productid");
?>

<form method="post">
<div class="na-card">

  <div class="na-row">
    <span class="na-lbl">Centrale de stage :</span>
    <select name="centrale" id="centrale" class="cc-select">
      <?php print $option ?>
    </select>
  </div>

  <div class="na-row">
    <span class="na-lbl">Mot de passe :</span>
    <input type="text" name="pass" size="40" class="cc-select">
  </div>

</div>
<div class="na-foot">
  <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT19 ?>","create");</script>
</div>
</form>

<?php
if (isset($_POST["create"])) {
	$pass=$_POST["pass"];
	$url=$_POST["centrale"];
	@unlink("./common/config.centralStageClient.php");
	$f=fopen("./common/config.centralStageClient.php","w");
	fwrite($f,"<?php\n");
	fwrite($f,"define(\"URLCENTRALSTAGE\",\"$url\");\n");
	fwrite($f,"define(\"PASSCENTRALSTAGE\",\"$pass\");\n");
	fwrite($f,"?>\n");
	fclose($f);
	print "<div style='margin:10px 0;padding:10px 14px;background:#e8f5e9;border:1px solid #a5d6a7;border-radius:8px;font-size:13px;color:#2e7d32;font-weight:600;text-align:center;'>".LANGDONENR."</div>";
}
?>

</td></tr></table>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
?>
<script>upcentrale('<?php print PRODUCTID ?>');</script>
</BODY></HTML>
