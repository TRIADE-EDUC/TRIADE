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
<script language="JavaScript" src="./librairie_js/jquery-min.js"></script>
<title>Triade - Comptabilité cantine - <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
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
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Gestionnaire de cantine — Comptabilité</font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:8px;">
<?php
$idpers = $_SESSION["id_pers"];
if ((verifDroit($idpers, "cantine")) || ($_SESSION["membre"] == "menuadmin")) {
?>

<div style="display:flex;flex-direction:column;gap:8px;">

<!-- Recherche élève -->
<div class="card">
  <div class="card-header card-header-primary">
    <span>Recherche par élève</span>
  </div>
  <div class="card-body">
    <form method="post" onsubmit="return valide_recherche_eleve()" name="formulaire">
      <div class="form-row">
        <label class="form-lbl"><?php print LANGABS3 ?> :</label>
        <div style="position:relative;">
          <input type="text" name="saisie_nom_eleve" size="22" id="search" autocomplete="off" class="bouton2">
          <div id="userList" style="width:13.5em;border-style:none;background-color:#EEEEEE;position:absolute;z-index:100;"></div>
        </div>
      </div>
      <div style="margin-top:8px;">
        <script language="JavaScript">buttonMagicSubmit("<?php print LANGBT39 ?>","create");</script>
      </div>
    </form>
  </div>
</div>

<!-- Compte non élève -->
<div class="card">
  <div class="card-header card-header-primary">
    <span>Compte non élève</span>
  </div>
  <div class="card-body">
    <form method="post" action="cantine_compta_2.php" name="form2">
      <div class="form-row">
        <label class="form-lbl">Compte :</label>
        <select name="saisie_pers" class="cc-select">
          <option value='0'><?php print LANGCHOIX ?></option>
          <?php
          if ($etatmodif == 1) {
              $liste = preg_replace('/[\{\}]/', '', $liste);
              $liste = explode(",", $liste);
              print "<optgroup label='".LANGGEN1."'>"; select_personne_grpmail('ADM', $liste);
              print "<optgroup label='".LANGGEN2."'>"; select_personne_grpmail('MVS', $liste);
              print "<optgroup label='".LANGGEN3."'>"; select_personne_grpmail('ENS', $liste);
              print "<optgroup label='Personnels'>";   select_personne_grpmail('PER', $liste);
          } else {
              print "<optgroup label='".LANGGEN1."'>"; select_personne('ADM');
              print "<optgroup label='".LANGGEN2."'>"; select_personne('MVS');
              print "<optgroup label='".LANGGEN3."'>"; select_personne('ENS');
              print "<optgroup label='Personnels'>";   select_personne('PER');
          }
          ?>
        </select>
      </div>
      <div style="margin-top:8px;display:flex;gap:8px;">
        <script language="JavaScript">buttonMagicSubmit("<?php print VALIDER ?>","createnoneleve");</script>
        <script language="JavaScript">buttonMagicRetour('cantine.php','_self')</script>
      </div>
    </form>
  </div>
</div>

</div><!-- /flex column -->

<?php
if (isset($_POST["saisie_nom_eleve"])) {
    $motif = strtolower(trim($_POST["saisie_nom_eleve"]));
    $sql = "SELECT c.libelle,e.nom,e.prenom,e.elev_id,e.classe FROM {$prefixe}eleves e, {$prefixe}classes c WHERE lower(e.nom) LIKE '%$motif%' AND c.code_class = e.classe ORDER BY c.libelle, e.nom, e.prenom";
    $res  = execSql($sql);
    $data = chargeMat($res);
?>
<div class="card" style="margin-top:8px;">
  <div class="card-header card-header-primary">
    <span><?php print LANGRECH2 ?> : <span class="card-badge"><?php print ucwords(stripslashes($motif)) ?></span></span>
  </div>
  <?php if (countTriade($data) <= 0): ?>
  <div class="card-body" style="color:#888;font-style:italic;"><?php print LANGRECH3 ?></div>
  <?php else: ?>
  <table class="table" style="margin:0;">
    <thead>
      <tr>
        <th class="cc-th"><?php print ucwords(LANGIMP10) ?></th>
        <th class="cc-th"><?php print LANGIMP8 ?> <?php print LANGIMP9 ?></th>
        <th class="cc-th"></th>
      </tr>
    </thead>
    <tbody>
    <?php for ($i = 0; $i < countTriade($data); $i++): ?>
      <tr class="cc-tr-data">
        <td><?php print ucfirst($data[$i][0]) ?></td>
        <td><?php print strtoupper($data[$i][1]) ?> <?php print ucwords($data[$i][2]) ?></td>
        <td>
          <a href="cantine_compta_2.php?eid=<?php print $data[$i][3] ?>&idclasse=<?php print $data[$i][4] ?>"
             style="color:#080A66;font-weight:600;">Fiche compta</a>
        </td>
      </tr>
    <?php endfor; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
<?php } ?>

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
<script>
$(document).ready(function(){
    $('#search').keyup(function(){
        var query = $(this).val();
        if (query != '') {
            $.ajax({ url:"librairie_php/search_eleve.php", method:"POST", data:{query:query},
                success:function(data){ $('#userList').fadeIn(); $('#userList').html(data); }
            });
        }
    });
    $(document).on('click','li',function(){ $('#search').val($(this).text()); $('#userList').fadeOut(); });
});
</script>
