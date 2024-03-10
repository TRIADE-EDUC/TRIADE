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
 //
 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<script type="text/javascript" src="./librairie_js/function.js"></script>
<script type="text/javascript" src="./librairie_js/clickdroit2.js"></script>
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include_once("./librairie_php/lib_licence.php"); 
include_once('librairie_php/db_triade.php');
$cnx=cnx();



$datap=config_param_visu("affNotePartielVatel");
$afficheNotePartielVatel=$datap[0][0];

$data=chercheClasse($_GET["saisie_classe"]);
$ideleverecup=$_GET["saisie_eleve"];
$classe_nom=$data[0][1];


?>
<table border=0  width=100% align=center style="width: 97%; padding: 5px; border: 1px solid rgba(0,0,0,0.5); border-radius: 10px; background: rgba(0,0,0,0.25); box-shadow: 0 2px 6px rgba(0,0,0,0.5), inset 0 1px rgba(255,255,255,0.3), inset 0 10px rgba(255,255,255,0.1), inset 0 10px 20px rgba(255,255,255,0.3), inset 0 -15px 30px rgba(0,0,0,0.3); -o-box-shadow: 0 2px 6px rgba(0,0,0,0.5), inset 0 1px rgba(255,255,255,0.3), inset 0 10px rgba(255,255,255,0.2), inset 0 10px 20px rgba(255,255,255,0.25), inset 0 -15px 30px rgba(0,0,0,0.3); -webkit-box-shadow: 0 2px 6px rgba(0,0,0,0.5), inset 0 1px rgba(255,255,255,0.3), inset 0 10px rgba(255,255,255,0.2), inset 0 10px 20px rgba(255,255,255,0.25), inset 0 -15px 30px rgba(0,0,0,0.3); -moz-box-shadow: 0 2px 6px rgba(0,0,0,0.5), inset 0 1px rgba(255,255,255,0.3), inset 0 10px rgba(255,255,255,0.2), inset 0 10px 20px rgba(255,255,255,0.25), inset 0 -15px 30px rgba(0,0,0,0.3);" >
<tr><td colspan=5 ><font color=blue>* Les commentaires des enseignants sont disponibles sur la moyenne de l'élève.</font><br><br></td></tr>
<tr>
<td width=5% valign=top align=left ><b><font size=2>&nbsp;<?php print LANGPER17?></font></b></td>
<?php
if ($afficheNotePartielVatel == "oui") {
	print "<td width=7% valign=top align=center id='bordure' ><b><font size=2>Moy Partiel</font></b></td>";
	print "<td width=7% valign=top align=center id='bordure' ><b><font size=2>Moy P&eacute;riode</font></b></td>";
}else{
	print "<td width=7% valign=top align=center  id='bordure' ><b><font size=2>".LANGPROJ14. " <font color=blue>*</font></font></b></td>";
}
?>
<td width=7% valign=top align=center   id='bordure' ><b><font size=2><?php print LANGPROJ15?></font></b></td>
<td width=7% valign=top align=center   id='bordure' ><b><font size=2><?php print "ECTS"?></font></b></td>
<td valign=top align=center  id='bordure' ><b><font size=2>
[<a href="video-proj-bulletin-UE.php?saisie_eleve=<?php print $ideleverecup ?> &saisie_classe=<?php print $_GET["saisie_classe"] ?>&saisie_trimestre=<?php print $_GET["saisie_trimestre"] ?>"><?php print "Commentaires"?></a>] / 
[<a href="video-proj-bulletin-UE.php?saisie_eleve=<?php print $ideleverecup ?> &saisie_classe=<?php print $_GET["saisie_classe"] ?>&saisie_trimestre=<?php print $_GET["saisie_trimestre"] ?>&detailNotes">Notes</a>]</font></td></tr>

<?php
// recupe du nom de la classe


include_once('librairie_php/recupnoteperiode.php');

