// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: select.js,v 1.14.2.3.2.1 2025/02/27 14:05:54 dgoron Exp $

function insertatcursor(myField, myValue) {
	if (document.selection) {
		myField.focus();
		sel = document.selection.createRange();
		sel.text = myValue;
	} else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		myField.value = myField.value.substring(0, startPos)+ myValue+ myField.value.substring(endPos, myField.value.length);
	} else {
		myField.value += myValue;
	}
}

function getWindowHeight() {
    var windowHeight=0;
    if (typeof(window.innerHeight)=='number') {
        windowHeight=window.innerHeight;
    }
    else {
     if (document.documentElement&&
       document.documentElement.clientHeight) {
         windowHeight = document.documentElement.clientHeight;
    }
    else {
     if (document.body&&document.body.clientHeight) {
         windowHeight=document.body.clientHeight;
      }
     }
    }
    return windowHeight;
}

function getWindowWidth() {
    var windowWidth=0;
    if (typeof(window.innerWidth)=='number') {
        windowWidth=window.innerWidth;
    }
    else {
     if (document.documentElement&&
       document.documentElement.clientWidth) {
         windowWidth = document.documentElement.clientWidth;
    }
    else {
     if (document.body&&document.body.clientWidth) {
         windowWidth=document.body.clientWidth
      }
     }
    }
    return windowWidth;
}

function show_frame(url) {
	var att=document.getElementById("att");
	var notice_view=document.createElement("iframe");
	notice_view.setAttribute('id','frame_notice_preview');
	notice_view.setAttribute('name','notice_preview');
	notice_view.setAttribute('aria-modal','true');
	notice_view.setAttribute('role','dialog');
	notice_view.src=url;
	notice_view.style.visibility="hidden";
	notice_view.style.display="block";
	notice_view=att.appendChild(notice_view);
	w=notice_view.clientWidth;
	h=notice_view.clientHeight;
	posx=(getWindowWidth()/2-(w/2))<0?0:(getWindowWidth()/2-(w/2))
	posy=(getWindowHeight()/2-(h/2))<0?0:(getWindowHeight()/2-(h/2));
	posy+=getScrollTop();
	notice_view.style.left=posx+"px";
	notice_view.style.top=posy+"px";
	notice_view.style.visibility="visible";
	document.onmousedown=clic;
	if(!document.onkeyup) {
		document.onkeyup=touch_keyboard;
	}
}

function open_popup(popup_view,html) {

	var att=document.getElementById('att');
	att.appendChild(popup_view);
	//le html

	var popup_content = '<div class="popup_preview_content modal">';
	popup_content += '<div class="modal-header"><button type="button" class="popup_preview_close modal-close" onclick="close_popup(\''+popup_view.getAttribute('id')+'\')">Fermer <i aria-hidden="true" class="fa fa-times"></i></button></div>';
	popup_content += '<div class="modal-content">' + html + '</div>';
	popup_content += '</div>';

	popup_view.innerHTML = popup_content;

	//les attributs
	popup_view.setAttribute('class','popup_preview modal-container');
	popup_view.setAttribute('style','visibility:hidden;');

	//la position
	popup_view.style.visibility = 'visible';

	document.addEventListener('keydown', function (e) {
		if (e.code === 'Escape') {
			close_popup(popup_view.getAttribute('id'));
		}
	})

	document.addEventListener('click', function (e) {
		if (e.target.id === 'facettes_help') {
			close_popup(popup_view.getAttribute('id'));
		}
	})
}

function close_popup(popup_view_id){
	var popup_view=document.getElementById(popup_view_id);
	if(popup_view){

		popup_view.innerHTML='';
		popup_view.style.visibility='hidden';
	}
}

function getScrollTop(){
    var scrollTop;
    if(typeof(window.pageYOffset) == 'number'){
        scrollTop = window.pageYOffset;
    }else{
        if(document.body && document.body.scrollTop){
            scrollTop = document.body.scrollTop;
        }else if(document.documentElement && document.documentElement.scrollTop){
            scrollTop = document.documentElement.scrollTop;
        }
    }
    return scrollTop;
}

function show_layer() {
	var att = document.getElementById("att");
	var div_view_container = document.createElement("div");
	div_view_container = att.appendChild(div_view_container);
	div_view_container.setAttribute('id','frame_notice_preview_container');
	div_view_container.setAttribute('class','modal-container');
	var div_view=document.createElement("div");
	div_view.setAttribute('id','frame_notice_preview');
	div_view.setAttribute('name','layer_view');
	div_view.setAttribute('aria-modal','true');
	div_view.setAttribute('role','dialog');
	div_view.style.visibility="hidden";
	div_view.style.display="block";
	div_view.style.position="fixed";
	div_view.style.overflow="auto";
	div_view=div_view_container.appendChild(div_view);
	w=div_view.clientWidth;
	h=div_view.clientHeight;
	posx=(getWindowWidth()/2-(w/2))<0?0:(getWindowWidth()/2-(w/2))
	posy=(getWindowHeight()/2-(h/2))<0?0:(getWindowHeight()/2-(h/2));
	div_view.style.left= '50%';
	div_view.style.top='50%';
	div_view.style.visibility="visible";
	div_view.style.zIndex="500";
	div_view.style.maxWidth="100%";
	div_view.style.transform="translate(-50%, -50%)";

	if (document.getElementById("container")) {
		document.getElementById("container").onmousedown=clic_layer;
	}
	if(!document.onkeyup) {
		document.onkeyup=touch_keyboard_layer;
	}

	document.body.style.overflow="hidden";
}

function clic(e){
  	if (!e) var e=window.event;
	if (e.stopPropagation) {
		e.preventDefault();
		e.stopPropagation();
	} else {
		e.cancelBubble=true;
		e.returnValue=false;
	}
	kill_frame("frame_notice_preview_container");
  	document.onmousedown='';
}

function touch_keyboard(e){
  	if (!e) var e=window.event;
  	if (e.keyCode == 27) {
  		if (e.stopPropagation) {
  			e.preventDefault();
  			e.stopPropagation();
  		} else {
  			e.cancelBubble=true;
  			e.returnValue=false;
  		}
  	  	kill_frame("frame_notice_preview_container");
  	}
}

function clic_layer(e){
  	if (!e) var e=window.event;
	if (e.stopPropagation) {
		e.preventDefault();
		e.stopPropagation();
	} else {
		e.cancelBubble=true;
		e.returnValue=false;
	}
  	kill_frame("frame_notice_preview_container");
  	document.onmousedown='';
}

function touch_keyboard_layer(e){
  	if (!e) var e=window.event;
  	if (e.keyCode == 27) {
  		if (e.stopPropagation) {
  			e.preventDefault();
  			e.stopPropagation();
  		} else {
  			e.cancelBubble=true;
  			e.returnValue=false;
  		}
  	  	kill_frame("frame_notice_preview_container");
  	}
}

function kill_frame(block_name) {
	var notice_view=document.getElementById(block_name);
	if (notice_view) {
		dojo.forEach(dijit.findWidgets(dojo.byId(block_name)), function(w) {
		    w.destroyRecursive();
		});
		notice_view.parentNode.removeChild(notice_view);
	}
	document.body.style.overflow = null;

	const container = document.getElementById("container");
	if(container) {
		container.onmousedown = "";
	}

	document.onmousedown = "";
}