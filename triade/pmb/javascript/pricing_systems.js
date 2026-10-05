// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pricing_systems.js,v 1.30 2024/04/04 07:03:27 dgoron Exp $

/**
 * Affichage Oui/Non de la grille dans la liste
 */
function display_grid(id) {
	var whichEl = document.getElementById('pricing_system_grid_'+id);
	var whichIm = document.getElementById('pricing_system_img_'+id);
	if (whichEl.style.display=='none') {
		whichEl.style.display='';
		if (whichIm)whichIm.src = imgOpened.src;
	} else {
		whichEl.style.display='none';
		if (whichIm)whichIm.src = imgClosed.src;
	}	
}

/**
 * Ajout d'un intervalle dans la gestion du minutage
 */
function pricing_system_grid_add_interval(){

	var intervals_content = document.getElementById('intervals_content');
	var interval_max = document.getElementById('pricing_system_grid_interval_max');
	var indice = parseInt(interval_max.value);
	
	var div_row=document.createElement('div');
	div_row.className='row';
	div_row.setAttribute('id','pricing_system_grid_interval_'+indice);
	
	var previous_node_id = intervals_content.children[intervals_content.children.length-1].id;
	var previous_indice = previous_node_id.substring(previous_node_id.length-1);
	var time_start_value=parseInt(document.getElementById('pricing_system_grid_interval_time_end_'+previous_indice).value) +1;
		
	var div_row_1=document.createElement('div');
	div_row_1.className='colonne10';
	var time_start = document.createElement('input');
	time_start.setAttribute('name','pricing_system_grid_intervals['+indice+'][time_start]');
	time_start.setAttribute('id','pricing_system_grid_interval_time_start_'+indice);
	time_start.setAttribute('type','number');
	time_start.setAttribute('min','0');
	time_start.className='saisie-5em';
	time_start.setAttribute('value',time_start_value);
	div_row_1.appendChild(time_start);
	
	var div_row_2=document.createElement('div');
	div_row_2.className='colonne10';
	var time_end = document.createElement('input');
	time_end.setAttribute('name','pricing_system_grid_intervals['+indice+'][time_end]');
	time_end.setAttribute('id','pricing_system_grid_interval_time_end_'+indice);
	time_end.setAttribute('type','number');
	time_end.setAttribute('min','0');
	time_end.className='saisie-5em';
	time_end.setAttribute('value','');
	div_row_2.appendChild(time_end);	
	
	var div_row_3=document.createElement('div');
	div_row_3.className='colonne10';
	var price = document.createElement('input');
	price.setAttribute('name','pricing_system_grid_intervals['+indice+'][price]');
	price.setAttribute('id','pricing_system_grid_interval_price_'+indice);
	price.setAttribute('type','text');
	price.className='saisie-5em';
	price.setAttribute('value','');
	div_row_3.appendChild(price);	

	var div_row_4=document.createElement('div');
	div_row_4.className='colonne10';
	var delete_button = document.createElement('input');
	delete_button.setAttribute('type','button');
	delete_button.className='bouton';
	delete_button.setAttribute('value','X');
	delete_button.setAttribute('onclick','pricing_system_grid_delete_interval(\"pricing_system_grid_interval_'+indice+'\");');
	div_row_4.appendChild(delete_button);	

	div_row.appendChild(div_row_1);
	div_row.appendChild(div_row_2);
	div_row.appendChild(div_row_3);
	div_row.appendChild(div_row_4);

	intervals_content.appendChild(div_row);
	document.getElementById('pricing_system_grid_interval_time_end_'+indice).focus();
	interval_max.setAttribute('value', (indice+1));
}

/**
 * Suppression d'une ligne d'intervalle
 * @param interval_id
 */
function pricing_system_grid_delete_interval(interval_id){

	var interval_node = document.getElementById(interval_id);
	interval_node.parentNode.removeChild(interval_node);
}

