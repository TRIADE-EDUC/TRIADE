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
<?php include("./librairie_php/lib_licence.php"); ?>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade — Code d'accès</title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Code d'accès</font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:8px;">

<div style="display:flex;flex-direction:column;gap:8px;">

  <div class="alert alert-info" style="display:flex;flex-direction:column;gap:6px;">
    <div style="display:flex;align-items:flex-start;gap:8px;">
      <i class="bi bi-info-circle-fill" style="flex-shrink:0;margin-top:2px;"></i>
      <span><?php print LANGKEY1 ?></span>
    </div>
    <div style="display:flex;align-items:flex-start;gap:8px;">
      <i class="bi bi-info-circle-fill" style="flex-shrink:0;margin-top:2px;"></i>
      <span><?php print LANGKEY2 ?></span>
    </div>
  </div>

  <div class="card">
    <div class="card-header card-header-primary">
      <span><i class="bi bi-key-fill" style="margin-right:6px;"></i>Génération du code</span>
    </div>
    <div class="card-body">
      <form method="post" action="key2.php">
        <?php if (!file_exists("../common/config3.inc.php")): ?>
          <script language="JavaScript">buttonMagicSubmit("Enregistrer votre code","Submit");</script>
        <?php else: ?>
          <script language="JavaScript">buttonMagicSubmit("Renouveler un nouveau code","Submit");</script>
        <?php endif; ?>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header card-header-primary">
      <span><i class="bi bi-shield-lock-fill" style="margin-right:6px;"></i>Pourquoi un code ?</span>
    </div>
    <div class="card-body" style="font-size:12px;color:#444;line-height:1.7;">
      Ce code permet de limiter l'accès à certains modules aux membres de la Direction.
      Certains modules sont irréversibles. Une mauvaise manipulation peut entraîner la perte d'informations.
    </div>
  </div>

</div>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</HTML>
