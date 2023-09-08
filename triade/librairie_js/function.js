
// module pour les messages error
function NoError() {
     return true;
}
window.onerror=NoError;

window.defaultStatus=' Service Triade -- http://www.triade-educ.com';

function getInnerText(elt) {
	var _innerText = elt.innerText;
	if (_innerText == undefined) {
  		_innerText = elt.innerHTML.replace(/<[^>]+>/g,"");
	}
	return _innerText;
}

// pour recharger la page //
function reload() {
         history.go(0)
}


function getRandomArbitrary(min,max) {
	min = Math.ceil(min);
  	max = Math.floor(max);
	return Math.floor(Math.random() * (max - min)) + min;
}



// pour afficher une fenetre au centre
function PopupCentrer(page,largeur,hauteur,options,nom_de_la_fenetre) {
  var top=(screen.height-hauteur)/2;
  var left=(screen.width-largeur)/2;
  window.open(page,nom_de_la_fenetre,"top="+top+",left="+left+",width="+largeur+",height="+hauteur+","+options);
}


function AfficheAttente() {
	document.getElementById('attenteDiv').style.visibility='visible';
	document['imgattente'].src='./image/commun/indicator.gif';
	document.getElementById('imgattente').src='./image/commun/indicator.gif';
	setTimeout("fini()",3000);
}

function fini() {
	document.getElementById('imgattente').src='./image/commun/indicator.gif';
	document['imgattente'].src='./image/commun/indicator.gif';
}


function eregi( chaine, find ) {
  var norm = new RegExp( find );
  return norm.exec( chaine )!=null;
 // x = prompt( 'Test','Chiffre' );
 // alert( eregi( x , "[\.]" ) );

}




// pour afficher une fenetre au centre l'attente
function PopupCentrerAttente(page,largeur,hauteur,options) {
  var top=(screen.height-hauteur)/2;
  var left=(screen.width-largeur)/2;
  attente=open(page,'attente',"top="+top+",left="+left+",width="+largeur+",height="+hauteur+","+options);
}

// repertoire racine exemple
function attente() {
    PopupCentrerAttente('attente.php','200','120','')
}


function attente_close() {
   attente.close();return true;
}

// ------------------------------------------------
// verif deja clicker 
var nbclic=0 // Initialisation à 0 du nombre de clic
function dejaclicker() {
    nbclic++; // nbclic+1
    if (nbclic>1) { // Plus de 1 clic
             alert(langfunc51);
     	     return false;
    } else { // 1 seul clic
            //alert("Premier Clic.");
	    return true;
    }
}


//fonction de validation d'après la longueur de la chaîne
function ValidLongueur(item,len) {
   drapeau = 1;
   return (item.length >= len);
}



////////////////////////////////////////////////////////
// affiche un message d'alerte
function error1(elem, text) {
// abandon si erreur déjà signalée
   if (errfound) return;
   window.alert(text);
   elem.select();
   elem.focus();
   errfound = true;
}

// verif du champ de recherche
function verif_recherche() {
     errfound = false;
     if (!ValidLongueur(document.recherche.search.value,3)){
      error1(document.recherche.search,langfunc2); }
return !errfound; /* vrai si il ya pas d'erreur */
}
////////////////////////////////////////////////////////
function delay(gap){ /* gap is in millisecs */
var then,now; then=new Date().getTime();
now=then;
while((now-then)<gap)
{now=new Date().getTime();}
}

////////////////////////////////////////////////////////

// fonction imprimer
function imprimer() {
        var ok=confirm(langfunc3);
        if (ok) {
                window.print();
        }
}

////////////////////////////////////////////////////////
// Fonction quitter session
function quitter_session() {
         var confirmation=confirm(langfunc1+langfunc0)
         if (confirmation) {
             location.href='index1.php?deconnexion';
             //parent.window.close();
         }
}

////////////////////////////////////////////////////////
// Fonction quitter avant session
function quitter_avant_session() {
         var confirmation=confirm(langfunc1+langfunc0)
         if (confirmation) {
             parent.window.close();
         }
}

//////////////////////////////////////////////////////
// function pour les bouton
function buttonMagic(value,lien,name,option,actionpossible) {
	document.write("<div style='float:left;margin-left:4px;' class='button1'>");
	document.write("<div class='btnleft1'></div>");
	document.write("<div class='btncenter1'><a href='#' onclick=\"open('"+lien+"','"+name+"','"+option+"')"+actionpossible+"\"  style='font-weight:bold;color:#000080'  >"+value+"</a></div>");
	document.write("<div class='btnright1'></div>");
	document.write("</div>");
}

function buttonMagicVATEL(value,lien,name,option,actionpossible) {
	document.write("<input type='button' onclick=\"open('"+lien+"','"+name+"','"+option+"')"+actionpossible+"\"    value=\""+value+"\" class='btn btn-primary btn-sm  vat-btn-footer' />");
}


function buttonMagic2(value,lien,name,option,disabled) {
	document.write("<div style='float:left;margin-left:4px;' class='button1'>");
	document.write("<div class='btnleft1'></div>");
	if (disabled == 1) {
		document.write("<div class='btncenter1'><a href='javascript:return(true)' disabled='disabled'   style='font-weight:bold;color:#000080' >"+value+"</a></div>");
	}else{
		document.write("<div class='btncenter1'><a href='#' onclick=\"open('"+lien+"','"+name+"','"+option+"');\"   style='font-weight:bold;color:#000080' >"+value+"</a></div>");
	}
	document.write("<div class='btnright1'></div>");
	document.write("</div>");
}

