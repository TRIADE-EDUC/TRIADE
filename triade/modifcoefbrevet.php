<?php
session_start();
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta charset="utf-8">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="librairie_css/css-v4.css">
<link rel="stylesheet" href="librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert=function(msg){alertify.error(msg);};</script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Coef. Brevet</title>
<style>
body { font-family:Electrolize,'Trebuchet MS',Arial,sans-serif; font-size:12px; background:#f5f7ff; margin:0; }
.mcb-header {
    background:linear-gradient(135deg,#080A66 0%,#1a1c8a 100%);
    color:#fff; padding:7px 14px; font-size:13px; font-weight:700;
    display:flex; align-items:center; gap:8px;
}
.mcb-header i { color:#CACCEF; }
.mcb-content { padding:12px 14px; }
</style>
</HEAD>
<body>
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
$idclasse=$_GET["idclasse"];
$libelle=$_GET["libelle"];

if (isset($_POST["update"])) {
    $nb=$_POST["nb"];
    $idclasse=$_POST["idclasse"];
    $libelle=$_POST["libelle"];
    for($i=0;$i<$nb;$i++) {
        $idmatiere=$_POST["idmatiere_$i"];
        $coef=$_POST["coef_$i"];
        miseAjourCoefBrevet($idclasse,$libelle,$idmatiere,$coef);
    }
    echo "<script>document.addEventListener('DOMContentLoaded',function(){alertify.success('Mise à jour effectuée');});</script>";
}
?>

<div class="mcb-header">
  <i class="bi bi-sliders"></i> Coef. &mdash; <?php echo htmlspecialchars($libelle) ?>
</div>

<div class="mcb-content">

<p style="font-size:11px;color:#555;margin:0 0 10px">Modifier les coefficients pour les matières du brevet.</p>

<form method="post">
  <?php print listMatiereBrevet2($libelle,$idclasse) ?>

  <div style="display:flex;gap:8px;margin-top:12px;justify-content:center">
    <script language=JavaScript>buttonMagicSubmit("<?php print LANGABS45?>","update");</script>
    <script language=JavaScript>buttonMagicFermeture()</script>
  </div>

  <input type="hidden" name="idclasse" value="<?php print $idclasse ?>">
  <input type="hidden" name="libelle" value="<?php print $libelle ?>">
</form>

</div>

<?php Pgclose(); ?>
</BODY>
</HTML>
