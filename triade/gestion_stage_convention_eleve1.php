<?php
session_start();
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if ( ($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"droitStageProRead") == 0) ) {
	PgClose();
	header("Location: accespersonneldenied.php?titre=Module Stage Pro.");	
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
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) { set_time_limit(9000); }
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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGSTAGE115 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<!-- // fin  --><br> <br>
<?php
include_once('librairie_php/timezone.php');
include_once("librairie_php/recupnoteperiode.php");
$cnx=cnx();

// recupe du nom de la classe
if (isset($_POST["idclasse"])) {
	$data=chercheClasse($_POST["idclasse"]);
	$classeNom=$data[0][1];
	$idClasse=$_POST["idclasse"];
	$laclasse=1;
	$fic="classe".$idClasse;
	$erreur=LANGMESS49;
}

if (isset($_POST["eid"])) {
	$ideleve=$_POST["eid"];
	$nomEleve=trim(strtoupper(recherche_eleve_nom($ideleve)));
	$prenomEleve=trim(ucwords(strtolower(recherche_eleve_prenom($ideleve))));
	$idClasse=chercheIdClasseDunEleve($ideleve);
	$naissance_eleve=chercheDateNaissance($ideleve);
	if ($naissance_eleve != "") { $naissance_eleve=trim(dateForm($naissance_eleve)); }
	$data=chercheClasse($idClasse);
	$classeNom=$data[0][1];
	$eleve=$ideleve."-".$idClasse;
	$laclasse=0;
	$erreur=LANGMESS49;
	list($choixEntrepriseId,$num_stagePost)=preg_split('/#/',$_POST["choix_stage"]);
}

// recuperation des coordonnées
// de l etablissement
$data=visu_param(); // nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement,annee_scolaire
for($i=0;$i<count($data);$i++) {
       $nom_etablissement=trim($data[$i][0]);
       $adresse=trim($data[$i][1]);
       $postal=trim($data[$i][2]);
       $ville=trim($data[$i][3]);
       $tel=trim($data[$i][4]);
       $mail=trim($data[$i][5]);
       $directeur=trim($data[$i][6]);
       $anneescolaire=trim($data[$i][11]);
}
// fin de la recup

$idstage=$_POST["idstage"];

$nbconv=$_POST["nbconv"];

