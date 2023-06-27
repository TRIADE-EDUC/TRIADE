// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: misc.js,v 1.11 2018-09-24 08:30:44 dgoron Exp $


function replace_texte(string,text,by) {
    var strLength = string.length, txtLength = text.length;
    if ((strLength == 0) || (txtLength == 0)) return string;

    var i = string.indexOf(text);
    if ((!i) && (text != string.substring(0,txtLength))) return string;
    if (i == -1) return string;

    var newstr = string.substring(0,i) + by;

    if (i+txtLength < strLength)
        newstr += replace_texte(string.substring(i+txtLength,strLength),text,by);

    return newstr;
}


function reverse_html_entities(text) {
    
    text = replace_texte(text,'&quot;',unescape('%22'));
    text = replace_texte(text,'&apos;',unescape('%27'));
	text = replace_texte(text,'&#039;',unescape('%27'));
    text = replace_texte(text,'&amp;',unescape('%26'));
    text = replace_texte(text,'&lt;',unescape('%3C'));
    text = replace_texte(text,'&gt;',unescape('%3E'));
    text = replace_texte(text,'&nbsp;',unescape('%A0'));
    text = replace_texte(text,'&iexcl;',unescape('%A1'));
    text = replace_texte(text,'&cent;',unescape('%A2'));
    text = replace_texte(text,'&pound;',unescape('%A3'));
    text = replace_texte(text,'&yen;',unescape('%A5'));
    text = replace_texte(text,'&brvbar;',unescape('%A6'));
    text = replace_texte(text,'&sect;',unescape('%A7'));
    text = replace_texte(text,'&uml;',unescape('%A8'));
    text = replace_texte(text,'&copy;',unescape('%A9'));
    text = replace_texte(text,'&ordf;',unescape('%AA'));
    text = replace_texte(text,'&laquo;',unescape('%AB'));
    text = replace_texte(text,'&not;',unescape('%AC'));
    text = replace_texte(text,'&shy;',unescape('%AD'));
    text = replace_texte(text,'&reg;',unescape('%AE'));
    text = replace_texte(text,'&macr;',unescape('%AF'));
    text = replace_texte(text,'&deg;',unescape('%B0'));
    text = replace_texte(text,'&plusmn;',unescape('%B1'));
    text = replace_texte(text,'&sup2;',unescape('%B2'));
    text = replace_texte(text,'&sup3;',unescape('%B3'));
    text = replace_texte(text,'&acute;',unescape('%B4'));
    text = replace_texte(text,'&micro;',unescape('%B5'));
    text = replace_texte(text,'&para;',unescape('%B6'));
    text = replace_texte(text,'&middot;',unescape('%B7'));
    text = replace_texte(text,'&cedil;',unescape('%B8'));
    text = replace_texte(text,'&sup1;',unescape('%B9'));
    text = replace_texte(text,'&ordm;',unescape('%BA'));
    text = replace_texte(text,'&raquo;',unescape('%BB'));
    text = replace_texte(text,'&frac14;',unescape('%BC'));
    text = replace_texte(text,'&frac12;',unescape('%BD'));
    text = replace_texte(text,'&frac34;',unescape('%BE'));
    text = replace_texte(text,'&iquest;',unescape('%BF'));
    text = replace_texte(text,'&Agrave;',unescape('%C0'));
    text = replace_texte(text,'&Aacute;',unescape('%C1'));
    text = replace_texte(text,'&Acirc;',unescape('%C2'));
    text = replace_texte(text,'&Atilde;',unescape('%C3'));
    text = replace_texte(text,'&Auml;',unescape('%C4'));
    text = replace_texte(text,'&Aring;',unescape('%C5'));
    text = replace_texte(text,'&AElig;',unescape('%C6'));
    text = replace_texte(text,'&Ccedil;',unescape('%C7'));
    text = replace_texte(text,'&Egrave;',unescape('%C8'));
    text = replace_texte(text,'&Eacute;',unescape('%C9'));
    text = replace_texte(text,'&Ecirc;',unescape('%CA'));
    text = replace_texte(text,'&Euml;',unescape('%CB'));
    text = replace_texte(text,'&Igrave;',unescape('%CC'));
    text = replace_texte(text,'&Iacute;',unescape('%CD'));
    text = replace_texte(text,'&Icirc;',unescape('%CE'));
    text = replace_texte(text,'&Iuml;',unescape('%CF'));
    text = replace_texte(text,'&ETH;',unescape('%D0'));
    text = replace_texte(text,'&Ntilde;',unescape('%D1'));
    text = replace_texte(text,'&Ograve;',unescape('%D2'));
    text = replace_texte(text,'&Oacute;',unescape('%D3'));
    text = replace_texte(text,'&Ocirc;',unescape('%D4'));
    text = replace_texte(text,'&Otilde;',unescape('%D5'));
    text = replace_texte(text,'&Ouml;',unescape('%D6'));
    text = replace_texte(text,'&times;',unescape('%D7'));
    text = replace_texte(text,'&Oslash;',unescape('%D8'));
    text = replace_texte(text,'&Ugrave;',unescape('%D9'));
    text = replace_texte(text,'&Uacute;',unescape('%DA'));
    text = replace_texte(text,'&Ucirc;',unescape('%DB'));
    text = replace_texte(text,'&Uuml;',unescape('%DC'));
    text = replace_texte(text,'&Yacute;',unescape('%DD'));
    text = replace_texte(text,'&THORN;',unescape('%DE'));
    text = replace_texte(text,'&szlig;',unescape('%DF'));
    text = replace_texte(text,'&agrave;',unescape('%E0'));
    text = replace_texte(text,'&aacute;',unescape('%E1'));
    text = replace_texte(text,'&acirc;',unescape('%E2'));
    text = replace_texte(text,'&atilde;',unescape('%E3'));
    text = replace_texte(text,'&auml;',unescape('%E4'));
    text = replace_texte(text,'&aring;',unescape('%E5'));
    text = replace_texte(text,'&aelig;',unescape('%E6'));
    text = replace_texte(text,'&ccedil;',unescape('%E7'));
    text = replace_texte(text,'&egrave;',unescape('%E8'));
    text = replace_texte(text,'&eacute;',unescape('%E9'));
    text = replace_texte(text,'&ecirc;',unescape('%EA'));
    text = replace_texte(text,'&euml;',unescape('%EB'));
    text = replace_texte(text,'&igrave;',unescape('%EC'));
    text = replace_texte(text,'&iacute;',unescape('%ED'));
    text = replace_texte(text,'&icirc;',unescape('%EE'));
    text = replace_texte(text,'&iuml;',unescape('%EF'));
    text = replace_texte(text,'&eth;',unescape('%F0'));
    text = replace_texte(text,'&ntilde;',unescape('%F1'));
    text = replace_texte(text,'&ograve;',unescape('%F2'));
    text = replace_texte(text,'&oacute;',unescape('%F3'));
    text = replace_texte(text,'&ocirc;',unescape('%F4'));
    text = replace_texte(text,'&otilde;',unescape('%F5'));
    text = replace_texte(text,'&ouml;',unescape('%F6'));
    text = replace_texte(text,'&divide;',unescape('%F7'));
    text = replace_texte(text,'&oslash;',unescape('%F8'));
    text = replace_texte(text,'&ugrave;',unescape('%F9'));
    text = replace_texte(text,'&uacute;',unescape('%FA'));
    text = replace_texte(text,'&ucirc;',unescape('%FB'));
    text = replace_texte(text,'&uuml;',unescape('%FC'));
    text = replace_texte(text,'&yacute;',unescape('%FD'));
    text = replace_texte(text,'&thorn;',unescape('%FE'));
    text = replace_texte(text,'&yuml;',unescape('%FF'));
    return text;

}

