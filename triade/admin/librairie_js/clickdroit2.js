/************************************************************

Last updated:08.07.02  by  Eric Taesch
*************************************************************/
// supprimer cette ligne pour activer
//######### click droit ###########//
function clicie() {
        // Fonction de détection pour Internet Explorer
       if (event.button==2) {
       		alert("Logiciel Libre GPL \n\n http://www.triade-educ.com ");
        }
}
function clicns(e){
        // Fonction pour Netscape
        if(e.which==3){
       	       alert("Logiciel Libre GPL \n\n http://www.triade-educ.com ");
               return false;
       }
}
if (document.all) { document.onmousedown=clicie;}
if (document.layers) {document.captureEvents(Event.MOUSEDOWN); document.onmousedown = clicns;}
//################################//
// supprimer cette ligne pour activer

