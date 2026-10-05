// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ExpandoPane.js,v 1.1 2018/01/25 11:19:49 vtouchard Exp $

define(["dojo/_base/declare", 
        "dojox/layout/ExpandoPane", 
        "dijit/layout/ContentPane", 
        "dojo/dom-construct", 
        "dojo/dom", 
        "dojo/on", 
        "dojo/topic",
        "dojo/_base/lang",
        "dijit/registry",
        "dijit/form/Button",
        "dojo/query",
        "dojo/dom-style"], function(declare, ExpandoPane, ContentPane, domConstruct, dom, on, topic, lang, registry, Button, query, domStyle){
	
	return declare([ExpandoPane], {
		templateString: '<div class="dojoxExpandoPane"><div dojoAttachPoint="titleWrapper" class="dojoxExpandoTitle"><div class="center dojoxExpandoIcon" dojoAttachPoint="iconNode" dojoAttachEvent="ondijitclick:toggle" id="expandoPaneIcon"><i class="fa fa-caret-left" aria-hidden="true"></i></div><span class="center dojoxExpandoTitleNode" dojoAttachPoint="titleNode">${title}</span></div><div class="dojoxExpandoWrapper" dojoAttachPoint="cwrapper" dojoAttachEvent="ondblclick:_trap"><div class="dojoxExpandoContent" dojoAttachPoint="containerNode"></div></div></div>',

		// easeOut: String|Function
		//		easing function used to hide pane
		easeOut: "dojo._DefaultEasing", // FIXME: This won't work with globalless AMD

		// easeIn: String|Function
		//		easing function use to show pane
		easeIn: "dojo._DefaultEasing", // FIXME: This won't work with globalless AMD

		// duration: Integer
		//		duration to run show/hide animations
		duration: 50,

		// startExpanded: Boolean
		//		Does this widget start in an open (true) or closed (false) state
		startExpanded: false,

		// previewOpacity: Float
		//		A value from 0 .. 1 indicating the opacity to use on the container
		//		when only showing a preview
		previewOpacity: 0.75,

		// previewOnDblClick: Boolean
		//		If true, will override the default behavior of a double-click calling a full toggle.
		//		If false, a double-click will cause the preview to popup
		previewOnDblClick: false,
		
		// tabIndex: String
		//		Order fields are traversed when user hits the tab key
		tabIndex: "0",
		icons: {
			vertical:{
				opened:'<i style="margin:auto;" class="fa fa-caret-left" aria-hidden="true"></i>',
				closed:'<i style="margin:auto;" class="fa fa-caret-right" aria-hidden="true"></i>',
			},
			horizontal:{
				opened:'<i style="margin:auto;" class="fa fa-caret-down" aria-hidden="true"></i>',
				closed:'<i style="margin:auto;" class="fa fa-caret-up" aria-hidden="true"></i>',
			}
		},
		direction: '',
		_setTabIndexAttr: "iconNode",
		baseClass: "dijitExpandoPane",
		constructor: function(){
		},
		postCreate:function(){
			this.inherited(arguments);
			this._iconContainer = query('#expandoPaneIcon', this.domNode)[0];
			if( !((this.direction == "vertical") || (this.direction == "horizontal")) ){
				throw 'ExpandoPane:: "direction" parameter must be filled (vertical or horizontal) '
			}
			
			var titleNode = query('.dojoxExpandoTitleNode', this.domNode)[0];
			domConstruct.empty(titleNode);
			this._setHandleStyle();
			this._startupSizes();
			this._setIcon();
			domStyle.set(this.containerNode, 'overflow', 'auto');
		},
		toggle: function(){
			// summary:
			//		Toggle this pane's visibility
			if(this._showing){
				this._hideWrapper();
				this._showAnim && this._showAnim.stop();
				this._hideAnim.play();
			}else{
				this._hideAnim && this._hideAnim.stop();
				this._showAnim.play();
			}
			this._showing = !this._showing;
			this.domNode.setAttribute("aria-expanded", this._showing);
			this._setIcon();
		},
		_setIcon: function(){
			domConstruct.empty(this._iconContainer);
			if(this._showing){
				domConstruct.place(this.icons[this.direction]['opened'], this._iconContainer, 'first');
			}else{
				domConstruct.place(this.icons[this.direction]['closed'], this._iconContainer, 'first');
			}
		},
		_setHandleStyle: function(){
			var dojoxExpandoTitle = query('.dojoxExpandoTitle', this.domNode)[0];
			var commonStyleIconContainer = {
				padding: '10px'
			};
			var commonStyleExpandoTitle = {
					
			};
			
			if(this.direction == "vertical"){
				commonStyleIconContainer['height'] = '100%';
				commonStyleIconContainer['display'] = 'inline-flex';
				
				commonStyleExpandoTitle['height'] = '100%';
				commonStyleExpandoTitle['float'] = 'right';
				
			}
			domStyle.set(this._iconContainer, commonStyleIconContainer);
			domStyle.set(dojoxExpandoTitle, commonStyleExpandoTitle);
		}
	});
});

