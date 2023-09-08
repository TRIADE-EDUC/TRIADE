<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
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
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Visa direction" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut form  -->
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

//print "$eleveid,$tri,$com,$montessori,$type_bulletin,$leap_felicitation,$leap_encouragement,$leap_megcomp,$leap_megtrav,$jtc_promu,$jtc_reprendre,$jtc_orientation,$pp_av_trav,$pp_av_comp,$pp_enc,$pp_feli,$ppv2_av,$ppv2_faible,$ppv2_passable,$ppv2_enc,$ppv2_feli,$anneeScolaire<br><br>";

	$cr=create_comm_direc_bull($eleveid,$tri,$com,$montessori,$type_bulletin,$leap_felicitation,$leap_encouragement,$leap_megcomp,$leap_megtrav,$jtc_promu,$jtc_reprendre,$jtc_orientation,$pp_av_trav,$pp_av_comp,$pp_enc,$pp_feli,$ppv2_av,$ppv2_faible,$ppv2_passable,$ppv2_enc,$ppv2_feli,$anneeScolaire);
	if ($cr) {
		history_cmd($_SESSION["nom"],"BULLETIN","Commentaire Direction");
	}
}	

	

?>
<br />
</form>
<form method='post' action="visa_direction2.php" >
<center><font class=T2>Bulletin Enregistré</font></center>
<br><br>
<table align='center' ><tr>
<td><script >buttonMagicSubmitAtt('Retour Commentaire <?php print chercheClasse_nom($idclasse) ?>','consult','','ok')</script></td>
<td><script >buttonMagic2('Retour','visa_direction.php?','_self','','0')</script></td>
</tr></table>
<input type=hidden name="saisie_classe" value='<?php print $idclasse ?>' />
<input type=hidden name="tri" value='<?php print $tri ?>' />
<input type=hidden name="saisie_trimestre"  value='<?php print $tri ?>' />
<input type=hidden name="type_bulletin"  value='<?php print $type_bulletin ?>' />
</form>
<br><br>
<!-- // fin form -->
</td></tr></table>


<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
