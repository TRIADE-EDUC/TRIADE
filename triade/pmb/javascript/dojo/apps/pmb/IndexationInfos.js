// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: IndexationInfos.js,v 1.8 2019-02-18 14:17:23 tsamson Exp $


define(["dojo/_base/declare",
        "dojo/_base/lang",
        "dojo/request",
        "dojo/dom",
        "dojo/dom-construct",
        "dojo/on"
], function(declare, lang, request, dom, domConstruct, on){
	
	  return declare(null, {
		  something:null,
		  callback: null,
		  constructor:function(){
			  if(!dom.byId('indexation_infos')){
				  return;
			  }
			  this.call();
			  on(window,'blur',lang.hitch(this,this.disableCall))
			  on(window,'focus',lang.hitch(this,this.initCall))
			  this.initCall();
		  },
		  call: function(){
			  request('./ajax.php?module=ajax&categ=indexation&sub=get_infos', {
					handleAs: 'json'
				}).then(lang.hitch(this, function(response){
					var container =  dom.byId('indexation_infos');
					domConstruct.empty(container);
					if(Object.keys(response).length){
						domConstruct.create('h2', {'class': 'indexation_title', innerHTML: pmbDojo.messages.getMessage('indexation', 'indexation_in_progress')}, container);
						for(var key in response){
							domConstruct.create('label', {'class': 'indexation_entity_label', innerHTML: response[key].label}, container);
							var ul = domConstruct.create('ul', {'class': 'indexation_entity_ul'}, container);
							for(var i=0 ; i<response[key].children.length ; i++){
								domConstruct.create('li', {'class': 'indexation_entity_li', innerHTML: response[key].children[i].entity_label + response[key].children[i].nb}, ul);
							}
						}
					}

				}));
			  
		  },
		  
		  initCall : function(){
			  this.disableCall();
			  if(window.document.hasFocus()){
				  this.callback = setInterval(lang.hitch(this, this.call), 30000);
			  }
		  },
		  
		  disableCall : function(){
			if(this.callback){
				clearInterval(this.callback);
			}
		  }
	  });
});