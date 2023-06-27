var CLANG = 
[
 [
  "FR",
  ["D", "L", "M", "M", "J", "V", "S"],
  ["JANVIER", "FEVRIER", "MARS", "AVRIL", "MAI", "JUIN", "JUILLET", "AOUT", "SEPTEMBRE", "OCTOBRE", "NOVEMBRE", "DECEMBRE"],
  ["Mois précédent", "Mois suivant", "Afficher le calendrier", "Fermer le calendrier", "Année précédente", "Année suivante"],
  "Veuillez vérifier le format de la date."
 ],
 [
  "EN",
  ["S", "M", "T", "W", "T", "F", "S"],
  ["JANUARY", "FEBRUARY", "MARCH", "APRIL", "MAY", "JUNE", "JULY", "AUGUST", "SEPTEMBER", "OCTOBER", "NOVEMBER", "DECEMBER"],
  ["Previous month", "Next month", "Show calendar", "Close calendar", "Previous year", "Next year"],
  "Please check date format."
 ]
];

CLanguage = function( inLANG )
{
 this.a = CLANG[0];
 this.Init = function()
 {
  if (inLANG + "" != "") {
   var i = 0, lng = inLANG.toUpperCase();
   for (i = 0; i < CLANG.length; i++) {
    if (CLANG[i][0].toUpperCase() == lng) {
     this.a = CLANG[i];
     break;
    }
   };
  }
 };
 this.Init();
};