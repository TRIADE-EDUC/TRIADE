<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
	$anneeScolaire=$_POST["anneeScolaire"];
	setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}

include_once("./librairie_php/verifEmailEnregistre.php");
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
if (empty($_SESSION["membre"])) {
    print "<script language='javascript'>location.href='./acces_refuse.php'</script>";
    exit;
}

include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("./common/config2.inc.php");
$cnx=cnx();
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);
$sql="SELECT a.code_classe, trim(c.libelle), a.code_matiere,";
if(DBTYPE=='pgsql') { $sql .= " trim(m.libelle)||' '||trim(m.sous_matiere)||' '||trim(langue), "; }
elseif(DBTYPE=='mysql') { $sql .= " CONCAT( trim(m.libelle),' ',trim(m.sous_matiere),' ',trim(IFNULL(langue,''))), "; }
$sql .= " a.code_groupe, trim(g.libelle)
FROM {$prefixe}affectations a, {$prefixe}matieres m, {$prefixe}classes c, {$prefixe}groupes g
WHERE code_prof='$mySession[Spid]' AND a.code_classe = c.code_class AND a.code_matiere = m.code_mat
AND a.code_groupe = group_id AND a.annee_scolaire = '$anneeScolaire'
GROUP BY a.code_matiere,a.code_classe,a.code_groupe ORDER BY c.libelle,m.libelle";
$curs=execSql($sql);
$data=chargeMat($curs);
@array_unshift($data,array());
for($i=0;$i<countTriade($data);$i++){
	$tmp=explode(" 0 ",$data[$i][3]);
	$data[$i][3]=$tmp[0].' '.$tmp[1];
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
.na-card  { background:#fff !important; border:1px solid #c5cae9 !important; border-radius:8px !important; padding:14px 16px !important; margin:10px 0 10px !important; }
.na-row   { display:flex !important; align-items:center !important; margin-bottom:8px !important; gap:8px !important; flex-wrap:wrap !important; }
.na-lbl   { font-size:12px !important; font-weight:600 !important; color:#333 !important; min-width:140px !important; flex-shrink:0 !important; }
.na-foot  { margin-top:8px !important; overflow:hidden !important; }
</style>
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
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
    var clas=tmp[0]; var grp=tmp[1];
    var opt='<?php print $choixmatiere ?>';
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
</script>
</head>

<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_note.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGMESS426 ?></font></b></td></tr>
<tr id='cadreCentral0'><td>

<?php
if (0) {
    ?>
    <br>
    <center><font class="T2"><?php print LANGKEY1 ?></font></center>
    <?php
}else {
    $date=dateDMY2();
    $heure=dateHIS();
    $idprof=$_SESSION["id_suppleant"];
    $dataseance=recupInfoSeance($date,$heure,$idprof);
    if (countTriade($dataseance) > 0) {
        $idmatiere=$dataseance[0][1];
        $idclasse=$dataseance[0][0];
        $idgroupe=$dataseance[0][4];
        if ($idgroupe > 0) { $groupelibelle="-".chercheGroupeNom($idgroupe); }
        $optionsClasseGrp="<option value='$idclasse:$idgroupe' style='color:#000066;background-color:#CCCCFF'>".chercheClasse_nom($idclasse)."$groupelibelle</option>";
        $optionssMat="<option value='$idmatiere' style='color:#000066;background-color:#CCCCFF'>".chercheMatiereNom($idmatiere)."</option>";
        $onsubmit="";
    }else{
        $optionsClasseGrp="<option value='0' style='color:#000066;background-color:#FCE4BA'>".LANGCHOIX3."</option>";
        $optionssMat="<option value='0' style='color:#000066;background-color:#FCE4BA'>".LANGCHOIX."</option>";
        if ($choixmatiere == 0) { $onsubmit="onsubmit=\"return verifAccesNotebis()\""; }
        else { $onsubmit="onsubmit=\"return verifAccesNote()\""; }
    }
?>

<div class="na-card">
  <form method="post" action="retardprof.php" name="formulaireAnnee">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGBULL29 ?> :</span>
    <select name='anneeScolaire' class="cc-select" onChange="document.formulaireAnnee.submit()">
      <?php filtreAnneeScolaireSelectNote($anneeScolaire,3); ?>
    </select>
  </div>
  </form>

  <form method="POST" <?php print $onsubmit ?> name="formulaire" action="retardprof2.php">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGPROFG ?> :</span>
    <select name="sClasseGrp" size="1" class="cc-select" onChange="upSelectMat(this)">
      <?php print $optionsClasseGrp ?>
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
      <?php print $optionssMat ?>
    </select>
  </div>

</div>
<div class="na-foot">
  <script language="JavaScript">buttonMagicSubmit("<?php print LANGBT31 ?>","rien");</script>
  <?php if (VATEL != 1) { ?>
  <script language="JavaScript">buttonMagicSubmit("<?php print LANGTMESS478 ?>","codebarre");</script>
  <?php if (PRESENTPROF == "oui") { ?>
  <br><br><script language="JavaScript">buttonMagicRetour2("gestion_abs_present.php","_parent","<?php print LANGTMESS479 ?>")</script>
  <?php } ?>
  <?php } ?>
</div>
<br><br>
</form>

<?php } ?>

</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
