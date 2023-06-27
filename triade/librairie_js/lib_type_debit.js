var tjs_img; 
var tjs_src="./image/commun/tjs.gif";   // Image à charger
var tjs_size=32768;	 // Taille de l'image
var tjs_delai=100; 
var tjs_nb=-1; 
var tjs_delai_max=20000; // 20 000 millisecondes
var timer1=0; var timer2=0;
var tjs_fin="";

function Checkkos() {
	// lance la vérif de la connexion
	tjs_img=new Image();
	timer1=new Date();
	timer1=timer1.getTime();
	tjs_img.src=tjs_src+"?dummy="+timer1;
	tjs_nb=0;
	document.inscripform.statDebit.value="no test";
	setTimeout("Timerkos()",tjs_delai);
}
function Timerkos() {
	var anim="-"
	tjs_nb++;
	document.inscripform.statDebit.value="no test";
	if (tjs_nb*tjs_delai>=tjs_delai_max) { // Fin de la durée maxi
		tjs_fin=EvalConnexion(0);
		document.inscripform.statDebit.value=tjs_fin;
	} else {
		if (tjs_img.complete) {
			timer2=new Date(); timer2=timer2.getTime();
			tjs_fin=EvalConnexion(tjs_size/(timer2-timer1));
			document.inscripform.statDebit.value=tjs_fin;
		} else {
			setTimeout("Timerkos()",tjs_delai)
		}
	}
}
function EvalConnexion(kos) {
	tjs_nb=-1;
	res="";
	if (kos==0) {res="no test";}
	if ((kos>0)&&(kos<3)) {res="Modem 28k";}
	if ((kos>3)&&(kos<6)) {res="Modem 56k";}
	if ((kos>6)&&(kos<100)) {res="Haut débit";}
	if (kos>100) {res="En local"; }
	kos=Math.round(kos*10)/10;
	//return res+" (" + kos +" ko/s)"; // avec indiquation du Ko/s
	return res; // sans indiquation du Ko/s
}
