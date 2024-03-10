<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
$saisie_trimestre=$_COOKIE["saisie_trimestre"];
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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script type='text/javascript' src='./librairie_js/ajax-moyenne.js'></script>
<script language="JavaScript" src="./librairie_js/ajaxIA.js"></script>
<title>Triade Vidéo-Projecteur</title>
</head>
<body id='coulfond1' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("profadmin");
include_once('librairie_php/recupnoteperiode.php');
$cnx=cnx();
$ok=1;



if (isset($_POST["valide"])) {
	$idclasse=$_POST["saisie_classe"];
	$trimes=$_POST["saisie_trimestre"];
	$nb=$_POST["saisie_nb"];
	$typecom=$_POST["typecom"];
	$anneeScolaire=$_POST["anneeScolaire"];
	$ok=0;
	$ideleve=$_POST["saisie_eleve"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
	$res=execSql($sql);
	$data_eleve=chargeMat($res);
	for ($j=0;$j<count($data_eleve);$j++) {
		if ($ideleve == $data_eleve[$j][1]) {
			$i=$j;
			break;
		}
	}
	$iplus= $i + 1;
	$imoins = $i - 1;

	$tri=$trimes;

	for($i=0;$i<$nb;$i++) {
		$ref="saisie_prof_$i";
		$idprof=$_POST[$ref];
		$ref="saisie_matiere_$i";
		$idmatiere=$_POST[$ref];
		$ref="saisie_groupe_$i";
		$idgroupe=$_POST[$ref];;
		$ref="saisie_text_$i";
		$commentaire=$_POST[$ref];
		$ref="saisie_eleve_$i";
		$idEleve=$_POST[$ref];
		enregistrement_com_bulletin($idmatiere,$idclasse,$tri,$idEleve,$commentaire,$idprof,$idgroupe,$typecom,$anneeScolaire);
	}
	$nomclasse=chercheClasse_nom($idclasse);
	$nommatiere=chercheMatiereNom($idmatiere);
	history_cmd($_SESSION["nom"],"MODIF","Commentaire Bulletin $nomclasse $nommatiere");
	$okenr=1;
}



/***************************************************************************/
if (isset($_GET["MT1"])) {
	$moyenClasseGenT1=$_GET["MT1"];
	$moyenClasseGenT2=$_GET["MT2"];
	$moyenClasseGenT3=$_GET["MT3"];
	$tri=$_GET["saisie_trimestre"];
	$typecom=$_GET["typecom"];
	
}else{
	$tri=$_POST["saisie_trimestre"];
	$idclasse=$_POST["saisie_classe"];
	$typecom=$_POST["typecom"];
	// recherche des dates de debut et fin
	$dateRecup=recupDateTrimByIdclasse("trimestre1",$idclasse,$anneeScolaire);
	for($j=0;$j<count($dateRecup);$j++) {
       	 	$dateDebut=$dateRecup[$j][0];
	       	 $dateFin=$dateRecup[$j][1];
	}
	$dateDebutT1=dateForm($dateDebut);
	$dateFinT1=dateForm($dateFin);
	//-----/
	$dateRecup=recupDateTrimByIdclasse("trimestre2",$idclasse,$anneeScolaire);
	for($j=0;$j<count($dateRecup);$j++) {
	       	 $dateDebut=$dateRecup[$j][0];
        	$dateFin=$dateRecup[$j][1];	
	}
	$dateDebutT2=dateForm($dateDebut);
	$dateFinT2=dateForm($dateFin);
	//-----/
	$dateRecup=recupDateTrimByIdclasse("trimestre3",$idclasse,$anneeScolaire);
	for($j=0;$j<count($dateRecup);$j++) {
        	$dateDebut=$dateRecup[$j][0];
	        $dateFin=$dateRecup[$j][1];
	}
	$dateDebutT3=dateForm($dateDebut);
	$dateFinT3=dateForm($dateFin);
	//-----/

//	$ordre=ordre_matiere($idclasse); // recup ordre matiere
//	$eleveT=recupEleve($idclasse); // recup liste eleve

//	$moyenClasseGenT1="";
//	$moyenClasseGenT2="";
//	$moyenClasseGenT3="";

	// idclasse,tableaueleve,datedebut,datefin,ordrematriere
//	$moyenClasseGenT1=calculMoyenClasse($idclasse,$eleveT,$dateDebutT1,$dateFinT1,$ordre);
//	$moyenClasseGenT2=calculMoyenClasse($idclasse,$eleveT,$dateDebutT2,$dateFinT2,$ordre);
//	$moyenClasseGenT3=calculMoyenClasse($idclasse,$eleveT,$dateDebutT3,$dateFinT3,$ordre);
//	if (($moyenClasseGenT1 == "") || ($moyenClasseGenT1 < 0)) {$moyenClasseGenT1=""; }
//	if (($moyenClasseGenT2 == "") || ($moyenClasseGenT2 < 0))  {$moyenClasseGenT2=""; }
//	if (($moyenClasseGenT3 == "") || ($moyenClasseGenT3 < 0)) {$moyenClasseGenT3=""; }
}




// Fin du Calcul moyenne classe
//
/*****************************************************************************/

if (isset($_POST["supp"])) {
	$idclasse=$_POST["saisie_classe"];
	$sql="SELECT * FROM ${prefixe}eleves  WHERE classe='$idclasse' ";
        $res=execSql($sql);
        $data=chargeMat($res);
	if (count($data) <=  0 ){
		print "<script language=JavaScript>";
		print "location.href='editer_bulletin.php?info=1'";
		print "</script>";
	}

}

$disabledPrecedent="";
$disabledSuivant="";
// via precendent ou suivant


// en direct avec le select


if (isset($_GET["apres"])) {
	$i=$_GET["apres"];
	$trimes=$_GET["saisie_trimestre"];
	$tri=$_GET["saisie_trimestre"];
	$idclasse=$_GET["saisie_classe"];
	$typecom=$_GET["typecom"];
	//$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' AND annee_scolaire='$anneeScolaire' ORDER BY nom";
	$sql="(SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves , ${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' AND annee_scolaire='$anneeScolaire') UNION (SELECT libelle,e.elev_id,e.nom,e.prenom  FROM ${prefixe}eleves e , ${prefixe}classes c , ${prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' GROUP BY e.nom) ORDER BY nom";
	$res=execSql($sql);
	$data_eleve=chargeMat($res);
	if ($i == 0) {$i=0;  $disabledPrecedent="disabled='disabled'"; }
	$nb=count($data_eleve);
	if ($i >= $nb) { $i=$nb-1 ; $disabledSuivant="disabled='disabled'";  }
	$ideleve=$data_eleve[$i][1];
	$ok=0;
	$iplus= $i + 1;
	$imoins = $i - 1;
}



if (isset($_POST["direct_eleve"])) {
	$idclasse=$_POST["saisie_classe"];
	$trimes=$_POST["saisie_trimestre"];
	$typecom=$_POST["typecom"];
	$ok=0;
	$ideleve=$_POST["direct_eleve"];
	//$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse'  AND annee_scolaire='$anneeScolaire' ORDER BY nom";
	$sql="(SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves , ${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' AND annee_scolaire='$anneeScolaire') UNION (SELECT libelle,e.elev_id,e.nom,e.prenom  FROM ${prefixe}eleves e , ${prefixe}classes c , ${prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' GROUP BY e.nom) ORDER BY nom";
	$res=execSql($sql);
	$data_eleve=chargeMat($res);
	for ($j=0;$j<count($data_eleve);$j++) {
		if ($ideleve == $data_eleve[$j][1]) {
			$i=$j;
			break;
		}
	}
	$iplus= $i + 1;
	$imoins = $i - 1;
	if ($i == 0) { $disabledPrecedent="disabled='disabled'"; }else{ $disabledPrecedent=""; }
}

// premier acces
if ($ok == 1) {
	$idclasse=$_POST["saisie_classe"];
	$trimes=$_POST["saisie_trimestre"];
	$typecom=$_POST["typecom"];
	//$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse'  AND annee_scolaire='$anneeScolaire' ORDER BY nom";
	$sql="(SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves , ${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' AND annee_scolaire='$anneeScolaire') UNION (SELECT libelle,e.elev_id,e.nom,e.prenom  FROM ${prefixe}eleves e , ${prefixe}classes c , ${prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' GROUP BY e.nom) ORDER BY nom";
	$res=execSql($sql);
	$data_eleve=chargeMat($res);
	$ideleve=$data_eleve[0][1];
	$i=0;
	$iplus = $i + 1;
	$imoins = $i - 1;
	$disabledPrecedent="disabled='disabled'";
}
//-----------------------------------------------//

$dateRecup=recupDateTrimByIdclasse($tri,$idclasse,$anneeScolaire);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebut=dateForm($dateDebut);
$dateFin=dateForm($dateFin);

?>

<table border="0" width="100%" align="center"  height="100%">
<tr>
<td colspan=2 valign=top height=10 width=100%>
	<table width=100% border=0 ><tr>
	<td valign=top >
	<form method=post onsubmit="return valide_supp_choix('direct_eleve','un élève')" name=formulaire>
	<input type=button class=BUTTON <?php print $disabledPrecedent ?> value="<-- Précédent" onclick="open('editer_bulletin2.php?apres=<?php print $imoins?>&saisie_classe=<?php print $idclasse?>&saisie_trimestre=<?php print $trimes?>&MT1=<?php print $moyenClasseGenT1?>&MT2=<?php print $moyenClasseGenT2?>&MT3=<?php print $moyenClasseGenT3?>&typecom=<?php print $typecom ?>','editer_bulletin','')">
	</td><td width=200 valign="top"><?php 
	include_once("./librairie_php/lib_conexpersistant.php"); 
	connexpersistance("font-weight:bold;font-size:11px;text-align: center;"); 
	?>
	</td>
        <td align=center valign=top >
<!--	<input type=text  value="<?php print ucwords($trimes)?>" size=10 class=BUTTON readonly> -->
	&nbsp;&nbsp;&nbsp;
	<input type=hidden name="saisie_classe" value="<?php print $idclasse?>">
	<input type=hidden name="saisie_trimestre" value="<?php print $trimes?>">
	<input type=hidden name="typecom" value="<?php print $typecom?>" />
	<select name="direct_eleve">
	<option id="select0" > <?php print LANGCHOIX?> </option>
	<?php
	//$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' AND annee_scolaire='$anneeScolaire' ORDER BY nom";
	$sql="(SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves , ${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' AND annee_scolaire='$anneeScolaire') UNION (SELECT libelle,e.elev_id,e.nom,e.prenom  FROM ${prefixe}eleves e , ${prefixe}classes c , ${prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' GROUP BY e.nom) ORDER BY nom";
	$res=execSql($sql);
	$data_eleve=chargeMat($res);
	for ($j=0;$j<count($data_eleve);$j++) {
	?>
	<option STYLE='color:#000066;background-color:#CCCCFF'  value="<?php print $data_eleve[$j][1]?>"><?php print ucwords(trim($data_eleve[$j][2]))." ".trim($data_eleve[$j][3])?></option>
	<?php
	}
	?>
	</select> <input type=submit class=BUTTON value="Visualiser" >
	</td>
	<td align=right valign=middle ><input type=button class=BUTTON value="Suivant -->" <?php print $disabledSuivant ?> onclick="open('editer_bulletin2.php?apres=<?php print $iplus ?>&saisie_classe=<?php print $idclasse?>&saisie_trimestre=<?php print $trimes?>&MT1=<?php print $moyenClasseGenT1?>&MT2=<?php print $moyenClasseGenT2?>&MT3=<?php print $moyenClasseGenT3?>&typecom=<?php print $typecom ?>','editer_bulletin','')">
	</form>
	</td></tr></table>
</td></tr>
<tr><td valign=top height=10 >
<font class=T2>
<?php
//$sql="SELECT  elev_id,nom,prenom,c.libelle,lv1,lv2,`option`,regime,date_naissance,numero_eleve  FROM ${prefixe}eleves, ${prefixe}classes c WHERE elev_id='$ideleve' AND c.code_class='$idclasse' AND annee_scolaire='$anneeScolaire' ";
$sql="(SELECT elev_id,nom,prenom,libelle,lv1,lv2,`option`,regime,date_naissance,numero_eleve  FROM ${prefixe}eleves , ${prefixe}classes  WHERE elev_id='$ideleve' AND classe='$idclasse' AND code_class='$idclasse' AND annee_scolaire='$anneeScolaire') UNION (SELECT e.elev_id,e.nom,e.prenom,libelle,e.lv1,e.lv2,e.`option`,e.regime,e.date_naissance,e.numero_eleve  FROM ${prefixe}eleves e , ${prefixe}classes c , ${prefixe}eleves_histo h WHERE e.elev_id='$ideleve' AND h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' GROUP BY e.elev_id) ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);
if( count($data)  <= 0 ) {
	print("<b><font color=red>Données introuvables</font></b>");
}else { //debut else
	?>
	<img src="image_trombi.php?idE=<?php print $ideleve?>" align=left>
	<font class=T2 ><?php print LANGNA1 ?> : <b><?php print strtoupper(trim($data[0][1]))?></b><br>
	<?php print LANGNA2 ?> : <b><?php print ucwords(trim($data[0][2]))?></b> <br>
<?php 
	$annenaissance=dateForm($data[0][8]);
	$age="(".calculAge(dateForm($data[0][8]))." ans) ";
	if ($annenaissance == "00/00/0000") {
		$annenaissance="??";
		$age="";
	}
	?>
		Age : <b><?php print $annenaissance ?>&nbsp;&nbsp;</b><?php print $age ?> </font> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<?php print LANGBULL3 ?> : <b><?php print $anneeScolaire?></b>
	<?php
}

?>
<br><br>
<table border='0' ><tr>
<td valign="top"><font class=T2>Moyenne de l'<?php print INTITULEELEVE ?> : </font></td>
<td><font class=T2><b><div id="e1"></div></b> (Premier Trimestre) /  </font></td>
<td><font class=T2><b><div id="e2"></div></b> (Deuxième Trimestre) /  </font></td>
<td><font class=T2><b><div id="e3"></div></b> (Troisième Trimestre)  </font></td>
<tr><td colspan='4'><hr></td></tr>
<td valign="top"><font class=T2>Moyenne de la classe : </font></td>
<td><font class=T2><b><div id="m1"></div></b> (Premier Trimestre) /  </font></td>
<td><font class=T2><b><div id="m2"></div></b> (Deuxième Trimestre) /  </font></td>
<td><font class=T2><b><div id="m3"></div></b> (Troisième Trimestre)  </font></td>
</tr></table>

<div id='afficheToken' style="position:relative;top:-50px;left:200px" ></div>

</font></td></tr>
<tr><td valign=top  ><br>
<form method=post name="form" >
<table border=1 >
<?php // ---------------------------------------------------------- 

include_once("common/productId.php");
include_once("common/config-ia.php");
$productID=PRODUCTID;
$iakey=IAKEY;

print "<script>";
print "verifToken('$productID','$iakey','afficheToken');";
print "</script>";

include_once('librairie_php/recupnoteperiode.php');

$ordre=ordre_matiere_visubull_trim($idclasse,$trimes,$anneeScolaire);
$idEleve=$ideleve;
$idClasse=$idclasse;

for($i=0;$i<count($ordre);$i++) {
	$matiere=chercheMatiereNom($ordre[$i][0]);
	$nomprof=recherche_personne($ordre[$i][1]);
	$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);
	$idMatiere=$ordre[$i][0];
	// mise en place du nom du prof
        $idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2],$anneeScolaire);
        $profAff=recherche_personne($ordre[$i][1]);

	if ($verifGroupe) { continue; } // verif pour l'eleve de l'affichage de la matiere

	// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
	$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2],$anneeScolaire);
        // mise en place des coeff
	$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$i][2],$anneeScolaire);

	// mise en place des matieres
	print "<tr>";
	print "<td bordercolor='#cccccc' valign=top ><font size='2'><input type=text readonly='readonly' value='".trunchaine(strtoupper($matiere),70)." (".$coeffaff.")' size='70' title=\"$matiere\"></font>";
	print "<br><i><font size=1> ".trunchaine(trim($profAff),50)." </font></i></td>";
	// mise en place moyenne eleve
	// mise en place des notes
	$idprof=profAff($idMatiere,$idClasse,$ordre[$i][2],$anneeScolaire);

	if (ISMAPP == 1) {
		$listeExamen=array("CC","DST","Dad","Soutenance","Rapport","Fiche de lecture","Exposé","Partiel","Lecture","Examen écrit","Recopiage vocabulaire","Mémoire Ip","Evaluation Tutorat");
		$epreuve="";
		$moyenneTT="";
		$coef="";
		foreach($listeExamen as $key=>$value) {
			if ($idgroupe == "0") {
				$noteaff=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,$value);
			}else{
				$noteaff=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,$value);
			}
			if (trim($noteaff) != "") {
				 if ($value == "CC") 	  { $valcoef="1"; }
				 if ($value == "DST") 	  { $valcoef="2"; }
				 if ($value == "Partiel") { $valcoef="3"; }
				 if ($value == "Soutenance") { $valcoef="3"; }
				 if ($value == "Rapport") { $valcoef="3"; }
				 if ($value == "Fiche de lecture") { $valcoef="2"; }
				 if ($value == "Exposé")  { $valcoef="1"; }
				 if ($value == "Dad")     { $valcoef="1"; }
			         if ($value == "Lecture") { $valcoef="3"; }
        			 if ($value == "Examen écrit")   { $valcoef="2"; }
			         if ($value == "Recopiage vocabulaire") { $valcoef="1"; }
				 if ($value == "Mémoire Ip")            { $valcoef="2"; }
                                 if ($value == "Evaluation Tutorat")    { $valcoef="2"; }


				$moyenneTT+=$noteaff*$valcoef;
				$coef+=$valcoef;
			}
		}
		$noteaff=$moyenneTT/$coef;
		$noteaff=number_format($noteaff,2,'.','');
	}else{
		if ($typecom == 4) {
			if ($idgroupe == "0") {
				$noteaff=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,"Partiel Blanc");
				$notetype=recherchetypenote($ordre[$i][0],$dateDebut,$dateFin,$idClasse);
			}else{
				$noteaff=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,"Partiel Blanc");
				$notetype=recherchetypenotegroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe);
			}
		}else{
			if ($idgroupe == "0") {
				$noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);			
				$notetype=recherchetypenote($ordre[$i][0],$dateDebut,$dateFin,$idClasse);
			}else{
				$noteaff=moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
				$notetype=recherchetypenotegroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe);
			}
		}
	}
	$noteaff1=$noteaff;
	if ($notetype=="en") { 
		$afficheMoyen="non";
		if (trim($notetype) == "en") { $noteaff1=number_format($noteaff1,0,'','')."% - ".recherche_note_en($noteaff); }
		$couleur="black";
	}else{
		if ($noteaff1 < 10) { $couleur="red"; }else{ $couleur="black"; }
	}
	$commentaireeleve="&nbsp;&nbsp;&nbsp;";
	$commentaireeleve=cherche_com_eleve2($idEleve,$idMatiere,$idClasse,$tri,$idprof,$idgroupe,$typecom,$anneeScolaire);
	$commentaireeleve=preg_replace('/"/',"&rdquo;",$commentaireeleve);
	$commentaireeleve=stripslashes($commentaireeleve);


    	print "<td bordercolor='orange' align=center bgcolor='#FFFFFF'>&nbsp;";
	print "<font size=3 color=$couleur>&nbsp;<b>$noteaff1</b>&nbsp;</font></td>";

	/*
	// mise en place des moyennes matieres de classe
	$notetype="";
	if (($idgroupe == "0") || ($idgroupe == "")) {
		// idMatiere,datedebut,dateFin,idclasse
	        $moyeMatGen=moyeMatGen($ordre[$i][0],$dateDebut,$dateFin,$idClasse,$idprof);
		$notetype=recherchetypenote($idMatiere,$dateDebut,$dateFin,$idClasse);
	}else {
		$moyeMatGen=moyeMatGenGroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
		$notetype=recherchetypenotegroupe($idMatiere,$dateDebut,$dateFin,$idgroupe);
	}
	$moyeMatGenaff=$moyeMatGen;
	if (trim($notetype) == "en") { $moyeMatGenaff=number_format($moyeMatGen,0,'','')."% - ".recherche_note_en($moyeMatGen); }
	
	print "<td bordercolor='blue' align=center bgcolor='#FFFFFF'> <font size=3 >&nbsp;$moyeMatGenaff</td>";
	 */

	print "<td align=left bgcolor='#FFFFFF'>";
	print "<input type=hidden name='saisie_eleve_$i' value='$idEleve' >";
	print "<input type=hidden name='saisie_matiere_$i' value='$idMatiere' >";
	print "<input type=hidden name='saisie_groupe_$i' value='$idgroupe' >";
	print "<input type=hidden name='saisie_prof_$i' value='$idprof' >";
	print "<input type=hidden name='direct_eleve' value='$idEleve' >";
	

	if (file_exists("./common/config-ia.php")) {
		include_once("common/productId.php");
	        include_once("common/config-ia.php");
	        $productID=PRODUCTID;
	        $iakey=IAKEY;
	        $prenom=recherche_eleve_prenom($idEleve);
	        $lienIA="ajaxIABulletinCom('".addslashes($commentaireeleve)."','$noteaff1','$productID','$iakey','saisie_text_$i','$prenom',document.getElementById('tonia_$i').value)";
	}else{
	        $lienIA="alert('Votre Triade n\'est pas configur&eacute; pour utiliser l\'IA. Contacter votre administrateur Triade')";
	}

	if (defined("NBCARBULL")) { $nbcar=NBCARBULL; }else{ $nbcar=400; }
	if ($typecom > 0) { $nbcar=150; }
	print "<input type='text' name='CharRestant_$i' size='2' disabled='disabled'> ($nbcar caract&eacute;res maximum)";
	print "&nbsp;&nbsp;<input type='button' value='TRIADE-COPILOT' class='BUTTON' onClick=\"$lienIA\" >";
	print "<select name='tonia' id='tonia_$i' >";
        print "<option value='IA' STYLE='color:#000066;background-color:#FCE4BA' >Comportement IA : Par défaut</option>";
        print "<option value='Neutre' STYLE='color:#000066;background-color:#CCCCFF' >Neutre</option>";
        print "<option value='Positif' STYLE='color:#000066;background-color:#CCCCFF' >Positif</option>";
        print "<option value='Encourageant' STYLE='color:#000066;background-color:#CCCCFF' >Encourageant</option>";
	print "<option value='Inquiétant' STYLE='color:#000066;background-color:#CCCCFF'  >Inquiétant</option>";
	print "<option value='Motivant' STYLE='color:#000066;background-color:#CCCCFF' >Motivant</option>";
        print "</select>";
	print "<br>";
	print "<textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestant_$i)\" cols='78' rows='3' id='saisie_text_$i' name='saisie_text_$i' >$commentaireeleve</textarea></td>";

	print "</tr>";
	
}
//
?>
</table>
<br><br>
<input type=hidden name='saisie_nb' value='<?php print count($ordre) ?>' >
<input type=hidden name="saisie_classe" value="<?php print $idclasse?>">
<input type=hidden name="saisie_trimestre" value="<?php print $trimes?>">
<input type=hidden name="anneeScolaire" value="<?php print $anneeScolaire?>">
<input type=hidden name="typecom" value="<?php print $typecom?>" >
<table><tr><td>&nbsp;&nbsp;<input type=submit value="Enregistrer les modifications" class="bouton2" name="valide" onclick="this.value='Veuillez patientez'">
</td><td><script  language="JavaScript" >buttonMagicFermeture()</script></td></tr></table>
</form>
</tr></td>

