<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_skos_concept_ui.class.php,v 1.52 2022/10/31 10:24:49 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path.'/templates/onto/skos/onto_skos_concept_ui.tpl.php');
require_once($class_path.'/authorities_statuts.class.php');

class onto_skos_concept_ui extends onto_common_ui{
	
	/**
	 * Retourne la liste hierarchisée en provenance du controler instancié
	 *
	 * @param onto_common_controler $controler
	 * @param onto_param $params
	 *
	 * @return array $elements
	 */
	
// 	public static function get_list_from_controler($controler,$params){
// 		if($params->action== 'list_selector' && $params->parent_id != 0){
// 			//dans le cas des concepts, on veut le parent
// 			$elements = $controler->get_hierarchized_list($controler->get_class_uri($params->element),$params);
// 		}else{
// 			switch($params->action){
// 				case "search" :
// 					$elements = $controler->get_searched_list($params);
// 					break;
// 				case "list" :	
// 					$elements = $controler->get_hierarchized_list($controler->get_class_uri($params->sub),$params);
// 					break;
// 				default :
// 					$elements = parent::get_list_from_controler($controler,$params);
// 					break;
// 			}
// 		}
// 		//@todo : ajouter une factory pour déterminer quelle liste remonter?
		
// 		if($this->)
		
		
// 		return $elements;
// 	}
	
