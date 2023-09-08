<?php
session_start();
if (isset($_GET["saisie_classe"])) {
	$idClasse=$_GET["saisie_classe"];
	$serie="STA";
}

if (isset($_POST["saisie_classe"])) {
	$idClasse=$_POST["saisie_classe"];
	$serie="STA";
}

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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
<script>
function somme(j) {
	var sommeTotal=0;
	var total=eval("document.formulaire.total"+j);

<?php if ($_POST["notehistarts"] == '1') { ?>
	var notehistoirearts=eval("document.formulaire.notehistoirearts"+j+".value");
	if (notehistoirearts > 0) { sommeTotal=sommeTotal+parseFloat(notehistoirearts); }
<?php } ?>

	var notefrancais=eval("document.formulaire.notefrancais"+j+".value");
	if (notefrancais > 0) { sommeTotal=sommeTotal+parseFloat(notefrancais); }

	var noteMathematiques=eval("document.formulaire.noteMathematiques"+j+".value");
	if (noteMathematiques > 0) { sommeTotal=sommeTotal+parseFloat(noteMathematiques); }

	var notelv1=eval("document.formulaire.notelv1"+j+".value");
	if (notelv1 > 0) { sommeTotal=sommeTotal+parseFloat(notelv1); }
	
	var notephysChimi=eval("document.formulaire.notesciencephysique"+j+".value");
	if (notephysChimi > 0) { sommeTotal=sommeTotal+parseFloat(notephysChimi); }

	var notephysChimi=eval("document.formulaire.noteprevsantenv"+j+".value");
	if (notephysChimi > 0) { sommeTotal=sommeTotal+parseFloat(notephysChimi); }
	
	var noteeps=eval("document.formulaire.noteeps"+j+".value");
	if (noteeps > 0) { sommeTotal=sommeTotal+parseFloat(noteeps); }
	
	var notearts=eval("document.formulaire.noteEducationSocio"+j+".value");
	if (notearts > 0) { sommeTotal=sommeTotal+parseFloat(notearts); }
	
	var notemusic=eval("document.formulaire.notesciencebio"+j+".value");
	if (notemusic > 0) { sommeTotal=sommeTotal+parseFloat(notemusic); }
	
	var notetechno=eval("document.formulaire.notetechnoAgricole"+j+".value");
	if (notetechno > 0) { sommeTotal=sommeTotal+parseFloat(notetechno); }

	//var noteLV2=eval("document.formulaire.notehistgeo"+j+".value");
	//if (noteLV2 > 0) { sommeTotal=sommeTotal+parseFloat(noteLV2); }

	var noteviescolaire=eval("document.formulaire.noteviescolaire"+j+".value");
	if (noteviescolaire > 0) { sommeTotal=sommeTotal+parseFloat(noteviescolaire); }
	
	
/*	var noteOPT=eval("document.formulaire.noteOPT"+j+".value");
	if (noteOPT > 0) { 
		noteOPT=eval(noteOPT)-10;
		sommeTotal=sommeTotal+parseFloat(noteOPT); 
} */


	sommeTotal=number_format(sommeTotal,2, '.','');
	if (sommeTotal < 100) { sommeTotal="0"+sommeTotal; } 

	total.value=sommeTotal;
 
}
</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print "Gestion d'examen" ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<script>
function ine(i,nom,prenom,img,imd) {
	var chaine="Indiquez l'identifiant nationnal de "+ nom + "  " + prenom;
	var resultat=prompt(chaine,"");
	var champ="document.formulaire.INE"+i;
	var champ2=eval(champ);
	champ2.value=resultat;
	if (resultat.length > 0) { document.getElementById(img).style.visibility='hidden'; document.getElementById(imd).style.visibility='hidden' } 
}

function cacheTelechargement() { document.getElementById("telechargement").style.visibility='hidden'; }
</script>


