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
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta charset="UTF-8">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/alertify.min.css">
<style>#coulBar0 { background-image: none; }</style>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/ajax-verif-champs.js"></script>
<script language="JavaScript" src="./librairie_js/prototype.js"></script>
<script language="JavaScript" src="./librairie_js/scriptaculous.js"></script>
<script language="JavaScript" src="./librairie_js/xorax_serialize.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx = cnx();

$date  = date("Y");
$date2 = date("Y") - 1;
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<?php
if (isset($_POST["create"])) {
	ajoutEntretienEnseignant($_POST["id_liste_eleve"], $_POST["idpers"], $_POST["heure"]);
}

if (isset($_GET["eid"]))     { $eid = $_GET["eid"]; }
if (isset($_POST["idpers"])) { $eid = $_POST["idpers"]; }
if ($eid) {
	$sql  = "SELECT pers_id,nom,prenom,prenom2,type_pers,civ,photo,email FROM {$prefixe}personnel WHERE pers_id='$eid'";
	$res  = execSql($sql);
	$data = chargeMat($res);
	$nomProf    = $data[0][1];
	$prenomProf = $data[0][2];
}
?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Temps d'accompagnement d'un enseignant</font></b></td></tr>
<tr id='cadreCentral0'><td>

<div style="padding:16px;">
<br>

<!-- Saisie accompagnement -->
<div class="card">
  <div class="card-header">
    <span class="card-title">Saisie d'accompagnement</span>
    <span style="font-size:12px;font-weight:700;color:#080A66;"><?php print ucwords($data[0][1]." ".$data[0][2]) ?></span>
  </div>
  <div class="card-body" style="padding:0;">

    <!-- Info enseignant -->
    <div style="display:flex;gap:16px;align-items:flex-start;padding:12px 16px;background:#f5f7ff;border-bottom:1px solid #eef0f8;">
      <img src="image_trombi.php?idP=<?php print $eid ?>" border="0" style="border-radius:6px;flex-shrink:0;">
      <div style="font-size:12px;">
        <div style="font-weight:700;color:#080A66;margin-bottom:4px;"><?php print ucwords($data[0][1]." ".$data[0][2]) ?></div>
        <div style="color:#555;margin-bottom:4px;">Classe : <b><?php print ucwords($data[0][3]) ?></b></div>
        <?php $lvo = chercheLvo($eid); ?>
        <div style="color:#777;font-size:11px;">Lv1/Spé : <a href="#" title="<?php print $lvo[0][0] ?>"><?php print trunchaine($lvo[0][0],40) ?></a></div>
        <div style="color:#777;font-size:11px;">Lv2/Spé : <a href="#" title="<?php print $lvo[0][1] ?>"><?php print trunchaine($lvo[0][1],40) ?></a></div>
        <div style="color:#777;font-size:11px;">Option : <a href="#" title="<?php print $lvo[0][2] ?>"><?php print trunchaine($lvo[0][2],40) ?></a></div>
      </div>
    </div>

    <form method="post" name="formulaire" action="gestion_entretient_enseignant_recap.php" onsubmit="return validEntretienprof()">
      <div class="form-row" style="padding:8px 16px;">
        <label class="form-label">Nombre d'heures</label>
        <input type="text" name="heure" class="form-control" style="max-width:100px;" value="hh:mm" onclick="this.value=''" onKeyPress="onlyChar2(event)">
      </div>
      <div class="form-row" style="padding:8px 16px;">
        <label class="form-label">Classe de l'élève</label>
        <select name="saisie_classe" onChange="afficheEleve(this.value)" class="form-control" style="max-width:200px;">
          <option id="select0" value=""><?php print LANGCHOIX ?></option>
          <?php select_classe(); ?>
        </select>
      </div>
      <div class="form-row" style="padding:8px 16px;">
        <label class="form-label">Nom de l'élève</label>
        <select name="saisie_eleve" id="saisie_eleve" class="form-control" style="max-width:260px;"></select>
        <button type="button" class="btn btn-secondary" onclick="ajout()">Ajouter</button>
      </div>
      <div style="padding:8px 16px;font-size:12px;color:#333;">
        Liste des élèves : <span id="liste_eleve" style="color:#080A66;font-weight:600;"></span>
      </div>
      <input type="hidden" value="<?php print $eid ?>" name="idpers">
      <input type="hidden" name="id_liste_eleve" id="id_liste_eleve">
      <div style="padding:12px 16px;border-top:1px solid #eef0f8;">
        <script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","create");</script>
      </div>
    </form>
  </div>
</div>

<br>

<!-- Historique accompagnements -->
<div class="card">
  <div class="card-header"><span class="card-title">Temps d'accompagnement effectué</span></div>
  <div class="card-body" style="padding:12px 16px;">
<?php
$data = listingEntretienEnseignantParReferenceViaIdprof($eid);
for ($i = 0; $i < countTriade($data); $i++) {
	$duree     = timeForm($data[$i][1]);
	$date      = dateForm($data[$i][3]);
	$reference = $data[$i][4];

	$dataDetail = listingEntretienEnseignantViaReference($reference);
	$listing = "";
	for ($j = 0; $j < countTriade($dataDetail); $j++) {
		$idclasse  = $dataDetail[$j][2];
		$nomEleve  = rechercheEleveNomPrenom($dataDetail[$j][4]);
		$classe    = chercheClasse_nom($idclasse);
		$listing  .= "- $nomEleve ($classe)<br>";
	}

	print "<div style='display:flex;align-items:center;gap:10px;padding:6px 0;border-bottom:1px solid #eef0f8;font-size:12px;'>";
	print "<span style='color:#333;'>Le <b>$date</b> — durée : <b>$duree</b></span>";
	print "<span class='htip-wrap' style='flex-shrink:0;'>[<a href='#' style='font-size:11px;'>participants</a><span class='htip' style='min-width:200px;text-align:left;font-weight:normal;'>$listing</span>]</span>";
	print "<a href='gestion_entretient_enseignant?supp=$reference' title='Supprimer' style='margin-left:auto;'><img src='image/commun/trash.png' border='0'></a>";
	print "</div>";
}
if (countTriade($data) == 0) {
	print "<div style='color:#888;font-size:12px;'>Aucun accompagnement enregistré.</div>";
}
?>
  </div>
</div>

</div>

</td></tr></table>

<script>
function ajout() {
	var select = document.getElementById("saisie_eleve");
	var choice = select.selectedIndex;
	var valeur = select.options[choice].value;
	var texte  = select.options[choice].text;
	document.getElementById('id_liste_eleve').value += valeur + ",";
	document.getElementById('liste_eleve').innerHTML += texte + " / ";
}
</script>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php Pgclose(); ?>
</BODY>
</HTML>
