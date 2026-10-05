<?php
session_start();
error_reporting(0);
include("./common/config.inc.php");
include("./librairie_php/db_triade.php");
$cnx = cnx();
error($cnx);
$ident = ['nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid'];
$mySession = hashSessionVar($ident);
unset($ident);
$Spid = $mySession["Spid"];
$sql = "SELECT a.code_classe, trim(c.libelle), a.code_matiere,";
if (DBTYPE == 'pgsql') {
    $sql .= " trim(m.libelle)||' '||trim(m.sous_matiere)||' '||trim(langue), ";
} elseif (DBTYPE == 'mysql') {
    $sql .= " CONCAT( trim(m.libelle),' ',trim(m.sous_matiere),' ',trim(langue) ), ";
}
$sql .= " a.code_groupe, trim(g.libelle) FROM {$prefixe}affectations a, {$prefixe}matieres m, {$prefixe}classes c, {$prefixe}groupes g WHERE code_prof='$Spid' AND a.code_classe = c.code_class AND a.code_matiere = m.code_mat AND a.code_groupe = group_id ORDER BY c.libelle";
$curs = execSql($sql);
$data = chargeMat($curs);
@array_unshift($data, []);
for ($i = 0; $i < countTriade($data); $i++) {
    $tmp = explode(" 0 ", $data[$i][3]);
    $data[$i][3] = $tmp[0] . ' ' . $tmp[1];
}
freeResult($curs); unset($curs);
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
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Affectation régimes - <?php print ucwords($mySession['Sp'])." ".strtoupper($mySession['Sn']) ?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"] ?>.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"] ?>1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGPROF13 ?></font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:8px;">

<div class="card">
  <div class="card-header card-header-primary">
    <span>Sélection de la classe</span>
  </div>
  <div class="card-body">
    <form method="POST" onsubmit="return verifAccesFiche()" name="formulaire" action="regime_affectation2.php">
      <div class="form-row">
        <label class="form-lbl"><?php print LANGPROFG ?> :</label>
        <?php if ($_SESSION["membre"] == "menuprof"): ?>
          <select name="sClasseGrp" class="cc-select">
            <option value="0"><?php print LANGCHOIX3 ?></option>
            <?php
            for ($i = 1; $i < countTriade($data); $i++) {
                if ($i > 1 && ($data[$i][4] == $gtmp) && ($data[$i][0] == $ctmp)) continue;
                $libelle = $data[$i][4] ? $data[$i][1]."-".$data[$i][5] : $data[$i][1];
                if (isset($verif[$libelle])) continue;
                $verif[$libelle] = $libelle;
                print "<option value=\"".$data[$i][0]."\">".$libelle."</option>\n";
                $gtmp = $data[$i][4]; $ctmp = $data[$i][0];
            }
            unset($gtmp, $ctmp, $libelle, $verif);
            ?>
          </select>
        <?php else: ?>
          <select name="sClasseGrp" class="cc-select">
            <option id='select0'><?php print LANGCHOIX ?></option>
            <?php select_classe(); ?>
          </select>
        <?php endif; ?>
      </div>
      <div style="margin-top:12px;display:flex;gap:8px;">
        <script language="JavaScript">buttonMagicSubmit("<?php print LANGBT31 ?>","rien");</script>
        <script language="JavaScript">buttonMagicRetour2("cantine.php",'_parent','Retour');</script>
      </div>
    </form>
  </div>
</div>

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
<?php @Pgclose() ?>
