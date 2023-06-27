function absplanifier0(id,heure,date) {
	var duree="document.formulaire_"+id+".saisie_duree_"+id;
	var choix="document.formulaire_"+id+".saisie_"+id;
	var duree2=eval(duree);
	var choix2=eval(choix);
	duree2.options[0].text="????";
	if (choix2.options[choix2.selectedIndex].value == "absent") {
		duree2.options[1].text="0H30";
		duree2.options[2].text="1H00";
		duree2.options[3].text="1H30";
		duree2.options[4].text="2H00";
		duree2.options[5].text="2H30";
		duree2.options[6].text="3H00";
		duree2.options[7].text="3H30";
		duree2.options[8].text="4H00";
		duree2.options[9].text="4H30";
		duree2.options[10].text="0.5 J";
		duree2.options[11].text="1 J";
		duree2.options[12].text="2 J";
		duree2.options[13].text="3 J";
		duree2.options[14].text="4 J";
		duree2.options[15].text="autre";
	
	var duree_ret="document.formulaire_"+id+".saisie_duree_retourner_"+id;
    	var duree_retourner=eval(duree_ret);
    	duree_retourner.value=duree2.options[duree2.selectedIndex].text;
		
	}
	if (choix2.options[choix2.selectedIndex].value == "retard") {
    		var duree_ret="document.formulaire_"+id+".saisie_duree_retourner_"+id;
    		var duree_retourner=eval(duree_ret);
    		duree_retourner.value="";
		duree2.options[1].text="5mn";
		duree2.options[2].text="10mn";
		duree2.options[3].text="15mn";
		duree2.options[4].text="20mn";
		duree2.options[5].text="25mn";
		duree2.options[6].text="30mn";
		duree2.options[7].text="35mn";
		duree2.options[8].text="45mn";
		duree2.options[9].text="1h00";
		duree2.options[10].text="1h15";
		duree2.options[11].text="1h30";
		duree2.options[12].text="1h45";
		duree2.options[13].text="2h00";
		duree2.options[14].text="2h30";
		duree2.options[15].text="autre";
    
	var duree_ret="document.formulaire_"+id+".saisie_duree_retourner_"+id;
    	var duree_retourner=eval(duree_ret);
    	duree_retourner.value=duree2.options[duree2.selectedIndex].text;


	}
	if (choix2.options[choix2.selectedIndex].value == 0) {
		duree2.options[0].text="Rien";
		duree2.options[1].text="";
		duree2.options[2].text="";
		duree2.options[3].text="";
		duree2.options[4].text="";
		duree2.options[5].text="";
		duree2.options[6].text="";
		duree2.options[7].text="";
		duree2.options[8].text="";
		duree2.options[9].text="";
		duree2.options[10].text="";
		duree2.options[11].text="";
		duree2.options[12].text="";
		duree2.options[13].text="";
		duree2.options[14].text="";
		duree2.options[15].text="";
		
		var date1="document.formulaire_"+id+".saisie_heure_"+id;
		var date2=eval(date1);
		date2.value="";
	
    	var duree_ret="document.formulaire_"+id+".saisie_duree_retourner_"+id;
    	var duree_retourner=eval(duree_ret);
    	duree_retourner.value="";
		
	}
}


function absplanifier(id,heure,date) {
	var duree="document.formulaire_"+id+".saisie_duree_"+id;
	var choix="document.formulaire_"+id+".saisie_"+id;
	var duree2=eval(duree);
	var choix2=eval(choix);
	duree2.options[0].text="????";
	if (choix2.options[choix2.selectedIndex].value == "absent") {
		duree2.options[1].text="0H30";
		duree2.options[2].text="1H00";
		duree2.options[3].text="1H30";
		duree2.options[4].text="2H00";
		duree2.options[5].text="2H30";
		duree2.options[6].text="3H00";
		duree2.options[7].text="3H30";
		duree2.options[8].text="4H00";
		duree2.options[9].text="4H30";
		duree2.options[10].text="0.5 J";
		duree2.options[11].text="1 J";
		duree2.options[12].text="2 J";
		duree2.options[13].text="3 J";
		duree2.options[14].text="4 J";
		duree2.options[15].text="autre";
		
		var date1="document.formulaire_"+id+".saisie_heure_"+id;
		var date2=eval(date1);
		date2.value="08:00";
	
	var duree_ret="document.formulaire_"+id+".saisie_duree_retourner_"+id;
    	var duree_retourner=eval(duree_ret);
    	duree_retourner.value=duree2.options[duree2.selectedIndex].text;
		
	}
	if (choix2.options[choix2.selectedIndex].value == "retard") {
    		var duree_ret="document.formulaire_"+id+".saisie_duree_retourner_"+id;
    		var duree_retourner=eval(duree_ret);
    		duree_retourner.value="";
		duree2.options[1].text="5mn";
		duree2.options[2].text="10mn";
		duree2.options[3].text="15mn";
		duree2.options[4].text="20mn";
		duree2.options[5].text="25mn";
		duree2.options[6].text="30mn";
		duree2.options[7].text="35mn";
		duree2.options[8].text="45mn";
		duree2.options[9].text="1h00";
		duree2.options[10].text="1h15";
		duree2.options[11].text="1h30";
		duree2.options[12].text="1h45";
		duree2.options[13].text="2h";
		duree2.options[14].text="2h30";
		duree2.options[15].text="autre";

		var date1="document.formulaire_"+id+".saisie_heure_"+id;
		var date2=eval(date1);
		date2.value="08:00";
    
	var duree_ret="document.formulaire_"+id+".saisie_duree_retourner_"+id;
    	var duree_retourner=eval(duree_ret);
    	duree_retourner.value=duree2.options[duree2.selectedIndex].text;


	}
	if (choix2.options[choix2.selectedIndex].value == 0) {
		duree2.options[0].text="Rien";
		duree2.options[1].text="";
		duree2.options[2].text="";
		duree2.options[3].text="";
		duree2.options[4].text="";
		duree2.options[5].text="";
		duree2.options[6].text="";
		duree2.options[7].text="";
		duree2.options[8].text="";
		duree2.options[9].text="";
		duree2.options[10].text="";
		duree2.options[11].text="";
		duree2.options[12].text="";
		duree2.options[13].text="";
		duree2.options[14].text="";
		duree2.options[15].text="";
		
		var date1="document.formulaire_"+id+".saisie_heure_"+id;
		var date2=eval(date1);
		date2.value="";
	
    	var duree_ret="document.formulaire_"+id+".saisie_duree_retourner_"+id;
    	var duree_retourner=eval(duree_ret);
    	duree_retourner.value="";
		
	}
}

