<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - F. ORY
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
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(3000);
}
?>
<!-- /************************************************************
Last updated: 09.07.2002    par Taesch  Eric
*************************************************************/ -->
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript"<?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print LANGbasededoni91 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
     <!-- // fin  -->
<?php
include_once("librairie_php/db_triade.php");
include_once("librairie_php/timezone.php");
validerequete("menuadmin");
validerequete2($_SESSION["adminplus"]);

// function appele par la suite
function eclair($x,$y){
	if (!is_array($x) || !is_array($y)){
		echo "<br><br><center>".LANGbasededoni92;
	}
	array_pad($x,count($y),"");
	array_pad($y,count($x),"");
	while(count($x) > 0){
		$in=gep_classe(array_shift($x),array_shift($y));
		if ($in == 0) {
			alertJs(LANGbasededoni93);
			print "<script>history.go(-2);</script>";
			break;
		}
	}
}

$cnx=cnx();
error($cnx);

// enregistrement dans la base de classe avec reference
// netoyage de la base gep_class;
vide_gep_classe();
eclair($_POST["saisie_classe"],$_POST["saisie_ref"]);
// fin d'enregistrement
$nbelevetotal=0;
$nbelevedejaffecte=0;



if (trim($_POST["typefichier"]) == "excel" ) {
	$fic_xls=$_POST["fichier"];
	include_once('./librairie_php/reader.php');
	$data = new Spreadsheet_Excel_Reader();
//	$data->setOutputEncoding('CP1251');
	$data->setOutputEncoding('UTF-8');
	$data->read($fic_xls);
/*
	1) N° MATRIC  
	2) GRILLE 
	3) CLASSE * 
	4) ANNÉE 
	5) NOM * 
	6) PRÉNOM * 
	7) SEXE  
	8) ADRESSE  
	9) CP  
	10) LOCALITÉ  
	11) ENT. COM.  
	12) DATE NAIS. * 
	13) LIEU NAIS.  
	14) NATIONALITE  
	15) ETABLIS_AN  
	16) ORIGINE  
	17) C. PHILOS. 
 	18) 2E L  
	19) INT /EXT  
	20) MIDI (CTD)  
	21) PERS. RESP. 
	22) PRÉNOM RESP.  
	23) D.P.  
	24) PROFESSION  
	25) CONJOINT  
	26) PROFES.  
	27) TÉL. PRIVÉ  
	28) TÉL.TRAV.P  
	29) GSM PÈRE  
	30) TÉL TRAV. M.  
	31) GSM MÈRE  
	32) RAPPORT 1È  
	33) RAPPORT 2È  
	34) INFO  
	35) DATE INSCR.  
	36) CASIER  
	37) RAPPORT 3È  
	38) PASSE  
	39) REMÉD  
	40) RAPPOT 1BI  
	41) RAPPORT 2B  
*
*/
		
	for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {


			$classe=recherche_gep_classe(trim($data->sheets[0]['cells'][$i][3]));
			
			$passwd="";
			$passwd_eleve="";
			$date_naissance=$data->sheets[0]['cells'][$i][12];

			if ((trim($passwd) == "") || (! isset($passwd)))  {
					$passwd=passwd_random(); // creation du mot de passe
					$passwd_enr=$passwd;
			}else {
					$passwd_enr=$passwd;
			}

			if ((trim($passwd_eleve) == "") || (! isset($passwd_eleve)) ||  (trim($passwd_eleve) == "null") )  {
					$passwd_eleve=passwd_random(); // creation du mot de passe
					$passwd_eleve_enr=$passwd_eleve;
			}else {
					$passwd_eleve_enr=$passwd_eleve;
			}


			if ($date_naissance == "") {
					$date_naissance=dateDMY();
			}

			if (strtoupper($data->sheets[0]['cells'][$i][19]) == "E") {
				$regime="Externe";
			}

			if (strtoupper($data->sheets[0]['cells'][$i][19]) == "I") {
				$regime="Interne";
			}

		
			if (strlen(trim($classe))) {
					$nbelevetotal++;
					// création du tableau de hash contenant les paramètres de la fonction create_eleve
					$params[ne]=            strtolower(trim(addslashes($data->sheets[0]['cells'][$i][5])));
					$params[pe]=            strtolower(trim(addslashes($data->sheets[0]['cells'][$i][6])));
					$params[ce]=            $classe;
					$params[lv1]=           strtolower(trim(addslashes($data->sheets[0]['cells'][$i][38])));
					$params[lv2]=           strtolower(trim(addslashes($data->sheets[0]['cells'][$i][18])));
					$params[option]=       	"";
					$params[regime]=        $regime;
					$params[naiss]=         $date_naissance;   // attend jj/mm/aaaa
					$params[lieunais]=      strtolower(trim(addslashes($data->sheets[0]['cells'][$i][13])));
					$params[nat]=           strtolower(trim(addslashes($data->sheets[0]['cells'][$i][14])));
					$params[mdp]=           $passwd_enr;
					$params[mdpeleve]=	$passwd_eleve_enr;
					$params[nt]=            strtolower(trim(addslashes($data->sheets[0]['cells'][$i][21])));
					$params[pt]=		strtolower(trim(addslashes($data->sheets[0]['cells'][$i][22])));
					$params[nadr1]=        	"";
					$params[adr1]=        	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][8])));
					$params[cpadr1]=      	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][9])));
					$params[commadr1]=     	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][10])));
					$params[nadr2]=         "";
					$params[adr2]=          "";
					$params[cpadr2]=       	"";
					$params[commadr2]=     	"";
					$params[tel]=          	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][27])));
					$params[profp]=        	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][24])));
					$params[telprofp]=     	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][28])));
					$params[profm]=         strtolower(trim(addslashes($data->sheets[0]['cells'][$i][26])));
					$params[telprofm]=     	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][30])));
					$params[nomet]=        	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][15])));
					$params[numet]=        	"";
					$params[cpet]=         	"";
					$params[commet]=    	"";
					$params[numero_eleve]=  strtolower(trim(addslashes($data->sheets[0]['cells'][$i][1])));
					$params[email]=  	"";
					$params[classe_ant]=  	"";
					$params[annee_ant]=  	"";
					$params[civ_1]=  	"";
					$params[civ_2]=  	"";
					$params[nom_resp2]=	"";
					$params[prenom_resp2]=	"";
					$params[tel_port_1]=	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][29])));
					$params[tel_port_2]=	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][31])));


					// nouvelle version de create_eleve()
					$ascii=1;
					$cr=@create_eleve($params,$ascii);
					if ($cr == 1) {
						$f_pass=fopen("./data/fic_pass.txt","a+");
				     	    	fwrite($f_pass,strtolower(trim($data->sheets[0]['cells'][$i][5])).";".strtolower(trim($data->sheets[0]['cells'][$i][6]." ".$data->sheets[0]['cells'][$i][7])).";".$passwd_enr.";".$passwd_eleve_enr."<br />");
			     			fclose($f_pass);
						$nbeleveaffecte++;
					}
					if ($cr == -3) {
						$nbelevedejaffecte++;
					}

		}else{
					$nbelevetotal++;
					// création du tableau de hash contenant les paramètres de la fonction create_eleve
					$params[ne]=            strtolower(trim(addslashes($data->sheets[0]['cells'][$i][5])));
					$params[pe]=            strtolower(trim(addslashes($data->sheets[0]['cells'][$i][6])));
					$params[ce]=            $classe;
					$params[lv1]=           strtolower(trim(addslashes($data->sheets[0]['cells'][$i][38])));
					$params[lv2]=           strtolower(trim(addslashes($data->sheets[0]['cells'][$i][18])));
					$params[option]=       	"";
					$params[regime]=        $regime;
					$params[naiss]=         $date_naissance;   // attend jj/mm/aaaa
					$params[lieunais]=      strtolower(trim(addslashes($data->sheets[0]['cells'][$i][13])));
					$params[nat]=           strtolower(trim(addslashes($data->sheets[0]['cells'][$i][14])));
					$params[mdp]=           $passwd_enr;
					$params[mdpeleve]=	$passwd_eleve_enr;
					$params[nt]=            strtolower(trim(addslashes($data->sheets[0]['cells'][$i][21])));
					$params[pt]=		strtolower(trim(addslashes($data->sheets[0]['cells'][$i][22])));
					$params[nadr1]=        	"";
					$params[adr1]=        	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][8])));
					$params[cpadr1]=      	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][9])));
					$params[commadr1]=     	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][10])));
					$params[nadr2]=         "";
					$params[adr2]=          "";
					$params[cpadr2]=       	"";
					$params[commadr2]=     	"";
					$params[tel]=          	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][27])));
					$params[profp]=        	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][24])));
					$params[telprofp]=     	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][28])));
					$params[profm]=         strtolower(trim(addslashes($data->sheets[0]['cells'][$i][26])));
					$params[telprofm]=     	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][30])));
					$params[nomet]=        	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][15])));
					$params[numet]=        	"";
					$params[cpet]=         	"";
					$params[commet]=    	"";
					$params[numero_eleve]=  strtolower(trim(addslashes($data->sheets[0]['cells'][$i][1])));
					$params[email]=  	"";
					$params[classe_ant]=  	"";
					$params[annee_ant]=  	"";
					$params[civ_1]=  	"";
					$params[civ_2]=  	"";
					$params[nom_resp2]=	"";
					$params[prenom_resp2]=	"";
					$params[tel_port_1]=	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][29])));
					$params[tel_port_2]=	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][31])));


					// nouvelle create eleve sans classe
					$ascii=1;
					$cr=@create_eleve_sans_classe($params,$ascii);
					if ($cr == 1) {
						$f_pass=fopen("./data/fic_pass.txt","a+");
						fwrite($f_pass,strtolower(trim($data->sheets[0]['cells'][$i][5])).";".strtolower(trim($data->sheets[0]['cells'][$i][6]." ".$data->sheets[0]['cells'][$i][7])).";".$passwd_enr.";".$passwd_eleve_enr."<br />");
						fclose($f_pass);
						$nbeleverreur++;
					}
					if ($cr == -3) {
						$nbelevedejaffecte++;
					}
				
			}
		    
} 
			// creation ou mise a jour du fichier log  avec prise en
			$today=dateDMY();
			$fichier_s=fopen("./".REPADMIN."/data/fic_opinion.txt","a+");
			$donnee=fwrite($fichier_s,"<BR>Message du : <FONT color=red>$today</font> De :<FONT color=red> $_SESSION[nom] $_SESSION[prenom]</FONT> <BR>Membre : <font color=red> $_SESSION[membre] </FONT><BR> <B>Message :</B> <font color=red> NOUVELLE BASE </font> - avec fichier EXCEL <BR>  Etablissement : <font color=red>".REPECOLE."</font> ");
			fclose($fichier_s);




}




Pgclose();
?>

<br />
<ul>
- <?php print LANGBASE6bis?> : <?php print $nbelevetotal?><br>
- <?php print LANGBASE7?> : <?php print $nbeleveaffecte?><br>
- <?php print LANGBASE7bis ?> : <?php print $nbelevedejaffecte?><br>
- <?php print LANGBASE8?> : <?php print $nbeleverreur?><br><br>
- <?php print LANGBASE9?> <br /> (<?php print LANGBASE8bis ?>)<br /><br />
<?php
if (file_exists("./data/fic_pass.txt")) {
?>
<input type=button class=BUTTON value="<?php print LANGBT40?>" onclick="open('recupepw.php','_blank','')">
<?php } ?>
<br><br>
<font color=red size=2><?php print LANGBASE17?></font>
<br /><br />
	<script language=JavaScript>buttonMagic("<?php print LANGBT41." sans création des groupes "?>","acces2.php","_parent","","");</script>
	<script language=JavaScript>buttonMagic("<?php print "Création des groupes" ?>","base_de_donne_importation414.php","_parent","","");</script>

<br><br />
</ul>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
</BODY></HTML>
