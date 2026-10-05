<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: external_services.class.php,v 1.30.2.1.2.1 2025/03/07 15:59:18 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/*
==========================================================================================
Comment ça marche toutes ces classes?

        .----------------------------------.
        |             es_base              |
        |----------------------------------| hérite de
        | classe de base, contient le      |<-------------------------.
        | mécanisme des erreurs            |                          |
        '----------------------------------'                          |
héritent de ^             ^ hérite de                  .----------------------------.
            |             |                            |        es_parameter        |
            |             |                     [0..n] |----------------------------|
            |   .------------------.    ^------------->| représente une variable d' |
            |   |    es_method     |    |              | entrée d'une méthode       |
            |   |------------------|    |              '----------------------------'
            |   | représente une   |----.                             ^ hérite de
            |   | méthode de l'API |    |                             |
            |   '------------------'    |                             |
            |             ^             |              .----------------------------.
            |             |             |              |         es_result          |
            |             |             |       [0..n] |----------------------------|
            |     .---------------.     v------------->| représente une variable de |
            |     |   es_group    |                    | retour d'une méthode       |
            |     |---------------|                    '----------------------------'
            '-----| contient des  |
            ^     | méthodes      |
            |     '---------------'
            |             ^
            |             |
            |             |
            |     .---------------.               .-------------------------------.
            |     |  es_catalog   |               |       external_services       |
            |     |---------------|[1]            |-------------------------------|
            '-----| contient des  |<--------------| gère les différentes méthodes |
            ^     | groupes       |               | et génère le proxy associé    |
            |     '---------------'               '-------------------------------'
            |                                                     |
            |-----------------------------------------------------'

==========================================================================================
* */

global $base_path, $class_path, $include_path;
global $lang, $charset, $opac_sur_location_activate;
global $gestion_acces_active, $gestion_acces_empr_notice, $gestion_acces_empr_docnum;

require_once $include_path."/parser.inc.php";
require_once $class_path."/external_services_rights.class.php";
require_once $class_path."/external_services_caches.class.php";
require_once $class_path."/external_services_common.class.php";
require_once $class_path."/acces.class.php";
require_once $include_path."/connecteurs_out_common.inc.php";

define("ES_GROUP_CANNOT_READ_MANIFEST_FILE",1);
define("ES_METHOD_NO_GROUP_DEFINED",2);
define("ES_PARAMETER_UNKNOWN_PARAMETER_TYPE",3);
define("ES_CATALOG_CANNOT_READ_CATALOG_FILE",4);

//Classe de base avec gestion des erreurs
class es_base {
	public $error=false;
	public $error_message="";
	public $description="";

	public function set_error($error_code,$error_message) {
		$this->error=$error_code;
		$this->error_message=$error_message;
	}

	public function copy_error($object) {
		$this->error=$object->error;
		$this->error_message=$object->error_message;
	}

	public function clear_error() {
		$this->error=false;
		$this->error_message="";
	}

	public function has_error() {
	    return $this->error;
	}

	public function get_error_message() {
	    return $this->error_message;
	}
}

//Paramètre d'une fonction
class es_parameter extends es_base {
	public $name="";
	public $type="scalar";
	public $datatype="string";
	public $nodename="PARAM";
	public $optional=false;
	public $default_value;

	//Pour les paramètres structure : un tableau de type es_parametre;
	public $struct=array();

	//Constructeur
	public function __construct($param = array()) {
	    if (is_array($param)) {
			$this->name=$param["NAME"];
			if(isset($param["DATATYPE"])) $this->datatype=$param["DATATYPE"];
			if(isset($param["OPTIONAL"])) $this->optional=$param["OPTIONAL"];
			if(isset($param["DEFAULT_VALUE"])) $this->default_value=$param["DEFAULT_VALUE"];

			//Selon le type (param ou result), ça change
			$classname = get_class($this);
			switch($param["TYPE"]) {
				case "scalar":
					break;
				case "array":
					if(isset($param[$this->nodename])) {
						for ($i=0; $i<count($param[$this->nodename]); $i++) {
							$parametre=$param[$this->nodename][$i];
							$p=new $classname($parametre);
							if (!$p->error)
								$this->struct[]=$p;
							else {
								$this->copy_error($p);
								return;
							}
						}
					}
					break;
				case "structure":
					if(isset($param[$this->nodename])) {
						for ($i=0; $i<count($param[$this->nodename]); $i++) {
							$parametre=$param[$this->nodename][$i];
							$p=new $classname($parametre);
							if (!$p->error)
								$this->struct[]=$p;
							else {
								$this->copy_error($p);
								return;
							}
						}
					}
					break;
				default:
					$this->set_error(ES_PARAMETER_UNKNOWN_PARAMETER_TYPE,"Type de paramètre inconnu");
					return;
			}
			$this->type=$param["TYPE"];
		}
	}
}

