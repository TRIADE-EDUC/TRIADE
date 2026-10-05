// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Store.js,v 1.1 2015/04/07 15:22:05 vtouchard Exp $


define(["dojo/_base/declare", "dojo/store/Memory", "dojo/request/xhr", "dojo/_base/lang", "dojo/topic"], function(declare,Memory, xhr, lang, topic){

	  return declare([Memory], {
		  directInit:true,
		  constructor:function(){
			  if(arguments[0].directInit != undefined){
				  this.directInit = arguments[0].directInit;
			  }
			  if(arguments[0].url){
				  this.url = arguments[0].url;
				  if(this.directInit){
					  this.initDatas();  
				  }
			  }
		  },
		  initDatas:function(){
			if(this.url){
				xhr(this.url+'&action=get_datas', {
					handleAs:'json',
				}).then(lang.hitch(this, this.gotDatas));
			}
		  },
		  gotDatas:function(datas){
			  this.setData(datas);
			  topic.publish("store","got_datas",{url:this.url});
		  },
	  });
});