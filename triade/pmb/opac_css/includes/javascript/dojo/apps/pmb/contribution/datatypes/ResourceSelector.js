// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ResourceSelector.js,v 1.18 2022/04/12 14:32:19 qvarin Exp $


define([
        'dojo/_base/declare',
        'dijit/_WidgetBase',
        'dojo/on',
        'dojo/_base/lang',
        'dojo/io-query',
        'dojo/request',
        'dojo/dom',
        'dojo/dom-construct', 
        'dojo/dom-attr' 
], function(declare, _WidgetBase, on, lang, ioQuery, request, dom, domConstruct, domAttr){
	return declare([_WidgetBase], {

		target: './ajax_selector.php',
		
		datalist: [],
		
		datalistNode: null,
		
		valueNode: null,
		
		draftNode: null,
		
		templateNode : null,
		
		lastValue: null,
		
		attIdFilter: null,

		requestTimeout: null,

		loaderNode: null,

		ImgloaderNode: null,
		
		attIdFilterEnableFor: [
			"concept",
			"onto"
		],
		
		postCreate: function() {
			this.inherited(arguments);
			this.datalistNode = dom.byId(this.domNode.id + '_list');
			this.valueNode = dom.byId(this.valueNodeId);
			this.draftNode = dom.byId(this.isDraftNodeId);
			this.templateNode = dom.byId(this.templateNodeId);
			on(this.domNode, 'keyup', lang.hitch(this, this.updateDatalist));
			on(this.domNode, 'input', lang.hitch(this, this.updateValue));
			if (this.valueNode.value) {
				this.updateTemplate();
			}
			
			if (domAttr.has(this.domNode, 'att_id_filter')) {
				this.attIdFilter = domAttr.get(this.domNode, 'att_id_filter');
			}
			this.initLoader()
		},
	
		updateDatalist: function(e) {
			if (this.domNode.value == this.lastValue) {
				return false;
			}

			if (this.requestTimeout != null) {
				clearTimeout(this.requestTimeout);
				this.requestTimeout = null;
			}
			
			this.lastValue = this.domNode.value;
			var data = this.domNode.value;
			if (!data) {
				data = "*";
			}
			
			// Pour eviter le spam de requête, on met un timeout
			this.requestTimeout = setTimeout(lang.hitch(this, this.fetchDataList, data), 500);
		},
		
		fetchDataList: function(data) {
			
			// Si completion est vide on ne fait pas de requête ajax
			if (!this.completion || this.completion == '') {
				return this.setDatalist([]);
			}
			
			var params = 'handleAs=json&completion='+this.completion+'&autexclude='+this.autexclude+'&param1='+this.param1+'&param2='+this.param2+'&from_contrib=1';
			params += '&datas=' + data;
			if (this.attIdFilterEnableFor.includes(this.completion)) {
				params += '&att_id_filter=' + this.attIdFilter;
			}
			this.showLoader();
			request.post(this.target, {
				handleAs: 'json',
				data: params
			}).then(lang.hitch(this, function(data) {
				this.setDatalist(data);
				this.hiddenLoader();
			}), function(err){console.log(err);});
		},
		
		setDatalist: function(data) {
			domConstruct.empty(this.datalistNode);
			this.datalist = [];
			for (var element of data) {
				if (element.value) {
					this.datalist[element.value] = element.label;
					domConstruct.create('option', {
						innerHTML: element.label,
						"data-id_entity": element.value,
					}, this.datalistNode);
				}
			}
			this.domNode.focus();
		},
		
		updateValue: function(e) {
			for (var id in this.datalist) {
				if (this.formatString(this.datalist[id]) == this.formatString(this.domNode.value)) {
					this.removeDraft();
					this.valueNode.value = id;
					this.domNode.setCustomValidity("");
					this.domNode.blur();
					this.updateTemplate();
					return true;
				}
			}
			this.valueNode.value = '';
			this.domNode.setCustomValidity("Invalid field.");
			return false;
		},
		
		updateTemplate : function() {
			//recupération du template
			if (this.templateNode) {
				let params = ioQuery.queryToObject(document.location.search.substring(1));
				var url = './ajax.php?module=ajax&categ=contribution&sub=get_resource_template&type='+this.completion+'&id='+encodeURIComponent(this.valueNode.value)+'&area_id='+params.area_id;			
				request.get(url, {
					handleAs: 'text'
				}).then(lang.hitch(this, function(tpl) {
					if (this.templateNode) {
						this.templateNode.innerHTML = tpl;
					}
				}), function(err){console.log(err);});
			}
		},
		
		removeDraft: function () {
			if (this.domNode) {
				var parent = this.domNode.parentNode
				if (parent) {
					var parentContainer = parent.parentNode
					if (parentContainer && parentContainer.classList.value.includes('contribution_draft')) {
						parentContainer.classList.remove('contribution_draft')
					}
				}
			}
			
			if (this.draftNode && this.draftNode != 0) {
				this.draftNode.value = 0;
			}
			
			var editButtonNode = dom.byId(this.baseId+"_edit");
			if (editButtonNode) {
				editButtonNode.type = "hidden";
			}
		},
		
		updateDisplayLabel : function() {
			//recupération du template
			let params = ioQuery.queryToObject(document.location.search.substring(1));
			var url = './ajax.php?module=ajax&categ=contribution&sub=get_resource_display_label&type='+this.completion+'&id='+encodeURIComponent(this.valueNode.value)+'&area_id='+params.area_id;			
			request.get(url, {
				handleAs: 'text'
			}).then(lang.hitch(this, function(tpl) {
				this.domNode.value = tpl;
			}), function(err){console.log(err);});
		},
		
		formatString : function (encodedStr) {
			var parser = new DOMParser();
			// convertie les "&eacute;" en "é", etc.
			var dom = parser.parseFromString(encodedStr, 'text/html');
			// remplace les multiples espaces en 1 seul
			var str = dom.body.textContent.replace(/(\s){2,}/gm, ' ');
			return str.trim();
		},
		
		initLoader: function () {
			this.ImgloaderNode = domConstruct.create("img", {
				id: this.domNode.id + "_img_loader",
				'class': "loading_img",
				style: "display:none;",
				src: pmbDojo.images.getImage('patience.gif'),
			});
			this.loaderNode = domConstruct.create("div", {
				id: this.domNode.id + "_loader",
				'class': "field_container_loader",
			});
			
			this.loaderNode.append(this.ImgloaderNode);
			this.domNode.parentNode.insertBefore(this.loaderNode, this.domNode);
			this.loaderNode.append(this.domNode);
		},
		
		showLoader: function() {
			this.ImgloaderNode.style = "";
		},

		hiddenLoader: function() {
			this.ImgloaderNode.style = "display:none;";			
		}
	})
});