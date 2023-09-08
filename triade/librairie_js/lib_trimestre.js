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


function trimes22() {
	if (document.formulaire1.typetrisem.options[document.formulaire1.typetrisem.selectedIndex].value == "trimestre") {
		document.formulaire1.saisie_trimestre.options[0].text="Trimestre 1";
		document.formulaire1.saisie_trimestre.options[1].text="Trimestre 2";
		document.formulaire1.saisie_trimestre.options[2].text="Trimestre 3";
		document.formulaire1.saisie_trimestre.options[3].text="        "; 
		document.formulaire1.saisie_trimestre.options[4].text="        ";
		document.formulaire1.saisie_trimestre.options[5].text="        ";
		document.formulaire1.saisie_trimestre.options[6].text="        "; 
		document.formulaire1.saisie_trimestre.options[7].text="        ";
		document.formulaire1.saisie_trimestre.options[8].text="        ";
		document.formulaire1.saisie_trimestre.options[0].value="trimestre1";
		document.formulaire1.saisie_trimestre.options[1].value="trimestre2";
		document.formulaire1.saisie_trimestre.options[2].value="trimestre3";
		document.formulaire1.saisie_trimestre.options[3].value="0";
		document.formulaire1.saisie_trimestre.options[4].value="0";
		document.formulaire1.saisie_trimestre.options[5].value="0";
		document.formulaire1.saisie_trimestre.options[6].value="0";
		document.formulaire1.saisie_trimestre.options[7].value="0";
		document.formulaire1.saisie_trimestre.options[8].value="0";
	}
	if (document.formulaire1.typetrisem.options[document.formulaire1.typetrisem.selectedIndex].value == "cycle") {
		document.formulaire1.saisie_trimestre.options[0].text="Cycle 1";
		document.formulaire1.saisie_trimestre.options[1].text="Cycle 2";
		document.formulaire1.saisie_trimestre.options[2].text="Cycle 3";
		document.formulaire1.saisie_trimestre.options[3].text="Cycle 4"; 
		document.formulaire1.saisie_trimestre.options[4].text="        ";
		document.formulaire1.saisie_trimestre.options[5].text="        ";
		document.formulaire1.saisie_trimestre.options[6].text="        "; 
		document.formulaire1.saisie_trimestre.options[7].text="        ";
		document.formulaire1.saisie_trimestre.options[8].text="        ";
		document.formulaire1.saisie_trimestre.options[0].value="cycle1";
		document.formulaire1.saisie_trimestre.options[1].value="cycle2";
		document.formulaire1.saisie_trimestre.options[2].value="cycle3";
		document.formulaire1.saisie_trimestre.options[3].value="cycle4";
		document.formulaire1.saisie_trimestre.options[4].value="0";
		document.formulaire1.saisie_trimestre.options[5].value="0";
		document.formulaire1.saisie_trimestre.options[6].value="0";
		document.formulaire1.saisie_trimestre.options[7].value="0";
		document.formulaire1.saisie_trimestre.options[8].value="0";
	}
	if (document.formulaire1.typetrisem.options[document.formulaire1.typetrisem.selectedIndex].value == "semestre") {
		document.formulaire1.saisie_trimestre.options[0].text="Semestre 1";
		document.formulaire1.saisie_trimestre.options[1].text="Semestre 2";
		document.formulaire1.saisie_trimestre.options[2].text="        ";
		document.formulaire1.saisie_trimestre.options[3].text="        "; 
		document.formulaire1.saisie_trimestre.options[4].text="        ";
		document.formulaire1.saisie_trimestre.options[5].text="        ";
		document.formulaire1.saisie_trimestre.options[6].text="        "; 
		document.formulaire1.saisie_trimestre.options[7].text="        ";
		document.formulaire1.saisie_trimestre.options[8].text="        ";
		document.formulaire1.saisie_trimestre.options[0].value="trimestre1";
		document.formulaire1.saisie_trimestre.options[1].value="trimestre2";
		document.formulaire1.saisie_trimestre.options[2].value="trimestre3";
		document.formulaire1.saisie_trimestre.options[3].value="0";
		document.formulaire1.saisie_trimestre.options[4].value="0";
		document.formulaire1.saisie_trimestre.options[5].value="0";
		document.formulaire1.saisie_trimestre.options[6].value="0";
		document.formulaire1.saisie_trimestre.options[7].value="0";
		document.formulaire1.saisie_trimestre.options[8].value="0";
	}
	if (document.formulaire1.typetrisem.options[document.formulaire1.typetrisem.selectedIndex].value == "examen") {
		document.formulaire1.saisie_trimestre.options[0].text="Examen Juin";
		document.formulaire1.saisie_trimestre.options[1].text="Examen Décembre";
		document.formulaire1.saisie_trimestre.options[2].text=" ";
		document.formulaire1.saisie_trimestre.options[3].text="        "; 
		document.formulaire1.saisie_trimestre.options[4].text="        ";
		document.formulaire1.saisie_trimestre.options[5].text="        ";
		document.formulaire1.saisie_trimestre.options[6].text="        "; 
		document.formulaire1.saisie_trimestre.options[7].text="        ";
		document.formulaire1.saisie_trimestre.options[8].text="        ";
		document.formulaire1.saisie_trimestre.options[0].value="exam_juin";
		document.formulaire1.saisie_trimestre.options[1].value="exam_dec";
		document.formulaire1.saisie_trimestre.options[2].value="0";
		document.formulaire1.saisie_trimestre.options[3].value="0";
		document.formulaire1.saisie_trimestre.options[4].value="0";
		document.formulaire1.saisie_trimestre.options[5].value="0";
		document.formulaire1.saisie_trimestre.options[6].value="0";
		document.formulaire1.saisie_trimestre.options[7].value="0";
		document.formulaire1.saisie_trimestre.options[8].value="0";
	}
	if (document.formulaire1.typetrisem.options[document.formulaire1.typetrisem.selectedIndex].value == "0") {
		document.formulaire1.saisie_trimestre.options[0].text="        "; 
		document.formulaire1.saisie_trimestre.options[1].text="        ";
		document.formulaire1.saisie_trimestre.options[2].text="        ";
		document.formulaire1.saisie_trimestre.options[3].text="        "; 
		document.formulaire1.saisie_trimestre.options[4].text="        ";
		document.formulaire1.saisie_trimestre.options[5].text="        ";
		document.formulaire1.saisie_trimestre.options[6].text="        "; 
		document.formulaire1.saisie_trimestre.options[7].text="        ";
		document.formulaire1.saisie_trimestre.options[8].text="        ";
		document.formulaire1.saisie_trimestre.options[0].value="0";
		document.formulaire1.saisie_trimestre.options[1].value="0";
		document.formulaire1.saisie_trimestre.options[2].value="0";
		document.formulaire1.saisie_trimestre.options[3].value="0";
		document.formulaire1.saisie_trimestre.options[4].value="0";
		document.formulaire1.saisie_trimestre.options[5].value="0";
		document.formulaire1.saisie_trimestre.options[6].value="0";
		document.formulaire1.saisie_trimestre.options[7].value="0";
		document.formulaire1.saisie_trimestre.options[8].value="0";
	}
	if (document.formulaire1.typetrisem.options[document.formulaire1.typetrisem.selectedIndex].value == "periode") {
		document.formulaire1.saisie_trimestre.options[0].text="1er";
		document.formulaire1.saisie_trimestre.options[1].text="2ieme";
		document.formulaire1.saisie_trimestre.options[2].text="3ieme";
		document.formulaire1.saisie_trimestre.options[3].text="4ieme";
		document.formulaire1.saisie_trimestre.options[4].text="5ieme";
		document.formulaire1.saisie_trimestre.options[5].text="6ieme";
		document.formulaire1.saisie_trimestre.options[6].text="7ieme";
		document.formulaire1.saisie_trimestre.options[7].text="8ieme";
		document.formulaire1.saisie_trimestre.options[8].text="9ieme";

		document.formulaire1.saisie_trimestre.options[0].value="periode1";
		document.formulaire1.saisie_trimestre.options[1].value="periode2";
		document.formulaire1.saisie_trimestre.options[2].value="periode3";
		document.formulaire1.saisie_trimestre.options[3].value="periode4";
		document.formulaire1.saisie_trimestre.options[4].value="periode5";
		document.formulaire1.saisie_trimestre.options[5].value="periode6";
		document.formulaire1.saisie_trimestre.options[6].value="periode7";
		document.formulaire1.saisie_trimestre.options[7].value="periode8";
		document.formulaire1.saisie_trimestre.options[8].value="periode9";
	}
}


