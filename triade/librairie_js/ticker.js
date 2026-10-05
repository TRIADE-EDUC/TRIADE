/*
 +-------------------------------------------------------------------+
 |                     J S - T I C K E R   (v1.4)                    |
 |                                                                   |
 | Copyright Gerd Tentler               www.gerd-tentler.de/tools    |
 | Created: Oct. 20, 2004               Last modified: Jan. 26, 2007 |
 +-------------------------------------------------------------------+
 | This program may be used and hosted free of charge by anyone for  |
 | personal purpose as long as this copyright notice remains intact. |
 |                                                                   |
 | Obtain permission before selling the code for this program or     |
 | hosting this software on a commercial website or redistributing   |
 | this software over the Internet or in any other medium. In all    |
 | cases copyright must remain intact.                               |
 +-------------------------------------------------------------------+

======================================================================================================
 This script was tested with the following systems and browsers:

 - Windows XP: IE 6, NN 7, Opera 7 + 9, Firefox 2
 - Mac OS X:   IE 5, Safari 1

 If you use another browser or system, this script may not work for you - sorry.

 NOTE: IE 5 on Mac OS doesn't view elements (text etc.) below the ticker area properly; they will
 appear underneath the ticker area instead.
======================================================================================================
*/
//---------------------------------------------------------------------------------------------------------
// Ticker entries
//---------------------------------------------------------------------------------------------------------

var tickerEntries = new Array();



//---------------------------------------------------------------------------------------------------------
// Configuration
//---------------------------------------------------------------------------------------------------------

var tickerWidth = 100;                               // width (pixels)
var tickerMargin = 20;                               // margin (pixels)
var tickerDelay = 30;                                // scrolling delay (smaller = faster)
var tickerSpacer = " ";                            // spacer between ticker entries

var tickerBGColor = "";                       // background color
var tickerHLColor = "";                       // hilight (mouse over) color

var tickerFont = "Courier New, Courier, Monospace";  // font family (CSS-spec)
var tickerFontSize = 16;                             // font size (pixels)
var tickerFontColor = "blue";                        // font color

var tickerBorderWidth = 2;                           // border width (pixels)
var tickerBorderStyle = "groove";                    // border style (CSS-spec)
var tickerBorderColor = "#FFFFFF";                   // border color

//---------------------------------------------------------------------------------------------------------
// Functions
//---------------------------------------------------------------------------------------------------------

var DOM = document.getElementById;
var IE4 = document.all;

var tickerIV, tickerID;
var tickerItems = new Array();
var tickerHeight = tickerFontSize + 8;

function tickerGetObj(id) {
  if(DOM) return document.getElementById(id);
  else if(IE4) return document.all[id];
  else return false;
}

function tickerObject(id) {
  this.elem = tickerGetObj(id);
  this.width = this.elem.offsetWidth;
  this.x = tickerWidth;
  this.css = this.elem.style;
  this.css.width = this.width + '%';
  this.css.left = this.x + 'px';
  this.move = false;
  return this;
}

function tickerNext() {
  if(!DOM && !IE4) return;
  var obj = tickerItems[tickerID];
  obj.x = tickerWidth;
  obj.css.left = tickerWidth + '%';
  obj.move = true;
}

function tickerMove() {
  if(!DOM && !IE4) return;
  for(var i = 0; i < tickerItems.length; i++) {
    if(tickerItems[i].move) {
      if(tickerItems[i].x > -tickerItems[i].width) {
        tickerItems[i].x -= 2;
        tickerItems[i].css.left = tickerItems[i].x + 'px';
      }
      else tickerItems[i].move = false;
    }
  }
  if(tickerItems[tickerID].x + tickerItems[tickerID].width <= tickerWidth) {
    tickerID++;
    if(tickerID >= tickerItems.length) tickerID = 0;
    tickerNext();
  }
}

function tickerStart(init) {
  if(!DOM && !IE4) return;
  if(tickerBGColor) {
    var obj = tickerGetObj('divTicker');
    obj.style.backgroundColor = tickerBGColor;
  }
  if(init) {
    tickerID = 0;
    tickerNext();
  }
  tickerIV = setInterval('tickerMove()', tickerDelay);
}

function tickerStop() {
  if(!DOM && !IE4) return;
  clearInterval(tickerIV);
  if(tickerHLColor) {
    var obj = tickerGetObj('divTicker');
    obj.style.backgroundColor = tickerHLColor;
  }
}

function tickerInit() {
  if(!DOM && !IE4) return;
  for(var i = 0; i < tickerEntries.length; i++) {
    tickerItems[i] = new tickerObject('divTickerEntry' + (i+1));
  }
  var obj = tickerGetObj('divTicker');
  obj.style.width = tickerWidth + '%';
  obj.style.visibility = 'visible';
  tickerStart(true);
}

function tickerReload() {
  if(!DOM && !IE4) return;
  document.location.reload();
}

window.onresize = tickerReload;
window.onload = tickerInit;

//---------------------------------------------------------------------------------------------------------
// Build ticker
//---------------------------------------------------------------------------------------------------------

function marquee(text) {

	tickerEntries = new Array(text,"                                       ");

document.write('<style> ' +
               '#divTicker { ' +
               'position: absolute; ' +
               'width: 10000px; ' +
               'height: ' + tickerHeight + 'px; ' +
               'cursor: default; ' +
               'overflow: hidden; ' +
               'visibility: hidden; ' +
               (tickerBorderWidth ? 'border-width: ' + tickerBorderWidth + 'px; ' : '') +
               (tickerBorderStyle ? 'border-style: ' + tickerBorderStyle + '; ' : '') +
               (tickerBorderColor ? 'border-color: ' + tickerBorderColor + '; ' : '') +
               '} ' +
               '.cssTickerContainer { ' +
               'position: relative; ' +
               'height: ' + tickerHeight + 'px; ' +
               'margin-top: ' + tickerMargin + 'px; ' +
               'margin-bottom: ' + tickerMargin + 'px; ' +
               '} ' +
               '.cssTickerEntry { ' +
               'font-family: ' + tickerFont + '; ' +
               'font-size: ' + tickerFontSize + 'px; ' +
               'color: ' + tickerFontColor + '; ' +
               '} ' +
               '</style>');

document.write('<div class="cssTickerContainer">' +
               '<div id="divTicker" onMouseOver="tickerStop()" onMouseOut="tickerStart()">');

for(var i = 0; i < tickerEntries.length; i++) {
  document.write('<div id="divTickerEntry' + (i+1) + '" class="cssTickerEntry" ' +
                 'style="position:absolute; top:2px; white-space:nowrap">' +
                 tickerEntries[i] + ((tickerEntries.length > 1) ? ' ' + tickerSpacer + '&nbsp;' : '') +
                 '</div>');
}
document.write('</div></div>');


}


//---------------------------------------------------------------------------------------------------------
