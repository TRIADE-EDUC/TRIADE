// gestion des listes "collapsibles" en Javascript
// $Id: tablist.js,v 1.45 2023/09/01 15:25:35 tsamson Exp $

if(!base_path) var base_path = '.';
var imgOpened = new Image();
if(typeof pmb_img_minus != 'undefined') {
	imgOpened.src = pmb_img_minus;
} else {
	imgOpened.src = base_path+'/images/minus.gif';
}
var imgClosed = new Image();
if(typeof pmb_img_plus != 'undefined') {
	imgClosed.src = pmb_img_plus;
} else {
	imgOpened.src = base_path+'/images/plus.gif';
}
var imgPatience =new Image();
if(typeof pmb_img_patience != 'undefined') {
	imgPatience.src = pmb_img_patience;
} else {
	imgOpened.src = base_path+'/images/patience.gif';
}
var expandedDb = '';


// on regarde si le client est DOM-compliant

var isDOM = (typeof(document.getElementsByTagName) != 'undefined') ? 1 : 0;

//Konqueror (support DOM partiel) : on rejette
if(isDOM && typeof(navigator.userAgent) != 'undefined') {
    var browserName = ' ' + navigator.userAgent.toLowerCase();
    if(browserName.indexOf('konqueror') > 0) {
        isDOM = 0;
    }
}

function changeCoverImage(elt) {
	imgs=elt.getElementsByTagName('img');
	for (i=0; i < imgs.length; i++) {
		img=imgs[i];		
		isbn=img.getAttribute('isbn');
		vigurl=img.getAttribute('vigurl');
		url_image=img.getAttribute('url_image');
		if (vigurl) {
			if (img.src.substring(img.src.length-8,img.src.length)=='vide.png') {
				img.src=vigurl;
			}
		} else if (isbn) {
			if (img.src.substring(img.src.length-8,img.src.length)=='vide.png') {
				img.src=url_image.replace(/!!noticecode!!/,isbn);
			}
		}
	}
}

function expandAll_ajax_callback(text,el) {
	var whichEl = document.getElementById(el + 'Child');
  	whichEl.innerHTML = text ;
  	if(typeof(dojo) == "object"){
  		if (dijit.registry.byId(whichEl) == "undefined") {
			dojo.parser.parse(whichEl);
		}
  		require(['dojo/dom-construct', 'dojo/query'], function(domConstruct, query){
			query('script', whichEl).forEach(function(node) {
				domConstruct.create('script', {
					innerHTML: node.innerHTML,
					type: 'text/javascript'
				}, node, 'replace');
			});
  		});
  	}
  	publishDojoResize();
 }

function expandAll_ajax_callback_error(status,text,el) {
 }
 
function expandAll_ajax_callback_block(text,el) {
	var res=text.split("|*|*|");
	
	for(var i = 0; i < res.length; i++){
		var res_notice=res[i].split("|*|");
		if(res_notice[0] &&  res_notice[1]) {
			if (res_notice[2]) {
				var whichEl = document.getElementById(res_notice[2] + 'Child');
			} else {
				var whichEl = document.getElementById('el' + res_notice[0] + 'Child');
			}
	  		whichEl.innerHTML = res_notice[1] ;	
	  		if(typeof(dojo) == "object"){
	  	  		dojo.parser.parse(whichEl);
	  	  		require(['dojo/dom-construct', 'dojo/query'], function(domConstruct, query){
	  				query('script', whichEl).forEach(function(node) {
	  					domConstruct.create('script', {
	  						innerHTML: node.innerHTML,
	  						type: 'text/javascript'
	  					}, node, 'replace');
	  				});
	  	  		});
	  	  	}
  		}
	}
	publishDojoResize();
 }
 
function expandAll_ajax_callback_block_error(status,text,el) {
 }
 
