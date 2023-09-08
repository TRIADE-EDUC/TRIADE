<?php
include_once("common/config2.inc.php");
if (NOTEEXAMEN == "oui") {
 // voir aussi fichier notemodif3.php si ajout d'élèment
 // voir aussi fichier notevisuadmin.php si ajout d'élèment
?>
			<select name="NoteExam">
						<option value="" STYLE="color:#000066;background-color:#FCE4BA">non</option>
					<?php if (EXAMENBLANC == "oui") { ?>
						<optgroup label="Blanc" />
						<?php if (PRODUCTID != "2b85614b9c7cc3e8f7f02fe4fd52e907") { ?>
					        	<option value="Brevet Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>Brevet Blanc</option>
						        <option value="Brevet Professionnel Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>Brevet Professionnel Blanc</option>
						        <option value="BAC Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>BAC Blanc</option>
						        <option value="CAP Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>CAP Blanc</option>
						        <option value="BEP Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>BEP Blanc</option>
						<?php } ?>
					        <option value="BTS Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>BTS Blanc</option>
					        <option value="Partiel Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>Partiel Blanc</option>
						<?php if (PRODUCTID != "2b85614b9c7cc3e8f7f02fe4fd52e907") { ?>
							<option value="Concours Blanc"  STYLE='color:#000066;background-color:#CCCCFF'>Concours Blanc</option>
						<?php } ?>
					<?php } ?>
					<?php if (EXAMENNAMUR == "oui") { ?>							
							<optgroup label="Spécif. Namur" />
					                <option value="décembre"  STYLE='color:#000066;background-color:#CCCCFF'>Décembre</option>
							<option value="juin" STYLE='color:#000066;background-color:#CCCCFF'>Juin</option>
					<?php } ?>
					<?php if (EXAMENPIGIERNIMES == "oui") { ?>
							<optgroup label="PIGIER" />
							<option value="ND" STYLE='color:#000066;background-color:#CCCCFF'>Note Devoir (DS)</option>
						        <option value="NP" STYLE='color:#000066;background-color:#CCCCFF'>Note Participation</option>
							<option value="DS" STYLE='color:#000066;background-color:#CCCCFF'>DS</option>
							<option value="examen" STYLE='color:#000066;background-color:#CCCCFF'>Examen</option>
							<option value="examen blanc" STYLE='color:#000066;background-color:#CCCCFF'>Examen Blanc</option>
					<?php } ?>
					<?php if (EXAMENISMAP == "oui") { ?>
						    <optgroup label="ISMAP" />
						    <option value="CC" STYLE='color:#000066;background-color:#CCCCFF'>CC - Participation</option>
						    <option value="DST" STYLE='color:#000066;background-color:#CCCCFF'>DST</option>
						    <option value="Partiel" STYLE='color:#000066;background-color:#CCCCFF'>Partiel</option> 
						    <option value="Soutenance" STYLE='color:#000066;background-color:#CCCCFF'>Soutenance</option> 
						    <option value="Rapport" STYLE='color:#000066;background-color:#CCCCFF'>Rapport</option>
						    <option value="Fiche de lecture" STYLE='color:#000066;background-color:#CCCCFF'>Fiche de lecture</option>
						    <option value="Exposé" STYLE='color:#000066;background-color:#CCCCFF'>Exposé</option>
  						    <option value="Dad" STYLE='color:#000066;background-color:#CCCCFF'>Dad</option>
						    <option value="Lecture" STYLE='color:#000066;background-color:#CCCCFF'>Lecture</option>
                                                    <option value="Examen écrit" STYLE='color:#000066;background-color:#CCCCFF'>Examen écrit</option>
                                                    <option value="Recopiage vocabulaire" STYLE='color:#000066;background-color:#CCCCFF'>Recopiage vocabulaire</option>
                                                    <option value="Mémoire Ip" STYLE='color:#000066;background-color:#CCCCFF'>Mémoire Ip</option>
                                                    <option value="Evaluation Tutorat" STYLE='color:#000066;background-color:#CCCCFF'>Evaluation Tutorat</option>

					<?php } ?>
					<?php if (EXAMENDS == "oui") { ?>
							<optgroup label="DS" />
							<option value="DS1"  STYLE='color:#000066;background-color:#CCCCFF'>DS1</option>
							<option value="DS2"  STYLE='color:#000066;background-color:#CCCCFF'>DS2</option>
							<option value="DS3"  STYLE='color:#000066;background-color:#CCCCFF'>DS3</option>
							<option value="DS4"  STYLE='color:#000066;background-color:#CCCCFF'>DS4</option>
					<?php } ?>
					<?php if (EXAMEN == "oui") { ?>	
							<optgroup label="Examen" />
							<option value="Partiel"  STYLE='color:#000066;background-color:#CCCCFF'>Partiel</option>
					<?php } ?>
					<?php if (EXAMENISPACADEMIES == "oui") { ?>
						    <optgroup label="ISP ACADEMIES" />
						    <option value="ISP" STYLE='color:#000066;background-color:#CCCCFF'>ISP</option>
					<?php } ?>
					<?php if (EXAMENCIEFORMATION == "oui") { ?>							
							<optgroup label="Spécif. Cie. Formation" />
					                <option value="TAS"  STYLE='color:#000066;background-color:#CCCCFF'>TAS</option>
							<option value="BTS Blanc" STYLE='color:#000066;background-color:#CCCCFF'>BTS Blanc</option>
							<option value="Partiel Blanc" STYLE='color:#000066;background-color:#CCCCFF'>Partiel Blanc</option>
					<?php } ?>
					<?php if (EXAMENEEPP == "oui") { ?>
							<optgroup label="Spécif. EEPP" />
   							<option value="semestre" STYLE='color:#000066;background-color:#CCCCFF'>Semestriel</option>
   							<option value="2session" STYLE='color:#000066;background-color:#CCCCFF'>2ème session</option>
					<?php } ?>
					<?php if (EXAMENJTC == "oui") { ?>
                                                        <optgroup label="Spécif. JTC" />
                                                        <option value="jtc" STYLE='color:#000066;background-color:#CCCCFF'>Carnet</option>
                                        <?php } ?>
					<?php if (EXAMENIPAC == "oui") { ?>
							<optgroup label="IPAC" />
							<option value="Partiel" STYLE='color:#000066;background-color:#CCCCFF'>Partiel</option>
   							<option value="Rattrapage" STYLE='color:#000066;background-color:#CCCCFF'>Rattrapage</option>
   							<option value="Examen complémentaire" STYLE='color:#000066;background-color:#CCCCFF'>Examen complémentaire</option>
   							<option value="Contrôle continu" STYLE='color:#000066;background-color:#CCCCFF'>Contrôle continu</option>
					<?php } ?>

					<?php if (EXAMENBREVETCOLLEGE == "oui") { ?>
							<optgroup label="Brevet Collège" />
						   	<option value="Brevet EPS" STYLE='color:#000066;background-color:#CCCCFF'>Brevet EPS</option>
							<option value="Brevet PREV. SANTE ENV." STYLE='color:#000066;background-color:#CCCCFF'>Brevet PREV. SANTE ENV.</option>
					<?php } ?>
                                       </select>
<?php
}
?>
