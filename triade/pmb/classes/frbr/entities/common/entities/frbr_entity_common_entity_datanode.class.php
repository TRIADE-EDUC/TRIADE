<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_entity_datanode.class.php,v 1.47.8.1 2025/04/24 12:37:05 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/frbr/frbr_entities.class.php");
require_once($class_path."/frbr/entities/common/filters/frbr_entity_common_children_filter.class.php");

class frbr_entity_common_entity_datanode extends frbr_entity_common_entity {

	/**
	 * Identifiant de la source de données
	 */
	protected $id;

	/**
	 * Libellé
	 * @var string
	 */
	protected $name;

	/**
	 * Description
	 */
	protected $comment;

	/**
	 * Limite
	 * @var int
	 */
	protected $limit;

	/**
	 * Instance de la source de données parente
	 * @var frbr_entity_common_entity_datanode
	 */
	protected $parent;

	protected $type = 'datanode';

	/**
	 * type d'entité du datanode
	 * @var int
	 */
	protected $entity_type;

	protected $datasource = array();
	protected $filter = array();
	protected $sort = array();
	protected $children_filter = array();

	/**
	 *
	 * @var frbr_entity_common_datasource
	 */
	protected $datasource_instance;

	protected static $datanode_instances = array();

	protected $parent_type = '';
	protected $parent_parameters = array();

	public function __construct($id=0) {
		parent::__construct($id);
	}