function buttonMagic2VATEL(value,lien,name,option,disabled) {
	if (disabled == 1) {
		document.write("<input type='button' disabled='disabled' class='btn btn-primary btn-sm  vat-btn-footer' value=\""+value+"\" ");
	}else{
		document.write("<input type='button' onclick=\"open('"+lien+"','"+name+"','"+option+"');\"   value=\""+value+"\" class='btn btn-primary btn-sm  vat-btn-footer' />");
	}
}


function buttonMagicSubmitAtt(value,name,attribut,bt) {
	document.write("<div style='float:left;margin-left:4px;' class='button1'>");
	document.write("<div class='btnleft1'></div>");
	document.write("<div class='btncenter1'>");
	var color="#000080";
	if (bt == 'ok') {
		document.write("&nbsp;&nbsp;<img src='./image/commun/ok.png' />");
		var color="blue";
	}
	if (bt == 'annul') {
		document.write("<img src='./image/commun/annul.png' />");
	}
	document.write("<input type='submit' style='font-weight:bold;color:"+color+"' "+attribut+" value='"+value+"' name='"+name+"' ></div>");
	document.write("<div class='btnright1'></div>");
	document.write("</div>");
}


function buttonMagicSubmitAttVATEL(value,name,attribut,bt) {
	document.write("<input type='submit' class='btn btn-primary btn-sm  vat-btn-footer' "+attribut+" value='"+value+"' name='"+name+"' ></div>");
}



function buttonMagicSubmit(value,name,bt) {
	document.write("<div style='float:left;margin-left:4px;' class='button1'>");
	document.write("<div class='btnleft1'></div>");
	document.write("<div class='btncenter1'>");
	var color="#000080";
	if (bt == 'ok') {
		document.write("<img src='./image/commun/ok.png' />");
		var color="blue";
	}
	if (bt == 'annul') {
		document.write("<img src='./image/commun/annul.png' />");
	}		
	document.write("<input type='submit' style='font-weight:bold;color:"+color+"' value='"+value+"' name='"+name+"' ></div>");
	document.write("<div class='btnright1'></div>");
	document.write("</div>");
}


function buttonMagicSubmitVATEL(value,name,bt) {
	var color="#000080";
	if (bt == 'ok') {
		// document.write("<img src='../image/commun/ok.png' />");
		var color="blue";
	}
	if (bt == 'annul') {
		// document.write("<img src='../image/commun/annul.png' />");
	}		
	document.write("<input type='submit' value='"+value+"' name='"+name+"' class='btn btn-primary btn-sm  vat-btn-footer' >");
}

function buttonMagicSubmitIdDivVATEL(value,name,bt,id,disabled) {
	var color="#000080";
	if (bt == 'ok') {
//		document.write("<img src='./image/commun/ok.png' />");
		var color="blue";
	}
	if (bt == 'annul') {
//		document.write("<img src='./image/commun/annul.png' />");
	}
	if (disabled == 1) {
		disabled="disabled='disabled'";
	}else{
		disabled="";
	}
	document.write("<input id='"+id+"' type='submit'  class='btn btn-primary btn-sm  vat-btn-footer' value='"+value+"' name='"+name+"' "+disabled+" />");
}



function buttonMagicSubmitIdDiv(value,name,bt,id,disabled) {
	document.write("<div style='float:left;margin-left:4px;' class='button1'>");
	document.write("<div class='btnleft1'></div>");
	document.write("<div class='btncenter1'>");
	var color="#000080";
	if (bt == 'ok') {
		document.write("<img src='./image/commun/ok.png' />");
		var color="blue";
	}
	if (bt == 'annul') {
		document.write("<img src='./image/commun/annul.png' />");
	}
	if (disabled == 1) {
		disabled="disabled='disabled'";
	}else{
		disabled="";
	}
	document.write("<input id='"+id+"' type='submit' style='font-weight:bold;color:"+color+"' value='"+value+"' name='"+name+"' "+disabled+" ></div>");
	document.write("<div class='btnright1'></div>");
	document.write("</div>");
}

function buttonMagicSubmit2VATEL(value,name,action) {
	document.write("<input type='submit' class='btn btn-primary btn-sm  vat-btn-footer' value='"+value+"' name='"+name+"' onclick=\"this.value='"+action+"'\" >");
}

function buttonMagicSubmit2(value,name,action) {
	document.write("<div style='float:left;margin-left:4px;' class='button1'>");
	document.write("<div class='btnleft1'></div>");
	document.write("<div class='btncenter1'><input type='submit' style='font-weight:bold;color:#000080' value='"+value+"' name='"+name+"' onclick=\"this.value='"+action+"'\" ></div>");
	document.write("<div class='btnright1'></div>");
	document.write("</div>");
}

function buttonMagicSubmit3VATEL(value,name,action) {
	document.write("<input type='submit' value='"+value+"' name='"+name+"' "+action+" class='btn btn-primary btn-sm  vat-btn-footer' />");
}


function buttonMagicSubmit3(value,name,action) {
	document.write("<div style='float:left;margin-left:4px;' class='button1'>");
	document.write("<div class='btnleft1'></div>");
	document.write("<div class='btncenter1'><input type='submit' style='font-weight:bold;color:#000080' value='"+value+"' name='"+name+"' "+action+"></div>");
	document.write("<div class='btnright1'></div>");
	document.write("</div>");
}

function buttonMagicSubmit4(value,name,action) {
	document.write("<div style='float:left;margin-left:4px;' class='button1'>");
	document.write("<div class='btnleft1'></div>");
	document.write("<div class='btncenter1'><input type='button' style='font-weight:bold;color:#000080' value=\""+value+"\" name='"+name+"' onclick=\""+action+"\"></div>");
	document.write("<div class='btnright1'></div>");
	document.write("</div>");
}

