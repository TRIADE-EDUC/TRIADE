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

function Validselect1(item){
 if (item == 1) {
        return (false) ;
 }else {
        return (true) ;
        }
}
// ---------------------------------------------------------------------------//
// ---------------------------------------------------------------------------//

//fonction de validation générale de creation admin,profs,vie scolaire
function verifcommun(text) {
     var bool="nonok";
	errfound=false;
   if (!ValidLongueur(document.formulaire.saisie_creat_nom.value,1)){
      error(document.formulaire.saisie_creat_nom,langfunc4); }
   if (!ValidLongueur(document.formulaire.saisie_creat_prenom.value,1)){
      error(document.formulaire.saisie_creat_prenom,langfunc5); }
   if (!ValidLongueur(document.formulaire.saisie_creat_password.value,4)){
      error1(document.formulaire.saisie_creat_password,text); }
	
	// transforme la donnee en minuscule
	var nom=document.formulaire.saisie_creat_nom.value;
	var lettre2= nom.toLowerCase();
	document.formulaire.saisie_creat_nom.value = lettre2;
	var prenom=document.formulaire.saisie_creat_prenom.value;
        var lettre = prenom.toLowerCase() ;
        document.formulaire.saisie_creat_prenom.value =lettre ;

	return !errfound; /* vrai si il ya pas d'erreur */
}

function verifadresse() {
	errfound=false;
   if (!ValidLongueur(document.formulaire.saisie_nom.value,1)){
      error1(document.formulaire.saisie_nom,"Indiquer le nom de l'établissement"); }
   if (!ValidLongueur(document.formulaire.saisie_adresse.value,1)){
      error1(document.formulaire.saisie_adresse,"Indiquer l'adresse de l'établissement"); }
   if (!ValidLongueur(document.formulaire.saisie_postal.value,1)){
      error1(document.formulaire.saisie_postal,"Indiquer le code postal de l'établissement"); }
   if (!ValidLongueur(document.formulaire.saisie_ville.value,1)){
      error1(document.formulaire.saisie_ville,"Indiquer la ville de l'établissement"); }
   if (!ValidLongueur(document.formulaire.saisie_departement.value,1)){
      error1(document.formulaire.saisie_departement,"Indiquer le département de l'établissement"); }
   if (!ValidLongueur(document.formulaire.saisie_pays.value,1)){
      error1(document.formulaire.saisie_pays,"Indiquer le pays de l'établissement"); }
	return !errfound; /* vrai si il ya pas d'erreur */
}

// --------------------------------------------------------------------------//

function valideTab() {
	errfound=false;
	if (!Validselect(document.formulaire.saisie_classe.options.selectedIndex)) {
        error2(langfunc11); }	
	if (!Validselect(document.formulaire.typetrisem.options.selectedIndex)) {
        error2(langfunc24); }
	if (!Validselect(document.formulaire.annee_scolaire.options.selectedIndex)) {
        error2(langfunc81); }
	if (!errfound) { document.getElementById('attenteDiv').style.visibility='visible'; }
//	if (!errfound) { document.formulaire.rien.disabled=true }
//	if (!errfound) { document.formulairean.rien.disabled=true }
//	if (!errfound) { document.formulaire3.rien.disabled=true }
	return !errfound; /* vrai si il ya pas d'erreur */
}

function valideTab2() {
	errfound=false;
	if (!Validselect(document.formulairean.saisie_classe.options.selectedIndex)) {
        error2(langfunc11); }	
	if (!Validselect(document.formulairean.typetriseman.options.selectedIndex)) {
        error2(langfunc24); }
	if (!Validselect(document.formulairean.annee_scolaire.options.selectedIndex)) {
        error2(langfunc81); }
//	if (!errfound) { document.formulaire.rien.disabled=true }
//	if (!errfound) { document.formulairean.rien.disabled=true }
//	if (!errfound) { document.formulaire3.rien.disabled=true }
	return !errfound; /* vrai si il ya pas d'erreur */
}

function valideTab3() {
	errfound=false;
	if (!Validselect(document.formulaire3.saisie_classe.options.selectedIndex)) {
        error2(langfunc11); }	
	if (!Validselect(document.formulaire3.typetriseman.options.selectedIndex)) {
        error2(langfunc24); }
	if (!Validselect(document.formulaire3.annee_scolaire.options.selectedIndex)) {
        error2(langfunc81); }
//	if (!errfound) { document.formulaire.rien.disabled=true }
//	if (!errfound) { document.formulairean.rien.disabled=true }	
//	if (!errfound) { document.formulaire3.rien.disabled=true }
	return !errfound; /* vrai si il ya pas d'erreur */
}



function validateBadge() {
	errfound=false;
	if (!ValidLongueur(document.formulaire.contact.value,1)){
      		error1(document.formulaire.contact,"Contact refusé \n\n L'Equipe Triade."); }
   	if (!ValidLongueur(document.formulaire.email.value,1)){
      		error1(document.formulaire.email,"Email refusé \n\n L'Equipe Triade."); }
	if (!ValidLongueur(document.formulaire.adresse.value,1)){
      		error1(document.formulaire.adresse,"adresse refusé \n\n L'Equipe Triade."); }
	if (!ValidLongueur(document.formulaire.ccp.value,1)){
      		error1(document.formulaire.ccp,"Code Postal \n\n L'Equipe Triade."); }
	if (!ValidLongueur(document.formulaire.ville.value,1)){
      		error1(document.formulaire.ville,"Ville refusé \n\n L'Equipe Triade."); }
	return !errfound; /* vrai si il ya pas d'erreur */
}

