<?php
function showCalendar($periode) {
	
		global $tab_res_1;
		global $tab_res_deb_1;
		global $tab_res_fin_1;
		
		global $tab_res_2;
		global $tab_res_deb_2;
		global $tab_res_fin_2;
		
		$an = getYear($periode);
		$mois = getMonth($periode); 
		
		$tab_res = array();
		$tab_res_deb = array();
		$tab_res_fin = array();
		
		$tab_slib = array();
		$tab_slib_deb = array();
		$tab_slib_fin = array();
		
		$jour = "1";
		while ($jour <= 31) {
		
			$date_test = "$an-$mois";
			if ($jour < 10) {
				$date_test .= "-0$jour";
			} else {
				$date_test .= "-$jour";
			} 
			
			// tableau des jours réservés
			if(in_array($date_test, $tab_res_1)){
				$tab_res[] = $jour;
			} 
			
			//tableau des jours partiellement libres
			if(in_array($date_test, $tab_res_2)){
				$tab_slib[] = $jour;
			} 
			
			$jour++; 
		}
	
		$leCalendrier = "";
		$leCalendrier .= '<div id="cadre_calendrier">'; 
		// Tableau des valeurs possibles pour un numéro de jour dans la semaine
		$tableau = Array("0", "1", "2", "3", "4", "5", "6", "0");
		$nb_jour = Date("t", mktime(0, 0, 0, getMonth($periode), 1, getYear($periode)));
		$pas = 0;
		$indexe = 1;
 
		// Affichage du mois et de l'année
		 $leCalendrier .= "\n\t<h2>" . monthNumToName(getMonth($periode)) . " " . getYear($periode) . "</h2>"; 
   
		// Affichage des entêtes
		$leCalendrier .= "
		<ul id=\"libelle\">
			\t<li>L</li>
			\t<li>M</li>
			\t<li>M</li>
			\t<li>J</li>
			\t<li>V</li>
			\t<li>S</li>
			\t<li>D</li>
		</ul><br/>";
		// Tant que l'on n'a pas affecté tous les jours du mois traité
		while ($pas < $nb_jour) {
			if ($indexe == 1) $leCalendrier .= "\n\t<ul class=\"ligne\">";
			// Si le jour calendrier == jour de la semaine en cours
			if (Date("w", mktime(0, 0, 0, getMonth($periode), 1 + $pas, getYear($periode))) == $tableau[$indexe]) {
				// Si jour calendrier == aujourd'hui
				$afficheJour = Date("j", mktime(0, 0, 0, getMonth($periode), 1 + $pas, getYear($periode)));
				
				if (in_array ($afficheJour, $tab_res) or in_array ($afficheJour, $tab_slib)) {
					// entre deux reserv
					if(in_array ($afficheJour, $tab_slib)){
						$class = " class=\"option_entre "; 
					}
					else{
						$class = " class=\"reserve "; 
					}	
				}else{
					$class = " class=\"libre_basse "; 
					// si $affichJour n'est pas dans le tableau des jours réservés alors on ne le met pas en forme
				} 
					
				// la date du jour en gras
				if (Date("Y-m-d", mktime(0, 0, 0, getMonth($periode), 1 + $pas, getYear($periode))) == Date("Y-m-d")) {
					$class .= " today\"";
				}
				else{
					$class .= "\"";
				}	
				
				// Ajout de la case avec la date
				$leCalendrier .= "\n\t\t<li$class>$afficheJour</li>";
				$pas++;
			}
			//
			else {
				// Ajout d'une case vide
				$leCalendrier .= "\n\t\t<li>&nbsp;</li>";
			}
			if ($indexe == 7 && $pas < $nb_jour) { 
				$leCalendrier .= "\n\t</ul><br/>"; 
				$indexe = 1;
			} else {
				$indexe++;
			}
		}
		// Ajustement du tableau
		for ($i = $indexe; $i <= 7; $i++) {
			$leCalendrier .= "\n\t\t<li>&nbsp;</li>";
		}
		$leCalendrier .= "\n\t</ul><br/><br/>\n";
 
		// Retour de la chaine contenant le Calendrier
		return $leCalendrier;
	}
?>