function buttonMagicSubmit4VATEL(value,name,action) {
	document.write("<input type='button' class='btn btn-primary btn-sm  vat-btn-footer' value=\""+value+"\" name='"+name+"' onclick=\""+action+"\" />");
}


function buttonMagicReactualise() {
	document.write("<div style='float:left;margin-left:4px;' class='button1'>");
	document.write("<div class='btnleft1'></div>");
	document.write("<div class='btncenter1'><input type='button' style='font-weight:bold;color:#000080' value='"+langfunc66+"' onclick=\"history.go(0);\"></div>");
	document.write("<div class='btnright1'></div>");
	document.write("</div>");
}


function buttonMagicReactualiseVATEL() {
	document.write("<input type='button' class='btn btn-primary btn-sm  vat-btn-footer'  value='"+langfunc66+"' onclick=\"history.go(0);\">");
}

// test dans le boutont "Modifier" 
function buttonMagicPrecedent() {
	document.write("<div style='float:left;margin-left:4px;' class='button1'>");
	document.write("<div class='btnleft1'></div>");
	document.write("<div class='btncenter1'><input type='button' style='font-weight:bold;color:#000080' value='"+langfunc65+"' onclick=\"history.go(-1);\"></div>");
	document.write("<div class='btnright1'></div>");
	document.write("</div>");
}

// test dans le boutont "Retour" 
function buttonMagicPrecedent2() {
	document.write("<div style='float:left;margin-left:4px;' class='button1'>");
	document.write("<div class='btnleft1'></div>");
	document.write("<div class='btncenter1'><input type='button' style='font-weight:bold;color:#000080' value='"+langfunc63+"' onclick=\"history.go(-1);\"></div>");
	document.write("<div class='btnright1'></div>");
	document.write("</div>");
}

// test dans le boutont "Retour" 
function buttonMagicPrecedentNbSaut(nb) {
	document.write("<div style='float:left;margin-left:4px;' class='button1'>");
	document.write("<div class='btnleft1'></div>");
	document.write("<div class='btncenter1'><input type='button' style='font-weight:bold;color:#000080' value='"+langfunc63+"' onclick=\"history.go("+nb+");\"></div>");
	document.write("<div class='btnright1'></div>");
	document.write("</div>");
}

function buttonMagicImprimer() {
	document.write("<div style='float:left;margin-left:4px;' class='button1'>");
	document.write("<div class='btnleft1'></div>");
	document.write("<div class='btncenter1'><input type='button' style='font-weight:bold;color:#000080' value='"+langfunc64+"' onclick=\"imprimer();\"></div>");
	document.write("<div class='btnright1'></div>");
	document.write("</div>");
}


function buttonMagicFermeture() {
	document.write("<div style='float:left;margin-left:4px;' class='button1'>");
	document.write("<div class='btnleft1'></div>");
	document.write("<div class='btncenter1'><input type='button' style='font-weight:bold;color:#000080' value='"+langfunc62+"' onclick=\"parent.window.close();\"></div>");
	document.write("<div class='btnright1'></div>");
	document.write("</div>");
}

function buttonMagicFermetureVATEL() {
	document.write("<input type='button' value='"+langfunc62+"' onclick=\"parent.window.close();\" class='btn btn-primary btn-sm  vat-btn-footer' />");
}

function buttonMagicRetour(lien,fen) {
	document.write("<div style='float:left;margin-left:4px;' class='button1'>");
	document.write("<div class='btnleft1'></div>");
	document.write("<div class='btncenter1'><input type='button' style='font-weight:bold;color:#000080' value='"+langfunc63+"' onclick=\"open('"+lien+"','"+fen+"','');\"></div>");
	document.write("<div class='btnright1'></div>");
	document.write("</div>");
}

function buttonMagicRetourVATEL(lien,fen) {
	document.write("<input type='button' class='btn btn-primary btn-sm  vat-btn-footer'  value='"+langfunc63+"' onclick=\"open('"+lien+"','"+fen+"','');\">");
}


function buttonMagicRetour2(lien,fen,nom) {
	document.write("<div style='float:left;margin-left:4px;' class='button1'>");
	document.write("<div class='btnleft1'></div>");
	document.write("<div class='btncenter1'><input type='button' style='font-weight:bold;color:#000080' value='"+nom+"' onclick=\"open('"+lien+"','"+fen+"','');\"></div>");
	document.write("<div class='btnright1'></div>");
	document.write("</div>");
}

function buttonMagicRetour2VATEL(lien,fen,nom) {
	document.write("<input type='button'  class='btn btn-primary btn-sm  vat-btn-footer' value='"+nom+"' onclick=\"open('"+lien+"','"+fen+"','');\">");
}


function buttonMagic3(nom,lien) {
	document.write("<div style='float:left;margin-left:4px;' class='button1'>");
	document.write("<div class='btnleft1'></div>");
	document.write("<div class='btncenter1'><input type='button' style='font-weight:bold;color:#000080' value='"+nom+"' onclick=\""+lien+"\"></div>");
	document.write("<div class='btnright1'></div>");
	document.write("</div>");
}


function buttonMagic3VATEL(nom,lien) {
        document.write("<input type='button' value='"+nom+"' onclick=\""+lien+"\"   class='btn btn-primary btn-sm  vat-btn-footer'  >");
}



function buttonMagicAlert(nom,commentaire) {
	document.write("<div style='float:left;margin-left:4px;' class='button1'>");
	document.write("<div class='btnleft1'></div>");
	document.write("<div class='btncenter1'><input type='button' style='font-weight:bold;color:#000080' value='"+nom+"' onclick='alert(\""+commentaire+"\")' ></div>");
	document.write("<div class='btnright1'></div>");
	document.write("</div>");
}