<form method="post" name="formulaire" action="notanet_export22.php">
<!-- // debut form  -->
<?php
include_once("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
include_once("librairie_php/lib_brevet.php");
include_once("librairie_php/recupnoteperiode.php");
validerequete("menuadmin");
$cnx=cnx();


$datap=config_param_visu("contnotanet");
$controle=$datap[0][0];
$datap=config_param_visu("notehistarts");
$notehistarts=$datap[0][0];
$datap=config_param_visu("notehistgeo");
$notehistgeo=$datap[0][0];
$datap=config_param_visu("noteeducivi");
$noteeducivi=$datap[0][0];
$datap=config_param_visu("noteA2");
$noteA2=$datap[0][0];
$datap=config_param_visu("epsviaexamen");
$epsviaexamen=$datap[0][0];
$datap=config_param_visu("noteeviescolaire");
$noteeviescolaire=$datap[0][0];
$datap=config_param_visu("prevsanteenv");
$prev_sante_envviaexamen=$datap[0][0];


$dateDebut=recupDateDebutAnnee();
$dateFin=recupDateFinAnnee();

if (isset($_POST["create3"])) {
	$ret="\n";
	if (PHP_OS == "WINNT") {  $ret="\r\n"; }	
	$err=0;

	for($j=0;$j<$_POST["nbeleve"];$j++) {  
		// variable eleve
		$INE="INE$j"; $INE=$_POST[$INE];
		$nomEleve="nomEleve$j";  $nomEleve=$_POST[$nomEleve];
		$idEleve="idEleve$j";	$idEleve=$_POST[$idEleve];
		$prenomEleve="prenomEleve$j";  $prenomEleve=$_POST[$prenomEleve];
		if (trim($INE) == "") { $err=1; continue; }
		DeleteBrevet($INE,"brevetcollege");
		updateINE($INE,$nomEleve,$prenomEleve);
		$INE=strtoupper($INE);

	$notefran="notefrancais$j";	$notefran=$_POST[$notefran];
	$codefran="codefrancais$j";	$codefran=$_POST[$codefran];
	if (trim($notefran) != "")  { $ligne.="$INE|$codefran|$notefran|$ret"; }else{ $err=1;$mess.="1 - $INE|$codefran|$notefran| <br>";$error0="<img src='image/nonlu.gif' >"; }
	enrgBrevet($INE,$codefran,$notefran,"brevetcollege",$idEleve);

if ($notehistarts == '1') { 
	$notemath="notehistoirearts$j";$notemath=$_POST[$notemath];
	$codemath="codehistoirearts$j";$codemath=$_POST[$codemath];
	if (trim($notemath) != "") { 	$ligne.="$INE|$codemath|$notemath|$ret";}else{ $err=1;$mess.="2 - $INE|$codemath|$notemath| <br>"; $error0="<img src='image/nonlu.gif' >";   }
	enrgBrevet($INE,$codemath,$notemath,"brevetcollege",$idEleve);
}

	$notemath="noteMathematiques$j";$notemath=$_POST[$notemath];
	$codemath="codeMathematiques$j";$codemath=$_POST[$codemath];
	if (trim($notemath) != "") { 	$ligne.="$INE|$codemath|$notemath|$ret";}else{ $err=1;$mess.="2 - $INE|$codemath|$notemath| <br>";  }
	enrgBrevet($INE,$codemath,$notemath,"brevetcollege",$idEleve);

	$notelv1="notelv1$j";		$notelv1=$_POST[$notelv1];
	$codelv1="codelv1$j";		$codelv1=$_POST[$codelv1];
	if (trim($notelv1) != "") { 	$ligne.="$INE|$codelv1|$notelv1|$ret";}else{ $err=1;$mess.="3 - $INE|$codelv1|$notelv1| <br>";  }
	enrgBrevet($INE,$codelv1,$notelv1,"brevetcollege",$idEleve);

	$notesvt="notesciencephysique$j";		$notesvt=$_POST[$notesvt];
	$codesvt="codesciencephysique$j";		$codesvt=$_POST[$codesvt];
	if (trim($notesvt) != "") { 	$ligne.="$INE|$codesvt|$notesvt|$ret";}else{ $err=1;$mess.="4 - $INE|$codesvt|$notesvt| <br>";  }
	enrgBrevet($INE,$codesvt,$notesvt,"brevetcollege",$idEleve);
	
	$notephy="noteprevsantenv$j";	$notephy=$_POST[$notephy];
	$codephy="codeprevsantenv$j";	$codephy=$_POST[$codephy];
	if (trim($notephy) != "") { 	$ligne.="$INE|$codephy|$notephy|$ret";}else{ $err=1;$mess.="5 - $INE|$codephy|$notephy| <br>";  }
	enrgBrevet($INE,$codephy,$notephy,"brevetcollege",$idEleve);
	
	$noteeps="noteeps$j";		$noteeps=$_POST[$noteeps];
	$codeeps="codeeps$j";		$codeeps=$_POST[$codeeps];
	if (trim($noteeps) != "") { 	$ligne.="$INE|$codeeps|$noteeps|$ret";}else{ $err=1;$mess.="6 - $INE|$codeeps|$noteeps| <br>";  }
	enrgBrevet($INE,$codeeps,$noteeps,"brevetcollege",$idEleve);
	
	$noteart="noteEducationSocio$j";		$noteart=$_POST[$noteart];
	$codeart="codeEducationSocio$j";		$codeart=$_POST[$codeart];
	if (trim($noteart) != "") { 	$ligne.="$INE|$codeart|$noteart|$ret";}else{ $err=1;$mess.="7 - $INE|$codeart|$noteart| <br>";  }
	enrgBrevet($INE,$codeart,$noteart,"brevetcollege",$idEleve);
	
	$notemuc="notesciencebio$j";		$notemuc=$_POST[$notemuc];
	$codemuc="codesciencebio$j";		$codemuc=$_POST[$codemuc];
	if (trim($notemuc) != "") { 	$ligne.="$INE|$codemuc|$notemuc|$ret";}else{ $err=1;$mess.="8 - $INE|$codemuc|$notemuc| <br>";  }
	enrgBrevet($INE,$codemuc,$notemuc,"brevetcollege",$idEleve);
	
	$notetech="notetechnoAgricole$j";	$notetech=$_POST[$notetech];
	$codetech="codetechnoAgricole$j";	$codetech=$_POST[$codetech];
	if (trim($notetech) != "") { 	$ligne.="$INE|$codetech|$notetech|$ret";}else{ $err=1;$mess.="9 - $INE|$codetech|$notetech| <br>";  }
	enrgBrevet($INE,$codetech,$notetech,"brevetcollege",$idEleve);

	$notescol="noteviescolaire$j";	$notescol=$_POST[$notescol];
	$codescol="codviescolaire$j";	$codescol=$_POST[$codescol];
	if (trim($notescol) != "") { 	$ligne.="$INE|$codescol|$notescol|$ret";}else{ $err=1;$mess.="12 - $INE|$codescol|$notescol| <br>";  }
	enrgBrevet($INE,$codescol,$notescol,"brevetcollege",$idEleve);

if ($notehistarts == '1') { 
	$noteA2="noteA2R$j";		$noteA2=$_POST[$noteA2];
	$codeA2="codeA2R$j";		$codeA2=$_POST[$codeA2];
	if (trim($noteA2) != "") { 	$ligne.="$INE|$codeA2|$noteA2|$ret";}
	enrgBrevet($INE,$codeA2,$noteA2,"brevetcollege",$idEleve);
	create_noteB2IA2($idEleve,$idClasse,'A2R',$noteA2);
}

if (($notehistgeo == "1") && ($noteeducivi == "1")) {
	$notehist="notehistgeo$j";	$notehist=$_POST[$notehist];
	$codehist="codehistgeo$j";	$codehist=$_POST[$codehist];
	if (trim($notehist) != "") { 	$ligne.="$INE|$codehist|$notehist|$ret";}else{ $err=1;$mess.="15 - $INE|$codehist|$notehist| <br>";  }
	enrgBrevet($INE,$codehist,$notehist,"brevetcollege",$idEleve);	
}

	$notetotal="total$j";		$notetotal=$_POST[$notetotal];
	$codetot="tot$j";		$codetot=$_POST[$codetot];
	if (trim($notetotal) != "") { 	$ligne.="$INE|$codetot|$notetotal|$ret"; }else{ $err=1;$mess.="17 - $INE|$codetot|$notetotal| <br>"; }
	//enrgBrevet($INE,$codetot,$notetotal,"brevetcollege",$idEleve);
	

	}

	if ( (($err == 0) && ($controle == 1)) ||  ($controle != 1) ) {
		$fichier="./data/fichier_ASCII/Triade2Notanet.txt";
		$fd=fopen("$fichier","w");
		fwrite($fd,$ligne);
		fclose($fd);
		print "<div id='telechargement'><br><br><center><input type=button onclick=\"open('telecharger.php?fichier=$fichier','_blank','');\" value=\"Récuperer le fichier Notanet\" class=\"bouton2\" ></center><br><br></div>";
	}else{
		print "<center><font color=red class=T2><b>Informations non complètes !!! </b></font></center>";
	}
}	


$eleveT=recupEleve($idClasse); // nom,prenom,lv1,lv2,elev_id,date_naissance,lieu_naissance,adr1,code_post_adr1,commune_adr1,telephone, numero_eleve
$nbEleveT=count($eleveT);
print "<table border='0' >";
for($j=0;$j<$nbEleveT;$j++) {  
	// variable eleve
	$noteGlobal="";
	$nomEleve=ucwords($eleveT[$j][0]);
	$prenomEleve=ucfirst($eleveT[$j][1]);
	$lv1Eleve=$eleveT[$j][2];
	$lv2Eleve=$eleveT[$j][3];
	$idEleve=$eleveT[$j][4];
	$INE=$eleveT[$j][11];
	$nomEleve=strtoupper(trim($nomEleve));
	$prenomEleve=trim($prenomEleve);

	if (trim($INE) != "") {
		$img="";	
		$img0="";
	}else{
		$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif'  id='img1$j' align=center/>";
		$img0="&nbsp;<a href='javascript:ine(\"$j\",\"$nomEleve\",\"$prenomEleve\",\"img1$j\",\"imd$j\")' ><img src='image/commun/editer.gif' align='center' border='0' id='imd$j' /></a>";
	}	

	print "<tr><td colspan='3'><font class=T2>Elève : <b>$nomEleve $prenomEleve</b>  </td></tr>";
	print "<tr><td colspan='3' >INE : <input type=text name='INE$j' value='$INE' onchange='cacheTelechargement()' />&nbsp;$img0&nbsp;$img</td></tr>";
	print "<input type=hidden name='nomEleve$j' value=\"$nomEleve\" />";
	print "<input type=hidden name='prenomEleve$j' value=\"$prenomEleve\" />";
	print "<input type=hidden name='idEleve$j' value=\"$idEleve\" />";


if ($notehistarts == '1') { 
	// --------------------------------------------------------------------------------
	// HISTOIRE DES ARTS
	$tab=rechercheMatiereBrevet("histoire des arts",$idClasse);
	$nb=0;$noteT="";$note="";
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") {
                        $coef=recupCoefBrevet($idClasse,"histoire des arts",$idMatiere);
                        $noteT=$noteT + ($note*$coef);
                        $nb+=$coef;
                }
		
	}
	$codeEpreuve=recupCodeEpreuve($serie,"histoire des arts");

	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; }
		$noteGlobal=$noteGlobal+$note;
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='imgA0$j' align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}
	}
	print "<tr><td>Histoire des arts </td><td><input type=text name='notehistoirearts$j' value='$note' size=4  onchange='somme($j);cacheTelechargement()' /> /40";
	print " $img";
	print "</td><td>AB <input type=checkbox value='AB' onclick=\"somme($j);document.formulaire.notehistoirearts$j.value='AB';document.getElementById('imgA0$j').style.visibility='hidden';cacheTelechargement();\"  title='Absent' />";
	print "<input type=hidden name='codehistoirearts$j' value='$codeEpreuve' />";
	print "</td></tr>";
	// ---------------------------------------------------------------------------------
}


	// --------------------------------------------------------------------------------
	// FRANCAIS
	$tab=rechercheMatiereBrevet("Français",$idClasse);
	$nb=0;$noteT="";$note="";
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") {
                        $coef=recupCoefBrevet($idClasse,"Français",$idMatiere);
                        $noteT=$noteT + ($note*$coef);
                        $nb+=$coef;
                }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"Français");

	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; }
		$noteGlobal=$noteGlobal+$note;
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img0$j' align=center/>";
		}
		if ($note > 0) {	
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}

	}



	
	print "<tr><td>Français </td><td><input type=text name='notefrancais$j' value='$note' size=4  onchange='somme($j);cacheTelechargement();' /> /20";
	print "$img";
	print "</td><td>AB <input type=checkbox value='AB' onclick=\"somme($j);document.formulaire.notefrancais$j.value='AB';document.getElementById('img0$j').style.visibility='hidden';cacheTelechargement();\"  title='Absent' />";
	print "<input type=hidden name='codefrancais$j' value='$codeEpreuve' />";
	print "</td></tr>";
	// ---------------------------------------------------------------------------------

	// --------------------------------------------------------------------------------
	// MATHEMATIQUES
	$tab=rechercheMatiereBrevet("Mathématiques",$idClasse);
	$nb=0;$noteT="";$note="";
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") {
                        $coef=recupCoefBrevet($idClasse,"Mathématiques",$idMatiere);
                        $noteT=$noteT + ($note*$coef);
                        $nb+=$coef;
                }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"Mathematiques");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$noteGlobal=$noteGlobal+$note;	
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif'  id='img1$j' align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}

	}	
	print "<tr><td>Mathématiques </td><td><input type=text name='noteMathematiques$j' value='$note' size=4 onchange='somme($j);cacheTelechargement();'  /> /20";
	print "$img";
	print "<input type='hidden' name='codeMathematiques$j' value='$codeEpreuve' />";
	print "</td><td>AB <input type=checkbox value='AB' onclick=\"somme($j);document.formulaire.noteMathematiques$j.value='AB';document.getElementById('img1$j').style.visibility='hidden';cacheTelechargement();\"   title='Absent' />";
	print "</td></tr>";
	// ---------------------------------------------------------------------------------
	//
	// --------------------------------------------------------------------------------
	// Langue vivante 1
	$tab=rechercheMatiereBrevet("Langue vivante 1",$idClasse);
	$nb=0;$noteT="";$note="";
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if (!verifMatiereLangue($idEleve,$idMatiere,'LV1',$idClasse)) { continue; } 
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") {
                        $coef=recupCoefBrevet($idClasse,"Langue vivante 1",$idMatiere);
                        $noteT=$noteT + ($note*$coef);
                        $nb+=$coef;
                }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"lv1");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$noteGlobal=$noteGlobal+$note;	
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img2$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}

	}	
	print "<tr><td>Langue vivante 1 </td><td><input type=text name='notelv1$j' value='$note' size=4  onchange='somme($j);cacheTelechargement();' /> /20";
	print "$img";
	print "</td><td>AB <input type=checkbox value='AB' onclick=\"somme($j);document.formulaire.notelv1$j.value='AB';document.getElementById('img2$j').style.visibility='hidden';cacheTelechargement();\"    title='Absent' />";
	print "DI <input type=checkbox value='DI' onclick=\"somme($j);document.formulaire.notelv1$j.value='DI';document.getElementById('img2$j').style.visibility='hidden';cacheTelechargement();\"    title='Dispensé' />";
	print "<input type=hidden name='codelv1$j' value='$codeEpreuve' />";
	print "</td></tr>";
	// ---------------------------------------------------------------------------------

	// --------------------------------------------------------------------------------
	// SVT
	$tab=rechercheMatiereBrevet("Sciences Physiques",$idClasse);
	$nb=0;$noteT="";$note="";
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") {
                        $coef=recupCoefBrevet($idClasse,"Sciences Physiques",$idMatiere);
                        $noteT=$noteT + ($note*$coef);
                        $nb+=$coef;
                }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"sciencephysique");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$noteGlobal=$noteGlobal+$note;	
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img3$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}

	}	
	print "<tr><td>Sciences Physiques</td><td><input type=text name='notesciencephysique$j' value='$note' size=4 onchange='somme($j);cacheTelechargement();' /> /20";
	print "$img";
	print "</td><td>AB <input type=checkbox value='AB' onclick=\"somme($j);document.formulaire.notesciencephysique$j.value='AB';document.getElementById('img3$j').style.visibility='hidden';cacheTelechargement();\"    title='Absent' />";
	print "DI <input type=checkbox value='DI' onclick=\"somme($j);document.formulaire.notesciencephysique$j.value='DI';document.getElementById('img3$j').style.visibility='hidden';cacheTelechargement();\"    title='Dispensé'/>";
	print "<input type=hidden name='codesciencephysique$j' value='$codeEpreuve' />";
	print "</td></tr>";
	// ---------------------------------------------------------------------------------
	
	// --------------------------------------------------------------------------------
	//  
	$tab=rechercheMatiereBrevet("Prévention Santé",$idClasse);
	$nb=0;$noteT="";$note="";
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($prev_sante_envviaexamen == "1") {
//			if ( "biologie" == strtolower(chercheMatiereNomBrevet($idMatiere))) {
				$note=moyenneEleveMatiereBrevetViaExamen($idEleve,$idMatiere,$dateDebut,$dateFin,"Brevet PREV. SANTE ENV.");
//			}else{
//				$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
//			}
		}else{
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		}
		if ($note != "") {
                        $coef=recupCoefBrevet($idClasse,"Prévention Santé",$idMatiere);
                        $noteT=$noteT + ($note*$coef);
                        $nb+=$coef;
                }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"prevsantenv");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$noteGlobal=$noteGlobal+$note;	
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img4$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}

	}	
	print "<tr><td>Prévention Santé Environnement</td><td><input type=text name='noteprevsantenv$j' value='$note' size=4 onchange='somme($j);cacheTelechargement();'   /> /20";
	print "$img";
	print "</td><td>AB <input type=checkbox value='AB' onclick=\"somme($j);document.formulaire.noteprevsantenv$j.value='AB';document.getElementById('img4$j').style.visibility='hidden';cacheTelechargement();\"    title='Absent' />";
	print "DI <input type=checkbox value='DI' onclick=\"somme($j);document.formulaire.noteprevsantenv$j.value='DI';document.getElementById('img4$j').style.visibility='hidden';cacheTelechargement();\"   title='Dispensé' />";
	print "<input type=hidden name='codeprevsantenv$j' value='$codeEpreuve' />";
	print "</td></tr>";
	// ---------------------------------------------------------------------------------

	
	// --------------------------------------------------------------------------------
	// EPS 
	$tab=rechercheMatiereBrevet("Education physique et sportive",$idClasse);
	$nb=0;$noteT="";$note="";
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		if ($epsviaexamen == "1") {
			$note=moyenneEleveMatiereBrevetViaExamen($idEleve,$idMatiere,$dateDebut,$dateFin,"Brevet EPS");
		}else{
			$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		}
		if ($note != "") {
                        $coef=recupCoefBrevet($idClasse,"Education physique et sportive",$idMatiere);
                        $noteT=$noteT + ($note*$coef);
                        $nb+=$coef;
                }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"eps");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$noteGlobal=$noteGlobal+$note;	
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img5$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}

	}	
	print "<tr><td>Education physique et sportive </td><td><input type=text name='noteeps$j' value='$note' size=4  onchange='somme($j);cacheTelechargement();'  /> /20";
	print "$img";
	print "</td><td>AB <input type=checkbox value='AB' onclick=\"document.formulaire.noteeps$j.value='AB';somme($j);document.getElementById('img5$j').style.visibility='hidden';cacheTelechargement();somme($j);\"  title='Absent' />";
	print "DI <input type=checkbox value='DI' onclick=\"document.formulaire.noteeps$j.value='DI';somme($j);document.getElementById('img5$j').style.visibility='hidden';cacheTelechargement();\" title='Dispensé' />";
	print "NN <input type=checkbox value='NN' onclick=\"document.formulaire.noteeps$j.value='NN';somme($j);document.getElementById('img5$j').style.visibility='hidden';cacheTelechargement();\" title='Non Noté' />";
	print "<input type=hidden name='codeeps$j' value='$codeEpreuve' />";
	print "</td></tr>";
	// ---------------------------------------------------------------------------------


	// --------------------------------------------------------------------------------
	// Arts 
	$tab=rechercheMatiereBrevet("Education Socioculturelle",$idClasse);
	$nb=0;$noteT="";$note="";
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") {
                        $coef=recupCoefBrevet($idClasse,"Education Socioculturelle",$idMatiere);
                        $noteT=$noteT + ($note*$coef);
                        $nb+=$coef;
                }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"EducationSocio");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$noteGlobal=$noteGlobal+$note;	
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img6$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}

	}	
	print "<tr><td>Education Socioculturelle</td><td><input type=text name='noteEducationSocio$j' value='$note' size=4  onchange='somme($j);cacheTelechargement();' /> /20";
	print "$img";
	print "</td><td>AB <input type=checkbox value='AB' onclick='document.formulaire.noteEducationSocio$j.value=\"AB\" '  title='Absent' />";
	print "DI <input type=checkbox value='DI' onclick=\"document.formulaire.noteEducationSocio$j.value='DI';somme($j);document.getElementById('img6$j').style.visibility='hidden';cacheTelechargement();\"  title='Dispensé' />";
	print "NN <input type=checkbox value='NN' onclick=\"document.formulaire.noteEducationSocio$j.value='NN';somme($j);document.getElementById('img0$j').style.visibility='hidden';cacheTelechargement();\"    title='Non Noté' />";
	print "<input type=hidden name='codeEducationSocio$j' value='$codeEpreuve' />";
	print "</td></tr>";
	// ---------------------------------------------------------------------------------


	// --------------------------------------------------------------------------------
	// Music 
	$tab=rechercheMatiereBrevet("Sciences Biologiques",$idClasse);
	$nb=0;$noteT="";$note="";
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") {
                        $coef=recupCoefBrevet($idClasse,"Sciences Biologiques",$idMatiere);
                        $noteT=$noteT + ($note*$coef);
                        $nb+=$coef;
                }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"SciencesBio");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$noteGlobal=$noteGlobal+$note;	
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img7$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}

	}	
	print "<tr><td>Sciences Biologiques</td><td><input type=text name='notesciencebio$j' value='$note' size=4  onchange='somme($j);cacheTelechargement();' /> /20";
	print "$img";
	print "</td><td>AB <input type=checkbox value='AB' onclick=\"document.formulaire.notesciencebio$j.value='AB';somme($j);document.getElementById('img7$j').style.visibility='hidden';cacheTelechargement();\"    title='Absent' />";
	print "DI <input type=checkbox value='DI' onclick=\"document.formulaire.notesciencebio$j.value='DI';somme($j);document.getElementById('img7$j').style.visibility='hidden';cacheTelechargement();\"    title='Dispensé'  />";
	print "NN <input type=checkbox value='NN' onclick=\"document.formulaire.notesciencebio$j.value='NN';somme($j);document.getElementById('img7$j').style.visibility='hidden';cacheTelechargement();\"    title='Non Noté' />";
	print "<input type=hidden name='codesciencebio$j' value='$codeEpreuve' />";
	print "</td></tr>";
	// ---------------------------------------------------------------------------------
	//

	// --------------------------------------------------------------------------------
	// Technologie 
	$tab=rechercheMatiereBrevet("Techno Secteur Agricoles",$idClasse);
	$nb=0;$noteT="";$note="";
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") {
                        $coef=recupCoefBrevet($idClasse,"Techno Secteur Agricoles",$idMatiere);
                        $noteT=$noteT + ($note*$coef);
                        $nb+=$coef;
                }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"TechnoAgricole");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$noteGlobal=$noteGlobal+$note;	
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img8$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}

	}	
	print "<tr><td>Technologique Secteur Agricole</td><td><input type=text name='notetechnoAgricole$j' value='$note' size=4 onchange='somme($j);cacheTelechargement();'  /> /20";
	print "$img";
	print "</td><td>AB <input type=checkbox value='AB' onclick=\"document.formulaire.notetechnoAgricole$j.value='AB';somme($j);document.getElementById('img8$j').style.visibility='hidden';cacheTelechargement();\"    title='Absent' />";
	print "DI <input type=checkbox value='DI' onclick=\"document.formulaire.notetechnoAgricole$j.value='DI';somme($j);document.getElementById('img8$j').style.visibility='hidden';cacheTelechargement();\"   title='Dispensé' />";
	print "<input type=hidden name='codetechnoAgricole$j' value='$codeEpreuve' />";
	print "</td></tr>";
	// --------------------------------------------------------------------------------
	// Vie Scolaire
	$note=calculNoteVieScolaireBrevet($idEleve,$idClasse);
	$codeEpreuve=recupCodeEpreuve($serie,"viescolaire");
	if ($note != "") { 
		$note=arrondiAuDemi($note);
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$noteGlobal=$noteGlobal+$note; 
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img11$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$noteGlobal=$noteGlobal+$note;
			$img="";	
		}

	}
	print "<tr><td>Vie Scolaire </td><td><input type=text name='noteviescolaire$j' value='$note' size=4  onchange='somme($j);cacheTelechargement();'  /> /20";
	print "$img";
	print "</td><td>AB <input type=checkbox value='AB' onclick=\"document.formulaire.noteviescolaire$j.value='AB';somme($j);document.getElementById('img11$j').style.visibility='hidden';cacheTelechargement();\"    title='Absent' />";
	print "<input type=hidden name='codviescolaire$j' value='$codeEpreuve' />";
	print "</td></tr>";
	// ---------------------------------------------------------------------------------
	// --------------------------------------------------------------------------------
	// A2R
	
