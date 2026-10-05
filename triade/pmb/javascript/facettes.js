// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facettes.js,v 1.1.2.3.2.5 2025/03/28 10:46:04 dgoron Exp $

function facettes_test_table(elmt_list) {
	if(elmt_list.className.includes('facette_expande')){
        elmt_list.setAttribute('class', 'facette_collapsed');
        elmt_list.querySelector('*[aria-expanded]').setAttribute('aria-expanded', 'false');
    } else {
        elmt_list.setAttribute('class', 'facette_expande');
        elmt_list.querySelector('*[aria-expanded]').setAttribute('aria-expanded', 'true');
    }
    var elmt_list_rows = elmt_list.querySelectorAll('tbody[id^=\'facette_body\'] tr');
	for(i in elmt_list_rows){
		if(elmt_list_rows[i].firstElementChild && elmt_list_rows[i].firstElementChild.nodeName!='TH'){
			if(elmt_list_rows[i].style.display == 'none'){
				elmt_list_rows[i].style.display = 'block';
                elmt_list_rows[i].setAttribute('class', 'facette_tr');
			}else{
				elmt_list_rows[i].style.display = 'none';
                elmt_list_rows[i].setAttribute('class', 'facette_tr_hidden uk-hidden');
			}
		}
	}
}

function facettes_test_fieldset(elmt_list) {
	if(elmt_list.className.includes('facette_expande')){
		elmt_list.setAttribute('class', 'facette_fieldset facette_collapsed');
        elmt_list.querySelector('*[aria-expanded]').setAttribute('aria-expanded', 'false');
    } else {
		elmt_list.setAttribute('class', 'facette_fieldset facette_expande');
        elmt_list.querySelector('*[aria-expanded]').setAttribute('aria-expanded', 'true');
    }
	var childs = elmt_list.querySelectorAll('ul[id^=\'facette_body\'] .facette_tr');
    if(childs) {
    	var nb_childs = childs.length;
    	if(nb_childs) {
			for(var i = 0; i < nb_childs; i++){
				if (childs[i].style.display == 'block') {
    				childs[i].style.display = 'none';
    				childs[i].setAttribute('data-facette-expanded','false');
    			} else {
    				childs[i].style.display = 'block';
    				childs[i].setAttribute('data-facette-expanded','true');
    			}
			}
    	}
	}
}

function test(elmt_id){
	var elmt_list=document.getElementById(elmt_id);
	if (facettes_display_fieldsets) {
		facettes_test_fieldset(elmt_list);
	} else {
		facettes_test_table(elmt_list);
	}
}

function facettes_add_searchform(datas) {
	var input_form_values = document.createElement('input');
	input_form_values.setAttribute('type', 'hidden');
	input_form_values.setAttribute('name', 'check_facette[]');
	input_form_values.setAttribute('value', datas);
	document.forms[facettes_hidden_form_name].appendChild(input_form_values);
}

function facettes_get_params_default_facettes(default_facettes) {
	var params = '&param_default_facette=1';
	for (i=0, n=default_facettes.length; i<n; i++){
		if(facettes_get_mode() == 'filter') {
            params += '&check_facette[]='+JSON.stringify(default_facettes[i]);
        }
	}
	return params;
}

function facettes_get_params_checked_facettes() {
	var params = '';
	var form = document.facettes_multi;
	for (i=0, n=form.elements.length; i<n; i++){
		if (form.elements[i].checked == true) {
			if(facettes_get_mode() == 'filter') {
                params += '&check_facette[]='+form.elements[i].value;
            } else {
                //copie le noeud vers search_form
				facettes_add_searchform(form.elements[i].value);
            }
		}
	}
	return params;
}

