// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MessagesStore.js,v 1.3.6.2 2024/05/30 08:54:30 jparis Exp $


define(["dojo/_base/declare", "apps/pmb/Store", "dojo/topic", "dojo/_base/lang","dojo/request/xhr", "dojo/store/Memory"], function(declare, PMBStore, topic, lang, xhr, Memory){
	
	  return declare([PMBStore], {
		  idProperty:"code",
		  allowNewGroup:true,
		  allowAjaxLoading:true,
		  groups:null,
		  waiting:null,
		  constructor:function(){
			  this.inherited(arguments);
			  this.groups = new Memory();
			  this.groups.idProperty = "group";
		  },
		  getMessage:function(group, code){
			  if(this.groups.query({group:group}).length == 0 || (this.groups.query({group:group}).length != 0 && this.groups.query({group:group})[0].loaded != true && this.groups.query({group:group})[0].loading != true)){
				if(this.allowAjaxLoading){
					if(this.groups.data.length == 0){
						this.groups.setData([{group:group, loaded:false}]);
					}else{
						this.groups.add({group:group, loaded:false});
					}
					this.initMessages(group);
					this.groups.query({group:group})[0].loaded = true;
					var retourQuery = this.query({group:group, code:code});
					if(retourQuery.length == 0){
						return "";
					}else{
						return retourQuery[0].message;  
					} 
				}else{
					return "";
				}
			}else{
				var retourQuery = this.query({group:group, code:code});
				if(retourQuery.length > 0){
					return retourQuery[0].message;
				}else{
					return "";
				}
			}  
		  },
		  initMessages:function(group){
			  this.groups.query({group:group})[0].loading = true;
			  if(this.groups.query({group:group}).length == 0 || (this.groups.query({group:group}).length != 0 && this.groups.query({group:group})[0].loaded != true)){
				  xhr(this.url+'&action=get_messages&group='+group+'&locale='+dojo.locale+'&v='+this.lastModified, {
						handleAs:'json',
						sync:true,
				  }).then(lang.hitch(this, this.gotMessages, group));  
			  }
		  },
		  gotMessages:function(group, messagesAjax){
			  if(this.data.length == 0){
				  this.setData(messagesAjax);
			  }else{
				  for(var i=0 ; i<messagesAjax.length ; i++){
					  this.add(messagesAjax[i]);  
				  }
			  }
			  this.groups.query({group:group})[0].loading = false;
		  },
		  getMessages:function(group){
			  if(this.groups.query({group:group}).length == 0 || (this.groups.query({group:group}).length != 0 && this.groups.query({group:group})[0].loaded != true && this.groups.query({group:group})[0].loading != true)){
				if(this.allowAjaxLoading){
					if(this.groups.data.length == 0){
						this.groups.setData([{group:group, loaded:false}]);
					}else{
						this.groups.add({group:group, loaded:false});
					}
					this.initMessages(group);
					this.groups.query({group:group})[0].loaded = true;
					var retourQuery = this.query({group:group});
					if(retourQuery.length == 0){
						return "";
					}else{
						return retourQuery;  
					} 
				}else{
					return "";
				}
			}else{
				var retourQuery = this.query({group:group});
				if(retourQuery.length > 0){
					return retourQuery;
				}else{
					return "";
				}
			}  
		  },
	  });
});