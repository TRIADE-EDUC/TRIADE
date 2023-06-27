// function deplacement d'un select à un autre
// appel : <input type="button" value="Ajouter >>>" onClick="Deplacer(this.form.liste1,this.form.liste2,'Sélectionner un élèment')">
// ou : <input type="button" value="&lt;&lt;&lt; Enlever" onClick="Deplacer(this.form.liste2,this.form.liste1,'Sélectionner un élèment')">
function Deplacer(l1,l2,message) {

	if (l1.options.selectedIndex>=0) {
		o=new Option(l1.options[l1.options.selectedIndex].text,l1.options[l1.options.selectedIndex].value);
		l2.options[l2.options.length]=o;
		l1.options[l1.options.selectedIndex]=null;	
	}else{
		alert(message);
	}
}
//----------------------------------------//
