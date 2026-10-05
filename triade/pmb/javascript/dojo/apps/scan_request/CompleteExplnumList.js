// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CompleteExplnumList.js,v 1.1 2016/01/22 13:25:27 vtouchard Exp $


define(["dojo/_base/declare", "dijit/_WidgetBase", "dojo/request/xhr", "dojo/_base/lang", "dojo/topic", "dojo/dom-construct", "dojo/dom-attr", "dijit/registry", "dojo/on", "apps/scan_request/RecordLine","apps/scan_request/BulletinLine", "dojo/dom", "dojo/dom-style"], function(declare, WidgetBase, xhr, lang, topic, domConstruct, domAttr, registry, on, RecordLine, BulletinLine, dom, domStyle){

	  return declare([WidgetBase], {
		  elements: null,
		  seeUrl: "./doc_num.php?explnum_id=",
		  state: 0, //0 -> pas de document numérique associés à la demande ; 1 -> Docnum associé à la demande
		  messageNode: null,
		  listNode: null,
		  labelNode: null,
		  constructor:function(params){
			 this.state = 0;
			 this.elements = {};
			 this.own(topic.subscribe('ExplnumList', lang.hitch(this, this.handleEvents)));
		  },
		  postCreate:function(){
			  this.inherited(arguments);
			  this.messageNode = domConstruct.create('label', {innerHTML: pmbDojo.messages.getMessage('scan_request', 'scan_request_no_explnum')}, this.domNode);
			  this.labelNode = domConstruct.create('label', {style:{fontWeight:'bold'},innerHTML: pmbDojo.messages.getMessage('scan_request', 'scan_request_explnum_from_request')}, this.domNode);
			  this.listNode = domConstruct.create('table', {}, this.domNode);
			  var thContainerNode = domConstruct.create('tr', {}, this.listNode);
			  var ressourceTh = domConstruct.create('th', {innerHTML: pmbDojo.messages.getMessage('scan_request', 'scan_request_explnum_ressource')}, thContainerNode);
			  var documentTh = domConstruct.create('th', {innerHTML: pmbDojo.messages.getMessage('scan_request', 'scan_request_explnum_document')}, thContainerNode);
			  var seeTh =  domConstruct.create('th', {innerHTML: pmbDojo.messages.getMessage('scan_request', 'scan_request_explnum_see')}, thContainerNode);
			  if(this.params.elementsData && this.params.elementsData.length){
				  for(var i=0 ; i<this.params.elementsData.length ; i++){
					  if(this.params.elementsData[i].explnums.length){ //Si un document numérique a été rattaché à l'élément parcouru 
						  for(var j=0 ; j<this.params.elementsData[i].explnums.length ; j++){
							  this.addElement({
								  id: this.params.elementsData[i].explnums[j].id, 
								  label: this.params.elementsData[i].label, 
								  title: this.params.elementsData[i].explnums[j].title
							  });
						  }
					  }
				  }
			  }
			  this.updateState();
		  },
		  handleEvents: function(evtType, evtArgs){
			switch(evtType){
				case 'explnumUpdated':
					if(evtArgs.elementAdded){
						this.addElement(evtArgs.elementAdded);
					}else if(evtArgs.elementRemoved){
						this.removeElement(evtArgs.elementRemoved);
					}
					this.updateState();
					break;
			}  
		  },
		  addElement: function(elementData){
			  this.elements[elementData.id] = {};
			  this.elements[elementData.id].ressourceLabel = elementData.label;
			  this.elements[elementData.id].explName = elementData.title;
			  this.elements[elementData.id].line = {};
			  
			  /**
			   * Construction du dom
			   */
			  this.elements[elementData.id].line.mainNode = domConstruct.create('tr', {}, this.listNode); //Tr du document courant
			  this.elements[elementData.id].line.ressourceNode = domConstruct.create('td', {innerHTML: this.elements[elementData.id].ressourceLabel}, this.elements[elementData.id].line.mainNode); //Td du nom de la resource
			  domConstruct.create('td', {innerHTML: this.elements[elementData.id].explName}, this.elements[elementData.id].line.mainNode); //Td du nom du document; 
			  var tdSee = domConstruct.create('td', {style: {textAlign:'center', cursor:'pointer'},innerHTML: '<i class="fa fa-eye fa-1"></i>'}, this.elements[elementData.id].line.mainNode);
			  on(tdSee, "click", lang.hitch(this, this.seeCallback, elementData.id));
		  },
		  removeElement: function(elementId){
			  if(this.elements[elementId]){
				  domConstruct.destroy(this.elements[elementId].line.mainNode);
				  delete this.elements[elementId];
			  }
		  },
		  editElement: function(elementId){
			  
		  },
		  seeCallback: function(explnumId){
			  window.open(this.seeUrl+explnumId, '_blank');
		  },
		  updateState: function(){
			  if(this.getNbExpl()){
				  this.switchView(true);
			  }else{
				  this.switchView(false);
			  }
		  },
		  switchView: function(showList){ //True affichage de la list ; false affichage du message
			if(showList){
				domStyle.set(this.listNode, 'display', 'table');
				domStyle.set(this.messageNode, 'display', 'none');	
				domStyle.set(this.labelNode, 'display', 'block');
			}else{
				domStyle.set(this.listNode, 'display', 'none');
				domStyle.set(this.messageNode, 'display', 'block');
				domStyle.set(this.labelNode, 'display', 'none');
			}
		  },
		  getNbExpl: function(){
			  var i = 0;
			  for(var key in this.elements){
				  i++;
			  }
			  return i;
		  }
	  });
});