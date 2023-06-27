<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_form.class.php,v 1.26 2019-04-04 13:45:53 tsamson Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php"))
	die("no access");

require_once($class_path.'/onto/common/onto_common_datatype_marclist.class.php');
require_once($class_path.'/onto/common/onto_common_property.class.php');
require_once($include_path.'/templates/onto/common/onto_common_item.tpl.php');

// require_once ($include_path . '/templates/contribution_area/contribution_area.tpl.php');
require_once ($class_path . '/contribution_area/contribution_area.class.php');
require_once ($class_path . '/contribution_area/contribution_area_equation.class.php');
require_once ($class_path . '/encoding_normalize.class.php');
require_once ($class_path . '/onto/onto_parametres_perso.class.php');
require_once ($class_path . '/onto/onto_store_arc2_extended.class.php');
require_once ($class_path . '/contribution_area/contribution_area_store.class.php');
/**
 * class contribution_area_form
 * Représente un formulaire
 */
class contribution_area_form {
	protected $id=0;
	protected $type = "";
	protected $uri = "" ;
	protected $availableProperties = array();
	protected $name="";
	protected $comment="";
	protected $parameters;
	protected $unserialized_parameters = array();
	protected $classname = "";
	protected $active_properties;	
	protected static $contribution_area_form = array();
	protected $onto_class;
	protected $area_id;
	protected $form_uri;
	
	/**
	 * Formulaires liés à celui-ci
	 * @var array
	 */
	protected $linked_forms;
	
	public function __construct($type, $id=0, $area_id = 0, $form_uri = '')
	{
		$this->id = $id*1;
		$this->type = $type;
		$this->area_id = $area_id*1;
		if ($form_uri) {
			$this->form_uri = $form_uri;
		}
		$this->fetch_data();
	}
	
	protected function fetch_data()
	{
		if($this->id){
			$query = 'select * from contribution_area_forms where id_form = "'.$this->id.'"';
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$params = pmb_mysql_fetch_object($result);
				$this->parameters = $params->form_parameters;
				$this->unserialized_parameters = json_decode($this->parameters);
				$this->name = $params->form_title;
				$this->comment = $params->form_comment;
				$this->type = $params->form_type;
			}
		}
		$onto = contribution_area::get_ontology();
		$classes = $onto->get_classes();
		$class_uri = "";
		
		foreach($classes as $class){
			if($class->pmb_name == $this->type){
				$this->uri = $class->uri;
				$properties = $onto->get_class_properties($this->uri);
				for($i=0 ; $i<count($properties) ; $i++){
					$property = $onto->get_property($this->uri, $properties[$i]);
					$this->availableProperties[$property->pmb_name] = $property;
				}
				if (is_array($class->sub_class_of)) {
					foreach($class->sub_class_of as $parent_uri) {
						$properties = $onto->get_class_properties($parent_uri);
						for($i=0 ; $i<count($properties) ; $i++){
							$property = $onto->get_property($parent_uri, $properties[$i]);
							$this->availableProperties[$property->pmb_name] = $property;
						}
					}
				}
				break;
			}
		}
		
