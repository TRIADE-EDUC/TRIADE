<?php
session_start();
unset($_SESSION["profpclasse"]);
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
        $anneeScolaire=$_POST["anneeScolaire"];
        setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}

include_once("./librairie_php/verifEmailEnregistre.php");
error_reporting(0);
/***************************************************************************
 *                              T.R.I.A.D.E
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
 *   Site                 : http://www.triade-educ.com
 ***************************************************************************/

if ($_COOKIE["tri_eleve"] == "classe") {
	$selectTrieClasse="selected='selected'";
	$selectTrieNom="";
}elseif ($_COOKIE["tri_eleve"] == "nomEleve") {
	$selectTrieNom="selected='selected'";
	$selectTrieClasse="";
}else{
	$selectTrieNom="selected='selected'";
	$selectTrieClasse="";
}

include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("./common/config2.inc.php");
$cnx=cnx();

$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);

$sql="
SELECT
	a.code_classe,
	trim(c.libelle),
	a.code_matiere,
";
$sql .= " CONCAT( trim(m.libelle),' ',trim(m.sous_matiere),' ',trim(IFNULL(langue,''))), ";
$sql .= "
	a.code_groupe,
	trim(g.libelle)
FROM
	{$prefixe}affectations a,
	{$prefixe}matieres m,
	{$prefixe}classes c,
	{$prefixe}groupes g
WHERE
	code_prof='$mySession[Spid]'
	AND a.code_classe = c.code_class
	AND a.code_matiere = m.code_mat
	AND a.code_groupe = group_id
	AND (a.visubull = '1' OR a.visubullbtsblanc = '1')
	AND c.offline = '0'
	AND a.annee_scolaire = '$anneeScolaire'
GROUP BY a.code_matiere,a.code_classe,a.code_groupe
ORDER BY c.libelle,m.libelle
	";
