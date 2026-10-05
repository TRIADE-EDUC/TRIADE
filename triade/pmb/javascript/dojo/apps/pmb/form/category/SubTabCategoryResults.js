// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabCategoryResults.js,v 1.8.6.1 2024/12/24 10:13:20 dgoron Exp $


define([
        'dojo/_base/declare',
        'dojo/dom',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/request/xhr',
        'dojo/dom-form',
        'dojo/dom-attr',
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
        'dojo/io-query',
        'dojo/request/iframe',
        'dojo/request',
        'apps/pmb/form/SubTabResults',
        ], function(declare, dom, on, lang, xhr, domForm, domAttr, TabContainer, ContentPane, query, ready, topic, registry, domAttr, geometry, domConstruct, domStyle, ioQuery, iframe, request, SubTabResults){
		return declare([SubTabResults], {
			searchType: null,
			onLoad: function(){
				if(query('form[name^="search_form_"]', this.containerNode).length){
					var searchForm = query('form[name^="search_form_"]', this.containerNode)[0];
				}else{
					var searchForm = query('form[name="store_search"]', this.containerNode)[0];
				}
				if(searchForm){
					domAttr.set(searchForm, 'action', this.origin);
					searchForm.submit = lang.hitch(this, this.changePage, searchForm);
					on(searchForm, 'submit', lang.hitch(this, this.changePage, searchForm));
				}
				if(this.searchType == 'hierarchy') {
					//./select.php?what=categorie&caller=notice&p1=f_categ_id0&p2=f_categ0&autoindex_class=autoindex_record&id_thes=1&dyn=1&module=selectors&indexation_lang=en_UK&parent=0&deb_rech=&action=hierarchical_results_search&parent=192&id2=192&id_thes=1
					var elements = query('a[href^="./select.php?what=categorie"]', this.containerNode);
					elements.forEach(lang.hitch(this, function(element){
						if(!element.getAttribute('data-type-link') || element.getAttribute('data-type-link') != 'pagination') {
							on(element, 'click', lang.hitch(this, this.showChildren, element));
						}
					}));
					var elements = query('[data-type-link="pagination"]', this.containerNode);
					elements.forEach(lang.hitch(this, function(element){
						on(element, 'click', lang.hitch(this, this.changePage, element));
					}));	
					if (dom.byId('category_id_thes')) {
						domAttr.set('category_id_thes', 'onchange', '');
					}
				} else {
					this.inherited(arguments);
				}
			},
			
			showChildren: function(element, e){
				e.preventDefault();
				var link = domAttr.get(element, 'href');
				var queryObject = ioQuery.queryToObject(link.substring(link.indexOf('?')+1, link.length));
				
 				request(this.parameters.selectorURL+"&action=hierarchical_results_search", {
					data: queryObject,
					method: 'POST',
					handleAs: 'html',
				}).then(lang.hitch(this, function(data){
					topic.publish('SubTabHierarchicalSearch', 'SubTabHierarchicalSearch', 'printResults', {results: data, origin: this.parameters.selectorURL+"&action=hierarchical_results_search&search_type=hierarchy", search_type:'hierarchy', parent: queryObject.parent[queryObject.parent.length-1]});
					setTimeout(() => {
						  this.getParent().resizeIframe();
					}, "500");
				}));
				return false; 
			},
			
			changePage: function(searchForm, e){
				if(e instanceof Event) {
					e.preventDefault();
				}
				if(searchForm.getAttribute('data-type-link') && searchForm.getAttribute('data-type-link') == 'pagination') {
					var link = domAttr.get(searchForm, 'href');
					var queryObject = ioQuery.queryToObject(link.substring(link.indexOf('?')+1, link.length));
	 				request(this.parameters.selectorURL+"&action=hierarchical_results_search", {
						data: queryObject,
						method: 'POST',
						handleAs: 'html',
					}).then(lang.hitch(this, function(data){
						this.set('content', data);
						this.getParent().resizeIframe();
					}));
				} else {
					var data = domForm.toObject(searchForm);
					if(data.action){
						delete data.action;
					}
					var previousOrigin = domAttr.get(searchForm, 'action');
					var queryObject = ioQuery.queryToObject(previousOrigin.substring(previousOrigin.indexOf('?')+1, previousOrigin.length));
					if(queryObject.mode && !data.mode){
						data.mode = queryObject.mode;
					}
					request(domAttr.get(searchForm, 'action'), {
						data: data,
						method: 'POST',
						handleAs: 'html',
					}).then(lang.hitch(this, function(data){
						this.set('content', data);
					}));
				}
				return false;
			},
			setSearchType: function(searchType){
				this.searchType = searchType;
			}
		})
});