function expandAll_ajax(start, context) {
	if ((context == undefined) || !context) context = document;
	var tempColl_img = context.querySelectorAll('img[name="imEx"]');	
	var tempCollNoticeChild = context.querySelectorAll('div[class~="notice-child"]');
	var tempCollChild = context.querySelectorAll('div[class~="child"]');
	var tempColl = Array.prototype.slice.call(tempCollNoticeChild).concat(Array.prototype.slice.call(tempCollChild));
	
	var liste_id='';
	var display_cmd='';
	var nb_to_send=0;
	var nb=0;
	if (!start)start=0;
	for (var i =start; i < tempColl.length; i++) {
 		tempColl[i].style.display = 'block';
 		nb++;     
 		if(nb >5){
 			setTimeout(function() {expandAll_ajax(i, context);},0);
 			return;
 		}
 		var callback = tempColl[i].getAttribute("callback");
 	    if(callback){
 	   	  window[callback]();
 	    }
 	    if(typeof ajax_resize_elements == "function"){
 	   	  ajax_resize_elements();
 	    }
	    changeCoverImage(tempColl[i]);	    
  	}
	
	for (var i = 0; i < tempColl_img.length; i++) {
		//on teste sur 2 niveaux
		if(Array.prototype.slice.call(tempColl_img[i].parentElement.classList).indexOf('notice-parent') != -1 || Array.prototype.slice.call(tempColl_img[i].parentElement.classList).indexOf('parent')!= -1 || Array.prototype.slice.call(tempColl_img[i].parentElement.parentElement.classList).indexOf('notice-parent') != -1 || Array.prototype.slice.call(tempColl_img[i].parentElement.parentElement.classList).indexOf('parent') != -1) {
			tempColl_img[i].src = imgOpened.src;
			
			var obj_id=tempColl_img[i].getAttribute('id');
	 		var el=obj_id.replace(/Img/,'');
	 		if(!expand_state[el]) {
	    		var mono_display_cmd= tempColl_img[i].getAttribute('param');
	    		expand_state[el]=1;
	    		
	    		if(mono_display_cmd) {
	    			nb_to_send++;
	    			document.getElementById(el + 'Child').innerHTML = "<div style='width:100%; height:30px;text-align:center'><img style='padding 0 auto;' src='"+imgPatience.src+"' id='collapseall' border='0'></div>";
	    			display_cmd+=mono_display_cmd+'|*|'+el;
	    			if (i<(tempColl_img.length -1))display_cmd+='|*|*|';
	    			if(nb_to_send>40) {
	    				setTimeout('expandAll_ajax_block_suite(\'display_cmd='+display_cmd+'\')',0);
	    				display_cmd='';
	    				nb_to_send=0;
	    			}	
				}
			}    
    	}
	} 
	publishDojoResize();
	if(nb_to_send)setTimeout('expandAll_ajax_block_suite(\'display_cmd='+display_cmd+'\')',0);
}

function expandAll_ajax_block_suite(post_data ) {
	// On initialise la classe:
	var req = new http_request();
	//	alert( post_data);
	// Ex�cution de la requette (url, post_flag ,post_param, async_flag, func_return, func_error) 
	req.request(base_path+"/ajax.php?module=ajax&categ=expand_block",1,post_data,1,expandAll_ajax_callback_block,expandAll_ajax_callback_block_error);
} 

function expandAll_ajax_suite(post_data,el ) {
	// On initialise la classe:
	var req = new http_request();
	// Ex�cution de la requette (url, post_flag ,post_param, async_flag, func_return, func_error) 
	req.request(base_path+"/ajax.php?module=ajax&categ=expand",1,post_data,1,expandAll_ajax_callback,expandAll_ajax_callback_error,el);
}

function expandAll(context) {
  if ((context == undefined) || !context) context = document;
  var tempCollNoticeChild = context.querySelectorAll('div[class~="notice-child"]');
  var tempCollChild = context.querySelectorAll('div[class~="child"]');
  var tempColl = Array.prototype.slice.call(tempCollNoticeChild).concat(Array.prototype.slice.call(tempCollChild));
  
  var tempCollCnt = tempColl.length;
  for (var i = 0; i < tempCollCnt; i++) {
     if (tempColl[i].previousElementSibling.style.display != 'none') {
    	 tempColl[i].style.display = 'block';
    	 setItemLocalStorage('pmb-expand-'+tempColl[i].id, tempColl[i].style.display);
     }
     var callback = tempColl[i].getAttribute("callback");
     if(callback){
   	  window[callback]();
     }
     if(typeof ajax_resize_elements == "function"){
   	  ajax_resize_elements();
     }
     changeCoverImage(tempColl[i]);
  }
  publishDojoResize();
  tempColl    = context.querySelectorAll('img[name="imEx"]');
  tempCollCnt = tempColl.length;
  for (var i = 0; i < tempCollCnt; i++) {
	  if(Array.prototype.slice.call(tempColl[i].parentElement.classList).indexOf('notice-parent') != -1 || Array.prototype.slice.call(tempColl[i].parentElement.classList).indexOf('parent')!= -1 || Array.prototype.slice.call(tempColl[i].parentElement.parentElement.classList).indexOf('notice-parent') != -1 || Array.prototype.slice.call(tempColl[i].parentElement.parentElement.classList).indexOf('parent') != -1) {
		  tempColl[i].src = imgOpened.src;
	  }
  }
}

