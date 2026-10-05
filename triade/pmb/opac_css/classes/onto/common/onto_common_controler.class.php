<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_controler.class.php,v 1.13.6.2 2024/06/04 09:00:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * 
 *
 */
class onto_common_controler {
	
	/**
	 * @var onto_handler handler
	 */
	protected $handler;
	
	/**
	 * @var onto_common_item item
	 */
	protected $item;
	
	/** variables d'aiguillage **/
	protected $params;
	
	protected $nb_results;
	
	public function __construct($handler,$params){
		$this->handler=$handler;
		$this->params=$params;
	}
	
	/**
	 * Aiguilleur principal
	 */
	public function proceed(){
		//on affecte la proprité item par une instance si nécessaire...
		$this->init_item();
		switch($this->params->action){
			case "ajax_selector" :
				return $this->proceed_ajax_selector();
				break;
			case "list_selector":
				$this->proceed_list_selector();
				break;
			case "edit" :
				$this->proceed_edit();
				break;
			case "save" :
				print $this->get_menu();
				$this->proceed_save(false);
				break;
			case "search" :
				print $this->get_menu();
				//si on peut on s'évite le processus de recherche... il est moins fluide !
				if($this->params->user_input == "*" ){
					$this->proceed_list();
				}else{
					$this->proceed_search();
				}
				break;
			case "delete" :
				print $this->get_menu();
				$this->proceed_delete(true);
				break;
			case "confirm_delete" :
				$this->proceed_delete(false);
				break;
			case "delete_from_cart" :
				//voir plus tard si on veut forcer la suppression
				return $this->proceed_delete_from_cart(false);
				break;
			case "add": //Cas ajouté pour être en conformité avec le cas des selecteurs autorité (voir ./selectors/classes/selector_ontology.class.php)
				return $this->proceed_selector_add();
				break;
			case "update": //Cas ajouté pour être en conformité avec le cas des selecteurs autorité (voir ./selectors/classes/selector_ontology.class.php)
				return $this->proceed_save(false);
				break;
			// AR - 08/11/2022 : Page de consultation d'une entité
			case "see" :
			    return $this->proceed_see();
			    break;
			case "list" :
			default :
				print $this->get_menu();
				$this->proceed_list();
				break;
		}
	}
	
	protected function init_item(){
		//dans le framework
	    if(!$this->item && $this->params->sub && ((isset($this->params->id) && $this->params->id) || in_array($this->params->action, array('edit', 'save', 'push', 'save_push', 'save_draft')))) {
	        if(in_array($this->params->action, array('save', 'save_push', 'save_draft'))){
				//lors d'une sauvegarde d'un item, on a posté l'uri
				$this->item = $this->handler->get_item($this->handler->get_class_uri($this->params->sub), $this->params->item_uri);
			}else{
				$this->item = $this->handler->get_item($this->handler->get_class_uri($this->params->sub), onto_common_uri::get_uri($this->params->id));
			}
		}
		onto_common_item::set_handler($this->handler);
	}
	
	protected function proceed_edit(){
		print $this->item->get_form("./".$this->get_base_resource()."categ=".$this->params->categ."&sub=".$this->params->sub."&id=".$this->params->id);
	}

	protected function proceed_save($list = true){
		$this->item->get_values_from_form();

		$result = $this->handler->save($this->item);
		if($result !== true){
			$ui_class_name=self::resolve_ui_class_name($this->params->sub,$this->handler->get_onto_name());
			$ui_class_name::display_errors($this,$result);
		}else {
			vedette_composee::update_vedettes_built_with_element($this->item->get_id(), "ontology".$this->params->ontology_id);
			if ($list) $this->proceed_list();
		}
	}

	protected function proceed_delete($force_delete = false){
		$result = $this->handler->delete($this->item,$force_delete);
		if ($force_delete || !count($result)) {
			$this->proceed_list();
		} else {
			$this->proceed_confirm_delete($result);
		}
	}

	protected function proceed_list(){
		$ui_class_name=self::resolve_ui_class_name($this->params->sub,$this->handler->get_onto_name());
		print $ui_class_name::get_search_form($this,$this->params);
		print $ui_class_name::get_list($this,$this->params);
	}