// --------------------------------------------------------------------------//

function validrtdcumul() {
	errfound=false;
	if (!Validselect(document.formulaire.saisie_mois.options.selectedIndex)) {
        error2(langfunc78); }		
	if (!Validselect(document.formulaire.saisie_classe.options.selectedIndex)) {
        error2(langfunc11); }		
	return !errfound; 
}

// --------------------------------------------------------------------------//
function verifcreatclasse() {
     	errfound = false;
	if (!ValidLongueur(document.formulaire.saisie_creat_classe.value,2)){
      	error1(document.formulaire.saisie_creat_classe,langfunc19); }
   	if (document.formulaire.saisie_creat_classe.value.indexOf("\"") != -1) {
	error1(document.formulaire.saisie_creat_classe,langfunc54); }
	if (document.formulaire.saisie_creat_classe.value.indexOf("\'") != -1) {
	error1(document.formulaire.saisie_creat_classe,langfunc53); }
	return !errfound; /* vrai si il ya pas d'erreur */
}

function verifcreatequip() {
     	errfound = false;
   	if (!ValidLongueur(document.formulaire.saisie_creat_classe.value,2)){
	error1(document.formulaire.saisie_creat_classe,langfunc57); }
	return !errfound; /* vrai si il ya pas d'erreur */
}

function verifcreatesalle() {
     	errfound = false;
   	if (!ValidLongueur(document.formulaire.saisie_creat_classe.value,2)){
	error1(document.formulaire.saisie_creat_classe,langfunc58); }
	return !errfound; /* vrai si il ya pas d'erreur */
}

// ---------------------------------------------------------------------------//
// --------------------------------------------------------------------------//

//fonction de validation générale de creation suppleant
function verifsuppleant() {
     	errfound = false;
   	if (!ValidLongueur(document.formulaire.saisie_creat_nom.value,1)){
      	error(document.formulaire.saisie_creat_nom,langfunc4); }
   	if (!ValidLongueur(document.formulaire.saisie_creat_prenom.value,1)){
      	error(document.formulaire.saisie_creat_prenom,langfunc5); }
   	if (!ValidLongueur(document.formulaire.saisie_creat_password.value,6)){
      	error1(document.formulaire.saisie_creat_password,langfunc17); }
   	if (!Validselect(document.formulaire.saisie_remplacement.options.selectedIndex)) {
        error2(langfunc20); }
   	if (!ValidLongueur(document.formulaire.saisie_date_entree.value,10)){
      	error1(document.formulaire.saisie_date_entree,langfunc21) }
   	if (!ValidLongueur(document.formulaire.saisie_date_sortie.value,7)){
      	error1(document.formulaire.saisie_date_sortie,langfunc22); }
	if (errfound !=  true) {
        	for (a=0;a<=2;a++) {
                	if (document.formulaire.saisie_intitule[a].checked == true) {
                        	bool="ok";
                	}
        	}
        	if (bool != "ok" ) {
                	errfound=true;
	                alert(langfunc18);
        	}else {
                	errfound=false;
        	}
   	}
	// transforme la donnee en minuscule
	var nom=document.formulaire.saisie_creat_nom.value;
	var lettre2= nom.toLowerCase();
	document.formulaire.saisie_creat_nom.value = lettre2;
	var prenom=document.formulaire.saisie_creat_prenom.value;
        var lettre = prenom.toLowerCase() ;
        document.formulaire.saisie_creat_prenom.value =lettre ;
	return !errfound; /* vrai si il ya pas d'erreur */
}


function verifAnneeScolaire() {
	errfound = false;
	if (!Validselect(document.formulaire.annee_scolaire.options.selectedIndex)) {
    	error2(langfunc81); }
	return !errfound;
}

// ---------------------------------------------------------------------------//
// ---------------------------------------------------------------------------//
function valide_choix_projo() {
	errfound = false;
	if (!Validselect(document.formulaire.anneeScolaire.options.selectedIndex)) {
    	error2(langfunc81); }
	if (!Validselect(document.formulaire.saisie_classe.options.selectedIndex)) {
    	error2(langfunc11); }
	if (!Validselect(document.formulaire.saisie_trimestre.options.selectedIndex)) {
    	error2(langfunc24); }
	if (errfound == false) {
    		document.formulaire.supp.value="Veuillez patienter S.V.P. ";
    		document.formulaire.supp.disabled=true;
	}
	return !errfound; /* vrai si il ya pas d'erreur */
}
// ---------------------------------------------------------------------------//
function valide_choix_projo2() {
	errfound = false;
	if (!Validselect(document.formulaire2.saisie_classe.options.selectedIndex)) {
    	error2(langfunc11); }
	if (!Validselect(document.formulaire2.saisie_trimestre.options.selectedIndex)) {
    	error2(langfunc24); }
	if (errfound == false) {
    		document.formulaire2.supp.value="Veuillez patienter S.V.P. ";
    		document.formulaire2.supp.disabled=true;
	}
	return !errfound; /* vrai si il ya pas d'erreur */
}
// ---------------------------------------------------------------------------//

