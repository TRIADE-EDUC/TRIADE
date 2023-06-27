<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_item.class.php,v 1.13 2019-06-04 14:58:14 tsamson Exp $
if (stristr ( $_SERVER ['REQUEST_URI'], ".class.php" ))
	die ( "no access" );

require_once($class_path.'/onto/common/onto_common_item.class.php');
require_once($class_path.'/contribution_area/contribution_area_scenario.class.php');
require_once($include_path.'/templates/onto/contribution/onto_contribution_item.tpl.php');

/**
 * class onto_common_item
 */
class onto_contribution_item extends onto_common_item {
	
	/**
	 *
	 * @var contribution_area_form
	 */
	protected $contribution_area_form;
	
	/**
	 *
	 * @param string $prefix_url        	
	 * @param string $flag        	
	 * @param string $action        	
	 * @return mixed
	 */
	public function get_grid($prefix_url = "", $flag = "", $action = "grid") {
		global $msg, $charset, $ontology_tpl;
		
		// lors de la première instance de notre contribution, on renseigne les champs avec les valeurs par défaut
		if (onto_common_uri::is_temp_uri ( $this->uri )) {
			$this->set_assertions ( $this->get_assertions_from_active_properties () );
		}
		$temp_datatype_tab = $this->order_datatypes ();
		
		$form = $ontology_tpl ['form_body_grid'];
		
		$form = str_replace ( "!!onto_form_scripts!!", $ontology_tpl ['form_scripts'], $form );
		
		$form = str_replace ( "!!return_url!!", $prefix_url . ($action ? "&action=" . $action : ""), $form );
		
		if (isset ( $this->contribution_area_form )) {
			$form = str_replace ( "!!onto_form_title!!", htmlentities ( $this->contribution_area_form->get_name (), ENT_QUOTES, $charset ), $form );
		} else {
			$form = str_replace ( "!!onto_form_title!!", htmlentities ( $msg ["admin_contribution_area_form_type"] . " : " . $this->onto_class->label, ENT_QUOTES, $charset ), $form );
		}
		
		$content = '';
		$index = 0;
		$properties = $this->onto_class->get_properties ();
		$properties = array_merge($properties, $this->onto_class->get_properties_and_restrictions_from_sub_class_of());
		sort ( $properties );
		foreach ( $properties as $uri_property ) {
			$property = $this->onto_class->get_property ( $uri_property );
			
			if ((! $flag || (in_array ( $flag, $property->flags ))) && isset ( $property->pmb_extended )) {
				
				$datatype_class_name = $this->resolve_datatype_class_name ( $property );
				$datatype_ui_class_name = $this->resolve_datatype_ui_class_name ( $datatype_class_name, $property, $this->onto_class->get_restriction ( $property->uri ) );
				
				// On encapsule dans des divs movables pour l'édition de la grille de saisie
				$movable_div = $ontology_tpl ['form_movable_div'];
				$movable_div = str_replace ( '!!movable_index!!', $index, $movable_div );
				$movable_div = str_replace ( '!!movable_property_label!!', htmlentities ( $property->label, ENT_QUOTES, $charset ), $movable_div );
				
				// on modifie la propiété avec le paramétrage du formulaire
				if (!empty($property->pmb_extended ['label'])) {
					$property->label = $property->pmb_extended ['label'];
				}
				
				if (!empty($property->pmb_extended ['default_value'])) {
					$property->default_value = array ();
					foreach ( $property->pmb_extended ['default_value'] as $key => $value ) {
						if (is_object ( $value ) && isset ( $value->value )) {
							$property->default_value [] = $value->value;
						}
						if (is_array ( $value ) && isset ( $value ['value'] )) {
							$property->default_value [] = $value ['value'];
						}
					}
				}
				
				// propriété obligatoire
				if (!empty($property->pmb_extended ['mandatory'])) {
					$this->onto_class->get_restriction ( $property->uri )->set_min ( '1' );
				}
				
				// propriété cachée
				if (!empty($property->pmb_extended ['hidden'])) {
					$movable_div = str_replace ( '!!datatype_ui_form!!', $datatype_ui_class_name::get_hidden_fields ( $this->uri, $property, $this->onto_class->get_restriction ( $property->uri ), $temp_datatype_tab [$property->uri] [$datatype_ui_class_name], onto_common_uri::get_name_from_uri ( $this->uri, $this->onto_class->pmb_name ), $flag ), $movable_div );
				} else {
					$movable_div = str_replace ( '!!datatype_ui_form!!', $datatype_ui_class_name::get_form ( $this->uri, $property, $this->onto_class->get_restriction ( $property->uri ), $temp_datatype_tab [$property->uri] [$datatype_ui_class_name], onto_common_uri::get_name_from_uri ( $this->uri, $this->onto_class->pmb_name ), $flag ), $movable_div );
				}
				$content .= $movable_div;
				
				$index ++;
			}
		}
		$form = str_replace ( "!!onto_form_content!!", $content, $form );
		$form = str_replace ( "!!onto_form_save!!", '<input type="button" class="bouton" value="' . htmlentities ( $msg ["77"], ENT_QUOTES, $charset ) . '" id="bt_save"/>', $form );
		$form = str_replace ( "!!onto_form_back!!", '<input type="button" class="bouton" value="' . htmlentities ( $msg ["76"], ENT_QUOTES, $charset ) . '" onclick="window.location = document.getElementById(\'return_url\').value;"/>', $form );
		
		$form = str_replace ( "!!onto_form_del_script!!", '', $form );
		$form = str_replace ( "!!onto_datasource_validation!!", '', $form );
		$form .= '<script type="text/javascript">window.document.title = "' . addslashes ( $this->onto_class->label ) . '";</script>';
		
		return $form;
	} // end of member function get_form
	
