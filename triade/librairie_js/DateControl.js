// DateControl v1.0 - Aurelien
//
// v1.0 :           18/01/2003
//   - DateControl  : v1.0
//   - DC_Instance  : v1.0
//   - DC_Format    : v1.0

// Array des delimiteurs :
var aDel =
[
 "/",
 "-",
 ":"
];

DateControl = function() {

 // Array de stockage des differentes
 // instances de controles de date
 this.gDC = new Array();

 // Methode d'ajout d'une instance
 this.Add = function( inNAME, inFORM )
 {
  this.gDC[this.gDC.length] = new DC_Instance(inNAME, inFORM);
 };

 // Methode de recuperation d'une instance
 this.GetObject = function( inNAME )
 {
  var i = 0, oObj = null;
  for (i = 0; i < this.gDC.length; i++) {
   if (this.gDC[i].name == inNAME) {
    oObj = this.gDC[i];
    break;
   }
  };
  return oObj;
 };

 // Methode publique pour la verification de date
 this.IsValidDate = function( inNAME )
 {
  var oObj = this.GetObject(inNAME);
  if (oObj) {
   if (oObj.vInit == 0) {
    oObj.Init();
    oObj.vInit = 1;
   }
   if (oObj.input.value + "" != "") {
    if (this.CheckDate(oObj.input.value, oObj.oFmt)) {
     return true;
    } else {
     oObj.input.value = "";
     alert(oObj.sErrMsg + " ( " + oObj.sFmt + " )");
     return false;
    }
   }
  }
 };

 // Methodes de changement des proprietes d'une instance
 this.SetErrMsg = function( inNAME, inMSG )
 {
  if ("" + inMSG != "") {
   var oObj = this.GetObject(inNAME);
   if (oObj) {
    oObj.sErrMsg = inMSG;
   }
  }
 };
 this.SetDateFormat = function( inNAME, inFMT )
 {
  if ("" + inFMT != "") {
   var oObj = this.GetObject(inNAME);
   if (oObj) {
    oObj.sFmt = inFMT;
    oObj.Init();
    oObj.vInit = 1;
   }
  }
 };


 // Methode de controle sur date
 // en fonction du format selectionne
 this.CheckDate = function( inDATE, inFMT )
 {
  var aD, dD, dM, dY, s, dYDigit;
  s = inDATE;
  aD = s.split(inFMT.sDel);
  dD = Math.round(parseFloat(aD[inFMT.aFmt[1]]));
  dM = Math.round(parseFloat(aD[inFMT.aFmt[2]])) - 1;
  dY = Math.round(parseFloat(aD[inFMT.aFmt[3]]));
  s = dY + "";
  dYDigit = s.length;
  if (isNaN(dD) || isNaN(dM) || isNaN(dY) || (dY < 1) || (dD < 1) || (dM < 0) || (dM > 11) || (dYDigit != 4) || (dD > this.DaysIn(dM, dY))){
   return false;
  } else{
   if (dM == 1) {
    if (!this.IsLeap(dY) && dD == 29) {
     return false;
    } else {
     return true;
    }
   } else {
    return true;
   }
  }
 };

 // Methode pour connaitre le nombre de jours dans un mois
 this.DaysIn = function( inMONTH, inYEAR )
 {
  var m = 0;
  if (("§0§§2§§4§§6§§7§§9§§11§").indexOf("§" + inMONTH + "§") >= 0) {
   m = 31;
  } else if (("§3§§5§§8§§10§").indexOf("§" + inMONTH + "§") >= 0) {
   m = 30;
  } else {
   if (this.IsLeap(inYEAR)) {
    m = 29;
   } else {
    m = 28;
   }
  }
  return m;
 };

 // Methode de verification annee bissextile
 this.IsLeap = function( inYEAR )
 {
  if (inYEAR % 400 == 0) {
   return true;
  } else if ((inYEAR % 4 == 0) && (inYEAR % 100 != 0)){
   return true;
  } else {
   return false;
  }
 };

};

DC_Instance = function( inNAME, inFORM ) {

 // Proprietes par defaut
 this.sFmt = "jj/mm/aaaa";
 this.sErrMsg = "Veuillez vérifier le format de date.";
 this.name = inNAME;
 this.input = inFORM;
 this.vInit = 0;

 // Methode d'initialisation de l'instance
 this.Init = function()
 {
  this.oFmt = new DC_Format(this.sFmt, this);
  this.vInit = 1;
 };

};

DC_Format = function( inFMT )
{

 // Attention : Si aucune combinaison ne correspond
 //             au format d'entree ( inFMT ), le format
 //             jj/mm/aaaa sera selectionne par defaut

 // Proprietes par defaut
 this.eMsg = 0;
 this.sDel = "";
 this.aFmt = null;

 // Methode d'initialisation de l'instance
 // Cette methode recherche le format de date
 // parametre en fonction des delimiteurs
 // possibles et des combinaisons de format
 this.Init = function()
 {
  var i = 0, dOK = 0, s = "", fOK = 0, a;
  // On cherche le delimiteur
  for (i = 0; i < aDel.length; i++) {
   if (inFMT.split(aDel[i]).length == 3) {
    this.sDel = aDel[i];
    dOK = 1;
    break;
   }
  };
  if (dOK == 0) {
   // Le delimiteur n'existe pas, on selectionne "/" par defaut
   this.sDel = this.aDel[0];
   oObj.sFmt = "jj/mm/aaaa";
  } else {
   // On cherche la combinaison
   a = inFMT.split(this.sDel);
   for (i = 0; i < a.length; i++) {
    s += a[i];
   };
   for (i = 0; i < aFmt.length; i++) {
    if (s == aFmt[i][0]) {
     this.aFmt = aFmt[i];
     fOK = 1;
     break;
    }
   };
   if (fOK == 0) {
    // Le format n'existe pas, on selectionne jjmmaaaa par defaut
    this.aFmt = aFmt[0];
   }
  }
 };

 // Methode de recuperation de la date
 // avec le format selectionne
 this.GetDateFormatted = function( inDAY, inMONTH, inYEAR )
 {
  var dD = "", dM = "", i = 0, s0 = "", s1 = "", s2 = "", s = "";
  if ((inDAY + "").length < 2) {dD = "0" + inDAY;} else {dD = inDAY;}
  dM = inMONTH + 1;
  if ((dM + "").length < 2) {dM = "0" + dM;}
  for (i = 1; i < this.aFmt.length; i++) {
   eval("s" + this.aFmt[i] + " = '§" + i + "§';");
  };
  s = s0 + this.sDel + s1 + this.sDel + s2;
  s = s.replace("§1§", dD);
  s = s.replace("§2§", dM);
  s = s.replace("§3§", inYEAR);
  return s;
 };

 // On initialise
 this.Init();

};

// Array des combinaisons de format de date
var aFmt =
[
 ["jjmmaaaa", "0", "1", "2"],
 ["jjaaaamm", "0", "2", "1"],
 ["mmjjaaaa", "1", "0", "2"],
 ["mmaaaajj", "2", "0", "1"],
 ["aaaammjj", "2", "1", "0"],
 ["aaaajjmm", "1", "2", "0"],
 ["ddmmyyyy", "0", "1", "2"],
 ["ddyyyymm", "0", "2", "1"],
 ["mmddyyyy", "1", "0", "2"],
 ["mmyyyydd", "2", "0", "1"],
 ["yyyymmdd", "2", "1", "0"],
 ["yyyyddmm", "1", "2", "0"]
];
