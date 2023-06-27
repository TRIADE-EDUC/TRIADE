/* +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: templates.js,v 1.5 2019-03-28 21:47:25 ccraig Exp $ */

if(typeof templates == "undefined"){
	templates = {
		input_completion_field : function(name, id, completion, autfield, attributes) {
			var input = document.createElement('input');
			input.setAttribute('name',name);
			input.setAttribute('data-form-name',name);
			input.setAttribute('id',id);
			input.setAttribute('type','text');
			input.className='saisie-30emr';
			input.setAttribute('value','');
			input.setAttribute('completion', completion);
			input.setAttribute('autfield', autfield);
			if(typeof attributes !== 'undefined' && attributes.length) {
				for(var i=0; i<attributes.length; i++) {
					input.setAttribute(attributes[i].name, attributes[i].value);
				}
			}
			return input;
		},
		get_input_completion_field : function(name, completion, autfield, suffixe, attributes) {
			if(document.getElementById(name+'_0')) {
				return this.input_completion_field(name+'['+suffixe+'][label]', name+'_'+suffixe,completion, autfield+'_'+suffixe, attributes);
			} else {
				return this.input_completion_field(name+suffixe, name+suffixe, completion, autfield+suffixe, attributes);
			}
		},
		delete_button_field : function(name, input_field, hidden_field) {
			var button = document.createElement('input');
			button.setAttribute('id',name);
			button.onclick=function() {
				input_field.value = '';
				hidden_field.value = '';
			};
			button.setAttribute('type','button');
			button.className='bouton_small';
			button.setAttribute('readonly','');
			button.setAttribute('value',pmbDojo.messages.getMessage('template','raz'));
			return button;
		},
		selector_button_field : function(name, selector_fonction) {
			var button = document.createElement('input');
			button.setAttribute('id',name);
			button.setAttribute('type','button');
			button.className='bouton';
			button.setAttribute('readonly','');
			button.setAttribute('value',pmbDojo.messages.getMessage('template','parcourir'));
			button.onclick=selector_fonction;
			return button;
		},
		hidden_field : function(name, id) {
			var hidden = document.createElement('input');
			hidden.setAttribute('name',name);
			hidden.setAttribute('data-form-name',name);
			hidden.setAttribute('type','hidden');
			hidden.setAttribute('id',id);
			hidden.setAttribute('value','');
			return hidden;
		},
		get_hidden_field : function(name, id, suffixe) {
			if(document.getElementById(id+'_0')) {
				return this.hidden_field(name+'['+suffixe+'][id]', id+'_'+suffixe);
			} else {
				return this.hidden_field(id+suffixe, id+suffixe);
			}
		},
		get_max_node : function(name) {
			if(document.getElementById('max_'+name)) {
				return document.getElementById('max_'+name);
			} else {
				//Hack
				if(document.getElementById('max_'+name.replace('f_', ''))) {
					return document.getElementById('max_'+name.replace('f_', ''));
				}
			}
		},
		get_add_node : function(name) {
			if(document.getElementById('add'+name)) {
				return document.getElementById('add'+name);
			} else {
				//Hack
				if(document.getElementById('add'+name.replace('f_', ''))) {
					return document.getElementById('add'+name.replace('f_', ''));
				}
			}
		},
		add_completion_field : function(name, id, completion) {
			var suffixe = this.get_max_node(name).value;
			
			var button_add_field = document.getElementById('button_add_' + name);
			
			var attributes = [];
			var node_attributes = document.getElementById('add'+name);
			if(node_attributes && node_attributes.getAttribute('data-completion-attributes')) {
				attributes = JSON.parse(node_attributes.getAttribute('data-completion-attributes'));
			}
			
			var input_completion_field = this.get_input_completion_field(name, completion, id, suffixe, attributes);
			
			var hidden_field = this.get_hidden_field(name, id, suffixe);
			
			var delete_button_field = this.delete_button_field('del_'+name+suffixe, input_completion_field, hidden_field);
			
			var div=document.createElement('div');
			div.className='row';
			div.appendChild(input_completion_field);
		    div.appendChild(document.createTextNode(' '));
		    div.appendChild(delete_button_field);
		    div.appendChild(hidden_field);
		    if (button_add_field) div.appendChild(button_add_field);

		    this.get_add_node(name).appendChild(div);
		    this.get_max_node(name).value = suffixe*1+1*1;
			ajax_pack_element(input_completion_field);
		},
		add_completion_selection_field : function(name, id, completion, selector_fonction) {
			
			var button_add_field = document.getElementById('button_add_' + id);
			
			var suffixe = this.get_max_node(name).value;
			
			var attributes = [];
			var node_attributes = document.getElementById('add'+name);
			if(node_attributes && node_attributes.getAttribute('data-completion-attributes')) {
				attributes = JSON.parse(node_attributes.getAttribute('data-completion-attributes'));
			}
			
			var input_completion_field = this.get_input_completion_field(name, completion, id, suffixe, attributes);
			
			var hidden_field = this.get_hidden_field(name, id, suffixe);
			
			var selector_button_field = this.selector_button_field('sel_'+name+suffixe, selector_fonction);
			
			var delete_button_field = this.delete_button_field('del_'+name+suffixe, input_completion_field, hidden_field);
			
			var div=document.createElement('div');
			div.className='row';
			div.appendChild(input_completion_field);
		    div.appendChild(document.createTextNode(' '));
		    div.appendChild(selector_button_field);
		    div.appendChild(document.createTextNode(' '));
		    div.appendChild(delete_button_field);
		    div.appendChild(hidden_field);
		    if (button_add_field) div.appendChild(button_add_field);

		    this.get_add_node(name).appendChild(div);
		    this.get_max_node(name).value = suffixe*1+1*1;
			ajax_pack_element(input_completion_field);
		},
		add_completion_qualified_field : function(name, id, completion, select_name) {
			var suffixe = this.get_max_node(name).value;
			
			var button_add_field = document.getElementById('button_add_' + name);
			
		    var select_field = document.getElementById(select_name+'0').cloneNode(true);	
		    select_field.setAttribute('name', select_name + suffixe);
		    select_field.setAttribute('id', select_name + suffixe);
		    
		    var input_completion_field = this.get_input_completion_field(name, completion, id, suffixe);
			
		    var hidden_field = this.get_hidden_field(name, id, suffixe);
			
			var delete_button_field = this.delete_button_field('del_'+name+suffixe, input_completion_field, hidden_field);
			
			var div=document.createElement('div');
			div.className='row';
			div.appendChild(select_field);
		    div.appendChild(document.createTextNode(' '));
			div.appendChild(input_completion_field);
		    div.appendChild(document.createTextNode(' '));
		    div.appendChild(delete_button_field);
		    div.appendChild(hidden_field);
		    if (button_add_field) div.appendChild(button_add_field);

		    this.get_add_node(name).appendChild(div);
		    this.get_max_node(name).value = suffixe*1+1*1;
			ajax_pack_element(input_completion_field);
		},
	}
}