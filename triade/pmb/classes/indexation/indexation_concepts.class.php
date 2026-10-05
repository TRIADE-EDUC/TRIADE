<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexation_concepts.class.php,v 1.2.2.1.2.1 2025/04/24 14:45:33 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/indexation/indexation_entities.class.php");

//classe de calcul d'indexation des notices...
class indexation_concepts extends indexation_entities {

    /**
     * handler
     *
     * @var onto_handler
     * @access public
     */
    public $handler;

    /**
     * properties
     *
     * @var Array()
     * @access protected
     */
    protected $properties;

    /**
     * infos
     *
     * @var array
     * @access public
     */
    public $infos;

    /**
     * sparql_result
     *
     * @var array
     * @access protected
     */
    protected static $sphinx_indexer;

    /**
     * en nettoyage de base ou non
     * @var bool
     */
    protected $netbase = false;

    protected $sparql_result;

    protected $classes;

    protected $lang_codes = array(
        'fr' => 'fr_FR',
        'en' => 'en_UK',
        'nl' => 'nl_NL',
        'ar' => 'ar',
        'ca' => 'ca_ES',
        'es' => 'es_ES',
        'hu' => 'hu_HU',
        'it' => 'it_IT',
        'pt' => 'pt_PT',
        'ro' => 'ro_RO'
    );

    protected $start = 0;

    protected $lot = 0;

    public function __construct($xml_filepath, $table_prefix, $type = 0){
        $this->fields_prefix = 'skos_fields';
        $this->words_prefix = 'skos_words';
        parent::__construct($xml_filepath, $table_prefix, $type);
    }

    protected function clean_temporary_files() {
        netbase_entities::clean_files($this->directory_files, 'skos');
    }

    protected function get_prefix_temporary_file() {
        if(empty($this->prefix_temporary_file)) {
            $this->prefix_temporary_file = "indexation_skos_".LOCATION;
        }
        return $this->prefix_temporary_file;

    }

    public function load_handler($ontology_filepath, $onto_store_type, $onto_store_config, $data_store_type, $data_store_config, $tab_namespaces, $default_display_label){
        $this->handler = new onto_handler($ontology_filepath, $onto_store_type, $onto_store_config, $data_store_type, $data_store_config, $tab_namespaces, $default_display_label);
    }

    public function set_handler($handler){
        $this->handler = $handler;
    }

    public function init(){
        global $base_path;

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

        $tab_namespaces=array(
            "skos"	=> "http://www.w3.org/2004/02/skos/core#",
            "dc"	=> "http://purl.org/dc/elements/1.1",
            "dct"	=> "http://purl.org/dc/terms/",
            "owl"	=> "http://www.w3.org/2002/07/owl#",
            "rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
            "rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
            "xsd"	=> "http://www.w3.org/2001/XMLSchema#",
            "pmb"	=> "http://www.pmbservices.fr/ontology#"
        );

        $this->load_handler($base_path."/classes/rdf/skos_pmb.rdf", "arc2", $onto_store_config, "arc2", $data_store_config,$tab_namespaces,'http://www.w3.org/2004/02/skos/core#prefLabel');

        $this->handler->get_ontology();
        $this->table_prefix = $this->handler->get_onto_name();
        $this->reference_key = "id_item";
        $this->analyse_indexation();
        if (!empty($this->tab_code_champ)) {
            $this->champ_trouve = true;
        }
    }

