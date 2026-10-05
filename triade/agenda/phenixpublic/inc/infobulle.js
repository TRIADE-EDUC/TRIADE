/**************************************************************************\
  * Phenix Agenda                                                            *
  * http://phenix.gapi.fr                                                    *
  * Written by    Stephane TEIL            <phenix-agenda@laposte.net>       *
  * Contributors  Christian AUDEON (Omega) <christian.audeon@gmail.com>      *
  *               Maxime CORMAU (MaxWho17) <maxwho17@free.fr>                *
  *               Mathieu RUE (Frognico)   <matt_rue@yahoo.fr>               *
  *               Bernard CHAIX (Berni69)  <ber123456@free.fr>               *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
\**************************************************************************/

var width=422; // largeur de la bulle
ns4 = (document.layers) ? true : false;
ope = (document.getElementById) ? true : false;
ie4 = (document.all) ? true : false;
if (ie4) {
  ie5 = (navigator.userAgent.indexOf('MSIE 5')>0 || navigator.userAgent.indexOf('MSIE 6')>0 || navigator.userAgent.indexOf('MSIE 7')>0) ? true : false;
} else {
  ie5 = false;
}

var x=0;
var y=0;
var xGauche;

var snow=0;
var sw=0;
var cnt=0;

if ( (ns4) || (ie4) || (ope) ) {
  if (ie4 && !ope)
    over = document.all["infoBulle"].style;
  else if (ope)
    over = document.getElementById("infoBulle").style;
  else if (ns4)
    over = document.infoBulle;
  document.onmousemove = mouseMove;
  if (ns4)
    document.captureEvents(Event.MOUSEMOVE);
}

function nd() {
  if ( cnt >= 1 ) {
    sw = 0;
  }
  if (((ns4) || (ie4) || (ope)) && (sw!=2)) {
    if ( sw == 0 ) {
      snow=0;
      hideObject(over);
    } else {
      cnt++;
    }
  }
}

// Popup "flottant" des notes
function dtc(heure, titre, texte) {
  sw=0;
  width = 390;
  layerWrite("<TABLE width="+width+" border=0 cellpadding=0 cellspacing=1 class=\"infoBulle\"><TR><TD><TABLE width=\"100%\" border=0 cellpadding=0 cellspacing=0><TR valign=\"top\"><TD class=\"ibHeure\" nowrap>"+heure+"&nbsp;</TD><TD class=\"ibTitre\" width=\"100%\">"+titre+"</TD></TR></TABLE></TD></TR><TR><TD class=\"ibTexte\">"+texte+"</TD></TR></TABLE>", "infoBulle");
  disp();
}
// Popup "fixe" de l'agenda quotidien
function stc(heure, titre, texte, btFermer) {
  sw=1;
  cnt=0;
  width = 390;
  layerWrite("<TABLE width="+width+" border=0 cellpadding=0 cellspacing=1 class=\"infoBulle\"><TR><TD><TABLE width=\"100%\" border=0 cellpadding=0 cellspacing=0><TR valign=\"top\"><TD class=\"ibHeure\" nowrap>"+heure+"&nbsp;</TD><TD class=\"ibTitre\" width=\"100%\">"+titre+"</TD><TD align=\"right\" class=\"ibTitre\"><A href=\"/\" onClick=\"cClick(); return false;\"><IMG src=\"image/popup_close.gif\" width=\"13\" height=\"13\" alt=\""+btFermer+"\" border=\"0\"></A></TD></TR></TABLE></TD></TR><TR><TD class=\"ibTexte\">"+texte+"</TD></TR></TABLE>", "infoBulle");
  disp();
  snow=0;
}
// Popup des contacts associes et des title HTML
function atc(titre, texte) {
  sw=0;
  width = 50;
  var _str = "<TABLE width="+width+" border=0 cellpadding=0 cellspacing=1 class=\"infoBulle\">";
  if (titre.length>0) {
    _str += "<TR><TD class=\"ibTitre\" nowrap>"+titre+"</TD></TR>";
  }
  _str += "<TR><TD class=\"ibTexte\" nowrap>"+texte+"</TD></TR></TABLE>";
  layerWrite(_str, "infoBulle");
  disp();
}
// Popup des memos
function mtc(titre, texte, largeur) {
  sw=0;
  width = largeur;
  layerWrite("<TABLE width="+width+" border=0 cellpadding=0 cellspacing=1 class=\"infoBulle\"><TR><TD class=\"ibHeure\">"+titre+"</TD></TR><TR><TD class=\"ibTexte\">"+texte+"</TD></TR></TABLE>", "infoBulle");
  disp();
}