function pricing_system_grid_add_percent(){
	var percents_content = document.getElementById('percents_content');
	var percent_max = document.getElementById('pricing_system_grid_percent_max');
	var indice = parseInt(percent_max.value);
	
	var div_row=document.createElement('div');
	div_row.className='row';
	div_row.setAttribute('id','pricing_system_grid_percent_column_'+indice);
	
	var div_row_1=document.createElement('div');
	div_row_1.className='colonne10';
	var percent = document.createElement('input');
	percent.setAttribute('name','pricing_system_grid_percents['+indice+']');
	percent.setAttribute('id','pricing_system_grid_percent_'+indice);
	percent.setAttribute('type','text');
	percent.className='saisie-5em';
	div_row_1.appendChild(percent);
	
	var div_row_2=document.createElement('div');
	div_row_2.className='colonne10';
	var delete_button = document.createElement('input');
	delete_button.setAttribute('type','button');
	delete_button.className='bouton';
	delete_button.setAttribute('value','X');
	delete_button.setAttribute('onclick','pricing_system_grid_delete_percent(\"pricing_system_grid_percent_column_'+indice+'\");');
	div_row_2.appendChild(delete_button);
	
	div_row.appendChild(div_row_1);
	div_row.appendChild(div_row_2);
	percents_content.appendChild(div_row);
	document.getElementById('pricing_system_grid_percent_'+indice).focus();
	percent_max.setAttribute('value', (indice+1));
}

function pricing_system_grid_delete_percent(percent_id){
	var percent_node = document.getElementById(percent_id);
	percent_node.parentNode.removeChild(percent_node);
}

/**
 * Demande de confirmation pour l'initialisation de la grille
 */
function pricing_system_grid_confirm_reset() {
	if(confirm(msg_pricing_system_grid_reset_confirm)) {
		document.location='./admin.php?categ=acquisition&sub=pricing_systems&id_entity=!!id_entity!!&action=grid_reset&id=!!id!!';
	}
}

function set_style_elements(form, elems, flag_error, msg_error) {
	for(var i = 0; i < elems.length; i++) {
		if(flag_error) {
			form.elements[elems[i]].setAttribute('style','border: 2px solid; border-color: red');
			form.elements[elems[i]].setAttribute('title',msg_error);
		} else {
			form.elements[elems[i]].setAttribute('style','');
			form.elements[elems[i]].setAttribute('title','');
		}
	}		
}

/**
 * V�rification des informations du formulaire
 * @param form
 * @returns {Boolean}
 */
