var DCAL = null;

GetDomLeft = function ( oNode )
{
  var oCurrentNode = oNode;
  var iLeft = 0;
  while (oCurrentNode.tagName != "BODY") {
    iLeft += oCurrentNode.offsetLeft;
    oCurrentNode = oCurrentNode.offsetParent;
  };
  return iLeft;
};
GetDomTop = function ( oNode )
{
  var oCurrentNode = oNode;
  var iTop = 0;
  while (oCurrentNode.tagName != "BODY") {
    iTop += oCurrentNode.offsetTop;
    oCurrentNode = oCurrentNode.offsetParent;
  };
  return iTop;
};

function myInit() {
 // On recupere les objets images
 oImg1 = document.getElementById("iDC1");
 // On instancie l'objet Calendar
 DCAL = new DOMCalendar();
 // On affecte le repertoire image
 DCAL.SetImgPath("./image/imgcal/");
 // On ajoute des zones Calendar
 // Name, Form : Nom et formulaire a controler
 DCAL.AddCalendar("DC1", document.form11.iDate[0]);
 // On affecte le premier jour de la semaine
 // 0 : Dimanche -> 6 : Samedi
 DCAL.SetFirstDayOfWeek("DC1", 1);
 // On affecte la position par rapport a l'image
 DCAL.SetPosition("DC1", GetDomLeft(oImg1) + oImg1.offsetWidth + 10, GetDomTop(oImg1) - 10);
 DCAL.Init();
};

