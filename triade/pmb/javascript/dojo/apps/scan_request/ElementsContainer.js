// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ElementsContainer.js,v 1.8 2016/02/19 10:41:22 vtouchard Exp $


define(["dojo/_base/declare", "dijit/_WidgetBase", "dojo/request/xhr", "dojo/_base/lang", "dojo/topic", "dojo/dom-construct", "dojo/dom-attr", "dijit/registry", "dojo/on", "apps/scan_request/RecordLine","apps/scan_request/BulletinLine", "dojo/dom"], function(declare, WidgetBase, xhr, lang, topic, domConstruct, domAttr, registry, on, RecordLine, BulletinLine, dom){

	  return declare([WidgetBase], {
		  elements: null,
		  addElementButton: null,
		  mode: null,
		  editable:null,
		  constructor:function(params){
			 this.mode = params.mode;
			 this.elements = new Array();
			 
			 if(!params.readOnly || ( (dom.byId('record_editable')&&(parseInt(dom.byId('record_editable').value)==1)))) {
				 this.editable = true;
			 }else{
				 this.editable = false;
			 }
			// console.log(this);
		  },
		  postCreate:function(){
			  this.inherited(arguments);
			  if(this.editable){
				  if(this.mode == 'record'){
					  this.addElementButton = domConstruct.create('input', {type: 'button', value:'+', 'class':'bouton'}, dom.byId('associated_record_label_container'));  
				  }else if(this.mode == 'bulletin'){
					  this.addElementButton = domConstruct.create('input', {type: 'button', value:'+', 'class':'bouton'}, dom.byId('associated_bulletin_label_container'));
				  }
				  on(this.addElementButton, 'click', lang.hitch(this, this.addElement,{id: '', label:'', comment:'', explnums:{}}));
			  }
			  
			  if(this.params.elementsToLoad && this.params.elementsToLoad.length){//On affiche une demande qui existe déjà en base et des éléments lui on été associé
				  for(var i=0 ; i<this.params.elementsToLoad.length ; i++){
					  this.addElement(this.params.elementsToLoad[i]);
				  }
			  }else{ //Nouvelle demande on crée au moins une ligne vide
				  if(this.params.readOnly){
					this.destroy();
				  }else{
					  this.addElement({id: '', label:'', comment:'', explnums:{}});  
				  }
			  }
		  },
		  addElement: function(elementData){
			  if(this.mode == 'bulletin'){
				  this.elements.push(new BulletinLine({data:elementData, index:this.elements.length, readOnly:this.params.readOnly, editable:this.editable}, domConstruct.create('div', {'class':'row'}, this.domNode)));
			  }else if(this.mode == 'record'){
				  this.elements.push(new RecordLine({data:elementData, index:this.elements.length, readOnly:this.params.readOnly, editable:this.editable}, domConstruct.create('div', {'class':'row'}, this.domNode)));
			  }
		  },
	  });
});