function disp() {
  if ( (ns4) || (ie4) || (ope) ) {
    if (snow == 0) {
      if (ie4 && !ope)
        width = document.all["infoBulle"].clientWidth;
      else if (ope)
        width = document.getElementById("infoBulle").clientWidth;
      else
        width = document.infoBulle.clientWidth;
      if (sw == 0) {
        if (x-(width/2)<8) {
          xG = 8;
        } else if (x+(width/2)>(document.body.clientWidth-6)) {
          xG = document.body.clientWidth-width-6;
        } else {
          xG = x-(width/2);
        }
        moveTo(over,xG,y+20)
      }
      else {
        xGauche = Math.max(146,x-width+5);
        moveTo(over,xGauche,y+10);
      }
      showObject(over);
      snow=1;
    }
  }
}

function mouseMove(e) {
  if (ie5) {
    x=event.x+document.body.scrollLeft;
    y=event.y+document.body.scrollTop;
  }
  else if (ie4 && !ope) {
    x=event.x;
    y=event.y;
  }
  else if (ope) {
    x=e.pageX;
    y=e.pageY;
  }
  else if (ns4) {
    x=e.pageX;
    y=e.pageY;
  }
  if (snow) {
    if (x-(width/2)<8) {
      xG = 8;
    } else if (x+(width/2)>document.body.clientWidth-6) {
      xG = document.body.clientWidth-width-6;
    } else {
      xG = x-(width/2);
    }
    moveTo(over,xG,y+20);
  }
}

function cClick() {
  hideObject(over);
  sw=0;
}

function layerWrite(txt,_layer) {
  if (ie4 && !ope)
    document.all[_layer].innerHTML = txt;
  else if (ope)
    document.getElementById(_layer).innerHTML = txt;
  else if (ns4) {
    var lyr = document._layer.document;
    lyr.write(txt);
    lyr.close();
  }
}

function showObject(obj) {
  if (ie4 || ope)
    obj.visibility = "visible";
  else if (ns4)
    obj.visibility = "show";
}

function hideObject(obj) {
  if (ie4 || ope)
    obj.visibility = "hidden";
  else if (ns4)
    obj.visibility = "hide";
}

function moveTo(obj,xL,yL) {
  obj.left = xL;
  obj.top = yL;
}

// Fonctions pour la palette de couleur des evenements -------------------------
function showPalette(_color, _colorDefault, _titre, _btFermer, _btSelect) {
  sw=1;
  cnt=0;
  width = 295;
  layerWrite(makeColorMap() + "<TABLE width="+width+" border=0 cellpadding=0 cellspacing=1 class=\"infoBulle\"><TR><TD><TABLE width=\"100%\" border=0 cellpadding=0 cellspacing=0><TR valign=\"top\"><TD class=\"ibTitre\" width=\"100%\">"+_titre+"</TD><TD align=\"right\" class=\"ibTitre\"><A href=\"/\" onClick=\"cClick(); return false;\"><IMG src=\"image/popup_close.gif\" width=\"13\" height=\"13\" alt=\""+_btFermer+"\" border=\"0\"></A></TD></TR></TABLE></TD></TR><TR><TD><FORM name=\"frmPalette\"><TABLE width=\"100%\" border=0 cellpadding=0 cellspacing=0><TR><TD class=\"ibTexte\" colspan=\"2\"><IMG src=\"image/evenement/palette.gif\" border=\"0\" width=\"289\" height=\"67\" usemap=\"#colorMap\"></TD></TR><TR><TD class=\"ibTexte\" align=\"left\"><INPUT type=\"text\" class=\"texte\" name=\"ztChoix\" style=\"width:185px; height:18px\" readonly></TD><TD class=\"ibTexte\" align=\"right\"><INPUT type=\"button\" class=\"bouton\" value=\""+_btSelect+"\" style=\"width:100px;height:18px\" onClick=\"javascript: selColor();\"></TD></TR></TABLE></FORM></TD></TR></TABLE>", "infoBulle");
  initColor(_color, _colorDefault);
  disp();
  snow=0;
}