$data=config_param_visu("param_conv_stag#$idClasse$nbconv");
$texte=$data[0][0];
if ($texte ==  "{FICHIERRTFSTAGE}") {
	
	
	nettoyage_repertoire("./data/pdf_certif/courrierstageconvention/".$_SESSION["id_pers"]);

	$nb=$_POST["nb"];
	$textecomplet="";


	if ($laclasse == 1) {
		$eleveT=recupEleve($idClasse); // nom,prenom,lv1,lv2,elev_id,date_naissance,lieu_naissance,adr1,code_post_adr1,commune_adr1,telephone,numero_eleve,tel_eleve
		for($j=0;$j<count($eleveT);$j++) {  // premiere ligne de la creation PDF
			// variable eleve
			$nomEleve=trim(strtoupper($eleveT[$j][0]));
			$prenomEleve=trim(ucwords(strtolower($eleveT[$j][1])));
			$naissance_eleve=$eleveT[$j][5];
			$lieu_naissance_eleve=$eleveT[$j][6];
			$idEleve=$eleveT[$j][4];
			$adresseEleve=$eleveT[$j][7];
			$codePostalEleve=$eleveT[$j][8];
			$villeEleve=$eleveT[$j][9];
			$telEleve=$eleveT[$j][12];
			$telParent=$eleveT[$j][10];
			if ($naissance_eleve != "") { $naissance_eleve=trim(dateForm($naissance_eleve)); }
			$okconv=2;
		
			$idClasse=chercheIdClasseDunEleve($idEleve);
			$classe_nom=chercheClasse_nom($idClasse);

			$dataEnt=recherche_stage_eleve_entreprise11($idEleve,$idClasse);
			//  e.id_eleve ,e.id_entreprise,f.id_serial,f.nom,f.contact,
			//  f.adresse,f.code_p,f.ville,f.secteur_ac,f.activite_prin,
			//10  f.tel,f.fax,f.email,f.contact_fonction,f.pays_ent,
			//15  e.num_stage, e.compte_tuteur_stage, e.id_prof_visite, e.date_visite_prof2, e.id_prof_visite2,
			//20   e.date_visite_prof,f.nbchambre,f.siteweb,f.grphotelier,
			//24   f.nbetoile,e.loger,e.service,e.indemnitestage,e.pays_stage,
			//35 e.alternance,e.dateDebutAlternance,e.dateDebutAlternance
			//38 e.autre_responsable,g.datedebut,g.datefin
			
			$ent_nom="";
			$ent_adr="";
			$ent_cp="";
			$ent_directeur="";
			$ent_localite="";
			$ent_tel="";
			$ent_fax="";
			$ent_mail="";
			$ent_fonction="";
			$ent_pays="";
			$ent_tuteur="";
			$nom_stage="";
			$enseignant_1="";
			$enseignant_2="";
			$date_suivi_1="";
			$date_suivi_2="";
			$ent_web="";
			$ent_etoile="";
			$ent_chambre="";
			$ent_grp_hotelier="";
			$ent_logement="";
			$ent_service="";
			$ent_indemnitestage="";
			$responsable2="";



 			for($h=0;$h<count($dataEnt);$h++) {
				$responsable2=trim($dataEnt[$h][36]);

				$ent_nom=$dataEnt[$h][3];
				$ent_adr=$dataEnt[$h][5];
				$ent_cp=$dataEnt[$h][6];
				$ent_directeur=$dataEnt[$h][4];
				if ($responsable2 != "") $ent_directeur=$responsable2;

				$ent_localite=$dataEnt[$h][7];
				$ent_tel=$dataEnt[$h][10];
				$ent_fax=$dataEnt[$h][11];
				$ent_mail=$dataEnt[$h][12];
				$ent_fonction=$dataEnt[$h][13];
				$ent_pays=$dataEnt[$h][14];
				$ent_tuteur=recherche_personne($dataEnt[$h][16]);

				$ent_web=$dataEnt[$h][22];
				$ent_etoile=$dataEnt[$h][23];
				$ent_chambre=$dataEnt[$h][21];
				$ent_grp_hotelier=$dataEnt[$h][24];
				$ent_logement=($dataEnt[$h][26] == 1) ? "oui" : "non";
				$ent_service=$dataEnt[$h][26];
				$ent_indemnitestage=$dataEnt[$h][27];

				$nom_stage=chercheNomStage($dataEnt[$h][15]);
				$enseignant_1=recherche_personne($dataEnt[$h][17]);
				$enseignant_2=recherche_personne($dataEnt[$h][19]);
				$date_suivi_1=dateForm($dataEnt[$h][20]);
				$date_suivi_2=dateForm($dataEnt[$h][18]);

				

				include_once("librairie_php/timezone.php");
				$date=dateDMY();

				$TempFilename="./data/parametrage/courrier_stageconvention_$idClasse$nbconv.rtf";
				$fichier=fopen($TempFilename,"r");
				$longueur=9000000;
				$texte=fread($fichier,$longueur);
				fclose($fichier);	

				$texte=preg_replace("/NomEleve/","$nomEleve",$texte);
				$texte=preg_replace("/PrenomEleve/","$prenomEleve",$texte);
				$texte=preg_replace("/classe_eleve/","$classe_nom",$texte);
				$texte=preg_replace("/naissance_eleve/","$naissance_eleve",$texte);	
				$texte=preg_replace("/ent_nom/","$ent_nom",$texte);
				$texte=preg_replace("/ent_rs/","$ent_rs",$texte);
				$texte=preg_replace("/ent_adr/","$ent_adr",$texte);
				$texte=preg_replace("/ent_cp/","$ent_cp",$texte);
				$texte=preg_replace("/ent_directeur/","$ent_directeur",$texte);
				$texte=preg_replace("/ent_localite/","$ent_localite",$texte);
				$texte=preg_replace("/ent_tel/","$ent_tel",$texte);
				$texte=preg_replace("/lieu_naissance/","$lieu_naissance_eleve",$texte);
				$texte=preg_replace("/directeur/","$directeur",$texte);
				$texte=preg_replace("/nom_stage/","$nom_stage",$texte);
				$texte=preg_replace("/ent_dir_fonction/","$ent_fonction",$texte);
				$texte=preg_replace("/adresse_eleve/","$adresseEleve",$texte);
				$texte=preg_replace("/ccp_eleve/","$codePostalEleve",$texte);
				$texte=preg_replace("/ville_eleve/","$villeEleve",$texte);
				$texte=preg_replace("/tel_eleve/","$telEleve",$texte);
				$texte=preg_replace("/tel_parent/","$telParent",$texte);
				$dateJour=dateDMY();
				$texte=preg_replace("/date_du_jour/","$dateJour",$texte);
				$texte=preg_replace("/ent_tuteur/","$ent_tuteur",$texte);
				$texte=preg_replace("/enseignant_suivi_1/","$enseignant_1",$texte);
				$texte=preg_replace("/enseignant_suivi_2/","$enseignant_2",$texte);
				$texte=preg_replace("/date_suivi_2/","$date_suivi_2",$texte);
				$texte=preg_replace("/date_suivi_1/","$date_suivi_1",$texte);
				$texte=preg_replace("/ent_fax/","$ent_fax",$texte);	

				$texte=preg_replace("/ent_web/","$ent_web",$texte);	
				$texte=preg_replace("/ent_etoile/","$ent_etoile",$texte);	
				$texte=preg_replace("/ent_chambre/","$ent_chambre",$texte);	
				$texte=preg_replace("/ent_grp_hotelier/","$ent_grp_hotelier",$texte);	
				$texte=preg_replace("/ent_logement/","$ent_logement",$texte);
				$texte=preg_replace("/ent_service/","$ent_service",$texte);
				$texte=preg_replace("/ent_indemnitestage/","$ent_indemnitestage",$texte);
				$texte=preg_replace("/ent_pays/","$ent_pays",$texte);
				$texte=preg_replace("/anneescolaire/","$anneescolaire",$texte);
				$texte=preg_replace("/&#8364;/","euros",$texte);


			}

			$periode="";
			$date_debut="";
			$date_fin="";
			for($h=0;$h<count($dataEnt);$h++) {
				$num_stage=$dataEnt[$h][15];
				if ($dataEnt[$h][35] == 1) {
					$date_debut=dateForm($dataEnt[$h][36]);
					$date_fin=dateForm($dataEnt[$h][37]);
				}else{
					$date_debut=dateForm($dataEnt[$h][39]);
					$date_fin=dateForm($dataEnt[$h][40]);
				}
				$periode="Du $date_debut au $date_fin";
				$nbjourentredeuxdate=nbjours_entre_2_date($dataEnt[$h][18],$dataEnt[$h][19]);
				$texte=preg_replace("nb_jour","$nbjourentredeuxdate",$texte); 
				if ($num_stage == 1) $texte=preg_replace("/periode_1/","$periode",$texte); 
				if ($num_stage == 2) $texte=preg_replace("/periode_2/","$periode",$texte); 
				if ($num_stage == 3) $texte=preg_replace("/periode_3/","$periode",$texte); 
				if ($num_stage == 4) $texte=preg_replace("/periode_4/","$periode",$texte); 
				if ($num_stage == 5) $texte=preg_replace("/periode_5/","$periode",$texte); 
				if ($num_stage == 6) $texte=preg_replace("/periode_6/","$periode",$texte); 
				if ($num_stage == 7) $texte=preg_replace("/periode_7/","$periode",$texte); 
				if ($num_stage == 8) $texte=preg_replace("/periode_8/","$periode",$texte); 
				if ($num_stage == 9) $texte=preg_replace("/periode_9/","$periode",$texte); 
				if ($num_stage == 0) $texte=preg_replace("/periode_0/","$periode",$texte); 
				$texte=preg_replace("/periode_x/","$periode",$texte); 
 
				if ($num_stage == 1) $texte=preg_replace("/debperio_1/","$date_debut",$texte); 
				if ($num_stage == 2) $texte=preg_replace("/debperio_2/","$date_debut",$texte); 
				if ($num_stage == 3) $texte=preg_replace("/debperio_3/","$date_debut",$texte); 
				if ($num_stage == 4) $texte=preg_replace("/debperio_4/","$date_debut",$texte); 
				if ($num_stage == 5) $texte=preg_replace("/debperio_5/","$date_debut",$texte); 
				if ($num_stage == 6) $texte=preg_replace("/debperio_6/","$date_debut",$texte); 
				if ($num_stage == 7) $texte=preg_replace("/debperio_7/","$date_debut",$texte); 
				if ($num_stage == 8) $texte=preg_replace("/debperio_8/","$date_debut",$texte); 
				if ($num_stage == 9) $texte=preg_replace("/debperio_9/","$date_debut",$texte); 
				if ($num_stage == 0) $texte=preg_replace("/debperio_0/","$date_debut",$texte); 
				$texte=preg_replace("/debperio_x/","$date_debut",$texte); 

				if ($num_stage == 1) $texte=preg_replace("/finperio_1/","$date_fin",$texte); 
				if ($num_stage == 2) $texte=preg_replace("/finperio_2/","$date_fin",$texte); 
				if ($num_stage == 3) $texte=preg_replace("/finperio_3/","$date_fin",$texte); 
				if ($num_stage == 4) $texte=preg_replace("/finperio_4/","$date_fin",$texte); 
				if ($num_stage == 5) $texte=preg_replace("/finperio_5/","$date_fin",$texte); 
				if ($num_stage == 6) $texte=preg_replace("/finperio_6/","$date_fin",$texte); 
				if ($num_stage == 7) $texte=preg_replace("/finperio_7/","$date_fin",$texte); 
				if ($num_stage == 8) $texte=preg_replace("/finperio_8/","$date_fin",$texte); 
				if ($num_stage == 9) $texte=preg_replace("/finperio_9/","$date_fin",$texte); 
				if ($num_stage == 0) $texte=preg_replace("/finperio_0/","$date_fin",$texte); 
				$texte=preg_replace("/finperio_x/","$date_fin",$texte); 


			}
				

	

			//	readfile($fic);
			if (!is_dir("./data/pdf_certif/courrierstageconvention")) { mkdir("./data/pdf_certif/courrierstageconvention"); }
			mkdir("./data/pdf_certif/courrierstageconvention/".$classeNom);
			$nomEleve=preg_replace("/'/","",$nomEleve);
			$prenomEleve=preg_replace("/'/","",$prenomEleve);
			$nomfic=$nomEleve."_".$prenomEleve."_".$h.".rtf";
			$fic="./data/pdf_certif/courrierstageconvention/".$classeNom."/$nomfic";
			$fichier=fopen("$fic","a+");
			fwrite($fichier,$texte);
			fclose($fichier);
			unset($texte);
		}
		
		include_once('./librairie_php/pclzip.lib.php');
		$archive = new PclZip('./data/pdf_certif/courrierstageconvention/'.$classeNom.'.zip');
		$archive->create('./data/pdf_certif/courrierstageconvention/'.$classeNom,PCLZIP_OPT_REMOVE_ALL_PATH);
		$bouton="<input type=button onclick=\"open('telecharger.php?fichier=./data/pdf_certif/courrierstageconvention/$classeNom.zip','_blank','');\" value=\"".LANGTMESS530."\"  STYLE=\"font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;\">";
	}else{
		$idEleve=$ideleve;
		$sql="SELECT nom,prenom,lv1,lv2,elev_id,date_naissance,lieu_naissance,adr1,code_post_adr1,commune_adr1,telephone, numero_eleve, tel_fixe_eleve FROM ${prefixe}eleves WHERE elev_id='$idEleve'" ;
		$res=execSql($sql);
		$eleveT=chargeMat($res);
		// nom,prenom,lv1,lv2,elev_id,date_naissance,lieu_naissance,adr1,code_post_adr1,commune_adr1,telephone, numero_eleve,tel_eleve
		// variable eleve
		$nomEleve=trim(strtoupper($eleveT[0][0]));
		$prenomEleve=trim(ucwords(strtolower($eleveT[0][1])));
		$naissance_eleve=$eleveT[0][5];
		$lieu_naissance_eleve=$eleveT[0][6];

		$adresseEleve=$eleveT[0][7];
		$codePostalEleve=$eleveT[0][8];
		$villeEleve=$eleveT[0][9];
		$telEleve=$eleveT[0][12];
		$telParent=$eleveT[0][10];
		if ($naissance_eleve != "") { $naissance_eleve=trim(dateForm($naissance_eleve)); }
	
		$classe_nom=chercheClasse_nom($idClasse);

		//$dataEnt=recherche_stage_eleve_entreprise($idEleve);
		$dataEnt=recherche_stage_eleve_entreprise3($ideleve,$choixEntrepriseId,$idClasse,$num_stagePost);
		// e.id_eleve ,e.id_entreprise,f.id_serial,
		// f.nom,f.contact,f.adresse,
		// f.code_p,f.ville,f.secteur_ac,
		// f.activite_prin,f.tel,f.fax,
	// 12      f.email,f.contact_fonction,f.pays_ent,
		// e.num_stage, e.compte_tuteur_stage, e.id_prof_visite,
		// g.datedebut,g.datefin, g.numstage,
		//  e.date_visite_prof2, e.id_prof_visite2, e.date_visite_prof,
	//24	//  f.nbchambre,f.siteweb,f.grphotelier,
		//  f.nbetoile,e.loger,e.service,
		//  e.indemnitestage,e.pays_stage
		//  e.alternance,e.dateDebutAlternance,e.dateFinAlternance
	//35	//  e.autre_responsable
		for($h=0;$h<count($dataEnt);$h++) {
			$responsable2=trim($dataEnt[$h][35]);

			$ent_nom=$dataEnt[$h][3];
			$ent_adr=$dataEnt[$h][5];
			$ent_cp=$dataEnt[$h][6];
			$ent_directeur=$dataEnt[$h][4];
			if ($responsable2 != "") $ent_directeur=$responsable2;

			$ent_localite=$dataEnt[$h][7];
			$ent_tel=$dataEnt[$h][10];
			$ent_fax=$dataEnt[$h][11];
			$ent_mail=$dataEnt[$h][12];
			$ent_fonction=$dataEnt[$h][13];
			$ent_pays=$dataEnt[$h][14];
			$ent_tuteur=recherche_personne($dataEnt[$h][16]);
			$numstage=$dataEnt[$h][20];
			$okconv=2;

			$ent_web=$dataEnt[$h][25];
			$ent_etoile=$dataEnt[$h][27];
			$ent_chambre=$dataEnt[$h][24];
			$ent_grp_hotelier=$dataEnt[$h][26];
			$ent_logement=($dataEnt[$h][28] == 1) ? "oui" : "non";
			$ent_service=$dataEnt[$h][29];
			$ent_indemnitestage=$dataEnt[$h][30];

			$nom_stage=chercheNomStage($dataEnt[$h][15]);
			$enseignant_1=recherche_personne($dataEnt[$h][17]);
			$enseignant_2=recherche_personne($dataEnt[$h][22]);
			$date_suivi_1=dateForm($dataEnt[$h][23]);
			$date_suivi_2=dateForm($dataEnt[$h][21]);

			$classe_nom=chercheClasse_nom($idClasse);
			include_once("librairie_php/timezone.php");
			$date=dateDMY();	

			$TempFilename="./data/parametrage/courrier_stageconvention_$idClasse$nbconv.rtf";
			$fichier=fopen($TempFilename,"r");
			$longueur=9000000;
			$texte=fread($fichier,$longueur);
			fclose($fichier);	

			$texte=preg_replace("/NomEleve/","$nomEleve",$texte);
			$texte=preg_replace("/PrenomEleve/","$prenomEleve",$texte);
			$texte=preg_replace("/classe_eleve/","$classe_nom",$texte);
			$texte=preg_replace("/naissance_eleve/","$naissance_eleve",$texte);	
			$texte=preg_replace("/ent_nom/","$ent_nom",$texte);
			$texte=preg_replace("/ent_rs/","$ent_rs",$texte);
			$texte=preg_replace("/ent_adr/","$ent_adr",$texte);
			$texte=preg_replace("/ent_cp/","$ent_cp",$texte);
			$texte=preg_replace("/ent_directeur/","$ent_directeur",$texte);
			$texte=preg_replace("/ent_localite/","$ent_localite",$texte);
			$texte=preg_replace("/ent_tel/","$ent_tel",$texte);
			$texte=preg_replace("/ent_mail/","$ent_mail",$texte);
			$texte=preg_replace("/lieu_naissance/","$lieu_naissance_eleve",$texte);
			$texte=preg_replace("/directeur/","$directeur",$texte);
			$texte=preg_replace("/nom_stage/","$nom_stage",$texte);
			$texte=preg_replace("/ent_dir_fonction/","$ent_fonction",$texte);
			$texte=preg_replace("/adresse_eleve/","$adresseEleve",$texte);
			$texte=preg_replace("/ccp_eleve/","$codePostalEleve",$texte);
			$texte=preg_replace("/ville_eleve/","$villeEleve",$texte);
			$texte=preg_replace("/tel_eleve/","$telEleve",$texte);
			$texte=preg_replace("/tel_parent/","$telParent",$texte);
			$dateJour=dateDMY();
			$texte=preg_replace("/date_du_jour/","$dateJour",$texte);
			$texte=preg_replace("/ent_tuteur/","$ent_tuteur",$texte);
			$texte=preg_replace("/enseignant_suivi_1/","$enseignant_1",$texte);
			$texte=preg_replace("/enseignant_suivi_2/","$enseignant_2",$texte);
			$texte=preg_replace("/date_suivi_2/","$date_suivi_2",$texte);
			$texte=preg_replace("/date_suivi_1/","$date_suivi_1",$texte);
			$texte=preg_replace("/ent_fax/","$ent_fax",$texte);
			$texte=preg_replace("/ent_pays/","$ent_pays",$texte);

			$texte=preg_replace("/ent_web/","$ent_web",$texte);	
			$texte=preg_replace("/ent_etoile/","$ent_etoile",$texte);	
			$texte=preg_replace("/ent_chambre/","$ent_chambre",$texte);	
			$texte=preg_replace("/ent_grp_hotelier/","$ent_grp_hotelier",$texte);	
			$texte=preg_replace("/ent_logement/","$ent_logement",$texte);	
			$texte=preg_replace('/ent_service/',"$ent_service",$texte);
			$texte=preg_replace("/ent_indemnitestage/","$ent_indemnitestage",$texte);
			$texte=preg_replace("/anneescolaire/","$anneescolaire",$texte);
				
			$texte=preg_replace("/&#8364;/","euros",$texte);


		}
			$periode="";
			$date_debut="";
			$date_fin="";

		for($h=0;$h<count($dataEnt);$h++) {
			$num_stage=$dataEnt[$h][20];
			if ($dataEnt[$h][32] == 1) {
				$date_debut=dateForm($dataEnt[$h][33]);
				$date_fin=dateForm($dataEnt[$h][34]);
			}else{
				$date_debut=dateForm($dataEnt[$h][18]);
				$date_fin=dateForm($dataEnt[$h][19]);
			}
			$periode="Du $date_debut au $date_fin";
			$nbjourentredeuxdate=nbjours_entre_2_date($dataEnt[$h][18],$dataEnt[$h][19]);
			$texte=preg_replace("nb_jour","$nbjourentredeuxdate",$texte); 
			if ($num_stage == 1) $texte=preg_replace("/periode_1/","$periode",$texte); 
			if ($num_stage == 2) $texte=preg_replace("/periode_2/","$periode",$texte); 
			if ($num_stage == 3) $texte=preg_replace("/periode_3/","$periode",$texte); 
			if ($num_stage == 4) $texte=preg_replace("/periode_4/","$periode",$texte); 
			if ($num_stage == 5) $texte=preg_replace("/periode_5/","$periode",$texte); 
			if ($num_stage == 6) $texte=preg_replace("/periode_6/","$periode",$texte); 
			if ($num_stage == 7) $texte=preg_replace("/periode_7/","$periode",$texte); 
			if ($num_stage == 8) $texte=preg_replace("/periode_8/","$periode",$texte); 
			if ($num_stage == 9) $texte=preg_replace("/periode_9/","$periode",$texte); 
			if ($num_stage == 0) $texte=preg_replace("/periode_0/","$periode",$texte); 
			$texte=preg_replace("/periode_x/","$periode",$texte); 
			if ($num_stage == 1) $texte=preg_replace("/debperio_1/","$date_debut",$texte);
                        if ($num_stage == 2) $texte=preg_replace("/debperio_2/","$date_debut",$texte);
                        if ($num_stage == 3) $texte=preg_replace("/debperio_3/","$date_debut",$texte);
                        if ($num_stage == 4) $texte=preg_replace("/debperio_4/","$date_debut",$texte);
                        if ($num_stage == 5) $texte=preg_replace("/debperio_5/","$date_debut",$texte);
                        if ($num_stage == 6) $texte=preg_replace("/debperio_6/","$date_debut",$texte);
                        if ($num_stage == 7) $texte=preg_replace("/debperio_7/","$date_debut",$texte);
                        if ($num_stage == 8) $texte=preg_replace("/debperio_8/","$date_debut",$texte);
                        if ($num_stage == 9) $texte=preg_replace("/debperio_9/","$date_debut",$texte);
                        if ($num_stage == 0) $texte=preg_replace("/debperio_0/","$date_debut",$texte);
                        $texte=preg_replace("/debperio_x/","$date_debut",$texte);
                        if ($num_stage == 1) $texte=preg_replace("/finperio_1/","$date_fin",$texte);
                        if ($num_stage == 2) $texte=preg_replace("/finperio_2/","$date_fin",$texte);
                        if ($num_stage == 3) $texte=preg_replace("/finperio_3/","$date_fin",$texte);
                        if ($num_stage == 4) $texte=preg_replace("/finperio_4/","$date_fin",$texte);
                        if ($num_stage == 5) $texte=preg_replace("/finperio_5/","$date_fin",$texte);
                        if ($num_stage == 6) $texte=preg_replace("/finperio_6/","$date_fin",$texte);
                        if ($num_stage == 7) $texte=preg_replace("/finperio_7/","$date_fin",$texte);
                        if ($num_stage == 8) $texte=preg_replace("/finperio_8/","$date_fin",$texte);
                        if ($num_stage == 9) $texte=preg_replace("/finperio_9/","$date_fin",$texte);
                        if ($num_stage == 0) $texte=preg_replace("/finperio_0/","$date_fin",$texte);
                        $texte=preg_replace("/finperio_x/","$date_fin",$texte);


		}

		if (!is_dir("./data/pdf_certif/courrierstageconvention")) { mkdir("./data/pdf_certif/courrierstageconvention"); }
		mkdir("./data/pdf_certif/courrierstageconvention/".$_SESSION["id_pers"]);
		$nomEleve=preg_replace("/'/","",$nomEleve);
		$prenomEleve=preg_replace("/'/","",$prenomEleve);
		$nomfic=$nomEleve."_".$prenomEleve."_".$h.".rtf";
		$fic="./data/pdf_certif/courrierstageconvention/".$_SESSION["id_pers"]."/$nomfic";
		$fichier=fopen("$fic","a+");
		fwrite($fichier,$texte);
		fclose($fichier);
		
		$bouton="<input type=button onclick=\"open('telecharger.php?fichier=$fic','_blank','');\" value=\""."Récuperation de la convention de stage"."\"  STYLE=\"font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;\">";
	}

}else{

	// creation PDF
	//
	define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
	include_once('./librairie_pdf/fpdf/fpdf.php');
	include_once('./librairie_pdf/html2pdf.php');

	$pdf=new PDF();  // declaration du constructeur

	if ($laclasse == 1) {

		$eleveT=recupEleve($idClasse); // recup liste eleve
		// nom,prenom,lv1,lv2,elev_id,date_naissance
		for($j=0;$j<count($eleveT);$j++) {  // premiere ligne de la creation PDF
			// variable eleve
			$nomEleve=trim(strtoupper($eleveT[$j][0]));
			$prenomEleve=trim(ucwords(strtolower($eleveT[$j][1])));
			$naissance_eleve=$eleveT[$j][5];
			$lieu_naissance_eleve=$eleveT[$j][6];
			$idEleve=$eleveT[$j][4];
			$adresseEleve=$eleveT[$j][7];
			$codePostalEleve=$eleveT[$j][8];
			$villeEleve=$eleveT[$j][9];
			$telEleve=$eleveT[$j][12];
			$telParent=$eleveT[$j][10];

			if ($naissance_eleve != "") {
				$naissance_eleve=trim(dateForm($naissance_eleve));
			}

			$idClasse=chercheIdClasseDunEleve($idEleve);
			$classe_nom=chercheClasse_nom($idClasse);


			$dataEnt=recherche_stage_eleve_entreprise($idEleve);
			// e.id_eleve ,e.id_entreprise,f.id_serial,f.nom,f.contact,
			// 5 f.adresse,f.code_p,f.ville,f.secteur_ac,f.activite_prin,
			// 10 f.tel,f.fax,f.email,f.contact_fonction,f.pays_ent,
			// e.num_stage, e.compte_tuteur_stage, e.id_prof_visite, e.date_visite_prof2, e.id_prof_visite2, 
			// 20 e.date_visite_prof,f.nbchambre,f.siteweb,f.grphotelier,f.nbetoile,
			// e.loger,e.service,e.indemnitestage, e.pays_stage,e.alternance,
			// 30 e.dateDebutAlternance,e.dateFinAlternance,e.autre_responsable,
			// 33 
	
			for($h=0;$h<count($dataEnt);$h++) {
				$responsable2=trim($dataEnt[$h][32]);

				$ent_nom=$dataEnt[$h][3];
				$ent_adr=$dataEnt[$h][5];
				$ent_cp=$dataEnt[$h][6];
				$ent_directeur=$dataEnt[$h][4];
				if (trim($responsable2) != "") $ent_directeur=$responsable2;

				$ent_localite=$dataEnt[$h][7];
				$ent_tel=$dataEnt[$h][10];
				$ent_fax=$dataEnt[$h][11];
				$ent_mail=$dataEnt[$h][12];
				$ent_fonction=$dataEnt[$h][13];
				$ent_pays=$dataEnt[$h][14];
				$ent_tuteur=recherche_personne($dataEnt[$h][16]);

				$nom_stage=chercheNomStage($dataEnt[$h][15]);
				$enseignant_1=recherche_personne($dataEnt[$h][17]);
				$enseignant_2=recherche_personne($dataEnt[$h][19]);
				$date_suivi_1=dateForm($dataEnt[$h][20]);
				$date_suivi_2=dateForm($dataEnt[$h][18]);

				$ent_web=$dataEnt[$h][25];
				$ent_etoile=$dataEnt[$h][27];
				$ent_chambre=$dataEnt[$h][24];
				$ent_grp_hotelier=$dataEnt[$h][26];
				$ent_logement=($dataEnt[$h][28] == 1) ? "oui" : "non";
				$ent_service=$dataEnt[$h][26];
				$ent_indemnitestage=$dataEnt[$h][27];


				$data=config_param_visu("param_conv_stag#$idClasse$nbconv");
				$texte=$data[0][0];


				$periode="";
				$date_debut="";
				$date_fin="";
				for($h=0;$h<count($dataEnt);$h++) {
					$num_stage=$dataEnt[$h][15];
					if ($dataEnt[$h][29] == 1) {
						$date_debut=dateForm($dataEnt[$h][30]);
						$date_fin=dateForm($dataEnt[$h][31]);
					}else{
						$date_debut=dateForm($dataEnt[$h][33]);
						$date_fin=dateForm($dataEnt[$h][34]);
					}
					$periode="Du $date_debut au $date_fin";
					
					$nbjourentredeuxdate=nbjours_entre_2_date($dataEnt[$h][18],$dataEnt[$h][19]);
					$texte=preg_replace("/nb_jour/","$nbjourentredeuxdate",$texte); 
					if ($num_stage == 1) $texte=preg_replace("/periode_1/","$periode",$texte); 
					if ($num_stage == 2) $texte=preg_replace("/periode_2/","$periode",$texte); 
					if ($num_stage == 3) $texte=preg_replace("/periode_3/","$periode",$texte); 
					if ($num_stage == 4) $texte=preg_replace("/periode_4/","$periode",$texte); 
					if ($num_stage == 5) $texte=preg_replace("/periode_5/","$periode",$texte); 
					if ($num_stage == 6) $texte=preg_replace("/periode_6/","$periode",$texte); 
					if ($num_stage == 7) $texte=preg_replace("/periode_7/","$periode",$texte); 
					if ($num_stage == 8) $texte=preg_replace("/periode_8/","$periode",$texte); 
					if ($num_stage == 9) $texte=preg_replace("/periode_9/","$periode",$texte); 
					if ($num_stage == 0) $texte=preg_replace("/periode_0/","$periode",$texte); 
					$texte=preg_replace("/periode_x/","$periode",$texte); 

					if ($num_stage == 1) $texte=preg_replace("/debperio_1/","$date_debut",$texte);
	                                if ($num_stage == 2) $texte=preg_replace("/debperio_2/","$date_debut",$texte);
	                                if ($num_stage == 3) $texte=preg_replace("/debperio_3/","$date_debut",$texte);
	                                if ($num_stage == 4) $texte=preg_replace("/debperio_4/","$date_debut",$texte);
        	                        if ($num_stage == 5) $texte=preg_replace("/debperio_5/","$date_debut",$texte);
	                                if ($num_stage == 6) $texte=preg_replace("/debperio_6/","$date_debut",$texte);
	                                if ($num_stage == 7) $texte=preg_replace("/debperio_7/","$date_debut",$texte);
	                                if ($num_stage == 8) $texte=preg_replace("/debperio_8/","$date_debut",$texte);
	                                if ($num_stage == 9) $texte=preg_replace("/debperio_9/","$date_debut",$texte);
        	                        if ($num_stage == 0) $texte=preg_replace("/debperio_0/","$date_debut",$texte);
	                                $texte=preg_replace("/debperio_x/","$date_debut",$texte);
	
	                                if ($num_stage == 1) $texte=preg_replace("/finperio_1/","$date_fin",$texte);
	                                if ($num_stage == 2) $texte=preg_replace("/finperio_2/","$date_fin",$texte);
	                                if ($num_stage == 3) $texte=preg_replace("/finperio_3/","$date_fin",$texte);
	                                if ($num_stage == 4) $texte=preg_replace("/finperio_4/","$date_fin",$texte);
	                                if ($num_stage == 5) $texte=preg_replace("/finperio_5/","$date_fin",$texte);
	                                if ($num_stage == 6) $texte=preg_replace("/finperio_6/","$date_fin",$texte);
        	                        if ($num_stage == 7) $texte=preg_replace("/finperio_7/","$date_fin",$texte);
	                                if ($num_stage == 8) $texte=preg_replace("/finperio_8/","$date_fin",$texte);
	                                if ($num_stage == 9) $texte=preg_replace("/finperio_9/","$date_fin",$texte);
	                                if ($num_stage == 0) $texte=preg_replace("/finperio_0/","$date_fin",$texte);
	                                $texte=preg_replace("/finperio_x/","$date_fin",$texte);

				}


				$texte=preg_replace("/NomEleve/","$nomEleve",$texte);
				$texte=preg_replace("/PrenomEleve/","$prenomEleve",$texte);
				$texte=preg_replace("/classe_eleve/","$classe_nom",$texte);
				$texte=preg_replace("/naissance_eleve/","$naissance_eleve",$texte);	
				$texte=preg_replace("/ent_nom/","$ent_nom",$texte);
				$texte=preg_replace("/ent_rs/","$ent_rs",$texte);
				$texte=preg_replace("/ent_adr/","$ent_adr",$texte);
				$texte=preg_replace("/ent_cp/","$ent_cp",$texte);
				$texte=preg_replace("/ent_mail/","$ent_mail",$texte);
				$texte=preg_replace("/ent_directeur/","$ent_directeur",$texte);
				$texte=preg_replace("/ent_localite/","$ent_localite",$texte);
				$texte=preg_replace("/ent_tel/","$ent_tel",$texte);
				$texte=preg_replace("/lieu_naissance/","$lieu_naissance_eleve",$texte);
				$texte=preg_replace("/ent_directeur/","$ent_directeur",$texte);
				$texte=preg_replace("/directeur/","$directeur",$texte);
				$texte=preg_replace("/nom_stage/","$nom_stage",$texte);
				$texte=preg_replace("/ent_dir_fonction/","$ent_fonction",$texte);
				$texte=preg_replace("/adresse_eleve/","$adresseEleve",$texte);
				$texte=preg_replace("/ccp_eleve/","$codePostalEleve",$texte);
				$texte=preg_replace("/ville_eleve/","$villeEleve",$texte);
				$texte=preg_replace("/tel_eleve/","$telEleve",$texte);
				$texte=preg_replace("/tel_parent/","$telParent",$texte);
				$dateJour=dateDMY();
				$texte=preg_replace("/date_du_jour/","$dateJour",$texte);
				$texte=preg_replace("/ent_tuteur/","$ent_tuteur",$texte);
				$texte=preg_replace("/enseignant_suivi_1/","$enseignant_1",$texte);
				$texte=preg_replace("/enseignant_suivi_2/","$enseignant_2",$texte);
				$texte=preg_replace("/date_suivi_2/","$date_suivi_2",$texte);
				$texte=preg_replace("/date_suivi_1/","$date_suivi_1",$texte);
				$texte=preg_replace("/ent_fax/","$ent_fax",$texte);
				$texte=preg_replace("/ent_pays/","$ent_pays",$texte);
				
				$texte=preg_replace("/ent_web/","$ent_web",$texte);	
				$texte=preg_replace("/ent_etoile/","$ent_etoile",$texte);	
				$texte=preg_replace("/ent_chambre/","$ent_chambre",$texte);	
				$texte=preg_replace("/ent_grp_hotelier/","$ent_grp_hotelier",$texte);	
				$texte=preg_replace("/ent_logement/","$ent_logement",$texte);	
				$texte=preg_replace("/ent_service/","$ent_service",$texte);
				$texte=preg_replace("/ent_indemnitestage/","$ent_indemnitestage",$texte);
				$texte=preg_replace("/anneescolaire/","$anneescolaire",$texte);
				$texte=preg_replace("/&#8364;/","euros",$texte);




				$okconv=1;	

				$pdf->AddPage();

				$xtitre=80;  // sans logo
				$xcoor0=3;   // sans logo
				$ycoor0=3;   // sans logo

				if (file_exists("./data/image_pers/logo_bull.jpg")) {
					$xlogo=3;	
					$ylogo=3;
					$xcoor0=30;
					$ycoor0=3;
					$xtitre=90; // avec logo
					$logo="./data/image_pers/logo_bull.jpg";
					$pdf->Image($logo,$xlogo,$ylogo);
				}

				// declaration variable
				$coordonne1= strtoupper($nom_etablissement)."<BR><BR>";
				$coordonne2=$adresse."<BR>";
				$coordonne3=$postal." - ".ucwords($ville)."<BR>";
				$coordonne4="Téléphone : ".$tel."<BR>";
				$coordonne5="E-mail : ".$mail;
				// FIN variables

				// Debut création PDF
				// mise en place des coordonnées
				$pdf->SetFont('Arial','',11);
				$pdf->SetXY($xcoor0,$ycoor0);
				$pdf->WriteHTML($coordonne1);
				$ycoor0=$ycoor0+5;
				$pdf->SetXY($xcoor0,$ycoor0);
				$pdf->WriteHTML($coordonne2);
				$ycoor0=$ycoor0+5;
				$pdf->SetXY($xcoor0,$ycoor0);
				$pdf->WriteHTML($coordonne3);
				$ycoor0=$ycoor0+5;
				$pdf->SetXY($xcoor0,$ycoor0);
				$pdf->SetFont('Arial','',8);
				$pdf->WriteHTML($coordonne4);	
				$ycoor0=$ycoor0+5;
				$pdf->SetXY($xcoor0,$ycoor0);
				$pdf->WriteHTML($coordonne5);
				//fin coordonnees	

				// insertion de la date
				$date=dateDMY();
				$Pdate="Date: ".$date;
				$pdf->SetFont('Arial','',10);
				$pdf->SetXY(150,3);
				$pdf->WriteHTML($Pdate);
				// fin d'insertion

				// cadre principale
				$pdf->SetXY(15,55);
				$pdf->WriteHTML($texte);
				// fin cadre principale	

			}
	
		}	
		$fichierpdf="./data/pdf_certif/convention-de-stage_".$fic.".pdf";
		if (file_exists($fichierpdf))  {  @unlink($fichierpdf); }
		$pdf->output('F',$fichierpdf);
	}else {

		$texte=$data[0][0];
		$dataEnt=recherche_stage_eleve_entreprise($ideleve);
		$idClasse=chercheIdClasseDunEleve($ideleve);
		$classe_nom=chercheClasse_nom($idClasse);

		$data=config_param_visu("param_conv_stag#$idClasse$nbconv");

		//e.id_eleve ,e.id_entreprise,f.id_serial,
		//f.nom,f.contact,f.adresse,
		//f.code_p,f.ville,f.secteur_ac,
		//f.activite_prin,f.tel,f.fax,
		//f.email,f.contact_fonction,f.pays_ent,
	//15	//e.num_stage, e.compte_tuteur_stage, e.id_prof_visite,
		// e.date_visite_prof2, e.id_prof_visite2, e.date_visite_prof,
		// f.nbchambre,f.siteweb,f.grphotelier,
		// f.nbetoile,e.loger,e.service,
		// e.indemnitestage, e.pays_stage,e.alternance,
	// 30	// e.dateDebutAlternance,e.dateFinAlternance,e.autre_responsable
	// 33 	   g.datedebut,g.datefin

		$okconv=0;
		for($h=0;$h<count($dataEnt);$h++) {
			$responsable2=trim($dataEnt[$h][32]);

			$ent_nom=$dataEnt[$h][3];
			$ent_adr=$dataEnt[$h][5];
			$ent_cp=$dataEnt[$h][6];
			$ent_directeur=$dataEnt[$h][4];
			if (trim($responsable2) != "") $ent_directeur=$responsable2;

			$ent_localite=$dataEnt[$h][7];
			$ent_tel=$dataEnt[$h][10];
			$ent_fax=$dataEnt[$h][11];
			$ent_mail=$dataEnt[$h][12];
			$ent_fonction=$dataEnt[$h][13];
			$ent_pays=$dataEnt[$h][14];
			$ent_tuteur=recherche_personne($dataEnt[$h][16]);
			$nom_stage=chercheNomStage($dataEnt[$h][15]);
			$enseignant_1=recherche_personne($dataEnt[$h][17]);
			$enseignant_2=recherche_personne($dataEnt[$h][19]);
			$date_suivi_1=dateForm($dataEnt[$h][20]);
			$date_suivi_2=dateForm($dataEnt[$h][18]);

			$ent_web=$dataEnt[$h][22];
			$ent_etoile=$dataEnt[$h][23];
			$ent_chambre=$dataEnt[$h][21];
			$ent_grp_hotelier=$dataEnt[$h][24];
			$ent_logement=($dataEnt[$h][25] == 1) ? "oui" : "non";
			$ent_service=$dataEnt[$h][26];
			$ent_indemnitestage=$dataEnt[$h][27];

			$data=config_param_visu("param_conv_stag#$idClasse$nbconv");
			$texte=$data[0][0];

			$periode="";
			$date_debut="";
			$date_fin="";
			for($h=0;$h<count($dataEnt);$h++) {
				$num_stage=$dataEnt[$h][15];
				if ($dataEnt[$h][29] == 1) {
					$date_debut=dateForm($dataEnt[$h][30]);
					$date_fin=dateForm($dataEnt[$h][31]);
				}else{
					$date_debut=dateForm($dataEnt[$h][33]);
					$date_fin=dateForm($dataEnt[$h][34]);
				}
				$periode="Du $date_debut au $date_fin";
				$nbjourentredeuxdate=nbjours_entre_2_date($dataEnt[$h][18],$dataEnt[$h][19]);
				$texte=preg_replace("/nb_jour/","$nbjourentredeuxdate",$texte); 
				if ($num_stage == 1) $texte=preg_replace("/periode_1/","$periode",$texte); 
				if ($num_stage == 2) $texte=preg_replace("/periode_2/","$periode",$texte); 
				if ($num_stage == 3) $texte=preg_replace("/periode_3/","$periode",$texte); 
				if ($num_stage == 4) $texte=preg_replace("/periode_4/","$periode",$texte); 
				if ($num_stage == 5) $texte=preg_replace("/periode_5/","$periode",$texte); 
				if ($num_stage == 6) $texte=preg_replace("/periode_6/","$periode",$texte); 
				if ($num_stage == 7) $texte=preg_replace("/periode_7/","$periode",$texte); 
				if ($num_stage == 8) $texte=preg_replace("/periode_8/","$periode",$texte); 
				if ($num_stage == 9) $texte=preg_replace("/periode_9/","$periode",$texte); 
				if ($num_stage == 0) $texte=preg_replace("/periode_0/","$periode",$texte);  
				$texte=preg_replace("/periode_x/","$periode",$texte); 



				if ($num_stage == 1) $texte=preg_replace("/debperio_1/","$date_debut",$texte);
                                if ($num_stage == 2) $texte=preg_replace("/debperio_2/","$date_debut",$texte);
                                if ($num_stage == 3) $texte=preg_replace("/debperio_3/","$date_debut",$texte);
                                if ($num_stage == 4) $texte=preg_replace("/debperio_4/","$date_debut",$texte);
                                if ($num_stage == 5) $texte=preg_replace("/debperio_5/","$date_debut",$texte);
                                if ($num_stage == 6) $texte=preg_replace("/debperio_6/","$date_debut",$texte);
                                if ($num_stage == 7) $texte=preg_replace("/debperio_7/","$date_debut",$texte);
                                if ($num_stage == 8) $texte=preg_replace("/debperio_8/","$date_debut",$texte);
                                if ($num_stage == 9) $texte=preg_replace("/debperio_9/","$date_debut",$texte);
                                if ($num_stage == 0) $texte=preg_replace("/debperio_0/","$date_debut",$texte);
                                $texte=preg_replace("/debperio_x/","$date_debut",$texte);

                                if ($num_stage == 1) $texte=preg_replace("/finperio_1/","$date_fin",$texte);
                                if ($num_stage == 2) $texte=preg_replace("/finperio_2/","$date_fin",$texte);
                                if ($num_stage == 3) $texte=preg_replace("/finperio_3/","$date_fin",$texte);
                                if ($num_stage == 4) $texte=preg_replace("/finperio_4/","$date_fin",$texte);
                                if ($num_stage == 5) $texte=preg_replace("/finperio_5/","$date_fin",$texte);
                                if ($num_stage == 6) $texte=preg_replace("/finperio_6/","$date_fin",$texte);
                                if ($num_stage == 7) $texte=preg_replace("/finperio_7/","$date_fin",$texte);
                                if ($num_stage == 8) $texte=preg_replace("/finperio_8/","$date_fin",$texte);
                                if ($num_stage == 9) $texte=preg_replace("/finperio_9/","$date_fin",$texte);
                                if ($num_stage == 0) $texte=preg_replace("/finperio_0/","$date_fin",$texte);
                                $texte=preg_replace("/finperio_x/","$date_fin",$texte);

			}
	
			$texte=preg_replace("/NomEleve/","$nomEleve",$texte);
			$texte=preg_replace("/PrenomEleve/","$prenomEleve",$texte);
			$texte=preg_replace("/classe_eleve/","$classe_nom",$texte);
			$texte=preg_replace("/naissance_eleve/","$naissance_eleve",$texte);	
			$texte=preg_replace("/ent_nom/","$ent_nom",$texte);
			$texte=preg_replace("/ent_rs/","$ent_rs",$texte);
			$texte=preg_replace("/ent_adr/","$ent_adr",$texte);
			$texte=preg_replace("/ent_cp/","$ent_cp",$texte);
			$texte=preg_replace("/ent_mail/","$ent_mail",$texte);
			$texte=preg_replace("/ent_directeur/","$ent_directeur",$texte);
			$texte=preg_replace("/ent_localite/","$ent_localite",$texte);
			$texte=preg_replace("/ent_tel/","$ent_tel",$texte);
			$texte=preg_replace("/lieu_naissance/","$lieu_naissance_eleve",$texte);
			$texte=preg_replace("/ent_directeur/","$ent_directeur",$texte);
			$texte=preg_replace("/directeur/","$directeur",$texte);
			$texte=preg_replace("/nom_stage/","$nom_stage",$texte);
			$texte=preg_replace("/ent_dir_fonction/","$ent_fonction",$texte);
			$texte=preg_replace("/adresse_eleve/","$adresseEleve",$texte);
			$texte=preg_replace("/ccp_eleve/","$codePostalEleve",$texte);
			$texte=preg_replace("/ville_eleve/","$villeEleve",$texte);
			$texte=preg_replace("/tel_eleve/","$telEleve",$texte);
			$texte=preg_replace("/tel_parent/","$telParent",$texte);
			$dateJour=dateDMY();
			$texte=preg_replace("/date_du_jour/","$dateJour",$texte);
			$texte=preg_replace("/ent_tuteur/","$ent_tuteur",$texte);
			$texte=preg_replace("/enseignant_suivi_1/","$enseignant_1",$texte);
			$texte=preg_replace("/enseignant_suivi_2/","$enseignant_2",$texte);
			$texte=preg_replace("/date_suivi_2/","$date_suivi_2",$texte);
			$texte=preg_replace("/date_suivi_1/","$date_suivi_1",$texte);
			$texte=preg_replace("/ent_fax/","$ent_fax",$texte);
			$texte=preg_replace("/ent_web/","$ent_web",$texte);	
			$texte=preg_replace("/ent_etoile/","$ent_etoile",$texte);	
			$texte=preg_replace("/ent_chambre/","$ent_chambre",$texte);	
			$texte=preg_replace("/ent_grp_hotelier/","$ent_grp_hotelier",$texte);	
			$texte=preg_replace("/ent_logement/","$ent_logement",$texte);	
			$texte=preg_replace("/ent_service/","$ent_service",$texte);
			$texte=preg_replace("/ent_indemnitestage/","$ent_indemnitestage",$texte);
			$texte=preg_replace("/ent_pays/","$ent_pays",$texte);
			$texte=preg_replace("/anneescolaire/","$anneescolaire",$texte);
			$texte=preg_replace("/&#8364;/","euros",$texte);

			print " &nbsp;&nbsp; La convention de ".$nomEleve." ".$prenomEleve." ".LANGCERTIF1bis.".<br><br>";
			$fic=$eleve;

			$okconv=1;



$pdf->AddPage();

$xtitre=80;  // sans logo
$xcoor0=3;   // sans logo
$ycoor0=3;   // sans logo

if (file_exists("./data/image_pers/logo_bull.jpg")) {
	$xlogo=3;
	$ylogo=3;
	$xcoor0=30;
	$ycoor0=3;
	$xtitre=90; // avec logo
	$logo="./data/image_pers/logo_bull.jpg";
	$pdf->Image($logo,$xlogo,$ylogo);
}


// declaration variable
$coordonne1= strtoupper($nom_etablissement)."<BR><BR>";
$coordonne2=$adresse."<BR>";
$coordonne3=$postal." - ".ucwords($ville)."<BR>";
$coordonne4="Téléphone : ".$tel."<BR>";
$coordonne5="E-mail : ".$mail;
// FIN variable

// Debut création PDF
// mise en place des coordonnées
$pdf->SetFont('Arial','',12);
$pdf->SetXY($xcoor0,$ycoor0);
$pdf->WriteHTML($coordonne1);
$ycoor0=$ycoor0+5;
$pdf->SetXY($xcoor0,$ycoor0);
$pdf->WriteHTML($coordonne2);
$ycoor0=$ycoor0+5;
$pdf->SetXY($xcoor0,$ycoor0);
$pdf->WriteHTML($coordonne3);
$ycoor0=$ycoor0+5;
$pdf->SetXY($xcoor0,$ycoor0);
$pdf->SetFont('Arial','',8);
$pdf->WriteHTML($coordonne4);
$ycoor0=$ycoor0+5;
$pdf->SetXY($xcoor0,$ycoor0);
$pdf->WriteHTML($coordonne5);
//fin coordonnees


// insertion de la date
$date=dateDMY();
$Pdate="Date: ".$date;
$pdf->SetFont('Courier','',10);
$pdf->SetXY(150,3);
$pdf->WriteHTML($Pdate);
// fin d'insertion

// cadre principale
$pdf->SetFont('Arial','',11);
$pdf->SetXY(15,55);
$pdf->WriteHTML($texte);
// fin cadre principale

}

$fichierpdf="./data/pdf_certif/convention-de-stage_".$fic.".pdf";
if (file_exists($fichierpdf))  {  @unlink($fichierpdf); }
$pdf->output('F',$fichierpdf);

}

}
// fin PDF

