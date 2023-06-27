/************************************************************
Last updated: 2014    par SARL TRIADE
*************************************************************/
// variable globale signalant une erreur
var errfound = false;
var drapeau = 0 ;
//fonction de validation d'après la longueur de la chaîne
function ValidLongueur(item,len) {
   drapeau = 1;
   return (item.length >= len);
}

// affiche un message d'alerte
function error(elem, text) {
// abandon si erreur déjà signalée
   if (errfound) return;
//   window.alert(text);
   elem.select();
   elem.focus();
   errfound = true;
}
// affiche un message d'alerte
function error2(text) {
// abandon si erreur déjà signalée
   if (errfound) return;
   window.alert(text);
   errfound = true;
}

// validation d'un champ de select
function Validselect(item){
 if (item == 0) {
        return (false) ;
 }else {
        return (true) ;
        }
}
//-------------------------------------------------------------------------//
// -----------------------------------------------------------------------//
//fonction de validation générale
function verifAccesNote() {
        var i = 0 ;
        bool=false;
     	errfound = false;
	if (arguments[0] == "anneeScolaire") {
		if (!Validselect(document.formulaire.anneeScolaire.options.selectedIndex)) {
	                error2("Indiquez l'année scolaire, S.V.P. \n\n Service Triade");
	        }
	}
	if (!Validselect(document.formulaire.sClasseGrp.options.selectedIndex)) {
                error2("Indiquez une classe, S.V.P. \n\n Service Triade");
        }
	if (!Validselect(document.formulaire.sMat.options.selectedIndex)) {
                error2("Indiquez une matière, S.V.P. \n\n Service Triade");
        }
	var val=document.formulaire.sMat.options[document.formulaire.sMat.options.selectedIndex].value;
	if ( val == "") {
		error2("Indiquez une matière, S.V.P. \n\n Service Triade");
	}

return !errfound; /* vrai si il ya pas d'erreur */
}

function verifAccesNotebis() {
        var i = 0 ;
        bool=false;
     	errfound = false;
	if (arguments[0] == "anneeScolaire") {
		if (!Validselect(document.formulaire.anneeScolaire.options.selectedIndex)) {
	                error2("Indiquez l'année scolaire, S.V.P. \n\n Service Triade");
	        }
	}
	if (!Validselect(document.formulaire.sClasseGrp.options.selectedIndex)) {
                error2("Indiquez une classe, S.V.P. \n\n Service Triade");
        }
	var val=document.formulaire.sMat.options[document.formulaire.sMat.options.selectedIndex].value;
	if ( val == "") {
		error2("Indiquez une matière, S.V.P. \n\n Service Triade");
	}
return !errfound; /* vrai si il ya pas d'erreur */
}

// -----------------------------------------------------------------------//
function verifAccesNote2() {
        var i = 0 ;
        bool=false;
     errfound = false;
	if (arguments[0] == "anneeScolaire") {
		if (!Validselect(document.formulaire2.anneeScolaire.options.selectedIndex)) {
	                error2("Indiquez l'année scolaire, S.V.P. \n\n Service Triade");
	        }
	}
if (!Validselect(document.formulaire2.sClasseGrp.options.selectedIndex)) {
                error2("Indiquez une classe, S.V.P. \n\n Service Triade");
        }
if (!Validselect(document.formulaire2.sMat.options.selectedIndex)) {
                error2("Indiquez une matière, S.V.P. \n\n Service Triade");
        }

return !errfound; /* vrai si il ya pas d'erreur */
}


