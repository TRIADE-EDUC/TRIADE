var g_LoadedIndex;
var g_LoadedUrl, Url2Load;
var g_inpopup = false;
var g_framaplayerUrl = "http://framaplayer.keonox.com/demo/framaplayer/"

function playxmld(url) {
// Play distant xml file (using proxy.php)
	var proxy= g_framaplayerUrl+"proxy.php?xml=";
	var xmlfile = proxy+url;
	var p = document.mp3player;
	playmp3(xmlfile);
	p.SetVariable("timeStatus","loading xml")
}

function playxml(url) {
//play local xml file
	var proxy= "";
	var xmlfile = proxy+url;
	var p = document.mp3player;
	playmp3(xmlfile);
	p.SetVariable("timeStatus","loading xml")
}

function playmp3() {
	var popupFound=false;
	var opener_urlprefix="";
	g_LoadedUrl = arguments[0];
	//alert(g_LoadedUrl);
	if ((typeof(mp3Popup)!="undefined") && (mp3Popup.popupOpened!=null) ) {
		var popupFound=true;
		if (arguments[0].indexOf("/")==-1) { // not a relative link
		  arguments[0] =  GetOpenerUrl() + arguments[0] ;
		}
		var p = mp3Popup.document.mp3player;
		mp3Popup.focus();
		mp3Popup.g_LoadedUrl = arguments[0];
	} else {
		var p = window.document.mp3player;
	}
	
	afAMPargs = new Array(); afAMPargsDefault = new Array();
	afAMPargs[0] = 'url'; afAMPargsDefault[0] = null;
	afAMPargs[1] = 'soundTitle'; afAMPargsDefault[1] = ' ';
	afAMPargs[2] = 'soundAuthor'; afAMPargsDefault[2] = ' ';
	afAMPargs[3] = 'soundDownloadUrl'; afAMPargsDefault[3] = null;
	afAMPargs[4] = 'my_bitrate'; afAMPargsDefault[4] = null;
	
	if ( (p) && (p.GetVariable('url')=='0') ) {
		p.SetVariable('autolaunch','true');
		if (arguments[0].indexOf('.xml')==-1) {
			p.SetVariable('mode','single');
		} else {
			p.SetVariable('mode','playlist');
		}
		for(var i=0; i<afAMPargs.length; i++) {
		  if (arguments[i]!=null) {
			p.SetVariable(afAMPargs[i],arguments[i]); 				
		  } else {
		  	if (afAMPargsDefault[i]!=null) { p.SetVariable(afAMPargs[i],afAMPargsDefault[i]); }
		  }
		  //alert(afAMPargs[i]+"="+p.GetVariable(afAMPargs[i])); // dbg
		} 
		//if(g_inpopup==true) { transferPlaylist() }
		//alert(g_LoadedUrl);		
	} else {
		alert('afAMP Player not found/loaded');
		return false;
	}
}
var comingUrl;

function afAmpPopup() {
	mp3Popup= window.open(g_framaplayerUrl+'index.php', 'afAmpPopup', 'scrollbars=yes,resizable=yes,menubar=yes,toolbar=yes,width=500,height=300');
	if (mp3Popup.opener == null) mp3Popup.opener = self;
}

function isOpenerStillHere() {
	if (opener!=null){
		//alert("On vient de "+opener.location.href+" et la fenêtre est toujours ouverte :)");

		var FullComingUrl = opener.document.location.href;
		arr = FullComingUrl.split("/");
		arr.pop();
		comingUrl = arr.join("/");
		return true;

	} else {
		//alert("Impossible de récuperer la page web ayant permis l'ouverture de la popup")
		return false;
	}
}

function GetOpenerUrl() {

  var opener_url = (g_inpopup==true) ? opener.document.location.href : document.location.href;
  arr = opener_url.split("/");
  arr.pop();
  var sound_path = arr.join("/");
  sound_path = sound_path+"/";
  return sound_path;
}

