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

//--------------------------------------------------------------------------//
//--------------------------------------------------------------------------//

//fonction de validation d établissement
function valide() {
        var i = 0 ;
      errfound = false;
       if (!ValidLongueur(document.formulaire.nom.value,2)){
      error(document.formulaire.nom,"Nom refusé"); }
       if (!ValidLongueur(document.formulaire.prenom.value,2)){
      error(document.formulaire.prenom,"Prénom refusé"); }
       if (!ValidLongueur(document.formulaire.mdp.value,2)){
      error(document.formulaire.mdp,"Mot de passe  refusé -- Minimum 6 caractères "); }

return !errfound; /* vrai si il ya pas d'erreur */
}


//fonction de validation d établissement
function valideMail() {
        var i = 0 ;
      errfound = false;
       if (!ValidLongueur(document.formulaire.email.value,5)){
      error(document.formulaire.email,"Indiquer une adresse E-mail, S.V.P. "); }

return !errfound; /* vrai si il ya pas d'erreur */
}

//--------------------------------------------------------------------------//
//--------------------------------------------------------------------------//
