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
if (isset($_POST["anneeScolaire"])) setcookie("anneeScolaire",$_POST["anneeScolaire"],time()+36000*24*30);
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
.ca2-student-card { background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,.08); overflow:hidden; margin-bottom:14px; }
.ca2-student-header { background:linear-gradient(90deg,#080A66,#1e4d8c); color:#fff; padding:8px 16px; font-size:11px; font-weight:700; font-family:Electrolize,'Trebuchet MS',Arial; letter-spacing:.5px; text-transform:uppercase; }
.ca2-student-body { padding:14px 16px; display:flex; gap:16px; align-items:flex-start; }
.ca2-student-info { font-size:13px; color:#333; display:flex; flex-direction:column; gap:4px; }
.ca2-student-info strong { color:#080A66; }
.ca2-year-bar { padding:10px 14px; background:#f5f7ff; border-bottom:1px solid #e8eaf6; display:flex; align-items:center; gap:8px; }
.ca2-year-bar label { font-size:13px; font-weight:600; color:#1a237e; white-space:nowrap; }
.ca2-year-bar select { padding:4px 10px; border:1px solid #c5cae9; border-radius:6px; font-size:13px; background:#fff; }
.ca2-form-card { background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,.08); overflow:hidden; margin-bottom:14px; }
.ca2-form-header { background:linear-gradient(90deg,#080A66,#1e4d8c); color:#fff; padding:8px 16px; font-size:11px; font-weight:700; font-family:Electrolize,'Trebuchet MS',Arial; letter-spacing:.5px; text-transform:uppercase; }
.ca2-fields { padding:18px 20px 8px; display:grid; grid-template-columns:1fr 1fr; gap:14px 20px; }
.ca2-field { display:flex; flex-direction:column; gap:5px; }
.ca2-field.full { grid-column:1/-1; }
.ca2-field label { font-size:11px; font-weight:700; color:#5c6bc0; text-transform:uppercase; letter-spacing:.4px; }
.ca2-field input[type=text], .ca2-field select, .ca2-field textarea { padding:8px 10px; border:1px solid #e0e3ef; border-radius:7px; font-size:13px; font-family:inherit; color:#333; background:#fafbff; outline:none; transition:border-color .18s,box-shadow .18s; width:100%; box-sizing:border-box; }
.ca2-field input[type=text]:focus, .ca2-field select:focus, .ca2-field textarea:focus { border-color:#1e4d8c; box-shadow:0 0 0 3px rgba(30,77,140,.08); background:#fff; }
.ca2-field textarea { resize:vertical; min-height:72px; }
.ca2-chars-hint { font-size:11px; color:#9e9e9e; margin-top:3px; }
.ca2-chars-hint input[type=text] { width:34px; padding:2px 4px; font-size:11px; border:none; background:transparent; color:#9e9e9e; text-align:center; }
.ca2-date-wrap { display:flex; align-items:center; gap:6px; }
.ca2-date-wrap input[type=text] { width:110px !important; }
.ca2-actions { padding:14px 20px; display:flex; gap:10px; align-items:center; border-top:1px solid #eef0f8; background:#f5f7ff; }
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php
include_once("./librairie_php/lib_licence.php");
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
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print "Ajouter un encaissement" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td style="padding:8px 4px;">

<?php
$anneescolairefiltre=$_POST["anneeScolaire"];
if (isset($_GET["anneescolaire"])) {
	$anneescolairefiltre=$_GET["anneescolaire"];
}

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
			print "<td class='cc-td' style='text-align:center;'><input type='button' onclick=\"open('compta_ajout2.php?ideleve=".$data[$i][0]."','_parent','')\" class='btn-primary' value='S&eacute;lectionner' /></td>";
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

<div class="ca2-student-card">
  <div class="ca2-student-header">Informations &eacute;l&egrave;ve</div>
  <div class="ca2-student-body">
    <div>
      <img src="image_trombi.php?idE=<?php print $ideleve ?>" border=0 style="border-radius:6px; box-shadow:0 4px 12px rgba(0,0,0,.3);">
    </div>
    <div class="ca2-student-info">
      <div><strong>Nom :</strong> <?php print $nomeleve ?></div>
      <div><strong>Pr&eacute;nom :</strong> <?php print $prenomeleve ?></div>
      <div><strong>Classe :</strong> <?php print ucwords($classe) ?></div>
      <div><strong>Boursier :</strong> <?php print etatBoursier($ideleve) ?> (<?php print montantBourse($ideleve) ?>)</div>
      <div><strong>Indemnit&eacute; de stage :</strong> <?php print montantIndemniteStage($ideleve) ?></div>
    </div>
  </div>
</div>

<div class="ca2-form-card">
  <div class="ca2-form-header">Saisie du versement</div>

  <!-- filtre année scolaire -->
  <form method='POST' action="compta_ajout2.php?ideleve=<?php print $ideleve ?>">
  <div class="ca2-year-bar">
    <label>Ann&eacute;e scolaire :</label>
    <select name='anneeScolaire' onchange="this.form.submit()" class="ca2-year-bar select">
      <option id='select0'><?php print LANGCHOIX ?></option>
      <?php filtreAnneeScolaireSelect($anneescolairefiltre) ?>
    </select>
  </div>
  </form>

  <!-- saisie du versement -->
  <form name="formulaire" method="post" action='compta_ajout3.php'>
  <input type='hidden' value="<?php print $anneescolairefiltre ?>" name='anneeScolaire'>
  <input type="hidden" name="ideleve" value="<?php print $ideleve ?>">

  <div class="ca2-fields">

    <div class="ca2-field">
      <label>Type de versement</label>
      <select name='typeversement'>
        <option id='select0'><?php print LANGCHOIX ?></option>
        <?php print selectVersementAjout($idclasse,$ideleve,$anneescolairefiltre) ?>
      </select>
    </div>

    <div class="ca2-field">
      <label>Montant r&eacute;gl&eacute;</label>
      <input type=text name='montant' placeholder="0.00">
    </div>

    <div class="ca2-field full">
      <label>N&deg; ch&egrave;que &mdash; Virement &mdash; Esp&egrave;ce</label>
      <input type=text name='numcheque' maxlength='250'>
    </div>

    <div class="ca2-field full">
      <label>&Eacute;tablissement bancaire</label>
      <input type=text name='banque' maxlength='250'>
    </div>

    <div class="ca2-field full">
      <label>Observation</label>
      <textarea name='modepaiement'
                onkeypress="compter(this,'145', this.form.CharRestant)"></textarea>
      <div class="ca2-chars-hint">
        Caract&egrave;res restants :
        <input type=text name='CharRestant' disabled='disabled' value=''>
      </div>
    </div>

    <div class="ca2-field">
      <label>Date d'encaissement</label>
      <div class="ca2-date-wrap">
        <input type="text" name="dateversement" value="" readonly>
        <?php include_once("librairie_php/calendar.php"); calendarDim('id1','document.formulaire.dateversement',$_SESSION["langue"],"0","0");?>
      </div>
    </div>

  </div>

  <div class="ca2-actions">
    <script language=JavaScript>buttonMagicSubmit("<?php print LANGENR?>","create");</script>
    <script language=JavaScript>buttonMagicRetour("comtpa_ajout.php","_self");</script>
  </div>

  </form>
</div>

<?php
}
?>

     </td></tr></table>
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
   </BODY></HTML>