// ----------------------------------------------
function ChangeStatus(formulaire,regagree,validation) {
	var1=formulaire+"."+regagree;
	var1=eval(var1);
	var2=formulaire+"."+validation;
	var2=eval(var2);
	if (var1.checked == true ) {
		var2.disabled=false ;
	}
	if (var1.checked == false ) {
		var2.disabled=true ;
	}
}

//------------------------------------------------
// en dessous à verifier avant nouvelle version

function pasdispo(){
	alert(langfunc50); // verifier aussi
}
function pasdispo1(){
	alert(langfunc51); // verifier aussi
}


function motifbulletin(id,val) {
        if (val == "0") {
                val="";
        }
        var motif="document.formulaire.saisie_text_"+id;
        var motif2=eval(motif);
	var text=tab[val];
	if (text == undefined) {
		text="";
	}
        motif2.value=motif2.value+", "+text;
}



function Compter(Target, compteur,nb) {
	//  onkeyup="Compter(this,this.form.CharRestant,'250');"
	var Nombre_Caracteres_Maximum = nb;
	var StrLen;
    	if (StrLen >= Nombre_Caracteres_Maximum ) {
    		Target.value = Target.value.substring(0,Nombre_Caracteres_Maximum);
    	}
    	StrLen = Target.value.length;
    	compteur.value = Nombre_Caracteres_Maximum-StrLen;
}


function compter(f,max,sortie) {
	var txt=f.value;
	var nb=txt.length;
	if (nb>max) { 
		alert("Pas plus de "+max+" caractères dans ce champ");
		f.value=txt.substring(0,max);
		nb=max;
	}
	sortie.value=nb;
}

function timer() {
	compter(document.forms["formulaire"]);
	setTimeout("timer()",100);
}


function verifDate(mot1) {
	erreur=false;
	mot=eval(mot1);
    	if (mot.length!=10){
		alert("Veuillez introduire votre date au format JJ/MM/AAAA (exemple : 12/02/2010)");
		Erreur=true;
		return Erreur;
	}else{
    		motjour=mot.substring(0,2);motmois=mot.substring(3,5);
    		motan=mot.substring(6,10);motsep=mot.charAt(2)+mot.charAt(5);}
	    	if (isNaN(motjour+motmois+motan)|| motan>3000 ||motan<1900 || motsep!="//"){
			alert("Veuillez introduire votre date au format JJ/MM/AAAA (exemple : 12/02/2010)");
			Erreur=true;
			return Erreur;
		}else {
			choix="";    
    			if (motan-(parseInt(motan/4)*4)==0) {choix="bi"};
	    		if (motan-(parseInt(motan/4)*4)==0&&motan-(parseInt(motan/100)*100)==0)	{choix=""};
    			if (motan-(parseInt(motan/4)*4)==0&&motan-(parseInt(motan/400)*400)==0) {choix="bi"}
    			switch (motmois){
    			case "01": if (motjour<1 || motjour>31){
					alert("Le mois de Janvier comporte 31 jours, veuillez choisir une date comprise en 1 et 31.");
					Erreur=true;
					return Erreur; 
				    };
    				    break;
		    	case"02": if(choix=="bi"){
					if(motjour<1 || motjour>29){
						alert("Le mois de Février comporte 29 jours car l'année choisie est bissextile.\nVeuillez choisir une date comprise en 1 et 29.");
						Erreur=true;
						return Erreur;
					}
				   }else{
					if(motjour<1 || motjour>28){
						alert("Le mois de Février comporte 28 jours car l'année choisie n'est pas bissextile.\nVeuillez choisir une date comprise en 1 et 28.");
						Erreur=true;
						return Erreur;
					}
				};
		    		break;
	    		case "03" : if(motjour<1 || motjour>31){alert("Le mois de Mars comporte 31 jours, veuillez choisir une date comprise en 1 et 31.");
						Erreur=true;
						return Erreur;
				     };
		    		     break;
    			case "04":
					if(motjour<1 || motjour>30){
						alert("Le mois de Avril comporte 30 jours, veuillez choisir une date comprise en 1 et 30.");
						Erreur=true;
						return Erreur;
					};
    					break;
    			case "05":
					if(motjour<1 || motjour>31){
						alert("Le mois de Mai comporte 31 jours, veuillez choisir une date comprise en 1 et 31.");
						Erreur=true;
						return Erreur;
					};
    					break;
    			case "06":
					if(motjour<1 || motjour>30){
						alert("Le mois de Juin comporte 30 jours, veuillez choisir une date comprise en 1 et 30.");
						Erreur=true;
						return Erreur;
					};
    					break;
    			case "07":
					if(motjour<1 || motjour>31){
						alert("Le mois de Juillet comporte 31 jours, veuillez choisir une date comprise en 1 et 31.");
						Erreur=true;
						return Erreur;
					};
    					break;
    			case "08":
					if(motjour<1 || motjour>31){
						alert("Le mois de Août comporte 31 jours, veuillez choisir une date comprise en 1 et 31.");
						Erreur=true;
						return Erreur;
					};
    					break;
		    	case "09":
					if(motjour<1 || motjour>30){
						alert("Le mois de Septembre comporte 30 jours, veuillez choisir une date comprise en 1 et 30.");
						Erreur=true;
						return Erreur;
					};
    					break;
    			case "10":
					if(motjour<1 || motjour>31){
						alert("Le mois de Octobre comporte 31 jours, veuillez choisir une date comprise en 1 et 31.");
						Erreur=true;
						return Erreur;
					};
    					break;
    			case "11":
					if(motjour<1 || motjour>30){
						alert("Le mois de Novembre comporte 30 jours, veuillez choisirune date comprise en 1 et 30.");
						Erreur=true;
						return Erreur;
					};
    					break;
    			case "12":
					if(motjour<1 || motjour>31){
						alert("Le mois de Décembre comporte 31 jours, veuillez choisir une date comprise en 1 et 31.");
						Erreur=true;
						return Erreur;
					};
    					break;
    			default:
					alert("Le mois que vous avez entré n'est pas valide. Choisissez un mois compris entre 1 et 12.");
					Erreur=true;
					return Erreur;	
		}
		return Erreur;
	}
}

