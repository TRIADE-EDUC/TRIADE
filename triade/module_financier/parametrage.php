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

include("librairie_php/lib_init_module.inc.php");

if(autorisation_module()) {
	$operation = lire_parametre('operation', '', 'POST');
	if($operation == "enregistrer") {
	}
} else {
	Pgclose();
	header('Location: ' . FIN_SCRIPT_PAS_AUTORISATION);
	exit();
}
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<base href="<?php echo site_url_racine(FIN_REP_MODULE); ?>">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script language="javascript" src="./librairie_js/clickdroit2.js"></script>
<script language="javascript" src="./librairie_js/function.js"></script>
<script language="javascript" src="./librairie_js/lib_css.js"></script>
<script language="javascript" src="./librairie_js/verif_creat.js"></script>
<link title="style" type="text/CSS" rel="stylesheet" href="./<?php echo $g_chemin_relatif_module; ?>librairie_css/css.css">
<?php inclure_scripts_js_toutes_pages(); ?>
<title>Triade - <?php echo LANG_FIN_PARA_001 ?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<?php include("./librairie_php/lib_licence.php"); ?>
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<?php validerequete("2"); ?>
<SCRIPT language="javascript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="top" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="javascript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<?php if(autorisation_module()) { ?>

<div class="dest-wrap">

  <div class="toolbar">
    <i class="bi bi-sliders" style="color:#080A66;font-size:15px"></i>
    <span class="card-title"><?php echo LANG_FIN_PARA_001 ?></span>
  </div>

  <div class="dest-list">

    <div class="dest-row">
      <div style="display:flex;align-items:center;gap:10px">
        <i class="bi bi-collection" style="color:#080A66;font-size:17px"></i>
        <span class="dest-row-label"><?php echo LANG_FIN_GROUPE_001 ?></span>
      </div>
      <script language="javascript">buttonMagic3("<?php echo LANG_FIN_GENE_009 ?>","onclick_groupe_frais()");</script>
    </div>

    <div class="dest-row">
      <div style="display:flex;align-items:center;gap:10px">
        <i class="bi bi-tag" style="color:#080A66;font-size:17px"></i>
        <span class="dest-row-label"><?php echo LANG_FIN_TFRA_001 ?></span>
      </div>
      <script language="javascript">buttonMagic3("<?php echo LANG_FIN_GENE_009 ?>","onclick_type_frais()");</script>
    </div>

    <div class="dest-row">
      <div style="display:flex;align-items:center;gap:10px">
        <i class="bi bi-credit-card" style="color:#080A66;font-size:17px"></i>
        <span class="dest-row-label"><?php echo LANG_FIN_TREG_001 ?></span>
      </div>
      <script language="javascript">buttonMagic3("<?php echo LANG_FIN_GENE_009 ?>","onclick_type_reglement()");</script>
    </div>

    <div class="dest-row">
      <div style="display:flex;align-items:center;gap:10px">
        <i class="bi bi-table" style="color:#080A66;font-size:17px"></i>
        <span class="dest-row-label"><?php echo LANG_FIN_BARE_001 ?></span>
      </div>
      <script language="javascript">buttonMagic3("<?php echo LANG_FIN_GENE_009 ?>","onclick_bareme()");</script>
    </div>

    <div class="dest-row">
      <div style="display:flex;align-items:center;gap:10px">
        <i class="bi bi-bank" style="color:#080A66;font-size:17px"></i>
        <span class="dest-row-label"><?php echo LANG_FIN_PARP_001 ?></span>
      </div>
      <script language="javascript">buttonMagic3("<?php echo LANG_FIN_GENE_009 ?>","onclick_config()");</script>
    </div>

  </div>

  <div style="margin-top:8px"><?php msg_util_afficher(); msg_util_attente_init(); ?></div>

</div>

<script language="javascript">
function onclick_type_frais()     { msg_util_attente_montrer(true); document.getElementById('formulaire_type_frais').submit(); }
function onclick_groupe_frais()   { msg_util_attente_montrer(true); document.getElementById('formulaire_groupe_frais').submit(); }
function onclick_type_reglement() { msg_util_attente_montrer(true); document.getElementById('formulaire_type_reglement').submit(); }
function onclick_bareme()         { msg_util_attente_montrer(true); document.getElementById('formulaire_bareme').submit(); }
function onclick_config()         { msg_util_attente_montrer(true); document.getElementById('formulaire_config').submit(); }
</script>

<form name="formulaire_type_frais"     id="formulaire_type_frais"     action="<?php echo $g_chemin_relatif_module ?>type_frais_ajout.php"     method="post"></form>
<form name="formulaire_groupe_frais"   id="formulaire_groupe_frais"   action="<?php echo $g_chemin_relatif_module ?>groupe_frais_ajout.php"    method="post"></form>
<form name="formulaire_type_reglement" id="formulaire_type_reglement" action="<?php echo $g_chemin_relatif_module ?>type_reglement_ajout.php"  method="post"></form>
<form name="formulaire_bareme"         id="formulaire_bareme"         action="<?php echo $g_chemin_relatif_module ?>bareme_liste.php"           method="post"></form>
<form name="formulaire_config"         id="formulaire_config"         action="<?php echo $g_chemin_relatif_module ?>config_prelevement.php"     method="post"></form>

<?php } ?>

<SCRIPT language="javascript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>

<script language="javascript">
function initialisation_page() {
  var liens_a_remplacer = [{ "lien_avec": '<?php echo site_url_racine(FIN_REP_MODULE); ?>#', "remplacer_par": 'javascript:;' }];
  initialisation_page_global(liens_a_remplacer);
}
if (window.addEventListener) { window.addEventListener("load", initialisation_page, false); }
else if (window.attachEvent)  { window.attachEvent("onload", initialisation_page); }
</script>

</body>
</HTML>
