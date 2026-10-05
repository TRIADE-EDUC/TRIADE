<?php
session_start();
include_once("./common/config5.inc.php");
header('Content-type: text/html; charset='.CHARSET);
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/scriptaculous.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_cantine.js"></script>
<style>
.cc2-modal-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:900;display:none;align-items:center;justify-content:center}
.cc2-modal-overlay.visible{display:flex}
.cc2-modal{background:#fff;border-radius:12px;padding:22px;width:400px;max-width:95vw;box-shadow:0 6px 28px rgba(0,0,0,.22)}
.cc2-modal-title{font-size:15px;font-weight:700;color:#080A66;margin-bottom:14px}
.cc2-modal-field{margin-bottom:10px}
.cc2-modal-label{font-size:12px;font-weight:600;color:#333;display:block;margin-bottom:4px}
.cc2-modal-input{width:100%;padding:8px 10px;border:1px solid #dde0f0;border-radius:6px;font-size:13px}
.cc2-modal-actions{display:flex;gap:8px;margin-top:14px}
.cc2-modal-btn{flex:1;padding:10px;border:none;border-radius:6px;font-size:13px;font-weight:700;cursor:pointer}
.cc2-modal-btn-save{background:#080A66;color:#fff}
.cc2-modal-btn-close{background:#f0f2fa;color:#333;border:1px solid #dde0f0}
.cc2-modal-result{color:#c62828;font-size:12px;margin-top:8px;min-height:16px}
</style>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
$cnx = cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Gestionnaire de cantine — Compte</font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:8px;">
<?php
if ((verifDroit($_SESSION["id_pers"], "cantine")) || ($_SESSION["membre"] == "menuadmin")) {

    if (isset($_GET["eid"])) {
        $idpers = $_GET["eid"];
        $membre = "menueleve";
        $nom     = recherche_eleve_nom($idpers);
        $prenom  = recherche_eleve_prenom($idpers);
        $nomprenom = "$nom $prenom";
    }
    if (isset($_GET["idpers"])) {
        $idpers = $_GET["idpers"];
        $membre = $_GET["membre"];
        if ($membre == "menueleve") {
            $nom    = recherche_eleve_nom($idpers);
            $prenom = recherche_eleve_prenom($idpers);
            $nomprenom = "$nom $prenom";
        } else {
            $membre    = renvoiTypePersonneMembre(recherche_type_personne($idpers));
            $nomprenom = recherche_personne2($idpers);
        }
    }
    if (isset($_POST["saisie_pers"])) {
        $idpers = $_POST["saisie_pers"];
        $membre = renvoiTypePersonneMembre(recherche_type_personne($idpers));
        $nomprenom = recherche_personne2($idpers);
    }
    if (isset($_GET["idsupp"])) {
        $idpers = $_GET["idpers2"];
        $membre = $_GET["membre"];
        if ($membre == "menueleve") {
            $nom    = recherche_eleve_nom($idpers);
            $prenom = recherche_eleve_prenom($idpers);
            $nomprenom = "$nom $prenom";
        } else {
            $membre    = renvoiTypePersonneMembre(recherche_type_personne($idpers));
            $nomprenom = recherche_personne2($idpers);
        }
        suppOperationCantine($_GET["idsupp"]);
    }
?>

<!-- Credit modal -->
<div id="cc2-credit-modal" class="cc2-modal-overlay" onclick="this.classList.remove('visible')">
  <div class="cc2-modal" onclick="event.stopPropagation()">
    <div class="cc2-modal-title">Créditer le compte de <?php print htmlspecialchars($nomprenom ?? '') ?></div>
    <form>
      <div class="cc2-modal-field">
        <label class="cc2-modal-label">Date</label>
        <input type="text" name="date" class="cc2-modal-input" value="<?php print dateDMY() ?>">
      </div>
      <div class="cc2-modal-field">
        <label class="cc2-modal-label">Crédit</label>
        <input type="text" name="credit" class="cc2-modal-input" value="0" onclick="this.value=''">
      </div>
      <div class="cc2-modal-field">
        <label class="cc2-modal-label">Détail</label>
        <input type="text" name="detail" class="cc2-modal-input" maxlength="250">
      </div>
      <div class="cc2-modal-actions">
        <button type="button" class="cc2-modal-btn cc2-modal-btn-save"
          onclick="enrCreditCantine('<?php print $idpers ?? '' ?>','<?php print $membre ?? '' ?>',this.form.date.value,this.form.credit.value,this.form.detail.value,'cc2-credit-result')">
          Enregistrer
        </button>
        <button type="button" class="cc2-modal-btn cc2-modal-btn-close"
          onclick="document.getElementById('cc2-credit-modal').classList.remove('visible')">Fermer</button>
      </div>
      <div id="cc2-credit-result" class="cc2-modal-result"></div>
    </form>
  </div>
</div>

<div style="display:flex;gap:8px;margin-bottom:10px;flex-wrap:wrap;">
  <button type="button" class="btn btn-primary"
    onclick="document.getElementById('cc2-credit-modal').classList.add('visible')">
    Créditer le compte
  </button>
  <button type="button" class="btn btn-secondary"
    onclick="open('cantine_compta_2.php?idpers=<?php print $idpers ?? '' ?>&amp;membre=<?php print $membre ?? '' ?>','_parent','')">
    Actualiser
  </button>
  <script language="JavaScript">buttonMagicRetour('cantine_compta.php','_self')</script>
</div>

<?php
    $data  = recupComptaPers($idpers ?? 0, $membre ?? '');
    $total = 0;
?>
<div class="card">
  <div class="card-header card-header-primary">
    <span>Opérations — <?php print htmlspecialchars($nomprenom ?? '') ?></span>
  </div>
  <table class="table" style="margin:0;">
    <thead>
      <tr>
        <th class="cc-th">Date</th>
        <th class="cc-th">Détail</th>
        <th class="cc-th" style="text-align:right;">Montant <?php print unitemonnaie() ?></th>
        <th class="cc-th" style="width:36px;"></th>
      </tr>
    </thead>
    <tbody>
    <?php for ($i = 0; $i < countTriade($data); $i++):
        if ($i >= 50) break;
        $total += $data[$i][1];
    ?>
      <tr class="cc-tr-data">
        <td><?php print dateForm($data[$i][0]) ?></td>
        <td><?php print urldecode($data[$i][2]) ?></td>
        <td style="text-align:right;"><?php print affichageFormatMonnaie($data[$i][1]) ?></td>
        <td style="text-align:center;">
          <a href="cantine_compta_2.php?idsupp=<?php print $data[$i][3] ?>&idpers2=<?php print $idpers ?? '' ?>&membre=<?php print $membre ?? '' ?>"
             title="Supprimer" style="color:#c62828;">
            <img src="image/commun/b_drop.png" border="0">
          </a>
        </td>
      </tr>
    <?php endfor; ?>
    </tbody>
  </table>
  <div class="card-footer-total">
    Total : <?php print affichageFormatMonnaie($total) ?> <?php print unitemonnaie() ?>
  </div>
</div>

<?php } else { ?>
<div class="alert alert-danger">Accès réservé</div>
<?php } ?>

</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
} else {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
}
?>
</BODY></HTML>