function verifHeure(mot1) {
	erreur=false;
	mot=eval(mot1);
    	if (mot.length!=5){
		alert("Veuillez introduire votre heure au format hh:mm ");
		Erreur=true;
		return Erreur;
	}else{
    		motheure=mot.substring(0,2);motminute=mot.substring(3,5);
		motsep=mot.charAt(2);
	}
	if (isNaN(motheure+motminute) || motsep!=":"){
		alert("Veuillez introduire votre heure au format hh:mm ");
		Erreur=true;
		return Erreur;
	}else {
		if (motminute > 60) {
			alert("Veuillez introduire votre heure au format hh:mm ");
			Erreur=true;
			return Erreur;
		}	
		if (motheure > 24) {
			alert("Veuillez introduire votre heure au format hh:mm ");
			Erreur=true;
			return Erreur;
		}	
	}
	return Erreur;
}

function verifHeure2(mot1,champs,info1) {

   var info=eval(info1);
   var info2=info.options[info.selectedIndex].value;

if (info2 == "retard") {
	var elem=eval(champs);
	mot=eval(mot1);
    	if (mot.length!=5){
		alert("Veuillez introduire votre heure au format hh:mm ");
		elem.select();
		elem.focus();
		return;
	}else{
    		motheure=mot.substring(0,2);motminute=mot.substring(3,5);
		motsep=mot.charAt(2);
	}
	if (isNaN(motheure+motminute) || motsep!=":"){
		alert("Veuillez introduire votre heure au format hh:mm ");
		elem.select();
		elem.focus();
		return;
	}else {
		if (motminute > 60) {
			alert("Veuillez introduire votre heure au format hh:mm ");
			elem.select();
			elem.focus();
			return;
		}	
		if (motheure > 24) {
			alert("Veuillez introduire votre heure au format hh:mm ");
			elem.select();
			elem.focus();
			return;
		}	
	}
}
if (info2 == "absent") {
	var mot=mot1;
	var elem=eval(champs);
    	if (mot.length!=10){
		alert("Veuillez introduire votre date au format JJ/MM/AAAA (exemple : 12/02/2008)");
		elem.select();
		elem.focus();
		return;
	}else{
    		motjour=mot.substring(0,2);motmois=mot.substring(3,5);
    		motan=mot.substring(6,10);motsep=mot.charAt(2)+mot.charAt(5);}
	    	if (isNaN(motjour+motmois+motan)|| motan>3000 ||motan<1900 || motsep!="//"){
			alert("Veuillez introduire votre date au format JJ/MM/AAAA (exemple : 12/02/2007)");
			elem.select();
			elem.focus();
			return;
		}else {
			choix="";    
    			if (motan-(parseInt(motan/4)*4)==0) {choix="bi"};
	    		if (motan-(parseInt(motan/4)*4)==0&&motan-(parseInt(motan/100)*100)==0)	{choix=""};
    			if (motan-(parseInt(motan/4)*4)==0&&motan-(parseInt(motan/400)*400)==0) {choix="bi"}
    			switch (motmois){
    			case "01": if (motjour<1 || motjour>31){
					alert("Le mois de Janvier comporte 31 jours, veuillez choisir une date comprise en 1 et 31.");
					elem.select();
					elem.focus();
					return;
				    };
    				    break;
		    	case"02": if(choix=="bi"){
					if(motjour<1 || motjour>29){
						alert("Le mois de Février comporte 29 jours car l'année choisie est bissextile.\nVeuillez choisir une date comprise en 1 et 29.");
						elem.select();
						elem.focus();
						return;
					}
				   }else{
					if(motjour<1 || motjour>28){
						alert("Le mois de Février comporte 28 jours car l'année choisie n'est pas bissextile.\nVeuillez choisir une date comprise en 1 et 28.");
						elem.select();
						elem.focus();
						return;
					}
				};
		    		break;
	    		case "03" : if(motjour<1 || motjour>31){alert("Le mois de Mars comporte 31 jours, veuillez choisir une date comprise en 1 et 31.");
						elem.select();
						elem.focus();
						return;
				     };
		    		     break;
    			case "04":
					if(motjour<1 || motjour>30){
						alert("Le mois de Avril comporte 30 jours, veuillez choisir une date comprise en 1 et 30.");
						elem.select();
						elem.focus();
						return;
					};
    					break;
    			case "05":
					if(motjour<1 || motjour>31){
						alert("Le mois de Mai comporte 31 jours, veuillez choisir une date comprise en 1 et 31.");
						elem.select();
						elem.focus();
						return;
					};
    					break;
    			case "06":
					if(motjour<1 || motjour>30){
						alert("Le mois de Juin comporte 30 jours, veuillez choisir une date comprise en 1 et 30.");
						elem.select();
						elem.focus();
						return;
					};
    					break;
    			case "07":
					if(motjour<1 || motjour>31){
						alert("Le mois de Juillet comporte 31 jours, veuillez choisir une date comprise en 1 et 31.");
						elem.select();
						elem.focus();
						return;
					};
    					break;
    			case "08":
					if(motjour<1 || motjour>31){
						alert("Le mois de Août comporte 31 jours, veuillez choisir une date comprise en 1 et 31.");
						elem.select();
						elem.focus();
						return;
					};
    					break;
		    	case "09":
					if(motjour<1 || motjour>30){
						alert("Le mois de Septembre comporte 30 jours, veuillez choisir une date comprise en 1 et 30.");
						elem.select();
						elem.focus();
						return;
					};
    					break;
    			case "10":
					if(motjour<1 || motjour>31){
						alert("Le mois de Octobre comporte 31 jours, veuillez choisir une date comprise en 1 et 31.");
						elem.select();
						elem.focus();
						return;
					};
    					break;
    			case "11":
					if(motjour<1 || motjour>30){
						alert("Le mois de Novembre comporte 30 jours, veuillez choisirune date comprise en 1 et 30.");
						elem.select();
						elem.focus();
						return;
					};
    					break;
    			case "12":
					if(motjour<1 || motjour>31){
						alert("Le mois de Décembre comporte 31 jours, veuillez choisir une date comprise en 1 et 31.");
						elem.select();
						elem.focus();
						return;
					};
    					break;
    			default:
					alert("Le mois que vous avez entré n'est pas valide. Choisissez un mois compris entre 1 et 12.");
					elem.select();
					elem.focus();
					return;
		}
	}
   }
}


