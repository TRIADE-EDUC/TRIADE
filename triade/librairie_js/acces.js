/************************************************************
Last updated: 11.08.2004    par Taesch  Eric
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
//-------------------------------------------------------------------------//
// -----------------------------------------------------------------------//

//fonction de validation générale
function Validate() {
        var i = 0 ;
        bool=false;
     errfound = false;
   if (!ValidLongueur(document.inscripform.saisienom.value,1)){
      error(document.inscripform.saisienom,langfunc4+langfunc0); }
   if (!ValidLongueur(document.inscripform.saisieprenom.value,1)){
      error(document.inscripform.saisieprenom,langfunc5+langfunc0); }
   if (!ValidLongueur(document.inscripform.saisiepasswd.value,1)){
      error(document.inscripform.saisiepasswd,langfunc6+langfunc0); }

        // transforme la donnee en minuscule
        var nom=document.inscripform.saisienom.value;
        var lettre2= nom.toLowerCase();
        document.inscripform.saisienom.value = lettre2;
        var prenom=document.inscripform.saisieprenom.value;
        var lettre = prenom.toLowerCase() ;
        document.inscripform.saisieprenom.value =lettre ;

	if (!errfound) { document.inscripform.rien.disabled=true; }

return !errfound; /* vrai si il ya pas d'erreur */
}