	protected function proceed_list_selector(){
		$type = $this->get_item_type_to_list($this->params,true);
		$ui_class_name=self::resolve_ui_class_name($type,$this->handler->get_onto_name());
		print $ui_class_name::get_search_form_selector($this,$this->params);
		print $ui_class_name::get_list_selector($this,$this->params);
	}

	protected function proceed_ajax_selector(){
		//on regarde le range (multiple  ou pas..)
		$ranges = explode("|||",$this->params->att_id_filter);
		$list = array();
		foreach ($ranges as $range){
			$elements = $this->get_ajax_searched_elements($range);
			foreach($elements['elements'] as $key => $value){
			    $newKey = $key;
			    if($this->params->return_concept_id){
			        $newKey = onto_common_uri::get_id($key);
			    }
			    $list['elements'][$newKey] = $value;
				if(count($ranges)>1){
					$list['prefix'][$key]['libelle'] = $elements['label'];
					$list['prefix'][$key]['id'] = $range;
				}
			}
		}
		return $list;
	}
	
	protected function proceed_search(){
		$ui_class_name=self::resolve_ui_class_name($this->params->sub,$this->handler->get_onto_name());
		print $ui_class_name::get_search_form($this,$this->params);
		print $ui_class_name::get_list($this,$this->params);		
	}
	
	protected function proceed_selector_add(){
		//on en aura besoin à la sauvegarde...
		$_SESSION['onto_skos_concept_selector_last_parent_id'] = $this->params->parent_id;
		//réglons rapidement ce problème... cf. dette technique
		print "<div id='att'></div>";
		$type = $this->get_item_type_to_list($this->params,true);
		print $this->item->get_form($this->params->base_url, '', 'update');
	}
	
	protected function proceed_confirm_delete($result){
		$ui_class_name=self::resolve_ui_class_name($this->params->sub,$this->handler->get_onto_name());
		print $ui_class_name::get_list_assertions($this, $this->params, $result);
	}
	
	/**
	 * Retourne le menu en fonction des classes de l'ontologie
	 *
	 * @return string menu
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
		$menu.= "
		</div>";
		return $menu;
	}
	
	public function get_base_resource($with_params=true){
		$end = "?";
		if(strpos($this->params->base_resource,"?")){
			$end = "&";
		}
		return $this->params->base_resource.($with_params? $end : "");
	}

	/**
	 * Retourn le titre en fonction des classes de l'ontologie
	 *
	 * @return string title
	 */
	public function get_title(){
		global $msg;
		if(isset($msg['onto_'.$this->handler->get_onto_name()])){
			$title = $msg['onto_'.$this->handler->get_onto_name()];
		}else {
			$title = $this->handler->get_title();
		}
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

	/**
	 *
	 * Retourne une liste sans hierarchie
	 *
	 * @var string class_uri
	 * @var onto_param params
	 */
	public function get_list($class_uri,$params){
		global $lang;
	
		$page = $params->page-1;
		$displayLabel = $this->handler->get_display_label($class_uri);
		$this->nb_results = $this->handler->get_nb_elements($class_uri);
		$query = "select * where {
			?elem rdf:type <".$class_uri."> .
			?elem <".$displayLabel."> ?label
		} order by ?label";
		if($params->nb_per_page>0){
			$query.= " limit ".$params->nb_per_page;
		}
		if($page>0){
			$query.= " offset ".($page*$params->nb_per_page);
		}
		
		$this->handler->data_query($query);
		$results = $this->handler->data_result();
		$list = array(
				'nb_total_elements' => 	$this->nb_results,
				'nb_onto_element_per_page' => $params->nb_per_page,
				'page' => $page
		);
		$list['elements'] = array();
		if($results && count($results)){
			foreach($results as $result){
				if(!isset($list['elements'][$result->elem]['default']) || !$list['elements'][$result->elem]['default']){
					$list['elements'][$result->elem]['default'] = $result->label;
				}
				if(isset($result->label_lang) && substr($lang,0,2) == $result->label_lang){
					$list['elements'][$result->elem][$result->label_lang] = $result->label;
				}
			}
		}
		return $list;
	}

	/**
	 * Renvoie un libellé en fonction du nom ou de l'uri
	 *
	 * @param string $name
	 */
	public function get_label($name){
		return $this->handler->get_label($name);
	}

