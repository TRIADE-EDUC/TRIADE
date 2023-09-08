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

<script>
function downloadBull(fichier) {
	if (fichier != "") {
		open("visu_document.php?fichier="+fichier,'_blank','');
	}
}
</script>

</head>
<body  style="" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include_once("./librairie_php/lib_licence.php"); 
include_once('librairie_php/db_triade.php');
$cnx=cnx();
// recherche des dates de debut et fin
$dateRecup=recupDateTrimByIdclasse($_GET["saisie_trimestre"],$_GET["saisie_classe"]);
for($j=0;$j<count($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebut=dateForm($dateDebut);
$dateFin=dateForm($dateFin);
$periode="$dateDebut au $dateFin";
?>
<table border=1  width=100% align=center style=" width: 97%; padding: 5px; border: 1px solid rgba(0,0,0,0.5); border-radius: 10px; background: rgba(0,0,0,0.25); box-shadow: 0 2px 6px rgba(0,0,0,0.5), inset 0 1px rgba(255,255,255,0.3), inset 0 10px rgba(255,255,255,0.1), inset 0 10px 20px rgba(255,255,255,0.3), inset 0 -15px 30px rgba(0,0,0,0.3); -o-box-shadow: 0 2px 6px rgba(0,0,0,0.5), inset 0 1px rgba(255,255,255,0.3), inset 0 10px rgba(255,255,255,0.2), inset 0 10px 20px rgba(255,255,255,0.25), inset 0 -15px 30px rgba(0,0,0,0.3); -webkit-box-shadow: 0 2px 6px rgba(0,0,0,0.5), inset 0 1px rgba(255,255,255,0.3), inset 0 10px rgba(255,255,255,0.2), inset 0 10px 20px rgba(255,255,255,0.25), inset 0 -15px 30px rgba(0,0,0,0.3); -moz-box-shadow: 0 2px 6px rgba(0,0,0,0.5), inset 0 1px rgba(255,255,255,0.3), inset 0 10px rgba(255,255,255,0.2), inset 0 10px 20px rgba(255,255,255,0.25), inset 0 -15px 30px rgba(0,0,0,0.3);" >
<tr><td colspan=4 id='bordure' height=20 ><font color=blue>* Les commentaires des enseignants sont disponibles sur la moyenne de l'élève. 
<font color=yellow><b>P&eacute;riode : <?php print $periode ?></b></font>
</font></td></tr>
<tr>
<td width=5% valign=top align=left id='bordure' ><b><font size=2>&nbsp;<?php print LANGPER17?></font></b></td>
<td width=7% valign=top align=center id='bordure' ><b><font size=2><?php print LANGPROJ14. " <font color=blue>*</font>" ?></font></b></td>
<td width=7% valign=top align=center id='bordure' ><b><font size=2><?php print LANGPROJ15?></font></b></td>
<td valign=top align=center id='bordure' >


<table border=0 width='100%' ><tr><td align='right'><b><font size=2><?php print "Commentaire" ?></font></b></td><td align='right'>
<?php
$databull=recupArchiveBulletin($_GET["saisie_eleve"]); //  ideleve,anneescolaire,trimestre,date,classe,file
?>
<font class=T2 color='blue' >Archive bulletin : </font><select onChange="downloadBull(this.value)" >
<option value='' id="select0" ><?php print LANGCHOIX ?></option>
<?php 
for ($i=0;$i<count($databull);$i++) {
	print "<option id='select1' value=\"".$databull[$i][5]."\" >".$databull[$i][1]."   (".$databull[$i][4].")   ".$databull[$i][2]."</option>";

}
?>
</select>
</td></tr></table>

</td></tr>

<?php
// recupe du nom de la classe
$data=chercheClasse($_GET["saisie_classe"]);
$ideleverecup=$_GET["saisie_eleve"];
$classe_nom=$data[0][1];

include_once('librairie_php/recupnoteperiode.php');

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



$idClasse=$_GET["saisie_classe"];
$ordre=ordre_matiere_visubull($_GET["saisie_classe"]); // recup ordre matiere


$noteMoyEleG=0; // pour la moyenne de l'eleve general
$coefEleG=0; // pour la moyenne de l'eleve general
$eleveT=recupEleve($_GET["saisie_classe"]); // recup liste eleve


$moyenClasseGen=""; // pour le calcul moyenne classe
$nbeleve=0;
$noteMoyEleG1=0; // pour la moyenne  general
$coefEleG1=0; // pour la moyenne  general
$nbmatiere=0;  // pour la moyenne general

// pour le calcul de moyenne classe
// idclasse,tableaueleve,datedebut,datefin,ordrematriere
$moyenClasseGen=calculMoyenClasse($idClasse,$eleveT,$dateDebut,$dateFin,$ordre);
if ($moyenClasseGen < 0) { $moyenClasseGen="&nbsp;"; }
// Fin du Calcul moyenne classe
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

$coeffaffTotal=0;
for($i=0;$i<count($ordre);$i++) {
	$matiere=chercheMatiereNom($ordre[$i][0]);
	$nomprof=recherche_personne($ordre[$i][1]);
	$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);
	$idMatiere=$ordre[$i][0];

	// mise en place du nom du prof
        $idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
        $profAff=recherche_personne($ordre[$i][1]);

	if ($verifGroupe) { continue; } // verif pour l'eleve de l'affichage de la matiere

	// recupe de l'id du groupe //idMatiere,$idEleve,$idClasse
	$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2]);
        // mise en place des coeff
	$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$i][2]);

	$tri=$_GET["saisie_trimestre"];

	// mise en place des matieres
	print "<tr>";
	print "<td bordercolor='#cccccc' valign='top' ><font size=2><input type=text readonly value=\"".trunchaine(strtoupper($matiere),25)." (".$coeffaff.")\" size=50 title=\"$matiere\" ></font>";
	print "<br><i><font size=1> ".trunchaine(trim($profAff),40)." </font></i></td>";
	// mise en place moyenne eleve
	// mise en place des notes
	if (PRODUCTID == "b3a295e8e8551f5cb0ebaf4814ca3ec0") {
		$coeffaff=1;
	}
	$idprof=profAff($idMatiere,$idClasse,$ordre[$i][2]);
	if ($_GET["type_bulletin"] == "ismapp") {
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
                                 if ($value == "CC")      { $valcoef="1"; }
                                 if ($value == "DST")     { $valcoef="2"; }
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

                                $epreuve.="$value:$noteaff ($valcoef) ";
                                $moyenneTT+=$noteaff*$valcoef;
                                $coef+=$valcoef;
                        }
                }
                $noteaff=$moyenneTT/$coef;


		if ($noteaff != "") {
	                $noteaff1=number_format($noteaff,2,',','');
			$cumulPoint+=$noteaff*$coeffaff;
			$nbcoeftotal+=$coeffaff;
                }else{
                	$noteaff1="<input type='text' size='4' onBlur='adpaterMoyenne(this.value,$coeffaff)'  />";
		}

		$couleur="black";	
		if ($noteaff1 < 10) { $couleur="red"; }
		if ($noteaff1 >= 15) { $couleur="green"; }


        }else{
		if ($idgroupe == "0") {
			$noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);			
			$notetype=recherchetypenote($ordre[$i][0],$dateDebut,$dateFin,$idClasse);
		}else{
			$noteaff=moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
			$notetype=recherchetypenotegroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe);
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
			$couleur="black";
        	        if ($noteaff1 < 10) { $couleur="red"; }
	                if ($noteaff1 >= 15) { $couleur="green"; }
		}
	}
	$commentaireeleve="&nbsp;&nbsp;&nbsp;";
	$commentaireeleve=cherche_com_eleve($idEleve,$idMatiere,$idClasse,$tri,$idprof,$idgroupe)."$epreuve";
	$commentaireeleve=preg_replace("/'/","\\'",$commentaireeleve);
	$commentaireeleve=preg_replace('/"/',"&rdquo;",$commentaireeleve);
        $commentaireeleve=preg_replace('/\r\n/',"<br />",$commentaireeleve);
	$commentaireeleve=preg_replace('/\n/',"<br />",$commentaireeleve);
	$commentaireeleve=stripslashes($commentaireeleve);
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

	$color='#FFFFFF';
	// ./commentaire.php?idm=$idMatiere&ide=$idEleve&idc=$idClasse&tri=$tri&idprof=$idprof&idgroupe=$idgroupe
	print "<td bordercolor='blue' align=center bgcolor='#FFFFFF'> <font size=3 >&nbsp;$moyeMatGenaff</td>";
	if ($_SESSION["membre"] == "menuadmin") {
		print "<td bgcolor='$color' ><div id='com$i' ><a href='#' onclick=\"modifCom('com$i','$idMatiere','$idEleve','$idClasse','$tri','$idprof','$idgroupe')\" ><font class='T2'>".stripslashes($commentaireeleve)."</font></a></div></td>";
	}else{
		print "<td bgcolor='$color' ><font class='T2'>".stripslashes($commentaireeleve)."</font></td>";
	}
	print "</tr>";
	// pour le calcul de la moyenne general de l'eleve
	if (( trim($noteaff) != "" ) && ( $noteaff >= 0 )) {
		$noteMoyEleGTempo=0;
		$noteMoyEleG += $coeffaff * $noteaff;
		$coefEleG += $coeffaff ;
	}

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
		$couleur="black";
                if ($noteaff1 < 10) { $couleur="red"; }
                if ($noteaff1 >= 15) { $couleur="green"; }


	}
	print "<tr>";
	print "<td><font size=2><input type=text readonly value=\"".trunchaine(strtoupper("Vie Scolaire"),15)." (".$coefBull.")\" size=30></font>";
	print "<br><i><font size=1> ".trunchaine(trim($persVieScolaire),20)." </font></i></td>";
	print "<td bordercolor='orange' align=center bgcolor='#FFFFFF'>&nbsp;";
	//$com=cherche_com_scolaire_eleve_cpe($idEleve,$idMatiere,$idClasse,$tri,$idgroupe);
	$com=cherche_com_scolaire_eleve_cpe($idEleve,"-10",$idClasse,$tri,"");
	print "<a href='#' onMouseOver=\"AffBulle('$com');\"  onMouseOut=\"HideBulle()\"; onclick=\"open('./commentaire.php?idm=$idMatiere&ide=$idEleve&idc=$idClasse&tri=$tri&idprof=$idprof&idgroupe=$idgroupe','','width=400,height=200')\" ><font size=3 color=$couleur><b>$noteaff1</b></font></a></td>";
	print "<td bordercolor='blue' align=center bgcolor='#FFFFFF'> <font size=3 >&nbsp;$moyeMatGen1</td>";
	print "</tr>";
	// pour le calcul de la moyenne general de l'eleve
	if (( trim($noteaff) != "" ) && ( $noteaff >= 0 )) {
		$noteMoyEleGTempo=0;
		$noteMoyEleG += $coefBull * $noteaff;
		$coefEleG += $coefBull ;
	}
}