function GetOpenerInfos() {
	p = opener.document.mp3player;
	t = window.document.mp3player;
	if ((p) && (t)) {
		var mode = p.GetVariable('mode');
		//alert("On recupere les infos du lecteur de l'autre page\n, on recupère le titre joué\n, on le lance dans ce lecteur\n, on arrête l'autre lecteur\n, et on cache le lien");
		if (mode=="playlist") {
			oldPlaylist = p.GetVariable("lastPlaylist");
			if (oldPlaylist.indexOf("/")==-1) { // not a relative link
			  oldPlaylist =  GetOpenerUrl() + oldPlaylist ;
			}
			oldIndex = p.GetVariable("soundIndex");
			p.SetVariable("PlayStatus", "Popup");
			p.SetVariable("stopSndExt", 1);
			t.SetVariable("startIndex", oldIndex) ;
			t.SetVariable("url", oldPlaylist) ;
			g_LoadedUrl = oldPlaylist;
			//alert(oldPlaylist);
		} else {
			p.SetVariable("stopSndExt", 1);
			oldSndUrl = p.GetVariable("sndurl");
			if (oldSndUrl.indexOf("/")==-1) { // not a relative link
			  oldSndUrl =  GetOpenerUrl() + oldSndUrl ;
			}
			t.SetVariable("url", oldSndUrl);
			g_LoadedUrl = oldSndUrl;
			//alert(oldSndUrl);
		}
		
		transferPlaylist();
		document.getElementById('recup').innerHTML = "&nbsp;";
	}
}

function focusOpener() {
    if (!opener.closed) {
        opener.focus();
    } else {
		window.blur();
	}
}

function swapItem(itemid) {
	window.focus();
	if (document.all) { detail=eval("document.all."+itemid); } else { detail=document.getElementById(itemid); }
	if (detail.style.display=="none") { detail.style.display="block"; } else { detail.style.display="none"; }
}

function diplayInner(myid, mytext) {
	if ((myid=="debug") || (myid=="debug2")) { mytext = "debug: "+mytext; }
	if (document.all) { dest=eval("document.all."+myid); } else { dest=document.getElementById(myid); }
	dest.innerHTML = mytext;
}

function displayLoadFields() {
	swapItem("loadfields");
}

function loadFromFields() {
	url=document.bidon.loadurl.value;
	bitrate=document.bidon.bitrate.options[document.bidon.bitrate.selectedIndex].value;
	if (url!="") { playmp3(url, '', '', '', bitrate); } else { diplayInner("debug", "Pas d'url"); }
}

function transferPlaylist() {
	p = window.document.mp3player;
	var mode = p.GetVariable('mode');
	var oldSndUrl = p.GetVariable("sndurl");
	if (oldSndUrl=="") { return diplayInner("debug", 'Pas de fichier à jouer'); }
	if (mode=="playlist") {
		oldPlaylist = p.GetVariable("lastPlaylist");
		oldIndex = p.GetVariable("soundIndex");
		window.frames['myiframe'].location.href = "xmltab.php?xmlloaded="+oldPlaylist;
	} else {
		diplayInner("debug", 'Le fichier joué ne fait pas partie d\'une playlist')
		window.frames['myiframe'].location.href = "xmltab.php";
	}

}

function UpdateColors(i, n) {
	ResetColors(n);
	SetColor(i);
}

function ResetColors(n, color) {
	if (typeof(color)=="undefined") { color = "#FFFFFF"; }
	//window.focus();
	for (i=0; i<=n; i++) {
		if (document.all) { detail=eval("document.all.file_"+i); } else { detail=document.getElementById("file_"+i); }
		detail.style.backgroundColor=color;
	}
}

function SetColor(i, color) {
	if (typeof(color)=="undefined") { color = "#EAEAFF"; }
	//window.focus();
	if (document.all) { detail=eval("document.all.file_"+i); } else { detail=document.getElementById("file_"+i); }
	if (detail.style.backgroundColor!=color) { detail.style.backgroundColor=color; }
}

function Intervalle() {
	p = parent.document.mp3player;
	s = p.GetVariable("soundIndex");
	n = p.GetVariable("my_playlistSoundsCount");
	//diplayInner("debug", s);
	if (g_LoadedIndex!=s) {
		UpdateColors(s, n);
		g_LoadedIndex = s;
	}
}

