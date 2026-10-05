// +-------------------------------------------------+
// + 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: OntoFormEdit.js,v 1.2 2022/11/02 11:54:09 arenou Exp $


define(['dojo/_base/declare',
	'dojo/request/xhr',
	'dojo/_base/lang',
	'dojo/topic',
	'dojo/on',
	'dojo/dom',
	'dojo/dom-geometry',
	'dojo/dom-style',
	'dojo/dom-attr',
	'dojo/query',
	'dojo/dom-construct',
	'apps/pmb/gridform/FormEdit',
	'dijit/registry',
	'dojo/dom-class'],
	function(declare, xhr, lang, topic, on, dom, domGeom, domStyle, domAttr, query, domConstruct, FormEdit, registry, domClass) {

		return declare([FormEdit], {
			ontologyId:'',
			ontoClass:'',
			getDatas: function() {
				var currentUrl = window.location;
				this.module = 'semantic';
				this.ontologyId = /ontology_id=(\d+)&?/g.exec(currentUrl)[1];
				this.type = this.getOntoClass()+"_"+this.ontologyId;
			
				var returnedInfos = { genericType: this.type, genericSign: this.getSign() };
				xhr("./ajax.php?module=" + this.module + "&categ=grid&action=get_datas", {
					handleAs: "json",
					method: 'post',
					data: 'datas=' + JSON.stringify(returnedInfos)
				}).then(lang.hitch(this, this.getDatasCallback));
			},
			getStruct: function() {
				var currentUrl = window.location;
				var JSONInformations = new Array();
				if (this.zones.length) {
					for (var i = 0; i < this.zones.length; i++) {
						JSONInformations.push(this.zones[i].getJSONInformations());
					}
				}
				this.module = 'semantic';
				this.ontologyId = /ontology_id=(\d+)&?/g.exec(currentUrl)[1];
				this.type = this.getOntoClass()+"_"+this.ontologyId;
			
				var returnedInfos = { zones: JSONInformations, genericType: this.type};
				return returnedInfos;
			},
			
			getOntoClass : function(){
				if(this.ontoClass != ''){
					return this.ontoClass;
				}
				var currentUrl = window.location;
				try{
					this.ontoClass = /sub=(\w+)&?/g.exec(currentUrl)[1];
				}catch(e){
				 	this.ontoClass = /range=(\w+)&?/g.exec(currentUrl)[1];
				}
				return this.ontoClass;
			}
		});
	});