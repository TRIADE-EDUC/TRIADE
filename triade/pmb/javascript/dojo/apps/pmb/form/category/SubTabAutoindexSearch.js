// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabAutoindexSearch.js,v 1.2 2020/01/08 07:46:46 btafforeau Exp $


define([
        'dojo/_base/declare',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/request',
        'dojo/dom-form',
        'dojo/dom-attr',
        'dojox/layout/ContentPane',
        'dojo/query',
        'dojo/topic',
        ], function(declare, on, lang, request, domForm, domAttr, ContentPane, query, topic){
		return declare([ContentPane], {
			
			constructor: function() {
				
			},
			postCreate: function() {
				this.inherited(arguments);
			},
			onLoad: function(){
				
			},
			onDownloadEnd: function(){
				var searchButton = query('input[id="launch_autoindex_search_button"]', this.containerNode)[0];
				if (searchButton) {
					searchButton.remove();
				}
				var refreshButton = query('input[id="refresh_autoindex_search_button"]', this.containerNode)[0];
				if (refreshButton) {
					domAttr.set(refreshButton, 'onclick', '');
					domAttr.set(refreshButton, 'type', 'submit');
					this.form = refreshButton.form;
					on(this.form, 'submit', lang.hitch(this, this.get_index));
				}
				this.getParent().resizeIframe();
			},
			destroy: function(){
				this.inherited(arguments);
			},
			get_index: function(e){
				
				if(!this.form) return false;
											
				//lecture des champs de la notice
				var something_checked=false;
				for(var i=0; i<fields_index_auto.length; i++){	
					fields_index_auto[i]['value']='';
					if(document.getElementById('chk_'+fields_index_auto[i]['name']).checked) {
						something_checked=true;
						if(parent.window.parent.document.forms[this.parameters.caller].elements[fields_index_auto[i]['field']]) {
							fields_index_auto[i]['value'] = encodeURIComponent(parent.window.parent.document.forms['notice'].elements[fields_index_auto[i]['field']].value);
						}
					}
					
				}
				
				// lecture de la langue d'indexation de la notice
				document.getElementById('user_lang').value=parent.window.parent.document.forms[this.parameters.caller].elements['indexation_lang'].value;
				
				document.getElementById('autoindex_txt').value=JSON.stringify(fields_index_auto);
				if (something_checked) {
					this.postForm(e);
				}
				return false;
			},
			postForm: function(e){
				e.preventDefault();
				request(this.parameters.selectorURL+"&action=autoindex_results_search", {
					data: domForm.toObject(this.form),
					method: 'POST',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data){
					topic.publish('SubTabAutoindexSearch', 'SubTabAutoindexSearch', 'printResults', {results: data, origin: this.parameters.selectorURL+"&action=autoindex_results_search&search_type=autoindex"});
				}));
				return false;
			}
		})
});