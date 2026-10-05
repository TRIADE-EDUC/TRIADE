// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SharePopup.js,v 1.10.4.1 2025/02/27 14:05:55 dgoron Exp $


define(["dojo/_base/declare",
    "dojo/topic",
    "dojo/_base/lang",
    "dijit/_WidgetBase",
    "dojo/dom",
    "dojo/dom-construct",
    "dojo/dom-attr",
    "dojo/dom-class",
    "dojo/dom-style",
    "dojo/on",
    "dojo/window",
    "dojo/_base/window",
    ], function (declare, topic, lang, WidgetBase, dom, domConstruct, domAttr, domClass, domStyle, on, win, BaseWindow) {

    return declare(null,{
    	overlay : null,
    	popupContainer : null,
        centerNode: null,
        windowSize : null,
        docHeight : null,
        signal : null,
        title: '',
		inputType: '',
		domNodeSource : '',

        constructor: function (link, params = {}) {
        	this.link = link;
			var body = BaseWindow.body();
			var html = BaseWindow.doc.documentElement;
			this.title = params.title || pmbDojo.messages.getMessage("shareLink", "share_link");
			this.inputType = params.inputType || 'text';
			this.domNodeSource = params.domNodeSource || null;
			this.docHeight = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );
			this.windowSize = win.getBox(); 
        	this.initOverlay();
        	this.buildPopup();
        },
        
        buildPopup: function() {
        	this.popupContainer = domConstruct.create('div',{
        		'id': 'popupContainer', 
				'class': 'sharePopupContainer uk-panel uk-panel-box',
				'role': 'dialog',
				'aria-modal': 'true',
				'aria-labelledby' : 'popupTitle'
        	});
			
        	var popupTopContainer = domConstruct.create('div', {
				'class': 'uk-modal-close uk-float-right'
			});
			
        	var popupTopA = domConstruct.create('button', {
				'type': 'button', 
				'class': 'button-unstylized', 
				'title': pmbDojo.messages.getMessage("opac", "rgaa_close_modal"),
				'aria-label': pmbDojo.messages.getMessage("opac", "rgaa_close_modal")
			});
			popupTopA.appendChild(domConstruct.create('span', {
				'class':'visually-hidden',
				'innerHTML': pmbDojo.messages.getMessage("opac", "rgaa_close_modal") 
			}));

        	var popupTitle = domConstruct.create('h3', {
				'id': 'popupTitle', 
				'class': 'left popupTitle uk-panel-title', 
				'innerHTML': this.title 
			});

        	var popupCloseButton = domConstruct.create('i', {
				'id': 'popupCloseButton', 
				'class':"fa fa-times popupCloseButton", 
				'aria-hidden': 'true'
			});
			
			on(popupTopContainer, "click",lang.hitch(this, function() {
		    	this.destroy();
		    }));

			var linkContainer = domConstruct.create('div', {
				'id': 'linkContainer',
				'class':'linkContainer'
			});  
			
			var linkLabel = domConstruct.create('label', {
				'for': 'linkInput',
				'class': 'linkLabel visually-hidden', innerHTML: this.title
			});

        	var linkInput = domConstruct.create((this.inputType == 'textarea' ? this.inputType : 'input'), {
        		'id': 'linkInput', 
        		'class': 'linkInput', 
        		'value': this.link, 
        		'type': this.inputType,
        		'readonly': 'readonly'
        	});

        	domStyle.set(linkInput, 'width', '300px');

        	if (this.inputType == 'textarea') {
        		domStyle.set(linkInput, 'height', '70px');
        		domStyle.set(this.popupContainer, 'height', 'auto');
        	}

        	var buttonContainer = domConstruct.create('div', {
				'id': 'buttonContainer', 
				'class': 'buttonContainer'
			});

        	var buttonCopy = domConstruct.create('input', {
				'id': 'buttonCopy',
				'class': 'uk-button-primary buttonCopy bouton',
				'type': 'button',
				'aria-label': pmbDojo.messages.getMessage("opac", "rgaa_share_copy_link"),
				'value': pmbDojo.messages.getMessage("shareLink", "copy_link")
			});

        	domStyle.set(buttonCopy,'float', 'right');
        	this.signal = on(buttonCopy,"click", lang.hitch(this, function() {
 		    	this.copyLink();
 		    }));
        	
        	popupTopContainer.appendChild(popupTopA);
        	popupTopA.appendChild(popupCloseButton);

        	this.popupContainer.appendChild(popupTopContainer);
        	this.popupContainer.appendChild(popupTitle);
        	this.popupContainer.appendChild(linkContainer);

        	linkContainer.appendChild(linkLabel);
        	linkContainer.appendChild(linkInput);

        	this.popupContainer.appendChild(buttonContainer);
        	buttonContainer.appendChild(buttonCopy);
        	
        	document.body.appendChild(this.popupContainer);
        	
			linkInput.select();

			on(document.body, "keyup", lang.hitch(this, this.isEscapeKey));
			if(typeof focus_trap == 'function') {
				focus_trap(this.popupContainer);
			}
        },
        
        initOverlay:function(){
		    this.overlay = domConstruct.create("div", {
		          id: 'unload_layer',
		          style: {
		            position: "absolute",
		            top: "0px",
		            left: "0px",
		            width: this.windowSize.w + "px",
		            height: this.docHeight + "px",
		            backgroundColor: "gray",
		            opacity: 0.6,
		            zIndex: '1000'
		       }
		    }, BaseWindow.body());
		    this.overlay.appendChild(domConstruct.create("p"));
			/* non RGAA
		    on(this.overlay,"click",lang.hitch(this, function() {
		    	this.destroy();
		    }));
			*/
        },
        
        destroy : function() {
        	domConstruct.destroy(this.popupContainer);
			domConstruct.destroy(this.overlay);
			// Repositionnement du focus sur le bouton déclencheur de la modal
			if (this.domNodeSource) {
				this.domNodeSource.focus();
			}
        },
        
        copyLink : function() {
        	try {
				dom.byId("linkInput").select();
				var copy_success = document.execCommand('copy');
				if (copy_success) {	
					var buttonCopy = dom.byId("buttonCopy"); 
					buttonCopy.value = pmbDojo.messages.getMessage("shareLink", "copied_link");	
					domClass.add(buttonCopy, 'uk-button-success');
					this.signal.remove();
					this.signal = on(buttonCopy,"click",lang.hitch(this, function() {
						this.copyLink();
		 		    	this.destroy();
		 		    }));
				}
			} catch (e) {
				dom.byId("linkInput").select();
			}
        },
        
        isEscapeKey: function(e) {
        	if(e.keyCode == 27) {
        		this.destroy();
        	}
        }
        
    });
});