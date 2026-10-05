<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_menu.class.php,v 1.23.2.1.2.1 2025/02/12 12:34:09 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_menu extends cms_module_common_module {

	public function __construct($id=0){
		$this->module_path = str_replace(basename(__FILE__),"",__FILE__);
		parent::__construct($id);
	}

	public function get_manage_form(){
		global $base_path;
		//variables persos...
		global $menu;

		$form="
		<div dojoType='dijit.layout.BorderContainer' style='width: 100%; height: 800px;'>
			<div dojoType='dijit.layout.ContentPane' region='left' splitter='true' style='width:300px;' >";
		if($this->managed_datas['module']['menus']){
			foreach($this->managed_datas['module']['menus'] as $key => $menu_infos){
				$form.="
					<p>
						<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->class_name)."&quoi=module&menu=".$key."&action=get_form'>".$this->format_text($menu_infos['name'])."</a>
					&nbsp;
						<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->class_name)."&quoi=module&menu_delete=".$key."&action=save_form' onclick='return confirm(\"".$this->format_text($this->msg['cms_module_menu_delete_menu'])."\")'>
							<img src='".get_url_icon('trash.png')."' alt='".$this->format_text($this->msg['cms_module_root_delete'])."' title='".$this->format_text($this->msg['cms_module_root_delete'])."'/>
						</a>
					</p>";
			}
		}
			$form.="
				<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->class_name)."&quoi=module&menu=new'>".$this->format_text($this->msg['cms_module_agenda_add_calendar'])."</a>
			";
		$form.="
			</div>
			<div dojoType='dijit.layout.ContentPane' region='center' >";
		if($menu){
			$form.=$this->get_managed_form_start(array('menu'=>$menu));
			$form.=$this->get_managed_menu_form($menu);
			$form.=$this->get_managed_form_end();
		}
		$form.="
			</div>
		</div>";
		return $form;
	}

	public function save_manage_form(){
		global $menu;
		global $menu_delete;
		global $cms_module_menu_menu_name;

		$params = $this->managed_datas['module'];

		if($menu_delete){
			unset($params['menus'][$menu_delete]);
		}else{
			//ajout d'un menu
			if($menu == "new"){
				$menu_infos = array(
					'name' => $cms_module_menu_menu_name
				);
				$params['menus']['menu'.(self::get_max_menu_id($this->managed_datas['module']['menus'])+1)] = $menu_infos;
			}else{
				//sinon on réécrit juste l'élément
				$params['menus'][$menu]['name'] = $cms_module_menu_menu_name;
			}
		}
		return $params;
	}

	protected function get_max_menu_id($datas) {
		$max = 0;
		if (is_countable($datas) && count($datas)) {
			foreach	($datas as $key => $val){
			    $key = intval(str_replace("menu","",$key));
				if($key>$max) $max = $key;
			}
		}
		return $max;
	}

	protected function get_managed_menu_form($menu){
		global $opac_url_base;
		global $base_path;

		$infos = array();
		if($menu != "new"){
			$infos = $this->managed_datas['module']['menus'][$menu];
		} else {
			$infos = array(
				'name' => ""
			);
		}
		$form="
			<div class='row'>
				<div class='colonne3'>
				</div>
				<div class='colonne-suite'>
				</div>
			</div>";
		//nom du menu
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_menu_menu_name'>".$this->format_text($this->msg['cms_module_menu_menu_name'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_menu_menu_name' value='".$this->format_text($infos['name'])."'/>
				</div>
			</div>";
		if($menu!="new"){
		//sélecteur d'entrée
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_menu_menu_add_entry'>".$this->format_text($this->msg['cms_module_menu_menu_add_entry'])."</label>
				</div>
				<div class='colonne-suite'>
					<select name='cms_module_menu_menu_add_entry' id='cms_module_menu_menu_add_entry' onchange='load_entry_form(this.value)'>
						<option value=''>".$this->format_text($this->msg['cms_module_menu_menu_add_entry_choice'])."</option>
						<option value='url'>".$this->format_text($this->msg['cms_module_menu_menu_add_entry_url'])."</option>
						<option value='infopage'>".$this->format_text($this->msg['cms_module_menu_menu_add_entry_infopage'])."</option>
						<option value='page'>".$this->format_text($this->msg['cms_module_menu_menu_add_entry_page'])."</option>
					</select>
					<script type='text/javascript'>
						var last = ".$this->get_next_item_id($menu).";
						var elements_infos= new Object();
						var tree_infos= new Object();
						var handle_events = new Object();
						function load_entry_form(type,item){
							var content = dojo.byId('cms_module_menu_entry_form');
							dijit.byId('cms_module_menu_entry_form').destroyDescendants(false);
							content.appendChild(cms_create_form_element('hidden','cms_module_menu_entry_type',type));
							switch (type){
								case 'url' :
									var row = cms_create_element('".$this->format_text($this->msg['cms_module_menu_menu_entry_url_label'])."','text','cms_module_menu_menu_entry_url_label','');
									content.appendChild(row);
									var row = cms_create_element('".$this->format_text($this->msg['cms_module_menu_menu_entry_url_link'])."','text','cms_module_menu_menu_entry_url_link','');
									content.appendChild(row);
									if(item){
										dojo.byId('cms_module_menu_menu_entry_url_label').value=item.title[0];
										dojo.byId('cms_module_menu_menu_entry_url_link').value=item.link[0];
										content.appendChild(cms_create_button('cms_update_item','".$this->format_text($this->msg['cms_module_menu_menu_entry_button_modify'])."','replace_entry'));
										dojo.byId('cms_update_item').onclick = function() {
											dijit.byId(\"cms_module_menu_entries\").model.store.setValues(item,'title',dojo.byId('cms_module_menu_menu_entry_url_label').value);
											dijit.byId(\"cms_module_menu_entries\").model.store.setValues(item,'link',dojo.byId('cms_module_menu_menu_entry_url_link').value);
											dijit.byId(\"cms_module_menu_entries\").model.store.save();
											dijit.byId('cms_module_menu_entry_form').destroyDescendants(false);
										}
									}
									break;
								case 'infopage' :
									dojo.xhrGet({
										url : '".$this->get_ajax_link(array('do'=> "get_infopages"))."',
										handleAs : 'json',
										load : function(data){
											if(item){
												var row = cms_create_element('".$this->format_text($this->msg['cms_module_menu_menu_entry_infopage'])."','select','cms_module_menu_menu_entry_infopage',item.link[0].replace('".$opac_url_base."index.php?lvl=infopages&pagesid=',''),data);
												}else{
												var row = cms_create_element('".$this->format_text($this->msg['cms_module_menu_menu_entry_infopage'])."','select','cms_module_menu_menu_entry_infopage','',data);
											}
											content.insertBefore(row,content.firstChild);
										}
									});
									break;
								case 'page' :
									dojo.xhrGet({
										url : '".$this->get_ajax_link(array('do'=> "get_pages"))."',
										handleAs : 'json',
										load : function(data){
											if(item){
												var row1 = cms_create_element('".$this->format_text($this->msg['cms_module_menu_menu_entry_url_label'])."','text','cms_module_menu_menu_entry_label',item.title[0]);
												var url_params= item.link[0].replace('./index.php?lvl=cmspage&pageid=','');
												if(url_params.indexOf('&')>0){
													var page_id = url_params.substr(0,url_params.indexOf('&'));
												}else{
													var page_id = url_params;
												}
												var page_params = url_params.replace(page_id,'');
												var row2 = cms_create_element('".$this->format_text($this->msg['cms_module_menu_menu_entry_page'])."','select','cms_module_menu_menu_entry_page',page_id,data);
												if(page_id){
													dojo.xhrGet({
														handleAs : 'json',
														url : '".$this->get_ajax_link(array('do'=>'get_page_vars'))."&page='+page_id,
														load : function(data){
															var content = dojo.byId('cms_vars');
															content.innerHTML = '';
															if(data.length > 0){
																dojo.forEach(data,function(page_var){
																	page_params = page_params.replace('&'+page_var.name+'=','');
																	if(page_params.indexOf('&')> 0){
																		var param_value = page_params.substr(0,page_params.indexOf('&'));
																	}else var param_value = page_params;
																	page_params = page_params.replace(param_value,'');
																	var row = cms_create_element(page_var.name+' - '+page_var.comment,'text','cms_module_menu_menu_entry_page_vars[]',param_value);
																	content.appendChild(row);
																	var row = cms_create_element('','hidden','cms_module_menu_menu_entry_page_vars_name[]',page_var.name);
																	content.appendChild(row);
																});

															}
														}
													});
												}
											}else{
												var row1 = cms_create_element('".$this->format_text($this->msg['cms_module_menu_menu_entry_url_label'])."','text','cms_module_menu_menu_entry_label','');
												var row2 = cms_create_element('".$this->format_text($this->msg['cms_module_menu_menu_entry_page'])."','select','cms_module_menu_menu_entry_page','',data);
											}
											var div = document.createElement('div');
											div.setAttribute('id','cms_vars');
											content.insertBefore(div,content.firstChild);
											content.insertBefore(row2,content.firstChild);
											content.insertBefore(row1,content.firstChild);
											if(handle_events.page){
												dojo.disconnect(handle_events.page);
											}
											handle_events.page = dojo.connect(dojo.byId('cms_module_menu_menu_entry_page'),'onchange',function(){
												if(dojo.byId('cms_module_menu_menu_entry_page').value != 0)
												dojo.xhrGet({
													handleAs : 'json',
													url : '".$this->get_ajax_link(array('do'=>'get_page_vars'))."&page='+dojo.byId('cms_module_menu_menu_entry_page').value,
													load : function(data){
														var content = dojo.byId('cms_vars');
														content.innerHTML = '';
														if(data.length > 0){
															dojo.forEach(data,function(page_var){
																var row = cms_create_element(page_var.name+' - '+page_var.comment,'text','cms_module_menu_menu_entry_page_vars[]','');
																content.appendChild(row);
																var row = cms_create_element('','hidden','cms_module_menu_menu_entry_page_vars_name[]',page_var.name);
																content.appendChild(row);
															});

														}
													}
												});
											});
										}
									});
									break;
								default :
									//do nothing
									break;
							}
							if(!item){
								content.appendChild(cms_create_button('ajouter','".$this->format_text($this->msg['cms_module_menu_menu_entry_button'])."'));
								dojo.byId('ajouter').onclick = function() {
									load_entry();
									dijit.byId('cms_module_menu_entry_form').destroyDescendants(false);
								}
								// on remet le sélecteur en place...
								document.getElementById('cms_module_menu_menu_add_entry').selectedIndex = 0;
							}else{
								content.appendChild(cms_create_button('cms_update_item','".$this->format_text($this->msg['cms_module_menu_menu_entry_button_edit'])."'));
								dojo.byId('cms_update_item').onclick = function() {
									dijit.byId(\"cms_module_menu_entries\").model.store.deleteItem(item);
									load_entry();
									dijit.byId('cms_module_menu_entry_form').destroyDescendants(false);
								}
								content.appendChild(cms_create_button('cms_delete_item','".$this->format_text($this->msg['cms_module_menu_menu_entry_button_delete'])."'));
								dojo.byId('cms_delete_item').onclick = function() {
									dijit.byId(\"cms_module_menu_entries\").model.store.deleteItem(item);
									dijit.byId('cms_module_menu_entry_form').destroyDescendants(false);
								}
							}
						}

						function delete_entry(item){
							if(typeof console != 'undefined') {
								console.log(item);
							}
						}

						function load_entry(){
							var type = document.getElementById('cms_module_menu_entry_type').value;
							var content = document.getElementById('cms_module_menu_entry_form');
							var treeModel = dijit.byId('cms_module_menu_entries').model;
							switch (type){
								case 'url' :
									var label = document.getElementById('cms_module_menu_menu_entry_url_label').value;
									var link = document.getElementById('cms_module_menu_menu_entry_url_link').value;
									treeModel.newItem({id:last,link:link,title:label,type:type});
									last++;
									break;
								case 'infopage' :
									var select = dojo.byId('cms_module_menu_menu_entry_infopage');
									var label = select.options[select.selectedIndex].textContent;
									var id = select.options[select.selectedIndex].value;
									var link = './index.php?lvl=infopages&pagesid='+id;
									treeModel.newItem({id:last,link:link,title:label,type:type});
									last++;
									break;
								case 'page' :
									var select = dojo.byId('cms_module_menu_menu_entry_page');
									var id = select.options[select.selectedIndex].value;
									var label = dojo.byId('cms_module_menu_menu_entry_label').value;
									var link = './index.php?lvl=cmspage&pageid='+id;
									var page_vars = document.forms['".$this->class_name."_manage_form'].cms_module_menu_menu_entry_page_vars;
									var page_vars_name = document.forms['".$this->class_name."_manage_form'].cms_module_menu_menu_entry_page_vars_name;
									if(page_vars && page_vars.value){
										link +='&'+page_vars_name.value+'='+page_vars.value;
									}else{
										for(i in page_vars){
											if(page_vars[i].value != ''){
												link +='&'+page_vars_name[i].value+'='+page_vars[i].value;
											}
										}
									}
									treeModel.newItem({id:last,link:link,title:label,type:type});
									last++;
									break;
							}
							dijit.byId('cms_module_menu_entry_form').destroyDescendants(false);
						}

						function cms_module_menu_update_tree_items(parent,newChildrenList){
							elements_infos= new Object();
							tree_infos= new Object();
							model = dijit.byId('cms_module_menu_entries').model;
							cms_module_menu_get_tree_infos(model.root);
							var http = new http_request();
							http.request('".$this->get_ajax_link(array('do' => "save_tree", 'menu' => $menu))."',true,'&elements='+dojo.toJson(elements_infos)+'&tree_infos='+dojo.toJson(tree_infos));
						}


						function cms_module_menu_get_tree_infos(elem){
							try{
								if(elem.id && elem.id[0] && elem.title && elem.title[0]){
									elements_infos[elem.id[0]] = {
										link : encodeURIComponent(elem.link[0]),
										title : elem.title[0],
										type : elem.type[0]
									};
								}
								if(elem.root && !tree_infos[0]){
									tree_infos[0] = new Array();
								}
								if(elem.children){
									if(!elem.root && !tree_infos[elem.id[0]]){
										tree_infos[elem.id[0]] = new Array();
									}
									for(var i=0 ; i<elem.children.length ; i++){
										if(elem.id && elem.id[0]){
											tree_infos[elem.id[0]].push(elem.children[i].id[0]);
										}else if(elem.root){
											tree_infos[0].push(elem.children[i].id[0]);
										}
										cms_module_menu_get_tree_infos(elem.children[i]);
									}
								}
							}catch(e){
								if(typeof console != 'undefined') {
									console.log(e)
								}
							}
						}
					</script>
				</div>
			</div>
			<div class='row'><hr/></div>
			<div dojoType='dojox.layout.ContentPane' id='cms_module_menu_entry_form' class='row'>
			</div>";
		//composition du menu...
		$form.="
			<div class='row'><hr/>
			</div>
			<script type='text/javascript'>
				dojo.require('dojo.data.ItemFileWriteStore');
				dojo.require('dijit.Tree');
				dojo.require('dijit.tree.dndSource');
				dojo.require('dojox.layout.ContentPane');

				function prepare(){
					var store = new dojo.data.ItemFileWriteStore({
    	        		url: '".$this->get_ajax_link(array('do' => "get_tree",'menu' => $menu))."'
        			});
        			var treeModel = new dijit.tree.ForestStoreModel({
	            		store: store,
        			});

					var treeControl = new dijit.Tree({
						model: treeModel,
						showRoot: false,
						onDblClick : cms_module_menu_edit_item,
						_createTreeNode: function(/*Object*/ args){
							var tnode = new dijit._TreeNode(args);
							tnode.labelNode.innerHTML = args.label;
							return tnode;
						},
						dndController: 'dijit.tree.dndSource'
					},'cms_module_menu_entries');
					dojo.connect(treeModel, 'onChildrenChange', cms_module_menu_update_tree_items);
					dojo.connect(treeModel, 'onChange', cms_module_menu_update_tree_items);
    			}
			    dojo.ready(prepare);

				function cms_module_menu_edit_item(item,node,evt){
					load_entry_form(this.model.store.getValue(item,'type'),item);
				}
    		</script>
			<div class='row' id='cms_module_menu_tree_container' dojoType='dijit.layout.ContentPane'>
				<div id='cms_module_menu_entries'>

				</div>
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<span>".$this->format_text($this->msg['cms_module_menu_manage_form_advertisements'])."</span>
			</div>";
		}
		return $form;
	}

	public function execute_ajax(){
		global $charset;
		global $do;
		global $menu;
		$response = array();
		switch($do){
			case "get_tree" :
				if (!isset($this->managed_datas['module']['menus'][$menu]['items'])) {
					$items = array(
						'identifier' => 'id',
						'label' => 'title',
						'items' => array()
					);
				} else {
					$items = array(
						'identifier' => 'id',
						'label' => 'title',
						'items' => $this->managed_datas['module']['menus'][$menu]['items']
					);
				}
				$response['content'] = json_encode($items);
				$response['content-type'] = "application/json";
				break;
			case "save_tree" :
				global $tree_infos;
				global $elements;

//				$this->debug("------------------------start-------------------------");
//				$this->debug("------------------------posted-------------------------");
//				$this->debug(stripslashes($elements));
//				$this->debug($tree_infos);
				$tree= array();

				if($charset != 'utf-8'){
					$elements = encoding_normalize::utf8_normalize($elements);
				}
				$elements = json_decode(stripslashes($elements),true);
				//$elements = $this->charset_normalize($elements,"utf-8");
				$tree_infos = json_decode(stripslashes($tree_infos),true);
				$tree_infos = array_reverse($tree_infos,true);

//				$this->debug("------------------------entrée-------------------------");
//				$this->debug($elements);
//				$this->debug($tree_infos);
//				$this->debug("------------------------debut boucle-------------------------");
				foreach($tree_infos as $elem => $children){
//					$this->debug("------------------------$elem-------------------------");
//					$this->debug($children);
					if($elements[$elem]){
						$tree[$elem] = array(
								'id' => $elem ,
								'title' => $elements[$elem]['title'],
								'link' => $elements[$elem]['link'],
								'type' => $elements[$elem]['type']
						);
						unset($elements[$elem]);
					}
					if($elem == 0){
						$name = 'items';
					}else $name = 'children';
					foreach($children as $child){
						if($elements[$child]){
							$tree[$elem][$name][] = array(
								'id' => $child ,
								'title' => $elements[$child]['title'],
								'link' => $elements[$child]['link'],
								'type' => $elements[$child]['type']
							);
							unset($elements[$child]);
						}else if($tree[$child]){
							$tree[$elem][$name][] = $tree[$child];
							unset($tree[$child]);
						}
					}
//					$this->debug("------------------------entrée-------------------------");
//					$this->debug($elements);
//					$this->debug("------------------------arbre-------------------------");
//					$this->debug($tree);
				}

				$this->managed_datas['module']['menus'][$menu]['items'] = $tree[0]['items'];
				$query = "replace into cms_managed_modules set managed_module_name = '".addslashes($this->class_name)."', managed_module_box = '".$this->addslashes(serialize($this->managed_datas))."'";
				pmb_mysql_query($query);
				$response['content'] = "OK";
				$response['content-type'] = "application/json";
				break;
			default :
				$response = parent::execute_ajax();
				break;
		}
		return $response;
	}

	public function get_next_item_id($menu){
		$max =  $this->_get_max_item_id($this->managed_datas['module']['menus'][$menu]['items'],0)+1;
		return $max;
	}

	public function _get_max_item_id($items,$max){
		if (is_array($items)){
			foreach($items as $item){
				if(count($item['children'])){
					$max = $this->_get_max_item_id($item['children'],$max);
				}
				if($item['id'] > $max){
					$max = $item['id'];
				}
			}
		}
		return $max;
	}
}