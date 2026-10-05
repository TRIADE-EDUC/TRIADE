// Nicht mehr als 24 Zeichen
langtitredepart0="Triade kontaktieren"; // "Contacter Triade" -> "Triade kontaktieren"
langtitredepart1="Suchen "; // "Rechercher " -> "Suchen "
langtitredepart2="Kontakt&nbsp;Triade"; // "Contact&nbsp;Triade" -> "Kontakt&nbsp;Triade" (keep HTML entity)
langtitredepart3="Verlassen"; // "Quitter" -> "Verlassen"
//-------
langmenudepart0="Ihr Konto"; // "Votre Compte" -> "Ihr Konto"
langmenudepart01="Zugang "+INTITULEDIRECTION; // "Acc&egrave;s " -> "Zugang " (keep HTML entity)
langmenudepart02="Zugang Schulleben"; // "Acc&egrave;s Vie scolaire" -> "Zugang Schulleben"
langmenudepart03="Zugang "+INTITULEENSEIGNANT; // "Acc&egrave;s " -> "Zugang "
langmenudepart04="Zugang Eltern"; // "Acc&egrave;s Parent" -> "Zugang Eltern"
langmenudepart05="Zugang "+INTITULEELEVE; // "Acc&egrave;s " -> "Zugang "
//-------
langmenudepart1="T.R.I.A.D.E."; // Keep as is
langmenudepart10="Startseite"; // "Accueil" -> "Startseite"
langmenudepart11="Über uns"; // "A propos" -> "Über uns"
langmenudepart12="Ihre Werbung"; // "Votre Publicit&eacute;" -> "Ihre Werbung" (keep HTML entity)
//-------
langmenudepart2="Unterstützung"; // "Assistance" -> "Unterstützung"
langmenudepart21="Zugangsproblem"; // "Probl&egrave;me d'acc&egrave;s" -> "Zugangsproblem" (keep HTML entity)
langmenudepart22="Fragen"; // "Questions" -> "Fragen"
//-------
langtitremembre1=INTITULEDIRECTION; // Keep variable
langtitremembre2="Schulleben"; // "Vie%20Scolaire" -> "Schulleben" (remove %20 as it's for GET method, not the string itself)
langtitremembre3=INTITULEENSEIGNANT; // Keep variable
langtitremembre4="Eltern"; // "Parent" -> "Eltern"
langtitremembre5=INTITULEELEVE; // Keep variable

// #########################
// Fußzeile
if (footer != "") {
	if (footerlien != "") {
		langmenupied="<a href='"+footerlien+"' target='_blank'>"+footer+"</a>";
	}else{
		langmenupied=footer;
	}
	// Translate the resolution text
	langmenupied+="<br>Um diese Seite optimal anzuzeigen: minimale Auflösung: 800x600 <br>";
}else{
	// Translate the full footer text
	langmenupied="<p> Die <b>T</b>ransparenz und die <b>R</b>aschheit der <b>I</b>nformatik im <b>D</b>ienste der <b>E</b>rziehung<br>Um diese Seite optimal anzuzeigen: minimale Auflösung: 800x600 <br> T.R.I.A.D.E. &copy; 2026 - Alle Rechte vorbehalten";
}

// --------------
// #########################
// Image paths and alt texts remain the same as they are not language strings
img_logo_pied="<img src='./image/commun/triade-xhtml.jpg' alt='XHTML'> <img src='./image/commun/triade-w3C.jpg' alt='w3C'> <img src='./image/commun/triade-css.png' alt='css' > <a href='http://www.triade-educ.com/accueil/don-triade.php' target='_blank' ><img border='0' src='./image/commun/triade_paypal.png' alt='Paypal' ></a><br /><br />";

langmenudepart06="Zugang Praktikumsbetreuer"; // "Acc&egrave;s Tuteur Stage" -> "Zugang Praktikumsbetreuer"
langtitremembre6="Praktikumsbetreuer"; // "Tuteurs de stage" -> "Praktikumsbetreuer"


langmenudepart3="Voranmeldung"; // "Pr&eacute;-inscriptions" -> "Voranmeldung" (keep HTML entity)
langmenudepart30="Bewerbung"; // "Candidature" -> "Bewerbung"
langmenudepart31="Status der Bewerbung"; // "Suivi Candidature" -> "Status der Bewerbung"

langmenudepart07="Zugang Personal"; // "Acc&egrave;s Personnels" -> "Zugang Personal"
langtitremembre7="Personal"; // "Personnels" -> "Personal"
