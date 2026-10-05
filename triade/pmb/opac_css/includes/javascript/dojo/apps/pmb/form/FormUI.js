// +-------------------------------------------------+
// + 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormUI.js,v 1.1 2018/10/08 13:59:39 vtouchard Exp $


define(['dojo/_base/declare', 
        'dojo/request/xhr', 
        'dojo/_base/lang', 
        'dojo/topic', 
        'dojo/on', 
        'dojo/dom', 
        'dojo/dom-geometry', 
        'dojo/dom-style', 
        'dojo/dom-attr', 
        'dojo/query',
        'dojo/dom-construct', 
        'dijit/registry',
        'dojo/dom-class',
        'dijit/layout/BorderContainer',
        'dijit/layout/ContentPane',
        'apps/pmb/form/FormContainer' //Dérivarion du tabcontainer
        ], 
        function(declare, xhr, lang, topic, on, dom, domGeom, domStyle, domAttr, query, domConstruct, registry, domClass, BorderContainer, ContentPane, FormContainer){

	  return declare(null, {
		  context: null, //Formulaire courant
		  tabContainer: null,
		  contentPane: null,
		  saveScroll: null,
		  constructor:function(context){
			 this.context = context;
			 topic.subscribe('openPopup', lang.hitch(this, this.handleEvents));
			 topic.subscribe('FormController', lang.hitch(this, this.handleEvents));
			 topic.subscribe('FormContainer', lang.hitch(this, this.handleEvents));
		  },
		  handleEvents: function(evtClass, evtType, evtArgs){
			  switch(evtClass){
			  	case 'openPopup':
			  		switch(evtType){
			  			case 'buttonClicked':
			  				this.manageTabs(evtArgs);
			  				break;
			  		}
			  		break;
			  	case 'FormContainer':
			  		switch(evtType){
				  		case 'noMoreForms':
				  			this.switchView();
				  			break;
			  		}
			  		break;
			  }
		  },
		  manageTabs: function(evtArgs){
			  if(!this.tabContainer){
				  this.initEnvironment();
			  }
			  this.tabContainer.addTab(evtArgs);			
		  },
		  initEnvironment: function(){
			  this.saveScroll = document.body.scrollTop;
			  this.tabContainer = new FormContainer({doLayout: false,  style: "height: 100%; width: 100%;"});
			  this.tabContainer.startup();
			  
			  var formNodes = this.context.childNodes;
			  formNodes = Array.prototype.slice.call(formNodes);
			  
			  this.contentPane = new ContentPane({style: "height: 100%; width: 100%;", title: '<i class="fa fa-file-o" aria-hidden="true"></i>', region: 'center'});
			  formNodes.forEach(lang.hitch(this, function(node){
				  domConstruct.place(node, this.contentPane.containerNode);
			  }));
			  
//			  domConstruct.place(this.context, this.contentPane.containerNode);
			  this.contentPane.startup();
			  
			  this.tabContainer.addChild(this.contentPane);
//			  if(dom.byId('contenu')){
				  this.tabContainer.placeAt(this.context, 'last');  
//			  }else{
//				  this.tabContainer.placeAt("tab_container", 'last');
//			  }
			  this.tabContainer.resize();
		  },
		  switchView: function(){
			  /**
			   * Nous n'avons plus que le formulaire initial
			   * Suppression de l'onglet, repassage en formulaire basique
			   */
			  var formNodes = this.contentPane.containerNode.childNodes;
			  formNodes = Array.prototype.slice.call(formNodes);
			  formNodes.forEach(lang.hitch(this, function(node){
				  domConstruct.place(node, this.context, 'last');
			  }));
			  this.destroyEnv();
		  },
		  destroyEnv: function(){
			  this.contentPane.destroyRecursive();
			  this.tabContainer.destroyRecursive();
			  
			  this.contentPane = null;
			  this.tabContainer = null;
			  document.body.scrollTop = this.saveScroll;
		  }
	  });
});