/************************************************************

Last updated:08.07.02  by  Eric Taesch
*************************************************************/

// module pour les messages error
function NoError() {
     return true;
}
window.onerror=NoError;


// pour recharger la page //
function reload() {
         history.go(0)
}

// pour afficher une fenetre au centre
function PopupCentrer(page,largeur,hauteur,options,nom_de_la_fenetre) {
  var top=(screen.height-hauteur)/2;
  var left=(screen.width-largeur)/2;
  window.open(page,nom_de_la_fenetre,"top="+top+",left="+left+",width="+largeur+",height="+hauteur+","+options);
}


// pour afficher une fenetre au centre l'attente
function PopupCentrerAttente(page,largeur,hauteur,options) {
  var top=(screen.height-hauteur)/2;
  var left=(screen.width-largeur)/2;
  attente=open(page,'attente',"top="+top+",left="+left+",width="+largeur+",height="+hauteur+","+options);
}


function attente() {
    PopupCentrerAttente('/'+REPECOLE+'/'+REPADMIN+'/image/attente.php','200','120','')
}


function attente_close() {
   attente.close();return true;
}




//fonction de validation d'après la longueur de la chaîne
function ValidLongueur(item,len) {
   drapeau = 1;
   return (item.length >= len);
}



////////////////////////////////////////////////////////
// affiche un message d'alerte
function error1(elem, text) {
// abandon si erreur déjà signalée
   if (errfound) return;
   window.alert(text);
   elem.select();
   elem.focus();
   errfound = true;
}

// verif du champ de recherche
function verif_recherche() {
     errfound = false;
     if (!ValidLongueur(document.recherche.search.value,3)){
      error1(document.recherche.search,"Minimum 3 caractères, S.V.P.  \n\n Service Triade"); }
return !errfound; /* vrai si il ya pas d'erreur */
}


////////////////////////////////////////////////////////

// fonction imprimer
function imprimer() {
        var ok=confirm("Confirmez l'impression, S.V.P. \n Service Triade ");
        if (ok) {
                window.print();
        }
}

////////////////////////////////////////////////////////
// Fonction quitter session
function quitter_session() {
    if (confirm('Souhaitez-vous fermer votre session ?')) {
        PopupCentrer('/'+REPECOLE+'/'+REPADMIN+'/librairie_php/deconnection.php','250','100','','quitte');
        parent.window.close();
    }
}

////////////////////////////////////////////////////////
// Fonction quitter avant session
function quitter_avant_session() {
    if (confirm('Souhaitez-vous fermer votre session ?')) {
        parent.window.close();
    }
}

////////////////////////////////////////////////////////
// Fonction quitter session  compte admin_triade
function quitter_session_admin() {
    if (confirm('Souhaitez-vous fermer votre session ?')) {
        PopupCentrer('/'+REPECOLE+'/'+REPADMIN+'/deconnection.php','250','100','','quitte');
        parent.window.close();
        location.href="/";
    }
}

var _ajsStyle = document.createElement('style');
_ajsStyle.textContent = '.alertify .ajs-dialog{min-height:0!important;max-width:380px!important;padding:12px 12px 0 12px!important}.alertify .ajs-header{margin:-12px!important;margin-bottom:0!important;padding:8px 12px!important}.alertify .ajs-body{min-height:0!important}.alertify .ajs-body .ajs-content{padding:8px 12px!important}.alertify .ajs-footer{margin-left:-12px!important;margin-right:-12px!important;min-height:0!important}';
(document.head || document.documentElement).appendChild(_ajsStyle);

//////////////////////////////////////////////////////
// function pour les bouton
function buttonMagic(value,lien,name,option,actionpossible) {
	document.write("<input type='button' class='btn btn-primary' value=\""+value+"\" onclick=\"open('"+lien+"','"+name+"','"+option+"')"+actionpossible+"\">");
}

function buttonMagicSubmit(value,name) {
	document.write("<input type='submit' class='btn btn-primary' value='"+value+"' name='"+name+"'>");
}

function buttonMagicSubmit2(value,name,action) {
	document.write("<input type='submit' class='btn btn-primary' value='"+value+"' name='"+name+"' onclick=\"this.value='"+action+"'\">");
}

function buttonMagicSubmit3(value,lien,name,option,action,actionpossible) {
	document.write("<input type='submit' class='btn btn-primary' value='"+value+"' name='"+name+"' onclick=\"open('"+lien+"','"+name+"','"+option+"')"+actionpossible+"\">");
}

function buttonMagicSubmit4(value,name,action) {
	document.write("<input type='submit' class='btn btn-primary' value='"+value+"' name='"+name+"' "+action+">");
}

function buttonMagicFermeture() {
	document.write("<input type='button' class='btn btn-secondary' value='Fermer la fenêtre' onclick=\"parent.window.close();\">");
}

function compter(f,max,sortie) {
	var txt=f.value;
	var nb=txt.length;
	if (nb>max) { 
		alert("Pas plus de "+max+" caractères dans ce champ");
		f.value=txt.substring(0,max);
		nb=max;
	}
	sortie.value=nb;
}		


function buttonMagicSubmitAtt(value,name,attribut) {
	document.write("<input type='submit' class='btn btn-primary' "+attribut+" value='"+value+"' name='"+name+"'>");
}

function buttonMagic2(value,lien,name,option,disabled) {
	if (disabled == 1) {
		document.write("<input type='button' class='btn btn-primary' disabled value=\""+value+"\">");
	} else {
		document.write("<input type='button' class='btn btn-primary' value=\""+value+"\" onclick=\"open('"+lien+"','"+name+"','"+option+"');\">");
	}
}

<!-- Matomo -->
var _paq = window._paq = window._paq || [];
/* tracker methods like "setCustomDimension" should be called before "trackPageView" */
_paq.push(['trackPageView']);
_paq.push(['enableLinkTracking']);
(function() {
    var u="https://analytics.triade-educ.net/matomo/";
    _paq.push(['setTrackerUrl', u+'matomo.php']);
    _paq.push(['setSiteId', '5']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
  })();
<!-- End Matomo Code -->



