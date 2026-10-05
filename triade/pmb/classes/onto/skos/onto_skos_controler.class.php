<?php
// +-------------------------------------------------+
// e 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_skos_controler.class.php,v 1.108.4.1 2025/03/04 15:50:02 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Ark\Entities\ArkEntityPmb;

global $class_path;
require_once($class_path."/vedette/vedette_composee.class.php");
require_once($class_path."/authority.class.php");
require_once($class_path."/aut_pperso.class.php");
require_once($class_path."/aut_link.class.php");
require_once($class_path."/audit.class.php");
require_once($class_path."/skos/skos_concept.class.php");
require_once($class_path."/onto/common/onto_common_controler.class.php");

class onto_skos_controler extends onto_common_controler {

	/** L'uri des schema **/
	protected static $concept_scheme_uri='http://www.w3.org/2004/02/skos/core#ConceptScheme';
	protected static $concept_uri='http://www.w3.org/2004/02/skos/core#Concept';

	protected static $onto_index;


	/**
	 * Gere la variable session breadcrumb qui garde les id presents dans la navigation
	 * permet la construction du fil de navigation dans le thesaurus
	 * renvoie un tableau des id de parents parcouru
	 *
	 * @param onto_handler $handler
	 * @param onto_param $params
	 * @param bool $reset
	 *
	 * @return array breadcrumb
	 */
	public function handle_breadcrumb($reset=false){
		$breadcrumb = array();


		// on enregistre la navigation en session en cas de conflit
		// Attention aux recherches concurrentes, on perdrait le fil de la navigation en cas de polyhierarchie
		if($this->params->parent_id && !preg_match('/\-'.$this->params->parent_id.'\-/',$_SESSION['breadcrumb'])){
		    $_SESSION['breadcrumb'].='-'.$this->params->parent_id.'-';
		}elseif($this->params->parent_id && !preg_match('/\-'.$this->params->parent_id.'\-$/',$_SESSION['breadcrumb'])){
		    $_SESSION['breadcrumb']=substr($_SESSION['breadcrumb'],0, strpos($_SESSION['breadcrumb'], '-'.$this->params->parent_id.'-')+strlen('-'.$this->params->parent_id.'-'));
		}elseif(!$this->params->parent_id){
		    $_SESSION['breadcrumb']='';
		    return $_SESSION['breadcrumb'];
		}
		// on a clique une fois sur le petit dossier...
		if($this->params->parent_id){
		    $i=0;
		    $current = $this->params->parent_id;
		    $breadcrumb[] =$current;
		    while(($this->has_broader(onto_common_uri::get_uri($current), $this->params)) && $i<10){
                $broaders = $this->get_broaders(onto_common_uri::get_uri($current), $this->params);
                if(count($broaders)> 1){
                    //LE cas ou il y a plusieurs parents dans le meme schema
                    // onregarde du cote de la session pour voir si on retrouve une navigation prealable
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
                //anti-loop sommaire
                if(in_array($broaders[0]['id'],$breadcrumb)){
                    return $breadcrumb;
                }
                array_unshift($breadcrumb, $broaders[0]['id']);
                $current = $broaders[0]['id'];
                $i++;
		    }
		}
		return $breadcrumb;
	}

	/**
	 * renvoie la liste des schema
	 *
	 * @return array
	 */
	public function get_scheme_list(){
		$params=new onto_param();
		$params->page = 1;
		$params->nb_per_page = 0;
		$params->action = "list";
		return $this->get_list(self::$concept_scheme_uri,$params);
	}

	public function get_list($class_uri,$params){
		switch($class_uri){
			case self::$concept_uri :
				return $this->get_hierarchized_list($class_uri,$params);
				break;
			default :
				return parent::get_list($class_uri,$params);
				break;
		}
	}

	/**
	 * renvoie le nombre d'enfants d'un noeud.
	 *
	 * @param string $class_uri
	 * @param onto_param $params
	 *
	 * @return int
	 */
	public function has_narrower($class_uri,$params){
		if(empty($params->concept_scheme) || in_array(-1,$params->concept_scheme)){
	        // Cas de tous les schemas, on veut ceux du/des schemas du concept
	        $query = "select * where {
	        <".$class_uri."> <http://www.w3.org/2004/02/skos/core#narrower> ?child .
            <".$class_uri."> <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme .
	        ?child <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme
	    } limit 1 offset 0";
	    }else if(in_array(0,$params->concept_scheme)){
	        // Cas des sans schema
	        $query = "select * where {
	        <".$class_uri."> <http://www.w3.org/2004/02/skos/core#narrower> ?child .
	        ?child pmb:showInTop owl:Nothing
	    } limit 1 offset 0";
	    }else{
	        //On a bien un schema par defaut specifie
	        $query = "select * where {
			<".$class_uri."> <http://www.w3.org/2004/02/skos/core#narrower> ?child .
			?child <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme .
            filter(";
	        $filter= "";
	        for($i=0 ; $i<count($params->concept_scheme) ; $i++){
	            if($filter) $filter.= " || ";
	            $filter.= "
                 ?scheme = <".onto_common_uri::get_uri($params->concept_scheme[$i]).">";
	        }
            $query.= $filter.")
		} limit 1 offset 0";
	    }
		$this->handler->data_query($query);
		return $this->handler->data_num_rows();
	}

	/**
	 * renvoie le nombre de parents d'un noeud.
	 *
	 * @param string $class_uri
	 * @param onto_param $params
	 *
	 * * @return int
	 */
	public function has_broader($class_uri,$params){
		if (!$class_uri) {
			return false;
		}
		if(empty($params->concept_scheme) || $params->concept_scheme[0] == -1){
	        // Cas de tous les schemas, on veut ceux du/des schemas du concept
	        $query = "select * where {
	        <".$class_uri."> <http://www.w3.org/2004/02/skos/core#broader> ?parent .
            <".$class_uri."> <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme .
	        ?parent <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme
	    } limit 1 offset 0";
	    }else if($params->concept_scheme[0] == 0){
	        // Cas des sans schema
	        $query = "select * where {
	        <".$class_uri."> <http://www.w3.org/2004/02/skos/core#broader> ?parent .
	        ?parent pmb:showInTop owl:Nothing
	    } limit 1 offset 0";
	    }else{
	        //On a bien un schema par defaut specifie
	        $query = "select * where {
			<".$class_uri."> <http://www.w3.org/2004/02/skos/core#broader> ?parent .
			?parent <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme .
            filter(";
	        $filter= "";
	        for($i=0 ; $i<count($params->concept_scheme) ; $i++){
	            if($filter) $filter.= " || ";
	            $filter.= "
                 ?scheme = <".onto_common_uri::get_uri($params->concept_scheme[$i]).">";
	        }
            $query.= $filter.")
		} limit 1 offset 0";
	    }
		$this->handler->data_query($query);
		return $this->handler->data_num_rows();
	}

	/**
	 * renvoie les parents d'un noeud
	 *
	 * @param string $class_uri
	 * @param onto_param $params
	 * @return array
	 */
	public function get_broaders($class_uri,$params){
	    if(!empty($params->concept_scheme) && $params->concept_scheme[0] == -1){
	        // Cas de tous les schemas, on veut ceux du/des schemas du concept
	        $query = "select * where {
	        <".$class_uri."> <http://www.w3.org/2004/02/skos/core#broader> ?parent .
            <".$class_uri."> <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme .
	        ?parent <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme
            }";
	    }else if(empty($params->concept_scheme) || $params->concept_scheme[0] == 0){
	        // Cas des sans schema
	        $query = "select * where {
	        <".$class_uri."> <http://www.w3.org/2004/02/skos/core#broader> ?parent .
	        ?parent pmb:showInTop owl:Nothing
            }";
	    }else{
	        //On a bien un schema par defaut specifie
	        $query = "select * where {
			<".$class_uri."> <http://www.w3.org/2004/02/skos/core#broader> ?parent .
			?parent <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme .
            filter(";
	        $filter= "";
	        for($i=0 ; $i<count($params->concept_scheme) ; $i++){
	            if($filter) $filter.= " || ";
	            $filter.= "
                 ?scheme = <".onto_common_uri::get_uri($params->concept_scheme[$i]).">";
	        }
            $query.= $filter.")
            }";
	    }
		$this->handler->data_query($query);
 		$results=$this->handler->data_result();
		if(!empty($results)){
			$return=array();
			foreach ($results as $key=>$result){
				$return[$key]["id"]=onto_common_uri::get_id($result->parent);
				$return[$key]["label"] = $this->get_data_label($result->parent);
			}
			return $return;
		}
		return array();
 	}

	/**
	 * renvoie les concepts generiques d'un noeud
	 *
	 * @param string $class_uri
	 * @return array
	 */
	public function get_broaders_uri($uri){
	    if ($uri) {
    		$query .= "select ?broader where {
    			<".$uri."> <http://www.w3.org/2004/02/skos/core#broader> ?broader .
    		}";
    		$this->handler->data_query($query);
    		$results=$this->handler->data_result();

    		if(is_array($results)){
    			$return=array();
    			foreach ($results as $result){
    				$return[] = $result->broader;
    			}
    			return $return;
    		}
	    }
		return array();
 	}

	/**
	 * renvoie les concepts specifiques d'un noeud
	 *
	 * @param string $class_uri
	 * @return array
	 */
	public function get_narrowers_uri($uri){
	    if ($uri) {
    		$query .= "select ?narrower where {
    			<".$uri."> <http://www.w3.org/2004/02/skos/core#narrower> ?narrower .
    		}";
    		$this->handler->data_query($query);
    		$results=$this->handler->data_result();

    		if(is_array($results)){
    			$return=array();
    			foreach ($results as $result){
    				$return[] = $result->narrower;
    			}
    			return $return;
    		}
	    }
		return array();
 	}

	/**
	 * Retourne une liste hierarchisee
	 *
	 * @param string $class_uri
	 * @param onto_param $params
	 * @return array
	 */
	public function get_hierarchized_list($class_uri,$params,$user_query_var='deb_rech'){
		global $lang;
	    global $pmb_allow_authorities_first_page;

		$page=$params->page-1;
		$displayLabel=$this->handler->get_display_label(self::$concept_uri);

		$filter = "";
		$query = "select ?elem ?label where {
			?elem rdf:type <".self::$concept_uri."> .
			?elem <".$displayLabel."> ?label ";
		$counted = false;

		$more = "";

		if (!empty($params->authority_statut)) {
			$more = " .
			?elem pmb:has_authority_status '".$params->authority_statut."'";
		}

		if($pmb_allow_authorities_first_page == 0 && !$params->parent_id && ($params->action == 'list_selector' && !$params->{$user_query_var})){
			$list = array(
					'nb_total_elements' => 	0,
					'nb_onto_element_per_page' => $params->nb_per_page,
					'page' => 0
			);
			$list['elements']=array();
		}else{
			if(!$params->parent_id){
				//retourne les top concepts
				if($params->only_top_concepts){
					if($params->concept_scheme[0] == 0) {
						$more.= " .
						?elem pmb:showInTop owl:Nothing";
						$count_query = "select count(?elem) as ?nb where{ ?elem pmb:showInTop owl:Nothing }";
						$this->handler->data_query($count_query);
						if($this->handler->data_num_rows()){
							$counted = true;
							$result = $this->handler->data_result();
							$this->nb_results = $result[0]->nb;
						}
					}else if ($params->concept_scheme[0] != -1) {
						$more.= " .	?elem skos:topConceptOf ?scheme .
                        filter(";
            	        $filter= "";
            	        for($i=0 ; $i<count($params->concept_scheme) ; $i++){
            	            if($filter) $filter.= " || ";
            	            $filter.= "
                             ?scheme = <".onto_common_uri::get_uri($params->concept_scheme[$i]).">";
            	        }
                        $more.= $filter.") ";
						$count_query = "select count(?elem) as ?nb where{ ?elem skos:topConceptOf ?scheme .
                        filter(";
            	        $filter= "";
            	        for($i=0 ; $i<count($params->concept_scheme) ; $i++){
            	            if($filter) $filter.= " || ";
            	            $filter.= "
                             ?scheme = <".onto_common_uri::get_uri($params->concept_scheme[$i]).">";
            	        }
            	        $count_query.= $filter.")}";
						$this->handler->data_query($count_query);
						if($this->handler->data_num_rows()){
							$counted = true;
							$result = $this->handler->data_result();
							$this->nb_results = $result[0]->nb;
						}
					} else {
						$more.= " .	?elem skos:topConceptOf ?top";
					}
				} else {
					if (!empty($params->concept_scheme) && $params->concept_scheme[0] == 0) {
						// On affiche les concepts qui n'ont pas de schema
						$more.= " .
						optional {
							?elem <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme
						}
						filter (!bound(?scheme))
						";
					} else if (!empty($params->concept_scheme) && $params->concept_scheme[0] != -1) {
						// On n'affiche qu'un schema
						$more.= " .
						?elem <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme .
                        filter(";
            	        $filter= "";
            	        for($i=0 ; $i<count($params->concept_scheme) ; $i++){
            	            if($filter) $filter.= " || ";
            	            $filter.= "
                             ?scheme = <".onto_common_uri::get_uri($params->concept_scheme[$i]).">";
            	        }
            	        $more.= $filter.")";
					}
				}
				$query.=$more;
				$this->nb_results=$this->handler->get_nb_elements(self::$concept_uri,$more);
			}else{
				//retourne les enfants du parent
				$more = "
					. ?elem <http://www.w3.org/2004/02/skos/core#broader> <".onto_common_uri::get_uri($params->parent_id).">";

				if ($params->concept_scheme[0] == 0) {
					// On affiche les concepts qui n'ont pas de schema
					$more.= " .
						optional {
							?elem <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme
						}
						filter (!bound(?scheme))
						";
				} else if ($params->concept_scheme[0] != -1) {
					// On n'affiche qu'un schema
					$more.= " .
					?elem <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme .
                    filter(";
        	        $filter= "";
        	        for($i=0 ; $i<count($params->concept_scheme) ; $i++){
        	            if($filter) $filter.= " || ";
        	            $filter.= "
                         ?scheme = <".onto_common_uri::get_uri($params->concept_scheme[$i]).">";
        	        }
        	        $more.= $filter.")";

				}
				$query.=$more;
				$this->nb_results=$this->handler->get_nb_elements(self::$concept_uri,$more);
			}

			$query.= " } group by ?elem order by ?label limit ".$params->nb_per_page;
			$query.= " offset ".($page*$params->nb_per_page);
			$this->handler->data_query($query);
			$results=$this->handler->data_result();

			$list = array(
					'nb_total_elements' => $this->nb_results,
					'nb_onto_element_per_page' => $params->nb_per_page,
					'page' => $page
			);
			$list['elements']=array();
			if($this->handler->data_num_rows()){
				foreach($results as $result){
					if(isset($result->elem) && $result->elem) {
						$skos_concept = new skos_concept(0, $result->elem);
						if(!isset($list['elements'][$result->elem]['default']) || !$list['elements'][$result->elem]['default']){
							$list['elements'][$result->elem]['default'] = $skos_concept->get_isbd();
						}
						if(isset($result->label_lang) && substr($lang,0,2) == $result->label_lang){
							$list['elements'][$result->elem][$lang] = $skos_concept->get_isbd();
						}
					}
				}
			}
		}
		return $list;
	}

	/**
	 * Derivation de l'aiguilleur principal pour les ajouts d'elements dans les selecteurs
	 */
	public function proceed(){
		global $save_and_continue, $save_and_create_concept, $PMBuserid;

		$this->init_item();
		switch($this->params->action){
			case "selector_add" :
				$this->proceed_selector_add();
				break;
			case "selector_save" :
				$this->proceed_selector_save();
				break;
			case "save":
				if( get_class($this->item) == "onto_skos_concept_item"){
					$entity_locking = new entity_locking(onto_common_uri::get_id($this->item->get_uri()), TYPE_CONCEPT);
					if (onto_common_uri::get_id($this->item->get_uri()) && $entity_locking->is_locked()) {
				        if($PMBuserid == $entity_locking->get_locked_user_id()){
				            $entity_locking->unlock_entity();
				            $this->proceed_save(false);
				            if ($save_and_continue) {
				                print "<script>document.location='./".$this->get_base_resource()."categ=".$this->params->categ."&sub=".$this->params->sub."&id=&parent_id=".$this->params->parent_id."&concept_scheme=".implode(",",$this->params->concept_scheme)."&action=edit';</script>";
				                break;
				            }
				            $authority_page = new skos_page_concept(onto_common_uri::get_id($this->item->get_uri()));
				            $authority_page->proceed();
				        }else{
				            print $entity_locking->get_save_error_message();
				        }
				    } else{
				        $this->proceed_save(false);
				        if ($save_and_continue) {
			                print "<script>document.location='./".$this->get_base_resource()."categ=".$this->params->categ."&sub=".$this->params->sub."&id=&parent_id=".$this->params->parent_id."&concept_scheme=".implode(",",$this->params->concept_scheme)."&action=edit';</script>";
				            break;
				        }
				        $authority_page = new skos_page_concept(onto_common_uri::get_id($this->item->get_uri()));
				        $authority_page->proceed();
				    }
				}else{
					if ($save_and_create_concept) {
						$this->proceed_save(false);
						print "<script>document.location='./".$this->get_base_resource()."categ=".$this->params->categ."&sub=concept&id=&parent_id=&concept_scheme=".onto_common_uri::get_id($this->item->get_uri())."&action=edit';</script>";
						break;
					}
				    print $this->get_menu();
					$this->proceed_save();
				}

				break;
			case "search" :
				print $this->get_menu();
				// On met e jour le dernier schema selectionne
				if (isset($this->params->concept_scheme) && (count($this->params->concept_scheme)> 0)) {
					$_SESSION['onto_skos_concept_last_concept_scheme'] = $this->params->concept_scheme;
				}
				if (isset($this->params->only_top_concepts)) {
					$_SESSION['onto_skos_concept_only_top_concepts'] = $this->params->only_top_concepts;
				}
				$_SESSION['onto_skos_concept_selector_last_parent_id'] = "";

				//si on peut on s'evite le processus de recherche... il est moins fluide !
				if($this->params->user_input == "*" && $this->params->concept_scheme[0] == -1 && $this->params->authority_statut == 0){
					$this->proceed_list();
				}else{
					$this->proceed_search();
				}
				break;
			case "last" :
				print $this->get_menu();
				$_SESSION['onto_skos_concept_selector_last_parent_id'] = "";
				$this->proceed_last();
				break;
			case "replace" :
			    $entity_locking = new entity_locking(onto_common_uri::get_id($this->item->get_uri()), TYPE_CONCEPT);
			    if($entity_locking->is_locked()){
			        print $entity_locking->get_locked_form();
			        break;
			    }
				$this->proceed_replace();
				break;
			case "duplicate":
				$this->item->set_uri(onto_common_uri::get_temp_uri($this->item->get_onto_class()->uri));
				$this->proceed_edit();
				break;
			case "merge":
			    $this->proceed_merge();
			    break;
			case "edit" :
			    if(!onto_common_uri::is_temp_uri($this->item->get_uri())){
			        $entity_locking = new entity_locking(onto_common_uri::get_id($this->item->get_uri()), TYPE_CONCEPT);
			        if($entity_locking->is_locked()){
			            print $entity_locking->get_locked_form();
			            break;
			        }
			        print $this->get_menu();
			        $this->proceed_edit();
			    }else{
			        print $this->get_menu();
			        $this->proceed_edit();
			    }


// 			    $entity_locking = new entity_locking(onto_common_uri::get_id($this->item->get_uri()), TYPE_CONCEPT);
// 			    if(!$entity_locking->is_locked() || $this->params->force_unlock){
// 			        print $this->get_menu();
// 			        $this->proceed_edit();
// 			    }else{
// 			        print $entity_locking->get_force_form();
// 			    }
			    break;
			case "delete" :
			    $entity_locking = new entity_locking(onto_common_uri::get_id($this->item->get_uri()), TYPE_CONCEPT);
			    if($entity_locking->is_locked()){
			        print $entity_locking->get_locked_form();
			        break;
			    }
			    print $this->get_menu();
			    $this->proceed_delete(true);
			    break;
			default :
				$_SESSION['onto_skos_concept_selector_last_parent_id'] = "";
				return parent::proceed();
				break;
		}
	}

	protected function init_item(){
		if($this->params->action == "selector_add"){
			//dans le selecteur, c'est forcement un nouveau...
			$this->item = $this->handler->get_item($this->get_item_type_to_list($this->params),"");
		}else if($this->params->action == "selector_save"){
			//lors d'une sauvegarde d'un item, on a poste l'uri
			$this->item = $this->handler->get_item($this->get_item_type_to_list($this->params), $this->params->item_uri);
		}else{
			//on reinvente pas la roue
			parent::init_item();
		}
	}

	protected function proceed_edit(){

	    $unlock_unload_script = "";
	    if(!onto_common_uri::is_temp_uri($this->item->get_uri())){
	        $entity_locking = new entity_locking(onto_common_uri::get_id($this->item->get_uri()), TYPE_CONCEPT);
	        $entity_locking->lock_entity();
	        $unlock_unload_script = $entity_locking->get_polling_script();
	    }
		print $this->item->get_form("./".$this->get_base_resource()."categ=".$this->params->categ."&sub=".$this->params->sub."&id=".$this->params->id."&parent_id=".$this->params->parent_id."&concept_scheme=".implode(",",$this->params->concept_scheme));
		print $unlock_unload_script;
	}

	protected function proceed_selector_save(){
			$this->item->get_values_from_form();
			$saved = $this->handler->save($this->item);
			$query = "select ?scheme ?broader ?broaderScheme where{
				<".$this->item->get_uri()."> rdf:type skos:Concept .
				<".$this->item->get_uri()."> skos:inScheme ?scheme .
				optional {
					<".$this->item->get_uri()."> skos:broader ?broader .
					?broader skos:inScheme ?broaderScheme
				}
			} order by ?scheme ?broader";
			$this->handler->data_query($query);
			if($this->handler->data_num_rows()){
				$results = $this->handler->data_result();
				$lastScheme=$results[0]->scheme;
				$flag = true;
				foreach($results as $result){
					if($result->scheme == $result->broaderScheme){
						$flag = false;
					}
					if($lastScheme != $result->scheme){
						if($flag){
							$query = "insert into <pmb> {<".$this->item->get_uri()."> pmb:showInTop <".$lastScheme.">}";
							$this->handler->data_query($query);
						}
						$flag = true;
						$lastScheme = $result->scheme;
					}
				}
				if($flag){
					$query = "insert into <pmb> {<".$this->item->get_uri()."> pmb:showInTop <".$lastScheme.">}";
					$this->handler->data_query($query);
				}
			}else{
				$query = "select * where{
				<".$this->item->get_uri()."> rdf:type skos:Concept .
				optional{
				 <".$this->item->get_uri()."> skos:inScheme ?scheme .
				} . filter(!bound(?scheme)) .
				 optional {
					<".$this->item->get_uri()."> skos:broader ?broader .
					?broader skos:inScheme ?broaderScheme
				} filter (!bound(?broaderScheme))
			} ";
				$this->handler->data_query($query);
				if(!$this->handler->data_num_rows()){
					$query = "insert into <pmb> {<".$this->item->get_uri()."> pmb:showInTop owl:Nothing}";
					$this->handler->data_query($query);
				}
			}

			$ui_class_name=self::resolve_ui_class_name($this->params->sub,$this->handler->get_onto_name());
			//ils sont nouveaux dont pas encore utilise...pas besoin du hook pour les notices...
			if($saved !== true){
				$ui_class_name::display_errors($this,$saved);
			}else{
				//sauvegarde des autorites liees pour les concepts...
				//Ajout de la sauvegarde du statut si c'est un concept egalement
				if( get_class($this->item) == "onto_skos_concept_item"){
					global $authority_statut;
					$authority_statut = intval($authority_statut);

					$concept_id = onto_common_uri::get_id($this->item->get_uri());
					$aut_link= new aut_link(AUT_TABLE_CONCEPT, $concept_id);
					$aut_link->save_form();

					$aut_pperso = new aut_pperso("skos", $concept_id);
					$aut_pperso->save_form();

					//Ajout de la reference dans la table authorities
					$authority = new authority(0, $concept_id, AUT_TABLE_CONCEPT);
					$authority->set_num_statut($authority_statut);
					$authority->update();
				}

	// 			$this->proceed_list();
				$this->params->action = "list_selector";
				$this->params->deb_rech = "\"".$this->item->get_label("http://www.w3.org/2004/02/skos/core#prefLabel")."\"";
	//  		$this->params->parent_id = $_SESSION['onto_skos_concept_selector_last_parent_id'];
				return parent::proceed();
			}
		}

	protected function proceed_selector_add(){
		//on en aura besoin e la sauvegarde...
		$_SESSION['onto_skos_concept_selector_last_parent_id'] = $this->params->parent_id;
		//reglons rapidement ce probleme... cf. dette technique
 		//print "<div id='att'></div>";
 		$type = $this->get_item_type_to_list($this->params,true);
// 		print $this->item->get_form($this->params->base_url."&range=".(isset($this->params->range) ? $this->params->range : ''), $type."_selector_form", "selector_save");
 		print $this->item->get_form($this->params->base_url, '', "update");
	}

	/*
	 * On hook la sauvegarde pour declencher la reindexation des elements impactes
	 */
	protected function proceed_save($list=true){
	    global $pmb_map_activate;

		$this->item->get_values_from_form();

		if (onto_common_uri::is_temp_uri($this->item->get_uri())) {
			audit::insert_creation(AUDIT_CONCEPT, $this->item->get_id());
		} else {
			audit::insert_modif(AUDIT_CONCEPT, $this->item->get_id());
		}

		//on stocke les anciens concepts specifiques et generiques avant de vider le store
		$keep = array();
		if (!empty($this->params->thesaurus_concepts_autopostage)) {
            $keep = array('pmb:broadPath', 'pmb:narrowPath');
		}

		// On supprime les prefLabel dans les tables d'index pour le reaffichage
		if($this->item->get_id()){
		    $this->delete_prefLabel($this->item->get_id());
		}

		$result = $this->handler->save($this->item, $keep);

		if($result !== true){
			$ui_class_name=self::resolve_ui_class_name($this->params->sub,$this->handler->get_onto_name());
			$ui_class_name::display_errors($this,$result);
		}else{
			//TODO: reprendre ce hack un peu crade
			//pour faciliter les requetes SPARQL en gestion, on ajoute une propriete qui sort de nulle part... pmb:showInTop si pas de parent dans le schema

			$query = "select ?scheme ?broader ?broaderScheme where{
				<".$this->item->get_uri()."> rdf:type skos:Concept .
				<".$this->item->get_uri()."> skos:inScheme ?scheme .
				optional {
					<".$this->item->get_uri()."> skos:broader ?broader .
					?broader skos:inScheme ?broaderScheme
				}
			} order by ?scheme ?broader";
			$this->handler->data_query($query);
			if($this->handler->data_num_rows()){
				$results = $this->handler->data_result();
				$lastScheme=$results[0]->scheme;
				$flag = true;
				foreach($results as $result){
					if(!empty($result->broaderScheme) && ($result->scheme == $result->broaderScheme)){
						$flag = false;
					}
					if($lastScheme != $result->scheme){
						if($flag){
							$query = "insert into <pmb> {<".$this->item->get_uri()."> pmb:showInTop <".$lastScheme.">}";
							 $this->handler->data_query($query);
						}
						$flag = true;
						$lastScheme = $result->scheme;
					}
				}
				if($flag){
					$query = "insert into <pmb> {<".$this->item->get_uri()."> pmb:showInTop <".$lastScheme.">}";
					$this->handler->data_query($query);
				}
			}else{
				$query = "select * where{
				<".$this->item->get_uri()."> rdf:type skos:Concept .
				optional{
				 <".$this->item->get_uri()."> skos:inScheme ?scheme .
				} . filter(!bound(?scheme)) .
				 optional {
					<".$this->item->get_uri()."> skos:broader ?broader .
					?broader skos:inScheme ?broaderScheme
				} filter (!bound(?broaderScheme))
			} ";
				$this->handler->data_query($query);
				if(!$this->handler->data_num_rows()){
					$query = "insert into <pmb> {<".$this->item->get_uri()."> pmb:showInTop owl:Nothing}";
					$this->handler->data_query($query);
				}
			}

			//sauvegarde des autorites liees pour les concepts...
			//Ajout de la sauvegarde du statut si c'est un concept egalement
			if( get_class($this->item) == "onto_skos_concept_item"){
				global $authority_statut;
				$authority_statut = intval($authority_statut);

				$concept_id = $this->item->get_id();

				if($pmb_map_activate){
				    $map = new map_edition_controler(AUT_TABLE_CONCEPT, $concept_id);
				    $map->save_form();
				}
				$aut_link= new aut_link(AUT_TABLE_CONCEPT, $concept_id);
				$aut_link->save_form();

				$aut_pperso = new aut_pperso("skos", $concept_id);
				$aut_pperso->save_form();

				//Ajout de la reference dans la table authorities
				$authority = new authority(0, $concept_id, AUT_TABLE_CONCEPT);
				$authority->set_num_statut($authority_statut);
				$authority->update();

				$query = "insert into <pmb> {
						<".$this->item->get_uri()."> pmb:has_authority_status '".$authority->get_num_statut()."'
					}";
				$this->handler->data_query($query);
			}
		}

		// Mise e jour des vedettes composees contenant cette autorite
		vedette_composee::update_vedettes_built_with_element($this->item->get_id(), TYPE_CONCEPT);
		indexation_stack::push($this->item->get_id(), TYPE_CONCEPT);
		//reindexation des notices indexes avec le concepts
		index_concept::update_linked_elements($this->item->get_id());

		if($list){
			$this->proceed_list();
		}else{
			return $this->item->get_id();
		}
	}
	protected function get_force_form($message) {
	    global $current_module, $msg, $charset;

	    return "
			<br /><div class='erreur'>$msg[540]</div>
			<script type='text/javascript' src='./javascript/tablist.js'></script>
			<div class='row'>
				<div class='colonne10'>
					<img src='".get_url_icon('error.gif')."' class='align_left'>
				</div>
				<div class='colonne80'>
					<strong>$message</strong>
				</div>
			</div>
			<div class='row'>
				<form class='form-$current_module' name='dummy'  method='post' action='" . $this->get_base_resource() . "categ=concepts&sub=concept&id=" . $this->item->get_id()  . "&parent_id=&concept_scheme=&action=confirm_delete'>
					<input type='hidden' name='force_delete' value='1'>
					<input type='hidden' name='ret_url' value='" . $this->get_base_resource() . "categ=concepts&sub=concept&action=edit&id=" . $this->item->get_id() . "'>
					<input type='button' name='ok' class='bouton' value=' $msg[76] ' onClick='history.go(-1);'>
					<input type='submit' class='bouton' name='bt_forcage' value=' ".htmlentities($msg["autorite_suppr_categ_forcage_button"], ENT_QUOTES,$charset)." '>
				</form>
			</div>
			";
	}

	/*
	 * On hook la suppression pour verifier l'utilisation au prealable
	 */
	protected function proceed_delete($force_delete = false, $print = true){
		global $msg;

		// On declare un flag pour savoir si on peut continuer la suppression
		$deletion_allowed = true;

		$message  = $this->item->get_label($this->handler->get_display_label($this->handler->get_class_uri($this->params->categ)));

		// On regarde si le concdept est utilise pour indexer d'autres elements (tbl index_concept)
		$query = "select num_object from index_concept where num_concept = ".onto_common_uri::get_id($this->item->get_uri());
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$deletion_allowed = false;
			$message.= "<br/>".$msg['concept_use_cant_delete'];
		}

		// On regarde si l'autorite est utilisee dans des vedettes composees
		$attached_vedettes = vedette_composee::get_vedettes_built_with_element(onto_common_uri::get_id($this->item->get_uri()), TYPE_CONCEPT);
		if (count($attached_vedettes)) {
			// Cette autorite est utilisee dans des vedettes composees, impossible de la supprimer
			$deletion_allowed = false;
			$message.= "<br/>".$msg['vedette_dont_del_autority'];
		}

		if(($usage = aut_pperso::delete_pperso(AUT_TABLE_CONCEPT, $this->item->get_uri(), $force_delete))){
			// Cette autorite est utilisee dans des champs perso, impossible de supprimer
			$deletion_allowed = false;
			$message.= '<br />'.$msg['autority_delete_error'].'<br /><br />'.$usage['display'];
		}

		if ($force_delete || $deletion_allowed) {
			audit::delete_audit(AUDIT_CONCEPT, $this->item->get_id());
			// On peut continuer la suppression
			$id_vedette = vedette_link::get_vedette_id_from_object($this->item->get_id(), TYPE_CONCEPT_PREFLABEL);
			$vedette = new vedette_composee($id_vedette);
			$vedette->delete();

			vedette_composee::delete_element_and_update_vedettes_built_with_element(onto_common_uri::get_id($this->item->get_uri()),TYPE_CONCEPT);

			//suppression des autorites liees... & des statuts des concepts
			// liens entre autorites
			if( get_class($this->item) == "onto_skos_concept_item"){
			    $concept_id = $this->item->get_id();

		        $map = new map_edition_controler(AUT_TABLE_CONCEPT, $concept_id);
		        $map->delete();

				$aut_link= new aut_link(AUT_TABLE_CONCEPT, $concept_id);
				$aut_link->delete();

				$aut_pperso = new aut_pperso("skos", $concept_id);
				$aut_pperso->delete();

				skos_concept::delete_autority_sources($concept_id);

				$query = "DELETE FROM index_concept where num_concept = $concept_id";
				pmb_mysql_query($query);

				$authority = new authority(0, $concept_id, AUT_TABLE_CONCEPT);
				$authority->delete();
			}
			parent::proceed_delete($force_delete, $print);
		} else {
		    print $this->get_force_form($message);
		}
	}

	protected function proceed_delete_from_cart($force_delete = false){
	    // on teste les liens entre concepts
	    $concept_links = $this->handler->is_object_of($this->item);
	    if (count($concept_links)) {
	        return $concept_links;
	    }
	    return parent::proceed_delete_from_cart($force_delete);
	}

	/**
	 * Place un concept en tete de hierarchie si il est dans un schema et qu'il n'a pas de broader
	 */
	protected function define_top_concept_of() {
		$query = "select ?scheme where {
				<".$this->item->get_uri()."> <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme .
				optional {
					<".$this->item->get_uri()."> <http://www.w3.org/2004/02/skos/core#topConceptOf> ?topscheme .
					filter (?topscheme = ?scheme)
				}
				filter (!bound(?topscheme))
				optional {
					<".$this->item->get_uri()."> <http://www.w3.org/2004/02/skos/core#broader> ?broader .
					?broader <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme
				}
				filter (!bound(?broader))
			}";
		// Details : on va chercher les schemas de l'item; pour chaque schema, on regarde si il est topconcept ou si il a un parent

		$this->handler->data_query($query);
		if($this->handler->data_num_rows()){
			// Le concept est dans des schemas dans lesquels il n'est pas topconcept et il n'a pas de parent
			// On le definit donc top concept de ces schemas
			$query = "insert into <pmb> {";

			$results = $this->handler->data_result();
			foreach($results as $result){
				$query .= "
					<".$this->item->get_uri()."> <http://www.w3.org/2004/02/skos/core#topConceptOf> <".$result->scheme."> .
					<".$result->scheme."> <http://www.w3.org/2004/02/skos/core#hasTopConcept> <".$this->item->get_uri()."> .";
			}
			$query .= "}";
		}
		$this->handler->data_query($query);
	}

	/**
	 * renvoie les informations d'un noeud
	 *
	 * @param string $uri
	 * @return array
	 */
	public function get_informations_concept($uri) {
		$query = "select ?scopeNote where {
					<$uri> rdf:type <".self::$concept_uri."> .
					optional {
						<$uri> skos:scopeNote ?scopeNote
					}
				}";

		$this->handler->data_query($query);
		$results = $this->handler->data_result();
		if (is_array($results) && !empty($results)) {
			$return = array();
			foreach ($results as $key => $result) {
				$return[$key]["scopeNote"] = $result->scopeNote;
			}
			return $return;
		}
		return array();
	}

	protected function proceed_ajax_selector(){
		//on regarde le range (multiple  ou pas..)
	    if (empty($this->params->att_id_filter)) {
	        $this->params->att_id_filter = "http://www.w3.org/2004/02/skos/core#Concept";
	    }
		$ranges = explode("|||",$this->params->att_id_filter);
		$list = array();
		foreach ($ranges as $range){
		    if ($range){
    			$elements = $this->get_ajax_searched_elements($range);
    			foreach($elements['elements'] as $key => $value){
    				$newKey = $key;
    				if($this->params->return_concept_id){
    					$newKey = onto_common_uri::get_id($key);
    				}
    				$list['elements'][$newKey] = $value;
    				if(count($ranges)>1){
    					$list['prefix'][$newKey]['libelle'] = $elements['label'];
    					$list['prefix'][$newKey]['id'] = $range;
    				}
    			}
		    }
		}
		return $list;
	}

	public function get_base_resource($with_params=true){
		return $this->params->base_resource.($with_params? "?" : "");
	}

	protected function proceed_last($no_print = false){
		$ui_class_name = self::resolve_ui_class_name($this->params->sub,$this->handler->get_onto_name());
		$result = $ui_class_name::get_search_form($this,$this->params);
		$result.= $ui_class_name::get_list($this,$this->params);
		$result = str_replace("!!caddie_link!!", entities_authorities_controller::get_caddie_link(), $result);
		if(!$no_print) echo($result);
		return $result;
	}

	/**
	 *
	 * Retourne les derniers elements crees
	 */
	public function get_last_elements(){
		global $lang;

		$page=$this->params->page-1;
		$query = "select uri, id_item, value, lang from skos_fields_global_index join onto_uri on id_item = uri_id where code_champ='1' order by id_item desc ";
		if ($page > 0) {
			$query.= " limit ".($page*$this->params->nb_per_page).", ".$this->params->nb_per_page;
		} elseif ($this->params->nb_per_page > 0) {
			$query.= " limit ".$this->params->nb_per_page;
		}

		$res=pmb_mysql_query($query);

		$list = array(
				'nb_onto_element_per_page' => $this->params->nb_per_page,
				'page' => $page,
				'elements' => array()
		);
		if (pmb_mysql_num_rows($res)) {
			while($result=pmb_mysql_fetch_object($res)) {
				if(empty($list['elements'][$result->uri]) || !$list['elements'][$result->uri]['default']){
					$list['elements'][$result->uri]['default'] = $result->value;
				}
				if($lang == $result->lang){
					$list['elements'][$result->uri][$result->lang] = $result->value;
				}
			}
		}
		$query = "SELECT DISTINCT id_item FROM skos_fields_global_index JOIN onto_uri ON id_item = uri_id WHERE code_champ='1'";
		$result = pmb_mysql_query($query);
		$list['nb_total_elements'] = pmb_mysql_num_rows($result);
		return $list;
	}

	protected function proceed_replace() {
	    global $pmb_synchro_rdf;
	    global $msg;
	    global $pmb_ark_activate;

		$by = $this->params->by;
		if (!$by) {
			print $this->item->get_replace_form("./".$this->get_base_resource()."categ=".$this->params->categ."&sub=".$this->params->sub."&id=".$this->params->id."&concept_scheme=".implode(",",$this->params->concept_scheme));
			return;
		}
		if (!is_numeric($by)) {
			$by = onto_common_uri::get_id($by);
		}
		if (($this->item->get_id() == $by) ||(!$this->item->get_id())) {
			return $msg['223'];
		}
		if ($by) {

    		$map = new map_edition_controler(AUT_TABLE_CONCEPT, $this->item->get_id());
    		$map->replace($by);

    		$aut_link = new aut_link(AUT_TABLE_CONCEPT, $this->item->get_id());
    		// "Conserver les liens entre autorites" est demande
    		if ($this->params->aut_link_save) {
    			// liens entre autorites
    			$aut_link->add_link_to(AUT_TABLE_CONCEPT, $by);
    		}
    		$aut_link->delete();

    		vedette_composee::replace(TYPE_CONCEPT, $this->item->get_id(), $by);

    		// nettoyage d'autorities_sources
    		$query = "select id_authority_source, authority_favorite from authorities_sources where num_authority = " .$this->item->get_id() ." and authority_type = 'concept'";
    		$result = pmb_mysql_query($query);
    		if (pmb_mysql_num_rows($result)) {
    			while ( $row = pmb_mysql_fetch_object($result) ) {
    				if ($row->authority_favorite ==1) {
    					// on suprime les references si l'autorite a ete importee...
    					$query = "delete from notices_authorities_sources where num_authority_source = " .$row->id_authority_source;
    					pmb_mysql_query($query);
    					$query = "delete from authorities_sources where id_authority_source = " .$row->id_authority_source;
    					pmb_mysql_query($query);
    				} else {
    					// on fait suivre le reste
    					$query = "update authorities_sources set num_authority = " .$by ." where id_authority_source = " .$row->id_authority_source;
    					pmb_mysql_query($query);
    				}
    			}
    		}

    		//Remplacement dans les champs persos selecteur d'autorite
    		aut_pperso::replace_pperso(AUT_TABLE_CONCEPT, $this->item->get_id(), $by);

    		if ($pmb_ark_activate) {
    		    $idReplaced = authority::get_authority_id_from_entity($this->item->get_id(), AUT_TABLE_CONCEPT);
    		    $idReplacing = authority::get_authority_id_from_entity($by, AUT_TABLE_CONCEPT);
    		    if ($idReplaced && $idReplacing) {
    		        $arkEntityReplaced = ArkEntityPmb::getEntityClassFromType(TYPE_AUTHORITY, $idReplaced);
    		        $arkEntityReplacing = ArkEntityPmb::getEntityClassFromType(TYPE_AUTHORITY, $idReplacing);
        		    $arkEntityReplaced->markAsReplaced($arkEntityReplacing);
    		    }
    		}

    		$onto_index = onto_index::get_instance($this->get_onto_name());
    		$onto_index->set_handler($this->handler);
    		$onto_index->maj($by);

    		//Remplacement de l'identifiant du concept source dans la table index concept
    		$query = "update index_concept set num_concept=".$by." where num_concept=".$this->item->get_id();
    		pmb_mysql_query($query);

    		$this->proceed_delete(true);
    		/**
    		 * Reindex des elements e la suite du remplacement des id du concept source
    		 */
    		index_concept::update_linked_elements($by);

    		// mise e jour de l'oeuvre rdf
    		if ($pmb_synchro_rdf) {
    			$synchro_rdf = new synchro_rdf();
    			$synchro_rdf->replaceAuthority($this->item->get_id(), $by, 'auteur');
    		}
		}
	}

	/**
	 * Retourne le label d'un data en fonction de son uri.
	 *
	 * @param string $uri
	 */
	public function get_data_label($uri){
		if(!empty($this->params->att_id_filter) && ($this->params->att_id_filter == self::$concept_uri)){
			$skos_concept = new skos_concept(0, $uri);
			return $skos_concept->get_isbd();
		}
		return parent::get_data_label($uri);
	}

	/**
	 *
	 * @param string $human_query
	 * @param array $tab
	 * @param string $type
	 * @param string $search_type
	 */
	protected function set_session_history($human_query, $search_type = "extended") {
		global $msg;

		if(!isset($_SESSION["session_history"])) $_SESSION["session_history"] = array();

		$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
		$_SESSION["session_history"][$_SESSION["CURRENT"]]['QUERY']["URI"] = './'.$this->params->base_resource;
		$_SESSION["session_history"][$_SESSION["CURRENT"]]['QUERY']["POST"] = $_POST;
		$_SESSION["session_history"][$_SESSION["CURRENT"]]['QUERY']["GET"] = $_GET;
		$_SESSION["session_history"][$_SESSION["CURRENT"]]['QUERY']["GET"]["sub"] = "";
		$_SESSION["session_history"][$_SESSION["CURRENT"]]['QUERY']["POST"]["sub"] = "";
		$_SESSION["session_history"][$_SESSION["CURRENT"]]['QUERY']["HUMAN_QUERY"] = $human_query;
		$_SESSION["session_history"][$_SESSION["CURRENT"]]['QUERY']["HUMAN_TITLE"] = "[".$msg["132"]."] ".$msg['ontology_skos_menu'];

		if ($_SESSION["CURRENT"] !== false) {
			$_SESSION["session_history"][$_SESSION["CURRENT"]]['AUT']["URI"] = './'.$this->params->base_resource;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]['AUT']["PAGE"] = $this->params->page;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]['AUT']["POST"] = $_POST;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]['AUT']["GET"] = $_GET;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]['AUT']["HUMAN_QUERY"] = $human_query;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]['AUT']["SEARCH_TYPE"] = $search_type;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]['AUT']["SEARCH_OBJECTS_TYPE"] = 'CONCEPTS';
			$_SESSION["session_history"][$_SESSION["CURRENT"]]['AUT']["HUMAN_TITLE"] = "[".$msg["132"]."] ".$msg['ontology_skos_menu'];
			$_SESSION["session_history"][$_SESSION["CURRENT"]]['AUT']['TEXT_LIST_QUERY']='';
			$_SESSION["session_history"][$_SESSION["CURRENT"]]['AUT']["TEXT_QUERY"] = "";
		}
	}

	public function get_human_query() {
		global $msg, $charset;

		$human_query = '';
		$human_queries = $this->_get_human_queries();
		if (count($human_queries)) {
			foreach ($human_queries as $element) {
				if ($human_query) {
					$human_query.= ', ';
				}
				$human_query.= '<b>'.$element['name'].'</b> '.htmlentities($element['value'], ENT_QUOTES, $charset);
			}
		}
		if($this->nb_results) {
			$human_query.= " => ".sprintf($msg["searcher_results"], $this->nb_results);
		} else {
			$human_query.= " => ".sprintf($msg['1915'], $this->nb_results);
		}
		return "<div class='othersearchinfo'>".$human_query."</div>";
	}

	protected function _get_human_queries() {
		global $msg;

		$human_queries = array();
		if ($this->params->user_input) {
			$human_queries[] = array(
					'name' => $msg['global_search'],
					'value' => $this->params->user_input
			);
		}
		if ($this->params->authority_statut) {
			$authority_statut_label = pmb_mysql_result(pmb_mysql_query('select authorities_statut_label from authorities_statuts where id_authorities_statut = '.$this->params->authority_statut), 0, 0);
			$human_queries[] = array(
					'name' => $msg['authorities_statut_label'],
					'value' => $authority_statut_label
			);
		}
		if (!empty($this->params->concept_scheme) && ($this->params->concept_scheme[0] != -1)) {
			$scheme_label = $msg['skos_view_concept_no_scheme'];
			if (count($this->params->concept_scheme) > 0) {
			    $scheme_label = "";
			    $scheme_labels = [];
			    for($i=0 ; $i<count($this->params->concept_scheme) ; $i++){
			        $query = 'select ?label where {
						<'.onto_common_uri::get_uri($this->params->concept_scheme[$i]).'> <'.$this->handler->get_display_label(self::$concept_scheme_uri).'> ?label
						}';
			        $this->handler->data_query($query);
			        $results = $this->handler->data_result();
			        if($results){
                        $scheme_labels[] = $results[0]->label;
			        }
			    }
			    $scheme_label = implode(', ',$scheme_labels);
			}
			$human_queries[] = array(
					'name' => $msg['search_extended_skos_concepts_scheme'],
					'value' => $scheme_label
			);
		}
		return $human_queries;
	}

	protected function proceed_search($no_print=false){
	    $result = parent::proceed_search(true);
		$this->set_session_history($this->get_human_query(), 'classic');
		$result = str_replace("!!caddie_link!!", entities_authorities_controller::get_caddie_link(), $result);
		if(!$no_print) echo($result);
	}

	protected function proceed_merge() {
	    global $msg, $id, $pmb_synchro_rdf;
	    global $pmb_ark_activate;

	    $by = $this->params->by;
	    if (!$by) {
	        print $this->item->get_merge_form("./".$this->get_base_resource()."categ=".$this->params->categ."&sub=".$this->params->sub."&id=".$this->params->id."&concept_scheme=".implode(",",$this->params->concept_scheme));
	        return;
	    }

	    if (!is_numeric($by)) {
	        $by = onto_common_uri::get_id($by);
	    }
	    $uri_target = onto_common_uri::get_uri($by);
	    $uri_source = onto_common_uri::get_uri($id);
	    $infos_source = $this->get_concept_data($uri_source);
	    $triples = "";
	    foreach ($infos_source as $property => $values) {
	        if ($property == "http://www.w3.org/2004/02/skos/core#prefLabel") {
	            foreach ($values as $value) {
	                $triples .= "<$uri_target> <http://www.w3.org/2004/02/skos/core#altLabel> '" . addslashes($value['object']) . "' .";
	            }
	        } else {
	            foreach ($values as $value) {
	                $triples .= "<$uri_target> <$property> ";
	                if ($value['type'] == "uri") {
	                    $triples .= "<{$value['object']}> .";
	                } else {
	                    $triples .= "'" . addslashes($value['object']) . "' .";
	                }
	            }
	        }
	    }
	    $query = "insert into <pmb> {".$triples."}";
	    $this->handler->data_query($query);
	    if (($this->item->get_id() == $by) ||(!$this->item->get_id())) {
	        return $msg['223'];
	    }

	    $map = new map_edition_controler(AUT_TABLE_CONCEPT, $this->item->get_id());
	    $map->replace($by);

	    $aut_link = new aut_link(AUT_TABLE_CONCEPT, $this->item->get_id());
        // liens entre autorites
        $aut_link->add_link_to(AUT_TABLE_CONCEPT, $by);
	    $aut_link->delete();

	    vedette_composee::replace(TYPE_CONCEPT, $this->item->get_id(), $by);

	    // nettoyage d'autorities_sources
	    $query = "select id_authority_source, authority_favorite from authorities_sources where num_authority = " .$this->item->get_id() ." and authority_type = 'concept'";
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result)) {
	        while ( $row = pmb_mysql_fetch_object($result) ) {
	            if ($row->authority_favorite ==1) {
	                // on suprime les references si l'autorite a ete importee...
	                $query = "delete from notices_authorities_sources where num_authority_source = " .$row->id_authority_source;
	                pmb_mysql_query($query);
	                $query = "delete from authorities_sources where id_authority_source = " .$row->id_authority_source;
	                pmb_mysql_query($query);
	            } else {
	                // on fait suivre le reste
	                $query = "update authorities_sources set num_authority = " .$by ." where num_authority_source = " .$row->id_authority_source;
	                pmb_mysql_query($query);
	            }
	        }
	    }

	    //Remplacement dans les champs persos selecteur d'autorite
	    aut_pperso::replace_pperso(AUT_TABLE_CONCEPT, $this->item->get_id(), $by);

	    if ($pmb_ark_activate) {
	        $idReplaced = authority::get_authority_id_from_entity($this->item->get_id(), AUT_TABLE_CONCEPT);
	        $idReplacing = authority::get_authority_id_from_entity($by, AUT_TABLE_CONCEPT);
	        if ($idReplaced && $idReplacing) {
	            $arkEntityReplaced = ArkEntityPmb::getEntityClassFromType(TYPE_AUTHORITY, $idReplaced);
	            $arkEntityReplacing = ArkEntityPmb::getEntityClassFromType(TYPE_AUTHORITY, $idReplacing);
	            $arkEntityReplaced->markAsReplaced($arkEntityReplacing);
	        }
	    }

	    //Remplacement de l'identifiant du concept source dans la table index concept
	    $query = "update index_concept set num_concept=".$by." where num_concept=".$this->item->get_id();
	    pmb_mysql_query($query);

	    $this->proceed_delete(true);
	    // Remplacement des triplets rdf
	    $query = "select ?s ?p where {
				?s ?p <".$this->item->get_uri()."> .
			}";

	    $this->handler->data_query($query);

	    if($this->handler->data_num_rows()){
	        $assertions = array();
	        $results = $this->handler->data_result();
	        foreach($results as $result){
	            $assertions[] = $result;
	        }

	        $query_insert = 'insert into <pmb> { ';
	        foreach($assertions as $assert){
	            $query_insert.= '<'.$assert['s'].'> <'.$assert['p'].'> <'.onto_common_uri::get_uri($by).'> .';
	        }
	        $query_insert.= '}';
	        $this->handler->data_query($query_insert);
	    }

	    $onto_index = onto_index::get_instance($this->get_onto_name());
	    $onto_index->set_handler($this->handler);
	    $onto_index->maj($by);
	    /**
	     * Reindex des elements a la suite du remplacement des id du concept source
	     */
	    index_concept::update_linked_elements($by);
	    indexation_stack::push($this->item->get_id(), TYPE_CONCEPT);

	    // mise a jour de l'oeuvre rdf
	    if ($pmb_synchro_rdf) {
	        $synchro_rdf = new synchro_rdf();
	        $synchro_rdf->replaceAuthority($this->item->get_id(), $by, 'auteur');
	    }
	    print "<script>document.location='./".$this->get_base_resource()."categ=see&sub=".$this->params->sub."&id=$by&parent_id=".$this->params->parent_id."&concept_scheme=".implode(",",$this->params->concept_scheme)."';</script>";
	}

	/**
	 * renvoie les informations d'un noeud
	 *
	 * @param string $uri
	 * @return array
	 */
	public function get_concept_data($uri) {
	    $query = "select * where {
					<$uri> ?p ?o
				}";

	    $this->handler->data_query($query);
	    $results = $this->handler->data_result();
	    if (is_array($results) && !empty($results)) {
	        $return = array();
	        foreach ($results as $result) {
	            if (empty($return[$result->p])) {
	                $return[$result->p] = [];
	            }
	            $return[$result->p][] = array(
	                'object' => $result->o,
	                'lang' => (!empty($result->o_lang) ? $result->o_lang : ''),
	                'type' => $result->o_type
	            );
	        }
	        return $return;
	    }
	    return array();
	}

	protected function delete_prefLabel($id) {
	    $query = "DELETE FROM skos_fields_global_index WHERE code_champ = 1 AND code_ss_champ = 1 AND id_item = ". $id;
       pmb_mysql_query($query);
	}
}