	/**
	 * Construit et retourne le selecteur de schema
	 * 
	 * @param onto_common_controler $controler
	 * @param onto_param $params
	 * @param bool $empty
	 * @param string $onchange
	 * @param string $name
	 * @param string $id
	 * 
	 * @return string $selector
	 */
	public static function get_scheme_list_selector($controler,$params,$empty=false,$onchange='',$name='',$id='',$multiple=false){
		global $msg,$charset,$lang,$ontology_tpl;
		if($params->action=='list_selector'){
			$list=$controler->get_scheme_list();
			if(!empty($params->unique_scheme) && $params->concept_scheme[0] != -1){
			    return "<input type='hidden' name='".$name.($multiple ? "[]" : "")."' value='".$params->concept_scheme[0]."' />";
			}
			
			$selector_options = '';
			$option='';
			
			// Ajout de l'option "Tous les concepts"
			$option = $ontology_tpl['scheme_radio_selector'];
			$option = str_replace("!!scheme_list_selector_options_value!!",'-1' , $option);
			$option = str_replace("!!scheme_list_selector_options_label!!", $msg["onto_skos_concept_all_concepts"], $option);
			$option = str_replace("!!scheme_list_selector_name!!",$name , $option);
			$option = str_replace("!!scheme_list_selector_onchange!!", $onchange , $option);
			$selected='';
			if(in_array(-1,$params->concept_scheme)){
				$selected='checked';
			}
			$option= str_replace("!!scheme_list_selector_options_selected!!", $selected, $option);
			$selector_options.=$option;
			
			// Ajout de l'option "Sans schéma"
			$option = $ontology_tpl['scheme_radio_selector'];
			$option = str_replace("!!scheme_list_selector_options_value!!",'0' , $option);
			$option = str_replace("!!scheme_list_selector_options_label!!", $msg["onto_skos_concept_no_scheme"], $option);
			$option = str_replace("!!scheme_list_selector_name!!",$name, $option);
			$option = str_replace("!!scheme_list_selector_onchange!!", $onchange , $option);
			$selected='';
			if(in_array(0,$params->concept_scheme)){
				$selected = 'checked';
			}
			$option = str_replace("!!scheme_list_selector_options_selected!!", $selected, $option);
			$selector_options.= $option;
			
			foreach($list['elements'] as $uri=>$scheme){
				
				$option = $ontology_tpl['scheme_radio_selector'];
				$scheme_id = onto_common_uri::get_id($uri);
				
				$selected = '';
				if((in_array($scheme_id,$params->concept_scheme))){
					$selected = 'checked';
				}
				
				if(isset($scheme[$lang]) && $scheme[$lang] != ''){
					$display_label = $scheme[$lang];
				}else{
					$display_label = $scheme['default'];
				}
	
				$option = str_replace("!!scheme_list_selector_name!!", $name , $option);
				$option = str_replace("!!scheme_list_selector_onchange!!", $onchange , $option);
				$option = str_replace("!!scheme_list_selector_options_selected!!", $selected , $option);
				$option = str_replace("!!scheme_list_selector_options_value!!", $scheme_id , $option);
				$option = str_replace("!!scheme_list_selector_options_label!!", htmlentities($display_label, ENT_QUOTES, $charset) , $option);
				$selector_options.= $option;
			}		
	
			return $selector_options;
		}else{
		    $selector=$ontology_tpl['scheme_list_selector'];
		    if($multiple){
		        $selector = str_replace('!!multiple!!','multiple="yes"',$selector);
		    } 
 			$selector = str_replace('!!multiple!!','',$selector);
			
			$selector = str_replace("!!scheme_list_selector_onchange!!",$onchange , $selector);
			$selector = str_replace("!!scheme_list_selector_name!!",$name.($multiple ? "[]" : "") , $selector);
			$selector = str_replace("!!scheme_list_selector_id!!",$id , $selector);
			
			$list=$controler->get_scheme_list();
			if(isset($params->unique_scheme) && $params->unique_scheme && $params->concept_scheme[0] !=-1){
				return "<input type='hidden' name='".$name."' value='".implode(",",$params->concept_scheme)."' />";
			}
			
			$selector_options='';
			$option='';
			
			// Ajout de l'option "Tous les concepts"
			$option=$ontology_tpl['scheme_list_selector_option'];
			$option= str_replace("!!scheme_list_selector_options_value!!",'-1' , $option);
			$option= str_replace("!!scheme_list_selector_options_label!!", $msg["onto_skos_concept_all_concepts"], $option);
			$selected='';
			if(count($params->concept_scheme) == 0 || in_array(-1,$params->concept_scheme)){
				$selected='selected="selected"';
			}
			$option= str_replace("!!scheme_list_selector_options_selected!!", $selected, $option);
			$selector_options.=$option;
			
			// Ajout de l'option "Sans schéma"
			$option=$ontology_tpl['scheme_list_selector_option'];
			$option= str_replace("!!scheme_list_selector_options_value!!",'0' , $option);
			$option= str_replace("!!scheme_list_selector_options_label!!", $msg["onto_skos_concept_no_scheme"], $option);
			$selected='';
			if(in_array('0',$params->concept_scheme)){
				$selected='selected="selected"';
			}
			$option= str_replace("!!scheme_list_selector_options_selected!!", $selected, $option);
			$selector_options.=$option;
			foreach($list['elements'] as $uri=>$scheme){
					
				$option=$ontology_tpl['scheme_list_selector_option'];
				$scheme_id=onto_common_uri::get_id($uri);
					
				$selected='';
				
				if(in_array($scheme_id,$params->concept_scheme)){
				    $selected = 'selected="selected"';
				}
					
				if(isset($scheme[$lang]) && $scheme[$lang]!=''){
					$display_label=$scheme[$lang];
				}else{
					$display_label=$scheme['default'];
				}
					
				$option= str_replace("!!scheme_list_selector_options_selected!!",$selected , $option);
				$option= str_replace("!!scheme_list_selector_options_value!!",$scheme_id , $option);
				$option= str_replace("!!scheme_list_selector_options_label!!",htmlentities($display_label,ENT_QUOTES,$charset) , $option);
				$selector_options.=$option;
			}
			$selector = str_replace("!!scheme_list_selector_options!!",$selector_options , $selector);
			return $selector;
		}
	}
	
	
	/**
	 * Renvoie la construction du fil de navigation
	 * 
	 * @param onto_common_controler $controler
	 * @param onto_param $params
	 * 
	 * @return string return 
	 */
	public static function get_breadcrumb($controler,$params){
		global $base_path;
		
		$breadcrumb=$controler->handle_breadcrumb();
		$return='';
		if(is_array($breadcrumb) && count($breadcrumb)) {
			foreach($breadcrumb as $parent_id){
				if($return){
					$return.=' > ';
				}
				
				$return.="<a href='$base_path/".$controler->get_base_resource()."categ=".$params->categ."&sub=".$params->sub."&action=".$params->action."&concept_scheme=".implode(",",$params->concept_scheme)."&parent_id=".$parent_id."'>".$controler->get_data_label(onto_common_uri::get_uri($parent_id)).'</a>';
			}
		}
		return $return;
	}
	
