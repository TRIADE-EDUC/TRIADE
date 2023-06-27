
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EntityTree.js,v 1.10 2018-09-25 09:49:57 tsamson Exp $


define(["dojo/_base/declare",
        "apps/pmb/tree_interface/EntityTree",
        "dojo/store/Memory",
        "dijit/tree/ObjectStoreModel",
        "dojo/_base/lang",
        "dijit/form/Button",
        "dojo/dom-construct",
        "dojo/topic",
        "dojox/widget/Standby",
        "dijit/tree/dndSource",
        "dojo/dom",
        "dojo/aspect",
        "dojo/store/Observable"
], function(declare, EntityTree, Memory, ObjectStoreModel, lang, Button, domConstruct, topic, Standby, dndSource, dom, aspect, Observable){
    return declare([EntityTree], {
        id : 'frbrTree',
        currentParentId: 0,
        showRoot: false,
        dndController: dndSource,
        betweenThreshold: 5,
        constructor: function(){
        },
        initObjectStore: function(){
            //ModelStore le modèle autour duquel est articulé l'arbre
            this.model = new ObjectStoreModel({
                store: this.memoryStore,
                labelType: 'html',
                query: {root:true},
                mayHaveChildren : function(item) {
                    return true;
                },
                onChildrenChange : lang.hitch(this, this.childrenChanged)
            });
        },
        
//        initMemoryStore: function(){
//        	this.inherited(arguments);
////        	aspect.around(this.memoryStore, "put", function(originalPut){
////    			return function(obj, options){
////    				if(options && options.parent){
////    					obj.parent = options.parent.id;
////    				}
////    				return originalPut.call(this.memoryStore, obj, options);
////    			}
////    		});
////        	this.memoryStore = new Observable(this.memoryStore);
//        },
//        
        checkAcceptance: function(source, node) {
            var item = source.tree.selectedItem;
            if (item.entity_type == 'facet' || item.entity_type == 'segment') {
                return true;
            }
            return false;
        },

        checkItemAcceptance: function(target, source, position) {
            var target_item = dijit.getEnclosingWidget(target).item;
            var current_item = dijit.getEnclosingWidget(target).tree.selectedItem;
            switch(current_item.entity_type){
            	case 'facet':
	            	switch (target_item.entity_type) {
	                    case 'facet' :
	                    case 'segment' :
	                        if (this.tree.getType(target_item.type) == this.tree.getType(current_item.type)) {
	                            return true;
	                        }
	                        break;
	                }	
	            	break;
            	case 'segment':
            		if((target_item.parent == current_item.parent) && (position != "over")){
            			return true;	
            		}
            		return false;
            }
            return false;
        },
        
        childrenChanged: function(parent, children) {
        	console.log(arguments);
        },

        postCreate:function(){
            this.inherited(arguments);
        },

        handleEvents: function(evtType,evtArgs){
            switch(evtType){
                case 'formLoaded':
                    this.hidePatience();
                    break;
                case 'parentChange':
                    this.focusParent(evtArgs.parentId);
                    break;
                case 'checkChildrenToDelete':
                    this.checkChildrenToDelete(evtArgs);
                    break;
            }
        },
        formatData : function(data) {
            var formatData = [];
            var fakeRootId = this.getUniqueId();
            formatData.push({
                id: fakeRootId,
                root: true,
                name: 'FakeRoot',
            });

            formatData.push({
                id: 'universes',
                name: 'Univers de recherche',
                parent: fakeRootId,
                link_edit: data.creation_links.universe,
                entity_type: 'universe',
                link_save: data.save_links.universe
            });

            formatData.push({
                id: 'facet',
                name: 'Facettes',
                entity_type: 'facet',
                parent: fakeRootId
            });

            formatData.push({
                id: 'search_perso',
                name: 'Recherches',
                parent: fakeRootId,
                entity_type: 'search_perso',
                link_edit: data.creation_links.search,
                link_save: data.save_links.search
            });
            var facetsNode = this.generateTypeNodes('facet', {link_edit: data.creation_links.facet, link_save: data.save_links.facet});
            var searchNode = this.generateTypeNodes('search_perso', {link_edit: data.creation_links.search, link_save: data.save_links.search});

            formatData = formatData.concat(facetsNode);
            formatData = formatData.concat(searchNode);
            if(typeof data.universes != "undefined"){
                for(var key in data.universes){
                    data.universes[key].parent = 'universes';
                    data.universes[key].id = 'universe_'+key;
                    formatData.push(data.universes[key]);
                    for(var subKey in data.universes[key].segments){
                    	data.universes[key].segments[subKey].parent = 'universe_'+key;
                        formatData = formatData.concat(this.generateRootNodes(data, 'facet', key, subKey, 'Facettes'));
                        formatData = formatData.concat(this.generateRootNodes(data, 'search_perso', key, subKey, 'Recherches'));

                        data.universes[key].segments[subKey].id = 'universe_'+key+'_'+subKey;
                        formatData.push(data.universes[key].segments[subKey]);
                    };
                };
            }
            if(typeof data.facet != "undefined"){
                for(var key in data.facet){
                    var facet_type = data.facet[key].type;
                    data.facet[key].parent = 'facet_' + this.getType(facet_type),
                    data.facet[key].id = data.facet[key].parent + '_' + key;
                    data.facet[key].link_edit = data.creation_links.facet + '&segment_type=' + this.getType(facet_type) + '&id=' + key,
                    data.facet[key].link_save = data.save_links.facet + '&segment_type=' + this.getType(facet_type) + '&id=' + key,
                    formatData.push(data.facet[key]);
                }
            }

//            if(typeof data.search_perso != "undefined"){
//                for(var key in data.search_perso){
//                    data.search_perso[key].parent = researchRootId;
//                    formatData.push(data.search_perso[key]);
//                }
//            }

            return formatData;
        },

        checkChildrenToDelete : function(params) {
            var childrenItems = this.getChildrenItems(params);
            if (childrenItems) {
                if (confirm(pmbDojo.messages.getMessage('frbr', 'frbr_delete_recursive'))) {
                    topic.publish('formButton', 'deleteNode', {id : params.id, type : params.type, recursive : 1});
                }
            } else {
                topic.publish('formButton', 'deleteNode', {id : params.id, type : params.type, recursive : 0});
            }
        },

        getLabel : function(item){
            var label = this.model.getLabel(item);
            switch(item.entity_type){
                case 'search_perso':
                    return '<i class="fa fa-search" aria-hidden="true"></i>&nbsp;<span class="leafLabel">'+label+'</span>';
                case 'facet':
                    return '<i class="fa fa-check-square-o" aria-hidden="true"></i>&nbsp;<span class="leafLabel">'+label+'</span>';
                case 'universe':
                    return '<i class="fa fa-ravelry" aria-hidden="true"></i>&nbsp;<span class="leafLabel">'+label+'</span>';
                case 'segment':
                    return '<i class="fa fa-arrows-h" aria-hidden="true"></i>&nbsp;<span class="leafLabel">'+label+'</span>';
                default:
                    return '<i class="fa fa-database" aria-hidden="true"></i>&nbsp;<span class="leafLabel">'+label+'</span>';
            }
        },
        getUniqueId: function(){
            return (this.currentParentId++)+10000;
        },
        //Ici type est "facets" ou "search_perso"
        generateRootNodes: function(data, type, key, subKey, name){
            /**
             * TODO: Factoriser la création des clÃ©s racines pour les segments.
             */
            var formatData = [];
            formatData.push({
                id: 'universe_'+key+'_'+subKey+'_'+type,
                name: name,
                parent: 'universe_'+key+'_'+subKey,
                entity_type: type,
                type: data.universes[key].segments[subKey].type
            });
            data.universes[key].segments[subKey][type].forEach(element => {
                formatData.push({
                    id: 'universe_'+key+'_'+subKey+'_'+type+'_'+element,
                    name: data[type][element].name,
                    link_edit: data[type][element].link_edit + '&segment_type=' + this.getType(data[type][element].type),
                    link_save: data[type][element].link_save + '&segment_type=' + this.getType(data[type][element].type),
                    real_id: data[type][element].real_id,
                    parent:'universe_'+key+'_'+subKey+'_'+type,
                    entity_type: type
                });
            });

            return formatData;
        },
        generateTypeNodes: function(parentID, links){
            var formatData = [];
            var types = {
                 'record' : 'Notices',
                 'author' : 'Auteur',
                 'category' : 'Categorie',
                 'publisher' : 'Editeur',
                 'collection' : 'Collection',
                 'subcollection' : 'Sous-collection',
                 'serie' : 'Serie',
                 'titre_uniforme' : 'Titre uniforme',
                 'indexint' : 'Indexation',
                 'concept': 'Concept',
                 'authperso': 'Autorite personnalisee'
            };
            Object.keys(types).forEach((type) => {
                formatData.push({
                    id : parentID + '_' + this.getType(type),
                    name : types[type],
                    link_edit: links.link_edit + '&segment_type=' + this.getType(type),
                    link_save: links.link_save + '&segment_type=' + this.getType(type),
                    parent: parentID
                });
            });
            return formatData;
        },
        onClick: function(item){
            if(item.link_edit){
                topic.publish("EntityTree","leafClicked",item);
                this.setPatience();
            }
        },
        getType: function(type){
            switch(type){
                case '1':
                case '11':
                case 'record':
                case 'notices':
                case 'notice':
                    return 1;
                case 'author':
                case 'authors':
                case 'auteurs':
                case 'auteur':
                case '2':
                    return 2;
                case 'category':
                case 'categories':
                case 'categorie':
                case '3':
                    return 3;
                case 'publisher':
                case 'publishers':
                case 'editeur':
                case 'editeurs':
                case '4':
                    return 4;
                case 'collection':
                case 'collections':
                case '5':
                    return 5;
                case 'subcollection':
                case 'subcollections':
                case '6':
                    return 6;
                case 'serie':
                case 'series':
                case '7':
                    return 7;
                case 'titre_uniforme':
                case 'titres_uniformes':
                case 'works':
                case 'work':
                case '8':
                    return 8;
                case 'indexint':
                case '9':
                    return 9;
                case 'concept' :
                case 'concepts' :
                case '17' :
                    return 17;
                case 'authperso':
                case 'authpersos':
                case '12':
                    return 12;
            }
        }
    });
});

