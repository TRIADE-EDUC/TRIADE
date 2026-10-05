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
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" onunload="attente_close()">
<?php
include("./librairie_php/lib_licence.php");
@unlink("./data/fichier_gep/traitement.xls");
if (empty($_SESSION["adminplus"])) {
	print "<script>location.href='./base_de_donne_importation.php'</script>";
	exit;
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Importation du fichier</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<form method="post" action="./base_de_donne_importation910.php" name="formulaire" ENCTYPE="multipart/form-data">
<div class="na-card">

  <div class="na-row">
    <span class="na-lbl"><?php print LANGGEP2 ?> <b>(xls)</b> :</span>
    <input type="file" name="fichier1" size="20">
  </div>

  <div class="na-row">
    <span class="na-lbl">Options :</span>
    <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:#333;cursor:pointer;">
      <input type="checkbox" name="optionligne" value="1">
      Prendre la première ligne du fichier (<?php print LANGOUI ?>)
    </label>
  </div>

  <div style="font-size:12px;color:#555;margin-top:6px;"><?php print LANGbasededon21 ?></div>

</div>
<div class="na-foot">
  <script language=JavaScript>buttonMagicSubmit2("<?php print LANGBT23 ?>","<?php print LANGbasededon201 ?>","<?php print LANGBT5 ?>");</script>
</div>
</form>

</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
