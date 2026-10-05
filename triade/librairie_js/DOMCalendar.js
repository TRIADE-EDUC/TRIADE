// DOMCalendar v1.1 - 07/03/2003
// Par AURELIEN : aurelien@asp-php.net
//
// v1.1 : Mise en place des methodes
//        pour cacher les SelectList
//        lors de l'affichage afin
//        d'eviter les chevauchements

var DC20030227 = null;

DOMCalendar = function()
{
 DC20030227 = this;
 this.aDC = new Array();
 this.imgPath = "";
 this.dCtrl = new DateControl();
 this.sD = this.sM = this.sY = "";
 this.dD = this.dM = this.dY = "";
 this.dcActive = null;

 this.SetImgPath = function( inPATH )
 {
  this.imgPath = inPATH;
 };

 this.SetDateFormat = function( inNAME, inFMT )
 {
  var oCal = this.GetCalendar(inNAME);
  if (oCal) {
   oCal.dFmt = inFMT;
  }
 };

 this.SetLanguage = function( inNAME, inLNG )
 {
  var oCal = this.GetCalendar(inNAME);
  if (oCal) {
   oCal.lng = inLNG;
  }
 };

 this.SetFirstDayOfWeek = function( inNAME, inFDW )
 {
  var oCal = this.GetCalendar(inNAME);
  if (oCal) {
   oCal.fDayWeek = inFDW;
  }
 };

 this.SetPosition = function( inNAME, inLEFT, inTOP )
 {
  var oCal = this.GetCalendar(inNAME);
  if (oCal) {
   oCal.left = inLEFT;
   oCal.top = inTOP;
  }
 };

 this.HideSelectList = function( inNAME, inSELECT )
 {
  var oCal = this.GetCalendar(inNAME);
  if (oCal) {
   oCal.hideSelect[oCal.hideSelect.length] = inSELECT;
  }
 };

 this.LoadImg = function()
 {
  var i = 0, oImg = null, aImg = ["c_b", "c_bd", "c_bg", "c_hd", "c_hg", "c_mp", "c_ms"];
  for (i = 0; i < aImg.length; i++) {
   oImg = new Image();
   oImg.src = this.imgPath + aImg[i] + ".gif";
  };
 };

 this.DeleteAll = function()
 {
  var i = 0;
  document.getElementById("DCYear").innerHTML = "&nbsp;";
  document.getElementById("DCMonth").innerHTML = "&nbsp;";
  for (i = 0; i < 42; i++) {
   document.getElementById("DCTd" + i).innerHTML = "&nbsp;";
   document.getElementById("DCTd" + i).onmouseover = null;
   document.getElementById("DCTd" + i).onmouseout = null;
   document.getElementById("DCTd" + i).onclick = null;
  };
  for (i = 0; i < 7; i ++) {
   document.getElementById("DCDay" + i).innerHTML = "&nbsp;";
  };
 };

 this.GetCalendar = function( inNAME )
 {
  var i = 0, oObj = null;
  for (i = 0; i < this.aDC.length; i++) {
   if (this.aDC[i].name == inNAME) {
    oObj = this.aDC[i];
    break;
   }
  };
  return oObj;
 };

 this.AddCalendar = function( inNAME, inFORM )
 {
  this.aDC[this.aDC.length] = new DC_Structure(inNAME, inFORM);
 };

 this.Show = function( inNAME )
 {
  var oCal = this.GetCalendar(inNAME), oValue = "", d = null, aD = null, oFmt = null, dOK = 1;
  if (oCal) {
   oValue = "" + oCal.form.value;
   oFmt = this.dCtrl.GetObject(inNAME);
   if (oValue == "") {
    d = new Date();
    this.dD = d.getDate();
    this.dM = d.getMonth();
    this.dY = d.getFullYear();
    this.sD = this.sM = this.sY = "";
   } else {
    if (this.dCtrl.IsValidDate(inNAME)) {
     aD = oValue.split(oFmt.oFmt.sDel);
     this.dD = this.sD = Math.round(parseFloat(aD[oFmt.oFmt.aFmt[1]]));
     this.dM = this.sM = Math.round(parseFloat(aD[oFmt.oFmt.aFmt[2]])) - 1;
     this.dY = this.sY = Math.round(parseFloat(aD[oFmt.oFmt.aFmt[3]]));
    } else {
     dOK = 0;
    }
   }
   if (dOK != 0) {
    this.Close();
    this.dcActive = oCal;
    this.GetContent(oCal, oFmt);
    for (var i = 0; i < oCal.hideSelect.length; i++) {
     oCal.hideSelect[i].style.visibility = "hidden";
    };
    this.Open(oCal);
   }
  }
 };

 this.Open = function( inOBJ )
 {
  var oTbl = document.getElementById("DCTable");
  with (oTbl.style) {
   top = inOBJ.top;
   left = inOBJ.left;
   visibility = "visible";
   display = "";
  }
 };

 this.Close = function()
 {
  var oTbl = document.getElementById("DCTable");
  this.DeleteAll();
  with (oTbl.style) {
   visibility = "hidden";
   display = "none";
  }
  var oCal = this.dcActive;
  if (oCal) {
   for (var  i = 0; i < oCal.hideSelect.length; i++) {
    oCal.hideSelect[i].style.visibility = "visible";
   };
  }
  this.dcActive = null;
 };

 this.Prev = function( inNAME )
 {
  var oCal = this.GetCalendar(inNAME), oFmt = null;
  if (oCal) {
   this.dM--;
   if (this.dM < 0) {
    this.dY--;
    this.dM = 11;
   }
   oFmt = this.dCtrl.GetObject(inNAME);
   this.DeleteAll();
   this.GetContent(oCal, oFmt);
  }
 };

 this.YearPrev = function( inNAME )
 {
  var oCal = this.GetCalendar(inNAME), oFmt = null;
  if (oCal) {
   this.dY--;
   if (this.dY < 1) {
    this.dY = 1;
   }
   oFmt = this.dCtrl.GetObject(inNAME);
   this.DeleteAll();
   this.GetContent(oCal, oFmt);
  }
 };

 this.Next = function( inNAME )
 {
  var oCal = this.GetCalendar(inNAME), oFmt = null;
  if (oCal) {
   this.dM++;
   if (this.dM > 11) {
    this.dY++;
    this.dM = 0;
   }
   oFmt = this.dCtrl.GetObject(inNAME);
   this.DeleteAll();
   this.GetContent(oCal, oFmt);
  }
 };

 this.YearNext = function( inNAME )
 {
  var oCal = this.GetCalendar(inNAME), oFmt = null;
  if (oCal) {
   this.dY++;
   oFmt = this.dCtrl.GetObject(inNAME);
   this.DeleteAll();
   this.GetContent(oCal, oFmt);
  }
 };

 this.SetDateBack = function( inNAME )
 {
  var dC = window.event.srcElement.innerHTML, oCal = this.GetCalendar(inNAME), oFmt = null;
  if (oCal) {
   oFmt = this.dCtrl.GetObject(inNAME);
   oCal.form.value = oFmt.oFmt.GetDateFormatted(dC, this.dM, this.dY);
   this.Close();
  }
 };

 this.GetContent = function( inOBJ, inFMT )
 {
  var fDayMonth = 0, indx = 0, lDayMonth = 0, i = 0, oTd = null;
  for (i = 0; i < inOBJ.aLng.a[1].length; i++) {
   oTd = document.getElementById("DCDay" + i);
   indx = i + inOBJ.fDayWeek;
   if (indx > 6) {
    indx = indx - 7;
   }
   oTd.innerHTML = inOBJ.aLng.a[1][indx];
  };
  fDayMonth = (new Date(this.dY, this.dM, "01")).getDay();
  fDayMonth = fDayMonth - inOBJ.fDayWeek;
  if (fDayMonth < 0) {
   fDayMonth = fDayMonth + 7;
  }
  lDayMonth = fDayMonth + this.dCtrl.DaysIn(this.dM, this.dY);
  indx = 1;
  for (i = fDayMonth; i < lDayMonth; i++) {
   if (indx == this.sD && this.dM == this.sM && this.dY == this.sY) {
    document.getElementById("DCTd" + i).style.color = "#FF0000";
   } else {
    document.getElementById("DCTd" + i).style.color = "#0000FF";
   }
   document.getElementById("DCTd" + i).style.cursor = "hand";
   document.getElementById("DCTd" + i).innerHTML = indx;
   document.getElementById("DCTd" + i).onclick = function() {DC20030227.SetDateBack(inOBJ.name);};
   indx = indx + 1;
  };
  document.getElementById("DCYear").innerHTML = this.dY;
  document.getElementById("DCMonth").innerHTML = inOBJ.aLng.a[2][this.dM];
  document.getElementById("DCClose").onmouseover = function() {fStatusOver(inOBJ.aLng.a[3][3]);};
  document.getElementById("DCClose").onmouseout = fStatusOut;
  document.getElementById("DCPrev").onmouseover = function() {fStatusOver(inOBJ.aLng.a[3][0]);};
  document.getElementById("DCPrev").onmouseout = fStatusOut;
  document.getElementById("DCPrev").onclick = function() {DC20030227.Prev(inOBJ.name);};
  document.getElementById("DCNext").onmouseover = function() {fStatusOver(inOBJ.aLng.a[3][1]);};
  document.getElementById("DCNext").onmouseout = fStatusOut;
  document.getElementById("DCNext").onclick = function() {DC20030227.Next(inOBJ.name);};
  document.getElementById("DCYearPrev").onmouseover = function() {fStatusOver(inOBJ.aLng.a[3][4]);};
  document.getElementById("DCYearPrev").onmouseout = fStatusOut;
  document.getElementById("DCYearPrev").onclick = function() {DC20030227.YearPrev(inOBJ.name);};
  document.getElementById("DCYearNext").onmouseover = function() {fStatusOver(inOBJ.aLng.a[3][4]);};
  document.getElementById("DCYearNext").onmouseout = fStatusOut;
  document.getElementById("DCYearNext").onclick = function() {DC20030227.YearNext(inOBJ.name);};
 };

 this.InitTable = function()
 {
  var str = "", i = 0;
  str += "<table id=\"DCTable\" border=\"0\" cellspacing=\"0\" style=\"table-layout:fixed;visibility:hidden;z-index:1000;display:none;position:absolute;top:0;left:0;\">";
  str += "<tr height=\"11\">";
  str += "<td width=\"21\" background=\"" + this.imgPath + "c_hg.gif\" style=\"font-size:1px;\">&nbsp;</td>";
  str += "<td width=\"140\" colspan=\"7\" style=\"border-top:1px solid #8183A2;font-size:1px\">&nbsp;</td>";
  str += "<td width=\"21\" background=\"" + this.imgPath + "c_hd.gif\" style=\"font-size:1px;\">&nbsp;</td>";
  str += "</tr>";
  str += "<tr height=\"20\">";
  str += "<td id=\"DCPrev\" align=\"center\" valign=\"middle\" style=\"border-left:1px solid #8183A2;cursor:hand;\"><img src=\"" + this.imgPath + "c_mp.gif\" border=\"0\" width=\"11\" height=\"11\"></td>";
  str += "<td id=\"DCMonth\" colspan=\"7\" align=\"center\" style=\"font-family:Tahoma;font-size:x-small;font-weight:bold;\">&nbsp;</td>";
  str += "<td id=\"DCNext\" align=\"center\" valign=\"middle\" style=\"border-right:1px solid #8183A2;cursor:hand;\"><img src=\"" + this.imgPath + "c_ms.gif\" border=\"0\" width=\"11\" height=\"11\"></td>";
  str += "</tr>";
  str += "<tr height=\"20\">";
  str += "<td style=\"border-left:1px solid #8183A2;font-size:1px\">&nbsp;</td>";
  for (var i = 0; i < 7; i++) {
   str += "<td id=\"DCDay" + i + "\" align=\"center\" width=\"20\" style=\"font-family:Tahoma;font-size:x-small;font-weight:bold;\">&nbsp;</td>";
  };
  str += "<td style=\"border-right:1px solid #8183A2;font-size:1px\">&nbsp;</td>";
  str += "</tr>";
  str += "<tr height=\"20\">";
  str += "<td style=\"border-left:1px solid #8183A2;font-size:1px\">&nbsp;</td>";
  for (var i = 0; i < 42; i++) {
   if (i != 0) {
    if (i % 7 == 0) {
     str += "<td style=\"border-right:1px solid #8183A2;font-size:1px\">&nbsp;</td>";
     str += "</tr>";
     str += "<tr height=\"20\">";
     str += "<td style=\"border-left:1px solid #8183A2;font-size:1px\">&nbsp;</td>";
    }
   }
   str += "<td id=\"DCTd" + i + "\" align=\"center\" width=\"20\" style=\"font-family:Tahoma;font-size:x-small;font-weight:bold;\">&nbsp;</td>";
  };
  str += "<td style=\"border-right:1px solid #8183A2;font-size:1px\">&nbsp;</td>";
  str += "</tr>";
  str += "<tr height=\"20\">";
  str += "<td background=\"" + this.imgPath + "c_bg.gif\" style=\"font-size:1px;\">&nbsp;</td>";
  str += "<td id=\"DCYearPrev\" align=\"center\" valign=\"middle\" style=\"border-bottom:1px solid #8183A2;cursor:hand;\"><img src=\"" + this.imgPath + "c_mp.gif\" border=\"0\" width=\"11\" height=\"11\"></td>";
  str += "<td id=\"DCYear\" colspan=\"5\" align=\"center\" style=\"border-bottom:1px solid #8183A2;font-family:Tahoma;font-size:x-small;font-weight:bold;\">&nbsp;</td>";
  str += "<td id=\"DCYearNext\" align=\"center\" valign=\"middle\" style=\"border-bottom:1px solid #8183A2;cursor:hand;\"><img src=\"" + this.imgPath + "c_ms.gif\" border=\"0\" width=\"11\" height=\"11\"></td>";
  str += "<td id=\"DCClose\" background=\"" + this.imgPath + "c_bd.gif\" style=\"font-size:1px;cursor:hand;\" onClick=\"DC20030227.Close();\">&nbsp;</td>";
  str += "</tr>";
  str += "</table>";
  document.body.insertAdjacentHTML("beforeend", str)
 };

 this.IsValidDate = function( inNAME )
 {
  this.dCtrl.IsValidDate(inNAME)
 };

 this.InitAll = function()
 {
  var i = 0, n = "";
  for (i = 0; i < this.aDC.length; i++) {
   n = this.aDC[i].name;
   this.aDC[i].aLng = new CLanguage(this.aDC[i].lng);
   this.dCtrl.Add(n, this.aDC[i].form);
   this.dCtrl.SetDateFormat(n, this.aDC[i].dFmt);
   this.dCtrl.SetErrMsg(n, this.aDC[i].aLng.a[4]);
   this.aDC[i].form.onblur = function() {DC20030227.IsValidDate(n)}
  };
 };

 this.Init = function()
 {
  this.LoadImg();
  this.InitAll();
  this.InitTable();
 };

};

DC_Structure = function( inNAME, inFORM )
{
 this.name = inNAME;
 this.form = inFORM;
 this.dFmt = "jj/mm/aaaa";
 this.lng = "FR";
 this.aLng = null;
 this.fDayWeek = 0;
 this.top = 0;
 this.left = 0;
 this.hideSelect = new Array();
};

fStatusOver = function( inSTATUS )
{
 window.status = inSTATUS;
 return true;
};

fStatusOut = function()
{
 window.status = " ";
 return true;
};