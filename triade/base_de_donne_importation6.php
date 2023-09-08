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
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(3000);
}
?>
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

if ($_POST["typefichier"] == "excel" ) {

		include_once("./librairie_php/lib_import_csv.php");
		$fic_ascii=$_POST["fichier"];
		$fp = fopen ("$fic_ascii","r");
		$str=file_get_contents("$fic_ascii");
		$rows=CSV2Array($str);
		for($i=0;$i<count($rows);$i++)  {
				/* 
1) nom  2) prénom  3) classe  
4) régime 5) date naissance  6) Lieu de naissance  
7) nationalité  8) Civilité tuteur  9) nom tuteur  
10) prénom tuteur  11) adresse 1 12) code postal 1 
13) commune 1 14) Tèl. Portable (1) 15) Civilité Pers. (2)  
16) Nom resp. (2)  17) Prénom resp. (2)  18) adresse 2 
19) code postal 2 20) commune 2 21) Tèl. Portable (2) 
22) téléphone tuteur  23) Tel. élève 24) profession père 
25) téléphone profession père 26) profession mère 27) téléphone profession mère 
28) nom établissement 29) numéro établissement 30) lv1 
31) lv2 32) option  33) Numéro élève  
34) mot de passe parent  35) Email tuteur  36) Email élève  
37) Classe antérieure  38) Année antérieure  39) mot de passe élève 
40) Adresse élève  41) Commune élève  42) CCP élève  
43) Tél. fixe élève  	44) email_universitaire	 45) sexe elève 
				*/
			$classe=recherche_gep_classe(trim($rows[$i][2]));
			$passwd=trim($rows[$i][33]);
			$passwd_eleve=trim($rows[$i][38]);
			$date_naissance=trim($rows[$i][4]);

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

			if (strtoupper(trim($rows[$i][3])) == "EXT") {
				$regime="Externe";
			}

			if (strtoupper(trim($rows[$i][3])) == "INT") {
				$regime="Interne";
			}

			if (strtoupper(trim($rows[$i][3])) == "DP") {
				$regime="Demi Pension";
			}


			$rows[$i][0]=preg_replace('/"/',"",$rows[$i][0]);
			$rows[$i][1]=preg_replace('/"/',"",$rows[$i][1]);

			if (strlen(trim($classe))) {
				$nbelevetotal++;


					// création du tableau de hash contenant les paramètres de la fonction create_eleve
					$params[ne]=            strtolower(trim(addslashes($rows[$i][0])));
					$params[pe]=            strtolower(trim(addslashes($rows[$i][1])));
					$params[ce]=            $classe;
					$params[regime]=        $regime;
					$params[naiss]=         $date_naissance;   // attend jj/mm/aaaa
					$params[lieunais]=	strtolower(trim(addslashes($rows[$i][5])));
					$params[nat]=           strtolower(trim(addslashes($rows[$i][6])));
					$params[civ_1]=  	civ2($rows[$i][7]);
					$params[nt]=            strtolower(trim(addslashes($rows[$i][8])));
					$params[pt]=		strtolower(trim(addslashes($rows[$i][9])));
					$params[adr1]=        	strtolower(trim(addslashes($rows[$i][10])));
					$params[cpadr1]=      	strtolower(trim(addslashes($rows[$i][11])));
					$params[commadr1]=     	strtolower(trim(addslashes($rows[$i][12])));
					$params[tel_port_1]=   	strtolower(trim(addslashes($rows[$i][13])));
					$params[civ_2]=  	civ2($rows[$i][14]);
					$params[nom_resp2]=	strtolower(trim(addslashes($rows[$i][15])));
					$params[prenom_resp2]=	strtolower(trim(addslashes($rows[$i][16])));
					$params[adr2]=          strtolower(trim(addslashes($rows[$i][17])));
					$params[cpadr2]=       	strtolower(trim(addslashes($rows[$i][18])));
					$params[commadr2]=     	strtolower(trim(addslashes($rows[$i][19])));
					$params[tel_port_2]=   	strtolower(trim(addslashes($rows[$i][20])));
					$params[tel]=          	strtolower(trim(addslashes($rows[$i][21])));
					$params[tel_eleve]=	trim($rows[$i][22]);
					$params[profp]=        	strtolower(trim(addslashes($rows[$i][23])));
					$params[telprofp]=     	strtolower(trim(addslashes($rows[$i][24])));
					$params[profm]=        	strtolower(trim(addslashes($rows[$i][25])));
					$params[telprofm]=     	strtolower(trim(addslashes($rows[$i][26])));
					$params[nomet]=        	strtolower(trim(addslashes($rows[$i][27])));
					$params[numet]=        	strtolower(trim(addslashes($rows[$i][28])));
					$params[cpet]=         	"";
					$params[commet]=    	"";
					$params[lv1]=        	strtolower(trim(addslashes($rows[$i][29])));
					$params[lv2]=        	strtolower(trim(addslashes($rows[$i][30])));
					$params[option]=        strtolower(trim(addslashes($rows[$i][31])));
					$params[numero_eleve]=  trim(addslashes($rows[$i][32]));
					$params[mdp]=           $passwd;
					$params[email]=  	trim(addslashes($rows[$i][34]));
					$params[mail_eleve]=  	trim(addslashes($rows[$i][35]));
					$params[classe_ant]=  	strtolower(trim($rows[$i][36]));
					$params[annee_ant]=  	trim(addslashes($rows[$i][37]));
					$params[mdpeleve]=	$passwd_eleve;
					$params[adr_eleve]=  	trim(addslashes($rows[$i][40]));
					$params[commune_eleve]= trim(addslashes($rows[$i][41]));
					$params[ccp_eleve]=  	trim(addslashes($rows[$i][42]));
					$params[tel_fixe_eleve]=trim(addslashes($rows[$i][43]));
					$params[mailpro_eleve]=	trim(addslashes($rows[$i][44]));
					$params[sexe]=		strtolower(trim(addslashes($rows[$i][45])));
					$params[annee_scolaire] = $_POST["annee_scolaire"];

					// nouvelle version de create_eleve()
					$ascii=1;
					$cr=@create_eleve($params,$ascii);
					if ($cr == 1) {
						$f_pass=fopen("./data/fic_pass.txt","a+");
				     	    	fwrite($f_pass,strtolower(trim($rows[$i][0])).";".strtolower(trim($rows[$i][1])).";".$passwd_enr.";".$passwd_eleve_enr."<br />");
			     			fclose($f_pass);
						$nbeleveaffecte++;
					}
					if ($cr == -3) {
						$nbelevedejaffecte++;
					}

		}else{
					$nbelevetotal++;
					// création du tableau de hash contenant les paramètres de la fonction create_eleve
					$params[ne]=            strtolower(trim(addslashes($rows[$i][0])));
					$params[pe]=            strtolower(trim(addslashes($rows[$i][1])));
					$params[ce]=            $classe;
					$params[regime]=        $regime;
					$params[naiss]=         $date_naissance;   // attend jj/mm/aaaa
					$params[lieunais]=	strtolower(trim(addslashes($rows[$i][5])));
					$params[nat]=           strtolower(trim(addslashes($rows[$i][6])));
					$params[civ_1]=  	civ2($rows[$i][7]);
					$params[nt]=            strtolower(trim(addslashes($rows[$i][8])));
					$params[pt]=		strtolower(trim(addslashes($rows[$i][9])));
					$params[adr1]=        	strtolower(trim(addslashes($rows[$i][10])));
					$params[cpadr1]=      	strtolower(trim(addslashes($rows[$i][11])));
					$params[commadr1]=     	strtolower(trim(addslashes($rows[$i][12])));
					$params[tel_port_1]=   	strtolower(trim(addslashes($rows[$i][13])));
					$params[civ_2]=  	civ2($rows[$i][14]);
					$params[nom_resp2]=	strtolower(trim(addslashes($rows[$i][15])));
					$params[prenom_resp2]=	strtolower(trim(addslashes($rows[$i][16])));
					$params[adr2]=          strtolower(trim(addslashes($rows[$i][17])));
					$params[cpadr2]=       	strtolower(trim(addslashes($rows[$i][18])));
					$params[commadr2]=     	strtolower(trim(addslashes($rows[$i][19])));
					$params[tel_port_2]=   	strtolower(trim(addslashes($rows[$i][20])));
					$params[tel]=          	strtolower(trim(addslashes($rows[$i][21])));
					$params[tel_eleve]=	trim($rows[$i][22]);
					$params[profp]=        	strtolower(trim(addslashes($rows[$i][23])));
					$params[telprofp]=     	strtolower(trim(addslashes($rows[$i][24])));
					$params[profm]=        	strtolower(trim(addslashes($rows[$i][25])));
					$params[telprofm]=     	strtolower(trim(addslashes($rows[$i][26])));
					$params[nomet]=        	strtolower(trim(addslashes($rows[$i][27])));
					$params[numet]=        	strtolower(trim(addslashes($rows[$i][28])));
					$params[cpet]=         	"";
					$params[commet]=    	"";
					$params[lv1]=        	strtolower(trim(addslashes($rows[$i][29])));
					$params[lv2]=        	strtolower(trim(addslashes($rows[$i][30])));
					$params[option]=        strtolower(trim(addslashes($rows[$i][31])));
					$params[numero_eleve]=  trim(addslashes($rows[$i][32]));
					$params[mdp]=           $passwd;
					$params[email]=  	trim(addslashes($rows[$i][34]));
					$params[mail_eleve]=  	trim(addslashes($rows[$i][35]));
					$params[classe_ant]=  	strtolower(trim($rows[$i][36]));
					$params[annee_ant]=  	trim(addslashes($rows[$i][37]));
					$params[mdpeleve]=	$passwd_eleve;
					$params[adr_eleve]=  	trim(addslashes($rows[$i][40]));
					$params[commune_eleve]= trim(addslashes($rows[$i][41]));
					$params[ccp_eleve]=  	trim(addslashes($rows[$i][42]));
					$params[tel_fixe_eleve]=trim(addslashes($rows[$i][43]));
					$params[mailpro_eleve]=	trim(addslashes($rows[$i][44]));
					$params[sexe]=		strtolower(trim(addslashes($rows[$i][45])));
					$params[annee_scolaire] = $_POST["annee_scolaire"];
					// nouvelle create eleve sans classe
					$ascii=1;
					$cr=@create_eleve_sans_classe($params,$ascii);
					if ($cr == 1) {
						$f_pass=fopen("./data/fic_pass.txt","a+");
						fwrite($f_pass,strtolower(trim($rows[$i][0])).";".strtolower(trim($rows[$i][1])).";".$passwd_enr.";".$passwd_eleve_enr."<br />");
						fclose($f_pass);
						$nbeleverreur++;
					}
					if ($cr == -3) {
						$nbelevedejaffecte++;
					}
			}
		}
			fclose($fp);
			// creation ou mise a jour du fichier log  avec prise en
			$today=dateDMY();
			$fichier_s=fopen("./data/fic_opinion.txt","a+");
			$donnee=fwrite($fichier_s,"<BR>Message du : <FONT color=red>$today</font> De :<FONT color=red> $_SESSION[nom] $_SESSION[prenom]</FONT> <BR>Membre : <font color=red> $_SESSION[membre] </FONT><BR> <B>Message :</B> <font color=red> NOUVELLE BASE </font> - avec fichier EXCEL <BR>  Etablissement : <font color=red>".REPECOLE."</font> ");
			fclose($fichier_s);

			// suppression du fichier ASCII
		//	@unlink($fic_ascii);


}


