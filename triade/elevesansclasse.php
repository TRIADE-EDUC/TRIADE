<?php
session_start();
$anneeScolaire = isset($_COOKIE["anneeScolaire"]) ? $_COOKIE["anneeScolaire"] : '';
if (isset($_POST["anneeScolaire"])) {
    $anneeScolaire = $_POST["anneeScolaire"];
    setcookie("anneeScolaire", $anneeScolaire, time()+36000*24*30);
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
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx = cnx();

$nbaff   = 30;
$depart  = 0;
$fichier = "elevesansclasse.php";
$table   = "eleves";
$requete = " WHERE classe='-1' ";

if ((isset($_GET["nba"])) && ($deb != 1)) {
    $depart = $_GET["limit"];
}

if (isset($_POST["create"])) {
    $depart       = $_POST["depart"];
    $nbaff        = $_POST["nbaff"];
    $anneeScolaire = $_POST["anneeScolaire"];
    for ($i = 0; $i < $_POST["nbelev"]; $i++) {
        $ideleve    = $_POST["saisie_id_$i"];
        $nomE       = $_POST["saisie_nom_$i"];
        $prenomE    = $_POST["saisie_prenom_$i"];
        $classe     = $_POST["saisie_classe_$i"];
        $lv1        = $_POST["saisie_lv1_$i"];
        $lv2        = $_POST["saisie_lv2_$i"];
        $idclasseold = $_POST["idclasseold_$i"];

        if ($idclasseold != '-1') {
            if ($classe == "supp") { supp_eleve_sansclass($ideleve); continue; }
            if ($classe != "choix") { create_eleve2($nomE, $prenomE, $classe, $lv1, $lv2); }
        } else {
            if ($classe == "supp") { suppression_eleve($ideleve); continue; }
            if ($classe != "choix") {
                if (trim($anneeScolaire) == "") $anneeScolaire = anneeScolaireViaIdClasse($classe);
                changementClasseEleve($ideleve, $classe, $anneeScolaire, '');
            }
        }
    }
}

$data1 = affElevesansclasse();
if ($data1 == "") $data1 = array();
$data2 = affichage_ElevesansclasseTotal_limit($depart, $nbaff);
if ($data2 == "") $data2 = array();
$data  = array_merge($data1, $data2);
$nb    = countTriade(affElevesansclasseAutreTotal());
$total = countTriade($data) + $nb;
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'><?php print LANGBASE20 ?></font></b>
</td></tr>
<tr id='cadreCentral0'>
<td>
<!-- // debut -->

<div style="display:flex;flex-direction:column;gap:10px;padding:8px 6px">

  <!-- Toolbar année + compteur -->
  <div class="toolbar" style="justify-content:space-between;flex-wrap:wrap;gap:8px">
    <form method="post" style="display:flex;align-items:center;gap:8px;margin:0">
      <input type="hidden" name="anneeScolaire" value="">
      <label style="font-size:12px;font-weight:600;color:#080A66;white-space:nowrap">
        <?php print LANGBULL3 ?> :
      </label>
      <select name="anneeScolaire" class="form-control" style="width:auto"
              onchange="this.form.elements['anneeScolaire'].value=this.value;this.form.submit()">
        <?php filtreAnneeScolaireSelectNote($anneeScolaire, 3); ?>
      </select>
    </form>
    <span class="badge badge-primary" style="font-size:11px">
      <?php print LANGBASE21 ?> : <strong><?php print $total ?></strong>
    </span>
  </div>

  <!-- Pagination -->
  <div style="display:flex;justify-content:space-between;align-items:center">
    <div><?php precedent0($fichier, $table, $depart, $nbaff, $requete); ?></div>
    <div><?php suivant0($fichier, $table, $depart, $nbaff, $requete); ?></div>
  </div>

  <!-- Tableau -->
  <form method="post" name="formulaire">
    <input type="hidden" name="anneeScolaire" value="<?php print htmlspecialchars($anneeScolaire) ?>">
    <input type="hidden" name="nbelev" value="<?php print countTriade($data) ?>">
    <input type="hidden" name="depart" value="<?php print $depart ?>">
    <input type="hidden" name="nbaff"  value="<?php print $nbaff ?>">

    <div style="overflow-x:auto">
      <table class="table" style="font-size:11px">
        <thead>
          <tr>
            <th class="cc-th"><?php print LANGTP1 ?></th>
            <th class="cc-th"><?php print LANGTP2 ?></th>
            <th class="cc-th"><?php print LANGIMP26 ?></th>
            <th class="cc-th"><?php print LANGIMP27 ?></th>
            <th class="cc-th"><?php print LANGASS17 ?></th>
          </tr>
        </thead>
        <tbody>
          <?php for ($i = 0; $i < countTriade($data); $i++): ?>
          <tr class="cc-tr-data">
            <td>
              <input type="hidden" name="saisie_id_<?php print $i ?>"    value="<?php print trim($data[$i][0]) ?>">
              <input type="hidden" name="idclasseold_<?php print $i ?>"  value="">
              <input type="text" readonly name="saisie_nom_<?php print $i ?>"
                     value="<?php print htmlspecialchars(trim($data[$i][1])) ?>"
                     style="border:none;background:transparent;font-size:11px;width:100%;cursor:default">
            </td>
            <td>
              <input type="text" readonly name="saisie_prenom_<?php print $i ?>"
                     value="<?php print htmlspecialchars(trim($data[$i][2])) ?>"
                     style="border:none;background:transparent;font-size:11px;width:100%;cursor:default">
            </td>
            <td>
              <input type="text" readonly name="saisie_lv1_<?php print $i ?>"
                     value="<?php print htmlspecialchars(trim($data[$i][3])) ?>"
                     maxlength="29" size="4"
                     style="border:1px solid #e4e9f8;border-radius:3px;font-size:11px;padding:2px 4px;width:50px">
            </td>
            <td>
              <input type="text" readonly name="saisie_lv2_<?php print $i ?>"
                     value="<?php print htmlspecialchars(trim($data[$i][4])) ?>"
                     maxlength="29" size="4"
                     style="border:1px solid #e4e9f8;border-radius:3px;font-size:11px;padding:2px 4px;width:50px">
            </td>
            <td>
              <select name="saisie_classe_<?php print $i ?>"
                      style="border:1px solid #c5cae9;border-radius:4px;font-size:11px;padding:2px 4px;width:100%">
                <option value="choix" style="color:#000066;background:#FCE4BA"><?php print LANGCHOIX ?></option>
                <option value="supp"  style="color:red;background:#fff"><?php print LANGMESS349 ?></option>
                <?php select_classe_gep(); ?>
              </select>
            </td>
          </tr>
          <?php endfor; ?>
        </tbody>
      </table>
    </div>

    <div style="margin-top:10px;display:flex;gap:8px;align-items:center">
      <script language=JavaScript>buttonMagicSubmit("<?php print LANGMESST398 ?>","create")</script>
      <script language=JavaScript>buttonMagicRetour("acces2.php","_self")</script>
    </div>

  </form>

</div>

<!-- // fin -->
<?php Pgclose(); ?>
</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
