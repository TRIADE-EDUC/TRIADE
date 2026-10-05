// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DraftManager.js,v 1.17 2022/05/24 13:25:48 qvarin Exp $

define([
        'dojo/_base/declare',
        'dojo/dom',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/query',
        'dijit/registry',
        'dojo/io-query',
        'dojo/dom-form',
        'dojo/topic',
        'dojo/request/xhr',
        'dojo/dom-attr',
        "dojo/dom-construct",
], function(declare, dom, on, lang, query, registry, ioQuery, domForm, topic, xhr, domAttr, domConstruct){
	return declare(null, {
		manualSave : false,
		auto_save_param : 0,
		constructor: function(auto_save_draft) {
			topic.subscribe('SaveDraft', lang.hitch(this, this.handleEvents))
			this.auto_save_param = auto_save_draft ? 1 : 0;
			if (this.auto_save_param){
				this.autoSave();
			}
		},
		handleEvents: function(evtType,evtArgs){
			switch(evtType){
				case 'save':
					this.manualSave = true; 
					this.saveDraft(evtArgs.subForm);
					break;
			}
		},
		saveDraft : async function(subForm = false) {
			var formList = this.getFormList();
			if (this.manualSave){
				this.disabledSaveDraft(formList[formList.length-1]);
			}
			for (var i = 0; i < formList.length; i++) {
				var form = formList[i];
				
				// Vignette
				var thumbnailFiles = query('input[type="file"][name*="thumbnail"]', form);
				if (thumbnailFiles.length) {
					var requiredUploadDirectory = false;
					for (var j = 0; j < thumbnailFiles.length; j++) {
						var inputFile = thumbnailFiles[j]
						if (inputFile.files.length > 0) {
							requiredUploadDirectory = true;
							break;
						}
					}
					
					if (requiredUploadDirectory) {
						var prefix = form.form_id.value;
						var uploadDirectory = query('input[id='+prefix+'_thumbnail_0_data', form);
						
						if (!uploadDirectory || !uploadDirectory[0] || uploadDirectory[0].value == "") {
							this.errorAlert(pmbDojo.messages.getMessage('contribution', 'contribution_area_save_docnum_error'));
							//On reactive le bouton enregistrer
							this.enabledSaveDraft(form);
							return false;
						}
					}
				}
				
				
				// Document numérique
				var inputFiles = query('input[type="file"][name*="docnum_file"]', form);
				if (inputFiles.length) {
					var requiredUploadDirectory = false;
					for (var j = 0; j < inputFiles.length; j++) {
						var inputFile = inputFiles[j]
						if (inputFile.files.length > 0) {
							requiredUploadDirectory = true;
							break;
						}
					}
					
					if (requiredUploadDirectory) {
						var prefix = form.form_id.value;
						var uploadDirectory = query('input[id='+prefix+'_upload_directory_0_value', form);
						
						if (!uploadDirectory || !uploadDirectory[0] || uploadDirectory[0].value == "") {
							this.errorAlert(pmbDojo.messages.getMessage('contribution', 'contribution_area_save_docnum_error'));
							//On reactive le bouton enregistrer
							this.enabledSaveDraft(form);
							return false;
						}
					}
				}
				
				topic.publish("contribution/submit", "draft");
				
				var data = this.prepareData(form);
				await xhr.post('./ajax.php?module=ajax&categ=contribution&action=save_draft', {
					data: data,
					handleAs: 'json',
				}).then(lang.hitch(this, function(response) {
					if (response.session_expired) {
						window.location.replace("./empr.php?tab=contribution_area&lvl=contribution_area_list_draft");
					} else {
						//lorsque l'on passe dans le dernier noeud
						if (i == formList.length-1){
							//on desactive le mecanisme d'alerte lors du changement de page 
							unload_off();
							if (this.manualSave){
								this.enabledSaveDraft(formList[formList.length-1]);
								topic.publish("dGrowl", pmbDojo.messages.getMessage('contribution', 'contribution_save_done'), {'sticky' : false, 'duration' : 5000, 'channel' : 'info'});
							}
							this.updateTime(response.date,response.time);
							var parent = form.parentNode;
							var widget = registry.byId(domAttr.get(parent, 'widgetid'))

							if (subForm == 1){
							    topic.publish('Contribution', 'savedSubDraftForm', {widgetId: widget.parameters.tabId, response: response});
							} else if (this.manualSave == false && formList.length > 1){
							    topic.publish('Contribution', 'savedSubDraftForm', {widgetId: widget.parameters.tabId, response: response, autoSave: true});
							}
						}
						if (inputFiles) {
							this.docnumSaved(inputFiles)
						}
						if (thumbnailFiles) {
							this.thumbnailSaved(thumbnailFiles, response.thumbnail_name, response.thumbnail_data)
						}
						this.replaceId(form, response.id);
					}
				}));
			}
		},
		updateTime : function(date,time){
			var lastSave = dom.byId('last_save');
			if (lastSave) {
				var message = pmbDojo.messages.getMessage('contribution', 'contribution_save_button_draft_time');
				message = message.replace('%d',date);
				message = message.replace('%h',time);
				lastSave.innerHTML = message;
			}
		},
		autoSave : function (){
			if (this.auto_save_param) {
				setInterval(lang.hitch(this,function(){
					this.manualSave = false;
					this.saveDraft();
				}), 30000);
			}
		},
		getFormList: function() {
			var formContainer = registry.byId("ContributionFormContainer");
			var childrens = formContainer.getChildren();
			var formList = [];
			for ( var index in childrens ) {
				var children = childrens[index];
				var input = query('input[name="form_id"]', children.containerNode);
				if (input[0]) {
					var form = dom.byId(input[0].value);
					if (form) {
						formList.push(form);
					}
				}
			}
			return formList;
		},
		replaceId : function (form, id){
			var action = form.action;
			var matchs = /\bid\b=(\d+)&?/g.exec(action);
			if (matchs) {
				var new_id = "id="+id+"&";
				if (matchs[0] != new_id) {
					form.action = action.replace(matchs[0], new_id);
				}
			}
		},
		errorAlert: function(error_message) {
			if (error_message) {
				var div = document.createElement('div');
				div.innerHTML = error_message;
				if (div.firstChild) {
					error_message = div.firstChild.nodeValue;
					alert(error_message);
				}
			}
		},
		prepareData: function(form) {
			/**
			 * Ne pas mettre le form dans le contructor
			 * ça prend pas en comptes les champs disabled.
			 */
			var data = new FormData();
			var elements = query("input, select, textarea", form);
			for( var i = 0; i < elements.length; ++i ) {
				var element = elements[i];
				var name = element.name;
				var value = element.value;
				if( name ) {
					if (element.nodeName == "INPUT" && element.type == "button") {
						continue;
					} else if (element.nodeName == "INPUT" && element.type == "file") {
						var idDefaultValue = element.id.replace("_value", "_default_value");
						var inputHidden = dom.byId(idDefaultValue);
						var file = element.files.item(0);
						if ((!file) || (file && inputHidden.value != "" && inputHidden.value == file.name) ) {
							data.append(name, "");
						} else {
							data.append(name, element.files.item(0));
						}
					} else if (element.nodeName == "SELECT" && element.multiple) {
						for (var option of element.selectedOptions) {
							data.append(name, option.value);
						}
					} else if (element.nodeName == "INPUT" && ["checkbox", "radio"].includes(element.type)) {
						if (element.checked) {
							data.append(name, value);
						}
					} else {
						data.append(name, value);
					}
				}
			}
			
			var params = ioQuery.queryToObject(form.action.substring(form.action.indexOf("?") + 1));
			params.action = "save_draft";
			params.is_draft = true;
			
			for ( var name in params) {
				data.append(name, params[name]);
			}
			return data;
		},
		disabledSaveDraft: function(node) {
			let button_save = query('INPUT[id="save_button_draft"]', node);
			if (button_save[0]){
				button_save[0].className= "bouton disabled";
			}
			//Ajout du message + cursoir wait
			var lastSave = dom.byId('last_save');
			if (lastSave) {
				lastSave.className += "contribution_save_in_progress"
				lastSave.innerHTML = pmbDojo.messages.getMessage('contribution', 'save_in_progress');
			}
			document.body.style.cursor = "wait";
		},
		enabledSaveDraft: function(node) {
			let button_save = query('INPUT[id="save_button_draft"]', node);
			if (button_save[0]){
				button_save[0].className= "bouton";
			}
			//Suppression du message + cursoir wait
			var lastSave = dom.byId('last_save');
			if (lastSave) {
				lastSave.className = "";
			}
			document.body.style.cursor = "";
		},
		docnumSaved: function(inputFiles) {
			for (let input of inputFiles) {
				var file = input.files.item(0)
				if (file) {
					var message = pmbDojo.messages.getMessage('contribution', 'onto_contribution_last_file');
					message = message.replace('%s', "<em>"+file.name+"<em>");
					
					var label = dom.byId(input.id+"_label");
					if (label) {
						label.innerHTML = message;
					} else {
						var label = domConstruct.create("label", {
							id: input.id+"_label",
							innerHTML : message
						});
						domConstruct.place(label, input.parentNode, "first");
					}
					
					var idDefaultValue = input.id.replace("_value", "_default_value");
					var inputHidden = dom.byId(idDefaultValue);
					inputHidden.value = file.name
					input.value = "";
				}
			}
		},
		thumbnailSaved: function(thumbnailInput, thumbnailName, thumbnailData) {
			if (thumbnailName && thumbnailData) {
				for (let input of thumbnailInput) {
					var message = pmbDojo.messages.getMessage('contribution', 'onto_contribution_last_file');
					message = message.replace('%s', "<em>"+thumbnailName+"<em>");
					var file = input.files.item(0)
					if (file) {
						var label = dom.byId(input.id+"_label");
						if (label) {
							label.innerHTML = message;
						} else {
							var label = domConstruct.create("label", {
								id: input.id+"_label",
								innerHTML : message
							});
							domConstruct.place(label, input.parentNode, "first");
						}
						
						var idDefaultValue = input.id.replace("_value", "_default_value");
						var idData = input.id.replace("_value", "_data");
						var inputHiddenDefaultValue = dom.byId(idDefaultValue);
						var inputHiddenData = dom.byId(idData);
						inputHiddenDefaultValue.value = thumbnailName
						inputHiddenData.value = thumbnailData;
						input.value = "";
					}
				}
			}
		},
	})
});