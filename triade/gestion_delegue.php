<?php
session_start();
error_reporting(0);
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
 *   Site                 : http://www.triade-educ.com
 *
 ***************************************************************************/
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("menuadmin");



$ident    = array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession = hashSessionVar($ident);
unset($ident);
$Spid = $mySession["Spid"];

$sql = "SELECT a.code_classe,trim(c.libelle),a.code_matiere, CONCAT( trim(m.libelle),' ',trim(m.sous_matiere),' ',trim(langue) ), a.code_groupe, trim(g.libelle) FROM {$prefixe}affectations a, {$prefixe}matieres m, {$prefixe}classes c, {$prefixe}groupes g WHERE code_prof='$Spid' AND a.code_classe = c.code_class AND a.code_matiere = m.code_mat AND a.code_groupe = group_id ORDER BY c.libelle";
$curs = execSql($sql);
$data = chargeMat($curs);
@array_unshift($data, array());
for ($i = 0; $i < countTriade($data); $i++) {
	$tmp = explode(" 0 ", $data[$i][3]);
	$data[$i][3] = $tmp[0] . ' ' . $tmp[1];
}
freeResult($curs);
unset($curs);

$saveOk = false;
if (isset($_POST["create"])) {
	@delete_delegue($_POST["idclasse"]);
	create_delegue($_POST["parent1"], $_POST["parent2"], $_POST["eleve1"], $_POST["eleve2"], $_POST["idclasse"]);
	$saveOk = true;
}
?>
<HTML>
<HEAD>
<title>Triade - Compte de <?php print ucwords($mySession['Sp'])." ".strtoupper($mySession['Sn']) ?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg) { alertify.error(msg); };</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"] ?>.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"] ?>1.js"></SCRIPT>

<form method="POST" onsubmit="return verifAccesFiche()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Gestion délégués</font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<div class="na-card">
  <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
    <label style="font-size:13px;color:#555;font-weight:600;"><?php print LANGPROFG ?> :</label>
    <?php if ($_SESSION["membre"] == "menuprof") : ?>
      <select name="sClasseGrp" class="cc-select" style="width:220px;">
        <option value="0"><?php print LANGCHOIX3 ?></option>
        <?php
        for ($i = 1; $i < countTriade($data); $i++) {
          if ($i > 1 && ($data[$i][4] == $gtmp) && ($data[$i][0] == $ctmp)) {
            continue;
          }
          $libelle = $data[$i][4] ? $data[$i][1]."-".$data[$i][5] : $data[$i][1];
          print "<option value=\"" . $data[$i][0] . "\">" . $libelle . "</option>\n";
          $gtmp = $data[$i][4];
          $ctmp = $data[$i][0];
        }
        unset($gtmp, $ctmp, $libelle);
        ?>
      </select>
    <?php else : ?>
      <select name="sClasseGrp" class="cc-select" style="width:220px;">
        <option><?php print LANGCHOIX ?></option>
        <?php select_classe(); ?>
      </select>
    <?php endif; ?>
    <script language="JavaScript">buttonMagicSubmit("<?php print LANGBT31 ?>","rien");</script>
    <script language="JavaScript">buttonMagic("Impression","gestion_delegue_impr.php","_parent","","");</script>
  </div>
</div>
</td></tr></table>
</form>

