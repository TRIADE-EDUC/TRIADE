// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: analytics_services.js,v 1.1.4.3 2025/05/06 14:39:46 dgoron Exp $

const ANALYTICS_DEBUG = false;

function analyticsSerciceIsSemanticNode(node) {
    const semanticTags = [
        'HEADER', 'NAV', 'MAIN', 'SECTION', 'ARTICLE',
        'ASIDE', 'FOOTER', 'FIGURE', 'FIGCAPTION',
        'MARK', 'TIME', 'ADDRESS', 'DETAILS', 'SUMMARY'
    ];
    return semanticTags.includes(node.tagName);
}

function analyticsSerciceGetSemanticTag(node) {
	let semanticTag = 'Body';
    while (node) {
        // Vérifie si l'élément est une balise sémantique
        if (analyticsSerciceIsSemanticNode(node)) {
        	let tagName = node.tagName;
        	tagName = String(tagName).charAt(0).toUpperCase() + String(tagName).slice(1).toLowerCase();
        	semanticTag = node.getAttribute('aria-label') || node.getAttribute('title') || tagName;
        	break;
        }
        node = node.parentElement;
    }
    return semanticTag;
}

function analyticsSerciceGenerateTag(node, eventType) {
	let semanticTag = analyticsSerciceGetSemanticTag(node);
    let value = node.innerText || node.getAttribute('aria-label') || node.getAttribute('title') || 'element';
    value = value.trim();
    let nature = '';
    switch(eventType) {
		case 'click':
			if (node.nodeName == 'A') {
				nature = 'Clic sur un lien';
			} else if (node.nodeName == 'BUTTON') {
				nature = 'Clic sur un bouton';
			}
			break;
    }
    return semanticTag + '||' + nature + '||' +value;
}

function analyticsSerciceEAPush(actionname, actionpname, actionpvalue, actionlabel) {
	const check = item => item !== undefined && item !== '';
    if (!check(actionname) || !check(actionname)) {
        console.warn('Analytics.push: invalid function call.', {actionname, actionpname, actionpvalue, actionlabel});
        return;
    }
    if (typeof EA_push !== 'function') {
        console.warn('Analytics.push: Eulerian API isn\'t yet avalaible.');
        return;
    }
    actionpvalue = actionpvalue === undefined ? '-' : String(actionpvalue).trim();
    let options = [
        'actionname', actionname,
        'actionpname', actionpname,
        'actionpvalue', actionpvalue
    ];
    if (actionlabel.length) {
    	for(var i = 0; i < actionlabel.length; i++){
    		options.push('actionlabel');
    		options.push(actionlabel[i]);
    	}
    }
    if (ANALYTICS_DEBUG) {
        console.info('Analytics.push: send event for "' + actionname + '".');
        console.table({actionname, actionpname, actionpvalue, actionlabel});
    }
    EA_push('action', options);
}

function analyticsSerciceAddTracking(node, callbackFunction, eventType) {
    if (!node.hasAttribute('data-analytics-tag')) {
        let dataAnalyticsTag = analyticsSerciceGenerateTag(node, eventType);
        node.setAttribute('data-analytics-tag', dataAnalyticsTag);
    }
    node.addEventListener('click', function () {
        const dataAnalyticsTag = node.getAttribute('data-analytics-tag');
        switch(callbackFunction) {
        	case 'EA_push':
        		if (typeof EA_push !== 'undefined' && dataAnalyticsTag) {
        			let [actionname, actionpname, actionpvalue] = dataAnalyticsTag.split('||');
        			let actionlabel = [];
        			if (node.nodeName == 'A') {
        				let href = node.href;
        				if ( href.includes('doc_num.php')) {
        					actionlabel.push('download');
        				} else {
        					actionlabel.push('link');
        				}
        			} else if(node.nodeName == 'BUTTON') {
        				actionlabel.push('button');
        			} else {
        				actionlabel.push('event');
        			}
        			actionlabel.push(actionname);
        			analyticsSerciceEAPush(actionname, actionpname, actionpvalue, actionlabel);
        		}
        		break;
        }
    });
}
  
function analyticsSerciceAddEvents(callbackFunction, query, eventType) {
	if (!query) {
		query = 'a, button, [role="button"], [onclick]';
	}
	if (!eventType) {
		eventType = 'click';
	}
	document.addEventListener('DOMContentLoaded', function () {
		const nodes = document.querySelectorAll(query);
		nodes.forEach(function(node) {
			analyticsSerciceAddTracking(node, callbackFunction, eventType);
		});
	});
}