function valid_facettes_multi(default_facettes){
    var facettes_checked = new Array();
    if(default_facettes && default_facettes.length) {
    	var params = facettes_get_params_default_facettes(default_facettes);
    } else {
    	var params = facettes_get_params_checked_facettes();
    }
    //on bloque si aucune case cochée
    if (params) {
		if(document.getElementById('filtre_compare_facette')) {
			document.getElementById('filtre_compare_facette').value='filter';
		}
		if(document.getElementById('filtre_compare_form_values')) {
			document.getElementById('filtre_compare_form_values').value='filter';
		}
        if(facettes_get_mode() == 'filter') {
            var req = new http_request();
            req.request(facettes_ajax_filters_get_elements_url, true, params, true, function(response){
        		let content = JSON.parse(response);
        		document.getElementById('results_list').innerHTML=content.elements_list_ui;
        		if(document.getElementById('results_pager')) {
        			document.getElementById('results_pager').innerHTML=content.pager;
        		}
                facettes_refresh();
        	});
        } else {
        	document.forms[facettes_hidden_form_name].page.value = 0;
        	document.forms[facettes_hidden_form_name].submit();
        }
		return true;
	} else {
		return false;
	}
}

function facette_see_more(id,json_facette_plus){
	const usingModal = parseInt(facettes_modal_activate);
	var myList = document.getElementById('facette_list_'+id);
	
	if (json_facette_plus == null) {
		if (usingModal) {
            if (typeof openModal == 'function') {
                return openModal(id);
            } else {
                console.error('[facettes_modal] : openModal is not a function !')
                return false;
            }
        }
		if (facettes_display_fieldsets) {
            var childs = myList.querySelectorAll('ul[id^=\'facette_body\'] .facette_tr');
        } else {
            var childs = myList.querySelectorAll('tbody[id^=\'facette_body\'] .facette_tr');
        }
		var nb_childs = childs.length;
		for(var i = 0; i < nb_childs; i++){
			if (childs[i].getAttribute('data-facette-ajax-loaded')!=null) {
				if (childs[i].getAttribute('style')=='display:block') {
					childs[i].setAttribute('style','display:none');
					childs[i].setAttribute('data-facette-expanded','false');
				} else {
					childs[i].setAttribute('style','display:block');
					childs[i].setAttribute('data-facette-expanded','true');
				}
			}
		}
		
		var see_more_less = document.getElementById('facette_see_more_less_'+id);
		see_more_less.innerHTML='';
		var span = document.createElement('span');
		if (see_more_less.getAttribute('data-etat')=='plus') {
			span.className='facette_moins_link';
			span.innerHTML=pmbDojo.messages.getMessage('facettes', 'facette_moins_link');
			see_more_less.setAttribute('data-etat','moins');
			see_more_less.setAttribute('aria-label', pmbDojo.messages.getMessage('facettes', 'facette_moins_label'));
		} else {
			span.className='facette_plus_link';
			span.innerHTML=pmbDojo.messages.getMessage('facettes', 'facette_plus_link');
			see_more_less.setAttribute('data-etat','plus');
			see_more_less.setAttribute('aria-label', pmbDojo.messages.getMessage('facettes', 'facette_plus_label'));
		}
		see_more_less.appendChild(span);
	} else {
		var req = new http_request();
		var sended_datas={'json_facette_plus':json_facette_plus};
		req.request(facettes_ajax_see_more_url,true,'sended_datas='+encodeURIComponent(JSON.stringify(sended_datas)),true,function(response){
			if (usingModal) {
	            if (typeof callback_see_more_modal == 'function') {
	                callback_see_more_modal(id, myList, response)
	            } else {
	                console.error('[facettes_modal] : callback_see_more_modal is not a function !')
	            }
	        } else {
	        	callback_see_more(id, myList, response);
	        }
		});
	}
}

