// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DjangoDataTree.js,v 1.1 2021/05/04 13:13:36 arenou Exp $


define([
	"dojo/_base/declare", 
	"dijit/Tree", 
	"dojo/topic", 
	"dojo/_base/lang",
    'dojo/request/xhr',
	"dojo/when",
    'dijit/tree/ObjectStoreModel',
    'dojo/store/Observable',
    'apps/pmb/Store',
    "dojo/Deferred"
	], function(declare, Tree, topic, lang, xhr, when,ObjectStoreModel, Observable, Store, Deferred){
   
	
	// Dérivation du TreeNode 
	// permet d'injecter un tooltip HTML
	var MyTreeNode = declare(Tree._TreeNode, {
        _setLabelAttr: {node: "labelNode", type: "innerHTML"}
    });
	
	// Dérivation du Tree
	// pour tooltip HTML et l'envoi d'un event après une modification de l'arbre (pb de redimensionnement dans les frames)
	var DataTree = declare([Tree], {	
		showRoot : false,

		//Dérivation permettant de balance un event pour cabler un resize du widget parent
		_startPaint: function(/*Promise|Boolean*/ p){
			// summary:
			//		Called at the start of an operation that will change what's displayed.
			// p:
			//		Promise that tells when the operation will complete.  Alternately, if it's just a Boolean, it signifies
			//		that the operation was synchronous, and already completed.

			this._outstandingPaintOperations++;
			if(this._adjustWidthsTimer){
				this._adjustWidthsTimer.remove();
				delete this._adjustWidthsTimer;
			}

			var oc = lang.hitch(this, function(){
				this._outstandingPaintOperations--;

				if(this._outstandingPaintOperations <= 0 && !this._adjustWidthsTimer && this._started){
					// Use defer() to avoid a width adjustment when another operation will immediately follow,
					// such as a sequence of opening a node, then it's children, then it's grandchildren, etc.
					this._adjustWidthsTimer = this.defer("_adjustWidths");
				}
				topic.publish("DjangoDataTree","DjangoDataTree","resize",{});
			});
			when(p, oc, oc);
		},
		
		// Pour avoir les tooltip HTML
		_createTreeNode: function(args){
            return new MyTreeNode(args);
        },  
	});
	
	// Classe "Proxy"
	// Je n'ai pas réussi à faire autrement que comme ça...
	return declare(null,{
		parameters: null,
		constructor: function(parameters){
			// on a le premier niveau de données...
			this.parameters = parameters;
			console.log(this.parameters);
			this.gotDatas(parameters.items);
		},
		
		// Données récupérées
		gotDatas: function(datas){
    		store = new Store({
    			data: datas,
    			getIdentity : function(object){
    				return object.Format
    			}
    		});
    		console.log(datas);
    		store.getChildren = lang.hitch(this,this.getChildren);
    		//l'observable permet de conserver le DOM synchrone au store après une modification
    		this.store = new Observable(store);
    		this.model = new ObjectStoreModel({
				store:  new Observable(this.store),
		        query: { Format: 'root' },
		        mayHaveChildren: lang.hitch(this,this.mayHaveChildren),
		        getLabel: lang.hitch(this,this.getLabel),
    		});
    		this.tree = new DataTree({
    			// on rattache le modèle
    			model : this.model,
    			// callback au clic
    			onDblClick : lang.hitch(this,this.onDblClick),
    			// gestion d'un tooltip avec le détail du concept
    			onMouseOver: lang.hitch(this,this.showTooltip),
    			onMouseOut: lang.hitch(this,this.hideTooltip),
    		});
    		// l'arbre est prêt, on l'annonce !
    		// il est judicieux de faire le raccrochement au DOM et le startup en réponse à cet event
    		topic.publish("DjangoDataTree","DjangoDataTree","ready",{});
    		this.tree.placeAt(this.parameters.parentNode);
    		this.startup();
       },
       
       mayHaveChildren : function(item){
    	   return item.hasChild;
       },
       
       getLabel : function(item){
    	   return item.Format;
       },
       
       
       
       // Récupération des enfants
       getChildren: function(object){
    	   if(object.Format == "root"){
    		   return this.store.query({parent: object.Format});
    	   }
   			var deferred = new Deferred();
			var url = './ajax.php?module=ajax&categ=dataTree&item='+object.childItem+'&parent='+object.Format;
			xhr.get(url,{
				handleAs : 'json'			
			}).then(lang.hitch(self,function(datas){
				//ajout dans le store
				for(var i=0 ; i<datas.length ; i++){
					this.store.add(datas[i]);
				}
				//on retourne le résultat du deferred
				deferred.resolve(this.store.query({parent: object.Format}));
			}));
			// on retourne le promise
			return deferred.promise;
       },
       
       onDblClick: function(item){
    	   //TODO Recurse parent
			if(pmbDojo.aceManager.getEditor(this.parameters.textarea)){
				pmbDojo.aceManager.getEditor(this.parameters.textarea).insert('{{'+this.getCompletion(item)+'}}');
			}else{
				document.getElementById(this.parameters.textarea).value = document.getElementById(this.parameters.textarea).value + '{{'+this.getCompletion(item)+'}}';		
			}
       },
		
       getCompletion : function(item){
    	   let completion = item.Format;
    	   let antiloop = 0;
    	   do{
    		   let parents = this.store.query({Format: item.parent});
    		   let parent = 0;
    		   if(parents.length >= 1){
        		   let parent = parents[0];
            	   if(parent.Format == "root"){
            		   break;
            	   }
            	   if(parent.Type == "Collection"){
            		   completion = parent.Format+".[i]."+completion;
            	   }else{
            		   completion = parent.Format+"."+completion;
            	   }
            	   item = parent;
        	   } 
    		   if(item.Format == "root" || antiloop > 20){
    			   break;
    		   }
    		   antiloop++;
    	   }while (true);
    	   return completion;
    	  
       },
       
       getParentCompletion: function(item){
    	   
       },
       
       // Méthode proxy
       placeAt : function(domNode){
    	   this.tree.placeAt(domNode);
       },
       
       // Méthode proxy
       startup : function(){
    	   this.tree.startup();
       },
       
       showTooltip: function(event) {
           var node = dijit.getEnclosingWidget(event.target);
           if(node.item){
	            var detail = node.item.Description;
	            dijit.showTooltip(detail, node.labelNode);
           }
       },
       
       hideTooltip: function(event){
           var node = dijit.getEnclosingWidget(event.target);
           dijit.hideTooltip(node.labelNode);
       }
    });
});