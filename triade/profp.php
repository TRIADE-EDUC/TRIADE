<?php
session_start();
unset($_SESSION["profpclasse"]);
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
error_reporting(0);
include_once("common/config.inc.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);
$donne=$_SESSION["id_suppleant"];
$sql=<<<SQL
SELECT p.idprof, p.idclasse
FROM {$prefixe}prof_p p, {$prefixe}classes c
WHERE p.idprof='$donne' AND p.annee_scolaire='$anneeScolaire' AND c.code_class=p.idclasse
ORDER BY c.libelle
SQL;
$curs=execSql($sql);
$data=chargeMat($curs);
for($i=0;$i<countTriade($data);$i++){
	$nomclasse=chercheClasse($data[$i][1]);
	$nomclasse=$nomclasse[0][1];
	$option.="<option style='color:#000066;background-color:#CCCCFF' value='".$data[$i][1]."'>$nomclasse</option>\n";
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
.na-hr    { border:none !important; border-top:1px solid #c5cae9 !important; margin:6px 0 !important; }
</style>
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
</head>

<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGPROF12 ?></font></b></td></tr>
<tr id='cadreCentral0'><td>

<div class="na-card">
  <form method="post" action="profp.php" name="formulaireAnnee">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGBULL29 ?> :</span>
    <select name='anneeScolaire' class="cc-select" onChange="document.formulaireAnnee.submit()">
      <?php filtreAnneeScolaireSelectNote($anneeScolaire,10); ?>
    </select>
  </div>
  </form>

  <form method="POST" onsubmit="return verifAccesFiche()" name="formulaire" action="profp2.php">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGPROFG ?> :</span>
    <select name="sClasseGrp" size="1" class="cc-select">
      <option value="0" style="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX3 ?></option>
      <?php print $option; ?>
    </select>
  </div>
</div>
<div class="na-foot">
  <script language="JavaScript">buttonMagicSubmit("<?php print LANGBT31 ?>","rien");</script>
</div>
<br><br>
</form>

<?php
if (verifProfPUE($_SESSION["id_pers"])) {
	$option="";
	$data=recupIdClasseUEProfp($_SESSION["id_pers"]);
	for($i=0;$i<countTriade($data);$i++){
		$nomclasse=chercheClasse($data[$i][0]);
		$nomclasse=$nomclasse[0][1];
		$option.="<option style='color:#000066;background-color:#CCCCFF' value='".$data[$i][0]."'>$nomclasse</option>\n";
	}
?>
<hr class="na-hr">
<div class="na-card">
  <form method="POST" action="profpUE2.php" name="formulaireUE">
  <div class="na-row">
    <span class="na-lbl">Unité d'Enseignement :</span>
    <select name="sClasseGrp" size="1" class="cc-select">
      <option value="0" style="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX3 ?></option>
      <?php print $option; ?>
    </select>
  </div>
</div>
<div class="na-foot">
  <script language="JavaScript">buttonMagicSubmit("<?php print LANGBT31 ?>","rien");</script>
</div>
<br><br>
</form>
<?php } ?>

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
