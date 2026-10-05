<?php
session_start();
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if ( ($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"droitStageProRead") == 0) ) {
	PgClose();
	header("Location: accespersonneldenied.php?titre=Module Stage Pro.");
}
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
<script language="JavaScript" src="./librairie_js/jquery-min.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGSTAGE22 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign="top">
<?php
$cnx=cnx();
include_once("./librairie_php/ajaxrecherche.php");
?>

<!-- ── Recherche par secteur / critères ── -->
<form method="post" action="gestion_stage_ent_visu_rech.php">
<div class="na-card">
  <div style="font-size:12px;font-weight:700;color:#080A66;margin-bottom:8px;"><?php print LANGSTAGE21 ?></div>
  <div class="na-row">
    <span class="na-lbl">Secteur d'activité :</span>
    <select name="activite" class="cc-select">
      <option value="<?php print LANGINCONNU ?>" id="select0"><?php print LANGINCONNU ?></option>
      <option value="-1" id="select0">Tous secteurs</option>
      <?php
      $data=activite_liste();
      for ($i=0; $i<countTriade($data); $i++) {
          print "<option value='".$data[$i][0]."' id='select1' title=\"".$data[$i][0]."\">".trunchaine($data[$i][0],30)."</option>";
      }
      ?>
    </select>
  </div>
  <div class="na-row">
    <span class="na-lbl">Département :</span>
    <input type="text" name="departement" size="15" class="cc-select" style="width:120px;">
    <span style="font-size:11px;color:#888;font-style:italic;">(ex : 75* ou 75001)</span>
  </div>
  <div class="na-row">
    <span class="na-lbl">Ville :</span>
    <input type="text" name="ville" size="35" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl">Secteur économique :</span>
    <input type="text" name="secteureconomique" size="35" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl">SIREN :</span>
    <input type="text" name="siren" size="35" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl">Forme juridique :</span>
    <input type="text" name="formejuridique" size="35" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl">Type organisation :</span>
    <input type="text" name="typeorganisation" size="35" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl">NAF (APE) :</span>
    <input type="text" name="NAFAPE" size="35" class="cc-select">
  </div>
  <div class="na-row">
    <span class="na-lbl">NACE :</span>
    <input type="text" name="NACE" size="35" class="cc-select">
  </div>
</div>
<div class="na-foot">
  <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","rech1");</script>
  <?php
  if ($_SESSION["membre"] == "menuprof") {
      print "<script language=JavaScript>buttonMagicRetour('gestion_stage_profp.php','_parent')</script>";
  } elseif (($_SESSION["membre"] == "menueleve") || ($_SESSION["membre"] == "menuparent")) {
      print "<script language=JavaScript>buttonMagicRetour('gestion_stage_el.php','_parent')</script>";
  } else {
      print "<script language=JavaScript>buttonMagicRetour('gestion_stage.php','_parent')</script>";
  }
  ?>
</div>
</form>

<div style="border-top:2px solid #c5cae9;margin:12px 0;"></div>

<!-- ── Recherche par nom ── -->
<form method="post" action="gestion_stage_ent_visu_rech_nom.php" name="formulaire_2">
<div class="na-card">
  <div style="font-size:12px;font-weight:700;color:#080A66;margin-bottom:8px;"><?php print LANGSTAGE20 ?></div>
  <div class="na-row">
    <span class="na-lbl">Nom :</span>
    <input type="text" autocomplete="off" name="recherche" size="30" class="cc-select"
      style="width:220px;" id="search">
  </div>
  <div id="userList" style="width:220px;margin-left:130px;background:#eee;font-size:12px;"></div>
</div>
<div class="na-foot">
  <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","rech2");</script>
  <?php
  if ($_SESSION["membre"] == "menuprof") {
      print "<script language=JavaScript>buttonMagicRetour('gestion_stage_profp.php','_parent')</script>";
  } elseif (($_SESSION["membre"] == "menueleve") || ($_SESSION["membre"] == "menuparent")) {
      print "<script language=JavaScript>buttonMagicRetour('gestion_stage_el.php','_parent')</script>";
  } else {
      print "<script language=JavaScript>buttonMagicRetour('gestion_stage.php','_parent')</script>";
  }
  ?>
</div>
</form>

<div style="border-top:2px solid #c5cae9;margin:12px 0;"></div>

<!-- ── Impression ── -->
<form method="post" action="gestion_stage_impr.php">
<div class="na-foot">
  <script language=JavaScript>buttonMagicSubmit('Imprimer la liste des entreprises','imp');</script>
</div>
</form>

</td></tr></table>
<?php
if ($_SESSION["membre"] == "menuadmin") :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
?>
<?php js_search_entreprise(); ?>
</BODY></HTML>
