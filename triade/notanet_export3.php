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
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
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
<form method="post" name="formulaire" action="notanet_export3.php">
<!-- // debut form  -->
<?php
include_once("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
include_once("librairie_php/lib_brevet.php");
include_once("librairie_php/recupnoteperiode.php");
validerequete("menuadmin");
$cnx=cnx();

$idClasse=$_POST["saisie_classe"];
$serie=$_POST["serie"];
$ligne="";
$err=0;

$ret="\n";
if (PHP_OS == "WINNT") {  $ret="\r\n"; }

for($j=0;$j<$_POST["nbeleve"];$j++) {  
	// variable eleve
	$INE="INE$j"; $INE=$_POST[$INE];
	$nomEleve="nomEleve$j";  $nomEleve=$_POST[$nomEleve];
	$idEleve="idEleve$j";	$idEleve=$_POST[$idEleve];
	$prenomEleve="prenomEleve$j";  $prenomEleve=$_POST[$prenomEleve];
	if (trim($INE) == "") { $err=1; continue; }
	DeleteBrevet($INE,"brevetcollege");
	updateINE($INE,$nomEleve,$prenomEleve);

	$notefran="notefrancais$j";	$notefran=$_POST[$notefran];
	$codefran="codefrancais$j";	$codefran=$_POST[$codefran];
	
	$INE=strtoupper($INE);

	if (trim($notefran) != "")  { $ligne.="$INE|$codefran|$notefran|$ret"; }else{ $err=1;$mess.="1 - $INE|$codefran|$notefran| <br>"; }
	enrgBrevet($INE,$codefran,$notefran,"brevetcollege",$idEleve);


	$notemath="notehistoirearts$j";$notemath=$_POST[$notemath];
	$codemath="codehistoirearts$j";$codemath=$_POST[$codemath];
	if (trim($notemath) != "") { 	$ligne.="$INE|$codemath|$notemath|$ret";}else{ $err=1;$mess.="2 - $INE|$codemath|$notemath| <br>";  }
	enrgBrevet($INE,$codemath,$notemath,"brevetcollege",$idEleve);

	$notemath="noteMathematiques$j";$notemath=$_POST[$notemath];
	$codemath="codeMathematiques$j";$codemath=$_POST[$codemath];
	if (trim($notemath) != "") { 	$ligne.="$INE|$codemath|$notemath|$ret";}else{ $err=1;$mess.="2 - $INE|$codemath|$notemath| <br>";  }
	enrgBrevet($INE,$codemath,$notemath,"brevetcollege",$idEleve);

	$notelv1="notelv1$j";		$notelv1=$_POST[$notelv1];
	$codelv1="codelv1$j";		$codelv1=$_POST[$codelv1];
	if (trim($notelv1) != "") { 	$ligne.="$INE|$codelv1|$notelv1|$ret";}else{ $err=1;$mess.="3 - $INE|$codelv1|$notelv1| <br>";  }
	enrgBrevet($INE,$codelv1,$notelv1,"brevetcollege",$idEleve);

	$notesvt="noteSVT$j";		$notesvt=$_POST[$notesvt];
	$codesvt="codeSVT$j";		$codesvt=$_POST[$codesvt];
	if (trim($notesvt) != "") { 	$ligne.="$INE|$codesvt|$notesvt|$ret";}else{ $err=1;$mess.="4 - $INE|$codesvt|$notesvt| <br>";  }
	enrgBrevet($INE,$codesvt,$notesvt,"brevetcollege",$idEleve);
	
	$notephy="notephysChimi$j";	$notephy=$_POST[$notephy];
	$codephy="codephysChimi$j";	$codephy=$_POST[$codephy];
	if (trim($notephy) != "") { 	$ligne.="$INE|$codephy|$notephy|$ret";}else{ $err=1;$mess.="5 - $INE|$codephy|$notephy| <br>";  }
	enrgBrevet($INE,$codephy,$notephy,"brevetcollege",$idEleve);
	
	$noteeps="noteeps$j";		$noteeps=$_POST[$noteeps];
	$codeeps="codeeps$j";		$codeeps=$_POST[$codeeps];
	if (trim($noteeps) != "") { 	$ligne.="$INE|$codeeps|$noteeps|$ret";}else{ $err=1;$mess.="6 - $INE|$codeeps|$noteeps| <br>";  }
	enrgBrevet($INE,$codeeps,$noteeps,"brevetcollege",$idEleve);
	
	$noteart="notearts$j";		$noteart=$_POST[$noteart];
	$codeart="codearts$j";		$codeart=$_POST[$codeart];
	if (trim($noteart) != "") { 	$ligne.="$INE|$codeart|$noteart|$ret";}else{ $err=1;$mess.="7 - $INE|$codeart|$noteart| <br>";  }
	enrgBrevet($INE,$codeart,$noteart,"brevetcollege",$idEleve);
	
	$notemuc="notemusic$j";		$notemuc=$_POST[$notemuc];
	$codemuc="codemusic$j";		$codemuc=$_POST[$codemuc];
	if (trim($notemuc) != "") { 	$ligne.="$INE|$codemuc|$notemuc|$ret";}else{ $err=1;$mess.="8 - $INE|$codemuc|$notemuc| <br>";  }
	enrgBrevet($INE,$codemuc,$notemuc,"brevetcollege",$idEleve);
	
	$notetech="notetechno$j";	$notetech=$_POST[$notetech];
	$codetech="codetechno$j";	$codetech=$_POST[$codetech];
	if (trim($notetech) != "") { 	$ligne.="$INE|$codetech|$notetech|$ret";}else{ $err=1;$mess.="9 - $INE|$codetech|$notetech| <br>";  }
	enrgBrevet($INE,$codetech,$notetech,"brevetcollege",$idEleve);

	if ($serie == "LV2") {
		$notelv2="noteLV2$j";		$notelv2=$_POST[$notelv2];
		$codelv2="codeLV2$j";		$codelv2=$_POST[$codelv2];
		if (trim($notelv2) != "") { 	$ligne.="$INE|$codelv2|$notelv2|$ret";}else{ $err=1;$mess.="10 - $INE|$codelv2|$notelv2| <br>";  }
		enrgBrevet($INE,$codelv2,$notelv2,"brevetcollege",$idEleve);
	}

	if ($serie == "DP6") {
		$notedp6="noteDP6h$j";		$notedp6=$_POST[$notedp6];
		$codedp6="codeDP6h$j";		$codedp6=$_POST[$codedp6];
		if (trim($notedp6) != "") { 	$ligne.="$INE|$codedp6|$notedp6|$ret";}else{ $err=1;$mess.="11 - $INE|$codedp6|$notedp6| <br>";  }
		enrgBrevet($INE,$codedp6,$notedp6,"brevetcollege",$idEleve);
	}

	$notescol="noteviescolaire$j";	$notescol=$_POST[$notescol];
	$codescol="codviescolaire$j";	$codescol=$_POST[$codescol];
	if (trim($notescol) != "") { 	$ligne.="$INE|$codescol|$notescol|$ret";}else{ $err=1;$mess.="12 - $INE|$codescol|$notescol| <br>";  }
	enrgBrevet($INE,$codescol,$notescol,"brevetcollege",$idEleve);
	
	$noteopt="noteOPT$j";		$noteopt=$_POST[$noteopt];
	$codeopt="codeOPT$j";		$codeopt=$_POST[$codeopt];
	if (trim($noteopt) != "") { 	$ligne.="$INE|$codeopt|$noteopt|$ret";}
	enrgBrevet($INE,$codeopt,$noteopt,"brevetcollege",$idEleve);

/*	$noteb2i="noteb2i$j";		$noteb2i=$_POST[$noteb2i];
	$codeb2i="codeb2i$j";		$codeb2i=$_POST[$codeb2i];
	if (trim($noteb2i) != "") { 	$ligne.="$INE|$codeb2i|$noteb2i|$ret";}else{ $err=1;$mess.="13 - $INE|$codeb2i|$noteb2i| <br>";  }
	enrgBrevet($INE,$codeb2i,$noteb2i,"brevetcollege",$idEleve);
 */	
	$noteA2="noteA2$j";		$noteA2=$_POST[$noteA2];
	$codeA2="codeA2$j";		$codeA2=$_POST[$codeA2];
	if (trim($noteA2) != "") { 	$ligne.="$INE|$codeA2|$noteA2|$ret";}
	enrgBrevet($INE,$codeA2,$noteA2,"brevetcollege",$idEleve);

	$notehist="notehistgeo$j";	$notehist=$_POST[$notehist];
	$codehist="codehistgeo$j";	$codehist=$_POST[$codehist];
	if (trim($notehist) != "") { 	$ligne.="$INE|$codehist|$notehist|$ret";}else{ $err=1;$mess.="15 - $INE|$codehist|$notehist| <br>";  }
	enrgBrevet($INE,$codehist,$notehist,"brevetcollege",$idEleve);
	
	$noteeduc="noteeduciv$j";	$noteeduc=$_POST[$noteeduc];
	$codeeduc="codeeduciv$j";	$codeeduc=$_POST[$codeeduc];
	if (trim($noteeduc) != "") { 	$ligne.="$INE|$codeeduc|$noteeduc|$ret";}else{ $err=1; $mess.="16 - $INE|$codeeduc|$noteeduc| <br>"; }
	enrgBrevet($INE,$codeeduc,$noteeduc,"brevetcollege",$idEleve);
	
	$notetotal="total$j";		$notetotal=$_POST[$notetotal];
	$codetot="tot$j";		$codetot=$_POST[$codetot];
	if (trim($notetotal) != "") { 	$ligne.="$INE|$codetot|$notetotal|$ret"; }else{ $err=1;$mess.="17 - $INE|$codetot|$notetotal| <br>"; }
	enrgBrevet($INE,$codetot,$notetotal,"brevetcollege",$idEleve);
}

	if ($err != 1) {
		$fichier="./data/fichier_ASCII/Triade2Notanet.txt";
		$fd=fopen("$fichier","w");
		fwrite($fd,$ligne);
		fclose($fd);
	}
PgClose();
?>

<?php
if ($err != 1) { ?>
	<center><input type=button onclick="open('telecharger.php?fichier=<?php print $fichier?>','_blank','');" value="<?php print "Récuperer le fichier Notanet" ?>" class="bouton2" ></center>
<?php  
}else{
	print "<script>location.href='notanet_export2.php?saisie_classe=$idClasse&serie=$serie&err'</script>";
	//print $mess;
}

?>
<!-- // fin form -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY>
</HTML>
