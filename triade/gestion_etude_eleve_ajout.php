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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("2");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGETUDE33 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<form method="post" onsubmit="return validecreatetude()" name="formulaire" action="gestion_etude_eleve_ajout2.php">
<div class="na-card">

  <div class="na-row">
    <span class="na-lbl"><?php print LANGETUDE34 ?> :</span>
    <select name="saisie_etude" class="cc-select">
      <option style="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
      <?php liste_etude_option(); ?>
    </select>
  </div>

  <div class="na-row" style="align-items:flex-start;">
    <span class="na-lbl" style="padding-top:4px;"><?php print LANGETUDE35 ?></span>
    <div>
      <select name="saisie_liste[]" size="6" style="width:150px;" multiple="multiple" class="cc-select">
        <?php select_classe(); ?>
      </select>
      <div style="margin-top:8px;padding:8px 12px;background:#fffde7;border:1px solid #f9c600;border-radius:6px;font-size:11px;color:#5a4000;max-width:220px;">
        <?php print LANGGRP3 ?> <b><?php print LANGGRP4 ?></b> <?php print LANGGRP5 ?>
      </div>
    </div>
  </div>

</div>
<div class="na-foot">
  <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT13 ?>","rien");</script>
  <button type="button" class="btn-retour" onclick="open('gestion_etude.php','_self','')">Retour</button>
</div>
</form>

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
</BODY></HTML>
