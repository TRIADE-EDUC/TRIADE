// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: harvest.js,v 1.3 2017/11/30 12:50:43 dgoron Exp $






var diff_list_checked=0;
function serialcirc_diff_get_group_form(){
	
	var num_abt = document.getElementById('num_abt').value;	
	var url= './ajax.php?module=catalog&categ=serialcirc_diff&sub=group_form';		
	url+='&num_abt='+num_abt;	
	var id = document.getElementById('serialcirc_diff_form_type');
	id.innerHTML =  '<div style=\"width:100%; height:30px;text-align:center\"><img style=\"padding 0 auto;\" src=\"'+pmb_img_patience+'\" id=\"collapseall\" border=\"0\"><\/div>' ;			
	// On initialise la classe:
	var req = new http_request();
	// Ex�cution de la requette
	if(req.request(url)) return 0;			
	// contenu
	id.innerHTML = req.get_text();
	
}	

function serialcirc_diff_get_option_form(){
	
	var num_abt = document.getElementById('num_abt').value;	
	var url= './ajax.php?module=catalog&categ=serialcirc_diff&sub=option_form';		
	url+='&num_abt='+num_abt;	
	var id = document.getElementById('serialcirc_diff_form_type');
	id.innerHTML =  '<div style=\"width:100%; height:30px;text-align:center\"><img style=\"padding 0 auto;\" src=\"'+pmb_img_patience+'\" id=\"collapseall\" border=\"0\"><\/div>' ;			
	// On initialise la classe:
	var req = new http_request();
	// Ex�cution de la requette
	if(req.request(url)) return 0;			
	// contenu
	id.innerHTML = req.get_text();
	
}	
function serialcirc_diff_get_ficheformat_form(){
	
	var num_abt = document.getElementById('num_abt').value;	
	var url= './ajax.php?module=catalog&categ=serialcirc_diff&sub=ficheformat_form';		
	url+='&num_abt='+num_abt;	
	var id = document.getElementById('serialcirc_diff_form_type');
	id.innerHTML =  '<div style=\"width:100%; height:30px;text-align:center\"><img style=\"padding 0 auto;\" src=\"'+pmb_img_patience+'\" id=\"collapseall\" border=\"0\"><\/div>' ;			
	// On initialise la classe:
	var req = new http_request();
	// Ex�cution de la requette
	if(req.request(url)) return 0;			
	// contenu
	id.innerHTML = req.get_text();
	// pour recalcuer les dragables de la fiche
	init_drag();
}	

function serialcirc_diff_get_empr_form(id_diff){

	var num_abt = document.getElementById('num_abt').value;		
	var url= './ajax.php?module=catalog&categ=serialcirc_diff&sub=empr_form';	
	url+='&num_abt='+num_abt;	
	url+='&id_diff='+id_diff;
	var id = document.getElementById('serialcirc_diff_form_type');
	id.innerHTML =  '<div style=\"width:100%; height:30px;text-align:center\"><img style=\"padding 0 auto;\" src=\"'+pmb_img_patience+'\" id=\"collapseall\" border=\"0\"><\/div>' ;		
	// On initialise la classe:
	var req = new http_request();
	// Ex�cution de la requette
	if(req.request(url)) return 0;			
	// contenu
	id.innerHTML = req.get_text();
}	

function serialcirc_diff_get_group_form(id_diff){

	var num_abt = document.getElementById('num_abt').value;		
	var url= './ajax.php?module=catalog&categ=serialcirc_diff&sub=group_form';	
	url+='&num_abt='+num_abt;	
	url+='&id_diff='+id_diff;
	var id = document.getElementById('serialcirc_diff_form_type');
	id.innerHTML =  '<div style=\"width:100%; height:30px;text-align:center\"><img style=\"padding 0 auto;\" src=\"'+pmb_img_patience+'\" id=\"collapseall\" border=\"0\"><\/div>' ;			
	// On initialise la classe:
	var req = new http_request();
	// Ex�cution de la requette
	if(req.request(url)) return 0;			
	// contenu
	id.innerHTML = req.get_text();
}	

