// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FileStore.js,v 1.2 2018/11/30 13:53:07 dgoron Exp $


define(["dojo/_base/declare", "apps/pmb/Store", "dojo/request/xhr", "dojo/_base/lang", "dojo/topic"], function(declare,PMBStore, xhr, lang, topic){
	return declare([PMBStore], {
		idProperty: "treeID",
		
		constructor: function(){
			topic.subscribe("FileUI",lang.hitch(this,this.handleEvents));
			topic.subscribe("FilesUI",lang.hitch(this,this.handleEvents));
			topic.subscribe("SubstFileContentUI",lang.hitch(this,this.handleEvents));
		},
		
		handleEvents: function(evtType,evtArgs){
			switch(evtType){
				case "savedFile":
					this.savedFile(evtArgs);
					break
				case "deletedFile":
					this.deletedFile(evtArgs);
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
			topic.publish("FileStore","got_datas",{url:this.url});
		},
		
		flatDatas:function(object,parentID){
			var item = {
				id: object.id,
				type: object.type,
				title: object.title
			};
			switch(object.type){
				case "folder" :
					item.parent_folder= object.num_parent;
					break;
				case "file":
					item.parent_folder= parentID;
					break;
				case "substFile":
					item.parent_file= parentID;
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
			if(object.files && object.files.length > 0){
				for(var i=0 ; i<object.files.length ; i++){
					this.flatDatas(object.files[i],object.id);
					if(object.files[i].children && object.files[i].children.length > 0){
						for(var j=0 ; j<object.files[i].children.length ; j++){
							this.flatDatas(object.files[i].children[j],object.files[i].id);
						}
					}
				}
			}
		},

		getChildren: function(object){
			var children = [];
			switch(object.type){
				case "folder" :
					children = this.query({parent_folder: object.id});
					break;
				case "file":
					children = this.query({parent_file: object.id});
					break;
				case "substFile":
					children = this.query({parent_substFile: object.id});
					break;
			}
			return children;
		},
		
		getFolders: function() {
			var elements = this.query({type: "folder"});
			var folders = [];
			for(var i=0 ; i<elements.length ; i++){
				folders.push({
					value: elements[i].id,
					label: elements[i].title
				});
			}
			return folders;
		},
		savedFile: function(response){
			if(response.status){
				var tree_element = {
						id:response.elementId, 
						type:'substFile', 
						title: '_subst.xml',
						parent_file:response.elementId.replace('_subst.xml', '.xml')
				};
				var searched = this.query(tree_element);
				if(searched.length > 0){
					tree_element.treeID = searched[0].treeID;
				}
				this.flatDatas(tree_element,tree_element.parent_file);
				topic.publish("FileStore","needTreeRefresh", {itemTreeToSelected:tree_element});
				topic.publish("FileStore","fileSaved", {});
				
			}
		},
		deletedFile: function(response){
			if(response.status){
				var searched = this.query({id:response.elementId});
				this.remove(this.getIdentity(searched[0]));
				topic.publish("FileStore","needTreeRefresh", {});
				topic.publish("FileStore","fileDeleted", {});
			}else {
				topic.publish("FileStore","deleteFileError",{})
			}
		},
	});
});