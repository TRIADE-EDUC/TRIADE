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
include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
validerequete("3");
$cnx=cnx();
if (isset($_POST["createstage"])) {
		$i=$_POST["i"];
		$ideleve=$_POST["ideleve"];
		$tabidstage=$_POST["idstage"];
		$identreprise=utf8_decode($_POST["ident"]);
		$service="";
		$indemnitestage=utf8_decode($_POST["indemnitestage"]);
		$loge='0';
		$nourri='0';

		$idstage=trim($_POST["idstage"]);

		$indemnitestage=utf8_decode($_POST['indemnitestage']);
		$euro=utf8_decode("&#8364;");
		$indemnitestage=preg_replace('/EURO/',"$euro",$indemnitestage);
		

		if (($idstage != "") && ($identreprise != 0) ) {
			$cr=create_eleve_stage($ideleve,$identreprise,$lieu,$ville,$_POST["idprof"],$_POST["date"],$_POST["loge"],$_POST["nourri"],utf8_decode($_POST["xservice"]),utf8_decode($_POST["raison"]),utf8_decode($_POST["info"]),$idstage,$postal,utf8_decode($_POST["responsable"]),$_POST["tel"],$_POST["alternance"],$_POST["dateDebutAlternance"],$_POST["dateFinAlternance"],$_POST["jourstage"],$_POST["idtuteur1"],$_POST["horairedebutjournalier"],$_POST["horairefinjournalier"],$_POST["date2"],$_POST["idprof2"],'',$indemnitestage,$pays,$fax,$responsable2,$langue,$trim,$_POST["idtuteur2"]);
			if($cr == 1){
				bonux_entreprise($identreprise);
				$dateperiode=recherchedatestage($idstage); //idclasse,datedebut,datefin,numstage,id,nom_stage
				$periode=dateForm($dateperiode[0][1])." au ".dateForm($dateperiode[0][2]);
				history_entreprise($identreprise,$ideleve,$periode,$langue,utf8_decode($_POST["service"]));
				history_cmd($_SESSION["nom"],"CREATION","Eleve Stage");
				print "<img src='image/commun/stat1.gif' title='Enregistrement r&eacute;ussi' >";
			}else{
				print "<img src='image/commun/stat2.gif' title='Erreur Enregistrement (A0)'>";
			}
		}else{
			print "<img src='image/commun/stat2.gif' title='Erreur Enregistrement (A1)'> ";
		}
}else{
	print "<img src='image/commun/stat2.gif' title='Erreur Enregistrement (A2)' >";
}
Pgclose();
?>
