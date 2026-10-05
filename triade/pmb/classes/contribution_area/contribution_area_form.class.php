<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_form.class.php,v 1.74 2024/03/13 11:21:03 tsamson Exp $

if (stristr($_SERVER ['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;

require_once "$class_path/onto/common/onto_common_datatype_marclist.class.php";
require_once "$class_path/onto/common/onto_common_property.class.php";
require_once "$include_path/templates/onto/common/onto_common_item.tpl.php";
require_once "$class_path/contribution_area/contribution_area.class.php";
require_once "$class_path/contribution_area/contribution_area_equation.class.php";
require_once "$class_path/encoding_normalize.class.php";
require_once "$class_path/onto/onto_parametres_perso.class.php";
require_once "$class_path/onto/onto_store_arc2_extended.class.php";
require_once "$class_path/contribution_area/contribution_area_store.class.php";

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
	protected $origin_id = 0;
	
	/**
	 * Champs du formulaire qui ne peuvent pas être désactivé
	 * Mettre le pmb_name du champ dans le tableau
	 * @var array
	 */
	private const CANNOT_BE_HIDDEN = array(
	    "docnum_file",
	    "thumbnail"
	);
	
	/**
	 * Formulaires liés à celui-ci
	 * @var array
	 */
	protected $linked_forms;
	
	public function __construct($type, $id = 0, $area_id = 0, $form_uri = '')
	{
	    $this->id = intval($id);
		$this->type = $type;
		$this->area_id = intval($area_id);
		if ($form_uri) {
			$this->form_uri = $form_uri;
		}
		$this->fetch_data();
	}
	
	protected function fetch_data()
	{
	    global $msg;
	    
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
				    if (!$property->is_undisplayed()) {
				        $this->availableProperties[$property->pmb_name] = $property;
				    }
				}
				if (is_array($class->sub_class_of)) {
					foreach($class->sub_class_of as $parent_uri) {
						$properties = $onto->get_class_properties($parent_uri);
						for($i=0 ; $i<count($properties) ; $i++){
							$property = $onto->get_property($parent_uri, $properties[$i]);
						    if (!$property->is_undisplayed()) {
                                $this->availableProperties[$property->pmb_name] = $property;
						    }
						}
					}
				}
				break;
			}
		}
		
		ksort($this->availableProperties);
		$classes_array = $onto->get_classes_uri();
		
		$classname = "";
		if (!empty($classes_array[$this->uri]) && !empty($classes_array[$this->uri]->label)) {
		    $classname = $classes_array[$this->uri]->label;
		    if (substr($classname,0,4) == "msg:") {
		        if (isset($msg[substr($classname,4)])) {
		            $classname = $msg[substr($classname,4)];
		        } else {
		            // si on trouve pas le message on met juste le code dans le label
		            $classname = substr($classname,4);
		        }
		    }
		}
		$this->classname = $classname;
	}
	
	/**
	 * Renvoie le formulaire de paramétrage d'un formulaire
	 * @param int $area Identifiant de l'espace de provenance, pour pouvoir revenir au paramétrage à la sauvegarde
	 */
	public function get_form($area = 0, $is_duplicate = false)
	{
		global $msg;
		global $charset;
		global $ontology_tpl;
		global $type;
		global $current_module;
		
		/**
		 * TODO : Edition / Creation (msg & cie) 
		 */
		
		$hasDraft = self::has_draft_contribution_from_id($this->id);
		$hasScenario = $this->get_scenario_linked($this->id)["count"];
		
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
            <div>';
		
		if ($hasDraft){
            $html.='        
                 <div class="row"><div class="erreur">'.$msg["contribution_has_draft"].'</div></div>';
		}
		if ($hasScenario) {
		    $html.='
                 <div class="row"><div class="erreur">'.$msg["contribution_disabled_delete"].'</div></div>';
		}

        $html.='
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
        		</div>';
		if ($is_duplicate) {
		    $html .= '
                <div class="row">
                    <label>'.$msg['admin_contribution_area_form_duplicate_grid'].'</label>
        			<input type="checkbox" name="duplicate_grid" value="1" checked />
        			<input type="hidden" name="duplicate_grid_origin_id" value="'. $this->origin_id .'"/>
        		</div>
            ';
		}
        
        		//Récupèrer la liste des scénario et des espaces ou le formualire est utilisé
        		$list_scenario_linked = array();
                $list_scenario_linked = $this->get_scenario_linked($this->id);
                
                if (!empty($list_scenario_linked) && $list_scenario_linked["count"] > 0) {
                    $message = str_replace("%a", count($list_scenario_linked)-1, $msg["contribution_scenario_list"]);
                    $message = str_replace("%s", $list_scenario_linked["count"], $message);
                    
                    $html .= '<label>'.$message.' </label>
                        '.get_expandBase_button('scenario_linked').'
                        <div id="scenario_linkedChild" style="margin-bottom: 6px; display: none; width: 94%;">
                        <table class="modern" style="width:40%">';
                    
                    foreach ($list_scenario_linked as $area_id => $scenario_linked) {
                        if ('count' == $area_id) {
                            continue;
                        }
                        
                        $count = count($scenario_linked);
                        $new_area = new contribution_area($area_id);
                        $title_area = $new_area->get_title();
                        
                        $html .= "
                        	<tr>
                                <th>".$msg['admin_menu_contribution_area']." : ".htmlentities($title_area, ENT_QUOTES, $charset)."</th>
                            </tr>";
                        
                        for ($i = 0; $i < $count; $i++) {
                            $html .= "
                            <tr>
                                <td>".$msg['contribution_scenario_name']." : ".htmlentities($scenario_linked[$i], ENT_QUOTES, $charset)."</td>
                            </tr>";
                        }
                    }
                    $html.='</table></div>';
                }
        $html.='</div>
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
			$property->pmb_extended['list_entities'] = $this->get_saved_property($property->pmb_name, 'list_entities');
			
			$html.='
			<tr>';
				if($min > 0){
					$html.='<td style="width:20px;">';
					$html.= '<input type="checkbox"  onclick="return false" checked="checked" id="switch_'.$property->pmb_name.'" name="'.$property->pmb_name.'[switch]" class="switch" />';
					$td_mandatory ='<input onclick="return false" type="checkbox" name="'.$property->pmb_name.'[mandatory]" checked="checked" value="1">';
				}else{
					$html.='<td style="width:20px;">';
					$html.= '<input type="checkbox"
                                '.($this->get_saved_property($property->pmb_name) ?  'checked="checked"' : '')
                                .' id="switch_'.$property->pmb_name.'"  name="'.$property->pmb_name
					            .'[switch]" class="switch"  '
				                .(($this->get_saved_property($property->pmb_name) && $hasDraft) ? 'onclick="return false;"' : '')
					            .'/>';
					$td_mandatory ='<input type="checkbox" id="'.$property->pmb_name.'_mandatory" name="'.$property->pmb_name.'[mandatory]" '.($this->get_saved_property($property->pmb_name,'mandatory') || $min>0 ? 'checked="checked"' : '').' value="1">';
				}
				
				$html.='<input type="hidden" name="inputs_name[]" value="'.$property->pmb_name.'"/>';

				
				$template_field = $datatype_ui_class_name::get_form('',$property,$this->onto_class->get_restriction($property->uri),$property_data, $this->onto_class->pmb_name,'');
				$template_field = str_replace(array(
				    '!!form_row_content_mandatory_sign!!',
				    '!!form_row_content_mandatory_class!!'
				), '', $template_field);
				
				$disabled_field_hidden = false;
				if (in_array($property->pmb_name, static::CANNOT_BE_HIDDEN)) {
				    $disabled_field_hidden = true;
				}
				
				$html.='<label for="switch_'.$property->pmb_name.'"' .(($this->get_saved_property($property->pmb_name) && $hasDraft) ? 'title="'. $msg['contribution_form_switch_disable'] .'"' : '') .'>'."&nbsp; ".'</label>
				</td>
				<td>
                    <!-- property uri : "'.htmlentities($property->uri, ENT_QUOTES, $charset).'" -->
					<label class="switch-label">'.htmlentities($property->label,ENT_QUOTES, $charset).'</label>
					<span class="contribution_datatype_info"> ('.$property->get_pmb_datatype_label($property->pmb_datatype).')</span><br/>
					'.$this->get_equations_selector($this->get_equations_from_type($property->pmb_datatype, $property->range),$property->pmb_name,$this->get_saved_property($property->pmb_name,'equation')).'
				</td>				
				<td>
					<input type="text" id="'.$property->pmb_name.'_label" name="'.$property->pmb_name.'[label]" value="'.htmlentities(($this->get_saved_property($property->pmb_name,'label')?encoding_normalize::utf8_decode($this->get_saved_property($property->pmb_name,'label')): $property->label ),ENT_QUOTES, $charset).'"/>
				</td>
				<td>
					<div>
                        <!-- datatype : "'.htmlentities($datatype_class_name, ENT_QUOTES, $charset).'" -->
                        <!-- datatype ui : "'.htmlentities($datatype_ui_class_name, ENT_QUOTES, $charset).'" -->
						'.$template_field.'
						<input type="hidden" name="'.$property->pmb_name.'[default_value]" value="'.$this->onto_class->pmb_name.'_'.$property->pmb_name.'"/>
					</div>
				</td>
				<td>'.$td_mandatory.'</td>
				<td>
					<input type="checkbox" name="'.$property->pmb_name.'[hidden]" '.(($this->get_saved_property($property->pmb_name,'hidden') && !$disabled_field_hidden) ? 'checked="checked"' : '').' value="1" '.( $disabled_field_hidden ? 'disabled title="'.$msg['cannot_be_hidden_field'].'"' : '' ).'>
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
					<input type="hidden" name="'.$property->pmb_name.'[list_entities]" id="'.$property->pmb_name.'_list_entities" value="'.htmlentities($this->get_saved_property($property->pmb_name, 'list_entities'), ENT_QUOTES, $charset).'"/>
					<input type="hidden" name="'.$property->pmb_name.'[template]" id="'.$property->pmb_name.'_template" value="'.htmlentities($this->get_saved_property($property->pmb_name, 'template'), ENT_QUOTES, $charset).'"/>
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
			<div class="left">';
		$html.=
			'<input type="button" class="bouton" value="'.$msg['77'].'" onClick="if (test_form(this.form)) this.form.submit();"/>';
		
		if ($this->id) {
			$html.= '
				<input type="button" class="bouton" value="'.$msg['duplicate'].'" onClick="if (test_form(this.form)) {document.location = \'./modelling.php?categ=contribution_area&sub=form&type='.$this->type.'&action=duplicate&form_id='.$this->id.'\'}"/>';
		}
		$html.= '
			</div>';
		if ($this->id) {
			$html.= '
			<div class="right">
				<input class="bouton '.(($hasDraft || $hasScenario) ? 'disabled' : '') .'" type="button" '.(($hasDraft || $hasScenario) ? 'disabled' : '') .' value="'.$msg['supprimer'].'" onClick="confirmation_delete('.$this->id.',\''.$this->name.'\')" />			
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
            ajax_parse_dom();
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

            function onto_add(element_name,element_order){
                var new_order=parseInt(document.getElementById(element_name+"_new_order").value)+1;
                document.getElementById(element_name+"_new_order").value=new_order;
                
                var parent = document.getElementById(element_name);
                
                //div container
                var new_container = document.createElement("div");
                new_container.setAttribute("id",element_name+"_"+new_order);
                new_container.setAttribute("class","row");
                
                //input pour la valeur
                var input_value = document.getElementById(element_name+"_"+element_order+"_value").cloneNode(false);
                input_value.setAttribute("id",element_name+"_"+new_order+"_value");
                input_value.setAttribute("name",element_name+"["+new_order+"][value]");
                input_value.value = "";
                
                // selecteur de langue
                var select = document.getElementById(element_name+"_"+element_order+"_lang").cloneNode(true);
                select.setAttribute("id",element_name+"_"+new_order+"_lang");
                select.setAttribute("name",element_name+"["+new_order+"][lang]");
                
                // input de type
                var input_type = document.getElementById(element_name+"_"+element_order+"_type").cloneNode(false);
                input_type.setAttribute("id",element_name+"_"+new_order+"_type");
                input_type.setAttribute("name",element_name+"["+new_order+"][type]");
                
                // bouton de suppression
                var del_button = document.createElement("input");
                del_button.setAttribute("type","button");
                del_button.setAttribute("class","bouton_small");
                del_button.setAttribute("onclick","onto_del(\'"+element_name+"\',"+new_order+")");
                del_button.setAttribute("value","X");
                
                new_container.appendChild(input_value);
                new_container.appendChild(document.createTextNode(" "));
                new_container.appendChild(select);
                new_container.appendChild(document.createTextNode(" "));
                new_container.appendChild(input_type);
                new_container.appendChild(del_button);
                
                parent.appendChild(new_container);
                return true;
            }

            function onto_add_link(element_name, element_order) {
                var new_order = 0;
                var newOrderNode = document.getElementById(element_name+"_new_order");
                if (newOrderNode) {
                    new_order = parseInt(newOrderNode.value) + 1;
                    newOrderNode.value = new_order;
                } else {
                    console.error(`#{$element_name+"_new_order"} not fond!`);
                } 
                
                var parent = document.getElementById(element_name);
                if (parent) {
                    
                    //div container
                    var new_container = document.createElement("div");
                    new_container.setAttribute("id",element_name+"_"+new_order);
                    new_container.setAttribute("class","row");
                    
                    //check link
                    var old_check_node = document.getElementById(element_name+"_"+element_order+"_lien_check");
                    if (old_check_node) {
                        var check_link = old_check_node.cloneNode(false);
                        check_link.setAttribute("id",element_name+"_"+new_order+"_lien_check");
                        check_link.innerHTML = "";
                        
                        new_container.appendChild(check_link);
                    } else {
                        console.error(`#{$element_name+"_"+element_order+"_lien_check"} not fond!`);
                    }
                    
                    //input pour la valeur
                    var old_value_node = document.getElementById(element_name+"_"+element_order+"_value");
                    if (old_value_node) {
                        var input_value = old_value_node.cloneNode(false);
                        input_value.setAttribute("id",element_name+"_"+new_order+"_value");
                        input_value.setAttribute("name",element_name+"["+new_order+"][value]");
                        input_value.value = "";
                        
                        new_container.appendChild(input_value);
                    } else {
                        console.error(`#{$element_name+"_"+element_order+"_value"} not fond!`);
                    }
                                
                    // input de type
                    var old_type_node = document.getElementById(element_name+"_"+element_order+"_type");
                    if (old_type_node) {
                        var input_type = old_type_node.cloneNode(false);
                        input_type.setAttribute("id",element_name+"_"+new_order+"_type");
                        input_type.setAttribute("name",element_name+"["+new_order+"][type]");
                        
                        new_container.appendChild(input_type);
                    } else {
                        console.error(`#{$element_name+"_"+element_order+"_type"} not fond!`);
                    } 
                    
                    // open link
                    var old_open_link = document.getElementById(element_name+"_"+element_order+"_open_link");
                    if (old_open_link) {
                        var open_link = old_open_link.cloneNode(false);
                        open_link.setAttribute("id",element_name+"_"+new_order+"_open_link");
                        
                        new_container.appendChild(document.createTextNode(" "));
                        new_container.appendChild(open_link);
                    } else {
                        console.error(`#{$element_name+"_"+element_order+"_open_link"} not fond!`);
                    } 
                    
                    // add link
                    var add_link = document.getElementById(element_name+"_add_text_link");
                    if (add_link) {
                        add_link.setAttribute("data-element-order", new_order);
                        
                        new_container.appendChild(document.createTextNode(" "));
                        new_container.appendChild(add_link);
                    } else {
                        console.error(`#{$element_name+"_add_text_link"} not fond!`);
                    } 
                    
                    parent.appendChild(new_container);
                    return true;
                } else {
                    console.error(`#{$element_name} not fond!`);
                }
                
                return true;
            }

        	function onto_add_selector(element_name,element_order){
        		var new_order_element=document.getElementById(element_name+"_new_order");
        		var last_element = document.getElementById(element_name+"_"+new_order_element.value+"_display_label");
        		var new_order=parseInt(new_order_element.value)+1;
        		new_order_element.value=new_order;
        		
        		var parent = document.getElementById(element_name);
        		var new_child="";
        		
        		//div container
        		var new_container = document.createElement("div");
        		new_container.setAttribute("id",element_name+"_"+new_order);
        		new_container.setAttribute("class","row");
        		//input pour le label
        		var input_label = document.createElement("input");
        		input_label.setAttribute("type","text");
        		input_label.setAttribute("id",element_name+"_"+new_order+"_display_label");
        		input_label.setAttribute("class",last_element.getAttribute("class"));
        		input_label.setAttribute("autocomplete",last_element.getAttribute("autocomplete"));
        		input_label.setAttribute("att_id_filter",last_element.getAttribute("att_id_filter"));
        		input_label.setAttribute("autexclude",last_element.getAttribute("autexclude"));
        		input_label.setAttribute("completion",last_element.getAttribute("completion"));
         		input_label.setAttribute("autfield",element_name+"_"+new_order+"_value");
         		input_label.setAttribute("name",element_name+"["+new_order+"][display_label]");
        		input_label.setAttribute("value","");
        		
        		//input type
        		var input_type = document.createElement("input");
        		input_type.setAttribute("type","hidden");
        		input_type.setAttribute("id",element_name+"_"+new_order+"_type");
         		input_type.setAttribute("name",element_name+"["+new_order+"][type]");
        		input_type.setAttribute("value","");
        		
        		//input value
        		var input_value = document.createElement("input");
        		input_value.setAttribute("type","hidden");
        		input_value.setAttribute("id",element_name+"_"+new_order+"_value");
         		input_value.setAttribute("name",element_name+"["+new_order+"][value]");
        		input_value.setAttribute("value","");
        		
        		var new_child_del=document.createElement("input");
        		new_child_del.setAttribute("type","button");
        		new_child_del.setAttribute("class","bouton_small");
        		new_child_del.setAttribute("onclick","onto_remove_selector_value(\'"+element_name+"\',"+new_order+")");
        		new_child_del.value="X";
        		
        		//vidage
        		new_container.appendChild(input_label);
        		new_container.appendChild(input_type);
        		new_container.appendChild(input_value);
        		new_container.appendChild(new_child_del);
        		parent.appendChild(new_container);
        		ajax_pack_element(input_label);
        		
        		return true;
        	}

            function onto_del(element_name, element_order){
                var parent = document.getElementById(element_name);
                var child = document.getElementById(element_name+"_"+element_order);
                if(element_order != 0){
                	parent.removeChild(child);
                }else{
                	var inputValue = document.getElementById(element_name+"_"+element_order+"_value")
                	if(inputValue){
                		inputValue.value = "";
                	}
                	var inputFileId = document.getElementById(element_name+"_"+element_order+"_onto_file_id");
                	if(inputFileId){
                		inputFileId.value = "";
                	}
                	var lastFileLabel = document.getElementById(element_name+"_"+element_order+"_onto_last_file_label");
                	if(lastFileLabel){
                		lastFileLabel.innerHTML = "";
                	}
                }
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
				<div id="admin_contribution_area_form_list_entities_block">
					<div class="row">
						<label class="etiquette" for="admin_contribution_area_form_list_entities">'.$msg['admin_contribution_area_form_list_entities'].'</label>
					</div>
					<div class="row">
						<select id="admin_contribution_area_form_list_entities" multiple="multiple" style="min-width:100px;"></select>
					</div>
				</div>
				<div id="admin_contribution_area_form_list_template_list_ui">
					<div class="row">
						<label class="etiquette" for="admin_contribution_area_form_list_template_default">'.$msg['admin_contribution_area_form_list_template'].'</label>
					</div>
					<div class="row">
						<input value="" type="radio" name="admin_contribution_area_form_list_template_value" id="admin_contribution_area_form_list_template_default">
                        <label for="admin_contribution_area_form_list_template_default">'.$msg['admin_contribution_area_form_list_template_default'].'</label>
						<input value="1" type="radio" name="admin_contribution_area_form_list_template_value" id="admin_contribution_area_form_list_template_tag">
                        <label for="admin_contribution_area_form_list_template_tag">'.$msg['admin_contribution_area_form_list_template_tags'].'</label>
					</div>
				</div>
				<div id="admin_contribution_area_form_list_template_responsability_ui">
					<div class="row">
						<label class="etiquette" for="admin_contribution_area_form_responsability_template_default">'.$msg['admin_contribution_area_form_responsability_template'].'</label>
					</div>
					<div class="row">
						<input value="" type="radio" name="admin_contribution_area_form_responsability_template_value" id="admin_contribution_area_form_responsability_template_value">
                        <label for="admin_contribution_area_form_responsability_template_default">'.$msg['admin_contribution_area_form_list_template_default'].'</label>
						<input value="1" type="radio" name="admin_contribution_area_form_responsability_template_value" id="admin_contribution_area_form_responsability_template_autocomplete">
                        <label for="admin_contribution_area_form_responsability_template_autocomplete">'.$msg['admin_contribution_area_form_responsability_template_autocomplete'].'</label>
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
					"dojo/dom-construct",
					"dojo/_base/lang"
			], function(domStyle, dom, domGeometry, on, query, Dialog, registry, domAttr, domConstruct, lang) {
 				var header = dom.byId("contribution_fixed_header");
				var offset = domGeometry.position(header).y + domGeometry.docScroll().y;
				on(window, "scroll", function(e){
					if(e.pageY > offset){
						domStyle.set(header, "top", e.pageY - (offset) +  "px");
					}else{
						domStyle.set(header, "top", "0px");
					}
				});
				var idList = "";
                var idListEntities = "";
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

                    var listToManage = false;
                    idList = "";
                    if (propertyDatatypeUI.indexOf("list") != -1) {
                        listToManage = true;
                        idList = ontoClass + "_" + propertyName + "_0_value";
                    }

                    if (propertyDatatypeUI.indexOf("has_concept_scheme") != -1) {
                        listToManage = true;
                        idList = ontoClass + "_" + propertyName + "_0_value";
                    }

                    if (propertyDatatypeUI.indexOf("linked_authority") != -1) {
                        listToManage = true;
                        idList = ontoClass + "_" + propertyName + "_0_relation_type_authority";
                    }

                    if (propertyDatatypeUI.indexOf("responsability_selector") != -1) {
                        listToManage = true;
                        idList = ontoClass + "_" + propertyName + "_0_assertions_author_function";
                    }
                    
					if (listToManage) {
						var list_values = dom.byId(propertyName + "_list_values").value.split(",");
						domStyle.set("admin_contribution_area_form_list_values_block", "display", "");
						
						var selected = false;
                        var selectList = dom.byId(idList);
                        if (selectList) {
                            var newSelectList = lang.clone(selectList);
                            list_values_selector.innerHTML = newSelectList.innerHTML;
                            query("optgroup", list_entities_selector).forEach(function(node) {
            					node.style = "";
                                node.classList.forEach((cssClass) => {
                                    node.classList.remove(cssClass)
                                })
            				});
                            if (list_values_selector.options) { 
                                for (var i=0; i < list_values_selector.options.length; i++) {
                                    selected = false;
									if ((list_values[0] === "") || (list_values.indexOf(list_values_selector.options[i].value) != -1)) {
										selected = true;
									}
                                    list_values_selector.options[i].selected = selected
                                    list_values_selector.options[i].style = "";
                                }
                            }
                        }
					} else {
						domStyle.set("admin_contribution_area_form_list_values_block", "display", "none");
					}

                    // Template de liste multiple
                    var templateTags = ["onto_contribution_datatype_list_ui", "onto_common_datatype_list_ui"];
                    if (templateTags.indexOf(propertyDatatypeUI) !== -1) {
                        var field = dom.byId(ontoClass + "_" + propertyName + "_0_value");
						if (field && field.multiple) {
     						domStyle.set("admin_contribution_area_form_list_template_list_ui", "display", "");

                            // Selection du template
                            var templates = query("input[name=\'admin_contribution_area_form_list_template_value\']");
                            var templateSelected = dom.byId(propertyName + "_template").value ?? "";
                            for (let i = 0; i < templates.length; i++) {
                                if (templates[i].value == templateSelected) {
                                    templates[i].checked = true;
                                } else {
                                    templates[i].checked = false;
            					}
                            }

                        } else {
                            domStyle.set("admin_contribution_area_form_list_template_list_ui", "display", "none");
                        }
                    } else {
                        domStyle.set("admin_contribution_area_form_list_template_list_ui", "display", "none");
                    }
                    // Template de Responsability
                    if (propertyDatatypeUI == "onto_contribution_datatype_responsability_selector_ui") {
 						domStyle.set("admin_contribution_area_form_list_template_responsability_ui", "display", "");
                        // Selection du template
                        var templates = query("input[name=\'admin_contribution_area_form_responsability_template_value\']");
                        var templateSelected = dom.byId(propertyName + "_template").value ?? "";
                        for (let i = 0; i < templates.length; i++) {
                            if (templates[i].value == templateSelected) {
                                templates[i].checked = true;
                            } else {
                                templates[i].checked = false;
        					}
                    }
                    } else {
                        domStyle.set("admin_contribution_area_form_list_template_responsability_ui", "display", "none");
                    }
					    
                    // Valeurs à afficher dans les datatypes linked_authority
					var list_entities_selector = dom.byId("admin_contribution_area_form_list_entities");
					domConstruct.empty(list_entities_selector);

                    var listEntitiesToManage = false;
                    idListEntities = "";
                    if (propertyDatatypeUI.indexOf("linked") != -1) {
                        listEntitiesToManage = true;
                        switch (propertyName) {
                            case "expression_of":
                            case "has_expression":
                            case "has_other_link":
                                idListEntities = ontoClass + "_" + propertyName + "_0_relation_type_work";
                                break;

                            case "has_linked_record":
                                idListEntities = ontoClass + "_" + propertyName + "[0][assertions][relation_type]";
                                break;

                            case "has_linked_authority":
                            default:
                                idListEntities = ontoClass + "_" + propertyName + "_0_authority_type";
                                break;
                        }
                    }
					if (listEntitiesToManage) {
						var list_entities = dom.byId(propertyName + "_list_entities").value.split(",");
						domStyle.set("admin_contribution_area_form_list_entities_block", "display", "");
						
						var selected = false;
                        var selectListEntities = dom.byId(idListEntities);
                        if (selectListEntities) {
                            var newSelectListEntities = lang.clone(selectListEntities);
                            list_entities_selector.innerHTML = newSelectListEntities.innerHTML;
                            query("optgroup", list_entities_selector).forEach(function(node) {
            					node.style = "";
                                node.classList.forEach((cssClass) => {
                                    node.classList.remove(cssClass)
                                })
            				});
                            if (list_entities_selector.options) {
                                for (var i=0; i < list_entities_selector.options.length; i++) {
                                    selected = false;
        							if ((list_entities[0] === "") || (list_entities.indexOf(list_entities_selector.options[i].value) != -1)) {
        								selected = true;
        							}
                                    list_entities_selector.options[i].selected = selected
                                    list_entities_selector.options[i].style = "";
                                }
                            }
                        }
					} else {
						domStyle.set("admin_contribution_area_form_list_entities_block", "display", "none");
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
					
                    // Récupération du template choisi pour les listes
                    var templates = query("input[name=\'admin_contribution_area_form_list_template_value\']");
                    var templateSelected = "";
                    for (let i = 0; i < templates.length; i++) {
                        if (templates[i].checked) {
                            templateSelected = templates[i].value;
                            break;
    					}
                    }
					dom.byId(propertyName + "_template").value = templateSelected;
                    // Récupération du template choisi pour les responsabilité
                    var templates = query("input[name=\'admin_contribution_area_form_responsability_template_value\']");
                    var templateSelected = "";
                    for (let i = 0; i < templates.length; i++) {
                        if (templates[i].checked) {
                            templateSelected = templates[i].value;
                            break;
    					}
                    }
					dom.byId(propertyName + "_template").value = templateSelected;

                    var list_values = "";
					query("option:checked", "admin_contribution_area_form_list_values").forEach(function(node) {
						if (list_values) {
							list_values = list_values + ",";
						}
						list_values = list_values + node.value;
					});
					dom.byId(propertyName + "_list_values").value = list_values;

					list_values = list_values.split(",");
                    var currentIdList = ontoClass + "_" + propertyName + "_0_value";
                    if (idList) {
                        currentIdList = idList;
                    }
                    var selectedOption = 0;
					query("option", currentIdList).forEach(function(node) {
						if ((list_values[0] === "") || (list_values.indexOf(node.value) != -1)) {
							domStyle.set(node, "display", "");
                            if (!selectedOption) {
                                dom.byId(currentIdList).value = node.value;
                                selectedOption = node.value;
                            }
						} else {
							domStyle.set(node, "display", "none");
						}
					});

					var list_entities = "";
					query("option:checked", "admin_contribution_area_form_list_entities").forEach(function(node) {
						if (list_entities) {
							list_entities = list_entities + ",";
						}
						list_entities += node.value;
					});
					dom.byId(propertyName + "_list_entities").value = list_entities;

					list_entities = list_entities.split(",");
                    var currentIdList = ontoClass + "_" + propertyName + "_0_authority_type";
                    if (idListEntities) {
                        currentIdList = idListEntities;
                    }
                    var selectedOption = 0;
					query("option", currentIdList).forEach(function(node) {
						if ((list_entities[0] === "") || (list_entities.indexOf(node.value) != -1)) {
							domStyle.set(node, "display", "");
                            if (!selectedOption) {
                                dom.byId(currentIdList).value = node.value;
                                selectedOption = node.value;
                            }
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
					
					if ($default_value[0]['type'] != 'http://www.w3.org/2000/01/rdf-schema#Literal' && empty($default_value[0]['value'])) {
					    // Si on n'a pas d'entité choisie, on ne met pas de label
					    // Risque d'avoir un mauvais type d'entité
					    $default_value[0]['display_label'] = "";
					}
					
					// On récupère l'uri de la propriété
				    $range = array();
				    $onto = $this->get_onto_class();
				    $properties = $onto->get_properties();
				    foreach ($properties as $property_uri) {
				        if (preg_match("/#$property_name$/", $property_uri)) {
				            $range = $onto->get_property_range($property_uri);
				            break;
				        }
				    }
				    
				    // Si on n'a pas le type on le définit en Literal par défaut
				    if (empty($default_value[0]['type'])) {
					    // Ce n'est pas un tableau numériques, on ne sait pas quoi en faire...
					    $default_value[0]['type'] = 'http://www.w3.org/2000/01/rdf-schema#Literal';
			        }
				    
			        // Le type ne correspond pas aux données dans le store ...
				    if (!empty($range) && !empty($range[0]) && ($range[0] != $default_value[0]['type'])) {
			            // On récupère le type qui correspond à la propriété
			            $default_value[0]['type'] = $range[0];
				    }
                
					
					if (!empty($default_value[0]["assertions"]["author_qualification"]["elements"])){
					    $default_value[0]["assertions"]["author_qualification"] = stripslashes( json_encode( htmlspecialchars_array( $default_value[0]["assertions"]["author_qualification"])));
					} else {
					    $default_value[0]["assertions"]["author_qualification"] = '';
					}
					
					$length = count($default_value);
					$temp_default_value = array();
					if ($length > 1) {
    					for($j = 0; $j < $length; $j++) {
    					    if (!empty($default_value[$j]['value'])) {
    					        $temp_default_value[] = $default_value[$j];
    					    }
    					}
    					
    					if (count($temp_default_value) == 0) {
    					    $temp_default_value[] = $default_value[0];
    					}
    					
    					$default_value = $temp_default_value;
					}
					
					for($j = 0; $j < $length; $j++) {
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
						
    					$props = $onto->get_property('http://www.pmbservices.fr/ontology#'.$property_name);
    					if (!empty($props) && !empty($props->pmb_datatype) && $props->pmb_datatype == "http://www.pmbservices.fr/ontology#floating_date") {
    					    foreach($default_value as $key => $values) {
    					        $default_value[$key]['value'] = $values['value'].'|||'.$values['date_begin'].'|||'.$values['date_end'].'|||'.$values['comment'];
    					    }
    					}
					}
                    
                    if (in_array($property_name, static::CANNOT_BE_HIDDEN) && isset($properties_list[$property_name]['hidden'])) {                            
                        // le champ ne peut pas être masqué
                        unset($properties_list[$property_name]['hidden']);
                    }
                    
					$properties_list[$property_name]['default_value'] = $default_value;
				}
			}
		}
		$this->parameters = encoding_normalize::json_encode($properties_list);
			
		$this->name = stripslashes($form_name);
		$this->comment = stripslashes($form_comment);
	}
	
	public function save($ajax_mode = false) {
	    global $thesaurus_ontology_filemtime, $pmb_authors_qualification;
	    global $duplicate_grid_origin_id, $duplicate_grid;
        
		$query = 'insert into ';
		$where = '';
		if (!empty($this->id)) {
			$query = 'update ';
			$where = ' where id_form = "'.$this->id.'"';
		}
		$query .= 'contribution_area_forms set ';
		$query .= 'form_title="'.addslashes($this->name).'",';
		$query .= 'form_comment="'.addslashes($this->comment).'",';
		$query .= 'form_parameters="'.addslashes($this->parameters).'",';
		$query .= 'form_type="'.addslashes($this->type).'"';
		
		pmb_mysql_query($query.$where);
		
		if (empty($this->id)) {
			$this->id = pmb_mysql_insert_id();
		}
		
		if ($duplicate_grid) {
		    $origin_grid = new grid("contribution_area_form_" . intval($duplicate_grid_origin_id));
		    if ($origin_grid->get_status()) {
    		    $grid = new grid("contribution_area_form_" . intval($this->id));
    		    $grid->set_data(json_decode($origin_grid->get_data()));
    		    $grid->save();
		    }
		}
		
		$tab_file_rdf = unserialize($thesaurus_ontology_filemtime);
		$tab_file_rdf['onto_contribution_form_' . $this->id] = 0;
		
		$query = 'UPDATE parametres SET valeur_param="'.addslashes(serialize($tab_file_rdf)).'" WHERE type_param="thesaurus" AND sstype_param="ontology_filemtime"';
		pmb_mysql_query($query);
		
		if (empty($ajax_mode)) {
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
	
    public static function save_parameters($id_contribution, $parameters) {
        pmb_mysql_query("UPDATE contribution_area_forms SET form_parameters = '" . addslashes(encoding_normalize::json_encode($parameters)) . "' WHERE id_form = '$id_contribution'");
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
		    
		    $contribution_area_store = new contribution_area_store();
		    
		    foreach($this->unserialized_parameters->$property_name as $key => $value){
		        if($key == "default_value" && is_array($value)){
		            $value_properties =  array();
		            foreach($value as $val){
		                
		                if (empty($val->type)) {
		                    $val->type = "http://www.w3.org/2000/01/rdf-schema#Literal";
		                }
		                
		                foreach ($val as $pmb_name => $property_value) {
		                    
		                    if ($pmb_name == "type" || $pmb_name == "value") {
		                        continue;
		                    }
		                    
		                    switch ($pmb_name) {
		                        case "lang":
		                            $value_properties["lang"] = $val->lang;
		                            break;
		                            
		                        case "display_label":
		                            $value_properties["display_label"] = stripslashes(encoding_normalize::utf8_decode($val->display_label));
		                            break;
		                            
		                        case "assertions":
		                            if (!isset($value_properties["assertions"])) {
		                                $value_properties["assertions"] = array();
		                            }
		                            foreach($property_value as $assertions_property => $assertions_property_value) {
		                                $value_properties["assertions"][] = new onto_assertion($val->value, $assertions_property, $assertions_property_value);
		                            }
		                            break;
		                            
		                        default:
		                            if (!isset($value_properties["assertions"])) {
		                                $value_properties["assertions"] = array();
		                            }
		                            
		                            $uri = $contribution_area_store->get_uri_from_pmb_name($pmb_name);
		                            if (empty($uri)) {
		                                $uri = $pmb_name;
		                            }
		                            $value_properties["assertions"][] = new onto_assertion($val->value, $uri, $property_value);                                    ;
		                            break;
		                    }
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
						$values[] = new $datatype_class_name((isset($val->value) ? $val->value : null), $val->type, $value_properties,'');
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
				if (is_array($tab_default_value)) {
    				for ($j = 0; $j < count($tab_default_value); $j++) {
    				    if (isset($tab_default_value[$j]) && !is_array($tab_default_value[$j]->value)) {
    						$tab_default_value[$j]->value = array($tab_default_value[$j]->value);
    					}
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
		
		switch($datatype) {
		    case "http://www.pmbservices.fr/ontology#resource_selector": 
		    case "http://www.pmbservices.fr/ontology#responsability_selector": 
    			if($range[0]) {
    				$type =  explode('#',$range[0])[1];
    
    				switch ($type) {
    					case "linked_record":
    					case "record":
    					case "bulletin":
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
		$selector = "<br/><h5 for='".$name."[equation]'>".$msg['contribution_area_autocompletion_equation']."</h5>";
		$selector .= "<select name='".$name."[equation]'>";
		$selector .= "<option value='0' >".$msg['admin_contribution_area_form_select_equation']."</option>";
		foreach ($equations as $id => $equation) {
			$selector .= "<option value='". $id ."' ". ($selected == $id ? "selected='selected'" : "") .">".$equation['name']."</option>";
		}
		$selector .= "</select>";
		
		return $selector;
	}
	
	public function render(){
		global $nb_per_page_gestion, $class_path;
		
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

		$onto_store = contribution_area_store::get_formstore($this->id, $this->get_active_properties());
		//chargement de l'ontologie dans son store
		$reset = $onto_store->load($class_path."/rdf/ontologies_pmb_entities.rdf", onto_parametres_perso::is_modified());
		onto_parametres_perso::load_in_store($onto_store, $reset);
		
		$onto_ui = new onto_ui("", $onto_store, array(), "arc2", contribution_area_store::DATASTORE_CONFIG, contribution_area_store::CONTRIBUTION_NAMESPACE, 'http://www.w3.org/2000/01/rdf-schema#label', $params);
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
		if (intval($id)) {
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
	    $this->origin_id = $this->id;
		$this->id = 0;
		$this->name = '';
		$this->comment = '';
		return $this->get_form($area, true);
	}
	
	/**
	 * Créer le formulaire de duplication automatiquement
	 * @param int $area
	 * @return string
	 */
	public function generate_duplication_form($ajax_mode = false) {
	    global $msg;
	    
		$this->id = 0;
		$this->comment = '';
		
		if (strpos($this->name, $msg['contribution_area_form_duplicated']) === false) {
		    $new_name = "";
		    $new_name = $msg['contribution_area_form_duplicated'] . " " . $this->name ;
		    $this->name = $new_name;
		}
		
	    $matches = array();
	    if (preg_match('/\([1-9]+\)/i', $this->name, $matches)) {
	        $name = explode(" (", $this->name);
	        $query = 'SELECT count(*) FROM contribution_area_forms WHERE form_title like "%'.$name[0].'%"';
	        $result = pmb_mysql_query($query);
	        $max = pmb_mysql_result($result,0,0);
	        $this->name = $name[0]." ($max)";
	    }else{
	        $query = 'SELECT count(*) FROM contribution_area_forms WHERE form_title like "%'.$this->name.'%"';
	        $result = pmb_mysql_query($query);
	        $max = pmb_mysql_result($result,0,0);
	        if (!empty($max)) {
    	        $this->name .= " ($max)";
	        }
	    }
	    
	    return encoding_normalize::json_encode($this->save($ajax_mode));
	}
	
	/**
	 * Renvoie l'identifiant
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}
	
	public function get_scenario_linked() {
	    $graphstore = contribution_area::get_graphstore();
	    $scenario_list = array();
	    $scenario_list["count"] = 0;
	    $count = 0;
	    
	    $succes = $graphstore->query('select ?uri where {
        ?uri ca:eltId "'.$this->id.'" .
        }');
	    
	    if ($succes) {
	        $forms = $graphstore->get_result();
	        $contribution_area_list = contribution_area::get_list_ajax();
	        foreach ($forms as $form) {
	            $succes = $graphstore->query('select ?uri ?name ?area where {
                ?attachment rdf:type ca:Attachment .
                ?attachment ca:attachmentSource ?uri .
                ?attachment ca:inArea ?area .
                ?attachment ca:attachmentDest <'.$form->uri.'> .
                ?uri rdf:label ?name .
            }');
	            
	            if ($succes) {
	                $scenarios = $graphstore->get_result();
	                foreach ($scenarios as $scenario) {
	                    $matches = array();
	                    preg_match('/(\d+)$/', $scenario->area, $matches);
	                    if (!empty($contribution_area_list[$matches[0]])) {
	                        $scenario_list[$matches[0]][] = $scenario->name;
    	                    $count ++;
	                    }
	                }
	            } else {
	                var_dump("Errors : ".$graphstore->get_errors());
	                break;
	            }
	        }
            $scenario_list["count"] = $count;
	    } else {
	        var_dump("Errors : ".$graphstore->get_errors());
	    }
	    return $scenario_list;
	}
	
	public static function has_draft_contribution_from_id($id){
	    $store = new contribution_area_store();
	    $dataStore = $store->get_datastore();
	    $success = $dataStore->query("
            select * where {
                ?uri pmb:form_id '$id' .
                optional {
                    ?uri pmb:identifier ?identifier .
                }
            }
        ");
	    
	    $results = array();
	    $has_draft = false;
	    
	    if ($success){
	        $results = $dataStore->get_result();
	        foreach ($results as $key => $result) {
	            if (!empty($result->identifier)) {
	                array_splice($results, $key, 1);
	            }
	        }
	    }
	    
	    if (count($results) > 1) {
	        $has_draft = true;
	    }
	    return $has_draft;
	}
	
	public static function has_draft_contribution_from_uri($uri){
	    $store = new contribution_area_store();
	    $dataStore = $store->get_datastore();
	    $success = $dataStore->query("
            select ?uri where {
                ?uri pmb:form_uri '$uri' . 
                optional {
                    ?uri pmb:identifier ?identifier .
                }
            }
        ");
	    
	    $results = array();
	    $has_draft = false;
	    
	    if ($success){
	        $results = $dataStore->get_result();
	        foreach ($results as $key => $result) {
	            if (!empty($result->identifier)) {
	                array_splice($results, $key, 1);
	            }
	        }
	    }
	    
	    if (count($results) > 1) {
	        $has_draft = true;
	    }
	    return $has_draft;
	}
	
	public function get_type() {
	    return $this->type;
	}

	/**
	 * Supprime une propriété du formulaire
	 * @param string $property
	 * @return void
	 */
	public function remove_property($property = "")
	{
		if(empty($this->unserialized_parameters->$property)) {
			return;
		}

		unset($this->unserialized_parameters->$property);
		self::save_parameters($this->id, serialize($this->unserialized_parameters));
	}
}