//Résultat d'une fonction
class es_result extends es_parameter {
	public $nodename="RESULT";
}

//Composant d'un type
class es_part extends es_parameter {
	public $nodename="PART";
}

//Un type
class es_type extends es_base {
	public $name='';
	public $description='';
	public $imported=false;
	public $imported_from = "";
	public $struct=array();
	public $type="structure";

	public function __construct($type) {
		if (isset($type["IMPORTED"]) && $type["IMPORTED"]) {
			$this->name = $type["NAME"];
			$this->imported = true;
			$this->imported_from = $type["IMPORTED_FROM"];
			return;
		}
		if (isset($type["DESCRIPTION"][0]["value"]))
			$this->description=$type["DESCRIPTION"][0]["value"];
		$this->name = $type["NAME"];
		if (isset($type["PART"])) {
			foreach ($type["PART"] as $part) {
				$part_object = new es_part($part);
				$this->struct[] = $part_object;
			}
		}
	}
}

//Référence d'une methode dans une autre méthode
class es_requirement extends es_base {
	public $group="";
	public $name="";
	public $version="";

	//Constructeur
	public function __construct($param = array()) {
		if (is_array($param) && !empty($param)) {
			$this->group=$param["GROUP"];
			$this->name=$param["NAME"];
			$this->version=$param["VERSION"];
		}
	}

    public function __toString() {
        return $this->group.'_'.$this->name;
    }
}

//Requirement d'une methode
class es_pmb_requirement extends es_base {
	public $start_path="";
	public $file="";

	//Constructeur
	public function __construct($param = array()) {
		if (is_array($param) && !empty($param)) {
			$this->start_path=$param["START_PATH"];
			$this->file=$param["FILE"];
		}
	}

	//Permet de pouvoir comparer deux instances, dans un array_unique par exemple
    public function __toString() {
        return $this->start_path.'___'.$this->file;
    }

}

//Méthode
class es_method extends es_base {
	public $group;
	public $name;
	//Tableau de es_params
	public $inputs=array();
	//Tableau de es_results
	public $outputs=array();
	//Descriptions
	public $description="";
	public $input_description="";
	public $output_description="";
	//Droits pour cette méthode
	public $rights=0;
	//Numéro de version de cette méthode
	public $version=0;
	//méthodes nécessaires pour executer cette méthode
	public $requirements=array();
	public $recurvised_requirement_list=array();
	//require_once nécessaires pour executer cette méthode
	public $pmb_file_requirements=array();
	//Défini si la méthode a besoin des messages localisés
	public $language_independant=false;

	public function __construct($method = array(), $group="") {
		if (is_array($method) && !empty($method)) {
			if (!$group) {
				$this->set_error(ES_METHOD_NO_GROUP_DEFINED,"No group defined");
				return;
			}
			//Analyse du tableau
			$this->group=$group;
			$this->name=$method["NAME"];
			if(isset($method["COMMENT"])) {
				$this->description=$method["COMMENT"];
			}
			$this->version=$method["VERSION"];
			if (isset($method["RIGHTS"]) && $method["RIGHTS"]) {
				$rights=explode("|",$method["RIGHTS"]);
				for ($i=0; $i<count($rights); $i++) {
					$this->rights|=constant($rights[$i]);
				}
			}
			if (isset($method["LANGUAGE_INDEPENDANT"]) && $method["LANGUAGE_INDEPENDANT"] == 'true')
				$this->language_independant = true;
			//Lecture des inputs
			if(isset($method["INPUTS"][0]["DESCRIPTION"][0]["value"])) {
				$this->input_description=$method["INPUTS"][0]["DESCRIPTION"][0]["value"];
			}
			if(isset($method["INPUTS"][0]["PARAM"])) {
				for ($i=0; $i<count($method["INPUTS"][0]["PARAM"]); $i++) {
					$parameter=$method["INPUTS"][0]["PARAM"][$i];
					$p=new es_parameter($parameter);
					if (!$p->error)
						$this->inputs[]=$p;
					else {
						$this->copy_error($p);
						return;
					}
				}
			}
			//Lecture des outputs
			if(isset($method["OUTPUTS"][0]["DESCRIPTION"][0]["value"])) {
				$this->output_description=$method["OUTPUTS"][0]["DESCRIPTION"][0]["value"];
			}
			if(isset($method["OUTPUTS"][0]["RESULT"])) {
				for ($i=0; $i<count($method["OUTPUTS"][0]["RESULT"]); $i++) {
					$result=$method["OUTPUTS"][0]["RESULT"][$i];
					$r=new es_result($result);
					if (!$r->error)
						$this->outputs[]=$r;
					else {
						$this->copy_error($r);
						return;
					}
				}
			}

			//Lecture des requirements
			if (isset($method["REQUIREMENTS"][0]["REQUIREMENT"])) {
				for ($i=0; $i<count($method["REQUIREMENTS"][0]["REQUIREMENT"]); $i++) {
					$result=$method["REQUIREMENTS"][0]["REQUIREMENT"][$i];
					$r=new es_requirement($result);
					if (!$r->error) {
						$this->requirements[]=$r;
						$this->recurvised_requirement_list[] = $r->__toString();
					}
					else {
						$this->copy_error($r);
						return;
					}
				}
			}

			//Lecture des pmb_requirements
			if (isset($method["PMB_REQUIREMENTS"][0]["PMB_REQUIREMENT"])) {
				for ($i=0; $i<count($method["PMB_REQUIREMENTS"][0]["PMB_REQUIREMENT"]); $i++) {
					$result=$method["PMB_REQUIREMENTS"][0]["PMB_REQUIREMENT"][$i];
					$r=new es_pmb_requirement($result);
					if (!$r->error)
						$this->pmb_file_requirements[]=$r;
					else {
						$this->copy_error($r);
						return;
					}
				}
			}
		}
	}
}

