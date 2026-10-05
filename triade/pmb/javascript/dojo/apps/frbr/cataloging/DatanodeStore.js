// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DatanodeStore.js,v 1.1 2018/01/17 15:01:13 dgoron Exp $


define(["dojo/_base/declare", "apps/pmb/Store", "dojo/request/xhr", "dojo/_base/lang", "dojo/topic"], function(declare,PMBStore, xhr, lang, topic){
	return declare([PMBStore], {
		idProperty: "treeID",
		
		constructor: function(){
			topic.subscribe("Category",lang.hitch(this,this.handleEvents));
			topic.subscribe("Datanode",lang.hitch(this,this.handleEvents));
			topic.subscribe("DatanodesUI",lang.hitch(this,this.handleEvents));
		},
		
		handleEvents: function(evtType,evtArgs){
			switch(evtType){
				case "saveCategory":
					this.saveCategory(evtArgs);
					break
				case "deleteCategory":
					this.deleteCategory(evtArgs.categoryId);
					break;
				case "saveDatanode":
					this.saveDatanode(evtArgs);
					break
				case "deleteDatanode":
					this.deleteDatanode(evtArgs.datanodeId);
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
			topic.publish("DatanodeStore","got_datas",{url:this.url});
		},
		
		flatDatas:function(object,parentID){
			var item = {
				id: object.id,
				type: object.type,
				title: object.title,
				comment: object.comment,
				allowed_users: object.allowed_users
			};
			switch(object.type){
				case "category" :
					item.parent_category= object.num_parent;
				break;
				case "datanode":
					item.parent_category= parentID;
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
			if(object.datanodes && object.datanodes.length > 0){
				for(var i=0 ; i<object.datanodes.length ; i++){
					this.flatDatas(object.datanodes[i],object.id);
				}
			}
		},

		getChildren: function(object){
			var children = [];
			switch(object.type){
				case "category" :
					children = this.query({parent_category: object.id});
				break;
				case "datanode":
					children = this.query({parent_datanode: object.id});
					break;
			}
			return children;
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
				topic.publish("DatanodeStore","needTreeRefresh", {});
				topic.publish("DatanodeStore","categorySaved", {});
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
				topic.publish("DatanodeStore","needTreeRefresh", {});
				topic.publish("DatanodeStore","categoryDeleted", {});
			}else {
				topic.publish("DatanodeStore","deleteCategoryError",{message: response.response})
			}
		},
		
		saveDatanode: function(item){
			xhr(this.url+"&action=save_datanode",{
				handleAs: "json",
				method: "post",
				data: item
			}).then(lang.hitch(this,this.savedDatanode,item));
		},
		
		savedDatanode: function(item,response){
			if(response.result){
				var searched = this.query({id:response.response.id, type:response.response.type});
				if(searched.length > 0){
					response.response.treeID = searched[0].treeID;
					topic.publish("DatanodeStore", "needDatanodeUpdate", {datanodeId:response.response.id, needItems:true});
				}
				this.flatDatas(response.response,response.response.num_category);
				topic.publish("DatanodeStore","needTreeRefresh", {itemTreeToSelected:response.response});
				topic.publish("DatanodeStore","datanodeSaved", {});
				
			}
		},
		
		deleteDatanode: function(datanodeId){
			xhr(this.url+"&action=delete_datanode&id="+datanodeId,{
				handleAs: "json"
			}).then(lang.hitch(this,this.deletedDatanode));
		},
		
		deletedDatanode: function(response){
			if(response.result){
				var searched = this.query({id:response.elementId});
				this.remove(this.getIdentity(searched[0]));
				topic.publish("DatanodeStore","needTreeRefresh", {});
				topic.publish("DatanodeStore","datanodeDeleted", {});
				topic.publish("DatanodeStore", "needDatanodeUpdate", {datanodeId:response.elementId, needItems:false});
			}else {
				topic.publish("DatanodeStore","deleteDatanodeError",{message: response.response})
			}
		},
		
//		updateChildren: function(id,children,type) {
//			xhr(this.url+"&action=update_children",{
//				handleAs: "json",
//				method: "post",
//				data:"&id="+id+"&type="+type+"&children="+children
//			}).then(lang.hitch(this,this.childrenUpdated));
//		},
//		
//		childrenUpdated: function(response){
//			if(!response.result){
//				topic.publish("watchStore","updateTreeError",{message: response.response})
//			}
//		},
		
	});
});