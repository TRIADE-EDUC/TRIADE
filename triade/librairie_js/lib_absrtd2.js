function abs2(id,heure,date) {
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
		duree2.options[9].text="0.5 J";
		duree2.options[10].text="1 J";
		duree2.options[11].text="2 J";
		duree2.options[12].text="3 J";
		duree2.options[13].text="4 J";
		duree2.options[14].text="5 J";
		duree2.options[15].text="6 J";
		
		var date1="document.formulaire.saisie_heure_"+id;
		var date2=eval(date1);
		date2.value=date;
		var mod="document.formulaire.saisie_motifs_"+id;
		var modi=eval(mod);
		if (modi.options[modi.selectedIndex].value == "0") {
			var modif="document.formulaire.saisie_motif_"+id;
			var modif1=eval(modif);
			modif1.value=langinconnu;	
		}
	}
	if (choix2.options[choix2.selectedIndex].value == "retard") {
		var modif="document.formulaire.saisie_motif_"+id;
		var modif1=eval(modif);
		var modif="document.formulaire.heurederetard_"+id;
		var modif2=eval(modif);
		var resultat=prompt(langfunc8,"");
		while(1) {
			if (resultat == null) {
				modif1.value=langinconnu;
				modif2.value="";
				duree2.options[0].text="???";
				duree2.options[1].text="???";
				duree2.options[2].text="???";
				duree2.options[3].text="???";
				duree2.options[4].text="???";
				duree2.options[5].text="???";
				duree2.options[6].text="???";
				duree2.options[7].text="???";
				duree2.options[8].text="???";
				duree2.options[9].text="???";
				duree2.options[10].text="???";
				duree2.options[11].text="???";
				duree2.options[12].text="???";
				duree2.options[13].text="???";
				duree2.options[14].text="???";
				duree2.options[15].text="???";
				
			}
			var y=resultat.indexOf("h");
			if (y == "-1") {
				alert(langfunc9);
				var resultat=prompt(langfunc8,"");
			}else {
				modif2.value=resultat;
				var date1="document.formulaire.saisie_heure_"+id;
				var date2=eval(date1);
				date2.value=resultat.replace('h',':');

				break;
			}
		}

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
		
		var mod="document.formulaire.saisie_motifs_"+id;
		var modi=eval(mod);
		if (modi.options[modi.selectedIndex].value == "0") {
			modif1.value=langinconnu;
		}

	}
	if (choix2.options[choix2.selectedIndex].value == 100) {
		duree2.options[0].text="";
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

		var modif="document.formulaire.saisie_motif_"+id;
		var modif1=eval(modif);
		modif1.value="";
	}
}


function motifabsretad(id,val) {
	if (val == 1) {
		AffBullePrompt('information','Indiquer le motif du retard ou de l\'absence : ',id); window.status=''; return true;
	}else{
		document.getElementById('saisie_motif_'+id).value=val;		
	}
}


function motifabsretad11(id,val) {
	if (val == "1") {
		document.getElementById('saisie_motifs_'+id).style.display='none';
		document.getElementById('saisie_motif_'+id).style.display='block';
        }else{
		document.getElementById('saisie_motif_'+id).value=val;		
	}
}


//------------------------------------------------------------------------//
//------------------------------------------------------------------------//