// onKeyPress="onlyChar(event)"
// N'autorise que [0-9] et / comme saisie
function onlyChar(event) {
	_code = (event.which) ? event.which : event.keyCode ;
   	if ((_code < 46) || (_code > 57) || (_code == ""))  {
        	if (_code != 8) {
            		if (event.preventDefault) {
                     		event.preventDefault();
            		}else{
                     		event.returnValue=false;
            		}
        	}
    	} 
}


// onKeyPress="onlyChar2(event)"
// N'autorise que [0-9] et : et / comme saisie
function onlyChar2(event) {
        _code = (event.which) ? event.which : event.keyCode ;
        if ((_code < 47) || (_code > 58) || (_code == ""))  {
                if (_code != 8) {
            		if (event.preventDefault) {
                     		event.preventDefault();
            		}else{
                     		event.returnValue=false;
            		}
        	}
        }
}


// onClick="DisplayLigne('tr<?php print $i ?>');"
function DisplayLigne(id) {
	if (document.getElementById(id).style.backgroundColor == '#c0c0c0') {
		document.getElementById(id).style.backgroundColor='';
	}else{
		document.getElementById(id).style.backgroundColor='#C0C0C0';
	}	
}


//onChange="DisplayLigne2('tr<?php print $i ?>',this.value);"
function DisplayLigne2(id,val) {
	if ((document.getElementById(id).style.backgroundColor == '#c0c0c0') && (val == '0')) {
		document.getElementById(id).style.backgroundColor='';
	}else{
		document.getElementById(id).style.backgroundColor='#C0C0C0';
	}	
}

function filtreAjax(commentaire) {
 	commentaire=commentaire.replace("&",""); 	
	return commentaire;
}

// ------------------------------------------------------------
function MontrerMenu() { 
// Disance par rapport aux bords de la fenetre 
var EspaceDroit = document.body.clientWidth-event.clientX; 
var EspaceBas = document.body.clientHeight-event.clientY; 

// Affichage du menu suivant la position du curseur 
if (EspaceDroit < CMenu.offsetWidth) 
CMenu.style.left = document.body.scrollLeft + event.clientX - CMenu.offsetWidth; 
else 
CMenu.style.left = document.body.scrollLeft + event.clientX; 

if (EspaceBas < CMenu.offsetHeight){ 
CMenu.style.top = document.body.scrollTop + event.clientY - CMenu.offsetHeight; } 
else{ 
CMenu.style.top = document.body.scrollTop + event.clientY; } 

// Affichage du menu 
CMenu.style.visibility = "visible"; 
return false; 
} 

function MasquerMenu(){ 
CMenu.style.visibility = "hidden"; 
} 
//------------------------------------------------------------

function number_format(number, decimals, dec_point, thousands_sep) {
    // *     example 1: number_format(1234.56);
    // *     returns 1: '1,235'
    // *     example 2: number_format(1234.56, 2, ',', ' ');
    // *     returns 2: '1 234,56'
    // *     example 3: number_format(1234.5678, 2, '.', '');
    // *     returns 3: '1234.57'
    // *     example 4: number_format(67, 2, ',', '.');
    // *     returns 4: '67,00'
    // *     example 5: number_format(1000);
    // *     returns 5: '1,000'
    // *     example 6: number_format(67.311, 2);
    // *     returns 6: '67.31'
    // *     example 7: number_format(1000.55, 1);
    // *     returns 7: '1,000.6'
    // *     example 8: number_format(67000, 5, ',', '.');
    // *     returns 8: '67.000,00000'
    // *     example 9: number_format(0.9, 0);
    // *     returns 9: '1'
    // *    example 10: number_format('1.20', 2);
    // *    returns 10: '1.20'
    // *    example 11: number_format('1.20', 4);
    // *    returns 11: '1.2000'
    // *    example 12: number_format('1.2000', 3);
    // *    returns 12: '1.200'
    // *    example 13: number_format('1 000,50', 2, '.', ' ');
    // *    returns 13: '100 050.00'
    number = (number+'').replace(',', '').replace(' ', '');
    var n = !isFinite(+number) ? 0 : +number, 
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}


function preg_replace (array_pattern, array_pattern_replace, my_string)  {
        var new_string = String (my_string);
        for (i=0; i<array_pattern.length; i++) {
                var reg_exp= RegExp(array_pattern[i], "gi");
                var val_to_replace = array_pattern_replace[i];
                new_string = new_string.replace (reg_exp, val_to_replace);
        }
        return new_string;
}

function dateForm(date1) { // recoit yyyy-mm-dd renvoi dd/mm/yyyy
        var jour=date1.substring(8,10);
        var mois=date1.substring(5,7);
        var annee=date1.substring(0,4);
        return(jour+"/"+mois+"/"+annee);
}