	/**
	 *
	 * @param contribution_area_form $contribution_area_form        	
	 * @return onto_contribution_item
	 */
	public function set_contribution_area_form($contribution_area_form) {
		$this->contribution_area_form = $contribution_area_form;
		return $this;
	}
	
	/**
	 *
	 * @return contribution_area_form
	 */
	public function get_contribution_area_form() {
		return $this->contribution_area_form;
	}
	
	/**
	 * Renvoie un tableau des déclarations associées à l'instance
	 *
	 * @return onto_assertion
	 * @access public
	 */
	public function get_assertions_from_active_properties() {
		$assertions = array ();
		
		// On construit manuellement l'assertion type
		$assertions [] = new onto_assertion ( $this->uri, "http://www.w3.org/1999/02/22-rdf-syntax-ns#type", $this->onto_class->uri, "", array (
				'type' => "uri" 
		) );
		foreach ( $this->onto_class->get_properties () as $uri_property ) {
			
			$property = $this->onto_class->get_property ( $uri_property );
			
			if ($property->pmb_extended) {
				/* @var $datatype onto_common_datatype */
				$datatype_class_name = $this->resolve_datatype_class_name ( $property );
				if (count ( $property->pmb_extended ['default_value'] )) {
					foreach ( $property->pmb_extended ['default_value'] as $bnode => $bnode_value ) {
						$datatype = new $datatype_class_name ( $bnode_value ['value'], $bnode_value ['type'], array (
								'lang' => $bnode_value ['lang'] 
						) );
						$assertions [] = new onto_assertion ( $this->uri, $property->uri, $datatype->get_raw_value (), $datatype->get_value_type (), $datatype->get_value_properties () );
					}
				}
				if ($this->onto_class->get_property ( $property->uri )->inverse_of) {
					$assertions [] = new onto_assertion ( $datatype->get_raw_value (), $this->onto_class->get_property ( $property->uri )->inverse_of->uri, $this->uri, $this->onto_class->uri );
				}
			}
		}
		return $assertions;
	} // end of member function get_assertions
	
