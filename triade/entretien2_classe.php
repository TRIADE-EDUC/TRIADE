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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if ($_SESSION["membre"] == "menupersonnel") {
	if (!verifDroit($_SESSION["id_pers"],"entretien")) {
		accesNonReserveFen();
		exit;
	}
}else{	
	validerequete("menuadmin");
}


if (defined("PASSMODULEINDIVIDUEL")) {
	if (PASSMODULEINDIVIDUEL == "oui") {
		if (empty($_SESSION["adminplus"])) {
			print "<script>";
			print "location.href='./base_de_donne_key.php?key=passmoduleindividuel'";
			print "</script>";
		}
	}
}

$date=date("Y");
$date2=date("Y")-1;

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<?php
$heureDebut="hh:mm";
$heureFin="hh:mm";
$date="";
$idclasse=$_POST["idclasse"];
$nb=$_POST['nb'];

for($i=0;$i<=$nb;$i++) {
	if (isset($_POST["listing_$i"])) $listingeid[]=$_POST["listing_$i"];
}

?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2" colspan=3><b><font   id='menumodule1'><?php print "Enregistrement d'entretien" ?> </b></font></td></tr>
<?php
if(countTriade($listingeid) == 0)  {
	print("<tr id='cadreCentral0' ><td align=center valign=center>".LANGRECH3."</td></tr>");
}else {
?>
<tr  id='cadreCentral0' >
<td >
<form method="post" name="formulaire" onsubmit="return validentretien()" action="entretien3_classe.php">
<?php
	foreach($listingeid as $key=>$value) {
		$liste.=rechercheEleveNomPrenom($value).", ";
		$listeid.=$value."#";
	}
	$listeid=preg_replace('/#$/','',$listeid);
	$listePedago=recupListNomPrenomPedago($identretien);
	$listing="";
	for($j=0;$j<countTriade($listePedago);$j++) {
		$listing.=" - ".civ($listePedago[$j][2])." ".$listePedago[$j][0]." ".$listePedago[$j][1];
	}
	$act="create";
	$textbutton=LANGENR;
?>
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl">Nom prénom :</span>
    <b><?php print $liste ?></b>
    <input type='hidden' value="<?php print $listeid ?>" name="listeeid">
  </div>
  <div class="na-row">
    <span class="na-lbl">Classe :</span>
    <b><?php print chercheClasse_nom($idclasse) ?></b>
    <input type="hidden" name="nomclasse" value="<?php print chercheClasse_nom($idclasse) ?>">
  </div>
  <div class="na-row">
    <span class="na-lbl">Préparation :</span>
    <input type="checkbox" name="preparation" value="1" <?php print $checked ?>> (oui)
  </div>
  <div class="na-row">
    <span class="na-lbl">Le :</span>
    <input type="text" name="saisiedate" onKeyPress="onlyChar(event)" size="10" value="<?php print $date ?>" class="bouton2">
    <?php include_once("librairie_php/calendar.php");
    calendarDim('id2','document.formulaire.saisiedate',$_SESSION["langue"],"1","0"); ?>
    <span style="font-size:12px;color:#555;">à</span>
    <input type="text" name="heuredepart" value="<?php print $heureDebut ?>" onclick="this.value=''" size="5" onKeyPress="onlyChar2(event)" class="bouton2">
  </div>
  <div style="margin:10px 0 4px;">
    <span class="na-lbl">Objet et contenu de l'entretien / Conclusion - Actions</span>
  </div>
  <textarea name="objet" style="width:100%;box-sizing:border-box;font-size:13px;" onkeypress="compter(this,'2000', this.form.CharRestant)" rows='15'><?php print $commentaire ?></textarea>
  <div class="na-row" style="margin-top:4px;">
    <input type="text" name="CharRestant" size="3" disabled="disabled" value="<?php print $len ?>" class="bouton2">
    <span style="font-size:11px;color:#555;">(2000 caractères maximum)</span>
  </div>
  <div class="na-row">
    <span class="na-lbl">Fin entretien :</span>
    <input type="text" name="heurefin" value="<?php print $heureFin ?>" onclick="this.value=''" size="5" onKeyPress="onlyChar2(event)" class="bouton2">
  </div>
  <div style="margin:10px 0 4px;">
    <span class="na-lbl">Equipe Pédagogue :</span>
    <?php print recherche_personne($_SESSION["id_pers"]) ?>
  </div>
  <div style="font-size:11px;color:#555;margin-bottom:6px;"><?php print $listing ?> <span id='listing'></span></div>
  <div class="na-row">
    <select id="pers" name="pers" class="cc-select">
      <option value=''>Autres personnes</option>
      <?php
      print "<optgroup label='Direction'>";
      select_personne_2("ADM",'25');
      print "<optgroup label='Vie Scolaire'>";
      select_personne_2("MVS",'25');
      ?>
    </select>
    <input type="button" value="Ajouter" onclick="ajoutPers()" class="btn-retour">
  </div>
  <input type="hidden" name="idpers" id='idpers' value="<?php print $_SESSION["id_pers"] ?>;">
</div>
<br>
<script language=JavaScript>buttonMagicSubmit("<?php print $textbutton ?>","<?php print $act ?>");</script>
<br>
<?php } ?>
</td></tr></table>
<br>




</form>

<script>
function ajoutPers() {
	var idqui=document.formulaire.pers.options[document.formulaire.pers.options.selectedIndex].value;
	var qui=document.formulaire.pers.options[document.formulaire.pers.options.selectedIndex].text;
	if ((idqui != '') && (idqui != '<?php print $_SESSION["id_pers"] ?>')) {
		document.getElementById('listing').innerHTML+=qui+" - ";
		document.formulaire.idpers.value+=idqui+";"
	}
	

}
</script>




<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       endif ;
?>

<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
