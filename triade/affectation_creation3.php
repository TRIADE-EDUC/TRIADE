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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content ="no-cache">
<META http-equiv="pragma" content ="no-cache">
<META http-equiv="expires" content ="-1">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/prevwong/drooltip.js@master/package/css/drooltip.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg) { alertify.error(msg); };</script>
<style>
#aff-table select {
  width:100%; box-sizing:border-box;
  border:1px solid #c5cae9; border-radius:4px;
  padding:2px 4px; font-size:11px; background:#fff;
}
#aff-table input[type=text] {
  width:100%; box-sizing:border-box;
  border:1px solid #c5cae9; border-radius:4px;
  padding:2px 4px; font-size:11px; text-align:center;
}
#aff-table input[type=checkbox] { accent-color:#080A66; width:15px; height:15px; cursor:pointer; }
#aff-table td { vertical-align:middle; padding:4px 5px; }
</style>
<script src="https://cdn.jsdelivr.net/gh/prevwong/drooltip.js@master/package/js/build/drooltip.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_affectation.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"]?></title>
</head>
<body id='bodyfond2' onScroll="openPopup()" >
<?php
include_once("librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();

$cid=$_GET["saisie_classe_envoi"];
$anneeScolaire=$_GET["anneeScolaire"];
$dataClasse=chercheClasse($_GET["saisie_classe_envoi"]);
$nom_classe=$dataClasse[0][1];
$matGroup=matGroup($nom_classe,$anneeScolaire);

$sql=<<<SQL
SELECT
	code_mat,
	libelle,
	sous_matiere
FROM
	{$prefixe}matieres
WHERE
	offline = '0'
ORDER BY
	libelle
SQL;

$cursor=execSql($sql);
$data=chargeMat($cursor);
freeResult($cursor);
for($l=0;$l<countTriade($data);$l++){
	for($c=0;$c<countTriade($data);$c++){
		if(empty($data[$l][2])):
			$bool=0;
		else:
			$bool=true;
		endif;
		$matMat[$l][0]=$data[$l][0].":".$bool;
		$sl=trim($data[$l][1])." ".trim($data[$l][2]);
		$matMat[$l][1]=$sl;
	}
}
?>

<form method="post" onsubmit="return valide();" action="affectation_creation4.php" name="formulaire">

<table border="0" cellpadding="3" cellspacing="1" width="100%" style="table-layout:fixed">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'><?php print LANGPER15?> <font id="color2"><?php print $nom_classe?></font></font></b>
  &nbsp;
  <b><font id='menumodule1'>pour l'ann&eacute;e scolaire </font><font id="color2"><?php print $anneeScolaire?></font></b>
</td></tr>
<tr id='cadreCentral0'>
<td>
<div style="overflow:hidden">

<div style="overflow-x:auto;min-width:0;padding:8px 4px">
<table id="aff-table" class="table" style="min-width:960px;margin:0">
  <thead>
    <tr>
      <th class="cc-th" style="min-width:44px"><?php print LANGPER16?></th>
      <th class="cc-th" style="min-width:150px"><?php print LANGPER17?></th>
      <th class="cc-th" style="min-width:150px"><?php print LANGPER18?></th>
      <th class="cc-th" style="min-width:46px"><?php print LANGPER19?></th>
      <th class="cc-th" style="min-width:110px"><?php print LANGPER20?></th>
      <th class="cc-th" style="min-width:70px"><?php print LANGPER21bis?></th>
      <th class="cc-th" style="min-width:46px">Visu.<i>*</i></th>
      <th class="cc-th" style="min-width:56px">Visu&nbsp;BTS&nbsp;Blanc<i>***</i></th>
      <th class="cc-th" style="min-width:56px">Nbr&nbsp;h.<i>**</i></th>
      <th class="cc-th" style="min-width:46px">ECTS</th>
      <th class="cc-th" style="min-width:56px">Info sem. <i class="bi bi-info-circle dtt" title="Numéro de semestre (1 à 10)" style="font-size:11px;color:#3949ab;cursor:pointer"></i></th>
      <th class="cc-th" style="min-width:56px">Coef&nbsp;Certif</th>
      <th class="cc-th" style="min-width:66px">Note&nbsp;plancher</th>
    </tr>
  </thead>
  <tbody>
  <?php for ($a=0;$a<$_GET["saisie_nb_matiere"];$a++) { ?>
    <tr class="cc-tr-data">
      <td style="text-align:center">
        <input type="text" name="ordre" value="<?php print $a?>" size="3" onfocus="this.blur()">
      </td>
      <td><?php $nameSelectMat="saisie_matiere_".$a; print(selectHtml($nameSelectMat,1,false,$matMat)); ?></td>
      <td>
        <select name="saisie_prof_<?php print $a?>">
          <option value="0" style="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX?></option>
          <?php select_personne_2('ENS','30'); ?>
        </select>
      </td>
      <td><input type="text" name="saisie_coef_<?php print $a?>" size="2"></td>
      <td><?php $nameSelectGrp="saisie_groupe_".$a; print(selectHtml($nameSelectGrp,1,false,$matGroup)); ?></td>
      <td>
        <select name="saisie_langue_<?php print $a?>">
          <option value=""><?php print LANGCHOIX?></option>
          <option value="LV1">LV1</option>
          <option value="LV2">LV2</option>
          <option value="LV3">LV3</option>
          <option value="LV4">LV4</option>
          <option value="OPT1">OPT1</option>
          <option value="OPT2">OPT2</option>
          <option value="OPT3">OPT3</option>
          <option value="OPT4">OPT4</option>
          <option value="DP3">DP3</option>
        </select>
      </td>
      <td style="text-align:center"><input type="checkbox" name="saisie_visubull_<?php print $a?>" value="1" checked="checked"></td>
      <td style="text-align:center"><input type="checkbox" name="saisie_visubull_btsblanc_<?php print $a?>" value="1"></td>
      <td><input type="text" name="saisie_nbheure_<?php print $a?>" value="" size="3"></td>
      <td><input type="text" name="saisie_ects_<?php print $a?>" value="" size="3"></td>
      <td>
        <select name="info_semestre_<?php print $a?>">
          <option value="0"></option>
          <?php for($s=1;$s<=10;$s++) print "<option value='$s'>$s</option>"; ?>
        </select>
      </td>
      <td><input type="text" size="2" name="saisie_coef_certif_<?php print $a?>"></td>
      <td><input type="text" size="2" name="saisie_note_planche_<?php print $a?>"></td>
    </tr>
  <?php } ?>
  </tbody>
</table>
</div>

<input type="hidden" name="saisie_nb_matiere"   value="<?php print $a-1?>">
<input type="hidden" name="saisie_classe_envoi" value="<?php print $_GET["saisie_classe_envoi"]?>">
<input type="hidden" name="anneeScolaire"       value="<?php print $anneeScolaire?>">
<input type="hidden" name="saisie_tri"          value="<?php print $_GET["tri"]?>">

<div style="font-style:italic;font-size:11px;color:#888;padding:4px 8px 8px">
  * Visu. : Visualiser au sein du bulletin &nbsp;/&nbsp; ** Nombre d'heure annuelle &nbsp;/&nbsp; *** Visu. : Visualiser au sein du bulletin AFTEC BTS BLANC
</div>

<div style="display:flex;gap:8px;padding:6px 8px 10px">
  <script language=JavaScript>buttonMagic("<?php print LANGBT20?>","./affectation_creation.php","_top","",";parent.window.close();");</script>
  <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT21?>","rien");</script>
</div>

</div>
</td></tr></table>
</form>

<script>new Drooltip({element:".dtt",position:"top",animation:"fade"});</script>
<script language=JavaScript>
function Validselect(item){
 if (item == 0) {
        return (false) ;
 }else {
        return (true) ;
        }
}

function ValidLongueur(item,len) {
   drapeau = 1;
   return (item.length >= len);
}

function error5(elem, text) {
   if (flag) return;
   window.alert(text);
   elem.select();
   elem.focus();
   flag = true;
}

function error6(text) {
   if (flag) return;
   window.alert(text);
   flag=true;
}

function valide() {
	flag=false;
	var nbmatiere=<?php print $a?>;
	nbmatiere=nbmatiere * 13;

	for (i=1;i<=nbmatiere;i++) {
		if (!Validselect(document.formulaire.elements[i].options.selectedIndex)) {
                error6(langfunc23);
       		}
		i=i+1;
		if (!Validselect(document.formulaire.elements[i].options.selectedIndex)) {
                error6(langfunc47);
       		}
		i=i+1;
		if (!ValidLongueur(document.formulaire.elements[i].value,1)) {
                	error5(document.formulaire.elements[i],langfunc48); }
        	if(isNaN(document.formulaire.elements[i].value)) {
                	error5(document.formulaire.elements[i],langfunc49); }
		i=i+10;
	}

	return !flag;
}
</script>
</BODY></HTML>
