// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Contribution.js,v 1.13 2019-04-03 13:01:37 tsamson Exp $

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
        'apps/pmb/contribution/ComputedFieldsStore'
], function(declare, dom, on, lang, ioQuery, topic, query, domConstruct, Standby, registry, iframe, ComputedFieldsStore){
	return declare(null, {
		nodeId : null,
		prefixURI : null,
		constructor: function(nodeId) {
			this.nodeId = nodeId;
			var prefix = query("#prefix_uri",this.nodeId);
			this.prefixURI = "";
			if (prefix.length) {
				this.prefixURI = prefix[0].value;
			}
			this.init();
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
			if(this.nodeId) {
				query('script', this.nodeId).forEach(function(node) {
					domConstruct.create('script', {
						innerHTML: node.innerHTML,
						type: 'text/javascript'
					}, node, 'replace');
				});
			}
			if (typeof window.computedFieldsStore == 'undefined') {
				var params = ioQuery.queryToObject(document.location.search.substring(1));
				window.computedFieldsStore = new ComputedFieldsStore({areaId: params.area_id});
			}
			window.computedFieldsStore.initFormFields(this.nodeId);
		},
		save: function(e) {
			if (!this.valid_form()) {
				return false;
			}
			var standby = new Standby({target: this.nodeId, text: '', imageText: ''});
			document.body.appendChild(standby.domNode);
			standby.startup();
			standby.show();
			var form = e.target.form;
			var params = ioQuery.queryToObject(form.action.substring(form.action.indexOf("?") + 1));
			form.setAttribute("accept-charset", "utf-8");
			iframe('./ajax.php?module=ajax&categ=contribution&iframe=1&action=save', {
				form: form.id,
				data: params,
				handleAs: 'json'
			}).then(lang.hitch(this, function(data) { 
				/**
				 * Contenu de data.data: array("uri" => $this->item->get_uri(), "displayLabel" => $display_label)
				 */
				standby.hide();
				if (data.session_expired) {
					window.location.replace("./empr.php?tab=contribution_area&lvl=contribution_area_list");
				} else {
					topic.publish('Contribution', 'savedForm', {widgetId: this.nodeId, response: data});
				}				
			}));
		},
		push: function(e) {
			if (!this.valid_form()) {
				return false;
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
			iframe('./ajax.php?module=ajax&categ=contribution&iframe=1&action=save_push', {
				form: form.id,
				data: params,
				handleAs: 'json'
			}).then(function(data) {
				window.location.href = "./empr.php?tab=contribution_area&lvl=contribution_area_done&last_id="+data.id;
			});
		},
		valid_form: function() {
			var error_message = "";
			
			for (var i in window[this.prefixURI + "_validations"]){
				if(!window[this.prefixURI + "_validations"][i].check()){
					if (error_message) {
						error_message += " ";
					}		
					error_message+= window[this.prefixURI + "_validations"][i].get_error_message();
				}
			}
			if(error_message != ""){
				var div = document.createElement('div');
				div.innerHTML = error_message;
				error_message = div.firstChild.nodeValue;
				alert(error_message);
				return false;
			}
			return true;
		}
	})
});