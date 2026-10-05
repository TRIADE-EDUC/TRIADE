// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: select.js,v 1.14.2.1.2.1 2025/02/27 14:05:55 dgoron Exp $

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
	if(document.getElementById('frame_notice_preview')) {
		var notice_view=document.getElementById('frame_notice_preview');
	} else {
		var notice_view=document.createElement("iframe");
		notice_view.setAttribute('id','frame_notice_preview');
		notice_view.setAttribute('name','notice_preview');
		notice_view.setAttribute('aria-modal','true');
		notice_view.setAttribute('role','dialog');
	}
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
}

function open_popup(popup_view,html) {
	
	var att=document.getElementById('att');
	att.appendChild(popup_view);
	
	//le html
	popup_view.innerHTML=html;
	//la croix de fermeture
	var notice_view_close=document.createElement('div');
	notice_view_close.setAttribute('class','popup_preview_close');
	notice_view_close.setAttribute('onclick','close_popup("'+popup_view.getAttribute('id')+'")');
	notice_view_close.innerHTML="X";
	
	//on ajoute la croix
	popup_view.appendChild(notice_view_close);
	
	//les attributs
	popup_view.setAttribute('class','popup_preview');
	popup_view.setAttribute('style','visibility:hidden;display:block;');
	
	//la position
	w=popup_view.clientWidth;
	h=popup_view.clientHeight;
	posx=(getWindowWidth()/2-(w/2))<0?0:(getWindowWidth()/2-(w/2));
	posy=(getWindowHeight()/2-(h/2))<0?0:(getWindowHeight()/2-(h/2));
	posy+=getScrollTop();
	popup_view.style.left=posx+'px';
	popup_view.style.top=posy+'px';
	popup_view.style.visibility='visible';
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

function frame_shortcuts(url) {
	var att=document.getElementById('att');
	var shortcuts_view=document.createElement('iframe');
	shortcuts_view.setAttribute('id','frame_shortcuts');
	shortcuts_view.setAttribute('name','shortcuts');
	shortcuts_view.src=url;
	shortcuts_view.style.visibility='hidden';
	shortcuts_view.style.display='block';
	shortcuts_view=att.appendChild(shortcuts_view);
	w=shortcuts_view.clientWidth;
	h=shortcuts_view.clientHeight;
	posx=(getWindowWidth()/2-(w/2))<0?0:(getWindowWidth()/2-(w/2))
	posy=(getWindowHeight()/2-(h/2))<0?0:(getWindowHeight()/2-(h/2));
	shortcuts_view.style.left=posx+'px';
	shortcuts_view.style.top=posy+'px';
	shortcuts_view.style.visibility='visible';
}

function show_layer() {
	var att=document.getElementById("att");
	if(document.getElementById('frame_notice_preview')) {
		var div_view=document.getElementById('frame_notice_preview');
	} else {
		var div_view=document.createElement("div");
		div_view.setAttribute('id','frame_notice_preview');
		div_view.setAttribute('name','layer_view');
		div_view.setAttribute('aria-modal','true');
		div_view.setAttribute('role','dialog');
	}
	div_view.style.visibility="hidden";
	div_view.style.display="block";
	div_view.style.position="fixed";
	div_view.style.overflow="auto";
	div_view=att.appendChild(div_view);
	w=div_view.clientWidth;
	h=div_view.clientHeight;
	posx=(getWindowWidth()/2-(w/2))<0?0:(getWindowWidth()/2-(w/2))
	posy=(getWindowHeight()/2-(h/2))<0?0:(getWindowHeight()/2-(h/2));
	div_view.style.left=posx+"px";
	div_view.style.top=posy+"px";
	div_view.style.visibility="visible";
	
	for (i=2; i<21; i++) {
		document.body.childNodes[i].onmousedown=clic_layer;
	}
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
  	kill_frame("frame_notice_preview");
  	document.onmousedown='';
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
  	kill_frame("frame_notice_preview");
  	document.onmousedown='';
}

function kill_frame(block_name) {
	var notice_view=document.getElementById(block_name);
	if (notice_view)
		notice_view.parentNode.removeChild(notice_view);
}