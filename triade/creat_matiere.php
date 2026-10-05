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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php echo $_SESSION['nom'].' '.$_SESSION['prenom']; ?></title>
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg) { alertify.error(msg); };</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre'].".js'"; ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre']."1.js'"; ?>></SCRIPT>

<!-- ── Section navigation ── -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php echo LANGTITRE13; ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<div class="ca-nav-card">
  <div class="ca-nav-actions">
    <script language="JavaScript">buttonMagic("<?php echo LANGMAT2 ?>","list_matiere.php","_parent","","");</script>
    <script language="JavaScript">buttonMagic("<?php echo LANGAGENDA86 ?>","base_de_donne_importation23.php?id=matierexls","_parent","","");</script>
    <script language="JavaScript">buttonMagic("<?php echo LANGMAT3 ?>","suppression_matiere.php","_parent","","");</script>
  </div>
</div>
</td></tr></table>

<br>

<!-- ── Section formulaire création ── -->
<form method="post" onsubmit="return verifcreatmatiere()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php echo LANGTITRE13; ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<br>
<div class="ca-form-wrap">

  <fieldset class="ca-fieldset">
    <legend class="ca-legend"><?php echo LANGTITRE13; ?></legend>

    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGGRP9; ?> <em style="font-size:10px;color:#666;"><?php echo LANGMESS208; ?></em></label>
      <input type="text" name="saisie_creat_matiere" size="33" maxlength="200" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGGRP9; ?> <em style="font-size:10px;color:#666;"><?php echo LANGTMESS450; ?></em></label>
      <input type="text" name="saisie_creat_matiere_en" size="33" maxlength="200" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGGRP9; ?> <em style="font-size:10px;color:#666;"><?php echo LANGMESS209; ?></em></label>
      <input type="text" name="saisie_creat_matiere_long" size="33" maxlength="250" class="ca-input ca-input-wide">
    </div>
    <div class="ca-field-row">
      <label class="ca-lbl"><?php echo LANGMESS210; ?></label>
      <input type="text" name="saisie_code_matiere" size="15" maxlength="20" class="ca-input">
    </div>
  </fieldset>

  <div class="ca-actions">
    <?php brmozilla($_SESSION["navigateur"]); ?>
    <script language="JavaScript">buttonMagicSubmit("<?php echo LANGMAT1 ?>","create");</script>
    <?php brmozilla($_SESSION["navigateur"]); ?>
  </div>

</div>

</td></tr></table>
</form>

<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre']."2.js'"; ?>></SCRIPT>

<?php
if (isset($_POST["create"])) {
    include_once("librairie_php/db_triade.php");
    validerequete("menuadmin");
    $cr = create_matiere_2($_POST["saisie_creat_matiere"], $_POST["saisie_creat_matiere_long"], $_POST["saisie_code_matiere"], $_POST["saisie_creat_matiere_en"]);
    if ($cr) {
        print "<script>document.addEventListener('DOMContentLoaded',function(){ alertify.success('".addslashes(LANGGRP8)."'); });</script>";
        $matiere = $_POST["saisie_creat_matiere"];
        history_cmd($_SESSION["nom"], "CREATION", "matiere $matiere");
    }
}
Pgclose();
?>
</body></html>