<?php
if ((isset($_POST["rien"])) || (isset($_POST["create"]))) {
	$saisie_classe = $_POST["sClasseGrp"];
	if (isset($_POST["idclasse"])) {
		$saisie_classe = $_POST["idclasse"];
	}

	$sql = "SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves,{$prefixe}classes WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
	$res  = execSql($sql);
	$data = chargeMat($res);
	$cl   = $data[0][0];

	$sql = "SELECT b.libelle,a.elev_id,a.nom,a.prenom FROM {$prefixe}eleves a,{$prefixe}classes b WHERE a.classe='$saisie_classe' AND b.code_class='$saisie_classe' ORDER BY nom";
	$res        = execSql($sql);
	$data_eleve = chargeMat($res);

	$data_del  = aff_delegue($saisie_classe);
	$idparent1 = $data_del[0][1];
	$idparent2 = $data_del[0][2];
	$ideleve1  = $data_del[0][3];
	$ideleve2  = $data_del[0][4];
?>
<br>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGPROFP12 ?><?php print LANGPROFP13 ?> : <font id='color2'><?php print $cl ?></font></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<form method="post">
<input type="hidden" name="idclasse" value="<?php print $saisie_classe ?>">
<div class="na-card">
  <div style="display:grid;grid-template-columns:220px 1fr;gap:10px 14px;align-items:center;max-width:560px;">

    <div style="text-align:right;font-size:13px;color:#555;font-weight:600;">
      <?php print LANGPROFP14 ?> 1
      <em style="font-size:11px;font-weight:400;color:#888;"><?php print LANGMESS62 ?></em> :
    </div>
    <select name="parent1" class="cc-select">
      <option value="null"><?php print LANGCHOIX ?></option>
      <?php for ($j = 0; $j < countTriade($data_eleve); $j++) {
        $sel = ($idparent1 == $data_eleve[$j][1]) ? "selected='selected'" : "";
        print "<option $sel value=\"" . $data_eleve[$j][1] . "\">" . ucwords(trim($data_eleve[$j][2])) . " " . trunchaine(trim($data_eleve[$j][3]), 15) . "</option>";
      } ?>
    </select>

    <div style="text-align:right;font-size:13px;color:#555;font-weight:600;">
      <?php print LANGPROFP14 ?> 2
      <em style="font-size:11px;font-weight:400;color:#888;"><?php print LANGMESS62 ?></em> :
    </div>
    <select name="parent2" class="cc-select">
      <option value="null"><?php print LANGCHOIX ?></option>
      <?php for ($j = 0; $j < countTriade($data_eleve); $j++) {
        $sel = ($idparent2 == $data_eleve[$j][1]) ? "selected='selected'" : "";
        print "<option $sel value=\"" . $data_eleve[$j][1] . "\">" . ucwords(trim($data_eleve[$j][2])) . " " . trunchaine(trim($data_eleve[$j][3]), 15) . "</option>";
      } ?>
    </select>

    <div style="text-align:right;font-size:13px;color:#555;font-weight:600;">
      <?php print LANGPROFP16 ?> 1
      <em style="font-size:11px;font-weight:400;color:#888;"><?php print LANGBULL31 ?></em> :
    </div>
    <select name="eleve1" class="cc-select">
      <option value="null"><?php print LANGCHOIX ?></option>
      <?php for ($j = 0; $j < countTriade($data_eleve); $j++) {
        $sel = ($ideleve1 == $data_eleve[$j][1]) ? "selected='selected'" : "";
        print "<option $sel value=\"" . $data_eleve[$j][1] . "\">" . ucwords(trim($data_eleve[$j][2])) . " " . trunchaine(trim($data_eleve[$j][3]), 15) . "</option>";
      } ?>
    </select>

    <div style="text-align:right;font-size:13px;color:#555;font-weight:600;">
      <?php print LANGPROFP16 ?> 2
      <em style="font-size:11px;font-weight:400;color:#888;"><?php print LANGBULL31 ?></em> :
    </div>
    <select name="eleve2" class="cc-select">
      <option value="null"><?php print LANGCHOIX ?></option>
      <?php for ($j = 0; $j < countTriade($data_eleve); $j++) {
        $sel = ($ideleve2 == $data_eleve[$j][1]) ? "selected='selected'" : "";
        print "<option $sel value=\"" . $data_eleve[$j][1] . "\">" . ucwords(trim($data_eleve[$j][2])) . " " . trunchaine(trim($data_eleve[$j][3]), 15) . "</option>";
      } ?>
    </select>

  </div>
</div>
<div class="na-foot">
  <script language="JavaScript">buttonMagicSubmit("<?php print LANGENR ?>","create");</script>
</div>
</form>

</td></tr></table>
<?php } ?>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
?>
<?php if ($saveOk) : ?>
<script>document.addEventListener('DOMContentLoaded', function(){ alertify.success('<?php print addslashes(LANGDONENR) ?>'); });</script>
<?php endif; ?>
</BODY>
</HTML>
<?php @Pgclose(); ?>
