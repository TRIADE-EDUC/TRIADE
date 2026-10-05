<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.class.php,v 1.474.2.9.2.11 2025/05/22 07:58:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
//Classe de gestion des recherches avancees

use Pmb\Searchform\Views\SearchformView;
use Pmb\Common\Library\Navbar\Navbar;

global $class_path, $include_path;
global $pmb_map_activate, $pmb_allow_external_search, $pmb_extended_search_dnd_interface;

require_once($include_path."/parser.inc.php");
require_once($class_path."/parametres_perso.class.php");
require_once($include_path."/templates/search.tpl.php");
require_once($class_path."/analyse_query.class.php");
require_once($class_path."/sort.class.php");
require_once($class_path."/acces.class.php");
require_once($include_path."/isbn.inc.php");
require_once($class_path."/fiche.class.php");

//pour les autorités
require_once($class_path."/author.class.php");
require_once($class_path."/categories.class.php");
require_once($class_path."/editor.class.php");
require_once($class_path."/collection.class.php");
require_once($class_path."/subcollection.class.php");
require_once($class_path."/serie.class.php");
require_once($class_path."/titre_uniforme.class.php");
require_once($class_path."/indexint.class.php");
require_once($class_path."/authperso.class.php");
require_once($class_path."/map/map_search_controler.class.php");
require_once($class_path."/entites.class.php");
require_once($class_path.'/vedette/vedette_composee.class.php');
require_once($class_path.'/onto/common/onto_common_uri.class.php');

if(!isset($pmb_map_activate)) $pmb_map_activate = 0;
if($pmb_map_activate){
	require_once($class_path."/map/map_search_controler.class.php");
}

if(!isset($pmb_allow_external_search)) $pmb_allow_external_search = 0;
if ($pmb_allow_external_search && (defined('SESSrights') && SESSrights & ADMINISTRATION_AUTH) && isset($id_connector_set))
	require_once($class_path."/connecteurs_out_sets.class.php");

if(!isset($pmb_extended_search_dnd_interface)) $pmb_extended_search_dnd_interface = 0;
if ($pmb_extended_search_dnd_interface) {
	require_once($include_path."/templates/extended_search_dnd.tpl.php");
}
require_once($class_path."/facettes.class.php");
require_once($class_path."/facettes_external.class.php");
require_once($class_path."/facettes_external_search_compare.class.php");
require_once($class_path."/cache_factory.class.php");
require_once($class_path."/searcher/searcher_factory.class.php");
require_once($class_path.'/elements_list/elements_records_list_ui.class.php');
require_once($class_path.'/misc/files/misc_file_search_fields.class.php');
require_once($class_path."/translation.class.php");

class mterm {
	public $sc_type;
	public	$uid;			//Identifiant du champ
	public	$ufield;		//Nom du champ UNIMARC
	public $op;			//Operateur
	public $values;		//Liste des valeurs (tableau)
	public $vars;			//Valeurs annexes
	public $sub;			//sous liste de termes (tableau)
	public $inter;			//operateur entre ce terme et le precedent

	public function __construct($ufield,$op,$values,$vars,$inter,$uid="") {
		$this->uid = $uid;
		$this->ufield=$ufield;
		$this->op=$op;
		$this->values=$values;
		$this->vars=$vars;
		$this->inter=$inter;
	}

	public function set_sub($sub) {
		$this->sub=$sub;
	}
}

class search {

	public $operators;
	public $op_empty;
	public $op_special;
	public $fixedfields;
	public $dynamicfields;
	public $dynamicfields_order;
	public $dynamicfields_hidebycustomname;
	public $specialfields;
	public $pp;
	public $error_message;
	public $link;
	public $link_expl;
	public $link_expl_bull;
	public $link_explnum;
	public $link_serial;
	public $link_analysis;
	public $link_bulletin;
	public $link_explnum_serial;
	public $link_explnum_analysis;
	public $link_explnum_bulletin;
	public $rec_history;
	public $tableau_speciaux;
	public $operator_multi_value;
	public $full_path='';
	public $fichier_xml;

	public $dynamics_not_visible;
	public $specials_not_visible;

	public $isfichier = false;
	public $memory_engine_allowed = false;
	public $current_engine;
	public $authpersos = array();
	public $groups_used = false;
	public $groups = array();
	public $filtered_objects_types = array();
	public $keyName = "";
	public $tableName = "";

	public $limited_search = 0;
	protected $is_created_temporary_table = false;
	public static $ignore_subst_file = false;
	protected $list_criteria;

	/**
	 * Script à appeler au chargement de la page (ne sert qu'à transmettre l'info au show_form())
	 * @var string
	 */
	protected $script_window_onload;

	protected $elements_list_ui_class_name;

	protected $context_parameters;

	protected $misc_file_search_fields;

    public function __construct($rec_history=false,$fichier_xml="",$full_path='') {
        global $default_tmp_storage_engine;

        $this->current_engine = $default_tmp_storage_engine;
		$this->rec_history=$rec_history;
		$this->full_path = $full_path;
		$this->fichier_xml=$fichier_xml;
		$this->parse_search_file();
		$this->strip_slashes();

		foreach ( $this->dynamicfields as $key => $value ) {
			$this->pp[$key]=new parametres_perso($value["TYPE"]);
		}

		if(isset($this->dynamicfields['a'])) {
			$authpersos=authpersos::get_instance();
			$this->authpersos=$authpersos->get_data();
		}
	}

	//Parse du fichier de configuration
	protected function parse_search_file() {
		global $include_path,$base_path, $charset;
		global $msg, $KEY_CACHE_FILE_XML;
		global $pmb_opac_url, $lang;

		$filepath = "";
		if(!$this->full_path){
			if ($this->fichier_xml == '') {
				$this->fichier_xml = 'search_fields';
			}
			if (!static::$ignore_subst_file && file_exists($include_path."/search_queries/".$this->fichier_xml."_subst.xml")) {
				$filepath = $include_path."/search_queries/".$this->fichier_xml."_subst.xml";
			} else {
				$filepath = $include_path."/search_queries/".$this->fichier_xml.".xml";
			}
		} else{
			if (!static::$ignore_subst_file && file_exists($this->full_path.$this->fichier_xml."_subst.xml")) {
				$filepath =$this->full_path.$this->fichier_xml."_subst.xml";
			} else {
				$filepath =$this->full_path.$this->fichier_xml.".xml";
			}
		}
		$fileInfo = pathinfo($filepath);
		$fileName = preg_replace("/[^a-z0-9]/i","",$fileInfo['dirname'].$fileInfo['filename'].$lang.$charset);
		$tempFile = $base_path."/temp/XML".$fileName.".tmp";
		$dejaParse = false;

		$cache_php=cache_factory::getCache();
		$key_file="";
		if ($cache_php) {
			$key_file=getcwd().$fileName.filemtime($filepath);
			$key_file=$KEY_CACHE_FILE_XML.md5($key_file);
			if($tmp_key = $cache_php->getFromCache($key_file)){
				if($cache = $cache_php->getFromCache($tmp_key)){
					if(is_array($cache) && (count($cache) == 16)){
						$this->groups_used = $cache[0];
						$this->groups = $cache[1];
						$this->memory_engine_allowed = $cache[2];
						$this->operators = $cache[3];
						$this->op_empty = $cache[4];
						$this->fixedfields = $cache[5];
						$this->dynamics_not_visible = $cache[6];
						$this->dynamicfields_order = $cache[7];
						$this->dynamicfields_hidebycustomname = $cache[8];
						$this->dynamicfields = $cache[9];
						$this->specials_not_visible = $cache[10];
						$this->tableau_speciaux = $cache[11];
						$this->keyName = $cache[12];
						$this->tableName = $cache[13];
						$this->specialfields = $cache[14];
						$this->op_special = $cache[15];
						$dejaParse = true;
					}
				}
			}
		}else{
			if (file_exists($tempFile) ) {
				//Le fichier XML original a-t-il été modifié ultérieurement ?
				if (filemtime($filepath) > filemtime($tempFile)) {
					//on va re-générer le pseudo-cache
					unlink($tempFile);
				} else {
					$dejaParse = true;
				}
			}
			if ($dejaParse) {
				$tmp = fopen($tempFile, "r");
				$cache = unserialize(fread($tmp,filesize($tempFile)));
				fclose($tmp);
				if(is_array($cache) && (count($cache) == 16)){
					$this->groups_used = $cache[0];
					$this->groups = $cache[1];
					$this->memory_engine_allowed = $cache[2];
					$this->operators = $cache[3];
					$this->op_empty = $cache[4];
					$this->fixedfields = $cache[5];
					$this->dynamics_not_visible = $cache[6];
					$this->dynamicfields_order = $cache[7];
					$this->dynamicfields_hidebycustomname = $cache[8];
					$this->dynamicfields = $cache[9];
					$this->specials_not_visible = $cache[10];
					$this->tableau_speciaux = $cache[11];
					$this->keyName = $cache[12];
					$this->tableName = $cache[13];
					$this->specialfields = $cache[14];
					$this->op_special = $cache[15];
				}else{
					//SOUCIS de cache...
					unlink($tempFile);
					$dejaParse = false;
				}
			}
		}
		if(!$dejaParse){
			if($this->fichier_xml == 'search_fields_opac' || strpos($this->full_path, '/opac_css/') !== false) {
				$save_msg=$msg;
				$url = $pmb_opac_url."includes/messages/$lang.xml";
				$fichier_xml = $base_path."/temp/opac_lang.xml";
				curl_load_opac_file($url,$fichier_xml);
				$messages = new XMLlist("$base_path/temp/opac_lang.xml", 0);
				$messages->analyser();
				$msg = $messages->table;
			}
			$fp=fopen($filepath,"r") or die("Can't find XML file");
			$size=filesize($filepath);
			$xml=fread($fp,$size);
			fclose($fp);
			$param=_parser_text_no_function_($xml, "PMBFIELDS");

			if(isset($param['GROUPS'])){
				$this->groups_used = true;
				$this->groups = array();
				foreach($param['GROUPS'][0]['GROUP'] as $group){
					$this->groups[$group['ID']] = array(
							'label' => (substr($group['LABEL'][0]['value'],0,4) == "msg:" ? $msg[substr($group['LABEL'][0]['value'],4,strlen($group['LABEL'][0]['value'])-4)] : $group['LABEL'][0]['value']),
							'order' => $group['ORDER'][0]['value'],
							'objects_type' => (isset($group['OBJECTS_TYPE'][0]['value']) ? $group['OBJECTS_TYPE'][0]['value'] : '')
					);
				}
				uasort($this->groups, array($this, 'sort_groups'));
			}

			//Lecture parametre memory_engine_allowed
			if(isset($param['MEMORYENGINEALLOWED'][0]['value']) && $param['MEMORYENGINEALLOWED'][0]['value']=='yes') {
				$this->memory_engine_allowed = true;
			} else {
				$this->memory_engine_allowed = false;
			}

			//Lecture des operateurs
			if (!empty($param["OPERATORS"][0]["OPERATOR"]) && is_countable($param["OPERATORS"][0]["OPERATOR"])) {
    			for ($i=0; $i<count($param["OPERATORS"][0]["OPERATOR"]); $i++) {
    				$operator_=$param["OPERATORS"][0]["OPERATOR"][$i];
    				if (substr($operator_["value"],0,4)=="msg:") {
    					$this->operators[$operator_["NAME"]]=$msg[substr($operator_["value"],4,strlen($operator_["value"])-4)];
    				} else {
    					$this->operators[$operator_["NAME"]]=$operator_["value"];
    				}
    				if (isset($operator_["EMPTYALLOWED"]) && ($operator_["EMPTYALLOWED"]=="yes")) {
    					$this->op_empty[$operator_["NAME"]]=true;
    				}else {
    					$this->op_empty[$operator_["NAME"]]=false;
    				}
    				if (isset($operator_["SPECIAL"]) && ($operator_["SPECIAL"]=="yes")) {
    				    $this->op_special[$operator_["NAME"]]=true;
    				} else {
    				    $this->op_special[$operator_["NAME"]]=false;
    				}
    			}
			}

			//Lecture des champs fixes
			if(!isset($param["FIXEDFIELDS"][0]["FIELD"])) {
			    $param["FIXEDFIELDS"][0]["FIELD"] = array();
			}
			for ($i=0; $i<count($param["FIXEDFIELDS"][0]["FIELD"]); $i++) {
				$t=array();
				$ff=$param["FIXEDFIELDS"][0]["FIELD"][$i];

				if (substr($ff["TITLE"],0,4)=="msg:" && isset($msg[substr($ff["TITLE"],4,strlen($ff["TITLE"])-4)])) {
					$t["TITLE"]=$msg[substr($ff["TITLE"],4,strlen($ff["TITLE"])-4)];
				} else {
					$t["TITLE"]=$ff["TITLE"];
				}
				$t["ID"]=$ff["ID"];
				$t["NOTDISPLAYCOL"]=(isset($ff["NOTDISPLAYCOL"]) ? $ff["NOTDISPLAYCOL"] : '');
				$t["UNIMARCFIELD"]=(isset($ff["UNIMARCFIELD"]) ? $ff["UNIMARCFIELD"] : '');
				$t["INPUT_TYPE"]=(isset($ff["INPUT"][0]["TYPE"]) ? $ff["INPUT"][0]["TYPE"] : '');
				$t["INPUT_FILTERING"]=(isset($ff["INPUT"][0]["FILTERING"]) ? $ff["INPUT"][0]["FILTERING"] : '');
				$t["INPUT_OPTIONS"]=(isset($ff["INPUT"][0]) ? $ff["INPUT"][0] : '');
				if($this->groups_used){
					$t["GROUP"]=(isset($ff["GROUP"]) ? $ff["GROUP"] : '');
				}
				$t["SEPARATOR"] = '';
				if(isset($ff["SEPARATOR"])) {
					if (substr($ff["SEPARATOR"],0,4)=="msg:") {
						$t["SEPARATOR"]=$msg[substr($ff["SEPARATOR"],4,strlen($ff["SEPARATOR"])-4)];
					} else {
						$t["SEPARATOR"]=$ff["SEPARATOR"];
					}
				}
				//Visibilite
				if(isset($ff["VISIBLE"]) && $ff["VISIBLE"]=="no") {
					$t["VISIBLE"]=false;
				} else {
					$t["VISIBLE"]=true;
				}

				//Moteur memory
				if(isset($ff['MEMORYENGINEFORBIDDEN']) && $ff['MEMORYENGINEFORBIDDEN']=='yes')
					$t['MEMORYENGINEFORBIDDEN']=true;
				else
					$t['MEMORYENGINEFORBIDDEN']=false;

				//Variables
				$t["VAR"] = array();
				if(isset($ff["VARIABLE"])) {
					for ($j=0; $j<count($ff["VARIABLE"]); $j++) {
						$v=array();
						$vv=$ff["VARIABLE"][$j];
						$v["NAME"]=$vv["NAME"];
						$v["TYPE"]=$vv["TYPE"];
						$v["COMMENT"]='';
						if(isset($vv["COMMENT"])) {
						    if (substr($vv["COMMENT"],0,4)=="msg:" && isset($msg[substr($vv["COMMENT"],4,strlen($vv["COMMENT"])-4)])) {
								$v["COMMENT"]=$msg[substr($vv["COMMENT"],4,strlen($vv["COMMENT"])-4)];
							} else {
								$v["COMMENT"]=$vv["COMMENT"];
							}
						}
						//Recherche des options
						reset($vv);
						foreach ($vv as $key => $val) {
							if (is_array($val)) {
								$v["OPTIONS"][$key]=$val;
							}
						}
						$v["PLACE"]=(isset($vv["PLACE"]) ? $vv["PLACE"] : '');
						$v["CLASS"]=(isset($vv["CLASS"]) ? $vv["CLASS"] : '');
						$t["VAR"][]=$v;
					}
				}

				if (!isset($ff["VISIBILITY"])) {
					$t["VISIBILITY"]=true;
				} else {
				    if ($ff["VISIBILITY"]=="yes") {
					$t["VISIBILITY"]=true;
				    } else {
				        $t["VISIBILITY"]=false;
				    }
				}
				if (empty($ff["QUERY"]) || !is_countable($ff["QUERY"])) {
				    $ff["QUERY"] = array();
				}
				for ($j=0; $j<count($ff["QUERY"]); $j++) {
					$q=array();
					$q["OPERATOR"]=$ff["QUERY"][$j]["FOR"];
					if(!isset($ff["QUERY"][$j]["MULTIPLE"])) $ff["QUERY"][$j]["MULTIPLE"] = '';
					if(!isset($ff["QUERY"][$j]["CONDITIONAL"])) $ff["QUERY"][$j]["CONDITIONAL"] = '';
					if (($ff["QUERY"][$j]["MULTIPLE"]=="yes")||($ff["QUERY"][$j]["CONDITIONAL"]=="yes")) {
						if($ff["QUERY"][$j]["MULTIPLE"]=="yes") $element = "PART";
						else $element = "VAR";

						for ($k=0; $k<count($ff["QUERY"][$j][$element]); $k++) {
							$pquery=$ff["QUERY"][$j][$element][$k];
							if($element == "VAR"){
								$q[$k]["CONDITIONAL"]["name"] = $pquery["NAME"];
								$q[$k]["CONDITIONAL"]["value"] = $pquery["VALUE"][0]["value"];
							}
							if (isset($pquery["MULTIPLEWORDS"]) && $pquery["MULTIPLEWORDS"]=="yes")
								$q[$k]["MULTIPLE_WORDS"]=true;
							else
								$q[$k]["MULTIPLE_WORDS"]=false;
							if (isset($pquery["REGDIACRIT"]) && $pquery["REGDIACRIT"]=="yes")
								$q[$k]["REGDIACRIT"]=true;
							else
								$q[$k]["REGDIACRIT"]=false;
							if (isset($pquery["KEEP_EMPTYWORD"]) && $pquery["KEEP_EMPTYWORD"]=="yes")
								$q[$k]["KEEP_EMPTYWORD"]=true;
							else
								$q[$k]["KEEP_EMPTYWORD"]=false;
							if (isset($pquery["REPEAT"])) {
								$q[$k]["REPEAT"]["NAME"]=$pquery["REPEAT"][0]["NAME"];
								$q[$k]["REPEAT"]["ON"]=$pquery["REPEAT"][0]["ON"];
								$q[$k]["REPEAT"]["SEPARATOR"]=$pquery["REPEAT"][0]["SEPARATOR"];
								$q[$k]["REPEAT"]["OPERATOR"]=$pquery["REPEAT"][0]["OPERATOR"];
								$q[$k]["REPEAT"]["ORDERTERM"]=(isset($pquery["REPEAT"][0]["ORDERTERM"]) ? $pquery["REPEAT"][0]["ORDERTERM"] : '');
							}
							if (isset($pquery["BOOLEANSEARCH"]) && $pquery["BOOLEANSEARCH"]=="yes") {
								$q[$k]["BOOLEAN"]=true;
								if ($pquery["BOOLEAN"]) {
									for ($z=0; $z<count($pquery["BOOLEAN"]); $z++) {
										$q[$k]["TABLE"][$z]=$pquery["BOOLEAN"][$z]["TABLE"][0]["value"];
										$q[$k]["INDEX_L"][$z]=$pquery["BOOLEAN"][$z]["INDEX_L"][0]["value"];
										$q[$k]["INDEX_I"][$z]=$pquery["BOOLEAN"][$z]["INDEX_I"][0]["value"];
										$q[$k]["ID_FIELD"][$z]=$pquery["BOOLEAN"][$z]["ID_FIELD"][0]["value"];
										if (isset($pquery["BOOLEAN"][$z]["KEEP_EMPTY_WORDS"][0]["value"]) && $pquery["BOOLEAN"][$z]["KEEP_EMPTY_WORDS"][0]["value"]=="yes") {
											$q[$k]["KEEP_EMPTY_WORDS"][$z]=1;
											$q[$k]["KEEP_EMPTY_WORDS_FOR_CHECK"]=1;
										}
										if (isset($pquery["BOOLEAN"][$z]["FULLTEXT"][0]["value"]) && $pquery["BOOLEAN"][$z]["FULLTEXT"][0]["value"]=="yes") {
											$q[$k]["FULLTEXT"][$z]=1;
										}
									}
								} else {
									$q[$k]["TABLE"]=$pquery["TABLE"][0]["value"];
									$q[$k]["INDEX_L"]=$pquery["INDEX_L"][0]["value"];
									$q[$k]["INDEX_I"]=$pquery["INDEX_I"][0]["value"];
									$q[$k]["ID_FIELD"]=$pquery["ID_FIELD"][0]["value"];
									if ($pquery["KEEP_EMPTY_WORDS"][0]["value"]=="yes") {
										$q[$k]["KEEP_EMPTY_WORDS"]=1;
										$q[$k]["KEEP_EMPTY_WORDS_FOR_CHECK"]=1;
									}
									if (isset($pquery["FULLTEXT"][0]["value"]) && $pquery["FULLTEXT"][0]["value"]=="yes") {
										$q[$k]["FULLTEXT"]=1;
									}
								}
							} else $q[$k]["BOOLEAN"]=false;
							if (isset($pquery["ISBNSEARCH"]) && $pquery["ISBNSEARCH"]=="yes") {
								$q[$k]["ISBN"]=true;
							} else $q[$k]["ISBN"]=false;
							if (isset($pquery["DETECTDATE"])) {
								$q[$k]["DETECTDATE"]=$pquery["DETECTDATE"];
							} else $q[$k]["DETECTDATE"]=false;
							$q[$k]["MAIN"]=(isset($pquery["MAIN"][0]["value"]) ? $pquery["MAIN"][0]["value"] : '');
							$q[$k]["MULTIPLE_TERM"]=(isset($pquery["MULTIPLETERM"][0]["value"]) ? $pquery["MULTIPLETERM"][0]["value"] : '');
							$q[$k]["MULTIPLE_OPERATOR"]=(isset($pquery["MULTIPLEOPERATOR"][0]["value"]) ? $pquery["MULTIPLEOPERATOR"][0]["value"] : '');
						}
						$t["QUERIES"][]=$q;
						$t["QUERIES_INDEX"][$q["OPERATOR"]]=count($t["QUERIES"])-1;
					} else {
						if (isset($ff["QUERY"][$j]["CUSTOM_SEARCH"]) && $ff["QUERY"][$j]["CUSTOM_SEARCH"]=="yes")
							$q[0]["CUSTOM_SEARCH"] = true;
						else
							$q[0]["CUSTOM_SEARCH"] = false;
						if (isset($ff["QUERY"][$j]["MULTIPLEWORDS"]) && $ff["QUERY"][$j]["MULTIPLEWORDS"]=="yes")
							$q[0]["MULTIPLE_WORDS"]=true;
						else
							$q[0]["MULTIPLE_WORDS"]=false;
						if (isset($ff["QUERY"][$j]["REGDIACRIT"]) && $ff["QUERY"][$j]["REGDIACRIT"]=="yes")
							$q[0]["REGDIACRIT"]=true;
						else
							$q[0]["REGDIACRIT"]=false;
						if (isset($ff["QUERY"][$j]["KEEP_EMPTYWORD"]) && $ff["QUERY"][$j]["KEEP_EMPTYWORD"]=="yes")
							$q[0]["KEEP_EMPTYWORD"]=true;
						else
							$q[0]["KEEP_EMPTYWORD"]=false;
						if (isset($ff["QUERY"][$j]["REPEAT"])) {
							$q[0]["REPEAT"]["NAME"]=$ff["QUERY"][$j]["REPEAT"][0]["NAME"];
							$q[0]["REPEAT"]["ON"]=$ff["QUERY"][$j]["REPEAT"][0]["ON"];
							$q[0]["REPEAT"]["SEPARATOR"]=$ff["QUERY"][$j]["REPEAT"][0]["SEPARATOR"];
							$q[0]["REPEAT"]["OPERATOR"]=$ff["QUERY"][$j]["REPEAT"][0]["OPERATOR"];
							$q[0]["REPEAT"]["ORDERTERM"]=(isset($ff["QUERY"][$j]["REPEAT"][0]["ORDERTERM"]) ? $ff["QUERY"][$j]["REPEAT"][0]["ORDERTERM"] : '');
						}
						if (isset($ff["QUERY"][$j]["BOOLEANSEARCH"]) && $ff["QUERY"][$j]["BOOLEANSEARCH"]=="yes") {
							$q[0]["BOOLEAN"]=true;
							if (isset($ff["QUERY"][$j]["BOOLEAN"])) {
								for ($z=0; $z<count($ff["QUERY"][$j]["BOOLEAN"]); $z++) {
									$q[0]["TABLE"][$z]=$ff["QUERY"][$j]["BOOLEAN"][$z]["TABLE"][0]["value"];
									$q[0]["INDEX_L"][$z]=$ff["QUERY"][$j]["BOOLEAN"][$z]["INDEX_L"][0]["value"];
									$q[0]["INDEX_I"][$z]=$ff["QUERY"][$j]["BOOLEAN"][$z]["INDEX_I"][0]["value"];
									$q[0]["ID_FIELD"][$z]=$ff["QUERY"][$j]["BOOLEAN"][$z]["ID_FIELD"][0]["value"];
									if (isset($ff["QUERY"][$j]["BOOLEAN"][$z]["KEEP_EMPTY_WORDS"][0]["value"]) && $ff["QUERY"][$j]["BOOLEAN"][$z]["KEEP_EMPTY_WORDS"][0]["value"]=="yes") {
										$q[0]["KEEP_EMPTY_WORDS"][$z]=1;
										$q[0]["KEEP_EMPTY_WORDS_FOR_CHECK"]=1;
									}
								}
							} else {
								$q[0]["TABLE"]=$ff["QUERY"][$j]["TABLE"][0]["value"];
								$q[0]["INDEX_L"]=$ff["QUERY"][$j]["INDEX_L"][0]["value"];
								$q[0]["INDEX_I"]=$ff["QUERY"][$j]["INDEX_I"][0]["value"];
								$q[0]["ID_FIELD"]=$ff["QUERY"][$j]["ID_FIELD"][0]["value"];
								if (isset($ff["QUERY"][$j]["KEEP_EMPTY_WORDS"][0]["value"]) && $ff["QUERY"][$j]["KEEP_EMPTY_WORDS"][0]["value"]=="yes") {
									$q[0]["KEEP_EMPTY_WORDS"]=1;
									$q[0]["KEEP_EMPTY_WORDS_FOR_CHECK"]=1;
								}
							}
						} else $q[0]["BOOLEAN"]=false;
						//prise en compte ou non du paramétrage du stemming
						if(isset($ff["QUERY"][$j]['STEMMING']) && $ff["QUERY"][$j]['STEMMING']=="no"){
							$q[0]["STEMMING"]= false;
						}else{
							$q[0]["STEMMING"]= true;
						}
						//modif arnaud pour notices_mots_global_index..
						if (isset($ff["QUERY"][$j]['WORDSEARCH']) && $ff["QUERY"][$j]['WORDSEARCH']=="yes"){
							$q[0]["WORD"]=true;
							if(isset($ff["QUERY"][$j]['CLASS'][0]['NAME'])) {
								$q[0]['CLASS'] = $ff["QUERY"][$j]['CLASS'][0]['NAME'];
								if(count($ff["QUERY"][$j]['CLASS'][0]['FIELDRESTRICT'])) {
									$q[0]['FIELDSRESTRICT'] = array();
									foreach ($ff["QUERY"][$j]['CLASS'][0]['FIELDRESTRICT'] as $fieldrestrict) {
										$subfieldsrestrict = array();
										if(isset($fieldrestrict['SUB'])) {
											foreach ($fieldrestrict['SUB'][0]['FIELDRESTRICT'] as $subfieldrestrict) {
												$subfieldsrestrict[] = array(
														'sub_field' => $subfieldrestrict['SUB_FIELD'][0]['value'],
														'values' => explode(',', $subfieldrestrict['VALUES'][0]['value']),
														'op' => $subfieldrestrict['OP'][0]['value'],
														'not' => (isset($subfieldrestrict['NOT'][0]['value']) ? $subfieldrestrict['NOT'][0]['value'] : '')
												);
											}
										}
										$q[0]['FIELDSRESTRICT'][] = array(
												'field' => $fieldrestrict['FIELD'][0]['value'],
												'values' => explode(',', $fieldrestrict['VALUES'][0]['value']),
												'op' => $fieldrestrict['OP'][0]['value'],
												'not' => (isset($fieldrestrict['NOT'][0]['value']) ? $fieldrestrict['NOT'][0]['value'] : ''),
												'sub' => $subfieldsrestrict
										);
									}
								}
							} else if (isset($ff["QUERY"][$j]['CLASS'][0]['TYPE'])){
								$q[0]['TYPE'] = $ff["QUERY"][$j]['CLASS'][0]['TYPE'];
								if(isset($ff["QUERY"][$j]['CLASS'][0]['MODE'])){
									$q[0]['MODE'] = $ff["QUERY"][$j]['CLASS'][0]['MODE'];
								}
								if(isset($ff["QUERY"][$j]['CLASS'][0]['FIELDRESTRICT']) && count($ff["QUERY"][$j]['CLASS'][0]['FIELDRESTRICT'])) {
									$q[0]['FIELDSRESTRICT'] = array();
									foreach ($ff["QUERY"][$j]['CLASS'][0]['FIELDRESTRICT'] as $fieldrestrict) {
										$subfieldsrestrict = array();
										if(isset($fieldrestrict['SUB'])) {
											foreach ($fieldrestrict['SUB'][0]['FIELDRESTRICT'] as $subfieldrestrict) {
												$subfieldsrestrict[] = array(
												    'sub_field' => (isset($subfieldrestrict['SUB_FIELD'][0]['value'])) ? $subfieldrestrict['SUB_FIELD'][0]['value'] : '',
														'values' => explode(',', $subfieldrestrict['VALUES'][0]['value']),
														'op' => $subfieldrestrict['OP'][0]['value'],
														'not' => (isset($subfieldrestrict['NOT'][0]['value']) ? $subfieldrestrict['NOT'][0]['value'] : '')
												);
											}
										}
										$q[0]['FIELDSRESTRICT'][] = array(
												'field' => $fieldrestrict['FIELD'][0]['value'],
												'values' => explode(',', $fieldrestrict['VALUES'][0]['value']),
												'op' => $fieldrestrict['OP'][0]['value'],
												'not' => (isset($fieldrestrict['NOT'][0]['value']) ? $fieldrestrict['NOT'][0]['value'] : ''),
												'sub' => $subfieldsrestrict
										);
									}
								}
							} else {
								$q[0]['CLASS'] = $ff["QUERY"][$j]['CLASS'][0]['value'];
							}
							$q[0]['FOLDER'] = (isset($ff["QUERY"][$j]['CLASS'][0]['FOLDER']) ? $ff["QUERY"][$j]['CLASS'][0]['FOLDER'] : '');
							$q[0]['FIELDS'] = (isset($ff["QUERY"][$j]['CLASS'][0]['FIELDS']) ? $ff["QUERY"][$j]['CLASS'][0]['FIELDS'] : '');
						}else $q[0]["WORD"]=false;
						//fin modif arnaud
						if (isset($ff["QUERY"][$j]["ISBNSEARCH"]) && $ff["QUERY"][$j]["ISBNSEARCH"]=="yes") {
							$q[0]["ISBN"]=true;
						} else $q[0]["ISBN"]=false;
						if (isset($ff["QUERY"][$j]["DETECTDATE"])) {
							$q[0]["DETECTDATE"]=$ff["QUERY"][$j]["DETECTDATE"];
						} else $q[0]["DETECTDATE"]=false;
						$q[0]["MAIN"]=(isset($ff["QUERY"][$j]["MAIN"][0]["value"]) ? $ff["QUERY"][$j]["MAIN"][0]["value"] : '');

						if (isset($ff["QUERY"][$j]['SPECIAL'])) {
    						$q[0]["SPECIAL"] = array();
    						$q[0]["SPECIAL"]["CLASS"] = (isset($ff["QUERY"][$j]['SPECIAL'][0]["CLASS"]) ? $ff["QUERY"][$j]['SPECIAL'][0]["CLASS"] : '');
    						$q[0]["SPECIAL"]['PARAMS'] = array();

    						//Variables
    						if(isset($ff["QUERY"][$j]['SPECIAL'][0]['VARIABLE'])) {
    						    $length = count($ff["QUERY"][$j]['SPECIAL'][0]['VARIABLE']);
    						    $params = array();

    						    for ($x = 0; $x < $length; $x++) {
    						        $variable = $ff["QUERY"][$j]['SPECIAL'][0]['VARIABLE'][$x];
    						        $v = array();
    						        $v["NAME"] = $variable['NAME'];
    						        $v["TYPE"] = $variable['TYPE'];

    						        $v["COMMENT"] = '';
    						        if(isset($variable['COMMENT'])) {
    						            if (substr($variable['COMMENT'], 0, 4) == "msg:" && isset($msg[substr($variable['COMMENT'], 4)])) {
    						                $v["COMMENT"] = $msg[substr($variable['COMMENT'], 4)];
    						            } else {
    						                $v["COMMENT"] = $variable['COMMENT'];
    						            }
    						        }

    						        $v["DEFAULT"] = array();
    						        if (isset($variable['DEFAULT'])) {
        						        $v["DEFAULT"] = $variable['DEFAULT'][0];
    						        }
    						        $params[] = $v;
    						    }

    						    $q[0]["SPECIAL"]['PARAMS'] = $params;
    						}
						}

						$q[0]["MULTIPLE_TERM"]=(isset($ff["QUERY"][$j]["MULTIPLETERM"][0]["value"]) ? $ff["QUERY"][$j]["MULTIPLETERM"][0]["value"] : '');
						$q[0]["MULTIPLE_OPERATOR"]=(isset($ff["QUERY"][$j]["MULTIPLEOPERATOR"][0]["value"]) ? $ff["QUERY"][$j]["MULTIPLEOPERATOR"][0]["value"] : '');
						$t["QUERIES"][]=$q;
						$t["QUERIES_INDEX"][$q["OPERATOR"]]=count($t["QUERIES"])-1;
					}
				}

				// recuperation des visibilites parametrees
				$t["VARVIS"] = array();
				if(isset($ff["VAR"])) {
					for ($j=0; $j<count($ff["VAR"]); $j++) {
						$q=array();
						$q["NAME"]=$ff["VAR"][$j]["NAME"];
						if ($ff["VAR"][$j]["VISIBILITY"]=="yes")
							$q["VISIBILITY"]=true;
						else
							$q["VISIBILITY"]=false;
						for ($k=0; $k<count($ff["VAR"][$j]["VALUE"]); $k++) {
							$v=array();
							if ($ff["VAR"][$j]["VALUE"][$k]["VISIBILITY"]=="yes")
								$v[$ff["VAR"][$j]["VALUE"][$k]["value"]] = true ;
							else
								$v[$ff["VAR"][$j]["VALUE"][$k]["value"]] = false ;
						} // fin for <value ...
						$q["VALUE"] = $v ;
						$t["VARVIS"][] = $q ;
					} // fin for
				}

				$this->fixedfields[$ff["ID"]]=$t;
			}

			//Lecture des champs dynamiques
			if (isset($param["DYNAMICFIELDS"][0]["VISIBLE"]) && $param["DYNAMICFIELDS"][0]["VISIBLE"]=="no") $this->dynamics_not_visible=true;
			if(!isset($param["DYNAMICFIELDS"][0]["FIELDTYPE"]) || !$param["DYNAMICFIELDS"][0]["FIELDTYPE"]){//Pour le cas de fichiers subst basés sur l'ancienne version
				$tmp=(isset($param["DYNAMICFIELDS"][0]["FIELD"]) ? $param["DYNAMICFIELDS"][0]["FIELD"] : '');
				unset($param["DYNAMICFIELDS"]);
				$param["DYNAMICFIELDS"][0]["FIELDTYPE"][0]["PREFIX"]="d";
				$param["DYNAMICFIELDS"][0]["FIELDTYPE"][0]["TYPE"]="notices";
				$param["DYNAMICFIELDS"][0]["FIELDTYPE"][0]["FIELD"]=$tmp;
				unset($tmp);
			}
			//Ordre des champs persos
			if (isset($param["DYNAMICFIELDS"][0]["OPTION"][0]["ORDER"])) {
				$this->dynamicfields_order=$param["DYNAMICFIELDS"][0]["OPTION"][0]["ORDER"];
			} else {
				$this->dynamicfields_order='';
			}
			for ($h=0; $h <count($param["DYNAMICFIELDS"][0]["FIELDTYPE"]); $h++){
				$champType=array();
				$ft=$param["DYNAMICFIELDS"][0]["FIELDTYPE"][$h];
				$champType["TYPE"]=$ft["TYPE"];
				//Exclusion de champs persos cités par nom
				if (isset($ft["HIDEBYCUSTOMNAME"])) {
					$this->dynamicfields_hidebycustomname[$ft["TYPE"]]=$ft["HIDEBYCUSTOMNAME"];
				}

				if($this->groups_used){
					$champType["GROUP"]=(isset($ft["GROUP"]) ? $ft["GROUP"] : '');
				}
				if (!empty($ft["FIELD"]) && is_countable($ft["FIELD"])) {
    				for ($i=0; $i<count($ft["FIELD"]); $i++) {
    					$t=array();
    					$ff=$ft["FIELD"][$i];
    					$t["DATATYPE"]=$ff["DATATYPE"];
    					$t["NOTDISPLAYCOL"]=(isset($ff["NOTDISPLAYCOL"]) ? $ff["NOTDISPLAYCOL"] : '');
    					//Moteur memory
    					if(isset($ff['MEMORYENGINEFORBIDDEN']) && $ff['MEMORYENGINEFORBIDDEN']=='yes')
    						$t['MEMORYENGINEFORBIDDEN']=true;
    					else
    						$t['MEMORYENGINEFORBIDDEN']=false;
    					$q=array();
    					if (!empty($ff["QUERY"])) {
        					for ($j=0; $j<count($ff["QUERY"]); $j++) {
        						$q["OPERATOR"]=$ff["QUERY"][$j]["FOR"];
        						if (isset($ff["QUERY"][$j]["MULTIPLEWORDS"]) && $ff["QUERY"][$j]["MULTIPLEWORDS"]=="yes")
        							$q["MULTIPLE_WORDS"]=true;
        						else
        							$q["MULTIPLE_WORDS"]=false;
        						if (isset($ff["QUERY"][$j]["REGDIACRIT"]) && $ff["QUERY"][$j]["REGDIACRIT"]=="yes")
        							$q["REGDIACRIT"]=true;
        						else
        							$q["REGDIACRIT"]=false;
        						if (isset($ff["QUERY"][$j]["KEEP_EMPTYWORD"]) && $ff["QUERY"][$j]["KEEP_EMPTYWORD"]=="yes")
        							$q["KEEP_EMPTYWORD"]=true;
        						else
        							$q["KEEP_EMPTYWORD"]=false;
        						if (isset($ff["QUERY"][$j]["DEFAULT_OPERATOR"]))
        							$q["DEFAULT_OPERATOR"] = $ff["QUERY"][$j]["DEFAULT_OPERATOR"];
        						$q["NOT_ALLOWED_FOR"]=array();
        						$naf=(isset($ff["QUERY"][$j]["NOTALLOWEDFOR"]) ? $ff["QUERY"][$j]["NOTALLOWEDFOR"] : '');
        						if ($naf) {
        							$naf_=explode(",",$naf);
        							$q["NOT_ALLOWED_FOR"]=$naf_;
        						}
        						if(isset($ff["QUERY"][$j]['WORDSEARCH']) && $ff["QUERY"][$j]['WORDSEARCH']=="yes"){
        							$q["WORD"]=true;
        							if(isset($ff["QUERY"][$j]['CLASS'][0]['NAME'])) {
        								$q['CLASS'] = $ff["QUERY"][$j]['CLASS'][0]['NAME'];
        								if(count($ff["QUERY"][$j]['CLASS'][0]['FIELDRESTRICT'])) {
        									$q['FIELDSRESTRICT'] = array();
        									foreach ($ff["QUERY"][$j]['CLASS'][0]['FIELDRESTRICT'] as $fieldrestrict) {
        										$subfieldsrestrict = array();
        										if(isset($fieldrestrict['SUB'])) {
        											foreach ($fieldrestrict['SUB'][0]['FIELDRESTRICT'] as $subfieldrestrict) {
        												$subfieldsrestrict[] = array(
        														'sub_field' => $subfieldrestrict['SUB_FIELD'][0]['value'],
        														'values' => explode(',', $subfieldrestrict['VALUES'][0]['value']),
        														'op' => $subfieldrestrict['OP'][0]['value'],
        														'not' => (isset($subfieldrestrict['NOT'][0]['value']) ? $subfieldrestrict['NOT'][0]['value'] : '')
        												);
        											}
        										}
        										$q['FIELDSRESTRICT'][] = array(
        												'field' => $fieldrestrict['FIELD'][0]['value'],
        												'values' => explode(',', $fieldrestrict['VALUES'][0]['value']),
        												'op' => $fieldrestrict['OP'][0]['value'],
        												'not' => (isset($fieldrestrict['NOT'][0]['value']) ? $fieldrestrict['NOT'][0]['value'] : ''),
        												'sub' => $subfieldsrestrict
        										);
        									}
        								}
        							}elseif(isset($ff["QUERY"][$j]['CLASS'][0]['TYPE'])) {
        								$q['TYPE'] = $ff["QUERY"][$j]['CLASS'][0]['TYPE'];
        								if(isset($ff["QUERY"][$j]['CLASS'][0]['MODE'])){
        									$q['MODE'] = $ff["QUERY"][$j]['CLASS'][0]['MODE'];
        								}
        								if(isset($ff["QUERY"][$j]['CLASS'][0]['FIELDRESTRICT']) && count($ff["QUERY"][$j]['CLASS'][0]['FIELDRESTRICT'])) {
        									$q['FIELDSRESTRICT'] = array();
        									foreach ($ff["QUERY"][$j]['CLASS'][0]['FIELDRESTRICT'] as $fieldrestrict) {
        										$subfieldsrestrict = array();
        										if(isset($fieldrestrict['SUB'])) {
        											foreach ($fieldrestrict['SUB'][0]['FIELDRESTRICT'] as $subfieldrestrict) {
        												$subfieldsrestrict[] = array(
        														'sub_field' => $subfieldrestrict['SUB_FIELD'][0]['value'],
        														'values' => explode(',', $subfieldrestrict['VALUES'][0]['value']),
        														'op' => $subfieldrestrict['OP'][0]['value'],
        														'not' => (isset($subfieldrestrict['NOT'][0]['value']) ? $subfieldrestrict['NOT'][0]['value'] : '')
        												);
        											}
        										}
        										$q['FIELDSRESTRICT'][] = array(
        												'field' => $fieldrestrict['FIELD'][0]['value'],
        												'values' => explode(',', $fieldrestrict['VALUES'][0]['value']),
        												'op' => $fieldrestrict['OP'][0]['value'],
        												'not' => (isset($fieldrestrict['NOT'][0]['value']) ? $fieldrestrict['NOT'][0]['value'] : ''),
        												'sub' => $subfieldsrestrict
        										);
        									}
        								}
        							}else{
        								$q['CLASS'] = $ff["QUERY"][$j]['CLASS'][0]['value'];
        							}
        							$q['FOLDER'] = (isset($ff["QUERY"][$j]['CLASS'][0]['FOLDER']) ? $ff["QUERY"][$j]['CLASS'][0]['FOLDER'] : '');
        							$q['FIELDS'] = (isset($ff["QUERY"][$j]['CLASS'][0]['FIELDS']) ? $ff["QUERY"][$j]['CLASS'][0]['FIELDS'] : '');
        						}else $q["WORD"]=false;
        						if (isset($ff["QUERY"][$j]['SEARCHABLEONLY']) && $ff["QUERY"][$j]['SEARCHABLEONLY']=="yes"){
        							$q["SEARCHABLEONLY"]=true;
        						}else $q["SEARCHABLEONLY"]=false;
    
        						$q["MAIN"]=(isset($ff["QUERY"][$j]["MAIN"][0]["value"]) ? $ff["QUERY"][$j]["MAIN"][0]["value"] : '');
        						$q["MULTIPLE_TERM"]=(isset($ff["QUERY"][$j]["MULTIPLETERM"][0]["value"]) ? $ff["QUERY"][$j]["MULTIPLETERM"][0]["value"] : '');
        						$q["MULTIPLE_OPERATOR"]=(isset($ff["QUERY"][$j]["MULTIPLEOPERATOR"][0]["value"]) ? $ff["QUERY"][$j]["MULTIPLEOPERATOR"][0]["value"] : '');
        						$t["QUERIES"][]=$q;
        						$t["QUERIES_INDEX"][$q["OPERATOR"]]=count($t["QUERIES"])-1;
        					}
    					}
    					$champType["FIELD"][$ff["ID"]]=$t;
    				}
				}
				$this->dynamicfields[$ft["PREFIX"]]=$champType;
			}


			//Lecture des champs speciaux
			if (isset($param["SPECIALFIELDS"][0]["VISIBLE"]) && $param["SPECIALFIELDS"][0]["VISIBLE"]=="no") {
				$this->specials_not_visible=true;
			}
			if(!empty($param["SPECIALFIELDS"][0]["FIELD"]) && is_array($param["SPECIALFIELDS"][0]["FIELD"])){
				for ($i=0; $i<count($param["SPECIALFIELDS"][0]["FIELD"]); $i++) {
					$t=array();
					$sf=$param["SPECIALFIELDS"][0]["FIELD"][$i];
					if (substr($sf["TITLE"],0,4)=="msg:" && isset($msg[substr($sf["TITLE"],4,strlen($sf["TITLE"])-4)])) {
						$t["TITLE"]=$msg[substr($sf["TITLE"],4,strlen($sf["TITLE"])-4)];
					} else {
						$t["TITLE"]=$sf["TITLE"];
					}
					if($this->groups_used){
						$t["GROUP"]=(isset($sf["GROUP"]) ? $sf["GROUP"] : '');
					}
					$t["NOTDISPLAYCOL"]=(isset($sf["NOTDISPLAYCOL"]) ? $sf["NOTDISPLAYCOL"] : '');
					$t["UNIMARCFIELD"]=(isset($sf["UNIMARCFIELD"]) ? $sf["UNIMARCFIELD"] : '');
					$t["SEPARATOR"]='';
					if(isset($sf["SEPARATOR"])) {
						if (substr($sf["SEPARATOR"],0,4)=="msg:") {
							$t["SEPARATOR"]=$msg[substr($sf["SEPARATOR"],4,strlen($sf["SEPARATOR"])-4)];
						} else {
							$t["SEPARATOR"]=$sf["SEPARATOR"];
						}
					}
					$t["TYPE"]=$sf["TYPE"];

					//Visibilite
					if(isset($sf["VISIBLE"]) && $sf["VISIBLE"]=="no")
						$t["VISIBLE"]=false;
					else
						$t["VISIBLE"]=true;

					if(isset($sf["DELNOTALLOWED"]) && $sf["DELNOTALLOWED"] == "yes")
						$t["DELNOTALLOWED"]=true;
					else
						$t["DELNOTALLOWED"]=false;
					$this->specialfields[$sf["ID"]]=$t;
				}
			}
			if (is_array($this->specialfields) && (count($this->specialfields)!=0)) {
				if (file_exists($include_path."/search_queries/specials/catalog_subst.xml")) {
					$nom_fichier=$include_path."/search_queries/specials/catalog_subst.xml";
				} else {
					$nom_fichier=$include_path."/search_queries/specials/catalog.xml";
				}
				$parametres=file_get_contents($nom_fichier);
				$this->tableau_speciaux=_parser_text_no_function_($parametres, "SPECIALFIELDS");
			}
			$this->keyName = (isset($param["KEYNAME"][0]["value"]) ? $param["KEYNAME"][0]["value"] : '');

			if($this->fichier_xml == 'search_fields_authorities') {
				if(!$this->keyName) {
					$this->keyName="id_authority";
				}
				$this->tableName="authorities";
			} else {
				if(!$this->keyName) {
					$this->keyName="notice_id";
				}
				$this->tableName="notices";
			}

			$tmp_array_cache=array(
					$this->groups_used,
					$this->groups,
					$this->memory_engine_allowed,
					$this->operators,
					$this->op_empty,
					$this->fixedfields,
					$this->dynamics_not_visible,
					$this->dynamicfields_order,
					$this->dynamicfields_hidebycustomname,
					$this->dynamicfields,
					$this->specials_not_visible,
					$this->tableau_speciaux,
					$this->keyName,
					$this->tableName,
					$this->specialfields,
			        $this->op_special);
			if ($key_file) {
				$key_file_content=$KEY_CACHE_FILE_XML.md5(serialize($tmp_array_cache));
				$cache_php->setInCache($key_file_content, $tmp_array_cache);
				$cache_php->setInCache($key_file,$key_file_content);
			}else{
				$tmp = fopen($tempFile, "wb");
				fwrite($tmp,serialize($tmp_array_cache));
				fclose($tmp);
			}
			if($this->fichier_xml == 'search_fields_opac' || strpos($this->full_path, '/opac_css/') !== false) {
				$msg = $save_msg;
			}
		}
	} // fin parse_search_file

