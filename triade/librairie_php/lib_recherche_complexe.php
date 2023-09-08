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

//<option >Choix ......</option>
// pour eviter tout decalage avec le graphique.
// max 12 caracteres dans l'option
$nom1=LANGNA1;
$prenom1=LANGNA2;
$classe=LANGELE4;
$naissance=LANGELE10;
$nationalite=LANGELE11;
$num_etablissement=LANGELE27;
$adresse1=LANGELE14;
$ville=LANGPARAM11;
$regime=LANGIMP11;
$profpere=LANGIMP21;
$profmere=LANGIMP23;
$email_eleve=LANGEDIT11;
$email_parent=LANGPARAM13." ".LANGEDIT10;
$tel_eleve=LANGEDIT12;
$num_etudiant=LANGELE12;
$code_postal=LANGELE15;
$classe_ant=LANGbasededoni41;
$option=ucwords(LANGIMP28);

$nomT1=LANGEDIT8;
$prenomT1=LANGEDIT5bis;
$nomT2=LANGEDIT4;
$prenomT2=LANGEDIT5;
$telephone=LANGIMP20;
$profpere=LANGIMP21;
$telprofpere=LANGIMP22;
$profmere=LANGIMP23;
$telprofmere=LANGIMP24;
$telport1=LANGEDIT2;
$lieudenaissance=LANGEDIT6;
$telport2=LANGEDIT9;
$email_parent_2="Email Parent 2";

$affiche=<<<EOF
<option STYLE='color:#000066;background-color:#CCCCFF' value='nom' >$nom1 </option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='prenom' >$prenom1 </option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='classe' >$classe </option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='LV1' >LV1 </option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='LV2' >LV2 </option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='option' >$option </option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='naissance' >$naissance</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='sexe' >Sexe</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='lieudenaissance' >$lieudenaissance</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='code_compta' >Code Comptabilité</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='nationalite' >$nationalite</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='numero etablissement' >$num_etablissement</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='adresse'>$adresse1</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='ville' >$ville</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='regime' >$regime</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='profession pere' >$profpere</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='profession mere' >$profmere</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='email parent' >$email_parent</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='email tuteur 2' >$email_parent_2</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='email eleve' >$email_eleve</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='tel eleve' >$tel_eleve</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='idnational' >$num_etudiant</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='codepostal' >$code_postal</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='classe_anterieure' >$classe_ant</option>

<option STYLE='color:#000066;background-color:#CCCCFF' value='nomT1'       >$nomT1</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='prenomT1'    >$prenomT1</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='nomT2'       >$nomT2</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='prenomT2'    >$prenomT2</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='telephone'   >$telephone</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='profpere'    >$profpere</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='telprofpere' >$telprofpere</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='profmere'    >$profmere</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='telprofmere' >$telprofmere</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='telport1'    >$telport1</option>
<option STYLE='color:#000066;background-color:#CCCCFF' value='telport2'    >$telport2</option>
EOF;
print $affiche;
?>
