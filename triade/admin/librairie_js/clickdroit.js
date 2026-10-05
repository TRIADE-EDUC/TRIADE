/************************************************************

Last updated:08.07.02  by  Eric Taesch
*************************************************************/
// pour remetre le menu enleve le commentaire
function affiche_menu() {

// Detection de la position du clic (distance au bords droit et bas)
        var droite = document.body.clientWidth - event.clientX;
        var bas = document.body.clientHeight - event.clientY;

// Si il n'y a pas la place a droite pour placer le menu
        if ( droite < menu.offsetWidth ) {

// On place le menu a gauche de la souris
                menu.style.left = document.body.scrollLeft + event.clientX - menu.offsetWidth;
                }

// Sinon, on le place a droite de la souris
        else {
                menu.style.left = document.body.scrollLeft + event.clientX;
                }

// Pareil vis-a-vis du bas de l'ecran
        if ( bas < menu.offsetHeight ) {
                menu.style.top = document.body.scrollTop + event.clientY - menu.offsetHeight;
                }
        else {
                menu.style.top = document.body.scrollTop + event.clientY;
                }

// Affichage proprement dit du menu
        menu.style.visibility = "visible";
        return false;
        }

// Masquage du menu
function masque_menu() {
        menu.style.visibility = "hidden";
        }


// Surlignage des intitules (position 'on')
function surlignage() {
        if ( event.srcElement.className == "intitules" ) {
                event.srcElement.style.backgroundColor = "highlight";
                event.srcElement.style.color = "white";
                }
        }


// Remise a l'etat normal des intitules (position 'off')
function normal() {
        if ( event.srcElement.className == "intitules" ) {
                event.srcElement.style.backgroundColor = "";
                event.srcElement.style.color = "black";
                }
        }


// Chargement des liens
function aller() {
        if ( event.srcElement.className == "intitules" ) {

// Verification de la fenetre cible pour le lien (nouvelle fenetre...)
                if ( event.srcElement.getAttribute("target") != null ) {
                        window.open(event.srcElement.url,event.srcElement.getAttribute("target"));
                        }
                else {
                        window.location = event.srcElement.url;
                        }
                }
        }
if ( document.all) {

// Sur clic-droit, affichage
        document.oncontextmenu = affiche_menu;

// Sur clic gauche, masquage
//document.body.onclick = masque_menu;
        }


//---------------------------------------------------------------------------//
//---------------------------------------------------------------------------//

////////////////////////////////////////////////////////
// pour afficher une fenetre au centre
function PopupCentrer(page,largeur,hauteur,options,nom_de_la_fenetre) {
  var top=(screen.height-hauteur)/2;
    var left=(screen.width-largeur)/2;
      window.open(page,nom_de_la_fenetre,"top="+top+",left="+left+",width="+largeur+
      ",height="+hauteur+","+options);
      }

// Fonction quitter session  compte admin_triade
function quitter_session_admin() {
         var confirmation=confirm('Souhaitez-vous fermer votre session ? \n\n Service Triade')
         if (confirmation) {
             PopupCentrer('/'+REPECOLE+'/'+REPADMIN+'/deconnection.php','250','100','','quitte');
             parent.window.close();
             location.href="/"
         }
}
      
////////////////////////////////////////////////////////