//Classe représentant un groupe de fonctions
class es_group extends es_base {
	public $name;
	//Tableau de es_type
	public $types=array();
	//Tableau de es_methods
	public $methods=array();
	//Identifiant unique du groupe
	public $id="";
	//Description
	public $description="";
	//Tableau des messages du groupe
	public $msg=array();

	private static $_parsed_methods;
	private static $instances;

	public function __construct($group_name,$id) {
		global $base_path,$lang;
		//Lecture des propriétés du fichier manifest
		$xml=@file_get_contents($base_path."/external_services/$group_name/manifest.xml");
		if (!$xml) {
			$this->set_error(ES_GROUP_CANNOT_READ_MANIFEST_FILE,"Can't read manifest file");
			return;
		}

		$this->name=$group_name;
		$this->id=$id;

		if(!isset(static::$_parsed_methods[$group_name])) {
			//Parse du fichier
			static::$_parsed_methods[$group_name] = _parser_text_no_function_($xml,"MANIFEST");
		}
		$methods = static::$_parsed_methods[$group_name];

		if(isset($methods["DESCRIPTION"][0]["value"])) {
			$this->description=$methods["DESCRIPTION"][0]["value"];
		}

		//Pour chaque type, on instancie sa représentation
		if (isset($methods["TYPES"][0]["TYPE"])) {
			foreach ($methods["TYPES"][0]["TYPE"] as $atype) {
				$t = new es_type($atype);
				if (!$t->error)
					$this->types[$t->name] = $t;
				else {
					$this->copy_error($t);
					return;
				}
			}
		}

		//Pour chaque méthode, on instancie sa représentation
		if(isset($methods["METHODS"][0]["METHOD"])) {
			for ($i=0; $i<count($methods["METHODS"][0]["METHOD"]); $i++) {
				$method=$methods["METHODS"][0]["METHOD"][$i];
				$m=new es_method($method,$this->name);
				if (!$m->error)
					$this->methods[$m->name]=$m;
				else {
					$this->copy_error($m);
					return;
				}
			}
		}

		//Lecture du fichier des messages
		if (!file_exists($base_path."/external_services/$group_name/messages/$lang.xml")) $tlang="fr_FR"; else $tlang=$lang;
		if (file_exists($base_path."/external_services/$group_name/messages/$tlang.xml")) {
			$msg_list=new XMLlist($base_path."/external_services/$group_name/messages/$tlang.xml");
			$msg_list->analyser();
			$this->msg=$msg_list->table;
		}
	}

	public static function get_instance($group_name,$id) {
		if(!isset(static::$instances[$group_name][$id])) {
			static::$instances[$group_name][$id] = new es_group($group_name, $id);
		}
		return static::$instances[$group_name][$id];
	}
}

//Classe de lecture du catalogue
class es_catalog extends es_base {

	public $groups; //Tableau de groupes

	public $recursive_depth;

	private static $_parsed_catalog;
	private static $instance;