	/**
	 * Renvoie le formulaire de recherche dans les concepts
	 * 
	 * @param onto_common_controler $controler
	 * @param onto_param $params
	 * 
	 * @return string $form
	 */
	public static function get_search_form($controler,$params){
		global $msg,$charset,$base_path,$ontology_tpl, $authority_statut;
		
		$title = '';
		$classes = $controler->get_classes();
		foreach($classes as $class){
			if($class->pmb_name == $params->sub){
				$title.= $controler->get_label($class->pmb_name);
			}
		}
		
		$onchange_scheme_list_selector = '';
		$name_scheme_list_selector = 'concept_scheme';
		$id_scheme_list_selector = 'id_concept_scheme';
		
		$form = $ontology_tpl['skos_concept_search_form'];
		$form = str_replace('!!skos_concept_search_form_action!!', $base_path.'/'.$controler->get_base_resource().'categ='.$params->categ.'&sub='.$params->sub.'&id=&action=search', $form);
		$form = str_replace('!!skos_concept_search_form_last_concepts_link!!', $base_path.'/'.$controler->get_base_resource().'categ='.$params->categ.'&sub='.$params->sub.'&id=&action=last', $form);
				
		$lien_imprimer_concepts = "&nbsp;<a href='#' onClick=\"openPopUp('./print_concepts.php?current_print=2&action=print_prepare&scheme_id=' + document.getElementById('id_concept_scheme').value, 'print'); return false;\">" . $msg['print_concepts'] . "</a> ";
		$form = str_replace('<!-- imprimer_concepts -->', $lien_imprimer_concepts, $form);
		
		$form = str_replace('!!skos_concept_search_form_title!!', $title, $form);
		$form = str_replace('<!-- sel_authority_statuts -->', authorities_statuts::get_form_for(AUT_TABLE_CONCEPT, intval($authority_statut), true), $form);
		$form = str_replace('!!skos_concept_search_form_selector!!', self::get_scheme_list_selector($controler, $params,false,$onchange_scheme_list_selector,$name_scheme_list_selector,$id_scheme_list_selector), $form);
		
		$onchange_only_top_concepts = '';
		$checked_only_top_concepts = '';
		if ($params->only_top_concepts) {
			$checked_only_top_concepts = 'checked="checked"';
		}
		$form = str_replace('!!only_top_concepts_onchange!!', $onchange_only_top_concepts, $form);
		$form = str_replace('!!only_top_concepts_checked!!', $checked_only_top_concepts, $form);
		
		if(isset($msg['onto_'.$controler->get_onto_name().'_add_'.$params->sub])){
			$add_msg = $msg['onto_'.$controler->get_onto_name().'_add_'.$params->sub];
		}else{
			$add_msg = sprintf($msg['onto_common_add'], $controler->get_label($params->sub));
		}
		$form = str_replace('!!skos_concept_search_form_user_input!!',stripslashes(htmlentities($params->user_input,ENT_QUOTES,$charset)),$form);
		$form = str_replace('!!skos_concept_search_form_concept_onclick!!','document.location=\'./'.$controler->get_base_resource().'categ='.$params->categ.'&sub='.$params->sub.'&id=&action=edit&concept_scheme='.implode(",",$params->concept_scheme).'&parent_id='.$params->parent_id.'\'', $form);
		$form = str_replace('!!skos_concept_search_form_concept_value!!',htmlentities($add_msg,ENT_QUOTES,$charset), $form);
		
		$form = str_replace('!!skos_concept_search_form_composed_onclick!!','document.location=\'./'.$controler->get_base_resource().'categ='.$params->categ.'&sub='.$params->sub.'&id=&action=edit&composed=composed&concept_scheme='.implode(",",$params->concept_scheme).'&parent_id='.$params->parent_id.'\'', $form);
		
		$form = str_replace('!!skos_concept_search_form_href!!', $base_path.'/'.$controler->get_base_resource().'categ='.$params->categ.'&sub='.$params->sub.'&action='.$params->action.'&concept_scheme='.implode(",",$params->concept_scheme), $form);
		
		$form = str_replace('!!skos_concept_search_form_breadcrumb!!', self::get_breadcrumb($controler,$params) ,$form);
		
		return $form;
	}
	