	protected function strip_slashes() {
		global $search, $explicit_search;

		if(isset($search) && is_array($search)) {
			for ($i=0; $i<count($search); $i++) {
				$s=explode("_",$search[$i]);
				$field=$this->get_global_value("field_".$i."_".$search[$i]);
				if (!empty($field)) {
					foreach ($field as $key=>$value) {
						if(is_array($value)) {
							$field[$key] = stripslashes_array($value);
						} else {
							$field[$key] = stripslashes($value);
						}
					}
				}
	    		$field1=$this->get_global_value("field_".$i."_".$search[$i]."_1");
	    		if(is_array($field1) && count($field1)){
	    			foreach ($field1 as $key=>$value) {
	    				if(isset($value)) {
	    					if(is_array($value)) {
	    						$field1[$key]=stripslashes_array($value);
	    					} else {
	    						$field1[$key]=stripslashes($value);
	    					}
	    				}
	    			}
	    		}
				if ($explicit_search) {
					if ($s[0]=="f") {
					    $ff = (!empty($this->fixedfields[$s[1]]) ? $this->fixedfields[$s[1]] : []);
					    if (isset($ff["INPUT_TYPE"])) {
    						switch ($ff["INPUT_TYPE"]) {
    							case "date":
    								$op=$this->get_global_value("op_".$i."_".$search[$i]);
    								switch ($op) {
    									case 'LESS_THAN_DAYS':
										case 'MORE_THAN_DAYS':
    										//Rien a faire
    										break;
    									default:
    										if(!preg_match("/^\d{4}-\d{2}-\d{2}$/",$field[0])) {
    											$field_temp=extraitdate($field[0]);
    											$field[0]=$field_temp;
    										}
    										break;
    								}
    								break;
    							default:
    								//Rien a faire
    								break;
    						}
    					}
					}
				}
				$this->set_global_value("field_".$i."_".$search[$i], $field);
				$this->set_global_value("field_".$i."_".$search[$i]."_1", $field1);
			}
		}
	}

	public function get_id_from_datatype($datatype, $fieldType = "d") {
		if(!is_array($this->dynamicfields[$fieldType]["FIELD"])) return '';
		foreach($this->dynamicfields[$fieldType]["FIELD"] as $key => $val){
			if ($val["DATATYPE"]==$datatype) return $key;
		}
		return '';
	}

	protected function get_completion_selection_field($i,$n,$search, $v, $params=array()) {
		global $charset;
		global $msg;

		$fnamesans="field_".$n."_".$search;
		$fname="field_".$n."_".$search."[]";
		$fname_id="field_".$n."_".$search."_id";
		$fnamesanslib="field_".$n."_".$search."_lib";
		$fnamelib="field_".$n."_".$search."_lib[]";

		$selector = $params['selector'];
		$p1 = $params['p1'];
		$p2 = $params['p2'];

		$op = $this->get_global_value("op_".$i."_".$search);

		$v=$this->clean_completion_empty_values($v);
		$nb_values=count($v);
		if(!$nb_values){
			//Création de la ligne
			$nb_values=1;
		}
		$nb_max_aut=$nb_values-1;
		$r = "<span class='ui-panel-display'>";
		$r.= "<input type='hidden' id='$fnamesans"."_max_aut' value='".$nb_max_aut."'>";
		$r.= "<input class='bouton' value='...' id='$fnamesans"."_selection_selector' title='".htmlentities($msg['title_select_from_list'],ENT_QUOTES,$charset)."' onclick=\"openPopUp('./select.php?what=$selector&caller=search_form&$p1=".$fname_id."_0&$p2=".$fnamesanslib."_0&search_xml_file=".$this->fichier_xml."&search_field_id=".$search."&deb_rech=&callback=selectionSelected&infield=".$fnamesans."_0', 'selector')\" type=\"button\">";
		$r.= "<input class='bouton' type='button' value='+' onclick='add_line(\"$fnamesans\", \"EQ\")'>";
		$r.= "</span>";
		$r.= "<div id='el$fnamesans'>";
		for($inc=0;$inc<$nb_values;$inc++){
			if(!isset($v[$inc])) $v[$inc] = '';
			$r.="<input id='".$fnamesans."_".$inc."' name='$fname' value='".htmlentities($v[$inc],ENT_QUOTES,$charset)."' type='hidden' />";
			switch ($op) {
				case 'EQ':
					if($v[$inc]){
						$libelle = $this->get_selector_display($v[$inc], $params['selector'], $search);
					}else{
						$libelle = "";
					}
					break;
				default:
					$libelle = $v[$inc];
					break;
			}
			$r.="<span class='search_value'>
					<input autfield='".$fname_id."_".$inc."' onkeyup='fieldChanged(\"".$fnamesans."\",".$inc.",this.value,event);' callback='selectionSelected' param1='".$this->fichier_xml."' param2='".$search."' completion='".$params['ajax']."' id='".$fnamesanslib."_".$inc."' name='$fnamelib' value='".htmlentities($libelle,ENT_QUOTES,$charset)."' type='text' class='saisie-20emr expand_completion' />
				</span>";
			$r.= "<input class='bouton vider' type='button' onclick='this.form.".$fnamesanslib."_".$inc.".value=\"\";this.form.".$fname_id."_".$inc.".value=\"0\";this.form.".$fnamesans."_".$inc.".value=\"0\";' value='X'>";
			$r.= "<input type='hidden' name='".$fname_id."_".$inc."' id='".$fname_id."_".$inc."' value='".htmlentities($v[$inc],ENT_QUOTES,$charset)."' /><br>";
		}
		$r.= "</div>";
		if($nb_values>1){
			$r.="<script>
					document.getElementById('op_".$n."_".$search."').disabled=true;
					if(operators_to_enable.indexOf('op_".$n."_".$search."') === -1) {
						operators_to_enable.push('op_".$n."_".$search."');
					}
				</script>";
		}
		return $r;
	}

	protected function get_completion_authority_field($i,$n,$search, $v, $params=array()) {
		global $charset;
		global $opac_thesaurus;
		global $msg;

		$fnamesans="field_".$n."_".$search;
		$fname="field_".$n."_".$search."[]";
		$fname_id="field_".$n."_".$search."_id";
		$fnamesanslib="field_".$n."_".$search."_lib";
		$fnamelib="field_".$n."_".$search."_lib[]";
		$fname_name_aut_id="fieldvar_".$n."_".$search."[authority_id][]";
		$fname_aut_id="fieldvar_".$n."_".$search."_authority_id";
		$fnamevar_id = "";

		$authperso_id = 0;
		if($params['selector'] == 'authperso') {
			if($authperso_id_pos = strrpos($search,'_')) {
				$authperso_id = substr($search,$authperso_id_pos+1);
			}
			$fnamevar_id = "";
			$fnamevar_id_js = "";
		}
		$selector = $params['selector'];
		$p1 = $params['p1'];
		$p2 = $params['p2'];

		if( ($params['ajax'] == "categories" || $params['ajax'] == "categories_mul") && $opac_thesaurus == 1){
			$fnamevar_id = "linkfield=\"fieldvar_".$n."_".$search."[id_thesaurus][]\"";
			$fnamevar_id_js = "fieldvar_".$n."_".$search."[id_thesaurus][]";
			}else if(($params['ajax'] == "onto") || ($params['ajax'] == "concepts")){
			switch ($params['att_id_filter']) {
				case "http://www.w3.org/2004/02/skos/core#ConceptScheme" :
					$element = 'conceptscheme';
					break;
				default :
					$element = 'concept';
					break;
			}
			$selector .= "&dyn=4&element=".$element."&return_concept_id=1";
			if (!$params['att_id_filter']) {
				$params['att_id_filter'] = 'http://www.w3.org/2004/02/skos/core#Concept';
			}
			$fnamevar_id = "linkfield=\"fieldvar_".$n."_".$search."[id_scheme][]\" att_id_filter=\"".$params['att_id_filter']."\"";
			$fnamevar_id_js = "fieldvar_".$n."_".$search."[id_scheme][]";
		}else if($params['selector'] == "collection" || $params['selector'] == "subcollection"){
		    $selector .= "&selfrom=rmc";
		    $fnamevar_id = "";
		    $fnamevar_id_js = "";
		}else if($params['ajax'] == "vedette"){
			$selector .= "&grammars=notice_authors,tu_authors";
			$fnamevar_id = "linkfield=\"fieldvar_".$n."_".$search."[grammars][]\"";
			$fnamevar_id_js = "fieldvar_".$n."_".$search."[grammars][]";
		}else{
			$fnamevar_id = "";
			$fnamevar_id_js = "";
		}
		$op = $this->get_global_value("op_".$i."_".$search);
		$fieldvar=$this->get_global_value("fieldvar_".$i."_".$search);

		$v=$this->clean_completion_empty_values($v);
		$nb_values=count($v);
		if(!$nb_values){
			//Création de la ligne
			$nb_values=1;
		}
		$nb_max_aut=$nb_values-1;
		$r = "<span class='ui-panel-display'>";
		$r.= "<input type='hidden' id='$fnamesans"."_max_aut' value='".$nb_max_aut."'>";
		if ($params['selector'] != 'instruments') {
		    $r.= "<input class='bouton' value='...' id='$fnamesans"."_authority_selector' title='".htmlentities($msg['title_select_from_list'],ENT_QUOTES,$charset)."' onclick=\"openPopUp('./select.php?what=$selector&caller=search_form".($authperso_id ? "&authperso_id=".$authperso_id : "")."&$p1=".$fname_id."_0&$p2=".$fnamesanslib."_0&deb_rech=&callback=authoritySelected&infield=".$fnamesans."_0', 'selector')\" type=\"button\">";
    		$r.= "<input class='bouton' type='button' value='+' onclick='add_line(\"$fnamesans\", \"AUTHORITY\")'>";
		}
		$r.= "</span>";
		$r.= "<div id='el$fnamesans'>";
		for($inc=0;$inc<$nb_values;$inc++){
			if(!isset($v[$inc])) $v[$inc] = '';
			switch ($op) {
				case 'AUTHORITY':
					if ($params['selector'] == 'ontology') {
						// On vérifie si c'est un id transmis
						if ($v[$inc] && !is_numeric($v[$inc])) {
							// C'est une uri
							$v[$inc] = onto_common_uri::get_id($v[$inc]);
						}
					}
					if($v[$inc]!= 0){
						$libelle = self::get_authoritie_display($v[$inc], $params['selector']);
					}else{
						$libelle = "";
					}
					break;
				default:
					$libelle = $v[$inc];
					if ($params['selector'] == 'instruments') {
					    $libelle = nomenclature_instrument::get_instrument_name_from_id($v[$inc]);
					}
					break;
			}
			$r.="<input id='".$fnamesans."_".$inc."' name='$fname' value='".htmlentities($v[$inc],ENT_QUOTES,$charset)."' type='hidden' />";
			$r.="<span class='search_value'>
					<input autfield='".$fname_id."_".$inc."' onkeyup='fieldChanged(\"".$fnamesans."\",".$inc.",this.value,event);' callback='authoritySelected' completion='".$params['ajax']."' $fnamevar_id id='".$fnamesanslib."_".$inc."' name='$fnamelib' value='".htmlentities($libelle,ENT_QUOTES,$charset)."' type='text' class='".(!empty($fieldvar['authority_id'][$inc]) && ($op == "AUTHORITY") ? "authorities " : "")."saisie-20emr expand_completion' param2='1' param1='".($params['param1'] ?? "")."'/>
				</span>";
			$r.= "<input class='bouton vider' type='button' onclick='this.form.".$fnamesanslib."_".$inc.".value=\"\";this.form.".$fname_id."_".$inc.".value=\"0\";this.form.".$fname_aut_id."_".$inc.".value=\"0\";this.form.".$fnamesans."_".$inc.".value=\"0\"; enable_operator(\"".$fnamesans."\", \"".$i."\");' value='".htmlentities($msg['raz'], ENT_QUOTES, $charset)."'>";
			$r.= "<input type='hidden' id='".$fname_aut_id."_".$inc."' name='$fname_name_aut_id' value='".htmlentities($v[$inc],ENT_QUOTES,$charset)."' />";
			$r.= "<input type='hidden' name='".$fname_id."_".$inc."' id='".$fname_id."_".$inc."' value='".htmlentities($v[$inc],ENT_QUOTES,$charset)."' /><br>";
		}
		$r.= "</div>";
		if($nb_values>1){
			$r.="<script>
					document.getElementById('op_".$n."_".$search."').disabled=true;
					if(operators_to_enable.indexOf('op_".$n."_".$search."') === -1) {
						operators_to_enable.push('op_".$n."_".$search."');
					}
				</script>";
		}
		return $r;
	}

    protected function compare_option_list_field($opt, $start='') {
        if (!$start) {
            return true;
        }
        $converted_opt = convert_diacrit(pmb_strtolower($opt));
        $converted_start = convert_diacrit(pmb_strtolower($start));
        if (pmb_substr($converted_opt, 0, pmb_strlen($converted_start)) == $converted_start || (strpos($converted_opt, $converted_start) !== false)) {
            return true;
        }
        return false;
    }
    
	public function get_options_list_field($ff, $start='', $limit=0) {
		$list = array();
		switch ($ff["INPUT_TYPE"]) {
			case 'query_list':
			case 'checkbox_query_list':
				$requete=$ff["INPUT_OPTIONS"]["QUERY"][0]["value"];
				if (isset($ff["INPUT_OPTIONS"]["FILTERING"])) {
					if ($ff["INPUT_OPTIONS"]["FILTERING"] == "yes") {
						$requete = str_replace("!!acces_j!!", "", $requete);
						$requete = str_replace("!!statut_j!!", "", $requete);
						$requete = str_replace("!!statut_r!!", "", $requete);
					}
				}
				if(isset($ff["INPUT_OPTIONS"]["QUERY"][0]["USE_GLOBAL"])) {
					$use_global = explode(",", $ff["INPUT_OPTIONS"]["QUERY"][0]["USE_GLOBAL"]);
					for($j=0; $j<count($use_global); $j++) {
						$var_global = $use_global[$j];
						global ${$var_global};
						$requete = str_replace("!!".$var_global."!!", ${$var_global}, $requete);
					}
				}
				if(!empty($ff["INPUT_OPTIONS"]["QUERY"][0]["TRANSLATIONTABLENAME"]) && !empty($ff["INPUT_OPTIONS"]["QUERY"][0]["TRANSLATIONFIELDNAME"])) {
					$trans_table = $ff["INPUT_OPTIONS"]["QUERY"][0]["TRANSLATIONTABLENAME"];
					$trans_field = $ff["INPUT_OPTIONS"]["QUERY"][0]["TRANSLATIONFIELDNAME"];
				} else {
					$trans_table = '';
					$trans_field = '';
				}
				$resultat=pmb_mysql_query($requete);
				while ($opt=pmb_mysql_fetch_row($resultat)) {
				    //ajout du "contient"
                    if ($this->compare_option_list_field($opt[1], $start)) {
						if(!empty($ff["INPUT_OPTIONS"]["QUERY"][0]["GROUP_BY"])) {
							if(empty($list[$opt[2]])) {
								$list[$opt[2]] = array();
							}
							if(!empty($trans_table) && !empty($trans_field)) {
								$list[$opt[2]][$opt[0]] = translation::get_translated_text($opt[0], $trans_table, $trans_field, $opt[1]);
							} else {
								$list[$opt[2]][$opt[0]] = $opt[1];
							}
						} else {
							if(!empty($trans_table) && !empty($trans_field)) {
								$list[$opt[0]] = translation::get_translated_text($opt[0], $trans_table, $trans_field, $opt[1]);
							} else {
								$list[$opt[0]] = $opt[1];
							}
						}
					}
				}
				break;
			case 'list':
			case 'checkbox_list':
				$options=$ff["INPUT_OPTIONS"]["OPTIONS"][0];
				sort($options["OPTION"]);
				for ($i=0; $i<count($options["OPTION"]); $i++) {
					$label = get_msg_to_display($options["OPTION"][$i]["value"]);
                    if ($this->compare_option_list_field($label, $start)) {
						$list[$options["OPTION"][$i]["VALUE"]] = $label;
					}
				}
				break;
			case 'marc_list':
			case 'checkbox_marc_list':
				$options = clone marc_list_collection::get_instance($ff["INPUT_OPTIONS"]["NAME"][0]["value"]);
				$tmp = array();
				if (count($options->inverse_of)) {
				    // sous tableau genre ascendant descendant...
				    foreach ($options->table as $table) {
				        $tmp = $tmp + $table;
				    }
				    $options->table = $tmp;
				} else {
				    $tmp = $options->table;
				}
				$tmp=array_map("convert_diacrit",$tmp);//On enlève les accents
				$tmp=array_map("strtoupper",$tmp);//On met en majuscule
				asort($tmp);//Tri sur les valeurs en majuscule sans accent
				foreach ( $tmp as $key => $value ) {
					$tmp[$key]=$options->table[$key];//On reprend les bons couples clé / libellé
				}
				$options->table=$tmp;
				reset($options->table);

				// gestion restriction par code utilise.
				$existrestrict=false;
				$restrictqueryarray=array();
				if ($ff["INPUT_OPTIONS"]["RESTRICTQUERY"][0]["value"]) {
					$restrictquery=pmb_mysql_query($ff["INPUT_OPTIONS"]["RESTRICTQUERY"][0]["value"]);
					if ($restrictqueryrow=pmb_mysql_fetch_row($restrictquery)) {
						if ($restrictqueryrow[0]) {
							$restrictqueryarray=explode(",",$restrictqueryrow[0]);
							$existrestrict=true;
						}
					}
				}
				foreach ($options->table as $key => $val) {
                    if ($this->compare_option_list_field($val, $start)) {
						if ((!$existrestrict) || (array_search($key,$restrictqueryarray)!==false)) {
							$list[$key] = $val;
						}
					}
				}
				break;
		}
		if($limit) {
			$list = array_slice($list, 0, $limit, true);
		}
		return $list;
	}

	protected function get_variable_field($var_field,$n,$search,$var_table,$fieldvar) {
		global $charset, $msg;
		if (empty($fieldvar)) {
		    $fieldvar = array();
		}
		$variable_field = '';

		if ($var_field["TYPE"]=="input") {
			$varname=$var_field["NAME"];
			$visibility=1;
			if(isset($var_field["OPTIONS"]["VAR"][0])) {
				$vis=$var_field["OPTIONS"]["VAR"][0];
				if ($vis["NAME"]) {
					$vis_name=$vis["NAME"];
					global ${$vis_name};
					if ($vis["VISIBILITY"]=="no") $visibility=0;
					for ($k=0; $k<count($vis["VALUE"]); $k++) {
						if ($vis["VALUE"][$k]["value"]==${$vis_name}) {
							if ($vis["VALUE"][$k]["VISIBILITY"]=="no") $sub_vis=0; else $sub_vis=1;
							if ($vis["VISIBILITY"]=="no") $visibility|=$sub_vis; else $visibility&=$sub_vis;
							break;
						}
					}
				}
			}
			$vdefault=[];
			//Recherche de la valeur par defaut
			if(isset($var_field["OPTIONS"]["DEFAULT"][0])) {
				$vdefault=$var_field["OPTIONS"]["DEFAULT"][0];
			}
			if (count($vdefault)) {
				switch ($vdefault["TYPE"]) {
					case "var":
						$default=$var_table[$vdefault["value"]];
						break;
					case "value":
					default:
						$default=$vdefault["value"];
				}
			} else $default="";

			if ($visibility) {
				$variable_field.="<span class='ui-panel-display'>";
				$variable_field.="&nbsp;";
				if(isset($var_field["CLASS"]) && $var_field["CLASS"]) {
					$variable_field.="<span class='".$var_field["CLASS"]."'>";
				}
				if (isset($var_field["OPTIONS"]["INPUT"][0]["CLASS"]) && $var_field["OPTIONS"]["INPUT"][0]["CLASS"]) {
					$variable_field.="<span class='".$var_field["OPTIONS"]["INPUT"][0]["CLASS"]."'>";
				}
				if (isset($var_field["SPAN"]) && $var_field["SPAN"]) {
					$variable_field.="<span class='".$var_field["SPAN"]."'>".$var_field["COMMENT"]."</span>";
				} else {
					$variable_field.=htmlentities($var_field["COMMENT"], ENT_QUOTES, $charset);
				}
				$input=$var_field["OPTIONS"]["INPUT"][0];
				if(!empty($input["QUERY"][0]["TRANSLATIONTABLENAME"]) && !empty($input["QUERY"][0]["TRANSLATIONFIELDNAME"])) {
					$trans_table = $input["QUERY"][0]["TRANSLATIONTABLENAME"];
					$trans_field = $input["QUERY"][0]["TRANSLATIONFIELDNAME"];
				} else {
					$trans_table = '';
					$trans_field = '';
				}
				switch ($input["TYPE"]) {
					case "query_list":
						if ((!isset($fieldvar[$varname]) || !$fieldvar[$varname])&&($default)) $fieldvar[$varname][0]=$default;
						$variable_field.="&nbsp;<span class='search_value'><select id=\"fieldvar_".$n."_".$search."[".$varname."][]\" name=\"fieldvar_".$n."_".$search."[".$varname."][]\">\n";
						$query_list_result=pmb_mysql_query($input["QUERY"][0]["value"]);
						$var_tmp=$concat="";
						while ($line=pmb_mysql_fetch_array($query_list_result)) {
							if($concat)$concat.=",";
							$concat.=$line[0];
							$var_tmp.="<option value=\"".htmlentities($line[0],ENT_QUOTES,$charset)."\"";
							$as = false;
							if(array_key_exists($varname, $fieldvar) && is_array($fieldvar[$varname])) {
								$as = array_search($line[0],$fieldvar[$varname]);
							}
							if (($as!==false)&&($as!==NULL)) $var_tmp.=" selected";
							$var_tmp.=">";
							if(!empty($trans_table) && !empty($trans_field)) {
								$var_tmp.= translation::get_translated_text($line[0], $trans_table, $trans_field, $line[1]);
							} else {
								$var_tmp.= htmlentities($line[1],ENT_QUOTES,$charset);
							}
							$var_tmp.="</option>\n";
						}
						if($input["QUERY"][0]["ALLCHOICE"] == "yes"){
							$variable_field.="<option value=\"".htmlentities($concat,ENT_QUOTES,$charset)."\"";
							$as = false;
							if(array_key_exists($varname, $fieldvar) && is_array($fieldvar[$varname])) {
								$as = array_search($concat,$fieldvar[$varname]);
							}
							if (($as!==false)&&($as!==NULL)) $variable_field.=" selected";
							$variable_field.=">".htmlentities($msg[substr($input["QUERY"][0]["TITLEALLCHOICE"],4,strlen($input["QUERY"][0]["TITLEALLCHOICE"])-4)],ENT_QUOTES,$charset)."</option>\n";
						}
						$variable_field.=$var_tmp;
						$variable_field.="</select></span>";
						if (isset($var_field["OPTIONS"]["INPUT"][0]["CLASS"]) && $var_field["OPTIONS"]["INPUT"][0]["CLASS"]) {
							$variable_field.="</span>";
						}
						break;
					case "checkbox" :
						if(!isset($input["DEFAULT_ON"]) || !$input["DEFAULT_ON"]){
							if ((!isset($fieldvar[$varname]) || !$fieldvar[$varname])&&($default)) $fieldvar[$varname][0]=$default;
						} elseif(!isset($fieldvar[$input["DEFAULT_ON"]][0]) || !$fieldvar[$input["DEFAULT_ON"]][0]) $fieldvar[$varname][0] =$default;
						$variable_field.="&nbsp;<input type=\"checkbox\" name=\"fieldvar_".$n."_".$search."[".$varname."][]\" value=\"".$input["VALUE"][0]["value"]."\" ";
						if(!empty($fieldvar[$varname][0]) && $input["VALUE"][0]["value"] == $fieldvar[$varname][0]) $variable_field.="checked";
						$variable_field.="/>\n";
						if (isset($var_field["OPTIONS"]["INPUT"][0]["CLASS"]) && $var_field["OPTIONS"]["INPUT"][0]["CLASS"]) {
							$variable_field.="</span>";
						}
						break;
					case "radio" :
						if ((!isset($fieldvar[$varname]) || !$fieldvar[$varname])&&($default)) $fieldvar[$varname][0]=$default;
						foreach($input["OPTIONS"][0]["LABEL"] as $radio_value){
							$variable_field.="&nbsp;<input type=\"radio\" name=\"fieldvar_".$n."_".$search."[".$varname."][]\" value=\"".$radio_value["VALUE"]."\" ";
							if(!empty($fieldvar[$varname][0]) && $radio_value["VALUE"] == $fieldvar[$varname][0]) $variable_field.="checked";
							$variable_field.="/>".htmlentities($msg[substr($radio_value["value"],4,strlen($radio_value["value"])-4)],ENT_QUOTES,$charset);
						}
						$variable_field.="\n";
						if (isset($var_field["OPTIONS"]["INPUT"][0]["CLASS"]) && $var_field["OPTIONS"]["INPUT"][0]["CLASS"]) {
							$variable_field.="</span>";
						}
						break;
					case "hidden":
						if ((!isset($fieldvar[$varname]) || !$fieldvar[$varname])&&($default)) $fieldvar[$varname][0]=$default;
						if(isset($input["VALUE"][0]) && is_array($input["VALUE"][0])) $hidden_value=$input["VALUE"][0]["value"];
						else $hidden_value=$fieldvar[$varname][0];
						$variable_field.="<input type='hidden' id=\"fieldvar_".$n."_".$search."[".$varname."][]\" name=\"fieldvar_".$n."_".$search."[".$varname."][]\" value=\"".htmlentities($hidden_value,ENT_QUOTES,$charset)."\"/>";
						if (isset($var_field["OPTIONS"]["INPUT"][0]["CLASS"]) && $var_field["OPTIONS"]["INPUT"][0]["CLASS"]) {
							$variable_field.="</span>";
						}
						break;
					case "number":
					    if ((!isset($fieldvar[$varname]) || !$fieldvar[$varname])&&($default)) $fieldvar[$varname][0]=$default;
					    if(is_array($input["VALUE"][0])) $hidden_value=$input["VALUE"][0]["value"];
					    else $hidden_value=$fieldvar[$varname][0];
					    $variable_field.="<input type='number' id=\"fieldvar_".$n."_".$search."[".$varname."][]\" name=\"fieldvar_".$n."_".$search."[".$varname."][]\" value=\"".htmlentities($hidden_value,ENT_QUOTES,$charset)."\"/>";
					    if (isset($var_field["OPTIONS"]["INPUT"][0]["CLASS"]) && $var_field["OPTIONS"]["INPUT"][0]["CLASS"]) {
					        $variable_field.="</span>";
					    }
					    break;
				}
				if(isset($var_field["CLASS"]) && $var_field["CLASS"]) {
					$variable_field.="</span>";
				}
				$variable_field.="</span>";
			} else {
				if($vis["HIDDEN"] != "no")
					$variable_field.="<input type='hidden' name=\"fieldvar_".$n."_".$search."[".$varname."][]\" value=\"".htmlentities($default,ENT_QUOTES,$charset)."\"/>";
			}
		}

		return $variable_field;
	}

