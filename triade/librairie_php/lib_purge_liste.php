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

// pas plus grand que ça :
//<option >Choix ......</option>
// pour eviter tout decalage avec le graphique.
// max 12 caracteres dans l'option
/*
	purgeprofPmedsupp();
*/
$affiche=<<<EOF
<optgroup label="Gestion personnels">
<option STYLE='color:#000066;background-color:#CCCCFF' value='direction' >Compte Direction </option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='enseignant' >Compte Enseignant </option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='vie scolaire' >Compte Vie scolaire</option>
<optgroup label="Gestion élèves / Parents">
<option STYLE='color:#000066;background-color:#CCCCFF' value='eleves' title='Compte Eleves / Parents' >Compte Eleves / Parents </option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='notes' >Les Notes </option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='notesscolaire' >Notes vie scolaire </option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='discipline' >Les disciplines</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='abs' >Les absences </option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='abssconet' >Les abs. sconet </option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='retard' >Les Retards</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='present' >Les Présents</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='dispenses' >Les Dispenses</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='entretien' >Les entretiens</option>
<optgroup label="Gestion stage">
<option STYLE='color:#000066;background-color:#CCCCFF' value='entreprises' >Entreprises / Tuteur Stage</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='datestage' >Date de stage</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='affectationstage' >Affectation élève</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='contrerendustage' >Contre rendu stage</option>
<optgroup label="Gestion enseignants">
<option STYLE='color:#000066;background-color:#CCCCFF' value='profpsupp' >L'Attribution Prof P.</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='messprofP' >Message Prof Princ.</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='devoirscolaire' >Devoir scolaire </option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='deleguesupp' >Attribution délégué</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='dst' >D.S.T. </option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='entretienduree' >Temps d'accompagnement</option>
<optgroup label="Gestion scolaires">
<option STYLE='color:#000066;background-color:#CCCCFF' value='purgimport'>Les Fichiers Imports</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='news' >Les Messages news</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='groupe' >Les groupes</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='parametude' >Etudes Param</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='purgevenement' >Gestion événements</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='purgaffectation' >Gestion affectations</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='trimestre' >Gestion Trimestre</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='trimestre' >Gestion Semestre</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='hist_periode'>Relevé période</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='hist_bulletin' >Relevé bulletin</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='com_bulletin' title='Les commentaires Bulletins' >Les commentaires Bulletins</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='purgcirculaire' >Liste circulaire</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='purgcertificat' >Certificat scolarité</option>
<optgroup label="Autres">
<option STYLE='color:#000066;background-color:#CCCCFF' value='brevetcollege' >Brevet Collège</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='inforesponsableeleve' title='Info Responsable Elève' >Info Responsable Elève</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='agenda' >Agenda</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='comptabilite' >Info Comptabilité</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='edt' >E.D.T</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='grpmail' >Groupe Messagerie</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='cantine' >Gestionnaire cantine</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='elevesansclasse' >Elève sans classe</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='stockage' >Stockage</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='reservation' >Réservation</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='equipement' >Equipement Résa.</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='forum' >Message Forum</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='photodeFrance' >WellPhoto</option>
EOF;
print $affiche;
?>