function collapseAll(context) {
  if ((context == undefined) || !context) context = document;
  var tempCollNoticeChild = context.querySelectorAll('div[class~="notice-child"]');
  var tempCollChild = context.querySelectorAll('div[class~="child"]');
  var tempColl = Array.prototype.slice.call(tempCollNoticeChild).concat(Array.prototype.slice.call(tempCollChild));
  
  var tempCollCnt = tempColl.length;
  for (var i = 0; i < tempCollCnt; i++) {
     tempColl[i].style.display = 'none';
     setItemLocalStorage('pmb-expand-'+tempColl[i].id, tempColl[i].style.display);
  }
  tempColl    = context.querySelectorAll('img[name="imEx"]');
  tempCollCnt = tempColl.length;
  for (var i = 0; i < tempCollCnt; i++) {
	  //on teste sur 2 niveaux
     if(Array.prototype.slice.call(tempColl[i].parentElement.classList).indexOf('notice-parent') != -1 || Array.prototype.slice.call(tempColl[i].parentElement.classList).indexOf('parent')!= -1 || Array.prototype.slice.call(tempColl[i].parentElement.parentElement.classList).indexOf('notice-parent') != -1 || Array.prototype.slice.call(tempColl[i].parentElement.parentElement.classList).indexOf('parent') != -1) {
    	 tempColl[i].src = imgClosed.src;
     }
  }
  publishDojoResize();
}

function initIt() {
  if (!isDOM) {
//    alert("ce navigateur n'est pas compatible avec le DOM.");
    return;
  }
  var tempCollNoticeChild = document.querySelectorAll('div[class~="notice-child"]');
  var tempCollChild = document.querySelectorAll('div[class~="child"]');
  var tempColl = Array.prototype.slice.call(tempCollNoticeChild).concat(Array.prototype.slice.call(tempCollChild));
  var tempCollCnt = tempColl.length;
  for (var i = 0; i < tempCollCnt; i++) {
 	if(tempColl[i].hasAttribute('startOpen')){
 		var localStorageItem = null;
 		if(typeof window.localStorage != 'undefined') {
 			try {
 				localStorageItem = parseInt(window.localStorage.getItem('pmb-expand-'+tempColl[i].id));
 			} catch(e) {
 			}
 		}
 		let elId = tempColl[i].id.substring(0,tempColl[i].id.indexOf('Child'));
		if (tempColl[i].getAttribute('startOpen') == 'Yes' ) {
			if(localStorageItem != null && !isNaN(localStorageItem) && !localStorageItem) {
	 			collapseBase (tempColl[i].id.substring(0,tempColl[i].id.indexOf('Child')));
	 		} else {
 				if (tempColl[i].hasAttribute('data-expand-ajax')) {
					let expandImg = document.getElementById(elId + 'Img');
					expandBase_ajax(elId,true,expandImg.getAttribute('param'));
				} else {
					expandBase(elId, true);
				}
	 		}
	 	} else {
	 		if(localStorageItem != null && !isNaN(localStorageItem) && localStorageItem) {
	 			if (tempColl[i].hasAttribute('data-expand-ajax')) {
					let expandImg = document.getElementById(elId + 'Img');
					expandBase_ajax(elId,true,expandImg.getAttribute('param'));
				} else {
					expandBase(elId, true);
				}
	 		} else {
	 			tempColl[i].style.display = 'none';
	 		}
	 	}
	  }
  }
} // end of the 'initIt()' function

var expand_state=new Array();