	public function __construct() {
		global $base_path;
		if(!isset(static::$_parsed_catalog)) {
			if (file_exists($base_path."/external_services/catalog_subst.xml")) {
				$catalog_file=$base_path."/external_services/catalog_subst.xml";
			} else {
				$catalog_file=$base_path."/external_services/catalog.xml";
			}
			$xml=@file_get_contents($catalog_file);
			if (!$xml) {
				$this->set_error(ES_CATALOG_CANNOT_READ_CATALOG_FILE,"Fichier catalog introuvable");
				return;
			}
			static::$_parsed_catalog = _parser_text_no_function_($xml,"CATALOG");
		}

		//Dépouillement du résultat
		for ($i=0; $i<count(static::$_parsed_catalog["ITEM"]);$i++) {
			$g=es_group::get_instance(static::$_parsed_catalog["ITEM"][$i]["NAME"],static::$_parsed_catalog["ITEM"][$i]["ID"]);
			if (!$g->error)
				$this->groups[$g->name]=$g;
			else {
				$this->copy_error($g);
			}
		}

		//Construit la liste des dépendances des fichiers php dont les méthodes ont besoin (exemple: $class_path/acces.class.php)
		$this->recursive_depth = 0;
		$this->create_requirements_lists();

		//Construit la liste des dépendandes des autres méthodes dont les méthodes ont besoin.
		$this->recursive_depth = 0;
		$this->fix_imported_pmb_requirements();
	}

	public function fix_imported_pmb_requirement(&$amethod) {
		if ($this->recursive_depth > 5) //Faut pas pousser mémé dans les orties: évitons une recursion infinie.
			return;
		$this->recursive_depth++;
		if ($amethod->requirements) {
			foreach ($amethod->requirements as &$arequirement) {
				if (isset($this->groups[$arequirement->group]->methods[$arequirement->name])) {
					$this->fix_imported_pmb_requirement($this->groups[$arequirement->group]->methods[$arequirement->name]);
				}
				if (!isset($this->groups[$arequirement->group]->methods[$arequirement->name]->pmb_file_requirements) || !$this->groups[$arequirement->group]->methods[$arequirement->name]->pmb_file_requirements)
					continue;
				$amethod->pmb_file_requirements = array_merge($amethod->pmb_file_requirements, $this->groups[$arequirement->group]->methods[$arequirement->name]->pmb_file_requirements);
				$amethod->pmb_file_requirements = array_unique($amethod->pmb_file_requirements);
			}
		}
		$this->recursive_depth--;
	}

	public function fix_imported_pmb_requirements() {
		foreach ($this->groups as &$agroup) {
			foreach ($agroup->methods as &$amethod) {
				$this->fix_imported_pmb_requirement($amethod);
			}
		}
	}

	public function create_requirements_list(&$amethod) {
		if ($this->recursive_depth > 5) //Faut pas pousser mémé dans les orties: évitons une recursion infinie.
			return;
		$this->recursive_depth++;
		if ($amethod->requirements) {
			foreach ($amethod->requirements as &$arequirement) {
				if (isset($this->groups[$arequirement->group]->methods[$arequirement->name])) {
					$this->create_requirements_list($this->groups[$arequirement->group]->methods[$arequirement->name]);
				}
				if (!isset($this->groups[$arequirement->group]->methods[$arequirement->name]->recurvised_requirement_list) || !$this->groups[$arequirement->group]->methods[$arequirement->name]->recurvised_requirement_list)
					continue;
				$amethod->recurvised_requirement_list = array_merge($amethod->recurvised_requirement_list, $this->groups[$arequirement->group]->methods[$arequirement->name]->recurvised_requirement_list);
				$amethod->recurvised_requirement_list = array_unique($amethod->recurvised_requirement_list);
			}
		}
		$this->recursive_depth--;
	}

	public function create_requirements_lists() {
		foreach ($this->groups as &$agroup) {
			foreach ($agroup->methods as &$amethod) {
				$this->create_requirements_list($amethod);
			}
		}
	}

	public static function get_instance() {
		if(!isset(static::$instance)) {
			static::$instance = new es_catalog();
		}
		return static::$instance;
	}

}

class external_services_api_class {
	protected  $proxy_parent=NULL;
	protected $msg=array();
	protected $es = NULL;
	public $error=false;		//Y-a-t-il eu une erreur
	public $error_message="";	//Message correspondant à l'erreur

	/**
	 *
	 * @param external_services $external_services
	 * @param string $group_name
	 * @param es_proxy $proxy_parent
	 */
	public function __construct($external_services, $group_name, &$proxy_parent) {
		$this->proxy_parent = &$proxy_parent;
		$this->es=$external_services;
		$this->msg=$this->es->msg($group_name);
		$this->merge_msg();
	}

	public function restore_general_config() {

	}

	public function form_general_config() {
		return false;
	}

	public function save_general_config() {

	}

	// Permet de surcharger les messages avec ceux du web services, utile pour bibloto par exeemple
	public function merge_msg() {
	   /*
	    global $msg;

	    foreach ($this->msg as $key => $val) {
	        $msg[$key] = $val;
	    }
	    */
	}

