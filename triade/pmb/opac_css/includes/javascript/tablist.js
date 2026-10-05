//+-------------------------------------------------+
//© 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
//+-------------------------------------------------+
//$Id: tablist.js,v 1.31 2023/09/01 15:25:35 tsamson Exp $

//gestion des listes "collapsibles" en Javascript

/*var imgOpened = new Image();
imgOpened.src = './getgif.php?nomgif=moins';
var imgClosed = new Image();
imgClosed.src = './getgif.php?nomgif=plus';*/
var expandedDb = '';

var expandNotice;
var expandAllNotices;
records_read = new Array();

try {
    expandNotice = new Event('expandnotice');
    expandAllNotices = new Event('expandallnotices');
}
catch (err) {
    if (document.createEvent) {
        expandNotice = document.createEvent('HTMLEvents');
        expandNotice.initEvent('expandnotice', true, true);
        expandAllNotices = document.createEvent('HTMLEvents');
        expandAllNotices.initEvent('expandallnotices', true, true);
    } else if (document.createEventObject) {// IE < 9
        expandNotice = document.createEventObject();
        expandNotice.eventType = 'expandnotice';
        expandAllNotices = document.createEventObject();
        expandAllNotices.eventType = 'expandallnotices';
    }
}

/*
 * abacarisse
 * On declare en global la variable des param addThis
 */
var waitingAddthis = new Array();
if (!param_social_network) {
    var param_social_network;
    /*
     * On recup les param
     */
    var opac_show_social_network = (typeof (opac_show_social_network) != 'undefined') ? opac_show_social_network : 0;
    if (opac_show_social_network == 1) {
        var req = new http_request();
        req.request("./ajax.php?module=ajax&categ=param_social_network&sub=get", false, null, true, function(data) {
            param_social_network = JSON.parse(data);
            for (var i = 0; i < waitingAddthis.length; i++) {
                creeAddthis(waitingAddthis[i]);
            }
            waitingAddthis = new Array();
        });
    }
}

function waitingAddthisLoaded(elem) {
    waitingAddthis.push(elem);
}
//on regarde si le client est DOM-compliant

var isDOM = (typeof (document.getElementsByTagName) != 'undefined') ? 1 : 0;

//Konqueror (support DOM partiel) : on rejette
if (isDOM && typeof (navigator.userAgent) != 'undefined') {
    var browserName = ' ' + navigator.userAgent.toLowerCase();
    if (browserName.indexOf('konqueror') > 0) {
        isDOM = 0;
    }
}

function changeCoverImage(elt) {
    imgs = elt.getElementsByTagName('img');
    for (i = 0; i < imgs.length; i++) {
        img = imgs[i];
        isbn = img.getAttribute('isbn');
        vigurl = img.getAttribute('vigurl');
        url_image = img.getAttribute('url_image');
        if (vigurl) {
            if (img.src.substring(img.src.length - 8, img.src.length) == 'vide.png') {
                img.src = vigurl;
            }
        } else if (isbn) {
            if (img.src.substring(img.src.length - 8, img.src.length) == 'vide.png') {
                img.src = url_image.replace(/!!noticecode!!/, isbn);
            }
        }
    }
}

function storageBase(e) {
    var found = false;
    for (var i = 0; i < records_read.length; i++) {
        if (records_read[i] == e.id) {
            found = true;
            break;
        }
    }
    if (!found) {
        var url = './ajax.php?module=ajax&categ=storage&sub=save';
        url += '&id=' + e.id + '&token=' + e.token + '&datetime=' + e.datetime;

        var req = new http_request();
        if (!req.request(url)) {
            //on marque l'�l�ment comme lu
            records_read.push(e.id);
        }
    }
}

function storageAll(e) {
    var url = './ajax.php?module=ajax&categ=storage&sub=save_all';

    var req = new http_request();
    if (!req.request(url, 1, '&records=' + JSON.stringify(e.records))) {
        //on marque les �l�ments comme lus
        for (var i = 0; i < e.records.length; i++) {
            records_read.push(e.records[i].id);
        }
    }
}