if ($_POST["typefichier"] == "txt" ) {

	$fic_ascii=$_POST["fichier"];
	$phraseok="<br /><center><?php print LANGbasededoni94 ?><br /></center><br />";
	//analyse du fichier ASCII
	$file=fopen ("$fic_ascii", "r");
	$lines = file ("$fic_ascii");
	// Affiche toutes les lignes du tableau comme code HTML, avec les numéros de ligne
	@unlink("./data/fic_pass.txt");
	$f_pass=fopen("./data/fic_pass.txt","w");
	fwrite($f_pass,"");
	fclose($f_pass);

	foreach ($lines as $line_num => $line) {
		$passwd="";
		$line=preg_replace('/"/',"",$line);
		list($nomE,$prenomE,$classe,$regime,$date_naissance,$lieunaissance,$nationalite,$civ1,$nom_tuteur,$prenom_tuteur,$adr1,$code_postal,$commune,$telportable1,$civ2,$nom2,$prenom2,$adr2,$code_postal2,$commune2,$telportable2,$tel,$tel_eleve,$prof_pere,$tel_prof_pere,$prof_mere,$tel_prof_mere,$nom_etablissement,$num_etablissement,$lv1,$lv2,$option,$numeleve,$passwd,$email,$email_eleve,$classe_ant,$annee_ant,$passwd_eleve,$adr_eleve,$commune_eleve,$ccp_eleve,$tel_fixe_eleve,$email_universitaire)= preg_split('/;/', $line, 43);
		$classe=recherche_gep_classe($classe);
		$lv2=preg_replace("/;/","", $lv2);
		$lv1=preg_replace("/;/","", $lv1);
		if ((trim($passwd) == "") || (! isset($passwd)))  {
			$passwd=passwd_random();; // creation du mot de passe
			$passwd_enr=$passwd;
		}else {
			$passwd_enr=$passwd;
		}

		if ((trim($passwd_eleve) == "") || (! isset($passwd_eleve)) ||  (trim($passwd_eleve) == "null") )  {
			$passwd_eleve=passwd_random();; // creation du mot de passe
			$passwd_eleve_enr=$passwd_eleve;
		}else {
			$passwd_eleve_enr=$passwd_eleve;
		}


		if ($date_naissance == "") {
			$date_naissance=dateDMY();
		}

		if ($regime == "EXT") {
				$regime="Externe";
		}

		if ($regime == "INT") {
				$regime="Interne";
		}

		if ($regime == "DP") {
				$regime="Demi-pension";
		}

		$nomE=preg_replace('/"/',"",$nomE);
		$prenomE=preg_replace('/"/',"",$prenomE);

		if (strlen(trim($classe))) {

					$nbelevetotal++;

					// création du tableau de hash contenant les paramètres de la fonction create_eleve
					$params[ne]=            strtolower(trim(addslashes($nomE)));
					$params[pe]=            strtolower(trim(addslashes($prenomE)));
					$params[ce]=            $classe;
					$params[lv1]=           strtolower(trim(addslashes($lv1)));
					$params[lv2]=           strtolower(trim(addslashes($lv2)));
					$params[option]=        $option;
					// faire un module pour le regime valeur possible 0,1,2,3
					$params[regime]=        $regime;
					$params[naiss]=         $date_naissance;
					$params[lieunais]=	strtolower(trim(addslashes($lieunaissance)));
					$params[nat]=           strtolower(trim(addslashes($nationalite)));
					$params[mdp]=           $passwd;
					$params[mdpeleve]=	$passwd_eleve;
					$params[nt]=            strtolower(trim(addslashes($nom_tuteur)));
					$params[pt]=		strtolower(trim(addslashes($prenom_tuteur)));
					$params[adr1]=        	strtolower(trim(addslashes($adr1)));
					$params[cpadr1]=      	strtolower(trim(addslashes($code_postal)));
					$params[commadr1]=     	strtolower(trim(addslashes($commune)));
					$params[tel_port_1]=   	strtolower(trim(addslashes($telportable1)));
					$params[adr2]=          strtolower(trim(addslashes($adr2)));
					$params[cpadr2]=       	strtolower(trim(addslashes($code_postal2)));
					$params[commadr2]=     	strtolower(trim(addslashes($commune2)));
					$params[tel_port_2]=   	strtolower(trim(addslashes($telportable2)));
					$params[tel]=          	strtolower(trim(addslashes($tel)));
					$params[profp]=        	strtolower(trim(addslashes($prof_pere)));
					$params[telprofp]=     	strtolower(trim(addslashes($tel_prof_pere)));
					$params[profm]=        	strtolower(trim(addslashes($prof_mere)));
					$params[telprofm]=     	strtolower(trim(addslashes($tel_prof_mere)));
					$params[nomet]=        	$nom_etablissement;
					$params[numet]=        	strtolower(trim(addslashes($num_etablissement)));
					$params[cpet]=         	"";
					$params[commet]=    	"";
					$params[numero_eleve]=  trim(addslashes($numeleve));
					$params[email]=  	trim(addslashes($email));
					$params[classe_ant]=  	strtolower(trim(addslashes($classe_ant)));
					$params[annee_ant]=  	$annee_ant;


					$params[civ_1]=  	civ2($civ1);
					$params[civ_2]=  	civ2($civ2);
					$params[nom_resp2]=	strtolower(trim($nom2));
					$params[prenom_resp2]=	strtolower(trim($prenom2));
					$params[mail_eleve]=  	trim(addslashes($email_eleve));
					$params[tel_eleve]=	trim($tel_eleve);

					$params[adr_eleve]=  	trim(addslashes($adr_eleve));
					$params[commune_eleve]= trim(addslashes($commune_eleve));
					$params[ccp_eleve]=  	trim(addslashes($ccp_eleve));
					$params[tel_fixe_eleve]=trim(addslashes($tel_fixe_eleve));
					$params[mailpro_eleve]=trim(addslashes($email_universitaire));
					$params[annee_scolaire] = $_POST["annee_scolaire"];

					// nouvelle version de create_eleve()
					$ascii=1;
					$cr=@create_eleve($params,$ascii);
					if ($cr == 1) {
						$nbeleveaffecte++;
						$f_pass=fopen("./data/fic_pass.txt","a+");
						fwrite($f_pass,strtolower(trim($nomE)).";".strtolower(trim($prenomE)).";".$passwd_enr.";".$passwd_eleve_enr."<br />");
						fclose($f_pass);
					}
					if ($cr == -3) {
						$nbelevedejaffecte++;
					}

			}else{
					$nbelevetotal++;
					// création du tableau de hash contenant les paramètres de la fonction create_eleve
					$params[ne]=            strtolower(trim(addslashes($nomE)));
					$params[pe]=            strtolower(trim(addslashes($prenomE)));
					$params[lv1]=           strtolower(trim($lv1));
					$params[lv2]=           strtolower(trim($lv2));
					$params[option]=        	$option;
					// faire un module pour le regime valeur possible 0,1,2,3
					$params[regime]=        $regime;
					$params[naiss]=         $date_naissance;
					$params[lieunais]=	strtolower(trim(addslashes($lieunaissance)));
					$params[nat]=           strtolower(trim(addslashes($nationalite)));
					$params[mdp]=           $passwd;
					$params[mdpeleve]=	$passwd_eleve;
					$params[nt]=            strtolower(trim(addslashes($nom_tuteur)));
					$params[pt]=		strtolower(trim(addslashes($prenom_tuteur)));
					$params[adr1]=        	strtolower(trim(addslashes($adr1)));
					$params[cpadr1]=      	strtolower(trim(addslashes($code_postal)));
					$params[commadr1]=     	strtolower(trim(addslashes($commune)));
					$params[adr2]=          strtolower(trim(addslashes($adr2)));
					$params[tel_port_2]=   	strtolower(trim(addslashes($telportable2)));
					$params[tel_port_1]=   	strtolower(trim(addslashes($telportable1)));
					$params[cpadr2]=       	strtolower(trim(addslashes($code_postal2)));
					$params[commadr2]=     	strtolower(trim(addslashes($commune2)));
					$params[tel]=          	strtolower(trim(addslashes($tel)));
					$params[profp]=        	strtolower(trim(addslashes($prof_pere)));
					$params[telprofp]=     	strtolower(trim(addslashes($tel_prof_pere)));
					$params[profm]=        	strtolower(trim(addslashes($prof_mere)));
					$params[telprofm]=     	strtolower(trim(addslashes($tel_prof_mere)));
					$params[nomet]=        	$nom_etablissement;
					$params[numet]=        	strtolower(trim(addslashes($num_etablissement)));
					$params[cpet]=         	"";
					$params[commet]=    	"";
					$params[numero_eleve]=  trim(addslashes($numeleve));
					$params[email]=  	trim(addslashes($email));
					$params[classe_ant]=  	strtolower(trim(addslashes($classe_ant)));
					$params[annee_ant]=  	$annee_ant;

					$params[civ_1]=  	civ2($civ1);
					$params[civ_2]=  	civ2($civ2);
					$params[nom_resp2]=	strtolower(trim($nom2));
					$params[prenom_resp2]=	strtolower(trim($prenom2));
					$params[mail_eleve]=  	trim(addslashes($email_eleve));
					$params[tel_eleve]=	trim($tel_eleve);

					$params[adr_eleve]=  	trim(addslashes($adr_eleve));
					$params[commune_eleve]= trim(addslashes($commune_eleve));
					$params[ccp_eleve]=  	trim(addslashes($ccp_eleve));
					$params[tel_fixe_eleve]=trim(addslashes($tel_fixe_eleve));
					$params[mailpro_eleve]=trim(addslashes($email_universitaire));
					$params[annee_scolaire] = $_POST["annee_scolaire"];

					// nouvelle create eleve sans classe
					$ascii=1;
					$cr=@create_eleve_sans_classe($params,$ascii);
					if ($cr == 1) {
						$f_pass=fopen("./data/fic_pass.txt","a+");
						fwrite($f_pass,strtolower(trim($nomE)).";".strtolower(trim($prenomE)).";".$passwd_enr.";".$passwd_eleve_enr."<br />");
						fclose($f_pass);
						$nbeleverreur++;
					}
					if ($cr == -3) {
						$nbelevedejaffecte++;
					}
			}
	}
	fclose($file);


	// creation ou mise a jour du fichier log  avec prise en
	$today=dateDMY();
	$fichier_s=fopen("./data/fic_opinion.txt","a+");
	$donnee=fwrite($fichier_s,"<BR>Message du : <FONT color=red>$today</font> De :<FONT color=red> $_SESSION[nom] $_SESSION[prenom]</FONT> <BR>Membre : <font color=red> $_SESSION[membre] </FONT><BR> <B>Message :</B> <font color=red> NOUVELLE BASE </font> - avec fichier ASCII <BR>  Etablissement : <font color=red>".REPECOLE."</font> ");
	fclose($fichier_s);

	// suppression du fichier ASCII
	@unlink($fic_ascii);
}

Pgclose();
?>

<br />
<ul>
<font class=T2>
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
<script language=JavaScript>buttonMagic("<?php print LANGBT41?>","acces2.php","_parent","","");</script>
<br><br />
</font></ul>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
</BODY></HTML>
