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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGCHER1?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
     <!-- // debut form  -->
     <blockquote><BR>
<?php
$ret="\n";
if (PHP_OS == "WINNT") {  $ret="\r\n"; }

$listeaffiche=$_POST["saisie_recherche"];
$listeaffiche=preg_replace('/,/',", ",$listeaffiche);
$listeaffiche=preg_replace('/ nomT1/',LANGEDIT8,$listeaffiche);
$listeaffiche=preg_replace('/ prenomT1/',LANGEDIT5bis,$listeaffiche);
$listeaffiche=preg_replace('/ nomT2/',LANGEDIT4,$listeaffiche);
$listeaffiche=preg_replace('/ prenomT2/',LANGEDIT5,$listeaffiche);
$listeaffiche=preg_replace('/ telephone/',LANGIMP20,$listeaffiche);
$listeaffiche=preg_replace('/ profpere/',LANGIMP21,$listeaffiche);
$listeaffiche=preg_replace('/ telprofpere/',LANGIMP22,$listeaffiche);
$listeaffiche=preg_replace('/ profmere/',LANGIMP23,$listeaffiche);
$listeaffiche=preg_replace('/ telprofmere/',LANGIMP24,$listeaffiche);
$listeaffiche=preg_replace('/ telport1/',LANGEDIT2,$listeaffiche);
$listeaffiche=preg_replace('/ lieudenaissance/',LANGEDIT6,$listeaffiche);
$listeaffiche=preg_replace('/ telport2/',LANGEDIT9,$listeaffiche);
$listeaffiche=preg_replace('/,/',", ",$listeaffiche);
?>
   <font class="T2"><?php print LANGCHER10?></font> : <b><?php print $listeaffiche ?> </b><br /><br />
 <br>

  <?php
  foreach ($_POST["saisie_critere"] as $value) {
        switch ($value) {
               case "choix":  print "<script language=JavaScript>alert('".LANGCHER17."');history.go(-1); </script>"; break;
        }
  }
  ?>

<?php
//-------------------------
// liste critere avec valeur
//-------------------------
function liste_critere($x , $y, $nb){
	if (!is_array($x) || !is_array($y)){
		return FALSE;
	}
	array_pad($x, count($y), "");
	array_pad($y, count($x), "");
	$retour = "<ul>";
	while(count($x) > 0){
		$nb--;
     		$retour .=  array_shift($x)."</b> ".LANGCHER18." <b>".strtolower(array_shift($y))."<br>" ;
		if ($nb !=  0) { $retour .= " <br />  "; }
	}
	$retour .= "</ul>";
	return $retour;
}


//--------------------
// Verif critere
//--------------------
function verif_critere($critere) {
	$select=0;
	$prefixe=PREFIXE;
	foreach ($critere as $value) {
		switch ($value) {
			case "classe":   $select="${prefixe}classes c"; break;
		}
	}
	return $select;
}





