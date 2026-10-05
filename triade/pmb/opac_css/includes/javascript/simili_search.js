// +-------------------------------------------------+
// � 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: simili_search.js,v 1.5 2023/08/16 14:02:17 dbellamy Exp $

var tab_notices_simili_search_all=new Array();
				
function show_simili_search(id_notice){
	var simili_id=document.getElementById('simili_search_'+id_notice);
	
	if(simili_id){
		var div_contens =simili_id.innerHTML;
		if(div_contens.length >0) {
			return;
		}
		// patience
		simili_id.innerHTML =  '<div style=\"width:100%; height:30px;text-align:center\"><img style=\"padding 0 auto;\" src=\"'+pmb_img_patience+'\" id=\"collapseall\" ><\/div>' ;			
		
	}else return;		
	var url= './ajax.php?module=ajax&categ=simili&sub=search';		
	url+='&id_notice='+id_notice;
	var req = new http_request();
	req.request(url,0,'',1,show_simili_search_callback,0,0);		
}

function show_simili_search_callback(response){
	var data = eval('('+response+')');
	var id_notice=data.id;
	var simili_id=document.getElementById('simili_search_'+id_notice);	
	if(!simili_id) return;		
	// contenu
	if(!data.aff.length){
		simili_id.innerHTML =' '; 
		return;		
	}
	simili_id.innerHTML = data.aff;		
}

function show_simili_search_all(){
	for(var i=0;i<tab_notices_simili_search_all.length;i++){
		show_simili_search(tab_notices_simili_search_all[i]);
	}
}

function show_expl_voisin_search_all(){
	for(var i=0;i<tab_notices_simili_search_all.length;i++){
		show_expl_voisin_search(tab_notices_simili_search_all[i]);
	}
}

function show_expl_voisin_search(id_notice){
	var expl_voisin_id=document.getElementById('expl_voisin_search_'+id_notice);	
	if(expl_voisin_id) {
		var div_contens =expl_voisin_id.innerHTML;
		if(div_contens.length >0) {
			return;
		}
		expl_voisin_id.innerHTML =  '<div style=\"width:100%; height:30px;text-align:center\"><img style=\"padding 0 auto;\" src=\"'+pmb_img_patience+'\" id=\"collapseall\" ><\/div>' ;
	}else return;	
	var url= './ajax.php?module=ajax&categ=expl_voisin&sub=search';		
	url+='&id_notice='+id_notice;	
	var req = new http_request();
	req.request(url,0,'',1,show_expl_voisin_search_callback,0);		
}

function show_expl_voisin_search_callback(response){
	var data = eval('('+response+')');
	var id_notice=data.id;
	var expl_voisin_id=document.getElementById('expl_voisin_search_'+id_notice);	
	if(!expl_voisin_id) return;		
	// contenu
	if(data.aff.length==0){
		expl_voisin_id.innerHTML =' '; 
		return;		
	}
	expl_voisin_id.innerHTML = data.aff;			
}