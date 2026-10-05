// +-------------------------------------------------+
// + 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormEdit.js,v 1.6 2021/06/30 09:25:29 gneveu Exp $


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
        'apps/pmb/gridform/Zone',
        'dijit/registry',
        'dojo/dom-class'], 
        function(declare, xhr, lang, topic, on, dom, domGeom, domStyle, domAttr, query, domConstruct, Zone, registry, domClass){

	  return declare(null, {
		  type: null,
		  nbZones: null,
		  zones: null,
		  context: null,
		  savedScheme: null,
		  constructor:function(type, context){
			  this.context = context ? context : document;
			  this.type = type;
			  this.zones = new Array();
			  this.getDefaultPos();
			  this.getDatas();
		  },
		  parseDom: function(){
			  var currentElts = query('div[movable="yes"]', this.context);
			  if(this.savedScheme){
				  for(var i=0; i < this.savedScheme.length; i++) {
					  var params = {
						  isExpandable:this.savedScheme[i].isExpandable, 
						  showLabel:this.savedScheme[i].showLabel, 
						  visible: this.savedScheme[i].visible,
						  label: this.savedScheme[i].label,
						  nodeId: this.savedScheme[i].nodeId
					  };
					  var nodeId = this.savedScheme[i].nodeId;
					  var newerZone = new Zone(params, nodeId, this, this.context);
					  newerZone.setVisible(this.savedScheme[i].visible);
					  if(this.savedScheme[i].visible){
						  newerZone.addConnectStyle();
					  }
					  if(this.savedScheme[i].elements.length) {
						  for(var j=0 ; j < this.savedScheme[i].elements.length; j++){
							  var domElt = query('#'+this.savedScheme[i].elements[j].nodeId, this.context)[0];
							  if(domElt != null) {
								  newerZone.addField(domElt, this.savedScheme[i].elements[j].visible, this.savedScheme[i].elements[j].disabled);
								  var indexElt = currentElts.indexOf(domElt);
								  currentElts.splice(indexElt, 1);
							  }
						  }
					  }
					  this.zones.push(newerZone);
					  this.nbZones++;
				  }
			  }  
			  if(currentElts.length) {
				  var objectZone = this.getZoneFromId('el0');
				  if(!objectZone){
					  objectZone = new Zone({label:pmbDojo.messages.getMessage('grid', 'grid_js_move_default_zone')}, 'el0', this, this.context);
					  objectZone.addConnectStyle();
					  this.zones.push(objectZone);
					  this.nbZones++;
				  }
				  for(var i=0 ; i < currentElts.length; i++){
					  objectZone.addField(currentElts[i], true, false);
				  }
			  }
			  this.callZoneRefresher();
		  },
		  unparseDom: function(){
			  var cleanElts = query('#zone-container > div', this.context);
			  for(var i=0; i < this.originalFormat.length; i++){
				  domConstruct.place(query('#'+this.originalFormat[i].id,this.context)[0], query('#zone-container',this.context)[0], 'last');
				  query('#'+this.originalFormat[i].id, this.context)[0].className = this.originalFormat[i].class;
			  }

			  for(var i=0; i < cleanElts.length ; i++){
			  	if(cleanElts[i].getAttribute('movable') == null){
			  		domConstruct.destroy(cleanElts[i]);	
			  	}
			  }
			  this.zones = new Array();
		  },
		  getZoneFromId: function(zoneId){
			  for(var i=0 ; i < this.zones.length ; i++){
				  if(this.zones[i].nodeId == zoneId){
					  return this.zones[i];
				  }
			  }
			  return false;
		  },
		  makeInvisibleZone: function(params){
			  var zoneToMakeInvisible = this.getZoneFromId(params.nodeId);
			  zoneToMakeInvisible.setVisible(0);
			  //this.disableNodes(zoneToMakeInvisible.domNode);
		  },
		  makeVisibleZone: function(params){
			  var zoneToMakeVisible = this.getZoneFromId(params.nodeId);
			  zoneToMakeVisible.setVisible(1);
			  zoneToMakeVisible.addConnectStyle();
			  //this.enableNodes(zoneToMakeVisible.domNode);
		  },
		  getHiddenZones: function(){
			var hiddenZones = new Array();
			for(var i=0 ; i < this.zones.length; i++){
				if(!this.zones[i].visible){
					hiddenZones.push(this.zones[i]);	
				}
			}
			return hiddenZones;
		  },
		  getDefaultPos: function(){
			  var defaultElts = query('div[movable="yes"]',this.context);
			  this.originalFormat = new Array();
			  for(var i=0; i < defaultElts.length; i++){
				  this.originalFormat.push({id:defaultElts[i].id, class: defaultElts[i].className});  
			  }
		  },
		  getDatas: function(){
			  if(!this.type) {
				  var currentUrl = window.location;
				  this.type = /lvl=(\w+)&?/g.exec(currentUrl)[1];
				  switch(this.type){
					  case 'contribution_area':
					      // On récupère l'id du formulaire dans l'url courante
					      var matchs = /form_id=(\w+)&?/g.exec(currentUrl)
					      var formId = 0;
					      if (matchs) {
					          formId = matchs[1];
					      } else {
	                          // Si on a aucun id on regarde le formulaire présent dans la page
				              var forms = query('form',this.context);
					          if (forms && forms[0]) {
					              var matchs = /form_id=(\w+)&?/g.exec(forms[0].action)
		                          if (matchs) {
		                              formId = matchs[1];
		                          }
					          }
					      } 
						  this.type += '_form_'+formId;
						  break;
				  }
			  }
			  
			  var returnedInfos = {genericType: this.type, genericSign: ''};
			  xhr("./ajax.php?module=ajax&categ=grid&type="+this.type+"&action=get_datas",{
					 handleAs: "json",
					 method:'post',
					 data:'datas='+JSON.stringify(returnedInfos)
			  }).then(lang.hitch(this, this.getDatasCallback));
		  },
		  getDatasCallback: function(response){
			  if(response.status == true){
				  this.buildGrid(response.datas);
			  } else {
				  if(this.flagOriginalFormat){
					  this.unparseDom();  
				  }
				  this.buildGrid();
			  }
		  },
		  buildGrid: function(datas){
			  var activeElement = document.activeElement;
			  //On stocke le premier niveau des enfants de zone-container des div non movable (nettoy� apres traitement)
			  //var cleanElts = query('#zone-container > div:not(div[movable="yes"])');
			  var cleanElts = query('#zone-container > div:not([movable="yes"])', this.context);
			  var currentElts = query('div[movable="yes"]',this.context);
			  if(typeof datas != 'undefined' && datas != ""){
				  this.savedScheme =  JSON.parse(datas);
				  for(var i=0 ; i < this.savedScheme.length; i++){
					  var params = {
						  isExpandable:this.savedScheme[i].isExpandable, 
						  showLabel:this.savedScheme[i].showLabel, 
						  visible: this.savedScheme[i].visible,
						  label: this.savedScheme[i].label,
						  nodeId: this.savedScheme[i].nodeId
					  };
					  if(params.isExpandable) {
						  var parentNode = domConstruct.create('div', {id:params.nodeId+'Parent', class:'parent'}, query('#zone-container',this.context)[0], 'last');
						  var labelNode = domConstruct.create('h3', {innerHTML:params.label, style:{'display':'inline'}}, parentNode, 'last');
						  domConstruct.create('img', {
							  src:pmbDojo.images.getImage('plus.gif'),
							  class:'img_plus',
							  align:'bottom',
							  name:'imEx',
							  id:params.nodeId+'Img',
							  title:'titre',
							  border:'0',
							  onClick:'expandBase("'+params.nodeId+'", true); return false;'
							  }, labelNode , 'before');
						  var domNode = domConstruct.create('div', {id:params.nodeId+'Child', label: params.label, class:'child'}, query('#zone-container',this.context)[0], 'last');
					  } else {
						  if(params.showLabel){
							  var parentNode = domConstruct.create('div', {id:params.nodeId+'Parent', class:'parent'}, query('#zone-container',this.context)[0], 'last');
							  var labelNode  = domConstruct.create('h3', {innerHTML:params.label}, parentNode, 'last');
						  }else{
							  var parentNode = domConstruct.create('div', {id:params.nodeId+'Parent', class:'parent', innerHTML:'&nbsp;'}, query('#zone-container',this.context)[0], 'last');
						  }
						  var domNode = domConstruct.create('div', {id:params.nodeId+'Child', label: params.label}, query('#zone-container',this.context)[0], 'last');
					  }
					  if(!params.visible){
						  domStyle.set(parentNode, 'display', 'none');
						  domStyle.set(domNode, 'display', 'none');
					  }
					  domAttr.set(domNode,'etirable', 'yes');
					  if(params.visible) {
						  domStyle.set(params.nodeId+'Parent', 'display', 'block');
						  if(params.isExpandable) {
							  domStyle.set(params.nodeId+'Child', 'display', 'none');  
						  }else{
							  domStyle.set(params.nodeId+'Child', 'display', 'inline-block');
						  }
						  domStyle.set(params.nodeId+'Child', 'width', '100%');
					  } else {
						  domStyle.set(params.nodeId+'Parent', 'display', 'none');
						  domStyle.set(params.nodeId+'Child', 'display', 'none');
					  }
					  
					  var nbColumn = 1;
					  var lastNbColumn = 1;
					  var columnInProgress = 0;
					  for(var j=0 ; j < this.savedScheme[i].elements.length; j++){
						  if(columnInProgress == 0) {
							  var parentDiv = domConstruct.create('div', {class:'container-div row contribution_input_size'}, domNode, 'last');
						  }
						  
						  var node = query('#'+this.savedScheme[i].elements[j].nodeId, this.context)[0];
						  if(node != null){
							  node.className = this.savedScheme[i].elements[j].className;
							  var result = /colonne([2-5]|_suite)/.exec(node.className);
							  if(result){
								  if (result[1] == '_suite') {
									  nbColumn = lastNbColumn;
								  } else {
									  nbColumn = result[1];
								  }
							  } else {
								  nbColumn = 1;
							  }
							  if (columnInProgress && ((nbColumn != lastNbColumn) && (j>0))) {
								  var parentDiv = domConstruct.create('div', {class:'container-div row contribution_input_size'}, domNode, 'last');
								  columnInProgress = 0;
							  }
							  domConstruct.place(node, parentDiv, 'last');
							  if(!this.savedScheme[i].elements[j].visible){
								  domStyle.set(node, 'display','none');
								  if(this.savedScheme[i].elements[j].disabled){
									  this.disableNodes(node);
								  }
							  } else {
								  domStyle.set(node, 'display','block');
							  }
							  columnInProgress++;
							  lastNbColumn = nbColumn;
							  if(nbColumn == columnInProgress){
								  columnInProgress = 0;
								  nbColumn = 1;
							  }
							  var indexElt = currentElts.indexOf(node);
							  currentElts.splice(indexElt, 1);
						  }
					  }
				  }
				  if(currentElts.length) {
					  var defaultZone = query('#el0Child',this.context);
					  if(!defaultZone.length) {
						  var parentNode = domConstruct.create('div', {id:'el0Parent', class:'parent', innerHTML:'&nbsp;'}, query('#zone-container',this.context)[0], 'last');
						  var domNode = domConstruct.create('div', {id:'el0Child', label: pmbDojo.messages.getMessage('grid', 'grid_js_move_default_zone')}, query('#zone-container',this.context)[0], 'last');
					  } else {
						  var domNode = query('#el0Child',this.context)[0];
					  }
					  domAttr.set(domNode, 'etirable', 'yes');
					  var nbColumn=1;
					  var lastNbColumn = 1;
					  var columnInProgress = 0;
					  for(var i=0 ; i < currentElts.length; i++){
						  if(columnInProgress == 0) {
							  var parentDiv = domConstruct.create('div', {class:'container-div row contribution_input_size'}, domNode, 'last');
						  }
						  var result = /colonne([2-5]|_suite)/.exec(currentElts[i].className);
						  if(result){
							  if (result[1] == '_suite') {
								  nbColumn = lastNbColumn;
							  } else {
								  nbColumn = result[1];
							  }
						  } else {
							  nbColumn = 1;
						  }
						  if (columnInProgress && ((nbColumn != lastNbColumn) && (j>0))) {
							  var parentDiv = domConstruct.create('div', {class:'container-div row contribution_input_size'}, domNode, 'last');
							  columnInProgress = 0;
						  }
						  domConstruct.place(currentElts[i], parentDiv, 'last');
						  domStyle.set(currentElts[i], 'display', 'block');
						  
						  columnInProgress++;
						  lastNbColumn = nbColumn;
						  if(nbColumn == columnInProgress){
							  columnInProgress = 0;
							  nbColumn = 1;
						  }
					  }
				  }
				  //Traitement termin�, on nettoye
				  for(var i=0 ; i < cleanElts.length; i++){
					  domConstruct.destroy(cleanElts[i]);
				  }
			  }
			  activeElement.focus();
		  },
		  getZones: function(){
				return this.zones;
		  },
		  getElementFromId:function(id){
			  for(var i=0 ; i < this.zones.length; i++) {
				  var elt = this.zones[i].getElementFromId(id);
				  if(elt){
					  return elt;
				  }
			  }
			  return false;
		  },
	  });
});