	/**
	 * Renvoie l'affichage html de la liste hierarchisée
	 * 
	 * @param onto_common_controler $controler
	 * @param onto_param $params
	 */
	public static function get_list($controler,$params){
		global $msg,$charset,$base_path,$ontology_tpl,$lang;
		global $authority_statut;
		
		if ($params->action != 'last') {
			$elements = $controler->get_list_elements($params);
		} else {
			$elements = $controler->get_last_elements();
		}
		$list = "<h3>".$elements['nb_total_elements']." ".$msg['onto_skos_concept_nb_results'] . "</h3>!!caddie_link!! !!sort_link!!" . $ontology_tpl['skos_concept_list'];
		if (!empty($params->user_input) && $params->user_input != "*" && $params->sub == "concept") {
		    $list = str_replace("!!sort_link!!",  entities_authorities_controller::get_sort_link($elements['nb_total_elements'], "concepts"), $list);
		} else {
		    $list = str_replace("!!sort_link!!",  "", $list);
		}
 		$list = str_replace("!!list_header!!", htmlentities($msg['103'], ENT_QUOTES, $charset), $list);
 		$list = str_replace("!!list_header_utilisation!!", htmlentities($msg['voir_notices_assoc'], ENT_QUOTES, $charset), $list);
		
		$list_content='';
		foreach($elements['elements'] as $uri => $item){
		    $id = onto_common_uri::get_id($uri);
		    if (!$id) {
		        $id = onto_common_uri::set_new_uri($uri);
		    }
			if($controler->has_narrower($uri,$params)){	
				$line=$ontology_tpl['skos_concept_list_line_folder'];
				$line=str_replace("!!list_line_folder_href!!",$base_path."/".$controler->get_base_resource()."categ=".$params->categ."&sub=".$params->sub."&action=".$params->action."&parent_id=".$id."&concept_scheme=".implode(",",$params->concept_scheme) , $line);
			}else{
				$line=$ontology_tpl['skos_concept_list_line_doc'];
			}
			if ($params->categ == 'concepts') {
			    $line=str_replace("!!list_line_link_see!!",$base_path."/".$controler->get_base_resource()."categ=see&sub=".$params->sub."&id=".$id, $line);
 			    $breadcrumb = array();
 			    $current = $uri;
 			    while(($controler->has_broader($current, $params))){
 			        $broaders = $controler->get_broaders($current, $params);
			        if(count($broaders)>1){
			            //LE cas où il y a plusieurs parents dans le même schéma
			            // onregarde du coté de la session pour voir si on retrouve une navigation préalable
			            // sinon, on prend le premier !
			            
			            if(isset($_SESSION['breadcrumb'])){
			                for($k=0 ; $k<count($broaders) ; $k++){
			                    $broaders[$k]['pos'] = strpos($_SESSION['breadcrumb'],'-'.$broaders[$k]['id'].'-');
			                    if( $broaders[$k]['pos'] === false){
			                        $broaders[$k]['pos'] = 100000;
			                    }
			                }
			                usort($broaders,function($a,$b){
			                    if ($a['pos'] == $b['pos']) {
			                        if ($a['label'] == $b['label']) {
			                            return 0;
			                        }
			                        return ($a['label'] < $b['label']) ? -1 : 1;
			                    }
			                    return ($a['pos'] < $b['pos']) ? -1 : 1;
			                });
			            }
			        }
 			        $broader_uri =onto_common_uri::get_uri($broaders[0]['id']);
 			        array_unshift($breadcrumb, $broader_uri);
 			        $current = $broader_uri;
 			    }
			    
                $parent_label = "";
			    foreach ($breadcrumb as $p){
			        if ($parent_label == ""){
			            $parent_concept = authorities_collection::get_authority(AUT_TABLE_INDEX_CONCEPT,onto_common_uri::get_id($p));
			            $parent_label = "[".$parent_concept->get_scheme()."] ";
			        }else{
    			        if($parent_label!= ""){
    			            $parent_label.=" -> ";
    			        }
			        }
			        $parent_label.=$controler->get_data_label($p);
			    }
			    if($parent_label == ""){
			        $concept = authorities_collection::get_authority(AUT_TABLE_INDEX_CONCEPT,$id);
			        $parent_label = "[".$concept->get_scheme()."] ".$controler->get_data_label($uri);
			    }else{
			        $parent_label.=" -> ".$controler->get_data_label($uri);
			    }
			   
			    $line=str_replace("!!list_line_title!!",$parent_label, $line);
			}
			$line=str_replace("!!list_line_title!!","", $line);
			$authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0,['num_object'=> onto_common_uri::get_id($uri),'type_object'=> AUT_TABLE_CONCEPT]);
			//$authority = new authority(0, onto_common_uri::get_id($uri), AUT_TABLE_CONCEPT);
			$line=str_replace("!!statut!!",$authority->get_display_statut_class_html(),$line);
			$line=str_replace("!!list_line_libelle!!",htmlentities((isset($item[$lang]) ? $item[$lang] : $item['default']),ENT_QUOTES,$charset) , $line);
			$concept = authorities_collection::get_authority(AUT_TABLE_INDEX_CONCEPT,$id);
			//$concept = new concept($id);
			
			if(!empty($params->selector_context)){
				$line = str_replace("!!list_line_onclick!!", "onclick=\"set_parent('".$params->caller."', '".$params->element."', '".$uri."', '".addslashes(htmlentities($authority->get_isbd(), ENT_NOQUOTES, $charset))."', '".$params->type."', '".$params->callback."');\"", $line);
				$line = str_replace("!!list_line_href!!", "#", $line);
			}else{
				$line = str_replace("!!list_line_onclick!!", '', $line);
				$line = str_replace("!!list_line_href!!",$base_path."/".$controler->get_base_resource()."categ=".$params->categ."&sub=".$params->sub."&action=edit&id=".$id."&parent_id=".$params->parent_id."&concept_scheme=".implode(",",$params->concept_scheme), $line);
				$line = str_replace("!!list_line_basket!!", $authority->get_caddie(), $line);
				
			}
			
			$line=str_replace("!!list_line_nb_utilisations!!", $concept->get_nb_indexed_entities(), $line);
			$line=str_replace("!!list_line_nb_utilisations_href!!", $base_path."/catalog.php?categ=search&mode=0&etat=aut_search&aut_type=concept&aut_id=".$id, $line);
			
			$list_content.=$line;
		}
		
