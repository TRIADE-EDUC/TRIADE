<?php
function recupCodeEpreuve($serie,$matiere) {
	if ($serie == "LV2") {
		switch($matiere){
		case 'Français' : return 101; break;
		case 'Mathematiques' : return 102; break;
		case 'lv1' : return 103; break;
		case 'SVT' : return 104; break;
		case 'physChimi' : return 105; break;
		case 'eps' : return 106; break;
		case 'Arts plastiques' : return 107; break;
		case 'Education musicale' : return 108; break;
		case 'Technologique' : return 109; break;
		case 'LV2' : return 110; break;
		case 'viescolaire' : return 112; break;
		case 'OPT' : return 113; break;
		case 'histoireGeo' : return 121; break;
		case 'educationcivique' : return 122; break;
		case 'b2i' : return 114; break;
		case 'A2' : return 130; break;
		case 'histoire des arts' : return '005'; break;
		}
	}


	if ($serie == "STA") {
		switch($matiere){
		case 'Français' : return 101; break;
		case 'Mathematiques' : return 102; break;
		case 'lv1' : return 103; break;
		case 'sciencephysique' : return 104; break;
		case 'prevsantenv' : return 105; break;
		case 'eps' : return 106; break;
		case 'EducationSocio' : return 107; break;
		case 'SciencesBio' : return 108; break;
		case 'TechnoAgricole' : return 109; break;
		case 'viescolaire' : return 112; break;
		case 'histoireGeo' : return 121; break;
		case 'A2' : return 130; break;
		case 'histoire des arts' : return '005'; break;
		}
	}

	if (($serie == "DP6") || (strtoupper($serie) == "DP6H")) {
		switch($matiere){
		case 'Français' : return 101; break;
		case 'Mathematiques' : return 102; break;
		case 'lv1' : return 103; break;
		case 'SVT' : return 104; break;
		case 'physChimi' : return 105; break;
		case 'eps' : return 106; break;
		case 'Arts plastiques' : return 107; break;
		case 'Education musicale' : return 108; break;
		case 'Technologique' : return 109; break;
		case 'DP6h' : return 110; break;
		case 'viescolaire' : return 112; break;
		case 'OPT' : return 113; break;
		case 'histoireGeo' : return 121; break;
		case 'educationcivique' : return 122; break;
		case 'b2i' : return 114; break;
		case 'A2' : return 130; break;
		case 'histoire des arts' : return '005'; break;
		}
	}

}



?>
