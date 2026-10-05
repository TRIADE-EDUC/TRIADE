// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabConceptAdd.js,v 1.8 2020/08/28 14:40:27 tsamson Exp $


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
        'apps/pmb/PMBDojoxDialogSimple',
        ], function(declare, dom, on, lang, xhr, domForm, TabContainer, ContentPane, query, ready, topic, registry, domAttr, geometry, domConstruct, domStyle, xhr, FormEdit, domForm, iframe, ioQuery, SubTabAdd, FormController, PMBDojoxDialogSimple){
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

				if (typeof check_onto_form === 'function' && check_onto_form()) {
					verified = true;
				}
				if ((typeof test_notice === 'function' && test_notice(form)) || (typeof test_form === 'function' && test_form(form))) {
					verified = true;
				}
				if (verified){
					iframe(domAttr.get(buttonClicked.form, 'action'),{
						form: buttonClicked.form,
						handleAs: 'json',
					}).then(lang.hitch(this, function(data){
						console.log(data);
						if(parseInt(data.id) && (parseInt(data.id) !=0)){
							this.set('href', this.href);
							data.ghostContainerId = this.parameters.ghostContainerId;
							topic.publish('SubTabConceptAdd', 'SubTabConceptAdd', 'elementAdded', data);
						}else if(data.html){
							var dialog = PMBDojoxDialogSimple({
								title: "",
								content: data.html,
							});
							var forcingForm = query('form', dialog.containerNode)[0];
							domAttr.remove(forcingForm, 'action');
							if(query('#forcing_button', dialog.containerNode)[0]) {
								var button = query('#forcing_button', dialog.containerNode)[0];
								domAttr.set(button, 'type', 'button');
								on(button, 'click', lang.hitch(this, 
									function(){
										this.postForm(buttonClicked, 1);
										dialog.hide();
									} ,
								buttonClicked));
							}
							dialog.show();
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