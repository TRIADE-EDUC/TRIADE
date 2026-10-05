// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PMBMainDialog.js,v 1.2.18.1 2025/02/28 14:05:08 tsamson Exp $


define(["dojo/_base/declare", 
        "dojox/widget/DialogSimple", 
        "dojo/_base/lang", 
        "dojo/dom-class", 
        "dojo/window", 
        "dojo/on", 
        "dojo/_base/lang", 
        "dojo/dom-geometry", 
        "dojo/dom-style",
        "dojo/_base/array",
        "dojo/sniff",
        "dijit/layout/utils",
        ], function(declare, Dialog, lang, domClass, win, on, lang, domGeometry, domStyle, array, has, utils){

	  return declare(Dialog, {
		  lastState: {},
		  expanded: false,
		  initialResize: false,
		  postCreate: function(){
			this.inherited(arguments);  
			on(this.titleBar, 'dblclick', lang.hitch(this, this.toggleSize));
			domStyle.set(this.containerNode, 'overflow', "auto");
		  },
		  show: function(){
			  this.inherited(arguments);
			  if (!domClass.contains(document.body, "dojoDialogOpened")){
				  domClass.add(document.body, "dojoDialogOpened");
			  }
			  if(!this.initialResize){
				var viewport = win.getBox(this.ownerDocument);
				
				if(!this.dim){
					this.dim = {
						w: Math.round(0.60 * viewport.w),
						h: Math.round(0.70 * viewport.h),
					};	
				}
				this.resize(this.dim);
				this.initialResize = true;
				this._position();
			  }
		  },
		  hide: function(){
			  this.inherited(arguments);
			  this.postClose();
		  },
		  destroy : function() {
			  this.inherited(arguments);
			  this.postClose();
			
		  },
		  postClose : function() {
			if (domClass.contains(document.body, "dojoDialogOpened")){
				  domClass.remove(document.body, "dojoDialogOpened");
				  if(this.expanded){ 
					  //nous n'avons qu'une instance du popup, il 
					  //faut donc le réinitialiser pour avoir un comportement logique 
					  this.expanded = false;
					  this.collapse(); 
				  }
				  this.initialResize = false;
			  }
		  },
		  toggleSize: function(){
			  /**
			   * TODO: Ajout d'un test pour le placement en hauteur du dijit (la title bar ne doit jamais être masquée);
			   * Récupérer position top et left
			   */
			  if(!this.expanded){
				  this.expand();
			  }else{
				  this.collapse();
			  }
		  },
		  expand: function(){
			  var size = win.getBox();
			  var marginSize = domGeometry.getMarginBox(this.domNode);
			  var currentScroll = domGeometry.docScroll().y;
			  this.lastState = domGeometry.getContentBox(this.domNode);
			  
			  this.lastState.t = marginSize.t;
			  this.lastState.l = marginSize.l;
			  this.resize(size);
			  this.expanded = true;
		  },
		  collapse: function(){
			  this.resize(this.lastState);
			  this.expanded = false;
			  domStyle.set(this.domNode, {top: this.lastState.top+"px"})  
		  },
		  _position: function(){
				if(!domClass.contains(this.ownerDocumentBody, "dojoMove")){    // don't do anything if called during auto-scroll
					var node = this.domNode,
						viewport = win.getBox(this.ownerDocument),
						p = this._relativePosition,
						bb = p ? null : domGeometry.position(node),
						l = Math.floor(viewport.l + (p ? p.x : (viewport.w - bb.w) / 2)),
						t = Math.floor(viewport.t + (p ? p.y : (viewport.h - bb.h) / 2))
						;
					if(Math.sign(t) === -1){
						t = 0;
					}
					var height = domStyle.get(node, 'height');
					var size = win.getBox();
					if(height > size.h){
						height = size.h;
					}
					domStyle.set(node, {
						left: l + "px",
						top: t + "px",
						
					});
					if(height != 0){
						domStyle.set(node, {
							height: height + "px"
						});	
					}
				}else{
				}
		  },
		  resize: function(dim){
				if(this.domNode.style.display != "none"){

					this._checkIfSingleChild();

					if(!dim){
						if(this._shrunk){
							// If we earlier shrunk the dialog to fit in the viewport, reset it to its natural size
							if(this._singleChild){
								if(typeof this._singleChildOriginalStyle != "undefined"){
									this._singleChild.domNode.style.cssText = this._singleChildOriginalStyle;
									delete this._singleChildOriginalStyle;
								}
							}
							array.forEach([this.domNode, this.containerNode, this.titleBar, this.actionBarNode], function(node){
								if(node){	// because titleBar may not be defined
									domStyle.set(node, {
										position: "static",
										width: "auto",
										height: "auto"
									});
								}
							});
							this.domNode.style.position = "absolute";
						}

						// If necessary, shrink Dialog to fit in viewport and have some space around it
						// to indicate that it's a popup.  This will also compensate for possible scrollbars on viewport.
						var viewport = win.getBox(this.ownerDocument);
						viewport.w *= this.maxRatio;
						viewport.h *= this.maxRatio;

						var bb = domGeometry.position(this.domNode);
						if(bb.w >= viewport.w || bb.h >= viewport.h){
							dim = {
								w: Math.min(bb.w, viewport.w),
								h: Math.min(bb.h, viewport.h)
							};
							this._shrunk = true;
						}else{
							this._shrunk = false;
						}
					}

					// Code to run if user has requested an explicit size, or the shrinking code above set an implicit size
					if(dim){
						// Set this.domNode to specified size
						domGeometry.setMarginBox(this.domNode, dim);

						// And then size this.containerNode
						var layoutNodes = [];
						if(this.titleBar){
							layoutNodes.push({domNode: this.titleBar, region: "top"});
						}
						if(this.actionBarNode){
							layoutNodes.push({domNode: this.actionBarNode, region: "bottom"});
						}
						var centerSize = {domNode: this.containerNode, region: "center"};
						layoutNodes.push(centerSize);

						var contentDim = utils.marginBox2contentBox(this.domNode, dim);
						utils.layoutChildren(this.domNode, contentDim, layoutNodes);

						// And then if this.containerNode has a single layout widget child, size it too.
						// Otherwise, make this.containerNode show a scrollbar if it's overflowing.
						if(this._singleChild){
							var cb = utils.marginBox2contentBox(this.containerNode, centerSize);
							// note: if containerNode has padding singleChildSize will have l and t set,
							// but don't pass them to resize() or it will doubly-offset the child
							this._singleChild.resize({w: cb.w, h: cb.h});
							// TODO: save original size for restoring it on another show()?
						}else{
							this.containerNode.style.overflow = "auto";
							this._layoutChildren();		// send resize() event to all child widgets
						}
					}else{
						this._layoutChildren();		// send resize() event to all child widgets
					}

					if(!has("touch") && !dim){
						// If the user has scrolled the viewport then reposition the Dialog.  But don't do it for touch
						// devices, because it will counteract when a keyboard pops up and then the browser auto-scrolls
						// the focused node into view.
						this._position();
					}
				}
			},
	  });
});