</td></tr></table>
<img src="image/commun/indicator.gif" style="visibility:hidden" />
<?php Pgclose(); ?>
<script>RecupMoyenneEleve('<?php print "trimestre1" ?>','<?php print $idEleve?>','e1','<?php print $idclasse ?>','<?php print $anneeScolaire ?>')</script>
<script>RecupMoyenneEleve('<?php print "trimestre2"?>','<?php print $idEleve?>','e2','<?php print $idclasse ?>','<?php print $anneeScolaire ?>')</script>
<script>RecupMoyenneEleve('<?php print "trimestre3"?>','<?php print $idEleve?>','e3','<?php print $idclasse ?>','<?php print $anneeScolaire ?>')</script>

<script>RecupMoyenne('<?php print "trimestre1" ?>','<?php print $idclasse?>','m1','<?php print $anneeScolaire ?>')</script>
<script>RecupMoyenne('<?php print "trimestre2"?>','<?php print $idclasse?>','m2','<?php print $anneeScolaire ?>')</script>
<script>RecupMoyenne('<?php print "trimestre3"?>','<?php print $idclasse?>','m3','<?php print $anneeScolaire ?>')</script>
<?php 
if ($okenr == 1) {
	alertJs(LANGDONENR);
}
?>
</body>
</html>
