// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabConceptNavigate.js,v 1.3.16.1 2025/02/14 10:47:58 dgoron Exp $


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
        'dojo/io-query',
        'dojo/request/iframe',
        'dojo/request',
        'dojox/widget/Standby',
        'apps/pmb/ConceptsTree',
        "dojo/Deferred"
        ], function(declare, dom, on, lang, xhr, domForm, TabContainer, ContentPane, query, ready, topic, registry, domAttr, geometry, domConstruct, domStyle, ioQuery, iframe, request, Standby,ConceptsTree,Deferred){
	return declare([ContentPane], {
		origin: '',
		currentType: '',
		currentURL: '',
		backURL: '',
		standby: null,
		tree: null,
		
		// On s'abonne aux events du ConceptsTree
		constructor: function(parameters) {
			this.parameters = parameters;
			topic.subscribe('ConceptsTree', lang.hitch(this, this.handleEvents));
		},
		
		
		//gestionnaire d'events
		handleEvents: function(evtClass, evtType, evtArgs){
			switch(evtClass){
				case 'ConceptsTree' :	
					switch(evtType){
						// Lorsqu'un noeud change dans l'arbre
						case 'resize' :
							this.resizeIframe();
							break;
						// appel du callback au clic sur un noeud de l'arbre
						case 'item_clicked' :
							this.selectItem(evtArgs.object);
							break;
						// l'arbre est pret, on l'insère dans le DOM, on start et on demande un petit refresh de la frame
						case 'ready' :
							this.tree.placeAt(this.containerNode);
							this.tree.startup();
							this.resizeIframe();
							break;
					}
					break;
			}
		},
		
		// A la fin du chargement de la frame, on instancie l'arbre
		onDownloadEnd: function(){
			this.inherited(arguments);
			var params = {};
			if(this.parameters.conceptSchemes && this.parameters.conceptSchemes.length > 0 ){
				params = {conceptSchemes: this.parameters.conceptSchemes};
			}
			this.tree = new ConceptsTree(params);
		},
				
		
		// Callback au clic sur un concept
		selectItem: function(object){
			if(object.type == "concept"){
				var query = this.parameters.currentURL.substring(this.parameters.currentURL.indexOf("?") + 1, this.parameters.currentURL.length);
				var queryObject = dojo.queryToObject(query);
				var id_value =  object.uri;
				if(queryObject.dyn == 4){
					id_value =  object.id;
				}
				var callback = ''
				if(queryObject.callback){
					callback = queryObject.callback;
				}
				set_parent(queryObject.caller, "concept", id_value, object.isbd, "",callback);
			}
		},
		
		//pour le redimensionnement de la frame
		resizeIframe: function(noresize=false){
			if(window.parent.location.href != window.location.href){
			    window.frameElement.style.height = (window.frameElement.contentWindow.document.body.scrollHeight)+'px';
			}
			this.resize();
		},
		
		// Je ne sais pas si le reste est vraiment utile, je suis reparti d'un autre sous-onglet
		setContent:function(){
			this.inherited(arguments);
			this.resizeIframe();
		},
		
		setOrigin: function(url){
			this.origin = url;
		},
		resizeIframe: function(noresize=false){
			if(window.parent.location.href != window.location.href){
			    window.frameElement.style.height = (window.frameElement.contentWindow.document.body.scrollHeight)+'px';
			}
			this.resize();
		},
		initStandby: function(){
			if(!this.standby){
				this.standby = new Standby({
					target: this.domNode
				});
				document.body.appendChild(this.standby.domNode);
				this.standby.startup();
			}
			this.standby.show();
		},
		shutStandby: function(){
			if(this.standby){
				this.standby.hide();
			}
		},
	})
});