function initIt() {
    if (!isDOM) {
        //		alert("ce navigateur n'est pas compatible avec le DOM.");
        return;
    }
    if (document.body.addEventListener) {
        document.body.addEventListener("expandnotice", function(e) { storageBase(e); }, false);
        document.body.addEventListener("expandallnotices", function(e) { storageAll(e); }, false);
    } else if (document.body.attachEvent) {
        document.body.attachEvent("expandnotice", function() { storageBase(window.event); });
        document.body.attachEvent("expandallnotices", function() { storageAll(window.event); });
    }
    var tempColl = document.getElementsByTagName('DIV');
    var tempCollCnt = tempColl.length;
    for (var i = 0; i < tempCollCnt; i++) {
        if (tempColl[i].className == 'notice-child') {
            if (tempColl[i].getAttribute('startOpen') == 'Yes') {
				if (tempColl[i].hasAttribute('data-expand-ajax')) {
					let elId = tempColl[i].id.substring(0,tempColl[i].id.indexOf('Child'));
					let expandImg = document.getElementById(elId + 'Img');
					expandBase_ajax(elId,true,expandImg.getAttribute('param'));
				} else {
                	expandBase(tempColl[i].id.substring(0, tempColl[i].id.indexOf('Child')), true);
				}
            } else tempColl[i].style.display = 'none';
        }
    }
} // end of the 'initIt()' function

function creeAddthis(el) {
    try {
        if (opac_show_social_network == 1) {
            var addthisEl = document.getElementById(el + 'addthis');
            for (key in param_social_network.toolBoxParams) {
                if (addthisEl.getAttribute(key) != param_social_network.toolBoxParams[key]) {
                    addthisEl.setAttribute(key, param_social_network.toolBoxParams[key]);
                }
            }

            for (buttonKey in param_social_network.buttons) {
                var button;
                button = addthisEl.appendChild(document.createElement("a"));
                if (param_social_network.buttons[buttonKey].text) {
                    button.innerHTML = param_social_network.buttons[buttonKey].text;
                }
                for (attrKey in param_social_network.buttons[buttonKey].attributes) {
                    button.setAttribute(attrKey, param_social_network.buttons[buttonKey].attributes[attrKey]);
                }
                for (elemKey in param_social_network.buttons[buttonKey].elements) {
                    var element;
                    element = button.appendChild(document.createElement(param_social_network.buttons[buttonKey].elements[elemKey].tag));
                    if (param_social_network.buttons[buttonKey].elements[elemKey].text) {
                        element.innerHtml = param_social_network.buttons[buttonKey].elements[elemKey].text;
                    }
                    for (elemAttrKey in param_social_network.buttons[buttonKey].elements[elemKey].attributes) {
                        element.setAttribute(elemAttrKey, param_social_network.buttons[buttonKey].elements[elemKey].attributes[elemAttrKey]);
                    }
                }
            }

            addthis.toolbox(addthisEl);
            //on marque l'�l�ment
            addthisEl.setAttribute("added", "1");
        }
    } catch (e) {
        if (typeof console != 'undefined') {
            console.log(e)
        }
    }
}

/*
 * Fonction de cr�ation des boutons addThis
 */
//function create_button(el){
//	var addthisEl = document.getElementById(el + 'addthis');
//	
//	for(key in param_social_network.toolBoxParams){
//		if(addthisEl.getAttribute(key) != param_social_network.toolBoxParams[key]){
//			addthisEl.setAttribute(key,param_social_network.toolBoxParams[key]);
//		}
//	}
//	
//	for(buttonKey in param_social_network.buttons){
//		var button;
//		button = addthisEl.appendChild(document.createElement("a"));
//		if(param_social_network.buttons[buttonKey].text){
//			button.innerHTML=param_social_network.buttons[buttonKey].text;
//		}
//		for(attrKey in param_social_network.buttons[buttonKey].attributes){
//			button.setAttribute(attrKey,param_social_network.buttons[buttonKey].attributes[attrKey]);
//		}
//		for(elemKey in param_social_network.buttons[buttonKey].elements){
//			var element;
//			element = button.appendChild(document.createElement(param_social_network.buttons[buttonKey].elements[elemKey].tag));
//			if(param_social_network.buttons[buttonKey].elements[elemKey].text){
//				element.innerHtml=param_social_network.buttons[buttonKey].elements[elemKey].text;
//			}
//			for(elemAttrKey in param_social_network.buttons[buttonKey].elements[elemKey].attributes){
//				element.setAttribute(elemAttrKey,param_social_network.buttons[buttonKey].elements[elemKey].attributes[elemAttrKey]);
//			}
//		}
//	}	
//	
//	addthis.toolbox(addthisEl);
//	//on marque l'�l�ment
//	addthisEl.setAttribute("added","1");
//}

//function creeAddthis(el){
//	var addthisEl = document.getElementById(el + 'addthis');
//	
//	var aEl = addthisEl.appendChild(document.createElement("a"));
//	var aEl2 = addthisEl.appendChild(document.createElement("a"));
//	var aEl3 = addthisEl.appendChild(document.createElement("a"));
//
//	aEl.setAttribute("class","addthis_button_facebook_like");
//	aEl.setAttribute("fb:like:layout","button_count");	
//	aEl2.setAttribute("class","addthis_button_tweet");	
//	aEl3.setAttribute("class","addthis_counter addthis_button_compact");
//	
//	addthis.toolbox(addthisEl);
//
//	//on marque l'�l�ment
//	addthisEl.setAttribute("added","1");
//} // end of the 'creeAddthis()' function