// fin notes
// --------

// affichage de la moyenne generale eleve
if ($afficheMoyen == "oui") {
	if ($_GET["type_bulletin"] == "ismapp") {
		$moyenEleve=$cumulPoint/$nbcoeftotal;
	        $moyenEleve=number_format($moyenEleve,2,',','');

	}else{
		$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
	}
	if (($moyenEleve == " ") || ($moyenEleve < 0)) { $moyenEleve="&nbsp;"; }
	$noteMoyEleG=0; // pour la moyenne de l'eleve general
	$coefEleG=0; // pour la moyenne de l'eleve general
	$color="black";
	if ($moyenEleve < 10) { $color="red"; }
        if ($moyenEleve >= 15) { $color="green"; }

	$moyenEleve=preg_replace('/,/','.',$moyenEleve);


	print "<tr><td  bgcolor='#FFFFFF'><font size=2>&nbsp;".LANGPROJ16."</font></td>";
	print "<td bgcolor='#FFFFFF' bordercolor='orange' align=center><b><font size=3 color=$color >";
	print "<span id='moyenGG' >$moyenEleve</span>";
	print "<input type=text id='moyenG' value='$moyenEleve' style='display:none' />";
	print "</font></b></td>";
	print "<td  bgcolor='#FFFFFF' bordercolor='blue' align=center><font size=3>$moyenClasseGen</font>";
	print "</td>";
	if ($_GET["type_bulletin"] == "ismapp") {
		print "<td id='bordure'><b><font color=#FFFFFF>Cumul : <span id='cumul'>$cumulPoint</span> / Coef : <span id='cumulcoef'>$nbcoeftotal</span></font></b></td>";
	}
	print "</tr>";
}
// fin affichage moy eleve


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


function adpaterMoyenne(valeur,coef) {
	if (valeur == "") valeur='0';

	var val=document.getElementById('moyenG').value;
	var valeurnew=<?php print $cumulPoint ?>+(eval(valeur)*coef) ;
	document.getElementById('cumul').innerHTML=valeurnew;
	var cumulcoef=<?php print $nbcoeftotal ?>+coef
	document.getElementById('cumulcoef').innerHTML=cumulcoef;
	valeurnew=(valeurnew/cumulcoef).toFixed(2) ;
			
	document.getElementById('moyenGG').innerHTML=valeurnew;
	document.getElementById('moyenG').value=valeurnew;

}

</script>
</body>
</html>