// ---------------------------------------------------------------------------//
//fonction de validation de creation de sous matiere
function valide_sous_matiere() {
	errfound = false;
	if (!Validselect(document.formulaire.saisie_matiere.options.selectedIndex)) {
	error2(langfunc23); }
	if (!ValidLongueur(document.formulaire.saisie_nom_matiere.value,2)){
      	error1(document.formulaire.saisie_nom_matiere,langfunc25); }
	return !errfound; /* vrai si il ya pas d'erreur */
}
// ---------------------------------------------------------------------------//
// ---------------------------------------------------------------------------//
//fonction de consultation classe
function valide_consul_classe() {
	errfound = false;
	if (!Validselect(document.formulaire.saisie_classe.options.selectedIndex)) {
	error2(langfunc11); }
	return !errfound; /* vrai si il ya pas d'erreur */
}

function validVignette1() {
	errfound = false;
	if (!Validselect(document.formulaire.saisie_classe.options.selectedIndex)) {
    		error2(langfunc11); }
	if (!Validselect(document.formulaire.id_vignette.options.selectedIndex)) {
    		error2(langfunc79); }
	return !errfound; /* vrai si il ya pas d'erreur */
}

function validVignette2() {
	errfound = false;
	if (!Validselect(document.formulaire2.saisie_type.options.selectedIndex)) {
    		error2(langfunc80); }
	if (!Validselect(document.formulaire2.id_vignette.options.selectedIndex)) {
    		error2(langfunc79); }
	return !errfound; /* vrai si il ya pas d'erreur */
}

function valide_consul_membre() {
errfound = false;
if (!Validselect(document.formulaire1.membre.options.selectedIndex)) {
    error2("Indiquer un membre, S.V.P. \n L'équipe Triade"); }
return !errfound; /* vrai si il ya pas d'erreur */
}

function valide_consul_classe1() {
errfound = false;
if (!Validselect(document.formulaire1.saisie_classe.options.selectedIndex)) {
    error2(langfunc11); }
return !errfound; /* vrai si il ya pas d'erreur */
}

function valide_consul_classe2() {
errfound = false;
if (!Validselect(document.formulaire_5.saisie_groupe.options.selectedIndex)) {
    error2(langfunc30); }
return !errfound; /* vrai si il ya pas d'erreur */
}

function valide_consul_classe3() {
errfound = false;
if (!Validselect(document.formulaire.typetrisem.options.selectedIndex)) {
    error2("Indiquer le trimestre ou semestre, S.V.P. \n\n L'Equipe Triade."); }
if (!Validselect(document.formulaire.saisie_trimestre.options[document.formulaire.saisie_trimestre.options.selectedIndex].value != 0)) {
    error2("Indiquer un élément de la liste, S.V.P. \n\n L'Equipe Triade."); }
return !errfound; /* vrai si il ya pas d'erreur */
}

function valide_consul_classe22() {
errfound = false;
if (!Validselect(document.formulaire_55.saisie_etude.options.selectedIndex)) {
    error2(langfunc76); }
return !errfound; /* vrai si il ya pas d'erreur */
}


function valide_consul_classe4() {
errfound = false;
if (!Validselect(document.formulaire.saisie_classe.options.selectedIndex)) {
    error2(langfunc11); }
if (!Validselect(document.formulaire.typetrisem.options.selectedIndex)) {
    error2("Indiquer le trimestre ou semestre, S.V.P. \n\n L'Equipe Triade."); }
return !errfound; /* vrai si il ya pas d'erreur */
}

function valide_supp_choix(objet,message) {
errfound = false;

if (!Validselect(eval("document.formulaire."+objet+".options.selectedIndex"))) {
    error2( langfuncchoix + message + langfunc0bis); }
return !errfound; /* vrai si il ya pas d'erreur */

}

function valide_choix_pers(message) {
	errfound = false;
	if (!Validselect(document.formulaire1.saisie_pers.options.selectedIndex)) {
	error2( langfuncchoix + message + langfunc0bis); }
	return !errfound; /* vrai si il ya pas d'erreur */
}

function valide_choix_ens(message) {
	errfound = false;
	if (!Validselect(document.formulaire1.saisie_idprof.options.selectedIndex)) {
	error2( langfuncchoix + message + langfunc0bis); }
	return !errfound; /* vrai si il ya pas d'erreur */
}

function validecompetence() {
	errfound = false;
	if (!Validselect(eval("document.formulaire.notation.options.selectedIndex"))) {
    	error2("Indiquer le type de notation, S.V.P. \n\n L'Equipe Triade."); }
	if (!Validselect(eval("document.formulaire.periode.options.selectedIndex"))) {
    	error2("Indiquer la période, S.V.P. \n\n L'Equipe Triade."); }
	return !errfound; /* vrai si il ya pas d'erreur */
}


