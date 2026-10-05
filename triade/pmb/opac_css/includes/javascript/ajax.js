// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax.js,v 1.44.2.2.2.3 2025/04/07 14:59:15 dgoron Exp $

requete=new Array();
line=new Array();
not_show=new Array();
last_word=new Array();
ids=new Array();
dontblur=false;
timers=new Array();
ajax_stat=new Array();//Permet de savoir si une requete Ajax est d�j� en cours
var pmb_dom_content_loaded = false;

document.addEventListener('DOMContentLoaded', function() {
	pmb_dom_content_loaded = true;
});

function isFirefox1() {
	if(navigator.userAgent.indexOf("Firefox")!=-1){
		var versionindex=navigator.userAgent.indexOf("Firefox")+8
		if (parseInt(navigator.userAgent.substr(versionindex))>1) {
			if (parseInt(navigator.userAgent.substr(versionindex))==2) {
				if (navigator.userAgent.substr(versionindex,7)=="2.0.0.2")
					return false;
				else
					return true;
			} else return true;
		} else return true;
	} else return true;
}

function findPos(obj) {
	var curleft = curtop = 0
	if (obj.offsetParent) {
		curleft = obj.offsetLeft
		curtop = obj.offsetTop
		while (obj = obj.offsetParent) {
		      if( (typeof(stop_find_pos) == "undefined") || (stop_find_pos && (obj.id != stop_find_pos))){
				curleft += obj.offsetLeft;
				curtop += obj.offsetTop;
		      }else{
			    break;
		      }
		}
	}
	return [curleft,curtop];
}

function show_simulate(id) {
	p=document.getElementById(id);
	poss=findPos(p);
	poss[1]+=p.clientHeight;
	document.getElementById('d'+id).style.left=poss[0]+'px';
	document.getElementById('d'+id).style.top=poss[1]+'px';
	document.getElementById('d'+id).style.display='block';
	not_show[id]=false;
	ajax_creerRequete(id);
	if (requete[id]) {
		last_word[id]=document.getElementById(id).value;
		ajax_get_info(id);
	}
}

function simulate_event(id) {
	if (document.getElementById("d"+id).style.display=="none") {
		if (document.getElementById(id).value=="") {
			document.getElementById(id).value="*";
		}
		setTimeout("show_simulate('"+id+"')",400);
	}
}

function ajax_resize_element(input){
	var id="";
	n=ids.length;
	if (input.getAttribute("completion")) {
		if (((input.getAttribute("type")=="text")||(input.nodeName=="TEXTAREA"))&&(input.getAttribute("id"))) {
			ids[n]=input.getAttribute("id");
			id=ids[n];
			w=input.clientWidth
			if(w) {
				d1= document.getElementById("d"+id);
				if(d1)d1.style.width=w+"px";
			}
		}
	}
}

function ajax_resize_elements(){
	var inputs=document.getElementsByTagName("input");
	for (i=0; i<inputs.length; i++) {
		ajax_resize_element(inputs[i]);
	}
	var textareas=document.getElementsByTagName("textarea");
	for (i=0; i<textareas.length; i++) {
		ajax_resize_element(textareas[i]);
	}
}

