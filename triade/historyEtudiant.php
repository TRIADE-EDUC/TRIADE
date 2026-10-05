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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
$cnx = cnx();
validerequete("menuadmin");

$saved         = false;
$saisie_classe = isset($_POST["saisie_classe"]) ? $_POST["saisie_classe"] : '';
$anneeScolaire = isset($_POST["anneeScolaire"]) ? $_POST["anneeScolaire"] : '';
$data          = array();

if (isset($_POST["create2"])) {
    $idclasse = $_POST["saisie_classe"];
    $nb       = (int)$_POST["nb"];
    for ($i = 0; $i < $nb; $i++) {
        if (isset($_POST["listing_$i"])) {
            $idEleve = $_POST["listing_$i"];
            enrEtudiantHistory($idEleve, $anneeScolaire, $idclasse);
        }
    }
    $saved = true;
}

if ($saisie_classe > "0") {
    $sql  = "SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves,{$prefixe}classes WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
    $res  = execSql($sql);
    $data = chargeMat($res);
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'><?php print "Assignation pour les classes antérieures des ".INTITULEELEVES ?></font></b>
</td></tr>
<tr id='cadreCentral0'>
<td>
<!-- // debut -->

<div style="display:flex;flex-direction:column;gap:12px;padding:10px 6px">

  <?php if ($saved): ?>
  <div style="background:#e8f5e9;border:1px solid #a5d6a7;border-radius:6px;padding:10px 14px;display:flex;align-items:center;gap:8px">
    <i class="bi bi-check-circle-fill" style="color:#2e7d32;font-size:15px"></i>
    <span style="font-size:12px;font-weight:600;color:#2e7d32"><?php print LANGDONENR ?></span>
  </div>
  <?php endif; ?>

  <!-- Sélection de la classe actuelle -->
  <div class="card">
    <div class="card-header">
      <span class="card-title"><i class="bi bi-mortarboard" style="margin-right:5px"></i><?php print LANGPROFG ?></span>
    </div>
    <div class="card-body">
      <form method="post" action="historyEtudiant.php" name="formulaire">
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
          <label style="font-size:12px;font-weight:600;color:#080A66"><?php print LANGPROFG ?> :</label>
          <select name="saisie_classe" class="form-control" style="width:auto"
                  onchange="this.form.submit()">
            <?php
            if ($saisie_classe > "0") {
                print "<option value='".$saisie_classe."'>".chercheClasse_nom($saisie_classe)."</option>";
            }
            print "<option>".LANGCHOIX."</option>";
            select_classe();
            ?>
          </select>
        </div>
      </form>
    </div>
  </div>

  <?php if ($saisie_classe > "0"): ?>
  <!-- Assignation historique -->
  <div class="card">
    <div class="card-header">
      <span class="card-title"><i class="bi bi-clock-history" style="margin-right:5px"></i>Classe et année antérieures</span>
      <span class="badge badge-primary"><?php print countTriade($data) ?> <?php print INTITULEELEVES ?></span>
    </div>
    <div class="card-body" style="padding:10px 12px">
      <form method="post" action="historyEtudiant.php">
        <input type="hidden" name="saisie_classe" value="<?php print htmlspecialchars($saisie_classe) ?>">

        <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:14px">
          <div class="form-row">
            <label class="form-label">Classe antérieure :</label>
            <select name="saisie_classe" class="form-control" style="width:auto">
              <option><?php print LANGCHOIX ?></option>
              <?php select_classe(); ?>
            </select>
          </div>
          <div class="form-row" style="border-bottom:none">
            <label class="form-label">Année scolaire antérieure :</label>
            <select name="anneeScolaire" class="form-control" style="width:auto">
              <?php filtreAnneeScolaireSelectAnterieur($anneeScolaire, 7); ?>
            </select>
          </div>
        </div>

        <?php if (countTriade($data) > 0): ?>
        <div style="overflow-x:auto">
          <table class="table" style="font-size:11px">
            <thead>
              <tr>
                <th class="cc-th">Nom</th>
                <th class="cc-th">Prénom</th>
                <th class="cc-th" style="text-align:center;width:60px">
                  Valider
                  <input type="checkbox" id="checkAll" style="accent-color:#080A66;cursor:pointer;margin-left:4px">
                </th>
              </tr>
            </thead>
            <tbody>
              <?php for ($i = 0; $i < countTriade($data); $i++): ?>
              <tr class="cc-tr-data">
                <td><?php print htmlspecialchars($data[$i][2]) ?></td>
                <td><?php print htmlspecialchars($data[$i][3]) ?></td>
                <td style="text-align:center">
                  <input type="checkbox" value="<?php print $data[$i][1] ?>"
                         name="listing_<?php print $i ?>" class="child"
                         style="accent-color:#080A66;cursor:pointer">
                </td>
              </tr>
              <?php endfor; ?>
            </tbody>
          </table>
        </div>
        <input type="hidden" name="nb" value="<?php print countTriade($data) ?>">
        <div style="margin-top:10px">
          <script language=JavaScript>buttonMagicSubmit('Valider la sélection','create2')</script>
        </div>
        <?php else: ?>
        <p style="font-size:12px;color:#888;margin:0"><?php print LANGPROJ6 ?></p>
        <?php endif; ?>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <div>
    <script language=JavaScript>buttonMagicRetour("acces2.php","_self")</script>
  </div>

</div>

<!-- // fin -->
<?php Pgclose(); ?>
</td></tr></table>

<script>
var checkAll = document.getElementById("checkAll");
if (checkAll) {
  checkAll.addEventListener("change", function() {
    document.querySelectorAll(".child").forEach(function(c) { c.checked = this.checked; }, this);
  });
}
</script>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")):
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
else:
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
endif;
?>
<script language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</script>
</BODY></HTML>
