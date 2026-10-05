/************************************************************

Last updated: 09.07.2002    par Taesch  Eric
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


// validation de la classe
function ValidClasse(item){
 if (item == 0) {

        return (false) ;
 }else {
        return (true) ;
        }
}


//--------------------------------------------------------------------------//
//--------------------------------------------------------------------------//

//fonction de validation creation etablissement
function valide_creation() {
        var i = 0 ;
        bool=false;
     errfound = false;
       if (!ValidLongueur(document.formulaire.saisie_nom.value,2)){
      error(document.formulaire.saisie_nom,"Nom établissement refusé"); }
       if (!ValidLongueur(document.formulaire.saisie_contact.value,2)){
      error(document.formulaire.saisie_contact,"Nom contact refusé"); }
       if (!ValidLongueur(document.formulaire.saisie_telephone.value,2)){
      error(document.formulaire.saisie_telephone,"Téléphone  refusé"); }
       if (!ValidLongueur(document.formulaire.saisie_adresse.value,2)){
      error(document.formulaire.saisie_adresse,"Adresse refusé"); }
       if (!ValidLongueur(document.formulaire.saisie_code_postale.value,2)){
      error(document.formulaire.saisie_code_postale,"Code Postal refusé"); }
       if (!ValidLongueur(document.formulaire.saisie_ville.value,2)){
      error(document.formulaire.saisie_ville,"Ville refusé"); }
       if (!ValidLongueur(document.formulaire.saisie_license.value,2)){
      error(document.formulaire.saisie_license,"License refusé"); }
       if (!ValidLongueur(document.formulaire.saisie_repertoire.value,2)){
      error(document.formulaire.saisie_repertoire,"Répertoire refusé"); }
       if (!ValidLongueur(document.formulaire.saisie_base.value,2)){
      error(document.formulaire.saisie_base,"Base refusé"); }

return !errfound; /* vrai si il ya pas d'erreur */
}
