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
include("./librairie_php/lib_licence.php");
if (empty($_SESSION["adminplus"])) {
    print "<script>location.href='./base_de_donne_key.php'</script>";
    exit;
}
include_once("librairie_php/db_triade.php");
$cnx = cnx();

$data = array();
$cl   = '';
if (isset($_POST["consult"])) {
    $nbIdClasse = $_POST["nbIdClasse"];
    $annee      = $_POST["annee"];
    $sqlsuite   = "WHERE ";
    for ($i = 0; $i < $nbIdClasse; $i++) {
        $idclasse = $_POST["idclasse_$i"];
        if (trim($idclasse) != "") {
            $sqlsuite .= " ( e.classe='$idclasse' AND c.code_class='$idclasse' ";
            if (trim($annee) != "") {
                $sqlsuite .= " AND ( e.annee_scolaire='$annee' OR e.annee_scolaire IS NULL) ) OR";
            } else {
                $sqlsuite .= " ) OR";
            }
            $cl .= " - " . chercheClasse_nom($idclasse);
        }
    }
    if ($sqlsuite == "WHERE ") { $sqlsuite = ""; }
    if ($sqlsuite != "") {
        $sqlsuite = preg_replace('/OR$/', '', $sqlsuite);
        $sql  = "SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM {$prefixe}eleves e,{$prefixe}classes c $sqlsuite AND e.annee_scolaire='$annee' GROUP BY e.nom,e.prenom ORDER BY e.nom";
        $res  = execSql($sql);
        $data = chargeMat($res);
    } else {
        $data = array();
    }
}
$nb = isset($data) ? countTriade($data) : 0;
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'><?php print LANGBASE23 ?></font></b>
</td></tr>
<tr id='cadreCentral0'>
<td>
<!-- // debut -->

