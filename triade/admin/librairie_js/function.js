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
         var confirmation=confirm('Souhaitez-vous fermer votre session ? \n\n Service Triade')
         if (confirmation) {
             PopupCentrer('/'+REPECOLE+'/'+REPADMIN+'/librairie_php/deconnection.php','250','100','','quitte');
             parent.window.close();
         }
}

////////////////////////////////////////////////////////
// Fonction quitter avant session
function quitter_avant_session() {
         var confirmation=confirm('Souhaitez-vous fermer votre session ? \n\n Service Triade')
         if (confirmation) {
             parent.window.close();
         }
}

////////////////////////////////////////////////////////
// Fonction quitter session  compte admin_triade
function quitter_session_admin() {
         var confirmation=confirm('Souhaitez-vous fermer votre session ? \n\n Service Triade')
         if (confirmation) {
             PopupCentrer('/'+REPECOLE+'/'+REPADMIN+'/deconnection.php','250','100','','quitte');
             parent.window.close();
             location.href="/"
         }
}

//////////////////////////////////////////////////////
// function pour les bouton
// appel de la fonction
// <script language=Javascript>buttonMagic("autre","#' onclick=\"open('essai.html','acces_compte','width=500,height=200')\""); // value,lien</script>
function buttonMagic(value,lien,name,option,actionpossible) {
        document.write("<div style='float:left;margin-left:4px;' class='button1'>");
        document.write("<div class='btnleft1'></div>");
        document.write("<div class='btncenter1'><a href='#' onclick=\"open('"+lien+"','"+name+"','"+option+"')"+actionpossible+"\"  >"+value+"</a></div>");
        document.write("<div class='btnright1'></div>");
        document.write("</div>");
}

function buttonMagicSubmit(value,name) {
        document.write("<div style='float:left;margin-left:4px;' class='button1'>");
        document.write("<div class='btnleft1'></div>");
        document.write("<div class='btncenter1'><input type=submit value='"+value+"' name='"+name+"' ></div>");
        document.write("<div class='btnright1'></div>");
        document.write("</div>");
}

function buttonMagicSubmit2(value,name,action) {
        document.write("<div style='float:left;margin-left:4px;' class='button1'>");
        document.write("<div class='btnleft1'></div>");
        document.write("<div class='btncenter1'><input type=submit value='"+value+"' name='"+name+"' onclick=\"this.value='"+action+"'\" ></div>");
        document.write("<div class='btnright1'></div>");
        document.write("</div>");
}

function buttonMagicSubmit3(value,lien,name,option,action,actionpossible) {
        document.write("<div style='float:left;margin-left:4px;' class='button1'>");
        document.write("<div class='btnleft1'></div>");
        document.write("<div class='btncenter1'><input type=submit value='"+value+"' name='"+name+"' onclick=\"open('"+lien+"','"+name+"','"+option+"')"+actionpossible+"\" ></div>");
        document.write("<div class='btnright1'></div>");
        document.write("</div>");
}

function buttonMagicSubmit4(value,name,action) {
	document.write("<div style='float:left;margin-left:4px;' class='button1'>");
	document.write("<div class='btnleft1'></div>");
	document.write("<div class='btncenter1'><input type=submit value='"+value+"' name='"+name+"' "+action+"></div>");
	document.write("<div class='btnright1'></div>");
	document.write("</div>");
}


function buttonMagicFermeture() {
        document.write("<div style='float:left;margin-left:4px;' class='button1'>");
        document.write("<div class='btnleft1'></div>");
        document.write("<div class='btncenter1'><input type=button value='Fermer la fenêtre' onclick=\"parent.window.close();\"></div>");
        document.write("<div class='btnright1'></div>");
        document.write("</div>");
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
	document.write("<div style='float:left;margin-left:4px;' class='button1'>");
	document.write("<div class='btnleft1'></div>");
	document.write("<div class='btncenter1'><input type=submit "+attribut+" value='"+value+"' name='"+name+"' ></div>");
	document.write("<div class='btnright1'></div>");
	document.write("</div>");
}

function buttonMagic2(value,lien,name,option,disabled) {
        document.write("<div style='float:left;margin-left:4px;' class='button1'>");
        document.write("<div class='btnleft1'></div>");
        if (disabled == 1) {
                document.write("<div class='btncenter1'><a href='javascript:return(true)' disabled='disabled'   style='font-weight:bold;color:#000080' >"+value+"</a></div>");
        }else{
                document.write("<div class='btncenter1'><a href='#' onclick=\"open('"+lien+"','"+name+"','"+option+"');\"   style='font-weight:bold;color:#000080' >"+value+"</a></div>");
        }
        document.write("<div class='btnright1'></div>");
        document.write("</div>");
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



