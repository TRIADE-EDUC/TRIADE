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

//fonction de validation générale de message  admin,profs,vie scolaire
function verif_message_envoi() {
     errfound=false;
   if (!ValidLongueur(document.formulaire.saisie_objet.value,3)){
      error(document.formulaire.saisie_objet,langfunc15); }
   if (!Validselect(document.formulaire.saisie_destinataire.options.selectedIndex)) { error2(langfunc16); }
  // if (document.form2.fichierjoint.value != "") { alert(document.form2.fichierjoint.value); error2("Vous devez valider votre fichier joint \n\n ou supprimer l'information dans le champs fichier.\n\n L'Equipe Triade."); }
   if (!errfound) {
	alert(document.formulaire.resultat1.value)
	document.formulaire.resultat.value=document.formulaire.resultat1.value;
	alert(document.formulaire.resultat.value)
	document.formulaire.submit();
   }
	return !errfound;
}
   //  if (!errfound) {
   //  	Switch();
   //  }
