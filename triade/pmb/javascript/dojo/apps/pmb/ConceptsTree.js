// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ConceptsTree.js,v 1.3 2019/01/14 15:34:20 arenou Exp $


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
	var ConceptTree = declare([Tree], {	
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
				topic.publish("ConceptsTree","ConceptsTree","resize",{});
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
			this.parameters = parameters;
    		// On commence par aller chercher les données
			xhr.get('./ajax.php?module=ajax&categ=concepts_selector',{
				handleAs : 'json'			
			}).then(lang.hitch(this,this.gotDatas));
		},
		
		// Données récupérées
		gotDatas: function(datas){
    		store = new Store({
    			data: datas,
    			getIdentity : function(object){
    				return object.id
    			}
    		});
    		store.getChildren = lang.hitch(this,this.getChildren);
    		//l'observable permet de conserver le DOM synchrone au store après une modification
    		this.store = new Observable(store);
    		this.model = new ObjectStoreModel({
				store:  new Observable(this.store),
		        query: { type: 'root' }
    		});
    		this.tree = new ConceptTree({
    			// on rattache le modèle
    			model : this.model,
    			// callback au clic
    			onClick : lang.hitch(this,this.onClick),
    			// gestion d'un tooltip avec le détail du concept
    			onMouseOver: lang.hitch(this,this.showTooltip),
    			onMouseOut: lang.hitch(this,this.hideTooltip),
    		});
    		// l'arbre est prêt, on l'annonce !
    		// il est judicieux de faire le raccrochement au DOM et le startup en réponse à cet event
    		topic.publish("ConceptsTree","ConceptsTree","ready",{});
       },
       
       // Récupération des enfants
       getChildren: function(object){
    	   switch(object.type){
    	   		// Root : c'est le traitement dans gotDatas, on l'a déjà
    	   		case 'root' :
    	   			if(this.parameters.conceptSchemes){
//    	   				return this.store.query({parent: object.id, id:this.parameters.conceptSchemes});
    	   				var conceptSchemes = this.parameters.conceptSchemes;
    	   				return this.store.query(function(item){
    	   					var flag = true;
    	   					flag = item.parent == object.id;
    	   					if(!flag) return false;
    	   					for(var i=0 ; i<conceptSchemes.length ; i++){
    	   						 flag = item.id == conceptSchemes[i];
    	   						 if(flag){
    	   							 return true;
    	   						 }
    	   					}
    	   					return flag;
    	   				});
    	   			}else{
    	   				return this.store.query({parent: object.id});
    	   			}
    	   		// Pagin : Pas logique de cliquer dessus, mais dans le doute, on ne renvoie rien!
    	   		case 'pagin' :
    	   			return this.store.query({parent: object.id});
    	   			break;
    	   		// Schéma et Concept, passage par deferred
    	   		default : 
    	   			var deferred = new Deferred();
					//les termes spécifiques
					var url = './ajax.php?module=ajax&categ=concepts_selector&scheme_id='+object.scheme+'&parent_id='+object.id;
					if(object.type == 'scheme'){
						//les tops concepts
						url = './ajax.php?module=ajax&categ=concepts_selector&scheme_id='+object.id;
					}
					xhr.get(url,{
						handleAs : 'json'			
					}).then(lang.hitch(self,function(datas){
						//ajout dans le store
						for(var i=0 ; i<datas.length ; i++){
							this.store.add(datas[i]);
						}
						//on retourne le résultat du deferred
						deferred.resolve(this.store.query({parent: object.id}));
					}));
					// on retourne le promise
					return deferred.promise;
    	   }
       },
		
       // Traitement du clic
       onClick : function(object,node,evt){
    	   // Si élément de pagination, on s'en occupe
    	   if(object.type == 'pagin'){
    		   //on marque le noeud en cours de traitement
    		   node.markProcessing();
    		   //on construit l'URL
    		   var url = './ajax.php?module=ajax&categ=concepts_selector&scheme_id='+object.scheme+'&parent_id='+object.id+'&page='+object.page;
    		   if(object.type == 'scheme'){
    			   url = './ajax.php?module=ajax&categ=concepts_selector&page='+object.page;
    		   }
    		   xhr.get(url,{
    			   handleAs : 'json'			
    		   }).then(lang.hitch(this,function(datas){
    			   // on rajoute dans le store
    			   for(var i=0 ; i<datas.length ; i++){
    				   this.store.add(datas[i]);
    			   }
    			   // on déflague le noeud
    			   node.unmarkProcessing()
    			   //on le retire du store
    			   this.store.remove(object.id);
    			   // Si on n'est pas à la racine de l'arbre, petit rafraissichement du childItems du noeud parent pour déclencher la MAJ du DOM.
    			   if(node.getParent()){
    				  node.getParent().setChildItems(this.store.query({parent: object.parent}))
    			   }
    		   }));
    	   }else{
    		   // Sinon, on balance l'infos à qui veut! 
    		   topic.publish("ConceptsTree","ConceptsTree","item_clicked",{object: object});
    	   }
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
	            var detail = node.item.name;
	            if(node.item.type != "pagin"){
		            if(node.item.detail && node.item.detail != "\n<div class='details'>\n\t\t<table>\n\t\t</table>\t\n</div>"){
		            	detail+= node.item.detail.replace(/<a[^>]+>([^<]+)<\/a>/gim,"$1")
		            }
		            dijit.showTooltip(detail, node.labelNode);
	            }
           }
       },
       
       hideTooltip: function(event){
           var node = dijit.getEnclosingWidget(event.target);
           dijit.hideTooltip(node.labelNode);
       }
    });
});