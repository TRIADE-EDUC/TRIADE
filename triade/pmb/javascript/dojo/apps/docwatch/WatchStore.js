// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: WatchStore.js,v 1.29 2019/10/25 07:56:16 dgoron Exp $


define(["dojo/_base/declare", "apps/pmb/Store", "dojo/request/xhr", "dojo/_base/lang", "dojo/topic"], function(declare,PMBStore, xhr, lang, topic){
	return declare([PMBStore], {
		idProperty: "treeID",
		
		constructor: function(){
			topic.subscribe("category",lang.hitch(this,this.handleEvents));
			topic.subscribe("watch",lang.hitch(this,this.handleEvents));
			topic.subscribe("sourcesStore", lang.hitch(this,this.handleEvents));
			topic.subscribe("watchesUI",lang.hitch(this,this.handleEvents));
			topic.subscribe("itemsListUI",lang.hitch(this,this.handleEvents));
		},
		
		handleEvents: function(evtType,evtArgs){
			//console.log('watchesStore', evtType, evtArgs);
			switch(evtType){
				case "saveCategory":
					this.saveCategory(evtArgs);
					break
				case "deleteCategory":
					this.deleteCategory(evtArgs.categoryId);
					break;
				case "saveWatch":
					this.saveWatch(evtArgs);
					break
				case "deleteWatch":
					this.deleteWatch(evtArgs.watchId);
					break;
				case "purgeItemsMarkAsDeleted":
					this.purgeItemsMarkAsDeleted(evtArgs.watchId);
					break;
				case "sourceSaved":
					this.sourceSaved(evtArgs.source);
					break;
				case "sourceDeleted":
					this.sourceDeleted(evtArgs.sourceId);
					break;
				case "updateChildren":
					if (evtArgs.children) {
						this.updateChildren(evtArgs.id,evtArgs.children,evtArgs.type);
					}
					break;
			}
		},
		
		gotDatas:function(datas){
			this.flatDatas(datas[0]);
			topic.publish("store","got_datas",{url:this.url});
		},
		
		flatDatas:function(object,parentID){
			var item = {
				id: object.id,
				type: object.type,
				title: object.title
			};
			
			switch(object.type){
				case "category" :
					item.parent_category= object.num_parent;
				break;
				case "watch":
					item.parent_category= parentID;
					var retourQuery = this.query({id:item.id,parent_category:item.parent_category, type:"watch"});
					if(retourQuery.length > 0){
						object.treeID = retourQuery[0].treeID;
					}
					item.ttl = object.ttl;
					item.desc = object.desc;	
					item.logo_url = object.logo_url;
					item.record_default_type = object.record_default_type;
					item.record_default_status = object.record_default_status;
                    item.record_default_index_lang = object.record_default_index_lang;
                    item.record_default_lang = object.record_default_lang;
                    item.record_default_lang_libelle = object.record_default_lang_libelle;
                    item.record_default_is_new = object.record_default_is_new;
					item.article_default_parent = object.article_default_parent;
					item.article_default_content_type = object.article_default_content_type;
					item.article_default_publication_status = object.article_default_publication_status;
					item.section_default_parent = object.section_default_parent;
					item.section_default_content_type = object.section_default_content_type;
					item.section_default_publication_status = object.section_default_publication_status;
					item.formated_last_date = object.formated_last_date;
					item.opac_link = object.opac_link;
					item.allowed_users = object.allowed_users;
					item.nb_sources = object.nb_sources;
					item.watch_rss_link = object.watch_rss_link;
					item.watch_rss_lang = object.watch_rss_lang;
					item.watch_rss_copyright = object.watch_rss_copyright;
					item.watch_rss_editor = object.watch_rss_editor;
					item.watch_rss_webmaster = object.watch_rss_webmaster;
					item.watch_rss_image_title = object.watch_rss_image_title;
					item.watch_rss_image_website = object.watch_rss_image_website;
					item.boolean_expression = object.boolean_expression;
					break;
				case "source":
					item.parent_watch= parentID;
					var retourQuery = this.query({id:item.id,parent_watch:item.parent_watch, type:"source"});
					if(retourQuery.length > 0){
						object.treeID = retourQuery[0].treeID;
					}
					break;
			}
			if(object.treeID){
				item.treeID = object.treeID;
				this.put(item,{
					overwrite:true
				});
			}else{
				this.add(item);
			}
			
			if(object.children && object.children.length > 0){
				for(var i=0 ; i<object.children.length ; i++){
					this.flatDatas(object.children[i]);
				}
			}
			if(object.watches && object.watches.length > 0){
				for(var i=0 ; i<object.watches.length ; i++){
					this.flatDatas(object.watches[i],object.id);
				}
			}
			if(object.sources && object.sources.length > 0){
				for(var i=0 ; i<object.sources.length ; i++){
					this.flatDatas(object.sources[i],object.id);
				}
			}
		},

		getChildren: function(object){
			var children = [];
			switch(object.type){
				case "category" :
					children = this.query({parent_category: object.id});
				break;
				case "watch":
					children = this.query({parent_watch: object.id});
					break;
			}
			return children;
		},
		
		getWatches: function() {
			var elements = this.query({type: "watch"});
			var watches = [];
			for(var i=0 ; i<elements.length ; i++){
				watches.push({
					value: elements[i].id,
					label: elements[i].title
				});
			}
			return watches;
		},
		
		getCategories: function() {
			var elements = this.query({type: "category"});
			var categories = [];
			for(var i=0 ; i<elements.length ; i++){
				categories.push({
					value: elements[i].id,
					label: elements[i].title
				});
			}
			return categories;
		},
		
		saveCategory: function(item){
			xhr(this.url+"&action=save_category",{
				handleAs: "json",
				method: "post",
				data: item
			}).then(lang.hitch(this,this.savedCategory,item));
		},
		
		savedCategory: function(item,response){
			if(response.result){
				var searched = this.query({id:response.response.id, type:response.response.type});
				if(searched.length > 0){
					response.response.treeID = searched[0].treeID;
				}
				this.flatDatas(response.response);
				topic.publish("watchStore","needTreeRefresh", {});
				topic.publish("watchStore","categorySaved", {});
			}
		},
		
		deleteCategory: function(categoryId){
			xhr(this.url+"&action=delete_category&id="+categoryId,{
				handleAs: "json"
			}).then(lang.hitch(this,this.deletedCategory));
		},
		
		deletedCategory: function(response){
			if(response.result){
				var searched = this.query({id:response.elementId});
				this.remove(this.getIdentity(searched[0]));
				topic.publish("watchStore","needTreeRefresh", {});
				topic.publish("watchStore","categoryDeleted", {});
			}else {
				topic.publish("watchStore","deleteCategoryError",{message: response.response})
			}
		},
		
		saveWatch: function(item){
			xhr(this.url+"&action=save_watch",{
				handleAs: "json",
				method: "post",
				data: item
			}).then(lang.hitch(this,this.savedWatch,item));
		},
		
		savedWatch: function(item,response){
			if(response.result){
				var searched = this.query({id:response.response.id, type:response.response.type});
				if(searched.length > 0){
					response.response.treeID = searched[0].treeID;
					topic.publish("watchStore", "needWatchUpdate", {watchId:response.response.id, needItems:true});
				}
				this.flatDatas(response.response,response.response.num_category);
				topic.publish("watchStore","needTreeRefresh", {itemTreeToSelected:response.response});
				topic.publish("watchStore","watchSaved", {});
				
			}
		},
		
		deleteWatch: function(watchId){
			xhr(this.url+"&action=delete_watch&id="+watchId,{
				handleAs: "json"
			}).then(lang.hitch(this,this.deletedWatch));
		},
		
		deletedWatch: function(response){
			if(response.result){
				var searched = this.query({id:response.elementId});
				this.remove(this.getIdentity(searched[0]));
				topic.publish("watchStore","needTreeRefresh", {});
				topic.publish("watchStore","watchDeleted", {});
				topic.publish("watchStore", "needWatchUpdate", {watchId:response.elementId, needItems:false});
			}else {
				topic.publish("watchStore","deleteWatchError",{message: response.response})
			}
		},
		
		purgeItemsMarkAsDeleted: function(watchId){
			xhr(this.url+"&action=purge_items_mark_as_deleted&id="+watchId,{
				handleAs: "json"
			}).then(lang.hitch(this,this.purgedItemsMarkAsDeleted));
		},
		
		purgedItemsMarkAsDeleted: function(response){
			if(response.result){
				var searched = this.query({id:response.elementId});
				topic.publish("watchStore","needTreeRefresh", {});
				topic.publish("watchStore","itemsMarkAsDeletedPurged", {watchId:response.elementId});
				topic.publish("watchStore", "needWatchUpdate", {watchId:response.elementId, needItems:false});
			}else {
				topic.publish("watchStore","deleteWatchError",{message: response.response})
			}
		},
		
		sourceSaved: function(source){
			var searched = this.query({
				id:source.id, 
				type:"source"
			});
			if(searched.length > 0){
				source.treeID = searched[0].treeID;
			}
			this.flatDatas(source,source.num_watch);
			this.updateNbSources(source.num_watch);
			topic.publish("watchStore","needTreeRefresh", {});
			topic.publish("watchStore", "needWatchUpdate", {watchId:source.num_watch, needItems:true});
		},
		
		sourceDeleted: function(sourceId){
			var searched = this.query({
				id: sourceId
			});
			this.remove(this.getIdentity(searched[0]));
			this.updateNbSources(searched[0].parent_watch);
			topic.publish("watchStore","needTreeRefresh", {});
			topic.publish("watchStore", "needWatchUpdate", {watchId:searched[0].parent_watch, needItems:false});
		},
		
		updateChildren: function(id,children,type) {
			xhr(this.url+"&action=update_children",{
				handleAs: "json",
				method: "post",
				data:"&id="+id+"&type="+type+"&children="+children
			}).then(lang.hitch(this,this.childrenUpdated));
		},
		
		childrenUpdated: function(response){
			if(!response.result){
				topic.publish("watchStore","updateTreeError",{message: response.response})
			}
		},
		
		updateNbSources: function(watchId) {
			var searched = this.query({
				parent_watch:watchId, 
				type:"source"
			});
			var watch = this.query({
				id:watchId, 
				type:"watch"
			});
			if(watch[0]){
				watch[0].nb_sources = searched.length;
				this.put(watch,{
					overwrite:true
			  });
			}
		},
		
	});
});