// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Contribution.js,v 1.30.4.1 2025/02/03 13:38:09 rtigero Exp $

define([
        'dojo/_base/declare',
        'dojo/dom',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/io-query',
        'dojo/topic',
        'dojo/query!css3',
        'dojo/dom-construct',
        'dojox/widget/Standby',
        'dijit/registry',
        'dojo/request/iframe',
        'apps/pmb/contribution/ComputedFieldsStore',
        'dojo/request/xhr',
        'dojo/dom-attr',
], function(declare, dom, on, lang, ioQuery, topic, query, domConstruct, Standby, registry, iframe, ComputedFieldsStore, xhr, domAttr){
	return declare(null, {
		nodeId : null,
		prefixURI : null,
		constructor: function(nodeId) {
			topic.subscribe('FormContributionSelector', lang.hitch(this, this.handleEvents))
			topic.subscribe('deleteContrib', lang.hitch(this, this.handleEvents))
			this.nodeId = nodeId;
			this.getPrefixUri()
			this.initScript();
		},
		handleEvents: function(evtType,evtArgs){
			switch(evtType){
				case 'formLoaded':
					this.getPrefixUri()
					break;
				case 'deleteSubContrib':
					this.deleteSubContrib(evtArgs.id, evtArgs.sub);
					break;
			}
		},
		init: function() {
			var onto_contribution_push_button = query('input[name$="'+this.prefixURI+'_onto_contribution_save_button"]', dom.byId(this.nodeId))[0];
			if (onto_contribution_push_button) {
				on(onto_contribution_push_button , 'click', lang.hitch(this, this.save));
			}
			var onto_contribution_push_button = query('input[name$="'+this.prefixURI+'_onto_contribution_push_button"]', dom.byId(this.nodeId))[0];
			if(onto_contribution_push_button) {
				on(onto_contribution_push_button, 'click', lang.hitch(this, this.push));
			}
			if (typeof window.computedFieldsStore == 'undefined') {
				var params = ioQuery.queryToObject(document.location.search.substring(1));
				if (!params.area_id) {
				    params.area_id = 0;
                }
				window.computedFieldsStore = new ComputedFieldsStore({areaId: params.area_id});
			}
			window.computedFieldsStore.initFormFields(this.nodeId);
		},
		save: function(e) {
			if (!this.valid_form()) {
				return false;
			}
			var form = e.target.form;
			var input = form.querySelectorAll("input[id$=is_draft]");
			if (input.length > 0) {
				var error = false;
				for (var i = 0; i < input.length; i++) {
					var inputNode = input[i];
					if (inputNode.value == true) {
						error = true;
					}
				}
				if (error) {
					this.errorAlert(pmbDojo.messages.getMessage('contribution', 'contribution_save_impossible_draft'));
					return false;
				}
			}

			var standby = new Standby({target: this.nodeId, text: '', imageText: ''});
			document.body.appendChild(standby.domNode);
			standby.startup();
			standby.show();
			var form = e.target.form;
			var params = ioQuery.queryToObject(form.action.substring(form.action.indexOf("?") + 1));
			form.setAttribute("accept-charset", "utf-8");

			topic.publish("contribution/submit", "save");

			iframe('./ajax.php?module=ajax&categ=contribution&iframe=1&action=save', {
				form: form.id,
				data: params,
				handleAs: 'json'
			}).then(lang.hitch(this, function(data) {
				/**
				 * Contenu de data.data: array("uri" => $this->item->get_uri(), "displayLabel" => $display_label)
				 */
				standby.hide();
				if (data && data.session_expired) {
					window.location.replace("./empr.php?tab=contribution_area&lvl=contribution_area_list");
				}else if(data.errors){
					let message = "";
					let errors = data.errors;
					let countErrors = errors.length;
					for(let i = 0; i < countErrors; i++){
						message += errors[i]+"\n";
					}
					standby.hide();
					window.alert(message);
				} else {
					//on desactive le mecanisme d'alerte lors du changement de page
					unload_off();
					topic.publish('Contribution', 'savedForm', {widgetId: this.nodeId, response: data});
				}
			}));
		},
		push: function(e) {
			if (!this.valid_form()) {
				return false;
			}

			var form = e.target.form;
			var input = form.querySelectorAll("input[id$=is_draft]");
			if (input.length > 0) {
				var error = false;
				for (var i = 0; i < input.length; i++) {
					var inputNode = input[i];
					if (inputNode.value == true) {
						error = true;
					}
				}
				if (error) {
					this.errorAlert(pmbDojo.messages.getMessage('contribution', 'contribution_save_impossible_draft'));
					return false;
				}
			}

			if (!confirm(pmbDojo.messages.getMessage('contribution', 'onto_contribution_push_confirm'))) {
				return false;
			}


			var standby = new Standby({target: this.nodeId, text: '', imageText: ''});
			document.body.appendChild(standby.domNode);
			standby.startup();
			standby.show();
			var form = e.target.form;
			var params = ioQuery.queryToObject(form.action.substring(form.action.indexOf("?") + 1));
			form.setAttribute("accept-charset", "utf-8");

			topic.publish("contribution/submit", "push");
			//Visiblement le parametre POST prend le pas sur le parametre GET alors on force save_push dans le form
			params.action = "save_push";
			iframe('./ajax.php?module=ajax&categ=contribution&iframe=1&action=save_push', {
				form: form.id,
				data: params,
				handleAs: 'json'
			}).then(lang.hitch(this, function(data) {
				unload_off();
				if ((form.identifier || form.create_entity) && form.sub_form.value == 1) {
					standby.hide();
					topic.publish('Contribution', 'savedForm', {widgetId: this.nodeId, response: data});

				} else {
					var url = "./empr.php?tab=contribution_area&lvl=contribution_area_done";
					if (data && data.id) {
						url += "&last_id="+data.id;
					} else {
						console.error(data)
					}

					window.location.href = url;
				}
			}));
		},
		valid_form: function() {
			var error_message = "";

			for (var i in window[this.prefixURI + "_validations"]){
				if(!window[this.prefixURI + "_validations"][i].check()){
					if (error_message) {
						error_message += " ";
					}
					error_message += window[this.prefixURI + "_validations"][i].get_error_message();
				}
			}
			if(error_message != ""){
				this.errorAlert(error_message);
				return false;
			}
			return true;
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
		initScript: function() {
			if(this.nodeId) {
				query('script', this.nodeId).forEach(function(node) {
					domConstruct.create('script', {
						innerHTML: node.innerHTML
					}, node, 'replace');
				});
			}
		},
		getPrefixUri: function () {
			var prefix = query("#prefix_uri", this.nodeId);
			this.prefixURI = "";
			if (prefix.length) {
				this.prefixURI = prefix[0].value;
			}
		},
		deleteSubContrib : function (id, sub){
			var parent = event.target.form.parentNode;
			var widget = registry.byId(domAttr.get(parent, 'widgetid'));

			if (widget.parameters.tabId == this.nodeId){
				xhr.post('./ajax.php?module=ajax&categ=contribution&sub='+sub+'&action=delete', {
					data: {'id':id, 'ajax': true},
					handleAs: 'json',
				}).then(lang.hitch(this, function(response) {
					if (response.session_expired) {
						window.location.replace('./empr.php?tab=contribution_area&lvl=contribution_area_list');
					} else {
						if (typeof response.success != undefined && response.success){
							topic.publish('removeSubContrib', 'removeSubContrib', {widgetId: widget.parameters.tabId, response: response});
						}
						if (typeof response.error != undefined && response.error){
							this.errorAlert(pmbDojo.messages.getMessage('contribution', 'onto_contribution_error_delete'));
						}
					}
				}));
			}
		}
	})
});