function facettes_get_see_more_selection_node(myLine) {
	var uniqueIdInput = Math.random().toString(20).slice(2, 15);
    var spanCheckbox = document.createElement('span');
    spanCheckbox.setAttribute('class','facette_coche');
    spanCheckbox.innerHTML = "<input id='facette-" + myLine['facette_code_champ'] + "-" + uniqueIdInput + "' type='checkbox' name='check_facette[]' value='" + myLine['facette_value'] + "'></span>";

    var labelCheckbox = document.createElement('label');
    spanCheckbox.prepend(labelCheckbox);
    labelCheckbox.setAttribute('for','facette-' + myLine['facette_code_champ'] + '-' + uniqueIdInput );
    labelCheckbox.setAttribute('class', 'visually-hidden');
    labelCheckbox.innerHTML = myLine['facette_libelle'];
    return spanCheckbox;
}

function facettes_get_see_more_label_node(myLine) {
	var aonclick = document.createElement('a');
	aonclick.setAttribute('style', 'cursor:pointer;');
	aonclick.setAttribute('rel', 'nofollow');
	aonclick.setAttribute('class', 'facet-link');
    if (myLine['facette_link']) {
		aonclick.setAttribute('onclick', myLine['facette_link']);
    } else {
        //Evt vers SearchSegmentController pour l'initialisation du clic
        require(['dojo/topic'], function(topic){
			topic.publish('FacettesRoot', 'FacettesRoot', 'initFacetLink', {elem: aonclick});
		});
    }
	var span_facette_link = aonclick.appendChild(document.createElement('span'));
	span_facette_link.setAttribute('class', 'facette_libelle');
	span_facette_link.innerHTML = myLine['facette_libelle'];
	aonclick.appendChild(document.createTextNode(' '));
	var span_facette_number = aonclick.appendChild(document.createElement('span'));
	span_facette_number.setAttribute('class', 'facette_number');
	span_facette_number.innerHTML = "[" + myLine['facette_number'] + "]";
	return aonclick;
}

function callback_see_more_table(id, myList, data) {
	var jsonArray = JSON.parse(data);
	var facetteList = myList.querySelector('tbody[id^=\'facette_body\']');
	//on supprime la ligne '+'
	facetteList.removeChild(myList.rows[myList.rows.length-1]);
	//on ajoute les lignes au tableau
	for(var i=0;i<jsonArray.length;i++) {
		var tr = document.createElement('tr');
		tr.setAttribute('style','display:block');
		tr.setAttribute('class', 'facette_tr');
		tr.setAttribute('data-facette-expanded','true');
		tr.setAttribute('data-facette-ajax-loaded','1');
    	var td = tr.appendChild(document.createElement('td'));
		td.setAttribute('class','facette_col_coche');
        td.appendChild(facettes_get_see_more_selection_node(jsonArray[i]));

    	var td2 = tr.appendChild(document.createElement('td'));
		td2.setAttribute('class','facette_col_info');
        td2.appendChild(facettes_get_see_more_label_node(jsonArray[i]));

		facetteList.appendChild(tr);
	}
}

function callback_see_more_fieldset(id, myList, data) {
	var jsonArray = JSON.parse(data);
    var facetteList = myList.querySelector('ul[id^=\'facette_body\']');
    //on supprime la ligne '+'
    facetteList.removeChild(myList.querySelectorAll('li')[myList.querySelectorAll('li').length-1]);
	//on ajoute les lignes au tableau
	for(var i=0;i<jsonArray.length;i++) {
		var li = document.createElement('li');
		li.setAttribute('style','display:block');
		li.setAttribute('class', 'facette_tr');
		li.setAttribute('data-facette-expanded','true');
		li.setAttribute('data-facette-ajax-loaded','1');
        li.appendChild(facettes_get_see_more_selection_node(jsonArray[i]));
        li.appendChild(facettes_get_see_more_label_node(jsonArray[i]));
    	facetteList.appendChild(li);
    }
}

function callback_see_more(id, myList, data) {
    if (facettes_display_fieldsets) {
        callback_see_more_fieldset(id, myList, data);
    } else {
        callback_see_more_table(id, myList, data)
    }
    facettes_add_see_less(myList, id);
}