	/**
	 * renvoie le nom de l'ontologie
	 *
	 * @return string
	 */
	public function get_onto_name(){
		return $this->handler->get_onto_name();
	}

	/**
	 * Retourne le nom de la classe ontologie en fonction de son uri
	 * 
	 * @param string $uri_class
	 */
	public function get_class_label($uri_class){
		return $this->handler->get_class_label($uri_class);
	}

	/**
	 * Renvoie l'uri d'une classe en fonction de son nom pmb
	 *
	 * @param string $class_name
	 */
	public function get_class_uri($class_name){
		return $this->handler->get_class_uri($class_name);
	}

	/**
	 * Renvoie le nom PMB d'une classe en fonction de son uri
	 * 
	 * @param string $class_uri
	 */
	public function get_class_pmb_name($class_uri){
		return $this->handler->get_class_pmb_name($class_uri);
	}
	
	/**
	 * retourne les uri des classes de l'ontologie
	 *
	 * @return array
	 */
	public function get_classes(){
		return $this->handler->get_classes();
	}

	/**
	 * Retourne le label d'un data en fonction de son uri.
	 * 
	 * @param string $uri
	 */
	public function get_data_label($uri){
		return $this->handler->get_data_label($uri);
	}

	/**
	 *
	 * Renvoi le nom de la class ui à utiliser pour la classe
	 *
	 * @return string
	 */
	public static function resolve_ui_class_name($class_name,$ontology_name){
		return self::search_ui_class_name($class_name,$ontology_name);
	}

	/**
	 * Renvoie les propriétés en fonction d'un nom de classe pmb
	 *
	 * @param string $pmb_name
	 *
	 * @return array
	 */
	public function get_onto_property_from_pmb_name($pmb_name) {
		return $this->handler->get_onto_property_from_pmb_name($pmb_name);
	}
	
	public static function search_ui_class_exists($class_name,$ontology_name = ''){
	    global $class_path;
	    
	    if (file_exists($class_path."/onto/".$ontology_name."/".$class_name.".class.php") && class_exists($class_name)) {
	        return true;
	    }
	    return false;
	}
	
	/**
	 *
	 * Recherche et renvoi le nom de classe ui le plus approprié pour la classe dont on passe le nom
	 *
	 * @param string $class_name
	 * @param string $ontology_name
	 * @return string 
	 */
	public static function search_ui_class_name($class_name,$ontology_name=''){
		$suffixe = "_ui";
		$prefix = "onto_";
		
		if(static::search_ui_class_exists($prefix.$ontology_name.'_'.$class_name.$suffixe, $ontology_name)){
			//La classe ui a le même nom que la classe
			//ex : onto_skos_concept<=>onto_skos_concept_ui
			return $prefix.$ontology_name.'_'.$class_name.$suffixe;
		}else{
			
			//On ne trouve pas l'ui exact, on remonte dans le common pour prendre l'ui qui correspond au type de classe
			//ex : onto_skos_concept<=>onto_common_concept_ui
			
		    if(static::search_ui_class_exists($prefix.'common_'.$class_name.$suffixe, 'common')){
				return $prefix.'common_'.$class_name.$suffixe;
			}else{
			    if (static::search_ui_class_exists('onto_common'.$suffixe, 'common')) {
					//Pas d'ui correspondant dans le common au nom de la classe... on renvoie onto_common_ui
					return 'onto_common'.$suffixe;
				} else {
					return 'onto_common_ui';
				}
			}
		}
		return false;
	}
	