// ------------------------------------
// creation de la requete avec 1 table
// -----------------------------------
function requete1($x , $y, $nb,$op){
	if (!is_array($x) || !is_array($y)){
		return FALSE;
	}
	array_pad($x, count($y), "");
	array_pad($y, count($x), "");
	$retour = "";
	$positionElement=0;
	while(count($x) > 0){
		$nb--;
		$operateur=$op;
		$operateur_naissance="";
		$valeurRechercher=strtolower(array_shift($y));
		switch (array_shift($x)) {

/*
 value='nomT1'       
 value='prenomT1'   
 value='nomT2'       
 value='prenomT2'   
 value='telephone'  
 value='profpere'   
 value='telprofpere'
 value='profmere'   
 value='telprofmere' 
 value='telport1'   
 value='telport2'  
 value='lieudenaissance'
 */
 			case "nomT1":    $select="nomtuteur"; 			break;
 			case "prenomT1": $select="prenomtuteur"; 		break;
 			case "nomT2":    $select="nom_resp_2"; 			break;
 			case "prenomT2": $select="prenom_resp_2"; 		break;
 			case "telephone":$select="telephone"; 			break;
 			case "profpere": $select="profession_pere"; 		break;
 			case "telprofpere": $select="tel_prof_pere"; 		break;
 			case "profmere":    $select="profession_mere"; 		break;
 			case "telprofmere": $select="tel_prof_mere"; 		break;
 			case "telport1":    $select="tel_port_1"; 		break;
 			case "telport2":    $select="tel_port_2"; 		break;
 			case "lieudenaissance": $select="lieu_naissance"; 	break;

			case "nom":      $select="nom";          break;
			case "prenom":   $select="prenom";       break;
			case "classe":   $select="classe";       break;
			case "adresse":  $select="adr1";         break;
			case "ville":    $select="commune_adr1"; break;
			case "LV1":	 $select="lv1";		 break;
			case "LV2":	 $select="lv2";		 break;
			case "option":	 $select="option";	 break;
			case "regime":	 $select="regime";	 break;
			case "naissance": $operateur="BETWEEN";
					  $naissancePlusUn=$valeurRechercher + 1 ;
					  $operateur_naissance=" AND ".$naissancePlusUn ;
					  $select="date_naissance";
					  break;
			case "nationalite":	 $select="nationalite";			 break;
			case "profession pere":	 $select="profession_pere";	 	 break;
			case "profession mere":	 $select="profession_mere";	 	 break;
			case "numero etablissement":	 $select="numero_etablissement"; break;

			case "email parent":	 $select="email"; break;
			case "email tuteur 2":	 $select="email_resp_2"; break;
			case "email eleve":	 $select="email_eleve"; break;
			case "tel eleve":	 $select="tel_eleve"; break;
			case "idnational":	 $select="numero_eleve"; break;
			case "codepostal":	 $select="code_post_adr1"; break;
			case "classe_anterieure":	 $select="class_ant"; break;
			case "sexe":	 $select="sexe"; break;
			case "code_compta":	 $select="code_compta"; break;
		}
		$positionElement++;
     		$retour .= "lower(".$select.") ".$operateur." '%".$valeurRechercher. "%'".$operateur_naissance ;
		if ($nb !=  0) { $retour .= " AND "; }
	}
	return $retour;
}
// ------------------------------------
// Creation de la requete avec 2 tables
// ------------------------------------
function requete2($x , $y, $nb,$op){
	if (!is_array($x) || !is_array($y)){
		return FALSE;
	}
	array_pad($x, count($y), "");
	array_pad($y, count($x), "");
	$retour = "";
	while(count($x) > 0){
		$operateur=$op;
		$nb--;
		$valeurRechercher=strtolower(array_shift($y));
		switch (array_shift($x)) {

			case "nomT1":    $select="e.nomtuteur"; 		break;
 			case "prenomT1": $select="e.prenomtuteur"; 		break;
 			case "nomT2":    $select="e.nom_resp_2"; 		break;
 			case "prenomT2": $select="e.prenom_resp_2"; 		break;
 			case "telephone":$select="e.telephone"; 		break;
 			case "profpere": $select="e.profession_pere"; 		break;
 			case "telprofpere": $select="e.tel_prof_pere"; 		break;
 			case "profmere":    $select="e.profession_mere"; 	break;
 			case "telprofmere": $select="e.tel_prof_mere"; 		break;
 			case "telport1":    $select="e.tel_port_1"; 		break;
 			case "telport2":    $select="e.tel_port_2"; 		break;
 			case "lieudenaissance": $select="e.lieu_naissance"; 	break;


			case "nom":      $select="e.nom";          break;
			case "prenom":   $select="e.prenom";       break;
			case "classe":   $select="c.libelle"; $sp="classe";       break;
			case "adresse":  $select="e.adr1";         break;
			case "ville":    $select="e.commune_adr1"; break;
			case "LV1":	 $select="e.lv1";		 break;
			case "LV2":	 $select="e.lv2";		 break;
			case "option":	 $select="e.option";		 break;
			case "regime":	 $select="e.regime";	 break;
			case "naissance": $operateur="BETWEEN";
					  $naissancePlusUn=$valeurRechercher + 1 ;
					  $operateur_naissance=" AND ".$naissancePlusUn ;
					  $select="e.date_naissance";
					  						 break;
			case "nationalite":	 $select="e.nationalite";		 break;
			case "profession pere":	 $select="e.profession_pere";	 	 break;
			case "profession mere":	 $select="e.profession_mere";	 	 break;
			case "numero etablissement":	 $select="e.numero_etablissement"; break;

			case "email parent":	 $select="e.email"; 		break;
			case "email tuteur 2":	 $select="e.email_resp_2"; 	break;
			case "email eleve":	 $select="e.email_eleve"; 	break;
			case "tel eleve":	 $select="e.tel_eleve"; 	break;
			case "idnational":	 $select="e.numero_eleve"; 	break;
			case "codepostal":	 $select="e.code_post_adr1"; 	break;
			case "classe_anterieure":	 $select="e.class_ant"; break;
			case "sexe":	 	 $select="sexe";  break;
			case "code_compta":	 $select="code_compta"; break;
		}
     		$retour .= "lower(".$select.") ".$operateur." '%".$valeurRechercher. "%'".$operateur_naissance ;
		if ($nb !=  0) { $retour .= " AND "; }
	}
	if ($sp == "classe") {
		$retour.= " AND c.code_class =  e.classe ";
	}

	return $retour;
}


