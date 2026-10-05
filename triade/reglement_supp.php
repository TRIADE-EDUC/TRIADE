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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/alertify.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
$cnx = cnx();

$suppOk = false;
if (isset($_POST["supp"])) {
	$cr = reglementSup($_POST["saisie_id"]);
	if ($cr) {
		@unlink("./data/circulaire/" . trim($_POST["saisie_nom_fic"]));
		$suppOk = true;
	} else {
		error(0);
	}
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGCIRCU19 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign="top">

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
	$data = reglementAffAdmin();
	if (countTriade($data) <= 0) {
?>
<div class="na-card"><p style="font-size:13px;color:#888;margin:0;text-align:center;"><?php print LANGRECH1 ?></p></div>
<?php
	} else {
?>
<div class="na-card" style="padding:0;">
<table style="width:100%;border-collapse:collapse;">
  <thead>
    <tr>
      <th class="cc-th" style="width:90px;"><?php print LANGTE7 ?></th>
      <th class="cc-th"><?php print LANGFORUM12 ?></th>
      <th class="cc-th"><?php print LANGCIRCU20 ?></th>
      <th class="cc-th" style="width:80px;text-align:center;"><?php print LANGBT50 ?></th>
    </tr>
  </thead>
  <tbody>
<?php
		for ($i = 0; $i < countTriade($data); $i++) {
?>
    <form method="post">
    <tr class="cc-tr-data">
      <td style="padding:6px 10px;border-bottom:1px solid #e8eaf6;white-space:nowrap;"><?php print dateForm($data[$i][4]) ?></td>
      <td style="padding:6px 10px;border-bottom:1px solid #e8eaf6;">
        <strong><?php print $data[$i][1] ?></strong>
        <span class="htip-wrap" style="margin-left:4px;"><img src='./image/help.gif' width='12' height='12' border='0'><span class="htip"><?php print LANGCIRCU211 ?> : <?php print $data[$i][2] ?></span></span>
      </td>
      <td style="padding:6px 10px;border-bottom:1px solid #e8eaf6;">
        <a href="visu_document.php?fichier=./data/circulaire/<?php print $data[$i][3] ?>" target="_blank" style="color:#3c4a8a;font-size:12px;">Visualiser</a>
      </td>
      <td style="padding:6px 10px;border-bottom:1px solid #e8eaf6;text-align:center;">
        <input type="hidden" name="saisie_id" value="<?php print $data[$i][0] ?>">
        <input type="hidden" name="saisie_nom_fic" value="<?php print $data[$i][3] ?>">
        <button type="submit" name="supp" value="<?php print LANGBT50 ?>" class="btn-enr" style="background:#c62828;font-size:11px;padding:3px 10px;"><?php print LANGBT50 ?></button>
      </td>
    </tr>
    </form>
<?php
		}
?>
  </tbody>
</table>
</div>
<?php
	}
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
Pgclose();
?>
<?php if ($suppOk) : ?>
<script>document.addEventListener('DOMContentLoaded', function(){ alertify.success('<?php print LANGCIRCU19 ?> supprimé'); });</script>
<?php endif; ?>
</BODY></HTML>
