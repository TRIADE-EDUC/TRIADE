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
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/alertify.min.css">
<style>#coulBar0 { background-image: none; }</style>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_comptaModele.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_comptaModeleSupp.js"></script>
<script language='JavaScript'>
function newmodele() {
	resultat = document.formulaire.listemodele.options[document.formulaire.listemodele.options.selectedIndex].value;
	resultat = resultat.substr(0, 19);
	if (document.formulaire.listemodele.options[document.formulaire.listemodele.options.selectedIndex].value != "-1") {
		document.getElementById("nommodele").style.visibility = 'hidden';
	} else {
		document.getElementById("nommodele").style.visibility = 'visible';
	}
}
</script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print "Gestion des versements" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx = cnx();
$option = "";

if (isset($_POST["create"])) {
	enrConfigVersementModele($_POST['listemodele'], $_POST["nameversement"], $_POST["montantversement"], $_POST["dateversement"], $_POST['nommodele']);
	if ($_POST['listemodele'] > 0) {
		$option = "<option id='select1' value='".$_POST['listemodele']."'>".chercheNomModeleVers($_POST['listemodele'])."</option>";
	} else {
		$option = "<option id='select1' value='".chercheIdConfigVersModele($_POST['nommodele'])."'>".$_POST['nommodele']."</option>";
	}
}
?>

<div style="padding:16px;">
<br>

<!-- Formulaire ajout modèle -->
<div class="card">
  <div class="card-header">Nouveau versement modèle</div>
  <div class="card-body" style="padding:0;">
    <form method="post" name="formulaire" action="comptaconfigmodele.php">
      <div class="form-row">
        <label class="form-label">Nom du modèle</label>
        <select name="listemodele" class="form-control" style="max-width:200px;" onChange='newmodele()'>
          <?php print $option ?>
          <option id='select1'><?php print LANGCHOIX ?></option>
          <option id='select1' value="-1"><?php print "Nouveau" ?></option>
          <?php listingModele() ?>
        </select>
        <input type="text" name='nommodele' id='nommodele' class="form-control" style="max-width:140px;visibility:hidden;" maxlength="15">
      </div>
      <div class="form-row">
        <label class="form-label">Intitulé du versement</label>
        <input type="text" name="nameversement" class="form-control" style="max-width:260px;" maxlength="30">
      </div>
      <div class="form-row">
        <label class="form-label">Montant du versement</label>
        <input type="text" name="montantversement" class="form-control" style="max-width:120px;">
      </div>
      <div class="form-row">
        <label class="form-label">Date d'échéance</label>
        <input type="text" name="dateversement" value="" class="form-control" style="max-width:120px;" onKeyPress="onlyChar(event)">
        <?php include_once("librairie_php/calendar.php"); calendarMoiAnnee('id1','document.formulaire.dateversement',$_SESSION["langue"],"0"); ?>
      </div>
      <div style="padding:10px 14px;">
        <script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","create");</script>
        <script language=JavaScript>buttonMagicRetour2("comptaconfig.php","_parent","Quitter")</script>
      </div>
    </form>
  </div>
</div>

<!-- Légende -->
<div style="font-size:11px;color:#666;margin-bottom:8px;display:flex;gap:14px;align-items:center;flex-wrap:wrap;">
  <span><img src="image/commun/update1.png"> Modification</span>
  <span><img src="image/commun/export.png"> Sauvegarde</span>
  <span><img src="image/commun/trash.png"> Suppression</span>
</div>

<script>
function modif(id,dateM,montant,libelle) {
	document.getElementById('date'+id).innerHTML="<input type='text' name='newdate' value='"+dateM+"' size='9' onchange='varnewdate"+id+"=this.value;' />";
	document.getElementById('montant'+id).innerHTML="<input type='text' name='newmontant' value='"+montant+"' size='4'  onchange='varnewmontant"+id+"=this.value'/>";
	document.getElementById('libelle'+id).innerHTML="<input type='text'  maxlength='30' name='newlibelle' value=\""+libelle+"\" size='35' onchange='varnewlibelle"+id+"=this.value'/>";
	document.getElementById('enr'+id).style.visibility='visible';
}
</script>

<?php
print "<table class='table' style='width:100%;border-collapse:collapse;'>";
$dataModele = affModele();
$j = 0;
foreach ($dataModele as $idmodele => $nommodele) {
	$j++;
	print "<tr><td colspan='4' style='background:#f0f2fa;padding:6px 10px;font-weight:700;color:#080A66;border-bottom:1px solid #dde0f0;'>Modèle : ".$nommodele;
	print "<span id='aff$j'></span></td></tr>";
	print "<tr>
		<td style='width:70px;border-bottom:1px solid #eef0f8;'></td>
		<td class='cc-th' style='border-bottom:1px solid #eef0f8;'>Libellé du versement</td>
		<td class='cc-th' style='width:10%;border-bottom:1px solid #eef0f8;'>&nbsp;Date d'échéance&nbsp;</td>
		<td class='cc-th' style='width:5%;border-bottom:1px solid #eef0f8;'>&nbsp;Montant&nbsp;</td></tr>";
	$data = recupConfigVersementModele($idmodele);
	for ($i = 0; $i < countTriade($data); $i++) {
		$libelle = addslashes($data[$i][3]);
		$montant = $data[$i][4];
		$id      = $data[$i][0];
		$date    = dateForm($data[$i][5]);
		print "<script>var varnewlibelle$j$i=\"$libelle\"; var varnewmontant$j$i=\"$montant\"; var varnewdate$j$i=\"$date\";</script>";
		print "<tr id='tr$j$i'><form>
			<td style='width:80px;white-space:nowrap;text-align:right;border-bottom:1px solid #eef0f8;padding:6px 8px;'>
				<span id='enr$j$i' style='visibility:hidden'><a href=\"javascript:enrModif('$id',varnewlibelle$j$i,varnewmontant$j$i,varnewdate$j$i,'aff$j','enr$j$i')\" title=\"".LANGENR."\"><img src='image/commun/export.png' border='0'></a></span>
				<span id='modif$j$i'><a href=\"javascript:modif('$j$i','$date','$montant','$libelle')\"><img src='image/commun/update1.png' border='0'></a></span>
				<span id='supp$j$i'><a href=\"javascript:suppModif('$id','enr$j$i','supp$j$i','modif$j$i','tr$j$i','aff$j')\" title=\"".LANGacce5."\"><img src='image/commun/trash.png' border='0'></a></span>
			</td>
			<td style='border-bottom:1px solid #eef0f8;padding:6px 8px;color:#333;'><img src='image/on1.gif' width='8' height='8'>&nbsp;<span id='libelle$j$i'>$libelle</span></td>
			<td style='width:5%;border-bottom:1px solid #eef0f8;padding:6px 8px;color:#555;'>&nbsp;<span id='date$j$i'>$date</span>&nbsp;</td>
			<td style='width:5%;border-bottom:1px solid #eef0f8;padding:6px 8px;color:#555;'>&nbsp;<span id='montant$j$i'>".preg_replace('/ /', '&nbsp;', affichageFormatMonnaie($montant))."</span>&nbsp;</td>
		</form></tr>";
	}
	print "<tr><td height='12' colspan='4'></td></tr>";
}
print "</table>";
?>

</div>

<br />
</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
else :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
endif;
?>
</BODY></HTML>
