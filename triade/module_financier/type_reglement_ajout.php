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
	$libelle   = lire_parametre('libelle',   '', 'POST');

	if($operation == "enregistrer") {
		$sql  = "INSERT INTO ".FIN_TAB_TYPE_REGLEMENT." (libelle, modifiable) ";
		$sql .= "VALUES('".esc($libelle)."', 1); ";
		$res  = execSql($sql);
		msg_util_ajout(LANG_FIN_GENE_001);
	}
} else {
	Pgclose();
	header('Location: ' . FIN_SCRIPT_PAS_AUTORISATION);
	exit();
}
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<base href="<?php echo site_url_racine(FIN_REP_MODULE); ?>">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script language="javascript" src="./librairie_js/clickdroit2.js"></script>
<script language="javascript" src="./librairie_js/function.js"></script>
<script language="javascript" src="./librairie_js/lib_css.js"></script>
<script language="javascript" src="./librairie_js/verif_creat.js"></script>
<link title="style" type="text/CSS" rel="stylesheet" href="./<?php echo $g_chemin_relatif_module; ?>librairie_css/css.css">
<?php inclure_scripts_js_toutes_pages(); ?>
<title>Triade - <?php echo LANG_FIN_TREG_010 ?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<?php include("./librairie_php/lib_licence.php"); ?>
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<?php validerequete("2"); ?>
<SCRIPT language="javascript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="top" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="javascript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?>></SCRIPT>

<?php if(autorisation_module()) { ?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'>
  <td height="2">
    <b><font id='menumodule1'><?php echo LANG_FIN_TREG_010 ?></font></b>
  </td>
</tr>
<tr id='cadreCentral0'>
<td valign="top" align="center" style="padding:14px">

  <form name="formulaire" id="formulaire" action="<?php echo url_script(); ?>" method="post" onsubmit="return valider_le_formulaire();">
    <input type="hidden" name="operation" id="operation" value="enregistrer">

    <div class="card" style="max-width:480px;margin:0 auto;text-align:left">

      <div class="form-row" style="padding:14px 16px;gap:10px;align-items:center">
        <label style="min-width:110px;font-size:12px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.3px">
          <?php echo LANG_FIN_TREG_007 ?>
        </label>
        <input type="text" name="libelle" id="libelle" size="30" maxlength="64"
          style="flex:1;border:1px solid #d0d4ee;border-radius:5px;padding:6px 10px;font-size:13px;font-family:Electrolize,Arial,sans-serif;background:#fafbff;color:#333">
      </div>

      <div style="padding:10px 16px">
        <?php msg_util_afficher(); msg_util_attente_init(); ?>
      </div>

      <div class="form-row" style="padding:12px 16px;gap:8px;justify-content:center;flex-wrap:wrap;border-top:1px solid #eef0f8">
        <script language="javascript">buttonMagicSubmit("<?php print LANG_FIN_TREG_009 ?>","create");</script>
        <script language="javascript">buttonMagic3("<?php print LANG_FIN_TREG_004 ?>","onclick_liste()");</script>
        <script language="javascript">buttonMagic3("<?php print LANG_FIN_TREG_005 ?>","onclick_supp()");</script>
        <script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_003 ?>","onclick_annuler()");</script>
      </div>

    </div>
  </form>

  <script language="javascript">
  function valider_le_formulaire() {
    var valide = true;
    var message_erreur = '';
    var separateur = '';

    var obj = document.getElementById('libelle');
    obj.value = trim(obj.value);
    if(obj.value == '') {
      message_erreur += separateur + "     - <?php echo sprintf(LANG_FIN_VALI_004, LANG_FIN_TREG_007); ?>";
      separateur = "\n";
      if(valide) obj.focus();
      valide = false;
    }

    if(valide) {
      msg_util_attente_montrer(true);
    } else {
      alert("<?php echo LANG_FIN_VALI_001; ?> : \n" + message_erreur);
    }
    return(valide);
  }

  function onclick_liste()   { msg_util_attente_montrer(true); document.getElementById('formulaire_liste').submit(); }
  function onclick_supp()    { msg_util_attente_montrer(true); document.getElementById('formulaire_supp').submit(); }
  function onclick_annuler() { msg_util_attente_montrer(true); document.getElementById('formulaire_parametrage').submit(); }
  </script>

  <form name="formulaire_liste"       id="formulaire_liste"       action="<?php echo $g_chemin_relatif_module ?>type_reglement_liste.php" method="post"></form>
  <form name="formulaire_supp"        id="formulaire_supp"        action="<?php echo $g_chemin_relatif_module ?>type_reglement_supp.php"  method="post"></form>
  <form name="formulaire_parametrage" id="formulaire_parametrage" action="<?php echo $g_chemin_relatif_module ?>parametrage.php"          method="post"></form>

</td></tr></table>

<?php } ?>

<SCRIPT language="javascript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?>></SCRIPT>
<script language="javascript">InitBulle("#000000","#FCE4BA","red",1);</script>
<script language="javascript">
function initialisation_page() {
  var liens_a_remplacer = [{"lien_avec":'<?php echo site_url_racine(FIN_REP_MODULE); ?>#',"remplacer_par":'javascript:;'}];
  initialisation_page_global(liens_a_remplacer);
}
if (window.addEventListener) { window.addEventListener("load", initialisation_page, false); }
else if (window.attachEvent)  { window.attachEvent("onload", initialisation_page); }
</script>

</body>
</HTML>
<?php Pgclose(); ?>