function serialcirc_diff_group_raz_empr(suffixe){
	document.getElementById('libelle_member_'+suffixe).value='';
	document.getElementById('id_empr_'+suffixe).value=0;
}
function serialcirc_diff_group_resp_empr(suffixe){
	document.getElementById('empr_resp_'+suffixe).value=document.getElementById('id_empr_'+suffixe).value;
}
function serialcirc_diff_group_add_empr(suffixe){
	openPopUp('./select.php?what=emprunteur&caller=form_edition&param1=id_empr_'+suffixe+'&param2=libelle_member_'+suffixe+'&auto_submit=NO', 'selector')
}
function serialcirc_diff_group_add_line_empr(suffixe){
	var empr_cpt=document.getElementById('empr_count').value;
	empr_cpt++;
	
	var template = document.getElementById('addempr');
	var div=document.createElement('div');
	div.className='row';	
	var suffixe = empr_cpt;

	var resp = document.createElement('input');
	resp.setAttribute('type','radio');
	resp.name='empr_resp';
	resp.setAttribute('id','empr_resp_'+suffixe);
	resp.setAttribute('value','');	
	resp.onclick=function() {serialcirc_diff_group_resp_empr(suffixe)};


	var nom_id = 'libelle_member_'+suffixe;
	var libelle_member = document.createElement('input');
	libelle_member.setAttribute('name',nom_id);
	libelle_member.setAttribute('id',nom_id);
	libelle_member.setAttribute('type','text');
	libelle_member.className='saisie-30emr';
	libelle_member.setAttribute('readonly','');
	libelle_member.setAttribute('value','');
	
	var empr_id = document.createElement('input');
	empr_id.name='id_empr_'+suffixe;
	empr_id.setAttribute('type','hidden');
	empr_id.setAttribute('id','id_empr_'+suffixe);
	empr_id.setAttribute('value','');	

	var raz = document.createElement('input');
	raz.setAttribute('id','raz'+suffixe);
	raz.onclick=function() {serialcirc_diff_group_raz_empr(suffixe)};
	raz.setAttribute('type','button');
	raz.className='bouton_small';
	raz.setAttribute('readonly','');
	raz.setAttribute('value','X');
	
	var add = document.createElement('input');
	add.setAttribute('id','add'+suffixe);
	add.onclick=function() {serialcirc_diff_group_add_empr(suffixe)};
	add.setAttribute('type','button');
	add.className='bouton_small';
	add.setAttribute('readonly','');
	add.setAttribute('value','...');

	div.appendChild(resp);
	div.appendChild(document.createTextNode(' '));
	div.appendChild(libelle_member);
	
	div.appendChild(document.createTextNode(' '));
	div.appendChild(document.createTextNode(' '));
	div.appendChild(add);
	div.appendChild(document.createTextNode(' '));
	div.appendChild(raz);
	div.appendChild(empr_id);

	template.appendChild(div);
	
	document.getElementById('empr_count').value=empr_cpt;
}

function serialcirc_diff_record_button(){		
	var form_type = document.getElementById('form_type').value;		
	var num_abt = document.getElementById('num_abt').value;			
	var url= './catalog.php?&categ=serialcirc_diff&sub='+form_type;		
	url+='&num_abt='+num_abt;
	url+='&action=save';
	document.form_edition.action=url; 
	document.form_edition.submit();
}
function serialcirc_print_add_button(){		
	var form_type = document.getElementById('form_type').value;		
	var num_abt = document.getElementById('num_abt').value;			
	var url= './catalog.php?&categ=serialcirc_diff&sub='+form_type;		
	url+='&num_abt='+num_abt;
	url+='&action=add_field';
	document.form_edition.action=url; 
	document.form_edition.submit();
}
function serialcirc_print_del_button(index){		
	var form_type = document.getElementById('form_type').value;		
	var num_abt = document.getElementById('num_abt').value;			
	var url= './catalog.php?&categ=serialcirc_diff&sub='+form_type;		
	url+='&num_abt='+num_abt;
	url+='&action=del_field';
	url+='&index='+index;
	document.form_edition.action=url; 
	document.form_edition.submit();
}

function serialcirc_diff_delete_empr_button(){		
		
	var num_abt = document.getElementById('num_abt').value;			
	var url= './catalog.php?&categ=serialcirc_diff&sub=del_diff';		
	url+='&num_abt='+num_abt;
	
	document.form_empr_list.action=url; 
	document.form_empr_list.submit();
}

function serialcirc_diff_option_form_param_change(circ_type){	
	if(document.getElementById('virtual_circ').checked) {//circ vituelle
		document.getElementById('virtual_circ_part').style.display='block';
	}else{
		document.getElementById('virtual_circ_part').style.display='none';
	}	
}

function serialcirc_diff_selection_empr_button(){
	var elts = document.forms["form_empr_list"].elements['diff_list[]'];
	if(!elts)return;
	if (elts.length) {
		diff_list_checked=1-diff_list_checked;
		for (var i = 0; i < elts.length; i++) {
			elts[i].checked = diff_list_checked;
		} 
	} 
}