function ajax_parse_element(inputs) {
	var id="";
	n=ids.length;
	if (inputs.getAttribute("completion")) {
		if (((inputs.getAttribute("type")=="text")||inputs.getAttribute("type")=="email"||(inputs.nodeName=="TEXTAREA"))&&(inputs.getAttribute("id"))) {
			ids[n]=inputs.getAttribute("id");
			id=ids[n];
			//Insertion d'un div parent
			w=inputs.clientWidth;
			d=document.createElement("span");
			if(w) {
				d.style.width=w+"px";
			}
			p=inputs.parentNode;
			var input=inputs;
			p.replaceChild(d,inputs);
			d.appendChild(input);
			d1=document.createElement("div");
			d1.setAttribute("id","d"+id);
			d1.setAttribute("class","ajax_completion");
			d1.style.width=w+"px";
			d1.style.border="1px #000 solid";
			d1.style.left="0px";
			d1.style.top="0px";
			d1.style.display="none";
			d1.style.position="absolute";
			d1.style.backgroundColor="#FFFFFF";
			d1.style.zIndex=1000;
			var att = document.getElementById('att');
			if (att) {
				att.appendChild(d1);
			}
			if (input.addEventListener) {
				input.addEventListener("keyup",function(e) { ajax_update_info(e,'up'); },false);
				input.addEventListener("blur",function(e) { ajax_hide_list(e); },false);
			} else if (input.attachEvent) {
				input.attachEvent("onkeydown",function() { ajax_update_info(window.event,'down'); });//Pour internet explorer il faut que je capte l'appuie sur "entr�e" avant le formulaire
				input.attachEvent("onkeyup",function() { ajax_update_info(window.event,'up'); });
				input.attachEvent("onblur",function() { ajax_hide_list(window.event); });
			}
			//on retire l'autocomplete du navigateur...
			input.setAttribute("autocomplete","off");
			ajax_control_submit_form(id);
		}
	}
	requete[id]="";
	line[id]=0;
	not_show[id]=true;
	last_word[id]="";
}

function ajax_pack_element(inputs, domContentLoaded) {
	if (domContentLoaded || pmb_dom_content_loaded) {
		ajax_parse_element(inputs);
	} else {
		addLoadEvent(
			function() {
				ajax_parse_element(inputs);
            }
        );
	}
}

function active_autocomplete(inputs) {
	var inputs=document.getElementsByTagName("input");
	for (i=0; i<inputs.length; i++) {
		if (inputs[i].getAttribute("completion")) {
			if (((inputs[i].getAttribute("type")=="text")||(inputs[i].nodeName=="TEXTAREA"))&&(inputs[i].getAttribute("id"))) {
				//on remet l'autocomplete du navigateur...
				inputs[i].setAttribute("autocomplete","on");
			}
		}
	}
}

function ajax_parse_dom() {
    document.addEventListener('DOMContentLoaded', function() {
        ajax_parse_elements();
    });
}

function ajax_parse_elements() {
	var inputs = document.getElementsByTagName("input");
	for (i = 0; i < inputs.length; i++) {
		ajax_pack_element(inputs[i], true);
	}
	var textareas=document.getElementsByTagName("textarea");
	for (i = 0; i < textareas.length; i++) {
		ajax_pack_element(textareas[i], true);
	}
}

function ajax_hide_list(e) {
	if (!dontblur) {
		if (e.target) {
			var id=e.target.getAttribute("id");
		} else {
			var id=e.srcElement.getAttribute("id");
		}
		if(document.getElementById('d'+id)) {
			setTimeout("document.getElementById('d"+id+"').style.display='none'; not_show['"+id+"']=true;",500);
		}
	} else dontblur=false;
}

function ajax_set_datas(sp_name,id) {
	var sp=document.getElementById(sp_name);
	var text=sp.firstChild.nodeValue;
	var autfield=document.getElementById(id).getAttribute("autfield");
	if (autfield){
		document.getElementById(autfield).value=sp.getAttribute("autid");
		var thesid = sp.getAttribute("thesid");
		if(thesid && thesid >0){
			var theselector = document.getElementById(autfield.replace('field','fieldvar').replace("_id","")+"[id_thesaurus][]");
			if(theselector){
				for (var i=1 ; i< theselector.options.length ; i++){
					if (theselector.options[i].value == thesid){
						theselector.options[i].selected = true;
						break;
					}
				}
			}
		}
	}
	var callback=document.getElementById(id).getAttribute("callback");
	var word_only = document.getElementById(id).getAttribute("word_only");
	if(word_only == 'yes' && document.getElementById(id).value.lastIndexOf(" ") != false){
		document.getElementById(id).value=document.getElementById(id).value.substring(0,document.getElementById(id).value.lastIndexOf(" "))+" "+text;
	}else{
		document.getElementById(id).value=text;
	}
	document.getElementById(id).focus();
	document.getElementById("d"+id).style.display='none';
	not_show[id]=true;
	if(callback) window[callback](id);
}

