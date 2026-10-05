<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontopmb_indexation_ui.class.php,v 1.2 2022/11/10 15:02:54 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path.'/templates/onto/ontopmb/onto_ontopmb_indexation.tpl.php');

class onto_ontopmb_indexation_ui extends onto_common_ui{
		
	
	/**
	 * Renvoie l'affichage html de la liste hierarchisée
	 * 
	 * @param onto_common_controler $controler
	 * @param onto_param $params
	 */
	public static function get_list($controler,$params){
	    global $msg,$charset,$include_path,$ontology_tpl,$lang;
		$elements = $controler->get_list("http://www.w3.org/2002/07/owl#Class", $params);
		
		$list="<h3>".$elements['nb_total_elements']." ".$msg['onto_nb_results']."</h3>".$ontology_tpl['list_indexation'];
		$list=str_replace("!!list_header!!", htmlentities($msg['103'],ENT_QUOTES,$charset), $list);
		$list_content='';
		foreach($elements['elements'] as $uri => $item){
		    $line=$ontology_tpl['list_line_indexation'];
		    $line=str_replace("!!list_line_href!!",'./'.$controler->get_base_resource().'categ='.$params->categ.'&sub='.$params->sub.'&action=edit&id='.onto_common_uri::get_id($uri) , $line);
		    $line=str_replace("!!list_line_libelle!!",htmlentities((isset($item[$lang]) ? $item[$lang] : $item['default']),ENT_QUOTES,$charset) , $line);
		    $list_content.= $line;
		}
		
		$list=str_replace("!!list_content!!",$list_content , $list);	
		$list=str_replace("!!list_onclick!!",'document.location=\'./'.$controler->get_base_resource().'categ='.$params->categ.'&sub='.$params->sub.'&id=&action=edit\'' , $list);
		$list=str_replace("!!list_pagination!!",aff_pagination("./".$controler->get_base_resource()."categ=".$params->categ."&sub=".$params->sub."&action=".$params->action."&user_input=".$params->user_input,$elements['nb_total_elements'],$elements['nb_onto_element_per_page'], $params->page, 10, true, true ) , $list);
		
		return $list;
	}
	