function TestIfUrlHasChanged() {
	diplayInner("debug", "Url2Load="+Url2Load);
	diplayInner("debug2", "g_LoadedUrl="+g_LoadedUrl);
	if (Url2Load!=g_LoadedUrl) {
		transferPlaylist();
		Url2Load=g_LoadedUrl;
	}
}

function LoadIndex(i) {
	p = parent.document.mp3player;
	s = p.SetVariable("LoadIndex", i);
}

function GetM3U(url) {
	document.location.href = g_framaplayerUrl+"xml2m3u.php?xmlloaded="+url;
}

// Browser Detect  v2.1.6
// documentation: http://www.dithered.com/javascript/browser_detect/index.html
// license: http://creativecommons.org/licenses/by/1.0/
// code by Chris Nott (chris[at]dithered[dot]com)
function BrowserDetect() {
   var ua = navigator.userAgent.toLowerCase(); 

   // browser engine name
   this.isGecko       = (ua.indexOf('gecko') != -1 && ua.indexOf('safari') == -1);
   this.isAppleWebKit = (ua.indexOf('applewebkit') != -1);
   // browser name
   this.isKonqueror   = (ua.indexOf('konqueror') != -1); 
   this.isSafari      = (ua.indexOf('safari') != - 1);
   this.isOmniweb     = (ua.indexOf('omniweb') != - 1);
   this.isOpera       = (ua.indexOf('opera') != -1); 
   this.isIcab        = (ua.indexOf('icab') != -1); 
   this.isAol         = (ua.indexOf('aol') != -1); 
   this.isIE          = (ua.indexOf('msie') != -1 && !this.isOpera && (ua.indexOf('webtv') == -1) ); 
   this.isMozilla     = (this.isGecko && ua.indexOf('gecko/') + 14 == ua.length);
   this.isFirebird    = (ua.indexOf('firebird/') != -1);
   this.isNS          = ( (this.isGecko) ? (ua.indexOf('netscape') != -1) : ( (ua.indexOf('mozilla') != -1) && !this.isOpera && !this.isSafari && (ua.indexOf('spoofer') == -1) && (ua.indexOf('compatible') == -1) && (ua.indexOf('webtv') == -1) && (ua.indexOf('hotjava') == -1) ) );
   // spoofing and compatible browsers
   this.isIECompatible = ( (ua.indexOf('msie') != -1) && !this.isIE);
   this.isNSCompatible = ( (ua.indexOf('mozilla') != -1) && !this.isNS && !this.isMozilla);
   // rendering engine versions
   this.geckoVersion = ( (this.isGecko) ? ua.substring( (ua.lastIndexOf('gecko/') + 6), (ua.lastIndexOf('gecko/') + 14) ) : -1 );
   this.equivalentMozilla = ( (this.isGecko) ? parseFloat( ua.substring( ua.indexOf('rv:') + 3 ) ) : -1 );
   this.appleWebKitVersion = ( (this.isAppleWebKit) ? parseFloat( ua.substring( ua.indexOf('applewebkit/') + 12) ) : -1 );
   // browser version
   this.versionMinor = parseFloat(navigator.appVersion); 
   // correct version number
   if (this.isGecko && !this.isMozilla) {
      this.versionMinor = parseFloat( ua.substring( ua.indexOf('/', ua.indexOf('gecko/') + 6) + 1 ) );
   } else if (this.isMozilla) {
      this.versionMinor = parseFloat( ua.substring( ua.indexOf('rv:') + 3 ) );
   } else if (this.isIE && this.versionMinor >= 4) {
      this.versionMinor = parseFloat( ua.substring( ua.indexOf('msie ') + 5 ) );
   } else if (this.isKonqueror) {
      this.versionMinor = parseFloat( ua.substring( ua.indexOf('konqueror/') + 10 ) );
   } else if (this.isSafari) {
      this.versionMinor = parseFloat( ua.substring( ua.lastIndexOf('safari/') + 7 ) );
   } else if (this.isOmniweb) {
      this.versionMinor = parseFloat( ua.substring( ua.lastIndexOf('omniweb/') + 8 ) );
   } else if (this.isOpera) {
      this.versionMinor = parseFloat( ua.substring( ua.indexOf('opera') + 6 ) );
   } else if (this.isIcab) {
      this.versionMinor = parseFloat( ua.substring( ua.indexOf('icab') + 5 ) );
   }  
   this.versionMajor = parseInt(this.versionMinor); 
   // dom support
   this.isDOM1 = (document.getElementById);
   this.isDOM2Event = (document.addEventListener && document.removeEventListener);
   // css compatibility mode
   this.mode = document.compatMode ? document.compatMode : 'BackCompat';
   // platform
   this.isWin    = (ua.indexOf('win') != -1);
   this.isWin32  = (this.isWin && ( ua.indexOf('95') != -1 || ua.indexOf('98') != -1 || ua.indexOf('nt') != -1 || ua.indexOf('win32') != -1 || ua.indexOf('32bit') != -1 || ua.indexOf('xp') != -1) );
   this.isMac    = (ua.indexOf('mac') != -1);
   this.isUnix   = (ua.indexOf('unix') != -1 || ua.indexOf('sunos') != -1 || ua.indexOf('bsd') != -1 || ua.indexOf('x11') != -1)
   this.isLinux  = (ua.indexOf('linux') != -1);
   // specific browser shortcuts
   this.isNS4x = (this.isNS && this.versionMajor == 4);
   this.isNS40x = (this.isNS4x && this.versionMinor < 4.5);
   this.isNS47x = (this.isNS4x && this.versionMinor >= 4.7);
   this.isNS4up = (this.isNS && this.versionMinor >= 4);
   this.isNS6x = (this.isNS && this.versionMajor == 6);
   this.isNS6up = (this.isNS && this.versionMajor >= 6);
   this.isNS7x = (this.isNS && this.versionMajor == 7);
   this.isNS7up = (this.isNS && this.versionMajor >= 7);
   
   this.isIE4x = (this.isIE && this.versionMajor == 4);
   this.isIE4up = (this.isIE && this.versionMajor >= 4);
   this.isIE5x = (this.isIE && this.versionMajor == 5);
   this.isIE55 = (this.isIE && this.versionMinor == 5.5);
   this.isIE5up = (this.isIE && this.versionMajor >= 5);
   this.isIE6x = (this.isIE && this.versionMajor == 6);
   this.isIE6up = (this.isIE && this.versionMajor >= 6);
   
   this.isIE4xMac = (this.isIE4x && this.isMac);
}
var browser = new BrowserDetect();

