<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_handler.class.php,v 1.86.2.1 2024/05/27 13:05:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/onto/onto_ontology.class.php");
require_once($class_path."/onto/common/onto_common_item.class.php");
require_once($class_path."/onto/onto_store.class.php");


/**
 * class onto_handler
 *
 */
class onto_handler {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 *
	 * @access protected
	 */
	protected $ontology;

	/**
	 * Store pour l'ontologie
	 *
	 * @var onto_store
	 *
	 * @access protected
	 */
	protected $onto_store;

	/**
	 * Store pour les donnnées
	 *
	 * @var onto_store
	 *
	 * @access private
	 */
	protected $data_store;

	protected $default_display_label;

	private $nb_elements =array();

	private static $display_labels = array();

	private static $item_instances = array();

	private static $pmb_names = [
	    'http://www.w3.org/1999/02/22-rdf-syntax-ns#type' => 'type',
	];

	/**
	 *
	 * @var onto_common_index
	 */
	protected $onto_index;

	public $labels;

	/**
	 *
	 *
	 * @param string ontology_filepath
	 * @param string onto_store nom ou instance de la classe store à utiliser pour l'ontologie
	 * @param array() onto_store_config Configuration du store pour l'ontologie
	 * @param string data_store_type Nom de la classe à utiliser pour le store data
	 * @param Array() data_store_config Configuration du store data
	 *
	 * @return void
	 *
	 * @access public
	 */
	public function __construct( $ontology_filepath,  $onto_store,  $onto_store_config,  $data_store,  $data_store_config,$tab_namespaces ,$default_display_label) {

		if (is_object($onto_store)) {
			$this->onto_store = $onto_store;
		} else {
			//on récupère les stores...
			$onto_store_class = "onto_store_".$onto_store;
			$this->onto_store = new $onto_store_class($onto_store_config);
			$this->onto_store->set_namespaces($tab_namespaces);
			//chargement de l'ontologie dans son store
			if($ontology_filepath){
				$this->onto_store->load($ontology_filepath);
			}
		}
		if (is_object($data_store)) {
			$this->data_store = $data_store;
		} else {
			$data_store_class = "onto_store_".$data_store;
			$this->data_store = new $data_store_class($data_store_config);
			$this->data_store->set_namespaces($tab_namespaces);
		}

		$this->default_display_label=$default_display_label;
	} // end of member function __construct

	/**
	 * PARTIE DATASTORE
	 */