function trimes2() {
	if (document.formulaire.typetrisem.options[document.formulaire.typetrisem.selectedIndex].value == "trimestre") {
		document.formulaire.saisie_trimestre.options[0].text="Trimestre 1";
		document.formulaire.saisie_trimestre.options[1].text="Trimestre 2";
		document.formulaire.saisie_trimestre.options[2].text="Trimestre 3";
		document.formulaire.saisie_trimestre.options[3].text="        " 
		document.formulaire.saisie_trimestre.options[4].text="        ";
		document.formulaire.saisie_trimestre.options[5].text="        ";
		document.formulaire.saisie_trimestre.options[6].text="        " 
		document.formulaire.saisie_trimestre.options[7].text="        ";
		document.formulaire.saisie_trimestre.options[8].text="        ";
		document.formulaire.saisie_trimestre.options[0].value="trimestre1";
		document.formulaire.saisie_trimestre.options[1].value="trimestre2";
		document.formulaire.saisie_trimestre.options[2].value="trimestre3";
		document.formulaire.saisie_trimestre.options[3].value="0";
		document.formulaire.saisie_trimestre.options[4].value="0";
		document.formulaire.saisie_trimestre.options[5].value="0";
		document.formulaire.saisie_trimestre.options[6].value="0";
		document.formulaire.saisie_trimestre.options[7].value="0";
		document.formulaire.saisie_trimestre.options[8].value="0";
	}
	if (document.formulaire.typetrisem.options[document.formulaire.typetrisem.selectedIndex].value == "semestre") {
		document.formulaire.saisie_trimestre.options[0].text="Semestre 1";
		document.formulaire.saisie_trimestre.options[1].text="Semestre 2";
		document.formulaire.saisie_trimestre.options[2].text="        ";
		document.formulaire.saisie_trimestre.options[3].text="        " 
		document.formulaire.saisie_trimestre.options[4].text="        ";
		document.formulaire.saisie_trimestre.options[5].text="        ";
		document.formulaire.saisie_trimestre.options[6].text="        " 
		document.formulaire.saisie_trimestre.options[7].text="        ";
		document.formulaire.saisie_trimestre.options[8].text="        ";
		document.formulaire.saisie_trimestre.options[0].value="trimestre1";
		document.formulaire.saisie_trimestre.options[1].value="trimestre2";
		document.formulaire.saisie_trimestre.options[2].value="trimestre3";
		document.formulaire.saisie_trimestre.options[3].value="0";
		document.formulaire.saisie_trimestre.options[4].value="0";
		document.formulaire.saisie_trimestre.options[5].value="0";
		document.formulaire.saisie_trimestre.options[6].value="0";
		document.formulaire.saisie_trimestre.options[7].value="0";
		document.formulaire.saisie_trimestre.options[8].value="0";
	}
	if (document.formulaire.typetrisem.options[document.formulaire.typetrisem.selectedIndex].value == "examen") {
		document.formulaire.saisie_trimestre.options[0].text="Examen Juin";
		document.formulaire.saisie_trimestre.options[1].text="Examen Décembre";
		document.formulaire.saisie_trimestre.options[2].text=" ";
		document.formulaire.saisie_trimestre.options[3].text="        " 
		document.formulaire.saisie_trimestre.options[4].text="        ";
		document.formulaire.saisie_trimestre.options[5].text="        ";
		document.formulaire.saisie_trimestre.options[6].text="        " 
		document.formulaire.saisie_trimestre.options[7].text="        ";
		document.formulaire.saisie_trimestre.options[8].text="        ";
		document.formulaire.saisie_trimestre.options[0].value="exam_juin";
		document.formulaire.saisie_trimestre.options[1].value="exam_dec";
		document.formulaire.saisie_trimestre.options[2].value="0";
		document.formulaire.saisie_trimestre.options[3].value="0";
		document.formulaire.saisie_trimestre.options[4].value="0";
		document.formulaire.saisie_trimestre.options[5].value="0";
		document.formulaire.saisie_trimestre.options[6].value="0";
		document.formulaire.saisie_trimestre.options[7].value="0";
		document.formulaire.saisie_trimestre.options[8].value="0";
	}
	if (document.formulaire.typetrisem.options[document.formulaire.typetrisem.selectedIndex].value == "0") {
		document.formulaire.saisie_trimestre.options[0].text="        " 
		document.formulaire.saisie_trimestre.options[1].text="        ";
		document.formulaire.saisie_trimestre.options[2].text="        ";
		document.formulaire.saisie_trimestre.options[3].text="        " 
		document.formulaire.saisie_trimestre.options[4].text="        ";
		document.formulaire.saisie_trimestre.options[5].text="        ";
		document.formulaire.saisie_trimestre.options[6].text="        " 
		document.formulaire.saisie_trimestre.options[7].text="        ";
		document.formulaire.saisie_trimestre.options[8].text="        ";
		document.formulaire.saisie_trimestre.options[0].value="0";
		document.formulaire.saisie_trimestre.options[1].value="0";
		document.formulaire.saisie_trimestre.options[2].value="0";
		document.formulaire.saisie_trimestre.options[3].value="0";
		document.formulaire.saisie_trimestre.options[4].value="0";
		document.formulaire.saisie_trimestre.options[5].value="0";
		document.formulaire.saisie_trimestre.options[6].value="0";
		document.formulaire.saisie_trimestre.options[7].value="0";
		document.formulaire.saisie_trimestre.options[8].value="0";
	}
	if (document.formulaire.typetrisem.options[document.formulaire.typetrisem.selectedIndex].value == "periode") {
		document.formulaire.saisie_trimestre.options[0].text="1er";
		document.formulaire.saisie_trimestre.options[1].text="2ieme";
		document.formulaire.saisie_trimestre.options[2].text="3ieme";
		document.formulaire.saisie_trimestre.options[3].text="4ieme";
		document.formulaire.saisie_trimestre.options[4].text="5ieme";
		document.formulaire.saisie_trimestre.options[5].text="6ieme";
		document.formulaire.saisie_trimestre.options[6].text="7ieme";
		document.formulaire.saisie_trimestre.options[7].text="8ieme";
		document.formulaire.saisie_trimestre.options[8].text="9ieme";

		document.formulaire.saisie_trimestre.options[0].value="periode1";
		document.formulaire.saisie_trimestre.options[1].value="periode2";
		document.formulaire.saisie_trimestre.options[2].value="periode3";
		document.formulaire.saisie_trimestre.options[3].value="periode4";
		document.formulaire.saisie_trimestre.options[4].value="periode5";
		document.formulaire.saisie_trimestre.options[5].value="periode6";
		document.formulaire.saisie_trimestre.options[6].value="periode7";
		document.formulaire.saisie_trimestre.options[7].value="periode8";
		document.formulaire.saisie_trimestre.options[8].value="periode9";
	}
}


