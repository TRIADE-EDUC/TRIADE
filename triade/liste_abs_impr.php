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
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print $_SESSION['nom']." ".$_SESSION['prenom'] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");

if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
	$profpclasse=$_SESSION["profpclasse"];
	validerequete("menuprof");
}else{
	validerequete("2");
}

$filtreABS="NonJustifie";
if (isset($_POST["ABSNJ"])) { $filtreABS="NonJustifie"; }
if (isset($_POST["ABSJ"])) { $filtreABS="Justifie"; }

$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>
<?php if ($filtreABS == "NonJustifie") { print LANGABS5; }else{ print "Absences justifi&eacute;es"; } ?>
</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<div style="font-size:12px;color:#080A66;font-weight:700;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;margin:8px 5px 4px;">Indiquer la liste des personnes qui recevront un email.</div>

<form method="post">
<div style="display:flex;gap:6px;flex-wrap:wrap;margin:6px 5px;">
  <button type="submit" name="trie_nom" class="btn-dest">Trier par nom</button>
  <button type="submit" name="trie_date" class="btn-dest">Trier par date</button>
  <?php if (isset($_POST["ABSJ"])) { ?>
  <button type="submit" name="ABSNJ" class="btn-dest">Absences non justifi&eacute;es</button>
  <?php }else{ ?>
  <button type="submit" name="ABSJ" class="btn-dest">Absences justifi&eacute;es</button>
  <?php } ?>
</div>
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl">Classe :</span>
    <select name="saisie_classe" class="cc-select" onchange="this.form.submit()">
      <option value='' style='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
      <?php if ((isset($_POST["saisie_classe"])) && ($_POST["saisie_classe"] != 'tous')) {
          print "<option value='".$_POST["saisie_classe"]."' selected>".trunchaine(chercheClasse_nom($_POST["saisie_classe"]),35)."</option>";
      } ?>
      <?php select_classe2(35); ?>
    </select>
  </div>
</div>
</form>

<?php
if (isset($_POST["trie_nom"])) { $trie='nom'; }
if (isset($_POST["trie_date"])) { $trie='date'; }
if (isset($_POST["trie_classe"])) { $trie='classe'; }
if (isset($_POST["saisie_classe"])) { $idclasse=$_POST["saisie_classe"]; }
?>

