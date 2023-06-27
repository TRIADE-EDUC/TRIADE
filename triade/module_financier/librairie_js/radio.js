// radio_lire_valeur() : Lire la valeur d'un bouton radio (ensemble de radio du meme groupe)
//		entree : 
//          - (object) radioObj : le bouton radio
//		sortie : la valeur selectionnee (vide si rien de selectionne)
function radio_lire_valeur(radioObj) {
	var i, radioLength;
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

// radio_selectionner() : Selectionner un bouton radio (faisant partie d'un ensemble de radio du meme groupe)
//		entree : 
//          - (object) radioObj : le bouton radio
//          - (string) newValue : la valeur du bouton radio a selectionner
//		sortie : rien
function radio_selectionner(radioObj, newValue) {
	var i, radioLength;
	if(!radioObj)
		return;
	var radioLength = radioObj.length;
	if(radioLength == undefined) {
		radioObj.checked = (radioObj.value == newValue.toString());
		return;
	}
	for(var i = 0; i < radioLength; i++) {
		radioObj[i].checked = false;
		if(radioObj[i].value == newValue.toString()) {
			radioObj[i].checked = true;
		}
	}
}
