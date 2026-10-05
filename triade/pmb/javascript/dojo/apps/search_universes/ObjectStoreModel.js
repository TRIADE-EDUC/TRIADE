
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ObjectStoreModel.js,v 1.2 2020/08/05 12:38:54 qvarin Exp $


define(["dojo/_base/declare",
        "dijit/tree/ObjectStoreModel",
        'dojo/Deferred',
        'dojo/_base/lang',
        "dojo/topic",
        'dojo/request/xhr', 
], function(declare, ObjectStoreModel, Deferred, lang, topic, xhr){
    return declare([ObjectStoreModel], {
    	
        mayHaveChildren : function(item) {
        	var results = this.store.query({parent:item.id});
        	return (results.length > 0);
        },
        
        pasteItem: function(childItem, oldParentItem, newParentItem, bCopy, insertIndex, before){

			
        	var d = new Deferred();
			if(oldParentItem === newParentItem && !bCopy && !before && typeof insertIndex == undefined){
				d.resolve(true);
				return d;
			}

			if(oldParentItem && !bCopy){
				
				this.getChildren(oldParentItem, lang.hitch(this, function(oldParentChildren){
					
					if (childItem.parent != newParentItem.parent && undefined == insertIndex ) {
						
						/**
						 * On Ajoute une facette à un segement dans l'arbre.
						 */
						cloneChildItem = lang.clone(childItem)
						cloneChildItem.id = newParentItem.id+"_"+cloneChildItem.entity_type+"_"+cloneChildItem.real_id;
						var result = this.store.query({parent: newParentItem.id, id: cloneChildItem.id})
						
						if (result.total == 0) {
							cloneChildItem.overwrite = true;
							cloneChildItem.parent = newParentItem.id;
							cloneChildItem.oldParent = oldParentItem.id;
							cloneChildItem.before = before;
							
							this.newItem(cloneChildItem, newParentItem);
							xhr.post(newParentItem.update_facet+"&id_facet="+cloneChildItem.real_id+"&id_universe="+newParentItem.id_universe)
							.then(lang.hitch(this, function(response) {
								topic.publish('ObjectStoreModel', 'updateTree', JSON.parse(response));
							}))
						}
					} else if(childItem.parent == newParentItem.id){
						
						/**
						 * On déplace juste le noeud  dans l'arbre.
						 */
						
						var baseUrl = "./ajax.php?module=admin&categ=search_universes&";

						switch (childItem.entity_type) {
							case 'facet':
								var url = baseUrl+"sub=facet&action=update_order&id="+childItem.real_id+"&order="+insertIndex+"&segment_id="+newParentItem.segment_id+"&id_universe="+newParentItem.id_universe;
								break;
							case 'segment':
								var url = baseUrl+"sub=segment&action=update_order&id="+childItem.real_id+"&order="+insertIndex+"&id_universe="+newParentItem.real_id;
								break;
						}
						
						xhr.post(url)
						.then(lang.hitch(this, function(response) {
							topic.publish('ObjectStoreModel', 'updateTree', JSON.parse(response));
						}))
					}
					
					d.resolve(true);
				}));
			}else{
				d.resolve(this.store.put(childItem, {
					overwrite: true,
					parent: newParentItem,
					oldParent: oldParentItem,
					before: before
				}));
			}

			return d;
        }
    
    });
});

