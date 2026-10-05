<?php
session_start();
include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET);
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
        $anneeScolaire=$_POST["anneeScolaire"];
        setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}

/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
include_once("./librairie_php/lib_error.php");
include_once("common/config.inc.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();

$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);
$mySessionSpid=$mySession['Spid'];
$sql="SELECT a.code_classe, trim(c.libelle), a.code_matiere,";
if(DBTYPE=='pgsql') { $sql .= " trim(m.libelle)||' '||trim(m.sous_matiere)||' '||trim(langue), "; }
elseif(DBTYPE=='mysql') { $sql .= " CONCAT( trim(m.libelle),' ',trim(m.sous_matiere),' ',trim(IFNULL(langue,''))), "; }
$sql .= " a.code_groupe, trim(g.libelle)
FROM {$prefixe}affectations a, {$prefixe}matieres m, {$prefixe}classes c, {$prefixe}groupes g
WHERE code_prof='$mySessionSpid' AND a.code_classe = c.code_class AND a.code_matiere = m.code_mat
AND a.code_groupe = group_id AND a.annee_scolaire = '$anneeScolaire'
AND a.visubull != '0' AND c.offline = '0'
GROUP BY a.code_matiere,a.code_classe,a.code_groupe ORDER BY c.libelle,m.libelle";
$curs=execSql($sql);
$data=chargeMat($curs);
@array_unshift($data,array());
for($i=0;$i<countTriade($data);$i++){
	$tmp=explode(" 0 ",$data[$i][3]);
	$data[$i][3]=$tmp[0].' '.$tmp[1];
}
freeResult($curs);
unset($curs);
?>
<HTML>
<HEAD>
<title>Triade - Compte de <?php print ucwords($mySession['Sp'])." ".strtoupper($mySession['Sn'])?></title>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<style>
.na-card  { background:#fff !important; border:1px solid #c5cae9 !important; border-radius:8px !important; padding:14px 16px !important; margin:10px 0 10px !important; }
.na-row   { display:flex !important; align-items:center !important; margin-bottom:8px !important; gap:8px !important; flex-wrap:wrap !important; }
.na-lbl   { font-size:12px !important; font-weight:600 !important; color:#333 !important; min-width:140px !important; flex-shrink:0 !important; }
.na-foot  { margin-top:8px !important; overflow:hidden !important; }
.na-title { font-size:12px !important; font-weight:bold !important; color:#080A66 !important; margin-bottom:10px !important; padding-bottom:4px !important; border-bottom:1px solid #e8eaf6 !important; }
.na-hr    { border:none !important; border-top:1px solid #c5cae9 !important; margin:6px 0 !important; }
</style>
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<?php
genMatJs('affectation',$data);
$choixmatiere='1';
if (defined("CHOIXMATIEREPROF")) {
	$choixmatiere=CHOIXMATIEREPROF;
}
if (trim($choixmatiere) == "") { $choixmatiere='1'; }
?>
<script type="text/javascript">
function upSelectMat(arg) {
    for(i=1;i<document.formulaire.sMat.options.length;i++){
        document.formulaire.sMat.options[i].value='';
        document.formulaire.sMat.options[i].text='';
    }
    var tmp=arg.value.split(":");
    var clas=tmp[0]; var grp=tmp[1]; var opt=1;
    for(i=0;i<affectation.length;i++) {
        if(affectation[i][0] == clas && affectation[i][4] == grp) {
            myOpt=new Option();
            myOpt.value = affectation[i][2];
            myOpt.text = affectation[i][3];
            myOpt.text = myOpt.text.replace(/ 0 *$/,"");
            document.formulaire.sMat.options[opt]=myOpt; opt++;
        }
    }
    return true;
}

function upSelectMat2(arg) {
    for(i=1;i<document.formulaire2.sMat.options.length;i++){
        document.formulaire2.sMat.options[i].value='';
        document.formulaire2.sMat.options[i].text='';
    }
    var tmp=arg.value.split(":");
    var clas=tmp[0]; var grp=tmp[1]; var opt=1;
    for(i=0;i<affectation.length;i++) {
        if(affectation[i][0] == clas && affectation[i][4] == grp) {
            myOpt=new Option();
            myOpt.value = affectation[i][2];
            myOpt.text = affectation[i][3];
            myOpt.text = myOpt.text.replace(/ 0 *$/,"");
            document.formulaire2.sMat.options[opt]=myOpt; opt++;
        }
    }
    return true;
}
</script>
</head>

<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGPROFB1 ?></font></b></td></tr>
<tr id='cadreCentral0'><td>
<?php
if ($choixmatiere == 0) {
    $onsubmit="onsubmit=\"return verifAccesNotebis()\"";
}else{
    $onsubmit="onsubmit=\"return verifAccesNote()\"";
}
?>

<div class="na-card">
  <div class="na-title"><?php print LANGMESS112 ?></div>

  <form method="post" name="formulaireAnnee1">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGBULL29 ?> :</span>
    <select name='anneeScolaire' class="cc-select" onChange="document.formulaireAnnee1.submit()">
      <?php filtreAnneeScolaireSelectNote($anneeScolaire,3); ?>
    </select>
  </div>
  </form>

  <form method="POST" <?php print $onsubmit ?> name="formulaire" action="bulletincomprof2.php">
  <input type='hidden' value="<?php print $anneeScolaire ?>" name='anneeScolaire' />

  <div class="na-row">
    <span class="na-lbl"><?php print LANGPROFG ?> :</span>
    <select name="sClasseGrp" size="1" class="cc-select" onChange="upSelectMat(this)">
      <option value="0" style="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX3 ?></option>
      <?php
      $dataList=$data;
      for($i=1;$i<countTriade($data);$i++){
          if($i>1 && ($data[$i][4]==$gtmp) && ($data[$i][0]==$ctmp)) { continue; }
          $libelle=$data[$i][4]?$data[$i][1]."-".$data[$i][5]:$data[$i][1];
          if (isset($verif[$libelle])) continue;
          $verif[$libelle]=$libelle;
          print "<option style='color:#000066;background-color:#CCCCFF' value=\"".$data[$i][0].":".$data[$i][4]."\">".$libelle."</option>\n";
          $gtmp=$data[$i][4]; $ctmp=$data[$i][0];
      }
      unset($gtmp,$ctmp,$libelle,$verif);
      ?>
    </select>
  </div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGPROF1 ?> :</span>
    <select name="sMat" size="1" class="cc-select">
      <option value="0" style="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
    </select>
  </div>

  <?php if (COMBULTINTYPE == "oui"): ?>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS113 ?> :</span>
    <select name="typecom" class="cc-select">
      <optgroup label="Standard">
        <option value="0">Appréciations, Conseils pour progresser.</option>
        <option value="5">Elèments du programme travaillés.</option>
      <optgroup label="Spécif.">
        <option value="1">Points d'appui. Progrès. Efforts</option>
        <option value="2">Ecarts par rapport aux objectifs attendu</option>
        <option value="3">Conseils pour progresser</option>
      <optgroup label="Examen">
        <option value="4">Examen Blanc</option>
    </select>
  </div>
  <?php endif; ?>

</div>
<div class="na-foot">
  <script language="JavaScript">buttonMagicSubmit("<?php print LANGBT31 ?>","rien");</script>
</div>
<br><br>
</form>

<div class="na-card">
  <form method="post" action="bulletin_param_prof.php">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGPROFB2 ?> :</span>
    <script language="JavaScript">buttonMagicSubmit("<?php print LANGPROFB3 ?>","rien");</script>
  </div>
  </form>
</div>

<hr class="na-hr">

<div class="na-card">
  <form method="post" action="bulletin_comm_classe.php">
  <div class="na-row">
    <span class="na-lbl">Commentaire sur la classe :</span>
    <script language="JavaScript">buttonMagicSubmit("<?php print LANGBT31 ?>","rien");</script>
  </div>
  </form>
</div>

<?php
$data=aff_enr_parametrage("autorisebulletinprof");
if ($data[0][1] == "oui") {
?>
<hr class="na-hr">
<div class="na-card">
  <form method="post" action="imprimer_trimestre.php">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS115 ?> :</span>
    <script language="JavaScript">buttonMagicSubmit("<?php print LANGMESS116 ?>","rien");</script>
  </div>
  </form>
</div>
<?php } ?>

<hr class="na-hr">

<?php
if ($choixmatiere == 0) {
    $onsubmit="onsubmit=\"return verifAccesNote2bis()\"";
}else{
    $onsubmit="onsubmit=\"return verifAccesNote2()\"";
}
$data=$dataList;
?>

<div class="na-card">
  <div class="na-title"><?php print LANGMESS114 ?></div>

  <form method="post" name="formulaireAnnee2">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGBULL29 ?> :</span>
    <select name='anneeScolaire' class="cc-select" onChange="document.formulaireAnnee2.submit()">
      <?php filtreAnneeScolaireSelectNote($anneeScolaire,3); ?>
    </select>
  </div>
  </form>

  <form method="post" <?php print $onsubmit ?> action="brevet_commentaire.php" name="formulaire2">

  <div class="na-row">
    <span class="na-lbl"><?php print LANGPROFG ?> :</span>
    <select name="sClasseGrp" size="1" class="cc-select" onChange="upSelectMat2(this)">
      <option value="0" style="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX3 ?></option>
      <?php
      for($i=1;$i<countTriade($data);$i++){
          if($i>1 && ($data[$i][4]==$gtmp) && ($data[$i][0]==$ctmp)) { continue; }
          $libelle=$data[$i][4]?$data[$i][1]."-".$data[$i][5]:$data[$i][1];
          if (isset($verif[$libelle])) continue;
          $verif[$libelle]=$libelle;
          print "<option style='color:#000066;background-color:#CCCCFF' value=\"".$data[$i][0].":".$data[$i][4]."\">".$libelle."</option>\n";
          $gtmp=$data[$i][4]; $ctmp=$data[$i][0];
      }
      unset($gtmp,$ctmp,$libelle,$verif);
      ?>
    </select>
  </div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGPROF1 ?> :</span>
    <select name="sMat" size="1" class="cc-select">
      <option value="0" style="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
    </select>
  </div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS117 ?> :</span>
    <select name="serie" class="cc-select">
      <option value="LV2">Collège, option de série LV2</option>
      <option value="DP6">Collège, option Découverte Prof. 6h (DP6)</option>
      <option value="STA">Collège, Technologie option agricole</option>
    </select>
  </div>

</div>
<div class="na-foot">
  <script language="JavaScript">buttonMagicSubmit("<?php print LANGBT31 ?>","rien");</script>
</div>
<br><br>
</form>

</td></tr></table>
<?php
if ($_SESSION['membre'] == "menuadmin") :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
?>
</BODY>
</HTML>
<?php @Pgclose() ?>
