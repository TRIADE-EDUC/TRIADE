/************************************************************

Last updated: 06.09.2002    par Taesch  Eric
*************************************************************/
// ---------------------------------------------------------------------------//
// ---------------------------------------------------------------------------//
// fonction imprimer
function print_affectation() {
        var ok=confirm(langfunc3);
        if (ok) {
                window.print();
        }
}

// Fonction pour toutes les verifications
// ------------------------------------------------//
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
function error1(elem, text) {
// abandon si erreur déjà signalée
   if (errfound) return;
   window.alert(text);
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
// ---------------------------------------------------------------------------//
// ---------------------------------------------------------------------------//
// fonction pour le test messagerie pour la gestion des classes
function affectation_classe() {
	errfound=false;
	if (!Validselect(document.formulaire.saisie_classe_envoi.options.selectedIndex)) {
   		error2(langfunc11); 
	}
	if (!ValidLongueur(document.formulaire.saisie_nb_matiere.value,1)) {
		error1(document.formulaire.saisie_nb_matiere,langfunc12); }
	if(isNaN(document.formulaire.saisie_nb_matiere.value)) {
		error1(document.formulaire.saisie_nb_matiere,langfunc13); }
	
   	return !errfound; /* vrai si il ya pas d'erreur */
}

function affectation_classe3() {
	errfound=false;
	if (!Validselect(document.formulaire.saisie_classe_envoi.options.selectedIndex)) {
   		error2(langfunc11); 
	}
   	return !errfound; /* vrai si il ya pas d'erreur */
}


function affectation_classe2() {
	errfound=false;
	if (!Validselect(document.formulaire.saisie_classe_envoi.options.selectedIndex)) {
   		error2(langfunc11); 
	}
	if (!ValidLongueur(document.formulaire.saisie_nb_matiere.value,1)) {
		error1(document.formulaire.saisie_nb_matiere,langfunc12); }
	if(isNaN(document.formulaire.saisie_nb_matiere.value)) {
		error1(document.formulaire.saisie_nb_matiere,langfunc12); }
	if (!Validselect(document.formulaire.anneeScolaire.options.selectedIndex)) {
   		error2(langfunc81); 
	}	
   	return !errfound; /* vrai si il ya pas d'erreur */
}

function openPopup() {
self.focus();
self.resizeTo( 700, screen.availHeight - window.screenTop + 40 );
}