<?php if (!empty($idclasse)) { ?>
<form method="post" action="liste_abs_impr2.php" name="formulaire">
<table border="1" bordercolor="#dde0f0" width="100%" style="border-collapse:collapse;">
<?php
if ($filtreABS == "NonJustifie") {
	$data_2=affAbsNonJustif2bis($trie,$idclasse);
}else{
	$data_2=affAbsJustif2bis($trie,$idclasse);
}
// elev_id, date_ab, date_saisie, origin_saisie, duree_ab, date_fin, motif, duree_heure, id_matiere, time, nom, elev_id, classe, courrierenvoyer, email, email_resp_2, email_eleve
for($j=0;$j<countTriade($data_2);$j++) {
	$ideleve=$data_2[$j][0];
	$idmatiere=$data_2[$j][7];
	$duree=$data_2[$j][4];
	$time=$data_2[$j][9];
	$courrierenvoyer=$data_2[$j][13];
	$emailP1=$data_2[$j][14];
	$emailP2=$data_2[$j][15];
	$email_eleve=$data_2[$j][16];

	if ($duree == "-1") { $duree=$data_2[$j][7]." heure(s)"; }else{ $duree.=" jour(s)"; }
	if ($data_2[$j][0] == "-4") { continue; }
	if ($data_2[$j][4] == "0") { $duree="???"; }
	$datedebut=$data_2[$j][1];
	$datefin=$data_2[$j][5];
	$classe=chercheClasse_nom($data_2[$j][12]);

	if (trim($email_eleve) == "") {
		$disabledE="disabled='disabled'";
		$titleE=" title=\"Aucun email &eacute;tudiant indiqu&eacute;\" ";
		$imgE="<img src='image/commun/alerte.png' $titleE />";
	}else{
		$titleE="title=\"$email_eleve\"";
		$disabledE="";
		$imgE="";
	}

	if ((trim($emailP1) == "") && (trim($emailP2) == "")) {
		$disabledT="disabled='disabled'";
		$titleT=" title=\"Aucun email tuteur indiqu&eacute;\" ";
		$imgT="<img src='image/commun/alerte.png' $titleT />";
	}else{
		$titleT="title=\"$emailP1 / $emailP2\"";
		$disabledT="";
		$imgT="";
	}

	$emailtuteurstage=recupEmailTuteurStage($ideleve);
	if ($emailtuteurstage == "") {
		$disabledTT="disabled='disabled'";
		$titleTT=" title=\"Aucun email tuteur stage indiqu&eacute;\" ";
		$imgTT="<img src='image/commun/alerte.png' $titleTT />";
	}else{
		$disabledTT="";
		$titleTT=" title=\"$emailtuteurstage\" ";
		$imgTT="";
	}

	print "<tr id='tr$j' class='tabnormal2' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\">";
	print "<td style='padding:4px 6px;' valign='top'>".trunchaine(strtoupper(recherche_eleve_nom($ideleve))." ".ucwords(strtolower(recherche_eleve_prenom($ideleve))),20)."</td>";
	print "<td style='padding:4px 6px;' valign='top'>$classe</td>";
	print "<td style='padding:4px 6px;' valign='top'>absent le ".dateForm($data_2[$j][1])." durant ".$duree."</td>";
	print "<td style='padding:4px 6px;' valign='top'><table><tr>";
	print "<td style='padding:2px 4px;' valign='top'>Email&nbsp;Etudiant&nbsp;<input $disabledE type='checkbox' name='liste[]' $titleE value='$ideleve:$datedebut:$datefin:$duree:$time:eleve' onClick=\"DisplayLigne('tr$j');\" id='check$j'>";
	$j++;
	print "$imgE</td></tr><tr>";
	print "<td style='padding:2px 4px;' valign='top'>Email&nbsp;Parent&nbsp;<input $disabledT type='checkbox' name='liste[]' $titleT value='$ideleve:$datedebut:$datefin:$duree:$time:tuteur' onClick=\"DisplayLigne('tr$j');\" id='check$j'>";
	$j++;
	print "$imgT</td></tr><tr>";
	$j++;
	print "$imgTT</td></tr></table></td>";
	print "</tr>";
}

if (countTriade($data_2) == "0") {
	print "<tr><td colspan='4' style='padding:8px;text-align:center;color:#888;'>aucune donnée</td></tr>";
}else{
	print "<tr><td></td><td></td><td style='padding:4px 6px;text-align:right;'>Toutes les cases :</td><td style='padding:4px 6px;'><input type='checkbox' onclick=\"cocheCase()\" name='allcase' /></td></tr>";
}
?>
</table>
<input type="hidden" name="nb" value="<?php print countTriade($data_2) ?>">
<input type="hidden" name="filtreABS" value="<?php print $filtreABS ?>">
<br>
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl">Email CC :</span>
    <input type="text" name="emailcc" class="cc-select" style="width:200px;" value="<?php print isset($_COOKIE['emailcc']) ? $_COOKIE['emailcc'] : '' ?>">
  </div>
</div>
<br>
<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:0 5px;">
  <button type="button" class="btn-retour" onclick="open('gestion_abs_retard.php','_self','')">Retour</button>
  <?php if (countTriade($data_2) != "0") { ?>
  <span style="display:flex;"><script language=JavaScript>buttonMagicSubmit("Envoyer email","rien");</script></span>
  <?php } ?>
</div>
<br><br>
</form>

<script>
function cocheCase() {
	if (document.formulaire.allcase.checked == true) {
		for(var i=0;i<<?php print $j ?>;i++) {
			if (document.getElementById('check'+i).disabled == false) document.getElementById('check'+i).checked=true;
		}
	}else{
		for(var i=0;i<<?php print $j ?>;i++) {
			document.getElementById('check'+i).checked=false;
		}
	}
}
</script>

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
Pgclose();
?>
</BODY></HTML>
