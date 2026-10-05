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
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TYPE="text/css" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TYPE="text/css" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
<style>
.vd3-wrap {
  padding:24px 16px;
}
.vd3-card {
  background:#fff;
  border:1px solid #e8eaf6;
  border-radius:10px;
  box-shadow:0 2px 12px rgba(8,10,102,.07);
  padding:32px 36px 28px;
  max-width:540px;
  margin:0 auto;
  text-align:center;
}
.vd3-icon {
  font-size:42px;
  color:#2e7d32;
  margin-bottom:12px;
}
.vd3-title {
  font-family:Electrolize,Arial,sans-serif;
  font-size:17px;
  font-weight:700;
  color:#080A66;
  margin-bottom:6px;
}
.vd3-class {
  font-size:13px;
  color:#6c757d;
  margin-bottom:24px;
}
.vd3-actions {
  display:flex;
  gap:12px;
  justify-content:center;
  flex-wrap:wrap;
}
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if ($_SESSION["membre"] == "menupersonnel") {
	if (!verifDroit($_SESSION["id_pers"],"visadirection")) {
		Pgclose();
		accesNonReserveFen();
		exit();
	}
}elseif ($_SESSION["membre"] == "menuadmin") {
	validerequete("menuadmin");
}else{
	if (PROFPACCESVISADIRECTION == "oui") {
		validerequete("menuprof");
		verif_profp_class($_SESSION["id_pers"],$_SESSION["profpclasse"]);
	}else{
		validerequete("menuadmin");
	}
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Visa direction</font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<?php
$idclasse=$_POST["saisie_classe"];
$tri=$_POST["saisie_trimestre"];
if ($tri == "Semestre 2") $tri="trimestre2";
if ($tri == "Semestre 1") $tri="trimestre1";
$nb=$_POST["saisie_nb"];
$type_bulletin=$_POST["type_bulletin"];
$anneeScolaire=$_POST["anneeScolaire"];

for($i=0;$i<$nb;$i++) {
	$com=$_POST["comm_$i"];
	$eleveid=$_POST["eleveid_$i"];
	$montessori="aucun";
	$tmpp="montessori_$eleveid";
	if (isset($_POST[$tmpp])) { $montessori=$_POST[$tmpp]; }

	$leap_felicitation=0;
	$leap_encouragement=0;
	$leap_megcomp=0;
	$leap_megtrav=0;
	$jtc_promu=0;
	$jtc_reprendre=0;
	$jtc_orientation=0;
	$pp_av_trav=0;
	$pp_av_comp=0;
	$pp_enc=0;
	$pp_feli=0;
	$ppv2_av=0;
	$ppv2_faible=0;
	$ppv2_passable=0;
	$ppv2_enc=0;
	$ppv2_feli=0;

	// LEAP
	$tmpp="leap_felicitation_$eleveid";
	if (isset($_POST[$tmpp])) { $leap_felicitation=$_POST[$tmpp]; }
	$tmpp="leap_encouragement_$eleveid";
	if (isset($_POST[$tmpp])) { $leap_encouragement=$_POST[$tmpp]; }
	$tmpp="leap_megcomp_$eleveid";
	if (isset($_POST[$tmpp])) { $leap_megcomp=$_POST[$tmpp]; }
	$tmpp="leap_megtrav_$eleveid";
	if (isset($_POST[$tmpp])) { $leap_megtrav=$_POST[$tmpp]; }

	//JTC
	$tmpp="jtc_promu_$eleveid";
	if (isset($_POST[$tmpp])) { $jtc_promu=$_POST[$tmpp]; }
	$tmpp="jtc_reprendre_$eleveid";
	if (isset($_POST[$tmpp])) { $jtc_reprendre=$_POST[$tmpp]; }
	$tmpp="jtc_orientation_$eleveid";
	if (isset($_POST[$tmpp])) { $jtc_orientation=$_POST[$tmpp]; }

	//Pigier Paris
	$tmpp="pp_av_trav_$eleveid";
	if (isset($_POST[$tmpp])) { $pp_av_trav=$_POST[$tmpp]; }
	$tmpp="pp_av_comp_$eleveid";
	if (isset($_POST[$tmpp])) { $pp_av_comp=$_POST[$tmpp]; }
	$tmpp="pp_enc_$eleveid";
	if (isset($_POST[$tmpp])) { $pp_enc=$_POST[$tmpp]; }
	$tmpp="pp_feli_$eleveid";
	if (isset($_POST[$tmpp])) { $pp_feli=$_POST[$tmpp]; }

	//Pigier Paris V2
	$tmpp="pp2_av_$eleveid";
	if (isset($_POST[$tmpp])) { $ppv2_av=$_POST[$tmpp]; }
	$tmpp="pp2_faible_$eleveid";
	if (isset($_POST[$tmpp])) { $ppv2_faible=$_POST[$tmpp]; }
	$tmpp="pp2_passable_$eleveid";
	if (isset($_POST[$tmpp])) { $ppv2_passable=$_POST[$tmpp]; }
	$tmpp="pp2_enc_$eleveid";
	if (isset($_POST[$tmpp])) { $ppv2_enc=$_POST[$tmpp]; }
	$tmpp="pp2_feli_$eleveid";
	if (isset($_POST[$tmpp])) { $ppv2_feli=$_POST[$tmpp]; }

	$cr=create_comm_direc_bull($eleveid,$tri,$com,$montessori,$type_bulletin,$leap_felicitation,$leap_encouragement,$leap_megcomp,$leap_megtrav,$jtc_promu,$jtc_reprendre,$jtc_orientation,$pp_av_trav,$pp_av_comp,$pp_enc,$pp_feli,$ppv2_av,$ppv2_faible,$ppv2_passable,$ppv2_enc,$ppv2_feli,$anneeScolaire);
	if ($cr) {
		history_cmd($_SESSION["nom"],"BULLETIN","Commentaire Direction");
	}
}
?>
<div class="vd3-wrap">
  <div class="vd3-card">
    <div class="vd3-icon"><i class="bi bi-check-circle-fill"></i></div>
    <div class="vd3-title">Bulletin enregistré</div>
    <div class="vd3-class"><i class="bi bi-people-fill"></i>&nbsp;<?php print chercheClasse_nom($idclasse) ?></div>
    <form method='post' action="visa_direction2.php">
      <input type="hidden" name="saisie_classe" value='<?php print $idclasse ?>'>
      <input type="hidden" name="tri" value='<?php print $tri ?>'>
      <input type="hidden" name="saisie_trimestre" value='<?php print $tri ?>'>
      <input type="hidden" name="type_bulletin" value='<?php print $type_bulletin ?>'>
      <div class="vd3-actions">
        <script>buttonMagicSubmitAtt('Retour Commentaire <?php print chercheClasse_nom($idclasse) ?>','consult','','ok')</script>
        <script>buttonMagic2('Retour','visa_direction.php?','_self','','0')</script>
      </div>
    </form>
  </div>
</div>
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php
Pgclose();
?>
</BODY>
</HTML>
