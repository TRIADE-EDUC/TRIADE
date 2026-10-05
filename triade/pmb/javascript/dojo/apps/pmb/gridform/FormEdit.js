// +-------------------------------------------------+
// + 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormEdit.js,v 1.47.8.1 2025/05/27 13:57:38 dgoron Exp $


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
		module: null,
		type: null,
		signalEditFormat: null,
		signalOriginFormat: null,
		state:null,		
		btnEdit: null,
		btnSave : null,
		btnOrigin: null,
		zonesClickedSignals: null,
		eltsClickedSignals: null,
		nbZones: null,
		zones: null,
		paramsForSign:null,
		savedScheme: null,
		originalZones: null,
		originalFormat:null,
		flagOriginalFormat:null,
		context : null,
		computedElements : [],
		constructor:function(module, type, context){
			this.module = (module ? module : 'autorites');
			this.context = context ? context : document;
			this.type = type;
			this.paramsForSign = new Array();
			this.buildParamsForSign();
			this.state = 'std';
			this.btnEdit = dom.byId('bt_inedit');
			this.btnSave = dom.byId('bt_save');
			this.btnOrigin = dom.byId('bt_origin_format');
			this.zones = new Array();
			if(this.btnEdit) {
				this.signalEditFormat = on(this.btnEdit,'click', lang.hitch(this, this.btnEditCallback));
			}
			if(this.btnSave) {
				on(this.btnSave,'click', lang.hitch(this, this.saveAll));
			}
			if(this.btnOrigin) {
				this.signalOriginFormat = on(this.btnOrigin,'click', lang.hitch(this, this.btnOriginFormatCallback));
			}
			topic.subscribe('PopupZone', lang.hitch(this, this.handleEvents, 'PopupZone'));
			topic.subscribe('ContextMenu', lang.hitch(this, this.handleEvents, 'ContextMenu'));
			topic.subscribe('DnDVirtualLine', lang.hitch(this, this.handleEvents, 'DnDVirtualLine'));
			topic.subscribe('DnDElement', lang.hitch(this, this.handleEvents, 'DnDElement'));
			//this.getDefaultZones();
			this.getDefaultPos();
			this.flagOriginalFormat = false;
			this.getDatas();
		},
		handleEvents: function(evtClass, evtType, evtArgs){
			switch(evtClass){
				case 'PopupZone':
					switch(evtType){
						case 'createZone':
							this.addZone(evtArgs);
							break;
						case 'editZone':
							this.editZone(evtArgs);
							break;
					}
					break;
				case 'ContextMenu':
					switch(evtType){
						case 'deleteZone':
							this.deleteZone(evtArgs);
							break;
						case 'upZone':
							this.upZone(evtArgs);
							break;
						case 'downZone':
							this.downZone(evtArgs);
							break;
						case 'makeInvisibleZone':
							this.makeInvisibleZone(evtArgs);
							break;
						case 'makeVisibleZone':
							this.makeVisibleZone(evtArgs);
							break;
						case 'changeZone':
							this.changeZone(evtArgs);
							break;
						case 'saveAll':
							this.saveAll(evtArgs);
							break;
						case 'saveAllBackbones':
							this.saveAllBackbones(evtArgs);
							break;
					}
					break;
				case 'DnDElement':
					switch(evtType){
						case 'onDrop':
							this.dropElement(evtArgs);
							break;
						}
					break;
			}
		},
		btnEditCallback: function(evt){
			switch(this.type){
				case 'reader':
					expandAll();
					break;
				case 'record':
					if (document.getElementById("grille_location")) {
						document.getElementById("grille_location").selectedIndex = 0;
						document.getElementById("grille_location").style.display = "block";
					}
					expandAll();
					break;
			}
			switch(this.state){
				case 'std':
					this.state = 'inedit';
					this.parseDom();
					domAttr.set(this.btnEdit, 'value', pmbDojo.messages.getMessage('grid', 'grid_js_move_back'));
					domAttr.set(this.btnOrigin, 'disabled', 'disabled');
					domStyle.set(this.btnOrigin, 'color', '#aaa');
							
//					var disableButtonsForm = query('form > input[type=button]');
//					if(disableButtonsForm.length){
//						for(var i=0; i < disableButtonsForm.length; i++){
//							domAttr.set(disableButtonsForm[i],'disabled','disabled');
//							domStyle.set(disableButtonsForm[i],'color','#aaa');
//						}
//					}
					break;
				case 'inedit':
					this.state = 'std';
					domAttr.remove(this.btnOrigin, 'disabled');
					domAttr.remove(this.btnOrigin, 'style');
					window.location.reload();
					break;
			}
		},
		btnOriginFormatCallback: function(evt){
			switch(this.type){
				case 'reader':
				case 'record':
					expandAll();
					break;
			}
			this.savedScheme = null;
			this.unparseDom();
			this.buildGrid();
			if(this.state != 'std'){
				this.parseDom();
			}
		},
		getZoneIdFromDOMElement: function(domElement) {
			var zoneId = domElement.id.substring(0, domElement.id.indexOf('Child'));
			var hasOtherZone = query('#'+zoneId+'Child', this.context);
			if(!hasOtherZone.length) {
				return 'el0';
			} else {
				return zoneId;
			}
		},
		parseDom: function(){
//			var zones = query('div[etirable="yes"]');
			var currentElts = query('div[movable="yes"]', this.context);
//			if(zones.length) {
			if(this.savedScheme){
				for(var i=0; i < this.savedScheme.length ; i++){
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
						for(var j=0 ; j < this.savedScheme[i].elements.length ; j++){
							var domElt = query('#'+this.savedScheme[i].elements[j].nodeId, this.context)[0];
							if(domElt != null) {
								newerZone.addField(domElt, this.savedScheme[i].elements[j].visible, this.savedScheme[i].elements[j].disabled);
								var indexElt = currentElts.indexOf(domElt);
								if (indexElt != -1){
									currentElts.splice(indexElt, 1);
								}
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
					objectZone = this.addDefaultZone('el0');
				}
				for(var j=0 ; j < currentElts.length ; j++){
					if(this.getZoneIdFromDOMElement(currentElts[j]) == 'el0') {
						objectZone.addField(currentElts[j], true, false);
					}
				}
				for(var i=0 ; i < this.originalZones.length ; i++){
					var objectZone = this.getZoneFromId(this.originalZones[i].id);
					if(!objectZone){
						objectZone = this.addDefaultZone(this.originalZones[i].id);
					}
					for(var j=0 ; j < currentElts.length ; j++){
						if(this.getZoneIdFromDOMElement(currentElts[j]) == this.originalZones[i].id) {
							if (!objectZone.getElementFromId(currentElts[j].id)) {
								objectZone.addField(currentElts[j], true, false);
							}
						}
					}
				}
			}
			this.callZoneRefresher();
		},
		cleanRecursiveElts: function(cleanElts, rule_css, with_container_div=false) {
			for(var i=0; i < cleanElts.length ; i++){
					if(cleanElts[i].getAttribute('etirable') && cleanElts[i].getAttribute('etirable') == 'yes') {
						var subCleanElts = query('#'+cleanElts[i].getAttribute('id')+rule_css, this.context);
						this.cleanRecursiveElts(subCleanElts, rule_css, with_container_div);
					} else {
						if(cleanElts[i].getAttribute('movable') == null && (with_container_div || !domClass.contains(cleanElts[i],'container-div')) && !domClass.contains(cleanElts[i],'parent')) {
							domConstruct.destroy(cleanElts[i]);	
						}
					}
			}
		},
		isOriginalZone: function(domElement) {
			var zoneId = this.getZoneIdFromDOMElement(domElement);
			return this.existsDefaultZone(zoneId);
		},
		isAjaxElementZone: function(domElement) {
			var zoneId = this.getZoneIdFromDOMElement(domElement);
			var domZone = dom.byId(zoneId+'Child');
			if(domZone && domAttr.has(domZone, 'data-zone-ajax') && domAttr.get(domZone, 'data-zone-ajax') == "yes") {
				return true;
			}
			return false;
		},
		destroyAjaxElements: function() {
			for(var i=0; i < this.originalFormat.length ; i++){
				if(this.isAjaxElementZone(this.originalFormat[i])){
					domConstruct.destroy(dom.byId(this.originalFormat[i].id));
				}
			}
		},
		unparseDom: function(){
			var container = query('#zone-container',this.context)[0];
			if (container) {
				for(var i=0; i < this.originalFormat.length ; i++) {
					var node = query('#'+this.originalFormat[i].id,this.context)[0];
					if(node) {
						domConstruct.place(node, container, 'last');
						node.className = this.originalFormat[i].class;
					}
				}
			}
			
			//Nettoyage des zones
			for(var i=0 ; i < this.originalZones.length ; i++) {
				var cleanZone = query('#'+this.originalZones[i].id+'Child > div', this.context);
				for(var j=0; j < cleanZone.length ; j++){
					domConstruct.destroy(cleanZone[j]);
				}
			}
			
			var cleanElts = query('#zone-container > div', this.context);
			if (cleanElts) {
				for(var i=0; i < cleanElts.length ; i++) {
					if(cleanElts[i].getAttribute('etirable') && cleanElts[i].getAttribute('etirable') == 'yes') {
						if(!this.isOriginalZone(cleanElts[i])) {
							domConstruct.destroy(cleanElts[i]);
						}
					} else {
						if(cleanElts[i].getAttribute('movable') == null){
							domConstruct.destroy(cleanElts[i]);
						}
					}
				}
			}
			
			for(var i=0 ; i < this.zones.length ; i++) {
				if (this.zones[i].nodeId != "el0") {
					domConstruct.destroy(this.zones[i].domNode);
				}
			}
			this.zones = new Array();
			
			var defaultZone = query('#el0Child', this.context);
			if(defaultZone.length) {
				domStyle.set(defaultZone[0], 'display', 'block');
			}
		},
		addDefaultZone: function(zoneId){
			var domZone = dom.byId(zoneId+'Child');
			var label = pmbDojo.messages.getMessage('grid', 'grid_js_move_default_zone');
			var isExpandable = false;
			if(domZone) {
				if(domAttr.get(domZone, 'label')) {
					label = domAttr.get(domZone, 'label');
				} else {
					label = domAttr.get(domZone, 'title');
				}
				switch(this.type){
					case 'reader':
					case 'record':
						var domParentZone = dom.byId(zoneId+'Parent');
						var domImgZone = dom.byId(zoneId+'Img');
						if(domParentZone && domImgZone) {
							isExpandable = true;
						}
						break;
				}
			}
			var objectZone = new Zone({label:label, isExpandable:isExpandable}, zoneId, this, this.context);
			objectZone.addConnectStyle();
			this.zones.push(objectZone);
			this.nbZones++;
			return objectZone;
		},
		addZone: function(params) {
			
			// On créer un id de zone qui n'est pas déjà utilisé
			var nbZones = this.nbZones;			
			var nodeId = 'zone'+nbZones;
			while (this.getZoneFromId(nodeId) != false) {
				nbZones++;
				nodeId = 'zone'+nbZones;
			}
			
			var newerZone = new Zone(params, nodeId, this);
			newerZone.createNodes();
			newerZone.addConnectStyle();
			this.zones.push(newerZone);
			this.nbZones++;
		},
		editZone: function(params){
			var zoneToEdit = this.getZoneFromId(params.zoneId);
			zoneToEdit.edit(params);
		},
		deleteZone: function(params){
			var zoneToDelete = this.getZoneFromId(params.nodeId);
			if(zoneToDelete.destroy()){
				var indexZone = this.zones.indexOf(zoneToDelete);
				if (indexZone != -1){
					this.zones.splice(indexZone, 1);
				}	
				zoneToDelete = null;
				this.nbZones--;
				return true;
			}
			return false;
		},
		getZoneFromId: function(zoneId){
			for(var i=0 ; i < this.zones.length ; i++){
				if(this.zones[i].nodeId == zoneId){
					return this.zones[i];
				}
			}
			return false;
		},
		addDefaultDOMZone: function(zoneId) {
			var defaultZone = query('#'+zoneId+'Child', this.context);
			if(!defaultZone.length) {
					var parentNode = domConstruct.create('div', {id:zoneId+'Parent', class:'parent', innerHTML:''}, query('#zone-container',this.context)[0], 'last');
					var domNode = domConstruct.create('div', {id:zoneId+'Child', label: pmbDojo.messages.getMessage('grid', 'grid_js_move_default_zone')}, query('#zone-container',this.context)[0], 'last');
			} else {
					var parentNode = query('#'+zoneId+'Parent',this.context)[0];
					if(parentNode) {
						domConstruct.place(parentNode, query('#zone-container',this.context)[0],'last');
					} else {
						domConstruct.create('div', {id:zoneId+'Parent', class:'parent', innerHTML:''}, query('#zone-container',this.context)[0], 'last');
					}
					var domNode = query('#'+zoneId+'Child',this.context)[0];
					domConstruct.place(domNode, query('#zone-container',this.context)[0],'last');
			}
			domAttr.set(domNode, 'etirable', 'yes');
			return domNode;
		},
		upZone: function(params){
			var zoneToUp = this.getZoneFromId(params.nodeId);
			if(this.zones.length > 1){
				var indexZone = this.zones.indexOf(zoneToUp);
				if(indexZone){
					var tempZone = this.zones[indexZone-1];
					this.zones[indexZone-1] = zoneToUp;
					this.zones[indexZone] = tempZone;
					domConstruct.place(this.zones[indexZone-1].nodeId+'Parent',this.zones[indexZone].nodeId+'Parent','before');
					domConstruct.place(this.zones[indexZone-1].nodeId+'Child',this.zones[indexZone].nodeId+'Parent','before');
				}
			}
		},
		downZone: function(params){
			var zoneToDown = this.getZoneFromId(params.nodeId);
			if(this.zones.length > 1){
				var indexZone = this.zones.indexOf(zoneToDown);
				if(indexZone < this.zones.length-1){
					var tempZone = this.zones[indexZone+1];
					this.zones[indexZone+1] = zoneToDown;
					this.zones[indexZone] = tempZone;
					domConstruct.place(this.zones[indexZone+1].nodeId+'Child',this.zones[indexZone].nodeId+'Child','after');
					domConstruct.place(this.zones[indexZone+1].nodeId+'Parent',this.zones[indexZone].nodeId+'Child','after');
				}
			}
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
		changeZone: function(params){
//			var originZone = this.getZoneFromId(params.zoneId);
//			var destZone = this.getZoneFromId(params.moveToZoneId);
//			var fieldNode = originZone.removeField(params.id);
//			var parentDiv = domConstruct.create('div', {class:'container-div row'}, destZone.domNode, 'last');
//			domConstruct.place(fieldNode, parentDiv, 'last');
//			destZone.addField(fieldNode,true);
//
//			
			
			var eltObject = this.getElementFromId(params.id);
			var oldClassName = eltObject.className;
			var originZone = this.getZoneFromId(params.zoneId);
//			console.log('originZone, before', originZone, originZone.elements)
			var destZone = this.getZoneFromId(params.moveToZoneId);
			var fieldNode = originZone.removeField(params.id);
			var parentDiv = domConstruct.create('div', {class:'container-div row'}, destZone.domNode, 'last');
			domConstruct.place(fieldNode, parentDiv, 'last');
			var test = destZone.addField(fieldNode,true, false);
			test.className = oldClassName;
			eltObject.destroy();
//			console.log('originZone, after', originZone, originZone.elements)
		},
		getSign: function(){
			var sign = '';
			if(this.paramsForSign.length) {
				for(var i=0 ; i < this.paramsForSign.length ; i++){
					if(dom.byId(this.paramsForSign[i])) {
						if(sign != '') {
							sign += '_'+dom.byId(this.paramsForSign[i]).value;
						} else {
							sign = dom.byId(this.paramsForSign[i]).value;
						}
					}
				}
			}
			return sign;
		},
		saveAll: function(){
			var returnedInfos = this.getStruct();
			returnedInfos['genericSign'] = this.getSign();
			this.launchXhrSave(returnedInfos);
		},
		saveAllBackbones: function(evtArgs){
			var returnedInfos = this.getStruct();
			returnedInfos['all_backbones'] = true;
			var backboneTable = new Array();
			for(var i=0 ; i < this.paramsForSign.length ; i++){
				var backboneOptions = dom.byId(this.paramsForSign[i]).options;
				var backboneValues = new Array();
				for(var j=0 ; j < backboneOptions.length ; j++){
					backboneValues.push(backboneOptions[j].value);
				}
				backboneTable.push(backboneValues);
			}
			returnedInfos['backbone_table'] = backboneTable;
			this.launchXhrSave(returnedInfos);
		},
		getHiddenZones: function(){
			var hiddenZones = new Array();
			for(var i=0 ; i < this.zones.length ; i++){
				if(!this.zones[i].visible){
					hiddenZones.push(this.zones[i]);	
				}
			}
			return hiddenZones;
		},
		saveCallback: function(response){
			if(response.status == true){				
				alert(pmbDojo.messages.getMessage('grid', 'grid_js_move_saved_ok'));
			}else{
				alert(pmbDojo.messages.getMessage('grid', 'grid_js_move_saved_error'));
			}
		},
		getDefaultZones: function() {
			this.originalZones = new Array();
			var defaultZones = query('div[etirable="yes"]', this.context);
			for(var i=0; i < defaultZones.length; i++){
				var zoneId = defaultZones[i].id.substring(0, defaultZones[i].id.indexOf('Child'));
				this.originalZones.push({id:zoneId, label: domAttr.get(defaultZones[i], 'label'), class: defaultZones[i].className});
			}
		},
		getDefaultPos: function(){
			var defaultElts = query('div[movable="yes"]', this.context);
			this.originalFormat = new Array();
			for(var i=0; i < defaultElts.length; i++){
				this.originalFormat.push({id:defaultElts[i].id, class: defaultElts[i].className});
			}
		},
		getDatas: function(){
			if(!this.type) {
				var currentUrl = window.location;
				this.type = /categ=(\w+)&?/g.exec(currentUrl)[1];
				switch(this.type){
					case 'authperso':
						var authPerso = /id_authperso=(\w+)&?/g.exec(currentUrl)[1];
						this.type += '_'+authPerso;
						break;
					case 'contribution_area':
						var formId = /form_id=(\w+)&?/g.exec(currentUrl)[1];
						this.type += '_form_'+formId;
						break;
				}
				
			}
			var returnedInfos = {genericType: this.type, genericSign: this.getSign()};
			xhr("./ajax.php?module="+this.module+"&categ=grid&action=get_datas",{
					 handleAs: "json",
					 method:'post',
					 data:'datas='+JSON.stringify(returnedInfos)
			}).then(lang.hitch(this, this.getDatasCallback));
		},
		getDatasCallback: function(response){
			if(response.status == true){
				this.computedElements = [];
				this.buildGrid(response.datas);
			} else {
				if(this.flagOriginalFormat){
					this.unparseDom();
				}
				this.buildGrid();
			}
		},
		buildZone: function(data, currentElts) {
			
			var params = {
				isExpandable:data.isExpandable, 
				showLabel:data.showLabel, 
				visible: data.visible,
				label: data.label,
				nodeId: data.nodeId
			};
			
			if(params.isExpandable) {
				
				var parentNode = null;
				var results = query('#'+params.nodeId+'Parent', this.context);
				if (results && results[0]) {
					parentNode = results[0];
				}
				
				if(parentNode) {
					domConstruct.place(parentNode, query('#zone-container',this.context)[0],'last');
				} else {
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
				}
				
				var domNode = null;
				var results = query('#'+params.nodeId+'Child', this.context);
				if (results && results[0]) {
					domNode = results[0];
				}
				
				if(domNode) {
					domAttr.set(domNode, 'label', params.label);
					domConstruct.place(domNode, query('#zone-container',this.context)[0],'last');
				} else {
					domNode = domConstruct.create('div', {id:params.nodeId+'Child', label: params.label, class:'child'}, query('#zone-container',this.context)[0], 'last');
				}
			} else {
				if(params.showLabel){
					var parentNode = domConstruct.create('div', {id:params.nodeId+'Parent', class:'parent'}, query('#zone-container',this.context)[0], 'last');
					var labelNode= domConstruct.create('h3', {innerHTML:params.label}, parentNode, 'last');
				}else{
					var parentNode = domConstruct.create('div', {id:params.nodeId+'Parent', class:'parent', innerHTML:'&nbsp;'}, query('#zone-container',this.context)[0], 'last');
				}
				
				var domNode = null;
				var results = query('#'+params.nodeId+'Child', this.context);
				if (results && results[0]) {
					domNode = results[0];
				}
				
				if(domNode) {
					domAttr.set(domNode, 'label', params.label);
					domConstruct.place(domNode, query('#zone-container',this.context)[0],'last');
				} else {
					domNode = domConstruct.create('div', {id:params.nodeId+'Child', label: params.label}, query('#zone-container',this.context)[0], 'last');
				}
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

			for(var j=0; j < data.elements.length ; j++) {
				if (this.computedElements.indexOf(data.elements[j].nodeId) != -1) {
					continue;
				} else {
					this.computedElements.push(data.elements[j].nodeId);
				}
				if(columnInProgress == 0) {
					var parentDiv = domConstruct.create('div', {class:'container-div row'}, domNode, 'last');
				}
				var node = query('#'+data.elements[j].nodeId, this.context)[0];
				if(node != null){
					node.className = data.elements[j].className;
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
						var parentDiv = domConstruct.create('div', {class:'container-div row'}, domNode, 'last');
						columnInProgress = 0;
					}
					domConstruct.place(node, parentDiv, 'last');
					if(!data.elements[j].visible){
						domStyle.set(node, 'display','none');
						if(data.elements[j].disabled){
							this.disableNodes(node);
						}
					} else {
						domStyle.set(node, 'display','block');
						this.enableNodes(node);								
					}
					columnInProgress++;
					lastNbColumn = nbColumn;
					if(nbColumn == columnInProgress){
						columnInProgress = 0;
						nbColumn = 1;
					}
					var indexElt = currentElts.indexOf(node);
					if (indexElt !=-1){
						currentElts.splice(indexElt, 1);
					} 
				}
			}
		},
		getDOMZoneFromDOMElement: function(domElement) {
			var zoneId = domElement.id.substring(0, domElement.id.indexOf('Child'));
			var hasOtherZone = query('#'+zoneId+'Child', this.context);
			if(hasOtherZone[0]) {
				return hasOtherZone[0];
			} else {
				var defaultZone = query('#el0Child', this.context);
				return defaultZone[0];
			}
		},
		existsDefaultZone(id) {
			for(var i=0 ; i < this.originalZones.length ; i++){
				if(this.originalZones[i].id == id) {
					return true;
				}
			}
			return false;
		},
		buildGrid: async function(datas){
			//Suppression des noeuds contenant l'editeur HTML
			var text_areas_with_tinymce = this.removeTinyEditor();

			var activeElement = document.activeElement;
			//On stocke le premier niveau des enfants de zone-container des div non movable (nettoy� apres traitement)
			//var cleanElts = query('#zone-container > div:not(div[movable="yes"])');
			var cleanElts = query('#zone-container > div:not([movable="yes"])', this.context);
			var currentElts = query('div[movable="yes"]', this.context);
			
			if(typeof datas != 'undefined' && datas != ""){
				let savedScheme = JSON.parse(datas);
				
				if (savedScheme) {
					this.savedScheme = savedScheme;
					
					// On évite d'avoir plusieurs fois le même id pour les zones
					let zones = new Object();
					for(var i=0 ; i < this.savedScheme.length ; i++) {
						let zone = this.savedScheme[i];
						
						if (zones[zone.nodeId]) {
							let nodeId = zone.nodeId;
							let found = false;
							let i = this.savedScheme.length;
							while (!found) {
								nodeId = `zone${i}`;
								if (!zones[nodeId]) {									
									found = true;
								}
								i++;
							}
							zone.nodeId = nodeId;
						}
						zones[zone.nodeId] = zone;
					}
					
					for(var i=0 ; i < this.savedScheme.length ; i++){
						 await this.buildZone(this.savedScheme[i], currentElts);
					}
				}
			}
			
			await this.getDefaultZones();
			
			if(currentElts.length) {
				if(!this.existsDefaultZone('el0')) {
					this.addDefaultDOMZone('el0');
				}
				for(var i=0 ; i < this.originalZones.length ; i++){
					this.addDefaultDOMZone(this.originalZones[i].id);
				}
				var nbColumn=1;
				var lastNbColumn = 1;
				var columnInProgress = 0;
				for(var i=0 ; i < currentElts.length ; i++){
					
					var domNode = this.getDOMZoneFromDOMElement(currentElts[i]);
					if(columnInProgress == 0) {
						var parentDiv = domConstruct.create('div', {class:'container-div row'}, domNode, 'last');
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
//					if (columnInProgress && ((nbColumn != lastNbColumn) && (j>0))) {
//						var parentDiv = domConstruct.create('div', {class:'container-div row'}, domNode, 'last');
//						columnInProgress = 0;
//					}
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
			
			//Ajout des noeuds contenant l'editeur HTML
			this.addTinyEditor(text_areas_with_tinymce);
			//Traitement termin�, on nettoye
			this.cleanRecursiveElts(cleanElts, ' > div:not([movable="yes"])');
			activeElement.focus();
		},
		getZones: function(){
				return this.zones;
		},
		getElementFromId:function(id){
			for(var i=0 ; i < this.zones.length ; i++){
				var elt = this.zones[i].getElementFromId(id);
				if(elt){
					return elt;
				}
			}
			return false;
		},
		removeTinyEditor: function(){
			//Prise en compte de la version de TinyMCE, 
			//certaines méthodes ne sont pas prises en compte sur les versions inférieures à la 4.9.9
			if (typeof(tinyMCE)!= 'undefined' && tinyMCE.majorVersion+'.'+tinyMCE.minorVersion >= '4.9.9'){
				var text_areas = document.getElementsByTagName('textarea');
				text_areas_with_tinymce = new Array();
				if (text_areas.length >0) {
					for (var j=0; j < text_areas.length; j++) {
						var instance = tinyMCE_getInstance(text_areas[j].getAttribute("id"));
						if (instance != null) {
							tinyMCE_execCommand('mceRemoveControl', true, text_areas[j].getAttribute("id"));
							text_areas_with_tinymce.push(text_areas[j].getAttribute("id"));
						}
					}
				}
				return text_areas_with_tinymce;
			} else {
				return null;
			}
		},
		addTinyEditor: function(text_areas_with_tinymce){
			//Prise en compte de la version de TinyMCE, 
			//certaines méthodes ne sont pas prises en compte sur les versions inférieures à la 4.9.9
			if (typeof(tinyMCE)!= 'undefined' && tinyMCE.majorVersion+'.'+tinyMCE.minorVersion >= '4.9.9'){
				if (text_areas_with_tinymce.length >0) {
					for (var j=0; j < text_areas_with_tinymce.length; j++) {
						tinyMCE_execCommand('mceAddControl', true, text_areas_with_tinymce[j]);
					}
				}
			}
		},
		dropElement: function(params){
			var droppedElt = this.getElementFromId(params.id);
			var oldWidgetSource = droppedElt.dnd;
			droppedElt.dnd = registry.byId(droppedElt.domNode.parentNode.id);
			var currentZone = droppedElt.zone;
			var newZone = this.getZoneFromId(params.newZone);
	 		if(newZone.domNode.id == currentZone.domNode.id){ //�l�ment d�plac� dans sa zone d'origine
				currentZone.removeField(droppedElt.nodeId);
				var elements = query('div[movable="yes"]', currentZone.domNode);
				var indexElt = 0;
				for(var i=0 ; i < elements.length ; i++){
					if(elements[i].id == droppedElt.nodeId) {
						newZone.elements.splice(i,0,droppedElt);
						var indexElt = i;
					}
				}
			}else{//Element d�plac� dans une nouvelle zone
				currentZone.removeField(droppedElt.nodeId);
				var elements = query('div[movable="yes"]', newZone.domNode);
				var indexElt = 0;
				for(var i=0 ; i < elements.length ; i++){
					if(elements[i].id == droppedElt.nodeId) {
						newZone.elements.splice(i,0,droppedElt);
						var indexElt = i;
					}
				}
				droppedElt.zone = newZone;
			}
			droppedElt.dnd.resize();
			if(droppedElt.dnd.id != oldWidgetSource.id){
				oldWidgetSource.resize();
			}
		},
		buildParamsForSign: function(){
			var backbones = query('select[backbone="yes"]', this.context);
			if(backbones.length){
				for(var i=0; i < backbones.length; i++){
					on(backbones[i], 'change', lang.hitch(this, this.switchGrid));
					this.paramsForSign.push(backbones[i].id);
				}
			}
		},
		switchGrid: function(evt){
			this.flagOriginalFormat = true;
			this.unparseDom();
			this.getDatas();
		},
		callZoneRefresher: function(){
			for(var i=0 ; i < this.zones.length ; i++){
				this.zones[i].refreshZoneLines();
			}
			/**
			 * Petit hack pour rester au niveau du bouton 
			 * Lors de l'appui sur le bouton "Editer Format"
			 */
			window.scrollTo(0,0);
		},
		getStruct: function(){
			var JSONInformations = new Array();
			if(this.zones.length) {
				for(var i=0; i < this.zones.length ; i++){
					JSONInformations.push(this.zones[i].getJSONInformations());
				}
			}
			if(!this.type) {
				var currentUrl = window.location;
				this.type = /categ=(\w+)&?/g.exec(currentUrl)[1];
				if(this.type == 'authperso'){
					var authPerso = /id_authperso=(\w+)&?/g.exec(currentUrl)[1];
					this.type += '_'+authPerso;
				}
			}
			var returnedInfos = {zones: JSONInformations, genericType: this.type};
			return returnedInfos;
		},
		launchXhrSave: function(datas){
			xhr("./ajax.php?module="+this.module+"&categ=grid&action=save",{
					 handleAs: "json",
					 method:'post',
					 data:'datas='+JSON.stringify(datas)
			}).then(lang.hitch(this, this.saveCallback));
		},
		disableNodes: function(containerNode){
			var toDisable = Array.prototype.slice.call(query('input', containerNode)).concat(Array.prototype.slice.call(query('select', containerNode)).concat(Array.prototype.slice.call(query('textarea', containerNode))));
			for(var i=0 ; i < toDisable.length ; i++){
				domAttr.set(toDisable[i], 'disabled', 'disabled');
			}
		},
		enableNodes: function(containerNode){
			var toEnable = Array.prototype.slice.call(query('input', containerNode)).concat(Array.prototype.slice.call(query('select', containerNode)).concat(Array.prototype.slice.call(query('textarea', containerNode))));
			for(var i=0 ; i < toEnable.length ; i++){
				domAttr.remove(toEnable[i], 'disabled');
			}
		},
	});
});