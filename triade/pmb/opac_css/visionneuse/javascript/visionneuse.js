// +-------------------------------------------------+
// � 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: visionneuse.js,v 1.23.2.1 2024/12/26 09:55:12 jparis Exp $

function open_visionneuse(callbackFunction,explnum_id,position){
	callback = function() {
		return callbackFunction(explnum_id, position);
	}
	var visionneuse = document.createElement('div');
	visionneuse.setAttribute('id','visionneuse');
	document.getElementsByTagName('body')[0].appendChild(visionneuse);
	visionneuse.setAttribute('style','position:absolute;left:0;z-index:9000; background: rgba(0,0,0, 0.4);');
	// NON RGAA - visionneuse.setAttribute('onclick','close_visionneuse();');
	visionneuse.setAttribute('role','dialog');
	visionneuse.setAttribute('aria-modal','true');
	visionneuse.setAttribute('aria-label', pmbDojo.messages.getMessage('visionneuse', 'visionneuse_title'));
	visionneuse.style.top=getWindowScrollY();
	visionneuse.style.width="100%";
	visionneuse.style.height="100%";
	
	var background = document.createElement('div');
	background.setAttribute('id','visionneuseBackground');
	visionneuse.appendChild(background);
	
	var iframe = document.createElement('iframe');
	iframe.setAttribute('style','width: 96%; height: 96%; overflow:hidden;background-color:white;position:absolute;z-index:9002;left:2%;top:2%;background-image:url("images/ajax-loader.gif");background-repeat:no-repeat;background-position:center center;');		
	iframe.setAttribute('name','visionneuse');
	iframe.setAttribute('id','visionneuseIframe');
	iframe.setAttribute('title', pmbDojo.messages.getMessage('visionneuse', 'visionneuse_title'));
	iframe.setAttribute('src','');
	iframe.addEventListener('load', function(e){
		if(this.contentWindow.location.href != 'about:blank'){
			this.setAttribute('style','width: 96%; height: 96%; overflow:hidden;background-color:white;position:absolute;z-index:9002;left:2%;top:2%;background-image:none;background-repeat:no-repeat;background-position:center center;');	
		}
	});
	visionneuse.appendChild(iframe);
	
	visionneuse.parentNode.style.overflow = "hidden";
	window.onresize();
	
	callback();

}

function open_alertbox(opac_visionneuse_alert) {
	var alertbox = document.createElement('div');
	alertbox.setAttribute('id','alertbox');
	alertbox.setAttribute('style','background-color:white;position:absolute;');
	alertbox.setAttribute('onclick','window.parent.close_alertbox();');
	alertbox.style.width="100%";
	alertbox.style.height="100%";
	alertbox.innerHTML=opac_visionneuse_alert;
	try {
		document.getElementById('visionneuseIframe').contentDocument.getElementsByTagName('body')[0].appendChild(alertbox);
	}catch(err){
		try {
			document.getElementById('visionneuseIframe').contentWindow.getElementsByTagName('body')[0].appendChild(alertbox);
		}catch(err){}
	}
}

function close_alertbox() {
	try {
		var ab= document.getElementById('visionneuseIframe').contentDocument.getElementById('alertbox');
		ab.parentNode.removeChild(ab);
	}catch(err){
		try {
			var ab= document.getElementById('visionneuseIframe').contentWindow.getElementById('alertbox');
			ab.parentNode.removeChild(ab);
		}catch(err){}
	}	 	
}

window.onresize = function(){
	var visionneuse = document.getElementById('visionneuse');
	if (visionneuse){
		visionneuse.style.width=getWindowWidth()+'px';
		visionneuse.style.height=getWindowHeight()+'px';
		visionneuse.style.top=getWindowScrollY()+'px';
	}	
}

function close_visionneuse(){
	var visionneuse = document.getElementById('visionneuse');
	visionneuse.parentNode.style.overflow = '';	
	visionneuse.parentNode.removeChild(visionneuse);
	if(document.form_values){
		document.form_values.target='';
		document.form_values.action=oldAction;
		var mode=document.getElementsByName('mode').item(0);
		if(mode && mode.value == 'extended'){
			mode.parentNode.removeChild(mode);
			var explnum_id=document.getElementsByName('explnum_id').item(0);
			if(explnum_id) explnum_id.parentNode.removeChild(explnum_id);
			var val_search=document.getElementById('search');
			if(val_search) val_search.parentNode.removeChild(val_search);
		}
	}
}

function getWindowHeight(){
	if(window.innerHeight) 
		return window.innerHeight+"px";
	else return document.documentElement.clientHeight;
}

function getWindowWidth(){
	if(window.innerWidth) 
		return window.innerWidth+"px";
	else return document.documentElement.clientWidth;
}

function getWindowScrollY(){
	if(window.scrollY)
		return window.scrollY+"px";
	else return document.documentElement.scrollTop;
}