    protected function get_field($i,$n,$search,$pp) {
		global $charset;
		global $aff_list_empr_search;
		global $msg;
		global $include_path;
		global $thesaurus_classement_mode_pmb;
		global $pmb_map_base_layer_type;
		global $pmb_map_base_layer_params;
		global $pmb_map_size_search_edition, $pmb_map_bounding_box;

		$r="";
		$s=explode("_",$search);

		//Champ
		$v=$this->get_global_value("field_".$i."_".$search);
		if ($v=="") $v=array();

		$v1=$this->get_global_value("field_".$i."_".$search.'_1');
		if ($v1=="") $v1=array();

		//Variables
		$fieldvar=$this->get_global_value("fieldvar_".$i."_".$search);

		if ($s[0]=="f") {
			//Champs fixes
			$ff=$this->fixedfields[$s[1]];

			//Variables globales et input
			$vvar=array();
			$var_table=array();
			if (!empty($ff["VAR"])) {
    			for ($j=0; $j<count($ff["VAR"]); $j++) {
    				switch ($ff["VAR"][$j]["TYPE"]) {
    					case "input":
    						$valvar="fieldvar_".$i."_".$search."[\"".$ff["VAR"][$j]["NAME"]."\"]";
    						global ${$valvar};
    						$vvar[$ff["VAR"][$j]["NAME"]]=${$valvar};
    						if ($vvar[$ff["VAR"][$j]["NAME"]]=="") $vvar[$ff["VAR"][$j]["NAME"]]=array();
    						$var_table[$ff["VAR"][$j]["NAME"]]=$vvar[$ff["VAR"][$j]["NAME"]];
    						break;
    					case "global":
    						$global_name=$ff["VAR"][$j]["NAME"];
    						global ${$global_name};
    						$var_table[$ff["VAR"][$j]["NAME"]]=${$global_name};
    						break;
    				}
    			}
			}

			//Traitement des variables d'entree
			//Variables
			$r_top='';
			$r_bottom='';
			if(!empty($ff["VAR"])){
				for ($j=0; $j<count($ff["VAR"]); $j++) {
					if ($ff["VAR"][$j]["PLACE"]=='top') {
						$r_top .= $this->get_variable_field($ff["VAR"][$j],$n,$search,$var_table,$fieldvar);
					} else {
						$r_bottom .= $this->get_variable_field($ff["VAR"][$j],$n,$search,$var_table,$fieldvar);
					}
				}
			}

			//Affichage des variables ayant l'attribut place='top'
			$r.=$r_top;

			switch ($ff["INPUT_TYPE"]) {
				case "authoritie_external":
					$op = "op_".$i."_".$search;
					global ${$op};
					$libelle = "";
					if (${$op} == "AUTHORITY"){
						if($v[0]!= 0){
							$libelle = self::get_authoritie_display($v[0], $ff['INPUT_OPTIONS']['SELECTOR']);
						}
						${$op} == "BOOLEAN";
						$r.="<script>document.forms['search_form'].".$op.".options[0].selected=true;</script>";
					}

					if($libelle){
						$r.="<span class='search_value'><input type='text' name='field_".$n."_".$search."[]' value='".htmlentities($libelle,ENT_QUOTES,$charset)."' class='ext_search_txt'/></span>";
					}else{
						$r.="<span class='search_value'><input type='text' name='field_".$n."_".$search."[]' value='".htmlentities($v[0],ENT_QUOTES,$charset)."' class='ext_search_txt'/></span>";
					}
					break;
				case "authoritie":
					$params = array(
						'ajax' => $ff["INPUT_OPTIONS"]["AJAX"],
						'selector' => $ff["INPUT_OPTIONS"]["SELECTOR"],
						'p1' => $ff["INPUT_OPTIONS"]["P1"],
						'p2' => $ff["INPUT_OPTIONS"]["P2"],
						'att_id_filter' => (isset($ff["INPUT_OPTIONS"]["ATT_ID_FILTER"]) ? $ff["INPUT_OPTIONS"]["ATT_ID_FILTER"] : ''),
						'param1' => (isset($ff["INPUT_OPTIONS"]["PARAM1"]) ? $ff["INPUT_OPTIONS"]["PARAM1"] : '')
					);
					$r.= $this->get_completion_authority_field($i,$n,$search, $v, $params);
					break;
				case "text":
					$input_placeholder = '';
					if(isset($ff['INPUT_OPTIONS']['PLACEHOLDER'])) {
						if (substr($ff['INPUT_OPTIONS']["PLACEHOLDER"],0,4)=="msg:") {
							$input_placeholder = $msg[substr($ff['INPUT_OPTIONS']["PLACEHOLDER"],4,strlen($ff['INPUT_OPTIONS']["PLACEHOLDER"])-4)];
						} else {
							$input_placeholder = $ff['INPUT_OPTIONS']["PLACEHOLDER"];
						}
					}
					if(!isset($v[0])) $v[0] = '';
					$r.="<span class='search_value'><input type='text' name='field_".$n."_".$search."[]' value='".htmlentities($v[0],ENT_QUOTES,$charset)."' ".($input_placeholder?"placeholder='".htmlentities($input_placeholder,ENT_QUOTES,$charset)."' alt='".htmlentities($input_placeholder,ENT_QUOTES,$charset)."' title='".htmlentities($input_placeholder,ENT_QUOTES,$charset)."'":"")." class='ext_search_txt'/></span>";
					break;
				case "query_list":
				case "list":
				case "marc_list":
					if(isset($ff["INPUT_OPTIONS"]["COMPLETION"]) && $ff["INPUT_OPTIONS"]["COMPLETION"] == 'yes') {
						$params = array(
								'ajax' => $ff["INPUT_TYPE"],
								'selector' => $ff["INPUT_TYPE"],
								'p1' => 'p1',
								'p2' => 'p2'
						);
						$r.=$this->get_completion_selection_field($i,$n,$search, $v, $params);
					} else {
					    $multiple = 'multiple';
					    if (isset($ff["INPUT_OPTIONS"]["MULTIPLE"]) && $ff["INPUT_OPTIONS"]["MULTIPLE"] == 'no') {
					        $multiple = '';
					    }
					    $r.="<span class='search_value'><select name='field_".$n."_".$search."[]' $multiple size='5' class=\"ext_search_txt\">";
						$list = $this->get_options_list_field($ff);
						foreach ($list as $key=>$value) {
							if(is_array($value)) {
								if(!$key) {
									$key = $msg['classementGen_default_libelle'];
								}
								$r.="<optgroup label='".htmlentities($key,ENT_QUOTES,$charset)."'>";
								foreach($value as $sub_key=>$sub_value) {
									$r.="<option value='".htmlentities($sub_key,ENT_QUOTES,$charset)."' ";
									$as=array_search($sub_key,$v);
									if (($as!==null)&&($as!==false)) $r.=" selected";
									$r.=">".htmlentities($sub_value,ENT_QUOTES,$charset)."</option>";
								}
								$r.="</optgroup>";
							} else {
								$r.="<option value='".htmlentities($key,ENT_QUOTES,$charset)."' ";
								$as=array_search($key,$v);
								if (($as!==null)&&($as!==false)) $r.=" selected";
								$r.=">".htmlentities($value,ENT_QUOTES,$charset)."</option>";
							}
						}
						$r.="</select></span>";
					}
					break;
				case "checkbox_list":
				case "checkbox_marc_list":
				case "checkbox_query_list":
				    $r.="<span class='search_value'>";
				    $list = $this->get_options_list_field($ff);
				    foreach ($list as $key=>$value) {
				        $r.="<input type='checkbox' name='field_".$n."_".$search."[]' value='".htmlentities($key,ENT_QUOTES,$charset)."' ";
				        $as=array_search($key,$v);
				        if (($as!==null)&&($as!==false)) $r.=" checked='checked'";
				        $r.=" />&nbsp;".htmlentities($value,ENT_QUOTES,$charset);
				    }
				    $r.="</span>";
				    break;
				case "date":
					$field=array();
					$op = "op_".$i."_".$search;
					global ${$op};
					$field['OP'] = ${$op};
					if(!isset($v[0])) $v[0] = '';
					$field['VALUES'][0]=$v[0];
					if(!isset($v1[0])) $v1[0] = '';
					$field['VALUES1'][0]=$v1[0];
					$r.="<span class='search_value'>".$aff_list_empr_search['date_box']($field, $check_scripts, "field_".$n."_".$search)."</span>";
					break;
				case "map" :
					$layer_params = json_decode($pmb_map_base_layer_params,true);
					$baselayer =  "baseLayerType: dojox.geo.openlayers.BaseLayerType.".$pmb_map_base_layer_type;
					if(!empty($layer_params) && count($layer_params)){
						if($layer_params['name']) $baselayer.=",baseLayerName:\"".$layer_params['name']."\"";
						if($layer_params['url']) $baselayer.=",baseLayerUrl:\"".$layer_params['url']."\"";
						if($layer_params['options']) $baselayer.=",baseLayerOptions:".json_encode($layer_params['options']);
					}
					$initialFit = '';
					if(!count($v)) {
						if( $pmb_map_bounding_box) {
							$map_bounding_box = $pmb_map_bounding_box;
						} else {
							$map_bounding_box = '-5 50,9 50,9 40,-5 40,-5 50';
						}
						$map_hold = new map_hold_polygon("bounding", 0, "polygon((".$map_bounding_box."))");
						if ($map_hold) {
							$coords = $map_hold->get_coords();
							$initialFit = explode(',', map_objects_controler::get_coord_initialFit($coords));
						} else{
							$initialFit = array(0, 0, 0, 0);
						}
					}
					$size=explode("*",$pmb_map_size_search_edition);
					if(count($size)!=2) {
						$map_size="width:800px; height:480px;";
					} else {
						if (is_numeric($size[0])) $size[0].= 'px';
						if (is_numeric($size[1])) $size[1].= 'px';
						$map_size= "width:".$size[0]."; height:".$size[1].";";
					}
					$map_holds=array();
					foreach($v as $map_hold){
						$map_holds[] = array(
								"wkt" => $map_hold,
								"type"=> "search",
								"color"=> null,
								"objects"=> array()
						);
					}
					$r.="<div id='map_search_".$n."_".$search."' data-dojo-type='apps/map/map_controler' style='$map_size' data-dojo-props='".$baselayer.",mode:\"search_criteria\",hiddenField:\"field_".$n."_".$search."\",initialFit:".json_encode($initialFit,true).",searchHolds:".json_encode($map_holds,true)."'></div>";

					break;
			}
			//Affichage des variables n'ayant pas l'attribut place='top'
			$r.=$r_bottom;
		} elseif (array_key_exists($s[0],$this->pp)) {
			//Recuperation du champ
			$field=array();
			$field['ID']=$s[1];
			$field['NAME']=$this->pp[$s[0]]->t_fields[$s[1]]['NAME'];
			$field['NUMBER']=$n;
			$field['MANDATORY']=$this->pp[$s[0]]->t_fields[$s[1]]['MANDATORY'];
			$field['ALIAS']=$this->pp[$s[0]]->t_fields[$s[1]]['TITRE'];
			$field['DATATYPE']=$this->pp[$s[0]]->t_fields[$s[1]]['DATATYPE'];
			$field['OPTIONS']=$this->pp[$s[0]]->t_fields[$s[1]]['OPTIONS'];
			$field['VALUES']=$v;
			$field['VALUES1']=$v1;
			$field['PREFIX']=$this->pp[$s[0]]->prefix;
			switch ($this->pp[$s[0]]->t_fields[$s[1]]['DATATYPE']) {
				case "date":
					$op = "op_".$i."_".$search;
					global ${$op};
					$field['OP'] = ${$op};
					break;
			}
			if(!empty($aff_list_empr_search[$this->pp[$s[0]]->t_fields[$s[1]]['TYPE']])) {
				$r="<span class='search_value'>".$aff_list_empr_search[$this->pp[$s[0]]->t_fields[$s[1]]['TYPE']]($field, $check_scripts, "field_".$n."_".$search)."</span>";
			}
		} elseif ($s[0]=="authperso") {
			$params = array(
 					'ajax' => $s[0].'_'.$s[1],
 					'selector' => $s[0],
 					'p1' => 'p1',
 					'p2' => 'p2'
 			);
 			$r = $this->get_completion_authority_field($i,$n,$search, $v, $params);
		}elseif ($s[0]=="s") {
			//appel de la fonction get_input_box de la classe du champ special
			$type=$this->specialfields[$s[1]]["TYPE"];
			for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
				if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
					$sf=$this->specialfields[$s[1]];
					if ($this->full_path && file_exists($this->full_path."/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php"))
						require_once($this->full_path."/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
					else
						require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
					$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$n,$sf,$this);
					$r=$specialclass->get_input_box();
					break;
				}
			}
		}
		return $r;
	}

	public function make_search($prefixe="") {
		global $search;
		global $msg;
		global $include_path;
		global $pmb_search_stemming_active;
		global $default_tmp_storage_engine;

		$this->error_message="";
		$main="";
		$last_table="";
	   	$field_keyName=$this->keyName;
	   	$field_tableName=$this->tableName;

		//Pour chaque champ
		if(is_array($search) && count($search)){
			for ($i=0; $i<count($search); $i++) {
				//construction de la requete
				$s=explode("_",$search[$i]);

				//Recuperation de l'operateur
				$op="op_".$i."_".$search[$i];
				global ${$op};

				//Recuperation du contenu de la recherche
				$field_="field_".$i."_".$search[$i];
				global ${$field_};
				$field=${$field_};

				$field1_="field_".$i."_".$search[$i].'_1';
				global ${$field1_};
				$field1=${$field1_};

				//Recuperation de l'operateur inter-champ
				$inter="inter_".$i."_".$search[$i];
				global ${$inter};

				//Recuperation des variables auxiliaires
				$fieldvar_="fieldvar_".$i."_".$search[$i];
				global ${$fieldvar_};
				$fieldvar=${$fieldvar_};

				//Si c'est un champ fixe
				if ($s[0]=="f") {
					$ff=$this->fixedfields[$s[1]];

					//Choix du moteur
					if ($this->memory_engine_allowed && !$ff['MEMORYENGINEFORBIDDEN'] ) {
						$this->current_engine = 'MEMORY';
					} else {
					    $this->current_engine = $default_tmp_storage_engine;
					}

					//Calcul des variables
					$var_table=array();
					if(is_array($ff["VAR"]) && count($ff["VAR"])){
						for ($j=0; $j<count($ff["VAR"]); $j++) {
							switch ($ff["VAR"][$j]["TYPE"]) {
								case "input":
									if( !empty($fieldvar[$ff["VAR"][$j]["NAME"]]) && is_array($fieldvar[$ff["VAR"][$j]["NAME"]]) ) {
										$var_table[$ff["VAR"][$j]["NAME"]] = implode(",",$fieldvar[$ff["VAR"][$j]["NAME"]]);
									}
									break;
								case "global":
									$global_name=$ff["VAR"][$j]["NAME"];
									global ${$global_name};
									$var_table[$ff["VAR"][$j]["NAME"]]=${$global_name};
									break;
								case "calculated":
									$calc=$ff["VAR"][$j]["OPTIONS"]["CALC"][0];
									switch ($calc["TYPE"]) {
										case "value_from_query":
											$query_calc=$calc["QUERY"][0]["value"];
											@reset($var_table);
											foreach ($var_table as $var_name => $var_value) {
												$query_calc=str_replace("!!".$var_name."!!",$var_value,$query_calc);
											}
											$r_calc=pmb_mysql_query($query_calc);
											$var_table[$ff["VAR"][$j]["NAME"]]=pmb_mysql_result($r_calc,0,0);
											break;
									}
									break;
							}
						}
					}
					$q_index=$ff["QUERIES_INDEX"];
					//Recuperation de la requete associee au champ et a l'operateur
					$q=$ff["QUERIES"][$q_index[${$op}]];

					//Si c'est une requete conditionnelle, on sélectionne la bonne requete et on supprime les autres
					if(isset($q[0]["CONDITIONAL"]) && $q[0]["CONDITIONAL"]){
						$k_default=0;
						$q_temp = array();
						$q_temp["OPERATOR"]=$q["OPERATOR"];
						for($k=0; $k<count($q)-1;$k++){
							if($var_table[$q[$k]["CONDITIONAL"]["name"]]== $q[$k]["CONDITIONAL"]["value"]) break;
							if ($q[$k]["CONDITIONAL"]["value"] == "default") $k_default=$k;
						}
						if($k == count($q)-1) $k=$k_default;
						$q_temp[0] = $q[$k];
						$q= $q_temp;
					}

					//Remplacement par les variables eventuelles pour chaque requete
					if(is_array($q) && count($q)){
						for ($k=0; $k<count($q)-1; $k++) {
							reset($var_table);
							foreach ($var_table as $var_name => $var_value) {
								$q[$k]["MAIN"]=str_replace("!!".$var_name."!!",$var_value,$q[$k]["MAIN"]);
								$q[$k]["MULTIPLE_TERM"]=str_replace("!!".$var_name."!!",$var_value,$q[$k]["MULTIPLE_TERM"]);
							}
						}
					}
					$last_main_table="";

					// pour les listes, si un opérateur permet une valeur vide, il en faut une...
					if(isset($this->op_empty[${$op}]) && $this->op_empty[${$op}] && !is_array($field) ){
						$field = array();
						$field[0] = "";
					}
					if (isset($this->op_empty[${$op}]) && !$this->op_empty[${$op}]) {
						// nettoyage des valeurs
						if (${$op}=='AUTHORITY') {
							$field = $this->clean_completion_empty_values($field);
						} else {
							$field = $this->clean_empty_values($field);
						}
					}

					//Pour chaque valeur du champ
					if(is_array($field) && count($field)){
						for ($j=0; $j<count($field); $j++) {
							//Pour chaque requete
							$field_origine=$field[$j];
							if (!empty($q)) {
    							for ($z=0; $z<count($q)-1; $z++) {
    								//Si le nettoyage de la saisie est demande
    								if($q[$z]["KEEP_EMPTYWORD"])	$field[$j]=strip_empty_chars($field_origine);
    								elseif ($q[$z]["REGDIACRIT"]) $field[$j]=strip_empty_words($field_origine);
    								elseif ($q[$z]["DETECTDATE"])  {
    									$field[$j]=detectFormatDate($field_origine,$q[$z]["DETECTDATE"]);
    								}
    								else $field[$j]=$field_origine;
    								$main=$q[$z]["MAIN"];
    								//Si il y a plusieurs termes possibles on construit la requete avec le terme !!multiple_term!!
    								if ($q[$z]["MULTIPLE_WORDS"]) {
    									$terms=explode(" ",$field[$j]);
    									//Pour chaque terme,
    									$multiple_terms=array();
    									for ($k=0; $k<count($terms); $k++) {
    										$terms[$k]=str_replace('*', '%', $terms[$k]);
    										$multiple_terms[]=str_replace("!!p!!",$terms[$k],$q[$z]["MULTIPLE_TERM"]);
    									}
    									$final_term=implode(" ".$q[$z]["MULTIPLE_OPERATOR"]." ",$multiple_terms);
    									$main=str_replace("!!multiple_term!!",$final_term,$main);
    								//Si la saisie est un ISBN
    								} else if ($q[$z]["ISBN"]) {
    									//Code brut
    									$terms[0]=$field[$j];
    									//EAN ?
    									if (isEAN($field[$j])) {
    										//C'est un isbn ?
    										if (isISBN($field[$j])) {
    											$rawisbn = preg_replace('/-|\.| /', '', $field[$j]);
    											//On envoi tout ce qu'on sait faire en matiere d'ISBN, en raw et en formatte, en 10 et en 13
    											$terms[1]=formatISBN($rawisbn,10);
    											$terms[2]=formatISBN($rawisbn,13);
    											$terms[3]=preg_replace('/-|\.| /', '', $terms[1]);
    											$terms[4]=preg_replace('/-|\.| /', '', $terms[2]);
    										}
    									}
    									else if (isISBN($field[$j])) {
    										$rawisbn = preg_replace('/-|\.| /', '', $field[$j]);
    										//On envoi tout ce qu'on sait faire en matiere d'ISBN, en raw et en formatte, en 10 et en 13
    										$terms[1]=formatISBN($rawisbn,10);
    										$terms[2]=formatISBN($rawisbn,13);
    										$terms[3]=preg_replace('/-|\.| /', '', $terms[1]);
    										$terms[4]=preg_replace('/-|\.| /', '', $terms[2]);
    									}
    									//Pour chaque terme,
    									$multiple_terms=array();
    									for ($k=0; $k<count($terms); $k++) {
    										$terms[$k]=str_replace('*', '%', $terms[$k]);
    										$multiple_terms[]=str_replace("!!p!!",$terms[$k],$q[$z]["MULTIPLE_TERM"]);
    									}
    									$final_term=implode(" ".$q[$z]["MULTIPLE_OPERATOR"]." ",$multiple_terms);
    									$main=str_replace("!!multiple_term!!",$final_term,$main);
    								} else if ($q[$z]["BOOLEAN"]) {
    									if($q[$z]['STEMMING']){
    										$stemming = $pmb_search_stemming_active;
    									}else{
    										$stemming = 0;
    									}
    									$aq=new analyse_query($field[$j],0,0,1,0,$stemming);
    									$aq1=new analyse_query($field[$j],0,0,1,1,$stemming);
    									if (isset($q[$z]["KEEP_EMPTY_WORDS_FOR_CHECK"]) && $q[$z]["KEEP_EMPTY_WORDS_FOR_CHECK"]) $err=$aq1->error; else $err=$aq->error;
    									if (!$err) {
    										if (is_array($q[$z]["TABLE"])) {
    											for ($z1=0; $z1<count($q[$z]["TABLE"]); $z1++) {
    												$is_fulltext=false;
    												if (isset($q[$z]["FULLTEXT"][$z1]) && $q[$z]["FULLTEXT"][$z1]) $is_fulltext=true;
    												if (!isset($q[$z]["KEEP_EMPTY_WORDS"][$z1]) || !$q[$z]["KEEP_EMPTY_WORDS"][$z1])
    													$members=$aq->get_query_members($q[$z]["TABLE"][$z1],$q[$z]["INDEX_L"][$z1],$q[$z]["INDEX_I"][$z1],$q[$z]["ID_FIELD"][$z1],$q[$z]["RESTRICT"][$z1],0,0,$is_fulltext);
    												else $members=$aq1->get_query_members($q[$z]["TABLE"][$z1],$q[$z]["INDEX_L"][$z1],$q[$z]["INDEX_I"][$z1],$q[$z]["ID_FIELD"][$z1],$q[$z]["RESTRICT"][$z1],0,0,$is_fulltext);
    												$main=str_replace("!!pert_term_".($z1+1)."!!",$members["select"],$main);
    												$main=str_replace("!!where_term_".($z1+1)."!!",$members["where"],$main);
    											}
    										} else {
    											$is_fulltext=false;
    											if (isset($q[$z]["FULLTEXT"]) && $q[$z]["FULLTEXT"]) $is_fulltext=true;
    											if (isset($q[$z]["KEEP_EMPTY_WORDS"]) && $q[$z]["KEEP_EMPTY_WORDS"])
    												$members=$aq1->get_query_members($q[$z]["TABLE"],$q[$z]["INDEX_L"],$q[$z]["INDEX_I"],$q[$z]["ID_FIELD"],(!empty($q[$z]["RESTRICT"]) ? $q[$z]["RESTRICT"] : ''),0,0,$is_fulltext);
    											else $members=$aq->get_query_members($q[$z]["TABLE"],$q[$z]["INDEX_L"],$q[$z]["INDEX_I"],$q[$z]["ID_FIELD"],(!empty($q[$z]["RESTRICT"]) ? $q[$z]["RESTRICT"] : ''),0,0,$is_fulltext);
    											$main=str_replace("!!pert_term!!",$members["select"],$main);
    											$main=str_replace("!!where_term!!",$members["where"],$main);
    										}
    									} else {
    										$main="select ".$field_keyName." from ".$this->tableName." where ".$field_keyName."=0";
    										$this->error_message=sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message);
    									}
    								} elseif (!empty($q[$z]["WORD"])) {
    									// Pour savoir si la recherche tous champs inclut les docnum
    									global $multi_crit_indexation_docnum_allfields;

    								    $multi_crit_indexation_docnum_allfields = -1;
    									if (!empty($var_table["is_num"])) {
    									    $multi_crit_indexation_docnum_allfields = 1;
    									}

    									// Pour savoir si la recherche inclut les oeuvres
    	    							global $multi_crit_indexation_oeuvre_title;

    								    $multi_crit_indexation_oeuvre_title = -1;
    									if (!empty($var_table["oeuvre_query"])) {
    									    $multi_crit_indexation_oeuvre_title = 1;
    									}

    									if(isset($q[$z]['TYPE']) && $q[$z]['TYPE']){
        									$mode = '';
        									if(isset($q[$z]['MODE'])){
        										$mode = $q[$z]['MODE'];
        									}
        									if($q[$z]["FIELDS"]){
        										$searcher = searcher_factory::get_searcher($q[$z]['TYPE'], $mode,$field[$j],$q[$z]["FIELDS"]);
        									}else{
        										$searcher = searcher_factory::get_searcher($q[$z]['TYPE'], $mode, $field[$j]);
    	    								}
    	    							}else{
    										//recherche par terme...
    										if($q[$z]["FIELDS"]){
    											$searcher = new $q[$z]['CLASS']($field[$j],$q[$z]["FIELDS"]);
    										}else{
    											$searcher = new $q[$z]['CLASS']($field[$j]);
    										}
    	    							}
    									if (!empty($var_table)) {
    									    $searcher->add_var_table($var_table);
    									}
    									if(isset($q[$z]['FIELDSRESTRICT']) && is_array($q[$z]['FIELDSRESTRICT'])) {
    										$searcher->add_fields_restrict($q[$z]['FIELDSRESTRICT']);
    									}
    									$main = $searcher->get_full_query();
    								}elseif ($q[$z]['CUSTOM_SEARCH']) {
    								    if (isset($this->op_special[${$op}]) && $this->op_special[${$op}]) {
    								        $table_tempo = $this->custom_search_op_special($ff, ${$op}, $i, $search[$i]);
    								    }
    								    if (!empty($table_tempo)) {
    								        $main = str_replace("!!table_tempo!!", $table_tempo, $main);
    								    }
    								}else{
    									$field[$j]=str_replace('*', '%', $field[$j]);
    									$main=str_replace("!!p!!",addslashes($field[$j]),$main);
    									$main=str_replace("!!p1!!",(isset($field1[$j]) ? addslashes($field1[$j]) : ''),$main);
    								}
    								//Y-a-t-il une close repeat ?
    								if (isset($q[$z]["REPEAT"]) && $q[$z]["REPEAT"]) {
    									//Si oui, on repete !!
    									$onvals=$q[$z]["REPEAT"]["ON"];
    									global ${$onvals};
    									$onvalst=explode($q[$z]["REPEAT"]["SEPARATOR"],${$onvals});
    									$mains=array();
    									for ($ir=0; $ir<count($onvalst); $ir++) {
    										$mains[]=str_replace("!!".$q[$z]["REPEAT"]["NAME"]."!!",$onvalst[$ir],$main);
    									}
    									$main=implode(" ".$q[$z]["REPEAT"]["OPERATOR"]." ",$mains);
    									$main="select * from (".$main.") as sbquery".($q[$z]["REPEAT"]["ORDERTERM"]?" order by ".$q[$z]["REPEAT"]["ORDERTERM"]:"");
    								}
    								if ($z<(count($q)-2)) pmb_mysql_query($main);
    							}
							}

							if(isset($fieldvar["operator_between_multiple_authorities"])){
								$operator=$fieldvar["operator_between_multiple_authorities"][0];
							} elseif(isset($q["DEFAULT_OPERATOR"])){
								$operator=$q["DEFAULT_OPERATOR"];
							} else {
								$operator = ($this->get_multi_search_operator()?$this->get_multi_search_operator():"or");
							}
							if (count($field)>1) {
								$suffixe = $i."_".$j;
								if($operator == "or"){
									//Ou logique si plusieurs valeurs
									if ($prefixe) {
										$this->gen_temporary_table($prefixe."mf_".$suffixe, $main);
									} else {
										$this->gen_temporary_table("mf_".$suffixe, $main);
									}

									if ($last_main_table) {
										if ($prefixe) {
											$requete="insert ignore into ".$prefixe."mf_".$suffixe." select ".$last_main_table.".* from ".$last_main_table;
										} else {
											$requete="insert ignore into mf_".$suffixe." select ".$last_main_table.".* from ".$last_main_table;
										}
										pmb_mysql_query($requete);
										//pmb_mysql_query("drop table if exists mf_".$suffixe);
										pmb_mysql_query("drop table if exists ".$last_main_table);
									} //else pmb_mysql_query("drop table if exists mf_".$suffixe);
									if ($prefixe) {
										$last_main_table=$prefixe."mf_".$suffixe;
									} else {
										$last_main_table="mf_".$suffixe;
									}
								} elseif($operator == "and"){
									//ET logique si plusieurs valeurs
									if ($prefixe) {
										$this->gen_temporary_table($prefixe."mf_".$suffixe, $main);
									} else {
										$this->gen_temporary_table("mf_".$suffixe, $main);
									}

									if ($last_main_table) {
										if($j>1){
											$search_table=$last_main_table;
										}else{
											$search_table=$last_tables;
										}
										if ($prefixe) {
										    $requete = $this->generate_query_op_and($prefixe, $suffixe, $search_table);
										} else {
										    $requete = $this->generate_query_op_and("", $suffixe, $search_table);
										}
										pmb_mysql_query($requete);
										pmb_mysql_query("drop table if exists ".$last_tables);

									}
									if ($prefixe) {
										$last_tables=$prefixe."mf_".$suffixe;
									} else {
										$last_tables="mf_".$suffixe;
									}
									if ($prefixe) {
										$last_main_table = $prefixe."and_result_".$suffixe;
									} else {
										$last_main_table = "and_result_".$suffixe;
									}
								}
							} //else print $main;
						}
					}
					if ($last_main_table){
						$main="select * from ".$last_main_table;
					}
				} elseif (array_key_exists($s[0],$this->pp)) {
					$datatype=$this->pp[$s[0]]->t_fields[$s[1]]["DATATYPE"];
					$df=$this->dynamicfields[$s[0]]["FIELD"][$this->get_id_from_datatype($datatype,$s[0])];
					$q_index=$df["QUERIES_INDEX"];
					$q=$df["QUERIES"][$q_index[${$op}]];

					//Choix du moteur
					if ($this->memory_engine_allowed && !$df['MEMORYENGINEFORBIDDEN'] ) {
						$this->current_engine = 'MEMORY';
					} else {
					    $this->current_engine = $default_tmp_storage_engine;
					}

					//Pour chaque valeur du champ
					$last_main_table="";
					if(is_countable($field)) {
    					if (count($field)==0) $field[0]="";
    					for ($j=0; $j<count($field); $j++) {
    						//appel de la classe dynamique associée au type de champ s'il y en a une
    						if(file_exists($include_path."/search_queries/dynamics/dynamic_search_".$this->pp[$s[0]]->t_fields[$s[1]]['TYPE'].".class.php")) {
    							require_once($include_path."/search_queries/dynamics/dynamic_search_".$this->pp[$s[0]]->t_fields[$s[1]]['TYPE'].".class.php");
    							$dynamic_class_name = "dynamic_search_".$this->pp[$s[0]]->t_fields[$s[1]]['TYPE'];
    							$dynamic_class = new $dynamic_class_name($s[1],$s[0], $i,$df,$this);
    							$main = $dynamic_class->get_query($field[$j], $field1[$j]);
    						} else {
    							if($q["KEEP_EMPTYWORD"]) $field[$j]=strip_empty_chars($field[$j]);
    							elseif ($q["REGDIACRIT"]) $field[$j]=strip_empty_words($field[$j]);
    							$main=$q["MAIN"];
    							//Si il y a plusieurs termes possibles
    							if ($q["MULTIPLE_WORDS"]) {
    								$terms=explode(" ",$field[$j]);
    								//Pour chaque terme
    								$multiple_terms=array();
    								for ($k=0; $k<count($terms); $k++) {
    									$terms[$k]=str_replace('*', '%', $terms[$k]);
    									$mt=str_replace("!!p!!",addslashes($terms[$k]),$q["MULTIPLE_TERM"]);
    									$mt=str_replace("!!field!!",$s[1],$mt);
    									$multiple_terms[]=$mt;
    								}
    								$final_term=implode(" ".$q["MULTIPLE_OPERATOR"]." ",$multiple_terms);
    								$main=str_replace("!!multiple_term!!",$final_term,$main);
    							}elseif ($q["WORD"]){
    								if(isset($q['TYPE']) && $q['TYPE']){
    									$mode = '';
    									if(isset($q['MODE'])){
    										$mode = $q['MODE'];
    									}
    									if($q["FIELDS"]){
    										$searcher = searcher_factory::get_searcher($q['TYPE'], $mode,$field[$j],$q["FIELDS"]);
    									}else{
    										$searcher = searcher_factory::get_searcher($q['TYPE'], $mode, $field[$j], $s[1]);
    									}
    								}else{
    									//recherche par terme...
    									if($q["FIELDS"]){
    										$searcher = new $q['CLASS']($field[$j],$q["FIELDS"]);
    									}else{
    										$searcher = new $q['CLASS']($field[$j]);
    									}
    								}
    								//cas particulier des CP, on ne peut pas générer en statique dans le XML le fieldrestrict.
    								// on le gère donc dynamiquement ici pour limiter la recherche au CP concerné
    								$q['FIELDSRESTRICT'] = [
    								    [
    								        'field' => 'code_champ',
    								        'values'=> [100],
    								        'op' => 'and',
    								        'not' => '',
    								        'sub' => [
    								            [
                                	                'sub_field'=> 'code_ss_champ',
    							    	            'values' => [str_pad($s[1], 2, "0", STR_PAD_LEFT)],
    							        	        'op' => 'and',
    							            	    'not' => '',
    
    								            ]
    								        ]
            	                        ]
    								];
    								if(isset($q['FIELDSRESTRICT']) && is_array($q['FIELDSRESTRICT'])) {
    									$searcher->add_fields_restrict($q['FIELDSRESTRICT']);
    								}
    								$main = $searcher->get_full_query();
    							} else {
    								$field[$j]=str_replace('*', '%', $field[$j]);
    								$main=str_replace("!!p!!",addslashes($field[$j]),$main);
    								$main=str_replace("!!p1!!",(isset($field1[$j]) ? addslashes($field1[$j]) : ''),$main);
    							}
    							$main=str_replace("!!field!!",$s[1],$main);
    
    						}
    						//Choix de l'operateur dans la liste
    						if(isset($q["DEFAULT_OPERATOR"])){
    							$operator=$q["DEFAULT_OPERATOR"];
    						} else {
    							$operator = ($this->get_multi_search_operator()?$this->get_multi_search_operator():"or");
    						}
    						if (count($field)>1) {
    							$suffixe = $i."_".$j;
    							if($operator == "or"){
    								//Ou logique si plusieurs valeurs
    								if ($prefixe) {
    									$this->gen_temporary_table($prefixe."mf_".$suffixe, $main);
    								} else {
    									$this->gen_temporary_table("mf_".$suffixe, $main);
    								}
    
    								if ($last_main_table) {
    									if ($prefixe) {
    										$requete="insert ignore into ".$prefixe."mf_".$suffixe." select ".$last_main_table.".* from ".$last_main_table;
    									} else {
    										$requete="insert ignore into mf_".$suffixe." select ".$last_main_table.".* from ".$last_main_table;
    									}
    									pmb_mysql_query($requete);
    									//pmb_mysql_query("drop table if exists mf_".$suffixe);
    									pmb_mysql_query("drop table if exists ".$last_main_table);
    								} //else pmb_mysql_query("drop table if exists mf_".$suffixe);
    								if ($prefixe) {
    									$last_main_table=$prefixe."mf_".$suffixe;
    								} else {
    									$last_main_table="mf_".$suffixe;
    								}
    							} elseif($operator == "and"){
    								//ET logique si plusieurs valeurs
    								if ($prefixe) {
    									$this->gen_temporary_table($prefixe."mf_".$suffixe, $main);
    								} else {
    									$this->gen_temporary_table("mf_".$suffixe, $main);
    								}
    
    								if ($last_main_table) {
    									if($j>1){
    										$search_table=$last_main_table;
    									}else{
    										$search_table=$last_tables;
    									}
    									if ($prefixe) {
    									    $requete = $this->generate_query_op_and($prefixe, $suffixe, $search_table);
    									} else {
    									    $requete = $this->generate_query_op_and("", $suffixe, $search_table);
    									}
    									pmb_mysql_query($requete);
    									pmb_mysql_query("drop table if exists ".$last_tables);
    
    								}
    								if ($prefixe) {
    									$last_tables=$prefixe."mf_".$suffixe;
    								} else {
    									$last_tables="mf_".$suffixe;
    								}
    								if ($prefixe) {
    									$last_main_table = $prefixe."and_result_".$suffixe;
    								} else {
    									$last_main_table = "and_result_".$suffixe;
    								}
    							}
    						} //else print $main;
    					}
					}

					if ($last_main_table) {
						$main="select * from ".$last_main_table;
					}
				} elseif ($s[0]=="s") {
					//instancier la classe de traitement du champ special
					$type=$this->specialfields[$s[1]]["TYPE"];
					if ($type=="facette") {
						//Traitement final
						if (!empty($search_previous_table)) {
							if(!empty($tsearched_sources)) {
								for ($i=0; $i<count($tsearched_sources); $i++) {
									$requete = "delete $last_table from $last_table join entrepot_source_".$tsearched_sources[$i]." on recid = notice_id where notice_id NOT IN (SELECT notice_id FROM $search_previous_table)";
									pmb_mysql_query($requete);
								}
							} else {
								$requete="insert ignore into $last_table (notice_id,pert) select notice_id,pert from $search_previous_table where notice_id NOT IN (SELECT notice_id FROM $last_table)";
								pmb_mysql_query($requete);
							}
							$search_previous_table="";
						}
					}
					if (!empty($this->tableau_speciaux['TYPE'])) {
    					for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
    						if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
    							$sf=$this->specialfields[$s[1]];
    							require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
    							$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$i,$sf,$this);
    							if(method_exists($specialclass, 'set_xml_file')){
    							    $specialclass->set_xml_file($this->fichier_xml);
    							}
    							$last_main_table=$specialclass->make_search();
    							break;
    						}
    					}
					}
					if (!empty($last_main_table)) {
					    $main="select * from ".$last_main_table;
					} else {
					    if (empty($main)) {
					        continue;
					    }
					}
				} elseif ($s[0]=="authperso") {
					//on est sur le cas de la recherche "Tous les champs" de l'autorité perso
    				//$s["1"] vaut l'identifiant du type d'autorité perso
    				$df=$this->dynamicfields["a"]["FIELD"]["10"];
    				$q_index=$df["QUERIES_INDEX"];
	    			$q=$df["QUERIES"][$q_index[${$op}]];

					//Choix du moteur
					if ($this->memory_engine_allowed && !$df['MEMORYENGINEFORBIDDEN'] ) {
						$this->current_engine = 'MEMORY';
					} else {
					    $this->current_engine = $default_tmp_storage_engine;
					}

					//Pour chaque valeur du champ
					$last_main_table="";
					if (count($field)==0) $field[0]="";
					for ($j=0; $j<count($field); $j++) {
						if($q["KEEP_EMPTYWORD"]) $field[$j]=strip_empty_chars($field[$j]);
						elseif ($q["REGDIACRIT"]) $field[$j]=strip_empty_words($field[$j]);
						$main=$q["MAIN"];
						//Si il y a plusieurs termes possibles
						if ($q["MULTIPLE_WORDS"]) {
							$terms=explode(" ",$field[$j]);
							//Pour chaque terme
							$multiple_terms=array();
							for ($k=0; $k<count($terms); $k++) {
								$terms[$k]=str_replace('*', '%', $terms[$k]);
								$mt=str_replace("!!p!!",addslashes($terms[$k]),$q["MULTIPLE_TERM"]);
								$mt=str_replace("!!autperso_type_num!!",$s[1],$mt);
								$multiple_terms[]=$mt;
							}
							$final_term=implode(" ".$q["MULTIPLE_OPERATOR"]." ",$multiple_terms);
							$main=str_replace("!!multiple_term!!",$final_term,$main);
						} else {
							$field[$j]=str_replace('*', '%', $field[$j]);
							$main=str_replace("!!p!!",addslashes($field[$j]),$main);
						}
						$main=str_replace("!!autperso_type_num!!",$s[1],$main);

						if (!empty($q["WORD"])) {
							// Recherche par termes...
						    $mode = '';
						    if (isset($q['MODE'])) {
						        $mode = $q['MODE'];
						    }
						    $field[$j] = str_replace('%', '*', $field[$j]);
						    $searcher = searcher_factory::get_searcher($q['TYPE'], $mode, $field[$j], $s[1]);
							$main = $searcher->get_full_query();
						}
						//Choix de l'operateur dans la liste
						if(isset($fieldvar["operator_between_multiple_authorities"])){
    						$operator=$fieldvar["operator_between_multiple_authorities"][0];
    					} elseif(isset($q["DEFAULT_OPERATOR"])){
							$operator=$q["DEFAULT_OPERATOR"];
						} else {
							$operator = ($this->get_multi_search_operator()?$this->get_multi_search_operator():"or");
						}
						if (count($field)>1) {
							$suffixe = $i."_".$j;
							if($operator == "or"){
								//Ou logique si plusieurs valeurs
								if ($prefixe) {
									$this->gen_temporary_table($prefixe."mf_".$suffixe, $main);
								} else {
									$this->gen_temporary_table("mf_".$suffixe, $main);
								}

								if ($last_main_table) {
									if ($prefixe) {
										$requete="insert ignore into ".$prefixe."mf_".$suffixe." select ".$last_main_table.".* from ".$last_main_table;
									} else {
										$requete="insert ignore into mf_".$suffixe." select ".$last_main_table.".* from ".$last_main_table;
									}
									pmb_mysql_query($requete);
									//pmb_mysql_query("drop table if exists mf_".$suffixe);
									pmb_mysql_query("drop table if exists ".$last_main_table);
								} //else pmb_mysql_query("drop table if exists mf_".$suffixe);
								if ($prefixe) {
									$last_main_table=$prefixe."mf_".$suffixe;
								} else {
									$last_main_table="mf_".$suffixe;
								}
							} elseif($operator == "and"){
								//ET logique si plusieurs valeurs
								if ($prefixe) {
									$this->gen_temporary_table($prefixe."mf_".$suffixe, $main);
								} else {
									$this->gen_temporary_table("mf_".$suffixe, $main);
								}

								if ($last_main_table) {
									if($j>1){
										$search_table=$last_main_table;
									}else{
										$search_table=$last_tables;
									}
									if ($prefixe) {
									    $requete = $this->generate_query_op_and($prefixe, $suffixe, $search_table);
									} else {
									    $requete = $this->generate_query_op_and("", $suffixe, $search_table);
									}
									pmb_mysql_query($requete);
									pmb_mysql_query("drop table if exists ".$last_tables);

								}
								if ($prefixe) {
									$last_tables=$prefixe."mf_".$suffixe;
								} else {
									$last_tables="mf_".$suffixe;
								}
								if ($prefixe) {
									$last_main_table = $prefixe."and_result_".$suffixe;
								} else {
									$last_main_table = "and_result_".$suffixe;
								}
							}
						} //else print $main;
					}
					if ($last_main_table) {
						$main="select * from ".$last_main_table;
					}
				}
				if ($prefixe) {
    				$table=$prefixe."t_".$i."_".$search[$i];
    				$this->gen_temporary_table($table, $main, true);
	    		} else {
    				$table="t_".$i."_".$search[$i];
    				$this->gen_temporary_table($table, $main, true);
    			}
    			if (!empty($last_main_table)) {
					$requete="drop table if exists ".$last_main_table;
					pmb_mysql_query($requete);
				}

				//On supprime la table temporaire si elle existe (exemple : DSI multiples via le planificateur)
				if ($prefixe) {
					pmb_mysql_query("drop table if exists ".$prefixe."t".$i);
				} else {
					pmb_mysql_query("drop table if exists t".$i);
				}

				if ($prefixe) {
					$requete="create temporary table ".$prefixe."t".$i." ENGINE=".$this->current_engine." ";
				} else {
					$requete="create temporary table t".$i." ENGINE=".$this->current_engine." ";
				}
				$isfirst_criteria=false;
				//si le premier critere n'a pas retourne de table tempo, on ne tient pas compte l'operateur $inter
				if (empty($last_table)) {
				    ${$inter} = "";
				}
				switch (${$inter}) {
					case "and":

				    	// Si la table precedente est vide, on stop la recherche
				    	$requete_c="select count(*) from ".$last_table;
				    	if (!pmb_mysql_result(pmb_mysql_query($requete_c),0,0)) {
				    		// On sort du switch
							$isfirst_criteria=true;
							$table = $last_table;
							$last_table = "";
				    		break;
				    	}

						$requete.="select ";
    					$req_col="SHOW columns FROM ".$table;
    					$res_col=pmb_mysql_query($req_col);
    					while ($col = pmb_mysql_fetch_object($res_col)){
    						if($col->Field == "pert"){
    							$requete.="SUM(".$table.".pert + ".$last_table.".pert) AS pert,";
	    					}else{
    							$requete.=$table.".".$col->Field.",";
    						}
    					}
    					$requete=substr($requete,0,-1);
    					$requete.=" from $last_table,$table where ".$table.".".$field_keyName."=".$last_table.".".$field_keyName." group by ".$field_keyName;
	    				pmb_mysql_query($requete);
						break;
					case "or":
						//Si la table precedente est vide, c'est comme au premier jour !
						$requete_c="select count(*) from ".$last_table;
						if (!pmb_mysql_result(pmb_mysql_query($requete_c),0,0)) {
							$isfirst_criteria=true;
						} else {
							$requete.="select * from ".$table;
	    					pmb_mysql_query($requete);
							if ($prefixe) {
								$this->upgrade_columns_temporary_table($prefixe."t".$i, $field_keyName);
							} else {
								$this->upgrade_columns_temporary_table("t".$i, $field_keyName);
							}
							if ($prefixe) {
								$requete="insert into ".$prefixe."t".$i." ($field_keyName,idiot) select distinct ".$last_table.".".$field_keyName.",".$last_table.".idiot from ".$last_table." left join ".$table." on ".$last_table.".$field_keyName=".$table.".$field_keyName where ".$table.".$field_keyName is null";
							} else {
								$requete="insert into t".$i." ($field_keyName,idiot) select distinct ".$last_table.".".$field_keyName.",".$last_table.".idiot from ".$last_table." left join ".$table." on ".$last_table.".$field_keyName=".$table.".$field_keyName where ".$table.".$field_keyName is null";
								//print $requete;
							}
							pmb_mysql_query($requete);
						}
	    				break;
					case "ex":
						//$requete_not="create temporary table ".$table."_b select notices.notice_id from notices left join ".$table." on notices.notice_id=".$table.".notice_id where ".$table.".notice_id is null";
						//pmb_mysql_query($requete_not);
						//$requete_not="alter table ".$table."_b add idiot int(1), add unique(notice_id)";
						//pmb_mysql_query($requete_not);
						$requete.="select ".$last_table.".* from $last_table left join ".$table." on ".$table.".$field_keyName=".$last_table.".$field_keyName where ".$table.".$field_keyName is null";
						pmb_mysql_query($requete);
						//$requete="drop table if exists ".$table."_b";
						//pmb_mysql_query($requete);
						if ($prefixe) {
							$this->upgrade_columns_temporary_table($prefixe."t".$i, $field_keyName);
						} else {
							$this->upgrade_columns_temporary_table("t".$i, $field_keyName);
						}
						break;
					default:
						$isfirst_criteria=true;
						if ($prefixe) {
							$requete="create temporary table ".$prefixe."t".$i." ( idiot int(1), ".$field_keyName." int(1) ) ENGINE=".$this->current_engine." ";
							pmb_mysql_query($requete);
							$requete="alter table ".$prefixe."t".$i." add unique(".$field_keyName.")";
							pmb_mysql_query($requete);
						} else {
							$requete="create temporary table t".$i." ( idiot int(1), ".$field_keyName." int(1) )  ENGINE=".$this->current_engine." ";
							pmb_mysql_query($requete);
							$requete="alter table t".$i." add unique(".$field_keyName.")";
							pmb_mysql_query($requete);
						}
						break;
				}
				if (!$isfirst_criteria) {
					if($last_table){
						pmb_mysql_query("drop table if exists ".$last_table);
					}
					if($table){
						pmb_mysql_query("drop table if exists ".$table);
					}
					if ($prefixe) {
						$last_table=$prefixe."t".$i;
					} else {
						$last_table="t".$i;
					}
				} else {
					if($last_table){
						pmb_mysql_query("drop table if exists ".$last_table);
					}
					$last_table=$table;
				}
			}
		}
		//Traitement final
		if (!empty($search_previous_table)) {
			if(!empty($tsearched_sources)) {
				for ($i=0; $i<count($tsearched_sources); $i++) {
					$requete = "delete $last_table from $last_table join entrepot_source_".$tsearched_sources[$i]." on recid = notice_id where notice_id NOT IN (SELECT notice_id FROM $search_previous_table)";
					pmb_mysql_query($requete);
				}
			} else {
				$requete="insert ignore into $last_table (notice_id,pert) select notice_id,pert from $search_previous_table where notice_id NOT IN (SELECT notice_id FROM $last_table)";
				pmb_mysql_query($requete);
			}
		}
		return $last_table;
	}

	public function make_hidden_search_form($url,$form_name="search_form",$target="",$close_form=true) {

		$r="<form id='$form_name' name='$form_name' action='$url' style='display:none' method='post'";
		if ($target) $r.=" target='$target'";
		$r.=">\n";

		$r.=$this->make_hidden_form_content();
		if ($close_form) $r.="</form>";
		return $r;
	}

    public function make_hidden_search_segment_form($url, array $values=[], $form_name="search_form",$target="") {
        $r="<form name='$form_name' action='$url' style='display:none' method='post' ".(!empty($target) ? "target='$target'" : "").">";
        foreach ($values as $name => $value) {
            $r.="<input type='hidden' name='$name' value='$value'/>";
        }
        $r.="</form>";
        return $r;
    }

	public function make_hidden_form_content() {
		global $search;
		global $charset;
		global $page;
		global $nb_per_page_custom;

		$r='';
		if (!empty($search)) {
    		for ($i=0; $i<count($search); $i++) {
    			$inter="inter_".$i."_".$search[$i];
    			global ${$inter};
    			$op="op_".$i."_".$search[$i];
    			global ${$op};
    
    			$field_="field_".$i."_".$search[$i];
    			$field=$this->get_global_value($field_);
    
    			$field1_="field_".$i."_".$search[$i]."_1";
    			$field1=$this->get_global_value($field1_);
    
    			$s=explode("_",$search[$i]);
    			$type='';
    			if ($s[0]=="s") {
    				//instancier la classe de traitement du champ special
    				$type=$this->specialfields[$s[1]]["TYPE"];
    			}
    
    			//Recuperation des variables auxiliaires
    			$fieldvar_="fieldvar_".$i."_".$search[$i];
    			$fieldvar=$this->get_global_value($fieldvar_);
    
    			if (!is_array($fieldvar)) $fieldvar=array();
    
    			// si sélection d'autorité et champ vide : on ne doit pas le prendre en compte
    			if(${$op}=='AUTHORITY'){
    				$field = $this->clean_completion_empty_values($field);
    			}elseif(${$op}=='EQ'){
    				$field = $this->clean_empty_values($field);
    			}
    
    			$r .= "<input type='hidden' name='search[]' value='" . htmlentities($search[$i] ?? "", ENT_QUOTES, $charset) . "'/>";
    			$r .= "<input type='hidden' name='" .$inter. "' value='" . htmlentities(${$inter} ?? "", ENT_QUOTES, $charset) . "'/>";
    			$r .= "<input type='hidden' name='" .$op. "' value='" . htmlentities(${$op} ?? "", ENT_QUOTES, $charset) . "'/>";
    
    			if ($type=='facette') {
    				$r.="<input type='hidden' name='".$field_."[]' value='".htmlentities(serialize($field),ENT_QUOTES,$charset)."'/>\n";
    			} elseif(is_array($field) && count($field)) {
    				for ($j=0; $j<count($field); $j++) {
        				if(is_array($field[$j])) {
        					foreach ($field[$j] as $key=>$value) {
        						$r.="<input type='hidden' name='".$field_."[".$j."][".$key."]' value='".htmlentities($value,ENT_QUOTES,$charset)."'/>";
        					}
        				} else {
        					$r.="<input type='hidden' name='".$field_."[]' value='".htmlentities($field[$j],ENT_QUOTES,$charset)."'/>";
        				}
        			}
    			}
    			if(is_array($field1)) {
    				for ($j=0; $j<count($field1); $j++) {
    					if(is_array($field1[$j])) {
    						foreach ($field1[$j] as $key=>$value) {
    							$r.="<input type='hidden' name='".$field1_."[".$j."][".$key."]' value='".htmlentities($value,ENT_QUOTES,$charset)."'/>";
    						}
    					} else {
    						$r.="<input type='hidden' name='".$field1_."[]' value='".htmlentities($field1[$j],ENT_QUOTES,$charset)."'/>";
    					}
    				}
    			}
    			reset($fieldvar);
    			foreach ($fieldvar as $var_name => $var_value) {
    			    if (is_countable($var_value)) {
        				for ($j=0; $j<count($var_value); $j++) {
        					if(isset($var_value[$j]) && is_array($var_value[$j])) {
            					foreach ($var_value[$j] as $key=>$value) {
            						$r.="<input type='hidden' name='".$fieldvar_."[".$j."][".$key."]' value='".htmlentities($value,ENT_QUOTES,$charset)."'/>";
            					}
            				} else {
            					$r.="<input type='hidden' name='".$fieldvar_."[".$var_name."][]' value='".(isset($var_value[$j]) ? htmlentities($var_value[$j],ENT_QUOTES,$charset) : '')."'/>";
            				}
            			}
    			    }
    			}
    		}
		}
		$r.="<input type='hidden' name='page' value='$page'/>
			<input type=\"hidden\" name=\"nb_per_page_custom\" value=\"$nb_per_page_custom\">\n";
    	global $dsi_active;
    	if ($dsi_active) {
    		global $id_equation;
    		$r.="<input type='hidden' name='id_equation' value='$id_equation'/>";
    	}

    	global $pmb_opac_view_activate;
    	if ($pmb_opac_view_activate) {
    		global $opac_view_id;
    		$r.="<input type='hidden' name='opac_view_id' value='$opac_view_id'/>";
    	}
		return $r;
	}

	public function make_hidden_opac_view_form_content() {
	    global $charset;
	    global $search;

	    $r = '';
	    if (isset($_SESSION['opac_view']) && $_SESSION['opac_view']) {
	        $query = "select opac_view_query from opac_views where opac_view_id = ".$_SESSION['opac_view'];
	        $result = pmb_mysql_query($query);

	        if ($result && pmb_mysql_num_rows($result)) {
	            $row = pmb_mysql_fetch_object($result);
	            $serialized = $row->opac_view_query;
	            if ($serialized) {
	                $nb_search = count($search);
	                $serialized_field = serialize(array(
	                    'serialized_search' => $serialized,
	                    'search_type' => "search_fields"
	                ));
	                $r.="<input type='hidden' name='field_".$nb_search."_s_4[]' value='".htmlentities($serialized_field,ENT_QUOTES,$charset)."'/>";
	                $r.="<input type='hidden' name='search[]' value='".htmlentities('s_4',ENT_QUOTES,$charset)."'/>";
	                $r.="<input type='hidden' name='inter_".$nb_search."_s_4' value='".htmlentities('and',ENT_QUOTES,$charset)."'/>";
	                $r.="<input type='hidden' name='op_".$nb_search."_s_4' value='".htmlentities('EQ',ENT_QUOTES,$charset)."'/>";
	            }
	        }
	    }
	    return $r;
	}

	public function make_human_query() {
		global $search;
		global $msg;
		global $charset;
		global $include_path;
		global $lang;
		global $thesaurus_classement_mode_pmb;

		$r="";
		if(is_array($search) && count($search)){
			for ($i=0; $i<count($search); $i++) {
				$s=explode("_",$search[$i]);
				if ($s[0]=="f") {
					$title = '';
				    if (isset($this->fixedfields[$s[1]]["TITLE"])) {
				        $title = $this->fixedfields[$s[1]]["TITLE"];
				    }
				} elseif(array_key_exists($s[0],$this->pp)){
					$title=$this->pp[$s[0]]->t_fields[$s[1]]["TITRE"];
				} elseif ($s[0]=="s") {
					$title=$this->specialfields[$s[1]]["TITLE"];
				} elseif ($s[0]=="authperso") {
					$title=$this->authpersos[$s[1]]['name'];
				}
				$op="op_".$i."_".$search[$i];
				global ${$op};
				if(${$op}) {
				    $operator=$this->operators[${$op}] ?? "";
				} else {
					$operator="";
				}
				$field=$this->get_global_value("field_".$i."_".$search[$i]);

				$field1=$this->get_global_value("field_".$i."_".$search[$i]."_1");

				//Recuperation des variables auxiliaires
				$fieldvar_="fieldvar_".$i."_".$search[$i];
				global ${$fieldvar_};
				$fieldvar=${$fieldvar_};
				if (!is_array($fieldvar)) $fieldvar=array();

				$field_aff=array();
				$fieldvar_aff=array();
				$operator_multi = ($this->get_multi_search_operator()?$this->get_multi_search_operator():"or");
				if (array_key_exists($s[0],$this->pp)) {
					$datatype=$this->pp[$s[0]]->t_fields[$s[1]]["DATATYPE"];
					$df=$this->dynamicfields[$s[0]]["FIELD"][$this->get_id_from_datatype($datatype,$s[0])];
					$q_index=$df["QUERIES_INDEX"];
					if(${$op}) {
						$q=$df["QUERIES"][$q_index[${$op}]];
					} else {
						$q=array();
					}
					if (isset($q["DEFAULT_OPERATOR"])) {
						$operator_multi=$q["DEFAULT_OPERATOR"];
					}
					if (!empty($field) && is_countable($field)) {
    					for ($j=0; $j<count($field); $j++) {
    						//appel de la classe dynamique associée au type de champ s'il y en a une
    						if(file_exists($include_path."/search_queries/dynamics/dynamic_search_".$this->pp[$s[0]]->t_fields[$s[1]]['TYPE'].".class.php")) {
    							require_once($include_path."/search_queries/dynamics/dynamic_search_".$this->pp[$s[0]]->t_fields[$s[1]]['TYPE'].".class.php");
    							$dynamic_class_name = "dynamic_search_".$this->pp[$s[0]]->t_fields[$s[1]]['TYPE'];
    							$dynamic_class = new $dynamic_class_name($s[1],$s[0], $i,$df,$this);
    							$field_aff[$j] = $dynamic_class->make_human_query($field[$j], $field1[$j]);
    						} else {
    							$field_aff[$j]=$this->pp[$s[0]]->get_formatted_output(array(0=>$field[$j]),$s[1]);
    							if($q['OPERATOR'] == 'BETWEEN' && $field1[$j]) {
    								$field_aff[$j].= ' - '.$this->pp[$s[0]]->get_formatted_output(array(0=>$field1[$j]),$s[1]);
    							}
    						}
    					}
					}
				} elseif ($s[0]=="f" && !empty($this->fixedfields[$s[1]])) {
					$ff=$this->fixedfields[$s[1]];
					$q_index=$ff["QUERIES_INDEX"];
					if(${$op}) {
						$q=$ff["QUERIES"][$q_index[${$op}]];
					} else {
						$q=array();
					}
					if(isset($fieldvar["operator_between_multiple_authorities"])){
		 				$operator_multi=$fieldvar["operator_between_multiple_authorities"][0];
		 			} else {
			 			if (isset($q["DEFAULT_OPERATOR"]))
			    			$operator_multi=$q["DEFAULT_OPERATOR"];
		 			}
					switch ($this->fixedfields[$s[1]]["INPUT_TYPE"]) {
						case "list":
							if(${$op} == 'EQ') {
								$field_aff = self::get_list_display($this->fixedfields[$s[1]], $field);
							} else {
								$field_aff = $this->clean_empty_values($field);
							}
							break;
						case "checkbox_list":
						    if(${$op} == 'EQ') {
						        $field_aff = self::get_checkbox_list_display($this->fixedfields[$s[1]], $field);
						    } else {
						        $field_aff = $this->clean_empty_values($field);
						    }
						    break;
						case "checkbox_query_list":
						case "query_list":
							if(${$op} == 'EQ') {
								$field_aff = self::get_query_list_display($this->fixedfields[$s[1]], $field);
							} else {
								$field_aff = $this->clean_empty_values($field);
							}
							break;
						case "marc_list":
							if(${$op} == 'EQ') {
								$field_aff = self::get_marc_list_display($this->fixedfields[$s[1]], $field);
							} else {
								$field_aff = $this->clean_empty_values($field);
							}
							break;
						case "date":
							switch ($q['OPERATOR']) {
								case 'LESS_THAN_DAYS':
								case 'MORE_THAN_DAYS':
									$field_aff[0]=$field[0]." ".htmlentities($msg['days'], ENT_QUOTES, $charset);
									break;
								default:
									$field_aff[0]=format_date($field[0]);
									break;
							}
	    					if($q['OPERATOR'] == 'BETWEEN' && $field1[0]) {
	    						$field_aff[0].= ' - '.format_date($field1[0]);
	    					}
							break;
						case "authoritie":
							if (is_countable($field) && is_array($field)) {
								$tmp_size = sizeof($field);
								for($j=0 ; $j<$tmp_size; $j++){
									if((${$op} == "AUTHORITY") && (($field[$j] === "") || ($field[$j] === "0"))){
										unset($field[$j]);
									}elseif(is_numeric($field[$j]) && (${$op} == "AUTHORITY")){
										$field[$j] = self::get_authoritie_display($field[$j], $ff['INPUT_OPTIONS']['SELECTOR']);

										if($ff['INPUT_OPTIONS']['SELECTOR'] == "categorie") {
											if(isset($fieldvar["id_thesaurus"])){
												unset($fieldvar["id_thesaurus"]);
											}
										} elseif($ff['INPUT_OPTIONS']['SELECTOR'] == "onto") {
											if(isset($fieldvar["id_scheme"])){
												unset($fieldvar["id_scheme"]);
											}
										} elseif($ff['INPUT_OPTIONS']['SELECTOR'] == "vedette") {
											if(isset($fieldvar["grammars"])){
												unset($fieldvar["grammars"]);
											}
										}
									} else if ($ff['INPUT_OPTIONS']['SELECTOR'] == "instruments" && is_numeric($field[$j])) {
									    $field[$j] = nomenclature_instrument::get_instrument_name_from_id($field[$j]);
									}
								}
							}
							$field_aff = $this->clean_empty_values($field);
							break;
						default:
							$field_aff = $this->clean_empty_values($field);
							break;
					}

					// Opérateur spécial on fait donc appel à la class
					if (!empty($this->op_special[${$op}])) {
					    $field_aff[0] = $this->make_human_query_special_op($ff, ${$op}, $field);
					}

					//Ajout des variables si necessaire
					reset($fieldvar);
					$fieldvar_aff=array();
					foreach ($fieldvar as $var_name => $var_value) {
						//Recherche de la variable par son nom
						$vvar=$this->fixedfields[$s[1]]["VAR"];
						for ($j=0; $j<count($vvar); $j++) {
							if (($vvar[$j]["TYPE"]=="input")&&($vvar[$j]["NAME"]==$var_name)) {

								//Calcul de la visibilite
								$varname=$vvar[$j]["NAME"];
								$visibility=1;
								if(isset($vvar[$j]["OPTIONS"]["VAR"][0])) {
									$vis=$vvar[$j]["OPTIONS"]["VAR"][0];
									if ($vis["NAME"]) {
										$vis_name=$vis["NAME"];
										global ${$vis_name};
										if ($vis["VISIBILITY"]=="no") $visibility=0;
										for ($k=0; $k<count($vis["VALUE"]); $k++) {
											if ($vis["VALUE"][$k]["value"]==${$vis_name}) {
												if ($vis["VALUE"][$k]["VISIBILITY"]=="no") $sub_vis=0; else $sub_vis=1;
												if ($vis["VISIBILITY"]=="no") $visibility|=$sub_vis; else $visibility&=$sub_vis;
												break;
											}
										}
									}
								}

								$var_list_aff=array();
								$flag_aff = false;

								if ($visibility) {
									switch ($vvar[$j]["OPTIONS"]["INPUT"][0]["TYPE"]) {
										case "query_list":
											$query_list=$vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["value"];
											$r_list=pmb_mysql_query($query_list);
											while ($line=pmb_mysql_fetch_array($r_list)) {
												$as=array_search($line[0],$var_value);
												if (($as!==false)&&($as!==NULL)) {
													$var_list_aff[]=$line[1];
												}
											}
											if($vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["ALLCHOICE"] == "yes" && count($var_list_aff) == 0){
												$var_list_aff[]=$msg[substr($vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["TITLEALLCHOICE"],4,strlen($vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["TITLEALLCHOICE"])-4)];
											}
											$fieldvar_aff[]=implode(" ".$msg["search_or"]." ",$var_list_aff);
											$flag_aff=true;
											break;
										case "checkbox":
											$value = $var_value[0];
											$label_list = $vvar[$j]["OPTIONS"]["INPUT"][0]["COMMENTS"][0]["LABEL"];
											$nb_label = count($label_list);
											for ($indice = 0; $indice < $nb_label; $indice++) {
												if($value == $label_list[$indice]["VALUE"]){
													$libelle = $label_list[$indice]["value"];
													if (substr($libelle,0,4)=="msg:") {
														$libelle=$msg[substr($libelle,4,strlen($libelle)-4)];
													}
													break;
												}
											}

											if ($libelle) {
												$fieldvar_aff[]=$libelle;
												$flag_aff=true;
											}
											break;
									}
									if($flag_aff) $fieldvar_aff[count($fieldvar_aff)-1]=$vvar[$j]["COMMENT"]." : ".$fieldvar_aff[count($fieldvar_aff)-1];
								}
							}
						}
					}
				} elseif ($s[0]=="s") {
					//appel de la fonction make_human_query de la classe du champ special
					//Recherche du type
					$type=$this->specialfields[$s[1]]["TYPE"];
					if(!empty($this->tableau_speciaux['TYPE'])) {
						for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
							if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
								$sf=$this->specialfields[$s[1]];
								require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
								$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$i,$sf,$this);
								$field_aff=$specialclass->make_human_query();
								$field_aff[0]=html_entity_decode(strip_tags($field_aff[0]),ENT_QUOTES,$charset);
								break;
							}
						}
					}
				}elseif ($s[0]=="authperso") {
					if(isset($fieldvar["operator_between_multiple_authorities"])){
						$operator_multi=$fieldvar["operator_between_multiple_authorities"][0];
					} else {
						if (isset($q["DEFAULT_OPERATOR"]))
							$operator_multi=$q["DEFAULT_OPERATOR"];
					}
					if (is_countable($field) && is_array($field)) {
						$tmpsize = sizeof($field);
						for($j=0 ; $j<$tmpsize; $j++){
							if((${$op} == "AUTHORITY") && (($field[$j] === "") || ($field[$j] === "0"))){
								unset($field[$j]);
							}elseif(is_numeric($field[$j]) && (${$op} == "AUTHORITY")){
								$field[$j] = authperso::get_isbd($field[$j]);
							}
						}
					}
	    			$field_aff= $field;
				}

				switch ($operator_multi) {
					case "and":
						$op_list=$msg["search_and"];
						break;
					case "or":
						$op_list=$msg["search_or"];
						break;
					default:
						$op_list=$msg["search_or"];
						break;
				}
				if(is_array($field_aff)){
					$texte=implode(" ".$op_list." ",$field_aff);
				}else{
					$texte="";
				}
				if (count($fieldvar_aff)) $texte.=" [".implode(" ; ",$fieldvar_aff)."]";
				$inter="inter_".$i."_".$search[$i];
				global ${$inter};
				switch (${$inter}) {
					case "and":
						$inter_op=$msg["search_and"];
						break;
					case "or":
						$inter_op=$msg["search_or"];
						break;
					case "ex":
						$inter_op=$msg["search_exept"];
						break;
					default:
						$inter_op="";
						break;
				}
				if ($inter_op) $inter_op="<strong>".htmlentities($inter_op,ENT_QUOTES,$charset)."</strong>";
				if (!empty($this->op_special[${$op}])) {
				    $r.= $inter_op." <i><strong>".htmlentities($title,ENT_QUOTES,$charset)."</strong> ".htmlentities($operator,ENT_QUOTES,$charset)." (".$texte.")</i> ";
				} elseif ((isset($ff['INPUT_OPTIONS']['SELECTOR']) && $ff['INPUT_OPTIONS']['SELECTOR'] == 'instruments') && (!empty($fieldvar))) {
				    $r.= $inter_op." <i><strong>".htmlentities($title,ENT_QUOTES,$charset)."</strong> (".nomenclature_instrument::get_instrument_name_from_id($field[0]) . ' ' .$operator.' '.$fieldvar['number_instruments'][0] . ') ';
				} elseif ((isset($ff['INPUT_OPTIONS']['SELECTOR']) && $ff['INPUT_OPTIONS']['SELECTOR'] == 'voices') && (!empty($fieldvar))) {
				    $r.= $inter_op." <i><strong>".htmlentities($title,ENT_QUOTES,$charset)."</strong> (".nomenclature_voice::get_voice_name_from_id($field[0]) . ' ' .$operator.' '.$fieldvar['number_voices'][0] . ') ';
				} else {
				    $r.=$inter_op." <i><strong>".htmlentities($title,ENT_QUOTES,$charset)."</strong> ".htmlentities($operator,ENT_QUOTES,$charset)." (".htmlentities($texte,ENT_QUOTES,$charset).")</i> ";
				}
			}
		}
		if ($r){
			$r="<span class='search-human-query'>".$r."</span>";
		}
		return $r;
	}

	public function make_serialized_human_query($serialized) {
		global $msg;
		global $charset;
		global $include_path;

		$to_unserialize=unserialize($serialized);
		$search=$to_unserialize["SEARCH"];
		if(!is_array($search)) {
		    $search = array();
		}
		for ($i=0; $i<count($search); $i++) {
			$op="op_".$i."_".$search[$i];
			$field_="field_".$i."_".$search[$i];
			$field1_="field_".$i."_".$search[$i]."_1";
			$inter="inter_".$i."_".$search[$i];
			$fieldvar_="fieldvar_".$i."_".$search[$i];
// 			if(!isset($GLOBALS[${$op}])){
				global ${$op};
// 			}
// 			if(!isset($GLOBALS[${$field_}])){
				global ${$field_};
// 			}
// 			if(!isset($GLOBALS[${$field1_}])){
				global ${$field1_};
// 			}
// 			if(!isset($GLOBALS[${$inter}])){
				global ${$inter};
// 			}
// 			if(!isset($GLOBALS[${$fieldvar}])){
				global ${$fieldvar_};
// 			}
			${$op}=$to_unserialize[$i]["OP"];
			${$field_}=$to_unserialize[$i]["FIELD"];
			${$field1_}=(isset($to_unserialize[$i]["FIELD1"]) ? $to_unserialize[$i]["FIELD1"] : '');
			${$inter}=$to_unserialize[$i]["INTER"];
			${$fieldvar_}=$to_unserialize[$i]["FIELDVAR"];
		}

		$r="";
		for ($i=0; $i<count($search); $i++) {
			$s=explode("_",$search[$i]);
			if ($s[0]=="f") {
				$title=$this->fixedfields[$s[1]]["TITLE"];
			} elseif (array_key_exists($s[0],$this->pp)) {
				$title=$this->pp[$s[0]]->t_fields[$s[1]]["TITRE"];
			} elseif ($s[0]=="s") {
				$title=(isset($this->specialfields[$s[1]]["TITLE"]) ? $this->specialfields[$s[1]]["TITLE"] : '');
			} elseif ($s[0]=="authperso") {
				$title=$this->authpersos[$s[1]]['name'];
			}
			$op="op_".$i."_".$search[$i];
			if(${$op}) {
			    $operator=$this->operators[${$op}] ?? "";
			} else {
			    $operator='';
			}
			
			$field_="field_".$i."_".$search[$i];
			$field=${$field_};

			//Recuperation des variables auxiliaires
			$fieldvar_="fieldvar_".$i."_".$search[$i];
			$fieldvar=${$fieldvar_};
			if (!is_array($fieldvar)) $fieldvar=array();

			$operator_multi = '';
			$field_aff=array();
			if (array_key_exists($s[0],$this->pp)) {
				$datatype=$this->pp[$s[0]]->t_fields[$s[1]]["DATATYPE"];
				$df=$this->dynamicfields[$s[0]]["FIELD"][$this->get_id_from_datatype($datatype,$s[0])];
				$q_index=$df["QUERIES_INDEX"];
				if(${$op}) {
					$q=$df["QUERIES"][$q_index[${$op}]];
				} else {
					$q=array();
				}
				if (isset($q["DEFAULT_OPERATOR"])) {
					$operator_multi=$q["DEFAULT_OPERATOR"];
				}
				if(is_array($field)) {
					for ($j=0; $j<count($field); $j++) {
						$field_aff[$j]=$this->pp[$s[0]]->get_formatted_output(array(0=>$field[$j]),$s[1]);
					}
				}
			} elseif($s[0]=="f") {
				$ff=$this->fixedfields[$s[1]];
				$q_index=$ff["QUERIES_INDEX"];
				if(${$op}) {
					$q=$ff["QUERIES"][$q_index[${$op}]];
				} else {
					$q=array();
				}
				if(isset($fieldvar["operator_between_multiple_authorities"])){
					$operator_multi=$fieldvar["operator_between_multiple_authorities"][0];
				} else {
					if (isset($q["DEFAULT_OPERATOR"]))
						$operator_multi=$q["DEFAULT_OPERATOR"];
				}
				switch ($this->fixedfields[$s[1]]["INPUT_TYPE"]) {
					case "list":
						if(${$op} == 'EQ') {
							$field_aff = self::get_list_display($this->fixedfields[$s[1]], $field);
						} else {
							$field_aff = $this->clean_empty_values($field);
						}
						break;
					case "checkbox_list":
					    if(${$op} == 'EQ') {
					        $field_aff = self::get_checkbox_list_display($this->fixedfields[$s[1]], $field);
					    } else {
					        $field_aff = $this->clean_empty_values($field);
					    }
					    break;
					case "checkbox_query_list":
					case "query_list":
						if(${$op} == 'EQ') {
							$field_aff = self::get_query_list_display($this->fixedfields[$s[1]], $field);
						} else {
							$field_aff = $this->clean_empty_values($field);
						}
						break;
					case "checkbox_marc_list":
					case "marc_list":
						if(${$op} == 'EQ') {
							$field_aff = self::get_marc_list_display($this->fixedfields[$s[1]], $field);
						} else {
							$field_aff = $this->clean_empty_values($field);
						}
						break;
					case "date":
						switch ($q['OPERATOR']) {
							case 'LESS_THAN_DAYS':
							case 'MORE_THAN_DAYS':
								$field_aff[0]=$field[0]." ".htmlentities($msg['days'], ENT_QUOTES, $charset);
								break;
							default:
								$field_aff[0]=format_date($field[0]);
								break;
						}
						if($q['OPERATOR'] == 'BETWEEN' && $field1[0]) {
							$field_aff[0].= ' - '.format_date($field1[0]);
						}
						break;
					case "authoritie":
						if (is_countable($field) && is_array($field)) {
							for($j=0 ; $j<sizeof($field) ; $j++){
								if(is_numeric($field[$j]) && (${$op} == "AUTHORITY")){
									$field[$j] = self::get_authoritie_display($field[$j], $ff['INPUT_OPTIONS']['SELECTOR']);
								}
							}
						}
						$field_aff = $this->clean_completion_empty_values($field);
						break;
					default:
						$field_aff = $this->clean_empty_values($field);
						break;
				}

				// Opérateur spécial on fait donc appel à la class
                if (isset($this->op_special[${$op}]) && $this->op_special[${$op}]) {
				    $field_aff[0] = $this->make_human_query_special_op($ff, ${$op}, $field);
				}

			} elseif ($s[0]=="s") {
				//appel de la fonction make_human_query de la classe du champ special
				//Recherche du type
				$type = (isset($this->specialfields[$s[1]]["TYPE"]) ? $this->specialfields[$s[1]]["TYPE"] : '');
				for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
					if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
						$sf=$this->specialfields[$s[1]];
						require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
						$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$i,$sf,$this);
						$field_aff=$specialclass->make_human_query();
						$field_aff[0]=html_entity_decode(strip_tags($field_aff[0]),ENT_QUOTES,$charset);
						break;
					}
				}
			} elseif ($s[0]=="authperso") {
				$field_aff[0]=$field[0];
			}

			//Ajout des variables si necessaire
			reset($fieldvar);
			$fieldvar_aff=array();
			foreach ($fieldvar as $var_name => $var_value) {
				//Recherche de la variable par son nom
				$vvar=$this->fixedfields[$s[1]]["VAR"];
				if(is_countable($vvar)) {
    				for ($j=0; $j<count($vvar); $j++) {
    					if (($vvar[$j]["TYPE"]=="input")&&($vvar[$j]["NAME"]==$var_name)) {
    						//Calcul de la visibilite
    						$varname=$vvar[$j]["NAME"];
    						$visibility=1;
    						$vis=(isset($vvar[$j]["OPTIONS"]["VAR"][0]) ? $vvar[$j]["OPTIONS"]["VAR"][0] : array("NAME" => ""));
    						if ($vis["NAME"]) {
    							$vis_name=$vis["NAME"];
    							global ${$vis_name};
    							if ($vis["VISIBILITY"]=="no") $visibility=0;
    							for ($k=0; $k<count($vis["VALUE"]); $k++) {
    								if ($vis["VALUE"][$k]["value"]==${$vis_name}) {
    									if ($vis["VALUE"][$k]["VISIBILITY"]=="no") $sub_vis=0; else $sub_vis=1;
    									if ($vis["VISIBILITY"]=="no") $visibility|=$sub_vis; else $visibility&=$sub_vis;
    									break;
    								}
    							}
    						}
    						$var_list_aff=array();
    						$flag_aff = false;
    						if ($visibility) {
    							switch ($vvar[$j]["OPTIONS"]["INPUT"][0]["TYPE"]) {
    								case "query_list":
    									$query_list=$vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["value"];
    									$r_list=pmb_mysql_query($query_list);
    									while ($line=pmb_mysql_fetch_array($r_list)) {
    										$as=array_search($line[0],$var_value);
    										if (($as!==false)&&($as!==NULL)) {
    											$var_list_aff[]=$line[1];
    										}
    									}
    									if($vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["ALLCHOICE"] == "yes" && count($var_list_aff) == 0){
    										$var_list_aff[]=$msg[substr($vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["TITLEALLCHOICE"],4,strlen($vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["TITLEALLCHOICE"])-4)];
    									}
    									$fieldvar_aff[]=implode(" ".$msg["search_or"]." ",$var_list_aff);
    									$flag_aff=true;
    									break;
    								case "checkbox":
    									$value = $var_value[0];
    									$label_list = $vvar[$j]["OPTIONS"]["INPUT"][0]["COMMENTS"][0]["LABEL"];
    									for($indice=0;$indice<count($label_list);$indice++){
    										if($value == $label_list[$indice]["VALUE"]){
    											$libelle = $label_list[$indice]["value"];
    											if (substr($libelle,0,4)=="msg:") {
    												$libelle=$msg[substr($libelle,4,strlen($libelle)-4)];
    											}
    											break;
    										}
    									}
    									if ($libelle) {
    										$fieldvar_aff[]=$libelle;
    										$flag_aff=true;
    									}
    									break;
    							}
    							if($flag_aff) $fieldvar_aff[count($fieldvar_aff)-1]=$vvar[$j]["COMMENT"]." : ".$fieldvar_aff[count($fieldvar_aff)-1];
    						}
    					}
    				}
				}
			}

			switch ($operator_multi) {
				case "and":
					$op_list=$msg["search_and"];
					break;
				case "or":
					$op_list=$msg["search_or"];
					break;
				default:
					$op_list=$msg["search_or"];
					break;
			}
			if(is_array($field_aff)){
				$texte=implode(" ".$op_list." ",$field_aff);
			}else{
				$texte="";
			}
			if (count($fieldvar_aff)) $texte.=" [".implode(" ; ",$fieldvar_aff)."]";

			$inter="inter_".$i."_".$search[$i];
			switch (${$inter}) {
				case "and":
					$inter_op=$msg["search_and"];
					break;
				case "or":
					$inter_op=$msg["search_or"];
					break;
				case "ex":
					$inter_op=$msg["search_exept"];
					break;
				default:
					$inter_op="";
					break;
			}
			if ($inter_op) $inter_op="<strong>".htmlentities($inter_op,ENT_QUOTES,$charset)."</strong>";
            if (isset($this->op_special[${$op}]) && $this->op_special[${$op}]) {
			    $r.= $inter_op." <i><strong>".htmlentities($title,ENT_QUOTES,$charset)."</strong> ".htmlentities($operator,ENT_QUOTES,$charset)." (".$texte.")</i> ";
			} else {
    			$r.=$inter_op." <i><strong>".htmlentities($title,ENT_QUOTES,$charset)."</strong> ".htmlentities($operator,ENT_QUOTES,$charset)." (".htmlentities($texte,ENT_QUOTES,$charset).")</i> ";
			}

		}
		return $r;
	}

	public function make_unimarc_query() {
		global $search;
		global $msg;
		global $charset;
		global $include_path;

		$mt=array();

		//Récupération du type de recherche
		$sc_type = $this->fichier_xml;
		$sc_type = substr($sc_type,0,strlen($sc_type)-8);

		for ($i=0; $i<count($search); $i++) {
			$sub="";
			$s=explode("_",$search[$i]);
			if ($s[0]=="f") {
				$id=$search[$i];
				$title=$this->fixedfields[$s[1]]["UNIMARCFIELD"];
			} elseif (array_key_exists($s[0],$this->pp)){
				$id=$search[$i];
				$title=$this->pp[$s[0]]->t_fields[$s[1]]["UNIMARCFIELD"];
			} elseif ($s[0]=="s") {
				$id=$search[$i];
				$title=$this->specialfields[$s[1]]["UNIMARCFIELD"];
			}
			$op="op_".$i."_".$search[$i];
			global ${$op};
			//$operator=$this->operators[${$op}];
			$field=$this->get_global_value("field_".$i."_".$search[$i]);

			$field1=$this->get_global_value("field_".$i."_".$search[$i]."_1");

			//Recuperation des variables auxiliaires
			$fieldvar_="fieldvar_".$i."_".$search[$i];
			global ${$fieldvar_};
			$fieldvar=${$fieldvar_};
			if (!is_array($fieldvar)) $fieldvar=array();

			$field_aff=array();
			$fieldvar_aff=array();

			if(array_key_exists($s[0],$this->pp)){
				for ($j=0; $j<count($field); $j++) {
					$field_aff[$j]=$this->pp[$s[0]]->get_formatted_output(array(0=>$field[$j]),$s[1]);
				}
			} elseif ($s[0]=="f") {
				switch ($this->fixedfields[$s[1]]["INPUT_TYPE"]) {
					case "list":
						if(${$op} == 'EQ') {
							$field_aff = self::get_list_display($this->fixedfields[$s[1]], $field);
						} else {
							$field_aff = $this->clean_empty_values($field);
						}
						break;
					case "checkbox_list":
					    if(${$op} == 'EQ') {
					        $field_aff = self::get_checkbox_list_display($this->fixedfields[$s[1]], $field);
					    } else {
					        $field_aff = $this->clean_empty_values($field);
					    }
					    break;
					case "checkbox_query_list":
					case "query_list":
						if(${$op} == 'EQ') {
							$field_aff = self::get_query_list_display($this->fixedfields[$s[1]], $field);
						} else {
							$field_aff = $this->clean_empty_values($field);
						}
						break;
					case "checkbox_marc_list":
					case "marc_list":
						if(${$op} == 'EQ') {
							$field_aff = self::get_marc_list_display($this->fixedfields[$s[1]], $field);
						} else {
							$field_aff = $this->clean_empty_values($field);
						}
						break;
					case "date":
						switch ($q['OPERATOR']) {
							case 'LESS_THAN_DAYS':
							case 'MORE_THAN_DAYS':
								$field_aff[0]=$field[0]." ".htmlentities($msg['days'], ENT_QUOTES, $charset);
								break;
							default:
								$field_aff[0]=format_date($field[0]);
								break;
						}
    					if($q['OPERATOR'] == 'BETWEEN' && $field1[0]) {
    						$field_aff[0].= ' - '.format_date($field1[0]);
    					}
						break;
					default:
						$field_aff=$this->clean_empty_values($field);
						break;
				}

				//Ajout des variables si necessaire
				reset($fieldvar);
				foreach ($fieldvar as $var_name => $var_value) {
					//Recherche de la variable par son nom
					$vvar=$this->fixedfields[$s[1]]["VAR"];
					for ($j=0; $j<count($vvar); $j++) {
						if (($vvar[$j]["TYPE"]=="input")&&($vvar[$j]["NAME"]==$var_name)) {

							//Calcul de la visibilite
							$varname=$vvar[$j]["NAME"];
							$visibility=1;
							$vis=$vvar[$j]["OPTIONS"]["VAR"][0];
							if ($vis["NAME"]) {
								$vis_name=$vis["NAME"];
								global ${$vis_name};
								if ($vis["VISIBILITY"]=="no") $visibility=0;
								for ($k=0; $k<count($vis["VALUE"]); $k++) {
									if ($vis["VALUE"][$k]["value"]==${$vis_name}) {
										if ($vis["VALUE"][$k]["VISIBILITY"]=="no") $sub_vis=0; else $sub_vis=1;
										if ($vis["VISIBILITY"]=="no") $visibility|=$sub_vis; else $visibility&=$sub_vis;
										break;
									}
								}
							}

							$var_list_aff=array();

							if ($visibility) {
								switch ($vvar[$j]["OPTIONS"]["INPUT"][0]["TYPE"]) {
									case "query_list":
										$query_list=$vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["value"];
										$r_list=pmb_mysql_query($query_list);
										while ($line=pmb_mysql_fetch_array($r_list)) {
											$as=array_search($line[0],$var_value);
											if (($as!==false)&&($as!==NULL)) {
												$var_list_aff[]=$line[1];
											}
										}
										$fieldvar_aff[]=implode(" ".$msg["search_or"]." ",$var_list_aff);
										break;
								}
								$fieldvar_aff[count($fieldvar_aff)-1]=$vvar[$j]["COMMENT"]." : ".$fieldvar_aff[count($fieldvar_aff)-1];
							}
						}
					}
				}
			} elseif ($s[0]=="s") {
				//appel de la fonction make_unimarc_query de la classe du champ special
				//Recherche du type
				$type=$this->specialfields[$s[1]]["TYPE"];
				for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
					if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
						$sf=$this->specialfields[$s[1]];
						require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
						$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$i,$sf,$this);
						$sub=$specialclass->make_unimarc_query();
						break;
					}
				}
			}

			$inter="inter_".$i."_".$search[$i];
			global ${$inter};

			$mterm=new mterm($title,${$op},$field_aff,$fieldvar_aff,${$inter},$id);
			if ($i==1) $mterm->sc_type=$sc_type;
			if ((is_array($sub))&&(count($sub))) {
				$mterm->set_sub($sub);
			} else if (is_array($sub)) {
				unset($mterm);
			}
			if (isset($mterm)) {
				$mt[]=$mterm;
			}
		}
		return $mt;
	}

	public function filter_searchtable_from_accessrights($table) {
		global $gestion_acces_active,$gestion_acces_user_notice;
		global $PMBUserId;

		if($gestion_acces_active && $gestion_acces_user_notice){
			//droits d'acces lecture notice
			$ac= new acces();
			$dom_1= $ac->setDomain(1);
			$usr_prf = $dom_1->getUserProfile($PMBUserId);
			if (!is_array($usr_prf)) {
			    $usr_prf = [$usr_prf];
			}

			$requete = "delete from $table using $table, acces_res_1 ";
			$requete.= "where ";
			$requete.= "$table.notice_id = res_num and usr_prf_num IN (".implode(",", $usr_prf).") ";
			$requete.= "and (((res_rights ^ res_mask) & 4)=0) ";
			pmb_mysql_query($requete);
		}
	}

	public function filter_searchtable_without_no_display($table) {
		global $no_display;

		if($no_display) {
			$requete = "delete from ".$table." using ".$table." ";
			$requete.= "where ";
			$requete.= $table.".".$this->keyName." = ".$no_display." ";
			pmb_mysql_query($requete);
		}
	}

	// fonction de calcul de la visibilite d'un champ de recherche
	public function visibility($ff) {

		if (!isset($ff["VARVIS"]) || !count($ff["VARVIS"])) return $ff["VISIBILITY"];

		for ($i=0; $i<count($ff["VARVIS"]); $i++) {
			$name=$ff["VARVIS"][$i]["NAME"] ;
			global ${$name};
			$visibilite=$ff["VARVIS"][$i]["VISIBILITY"] ;
			if (isset($ff["VARVIS"][$i]["VALUE"][${$name}])) {
				if ($visibilite)
					$test = $ff["VARVIS"][$i]["VALUE"][${$name}] ;
				else
					$test = $visibilite || $ff["VARVIS"][$i]["VALUE"][${$name}] ;
				return $test ;
			}
		} // fin for
		// aucune condition verifiee : on retourne la valeur par defaut
		return $ff["VISIBILITY"] ;
	}

	protected function sort_list_criteria() {
		$sort_list_criteria_by_groups = array();
		foreach($this->groups as $group_id => $group){
			$group_name = $this->groups[$group_id]['label'];
			if(isset($this->list_criteria[$group_name])){ //On a des champs définis pour le groupe courant
				if(!count($this->filtered_objects_types) || in_array($group['objects_type'], $this->filtered_objects_types)) {
					$sort_list_criteria_by_groups[$group_name] = $this->list_criteria[$group_name];
				}
			}
		}
		$this->list_criteria = $sort_list_criteria_by_groups;
	}

	protected function add_criteria($group_name, $id, $label, $authperso_id = 0) {
	    $this->list_criteria[$group_name][] = array('id' => $id, 'label' => $label, 'authperso_id' => $authperso_id);
	}

	public function get_list_criteria() {
		global $msg, $charset;
		global $include_path;

		if(!empty($this->list_criteria)) {
			return $this->list_criteria;
		}
		$this->list_criteria = array();
		$group_name = '';
		/**
		 * if else, si il n'y a pas de groupe défini, on conserve le traitement de base
		 * Sinon, ordonnancement via les IDs de groupes
		 */
		if(!$this->groups_used){
			//Champs fixes
			if(!empty($this->fixedfields)) {
				reset($this->fixedfields);
				foreach ($this->fixedfields as $id => $ff) {
					if ($ff["SEPARATOR"]) {
						$group_name = $ff["SEPARATOR"];
					}
					if ($this->visibility($ff)) {
						$this->add_criteria($group_name, "f_".$id, $ff["TITLE"]);
					}
				}
			}
			//Champs dynamiques
			if(!$this->dynamics_not_visible){
				foreach ( $this->dynamicfields as $key => $value ) {
					if(!$this->pp[$key]->no_special_fields && count($this->pp[$key]->t_fields) && ($key != 'a')){
						$group_name = $msg["search_custom_".$value["TYPE"]];
						reset($this->pp[$key]->t_fields);
						$array_dyn_tmp=array();
						//liste des champs persos à cacher par type
						$hide_customfields_array = array();
						if (!empty($this->dynamicfields_hidebycustomname[$value["TYPE"]])) {
							$hide_customfields_array = explode(",",$this->dynamicfields_hidebycustomname[$value["TYPE"]]);
						}
						foreach ($this->pp[$key]->t_fields as $id => $df) {
							//On n'affiche pas les champs persos cités par nom dans le fichier xml
							if ((!count($hide_customfields_array)) || (!in_array($df["NAME"],$hide_customfields_array))) {
								$array_dyn_tmp[strtolower($df["TITRE"])] = array('id' => $key."_".$id, 'label' => $df["TITRE"]);
							}
						}
						if (count($array_dyn_tmp)) {
							if ($this->dynamicfields_order=="alpha") {
								ksort($array_dyn_tmp);
							}
							foreach($array_dyn_tmp as $dynamic_option){
								$this->add_criteria($group_name, $dynamic_option['id'], $dynamic_option['label']);
							}
						}
					}
				}
			}
			//Champs autorités perso
			foreach($this->authpersos as $authperso){
				if(!$authperso['gestion_multi_search'])continue;
				$group_name = $msg["authperso_multi_search_by_field_title"]." : ".$authperso['name'];
				$this->add_criteria($group_name, "authperso_".$authperso['id'], $msg["authperso_multi_search_tous_champs_title"]);
				foreach($authperso['fields'] as $field){
					$this->add_criteria($group_name, "a_".$field['id'], $field['label']);
				}
			}
			//Champs speciaux
			if (!$this->specials_not_visible && $this->specialfields) {
			    foreach ($this->specialfields as $id => $sf) {
					for($i=0 ; $i<count($this->tableau_speciaux['TYPE']) ; $i++){
						if ($this->tableau_speciaux["TYPE"][$i]["NAME"] == $sf['TYPE']) {
							require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$i]["PATH"]."/search.class.php");
							$classname = $this->tableau_speciaux["TYPE"][$i]["CLASS"];
							if((isset($sf['VISIBLE']) && $sf['VISIBLE'] && !method_exists($classname, 'check_visibility')) || (method_exists($classname, 'check_visibility') && $classname::check_visibility() == true)){
								if ($sf["SEPARATOR"]) {
									$group_name = $sf["SEPARATOR"];
								}
								$this->add_criteria($group_name, "s_".$id, $sf["TITLE"]);
							}
							break;
						}
					}
				}
			}
		} else {
			//Traitement des champs fixes
			if (isset($this->fixedfields) && is_array($this->fixedfields)) {
				reset($this->fixedfields);
				foreach ($this->fixedfields as $id => $ff) {
					if ($this->visibility($ff)) {
					    if (isset($ff["GROUP"]) && isset($this->groups[$ff["GROUP"]]['label'])) {
							$this->add_criteria($this->groups[$ff["GROUP"]]['label'], "f_".$id, $ff["TITLE"]);
						} else {
							$this->add_criteria($msg["search_extended_lonely_fields"], "f_".$id, $ff["TITLE"]);
						}
					}
				}
			}
			//Traitement des champs dynamiques (champs persos)
			if(!$this->dynamics_not_visible){
				foreach ( $this->dynamicfields as $key => $value ) {
					if(!$this->pp[$key]->no_special_fields && count($this->pp[$key]->t_fields) && ($key != 'a')){
						reset($this->pp[$key]->t_fields);
						$array_dyn_tmp=array();
						//liste des champs persos à cacher par type
						$hide_customfields_array = array();
						if (isset($this->dynamicfields_hidebycustomname[$value["TYPE"]]) && $this->dynamicfields_hidebycustomname[$value["TYPE"]]) {
							$hide_customfields_array = explode(",",$this->dynamicfields_hidebycustomname[$value["TYPE"]]);
						}
						foreach ($this->pp[$key]->t_fields as $id => $df) {
							//On n'affiche pas les champs persos cités par nom dans le fichier xml
							if ((!count($hide_customfields_array)) || (!in_array($df["NAME"],$hide_customfields_array))) {
								$array_dyn_tmp[strtolower($df["TITRE"])]= array('id' => $key."_".$id, 'label' => $df["TITRE"]);
							}
						}
						if (count($array_dyn_tmp)) {
							if ($this->dynamicfields_order=="alpha") {
								ksort($array_dyn_tmp);
							}
							$reorganized_array = array();
							foreach($array_dyn_tmp as $dynamic_option){
								$reorganized_array[] = $dynamic_option;
							}
                            if(isset($value["GROUP"]) && isset($this->groups[$value["GROUP"]])){
								$group_name = $this->groups[$value["GROUP"]]['label'];
								if(!isset($this->list_criteria[$group_name]) || !is_array($this->list_criteria[$group_name])) {
									$this->list_criteria[$group_name] = array();
								}
								$this->list_criteria[$group_name] = array_merge($this->list_criteria[$group_name], $reorganized_array);
							}else{
								$lonely_fields = array_merge($this->list_criteria[$msg["search_extended_lonely_fields"]], $reorganized_array);
							}
						}
					}
				}
			}
			//Traitement des champs spéciaux
			if (!$this->specials_not_visible && $this->specialfields) {
			    foreach ($this->specialfields as $id => $sf) {
					for($i=0 ; $i<count($this->tableau_speciaux['TYPE']) ; $i++){
					    if (!empty($this->tableau_speciaux["TYPE"][$i]) && $this->tableau_speciaux["TYPE"][$i]["NAME"] == $sf['TYPE']) {
							require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$i]["PATH"]."/search.class.php");
							$classname = $this->tableau_speciaux["TYPE"][$i]["CLASS"];
							if((isset($sf['VISIBLE']) && $sf['VISIBLE'] && !method_exists($classname, 'check_visibility')) || (method_exists($classname, 'check_visibility') && $classname::check_visibility() == true)){
							    if (isset($sf["GROUP"]) && $sf["GROUP"] && !empty($this->groups[$sf["GROUP"]])) {
									$this->add_criteria($this->groups[$sf["GROUP"]]['label'], "s_".$id, $sf["TITLE"]);
								} else {
									$this->add_criteria($msg["search_extended_lonely_fields"], "s_".$id, $sf["TITLE"]);
								}
							}
						}
					}
				}
			}
			/**
			 * On parcourt la propriété groups contenant les
			 * groupes ordonnés selon l'ordre défini dans le XML
			 */
			$this->sort_list_criteria();

			//Traitement des autorités persos (le champs doit être généré dynamiquement
			$r_authperso="";
			global $authperso_id;
			foreach($this->authpersos as $authperso){
				if(!count($this->filtered_objects_types) || (in_array("authperso", $this->filtered_objects_types) && $authperso_id == $authperso['id'])) {
					if(!$authperso['gestion_multi_search'])continue;
					$this->add_criteria($msg["authperso_multi_search_by_field_title"]." : ".$authperso['name'], "authperso_".$authperso['id'], $msg["authperso_multi_search_tous_champs_title"], $authperso['id']);
					if ($authperso['responsability_authperso']) {
					    $this->add_criteria($msg["authperso_multi_search_by_field_title"]." : ".$authperso['name'], "f_2000_".$authperso['id'], $msg['aut_responsability_form_responsability_authperso'], $authperso['id']);
					}
					foreach($authperso['fields'] as $field){
					    $this->add_criteria($msg["authperso_multi_search_by_field_title"]." : ".$authperso['name'], "a_".$field['id'], $field['label'], $authperso['id']);
					}
				}
			}
		}
		return $this->list_criteria;
	}

	/**
	 * Méthode de génération du sélecteur des champs de recherche
	 *
	 */
	public function get_field_selector($url, $limit_search){
		global $pmb_extended_search_auto, $charset, $msg, $authperso_id, $include_path;
		if ($pmb_extended_search_auto) {
		    $r="
                <script>
                    function getFormInfos() {
                        if (search_form.add_field.value != '') {
                            enable_operators();

                            var node = document.getElementById('add_field');
                            if (node && search_form.authperso_id) {
                                var option = node.options.item(node.selectedIndex);
        			            if (option && option.attributes.getNamedItem('data-authperso_id')) {
        			                attribute = option.attributes.getNamedItem('data-authperso_id');
                                    if (attribute) {
                                        search_form.authperso_id.value = attribute.value;
                                    }
        			            }
                            }

                            search_form.action = '$url';
                            search_form.target = '';
                            if(search_form.launch_search) {
                                search_form.launch_search.value = 0;
                            }
                            $limit_search
                            search_form.submit();
                        } else {
                            alert('".htmlentities($msg["multi_select_champ"],ENT_QUOTES,$charset)."');
                        }
                    }
                </script>
                <select name='add_field' id='add_field' onChange=\"getFormInfos()\" >\n";
		} else {
		    $r="<select name='add_field' id='add_field'>\n";
		}
		$r.="<option value='' style='color:#000000'>".htmlentities($msg["multi_select_champ"],ENT_QUOTES,$charset)."</option>\n";

		$this->list_criteria = $this->get_misc_search_fields()->apply_substitution($this->get_list_criteria());
		foreach ($this->list_criteria as $group=>$criteria) {
			$r .= "<optgroup label='".htmlentities($group,ENT_QUOTES,$charset)."' class='optgroup_multicriteria'>\n";
			foreach ($criteria as $field) {
			    if (!empty($field['authperso_id'])) {
			        $r.="<option value='".$field['id']."' data-authperso_id='".$field['authperso_id']."'  style='color:#000000'>".htmlentities($field['label'],ENT_QUOTES,$charset)."</option>\n";
			    } else {
    				$r.="<option value='".$field['id']."' style='color:#000000'>".htmlentities($field['label'],ENT_QUOTES,$charset)."</option>\n";
			    }
			}
			$r.="</optgroup>\n";
		}
		$r.="</select>";
		return $r;
	}

	protected function get_formated_criterias(){
	    global $search;


	    if(!$this->full_path) {
	        $full_path = $include_path."/search_queries";
	    } else {
	        $full_path = substr($this->full_path, strlen($this->full_path)-1);
	    }
	    $misc_search_fields = new misc_file_search_fields($full_path, $this->fichier_xml.".xml");

	    $this->list_criteria = $this->get_misc_search_fields()->apply_substitution($this->get_list_criteria());
	    $result = [];
	    foreach($this->list_criteria as $typeName => $type){
	        foreach ($type as $field){
	            $field_id = explode("_", $field['id']);

	            $field_type = $field_id[0];
	            $field_id = $field_id[1];


	            switch($field_type){
	                case "f":
	                    $content = isset($this->fixedfields[$field_id]) ? $this->fixedfields[$field_id] : "err";
	                    break;
	                    // 	                case "d":
	                    // 	                    $content = isset($this->dynamicfields['d']['FIELD'][$field_id]) ? $this->dynamicfields['d']['FIELD'][$field_id] : "err";
	                    // 	                    break;
	                    // 	                case "e":
	                    // 	                    break;
	                    case "s":
	                        $content = isset($this->specialfields[$field_id]) ? $this->specialfields[$field_id] : "err";

	                        break;
	                    case "u":
	                        $content = isset($this->universesfields[$field_id]) ? $this->universesfields[$field_id] : "err";

	                        break;
	                    case "authperso":
	                        $content = isset($this->pp[$field_id]) ? $this->pp[$field_id] : "err";

	                        break;
	                    default:
	                        break 2;
	            }
	            if(!isset($result[$typeName])){
	                $result[$typeName] = [];
	            }

	                $result[$typeName][$field['id']] =  $this->format_field($content, $field['id']);
	        }
	    }
	    return $result;
	}

	protected function format_field($field, $field_id=""){
	    global $search;

	    $values = array(0 => "");
	    $operator = $field["QUERIES"][0]['OPERATOR'] ?? "";
	    $index_in_search = 0;
	    if (!empty($search) && in_array($field_id, $search)) {
	        $index = count($search);
	        for ($i = 0; $i < $index; $i++) {
	            if ($search[$i] == $field_id) {

	                $values = $this->get_global_value("field_".$i."_".$field_id);
	                $operator = $this->get_global_value("op_".$i."_".$field_id);
	                if($operator === "BETWEEN") {
	                    $values[1] = $this->get_global_value("field_".$i."_".$field_id."_1")[0];
	                }
	                $index_in_search = $i;
	                break;
	            }
	        }
	    }

	    $formated_field = [
	        "ID" => $field["ID"],
	        "GROUP" => $field["GROUP"],
	        "INPUT_OPTIONS" => $field["INPUT_OPTIONS"],
	        "INPUT_TYPE" => $field["INPUT_TYPE"],
	        "QUERIES" => $field["QUERIES"],
	        "QUERIES_INDEX" => $field["QUERIES_INDEX"],
	        "TITLE" => $field["TITLE"],
	        "VAR" => $this->format_fieldvar($field["VAR"], $field_id, $index_in_search),
	        "VARVIS" => $field["VARVIS"] ?? "",
	        "VISIBLE" => $field["VISIBLE"],
	        "VALUES" => $values,
	        "OPERATOR" => $operator,
	        "LINKFIELD" => $this->format_linkfield($field),
	    ];


	    switch($field['INPUT_TYPE']){
	        case 'marc_list':
	        case 'list':
	        case 'query_list':
	        case 'checkbox_list':
	        case 'checkbox_marc_list':
	        case 'query_list':

	        case 'checkbox_query_list':
	            $formated_field['INPUT_OPTIONS']['VALUES'] = $this->get_options_list_field($field);
	            break;



	        case "authoritie_external":
	        case "authoritie":
	        case "text":
	        case "date":
	        case "map" :
	        default:
	            break;
	    }
	    for($i=0; $i<count($formated_field['QUERIES']); $i++){
	        if(isset($formated_field['QUERIES'][$i]['OPERATOR'])){
	            $formated_field['QUERIES'][$i]['LABEL'] = $this->operators[$formated_field['QUERIES'][$i]['OPERATOR']];
	        }
	    }

	    return $formated_field;
	}

	private function format_linkfield($field) {
	    if ($field["INPUT_TYPE"] && $field["INPUT_TYPE"] == "authoritie" && !empty($field["INPUT_OPTIONS"]["AJAX"])) {
	        switch ($field["INPUT_OPTIONS"]["AJAX"]) {
	            case "categories":
	            case "categories_mul":
	                return "id_thesaurus";
	            case "onto":
	            case "categories_mul":
	                return "id_scheme";
	            default:
	                return "";
	        }
	    }
	    return "";
	}

	protected function format_fieldvar($fieldsvar, $field_id = "", $index_in_search = 0) {
	    global $msg;

	    $fieldvar = array();
	    if (!isset($fieldsvar)) {
	        $fieldsvar = array();
	    }
	    $index = is_countable($fieldsvar) ? count($fieldsvar) : 0;
	    $globl_fieldvar = $this->get_global_value("fieldvar".$index_in_search."".$field_id);
	    if (empty($globl_fieldvar)) {
	        $globl_fieldvar = array();
	    }
	    //Variables globales et input
	    for ($i=0; $i < $index; $i++) {
	        $name = $fieldsvar[$i]["NAME"];
	        switch ($fieldsvar[$i]["TYPE"]) {
	            case "input":
	                $var_table[$name] = "";
	                break;
	            case "global":
	                $var_table[$name] = $this->get_global_value($name);
	                break;
	        }
	    }

	    for ($i = 0; $i < $index; $i++) {
	        $var_field = $fieldsvar[$i];
	        if ($var_field["TYPE"]=="input") {
	            $visibility = 1;

	            if(isset($var_field["OPTIONS"]["VAR"][0])) {
	                $var = $var_field["OPTIONS"]["VAR"][0];
	                if ($var["NAME"]) {
	                    $varname = $var["NAME"];
	                    global ${$varname};

	                    if (isset($var_field["VISIBILITY"]) && $var_field["VISIBILITY"] == "no") {
	                        $visibility=0;
	                    }

	                    $index_value = count($var["VALUE"]);
	                    for ($j=0; $j < $index_value; $j++) {
	                        if ($var["VALUE"][$j]["value"] == ${$varname}) {

	                            $sub_vis=1;
	                            if ($var_field["VALUE"][$j]["VISIBILITY"]=="no") {
	                                $sub_vis=0;
	                            }

                                    if (!empty($var_field["VISIBILITY"]) && $var_field["VISIBILITY"] == "no") {
	                                $visibility |= $sub_vis;
	                            } else {
	                                $visibility &= $sub_vis;
	                            }

	                            break;
	                        }
	                    }
	                }
	            }
	            $default_value = "";
	            //Recherche de la valeur par defaut
	            if(isset($var_field["OPTIONS"]["DEFAULT"][0])) {
	                $default = $var_field["OPTIONS"]["DEFAULT"][0];
	                if (!empty($default) && count($default)) {
	                    switch ($default["TYPE"]) {
    	                    case "var":
                                $default_value = $var_table[$default["value"]] ?? "";
    	                        break;
    	                    case "value":
    	                    default:
    	                        $default_value = $default["value"];
    	                }
    	            };
	            }

	            if ($visibility) {
	                $input = $var_field["OPTIONS"]["INPUT"][0];
	                $key = count($fieldvar);
	                $fieldvar[$key] = $fieldsvar[$i];
	                $fieldvar[$key]["VALUE"] = $default_value;

	                $fieldvar[$key]["INPUT_TYPE"] = $input["TYPE"];
	                switch ($input["TYPE"]) {
	                    case "query_list":

	                        $fieldvar[$key]["VALUES"] = array();

	                        $result = pmb_mysql_query($input["QUERY"][0]["value"]);
	                        if (pmb_mysql_num_rows($result)) {
	                            if($input["QUERY"][0]["ALLCHOICE"] == "yes") {
	                                $all_choice = true;
	                                $all_value = "";
	                            }
	                            if(!empty($input["QUERY"][0]["TRANSLATIONTABLENAME"]) && !empty($input["QUERY"][0]["TRANSLATIONFIELDNAME"])) {
	                                $trans_table = $input["QUERY"][0]["TRANSLATIONTABLENAME"];
	                                $trans_field = $input["QUERY"][0]["TRANSLATIONFIELDNAME"];
	                            } else {
	                                $trans_table = '';
	                                $trans_field = '';
	                            }
	                            while($row = pmb_mysql_fetch_assoc($result)) {

	                                if ($all_choice) {
	                                    if (!empty($all_value)) {
	                                        $all_value .= ",";
	                                    }
	                                    $all_value .= $row[0];
	                                }
	                                if(!empty($trans_table) && !empty($trans_field)) {
	                                    $fieldvar[$key]["OPTIONS"][] = [
	                                        "VALUE" => $row[0] ?? "",
	                                        "LABEL" => ($row[1] ? translation::get_translated_text($row[0], $trans_table, $trans_field, $row[1]) : "")
	                                    ];
	                                } else {
	                                    $fieldvar[$key]["OPTIONS"][] = [
	                                        "VALUE" => $row[0] ?? "",
	                                        "LABEL" => $row[1] ?? ""
	                                    ];
	                                }
	                            }
	                            if ($all_choice) {
	                                $label = $input["QUERY"][0]["TITLEALLCHOICE"];
	                                $fieldvar[$key]["OPTIONS"][] = [
	                                    "VALUE" => $all_value,
	                                    "LABEL" => $msg[substr($label, 4, strlen($label)-4)]
	                                ];
	                            }
	                        }
	                        break;
	                    case "checkbox":
	                        $fieldvar[$key]["VALUE"] = $input["VALUE"][0]["value"];
	                        $fieldvar[$key]["CHECKED"] = false;
	                        break;
	                    case "radio":
	                        $fieldvar[$key]["VALUES"] = array();
	                        foreach($input["OPTIONS"][0]["LABEL"] as $radio_value) {
	                            $fieldvar[$key]["OPTIONS"][] = [
	                                "VALUE" => $radio_value["VALUE"],
	                                "LABEL" => $msg[substr($radio_value["value"], 4, strlen($radio_value["value"])-4)]
	                            ];
                                if ((isset($varname) && isset($fieldvar[$varname])) && $radio_value["VALUE"] == $fieldvar[$varname][0]) {
	                                $fieldvar[$key]["VALUE"] = $radio_value["VALUE"];
	                            }
	                        }
	                        break;
	                    case "number":
	                    case "hidden":
	                        if (is_array($input["VALUE"][0])) {
	                            $value = $input["VALUE"][0]["value"];
	                        }
	                        $fieldvar[$key]["VALUE"] = $value;
	                        break;
	                }
	            } elseif (!empty($var) && $var["HIDDEN"] != "no") {
	                $key = count($fieldvar);
	                $fieldvar[$key] = $fieldsvar[$i];
	                $fieldvar[$key]["VALUE"] = $default_value;
	                $fieldvar[$key]["INPUT_TYPE"] = "hidden";
	            }
	        }
	    }

	    return $fieldvar;
	}

	public function get_search_form() {
	    global $search, $msg;
		global $opac_rgaa_active;
	    $inter_array = [];
	    $criterias = $this->get_formated_criterias();
	    for($i=0; $i<count($search);$i++){
	        $inter = $this->get_global_value("inter_".$i."_".$search[$i]);
	        if(isset($inter)){
	            $inter_array[$i] = $inter;
	        }
	        if(!isset($inter_array[$i])){
	            $inter_array[$i] = "and";
	        }
	    }
	    //On remplit la search avec les 3 premiers critères et l'inter par défaut si elle est vide
	    if(empty($search)){
	        for($i=0; $i<3; $i++){
	            $search[] = $this->list_criteria[$this->groups[1]["label"]][$i]["id"];
	            $inter_array[] = self::DEFAULT_INTER;
	        }
	    }


	    $searchView = new SearchformView("searchform/searchform", [
	        "criterias" => $criterias,
	        "search" => $search,
	        "inter" => [
	            "or" => $msg['search_or'],
	            "and" => $msg['search_and'],
	            "ex" => $msg['search_exept'],

	        ],
	        "inter_values" => $inter_array,
	        "hidden" => [
	            "explicit_search" => "1",
	            "search_xml_file" => $this->fichier_xml,
	            "launch_search" => "1",
	            "no_search" => "0",
	            "page" => "",
	            "add_field" => "",
	            "delete_field" => ""
			],
			'globals' => [
				'opac_rgaa_active' => intval($opac_rgaa_active)
			]

	    ]);
	    return $searchView->render();
	}
	/**
	 * Templates des listes d'operateurs
	 * @param string $url
	 * @param string $result_url
	 * @param string $result_target
	 * @param string $memo_url
	 * @param string $only_selected_fields
	 */
	public function show_form($url,$result_url,$result_target='',$memo_url='',$only_selected_fields='false') {
		global $charset;
		global $search;
		global $add_field;
		global $delete_field;
		global $launch_search;
		global $page;
		global $search_form;
		global $msg;
		global $include_path;
		global $option_show_expl,$option_show_notice_fille;
		global $pmb_extended_search_auto, $pmb_extended_search_dnd_interface;
		global $authperso_id;
		global $id_predefined_search;
		global $mode;

		//$search_form = $this->get_search_form();

		if($option_show_expl)$option_show_expl_check="checked='checked'";
		else $option_show_expl_check="";
		if($option_show_notice_fille)$option_show_notice_fille_check="checked='checked'";
		else $option_show_notice_fille_check="";
		$option="
    		<div class='row'>
    			<h3>".$msg['search_option_show_title']."</h3>
	    			<input $option_show_expl_check value='1' name='option_show_expl' id='option_show_expl'  type='checkbox'>".$msg["search_option_show_expl"]."
	    			<input $option_show_notice_fille_check value='1' name='option_show_notice_fille' id='option_show_notice_fille'  type='checkbox'>".$msg["search_option_show_notice_fille"]."
    		</div>";
		$search_form=str_replace("<!--!!limitation_affichage!!-->",$option,$search_form);

		if (($add_field)&&(($delete_field==="")&&(!$launch_search))) {
			if(empty($search)) {
				$search = array();
			}
			$search[]=$add_field;
		}
		$search_form=str_replace("!!search_form_title!!",$this->get_form_title(),$search_form);
		$search_form=str_replace("!!url!!",$url,$search_form);
		if(!$memo_url) {
		    $memo_url="catalog.php?categ=search_perso";
		    if($mode == 8) {
		        $memo_url.="&type=EXPL";
		    }
		    $memo_url.="&sub=edit";
			if($id_predefined_search) {
				$memo_url.="&id=".$id_predefined_search;
			}
		}
		$search_form=str_replace("!!memo_url!!",$memo_url,$search_form);

		//Génération de la liste des champs possibles
		if($this->limited_search){
			$search_form = str_replace("!!limit_search!!","<input type='hidden' id='limited_search' name='limited_search' />",$search_form);
			$limit_search = " this.form.limited_search.value='1'; ";
		} else {
			$search_form = str_replace("!!limit_search!!","",$search_form);
			$limit_search = "";
		}

		$r = $this->get_field_selector($url, $limit_search);

		$search_form=str_replace("!!field_list!!",$r,$search_form);

		$search_form=str_replace("!!already_selected_fields!!",$this->get_already_selected_fields(),$search_form);
		$search_form=str_replace("!!page!!", $page ?? "",$search_form);
		$search_form=str_replace("!!result_url!!",$result_url,$search_form);

		global $dsi_active, $id_equation;
		if ($dsi_active && !empty($id_equation)) {
			$search_form=str_replace("!!id_equation!!", $id_equation, $search_form);
		} else {
			$search_form=str_replace("!!id_equation!!","",$search_form);
		}

		global $id_search_persopac;
		if ($id_search_persopac) {
			$search_form=str_replace("!!id_search_persopac!!",$id_search_persopac,$search_form);
		} else {
			$search_form=str_replace("!!id_search_persopac!!","",$search_form);
		}

		global $pmb_opac_view_activate;
		if ($pmb_opac_view_activate) {
			global $opac_view_id;
			$search_form=str_replace("!!opac_view_id!!",$opac_view_id,$search_form);
		} else {
			$search_form=str_replace("!!opac_view_id!!","",$search_form);
		}

		global $id_connector_set;
		if (isset($id_connector_set)) {
		    $search_form=str_replace("!!id_connector_set!!",$id_connector_set,$search_form);
		} else {
		    $search_form=str_replace("!!id_connector_set!!","",$search_form);
		}

		global $search_type;
		if (isset($search_type)) {
		    $search_form=str_replace("!!search_type!!",$search_type,$search_form);
		} else {
		    $search_form=str_replace("!!search_type!!","",$search_form);
		}

		if ($result_target) $r="document.search_form.target='$result_target';"; else $r="";
		$search_form=str_replace("!!target_js!!",$r,$search_form);

		$search_form .= "\n\n<script type=\"text/javascript\" >
			function change_source_checkbox(changing_control, source_id) {
				var i=0; var count=0;
				onoff = changing_control.checked;
				for(i=0; i<document.search_form.elements.length; i++) {
					if(document.search_form.elements[i].name == 'source[]')	{
						if (document.search_form.elements[i].value == source_id)
							document.search_form.elements[i].checked = onoff;
					}
				}
			}

			function date_flottante_type_onchange(varname, operator) {
				if(!document.getElementById(varname + '_date_begin_zone_label')) return;
				switch(operator) {
					case 'ISEMPTY': // vide
					case 'ISNOTEMPTY': // pas vide
						document.getElementById(varname + '_date_begin_zone').style.display = 'none';
						document.getElementById(varname + '_date_end_zone').style.display = 'none';
						break;
					case 'BETWEEN': // interval date
						document.getElementById(varname + '_date_begin_zone').style.display = '';
						document.getElementById(varname + '_date_begin_zone_label').style.display = '';
						document.getElementById(varname + '_date_end_zone').style.display = '';
						break;
					default:
						// NEAR, =, <=, >=
						document.getElementById(varname + '_date_begin_zone').style.display = '';
						document.getElementById(varname + '_date_begin_zone_label').style.display = 'none';
						document.getElementById(varname + '_date_end_zone').style.display = 'none';
						break;
				}
			}

			function getFieldDate(field_name, field_value) {
				var field = document.createElement('input');
				field.setAttribute('type', 'text');
				field.setAttribute('id', 'field_' +field_name+'[]');
				field.setAttribute('name', 'field_' +field_name+'[]');
				field.setAttribute('style', 'width: 10em;');
				field.setAttribute('value',field_value);
				field.setAttribute('data-dojo-type','dijit/form/DateTextBox');
				field.setAttribute('constraints','{datePattern:\"".getDojoPattern($msg['format_date'])."\"}');
				field.setAttribute('required','false');
				return field;
			}

			function getFieldDateNumber(field_name, field_value) {
				var field = document.createElement('input');
				field.setAttribute('type', 'text');
				field.setAttribute('id', 'field_' +field_name+'[]');
				field.setAttribute('name', 'field_' +field_name+'[]');
				field.setAttribute('style', 'width: 10em;');
				field.setAttribute('value',field_value);
				return field;
			}

			//callback du selecteur d'opérateur
			function operatorChanged(field,operator,datatype) {
				if(datatype == 'small_text') {
					date_flottante_type_onchange('field_' + field, operator);
				}
				if(datatype == 'date') {
					switch(operator) {
						case 'BETWEEN': // 2eme champ date visible
							var part = document.getElementById('field_'+field+'_start_part[]');
							if(part){
								part.style.display='inline';
								part.title='".$msg['search_between_query_date_start']."'
							}
							var part = document.getElementById('field_'+field+'_end_part[]');
							if(part){
								part.style.display='inline';
								part.title='".$msg['search_between_query_date_end']."'
							}
							break;
						case 'ISEMPTY':
						case 'ISNOTEMPTY':
						case 'THIS_WEEK':
						case 'LAST_WEEK':
						case 'THIS_MONTH':
						case 'LAST_MONTH':
						case 'THIS_YEAR': // aucun champ date visible
							var part = document.getElementById('field_'+field+'_start_part[]');
							if(part) part.style.display='none';
							var part = document.getElementById('field_'+field+'_end_part[]');
							if(part) part.style.display='none';
							break;
						case 'LESS_THAN_DAYS':
						case 'MORE_THAN_DAYS':
							var part = document.getElementById('field_'+field+'_start_part[]');
							if(part){
								part.style.display='block';
								part.title=''
							}
							var part = document.getElementById('field_'+field+'_end_part[]');
							if(part) part.style.display='none';
							break;
						default : // un seul champ date
							var part = document.getElementById('field_'+field+'_start_part[]');
							if(part){
								part.style.display='block';
								part.title=''
							}
							var part = document.getElementById('field_'+field+'_end_part[]');
							if(part) part.style.display='none';
						break;
					}
					return;
				}
				if(	!document.getElementById('field_'+field+'_max_aut') ) return;
				for(i=0;i<=document.getElementById('field_'+field+'_max_aut').value;i++){
					var f_lib = document.getElementById('field_'+field+'_lib_'+i);
					var f_id = document.getElementById('field_'+field+'_id_'+i);
					var f = document.getElementById('field_'+field+'_'+i);
					if(operator == 'AUTHORITY' || (f_lib.getAttribute('completion') && (operator == 'EQ'))){
		//				var authority_id = document.getElementById('fieldvar_'+field+'_authority_id');
		//				f_lib.setAttribute('class','saisie-20emr');
		//				if(authority_id.value != 0) f.value = authority_id.value;
					}else {
						f_lib.removeAttribute('class');
						f.value = f_lib.value;
					}
					if(f_lib.getAttribute('completion')){
						if (f_lib.getAttribute('class')) {
							f_lib.setAttribute('class',f_lib.getAttribute('class') + ' expand_completion');
						} else {
							f_lib.setAttribute('class','expand_completion');
						}
					}
				}
			}

			//callback du selecteur AJAX
			function selectionSelected(infield) {
				//on enlève le dernier _X
				var tmp_infield = infield.split('_');
				var tmp_infield_length = tmp_infield.length;
				//var inc = tmp_infield[tmp_infield_length-1];
				tmp_infield.pop();
				infield = tmp_infield.join('_');
				//pour assurer la compatibilité avec le selecteur AJAX
				infield=infield.replace('_lib','');

				var op_name =infield.replace('field','op');
				var op_selector = document.forms['search_form'][op_name];
				//on passe le champ en selecteur simple !
				for (var i=0 ; i<op_selector.options.length ; i++) {
					if(op_selector.options[i].value == 'EQ')
						op_selector.options[i].selected = true;
				}
				var empty_line=false;
				for(i=0;i<=document.getElementById(infield+'_max_aut').value;i++) {
					var searchField = document.getElementById(infield+'_'+i);
					var f_lib = document.getElementById(infield+'_lib'+'_'+i);
					var f_id = document.getElementById(infield+'_id'+'_'+i);

					if(f_lib) {
						f_lib.setAttribute('class','saisie-20emr expand_completion');
					}
					if(f_id && f_id.value=='') {
						f_id.value=0;
					}
					if(f_id) {
						if(f_id.value == 0) {
							empty_line = true;
						}
						searchField.value=f_id.value;
					}
				}
				if(!empty_line) {
					add_line(infield, 'EQ');
				}
			}

			//callback du selecteur AJAX pour les autorités
			function authoritySelected(infield) {
				//on enlève le dernier _X
				var tmp_infield = infield.split('_');
				var tmp_infield_length = tmp_infield.length;
				//var inc = tmp_infield[tmp_infield_length-1];
				tmp_infield.pop();
				infield = tmp_infield.join('_');
				//pour assurer la compatibilité avec le selecteur AJAX
				infield=infield.replace('_lib','');
				infield=infield.replace('_authority_label','');

				var op_name =infield.replace('field','op');
				var op_selector = document.forms['search_form'][op_name];
				//on passe le champ en selecteur d'autorité !
				for (var i=0 ; i<op_selector.options.length ; i++) {
					if(op_selector.options[i].value == 'AUTHORITY') {
						op_selector.options[i].selected = true;
					    op_selector.disabled=true;
			        }
				}
				var empty_line=false;
				var max_aut = document.getElementById(infield+'_max_aut');
				if (max_aut) {
    				for(i=0;i<=max_aut.value;i++) {
						var searchField = document.getElementById(infield+'_'+i);
						var f_lib = document.getElementById(infield+'_lib'+'_'+i);
						var f_id = document.getElementById(infield+'_id'+'_'+i);
						var authority_id = document.getElementById(infield.replace('field','fieldvar')+'_authority_id'+'_'+i);

						if(f_lib) {
							f_lib.setAttribute('class','saisie-20emr expand_completion');
						}
						if(f_id && f_id.value=='') {
							f_id.value=0;
						}
						if(f_id) {
							if(f_id.value == 0) {
                                empty_line = true;
                            }
                            searchField.value=f_id.value;
                            authority_id.value= f_id.value;
						}
					}
    				if(!empty_line && op_selector.value == 'AUTHORITY') {
						add_line(infield, 'AUTHORITY');
					}
				}
			}

			//callback sur la saisie libre
			function fieldChanged(id,inc,value,e) {
				var ma_touche;
				if(window.event){
					ma_touche=window.event.keyCode;
				}else{
					ma_touche=e.keyCode;
				}
				var f_lib = document.getElementById(id+'_lib_'+inc);
				var f_id = document.getElementById(id+'_id_'+inc);
				var f = document.getElementById(id+'_'+inc);
				var selector = document.forms['search_form'][id.replace('field','op')];
				if (selector.options[selector.selectedIndex].value != 'AUTHORITY')
					f.value = value;
				else if(ma_touche != 13) {
					var max_aut=document.getElementById(id+'_max_aut').value;
					if(max_aut>0) {
						//Plus d'un champ : on bloque
						return;
					}
					f_lib.setAttribute('class','ext_search_txt');
					selector.options[0].selected = true;
					f.value = f_lib.value;
					if(document.getElementById(id.replace('field','fieldvar')+'_authority_id'+'_'+inc)) {
						var authority_id = document.getElementById(id.replace('field','fieldvar')+'_authority_id'+'_'+inc);
						authority_id.value = '';
					}
				}
			}

            function raz_line(fnamesans, inc) {
                var fname_id=fnamesans+'_id';
				var fnamesanslib=fnamesans+'_lib';
                if(document.getElementById(fnamesanslib+'_'+inc)) {
                    document.getElementById(fnamesanslib+'_'+inc).value = '';
                }
                if(document.getElementById(fname_id+'_'+inc)) {
                    document.getElementById(fname_id+'_'+inc).value = '0';
                }
                if(document.getElementById('fieldvar_'+fname_id+'_authority_id')) {
                    document.getElementById('fieldvar_'+fname_id+'_authority_id').value = '0';
                }
                if(document.getElementById(fnamesans+'_'+inc)) {
                    document.getElementById(fnamesans+'_'+inc).value = '0';
                }
            }

			function add_line(fnamesans, type) {
				var fname=fnamesans+'[]';
				var fname_id=fnamesans+'_id';
				var fnamesanslib=fnamesans+'_lib';
				var fnamelib=fnamesans+'_lib[]';
				var op=fnamesans.replace('field','op');
				var tmp_fnamesans = fnamesans.split('_');
				var search_field_id=tmp_fnamesans[2]+'_'+tmp_fnamesans[3];

				var template = document.getElementById('el'+fnamesans);
				var inc=document.getElementById(fnamesans+'_max_aut').value;
				inc++;
		        var line=document.createElement('div');
				line.setAttribute('style','display:inline;');

				var f_id = document.createElement('input');
				f_id.setAttribute('id',fnamesans+'_'+inc);
				f_id.setAttribute('name',fname);
				f_id.setAttribute('value','');
				f_id.setAttribute('type','hidden');

				var f_lib = document.createElement('input');
				f_lib.setAttribute('autfield',fname_id+'_'+inc);
				f_lib.setAttribute('onkeyup','fieldChanged(\''+fnamesans+'\',\''+inc+'\',this.value,event)');
				if(document.getElementById(fnamesanslib+'_0').getAttribute('completion')){
					f_lib.setAttribute('completion',document.getElementById(fnamesanslib+'_0').getAttribute('completion'));
					if(f_lib.getAttribute('completion') == 'onto') {
						f_lib.setAttribute('att_id_filter', 'http://www.w3.org/2004/02/skos/core#Concept');
    				}
				}
				switch(type) {
					case 'AUTHORITY':
						f_lib.setAttribute('callback','authoritySelected');
						var fname_name_aut_id=fnamesans+'[authority_id][]';
						var fname_name_aut_id=fname_name_aut_id.replace('field','fieldvar');
						var fname_aut_id=fnamesans+'_authority_id';
						var fname_aut_id=fname_aut_id.replace('field','fieldvar');
						var f_aut = document.createElement('input');
						f_aut.setAttribute('type','hidden');
						f_aut.setAttribute('value','');
						f_aut.setAttribute('id',fname_aut_id+'_'+inc);
						f_aut.setAttribute('name',fname_name_aut_id);
						break;
					case 'EQ':
						f_lib.setAttribute('callback','selectionSelected');
						f_lib.setAttribute('param1', '".$this->fichier_xml."');
						f_lib.setAttribute('param2', search_field_id);
						break;
				}
				f_lib.setAttribute('id',fnamesanslib+'_'+inc);
				f_lib.setAttribute('name',fnamelib);
				f_lib.setAttribute('value','');
				f_lib.setAttribute('type','text');
				if(document.getElementById(fnamesanslib+'_0').getAttribute('linkfield')){
					f_lib.setAttribute('linkfield',document.getElementById(fnamesanslib+'_0').getAttribute('linkfield'));
				}
				var op_selected = document.getElementById(op).options[document.getElementById(op).selectedIndex].value;
				if (op_selected == 'AUTHORITY'){
					f_lib.setAttribute('class','authorities saisie-20emr expand_completion');
				} else if(f_lib.getAttribute('completion') && (op_selected == 'EQ')) {
					f_lib.setAttribute('class','saisie-20emr expand_completion');
				} else {
					f_lib.setAttribute('class','expand_completion');
				}

				var f_del = document.createElement('input');
				f_del.setAttribute('class','bouton vider');
				f_del.setAttribute('type','button');
				f_del.setAttribute('onclick','raz_line(\''+fnamesans+'\', \''+inc+'\');');
				f_del.setAttribute('value','".htmlentities($msg['raz'], ENT_QUOTES, $charset)."');

				var f_id2 = document.createElement('input');
				f_id2.setAttribute('type','hidden');
				f_id2.setAttribute('value','');
				f_id2.setAttribute('id',fname_id+'_'+inc);
				f_id2.setAttribute('name',fname_id);

		        line.appendChild(f_id);
		        line.appendChild(f_lib);
		        line.appendChild(f_del);
		        if(type == 'AUTHORITY') {
					line.appendChild(f_aut);
				}
		        line.appendChild(f_id2);

		        template.appendChild(line);
		        template.appendChild(document.createElement('br'));

				ajax_pack_element(f_lib);
				f_lib.focus();

		        document.getElementById(fnamesans+'_max_aut').value=inc;

				//Plus d'un champ : on bloque
				var selector = document.getElementById(op);
				selector.disabled=true;
				if(operators_to_enable.indexOf(op) === -1) {
					operators_to_enable.push(op);
				}
			}

			function enable_operators() {
				if(operators_to_enable.length>0){
					for	(index = operators_to_enable.length; index >= 0; index--) {
					    if(document.getElementById(operators_to_enable[index])) {
							document.getElementById(operators_to_enable[index]).disabled=false;
						} else {
							operators_to_enable.splice(index,1);
						}
					}
				}
			}

			function enable_operator(fnamesans, index) {
				var empty = true;
				var max = document.getElementById(fnamesans+'_max_aut').value;
				for(var i = 0; i < max; i++) {
					if(parseInt(document.getElementById(fnamesans+'_id_'+i).value) != 0) {
						empty = false;
					}
				}
				var op_name =fnamesans.replace('field','op');
                if(empty && operators_to_enable.indexOf(op_name) !== -1) {
					document.getElementById(op_name).disabled=false;
				}
			}
			".$this->script_window_onload."
		</script>";

		if ($pmb_extended_search_dnd_interface) {
			$search_form .= $this->show_dnd_form();
		}
		return $search_form;
	}

	public function get_already_selected_fields() {
		global $add_field;
		global $delete_field;
		global $search;
		global $launch_search;
		global $charset;
		global $msg;
		global $include_path;

		//Affichage des champs deja saisis
		$r="";
		$n=0;
		$this->script_window_onload='';
		$r.="<table id='extended-search-container' class='table-no-border' role='presentation'>";
		if(is_array($search) && count($search)){
			for ($i=0; $i<count($search); $i++) {
				if ((string)$i!=$delete_field) {
					$f=explode("_",$search[$i]);
					//On regarde si l'on doit masquer des colonnes
					$notdisplaycol=array();
					if ($f[0]=="f") {
						if($this->fixedfields[$f[1]]["NOTDISPLAYCOL"]){
							$notdisplaycol=explode(",",$this->fixedfields[$f[1]]["NOTDISPLAYCOL"]);
						}
					} elseif ($f[0]=="s") {
						if($this->specialfields[$f[1]]["NOTDISPLAYCOL"]){
							$notdisplaycol=explode(",",$this->specialfields[$f[1]]["NOTDISPLAYCOL"]);
						}
					} elseif (array_key_exists($f[0],$this->pp)) {
						if(isset($this->pp[$f[0]]->t_fields[$f[1]]["NOTDISPLAYCOL"]) && $this->pp[$f[0]]->t_fields[$f[1]]["NOTDISPLAYCOL"]){
							$notdisplaycol=explode(",",$this->pp[$f[0]]->t_fields[$f[1]]["NOTDISPLAYCOL"]);
						}
					}
					$r.="<tr>";
					$r.="<td ".(in_array("1",$notdisplaycol)?"style='display:none;'":"").">";//Colonne 1
					$r.="<input type='hidden' name='search[]' value='".$search[$i]."'>";
					$r.="</td>";
					$r.="<td class='search_first_column' ".(in_array("2",$notdisplaycol)?"style='display:none;'":"").">";//Colonne 2
					if ($n>0) {
						$inter="inter_".$i."_".$search[$i];
						global ${$inter};
						$r.="<span class='search_operator'><select name='inter_".$n."_".$search[$i]."'>";
						$r.="<option value='and' ";
						if (${$inter}=="and")
							$r.=" selected";
						$r.=">".$msg["search_and"]."</option>";
						$r.="<option value='or' ";
						if (${$inter}=="or")
							$r.=" selected";
						$r.=">".$msg["search_or"]."</option>";
						$r.="<option value='ex' ";
						if (${$inter}=="ex")
							$r.=" selected";
						$r.=">".$msg["search_exept"]."</option>";
						$r.="</select></span>";
					} else $r.="&nbsp;";
					$r.="</td>";

					$r.="<td ".(in_array("3",$notdisplaycol)?"style='display:none;'":"")."><span class='search_critere'>";//Colonne 3
					if ($f[0]=="f") {
						$r.=htmlentities($this->fixedfields[$f[1]]["TITLE"],ENT_QUOTES,$charset);
					} elseif ($f[0]=="s") {
						$r.=htmlentities($this->specialfields[$f[1]]["TITLE"],ENT_QUOTES,$charset);
					} elseif (array_key_exists($f[0],$this->pp)) {
						$r.=htmlentities($this->pp[$f[0]]->t_fields[$f[1]]["TITRE"],ENT_QUOTES,$charset);
					}elseif ($f[0]=="authperso") {
						$r.=htmlentities($this->authpersos[$f[1]]['name'],ENT_QUOTES,$charset);
					}
					$r.="</span></td>";
					//Recherche des operateurs possibles
					$r.="<td ".(in_array("4",$notdisplaycol)?"style='display:none;'":"").">";//Colonne 4
					$op="op_".$i."_".$search[$i];
					global ${$op};
					if ($f[0]=="f") {
						$r.="<span class='search_sous_critere'><select name='op_".$n."_".$search[$i]."' id='op_".$n."_".$search[$i]."'";
						//gestion des autorités
						$onchange ="";
						if (isset($this->fixedfields[$f[1]]["QUERIES_INDEX"]["AUTHORITY"])){
							$selector=$this->fixedfields[$f[1]]["INPUT_OPTIONS"]["SELECTOR"];
							$p1=$this->fixedfields[$f[1]]["INPUT_OPTIONS"]["P1"];
							$p2=$this->fixedfields[$f[1]]["INPUT_OPTIONS"]["P2"];
						}
						$onchange =" onchange='operatorChanged(\"".$n."_".$search[$i]."\",this.value, \"".$this->fixedfields[$f[1]]['INPUT_TYPE']."\");' ";
						$r.="$onchange>\n";

						//Personnalisation des opérateurs par l'interface
						$this->fixedfields[$f[1]]["QUERIES"] = $this->get_misc_search_fields()->apply_operators_substitution($search[$i], $this->fixedfields[$f[1]]["QUERIES"]);

						if (is_countable($this->fixedfields[$f[1]]["QUERIES"])) {
    						for ($j=0; $j<count($this->fixedfields[$f[1]]["QUERIES"]); $j++) {
    							$q=$this->fixedfields[$f[1]]["QUERIES"][$j];
    							$r.="<option value='".$q["OPERATOR"]."' ";
    							if (${$op}==$q["OPERATOR"]) $r.=" selected";
    							$r.=">".htmlentities($this->operators[$q["OPERATOR"]],ENT_QUOTES,$charset)."</option>\n";
    						}
						}
						$r.="</select></span>";
						$this->script_window_onload.=" operatorChanged('".$n."_".$search[$i]."', document.getElementById('op_".$n."_".$search[$i]."').value,'".$this->fixedfields[$f[1]]['INPUT_TYPE']."'); ";
					} elseif (array_key_exists($f[0],$this->pp)) {
						$datatype=$this->pp[$f[0]]->t_fields[$f[1]]["DATATYPE"];
						$type=$this->pp[$f[0]]->t_fields[$f[1]]["TYPE"];
						$df=$this->get_id_from_datatype($datatype, $f[0]);
						$onchange =" onchange=\"operatorChanged('".$n."_".$search[$i]."', this.value,'".$datatype."');\" ";

						$r.="<span class='search_sous_critere'><select name='op_".$n."_".$search[$i]."'  id='op_".$n."_".$search[$i]."' ".$onchange.">\n";

						//Personnalisation des opérateurs par l'interface
						$this->dynamicfields[$f[0]]["FIELD"][$df]["QUERIES"] = $this->get_misc_search_fields()->apply_operators_substitution($search[$i], $this->dynamicfields[$f[0]]["FIELD"][$df]["QUERIES"]);

						for ($j=0; $j<count($this->dynamicfields[$f[0]]["FIELD"][$df]["QUERIES"]); $j++) {
							$q=$this->dynamicfields[$f[0]]["FIELD"][$df]["QUERIES"][$j];
							$as=array_search($type,$q["NOT_ALLOWED_FOR"]);
							if (!(($as!==null)&&($as!==false))) {
								$r.="<option value='".$q["OPERATOR"]."' ";
								if (${$op}==$q["OPERATOR"]) $r.="selected";
								$r.=">".htmlentities($this->operators[$q["OPERATOR"]],ENT_QUOTES,$charset)."</option>\n";
							}
						}
						$r.="</select></span>&nbsp;";
						$this->script_window_onload.=" operatorChanged('".$n."_".$search[$i]."', document.getElementById('op_".$n."_".$search[$i]."').value,'".$datatype."'); ";
					} elseif ($f[0]=="s") {
						//appel de la fonction get_input_box de la classe du champ special
						$type=$this->specialfields[$f[1]]["TYPE"];
						for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
							if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
								$sf=$this->specialfields[$f[1]];
								require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
								$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($f[1],$n,$sf,$this);
								$q=$specialclass->get_op();
								if (count($q)) {
									$r.="<span class='search_sous_critere'><select id='op_".$n."_".$search[$i]."' name='op_".$n."_".$search[$i]."'>\n";
									foreach ($q as $key => $value) {
										$r.="<option value='".$key."' ";
										if (${$op}==$key) $r.="selected";
										$r.=">".htmlentities($value,ENT_QUOTES,$charset)."</option>\n";
									}
									$r .= "</select></span>";
								} else $r.= "&nbsp;";
								break;
							}
						}
					} elseif ($f[0]=="authperso") {
						$type = '';
						//on est sur le cas de la recherche "Tous les champs" de l'autorité perso
						//$f["1"] vaut l'identifiant du type d'autorité perso
						$df=10;
						$r.="<span class='search_sous_critere'><select name='op_".$n."_".$search[$i]."' id='op_".$n."_".$search[$i]."'";
						//gestion des autorités
						$onchange ="";
						if (isset($this->dynamicfields["a"]["FIELD"][$df]["QUERIES_INDEX"]["AUTHORITY"]) && isset($this->dynamicfields["a"]["FIELD"][$df]["INPUT_OPTIONS"])){
							$selector=$this->dynamicfields["a"]["FIELD"][$df]["INPUT_OPTIONS"]["SELECTOR"];
							$p1=$this->dynamicfields["a"]["FIELD"][$df]["INPUT_OPTIONS"]["P1"];
							$p2=$this->dynamicfields["a"]["FIELD"][$df]["INPUT_OPTIONS"]["P2"];
							$onchange =" onchange='operatorChanged(\"".$n."_".$search[$i]."\",this.value);' ";
						}
						$r.="$onchange>\n";

						//Personnalisation des opérateurs par l'interface
						$this->dynamicfields["a"]["FIELD"][$df]["QUERIES"] = $this->get_misc_search_fields()->apply_operators_substitution($search[$i], $this->dynamicfields["a"]["FIELD"][$df]["QUERIES"]);

						for ($j=0; $j<count($this->dynamicfields["a"]["FIELD"][$df]["QUERIES"]); $j++) {
							$q=$this->dynamicfields["a"]["FIELD"][$df]["QUERIES"][$j];
							$as=array_search($type,$q["NOT_ALLOWED_FOR"]);
							if (!(($as!==null)&&($as!==false))) {
								$r.="<option value='".$q["OPERATOR"]."' ";
								if (${$op}==$q["OPERATOR"]) $r.="selected";
								$r.=">".htmlentities($this->operators[$q["OPERATOR"]],ENT_QUOTES,$charset)."</option>\n";
							}
						}
						$r.="</select></span>";
						$r.="&nbsp;";
					}
					$r.="</td>";

					//Affichage du champ de saisie
					$r.="<td ".(count($notdisplaycol)?"colspan='".(count($notdisplaycol)+1)."'":"")." ".(in_array("5",$notdisplaycol)?"style='display:none;'":"")." class='td-border-display'>";//Colonne 5

					if (!empty($this->op_special[${$op}])) {
					    $r .= $this->get_field_op_special(${$op}, $this->fixedfields[$f[1]], $i, $n, $search[$i], $this->pp);
					} else {
    					$r.=$this->get_field($i,$n,$search[$i],$this->pp);
					}

					$r.="</td>";
					$delnotallowed=false;
					if ($f[0]=="f") {
						$delnotallowed=(isset($this->fixedfields[$f[1]]["DELNOTALLOWED"]) ? $this->fixedfields[$f[1]]["DELNOTALLOWED"] : '');
					} elseif ($f[0]=="s") {
						$delnotallowed=(isset($this->specialfields[$f[1]]["DELNOTALLOWED"]) ? $this->specialfields[$f[1]]["DELNOTALLOWED"] : '');
					}
					if($this->limited_search) {
						$script_limit = " this.form.limited_search.value='0'; ";
					} else {
						$script_limit = "";
					}
					$r.="<td ".(in_array("6",$notdisplaycol)?"style='display:none;'":"")."><span class='search_cancel'>".(!$delnotallowed?"<input id='delete_field_button_".$n."' type='button' class='bouton' value='".$msg["raz"]."' onClick=\"enable_operators(); this.form.delete_field.value='".$n."'; this.form.action=''; this.form.target=''; $script_limit this.form.submit();\">":"&nbsp;")."</td>";//Colonne 6
					$r.="</tr>\n";
					$n++;
				}
			}
		}
		//Recherche explicite
		$r.="</table>\n";
		$r.="<input type='hidden' name='explicit_search' value='1'/>\n";
		$r.="<input type='hidden' name='search_xml_file' value='".$this->fichier_xml."'/>\n";
		$r.="<input type='hidden' name='search_xml_file_full_path' value='".$this->full_path."'/>\n";
		return $r;
	}

	/**
	 * Génération du formulaire en drag'n'drop
	 */
	public function show_dnd_form() {
	    global $javascript_path, $extended_search_dnd_tpl, $extended_search_dnd_tab_tpl;
		global $mode, $external_type;
		global $current_module, $rmc_tab;

		$search_controller_class = 'SearchController';
		// On gère le cas particulier de la recherche simple externe
		if (($mode == 7) && (($external_type == 'simple') || ($_SESSION["ext_type"] == 'simple'))) {
			$search_controller_class = 'SimpleExternalSearchController';
		}
		$unique_identifier = md5(microtime());

		if (isset($rmc_tab) && $rmc_tab == 'false') {
    		$form = $extended_search_dnd_tpl;
		} else {
		    $form = $extended_search_dnd_tab_tpl;
		}

		$form = str_replace('!!unique_identifier!!', $unique_identifier, $form);
		$form = str_replace('!!search_controller_class!!', $search_controller_class, $form);
		$form = str_replace('!!search_controller_module!!', $current_module, $form);
		$form = str_replace('!!search_ontology_id!!', "", $form);
		return $form;
	}

	public function serialize_search() {
		global $search;

		$to_serialize=array();
        $to_serialize["SEARCH"] = $search ?? [];
		if(is_array($search) && count($search)){
			for ($i=0; $i<count($search); $i++) {
				$to_serialize[$i]["SEARCH"]=$search[$i];
				$to_serialize[$i]["OP"]=$this->get_global_value("op_".$i."_".$search[$i]);
				$to_serialize[$i]["FIELD"]=$this->get_global_value("field_".$i."_".$search[$i]);
				$to_serialize[$i]["FIELD1"]=$this->get_global_value("field_".$i."_".$search[$i]."_1");
				$to_serialize[$i]["INTER"]=$this->get_global_value("inter_".$i."_".$search[$i]);
				$to_serialize[$i]["FIELDVAR"]=$this->get_global_value("fieldvar_".$i."_".$search[$i]);
			}
		}
		return serialize($to_serialize);
	}

	public function unserialize_search($serialized) {
		global $search;
		
		if (!is_array($search)) {
		    $search = [];
		}
		
		$to_unserialize=unserialize($serialized);
		$search = $to_unserialize["SEARCH"] ?? [];
		if (is_array($search) && count($search)){
    		for ($i=0; $i<count($search); $i++) {
    			$this->set_global_value("op_".$i."_".$search[$i], $to_unserialize[$i]["OP"]);
    			$this->set_global_value("field_".$i."_".$search[$i], $to_unserialize[$i]["FIELD"]);
    			if(isset($to_unserialize[$i]["FIELD1"])) {
    				$this->set_global_value("field_".$i."_".$search[$i]."_1", $to_unserialize[$i]["FIELD1"]);
    			} else {
    				$this->set_global_value("field_".$i."_".$search[$i]."_1");
    			}
    			$this->set_global_value("inter_".$i."_".$search[$i], $to_unserialize[$i]["INTER"]);
    			$this->set_global_value("fieldvar_".$i."_".$search[$i], $to_unserialize[$i]["FIELDVAR"]);
    		}
		}
	}

    /**
     * On met la recherche courrante dans la pile
     *
     * @return void
     */
	public function push() {
		global $search;
		global $pile_search;
		$pile_search[]=$this->serialize_search();
		if(is_array($search) && count($search)){
			for ($i=0; $i<count($search); $i++) {
				$this->set_global_value("op_".$i."_".$search[$i]);
				$this->set_global_value("field_".$i."_".$search[$i]);
				$this->set_global_value("field_".$i."_".$search[$i]."_1");
				$this->set_global_value("inter_".$i."_".$search[$i]);
				$this->set_global_value("fieldvar_".$i."_".$search[$i]);
			}
		}
		$search = array();
	}

    /**
     * On redefinis les globales de la dernière recherche mis dans la pile
     *
     * @return void
     */
	public function pull() {
		global $pile_search;
		$this->unserialize_search($pile_search[count($pile_search)-1]);
		$t=array();
		for ($i=0; $i<count($pile_search)-1; $i++) {
			$t[$i]=$pile_search[$i];
		}
		$pile_search=$t;
	}

	public function destroy_global_env(){
		global $search;
		if(is_countable($search)) {
			for ($i=0; $i<count($search); $i++) {
				$op="op_".$i."_".$search[$i];
				$field_="field_".$i."_".$search[$i];
				$field1_="field_".$i."_".$search[$i]."_1";
				$inter="inter_".$i."_".$search[$i];
				$fieldvar="fieldvar_".$i."_".$search[$i];
				global ${$op};
				global ${$field_};
				global ${$field1_};
				global ${$inter};
				global ${$fieldvar};
				unset($GLOBALS[$op]);
				unset($GLOBALS[$field_]);
				unset($GLOBALS[$field1_]);
				unset($GLOBALS[$inter]);
				unset($GLOBALS[$fieldvar]);
			}
		}
		$search = array();
	}

	public function reduct_search() {
		global $search;
		if(! is_countable($search)) {
			$search = array();
		}
		$tt=array();
		$fieldt=array();
		$it=0;
		for ($i=0; $i<count($search); $i++) {
			$op="op_".$i."_".$search[$i];
			global ${$op};
			$field_="field_".$i."_".$search[$i];
			global ${$field_};
			$field=${$field_};

			$field1_="field_".$i."_".$search[$i]."_1";
			global ${$field1_};
			$field1=${$field1_};

			switch ($search[$i]) {
			    case 's_1':
			        if (((isset($field[0]) && (string)$field[0]!="" && (string)$field[0]!="-1") || (isset($field1[0]) && (string)$field1[0]!="" && (string)$field1[0]!="-1")) || ($this->op_empty[${$op}])) {
			            $tt[$it]=$i;
			            $it++;
			        }
			        break;
			    default:
			        if (((isset($field[0]) && (string)$field[0]!="") || (isset($field1[0]) && (string)$field1[0]!="")) || ($this->op_empty[${$op}])) {
			            $tt[$it]=$i;
			            $it++;
			        }
			        break;
			}
		}

		//Décalage des critères
		//1) copie des critères valides
		for ($i=0; $i<count($tt); $i++) {
			$it=$tt[$i];
			$fieldt[$i]["op"]=$this->get_global_value("op_".$it."_".$search[$it]);
			$fieldt[$i]["field"]=$this->get_global_value("field_".$it."_".$search[$it]);
			$fieldt[$i]["field1"]=$this->get_global_value("field_".$it."_".$search[$it]."_1");
			$fieldt[$i]["fieldvar"]=$this->get_global_value("fieldvar_".$it."_".$search[$it]);
			$fieldt[$i]["inter"]=$this->get_global_value("inter_".$it."_".$search[$it]);
			$fieldt[$i]["search"]=$search[$it];
		}
		//On nettoie et on reconstruit
		$this->destroy_global_env();
		$search=array();
		for ($i=0; $i<count($tt); $i++) {
			$search[$i]=$fieldt[$i]["search"];
			$this->set_global_value("op_".$i."_".$search[$i], $fieldt[$i]["op"]);
			$this->set_global_value("field_".$i."_".$search[$i], $fieldt[$i]["field"]);
			$this->set_global_value("field_".$i."_".$search[$i]."_1", $fieldt[$i]["field1"]);
			$this->set_global_value("inter_".$i."_".$search[$i], $fieldt[$i]["inter"]);
			$this->set_global_value("fieldvar_".$i."_".$search[$i], $fieldt[$i]["fieldvar"]);
		}
	}

	//suppression des champs de recherche marqués FORBIDDEN pour recherche externe
	public function remove_forbidden_fields() {
		global $search;

		if(is_countable($search)){
			$old_search=array();
			$old_search['search']=$search;
			for ($i=0; $i<count($search); $i++) {

				$inter="inter_".$i."_".$search[$i];
				$old_search[$inter]=$this->get_global_value($inter);

				$op="op_".$i."_".$search[$i];
				$old_search[$op]=$this->get_global_value($op);

				$field="field_".$i."_".$search[$i];
				$old_search[$field]=$this->get_global_value($field);

				$fieldvar="fieldvar_".$i."_".$search[$i];
				$old_search[$fieldvar]=$this->get_global_value($fieldvar);

			}
		}
		$saved_search=array();
		if(is_countable($search) && count($search)){
			foreach($search as $k=>$s) {
				if ($s[0]=="f") {
					if ($this->fixedfields[substr($s,2)] && ($this->fixedfields[substr($s,2)]['UNIMARCFIELD']!='FORBIDDEN')) {
						$saved_search[$k]=$s;
					}
				} elseif(array_key_exists($s[0],$this->pp)){
					//Pas de recherche affiliée dans des champs personnalisés.
				} elseif ($s[0]=="s") {
					if ($this->specialfields[substr($s,2)] && ($this->specialfields[substr($s,2)]['UNIMARCFIELD']!='FORBIDDEN')) {
						$saved_search[$k]=$s;
					}
				}elseif (substr($s,0,9)=="authperso") {
					$saved_search[$k]=$s;
				}
			}
		}

		$new_search=array();
		$i=0;
		foreach($saved_search as $k=>$v) {
			$new_search['search'][$i]=$v;

			$old_inter="inter_".$k."_".$v;
			$new_inter="inter_".$i."_".$v;
			$new_search[$new_inter]=$this->get_global_value($old_inter);

			$old_op="op_".$k."_".$v;
			$new_op="op_".$i."_".$v;
			$new_search[$new_op]=$this->get_global_value($old_op);

			$old_field="field_".$k."_".$v;
			$new_field="field_".$i."_".$v;
			$new_search[$new_field]=$this->get_global_value($old_field);

			$old_fieldvar="fieldvar_".$k."_".$v;
			$new_fieldvar="fieldvar_".$i."_".$v;
			$new_search[$new_fieldvar]=$this->get_global_value($old_fieldvar);

			$i++;
		}
		$this->destroy_global_env();
		foreach($new_search as $k=>$va) {
			global ${$k};
			${$k}=$va;
		}
	}

	protected function get_global_value($name) {
		global ${$name};
		return ${$name};
	}

	protected function set_global_value($name, $value='') {
		global ${$name};
		if (is_array($value) || is_object($value)) {
	        $tmp = [];
		    foreach ($value as $key => $val) {
		        if (is_string($val) && strpos($val, "u0022") !== false) {
		            $tmp[$key] = str_replace("u0022", '"', $val);
                } else {
                    $tmp[$key] = $val;
                }
		    }
		    $value = $tmp;
        } else if (!empty($value) && strpos($value ?? "", "u0022") !== false) {
		    $value = str_replace("u0022", '"', $value);
		}
		${$name} = $value;
	}

	protected function clean_completion_empty_values($values) {
		$suppr = false;
		if(is_array($values)) {
			foreach($values as $k=>$v){
				if(!$v){
					unset($values[$k]);
					$suppr=true;
				}
			}
			if($suppr){
				$values = array_values($values);
			}
		}
		return $values;
	}

	protected function clean_empty_values($values) {
		$suppr = false;
		if(is_array($values)) {
			foreach($values as $k=>$v){
				if($v===""){
					unset($values[$k]);
					$suppr=true;
				}
			}
			if($suppr){
				$values = array_values($values);
			}
		}
		return $values;
	}

	protected function sort_groups($a, $b){
		if($a['order'] == $b['order']){
			return 0;
		}
		return ($a['order'] > $b['order'] ? 1 : -1);
	}

	public static function get_authoritie_display($id, $type) {
		global $thesaurus_classement_mode_pmb;
		global $lang;

		$libelle = '';
		switch ($type){
			case "auteur":
				$aut=new auteur($id);
				$libelle = $aut->get_isbd();
				break;
			case "categorie":
				$thes = thesaurus::getByEltId($id);
				$libelle = categories::getlibelle($id,$lang)." [".$thes->libelle_thesaurus."]";
				break;
			case "editeur":
				$ed = new editeur($id);
				$libelle = $ed->get_isbd();
				break;
			case "collection" :
				$coll = new collection($id);
				$libelle = $coll->get_isbd();
				break;
			case "subcollection" :
				$coll = new subcollection($id);
				$libelle = $coll->get_isbd();
				break;
			case "serie" :
				$serie = new serie($id);
				$libelle = $serie->get_isbd();
				break;
			case "indexint" :
				$indexint = new indexint($id);
				$libelle = $indexint->get_isbd();
				break;
			case "titre_uniforme" :
				$tu = new titre_uniforme($id);
				$libelle = $tu->get_isbd();
				break;
			case "notice" :
				$libelle = notice::get_notice_title($id);
				break;
			case "fournisseur" :
				$entites = new entites($id);
				$libelle = $entites->raison_sociale;
				break;
			case "vedette" :
				$vedette_composee = new vedette_composee($id);
				$libelle = $vedette_composee->get_label();
				break;
			case "authperso" :
			    $libelle = authperso::get_isbd($id);
			    break;
			case "ontology" :
				if ($id && !is_numeric($id)) {
					$id = onto_common_uri::get_id($id);
				}
				$query ="select value from skos_fields_global_index where id_item = '".$id."'";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)) {
					$row = pmb_mysql_fetch_object($result);
					$libelle = $row->value;
				} else {
					$libelle = "";
				}
				break;
			default :
			    if (strpos($type, "authperso") !== false) {
			        $libelle = strip_tags(authperso::get_isbd($id));
			    } else {
			        $libelle = $id;
			    }
			    break;
		}
		return $libelle;
	}

	public function get_selector_display($id, $type, $search) {
		global $msg;

		$display = '';
		$p = explode('_', $search);
		switch ($type){
			case 'list':
				$options=$this->fixedfields[$p[1]]["INPUT_OPTIONS"]["OPTIONS"][0];
				foreach ($options["OPTION"] as $option) {
					if($option['VALUE'] == $id) {
						$display .= get_msg_to_display($option["value"]);
						break;
					}
				}
				break;
			case 'query_list':
				if($p[0] == 'f') {
					$requete=$this->fixedfields[$p[1]]["INPUT_OPTIONS"]["QUERY"][0]["value"];
					if (isset($this->fixedfields[$p[1]]["INPUT_OPTIONS"]["FILTERING"])) {
						if ($this->fixedfields[$p[1]]["INPUT_OPTIONS"]["FILTERING"] == "yes") {
							$requete = str_replace("!!acces_j!!", $this->tableau_access_rights["acces_j"], $requete);
							$requete = str_replace("!!statut_j!!", $this->tableau_access_rights["statut_j"], $requete);
							$requete = str_replace("!!statut_r!!", $this->tableau_access_rights["statut_r"], $requete);
						}
					}
					$resultat=pmb_mysql_query($requete);
					while ($r_=pmb_mysql_fetch_row($resultat)) {
						if($r_[0] == $id) {
							$display .= $r_[1];
							break;
						}
					}
				}
				break;
			case 'marc_list':
				$opt = marc_list_collection::get_instance($this->fixedfields[$p[1]]["INPUT_OPTIONS"]["NAME"][0]["value"]);
				$tmp = array();
                if (is_countable($opt->inverse_of) && count($opt->inverse_of)) {
				    // sous tableau genre ascendant descendant...
				    foreach ($opt->table as $table) {
				        $tmp = array_merge($tmp, $table);
				    }
				} else {
				    $tmp = $opt->table;
				}
				$display.= $tmp->table[$id];
				break;
		}
		return $display;
	}

	public static function get_list_display($fixedfield, $field) {
		global $msg;

		$field_aff = array();

		$options=$fixedfield["INPUT_OPTIONS"]["OPTIONS"][0];
		$opt=array();
		for ($j=0; $j<count($options["OPTION"]); $j++) {
			if (substr($options["OPTION"][$j]["value"],0,4)=="msg:") {
				$opt[$options["OPTION"][$j]["VALUE"]]=$msg[substr($options["OPTION"][$j]["value"],4,strlen($options["OPTION"][$j]["value"])-4)];
			} else {
				$opt[$options["OPTION"][$j]["VALUE"]]=$options["OPTION"][$j]["value"];
			}
		}
		for ($j=0; $j<count($field); $j++) {
			if(isset($field[$j]) && ($field[$j]!=="")) {
				$field_aff[]=$opt[$field[$j]];
			}
		}
		return $field_aff;
	}

	public static function get_checkbox_list_display($fixedfield, $field) {
	    return static::get_list_display($fixedfield, $field);
	}

	public static function get_query_list_display($fixedfield, $field) {
		$field_aff = array();

		$requete=$fixedfield["INPUT_OPTIONS"]["QUERY"][0]["value"];
		if(isset($fixedfield["INPUT_OPTIONS"]["FILTERING"])) {
			if ($fixedfield["INPUT_OPTIONS"]["FILTERING"] == "yes") {
				$requete = str_replace("!!acces_j!!", "", $requete);
				$requete = str_replace("!!statut_j!!", "", $requete);
				$requete = str_replace("!!statut_r!!", "", $requete);
			}
		}
		if (isset($fixedfield["INPUT_OPTIONS"]["QUERY"][0]["USE_GLOBAL"]) && $fixedfield["INPUT_OPTIONS"]["QUERY"][0]["USE_GLOBAL"]) {
			$use_global = explode(",", $fixedfield["INPUT_OPTIONS"]["QUERY"][0]["USE_GLOBAL"]);
			for($j=0; $j<count($use_global); $j++) {
				$var_global = $use_global[$j];
				global ${$var_global};
				$requete = str_replace("!!".$var_global."!!", ${$var_global}, $requete);
			}
		}
		if(!empty($fixedfield["INPUT_OPTIONS"]["QUERY"][0]["TRANSLATIONTABLENAME"]) && !empty($fixedfield["INPUT_OPTIONS"]["QUERY"][0]["TRANSLATIONFIELDNAME"])) {
			$trans_table = $fixedfield["INPUT_OPTIONS"]["QUERY"][0]["TRANSLATIONTABLENAME"];
			$trans_field = $fixedfield["INPUT_OPTIONS"]["QUERY"][0]["TRANSLATIONFIELDNAME"];
		} else {
			$trans_table = '';
			$trans_field = '';
		}
		$resultat=pmb_mysql_query($requete);
		$opt=array();
		while ($r_=pmb_mysql_fetch_row($resultat)) {
			if(!empty($trans_table) && !empty($trans_field)) {
				$opt[$r_[0]] = translation::get_translated_text($r_[0], $trans_table, $trans_field, $r_[1]);
			} else {
				$opt[$r_[0]]=$r_[1];
			}
		}
		if (!empty($field) && is_countable($field)) {
    		for ($j=0; $j<count($field); $j++) {
    			if(isset($field[$j]) && ($field[$j]!=="")) {
    				$field_aff[] = (isset($opt[$field[$j]]) ? $opt[$field[$j]] : '');
    			}
    		}
		}
		return $field_aff;
	}

	public static function get_marc_list_display($fixedfield, $field) {
		$field_aff = array();

		$opt = marc_list_collection::get_instance($fixedfield["INPUT_OPTIONS"]["NAME"][0]["value"]);

		$tmp = array();
		if (count($opt->inverse_of)) {
		    // sous tableau genre ascendant descendant...
		    foreach ($opt->table as $table) {
				foreach($table as $code => $label) {
					$tmp[$code] = $label;
				}
		    }
		} else {
		    $tmp = $opt->table;
		}
		if (is_array($field)) {
		    $nb_fields = count($field);
		    for ($j = 0; $j < $nb_fields; $j++) {
    			if (isset($field[$j]) && ($field[$j]!=="")) {
    			    $field_aff[] = (isset($tmp[$field[$j]]) ? $tmp[$field[$j]] : '');
    			}
    		}
		}
		return $field_aff;
	}

	protected function get_current_search_map(){
		global $pmb_map_activate;
		$map = "";
		if($pmb_map_activate){
			$map = "<div id='map_container'><div id='map_search' ></div></div>";
		}
		return $map;
	}

	public function get_unimarc_fields() {
		$r=array();
		foreach($this->fixedfields as $values) {
			if ($values["UNIMARCFIELD"]) {
				$r[$values["UNIMARCFIELD"]]["TITLE"][]=$values["TITLE"];
				foreach($values["QUERIES_INDEX"] as $op=>$top) {
					$r[$values["UNIMARCFIELD"]]["OPERATORS"][$op]=$this->operators[$op];
				}
			}
		}
		return $r;
	}

	protected function gen_temporary_table($table_name, $main='', $with_pert=false) {
		if(!$main) {
			$this->is_created_temporary_table = false;
			return;
		}
		$query = "create temporary table if not exists ".$table_name." ENGINE=".$this->current_engine." ".$main;
		$result = pmb_mysql_query($query);
		if($result) {
			if (!pmb_mysql_num_rows(pmb_mysql_query("show columns from ".$table_name." like 'idiot'"))) {
				$query = "alter table ".$table_name." add idiot int(1)";
				pmb_mysql_query($query);
			}
			$query = "alter table ".$table_name." add unique(".$this->keyName.")";
			pmb_mysql_query($query);
			if($with_pert) {
				if (!pmb_mysql_num_rows(pmb_mysql_query("show columns from ".$table_name." like 'pert'"))) {
					$query="alter table ".$table_name." add pert decimal(16,1) default 1";
					pmb_mysql_query($query);
				}
			}
			$this->is_created_temporary_table = true;
		}
	}

	public function is_created_temporary_table($table_name) {
		return $this->is_created_temporary_table;
	}

	protected function upgrade_columns_temporary_table($table_name, $field_keyName, $with_pert=false) {
		if (!pmb_mysql_num_rows(pmb_mysql_query("show columns from ".$table_name." like 'idiot'"))) {
			$query="alter table ".$table_name." add idiot int(1)";
			pmb_mysql_query($query);
		}
		$query="alter table ".$table_name." add unique($field_keyName)";
		pmb_mysql_query($query);
		if($with_pert) {
			if (!pmb_mysql_num_rows(pmb_mysql_query("show columns from ".$table_name." like 'pert'"))) {
				$query="alter table ".$table_name." add pert decimal(16,1) default 1";
				pmb_mysql_query($query);
			}
		}
	}

	protected function is_empty($field, $field_name) {
		if (empty($field) && !is_countable($field)) {
			return true;
		}
		if ((!count($field))||((count($field)==1)&&((string)$field[0]==""))) {
			return true;
		}
		if(count($field) > 1) {
			if((string)$field[0]=="") {
				$field = array_filter($field, function($var){
					return (!($var == '' || is_null($var)));
				});
					$field = array_values($field);
					global ${$field_name};
					${$field_name} = $field;
					if(!count($field)) {
						return true;
					}
			}
		}
		return false;
	}

	protected static function init_session_facets($table) {
	    $objects = "";
	    $result = pmb_mysql_query('SELECT * FROM '.$table);
	    while($row = pmb_mysql_fetch_object($result)){
	        if($objects){
	            $objects .= ",";
	        }
	        $objects .= $row->notice_id;
	    }
	    session::set_value('search', ['notices' => ['extended_search' => $objects]]);
	    facettes::set_facet_type('notices');
	}

	public function show_results($url,$url_to_search_form,$hidden_form=true,$search_target="", $acces=false) {
		global $begin_result_liste;
		global $nb_per_page_search;
		global $page;
		global $charset;
		global $search;
		global $msg;
		global $pmb_nb_max_tri;
		global $pmb_allow_external_search;
		global $debug;

		//Y-a-t-il des champs ?
		if (!is_countable($search) || count($search)==0) {
			array_pop($_SESSION["session_history"]);
			error_message_history($msg["search_empty_field"], $msg["search_no_fields"], 1);
			exit();
		}
    	$recherche_externe=true;//Savoir si l'on peut faire une recherche externe à partir des critères choisis
		//Verification des champs vides
		for ($i=0; $i<count($search); $i++) {
			$op=$this->get_global_value("op_".$i."_".$search[$i]);

			$field=$this->get_global_value("field_".$i."_".$search[$i]);

			$field1=$this->get_global_value("field_".$i."_".$search[$i]."_1");

			$s=explode("_",$search[$i]);
			$bool=false;
			if ($s[0]=="f") {
				$champ=$this->fixedfields[$s[1]]["TITLE"];

				if ($this->op_special[$op]) {
				    if ($this->is_empty_op_special($this->fixedfields[$s[1]], $op, $i, $search[$i])) {
    					$bool = true;
				    }
				} else if ($this->is_empty($field, "field_".$i."_".$search[$i]) && $this->is_empty($field1, "field_".$i."_".$search[$i]."_1")) {
					$bool = true;
				}
			} elseif(array_key_exists($s[0],$this->pp)) {
				$champ=$this->pp[$s[0]]->t_fields[$s[1]]["TITRE"];
				if ($this->is_empty($field, "field_".$i."_".$search[$i]) && $this->is_empty($field1, "field_".$i."_".$search[$i]."_1")) {
					$bool=true;
				}
			} elseif($s[0]=="s") {
				$recherche_externe=false;
				$champ=$this->specialfields[$s[1]]["TITLE"];
				$type=$this->specialfields[$s[1]]["TYPE"];
				for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
					if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
						$sf=$this->specialfields[$s[1]];
						global $include_path;
						require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
						$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$i,$sf,$this);
						$bool=$specialclass->is_empty($field);
						break;
					}
				}
			} elseif (substr($s[0], 0, 9) == "authperso") {

			}
			if (($bool)&&(!$this->op_empty[$op])) {
				$query_data = array_pop($_SESSION["session_history"]);
				error_message_history($msg["search_empty_field"], sprintf($msg["search_empty_error_message"],$champ), 1);
				print $this->get_back_button($query_data);
				exit();
			}
		}
		$table=$this->make_search();

		if ($acces==true) {
	    	$this->filter_searchtable_from_accessrights($table);
		}
		if(!empty($this->context_parameters['in_selector'])) {
			$this->filter_searchtable_without_no_display($table);
		}

		$requete="select count(1) from $table";
		if($res=pmb_mysql_query($requete)){
			$nb_results=pmb_mysql_result($res,0,0);
		}else{
			$query_data = array_pop($_SESSION["session_history"]);
    		error_message_history("",$msg["search_impossible"], 1);
    		print $this->get_back_button($query_data);
    		exit();
		}

    	//gestion du tri
    	$has_sort = false;
 		if ($nb_results <= $pmb_nb_max_tri) {
			if ($_SESSION["tri"]) {
				$table = $this->sort_results($table);
				$has_sort = true;
			}
		}
		// fin gestion tri
    	//Y-a-t-il une erreur lors de la recherche ?
    	if ($this->error_message) {
    		$query_data = array_pop($_SESSION["session_history"]);
    		error_message_history("", $this->error_message, 1);
    		print $this->get_back_button($query_data);
    		exit();
    	}

    	if ($hidden_form) {
    		print $this->make_hidden_search_form($url,$this->get_hidden_form_name(),"",false);
    		print facette_search_compare::form_write_facette_compare();
    		print "</form>";
    	}

    	static::init_session_facets($table);

		$human_requete = $this->make_human_query();
		print "<h3 class='section-sub-title'>";
    	print "<strong>".$msg["search_search_extended"]."</strong> : ".$human_requete ;
    	if ($debug) {
    	    print "<br />".$this->serialize_search();
    	}
		if ($nb_results) {
			print $this->get_display_nb_results($nb_results);
			print "</h3>";
			print $begin_result_liste;
			print $this->get_display_icons($nb_results, $recherche_externe);
		} else {
		    print "<br />".$msg["1915"]."</h3> ";
		}
		print "<div class='row'>";
		if(empty($this->context_parameters['in_selector'])) {
			print "<input type='button' class='bouton' onClick=\"document.".$this->get_hidden_form_name().".action='".$url_to_search_form."'; document.".$this->get_hidden_form_name().".target='".$search_target."'; document.".$this->get_hidden_form_name().".submit(); return false;\" value=\"".$msg["search_back"]."\"/>";
			print $this->get_display_actions();
		}
		if ($nb_results) {
		    print searcher::get_check_uncheck_all_buttons();
		}
		print "</div>";

		facettes::set_hidden_form_name($this->get_hidden_form_name());
		facettes::set_elements_list_nb_per_page($nb_per_page_search);
		print "
        <div class='row'>"
		    .$this->get_current_search_map()."
        </div>
        <div class='content_details'>
            <div id='facettes_list' class='facettes_list'>".facettes::call_ajax_facettes()."</div>
                <div id='results_list' class='results_list'>";
		$this->show_objects_results($table, $has_sort);
		print "</div>
        </div>";

    	$this->get_navbar($nb_results, $hidden_form);
    }

	public function show_results_unimarc($url,$url_to_search_form,$hidden_form=true,$search_target="") {
    	global $begin_result_liste;
    	global $nb_per_page_search;
    	global $page;
    	global $charset;
    	global $search;
    	global $msg;
    	global $pmb_nb_max_tri;
    	global $pmb_allow_external_search;
    	global $opac_view_id;
    	global $filtre_compare, $reinit_compare, $mode, $notice_id;
    	
    	$page = intval($page);
    	if($page) {
    	    $start_page = $nb_per_page_search * ($page-1);
    	} else {
    	    $start_page = 0;
    	}

    	//Y-a-t-il des champs ?
    	if (!is_array($search) || count($search)==0) {
    		error_message_history($msg["search_empty_field"], $msg["search_no_fields"], 1);
    		exit();
    	}
    	//Verification des champs vides
    	for ($i=0; $i<count($search); $i++) {
    		$op="op_".$i."_".$search[$i];
    		global ${$op};

     		$field_="field_".$i."_".$search[$i];
    		global ${$field_};
    		$field=${$field_};

    		$field1_="field_".$i."_".$search[$i]."_1";
    		global ${$field1_};
    		$field1=${$field1_};

    		$s=explode("_",$search[$i]);
    		$bool=false;
    		if ($s[0]=="f") {
    			$champ=$this->fixedfields[$s[1]]["TITLE"];
    			if ($this->is_empty($field, "field_".$i."_".$search[$i]) && $this->is_empty($field1, "field_".$i."_".$search[$i]."_1")) {
    				$bool=true;
    			}
    		} elseif(array_key_exists($s[0],$this->pp)) {
    			$champ=$this->pp[$s[0]]->t_fields[$s[1]]["TITRE"];
    			if ($this->is_empty($field, "field_".$i."_".$search[$i]) && $this->is_empty($field1, "field_".$i."_".$search[$i]."_1")) {
    				$bool=true;
    			}
    		} elseif($s[0]=="s") {
    			$champ=$this->specialfields[$s[1]]["TITLE"];
    			$type=$this->specialfields[$s[1]]["TYPE"];
		 		for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
					if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
						$sf=$this->specialfields[$s[1]];
						global $include_path;
						require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
						$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$i,$sf,$this);
						$bool=$specialclass->is_empty($field);
						break;
					}
				}
    		}
    		if (($bool)&&(!$this->op_empty[${$op}])) {
    			error_message_history($msg["search_empty_field"], sprintf($msg["search_empty_error_message"],$champ), 1);
    			exit();
    		}
    	}
    	global $inter_1_f_1;
    	$table=$this->make_search();
    	$requete="select count(1) from $table";
    	if($res=pmb_mysql_query($requete)){
			$nb_results=pmb_mysql_result($res,0,0);
		}else{
    		error_message_history("",$msg["search_impossible"], 1);
    		exit();
		}

    	//gestion du tri
		$requete = "";
    	if ($nb_results <= $pmb_nb_max_tri) {
    	    if (!empty($_SESSION["tri"])) {
    	        $sort = new sort_external('external','base');
				if (!empty($nb_per_page_search)) {
				    $requete = $sort->appliquer_tri($_SESSION["tri"], "select * from $table", "notice_id", $start_page, $nb_per_page_search);
				} else {
				    $requete = $sort->appliquer_tri($_SESSION["tri"], "select * from $table", "notice_id");
				}
			}
    	}
		// fin gestion tri

    	if (empty($requete)) {
//    	    $requete = "select * from $table limit ".$start_page.",".$nb_per_page_search;
    	    $sort = new sort_external('external','base');
    	    if (!empty($nb_per_page_search)) {
    	        $requete = $sort->appliquer_tri("default", "select * from $table", "notice_id", $start_page, $nb_per_page_search);
    	    } else {
    	        $requete = $sort->appliquer_tri("default", "select * from $table", "notice_id");
    	    }
    	}

    	//Y-a-t-il une erreur lors de la recherche ?
    	if ($this->error_message) {
    		error_message_history("", $this->error_message, 1);
    		exit();
    	}

    	if ($hidden_form) {
    		print $this->make_hidden_search_form($url,$this->get_hidden_form_name(),"",false);
    		print facettes_external_search_compare::form_write_facette_compare();
    		print "</form>";
    	}

    	//$requete="select $table.* from $table left join entrepots on recid=notice_id and (ufield='200' and usubfield='a') or (recid is null) order by i_value";
    	//$requete .= " limit ".$start_page.",".$nb_per_page_search;
    	//$resultat=pmb_mysql_query($requete);

    	$objects = "";
		$resultat = pmb_mysql_query($requete);
		while($row = pmb_mysql_fetch_object($resultat)){
			if($objects){
				$objects .= ",";
			}
			$objects .= $row->notice_id;
		}
		$_SESSION['tab_result_external'] = $objects;

		$resultat = pmb_mysql_query($requete);
    	$human_requete = $this->make_human_query();
    	print "<strong>".$msg["search_search_extended"]."</strong> : ".$human_requete ;

		if ($nb_results) {
			print $this->get_display_nb_results($nb_results);
			print $begin_result_liste;
			print $this->get_display_icons($nb_results, true);
			if ($this->rec_history && $mode != 7) {
				//Affichage des liens paniers et impression
			    //On n'affichage pas les liens paniers et impression en recherche externe ($mode != 7)
				$current=$_SESSION["CURRENT"];
				if ($current!==false) {
					$tri_id_info = $_SESSION["tri"] ? "&sort_id=".$_SESSION["tri"] : "";
					print "&nbsp;<a href='#' onClick=\"openPopUp('./print_cart.php?current_print=$current&action=print_prepare$tri_id_info','print',600,700,-2,-2,'scrollbars=yes,menubar=0,resizable=yes'); return false;\"><img src='".get_url_icon('basket_small_20x20.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_add_to_cart"]."\" title=\"".$msg["histo_add_to_cart"]."\"></a>&nbsp;<a href='#' onClick=\"openPopUp('./print.php?current_print=$current&action_print=print_prepare$tri_id_info','print',500,600,-2,-2,'scrollbars=yes,menubar=0'); return false;\"><img src='".get_url_icon('print.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_print"]."\" title=\"".$msg["histo_print"]."\"/></a>";
				}
			}
		} else print "<br />".$msg["1915"]." ";
		print "<input type='button' class='bouton' onClick=\"document.search_form.action='$url_to_search_form'; document.search_form.target='$search_target'; document.search_form.submit(); return false;\" value=\"".$msg["search_back"]."\"/>";

		print "<input type='button' class='bouton' onClick=\"integer_notices_submit();\" value=\"".$msg["external_search_integer_results"]."\"/>";

		global $dsi_active;
		if (($dsi_active)&&false) {
			global $id_equation, $priv_pro, $id_empr;
			if ($id_equation) {
				$mess_bouton = $msg['dsi_sauvegarder_equation'];
			} else {
				$mess_bouton = $msg["dsi_transformer_equation"];
				if($dsi_active == 2) {
					$mess_bouton = $msg["dsi_transformer_item"];
				}
			}
			print "&nbsp;<input  type='button' class='bouton' onClick=\"document.forms['transform_dsi'].submit(); \" value=\"".$mess_bouton."\"/>
						<form name='transform_dsi' style='display:none;' method='post' action='./dsi.php'>";
			if ($priv_pro=="PRI") print "
						<input type=hidden name='categ' value='bannettes' />
						<input type=hidden name='sub' value='abo' />
						<input type=hidden name='suite' value='transform_equ' />
						<input type=hidden name='id_equation' value='$id_equation' />
						<input type=hidden name='id_empr' value='$id_empr' />
						<input type=hidden name='requete' value='".htmlentities($this->serialize_search(),ENT_QUOTES,$charset)."' />
						</form>";
				else print "
						<input type=hidden name='categ' value='equations' />
						<input type=hidden name='sub' value='gestion' />
						<input type=hidden name='suite' value='transform' />
						<input type=hidden name='id_equation' value='$id_equation' />
						<input type=hidden name='requete' value='".htmlentities($this->serialize_search(),ENT_QUOTES,$charset)."' />
						</form>";
			}
   		global $pmb_opac_view_activate;
    	if ($pmb_opac_view_activate) {
    		if($opac_view_id){
    			$mess_bouton = $msg['opac_view_sauvegarder_equation'];
				print "
					&nbsp;<input  type='button' class='bouton' onClick=\"document.forms['transform_opac_view'].submit(); \" value=\"".$mess_bouton."\"/>
					<form name='transform_opac_view' style='display:none;' method='post' action='./dsi.php'>
							<input type=hidden name='categ' value='opac' />
							<input type=hidden name='sub' value='opac_view' />
							<input type=hidden name='section' value='list' />
							<input type=hidden name='action' value='form' />
							<input type=hidden name='suite' value='transform_equ' />
							<input type=hidden name='opac_view_id' value='$opac_view_id' />
							<input type=hidden name='requete' value='".htmlentities($this->serialize_search(),ENT_QUOTES,$charset)."' />
					</form>";
    		}
    	}

		flush();
		$entrepots_localisations = array();
		$entrepots_localisations_sql = "SELECT * FROM entrepots_localisations ORDER BY loc_visible DESC";
		$res = pmb_mysql_query($entrepots_localisations_sql);
		while ($row = pmb_mysql_fetch_array($res)) {
			$entrepots_localisations[$row["loc_code"]] = array("libelle" => $row["loc_libelle"], "visible" => $row["loc_visible"]);
		}

		print "
		<div class='row'>
		<input type='button' id='select_all' class='bouton' onClick='external_notices_select_all()' value='".$msg['tout_cocher_checkbox']."'/>
		<script type='text/javascript'>
			function external_notices_select_all(){
				switch(document.getElementById('select_all').todo){
		    		case 'unselect':
		    			if (document.forms.integer_notices['external_notice_to_integer[]'].length) {
			    			for (var i=0; i < document.forms.integer_notices['external_notice_to_integer[]'].length; i++){
								if(!document.forms.integer_notices['external_notice_to_integer[]'][i].disabled){
			    					document.forms.integer_notices['external_notice_to_integer[]'][i].checked=false;
			    				}
			    			}
		    			} else {
							if(!document.forms.integer_notices['external_notice_to_integer[]'].disabled){
		    					document.forms.integer_notices['external_notice_to_integer[]'].checked=false;
		    				}
    					}
		    			document.getElementById('select_all').value='".$msg['tout_cocher_checkbox']."';
		    			document.getElementById('select_all').todo = 'select';
		    			break;
		    		case 'select' :
		    		default :
		    			if (document.forms.integer_notices['external_notice_to_integer[]'].length) {
			    			for (var i=0; i < document.forms.integer_notices['external_notice_to_integer[]'].length; i++){
								if(!document.forms.integer_notices['external_notice_to_integer[]'][i].disabled){
			    					document.forms.integer_notices['external_notice_to_integer[]'][i].checked=true;
			    				}
			    			}
		    			} else {
							if(!document.forms.integer_notices['external_notice_to_integer[]'].disabled){
		    					document.forms.integer_notices['external_notice_to_integer[]'].checked=true;
		    				}
    					}
		    			document.getElementById('select_all').value='".$msg['tout_decocher_checkbox']."';
		    			document.getElementById('select_all').todo = 'unselect';
		    			break;

		    	}

    		}
    		function integer_notices_submit(){
				var ok = false;
				if (document.forms.integer_notices['external_notice_to_integer[]'].length) {
	    			for (var i = 0; i < document.forms.integer_notices['external_notice_to_integer[]'].length; i++){
						if(document.forms.integer_notices['external_notice_to_integer[]'][i].checked && !document.forms.integer_notices['external_notice_to_integer[]'][i].disabled){
							ok = true;
							break;
	    				}
	    			}
	   			} else {
	   				if(!document.forms.integer_notices['external_notice_to_integer[]'].disabled){
	    				document.forms.integer_notices['external_notice_to_integer[]'].checked=true;
						ok = true;
		    		}
	   			}
    			if(!ok){
    				alert('".$msg['integer_notices_error']."');
    			}else{
    				document.forms.integer_notices.submit();
    			}
    		}
		</script>
		</div>
    	<div class='content_details'>
	    	<div id='facettes_list' class='facettes_list'>".facettes_external::call_ajax_facettes()."</div>
	    	<div id='results_list' class='results_list'>
				<form name='integer_notices' action='./catalog.php?categ=search&mode=7&sub=integre_notices' method='post'>
					<input type='hidden' name='serialized_search' value='".htmlentities($this->serialize_search(), ENT_QUOTES, $charset)."' />
					<input type='hidden' name='page' value='".htmlentities($page, ENT_QUOTES, $charset)."'/>
					<input type='hidden' name='notice_to_replace' value='".htmlentities($notice_id ?? '', ENT_QUOTES, $charset) ."'/>	";
		//on suis le flag filtre/compare
		facettes_external::session_filtre_compare();
		if($filtre_compare=='compare'){
			//on valide la variable session qui comprend les critères de comparaisons
			facettes_external_search_compare::session_facette_compare();
			//affichage comparateur
			$facette_compare= new facettes_external_search_compare();
			$compare=$facette_compare->compare_from_objects($objects);
			if($compare===true){
				print  $facette_compare->display_compare();
			}else{
				print  $msg[$compare];
			}
		} else {
			//si demande de réinitialisation
			if($reinit_compare==1){
				facettes_external_search_compare::session_facette_compare(null,$reinit_compare);
			}
	    	while ($r=pmb_mysql_fetch_object($resultat)) {
				$nt = new mono_display_unimarc($r->notice_id,6, 1, 0, 1, false, $entrepots_localisations);
	    		echo "<div class='row'>".$nt->result."</div>";
	    	}
		}
    	print "	</form>
    		</div>
    	</div>";
    	//Gestion de la pagination
    	if ($nb_results && ($filtre_compare!='compare')) {
    		if (!$page) {
    		    $current_page = 1;
    		} else {
    		    $current_page = $page;
    		}
    		$navbar = new Navbar($current_page, $nb_results, $nb_per_page_search);
    		if (!$hidden_form) {
    		    $navbar->setHiddenFormName('search_form', true);
    		} else {
    		    $navbar->setHiddenFormName('search_form');
    		}
    		print "<div id='results_pager' class='center'>".$navbar->render()."</div>";
    	}
    }

	public function show_results_fichier($url,$url_to_search_form,$hidden_form=true,$search_target="", $acces=false) {
    	global $begin_result_liste;
    	global $nb_per_page_search;
    	global $page,$dest;
    	global $charset;
    	global $search;
    	global $msg;
    	global $pmb_nb_max_tri;
    	global $pmb_allow_external_search;
    	global $debug;
		global $link_bulletin;


		$page = intval($page);
		if($page) {
		    $start_page = $nb_per_page_search * ($page-1);
		} else {
		    $start_page = 0;
		}

    	//Y-a-t-il des champs ?
    	if (count($search)==0) {
    		error_message_history($msg["search_empty_field"], $msg["search_no_fields"], 1);
    		exit();
    	}

    	//Verification des champs vides
    	for ($i=0; $i<count($search); $i++) {
    		$op="op_".$i."_".$search[$i];
    		global ${$op};

     		$field_="field_".$i."_".$search[$i];
    		global ${$field_};
    		$field=${$field_};

    		$field1_="field_".$i."_".$search[$i]."_1";
    		global ${$field1_};
    		$field1=${$field1_};

    		$s=explode("_",$search[$i]);
    		$bool=false;
    		if ($s[0]=="f") {
    			$champ=$this->fixedfields[$s[1]]["TITLE"];
    			if ($this->is_empty($field, "field_".$i."_".$search[$i]) && $this->is_empty($field1, "field_".$i."_".$search[$i]."_1")) {
    				$bool=true;
    			}
    		} elseif(array_key_exists($s[0],$this->pp)) {
    			$champ=$this->pp[$s[0]]->t_fields[$s[1]]["TITRE"];
    			if ($this->is_empty($field, "field_".$i."_".$search[$i]) && $this->is_empty($field1, "field_".$i."_".$search[$i]."_1")) {
    				$bool=true;
    			}
    		} elseif($s[0]=="s") {
    			$champ=$this->specialfields[$s[1]]["TITLE"];
    			$type=$this->specialfields[$s[1]]["TYPE"];
		 		for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
					if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
						$sf=$this->specialfields[$s[1]];
						global $include_path;
						require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
						$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$i,$sf,$this);
						$bool=$specialclass->is_empty($field);
						break;
					}
				}
    		}
    		if (($bool)&&(!$this->op_empty[${$op}])) {
    			error_message_history($msg["search_empty_field"], sprintf($msg["search_empty_error_message"],$champ), 1);
    			exit();
    		}
    	}

    	$table=$this->make_search();

		if ($acces==true) {
	    	$this->filter_searchtable_from_accessrights($table);
		}
		if(!empty($this->context_parameters['in_selector'])) {
			$this->filter_searchtable_without_no_display($table);
		}

		$requete="select count(1) from $table";
		$nb_results=pmb_mysql_result(pmb_mysql_query($requete),0,0);

    	//Y-a-t-il une erreur lors de la recherche ?
    	if ($this->error_message) {
    		error_message_history("", $this->error_message, 1);
    		exit();
    	}

    	if (!$dest && $hidden_form){
    		print $this->make_hidden_search_form($url,$this->get_hidden_form_name(),"",false);
    		print "<input type='hidden' name='dest' value='' />\n";
    		print "</form>\n";
    	}

    	if($dest != "TABLEAU"){
	    	$human_requete = $this->make_human_query();
	    	print "<strong>".$msg["search_search_extended"]."</strong> : ".$human_requete ;
			if ($debug) print "<br />".$this->serialize_search();
			if ($nb_results) {
				print " => ".$nb_results." ".$msg["fiche_found"]."<br />\n";
			} else print "<br />".$msg["1915"]." ";
    	}

		$requete="select $table.* from ".$table.",fiche where fiche.id_fiche=$table.id_fiche";
		if(!$dest){
			$requete .= " limit ".$start_page.",".$nb_per_page_search;
		}

    	$resultat=pmb_mysql_query($requete);

    	if(pmb_mysql_num_rows($resultat)){
	    	$result_fic=array();
			$fic = new fiche();
	    	while ($r=pmb_mysql_fetch_object($resultat)) {
	    		$result_fic[$r->id_fiche] = $fic->get_values($r->id_fiche,1);
	    	}
	    	if($result_fic){
	    		if($dest == "TABLEAUHTML"){
					print "<div class='row'>".$fic->display_results_tableau($result_fic,"",0,true)."</div>";
				}elseif($dest == "TABLEAU"){
					$fic->print_results_tableau($result_fic);
				}else{
	    			print "<div class='row'>".$fic->display_results_tableau($result_fic)."</div>";
	    		}
	    	}
    	}
    	if($this->limited_search)
    		$limit_script = "&limited_search=1";
    	else $limit_script="";

		if(!$dest){
			print "<div class='row'><input type='button' class='bouton' onClick=\"document.search_form.dest.value=''; document.search_form.action='$url_to_search_form$limit_script'; document.search_form.target='$search_target'; document.search_form.submit(); return false;\" value=\"".$msg["search_back"]."\"/>";
			print "&nbsp;&nbsp;&nbsp;&nbsp;
			<input type='image' src='".get_url_icon('tableur.gif')."' style='border:0px;' onClick=\"document.search_form.dest.value='TABLEAU'; document.search_form.submit(); \" alt='".htmlentities($msg["fiche_export_excel"],ENT_QUOTES,$charset)."' title='".htmlentities($msg["fiche_export_excel"],ENT_QUOTES,$charset)."' />
			&nbsp;&nbsp;&nbsp;&nbsp;
			<input type='image' src='".get_url_icon('tableur_html.gif')."' style='border:0px;' onClick=\"document.search_form.dest.value='TABLEAUHTML'; document.search_form.submit(); \" alt='".htmlentities($msg["fiche_export_tableau"],ENT_QUOTES,$charset)."' title='".htmlentities($msg["fiche_export_tableau"],ENT_QUOTES,$charset)."' /></div>";
		}
    	//Gestion de la pagination
    	if ($nb_results && !$dest) {
    	    if (!$page) {
    	        $current_page = 1;
    	    } else {
    	        $current_page = $page;
    	    }
    	    $navbar = new Navbar($current_page, $nb_results, $nb_per_page_search);
    	    if (!$hidden_form) {
    	        $navbar->setHiddenFormName('search_form', true);
    	    } else {
    	        $navbar->setHiddenFormName('search_form');
    	    }
    	    print "<div id='results_pager' class='center'>".$navbar->render()."</div>";
    	}
    }

	public function get_ajax_params(){
		global $field_form;
		global $charset,$include_path;

		$elem = explode('_',$field_form);
		if($elem[count($elem)-2] == "s"){
			$field_id = $elem[count($elem)-1];
			//appel de la fonction get_input_box de la classe du champ special
    		$type=$this->specialfields[$field_id]["TYPE"];
    		for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
				if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
					$sf=$this->specialfields[$field_id];
					if ($this->full_path && file_exists($this->full_path."/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php"))
						require_once($this->full_path."/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
					else
						require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
					$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$n,$sf,$this);
					$specialclass->get_ajax_params();
					break;
				}
    		}
		}
	}

	public function show_search_history($idcaddie=0, $object_type="NOTI", $lien_origine="./catalog.php?", $action_click = "add_item") {
    	global $msg;
    	global $charset;
    	$r = "<form name='print_options' action='$lien_origine&action=$action_click&object_type=".$object_type."&idcaddie=$idcaddie' method='post'>
    	<input type='hidden' id='item' name='item' >
    	<input type='hidden' id='search[0]' name='search[0]' value='s_1'>
    	<input type='hidden' id='field_0_s_1[0]' name='field_0_s_1[0]'>
    	<hr />
    	<div class='row'>".$msg["caddie_select_pointe_search_history"]."</div>";

    	//parcours de l'historique des recherches
    	$bool = false;
    	if (count($_SESSION["session_history"])) {
			$r .="<div id='Child' style='margin-bottom:6px;width:94%'>
				<table class='table-no-border'>!!contenu!!</table>
			</div>
			";

    		$style_odd="class='odd' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='odd'\" ";
    		$style_even="class='even' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='even'\" ";

    		$liste = "";
    		$pair = 0;
    	    for ($i=count($_SESSION["session_history"])-1; $i>=0; $i--) {
    			if ($_SESSION["session_history"][$i][$object_type]) {
    				$bool = true;
    				$temp=html_entity_decode(strip_tags(($i+1).") ".$_SESSION["session_history"][$i]["QUERY"]["HUMAN_QUERY"]),ENT_QUOTES,$charset);
    				if(($pair==1)) {
    					$style=$style_odd;
    				} else {
    					$style=$style_even;
    				}
    				$pair = 1-$pair;
    				$liste.="<tr $style><td><a href='#' onclick='javascript:document.getElementById(\"item\").value=".$i.";document.getElementById(\"field_0_s_1[0]\").value=".$i.";document.forms[\"print_options\"].submit();' />$temp</a>
    				<input type='hidden' id='human_query_history_".$i."' name='human_query_history_".$i."' value='".serialize($_SESSION["session_history"][$i]["QUERY"]["HUMAN_QUERY"])."' ></td></tr>";
    			}
    		}
    		$r=str_replace("!!contenu!!",$liste, $r);
    	}
    	if (!$bool) {
    		$r .= "<b>".$msg["histo_empty"]."</b>";
   		}
    	return $r;
    }

    public function get_results($url,$url_to_search_form,$hidden_form=true,$search_target="") {
    	global $search;
    	global $msg;
    	global $pmb_allow_external_search;

    	//Y-a-t-il des champs ?
    	if (empty($search) || !is_countable($search) || count($search)==0) {
    		array_pop($_SESSION["session_history"]);
    		error_message_history($msg["search_empty_field"], $msg["search_no_fields"], 1);
    		exit();
    	}

    	//Verification des champs vides
    	for ($i=0; $i<count($search); $i++) {
    		$op="op_".$i."_".$search[$i];
    		global ${$op};

    		$field_="field_".$i."_".$search[$i];
    		global ${$field_};
    		$field=${$field_};

    		$field1_="field_".$i."_".$search[$i]."_1";
    		global ${$field1_};
    		$field1=${$field1_};

    		$s=explode("_",$search[$i]);
    		$bool=false;
    		if ($s[0]=="f") {
    			$champ=$this->fixedfields[$s[1]]["TITLE"];
    			if ((string)$field[0]=="" && (string)$field1[0]=="") {
    				$bool=true;
    			}
    		} elseif(array_key_exists($s[0],$this->pp)) {
    			$champ=$this->pp[$s[0]]->t_fields[$s[1]]["TITRE"];
    			if ((string)$field[0]=="" && (string)$field1[0]=="") {
    				$bool=true;
    			}
    		} elseif($s[0]=="s") {
    			$champ=$this->specialfields[$s[1]]["TITLE"];
    			$type=$this->specialfields[$s[1]]["TYPE"];
    			for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
    				if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
    					$sf=$this->specialfields[$s[1]];
    					global $include_path;
    					require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
    					$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$i,$sf,$this);
    					$bool=$specialclass->is_empty($field);
    					break;
    				}
    			}
    		}elseif (substr($s,0,9)=="authperso") {

    		}
    		if (($bool)&&(!$this->op_empty[${$op}])) {
    			$query_data = array_pop($_SESSION["session_history"]);
    			error_message_history($msg["search_empty_field"], sprintf($msg["search_empty_error_message"],$champ), 1);
    			print $this->get_back_button($query_data);
    			exit();
    		}
    	}
    	$table=$this->make_search();
    	return $table;
    }

    protected function sort_results($table) {
    	global $nb_per_page_search;
    	global $page;

    	$page = intval($page);
    	if($page) {
    	    $start_page = $nb_per_page_search * ($page-1);
    	} else {
    	    $start_page = 0;
    	}
    	$sort = new sort('notices','base');
    	$sort->appliquer_tri($_SESSION["tri"],"SELECT * FROM " . $table, "notice_id", $start_page, $nb_per_page_search);
    	return $sort->table_tri_tempo;
    }

    public function check_emprises(){
    	global $pmb_map_activate;
    	global $pmb_map_max_holds;
    	global $pmb_map_size_search_result;
    	$current_search = $_SESSION['CURRENT'];
    	$map = "";
    	$size=explode("*",$pmb_map_size_search_result);
    	if(count($size)!=2) {
    		$map_size="width:800px; height:480px;";
    	} else {
    		if (is_numeric($size[0])) $size[0].= 'px';
    		if (is_numeric($size[1])) $size[1].= 'px';
    		$map_size= "width:".$size[0]."; height:".$size[1].";";
    	}

    	$map_search_controler = new map_search_controler(null, $current_search, $pmb_map_max_holds,false);
    	$json = $map_search_controler->get_json_informations();
    	//Obligatoire pour supprimer les {}
    	$json = substr($json, 1, strlen($json)-2);
    	if($map_search_controler->have_results()){
    		$map.= "<script type='text/javascript'>
						require(['dojo/ready', 'dojo/dom-attr', 'dojo/parser', 'dojo/dom'], function(ready, domAttr, parser, dom){
							ready(function(){
								domAttr.set('map_search', 'data-dojo-type', 'apps/map/map_controler');
								domAttr.set('map_search', 'data-dojo-props','searchId: ".$current_search.", mode:\"search_result\", ".$json."');
    								domAttr.set('map_search', 'style', '$map_size');
    								parser.parse('map_container');
    	});
    	});
    	</script>";
    	}else{
    		$map.= "<script type='text/javascript'>
						require(['dojo/ready', 'dojo/dom-construct'], function(ready, domConstruct){
							ready(function(){
								domConstruct.destroy('map_container');
							});
						});
			</script>";
    	}
    	print $map;
    }

    protected function get_display_nb_results($nb_results) {
    	global $msg;

    	return " => ".$nb_results." ".$msg["1916"]."<br />\n";
    }

    protected function show_objects_results($table, $has_sort) {
    	global $search;
    	global $nb_per_page_search;
    	global $page;
    	global $link_bulletin;

    	$recherche_ajax_mode=0;
    	$page = intval($page);
    	if($page) {
    	    $start_page = $nb_per_page_search * ($page-1);
    	} else {
    	    $start_page = 0;
    	}

    	$query = "select $table.*,notices.niveau_biblio from ".$table.",notices where notices.notice_id=$table.notice_id";
    	if(count($search) > 1 && !$has_sort) {
    		$query .= " order by index_serie, tnvol, index_sew";
    	}
    	$query .= " limit ".$start_page.",".$nb_per_page_search;

    	$result=pmb_mysql_query($query);
    	$records = array();
    	while ($r=pmb_mysql_fetch_object($result)) {
    		$records[] = $r->notice_id;
    	}
    	$this->get_elements_list_ui_class_name();
    	$elements_list_ui_class_instance = new $this->elements_list_ui_class_name($records, count($records), false);
    	$elements_list_ui_class_instance->add_context_parameter('in_search', '1');
    	$elements_list_ui_class_name = $this->elements_list_ui_class_name;
    	$elements_list_ui_class_name::set_link($this->link);
    	$elements_list_ui_class_name::set_link_expl($this->link_expl);
    	$elements_list_ui_class_name::set_link_explnum($this->link_explnum);
    	$elements_list_ui_class_name::set_link_serial($this->link_serial);
    	$elements_list_ui_class_name::set_link_analysis($this->link_analysis);
    	$elements_list_ui_class_name::set_link_bulletin($this->link_bulletin);
    	$elements_list_ui_class_name::set_link_explnum_serial($this->link_explnum_serial);
    	$elements_list_ui_class_name::set_link_explnum_analysis($this->link_explnum_analysis);
    	$elements_list_ui_class_name::set_link_explnum_bulletin($this->link_explnum_bulletin);
    	print "<div class='row'>".$elements_list_ui_class_instance->get_elements_list()."</div>";
    }

    protected function get_display_actions() {
    	global $msg, $charset;
    	global $opac_view_id;
    	global $dsi_active;
    	global $id_equation, $priv_pro, $id_empr;
    	global $pmb_opac_view_activate;
    	global $id_connector_set;
    	global $pmb_allow_external_search;
    	$display_actions = "";
		if ($dsi_active && !$opac_view_id) {
			if ($id_equation) {
				$mess_bouton = $msg['dsi_sauvegarder_equation'];
			} else {
				$mess_bouton = $msg["dsi_transformer_equation"];
				if($dsi_active == 2) {
					$mess_bouton = $msg["dsi_transformer_item"];
				}
			}
			$display_actions .= "<span class='space-disable'>&nbsp;</span>
						<input  type='button' class='bouton' onClick=\"document.forms['transform_dsi'].submit(); \" value=\"".$mess_bouton."\"/>
						<form name='transform_dsi' style='display:none;' method='post' action='./dsi.php'>";
			if ($priv_pro=="PRI") {
				$display_actions .= "
				<input type=hidden name='categ' value='bannettes' />
				<input type=hidden name='sub' value='abo' />
				<input type=hidden name='suite' value='transform_equ' />
				<input type=hidden name='id_equation' value='$id_equation' />
				<input type=hidden name='id_empr' value='$id_empr' />
				<input type=hidden name='requete' value='".htmlentities($this->serialize_search(),ENT_QUOTES,$charset)."' />";
			} else {
				$display_actions .= "
				<input type=hidden name='categ' value='equations' />
				<input type=hidden name='sub' value='gestion' />
				<input type=hidden name='suite' value='transform' />
				<input type=hidden name='id_equation' value='$id_equation' />
				<input type=hidden name='requete' value='".htmlentities($this->serialize_search(),ENT_QUOTES,$charset)."' />";
			}
			$display_actions .= "</form>";
    	}
		if ($pmb_opac_view_activate) {
			if($opac_view_id){
				$mess_bouton = $msg['opac_view_sauvegarder_equation'] ;
				$display_actions .= "
					<span class='space-disable'>&nbsp;</span>
					<input  type='button' class='bouton' onClick=\"document.forms['transform_opac_view'].submit(); \" value=\"".$mess_bouton."\"/>
					<form name='transform_opac_view' style='display:none;' method='post' action='./admin.php?categ=opac&sub=opac_view&section=list&action=form&opac_view_id=$opac_view_id'>
						<input type=hidden name='suite' value='transform_equ' />
						<input type=hidden name='opac_view_id' value='$opac_view_id' />
						<input type=hidden name='requete' value='".htmlentities($this->serialize_search(),ENT_QUOTES,$charset)."' />
					</form>";
			}
    	}
    	// transformation de la recherche en multicriteres: on reposte tout avec mode=8
    	if(!$opac_view_id){
			$display_actions .= "<span class='space-disable'>&nbsp;</span><input  type='button' class='bouton' onClick='document.search_transform.submit(); return false;' value=\"".$msg["search_notice_to_expl_transformation"]."\"/>";
			$display_actions .= "<form name='search_transform' action='./catalog.php?categ=search&mode=8&sub=launch' style=\"display:none\" method='post'>";
			$memo_search="";
			foreach($_POST as $key =>$val) {
				if($val) {
					if(is_array($val)) {
						foreach($val as $cle=>$val_array) {
							if(is_array($val_array)){
								foreach($val_array as $key_valeur => $valeur){
								    if(is_array($valeur)) {
								        foreach($valeur as $sub_key => $sub_valeur){
								            $memo_search.= "<input type='hidden' name=\"".$key."[".$cle."][".$key_valeur."][".$sub_key."]\" value='".htmlentities($sub_valeur,ENT_QUOTES,$charset)."'/>";
								        }
								    }else{
								        $memo_search.= "<input type='hidden' name=\"".$key."[".$cle."][".$key_valeur."]\" value='".htmlentities($valeur,ENT_QUOTES,$charset)."'/>";
 								    }
								}
							} else $memo_search.= "<input type='hidden' name='".$key."[]' value='".htmlentities($val_array,ENT_QUOTES,$charset)."'/>";
						}
					} else {
						$memo_search.="<input type='hidden' name='$key' value='$val'/>";
					}
    			}
    		}
    		$display_actions .= "$memo_search</form>";
    	}
		//transformation en set pour connecteur externe
		$id_connector_set=intval($id_connector_set);
		//Il faut que l'on soit passé par le formulaire d'édition de set pour avoir $id_connector_set pour ne pas avoir le bouton tout le temps vu qu'il sert rarement
		if ($pmb_allow_external_search && (SESSrights & ADMINISTRATION_AUTH) && $id_connector_set) {
			//Il faut qu'il y ait des sets multi critères si on veut pouvoir associer la recherche à quelque chose
			if (connector_out_sets::get_typed_set_count(2)) {
				$display_actions .= '<form name="export_to_outset" style="display:none;" method="post" action="./admin.php?categ=connecteurs&sub=out_sets&action=import_notice_search_into_set&candidate_id='.$id_connector_set.'"><input type="hidden" name="toset_search" value="'.htmlentities($this->serialize_search(),ENT_QUOTES,$charset).'" /></form>';
				$display_actions .= '<span class="space-disable">&nbsp;</span><input type="button" onClick="document.forms[\'export_to_outset\'].submit(); " class="bouton" value="'.htmlentities($msg["search_notice_to_connector_out_set"] ,ENT_QUOTES, $charset).'">';
			}
		}
		return $display_actions;
	}

	protected function get_display_icons($nb_results, $recherche_externe = false) {
	    global $msg, $mode;
		global $pmb_allow_external_search;
		global $pmb_nb_max_tri;
		global $affich_tris_result_liste;
		global $affich_external_tris_result_liste;

		$display_icons = "";
		if ($this->rec_history) {
			//Affichage des liens paniers et impression
			$current=$_SESSION["CURRENT"];
			if ($current!==false) {
				$tri_id_info = $_SESSION["tri"] ? "&sort_id=".$_SESSION["tri"] : "";
				$display_icons .= "<span class='space-disable'>&nbsp;</span>
									<a href='#' onClick=\"openPopUp('./print_cart.php?current_print=$current&action=print_prepare$tri_id_info','print',600,700,-2,-2,'scrollbars=yes,menubar=0,resizable=yes'); return false;\">
										<img src='".get_url_icon('basket_small_20x20.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_add_to_cart"]."\" title=\"".$msg["histo_add_to_cart"]."\">
									</a>
									<span class='space-disable'>&nbsp;</span>
									<a href='#' onClick=\"openPopUp('./print.php?current_print=$current&action_print=print_prepare$tri_id_info','print',500,600,-2,-2,'scrollbars=yes,menubar=0'); w.focus(); return false;\">
										<img src='".get_url_icon('print.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_print"]."\" title=\"".$msg["histo_print"]."\"/>
									</a>";
				$display_icons .= "<span class='space-disable'>&nbsp;</span>
									<a href='#' onClick=\"openPopUp('./download.php?current_download=$current&action_download=download_prepare".$tri_id_info."','download'); return false;\">
										<img src='".get_url_icon('upload.gif')."' style='border:0px' class='center' alt=\"".$msg["docnum_download"]."\" title=\"".$msg["docnum_download"]."\"/>
									</a>";
				if ($pmb_allow_external_search){

				    if ($mode == 7) {
				        // On n'affiche que le bouton de tri pour la recherche externe
    				    $display_icons = "";
				    }

					if($recherche_externe){
						$tag_a="href='catalog.php?categ=search&mode=7&from_mode=6&external_type=multi'";
					}else{
						$tag_a="onClick=\"alert('".$msg["search_interdite_externe"]."')\"";
					}
					$display_icons .= "<span class='space-disable'>&nbsp;</span>
										<a ".$tag_a."  title='".$msg["connecteurs_external_search_sources"]."'><img src='".get_url_icon('external_search.png')."' style='border:0px' class='center' alt=\"".$msg["connecteurs_external_search_sources"]."\"/></a>";
				}
				if ($nb_results<=$pmb_nb_max_tri) {
				    if ($pmb_allow_external_search && $recherche_externe && $mode == 7) {
				        $display_icons .= $affich_external_tris_result_liste;
				    } else {
    					$display_icons .= $affich_tris_result_liste;
				    }
				}
			}
		}
		if(empty($this->context_parameters['in_selector']) && $mode != 7) {
		    // mode != 7 || On n'affiche que le d'action en lot pour la recherche externe
		    $display_icons .= searcher::get_quick_actions();
		}
		return $display_icons;
	}

	protected function get_back_button($query_data) {
		global $form_json_data, $msg;
		return '
				<script type="text/javascript">
					var form_json_data_form = document.createElement("form");
					form_json_data_form.setAttribute("action", "'.$query_data['QUERY']["URI"].'");
					form_json_data_form.setAttribute("method", "POST");
					document.getElementById("error_message_history_button").innerHTML = "";
					document.getElementById("error_message_history_button").appendChild(form_json_data_form);

					var form_json_data = JSON.parse("'.$form_json_data.'");
					for (var key in form_json_data) {
						if (Array.isArray(form_json_data[key])) {
							for (var i = 0; i < form_json_data[key].length; i++) {
								var input = document.createElement("input");
								input.setAttribute("type", "hidden");
								input.setAttribute("name", key);
								input.setAttribute("value", form_json_data[key][i]);
								form_json_data_form.appendChild(input);
							}
						} else {
							var input = document.createElement("input");
							input.setAttribute("type", "hidden");
							input.setAttribute("name", key);
							input.setAttribute("value", form_json_data[key]);
							form_json_data_form.appendChild(input);
						}
					}
					var input = document.createElement("input");
					input.setAttribute("value", "'.$msg['89'].'");
					input.setAttribute("class", "bouton");
					input.setAttribute("type", "submit");

					form_json_data_form.appendChild(input);
				</script>';
	}

	public function get_script_window_onload() {
		return $this->script_window_onload;
	}

	public function get_multi_search_operator() {
		global $pmb_multi_search_operator;
		return $pmb_multi_search_operator;
	}

	public function set_filtered_objects_types($filtered_objects_types=array()) {
		$this->filtered_objects_types = $filtered_objects_types;
	}

	public function get_form_title() {
		global $msg;
		global $mode;
		global $current_module;
		global $charset;

		$form_title = '';
		switch ($current_module) {
			case 'catalog':
				switch ($mode) {
					case 8:
						$form_title .= htmlentities($msg["search_expl"], ENT_QUOTES, $charset);
						break;
					case 6:
						$form_title .= htmlentities($msg["search_extended"], ENT_QUOTES, $charset);
						break;
					default:
						if((isset($_SESSION["ext_type"]) && $_SESSION["ext_type"]=="simple")) {
							$form_title .= htmlentities($msg["connecteurs_external_simple"], ENT_QUOTES, $charset);
						} else {
							$form_title .= htmlentities($msg["connecteurs_external_multi"], ENT_QUOTES, $charset);
						}
						break;
				}
				break;
			case 'autorites':
				$form_title .= htmlentities($msg["search_extended"], ENT_QUOTES, $charset);
				break;
			case 'circ':
				$form_title .= htmlentities($msg["search_emprunteur"], ENT_QUOTES, $charset);
				break;
			case 'ajax':
				$form_title .= htmlentities($msg["search_extended"], ENT_QUOTES, $charset);
				break;
		}
		return $form_title;
	}

	public function get_elements_list_ui_class_name() {
		if(!isset($this->elements_list_ui_class_name)) {
			$this->elements_list_ui_class_name = "elements_records_list_ui";
		}
		return $this->elements_list_ui_class_name;
	}

	public function set_elements_list_ui_class_name($class_name) {
		$this->elements_list_ui_class_name = $class_name;
	}

	public function get_navbar($nb_results, $hidden_form){
		global $nb_per_page_search, $page;
		if ($nb_results) {
		    if (!$page) {
		        $current_page = 1 ;
		    } else {
		        $current_page = intval($page);
		    }
			$navbar = new Navbar($current_page, $nb_results, $nb_per_page_search);
			if (!$hidden_form) {
			    $navbar->setHiddenFormName($this->get_hidden_form_name(), true);
			} else {
			    $navbar->setHiddenFormName($this->get_hidden_form_name());
			}
			$nav_bar = "<div id='results_pager' class='center'>".$navbar->render()."</div>";
			echo $nav_bar ;
		}
	}

	protected function get_hidden_form_name(){
		return 'search_form';
	}

	public function json_encode_search() {
	    global $search;

	    $to_json=array();
	    $to_json["SEARCH"]=$search;
	    if (!empty($search) && is_array($search)) {
    	    for ($i=0; $i<count($search); $i++) {
    	        $to_json[$i]["SEARCH"]=$search[$i];
    	        $to_json[$i]["OP"]=$this->get_global_value("op_".$i."_".$search[$i]);
    	        $to_json[$i]["FIELD"]=$this->get_global_value("field_".$i."_".$search[$i]);
    	        $to_json[$i]["FIELD1"]=$this->get_global_value("field_".$i."_".$search[$i]."_1");
    	        $to_json[$i]["INTER"]=$this->get_global_value("inter_".$i."_".$search[$i]);
    	        $to_json[$i]["FIELDVAR"]=$this->get_global_value("fieldvar_".$i."_".$search[$i]);
    	    }
	    }
	    return encoding_normalize::json_encode($to_json);
	}

	public function json_decode_search($json_encoded) {
	    global $search;

	    $from_json = encoding_normalize::json_decode($json_encoded, true);
	    $search = $from_json["SEARCH"];
	    if (!empty($search) && is_array($search)) {
    	    for ($i=0; $i<count($search); $i++) {
    	        $this->set_global_value("op_".$i."_".$search[$i], $from_json[$i]["OP"]);
    	        $this->set_global_value("field_".$i."_".$search[$i], $from_json[$i]["FIELD"]);
    	        if(isset($from_json[$i]["FIELD1"])) {
    	            $this->set_global_value("field_".$i."_".$search[$i]."_1", $from_json[$i]["FIELD1"]);
    	        } else {
    	            $this->set_global_value("field_".$i."_".$search[$i]."_1");
    	        }
    	        $this->set_global_value("inter_".$i."_".$search[$i], $from_json[$i]["INTER"]);
    	        $this->set_global_value("fieldvar_".$i."_".$search[$i], $from_json[$i]["FIELDVAR"]);
    	    }
	    }
	}

	public function proceed_ajax() {
		global $sub;
		global $add_field, $delete_field, $launch_search;
		global $search;

		$this->init_links();
		switch ($sub) {
			case 'get_data_search' :
			    $data = array();
			    $data['human_query'] = encoding_normalize::utf8_normalize($this->make_human_query());
			    $data['search'] = $this->json_encode_search();
			    $data['search_serialize'] = $this->serialize_search();

			    ajax_http_send_response($data);
				break;

			case 'get_already_selected_fields' :
			default:
				if (($add_field)&&(($delete_field==="")&&(!$launch_search))) {
					if(empty($search)) {
						$search = array();
					}
					$search[]=$add_field;
				}
				print encoding_normalize::utf8_normalize($this->get_already_selected_fields());
				print '<script type="text/javascript">';
				print $this->get_script_window_onload();
				print '</script>';
				break;
		}
	}

	public function init_links() {
		$this->link = notice::get_pattern_link();
		$this->link_expl = exemplaire::get_pattern_link();
		$this->link_explnum = explnum::get_pattern_link();
		$this->link_serial = serial::get_pattern_link();
		$this->link_analysis = analysis::get_pattern_link();
		$this->link_bulletin = bulletinage::get_pattern_link();
		$this->link_explnum_serial = "./catalog.php?categ=serials&sub=explnum_form&serial_id=!!serial_id!!&explnum_id=!!explnum_id!!";
		$this->link_explnum_analysis = "./catalog.php?categ=serials&sub=analysis&action=explnum_form&bul_id=!!bul_id!!&analysis_id=!!analysis_id!!&explnum_id=!!explnum_id!!";
		$this->link_explnum_bulletin = "./catalog.php?categ=serials&sub=bulletinage&action=explnum_form&bul_id=!!bul_id!!&explnum_id=!!explnum_id!!";
	}

	public function get_context_parameters() {
		return $this->context_parameters;
	}

	public function set_context_parameters($context_parameters=array()) {
		$this->context_parameters = $context_parameters;
	}

	public function add_context_parameter($key, $value) {
		$this->context_parameters[$key] = $value;
	}

	public function delete_context_parameter($key) {
		unset($this->context_parameters[$key]);
	}

	public function get_misc_search_fields() {
		global $include_path;

		if(!isset($this->misc_file_search_fields)) {
			if(!$this->full_path) {
				$full_path = $include_path."/search_queries";
			} else {
				$full_path = substr($this->full_path, strlen($this->full_path)-1);
			}
			$this->misc_file_search_fields = new misc_file_search_fields($full_path, $this->fichier_xml.".xml");
		}
		return $this->misc_file_search_fields;
	}

	public function generate_query_op_and($prefixe = "", $suffixe = "", $search_table = "") {
	    if ($prefixe) {
	        return "create temporary table ".$prefixe."and_result_".$suffixe." ENGINE=".$this->current_engine." select ".$search_table.".* from ".$search_table." where exists ( select ".$prefixe."mf_".$suffixe.".* from ".$prefixe."mf_".$suffixe." where ".$search_table.".notice_id=".$prefixe."mf_".$suffixe.".notice_id)";
	    } else {
	        return "create temporary table and_result_".$suffixe." ENGINE=".$this->current_engine." select ".$search_table.".* from ".$search_table." where exists ( select mf_".$suffixe.".* from mf_".$suffixe." where ".$search_table.".notice_id=mf_".$suffixe.".notice_id)";
	    }
	}

	public function get_field_op_special($op, $Fixedfield, $i, $n, $search, $pp)
	{
	    $class = $this->get_class_op_special($Fixedfield, $op);
	    return $class->get_field($i, $n, $search, $pp);
	}

	public function is_empty_op_special($Fixedfield, $op,  $i, $search)
	{
	    $class = $this->get_class_op_special($Fixedfield, $op);
	    return $class->is_empty($i, $search);
	}

	public function custom_search_op_special($Fixedfield, $op,  $i, $search)
	{
	    $class = $this->get_class_op_special($Fixedfield, $op);
	    return $class->make_search($i, $search);
	}

	public function make_human_query_special_op($Fixedfield, $op, $field)
	{
	    $class = $this->get_class_op_special($Fixedfield, $op);
	    return $class->make_human_query($field);
	}

	private function get_class_op_special($Fixedfield, $op)
	{
	    global $include_path;

	    $class = null;
	    $operator_index = $Fixedfield['QUERIES_INDEX'][$op];
	    $operator = $Fixedfield['QUERIES'][$operator_index];
	    $classname = $operator[0]['SPECIAL']['CLASS'];
	    require_once($include_path."/search_queries/operators/".$classname.".class.php");

	    $params = array();
	    $params['search'] = $this;
	    if (!empty($operator[0]['SPECIAL']['PARAMS'])) {
	        $length = count($operator[0]['SPECIAL']['PARAMS']);
	        for ($i = 0; $i < $length; $i++) {
	            $param = $operator[0]['SPECIAL']['PARAMS'][$i];

	            $value = "";
	            if (!empty($param['DEFAULT']['value'])) {
	                $value = $param['DEFAULT']['value'];
	            }

	            switch ($param['TYPE']) {

	                case 'global':
	                    global ${$param['NAME']};
	                    if (!empty(${$param['NAME']})) {
	                        $value=${$param['NAME']};
	                    }

	                    $params[$param['NAME']] = $value;
	                    break;

	                case 'const':
	                    if (defined(strtoupper($param['NAME']))) {
	                        $params[strtoupper($param['NAME'])] = constant(strtoupper($param['NAME']));
	                    } else {
	                        $params[strtoupper($param['NAME'])] = $value;
	                    }
	                    break;

	                case "text":
	                default:
	                    $params[$param['NAME']] = $value;
	                    break;

	            }
	        }
	    }
        $class = new $classname($params);

	    return $class;
	}

	public static function get_join_and_clause_from_equation($type = TYPE_NOTICE, $equation='') {
	    $notice_clause = '';
	    $notice_ids = array();

	    if($equation) {
	        $my_search = new search(false, 'search_fields');
	        $my_search->unserialize_search(stripslashes($equation));
	        $res = $my_search->make_search();

	        $req="select * from ".$res ;
	        $resultat=pmb_mysql_query($req);

	        while($r=pmb_mysql_fetch_object($resultat)) {
	            $notice_ids[]=$r->notice_id;
	        }
	        switch ($type) {
	            case TYPE_BULLETIN :
	                $clause_property = "bulletins.num_notice";
	                break;
	            case TYPE_NOTICE :
	            default:
	                $clause_property = "notices.notice_id";
	                break;
	        }
	        if (count($notice_ids)) {
	            $notice_clause = " and $clause_property IN (".implode(',',$notice_ids).") ";
	        }else {
	            $notice_clause = " and $clause_property IN (0) ";
	        }
	    }

	    return array(
	        'clause' => $notice_clause
	    );
	}
}
?>