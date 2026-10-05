<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -
 *   Site                 : http://www.triade-educ.com
 *
 ***************************************************************************/
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<!-- ── Iframe demandes ── -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="185">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>
    <i class="bi bi-briefcase"></i> Gestion des demandes de stage via la centrale des stages
</font></b></td></tr>
<tr id='cadreCentral0'>
<td valign="top">
<iframe width="468" height="100%" src="gestionsouhaitcentral.php" name="centralstage"
  marginwidth="0" marginheight="0" hspace="0" vspace="0" frameborder="0" scrolling="no"></iframe>
</td></tr></table>

<br>

<!-- ── Menu gestion centrale ── -->
<div class="dest-wrap" style="margin-bottom:8px">
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;font-family:Electrolize,'Trebuchet MS',Arial;display:flex;align-items:center;gap:7px">
    <i class="bi bi-diagram-3-fill"></i> Gestion de la centrale des stages
</div>
<div class="dest-list" style="border-radius:0 0 8px 8px">

<?php if (!file_exists("./common/config.centralStageClient.php")) { ?>
  <form action="gestion_central_stage_actif.php" method="post" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label">
        <i class="bi bi-toggle-on" style="color:#080A66;margin-right:5px"></i>
        Activer / Désactiver votre centrale de stage
      </span>
      <button type="submit" class="btn-dest">Accès</button>
    </div>
  </form>
<?php } ?>

<?php if (file_exists("./common/config.centralStage.php")) { ?>
  <form action="gestion_central_stage_demande.php" method="post" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label">
        <i class="bi bi-inbox-fill" style="color:#080A66;margin-right:5px"></i>
        Gestion des demandes d'affiliation
      </span>
      <button type="submit" class="btn-dest"><?php print LANGBT28 ?></button>
    </div>
  </form>
  <form action="gestion_central_stage_planification.php" method="post" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label">
        <i class="bi bi-calendar3" style="color:#080A66;margin-right:5px"></i>
        Gestion des planifications des périodes de stage
      </span>
      <button type="submit" class="btn-dest">Accès</button>
    </div>
  </form>
<?php } ?>

<?php if (!file_exists("./common/config.centralStage.php")) { ?>
  <form action="gestion_central_stage_consulter.php" method="post" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label">
        <i class="bi bi-link-45deg" style="color:#080A66;margin-right:5px"></i>
        S'affilier à une centrale de stage
      </span>
      <button type="submit" class="btn-dest"><?php print LANGBT19 ?></button>
    </div>
  </form>
  <form action="gestion_central_stage_client.php" method="post" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label">
        <i class="bi bi-patch-check" style="color:#080A66;margin-right:5px"></i>
        Valider votre accréditation à une centrale de stage
      </span>
      <button type="submit" class="btn-dest"><?php print LANGBT19 ?></button>
    </div>
  </form>
<?php } ?>

<?php if (file_exists("./common/config.centralStageClient.php")) { ?>
  <form action="gestion_central_stage_export.php" method="post" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label">
        <i class="bi bi-send" style="color:#080A66;margin-right:5px"></i>
        Transmettre les entreprises à la centrale
      </span>
      <button type="submit" class="btn-dest"><?php print LANGBT19 ?></button>
    </div>
  </form>
<?php } ?>

</div>
</div>

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