	protected function fetch_data() {
		$this->name = '';
		$this->comment = '';
		$this->limit = '';
		$this->entity_type = '';
		if($this->id) {
			$query = 'select * from frbr_datanodes where id_datanode ='.$this->id;
			$result = pmb_mysql_query($query);
			$row = pmb_mysql_fetch_object($result);
			$this->name = $row->datanode_name;
			$this->comment = $row->datanode_comment;
			$this->entity_type = static::get_entity_type_from_object($row->datanode_object);
			$this->page = new frbr_entity_common_entity_page($row->datanode_num_page);
			//parent
			$this->set_parent_from_num($row->datanode_num_parent);
			$query = "select id_datanode_content,datanode_content_object,datanode_content_type, datanode_content_data from frbr_datanodes_content where datanode_content_num_datanode = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if($result && pmb_mysql_num_rows($result)){
				while ($ligne=pmb_mysql_fetch_object($result)) {
					switch ($ligne->datanode_content_type) {
						case "datasource":
							$this->datasource = array(
							    'id' => (int) $ligne->id_datanode_content,
								'name' => $ligne->datanode_content_object,
								'data' => json_decode($ligne->datanode_content_data)
							);
							break;
						case "filter":
							$this->filter = array(
							    'id' => (int) $ligne->id_datanode_content,
								'name' => $ligne->datanode_content_object,
								'data' => json_decode($ligne->datanode_content_data)
							);
							break;
						case "sort":
							$this->sort = array(
							    'id' => (int) $ligne->id_datanode_content,
								'name' => $ligne->datanode_content_object,
								'data' => json_decode($ligne->datanode_content_data)
							);
							break;
						case "children_filter":
							$this->children_filter = array(
							    'id' => (int) $ligne->id_datanode_content,
								'name' => $ligne->datanode_content_object,
								'data' => json_decode($ligne->datanode_content_data)
							);
							break;
					}
				}
			}
		}
	}

	protected function get_linked_form() {
		$num_parent = 0;
		if (isset($this->parent) && is_object($this->parent)) {
			$num_parent = $this->parent->get_id();
		}
		$form = "
				<input type='hidden' name='".$this->type."_num_page' id='".$this->type."_num_page' value='".$this->page->get_id()."'/>
				<div class='colonne3'>
					<label class='etiquette' for='".$this->type."_num_parent'>".$this->format_text($this->msg['frbr_entity_common_entity_'.$this->type.'_parent'])."</label>
				</div>
				<div class='colonne-suite'>";
		$form .= $this->get_parent_name_from_page($num_parent);
		if ($this->id) {
			$form .= "<input type='hidden' name='".$this->type."_num_parent' id='".$this->type."_num_parent' value='".$num_parent."'/>";
		}
		$form .="</div>";
		return $form;
	}

	protected function get_datasources_options_authperso($datasource_name, $selected_datasource) {
	    global $msg;
	    $options = "";
	    $query = "SELECT id_authperso, authperso_name FROM authperso";
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result)) {
            $options .= "<optgroup label='".$msg["authperso"]."'>";
	        while ($row = pmb_mysql_fetch_assoc($result)) {
	            $options .= "<option value='".$datasource_name."_".$row['id_authperso']."'".($datasource_name."_".$row['id_authperso'] == $selected_datasource ? " selected='selected'" : "").">".$row['authperso_name']."</option>";
	        }
	        $options .= "</optgroup>";
	    }
	    return $options;
	}

	protected function get_datasources_list_form($no_child = false){
		$datasources = array();
		if ($no_child) {
			//cas d'une instance qui nous sert juste à afficher ses datasources
			$datasources = $this->elements_used['datasource'] ?? [];
			//on réinitialise son entity_type pour ne pas afficher ses filtres et ses tris
			$this->entity_type = null;
		} else {
			$datasources = $this->elements_used['parent_datasource'];
		}
		if (is_countable($datasources) && count($datasources) > 1) {
			$form = "
			<div class='colonne3'>
				<label for='datanode_datasource_choice'>".$this->format_text($this->msg['frbr_entity_common_entity_datanode_datasource_choice'])."</label>
			</div>
			<div class='colonne-suite'>";
				if(!$this->have_child() || $no_child){
					$form.= "
				<select name='datanode_datasource_choice' id='datanode_datasource_choice' data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"change\", \"method\":\"frbrEntityLoadElemForm\", \"parameters\":{\"id\":\"0\", \"domId\":\"datasource_form\", \"numPage\":\"".$this->get_page()->get_id()."\",\"filterRefresh\":\"1\",\"sortRefresh\":\"1\"}}'>
						<option value='frbr_entity_common_datasource'>".$this->format_text($this->msg['frbr_entity_common_entity_datanode_datasource_choice'])."</option>";
					foreach($datasources as $datasource){
					    if (preg_match("#authperso$#", $datasource)) {
					        $selected_datasource = isset($this->datasource['name']) ? $this->datasource['name'] : "";
					        $form.= $this->get_datasources_options_authperso($datasource, $selected_datasource);
					    } else {
    						$form.= "
    						<option value='".$datasource."'".(isset($this->datasource['name']) && $datasource == $this->datasource['name'] && !$no_child ? " selected='selected'" : "").">".$this->format_text($this->msg[$datasource])."</option>";
					    }
					}
					$form.="
				</select>";
				}else{
				    $form.= $this->format_datasource_name($this->datasource['name'])."<input type='hidden' value='".$this->datasource['name']."' name='datanode_datasource_choice'/>";
				}
				$form.= "</div>";
		}else{
			$form = "
				<input type='hidden' name='datanode_datasource_choice' id='datanode_datasource_choice' value='".($datasources[0] ?? '')."'/>";
		}
		return $form;
	}

	protected  function format_datasource_name($name) {
	    return $this->format_text($this->msg[$name]);
	}

	protected function get_filters_list_form(){
		$form = "";
		$form.="
 			<hr/>
 			<div class='row'>
 				<div class='colonne3'>
 					<label>".$this->format_text($this->msg['frbr_entity_common_entity_datanode_filter_choice'])."</label>
 				</div>
 				<div class='colonne_suite' id='datasource_filters'>";

 			$form.= $this->get_filters_selector();
 			$form.=	"</div>
 			</div>
 			<div class='row' id='filter_form' dojoType='dijit/layout/ContentPane'></div>";
 			// TODO Vérifier s'il y a des jeux de données enfants
 			$children = $this->get_children();
 			if (count($children)) {
 		        $form.= "
     			<div class='row'>
                    <div class='colonne'>
                        <input type='radio' id='datanode_children_filter_operator_and' name='datanode_children_filter_operator' value='and' ".(empty($this->children_filter['data']->children_filter_operator) ? "checked" : ($this->children_filter['data']->children_filter_operator == "and" ? "checked" : ""))." />
                        <label for='datanode_children_filter_operator_and'>".$this->msg['frbr_entity_common_datanode_operator_and']."</label>
                        <input type='radio' id='datanode_children_filter_operator_or' name='datanode_children_filter_operator' value='or' ".( isset($this->children_filter['data']->children_filter_operator) && $this->children_filter['data']->children_filter_operator == "or" ? "checked" : "")."/>
                        <label for='datanode_children_filter_operator_or'>".$this->msg['frbr_entity_common_datanode_operator_or']."</label>
                    </div>
                    <br/>
     				<div class='colonne3'>
     					<label>".$this->format_text($this->msg['frbr_entity_common_entity_datanode_children_filter'])."</label>
     				</div>
     				<div class='colonne_suite' id='datasource_children_filters'>
     			        <ul>";
 		        foreach ($children as $child_id => $child_name) {
 		            $form.= "
 		                    <li>
 		                        ".$this->get_children_filters_operators($child_name, $child_id)."
 		                    </li>";
 		        }
 		        $form.= "
 		                </ul>
 		                 <input type='hidden' name='datanode_children_filter_id' value='".(!empty($this->children_filter) ? $this->children_filter['id'] : 0)."' />
     			    </div>
     			</div>";
 			}
 		    $form.= "
 			<div class='row'>&nbsp;</div>";
		return $form;
	}

	protected function get_sorting_list_form(){
		$form = "";
		$form.="
 			<hr/>
 			<div class='row'>
 				<div class='colonne3'>
 					<label>".$this->format_text($this->msg['frbr_entity_common_entity_datanode_sort_choice'])."</label>
 				</div>
 				<div class='colonne_suite' id='datasource_sort'>";
			$form.= $this->get_sort_selector();
 			$form.=	"</div>
 			</div>
 			<div class='row' id='sort_form' dojoType='dijit/layout/ContentPane'></div>
 			<div class='row'>&nbsp;</div>";
		return $form;
	}

	public function get_parameters_form($no_child = false) {
		$parameters_form = "
			<div class='row'>";
		$parameters_form .= $this->get_authperso_parameter_form();
		$parameters_form .= $this->get_datasources_list_form($no_child);
		$parameters_form .= "</div>";
		if((isset($this->datasource['id']) && $this->datasource['id']) || (isset($this->elements_used['datasource']) && count($this->elements_used['datasource'])==1)){
			if(isset($this->datasource['id']) && $this->datasource['id']){
				$datasource_name = $this->datasource['name'];
				$datasource_id = $this->datasource['id'];
			}else if(count($this->elements_used['datasource'])==1){
				$datasource_name = $this->elements_used['datasource'][0];
				$datasource_id = 0;
			}
			if(!$no_child){
				$parameters_form.="
				<script type='text/javascript'>
						require(['dojo/topic'],
						function(topic){
							topic.publish('ParametersFormsReady', 'frbrEntityLoadElemForm', {elem:'".$datasource_name."',id:'".$datasource_id."',domId:'datasource_form', numPage : '".$this->page->get_id()."'})
						});
				</script>";
			}
		}
		$parameters_form .= "
			<div id='datasource_form' data-dojo-type='dijit/layout/ContentPane'>
			</div>
			<div class='row'>";
		$parameters_form .= $this->get_filters_list_form();
		if(isset($this->filter['data']->id) && $this->filter['data']->id) {
			$parameters_form.="
				<script type='text/javascript'>
					require(['dojo/topic'],
					function(topic){
						topic.publish('ParametersFormsReady', 'frbrEntityLoadManagedElemForm', {elem:'frbr_entity_common_filter',selectedIndex:'filter".$this->filter['data']->id."',id: '".$this->filter['id']."', domId:'filter_form',numPage:'".$this->page->get_id()."'})
					});
				</script>";
		}
		$parameters_form .= "</div>";
		$parameters_form .=	"
			<div class='row'>";
		$parameters_form .= $this->get_sorting_list_form();
		if(isset($this->sort['data']->id) && $this->sort['data']->id) {
			$parameters_form.="
				<script type='text/javascript'>
					require(['dojo/topic'],
					function(topic){
						topic.publish('ParametersFormsReady', 'frbrEntityLoadManagedElemForm', {elem:'frbr_entity_common_sort',selectedIndex:'sort".$this->sort['data']->id."',id: '".$this->sort['id']."', domId:'sort_form',numPage:'".$this->page->get_id()."'})
					});
				</script>";
		}
		$parameters_form .= "</div>";
		$parameters_form .=	"
			<div class='row'>
			</div>";
		return $parameters_form;
	}

	/**
	 * Données provenant d'un formulaire
	 */
	public function set_properties_from_form() {
		global $datanode_name;
		global $datanode_comment;
		global $datanode_num_parent;
		global $datanode_num_page;
		global $datanode_entity_type;

		$this->name = stripslashes($datanode_name);
		$this->comment = stripslashes($datanode_comment);
		$this->entity_type = stripslashes($datanode_entity_type);
		$this->class_name = stripslashes("frbr_entity_".$datanode_entity_type."_datanode");
		$this->set_parent_from_num($datanode_num_parent);
		$this->set_page_from_num($datanode_num_page);
	}

	/**
	 * Formatage pour la sauvegarde dans la table '_content'
	 */
	public function save_content($type='') {
		$datanode_type = 'datanode_'.$type.'_choice';
		global ${$datanode_type};

		if(isset(${$datanode_type}) && isset($this->{$type}['name']) && ${$datanode_type} == $this->{$type}['name']){
			$type_id = $this->{$type}['id'];
		}else{
			$type_id = 0;
		}
		if(${$datanode_type}) {
			switch ($type) {
				case 'children_filter':
				    $type_instance = new frbr_entity_common_children_filter($type_id);
				    break;
				case 'filter':
					$type_instance = new frbr_entity_common_filter($type_id);
					break;
				case 'sort':
					$type_instance = new frbr_entity_common_sort($type_id);
					break;
				default:
				    if (preg_match("#authperso_([\d]+)$#", ${$datanode_type})) {
				        $authperso =  preg_split("#_([\d]+)#", ${$datanode_type}, 0 ,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
				        $type_instance = new $authperso[0]($type_id);
				        $type_instance->set_authperso_id($authperso[1]);
				        $type_instance->set_class_name(${$datanode_type});
				    } else {
				        $type_instance = new ${$datanode_type}($type_id);
				    }
					break;
			}
			$type_instance->set_num_datanode($this->id);
			$result = $type_instance->save_form();
			if($result) {
				$this->{$type} = array(
						'id' => $type_instance->id,
						'name' => ${$datanode_type}
				);
				return true;
			} else {
				return false;
			}
		} else {
			if($type != 'datasource') {
				if(!isset($this->{$type}['id'])) $this->{$type}['id'] = 0;
				if($this->{$type}['id']){
					$query = "delete from frbr_datanodes_content
						where id_datanode_content = '".($this->{$type}['id']*1)."'
						and datanode_content_type='".$type."'
						and datanode_content_num_datanode='".$this->id."'";
					pmb_mysql_query($query);
				}
			}
		}
		return false;
	}

	/**
	 * Sauvegarde
	 */
	public function save(){
		if($this->id) {
			$query = 'update frbr_datanodes set ';
			$where = 'where id_datanode= '.$this->id;
		} else {
			$query = 'insert into frbr_datanodes set ';
			$where = '';
		}
		$query .= '
				datanode_name = "'.addslashes($this->name).'",
				datanode_comment = "'.addslashes($this->comment).'",
				datanode_object = "'.addslashes($this->class_name).'",
				datanode_num_page = "'.$this->page->get_id().'",
				datanode_num_parent = "'.(is_object($this->parent) ? $this->parent->get_id() : 0).'"
				'.$where;
		$result = pmb_mysql_query($query);
		if($result) {
			if(!$this->id) {
				$this->id = pmb_mysql_insert_id();
			}
			//source de données
			$saved = $this->save_content('datasource');
			if($saved) {
 				//le filtre
				$saved = $this->save_content('filter');
				//le tri
				$saved = $this->save_content('sort');
				//le filtre en fonction des datanodes enfant
				$saved = $this->save_content('children_filter');
				return $this->id;
			}
		}
		return false;
	}

	/**
	 * Suppression
	 */
	public static function delete($id=0, $recursive = false){
		$id = intval($id);
		if($id) {
			if ($recursive) {
				//cadres
				$query = "SELECT id_cadre FROM frbr_cadres WHERE cadre_num_datanode = '".$id."'";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					while ($row = pmb_mysql_fetch_object($result)) {
						frbr_entity_common_entity_cadre::delete($row->id_cadre);
					}
				}
				//datanodes
				$query = "SELECT id_datanode FROM frbr_datanodes WHERE datanode_num_parent = '".$id."'";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					while ($row = pmb_mysql_fetch_object($result)) {
						frbr_entity_common_entity_datanode::delete($row->id_datanode, true);
					}
				}
			}
			$query = "DELETE FROM frbr_datanodes_content WHERE datanode_content_num_datanode = '".$id."'";
			$result = pmb_mysql_query($query);
			$query = "DELETE FROM frbr_datanodes WHERE id_datanode = '".$id."'";
			$result = pmb_mysql_query($query);
			return true;
		}
		return false;
	}

	public function get_id() {
		return $this->id;
	}

	public function get_name() {
		return $this->name;
	}

	public function get_page() {
		return $this->page;
	}

	public function get_type() {
		return $this->type;
	}

	public function get_entity_type() {
		return $this->entity_type;
	}

	public function get_parent() {
		return $this->parent;
	}

	public function set_parent_from_num($num_parent) {
		$num_parent = (int) $num_parent;
		if (!empty($num_parent)) {
			$class_name = frbr_entity_common_entity_datanode::get_class_name_from_id($num_parent);
			$this->parent = new $class_name($num_parent);
		}
		return $this;
	}

	public function have_child(){
		if(!isset($this->have_child) && $this->id){
			$query = 'select id_cadre from frbr_cadres where cadre_num_datanode = "'.$this->id.'"';
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$this->have_child = true;
				return $this->have_child;
			}
			$query = 'select id_datanode from frbr_datanodes where datanode_num_parent = "'.$this->id.'"';
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$this->have_child = true;
				return $this->have_child;
			}
			$this->have_child = false;
		}
		return $this->have_child;
	}

	public static function get_entity_type_from_object($object) {
		return str_replace(array("frbr_entity_","_datanode"), "", $object);
	}

	public static function get_entity_type_from_id($id_datanode) {
		$entity_type = '';
		$query = '	SELECT datanode_object
					FROM frbr_datanodes
					WHERE id_datanode = "'.$id_datanode.'"
				';
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row  =  pmb_mysql_fetch_object($result);
			$entity_type = static::get_entity_type_from_object($row->datanode_object);
		}
		return $entity_type;
	}

	public function get_parent_informations() {
		if (is_object($this->parent) && $this->parent->get_id()) {
			$this->elements_used["parent_datasource"] = $this->parent->elements_used["datasource"];
			$this->msg = array_merge($this->msg, $this->parent->msg);
			$this->parent_parameters = $this->parent->get_datasource()["data"];
		}else{ //Noeud directement sous la page donc
			$class_name = 'frbr_entity_'.$this->page->get_entity().'_datanode';
			$datanode = new $class_name();
			$this->elements_used["parent_datasource"] = $datanode->elements_used["datasource"];
			$this->msg = array_merge($this->msg, $datanode->msg);
			$this->parent_parameters = $this->page->get_parameters();
		}
	}

	public static function get_class_name_from_id($id_datanode) {
		$id_datanode = intval($id_datanode);
		$class_name = '';
		$query = '	SELECT datanode_object
					FROM frbr_datanodes
					WHERE id_datanode = "'.$id_datanode.'"
				';
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row  =  pmb_mysql_fetch_object($result);
			$class_name = $row->datanode_object;
		}else {
			$class_name = 'frbr_entity_common_entity_datanode';
		}
		return $class_name;
	}

	public function get_datasource() {
		return $this->datasource;
	}

	public function get_filter() {
		return $this->filter;
	}

	public function get_sort() {
		return $this->sort;
	}

	public function get_children_filter() {
		return $this->children_filter;
	}

	public function has_children_filter() {
	    if (count($this->get_children_filter())) {
	        return true;
	    }
	    return false;
	}

	public function get_children() {
		$children = array();
		if ($this->id) {
    		$query = 'select id_datanode, datanode_name from frbr_datanodes where datanode_num_parent = "'.$this->id.'"';
    		$result = pmb_mysql_query($query);
    		if(pmb_mysql_num_rows($result)){
    			while ($row = pmb_mysql_fetch_object($result)) {
    				$children[$row->id_datanode] = $row->datanode_name;
    			}
    		}
		}
		return $children;
	}

	public function get_filters_selector(){
		global $msg, $charset;
		$form = "";
		if($this->entity_type){
			$form .= "
 					<select id='datanode_filter_choice' name='datanode_filter_choice' data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"change\", \"method\":\"frbrEntityLoadManagedElemForm\", \"parameters\":{\"elem\":\"frbr_entity_common_filter\", \"id\":\"0\", \"domId\":\"filter_form\", \"numPage\":\"".$this->page->get_id()."\", \"className\" : \"".$this->class_name."\", \"indexation\" : ".encoding_normalize::json_encode($this->informations['indexation'])."}}'>
 						<option value=''>".$this->format_text($this->msg['frbr_entity_common_entity_datanode_filter_choice'])."</option>";
			if(isset($this->managed_datas['filters'])) {
				foreach($this->managed_datas['filters'] as $key => $infos) {
					$form.= "
						<option value='".$key."' ".(isset($this->filter['data']) && $key == "filter".$this->filter['data']->id ? "selected='selected'" : "").">".$infos['name']."</option>";
				}
			}
			$form.="
					</select>";
			$form.="<img src='".get_url_icon('add.png')."' alt='".$msg["925"]."' data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"click\", \"method\":\"loadDialog\", \"parameters\":{\"element\":\"filter\", \"idElement\":\"".$this->id."\", \"manageId\": 0, \"quoi\" : \"filters\", \"className\" : \"".$this->class_name."\"}}' title=\"".$this->format_text($this->msg['frbr_entity_common_entity_datanode_filter_create'])."\" />";
		} else {
			$form .= "<p>".htmlentities($msg['frbr_datasource_choice'], ENT_QUOTES, $charset)."</p>";
		}
		return $form;
	}

	public function get_sort_selector(){
		global $msg, $charset;
		$form = "";
		if($this->entity_type){
			$form .= "<select id='datanode_sort_choice' name='datanode_sort_choice' data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"change\", \"method\":\"frbrEntityLoadManagedElemForm\", \"parameters\":{\"elem\":\"frbr_entity_common_sort\", \"id\":\"0\", \"domId\":\"sort_form\", \"numPage\":\"".$this->page->get_id()."\", \"className\" : \"".$this->class_name."\", \"indexation\" : ".encoding_normalize::json_encode($this->informations['indexation'])."}}'>
 						<option value=''>".$this->format_text($this->msg['frbr_entity_common_entity_datanode_sort_choice'])."</option>
						<option value='sortRandom' ".$this->get_selected_sort("sortRandom").">".$this->format_text($this->msg['frbr_entity_common_entity_datanode_sort_random'])."</option>";

			if(isset($this->managed_datas['sorting'])) {
				foreach($this->managed_datas['sorting'] as $key => $infos) {
					$form.= "
			 			<option value='".$key."' ".$this->get_selected_sort($key).">".$infos['name']."</option>";

				}
			}
			$form.="
				</select>
			<img src='".get_url_icon('add.png')."' alt='".$msg["925"]."' data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"click\", \"method\":\"loadDialog\", \"parameters\":{\"element\":\"sort\", \"idElement\":\"".$this->id."\", \"manageId\": \"0\", \"quoi\" : \"sorting\", \"className\" : \"".$this->class_name."\"}}' title=\"".$this->format_text($this->msg['frbr_entity_common_entity_datanode_sort_create'])."\" />";
		} else {
			$form .= "<p>".htmlentities($msg['frbr_datasource_choice'], ENT_QUOTES, $charset)."</p>";
		}
		return $form;
	}

	private function get_selected_sort($key) {
	    if (isset($this->sort['data']) && $key == "sort".$this->sort['data']->id) {
	        return "selected='selected'";
	    }
	    return "";
	}

	public static function have_cadre_visible_in_graph($id) {
		$id = intval($id);
		if ($id) {
			$query = '	SELECT id_cadre
						FROM frbr_cadres
						WHERE cadre_num_datanode = "'.$id.'"
						AND cadre_visible_in_graph = 1';
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				return true;
			}
		}
		return false;
	}

	public function get_datanode_datas($data, $limit = false) {
		//cas particulier des autorites perso
	    if (preg_match("#authperso_([\d]+)$#", $this->get_datasource()['name'])) {
	        $authperso =  preg_split("#_([\d]+)#", $this->get_datasource()['name'], 0 ,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		    $this->datasource_instance = new $authperso[0]($this->get_datasource()['id']);
		    if (!empty($authperso[1])) {
		        $this->datasource_instance->set_authperso_id($authperso[1]);
		    }
		} else {
		    $datanode_datasource_class_name = $this->get_datasource()['name'];
		    $this->datasource_instance = new $datanode_datasource_class_name($this->get_datasource()['id']);
		}
		if (!empty($this->get_filter()['id'])) {
			$datanode_filter_class_name = $this->get_filter()['name'];
			$filter = new $datanode_filter_class_name($this->get_filter()['id']);
			$filter->set_indexation_type($this->informations['indexation']['type']);
			$filter->set_indexation_path($this->informations['indexation']['path']);
			$filter->set_indexation_sub_type($this->informations['indexation']['sub_type']);
			$filter->set_fields($this->managed_datas['filters']['filter'.$this->get_filter()['data']->id]['fields']);
			if (!isset($this->managed_datas['filters']['filter'.$this->get_filter()['data']->id]['details'])) {
			    $this->managed_datas['filters']['filter'.$this->get_filter()['data']->id]['details'] = [];
			}
			$filter->set_details($this->managed_datas['filters']['filter'.$this->get_filter()['data']->id]['details']);
			$this->datasource_instance->set_filter($filter);
		}
		if (!empty($this->get_sort()['id'])) {
			$datanode_sort_class_name = $this->get_sort()['name'];
			$sort = new $datanode_sort_class_name($this->get_sort()['id']);
			$sort->set_indexation_type($this->informations['indexation']['type']);
			$sort->set_indexation_path($this->informations['indexation']['path']);
			$sort->set_indexation_sub_type($this->informations['indexation']['sub_type']);
			if (isset($this->managed_datas['sorting']['sort'.$this->get_sort()['data']->id]['fields'])) {
				$sort->set_fields($this->managed_datas['sorting']['sort'.$this->get_sort()['data']->id]['fields']);
			}
			if (!isset($this->managed_datas['sorting']['sort'.$this->get_sort()['data']->id]['details'])) {
			    $this->managed_datas['sorting']['sort'.$this->get_sort()['data']->id]['details'] = [];
			}
			$sort->set_details($this->managed_datas['sorting']['sort'.$this->get_sort()['data']->id]['details']);
			$this->datasource_instance->set_sort($sort);
		}
		$this->datasource_instance->set_parent_type($this->get_parent_type());
		if (count($data)) {
		    $data = $this->datasource_instance->get_datas($data);
		}
		return $data;
	}

	public function sort_data($data) {
	    if (isset($this->datasource_instance)) {
	        $data = $this->datasource_instance->sort_datas($data);
	    }
	    return $data;
	}

	public function filter_data($data) {
	    if (isset($this->datasource_instance)) {
	        $data = $this->datasource_instance->filter_datas($data);
	    }
	    return $data;
	}

	/**
	 *
	 * @param int $id
	 * @return frbr_entity_common_entity_datanode|NULL
	 */
	public static function get_instance($id) {
		$id = intval($id);
	    if (isset(static::$datanode_instances[$id])) {
	        return static::$datanode_instances[$id];
	    }
		$query = '	SELECT datanode_object
					FROM frbr_datanodes
					WHERE id_datanode = "'.$id.'"
				';
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$instance_name = pmb_mysql_result($result,0,0);
			if (class_exists($instance_name)) {
				static::$datanode_instances[$id] = new $instance_name($id);
				return static::$datanode_instances[$id];
			}
		}
		return null;
	}

	public function get_path($path = 0) {
		if (!$path) {
			$path = $this->id;
		}
		$id = explode('/',$path)[0];
		$query = '	SELECT datanode_num_parent
					FROM frbr_datanodes
					WHERE id_datanode = "'.$id.'"';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$parent =  pmb_mysql_result($result,0,0);
			if ($parent) {
				$path = $parent.'/'.$path;
				return $this->get_path($path);
			}
		}
		return $path;
	}

	protected function get_children_filters_operators($child_name, $child_id) {
	    return "
	        <span>".$child_name."</span>
            <div>
    	        <input type='radio' id='datanode_children_filter_".$child_id."_not_used' name='datanode_children_filter_choice[".$child_id."]' ".((isset($this->children_filter['data']->children_filter->{$child_id}) && $this->children_filter['data']->children_filter->{$child_id} == 0) || (!isset($this->children_filter['data']->children_filter->{$child_id}))? "checked='checked'" : "")." value='0'>
    	        <label for='datanode_children_filter_".$child_id."_not_used'>".$this->format_text($this->msg['frbr_entity_common_entity_datanode_children_filter_not_used'])."</label>
    	        <input type='radio' id='datanode_children_filter_".$child_id."_not_empty' name='datanode_children_filter_choice[".$child_id."]' ".(isset($this->children_filter['data']->children_filter->{$child_id}) && $this->children_filter['data']->children_filter->{$child_id} == 1 ? "checked='checked'" : "")." value='1'>
    	        <label for='datanode_children_filter_".$child_id."_not_empty'>".$this->format_text($this->msg['frbr_entity_common_entity_datanode_children_filter_not_empty'])."</label>
    	        <input type='radio' id='datanode_children_filter_".$child_id."_empty' name='datanode_children_filter_choice[".$child_id."]' ".(isset($this->children_filter['data']->children_filter->{$child_id}) && $this->children_filter['data']->children_filter->{$child_id} == 2 ? "checked='checked'" : "")." value='2'>
    	        <label for='datanode_children_filter_".$child_id."_empty'>".$this->format_text($this->msg['frbr_entity_common_entity_datanode_children_filter_empty'])."</label>
	        </div>
	            ";
	}

	public function get_datasource_data($property='') {
	    $parameter = '';
	    if(isset($this->datasource['data']->$property)) {
	        $parameter = $this->datasource['data']->$property;
	    }
	    return $parameter;
	}

	public function get_parent_type(){
	    if(!empty($this->parent_type)){
	        return $this->parent_type;
	    }
	    $query = "select datanode_num_parent, page_entity from frbr_datanodes join frbr_pages on
				frbr_pages.id_page=frbr_datanodes.datanode_num_page where id_datanode = ".$this->id;
	    $result = pmb_mysql_query($query);
	    if(pmb_mysql_num_rows($result)){
	        $result = pmb_mysql_fetch_assoc($result);
	        if($result['datanode_num_parent'] != 0){
	            $this->parent_type = frbr_entity_common_entity_datanode::get_entity_type_from_id($result['datanode_num_parent']);
	            return $this->parent_type;
	        }
	        $this->parent_type = $result['page_entity'];
	    }
	    return $this->parent_type;
	}

	protected function get_authperso_parameter_form() {
	    $form = "";
	    if (!empty($this->parent_parameters)) {
	        $num_authperso = (!empty($this->parent_parameters->authperso_id) ? $this->parent_parameters->authperso_id : (!empty($this->parent_parameters->authperso->value) ? $this->parent_parameters->authperso->value : 0));
	        if ($num_authperso) {
	            $form .= "<input type='hidden' name='datanode_authperso_num' id='datanode_authperso_num' value='$num_authperso' />";
	        }
	    }
	    return $form;

	}
}