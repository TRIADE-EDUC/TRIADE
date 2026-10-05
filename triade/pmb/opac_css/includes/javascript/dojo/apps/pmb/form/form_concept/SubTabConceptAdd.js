// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabConceptAdd.js,v 1.3 2020/01/08 07:46:46 btafforeau Exp $


define([
        'dojo/_base/declare',
        'dojo/dom',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/request/xhr',
        'dojo/dom-form',
        'dijit/layout/TabContainer',
        'dojox/layout/ContentPane',
        'dojo/query',
        'dojo/ready',
        'dojo/topic',
        'dijit/registry',
        'dojo/dom-attr',
        'dojo/dom-geometry',
        'dojo/dom-construct',
        'dojo/dom-style',
        'dojo/_base/xhr',
        'apps/pmb/gridform/FormEdit',
        'dojo/dom-form',
        'dojo/request/iframe',
        'dojo/io-query',
        'apps/pmb/form/SubTabAdd',
        'apps/pmb/form/FormController',
        ], function(declare, dom, on, lang, xhr, domForm, TabContainer, ContentPane, query, ready, topic, registry, domAttr, geometry, domConstruct, domStyle, xhr, FormEdit, domForm, iframe, ioQuery, SubTabAdd, FormController){
		return declare([SubTabAdd], {
			postCreate: function() {
				this.inherited(arguments);
			},
			postForm: function(buttonClicked){
				var form = buttonClicked.form;
				var verified = false;
				if(domAttr.get(form, 'action').indexOf('select.php') != -1){
					domAttr.set(form, 'action', domAttr.get(form, 'action').replace('select.php?', 'ajax.php?module=selectors&is_iframe=1&'));
				}
				if (typeof submit_onto_form() === 'function' && submit_onto_form()) {
					verified = true;
				}
				if (verified) {
					iframe(domAttr.get(buttonClicked.form, 'action'),{
						form: buttonClicked.form,
						handleAs: 'json',
					}).then(lang.hitch(this, function(data){
						if(parseInt(data.id) && (parseInt(data.id) !=0)){
							this.set('href', this.href);
							data.ghostContainerId = this.parameters.ghostContainerId;
							topic.publish('SubTabConceptAdd', 'SubTabConceptAdd', 'elementAdded', data);
						} 
					}));
				}
				return false;
			},
			setSubmitEvent: function(queryResult){
				var submitButton = queryResult[0];
				domAttr.set(submitButton,'type', 'button');
				domAttr.remove(submitButton, 'onclick');
				on(submitButton, 'click', lang.hitch(this, this.postForm, submitButton));
			},
		})
});