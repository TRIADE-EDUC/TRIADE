<?php
session_start();
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4-2.css">
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<title>Triade — Gestion des mots de passe</title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Gestion des mots de passe</font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:8px;">

<div class="card">
  <div class="card-header card-header-primary">
    <span><i class="bi bi-key-fill" style="margin-right:6px;"></i>Actions disponibles</span>
  </div>
  <div class="card-body" style="display:flex;flex-direction:column;gap:8px;">

    <button type="button" class="btn btn-secondary" style="text-align:left;"
      onclick="open('pass_admin.php','','width=420,height=230')">
      <i class="bi bi-person-lock" style="margin-right:8px;"></i>Modifier votre mot de passe d'accès
    </button>

    <button type="button" class="btn btn-secondary" style="text-align:left;"
      onclick="open('pass_forum.php','','width=420,height=230')">
      <i class="bi bi-chat-dots" style="margin-right:8px;"></i>Modifier le mot de passe du forum
    </button>

    <button type="button" class="btn btn-secondary" style="text-align:left;"
      onclick="open('pass_total.php','','width=520,height=440')">
      <i class="bi bi-people-fill" style="margin-right:8px;"></i>Changer les mots de passe parents / élèves
    </button>

    <button type="button" class="btn btn-secondary" style="text-align:left;"
      onclick="open('pass_total_ens.php','','width=520,height=380')">
      <i class="bi bi-person-workspace" style="margin-right:8px;"></i>Changer les mots de passe des enseignants
    </button>

    <button type="button" class="btn btn-secondary" style="text-align:left;"
      onclick="open('pass_intra-msn.php','_parent','')">
      <i class="bi bi-chat-square-dots" style="margin-right:8px;"></i>Modifier le mot de passe Intra-MSN
    </button>

    <button type="button" class="btn btn-secondary" style="text-align:left;"
      onclick="open('pass_moodle.php','_parent','')">
      <i class="bi bi-mortarboard" style="margin-right:8px;"></i>Modifier le mot de passe Moodle
    </button>

  </div>
</div>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</HTML>
