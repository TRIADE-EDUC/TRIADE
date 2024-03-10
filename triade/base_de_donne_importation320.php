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
	set_time_limit(900);
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

$optionligne=1;
if ($_POST['optionligne'] == 1) { $optionligne=0; }

if ($_POST["typefichier"] == "excel" ) {
	$fic_xls=$_POST["fichier"];
		include_once('./librairie_php/reader.php');
		$data = new Spreadsheet_Excel_Reader();
//		$data->setOutputEncoding('CP1251');
		$data->setOutputEncoding('UTF-8');
		$data->read($fic_xls);
/*
1 Sexe;
2 Pays Nat.;
3 Elève No Etab;
4 Num. Elève Etab;
5 Nom;
6 Prénom;
7 Prénom 2;
8 Prénom 3;
9 Date Naissance;
10 Doublement;
11 Id National;
12 Date Entrée;
13 Date Sortie;
14 Adhésion Transport;
15 Date Modification;
16 Autorisation Abs. Perm.;
17 Autorisation Abs. Temp.;
18 Présence Doss. Médic.;
19 Présence Doss. Scol.;
20 Ville Naiss. Etrangère;
21 Commune Naiss.;
22 Pays Naiss.;
23 Code Régime;
24 Lib. Régime;
25 Motif Sortie;
26 Code Circuit;
27 Lib. Circuit;
28 Code Bourse 1;
29 Lib. Bourse 1;
30 Code Bourse 2;
31 Lib. Bourse 2;
32 Code MEF;
33 Lib. MEF;
34 Code Structure;
35 Type Structure;
36 Lib. Structure;
37 Clé Gestion Mat. Enseignée 1;
38 Lib. Mat. Enseignée 1;
39 Code Modalité Elect. 1;
40 Lib. Modalité Elect. 1;
41 Clé Gestion Mat. Enseignée 2;
42 Lib. Mat. Enseignée 2;
43 Code Modalité Elect. 2;
44 Lib. Modalité Elect. 2;
45 Clé Gestion Mat. Enseignée 3;
46 Lib. Mat. Enseignée 3;
47 Code Modalité Elect. 3;
48 Lib. Modalité Elect. 3;
49 Clé Gestion Mat. Enseignée 4;
50 Lib. Mat. Enseignée 4;
51 Code Modalité Elect. 4;
52 Lib. Modalité Elect. 4;
53 Clé Gestion Mat. Enseignée 5;
54 Lib. Mat. Enseignée 5;
55 Code Modalité Elect. 5;
56 Lib. Modalité Elect. 5;
57 Clé Gestion Mat. Enseignée 6;
58 Lib. Mat. Enseignée 6;
59 Code Modalité Elect. 6;
60 Lib. Modalité Elect. 6;
61 Clé Gestion Mat. Enseignée 7;
62 Lib. Mat. Enseignée 7;
63 Code Modalité Elect. 7;
64 Lib. Modalité Elect. 7;
65 Clé Gestion Mat. Enseignée 8;
66 Lib. Mat. Enseignée 8;
67 Code Modalité Elect. 8;
68 Lib. Modalité Elect. 8;
69 Clé Gestion Mat. Enseignée 9;
70 Lib. Mat. Enseignée 9;
71 Code Modalité Elect. 9;
72 Lib. Modalité Elect. 9;
73 Clé Gestion Mat. Enseignée 10;
74 Lib. Mat. Enseignée 10;
75 Code Modalité Elect. 10;
76 Lib. Modalité Elect. 10;
77 Clé Gestion Mat. Enseignée 11;
78 Lib. Mat. Enseignée 11;
79 Code Modalité Elect. 11;
80 Lib. Modalité Elect. 11;
81 Clé Gestion Mat. Enseignée 12;
82 Lib. Mat. Enseignée 12;
83 Code Modalité Elect. 12;
84 Lib. Modalité Elect. 12;
85 Tél. Personnel;
86 Tél. Professionnel;
87 Tél. Portable;
88  Email;
89 Ligne Adresse 1;
90 Ligne Adresse 2;
91 Ligne Adresse 3;
92 Ligne Adresse 4;
93 Lib. Postal;
94 Code Postal;
95 Département;
96 Commune Etrangère;
97 Pays;
98 Civilité Resp. Lég. 1;
99 Nom Resp. Lég. 1;
100 Prénom Resp. Lég. 1;
101 Tél. Personnel Resp. Lég. 1;
102 Lien Parenté Resp. Lég. 1;
103 Tél. Portable Resp. Lég. 1;
104 Tél. Professionnel Resp. Lég. 1;
105 Email Resp. Lég. 1;
106 Communication Adresse Resp. Lég. 1;
107 Ligne Adresse 1 Resp. Lég. 1;
108 Ligne Adresse 2 Resp. Lég. 1;
109 Ligne Adresse 3 Resp. Lég. 1;
110 Ligne Adresse 4 Resp. Lég. 1;
111 Lib. Postal Resp. Lég. 1;
112 Code Postal Resp. Lég. 1;
113 Code Département Resp. Lég. 1;
114 Commune Etrangère Resp. Lég. 1;
115 Pays Resp. Lég. 1;
116 Civilité Resp. Lég. 2;
117 Nom Resp. Lég. 2;
118 Prénom Resp. Lég. 2;
119 Tél. Personnel Resp. Lég. 2;
120 Lien Parenté Resp. Lég. 2;
121 Tél. Portable Resp. Lég. 2;
122 Tél. Professionnel Resp. Lég. 2;
123 Email Resp. Lég. 2;
124 Communication Adresse Resp. Lég. 2;
125 Ligne Adresse 1 Resp. Lég. 2;
126 Ligne Adresse 2 Resp. Lég. 2;
127 Ligne Adresse 3 Resp. Lég. 2;
128 Ligne Adresse 4 Resp. Lég. 2;
129 Lib. Postal Resp. Lég. 2;
130 Code Postal Resp. Lég. 2;
131 Code Département Resp. Lég. 2;
132 Commune Etrangère Resp. Lég. 2;
133 Pays Resp. Lég. 2
*/
		
		for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
			if ($i == $optionligne ) { continue; }

			if (strtolower($data->sheets[0]['cells'][$i][35]) == "g") { continue ; }

			$classe=recherche_gep_classe(trim($data->sheets[0]['cells'][$i][34]));
			
			$passwd="";
			$passwd_eleve="";
			$date_naissance=$data->sheets[0]['cells'][$i][9];

			if ((trim($passwd) == "") || (! isset($passwd)))  {
					$passwd=passwd_random2(); // creation du mot de passe
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

			if (strtoupper($data->sheets[0]['cells'][$i][24]) == "EXTERN") {
				$regime="Externe";
			}

			if (strtoupper($data->sheets[0]['cells'][$i][24]) == "INT") {
				$regime="Interne";
			}

			if (strtoupper($data->sheets[0]['cells'][$i][24]) == "DP DAN") {
				$regime="Demi Pension";
			}

			$sexe="";
			if (strtoupper(trim($data->sheets[0]['cells'][$i][1])) == 'M') { $sexe="m"; }
			if (strtoupper(trim($data->sheets[0]['cells'][$i][1])) == 'F') { $sexe="f"; }

			if (strlen(trim($classe))) {
					$nbelevetotal++;
					// création du tableau de hash contenant les paramètres de la fonction create_eleve
					$params[ne]=            strtolower(trim(addslashes($data->sheets[0]['cells'][$i][5])));
					$params[pe]=            strtolower(trim(addslashes($data->sheets[0]['cells'][$i][6]." ".$data->sheets[0]['cells'][$i][7])));
					$params[ce]=            $classe;
					$params[lv1]=           strtolower(trim(addslashes($data->sheets[0]['cells'][$i][38])));
					$params[lv2]=           strtolower(trim(addslashes($data->sheets[0]['cells'][$i][42])));
					$params[option]=        strtolower(trim(addslashes($data->sheets[0]['cells'][$i][45])));
					$params[regime]=        $regime;
					$params[naiss]=         $date_naissance;   // attend jj/mm/aaaa
					$params[lieunais]=      strtolower(trim(addslashes($data->sheets[0]['cells'][$i][22])));
					$params[nat]=           strtolower(trim(addslashes($data->sheets[0]['cells'][$i][2])));
					$params[mdp]=           $passwd_enr;
					$params[mdpeleve]=	$passwd_eleve_enr;
					$params[nt]=            strtolower(trim(addslashes($data->sheets[0]['cells'][$i][99])));
					$params[pt]=		strtolower(trim(addslashes($data->sheets[0]['cells'][$i][100])));
					$params[nadr1]=        	"";
					$params[adr1]=        	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][107])));
					$params[cpadr1]=      	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][112])));
					$params[commadr1]=     	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][111])));
					$params[nadr2]=         "";
					$params[adr2]=          strtolower(trim(addslashes($data->sheets[0]['cells'][$i][125])));
					$params[cpadr2]=       	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][130])));
					$params[commadr2]=     	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][129])));
					$params[tel]=          	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][101])));
					$params[profp]=        	"";
					$params[telprofp]=     	"";
					$params[profm]=         "";
					$params[telprofm]=     	"";
					$params[nomet]=        	"";
					$params[numet]=        	"";
					$params[cpet]=         	"";
					$params[commet]=    	"";
					$params[numero_eleve]=  strtolower(trim(addslashes($data->sheets[0]['cells'][$i][11])));
					$params[email]=  	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][105])));
					$params[classe_ant]=  	"";
					$params[annee_ant]=  	"";
					$params[civ_1]=  	civ2($data->sheets[0]['cells'][$i][98]);
					$params[civ_2]=  	civ2($data->sheets[0]['cells'][$i][116]);
					$params[nom_resp2]=	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][117])));
					$params[prenom_resp2]=	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][118])));
					$params[tel_port_1]=	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][103])));
					$params[tel_port_2]=	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][121])));
					$params[sexe]=		$sexe;

					// nouvelle version de create_eleve()
					$ascii=1;
					$datedesorti=dateFormBase($data->sheets[0]['cells'][$i][13]);
					$datedesorti=preg_replace('/-/',"",$datedesorti);
					$datedujour=dateYMD();
					if (($datedesorti < $datedujour) && (trim($datedesorti) != "")) {
						delete_compte_eleve($params[ne],$params[pe],$params[naiss]);
					}else{
						if ($_POST['update'] == 1) {
							$cr=create_update_eleve_scolnet($params[ne],$params[pe],$params[naiss],$params,$ascii,$_POST['updatevide'],$_POST['updatepasswd']);
						}else{
							$cr=@create_eleve($params,$ascii);
						}
						if ($cr == 1) {
							$f_pass=fopen("./data/fic_pass.txt","a+");
					     	    	fwrite($f_pass,strtolower(trim($data->sheets[0]['cells'][$i][5])).";".strtolower(trim($data->sheets[0]['cells'][$i][6]." ".$data->sheets[0]['cells'][$i][7])).";".$passwd_enr.";".$passwd_eleve_enr."<br />");
				     			fclose($f_pass);
							$nbeleveaffecte++;
						}
						if ($cr == -3) {
							$nbelevedejaffecte++;
						}
					}
		}else{
					$nbelevetotal++;
					// création du tableau de hash contenant les paramètres de la fonction create_eleve
					$params[ne]=            strtolower(trim(addslashes($data->sheets[0]['cells'][$i][5])));
					$params[pe]=            strtolower(trim(addslashes($data->sheets[0]['cells'][$i][6]." ".$data->sheets[0]['cells'][$i][7])));
					$params[ce]=            $classe;
					$params[lv1]=           strtolower(trim(addslashes($data->sheets[0]['cells'][$i][38])));
					$params[lv2]=           strtolower(trim(addslashes($data->sheets[0]['cells'][$i][42])));
					$params[option]=        strtolower(trim(addslashes($data->sheets[0]['cells'][$i][45])));
					$params[regime]=        $regime;
					$params[naiss]=         $date_naissance;   // attend jj/mm/aaaa
					$params[lieunais]=      strtolower(trim(addslashes($data->sheets[0]['cells'][$i][22])));
					$params[nat]=           strtolower(trim(addslashes($data->sheets[0]['cells'][$i][2])));
					$params[mdp]=           $passwd_enr;
					$params[mdpeleve]=	$passwd_eleve_enr;
					$params[nt]=            strtolower(trim(addslashes($data->sheets[0]['cells'][$i][99])));
					$params[pt]=		strtolower(trim(addslashes($data->sheets[0]['cells'][$i][100])));
					$params[nadr1]=        	"";
					$params[adr1]=        	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][107])));
					$params[cpadr1]=      	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][112])));
					$params[commadr1]=     	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][111])));
					$params[nadr2]=         "";
					$params[adr2]=          strtolower(trim(addslashes($data->sheets[0]['cells'][$i][125])));
					$params[cpadr2]=       	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][130])));
					$params[commadr2]=     	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][129])));
					$params[tel]=          	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][101])));
					$params[profp]=        	"";
					$params[telprofp]=     	"";
					$params[profm]=         "";
					$params[telprofm]=     	"";
					$params[nomet]=        	"";
					$params[numet]=        	"";
					$params[cpet]=         	"";
					$params[commet]=    	"";
					$params[numero_eleve]=  strtolower(trim(addslashes($data->sheets[0]['cells'][$i][4])));
					$params[email]=  	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][105])));
					$params[classe_ant]=  	"";
					$params[annee_ant]=  	"";
					$params[civ_1]=  	civ2($data->sheets[0]['cells'][$i][98]);
					$params[civ_2]=  	civ2($data->sheets[0]['cells'][$i][116]);
					$params[nom_resp2]=	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][117])));
					$params[prenom_resp2]=	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][118])));
					$params[tel_port_1]=	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][103])));
					$params[tel_port_2]=	strtolower(trim(addslashes($data->sheets[0]['cells'][$i][121])));
					$params[sexe]=		$sexe;

					// nouvelle create eleve sans classe
					$ascii=1;
					$datedesorti=dateFormBase($data->sheets[0]['cells'][$i][13]);
					$datedesorti=preg_replace('/-/',"",$datedesorti);
					$datedujour=dateYMD();
					if (($datedesorti < $datedujour) && (trim($datedesorti) != "")) {
						delete_compte_eleve($params[ne],$params[pe],$params[naiss]);
					}else{
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
		    
} 
			fclose($fp);
			// creation ou mise a jour du fichier log  avec prise en
			$today=dateDMY();
			$fichier_s=fopen("./".REPADMIN."/data/fic_opinion.txt","a+");
			$donnee=fwrite($fichier_s,"<BR>Message du : <FONT color=red>$today</font> De :<FONT color=red> $_SESSION[nom] $_SESSION[prenom]</FONT> <BR>Membre : <font color=red> $_SESSION[membre] </FONT><BR> <B>Message :</B> <font color=red> NOUVELLE BASE </font> - avec fichier EXCEL <BR>  Etablissement : <font color=red>".REPECOLE."</font> ");
			fclose($fichier_s);

			// suppression du fichier EXCEL
			@unlink($fic_xls);


}




Pgclose();
?>

<br />
<ul>
<font class='T2'>
- <?php print LANGBASE6bis?> : <?php print $nbelevetotal?><br>
- <?php print LANGBASE7?> : <?php print $nbeleveaffecte?><br>
- <?php print LANGBASE7bis ?> : <?php print $nbelevedejaffecte?><br>
- <?php print LANGBASE8?> : <?php print $nbeleverreur?><br><br>
- <?php print LANGBASE9?> <br /> (<?php print LANGBASE8bis ?>)<br /><br />
</font>
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
</ul>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
</BODY></HTML>
