// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormCMSEdit.js,v 1.9 2024/03/20 16:27:52 dbellamy Exp $

define([
        'dojo/_base/declare',
        'dojo/_base/lang',
        'dojo/topic',
        'dojo/query',
        'dojo/on',
        'dojo/request',
        'dojo/dom',
        'dojo/dom-attr',
        'dojo/dom-style',
        'apps/pmb/gridform/FormEdit',
        ], function(declare, lang, topic, query, on, request, dom, domAttr, domStyle, FormEdit){
		return declare([FormEdit], {
			
			 constructor:function(module, type, context){
			 },
			switchGrid: function(evt){
				this.flagOriginalFormat = true;
				this.destroyTinymceElements();
				this.destroyAjaxElements();
				this.unparseDom();
				var loaded = cms_editorial_load_type_form(document.getElementById('cms_editorial_form_type').value, document.getElementById('cms_editorial_form_type'));
				if(loaded) {
					var context = this;
					//Attendre le refresh
					setTimeout(function() {
				        context.getDefaultPos();
				        context.getDatas();
				        context.loadTinymceElements();
				    }, 1000);
				}
			},
			btnEditCallback: function(evt){
				this.inherited(arguments);
				
				//Particularité au contenu éditorial - L'identifiant n'est pas visible en création
		  		var domCMSEditorialId = dom.byId('el0Child_0');
	  			domStyle.set(domCMSEditorialId, 'display', 'block');
			},
			getDatasCallback: function(response){
				this.inherited(arguments);
				if(!this.getObjId()) {
					this.hideNodeIdentifiant();
				}
			},
			destroyTinymceElements: function() {
				if(typeof(tinyMCE)!= 'undefined') {
					tinyMCE.remove();
				}
			},
			loadTinymceElements: function() {
				if(typeof(tinyMCE)!= 'undefined') {
					setTimeout(function(){
						if(document.getElementById('cms_editorial_form_resume')) {
							tinyMCE_execCommand('mceAddControl', true, 'cms_editorial_form_resume');
						}
					},1000);
				}
				if(typeof(tinyMCE)!= 'undefined') {
					setTimeout(function(){
						if(document.getElementById('cms_editorial_form_contenu')) {
							tinyMCE_execCommand('mceAddControl', true, 'cms_editorial_form_contenu');
						}
					},1000);
				}
				//Ajout du parse pour les champs perso
				if(typeof(tinyMCE)!= 'undefined') {
					setTimeout(function(){
						tinyMCE_init();
					},1000);
				}
			},
			getObjId: function() {
				var nodeObjId = dom.byId('cms_editorial_form_obj_id');
				if(nodeObjId) {
					return parseInt(nodeObjId.value);
				}
				return 0;
			},
			hideNodeIdentifiant: function() {
				var domCMSEditorialId = dom.byId('el0Child_0');
				domStyle.set(domCMSEditorialId, 'display', 'none');
			}
		})
});