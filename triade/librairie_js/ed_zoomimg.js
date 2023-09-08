/** ZoomImg, V1.0
* Réalisé par Elforia Design
* http://www.elforia-design.fr
* infos@elforia-design.fr
* 15/02/2007
*
* Distribué sous licence GPL
*/

/**
* Retourne la position de la souris par rapport au bord haut gauche du navigateur et non de la page
*/
function edz_getMouseXY(evt) {
  var evt = evt?evt:window.event?window.event:null; if(!evt){ return null;}

  var pos = new Array();
  if (evt.pageX) {
    pos[0] = evt.pageX;
    pos[1] = evt.pageY;
  } else {
    pos[0] = evt.clientX + document.documentElement.scrollLeft;
    pos[1] = evt.clientY + document.documentElement.scrollTop;
  }

  return pos;
}
/**
* Le curseur a quittÃ© le zoom :
*   Soit c'est un dÃ©placement trÃ¨s rapide et le curseur est encore dans l'image, alors il faut appeler edz_zoomMove(evt)
*   Soit le curseur a quittÃ© l'image, alors il n'existe plus d'objet Zoom courant
*/
function edz_zoomOut(evt) {
  var pos = edz_getMouseXY(evt);
  if (pos==null) return;

  var zoom = edz_zooms.zoomCur();
  if (zoom.isIn(pos[0],pos[1])) edz_zoomMove(evt); // La souris est dedans
  else {
    edz_zooms.setZoomCur(null);
    zoom.getDivLoupe().style.display = 'none';
  }
}

/** 
* Souris est entrÃ©e dans un div :
*
*/

function edz_zoomIn(evt, div) { 
  var zoom = edz_zooms.getZoom(div);
  if (zoom==null) zoom = edz_zooms.addZoom(div);

  zoom._zooms.setZoomCur(zoom);

  zoom.findPos(); /* Retrouver la position du div Ã  chaque fois que l'on entre */
  var divLoupe = zoom.getDivLoupe();
  var imgLoupe = zoom.getImgLoupe();

  if (!zoom.ready()) {
    zoom._zooms.setLoupeLoading();
    imgLoupe.style.display = 'none';
  } else {
    zoom._zooms.setWidthLoupe( zoom._zooms.widthLoupeCopy() );
    zoom._zooms.setHeightLoupe( zoom._zooms.heightLoupeCopy() );
    imgLoupe.style.display = 'inline';
  }

  if (zoom.isBigLoaded()) imgLoupe.src = zoom.getBigImg().src;
  if (zoom.isSmallLoaded()) {
    imgLoupe.width = zoom.getSmallImg().width * zoom._zooms.factZoom();
    imgLoupe.height = zoom.getSmallImg().height * zoom._zooms.factZoom();
  }

  edz_zoomMove(evt);

  divLoupe.style.display = 'inline';
}

function edz_zoomMove(evt) {
  var zoom = edz_zooms.zoomCur();
  if (zoom==null) return;
  zoom.move(evt);
}
/**
* Une petite image est finie de charger. 
* CrÃ©er l'objet Zoom s'il n'existe pas et l'ajouter Ã  la liste
*/
function edz_smallLoaded(img) { 
  var div = edz_findDiv(img);
  if (div==null) return;

  var zoom = edz_zooms.getZoom(div);
  if (zoom==null) zoom = edz_zooms.addZoom(div);
  zoom.smallLoaded(img);

  if (edz_zooms.isCurZoom(zoom)) {
    zoom.getImgLoupe().width  = img.width  * zoom._zooms.factZoom();
    zoom.getImgLoupe().height = img.height * zoom._zooms.factZoom();
  }
}
/** 
* Une grande image est finie de charger 
* CrÃ©er l'objet Zoom s'il n'existe pas et l'ajouter Ã  la liste
*/
function edz_bigLoaded(img) { 
  var div = edz_findDiv(img);
  if (div==null) return;
  var zoom = edz_zooms.getZoom(div);
  if (zoom==null) zoom = edz_zooms.addZoom(div);
  zoom.bigLoaded(img);

  if (edz_zooms.isCurZoom(zoom)) {
    zoom.getDivLoupe().style.width  = zoom._zooms.widthLoupeCopy()+'px';
    zoom.getDivLoupe().style.height = zoom._zooms.heightLoupeCopy()+'px';
    zoom.getImgLoupe().src = img.src;
    zoom.getImgLoupe().style.display = 'inline';
  }
}
/**
* Retourne le premier node parent qui est un Ã©lÃ©ment div
*/
function edz_findDiv(img) {
  var node = img;
  while (node.parentNode && node.parentNode.nodeName!='DIV') { node = node.parentNode;}

  return node.parentNode;
}