function ajax_update_info(e,code) {
	if(e.target) {
		var id=e.target.getAttribute("id");
	} else {
		var id=e.srcElement.getAttribute("id");
	}

	if((code == "down") && (e.keyCode != 13)){
		return;
	}

	if (document.getElementById(id).getAttribute("disableCompletion")=='true') {
		return;
	}

	switch (e.keyCode) {
		case 27:	//Echap
			if (document.getElementById("d"+id).style.display=="block") {
				document.getElementById("d"+id).style.display='none';
				not_show[id]=true;
				if (timers[id]) {
					clearTimeout(timers[id]);
				}
				e.cancelBubble = true;
				if (e.stopPropagation) { e.stopPropagation(); }
			}
			break;
		case 40:	//Fl�che bas
			if(document.getElementById(id).value=="")	document.getElementById(id).value="*";
			next_line=line[id]+1;
			if (document.getElementById("d"+id).style.display=="block") {
				if (document.getElementById("l"+id+"_"+next_line)==null) break;
				old_line=line[id];
				line[id]++;
				sp=document.getElementById("l"+id+"_"+line[id]);
				sp.style.background='#000088';
				sp.style.color='#FFFFFF';
				if (old_line) {
					sp_old=document.getElementById("l"+id+"_"+old_line);
					sp_old.style.background='';
					sp_old.style.color='#000000';
				}
				e.cancelBubble = true;
				if (e.stopPropagation) e.stopPropagation();
			} else {
				if ((document.getElementById("d"+id).style.display=="none")&&(document.getElementById(id).value!="")) {
					p=document.getElementById(id);
					poss=findPos(p);
					poss[1]+=p.clientHeight;
					document.getElementById("d"+id).style.left=poss[0]+"px";
					document.getElementById("d"+id).style.top=poss[1]+"px";
					document.getElementById("d"+id).style.display='block';

					not_show[id]=false;
					if (timers[id]) {
						clearTimeout(timers[id]);
					}
					ajax_timer_creerRequete(id);
					e.cancelBubble = true;
					if (e.stopPropagation) e.stopPropagation();
				}
			}
			break;
		case 38:	//Fl�che haut
			if (document.getElementById("d"+id).style.display=="block") {
				old_line=line[id];
				if (line[id]>0) line[id]--;
				if (line[id]>0) {
					sp=document.getElementById("l"+id+"_"+line[id]);
					sp.style.background='#000088';
					sp.style.color='#FFFFFF';
				}
				if (old_line) {
					sp_old=document.getElementById("l"+id+"_"+old_line);
					sp_old.style.background='';
					sp_old.style.color='#000000';
				}
			}
			break;
		case 9:		//Tab
			if (document.getElementById("d"+id).style.display=="block") {
				document.getElementById("d"+id).style.display='none';
				not_show[id]=true;
				if (timers[id]) {
					clearTimeout(timers[id]);
				}
			}
			break;
		case 13:	//Enter
			if ((line[id])&&(document.getElementById("d"+id).style.display=="block")) {
				var sp=document.getElementById("l"+id+"_"+line[id]);
				var text=sp.firstChild.nodeValue;
				var autfield=document.getElementById(id).getAttribute("autfield");
				var callback=document.getElementById(id).getAttribute("callback");
				var div_cache=document.getElementById("c"+id+"_"+line[id]);
				if (autfield) {
					var autid=sp.getAttribute("autid");
					document.getElementById(autfield).value=autid;
				}
				if(div_cache){
					document.getElementById(id).value=div_cache.firstChild.nodeValue;
				} else {
					document.getElementById(id).value=text;
				}
				document.getElementById("d"+id).style.display='none';
				not_show[id]=true;
				if(e.preventDefault){
					e.preventDefault();//Firefox : Si je suis dans une liste je ne veux pas valider le formulaire quand je clic sur entr�e
				}else{
					e.returnValue = false;//IE : Si je suis dans une liste je ne veux pas valider le formulaire quand je clic sur entr�e
				}
			}

			if (sp) {
				var thesid = sp.getAttribute("thesid");
				if(thesid && thesid >0){
					var theselector = document.getElementById(autfield.replace('field','fieldvar').replace("_id","")+"[id_thesaurus][]");
					if(theselector){
						for (var i=1 ; i< theselector.options.length ; i++){
							if (theselector.options[i].value == thesid){
								theselector.options[i].selected = true;
								break;
							}
						}
					}
				}
			}
			if (callback) window[callback](id);
			break;
		case 113:	//F2
			if ((document.getElementById("d"+id).style.display=="none")&&(document.getElementById(id).value!="")) {
				p=document.getElementById(id);
				poss=findPos(p);
				poss[1]+=p.clientHeight;
				document.getElementById("d"+id).style.left=poss[0]+"px";
				document.getElementById("d"+id).style.top=poss[1]+"px";
				document.getElementById("d"+id).style.display='block';
				not_show[id]=false;
				if (timers[id]) {
					clearTimeout(timers[id]);
				}
				ajax_timer_creerRequete(id);
				e.cancelBubble = true;
				if (e.stopPropagation) e.stopPropagation();
			}
			break;
		default:	//Autres
			if (document.getElementById(id).getAttribute("expand_mode") || (document.getElementById(id).value.length > 2)) {
				if(document.getElementById(id).value=="") {
					if (timers[id]) {
						clearTimeout(timers[id]);
					}
				}
				if (document.getElementById(id).value!=""){
					if (timers[id]) {
						clearTimeout(timers[id]);
					}
					timeWait = parseInt(document.getElementById(id).getAttribute("expand_mode")) * 1000;
					timers[id]=setTimeout(function(){ajax_timer_creerRequete(id)},timeWait);
					break;
				}
			}
			if ((last_word[id]==document.getElementById(id).value)&&(last_word[id])) break;
			if ((document.getElementById(id).value!="")&&(!not_show[id])) {
				ajax_timer_creerRequete(id);
			} else {
				document.getElementById("d"+id).style.display='none';
				if (document.getElementById(id).value=="") not_show[id]=true;
			}
			last_word[id]=document.getElementById(id).value;
			break;
	}
}

