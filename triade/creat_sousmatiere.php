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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php echo $_SESSION['nom'].' '.$_SESSION['prenom']; ?></title>
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg) { alertify.error(msg); };</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx = cnx();
?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre'].".js'"; ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre']."1.js'"; ?>></SCRIPT>

<!-- ── Section navigation ── -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php echo LANGTITRE14; ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<div class="ca-nav-card">
  <div class="ca-nav-actions">
    <script language="JavaScript">buttonMagic("<?php echo LANGSUPP24 ?>","suppression_matiere.php","_parent","","");</script>
  </div>
</div>
</td></tr></table>

<br>

<!-- ── Section formulaire création ── -->
<form name="formulaire" method="POST" onsubmit="return valide_sous_matiere();" action="./creat_sousmatiere2.php">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php echo LANGTITRE14; ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<br>
<div class="ca-form-wrap">

  <fieldset class="ca-fieldset">
    <legend class="ca-legend"><?php echo LANGTITRE14; ?></legend>

    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGGRP9; ?></label>
      <select name="saisie_matiere" class="ca-select">
        <option value=""><?php echo LANGCHOIX; ?></option>
        <?php select_matiere3(53); ?>
      </select>
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGGRP10; ?></label>
      <input type="text" name="saisie_nom_matiere" size="33" maxlength="50" class="ca-input ca-input-wide">
    </div>
  </fieldset>

  <div class="ca-actions">
    <?php brmozilla($_SESSION["navigateur"]); ?>
    <script language="JavaScript">buttonMagicSubmit("<?php echo LANGBT17 ?>","rien");</script>
    <?php brmozilla($_SESSION["navigateur"]); ?>
  </div>

</div>

</td></tr></table>
</form>

<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre']."2.js'"; ?>></SCRIPT>
</body></html>