	/**
	 * Appel les fonctions static get_form et articule le formulaire de l'item courant
	 *
	 * on itère sur les propriétés de l'onto_class, on envoi aussi le datatype si présent
	 *
	 * @param string $prefix_url
	 *        	Préfixe de l'url de soumission du formulaire
	 * @param string $flag
	 *        	Nom du flag à utiliser pour limiter aux champs concernés
	 *        	
	 * @return string
	 * @access public
	 */
	public function get_form($prefix_url = "", $flag = "", $action = "save") {
		global $msg, $charset, $ontology_tpl, $area_id, $sub_form, $form_id, $sub, $scenario, $pmb_id, $contributor;
		global $ontology_contribution_tpl;
		// lors de la première instance de notre contribution, on renseigne les champs avec les valeurs par défaut
		
		$this->merge_datatypes();
		
		$temp_datatype_tab = $this->order_datatypes ();
		
		$end_form = '';
		$form = '';
		
		if (!$sub_form) {
			$form .= '
					<div class="contributionDivContainer">
						<div data-dojo-type="apps/contribution_area_form/form_progress/FormContainer" doLayout="false" style="width: 100%">';
			$form .= '		<div title="!!onto_form_title!!" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="selected:true">';
			$end_form .= "</div>
						</div>
					</div>";
		}
		
		$form .= $ontology_tpl['form_body'];
		
		if (!is_numeric((explode('#', $this->uri)[1]))) {
			$prefix_uri = explode('#', $this->uri)[1];
		} else {
			$prefix_uri = $sub."_".explode('#', $this->uri)[1];
		}
		$form = str_replace("!!uri!!", $this->uri, $form );
		$form = str_replace("!!prefix_uri!!", $prefix_uri, $form );
		$form = str_replace("!!onto_form_scripts!!", $ontology_contribution_tpl['form_scripts'], $form);
		$form = str_replace("!!caller!!", rawurlencode ( onto_common_uri::get_name_from_uri ( $this->uri, $this->onto_class->pmb_name ) ), $form );
		
		$form = str_replace("!!onto_form_id!!", onto_common_uri::get_name_from_uri ( $this->uri, $this->onto_class->pmb_name ), $form );
		$form = str_replace("!!onto_form_action!!", $prefix_url . "&action=" . $action, $form );
		$form = str_replace("!!onto_form_title!!", htmlentities($this->contribution_area_form->get_name(), ENT_QUOTES, $charset ), $form );
		
		$linked_forms = array ();
		if ($this->contribution_area_form->get_linked_forms ()) {
			$linked_forms = $this->contribution_area_form->get_linked_forms ();
		}
		
		$content = '';
		$valid_js = "";
		
		/**
		 * *****TODO : modif temporaire**********
		 */
		$properties = $this->onto_class->get_properties ();
		$properties = array_merge($properties, $this->onto_class->get_properties_and_restrictions_from_sub_class_of());
		
		sort($properties);
		/**
		 * ***********************************
		 */
		
		if (sizeof($properties)) {
			$index = 0;
			foreach ( $properties as $uri_property ) {
				
				$property = $this->onto_class->get_property ( $uri_property );
				
				if ((! $flag || (in_array ( $flag, $property->flags ))) && isset ( $property->pmb_extended )) {
					
					$datatype_class_name = $this->resolve_datatype_class_name ( $property );
					
					$datatype_ui_class_name = $this->resolve_datatype_ui_class_name ( $datatype_class_name, $property, $this->onto_class->get_restriction ( $property->uri ) );
					
					// On encapsule dans des divs movables pour l'édition de la grille de saisie
					$movable_div = $ontology_tpl ['form_movable_div'];
					$movable_div = str_replace ( '!!movable_index!!', $index, $movable_div );
					$movable_div = str_replace ( '!!movable_property_label!!', htmlentities ( $property->label, ENT_QUOTES, $charset ), $movable_div );
					
					// gestion des formulaires liés
					$property->has_linked_form = false;
					$property->linked_form = array ();
					
					for($i=0; $i<count($linked_forms);$i++) {
						// recherche du formulaire lié
						if ($linked_forms[$i]['propertyPmbName'] == $property->pmb_name) {
							$property->has_linked_form = true;
							$property->linked_form['attachment_id'] = $linked_forms[$i]['id'];
							
							// id_du formulaire dans la base relationnelle
							$property->linked_form['form_id'] = $linked_forms[$i]['formId'];
							// id du formulaire dans le store
							$property->linked_form['form_id_store'] = $linked_forms[$i]['id'];
							// uri du formulaire dans le store
							$property->linked_form['form_uri'] = $linked_forms[$i]['uri'];
							if ($area_id) {
								// id de l'espace
								$property->linked_form['area_id'] = $area_id;
							}
							// type du formulaire
							$property->linked_form['form_type'] = $linked_forms[$i]['entityType'];
							// titre du formulaire
							$property->linked_form['form_title'] = $linked_forms[$i]['name'];
							// URI du scénario parent
							$property->linked_form['scenario_uri'] = $linked_forms[$i]['scenarioUri'];
						}
						// recherche du formulaire lié
						// if ($linked_forms[$i]['propertyPmbName'] == $property->pmb_name) {
						// $property->has_linked_form = true;
						// $property_linked_form = array();
						// $property_linked_form['attachment_id'] = $linked_forms[$i]['id'];
						
						// id_du formulaire dans la base relationnelle
						// $property_linked_form['form_id'] = $linked_forms[$i]['formId'];
						// id du formulaire dans le store
						// $property_linked_form['form_id_store'] = $linked_forms[$i]['id'];
						// uri du formulaire dans le store
						// $property_linked_form['form_uri'] = $linked_forms[$i]['uri'];
						// if ($area_id) {
						// id de l'espace
						// $property_linked_form['area_id'] = $area_id;
						// }else {
						// $property_linked_form['area_id'] = "";
						// }
						// type du formulaire
						// $property_linked_form['form_type'] = $linked_forms[$i]['entityType'];
						// //titre du formulaire
						// $property_linked_form['form_title'] = $linked_forms[$i]['name'];
						// URI du scénario parent
						// $property_linked_form['scenario_uri'] = $linked_forms[$i]['scenarioUri'];
						
						// $property->linked_form[] = $property_linked_form;
						
						// }
					}
					
					// on modifie la propiété avec le paramétrage du formulaire
					if (!empty($property->pmb_extended ['label'])) {
						$property->label = $property->pmb_extended ['label'];
					}
					
					if (!empty($property->pmb_extended ['default_value'])) {
						$property->default_value = array ();
						foreach ($property->pmb_extended ['default_value'] as $key => $value ) {
							//$property->default_value[] = $value["value"];
							if($value && is_array($value)){
							    $property->default_value[] = $value['value'];
							}
						}
					}
					
					// propriété obligatoire
					if (!empty($property->pmb_extended ['mandatory'])) {
						$this->onto_class->get_restriction ( $property->uri )->set_min ( '1' );
					}
					
					// enregistrement de l'espace
					// if ($property->pmb_datatype == "http://www.pmbservices.fr/ontology#resource_selector") {
					// $property->pmb_extended['values_from_area'] = self::get_values_from_area($property->range[0]);
					// }
					
					$property_data = array();
					
					if (!empty($temp_datatype_tab[$property->uri])) {
						$property_data = $temp_datatype_tab[$property->uri][$datatype_ui_class_name];
					}
					
					// propriété cachée
					if (!empty($property->pmb_extended['hidden'])) {
					    $movable_div = str_replace('!!datatype_ui_form!!', $datatype_ui_class_name::get_hidden_fields($this->uri, $property, $this->onto_class->get_restriction($property->uri), $property_data, onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name)), $movable_div);
					} else {
						$movable_div = str_replace('!!datatype_ui_form!!', $datatype_ui_class_name::get_form($this->uri, $property, $this->onto_class->get_restriction($property->uri), $property_data, onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name), $flag), $movable_div);
					}
					
					$content .= $movable_div;
					
					if ($valid_js) {
						$valid_js .= ",";
					}					
					$valid_js .= $datatype_ui_class_name::get_validation_js ( $this->uri, $property, $this->onto_class->get_restriction ( $property->uri ), $property_data, onto_common_uri::get_name_from_uri ( $this->uri, $this->onto_class->pmb_name ), $flag );
					$index ++;
				}
			}
		}
		
		$content .= "<input type='hidden' name='sub_form' value='" . $sub_form . "'>";
		
		$form = str_replace ( "!!onto_form_content!!", $content, $form );
		
		$scenario_uri = '';
		if (isset ( $scenario )) {
			$form = str_replace ( "!!parent_scenario_uri!!", $scenario, $form );
			$scenario_uri = 'http://www.pmbservices.fr/ca/Scenario#' . $scenario;
		} else {
			$form = str_replace ( "!!parent_scenario_uri!!", '', $form );
		}

		$form = str_replace ( "!!contributor!!", ($contributor ? $contributor : $_SESSION ['id_empr_session']), $form );
		
		// id de l'entité liée en base SQL
		if ($pmb_id) {
			$form = str_replace ( "!!onto_form_submit!!", '', $form );
		} else {
			if ($sub_form) {
				$submit_msg = $msg ['onto_contribution_inter_submit_button'];
			} else {
				$submit_msg = $msg ['onto_contribution_submit_button'];
			}
			$form = str_replace ( "!!onto_form_submit!!", '<input type="button" id="' . onto_common_uri::get_name_from_uri ( $this->uri, $this->onto_class->pmb_name ) . '_onto_contribution_save_button" class="bouton" name="' . onto_common_uri::get_name_from_uri ( $this->uri, $this->onto_class->pmb_name ) . '_onto_contribution_save_button" value="' . htmlentities ( $submit_msg, ENT_QUOTES, $charset ) . '"/>', $form );
		}
		
		$form = str_replace ( "!!onto_form_push!!", (!$sub_form ? '<input type="button" id="' . onto_common_uri::get_name_from_uri ( $this->uri, $this->onto_class->pmb_name ) . '_onto_contribution_push_button" class="bouton" name="' . onto_common_uri::get_name_from_uri ( $this->uri, $this->onto_class->pmb_name ) . '_onto_contribution_push_button" value="' . htmlentities ( $msg ['onto_contribution_push_button'], ENT_QUOTES, $charset ) . '"/>' : ''), $form );
		
		$form = str_replace ( "!!onto_form_history!!", '<span class="cancel_part"><input type="button" class="bouton" onclick="history.go(-1);" value="' . htmlentities ( $msg ['76'], ENT_QUOTES, $charset ) . '"/></span>', $form );
		
		$script = "
				function confirmation_delete() {
        			if (confirm('" . $msg ['onto_contribution_delete_confirm'] . "')) {
        				document.location = './catalog.php?categ=contribution_area&sub=" . $sub . "&id=" . $this->get_id () . "&action=delete';
        			}
   				}";
		$form = str_replace ( "!!onto_form_del_script!!", $script, $form );
		$form = str_replace ( "!!onto_form_delete!!", '<input type="button"  id="'.onto_common_uri::get_name_from_uri($this->uri,$this->onto_class->pmb_name).'_onto_contribution_delete_button" class="bouton" onclick=\'confirmation_delete();\' value="'.htmlentities($msg['onto_contribution_delete_button'], ENT_QUOTES, $charset).'"/>', $form );
		$form = str_replace ( '!!document_title!!', addslashes ( $this->onto_class->label ), $form );
		
		$valid_js = "var " . $prefix_uri . "_validations = [" . $valid_js . "];";
				
		$form = str_replace ( "!!onto_datasource_validation!!", $valid_js, $form );
		$form = str_replace ( "!!onto_form_name!!", onto_common_uri::get_name_from_uri ( $this->uri, $this->onto_class->pmb_name ), $form );
		$form .= $end_form;
		return $form;
	} // end of member function get_form
	
	/**
	 * Renvoie un tableau des déclarations associées à l'instance
	 *
	 * @return onto_assertion
	 * @access public
	 */
	public function get_assertions() {
		global $form_id, $form_uri, $sub, $sub_form, $parent_scenario_uri, $contributor;
	
		$assertions = array();
	
		// On construit manuellement l'assertion type
		$assertions[] = new onto_assertion($this->uri, "http://www.w3.org/1999/02/22-rdf-syntax-ns#type", $this->onto_class->uri, "", array('type'=>"uri"));
		
		foreach ($this->datatypes as $property => $datatypes) {
			/* @var $datatype onto_common_datatype */
			foreach ($datatypes as $datatype) {
				if(get_class($datatype) == 'onto_common_datatype_merge_properties'){
					$class = new onto_common_class($datatype->get_value_type(),$this->onto_class->get_ontology());
					$class->set_pmb_name(explode('#', $datatype->get_value_type())[1]);
						
					$sub_item = new onto_common_item($class, $datatype->get_value());
					$sub_item->get_values_from_form();
					if(onto_common_uri::is_temp_uri($sub_item->get_uri())){
						$sub_item->replace_temp_uri();
					}
					if($sub_item->check_values()){
						$assertions = array_merge($assertions, $sub_item->get_assertions());
						$assertions[] = new onto_assertion($this->uri, $property, $sub_item->get_uri(), $datatype->get_value_type(), $datatype->get_value_properties());
					}
						
				}else{
					$assertions[] = new onto_assertion($this->uri, $property, $datatype->get_raw_value(), $datatype->get_value_type(), $datatype->get_value_properties());
					if($this->onto_class->get_property($property)->inverse_of){
						$assertions[] = new onto_assertion($datatype->get_raw_value(), $this->onto_class->get_property($property)->inverse_of->uri, $this->uri, $this->onto_class->uri);
					}
				}
			}
		}
	
		//on ajoute le sub
		if ($sub) {
			$assertions[] = new onto_assertion($this->uri, "http://www.pmbservices.fr/ontology#sub", $sub, "", array('type'=>"literal"));
		}
		//on ajoute l'id du formulaire en cours
		if ($form_id) {
			$assertions[] = new onto_assertion($this->uri, "http://www.pmbservices.fr/ontology#form_id", $form_id, "", array('type'=>"literal"));
		}
		//on ajoute l'uri du formulaire en cours
		if ($form_uri) {
			$assertions[] = new onto_assertion($this->uri, "http://www.pmbservices.fr/ontology#form_uri", $form_uri, "", array('type'=>"literal"));
		}
		// On ajoute le contributeur
		if ($contributor) {
			$assertions[] = new onto_assertion($this->uri, "http://www.pmbservices.fr/ontology#has_contributor", $contributor, "", array('type'=>"literal"));
		}
		// On ajoute le sub_form
		if ($sub_form) {
			$assertions[] = new onto_assertion($this->uri, "http://www.pmbservices.fr/ontology#sub_form", $sub_form, "", array('type'=>"literal"));
		}
		// uri du scenario
		if ($parent_scenario_uri) {
			$assertions[] = new onto_assertion($this->uri, "http://www.pmbservices.fr/ontology#parent_scenario_uri", $parent_scenario_uri, "", array('type'=>"literal"));
		}
		//timestamp
		$assertions[] = new onto_assertion($this->uri, "http://www.pmbservices.fr/ontology#last_edit", time(), "", array('type'=>"literal"));
	
		return $assertions;
	}
	
	private function merge_datatypes() {
	    $datatypes = $this->datatypes;
	    $this->datatypes = [];
	    $this->set_assertions($this->get_assertions_from_active_properties());
	    foreach ($datatypes as $uri => $values) {
            $this->datatypes[$uri] = $values;    
	    }
	}
	
	public function get_label($uri_property){
	    global $lang;
	    
	    if (!is_array($uri_property)) {
	        return parent::get_label($uri_property);
	    }
	    $values = [];
	    foreach ($uri_property as $uri) {
	        $values = array_merge($values, $this->datatypes[$uri]);
	    }
	    $label = "";
	    $default_label = "";
	    if(count($values) == 1){
	        $label = $values[0]->get_value();
	    }else if(count($values) > 1){
	        foreach($values as $value){
	            if ($label) {
	                $label .= ", ";
	            }
	            if ($default_label) {
	                $default_label .= ", ";
	            }
	            if($value->offsetget_value_property("lang") == ""){
	                $default_label .= $value->get_value();
	            }
	            if(!$default_label){
	                $default_label .= $value->get_value();
	            }
	            if($value->offsetget_value_property("lang") == substr($lang,0,2)){
	                $label .= $value->get_value();
	            }
	        }
	        if(!$label) $label = $default_label;
	    }
	    return $label;
	}
} // end of onto_common_item
