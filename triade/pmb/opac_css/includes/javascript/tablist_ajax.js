//+-------------------------------------------------+
//� 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
//+-------------------------------------------------+
//$Id: tablist_ajax.js,v 1.13 2023/08/17 09:47:54 dbellamy Exp $

var expand_state=new Array();

function expandBase_ajax(el, unexpand,	notice_affichage_cmd){
	if (!isDOM)
		return;

	var whichEl = document.getElementById(el + 'Child');
	var whichIm = document.getElementById(el + 'Img');
	if (whichEl.style.display == 'none' && whichIm) {
		whichEl.style.display  = 'block';
		whichIm.src = whichIm.src.replace('nomgif=plus','nomgif=moins');
		changeCoverImage(whichEl);
		if(!expand_state[el]) {
			whichEl.innerHTML =  "<div style='width:100%; height:30px;text-align:center'><img style='padding 0 auto;' src='"+pmb_img_patience+"' id='collapseall' ></div>" ;
			var url= "./ajax.php?module=expand_notice&categ=expand";
			// On initialise la classe:
			var req = new http_request();
			// Ex�cution de la requette (url, post_flag ,post_param, async_flag, func_return, func_error) 
			req.request(url,1,'notice_affichage_cmd='+notice_affichage_cmd,1,expandBase_ajax_callback,expandBase_ajax_callback_error,el);
			expand_state[el]=1;
		}
	}else if (unexpand) {
		whichEl.style.display  = 'none';
		whichIm.src = whichIm.src.replace("nomgif=moins","nomgif=plus");
	}
} // end of the 'expandBase()' function

function expandBase_ajax_callback(text,el) {
	var whichEl = document.getElementById(el + 'Child');

	whichEl.innerHTML = text ;
	if (whichEl.getAttribute("enrichment")){
		getEnrichment(whichEl.getAttribute("id").replace("el","").replace("Child",""));
	}	
	if (whichEl.getAttribute("simili_search")){
		show_simili_search(whichEl.getAttribute("id").replace("el","").replace("Child",""));
		show_expl_voisin_search(whichEl.getAttribute("id").replace("el","").replace("Child",""));
	}
	
//	var whichAddthis = document.getElementById(el + 'addthis');
//	if (whichAddthis && !whichAddthis.getAttribute("added")){
//		creeAddthis(el);
//	}
	if(document.getElementsByName('surligne')) {
		var surligne = document.getElementsByName('surligne');
		if(surligne[0]){
			if (surligne[0].value == 1) rechercher(1);
		}
	}
  	if(typeof(dojo) == "object"){
  		dojo.parser.parse(whichEl);
  		require(['dojo/dom-construct', 'dojo/query'], function(domConstruct, query){
			query('script', whichEl).forEach(function(node) {
				domConstruct.create('script', {
					innerHTML: node.innerHTML
				}, node, 'replace');
			});
  		});
  	}
	ReinitializeAddThis();
}

function expandBase_ajax_callback_error(status,text,el) {
}

function expandAll_ajax(mode) {
	var tempColl    = document.getElementsByTagName('DIV');
	var nb_to_send=0;
	var display_cmd_all='';
	var elements = new Array();
	for (var i = 0 ; i< tempColl.length ; i++) {		
		if ((tempColl[i].className == 'notice-child') || (tempColl[i].className == 'child')) {
			tempColl[i].style.display = 'block';
		}		
		changeCoverImage(tempColl[i]);
		
		if (tempColl[i].className == 'notice-parent') {
			var obj_id=tempColl[i].getAttribute('id');
			var el=obj_id.replace(/Parent/,'');

			var tempImg = document.getElementById(el + 'Img');
			tempImg.src=tempImg.src.replace("nomgif=plus","nomgif=moins");
			if(!expand_state[el]) {
				var display_cmd= tempImg.getAttribute('param');
				if(!display_cmd){//Pour le cas ou le lien se fait sur le titre et pas sur l'image
					display_cmd= tempImg.parentNode.getAttribute('param');
				}
				expand_state[el]=1;
				if(display_cmd) {
					if(mode==1){
						//appel par lot
						nb_to_send++;
						document.getElementById(el + 'Child').innerHTML = "<div style='width:100%; height:30px;text-align:center'><img style='padding 0 auto;' src='"+pmb_img_patience+"' id='collapseall' ></div>";
						display_cmd_all+=display_cmd;
						if (i<(tempColl.length -1))display_cmd_all+='|*|*|';
						if(nb_to_send>40) {
							expandAll_ajax_block_suite('display_cmd='+display_cmd_all);
							display_cmd_all='';
							nb_to_send=0;
						}
					}else{
						//appel par notice		    		
						document.getElementById(el + 'Child').innerHTML = "<div style='width:100%; height:30px;text-align:center'><img style='padding 0 auto;' src='"+pmb_img_patience+"' id='collapseall' ></div>";
						expandAll_ajax_block_suite('display_cmd='+display_cmd);
					}
				}else{
					//les notices charg�es avec la page : enrichissement et addthis
					var whichEl = document.getElementById(el + 'Child');		
					if(whichEl.getAttribute("enrichment")){
						getEnrichment(el.replace("el",""));
					} 
					var whichAddthis = document.getElementById(el + 'addthis');
					if (whichAddthis && !whichAddthis.getAttribute("added")){
						creeAddthis(el);
					}
					if(whichEl.getAttribute("token") && whichEl.getAttribute("datetime")){
						elements.push(
								{"id":el.replace("el",""),
								"token":whichEl.getAttribute("token"),
								"datetime":whichEl.getAttribute("datetime")
								});
					}
				}
			}
		}
	}
	if (elements.length > 0) {
		expandAllNotices.records = elements;
		document.body.dispatchEvent(expandAllNotices);
	}
	if(nb_to_send){
		expandAll_ajax_block_suite('display_cmd='+display_cmd_all);
	}
}

function expandAll_ajax_block_suite(post_data){
	// On initialise la classe:
	var req = new http_request();
	// Ex�cution de la requette (url, post_flag , post_param, async_flag, func_return, func_error) 
	req.request("./ajax.php?module=expand_notice&categ=expand_block",1,post_data,1,expandAll_ajax_callback_block,expandAll_ajax_callback_block_error);
} 

function expandAll_ajax_callback_block(text,el) {
	var res=text.split("|*|*|");

	for(var i = 0; i < res.length; i++){
		var res_notice=res[i].split("|*|");
		if(res_notice[0] &&  res_notice[1]) {
			var whichEl = document.getElementById('el' + res_notice[0] + 'Child');
			whichEl.innerHTML = res_notice[1] ;
			if (whichEl.getAttribute("enrichment")){
				getEnrichment(whichEl.getAttribute("id").replace("el","").replace("Child",""));
			}
//			var whichAddthis = document.getElementById('el' + res_notice[0] + 'addthis');
//			if (whichAddthis && !whichAddthis.getAttribute("added")){
//				creeAddthis('el' + res_notice[0]);
//			}			
		  	if(typeof(dojo) == "object"){
		  		dojo.parser.parse(whichEl);
		  		require(['dojo/dom-construct', 'dojo/query'], function(domConstruct, query){
					query('script', whichEl).forEach(function(node) {
						domConstruct.create('script', {
							innerHTML: node.innerHTML
						}, node, 'replace');
					});
		  		});
		  	}
		}
	}
	if(document.getElementsByName('surligne')) {
		var surligne = document.getElementsByName('surligne');
		if (surligne[0].value == 1) {
			rechercher(1);
		}
	}
	ReinitializeAddThis();
}

function expandAll_ajax_callback_block_error(status,text,el) {
}