function abs(id,heure,date) {
	var duree="document.formulaire.saisie_duree_"+id;
	var choix="document.formulaire.saisie_"+id;
	var duree2=eval(duree);
	var choix2=eval(choix);
	duree2.options[0].text="???";
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
		duree2.options[16].text="heure";
				
		
		var date1="document.formulaire.saisie_heure_"+id;
		var date2=eval(date1);
		date2.value=date;

		var modif="document.formulaire.saisie_motif_"+id;
		var modif1=eval(modif);
		modif1.value=langinconnu;

		var duree1="document.formulaire.saisie_duree1_"+id;
		var duree2=eval(duree1);
		duree2.value="";


	}
	if (choix2.options[choix2.selectedIndex].value == "retard") {
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
		duree2.options[16].text="3h30";

		var date1="document.formulaire.saisie_heure_"+id;
		var date2=eval(date1);
		date2.value=heure;

		var modif="document.formulaire.saisie_motif_"+id;
		var modif1=eval(modif);
		modif1.value=langinconnu;

		var duree1="document.formulaire.saisie_duree1_"+id;
		var duree2=eval(duree1);
		duree2.value="";


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
		duree2.options[16].text="";
		
		var date1="document.formulaire.saisie_heure_"+id;
		var date2=eval(date1);
		date2.value="";

		var modif="document.formulaire.saisie_motif_"+id;
		var modif1=eval(modif);
		modif1.value="";

		var duree1="document.formulaire.saisie_duree1_"+id;
		var duree2=eval(duree1);
		duree2.value="";

	}
}

function abs3(id,heure,date) {
	var choix1="document.formulaire.saisie_duree_"+id;
	var duree1="document.formulaire.saisie_duree1_"+id;
	var duree2=eval(duree1);
	var choix2=eval(choix1);

	var reponse=choix2.options[choix2.selectedIndex].text;
	if (choix2.options[choix2.selectedIndex].text == "autre") {
		reponse=prompt('Indiquer un nombre de jours, S.V.P. L\'Equipe Triade ','');
		if(!isNaN(reponse)) {		
			if ((reponse != null) && (reponse != "")) {
				reponse=reponse+" J";
			}
		}else{
			alert("ATTENTION \n\n Ce n'est pas un nombre, valeur par défaut ??? \n\n L'Equipe Triade");
			reponse="???";
		}
		if ((reponse == null) || (reponse == "")) { reponse="???"; } 
	}
	if (choix2.options[choix2.selectedIndex].text == "heure") {
		reponse=prompt('Indiquer un nombre d\'heures et de minutes (sous la forme 1h00 ou 1h19),\nS.V.P. L\'Equipe Triade ','');
		if (reponse.length == 4)  {		
			//alert(reponse);
		}else{
			alert("ATTENTION \n\n Ce n'est pas au bon format, valeur par défaut ??? \n\n L'Equipe Triade");
			reponse="???";
		}
		if ((reponse == null) || (reponse == "")) { reponse="???"; } 
	}
	duree2.value=reponse
}

function discipline(id) {
	var choix="document.formulaire.saisie_retenu_"+id;
        var choix2=eval(choix);
	if (choix2.options[choix2.selectedIndex].value == 0) {	
		var modif="document.formulaire.saisie_devoir_"+id;
		var modif1=eval(modif);
		modif1.value="";
	}
	if (choix2.options[choix2.selectedIndex].value == 1) {
		var modif="document.formulaire.saisie_devoir_"+id;
                var modif1=eval(modif);
                modif1.value="à définir !!! ";
	}
}
//------------------------------------------------------------------------//
//------------------------------------------------------------------------//