function ajax_creerRequete(id) {
	ajax_requete_wait(id);
	try {
		requete[id]=new XMLHttpRequest();
	} catch (essaimicrosoft) {
		try {
			requete[id]=new ActiveXObject("Msxml2.XMLHTTP");
		} catch (autremicrosoft) {
			try {
				requete[id]=new ActiveXObject("Microsoft.XMLHTTP");
			} catch (echec) {
				requete[id]=null;
			}
		}
	}
}

function ajax_show_info(id) {
	if (requete[id].readyState==4) {
		if (requete[id].status=="200") {
			cadre=document.getElementById("d"+id);
			cadre.innerHTML=requete[id].responseText;
			line[id]=0;
			if (requete[id].responseText=="") {
				document.getElementById("d"+id).style.display='none';
			} else {
				p=document.getElementById(id);
				poss=findPos(p);
				poss[1]+=p.clientHeight;
				document.getElementById("d"+id).style.left=poss[0]+"px";
				document.getElementById("d"+id).style.top=poss[1]+"px";
				document.getElementById("d"+id).style.display='block';
			}
		} else {
			if(typeof console != 'undefined') {
				console.log("Erreur : le serveur a r�pondu "+requete.responseText);
			}
		}
		ajax_requete_wait_remove(id);
	}
}