function openFenCentre(page,idfen,width,height) {
        if(window.innerWidth)   {
                var left = (window.innerWidth-width)/2;
                var top = (window.innerHeight-height)/2;
        }else{
                var left = (document.body.clientWidth-width)/2;
                var top = (document.body.clientHeight-height)/2;
        }
        window.open(page,idfen,'menubar=no, scrollbars=no, top='+top+', left='+left+',width='+width+', height='+height+'');
}


 
var N=navigator.appName; var V=navigator.appVersion;
var version="?"; var nom=N; var os="?"; var langue="?";
if (N=="Microsoft Internet Explorer") {
        langue=navigator.systemLanguage
        version=V.substring(V.indexOf("MSIE",0)+5,V.indexOf(";",V.indexOf("MSIE",0)));
        if (V.indexOf("Win",0)>0) {
                if ( V.indexOf(";",V.indexOf("Win",0)) > 0 ) {
                        os=V.substring(V.indexOf("Win",0),V.indexOf(";",V.indexOf("Win",0)));
                } else {
                        os=V.substring(V.indexOf("Win",0),V.indexOf(")",V.indexOf("Win",0)));
                }
        }
        if (V.indexOf("Mac",0)>0) {
                os="Macintosh";
                version=V.substring(V.indexOf("MSIE",0)+5,V.indexOf("?",V.indexOf("MSIE",0)));
        }
}
if (N=="Opera") {
        langue=navigator.language;
        version=V.substring(0,V.indexOf("(",0));
        os=V.substring(V.indexOf("(",0)+1,V.indexOf(";",0));
}
if (N=="Netscape") {
        langue=navigator.language;
        if (navigator.vendor=="") { // Mozilla
                version=(V.substring(0,V.indexOf("(",0)));
                nom="Mozilla";
                if (V.indexOf("Mac",0)>0) {
                        os="Macintosh";
                }
                if (V.indexOf("Linux",0)>0) {
                        os="Linux";
                }
                if (V.indexOf("Win",0)>0) {
                        os=V.substring(V.indexOf("Win",0),V.indexOf(";",V.indexOf("Win",0)));
                }
                if (version==5) {
                        version="1";
                }
                if (navigator.oscpu) {os=navigator.oscpu;}
        } else {        // NS 4 ou 6
                version=(V.substring(0,V.indexOf("(",0)));
                if (V.indexOf("Mac",0)>0) {
                        os="Macintosh";
                }
                if (V.indexOf("Linux",0)>0) {
                        os="Linux";
                }
                if (V.indexOf("Win",0)>0) {
                        os=V.substring(V.indexOf("Win",0),V.indexOf(";",V.indexOf("Win",0)));
                }
                if (version==5) {
                        version="6.0";
                        if (navigator.vendorSub!="") {version=navigator.vendorSub;}
                }
                if (navigator.oscpu) {os=navigator.oscpu;}
        }
}


var N=navigator.appName; var V=navigator.appVersion;

var version="?"; var nom=N; var os="?"; var langue="?";
if (N=="Microsoft Internet Explorer") {
	langue=navigator.systemLanguage
	version=V.substring(V.indexOf("MSIE",0)+5,V.indexOf(";",V.indexOf("MSIE",0)));
	if (V.indexOf("Win",0)>0) {
		if ( V.indexOf(";",V.indexOf("Win",0)) > 0 ) {
			os=V.substring(V.indexOf("Win",0),V.indexOf(";",V.indexOf("Win",0)));
		} else {
			os=V.substring(V.indexOf("Win",0),V.indexOf(")",V.indexOf("Win",0)));
		}
	}
	if (V.indexOf("Mac",0)>0) {
		os="Macintosh";
		version=V.substring(V.indexOf("MSIE",0)+5,V.indexOf("?",V.indexOf("MSIE",0)));
	}
}
if (N=="Opera") {
	langue=navigator.language;
	version=V.substring(0,V.indexOf("(",0));
	os=V.substring(V.indexOf("(",0)+1,V.indexOf(";",0));
}		
if (N=="Netscape") {
	langue=navigator.language;
	if (navigator.vendor=="") { // Mozilla
		version=(V.substring(0,V.indexOf("(",0)));
		nom="Mozilla";
		if (V.indexOf("Mac",0)>0) {
			os="Macintosh";
		}
		if (V.indexOf("Linux",0)>0) {
			os="Linux";
		}
		if (V.indexOf("Win",0)>0) {
			os=V.substring(V.indexOf("Win",0),V.indexOf(";",V.indexOf("Win",0)));
		}
		if (version==5) {
			version="1";
		}
		if (navigator.oscpu) {os=navigator.oscpu;}
	} else {	// NS 4 ou 6
		version=(V.substring(0,V.indexOf("(",0)));
		if (V.indexOf("Mac",0)>0) {
			os="Macintosh";
		}
		if (V.indexOf("Linux",0)>0) {
			os="Linux";
		}
		if (V.indexOf("Win",0)>0) {
			os=V.substring(V.indexOf("Win",0),V.indexOf(";",V.indexOf("Win",0)));
		}
		if (version==5) {
			version="6.0";
			if (navigator.vendorSub!="") {version=navigator.vendorSub;}
		}
		if (navigator.oscpu) {os=navigator.oscpu;}
	}
}


var IB = new Object;
var posX = 0;
var posY = 0;
var xOffset = 10;
var yOffset = 10;

