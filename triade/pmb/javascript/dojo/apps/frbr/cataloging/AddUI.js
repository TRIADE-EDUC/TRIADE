// +-------------------------------------------------+
// é 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AddUI.js,v 1.8 2018/03/21 14:05:42 tsamson Exp $

define(["dojo/_base/declare", 
        "dojox/layout/ExpandoPane", 
        "dojox/layout/ContentPane", 
        "dojo/dom-construct", 
        "dojo/dom", 
        "dojo/on", 
        "dojo/topic",
        "dojo/_base/lang",
        "dijit/registry",
        "dijit/form/Button",
        "dojo/query",
        "dojo/dom-style",
        "dijit/form/DropDownButton",
        "dijit/DropDownMenu",
        "dijit/MenuItem",
        "dijit/form/Select",
        "dojo/dom-attr",
        "dojo/request/iframe",
        "apps/frbr/cataloging/EntitySelector",
        "dojo/io-query"], function(declare, ExpandoPane, ContentPane, domConstruct, dom, on, topic, lang, registry, Button, query, domStyle, DropDownButton, DropDownMenu, MenuItem, Select, domAttr, iframe, EntitySelector, ioQuery){
	
	return declare([ContentPane, EntitySelector], {
		menuCreated: 0,
		constructor: function(){

		},
		postCreate:function(){
			this.inherited(arguments);
			this.own(
				topic.subscribe('AddUI', lang.hitch(this, this.handleEvents))
			);
		},
		handleEvents: function(evtType,evtArgs){
			switch(evtType){
				case "setSelectorDefaultValue":
					this.setSelectorDefaultValue(evtArgs.value);
					break;
			}
		},
		onShow: function(){
			this.inherited(arguments);
			if(!this.menuCreated){
				if(query('#containerNode', this.containerNode).length == 0){
					domConstruct.create('div', {id: "containerNode"}, this.containerNode)
				}
				this.createSelector(this.loadForm);
				this.menuCreated = 1;
			}
			if(this.selectedValue){
				this.selector.set('value', this.selectedValue);
			}//TODO REPRENDRE ICI, reprise valeur selecteur après bascule saisie complete. 
			 //TODO: Formulaire de notice fait tout planter
		},
		loadForm: function(url){
			if(url){
				this.set('href', url);
				return true;
			}
			return true;
		},
		
		_load: function(){
			// summary:
			//		Load/reload the href specified in this.href

			// display loading messageg
			this._setContent(this.onDownloadStart(), true);

			var self = this;
			var getArgs = {
				preventCache: (this.preventCache || this.refreshOnShow),
				url: this.href,
				handleAs: "text"
			};
			if(lang.isObject(this.ioArgs)){
				lang.mixin(getArgs, this.ioArgs);
			}

			var hand = (this._xhrDfd = (this.ioMethod || xhr.get)(getArgs)),
				returnedHtml;
			var self = this;
			
			hand.then(
				function(html){
					
					var fctToText = Function.prototype.toString.call(self.moveFields);
					var pattern = 'function(domXML)';
					
					if(fctToText.indexOf('function (domXML)') != -1){
						pattern = 'function (domXML)';
					}
					html+= '<script type="text/javascript">'+Function.prototype.toString.call(self.moveFields).replace(pattern, 'function move_fields(domXML)')+'</script>';
				
					returnedHtml = html;
					try{
						self._isDownloaded = true;
						return self._setContent(html, false);
					}catch(err){
						self._onError('Content', err); // onContentError
					}
				},
				function(err){
					if(!hand.canceled){
						// show error message in the pane
						self._onError('Download', err); // onDownloadError
					}
					delete self._xhrDfd;
					return err;
				}
			).then(function(){
					self.onDownloadEnd();
					delete self._xhrDfd;
					return returnedHtml;
				});

			// Remove flag saying that a load is needed
			delete this._hrefChanged;
		},
		
		onLoad: function(){
			this.applyButtonEvent('btcancel', function(button){
				domAttr.set(button, 'onclick', '');
				on(button, 'click', lang.hitch(this, this.cancelAdd));
			});
			
			this.applyButtonEvent('switch_input_type', function(button){
				var oldClick = button.getAttribute('onclick'); 
				var regX =  /document.location=\'(.*?)\'/;
				domAttr.set(button, 'onclick', '');
				var result = oldClick.match(regX);
				if(result){
					on(button, 'click', lang.hitch(this, function(url){
						url = url.replace('select.php', 'ajax.php');
						this.destroyWidgets();
						this.loadForm(url);
					},result[1]));
				}
			});

			this.applyButtonEvent('btsubmit', function(button){
				domAttr.set(button, 'onclick', '');
				domAttr.set(button, 'type', 'button');
				button.onclick = null;
				on(button, 'click', lang.hitch(this, this.managePost, button));
			});
			
			collapseAll(this.containerNode);
			this.createSelector(this.loadForm, this.get('href'));
			
			var btnEdit = dom.byId('bt_inedit');
			var btnOrigin = dom.byId('bt_origin_format');
			
			if(btnEdit){
				domConstruct.destroy(btnEdit);
			}
			if(btnOrigin){
				domConstruct.destroy(btnOrigin);
			}
			
		},
		applyButtonEvent: function(id, callback){			
			var button = query('input[id="'+id+'"]', this.containerNode);
			if(button.length){
				lang.hitch(this, callback, button[0])();
			}
		},
		cancelAdd: function(){
			if(confirm('Etes vous sur de vouloir annuler la saisie ?')){
				this.destroyWidgets();
				test_form = "";
				
				this.createSelector(this.loadForm);
			}
		},
		destroyWidgets: function(){
			var widgets = this.containerNode.querySelectorAll('[widgetid]');
			
			for(var i=0 ; i<widgets.length ; i++){
				var widget = registry.byId(domAttr.get(widgets[i], 'widgetid'));
				if (widget && widget.declaredClass != 'dGrowl') {
					widget.destroy();
				}
			}
		},
		managePost: function(buttonClicked){
			switch(this.getEntityType()){
				case 'notice':
					this.checkPostValidator(test_notice, buttonClicked.form);
					break;
				case 'ontology': 
					if(this.checkConceptForm()){
						this.postForm(buttonClicked.form);
					}
					break;
				default:
					this.checkPostValidator(test_form, buttonClicked.form);
					break;
			}
			return false;
		},
		postForm:function(form){
			if(domAttr.get(form, 'action').indexOf('select.php') != -1){
				domAttr.set(form, 'action', domAttr.get(form, 'action').replace('select.php?', 'ajax.php?module=selectors&'));
			}
			iframe(domAttr.get(form, 'action'),{
				form: form,
				handleAs: 'json',
			}).then(lang.hitch(this, function(data){
				if(parseInt(data.id) && (parseInt(data.id) !=0)){
					this.destroyWidgets();	
					test_form = "";
					this.selectedValue = '';
					domConstruct.empty(this.containerNode);
					this.createSelector(this.loadForm);
					//TODO: Print the list back (click sur l'expandopane)
					topic.publish('AddUI', 'itemAdded', data);
					
				} 
			}));
			return false;
		},
		getEntityType: function(){
			var url = this.get('href');
			var object = ioQuery.queryToObject(url.split('?')[1]);
			return (object.what ? object.what : '');
		},
		checkPostValidator: function(functionName, form){
			if(typeof functionName == "function" && functionName(form)){
				this.postForm(form);
			}else if(typeof functionName == "undefined"){
				this.postForm(form);	
			}
		},
		checkConceptForm: function(){
			var error_message = "";
			for (var i in validations){
				if(!validations[i].check()){
					error_message+= validations[i].get_error_message();
				}
			}
			if(error_message != ""){
				alert(error_message);
			}else{
				return true;
			}
			return false;
		},
		setSelectorDefaultValue : function(value) {
			this.destroyDescendants();
			this.createSelector(this.loadForm, value);
			this.set("href", value);
			this.menuCreated = 1;
		},
		moveFields: function(domXML){ //remplacement de la méthode standard de remplacement par celle ci
			var need_parse = false;
			if(typeof(dojo) == "object"){
				var widgets = dijit.registry.findWidgets(document.getElementById('notice'));
				for(var i=0 ; i<widgets.length ; i++){
					if (widgets[i].declaredClass != 'dGrowl') {
						widgets[i].destroy(true);
						need_parse = true;
					}
				}
		  	}
			var text_areas = document.getElementsByTagName('textarea');
			text_areas_with_tinymce = new Array();
			if (text_areas.length >0) {
				for (var j=0; j<text_areas.length; j++) {
					if(typeof(tinyMCE)!= 'undefined') {
						var test = tinyMCE_getInstance(text_areas[j].getAttribute("id"));
						if (test != null) {
							tinyMCE_execCommand('mceRemoveControl', true, text_areas[j].getAttribute("id"));
							text_areas_with_tinymce.push(text_areas[j].getAttribute("id"));
						}
					}
				}
			}
			root=domXML.getElementsByTagName("formpage");
			relative=root[0].getAttribute("relative");
			if (relative=="yes") relative=true; else relative=false;
			
			var relp=relative;
			
			var etirables=domXML.getElementsByTagName("etirable");
			if(!document.getElementById(etirables[0].getAttribute("id"))) return;
			var parent_onglet=document.getElementById(etirables[0].getAttribute("id")).parentNode;
			var onglet=new Array();
			var onglet_titre=Array();
			var fields= new Array();
			var id=0;
			
			for (i=0; i<etirables.length; i++) {
				//Onglets flottants
				id=etirables[i].getAttribute("id");
				if(!document.getElementById(id)) continue;
				//on regénére le dom des textarea, le navigateur se contente d'affecter la propriété value... 
				var text_areas = document.getElementById(id).getElementsByTagName('textarea');
				for(var x=0 ; x<text_areas.length ; x++){
					if(!text_areas[x].firstChild){
						text_areas[x].appendChild(document.createTextNode(text_areas[x].value));
					}
				}
				//on regénére le dom des select, le navigateur se contente d'affecter la propriété selected sans recréer l'attribut... 
				var selects = document.getElementById(id).getElementsByTagName('select');
				for(var x=0 ; x<selects.length ; x++){
					for(var y=0 ; y<selects[x].options.length ; y++){
						if(selects[x].options[y].selected){
							selects[x].options[y].setAttribute('selected','selected');
						}
					}
				}
				onglet[i]=document.getElementById(id).cloneNode(true);
				if (etirables[i].getAttribute("invert")=="yes") onglet[i].setAttribute("invert","yes"); else onglet[i].setAttribute("invert","");
				var onglet_tit=get_onglet_title(document.getElementById(id));
				onglet_titre[i]=onglet_tit.cloneNode(true);
				parent_onglet.removeChild(document.getElementById(id));
				parent_onglet.removeChild(onglet_tit);
			}
			for (i=0; i<etirables.length; i++) {
				//Remise en ordre
				if(!onglet_titre[i]) continue;
				parent_onglet.appendChild(onglet_titre[i]);
				parent_onglet.appendChild(onglet[i]);
				if (onglet[i].getAttribute("invert")=="yes") 
					relp=(!relative)
				else relp=relative;
				onglet[i].style.position=relp?"":"relative";
				if (!relp) onglet[i].style.height=etirables[i].getAttribute("height")+"px"; else onglet[i].style.height="";
				if (etirables[i].getAttribute("visible")=="no") {
					onglet_titre[i].style.display="none";
					onglet[i].style.display="none";
					onglet[i].setAttribute("hide","yes");
				} else {
					onglet_titre[i].style.display="block";
					onglet[i].style.display="block";
					onglet[i].setAttribute("hide","");
				}	

				if(etirables[i].getAttribute("startOpen")=="yes"){
					onglet[i].setAttribute("startOpen","yes");
				}
				if(etirables[i].getAttribute("startOpen")=="no"){
					onglet[i].setAttribute("startOpen","no");
				}
				if(etirables[i].getAttribute("visible")=="no"){
					onglet[i].setAttribute("startOpen","no");
				}
				if(etirables[i].getAttribute("startOpen")!="no" && onglet[i].id=='el0Child'){
					onglet[i].setAttribute("startOpen","yes");
				}
			}

			var movables=domXML.getElementsByTagName("movable");

			for (i=0; i<movables.length; i++) {
				id=movables[i].getAttribute("id");
				var parent_id=movables[i].getAttribute("parent");
				var mov=document.getElementById(id);
				if (mov != null && document.getElementById(parent_id)) {
					var new_mov=mov.cloneNode(true);
					mov.parentNode.removeChild(mov);
					document.getElementById(parent_id).appendChild(new_mov);
					//Positionnement en fonction de relative
					if (document.getElementById(parent_id).getAttribute("invert")=="yes") 
						relp=(!relative) 
					else relp=relative;
					new_mov.style.position=relp?"":"absolute";
					if (!relp) {
						new_mov.style.left=movables[i].getAttribute("left")+"px";
						new_mov.style.top=movables[i].getAttribute("top")+"px";
					} else {
						new_mov.style.left="";
						new_mov.style.top="";
					} 
					if (movables[i].getAttribute("visible")=="no") {
						new_mov.style.display="none";
					} else {
						new_mov.style.display="block";
					}
				}
			}
			parent_onglet.style.visibility="visible";
			if(need_parse){
				dojo.parser.parse(document.getElementById('notice'));
			}
			if (text_areas_with_tinymce.length >0) {
				for (var j=0; j<text_areas_with_tinymce.length; j++) {
					tinyMCE_execCommand('mceAddControl', true, text_areas_with_tinymce[j]);
				}
			}
		}
	});
});

