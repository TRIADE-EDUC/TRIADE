/************************************************************

Last updated: 08.07.2002    par Taesch  Eric
*************************************************************/
// variable globale signalant une erreur
var errfound = false;
//fonction de validation d'après la longueur de la chaîne
function Validlongueur(item,len) {
   return (item.length >= len);
}

// affiche un message d'alerte
function error9(elem, text) {
// abandon si erreur déjà signalée
   if (errfound) return;
   window.alert(text);
   elem.select();
   elem.focus();
   errfound = true;
}

function Validdate(nom) {
        var dernier = nom.lenght ;
        var slach1  = nom.charAt(2);
        var slach2  = nom.charAt(5);
        var jour = nom.substring(0,2);
        var mois = nom.substring(3,5);
        var caractere= nom.charAt(6);
        if (isNaN(caractere)) { return false }
        var annee = nom.substring(6,10);
        if (isNaN(jour)) { return false }
        if (isNaN(mois)) { return false }
        if (isNaN(annee)) { return false }
        if ((annee > 9999) || (jour > 31) || (mois > 12) || (slach1 != '/') || (slach2 != '/') ){
                return false
        }
        else {

                return true
        }
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
/*
//fonction de validation générale
function validedatestage() {	
     errfound = false;
if (document.formulaire.num.value.length < 1) {
	error9(document.formulaire.num,"Indiquer une valeur numerique, S.V.P.  \n\n Service Triade ");
}
if (isNaN(document.formulaire.num.value)) {
	error9(document.formulaire.num,"Indiquer une valeur numerique, S.V.P.  \n\n Service Triade ");
}
if (!Validdate(document.formulaire.debutdate.value)) {
      error9(document.formulaire.debutdate,"Indiquez la date du début de stage, S.V.P. \n\n Service Triade"); }
if (!Validdate(document.formulaire.findate.value)) {
      error9(document.formulaire.findate,"Indiquez la date de fin de stage, S.V.P. \n\n Service Triade"); }
if (!Validselect(document.formulaire.saisie_classe.options.selectedIndex)) {
      error2(langfunc11);
}
return !errfound; /* vrai si il ya pas d'erreur */
}
*/



function validedatestage2() {
     errfound = false;
if (document.formulaire.num.value.length < 1) {
	error9(document.formulaire.num,"Indiquer une valeur numerique, S.V.P.  \n\n Service Triade ");
}
if (isNaN(document.formulaire.num.value)) {
	error9(document.formulaire.num,"Indiquer une valeur numerique, S.V.P.  \n\n Service Triade ");
}
if (!Validdate(document.formulaire.debutdate.value)) {
      error9(document.formulaire.debutdate,"Indiquez la date du début de stage, S.V.P. \n\n Service Triade"); }
if (!Validdate(document.formulaire.findate.value)) {
      error9(document.formulaire.findate,"Indiquez la date de fin de stage, S.V.P. \n\n Service Triade"); }
}
return !errfound; /* vrai si il ya pas d'erreur */
}