function html_entities(text) {
    
    text = replace_texte(text,unescape('%22'),'&quot;');
    text = replace_texte(text,unescape('%26'),'&amp;');
    text = replace_texte(text,unescape('%3C'),'&lt;');
    text = replace_texte(text,unescape('%3E'),'&gt;');
    text = replace_texte(text,unescape('%A0'),'&nbsp;');
    text = replace_texte(text,unescape('%A1'),'&iexcl;');
    text = replace_texte(text,unescape('%A2'),'&cent;');
    text = replace_texte(text,unescape('%A3'),'&pound;');
    text = replace_texte(text,unescape('%A5'),'&yen;');
    text = replace_texte(text,unescape('%A6'),'&brvbar;');
    text = replace_texte(text,unescape('%A7'),'&sect;');
    text = replace_texte(text,unescape('%A8'),'&uml;');
    text = replace_texte(text,unescape('%A9'),'&copy;');
    text = replace_texte(text,unescape('%AA'),'&ordf;');
    text = replace_texte(text,unescape('%AB'),'&laquo;');
    text = replace_texte(text,unescape('%AC'),'&not;');
    text = replace_texte(text,unescape('%AD'),'&shy;');
    text = replace_texte(text,unescape('%AE'),'&reg;');
    text = replace_texte(text,unescape('%AF'),'&macr;');
    text = replace_texte(text,unescape('%B0'),'&deg;');
    text = replace_texte(text,unescape('%B1'),'&plusmn;');
    text = replace_texte(text,unescape('%B2'),'&sup2;');
    text = replace_texte(text,unescape('%B3'),'&sup3;');
    text = replace_texte(text,unescape('%B4'),'&acute;');
    text = replace_texte(text,unescape('%B5'),'&micro;');
    text = replace_texte(text,unescape('%B6'),'&para;');
    text = replace_texte(text,unescape('%B7'),'&middot;');
    text = replace_texte(text,unescape('%B8'),'&cedil;');
    text = replace_texte(text,unescape('%B9'),'&sup1;');
    text = replace_texte(text,unescape('%BA'),'&ordm;');
    text = replace_texte(text,unescape('%BB'),'&raquo;');
    text = replace_texte(text,unescape('%BC'),'&frac14;');
    text = replace_texte(text,unescape('%BD'),'&frac12;');
    text = replace_texte(text,unescape('%BE'),'&frac34;');
    text = replace_texte(text,unescape('%BF'),'&iquest;');
    text = replace_texte(text,unescape('%C0'),'&Agrave;');
    text = replace_texte(text,unescape('%C1'),'&Aacute;');
    text = replace_texte(text,unescape('%C2'),'&Acirc;');
    text = replace_texte(text,unescape('%C3'),'&Atilde;');
    text = replace_texte(text,unescape('%C4'),'&Auml;');
    text = replace_texte(text,unescape('%C5'),'&Aring;');
    text = replace_texte(text,unescape('%C6'),'&AElig;');
    text = replace_texte(text,unescape('%C7'),'&Ccedil;');
    text = replace_texte(text,unescape('%C8'),'&Egrave;');
    text = replace_texte(text,unescape('%C9'),'&Eacute;');
    text = replace_texte(text,unescape('%CA'),'&Ecirc;');
    text = replace_texte(text,unescape('%CB'),'&Euml;');
    text = replace_texte(text,unescape('%CC'),'&Igrave;');
    text = replace_texte(text,unescape('%CD'),'&Iacute;');
    text = replace_texte(text,unescape('%CE'),'&Icirc;');
    text = replace_texte(text,unescape('%CF'),'&Iuml;');
    text = replace_texte(text,unescape('%D0'),'&ETH;');
    text = replace_texte(text,unescape('%D1'),'&Ntilde;');
    text = replace_texte(text,unescape('%D2'),'&Ograve;');
    text = replace_texte(text,unescape('%D3'),'&Oacute;');
    text = replace_texte(text,unescape('%D4'),'&Ocirc;');
    text = replace_texte(text,unescape('%D5'),'&Otilde;');
    text = replace_texte(text,unescape('%D6'),'&Ouml;');
    text = replace_texte(text,unescape('%D7'),'&times;');
    text = replace_texte(text,unescape('%D8'),'&Oslash;');
    text = replace_texte(text,unescape('%D9'),'&Ugrave;');
    text = replace_texte(text,unescape('%DA'),'&Uacute;');
    text = replace_texte(text,unescape('%DB'),'&Ucirc;');
    text = replace_texte(text,unescape('%DC'),'&Uuml;');
    text = replace_texte(text,unescape('%DD'),'&Yacute;');
    text = replace_texte(text,unescape('%DE'),'&THORN;');
    text = replace_texte(text,unescape('%DF'),'&szlig;');
    text = replace_texte(text,unescape('%E0'),'&agrave;');
    text = replace_texte(text,unescape('%E1'),'&aacute;');
    text = replace_texte(text,unescape('%E2'),'&acirc;');
    text = replace_texte(text,unescape('%E3'),'&atilde;');
    text = replace_texte(text,unescape('%E4'),'&auml;');
    text = replace_texte(text,unescape('%E5'),'&aring;');
    text = replace_texte(text,unescape('%E6'),'&aelig;');
    text = replace_texte(text,unescape('%E7'),'&ccedil;');
    text = replace_texte(text,unescape('%E8'),'&egrave;');
    text = replace_texte(text,unescape('%E9'),'&eacute;');
    text = replace_texte(text,unescape('%EA'),'&ecirc;');
    text = replace_texte(text,unescape('%EB'),'&euml;');
    text = replace_texte(text,unescape('%EC'),'&igrave;');
    text = replace_texte(text,unescape('%ED'),'&iacute;');
    text = replace_texte(text,unescape('%EE'),'&icirc;');
    text = replace_texte(text,unescape('%EF'),'&iuml;');
    text = replace_texte(text,unescape('%F0'),'&eth;');
    text = replace_texte(text,unescape('%F1'),'&ntilde;');
    text = replace_texte(text,unescape('%F2'),'&ograve;');
    text = replace_texte(text,unescape('%F3'),'&oacute;');
    text = replace_texte(text,unescape('%F4'),'&ocirc;');
    text = replace_texte(text,unescape('%F5'),'&otilde;');
    text = replace_texte(text,unescape('%F6'),'&ouml;');
    text = replace_texte(text,unescape('%F7'),'&divide;');
    text = replace_texte(text,unescape('%F8'),'&oslash;');
    text = replace_texte(text,unescape('%F9'),'&ugrave;');
    text = replace_texte(text,unescape('%FA'),'&uacute;');
    text = replace_texte(text,unescape('%FB'),'&ucirc;');
    text = replace_texte(text,unescape('%FC'),'&uuml;');
    text = replace_texte(text,unescape('%FD'),'&yacute;');
    text = replace_texte(text,unescape('%FE'),'&thorn;');
    text = replace_texte(text,unescape('%FF'),'&yuml;');
    return text;

}

