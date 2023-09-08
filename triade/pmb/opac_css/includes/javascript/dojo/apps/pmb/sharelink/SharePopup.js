// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SharePopup.js,v 1.4 2019-02-25 09:45:12 apetithomme Exp $


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
        constructor: function (link, params = {}) {
        	this.link = link;
			var body = BaseWindow.body();
			var html = BaseWindow.doc.documentElement;
			this.title = params.title || pmbDojo.messages.getMessage("shareLink", "share_link");
			this.inputType = params.inputType || 'text';
			this.docHeight = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );
			this.windowSize = win.getBox(); 
        	this.initOverlay();
        	this.buildPopup();
        },
        
        buildPopup: function(){
        	this.popupContainer = domConstruct.create('div',{
        		id:'popupContainer', 
        		'class':'sharePopupContainer uk-panel uk-panel-box'
        	});
        	var popupTopContainer = domConstruct.create('div', {id:'', 'class':'uk-modal-close uk-float-right'});
        	var popupTopA = domConstruct.create('a');
        	var popupTitle = domConstruct.create('h3', {id:'popupTitle', 'class':'left popupTitle uk-panel-title', innerHTML: this.title });
        	var popupCloseButton = domConstruct.create('i', {id:'popupCloseButton', 'class':"fa fa-times popupCloseButton", "aria-hidden" : "true"});
        	on(popupTopContainer, "click",lang.hitch(this, function() {
		    	this.destroy();
		    }));
			var linkContainer = domConstruct.create('div', {id:'linkContainer', 'class':'linkContainer'});        	
        	var linkInput = domConstruct.create((this.inputType == 'textarea' ? this.inputType : 'input'), {
        		id:'linkInput', 
        		'class':'linkInput', 
        		value : this.link, 
        		type: this.inputType,
        		readonly: 'readonly'
        	});
        	domStyle.set(linkInput,'width', '300px');
        	if (this.inputType == 'textarea') {
        		domStyle.set(linkInput, 'height', '70px');
        		domStyle.set(this.popupContainer, 'height', 'auto');
        	}
        	var buttonContainer = domConstruct.create('div', {id:'buttonContainer', 'class':'buttonContainer'});
        	var buttonCopy = domConstruct.create('input', {id:'buttonCopy', 'class':'uk-button-primary buttonCopy bouton', type : 'button', value : pmbDojo.messages.getMessage("shareLink", "copy_link")});
        	domStyle.set(buttonCopy,'float', 'right');
        	this.signal = on(buttonCopy,"click",lang.hitch(this, function() {
 		    	this.copyLink();
 		    }));
        	
        	popupTopContainer.appendChild(popupTopA);
        	popupTopA.appendChild(popupCloseButton);
        	this.popupContainer.appendChild(popupTopContainer);
        	this.popupContainer.appendChild(popupTitle);
        	this.popupContainer.appendChild(linkContainer);
        	linkContainer.appendChild(linkInput);
        	this.popupContainer.appendChild(buttonContainer);
        	buttonContainer.appendChild(buttonCopy);
        	
        	document.body.appendChild(this.popupContainer);
        	
        	linkInput.select();
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
		    on(this.overlay,"click",lang.hitch(this, function() {
		    	this.destroy();
		    }));
        },
        
        destroy : function() {
        	domConstruct.destroy(this.popupContainer);
        	domConstruct.destroy(this.overlay);
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
        }
        
    });
});