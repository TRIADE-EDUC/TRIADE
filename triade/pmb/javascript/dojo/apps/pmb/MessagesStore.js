// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MessagesStore.js,v 1.7 2021/02/16 09:47:27 btafforeau Exp $


define(["dojo/_base/declare", "apps/pmb/Store", "dojo/topic", "dojo/_base/lang","dojo/request/xhr", "dojo/store/Memory"], function(declare, PMBStore, topic, lang, xhr, Memory){
	
	  return declare([PMBStore], {
		  idProperty:"code",
		  allowNewGroup:true,
		  allowAjaxLoading:true,
		  groups:null,
		  waiting:null,
		  constructor : function() {
			  this.inherited(arguments);
			  this.groups = new Memory();
			  this.groups.idProperty = "group";
			  if (arguments[0].messages) {
				  let msg = {};
				  let group = '';
				  for (let i = 0; i < arguments[0].messages.length; i++) {
					  group = arguments[0].messages[i].group;
					  if (!msg[group]) {
						  msg[group] = new Array();
					  }
					  msg[group].push(arguments[0].messages[i]);
				  }
				  for (group in msg) {
					  if (this.groups.data.length == 0) {
						  this.groups.setData([{group : group, loaded : false, loading : true}]);
					  } else {
						  this.groups.add({group : group, loaded : false, loading : true});
					  }
					  this.gotMessages(group, msg[group]);
					  this.groups.query({group:group})[0].loaded = true;
				  }
			  }
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
				  xhr(this.url+'&action=get_messages&group='+group, {
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
	  });
});