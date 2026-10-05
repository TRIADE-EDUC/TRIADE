// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MemorySelector.js,v 1.2 2018/12/12 16:26:40 apetithomme Exp $


define([
        'dojo/_base/declare',
        'dojo/dom',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/topic',
        'dojo/dom-construct',
        'dijit/registry',
        'dojo/store/Memory',
        'dojo/io-query',
        'dojo/_base/xhr',
        'dojo/store/util/QueryResults'
], function(declare, dom, on, lang, topic, domConstruct, registry, Memory, ioQuery, xhr, QueryResults){
	return declare([Memory], {
		target: './ajax_selector.php',
		
		postCreate : function() {
			this.inherited(arguments);
		},
		
		query: function(queryParameters){
			if(typeof queryParameters == "object"){ //Paramètres de requete bien présent
				var recomposedParameters = ioQuery.objectToQuery(queryParameters);
				var results = xhr("GET", {
					url: this.target+'?'+recomposedParameters,
					handleAs: "json",
				});
				results.total = results.then(function(){
					var range = results.ioArgs.xhr.getResponseHeader("Content-Range");
					if (!range){
						// At least Chrome drops the Content-Range header from cached replies.
						range = results.ioArgs.xhr.getResponseHeader("X-Content-Range");
					}					
					return range && (range = range.match(/\/(.*)/)) && +range[1];
				});
				QueryResults(results).then(lang.hitch(this, this.addData));
				return QueryResults(results);
			}
		},
		addData: function(data){
			data.forEach(lang.hitch(this,function(item){
				if(!this.presentInData(item)){
					this.data.push(item);
				}
			}));
		},
		presentInData: function(obj){
			if(this.data[i] && typeof this.data == 'object'){
				for(var i=0 ; i<this.data.length ; i++){
					if(this.data[i].id == obj.id && this.data[i].datas == obj.datas){
						return true;
					}
				}	
				return false;
			}
			return false;
		}
	})
});