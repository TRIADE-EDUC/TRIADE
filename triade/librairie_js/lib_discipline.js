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

function Valid_retenue(id) {
	form="document.formulaire.saisie_retenu_"+id;
	form1=eval(form);
	if (form1.options[form1.selectedIndex].value == 1) {
	//-------//
	form="document.formulaire.saisie_date_retenue_"+id;
	form=eval(form);
	form.value='jj/mm/aaaa';
 	form.select();
   	form.focus();
	//-------//
	form="document.formulaire.saisie_heure_retenue_"+id;
	form=eval(form);
	form.value='hh:mm';
	//-------//
	form="document.formulaire.saisie_duree_retenue_"+id;
	form=eval(form);
	form.value="01:00";
	//-------//
	}else {
	//-------//
	form="document.formulaire.saisie_date_retenue_"+id;
	form=eval(form);
	form.value="";
	//-------//
	form="document.formulaire.saisie_heure_retenue_"+id;
	form=eval(form);
	form.value="";
	//-------//
	form="document.formulaire.saisie_duree_retenue_"+id;
	form=eval(form);
	form.value="";
	}
}


function Valid_retenue2(id) {
	form="document.formulaire5.saisie_retenu_"+id;
	form1=eval(form);
	if (form1.options[form1.selectedIndex].value == 1) {
	//-------//
	form="document.formulaire5.saisie_date_retenue_"+id;
	form=eval(form);
	form.value='jj/mm/aaaa';
 	form.select();
   	form.focus();
	//-------//
	form="document.formulaire5.saisie_heure_retenue_"+id;
	form=eval(form);
	form.value='hh:mm';
	//-------//
	form="document.formulaire5.saisie_duree_retenue_"+id;
	form=eval(form);
	form.value="01:00";
	//-------//
	}else {
	//-------//
	form="document.formulaire5.saisie_date_retenue_"+id;
	form=eval(form);
	form.value="";
	//-------//
	form="document.formulaire5.saisie_heure_retenue_"+id;
	form=eval(form);
	form.value="";
	//-------//
	form="document.formulaire5.saisie_duree_retenue_"+id;
	form=eval(form);
	form.value="";
	}
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

function ValidCaractere(nom) {
        var slach1  = nom.charAt(2);
        var slach2  = nom.charAt(5);
        var jour = nom.substring(0,2);
        var mois = nom.substring(3,5);
        var annee = nom.substring(6,10);
        var annee = nom.substring(6,10);
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


function Valid_formulaire(id) {
	form="document.formulaire.saisie_date_retenue_"+id;
	form=eval(form);
  	if (!ValidCaractere(form.value)) {
     	 error(form," Erreur sur la date  \n\n Syntaxe : jj/mm/aaaa \n ex:10/01/2005 "); }
	else {
	form="document.formulaire.saisie_heure_retenue_"+id;
        form=eval(form);
        form.select();
        form.focus();
	}
}


function valide_discipline() {
	errfound=false;
	if (!Validselect(document.formulaire.saisie_sanction.options.selectedIndex)) {
    		error2("Choisissez une sanction \n\n Service Triade"); }
	if (!Validselect(document.formulaire.saisie_qui.options.selectedIndex)) {
    		error2("Attribué par : ????   \n\n Service Triade"); }
	return !errfound; /* vrai si il ya pas d'erreur */
}

function valide_discipline1()  {
	errfound=false;
	if (!Validselect(document.formulaire.saisie_sanction.options.selectedIndex)) {
    		error2("Choisissez une sanction \n\n Service Triade"); }
	return !errfound; /* vrai si il ya pas d'erreur */
}

function valide_discipline2()  {
	errfound=false;
	if (!Validselect(document.formulaire.saisie_sanction.options.selectedIndex)) {
    		error2("Choisissez une sanction \n\n Service Triade"); }
	return !errfound; /* vrai si il ya pas d'erreur */
}

function valid_date(nom,nom2) {
      if (nom2.options.selectedIndex == 1 ) {
	if (nom.value == "jj/mm/aaaa") {
		return ;
	}
      if ((nom.value.length != 10)  || (!ValidCaractere(nom.value))){
      alert("Erreur sur la date \n\n Syntaxe : jj/mm/aaaa  \n ex:10/01/2005"); }
	}
}

function valid_heure(nom,nom2) {
      if (nom2.options.selectedIndex == 1 ) {
      	if ((nom.value.length != 5)  || (ValidCaractereTime(nom.value))){
      		alert("Erreur sur l'heure  \n\n Syntaxe : hh:mm \n ex: 17:00 ou  09:30 "); 
	}
      }
}


function print_retenue_du_jour(){
        var ok=confirm("Confirmez l'impression \n Service Triade ");
        if (ok) {
                open('gestion_discipline_du_jour_print.php','_blank','');
        }
}

function print_retenue_du_jour_2(date,dateFin,tri){
        var ok=confirm("Confirmez l'impression \n Service Triade ");
        if (ok) {
		var lien="gestion_discipline_du_jour_print.php?date="+date+"&dateFin="+dateFin+"&tri="+tri;
                open(lien,'_blank','');
        }
}

function print_sanction_du_jour(){
        var ok=confirm("Confirmez l'impression \n Service Triade ");
        if (ok) {
                open('gestion_sanction_du_jour_print.php','_blank','');

        }
}

function verif_retenue_non_fait() {
	errfound=false;

if (document.formulaire.datenews.value.length != 10){
error(document.formulaire.datenews,"Date erreur  \n\n Syntaxe : jj/mm/aaaa \n ex:10/01/2005 "); }

if (!ValidCaractere(document.formulaire.datenews.value)) {
error(document.formulaire.datenews,"Date erreur  \n\n Syntaxe : jj/mm/aaaa\n ex:10/01/2005  "); }

if (document.formulaire.heurenews.value.length != 5) {
error(document.formulaire.heurenews,"Indiquez l\'heure de la retenue  \n\n Syntaxe : hh:mm  \n ex: 17:00 ou 01:00 "); }

if (ValidCaractereTime(document.formulaire.heurenews.value)) {
error(document.formulaire.heurenews,"Indiquez l'\heure  de la retenue  \n\n Syntaxe : hh:mm \n ex: 17:00 ou 01:00"); }

if (document.formulaire.dureenews.value.length != 5) {
error(document.formulaire.dureenews,"Indiquez l\'heure de la retenue  \n\n Syntaxe : hh:mm  \n ex: 17:00 ou 01:00 "); }

if (ValidCaractereTime(document.formulaire.dureenews.value)) {
error(document.formulaire.dureenews,"Indiquez l'\heure  de la retenue  \n\n Syntaxe : hh:mm \n ex: 17:00 ou 01:00"); }


return !errfound;
}


function valide_sanction() {
	errfound=false;
	if (!Validselect(document.formulaire.saisie_category.options.selectedIndex)) {
	    error2("Indiquer la categorie  \n\n Service Triade"); }
	return !errfound;
}



function valide_supp_discipline() {
	errfound=false;
	errfound=confirm("Confirmez la suppression des retenues et des sanctions \n\n L'Equipe Triade");
	return errfound;

}
