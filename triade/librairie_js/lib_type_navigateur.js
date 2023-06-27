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