function expandBase_ajax(el, unexpand,	mono_display_cmd) {
  if (!isDOM)
    return;
  
  var whichEl = document.getElementById(el + 'Child');
  var whichIm = document.getElementById(el + 'Img');
  var callback = whichEl.getAttribute("callback");
  if (whichEl.style.display == 'none' && whichIm) {
   	whichEl.style.display  = 'block';
    whichIm.src            = imgOpened.src;

    changeCoverImage(whichEl);     
    if(!expand_state[el]) {
    	whichEl.innerHTML =  "<div style='width:100%; height:30px;text-align:center'><img style='padding 0 auto;' src='"+imgPatience.src+"' id='collapseall' border='0'></div>" ;

		// On initialise la classe:
			    var url= base_path+"/ajax.php?module=ajax&categ=expand";	 
				// On initialise la classe:
				var req = new http_request();
				// Ex�cution de la requette (url, post_flag ,post_param, async_flag, func_return, func_error) 
				req.request(url,1,'mono_display_cmd='+mono_display_cmd,1,expandAll_ajax_callback,expandAll_ajax_callback_error,el);
			expand_state[el]=1;
		
	}
 
  }
  else if (unexpand) {
    whichEl.style.display  = 'none';
    whichIm.src            = imgClosed.src;
  }
  setItemLocalStorage('pmb-expand-'+el+'Child', whichEl.style.display);
  if(callback){
	  window[callback]();
  }
  if(typeof ajax_resize_elements == "function"){
	  ajax_resize_elements();
  }
  publishDojoResize();
} // end of the 'expandBase()' function

function expandBase(el, unexpand) {
  if (!isDOM)
    return;
  var whichEl = document.getElementById(el + 'Child');
  var whichIm = document.getElementById(el + 'Img');
  var callback = whichEl.getAttribute("callback");
  if (whichEl.style.display == 'none') {
	if(whichEl.tagName == 'TABLE') {
		whichEl.style.display  = 'inline-table';
	} else {
		whichEl.style.display  = 'block';
	}
    if (whichIm)whichIm.src = imgOpened.src;
    changeCoverImage(whichEl);
  }
  else if (unexpand) {
    whichEl.style.display  = 'none';
    if (whichIm)whichIm.src            = imgClosed.src;
  }
  setItemLocalStorage('pmb-expand-'+el+'Child', whichEl.style.display);
  if(callback){
	  window[callback]();
  }
  if(typeof ajax_resize_elements == "function"){
	  ajax_resize_elements();
  }
  publishDojoResize();
} // end of the 'expandBase()' function

function collapseBase(el) {
  if (!isDOM)
    return;
  var whichEl = document.getElementById(el + 'Child');
  var whichIm = document.getElementById(el + 'Img');
  whichEl.style.display  = 'none';
  if (whichIm)whichIm.src            = imgClosed.src;
  setItemLocalStorage('pmb-expand-'+el+'Child', whichEl.style.display);
  if(typeof ajax_resize_elements == "function"){
	  ajax_resize_elements();
  }
  publishDojoResize();
} // end of the 'collapseBase()' function

function publishDojoResize(){
	if(typeof require == "function"){
		  require(['dojo/topic'], function(topic){
			  topic.publish('tablist', 'tablist', 'expand');
		  });  
	  }
}

function setItemLocalStorage(name, value) {
	try {
		if(typeof window.localStorage != 'undefined') {
			if(value == 'none') {
				window.localStorage.setItem(name, 0);
			} else if(value == 'block') {
				window.localStorage.setItem(name, 1);
			} else {
				window.localStorage.setItem(name, value);
			}
			
		}
	} catch(e) {
	    if(e.name == "NS_ERROR_FILE_CORRUPTED") {
	        console.log("Sorry, it looks like your browser storage has been corrupted. Please clear your storage by going to Tools -> Clear Recent History -> Cookies and set time range to 'Everything'. This will remove the corrupted browser storage across all sites.");
	    }
	}
}

function checkAllObjects(action, name) {
	var elements = document.querySelectorAll("input[name='"+name+"']");
	elements.forEach(function(element) {
		element.checked = (action === "check");
	})
}

//on pr�vient les doubles inclusions du fichier selon le contexte...
if (typeof addLoadEventTablistJs == "undefined" && typeof addLoadEvent != "undefined") {
	addLoadEvent(initIt);
	addLoadEventTablistJs = true;
}
