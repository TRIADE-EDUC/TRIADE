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

//include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php"); // futur : auto_prepend_file
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/notes.inc.php");
include_once("./common/config2.inc.php");
if ((VIESCOLAIRENOTEENSEIGNANT == "oui") && ($_SESSION["membre"] != "menupersonnel"))  {
	validerequete("3");
}else{
	if (($_SESSION["membre"] != "menuadmin") && ($_SESSION["membre"] != "menuprof")) {
		$cnx=cnx();
		if (!verifDroit($_SESSION["id_pers"],"carnetnotes")) {
			accesNonReserveFen();
			exit();
		}
		Pgclose();
	}else{
		validerequete("profadmin");
	}
}

$cnx=cnx();



// Sn : variable de Session nom
// Sp : variable de Session prenom
// Sm : variable de Session membre
// Spid : variable de Session pers_id
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
//print_r($mySession);
$eleves=$_POST["elev_id"];
$dates=$_POST["iDate"];
$mid=$_POST["mid"];
$coefs=$_POST["iCoef"];
$notes=$_POST["iNotes"];
$sujets=$_POST["iSujet"];
$noms=$_POST["elev_nom"];
$noteusa=$_POST["NoteUsa"];
$noteExam=$_POST["NoteExam"];
$notationSur= $_POST["NotationSur"];
$adminIdprof=$_POST["adminIdprof"];
$notevisiblele=$_POST["notevisiblele"];

if ($_POST["NoteUsa"] == "oui") {
	$notetype="Notation en mode USA";
}else{
	$notetype="Notation sur $notationSur";
}

if($_POST["gid"]):
	$verif=verifProfDansGroupe($_SESSION["id_pers"],$_POST["gid"]);
	if (($_SESSION["membre"] == "menuprof") && (!isset($_SESSION["profpclasse"]))) {
		if ($verif) { header("Location:noteajout.php");exit; }
	}
	$who="<font color=\"#FFFFFF\">- ".LANGPROF4."  : </font> ".trunchaine(chercheGroupeNom($_POST["gid"]),10)." <font color='#FFFFFF'>-</font> $notetype ";
	$who2=chercheGroupeNom($_POST["gid"])."- $notetype ";
	$idgrp=$_POST["gid"];
	$idcl="-1";
else:
	$idcl=$_POST["cid"];
	$idgrp="NULL";
	$cl=chercheClasse($_POST["cid"]);
	$verif=verifProfDansClasse($_SESSION["id_pers"],$cl[0][0]);
	if (($_SESSION["membre"] == "menuprof") && (!isset($_SESSION["profpclasse"]))) {
		if ($verif) { header("Location:noteajout.php");exit; }
	}
	$who2=$cl[0][1]."- $notetype ";
	$who="<font color=\"#FFFFFF\">- ".LANGIMP10." : </font>".trunchaine($cl[0][1],10)." <font color='#FFFFFF'>-</font> $notetype ";
	unset($cl);
endif;
?>
<HTML>
<HEAD>
<title>Enseignant - Triade - Compte de <?php print ucwords($mySession[Sp])." ".strtoupper($mySession[Sn])?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"] ?>.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h();?></div>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"] ?>1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPROF14 ?> </b><font id='color2'><?php print trunchaine(chercheMatiereNom($mid),25)." ".$who?></font></font></td></tr>
<tr id='cadreCentral0'><td>
<!-- // fin  -->
<br />
<?php
// construction des tableaux de notes
for($i=0;$i<count($sujets);$i++){
	for($j=0;$j<count($eleves);$j++){
		$Notes[$i][$j]= new Note($noms[$j],$eleves[$j],$notes[$j][$i]);
	}
	if ($noteusa == "oui") {
		$typenote="en";
	}else{
		$typenote="fr";
	}

	$sujets[$i]=preg_replace('/\+/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/\?/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/\//',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/&/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/%/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/µ/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/\^/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/\(/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/\)/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/"/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace("/'/",' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/\$/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/£/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/:/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/=/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/\*/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/¨/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/;/',' ',$sujets[$i]);

	//$sujets[$i]=preg_replace('/\\/',' ',$sujets[$i]);
	
	$idprof=$mySession[Spid];
	if ($adminIdprof != "") { $idprof=$adminIdprof; }

	$listeNotes[$i]=new ListeNotes($i,$idprof,$mid,$coefs[$i],$dates[$i],$sujets[$i],$Notes[$i],$idcl,$idgrp,$typenote,$noteExam,$notationSur,$notevisiblele);  // 20/01/2006 $noteusa
	$listeNotes[$i]->persist();
	$listeNotes[$i]->affHtml();
}
// history cmd
$mid=chercheMatiereNom($mid);
history_cmd($mySession[Sn],"AJOUT","Notes - $who2 - $mid");
// fin history
 
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menupersonnel")) {
	print "<form method='post' action='notevisuadmin.php' target='_parent' >";
	print "<div><script language='JavaScript'>buttonMagicSubmitAtt('Retour menu principal','create','');</script></div>";
	print "<input type='hidden' name='saisie_pers' value='".$_POST["adminIdprof"]."' /></form><br><br>";
} 

if (($_SESSION["membre"] == "menuprof") && (isset($_SESSION["profpclasse"]))) {
	print "<br><form method='post' action='carnetnoteprofp.php?sClasseGrp=".$_POST["cid"]."' target='_parent' >";
	print "<div><script language='JavaScript'>buttonMagicSubmitAtt('Choisir un autre enseignant','create','');</script></div>";
	print "</form><br><br>";

	print "<form method='post' action='notevisuadmin.php' target='_parent' >";
	print "<div><script language='JavaScript'>buttonMagicSubmitAtt('Ajouter un autre devoir','create','');</script></div>";
	print "<input type='hidden' name='idclasse' value='".$_POST["cid"]."' />";
	print "<input type='hidden' name='saisie_pers' value='".$_POST["saisie_pers"]."' />";
	print "</form>";
} 

?>
<!-- // fin  -->
<br /><br />
</td></tr></table>
<script language=JavaScript>attente_close();</script>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
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
Pgclose();
     ?>
</BODY>
</HTML>