if ($_GET["saisie_trimestre"] == "trimestre1" ) {  $sem=1; }
if ($_GET["saisie_trimestre"] == "trimestre2" ) {  $sem=2; }
if ($_GET["saisie_trimestre"] == "trimestre3" ) {  $sem=3; }


// recuperation des coordonnées
// de l'etablissement
$data=visu_paramViaIdSite(chercheIdSite($_GET["saisie_classe"]));
for($i=0;$i<count($data);$i++) {
       $nom_etablissement=trim($data[$i][0]);
       $adresse=trim($data[$i][1]);
       $postal=trim($data[$i][2]);
       $ville=trim($data[$i][3]);
       $tel=trim($data[$i][4]);
       $mail=trim($data[$i][5]);
}
// fin de la recup


// recherche des dates de debut et fin
$dateRecup=recupDateTrimByIdclasse($_GET["saisie_trimestre"],$_GET["saisie_classe"]);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebut=dateForm($dateDebut);
$dateFin=dateForm($dateFin);

$idClasse=$_GET["saisie_classe"];


$noteMoyEleG=0; // pour la moyenne de l'eleve general
$coefEleG=0; // pour la moyenne de l'eleve general
$eleveT=recupEleve($_GET["saisie_classe"]); // recup liste eleve


$moyenClasseGen=""; // pour le calcul moyenne classe
$nbeleve=0;
$noteMoyEleG1=0; // pour la moyenne  general
$coefEleG1=0; // pour la moyenne  general
$nbmatiere=0;  // pour la moyenne general

// ----------------------------


$noteMoyEleG=0;
$coefEleG=0;
$afficheMoyen="oui";

