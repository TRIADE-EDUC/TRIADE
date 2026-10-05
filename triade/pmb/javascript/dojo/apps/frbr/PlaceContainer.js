// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PlaceContainer.js,v 1.10 2017/05/10 16:08:24 tsamson Exp $


define([
        "dojo/_base/declare", 
        "dijit/layout/ContentPane", 
        "dojo/parser",
        "dojo/topic",
        "dojo/_base/lang",
        "dojo/aspect",
        "dijit/registry", 
        "dojo/dom-construct",
        "dojo/dom-style",
        "dojo/on",
        "dojo/dom",
        "dojo/query",
        "dojo/request/xhr"], 
		function(declare,ContentPane,parser, topic, lang, aspect, registry, domConstruct, domStyle, on, dom, query, xhr){
	return declare([ContentPane], {
		numPage : null,
		cadres: null,
		built: null,
		idDragged: null,
		cadreUl : null,
		dropZoneList : new Array(),
		constructor: function(){
			
		},
		postCreate: function(){
			
		},
		buildForm: function(){
			var treeInstance = registry.byId('frbrTree');
			var page = treeInstance.memoryStore.query({root:'1'})[0];
			this.numPage = page.page;
			this.cadres = this.formatCadresOPAC(page.cadres_opac);
			this.cadres = this.cadres.concat(treeInstance.memoryStore.query({type:'cadre'}));

			if(this.cadres.length){			
				//Faire un ul/li avec les cadres présents.
				//Si une sauvegarde de l'ordre est présente pour ces données; la charger
				//Sinon vérifier les parametres (list facette, liste notices,isbd) 
				//; afficher les cadres, puis ajouter ceux des parametres à la fin
				this.createDom();
			}else{
				alert(pmbDojo.messages.getMessage('frbr', 'frbr_no_cadre_to_place'));
			}
		},
		onShow: function(){
			this.inherited(arguments);
			this.buildForm();
		},		
		createDom : function() {
			this.domNode.innerHTML = "";
			this.cadreUl = domConstruct.create('ul', {},this.domNode);
			this.cadres.sort(this.sortCadres);
			
			var nonOrderedCadre = new Array();
			for(var j=0 ; j<this.cadres.length ; j++){
				if(this.cadres[j].order == '0'){
					nonOrderedCadre.push(this.cadres[j]);
				}else{
					this.createDraggableLi(this.cadres[j]);	
				}
			}
			for(var j=0 ; j<nonOrderedCadre.length ; j++){
				this.createDraggableLi(nonOrderedCadre[j]);
			}
			var saveButton = domConstruct.create('input', {
				type : 'button',
				'class' : 'bouton',
				id : 'cadresSaveButton',
				name : 'cadresSaveButton',
				value : pmbDojo.messages.getMessage('frbr', 'frbr_save_placement'),
				onclick : lang.hitch(this, this.savePlacement)
			},this.domNode);
		},
		calldragenter: function(cadre,ev){
			
		},
		calldragover: function(dropZone, ev){
			ev.preventDefault();
			dropZone.setAttribute('class', 'dragoverDropZone');		
		},
		
		calldragstart: function(cadre, ev){
			this.initDropZone();			
			var cadreDrag = dom.byId(cadre.id);
			ev.dataTransfer.setData("text", ev.target.id);
			ev.dataTransfer.dropEffect = "move";
			cadreDrag.setAttribute('class', 'dragStartFrame');
			this.idDragged = cadre.id;
		},
		calldragend: function(cadre, ev){
			var cadreDrag = dom.byId(cadre.id);
			cadreDrag.setAttribute('class', 'draggableFrame');
			this.idDragged = null;			
			this.destroyDropZone();
			this.reorderCadres();
		},
		calldrop: function(dropZone, ev){
			ev.preventDefault();
			dropZone.setAttribute('class', 'dropZone');
			var flyingCadre = dom.byId(this.idDragged);
			dropZone.parentNode.replaceChild(flyingCadre, dropZone);
		},
		calldragleave: function(dropZone){
			dropZone.setAttribute('class', 'dropZone');
		},
		sortCadres : function(a, b) {
			return a.order - b.order;
		},
		reorderCadres : function() {
			var listOfLi = this.cadreUl.childNodes;
			for (var i = 0; i < listOfLi.length; i++) {
				listOfLi[i].setAttribute('dataOrder', i+1);
				this.getCadreFromId(listOfLi[i].getAttribute('id')).order = i+1;
			}
		},
		applyDragEvents: function(node, cadre){
			on(node, 'dragstart', lang.hitch(this, this.calldragstart, cadre));
			on(node, 'dragend', lang.hitch(this, this.calldragend, cadre));
		},
		initDropZone: function(){
			var cadres = query('li', this.cadreUl);
			cadres.forEach(lang.hitch(this, function(cadre){
				this.createDropZone(cadre, 'before');
			}));
			this.createDropZone(cadres[cadres.length-1], 'after');
		},
		createDropZone: function(refNode, position){
			var dropZone = domConstruct.create('li', {'class':'dropZone'}, refNode, position);
			this.dropZoneList.push(dropZone);
			on(dropZone, 'dragover', lang.hitch(this, this.calldragover, dropZone));
			on(dropZone, 'dragleave', lang.hitch(this, this.calldragleave, dropZone));
			on(dropZone, 'drop', lang.hitch(this, this.calldrop, dropZone));
			on(dropZone, 'dragend', lang.hitch(this, this.destroyDropZone));
		},
		destroyDropZone : function() {
			this.dropZoneList.forEach(lang.hitch(this, function(dropZone) {
				domConstruct.destroy(dropZone);
			}));
			this.dropZoneList = new Array();
		},
		getCadreFromId:function(id){
			for(var i=0 ; i<this.cadres.length ; i++){
				if(this.cadres[i].id == id){
					return this.cadres[i];
				}
			}
			return false;
		},
		cadreVisibility : function(cadre, nodeId) {			
			var node = dom.byId(nodeId);
			if (cadre.visibility == 0) {
				cadre.visibility = 1;
				node.setAttribute('class','fa fa-eye');
			} else {
				cadre.visibility = 0;
				node.setAttribute('class','fa fa-eye-slash');
			}
		},
		getJSONCadres : function() {
			var formatted_cadres = new Array();
			for(var i=0 ; i<this.cadres.length ; i++){
				//fonction pour cloner l'objet afin de passer par valeur et non par référence
				formatted_cadres.push(this.cloneObject(this.cadres[i]));
				if(formatted_cadres[i].cadre_type == '') {
					formatted_cadres[i].id = formatted_cadres[i].id.split('_')[1];
					formatted_cadres[i].parent = formatted_cadres[i].parent.split('_')[1];
				}
			}			
			return JSON.stringify(formatted_cadres);
		},
		savePlacement : function() {
			this.reorderCadres();
			xhr("./ajax.php?module=cms&categ=frbr_entities&action=save_cadres_placement&num_page=" + this.numPage,{
				data : {'cadres' : this.getJSONCadres()},
				handleAs: "json",
				method:'POST'
			}).then(function(){
				topic.publish("dGrowl", pmbDojo.messages.getMessage('frbr', 'frbr_save_done'), {'sticky' : false, 'duration' : 5000, 'channel' : 'info'});
				topic.publish("formButton", "clearForm");
			});
		},
		createDraggableLi: function(cadre){
			var cadreLi = domConstruct.create('li', {
				name : cadre.id, 
				id : cadre.id, 
				'class' : 'draggableFrame', 
				'dataOrder' : cadre.order,
				'draggable' : true,
			}, this.cadreUl);
			var titleContainer = domConstruct.create('div', {
				style:{
					'text-align': 'center',
					'display' : 'inline-block'
				}
			}, cadreLi);
			titleContainer.innerHTML = cadre.name;
			var checkboxContainer = domConstruct.create('div', {
				style:{
					'float': 'right',
					'display' : 'inline-block'
						}
			}, cadreLi);
			var visibilityCheckbox = domConstruct.create('i',{
				id : cadre.id + '_visibility',
				'class': (cadre.visibility != '0' ? 'fa fa-eye' : 'fa fa-eye-slash'),
				'aria-hidden' : true,
				onclick : lang.hitch(this, this.cadreVisibility, cadre, cadre.id + '_visibility')
			}, checkboxContainer);			
			this.applyDragEvents(cadreLi, cadre);
		},
		formatCadresOPAC : function(data) {
			var formatData = [];
			for(var key in data){
				data[key].id = data[key].cadre_type;
				formatData.push(data[key]);
			}
			return formatData;
		},
		cloneObject : function(obj) {
			if(obj == null || typeof(obj) != 'object') {
				return obj;
			}
		    var newObj = new obj.constructor(); 
		    for(var key in obj)
		    	newObj[key] = this.cloneObject(obj[key]);

		    return newObj;
		}
		
	});
});