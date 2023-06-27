// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchUniverseController.js,v 1.15 2018-09-21 12:39:22 vtouchard Exp $


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
    	memoryNodes : null,
    	links: null,
    	search_field: null,
    	selectedLink: null,
    	universeQuery:null,
    	segmentsValues: null,
    	constructor: function(universeQuery){
    		this.search_field = document.getElementsByName('user_query')[0];
    		this.links = query('.search_universe_segments_row');
			this.addUniverseEvents();
			this.segmentsValues = new Array();
    	},
    	removeSelected: function(){
    		this.links.forEach(link => {
    			domClass.remove(link, 'selected');
    		});
    	},
    	setWaitingIcon: function(){
    		this.links.forEach(link => {
    			var resultP = query('.segment_nb_results', link)[0];
    			resultP.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    		});
    	},
    	setUniverseHistory : function(data) {
    		dom.byId('last_query').value = data.user_query;
    		xhr("./ajax.php?module=ajax&categ=search_universes&sub=search_universe&action=rec_history&id="+data.universe_id,{
				data : data,
				handleAs: "json",
				method:'POST',
			}).then((response)=>{
				if (response) {
					var historyNode = dom.byId('search_index');
					if (historyNode && response.search_index) {
						historyNode.value = response.search_index;
					}
					this.links.forEach(segment => {
						this.updateSegmentsLinks(segment);
					});
				}
			});
    	},
    	addUniverseEvents: function(){
    		var form = dom.byId('search_universe_input');
			on(form, 'submit', lang.hitch(this, function(e){
				e.preventDefault();
				this.universeFormSubmit(form);
			}));

			//si on a une valeur par défaut (provenant de l'historique), on poste les formulaires des segments;			
			var last_query = dom.byId('last_query').value;
			if (last_query) {
				this.setUserQuery(last_query);
				this.universeFormSubmit(form);
			}
    	},
    	
    	setUserQuery : function(newValue) {
    		dom.byId('user_query').value = newValue; 
    	},
    	
    	universeFormSubmit : function (form, setHistory = true) {
			var defaultSegment = dom.byId('default_segment').value;
    		var data = JSON.parse(domForm.toJson(form.id));
			this.setWaitingIcon();
			if (setHistory) {
				this.setUniverseHistory(data);
			}
			this.links.forEach(link => {
				data.segment_id = domAttr.get(link, 'data-segment-id');
				xhr(form.action,{
					data : data,
					handleAs: "json",
					method:'POST',
				}).then(lang.hitch(this,function(response){
					if (response) {
						var resultP = query('.segment_nb_results', link)[0];
						resultP.innerHTML = '('+response.nb_result+')';
						
						var hiddenMC = query('.simple_search_mc', link)[0];
						if (hiddenMC) {
							hiddenMC.value = response.simple_search_mc
						}
						if (defaultSegment == response.segment_id) {
							this.segmentsValues = response;
							this.displayResult();
						}
						var segmentId = domAttr.get(link, 'data-segment-id');
						var universeId = domAttr.get(link, 'data-universe-id');
						localStorage.setItem('universe_'+universeId+'_segment_'+segmentId, response.nb_result);
					}
				}));
			});
    	},
    	
    	displayResult: function(){
	    	dom.byId('result_container').innerHTML = '';
			if(this.segmentsValues.results){
				dom.byId('result_container').innerHTML = '<h3>'+this.segmentsValues.label+'</h3>'+this.segmentsValues.results;
				collapseAll();
			}
    	},
    	
    	updateSegmentsLinks : function(segment) {
			var searchIndex = dom.byId('search_index').value;
			if (segment) {
				var segmentLink = query('a', segment)[0];
				domAttr.set(segmentLink, "href", domAttr.get(segmentLink, "href") + "&search_index=" + searchIndex);
    		}
    	},
    });
});