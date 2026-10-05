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
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
if ($_SESSION["membre"] != "menupersonnel") { validerequete("3"); }
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Publipostage des sociétés</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<form method="post" name="formulaire" action="publipostagesociete_2.php">
<div class="na-card">

  <div class="na-row">
    <span class="na-lbl">Classe :</span>
    <select id="idclasse" name="idclasse" class="cc-select">
      <option value="0">Toutes les classes</option>
      <?php select_classe() ?>
    </select>
  </div>

  <div class="na-row">
    <span class="na-lbl">Société :</span>
    <select id="type_societe" name="type_societe" class="cc-select">
      <option value="1">avec étudiant affecté</option>
      <option value="2">sans étudiant affecté</option>
    </select>
  </div>

  <div class="na-row">
    <span class="na-lbl">Type de vignette :</span>
    <select id="id_vignette" name="id_vignette" class="cc-select">
      <option value="1">3 colonnes (70x42,3)</option>
      <option value="2">2 colonnes (105x39)</option>
      <option value="3">2 colonnes (105x39) avec marge</option>
    </select>
  </div>

  <div class="na-row">
    <span class="na-lbl">Ville de la société :</span>
    <input type="text" name="ville_societe" size="20" class="cc-select">
  </div>

  <div class="na-row">
    <span class="na-lbl">Adresse :</span>
    <div style="display:flex;flex-direction:column;gap:6px;font-size:12px;color:#333;">
      <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
        <input type="radio" name="adr" value="siege" checked="checked"> Adresse du siège
      </label>
      <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
        <input type="radio" name="adr" value="lieustage"> Adresse du lieu de stage
      </label>
    </div>
  </div>

</div>
<div class="na-foot">
  <script language=JavaScript>buttonMagicSubmit("<?php print VALIDER ?>","consult1");</script>
  <script language=JavaScript>buttonMagicRetour('gestion_stage.php','_parent');</script>
</div>
</form>

</td></tr></table>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
Pgclose();
?>
</BODY>
</HTML>
