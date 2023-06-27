/************************************************************
Last updated: 11.08.2004    par Taesch  Eric
*************************************************************/

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
   window.alert(text);
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

//fonction de validation générale de creation admin,profs,vie scolaire
function valid_circulaire() {
	errfound=false;
   if (!ValidLongueur(document.formulaire.saisie_titre.value,3)){
      error(document.formulaire.saisie_titre,langfunc13); }
   if (!ValidLongueur(document.formulaire.saisie_ref.value,3)){
      error(document.formulaire.saisie_ref,lanfunc14); }
	// transforme la donnee en minuscule
	var nom=document.formulaire.saisie_titre.value;
	var lettre2= nom.toLowerCase();
	document.formulaire.saisie_titre.value = lettre2;
	var prenom=document.formulaire.saisie_ref.value;
        var lettre = prenom.toLowerCase() ;
        document.formulaire.saisie_ref.value =lettre ;
return !errfound; /* vrai si il ya pas d'erreur */
}