function makeColorMap() {
  var _colormap = ("<MAP name=\"colorMap\"><AREA shape=\"rect\" coords=\"1,1,7,10\" href=\"javascript: setColor('#00FF00')\"><AREA shape=\"rect\" coords=\"9,1,15,10\" href=\"javascript: setColor('#00FF33')\"><AREA shape=\"rect\" coords=\"17,1,23,10\" href=\"javascript: setColor('#00FF66')\"><AREA shape=\"rect\" coords=\"25,1,31,10\" href=\"javascript: setColor('#00FF99')\"><AREA shape=\"rect\" coords=\"33,1,39,10\" href=\"javascript: setColor('#00FFCC')\"><AREA shape=\"rect\" coords=\"41,1,47,10\" href=\"javascript: setColor('#00FFFF')\"><AREA shape=\"rect\" coords=\"49,1,55,10\" href=\"javascript: setColor('#33FF00')\"><AREA shape=\"rect\" coords=\"57,1,63,10\" href=\"javascript: setColor('#33FF33')\"><AREA shape=\"rect\" coords=\"65,1,71,10\" href=\"javascript: setColor('#33FF66')\"><AREA shape=\"rect\" coords=\"73,1,79,10\" href=\"javascript: setColor('#33FF99')\"><AREA shape=\"rect\" coords=\"81,1,87,10\" href=\"javascript: setColor('#33FFCC')\">");
  _colormap += ("<AREA shape=\"rect\" coords=\"89,1,95,10\" href=\"javascript: setColor('#33FFFF')\"><AREA shape=\"rect\" coords=\"97,1,103,10\" href=\"javascript: setColor('#66FF00')\"><AREA shape=\"rect\" coords=\"105,1,111,10\" href=\"javascript: setColor('#66FF33')\"><AREA shape=\"rect\" coords=\"113,1,119,10\" href=\"javascript: setColor('#66FF66')\"><AREA shape=\"rect\" coords=\"121,1,127,10\" href=\"javascript: setColor('#66FF99')\"><AREA shape=\"rect\" coords=\"129,1,135,10\" href=\"javascript: setColor('#66FFCC')\"><AREA shape=\"rect\" coords=\"137,1,143,10\" href=\"javascript: setColor('#66FFFF')\"><AREA shape=\"rect\" coords=\"145,1,151,10\" href=\"javascript: setColor('#99FF00')\"><AREA shape=\"rect\" coords=\"153,1,159,10\" href=\"javascript: setColor('#99FF33')\"><AREA shape=\"rect\" coords=\"161,1,167,10\" href=\"javascript: setColor('#99FF66')\"><AREA shape=\"rect\" coords=\"169,1,175,10\" href=\"javascript: setColor('#99FF99')\">");
  _colormap += ("<AREA shape=\"rect\" coords=\"177,1,183,10\" href=\"javascript: setColor('#99FFCC')\"><AREA shape=\"rect\" coords=\"185,1,191,10\" href=\"javascript: setColor('#99FFFF')\"><AREA shape=\"rect\" coords=\"193,1,199,10\" href=\"javascript: setColor('#CCFF00')\"><AREA shape=\"rect\" coords=\"201,1,207,10\" href=\"javascript: setColor('#CCFF33')\"><AREA shape=\"rect\" coords=\"209,1,215,10\" href=\"javascript: setColor('#CCFF66')\"><AREA shape=\"rect\" coords=\"217,1,223,10\" href=\"javascript: setColor('#CCFF99')\"><AREA shape=\"rect\" coords=\"225,1,231,10\" href=\"javascript: setColor('#CCFFCC')\"><AREA shape=\"rect\" coords=\"233,1,239,10\" href=\"javascript: setColor('#CCFFFF')\"><AREA shape=\"rect\" coords=\"241,1,247,10\" href=\"javascript: setColor('#FFFF00')\"><AREA shape=\"rect\" coords=\"249,1,255,10\" href=\"javascript: setColor('#FFFF33')\"><AREA shape=\"rect\" coords=\"257,1,263,10\" href=\"javascript: setColor('#FFFF66')\">");
  _colormap += ("<AREA shape=\"rect\" coords=\"265,1,271,10\" href=\"javascript: setColor('#FFFF99')\"><AREA shape=\"rect\" coords=\"273,1,279,10\" href=\"javascript: setColor('#FFFFCC')\"><AREA shape=\"rect\" coords=\"281,1,287,10\" href=\"javascript: setColor('#FFFFFF')\"><AREA shape=\"rect\" coords=\"1,12,7,21\" href=\"javascript: setColor('#00CC00')\"><AREA shape=\"rect\" coords=\"9,12,15,21\" href=\"javascript: setColor('#00CC33')\"><AREA shape=\"rect\" coords=\"17,12,23,21\" href=\"javascript: setColor('#00CC66')\"><AREA shape=\"rect\" coords=\"25,12,31,21\" href=\"javascript: setColor('#00CC99')\"><AREA shape=\"rect\" coords=\"33,12,39,21\" href=\"javascript: setColor('#00CCCC')\"><AREA shape=\"rect\" coords=\"41,12,47,21\" href=\"javascript: setColor('#00CCFF')\"><AREA shape=\"rect\" coords=\"49,12,55,21\" href=\"javascript: setColor('#33CC00')\"><AREA shape=\"rect\" coords=\"57,12,63,21\" href=\"javascript: setColor('#33CC33')\"><AREA shape=\"rect\" coords=\"65,12,71,21\" href=\"javascript: setColor('#33CC66')\">");
  _colormap += ("<AREA shape=\"rect\" coords=\"73,12,79,21\" href=\"javascript: setColor('#33CC99')\"><AREA shape=\"rect\" coords=\"81,12,87,21\" href=\"javascript: setColor('#33CCCC')\"><AREA shape=\"rect\" coords=\"89,12,95,21\" href=\"javascript: setColor('#33CCFF')\"><AREA shape=\"rect\" coords=\"97,12,103,21\" href=\"javascript: setColor('#66CC00')\"><AREA shape=\"rect\" coords=\"105,12,111,21\" href=\"javascript: setColor('#66CC33')\"><AREA shape=\"rect\" coords=\"113,12,119,21\" href=\"javascript: setColor('#66CC66')\"><AREA shape=\"rect\" coords=\"121,12,127,21\" href=\"javascript: setColor('#66CC99')\"><AREA shape=\"rect\" coords=\"129,12,135,21\" href=\"javascript: setColor('#66CCCC')\"><AREA shape=\"rect\" coords=\"137,12,143,21\" href=\"javascript: setColor('#66CCFF')\"><AREA shape=\"rect\" coords=\"145,12,151,21\" href=\"javascript: setColor('#99CC00')\"><AREA shape=\"rect\" coords=\"153,12,159,21\" href=\"javascript: setColor('#99CC33')\">");
  _colormap += ("<AREA shape=\"rect\" coords=\"161,12,167,21\" href=\"javascript: setColor('#99CC66')\"><AREA shape=\"rect\" coords=\"169,12,175,21\" href=\"javascript: setColor('#99CC99')\"><AREA shape=\"rect\" coords=\"177,12,183,21\" href=\"javascript: setColor('#99CCCC')\"><AREA shape=\"rect\" coords=\"185,12,191,21\" href=\"javascript: setColor('#99CCFF')\"><AREA shape=\"rect\" coords=\"193,12,199,21\" href=\"javascript: setColor('#CCCC00')\"><AREA shape=\"rect\" coords=\"201,12,207,21\" href=\"javascript: setColor('#CCCC33')\"><AREA shape=\"rect\" coords=\"209,12,215,21\" href=\"javascript: setColor('#CCCC66')\"><AREA shape=\"rect\" coords=\"217,12,223,21\" href=\"javascript: setColor('#CCCC99')\"><AREA shape=\"rect\" coords=\"225,12,231,21\" href=\"javascript: setColor('#CCCCCC')\"><AREA shape=\"rect\" coords=\"233,12,239,21\" href=\"javascript: setColor('#CCCCFF')\"><AREA shape=\"rect\" coords=\"241,12,247,21\" href=\"javascript: setColor('#FFCC00')\">");
  _colormap += ("<AREA shape=\"rect\" coords=\"249,12,255,21\" href=\"javascript: setColor('#FFCC33')\"><AREA shape=\"rect\" coords=\"257,12,263,21\" href=\"javascript: setColor('#FFCC66')\"><AREA shape=\"rect\" coords=\"265,12,271,21\" href=\"javascript: setColor('#FFCC99')\"><AREA shape=\"rect\" coords=\"273,12,279,21\" href=\"javascript: setColor('#FFCCCC')\"><AREA shape=\"rect\" coords=\"281,12,287,21\" href=\"javascript: setColor('#FFCCFF')\"><AREA shape=\"rect\" coords=\"1,23,7,32\" href=\"javascript: setColor('#009900')\"><AREA shape=\"rect\" coords=\"9,23,15,32\" href=\"javascript: setColor('#009933')\"><AREA shape=\"rect\" coords=\"17,23,23,32\" href=\"javascript: setColor('#009966')\"><AREA shape=\"rect\" coords=\"25,23,31,32\" href=\"javascript: setColor('#009999')\"><AREA shape=\"rect\" coords=\"33,23,39,32\" href=\"javascript: setColor('#0099CC')\"><AREA shape=\"rect\" coords=\"41,23,47,32\" href=\"javascript: setColor('#0099FF')\">");
  _colormap += ("<AREA shape=\"rect\" coords=\"49,23,55,32\" href=\"javascript: setColor('#339900')\"><AREA shape=\"rect\" coords=\"57,23,63,32\" href=\"javascript: setColor('#339933')\"><AREA shape=\"rect\" coords=\"65,23,71,32\" href=\"javascript: setColor('#339966')\"><AREA shape=\"rect\" coords=\"73,23,79,32\" href=\"javascript: setColor('#339999')\"><AREA shape=\"rect\" coords=\"81,23,87,32\" href=\"javascript: setColor('#3399CC')\"><AREA shape=\"rect\" coords=\"89,23,95,32\" href=\"javascript: setColor('#3399FF')\"><AREA shape=\"rect\" coords=\"97,23,103,32\" href=\"javascript: setColor('#669900')\"><AREA shape=\"rect\" coords=\"105,23,111,32\" href=\"javascript: setColor('#669933')\"><AREA shape=\"rect\" coords=\"113,23,119,32\" href=\"javascript: setColor('#669966')\"><AREA shape=\"rect\" coords=\"121,23,127,32\" href=\"javascript: setColor('#669999')\"><AREA shape=\"rect\" coords=\"129,23,135,32\" href=\"javascript: setColor('#6699CC')\">");
  _colormap += ("<AREA shape=\"rect\" coords=\"137,23,143,32\" href=\"javascript: setColor('#6699FF')\"><AREA shape=\"rect\" coords=\"145,23,151,32\" href=\"javascript: setColor('#999900')\"><AREA shape=\"rect\" coords=\"153,23,159,32\" href=\"javascript: setColor('#999933')\"><AREA shape=\"rect\" coords=\"161,23,167,32\" href=\"javascript: setColor('#999966')\"><AREA shape=\"rect\" coords=\"169,23,175,32\" href=\"javascript: setColor('#999999')\"><AREA shape=\"rect\" coords=\"177,23,183,32\" href=\"javascript: setColor('#9999CC')\"><AREA shape=\"rect\" coords=\"185,23,191,32\" href=\"javascript: setColor('#9999FF')\"><AREA shape=\"rect\" coords=\"193,23,199,32\" href=\"javascript: setColor('#CC9900')\"><AREA shape=\"rect\" coords=\"201,23,207,32\" href=\"javascript: setColor('#CC9933')\"><AREA shape=\"rect\" coords=\"209,23,215,32\" href=\"javascript: setColor('#CC9966')\"><AREA shape=\"rect\" coords=\"217,23,223,32\" href=\"javascript: setColor('#CC9999')\">");
  _colormap += ("<AREA shape=\"rect\" coords=\"225,23,231,32\" href=\"javascript: setColor('#CC99CC')\"><AREA shape=\"rect\" coords=\"233,23,239,32\" href=\"javascript: setColor('#CC99FF')\"><AREA shape=\"rect\" coords=\"241,23,247,32\" href=\"javascript: setColor('#FF9900')\"><AREA shape=\"rect\" coords=\"249,23,255,32\" href=\"javascript: setColor('#FF9933')\"><AREA shape=\"rect\" coords=\"257,23,263,32\" href=\"javascript: setColor('#FF9966')\"><AREA shape=\"rect\" coords=\"265,23,271,32\" href=\"javascript: setColor('#FF9999')\"><AREA shape=\"rect\" coords=\"273,23,279,32\" href=\"javascript: setColor('#FF99CC')\"><AREA shape=\"rect\" coords=\"281,23,287,32\" href=\"javascript: setColor('#FF99FF')\"><AREA shape=\"rect\" coords=\"1,34,7,43\" href=\"javascript: setColor('#006600')\"><AREA shape=\"rect\" coords=\"9,34,15,43\" href=\"javascript: setColor('#006633')\"><AREA shape=\"rect\" coords=\"17,34,23,43\" href=\"javascript: setColor('#006666')\">");
  _colormap += ("<AREA shape=\"rect\" coords=\"25,34,31,43\" href=\"javascript: setColor('#006699')\"><AREA shape=\"rect\" coords=\"33,34,39,43\" href=\"javascript: setColor('#0066CC')\"><AREA shape=\"rect\" coords=\"41,34,47,43\" href=\"javascript: setColor('#0066FF')\"><AREA shape=\"rect\" coords=\"49,34,55,43\" href=\"javascript: setColor('#336600')\"><AREA shape=\"rect\" coords=\"57,34,63,43\" href=\"javascript: setColor('#336633')\"><AREA shape=\"rect\" coords=\"65,34,71,43\" href=\"javascript: setColor('#336666')\"><AREA shape=\"rect\" coords=\"73,34,79,43\" href=\"javascript: setColor('#336699')\"><AREA shape=\"rect\" coords=\"81,34,87,43\" href=\"javascript: setColor('#3366CC')\"><AREA shape=\"rect\" coords=\"89,34,95,43\" href=\"javascript: setColor('#3366FF')\"><AREA shape=\"rect\" coords=\"97,34,103,43\" href=\"javascript: setColor('#666600')\"><AREA shape=\"rect\" coords=\"105,34,111,43\" href=\"javascript: setColor('#666633')\">");
  _colormap += ("<AREA shape=\"rect\" coords=\"113,34,119,43\" href=\"javascript: setColor('#666666')\"><AREA shape=\"rect\" coords=\"121,34,127,43\" href=\"javascript: setColor('#666699')\"><AREA shape=\"rect\" coords=\"129,34,135,43\" href=\"javascript: setColor('#6666CC')\"><AREA shape=\"rect\" coords=\"137,34,143,43\" href=\"javascript: setColor('#6666FF')\"><AREA shape=\"rect\" coords=\"145,34,151,43\" href=\"javascript: setColor('#996600')\"><AREA shape=\"rect\" coords=\"153,34,159,43\" href=\"javascript: setColor('#996633')\"><AREA shape=\"rect\" coords=\"161,34,167,43\" href=\"javascript: setColor('#996666')\"><AREA shape=\"rect\" coords=\"169,34,175,43\" href=\"javascript: setColor('#996699')\"><AREA shape=\"rect\" coords=\"177,34,183,43\" href=\"javascript: setColor('#9966CC')\"><AREA shape=\"rect\" coords=\"185,34,191,43\" href=\"javascript: setColor('#9966FF')\"><AREA shape=\"rect\" coords=\"193,34,199,43\" href=\"javascript: setColor('#CC6600')\">");
  _colormap += ("<AREA shape=\"rect\" coords=\"201,34,207,43\" href=\"javascript: setColor('#CC6633')\"><AREA shape=\"rect\" coords=\"209,34,215,43\" href=\"javascript: setColor('#CC6666')\"><AREA shape=\"rect\" coords=\"217,34,223,43\" href=\"javascript: setColor('#CC6699')\"><AREA shape=\"rect\" coords=\"225,34,231,43\" href=\"javascript: setColor('#CC66CC')\"><AREA shape=\"rect\" coords=\"233,34,239,43\" href=\"javascript: setColor('#CC66FF')\"><AREA shape=\"rect\" coords=\"241,34,247,43\" href=\"javascript: setColor('#FF6600')\"><AREA shape=\"rect\" coords=\"249,34,255,43\" href=\"javascript: setColor('#FF6633')\"><AREA shape=\"rect\" coords=\"257,34,263,43\" href=\"javascript: setColor('#FF6666')\"><AREA shape=\"rect\" coords=\"265,34,271,43\" href=\"javascript: setColor('#FF6699')\"><AREA shape=\"rect\" coords=\"273,34,279,43\" href=\"javascript: setColor('#FF66CC')\"><AREA shape=\"rect\" coords=\"281,34,287,43\" href=\"javascript: setColor('#FF66FF')\">");
  _colormap += ("<AREA shape=\"rect\" coords=\"1,45,7,54\" href=\"javascript: setColor('#003300')\"><AREA shape=\"rect\" coords=\"9,45,15,54\" href=\"javascript: setColor('#003333')\"><AREA shape=\"rect\" coords=\"17,45,23,54\" href=\"javascript: setColor('#003366')\"><AREA shape=\"rect\" coords=\"25,45,31,54\" href=\"javascript: setColor('#003399')\"><AREA shape=\"rect\" coords=\"33,45,39,54\" href=\"javascript: setColor('#0033CC')\"><AREA shape=\"rect\" coords=\"41,45,47,54\" href=\"javascript: setColor('#0033FF')\"><AREA shape=\"rect\" coords=\"49,45,55,54\" href=\"javascript: setColor('#333300')\"><AREA shape=\"rect\" coords=\"57,45,63,54\" href=\"javascript: setColor('#333333')\"><AREA shape=\"rect\" coords=\"65,45,71,54\" href=\"javascript: setColor('#333366')\"><AREA shape=\"rect\" coords=\"73,45,79,54\" href=\"javascript: setColor('#333399')\"><AREA shape=\"rect\" coords=\"81,45,87,54\" href=\"javascript: setColor('#3333CC')\"><AREA shape=\"rect\" coords=\"89,45,95,54\" href=\"javascript: setColor('#3333FF')\">");
  _colormap += ("<AREA shape=\"rect\" coords=\"97,45,103,54\" href=\"javascript: setColor('#663300')\"><AREA shape=\"rect\" coords=\"105,45,111,54\" href=\"javascript: setColor('#663333')\"><AREA shape=\"rect\" coords=\"113,45,119,54\" href=\"javascript: setColor('#663366')\"><AREA shape=\"rect\" coords=\"121,45,127,54\" href=\"javascript: setColor('#663399')\"><AREA shape=\"rect\" coords=\"129,45,135,54\" href=\"javascript: setColor('#6633CC')\"><AREA shape=\"rect\" coords=\"137,45,143,54\" href=\"javascript: setColor('#6633FF')\"><AREA shape=\"rect\" coords=\"145,45,151,54\" href=\"javascript: setColor('#993300')\"><AREA shape=\"rect\" coords=\"153,45,159,54\" href=\"javascript: setColor('#993333')\"><AREA shape=\"rect\" coords=\"161,45,167,54\" href=\"javascript: setColor('#993366')\"><AREA shape=\"rect\" coords=\"169,45,175,54\" href=\"javascript: setColor('#993399')\"><AREA shape=\"rect\" coords=\"177,45,183,54\" href=\"javascript: setColor('#9933CC')\">");
  _colormap += ("<AREA shape=\"rect\" coords=\"185,45,191,54\" href=\"javascript: setColor('#9933FF')\"><AREA shape=\"rect\" coords=\"193,45,199,54\" href=\"javascript: setColor('#CC3300')\"><AREA shape=\"rect\" coords=\"201,45,207,54\" href=\"javascript: setColor('#CC3333')\"><AREA shape=\"rect\" coords=\"209,45,215,54\" href=\"javascript: setColor('#CC3366')\"><AREA shape=\"rect\" coords=\"217,45,223,54\" href=\"javascript: setColor('#CC3399')\"><AREA shape=\"rect\" coords=\"225,45,231,54\" href=\"javascript: setColor('#CC33CC')\"><AREA shape=\"rect\" coords=\"233,45,239,54\" href=\"javascript: setColor('#CC33FF')\"><AREA shape=\"rect\" coords=\"241,45,247,54\" href=\"javascript: setColor('#FF3300')\"><AREA shape=\"rect\" coords=\"249,45,255,54\" href=\"javascript: setColor('#FF3333')\"><AREA shape=\"rect\" coords=\"257,45,263,54\" href=\"javascript: setColor('#FF3366')\"><AREA shape=\"rect\" coords=\"265,45,271,54\" href=\"javascript: setColor('#FF3399')\">");
  _colormap += ("<AREA shape=\"rect\" coords=\"273,45,279,54\" href=\"javascript: setColor('#FF33CC')\"><AREA shape=\"rect\" coords=\"281,45,287,54\" href=\"javascript: setColor('#FF33FF')\"><AREA shape=\"rect\" coords=\"1,56,7,65\" href=\"javascript: setColor('#000000')\"><AREA shape=\"rect\" coords=\"9,56,15,65\" href=\"javascript: setColor('#000033')\"><AREA shape=\"rect\" coords=\"17,56,23,65\" href=\"javascript: setColor('#000066')\"><AREA shape=\"rect\" coords=\"25,56,31,65\" href=\"javascript: setColor('#000099')\"><AREA shape=\"rect\" coords=\"33,56,39,65\" href=\"javascript: setColor('#0000CC')\"><AREA shape=\"rect\" coords=\"41,56,47,65\" href=\"javascript: setColor('#0000FF')\"><AREA shape=\"rect\" coords=\"49,56,55,65\" href=\"javascript: setColor('#330000')\"><AREA shape=\"rect\" coords=\"57,56,63,65\" href=\"javascript: setColor('#330033')\"><AREA shape=\"rect\" coords=\"65,56,71,65\" href=\"javascript: setColor('#330066')\"><AREA shape=\"rect\" coords=\"73,56,79,65\" href=\"javascript: setColor('#330099')\">");
  _colormap += ("<AREA shape=\"rect\" coords=\"81,56,87,65\" href=\"javascript: setColor('#3300CC')\"><AREA shape=\"rect\" coords=\"89,56,95,65\" href=\"javascript: setColor('#3300FF')\"><AREA shape=\"rect\" coords=\"97,56,103,65\" href=\"javascript: setColor('#660000')\"><AREA shape=\"rect\" coords=\"105,56,111,65\" href=\"javascript: setColor('#660033')\"><AREA shape=\"rect\" coords=\"113,56,119,65\" href=\"javascript: setColor('#660066')\"><AREA shape=\"rect\" coords=\"121,56,127,65\" href=\"javascript: setColor('#660099')\"><AREA shape=\"rect\" coords=\"129,56,135,65\" href=\"javascript: setColor('#6600CC')\"><AREA shape=\"rect\" coords=\"137,56,143,65\" href=\"javascript: setColor('#6600FF')\"><AREA shape=\"rect\" coords=\"145,56,151,65\" href=\"javascript: setColor('#990000')\"><AREA shape=\"rect\" coords=\"153,56,159,65\" href=\"javascript: setColor('#990033')\"><AREA shape=\"rect\" coords=\"161,56,167,65\" href=\"javascript: setColor('#990066')\">");
  _colormap += ("<AREA shape=\"rect\" coords=\"169,56,175,65\" href=\"javascript: setColor('#990099')\"><AREA shape=\"rect\" coords=\"177,56,183,65\" href=\"javascript: setColor('#9900CC')\"><AREA shape=\"rect\" coords=\"185,56,191,65\" href=\"javascript: setColor('#9900FF')\"><AREA shape=\"rect\" coords=\"193,56,199,65\" href=\"javascript: setColor('#CC0000')\"><AREA shape=\"rect\" coords=\"201,56,207,65\" href=\"javascript: setColor('#CC0033')\"><AREA shape=\"rect\" coords=\"209,56,215,65\" href=\"javascript: setColor('#CC0066')\"><AREA shape=\"rect\" coords=\"217,56,223,65\" href=\"javascript: setColor('#CC0099')\"><AREA shape=\"rect\" coords=\"225,56,231,65\" href=\"javascript: setColor('#CC00CC')\"><AREA shape=\"rect\" coords=\"233,56,239,65\" href=\"javascript: setColor('#CC00FF')\"><AREA shape=\"rect\" coords=\"241,56,247,65\" href=\"javascript: setColor('#FF0000')\"><AREA shape=\"rect\" coords=\"249,56,255,65\" href=\"javascript: setColor('#FF0033')\">");
  _colormap += ("<AREA shape=\"rect\" coords=\"257,56,263,65\" href=\"javascript: setColor('#FF0066')\"><AREA shape=\"rect\" coords=\"265,56,271,65\" href=\"javascript: setColor('#FF0099')\"><AREA shape=\"rect\" coords=\"273,56,279,65\" href=\"javascript: setColor('#FF00CC')\"><AREA shape=\"rect\" coords=\"281,56,287,65\" href=\"javascript: setColor('#FF00FF')\"></MAP>");
  return _colormap;
}

function initColor(_color, _colorDefault) {
  if (_color != "") {
    document.frmPalette.ztChoix.style.backgroundColor = _color;
    document.frmPalette.ztChoix.value = _color;
  } else {
    document.frmPalette.ztChoix.style.backgroundColor = _colorDefault;
  }
}

function setColor(_color) {
  document.frmPalette.ztChoix.style.backgroundColor = _color;
  document.frmPalette.ztChoix.value = _color;
}

function selColor() {
  if (document.frmPalette.ztChoix.value!="") {
    document.Form1.ztCouleur.value = document.frmPalette.ztChoix.value;
    document.Form1.ztApercu.style.backgroundColor = document.frmPalette.ztChoix.value;
  }
  cClick();
}
//------------------------------------------------------------------------------