	/**
	 * Renvoie le formulaire de définition de l'indexation
	 *
	 * @param onto_common_controler $controler
	 * @param onto_param $params
	 */
	public static function get_indexation_form($controler,$params){
	    global $include_path,$ontology_tpl,$msg,$charset;
	    // On a besoin d'aller chercher les properties de la classe sélectionnée.
	    $class_uri = onto_common_uri::get_uri($params->id);
	    $properties = $controler->get_properties($class_uri);
	    $indexation  = $controler->get_existing_indexation($class_uri);
	    $nb = count($properties);
	    $html = $ontology_tpl['indexation_form'] ;
	    $html = str_replace('!!onto_form_title!!',$controler->get_data_label($class_uri),$html);
	    $html=str_replace("!!onto_form_id!!",onto_common_uri::get_name_from_uri($class_uri, "ontopmb") , $html);
	    $html=str_replace("!!onto_form_name!!",onto_common_uri::get_name_from_uri($class_uri, "ontopmb") , $html);
	    $html=str_replace("!!onto_form_history!!",'<input type="button" class="bouton" id="btcancel" onclick="history.go(-1);" value="'.htmlentities($msg['76'],ENT_QUOTES,$charset).'"/>' , $html);
	    $html=str_replace("!!onto_form_submit!!",'<input type="submit" class="bouton" id="btsubmit" value="'.htmlentities($msg['77'],ENT_QUOTES,$charset).'"/>' , $html);
	    $html=str_replace("!!onto_form_action!!",$controler->get_base_resource().'categ='.$params->categ.'&sub='.$params->sub.'&action=save&id='.$params->id, $html);
	    
	    $content = ''; 
	    $field = $controler->get_class_field($class_uri);
	    
	    for($i=0 ; $i<$nb ; $i++){ 
	        $pound = 0;
	        $index_uri = $subfield = ''; 
	        $subfield = $properties[$i]->subfield;
	        $entity = false;
	        $libelle = $controler->get_data_label($properties[$i]->property);
	        if(in_array($properties[$i]->range,['http://www.w3.org/2000/01/rdf-schema#Literal','http://www.pmbservices.fr/ontology#marclist']) || $properties[$i]->datatype != 'http://www.pmbservices.fr/ontology#resource_selector' ){
	            $item = $ontology_tpl['indexation_form_item_row'] ;
	        }else{
	            $item = $ontology_tpl['indexation_form_item_row_entity'] ;
	            $libelle .= " - ".$controler->get_data_label($properties[$i]->range);
	            $entity = true;
	        }
	    
	        $item = str_replace('!!libelle!!',$libelle,$item);  
	    
	        // Cas Standard
	        foreach($indexation as $indexation_infos){
	            if($indexation_infos['property'] == $properties[$i]->property && !isset($indexation_infos['useProperty'])){  
	                $pound = $indexation_infos['pound'];
	                $index_uri = $indexation_infos['index'];
	                break;
	            }
	        }
	       
	        $item = str_replace("!!pound!!",$pound,$item);
	        $item = str_replace('!!name!!',$properties[$i]->pmb_name,$item);
	        $item = str_replace('!!index_uri!!',$index_uri,$item);
	        $item = str_replace('!!field!!',$field,$item);
	        $item = str_replace('!!subfield!!',$subfield,$item);
	      
	        $subContent = "";
	        // Cas des entités
	        if($entity){
	            $useProperties = $controler->get_properties($properties[$i]->range);
	            $subNb = count($useProperties);
	            for($j=0 ; $j<$subNb ; $j++){ 
	                $pound = 0;
	                $index_uri  = "";
	                $subSubfield = $controler->get_class_field($properties[$i]->range)*1000+$useProperties[$j]->subfield;
	                $subName = $properties[$i]->pmb_name.'_'.$controler->get_class_name($properties[$i]->range).'_'.$useProperties[$j]->pmb_name;
	                $entity = false;
	                $libelle = $controler->get_data_label($useProperties[$j]->property);
	                if(in_array($useProperties[$j]->range,['http://www.w3.org/2000/01/rdf-schema#Literal','http://www.pmbservices.fr/ontology#marclist']) || $useProperties[$j]->datatype != 'http://www.pmbservices.fr/ontology#resource_selector' ){
	                    $subItem = $ontology_tpl['indexation_form_item_row'] ;
	                }else{
	                    // TODO AR : Pour le moment on coupe la récursivité !
	                    continue;
	                    $subItem = $ontology_tpl['indexation_form_item_row_entity'] ;
	                    $libelle .= " - ".$controler->get_data_label($useProperties[$j]->range);
	                }
	                $subItem = str_replace('!!libelle!!',$libelle,$subItem);    
	               
	                // Cas Standard
	                foreach($indexation as $indexation_infos){
	                    if($indexation_infos['property'] == $properties[$i]->property && $indexation_infos['onRange'] == $properties[$i]->range && $indexation_infos['useProperty'] == $useProperties[$j]->property ){
	                        $pound = $indexation_infos['pound'];
	                        $index_uri = $indexation_infos['index'];
	                        break;
	                    }
	                }
	                $subItem = str_replace("!!pound!!",$pound,$subItem);
	                $subItem = str_replace('!!name!!',$subName,$subItem);
	                $subItem = str_replace('!!index_uri!!',$index_uri,$subItem);
	                $subItem = str_replace('!!field!!',$field,$subItem);
	                $subItem = str_replace('!!subfield!!',$subSubfield,$subItem);
	                $subItem = str_replace('!!useProperty!!',$useProperties[$j]->property,$subItem);
	                $subContent.=$subItem;
	            }
	        }
	        $item = str_replace('!!useProperty!!',$subContent,$item);
	        $content.=$item;
	    } 
	    $html= str_replace('!!onto_form_content!!',$content,$html);
	    $html= str_replace('!!onto_form_scripts!!',"",$html);
	    
	    return $html;
	}
}