function ajax_get_info(id) {
	var autexclude = '' ;
	var autfield = '' ;
	var linkfield = '' ;
	var listfield = '';
	var att_id_filter = '' ;
	var param1 = '' ;
	var param2 = '' ;
	var from_contrib = '';

	if(!base_path) var base_path = '.';
	requete[id].open("POST",base_path+"/ajax_selector.php",true);
	requete[id].onreadystatechange=function() { ajax_show_info(id) };
	requete[id].setRequestHeader("Content-Type","application/x-www-form-urlencoded");

	if (document.getElementById(id).getAttribute("autexclude")) autexclude = document.getElementById(id).getAttribute("autexclude");
	if (document.getElementById(id).getAttribute("linkfield") && document.getElementById(document.getElementById(id).getAttribute("linkfield"))) linkfield = document.getElementById(document.getElementById(id).getAttribute("linkfield")).value;
	if (document.getElementById(id).getAttribute("autfield")) autfield = document.getElementById(id).getAttribute("autfield");
	if (document.getElementById(id).getAttribute("param1")) param1 = document.getElementById(id).getAttribute("param1");
	if (document.getElementById(id).getAttribute("param2")) param2 = document.getElementById(id).getAttribute("param2");
	if (document.getElementById(id).getAttribute("att_id_filter")) att_id_filter = document.getElementById(id).getAttribute("att_id_filter");
	if (document.getElementById(id).getAttribute("from_contrib")) from_contrib = document.getElementById(id).getAttribute("from_contrib");
	if (document.getElementById(id).getAttribute("listfield")){
		var reg = new RegExp("[,]","g");
		var tab = (document.getElementById(id).getAttribute("listfield")).split(reg);
		for(var k=0;k<tab.length;k++){
			listfield = listfield + "&"+tab[k]+"="+(document.getElementById(tab[k]).value);
		}
	}
	requete[id].send("datas="+encode_URL(document.getElementById(id).value)+"&id="+encode_URL(id)+"&completion="+encode_URL(document.getElementById(id).getAttribute("completion"))+"&persofield="+encode_URL(document.getElementById(id).getAttribute("persofield"))+"&autfield="+encode_URL(autfield)+"&autexclude="+encode_URL(autexclude)+"&linkfield="+encode_URL(linkfield)+listfield+"&att_id_filter="+encode_URL(att_id_filter)+"&param1="+encode_URL(param1)+"&param2="+encode_URL(param2)+"&from_contrib="+encode_URL(from_contrib));
}

function ajax_requete_wait(id) {
	//Insertion d'un �l�ment pour l'attente
	if (document.getElementById("patience_"+id)) return;
	div=document.createElement("span");
	div.setAttribute("id","patience_"+id);
	div.style.width="100%";
	div.style.height="30px";
	img=document.createElement("img");
	img.src=pmb_img_patience;
	img.id="collapseall";
	img.style.border="0px";
	div.appendChild(img);
	document.getElementById(id).parentNode.appendChild(div);
}
function ajax_requete_wait_remove(id) {
	//Suppression de l'�l�ment pour l'attente
	try {
		wait=document.getElementById("patience_"+id);
		wait.parentNode.removeChild(wait);
	} catch(err){}

	//Controle du statut des requetes ajax
	if(ajax_stat[id] == "InProgress"){
		ajax_stat[id] = "End";
		ajax_timer_creerRequete(id);//Relance la requete ajax si il y a plusieurs requetes de suite
	}
	ajax_stat[id] = "End";
}

function ajax_timer_creerRequete(id) {

	if(ajax_stat[id] == "Start" || ajax_stat[id] == "InProgress"){
		ajax_stat[id] = "InProgress";
		return;//Pas d'appel ajax temps qu'il y en a une en cours
	}else{
		ajax_stat[id] = "Start";
	}
	ajax_creerRequete(id);
	if (requete[id]) {
		last_word[id]=document.getElementById(id).value;
		ajax_get_info(id);
	}
}

function ajax_control_submit_form(id){
	var node = document.getElementById(id);
	if (node) {
		while (node.parentNode!=null) {
			if (node.nodeName.toUpperCase() == "FORM" ) {
				break;
			} else {
				node = node.parentNode;
			}
		}

		var onsubmit = node.onsubmit;
		node.onsubmit = function(event) {
			const node = document.getElementById("d"+id);
			if (node && node.style.display == "block") {
				return false;
			} else {
				return onsubmit();
			}
		}
	}
}