	public function get_searched_elements($class_uri,$params){
		$search_class_name = $this->get_searcher_class_name($class_uri);
		if($params->deb_rech && $search_class_name){
			$searcher = new $search_class_name($params->deb_rech);
			if($searcher->get_nb_results()){
				$results = $searcher->get_sorted_result("default",(($params->page-1)*$params->nb_per_page),$params->nb_per_page);
			}else{
				$results = array();
			}
			$elements = array(
					'nb_total_elements' => 	$searcher->get_nb_results(),
					'nb_onto_element_per_page' => $params->nb_per_page,
					'page' => $params->page-1
			);
			$elements['elements'] = array();
			foreach($results as $item){
				$elements['elements'][onto_common_uri::get_uri($item)]['default'] = $this->get_data_label(onto_common_uri::get_uri($item));
					
			}
		}else {
			//PAS DE CLASSE DE RECHERCHE, on affiche juste la liste
			$elements = $this->get_list($class_uri,$params);
		}
		return $elements;
	}
	
	
	public function get_searched_list($class_uri, $params, $user_query_var="user_input"){
		global $dbh;

		if(!$params->{$user_query_var}){
			return $this->get_list($class_uri, $params);
		}else{
			$search_class_name = $this->get_searcher_class_name($class_uri);
			$searcher = new $search_class_name($params->{$user_query_var});
			if($searcher->get_nb_results()){
				$results = $searcher->get_sorted_result("default",(($params->page-1)*$params->nb_per_page),$params->nb_per_page);
			}else{
				$results = array();
			}
			$list = array(
				'nb_total_elements' => 	$searcher->get_nb_results(),
				'nb_onto_element_per_page' => $params->nb_per_page,
				'page' => $params->page-1
			);
			$list['elements'] = array();
			if(is_array($results))
			foreach($results as $item){
				$list['elements'][onto_common_uri::get_uri($item)]['default'] = $this->get_data_label(onto_common_uri::get_uri($item));
			}
		}
		return $list;
	}

	public function searcher_class_exists($search_class_name){
	    global $class_path;
	    
	    if (file_exists($class_path."/searcher/".$search_class_name.".class.php") && class_exists($search_class_name)) {
	        return true;
	    }
	    return false;
	}
	
	public function get_searcher_class_name($class_uri){
		$classes= $this->handler->get_classes();
		$search_class_name = "searcher_autorities_".$this->handler->get_onto_name()."_".$classes[$class_uri]->pmb_name;
		if($this->searcher_class_exists($search_class_name)){
		    return $search_class_name;
		}
		$search_class_name.= "s";
		if($this->searcher_class_exists($search_class_name)){
		    return $search_class_name;
		}
		$search_class_name = 'searcher_ontologies_'.$classes[$class_uri]->pmb_name;
		if(!$this->searcher_class_exists($search_class_name)){
		    $search_class_name.= 's';
		    if ($this->searcher_class_exists($search_class_name)) {
		        return $search_class_name;
		    }
		}
		return false;
	}
	
	/**
	 *
	 * Retourne une liste des éléments utilisable pour l'autocomplétion (retourne une liste vide si pas de recherche implémentée pour le type d'item
	 *
	 * @return array $elements
	 */
	public function get_ajax_searched_elements($class_uri){
	    $search_class_name = $this->get_searcher_class_name($class_uri);
		$elements = array(
			'label' => "[".$this->get_label($class_uri)."]",
			'elements' => array()
		);
		if($this->params->datas && $search_class_name){
		    $searcher = new $search_class_name(($this->params->datas == "*" ? '*' : $this->params->datas.'*'));
			if($searcher->get_nb_results()){
				$results = $searcher->get_sorted_result("default",0,20);
			}else{
				$results = array();
			}
			foreach($results as $id){
				$elements['elements'][onto_common_uri::get_uri($id)] = $this->get_data_label(onto_common_uri::get_uri($id));
			}
		}
		return $elements;
	}
	
	/**
	 *
	 * Retourne une liste des éléments à lister  
	 *
	 * @return array $elements
	 */
	public function get_list_elements($params){
		$class_uri = $this->get_item_type_to_list($params);
		switch($params->action){
			case "search" :
				if($params->user_input == "*"){
					return $this->get_list($class_uri, $params);
				}
				if($this->get_searcher_class_name($class_uri) != false){
					return $this->get_searched_list($class_uri, $params);
				}
				break;
			case "list_selector" :
				if($this->get_searcher_class_name($class_uri)!= false){
					return $this->get_searched_list($class_uri, $params,"deb_rech");
				}
				break;
		}
		return $this->get_list($class_uri, $params);
	}
	