<div style="display:flex;flex-direction:column;gap:12px;padding:10px 6px">

  <?php if (isset($_POST["consult"])): ?>
  <!-- Résultats multi-classes -->
  <?php if ($nb <= 0): ?>
  <div class="alert" style="background:#fff3e0;border:1px solid #ffb74d;border-radius:6px;padding:10px 14px;font-size:12px;color:#e65100;font-weight:600">
    <i class="bi bi-info-circle"></i> <?php print LANGPROJ6 ?>
  </div>
  <?php else: ?>
  <div class="card">
    <div class="card-header">
      <span class="card-title">
        <i class="bi bi-arrow-left-right" style="margin-right:5px"></i>
        <?php print LANGMESS383 ?> <span style="color:#3949ab;text-transform:none;font-size:10px"><?php print $cl ?></span>
      </span>
      <span class="badge badge-primary"><?php print $nb ?> <?php print INTITULEELEVES ?></span>
    </div>
    <div class="card-body" style="padding:10px 12px">
      <form method="post" action="chgmentClas11.php">
        <script>
        function validSelect(val) {
          var nc = document.getElementById('new_classe');
          var tous = document.getElementById('tous');
          if (val != "") {
            nc.disabled = false;
            tous.disabled = false;
          } else {
            nc.disabled = true; nc.selectedIndex = 0;
            tous.disabled = true; tous.checked = false;
            for (var i = 0; i < <?php print $nb ?>; i++) {
              var el = document.getElementById('eleve' + i);
              if (el) { el.checked = false; el.disabled = true; }
            }
          }
        }
        function validSelect2(val) {
          var tous = document.getElementById('tous');
          if (val != "rien") {
            tous.disabled = false;
            for (var i = 0; i < <?php print $nb ?>; i++) {
              var el = document.getElementById('eleve' + i);
              if (el) el.disabled = false;
            }
          } else {
            tous.disabled = true; tous.checked = false;
            for (var i = 0; i < <?php print $nb ?>; i++) {
              var el = document.getElementById('eleve' + i);
              if (el) { el.checked = false; el.disabled = true; }
            }
          }
        }
        function selectTous() {
          var checked = document.getElementById('tous').checked;
          for (var i = 0; i < <?php print $nb ?>; i++) {
            var el = document.getElementById('eleve' + i);
            if (el) el.checked = checked;
          }
        }
        </script>

        <div style="margin-bottom:10px;display:flex;align-items:center;gap:8px;flex-wrap:wrap">
          <label style="font-size:12px;font-weight:600;color:#080A66;white-space:nowrap">
            <?php print "Nouvelle année scolaire" ?> :
          </label>
          <select name="anneefutur" class="form-control" style="width:auto"
                  onchange="validSelect(this.value)">
            <?php filtreAnneeScolaireSelectNote('', 2) ?>
          </select>
        </div>

        <div style="margin-bottom:8px;display:flex;align-items:center;gap:8px;flex-wrap:wrap">
          <label style="font-size:12px;font-weight:600;color:#080A66"><?php print LANGMESS381 ?></label>
          <select id="new_classe" name="new_classe" disabled="disabled"
                  class="form-control" style="width:auto"
                  onchange="validSelect2(this.value)">
            <option value="rien"      style="color:#000066;background:#FCE4BA"><?php print LANGCHOIX ?></option>
            <option value="quit"      style="color:red;background:#fff"><?php print LANGBASE37 ?></option>
            <option value="sansclasse" style="color:#000066;background:#fffde7"><?php print LANGMESS385 ?></option>
            <?php select_classe2('20'); ?>
          </select>
          <label style="font-size:12px;color:#333">
            <?php print LANGTOUS ?> :
            <input type="checkbox" id="tous" onclick="selectTous()" disabled="disabled"
                   style="accent-color:#080A66;cursor:pointer">
          </label>
        </div>

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:4px;margin-bottom:12px">
          <?php for ($i = 0; $i < $nb; $i++): ?>
          <?php if ($data[$i][1] != ""): ?>
          <div id="tr<?php print $i ?>"
               style="background:#f5f7ff;border:1px solid #e4e9f8;border-radius:4px;padding:4px 8px;font-size:11px;display:flex;align-items:center;gap:6px">
            <input type="checkbox" name="idEleve_<?php print $i ?>" disabled="disabled"
                   id="eleve<?php print $i ?>" value="<?php print $data[$i][1] ?>"
                   style="accent-color:#080A66;cursor:pointer">
            <label for="eleve<?php print $i ?>" style="cursor:pointer;line-height:1.3">
              <?php print strtoupper(infoBulleEleveSansLoupe($data[$i][1], $data[$i][2])) ?>
              <?php print trunchaine(trim($data[$i][3]), 15) ?>
            </label>
          </div>
          <?php endif; ?>
          <?php endfor; ?>
        </div>

        <input type="hidden" name="nbEleve" value="<?php print $nb ?>">
        <script language=JavaScript>buttonMagicSubmit("<?php print LANGBASE38 ?>","rien")</script>
      </form>
    </div>
  </div>
  <?php endif; ?>
  <?php endif; ?>

  <!-- Nouvelle sélection multi-classes -->
  <div class="card">
    <div class="card-header">
      <span class="card-title"><i class="bi bi-people-fill" style="margin-right:5px"></i><?php print LANGMESS381 ?></span>
    </div>
    <div class="card-body">
      <form method="post" action="chgmentClas00.php">
        <div class="form-row">
          <label class="form-label"><?php print "Année scolaire en cours" ?> :</label>
          <select name="annee" class="form-control" style="width:auto">
            <?php filtreAnneeScolaireSelectNote('', 4) ?>
            <option value=""><?php print LANGMESS379 ?></option>
          </select>
        </div>
        <div style="margin:10px 0;font-size:12px;font-weight:600;color:#080A66"><?php print LANGMESS381 ?></div>
        <div style="margin-bottom:10px">
          <?php checkbox_classe2() ?>
        </div>
        <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","consult")</script>
      </form>
    </div>
  </div>

  <div>
    <script language=JavaScript>buttonMagicRetour("chgmentClas0.php","_self")</script>
  </div>

</div>

<!-- // fin -->
<?php Pgclose(); ?>
</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<script language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</script>
</BODY></HTML>
