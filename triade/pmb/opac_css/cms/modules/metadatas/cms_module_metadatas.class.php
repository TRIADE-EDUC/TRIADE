<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_metadatas.class.php,v 1.14.2.1.2.2 2025/02/12 12:34:07 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_metadatas extends cms_module_common_module {

	public function __construct($id=0){
		$this->module_path = str_replace(basename(__FILE__),"",__FILE__);
		parent::__construct($id);
	}

	public function get_manage_form(){
		global $base_path;

		//variables persos...
		global $metadatas;

		$form="
		<h3>".$this->format_text($this->msg['cms_module_metadatas_manage_title'])."</h3>
		<div data-dojo-type='dijit/layout/BorderContainer' style='width: 100%; height: 800px;'>
			<div data-dojo-type='dijit/layout/ContentPane' region='left' splitter='true' style='width:300px;' >";
		if($this->managed_datas['module']['metadatas']){
			foreach($this->managed_datas['module']['metadatas'] as $key => $group_metadatas){
				$form.="
					<p>
						<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->class_name)."&quoi=module&metadatas=".$key."&action=get_form'>".$this->format_text($group_metadatas['name'])."</a>
					&nbsp;
						<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->class_name)."&quoi=module&metadatas_delete=".$key."&action=save_form' onclick='return confirm(\"".$this->format_text($this->msg['cms_module_metadatas_group_delete_metadatas'])."\")'>
							<img src='".get_url_icon('trash.png')."' alt='".$this->format_text($this->msg['cms_module_root_delete'])."' title='".$this->format_text($this->msg['cms_module_root_delete'])."'/>
						</a>
					</p>";
			}
		}
		$form.="
				<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->class_name)."&quoi=module&metadatas=new'>".$this->format_text($this->msg['cms_module_metadatas_add_metadatas'])."</a>
			";
		$form.="
			</div>
			<div data-dojo-type='dijit/layout/ContentPane' region='center' >";
		if($metadatas){
			$form.=$this->get_managed_form_start(array('metadatas'=>$metadatas));
			$form.=$this->get_managed_metadatas_form($metadatas);
			$form.=$this->get_managed_form_end();
		}
		$form.="
			</div>
		</div>";
		return $form;
	}

	public function get_metas_list(){

		$struct = array();
		/* $struct[] = array(
		 *		'prefix' => "..",
		 *		'name' => "..",
		 *		'items' => array(
		 *			'cle' => array(
		 *						'label' => "..",
		 *						'desc' => "..",
		 *						'default_template' => "{{..}}"
		 *			),
		 *		),
		 *		'separator' => ":",
		 *		'group_template' => "<meta name='{{key_metadata}}' content='{{value_metadata}}' />"
		 *	);
		 *
		 *
		 */
		if (is_countable($this->managed_datas['module']['metadatas']) && count($this->managed_datas['module']['metadatas'])) {
			foreach ($this->managed_datas['module']['metadatas'] as $key_metadatas=>$group_metadatas) {
				$struct[$key_metadatas] = $group_metadatas;
			}
		}
		return $struct;
	}

	protected function get_managed_metadatas_form($metadatas){
		global $opac_url_base;
		global $base_path;

		if($metadatas != "new"){
			$infos = $this->managed_datas['module']['metadatas'][$metadatas];
		} else {
			$infos = array(
					'name' => '',
					'prefix' => '',
					'separator' => '',
					'group_template' => '<meta property="{{key_metadata}}" content="{{value_metadata}}" />',
					'replace' => ''
			);
		}
		$form="
			<div class='row'>
				<div class='colonne3'>
				</div>
				<div class='colonne-suite'>
				</div>
			</div>";
		//nom du groupe de méta-données
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_metadatas_group_name'>".$this->format_text($this->msg['cms_module_metadatas_group_name'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_metadatas_group_name' value='".$this->format_text($infos['name'])."'/>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_metadatas_group_prefix'>".$this->format_text($this->msg['cms_module_metadatas_group_prefix'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_metadatas_group_prefix' value='".$this->format_text($infos['prefix'])."'/>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_metadatas_group_separator'>".$this->format_text($this->msg['cms_module_metadatas_group_separator'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_metadatas_group_separator' value='".$this->format_text($infos['separator'])."'/>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_metadatas_group_template'>".$this->format_text($this->msg['cms_module_metadatas_group_template'])."</label>
				</div>
				<div class='colonne-suite'>
					<textarea name='cms_module_metadatas_group_template' id='cms_module_metadatas_group_template'>".$this->format_text($infos['group_template'])."</textarea>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_metadatas_replace'>".$this->format_text($this->msg['cms_module_metadatas_replace'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='checkbox' name='cms_module_metadatas_replace' id='cms_module_metadatas_replace' ".($this->format_text($infos['replace']) ? "checked" : "")." />
				</div>
			</div>";
		if($metadatas!="new"){
			//sélecteur de méta
			$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_metadatas_group_add_metadata'>".$this->format_text($this->msg['cms_module_metadatas_group_add_metadata'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='button' class='bouton' name='cms_module_metadatas_group_bt_add_line' id='cms_module_metadatas_group_bt_add_line' value=\"".$this->format_text($this->msg['cms_module_metadatas_group_bt_add_line'])."\" onclick=\"load_metadata_form()\"/>
					<script>
						var last = ".$this->get_next_item_id($metadatas).";
						function load_metadata_form(key){
							var elem = {};
							if(key) {
								var response = cms_module_metadatas_get_tab_item(key);
								elem = dojo.fromJson(response);
							} else {
								key = '';
								elem.label = '';
								elem.desc = '';
								elem.default_template = '';
							}
							var content = dojo.byId('cms_module_metadatas_metadata_form');
							content.innerHTML = '';
							var row = cms_create_element('".$this->format_text($this->msg['cms_module_metadatas_group_key_metadata'])."','text','cms_module_metadatas_group_key_metadata',key);
							content.appendChild(row);
							var row = cms_create_element('".$this->format_text($this->msg['cms_module_metadatas_group_label_metadata'])."','text','cms_module_metadatas_group_label_metadata',elem.label);
							content.appendChild(row);
							var row = cms_create_element('".$this->format_text($this->msg['cms_module_metadatas_group_desc_metadata'])."','text','cms_module_metadatas_group_desc_metadata',elem.desc);
							content.appendChild(row);
							var row = cms_create_element('".$this->format_text($this->msg['cms_module_metadatas_group_default_template_metadata'])."','textarea','cms_module_metadatas_group_default_template_metadata',elem.default_template);
							content.appendChild(row);

							if(key) {
								content.appendChild(cms_create_button('edit','".$this->format_text($this->msg['cms_module_metadatas_group_modify_button_metadata'])."'));
								document.getElementById('cms_module_metadatas_group_key_metadata').setAttribute('disabled', 'disabled');
							} else {
								content.appendChild(cms_create_button('edit','".$this->format_text($this->msg['cms_module_metadatas_group_add_button_metadata'])."'));
							}
							dojo.byId('edit').onclick = function() {
								edit_metadata(key);
							}
						}
					</script>
				</div>
			</div>
			<div class='row'><hr/></div>
			<div id='cms_module_metadatas_metadata_form' class='row'>
			</div>
			<div class='row'>&nbsp;</div>";
		//composition du groupe de méta-données...
		$form.="
			<script>
				var elements_infos= new Object();

				function edit_metadata(key){
					elements_infos= new Object();

					var label = document.getElementById('cms_module_metadatas_group_label_metadata').value;
					var desc = document.getElementById('cms_module_metadatas_group_desc_metadata').value;
					var default_template = document.getElementById('cms_module_metadatas_group_default_template_metadata').value;
					if(key) {
						var adding = false;
					} else {
						var key = document.getElementById('cms_module_metadatas_group_key_metadata').value;
						var adding = true;
					}
					if(!key || !label)	return;

					elements_infos[key] = {
						label : label,
						desc : desc,
						default_template : encodeURIComponent(default_template)
					};

					cms_module_metadatas_update_tab_items(elements_infos);

					if(!adding) {
						document.getElementById('td_metadata_label_'+key).innerHTML = label;
						document.getElementById('td_metadata_desc_'+key).innerHTML = desc;
						document.getElementById('td_metadata_default_template_'+key).innerHTML = default_template;
					} else {
						var tr = document.createElement('TR');
						tr.setAttribute('id', 'tr_metadata_'+key);

						// edit
						var td = document.createElement('TD');
						var img = document.createElement('img');
				        img.setAttribute('src', '".get_url_icon('b_edit.png')."');
				        img.setAttribute('title', \"".$this->format_text($this->msg['cms_module_metadatas_group_edit_metadata'])."\");
						img.onclick=function(){load_metadata_form(key);};
						td.appendChild(img);
						tr.appendChild(td);

						// clé
						var td = document.createElement('TD');
						td.setAttribute('id', 'td_metadata_key_'+key);
						td.appendChild(document.createTextNode(key));
						tr.appendChild(td);

						// label
						var td = document.createElement('TD');
						td.setAttribute('id', 'td_metadata_label_'+key);
						td.appendChild(document.createTextNode(label));
						tr.appendChild(td);

						// description
						var td = document.createElement('TD');
						td.setAttribute('id', 'td_metadata_desc_'+key);
						td.appendChild(document.createTextNode(desc));
						tr.appendChild(td);

						// template par défaut
						var td = document.createElement('TD');
						td.setAttribute('id', 'td_metadata_default_template_'+key);
						td.appendChild(document.createTextNode(default_template));
						tr.appendChild(td);

						// suppression
						var td = document.createElement('TD');
						var supr = document.createElement('input');
				        supr.setAttribute('type', 'button');
				        supr.setAttribute('value', 'X');
				        supr.setAttribute('class', 'bouton');
						supr.onclick=function(){del_metadata(key);};
						td.appendChild(supr);
						tr.appendChild(td);

						document.getElementById('metadatas_group_list').appendChild(tr);
					}
					dojo.byId('cms_module_metadatas_group_key_metadata').value='';
					dojo.byId('cms_module_metadatas_group_label_metadata').value='';
					dojo.byId('cms_module_metadatas_group_desc_metadata').value='';
					dojo.byId('cms_module_metadatas_group_default_template_metadata').value='';
				}

				function del_metadata(key){
					var tr = document.getElementById('tr_metadata_'+key);
					cms_module_metadatas_delete_tab_item(key);
					tr.parentNode.removeChild(tr);
				}

				function cms_module_metadatas_update_tab_items(elem){
					var http = new http_request();
					var response = http.request('".$this->get_ajax_link(array('do' => "save_tab", 'metadatas' => $metadatas))."',true,'&elements='+dojo.toJson(elem));
					return response;
				}
				function cms_module_metadatas_delete_tab_item(elem){
					var http = new http_request();
					var response = http.request('".$this->get_ajax_link(array('do' => "del_tab", 'metadatas' => $metadatas))."',true,'&suppr_element='+elem);
					return response;
				}
				function cms_module_metadatas_get_tab_item(elem){
					var http = new http_request();
					var response = http.request('".$this->get_ajax_link(array('do' => "get_tab", 'metadatas' => $metadatas))."',true,'&get_element='+elem);
					if(response == 0){
						return http.get_text();
					}
					return '';
				}
			</script>
			<div class='row'><hr/>
			</div>
			<div class='row' id='cms_module_metadatas_tab_container' data-dojo-type='dijit/layout/ContentPane'>
				<div id='cms_module_metadatas_group'>
					<div class='row'>
						<table id='metadatas_group_list' name='metadatas_group_list'>
							<tr>
								<th></th>
								<th>".$this->format_text($this->msg['cms_module_metadatas_group_key_metadata'])."</th>
								<th>".$this->format_text($this->msg['cms_module_metadatas_group_label_metadata'])."</th>
								<th>".$this->format_text($this->msg['cms_module_metadatas_group_desc_metadata'])."</th>
								<th>".$this->format_text($this->msg['cms_module_metadatas_group_default_template_metadata'])."</th>
								<th></th>
							</tr>
							!!item_list!!
						</table>
					</div>
    	        </div>
			</div>
			<div class='row'>
				<span>".$this->format_text($this->msg['cms_module_metadatas_manage_form_advertisements'])."</span>
			</div>";

			$elt_tpl="
			<tr id='tr_metadata_!!item_key!!'>
				<td><img src='".get_url_icon('b_edit.png')."' title=\"".$this->format_text($this->msg['cms_module_metadatas_group_edit_metadata'])."\" alt=\"".$this->format_text($this->msg['cms_module_metadatas_group_edit_metadata'])."\" onclick=\"load_metadata_form('!!item_key!!');\" /></td>
				<td id='td_metadata_key_!!item_key!!'>!!item_key!!</td>
				<td id='td_metadata_label_!!item_key!!'>!!item_label!!</td>
				<td id='td_metadata_desc_!!item_key!!'>!!item_desc!!</td>
				<td id='td_metadata_default_template_!!item_key!!'>!!item_default_template!!</td>
				<td>
					<input class='bouton' type='button' value='X' onclick=\"del_metadata('!!item_key!!');\" >
				</td>
			</tr>";

			$item_list = "";
			if(is_countable($infos['items']) && count($infos['items'])) {
				foreach($infos['items'] as $key=>$item){
					$tpl_item=$elt_tpl;
					$tpl_item=str_replace('!!item_key!!',$this->format_text($key), $tpl_item);
					$tpl_item=str_replace('!!item_label!!',$this->format_text($item["label"]), $tpl_item);
					$tpl_item=str_replace('!!item_desc!!',$this->format_text($item["desc"]), $tpl_item);
					$tpl_item=str_replace('!!item_default_template!!',$this->format_text($item["default_template"]), $tpl_item);
					$item_list.=$tpl_item;
				}
			}
			$form = str_replace('!!item_list!!', $item_list, $form);

		}

		return $form;
	}

	public function execute_ajax(){
		global $charset;
		global $do;
		global $metadatas;
		$response = array();
		switch($do){
			case "get_tab" :
				global $get_element;
				$response['content'] = json_encode($this->managed_datas['module']['metadatas'][$metadatas]['items'][$get_element]);
				$response['content-type'] = "application/json";
				break;
			case "del_tab" :
				global $suppr_element;

				if (!isset($this->managed_datas['module']['metadatas'][$metadatas]['items'])) {
					$items = array(
							'identifier' => 'id',
							'label' => 'title',
							'items' => array()
					);
				} else {
					$items = array(
							'identifier' => 'id',
							'label' => 'title',
							'items' => $this->managed_datas['module']['metadatas'][$metadatas]['items']
					);
				}
				if($charset != 'utf-8'){
					$suppr_element = encoding_normalize::utf8_normalize($suppr_element);
				}
				if ($suppr_element) {
				    if (is_countable($items['items']) && count($items['items'])) {
						if (array_key_exists($suppr_element, $items['items'])) {
						    array_splice($items['items'],$suppr_element,1);
						}
					}
				}
				$this->managed_datas['module']['metadatas'][$metadatas]['items'] = $items["items"];
				$query = "replace into cms_managed_modules set managed_module_name = '".addslashes($this->class_name)."', managed_module_box = '".$this->addslashes(serialize($this->managed_datas))."'";
				pmb_mysql_query($query);
				$response['content'] = "OK";
				$response['content-type'] = "application/json";
				break;
			case "save_tab" :
				global $elements;

				if(!isset($this->managed_datas['module']['metadatas'][$metadatas]) || !isset($this->managed_datas['module']['metadatas'][$metadatas]['items'])){
					$items = array(
							'identifier' => 'id',
							'label' => 'title',
							'items' => array()
					);
				}else {
					$items = array(
							'identifier' => 'id',
							'label' => 'title',
							'items' => $this->managed_datas['module']['metadatas'][$metadatas]['items']
					);
				}
				if($charset != 'utf-8'){
					$elements = encoding_normalize::utf8_normalize($elements);
				}

				// \s matches any whitespace character (equal to [\r\n\t\f\v ])
				$text = preg_replace("/\r|\n/", "", $elements);
				$elements = encoding_normalize::json_decode(stripslashes($text), true);

				if (!empty($elements) && is_countable($elements) && count($elements)) {
					foreach ($elements as $key=>$value) {
						$items['items'][$key] = array(
							'label' => $value['label'],
							'desc' => $value['desc'],
							'default_template' => $value['default_template']
						);
					}
				}
				$this->managed_datas['module']['metadatas'][$metadatas]['items'] = $items["items"];
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

	public function get_next_item_id($metadatas){
		$max =  $this->_get_max_item_id($this->managed_datas['module']['metadatas'][$metadatas]['items'],0)+1;
		return $max;
	}

	public function _get_max_item_id($items,$max){
		if(is_array($items)){
			foreach($items as $item){
			    if(isset($item['children']) && is_countable($item['children']) && count($item['children'])){
					$max = $this->_get_max_item_id($item['children'],$max);
				}
				if(isset($item['id']) && $item['id'] > $max){
					$max = $item['id'];
				}
			}
		}
		return $max;
	}

	public function save_manage_form(){
		global $metadatas;
		global $metadatas_delete;
		global $cms_module_metadatas_group_name;
		global $cms_module_metadatas_group_prefix;
		global $cms_module_metadatas_group_separator;
		global $cms_module_metadatas_group_template;
		global $cms_module_metadatas_replace;
		$params = $this->managed_datas['module'];

		if($metadatas_delete){
			unset($params['metadatas'][$metadatas_delete]);
		}else{
			//ajout d'un groupe de méta-données
			if($metadatas == "new"){
				$metadatas_infos = array(
					'name' => stripslashes($cms_module_metadatas_group_name),
					'prefix' => stripslashes($cms_module_metadatas_group_prefix),
					'separator' => stripslashes($cms_module_metadatas_group_separator),
					'group_template' => stripslashes($cms_module_metadatas_group_template),
					'replace' => $cms_module_metadatas_replace,
					'items' => array()
				);
				$params['metadatas']['metadatas'.(self::get_max_metadatas_id($this->managed_datas['module']['metadatas'])+1)] = $metadatas_infos;
			}else{
				//sinon on réécrit juste l'élément
				$params['metadatas'][$metadatas]['name'] = stripslashes($cms_module_metadatas_group_name);
				$params['metadatas'][$metadatas]['prefix'] = stripslashes($cms_module_metadatas_group_prefix);
				$params['metadatas'][$metadatas]['separator'] = stripslashes($cms_module_metadatas_group_separator);
				$params['metadatas'][$metadatas]['group_template'] = stripslashes($cms_module_metadatas_group_template);
				$params['metadatas'][$metadatas]['replace'] = $cms_module_metadatas_replace;
			}
		}
		return $params;
	}

	protected function get_max_metadatas_id($datas){
		$max = 0;
		if(is_countable($datas) && count($datas)){
			foreach	($datas as $key => $val){
			    $key = intval(str_replace("metadatas","",$key));
				if($key>$max) $max = $key;
			}
		}
		return $max;
	}
}