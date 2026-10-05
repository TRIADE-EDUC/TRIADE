<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_sparql_datasource_sparql.class.php,v 1.8.2.2.2.1 2025/02/12 12:34:09 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_sparql_datasource_sparql extends cms_module_common_datasource{

	public function __construct($id=0){
		parent::__construct($id);
	}

	public function get_available_selectors(){
		return array(
			"cms_module_sparql_selector_server"
		);
	}

	public function get_managed_selectors(){
		return array(
			"cms_module_sparql_selector_endpoint"
		);
	}


	public function get_form(){
		$form = parent::get_form();
		$form.="
		<div class='row'>
			<div class='colonne3'>
				<label for='".$this->get_form_value_name("query")."'>".$this->format_text($this->msg['cms_module_sparql_datasource_sparql_query'])."</label>
				".$this->get_format_data_structure_tree($this->get_form_value_name("query"))."
			</div>
			<div class='colonne-suite'>
				<textarea id='".$this->get_form_value_name("query")."' name='".$this->get_form_value_name("query")."'>".$this->format_text($this->parameters['query'])."</textarea>
			</div>
		</div>";
		return $form;
	}

	public function save_form(){
		$this->parameters['query'] = stripslashes($this->get_value_from_form("query"));
		return parent::save_form();
	}

	public function get_datas(){
	    global $base_path;
		$datas = array();
		$selector = $this->get_selected_selector();
		$this->set_module_class_name("cms_module_sparql");
		if($selector->get_value()){
			//la config ARC2 varie en fonction de l'origine du server SPARL
			$selector_config = new $this->managed_datas['stores'][$selector->get_value()]['selector']($this->managed_datas['stores'][$selector->get_value()]['selector_id']);
			$config = array();
			switch($this->managed_datas['stores'][$selector->get_value()]['selector']){
				case "cms_module_sparql_selector_endpoint" :
					$config = array(
						'remote_store_endpoint' => $selector_config->get_value(),
						'remote_store_timeout' => 15
					);
					$store = ARC2::getRemoteStore($config);
					break;
			}
			if($this->parameters['query']){
				$querydatas = array(
					'get_vars' => $_GET,
					'post_vars' => $_POST,
				);
				try{
					$template_path = $base_path.'/temp/'.LOCATION.'_sparql_datasource_sparql_'.$this->id;
					if(!file_exists($template_path) || (md5($this->parameters['query']) != md5_file($template_path))){
					    file_put_contents($template_path, $this->parameters['query']);
					}
					$H2o = H2o_collection::get_instance($template_path);
					$query = $H2o->render($querydatas);

					$rows = $store->query($query, 'rows');
					if(!$rows){
						$this->debug("Execution failed : ".$query);
						$errors=$store->getErrors();
						foreach($errors as $error){
						    $this->debug(encoding_normalize::utf8_decode($error));
						}
					}else{
					//	$this->charset_normalize($rows, "utf-8");
					}
				}catch(Exception $e){
					$rows = array();
				}
			}
		}
		$this->debug($query);
		$datas['result'] = $rows;
		return $datas;
	}

	public function get_manage_form(){
		global $base_path;
		//variables persos...
		global $cms_store;
		global $cms_store_delete;

		if(!$this->managed_datas) $this->managed_datas = array();
		if($this->managed_datas['stores'][$cms_store_delete]) unset($this->managed_datas['stores'][$cms_store_delete]);

		$form="
		<div dojoType='dijit.layout.BorderContainer' style='width: 100%; height: 800px;'>
			<div dojoType='dijit.layout.ContentPane' region='left' splitter='true' style='width:200px;' >";
		if($this->managed_datas['stores']){
			foreach($this->managed_datas['stores'] as $key => $infos){
				$form.="
					<p>
						<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->module_class_name)."&quoi=datasources&elem=".$this->class_name."&cms_store=".$key."&action=get_form'>".$this->format_text($infos['name'])."</a>
						&nbsp;
						<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->module_class_name)."&quoi=datasources&elem=".$this->class_name."&cms_store_delete=".$key."&action=save_form' onclick='return confirm(\"".$this->format_text($this->msg['cms_module_common_view_django_delete_store'])."\")'>
							<img src='".get_url_icon('trash.png')."' alt='".$this->format_text($this->msg['cms_module_root_delete'])."' title='".$this->format_text($this->msg['cms_module_root_delete'])."'/>
						</a>
					</p>";
			}
		}
		$form.="
				<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->module_class_name)."&quoi=datasources&elem=".$this->class_name."&cms_store=new&action=get_form'/>".$this->format_text($this->msg['cms_module_sparql_datasource_sparql_add_store'])."</a>";
		$form.="
			</div>
			<div dojoType='dijit.layout.ContentPane' region='center' >";
		if($cms_store){
			$form.=$this->get_managed_form_start(array('cms_store'=>$cms_store));
			$form.=$this->get_managed_store_form($cms_store);
			$form.=$this->get_managed_form_end();
		}
		$form.="
			</div>
		</div>";
		return $form;
	}

	protected function get_managed_store_form($cms_store){
		global $opac_url_base;
		global $cms_module_sparql_datasource_sparql_managed_store_type;
		global $selector_choice;

		if($cms_store != "new"){
			$infos = $this->managed_datas['stores'][$cms_store];
		}else{
			$infos = array(
					'name' => "Nouveau store",
					'selector' => "",
					'selector_id' => 0,
					'content' => $this->default_store
			);
		}
		//nom
		$form ="
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_sparql_datasource_sparql_managed_storename'>".$this->format_text($this->msg['cms_module_sparql_datasource_sparql_managed_storename'])."</label>
			</div>
			<div class='colonne-suite'>
				<input type='text' name='cms_module_sparql_datasource_sparql_managed_storename' value='".$this->format_text($infos['name'])."'/>
			</div>
		</div>";

		$selectors = $this->get_managed_selectors();
		$form.= "
			<div class='colonne3'>
				<label for='selector_choice'>".$this->format_text($this->msg['cms_module_common_datasource_selector_choice'])."</label>
			</div>
			<div class='colonne-suite'>
				<input type='hidden' name='selector_choice_last_value' id='selector_choice_last_value' value='".($infos['selector'] ? $infos['selector'] : "" )."' />
				<select name='selector_choice' id='selector_choice' onchange='load_selector_form(this.value)'>
					<option value=''>".$this->format_text($this->msg['cms_module_common_datasource_selector_choice'])."</option>";
		foreach($selectors as $selector){
			$form.= "
					<option value='".$selector."' ".($selector == $infos['selector'] ? "selected='selected'":"").">".$this->format_text($this->msg[$selector])."</option>";
		}
		$form.="
				</select>
				<script>
					dojo.require('dojox.layout.ContentPane');
					function cms_module_load_elem_form(elem,id,dom_id){
						dojo.xhrPost({
							url : './ajax.php?module=cms&categ=module&elem='+elem+'&action=get_form&id='+id,
							postData : 'cms_build_info=".rawurlencode(serialize($this->cms_build_env))."&cms_module_class=".rawurlencode($this->class_name)."',
							handelAs : 'text/html',
							load : function(data){
								dijit.byId(dom_id).set('content',data);
							}
						});
					}
					function load_selector_form(selector){
						if(selector != ''){
							//on évite un message d'alerter si le il n'y a encore rien de fait...
							if(document.getElementById('selector_choice_last_value').value != ''){
								var confirmed = confirm('".addslashes($this->msg['cms_module_common_selector_confirm_change_selector'])."');
							}else{
								var confirmed = true;
							}
							if(confirmed){
								document.getElementById('selector_choice_last_value').value = selector;
								cms_module_load_elem_form(selector,0,'selector_form');
							}else{
								var sel = document.getElementById('selector_choice');
								for(var i=0 ; i<sel.options.length ; i++){
									if(sel.options[i].value == document.getElementById('selector_choice_last_value').value){
										sel.selectedIndex = i;
									}
								}
							}
						}
					}
				</script>
			</div>";
		$form.="
			<div id='selector_form' dojoType='dojox.layout.ContentPane'></div>";
		if($infos['selector']){
			$form.="
			<script>
				cms_module_load_elem_form('".$infos['selector']."','".$infos['selector_id']."','selector_form');
			</script>";
		}

		return $form;
	}


	public function save_manage_form($managed_datas){
		global $cms_store;
		global $cms_store_delete;
		global $cms_module_sparql_datasource_sparql_managed_storename;
		global $selector_choice;

		if($cms_store_delete){
			if($managed_datas['stores'][$cms_store_delete]['selector']){
				$selector = new $managed_datas['stores'][$cms_store_delete]['selector']($managed_datas['stores'][$cms_store_delete]['selector_id']);
				$selector->delete();
			}
			unset($managed_datas['stores'][$cms_store_delete]);
		}else{
			if($cms_store == "new"){
				$cms_store = "store".(self::get_max_store_id($managed_datas['stores'])+1);
				$selector = new $selector_choice();
			}else{
				$selector = new $selector_choice($managed_datas['stores'][$cms_store]['selector_id']);
			}
			$result = $selector->save_form();
			if($result){
				$managed_datas['stores'][$cms_store] = array(
					'name' => stripslashes($cms_module_sparql_datasource_sparql_managed_storename),
					'selector' => stripslashes($selector_choice),
					'selector_id' => stripslashes($selector->id)

				);
			}
		}
		return $managed_datas;
	}

	protected function get_max_store_id($datas){
		$max = 0;
		if(is_countable($datas) && count($datas)){
			foreach	($datas as $key => $val){
			    $key = intval(str_replace("store","",$key));
				if($key>$max) $max = $key;
			}
		}
		return $max;
	}

	public function get_format_data_structure_tree($textarea){

		$html = "
		<div id='datasource_tree' class='row'>
		</div>
		<script>
			require(['dojo/data/ItemFileReadStore', 'dijit/tree/ForestStoreModel', 'dijit/Tree','dijit/Tooltip'],function(Memory,ForestStoreModel,Tree,Tooltip){
				var datas = {identifier:'var',label:'var'};
				datas.items = ".json_encode(encoding_normalize::utf8_normalize($this->get_format_datasource_data_structure())).";

				var store = Memory({
					data :datas
				});
				var model = new ForestStoreModel({
					store: store,
					rootId: 'root',
					rootLabel:'Vars'
				});
				var tree = new Tree({
					model: model,
					showRoot: false,
					onDblClick: function(item){
						document.getElementById('".$textarea."').value = document.getElementById('".$textarea."').value + '{{'+item.var[0]+'}}';
					},

					},'datasource_tree');
					new Tooltip({
					connectId: 'struct_tree',
					selector: 'span',
					getContent: function(matchedNode){
						return dijit.getEnclosingWidget(matchedNode).item.desc[0];
					}
				});
			});


		</script>";

		return $html;
	}

	public function get_format_datasource_data_structure(){
// 		$this->debug($GLOBALS,CMS_DEBUG_MODE_FILE);na
		$postdatas = $getdatas = $datas = array();
		foreach($this->cms_build_env['get'] as $key => $value){
			$getdatas[] = array(
				'var' => "get_vars.".$key,
				'desc'=> "",
			);
		}
		$datas[] = array(
				'var' => "get",
				'desc'=> "",
				'children' => $getdatas
		);
		foreach($this->cms_build_env['post'] as $key => $value){
			$postdatas[] = array(
				'var' => "post_vars.".$key,
				'desc'=> "",
			);
		}
		$datas[] = array(
				'var' => "post",
				'desc'=> "",
				'children' => $postdatas
		);
		return $datas;
	}
}