	/**
	 * Filtrage du tableau des Id de notices pour la visibilité des notices
	 * si for=exemplaire -> on filtre les notices pour la visibilté des exemplaires
	 * si for=docnum -> on filtre les notices pour la visibilté des documents numériques
	 *
	 * @param array $notice_ids : tableau d'ids de notices
	 * @param string $for : notices|exemplaire|docnum
	 * @return array
	 */
	public function filter_tabl_notices($notice_ids, $for = "notices")
	{
	    if (! is_array($notice_ids) || empty($notice_ids)) {
	        return [];
	    }

	    // Suppression ids non valables
	    $tmp_notice_ids = $notice_ids;
	    $notice_ids = [];
	    foreach($tmp_notice_ids as $notice_id) {
	        if(intval($notice_id) > 0) {
	            $notice_ids[] = $notice_id;
	        }
	    }
	    unset($tmp_notice_ids);

	    // Appel en gestion >> Pas de filtre
	    if (! $this->proxy_parent->isOPAC) {
	        return $notice_ids;
	    }

	    if ($for == "exemplaire") {
	        $mask = 8;
	        $filter = " ((expl_visible_opac=1 and expl_visible_opac_abon=0)" . ($this->proxy_parent->idEmpr ? " or (expl_visible_opac_abon=1 and expl_visible_opac=1)" : "") . ")";
	        $filter .= " AND ((notice_visible_opac=1 and notice_visible_opac_abon=0)" . ($this->proxy_parent->idEmpr ? " or (notice_visible_opac_abon=1 and notice_visible_opac=1)" : "") . ")"; // Je dois aussi avoir le droit de voir les notices
	    } elseif ($for == "docnum") {
	        $mask = 16;
	        $filter = " ((explnum_visible_opac=1 and explnum_visible_opac_abon=0)" . ($this->proxy_parent->idEmpr ? " or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)" : "") . ")";
	        $filter .= " AND ((notice_visible_opac=1 and notice_visible_opac_abon=0)" . ($this->proxy_parent->idEmpr ? " or (notice_visible_opac_abon=1 and notice_visible_opac=1)" : "") . ")"; // Je dois aussi avoir le droit de voir les notices
	    } else {
	        $mask = 4;
	        $filter = " ((notice_visible_opac=1 and notice_visible_opac_abon=0)" . ($this->proxy_parent->idEmpr ? " or (notice_visible_opac_abon=1 and notice_visible_opac=1)" : "") . ")";
	    }

	    $result = [];
	    // Vérifions que l'emprunteur a bien le droit de voir les notices
	    global $gestion_acces_active, $gestion_acces_empr_notice;

	    if ($gestion_acces_active == 1 && $gestion_acces_empr_notice == 1) {

	        //Gestion des droit d'acces empr-notice activee
	        $ac = new acces();
	        $dom_2 = $ac->setDomain(2);
	        foreach ($notice_ids as $anoticeid) {
	            $rights = $dom_2->getRights($this->proxy_parent->idEmpr, $anoticeid);
	            if ( $rights & $mask) {
	                $result[] = $anoticeid;
	            }
	        }

	    } else {

	        // On contrôle les statuts de notices si pas de gestion de droit
	        $where = "notice_id in (" . implode(",", $notice_ids) . ") AND";
	        $query = "select distinct notice_id from notices join notice_statut on notices.statut= id_notice_statut where " . $where . " " . $filter;
	        $res = pmb_mysql_query($query);
	        if (pmb_mysql_num_rows($res)) {
	            while ($ligne = pmb_mysql_fetch_object($res)) {
	                $result[] = $ligne->notice_id;
	            }
	        }
	    }
	    return $result;
	}