function trimes() {
	if (document.formulaire.typetrisem.options[document.formulaire.typetrisem.selectedIndex].value == "trimestre") {
		document.formulaire.saisie_trimestre.options[0].text="Trimestre 1";
		document.formulaire.saisie_trimestre.options[1].text="Trimestre 2";
		document.formulaire.saisie_trimestre.options[2].text="Trimestre 3";
//		document.formulaire.saisie_trimestre.options[3].text="";
		document.formulaire.saisie_trimestre.options[0].value="trimestre1";
		document.formulaire.saisie_trimestre.options[1].value="trimestre2";
		document.formulaire.saisie_trimestre.options[2].value="trimestre3";
//		document.formulaire.saisie_trimestre.options[3].value="";

	}
	if (document.formulaire.typetrisem.options[document.formulaire.typetrisem.selectedIndex].value == "semestre") {
		document.formulaire.saisie_trimestre.options[0].text="Semestre 1";
		document.formulaire.saisie_trimestre.options[1].text="Semestre 2";
		document.formulaire.saisie_trimestre.options[2].text="Annuel";
//		document.formulaire.saisie_trimestre.options[3].text="";
		document.formulaire.saisie_trimestre.options[0].value="trimestre1";
		document.formulaire.saisie_trimestre.options[1].value="trimestre2";
		document.formulaire.saisie_trimestre.options[2].value="annuel";
//		document.formulaire.saisie_trimestre.options[3].value="";
	}

	if (document.formulaire.typetrisem.options[document.formulaire.typetrisem.selectedIndex].value == "cycle") {
		document.formulaire.saisie_trimestre.options[0].text="Cycle 1";
		document.formulaire.saisie_trimestre.options[1].text="Cycle 2";
		document.formulaire.saisie_trimestre.options[2].text="Cycle 3";
		document.formulaire.saisie_trimestre.options[3].text="Cycle 4"; 
		document.formulaire.saisie_trimestre.options[0].value="cycle1";
		document.formulaire.saisie_trimestre.options[1].value="cycle2";
		document.formulaire.saisie_trimestre.options[2].value="cycle3";
		document.formulaire.saisie_trimestre.options[3].value="cycle4";
	}	
	if (document.formulaire.typetrisem.options[document.formulaire.typetrisem.selectedIndex].value == "annuel") {
		document.formulaire.saisie_trimestre.options[0].text="Annuel";
		document.formulaire.saisie_trimestre.options[1].text="      ";
		document.formulaire.saisie_trimestre.options[2].text="      ";
		document.formulaire.saisie_trimestre.options[3].text="      ";
		document.formulaire.saisie_trimestre.options[0].value="annuel";
		document.formulaire.saisie_trimestre.options[1].value="0";
		document.formulaire.saisie_trimestre.options[2].value="0";
		document.formulaire.saisie_trimestre.options[3].value="";
	}
	if (document.formulaire.typetrisem.options[document.formulaire.typetrisem.selectedIndex].value == "examen") {
		document.formulaire.saisie_trimestre.options[0].text="Examen Juin";
		document.formulaire.saisie_trimestre.options[1].text="Examen Décembre";
		document.formulaire.saisie_trimestre.options[2].text=" ";
		document.formulaire.saisie_trimestre.options[3].text="      ";
		document.formulaire.saisie_trimestre.options[0].value="exam_juin";
		document.formulaire.saisie_trimestre.options[1].value="exam_dec";
		document.formulaire.saisie_trimestre.options[2].value="0";
		document.formulaire.saisie_trimestre.options[3].value="";
	}
	if (document.formulaire.typetrisem.options[document.formulaire.typetrisem.selectedIndex].value == "0") {
		document.formulaire.saisie_trimestre.options[0].text="        " 
		document.formulaire.saisie_trimestre.options[1].text="        ";
		document.formulaire.saisie_trimestre.options[2].text="        ";
		document.formulaire.saisie_trimestre.options[3].text="      ";
		document.formulaire.saisie_trimestre.options[0].value="0";
		document.formulaire.saisie_trimestre.options[1].value="0";
		document.formulaire.saisie_trimestre.options[2].value="0";
		document.formulaire.saisie_trimestre.options[3].value="";
	}

}