// --------------------------------
// module de recherche dans la base
// --------------------------------

include_once("librairie_php/db_triade.php");
validerequete("3");
$prefixe=PREFIXE;
$cnx=cnx(); // connexion à la base
error($cnx);

$positionClasse=-1;
$positionNaissance=-1;
$compt_element=0;
$nbTable=0;
$nbTableok=1;
$liste = preg_split ("/,/", $_POST["saisie_recherche"]);
$nbElems = count($liste);
$nbElems3 = count($liste);
$verifCritere=verif_critere($_POST["saisie_critere"]); // verification du critere pour le type de lecture
if ($verifCritere == "0") { $nbTableok = 1; } else { $table2[]=$verifCritere; $nbTableok =2; }
foreach ($liste as $value) {
	switch ($value) {

case "nom":$libelle.="nom";$libelle2.="e.nom";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "prenom":$libelle.="prenom";$libelle2.="e.prenom";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "classe":$libelle.="classe";$libelle2.="c.libelle";$table="${prefixe}eleves";$positionClasse=$compt_element;$table2[]=""; break;
case "adresse":$libelle.="adr1";$libelle2.="e.adr1";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "ville":$libelle.="commune_adr1";$libelle2.="e.commune_adr1";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "LV1":$libelle.="lv1";$libelle2.="e.lv1";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "LV2":$libelle.="lv2";$libelle2.="e.lv2";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "option":$libelle.="option";$libelle2.="e.option";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "regime":$libelle.="regime";$libelle2.="e.regime";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "naissance":$libelle.="date_naissance";$libelle2.="e.date_naissance";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";$positionNaissance=$compt_element;break;
case "nationalite":$libelle.="nationalite";$libelle2.="e.nationalite";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "profession pere":$libelle.="profession_pere";$libelle2.="e.profession_pere";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "profession mere":$libelle.="profession_mere";$libelle2.="e.profession_mere";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "numero etablissement":$libelle.="numero_etablissement";$libelle2.="e.numero_etablissement";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "email parent":$libelle.="email";$libelle2.="e.email";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "email tuteur 2":$libelle.="email_resp_2";$libelle2.="e.email_resp_2";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "email eleve":$libelle.="email_eleve";$libelle2.="e.email_eleve";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "tel eleve":$libelle.="tel_eleve";$libelle2.="e.tel_eleve";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "idnational":$libelle.="numero_eleve";$libelle2.="e.numero_eleve";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "codepostal":$libelle.="code_post_adr1";$libelle2.="e.code_post_adr1";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "classe_anterieure":$libelle.="class_ant";$libelle2.="e.class_ant";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;

case "nomT1":$libelle.="nomtuteur";$libelle2.="e.nomtuteur";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "prenomT1":$libelle.="prenomtuteur";$libelle2.="e.prenomtuteur";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "nomT2":$libelle.="nom_resp_2";$libelle2.="e.nom_resp_2";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "prenomT2":$libelle.="prenom_resp_2";$libelle2.="e.prenom_resp_2";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "telephone":$libelle.="telephone";$libelle2.="e.telephone";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "profpere":$libelle.="profession_pere";$libelle2.="e.profession_pere";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "telprofpere":$libelle.="tel_prof_pere";$libelle2.="e.tel_prof_pere";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "profmere":$libelle.="profession_mere";$libelle2.="e.profession_mere";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "telprofmere":$libelle.="tel_prof_mere";$libelle2.="e.tel_prof_mere";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "telport1":$libelle.="tel_port_1";$libelle2.="e.tel_port_1";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "telport2":$libelle.="tel_port_2";$libelle2.="e.tel_port_2";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "lieudenaissance":$libelle.="lieu_naissance";$libelle2.="e.lieu_naissance";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "sexe":$libelle.="sexe";$libelle2.="e.sexe";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;
case "code_compta":$libelle.="code_compta";$libelle2.="e.code_compta";$table="${prefixe}eleves";$table2[]="${prefixe}eleves e";break;


	}
	$compt_element++;
	if ( $nbElems--  >  1 ) {  $libelle .= ","; }
	if ( $nbElems3--  >  1 ) {  $libelle2 .= ","; }
}

