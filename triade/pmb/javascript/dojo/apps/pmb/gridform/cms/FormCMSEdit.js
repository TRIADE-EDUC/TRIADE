// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormCMSEdit.js,v 1.4 2019-05-17 12:39:05 dgoron Exp $

define([
        'dojo/_base/declare',
        'dojo/_base/lang',
        'dojo/topic',
        'dojo/query',
        'dojo/on',
        'dojo/request',
        'dojo/dom-attr',
        'apps/pmb/gridform/FormEdit',
        ], function(declare, lang, topic, query, on, request, domAttr, FormEdit){
		return declare([FormEdit], {
			
			 constructor:function(module, type, context){
				this.loadTinymceElements(); 
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
			destroyTinymceElements: function() {
				unload_tinymce();
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
			}
		})
});