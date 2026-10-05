/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_pages.js,v 1.3 2012/11/13 16:17:16 ngantier Exp $ */

	
function cms_page_raz_var(suffixe){
	document.getElementById('var_name_'+suffixe).value='';
	document.getElementById('var_comment_'+suffixe).value='';
}

function cms_page_add_var(){
	var cpt=document.getElementById('var_count').value;
	cpt++;
	
	var template = document.getElementById('add_var');
	var div=document.createElement('div');
	div.className='row';
	var suffixe = cpt;
	
	var nom_id = 'var_name_'+suffixe;
	var libelle_member = document.createElement('input');
	libelle_member.setAttribute('name',nom_id);
	libelle_member.setAttribute('id',nom_id);
	libelle_member.setAttribute('type','text');
	libelle_member.className='saisie-50em';	
	libelle_member.setAttribute('value','');
	
	var comment_id = 'var_comment_'+suffixe;
	var comment_member = document.createElement('input');
	comment_member.setAttribute('name',comment_id);
	comment_member.setAttribute('id',comment_id);
	comment_member.setAttribute('type','text');
	comment_member.className='saisie-50em';	
	comment_member.setAttribute('value','');

	var raz = document.createElement('input');
	raz.setAttribute('id','raz'+suffixe);
	raz.onclick=function() {cms_page_raz_var(suffixe);};
	raz.setAttribute('type','button');
	raz.className='bouton_small';
	raz.setAttribute('readonly','');
	raz.setAttribute('value','X');
	
	div.appendChild(libelle_member);	
	div.appendChild(document.createTextNode(' '));
	div.appendChild(comment_member);
	
	div.appendChild(document.createTextNode(' '));
	div.appendChild(document.createTextNode(' '));
	div.appendChild(raz);

	template.appendChild(div);
	
	document.getElementById('var_count').value=cpt;
	
}	

function cms_page_ajax_save(id){
		
	var http=new http_request();	
	var url = './ajax.php?module=cms&categ=pages&sub=save';
	url+='&name='+ document.getElementById('name').value;
	url+='&description='+ document.getElementById('description').value;
	url+='&id='+ id;
	var var_count=document.getElementById('var_count').value;
	for(var i=0; i<var_count; i++){
		var var_name='var_name_'+(i+1);
		if(document.getElementById(var_name)){
			url+='&'+ var_name +'='+ document.getElementById(var_name).value;
			var var_comment='var_comment_'+(i+1);
			url+='&'+ var_comment +'='+ document.getElementById(var_comment).value;
		}	
	}	
	url+='&var_count='+ var_count;
	http.request(url);	
	return http.get_text();
}

function cms_page_ajax_delete(id){

	var http=new http_request();	
	var url = './ajax.php?module=cms&categ=pages&sub=del';
	url+='&id='+ id;	
	http.request(url);	
	return http.get_text();
}	

function cms_build_load_pages_list(){	
	

}
   