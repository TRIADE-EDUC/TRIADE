// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchSegmentController.js,v 1.8 2018-09-20 15:36:42 vtouchard Exp $


define(["dojo/_base/declare",
    "dojo/topic",
    "dojo/_base/lang",
    "d3/d3",
    "dojo/dom",
    "dojo/dom-construct",
    "dojo/dom-style",
    "dojo/on",
    "dojo/query",
    "dojo/dom-class",
    "dojo/dom-form",
    "dojo/dom-attr",
    "dojo/request/xhr",
    ], function (declare, topic, lang, d3, dom, domConstruct, domStyle, on, query, domClass, domForm, domAttr, xhr) {

    return declare(null, {
    	numSegment : null,
    	universeQuery: null,
    	constructor: function(data){
    		console.log('segmentController constructed')
    		this.numSegment = data.numSegment;
    		if(data.universeQuery){
    			this.universeQuery = data.universeQuery; 
    		}
    		this.init();
    	},
    	
    	init : function() {
			query("img[data-divId]").forEach(lang.hitch(this,function(node){				
				var div =  dom.byId(domAttr.get(node, 'data-divId'));
				on(node, "click", lang.hitch(this, this.removeSearch,div));
			}));
			this.addJsonSearchToForm();
			this.addSearchIndexToForm();
			this.initFacets();
			this.initSegmentsLinks();
			this.getNbResultsOtherSegments();
    	},
    	
    	removeSearch : function(div) {
    		var form = query("form[name='form_values']")[0];
    		if (form) {
        		var deletedSearch = query("input[name='search_nb']", div)[0];
        		if (deletedSearch) {
        			domConstruct.create('input', { type : 'hidden', name : 'deleted_search_nb', value : deletedSearch.value}, form);
        		}
        		var pageNumber = dom.byId('page_number');        		
        		if (pageNumber) {
        			//on réinitalise le numéro de page
        			domAttr.set(pageNumber, {value : 1});
        		}        		
    			form.submit();
    		}
    	},
    	
    	addJsonSearchToForm : function() {
			var jsonSearch = dom.byId('segment_json_search').value;
			if (jsonSearch) {
	    		var formSearchInput = query("form[name='search_input']")[0];
	    		if (formSearchInput) {
    				domConstruct.create('input', { type : 'hidden', name : 'segment_json_search', value : jsonSearch}, formSearchInput);
	    		}
    		}    		
    	},
    	
    	addSearchIndexToForm : function() {
    		var searchIndex = dom.byId('search_index').value;
    		if (searchIndex) {
    			var formSearchInput = query("form[name='search_input']")[0];
    			if (formSearchInput) {
    				domConstruct.create('input', { type : 'hidden', name : 'search_index', value : searchIndex}, formSearchInput);
    			}
    		}    		
    	},
    	
    	initFacets : function() {
    		query(".facet-link").forEach(lang.hitch(this,function(facetLink){
    			on(facetLink, 'click', lang.hitch(this, function() {
    				var facetLine = this.findParent(facetLink, 'facette_tr');
    				var clickedFacet = query('input[name="check_facette[]"]', facetLine)[0];
    				var form = query('form[name="form_values"]')[0];
    				domConstruct.create('input', {type:'hidden', value:clickedFacet.value, name:'check_facette[]'}, form);
    				form.submit()
    			}));
    		}));
    		
    		query(".filter_button").forEach(lang.hitch(this,function(button){
    			on(button, 'click', lang.hitch(this, function() {
    				var facetsForm = query('form[name="facettes_multi"]')[0];
    				var tickedFacets = query('input[name="check_facette[]"]', facetsForm);
    				var facetsValue = [];
    				tickedFacets.forEach(function(facet){
    					if(facet.checked == true){
    						facetsValue.push(facet.value);
    					}
    				});
    				var form = query('form[name="form_values"]')[0];
    				facetsValue.forEach(function(facetValue){
    					domConstruct.create('input', {type:'hidden', value:facetValue, name:'check_facette[]'}, form);	
    				});
    				form.submit()
    			}));
    		}));
    	},
    	
    	findParent: function(node, parentClass){
    		var node = node.parentElement;
    		if(domClass.contains(node, parentClass)){
    			return node;
    		}else{
    			return this.findParent(node, parentClass);
    		}
    	},
    	
    	initSegmentsLinks: function(){
			var searchIndex = dom.byId('search_index').value;
    		query('.search_universe_segments_row').forEach(segment => {
				var segmentLink = query('a', segment)[0];
				domAttr.set(segmentLink, "href", domAttr.get(segmentLink, "href") + "&search_index=" + searchIndex);
			});
    	},
    	
    	getNbResultsOtherSegments : function () {
			var searchIndex = dom.byId('search_index').value;
    		var data = {'search_index' : searchIndex};    		
    		query('.search_universe_segments_row').forEach(link => {
//    			if (domAttr.get(link, 'selected') == null) {
    				this.setWaitingIcon(link);
    				
    				var segmentId = domAttr.get(link, 'data-segment-id');
					var universeId = domAttr.get(link, 'data-universe-id');
					if(localStorage.getItem('universe_'+universeId+'_segment_'+segmentId) != null){
						var resultP = query('.segment_nb_results', link)[0];
						resultP.innerHTML = '('+localStorage.getItem('universe_'+universeId+'_segment_'+segmentId)+')';
					}else{
						var action = "./ajax.php?module=ajax&categ=search_universes&sub=search_segment&action=get_nb_result&id="+ domAttr.get(link, 'data-segment-id');
						xhr(action,{
							data : data,
							handleAs: "json",
							method:'POST',
						}).then(lang.hitch(this,function(response){
							if (response) {
								var resultP = query('.segment_nb_results', link)[0];
								resultP.innerHTML = '('+response.nb_result+')';
							}
						}));	
					}
//    			}
    		});			
    	},
    	
    	setWaitingIcon: function(link){
			var resultP = query('.segment_nb_results', link)[0];
			resultP.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    	},
    });
});