// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bbcode.js,v 1.2 2011/08/11 10:07:47 ngantier Exp $

function insert_text(field, open, close)	{
	msgfield=document.getElementById(field);
	// IE support
	if (document.selection && document.selection.createRange) {
		msgfield.focus();
		sel = document.selection.createRange();
		sel.text = open + sel.text + close;
		msgfield.focus();
	}
	// Moz support
	else if (msgfield.selectionStart || msgfield.selectionStart == '0')	{
		var startPos = msgfield.selectionStart;
		var endPos = msgfield.selectionEnd;
		msgfield.value = msgfield.value.substring(0, startPos) + open + msgfield.value.substring(startPos, endPos) + close + msgfield.value.substring(endPos, msgfield.value.length);
		msgfield.selectionStart = msgfield.selectionEnd = endPos + open.length + close.length;
		msgfield.focus();
	}
	// Fallback support for other browsers
	else {
		msgfield.value += open + close;
		msgfield.focus();
	}
	return;
}	