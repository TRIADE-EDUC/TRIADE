// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: accessibility.js,v 1.5 2023/06/20 12:43:30 rtigero Exp $

function getElementAccessibility(pageDocument) {
    let opacAccessibility = document.getElementById("opacAccessibility");
    
    if(opacAccessibility !== null) {
        opacAccessibility = parseInt(opacAccessibility.value);
        if(opacAccessibility == 2) {
            return document.documentElement || null;
        }
    }
    return pageDocument.body || null;
}

function accessibilitySetFontSize(fontSize = "") {
    const elementAccessibility = getElementAccessibility(document);
    if (!elementAccessibility) {
        return;
    }

    elementAccessibility.style['fontSize'] = fontSize;
    accessibilitySave(fontSize);
}

function accessibilitySetIframeFontSize(frameNodeId, fontSize = "") {
	const iframe = document.getElementById(frameNodeId);
	
	const iframeDocument = (iframe.contentDocument) ? iframe.contentDocument : iframe.contentWindow.document;
	const elementAccessibility = getElementAccessibility(iframeDocument);
	if (!elementAccessibility) {
        return;
    }
	
	elementAccessibility.style['fontSize'] = fontSize;
}

function accessibilitySave(value = "") {
    const url = "./ajax.php?module=ajax&categ=misc&fname=session";
    
	let post = new URLSearchParams();
	post.append("session_key", "accessibility");
	if (value != "") {
		post.append("session_value", value);
	}
    
	fetch(url, {
		method: "POST",
		cache: 'no-cache',
		body: post
	});
}

function accessibilityFontSize(action) {
    
    const DEFAULT = 0;
    const REDUCE = 1;
    const EXPAND = 2;
    
    if (![DEFAULT, REDUCE, EXPAND].includes(action)) {
        return;
    }
    
    const node = getElementAccessibility(document);
    if (!node) {
        return;
    }
    
    let unit = "px";
    let value = 0;
    
    const computedStyle = getComputedStyle(node);
    if (computedStyle['font-size']) {
        unit = computedStyle['font-size'].replace(/[-+0-9.]/g, '');

        value = computedStyle['font-size'].replace(/[^-+0-9.]/g, '');
        value = Number(value);
    }
    
    let fontSize;
    switch (action) {
		case REDUCE:
		    fontSize = (value * 0.9) + unit;
			break;
		case EXPAND:
		    fontSize = (value * 1.1) + unit;
			break;
		case DEFAULT:
    	default:
    	    fontSize = "";
			break;
    }

    accessibilitySetFontSize(fontSize);
	if (document.getElementById('iframe_resume_panier')) {
	    accessibilitySetIframeFontSize('iframe_resume_panier', fontSize);
	}
}