/************************************************************
Last updated: 11.08.2004  by  Eric Taesch
*************************************************************/
// supprimer cette ligne pour activer
//######### click droit ###########//
function clicie() {
        // Fonction de détection pour Internet Explorer
       if (event.button==2) {
		alert(langfunc7);
        }
}
function clicns(e){
        // Fonction pour Netscape
        if(e.which==3){
             alert(langfunc7);
               return false;
       }
}
if (document.all) { document.onmousedown=clicie;}
if (document.layers) {document.captureEvents(Event.MOUSEDOWN); document.onmousedown = clicns;}
//################################//
// supprimer cette ligne pour activer

