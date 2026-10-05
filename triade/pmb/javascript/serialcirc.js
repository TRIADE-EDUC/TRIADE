// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc.js,v 1.10 2023/01/05 11:11:13 dgoron Exp $

function serialcirc_circ_get_info_cb(cb,container){		
	
	var post_data='cb='+cb;	
	// Envoi du tout au serveur
	var http=new http_request();		
	var url = './ajax.php?module=circ&categ=periocirc&sub=cb_enter';
	http.request(url,true,post_data);
	
	var ret=http.get_text();
	document.getElementById(container).innerHTML=http.get_text();	
}	

function serialcirc_circ_list_bull_envoyer_alert(expl_id){
	var http=new http_request();		
	var url = './ajax.php?module=circ&categ=periocirc&sub=send_alert';
	url+='&expl_id='+expl_id;		
	http.request(url);	
	return http.get_text();
}

function serialcirc_print_list_circ(expl_id,start_diff_id){
	var url = './ajax.php?module=circ&categ=periocirc&sub=print_diffusion&expl_id='+expl_id;
	url+='&start_diff_id='+start_diff_id;	
	openPopUp(url, 'circulation');
}

function serialcirc_repair_list_circ(expl_id){
	var http=new http_request();		
	var url = './ajax.php?module=circ&categ=periocirc&sub=repair_diffusion&expl_id='+expl_id;
	http.request(url);	
	return http.get_text();
}

function serialize (txt) {
	switch(typeof(txt)){
	case 'string':
		return 's:'+txt.length+':\"'+txt+'\";';
	case 'number':
		if(txt>=0 && String(txt).indexOf('.') == -1 && txt < 65536) return 'i:'+txt+';';
		return 'd:'+txt+';';
	case 'boolean':
		return 'b:'+( (txt)?'1':'0' )+';';
	case 'object':
		var i=0,k,ret='';
		for(k in txt){
			//alert(isNaN(k));
			if(!isNaN(k)) k = Number(k);
			ret += serialize(k)+serialize(txt[k]);
			i++;
		}
		return 'a:'+i+':{'+ret+'}';
	default:
		return 'N;';
		alert('var undefined: '+typeof(txt));return undefined;
	}
}
function serialcirc_print_all_sel_list_diff(list){
	
	
	var url = './ajax.php?module=circ&categ=periocirc&sub=print_sel_diffusion';
	url+='&list='+serialize(list);
	//console.log(url);
	
	openPopUp(url, 'circulation');
}

function serialcirc_comeback_expl(expl_id){
	var http=new http_request();		
	var url = './ajax.php?module=circ&categ=periocirc&sub=return_expl';
	url+='&expl_id='+expl_id;		
	http.request(url);	
	return http.get_text();
}

function serialcirc_call_expl(expl_id){
	var http=new http_request();		
	var url = './ajax.php?module=circ&categ=periocirc&sub=call_expl';
	url+='&expl_id='+expl_id;		
	http.request(url);	
	return http.get_text();
}

function serialcirc_do_trans(expl_id){
	var http=new http_request();		
	var url = './ajax.php?module=circ&categ=periocirc&sub=do_trans';
	url+='&expl_id='+expl_id;		
	http.request(url);	
	return http.get_text();
}

function serialcirc_callinsist_expl(expl_id){
	var http=new http_request();		
	var url = './ajax.php?module=circ&categ=periocirc&sub=call_insist';
	url+='&expl_id='+expl_id;		
	http.request(url);	
	return http.get_text();	
}

function serialcirc_delete_circ(expl_id){
	var http=new http_request();		
	var url = './ajax.php?module=circ&categ=periocirc&sub=delete_diffusion';
	url+='&expl_id='+expl_id;		
	http.request(url);	
	return http.get_text();	
}
function serialcirc_is_returned(expl_id){
	var http=new http_request();		
	var url = './ajax.php?module=circ&categ=periocirc&sub=returned';
	url+='&expl_id='+expl_id;		
	http.request(url);	
	return http.get_text();	
}

function serialcirc_copy_accept(copy_id){
	var http=new http_request();		
	var url = './ajax.php?module=circ&categ=periocirc&sub=copy_accept';
	url+='&copy_id='+copy_id;		
	http.request(url);	
	return http.get_text();	
}
function serialcirc_copy_none(copy_id){
	var http=new http_request();		
	var url = './ajax.php?module=circ&categ=periocirc&sub=copy_none';
	url+='&copy_id='+copy_id;
	http.request(url);	
	return http.get_text();	
}

function serialcirc_resa_accept(expl_id,empr_id){
	var http=new http_request();		
	var url = './ajax.php?module=circ&categ=periocirc&sub=resa_accept';
	url+='&expl_id='+expl_id;		
	url+='&empr_id='+empr_id;		
	http.request(url);	
	return http.get_text();	
}
function serialcirc_resa_none(expl_id,empr_id){
	var http=new http_request();		
	var url = './ajax.php?module=circ&categ=periocirc&sub=resa_none';
	url+='&expl_id='+expl_id;		
	url+='&empr_id='+empr_id;		
	http.request(url);	
	return http.get_text();	
}