function pricing_system_grid_check_form(form){
	var elems_with_intervals_errors = new Array();
	var elems_without_intervals_errors = new Array();
	var elems_with_values_errors = new Array();
	var elems_without_values_errors = new Array();
	var flag_error = false;
	var interval_max = document.getElementById('pricing_system_grid_interval_max').value;
	var time_start = 0;
	var time_end = 0;
	var price = '0.00';
	var previous_time_end = -1;
	for(var i = 0; i < interval_max; i++) {
		if(form.elements['pricing_system_grid_intervals['+i+'][time_start]'].value) {
			time_start = parseInt(form.elements['pricing_system_grid_intervals['+i+'][time_start]'].value);
			time_end = parseInt(form.elements['pricing_system_grid_intervals['+i+'][time_end]'].value);
			price = parseFloat(form.elements['pricing_system_grid_intervals['+i+'][price]'].value);
			if(!isNaN(time_start) && !isNaN(time_end) && !isNaN(price)) {
				elems_without_intervals_errors.push('pricing_system_grid_intervals['+i+'][time_end]');
				elems_without_intervals_errors.push('pricing_system_grid_intervals['+i+'][price]');
				if((previous_time_end+1) != time_start) {
					if(i == 0 && (time_start != 0)) {
						set_style_elements(form, ['pricing_system_grid_intervals['+i+'][time_start]'], true, msg_pricing_system_grid_error_first_interval);
						flag_error = true;
					} else {
						elems_with_intervals_errors.push('pricing_system_grid_intervals['+i+'][time_start]');
					}
				} else {
					elems_without_intervals_errors.push('pricing_system_grid_intervals['+i+'][time_start]');
				}
				previous_time_end = time_end;
			} else {
				if(isNaN(time_start)) {
					elems_with_values_errors.push('pricing_system_grid_intervals['+i+'][time_start]');
				} else {
					if(i == 0 && (time_start != 0)) {
						set_style_elements(form, ['pricing_system_grid_intervals['+i+'][time_start]'], true, msg_pricing_system_grid_error_first_interval);
						flag_error = true;
					} else {
						elems_without_values_errors.push('pricing_system_grid_intervals['+i+'][time_start]');
					}
				}
				if(isNaN(time_end)) {
					elems_with_values_errors.push('pricing_system_grid_intervals['+i+'][time_end]');
				} else {
					elems_without_values_errors.push('pricing_system_grid_intervals['+i+'][time_end]');
				}
				if(isNaN(price)) {
					elems_with_values_errors.push('pricing_system_grid_intervals['+i+'][price]');
				} else {
					elems_without_values_errors.push('pricing_system_grid_intervals['+i+'][price]');
				}
			}
		}
	}
	if(isNaN(parseInt(form.elements['pricing_system_grid_extra[0][time]'].value))) {
		elems_with_values_errors.push('pricing_system_grid_extra[0][time]');
	} else {
		elems_without_values_errors.push('pricing_system_grid_extra[0][time]');
	}
	if(isNaN(parseFloat(form.elements['pricing_system_grid_extra[0][price]'].value))) {
		elems_with_values_errors.push('pricing_system_grid_extra[0][price]');
	} else {
		elems_without_values_errors.push('pricing_system_grid_extra[0][price]');
	}
	if(isNaN(parseFloat(form.elements['pricing_system_grid_not_used[0][price]'].value))) {
		elems_with_values_errors.push('pricing_system_grid_not_used[0][price]');		
	} else {
		elems_without_values_errors.push('pricing_system_grid_not_used[0][price]');
	}
	var percent_max = document.getElementById('pricing_system_grid_percent_max').value;
	for(var i = 0; i < percent_max; i++) {
		if(form.elements['pricing_system_grid_percents['+i+']'].value) {
			if(isNaN(parseFloat(form.elements['pricing_system_grid_percents['+i+']'].value))) {
				elems_with_values_errors.push('pricing_system_grid_percents['+i+']');
			} else {
				elems_without_values_errors.push('pricing_system_grid_percents['+i+']');
			}
		}		
	}
	if(elems_without_intervals_errors.length) {
		set_style_elements(form, elems_without_intervals_errors, false);
	}
	if(elems_without_values_errors.length) {
		set_style_elements(form, elems_without_values_errors, false);
	}
	if(elems_with_intervals_errors.length) {
		set_style_elements(form, elems_with_intervals_errors, true, msg_pricing_system_grid_error_interval);
		flag_error = true;
	}
	if(elems_with_values_errors.length) {
		set_style_elements(form, elems_with_values_errors, true, msg_pricing_system_grid_error_value);
		flag_error = true;
	}
	if(flag_error) {
		if(msg_pricing_system_grid_error) alert(msg_pricing_system_grid_error);
		return false;
	} else {
		return true;
	}
}

function show_grid_in_account(id) {
	var url = './ajax.php?module=acquisition&categ=rent&sub=get_grid&id='+id;
	var req = new http_request();
	req.request(url,0,'');
	document.getElementById('frame_notice_preview').innerHTML=req.get_text();
}

function account_load_exercices(id_entity, objects_type) {
	var url = './ajax.php?module=acquisition&categ=rent&sub=get_exercices&objects_type='+objects_type+'&id_entity='+id_entity;
	var req = new http_request();
	req.request(url,0,'');
	if(document.getElementById('rent_requests_ui_exercice')) {
		var anchor = document.getElementById('rent_requests_ui_exercice');
	} else if(document.getElementById('rent_accounts_ui_exercice')) {
		var anchor = document.getElementById('rent_accounts_ui_exercice');
	} else {
		var anchor = document.getElementById('rent_invoices_ui_exercice');
	}
	var span = document.createElement("span");
	span.innerHTML = req.get_text();
	anchor.parentNode.replaceChild(span, anchor); 
}