function get_ref(obj) {
	if (typeof obj == "string") {
		obj = document.getElementById(obj);
	}
	return obj;
}

function set_value_style(obj, style, value) {
	get_ref(obj).style[style] = value;
	
	var url = "./ajax.php?module=ajax&categ=misc&fname=session";
	var req = new http_request();
	var params = "key="+obj+"_"+style;
	if (value != "") {
		params += "&value="+value;
	}
	req.request(url, true, params);
}

function get_value_style(obj, style) {
	if (!document.getElementById)
		return;

	var obj = get_ref(obj);
	var value = obj.style[style];
	if (!value) {
		if (document.defaultView) {
			value = document.defaultView.getComputedStyle(obj, "").getPropertyValue(style);
		} else if (obj.currentStyle) {
			value = obj.currentStyle[style]
		}
	}
	return value;
}

function set_font_size(i) {
	var str = get_value_style('pmbopac','font-size');
	var unit = str.substring(str.length-2);
	var value = str.substring(0, str.length-2);
	switch (i) {
		case -1: //Reduce
			set_value_style('pmbopac', 'fontSize', (value*0.9)+unit);
			break;
		case 0: //Reset
			set_value_style('pmbopac', 'fontSize', '');
			break;
		case 1: //Large
			set_value_style('pmbopac', 'fontSize', (value*1.1)+unit);
			break;
	}
	if(document.getElementById('iframe_resume_panier')) {
		set_iframe_font_size('iframe_resume_panier', 'cart_info_body');
	}
}

