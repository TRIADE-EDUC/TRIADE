<?php
session_start();
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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Configuration cantine - <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Configuration de la cantine</font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:8px;">
<?php
include_once('./librairie_php/db_triade.php');
$idpers = $_SESSION["id_pers"];
if ((verifDroit($idpers, "cantine")) || ($_SESSION["membre"] == "menuadmin")) {

    if (isset($_GET["idedit"])) {
        $data         = recupConfig($_GET["idedit"]);
        $id           = $data[0][0]; $plat = $data[0][1]; $prix = $data[0][2];
        $attribue     = $data[0][3]; $indice_salaire = $data[0][4];
        $platdefault  = ($data[0][5] == 1) ? "checked='checked'" : "";
    }
    if (isset($_POST["create"])) { ajoutPlateau($_POST["plat"], $_POST["prix"], $_POST["attribue"], $_POST["indice_salaire"], $_POST["platdefault"]); }
    if (isset($_POST["modif"]))  { modifPlateau($_POST["plat"], $_POST["prix"], $_POST["attribue"], $_POST["indice_salaire"], $_POST["id"], $_POST["platdefault"]); }
    if (isset($_GET["idsupp"]))  { suppPlateau($_GET["idsupp"]); }
?>

<div class="card" style="margin-bottom:10px;">
  <div class="card-header card-header-primary">
    <span><?php print isset($_GET["idedit"]) ? "Modifier le plat" : "Ajouter un plat" ?></span>
  </div>
  <div class="card-body">
    <form method='post' action="cantine_config.php">
      <div class="form-row">
        <label class="form-lbl">Nom du plat :</label>
        <input type="text" name="plat" size="30" value="<?php print htmlspecialchars($plat ?? '') ?>" class="bouton2">
      </div>
      <div class="form-row" style="margin-top:6px;">
        <label class="form-lbl">Prix du plat :</label>
        <input type="text" name="prix" size="30" value="<?php print htmlspecialchars($prix ?? '') ?>" class="bouton2">
      </div>
      <div class="form-row" style="margin-top:6px;">
        <label class="form-lbl">Membre concerné :</label>
        <select name="attribue" class="cc-select">
          <?php
          if (!empty($attribue)) {
              $lib = ['tous'=>'tous','menueleve'=>'Élève','menuautre'=>'Enseignants / Personnels','menuext'=>'Extérieurs'];
              print "<option value='$attribue' id='select0'>".$lib[$attribue]."</option>";
          }
          ?>
          <option value='tous'>tous</option>
          <option value='menueleve'>Élève</option>
          <option value='menuautre'>Enseignants / Personnels</option>
          <option value='menuext'>Extérieurs</option>
        </select>
      </div>
      <div class="form-row" style="margin-top:6px;">
        <label class="form-lbl">Indice salaire :</label>
        <select name="indice_salaire" class="cc-select">
          <?php
          if (!empty($indice_salaire) && $indice_salaire != "0") {
              print "<option value='$indice_salaire'>$indice_salaire</option>";
          }
          ?>
          <option value='0'>aucun</option>
          <?php recupListIndiceSalaire(); ?>
        </select>
      </div>
      <div class="form-row" style="margin-top:6px;">
        <label class="form-lbl">Plat par défaut :</label>
        <input type="checkbox" name="platdefault" value="1" <?php print $platdefault ?? '' ?>>
      </div>
      <input type="hidden" name="id" value="<?php print $id ?? '' ?>">
      <div style="margin-top:10px;">
        <?php if (isset($_GET["idedit"])): ?>
          <script language="JavaScript">buttonMagicSubmit("<?php print VALIDER ?>","modif");</script>
        <?php else: ?>
          <script language="JavaScript">buttonMagicSubmit("<?php print VALIDER ?>","create");</script>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

<?php
    $data = recupConfigCantine();
?>
<div class="card">
  <div class="card-header card-header-primary">
    <span>Liste des plats</span>
  </div>
  <table class="table" style="margin:0;">
    <thead>
      <tr>
        <th class="cc-th">Nom du plat</th>
        <th class="cc-th">Prix <?php print unitemonnaie() ?></th>
        <th class="cc-th">Attribué</th>
        <th class="cc-th">Indice sal.</th>
        <th class="cc-th" style="width:60px;">Action</th>
      </tr>
    </thead>
    <tbody>
    <?php for ($i = 0; $i < countTriade($data); $i++):
        $platdefaut = ($data[$i][5] == 1) ? " ★" : "";
        $attribLabel = ['tous'=>'Tous','menueleve'=>'Élèves','menuautre'=>'Profs/Pers.','menuext'=>'Extérieurs'];
        $att = $attribLabel[$data[$i][3]] ?? $data[$i][3];
        $ind = ($data[$i][4] == "0") ? "<em>aucun</em>" : $data[$i][4];
    ?>
      <tr class="cc-tr-data">
        <td><?php print htmlspecialchars($data[$i][1]) . $platdefaut ?></td>
        <td><?php print affichageFormatMonnaie($data[$i][2]) ?></td>
        <td><?php print $att ?></td>
        <td><?php print $ind ?></td>
        <td style="white-space:nowrap;">
          <a href="cantine_config.php?idsupp=<?php print $data[$i][0] ?>" title="Supprimer" style="color:#c62828;">
            <img src="image/commun/trash.png" border="0">
          </a>
          &nbsp;
          <a href="cantine_config.php?idedit=<?php print $data[$i][0] ?>" title="Modifier" style="color:#080A66;">
            <img src="image/commun/editer.gif" border="0">
          </a>
        </td>
      </tr>
    <?php endfor; ?>
    </tbody>
  </table>
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
