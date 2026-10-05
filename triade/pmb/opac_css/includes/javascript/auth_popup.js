// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: auth_popup.js,v 1.4.8.2 2025/03/06 14:22:04 dgoron Exp $

function auth_popup(url, mandatory=false, title=''){
	if(url==''){
		url = "./ajax.php?module=ajax&categ=auth&action=get_form";
	}
	var div = document.createElement('div');
	div.setAttribute('id','auth_popup');
	div.setAttribute("style","z-index:9001;position:absolute;background:white;top:30%;left:40%;");
	div.setAttribute('aria-labelledby','mypopupform-title');
	var iframe = document.createElement("iframe");
	iframe.setAttribute('src',url);
	iframe.setAttribute("id","frame_auth_popup");
	if(title) {
		iframe.setAttribute("title",title);
	}
	iframe.setAttribute('aria-modal','true');
	iframe.setAttribute('role','dialog');
	if(!mandatory) {
		var close = document.createElement('div');
		
		var closeButtonContainer = document.createElement('button');
		closeButtonContainer.setAttribute('type', 'button');
		closeButtonContainer.setAttribute('class', 'button-unstylized');
		closeButtonContainer.setAttribute('title', pmbDojo.messages.getMessage("opac", "rgaa_close_modal"));
		closeButtonContainer.setAttribute('aria-label', pmbDojo.messages.getMessage("opac", "rgaa_close_modal"));
		closeButtonContainer.setAttribute('style', 'width:20px;position:absolute;right:0px');
		
		var closeSpanContainer = document.createElement('span');
		closeSpanContainer.setAttribute('class', 'visually-hidden');
		closeSpanContainer.innerHTML = pmbDojo.messages.getMessage("opac", "rgaa_close_modal");
		closeButtonContainer.appendChild(closeSpanContainer);
		
		var closeButton = document.createElement('i');
		closeButton.setAttribute('id', 'popupCloseButton');
		closeButton.setAttribute('class', 'fa fa-times popupCloseButton');
		closeButton.setAttribute('aria-hidden', 'true');
		
		closeButtonContainer.appendChild(closeButton);
		
		closeButton.onclick = function (){
			var frame = window.parent.document.getElementById('auth_popup');
			if(!frame){
				frame = document.getElementById('auth_popup');
			}
			frame.parentNode.removeChild(frame);
		}
		div.appendChild(closeButtonContainer);
		
		document.body.onkeyup = function (e){
			if(e.keyCode == 27) {
				var frame = window.parent.document.getElementById('auth_popup');
				if(!frame){
					frame = document.getElementById('auth_popup');
				}
				frame.parentNode.removeChild(frame);
        	}
		}
		if (typeof focus_trap == 'function') {
			focus_trap(div);
		}
	}
	div.appendChild(iframe);
	var att = document.getElementById('att');
	if(att){
		att.appendChild(div);
	}else{
		document.body.appendChild(div);
	}
	
}