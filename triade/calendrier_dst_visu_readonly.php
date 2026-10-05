<?php
session_start();
include_once("./common/config2.inc.php");
if ((DSTPROF == "oui") && ($_SESSION["membre"] == "menuprof")) {
    print "<script>location.href='calendrier_config_dst1.php';</script>";
    exit();
}
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
<style>
.cal-wrap{margin:4px 0;font-family:Arial,sans-serif}.cal-title{font-size:12px;font-weight:700;color:#080A66;text-align:center;padding:6px 4px;background:#f0f2fa;border:1px solid #dde0f0;border-bottom:none;border-radius:6px 6px 0 0}.cal-table{border-collapse:collapse;width:100%;background:#fff;border:1px solid #dde0f0;border-radius:0 0 6px 6px}.cal-head th{background:#080A66;color:#fff;font-size:11px;font-weight:600;padding:4px 2px;text-align:center}.cal-day{text-align:center;padding:3px 2px;font-size:11px;color:#333;border:1px solid #f0f2fa}.cal-day a{color:#080A66;text-decoration:none;font-weight:600}.cal-day-today{display:inline-flex;align-items:center;justify-content:center;background:#080A66;color:#fff;border-radius:50%;width:20px;height:20px;font-weight:700}.cal-day-event{background:#e8f5e9}.cal-day-dst{background:#fce4ec}.cal-day-ferie{background:#e8eaf6;color:#5c35be}.cal-year-grid{display:flex;flex-wrap:wrap;gap:14px;justify-content:center;padding:8px}.cal-info{margin-top:6px;padding:10px 14px;background:#fffbe6;border:1px solid #f5c842;border-radius:6px;font-size:12px;color:#5a4000;line-height:1.6}
.dst-legend{display:flex;gap:14px;flex-wrap:wrap;padding:2px 4px 6px 10px;margin-top:-4px;font-size:11px;color:#555}.dst-legend-item{display:flex;align-items:center;gap:5px}.dst-legend-dot{width:12px;height:12px;border-radius:3px;display:inline-block}.dst-legend-dst{background:#fce4ec;border:1px solid #f48fb1}.dst-legend-ferie{background:#e8eaf6;border:1px solid #9fa8da}.dst-legend-today{background:#080A66;border-radius:50%}
.dst-nav{display:flex;justify-content:space-between;align-items:center;padding:10px 4px 4px}.dst-nav a{color:#080A66;font-size:12px;font-weight:600;text-decoration:none;padding:4px 12px;border:1px solid #dde0f0;border-radius:6px;background:#f0f2fa}.dst-nav a:hover{background:#dde0f0}
.dst-prof-actions{display:flex;align-items:center;gap:10px;padding:8px 4px 10px}
</style>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print htmlspecialchars($_SESSION["nom"])." ".htmlspecialchars($_SESSION["prenom"]) ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGPARENT23 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign="top">

<?php if ($_SESSION["membre"] == "menuprof") { ?>
<div class="dst-prof-actions">
  <script language=JavaScript>buttonMagic('<?php print LANGPARENT24 ?>','demdst.php','fenDST','height=250,width=430,scrollbars=no,status=no','');</script>
  <?php
  $data=attenteValidDST($_SESSION["id_pers"]);
  if (countTriade($data)) { ?>
  <script language=JavaScript>buttonMagic('<?php print "Demande en attente..." ?>','attentedst.php','_parent','','');</script>
  <?php } ?>
</div>
<?php } ?>
<br />
<div class="dst-legend">
  <span class="dst-legend-item"><span class="dst-legend-dot dst-legend-dst"></span> DST planifié</span>
  <span class="dst-legend-item"><span class="dst-legend-dot dst-legend-ferie"></span> Férié / jour off</span>
  <span class="dst-legend-item"><span class="dst-legend-dot dst-legend-today"></span> Aujourd'hui</span>
</div>

<?php $saisie_annee_choix=dateY(); ?>
<?php include("./librairie_php/lib_calendrier_dstannee_visu_readonly.php"); ?>
<SCRIPT LANGUAGE="JavaScript"><!--
    annee(<?php print $saisie_annee_choix ?>);
//--></SCRIPT>

<div id="cal-info" class="cal-info" style="display:none;margin:8px 0"></div>

<?php
$saisie_annee_plus = dateY() + 1;
$saisie_annee_moin = dateY() - 1;
?>
<div class="dst-nav">
  <a href="./calendrier_dst_visu11_readonly.php?saisie_annee_choix=<?php print $saisie_annee_moin ?>">&#8592; <?php print LANCALED1 ?></a>
  <a href="./calendrier_dst_visu11_readonly.php?saisie_annee_choix=<?php print $saisie_annee_plus ?>"><?php print LANCALED2 ?> &#8594;</a>
</div>

</td></tr></table>
<?php
if (in_array($_SESSION["membre"], ["menuadmin","menuscolaire"])) :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
?>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
<?php include_once("./librairie_php/finbody.php"); ?>
</BODY></HTML>
