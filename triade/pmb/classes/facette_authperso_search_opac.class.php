<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facette_authperso_search_opac.class.php,v 1.3.10.3 2025/03/28 14:34:15 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path."/facette_search_opac.class.php";

// classes de gestion des facettes sur les autorites perso pour la recherche OPAC
class facette_authperso_search_opac extends facette_search_opac {
	
	protected static $authperso_id = 0;
	
	public function __construct($type='notices', $is_external=false){
	    if (strpos($type, "authperso") !== false) {
	        $authperso =  preg_split("#_([\d]+)#", $type, 0 ,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	        //$type = "authperso";
	        if (!empty($authperso[1]) && intval($authperso[1])) {
	            self::$authperso_id = $authperso[1];
	        }
	    }
		parent::__construct($type, $is_external);
	}
	
	//recuperation de champs_base.xml
	public static function parse_xml_file($type='notices') {
		if(!isset(self::$fields[$type])) {
			$file = static::get_xml_file('authperso');
			$fp=fopen($file,"r");
			if ($fp) {
				$xml=fread($fp,filesize($file));
			}
			fclose($fp);
			$xml = str_replace("!!id_authperso!!", self::$authperso_id, $xml);
			self::$fields[$type] = _parser_text_no_function_($xml,"INDEXATION",$file);
		}
	}
	
	protected function get_prefix_id() {
        return '100'.intval(self::$authperso_id);
	}
	
	protected function get_custom_fields_table() {
		return 'authperso';
	}
	
	public function get_authperso_start() {
	    if($this->get_prefix_id()) {
	        return $this->get_prefix_id().'000';
	    } else {
	        return 1000;
	    }
	}
	
	public function array_subfields($id){
	    $array_subfields = array();
        $authperso_id = substr(substr($id, 0, -3), 3);
		//utile pour l'affichage en liste car on a un singleton de cette classe
		if ($authperso_id && self::$authperso_id != $authperso_id) {
			self::$authperso_id = $authperso_id;
		}
	    if ($id == $this->get_custom_fields_id()) {
	        $query = "SELECT idchamp, CONCAT(authperso_name, ' - ', titre) AS titre 
                    FROM authperso_custom 
                    JOIN authperso ON num_type = id_authperso 
                    ".(!empty($authperso_id) ? "WHERE num_type= ".$authperso_id." " : "")." 
                    ORDER BY titre ASC";
	        $result = pmb_mysql_query($query);
	        while ($row = pmb_mysql_fetch_object($result)) {
	            $array_subfields[$row->idchamp] = $row->titre;
	        }
	    } else {
	        $array_subfields = $this->get_subfields_from_xml($id);
	    }
	    return $array_subfields;
	}
	
	//creation de la liste des criteres principaux en fonction du type d'authperso
	public function create_list_fields($crit=0, $ss_crit=0){
	    if (static::$authperso_id) {
	        return parent::create_list_fields($crit, $ss_crit);
	    }
	    //sinon on charge les champs en fonction de l'authperso selectionnee dans le template (#list_authperso)
	    return "<script>load_authperso_fields(0);";
	}
}

