// nom du champs en dure saisienews
var max=500;  		// 500 caractères maximum
function compter(f) {
	var txt=f.saisienews.value;
	var nb=txt.length;
	if (nb>max) { 
		f.saisienews.value=txt.substring(0,max);
		nb=max;
	}
	f.nbcar.value=nb;
}

function timer() {
	compter(document.forms["formulaire"]);
	setTimeout("timer()",100);
}
