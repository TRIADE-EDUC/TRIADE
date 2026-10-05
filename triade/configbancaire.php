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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<style>
.cb-card { background:#fff; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,.08); overflow:hidden; }
.cb-header { background:linear-gradient(90deg,#080A66,#1e4d8c); color:#fff; padding:10px 18px; font-size:12px; font-weight:700; font-family:Electrolize,'Trebuchet MS',Arial; letter-spacing:.5px; text-transform:uppercase; display:flex; align-items:center; gap:8px; }
.cb-body { padding:20px 22px; display:flex; flex-direction:column; gap:18px; }
.cb-field { display:flex; flex-direction:column; gap:5px; }
.cb-field label { font-size:11px; font-weight:700; color:#5c6bc0; text-transform:uppercase; letter-spacing:.4px; }
.cb-field input { padding:9px 12px; border:1px solid #e0e3ef; border-radius:7px; font-size:13px; font-family:inherit; color:#333; background:#fafbff; outline:none; transition:border-color .18s,box-shadow .18s; width:100%; box-sizing:border-box; }
.cb-field input:focus { border-color:#1e4d8c; box-shadow:0 0 0 3px rgba(30,77,140,.08); background:#fff; }
.cb-section { display:flex; flex-direction:column; gap:12px; }
.cb-section-title { font-size:11px; font-weight:700; color:#9e9e9e; text-transform:uppercase; letter-spacing:.5px; padding-bottom:6px; border-bottom:2px solid #eef0f8; }
.cb-rib-grid { display:grid; grid-template-columns:110px 80px 1fr 56px; gap:10px; }
.cb-iban input { font-family:'Courier New',monospace; letter-spacing:1.5px; font-size:13px; color:#1a237e; }
.cb-divider { border:none; border-top:1px solid #eef0f8; margin:0; }
.cb-actions { padding:14px 22px; display:flex; gap:10px; align-items:center; border-top:1px solid #eef0f8; background:#f5f7ff; }
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return verifcommun()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print "Enregistrement des informations bancaires"?></font></b></td></tr>
<tr id='cadreCentral0' >
<td style="padding:8px 4px;">
<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
if (isset($_POST["create"])) {
	if (check_rib($_POST["codebanque"], $_POST["guichet"], $_POST["num_compte"], $_POST["rib"])) {
		enr_parametrage("bancsociete",$_POST["saisie_societe"]);
		enr_parametrage("banccode",$_POST["codebanque"]);
		enr_parametrage("bancguic",$_POST["guichet"]);
		enr_parametrage("bancompte",$_POST["num_compte"]);
		enr_parametrage("banrib",$_POST["rib"]);
		if (isValideIBAN($_POST["iban"])) {
			enr_parametrage("iban",$_POST["iban"]);
			echo '<script>document.addEventListener("DOMContentLoaded",function(){ alertify.success("'.LANGDONENR.'"); });</script>';
		} else {
			echo '<script>document.addEventListener("DOMContentLoaded",function(){ alertify.error("Erreur IBAN"); });</script>';
		}
	} else {
		echo '<script>document.addEventListener("DOMContentLoaded",function(){ alertify.error("Erreur RIB"); });</script>';
	}
}

$bancsoc=aff_enr_parametrage("bancsociete");
$banccode=aff_enr_parametrage("banccode");
$bancguic=aff_enr_parametrage("bancguic");
$bancompte=aff_enr_parametrage("bancompte");
$banrib=aff_enr_parametrage("banrib");
$iban=aff_enr_parametrage("iban");

if ($iban[0][1] == "") {
	$iban=Rib2Iban($banccode[0][1],$bancguic[0][1],$bancompte[0][1],$banrib[0][1]);
} else {
	$iban=$iban[0][1];
}
if ($iban == "FR76") $iban="";

Pgclose();
?>

<div class="cb-card">
  <div class="cb-body">

    <div class="cb-field">
      <label>Nom de la soci&eacute;t&eacute; / &eacute;tablissement</label>
      <input type=text name="saisie_societe" value="<?php print $bancsoc[0][1] ?>" maxlength=30>
    </div>

    <hr class="cb-divider">

    <div class="cb-section">
      <div class="cb-section-title">Coordonn&eacute;es RIB</div>
      <div class="cb-rib-grid">
        <div class="cb-field">
          <label>Code banque</label>
          <input type=text name="codebanque" value="<?php print $banccode[0][1] ?>" maxlength=10>
        </div>
        <div class="cb-field">
          <label>Guichet</label>
          <input type=text name="guichet" value="<?php print $bancguic[0][1] ?>" maxlength=5>
        </div>
        <div class="cb-field">
          <label>N&deg; de compte</label>
          <input type=text name="num_compte" value="<?php print $bancompte[0][1] ?>" maxlength=50>
        </div>
        <div class="cb-field">
          <label>Cl&eacute; RIB</label>
          <input type=text name="rib" value="<?php print $banrib[0][1] ?>" maxlength=2>
        </div>
      </div>
    </div>

    <hr class="cb-divider">

    <div class="cb-field cb-iban">
      <label>IBAN</label>
      <input type=text name="iban" value="<?php print $iban ?>" maxlength=40 placeholder="FR76 XXXX XXXX XXXX XXXX XXXX XXX">
    </div>

  </div>
  <div class="cb-actions">
    <script language=JavaScript>buttonMagicSubmit("<?php print "Enregistrer"?>","create");</script>
    <script language=JavaScript>buttonMagicRetour("comptavers.php","_self");</script>
  </div>
</div>

</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
