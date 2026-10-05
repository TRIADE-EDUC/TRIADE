<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_item.class.php,v 1.95.4.1 2025/01/30 09:08:05 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($include_path.'/templates/onto/contribution/onto_contribution_item.tpl.php');
require_once($class_path.'/contribution_area/contribution_area_scenario.class.php');
require_once($class_path.'/contribution_area/contribution_area_item.class.php');
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
	 * identifiant brouillon de l'item
	 */
	protected $draft_identifier;
	
	/**
	 * identifiant de l'item
	 */
	protected $identifier = 0;
	
	/**
	 * Date de la dernière edition
	 */
	protected $last_edit;
	
	/**
	 * Propriété précisant qu'il s'agit ou non d'un brouillon
	 */
	protected $is_draft = false;
	
	private $isbd = '';
	
	private const IS_DRAFT = true;
	
	private const NOT_DRAFT = false;
	
	private const IS_ENTITY = true;
	
	private const NOT_ENTITY = false;
	
	/**
	 * Appel les fonctions static get_form et articule le formulaire de l'item courant
	 *
	 * on itère sur les propriétés de l'onto_class, on envoi aussi le datatype si présent
	 * 
	 * @param string $prefix_url  Préfixe de l'url de soumission du formulaire
	 * @param string $flag  Nom du flag à utiliser pour limiter aux champs concernés
	 *  
	 * @return string
	 * @access public
	 */
	public function get_form($prefix_url="",$flag="",$action="save") {
	    global $msg, $charset, $ontology_tpl, $area_id, $sub_form, $form_id;
	    global $form_uri, $sub, $scenario, $pmb_id, $contributor, $pmb_contribution_opac_auto_save_draft;
	    global $is_draft, $ontology_contribution_tpl;
	    
		//gestion des droits
		global $gestion_acces_active, $gestion_acces_empr_contribution_scenario, $gestion_acces_contribution_moderator_empr;
		if ($gestion_acces_active == 1) {
			$ac = new acces();
			if ($gestion_acces_empr_contribution_scenario == 1) {
				$dom_5 = $ac->setDomain(5);
			}
			if ($gestion_acces_contribution_moderator_empr == 1) {
				$dom_6 = $ac->setDomain(6);
			}
		}
		
		//lors de la première instance de notre contribution, on renseigne les champs avec les valeurs par défaut
		$is_new = onto_common_uri::is_temp_uri($this->uri);
		if($is_new && !$this->is_draft){
			$this->set_assertions($this->get_assertions_from_active_properties());
		} else {
		    $this->merge_datatypes();
		}
		
		// On indique si c'est une contribution draft
		$contribution_draft = self::NOT_DRAFT;
		if ($is_new || (isset($this->is_draft) && $this->is_draft) ) {
    		$contribution_draft = self::IS_DRAFT;
		}
		
		// $is_draft provient de l'item parent
		// permet de savoir si on affiche le bouton ou pas.
		if (isset($is_draft)) {
		    if ($is_draft) {
		        $contribution_draft = self::IS_DRAFT;
    		}else {
    		    $contribution_draft = self::NOT_DRAFT;
    		}
		}
		
		$temp_datatype_tab = $this->order_datatypes();
		
		$end_form = '';
		$form = '';
				
		if(!$sub_form){
    		$form .= jscript_unload_question();
			if ($scenario) {
				$contribution_area_scenario = new contribution_area_scenario($scenario,$area_id);
				$form .="
                <ul class='uk-breadcrumb breadcrumb'>
					<li><a href='./empr.php?tab=contribution_area&lvl=contribution_area_new'>".$msg["empr_menu_contribution_area_new"]."</a></li>
					<li class='breadcrumb-item'><span class='breadcrumb_home'><a href='./index.php?lvl=contribution_area&sub=area&id=".$area_id."'>".htmlentities($contribution_area_scenario->get_area()->get_title() ?? "", ENT_QUOTES, $charset)."</a></span></li>
					<li class='breadcrumb-item'><span class='elem'><a href='./index.php?lvl=contribution_area&sub=scenario&id=".$area_id."&scenario=".$contribution_area_scenario->get_id()."'>".htmlentities($contribution_area_scenario->get_name(), ENT_QUOTES, $charset)."</a></span></li>
				</ul>";
			}			
			if ($contribution_draft){
    			$message = '';
    			if ($this->is_draft){
        			$save_date = new DateTime();
        			$save_date->setTimestamp($this->get_last_edit());
    			    $message = str_replace(["%d", "%h"], [$save_date->format('d/m/Y'), $save_date->format('H:i:s')], $msg['contribution_save_button_draft_time']);
    			}
    			
    			//Chargement du script JS avec paramètre enregistrement automatique
    		    $form.= "
                    <div id='draft_manager' class='contributionRight'>
                        <label id='last_save'>".$message."</label>
                          <script>
                        	require(['dojo/ready', 'apps/pmb/contribution/DraftManager'], function(ready, DraftManager){
                        	     ready(function() {
                        	     	new DraftManager(".(!empty($pmb_contribution_opac_auto_save_draft) ? true : false).");
                        	     });
                        	});
                            function event_save_draft(sub_form=false){
                                require(['dojo/topic'], function(topic){
                                    topic.publish('SaveDraft', 'save', {subForm: sub_form});
                                });
                            }
                        </script>
                    </div>
                ";
 			}
			$form.= '
					<div class="contributionDivContainer">
						<div data-dojo-type="apps/pmb/contribution/form_progress/FormContainer" data-dojo-props="id : \'ContributionFormContainer\'" doLayout="false" style="width: 100%">';
			$form.= '		<div title="!!onto_form_title!!" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="selected:true">';
			$end_form .= "</div>
						</div>
                        <script>ajax_parse_dom();</script>
					</div>";
		}
		
		$comment = $this->contribution_area_form->get_comment();
		if ($comment) {
		    $comment = "<div class='contribution_area_form_comment'>" . $comment . "</div>";
		}
		$form.= $comment . $ontology_tpl['form_body'];
		
		$prefix_uri = "";
		if (isset(explode('#',$this->uri)[1])) {
    		if (!is_numeric((explode('#',$this->uri)[1]))) {
    			$prefix_uri = strtolower(explode('#',$this->uri)[1]);
    		} else {
    			$prefix_uri = strtolower($sub."_".explode('#',$this->uri)[1]);
    		}
		}
		
		$form=str_replace("!!uri!!",$this->uri,$form);
		$form=str_replace("!!prefix_uri!!",$prefix_uri,$form);
		$form=str_replace("!!onto_form_scripts!!",(!$sub_form ? $ontology_tpl['form_scripts'] : $ontology_tpl['form_scripts']), $form);
		$form=str_replace("!!caller!!",rawurlencode(onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name)), $form);
		
		$form=str_replace("!!onto_form_id!!",onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name) , $form);
		$form=str_replace("!!onto_form_action!!",$prefix_url."&action=".$action, $form);
		$form=str_replace("!!onto_form_title!!",htmlentities($this->contribution_area_form->get_name(),ENT_QUOTES,$charset) , $form);

		$linked_forms = array();
		
		if ($this->contribution_area_form->get_linked_forms()) {
			$linked_forms = $this->contribution_area_form->get_linked_forms();
		}
		
		$linked_scenarios = $this->contribution_area_form->get_linked_scenarios();
		
		$content='';
		$valid_js = "";
		
		/*******TODO : modif temporaire***********/
		$properties = $this->onto_class->get_properties();
		$properties = array_merge($properties, $this->onto_class->get_properties_and_restrictions_from_sub_class_of());
		$properties = array_unique($properties);
		
		sort($properties);
		/**************************************/
		
		if(is_countable($properties) && sizeof($properties)){
			$index = 0;
			foreach($properties as $uri_property){
				$property=$this->onto_class->get_property($uri_property);
								
				if((!$flag || (in_array($flag,$property->flags))) && isset($property->pmb_extended) && (!$property->is_undisplayed())){
					$datatype_class_name=$this->resolve_datatype_class_name($property);
					
					$datatype_ui_class_name=$this->resolve_datatype_ui_class_name($datatype_class_name,$property,$this->onto_class->get_restriction($property->uri));
					
					// On encapsule dans des divs movables pour l'édition de la grille de saisie
					$movable_div = $ontology_contribution_tpl['form_movable_div'];
					$movable_div = str_replace('!!movable_index!!', $index, $movable_div);				
					
					$property = $this->format_property($property, $linked_forms, $contribution_draft, self::NOT_ENTITY, $linked_scenarios);
					
					//propriété obligatoire					
					if (!empty($property->pmb_extended['mandatory'])) {					
						$this->onto_class->get_restriction($property->uri)->set_min('1');
					}	

					//enregistrement de l'espace
// 					if ($property->pmb_datatype == "http://www.pmbservices.fr/ontology#resource_selector") {
// 						$property->pmb_extended['values_from_area'] = self::get_values_from_area($property->range[0]);
// 					}
					
					//propriété cachée
					if (!empty($property->pmb_extended['hidden'])) {
					    //propriété cachée
					    $template = $this->get_property_hidden_template($property, $datatype_ui_class_name, $temp_datatype_tab);
					} else {
					    $template = $this->get_property_template($property, $datatype_ui_class_name, $temp_datatype_tab, $flag);
					}
					
					$movable_div = str_replace('!!property_name!!', $property->pmb_name, $movable_div);
					$movable_div = str_replace('!!datatype_ui_form!!', $template, $movable_div);
					$movable_div = str_replace('!!data_pmb_uniqueid!!', $form_uri.'_'.$property->pmb_name, $movable_div);
					$content .= $movable_div;
					
					if($valid_js){
						$valid_js.= ",";
					}
					$valid_js.= $datatype_ui_class_name::get_validation_js($this->uri,$property,$this->onto_class->get_restriction($property->uri),(isset($temp_datatype_tab[$property->uri]) ? $temp_datatype_tab[$property->uri][$datatype_ui_class_name] : null),onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name),$flag);
					$index++;
				}
			}
		}
		
		$content.= "<input type='hidden' name='sub_form' value='".$sub_form."'>";
		
		if (!empty($this->get_additionnal_data())) {
		    $additionnal_data = json_encode($this->get_additionnal_data()) ?? "{}";
		    $content .= "<input id='additionnal_data' type='hidden' name='additionnal_data' value='{$additionnal_data}'>";
		} else {
		    $content .= "<input id='additionnal_data' type='hidden' name='additionnal_data' value='{}'>";		    
		}
			
		$form=str_replace("!!onto_form_content!!",$content , $form);
		
		$scenario_uri = '';
		if (isset($scenario)) {
			$form=str_replace("!!parent_scenario_uri!!",$scenario , $form);
			$scenario_uri = 'http://www.pmbservices.fr/ca/Scenario#'.$scenario;
		} else {
			$form=str_replace("!!parent_scenario_uri!!",'', $form);
		}
		
		$edition_granted = true;
		$validation_granted = true;
		if ($contributor) {
			//droit de modification / validation sur ce contributeur
		    if (isset($dom_6)) {
				$edition_granted = ($dom_6->getRights($_SESSION['id_empr_session'],$contributor, 8) ? true : false);
				$validation_granted = ($dom_6->getRights($_SESSION['id_empr_session'],$contributor, 16) ? true : false);
			}
		}
		$form=str_replace("!!contributor!!",($contributor ? $contributor : $_SESSION['id_empr_session']), $form);
		
		//id de l'entité liée en base SQL
		if ($pmb_id) {
			$form=str_replace("!!onto_form_submit!!",'' , $form);
		} else {
		    $button_class = "contribution_main_save_button";
			if($sub_form) {
                $button_class = "contribution_save_button";
				$submit_msg = $msg['onto_contribution_inter_submit_button'];
			}else {
				$submit_msg = $msg['onto_contribution_submit_button'];
			}
			
			$acces_right = ($is_new ? 4 : 8);
			if ($scenario_uri && isset($dom_5) && !$dom_5->getRights($_SESSION['id_empr_session'],onto_common_uri::get_id($scenario_uri), $acces_right)) {
				$edition_granted = false;
			}
			if ($edition_granted) {
				$form=str_replace("!!onto_form_submit!!",'<input type="button" id="'.onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name).'_onto_contribution_save_button" class="bouton '.$button_class.'" name="'.onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name).'_onto_contribution_save_button" title="'.htmlentities($msg['onto_contribution_push_title'],ENT_QUOTES,$charset).'" value="'.htmlentities($submit_msg,ENT_QUOTES,$charset).'"/>' , $form);
				
			} else {
				$form=str_replace("!!onto_form_submit!!",'' , $form);
			}
		}
    
		if ($contribution_draft && isset($ontology_tpl['onto_contribution_save_button_draft'])){
    		$onto_contribution_save_button_draft = $ontology_tpl['onto_contribution_save_button_draft'];
    		$onto_contribution_save_button_draft = str_replace("!!sub_params!!", $sub_form, $onto_contribution_save_button_draft);
		    $form=str_replace("!!onto_form_save_draft!!",$onto_contribution_save_button_draft , $form);
		} else {
    		$form=str_replace("!!onto_form_save_draft!!",'', $form);
		}
		
		//droit de validation
		if ($scenario_uri && isset($dom_5) && !$dom_5->getRights($_SESSION['id_empr_session'],onto_common_uri::get_id($scenario_uri), 16)) {
			$validation_granted = false;
		}
		if ($validation_granted) {
			$form=str_replace("!!onto_form_push!!",(!$sub_form ? '<input type="button" id="'.onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name).'_onto_contribution_push_button" class="bouton contribution_push_button" name="'.onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name).'_onto_contribution_push_button" title="'.htmlentities($msg['onto_contribution_save_push_title'],ENT_QUOTES,$charset).'" value="'.htmlentities($msg['onto_contribution_push_button'],ENT_QUOTES,$charset).'"/>' : ''), $form);
		} else {
			$form=str_replace("!!onto_form_push!!","", $form);
		}
		
		//Suppression du bouton annuler pour les sous onglets
		if (!$sub_form) {
    		$form=str_replace("!!onto_form_history!!",'<span class="cancel_part"><input type="button" class="bouton" onclick="history.go(-1);" title="'.htmlentities($msg['onto_contribution_cancel_title'],ENT_QUOTES,$charset).'" value="'.htmlentities($msg['76'],ENT_QUOTES,$charset).'"/></span>' , $form);
		}else{
    		$form=str_replace("!!onto_form_history!!", '', $form);
		}
		
		if (!$is_new || $this->is_draft) {
			$script="
					function confirmation_delete_".$this->get_id()."() {
        				if (confirm('".$msg['onto_contribution_delete_confirm'] ."')) {";
			if ($sub_form){
    			$script .="
                            require(['dojo/topic'], function(topic){
                                topic.publish('deleteContrib', 'deleteSubContrib', {id: '".$this->get_id()."', sub : '".$sub."'});
                            });
        				";
			} else {
    			$script .= "
        					document.location = './index.php?lvl=contribution_area&sub=".$sub."&id=".$this->get_id()."&action=delete';";
			}
            $script .="
        				}
   					}";
			$form=str_replace("!!onto_form_del_script!!",$edition_granted ? $script : "" , $form);
			$form=str_replace("!!onto_form_delete!!",$edition_granted ? '<input type="button"  id="'.onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name).'_onto_contribution_delete_button" title="'.htmlentities($msg['onto_contribution_delete_title'],ENT_QUOTES,$charset).'" class="bouton" onclick=\'confirmation_delete_'.$this->get_id().'();\' value="'.htmlentities($msg['onto_contribution_delete_button'],ENT_QUOTES,$charset).'"/>' :'' , $form);			    
		} else {
			$form=str_replace("!!onto_form_del_script!!",'' , $form);
			$form=str_replace("!!onto_form_delete!!",'' , $form);
		}
		$form = str_replace('!!document_title!!', addslashes($this->onto_class->label), $form);
		
		$valid_js = "var ".$prefix_uri."_validations = [".$valid_js."];";		
		
		$form = str_replace("!!onto_datasource_validation!!", $valid_js, $form);
		$form = str_replace("!!onto_form_name!!", onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name), $form);
		$form = str_replace("!!caller_contribution!!", onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name), $form);
		
		$form .= $end_form;
		return $form;
	} // end of member function get_form
	
	/**
	 * retourne le formulaire de contribution lié à l'item
	 * @return contribution_area_form
	 */
	public function get_contribution_area_form() {
		return $this->contribution_area_form;
	}
	
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
	 * Renvoie un tableau des déclarations associées à l'instance
	 *
	 * @return onto_assertion
	 * @access public
	 */
	public function get_assertions_from_active_properties() {
		$assertions = array();
	
		// On construit manuellement l'assertion type
		$assertions[] = new onto_assertion($this->uri, "http://www.w3.org/1999/02/22-rdf-syntax-ns#type", $this->onto_class->uri, "", array('type'=>"uri"));
		$contribution_area_store = new contribution_area_store();
		
		foreach($this->onto_class->get_properties() as $uri_property) {
		    
			$property=$this->onto_class->get_property($uri_property);
			
			if (empty($property->pmb_extended)) {
		        $restriction = $this->onto_class->get_restriction($uri_property);
		        if ($restriction->get_min() >= 1 && !empty($property->default_value)) {
		            $property->pmb_extended['default_value'] = [$property->default_value];
		            $property->pmb_extended['hidden'] = 1;
		        }
			}
			
			if ($property->pmb_extended) {
				if (onto_common_uri::is_temp_uri($this->uri) && isset($_SESSION['contribution_default_fields']) && !empty($_SESSION['contribution_default_fields'][$uri_property])) {
					$property->pmb_extended['default_value'] = $_SESSION['contribution_default_fields'][$uri_property];
				}
				$datatype_class_name=$this->resolve_datatype_class_name($property);
				if(!empty($property->pmb_extended['default_value']) && count($property->pmb_extended['default_value'])) {
					foreach ($property->pmb_extended['default_value'] as $bnode => $bnode_value) {
					    
					    $range = $this->onto_class->get_property_range($uri_property);
					    if (empty($bnode_value['type']) && !empty($range)) {
					        // on prend le premier range par défaut
					        $bnode_value['type'] = $range[0];
					    }
					    if (!empty($range) && $bnode_value['type'] != $range[0]) {
					        // le type ne correspond pas avec les donner dans le store.
					        $bnode_value['type'] = $range[0];
					    }
					    
					    $value_properties = array();
					    foreach ($bnode_value as $pmb_name => $property_value) {
					        
					        if ($pmb_name == "type" || $pmb_name == "value") {
					            continue;
					        }
					        switch ($pmb_name) {
					            case "lang":
					                $value_properties["lang"] = $bnode_value['lang'];
					                break;
					                
					            case "display_label":
					                $value_properties["display_label"] = encoding_normalize::utf8_decode($property_value); 
					                break;
					                
					            case "assertions":
					                if (!isset($value_properties["assertions"])) {
					                    $value_properties["assertions"] = array();
					                }
					                foreach($bnode_value['assertions'] as $prop => $value) {
					                    $value_properties["assertions"][] = new onto_assertion($bnode_value['value'] ?? "", $prop, $value);
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
					                $value_properties["assertions"][] = new onto_assertion($bnode_value['value'] ?? "", $uri, $property_value);                                    ;
					                break;
					        }
					    }

						if (!isset($bnode_value['value'])) {
						    $bnode_value['value'] = "";
						}
						
						$datatype = new $datatype_class_name($bnode_value['value'], $bnode_value['type'], $value_properties);
						$assertions[] = new onto_assertion($this->uri, $property->uri, $datatype->get_raw_value(), $datatype->get_value_type(), $datatype->get_value_properties());
					}
				}
				if($this->onto_class->get_property($property->uri)->inverse_of){
					$assertions[] = new onto_assertion($datatype->get_raw_value(), $this->onto_class->get_property($property->uri)->inverse_of->uri, $this->uri, $this->onto_class->uri);
				}
			}
		}
		unset($_SESSION['contribution_default_fields']);
		return $assertions;
	} // end of member function get_assertions
	
	/**
	 * Renvoie un tableau des déclarations associées à l'instance
	 *
	 * @return onto_assertion
	 * @access public
	 */
	public function get_assertions() {
	    global $form_id, $form_uri, $sub, $sub_form, $parent_scenario_uri, $contributor, $is_draft;
	    global $identifier;
		//$assertions = parent::get_assertions();
		
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
		// Contribution brouillon
		if ($is_draft) {
		    $assertions[] = new onto_assertion($this->uri, "http://www.pmbservices.fr/ontology#is_draft", $is_draft, "", array('type'=>"literal"));
    		if (empty($this->draft_identifier)) {
    		    // On créer un identifient unique
    	        $this->draft_identifier = "0.".round(microtime(true)*10000);
    		}
		    $assertions[] = new onto_assertion($this->uri, "http://www.pmbservices.fr/ontology#draft_identifier", $this->draft_identifier, "", array('type'=>"literal"));
		}
		//timestamp
		$assertions[] = new onto_assertion($this->uri, "http://www.pmbservices.fr/ontology#last_edit", time(), "", array('type'=>"literal"));

		//on ajoute l'identifier
		if ($identifier) {
		    $assertions[] = new onto_assertion($this->uri, "http://www.pmbservices.fr/ontology#identifier", $identifier, "", array('type'=>"literal"));
		}
		
		return $assertions;
	}
	
	/**
	 * Instancie les datatypes à partir des triplets du store
	 *
	 * @param onto_assertion assertions Tableau des déclarations à associer à l'instance
	
	 * @return void
	 * @access public
	 */
	public function set_assertions($assertions) {
	    $temp_dataype = $this->datatypes;
	    $this->datatypes = [];
	    
	    /* @var $assertion onto_assertion */
		foreach ($assertions as $assertion) {
		    $range = $this->onto_class->get_property_range($assertion->get_predicate());
		    
		    // On supprime les tests pour avec des proprietes hybrides
		    // Ex : Schemas de concept stockes en litteral
		    //
		    // if (count($range) && (in_array($assertion->get_object_type(), $range) || $assertion->get_object_type() == "http://www.w3.org/2000/01/rdf-schema#range" || $assertion->get_object_type() == "merge_properties") ) {
		    
			if (!empty($range) && is_countable($range) && count($range)) {
				$property = $this->onto_class->get_property($assertion->get_predicate());
				$datatype_class_name=$this->resolve_datatype_class_name($property);
				$datatype_ui_class_name=$this->resolve_datatype_ui_class_name($datatype_class_name,$property,$this->onto_class->get_restriction($assertion->get_predicate()));
				$datatype=new $datatype_class_name($assertion->get_object(), $assertion->get_object_type(), $assertion->get_object_properties());
				$datatype->set_datatype_ui_class_name($datatype_ui_class_name,$this->onto_class->get_restriction($assertion->get_predicate()));
				$this->datatypes[$assertion->get_predicate()][]=$datatype;
			} else {
			    if ($assertion->get_predicate() == "http://www.pmbservices.fr/ontology#draft_identifier") {
			        $this->draft_identifier = $assertion->get_object();
			    }
			    if ($assertion->get_predicate() == "http://www.pmbservices.fr/ontology#last_edit") {
			        $this->last_edit = $assertion->get_object();
			    }
			    if ($assertion->get_predicate() == "http://www.pmbservices.fr/ontology#is_draft") {
			        $this->is_draft= ($assertion->get_object() ? true : false) ;
			    }
			}
		}
		
		if (empty($this->datatypes)) {
		    // si on n'a rien mis dans $this->datatypes on récupère les anciens.
		    $this->datatypes = $temp_dataype;
		}
		
		return true;
	} // end of member function set_assertions
	
	/**
	 * Instancie les datatypes à partir des données postées du formulaire
	 *
	 * @return void
	 * @access public
	 */
	public function get_values_from_form() {
	    global $additionnal_data;
	    
		$this->datatypes = array();
		$prefix = onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name);
	
		if(is_countable($this->onto_class->get_properties())){
			foreach($this->onto_class->get_properties() as $uri_property){
				$property=$this->onto_class->get_property($uri_property);
				$datatype_class_name = $this->resolve_datatype_class_name($property);
				$this->datatypes = array_merge($this->datatypes, $datatype_class_name::get_values_from_form($prefix, $property, $this->uri));
			}
		}
	
		foreach($this->onto_class->get_properties_and_restrictions_from_sub_class_of() as $uri_property){
			$property=$this->onto_class->get_property($uri_property);
			$datatype_class_name = $this->resolve_datatype_class_name($property);
			$this->datatypes = array_merge($this->datatypes, $datatype_class_name::get_values_from_form($prefix, $property, $this->uri));

		}
	
		foreach ($this->datatypes as $uri_property => $datatype) {
			if (!in_array($uri_property,$this->onto_class->get_properties())) {
				$this->onto_class->set_property($this->onto_class->get_property($uri_property));
			}
		}
		
		$additionnal_data = stripslashes($additionnal_data);
		$additionnal_data = json_decode($additionnal_data, true);
		$this->set_additionnal_data($additionnal_data);
		
		$this->onto_class->get_restrictions();
		return $this->datatypes;
	} // end of member function get_values_from_form
	
	private function merge_datatypes() {
	    $datatypes = $this->datatypes;
	    $this->datatypes = [];
	    $this->set_assertions($this->get_assertions_from_active_properties());
	    if(!empty($datatypes)) {
	    	foreach ($datatypes as $uri => $values) {
	        	$this->datatypes[$uri] = $values;
	    	}
		}
	}
	
	public function get_label($uri_property){
	    global $lang, $msg;
	    
	    if (!is_array($uri_property)) {
	        $label = parent::get_label($uri_property);
	        if (empty($label)){
	            $label = $msg['contribution_draft_name'];
	        }
	        return $label;
	    }	    
	    $values = [];
	    foreach ($uri_property as $uri) {
	        if (!empty($this->datatypes[$uri])) {
	            $values = array_merge($values, $this->datatypes[$uri]);
	        }
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
	    
	    if (empty($label)){
	        if ($this->isbd){
	            return $this->isbd;
	        }
	        $label = $msg['contribution_draft_name'];
	    }
	    return $label;
	}
	
	/**
	 * methode appelee apres la sauvegarde l'item
	 */
	public function post_save() {
	    $this->update_isbd();
	}
	
	private function update_isbd() {
	    global $include_path;
	    $isbd = "";
	    $type = $this->get_onto_class_pmb_name();
	    $template_path = "";
	    $store = new contribution_area_store();
	    switch (true) {
	        case file_exists("$include_path/templates/contribution_area/isbd/".$type."_subst.html") :
	            $template_path = "$include_path/templates/contribution_area/isbd/".$type."_subst.html";
	            break;
	        case file_exists("$include_path/templates/contribution_area/isbd/".$type.".html") :
	            $template_path = "$include_path/templates/contribution_area/isbd/".$type.".html";
	            break;
	        case (strpos($type, "authperso") == 0 && file_exists("$include_path/templates/contribution_area/isbd/authperso_subst.html")) :
	            $template_path = "$include_path/templates/contribution_area/isbd/authperso_subst.html";
	            break;
	        case (strpos($type, "authperso") == 0 && file_exists("$include_path/templates/contribution_area/isbd/authperso.html")) :
	            $template_path = "$include_path/templates/contribution_area/isbd/authperso.html";
	            break;
	        case file_exists("$include_path/templates/contribution_area/isbd/gabarit.html") :
	            $template_path = "$include_path/templates/contribution_area/isbd/gabarit.html";
	            break;
	    }
	    if($template_path) {
	        $contribution = new contribution_area_item($this->uri);
	        $h2o = H2o_collection::get_instance($template_path);
	        $isbd = $h2o->render(['contribution' => $contribution]);
	    } else {
	        $isbd = contribution_area_forms_controller::get_display_label($this->uri);
	    }
	    //delete / insert
	    $query = "delete {
				<".$this->uri."> pmb:isbd ?o
			}";
	    $store->get_datastore()->query($query);
	    $query = "insert into <pmb> {
				<".$this->uri."> pmb:isbd '".addslashes($isbd)."'
			}";
	    $store->get_datastore()->query($query);	 
	    //Traitement particulier pour les authperso
	    if(strpos($this->uri,'authperso') !== false){
    	    $query = "delete {
    				<".$this->uri."> pmb:displayLabel ?o
    			}";
    	    $store->get_datastore()->query($query);
    	    $query = "insert into <pmb> {
    				<".$this->uri."> pmb:displayLabel '".addslashes($isbd)."'
    			}";
    	    $store->get_datastore()->query($query);	    
	    }
	    $this->isbd = $isbd;
	}
	
	public function get_draft_identifier() {
	    return $this->draft_identifier;
	}
	
	public function get_last_edit() {
	    return $this->last_edit;
	}
	
	/**
	 * Suppression d'une fichier liée au document numérique
	 * Le pmb_name doit être égal à "docnum"
	 * 
	 * @return boolean
	 */
	public function remove_file_uploads() 
	{
	    if ($this->item->onto_class->pmb_name != "docnum") {
	        return FALSE;
	    }
	    
	    $file_name = "";
	    $upload_directory = 0;
	    $success = FALSE;
	    
	    $docnum_files = $this->datatypes["http://www.pmbservices.fr/ontology#docnum_file"] ?? array();
	    if (!empty($docnum_files) && !empty($docnum_files[0])) {
	        $file_name = $docnum_files[0]->get_value();
	    }
	    
	    $upload_directories = $this->datatypes["http://www.pmbservices.fr/ontology#upload_directory"] ?? array();
	    if (!empty($upload_directories) && !empty($upload_directories[0])) {
	        $upload_directory = $upload_directories[0]->get_value();
	    }
	    
	    if (!empty($file_name) && !empty($upload_directory)) {
	        
            $upload_folder = new upload_folder($upload_directory);
            $repertoire_path = $upload_folder->repertoire_path;
            if (substr($repertoire_path, -1) != "/") {
                $repertoire_path .= "/";
            }
            
            $file_path = $repertoire_path.explnum::clean_explnum_file_name($file_name);
            
            /**
             * On vérifie si le fichier existe et que l'on a bien les autorisations nécessaires 
             * pour modifier/supprimer un fichier.
             */
            if (is_file($file_path) && is_writable($file_path)) {
                $success = unlink($file_path);
            }
	    }
	    
	    return $success;
	}
	
	/**
	 * Retourne le formulaire pour une entité du fond
	 * 
	 * @param string $prefix_url Préfixe de l'url de soumission du formulaire
	 * @param string $flag Nom du flag à utiliser pour limiter aux champs concernés
	 * @param string $action
	 * @return string
	 */
	public function get_form_entity(string $prefix_url = "", string $flag = "", string $action = "save") 
	{
	    global $ontology_tpl, $sub_form, $sub, $contributor, $charset, $form_uri, $msg, $lvl_redirect, $sub_tab;
	    global $scenario_uri, $create_entity, $ontology_contribution_tpl;
	    
	    //gestion des droits
	    global $gestion_acces_active, $gestion_acces_empr_contribution_scenario;
	    if ($gestion_acces_active == 1) {
	        $ac = new acces();
	        if ($gestion_acces_empr_contribution_scenario == 1) {
	            $dom_5 = $ac->setDomain(5);
	        }
	    }
	    
	    // Si on a aucun identifier en edition on fait la redirection
	    if (empty($this->identifier) && !$create_entity) {
	        $template = $msg['empr_contribution_area_unauthorized'];
	        if (!$sub_tab) {
	            $template .='<script>
                            window.location = "./empr.php?tab=contribution_area&lvl='.(!empty($lvl_redirect) ? $lvl_redirect : 'contribution_area_list') .'"
                      </script>';
	        }
	        return $template;
	    }
	    
	    $form = "";
	    $end_form = "";
	    
        $this->merge_datatypes();
	    $temp_datatype_tab = $this->order_datatypes();
	    
	    if(!$sub_form){
	        $form .= jscript_unload_question();
	        $form.= '
					<div class="contributionDivContainer">
						<div data-dojo-type="apps/pmb/contribution/form_progress/FormContainer" data-dojo-props="id : \'ContributionFormContainer\'" doLayout="false" style="width: 100%">';
	        $form.= '		<div title="!!onto_form_title!!" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="selected:true">';
	        $end_form .= "</div>
						</div>
                        <script>ajax_parse_dom();</script>
					</div>";
	    }
	    
	    // Commentaire du formulaire :
	    $comment = $this->contribution_area_form->get_comment();
	    if ($comment) {
	        $form .= "<div class='contribution_area_form_comment'>" . $comment . "</div>";
	    }
	    
	    // Contenu du formulaire :
	    $form .= $ontology_tpl['form_body'];
	    
	    if (!is_numeric((explode('#',$this->uri)[1]))) {
	        $prefix_uri = explode('#',$this->uri)[1];
	    } else {
	        $prefix_uri = $sub."_".explode('#',$this->uri)[1];
	    }
	    
	    $form = str_replace("!!uri!!",$this->uri,$form);
	    $form = str_replace("!!prefix_uri!!",$prefix_uri,$form);
	    $form = str_replace("!!onto_form_scripts!!",(!$sub_form ? $ontology_tpl['form_scripts'] : $ontology_tpl['form_scripts']), $form);
	    $form = str_replace("!!caller!!",rawurlencode(onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name)), $form);
	    
	    $form = str_replace("!!onto_form_id!!",onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name) , $form);
	    $form = str_replace("!!onto_form_action!!",$prefix_url."&action=".$action, $form);
	    $form = str_replace("!!onto_form_title!!",htmlentities($this->contribution_area_form->get_name(),ENT_QUOTES,$charset) , $form);
	    
	    $linked_forms = array();
	    if ($this->contribution_area_form->get_linked_forms()) {
	        $linked_forms = $this->contribution_area_form->get_linked_forms();
	    }
	    
	    $content='';
	    $valid_js = "";
	    
	    $properties = array();
	    $properties = $this->onto_class->get_properties();
	    $properties = array_merge($properties, $this->onto_class->get_properties_and_restrictions_from_sub_class_of());
	    $properties = array_unique($properties);
	    sort($properties);
	    
	    if(is_countable($properties) && sizeof($properties)){
	        $index = 0;
	        foreach($properties as $uri_property){
	            $property = $this->onto_class->get_property($uri_property);
                $datatype_class_name=$this->resolve_datatype_class_name($property);
                $datatype_ui_class_name=$this->resolve_datatype_ui_class_name($datatype_class_name,$property,$this->onto_class->get_restriction($property->uri));
	            
                // On encapsule dans des divs movables pour l'édition de la grille de saisie
                $movable_div = $ontology_contribution_tpl['form_movable_div'];
                $movable_div = str_replace('!!movable_index!!', $index, $movable_div);
                $movable_div = str_replace('!!property_name!!', $property->pmb_name, $movable_div);

	            if((!$flag || (in_array($flag,$property->flags))) && isset($property->pmb_extended) && (!$property->is_undisplayed())){
	                
	                $property = $this->format_property($property, $linked_forms, self::NOT_DRAFT, self::IS_ENTITY);
	                
	                //propriété obligatoire
	                if (!empty($property->pmb_extended['mandatory'])) {
	                    $this->onto_class->get_restriction($property->uri)->set_min('1');
	                }
	                    
	                if (!empty($property->pmb_extended['hidden'])) {
    	                //propriété cachée
                        $template = $this->get_property_hidden_template($property, $datatype_ui_class_name, $temp_datatype_tab);
                    } else {
                        $template = $this->get_property_template($property, $datatype_ui_class_name, $temp_datatype_tab, $flag);
                    }
                    
                    $movable_div = str_replace('!!datatype_ui_form!!', $template, $movable_div);
	                $movable_div = str_replace('!!data_pmb_uniqueid!!', $form_uri.'_'.$property->pmb_name, $movable_div);
	                $content .= $movable_div;
	                
	                if($valid_js){
	                    $valid_js.= ",";
	                }
	                $valid_js.= $datatype_ui_class_name::get_validation_js($this->uri,$property,$this->onto_class->get_restriction($property->uri),(isset($temp_datatype_tab[$property->uri]) ? $temp_datatype_tab[$property->uri][$datatype_ui_class_name] : null),onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name),$flag);
	                $index++;
	            
	            } elseif (!empty($temp_datatype_tab[$uri_property]) && !empty($datatype_ui_class_name) && !empty($temp_datatype_tab[$uri_property][$datatype_ui_class_name])) {
	                if (!empty($temp_datatype_tab[$uri_property][$datatype_ui_class_name][0])) {
    	                $values = $temp_datatype_tab[$uri_property][$datatype_ui_class_name][0]->get_value();
    	                if (!empty($values)) {
    	                    $movable_div = str_replace('!!datatype_ui_form!!', $datatype_ui_class_name::get_hidden_fields($property, $temp_datatype_tab[$property->uri][$datatype_ui_class_name], onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name)), $movable_div);
        	                $movable_div = str_replace('!!data_pmb_uniqueid!!', $form_uri.'_'.$property->pmb_name, $movable_div);
        	                $content .= $movable_div;
        	                
        	                if($valid_js){
        	                    $valid_js.= ",";
        	                }
        	                $valid_js.= $datatype_ui_class_name::get_validation_js($this->uri,$property,$this->onto_class->get_restriction($property->uri),(isset($temp_datatype_tab[$property->uri]) ? $temp_datatype_tab[$property->uri][$datatype_ui_class_name] : null),onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name),$flag);
        	                $index++;
    	                }
	                }
	            }
	        }
	    }
	    
	    $content.= "<input type='hidden' name='sub_form' value='".$sub_form."'>";
	    if ($this->identifier) {
	        $content.= "<input type='hidden' name='identifier' value='".$this->identifier."'>";
	    }
	    if ($create_entity) {
	        $content.= "<input type='hidden' name='create_entity' value='".$create_entity."'>";
	    }
	    
	    $form = str_replace("!!onto_form_content!!", $content, $form);
        $form = str_replace("!!parent_scenario_uri!!", '', $form);
	    
	    
	    $form = str_replace("!!contributor!!",($contributor ? $contributor : $_SESSION['id_empr_session']), $form);
	    
        // Bouton proposer (save) :
        $form = str_replace("!!onto_form_submit!!", '', $form);
        
        // Bouton enregistrer (draft) :
        $form = str_replace("!!onto_form_save_draft!!", '', $form);
        
        // Bouton valider (push) :
        // Droit de validation sur cette contribution
	    $validation_granted = true;
	    if ($scenario_uri && isset($dom_5) && !$dom_5->getRights($_SESSION['id_empr_session'],onto_common_uri::get_id($scenario_uri), 16)) {
	        $validation_granted = false;
	    }
        $button = "";
        if ($validation_granted) {
            if ($sub_form) {
                $button = '<input type="button" id="'.onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name).'_onto_contribution_save_button" class="bouton" name="'.onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name).'_onto_contribution_save_button" value="'.htmlentities($msg['onto_contribution_push_button'],ENT_QUOTES,$charset).'"/>';
            } else {
                $button = '<input type="button" id="'.onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name).'_onto_contribution_push_button" class="bouton" name="'.onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name).'_onto_contribution_push_button" value="'.htmlentities($msg['onto_contribution_push_button'],ENT_QUOTES,$charset).'"/>';
            }
	    }
	    $form = str_replace("!!onto_form_push!!", $button, $form);
	    
	    // Bouton annuler
        $button = "";
        if (!$sub_form) {
            $button = '<span class="cancel_part"><input type="button" class="bouton" onclick="history.go(-1);" value="'.htmlentities($msg['76'],ENT_QUOTES,$charset).'"/></span>';
	    }
	    $form = str_replace("!!onto_form_history!!", $button, $form);
	    
	    // Bouton supprimer :
        $form = str_replace("!!onto_form_del_script!!", '', $form);
        $form = str_replace("!!onto_form_delete!!", '', $form);
	    
        $form = str_replace('!!document_title!!', addslashes($this->onto_class->label), $form);
	    $form = str_replace("!!onto_datasource_validation!!", "var ".$prefix_uri."_validations = [".$valid_js."];", $form);
	    $form = str_replace("!!onto_form_name!!", onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name), $form);
	    
	    $form .= $end_form;
	    
	    return $form;
	}
	
	/**
	 * Retourne une property formater avec les données dans linked_forms et des paramètre is_draft et is_entity
	 * 
	 * @param onto_common_property $property
	 * @param array $linked_forms
	 * @param bool $is_draft
	 * @param bool $is_entity
	 * @return onto_common_property
	 */
	private function format_property($property, $linked_forms = array(), bool $is_draft = self::NOT_DRAFT, bool $is_entity = self::NOT_ENTITY, $linked_scenarios = array()) 
	{
	    global $area_id, $charset;
        
	    $property->is_entity = $is_entity;
	    $property->is_draft = $is_draft;
	    
	    //gestion des scenarios liés
	    $property->has_multiple_scenarios = false;
	    $property->linked_scenarios = array();
	    $property->scenarios_tab = array();
	    
	    //gestion des formulaires liés
	    $property->has_linked_form = false;
	    $property->linked_forms = array();
	    
	    foreach($linked_scenarios as $scenario_id => $linked_scenario) {
	        if ($linked_scenario['propertyPmbName'] == $property->pmb_name) {
	            if (!in_array($scenario_id, $property->linked_scenarios)) {
	                $property->linked_scenarios[] = $scenario_id;
	                $property->scenarios_tab[] = $linked_scenario;
	            }
	        }
	    }
	    
	    for($i = 0; $i < count($linked_forms); $i++) {
	        //recherche du formulaire lié
	        if ($linked_forms[$i]['propertyPmbName'] == $property->pmb_name) {
	            $form_key = count($property->linked_forms);
	            $property->has_linked_form = true;
	            $property->linked_forms[$form_key] = array();
	            $property->linked_forms[$form_key]['attachment_id'] = $linked_forms[$i]['attachmentId'];
	            $property->linked_forms[$form_key]['area_id'] = $area_id;
	            
	            //id_du formulaire dans la base relationnelle
	            $property->linked_forms[$form_key]['form_id'] = $linked_forms[$i]['formId'];
	            //id du formulaire dans le store
	            $property->linked_forms[$form_key]['form_id_store'] = $linked_forms[$i]['id'];
	            //uri du formulaire dans le store
	            $property->linked_forms[$form_key]['form_uri'] = $linked_forms[$i]['uri'];
	            //type du formulaire
	            $property->linked_forms[$form_key]['form_type'] = $linked_forms[$i]['entityType'];
	            //titre du formulaire
	            $property->linked_forms[$form_key]['form_title'] = $linked_forms[$i]['name'];
	            //URI du scénario parent
	            $property->linked_forms[$form_key]['scenario_uri'] = $linked_forms[$i]['scenarioUri'];
	            $property->linked_forms[$form_key]['scenario_id'] = $linked_forms[$i]['scenarioId'];
	            
	            if (!in_array($linked_forms[$i]['scenarioId'], $property->linked_scenarios)) {
	                $property->linked_scenarios[] = $linked_forms[$i]['scenarioId'];
	            }
	        }
	    }
	    
	    if (count($property->linked_scenarios) > 1) {
	        $property->has_multiple_scenarios = true;
	    }
	    
	    //on modifie la propiété avec le paramétrage du formulaire
	    if (!empty($property->pmb_extended['label'])) {
	        $property->pmb_extended['label'] = onto_common_ui::get_message($property->pmb_extended['label']);
	        $property->label = $property->pmb_extended['label'];
	    }
	    
	    if (!empty($property->pmb_extended['default_value'])) {
	        $property->default_value = array();
	        foreach ($property->pmb_extended['default_value'] as $value) {
	            if($value && is_array($value)){
	                $property->default_value[] = !empty($value['value']) ? $value['value'] : [];
	            }
	        }
	    }
	    
	    if (!empty($property->pmb_extended['placeholder'])) {
	        $property->pmb_extended['placeholder'] = htmlentities($property->pmb_extended['placeholder'], ENT_QUOTES, $charset);
	    }
	    
	    return $property;
	}
	
	public function get_property_hidden_template($property, $datatype_ui_class_name, $temp_datatype_tab) 
	{
	    if (!empty($datatype_ui_class_name)) {
	        $tmp_class = '';
    	    if (!empty($temp_datatype_tab[$property->uri][$datatype_ui_class_name])) {
    	        $tmp_class = $temp_datatype_tab[$property->uri][$datatype_ui_class_name];
    	    }
    	    return $datatype_ui_class_name::get_hidden_fields($property, $tmp_class, onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name));
	    }
	    return '';
	}
	
	public function get_property_template($property, $datatype_ui_class_name, $temp_datatype_tab, $flag) 
	{
	    global $ontology_tpl, $charset;
	    
	    $datatype_ui_form = "";
	    if (!empty($datatype_ui_class_name)) {
            $datatype_ui_form = $datatype_ui_class_name::get_form($this->uri, $property, $this->onto_class->get_restriction($property->uri), (isset($temp_datatype_tab[$property->uri]) ? $temp_datatype_tab[$property->uri][$datatype_ui_class_name] : null), onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name),$flag);
            $form_row_content_tooltip = '';
            $form_row_tooltip = '';
            
            if (!empty($property->pmb_extended['tooltip'])) {
                $property->pmb_extended['tooltip'] = onto_common_ui::get_message($property->pmb_extended['tooltip']);
                $form_row_content_tooltip = $ontology_tpl['form_row_content_tooltip'];
                $form_row_content_tooltip = str_replace('!!form_row_content_tooltip_content!!', htmlentities($property->pmb_extended['tooltip'], ENT_QUOTES, $charset), $form_row_content_tooltip);
                $form_row_content_tooltip = str_replace('!!onto_row_id!!', onto_common_uri::get_name_from_uri($this->uri, $this->onto_class->pmb_name).'_'.$property->pmb_name, $form_row_content_tooltip);
                $form_row_tooltip = htmlentities($property->pmb_extended['tooltip'], ENT_QUOTES, $charset);
            }
            
            $datatype_ui_form = str_replace('!!form_row_content_tooltip!!', $form_row_content_tooltip, $datatype_ui_form);
            $datatype_ui_form = str_replace('!!form_row_label_tooltip!!', $form_row_tooltip, $datatype_ui_form);
            
            $mandatory_sign = '';
            $mandatory_class = '';
            
            if (!empty($property->pmb_extended['mandatory'])) {
                $mandatory_sign = $ontology_tpl['form_row_content_mandatory_sign'];
                $mandatory_class = 'mandatory-contribution-field';
            }
            
            $datatype_ui_form = str_replace('!!form_row_content_mandatory_sign!!', $mandatory_sign, $datatype_ui_form);
            $datatype_ui_form = str_replace('!!form_row_content_mandatory_class!!', $mandatory_class, $datatype_ui_form);
            
            $comment = '';
            if (!empty($property->pmb_extended['comment'])) {
                $property->pmb_extended['comment'] = onto_common_ui::get_message($property->pmb_extended['comment']);
                $comment = '<i class="contribution_comment" title="' . $form_row_tooltip . '">'.nl2br($property->pmb_extended['comment']).'</i>';
            }
            $datatype_ui_form = str_replace('!!form_row_content_comment!!', $comment, $datatype_ui_form);
            
        }
        return $datatype_ui_form;
	}
	
	public function set_identifier($id) 
	{
	    $this->identifier = $id;
	}
} // end of onto_contribution_item
