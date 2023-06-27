
// est_nombre() : Verifier si une chaine est un nombre (entier, decimal, ...)
//		entree :
//			- str_chaine (string) : la chaine a verifier
//			- str_type ('entier'|'entier_moins'|'decimal'|'decimal_moins') : type de nombre desire
//			- str_decimal (','|'.') : le separateur de decimal a utiliser
//		sortie : (true|false) indique si la chaine est un nombre ou non
function est_nombre(str_chaine, str_type, str_decimal) {
	var msg_erreur="";
	var str_valid_car;
	var str_car;
	var bln_resultat = false;
	//alert(str_chaine);
	// Verifier si le separateur de decimal est utilise
	if(!str_decimal) {
		str_decimal = '';
	}

	// La liste des carateres valides depend du type de nombre (avec ou sans '-', avec ou sans decimal)
	switch(str_type) {
		case "entier":
			str_valid_car = "0123456789";
		case "entier_moins":
			str_valid_car = "0123456789-";
		case "decimal":
			str_valid_car = "0123456789" + str_decimal;
			break;
		case "decimal_moins":
			str_valid_car = "0123456789-" + str_decimal;
			break;
		default:
			str_valid_car = "";
	}
	
	// Verifier si il y a quelque chose a verifier
	if(str_valid_car != '') {
		bln_resultat = true;
		// Verifier si la chaine est vide ou non
		if (str_chaine.length > 0) 
		{
			// Verifier que chaque caractere de la chaine fait partie des caracteres valides
			for (i = 0; i < str_chaine.length; i++) 
			{
				str_car = str_chaine.charAt(i);
				if (str_valid_car.indexOf(str_car) == -1) 
				{
					bln_resultat = false;
					break;
				}
			}
		}
	} else {
		bln_resultat = false;
	}
	return(bln_resultat);
}


// est_nombre() : Verifier si une chaine est un nombre (entier, decimal, ...)
//		entree :
//			- str_chaine (string) : la chaine a verifier
//			- str_car_autorises (string) : les caracteres autorises
//		sortie : (true|false) indique si la chaine est valide ou non
function valider_chaine(str_chaine, str_car_autorises) {
	var msg_erreur="";
	var str_car;
	var bln_resultat = false;
	
	// Verifier si il y a quelque chose a verifier
	if(str_car_autorises != '') {
		bln_resultat = true;
		// Verifier si la chaine est vide ou non
		if (str_chaine.length > 0) 
		{
			// Verifier que chaque caractere de la chaine fait partie des caracteres valides
			for (i = 0; i < str_chaine.length; i++) 
			{
				str_car = str_chaine.charAt(i);
				if (str_car_autorises.indexOf(str_car) == -1) 
				{
					bln_resultat = false;
					break;
				}
			}
		}
	} else {
		bln_resultat = false;
	}
	return(bln_resultat);
}

// est_email() : Verifier si une chaine est une adresse email
//		entree :
//			- str_email (string) : la chaine a verifier
//		sortie : (true|false) indique si la chaine est une adresse email
function est_email(str_email) 
{
	// Preparer le filtre
	var filter  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	// Appliquer le filtre
	if (!filter.test(str_email)) 
	{
		return(false);
	}
	return(true);
}



// trim() : Enlever les espaces a gauche et a droite
//		entree :
//			- str_chaine (string) : la chaine a traiter
//		sortie : la chaine sans les espaces
function trim(str_chaine) {
	return str_chaine.replace(/^\s+|\s+$/g,"");
}

// ltrim() : Enlever les espaces a gauche
//		entree :
//			- str_chaine (string) : la chaine a traiter
//		sortie : la chaine sans les espaces
function ltrim(str_chaine) {
	return str_chaine.replace(/^\s+/,"");
}

// rtrim() : Enlever les espaces a droite
//		entree :
//			- str_chaine (string) : la chaine a traiter
//		sortie : la chaine sans les espaces
function rtrim(str_chaine) {
	return str_chaine.replace(/\s+$/,"");
}

// strtoupper() : mettre une chaine en majuscules
//		entree :
//			- str_text (string) : la chaine a traiter
//		sortie : la chaine en majuscules
function strtoupper(str_text) {
	return(str_text.toUpperCase());
}

// strtolower() : mettre une chaine en minuscules
//		entree :
//			- str_text (string) : la chaine a traiter
//		sortie : la chaine en minuscules
function strtolower(str_text) {
	return(str_text.toLowerCase());
}

// est_date() : verifier si une chaine est une date
//		entree :
//			- dtStr (string) : la chaine a verifier
//			- bMessage (true|false) : afficher ou non les messages d'erreur a l'interieur de 
//                                    la fonction (false par defaut)
//		sortie : (true|false)
function est_date(dtStr, bMessage) {
	var minYear=1900;
	var maxYear=2100;
	var dtCh= "/";
	var daysInMonth = DaysArray(12);
	var pos1=dtStr.indexOf(dtCh);
	var pos2=dtStr.indexOf(dtCh,pos1+1);
	
	// Depend on date format
	var strDay=dtStr.substring(0,pos1);
	var strMonth=dtStr.substring(pos1+1,pos2);
	var strYear=dtStr.substring(pos2+1);

	if(bMessage == null) {
		bMessage = true;
	}
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (pos1==-1 || pos2==-1){
		if(bMessage) {
			alert("The date format should be : yyyy-mm-dd")
		}
		return false
	}
	if (strMonth.length<1 || month<1 || month>12){
		if(bMessage) {
			alert("Please enter a valid month")
		}
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		if(bMessage) {
			alert("Please enter a valid day")
		}
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		if(bMessage) {
			alert("Please enter a valid 4 digit year between "+minYear+" and "+maxYear)
		}
		return false
	}
	
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || est_nombre(stripCharsInBag(dtStr, dtCh), 'entier', '')==false){
		if(bMessage) {
			alert("Please enter a valid date")
		}
		return false
	}
	return true
}


// Utilise par est_date()
function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

// Utilise par est_date()
function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   } 
   return this
}