// ---------------------------------------------------------------------------//
// ---------------------------------------------------------------------------//
//fonction de	recherche  complexe
function valide_recherche_complexe_0() {
	errfound = false;
   	if (!Validselect(document.formulaire1.saisie_nb_recherche.options.selectedIndex)){
      	error2(langfunc26); }
	return !errfound; /* vrai si il ya pas d'erreur */
}
//fonction de	recherche  complexe
function valide_recherche_complexe() {
	errfound = false;
   	if (!Validselect(document.formulaire.saisie_recherche.options.selectedIndex)){
      	error2(langfunc26); }
	return !errfound; /* vrai si il ya pas d'erreur */
}
//fonction de	recherche  complexe_2
function valide_recherche_complexe_2() {
	errfound = false;
   	if (!Validselect(document.formulaire.saisie_nombre.options.selectedIndex)){
      	error2(langfunc27); }
	return !errfound; /* vrai si il ya pas d'erreur */
}
// ---------------------------------------------------------------------------//
// ---------------------------------------------------------------------------//
// ---------------------------------------------------------------------------//
//fonction de	recherche  eleve
function valide_recherche_eleve() {
	errfound = false;
   	if (!ValidLongueur(document.formulaire.saisie_nom_eleve.value,2)){
      	error1(document.formulaire.saisie_nom_eleve,langfunc28); }
	return !errfound; /* vrai si il ya pas d'erreur */
}
// ---------------------------------------------------------------------------//
// ---------------------------------------------------------------------------//
//fonction de	recherche  eleve
function valide_recherche_eleve_1() {
	errfound = false;
   	if (!ValidLongueur(document.formulaire_1.saisie_nom_eleve.value,2)){
     	error1(document.formulaire_1.saisie_nom_eleve,langfunc28); }
	return !errfound; /* vrai si il ya pas d'erreur */
}
// ---------------------------------------------------------------------------//
// ---------------------------------------------------------------------------//
//fonction de	recherche  eleve
function valide_recherche_eleve_2() {
	errfound = false;
   	if (!ValidLongueur(document.formulaire_2.saisie_nom_eleve.value,2)){
      	error1(document.formulaire_2.saisie_nom_eleve,langfunc28); }
	return !errfound; /* vrai si il ya pas d'erreur */
}
// ---------------------------------------------------------------------------//
// ---------------------------------------------------------------------------//
//fonction de	recherche  eleve
function valide_recherche_eleve_3() {
	errfound = false;
   	if (!ValidLongueur(document.formulaire_3.saisie_nom_eleve.value,2)){
      	error1(document.formulaire_3.saisie_nom_eleve,langfunc28); }
	return !errfound; /* vrai si il ya pas d'erreur */
}
// ---------------------------------------------------------------------------//
// ---------------------------------------------------------------------------//
//fonction de	recherche  eleve
function valide_recherche_eleve_4() {
	errfound = false;
   	if (!ValidLongueur(document.formulaire_4.saisie_nom_eleve.value,2)){
      	error1(document.formulaire_4.saisie_nom_eleve,langfunc28); }
	return !errfound; /* vrai si il ya pas d'erreur */
}
// ---------------------------------------------------------------------------//
// ---------------------------------------------------------------------------//
//fonction de validation de creation matiere
function verifcreatmatiere() {
     	errfound = false;
	if (!ValidLongueur(document.formulaire.saisie_creat_matiere.value,2)){
      	error1(document.formulaire.saisie_creat_matiere,langfunc29); }
	return !errfound; /* vrai si il ya pas d'erreur */
}

// ---------------------------------------------------------------------------//
// ---------------------------------------------------------------------------//
function validecreatgroupe() {
   	errfound = false;
   	if (!ValidLongueur(document.formulaire.saisie_intitule.value,2)){
   	error1(document.formulaire.saisie_intitule,langfunc30); }
	if (!Validselect(document.formulaire.annee_scolaire.options.selectedIndex)){
      	error2(langfunc81); }
	return !errfound; /* vrai si il ya pas d'erreur */
}


function validecreatgroupe3() {
   	errfound = false;
   	if (!ValidLongueur(document.formulaire2.saisie_intitule.value,2)){
   	error1(document.formulaire2.saisie_intitule,langfunc30); }
	if (!Validselect(document.formulaire2.annee_scolaire.options.selectedIndex)){
      	error2(langfunc81); }
	return !errfound; /* vrai si il ya pas d'erreur */
}

function validepass(text) {
   	errfound = false;
   	if (!ValidLongueur(document.formulaire.pass.value,4)){
      	error1(document.formulaire.pass,text); }
	return !errfound; /* vrai si il ya pas d'erreur */
}

// ---------------------------------------------------------------------------//
// ---------------------------------------------------------------------------//
function validecreatgroupe2bis() {
	errfound=false;
	if (document.formulaire.aucun_eleve.checked != true) {
		if (!ValidLongueur(document.formulaire.saisie_eleve.value,2)){
   		error2(langfunc31); }
	}
	return !errfound;
}

