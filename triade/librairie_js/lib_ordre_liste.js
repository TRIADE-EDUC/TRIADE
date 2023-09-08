// monter et descendre des elements dans une liste select
// <input type=button value='Monter' style='width:100px' onClick='tjs_haut(this.form.liste)'>
// <input type=button value='Descendre' style='width:100px' onClick='tjs_bas(this.form.liste)'>
											
function tjs_haut(l) {
	var indice=l.selectedIndex
	if (indice<0) {
		alert("Aucune ligne n'est sélectionnée");
	}
	if (indice>0) {	// Il reste une ligne au-dessus
		tjs_swap(l,indice,indice-1);
	}
}

function tjs_bas(l) {
	var indice=l.selectedIndex
	if (indice<0) {
		alert("Aucune ligne n'est sélectionnée");
	}
	if (indice<l.options.length-1) {	// Il reste une ligne en-dessous
		tjs_swap(l,indice,indice+1);
	}
}

function tjs_swap(l,i,j) {
	var valeur=l.options[i].value;
	var texte=l.options[i].text;
	l.options[i].value=l.options[j].value;
	l.options[i].text=l.options[j].text;
	l.options[j].value=valeur;
	l.options[j].text =texte;
	l.selectedIndex=j
	tjs_ordre(l.form);
}

function tjs_ordre(f) {
	var l=f.liste;
	var ordre="";
	for(var i=0;i<l.options.length;i++) {
		if (i>0) {ordre+="-";}
		ordre+=l.options[i].value;		
	}
	//f.ordre.value=ordre;  // pour afficher l'ordre
}
