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
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type='text/javascript' src="./librairie_php/server.php?client=Util,main,dispatcher,httpclient,request,json,loading,iframe"></script>
<script type='text/javascript' src="./librairie_php/auto_server.php?client=all&stub=livesearch"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<?php
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
include_once("./librairie_php/ajax.php");
ajax_js();
?>

<div class="dest-wrap">

<!-- ── Créer une dispense ── -->
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;"><?php print LANGTITRE24 ?></div>
<div style="background:#fff;border:1px solid #dde0f0;border-top:none;border-radius:0 0 8px 8px;padding:12px 14px;margin-bottom:16px;">
  <form method="post" onsubmit="return valide_recherche_eleve()" action="gestion_dispence_suite.php" name="formulaire">
    <div class="na-row">
      <span class="na-lbl"><?php print LANGABS3 ?> :</span>
      <div>
        <input type="text" name="saisie_nom_eleve" size="20" id="search" autocomplete="off"
          onkeyup="searchRequest(this,'eleve','target0','formulaire','saisie_nom_eleve')"
          class="cc-select" style="width:200px;">
        <div id="target0" style="width:200px;background:#eee;font-size:12px;"></div>
      </div>
    </div>
    <div class="na-foot">
      <script language=JavaScript>buttonMagicSubmit("<?php print LANGDISP20 ?>","rien");</script>
    </div>
  </form>
</div>

<!-- ── Modifier une dispense ── -->
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;"><?php print LANGTITRE25 ?></div>
<div style="background:#fff;border:1px solid #dde0f0;border-top:none;border-radius:0 0 8px 8px;padding:12px 14px;margin-bottom:16px;">
  <form method="post" onsubmit="return valide_recherche_eleve_1()" action="gestion_dispence_modif.php" name="formulaire_1">
    <div class="na-row">
      <span class="na-lbl"><?php print LANGABS3 ?> :</span>
      <div>
        <input type="text" name="saisie_nom_eleve" size="20" id="search1" autocomplete="off"
          onkeyup="searchRequest(this,'eleve','target1','formulaire_1','saisie_nom_eleve')"
          class="cc-select" style="width:200px;">
        <div id="target1" style="width:200px;background:#eee;font-size:12px;"></div>
      </div>
    </div>
    <div class="na-foot">
      <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT33 ?>","rien");</script>
    </div>
  </form>
</div>

<!-- ── Supprimer une dispense ── -->
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;"><?php print LANGTITRE26 ?></div>
<div style="background:#fff;border:1px solid #dde0f0;border-top:none;border-radius:0 0 8px 8px;padding:12px 14px;margin-bottom:16px;">
  <form method="post" onsubmit="return valide_recherche_eleve_2()" action="gestion_dispence_supp.php" name="formulaire_2">
    <div class="na-row">
      <span class="na-lbl"><?php print LANGABS3 ?> :</span>
      <div>
        <input type="text" name="saisie_nom_eleve" size="20" id="search2" autocomplete="off"
          onkeyup="searchRequest(this,'eleve','target2','formulaire_2','saisie_nom_eleve')"
          class="cc-select" style="width:200px;">
        <div id="target2" style="width:200px;background:#eee;font-size:12px;"></div>
      </div>
    </div>
    <div class="na-foot">
      <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT32 ?>","rien");</script>
    </div>
  </form>
</div>

</div><!-- /.dest-wrap -->

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
