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
	set_time_limit(300);
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
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();"  onunload="attente_close()" >
<?php include_once("./librairie_php/lib_licence.php"); ?>
<?php include_once("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
<?php  $today= dateDMY();  ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript"<?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE22?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<!-- // fin  -->
<?php
include_once("librairie_php/db_triade.php");

if (empty($_SESSION["adminplus"])) {
	print "<script>";
	print "location.href='./base_de_donne_importation.php'";
	print "</script>";
}

$type=$_FILES['fichier']['type'];
$tmp_name=$_FILES['fichier']['tmp_name'];
$size=$_FILES['fichier']['size'];


$nbseparateurattendu="42";
$nbseparateurattendu2=$nbseparateurattendu + 1;
$nbseparateurattendu3=$nbseparateurattendu + 1;

$taille=2000000;
$taille2="2Mo";
include_once("librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(0); // en secondes
	$taille=8000000;
	$taille2="8Mo";
}

$ok=0;

include_once("./librairie_php/lib_import_csv.php");

if ( (!empty($tmp_name)) && (($type == "text/plain" ) || ($type == "application/vnd.ms-excel") || ($type == "application/octet-stream")) && ($size <= $taille)) {
	if ($type == "application/octet-stream") {
		print "<br /><center><font color=red >Attention votre fichier est ouvert par une autre application</font><BR><BR>";
		print "<input type=button Value='".LANGBT24."' onclick='javascript:history.go(-1)' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'><br />";
		print "<br /></center>";


	}

	else if ($type == "application/vnd.ms-excel" ) {
			$typefichier="excel";
			$fichier="import.csv";
			move_uploaded_file($tmp_name,"./data/fichier_ASCII/$fichier");
			$row = 1;
			$fp = fopen ("data/fichier_ASCII/$fichier","r");

			$str=file_get_contents("data/fichier_ASCII/$fichier");
			$rows=CSV2Array($str);


			for($i=0;$i<count($rows);$i++)  {
   				 //  for ($j=0;$j<count($rows[$i]);$j++) {
         			   // print $rows[$i][$j]."<br>";
			//	  } 
       			
	       		//	print $ii." ".$rows[$i][0]."<br>";
			//	if (( $j == $nbseparateurattendu3 ) && (trim($rows[$i][0]) != "")){
					$classe=$rows[$i][2];
					$tab11[$classe]=null;
			//	}else{
			//		$ii=$i+1;
			//		print " La ligne $ii  comporte $j colonnes sur $nbseparateurattendu3 <br>";
					/* for ($j=0;$j<count($rows[$i]);$j++) {
					     // print $rows[$i][$j]."<br>";
       					} */
			//		$ok=1;
			//}

   			}

	}else{
		$typefichier="txt";
		$fichier="import.txt";
		//print "Nom du fichier :".$fichier." ".$type." ".$size." ".$tmp_name." ";
		move_uploaded_file($tmp_name,"data/fichier_ASCII/$fichier");
		print "<br /><center><font class=T1>".LANGIMP40."</font></center><br /><br />";


		//analyse du fichier ASCII
		$file=fopen ("data/fichier_ASCII/$fichier", "r");
		$lines = file ("data/fichier_ASCII/$fichier");
		// Affiche toutes les lignes du tableau comme code HTML, avec les numéros de ligne
		$nbelevevalide=0;
		$lg=0;
		foreach ($lines as $line_num => $line) {
			$nbseparateur=substr_count("$line", ";");
			$line=preg_replace('/"/',"",$line);
	  		$lg++;
	  		if ( $nbseparateur == $nbseparateurattendu ) {
	    			$nbelevevalide++;
				/* 
1) nom * 2) prénom * 3) classe * 
4) régime 5) date naissance * 6) Lieu de naissance * 
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
43) Tél. fixe élève  44) Email Universitaire 45) Sexe élève

				* 
				*/
				list($nomE,$prenomE,$classe,$regime,$date_naissance,$lieu_naissance,$nationalite,$civ1,$nom_tuteur,$prenom_tuteur,$adr1,$code_postal,$commune,$tel_portable1,$civ2,$nom2,$prenom2,$adr2,$code_postal2,$commune2,$tel_portable2,$tel,$tel_eleve,$prof_pere,$tel_prof_pere,$prof_mere,$tel_prof_mere,$nom_etablissement,$num_etablissement,$lv1,$lv2,$option,$numeleve,$passwd,$email,$email_eleve,$classe_ant,$annee_ant,$passwd_eleve,$adr_eleve,$commune_eleve,$ccp_eleve,$tel_fixe_eleve,$email_universitaire,$sexe)= preg_split('/;/', $line, $nbseparateurattendu2);
				$tab11[$classe]=null;
			}else {
				$ok=1;
				print "<br><ul>Problème sur la ligne : $lg</ul>" ;
				print "<br><ul>il y a $nbseparateur élèments au lieu de $nbseparateurattendu </ul><br><br>" ;
				print "<br><center><font color=red class='T2'>".LANGATT."</font><font class='T2'> ".LANGIMP41."</font>";
				print "<br><br><br><input class=BUTTON type=button value='".LANGBT24."' onclick='history.go(-1);'></center><br>";
				break;
			}
		}
		fclose($file);
	 }

	 if ($ok != 1) {

		 if ($_POST["vide_eleve"] == "oui") { 
			 $cnx=cnx();
			 purge_element_eleve(); 
			 if (is_dir("./data/archive")) {
				 nettoyage_repertoire("./data/archive");
			 }
			 Pgclose();
		 }


	 	@ksort($tab11);
	 	$ligne=0;
		print "<form method=post action='base_de_donne_importation6.php'>";
		print "<input type=hidden name='fichier' value=\"data/fichier_ASCII/$fichier\">";
		print "<input type=hidden name='annee_scolaire' value='".$_POST["annee_scolaire"]."' />";
		print "<input type=hidden name='typefichier' value=\"$typefichier\">";
		print "<ul>".LANGIMP42."</ul><br />";
		print "<table border=1 align=center bgcolor='#FFFFFF'><tr bordercolor='#000000'>";
		$cnx=cnx();
		foreach ($tab11 as $clef => $b ) {
			if (strlen(trim($clef))) {
		    	?>
				<td><input type=text name='saisie_ref[]' value='<?php print $clef?>' size=20 onfocus='this.blur()'></td>
				<td><select name="saisie_classe[]">
			<?php
				print "<option value='-1' STYLE='color:#000066;background-color:#FCE4BA'>".LANGCHOIX."</option>";
				$id_classe=recherche_gep_classe($clef);
		    	if ($id_classe != "") {
			    	  $nom_classe=chercheClasse_nom($id_classe);
			    	  if ($nom_classe != "") {
	        		  		print "<option selected value='$id_classe'>$nom_classe</option>";
	        		  }
		    	}
			    	select_classe_gep(); // creation des options
			    ?>
				</select>
				</td>
                	<?php
	                if ($ligne == 1) {
        	           	print "</tr><tr bordercolor='#000000'>";
               		     	$ligne=0;
	                }else {
        	            $ligne++;
                	}
        	}
        }
        	Pgclose();
		print "</table>";
    		print "<br /><br /><center>";
    		print "<script language=JavaScript>buttonMagicReactualise()</script>";
		print "<script language=JavaScript>buttonMagic('".LANGBT26."',\"creat_classe_gep.php\",\"ajclass\",\"width=450,height=150\",\"\")</script>";
    		print "<input class=BUTTON type=submit value='".LANGBTS."' onclick='attente()' ></center>";
    		print "</form>";
		print "<br>";
	}
}else{
	$ok=1;
}

if ($ok == 1) {
	@unlink("data/fichier_ASCII/$fichier");
?>
	<br /><center><font id="color2" class=T2><?php print LANGIMP43 ?><BR><BR>
	<?php print LANGIMP44 ?></font><br /><br />
	<input type=button Value="<?php print LANGBT24?>" onclick="javascript:history.go(-1)" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"><br />
	<br /></center>
<?php
}
?>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
</BODY></HTML>
