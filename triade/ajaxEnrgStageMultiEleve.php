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
		$service=utf8_decode($_POST["service_$i"]);
		$indemnitestage=utf8_decode($_POST["indemnitestage"]);
		$loge=$_POST["loge"];
		$nourri=$_POST["nourri"];



		if (preg_match('/CS/',$identreprise)) {
			$nomentreprise=utf8_decode($_POST["nom_entreprise_via_central"]);
			list($null,$idcs)=preg_split('/:/',$identreprise);
			$contact=utf8_decode($_POST["contact"]);
			$adressesiege=utf8_decode($_POST["adressesiege"]);
			$activite=utf8_decode($_POST["activite_$i"]);
			$activiteprin=utf8_decode($_POST["activiteprin"]);
			$email=$_POST["email"];
			$information=utf8_decode($_POST["information"]);
			$activite2=utf8_decode($_POST["activite2"]);
			$activite3=utf8_decode($_POST["activite3"]);
			$fonction=utf8_decode($_POST["fonction"]);
			$nbchambre=$_POST["nbchambre"];
			$siteweb=$_POST["siteweb"];
			$grphotelier=$_POST["grphotelier"];
			$nbetoile=$_POST["nbetoile_$i"];
			$registrecommerce=utf8_decode($_POST["registrecommerce"]);
			$siren=$_POST["siren"];
			$siret=$_POST["siret"];
			$formejuridique=$_POST["formejuridique"];
			$secteureconomique=$_POST["secteureconomique"];
			$INSEE=$_POST["INSEE"];
			$NAFAPE=$_POST["NAFAPE"];
			$NACE=$_POST["NACE"];
			$typeorganisation=utf8_decode($_POST["typeorganisation"]);
			$lieu=utf8_decode($_POST["lieu"]);
			$postal=utf8_decode($_POST["postal"]);
			$ville=utf8_decode($_POST["ville"]);
			$pays=utf8_decode($_POST["pays"]);
			$tel=$_POST["tel"];
			$fax=$_POST["fax"];
			
			$identreprise=create_entreprise_via_cs($nomentreprise,$contact,$lieu,$postal,$ville,$activite,$activiteprin,$tel,$fax,$email,$information,$activite2,$activite3,$fonction,$nbchambre,$siteweb,$grphotelier,$nbetoile,$pays,$registrecommerce,$siren,$siret,$formejuridique,$secteureconomique,$INSEE,$NAFAPE,$NACE,$typeorganisation,$idcs);
		}

		$idstage=trim($_POST["idstage"]);

		$indemnitestage=utf8_decode($_POST['indemnitestage']);
		$euro=utf8_decode("&#8364;");
		$indemnitestage=preg_replace('/EURO/',"$euro",$indemnitestage);
		

		if (($idstage != "") && ($identreprise != 0) ) {
			$cr=create_eleve_stage($ideleve,$identreprise,$lieu,$ville,$_POST["idprof"],$_POST["date"],$_POST["loge"],$_POST["nourri"],utf8_decode($_POST["xservice"]),utf8_decode($_POST["raison"]),utf8_decode($_POST["info"]),$idstage,$postal,utf8_decode($_POST["responsable"]),$_POST["tel"],$_POST["alternance"],$_POST["dateDebutAlternance"],$_POST["dateFinAlternance"],$_POST["jourstage"],$_POST["idtuteur"],$_POST["horairedebutjournalier"],$_POST["horairefinjournalier"],$_POST["date2"],$_POST["idprof2"],utf8_decode($_POST["service"]),$indemnitestage,$pays,$fax);
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
