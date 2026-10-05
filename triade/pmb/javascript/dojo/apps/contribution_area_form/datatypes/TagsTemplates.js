// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: TagsTemplates.js,v 1.2 2021/08/19 12:08:13 qvarin Exp $


define([
	"dojo/_base/declare",
	"dojo/query",
	"dojo/dom",
	"dijit/form/DropDownButton",
	"dijit/DropDownMenu",
	"dijit/form/Button",
	"dojo/dom-construct",
	"dojo/_base/lang",
	"dijit/registry",
	"dijit/MenuItem",
	"dijit/form/TextBox",
	"dojo/on",
    "dojo/dom-attr", 
], function(declare, query, dom, DropDownButton, DropDownMenu, Button, domConstruct, lang, registry, MenuItem, TextBox, on, domAttr) {
	return declare([], {
		
		prefixId: "",
		
		fields: [],
		
		selectedOptions: {},
		
		datalist: {},
		
		options: {},

		constructor: function(prefixId) {
			
			// On évite les conflis entre les instances
			this.fields = new Array();
			this.selectedOptions = {};
			this.datalist = {};
			this.options = {};
			
			this.prefixId = prefixId;
			this.foundFields();
			this.init();
		},

		foundFields: function () {
			var inputNewOrder = dom.byId(this.prefixId + "_new_order");
			var countNewOrder = inputNewOrder.value ?? 0;
			countNewOrder++;
			for (var i = 0; i < countNewOrder; i++) {
				var field = dom.byId(this.prefixId + "_" + i + "_value");
				if (field) {					
					this.fields.push(field)
				}
			}
		},

		init: function() {
			for (var fieldIndex = 0; fieldIndex < this.fields.length; fieldIndex++) {
				
				// Init datalist
				this.datalist[fieldIndex] = null;
				var menu = new DropDownMenu({
					id: this.prefixId+"_DropDownMenu" + fieldIndex,
					"class": "contribution_drop_down_menu",
					style: "display: none;",
				});
				on(menu, 'blur', lang.hitch(this, this.hiddenDataList, fieldIndex))
				menu.startup();

				var menuItem = new MenuItem({
					id: this.prefixId+"_menuItem" + fieldIndex,
					"class": "contribution_drop_down_menu_item"
				});
				menu.addChild(menuItem);

				var containerTextBox = new TextBox({
					name: this.prefixId+"_new_input_tag" + fieldIndex,
					list: this.prefixId+"_datalist_value" + fieldIndex,
					id: this.prefixId+"_new_input_tag" + fieldIndex,
					value: "",
					type: "text",
					style: "width:20em",
					autocomplete: "off",
					onClick: function(e) {
						e.stopPropagation();
					}
				}).placeAt(menuItem);
				on(containerTextBox, 'keyup', lang.hitch(this, this.keyPressInput, fieldIndex))
				on(containerTextBox, 'focus', lang.hitch(this, this.showDataList, fieldIndex))

				var dataList = domConstruct.create("div", {
					id: this.prefixId+"_datalist_value" + fieldIndex,
					"class": "contributon_datalist",
					"data-input_id": this.prefixId+"_new_input_tag" + fieldIndex,
					style: "width:20em;display:none;",
				}, null);
				this.datalist[fieldIndex] = dataList;
				domConstruct.place(dataList, containerTextBox.domNode);

				new Button({
					label: pmbDojo.messages.getMessage('contribution', 'add_tags'),
					onClick: lang.hitch(this, this.add_new_tag, fieldIndex, "", "")
				}).placeAt(menuItem);
				
				var button = new DropDownButton({
					label: "",
					id: this.prefixId+"_button_tag_id_" + fieldIndex,
					dropDown: menu,
					style: { width: "auto" }
				}, domConstruct.create(this.prefixId+"_button_tag_" + fieldIndex, {}, this.prefixId + "_" + fieldIndex, "first"));
				on(button, 'click', lang.hitch(this, this.prepareInput, fieldIndex))
				on(button, 'blur', lang.hitch(this, this.hiddenDataList, fieldIndex))
				
				// Hidden Select and remove class "contribution_area_flex"
				var container = dom.byId(this.prefixId + "_" + fieldIndex);
				container.className = container.className.replace(/\s?contribution_area_flex/, '');
				query("select", container).forEach(lang.hitch(this, function(node) {
					domAttr.set(node, "style", "display:none;");			
				}));
				
				button.startup();
			}
			this.initDefaultValue();
		},

		add_new_tag: function(fieldIndex, optionValue, optionLabel) {
			var label = "";
			var index = false;
			
			if (!this.selectedOptions[fieldIndex]) {
				this.selectedOptions[fieldIndex] = [];
			}

			if (!optionValue) {				
				var input = dom.byId(this.prefixId+"_new_input_tag" + fieldIndex);
				if (input) {
					label = input.value;
					input.value = "";
				}
				
				var countOptions = this.fields[fieldIndex].options.length;
				for (var j = 0; j < countOptions; j++) {
					var option = this.fields[fieldIndex].options[j];
					if (this.formatString(option.innerText) == this.formatString(label)) {
						index = option.value;
						label = option.innerText;
						this.selectedOptions[fieldIndex].push(index);
						break;
					}
				}
			} else {
				index = optionValue;
				label = optionLabel;
				this.selectedOptions[fieldIndex].push(optionValue);
			}
			
			if (label === "" || index === false) {
				return;
			}
			
			this.updateSelectedlist(fieldIndex);
			this.updateDatalist(fieldIndex);

			var menu = new DropDownMenu({
				id: this.prefixId+"_DropDownMenu_value_" + fieldIndex + "_" + index,
				style: "display: none;"
			});

			var tmp = new MenuItem({
				id: this.prefixId+"_MenuItem_value_" + fieldIndex + "_" + index,
			});
			menu.addChild(tmp);
			
			new Button({
				iconClass: 'dijitIconDelete',
				showLabel: false,
				label: "X",
				onClick: lang.hitch(this, this.remove_tag, fieldIndex, index)
			}).placeAt(tmp);

			menu.startup();
			var button = new DropDownButton({
				label: label,
				id: this.prefixId+"_button_tag_id_value_" + fieldIndex + "_" + index,
				dropDown: menu,
				style: { width: "auto" }
			}, domConstruct.create("button_tag_" + fieldIndex, {}, this.prefixId + "_" + fieldIndex, "first"));

			button.startup();
		},

		remove_tag: function(fieldIndex, index) {
			var pos = this.selectedOptions[fieldIndex].indexOf(index);
			if (pos !== -1) {
				this.selectedOptions[fieldIndex].splice(pos,1);
				this.updateSelectedlist(fieldIndex);
				this.updateDatalist(fieldIndex);
			}
			var widget = registry.byId(this.prefixId+"_button_tag_id_value_" + fieldIndex + "_" + index)
			widget.destroyRecursive(false);
		},
		
		updateDatalist: function(fieldIndex, filter) {
					
			domConstruct.empty(dom.byId(this.prefixId+"_datalist_value" + fieldIndex));
			this.options[fieldIndex] = new Array();
			
			var options = this.fields[fieldIndex].options;
			var countOptions = this.fields[fieldIndex].options.length;
			for (var j = 0; j < countOptions; j++) {
				if (!this.selectedOptions[fieldIndex].includes(options[j].value)) {
					
					var labelOption = this.slugify(options[j].innerText);
					var filter = this.slugify(filter);
					if ( filter && !labelOption.includes(filter) && filter != "*")  {
						continue;
					}
					
					var classname = "contributon_datalist_option";
					if (this.datalist[fieldIndex].optionHover == this.options[fieldIndex].length) {
						classname = "contributon_datalist_option_surbrillance";						
					}
					
					var option = domConstruct.create('div', {
						id: this.prefixId+"_datalist_"+fieldIndex+"_option_" + j,
						"class": classname,
						"data-field_index": fieldIndex,
						innerHTML: options[j].innerHTML,
						title: options[j].innerText,
						onmouseover: lang.hitch(this, this.hoverOption, fieldIndex, this.options[fieldIndex].length),
						onmouseout: lang.hitch(this, this.hoverOption, fieldIndex, -1),
						onclick: lang.hitch(this, this.selectedOptionDatalist)
					}, dom.byId(this.prefixId+"_datalist_value" + fieldIndex));
					
					this.options[fieldIndex].push(option);
				}
			}
		},
		
		updateSelectedlist: function(fieldIndex) {
			var countOptions = this.fields[fieldIndex].options.length;
			for (var j = 0; j < countOptions; j++) {
				var option = this.fields[fieldIndex].options[j];
				if (this.selectedOptions[fieldIndex].indexOf(option.value) !== -1) {
					option.selected = true;
				} else {
					option.selected = false;
				}
			}
		},
		
		initDefaultValue: function() {
			if (this.fields) {			
				for (var i = 0; i <= this.fields.length; i++) {
					if (this.fields[i]) {						
						var countOptions = this.fields[i].options.length;
						for (var j = 0; j < countOptions; j++) {
							var option = this.fields[i].options[j];
							if (option.selected == true || option.attributes['selected']) {
								this.add_new_tag(i, option.value, option.innerText);
							}
						}
					}
				}
			}
		},
		
		keyPressInput: function(fieldIndex, event) {
			switch (event.code) {
				case "ArrowDown":
					if (!this.datalist[fieldIndex].isDisplay) {					
						this.showDataList(fieldIndex);
					} else {
						this.selectOptionDown(fieldIndex);
					}
					break;
				case "ArrowUp":
					if (this.datalist[fieldIndex].isDisplay) {					
						this.selectOptionUp(fieldIndex);
					}
					break;
				case "Enter":
				case "NumpadEnter":
					if (this.datalist[fieldIndex].isDisplay) {
						var optionIndex = this.datalist[fieldIndex].optionHover;
						if (!isNaN(optionIndex) && this.options[fieldIndex][optionIndex]) {
							this.options[fieldIndex][optionIndex].click();
						}				
					}
					break;
				default:
					this.filterOptionDatalist(fieldIndex);
					break;
			}
		},
		
		showDataList: function(fieldIndex) {
			if (this.datalist[fieldIndex]) {				
				this.datalist[fieldIndex].style.display = "block";
				this.datalist[fieldIndex].isDisplay = true;
			}
		},
		
		hiddenDataList: function(fieldIndex) {
			if (this.datalist[fieldIndex]) {				
				this.datalist[fieldIndex].style.display = "none";
				this.datalist[fieldIndex].isDisplay = false;
			}
		},
		
		selectedOptionDatalist: function(e) {
			var option = e.target;
			var attribute = null;
			
			attribute = option.attributes['data-field_index'];
			if (attribute) {
				var fieldIndex = attribute.value;
				this.hiddenDataList(fieldIndex);
				var datalist = this.datalist[fieldIndex];
				if (datalist) {
					attribute = datalist.attributes['data-input_id'];
					if (attribute) {										
						var input = document.getElementById(attribute.value);
						input.value = option.innerText;
					}
				}
			}
		},
		
		filterOptionDatalist: function (fieldIndex) {
			var search = "*";
			var input = dom.byId(this.prefixId+"_new_input_tag" + fieldIndex);
			if (input) {
				search = this.formatString(input.value);
			}
			if (search.length == 0) {
				search = "*";				
			}
			this.updateDatalist(fieldIndex, search);
		},
		
		selectOptionDown: function(fieldIndex) {
			if (typeof this.datalist[fieldIndex].optionHover == "undefined") {
				this.datalist[fieldIndex].optionHover = 0;
			} else {
				this.datalist[fieldIndex].optionHover++;
			}
			
			var countOptions = this.options[fieldIndex].length;
			if (this.datalist[fieldIndex].optionHover >= countOptions) {
				this.datalist[fieldIndex].optionHover = 0;						
			}
			
			var optionIndex = this.datalist[fieldIndex].optionHover;
			this.hoverOption(fieldIndex, optionIndex);
		},
		
		selectOptionUp: function(fieldIndex) {
			if (typeof this.datalist[fieldIndex].optionHover == "undefined") {
				this.datalist[fieldIndex].optionHover = 0;
			} else {
				this.datalist[fieldIndex].optionHover--;
			}
			
			var countOptions = this.options[fieldIndex].length;
			if (this.datalist[fieldIndex].optionHover < 0) {
				this.datalist[fieldIndex].optionHover = countOptions-1;						
			}
			
			var optionIndex = this.datalist[fieldIndex].optionHover;
			this.hoverOption(fieldIndex, optionIndex);
		},
		
		hoverOption: function (fieldIndex, optionIndex) {
			var countOptions = this.options[fieldIndex].length;
			for (var i=0; i < countOptions; i++) {
				if (i == optionIndex) {
					if (!this.datalist[fieldIndex].optionHover || this.datalist[fieldIndex].optionHover != optionIndex) {
						this.datalist[fieldIndex].optionHover = optionIndex;
					}
					this.options[fieldIndex][i].className="contributon_datalist_option_surbrillance";							
				} else {
					this.options[fieldIndex][i].className="contributon_datalist_option";														
				}
			}
		},
		
		prepareInput: function (fieldIndex) {
			var input = dom.byId(this.prefixId+"_new_input_tag" + fieldIndex);
			if (input) {
				input.value = "*";
				this.showDataList(fieldIndex);
				input.focus();
			}
		},
		
		formatString: function(encodedStr) {
			var parser = new DOMParser();
			// convertie les "&eacute;" en "é", etc.
			var dom = parser.parseFromString(encodedStr, 'text/html');
			// remplace les multiples espaces en 1 seul
			var str = dom.body.textContent.replace(/(\s){2,}/gm, ' ');
			return str.trim();
		},
		
        slugify : function(str) {
			if (str) {	
				var map = {
					'' : ' |-|_|\'|"|\\.|:|;',
					'a' : 'á|à|ã|â',
					'e' : 'é|è|ê',
					'i' : 'í|ì|î',
					'o' : 'ó|ò|ô|õ',
					'u' : 'ú|ù|û|ü',
					'c' : 'ç',
					'n' : 'ñ',
				};
				
				str = str.toLowerCase();
				for (var pattern in map) {
					str = str.replace(new RegExp(map[pattern], 'g'), pattern);
				};
			}
			return str;
		},
	});
});