/**
* Un objet Zoom
*/
function Zoom(zooms, div, divLoupe, imgLoupe) {
  this._zooms = zooms; /* L'objet Zooms */
  this._div = div; /* Le node du div */
  this._divLoupe = divLoupe; /* Le node div, conteneur de la loupe */
  this._imgLoupe = imgLoupe; /* Le node img inclu dans this._divLoupe */
  this._smallImg = null; /* Le node de la petite image */
  this._bigImg = null; /* Le node de la grande image */
  this._smallLoaded = false;
  this._bigLoaded = false;

  this._x = 0; /* Position du div par rapport au bord gauche du navigateur */
  this._y = 0; /* Position du div par rapport au bord haut du navigateur */

  this._lastMouseX = 0; /* DerniÃ¨re position de la souris. NÃ©cessaire lors de la modification du Zoom */
  this._lastMouseY = 0;

  this.getDiv = function() { return this._div; }
  this.getDivLoupe = function() { return this._divLoupe; }
  this.getImgLoupe = function() { return this._imgLoupe; }
  this.getSmallImg = function() { return this._smallImg; }
  this.getBigImg = function() { return this._bigImg; }
  this.smallLoaded = function(img) { this._smallImg = img; }
  this.isSmallLoaded = function() { if (this._smallImg!=null) return true; return false; }
  this.isBigLoaded = function() { if (this._bigImg!=null) return true; return false; }
  this.bigLoaded = function(img) { this._bigImg = img; }
  this.ready = function() { 
    if (this._smallImg!=null && this._bigImg!=null) return true;
    return false;
  }
  this.x = function() { return this._x; }
  this.y = function() { return this._y; }

  this.isIn = function(x, y) { 
    if (x<=this.x()) return false;
    if (y<=this.y()) return false;

    var smallImgWidth, smallImgHeight;
    if (this._smallImg==null) {
      smallImgWidth  = this._zooms.widthLoupe();
      smallImgHeight = this._zooms.heightLoupe();
    } else {
      smallImgWidth  = this._smallImg.width;
      smallImgHeight = this._smallImg.height;
    }

    if (x>=this.x()+smallImgWidth) return false;
    if (y>=this.y()+smallImgHeight) return false;

    return true;
  }

  this.findPos = function() {
    var obj = this._div;
    var curleft = curtop = 0;

    if (obj.offsetParent) {
      curleft = obj.offsetLeft;
      curtop = obj.offsetTop;
      while (obj = obj.offsetParent) {
        curleft += obj.offsetLeft;
        curtop += obj.offsetTop;
      }
    }

    this._x = curleft;
    this._y = curtop;
  }

  this.move = function(evt) {

    var xx, yy;
    if (evt!=null) {
      var pos = edz_getMouseXY(evt);
      xx = pos[0];
      yy = pos[1];

      this._lastMouseX = xx;
      this._lastMouseY = yy;

    } else {
      xx = this._lastMouseX;
      yy = this._lastMouseY;
    }

    var smallImgWidth, smallImgHeight;
    if (this._smallImg==null) {
      smallImgWidth  = this._zooms.widthLoupe();
      smallImgHeight = this._zooms.heightLoupe();
    } else {
      smallImgWidth  = this._smallImg.width;
      smallImgHeight = this._smallImg.height;
    }

    var b = this._zooms.border()*2;

    if (xx-this.x()<this._zooms.widthLoupe()/2) leftDiv = this.x();
    else if (xx-this.x()>smallImgWidth-this._zooms.widthLoupe()/2-b) leftDiv = this.x() + smallImgWidth-this._zooms.widthLoupe() - b;
    else leftDiv = xx-this._zooms.widthLoupe()/2;
    leftDiv = Math.floor(leftDiv);

    if (yy-this.y()<this._zooms.heightLoupe()/2) topDiv = this.y();
    else if (yy-this.y()>smallImgHeight-this._zooms.heightLoupe()/2-b) topDiv = this.y() + smallImgHeight-this._zooms.heightLoupe()-b;
    else topDiv = yy-this._zooms.heightLoupe()/2;
    topDiv = Math.floor(topDiv);

    this._divLoupe.style.marginLeft = Math.floor(leftDiv)+'px';
    this._divLoupe.style.marginTop= Math.floor(topDiv)+'px';

    leftImg = -(xx-this.x())*this._zooms.factZoom() + this._zooms.widthLoupe()/2;
    if (leftImg>0) leftImg = 0;
    else if (-leftImg>=smallImgWidth*this._zooms.factZoom()-this._zooms.widthLoupe()-b) leftImg = -(smallImgWidth*this._zooms.factZoom()-this._zooms.widthLoupe()-b);

    topImg  = -(yy-this.y())*this._zooms.factZoom() + this._zooms.heightLoupe()/2 ;
    if (topImg>0) topImg = 0;
    else if (-topImg>=smallImgHeight*this._zooms.factZoom()-this._zooms.heightLoupe()-b) topImg = -(smallImgHeight*this._zooms.factZoom()-this._zooms.heightLoupe()-b);

    this._imgLoupe.style.marginLeft = Math.floor(leftImg)+'px';
    this._imgLoupe.style.marginTop = Math.floor(topImg)+'px';
  }
}