?>



<ul><ul>
<br>
<?php if ($okconv == 1) { ?>

	<?php if ($_SESSION["membre"] == "menuprof") { ?>
		<input type=button onclick="open('visu_pdf_prof.php?id=<?php print $fichierpdf?>','_blank','');" value="<?php print LANGPER5?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
	<?php }else{ ?>	
		<input type=button onclick="open('visu_pdf_admin.php?id=<?php print $fichierpdf?>','_blank','');" value="<?php print LANGPER5?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
<?php } 
}elseif ($okconv == 2) {
	print $bouton;
}else{
?>
	<font class=T2><?php print $erreur ?></font>
<?php } ?>
	</ul></ul>
<br><br>
<?php 
print "<table align='center'><tr><td>";
if ($_SESSION["membre"] == "menuprof") {
	print "<script language=JavaScript>buttonMagicRetour('gestion_stage_profp.php','_parent')</script>&nbsp;&nbsp;";
}else{
	print "<script language=JavaScript>buttonMagicRetour2('gestion_stage.php','_parent','".LANGCIRCU14."')</script>&nbsp;&nbsp;</td><td>";	
	print "<script language=JavaScript>buttonMagicRetour2('gestion_stage_convention_eleve.php?idclasse=$idClasse','_parent','".LANGTMESS529."')</script>&nbsp;&nbsp;";	
}
print "</td></tr></table>";
?>

	<br /><br />
<!-- // fin  -->
</td></tr></table>
</form>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION["membre"] == "menuadmin") :
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
?>
</BODY></HTML>
<?php
Pgclose();
?>