function account_selected_grid(grid) {
	if(parseInt(grid.value)) {
		document.getElementById('account_percent').removeAttribute('disabled');
		document.getElementById('account_grid_see').setAttribute('style', '');
		account_update_price_from_time(document.getElementById('account_time').value);
	} else {
		document.getElementById('account_percent').setAttribute('disabled', 'disabled');
		document.getElementById('account_grid_see').setAttribute('style', 'display:none;');
	}
}

function invoices_delete_account(id, id_invoice) {
	var url = './ajax.php?module=acquisition&categ=rent&sub=invoices&id_invoice='+id_invoice+'&action=delete_account&id='+id;
	var req = new http_request();
	req.request(url, 0, '');
	if(req.get_text() == '1') {
		document.location = './acquisition.php?categ=rent&sub=invoices&action=edit&id='+id_invoice;
	}
}

function account_update_price_from_time(time) {
	var grid = document.getElementById('account_num_pricing_system');
	if(grid.value != 0) {
		var percent = document.getElementById('account_percent').value;
		var url = './ajax.php?module=acquisition&categ=rent&sub=get_grid&id='+grid.value+'&action=get_price&from=time&value='+time+'&with='+percent;
		var req = new http_request();
		req.request(url,0,'');
		document.getElementById('account_price').value=req.get_text();
		
		var percent = document.getElementById('account_web_percent').value;
		var url = './ajax.php?module=acquisition&categ=rent&sub=get_grid&id='+grid.value+'&action=get_price&from=time&value='+time+'&with='+percent;
		var req = new http_request();
		req.request(url,0,'');
		document.getElementById('account_web_price').value=req.get_text();
	}
}

function account_update_price_from_percent(percent) {
	var grid = document.getElementById('account_num_pricing_system');
	if(grid.value != 0) {
		var time = document.getElementById('account_time').value;
		var url = './ajax.php?module=acquisition&categ=rent&sub=get_grid&id='+grid.value+'&action=get_price&from=percent&value='+percent+'&with='+time;
		var req = new http_request();
		req.request(url,0,'');
		document.getElementById('account_price').value=req.get_text();
	}
}

function account_update_web_price_from_web_percent(percent) {
	var grid = document.getElementById('account_num_pricing_system');
	if(grid.value != 0) {
		var time = document.getElementById('account_time').value;
		var url = './ajax.php?module=acquisition&categ=rent&sub=get_grid&id='+grid.value+'&action=get_price&from=percent&value='+percent+'&with='+time;
		var req = new http_request();
		req.request(url,0,'');
		document.getElementById('account_web_price').value=req.get_text();
	}
}

function account_change_checkbox_web(checked) {
	if(checked) {
		document.getElementById('account_web_percent').removeAttribute('disabled');
		document.getElementById('account_web_price').removeAttribute('disabled');
	} else {
		document.getElementById('account_web_percent').setAttribute('disabled', 'disabled');
		document.getElementById('account_web_price').setAttribute('disabled', 'disabled');
	}
}

function account_set_uniform_title_fields(){
	var uniform_title_id=parseInt(document.getElementById('account_num_uniform_title').value);
	if(uniform_title_id) {
		var url = './ajax.php?module=acquisition&categ=rent&sub=get_uniform_title_fields&uniform_title_id='+uniform_title_id;
		var req = new http_request();
		req.request(url,0,'');
		uniform_title_fields=JSON.parse(req.get_text());
		
		for(var i = 0; i < uniform_title_fields.length; i++) {
			for(var j = 0; j < uniform_title_fields[i].fields.length; j++) {
				if(uniform_title_fields[i].fields[j].values[0] != undefined) {
					document.getElementById(uniform_title_fields[i].fields[j].name).value=uniform_title_fields[i].fields[j].values[0];
				} else {
					document.getElementById(uniform_title_fields[i].fields[j].name).value= '';
				}
			}
		}
	}
}