	//Filtrage du tableau des Id de bulletins pour la visibilité
	//Pour les droits sur les bulletins c'est la notice de périodique qui les définis
	//si for=exemplaire -> on filtre les bulletins pour la visibilté des exemplaires
	//si for=docnum -> on filtre les bulletins pour la visibilté des documents numériques
	public function filter_tabl_bulletins($tabl_bulletins,$for="notices"){

		$requete="SELECT DISTINCT bulletin_notice FROM bulletins WHERE bulletin_id IN (".implode(",",$tabl_bulletins).")";
		$res=pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($res)){
			$notice_ids=array();
			while ($ligne=pmb_mysql_fetch_object($res)) {
				$notice_ids[]=$ligne->bulletin_notice;
			}
			$notice_ids=$this->filter_tabl_notices($notice_ids,$for);
			if(count($notice_ids)){
				$requete="SELECT DISTINCT bulletin_id FROM bulletins WHERE bulletin_id IN (".implode(",",$tabl_bulletins).") AND bulletin_notice IN (".implode(",",$notice_ids).")";
				$res=pmb_mysql_query($requete);
				$tabl_bulletins=array();
				if(pmb_mysql_num_rows($res)){
					while ($ligne=pmb_mysql_fetch_object($res)) {
						$tabl_bulletins[]=$ligne->bulletin_id;
					}
				}else{
					//Je ne peux normalement pas passer par ici
					return array();
				}
			}else{
				//Je n'ai plus de notices de périodique après filtrage donc je ne dois pas voir de bulletin
				return array();
			}
		}else{
			//Comme un bulletin à forcément un périodique je ne dois pas passer par là
			return array();
		}
		return $tabl_bulletins;
	}

	//Permet de savoir si un exemplaire est visible ou non
	public function expl_visible_opac($expl_id){
		$visible=true;
		if($this->proxy_parent->isOPAC){
			global $opac_sur_location_activate;
			$opac_sur_location_join="";
			if($opac_sur_location_activate){
				$opac_sur_location_join=" JOIN sur_location ON docs_location.surloc_num=sur_location.surloc_id AND surloc_visible_opac=1 ";
			}
			$requete="SELECT expl_id FROM exemplaires JOIN docs_location ON expl_location=idlocation JOIN docs_section ON expl_section=idsection JOIN docs_statut ON expl_statut=idstatut $opac_sur_location_join WHERE ";
			$requete.=" expl_id='".$expl_id."' ";
			$requete.=" AND location_visible_opac=1 AND section_visible_opac=1 AND statut_visible_opac=1 ";
			$res=pmb_mysql_query($requete);
			if(!pmb_mysql_num_rows($res)){
				$visible=false;
			}
		}
		return $visible;
	}

	//Filtrage sur les documents numériques
	public function filter_tabl_explnum(){
		global $gestion_acces_active, $gestion_acces_empr_docnum;

		$filter = "";
		if($this->proxy_parent->isOPAC){
			//Vérifions que l'emprunteur a bien le droit de voir les documents numériques
			if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
				$ac= new acces();
				$dom_3= $ac->setDomain(3);
				$filter = $dom_3->getJoin($this->proxy_parent->idEmpr,16,"explnum_id");
			}else{
				//On contrôle les statuts de documents numériques si pas de gestion de droit
				$filter="join explnum_statut on explnum_docnum_statut=id_explnum_statut and (explnum_visible_opac=1 and explnum_visible_opac_abon=0)".($this->proxy_parent->idEmpr?" or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)":"");
			}
		}
		return $filter;
	}

	public function clear_error() {
	    $this->error = false;
	    $this->error_message = '';
	}

	public function set_error($error_message) {
	    $this->error = true;
	    $this->error_message = $error_message;
	}

	public function has_user_rights($module_auth) {
	    if (SESSrights & $module_auth) {
	        return true;
	    }
	    return false;
	}
}

//Classe qui implémente les fonctions externes
class external_services extends es_base {
	public $msg=array();
	public $catalog;
	public $proxy;	//Classe regroupant toutes les fonctions

	//Constructeur
	public function __construct($allow_caching=false) {
		if ($allow_caching) {
			$es_cache = new external_services_cache('es_cache_blob', 86400);

			//Vérifions que le catalogue xml n'a pas changé avant de chercher dans le cache
			$situation = $this->compute_situation_catalog_identity();
			$old_situation = $es_cache->decache_single_object('external_service_catalog_situation', CACHE_TYPE_MISC);
			if ($old_situation == $situation) {
				$cached_result = $es_cache->decache_single_object('external_service_catalog', CACHE_TYPE_MISC);
				if ($cached_result !== false) {
					$cached_result = unserialize(base64_decode($cached_result));
					$this->catalog = $cached_result;
				}
			}
		}

		if (!$this->catalog) {
			//Parse des bibliothèques disponibles
			$this->catalog=es_catalog::get_instance();
			if ($this->catalog->error) {
				$this->copy_error($this->catalog);
				return;
			}

			if ($allow_caching) {
				//Mettons le catalogue dans le cache
				$es_cache = new external_services_cache('es_cache_blob', 86400);
				$es_cache->encache_single_object('external_service_catalog', CACHE_TYPE_MISC, base64_encode(serialize($this->catalog)));
				$es_cache->encache_single_object('external_service_catalog_situation', CACHE_TYPE_MISC, $situation);
			}
		}

	}

