// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tinyMCE_interface.js,v 1.3 2016-09-19 14:14:42 jpermanne Exp $

function tinyMCE_getInstance(dom_id){
	var myInstance = null;
	switch(tinyMCE.majorVersion) {
	    case "4":
	    	myInstance = tinyMCE.get(dom_id);
	    	break;
	    default: //V2 et 3
	    	myInstance = tinyMCE.getInstanceById(dom_id);
	}
	return myInstance;
}

function tinyMCE_execCommand(c,u,v){
	switch(c) {
		case 'mceAddControl' :
			switch(tinyMCE.majorVersion) {
			    case "4":
			    	c='mceAddEditor';
			    	break;
			    default: //V2 et 3
			    	c='mceAddControl';
			}
			break;
		case 'mceRemoveControl' :
			switch(tinyMCE.majorVersion) {
			case "4":
				c='mceRemoveEditor';
				break;
			default: //V2 et 3
				c='mceRemoveControl';
			}
			break;
	}
	
	tinyMCE.execCommand(c,u,v);
}

function tinyMCE_updateContent(dom_id,content){
	
	switch(tinyMCE.majorVersion) {
		case "4":
			tinyMCE.get(dom_id).setContent(content);
			break;
		default :
			tinyMCE.updateContent(dom_id);
			break;	
	}
}