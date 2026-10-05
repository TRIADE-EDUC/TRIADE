// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ItemsStore.js,v 1.29 2020/10/01 07:41:42 dgoron Exp $


define(["dojo/_base/declare", "apps/pmb/Store", "dojo/topic", "dojo/_base/lang","dojo/request/xhr","dojo/_base/json"], function(declare, PMBStore, topic, lang, xhr, json){
	
	  return declare([PMBStore], {
		  idProperty:"id",
		  constructor:function(){
			this.inherited(arguments);
			topic.subscribe("itemsListUI", lang.hitch(this,this.handleEvents));
			topic.subscribe("itemUI", lang.hitch(this,this.handleEvents));
		  },
		  handleEvents:function(evtType, evtArgs){
			  //console.log('itemStore', evtType, evtArgs);
			  switch(evtType){
			  case "itemMarkAsRead":
				  this.markItemAsRead(evtArgs.itemId);
				  break;
			  case "itemMarkAsUnread":
				  this.markItemAsUnread(evtArgs.itemId);
				  break;
			  case "itemMarkAsInteresting":
				  this.markItemAsInteresting(evtArgs.itemId);
				  break;
			  case "itemMarkAsUninteresting":
				  this.markItemAsUninteresting(evtArgs.itemId);
				  break;
			  case "itemCreateNotice":
				  this.itemCreateNotice(evtArgs.itemId);
				  break;
			  case "itemSeeNotice":
				  this.itemSeeNotice(evtArgs.itemId);
				  break;				  
			  case "itemCreateSection":
				  this.itemCreateSection(evtArgs.itemId);
				  break;
			  case "itemSeeSection":
				  this.itemSeeSection(evtArgs.itemId);
				  break;
			  case "itemCreateArticle":
				  this.itemCreateArticle(evtArgs.itemId);
				  break;
			  case "itemSeeArticle":
				  this.itemSeeArticle(evtArgs.itemId);
				  break;
			  case "itemIndex":
				  this.itemIndex(evtArgs.itemId,evtArgs.data);
				  break;		
			  case "itemDelete":
				  this.deleteItem(evtArgs.itemId);
				  break;
			  case "updateWatch":
				  this.updateWatch(evtArgs.watchId);
				  break;
			  case "itemRestore":
				  this.itemRestore(evtArgs.itemId);
				  break;
			  case "itemReIndex":
				  this.itemReIndex(evtArgs.itemId,evtArgs.data);
				  break;
			  }
		  },
		  needItems:function(watchId){
			  //Les items de cette veille ont d�j� �t� r�cup�r� 
			  if(this.query({watch_id:watchId}).length != 0){
				  topic.publish("itemsStore", "gotItems", {watchId:watchId,formated_last_date:itemsAjax.formated_last_date});
			  }else{//Nous n'avons pas encore les items associ�s a cette veille
				  xhr(this.url+'&action=get_items&watch_id='+watchId, {
						handleAs:'json',
				  }).then(lang.hitch(this, this.gotItems, watchId));  
			  }
		  },
		  gotItems:function(watchId, itemsAjax){
			  this.setDataAjax(itemsAjax.items);
			  topic.publish("itemsStore", "gotItems", {sources_updated:itemsAjax.sources_updated, watchId:watchId, formated_last_date:itemsAjax.formated_last_date, deflt_docwatch_watch_filter_deleted:itemsAjax.deflt_docwatch_watch_filter_deleted});
		  },
		  markItemAsRead:function(itemId) {
			  if(itemId) {
				  xhr(this.url+'&action=markItemAsRead&item_id='+itemId, {
						handleAs:'json',
				  }).then(lang.hitch(this, this.setItem));
			  }
		  },
		  markItemAsUnread:function(itemId) {
			  if(itemId) {
				  xhr(this.url+'&action=markItemAsUnread&item_id='+itemId, {
						handleAs:'json',
				  }).then(lang.hitch(this, this.setItem));
			  }
		  },
		  markItemAsInteresting:function(itemId) {
			  if(itemId) {
				  xhr(this.url+'&action=markItemAsInteresting&item_id='+itemId, {
						handleAs:'json',
				  }).then(lang.hitch(this, this.setItem));
			  }
		  },
		  markItemAsUninteresting:function(itemId) {
			  if(itemId) {
				  xhr(this.url+'&action=markItemAsUninteresting&item_id='+itemId, {
						handleAs:'json',
				  }).then(lang.hitch(this, this.setItem));
			  }
		  },
		  itemCreateNotice:function(itemId) {
			  if(itemId) {
				  xhr(this.url+'&action=itemCreateNotice&item_id='+itemId, {
						handleAs:'json',
				  }).then(lang.hitch(this, this.successfullyCreated));
			  }
		  },
		  itemSeeNotice:function(itemId) {
			  if(itemId) {
			  }
		  },
		  itemCreateSection:function(itemId) {
			  if(itemId) {
				  xhr(this.url+'&action=itemCreateSection&item_id='+itemId, {
						handleAs:'json',
				  }).then(lang.hitch(this, this.successfullyCreated));
			  }
		  },
		  itemSeeSection:function(itemId) {
			  if(itemId) {
			  }
		  },
		  itemCreateArticle:function(itemId) {
			  if(itemId) {
				  xhr(this.url+'&action=itemCreateArticle&item_id='+itemId, {
						handleAs:'json',
				  }).then(lang.hitch(this, this.successfullyCreated));
			  }
		  },
		  itemDeleteCreatedNotice:function(noticeId) {
			  if(noticeId) {
				  xhr(this.url+'&action=itemDeleteCreatedNotice&notice_id='+noticeId);
			  }
		  },
		  itemSeeArticle:function(itemId) {
			  if(itemId) {
			  }
		  },
		  itemIndex:function(itemId,data) {
			  if(itemId) {
				  xhr(this.url+'&action=itemIndex&item_id='+itemId, {
						handleAs:'json',
						method: "post",
						data: "data="+json.toJson(data)
				  }).then(lang.hitch(this, this.itemIndexAck));
			  }
		  },
		  itemReIndex:function(itemId,data) {
			  if(itemId) {
				  xhr(this.url+'&action=itemIndex&item_id='+itemId, {
						handleAs:'json',
						method: "post",
						data: "data="+json.toJson(data)
				  }).then(					  
						  lang.hitch( this,function(itemId,response){
								  		this.setItem(response);
								  		topic.publish('itemsStore','redrawItem',{item:this.query({id:itemId})[0]})
						  },itemId
						  )
				);
			  }
		  },
		  itemIndexAck:function(response) {
			  this.setItem(response);
			  topic.publish("itemsStore", "itemIndexAck", response.item);
		  },
		  deleteItem:function(itemId) {
			  if(itemId) {
				  xhr(this.url+'&action=deleteItem&item_id='+itemId, {
						handleAs:'json',
				  }).then(lang.hitch(this, this.delItem));
			  }
		  },
		  setItem:function(response){
			  if (response.state) {
				  if(response.action == "markItemAsUninteresting" || response.action == "markItemAsInteresting" || response.action == "itemRestore"){
					  var item = this.query({id:response.item.id})[0];
					  if(item){
						  if (response.action == "itemRestore") {
							  item.status = 1;
						  }
						  if (response.action == "markItemAsUninteresting") {
							  item.interesting = 0;
						  }
						  if (response.action == "markItemAsInteresting") {
							  item.interesting = 1;
						  }
					  }
				  }
				  this.put(response.item,{
						overwrite:true
				  });
				  if(response.action == "markItemAsUninteresting" || response.action == "markItemAsInteresting" || response.action == "itemRestore"){
					  topic.publish("itemsStore", "itemModified", {itemUIRefresh:true, itemId:response.item.id});
				  }else{
					  topic.publish("itemsStore", "itemModified", {itemId:response.item.id});  
				  } 
				  
			  }
		  },
		  
		  successfullyCreated: function(response){
			  if(response.state){
				  this.put(response.item,{
						overwrite:true
				  });
				  var item = this.query({id:response.item.id})[0];
				  switch(response.action){
					  case "itemCreateNotice":
						  if (response.record.is_doublon == 1) {
							  if (confirm(this.getMsg("dsi_js_item_action_create_record_doublon_confirm"))) {
								  item.record_link = response.record.link;
							  } else {
								  response.item.num_notice=0;
								  this.itemDeleteCreatedNotice(response.record.id);
							  }
						  } else {
							  item.record_link = response.record.link;
						  }
						  break;
					  case "itemCreateArticle":
						  item.article_link = response.article.link;
						  break;
					  case "itemCreateSection":
						  item.section_link = response.section.link;
						  break;
				  }
				  topic.publish("itemsStore", "itemModified", {itemUIRefresh:true, itemId:response.item.id});
			  }
		  },
		  
		  delItem:function(response){
			  if (response.state) {
				  var item = this.query({id:response.item.id})[0];
				  if(item){
				  	item.status = 2;
				  	topic.publish("itemsStore", "itemModified", {itemUIRefresh:true, itemId:response.item.id});
			  	  }
			  }
		  },
		  setDataAjax:function(dataAjax){
			  for(var i=0 ; i<dataAjax.items.length ; i++){
				  if(this.data.length == 0){
					  this.setData([dataAjax.items[i]]);
				  }else{
					  this.add(dataAjax.items[i])
				  }  
			  }
			  if(this.data.length == 0){
				  this.setData([{watch_id:dataAjax.watch_id, outdated:false}]);
			  }else{
				  this.add({watch_id:dataAjax.watch_id, outdated:false});
			  }  
		  },
		  updateWatch: function(watchId,needItems=true){
			 var items = this.query({num_watch:watchId});
			 for(var i=0 ; i<items.length ; i++){
				 this.remove(items[i].id);
			 }
			 var watch = this.query({watch_id:watchId})[0];
			 if(watch!=undefined){
				 this.remove(watch.id);	 
			 }
			 if(needItems) {
				 this.needItems(watchId);
			 }
		  },
		  itemRestore:function(itemId){
			  if(itemId) {
				  xhr(this.url+'&action=itemRestore&item_id='+itemId, {
						handleAs:'json',
				  }).then(lang.hitch(this, this.setItem));
			  }
		  },
		  getMsg: function(key){			 
			  return pmbDojo.messages.getMessage("dsi",key);
		  },
	  });
});