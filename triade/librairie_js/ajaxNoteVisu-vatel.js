ajaxVisuNote = function (idEleve,idClasse,mois,annee) {
        var myAjax = new Ajax.Request(
                "ajaxNoteVisu.php",
                {       method: "post",
                        parameters : "idEleve="+idEleve+"&idClasse="+idClasse+"&m="+mois+"&annee="+annee,
                        asynchronous: true,
                        timeout: 5000,
                        onComplete: displayText
                }
        )
        show_loader(true);
        var myGlobalHandlers = {
                onLoading: function(){$('visunote').innerHTML = '<center><font class="T2">Chargement en cours ...</font> <img src="./image/temps1.gif" align="center" /></center>';},
                onLoaded: function(){$('visunote').innerHTML = '<center><font class="T2">Chargement terminé</font></center>';},
                onTimeout: function(){ show_loader(false);
                                        $('visunote').innerHTML = "<center><font class='T2'><img src='./image/commen/warning.png' align='center' />  Erreur d'accès</font></center>";
                }
        };
        Ajax.Responders.register(myGlobalHandlers);
}

show_loader = function (display)
{
        $('visunote').style.display = (display == true)?'inline':'none';
}


displayText = function (request)
{
//      $('visunote').innerHTML = request.responseText;
        document.getElementById('visunote').innerHTML = request.responseText;
}

