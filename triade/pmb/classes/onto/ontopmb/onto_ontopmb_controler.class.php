<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontopmb_controler.class.php,v 1.11 2022/11/22 11:07:01 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/onto/onto_files.class.php');

class onto_ontopmb_controler extends onto_common_controler {
    
    public const INDEXATION_CLASS_URI = "http://www.pmbservices.fr/ontology#indexation";
	
	public function get_base_resource($with_params=true){
		return $this->params->base_resource."?ontology_id=".$this->params->ontology_id.($with_params? "&" : "");
	}
	public function get_title(){
	    $title = $this->params->onto_name;
	    if($this->params->sub){
	        $classes = $this->handler->get_classes();
	        foreach($classes as $class){
	            if($class->pmb_name == $this->params->sub){
	                $title.= " > ".$this->get_label($class->pmb_name);
	            }
	        }
	    }
	    return $title;
	}
	protected function proceed_save($list=true){
	    if($this->params->sub == "indexation"){
	        return $this->proceed_save_indexation();
	    }
	    
		$this->item->get_values_from_form();
		$keepProperties = [
		    'pmb:subfield',
		    'pmb:field',
		    'pmb:formOrder',
		    'pmb_onto:indexWith',
		];
		
		$result = $this->handler->save($this->item,$keepProperties);
		if($result !== true){
			$ui_class_name=self::resolve_ui_class_name($this->params->sub,$this->handler->get_onto_name());
			$ui_class_name::display_errors($this,$result);
			return;
		}else{
		  
			//on a besoin pour notre ontologie de revoir certaine property...
			$query = "select * where {
				<".$this->item->get_uri()."> rdf:type ?type .
				<".$this->item->get_uri()."> rdfs:label ?label .
				optional {
					<".$this->item->get_uri()."> pmb_onto:restrictWith ?restrictWith .
				} .
				optional {
					<".$this->item->get_uri()."> pmb:formOrder ?formOrder .
				} .
				optional {
					<".$this->item->get_uri()."> pmb:field ?field .
				} .
				optional {
					<".$this->item->get_uri()."> pmb:subfield ?subfield .
				} 
			}";
			$needFormOrder = false;
			$needField = false;
			$needSubfield = false;
			$needFormOrder = false;
			$this->handler->data_query($query);
			if($this->handler->data_num_rows()){
			    $results = $this->handler->data_result();
				$query = "
					insert into <pmb> {";
 				switch($results[0]->type){
 					case "http://www.w3.org/2002/07/owl#Class" :
 					    //AR - 13/10/22: On s'assure d'avoir un code champ sur chaque classe
 					    if(empty($results[0]->field)){
 					        $needField = true;
 					    }
 						break;
 					case "http://www.w3.org/2002/07/owl#ObjectProperty" :
 						$query.= "
						<".$this->item->get_uri()."> rdf:type <http://www.w3.org/1999/02/22-rdf-syntax-ns#Property> .";
 						//AR - 03/10/22: On s'assure que l'ordre d'affichage des properties dans nos formulaires arrete de faire n'importe quoi à chaque fois qu'on recharge la page...
 						if(empty($results[0]->formOrder)){
 						    $needFormOrder = true;
 						}
 						//AR - 13/10/22: On s'assure d'avoir un code sous champ sur chaque property
 						if(empty($results[0]->subfield)){
 						    $needSubfield = true;
 						}
						break;
 				}
 				if($needFormOrder){
     				$q = 'select ?formOrder where { ?s pmb:formOrder ?formOrder } order by desc(?formOrder) limit 1';
     				$this->handler->data_query($q);
     				$formOrder = 0;
     				if($this->handler->data_num_rows()){
     				    $r = $this->handler->data_result();
     				    $formOrder = intval($r[0]->formOrder);
     				}
     				$formOrder++;
     				$query.= "<".$this->item->get_uri()."> pmb:formOrder ".$formOrder." .";
 				}
 				if($needField){
 				    $q = 'select ?field where { ?s pmb:field ?field } order by desc(?field) limit 1';
 				    $this->handler->data_query($q);
 				    $field = 0;
 				    if($this->handler->data_num_rows()){
 				        $r = $this->handler->data_result();
 				        $field = intval($r[0]->field);
 				    }
 				    $field++;
 				    $query.= "<".$this->item->get_uri()."> pmb:field ".$field." .";
 				}
 				if($needSubfield){
 				    $q = 'select ?subfield where { ?s pmb:subfield ?subfield } order by desc(?subfield) limit 1';
 				    $this->handler->data_query($q);
 				    $subfield = 0;
 				    if($this->handler->data_num_rows()){
 				        $r = $this->handler->data_result();
 				        $subfield = intval($r[0]->subfield);
 				    }
 				    $subfield++;
 				    $query.= "<".$this->item->get_uri()."> pmb:subfield ".$subfield." .";
 				}
				$labels = $inverseOf = $restrictWith = array();
				foreach($results as $result){
					
					$labels[] = "\"".$result->label."\"".(isset($result->label_lang) ? "@".$result->label_lang : "");
					if(!empty($result->indexWith)){
						$indexWith[] = "<".$result->indexWith.">";
					}
					if(!empty($result->restrictWith)){
						$restrictWith[] = "<".$result->restrictWith.">";
					}
				}
				$labels = array_unique($labels);
				$restrictWith = array_unique($restrictWith);
				foreach($restrictWith as $elem){
					$query.= "
						<".$this->item->get_uri()."> rdfs:subClassOf ".$elem." .";
				}
				$query.= "
					}";
				$this->handler->data_query($query);
			}
		}
		if($list){
		    print $this->get_menu();
			$this->proceed_see();
		}
	}
	
	
	protected function proceed_edit(){
	    if($this->params->sub != "indexation"){
	        print $this->item->get_form("./".$this->get_base_resource()."categ=".$this->params->categ."&sub=".$this->params->sub."&id=".$this->params->id);
	        return;
	    }
	    // On n'est pas sorti, donc on est dans l'indexation!
	    $this->proceed_edit_indexation();
	}
	
	/**
	 * 
	 * On dérive ici pour rajouter notre petite interface de définition de l'indexation maison
	 * @see onto_common_controler::get_menu()
	 */
	public function get_menu(){
	    global $base_path;
	    $menu = "
		<h1>".$this->get_title()."</h1>
		<div class='hmenu'>";
	    $classes = $this->handler->get_classes();
	    foreach($classes as $class){
	        $menu.="
			<span ".($class->pmb_name == $this->params->sub ? "class='selected'" : "").">
			<a href='".$base_path."/".$this->get_base_resource()."categ=".$this->params->categ."&sub=".$class->pmb_name."&action=list'>".$this->get_label($class->pmb_name)."</a>
			</span>";
	    }
	    // AR - 13/10/22 : On rajoute notre point d'entrée maison pour l'indexation
	    $menu.="
			<span ".("indexation" == $this->params->sub ? "class='selected'" : "").">
			<a href='".$base_path."/".$this->get_base_resource()."categ=".$this->params->categ."&sub=indexation&action=list'>".$this->get_label("indexation")."</a>
			</span>";
	    // AR - 13/10/22 : Fin du rajout
	    $menu.= "
		</div>";
	    return $menu;
	}
	
	/**
	 * Affichage spécificique pour l'indexation
	 */
	protected function proceed_edit_indexation(){ 
	    $ui_class_name=self::resolve_ui_class_name($this->params->sub,$this->handler->get_onto_name());
        $html = $ui_class_name::get_indexation_form($this,$this->params);
	    print $html;
	}
	
	/**
	 * On va chercher les propriétés d'une classe de l'ontologie en cours de définition !
	 * @param integer $id_item identifiant de la classe
	 */
	public function get_properties($class_uri)
	{
	    $query = 'select * where {
                ?property rdf:type <http://www.w3.org/1999/02/22-rdf-syntax-ns#Property> .
                ?property pmb:name ?pmb_name .
                ?property rdfs:label ?label .
                ?property pmb:datatype ?datatype .
                ?property rdfs:range ?range .
                ?property pmb:subfield ?subfield .
                optional {
                    ?property rdfs:domain ?domain .
                } .
                filter((!bound(?domain)) || (?domain = <'.$class_uri.'>))
			}
           ';
	    $this->handler->data_query($query);
        $result = $this->handler->data_result();
        return $result;
	}
	
	public function get_class_field($class_uri)
	{

	    $query = 'select ?field where {
            <'.$class_uri.'> pmb:field ?field
        }';  
	    $this->handler->data_query($query);
	    $result = $this->handler->data_result();
	    return $result[0]->field;
	}
	
	
	
	public function get_existing_indexation($class_uri)
	{
	    $query = 'select * where {	
            <'.$class_uri.'> pmb_onto:indexWith ?index .
            ?index pmb:pound ?pound .
            ?index owl:onProperty ?property .
            optional {
                ?index pmb:useProperty ?useProperty .
                ?index pmb:onRange ?onRange
            }

        }';
	    $this->handler->data_query($query);
        $result = $this->handler->data_result();
        $index = [];
        //$field = $this->get_class_field($class_uri);
        if($result!==false){
            for($i=0 ; $i<count($result) ; $i++){
                if(empty($index[$result[$i]->index])){
                    $index[$result[$i]->index] = [];
                }
                $index[$result[$i]->index]['index'] = $result[$i]->index;
           
                $index[$result[$i]->index]['pound'] = $result[$i]->pound;
                $index[$result[$i]->index]['property'] = $result[$i]->property;
                if(!empty($result[$i]->useProperty)){
                    $index[$result[$i]->index]['useProperty'] = $result[$i]->useProperty; 
                    $index[$result[$i]->index]['onRange'] = $result[$i]->onRange;
                }
            }
        }
        return $index;
	}
	/**
	 * Sauvegarde spécificique pour l'indexation
	 */
	protected function proceed_save_indexation(){   
	    $class_uri = onto_common_uri::get_uri($this->params->id);
	    $properties = $this->get_properties($class_uri);  
	   
	    $nb = count($properties);
	    for($i=0 ; $i<$nb ; $i++){
	        if(in_array($properties[$i]->range,['http://www.w3.org/2000/01/rdf-schema#Literal','http://www.pmbservices.fr/ontology#marclist']) || $properties[$i]->datatype != 'http://www.pmbservices.fr/ontology#resource_selector' ){
	            //cas simple
	            $triplets = $this->buildIndexationTriplets($class_uri,$properties[$i],$properties[$i]->pmb_name);
	            if(count($triplets['delete'])){
	                $query = 'delete { '.implode($triplets['delete'],' . ').'}';
	                $this->handler->data_query($query);
	            }
	            if(count($triplets['insert'])){
	                $query = 'insert into <pmb> { '.implode($triplets['insert'],' . ').'}';
	                $this->handler->data_query($query);
	            }
	        }else{
	           // la property est une ressource
	            $useProperties = $this->get_properties($properties[$i]->range);
	            $subNb = count($useProperties);
	            for($j=0 ; $j<$subNb ; $j++){ 
	                if(!(in_array($useProperties[$j]->range,['http://www.w3.org/2000/01/rdf-schema#Literal','http://www.pmbservices.fr/ontology#marclist']) || $useProperties[$j]->datatype != 'http://www.pmbservices.fr/ontology#resource_selector' )){
	                    // TODO AR : Pour le moment on coupe la récursivité !
	                    continue;
	                }
	                $var_sub_property = $properties[$i]->pmb_name.'_'.$this->handler->get_pmb_name($properties[$i]->range).'_'.$useProperties[$j]->pmb_name;
	                $triplets = $this->buildIndexationTriplets($class_uri, $properties[$i], $var_sub_property,$useProperties[$j]);
	                if(count($triplets['delete'])){
	                    $query = 'delete { '.implode($triplets['delete'],' . ').'}';
	                    $this->handler->data_query($query);
	                }
	                if(count($triplets['insert'])){
	                    $query = 'insert into <pmb> { '.implode($triplets['insert'],' . ').'}';
	                    $this->handler->data_query($query);
	                }
	            }
	        }
	    }
	    // Les infos viennent de bouger, on flushe le cache APCU si y'en a un...
	    $cache = cache_factory::getCache();
	    $query = 'select ontology_pmb_name from ontologies where id_ontology ='.$this->params->ontology_id;
	    $name = pmb_mysql_result(pmb_mysql_query($query),0,0);
	    $cache->deleteFromCache('onto_'.$name.'_index_infos');
	    
        // Fin, on réaffiche la liste
        print $this->get_menu();
	    $this->proceed_list();
	}
	
	/**
	 * 
	 * @param string $class_uri
	 * @param array $property
	 * @param string $var_property
	 * @param string $onProperty
	 * @return array[]|string
	 */
	private function buildIndexationTriplets($class_uri,$property,$var_property,$useProperty=[])
	{ 
	    $triplets =[
	        'insert' => [],
	        'delete' => []
	    ];
	    // On recupère le contenu du formulaire !
	    $var_property_pound = $var_property.'_pound';
	    $var_property_index = $var_property.'_index_uri';
	    $var_property_field = $var_property.'_field';
	    $var_property_subfield = $var_property.'_subfield';
	    global ${$var_property_pound},${$var_property_index},${$var_property_field},${$var_property_subfield};
	    $pound = intval(${$var_property_pound});
	    $index_uri = ${$var_property_index};
	    $field = intval(${$var_property_field});
	    $subfield = intval(${$var_property_subfield});
	    $onProperty = $property->property;
	    if(!empty($useProperty)){
	        $useProperty = $useProperty->property;
	        $onRange = $property->range;
	    }
	    
	    if($pound>0){
	        // Nouvelle entrée ! On doit tout reprendre
	        if(empty($index_uri)){
	            $index_uri = onto_common_uri::get_new_uri(self::INDEXATION_CLASS_URI);
	            $triplets['insert'][] = '<'.$index_uri.'> rdf:type <'.self::INDEXATION_CLASS_URI.'>';
	            $triplets['insert'][] = '<'.$index_uri.'> pmb:field '.$field;
	            $triplets['insert'][] = '<'.$index_uri.'> pmb:subfield '.$subfield;
	            $triplets['insert'][] = '<'.$index_uri.'> owl:onProperty <'.$onProperty.'>';
	            if(!empty($useProperty)){
	                $triplets['insert'][] = '<'.$index_uri.'> pmb:useProperty <'.$useProperty.'>';
	                $triplets['insert'][] = '<'.$index_uri.'> pmb:onRange <'.$onRange.'>';
	            }
	            $triplets['insert'][] = '<'.$index_uri.'> pmb:subfield '.$subfield;
	            $triplets['insert'][] = '<'.$class_uri.'> pmb_onto:indexWith <'.$index_uri.'>';
	            // C'est bien la property sous-classe qui permet de déclencher l'indexation actuellement... Probablement à revoir un jour
	            $triplets['insert'][] = '<'.$class_uri.'> rdfs:subClassOf <'.$index_uri.'>';
	        }else{
	            // Ca existe déjà, on s'assure juste que la pondération est bien mise à jour !
	            $triplets['delete'][] = '<'.$index_uri.'> pmb:pound ?pond ';
	        }
	        $triplets['insert'][] = '<'.$index_uri.'> pmb:pound '.$pound;
	    }else{
	        // Pas de pondération, on vire ce qui existe !
	        if(!empty($index_uri)){
    	        $triplets['delete'][] = '<'.$index_uri.'> ?s ?o';
    	        $triplets['delete'][] = '<'.$class_uri.'> pmb_onto:indexWith <'.$index_uri.'>'; 
    	        $triplets['delete'][] = '<'.$class_uri.'> rdfs:subClassOf <'.$index_uri.'>';
	        }
	    }
	    return $triplets;
	}
}