function InitBulle(ColTexte,ColFond,ColContour,NbPixel) {
	IB.ColTexte = ColTexte;
	IB.ColFond = ColFond;
	IB.ColContour = ColContour;
	IB.NbPixel = NbPixel;

	if (document.layers) {
		window.captureEvents(Event.MOUSEMOVE);
		window.onMouseMove = getMousePos;
		document.write('<layer name="bulle" top="0" left="0" visibility="hide"></layer>');
		document.write('<layer name="bullep" top="0" left="0" visibility="hide"></layer>');
	}
	else if (document.all) {
		document.onmousemove = getMousePos;
		document.write('<div id="bulle" style="position:absolute; top:0; left:0; z-index:1000000; visibility:hidden;"></div>');
		document.write('<div id="bullep" style="position:absolute; top:0; left:0; z-index:1000000; visibility:hidden;"></div>');
	}
	//modif CL 09/2001 - NS6 : celui-ci ne supporte plus document.layers mais document.getElementById
	else if (document.getElementById) {
		document.onmousemove = getMousePos;
		document.write('<div id="bulle" style="position:absolute; top:0px; left:0px; z-index:1000000;  visibility:hidden;"></div>');
		document.write('<div id="bullep" style="position:absolute; top:0px; left:0px; z-index:1000000;  visibility:hidden;"></div>');
	}
}


var RposX;
var RposY;

function pos(evt) {
  if(!evt) var e=window.event;
  RposX=evt.clientX;
  RposY=evt.clientY;
}

function AffBulleRadioAvecQuit(strTitre,strIcone,texte) {
	// image/commun/stop.jpg 
	// image/commun/info.jpg 
	// image/commun/warning.jpg 

	var contenu = '<table Id="HelpTable" style="width: 335px;" cellspacing="0" cellpadding="0">';
	contenu += '<tr style="height: 30px;">';
	contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_HG.gif); background-repeat: no-repeat;"></td>';
	contenu +=  '<td style="width: 30px; background: url(./image/commun/Bulle_HC1.gif); background-repeat: no-repeat;"></td>';
	contenu +=  '<td style="width: 285px; background: url(./image/commun/Bulle_HC2.gif); background-repeat: repeat-x;"></td>';
	contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_HD.gif); background-repeat: no-repeat;"></td>';
	contenu += '</tr>';

	if ( strTitre != "" ){
		contenu += '<tr style="height: 30px;">';
		contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_CG.gif); background-repeat: repeat-y;"></td>';
		contenu +=  '<td colspan="2" style="width: 305px; text-align: left; vertical-align: middle; background: #FBFFD9; font-size: 14px; font-family: Tahoma;">';
		contenu +=   '<img src="' + strIcone + '" style="border: 0; width: 15px; height: 15px; margin-right: 10px;" alt="">';
		contenu +=   '<b>' + strTitre + '</b>';
		contenu +=  '</td>';
		contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_CD.gif); background-repeat: repeat-y;"></td>';
		contenu += '</tr>';
	}

	contenu +=  '<tr> ';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_CG.gif); background-repeat: repeat-y;"></td>';
	contenu +=   '<td colspan="2" style="width: 305px; background: #FBFFD9; font-family: Arial; font-size: 10px;"><div id="id1" style="overflow:auto; width: 300px;">' + texte + '</div></td>';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_CD.gif); background-repeat: repeat-y;"></td>';
	contenu +=  '</tr>';

	contenu +=  '<tr style="height: 10px;">';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_BG.gif); background-repeat: no-repeat;"></td>';
	contenu +=   '<td colspan="2" style="width: 305px; background: url(./image/commun/Bulle_BC.gif); background-repeat: repeat-x;"></td>';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_BD.gif); background-repeat: no-repeat;"></td>';
	contenu +=  '</tr>';
	contenu += '</table>';

	var finalPosX = posX - xOffset;

	if (finalPosX<0) finalPosX = 0;

	if (document.layers) {
		document.layers["bulle"].document.write(contenu);
		document.layers["bulle"].document.close();
		document.layers["bulle"].top = posY + yOffset;
		document.layers["bulle"].left = finalPosX;
		document.layers["bulle"].visibility = "show";
	}
	else if (document.all) {
		//var f=window.event;
		//doc=document.body.scrollTop;
		bulle.innerHTML = contenu;
		document.all["bulle"].style.top = posY + yOffset;
		document.all["bulle"].style.left = finalPosX;//f.x-xOffset;
		document.all["bulle"].style.visibility = "visible";
	}
	//modif CL 09/2001 - NS6 : celui-ci ne supporte plus document.layers mais document.getElementById
	else if (document.getElementById) {
		document.getElementById("bulle").innerHTML = contenu;
		document.getElementById("bulle").style.top = posY + yOffset;
		document.getElementById("bulle").style.left = finalPosX;
		document.getElementById("bulle").style.visibility = "visible";
	}
}

InitBulleRadio('#000000','#FCE4BA','red',1);

function HideBulleRadio() {
	if (document.layers) { document.layers["bulle"].visibility = "hide"; }
	else if (document.all) { document.all["bulle"].style.visibility = "hidden"; }
	else if (document.getElementById) { document.getElementById("bulle").style.visibility = "hidden"; }
}


function error3(item,text) {
        window.alert(text);
        item.value="";
}

function controlEmail(item) {
        var alerteMail='0';
        var myreg=/hotmail/i;
        if (myreg.test(item)) { alerteMail='1'; }
        myreg=/live/i;
        if (myreg.test(item)) { alerteMail='1'; }
        if (alerteMail == '1') {
                return(false);
        }else{
                return(true);
        }
}


function verifEmail(item) {
        if (!controlEmail(item.value)){
        error3(item,"Merci de choisir une autre adresse email.\n\nVotre email n'est pas reconnu par nos serveurs de messagerie.\n\n"); }
}


function encode_utf8(s) {
	return unescape(encodeURIComponent(s));
}

function decode_utf8(s) {
	return decodeURIComponent(escape(s));
}