	public function compute_situation_catalog_identity() {
		global $base_path;
		if (file_exists($base_path."/external_services/catalog_subst.xml"))
			$catalog_file=$base_path."/external_services/catalog_subst.xml";
		else
			$catalog_file=$base_path."/external_services/catalog.xml";

		$xml=@file_get_contents($catalog_file);

		if (!$xml) {
			return "";
		}

		$catalog=_parser_text_no_function_($xml,"CATALOG");

		//Dépouillement du résultat
		$identity = "";
		for ($i=0; $i<count($catalog["ITEM"]);$i++) {
			$identity .= $catalog["ITEM"][$i]["NAME"].filemtime($base_path."/external_services/".$catalog["ITEM"][$i]["NAME"]."/manifest.xml")."_";
		}
		$identity = md5($identity);
		return $identity;
	}

	public function get_proxy($user, $restrict_use_to_function_list=array()) {
		if ($this->proxy) return $this->proxy;
		$proxy_desc=array();
		$rights=new external_services_rights($this);
		$proxy_func = '';
		$proxy_require = '';
		$proxy="
class es_proxy extends es_base {
	public \$es;
	public \$user".($user?"=$user":"").";
	public \$isOPAC=false;
	public \$idEmpr=0;
	public \$description=\"\";
	public \$error_callback_function=NULL;
	public \$input_charset='utf-8';
";

		$proxy_method_requires = "";

		$proxy_err_calback_set = "

	public function set_error_callback(\$callback_function) {
		\$this->error_callback_function = \$callback_function;
	}
		";

		$proxy_init="

	public function init() {";

		//Si on nous soumet une liste de fonctions, il ne faut pas oublier les éventuelles dépendances de celles-ci.
		if ($restrict_use_to_function_list) {
			 $restrict_use_to_function_list_requirements = array();
			 foreach ($this->catalog->groups as $group_name=>$es_group) {
				foreach ($es_group->methods as $method_name=>$es_method) {
					if ($restrict_use_to_function_list && !in_array($group_name.'_'.$method_name, $restrict_use_to_function_list))
						continue;
					if (!$es_method->recurvised_requirement_list)
						continue;
					$restrict_use_to_function_list_requirements = array_merge($restrict_use_to_function_list_requirements, $es_method->recurvised_requirement_list);
				}
			 }
			if (!$restrict_use_to_function_list_requirements)
				$restrict_use_to_function_list_requirements = array();
			$restrict_use_to_function_list = array_merge($restrict_use_to_function_list, $restrict_use_to_function_list_requirements);
			$restrict_use_to_function_list = array_unique($restrict_use_to_function_list);
		}

		$pmb_file_requirements = array();

		//Création dess variables des classes correspondantes aux groupes
		foreach ($this->catalog->groups as $group_name=>$es_group) {
			//Création des fonctions
			$group_has_method=false;
			$methods_desc=array();
			foreach ($es_group->methods as $method_name=>$es_method) {
				if ($restrict_use_to_function_list && !in_array($group_name.'_'.$method_name, $restrict_use_to_function_list))
					continue;

				if (!$es_method->pmb_file_requirements)
					$es_method->pmb_file_requirements = array();
				$pmb_file_requirements = array_merge($pmb_file_requirements, $es_method->pmb_file_requirements);

				//Les droits sont-ils là ?
				if ($rights->has_rights($user,$group_name,$method_name)) {

					//Construction des paramètres de la méthode
					$params=array();
					$params_call = array();
					for ($i=0; $i<count($es_method->inputs); $i++) {
					    $optional = (!empty($es_method->inputs[$i]->optional) && $es_method->inputs[$i]->optional === "yes");
					    if (!isset($es_method->inputs[$i]->default_value)) {
					    	$es_method->inputs[$i]->default_value = "''";
					    }
					    $params_call[] ="\$".$es_method->inputs[$i]->name;
					    $params[] = "\$".$es_method->inputs[$i]->name.($optional === true ? "=" . $es_method->inputs[$i]->default_value : "");
					}
					$group_has_method=true;
					$proxy_func.="
	public function ".$group_name."_".$method_name."(".implode(",",$params).") {
		try {
		\$result =  \$this->".$group_name."->".$method_name."(".implode(",",$params_call).");
		} catch(Exception \$e) {
			if (\$this->error_callback_function)
				call_user_func(\$this->error_callback_function, \$e);
		}
		return \$result;
	}
    public function ".$group_name."_".$method_name."_hasError() {
		return \$this->".$group_name."->error;
	}
    public function ".$group_name."_".$method_name."_errorMessage() {
		return \$this->".$group_name."->error_message;
	}
";
					$mdesc=array();
					$mdesc["name"]=$method_name;
					$mdesc["description"]=$this->get_text($es_method->description,$group_name);
					$mdesc["inputs_description"]=$this->get_text($es_method->input_description,$group_name);
					$mdesc["outputs_description"]=$this->get_text($es_method->output_description,$group_name);
					$methods_desc[]=$mdesc;
				}
			}
			if ($group_has_method) {
				//Fonction d'initialisation
				$proxy_init.="
		\$this->".$group_name."=new ".$group_name."(\$this->es, '".$group_name."', \$this);";

				//Variable pour la classe du groupe
				$proxy.="
	public \$".$group_name.";";

				//Require pour le groupe
				$proxy_require.="require_once(\$base_path.\"/external_services/".$group_name."/".$group_name.".class.php\");
";
				//Description du groupe
				$gdesc=array();
				$gdesc["name"]=$group_name;
				$gdesc["description"]=$this->get_text($es_group->description,$group_name);
				$gdesc["methods"]=$methods_desc;
				$proxy_desc[]=$gdesc;
			}
		}

		$pmb_file_requirements = array_unique($pmb_file_requirements);
		$name_variable_correspondance = array(
			"class" => '$class_path',
			"base" => '$base_path',
			"include" => '$include_path'
		);
		foreach ($pmb_file_requirements as $arequirement) {
			if (!$name_variable_correspondance[$arequirement->start_path])
				continue;
			$proxy_method_requires .= 'require_once("'.$name_variable_correspondance[$arequirement->start_path].'/'.$arequirement->file.'");'."\n";
		}

		$proxy_init.="
	}
";
		$proxy_end="
	public function __construct(\$external_services) {
		\$this->es=\$external_services;
		\$this->init();
	}
}
";
		//Instanciation de la classe proxy !
		$proxy=$proxy_method_requires.$proxy_require.$proxy.$proxy_init.$proxy_err_calback_set.$proxy_func.$proxy_end;

		//Restauration de l'environnement global
		foreach ($GLOBALS as $var_name=>$value) {
			global ${$var_name};
		}

		//Enregistrons le nom des variables qui existent déjà avant l'eval
		$before_eval_vars = get_defined_vars();

		try {
			//error_reporting(E_ALL);
			$re =eval("try { $proxy } catch (Exception \$e) { }");
		} catch (Exception $e) {
			//print $e->getMessage();
		}
		$this->proxy=new es_proxy($this);
		//Affectation des descriptions
		$this->proxy->description=$proxy_desc;
		//Affectation du charset
		global $charset;
		$this->proxy->input_charset = $charset;

		//Maintenant nous avons sortir toutes les variables globales générée par l'eval du contexte de la fonction
		$function_variable_names = array("function_variable_names" => 0, "before_eval_vars" => 0, "created" => 0);
		$created = array_diff_key(get_defined_vars(), $GLOBALS, $function_variable_names, $before_eval_vars);
		foreach ($created as $created_name => $on_sen_fiche)
			global ${$created_name};
		extract($created);

		return $this->proxy;
	}