for($j=0;$j<count($eleveT);$j++) {  // premiere ligne
	// variable eleve
	$lv1Eleve=$eleveT[$j][2];
	$lv2Eleve=$eleveT[$j][3];
	$idEleve=$eleveT[$j][4];

	if ($idEleve != $ideleverecup) { continue; }


	$recupUE=recupUE($idClasse,$sem); //code_ue,nom_ue,coef_ue,ects_ue
	
	$ectsTOTALP1=0;
	$ectsTOTALP2=0;

	for($f=0;$f<count($recupUE);$f++) {
		$code_ue=$recupUE[$f][0];
		$nom_ue=$recupUE[$f][1];
		$coef_ue=$recupUE[$f][2];
		$ects_ue=$recupUE[$f][3];

		$listeMatiere=recupMatiereUE($code_ue,$idClasse);  // u.code_matiere,m.libelle,u.code_enseignant
	
		print "<tr>";
		print "<td  valign='top' colspan='5'><font class='T2' color='#FFFFFF' >$nom_ue</font> (coef UE: $coef_ue) </td>";
		print "</tr>";

		// u.code_matiere,m.libelle
		for($i=0;$i<count($listeMatiere);$i++) {
			$X=$Xorigine;
			
			$idmatiere=$listeMatiere[$i][0];
			$idMatiere=$listeMatiere[$i][0];
			$matiere=$listeMatiere[$i][1];
			$idprof=$listeMatiere[$i][2];
			$ordreaffichage=$listeMatiere[$i][3];

			$coeffaffTotal=0;
			$matiere=chercheMatiereNom($idmatiere);
			$nomprof=recherche_personne($idprof);

			$verifGroupe=verifMatiereAvecGroupeUE($idmatiere,$idEleve,$idClasse,$ordreaffichage);
			if ($verifGroupe) {  continue; } // verif pour l'eleve de l'affichage de la matiere

			// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
			$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordreaffichage);


			$datasousmatiere=verifsousmatierebull($idMatiere);
			//	print $datasousmatiere;
			if ($datasousmatiere != "0") {
				$nomMatierePrincipale=$datasousmatiere[0][2];
				$nomSousMatiere=$datasousmatiere[0][1];
				$matiere="$nomMatierePrincipale $nomSousMatiere";
			}

			// mise en place du nom du prof
		        $profAff=recherche_personne($idprof);

		        // mise en place des coeff
			$coeffaff=recupCoeff($idMatiere,$idClasse,$ordreaffichage);
	
			$ects=recupECTS($idmatiere,$idClasse,$_GET["saisie_trimestre"]);
			
			$coef=recupCoefUE($idmatiere,$idClasse,$_GET["saisie_trimestre"]);
			$coeffaff=$coef;

			$tri=$_GET["saisie_trimestre"];

			// mise en place des matieres
			print "<tr>";
			print "<td valign='top' ><font size=2><input type=text readonly value=\"".trunchaine(strtoupper($matiere),25)." (".$coeffaff.")\" size=50 title=\"$matiere\" ></font>";
			print "<br><i><font size=1> ".trunchaine(trim($profAff),40)." </font></i></td>";


			if ($afficheNotePartielVatel == "oui") {
				include_once('librairie_php/fonctions_vatel.php');
				$notepartiel = recupNotepartiel($idEleve,$idMatiere,$dateDebut,$dateFin,$idClasse);
				$moy_partiel_eu='';
				$som_coef_partiel_eu=0;
				for ($nb_note=0;$nb_note<count($notepartiel);$nb_note++) { 
					if ($notepartiel[$nb_note][0]>=0 && is_numeric($notepartiel[$nb_note][0])) {$nb_mat+=1;
						$moy_partiel_eu+=$notepartiel[$nb_note][6]*$notepartiel[$nb_note][0];
						$som_coef_partiel_eu+=$notepartiel[$nb_note][6];
						$moyenne_ue+=$notepartiel[$nb_note][6]*$notepartiel[$nb_note][0];
						$somme_coef+=$notepartiel[$nb_note][6];
						$affich_coef_partiel =$notepartiel[$nb_note][6];
					}else{
						$affich_coef_partiel="";
					}
				}
				$moy_coef_partiel="";
				if (count($notepartiel)>0) {
					$moy_coef_partiel=$som_coef_partiel_eu/count($notepartiel);
					$moy_coef_partiel="($moy_coef_partiel)";
				}
				$affich_coef_partiel='';
				if ($moy_partiel_eu >=0 && is_numeric($moy_partiel_eu) ) {
					$affiche_note = $moy_partiel_eu/$som_coef_partiel_eu ;
				} else {
					$affiche_note = "";
				}			
				print "<td bordercolor='yellow' align=center bgcolor='#FFFFFF' title='moy (coef)' ><font size=3>&nbsp; $affiche_note $moy_coef_pariel</font></td>";
			}



			// mise en place moyenne eleve
			// mise en place des notes

			if ($afficheNotePartielVatel == "oui") {
				$noteaff="";
				$noteperiode=recupNoteperiode($idEleve,$idmatiere,$dateDebut,$dateFin);
				$moyenne_periode='';
				$nb_note_periode=0;
				$som_coef_periode=0;
				for ($nb_note=0;$nb_note<count($noteperiode);$nb_note++) { 
					if ($noteperiode[$nb_note][0]>=0 && is_numeric($noteperiode[$nb_note][0]) && count($noteperiode)>0) {
						$moyenne_periode+=$noteperiode[$nb_note][0]*$noteperiode[$nb_note][6];
						$nb_note_periode+=1;
						$som_coef_periode+=$noteperiode[$nb_note][6];
					}
				}
				$moyenne_periode=$moyenne_periode/($som_coef_periode);
				$som_coef_periode_ue+= $som_coef_periode;
				$som_coef_periode=0;
				$moyenne_periode_ue+=$moyenne_periode;
				if ($nb_note_periode>0) { $nb_note_periode_ue++; $nb_mat_per++; }
				if ($moyenne_periode >=0 && is_numeric($moyenne_periode) && $nb_note_periode>0) { 
					$noteaff=number_format($moyenne_periode,2,'.','');
				}
			}else{
				if (($idgroupe == "0") || (trim($idgroupe) == "")) {
					$noteaff=moyenneEleveMatiere($idEleve,$idmatiere,$dateDebut,$dateFin,$idprof);
				}else{
					$noteaff=moyenneEleveMatiereGroupe($idEleve,$idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
				}
			}
			$noteaff1=$noteaff;
			if ($notetype=="en") { 
				$afficheMoyen="non";
				if (trim($notetype) == "en") { 
					if ($noteaff1 != "") {
						$noteaff1=number_format($noteaff1,0,'',''); 
						$noteaff1=$noteaff1."% - ".recherche_note_en($noteaff);
					}else{
						$noteaff1="---";
					}
				}
				$couleur="black";
			}else{
				if ($noteaff1 < 10) { $couleur="red"; }else{ $couleur="black"; }
			}

			if ($noteaff >= 10) {
				$ectsTotal+=$ects;
			}

			$commentaireeleve="&nbsp;&nbsp;&nbsp;";
			$commentaireeleve=cherche_com_eleve($idEleve,$idMatiere,$idClasse,$tri,$idprof,$idgroupe);
			$commentaireeleve=preg_replace("/'/","\\'",$commentaireeleve);
			$commentaireeleve=preg_replace('/"/',"&rdquo;",$commentaireeleve);
		        $commentaireeleve=preg_replace('/\r\n/',"<br />",$commentaireeleve);
			$commentaireeleve=preg_replace('/\n/',"<br />",$commentaireeleve);
			$b="<b>";
			$bb="</b>";
			if (trim($noteaff1) == "") {
				$noteaff1='---';
				$couleur="black";
				$b="<i>";
				$bb="</i>";
			}

		    	print "<td bordercolor='orange' align=center bgcolor='#FFFFFF'>&nbsp;";
		    	print "<font size=3 color=$couleur>$b $noteaff1 $bb</font></td>";

			// mise en place des moyennes matieres de classe
			$notetype="";
			unset($moyeMatGen);


			if (($idgroupe == "0") || (trim($idgroupe) == "")) { 
				// idMatiere,datedebut,dateFin,idclasse
	       	  		$moyeMatGen=moyeMatGen($idmatiere,$dateDebut,$dateFin,$idClasse,$idprof);
			}else {
        			$moyeMatGen=moyeMatGenGroupe($idmatiere,$dateDebut,$dateFin,$idgroupe,$idprof);
			}
			$moyeMatGenaff=$moyeMatGen;
			if (trim($notetype) == "en") { $moyeMatGenaff=number_format($moyeMatGen,0,'','')."% - ".recherche_note_en($moyeMatGen); }



			$color='#FFFFFF';
			// ./commentaire.php?idm=$idMatiere&ide=$idEleve&idc=$idClasse&tri=$tri&idprof=$idprof&idgroupe=$idgroupe
			print "<td bordercolor='blue' align=center bgcolor='#FFFFFF'> <font size=3 >&nbsp;$moyeMatGenaff</td>";
			
			// Mise en place ECTS
			print "<td bordercolor='blue' align=center bgcolor='#FFFFFF'> <font size=3 >&nbsp;$ects</td>";

			if (isset($_GET["detailNotes"])) {
				$listingNotes=recupNoteBull(20,$idEleve,$idmatiere,$dateDebut,$dateFin,$idprof,$idgroupe);
				print "<td bgcolor='$color' ><font class='T2'>";
				print preg_replace('/ /',', ',$listingNotes);
				print "</font></td>";

			}else{

				if ($_SESSION["membre"] == "menuadmin") {
					print "<td bgcolor='$color' ><div id='com$i' ><a href='#' onclick=\"modifCom('com$i','$idMatiere','$idEleve','$idClasse','$tri','$idprof','$idgroupe')\" ><font class='T2'>".stripslashes($commentaireeleve)."</font></a></div></td>";
				}else{
					print "<td bgcolor='$color' ><font class='T2'>".stripslashes($commentaireeleve)."</font></td>";
				}
			}
			print "</tr>";
			// pour le calcul de la moyenne general de l'eleve
			if (( trim($noteaff) != "" ) && ( $noteaff >= 0 )) {
				$noteMoyEleGTempo=0;
				$noteMoyEleG += $coeffaff * $noteaff;
				$coefEleG += $coeffaff ;

				$moyenEU += $noteaff * $coeffaff;
				$coeffEU += $coeffaff ;
			}
			if (($moyeMatGen >= 0) && ( trim($noteaff) != "" )) { 
				$moyenMatEU +=$moyeMatGen * $coeffaff;
				$coeffMatEU += $coeffaff ;
			}

			if ($moyeMatGenaff != "") {
				$coefGNCLASS+=$coef;
				$moyGNCLASS+=$moyeMatGenaff*$coef;
			//	$minUECLASS+=$moyeMatGenMinaff*$coef;
			//	$maxUECLASS+=$moyeMatGenMaxaff*$coef;
			}

		}
		$moyenEU=$moyenEU/$coeffEU;
		$moyenMatEU=$moyenMatEU/$coeffMatEU;
		print "<tr>";
		print "<td bgcolor='yellow' align='right' ><font class='T2'>Moyenne : </font></td>";
		if ($afficheNotePartielVatel == "oui") { print "<td></td>"; }
		print "<td bgcolor='yellow' align='center' ><font size=3 >".number_format($moyenEU,2,',','')."</font></td>";
		print "<td bgcolor='yellow' align='center' ><font size=3 >".number_format($moyenMatEU,2,',','')."</font></td>";
		print "<td colspan='2'></td>";
		print "</tr>";
		unset($moyenEU);
		unset($coeffEU);
		unset($moyenMatEU);
		unset($coeffMatEU);
	}

// fin de la mise en place des matiere
if ((MODNAMUR0 == "oui")  && ($_SESSION["validenoteviescolaire"] == "oui")) {
	$recupInfo=recupCaractVieScolaire($_GET["saisie_classe"]);
	$persVieScolaire=$recupInfo[0][4];
	$coefBull=$recupInfo[0][1];
	$coefProf=$recupInfo[0][2];
	$coefVieScol=$recupInfo[0][3];
	$noteaff=calculNoteVieScolaire($idEleve,$coefProf,$coefVieScol,$tri);
	$moyeMatGen1=moyeMatGenVieScolaire($tri,$idClasse);
	$noteaff1=$noteaff;
	if ($notetype=="en") { 
		$afficheMoyen="non";
		if (trim($notetype) == "en") { 
			if ($noteaff1 != "") {
				$noteaff1=number_format($noteaff1,0,'',''); 
				$noteaff1=$noteaff1."% - ".recherche_note_en($noteaff);
			}else{
				$noteaff1="---";
			}
		}
		$couleur="black";
	}else{
		if ($noteaff1 < 10) { $couleur="red"; }else{ $couleur="black"; }
	}
	print "<tr>";
	print "<td bordercolor='khaki'><font size=2><input type=text readonly value=\"".trunchaine(strtoupper("Vie Scolaire"),15)." (".$coefBull.")\" size=30></font>";
	print "<br><i><font size=1> ".trunchaine(trim($persVieScolaire),20)." </font></i></td>";
	print "<td bordercolor='blue' align=center bgcolor='#FFFFFF'> <font size=3 >&nbsp;</td>";
	print "<td bordercolor='orange' align=center bgcolor='#FFFFFF'>&nbsp;";
	$com=cherche_com_scolaire_eleve_cpe($idEleve,"-10",$idClasse,$tri,"");
	print "<a href='#' onMouseOver=\"AffBulle('$com');\"  onMouseOut=\"HideBulle()\"; onclick=\"open('./commentaire.php?idm=$idMatiere&ide=$idEleve&idc=$idClasse&tri=$tri&idprof=$idprof&idgroupe=$idgroupe','','width=400,height=200')\" ><font size=3 color=$couleur><b>$noteaff1</b></font></a></td>";
	print "<td bordercolor='blue' align=center bgcolor='#FFFFFF'> <font size=3 >&nbsp;$moyeMatGen1</td>";
	print "</tr>";
	// pour le calcul de la moyenne general de l'eleve
	if (( trim($noteaff) != "" ) && ( $noteaff >= 0 )) {
		$noteMoyEleGTempo=0;
		$noteMoyEleG += $coefBull * $noteaff;
		$coefEleG += $coefBull ;

		$coefGNCLASS+=$coef;
		$moyGNCLASS+=$moyeMatGenaff*$coef;
	}
}

// fin notes
// --------

// affichage de la moyenne generale eleve
if ($afficheMoyen == "oui") {
	if ($moyGNCLASS != "") {
		$moyGNCLASS=$moyGNCLASS/$coefGNCLASS;
		if (($moyGNCLASS < 10) && ($moyGNCLASS != "")) { $moyGNCLASS="0".$moyGNCLASS; }
		$moyGNCLASS = number_format($moyGNCLASS, 2, '.', '');
	}else{
		$moyGNCLASS="";
	}

	$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
	if (($moyenEleve == " ") || ($moyenEleve < 0)) { $moyenEleve="&nbsp;"; }
	$noteMoyEleG=0; // pour la moyenne de l'eleve general
	$coefEleG=0; // pour la moyenne de l'eleve general
	$color="black";
	if ($moyenEleve < 10) { $color="red"; }
 
	print "<tr><td  bgcolor='red' align='right' ><font class='T2'  color='#FFFFFF' >&nbsp;".LANGPROJ16." : </font></td>";
	if ($afficheNotePartielVatel == "oui") {
		print "<td></td>";
	}
	print "<td bgcolor='#FFFFFF' bordercolor='orange' align=center><b><font size=3 color=$color >";
	print "$moyenEleve";
	print "</font></b></td>";
	print "<td  bgcolor='#FFFFFF' bordercolor='blue' align=center><font size=3>$moyGNCLASS</font></td>";
	print "<td  bgcolor='#FFFFFF' bordercolor='blue' align=center><font size=3>$ectsTotal</font></td>";
	print "</tr>";
}
// fin affichage moy eleve

	unset($ectsTotal);
// fin affichage



} // fin du for on passe à l'eleve suivant