$curs=execSql($sql);
$data=chargeMat($curs);
@array_unshift($data,array());
for($i=0;$i<countTriade($data);$i++){
	$tmp=explode(" 0 ",$data[$i][3]);
	$data[$i][3]=trim($tmp[0].' '.$tmp[1]);
}
genMatJs('affectation',$data);
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
.na-card  { background:#fff; border:1px solid #c5cae9; border-radius:8px; padding:14px 16px; margin:10px 0 14px; }
.na-title { font-size:13px; font-weight:700; color:#080A66; margin:0 0 12px; padding-bottom:6px; border-bottom:2px solid #080A66; }
.na-row   { display:flex; align-items:center; margin-bottom:8px; gap:8px; flex-wrap:wrap; }
.na-lbl   { font-size:12px; font-weight:600; color:#333; min-width:140px; flex-shrink:0; }
.na-foot  { margin-top:12px; text-align:right; overflow:hidden; }
.na-period-block { margin-bottom:8px; font-size:12px; }
.na-period-class { font-weight:700; color:#080A66; margin-bottom:2px; }
.na-period-line  { color:#444; padding-left:14px; }
</style>
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script type="text/javascript">
<?php
$choixmatiere='1';
if (defined("CHOIXMATIEREPROF")) { $choixmatiere=CHOIXMATIEREPROF; }
if (trim($choixmatiere) == "") { $choixmatiere='1'; }
?>
function upSelectMat(arg) {
	for(i=1;i<document.formulaire.sMat.options.length;i++){
		document.formulaire.sMat.options[i].value='';
		document.formulaire.sMat.options[i].text='';
	}
	var tmp=arg.value.split(":");
	var clas=tmp[0];
	var grp=tmp[1];
	var opt='<?php print $choixmatiere ?>';
	for(i=0;i<affectation.length;i++) {
		if(affectation[i][0] == clas && affectation[i][4] == grp) {
		myOpt=new Option();
		myOpt.value = affectation[i][2];
		myOpt.text = affectation[i][3];
		myOpt.text = myOpt.text.replace(/ 0 *$/,"");
		document.formulaire.sMat.options[opt]=myOpt;
		opt++;
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
	var clas=tmp[0];
	var grp=tmp[1];
	var opt='<?php print $choixmatiere ?>';
	for(i=0;i<affectation.length;i++) {
		if(affectation[i][0] == clas && affectation[i][4] == grp) {
		myOpt=new Option();
		myOpt.value = affectation[i][2];
		myOpt.text = affectation[i][3];
		myOpt.text = myOpt.text.replace(/ 0 *$/,"");
		document.formulaire2.sMat.options[opt]=myOpt;
		opt++;
		}
	}
	return true;
}
</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_note.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="top" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof1.js"></SCRIPT>

<?php /* ══════════════ BLOC 1 : AJOUT DE NOTES ══════════════ */ ?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGPROFF?> <?php print LANGMESS77 ?></font></b></td></tr>
<tr id='cadreCentral0'><td>
<div class="na-card">

  <form method="post" action="noteajout.php">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGBULL29 ?> :</span>
    <select name='anneeScolaire' class="cc-select" onChange="this.form.submit()">
      <?php filtreAnneeScolaireSelectNote($anneeScolaire,4); ?>
    </select>
  </div>
  </form>

<?php
$res=vsuite(); $res=0;
if ($res) { print $message; } else {
  if ($choixmatiere == 0) $onsubmit='onsubmit="return verifAccesNotebis()"';
  else                    $onsubmit='onsubmit="return verifAccesNote()"';
?>
<div class="na-title"><?php print LANGPROFF?> <?php print LANGMESS77 ?></div>
<form method="POST" <?php print $onsubmit ?> name="formulaire" action="noteajout2.php">

  <div class="na-row">
    <span class="na-lbl"><?php print LANGPROFG ?> :</span>
    <select name="sClasseGrp" size="1" class="cc-select" onChange="upSelectMat(this)">
      <option value="0"><?php print LANGCHOIX3 ?></option>
      <?php
      for($i=1;$i<countTriade($data);$i++){
        if($i>1 && ($data[$i][4]==$gtmp) && ($data[$i][0]==$ctmp)) continue;
        $libelle=$data[$i][4]?$data[$i][1]."-".$data[$i][5]:$data[$i][1];
        if (isset($verif[$libelle])) continue;
        $verif[$libelle]=$libelle;
        print "<option value=\"".$data[$i][0].":".$data[$i][4]."\">".ucfirst($libelle)."</option>\n";
        $gtmp=$data[$i][4]; $ctmp=$data[$i][0];
      }
      unset($gtmp,$ctmp,$libelle,$verif);
      ?>
    </select>
  </div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGPROF1 ?> :</span>
    <select name="sMat" size="1" class="cc-select">
      <option value="0"><?php print LANGCHOIX ?></option>
    </select>
  </div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS78 ?> :</span>
    <select name="trier" class="cc-select">
      <option value="nomeleve" <?php print $selectTrieNom ?>>Nom</option>
      <option value="classe"   <?php print $selectTrieClasse ?>>Classe</option>
    </select>
  </div>

  <?php if (NOTEUSA == "oui"): ?>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGDISC59 ?> :</span>
    <select name="NoteUsa" class="cc-select">
      <option value="non">Non</option>
      <option value="oui">Oui</option>
    </select>
  </div>
  <?php endif; ?>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGPROF2 ?> :</span>
    <select name='sNbNote' class="cc-select">
      <?php for($j=1;$j<=NOTEPROF;$j++) print "<option value='$j'>$j</option>"; ?>
    </select>
  </div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGGRP56 ?> :</span>
    <select name='NotationSur' class="cc-select">
      <?php $info=0;
        if (NOTATION40=="oui"){$info=1;?><option value="40">40</option><?php }
        if (NOTATION30=="oui"){$info=1;?><option value="30">30</option><?php }
        if (NOTATION20=="oui"){$info=1;?><option value="20" selected>20</option><?php }
        if (NOTATION15=="oui"){$info=1;?><option value="15">15</option><?php }
        if (NOTATION10=="oui"){$info=1;?><option value="10">10</option><?php }
        if (NOTATION5 =="oui"){$info=1;?><option value="5">05</option><?php }
        if (NOTATION6 =="oui"){$info=1;?><option value="6">06</option><?php }
        if ($info==0):?><option value="20">20</option><?php endif; ?>
    </select>
  </div>

  <?php if (NOTEEXAMEN == "oui"): ?>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGDISC60 ?> :</span>
    <select name="NoteExam" class="cc-select">
      <option value="">non</option>
      <?php if (EXAMENBLANC == "oui"): ?>
        <optgroup label="Blanc">
        <?php if (PRODUCTID != "2b85614b9c7cc3e8f7f02fe4fd52e907"): ?>
          <option value="Brevet Blanc">Brevet Blanc</option>
          <option value="Brevet Professionnel Blanc">Brevet Professionnel Blanc</option>
          <option value="BAC Blanc">BAC Blanc</option>
          <option value="CAP Blanc">CAP Blanc</option>
          <option value="BEP Blanc">BEP Blanc</option>
        <?php endif; ?>
          <option value="BTS Blanc">BTS Blanc</option>
          <option value="Partiel Blanc">Partiel Blanc</option>
        <?php if (PRODUCTID != "2b85614b9c7cc3e8f7f02fe4fd52e907"): ?>
          <option value="Concours Blanc">Concours Blanc</option>
        <?php endif; ?>
        </optgroup>
      <?php endif; ?>
      <?php if (EXAMENNAMUR=="oui"): ?><optgroup label="Spécif. Namur"><option value="décembre">Décembre</option><option value="juin">Juin</option></optgroup><?php endif; ?>
      <?php if (EXAMENKINSHASA=="oui"): ?><optgroup label="Spécif. Kinshasa"><option value="1er Session">1er Session</option><option value="Rattrapage">Rattrapage</option></optgroup><?php endif; ?>
      <?php if (EXAMENPIGIERNIMES=="oui"): ?><optgroup label="PIGIER"><option value="ND">Note Devoir (DS)</option><option value="NP">Note Participation</option><option value="DS">DS</option><option value="examen">Examen</option><option value="examen blanc">Examen Blanc</option></optgroup><?php endif; ?>
      <?php if (EXAMENISMAP=="oui"): ?><optgroup label="ISMAP"><option value="CC">CC - Participation</option><option value="DST">DST</option><option value="Rapport">Rapport</option><option value="Fiche de lecture">Fiche de lecture</option><option value="Exposé">Exposé</option><option value="Dad">Dad</option><option value="Lecture">Lecture</option><option value="Examen écrit">Examen écrit</option><option value="Recopiage vocabulaire">Recopiage vocabulaire</option><option value="Mémoire Ip">Mémoire Ip</option><option value="Evaluation Tutorat">Evaluation Tutorat</option></optgroup><?php endif; ?>
      <?php if (EXAMENDS=="oui"): ?><optgroup label="DS"><option value="DS1">DS1</option><option value="DS2">DS2</option><option value="DS3">DS3</option><option value="DS4">DS4</option></optgroup><?php endif; ?>
      <?php if (EXAMEN=="oui"): ?><optgroup label="Examen"><option value="Partiel">Partiel</option></optgroup><?php endif; ?>
      <?php if (EXAMENISPACADEMIES=="oui"): ?><optgroup label="ISP ACADEMIES"><option value="ISP">ISP</option></optgroup><?php endif; ?>
      <?php if (EXAMENCIEFORMATION=="oui"): ?><optgroup label="Spécif. Cie. Formation"><option value="TAS">TAS</option><option value="BTS Blanc">BTS Blanc</option><option value="Partiel Blanc">Partiel Blanc</option></optgroup><?php endif; ?>
      <?php if (EXAMENEEPP=="oui"): ?><optgroup label="Spécif. EEPP"><option value="semestre">Semestriel</option><option value="2session">2ème session</option></optgroup><?php endif; ?>
      <?php if (EXAMENJTC=="oui"): ?><optgroup label="Spécif. JTC"><option value="jtc">Carnet</option></optgroup><?php endif; ?>
      <?php if (EXAMENIPAC=="oui"): ?><optgroup label="IPAC"><option value="Partiel">Partiel</option><option value="Rattrapage">Rattrapage</option><option value="Examen complémentaire">Examen complémentaire</option><option value="Contrôle continu">Contrôle continu</option></optgroup><?php endif; ?>
      <?php if (EXAMENBREVETCOLLEGE=="oui"): ?><optgroup label="Brevet Collège"><option value="Brevet EPS">Brevet EPS</option><option value="Brevet PREV. SANTE ENV.">Brevet PREV. SANTE ENV.</option></optgroup><?php endif; ?>
      <?php
        $dataexam=recupExamenConfig();
        if (countTriade($dataexam)>0) print "<optgroup label='Examen Config'>";
        for($ex=0;$ex<countTriade($dataexam);$ex++) {
          $libelle=$dataexam[$ex][1]; $coef=$dataexam[$ex][2];
          print "<option value='$libelle###$coef'>$libelle</option>";
        }
      ?>
    </select>
  </div>
  <?php endif; ?>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS79 ?> :</span>
    <input type="text" name="notevisiblele" size="12" class="cc-select" value="<?php print dateDMY(); ?>" readonly="readonly">
    <?php include_once("librairie_php/calendar.php"); calendar("id1","document.formulaire.notevisiblele",$_SESSION["langue"],"0"); ?>
  </div>

</div>
<div class="na-foot">
  <script language="JavaScript">buttonMagicSubmit("<?php print LANGBT31 ?>","rien");</script>
</div>
<br><br>
</form>
<?php } ?>
</td></tr></table><br>

<?php /* ══════════════ BLOC 2 : VIE SCOLAIRE ══════════════ */ ?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGPROFF?> <?php print LANGMESS80 ?></font></b></td></tr>
<tr id='cadreCentral0'><td>
<div class="na-card">
<?php
$res=vsuite(); $res=0;
if ($res) { print $message; } else {
  if ($choixmatiere == 0) $onsubmit='onsubmit="return verifAccesNote2bis()"';
  else                    $onsubmit='onsubmit="return verifAccesNote2()"';
?>
<div class="na-title"><?php print LANGPROFF?> <?php print LANGMESS80 ?></div>
<form method="POST" <?php print $onsubmit ?> name="formulaire2" action="noteajoutviescolaire2.php">

  <div class="na-row">
    <span class="na-lbl"><?php print LANGBULL29 ?> :</span>
    <select name='anneeScolaire' class="cc-select">
      <?php filtreAnneeScolaireSelectNote($anneeScolaire,4); ?>
    </select>
  </div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGPROFG ?> :</span>
    <select name="sClasseGrp" size="1" class="cc-select" onChange="upSelectMat2(this)">
      <option value="0"><?php print LANGCHOIX3 ?></option>
      <?php
      for($i=1;$i<countTriade($data);$i++){
        if($i>1 && ($data[$i][4]==$gtmp) && ($data[$i][0]==$ctmp)) continue;
        $libelle=$data[$i][4]?$data[$i][1]."-".$data[$i][5]:$data[$i][1];
        if (isset($verif[$libelle])) continue;
        $verif[$libelle]=$libelle;
        print "<option value=\"".$data[$i][0].":".$data[$i][4]."\">".$libelle."</option>\n";
        $gtmp=$data[$i][4]; $ctmp=$data[$i][0];
      }
      unset($gtmp,$ctmp,$libelle,$verif);
      ?>
    </select>
  </div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGPROF1 ?> :</span>
    <select name="sMat" size="1" class="cc-select">
      <option value="0"><?php print LANGCHOIX ?></option>
    </select>
  </div>

</div>
<div class="na-foot">
  <script language="JavaScript">buttonMagicSubmit("<?php print LANGBT31 ?>","rien");</script>
</div>
<br><br>
</form>
<?php } ?>
</td></tr></table><br>

<?php /* ══════════════ BLOC 3 : PÉRIODES ══════════════ */ ?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGTMESS502 ?> — <?php print $anneeScolaire ?></font></b></td></tr>
<tr id='cadreCentral0'><td>
<div class="na-card">
<?php
for($i=0;$i<countTriade($data);$i++) {
  $idclasse=$data[$i][0]; $nomClasse=$data[$i][1];
  if ($idclasse==0) $nomClasse="Toutes les classes";
  $data2=recupDateTrimIdclasse($idclasse,$anneeScolaire);
  $dateDebutT1=$dateFinT1=$dateDebutT2=$dateFinT2=$dateDebutT3=$dateFinT3="";
  for($j=0;$j<countTriade($data2);$j++){
    $trim=$data2[$j][2];
    if($trim=="trimestre1"){$dateDebutT1=$data2[$j][0];$dateFinT1=$data2[$j][1];}
    if($trim=="trimestre2"){$dateDebutT2=$data2[$j][0];$dateFinT2=$data2[$j][1];}
    if($trim=="trimestre3"){$dateDebutT3=$data2[$j][0];$dateFinT3=$data2[$j][1];}
    if(($dateDebutT3=="")&&($dateDebutT1!="")&&($dateDebutT2!="")) $semestriel="oui";
    if($dateDebutT1!="") $dateDebutT1=dateForm($dateDebutT1);
    if($dateFinT1!="")   $dateFinT1=dateForm($dateFinT1);
    if($dateDebutT2!="") $dateDebutT2=dateForm($dateDebutT2);
    if($dateFinT2!="")   $dateFinT2=dateForm($dateFinT2);
    if($dateDebutT3!="") $dateDebutT3=dateForm($dateDebutT3);
    if($dateFinT3!="")   $dateFinT3=dateForm($dateFinT3);
  }
  if(trim($dateDebutT1)=="") continue;
  echo '<div class="na-period-block">';
  echo '<div class="na-period-class"><img src="image/on10.gif"> '.LANGASS17.' : '.htmlspecialchars($nomClasse).'</div>';
  echo '<div class="na-period-line">'.LANGMESS157.' 1 : '.$dateDebutT1.' &ndash; '.$dateFinT1.'</div>';
  if($dateDebutT2) echo '<div class="na-period-line">'.LANGMESS157.' 2 : '.$dateDebutT2.' &ndash; '.$dateFinT2.'</div>';
  if($dateDebutT3) echo '<div class="na-period-line">'.LANGMESS157.' 3 : '.$dateDebutT3.' &ndash; '.$dateFinT3.'</div>';
  echo '</div>';
}
?>
</div>
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
