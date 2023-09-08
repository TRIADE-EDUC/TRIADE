<?php
session_start();
error_reporting(0);
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

$anneeScolaire=$_POST["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
	$anneeScolaire=$_POST["anneeScolaire"];
	setcookie("anneeScolaire",$anneeScolaire,time()+3600*24*30);
}

include_once("./librairie_php/lib_error.php");
include_once("common/config.inc.php"); 
include_once("common/config2.inc.php"); 
include("librairie_php/db_triade.php");


if ($_SESSION["membre"] == "menuprof") {
	if (PROFPACCESNOTE == "oui") {
		$cnx=cnx();
		verif_profp_ens($_SESSION["id_pers"]);
		Pgclose();
	}else{
		validerequete("menuadmin");
	}
}else{
	if ((VIESCOLAIRENOTEENSEIGNANT == "oui") && ($_SESSION["membre"] != "menupersonnel")) {
		validerequete("2");
	}else{
		if ($_SESSION["membre"] != "menuadmin" ) {
			$cnx=cnx();
			if (!verifDroit($_SESSION["id_pers"],"carnetnotes")) {
				accesNonReserveFen();
				exit();
			}
			Pgclose();
		}else{
			validerequete("menuadmin");
		}
	}
}
$cnx=cnx();


//variables utiles
$mySession[Sn]=$_SESSION["nom"];
$mySession[Sp]=$_SESSION["prenom"];
$pid=$_SESSION["id_pers"];

$cgrp=$_POST["sClasseGrp"];
$cgrp=explode(":",$cgrp);
$cid=$cgrp[0];
$gid=$cgrp[1];
$mid=$_POST["sMat"];

$nomClasse=chercheClasse($cid);
$nomClasse=$nomClasse[0][1];
$nomMat=chercheMatiereNom($mid);
$nomGrp=chercheGroupeNom($gid);
$libel=$nomClasse." ".$nomGrp." ".$nomMat;

?>
<HTML>
<HEAD>
<title>Enseignant - Triade - Compte de <?php print ucwords($mySession[Sp])." ".strtoupper($mySession[Sn])?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"] ?>.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD>
<td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"] ?>1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="1000">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGPROF3 ?> /  </b><font id="color2" > <?php print $libel?></font></font></td>
</tr>
<tr id='cadreCentral0' valign=top>
<td>
<!-- // fin  -->
<?php
$valeur=aff_Trimestre();
if (count($valeur)) {
?>
	<iframe MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0  name="visunote" src="visunoteadmin.php?sClasseGrp=<?php print $_POST["sClasseGrp"]?>&sMat=<?php print $_POST["sMat"]?>&saisie_pers=<?php print $_POST['saisie_pers'] ?>&anneeScolaire=<?php print $anneeScolaire ?>" width="100%" height="100%" ></iframe>
<?php
}else {
?>
<iframe MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0  scrolling=no name="visunote" src="visunoteadminnon.php" width="100%" height="100%" ></iframe>
<?php } ?>

     <!-- // fin  -->
     </td>
	 </tr>
	 </table>
     <?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
print "</SCRIPT>";
else :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
print "</SCRIPT>";
top_d();
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
print "</SCRIPT>";
endif ;
     ?>
   </BODY>
   </HTML>
   <?php @Pgclose() ?>