?>


</table>
<?php
Pgclose();
?>
<SCRIPT type="text/javascript">InitBulle("#000000","#CCCCCC","#000000",1);</SCRIPT>
<script>
// ./commentaire.php?idm=$idMatiere&ide=$idEleve&idc=$idClasse&tri=$tri&idprof=$idprof&idgroupe=$idgroupe
function modifCom(retourAffiche,idMatiere,idEleve,idClasse,tri,idprof,idgroupe) {
	var divid=retourAffiche;
	var myAjax = new Ajax.Request(
		"ajaxCommentaireVideo.php",
		{	method: "post",
			asynchronous: true,
			parameters: "idm="+idMatiere+"&ide="+idEleve+"&idc="+idClasse+"&tri="+tri+"&idprof="+idprof+"&idgroupe="+idgroupe+"&retourAffiche="+retourAffiche,
			timeout: 5000,
			onComplete: function (request) {
				$(divid).innerHTML=request.responseText;
			}
		}
	);
}

function saveCommentaire(commentaire,idEleve,idMatiere,idClasse,tri,idprof,idgroupe,retourAffiche) {
	var divid=retourAffiche;
	var typecom="0";
	var myAjax = new Ajax.Request(
		"ajaxCommentaireVideoEnr.php",
		{	method: "post",
			asynchronous: true,
			parameters: "idm="+idMatiere+"&ide="+idEleve+"&idc="+idClasse+"&tri="+tri+"&idprof="+idprof+"&idgroupe="+idgroupe+"&com="+commentaire+"&typecom="+typecom+"&retourAffiche="+retourAffiche,
			timeout: 5000,
			onComplete: function (request) {
				$(divid).innerHTML=request.responseText;
			}
		}
	);
}
</script>
</body>
</html>