function account_maj_supplier_field() {
	var publisher_id = parseInt(document.getElementById('account_num_publisher').value);
	if(publisher_id) {
		var url = './ajax.php?module=acquisition&categ=rent&sub=get_supplier_from_publisher&publisher_id='+publisher_id;
		var req = new http_request();
		req.request(url,0,'');
		var response = JSON.parse(req.get_text());
		if(response.state) {
			document.getElementById('account_num_supplier').value = response.id_entite;
			document.getElementById('account_supplier').value = response.raison_sociale;
		}
	}
}


require(['dijit/registry', 'apps/pmb/PMBDialog'], function (registry, Dialog) {
	window.account_show_invoices_selector = function(id){
     	if(!registry.byId('account_show_invoices_selector_layer')){
        	var myDijit = new Dialog({title: msg_account_show_invoices_selector_title, executeScripts:true, id:'account_show_invoices_selector_layer', style:{width:'85%'}});
		}else{
			var myDijit = registry.byId('account_show_invoices_selector_layer');
		}
        var path = './ajax.php?module=acquisition&categ=rent&sub=show_invoices_selector&id='+id;      
        myDijit.attr('href', path);
     	myDijit.startup();
        myDijit.show();
	},	
	window.account_hide_invoices_selector = function(id){
		if(registry.byId('account_show_invoices_selector_layer')){
			var myDijit = registry.byId('account_show_invoices_selector_layer');
	        myDijit.hide();
			
		}
	}
 });

function account_add_account_in_invoice(account_id, invoice_id){
	var url = './ajax.php?module=acquisition&categ=rent&sub=add_account_in_invoice&id='+account_id+'&invoice_id='+invoice_id;
	var req = new http_request();
	req.request(url,0,'');
	result=JSON.parse(req.get_text());
	
	document.getElementById('icon_'+account_id).innerHTML=result.icon;
	account_hide_invoices_selector();
}

function account_form_hide_fields() {
	if(document.getElementById('el_account_request_types')) {
		document.getElementById('el_account_request_types').setAttribute('style', 'display:none;');
	}
	if(document.getElementById('el_account_dates')) {
		document.getElementById('el_account_dates').setAttribute('style', 'display:none;');
	} else {
		if(document.getElementById('el_account_receipt_limit_date')) {
			document.getElementById('el_account_receipt_limit_date').setAttribute('style', 'display:none;');
		}
		if(document.getElementById('el_account_receipt_effective_date')) {
			document.getElementById('el_account_receipt_effective_date').setAttribute('style', 'display:none;');
		}
		if(document.getElementById('el_account_return_date')) {
			document.getElementById('el_account_return_date').setAttribute('style', 'display:none;');
		}
	}
}

function request_form_hide_fields() {
	if(document.getElementById('el_account_types')) {
		document.getElementById('el_account_types').setAttribute('style', 'display:none;');
	}
	if(document.getElementById("account_types")) {
		document.getElementById("account_types").disabled = true;
	}
	if(document.getElementById('el_account_pricing_system')) {
		document.getElementById('el_account_pricing_system').setAttribute('style', 'display:none;');
	}
	if(document.getElementById('el_account_minutage')) {
		document.getElementById('el_account_minutage').setAttribute('style', 'display:none;');
	}
	if(document.getElementById('el_account_web_minutage')) {
		document.getElementById('el_account_web_minutage').setAttribute('style', 'display:none;');
	}
}

function account_not_editable_form_hide_fields() {
	if(document.getElementById('el_account_uniform_title')) {
		document.getElementById('el_account_uniform_title').setAttribute('style', 'display:none;');
	}
}

function tu_account_mapper_callback(field,tu_id){
	if(typeof(formMapperCallback) != 'undefined'){
		var tu_id = document.getElementById('account_num_uniform_title').value;
		formMapperCallback(tu_id);
	}
}