// ---------------------------
foreach ($table2 as $value) {
	sort ($table2);
	$table2 = array_unique ($table2);
}
$nbElems2 = count($table2);
//alertJs($nbElems2);
foreach ($table2 as $value) {
	if ($value != "") {
		$table22 .= $value;
		if ( $nbElems2  >=  2 ) {  $table22 .= ",";$nbElems2--; }
	}
	//alertJs($table22);
}
// recherche le "," à la fin de la chaine et on remplace par rien
if (preg_match('/,$/', $table22)) {
        $table22 = preg_match('/,$/i',"", $table22);
}

// ---------------------------

if ($nbTableok == 1 ) {
	$lignepoursql=requete1($_POST["saisie_critere"],$_POST["saisie_valeur"],$_POST["saisie_nombre"],$_POST["saisie_operateur"]);
	$sqlsuite = $lignepoursql;
	if (($libelle != "") && ($table != "") && ($sqlsuite != "")) {
		$sql = "SELECT ".$libelle ." FROM ". $table  ."  WHERE ".$sqlsuite. " ORDER BY ". $libelle;
		//print("<h1>$libelle</h1>");
		$res = execSql($sql);
		$data =  chargeMat($res);
		$totaltrouve=count($data);
		//alertJs($sql);
		//print $sql;
		// $data : tab bidim - soustab 3 champs
		for($i=0;$i<count($data);$i++)
		{
			$jj=$_POST["saisie_nb_recherche"];
			for($j=0;$j<$_POST["saisie_nb_recherche"];$j++) {
				if ($positionClasse == $j) {    // procedure pour la recuperation du libelle de la classe
					$data2=chercheClasse($data[$i][$j]);
					$resultat.=$data2[0][1];
					$resultat.=$_POST["saisie_separateur"];
					continue;
				}
				if ($positionNaissance == $j) {    // procedure pour la recuperation de la date de naissance
					$data2=dateForm($data[$i][$j]);
					$resultat.=$data2;
					$resultat.=$_POST["saisie_separateur"];
					continue;
				}
			
				$resultat.=trim($data[$i][$j]);
	
	
				if ($jj-- > 1) {
					$resultat.=$_POST["saisie_separateur"];
				}
			}
			$resultat.="$ret";
		}
	}
}
	
if ($nbTableok == 2) {
	$lignepoursql=requete2($_POST["saisie_critere"],$_POST["saisie_valeur"],$_POST["saisie_nombre"],$_POST["saisie_operateur"]);
	if (($libelle2 != "") && ($table22 != "") && ($lignepoursql != "")) {
		$sql = "SELECT " . $libelle2 . " FROM " . $table22 ." WHERE ". $lignepoursql ." ORDER BY " . $libelle2;
		$res = execSql($sql);
		$data =  chargeMat($res);
		//alertJs($sql);
		//print $sql;
		// $data : tab bidim - soustab 3 champs
		$totaltrouve=count($data);
		for($i=0;$i<count($data);$i++)
		{
			$jj=$_POST["saisie_nb_recherche"];
			for($j=0;$j<$_POST["saisie_nb_recherche"];$j++) {
				$resultat.=trim($data[$i][$j]);
				if ($jj-- > 1) {
					$resultat.=$_POST["saisie_separateur"];
				}
			}
			$resultat.="$ret";
		}
	}
}
// deconnexion en fin de fichier
Pgclose();


//--------------------
// creation du fichier
$rep="./";
$fichier="data/recherche/"."rapport_".$_SESSION["id_pers"].".".$_POST["saisie_fichier_format"];
$handle = fopen ($rep.$fichier, "w");
if ($handle = fopen($rep.$fichier, 'a')) {
	fwrite($handle, $resultat);
}
?>

<!--------------------->
<!--------------------->
<!--------------------->
<font class="T2"><b><?php print LANGCHERCH1 ?></b> : <b></font>
<?php
$reponse=liste_critere($_POST["saisie_critere"],$_POST["saisie_valeur"],$_POST["saisie_nombre"]);
print $reponse;
?>
</b><br /><br /><br />



<font class="T2"><?php print LANGCHERCH2 ?> : </font><input type=button onclick="open('visu_document.php?fichier=<?php print $fichier?>','_blank','');" value="Cliquez ici (format:.<?php print $_POST["saisie_fichier_format"]?>)"  class="bouton2">
<br> <br />
<br /><br />
<font class="T2"><i><?php print LANGCHERCH3 ?></i> : <b><?php print $totaltrouve?></b></font>
<br /><br />
</blockquote>
<!-- // fin form -->
</td></tr></table>
<br /><br />
<script type="text/JavaScript">InitBulle('#000000','#CCCCFF','red',1);</script>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY>
</HTML>