		ksort($this->availableProperties);
		$classes_array = $onto->get_classes_uri();
		$this->classname = isset($classes_array[$this->uri]) && isset($classes_array[$this->uri]->name) ? $classes_array[$this->uri]->name : '';
	}
	
	/**
	 * Renvoie le formulaire de paramétrage d'un formulaire
	 * @param int $area Identifiant de l'espace de provenance, pour pouvoir revenir au paramétrage à la sauvegarde
	 */
	public function get_form($area = 0)
	{
		global $msg;
		global $charset;
		global $ontology_tpl;
		global $type;
		global $current_module;
		/**
		 * TODO : Edition / Creation (msg & cie) 
		 */
		$html = '
		<form class="form-'.$current_module.'" name="contribution_area_form" method="post" id="contribution_area_form" action="./modelling.php?module=modelling&categ=contribution_area&sub=form&action=save&type='.$type.'&form_id='.$this->id.'">
		
		<div class="row">
		   <div class="left">
                <h3>'.$msg["admin_contribution_area_form_type"].' : '.$this->classname.'</h3>
           </div>';		
		if($this->id){
			$html.='
				<div class="right">
					<input type="button" class="bouton" name="grid_button" id="grid_button" value="'.htmlentities($msg['pricing_system_edit_grid'], ENT_QUOTES, $charset).'" onclick="window.location.href=\'./modelling.php?categ=contribution_area&sub=form&action=grid&form_id='.$this->id.'\'"/>
				</div>';
		}	
		$html.='
        </div>
		<div class="form-contenu">
		<input type="hidden" name="area" value="'.($area*1).'"/>
		<div class="row">
			<label>'.$msg['admin_contribution_area_form_name'].'</label>
        </div>
		<div class="row">
			<input type="text" name="form_name" value="'. htmlentities(($this->name ? $this->name : ''), ENT_QUOTES, $charset).'"/>
		</div>
		<div class="row">
			<label>'.$msg['admin_contribution_area_form_comment'].'</label>
		</div>
		<div class="row">
			<textarea cols="80" rows="2" name="form_comment" id="form_comment" wrap="virtual">' . ($this->comment ? $this->comment : '') . '</textarea>
		</div>
		<div class="row">&nbsp;</div>
		<table class="modern">
			<thead id="contribution_fixed_header">
				<th>'.$msg['admin_contribution_area_th_enabled'].'</th>
				<th>'.$msg['admin_contribution_area_form_fields'].'</th>
				<th>'.$msg['admin_contribution_area_form_displayed_label'].'</th>
				<th>'.$msg['admin_contribution_area_form_default_value'].'</th>
				<th>'.$msg['admin_contribution_area_form_needed_field'].'</th>
				<th>'.$msg['admin_contribution_area_form_hidden_field'].'</th>
				<th>'.$msg['admin_contribution_area_form_readonly'].'</th>
				<th>'.$msg['admin_contribution_area_form_advanced'].'</th>
			</thead>';
		
		$this->get_onto_class();
		
		uasort($this->availableProperties, array($this, 'sort_properties'));
		foreach($this->availableProperties as $key => $property){
						
// 			$onto_item = new onto_common_item($this->onto_class, $property->uri);
			
			$restriction = $this->onto_class->get_restriction($property->uri);
			$min = $restriction->get_min();
			
			$datatype_class_name = onto_common_item::search_datatype_class_name($property, $this->onto_class->pmb_name,$this->onto_class->onto_name);
			
			$datatype_ui_class_name = onto_common_item::search_datatype_ui_class_name($datatype_class_name, $this->onto_class->pmb_name, $property, $restriction,$this->onto_class->onto_name);
						
			/**
			 * recomposition d'un tableau datas ; contenant les instances de datatypes 
			 */
			$property_data = $this->recompose_data($property->pmb_name,$property->uri, $datatype_class_name);
			
			// On a besoin des valeurs disponibles dans les liste pour la génération du champ
			$property->pmb_extended['list_values'] = $this->get_saved_property($property->pmb_name, 'list_values');
			
			$html.='
			<tr>';
				if($min > 0){
					$html.='<td style="width:20px;">';
					$html.= '<input type="checkbox"  onclick="return false" checked="checked" id="switch_'.$property->pmb_name.'" name="'.$property->pmb_name.'[switch]" class="switch" />';
					$td_mandatory ='<input onclick="return false" type="checkbox" name="'.$property->pmb_name.'[mandatory]" checked="checked" value="1">';
				}else{
					$html.='<td style="width:20px;">';
					$html.= '<input type="checkbox" '.($this->get_saved_property($property->pmb_name) ? 'checked="checked"' : '').' id="switch_'.$property->pmb_name.'" name="'.$property->pmb_name.'[switch]" class="switch" />';
					$td_mandatory ='<input type="checkbox" id="'.$property->pmb_name.'_mandatory" name="'.$property->pmb_name.'[mandatory]" '.($this->get_saved_property($property->pmb_name,'mandatory') || $min>0 ? 'checked="checked"' : '').' value="1">';
				}
				
				$html.='<input type="hidden" name="inputs_name[]" value="'.$property->pmb_name.'"/>';
				
				$html.='<label for="switch_'.$property->pmb_name.'">'."&nbsp; ".'</label>
				</td>
				<td>
					<label class="switch-label">'.htmlentities($property->label,ENT_QUOTES, $charset).'</label>
					<span class="contribution_datatype_info"> ('.$property->get_pmb_datatype_label($property->pmb_datatype).')</span><br/>
					'.$this->get_equations_selector($this->get_equations_from_type($property->pmb_datatype, $property->range),$property->pmb_name,$this->get_saved_property($property->pmb_name,'equation')).'
				</td>				
				<td>
					<input type="text" id="'.$property->pmb_name.'_label" name="'.$property->pmb_name.'[label]" value="'.htmlentities(($this->get_saved_property($property->pmb_name,'label')?pmb_utf8_array_decode($this->get_saved_property($property->pmb_name,'label')): $property->label ),ENT_QUOTES, $charset).'"/>
				</td>
				<td>
					<div>
						'.$datatype_ui_class_name::get_form('',$property,$this->onto_class->get_restriction($property->uri),$property_data, $this->onto_class->pmb_name,'').'
						<input type="hidden" name="'.$property->pmb_name.'[default_value]" value="'.$this->onto_class->pmb_name.'_'.$property->pmb_name.'"/>
					</div>
				</td>
				<td>'.$td_mandatory.'</td>
				<td>
					<input type="checkbox" name="'.$property->pmb_name.'[hidden]" '.($this->get_saved_property($property->pmb_name,'hidden') ? 'checked="checked"' : '').' value="1">
				</td>
				<td>
					<input type="checkbox" name="'.$property->pmb_name.'[readonly]" '.($this->get_saved_property($property->pmb_name,'readonly') ? 'checked="checked"' : '').' value="1">
				</td>
				<td>
					<img class="icon contribution_area_form_advanced_button" alt="'.$msg['admin_contribution_area_form_advanced'].'" src="./images/b_edit.png" style="cursor:pointer;" ontoClass="'.$this->onto_class->pmb_name.'" property="'.$property->pmb_name.'" propertyDatatypeUI="'.$datatype_ui_class_name.'">
					<input type="hidden" name="'.$property->pmb_name.'[comment]" id="'.$property->pmb_name.'_comment" value="'.htmlentities($this->get_saved_property($property->pmb_name, 'comment'), ENT_QUOTES, $charset).'"/>
					<input type="hidden" name="'.$property->pmb_name.'[tooltip]" id="'.$property->pmb_name.'_tooltip" value="'.htmlentities($this->get_saved_property($property->pmb_name, 'tooltip'), ENT_QUOTES, $charset).'"/>
					<input type="hidden" name="'.$property->pmb_name.'[placeholder]" id="'.$property->pmb_name.'_placeholder" value="'.htmlentities($this->get_saved_property($property->pmb_name, 'placeholder'), ENT_QUOTES, $charset).'"/>
					<input type="hidden" name="'.$property->pmb_name.'[list_values]" id="'.$property->pmb_name.'_list_values" value="'.htmlentities($this->get_saved_property($property->pmb_name, 'list_values'), ENT_QUOTES, $charset).'"/>
				</td>';
			$html.='
			</tr>';
		}

		$html = str_replace('!!onto_form_name!!', 'contribution_area_form', $html);
		
		$html.= '
		</table>
		</div>
		<div class="row">&nbsp;</div>
		<div class="row">
			<div class="left">
				<input type="button" class="bouton" value="'.$msg['77'].'" onClick="if (test_form(this.form)) this.form.submit();"/>';
		if ($this->id) {
			$html.= '
				<input type="button" class="bouton" value="'.$msg['duplicate'].'" onClick="if (test_form(this.form)) {document.location = \'./modelling.php?categ=contribution_area&sub=form&type='.$this->type.'&action=duplicate&form_id='.$this->id.'\'}"/>';
		}
		$html.= '
			</div>';
		if ($this->id) {
			$html.= '
			<div class="right">
				<input class="bouton" type="button" value="'.$msg['supprimer'].'" onClick="confirmation_delete('.$this->id.',\''.$this->name.'\')" />			
			</div>';
			$html .= confirmation_delete("./modelling.php?module=modelling&categ=contribution_area&sub=form&action=delete&type=".$type."&form_id=");
		}	
		$html.= '
		</div>
		<style type="text/css">
			#contribution_fixed_header { 
			    position: relative;
				z-index: 1;
			}
		</style>
		<script type="text/javascript">		
			function test_form(form) {
				if(form.form_name.value.replace(/^\s+|\s+$/g, "").length == 0)	{
					alert("'.$msg["admin_contribution_area_form_name_empty"].'");
					return false;
				}
				return true;
			}
							
			function onto_open_selector(element_name, selector_url) {
				try {
					var element = encodeURIComponent(element_name);		
					var order =  document.getElementById(element_name + "_new_order").value;
					var deb_rech = document.getElementById(element_name + "_" + order + "_display_label").value;
					openPopUp(selector_url + "contribution_area_form&p1=" + element + "_" + order + "_value&p2=" + element + "_" + order + "_display_label&deb_rech=" + encodeURIComponent(deb_rech), "select_object", 500, 400, 0, 0, "infobar=no, status=no, scrollbars=yes, toolbar=no, menubar=no, dependent=yes, resizable=yes");
					return false;
				} catch(e){
					console.log(e);
				}
			}
					    
			function onto_remove_selector_value(element_name,element_order){
        		document.getElementById(element_name+"_"+element_order+"_value").value = "";
        		document.getElementById(element_name+"_"+element_order+"_type").value = "";
        		document.getElementById(element_name+"_"+element_order+"_display_label").value = "";
        	}
		</script>
		</form>
		
		<div data-dojo-type="apps/pmb/PMBDialog" id="contribution_area_form_advanced_dialog">
			<form class="form-admin" name="contribution_area_form_advanced" method="post" id="contribution_area_form_advanced" action="#">
				<input type="hidden" id="admin_contribution_area_form_advanced_onto_class"/>
				<input type="hidden" id="admin_contribution_area_form_advanced_property_name"/>
				<div class="row">
					<label class="etiquette" for="admin_contribution_area_form_comment">'.$msg['admin_contribution_area_form_comment'].'</label>
				</div>
				<div class="row">
					<textarea id="admin_contribution_area_form_comment"></textarea>
				</div>
				<div class="row">
					<label class="etiquette" for="admin_contribution_area_form_tooltip">'.$msg['admin_contribution_area_form_tooltip'].'</label>
				</div>
				<div class="row">
					<textarea id="admin_contribution_area_form_tooltip"></textarea>
				</div>
				<div id="admin_contribution_area_form_placeholder_block">
					<div class="row">
						<label class="etiquette" for="admin_contribution_area_form_placeholder">'.$msg['admin_contribution_area_form_placeholder'].'</label>
					</div>
					<div class="row">
						<input type="text" id="admin_contribution_area_form_placeholder"/>
					</div>
				</div>
				<div id="admin_contribution_area_form_list_values_block">
					<div class="row">
						<label class="etiquette" for="admin_contribution_area_form_list_values">'.$msg['admin_contribution_area_form_list_values'].'</label>
					</div>
					<div class="row">
						<select id="admin_contribution_area_form_list_values" multiple="multiple" style="min-width:100px;"></select>
					</div>
				</div>
				<div class="row">
					<input type="button" id="admin_contribution_area_form_submit" class="bouton" value="'.$msg['77'].'"/>
				</div>
			</form>
		</div>
		
		<script type="text/javascript">
			require([
					"dojo/dom-style",
					"dojo/dom",
					"dojo/dom-geometry",
					"dojo/on",
					"dojo/query",
					"apps/pmb/PMBDialog",
					"dijit/registry",
					"dojo/dom-attr",
					"dojo/dom-construct"
			], function(domStyle, dom, domGeometry, on, query, Dialog, registry, domAttr, domConstruct) {
 				var header = dom.byId("contribution_fixed_header");
				var offset = domGeometry.position(header).y + domGeometry.docScroll().y;
				on(window, "scroll", function(e){
					if(e.pageY > offset){
						domStyle.set(header, "top", e.pageY - (offset) +  "px");
					}else{
						domStyle.set(header, "top", "0px");
					}
				});
				
				var openContributionAreaFormAdvancedForm = function(e) {
					var ontoClass = domAttr.get(e.target, "ontoClass");
					var propertyName = domAttr.get(e.target, "property");
					var propertyDatatypeUI = domAttr.get(e.target, "propertyDatatypeUI");
					var dialog = registry.byId("contribution_area_form_advanced_dialog");
					dialog.set("title", dom.byId(propertyName + "_label").value);
					dom.byId("admin_contribution_area_form_advanced_onto_class").value = ontoClass;
					dom.byId("admin_contribution_area_form_advanced_property_name").value = propertyName;
					dom.byId("admin_contribution_area_form_comment").value = dom.byId(propertyName + "_comment").value;
					dom.byId("admin_contribution_area_form_tooltip").value = dom.byId(propertyName + "_tooltip").value;
					dom.byId("admin_contribution_area_form_placeholder").value = dom.byId(propertyName + "_placeholder").value;
					if (propertyDatatypeUI.indexOf("text") == -1) {
						domStyle.set("admin_contribution_area_form_placeholder_block", "display", "none");
					} else {
						domStyle.set("admin_contribution_area_form_placeholder_block", "display", "");
					}

					// Valeurs à afficher dans les datatypes liste
					var list_values_selector = dom.byId("admin_contribution_area_form_list_values");
					domConstruct.empty(list_values_selector);
					if (propertyDatatypeUI.indexOf("list") != -1) {
						var list_values = dom.byId(propertyName + "_list_values").value.split(",");
						domStyle.set("admin_contribution_area_form_list_values_block", "display", "");
						var selected = false;
						query("option", ontoClass + "_" + propertyName + "_0_value").forEach(function(node) {
							selected = false;
							if ((list_values[0] === "") || (list_values.indexOf(node.value) != -1)) {
								selected = true;
							}
							domConstruct.create("option", {value: node.value, innerHTML: node.innerHTML, selected: selected}, list_values_selector);
						});
					} else {
						domStyle.set("admin_contribution_area_form_list_values_block", "display", "none");
					}

					dialog.show();
				}
				
				query(".contribution_area_form_advanced_button").forEach(function(node) {
					on(node, "click", openContributionAreaFormAdvancedForm);
				});
				
				var saveContributionAreaFormAdvancedForm = function() {
					var ontoClass = dom.byId("admin_contribution_area_form_advanced_onto_class").value;
					var propertyName = dom.byId("admin_contribution_area_form_advanced_property_name").value;
					dom.byId(propertyName + "_comment").value = dom.byId("admin_contribution_area_form_comment").value;
					dom.byId(propertyName + "_tooltip").value = dom.byId("admin_contribution_area_form_tooltip").value;
					dom.byId(propertyName + "_placeholder").value = dom.byId("admin_contribution_area_form_placeholder").value;
					var list_values = "";
					query("option:checked", "admin_contribution_area_form_list_values").forEach(function(node) {
						if (list_values) {
							list_values = list_values + ",";
						}
						list_values = list_values + node.value;
					});
					dom.byId(propertyName + "_list_values").value = list_values;

					list_values = list_values.split(",");
					query("option", ontoClass + "_" + propertyName + "_0_value").forEach(function(node) {
						if ((list_values[0] === "") || (list_values.indexOf(node.value) != -1)) {
							domStyle.set(node, "display", "");
						} else {
							domStyle.set(node, "display", "none");
						}
					});

					registry.byId("contribution_area_form_advanced_dialog").hide();
				}
				
				on(dom.byId("admin_contribution_area_form_submit"), "click", saveContributionAreaFormAdvancedForm);
			});
		</script>
		';
		return $html;
	}
	
	public function set_from_form()	
	{
		global $inputs_name;
		global $charset;
		global $form_name;
		global $form_comment;
		
		$properties_list = array();
		for($i = 0 ; $i < count($inputs_name); $i++){
			$property_name = $inputs_name[$i];
			global ${$property_name};
			$property = ${$property_name};
			
			if (is_array($property) && isset($property['switch']) && ($property['switch'] == 'on')) {
				$properties_list[$property_name] = stripslashes_array($property);
				
				// On gère les traitements particuliers
				if (!empty($property['default_value'])) {
					$var_name = $property['default_value'];
					global ${$var_name};
					$default_value = stripslashes_array(${$var_name});
					if (!isset($default_value[0])) {
						// Ce n'est pas un tableau numériques, on ne sait pas quoi en faire...
						$default_value = array(array('value' => ''));
					}
					for($j = 0; $j < count($default_value); $j++) {
						if ($default_value[$j]['type'] == "merge_properties") {
							global ${$default_value[$j]['value']};
							$sub_properties = array();
							foreach(${$default_value[$j]['value']}  as $key => $value) {
								global ${$value};
								if (isset(${$value}) && ${$value}) {
									$sub_properties[$key] = ${$value};
								}
							}
							$default_value[$j]['value'] = $sub_properties;
						}
					}
					$properties_list[$property_name]['default_value'] = $default_value;
				}
			}
		}
		$this->parameters = encoding_normalize::json_encode($properties_list);
			
		$this->name = stripslashes($form_name);
		$this->comment = stripslashes($form_comment);
	}
	
	public function save($ajax_mode=false) {
		global $thesaurus_ontology_filemtime;
		
		$query = 'insert into ';
		$where = '';
		if($this->id){
			$query = 'update ';
			$where = ' where id_form = "'.$this->id.'"';
		}
		$query.='contribution_area_forms set ';
		$query.='form_title="'.addslashes($this->name).'",';
		$query.='form_comment="'.addslashes($this->comment).'",';
		$query.='form_parameters="'.addslashes($this->parameters).'",';
		$query.='form_type="'.addslashes($this->type).'"';
		
		$result = pmb_mysql_query($query.$where);
		if(!$this->id){
			$this->id = pmb_mysql_insert_id();
		}
		
		$tab_file_rdf = unserialize($thesaurus_ontology_filemtime);
		$tab_file_rdf['onto_contribution_form_' . $this->id] = 0;
		
		$query='UPDATE parametres SET valeur_param="'.addslashes(serialize($tab_file_rdf)).'" WHERE type_param="thesaurus" AND sstype_param="ontology_filemtime"';
		pmb_mysql_query($query);
		
		if(!$ajax_mode){
			return true;
		}
		
		
		$form = array(
				'type' => "form",
				'form_id' => $this->id,
				'parent_type' => $this->type,
		        'name' => $this->name,
		        'comment' => $this->comment,
		);
		return $form;
	}
	
	protected function get_saved_property($property,$sub_property = ''){
		
		if ($sub_property) {
			if(isset($this->unserialized_parameters->$property->$sub_property)){
				return $this->unserialized_parameters->$property->$sub_property; 	
			}
		} else {
			if(isset($this->unserialized_parameters->$property)){
				return $this->unserialized_parameters->$property;
			}
		}
		return '';
	}
	
	public function delete($ajax_mode = false){
		global $thesaurus_ontology_filemtime;
		
		/**
		 * TODO: Vérification de l'utilisation dans les scénarios. 
		 */
		$success = false;
		$query = 'delete from contribution_area_forms where id_form = "'.$this->id.'"';
		$result = pmb_mysql_query($query);

		$tab_file_rdf = unserialize($thesaurus_ontology_filemtime);
		unset($tab_file_rdf['onto_contribution_form_' . $this->id]);
		
		$query='UPDATE parametres SET valeur_param="'.addslashes(serialize($tab_file_rdf)).'" WHERE type_param="thesaurus" AND sstype_param="ontology_filemtime"';
		pmb_mysql_query($query);
		
		if($result){
			$success = true;
		}
		if($ajax_mode){
			return (array('form_id'=> $this->id, 'success'=>$success));
		}
		return $success;
	}	
	
	/**
	 * Fonction permettant d'émuler une partie du framework 
	 * @param array $params tableau des paramètres sérialisés du formulaire
	 * @param string $property_uri uri de la propriété que l'on veux valoriser
	 * @return array Soit un array contenant des instance de datatype, soit un array vide
	 */
	protected function recompose_data($property_name, $property_uri, $datatype_class_name){
		$values = array();	
		if(isset($this->unserialized_parameters->$property_name)) {
			foreach($this->unserialized_parameters->$property_name as $key => $value){
				if($key == "default_value" && is_array($value)){
					$value_properties =  array();
					foreach($value as $val){
						if (!empty($val->lang)) {
							$value_properties["lang"] = $val->lang; 							
						}
						if (!empty($val->display_label)) {
							$value_properties["display_label"] = stripslashes(encoding_normalize::utf8_decode($val->display_label)); 							
						}
						if (empty($val->type)) {
						    $val->type = "http://www.w3.org/2000/01/rdf-schema#Literal";
						}
						
						if ($datatype_class_name == 'onto_contribution_datatype_merge_properties') {
							$onto = contribution_area::get_ontology();
							$merge_properties = $onto->get_property($this->uri, $property_uri);
							
							foreach($onto->get_class_properties($merge_properties->range[0]) as $uri_sub_property){
								$property = $onto->get_property($merge_properties->range[0], $uri_sub_property);
								$sub_datatype_class_name = onto_common_item::search_datatype_class_name($property, $merge_properties->pmb_name,$merge_properties->onto_name);
								if (is_object($val->value)) {									
									$sub_property = $val->value->{$property->pmb_name}[0];
									$sub_value_properties = array();
									if ($sub_property->display_label) {
										$sub_value_properties["display_label"] = stripslashes(encoding_normalize::utf8_decode($sub_property->display_label)); 
									} 
									$val->value->{$property->pmb_name} = array();
									$val->value->{$property->pmb_name}[0] = new $sub_datatype_class_name ($sub_property->value, $sub_property->type, $sub_value_properties,'');
								}
							}
						}						
						$values[] = new $datatype_class_name($val->value, $val->type, $value_properties,'');
					}
				}
			}
		}
		return $values;
	}
	
	/**
	 * Méthode originaire de la classe opac permettant de constituer un tableau des propriétés actives.
	 * @return multitype:
	 */
	public function get_active_properties() {
		if (isset($this->active_properties)) {
			return $this->active_properties;
		}
		$this->active_properties = array();
		if ($this->unserialized_parameters) {
			foreach($this->unserialized_parameters as $key => $param){
				$uri = $this->availableProperties[$key]->uri;
				$this->active_properties[$uri] = new stdClass();
				$this->active_properties[$uri] = $this->unserialized_parameters->$key;
		
				$tab_default_value = $this->unserialized_parameters->$key->default_value;
		
				//on uniformise toutes les valeurs sous forme de tableau
				for ($j = 0; $j < count($tab_default_value); $j++) {
					if (!is_array($tab_default_value[$j]->value)) {
						$tab_default_value[$j]->value = array($tab_default_value[$j]->value);
					}
				}
				$this->active_properties[$uri]->default_value = $tab_default_value;
			}
		}
		return $this->active_properties;
	}
	
	public function get_uri(){
		return $this->uri;
	}
	
	public function get_equations_from_type($datatype, $range) {
		
		$equations = array();
		
		if ($datatype == "http://www.pmbservices.fr/ontology#resource_selector") {
			if($range[0]) {
				$type =  explode('#',$range[0])[1];

				switch ($type) {
					case "linked_record":
					case "record":
						$type = "record";
						break;
					case "responsability":
						$type = "author";
						break;
				}
				$equations = contribution_area_equation::get_list_by_type($type);				
			}
		}
		return $equations;
	}
	
	public function get_equations_selector ($equations, $name, $selected = 0) {
		global $msg;
		
		if (!count($equations)) {
			return "";
		}	
		$selector = "<select name='".$name."[equation]'>";
		$selector .= "<option value='0' >".$msg['admin_contribution_area_form_select_equation']."</option>";
		foreach ($equations as $id => $equation) {
			$selector .= "<option value='". $id ."' ". ($selected == $id ? "selected='selected'" : "") .">".$equation['name']."</option>";
		}
		$selector .= "</select>";
		
		return $selector;
	}
	
	public function render(){
		global $base_path, $nb_per_page_gestion, $class_path;
		$onto_store_config = array(
				/* db */
				'db_name' => DATA_BASE,
				'db_user' => USER_NAME,
				'db_pwd' => USER_PASS,
				'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'onto_contribution_form_' . $this->id,
				/* stop after 100 errors */
				'max_errors' => 100,
				'store_strip_mb_comp_str' => 0,
				'params' => $this->get_active_properties()
		);
		$data_store_config = array(
				/* db */
				'db_name' => DATA_BASE,
				'db_user' => USER_NAME,
				'db_pwd' => USER_PASS,
				'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'contribution_area_datastore',
				/* stop after 100 errors */
				'max_errors' => 100,
				'store_strip_mb_comp_str' => 0
		);
		
		$tab_namespaces = array(
				"skos"	=> "http://www.w3.org/2004/02/skos/core#",
				"dc"	=> "http://purl.org/dc/elements/1.1",
				"dct"	=> "http://purl.org/dc/terms/",
				"owl"	=> "http://www.w3.org/2002/07/owl#",
				"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
				"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
				"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
				"pmb"	=> "http://www.pmbservices.fr/ontology#"
		);
		
		$params = new onto_param(array(
				'base_resource' => 'modelling.php',
				'lvl' => 'contribution_area',
				'sub' => 'form',
				'type' => $this->type,
				'action' => 'edit',
				'page' => '1',
				'nb_per_page' => $nb_per_page_gestion,
				'id' => '0',
				'area_id' => (isset($this->area_id) ? $this->area_id : 0),
				'parent_id' => '',
				'form_id' => (isset($this->id) ? $this->id : 0),
				'form_uri' => (isset($this->form_uri) ? $this->form_uri : ''),
				'item_uri' => ''
		));

		$onto_store = new onto_store_arc2_extended($onto_store_config);
		$onto_store->set_namespaces($tab_namespaces);
			
		//chargement de l'ontologie dans son store
		$reset = $onto_store->load($class_path."/rdf/ontologies_pmb_entities.rdf", onto_parametres_perso::is_modified());
		onto_parametres_perso::load_in_store($onto_store, $reset);
		
		$onto_ui = new onto_ui("", $onto_store, array(), "arc2", $data_store_config,$tab_namespaces,'http://www.w3.org/2000/01/rdf-schema#label',$params);
		return $onto_ui->proceed();
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function get_comment() {
	    return $this->comment;
	}
	
	/**
	 * Retourne le script de redirection post sauvegarde ou suppression
	 * @param number $area Identifiant de l'espace vers lequel faire une redirection
	 */
	public function get_redirection($area = 0) {
		global $base_path;
		$location = $base_path.'/modelling.php?categ=contribution_area&sub=form';
		if ($area*1) {
			$location = $base_path.'/modelling.php?categ=contribution_area&sub=area&action=define&id='.$area;
		}
		return '
				<script type="text/javascript">
					document.location = "'.$location.'";
				</script>';
	}
	
	public static function get_contribution_area_form($type, $id=0, $area_id = 0, $form_uri = '') {
		if (!isset(self::$contribution_area_form[$type])) {
			self::$contribution_area_form[$type] = array();
		}
		$key = '';
		if ($id*1) {
			$key = $id;
			$key.= ($area_id ? '_'.$area_id : '');
		}
		if (!$key) {
			return new contribution_area_form($type, $id, $area_id, $form_uri);
		}
		if (!isset(self::$contribution_area_form[$type][$key])) {
			self::$contribution_area_form[$type][$key] = new contribution_area_form($type, $id, $area_id, $form_uri);
		}
		return self::$contribution_area_form[$type][$key];
	}
	
	public function sort_properties($a, $b){
		$asavedprop = $this->get_saved_property($a->pmb_name);
		$bsavedprop = $this->get_saved_property($b->pmb_name);
		$restriction = $this->onto_class->get_restriction($a->uri);
		$amin = $restriction->get_min();
		$restriction = $this->onto_class->get_restriction($b->uri);
		$bmin = $restriction->get_min();
		
		if(($asavedprop || ($amin > 0)) && !($bsavedprop || ($bmin > 0))){
			return -1;
		}
		if(!($asavedprop || ($amin > 0)) && ($bsavedprop || ($bmin > 0))){
			return 1;
		}
		if(strtolower(convert_diacrit($a->label)) < strtolower(convert_diacrit($b->label))) {
			return -1;
		}
		return 1;
	}
	
	public function get_onto_class() {
		if (isset($this->onto_class)) {
			return $this->onto_class;
		}
		$ontology = contribution_area::get_ontology();
		$this->onto_class = $ontology->get_class($this->uri);
		return $this->onto_class;
	}
	
	public function get_linked_forms () {
		if (isset($this->linked_forms)) {
			return $this->linked_forms;
		}
		$contribution_area_store  = new contribution_area_store();
		$complete_form_uri = $contribution_area_store->get_uri_from_id($this->form_uri);
		$graph_store_datas = $contribution_area_store->get_attachment_detail($complete_form_uri, 'http://www.pmbservices.fr/ca/Area#'.$this->area_id,'','',1);
	
		$this->linked_forms = array();
		for ($i = 0 ; $i < count($graph_store_datas); $i++) {
			if ($graph_store_datas[$i]['type'] == "form") {
				$graph_store_datas[$i]['area_id'] = $this->area_id;
				$this->linked_forms[] = $graph_store_datas[$i];
			} else {
				$data_form = $contribution_area_store->get_attachment_detail($graph_store_datas[$i]['uri'], 'http://www.pmbservices.fr/ca/Area#'.$this->area_id,'','',1);
				for ($j = 0 ; $j < count($data_form); $j++) {
					if ($data_form[$j]['type'] == "form") {
						$data_form[$j]['area_id'] = $this->area_id;
						$data_form[$j]['propertyPmbName'] = $graph_store_datas[$i]['propertyPmbName'];
						if ($graph_store_datas[$i]['type'] == "scenario") {
							$data_form[$j]['scenarioUri'] = $graph_store_datas[$i]['uri'];
						}
						$this->linked_forms[] = $data_form[$j];
					}
				}
			}
		}
		return $this->linked_forms;
	}
	
	/**
	 * Renvoie le formulaire de duplication
	 * @param int $area
	 * @return string
	 */
	public function get_duplication_form($area = 0) {
		// On vide l'id, le nom et on affiche le formulaire.
		$this->id = 0;
		$this->name = '';
		$this->comment = '';
		return $this->get_form($area);
	}
	
	/**
	 * Renvoie l'identifiant
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}
}