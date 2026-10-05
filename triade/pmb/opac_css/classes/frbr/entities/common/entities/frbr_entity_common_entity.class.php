<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_entity.class.php,v 1.18.4.1 2025/02/12 12:34:08 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/frbr/frbr_filter_fields.class.php");

class frbr_entity_common_entity extends frbr_entity_root{
	protected $manifest;
	protected $have_child; //Propriété booleene nous indiquant si l'entité à des enfants ou non (cadres ou datanode)
	public $informations = array();
	public $elements_used = array();

	/**
	 * instance de la page associée
	 * @var frbr_page
	 */
	protected $page;

	public function __construct($id=0){
	    $this->id = (int) $id;
		$infos = self::read_manifest();
		if (isset($infos['informations'])) {
			$this->informations = $infos['informations'];
		}
		if (isset($infos['elements_used'])) {
			$this->elements_used = $infos['elements_used'];
		}
		parent::__construct();
		$this->fetch_managed_datas();
	}

	public static function get_informations(){
		$infos = self::read_manifest();
		return $infos['informations'];
	}

	public static function read_manifest(){
		global $class_path, $include_path, $lang;
		$informations = array();
		$manifest = new domDocument();
		$entity_path = realpath(dirname($class_path."/frbr/entities/".str_replace(array("frbr_entity_", "_datanode", "_cadre", "_page"),"",static::class)."/".static::class.".class.php"));
		$manifest_path = $entity_path."/manifest.xml";

		if (file_exists($manifest_path)) {
			$manifest->load($manifest_path);

			// on récupère la langue par défaut du module...
	// 		$informations['informations']['default_language'] = self::get_module_default_language($manifest);

			// chemin d'indexation ?
			$path_indexation = $manifest->getElementsByTagName("path_indexation");

			$informations['informations']['indexation']['type'] = $path_indexation->item(0)->attributes->getNamedItem('directory')->nodeValue;
			$informations['informations']['indexation']['path'] = $include_path."/indexation/".(!empty($path_indexation->item(0)->attributes->getNamedItem('directory')->nodeValue) ? $path_indexation->item(0)->attributes->getNamedItem('directory')->nodeValue."/" : "").$manifest->getElementsByTagName("path_indexation")->item(0)->nodeValue."/champs_base.xml";

			$informations['informations']['indexation']['sub_type'] = "";
			$item = $manifest->getElementsByTagName("sub_type")->item(0);
			if (!empty($item)) {
			    $informations['informations']['indexation']['sub_type'] = constant($manifest->getElementsByTagName("sub_type")->item(0)->nodeValue);
			}

			//on récupère le nom
			$names = $manifest->getElementsByTagName("name");
			$name = array();
			for ($i = 0; $i < $names->length; $i++) {
				if ($names->item($i)->parentNode->nodeName == "manifest") {
					if (isset($names->item($i)->attributes->getNamedItem('lang')->nodeValue) && ($names->item($i)->attributes->getNamedItem('lang')->nodeValue == $lang)) {
						$name[$lang] = $names->item($i)->nodeValue;
						break;
					} else if (!$names->item($i)->attributes->getNamedItem('lang') || ($names->item($i)->attributes->getNamedItem('lang') == $informations['informations']['default_language'])) {
						$name['default'] = $names->item($i)->nodeValue;
					}
				}
			}
			$informations['informations']['name'] = encoding_normalize::charset_normalize(isset($name[$lang]) ? $name[$lang] : $name['default'],"utf-8");

			//on récupère le(les) auteur(s)
			$informations['informations']['author'] = array();
			$authors = $manifest->getElementsByTagName("author");
			for($i=0 ; $i<$authors->length ; $i++){
				$author = array();
				//on récupère son nom
				$author['name'] = encoding_normalize::charset_normalize($authors->item($i)->getElementsByTagName('name')->item(0)->nodeValue,"utf-8");
				//on récupère son organisation
				$organisation = $authors->item($i)->getElementsByTagName("organisation");
				if($organisation->length>0){
					$author['organisation'] = encoding_normalize::charset_normalize($organisation->item(0)->nodeValue,"utf-8");
				}
				$informations['informations']['author'][] = $author;
			}

			//on récupère les dates
			$created_date = $manifest->getElementsByTagName("created_date")->item(0);
			$informations['informations']['created_date']= encoding_normalize::charset_normalize($created_date->nodeValue,"utf-8");
			$updated_date = $manifest->getElementsByTagName("updated_date");
			if($updated_date->length>0){
				$informations['informations']['updated_date'] = encoding_normalize::charset_normalize($updated_date->item(0)->nodeValue,"utf-8");
			}
			//on récupère la version
			$version = $manifest->getElementsByTagName("version")->item(0);
			$informations['informations']['version']= encoding_normalize::charset_normalize($version->nodeValue,"utf-8");

			// administrable?
	// 		$informations['informations']['managed'] = ($manifest->getElementsByTagName("managed") && $manifest->getElementsByTagName("managed")->item(0)->nodeValue == "true" ? true : false);

			//fournisseur de liens?
			if(isset($manifest->getElementsByTagName("extension_form")->item(0)->nodeValue)) {
				$informations['informations']['extension_form'] = ($manifest->getElementsByTagName("extension_form")->item(0)->nodeValue == "true" ? true : false);
			} else {
				$informations['informations']['extension_form'] = '';
			}

			//on récupère la listes des éléments utilisés par le module...
			$use = $manifest->getElementsByTagName("use")->item(0);
			$informations['elements_used'] = self::read_elements_used($use);
		}
		return $informations;
	}