function trimesan() {
	if (document.formulairean.typetriseman.options[document.formulairean.typetriseman.selectedIndex].value == "trimestre") {
		document.formulairean.saisie_trimestre.options[0].text="Trimestre 1";
		document.formulairean.saisie_trimestre.options[1].text="Trimestre 2";
		document.formulairean.saisie_trimestre.options[2].text="Trimestre 3";
		document.formulairean.saisie_trimestre.options[0].value="trimestre1";
		document.formulairean.saisie_trimestre.options[1].value="trimestre2";
		document.formulairean.saisie_trimestre.options[2].value="trimestre3";
	}
	if (document.formulairean.typetriseman.options[document.formulairean.typetriseman.selectedIndex].value == "semestre") {
		document.formulairean.saisie_trimestre.options[0].text="Semestre 1";
		document.formulairean.saisie_trimestre.options[1].text="Semestre 2";
		document.formulairean.saisie_trimestre.options[2].text=" ";
		document.formulairean.saisie_trimestre.options[0].value="trimestre1";
		document.formulairean.saisie_trimestre.options[1].value="trimestre2";
		document.formulairean.saisie_trimestre.options[2].value="trimestre3";
	}
	if (document.formulairean.typetriseman.options[document.formulairean.typetriseman.selectedIndex].value == "0") {
		document.formulairean.saisie_trimestre.options[0].text="        " 
		document.formulairean.saisie_trimestre.options[1].text="        ";
		document.formulairean.saisie_trimestre.options[2].text="        ";
		document.formulairean.saisie_trimestre.options[0].value="0";
		document.formulairean.saisie_trimestre.options[1].value="0";
		document.formulairean.saisie_trimestre.options[2].value="0";
	}
}

