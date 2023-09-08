<?php
session_start();
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");

$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
        $anneeScolaire=$_POST["anneeScolaire"];
        setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}

$matricule=$_POST["matricule"];
$classe=$_POST["classe"];
$adresse=$_POST["adresse"];
$adresseinfo=$_POST["adresseinfo"];
$civeleve=$_POST["civeleve"];
$classe=$_POST["classe"];
$membre=$_POST["membre"];

setcookie("publipomatricule","$matricule");
setcookie("publipoadresse","$adresse");
setcookie("publipoadresseinfo","$adresseinfo");
setcookie("publipomembre","$membre");
setcookie("publicivilite","$civeleve");
setcookie("publiclasse","$classe");

if ($id != 1) {
	set_time_limit(900);
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
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Publipostage" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >

<?php





if (isset($_POST["consult1"])) {
	$idClasse=$_POST["saisie_classe"];
	$membre=$_POST["membre"];
	$classe=chercheClasse_nom($idClasse);
	$nbeleve=count($eleveT);
	$type=$membre;
	$matricule=$_POST["matricule"];
	$classe=$_POST["classe"];
	$adresse=$_POST["adresse"];
	$adresseinfo=$_POST["adresseinfo"];
}

$couleur=$_POST["couleur"];
$id_vignette=$_POST["id_vignette"];

if (isset($_POST["consult2"])) {
	$type=$_POST["saisie_type"];
	switch($type) {
  	      	case 'ENS' :
		        $membre="Enseignant";
	        	break;
		case 'ADM':
	        	$membre="Direction";
		        break;
	        case 'MVS' :
		        $membre="Vie Scolaire";
		        break;
	        case 'PER' :
		        $membre="Personnel";
	        	break;
	        case 'TUT' :
		        $membre="Tuteur de Stage";
		        break; 
        }
}





define('FPDF_FONTPATH','./librairie_pdf/fpdf/font2/');
include_once('./librairie_pdf/fpdf/fpdf3.php');
include_once('./librairie_pdf/etiquette.php');
/*--------------------------------------------------------------------------------
Pour créer l'objet on a 2 manières :
soit on donne les valeurs d'un format personnalisé en les passant dans un tableau
soit on donne le nom d'un format AVERY
--------------------------------------------------------------------------------*/

// Exemple avec un format personnalisé
if ($id_vignette == 1) {
	$pdf = new PDF_Label(array('paper-size'=>'A4', 'metric'=>'mm', 'marginLeft'=>1, 'marginTop'=>1, 'NX'=>3, 'NY'=>7, 'SpaceX'=>0, 'SpaceY'=>0, 'width'=>70, 'height'=>42.3, 'font-size'=>10));

}elseif($id_vignette == 2) {
	$pdf = new PDF_Label(array('paper-size'=>'A4', 'metric'=>'mm', 'marginLeft'=>1, 'marginTop'=>1, 'NX'=>2, 'NY'=>8, 'SpaceX'=>0, 'SpaceY'=>0, 'width'=>105, 'height'=>39, 'font-size'=>10));

}elseif($id_vignette == 4) {
	$pdf = new PDF_Label(array('paper-size'=>'A4', 'metric'=>'mm', 'marginLeft'=>1, 'marginTop'=>1, 'NX'=>3, 'NY'=>8, 'SpaceX'=>0, 'SpaceY'=>2, 'width'=>70, 'height'=>37, 'font-size'=>10));

}elseif($id_vignette == 5) {
	$pdf = new PDF_Label(array('paper-size'=>'A4', 'metric'=>'mm', 'marginLeft'=>1, 'marginTop'=>1, 'NX'=>2, 'NY'=>7, 'SpaceX'=>0, 'SpaceY'=>2, 'width'=>102, 'height'=>41, 'font-size'=>10));

}elseif($id_vignette == 6) {
	$pdf = new PDF_Label(array('paper-size'=>'A4', 'metric'=>'mm', 'marginLeft'=>1, 'marginTop'=>1, 'NX'=>2, 'NY'=>8, 'SpaceX'=>0, 'SpaceY'=>2, 'width'=>105, 'height'=>37, 'font-size'=>10));

}else{
	$pdf = new PDF_Label(array('paper-size'=>'A4', 'metric'=>'mm', 'marginLeft'=>1, 'marginTop'=>5, 'NX'=>2, 'NY'=>7, 'SpaceX'=>0, 'SpaceY'=>2, 'width'=>105, 'height'=>39, 'font-size'=>10));
}

// Format standard
//$pdf = new PDF_Label('L7163');

$pdf->AddPage();





switch($type) {
     	case 'ENS' :
	        $data=recupAdrMembre($type);
        	break;
	case 'ADM':
        	$data=recupAdrMembre($type);
	        break;
        case 'MVS' :
	        $data=recupAdrMembre($type);
	        break;
        case 'PER' :
	      	$data=recupAdrMembre($type);
        	break;
        case 'TUT' :
	        $data=recupAdrMembre($type);
		break; 
        case 'ELE' :
	        $data=recupAdrEleve($idClasse,$anneeScolaire);
		break;
        case 'PAR' :
	        $data=recupAdrParent1($idClasse,$anneeScolaire);
	        break;
}


	

// On imprime les étiquettes
for($i=0;$i<count($data);$i++) { //nom,prenom,adr,ccp,commune,civ,pays,numero_eleve
	if (trim($data[$i][0]) == "") { continue; }
	$nom=strtoupper($data[$i][0]);
	$prenom=ucfirst($data[$i][1]);
	$nomprenom=trunchaine2("$nom $prenom",25);
	$adr=$data[$i][2];
	$ccp=$data[$i][3];
	$commune=ucwords(strtolower($data[$i][4]));
	$pays=strtoupper($data[$i][6]);
	$civ=$data[$i][5];
	if ($civ == 'f') { $civ=2; }
	if ($civ == 'm') { $civ=0; }
	$civ=civ($civ);

	if ($adresseinfo == "PAR1") {
		$dataparent=recupAdrParent1($idClasse);
		$adr=$dataparent[$i][2];
		$ccp=$dataparent[$i][3];
		$commune=ucwords(strtolower($dataparent[$i][4]));
		$pays=strtoupper($dataparent[$i][6]);
	}

	if ($adresseinfo == "PAR2") {
		$dataparent=recupAdrParent2($idClasse);
		$adr=$dataparent[$i][2];
		$ccp=$dataparent[$i][3];
		$commune=ucwords(strtolower($dataparent[$i][4]));
		$pays=strtoupper($dataparent[$i][6]);
	}

	if ($adresseinfo == "ELE") { 
		$dataEleve=recupAdrEleve($idClasse);
		$adr=$dataEleve[$i][2];
		$ccp=$dataEleve[$i][3];
		$commune=ucwords(strtolower($dataEleve[$i][4]));
		$pays=strtoupper($dataEleve[$i][6]);
		if (!isset($_POST["civeleve"])) $civ="";
	}


	

	if ($matricule == "oui") { $matricule="N° Etudiant : ".$data[$i][7]; }
	if ($classe == "oui") { $classe="Classe : ".chercheClasse_nom($idClasse); }
	if ($adresse == "oui") {
	    	$text = sprintf("%s\n%s\n%s\n%s %s %s", "$civ $nomprenom", '', "$adr", "$ccp", "$commune", "$pays");
	}else{
		$text = sprintf("%s\n%s\n%s\n%s\n", "$civ $nomprenom", '', "$matricule", "$classe");	
	}
    	$pdf->Add_Label(utf8_decode($text));
}


if (!is_dir("./data/pdf_quantification/")) { mkdir("./data/pdf_quantification/"); }
$fichier="./data/pdf_quantification/publipostage_".$_SESSION['id_pers'].".pdf";
@unlink($fichier); // destruction avant creation
$pdf->output($fichier);
$pdf->close();

?>

<br><ul><ul>
<table><tr><td><input type=button onclick="open('visu_pdf_admin.php?id=<?php print $fichier?>','_blank','');" value="<?php print "R&eacute;cup&eacute;ration du document Publipostage" ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"></td><td><script>buttonMagicPrecedent2()</script></td></tr></table>
</ul></ul><br>


</td></tr></table>
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
// deconnexion en fin de fichier
Pgclose();
?>

</BODY>
</HTML>
