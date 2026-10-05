<?php
session_start();
error_reporting(0);
if (isset($_POST["saisieecole"])) {
    if ($saisieecole != '0') {
        print "<script language=JavaScript>open('../forum/admin.php?repforum=".$_POST["repforum"]."','forum','width=800,height=600,scrollbars=yes,resizable=yes');</script>";
    }
}
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<?php include("./librairie_php/lib_licence.php"); ?>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade — Gestion des forums</title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Gestion des forums</font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:8px;">

<div class="card">
  <div class="card-header card-header-primary">
    <span><i class="bi bi-chat-dots-fill" style="margin-right:6px;"></i>Forum de l'établissement</span>
  </div>
  <div class="card-body">
    <form method="POST">
      <div class="form-row">
        <label class="form-lbl">Forum :</label>
        <select name="repforum" class="cc-select">
          <option value="menuadmin">Forum Direction</option>
          <option value="menuscolaire">Forum Vie Scolaire</option>
          <option value="menuprof">Forum Enseignant</option>
          <option value="menueleve">Forum Élève</option>
          <option value="menuparent">Forum Parent d'élève</option>
        </select>
      </div>
      <input type="hidden" name="saisieecole" value="<?php print REPECOLE ?>">
      <div style="margin-top:12px;">
        <script language="JavaScript">buttonMagicSubmit("Consulter","rien");</script>
      </div>
    </form>
  </div>
</div>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</HTML>