	/**
	 * revoie les assertion à inserer pour un item
	 *
	 * @param string $uri
	 *
	 * @return array
	 */
	public function get_assertions($uri){
		$assertions = array();
		$query = "select * where {
			<".$uri."> ?predicate ?object .
			optional {
				?object rdf:type ?type
			} .
			optional {
				?object pmb:has_assertions ?has_assertions.
			}
		}";
		$this->data_store->query($query);
		$results = $this->data_store->get_result();
		foreach($results as $assertion){
			$object_properties = array();
			foreach($assertion as $key=>$value){
				if(substr($key,0,strlen("object_")) == "object_"){
					$object_properties[substr($key,strlen("object_"))] = $value;
				}
			}
			if($object_properties['type'] == "literal"){
				$type = "http://www.w3.org/2000/01/rdf-schema#Literal";
			}else{
				$type = (!empty($assertion->type) ? $assertion->type : "");
				if(!$type){
					$tmp = $this->ontology->get_property_pmb_datatype($assertion->predicate);
					if($tmp == 'http://www.pmbservices.fr/ontology#resource_pmb_selector'){
						$type = substr($assertion->object,0,strrpos($assertion->object,'_'));
					}else if($tmp == 'http://www.pmbservices.fr/ontology#marclist'){
						$type = $this->ontology->get_property_pmb_datatype($assertion->predicate);
					}else{
						$type = $assertion->predicate;
					}
				}else{
					/**
					 * TODO: Récupérer le display label dans le cas dun resource pmb selector +
					 * Affichage correct au niveau du datatype UI (Autre chose que l'uri de la propriété)
					 */
					$displayLabel = $this->get_display_label($assertion->type);
					$query="select ?display_label where {
						<".$assertion->object."> <".$displayLabel."> ?display_label
					}";
					$this->data_store->query($query);
					if($this->data_store->num_rows()){
						$result = $this->data_store->get_result();
						$object_properties['display_label'] = $result[0]->display_label;
					} else {//cas particulier pour les assertions qui auraient directement un displayLabel
						$query="select ?display_label where {
									<".$assertion->object."> pmb:displayLabel ?display_label
								}";
						$this->data_store->query($query);
						if($this->data_store->num_rows()){
							$result = $this->data_store->get_result();
							$object_properties['display_label'] = $result[0]->display_label;
						}
					}
				}
				if (!empty($assertion->has_assertions)) {
				    $object_properties["assertions"] = $this->get_assertions($assertion->object);
				}
			}
			$assertions[] = new onto_assertion($uri, $assertion->predicate, $assertion->object, $type,$object_properties);
		}
		return $assertions;
	}

	/**
	 * Fonction d'accès aux requetes sparql dans le data store
	 *
	 * @param string $query
	 *
	 */
	public function data_query($query){
		$this->data_store->query($query);
		$errs = $this->data_store->get_errors();
		if (!empty($errs)) {
		    if ($this->get_onto_name() == "contribution") {
		        global $msg;

		        // Erreur lors de l'enregistrement
		        $report = array();
		        $report["error"] = [
		            "type" => "sparql",
		            "message" => $msg['contribution_error_save'],
		            "errors" => $errs
		        ];
		        return $report;
		    } else {
		        self::print_errors($errs, $query);
		    }
		}
		return true;
	}

	/**
	 * Fonction d'accès aux requetes sparql dans le data store
	 * renvoi le résultat
	 *
	 * @return array result
	 */
	public function data_result(){
		if($this->data_store->num_rows()){
			return $this->data_store->get_result();
		}elseif ($errs = $this->data_store->get_errors()) {
		    self::print_errors($errs);
		}
		return false;
	}

	/**
	 * Fonction d'accès aux requetes sparql dans le data store
	 * renvoi le nombre de résultat
	 *
	 * @return integer num rows
	 */
	public function data_num_rows(){
		if($this->data_store->num_rows()){
			return $this->data_store->num_rows();
		}elseif ($errs = $this->data_store->get_errors()) {
		    self::print_errors($errs);
		}
		return false;
	}

	public function get_data_label($uri){
		global $lang;

		$displayLabel=$this->get_display_label($uri);

		$query = "select * where {
			<".$uri.">  <".$displayLabel."> ?label
		}";
		$this->data_store->query($query);

		if($this->data_store->num_rows()){
			$results = $this->data_store->get_result();
			foreach($results as $result){
				if(isset($result->label_lang) && $result->label_lang==substr($lang,0,2)){
					return $result->label;
				}
			}
			//pas de langue de l'interface trouvée
			foreach($results as $result){
				return $result->label;
			}
		}

	}


	public function get_nb_elements($class_uri,$more=""){

		if(!isset($this->nb_elements[$class_uri.$more])){
			$query="";
			$query.="select distinct ?elem where {
				?elem rdf:type <".$class_uri."> .";
			$displayLabel=$this->get_display_label($class_uri);
			if ($displayLabel) {
			    $query .= "?elem <".$displayLabel."> ?label ";
			}
			if($more){
				if(substr(trim($more),0,1) == '.'){
					$query.=$more;
				}else{
					$query.=' . '.$more;
				}

			}
			$query.="}";
			$this->data_store->query($query);
			$this->nb_elements[$class_uri.$more] = 0;
			if ($this->data_store->num_rows()) {
				$this->nb_elements[$class_uri.$more] = $this->data_store->num_rows();
			}
		}
		return $this->nb_elements[$class_uri.$more];
	}


	/**
	 * Supprime et recrée les déclarations de l'instance passée en paramètre
	 * @access public
	 *
	 * @param onto_common_item $item Instance à sauvegarder
	 * @param array $kept_properties tableau des proprietes a conserver dans le store
	 * @return bool
	 */
	public function save( $item , $kept_properties = array(), $force = false) {
		global $opac_url_base, $area_id, $action;

		if ($force || $item->check_values()) {
			if(onto_common_uri::is_temp_uri($item->get_uri())){
				$item->replace_temp_uri();
			}
			$assertions = $item->get_assertions();
			$nb_assertions = count($assertions);
			$i = 0;
			// On commence par supprimer ce qui existe
			$query = "delete {
				<".$item->get_uri()."> ?prop ?obj .";
			if (count($kept_properties)) {
    			$filter = "";
    		    foreach ($kept_properties as $kept_property) {
    		        if ($filter) {
    		            $filter .= " && ";
    		        }
    		        $filter .= " ?prop != ".$kept_property." ";
    		    }

    		    $query .= "} WHERE {
    		        <".$item->get_uri()."> ?prop ?obj .
    		        FILTER( ".$filter." )";
			}

			$query .= "}";
			$this->data_store->query($query);

			if ($errs = $this->data_store->get_errors()) {
			    self::print_errors($errs);
			}

			$query = "delete {
				?suj ?prop <".$item->get_uri()."> .";

			$inverse_properties = $this->get_inverse_of_properties($item);
		    $filter = "";
		    foreach ($inverse_properties as $property => $inverse_property) {
		        if (!in_array($property, $kept_properties)) {
    		        if ($filter) {
    		            $filter .= " || ";
    		        }
    		        $filter .= " ?prop = <".$inverse_property."> ";
		        }
		    }
		    if ($filter) {
    		    $query .= "} WHERE {
    		         ?suj ?prop <".$item->get_uri()."> .
    		        FILTER( ".$filter." )";

    		    $query .= "}";
    		    $this->data_store->query($query);

    		    if ($errs = $this->data_store->get_errors()) {
    		        self::print_errors($errs);
    		    }
		    }

			// On peut y aller
			$query = "insert into <pmb> {
				";
			foreach ($assertions as $assertion) {
				if ($assertion->offset_get_object_property("type") == "literal"){
					$object = "'".addslashes($assertion->get_object())."'";
					$object_properties = $assertion->get_object_properties();
					if(!empty($object_properties['lang'])){
						$object.="@".$object_properties['lang'];
					}
				} else {
					$object = $assertion->get_object();
					// On traite le cas où on récupère l'id
					if (is_numeric($object)) {
						$object = intval($object);
						$object = onto_common_uri::get_uri($object);
					}

					if (!empty($object)) {
    					$object = "<".addslashes($object).">";
					} else {
    					$object = "''";
					}
				}
				$query.= "<".addslashes($assertion->get_subject())."> <".addslashes($assertion->get_predicate())."> ".$object;
				$i++;
				if ($i < $nb_assertions) $query.=" .";
				$query.="\n";
			}
			$query.="}";
			$this->data_store->query($query);
			if ($errs = $this->data_store->get_errors()) {
			    self::print_errors($errs, $query);
			}
		} else {
			return $item->get_checking_errors();
		}
		$this->data_store->reset_after_save();

		return true;
	} // end of member function save

	/**
	 * Détruit une instance (l'ensemble de ses déclarations)
	 *
	 * @param onto_common_item $item Instance à supprimer (l'ensemble de ses déclarations)
	 * @param bool $force_delete Si false, renvoie un tableau des assertions où l'item est objet. Si true, supprime toutes les occurences de l'item
	 *
	 * @return bool
	 * @access public
	 */
	public function delete($item, $force_delete = false) {
		// On stockera dans un tableau tous les triplets desquels l'item est l'objet
		$is_object_of = $this->is_object_of($item);

		$query = "select uri_id from onto_uri where uri = '".$item->get_uri()."'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			aut_pperso::delete_pperso(AUT_TABLE_CONCEPT,  pmb_mysql_result($result, 0, 0) ,1) ;
		}
		if ($force_delete || !count($is_object_of)) {

		    if ($this->get_onto_name() == "contribution") {
		        $this->delete_linked_contribution_from_uri($item->get_uri());
		    }

			$query = "delete {
				<".$item->get_uri()."> ?prop ?obj
			}";
			$this->data_store->query($query);

			if ($errs = $this->data_store->get_errors()) {
			    self::print_errors($errs);
			} else {
				$query = "delete {
					?subject ?predicate <".$item->get_uri().">
				}";
				$result = $this->data_store->query($query);

				if ($errs = $this->data_store->get_errors()) {
				    self::print_errors($errs);
				}else{
					// On met à jour l'index
				    if ($this->get_onto_name() == "skos") {
				        $onto_index = onto_index::get_instance($this->get_onto_name());
    					$onto_index->set_handler($this);
    					$onto_index->maj(0,$item->get_uri());

    					if (count($is_object_of)) {
    						foreach ($is_object_of as $object) {
    						    $onto_index->maj(0,$object->get_subject());
    						}
    					}
				    }

					//on a tout viré on supprime aussi l'URI dans la table
					$query = "delete from onto_uri where uri = '".$item->get_uri()."'";
					pmb_mysql_query($query);
				}
			}
		}
		return $is_object_of;
	} // end of member function delete


	/**
	 *
	 * @param onto_common_item $item
	 * @return array
	 */
	public function is_object_of($item) {
	    // On stockera dans un tableau tous les triplets desquels l'item est l'objet
	    $is_object_of = array();
	    if (!empty($item->get_uri())) {

	        $query = "select * where {
    			?subject ?predicate <".$item->get_uri().">
    		}";
	        $this->data_store->query($query);
	        $result = $this->data_store->get_result();

	        foreach ($result as $assertion) {
	            $is_object_of[] = new onto_assertion($assertion->subject, $assertion->predicate, $item->get_uri());
	        }
	    }
	    return $is_object_of;
	}
	/**
	 * PARTIE DATASTORE
	 */


	/**
	 * Retourne l'item le plus approprié pour définir l'URI passée en paramètre
	 *
	 * @param string class_uri URI de la classe de l'ontologie à instancier
	 * @param string uri URI de l'instance à créer
	 *
	 * @return onto_common_item $item
	 *
	 * @access public
	 */
	public function get_item($class_uri,$uri) {
		$item_class = "onto_".$this->ontology->name."_".$this->get_class_pmb_name($class_uri)."_item";
		if(!class_exists($item_class)){
			$item_class = "onto_".$this->ontology->name."_item";
		}
		if(!class_exists($item_class)){
			$item_class = "onto_common_item";
		}
		$item = new $item_class($this->ontology->get_class($class_uri),$uri);
		$item->set_assertions($this->get_assertions($uri));
		if(!$uri){
			//pas d'uri, on instancie les assertions par défaut...
			$assertions = array();
			foreach($this->ontology->get_class_properties($class_uri) as $uri_property){
				$property=$this->ontology->get_property($class_uri,$uri_property);
				if(count($property->default_value)){
					global ${$property->default_value['value']};
					if(isset(${$property->default_value['value']})){
					    if(is_array(${$property->default_value['value']})){
					        for($i=0 ; $i<count(${$property->default_value['value']}) ; $i++){
					           $assertions[] = new onto_assertion($item->get_uri(),$uri_property,onto_common_uri::get_uri(${$property->default_value['value']}[$i]),$property->range[0], array('type' => "uri",'display_label' => $this->get_data_label(onto_common_uri::get_uri(${$property->default_value['value']}[$i]))));
					        }
					    }else{
                            $assertions[] = new onto_assertion($item->get_uri(),$uri_property,onto_common_uri::get_uri(${$property->default_value['value']}),$property->range[0], array('type' => "uri",'display_label' => $this->get_data_label(onto_common_uri::get_uri(${$property->default_value['value']}))));
					    }
                    }
				}
			}
			if(count($assertions)){
				$item->set_assertions($assertions);
			}
		}
		self::$item_instances[$item->get_uri()] = $item;

		return $item;
	} // end of member function get_item


	/**
	 * PARTIE ONTOLOGIE
	 */


	/**
	 * retourne les uri des classes de l'ontologie
	 *
	 * @return array
	 */
	public function get_classes(){
	    if (!isset($this->ontology)) {
            $this->get_ontology();
        }
		return $this->ontology->get_classes_uri();
	}


	/**
	 * Retourne le nom de la classe ontologie en fonction de son uri
	 *
	 * @param string $uri_class
	 */
	public function get_class_label($uri_class){
		return $this->ontology->get_class_label($uri_class);
	}

	/**
	 * Renvoie le premier nom de classe de l'ontologie (choisi par défaut)
	 *
	 * @return string
	 */
	public function get_first_ontology_class_name(){
		$classes = $this->get_classes();
		reset($classes);
		return current($classes)->pmb_name;
	}

	/**
	 * Renvoie l'uri d'une classe en fonction de son nom pmb
	 *
	 * @param string $class_name
	 */
	public function get_class_uri($class_name){
		$classes = $this->get_classes();
		$class_uri = "";
		foreach($classes as $class){
			if($class->pmb_name == $class_name){
				$class_uri = $class->uri;
				break;
			}
		}
		return $class_uri;
	}

	/**
	 * Renvoie le nom PMB d'une classe en fonction de son uri
	 *
	 * @param string $class_uri
	 */
	public function get_class_pmb_name($class_uri){
		$classes = $this->get_classes();
		$class_pmb_name = "";
		foreach($classes as $class){
			if($class->uri == $class_uri){
				$class_pmb_name = $class->pmb_name;
				break;
			}
		}
		return $class_pmb_name;
	}

	/**
	 * Renvoi le titre de l'ontologie
	 *
	 * @return string
	 */
	public function get_title(){
		if (!isset($this->ontology)) {
			$this->get_ontology();
		}
		return $this->ontology->title;
	}

	/**
	 * renvoie le nom de l'ontologie
	 *
	 * @return string
	 */
	public function get_onto_name(){
		if (!isset($this->ontology)) {
			$this->get_ontology();
		}
		return $this->ontology->name;
	}

	/**
	 * Instancie et renvoie la valeur labels
	 * Contient les libellés des mots présents dans le data_store
	 *
	 * @return array
	 */
	public function get_labels(){
		if(!isset($this->labels) || !$this->labels){
			$this->labels = array();
			$query="select * where {
				?uri pmb:name ?name .
				?uri rdfs:label ?label .
				optional {
					?uri pmb:displayLabel ?displayLabel .
					?uri pmb:searchLabel ?searchLabel
				}
			}";

			$this->onto_store->query($query);
			$results = $this->onto_store->get_result();
			if (!empty($results)) {
    			foreach($results as $result){
    				$this->labels[$result->name]['uri'] = $result->uri;

    				$this->labels[$result->name]['name'] = $result->name;

    				if(isset($result->displayLabel) && $result->displayLabel){
    					$this->labels[$result->name]['displayLabel'] = $result->displayLabel;
    				}

    				if(isset($result->searchLabel) && $result->searchLabel){
    					$this->labels[$result->name]['searchLabel'] = $result->searchLabel;
    				}

    				if(!isset($this->labels[$result->name]['label']['default'])){
    					$this->labels[$result->name]['label']['default'] = $result->label;
    				}
    				if(empty($result->label_lang)){
    				    $result->label_lang = '';
    				}
    				$this->labels[$result->name]['label'][$result->label_lang] = $result->label;
    			}
			}
		}
		return $this->labels;
	}


	public function get_display_label($class_uri,$recurse=true){
		if(isset(self::$display_labels[$class_uri])){
			return self::$display_labels[$class_uri];
		}
		$query = "select ?displayLabel where {
			<".$class_uri."> pmb:displayLabel ?displayLabel
		}";
		$this->onto_store->query($query);
		$displayLabel = $this->default_display_label;
		if($this->onto_store->num_rows()){
			$result = $this->onto_store->get_result();
			$displayLabel = $result[0]->displayLabel;
		}else{
			$query = "select ?type where {
				<".$class_uri."> rdf:type ?type .
			}";
			$this->data_store->query($query);
			if($this->data_store->num_rows()){
				$result = $this->data_store->get_result();
				if($recurse){
					$displayLabel = $this->get_display_label($result[0]->type,false);
				}
			}
		}
		self::$display_labels[$class_uri] = $displayLabel;
		return self::$display_labels[$class_uri];
	}

	/**
	 * Renvoie un libellé en fonction du nom ou de l'uri
	 *
	 * @param string $name
	 */
	public function get_label($name){
		global $msg,$lang;
		$label= "";

		//@todo recherche SPARQL sur un libelle?
		if(!isset($this->labels) || !$this->labels){
			$this->get_labels();
		}

		foreach($this->labels as $key => $infos){
			if($name == $key || $name == $infos['uri']){
				if(isset($msg['onto_'.$this->get_onto_name().'_'.$infos['name']])){
					//le message PMB spécifique pour l'ontologie courante
					$label = $msg['onto_'.$this->get_onto_name().'_'.$infos['name']];
				}else if (isset($msg['onto_common_'.$infos['name']])){
					//le message PMB générique
					$label = $msg['onto_common_'.$infos['name']];
				}else if (isset($infos['label'][substr($lang,0,2)])){
					//le label de l'ontologie dans la langue de l'interface
					$label = $infos['label'][substr($lang,0,2)];
				}else{
					//le label générique de l'ontologie
					$label = $infos['label']['default'];
				}
				break;
			}
		}

		return $label;
	}

	/**
	 * Renvoie les propriétés en fonction d'un nom de classe pmb
	 *
	 * @param string $pmb_name
	 *
	 * @return array
	 */
	public function get_onto_property_from_pmb_name($pmb_name) {
		if (!isset($this->ontology)) {
			$this->get_ontology();
		}
		$properties_uri = $this->ontology->get_properties();
		foreach ($properties_uri as $uri => $info) {
			if ($info->pmb_name == $pmb_name) {
				return $this->ontology->get_property("", $uri);
			}
		}
	}


	/**
	 * Retourne une instance de l'ontologie chargée à partir de onto_store
	 *
	 * @return onto_ontology
	 *
	 * @access public
	 */
	public function get_ontology() {
		if(!isset($this->ontology )){
			$this->ontology = new onto_ontology($this->onto_store);
		}
		$this->ontology->set_data_store($this->data_store);
		return $this->ontology;
	} // end of member function get_ontology


	/**
	 * Fonction d'accès aux requetes sparql dans l'onto store
	 *
	 * @param string $query
	 *
	 */
	public function onto_query($query){
		$this->onto_store->query($query);
		if($this->onto_store->num_rows()){
			return true;
		}elseif ($errs = $this->onto_store->get_errors()) {
		    self::print_errors($errs);
		}
		return false;
	}

	/**
	 * Fonction d'accès aux requetes sparql dans l'onto store
	 * renvoi le résultat
	 *
	 * @return array result
	 */
	public function onto_result(){
		if($this->onto_store->num_rows()){
			return $this->onto_store->get_result();
		}elseif ($errs = $this->onto_store->get_errors()) {
		    self::print_errors($errs);
		}
		return false;
	}

	/**
	 * Fonction d'accès aux requetes sparql dans l'onto store
	 * renvoi le nombre de résultat
	 *
	 * @return integer num rows
	 */
	public function onto_num_rows(){
		if($this->onto_store->num_rows()){
			return $this->onto_store->num_rows();
		}elseif ($errs = $this->onto_store->get_errors()) {
		    self::print_errors($errs);
		}
		return false;
	}

	/**
	 * Retourne vrai si la classe est une sous classe d'une indexation, faux sinon
	 * @param string $class_uri URI d'une classe
	 */
	public function class_is_indexed($class_uri){
		$query = "select ?subclass {
				<".$class_uri."> <http://www.w3.org/2000/01/rdf-schema#subClassOf> ?subclass .
				?subclass rdf:type pmb:indexation }";
		$this->onto_query($query);
		if($this->onto_num_rows()){
			return true;
		}
		return false;
	}

	/**
	 *
	 * @return onto_store
	 */
	public function get_data_store() {
		return $this->data_store;
	}

	/**
	 *
	 * @return onto_common_index
	 */
	public function get_onto_index() {
		if (empty($this->onto_index)) {
			$onto_index_class_name = $this->search_index_class_name();
			$this->onto_index = new $onto_index_class_name();
			$this->onto_index->set_handler($this);
		}
		return $this->onto_index;
	}

	/**
	 *
	 * @return string
	 */
	protected function search_index_class_name(){
		$suffixe = "_index";
		$prefix="onto_";
		if(class_exists($prefix.$this->get_onto_name().$suffixe)){
			return $prefix.$this->get_onto_name().$suffixe;
		}else{
			return 'onto_index';
		}
	}

	/**
	 *
	 * @param onto_common_item $item
	 */
	protected function get_inverse_of_properties($item) {
	    $inverse_of_properties = [];
	    if (!empty($item)) {
	        $inverse_of = $this->ontology->get_inverse_of_properties();
            $onto_class = $item->get_onto_class();
            foreach($onto_class->get_properties() as $property) {
                if (isset($inverse_of[$property])) {
                    $inverse_of_properties[$property] = $inverse_of[$property];
                }
            }
	    }
	    return $inverse_of_properties;
	}

	/**
	 * recupere les uri des proprietes qui composent le display_label
	 * @param string uri $class_uri
	 * @return NULL[]
	 */
	public function get_display_labels($class_uri){
	    $query = "select ?displayLabel where {
			<".$class_uri."> pmb:displayLabel ?displayLabel
		}";
	    $this->onto_store->query($query);
	    $displayLabels = [$this->default_display_label];
	    if($this->onto_store->num_rows()){
	        $displayLabels = [];
	        $results = $this->onto_store->get_result();
	        foreach ($results as $result) {
	            $displayLabels[] = $result->displayLabel;
	        }
	    }
	    return $displayLabels;
	}

	private function delete_linked_contribution_from_uri($item_uri)
	{
	    $query = "select * where {
			<".$item_uri."> ?predicate ?object
		}";

	    $success = false;

	    if ($this->data_store->query($query)) {
	        $results = $this->data_store->get_result();
	        foreach ($results as $result) {
	            if ($result->object_type == "uri" && $result->predicate != "http://www.w3.org/1999/02/22-rdf-syntax-ns#type" && !empty($result->object)) {
	                switch (TRUE) {
	                    // Author - Responsability
	                    case (strpos($result->object, "responsability") !== FALSE) :
	                        $success = $this->delete_linked_contribution_specific($result->object, "responsability");
	                        break;
	                        // Work - Linked work
	                    case (strpos($result->object, "linked_work") !== FALSE) :
	                        $success = $this->delete_linked_contribution_specific($result->object, "linked_work");
	                        break;
	                        // Record - Linked record
	                    case (strpos($result->object, "linked_record") !== FALSE) :
	                        $success = $this->delete_linked_contribution_specific($result->object, "linked_record");
	                        break;
	                        // Authority - Linked authority
	                    case (strpos($result->object, "linked_authority") !== FALSE) :
	                        $success = $this->delete_linked_contribution_specific($result->object, "linked_authority");
	                        break;
	                    default:
	                        $success = $this->delete_linked_contribution($result->object, $item_uri);
	                        break;
	                }
	            }
	        }
	    }

	    return $success;
	}

	private function delete_linked_contribution($linked_uri, $parent_uri)
	{
	    if (empty($linked_uri) || empty($parent_uri)) {
	        return FALSE;
	    }

	    $success = $this->data_store->query('select * where {
			?subject ?predicate <'.$linked_uri.'> .
            filter ( ?subject != "'.$parent_uri.'")
        }');

	    if ($success) {
	        $results = $this->data_store->get_result();
	        if (!count($results)) {
	            $this->data_store->query('delete {
                    <'.$linked_uri.'> ?predicate ?object .
                }');

	            $query = "delete from onto_uri where uri = '".$linked_uri."'";
	            pmb_mysql_query($query);

	            return TRUE;
	        }
	    }
	    return FALSE;
	}

	private function delete_linked_contribution_specific(string $linked_uri, string $type)
	{
	    if (empty($linked_uri) || empty($type)) {
	        return FALSE;
	    }
	    $predicate = "";
	    switch ($type) {
	        case "responsability":
	            $predicate = "pmb:has_author";
	            break;
	        case "linked_work":
	            $predicate = "pmb:has_work";
	            break;
	        case "linked_record":
	            $predicate = "pmb:has_record";
	            break;
	        case "linked_authority":
	            $predicate = "pmb:has_authority";
	            break;
	    }
	    if (!empty($predicate)) {

	        $success = $this->data_store->query('select ?uri where {
                <'.$linked_uri.'> '.$predicate.' ?uri .
            }');

	        if($success){
	            $uri = '';
	            if ($this->data_store->num_rows()) {
	                $results = $this->data_store->get_result();
	                $uri = $results[0]->uri;
	            }

	            if (!empty($uri)) {

	                // On supprime le lien
	                $this->data_store->query('delete {
                        <'.$linked_uri.'> ?predicate ?object .
                    }');

	                $this->delete_linked_contribution($uri, $linked_uri);

	                return TRUE;
	            }
	        }
	    }
	    return FALSE;
	}


	/**
	 *
	 * @param string $uri
	 * @return NULL|onto_common_item
	 */
	public static function get_item_instance($uri) {
	    return self::$item_instances[$uri] ?? null;
	}

	public static function print_errors($errs = array(), $query = "") {
	    global $charset;
	    global $pmb_display_errors;

	    if($pmb_display_errors) {
    	    print __FILE__.'  ('.__LINE__.')';
    	    print "<br>Erreurs: <br>";
    	    print "<pre>".(new Exception())->getTraceAsString()."</pre><br>";
    	    if (!empty($query)) {
        	    print "<pre>";print_r(htmlentities($query, ENT_QUOTES, $charset));print "</pre><br>";
    	    }
    	    if (!empty($errs)) {
        	    print "<pre>";print_r($errs);print "</pre><br>";
    	    }
	    }
	}

	public function save_conversion( $item , $draft = false, $force = false) {
	    global $area_id;

	    if ($force || $item->check_values() || $draft) {
	        if(onto_common_uri::is_temp_uri($item->get_uri()) && !$draft){
	            $item_tmp_uri = $item->get_uri();
	            $item->replace_temp_uri();
	            $this->replace_tmp_uri_store($item_tmp_uri, $item->get_uri());
	            $this->deleteDraft($item_tmp_uri);
	        }
	        $assertions = $item->get_assertions();

	        // On peut y aller
	        $query = "insert into <pmb> {";
	        $query .= $this->build_triples($assertions, $item->get_uri());
	        //$query .= ".\n <".addslashes($assertions[0]->get_subject())."> pmb:area ".$area_id;

	        //on ne rentre qu'une seule, afin de ne pas écraser le display label
	        if($assertions[0]->get_object_properties()['type'] == "uri") {
	            $display_label = $item->get_label($this->get_display_label($assertions[0]->get_object()));

	            //si pas de display label, on va chercher celui du parent
	            if (!$display_label) {
	                $sub_class_of = $this->ontology->get_sub_class_of($assertions[0]->get_object());
	                foreach ($sub_class_of as $parent_uri) {
	                    $display_label = $item->get_label($this->get_display_label($parent_uri));
	                    if ($display_label) {
	                        break;
	                    }
	                }
	            }
	            $query .= " .\n <".addslashes($assertions[0]->get_subject())."> pmb:displayLabel '".addslashes($display_label)."'";
	        }

	        $additionnal_data = $item->get_additionnal_data();
	        if (!empty($additionnal_data)) {
	            $query .= ".\n <".addslashes($assertions[0]->get_subject())."> pmb:additionnal_data '".addslashes(json_encode($additionnal_data))."'";
	        }

	        $query.="}";
	        $this->data_store->query($query);
	        $errs = $this->data_store->get_errors();
	        if (!empty($errs)) {
	            if ($this->get_onto_name() == "contribution") {
	                global $msg;

	                // Erreur lors de l'enregistrement
	                $report = array();
	                $report[$item->get_uri()] = [
	                    "type" => "sparql",
	                    "message" => $msg['contribution_error_save'],
	                    "errors" => $errs
	                ];
	                return $report;
	            } else {
	                print "<br/>Erreurs: <br/>";
	                print "<pre>";print_r($errs);print "</pre><br/>";
	            }
	        } else {
	            $item->post_save();
	            //TODO: a reprendre plus tard si besoin (indexation des contribution par exemple...)
	            if ($this->get_onto_name() == "skos") {
	                $index = new onto_index();
	                $index->set_handler($this);
	                $index->maj(0,$item->get_uri());
	            }
	        }
	    } else {
	        return $item->get_checking_errors();
	    }
	    return true;
	} // end of member function save

	private function build_triples($assertions, $main_uri) {
	    global $opac_url_base;

	    $nb_assertions = count($assertions);
	    $i = 0;

	    $subjects_deleted = array();
	    $pmb_id_stored = false;

	    // On peut y aller
	    $query = "";
	    foreach ($assertions as $assertion) {
	        if (!in_array($assertion->get_subject(), $subjects_deleted)) {
	            $pmb_id = 0;

	            //on stocke l'id de l'entité en base SQL s'il existe
	            $query_pmb_id = '	select ?pmb_id where {
						<'.$assertion->get_subject().'> pmb:identifier ?pmb_id
					}';
	            $this->data_store->query($query_pmb_id);
	            if ($this->data_store->num_rows()) {
	                $pmb_id = $this->data_store->get_result()[0]->pmb_id;
	            }

	            // On supprime tous les triplets correspondant à cette uri pour les mettre à jour par la suite
	            if ($assertion->get_subject() == $main_uri) {
	                $query_delete = "delete {
    						<".$assertion->get_subject()."> ?prop ?obj
    						}";
	                $this->data_store->query($query_delete);
	                $subjects_deleted[] = $assertion->get_subject();
	            } else {
	                $query_delete = "delete {
    						<".$assertion->get_subject()."> <".$assertion->get_predicate()."> <".$assertion->get_object().">
    						}";
	                $this->data_store->query($query_delete);
	            }

	            //puis on commence par ré-insèrer l'id de l'entité en base SQL dans le store
	            if ($pmb_id) {
	                if (!$this->data_store->num_rows()) {
	                    $query_insert = 'insert into <pmb> {
									<'.$assertion->get_subject().'> pmb:identifier "'.$pmb_id.'" .
								}';
	                    $this->data_store->query($query_insert);
	                    $pmb_id_stored = true;
	                }
	            }
	        }
	        if ($pmb_id_stored && strpos($assertion->get_predicate(), "identifier")) {
	            $i++;
	            continue;
	        }
	        if ($assertion->offset_get_object_property("type") == "literal"){
	            $object = '"'.addslashes($assertion->get_object()).'"';
	            $object_properties = $assertion->get_object_properties();
	            if (!empty($object_properties['lang'])) {
	                $object.="@".$object_properties['lang'];
	            }
	        }else{

	            if (empty($assertion->get_object())) {
	                // Aucune uri
	                $object = "''";
	            } else {
	                $object = "<".addslashes($assertion->get_object()).">";
	                if ($assertion->offset_get_object_property("type") == "uri"){

	                    if ($assertion->get_object_type()) {
	                        if (is_numeric($assertion->get_object())) {
	                            $query_bis = "	select ?uri where {
    													?uri pmb:identifier '".addslashes($assertion->get_object())."' .
    													?uri <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <".addslashes($assertion->get_object_type()).">
    												}";
	                            $this->data_store->query($query_bis);
	                            if (!$this->data_store->num_rows()) {
	                                $uri = "<".addslashes(onto_common_uri::get_new_uri($this->get_class_pmb_name($assertion->get_object_type()),$opac_url_base.$this->get_class_pmb_name($assertion->get_object_type())."#")).">";
	                                if ($assertion->offset_get_object_property('sub_uri')) {
	                                    $uri = "<".$assertion->offset_get_object_property('sub_uri').">";
	                                }
	                                $object = $uri;

	                                $object .= " .\n";
	                                //sujet
	                                $object .= $uri;
	                                //prédicat
	                                $object .= ' pmb:identifier ';
	                                //objet
	                                $object .= '"'.addslashes($assertion->get_object()).'"';

	                                $object .= " .\n";
	                                //sujet
	                                $object .= $uri;
	                                //prédicat
	                                $object .= ' <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ';
	                                //objet
	                                $object .= '<'.addslashes($assertion->get_object_type()).'>';

	                                if ($assertion->offset_get_object_property('display_label')) {
	                                    $object .= " .\n";
	                                    //sujet
	                                    $object .= $uri;
	                                    //prédicat
	                                    $object .= ' pmb:displayLabel ';
	                                    //objet
	                                    $object .= '"'.addslashes($assertion->offset_get_object_property('display_label')).'"';
	                                }
	                                $new_uri = $uri;
	                                $uri = "";
	                            } else {
	                                $uri = "<".$this->data_store->get_result()[0]->uri.">";
	                                $object = $uri;
	                                $new_uri = $object;
	                            }
	                        }
	                        if ($assertion->offset_get_object_property('object_assertions')) {
	                            $tmp_uri = '<'.addslashes($assertion->get_object()).'>';
	                            if (is_numeric($assertion->get_object()) && !empty($new_uri)) {
	                                $tmp_uri = $new_uri;
	                            }
	                            $object .= " .\n";
	                            //sujet
	                            $object .= $tmp_uri;
	                            //prédicat
	                            $object .= ' pmb:has_assertions ';
	                            //objet
	                            $object .= '"1"';
	                            $object .= " .\n";
	                            if (!is_numeric($assertion->get_object())) {
    	                            //sujet
    	                            $object .= $tmp_uri;
    	                            //prédicat
    	                            $object .= ' <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ';
    	                            //objet
    	                            $object .= "<".addslashes($assertion->get_object_type())."> .\n";
	                            }
	                            $object .= $this->build_triples($assertion->offset_get_object_property('object_assertions'),trim($tmp_uri, "<>"));
	                        } else {

	                            // On essaie de recuperer le display label supprime suite à une purge des stores
	                            if (!is_numeric($assertion->get_object())) {
	                                $uri = "<".addslashes($assertion->get_object()).">";
	                            }
	                            if (!empty($uri)) {
	                                $query_bis = "select ?displayLabel where {
                    	                               $uri pmb:displayLabel ?displayLabel .
                                                    }";

	                                $this->data_store->query($query_bis);
	                                if (!$this->data_store->num_rows()) {
	                                    if ($assertion->offset_get_object_property('display_label')) {
	                                        $object .= " .\n";
	                                        //sujet
	                                        $object .= $uri;
	                                        //prédicat
	                                        $object .= ' pmb:displayLabel ';
	                                        //objet
	                                        $object .= '"'.addslashes($assertion->offset_get_object_property('display_label')).'"';
	                                    }
	                                }
	                            }
	                        }
	                    }
	                }
	            }
	        }
	        $query.= " <".addslashes($assertion->get_subject())."> <".addslashes($assertion->get_predicate())."> ".$object;

	        $i++;
	        if ($i < $nb_assertions) {
	            $query.=" .\n";
	        }

	        //$query.=" \n";
	    }
	    return $query;
	}

	public function get_pmb_name($class_uri)
	{
	    if(!empty(self::$pmb_names[$class_uri])){
	        return self::$pmb_names[$class_uri];
	    }
	    $query = 'select ?name where {
            <'.$class_uri.'> pmb:name ?name
        }';
	    $this->onto_query($query);
	    $result = $this->onto_result();
	    self::$pmb_names[$class_uri] = $result[0]->name ?? "";
	    return self::$pmb_names[$class_uri];

	}

} // end of onto_handler