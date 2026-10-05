<?php
session_start();
include_once("./librairie_php/lib_licence.php");
include_once("../common/lib_ecole.php");
include_once("../common/lib_admin.php");
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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade — Assistance</title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Assistance / Besoin d'aide</font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:8px;">

<div class="card">
  <div class="card-body">
    <p style="font-size:12px;color:#444;margin:0 0 14px;">
      Accédez aux ressources d'aide, à la documentation officielle et à la communauté Triade.
    </p>
    <div style="display:flex;flex-direction:column;gap:8px;">

      <button type="button" class="btn btn-secondary" style="text-align:left;"
        onclick="open('https://www.triade-educ.org/fr/espace-client.php','_blank','')">
        <i class="bi bi-person-circle" style="margin-right:8px;"></i>Espace client Triade
      </button>

      <button type="button" class="btn btn-secondary" style="text-align:left;"
        onclick="open('https://www.triade-educ.org/fr/documentation.php','_blank','')">
        <i class="bi bi-book-fill" style="margin-right:8px;"></i>Documentation Triade
      </button>

      <button type="button" class="btn btn-secondary" style="text-align:left;"
        onclick="open('https://www.triade-educ.org/fr/discord.php','_blank','')">
        <i class="bi bi-discord" style="margin-right:8px;"></i>Communauté Discord Triade
      </button>

    </div>
  </div>
</div>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</HTML>
