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
		duree2.options[9].text="4H30";
		duree2.options[10].text="0.5 J";
		duree2.options[11].text="1 J";
		duree2.options[12].text="2 J";
		duree2.options[13].text="3 J";
		duree2.options[14].text="4 J";
		duree2.options[15].text="autre";
		duree2.options[16].text="heure";
	
		var modif="document.formulaire.saisie_motif_"+id;
		var modif1=eval(modif);
		modif1.value=langinconnu;
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

		var modif="document.formulaire.saisie_motif_"+id;
		var modif1=eval(modif);
		modif1.value=langinconnu;

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
		duree2.options[16].text="";
		
		var date1="document.formulaire.saisie_heure_"+id;
		var date2=eval(date1);
		date2.value="";

		var modif="document.formulaire.saisie_motif_"+id;
		var modif1=eval(modif);
		modif1.value="";
	}
}

function verifjustifier(id) {
	var duree="document.formulaire.saisie_duree_"+id+".options.selectedIndex";
	var motif="document.formulaire.saisie_motifs_"+id+".options.selectedIndex";
	var justifier="document.formulaire.saisie_justifie_"+id;
	var duree2=eval(duree);
	var motif2=eval(motif);
	var justifier2=eval(justifier);

	if ( (duree2 != 0) && (motif2 != 0) ) {
		justifier2.disabled=false;
		
	}else{
		justifier2.disabled=true;
		justifier2.checked=false;
	}
}


function verifjustifier2(id) {
	var duree="document.formulaire.saisie_duree_"+id+".options.selectedIndex";
	var motif="document.formulaire.saisie_motif_"+id+".options.selectedIndex";
	var justifier="document.formulaire.saisie_justifie_"+id;
	var duree2=eval(duree);
	var motif2=eval(motif);
	var justifier2=eval(justifier);

	if ( (duree2 != 0) &&  (motif2 != 0)) {
		justifier2.disabled=false;
		
	}else{
		justifier2.disabled=true;
		justifier2.checked=false;
	}

}

function motifabsretad(id,val) {
        if (val == 1) {
                val=prompt("Indiquer le motif du retard ou de l'absence : ","");
                val=val.substr(0,25);
                if (val == "") {
                        val="inconnu";
                }
        }

        if (val == "0") {
                val="inconnu";
        }
        var motif="document.formulaire.saisie_motif_"+id;
        var motif2=eval(motif);
        motif2.value=val;
}

function motifabsretad2(id,val) {
	if (val == "0") { val="inconnu"; }
        if (val == "autre") {
		AffBullePrompt('information','Indiquer le motif du retard ou de l\'absence : ',id); window.status=''; return true;	
        }else{
		document.getElementById('saisie_motif_'+id).value=val;		
	}

}

function motifabsretad22(id,val) {
	if (val == "0") { val="inconnu"; }
        if (val == "autre") {
		document.getElementById('motif_'+id).style.display='none';
		document.getElementById('saisie_motif_'+id).style.display='block';
        }else{
		document.getElementById('saisie_motif_'+id).value=val;		
	}

}

function verifdonne(id) {
	var choix="document.formulaire.saisie_"+id;
	var choix2=eval(choix);
	if (choix2.options[choix2.selectedIndex].value == "absent") {
		verifDate('document.formulaire.saisie_heure_'+id+'.value');
	}
	if (choix2.options[choix2.selectedIndex].value == "retard") {
		verifHeure('document.formulaire.saisie_heure_'+id+'.value');
	}

}


//------------------------------------------------------------------------//
//------------------------------------------------------------------------//