    protected function analyse_indexation(){
        if(empty($this->infos) || count($this->infos) == 0){
            $cache = cache_factory::getCache();
            $ontology = $this->handler->get_ontology();
            $this->classes = $this->handler->get_classes();
            $this->properties = $ontology->get_properties();
            if(is_object($cache)){
                $infos = $cache->getFromCache('onto_'.$ontology->name.'_index_infos');
                if(is_array($infos) && count($infos)){
                    $tab_code_champ = $cache->getFromCache('onto_'.$ontology->name.'_index_tab_code_champ');
                    if(is_array($tab_code_champ) && count($tab_code_champ)){
                        $this->infos = $infos;
                        $this->tab_code_champ = $tab_code_champ;
                        return;
                    }

                }
            }

            if (is_array($this->classes)) {
                foreach($this->classes as $class){
                    $query = "select * where {
        				<".$class->uri."> <http://www.w3.org/2000/01/rdf-schema#subClassOf> ?subclass .
        				?subclass rdf:type pmb:indexation .
        			}";
                    $this->handler->onto_query($query);
                    if($this->handler->onto_num_rows()){
                        $results= $this->handler->onto_result();
                        foreach($results as $result){
                            $this->recurse_analyse_indexation($class->uri,$result->subclass);
                        }
                    }
                }
            }
            if(is_object($cache)){
                $cache->setInCache('onto_'.$ontology->name.'_index_tab_code_champ',$this->tab_code_champ);
                $cache->setInCache('onto_'.$ontology->name.'_index_infos',$this->infos);
            }
        }
    }

    protected function recurse_analyse_indexation($class_uri,$indexnode){
        $unions  =array();
        $query = "select * where {
			<".$indexnode."> rdf:type pmb:indexation .
			<".$indexnode."> owl:onProperty ?property .
			<".$indexnode."> pmb:pound ?pound .
			<".$indexnode."> pmb:field ?field .
			<".$indexnode."> pmb:subfield ?subfield .
            optional {
                <".$indexnode."> owl:unionOf ?union
            } .
            optional {
				<".$indexnode."> pmb:useProperty ?use .
			} .
            optional {
                <".$indexnode."> pmb:onRange ?on_range .
			} .
		}";
        $this->handler->onto_query($query);
        if($this->handler->onto_num_rows()){
            $results= $this->handler->onto_result();
            foreach($results as $result){
                $element = [
                    'property' => $result->property,
                ];
                if(!empty($result->use)){
                    $element['use'] = $result->use;
                }
                if(!empty($result->on_range)){
                    $element['on_range'] = $result->on_range;
                }
                $this->infos[$class_uri][$result->pound][]= $element;
                $name = $this->classes[$class_uri]->pmb_name."_".$this->properties[$result->property]->pmb_name;
                if(!empty($result->on_range)){
                    $name.= '_'.$this->classes[$result->on_range]->pmb_name.'_'.$this->properties[$result->use]->pmb_name;
                } else if(!empty($result->use)){
                    $name.= '_'.$this->properties[$result->use]->pmb_name;
                }
                $this->tab_code_champ[$result->field][$name] = array(
                    'champ' => $result->field,
                    'ss_champ' => $result->subfield,
                    'pond' => $result->pound,
                    'no_words' => false
                );
            }
            if(isset($result->union) && $result->union && !in_array($result->union,$unions)){
                $unions[]=$result->union;
            }
        }
        foreach($unions as $union){
            $this->recurse_analyse_indexation($class_uri,$union);
        }
    }

    public function get_sparql_result($object_uri) {

        $assertions = array();
        $query = "SELECT * WHERE {
			<".$object_uri."> rdf:type ?type
 		}";
        $this->sparql_result = array();

        $this->handler->data_query($query);
        if($this->handler->data_num_rows()){
            $result = $this->handler->data_result();
            $type = $result[0]->type;
            if($type){
                if(isset($this->infos[$type]) && is_array($this->infos[$type])){
                    foreach($this->infos[$type] as $elements){
                        foreach($elements as $element){
                            $name = $this->classes[$type]->pmb_name."_".$this->properties[$element['property']]->pmb_name;
                            $assertions[] = 'optional { '.PHP_EOL.'  <'.$object_uri.'> <'.$element['property'].'> ?'.$name . ' . '.PHP_EOL.'}';
                        }
                    }
                }
            }
        }
        // On peut avoir des doublons en cas de range multiples !
        $assertions=array_unique($assertions);
        if(count($assertions)){
            // Une query ne peut pas être composer que d'optional
            $query = "SELECT * WHERE {".PHP_EOL;
            $query .= "<".$object_uri."> rdf:type ?type .".PHP_EOL;
            $query .= implode(" . ".PHP_EOL,$assertions).PHP_EOL."}";

            if($this->handler->data_query($query)){
                if($this->handler->data_num_rows()){
                    $rows = $this->handler->data_result();
                    //on parcours toutes les assertions utilies à l'indexation
                    foreach($rows as $row){
                        //on parcours la propriété infos pour retrouver les bons éléments
                        foreach($this->infos[$type] as $elements){
                            $prefix = $this->classes[$type]->pmb_name."_";
                            foreach($elements as $element){
                                $var_name = $prefix.$this->properties[$element['property']]->pmb_name;
                                if(isset($row->{$var_name})){
                                    switch(true){
                                        case !empty($element['on_range']) :
                                            $query = "select * where {
												<".$row->{$var_name}."> <".$element['use']."> ?sub_property .
                                                <".$row->{$var_name}."> rdf:type <".$element['on_range']."> .
											}";
                                            $this->handler->data_query($query);
                                            if($this->handler->data_num_rows()){
                                                $result = $this->handler->data_result();
                                                $lang = '';
                                                $subrows = $this->handler->data_result();
                                                $subname = $var_name.'_'.$this->classes[$element['on_range']]->pmb_name.'_'.$this->properties[$element['use']]->pmb_name;
                                                foreach($subrows as $subrow){
                                                    if (isset($subrow->sub_property_lang) && isset($this->lang_codes[$subrow->sub_property_lang])) {
                                                        $lang = $this->lang_codes[$subrow->sub_property_lang];
                                                    }
                                                    if(!isset($this->sparql_result[$subname][$row->{$var_name}])){
                                                        $this->sparql_result[$subname][$row->{$var_name}] = array();
                                                    }
                                                    if(!isset($this->sparql_result[$subname][$row->{$var_name}][$lang])){
                                                        $this->sparql_result[$subname][$row->{$var_name}][$lang] = array();
                                                    }
                                                    if (!in_array($subrow->sub_property,$this->sparql_result[$subname][$row->{$var_name}][$lang])){
                                                        $this->sparql_result[$subname][$row->{$var_name}][$lang][] = $subrow->sub_property;
                                                    }
                                                }
                                            }
                                            break;
                                        case !empty($element['use']) :
                                            $query = "select * where {
												<".$row->{$var_name}."> <".$element['use']."> ?sub_property .
											}";
                                            $this->handler->data_query($query);
                                            if($this->handler->data_num_rows()){
                                                $lang = '';
                                                $subrows = $this->handler->data_result();
                                                $subname = $var_name.'_'.$this->properties[$element['use']]->pmb_name;
                                                foreach($subrows as $subrow){
                                                    if (isset($subrow->sub_property_lang) && isset($this->lang_codes[$subrow->sub_property_lang])) {
                                                        $lang = $this->lang_codes[$subrow->sub_property_lang];
                                                    }
                                                    if(!isset($this->sparql_result[$subname][$row->{$var_name}])){
                                                        $this->sparql_result[$subname][$row->{$var_name}] = array();
                                                    }
                                                    if(!isset($this->sparql_result[$subname][$row->{$var_name}][$lang])){
                                                        $this->sparql_result[$subname][$row->{$var_name}][$lang] = array();
                                                    }
                                                    if (!in_array($subrow->sub_property,$this->sparql_result[$subname][$row->{$var_name}][$lang])){
                                                        $this->sparql_result[$subname][$row->{$var_name}][$lang][] = $subrow->sub_property;
                                                    }
                                                }
                                            }
                                            break;
                                        default :
                                            $lang = "";
                                            if (isset($row->{$var_name."_lang"}) && isset($this->lang_codes[$row->{$var_name."_lang"}])) {
                                                $lang = $this->lang_codes[$row->{$var_name."_lang"}];
                                            }
                                            if(!isset($this->sparql_result[$var_name][$lang])){
                                                $this->sparql_result[$var_name][$lang] = array();
                                            }
                                            if(!in_array($row->{$var_name},$this->sparql_result[$var_name][$lang])){
                                                $this->sparql_result[$var_name][$lang][] = $row->{$var_name};
                                            }
                                            break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    protected function maj_tab_code_champ($object_id=0) {
        foreach($this->tab_code_champ as $element) {
            foreach ($element as $column => $infos){
                if(isset($this->sparql_result[$column])){
                    $field_order = 1;
                    foreach($this->sparql_result[$column] as $key => $values){
                        foreach($values as $key2 => $value){
                            if(is_string($value)){
                                $language = $key;
                                //fields (contenu brut)
                                $this->add_tab_field_insert($object_id,$infos,$field_order,$value, $language);

                                //words (contenu éclaté)
//                                 $tab_tmp=explode(' ',strip_empty_words($value));
//                                 $word_position = 1;
//                                 foreach($tab_tmp as $word){
//                                     $num_word = indexation::add_word($word, $language);
//                                     $tab_words_insert[]="(".$object_id.",".$infos["champ"].",".$infos["ss_champ"].",".$num_word.",".$infos["pond"].",$field_order,$word_position)";
//                                     $word_position++;
//                                 }
                            }else {
                                $language = $key2;
                                $autority_num = onto_common_uri::get_id($key);

                                foreach($value as $val){
                                    //fields (contenu brut)
                                    $this->add_tab_field_insert($object_id,$infos,$field_order,$val, $language, $autority_num);

                                    //words (contenu éclaté)
//                                     $tab_tmp=explode(' ',strip_empty_words($val));
//                                     $word_position = 1;
//                                     foreach($tab_tmp as $word){
//                                         $num_word = indexation::add_word($word, $language);
//                                         $tab_words_insert[]="(".$object_id.",".$infos["champ"].",".$infos["ss_champ"].",".$num_word.",".$infos["pond"].",$field_order,$word_position)";
//                                         $word_position++;
//                                     }
                                }
                            }
                            $field_order++;
                        }
                    }
                }else{
                    continue;
                }
            }
        }
    }

    public function maj($object_id, $datatype="all"){
        if(!count($this->tab_code_champ)){
            $this->init();
        }

//         $onto_index = onto_index::get_instance("skos");

        //la requete de base...
        $query = "select * where {
				?item <http://www.w3.org/2004/02/skos/core#prefLabel> ?label .
				?item rdf:type ?type .
				filter(";
        $i=0;
        foreach($this->infos as $uri => $infos){
            if($i) $query.=" || ";
            $query.= "?type=<".$uri.">";
            $i++;
        }
        $query.=")
			}";
        $query .= " order by asc(?label)";
        $this->objects_ids = [];
        if(!empty($this->start) || !empty($this->lot)) {
            $query .= " limit ".$this->lot." offset ".$this->start;
        }
        $this->handler->data_query($query);
        if($this->handler->data_num_rows()) {
            $this->set_deleted_index(true);
            $results = $this->handler->data_result();
            foreach($results as $row){
                $concept_id = onto_common_uri::get_id($row->item);
                $this->get_sparql_result($row->item);
                $this->maj_tab_code_champ($concept_id);
                if(!empty($this->start) || !empty($this->lot)) {
                    $this->objects_ids[] = $concept_id;
                }
            }
        }
        $this->maj_custom_field($object_id, 'skos', 0, '1100');
        return true;
    }

    protected function push_elements($tab_insert, $tab_field_insert){
        if($tab_insert && count($tab_insert)){
            $req_insert="insert into ".$this->table_prefix."_words_global_index(id_item,code_champ,code_ss_champ,num_word,pond,position,field_position) values ".implode(',',$tab_insert)." ON DUPLICATE KEY UPDATE num_word = num_word";
            file_put_contents($this->directory_files.$this->words_prefix.'_global_index.sql', $req_insert."\n", FILE_APPEND);
        }
        if($tab_field_insert && count($tab_field_insert)){
            //la table pour les recherche exacte
            $req_insert="insert into ".$this->table_prefix."_fields_global_index(id_item,code_champ,code_ss_champ,ordre,value,lang,pond,authority_num) values ".implode(',',$tab_field_insert)." ON DUPLICATE KEY UPDATE value = value";
            file_put_contents($this->directory_files.$this->fields_prefix.'_global_index.sql', $req_insert."\n", FILE_APPEND);
        }
    }

    protected function save_elements($tab_insert, $tab_field_insert){
        if($tab_insert && count($tab_insert)){
            $req_insert="insert into ".$this->table_prefix."_words_global_index(id_item,code_champ,code_ss_champ,num_word,pond,position,field_position) values ".implode(',',$tab_insert)." ON DUPLICATE KEY UPDATE num_word = num_word";
            pmb_mysql_query($req_insert);
        }
        if($tab_field_insert && count($tab_field_insert)){
            //la table pour les recherche exacte
            $req_insert="insert into ".$this->table_prefix."_fields_global_index(id_item,code_champ,code_ss_champ,ordre,value,lang,pond,authority_num) values ".implode(',',$tab_field_insert)." ON DUPLICATE KEY UPDATE value = value";
            pmb_mysql_query($req_insert);
        }
    }

    public function get_label() {
        global $msg;

        return $msg["nettoyage_reindex_concept"];
    }

    public function set_start($start) {
        $this->start = intval($start);
    }

    public function set_lot($lot) {
        $this->lot = intval($lot);
    }

}