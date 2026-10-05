// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubTabAdd.js,v 1.22 2020/08/28 14:40:27 tsamson Exp $


define([
        'dojo/_base/declare',
        'dojo/dom',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/request/xhr',
        'dojo/dom-form',
        'dijit/layout/TabContainer',
        'dojox/layout/ContentPane',
        'dojo/query',
        'dojo/ready',
        'dojo/topic',
        'dijit/registry',
        'dojo/dom-attr',
        'dojo/dom-geometry',
        'dojo/dom-construct',
        'dojo/dom-style',
        'dojo/_base/xhr',
        'apps/pmb/gridform/FormEdit',
        'dojo/dom-form',
        'dojo/request/iframe',
        'dojo/io-query',
        'apps/pmb/form/FormController',
        'dojox/widget/DialogSimple'
        ], function(declare, dom, on, lang, xhr, domForm, TabContainer, ContentPane, query, ready, topic, registry, domAttr, geometry, domConstruct, domStyle, xhr, FormEdit, domForm, iframe, ioQuery, FormController, PMBDojoxDialogSimple){
		return declare([ContentPane], {
			resizeTimeout: null,
			currentHeight: null,
			constructor: function() {
				
			},
			handleEvents: function(evtType,evtArgs){
				switch(evtType){
					case 'savedForm':
						break;
						
				}
			},
			postCreate: function() {
				this.inherited(arguments);
				this.resizeTimeout = setInterval(lang.hitch(this, this.checkSize), 200);
				this.currentHeight = this.containerNode.clientHeight; 
			},
			checkSize: function(){
				if(this.currentHeight < this.containerNode.clientHeight){				
					this.getParent().resizeIframe();
					this.currentHeight = this.containerNode.clientHeight;
					if(typeof ajax_resize_elements == "function"){
						ajax_resize_elements();
					}
				}
			},
			onDownloadEnd: function(){
				var buttons = query('input[type="button"][onclick*="document.location"]', this.containerNode);
				buttons.forEach(function(button){
					domConstruct.destroy(button);
				});
				//Maintenant nous allons shunter le submit

				var selectorURL = this.parameters.selectorURL.substring(this.parameters.selectorURL.indexOf("?") + 1, this.parameters.selectorURL.length);
			  	var queryObject = ioQuery.queryToObject(selectorURL);
				
			  	var querySubmit = query('input[type="button"][id="btsubmit"]', this.containerNode);
			  	if(querySubmit.length){
			  		this.setSubmitEvent(querySubmit);
			  	}
			  	
			  	var querySubmit = query('input[type="submit"][id="btsubmit"]', this.containerNode);
			  	if(querySubmit.length){
			  		this.setSubmitEvent(querySubmit);
			  	}
			  	
			  	//Bouton "Enregistrer et continuer"
			  	var querySaveAndContinue = query('input[type="button"][id="update_continue"]', this.containerNode);
			  	if(querySaveAndContinue.length){
			  		domConstruct.destroy(querySaveAndContinue[0]);
			  	}
			  	
			  	/**
			  	 * Cas particuliers pour les concepts
			  	 */
			  	var queryPrevious = query('input[onclick="history.go(-1);"]', this.containerNode);
			  	if(queryPrevious.length){
			  		domConstruct.destroy(queryPrevious[0]);
			  	}
			  	var querySubmit = query('input[onclick="submit_onto_form();"]', this.containerNode);
			  	if(querySubmit.length){
			  		this.setSubmitEvent(querySubmit);
			  	}
			  	//Bouton "Enregistrer et continuer"
			  	var querySaveAndContinue = query('input[id="btsubmit_continue"]', this.containerNode);
			  	if(querySaveAndContinue.length){
			  		domConstruct.destroy(querySaveAndContinue[0]);
			  	}
			  	/** fin cas particulier **/
			  	
				if(queryObject.what != 'notice'){
					switch(queryObject.what) {
						case 'authperso':
						case 'oeuvre_event':
							new FormEdit('autorites', this.getGridTypeEntity(queryObject.what)+'_'+queryObject.authperso_id, this.containerNode);
							break;
						default:
							new FormEdit('autorites', this.getGridTypeEntity(queryObject.what), this.containerNode);
							break;
					}
				}
//				new FormController();
				
//				var observer = new MutationObserver(function(mutations) {
//					console.log(mutations);
//					  mutations.forEach(function(mutation) {
//						  console.log('mutationCalled', mutation);
//						  resizeClosure();
//					  });
//					});
//				var config = { 
//			          attributes: true, 
//			          attributeOldValue: true,
//			          attributeFilter : ['style'],
//				};
//				
//				observer.observe(this.containerNode, config);
//				
				var formName = query("input[type='text'][data-pmb-deb-rech]", this.containerNode);
			  	if(formName.length){
			  		this.updateFormName(formName);
			  	}
				this.getParent().resizeIframe();
			},
			resize: function(){
				this.inherited(arguments);
			},
			_load: function(){
				// summary:
				//		Load/reload the href specified in this.href

				// display loading message
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

				hand.then(
					function(html){
						var fctToText = Function.prototype.toString.call(self.moveFields);
						var pattern = 'function(domXML)';
						
						if(fctToText.indexOf('function (domXML)') != -1){
							pattern = 'function (domXML)';
						}
						html = html.replace('new FormEdit();', '');
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
			
			destroy: function(){
				this.inherited(arguments);
				clearTimeout(this.resizeTimeout);
			},
			postForm: function(buttonClicked, forcing){
				var form = buttonClicked.form;
				var forcing = forcing || false;
				var verified = false;
				if(domAttr.get(form, 'action').indexOf('select.php') != -1){
					domAttr.set(form, 'action', domAttr.get(form, 'action').replace('select.php?', 'ajax.php?module=selectors&is_iframe=1&'));
				}
				if(forcing){
					domAttr.set(form, 'action', domAttr.get(form, 'action')+'&forcing=1');
				}
				if (typeof test_notice === 'function' && test_notice(form) || typeof test_form === 'function' && test_form(form)) {
					verified = true;
				}
				if (verified) {
					iframe(domAttr.get(buttonClicked.form, 'action'),{
						form: buttonClicked.form,
						handleAs: 'json',
					}).then(lang.hitch(this, function(data){
						console.log(data);
						if(parseInt(data.id) && (parseInt(data.id) !=0)){
							this.set('href', this.href);
							data.ghostContainerId = this.parameters.ghostContainerId;
							topic.publish('SubTabAdd', 'SubTabAdd', 'elementAdded', data);	
						}else if(data.html){
							var dialog = PMBDojoxDialogSimple({
								title: "",
								content: data.html,
							});
							var forcingForm = query('form', dialog.containerNode)[0];
							domAttr.remove(forcingForm, 'action');
							if(query('#forcing_button', dialog.containerNode)[0]) {
								var button = query('#forcing_button', dialog.containerNode)[0];
								domAttr.set(button, 'type', 'button');
								on(button, 'click', lang.hitch(this, 
									function(){
										this.postForm(buttonClicked, 1);
										dialog.hide();
									} ,
								buttonClicked));
							}
							dialog.show();
						} else {
							test_form(form);
						}
					}));
				}
				return false;
			},
			getGridTypeEntity: function(type){
				switch(type) {
					case 'auteur':
						return 'auteurs';
					case 'editeur':
						return 'editeurs';
					case 'collection':
						return 'collections';
					case 'subcollection':
						return 'souscollections';
					case 'categorie':
						return 'categories';
					case 'serie':
						return 'series';
					case 'indexint':
						return 'indexint';
					case 'titre_uniforme':
						return 'titres_uniformes';
					case 'authperso':
						return 'authperso';
					case 'ontology':
						return 'concepts';
					case 'oeuvre_event':
						return 'authperso';
				}
			},
			setSubmitEvent: function(queryResult){
				var submitButton = queryResult[0];
				domAttr.set(submitButton,'type', 'button');
				domAttr.remove(submitButton, 'onclick');
				on(submitButton, 'click', lang.hitch(this, this.postForm, submitButton, 0));
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
					//on reg�n�re le dom des textarea, le navigateur se contente d'affecter la propri�t� value... 
					var text_areas = document.getElementById(id).getElementsByTagName('textarea');
					for(var x=0 ; x<text_areas.length ; x++){
						if(!text_areas[x].firstChild){
							text_areas[x].appendChild(document.createTextNode(text_areas[x].value));
						}
					}
					//on reg�n�re le dom des select, le navigateur se contente d'affecter la propri�t� selected sans recr�er l'attribut... 
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
			},
			updateFormName : function(formName) {
				var debRech = '';
				this.parameters.selectorURL.split('?')[1].split('&').forEach((param) => {
					var item = param.split('=');
					if (item[0] == 'deb_rech' && item[1] != '*') debRech = decodeURIComponent(item[1]);
				});
				if (formName[0] && debRech) {
					formName[0].value = debRech;
				}
			}
		})
});