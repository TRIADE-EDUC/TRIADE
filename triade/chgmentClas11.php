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
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
if (empty($_SESSION["adminplus"])) {
    print "<script>location.href='./base_de_donne_key.php'</script>";
    exit;
}
include_once("librairie_php/db_triade.php");
$cnx = cnx();

$annefutur = isset($_POST["anneefutur"]) ? $_POST["anneefutur"] : '';
$nbEleve   = isset($_POST["nbEleve"]) ? (int)$_POST["nbEleve"] : 0;
for ($i = 0; $i < $nbEleve; $i++) {
    $id_eleve = $_POST["idEleve_$i"];
    if ($id_eleve > 0) {
        $newsClasse  = $_POST["new_classe"];
        $nomeleve    = recherche_eleve_nom($id_eleve);
        $prenomeleve = recherche_eleve_prenom($id_eleve);
        if ($newsClasse == 'rien') {
            continue;
        } elseif ($newsClasse == 'quit') {
            @suppression_eleve($id_eleve);
            history_cmd($_SESSION["nom"], "SUPPRESSION", "Eleve $nomeleve $prenomeleve");
            continue;
        } elseif ($newsClasse == 'sansclasse') {
            $cr = @changementClasseEleve($id_eleve, "-1", $annefutur);
            history_cmd($_SESSION["nom"], "SANS CLASSE", "Eleve $nomeleve");
        } else {
            $cr = @changementClasseEleve($id_eleve, $newsClasse, $annefutur);
        }
        if ($cr) {
            history_cmd($_SESSION["nom"], "CHANGEMENT CLASSE", "Eleve $nomeleve");
            SuppressionInfoEleveSuiteChangement($id_eleve);
        }
    }
}
Pgclose();
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

  <div class="card" style="border-top:3px solid #2e7d32">
    <div class="card-body" style="display:flex;flex-direction:column;gap:14px">

      <div style="display:flex;align-items:center;gap:10px">
        <i class="bi bi-check-circle-fill" style="color:#2e7d32;font-size:20px;flex-shrink:0"></i>
        <p style="margin:0;font-size:13px;color:#2e7d32;font-weight:600"><?php print LANGBASE24 ?>.</p>
      </div>

      <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
        <script language=JavaScript>buttonMagic("<?php print LANGBT48 ?>","acces2.php","_parent","","")</script>
        <script language=JavaScript>buttonMagic("<?php print LANGBT47 ?>","chgmentClas00.php","_parent","","")</script>
      </div>

    </div>
  </div>

</div>

<!-- // fin -->
</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
