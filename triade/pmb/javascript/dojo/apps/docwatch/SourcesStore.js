// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SourcesStore.js,v 1.10 2019/03/13 14:48:22 dgoron Exp $


define(["dojo/_base/declare", "apps/pmb/Store", "dojo/topic", "dojo/_base/lang","dojo/request/xhr"], function(declare, PMBStore, topic, lang, xhr){
	return declare([PMBStore], {
		idProperty:"id",
		constructor:function(){
			this.inherited(arguments);
			topic.subscribe("sourcesListUI", lang.hitch(this, this.handleEvents));
			topic.subscribe("sourceUI", lang.hitch(this, this.handleEvents));
			topic.subscribe("source",lang.hitch(this,this.handleEvents));
			topic.subscribe("itemsStore", lang.hitch(this,this.handleEvents));
			topic.subscribe("watchesUI", lang.hitch(this, this.handleEvents));
			topic.subscribe("duplicateSource", lang.hitch(this, this.handleEvents));
		},
		handleEvents:function(evtType, evtArgs){
			//console.log('sourceStore', evtType, evtArgs);
			switch(evtType){
				case "saveSource":
					this.saveSource(evtArgs);
					break;
				case "duplicateSource":
					this.duplicateSource(evtArgs);
					break;
				case "deleteSource":
					this.deleteSource(evtArgs.sourceId);
					break;
				case "gotItems":
					if(evtArgs.sources_updated){
						this.updateSources(evtArgs.sources_updated);
					}
					break;
				case 'actions':
				/** TODO: do something **/
				break;
				case 'sourcesAsked':
					this.sendSources(evtArgs);
					break;
			}
		},
		needSources:function(watchId){
			var retourGet = this.query({num_watch:watchId});
			if(retourGet.length){
				topic.publish("sourcesStore", "gotSources", {sources:retourGet.sources});
			}else{
				xhr(this.url+'&action=get_sources&watch_id='+watchId, {
					handleAs:'json',
				}).then(lang.hitch(this, this.gotSources));
			}
		},
		gotSources:function(sourcesAjax){
			this.setDataAjax(sourcesAjax);
			topic.publish("sourcesStore", "gotSources");
		},
		
		saveSource: function(item){
			xhr(this.url+"&action=save_source",{
				handleAs: "json",
				method: "post",
				data: item
			}).then(lang.hitch(this,this.savedSource,item));
		},
		
		savedSource: function(item,response){
			if(response.result){
				var searched = this.query({id:response.response.id});
				if(searched.length > 0){
					this.put(response.response,{
						overwrite:true
					});
				}else{
					this.add(response.response);
				}
				topic.publish("sourcesStore","sourceSaved", {source : response.response});
			}
		},
		
		duplicateSource: function(item){
			xhr(this.url+"&action=duplicate_source",{
				handleAs: "json",
				method: "post",
				data: item
			}).then(lang.hitch(this,this.savedSource,item));
		},
		
		deleteSource: function(SourceId){
			xhr(this.url+"&action=delete_source&id="+SourceId,{
				handleAs: "json"
			}).then(lang.hitch(this,this.deletedSource));
		},
		
		deletedSource: function(response){
			if(response.result){
				var searched = this.query({id:response.elementId});
				this.remove(this.getIdentity(searched[0]));
				topic.publish("sourcesStore","sourceDeleted", {
					sourceId: response.elementId
				});
			}else {
				topic.publish("sourcesStore","deleteSourceError",{message: response.response})
			}
		},		
		  
		setDataAjax:function(dataAjax){
			for(var i=0 ; i<dataAjax.sources.length ; i++){
				if(this.data.length == 0){
					this.setData([dataAjax.sources[i]]);
				}else{
					this.add(dataAjax.sources[i])
				}  
			}
			if(this.data.length == 0){
				this.setData([{watch_id:dataAjax.watch_id}]);
			}else{
				this.add({watch_id:dataAjax.watch_id});
			}  
		},
		updateSources: function(sources){
			if(sources.length > 0){
				for(var i=0 ; i<sources.length ; i++){
					var query = this.query({id:sources[i].id});
					if(query.length > 0){
						query[0].last_date = sources[i].last_date;
						query[0].formated_last_date = sources[i].formated_last_date;
					}
				}
				topic.publish("sourcesStore", "needRefresh");
			}
		},
		sendSources:function(evtArgs){
			var retour = this.query({num_watch:evtArgs.watchId});
			if(retour.length > 0){
				topic.publish('sourcesStore','askedSources', {sources:retour});
			}
		}
	});
});