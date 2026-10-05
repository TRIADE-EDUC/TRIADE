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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<?php include("./librairie_php/lib_licence.php"); ?>
<script language="JavaScript">
var errfound = false;
function Validlongueur(item,len) { return (item.length >= len); }
function error9(elem, text) {
    if (errfound) return;
    window.alert(text);
    elem.select(); elem.focus();
    errfound = true;
}
function Validdate(nom) {
    var slach1 = nom.charAt(2), slach2 = nom.charAt(5);
    var jour = nom.substring(0,2), mois = nom.substring(3,5);
    var caractere = nom.charAt(6);
    if (isNaN(caractere)) { return false; }
    var annee = nom.substring(6,10);
    if (isNaN(jour) || isNaN(mois) || isNaN(annee)) { return false; }
    if ((annee > 9999) || (jour > 31) || (mois > 12) || (slach1 != '/') || (slach2 != '/')) { return false; }
    return true;
}
function error2(text) {
    if (errfound) return;
    window.alert(text);
    errfound = true;
}
function Validselect(item) { return (item != 0); }
function ValiddatePeriode(datedebut, datefin) {
    var j=datedebut.substring(0,2), m=datedebut.substring(3,5), a=datedebut.substring(6,10);
    var d1 = a+""+m+""+j;
    j=datefin.substring(0,2); m=datefin.substring(3,5); a=datefin.substring(6,10);
    var d2 = a+""+m+""+j;
    return (d1 <= d2);
}
function validedatestage() {
    errfound = false;
    if (document.formulaire.num.value.length < 1) {
        error9(document.formulaire.num,"<?php print LANGSTAGE97 ?>  \n\n Service Triade ");
    }
    if (isNaN(document.formulaire.num.value)) {
        error9(document.formulaire.num,"<?php print LANGSTAGE97 ?>    \n\n Service TRIADE ");
    }
    if (!Validdate(document.formulaire.debutdate.value)) {
        error9(document.formulaire.debutdate,"<?php print LANGSTAGE98 ?> \n\n Service TRIADE");
    }
    if (!Validdate(document.formulaire.findate.value)) {
        error9(document.formulaire.findate,"<?php print LANGSTAGE99 ?> \n\n Service TRIADE");
    }
    if (!ValiddatePeriode(document.formulaire.debutdate.value, document.formulaire.findate.value)) {
        error2("<?php print "La date de fin de stage ne peut être avant la date de début" ?> \n\n Service TRIADE");
    }
    if (!Validselect(document.formulaire.saisie_classe.options.selectedIndex)) {
        error2(langfunc11);
    }
    return !errfound;
}
</script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGSTAGE44 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign="top">
<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();
validerequete("2");
?>

<form method="post" onsubmit="return validedatestage()" name="formulaire">
<div class="na-card">

  <div class="na-row">
    <span class="na-lbl"><?php print LANGSTAGE48 ?> :</span>
    <select name="num" class="cc-select">
      <?php if ($numstage != '') { print "<option value='$numstage' id='select0'>$numstage</option>"; } ?>
      <option value="" id="select0"></option>
      <?php for ($i=0; $i<=30; $i++) { print "<option value='$i' id='select1'>$i</option>"; } ?>
    </select>
  </div>

  <div class="na-row">
    <span class="na-lbl">Nom du stage :</span>
    <input type="text" name="nom_stage" size="30" value="<?php print $nomstage ?>" maxlength="50" class="cc-select">
  </div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGSTAGE45 ?> :</span>
    <input type="text" name="debutdate" size="12" value="<?php print $datedebut ?>" class="cc-select" onKeyPress="onlyChar(event)" maxlength="10">
    <?php include_once("librairie_php/calendar.php"); calendar("id1","document.formulaire.debutdate",$_SESSION["langue"],"0"); ?>
  </div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGSTAGE46 ?> :</span>
    <input type="text" name="findate" size="12" value="<?php print $datefin ?>" class="cc-select" onKeyPress="onlyChar(event)" maxlength="10">
    <?php include_once("librairie_php/calendar.php"); calendar("id2","document.formulaire.findate",$_SESSION["langue"],"0"); ?>
  </div>

  <div class="na-row">
    <span class="na-lbl">Durée hebdomadaire :</span>
    <?php if ($duree_hebdo == "") $duree_hebdo = 35; ?>
    <input type="number" name="duree_hebdo" size="3" value="<?php print $duree_hebdo ?>" class="cc-select" style="width:70px;">
    <span style="font-size:12px;color:#888;">heures</span>
  </div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGELE4 ?> :</span>
    <select name="saisie_classe" class="cc-select">
      <option style="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
      <?php select_classe(); ?>
    </select>
  </div>

  <div class="na-row" style="align-items:flex-start;">
    <span class="na-lbl" style="padding-top:4px;"><?php print LANGTMESS521 ?></span>
    <div style="display:flex;flex-wrap:wrap;gap:10px;font-size:12px;color:#333;">
      <label style="display:flex;align-items:center;gap:4px;cursor:pointer;"><input type="checkbox" name="jourstage[]" value="1" id="j1" checked> L / M</label>
      <label style="display:flex;align-items:center;gap:4px;cursor:pointer;"><input type="checkbox" name="jourstage[]" value="2" id="j2" checked> M / T</label>
      <label style="display:flex;align-items:center;gap:4px;cursor:pointer;"><input type="checkbox" name="jourstage[]" value="3" id="j3" checked> M / W</label>
      <label style="display:flex;align-items:center;gap:4px;cursor:pointer;"><input type="checkbox" name="jourstage[]" value="4" id="j4" checked> J / T</label>
      <label style="display:flex;align-items:center;gap:4px;cursor:pointer;"><input type="checkbox" name="jourstage[]" value="5" id="j5" checked> V / F</label>
      <label style="display:flex;align-items:center;gap:4px;cursor:pointer;"><input type="checkbox" name="jourstage[]" value="6" id="j6" checked> S / S</label>
      <label style="display:flex;align-items:center;gap:4px;cursor:pointer;"><input type="checkbox" name="jourstage[]" value="7" id="j7" checked> D / S</label>
    </div>
  </div>

</div>

<div class="na-foot">
  <script language=JavaScript>buttonMagicSubmit("<?php print LANGSTAGE47 ?>","create");</script>
  <script language=JavaScript>buttonMagicRetour("gestion_stage.php","_parent");</script>
</div>
</form>

<?php
if (isset($_POST["create"])) {
	$cr=stage_ajout($_POST["num"],$_POST["debutdate"],$_POST["findate"],$_POST["saisie_classe"],$_POST["nom_stage"],$_POST["jourstage"],$_POST['duree_hebdo']);
	if ($cr) {
		history_cmd($_SESSION["nom"],"CREATION","date de stage");
		print "<div style='margin:12px 0;padding:10px 14px;background:#e8f5e9;border:1px solid #a5d6a7;border-radius:8px;font-size:13px;color:#2e7d32;font-weight:600;text-align:center;'>";
		print "Stage du ".$_POST["debutdate"]." au ".$_POST["findate"]."<br>";
		print "Classe : ".chercheClasse_nom($_POST["saisie_classe"])." — enregistré.";
		print "</div>";
	}
}
?>

<!-- // fin  -->
</td></tr></table>

<?php
if ($_SESSION['membre'] == "menuadmin") :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
Pgclose();
?>
</BODY></HTML>
