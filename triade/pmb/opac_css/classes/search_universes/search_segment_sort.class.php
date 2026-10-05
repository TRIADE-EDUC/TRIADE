<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_sort.class.php,v 1.30.2.3.2.1 2025/04/23 08:26:49 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once "$class_path/fields/sort_fields.class.php";

class search_segment_sort extends sort {

	protected $num_segment;

	protected $human_query;

	protected $sort;

	protected $type;

	protected $table_tempo;

	protected $sort_fields;

	protected $default_sort;

	public $result = array();

	public function __construct($num_segment = 0) {
	    $this->num_segment = intval($num_segment);
		$this->fetch_data();
		if($this->type >= 10000){
		    $const_type = TYPE_ONTOLOGY;
		}else if ($this->type >= 1000){
		    $const_type = TYPE_AUTHPERSO;
		}else{
		    $const_type = $this->type;
		}
		$type = entities::get_string_from_const_type($const_type);
		parent::__construct($type, 'session');
	}

	protected function fetch_data() {
	    $this->type = '';
		if ($this->num_segment) {
			$query = '
			    SELECT search_segment_sort, search_segment_type
			    FROM search_segments
			    WHERE id_search_segment = "'.$this->num_segment.'"
			';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_assoc($result);
				pmb_mysql_free_result($result);

				$this->sort = $this->parse_sort(stripslashes($row['search_segment_sort']));
				$this->type = $row['search_segment_type'];
			}
		}
	}

	/*
	 * Permet de récupérer le tri par défaut dans la chaine de tris du segment et de l'y retirer
	 */
	protected function parse_sort($sort_string){
	    $sort_string = translation::get_translated_text($this->num_segment, 'search_segments', 'segment_sort', $sort_string);
	    $sort_array = explode("||", $sort_string);
	    $this->default_sort = 0;
	    foreach ($sort_array as $key=>$sort) {
	        if (is_numeric(trim($sort))) {
	            $this->default_sort = trim($sort);
	            unset($sort_array[$key]);
	        }
	    }
	    return implode("||", $sort_array);
	}

	public function get_sort() {
	    return $this->sort;
	}

	public function get_segment_default_sort() {
	    return $this->default_sort;
	}

	public function get_human_query() {
	    if (isset($this->human_query)) {
	        return $this->human_query;
	    }
	    if (empty($this->sort)) {
	        return '';
	    }
	    $this->get_sort_fields();
	    $fields = encoding_normalize::json_decode($this->sort, true);
	    $this->human_query = $this->sort_fields->get_human_query($fields);
	    return $this->human_query;
	}

	public function get_form() {
	    return "";
	}

	public function set_properties_from_form(){
	    $this->get_sort_fields();
	    $this->sort = encoding_normalize::json_encode($this->sort_fields->format_fields());
	    $this->human_query = $this->get_human_query();
	}

	public function update() {
	    if (!$this->num_segment) {
	        return false;
	    }
		$query = '
		    UPDATE search_segments
		    SET search_segment_sort = "'.addslashes($this->sort).'"
		    WHERE id_search_segment = "'.$this->num_segment.'"';
		return pmb_mysql_query($query);
	}

	public function delete_sort(){
	    $this->sort = "";
	    $this->human_query = "";
	}

	private function get_sort_fields() {
	    if (!isset($this->sort_fields)) {
	        $this->sort_fields = new sort_fields($this->get_indexation_type(), $this->get_indexation_path(), $this->get_sub_type());
	    }
	    return $this->sort_fields;
	}

	private function get_indexation_path() {
	    global $include_path;
	    $string_type = entities::get_string_from_const_type($this->type);
	    switch ($string_type) {
	        case 'ontology' :
	            break;
	        case 'notices' :
	            return $include_path."/indexation/notices/champs_base.xml";
	        case 'animations' :
	            return $include_path."/indexation/animations/champs_base.xml";
	        default :
	            return $include_path."/indexation/authorities/$string_type/champs_base.xml";
	    }
	}

	private function get_indexation_type() {
	    switch ($this->type) {
	        case TYPE_NOTICE :
	            return "notices";
	        case TYPE_ANIMATION :
	            return "animations";
	        case TYPE_CMS_EDITORIAL :
	            return "cms_editorial";
	        default :
	            if($this->type> 10000){
	                return "ontologies";
	            }
	            return "authorities";
	    }
	}

	private function get_sub_type() {
	    return entities::get_aut_table_from_type($this->type);
	}

	/**
	 * Sort data
	 *
	 * @param array $data
	 * @param integer $offset
	 * @param integer $limit
	 * @return array
	 */
	public function sort_data($data, $table_tempo, $offset = 0, $limit = 0) {
		$query_searcher = "SELECT * FROM $table_tempo";
		if (false === pmb_mysql_query($query_searcher)) {
			// $query_searcher ne fonctionne pas, on affiche quand meme des resultat
			$this->result = array_slice($data, $offset, $limit);
		} else {
			$query = $this->appliquer_tri($this->num_segment, $query_searcher, $this->params['REFERENCEKEY'], $offset, $limit);
			$res = pmb_mysql_query($query);
			if ($res && pmb_mysql_num_rows($res)) {
				$this->result = array();
				while ($row = pmb_mysql_fetch_assoc($res)) {
					$this->result[] = $row[$this->params["REFERENCEKEY"]];
				}
				pmb_mysql_free_result($res);
			}
		}

	    return $this->result;
	}

	public function add_session_currentSegment($id){
	    $_SESSION['sort_segment_'.$this->num_segment.'currentSort'] = $id;
	    return true;
	}

	public function show_tris_selector_segment() {
        global $search_index, $charset, $msg;

        $selected_sort = 0;
        if (isset($_SESSION['sort_segment_'.$this->num_segment.'currentSort'])){
            $selected_sort = $_SESSION['sort_segment_'.$this->num_segment.'currentSort'];
        } else {
            if ($this->get_segment_default_sort()!=null) {
                $selected_sort = $this->get_segment_default_sort();
            }
        }
	    $sorts = array();
	    $sorts = explode('||',$this->sort);
        $html = '<label id="segment_sort_label" for="segment_sort">' . $msg['list_applied_sort'] . '</label>
                    <select onChange=applySort(this.options[this.selectedIndex].value) name="segment_sort" id="segment_sort">';
        foreach ($sorts as $sort_id => $sort){
            if (!empty(explode('|',$sort)[1])){
                $sort_name = explode('|',$sort)[1];
            } else {
                $sort_name = '';
            }
            $html .= '<option  value="'.$sort_id.'"'.  (( intval($selected_sort) == intval($sort_id)) ? " selected" : "").'" >'.htmlentities($sort_name, ENT_QUOTES, $charset).'</option>';
        }

        //permet que le tri conserve l'historique de recherche
        if ($_SESSION['search_universes'.$search_index]['segments']){
            $segment_history = count($_SESSION['search_universes'.$search_index]['segments'])-1;
        } else {
            $segment_history = 0;
        }
        $location = "&action=segment_results&id=".$this->num_segment."&universe_history=".$search_index."&segment_history=".$segment_history;

	    $html .= "</select></span>
            <script>
            function applySort(value){
        	    var myRequest = new XMLHttpRequest();
        	    myRequest.open('GET', './ajax.php?module=ajax&categ=search_segment&action=add_session_currentSegment&num_segment=".$this->num_segment."&segment_sort='+value);
        	    myRequest.onreadystatechange = function () {
                    if (myRequest.readyState === 4) {
                        document.location = 'index.php?lvl=search_segment".$location."&segment_sort='+value;
        	        }
        	    };
                myRequest.send();
            }
            </script><span class=\"espaceResultSearch\">&nbsp;</span>";
	    return $html;
	}

	/**
	 * Ajoute les tris par défaut éventuellement saisis en paramètre
	 */
	public function add_default_sort(){
	    if ($this->sort) {
	        if (empty($_SESSION['sort_segment_'.$this->num_segment.'_list']) || $_SESSION['sort_segment_'.$this->num_segment.'_list'] != $this->sort) {
	            $_SESSION['sort_segment_'.$this->num_segment.'_list'] = $this->sort;
	            $_SESSION['sort_segment_'.$this->num_segment.'flag'] = 0;
	        }
	        //on vérifie l'existence d'un flag : que la recherche par défaut ne revienne pas si l'utilisateur l'a supprimée par le formulaire
	        if(empty($_SESSION['sort_segment_'.$this->num_segment.'flag'])){
	            //On réinitialise les tri
        	    $_SESSION["sort_segment_".$this->num_segment] = array();
        	    //Puis on les réajoute en session
	            $tmpArray = explode("||",$this->sort);
	            foreach($tmpArray as $tmpElement){
	                if(trim($tmpElement)){
	                    if (strstr($tmpElement,'|')) {
	                        $tmpSort=explode("|",$tmpElement);
	                        $this->add_session_sort($tmpSort[0],$tmpSort[1]);
	                    } else {
	                        $this->add_session_sort($tmpElement);
	                    }
	                }
	            }
	            $_SESSION['sort_segment_'.$this->num_segment.'flag']=1;
	        }
	    }
	}

	private function add_session_sort($sortDes, $sortName =''){
	    global $charset;
	    $_SESSION["sort_segment_".$this->num_segment][]= [
	        "name" => htmlentities($sortName,ENT_QUOTES,$charset),
	        "des"  => htmlentities($sortDes,ENT_QUOTES,$charset)
	    ];
	}

	public function get_translated_sort($sort_name, $i, $language) {
	    $translated_text = translation::get_translated_text($this->num_segment, 'search_segments', 'segment_sort', $sort_name, $language);
	    if (!empty($translated_text)) {
	        $translated_sorts = explode('||',$translated_text);
	        if (!empty(explode('|',$translated_sorts[$i])[1])){
	            return explode('|',$translated_sorts[$i])[1];
	        }
	    }
	    return $sort_name;
	}

	public function recupTriParId($id) {
	    $tab = [];
	    $tab["nom_tri"] = "";
		//Ajout des tri de session pour les segments
		if (!empty($_SESSION["sort_segment_".$id])) {
    	    if (isset($_SESSION['sort_segment_'.$id.'currentSort'])){
    	        $segment_sort = $_SESSION['sort_segment_'.$id.'currentSort'];
    	    } else {
    	        $segment_sort = $this->get_segment_default_sort();
    	    }
		    $tab["nom_tri"] = $_SESSION["sort_segment_".$id][$segment_sort]['name'];
		    $tab["tri_par"] = $_SESSION["sort_segment_".$id][$segment_sort]['des'];
		}
		return $tab;
	}

	public function parse() {
	    if ($this->type >= 10000) {
	        $this->parse_ontology();
	    } elseif ($this->type >= 1000) {
	        $this->parse_authperso();
	    } else {
	        parent::parse();
	    }
	}

	private function parse_ontology() {

	    $params_name = $this->dSort->sortName . "_params";
	    global ${$params_name};
	    $params = ${$params_name};
	    if ($params) {
	        $this->params = $params;
	    }
	    $this->params['REFERENCEKEY'] = 'id_item';

	    $class_uri = onto_common_uri::get_uri(($this->type-10000));
	    $ontology = new ontology(ontologies::get_ontology_id_from_class_uri($class_uri));
	    $onto = $ontology->get_onto();
	    foreach ($onto->get_classes() as $c) {
	        if($class_uri != $c->uri){
	            continue;
	        }
	        $class = $onto->get_class($c->uri);
	        foreach ($class->get_properties() as $uri_property) {
	            $property = $class->get_property($uri_property);
	            switch ($property->pmb_datatype) {
	                case "http://www.pmbservices.fr/ontology#integer";
	                $type = "num";
	                break;
	                default :
	                    $type = "text";
	                    break;
	            }
	            $p_tri = array(
	                'SOURCE' => 'onto',
	                'TYPEFIELD' => $property->pmb_datatype,
	                'ID' => onto_common_uri::get_id($property->uri),
	                'TYPE' => $type,
	                'NAME' => $property->pmb_name,
	                'LABEL' => $property->get_label()
	            );
                if (!empty($p_tri)) {
                   $this->params['FIELD'][] = $p_tri;
                }
	        }
	    }
	}

	private function parse_authperso() {
	    global $include_path;

	    $params_name = $this->dSort->sortName . "_params";
	    global ${$params_name};
	    $params = ${$params_name};

	    if ($params) {
	        $this->params = $params;
	    } else {
	        $nomfichier = $include_path . "/sort/" . $this->dSort->sortName . "/sort.xml";

	        if (file_exists($include_path . "/sort/" . $this->dSort->sortName . "/sort_subst.xml")) {
	            $nomfichier = $include_path . "/sort/" . $this->dSort->sortName . "/sort_subst.xml";
	            $fp = fopen($nomfichier, "r");
	        } elseif (file_exists($nomfichier)) {
	            $fp = fopen($nomfichier, "r");
	        }

	        if ($fp) {
	            $xml = fread($fp, filesize($nomfichier));
	            fclose($fp);
	            $params = _parser_text_no_function_($xml, "SORT", $nomfichier);
	            $this->params = $params;
	        } else {
	            $this->error = true;
	            $this->error_message = "Can't open definition file";
	        }
	    }

	    if (empty($this->params['PPERSOPREFIX'])) {
	        return;
	    }

	    $authperso = new authperso($this->type - 1000);

	    foreach ($authperso->info['fields'] as $field) {
	        if (!empty($field['opac_sort'])) {
	            $param = $field['OPTIONS'][0];
	            switch ($field['type']) {
	                case 'comment':
	                case 'text':
	                    $tablefield = $field['custom_prefixe'] . '_custom_' . $field['datatype'];
                        $groupby = '';
	                    if (isset($param['REPETABLE']) && $param['REPETABLE'][0]['value']) {
	                        $tablefield = "group_concat(" . $field['custom_prefixe'] . "_custom_" . $field['datatype'] . " separator ' ')";
	                        $groupby = 'group by ' . $this->params['REFERENCEKEY'];
	                    }
	                    $p_tri = array(
	                        'SOURCE' => 'cp',
	                        'TYPEFIELD' => 'select',
	                        'ID' => 'cp' . $field['id'],
	                        'TYPE' => 'text',
	                        'NAME' => $field['name'],
	                        'LABEL' => translation::get_text($field['id'], 'authperso_custom', 'titre',  $field['label']),
	                        'TABLEFIELD' => ['value' => $tablefield],
	                        'REQ_SUITE' => "left join " . $field['custom_prefixe'] . "_custom_values on " . $this->params["REFERENCE"] . ".num_object = " . $field['custom_prefixe'] . "_custom_values." . $field['custom_prefixe'] . "_custom_origine and type_object = " . $this->params["TYPEOBJECT"] . " where " . $field['custom_prefixe'] . "_custom_values." . $field['custom_prefixe'] . "_custom_champ = '" . $field['id'] . "' $groupby"
	                    );
	                    break;
	                case 'list':
                        $tablefield = 'authperso_custom_list_lib';
                        $groupby = '';
	                    if ($param['MULTIPLE'][0]['value']) {
	                        $tablefield = "group_concat(authperso_custom_list_lib separator ' ')";
	                        $groupby = 'group by ' . $this->params['REFERENCEKEY'];
	                    }
	                    $p_tri = array(
	                        'SOURCE' => 'cp',
	                        'TYPEFIELD' => 'select',
	                        'ID' => 'cp' . $field['id'],
	                        'TYPE' => 'text',
	                        'NAME' => $field['name'],
	                        'LABEL' => translation::get_text($field['id'], 'authperso_custom', 'titre',  $field['label']),
	                        'TABLEFIELD' => ['value' => $tablefield],
	                        'REQ_SUITE' => "left join " . $field['custom_prefixe'] . "_custom_values on " . $this->params["REFERENCE"] . ".num_object = " . $field['custom_prefixe'] . "_custom_values." . $field['custom_prefixe'] . "_custom_origine and type_object = " . $this->params["TYPEOBJECT"] . "
                                                   left join " . $field['custom_prefixe'] . "_custom_lists on " . $field['custom_prefixe'] . "_custom_" . $field['datatype'] . " = " . $field['custom_prefixe'] . "_custom_list_value
                                                   where " . $field['custom_prefixe'] . "_custom_lists." . $field['custom_prefixe'] . "_custom_champ ='" . $field['id'] . "' and " . $field['custom_prefixe'] . "_custom_values." . $field['custom_prefixe'] . "_custom_champ ='" . $field['id'] . "' $groupby"
	                    );
	                    break;
	                case 'date_box':
	                    $p_tri = array(
    	                    'SOURCE' => 'cp',
    	                    'TYPEFIELD' => 'select',
    	                    'ID' => 'cp' . $field['id'],
    	                    'TYPE' => 'text',
    	                    'NAME' => $field['name'],
    	                    'LABEL' => translation::get_text($field['id'], 'authperso_custom', 'titre',  $field['label']),
    	                    'TABLEFIELD' => ['value' => 'authperso_custom_' . $field['datatype']],
    	                    'REQ_SUITE' => "left join " . $field['custom_prefixe'] . "_custom_values on " . $this->params["REFERENCE"] . ".num_object = " . $field['custom_prefixe'] . "_custom_values." . $field['custom_prefixe'] . "_custom_origine where " . $field['custom_prefixe'] . "_custom_values." . $field['custom_prefixe'] . "_custom_champ = '" . $field['id'] . "' and type_object = " . $this->params["TYPEOBJECT"]
                        );
	                    break;
	                case 'query_list':
	                    $tableid = '';
	                    $tablefield = '';
	                    $tablename = '';
	                    if ($param['MULTIPLE'][0]['value']) {
	                        if ($param['QUERY'][0]['value']) {
	                            $res = pmb_mysql_query($param['QUERY'][0]['value']);
	                            if ($res) {
	                                $tableid = pmb_mysql_field_name($res, 0);
	                                $tablefield = "group_concat(" . pmb_mysql_field_name($res, 1) . " separator ' ')";
	                                $tablename = pmb_mysql_field_table($res ,0);
	                            }
	                        }
	                        $groupby = 'group by ' . $this->params['REFERENCEKEY'];
	                    } else {
	                        if ($param['QUERY'][0]['value']) {
	                            $res = pmb_mysql_query($param['QUERY'][0]['value']);
	                            if ($res) {
	                                $tableid = pmb_mysql_field_name($res, 0);
	                                $tablefield = pmb_mysql_field_name($res, 1);
	                                $tablename = pmb_mysql_field_table($res, 0);
	                            }
	                        }
	                        $groupby = '';
	                    }

	                    $p_tri = array(
	                        'SOURCE' => 'cp',
	                        'TYPEFIELD' => 'select',
	                        'ID' => 'cp' . $field['id'],
	                        'TYPE' => 'text',
	                        'NAME' => $field['name'],
	                        'LABEL' => translation::get_text($field['id'], 'authperso_custom', 'titre',  $field['label']),
	                        'TABLEFIELD' => ['value' => $tablefield],
	                    	'REQ_SUITE' => "left join " . $field['custom_prefixe'] . "_custom_values on " . $this->params["REFERENCE"] . ".num_object = " . $field['custom_prefixe'] . "_custom_values." . $field['custom_prefixe'] . "_custom_origine and type_object = " . $this->params["TYPEOBJECT"] . "
                                            left join $tablename on " . $field['custom_prefixe'] . "_custom_" . $field['datatype'] . " = $tableid
                                            where " . $field['custom_prefixe'] . "_custom_values." . $field['custom_prefixe'] . "_custom_champ ='" . $field['id'] . "' $groupby"
	                    );
	                    break;
	                case 'query_auth':
	                    $p_tri = array(
    	                    'SOURCE' => 'cp',
    	                    'TYPEFIELD' => 'authority',
    	                    'ID' => 'cp' . $field['id'],
    	                    'TYPE' => 'text',
    	                    'NAME' => $field['name'],
    	                    'LABEL' => translation::get_text($field['id'], 'authperso_custom', 'titre',  $field['label']),
    	                    'REQ_SUITE' => "left join " . $field['custom_prefixe'] . "_custom_values on authorities.num_object = " . $field['custom_prefixe'] . "_custom_values." . $field['custom_prefixe'] . "_custom_origine
    	                                    where " . $field['custom_prefixe'] . "_custom_values." . $field['custom_prefixe'] . "_custom_champ ='" . $field['id'] . "' ",
                            'PREFIX' => 'authperso',
                            'T_FIELD' => $field
	                    );
	                    break;
	                default:
	                    $p_tri = array();
	                    break;
	            }
	            if (!empty($p_tri)) {
	                $this->params['FIELD'][] = $p_tri;
	            }
	        }
	    }
	}

	public function ajoutTriForUniqueRender($trier_par) {
	    switch ($this->dSort->sortName) {
	        case 'notices':
	            if( !in_array('d_num_20', $trier_par) && !in_array('c_num_20', $trier_par) ) {
	                $trier_par[] = 'd_num_20';
	            }
	            break;
	    }
	    return $trier_par;
	}
}