/**
* Liste des objets Zoom de la page
*/
function Zooms() {
  this._zooms = new Array();
  this._divLoupe = null; /* Le node div conteneur de la loupe */
  this._imgloupe = null; /* Le node img, image de la loupe */

  this._widthLoupeLoading = 130; /* Dimensions de la loupe en cours de chargement */
  this._heightLoupeLoading = 30;

  this._widthLoupe = 0; /* Dimensions rÃ©Ã©lles de la loupe (this._widthLoupeLoading ou this._curWidthLoupe) */
  this._heightLoupe = 0;

  this._copyWidthLoupe = 320; /* Sauvegarde des dimensions de la loupe, sachant qu'elle peut passer Ã  l'Ã©tat loading sans perdre ses dimensions */
  this._copyHeightLoupe = 180;

  this._minWidthLoupe = 50;
  this._maxWidthLoupe = 400;
  this._scaleDimsLoupe = 1.2;
  this._scaleFactLoupe = 1.2;

  this._factZoom = 5;

  this._border = 2; /* Nombre de pixels de bordure de l'image */
  this._borderColor = '#000';

  this._textLoading = "Zoom loading...";

  this._zoomCur = null; /* L'objet Zoom actif */

  this.getZoom = function(div) {
    for (var i=0;i<this._zooms.length;i++) if (div==this._zooms[i].getDiv()) return this._zooms[i];
    return null;
  }

  this.addZoom = function(div) {
    if (this._divLoupe==null) this.createDivLoupe();
    var zoom = new Zoom(this, div, this._divLoupe, this._imgLoupe);
    this._zooms.push(zoom);

    return zoom;
  }

  this.widthLoupe = function() { return this._widthLoupe; }
  this.heightLoupe = function() { return this._heightLoupe; }
  this.widthLoupeCopy = function() { return this._copyWidthLoupe; }
  this.heightLoupeCopy = function() { return this._copyHeightLoupe; }
  this.setLoupeLoading = function() { this.setWidthLoupe(this._widthLoupeLoading); this.setHeightLoupe(this._heightLoupeLoading); }
  this.factZoom = function() { return this._factZoom; }
  this.setWidthLoupe = function(width) { this._widthLoupe = width; if (this._divLoupe!=null) this._divLoupe.style.width = width+'px';}
  this.setHeightLoupe = function(height) { this._heightLoupe = height; if (this._divLoupe!=null) this._divLoupe.style.height = height+'px'; }
  this.border = function() { return this._border; }
  this.setZoomCur = function(zoom) { this._zoomCur = zoom; }
  this.zoomCur = function() { return this._zoomCur; }

  this.isCurZoom = function(zoom) { if (this._zoomCur==zoom) return true; return false; }

  this.createDivLoupe = function() {
    this._divLoupe = document.createElement('div');
    document.body.appendChild(this._divLoupe);

    this._divLoupe.appendChild( document.createTextNode(this._textLoading) );

    this._divLoupe.style.position = 'absolute';
    this._divLoupe.style.top = 0;
    this._divLoupe.style.display = 'none';
    this._divLoupe.style.width = this._widthLoupe + 'px';
    this._divLoupe.style.height = this._heightLoupe + 'px';
    this._divLoupe.style.overflow = 'hidden';
    this._divLoupe.style.backgroundColor = '#ffffe1';
    this._divLoupe.style.margin = '0';
    this._divLoupe.style.padding = '0';
    this._divLoupe.style.border = this._border + 'px solid ' + this._borderColor;
    this._divLoupe.onmousemove = function(e) { edz_zoomMove(e?e:event); };

    this._imgLoupe = document.createElement('img');
    this._imgLoupe.onmousemove = function(e) { edz_zoomMove(e?e:event); };
    this._imgLoupe.onmouseout = function(e) { edz_zoomOut(e?e:event); };

    this._imgLoupe.style.margin = '0';
    this._imgLoupe.style.padding = '0';
    this._imgLoupe.style.border = '0';
    this._imgLoupe.style.position = 'absolute';
    this._imgLoupe.style.top = 0;
    this._imgLoupe.style.left = 0;
    this._imgLoupe.style.display = 'none';

    this._divLoupe.appendChild(this._imgLoupe);

    if (browser.name()=='opera') document.onkeypress = edz_chgZoom;
    else if (document.all) document.onkeydown = edz_chgZoom; // IE
    else {
      document.onkeydown = edz_chgZoom;
      window.captureEvents(Event.KEYDOWN);
      window.onkeydown = edz_chgZoom;
    }
  }

  this.delZoomWidth = function() {
    if (this._zoomCur==null) return;
    var newWidth = this._widthLoupe/this._scaleDimsLoupe;
    if (newWidth<this._minWidthLoupe) return;

    this._copyWidthLoupe  = Math.floor(newWidth);
    this._copyHeightLoupe = Math.floor(this._heightLoupe/this._scaleDimsLoupe);

    this.setWidthLoupe(this._copyWidthLoupe);
    this.setHeightLoupe(this._copyHeightLoupe);
    this._zoomCur.move(null);
  }

  this.addZoomWidth = function() {
    if (this._zoomCur==null) return;
    var newWidth = this._widthLoupe*this._scaleDimsLoupe;
    if (newWidth>this._maxWidthLoupe) return;

    this._copyWidthLoupe  = Math.floor(newWidth);
    this._copyHeightLoupe = Math.floor(this._heightLoupe*this._scaleDimsLoupe);

    this.setWidthLoupe(this._copyWidthLoupe);
    this.setHeightLoupe(this._copyHeightLoupe);
    this._zoomCur.move(null);
  }

  this.delFactZoom = function() {
    if (this._zoomCur==null) return;
    this._factZoom /= this._scaleFactLoupe;
    this._imgLoupe.width = this._zoomCur.getSmallImg().width * this._factZoom;
    this._imgLoupe.height = this._zoomCur.getSmallImg().height * this._factZoom;    
    this._zoomCur.move(null);
  }

  this.addFactZoom = function() {
    if (this._zoomCur==null) return;
    this._factZoom *= this._scaleFactLoupe;
    this._imgLoupe.width = this._zoomCur.getSmallImg().width * this._factZoom;
    this._imgLoupe.height = this._zoomCur.getSmallImg().height * this._factZoom;    
    this._zoomCur.move(null);
  }

/**
* Configurations 
*/ 
  this.setFactZoom = function(fact) { this._factZoom = fact; }
  this.setDimsLoupeLoading = function(width, height) { this._widthLoupeLoading = width; this._heightLoupeLoading = height; }
  this.setInitialWidthLoupe = function(width, height) { this._copyWidthLoupe = width; this._copyHeightLoupe = height; }
  this.setMinMaxLoupe = function(min, max) { this._minWidthLoupe = min; this._maxWidthLoupe = max; }
  this.setSteps = function(width, zoom) { this._scaleDimsLoupe = width; this._scaleFactLoupe = zoom; }
  this.setBorder = function(widge, color) { this._border = widge; this._borderColor = color; }
  this.setTextLoading = function(text) { this._textLoading = text; }
}