function validecreatgroupe2() {
	errfound=false;
	if (!ValidLongueur(document.formulaire.saisie_eleve.value,2)){
   	error2(langfunc31); }
	return !errfound;
}

function click_eleve() {
	document.formulaire.saisie_eleve.value="ok";

}

// ---------------------------------------------------------------------------//
// ---------------------------------------------------------------------------//
function valid_calendrier() {
	errfound=false;
   	if (!ValidLongueur(document.formulaire.saisie_dst1.value,2)){
   	error1(document.formulaire.saisie_dst1,langfunc32); }
	return !errfound;
}
// ---------------------------------------------------------------------------//
// ---------------------------------------------------------------------------//
function valid_cal_evnt() {
	errfound=false;
   	if (!ValidLongueur(document.formulaire.saisieevenement1.value,2)){
   	error1(document.formulaire.saisieevenement1,langfunc33); }
	return !errfound;
}
// ---------------------------------------------------------------------------//
// --------------------------------------------------------------------------//
function ValidDate(nom) {
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
//----------------------------------------------------------------------------//
//----------------------------------------------------------------------------//
function valide_creat_eleve() {
     errfound = false;
   if (!ValidLongueur(document.formulaire.saisie_nom.value,2)){
      error1(document.formulaire.saisie_nom,langfunc4); }
   if (!ValidLongueur(document.formulaire.saisie_prenom.value,2)){
      error1(document.formulaire.saisie_prenom,langfunc5); }
   if (!Validselect(document.formulaire.annee_scolaire.options.selectedIndex)) {
      error2(langfunc81); }
   if (!Validselect(document.formulaire.saisie_classe.options.selectedIndex)) {
      error2(langfunc11); }
   if (!ValidLongueur(document.formulaire.saisie_date_naissance.value,10)){
      error1(document.formulaire.saisie_date_naissance,langfunc35); }
   if (!ValidDate(document.formulaire.saisie_date_naissance.value)) {
      error1(document.formulaire.saisie_date_naissance,langfunc35); }
	// transforme la donnee en minuscule
	var nom=document.formulaire.saisie_nom.value;
	var lettre2= nom.toLowerCase();
	document.formulaire.saisie_nom.value = lettre2;
	var prenom=document.formulaire.saisie_prenom.value;
        var lettre = prenom.toLowerCase() ;
        document.formulaire.saisie_prenom.value =lettre ;

return !errfound; /* vrai s'il ya pas d'erreur */
}


function valide_creat_eleve2() {
     errfound = false;
   if (!ValidLongueur(document.formulaire.saisie_nom.value,2)){
      error1(document.formulaire.saisie_nom,langfunc4); }
   if (!ValidLongueur(document.formulaire.saisie_prenom.value,2)){
      error1(document.formulaire.saisie_prenom,langfunc5); }
   if (!Validselect(document.formulaire.saisie_classe.options.selectedIndex)) {
      error2(langfunc11); }
   if (!ValidLongueur(document.formulaire.saisie_date_naissance.value,10)){
      error1(document.formulaire.saisie_date_naissance,langfunc35); }
   if (!ValidDate(document.formulaire.saisie_date_naissance.value)) {
      error1(document.formulaire.saisie_date_naissance,langfunc35); }
   if (!ValidLongueur(document.formulaire.saisie_passwd_eleve.value,2)){
      error1(document.formulaire.saisie_passwd_eleve,langfunc36); }
	// transforme la donnee en minuscule
	var nom=document.formulaire.saisie_nom.value;
	var lettre2= nom.toLowerCase();
	document.formulaire.saisie_nom.value = lettre2;
	var prenom=document.formulaire.saisie_prenom.value;
        var lettre = prenom.toLowerCase() ;
        document.formulaire.saisie_prenom.value =lettre ;

return !errfound; /* vrai s'il ya pas d'erreur */
}


function valide_creat_eleve3() {
     errfound = false;
   if (!ValidLongueur(document.formulaire.saisie_nom.value,2)){
      error1(document.formulaire.saisie_nom,langfunc4); }
   if (!ValidLongueur(document.formulaire.saisie_prenom.value,2)){
      error1(document.formulaire.saisie_prenom,langfunc5); }
   if (!Validselect(document.formulaire.saisie_classe.options.selectedIndex)) {
      error2(langfunc11); }
   if (!Validselect(document.formulaire.annee_scolaire.options.selectedIndex)) {
      error2(langfunc81); }
   if (!ValidLongueur(document.formulaire.saisie_date_naissance.value,10)){
      error1(document.formulaire.saisie_date_naissance,langfunc35); }
   if (!ValidDate(document.formulaire.saisie_date_naissance.value)) {
      error1(document.formulaire.saisie_date_naissance,langfunc35); }
   if (!ValidLongueur(document.formulaire.saisie_passwd_eleve.value,2)){
      error1(document.formulaire.saisie_passwd_eleve,langfunc36); }
   if (!ValidLongueur(document.formulaire.saisie_email_eleve.value,4)){
      error1(document.formulaire.saisie_email_eleve,"Email refusé \n\n L'Equipe Triade."); }
	// transforme la donnee en minuscule
	var nom=document.formulaire.saisie_nom.value;
	var lettre2= nom.toLowerCase();
	document.formulaire.saisie_nom.value = lettre2;
	var prenom=document.formulaire.saisie_prenom.value;
        var lettre = prenom.toLowerCase() ;
        document.formulaire.saisie_prenom.value =lettre ;

return !errfound; /* vrai s'il ya pas d'erreur */
}

//----------------------------------------------------------------------------//
//----------------------------------------------------------------------------//
function valide_modif_eleve() {
     errfound = false;
   if (!ValidLongueur(document.formulaire.saisie_nom.value,2)){
      error1(document.formulaire.saisie_nom,langfunc4); }
   if (!ValidLongueur(document.formulaire.saisie_prenom.value,2)){
      error1(document.formulaire.saisie_prenom,langfunc5); }
   //if (!ValidLongueur(document.formulaire.saisie_lv1.value,2)){
   //   error1(document.formulaire.saisie_lv1,langfunc34); }
   if (!ValidLongueur(document.formulaire.saisie_date_naissance.value,10)){
      error1(document.formulaire.saisie_date_naissance,langfunc35); }
   if (!ValidDate(document.formulaire.saisie_date_naissance.value)) {
      error1(document.formulaire.saisie_date_naissance,langfunc35); }
   if (!ValidLongueur(document.formulaire.saisie_passwd.value,2)){
      error1(document.formulaire.saisie_passwd,langfunc36); }

	// transforme la donnee en minuscule
	var nom=document.formulaire.saisie_nom.value;
	var lettre2= nom.toLowerCase();
	document.formulaire.saisie_nom.value = lettre2;
	var prenom=document.formulaire.saisie_prenom.value;
        var lettre = prenom.toLowerCase() ;
        document.formulaire.saisie_prenom.value =lettre ;

return !errfound; /* vrai s'il ya pas d'erreur */
}

//----------------------------------------------------------------------------//
//----------------------------------------------------------------------------//
// fonction de validation pour le trimestre
function ValidCaractere(nom) {
        var dernier = nom.lenght ;
        var milieu  = nom.charAt(10);
        var slach1  = nom.charAt(2);
        var slach2  = nom.charAt(5);
        var slach3  = nom.charAt(13);
        var slach4  = nom.charAt(16);
        var jour = nom.substring(0,2);
        var mois = nom.substring(3,5);
        var annee = nom.substring(6,10);
        var jour2 = nom.substring(11,13);
        var mois2 = nom.substring(14,16);
        var annee2 = nom.substring(17,21);
	var caractere2= nom.charAt(6);
        if (isNaN(caractere2)) { return false }
	var caractere= nom.charAt(17);
        if (isNaN(caractere)) { return false }
        if (isNaN(jour)) { return false }
        if (isNaN(mois)) { return false }
        if (isNaN(annee)) { return false }
        if (isNaN(jour2)) { return false }
        if (isNaN(mois2)) { return false }
        if (isNaN(annee2)) { return false }
        if ((annee2 < annee) || (annee < 2000) || (jour2 > 31) || (mois2 > 12) || (jour > 31) || (mois > 12) || (milieu != '-') || (slach1 != '/') || (slach2 != '/') || (slach3 != '/') || (slach4 != '/')){
                return false
        }
        else {
		var date_1=annee+mois+jour;
		var date_2=annee2+mois2+jour2;
		if (date_2 <= date_1) { 
			alert(langfunc37);
			return false 
		}else { 
			return true 
		}
        }
}

function valide_trimestre() {
     errfound = false;

   if (!ValidLongueur(document.formulaire.trimestre1.value,21)){
      error1(document.formulaire.trimestre1,langfunc38);}

   if (!ValidCaractere(document.formulaire.trimestre1.value)) {
      error1(document.formulaire.trimestre1,langfunc38);}

   if (!ValidLongueur(document.formulaire.trimestre2.value,21)){
      error1(document.formulaire.trimestre2,langfunc39); }

   if (!ValidCaractere(document.formulaire.trimestre2.value)) {
      error1(document.formulaire.trimestre2,langfunc39); }

   if (document.formulaire.semestre.checked!="1") { 
	   if (!ValidLongueur(document.formulaire.trimestre3.value,21)){
      		error1(document.formulaire.trimestre3,langfunc40); }
	   if (!ValidCaractere(document.formulaire.trimestre3.value)) {
      		error1(document.formulaire.trimestre3,langfunc40); }
   }

return !errfound; /* vrai si il ya pas d'erreur */
}

//----------------------------------------------------------------------------//
//----------------------------------------------------------------------------//
// Deplacer des l element d une liste a une autre
function Deplacer(l1,l2) {
	if (l1.options.selectedIndex>=0) {
		o=new Option(l1.options[l1.options.selectedIndex].text,l1.options[l1.options.selectedIndex].value);
		l2.options[l2.options.length]=o;
		l1.options[l1.options.selectedIndex]=null;
	}else{
		alert(langfunc41);
	}
}
//----------------------------------------------------------------------------//
//----------------------------------------------------------------------------//

function ValidCaractereDispence(nom) {
        var slach1  = nom.charAt(2);
        var slach2  = nom.charAt(5);
        var jour = nom.substring(0,2);
        var mois = nom.substring(3,5);
        var annee = nom.substring(6,10);
	var caractere= nom.charAt(6);
        if (isNaN(caractere)) { return false }
        if (isNaN(jour)) { return false }
        if (isNaN(mois)) { return false }
        if (isNaN(annee)) { return false }
	if ((annee < 2000) || (jour > 31) || (mois > 12) || (slach1 != '/') || (slach2 != '/')){
                return false
        }
        else {
                return true
        }
}

function verif_date(date1,date2) {
        var jour = date1.substring(0,2);
        var mois = date1.substring(3,5);
        var annee = date1.substring(6,10);
        var jour_1 = date2.substring(0,2);
        var mois_1 = date2.substring(3,5);
        var annee_1 = date2.substring(6,10);
	var date_1=annee+mois+jour;
	var date_2=annee_1+mois_1+jour_1;
	if (date_2 < date_1) { return false }else { return true }

}

function Valide_dispense(id) {
        errfound = false;
	// ------------- //
	form2="document.formulaire_"+id+".saisie_date_debut_"+id+".value";
	date_debut=eval(form2);
	form3="document.formulaire_"+id+".saisie_date_debut_"+id;
	date_debut_P=eval(form3);
   	if (!ValidCaractereDispence(date_debut)) {
        error1(date_debut_P,langfunc42); }
	// ------------- //
	form4="document.formulaire_"+id+".saisie_date_fin_"+id+".value";
	date_fin=eval(form4);
	form5="document.formulaire_"+id+".saisie_date_fin_"+id;
	date_fin_P=eval(form5);
   	if (!ValidCaractereDispence(date_fin)) {
      error1(date_fin_P,langfunc42); }
	// ------------- //
   	if (!verif_date(date_debut,date_fin)) {
      error1(date_fin_P,langfunc43); }
	// ------------- //
form6="document.formulaire_"+id+".saisie_matiere_"+id+".options.selectedIndex";
matiere=eval(form6);
  if (!Validselect(matiere)) {
      error2(langfunc23bis); }
	// ------------- //
form8="document.formulaire_"+id+".saisie_jour_"+id+"_0.options.selectedIndex";
jour=eval(form8);
  if (!Validselect(jour)) {
      error2(langfunc44); }
	// ------------- //
	form7="document.formulaire_"+id+".saisie_heure_"+id+"_0.value";
        heure1=eval(form7);
   	if (!ValidLongueur(heure1,2)){
      error2(langfunc45); }

	return !errfound; /* vrai si il ya pas d'erreur */
}

function validestageeleve() {
	errfound = false;
   	if (!Validselect(document.formulaire.idstage.options.selectedIndex)){
      	error2(langfunc55); }
	if (!Validselect(document.formulaire.ident.options.selectedIndex)){
      	error2(langfunc56); }

	if (ValidLongueur(document.formulaire.date.value,1)){
		if (!ValidDate(document.formulaire.date.value)) {
      	error1(document.formulaire.date,langfunc42); }
	}

	return !errfound; /* vrai si il ya pas d'erreur */
}


function validestageelevemodif() {
	errfound = false;
	if (ValidLongueur(document.formulaire.date.value,1)){
		if (!ValidDate(document.formulaire.date.value)) {
      	error1(document.formulaire.date,langfunc42); }
	}
	return !errfound; /* vrai si il ya pas d'erreur */
}

function  valideProfP() {
	errfound = false;
	if (!ValidDate(document.formulaire.dateDebut.value)) {
      	error1(document.formulaire.dateDebut,langfunc42); }
	if (!ValidDate(document.formulaire.dateFin.value)) {
     	error1(document.formulaire.dateFin,langfunc42); }
   	if (!ValidLongueur(document.formulaire.commentaire.value,4)){
      	error1(document.formulaire.commentaire,langfunc46);}
return !errfound; /* vrai si il ya pas d'erreur */
}

function ValidCaractereTime(nom) {
        var deuxpoint  = nom.charAt(2);
        var heure = nom.substring(0,2);
        var minute = nom.substring(3,5);
        if (isNaN(heure)) { return true }
        if (isNaN(minute)) { return true }
        if ((heure > 24) || (minute > 60) || (deuxpoint != ':')) {
                return true
        }
        else {
                return false
        }
}

function validresa2() {
	errfound = false;
	var opt=document.formulaire.saisie_equip.options.selectedIndex;
   	if (document.formulaire.saisie_equip.options[opt].value == "choix" ) {
        error2(langfunc61); }
	if (ValidLongueur(document.formulaire.saisie_date.value,1)){
		if (!ValidDate(document.formulaire.saisie_date.value)) {
      	error1(document.formulaire.saisie_date,langfunc42); }
	}
	if (ValidCaractereTime(document.formulaire.saisie_heure1.value)) {
	error1(document.formulaire.saisie_heure1,langfunc60); }
	if (ValidCaractereTime(document.formulaire.saisie_heure2.value)) {
	error1(document.formulaire.saisie_heure2,langfunc60); }

	
return !errfound; /* vrai si il ya pas d'erreur */
}


function validEntretienprof() {
	errfound = false;
	if (ValidCaractereTime(document.formulaire.heure.value)) {
	error1(document.formulaire.heure,"Indiquez la durée sous la forme.  \n\n Syntaxe : hh:mm \n ex: 17:00 ou 01:00"); }
	return !errfound; /* vrai si il ya pas d'erreur */
}

function validresa() {
	errfound = false;
	var opt=document.formulaire.saisie_equip.options.selectedIndex;
   	if (document.formulaire.saisie_equip.options[opt].value == "choix" ) {
        error2(langfunc59); }
	if (ValidLongueur(document.formulaire.saisie_date.value,1)){
		if (!ValidDate(document.formulaire.saisie_date.value)) {
      	error1(document.formulaire.saisie_date,langfunc42); }
	}
	if (ValidCaractereTime(document.formulaire.saisie_heure1.value)) {
	error1(document.formulaire.saisie_heure1,langfunc60); }
	if (ValidCaractereTime(document.formulaire.saisie_heure2.value)) {
	error1(document.formulaire.saisie_heure2,langfunc60); }

	
return !errfound; /* vrai si il ya pas d'erreur */
}

function validecreatetude() {
	errfound = false;
	if (!Validselect(document.formulaire.saisie_etude.options.selectedIndex)){
        error2("Indiquez une étude, S.V.P.  \n\n Service Triade  "); }
	return !errfound; /* vrai si il ya pas d'erreur */
}

function valide_classe() {
	errfound = false;
	if (!Validselect(document.formulaire.saisie_classe.options.selectedIndex)){
        error2("Indiquez une classe, S.V.P.  \n\n Service Triade  "); }
	return !errfound; /* vrai si il ya pas d'erreur */
}

function valideetude() {
	errfound = false;
	bool="pasok";
	if (!ValidLongueur(document.formulaire.nometude.value,1)) {
        	error1(document.formulaire.nometude,"Indiquer un nom à l'étude, S.V.P.  \n\n Service Triade "); }
        if (document.formulaire.jour1.checked == true) { bool="ok"; }
        if (document.formulaire.jour2.checked == true) { bool="ok"; }
        if (document.formulaire.jour3.checked == true) { bool="ok"; }
        if (document.formulaire.jour4.checked == true) { bool="ok"; }
        if (document.formulaire.jour5.checked == true) { bool="ok"; }
        if (document.formulaire.jour6.checked == true) { bool="ok"; }
	if (bool != "ok" ) { error2("Choisissez un jour de la semaine \n\n Service Triade "); }
	if (ValidCaractereTime(document.formulaire.heure_etude.value)) {
      		error1(document.formulaire.heure_etude,"Indiquez l'heure de l'étude, S.V.P. \n\n Service Triade"); }
return !errfound; /* vrai si il ya pas d'erreur */
}

function verifimpbull() {
	errfound = false;
	if (document.formulaire.saisie_classe.value <= 0){
	error2("Indiquez une classe, S.V.P.  \n\n Service Triade  "); }
	if (!Validselect(document.formulaire.typetrisem.options.selectedIndex)){
	error2("Indiquez le choix trimestre ou semestre, S.V.P.  \n\n Service Triade  "); }
	if (!Validselect(document.formulaire.anneeScolaire.options.selectedIndex)){
	error2(langfunc81+" \n\n Service Triade  "); }
	if (!Validselect(document.formulaire.typebull.options.selectedIndex)){
	error2("Indiquez le choix du bulletin, S.V.P.  \n\n Service Triade  "); }
	return !errfound; /* vrai si il ya pas d'erreur */	
}

function bulletinperso() {
	if (!Validselect1(document.formulaire.typebull.options.selectedIndex)){
		document.getElementById('bullperso').innerHTML="<center><font class='T2'>[ <a href='http://support.triade-educ.com/support/bulletinPerso.php?type=bull' target='_blank' ><font class='T2'><b>Cliquez ici pour personnaliser votre bulletin.</b></font></a> ]</center></font><br><br>";
	}else{
		document.getElementById('bullperso').innerHTML="";
	}
}

function bulletinperso1() {
	if (!Validselect1(document.formulaire.type_periode.options.selectedIndex)){
		open('http://support.triade-educ.com/support/bulletinPerso.php?type=perio','_blank','');
	}
}



function validentretien() {
	errfound = false;
	if (ValidLongueur(document.formulaire.saisiedate.value,1)){
		if (!ValidDate(document.formulaire.saisiedate.value)) {
      			error1(document.formulaire.saisiedate,langfunc42);
		}
	}else{
		error1(document.formulaire.saisiedate,langfunc42);
	}
	if (ValidCaractereTime(document.formulaire.heuredepart.value)) {
	error1(document.formulaire.heuredepart,"Indiquez l'heure de début de l'entretien."); }
	if (ValidCaractereTime(document.formulaire.heurefin.value)) {
	error1(document.formulaire.heurefin,"Indiquez l'heure de fin de l'entretien."); }
	return !errfound;
}