		$list=str_replace("!!list_content!!",$list_content , $list);
		$list=str_replace("!!list_pagination!!",aff_pagination("./".$controler->get_base_resource()."categ=".$params->categ."&sub=".$params->sub."&action=".$params->action."&concept_scheme=".implode(",",$params->concept_scheme)."&parent_id=".$params->parent_id."&user_input=".$params->user_input.($authority_statut ? '&authority_statut='.$authority_statut : ''),$elements['nb_total_elements'],$elements['nb_onto_element_per_page'], $params->page, 10, false, true ) , $list);
		
		return $list;
	}
	
	/**
	 * Renvoie l'affichage html de la liste pour le selecteur d'autorité
	 *
	 * @param onto_common_controler $controler
	 * @param onto_param $params
	 *
	 * @return string $form
	 */
	public static function get_list_selector($controler,$params){
		global $msg,$charset,$element_form,$ontology_tpl,$list_form,$lang;
	
		if($params->objs){
			$property=$controler->get_onto_property_from_pmb_name($params->objs);
			$element = $property->range[$params->range];
		}else {
			$element = $controler->get_class_uri($params->element);
		}
		$elements = $controler->get_list_elements($params);
		$elements_form = "";
		$list = $list_form;
		if($elements["nb_total_elements"]){
			foreach($elements['elements'] as $uri => $item){
				switch($element){
					case "http://www.w3.org/2004/02/skos/core#Concept" :
						if($controler->has_narrower($uri,$params)){
							$current_element_form = $ontology_tpl['skos_concept_list_selector_line_folder'];
							$current_element_form = str_replace("!!folder_href!!",  $params->base_url."&parent_id=".onto_common_uri::get_id($uri), $current_element_form);
						}else{
							$current_element_form = $ontology_tpl['skos_concept_list_selector_line_doc'];
						}
						break;
					default :
						$current_element_form = $element_form;
						break;
				}
				
				$current_element_form = str_replace("!!caller!!", $params->caller, $current_element_form);
				$current_element_form = str_replace("!!element!!", $params->element, $current_element_form);
				$current_element_form = str_replace("!!order!!", (!empty($params->order) ? $params->order : 0), $current_element_form);
				if(!empty($params->return_concept_id)){
					$current_element_form = str_replace("!!uri!!", onto_common_uri::get_id($uri), $current_element_form);
				}else{
					$current_element_form = str_replace("!!uri!!", $uri, $current_element_form);
				}
				$current_element_form = str_replace("!!item!!", htmlentities(addslashes((isset($item[$lang]) ? $item[$lang] : $item['default'])),ENT_QUOTES,$charset), $current_element_form);
				$current_element_form = str_replace("!!range!!", rawurlencode($element ? $element : $controler->get_class_uri($params->sub)), $current_element_form);
				$current_element_form = str_replace("!!callback!!", $params->callback, $current_element_form);
			
				$authority = new authority(0, onto_common_uri::get_id($uri), AUT_TABLE_CONCEPT);
				$current_element_form = str_replace("!!statut!!",$authority->get_display_statut_class_html(),$current_element_form);
				$current_element_form = str_replace("!!item_libelle!!", htmlentities((isset($item[$lang]) ? $item[$lang] : $item['default']),ENT_QUOTES,$charset), $current_element_form);
				$infobulle_libelle = "";
				if ($controler->has_broader($uri,$params)) {
					$parents = $controler->get_broaders($uri,$params);
					if (!empty($parents[0]["id"])) {
						$infobulle_libelle .= $msg["onto_skos_concept_broader"]." ".$parents[0]["label"].". ";
					}
				} 				
				$infos = $controler->get_informations_concept($uri);
				if (isset($infos[0]["scopeNote"]) && $infos[0]["scopeNote"]) $infobulle_libelle .= $msg["onto_skos_concept_scopenote"]." ".$infos[0]["scopeNote"];
				$current_element_form = str_replace("!!infobulle_libelle!!", htmlentities($infobulle_libelle, ENT_QUOTES, $charset), $current_element_form);
				$elements_form.= $current_element_form;
			}
			$list = str_replace("!!elements_form!!", $elements_form, $list);
			$list = str_replace("!!aff_pagination!!", aff_pagination($params->base_url."&deb_rech=".$params->deb_rech."&concept_scheme=".implode(",",$params->concept_scheme)."&parent_id=".$params->parent_id,$elements['nb_total_elements'],$elements['nb_onto_element_per_page'], $params->page, 10, true, true ), $list);
		}else{
			$list = $msg["1915"];
		}
		return $list;
	}
	
	public static function get_search_form_selector($controler,$params){
		global $ontology_tpl,$jscript,$range_link_form;
		global $list_range_links_form;
		global $sel_no_available_search_form;
		global $msg, $charset;
		global $authority_statut;
		global $pmb_popup_form_display_mode;
		
		if($params->objs){
			$property=$controler->get_onto_property_from_pmb_name($params->objs);
			// AR - 31/10/22 Pas d'ordre naturel à  la lecture de l'ontologie, donc on trie arbitrairement pour garantir une cohérence dans les ids d'onglets
			sort($property->range);
			$element = $property->range[$params->range];
		}else {
			$property=null;
			$element = $controler->get_class_uri($params->element);
		}
		$form = "";
		if($controler->get_searcher_class_name($element)){
			$search = $ontology_tpl['skos_concept_selector_search_form'];
			$search = str_replace("!!base_url!!", $params->base_url, $search);
			$search = str_replace("!!deb_rech!!", stripslashes(htmlentities($params->deb_rech,ENT_QUOTES,$charset)), $search);
			$form.= $search;
		}else{
			$form = $sel_no_available_search_form;
		}
		if (is_object($property) && count($property->range) > 1) {
			$range_links_form = "";
			foreach ($property->range as $i => $uri_class) {
				$current_range_link_form = $range_link_form;
				$current_range_link_form = str_replace("!!class!!", (($params->range == $i) ? "class='selected'" : ""), $current_range_link_form);
				$current_range_link_form = str_replace("!!href!!", $params->base_url."&range=".$i, $current_range_link_form);
				$current_range_link_form = str_replace("!!libelle!!", $controler->get_class_label($uri_class), $current_range_link_form);
	
				$range_links_form.= $current_range_link_form;
			}
			$range_links = str_replace("!!range_links_form!!", $range_links_form, $list_range_links_form);
			$form.= $range_links;
		}
		
		$onchange_scheme_list_selector = '';
		$name_scheme_list_selector='concept_scheme';
		$id_scheme_list_selector='id_concept_scheme';
		$form=str_replace('<!-- sel_authority_statuts -->', authorities_statuts::get_form_for(AUT_TABLE_CONCEPT, intval($authority_statut), true), $form);
		$form=str_replace('!!skos_concept_search_form_selector!!', self::get_scheme_list_selector($controler, $params,false,$onchange_scheme_list_selector,$name_scheme_list_selector,$id_scheme_list_selector), $form);

		$onchange_only_top_concepts = '';
		$checked_only_top_concepts = '';
		if ($params->only_top_concepts) $checked_only_top_concepts = 'checked="checked"';
		
		$form=str_replace('!!only_top_concepts_onchange!!', $onchange_only_top_concepts, $form);
		$form=str_replace('!!only_top_concepts_checked!!', $checked_only_top_concepts, $form);
		
		//fil d'arianne
		$form=str_replace('!!skos_concept_search_form_href!!',$params->base_url ,$form);
		$form=str_replace('!!skos_concept_selector_breadcrumb!!',self::get_selector_breadcrumb($controler,$params) ,$form);
		$form.= $jscript;
		
		$button_add = '';
		if(!empty($params->bt_ajouter) && $params->bt_ajouter != 'no') {
			//ajout d'un nouveau concept
			$button_add = $ontology_tpl['skos_concept_selector_search_form_add'];
			if(isset($msg['onto_'.$controler->get_onto_name().'_add_concept'])){
				$add_msg = $msg['onto_'.$controler->get_onto_name().'_add_concept'];
			}else{
				$add_msg = sprintf($msg['onto_common_add'],$controler->get_label("concept"));
			}
			$button_add = str_replace("!!add_button_label!!", $add_msg, $button_add);
			if($pmb_popup_form_display_mode == 2) {
			    $onclick = "document.location=\"./".$params->base_url."&concept_scheme=".implode(",",$params->concept_scheme)."&parent_id=".$params->parent_id."&action=edit\"";
			} else {
			    $onclick = "document.location=\"./".$params->base_url."&concept_scheme=".implode(",",$params->concept_scheme)."&parent_id=".$params->parent_id."&action=selector_add\"";
			}			
			$button_add = str_replace("!!add_button_onclick!!", $onclick, $button_add);
		}
		$form = str_replace("!!button_add!!", $button_add, $form);
		
		return $form;
	}
	
	public static function get_selector_breadcrumb($controler,$params){
		$breadcrumb=$controler->handle_breadcrumb();
		$return='';
		if(is_array($breadcrumb) && count($breadcrumb)) {
			foreach($breadcrumb as $parent_id){
				if($return){
					$return.=' > ';
				}
				$return.="<a href='".$params->base_url."&parent_id=".$parent_id."'>".$controler->get_data_label(onto_common_uri::get_uri($parent_id)).'</a>';
			}
		}
		return $return;
	}
	
}