function set_iframe_font_size(frameNodeId, bodyNodeId) {
	var iframe = document.getElementById(frameNodeId);
	var innerDoc = (iframe.contentDocument) ? iframe.contentDocument : iframe.contentWindow.document;
	var cartBodyNode = innerDoc.getElementById(bodyNodeId);
	get_ref(cartBodyNode).style['fontSize'] = get_value_style('pmbopac','font-size');
}


function empty_dojo_calendar_by_id(id){
	require(["dijit/registry"], function(registry) {registry.byId(id).set('value',null);});
}

function closeCurrentEnv(){
	window.close();
}

function set_parent_value(f_caller, id, value){
	window.opener.document.forms[f_caller].elements[id].value = value;
}

function get_parent_value(f_caller, id){
	return window.opener.document.forms[f_caller].elements[id].value;
}

function set_parent_focus(f_caller, id){
	window.opener.document.forms[f_caller].elements[id].focus();
}

/* --------------------------------------------------------------------------------------
 * Fonction addLoadEvent
 * Empile les differentes fonctions a appeler quand la page est chargee
 */
function addLoadEvent(func) {
  if (window.addEventListener)
    window.addEventListener("load", func, false);
  else if (window.attachEvent)
    window.attachEvent("onload", func);
  else { // fallback
    var old = window.onload;
    window.onload = function() {
      if (old) old();
      func();
    };
  }
}
