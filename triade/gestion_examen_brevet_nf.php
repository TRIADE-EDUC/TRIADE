<?php
session_start();
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta charset="utf-8">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="librairie_css/css-v4.css">
<link rel="stylesheet" href="librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert=function(msg){alertify.error(msg);};</script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Brevet série collège</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="top" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr id='coulBar0' bgcolor="#0B3A0C"><td height="28" style="padding:4px 10px">
  <b><font id='menumodule1'><i class="bi bi-mortarboard-fill" style="margin-right:6px"></i>Fiche Brevet série collège</font></b>
</td></tr>
<tr id='cadreCentral0'><td style="padding:16px">

<div class="card" style="max-width:540px;margin:0 auto">
<div class="card-body" style="padding:0">

  <form method="post" action="gestion_examen_brevet_config_classe2.php" target="_blank" onsubmit="return valide_consul_classe()" name="formulaire">
  <div style="padding:12px 16px;border-bottom:1px solid #e8eaf6;display:flex;align-items:center;gap:10px;flex-wrap:wrap">
    <span style="font-size:12px;font-weight:600;color:#333;min-width:170px">
      <i class="bi bi-gear" style="color:#080A66;margin-right:5px"></i>Config. matières en
    </span>
    <select id="saisie_classe" name="saisie_classe" style="font-size:12px;padding:4px 8px;border:1px solid #c5cae9;border-radius:4px;flex:1;min-width:130px">
      <option id='select0'><?php print LANGCHOIX?></option>
      <?php select_classe(); ?>
    </select>
    <script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1 ?>","create");</script>
  </div>
  </form>

  <div style="padding:12px 16px;border-bottom:1px solid #e8eaf6;display:flex;align-items:center;gap:10px;flex-wrap:wrap">
    <span style="font-size:12px;font-weight:600;color:#333;min-width:170px">
      <i class="bi bi-printer" style="color:#080A66;margin-right:5px"></i>Imprimer Brevet
    </span>
    <script language=JavaScript>buttonMagic("<?php print LANGBREVET1 ?>","gestion_examen_impr_brevet_college_2011.php",'_parent','','');</script>
  </div>

  <div style="padding:12px 16px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">
    <span style="font-size:12px;font-weight:600;color:#333;min-width:170px">
      <i class="bi bi-chat-text" style="color:#080A66;margin-right:5px"></i>Saisie commentaire Brevet
    </span>
    <script language=JavaScript>buttonMagic("<?php print LANGBREVET1 ?>","brevet_commentaire_admin.php",'_parent','','');</script>
  </div>

</div>
</div>

</td></tr>
</table>
</div>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php Pgclose(); ?>
</BODY>
</HTML>
