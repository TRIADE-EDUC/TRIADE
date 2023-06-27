<?php
class planning {
	var $nb_jours_defaut = 14;

	function afficher($date_debut, $lignes, $plages) {
		$date_courante = $date_debut;
		//echo "<pre>";
		//print_r($plages);
		//echo "</pre>";
		?>
		<style type="text/css">
			.planning {
				border-left:#000000 solid 1px;
				border-top:#000000 solid 1px;
			}
			.planning .jour_semaine {
				border-right:#000000 solid 1px;
				border-bottom:#000000 solid 1px;
				width:64px;
				background-color:#999999;
				font-weight:bold;
			}
			.planning .ajout {
				border-right:#000000 solid 1px;
				border-bottom:#000000 solid 1px;
				width:60px;
				background-color:#999999;
				font-weight:bold;
			}
			.planning .colonne_gauche {
				border-right:#000000 solid 1px;
				border-bottom:#000000 solid 1px;
				padding-left:3px;
				padding-right:3px;
			}
			.planning .jours {
				border-right:#000000 solid 1px;
				border-bottom:#000000 solid 1px;
			}
			.planning .jours_actif {
				border-right:#000000 solid 1px;
				border-bottom:#000000 solid 1px;
				background-color:#FFCC00;
			}
			.planning .jours_actif_orange {
				border-right:#000000 solid 1px;
				border-bottom:#000000 solid 1px;
				background-color:#fdcc49;
			}
			.planning .jours_actif_rouge {
				border-right:#000000 solid 1px;
				border-bottom:#000000 solid 1px;
				background-color:#f2888a;
			}
		</style>
		<table border="0" cellpadding="0" cellspacing="0" align="center" class="planning">
			<tr>
				<td align="left" class="jour_semaine" nowrap="nowrap"><?php echo LANG_CHA_RESA_006; ?></td>
				<?php
				for($jour=1;$jour<=$this->nb_jours_defaut;$jour++) {
					$pos_jour = date('w', strtotime($date_courante));
					if($pos_jour == 0) {
						$pos_jour = 7;
					}
					//echo "$date_courante = " . $pos_jour . "<br>";
					//echo "texte = LANG_CHA_JOURS_00" . $pos_jour . ";<br>";
					eval("\$texte = LANG_CHA_JOURS_00" . $pos_jour . ";");
				?>
				<td align="center" class="jour_semaine" nowrap="nowrap"><?php echo $texte . '<br>' . date("d/m/Y", strtotime($date_courante)); ?></td>
				<?php
					$timestamp = strtotime($date_courante);
					$date_courante = date('Y-m-d', mktime(0,0,0,date('m',$timestamp),date('d',$timestamp) + 1,date('Y',$timestamp))); 
				}
				?>
				<td align="center" class="ajout" nowrap="nowrap"><?php echo "Option"; ?></td>
			</tr>

			<?php
			for($ligne=0;$ligne<count($lignes);$ligne++) {
			?>
			<tr>
			<?php
				$nom = $lignes[$ligne]['libelle'];
				print"<td align='left' class='colonne_gauche' nowrap='nowrap'><span id='disp$ligne'>$nom</span></td>";
				$date_courante = $date_debut;
				for($jour=1;$jour<=$this->nb_jours_defaut;$jour++) {
					// Verifier si il y a une reservation pour ce jour et cette chambre
					$classe = 'jours';
					$texte = '&nbsp;';
					//$texte .= '{' . $plages[$plage]['id_ligne'] . '-'.$lignes[$ligne]['id_ligne'].'}';
					for($plage=0;$plage<count($plages);$plage++) {
						//$texte .= '[' . $plages[$plage]['id_ligne'] . '-'.$lignes[$ligne]['id_ligne'].']';
						if($plages[$plage]['id_ligne'] == $lignes[$ligne]['id_ligne']) {
							if(strtotime($date_courante) >= strtotime($plages[$plage]['date_debut']) && strtotime($date_courante) <= strtotime($plages[$plage]['date_fin'])) {
								//$classe = 'jours_actif';
								$classe = $plages[$plage]['classe_cellule'];
								$texte = $plages[$plage]['libelle'];
								break;
							}
						}
					}
			?>
				<td align="center" class="<?php echo $classe; ?>" nowrap="nowrap" valign="middle"><?php echo $texte; ?></td>
			<?php
					$timestamp = strtotime($date_courante);
					$date_courante = date('Y-m-d', mktime(0,0,0,date('m',$timestamp),date('d',$timestamp) + 1,date('Y',$timestamp))); 
				}
			
			$chambre_id = $lignes[$ligne]['id_ligne'];	
			$debut_date = date("d/m/Y", strtotime($date_debut));
			$fin_date =  date("d/m/Y", strtotime(date('Y-m-d', mktime(0,0,0,date('m',$timestamp),date('d',$timestamp),date('Y',$timestamp))))); 
			$annee = date("Y", strtotime($date_debut));	
			$operation = 'par_planing';
			print "
				<td align='center' class='ajout' nowrap='nowrap'>
					<a href='module_chambres/planning_reservation.php?operation1=execute&chambre=$chambre_id&date_debut=$debut_date&date_fin=$fin_date' title=\"Ajouter une réservation\" onmouseover=\"document.getElementById('disp$ligne').style.cssText='color:blue;font-weight:bold;'\"  
					onmouseout=\"document.getElementById('disp$ligne').style.cssText='color:black;' \" ><img src='module_chambres/images/ajouter.png' border='0' align='center'/></a>
					<a href=\"#\"  title=\"Voir le calendrier de la chambre\" 
					onmouseover=\"document.getElementById('disp$ligne').style.cssText='color:green;font-weight:bold;'\"
					onmouseout=\"document.getElementById('disp$ligne').style.cssText='color:black;'\" 
					onclick=\"window.open('module_chambres/planning_calendrier.php?operation=$operation&chambre=$chambre_id&annee=$annee','','toolbar=0,menubar=0,location=0,scrollbars=1,width=840,height=800')\">
					<img src='module_chambres/images/calendrier.png' border='0' align='center'/>
					</a>	
					
					
				";?>
				
				</td>
			</tr>
			<?php
			}
			?>


		</table>
		<?php
	}
}
?>