	public function get_group_list() {
		$r=array();
		foreach ($this->catalog->groups as $group_name=>$group) {
			$t=array();
			$t["name"]=$group_name;
			$t["description"]=$this->get_text($group->description,$group_name);
			foreach ($group->methods as $method_name=>$method) {
				$m=array();
				$m["name"]=$method_name;
				$m["description"]=$this->get_text($method->description,$group_name);
				$m["inputs_description"]=$this->get_text($method->input_description,$group_name);
				$m["outputs_description"]=$this->get_text($method->output_description,$group_name);
				$t["methods"][]=$m;
			}
			$r[]=$t;
		}
		return $r;
	}

	public function group_exists($group) {
		if (is_object($this->catalog->groups[$group])) return true; else return false;
	}

	public function method_exists($group,$method) {
		if ($this->group_exists($group)) {
		    if (!empty($this->catalog->groups[$group]->methods[$method]) && is_object($this->catalog->groups[$group]->methods[$method])) {
		        return true;
		    }
		}
		return false;
	}

	public function save_persistent($group_name,$uniqueid,$message) {
		//Sauvegarde de manière persistente
		$requete="insert into external_persist (group_name,uniqueid,message) values('".addslashes($group_name)."','".addslashes($uniqueid)."','".addslashes($message)."')";
		$r=pmb_mysql_query($requete);
		if ($r) return true; else return false;
	}

	public function msg($group_name) {
		return $this->catalog->groups[$group_name]->msg;
	}

	public function get_text($text,$group_name) {
		if (substr($text,0,4)=="msg:") {
			$lmsg=$this->msg($group_name);
			return (isset($lmsg[substr($text,4)]) ? $lmsg[substr($text,4)] : '');
		} else return $text;
	}

	public function operation_need_messages($operation) {
		foreach ($this->catalog->groups as &$agroup) {
			foreach ($agroup->methods as &$amethod) {
				if ($operation == $agroup->name."_".$amethod->name) {
					return !$amethod->language_independant;
				}
			}
		}
	}
}