function trimesan2() {
	if (document.formulaire3.typetriseman.options[document.formulaire3.typetriseman.selectedIndex].value == "trimestre") {
		document.formulaire3.saisie_trimestre.options[0].text="Trimestre 1";
		document.formulaire3.saisie_trimestre.options[1].text="Trimestre 2";
		document.formulaire3.saisie_trimestre.options[2].text="Trimestre 3";
		document.formulaire3.saisie_trimestre.options[0].value="trimestre1";
		document.formulaire3.saisie_trimestre.options[1].value="trimestre2";
		document.formulaire3.saisie_trimestre.options[2].value="trimestre3";
	}
	if (document.formulaire3.typetriseman.options[document.formulaire3.typetriseman.selectedIndex].value == "semestre") {
		document.formulaire3.saisie_trimestre.options[0].text="Semestre 1";
		document.formulaire3.saisie_trimestre.options[1].text="Semestre 2";
		document.formulaire3.saisie_trimestre.options[2].text=" ";
		document.formulaire3.saisie_trimestre.options[0].value="trimestre1";
		document.formulaire3.saisie_trimestre.options[1].value="trimestre2";
		document.formulaire3.saisie_trimestre.options[2].value="trimestre3";
	}
	if (document.formulaire3.typetriseman.options[document.formulaire3.typetriseman.selectedIndex].value == "0") {
		document.formulaire3.saisie_trimestre.options[0].text="        " 
		document.formulaire3.saisie_trimestre.options[1].text="        ";
		document.formulaire3.saisie_trimestre.options[2].text="        ";
		document.formulaire3.saisie_trimestre.options[0].value="0";
		document.formulaire3.saisie_trimestre.options[1].value="0";
		document.formulairean.saisie_trimestre.options[2].value="0";
	}
}

//------------------------------------------------------------------------//
//------------------------------------------------------------------------//