function absplanifier2(id) {
    var duree_ret="document.formulaire_"+id+".saisie_duree_retourner_"+id;
    var duree_retourner=eval(duree_ret);
    var duree2="document.formulaire_"+id+".saisie_duree_"+id;
    var duree3=eval(duree2);

	if (duree3.options[duree3.selectedIndex].text == "autre" ) {
		var ok=1;
		while (ok != 0) {
			valeur=prompt(langfunc10,'')
			if (isNaN(valeur)) { ok=1; }
			else {
				ok=0;
			}
			if (valeur == null) { valeur=0; }	
    			duree_retourner.value=valeur+" J";
		}
	}else {
    		duree_retourner.value=duree3.options[duree3.selectedIndex].text;
	}

}



//----------------------------------------------------------------------//
//---------------------------------------------------------------------//
function chargement_pendant(choix,id,idF) {
	var duree="document.formulaire_"+idF+".saisie_duree_"+id;
	var duree2=eval(duree);
    	var duree_ret="document.formulaire_"+idF+".saisie_duree_retourner_"+id;
    	var duree_retourner=eval(duree_ret);
	duree_retourner.value="";
        duree2.options[0].text=choix;
	duree2.options[1].text="5mn";
	duree2.options[2].text="10mn";
	duree2.options[3].text="15mn";
	duree2.options[4].text="20mn";
	duree2.options[5].text="25mn";
	duree2.options[6].text="30mn";
	duree2.options[7].text="35mn";
	duree2.options[8].text="45mn";
	duree2.options[9].text="1h";
	duree2.options[10].text="1h15";
	duree2.options[11].text="1h30";
	duree2.options[12].text="1h45";
	duree2.options[13].text="2h";
	duree2.options[14].text="2h30";
	duree2.options[15].text="3h";
    	duree_retourner.value=duree2.options[duree2.selectedIndex].text;
}

//---------------------------------------------------------------------//
//---------------------------------------------------------------------//
function chargement_pendant_jour(choix,id,idF) {
	var duree="document.formulaire_3_"+idF+".saisie_duree_"+id;
	var duree2=eval(duree);
    	var duree_ret="document.formulaire_3_"+idF+".saisie_duree_retourner_"+id;
    	var duree_retourner=eval(duree_ret);
        duree2.options[0].text=choix;
	duree2.options[1].text="0H30";
	duree2.options[2].text="1H00";
	duree2.options[3].text="1H30";
	duree2.options[4].text="2H00";
	duree2.options[5].text="2H30";
	duree2.options[6].text="3H00";
	duree2.options[7].text="3H30";
	duree2.options[8].text="4H00";
	duree2.options[9].text="4H30";
	duree2.options[10].text="0.5 J";
	duree2.options[11].text="1 J";
	duree2.options[12].text="2 J";
	duree2.options[13].text="3 J";
	duree2.options[14].text="4 J";
        duree2.options[15].text="autre";
    	duree_retourner.value=duree2.options[duree2.selectedIndex].text;
	
	if (duree2.options[duree2.selectedIndex].text == "autre" ) {
		var ok=1;
		while (ok != 0) {
			valeur=prompt(langfunc10,'')
			if (isNaN(valeur)) { ok=1; }
			else {
				ok=0;
			}
			if (valeur == null) { valeur=0; }	
    			duree_retourner.value=valeur+" J";
		}
	}else {
    		duree_retourner.value=duree2.options[duree2.selectedIndex].text;
	}

}