//flash detection
function FlashDetection() {
var difference;
var startTimer= new Date();
//alert("test");
//alert(startTimer.getTime());
	// Initialize variables and arrays
	var flash = new Object();
	flash.installed=false;
	flash.version='0.0';
	flash_versions=15;
	
	// Dig through Netscape-compatible plug-ins first.
	if (navigator.plugins && navigator.plugins.length) {
		for (x=0; x < navigator.plugins.length; x++) {
			if (navigator.plugins[x].name.indexOf('Shockwave Flash') != -1) {
				//alert(navigator.plugins[x].name)
				flash.version = navigator.plugins[x].description.split('Shockwave Flash ')[1];
				flash.installed = true;
				break;
			}
		}
	}
	// Then, dig through ActiveX-style plug-ins afterwords
	else if (window.ActiveXObject) {
		for (x = 2; x <= flash_versions; x++) {
			try {
				oFlash = eval("new ActiveXObject('ShockwaveFlash.ShockwaveFlash." + x + "');");
				if(oFlash) {
					flash.installed = true;
					flash.version = x + '.0';
				}
			}
			catch(e) { }
		}
	}
	var endTimer= new Date();
	//alert(endTimer.getTime()-startTimer.getTime());
	return flash.installed;
}

function Framaplayer(fpa) {

  if (typeof(fpa)!="object") { fpa = new Array(); }	
  fpa["id"] = (fpa["id"]!=null) ? fpa["id"] : "mp3player"; //default id
  fpa["width"] = (fpa["width"]!=null) ? fpa["width"] : "150"; //default width
  fpa["height"] = (fpa["height"]!=null) ? fpa["height"] : "75"; //default height
  fpa["style"] = (fpa["style"]!=null) ? fpa["style"] : "vertical-align: bottom;"; //default style
  fpa["src"] = (fpa["src"]!=null) ? fpa["src"] : "framaplayer/framaplayer.swf"; //path ot .swf (absolute or relative
  fpa["noflash"] = (fpa["noflash"]!=null) ? fpa["noflash"] : "[ <a href=\"http://www.macromedia.com/go/getflashplayer/\">Flash Player</a> requis ]"; //default html if flash is absent
  fpa["params"] = (fpa["params"]!=null) ? fpa["params"] : ""; //optional parameters (<param (...) />)

  var FlashVariables = "";
  if (fpa["FlashVars"]!=null) {
	  if (typeof(fpa["FlashVars"])!="object") { 
		FlashVariables = fpa["FlashVars"];
	  }	else {
		for(var i=0; i<fpa["FlashVars"].length; i++) {
			FlashVariables += fpa["FlashVars"][i];
			FlashVariables += "&amp;"
	    }
      } 
  }
//alert (FlashVariables);

	if ((browser.isMac==false) && (browser.isOpera==false)) {
 		document.write('<object id="'+ fpa["id"] +'" type="application/x-shockwave-flash" data="'+ fpa["src"] +'" width="'+ fpa["width"] +'" height="'+ fpa["height"] +'" style="'+ fpa["style"] +'">');
		document.write('  <!-- Framaplayer : Framasoft.net MP3 Flash player. Credits, license, contact & examples: http://framaplayer.keonox.com/ -->');
		document.write('  <param name="type" value="application/x-shockwave-flash" />');
		document.write('  <param name="codebase" value="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" />');
		document.write('  <param name="movie" value="'+ fpa["src"] +'" />');
		document.write('  <param name="FlashVars" value="'+ FlashVariables +'" />');
		document.write(' '+ fpa["noflash"] +'');
		document.write(' '+ fpa["params"] +''); 
		document.write('  </object>');
	} else {
	  if (FlashDetection()) {
		//alert('code alternatif')
		document.write('<object name="'+ fpa["id"] +'" id="'+ fpa["id"] +'" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,79,0" width="'+ fpa["width"] +'" height="'+ fpa["height"] +'">')
		document.write('	<!-- MP3 Flash player. Credits, license, contact & examples: http://pyg.keonox.com/flashmp3player/  -->')
		document.write('	<param name="movie" value="'+ fpa["src"] +'">')
		document.write('    <param name="FlashVars" value="'+ FlashVariables +'" />');
		document.write('	<embed swLiveConnect="true" FlashVars="movieid='+ fpa["id"] +'&amp;'+ FlashVariables +'" name="'+ fpa["id"] +'" src="'+ fpa["src"] +'" width="'+ fpa["width"] +'" height="'+ fpa["height"] +'" type="application/x-shockwave-flash" pluginspace="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash"></embed>')
		document.write('</object>');
	  } else {
  		document.write(' '+ fpa["noflash"] +' ');
	  }
	}
}