function edz_chgZoom(evt) {
  var evt = evt?evt:window.event?window.event:null; if(!evt){ return true;}

  if (evt.keyCode==37 || evt.keyCode==100) { edz_zooms.delZoomWidth(); return false; } /* left */
  if (evt.keyCode==39 || evt.keyCode==102) { edz_zooms.addZoomWidth(); return false; } /* right */
  if (evt.keyCode==40 || evt.keyCode==98) { edz_zooms.delFactZoom(); return false; } /* down */
  if (evt.keyCode==38 || evt.keyCode==104) { edz_zooms.addFactZoom(); return false; } /* up */

  return true;
}

function Browser() {
  this._browser = navigator.appName;
  this._version = parseFloat(navigator.appVersion)

  this._name = "inconnu";
  if (this._browser=='Microsoft Internet Explorer') this._name = 'ie';
  else if (navigator.userAgent.toLowerCase().indexOf('opera')!=-1) this._name = 'opera';
  else this._name = 'mozilla';

  this.name = function() { return this._name; }
}

var browser = new Browser();
var edz_zooms = new Zooms(); /* Liste des objets zoomables de la page */

/**
* edz_zooms.setFactZoom(fact)                   : Facteur initial du zoom
* edz_zooms.setDimsLoupeLoading(width, height)  : Largeur et hauteur de la loupe 'En cours de chargement...'
* edz_zooms.setInitialWidthLoupe(width, height) : Largeur et hauteur initiales de la loupe (lorsque les images sont chargÃ©es)
* edz_zooms.setMinMaxLoupe(min, max)            : Largeur minimale et maximale que peut prendre la loupe modifiÃ©e par les flÃ¨ches
* edz_zooms.setSteps(width, zoom)               : Multiplicateurs de largeur et de facteur de zoom
* edz_zooms.setBorder(widge, color)             : bordure de la loupe en pixels et couleur
* edz_zooms.setTextLoading(text)                : texte Ã  afficher lorsque les images sont en cours de chargement
*/

