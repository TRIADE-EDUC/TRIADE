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
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGTITRE23 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<div style="font-size:12px;color:#080A66;font-weight:700;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;margin:8px 5px 4px;">Indiquer la liste des tuteurs d'étudiants qui recevront un email. Le tuteur de stage recevra aussi un message.</div>

<form method="post">
<div style="display:flex;gap:6px;flex-wrap:wrap;margin:6px 5px;">
  <button type="submit" name="trie_nom" class="btn-dest">Trier par nom</button>
  <button type="submit" name="trie_date" class="btn-dest">Trier par date</button>
</div>
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl">Classe :</span>
    <select name="saisie_classe" class="cc-select" onchange="this.form.submit()">
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
<form method="post" action="liste_rtd_impr2.php">
<table border="1" bordercolor="#dde0f0" width="100%" style="border-collapse:collapse;">
<?php
$data_2=affRetardNonJustifie2bis($trie,$idclasse);
// elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, nom, elev_id, classe, courrierenvoyer, emailP1, emailP2
for($j=0;$j<countTriade($data_2);$j++) {
	$ideleve=$data_2[$j][0];
	$idmatiere=$data_2[$j][7];
	$courrierenvoyer=$data_2[$j][11];
	$emailP1=$data_2[$j][12];
	$emailP2=$data_2[$j][13];
	if ($data_2[$j][0] == "-4") { continue; }
	if ($idmatiere != null) { $nomMatiere="pour le cours ".chercheMatiereNom($idmatiere); }
	$duree=$data_2[$j][5];
	if ($data_2[$j][5] == "0") { $duree="???"; }

	if ((trim($emailP1) == "") && (trim($emailP2) == "")) {
		$disabled="disabled='disabled'";
		$title=" title=\"Aucun email parent d'indiqu&eacute;\" ";
		$img="<img src='image/commun/alerte.png' $title />";
	}else{
		$title="";
		$disabled="";
		$img="";
	}

	$datedebut=$data_2[$j][2];
	$heure=$data_2[$j][1];
	$classe=chercheClasse_nom($data_2[$j][10]);

	print "<tr id='tr$j' class='tabnormal2' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\">";
	print "<td style='padding:4px 6px;'>".trunchaine(strtoupper(recherche_eleve_nom($ideleve))." ".ucwords(strtolower(recherche_eleve_prenom($ideleve))),15)."</td>";
	print "<td style='padding:4px 6px;'>$classe</td>";
	print "<td style='padding:4px 6px;'>en retard le ".dateForm($data_2[$j][2])." à ".timeForm($data_2[$j][1])." $nomMatiere</td>";
	print "<td style='padding:4px 6px;'><input type='checkbox' $disabled $title name='liste[]' value='$ideleve;$datedebut;$heure;$duree;$nomMatiere' onClick=\"DisplayLigne('tr$j');\">";
	print "$img</td>";
	print "</tr>";
}

if (countTriade($data_2) == "0") {
	print "<tr><td colspan='4' style='padding:8px;text-align:center;color:#888;'>aucune donnée</td></tr>";
}
?>
</table>
<input type="hidden" name="nb" value="<?php print countTriade($data_2) ?>">
<br>
<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:0 5px;">
  <button type="button" class="btn-retour" onclick="open('gestion_abs_retard.php','_self','')">Retour</button>
  <?php if (countTriade($data_2) != "0") { ?>
  <span style="display:flex;"><script language=JavaScript>buttonMagicSubmit("Envoyer email","rien");</script></span>
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
Pgclose();
?>
</BODY></HTML>
