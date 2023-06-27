<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET -
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/


function  ajax_js() {
?>



<script type="text/javascript">

var text;
var target;
var formulaire;
var nom;

function mettre(text,formulaire,nom,target) {
	var champ0="document."+formulaire+"."+nom;
	var champ=eval(champ0);
	text = text.replace(/#/g,"'");
	champ.value=text;
	document.getElementById(target).style.visibility = 'hidden' ;
	document.getElementById(target).style.display= 'none';
}



callback = {
    search: function(result) {
        var out = "";

        var target=result[0];
        var form=result[1];
        var champs=result[2];
	result.shift();
	result.shift();
	result.shift();
		
        for(var i in result) {
            if (i != '______array') {
		var text=result[i];
		var key=i;
		text2 = text.replace(/'/g,"#");
                out += "&nbsp;<a href='#' onclick='mettre(\""+text2+"\",\""+form+"\",\""+champs+"\",\""+target+"\")' >"+text+"</a>&nbsp;&nbsp;<br>";
            }
        }
        out += "";
	  if (out != "") {
		document.getElementById(target).style.visibility = 'visible' ;
		document.getElementById(target).style.display = 'block' ;
	      	document.getElementById(target).innerHTML = out;
	  }else{
	  	document.getElementById(target).innerHTML = out;
		document.getElementById(target).style.visibility = 'hidden' ;
		document.getElementById(target).style.display = 'none' ;
	  }
    }
}


// setup our remote object from the generated proxy stub
var remoteLiveSearch = new livesearch(callback);

// we could change the queue by overriding the default one, but generally you want to create a new one
// set our remote object to use the rls queue
remoteLiveSearch.dispatcher.queue = 'rls';


// create the rls queue, with a 350ms buffer, a larger interval such as 2000 is useful to see what is happening but not so useful in real life
HTML_AJAX.queues['rls'] = new HTML_AJAX_Queue_Interval_SingleBuffer(350);

// what to call on onkeyup, you might want some logic here to not search on empty strings or to do something else in those cases
function searchRequest(searchBox,table,target,form,champs) {
    remoteLiveSearch.search(searchBox.value,table,target,form,champs);
   // alert(searchBox.value,table,target,form,champs);
}

</script>

<?php } ?>
