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
error_reporting(0);
include_once("./common/config3.inc.php");
include_once("./common/config7.inc.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");

validerequete("3");

$verif="0";
$verif2=0;

if (defined("KEYENR")) {
	$verif3=KEYENR;
}else{
	$verif3=0;
}

if ($_SESSION["adminplus"] == "suppreme" ) {
	$verif2=1;
}else {

	if ((!defined("PASS1")) || (!defined("PASS2")) || (!defined("PASS3"))) {
		print "<html><script type='text/javascript'>";
		print "location.href='./base_de_donne_key.php?saisie_resultat=erreur&base=".$_POST["base"]."'</script></html>";
		exit;
	}

	if (((crypt(md5($_POST["saisie_code1"]),"T2") == PASS1) && (crypt(md5($_POST["saisie_code2"]),"T2") == PASS2) && (crypt(md5($_POST["saisie_code3"]),"T2") == PASS3)) || ($verif3 == "1")) {
		$verif=verifkey($_POST["saisie_code1"],$_POST["saisie_code2"],$_POST["saisie_code3"]);

		if ($verif3 == "1") {
			$verif=1;
		}	

		if ($verif != "1") {
			print "<html><script type='text/javascript'>";
			print "location.href='./base_de_donne_key.php?saisie_resultat=erreur&base=".$_POST["base"]."'</script></html>";
			exit;
		}

		 $nom=$_SESSION["nom"];
		 $prenom=$_SESSION["prenom"];
		 $membre=$_SESSION["membre"];
		 $id_pers=$_SESSION["id_pers"];
		 $navigateur=$_SESSION["navigateur"];
		 $adminplus="suppreme";
		 $verif2=1;
	}else {
       		header("location: ./base_de_donne_key.php?saisie_resultat=erreur&base=$_POST[base]");
	}
}



if ($verif2 == "1" ) {
	if ($_POST["base"] == "archive")  {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./archivage2.php'</script></html>";
		exit;
	}
	if ($_POST["base"] == "archive2")  {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./archivage2.php'</script></html>";
		exit;
	}
	if ($_POST["base"] == "archive4")  {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./archivage2.php'</script></html>";
		exit;
	}
	if ($_POST["base"] == "infolog")  {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>";
		print "open('./visu_accesslog.php','_blank','');";
		print "location.href='./history_cmd.php';";
		print "</script></html>";
		exit;
	}
 	if ($_POST["base"] == "ascii")	{
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./base_de_donne_importation4.php'</script></html>";
		exit;
	}
	if ($_POST["base"] == "gep")  {
		if ($_POST["dbf_name"] == "F_ELE.DBF") {
			$_SESSION["adminplus"]="suppreme";
			print "<html><script type='text/javascript'>location.href='./base_de_donne_gep1.php'</script></html>";
			exit;
	 	}
	 	if ($_POST["dbf_name"] == "F_WIND.DBF") {
			 $_SESSION["adminplus"]="suppreme";
			print "<html><script type='text/javascript'>location.href='./base_de_donne_gep4.php'</script></html>";
			exit;
	 	}
	 	if ($_POST["dbf_name"] == "F_TMT.DBF") {
			$_SESSION["adminplus"]="suppreme";
			print "<html><script type='text/javascript'>location.href='./base_de_donne_gep5.php'</script></html>";
			exit;
	 	}
		if ($_POST["dbf_name"] == "F_ERE.DBF") {
			$_SESSION["adminplus"]="suppreme";
			print "<html><script type='text/javascript'>location.href='./base_de_donne_gep6.php'</script></html>";
			exit;
	 	}
	}
	if ($_POST["base"] == "affectation")  {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./affectation_creation.php'</script></html>";
		exit;
	}
	if ($_POST["base"] == "affectationmodif")  {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./modifaffect.php?sClasseGrp=".$_POST["sClasseGrp"]."'</script></html>";
		exit;
	}
	if ($_POST["base"] == "suppression")  {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./suppression_affectation0.php'</script></html>";
		exit;
	}
	if ($_POST["base"] == "purge")  {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./base_purge.php'</script></html>";
		exit;
	}

	if ($_POST["base"] == "change")  {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./chgmentClas0.php'</script></html>";
		exit;
	}

	if ($_POST["base"] == "xls")  {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./base_de_donne_importation30.php'</script></html>";
		exit;
	}
	if ($_POST["base"] == "ipacxls")  {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./base_de_donne_importation83.php'</script></html>";
		exit;
	}
	if ($_POST["base"] == "sconetxls")  {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./base_de_donne_importation300.php'</script></html>";
		exit;
	}
	if ($_POST["base"] == "entreprisexls")  {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./base_de_donne_importation900.php'</script></html>";
		exit;
	}
	if ($_POST["base"] == "entreprispigierexls")  {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./base_de_donne_importation1000.php'</script></html>";
		exit;
	}

	if ($_POST["base"] == "sconetxls2")  {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./base_de_donne_importation500.php'</script></html>";
		exit;
	}

	if ($_POST["base"] == "ctixls")  {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./base_de_donne_importation411.php'</script></html>";
		exit;
	}

	if (($_POST["base"] == "profxls") || ($_POST["base"] == "scolairexls") || ($_POST["base"] == "administrationxls")  || ($_POST["base"] == "personnelxls") || ($_POST["base"] == "tuteurstagexls") ) {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href=' ./base_de_donne_importation41.php?base=".$_POST["base"]."'</script></html>";
		exit;
	}

	if ($_POST["base"] == "matierexls")  {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./base_de_donne_importation230.php'</script></html>";
		exit;
	}

	if ($_POST["base"] == "xml")  {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./base_de_donne_importation60.php'</script></html>";
		exit;
	}
	if ($_POST["base"] == "stswebxls0")  {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./base_de_donne_importation610.php'</script></html>";
		exit;
	}
	if (($_POST["base"] == "prof") || ($_POST["base"] == "scolaire") || ($_POST["base"] == "administration") || ($_POST["base"] == "personnel") ) {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./base_de_donne_importation11.php?base=".$_POST["base"]."'</script></html>";
		exit;
	}
	if ($_POST["modulepost"] == "suppversement") {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./compta_supp_vers.php'</script></html>";
		exit;
	}
	if ($_POST["base"] == "newannee")  {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./newannee0.php?supp_date_cal=".$_POST["supp_date_cal"]."&supp_date_dst=".$_POST["supp_date_dst"]."&supp_date_edt=".$_POST["supp_date_edt"]."'</script></html>";
		exit;
	}
	if ($_POST["modulesecurite"] == "passmoduleindividuel") {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./entretien.php'</script></html>";
		exit;

	}
	if ($_POST["base"] == "medic") {
		$_SESSION["adminplusprofp"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./profpmedic.php?eid=".$_POST['eid']."'</script></html>";
		exit;

	}
	if ($_POST["modulesecurite"] == "passmodulemedical") {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./ficheeleve6.php?eid=".$_POST['eid']."'</script></html>";
		exit;

	}
	if ($_POST["base"] == "sconetabsxml") {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./base_de_donne_importation720.php'</script></html>";
		exit;

	}
	if ($_POST["base"] == "codebarrexls") {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./base_de_donne_importation810.php'</script></html>";
		exit;
	}	
	if ($_POST["base"] == "bilanfinancier") {
		$_SESSION["adminplus"]="suppreme";
		print "<html><script type='text/javascript'>location.href='./comptaetat.php'</script></html>";
		exit;
	}	

}
?>
