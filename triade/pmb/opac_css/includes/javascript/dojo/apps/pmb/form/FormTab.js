// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormTab.js,v 1.3.6.1.2.1 2025/02/14 10:47:58 dgoron Exp $


define([
        'dojo/_base/declare',
        'dojo/dom',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/request/xhr',
        'dojo/dom-form',
        'dijit/layout/TabContainer',
        'dijit/layout/ContentPane',
        'dojo/query',
        'dojo/ready',
        'dojo/topic',
        'dijit/registry',
        'dojo/dom-attr',
        'dojo/dom-geometry',
        'dojo/dom-construct',
        'dojo/dom-style',
        'dijit/layout/LayoutContainer',
        'dijit/layout/BorderContainer',
        'dojox/layout/ContentPane',
        'apps/pmb/form/FormSelector',
        'apps/pmb/form/GhostContainer'
        ], function(declare, dom, on, lang, xhr, domForm, TabContainer, ContentPane, query, ready, topic, registry, domAttr, 
        		geometry, domConstruct, domStyle, LayoutContainer, BorderContainer, ContentPaneDojox, FormSelector, GhostContainer){
		return declare([ContentPane], {
			context: null,
			selectorContainer: null,
			ghostContainer: null,
			formNode: null,
			parameters: null,
			constructor: function(data) {
				this.parameters = data;
				this.title = data.field.getAttribute('title');
			},
			handleEvents: function(evtType,evtArgs){
				switch(evtType){
					case 'savedForm':
						break;
						
				}
			},
			postCreate: function() {
				this.inherited(arguments);
				//Récupération du contexte de l'élément sur lequel on a cliqué (formulaire)
				this.context = this.field.querySelector('input').form;
				
				//A voir pour faire une dérivation du layour container afin de 
				//faire la fermeture de l'onglet de façon plus propre
				this.ghostContainer = new GhostContainer({region: 'top', parameters: this.parameters});

				this.parameters.ghostContainerId = this.ghostContainer.id;
				
				this.selectorContainer = new ContentPane({region: 'bottom', doLayout: false, style: 'width:98%; height:100%;', parameters: this.parameters});
				this.iframe = domConstruct.create('iframe', {seamless: '', 'class': 'selectorsIframe', scrolling: 'no', style:{ width: '100%'}, src: this.parameters.selectorURL});
				
				this.selectorContainer.startup();
				this.selectorContainer.resize();
				this.resize();
				
				domConstruct.place(this.iframe, this.selectorContainer.containerNode, "last");
				this.ghostContainer.startup();				
				this.addChild(this.ghostContainer);
				this.addChild(this.selectorContainer);
				this.startup();
				this.resize();
				
				on(this.iframe, 'load', lang.hitch(this, function(){
					if(this.iframe.contentWindow != null) {
						this.iframe.style.height = this.iframe.contentWindow.document.body.scrollHeight+50+'px';
					}
				}));
			},
			resizeIframe: function(){
				if(this.iframe.contentWindow != null) {
					this.iframe.style.height = parseInt(this.iframe.contentWindow.document.body.scrollHeight)+35+'px';
				}
			},
			onFocus: function(){
				this.inherited(arguments);
				if(this.iframe.contentWindow != null) {
					this.iframe.style.height = this.iframe.contentWindow.document.body.scrollHeight+50+'px';
				}
			},
			onShow: function(){
				this.inherited(arguments);
				if(this.iframe.contentWindow != null) {
					this.iframe.style.height = this.iframe.contentWindow.document.body.scrollHeight+50+'px';
				}
			},
			onClose: function() {
				var button = this.parameters.field.querySelector('input.'+this.id);
				if (button) {
					button.removeAttribute('disabled')
					button.classList.remove('disabled')
					button.classList.remove(this.id)
				}
				return true;
			}
		})
});