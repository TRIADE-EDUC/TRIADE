// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ItemUI.js,v 1.42 2021/07/19 08:13:51 moble Exp $


define(["dojo/_base/declare", "dijit/layout/ContentPane", "dojo/dom-construct", "dojo/dom", "dojo/on", "dojo/topic","dojo/_base/lang","dijit/form/Button","dijit/form/RadioButton","dijit/form/ToggleButton", "apps/pmb/authForm","dijit/form/DropDownButton", "dijit/DropDownMenu", "dijit/MenuItem", "dijit/form/TextBox","dijit/registry","dojo/dom-style"], function(declare,ContentPane, domConstruct, dom, on, topic, lang, Button, RadioButton, ToggleButton,authForm, DropDownButton, DropDownMenu, MenuItem, TextBox, registry,domStyle){
	
	return declare([ContentPane], {
		itemId:0,
		postCreate:function(){
			this.own(
				topic.subscribe("sourcesListUI",lang.hitch(this,this.handleEvents)),
				topic.subscribe("itemsListUI",lang.hitch(this,this.handleEvents)),
				topic.subscribe("itemsStore",lang.hitch(this,this.handleEvents)),
				topic.subscribe("itemUI",lang.hitch(this,this.handleEvents)),
				topic.subscribe("autForm",lang.hitch(this,this.handleEvents)),
				topic.subscribe("watchStore", lang.hitch(this, this.handleEvents))
			);
		},		
		handleEvents: function(evtType,evtArgs){
			//console.log('itemUI', evtType, evtArgs);
			switch(evtType){
				case "sourcesListRefreshed":
					this.display_raz();
					break;
				case "itemsListRefreshed":
					this.itemId=evtArgs.item.id;
					this.display(evtArgs);
					break;
				case "itemSelected":
					this.itemId=evtArgs.item.id;
					this.display(evtArgs);
					break;
				case "showItemsList":
					this.show();
					break;
				case "showSourcesList":
					break;
				case "noMoreItems":
					this.display_raz();
					break;
				case "itemIndexAck":
					this.itemIndexAck(evtArgs);
					break;
				case "itemUIChange":
					this.itemIndexChange(evtArgs);
					break;
				case "autFormChange":
					this.itemIndexChange(evtArgs);
					break;
				case "watchDeleted":
					this.display_raz();
					break;
				case "redrawItem":
					this.display(evtArgs);
					break;
			}			
		},		
		buildRendering: function(){
			this.inherited(arguments);
		},
		destroy: function(){
			this.inherited(arguments);
		},
		setAvailableDatatags: function(tags) {
			for(var i = 0; i < tags.length; i++) {
				if(typeof availableDatatags.get(tags[i].id) == 'undefined') {
					var nb_item_tags=dojo.byId("max_tags").value;
					for (var j=0; j<nb_item_tags; j++){
						var dropDownMenu=registry.byId("DropDownMenu"+j);
						dropDownMenu.addChild(new MenuItem({
							label : tags[i].label,
							onClick : lang.hitch(this,this.add_tag,0,tags[i].label)
						}));
					}
				}
				var response = availableDatatags.put(tags[i],{
					overwrite:true
				});
			}
		},
		itemIndexAck: function(response){
			if(response.tags.length) {
				this.setAvailableDatatags(response.tags);
			}
			if(dojo.byId('descriptors_isbd')) {
				domConstruct.place('<label>'+response.descriptors_isbd+'</label>', dojo.byId('descriptors_isbd'), 'only');
			}
			if(dojo.byId('tags_isbd')) {
				domConstruct.place('<label>'+response.tags_isbd+'</label>', dojo.byId('tags_isbd'), 'only');
			}
			if(dojo.byId('concept_isbd')) {
				domConstruct.place('<label>'+response.concepts_isbd+'</label>', dojo.byId('concept_isbd'), 'only');
			}
			if(dijit.byId("button_index_id")) {
				dojo.style("button_index_id", "opacity",0.5);	
				dijit.byId("button_index_id").set('disabled', true);
			}
		},
		display: function(data){
			this.display_raz();
			var html="";
			if(data.item.status!=2){
				html+="<div id='itemUI_action' class='itemUI_action'>";
				html+="	<div id='button_mark_as_interesting'></div>";
				html+="	<div id='button_mark_as_read'></div>";
				html+="	<div id='button_notice'></div>";
				html+="	<div id='button_section'></div>";
				html+="	<div id='button_article'></div>";
				html+="	<div id='button_delete'></div>";
			}else{
				html+="	<div id='button_restore'></div>";
			}
					
			html+="</div>";
			html+="<div id='itemUI_content' class='itemUI_content'>";
			html+="<div class='row'>";
			html+="<b>"+this.getMsg("dsi_js_item_title")+"</b> : "+data.item.title+"</br>";
			if(data.item.summary)
				html+="<b>"+this.getMsg("dsi_js_item_summary")+"</b> : "+data.item.summary+"</br>";
			if(data.item.publication_date)
				html+="<b>"+this.getMsg("dsi_js_item_publication_date")+"</b> : "+data.item.publication_date+"</br>";
			if(data.item.added_date)
				html+="<b>"+this.getMsg("dsi_js_item_added_date")+"</b> : "+data.item.added_date+"</br>";
			if(data.item.content)
				html+="<b>"+this.getMsg("dsi_js_item_content")+"</b> : "+data.item.content+"</br>";
			if(data.item.url){
				if(data.item.logo_url)
					html+="<b>"+this.getMsg("dsi_js_item_link")+"</b> : <a href='"+data.item.url+"' target='_blank'><img src='"+data.item.logo_url+"' alt='"+data.item.url+"' width='auto' height='20'/></a></br>";
				else
					html+="<b>"+this.getMsg("dsi_js_item_link")+"</b> : <a href='"+data.item.url+"' target='_blank'>"+data.item.url+"</a></br>";
			}
			if (!(data.item.status == 2 && data.item.tags.length == 0)) {
				html+="<b>"+this.getMsg("dsi_js_item_tags")+"</b> :  <span id='tags_isbd'></span></br><input id='max_tags' type='hidden' value='0' name='max_tags'>";	
				html+="	<div class='row' id='buttons_tags'></div></br>";
			}
			if (!(data.item.status == 2 && data.item.descriptors.length == 0)) {
				html+="<b>"+this.getMsg("dsi_docwatch_item_categ")+"</b> : <span id='descriptors_isbd'></span></br>";
				html+="	<div class='row' id='categ'></div></br>";
			}
			if(data.item.concepts) {
				if (!(data.item.status == 2 && data.item.concepts.length == 0)) {
					html+="<b>"+this.getMsg("dsi_docwatch_item_concept")+"</b> : <span id='concept_isbd'></span></br>";
					html+="	<div class='row' id='concept'></div></br>";
				}
			}
			html+="<div class='row'>&nbsp;</div>";
			html+="<div class='row'>";
			html+="	<div id='button_index'></div>";		
			html+="</div>";
			html+="</div>";
			html+="</div>";

			this.own(domConstruct.place(html,this.domNode));

			if(data.item.status != 2){
				var index=0;
				if(Array.isArray(data.item.tags)){
					for(var i=0; i<data.item.tags.length; i++){
						this.add_new_tag_selector(data.item.tags[i].label,i);
					}
					index=data.item.tags.length;
				}
				this.add_new_tag_selector("",index);
				this.own( new ToggleButton({
						checked: (data.item.interesting == 1 ? true : false),
						iconClass: "dijitCheckBoxIcon",
						label: this.getMsg("dsi_js_item_action_mark_as_interesting")
					}, "button_mark_as_interesting").on('change', lang.hitch(this,this.markAsInteresting))
				);
				this.own( new ToggleButton({
						checked: (data.item.status == 1 ? true : false),
						iconClass: "dijitCheckBoxIcon",
						label: this.getMsg("dsi_js_item_action_mark_as_read")
					}, "button_mark_as_read").on('change', lang.hitch(this,this.markAsRead))
				);
				this.own( new Button({
						label: this.getMsg("dsi_js_item_action_index"),
						id:"button_index_id"
					}, "button_index").on('click', lang.hitch(this,this.itemIndex))
				);	
				dojo.style("button_index_id", "opacity", 0.5);
				dijit.byId("button_index_id").set('disabled', true);
				
				if(data.item.num_notice == 0) {
					this.own( new Button({
							label: this.getMsg("dsi_js_item_action_create_notice"),
						}, "button_notice").on('click', lang.hitch(this,this.createNotice))
					);
				} else {
					this.own( new Button({
							label: this.getMsg("dsi_js_item_action_see_notice"),
						}, "button_notice").on('click', lang.hitch(this,this.see, data.item.record_link))
					);				
				}
				if(data.item.num_section == 0) {
					this.own( new Button({
							label: this.getMsg("dsi_js_item_action_create_section"),
						}, "button_section").on('click', lang.hitch(this,this.createSection))
					);
				} else {
					this.own( new Button({
							label: this.getMsg("dsi_js_item_action_see_section"),
						}, "button_section").on('click', lang.hitch(this,this.see, data.item.section_link))
					);				
				}			
				if(data.item.num_article == 0) {
					this.own( new Button({
							label: this.getMsg("dsi_js_item_action_create_article"),
						}, "button_article").on('click', lang.hitch(this,this.createArticle))
					);
				} else {
					this.own( new Button({
							label: this.getMsg("dsi_js_item_action_see_article"),
						}, "button_article").on('click', lang.hitch(this,this.see, data.item.article_link))
					);				
				}		
				if(!data.item.descriptors)data.item.descriptors= new Array();
				this.own(this.categForm= new authForm({
						id: "categ",
						what_sel: "select_categ",
						add_function: "add_categ",
						completion: "categories_mul",
						selectUrl: "./select.php?what=categorie&dyn=1",
						inputIdUrl: "p1",
						inputNameUrl: "p2",
						data: data.item.descriptors,
						callback:"callback_categ",
					},'categ')
				);
				
				if(!data.item.concepts)data.item.concepts = new Array();
				if (!this.conceptForm) {
					this.own(this.conceptForm= new authForm({
							id: "concept",
							what_sel: "select_categ",
							add_function: "add_categ",
							completion: "onto",
							selectUrl: "./select.php?what=ontology&dyn=1",
							inputIdUrl: "p1",
							inputNameUrl: "p2",
							data: data.item.concepts,
							callback:"callback_categ",
						},'concept')
					);
				} else {
					this.conceptForm.data = data.item.concepts;
				}
				
				if (data.item.status == 0) {
					setTimeout(lang.hitch(this,function(){
						var childs = this.getChildren();
						for(var i=0 ; i<childs.length ; i++){
							if(childs[i].id == "button_mark_as_read"){
								//Le set checked appel directement le callback -> markAsRead
								childs[i].setChecked(true);
							}
						}
					}),2000);
				} 			
				this.own( new Button({
						label: this.getMsg("dsi_js_item_action_delete"),
					}, "button_delete").on('click', lang.hitch(this,this.itemDelete))
				);
			}else{
				this.own( new ToggleButton({
						iconClass: "dijitCheckBoxIcon",
						label: this.getMsg("dsi_js_item_action_restore")
					}, "button_restore").on('change', lang.hitch(this,this.restore))
				);
			}
			this.itemIndexAck(data.item);
		},				
		itemIndexChange: function(info){
			dojo.style("button_index_id", "opacity",1);
			dijit.byId("button_index_id").set('disabled', false);			
		},
		add_new_tag_selector: function(label,index){			
			var menu = new DropDownMenu({id: "DropDownMenu"+index, style: "display: none;"});

			var tmp = new MenuItem({id: "MenuItem"+index});
			menu.addChild(tmp);
			
			 new TextBox({
				id:"new_input_tag"+index,
				name:"new_input_tag"+index,
				value:"",
				style:"width:20em",
				onClick:function(e){
					e.stopPropagation();
				}
			}).placeAt(tmp);
			
			new Button({
				label: this.getMsg("dsi_js_item_action_create_tag"),
				onClick : lang.hitch(this,this.add_new_tag,index)
			}).placeAt(tmp);
			
			if (label != "") {
				new Button({
					iconClass: 'dijitIconDelete',
					showLabel: false,
					label: this.getMsg("dsi_js_item_action_remove_tag"),
					onClick : lang.hitch(this,this.remove_tag,index)
				}).placeAt(tmp);
			}
			
			for(var i=0 ; i<availableDatatags.data.length ; i++){
				menu.addChild(new MenuItem({
					label : availableDatatags.data[i].label,
					onClick : lang.hitch(this,this.add_tag,index,availableDatatags.data[i].label)
				}));
			}	
			menu.startup();		
			var button=  new DropDownButton({
				label: label,
				id: "button_tag_id_"+index,
			    dropDown: menu,
			    style: { width: "auto"}
			},domConstruct.create("button_tag_"+index,{},"buttons_tags","last"));
			
			button.startup();
			this.own(button);		
			this.own(menu);		
			dojo.byId("max_tags").value=index+1;
		},	
		add_new_tag: function(index){
			if (dom.byId("new_input_tag"+index).value != "") {
				if(!registry.byId("button_tag_id_"+ (index+1)))
						this.add_new_tag_selector("",(index+1));	
				registry.byId("button_tag_id_"+index).set('label',dom.byId("new_input_tag"+index).value);
				topic.publish('itemUI',"itemUIChange",{action:"add_new_tag"});
				var button = new Button({
					iconClass: 'dijitIconDelete',
					showLabel: false,
					label: this.getMsg("dsi_js_item_action_remove_tag"),
					onClick : lang.hitch(this,this.remove_tag,index)
				});
				button.placeAt("MenuItem"+index);
				button.startup();
			}
		},			
		add_tag: function(index,label){	
			topic.publish('itemUI',"itemUIChange",{action:"add_tag"});
			if(!registry.byId("button_tag_id_"+ (index+1)))
				this.add_new_tag_selector("",(index+1));	
			registry.byId("button_tag_id_"+index).set('label',label);
			var button = new Button({
				iconClass: 'dijitIconDelete',
				showLabel: false,
				label: this.getMsg("dsi_js_item_action_remove_tag"),
				onClick : lang.hitch(this,this.remove_tag,index)
			});
			button.placeAt("MenuItem"+index);
			button.startup();
		},	
		remove_tag: function(index){
			var data=this.categForm.get_data();
			var data_tags=this.get_tags();
			data_tags.splice(index,1);
			topic.publish('itemUI',"itemReIndex",{itemId:this.itemId,data:{descriptors:data,tags:data_tags}});
		},
		display_raz: function(){		
			this.destroyDescendants();
		},
		getMsg: function(key){			 
			return pmbDojo.messages.getMessage("dsi",key);
		},
		markAsRead: function(evt){
			if(evt== true) topic.publish('itemUI',"itemMarkAsRead",{itemId:this.itemId});
			else topic.publish('itemUI',"itemMarkAsUnread",{itemId:this.itemId});
		},
		markAsInteresting: function(evt){
			if(evt== true) topic.publish('itemUI',"itemMarkAsInteresting",{itemId:this.itemId});
			else topic.publish('itemUI',"itemMarkAsUninteresting",{itemId:this.itemId});
		},
		itemIndex: function(evt){
			var data = this.categForm.get_data();
			var data_tags = this.get_tags();
			var data_concept = this.conceptForm.get_data();
			topic.publish('itemUI',"itemIndex",{itemId:this.itemId,data:{descriptors:data,tags:data_tags, concepts_list:data_concept, item_id:this.itemId}});
		},
		get_tags: function(evt){
			var data= new Array();
			var nb=dojo.byId("max_tags").value;
			index=0;
			for (var i=0;i<nb;i++){
				var label=registry.byId("button_tag_id_"+i).get('label');
				if(label){					
					data[index]=label;
					index++;
				}
			}
			return data;	
		},
		itemDelete: function(evt){
			topic.publish('itemUI',"itemDelete",{itemId:this.itemId});
		},		
		see: function(link){
			window.open(link, '_blank');
		},
		createNotice: function(evt){
			if(confirm(this.getMsg("dsi_js_item_action_create_record_confirm"))){
				topic.publish('itemUI',"itemCreateNotice",{itemId:this.itemId});	
			}
		},			
		createSection: function(evt){
			if(confirm(this.getMsg("dsi_js_item_action_create_section_confirm"))){
				topic.publish('itemUI',"itemCreateSection",{itemId:this.itemId});
			}
		},	
		createArticle: function(evt){
			if(confirm(this.getMsg("dsi_js_item_action_create_article_confirm"))){
				topic.publish('itemUI',"itemCreateArticle",{itemId:this.itemId});
			}
		},
		show:function(){
			if(this.domNode.style.display == "none"){
				this.domNode.style.display = "block";
			}
		},
		hide:function(){
			if(this.domNode.style.display != "none"){
				this.domNode.style.display = "none";
			}
		},
		restore: function(evt){
			topic.publish('itemUI',"itemRestore",{itemId:this.itemId});
		},
		
	});
});