if ($noteA2 == 1) {
	$note=rechercheB2IEleve($idEleve,$idClasse,"A2R");
	if ($note != "") {
		$img="";	
	}else{
		$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='imgR15$j'  align=center/>";
	}
	$codeEpreuve=recupCodeEpreuve($serie,"A2");
	print "<tr><td>Socle Niveau A2 </td><td><input type=text name='noteA2R$j' value='$note' size=4  onchange='cacheTelechargement()'  />";
	print "$img";
	print "</td><td>AB <input type=checkbox value='AB' onclick=\"document.formulaire.noteA2R$j.value='AB';document.getElementById('imgR15$j').style.visibility='hidden';cacheTelechargement()\"    title='Absent' />";
	print "VA <input type=checkbox value='VA' onclick=\"document.formulaire.noteA2R$j.value='VA';document.getElementById('imgR15$j').style.visibility='hidden';cacheTelechargement()\" title='Validé' />";
	print "NV <input type=checkbox value='NV' onclick=\"document.formulaire.noteA2R$j.value='NV';document.getElementById('imgR15$j').style.visibility='hidden';cacheTelechargement()\" title='Non Validé' />";

	print "<input type=hidden name='codeA2R$j' value='$codeEpreuve' />";
	print "</td></tr>";
}
	// ---------------------------------------------------------------------------------


	// --------------------------------------------------------------------------------
	// Histoire - Géographie  Education civique 
