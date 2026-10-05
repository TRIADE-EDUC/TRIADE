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
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/rechercheV4.js"></script>
<script language="JavaScript" src="./librairie_js/lib_discipline.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<?php
include_once('librairie_php/db_triade.php');
validerequete("2");
include_once("./librairie_php/ajax.php");
ajax_js();
?>

<div class="dest-wrap">

<!-- ── Section 1 : Ajouter une discipline ── -->
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;"><?php print LANGDISC58 ?></div>
<div style="background:#fff;border:1px solid #dde0f0;border-top:none;border-radius:0 0 8px 8px;padding:12px 14px;margin-bottom:16px;">
  <form name="formulaire" onsubmit="return valide_consul_classe()" method="post" action="gestion_discipline_ajout.php">
    <div class="na-row">
      <span class="na-lbl"><?php print LANGELE4 ?> :</span>
      <select name="saisie_classe" class="cc-select">
        <option value="0" style="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
        <?php select_classe2(20); ?>
      </select>
    </div>
    <div class="na-foot">
      <script language=JavaScript>buttonMagicSubmit("<?php print LANGDISC38 ?>","rien");</script>
    </div>
  </form>
</div>

<!-- ── Section 2 : Gestion des retenues ── -->
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;"><?php print LANGDISC39 ?></div>
<div class="dest-list" style="border-radius:0 0 8px 8px;margin-bottom:16px;">
  <div class="dest-row">
    <span class="dest-row-label"><?php print LANGDISC40 ?></span>
    <div style="display:flex;gap:6px;">
      <button type="button" class="btn-dest" onclick="open('gestion_discipline_retenue_non_fait.php','_parent','')"><?php print LANGBT28 ?></button>
      <button type="button" class="btn-dest" onclick="open('liste_retenu_impr.php','_parent','')">Courrier</button>
    </div>
  </div>
  <div class="dest-row">
    <span class="dest-row-label"><?php print LANGDISC41 ?></span>
    <button type="button" class="btn-dest" onclick="open('gestion_discipline_calendrier.php','_parent','')"><?php print LANGBT28 ?></button>
  </div>
  <div class="dest-row">
    <span class="dest-row-label"><?php print LANGDISC42 ?></span>
    <button type="button" class="btn-dest" onclick="open('gestion_discipline_non_aff.php','_parent','')"><?php print LANGBT28 ?></button>
  </div>
  <div class="dest-row">
    <span class="dest-row-label"><?php print LANGDISC44 ?></span>
    <button type="button" class="btn-dest" onclick="open('gestion_discipline_supprimer.php','_parent','')"><?php print LANGBT28 ?></button>
  </div>
  <div class="dest-row">
    <span class="dest-row-label"><?php print CUMUL03 ?></span>
    <button type="button" class="btn-dest" onclick="open('cumul_disci_classe.php','_parent','')"><?php print LANGBT28 ?></button>
  </div>
  <div class="dest-row">
    <span class="dest-row-label"><?php print LANGDISC43 ?></span>
    <button type="button" class="btn-dest" onclick="open('gestion_discipline_config.php','_parent','')"><?php print LANGBT28 ?></button>
  </div>
</div>

<!-- ── Section 3 : Modifier les retenues d'un élève ── -->
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;"><?php print LANGDISC54 ?></div>
<div style="background:#fff;border:1px solid #dde0f0;border-top:none;border-radius:0 0 8px 8px;padding:12px 14px;margin-bottom:16px;">
  <form method="post" onsubmit="return valide_recherche_eleve_2()" action="gestion_discipline_modif.php" name="formulaire_2" id="formulaire_2">
    <div class="na-row">
      <span class="na-lbl"><?php print LANGABS3 ?> :</span>
      <div>
        <input type="text" name="saisie_nom_eleve" size="20" id="search" autocomplete="off"
          onkeyup="searchRequestV4('search','eleve','resultat','formulaire_2','saisie_nom_eleve')"
          class="cc-select" style="width:200px;">
        <div id="resultat" style="width:200px;background:#eee;font-size:12px;"></div>
      </div>
    </div>
    <div class="na-foot">
      <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","rien");</script>
    </div>
  </form>
</div>

<!-- ── Section 4 : Modifier les disciplines d'un élève ── -->
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;">Modifier les disciplines d'un élève</div>
<div style="background:#fff;border:1px solid #dde0f0;border-top:none;border-radius:0 0 8px 8px;padding:12px 14px;margin-bottom:16px;">
  <form method="post" onsubmit="return valide_recherche_eleve_4()" action="gestion_discipline_modif_sanc.php" name="formulaire_4" id="formulaire_4">
    <div class="na-row">
      <span class="na-lbl"><?php print LANGABS3 ?> :</span>
      <div>
        <input type="text" name="saisie_nom_eleve" size="20" id="search2" autocomplete="off"
          onkeyup="searchRequestV4('search2','eleve','resultat1','formulaire_4','saisie_nom_eleve')"
          class="cc-select" style="width:200px;">
        <div id="resultat1" style="width:200px;background:#eee;font-size:12px;"></div>
      </div>
    </div>
    <div class="na-foot">
      <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","rien");</script>
    </div>
  </form>
</div>

<!-- ── Section 5 : Supprimer ── -->
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;"><?php print LANGMESS47 ?></div>
<div style="background:#fff;border:1px solid #dde0f0;border-top:none;border-radius:0 0 8px 8px;padding:12px 14px;margin-bottom:16px;">
  <form method="post" onsubmit="return valide_recherche_eleve_3()" action="gestion_discipline_supp.php" name="formulaire_3" id="formulaire_3">
    <div class="na-row">
      <span class="na-lbl"><?php print LANGABS3 ?> :</span>
      <div>
        <input type="text" name="saisie_nom_eleve" size="20" id="search4" autocomplete="off"
          onkeyup="searchRequestV4('search4','eleve','resultat5','formulaire_3','saisie_nom_eleve')"
          class="cc-select" style="width:200px;">
        <div id="resultat5" style="width:200px;background:#eee;font-size:12px;"></div>
      </div>
    </div>
    <div class="na-foot">
      <script language=JavaScript>buttonMagicSubmit("<?php print LANGDISC56 ?>","rien");</script>
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
<?php include_once("./librairie_php/finbody.php"); ?>
</BODY></HTML>