	protected function fetch_data(){

	}

	public static function read_elements_used($use_node){
		$elements_used = array();
		$types = array(
			'view',
			'datasource',
			'filter',
			'sorting'
		);
		foreach($types as $type){
			$elements = $use_node->getElementsByTagName($type);
			$elements_used[$type] = array();
			if($elements->length>0){
				for($i=0 ; $i<$elements->length ; $i++){
					if(($elements->item($i)->nodeValue != "")) {
						$elements_used[$type][] = $elements->item($i)->nodeValue;
					}
				}
			}
		}
		return $elements_used;
	}

	protected function get_js_form() {
		$js_form = "
			<script>
				require(['apps/frbr/EntityForm'], function(EntityForm){
					new EntityForm(".encoding_normalize::json_encode(array(
							"id" => $this->id,
							"type" => $this->type,
    					    "className" => $this->class_name,
    					    "numPage" => !empty($this->page) ? $this->page->get_id() : 0,
							"indexation" => (isset($this->informations['indexation']) ? $this->informations['indexation'] : ''),
							"msg" => $this->msg
					)).");
				});
			</script>";
		return $js_form;
	}

	protected function get_buttons_form() {
		$buttons_form = "
		<div class='left'>
			<input type='button' id='cancelButton' class='bouton' value='".$this->format_text($this->msg['frbr_entity_common_entity_cancel'])."' />
			<input type='submit' id='save_button' class='bouton' value='".$this->format_text($this->msg['frbr_entity_common_entity_save'])."' />
			".($this->id ? "<input type='button' class='bouton' value='".$this->format_text($this->msg['frbr_entity_common_entity_duplicate'])."' onClick=\"confirm_duplicate();\" />" : "")."
		</div>
		<div class='right'>
			".($this->id && $this->type != 'page' ? "<input type='button' id='delete_button' class='bouton' value='".$this->format_text($this->msg['frbr_entity_common_entity_delete'])."'/>" : "")."
		</div>";
		return $buttons_form;
	}

	public function get_form($ajax= true) {
		global $msg, $charset;
		global $current_module;

		if($ajax){
			$action = "./ajax.php?module=cms&categ=frbr_entities&elem=".$this->class_name."&action=save_form";
		}
		$form = $this->get_js_form();
		$form .= "
			<form class='form-".$current_module."' id='".$this->class_name."_form' name='".$this->class_name."_form'  method='post' action=\"".htmlentities($action, ENT_QUOTES, $charset)."\" onsubmit='return false;'>
				<h3>".$this->format_text(($this->id ? sprintf($this->msg['frbr_entity_common_entity_'.$this->type.'_edit'],$this->informations['name']." : ".$this->class_name."_".$this->id) : $this->msg['frbr_entity_common_entity_'.$this->type.'_add']))."</h3>
				<div class='form-contenu'>
					<div class='row'>
						".$this->get_linked_form()."
					</div>
					<hr />
					".($this->id ? '<input type="hidden" value="'.$this->id.'" name="id_element" />': '')."
					<div class='row'>&nbsp;</div>
					<div class='row'>
						<div class='colonne3'>
							<label class='etiquette' for='".$this->type."_name'>".$this->format_text($this->msg['frbr_entity_common_entity_'.$this->type.'_name'])."</label>
						</div>
						<div class='colonne-suite'>
							<input type='text' class='saisie-50em' name='".$this->type."_name' id='".$this->type."_name' value='".htmlentities($this->name, ENT_QUOTES, $charset)."' />
						</div>
					</div>
					<div class='row'>
						<div class='colonne3'>
							<label class='etiquette' for='".$this->type."_comment'>".$this->format_text($this->msg['frbr_entity_common_entity_'.$this->type.'_comment'])."</label>
						</div>
						<div class='colonne-suite'>
							<textarea name='".$this->type."_comment' id='".$this->type."_comment' cols='55' rows='5'>".htmlentities($this->comment, ENT_QUOTES, $charset)."</textarea>
						</div>
					</div>
					<hr />
					<div class='row'>&nbsp;</div>
					<div id='parameters_form'> ";
		$form.= $this->get_parameters_form();
		$form.= "	</div>
				<hr />
				</div>
				<div class='row'>
					".$this->get_buttons_form()."
				</div>
			<div class='row'></div>
			</form>
			<script>
				document.forms['".$this->class_name."_form'].elements['".$this->type."_name'].focus();
			</script>
			";
		return $form;
	}

	public function get_manage_menu(){
		$manage_menu = "";
		return $manage_menu;
	}

	protected function get_element_manage_form($quoi){
		global $base_path;
		global $elem;
		switch ($quoi) {
			case 'sorting':
				$type = 'sort';
			default:
				$type = substr($quoi, 0, strlen($quoi)-1);
				break;
		}
		$nb_managed_elems=0;
		$elem_choice="";
		if(isset($this->elements_used[$type])) {
			for($i=0 ; $i<count($this->elements_used[$type]) ; $i++){
				if(method_exists($this->elements_used[$type][$i],"get_manage_form")){
					if(!$elem) $elem = $this->elements_used[$type][$i];
					$nb_managed_elems++;
					$elem_choice.="<p><a href='".$base_path."/cms.php?categ=frbr_manage&sub=".str_replace("frbr_entity_","",$this->class_name)."&quoi=".$quoi."&elem=".$this->elements_used[$type][$i]."&action=get_form'>".$this->format_text($this->msg[$this->elements_used[$type][$i]])."</a></p>";
				}
			}
		}
		$form="
		<div dojoType='dijit.layout.BorderContainer' style='width: 100%; height: 800px;'>";
		if($nb_managed_elems>1){
			$form.="
			<div dojoType='dijit.layout.ContentPane' region='left' splitter='true' style='width:300px;' >
				".$elem_choice."
			</div>";
		}
		$form.="
			<div dojoType='dijit.layout.ContentPane' region='center' >";
		$view = new $elem();
		$view->set_entity_class_name($this->class_name);
		$form.= $view->get_manage_form();
		$form.="
			</div>
		</div>";
		return $form;
	}

	protected function get_managed_form($quoi){

	}

	public function get_already_selected_fields($quoi) {
		global $add_field;
		switch ($quoi) {
			case 'filters':
				$frbr_fields_class_name = 'frbr_filter_fields';
				break;
			case 'sorting':
				$frbr_fields_class_name = 'frbr_sort_fields';
				break;
			case 'backbones':
				$frbr_fields_class_name = 'frbr_backbone_fields';
				break;
		}
		$frbr_instance_fields = new $frbr_fields_class_name($this->informations['indexation']['type'], $this->informations['indexation']['path']);
		if($add_field) {
			$frbr_instance_fields->add_field($add_field);
		}
		return $frbr_instance_fields->get_already_selected();
	}

	protected function fetch_managed_datas($type=""){
		$query = "select managed_entity_box from frbr_managed_entities where managed_entity_name = '".$this->class_name."'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$this->managed_datas = encoding_normalize::charset_normalize(json_decode(pmb_mysql_result($result,0,0), true),'utf-8');
			$this->managed_datas = $this->stripslashes($this->managed_datas);
		}
	}

	protected function get_manage_form($quoi){
		global $msg, $current_module;
		global $base_path;
		global $manage_id;
		global $num_page;
		global $charset;

		$entity_manage_controller = "";
		$frbr_fields_class_name = '';
		switch ($quoi) {
			case 'filters':
				$entity_manage_controller = "FiltersEntityManageController";
				$frbr_fields_class_name = 'frbr_filter_fields';
				$type = 'filter';
				break;
			case 'sorting':
				$entity_manage_controller = "SortingEntityManageController";
				$frbr_fields_class_name = 'frbr_sort_fields';
				$type = 'sort';
				break;
			case 'backbones':
				$entity_manage_controller = "BackbonesEntityManageController";
				$frbr_fields_class_name = 'frbr_backbone_fields';
				$type = 'backbone';
				break;
		}
		$frbr_instance_fields = new $frbr_fields_class_name($this->informations['indexation']['type'], $this->informations['indexation']['path']);
		$manage_id = intval($manage_id);
		if($manage_id) {
			$frbr_instance_fields->unformat_fields($this->managed_datas[$quoi][$type.$manage_id]['fields']);
			$name = $this->managed_datas[$quoi][$type.$manage_id]['name'];
		} else {
			$name = '';
		}
		$action = $base_path."/ajax.php?module=cms&categ=frbr_entities&elem=".$this->class_name."&id_element=".$this->id."&action=save_manage_form&quoi=".$quoi."&manage_id=".$manage_id."&num_page=".$num_page;
		$form = "
			<script src=\"javascript/ajax.js\"></script>
			<script>var operators_to_enable = new Array();</script>
			<form class='form-$current_module' id='".$this->class_name."_".$type."_".$manage_id."_manage_form' name='".$this->class_name."_".$type."_".$manage_id."_manage_form' action='".$action."' method='post'>
				<h3><div class='left'></div><div class='row'></div></h3>
				<div class='form-contenu'>
					<div class='row'>
						<label class='etiquette' for='add_field'>
							".$msg["frbr_".$type."_add_field"]."
						</label>
						".$frbr_instance_fields->get_selector($this->class_name."_".$type."_".$manage_id."_add_field")."
					</div>
					<div class='row'>
						<div class='colonne3'>
							<label class='etiquette' for='".$type."_name'>
								".$msg["frbr_".$type."_name"]."
							</label>
						</div>
						<div class='colonne-suite'>
							<input type='text' id='".$type."_name' name='".$type."_name' value='".htmlentities($name, ENT_QUOTES, $charset)."' class='saisie-80em' />
						</div>
					</div>
					 <br />
					<div class='row'>
						".$frbr_instance_fields->get_already_selected()."
					</div>
					<br />
					<div class='row'>
						<input type='hidden' name='delete_field' value=''/>
						<input type='hidden' name='".$type."_delete' value=''/>
						<input type='hidden' name='num_page' value='".$num_page."'/>
						<div class='left'>
							<input type='button' class='bouton' value='".$msg["76"]."' data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"click\", \"method\":\"hideDialog\", \"parameters\":{\"element\":\"".$type."\", \"idElement\":\"".$this->id."\", \"manageId\": \"".$manage_id."\"}}' />
							<input type='button' class='bouton' value='".$msg["77"]."' data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"click\", \"method\":\"manageSaveForm\", \"parameters\":{\"element\":\"".$type."\", \"idElement\":\"".$this->id."\", \"manageId\": \"".$manage_id."\", \"hide\" : \"1\", \"type\" : \"".(isset($this->type) ? $this->type : '')."\", \"className\" : \"".$this->class_name."\"}}'/>
						</div>
						<div class='right'>
							".($manage_id ? "<input type='button' class='bouton' value='".$msg["63"]."' data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"click\", \"method\":\"manageDeleteForm\", \"parameters\":{\"element\":\"".$type."\", \"idElement\":\"".$this->id."\", \"manageId\": \"".$manage_id."\", \"hide\" : \"1\", \"type\" : \"".(isset($this->type) ? $this->type : '')."\" , \"className\" : \"".$this->class_name."\"}}' />" : "")."
						</div>
					</div>
				</div>
			</form>";
		$form .= "
		<div id='".$this->class_name."_".$type."_".$manage_id."_manage_dnd_container' dojoType='dijit.layout.BorderContainer' data-dojo-props='splitter:true' style='width: 100%; height: 800px;'>
		</div>
		<script>
			require(['apps/frbr/".$entity_manage_controller."', 'dojo/domReady!'], function(EntityManageController){
				var params = {id:'".$this->id."', elem:'".$this->class_name."', type:'".$type."', manage_id:'".$manage_id."'};
				var entityManageController = new EntityManageController(params);
			});
		</script>";
		return $form;
	}

	public function save_manage_form(){
		global $quoi;
		global $manage_id;

		if(!isset($this->managed_datas[$quoi])) $this->managed_datas[$quoi] = array();
		$params = $this->managed_datas[$quoi];
		switch ($quoi) {
			case 'filters' :
				global $filter_delete;
				if($filter_delete){
					unset($params["filter".$filter_delete]);
				}else{
					$frbr_instance_fields = new frbr_filter_fields($this->informations['indexation']['type'], $this->informations['indexation']['path'], $this->informations['indexation']['sub_type']);
					global $filter_name;
					if(!$manage_id) {
						$manage_id = static::get_max_manage_id("filter",$this->managed_datas[$quoi])+1;
					}
					$params["filter".$manage_id] = array(
						'name' => stripslashes($filter_name),
					    'fields' => $frbr_instance_fields->format_fields(),
					    'details' => $this->get_additional_managed_datas(),
					);
				}
				break;
			case 'sorting' :
				global $sort_delete;
				if($sort_delete){
					unset($params["sort".$sort_delete]);
				}else{
					$frbr_instance_fields = new frbr_sort_fields($this->informations['indexation']['type'], $this->informations['indexation']['path'], $this->informations['indexation']['sub_type']);
					global $sort_name;
					if(!$manage_id) {
						$manage_id = static::get_max_manage_id("sort",$this->managed_datas[$quoi])+1;
					}
					$params["sort".$manage_id] = array(
						'name' => stripslashes($sort_name),
						'fields' => $frbr_instance_fields->format_fields(),
					    'details' => $this->get_additional_managed_datas(),
					);
				}
				break;
			case 'backbones' :
				global $backbone_delete;
				if($backbone_delete){
					unset($params["backbone".$backbone_delete]);
				}else{
					$frbr_instance_fields = new frbr_backbone_fields($this->informations['indexation']['type'], $this->informations['indexation']['path']);
					global $backbone_name;
					if(!$manage_id) {
						$manage_id = static::get_max_manage_id("backbone",$this->managed_datas[$quoi])+1;
					}
					$params["backbone".$manage_id] = array(
							'name' => stripslashes($backbone_name),
							'fields' => $frbr_instance_fields->format_fields()
					);
				}
				break;
		}
		return $params;
	}

	public function get_manage_forms(){
		global $base_path;
		global $quoi;

		$form = "
			<script>
				require(['dijit/layout/BorderContainer','dijit/layout/ContentPane']);
			</script>";
		switch($quoi){
			case "views" :
				$form = $this->get_element_manage_form($quoi);
				break;
			case "filters" :
			case "sorting" :
			case "backbones" :
				$form = $this->get_manage_form($quoi);
				break;
		}
		return $form;
	}

	public function save_manage_forms(){
		global $quoi,$elem;

		//on sauvegarde les infos modifiées
		switch ($quoi){
			case "views" :
				$this->managed_datas[$quoi][$elem] = call_user_func(array($elem,"save_manage_form"),$this->managed_datas[$quoi][$elem]);
				break;
			default :
				$this->managed_datas[$quoi] = $this->save_manage_form();
				break;
		}
		$query = "replace into frbr_managed_entities set managed_entity_name = '".$this->class_name."', managed_entity_box = '".addslashes(encoding_normalize::json_encode($this->managed_datas))."'";
		pmb_mysql_query($query);
		return true;
	}

	protected function get_max_manage_id($property="entity", $datas=array()){
		$max = 0;
		if(count($datas)){
			foreach	($datas as $key => $val){
			    $key = intval(str_replace($property,"",$key));
				if($key>$max) $max = $key;
			}
		}
		return $max;
	}

	protected function get_parent_name_from_page($parent_id) {
		global $num_page, $charset;

		$selector = "<select name='".$this->type."_num_parent' id='".$this->type."_num_parent' ".($this->id ? "disabled" :"")." data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"change\", \"method\":\"loadParametersForm\", \"parameters\":{\"type\":\"".$this->type."\", \"page\":\"".($num_page ? $num_page : 0)."\"}}'>";
		if (isset($this->page) && $this->page) {
			$selector .=	'
						<optgroup label="'.$this->msg['frbr_entity_common_entity_page_label'].'">
							<option value="0" '.(!$parent_id ? 'selected="selected"' : '').'>'.htmlentities($this->page->get_name(), ENT_QUOTES, $charset).'</option>
						</optgroup>';
		}
		if ($num_page) {
			$query = '	SELECT id_datanode, datanode_name
						FROM frbr_datanodes
						WHERE datanode_num_page = "'.$num_page.'"
					';
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$selector .= '<optgroup label="'.$this->msg['frbr_entity_common_entity_datanode_label'].'">';
				while ($row = pmb_mysql_fetch_object($result)) {
					$selector .= '<option value="'.$row->id_datanode.'" '.($parent_id == $row->id_datanode ? 'selected="selected"' : '').'>'.htmlentities($row->datanode_name, ENT_QUOTES, $charset).'</option>';
				}
				$selector .= '</optgroup>';
			}
		}
		$selector .= '</select>';
		return $selector;
	}

	public function set_entity_type($entity_type){
		$this->entity_type = $entity_type;
	}

	public function get_page() {
		return $this->page;
	}

	/**
	 *
	 * @param frbr_page $page
	 * @return frbr_entity_common_entity
	 */
	public function set_page($page) {
		$this->page = $page;
		return $this;
	}

	/**
	 * @param int $num_page
	 */
	public function set_page_from_num($num_page) {
	    $num_page = intval($num_page);
		$this->page = new frbr_entity_common_entity_page($num_page);
	}

	/**
	 * donnees complementaires
	 * @return array
	 */
	public function get_additional_managed_datas() {
	    return [];
	}
}