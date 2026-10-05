<?php
session_start();
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();

$flashOk  = '';
$flashErr = '';

if (isset($_POST["supp"])) {
    $cr = @verif_utiliser_matiere($_POST["saisie_matiere_supp"]);
    if (!$cr) {
        $cr = suppression_matiere($_POST["saisie_matiere_supp"]);
        if ($cr) {
            $matierenom = chercheMatiereNom($_POST["saisie_matiere_supp"]);
            history_cmd($_SESSION["nom"], "SUPPRESSION", "matière $matierenom");
            $flashOk = addslashes(LANGSUPP26);
        } else {
            $flashErr = 'Erreur lors de la suppression.';
        }
    } else {
        $flashErr = addslashes("Impossible de supprimer cette matière. Matière affectée à une classe.");
    }
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<?php if ($flashOk): ?>
<script>document.addEventListener('DOMContentLoaded',function(){
    alertify.success('<?php echo $flashOk; ?>');
    setTimeout(function(){ location.href='suppression_matiere.php'; }, 1800);
});</script>
<?php elseif ($flashErr): ?>
<script>document.addEventListener('DOMContentLoaded',function(){
    alertify.error('<?php echo $flashErr; ?>');
});</script>
<?php endif; ?>

<form method="post" onsubmit="return valide_supp_choix('saisie_matiere_supp','une matière')" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>
    <i class="bi bi-trash3-fill"></i> <?php print LANGSUPP23; ?>
</font></b></td></tr>
<tr id='cadreCentral0'><td valign="top">

<div class="card" style="margin:10px 4px">
<div class="card-body">
    <div class="card-title" style="font-size:12px;font-weight:bold;color:#080A66;margin-bottom:8px">
        <i class="bi bi-info-circle" style="color:#546e7a"></i>
        <?php print LANGSUPP1; ?>
    </div>
    <p style="font-size:12px;color:#555;margin:0 0 12px;font-family:Electrolize,'Trebuchet MS',Arial">
        Liste des matières pouvant être supprimées.<br>
        <i style="color:#888">Matière n'étant pas affectée à une classe de cette année ou d'une année précédente.</i>
    </p>
    <div class="form-row" style="align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:14px">
        <label style="font-size:12px;font-weight:600;color:#333;font-family:Electrolize,'Trebuchet MS',Arial;white-space:nowrap">
            <i class="bi bi-journal-bookmark-fill" style="color:#080A66"></i>
            <?php print LANGPER17; ?> :
        </label>
        <select name="saisie_matiere_supp" class="cc-select" style="flex:1;min-width:180px;max-width:320px">
            <option style="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX; ?></option>
            <?php select_MatiereNonAffecter(20); Pgclose(); ?>
        </select>
    </div>
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
        <input type="submit" name="supp" value="1" id="hidden-supp" style="display:none">
        <button type="button"
            style="background:#c62828;color:#fff;border:none;border-radius:6px;padding:8px 20px;font-size:12px;font-weight:700;cursor:pointer;font-family:Electrolize,'Trebuchet MS',Arial;display:inline-flex;align-items:center;gap:6px"
            onclick="if(valide_supp_choix('saisie_matiere_supp','une matière')){document.getElementById('hidden-supp').click();}">
            <i class="bi bi-trash3-fill"></i> <?php print LANGSUPP24; ?>
        </button>
        <script language=JavaScript>buttonMagicRetour("creat_matiere.php","_parent");</script>
    </div>
</div>
</div>

</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
