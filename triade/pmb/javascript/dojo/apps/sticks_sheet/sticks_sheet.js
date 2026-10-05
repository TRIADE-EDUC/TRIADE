// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sticks_sheet.js,v 1.2 2017/01/19 14:25:39 apetithomme Exp $

define([
        "dojo/_base/declare",
        "dijit/_WidgetBase",
        "dojo/dom-construct",
        "dojo/dom-style",
        "dijit/layout/BorderContainer",
        "dijit/layout/ContentPane",
        "dojo/on",
        "dojo/_base/lang",
        "dojo/dom",
        "dojo/topic",
        "dijit/registry",
        "dojo/dom-attr"
], function(declare, widgetBase, domConstruct, domStyle, BorderContainer, ContentPane, on, lang, dom, topic, registry, domAttr){
	/*
	 * Classe sticks_sheets
	 */
	return declare([widgetBase], {
		data: null,
		container : null,
		leftContainer : null,
		centerContainer : null,
		rightContainer : null,
		bottomContainer : null,
		topContainer : null,
		stickNodeSelected : null,
		sticksSheetSelected : 0,
		source: "",
		
		constructor: function(params) {
			this.data = params.data;
			this.source = params.source;
		},
		
		buildRendering: function() {
			this.domNode = domConstruct.create('div', {id: 'sticks_sheet_layout', style: "width: 900px;height: 600px;"});
			this.container = new BorderContainer({id:'bordercontainerid', style: "height: 100%; width: 100%;", design:"headline", gutters:true, liveSplitters:true});
			this.buildTopContainer();
			this.buildLeftContainer();
			this.buildCenterContainer();
			this.buildRightContainer();
			this.buildBottomContainer();
			this.container.addChild(this.topContainer);
			this.container.addChild(this.leftContainer);
			this.container.addChild(this.centerContainer);
			this.container.addChild(this.rightContainer);
			this.container.addChild(this.bottomContainer);
			this.container.placeAt(this.domNode);
			this.container.startup();
		},
		
		buildTopContainer: function() {
			var content = domConstruct.create('select', {name: 'sticks_sheet_id', id: 'sticks_sheet_selector'});
			for (var i in this.data) {
				if (!this.sticksSheetSelected) this.sticksSheetSelected = this.data[i].id;
				var currentOption = domConstruct.create('option', {value:this.data[i].id, innerHTML: this.data[i].label}, content);
				if (this.sticksSheetSelected == this.data[i].id) {
					domAttr.set(currentOption, 'selected', 'selected');
				}
			}
			on(content, 'change', lang.hitch(this, this.updateSticksSheet));
			this.topContainer = new ContentPane({
				region: "top",
				content: content
			});
		},
		
		buildLeftContainer : function() {
			this.leftContainer = new ContentPane({
				region: "left",
				splitter: "true",
		        style: "width: 20%",
		        content: this.getLeftContent()
			});
		},
		
		getLeftContent: function() {
			var content = domConstruct.create('div', {});
			var table = domConstruct.create('table', {}, content);
			var tr = domConstruct.create('tr', {}, table);
			domConstruct.create('td', {innerHTML: pmbDojo.messages.getMessage("sticks_sheet","sticks_sheet_label")}, tr);
			domConstruct.create('td', {innerHTML: this.data[this.sticksSheetSelected].label}, tr);
			tr = domConstruct.create('tr', {}, table);
			domConstruct.create('td', {innerHTML: pmbDojo.messages.getMessage("sticks_sheet","sticks_sheet_page_format")}, tr);
			domConstruct.create('td', {innerHTML: this.data[this.sticksSheetSelected].page_format}, tr);
			tr = domConstruct.create('tr', {}, table);
			domConstruct.create('td', {innerHTML: pmbDojo.messages.getMessage("sticks_sheet","sticks_sheet_page_orientation")}, tr);
			domConstruct.create('td', {innerHTML: this.data[this.sticksSheetSelected].page_orientation}, tr);
			tr = domConstruct.create('tr', {}, table);
			domConstruct.create('td', {innerHTML: pmbDojo.messages.getMessage("sticks_sheet","sticks_sheet_unit")}, tr);
			domConstruct.create('td', {innerHTML: this.data[this.sticksSheetSelected].unit}, tr);
			tr = domConstruct.create('tr', {}, table);
			domConstruct.create('td', {innerHTML: pmbDojo.messages.getMessage("sticks_sheet","sticks_sheet_nbr_x_sticks")}, tr);
			domConstruct.create('td', {innerHTML: this.data[this.sticksSheetSelected].nbr_x_sticks}, tr);
			tr = domConstruct.create('tr', {}, table);
			domConstruct.create('td', {innerHTML: pmbDojo.messages.getMessage("sticks_sheet","sticks_sheet_nbr_y_sticks")}, tr);
			domConstruct.create('td', {innerHTML: this.data[this.sticksSheetSelected].nbr_y_sticks}, tr);
			tr = domConstruct.create('tr', {}, table);
			domConstruct.create('td', {innerHTML: pmbDojo.messages.getMessage("sticks_sheet","sticks_sheet_stick_width")}, tr);
			domConstruct.create('td', {innerHTML: this.data[this.sticksSheetSelected].stick_width}, tr);
			tr = domConstruct.create('tr', {}, table);
			domConstruct.create('td', {innerHTML: pmbDojo.messages.getMessage("sticks_sheet","sticks_sheet_stick_height")}, tr);
			domConstruct.create('td', {innerHTML: this.data[this.sticksSheetSelected].stick_height}, tr);
			tr = domConstruct.create('tr', {}, table);
			domConstruct.create('td', {innerHTML: pmbDojo.messages.getMessage("sticks_sheet","sticks_sheet_left_margin")}, tr);
			domConstruct.create('td', {innerHTML: this.data[this.sticksSheetSelected].left_margin}, tr);
			tr = domConstruct.create('tr', {}, table);
			domConstruct.create('td', {innerHTML: pmbDojo.messages.getMessage("sticks_sheet","sticks_sheet_top_margin")}, tr);
			domConstruct.create('td', {innerHTML: this.data[this.sticksSheetSelected].top_margin}, tr);
			tr = domConstruct.create('tr', {}, table);
			domConstruct.create('td', {innerHTML: pmbDojo.messages.getMessage("sticks_sheet","sticks_sheet_x_sticks_spacing")}, tr);
			domConstruct.create('td', {innerHTML: this.data[this.sticksSheetSelected].x_sticks_spacing}, tr);
			tr = domConstruct.create('tr', {}, table);
			domConstruct.create('td', {innerHTML: pmbDojo.messages.getMessage("sticks_sheet","sticks_sheet_y_sticks_spacing")}, tr);
			domConstruct.create('td', {innerHTML: this.data[this.sticksSheetSelected].y_sticks_spacing}, tr);
			return content;
		},
		
		buildCenterContainer : function() {
			this.centerContainer = new ContentPane({
				region: "center",
		        style: "width: 60%",
		        content: this.getCenterContent()
			});
		},
		
		getCenterContent: function() {
			var stick_sheet_page = domConstruct.create('div', {id: 'sticks_sheet_page'});
			domConstruct.create('style', {innerHTML: '.stick:hover {background-color: grey}'}, stick_sheet_page);
			domStyle.set(stick_sheet_page, {
				width: this.data[this.sticksSheetSelected].page_sizes[0]+'px',
				height: this.data[this.sticksSheetSelected].page_sizes[1]+'px',
				position: 'relative',
				border: 'solid 1px black',
				margin: 'auto'
			});
			var stick = null;
			for (var j = 0; j < this.data[this.sticksSheetSelected].nbr_y_sticks; j++) {
				for (var i = 0; i < this.data[this.sticksSheetSelected].nbr_x_sticks; i++) {
					stick = domConstruct.create('div', {id: 'stick_'+i+'_'+j, class: 'stick'}, stick_sheet_page);
					domStyle.set(stick, {
						width: this.data[this.sticksSheetSelected].stick_width+'px',
						height: this.data[this.sticksSheetSelected].stick_height+'px',
						position: 'absolute',
						border: 'solid 1px black',
						left: (parseFloat(this.data[this.sticksSheetSelected].left_margin) + (i*parseFloat(this.data[this.sticksSheetSelected].x_sticks_spacing))) + 'px',
						top: (parseFloat(this.data[this.sticksSheetSelected].top_margin) + (j*parseFloat(this.data[this.sticksSheetSelected].y_sticks_spacing))) + 'px'
					});
					on(stick, "click", lang.hitch(this, this.stickClick, stick));
					if(this.stickNodeSelected == null) {
						this.selectStick(stick);
					}
				}
			}
			return stick_sheet_page;
		},
		
		buildRightContainer : function() {
			var stickCoords = this.stickNodeSelected.id.split('_');
			
			var content = domConstruct.create('div', {});
			var table = domConstruct.create('table', {}, content);
			var tr = domConstruct.create('tr', {}, table);
			domConstruct.create('td', {innerHTML: pmbDojo.messages.getMessage("sticks_sheet","sticks_sheet_x_stick_selected")}, tr);
			domConstruct.create('td', {id: 'x_stick_selected', innerHTML: parseInt(stickCoords[2])+1}, tr);
			tr = domConstruct.create('tr', {}, table);
			domConstruct.create('td', {innerHTML: pmbDojo.messages.getMessage("sticks_sheet","sticks_sheet_y_stick_selected")}, tr);
			domConstruct.create('td', {id: 'y_stick_selected', innerHTML: parseInt(stickCoords[1])+1}, tr);
			tr = domConstruct.create('tr', {}, table);
			
			this.rightContainer = new ContentPane({
				region: "right",
				splitter: "true",
				style: "width: 20%",
				content: content
			});
		},
		
		buildBottomContainer : function() {
			var content = domConstruct.create('input', {type: 'button', class: 'bouton', value: pmbDojo.messages.getMessage("sticks_sheet","sticks_sheet_dialog_validate")});
			on(content, 'click', lang.hitch(this, this.validate));
			this.bottomContainer = new ContentPane({
				region: "bottom",
				content: content
			});
		},
		
		postCreate : function() {
			this.inherited(arguments);
			this.container.resize();
		},
		
		selectStick : function(stickNode) {
			domStyle.set(stickNode, {
				backgroundColor: 'blue'
			});
			this.stickNodeSelected = stickNode;
		},
		
		unselectStick : function(stickNode) {
			domStyle.set(stickNode, {
				backgroundColor: ''
			});
			this.stickNodeSelected = null;
		},
		
		stickClick: function(stickNode) {			
			if(this.stickNodeSelected.id != stickNode.id) {
				this.unselectStick(this.stickNodeSelected);
				this.selectStick(stickNode);
				this.updateRightContainer();
			}
		},
		
		updateRightContainer : function() {
			var stickCoords = this.stickNodeSelected.id.split('_');
			dom.byId('x_stick_selected').innerHTML = parseInt(stickCoords[2])+1;
			dom.byId('y_stick_selected').innerHTML = parseInt(stickCoords[1])+1;
		},
		
		validate: function(stick) {
			var stickCoords = this.stickNodeSelected.id.split('_');
			topic.publish("sticks_sheet","validate_stick_selected", {
				id : this.data[this.sticksSheetSelected].id,
				source : this.source,
				x_stick_selected : parseInt(stickCoords[2])+1, 
				y_stick_selected : parseInt(stickCoords[1])+1
			});
			registry.byId("sticks_sheets_stick_select_dialog").hide();
		},
		
		updateSticksSheet : function(e) {
			if (e) {
				this.sticksSheetSelected = e.target.value;
			}
			this.stickNodeSelected = null;
			this.leftContainer.destroyDescendants();
			this.centerContainer.destroyDescendants();
			this.leftContainer.set('content', this.getLeftContent());
			this.centerContainer.set('content', this.getCenterContent());
			this.updateRightContainer();
			var sticksSheetsOptions = dom.byId('sticks_sheet_selector').childNodes;
			for (var i = 0; i < sticksSheetsOptions.length; i++) {
				if (domAttr.get(sticksSheetsOptions[i], 'selected')) {
					domAttr.remove(sticksSheetsOptions[i], 'selected');
				}
				if (sticksSheetsOptions[i].value == this.sticksSheetSelected) {
					dom.byId('sticks_sheet_selector').value = this.sticksSheetSelected;
					domAttr.set(sticksSheetsOptions[i], 'selected', 'selected');
				}
			}
		},
		
		setSource : function(source) {
			this.source = source;
		},
		
		setSticksSheetSelected : function(sticksSheetSelected) {
			this.sticksSheetSelected = sticksSheetSelected;
		}
	});
});
