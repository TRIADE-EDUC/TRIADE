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
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/alertify.min.css">
<link rel="stylesheet" href="./librairie_css/alertify.default.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<style>
.cl2-student-card { background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,.08); overflow:hidden; margin-bottom:14px; }
.cl2-student-header { background:linear-gradient(90deg,#080A66,#1e4d8c); color:#fff; padding:8px 16px; font-size:11px; font-weight:700; font-family:Electrolize,'Trebuchet MS',Arial; letter-spacing:.5px; text-transform:uppercase; }
.cl2-student-body { padding:14px 16px; display:flex; gap:16px; align-items:flex-start; }
.cl2-student-info { font-size:13px; color:#333; display:flex; flex-direction:column; gap:4px; }
.cl2-student-info strong { color:#080A66; }
.cl2-payments-card { background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,.08); overflow:hidden; margin-bottom:14px; }
.cl2-payments-header { background:linear-gradient(90deg,#080A66,#1e4d8c); color:#fff; padding:8px 16px; font-size:11px; font-weight:700; font-family:Electrolize,'Trebuchet MS',Arial; letter-spacing:.5px; text-transform:uppercase; }
.cl2-btn-row { padding:10px 16px; display:flex; gap:10px; align-items:center; background:#f5f7ff; border-top:1px solid #e8eaf6; }
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print "Modifier un encaissement" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td style="padding:8px 4px;">

<?php
if (!isset($_GET["ideleve"])) {
	$nomEleve=$_POST["saisie_nom_eleve"];
	$sql="SELECT elev_id,nom,prenom,classe FROM  {$prefixe}eleves  WHERE  nom='$nomEleve' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	if (countTriade($data) > 1) {
		print "<table class='cc-table' width='100%' style='border-collapse:collapse;'>";
		print "<thead><tr class='cc-thead-row'>";
		print "<th class='cc-th'>Nom Pr&eacute;nom</th><th class='cc-th'>Classe</th>";
		print "<th class='cc-th' style='text-align:center;'>S&eacute;lectionner</th>";
		print "</tr></thead><tbody>";
		for($i=0;$i<countTriade($data);$i++) {
			print "<tr class='cc-tr-data'>";
			print "<td class='cc-td'>".$data[$i][1]." ".$data[$i][2]."</td>";
			print "<td class='cc-td'>".chercheClasse_nom($data[$i][3])."</td>";
			print "<td class='cc-td' style='text-align:center;'><input type='button' onclick=\"open('compta_modif.php?ideleve=".$data[$i][0]."','_parent','')\" class='btn-primary' value='S&eacute;lectionner' /></td>";
			print "</tr>";
		}
		print "</tbody></table>";
	} else {
		$ideleve=$data[0][0];
	}
} else {
	$ideleve=$_GET["ideleve"];
}

if ($ideleve > 0) {
	$nomeleve=recherche_eleve_nom($ideleve);
	$prenomeleve=recherche_eleve_prenom($ideleve);
	$idclasse=chercheClasseEleve($ideleve);
	$classe=chercheClasse_nom($idclasse);
?>

<div class="cl2-student-card">
  <div class="cl2-student-header">Informations &eacute;l&egrave;ve</div>
  <div class="cl2-student-body">
    <div>
      <img src="image_trombi.php?idE=<?php print $ideleve ?>" border=0 style="border-radius:6px;">
    </div>
    <div class="cl2-student-info">
      <div><strong>Nom :</strong> <?php print $nomeleve ?></div>
      <div><strong>Pr&eacute;nom :</strong> <?php print $prenomeleve ?></div>
      <div><strong>Classe :</strong> <?php print ucwords($classe) ?></div>
      <div><strong>Boursier :</strong> <?php print etatBoursier($ideleve) ?> (<?php print montantBourse($ideleve) ?>)</div>
      <div><strong>Indemnit&eacute; de stage :</strong> <?php print montantIndemniteStage($ideleve) ?></div>
    </div>
  </div>
</div>

<div class="cl2-payments-card">
  <div class="cl2-payments-header">Versements</div>
  <table class="cc-table" width="100%" style="border-collapse:collapse;">
  <thead>
    <tr class="cc-thead-row">
      <th class="cc-th">Date</th>
      <th class="cc-th">Versement</th>
      <th class="cc-th">Montant</th>
      <th class="cc-th">Mode paiement</th>
      <th class="cc-th" style="width:80px; text-align:center;">Modifier</th>
    </tr>
  </thead>
  <tbody>
<?php
	$data=listVersement($ideleve); // ideleve,idversement,montantvers,datevers,modepaiement
	for($i=0;$i<countTriade($data);$i++) {
		$nomVersement=chercheNomVersement($data[$i][1]);
		if ($nomVersement != "") {
			print "<tr class='cc-tr-data'>";
			print "<td class='cc-td'>&nbsp;".dateForm($data[$i][3])."&nbsp;</td>";
			print "<td class='cc-td'>&nbsp;".$nomVersement."</td>";
			print "<td class='cc-td'>&nbsp;".preg_replace('/ /','&nbsp;',affichageFormatMonnaie($data[$i][2]))."</td>";
			print "<td class='cc-td'>&nbsp;".nl2br($data[$i][4])."</td>";
			print "<td class='cc-td' style='text-align:center;'><input type=button value='Modifier' class='btn-primary' onclick=\"open('compta_modif.php?idvers=".$data[$i][1]."&date=".$data[$i][3]."&ideleve=".$ideleve."','_parent','')\" /></td>";
			print "</tr>";
		}
	}
?>
  </tbody>
  </table>
  <div class="cl2-btn-row">
    <script language=JavaScript>buttonMagicRetour('compta_liste.php','_self')</script>
  </div>
</div>

<?php
}
?>

     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
            print "</SCRIPT>";

       endif ;
     ?>
   </BODY></HTML>
