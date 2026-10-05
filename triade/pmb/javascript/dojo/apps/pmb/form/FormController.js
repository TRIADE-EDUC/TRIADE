// +-------------------------------------------------+
// + 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormController.js,v 1.2 2017/10/11 15:18:56 vtouchard Exp $


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
        'apps/pmb/form/FormUI'
        ], 
        function(declare, xhr, lang, topic, on, dom, domGeom, domStyle, domAttr, query, domConstruct, registry, domClass, FormUI){

	  return declare(null, {
		  module: null,
		  context: null, //Formulaire courant
		  formUI: null,
		  constructor:function(context){
			  /****
			   * 
			   * TODO : N'instancier la classe qu'une seule et unique fois
			   * 
			   */
			 this.init();
		  },
		  init: function(){
			var elements = query('form[data-advanced-form]');
			if(elements.length == 1){
				this.context = elements[0];
				//suppression du conditionnement sur la présence de dataSelector, kle tout est maintenant fait
				//Dans la méthode openPopup (vu avec DG, plus simple pour la suite);
				this.formUI = new FormUI(this.context);
			}else{
			}
		  },
		  parseDataSelector: function(){
			  var dataSelectors = query('input[data-selector]');
			  if(dataSelectors.length){
				  //Boutons détéctés, application d'un evenement pour l'ouverture d'un onglet
				  dataSelectors.forEach(lang.hitch(this, function(dataSelector){
					  on(dataSelector, 'click', lang.hitch(this, this.dataSelectorClicked, dataSelector));
				  }));
				  return true;
			  }
			  return false;
		  },
		  dataSelectorClicked: function(dataSelector, evt){
			  var json = JSON.parse(domAttr.get(dataSelector, 'data-selector'));
			  topic.publish('FormController', 'FormController', 'dataSelectorClicked', json);
		  },
		  handleEvents: function(evtClass, evtType, evtArgs){
			  switch(evtClass){
//			  	case 'FormContainer':
//			  		switch(evtType){
//				  		case 'noMoreForms':
//				  			this.switchView();
//				  			break;
//			  		}
//			  		break;
			  }
		  },
		  
	  });
});