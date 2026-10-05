<?php
session_start();
include("librairie_php/lib_init_module.inc.php");

if(autorisation_module()) {
	$operation = lire_parametre('operation', '', 'GET');

	$sql  = "SELECT groupe_id, libelle FROM ".FIN_TAB_GROUPE_FRAIS." ORDER BY groupe_id";
	$res  = execSql($sql);

	if($res->numRows() == 0) {
		msg_util_ajout(LANG_FIN_GROUPE_008, 'avertissement');
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
<title>Triade - <?php echo LANG_FIN_GROUPE_006 ?></title>
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
    <b><font id='menumodule1'><?php echo LANG_FIN_GROUPE_006 ?></font></b>
  </td>
</tr>
<tr id='cadreCentral0'>
<td valign="top" align="center" style="padding:14px">

  <?php if($res->numRows() > 0) { ?>
  <table class="table" style="max-width:500px;margin:0 auto 14px">
    <thead>
      <tr>
        <th class="cc-th" style="width:50px">#</th>
        <th class="cc-th"><?php echo LANG_FIN_GENE_010 ?></th>
        <th class="cc-th" style="width:80px"></th>
      </tr>
    </thead>
    <tbody>
      <?php for($i = 0; $i < $res->numRows(); $i++) {
        $ligne = &$res->fetchRow(); ?>
      <tr class="cc-tr-data">
        <td><?php echo $ligne[0] ?></td>
        <td><?php echo $ligne[1] ?></td>
        <td style="text-align:center">
          <button type="button" class="btn-sm"
            style="background:#080A66;color:#fff;border:none;border-radius:4px;padding:4px 10px;font-size:11px;cursor:pointer"
            onclick="onclick_modifier('<?php echo $ligne[0] ?>')">
            <?php echo LANG_FIN_GENE_005 ?>
          </button>
        </td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
  <?php } ?>

  <div style="margin-bottom:10px">
    <?php msg_util_afficher(); msg_util_attente_init(); ?>
  </div>

  <script language="javascript">buttonMagic3("<?php print LANG_FIN_GENE_003 ?>","onclick_annuler()");</script>

  <script language="javascript">
  function onclick_annuler() {
    msg_util_attente_montrer(true);
    document.getElementById('formulaire_annuler').submit();
  }
  function onclick_modifier(groupe_id) {
    msg_util_attente_montrer(true);
    document.getElementById('groupe_frais_id').value = groupe_id;
    document.getElementById('formulaire_modif').submit();
  }
  </script>

  <form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module ?>groupe_frais_ajout.php" method="post"></form>
  <form name="formulaire_modif"   id="formulaire_modif"   action="<?php echo $g_chemin_relatif_module ?>groupe_frais_modif.php" method="post">
    <input type="hidden" name="groupe_frais_id" id="groupe_frais_id" value="0">
  </form>

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
