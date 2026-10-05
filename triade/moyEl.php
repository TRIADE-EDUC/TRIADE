<?php
session_start();
include_once("./librairie_php/verifEmailEnregistre.php");
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
include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php"); // futur : auto_prepend_file
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/timezone.php");
include_once("librairie_php/recupnoteperiode.php");
$cnx=cnx();
// variables de session
$myS=$_SESSION;
$Snom=$myS['nom'];
$Sprenom=$myS['prenom'];
$Seid=$myS['id_pers'];
$Scid=$myS['idClasse'];
unset($myS);
// la date
if(!$date=$_GET["m"]){
	$date=dateM();
        $annee=dateY();
}

if (!empty($_GET['annee'])) {
	$annee=$_GET['annee'];
}

$prevannee=$annee;
$nextannee=$annee;

if ($date == 1)  $prevannee=$annee-1;
if ($date == 12) $nextannee=$annee+1;

// la date
if(!$date=$_GET["m"]){ $date=date("n"); }
if($date==1)  { $prev=12; }else{ $prev=$date-1; }
if($date==12) { $next=1; }else{ $next=$date+1; }


// à verifier avec postgresql
if ($date < 10) {
	$date=preg_replace("/0/","",$date);
}


if($date==1):
	$prev=12;
else:
	$prev=$date-1;
endif;
if($date==12):
	$next=1;
else:
	$next=$date+1;
endif;

if ($_SESSION["membre"] == "menututeur") { $Seid=""; }

if (isset($_POST["idelevetuteur"])) {
	$Seid=$_POST["idelevetuteur"];
	$_SESSION["idelevetuteur"]=$Seid;
	$Scid=chercheClasseEleve($Seid);
	$_SESSION["idClasse"]=$Scid;
}

if ((trim($Seid) == "") && ($_SESSION["membre"] == "menututeur")) {
         $list=listEleveTuteur2($_SESSION["id_pers"]);
         if (countTriade($list) == 1) {
                $Seid=$list[0][0];
                $Scid=chercheClasseEleve($Seid);
                $idClasse=$Scid;
        }
}

if (isset($_SESSION["idelevetuteur"])) {
	$Seid=$_SESSION["idelevetuteur"];
	
}

$anneeScolaire=anneeScolaireViaIdClasse($Scid);
?>
<HTML>
<HEAD>
<title>Triade - Compte de <?php print ucwords($Sprenom)." ".strtoupper($Snom)?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<style>
	 td.local {background-color : #FFFFFF;color:red;}
	 a.local {background-color : #FFFFFF;color:red;}
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<form name="formnote" method='post' action='notesEleve.php' >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Moyenne du " ?>
<?php
// recherche des dates de debut et fin
$dateRecup=recupDateTrimByIdclasse($_POST["saisie_trimestre"],$Scid);
for($j=0;$j<countTriade($dateRecup);$j++) {
        $dateDebut=$dateRecup[$j][0];
        $dateFin=$dateRecup[$j][1];
}
$dateDebut=dateForm($dateDebut);
$dateFin=dateForm($dateFin);
$periode="<font id=color2>$dateDebut</font> au <font id=color2>$dateFin</font>";
?>
p&eacute;riode du <?php print $periode ?>
</font></b>
</td></tr>
<tr id='cadreCentral0'>
<?php
if ($_SESSION["membre"] == "menututeur") {
?>
	&nbsp;&nbsp;
	<select name='idelevetuteur' onchange="this.form.submit()" >
		<?php 
		if ($Seid != "") {
			$nom=recherche_eleve_nom($Seid);
			$prenom=recherche_eleve_prenom($Seid);
	        	print "<option id='select1' value='$Seid' title=\"".strtoupper($nom)." $prenom\" >".trunchaine(strtoupper($nom)." ".$prenom,30)."</option>\n";
		}else{
			print "<option id='select0' >".LANGCHOIX."</option>";
		}
		listEleveTuteur($_SESSION["id_pers"],30)
		?>
	</select>
<?php
}
?>
     </tr>
     <tr bgcolor="#FFFFFF">
     <td colspan=2  style="padding:0; margin:0;" ><br><iframe src="video-proj-bulletin.php?saisie_trimestre=<?php print $_POST['saisie_trimestre']?>" width='100%' height=700 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no align=left  ></iframe>
     <!-- CORPS -->
	<?php
	if (((ACCESNOTEPARENT == "non") && ($_SESSION["membre"] == "menuparent")) || ((ACCESNOTEELEVE == "non") && ($_SESSION["membre"] == "menueleve")) ){
		print "<center><font color='red' class='T2' >".LANGMESS37.".</font></center>";
	}else{
	?>
 	<?php

	}
	?>
	 <!-- fin CORPS  -->
     </td></tr>
	 </table>
	

     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")):
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
<SCRIPT language="JavaScript">InitBulle("#000000","#FFFFFF","red",1);</SCRIPT>
   </BODY>
   </HTML>
   <?php @Pgclose() ?>