if (($notehistgeo == "1") && ($noteeducivi == "1")) {
	$tab=rechercheMatiereBrevet("Histoire - Géographie - Civique",$idClasse);
	$nb=0;$noteT="";$note="";
	for($i=0;$i<count($tab);$i++) {
		$idMatiere=$tab[$i][0];
		$note=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
		if ($note != "") {
                        $coef=recupCoefBrevet($idClasse,"Histoire - Géographie - Civique",$idMatiere);
                        $noteT=$noteT + ($note*$coef);
                        $nb+=$coef;
                }
	}
	$codeEpreuve=recupCodeEpreuve($serie,"histoireGeo");
	if ($nb > 0) { $note = $noteT / $nb ; }
	if ($note != "") { 
		$note=arrondiAuDemi($note); 
		$note=number_format($note,2,'.','');
		if ($note < 10) { $note="0".$note; } 
		$img="";	
	}else{
		$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);
		if (trim($note) == "") {
			$img="&nbsp;&nbsp;&nbsp; <img src='image/commun/actif.gif' id='img16$j'  align=center/>";
		}
		if ($note > 0) {
			$note=arrondiAuDemi($note); 
			$note=number_format($note,2,'.','');
			if ($note < 10) { $note="0".$note; }
			$img="";	
		}

	}	
	print "<tr><td>Histoire - Géo - Education civique </td><td><input type=text name='notehistgeo$j' value='$note' size=4  onchange='cacheTelechargement()'  /> /20";
	print "$img";
	print "</td><td>AB <input type=checkbox value='AB' onclick=\"document.formulaire.notehistgeo$j.value='AB';document.getElementById('img16$j').style.visibility='hidden';cacheTelechargement()\"    title='Absent' />";
	print "<input type=hidden name='codehistgeo$j' value='$codeEpreuve' />";
	print "</td></tr>";
}
	// ---------------------------------------------------------------------------------
	//



	//
	//TOTAL
	$noteGlobal=number_format($noteGlobal,2,'.','');
	if ($noteGlobal < 100) {$noteGlobal="0".$noteGlobal; }
	print "<tr><td><b>TOTAL<b> </td><td><input type=text name='total$j' value='$noteGlobal' size=4  onchange='cacheTelechargement()'  /> ";
	print "<input type=hidden name='tot$j' value='TOT' />";

	print "<tr><td colspan=3><hr></td></tr>";


}
	
	print "</table>";

	/*
	config_param_ajout($controle,"contnotanet");
	config_param_ajout($notehistarts,"notehistarts");
	config_param_ajout($noteA2,"noteA2");
	config_param_ajout($notehistgeo,"notehistgeo");
	config_param_ajout($noteeducivi,"noteeducivi");
	*/


PgClose();
?>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGENR." les notes" ?>","create3"); //text,nomInput</script>

<input type=hidden name='notehistarts' value='<?php print $notehistarts ?>' />
<input type=hidden name='noteA2' value='<?php print $noteA2 ?>' />
<input type=hidden name='notehistgeo' value='<?php print $notehistgeo ?>' />
<input type=hidden name='noteeducivi' value='<?php print $noteeducivi ?>' />

<input type=hidden name='nbeleve' value='<?php print count($eleveT) ?>' />
<input type=hidden name='serie' value="<?php print $serie ?>" />
<input type=hidden name='saisie_classe' value="<?php print $idClasse ?>" />
<input type=hidden name='controle' value="<?php print $controle ?>" />
</form>
<br><br><center><i><font class=T1>AB:ABsent  - DI:DIspensé  - NN:Non Noté <br>VA: Niveau A2 de Langue régionale valide <br>NV: Niveau A2 de langue regionale non valide</font></i></center><br><br>

<?php
include_once("./librairie_php/lib_conexpersistant.php"); 
connexpersistance("color:black;font-weight:bold;font-size:11px;text-align: center;"); 
?>

<!-- // fin form -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<script language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</script>
</BODY>
</HTML>
