<?php
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

function fete_prenom() {

// ===== Declaration du fichier des prenoms: prenom.txt
// Attention: Le fichier prenom.txt doit etre sauve sous word au format: Texte MS-DOS avec saut de ligne
	$fp = "data/parametrage/liste_prenom";

// ===== Declaration du tableau des noms de mois en Francais
	$m_fr{1}  = "Janvier";
	$m_fr{2}  = "F&eacutevrier";
	$m_fr{3}  = "Mars";
	$m_fr{4}  = "Avril";
	$m_fr{5}  = "Mai";
	$m_fr{6}  = "Juin";
	$m_fr{7}  = "Juillet";
	$m_fr{8}  = "Ao&ucirct";
	$m_fr{9}  = "Septembre";
	$m_fr{10} = "Octobre";
	$m_fr{11} = "Novembre";
	$m_fr{12} = "D&eacutecembre.";

// ===== ouverture du fichier des prenoms. Chaque ligne sera une ligne d_un tableau
	$fpren = file($fp);

// ===== recherche du prenom dans le fichier des prenomns.
// Affichage du prenom et de son jour et mois de fete si trouve
	$jour=intval(date("j"));
	$mois=intval(date("n"));
	$prenom="";

	for($i=0;$i<count($fpren);$i++) {
		$case = explode(";", $fpren[$i]);
	     	if (($case[1] == $jour) && ($case[2] == $mois )) {

		  // recupe le prenom
		  $prenom=trim($case[0]);

       		  // retrait des blancs dans la chaine de caractere representant le numero du mois
       		  $case[2] = trim($case[2]);
	
		  // Conversion du nom du mois en Francais
		  $mois_l = $m_fr{$case[2]} ;

        	  // si on est le 1er du mois alors la variable $premier contier "er"
	          if($case[1] == "1") {$premier = "er"; }


        	  // affichage du resultat trouve
    		  //print("La f&ecircte de $case[0] est le  $case[1]$premier $mois_l ");

    	  	  // positionnement de $i a la valeur maxi pour sortir de la boucle rapidement
       		  //$i = count($fpren);
		  $listeprenom.=" ".ucwords($prenom).",";
	   	  } 
	}
	$listeprenom=preg_replace('/,$/','.',$listeprenom);
	$retour=$listeprenom  ."</i>";	  
	
return $retour;
}
?>