function facettes_get_see_less_button_node(id) {
    if(facettes_display_fieldsets) {
        var button_node = document.createElement('button');
        button_node.setAttribute('type','button');
        button_node.setAttribute('class','button-unstylized');
    } else {
        var button_node = document.createElement('a');
        button_node.setAttribute('role','button');
        button_node.setAttribute('href','#');
    }
    button_node.setAttribute('id','facette_see_more_less_'+id);
    button_node.setAttribute('data-etat','moins');
    button_node.setAttribute('onclick','javascript:facette_see_more(' + id + ',null); return false;');
    button_node.setAttribute('style','cursor:pointer');
    button_node.setAttribute('aria-label', pmbDojo.messages.getMessage('facettes', 'facette_moins_label'));
    button_node.innerHTML='';
    return button_node;
}

function facettes_get_see_less_span_node() {
    var span = document.createElement('span');
    span.className='facette_moins_link';
    if (parseInt(facettes_modal_activate)) {
    	span.innerHTML=pmbDojo.messages.getMessage('facettes', 'facette_plus_link');
    } else {
    	span.innerHTML=pmbDojo.messages.getMessage('facettes', 'facette_moins_link');
    }
    return span;
}

function facettes_add_see_less_table(myList, id) {
    //Ajout du see_less
	var tr = document.createElement('tr');
	tr.setAttribute('style','display:block');
	tr.setAttribute('data-see-less','1');
	tr.setAttribute('class','facette_tr_see_more');

	var td = tr.appendChild(document.createElement('td'));
	td.setAttribute('colspan','3');

    var button_node = facettes_get_see_less_button_node(id);
    td.appendChild(button_node);
    var span_node = facettes_get_see_less_span_node();
    button_node.appendChild(span_node);

    var facetteList = myList.querySelector('tbody[id^=\'facette_body\']');
    facetteList.appendChild(tr);
}

function facettes_add_see_less_fieldset(myList, id) {
    //Ajout du see_less
	var li = document.createElement('li');
	li.setAttribute('style','display:block');
	li.setAttribute('data-see-less','1');
	li.setAttribute('class','facette_see_more');

    var button_node = facettes_get_see_less_button_node(id);
    li.appendChild(button_node);
    var span_node = facettes_get_see_less_span_node();
    button_node.appendChild(span_node);

    var facetteList = myList.querySelector('ul[id^=\'facette_body\']');
    facetteList.appendChild(li);
}

function facettes_add_see_less(myList, id) {
    if (facettes_display_fieldsets) {
        facettes_add_see_less_fieldset(myList, id);
    } else {
        facettes_add_see_less_table(myList, id)
    }
}

function facettes_set_selection(num_facettes_set) {
    facettes_refresh(num_facettes_set);
}

function facettes_valid_facette(datas, page){
    if(facettes_get_mode() == 'filter') {
    	var params = '';
    	if(datas) {
    		params += '&check_facette[]='+JSON.stringify(datas);
    	}
    	page = parseInt(page);
    	if(page) {
    		params += '&page='+page;
    	}
    	var req = new http_request();
    	req.request(facettes_ajax_filters_get_elements_url, true, params, true, function(response){
    		let content = JSON.parse(response);
    		document.getElementById('results_list').innerHTML=content.elements_list_ui;
    		if(document.getElementById('results_pager')) {
    			document.getElementById('results_pager').innerHTML=content.pager;
    		}
    		if (datas) {
    			facettes_refresh();
    		}
    	});
    } else {
		facettes_add_searchform(JSON.stringify(datas));
		document.forms[facettes_hidden_form_name].page.value = 0;
		document.forms[facettes_hidden_form_name].submit();
    }
	return true;
}

