// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DnDElement.js,v 1.8 2021/11/16 15:48:32 dgoron Exp $


define(['dojo/_base/declare', 
        'dojo/topic', 
        'dojo/dom-construct', 
        'dojo/query', 
        'dojo/dnd/Source', 
        'dojo/dom-class',
        'apps/pmb/gridform/WidgetSource',
        'dojo/_base/lang',
        'dojo/dom-style'], 
        function(declare, topic, domConstruct, query, dndSource, domClass, WidgetBase, lang, domStyle){
	  return declare(WidgetBase, {
		  constructor: function(params,srcNodeRef){
			  this.dndParams = params.dndParams;
			  this.class = 'dndLine';
			  //console.log('constructor called');
		  },
		  checkAcceptance:function(source, nodes){
			  if(this == source){
					return !this.copyOnly || this.selfAccept;
				}
			  //console.log('this.node', this.node);
			  	if((nodes[0].parentNode != this.node) && (query('div[movable="yes"]',this.node).length > 3)){
			  		return false;
			  	}
				for(var i = 0; i < nodes.length; ++i){
					var type = source.getItem(nodes[i].id).type;
					// type instanceof Array
					var flag = false;
					for(var j = 0; j < type.length; ++j){
						if(type[j] in this.accept){
							flag = true;
							break;
						}
					}
					if(!flag){
						return false;	// Boolean
					}
				}
				return true;	// Boolean
		  },
		  
		  getItem: function(nodeId){
			  var item = {type:new Array('movable','virtual')};
			  return item;
		  },
		  onDrop: function(source,nodes,copy){
//			  console.log('Arguments Ondrop', arguments);
			  this.inherited(arguments);
			  if(this.node.className.search('container-div') != -1){
				  //
				lang.hitch(this, this.element.dnd.reloadCssClasses, query('div[movable="yes"]', this.node))();
				lang.hitch(this, this.element.dnd.reloadCssClasses, query('div[movable="yes"]', source.node))();
				if(query('div[movable="yes"]', source.node).length == 0){
					domConstruct.destroy(source.node);
				}
			  }
			  topic.publish('DnDElement', 'onDrop', {
					id: nodes[0].id,
					newZone : this.node.parentNode.id.replace('Child','')
			  });
		  },
		  reloadCssClasses: function(fields){
				  if(fields.length == 1){
					  this.element.zone.parent.getElementFromId(fields[0].id).switchClass('row');
				  }else{
					  for(var i=0 ; i<fields.length ; i++){
						  var eltToEdit = this.element.zone.parent.getElementFromId(fields[i].id);
						  eltToEdit.switchClass('colonne'+(fields.length));
						  eltToEdit.switchWidth(fields.length);
					  }  
				  }
				  
		  },
		  onMouseUp: function(evt){
			  this.inherited(arguments);
			  var element = evt.target;
			  do{
				if(element.getAttribute('movable') || element.getAttribute('etirable')){
					break;
				}
				element = element.parentNode;
			  }while(element.parentNode);
			  if(element.getAttribute('movable')){
				  domClass.remove(element, 'dojoDndItemAnchor');
			  }
		  },
		  copyState:function(){
			  return false;
		  },
		  postCreate:function(){
			  this.dnd = new dndSource(this.domNode,this.dndParams);
			  this.dnd.onDrop = lang.hitch(this.dnd, this.onDrop);
			  this.dnd.checkAcceptance = lang.hitch(this.dnd, this.checkAcceptance);
			  this.dnd.getItem = lang.hitch(this.dnd, this.getItem);
			  this.dnd.copyState = lang.hitch(this.dnd, this.copyState);
			  if(this.dnd && this.dnd.node && this.dnd.node.firstChild){
				  domStyle.set(this.dnd.node.firstChild, 'cursor', 'move');  
			  }
			  
		  },
		  destroy:function(){
			  this.dnd.destroy();
			  this.inherited(arguments);
		  }
	  });
});