/* -----------------------------------------------------------
function setFlashVariables(movieid, flashquery)
http://www.mustardlab.com/developer/flash/jscommunication
movieid: id of object tag, name of movieid passed in through FlashVars
flashquery: querystring of values to set. example( var1=foo&var2=bar )
----------------------------------------------------------- */
function setFlashVariables(movieid, flashquery){
	var i,values;
	if ((browser.isMac==false) && (browser.isOpera==false)) {
		var chunk = flashquery.split("&");
		for(i in chunk){
			values = chunk[i].split("=");
			window.document[movieid].SetVariable(values[0],values[1]);
			alert(''+values[0]+'='+values[1]+'')
		}
	}else{
		var divcontainer = "flash_setvariables_"+movieid;
		if(!document.getElementById(divcontainer)){
			var divholder = document.createElement("div");
			divholder.id = divcontainer;
			document.body.appendChild(divholder);
		}
		document.getElementById(divcontainer).innerHTML = "";
		var divinfo = "<embed src='"+ g_framaplayerUrl +"gateway.swf' FlashVars='lc="+movieid+"&fq="+escape(flashquery)+"' width='0' height='0' type='application/x-shockwave-flash'></embed>";
		document.getElementById(divcontainer).innerHTML = divinfo;
		//alert(divinfo)
	}
}