	protected function get_item_type_to_list($params, $pmb_name=false){
		//on commence par récupérer l'URI de la classe de l'ontologie des éléments que l'on veut lister...
		switch($params->action){
			case "list_selector":
			case "selector_add" :
			case "selector_save" :
				//dans le cas de list_selector, l'information peut provenir de différents endroits selon que l'on soit dans un sélecteur dans un formulaire du framework ou en externe
				//1er cas : pas d'objs, pas d'éléments, l'infos est dans le sub
				if (!$this->params->objs && !$params->element) {
					$class_uri = $this->get_class_uri($params->sub);
				}else{
					//2ème cas : on a objs, on est dans le framework et objs contient le nom PMB de la propriété
					if($this->params->objs != ""){
						//on récupère la propriété
						$property = $this->get_onto_property_from_pmb_name($params->objs);
						//à partir de la propriété, on a le range
						$class_uri = $property->range[$params->range];
					}else {
						//3ème et dernier cas, on prend le le pmb_name dans element
						$class_uri = $this->get_class_uri($params->element);
					}
				}
				break;
			//sinon c'est simple, c'est dans le sub
			default :
				$class_uri = $this->get_class_uri($params->sub);
				break;
		}
		if($pmb_name){
			return $this->get_class_pmb_name($class_uri);
		}
		return $class_uri;
	}
	
	public function get_ontology_display_name_from_uri($uri){
		global $opac_url_base;
		$display_name = "";
		if(strpos($uri, "skos") !== false) {
			$display_name = "http://www.w3.org/2004/02/skos/core#prefLabel";
		}
		if(strpos($uri, $opac_url_base."ontologies/") !== false) {
			$ontology_id = substr(str_replace($opac_url_base."ontologies/", "", $uri), 0, strpos(str_replace($opac_url_base."ontologies/", "", $uri),"#"));
			$ontology = new ontology($ontology_id);
			$display_name = $ontology->get_display_label_property($uri);
		}
		return $display_name;
	}
	
	public function get_skos_datastore(){
		$data_store_config = array(
				/* db */
				'db_name' => DATA_BASE,
				'db_user' => USER_NAME,
				'db_pwd' => USER_PASS,
				'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'rdfstore',
				/* stop after 100 errors */
				'max_errors' => 100,
				'store_strip_mb_comp_str' => 0
		);
		return new onto_store_arc2($data_store_config);
	}
	
	public function get_skos_controler(){
		global $deflt_concept_scheme;
		$params = new onto_param(array(
				'categ'=>'concepts',
				'sub'=> 'concept',
				'action'=>'list',
				'page'=>'1',
				'nb_per_page'=>'20',
				'id'=>'',
				'parent_id'=>'',
				'user_input'=>'',
				'concept_scheme' => ((isset($_SESSION['onto_skos_concept_last_concept_scheme']) && ($_SESSION['onto_skos_concept_last_concept_scheme'] !== "")) ? $_SESSION['onto_skos_concept_last_concept_scheme'] : $deflt_concept_scheme),
				'item_uri' => "",
				'only_top_concepts' => '0',
				'base_resource'=> "autorites.php"
		));
		return new onto_skos_controler($this->get_skos_handler(), $params);
	}
	
	public function get_skos_handler(){
		global $class_path;
	
		$onto_store_config = array(
				/* db */
				'db_name' => DATA_BASE,
				'db_user' => USER_NAME,
				'db_pwd' => USER_PASS,
				'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'ontology',
				/* stop after 100 errors */
				'max_errors' => 100,
				'store_strip_mb_comp_str' => 0
		);
		$data_store_config = array(
				/* db */
				'db_name' => DATA_BASE,
				'db_user' => USER_NAME,
				'db_pwd' => USER_PASS,
				'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'rdfstore',
				/* stop after 100 errors */
				'max_errors' => 100,
				'store_strip_mb_comp_str' => 0
		);
		$handler = new onto_handler($class_path."/rdf/skos_pmb.rdf", "arc2", $onto_store_config, "arc2", $data_store_config, $this->get_skos_namespaces(), 'http://www.w3.org/2004/02/skos/core#prefLabel');
		$handler->get_ontology();
		return $handler;
	}
	
	public function get_skos_namespaces(){
		return array(
				"skos"	=> "http://www.w3.org/2004/02/skos/core#",
				"dc"	=> "http://purl.org/dc/elements/1.1",
				"dct"	=> "http://purl.org/dc/terms/",
				"owl"	=> "http://www.w3.org/2002/07/owl#",
				"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
				"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
				"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
				"pmb"	=> "http://www.pmbservices.fr/ontology#"
		);
	}
	
	protected function proceed_see()
	{
	    $instance = onto_common_page::get_instance($this->item,$this->handler,$this->params);
	    $instance->render();
	}
}