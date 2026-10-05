// +-------------------------------------------------+
// ? 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormMapper.js,v 1.21 2022/11/18 09:52:38 rtigero Exp $

define(['dojo/_base/declare','dojo/io-query', 'dojo/request/xhr', 'dojo/_base/lang', 'dojo/query', 'dojo/dom-attr', 'dojo/dom', 'dojo/dom-construct', 'dojo/topic', 'dijit/registry'],function(declare, ioQuery, xhr, lang, query, domAttr, dom, domConstruct, topic, registry) {
	return declare(null, {
		idElement: 0,
		source: '',
		dest: '',
		url: '',
		formId: '',
		constructor: function(dest, formId) {
			this.dest = dest;
			this.formId = formId;
		    var params = ioQuery.queryToObject(decodeURIComponent(dojo.doc.location.search.slice(1)));
		    var mapperParams = {};
		    for(var key in params){
		    	if(key.split('[').length == 2 && key.split('[')[0] == "mapper"){ //Tableau de parametre mapper ? passer en plus
		    		if(!mapperParams[key.split('[')[0]]){
		    			mapperParams[key.split('[')[0]] = {};	
		    		}
		    		mapperParams[key.split('[')[0]][key.split('[')[1].replace(']', '')] = params[key];
		    	}
		    }
			if(params.source_id){
				this.source = params.source_type;
				this.idElement = params.source_id;
				this.mapperParams = mapperParams;
				
				if(this.formId){
					domConstruct.create('input', {type:'hidden', name:'mapping_source_type', value:this.source}, dom.byId(this.formId), 'first');
					domConstruct.create('input', {type:'hidden', name:'mapping_source_id', value:this.idElement}, dom.byId(this.formId), 'first');
				}
				this.getMapping();
			}
			if(params.parent_record){
				this.dest = 'child_record';
				this.idElement = params.parent_record;
				this.getMapping();
			}
		},
		getMapping:function(){
			//TODO: Xhr request on pt d'entree
			switch(this.dest){
				case 'notice':
					var url = './ajax.php?module=catalog&categ=fill_form&sub=notice&quoi='+this.source+'&id='+this.idElement;
					break;
				case 'child_record': //Cas d'une notice fille
					var url = './ajax.php?module=catalog&categ=fill_form&sub=child_record&quoi=notice&id='+this.idElement;
					break;
				default:
					var url = './ajax.php?module=autorites&categ=fill_form&sub='+this.dest+'&quoi='+this.source+'&id='+this.idElement;
					break;
			}
			if(url != ''){
				xhr.post(url, {
					handleAs: "json",
					data: {mapperParams:JSON.stringify(this.mapperParams)},
				}).then(lang.hitch(this, this.treatDatas));
			}
		},
		treatDatas: function(datas){
			if(datas){
				this.datas = datas;
				this.mapForm();
			}else{
				
			}
		},
		mapForm: function(){
			for(var i=0 ; i<this.datas.length ; i++){
				switch(this.datas[i].mainType){
				case 'concept':
					this.treatConcept(this.datas[i]);
					break;
				case 'exotics_data':
					topic.publish('FormMapper/exotics_data', this.datas[i]);
					break;
				default: 
					this.treatFields(this.datas[i]);
					break;
				}
			}
			topic.publish('FormMapper/EndOfMapping', {});
		},

		purgeConcept: function(baseFieldId, baseFieldIdValue,baseFieldIdType){
			var i = 0;
			while(dom.byId(baseFieldId[0]+i+baseFieldId[1])){
				domAttr.set(dom.byId(baseFieldId[0]+i+baseFieldId[1]), 'value', '');
				domAttr.set(dom.byId(baseFieldIdValue[0]+i+baseFieldIdValue[1]), 'value', '');
				domAttr.set(dom.byId(baseFieldIdType[0]+i+baseFieldIdType[1]), 'value', '');
				i++;
			}
		},
		purgeCheckbox:function(id, multiple){
			if(multiple == 'true'){
				var i = 0;
				while(dom.byId(id+i)){
					domAttr.set(dom.byId(id+i), 'checked', false);
					i++;
				}
			}else{
				domAttr.set(dom.byId(id), 'checked', false);
			}
		},
		selectorCallback:function(sourceType, elementId){
			if(confirm(pmbDojo.messages.getMessage('catalog','form_mapper_confirm_load_tu'))){
				//Gestion du cas ou on n'a pas l'id de l'element mais l'id du champ de formulaire
				var id = 0;
				if(! parseInt(elementId)) {
					let field = document.getElementById(elementId);
					let autfield = field.getAttribute('autfield');
					if(autfield){
						let codeField = document.getElementById(autfield);
						id = codeField.value;
					}
				} else {
					id = elementId;
				}
				this.source = sourceType;
				this.idElement = id;
				this.getMapping();	
			}
		},
		treatFields: function(fieldData){
			var jsCallback = fieldData.jscallback;
			for(var i=0 ; i<fieldData.fields.length ; i++){
				var currentFieldName = fieldData.fields[i].name;
				var domEltField = query(fieldData.fields[i].type+'[data-form-name="'+currentFieldName+'"]')[0];
				var fieldId = domAttr.get(domEltField, 'id');
				if (fieldId.endsWith('_display_label')){
					fieldId = fieldId.substr(0,fieldId.length-14);	
				}
				if(fieldData.multiple != 'true'){
					this.setValue(fieldData.fields[i].type, fieldData.fields[i].values[0], fieldId, fieldData.fields[i].subtype);
					//this[fieldData.fields[i].type+'Purge'](fieldId, false);
				}else{
					fieldId = fieldId.substr(0, fieldId.length-1);
					if((typeof this[fieldData.fields[i].type+'Purge'] == 'function') && (fieldData.fields[i].values.length)){
						this[fieldData.fields[i].type+'Purge'](fieldId, true);
					}
						
					var params=new Array();
					if(fieldData.callbackParams){
						params=fieldData.callbackParams;
					}
					for(var j=0 ; j<fieldData.fields[i].values.length ; j++){
						if(!dom.byId(fieldId+j)){
							window[fieldData.jscallback].apply(window,params);
						}
						this.setValue(fieldData.fields[i].type, fieldData.fields[i].values[j], fieldId+j, fieldData.fields[i].subtype);
					}	
				}
			}
		},
		treatConcept: function(fieldData){
			var jsCallback = fieldData.jscallback;
			for(var i=0 ; i<fieldData.fields.length ; i++){
				var currentFieldName = fieldData.fields[i].name;
				var domEltField = query(fieldData.fields[i].type+'[data-form-name="'+currentFieldName+'"]')[0];
				var fieldId = domAttr.get(domEltField, 'id');
				fieldId = fieldId.split('0');
				var params=new Array();
				if(fieldData.callbackParams){
					params=fieldData.callbackParams;
				}
				if((typeof this[fieldData.fields[i].type+'Purge'] == 'function') && (fieldData.fields[i].values.length)){
					this[fieldData.fields[i].type+'Purge'](fieldId, true);
				}
				for(var j=0 ; j<fieldData.fields[i].values.length ; j++){
					if(!dom.byId(fieldId[0]+j+fieldId[1])){
						window[fieldData.jscallback].apply(window,params);
					}
					this.setValue(fieldData.fields[i].type, fieldData.fields[i].values[j], fieldId[0]+j+fieldId[1]);
				}	
			}
		},
		setValue: function(type, value, id, subtype){
			if(value){
				switch(type){
					case 'input':
					case 'textarea':
						this.setInputValue(value, id, subtype);
						break;
					case 'select':
						this.setSelectorValue(value, id);
						break;
					case 'checkbox':
						this.setCheckboxValue(value, id);
						break;
				}
				this.triggerEvent(dom.byId(id));
			}
		},
		setInputValue: function(value, id, subtype){
			var eltToEdit = dom.byId(id);
			if(!subtype){
				if(registry.byId(id)){
					//DATEPICKER
					var widget = registry.byId(id);
					widget.set('value',value[0]);
				}else{
					var eltToEditDisplayLabel = dom.byId(id + "_display_label");                    
                    if (eltToEditDisplayLabel) {
                        domAttr.set(eltToEditDisplayLabel, 'value', value);
                    } else if (eltToEdit) {
                        domAttr.set(eltToEdit, 'value', value);    
                    }
				}
			}else{
				switch(subtype){ //A voir pour ajouter radiobutton ?
					case 'checkbox':
						if(value==1){
							domAttr.set(eltToEdit, 'checked', true);
						}else{
							domAttr.set(eltToEdit, 'checked', false);
						}
						break;
				}
			}
			
		},
		setSelectorValue: function(value, id){
			var selectToEdit = dom.byId(id);
			var selectOptions = selectToEdit.options;
			if(value){
				for(var i=0; i<selectOptions.length; i++){
					if(selectOptions[i].value == value){
						selectToEdit.selectedIndex = i;
						domAttr.set(selectOptions[i],'selected',true);
					}else{
						domAttr.set(selectOptions[i],'selected',false);
					}
				}	
			}
		},
		inputPurge: function(id, multiple){
			this.commonInputPurge(id, multiple);
		},
		textareaPurge: function(id, multiple){
			this.commonInputPurge(id, multiple);
		},
		commonInputPurge: function(id, multiple){
			if(multiple == true){
				var i = 0;
				if(typeof id == 'object'){ // Cas d'un array composant les deux parties de l'id (surtout pour les concepts)
					while(dom.byId(id[0]+i+id[1])){
						domAttr.set(dom.byId(id[0]+i+id[1]), 'value', '');
						i++;
					}
				}else{
					while(dom.byId(id+i)){
						domAttr.set(dom.byId(id+i), 'value', '');
						i++;
					}
				}
			}else{
				domAttr.set(dom.byId(id), 'value', '');
			}
		},
		selectPurge: function(id, multiple){
			if(multiple == true){
				var k = 0;
				while(dom.byId(id+k)){
					var selectToEdit = dom.byId(id+k);
					var selectOptions = selectToEdit.options;
					for(var i=0; i<selectOptions.length; i++){
						domAttr.set(selectOptions[i],'selected',false);
					}
					k++;
				}
			}else{
				var selectToEdit = dom.byId(id);
				var selectOptions = selectToEdit.options;
				for(var i=0; i<selectOptions.length; i++){
					domAttr.set(selectOptions[i],'selected',false);
				}
			}
		},
		triggerEvent: function(element){
			var evt = document.createEvent("HTMLEvents");
			evt.initEvent("change", false, true);
			if (element) {
				element.dispatchEvent(evt);	
			}
		}
	});
});