function verifAccesNote2bis() {
        var i = 0 ;
        bool=false;
     	errfound = false;
	if (arguments[0] == "anneeScolaire") {
		if (!Validselect(document.formulaire2.anneeScolaire.options.selectedIndex)) {
	                error2("Indiquez l'année scolaire, S.V.P. \n\n Service Triade");
	        }
	}
	if (!Validselect(document.formulaire2.sClasseGrp.options.selectedIndex)) {
                error2("Indiquez une classe, S.V.P. \n\n Service Triade");
        }
	var val=document.formulaire2.sMat.options[document.formulaire2.sMat.options.selectedIndex].value;
	if ( val == "") {
		error2("Indiquez une matière, S.V.P. \n\n Service Triade");
	}

return !errfound; /* vrai si il ya pas d'erreur */
}
// -----------------------------------------------------------------------//
function verifAccesNote3() {
        var i = 0 ;
        bool=false;
        errfound = false;
	if (arguments[0] == "anneeScolaire") {
		if (!Validselect(document.formulaire3.anneeScolaire.options.selectedIndex)) {
	                error2("Indiquez l'année scolaire, S.V.P. \n\n Service Triade");
	        }
	}
	if (!Validselect(document.formulaire3.sClasseGrp.options.selectedIndex)) {
                error2("Indiquez une classe, S.V.P. \n\n Service Triade");
        }
	if (!Validselect(document.formulaire3.sMat.options.selectedIndex)) {
                error2("Indiquez une matière, S.V.P. \n\n Service Triade");
        }

return !errfound; /* vrai si il ya pas d'erreur */
}
// -----------------------------------------------------------------------//
function verifAccesNote4() {
     	errfound = false;
	if (arguments[0] == "anneeScolaire") {
		if (!Validselect(document.formulaire4.anneeScolaire.options.selectedIndex)) {
	                error2("Indiquez l'année scolaire, S.V.P. \n\n Service Triade");
	        }
	}
	if (!Validselect(document.formulaire4.sClasseGrp.options.selectedIndex)) {
                error2("Indiquez une classe, S.V.P. \n\n Service Triade");
        }
	if (!Validselect(document.formulaire4.sMat.options.selectedIndex)) {
                error2("Indiquez une matière, S.V.P. \n\n Service Triade");
        }

return !errfound; /* vrai si il ya pas d'erreur */
}
// -----------------------------------------------------------------------//
function verifAccesNote5() {
     	errfound = false;
	if (arguments[0] == "anneeScolaire") {
		if (!Validselect(document.formulaire5.anneeScolaire.options.selectedIndex)) {
	                error2("Indiquez l'année scolaire, S.V.P. \n\n Service Triade");
	        }
	}
	if (!Validselect(document.formulaire5.sClasseGrp.options.selectedIndex)) {
                error2("Indiquez une classe, S.V.P. \n\n Service Triade");
        }
	return !errfound;
}

// -----------------------------------------------------------------------//

// -----------------------------------------------------------------------//
// -----------------------------------------------------------------------//
function verifAccesFiche() {
	var i = 0 ;
	errfound = false;
	if (!Validselect(document.formulaire.sClasseGrp.options.selectedIndex)) {
        	error2("Indiquez une classe, S.V.P. \n\n Service Triade");
        }
	return !errfound; /* vrai si il ya pas d'erreur */
}
// -----------------------------------------------------------------------//
function verifAccesFiche2() {
        var i = 0 ;
     	errfound = false;
	if (!Validselect(document.formulaire2.sClasseGrp.options.selectedIndex)) {
                error2("Indiquez une classe, S.V.P. \n\n Service Triade");
        }
	return !errfound; /* vrai si il ya pas d'erreur */
}
// -----------------------------------------------------------------------//

function chNote(i) {
document.form11.elements[i].value=document.form11.elements[i-1].options[document.form11.elements[i-1].selectedIndex].value;
}

function chNoteAj() {
document.form12.note.value=document.form12.notation.options[document.form12.notation.selectedIndex].value;
}

// -----------------------------------------------------------------------//

function verifCarnet() {
     	errfound = false;
	if (!Validselect(document.formulaire.idcarnet.options.selectedIndex)) {
                error2("Indiquez un carnet de suivi, S.V.P. \n\n Service Triade");
        }
return !errfound; /* vrai si il ya pas d'erreur */

}

function verifCarnet1() {
     	errfound = false;
	if (!Validselect(document.formulaire1.idcarnet.options.selectedIndex)) {
                error2("Indiquez un carnet de suivi, S.V.P. \n\n Service Triade");
        }
return !errfound; /* vrai si il ya pas d'erreur */

}

function verifCarnet2() {
     	errfound = false;
	if (!Validselect(document.formulaire2.idcarnet.options.selectedIndex)) {
                error2("Indiquez un carnet de suivi, S.V.P. \n\n Service Triade");
        }
return !errfound; /* vrai si il ya pas d'erreur */

}
