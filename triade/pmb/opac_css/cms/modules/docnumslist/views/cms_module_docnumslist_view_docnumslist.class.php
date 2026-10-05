<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_docnumslist_view_docnumslist.class.php,v 1.11 2023/08/28 14:01:11 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_docnumslist_view_docnumslist extends cms_module_common_view {
	
	protected static $nb_row=0;
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->use_dojo = true;
	}
	
	
	public static function format_datas_from_source($datas){
		$datastore = array();
		foreach($datas as $data){
			$datastore = array_merge($datastore,self::recurse_datas($data));
		}
		return encoding_normalize::utf8_normalize($datastore);
	}
	
	
	public function get_headers($datas=array()){
		
		$headers = parent::get_headers($datas);

		$headers[] = '<script>
		require(["dojo/_base/declare", "dojo/_base/window", "dojo/store/Memory", "dojo/store/JsonRest", "dijit/tree/ObjectStoreModel", "dijit/Tree", "dijit/registry", "dojo/topic", "dijit/Tooltip", "dojo/domReady!"], function(declare, win, Memory, JsonRest, ObjectStoreModel, Tree, registry, topic, Tooltip){
		var myStore = new JsonRest({
			target: "'.$datas['jsonstore'].'",
			getChildren: function (object){
 				return this.query({parent: object.id});
 			},
			getExplnums: function (id){
 				return this.query({parent: id});
 			},
		});
		
		// Create the model
	    var myModel = new ObjectStoreModel({
	        store: myStore,
	        query: {id: "root"},
			mayHaveChildren: function(object){
				return object.children;
			},
	    });
	
	    // Create the Tree.
	    var tree = new Tree({
	        model: myModel,
			showRoot: false,
			onClick : function (item,node,evt){
				if(item.explnum_id){
					var url = "./visionneuse.php?lvl=afficheur&explnum="+item.explnum_id;
  					registry.byId("'.$this->get_module_dom_id().'_visualizer").set("content","<iframe src=\'"+url+"\' style=\'border:none;height:99%;width:100%\'></iframe>");
					topic.publish("'.$this->get_module_dom_id().'_openItem", item);
				}
			},
			getIconClass : function (item, opened){
				if(!item.explnum_id){
					return (!item || this.model.mayHaveChildren(item)) ? (opened ? "dijitFolderOpened" : "dijitFolderClosed") : "dijitLeaf"
				}else{
					return "dijitLeaf"
				}
			},
			switchItem: function(item){
				this._setSelectedNodesAttr(this.getNodesByItem(item));
				this.onClick(item);
			}
	    },"'.$this->get_module_dom_id().'_tree");
	    new Tooltip({
			connectId: "'.$this->get_module_dom_id().'_tree",
			selector: "span",
			getContent: function(matchedNode){
				return dijit.getEnclosingWidget(matchedNode).item.name;
			}
		});
	});	
</script>';
		$headers[] = '<script>
	require(["dojo/_base/declare", "dojo/_base/lang", "dijit/registry", "dojo/dom", "dojo/on", "dojo/ready", "dojo/dom-style", "dojo/topic", "dojo/window"], function(declare, lang, registry, dom, on, ready, domStyle, topic, win){
		ready(function(){
			var Pager = declare(null, {
				domId: "",
				store: "",
				itemClicked: "",
				handlers : "",
				constructor: function(){
					this.handlers = new Array();
					topic.subscribe("'.$this->get_module_dom_id().'_openItem", lang.hitch(this,this.openedItem));
					this.domId = "'.$this->get_module_dom_id().'_pager";
					this.model= registry.byId("'.$this->get_module_dom_id().'_tree").model;
				},
				openedItem: function(item){
					this.itemClicked = item;
					this.model.store.getExplnums(item.parent).then(lang.hitch(this,this.render));
				},
				
				switchPagin: function(item){
					registry.byId("'.$this->get_module_dom_id().'_tree").switchItem(item);		
				},
				
				render: function(response){
					var content = "<span id=\'pager_current\'>"+this.itemClicked.name+"<\/span>";
					for(var i=0; i<this.handlers.length ; i++){
						this.handlers[i].remove();
					}	
					if (response.length > 1) {
						previous = next = content = "";		
						for(var i=0 ; i < response.length ; i++){
							if(response[i].id == this.itemClicked.id){
								if(response[i-1]){
									content+= "<span style=\'margin-right:10px;cursor:pointer;\' id=\'pager_previous\' title=\'"+response[i-1].name+"\'>'.$this->format_text($this->msg['cms_module_docnumslist_view_pager_previous']).'<\/span>";
									previous = lang.hitch(this,this.switchPagin,response[i-1]);
								}
								content+= "<span id=\'pager_current\' style=\'font-weight:bold;\'>"+this.itemClicked.name+"<\/span>";
								if(response[i+1]){
									content+= "<span style=\'margin-left:10px;cursor:pointer;\' id=\'pager_next\' title=\'"+response[i+1].name+"\'>'.$this->format_text($this->msg['cms_module_docnumslist_view_pager_next']).'<\/span>";
									next = lang.hitch(this,this.switchPagin,response[i+1]);
								}
								break;
							}
						}
					}
					registry.byId(this.domId = "'.$this->get_module_dom_id().'_pager").set("content", content);
					if(dom.byId("pager_previous")){
						this.handlers.push(on(dom.byId("pager_previous"),"click",previous));
					}
					if(dom.byId("pager_next")){
						this.handlers.push(on(dom.byId("pager_next"),"click",next));
					}
					registry.byId("'.$this->get_module_dom_id().'_container").resize();
				}
			
			});		
		    var pager = new Pager();				
			var FSSwitcher = declare(null, {
				domId:"",
				style: "",
				state: "",
				fullscreenStyle: "",
				constructor: function(domId){
					var fs = win.getBox();
					this.fullscreenStyle = {
						node: {
							position: "fixed",
							top: "0px",
							left: "0px",
							height: (fs.h)+"px",
							width: (fs.w)+"px",
							background: "rgba(0, 0, 0, 0.5)"
						},
						container: {
							top: (fs.h*0.02)+"px",
							margin: "auto",
							height: (fs.h*0.90)+"px",
							width: (fs.w*0.95)+"px"
						},
						treeContainer: {
							width: ((fs.h*0.90)*0.2)+"px"
						}
					};
					this.style = {};
					this.domId = domId;
					this.saveStyle(dom.byId(this.domId),"node");
					this.saveStyle(registry.byId("'.$this->get_module_dom_id().'_container").domNode, "container");
					this.saveStyle(registry.byId("'.$this->get_module_dom_id().'_treeContainer").domNode, "treeContainer");
					this.connect();
				},
				connect: function(){
					on(dom.byId(this.domId+"_fullscreen"),"click",lang.hitch(this,this.clicked));
				},
				clicked: function(){
				
					if(this.state == true){
						this.state = false;
						dom.byId(this.domId+"_fullscreen").innerHTML = "'.$this->msg['cms_module_docnumslist_view_fullsceen'].'";
						this.setStyle(dom.byId(this.domId),this.style.node);
						this.setStyle(registry.byId("'.$this->get_module_dom_id().'_container").domNode, this.style.container);
						this.setStyle(registry.byId("'.$this->get_module_dom_id().'_treeContainer").domNode, this.style.treeContainer);
						registry.byId("'.$this->get_module_dom_id().'_container").resize();
					}else{
						this.state = true;
						dom.byId(this.domId+"_fullscreen").innerHTML = "'.$this->msg['cms_module_docnumslist_view_reduce'].'";
						this.setStyle(dom.byId(this.domId),this.fullscreenStyle.node);
						this.setStyle(registry.byId("'.$this->get_module_dom_id().'_container").domNode, this.fullscreenStyle.container);
						this.setStyle(registry.byId("'.$this->get_module_dom_id().'_treeContainer").domNode, this.fullscreenStyle.treeContainer);
						registry.byId("'.$this->get_module_dom_id().'_container").resize();
					}
					
				},
				saveStyle : function(node,type){
					var computedStyle = domStyle.get(node);
					for (key in this.fullscreenStyle[type]){
						if(!this.style[type]){
							this.style[type] = {};
						}
						if(!this.style[type]){
							this.style[type] = {};
						}
						this.style[type][key] = computedStyle[key];
					}
				},
				setStyle: function(node,newStyle){
					try{
					domStyle.set(node, newStyle);
					}catch(e){}
				}
			});
			switcher = new FSSwitcher("'.$this->get_module_dom_id().'");
		});
	});	
</script>';
		return $headers;
	}
	
	
	public function render($datas){
		$html  = '';
		$html  = '
		<div class="itemSolo">		
			<h2>'.$this->cadre_name.'</h2>	
		</div>	
		<div id="'.$this->get_module_dom_id().'_container" data-dojo-type="dijit/layout/BorderContainer" style="background-color:white;height:500px;width:100%">
			<div data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:\'top\'" style="padding:5px;text-align:center;width:100%;">
            	<h6 style="cursor:pointer" id="'.$this->get_module_dom_id().'_fullscreen">'.$this->format_text($this->msg['cms_module_docnumslist_view_fullsceen']).'</h6>
			</div>
            <div id="'.$this->get_module_dom_id().'_treeContainer" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="splitter:true,region:\'left\'" style="width:30%;">
            	<div id="'.$this->get_module_dom_id().'_tree"></div>
			</div>
            <div data-dojo-type="dijit/layout/LayoutContainer" data-dojo-props="region: \'center\'" style="width:auto;height:100%">
            	<div id="'.$this->get_module_dom_id().'_visualizer" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region: \'center\'"></div>
                <div data-dojo-type="dijit/layout/ContentPane" id="'.$this->get_module_dom_id().'_pager" data-dojo-props="region:\'bottom\'" style="padding:5px;text-align:center;width:100%;"></div>
			</div>
		</div>';
		return $html;
	}
}
