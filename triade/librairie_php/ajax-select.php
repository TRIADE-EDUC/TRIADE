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


callback = {
    search: function(result) {
    

        var target=result[0];
        var form=result[1];
        var champs=result[2];
	//	alert(result);

		result.shift();
		result.shift();
		result.shift()

		var champ0="document."+form+"."+champs;
		var champ=eval(champ0);

		for(i=1;i<champ.options.length;i++){
				champ.options[i].value='';
				champ.options[i].text='';
				champ.options.length--;
		}
		champ.options.length--;
        for(var i in result) {
            if (i != '______array') {
		    	var text=result[i];
		    	 champ.options[champ.options.length] = new Option(text,text);
            }
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
	//	alert(searchBox.value+" "+table+" "+target+" "+form+" "+champs);  //verif 1
  	remoteLiveSearch.search(searchBox.value,table,target,form,champs);
}

</script>

<?php } ?>