function facettes_reinit(is_external=false) {
	if(facettes_get_mode() == 'filter') {
		if(is_external) {
			var params = '&reinit_facettes_external=1';
		} else {
			var params = '&reinit_facettes=1';
		}
    	var req = new http_request();
    	req.request(facettes_ajax_filters_get_elements_url, true, params, true, function(response){
    		let content = JSON.parse(response);
    		document.getElementById('results_list').innerHTML=content.elements_list_ui;
    		if(document.getElementById('results_pager')) {
    			document.getElementById('results_pager').innerHTML=content.pager;
    		}
            facettes_refresh();
    	});
    } else {
        var input_form_values = document.createElement('input');
		input_form_values.setAttribute('type', 'hidden');
		if(is_external) {
			input_form_values.setAttribute('name', 'reinit_facettes_external');
		} else {
			input_form_values.setAttribute('name', 'reinit_facettes');
		}
		input_form_values.setAttribute('value', '1');
		document.forms[facettes_hidden_form_name].appendChild(input_form_values);
		document.forms[facettes_hidden_form_name].page.value = 0;
		document.forms[facettes_hidden_form_name].submit();
    }
	return true;
}

function facettes_external_reinit() {
	return facettes_reinit(1);
}

function facettes_delete_facette(indice) {
	if(facettes_get_mode() == 'filter') {
        var params = '&param_delete_facette='+indice;
    	var req = new http_request();
    	req.request(facettes_ajax_filters_get_elements_url, true, params, true, function(response){
    		let content = JSON.parse(response);
    		document.getElementById('results_list').innerHTML=content.elements_list_ui;
    		if(document.getElementById('results_pager')) {
    			document.getElementById('results_pager').innerHTML=content.pager;
    		}
            facettes_refresh();
    	});
    } else {
        var input_form_values = document.createElement('input');
		input_form_values.setAttribute('type', 'hidden');
		input_form_values.setAttribute('name', 'param_delete_facette');
		input_form_values.setAttribute('value', indice);
		document.forms[facettes_hidden_form_name].appendChild(input_form_values);
		document.forms[facettes_hidden_form_name].submit();
    }
	return true;
}

function facettes_reinit_compare() {
	var input_form_values = document.createElement('input');
	input_form_values.setAttribute('type', 'hidden');
	input_form_values.setAttribute('name', 'reinit_compare');
	input_form_values.setAttribute('value', '1');
	document.forms[facettes_hidden_form_name].appendChild(input_form_values);
	document.forms[facettes_hidden_form_name].submit();
	return true;
}

function facettes_refresh(num_facettes_set) {
    var req = new http_request();
    var url = facettes_ajax_filtered_data_url;
    if(typeof(num_facettes_set) != 'undefined') {
        url += '&num_facettes_set='+num_facettes_set;
    }
	req.request(url,true,null,true,function(data){
		var response = JSON.parse(data);
		document.getElementById('facette_wrapper').innerHTML=response.display;
		document.getElementById('results_list').classList.add('has_facettes');
	});
}

function facettes_change_buttons(css_class) {
	var container = document.getElementById('facette_wrapper');
	if (container) {
		var button = container.querySelector('button[class=\''+css_class+'\']');
		if (button) {
			button.setAttribute('disabled', true);
		}
		if (css_class == 'define-default-facettes-link') {
			var other_css_class = 'delete-default-facettes-link';
		} else {
			var other_css_class = 'define-default-facettes-link';
		}
		var other_button = container.querySelector('button[class=\''+other_css_class+'\']');
		if (other_button) {
			other_button.removeAttribute('disabled');
		}
	}
}

function facettes_default_values(action) {
	var params = '&action='+action;
	var req = new http_request();
	req.request(facettes_ajax_session_default_values_url, true, params, true, function(data){
		require(["dojo/topic"], function(topic){
			topic.publish("dGrowl", pmbDojo.messages.getMessage('common', 'success_save'), {});
		});
	});
	facettes_refresh();
	setTimeout(() => {
		facettes_change_buttons(action+'-default-facettes-link');
	}, 500);
	return false;
}

function facettes_define_default() {
	return facettes_default_values('define');
	
}

function facettes_delete_default() {
	return facettes_default_values('delete');
}