function ReinitializeAddThis() {
    if (window.addthis) {
        window.addthis.ost = 0;
        window.addthis.ready();
    }
}

function expandBase(el, unexpand) {
    if (!isDOM) {
        return;
    }

    let whichEl = document.getElementById(el + 'Child');
    let whichIm = document.getElementById(el + 'Img');

    if (whichEl.style.display == 'none' && whichIm) {
        if (whichEl.getAttribute("enrichment")) {
            getEnrichment(el.replace("el", ""));
        }

        let whichAddthis = document.getElementById(el + 'addthis');
        if (whichAddthis && !whichAddthis.getAttribute("added")) {
            creeAddthis(el);
        }
        whichEl.style.display = 'block';

        if (whichIm.src.includes('plus.gif')) {
            whichIm.src = whichIm.src.replace('plus.gif', 'minus.gif');
        } else if (whichIm.src.includes('nomgif=plus')) {
            whichIm.src = whichIm.src.replace('nomgif=plus', 'nomgif=moins');
        }

        changeCoverImage(whichEl);
        if (whichEl.getAttribute("token") && whichEl.getAttribute("datetime")) {
            expandNotice.id = el.replace("el", "");
            expandNotice.token = whichEl.getAttribute("token");
            expandNotice.datetime = whichEl.getAttribute("datetime");
            document.body.dispatchEvent(expandNotice);
        }


        if (whichIm.parentNode.tagName == "BUTTON" && whichIm.parentNode.hasAttribute("aria-expanded")) {
            whichIm.parentNode.setAttribute("aria-expanded", 'true');
        }

    } else if (unexpand) {
        whichEl.style.display = 'none';
        if (whichIm.src.includes('minus.gif')) {
            whichIm.src = whichIm.src.replace('minus.gif', 'plus.gif');
        } else if (whichIm.src.includes('nomgif=moins')) {
            whichIm.src = whichIm.src.replace('nomgif=moins', 'nomgif=plus');
        }

        if (whichIm.parentNode.tagName == "BUTTON" && whichIm.parentNode.hasAttribute("aria-expanded")) {
            whichIm.parentNode.setAttribute("aria-expanded", 'false');
        }
    }
    ReinitializeAddThis();
}

function expandAll() {
    var tempColl = document.getElementsByTagName('DIV');
    var tempCollCnt = tempColl.length;
    var elements = new Array();

    for (var i = 0; i < tempCollCnt; i++) {
        if (tempColl[i].className == 'notice-child') {
            tempColl[i].style.display = 'block';
            var el = tempColl[i].getAttribute("id").replace("Child", "");
            if (tempColl[i].getAttribute("enrichment")) {
                getEnrichment(el.replace("el", ""));
            }
            var whichAddthis = document.getElementById(el + 'addthis');
            if (whichAddthis && !whichAddthis.getAttribute("added")) {
                creeAddthis(el);
            }
            if (tempColl[i].getAttribute("token") && tempColl[i].getAttribute("datetime")) {
                elements.push(
                    {
                        "id": el.replace("el", ""),
                        "token": tempColl[i].getAttribute("token"),
                        "datetime": tempColl[i].getAttribute("datetime")
                    });
            }
        }
        changeCoverImage(tempColl[i]);
    }
    tempColl = document.getElementsByTagName('IMG');
    tempCollCnt = tempColl.length;
    for (var i = 0; i < tempCollCnt; i++) {
        if (tempColl[i].name == 'imEx' && (tempColl[i].parentElement.className == 'notice-parent')) {
            tempColl[i].src = tempColl[i].src.replace("nomgif=plus", "nomgif=moins");
        }
    }
    expandAllNotices.records = elements;
    document.body.dispatchEvent(expandAllNotices);
    ReinitializeAddThis();
}

function collapseAll() {
    var tempColl = document.getElementsByTagName('DIV');
    var tempCollCnt = tempColl.length;
    for (var i = 0; i < tempCollCnt; i++) {
        if (tempColl[i].className == 'notice-child')
            tempColl[i].style.display = 'none';
    }
    tempColl = document.getElementsByTagName('IMG');
    tempCollCnt = tempColl.length;
    for (var i = 0; i < tempCollCnt; i++) {
        if (tempColl[i].name == 'imEx' && (tempColl[i].parentElement.className == 'notice-parent')) {
            tempColl[i].src = tempColl[i].src.replace("nomgif=moins", "nomgif=plus");
        }
    }
}

onload = initIt;