<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_tabs.class.php,v 1.98.4.4 2025/02/25 16:15:36 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Common\Library\Navbar\Navbar;

global $class_path;
require_once($class_path."/searcher/searcher_authorities_tab.class.php");
require_once($class_path."/search.class.php");
require_once($class_path."/elements_list/elements_authorities_list_ui.class.php");
require_once($class_path."/elements_list/elements_records_list_ui.class.php");
require_once($class_path."/search_perso.class.php");
require_once($class_path."/cache_factory.class.php");
require_once($class_path."/search_authorities.class.php");

class searcher_tabs {
	
	protected $xml_file = "";
	protected $full_path = "";
	protected $default_mode = 0;
	protected $url_target = "";
	protected $tabs = array();
	protected $current_mode = 0;
	protected $object_ids = array();
	protected $search_nb_results = 0;
	protected $page = 0;
	protected $nbepage = 0;
	protected $js_dynamic_check_form = "";
	protected $default_selector_mode = 0;
	private $search_failed = false;
	protected $default_authority_selector_mode = array();
	protected $redirect_link = "";
	protected $objects_all_ids = "";
	protected $objects_ids = array();
	
    public function __construct($xml_file="",$full_path='') {
    	$this->xml_file = $xml_file;
    	$this->full_path = $full_path;
    	$this->parse_search_file();
    }
    
    /**
     * Visibilité d'un onglet / d'un champ de recherche
     * @param array $element
     */
    protected function parse_visibility($element) {
    	$parsed_visibility = array();
    	if(isset($element['VAR']) && is_array($element['VAR'])) {
    		for ($j=0; $j<count($element['VAR']); $j++) {
    			$q=array();
    			$q['NAME']=$element['VAR'][$j]['NAME'];
    			if ($element['VAR'][$j]['VISIBILITY']=="yes")
    				$q['VISIBILITY']=true;
    			else
    				$q['VISIBILITY']=false;
    			$v=array();
    			if(isset($element['VAR'][$j]['VALUE']) && is_array($element['VAR'][$j]['VALUE'])) {
	    			for ($k=0; $k<count($element['VAR'][$j]['VALUE']); $k++) {
	    				if ($element['VAR'][$j]['VALUE'][$k]['VISIBILITY']=="yes")
	    					$v[$element['VAR'][$j]['VALUE'][$k]['value']] = true ;
	    				else
	    					$v[$element['VAR'][$j]['VALUE'][$k]['value']] = false ;
	    			}
    			}
    			$q['VALUE'] = $v ;
    			$parsed_visibility[] = $q ;
    		}
    	}
    	return $parsed_visibility;
    }
       
    protected function parse_field($field) {
    	
    	$parsed_field = array();
    	
    	$parsed_field['ID'] = $field['ID'];
    	$parsed_field['TITLE'] = $field['TITLE'];
    	$parsed_field['HIDDEN'] = (isset($field['HIDDEN']) ? $field['HIDDEN'] : '');
    	if(isset($field['INPUT'])){
    	   $parsed_field['INPUT_TYPE'] = $field['INPUT'][0]['TYPE'];
    	   $parsed_field['INPUT_OPTIONS']=$field['INPUT'][0];
    	}
    	$parsed_field['GLOBALVAR'] = (isset($field['GLOBALVAR']) ? $field['GLOBALVAR'] : '');
    	$parsed_field['VALUE'] = (isset($field['VALUE']) ? $field['VALUE'] : '');
    	$parsed_field['KEEP_EMPTY_WORDS'] = ((isset($field['CLASS'][0]['KEEP_EMPTYWORD']) && $field['CLASS'][0]['KEEP_EMPTYWORD'] == 'yes') ? 1 : 0);
    	
    	if (isset($field['CLASS']) && is_array($field['CLASS'])) {
	    	if (isset($field['CLASS'][0]['TYPE'])) {
	    		$parsed_field['TYPE'] = $field['CLASS'][0]['TYPE'];
	    		if(isset($field['CLASS'][0]['MODE'])){
	    			$parsed_field['MODE'] = $field['CLASS'][0]['MODE'];
	    			 
	    		}
	    		if(isset($field['CLASS'][0]['FIELDRESTRICT']) && count($field['CLASS'][0]['FIELDRESTRICT'])) {
	    			foreach ($field['CLASS'][0]['FIELDRESTRICT'] as $fieldrestrict) {
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
	    				$parsed_field['FIELDRESTRICT'][] = array(
	    						'field' => $fieldrestrict['FIELD'][0]['value'],
	    						'values' => explode(',', $fieldrestrict['VALUES'][0]['value']),
	    						'op' => $fieldrestrict['OP'][0]['value'],
	    						'not' => (isset($fieldrestrict['NOT'][0]['value']) ? $fieldrestrict['NOT'][0]['value'] : ''),
	    						'sub' => $subfieldsrestrict
	    				);
	    			}
	    		} else {
	    		    if(isset($field['CLASS'][0]['QUERY']) && count($field['CLASS'][0]['QUERY'])){
	    		        $parsed_field['QUERY'] =$field['CLASS'][0]['QUERY'][0]['value'];
	    		    }
	    		}
	    	}else if(isset($field['CLASS'][0]['NAME'])) {
	    		$parsed_field['CLASS'] = $field['CLASS'][0]['NAME'];
	    		if(isset($field['CLASS'][0]['FIELDRESTRICT']) && count($field['CLASS'][0]['FIELDRESTRICT'])) {
	    			$field_restrict = array();
	    			foreach ($field['CLASS'][0]['FIELDRESTRICT'] as $fieldrestrict) {
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
	    				$parsed_field['FIELDRESTRICT'][] = array(
	    						'field' => $fieldrestrict['FIELD'][0]['value'],
	    						'values' => explode(',', $fieldrestrict['VALUES'][0]['value']),
	    						'op' => $fieldrestrict['OP'][0]['value'],
	    						'not' => (isset($fieldrestrict['NOT'][0]['value']) ? $fieldrestrict['NOT'][0]['value'] : ''),
	    						'sub' => $subfieldsrestrict
	    				);
	    			}
	    		}
	    	} else {
	    		$parsed_field['CLASS'] = $field['CLASS'][0]['value'];
	    	}
    	}
    	if(isset($field['QUERYID']) && is_array($field['QUERYID'])) {
    	    $parsed_field['QUERYID'] =  $field['QUERYID'][0]['value'];
    	}
    	if(isset($field['QUERYFILTER'])){
    	    foreach($field['QUERYFILTER'] as $queryfilter){
    	        $parsed_field['QUERYFILTER'][$queryfilter['FILTER']] = $queryfilter['value'];
    	    }
    	}
		if (!empty($field["DETAILS"])) {
    		$parsed_field['DETAILS'] = [];
    		foreach ($field["DETAILS"][0] as $name => $info) {
    		    $parsed_field['DETAILS'][$name] = $info[0]['value'];
    		}
		}
    	$parsed_field['VARVIS'] = $this->parse_visibility($field);
    	return $parsed_field;
    }
    
    //Parse du fichier de configuration
	protected function parse_search_file() {
    	global $base_path, $charset, $include_path, $what;
    	global $msg, $KEY_CACHE_FILE_XML, $mode;
    	
    	if(!$this->xml_file) {
    		$this->xml_file = "authorities";
    	}
    	if(!$this->full_path){
	    	$filepath = $include_path."/searcher_tabs/".$this->xml_file."_subst.xml";
	    	if (!file_exists($filepath)) {
	    		$filepath = $include_path."/searcher_tabs/".$this->xml_file.".xml";
	    	}
    	} else {
    		$filepath = $this->full_path.$this->xml_file."_subst.xml";
    		if (!file_exists($filepath)) {
    			$filepath = $this->full_path.$this->xml_file.".xml";
    		}
    	}
    	$fileInfo = pathinfo($filepath);
    	$fileName = preg_replace("/[^a-z0-9]/i","",$fileInfo['dirname'].$fileInfo['filename'].$charset);
    	$tempFile = $base_path."/temp/XML".$fileName.".tmp";
    	$dejaParse = false;
    	
    	$cache_php=cache_factory::getCache();
    	$key_file="";
    	if ($cache_php) {
    		$key_file=getcwd().$fileName.filemtime($filepath);
    		$key_file=$KEY_CACHE_FILE_XML.md5($key_file);
    		if($tmp_key = $cache_php->getFromCache($key_file)){
    			if($cache = $cache_php->getFromCache($tmp_key)){
    				if(count($cache) == 5){
    					$this->url_target = $cache[0];
		    			$this->default_mode = $cache[1];
		    			$this->default_selector_mode = $cache[2];
		    			$this->tabs = $cache[3];
		    			$this->default_authority_selector_mode = $cache[4];
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
	    		if(count($cache) == 5){
	    			$this->url_target = $cache[0];
	    			$this->default_mode = $cache[1];
	    			$this->default_selector_mode = $cache[2];
	    			$this->tabs = $cache[3];
	    			$this->default_authority_selector_mode = $cache[4];
	    		}else{
	    			//SOUCIS de cache...
	    			unlink($tempFile);
					$dejaParse = false;
	    		}
	    	}
    	}
	    if(!$dejaParse){
    		$fp=fopen($filepath,"r") or die("Can't find XML file");
    		$size=filesize($filepath);
    	    	
			$xml=fread($fp,$size);
			fclose($fp);
			$param=_parser_text_no_function_($xml, "PMBTABS", $filepath);
	
			//Lecture du mode par défaut
			if(!empty($param['DEFAULT_MODE'])) {
				$this->default_mode = $param['DEFAULT_MODE'][0]['value']; 
			}
			
			//Lecture du mode par défaut pour les autorités
			if (!empty($param["SELECTOR_MODE"][0]["MODE"])) {
    			$selector_mode = $param["SELECTOR_MODE"][0]["MODE"];
    		    for ($i = 0; $i < count($selector_mode); $i++) {
    		        $element = $selector_mode[$i];
    		        $this->default_authority_selector_mode[$element["TYPE"][0]["value"]] = $element["VALUE"][0]["value"];
    		    }
			}
			//Lecture du mode par défaut pour les sélécteurs de concepts 
			if(!empty($param['DEFAULT_SELECTOR_MODE'])) {
				$this->default_selector_mode = $param['DEFAULT_SELECTOR_MODE'][0]['value'];
			}
			
			//Lecture de l'url
			$this->url_target = $param['URL_TARGET'][0]['value'];
					
			$this->tabs = array();
			//Lecture des onglets
			for ($i=0; $i<count($param["TABS"][0]["TAB"]); $i++) {
				$tab = array();
				$p_tab = $param['TABS'][0]['TAB'][$i];
				$tab['TITLE'] = $p_tab['TITLE'];
				$tab['MODE'] = $p_tab['MODE'];
				$tab['OBJECTS_TYPE'] = (isset($p_tab['OBJECTS_TYPE']) ? $p_tab['OBJECTS_TYPE'] : '');
				$tab['SHOW_IN_SELECTOR'] = (isset($p_tab['SHOW_IN_SELECTOR']) ? $p_tab['SHOW_IN_SELECTOR'] : '');
				$tab['MULTISEARCHCRITERIA']=0;
				if(isset($p_tab['ADD'])){
				    $tab['ADD'] = $p_tab['ADD'];
				}
				
				if(isset($p_tab['MULTISEARCHCRITERIA'])) {
					if($p_tab['MULTISEARCHCRITERIA']=='yes'){
						$tab['MULTISEARCHCRITERIA']=1;
					}				
				}
				if(isset($p_tab['PREDEFINEDSEARCH'])) {
					$tab['PREDEFINEDSEARCH']=$p_tab['PREDEFINEDSEARCH'];
				} else {
					$tab['PREDEFINEDSEARCH']=0;
				}
				$tab['VARVIS']=$this->parse_visibility($p_tab);
				
				$fields = array();
				if(isset($p_tab['SEARCHFIELDS'][0]['FIELD'])) {
					$search_fields = $p_tab['SEARCHFIELDS'][0]['FIELD'];
					//Lecture des champs de recherche
					if(is_array($search_fields)){
						for ($j=0; $j<count($search_fields); $j++) {
							$fields[] = $this->parse_field($search_fields[$j]);
						}
					}
				}
				$tab['SEARCHFIELDS'] = $fields;
				
				$fields = array();
				if(isset($p_tab['FILTERFIELDS'][0]['FIELD'])) {
					$filter_fields = $p_tab['FILTERFIELDS'][0]['FIELD'];
					//Lecture des filtres de recherche
					if(is_array($filter_fields)){
						for ($j=0; $j<count($filter_fields); $j++) {
							$fields[] = $this->parse_field($filter_fields[$j]);
						}
					}
				}
				$tab['FILTERFIELDS'] = $fields;
	
				$this->tabs[$tab['MODE']] = $tab;
			}
			
			//mise en cache
			if($key_file){
			    $key_file_content=$KEY_CACHE_FILE_XML.md5(serialize(array($this->url_target,$this->default_mode, $this->default_selector_mode, $this->tabs, $this->default_authority_selector_mode)));
			    $cache_php->setInCache($key_file_content, array($this->url_target,$this->default_mode, $this->default_selector_mode,$this->tabs, $this->default_authority_selector_mode));
				$cache_php->setInCache($key_file,$key_file_content);
			}else{
				$tmp = fopen($tempFile, "wb");
				fwrite($tmp,serialize(array(
						$this->url_target,
						$this->default_mode,
						$this->default_selector_mode,
						$this->tabs,
						$this->default_authority_selector_mode
				)));
				fclose($tmp);
			}
    	}
    }
        
    protected function get_field($field, $type) {
    	global $charset;
    	global $msg;
    	global $lang;
    	
    	//Récupération des valeurs du POST
    	$field_name = $this->get_field_name($field, $type);
    	$values = $this->get_values_from_field($field_name, $type);
    	if (empty($values) && !empty($field['GLOBALVAR'])) {
    		global ${$field['GLOBALVAR'][0]['value']};
    		if (!empty(${$field['GLOBALVAR'][0]['value']})) {
    			$global_value = ${$field['GLOBALVAR'][0]['value']};
    			if (!is_array($global_value)) {
    				$global_value = array($global_value);
    			}
    			$values = $global_value;
    		}
    	}
    	$display = "<div class='row'>";
    	$display .= "<label class='etiquette'>".get_msg_to_display($field['TITLE'])."</label>";
    	$display .= "</div>";
    	$display .= "<div class='row'>";
    	switch ($field['INPUT_TYPE']) {
    		case "authoritie":
				if(!isset($values[0])) $values[0] = '';
    			if ($values[0] != 0) {
    			    if(!isset($field ['INPUT_OPTIONS'] ['SELECTOR'])) {
    			        $field ['INPUT_OPTIONS'] ['SELECTOR'] = '';
    			    }
    			    switch ($field ['INPUT_OPTIONS'] ['SELECTOR']) {
						case "auteur" :
							$aut = new auteur($values[0]);
							$libelle = $aut->get_isbd();
							break;
						case "categorie" :
							$libelle = categories::getlibelle($values[0], $lang);
							break;
						case "editeur" :
							$ed = new editeur($values[0]);
							$libelle = $ed->get_isbd();
							break;
						case "collection" :
							$coll = new collection($values[0]);
							$libelle = $coll->get_isbd();
							break;
						case "subcollection" :
							$coll = new subcollection($values[0]);
							$libelle = $coll->get_isbd();
							break;
						case "serie" :
							$serie = new serie($values[0]);
							$libelle = $serie->get_isbd();
							break;
						case "indexint" :
							$indexint = new indexint($values[0]);
							$libelle = $indexint->get_isbd();
							break;
						case "titre_uniforme" :
							$tu = new titre_uniforme($values[0]);
							$libelle = $tu->get_isbd();
							break;
						default :
							$libelle = $values[0];
							break;
					}
				} else {
					$libelle = "";
				}
				if(isset($field ['INPUT_OPTIONS'] ['ATT_ID_FILTER']) && $field ['INPUT_OPTIONS'] ['ATT_ID_FILTER']) {
					$att_id_filter = "att_id_filter='".$field ['INPUT_OPTIONS'] ['ATT_ID_FILTER']."'";
				} else {
					$att_id_filter = "";
				}
				if(isset($field ['INPUT_OPTIONS'] ['PARAM1']) && $field ['INPUT_OPTIONS'] ['PARAM1']) {
				    $param1 = "param1='".$field ['INPUT_OPTIONS'] ['PARAM1']."'";
				} else {
				    $param1 = "";
				}
				if(isset($field ['INPUT_OPTIONS'] ['PARAM2']) && $field ['INPUT_OPTIONS'] ['PARAM2']) {
				    $param2 = "param2='".$field ['INPUT_OPTIONS'] ['PARAM2']."'";
				} else {
				    $param2 = "";
				}
				$display .= "<input type='text' id='".$field_name."' name='".$field_name."[]' autfield='".$field_name."_id' autocomplete='off' completion='".$field["INPUT_OPTIONS"]["AJAX"]."'  value='" . htmlentities ( $libelle, ENT_QUOTES, $charset ) . "' class='saisie-80em' $param1 $param2 $att_id_filter onkeyup='reset_input(this.id);' callback='switch_input'/>";
				$display .= "<input type='hidden' id='".$field_name."_id' name='".$field_name."_id[]'  value='0'/>";
    			break;
    		case "text":
    			if(isset($field['INPUT_OPTIONS']['IFSEARCHEMPTY'])) {
    				$this->js_dynamic_check_form .= "if(!document.getElementById('".$field_name."').value) document.getElementById('".$field_name."').value='".$field['INPUT_OPTIONS']['IFSEARCHEMPTY']."';";
    			}
    			$input_placeholder = '';
    			if(isset($field['INPUT_OPTIONS']['PLACEHOLDER'])) {
	    			if (substr($field['INPUT_OPTIONS']['PLACEHOLDER'],0,4)=="msg:") {
	    				$input_placeholder = $msg[substr($field['INPUT_OPTIONS']['PLACEHOLDER'],4,strlen($field['INPUT_OPTIONS']['PLACEHOLDER'])-4)];
	    			} else {
	    				$input_placeholder = $field['INPUT_OPTIONS']["PLACEHOLDER"];
	    			}
    			}
    			$display.="<input type='text' id='".$field_name."' name='".$field_name."[]' value='".(isset($values[0]) ? htmlentities($values[0],ENT_QUOTES,$charset) : '')."' class='saisie-80em' ".($input_placeholder?"placeholder='".htmlentities($input_placeholder,ENT_QUOTES,$charset)."'":"")."/>";
    			break;
			case "query_list":
				$query=$field["INPUT_OPTIONS"]["QUERY"][0]["value"];
				if (isset($field["INPUT_OPTIONS"]["FILTERING"])) {
					if ($field["INPUT_OPTIONS"]["FILTERING"] == "yes") {
	    				$query = str_replace("!!acces_j!!", "", $query);
						$query = str_replace("!!statut_j!!", "", $query);
						$query = str_replace("!!statut_r!!", "", $query);
					}
				}
				if (isset($field["INPUT_OPTIONS"]["QUERY"][0]["USE_GLOBAL"])) {
					$use_global = explode(",", $field["INPUT_OPTIONS"]["QUERY"][0]["USE_GLOBAL"]);
					for($j=0; $j<count($use_global); $j++) {
						$var_global = $use_global[$j];
						global ${$var_global};
						// AR Pour le cas de la global concept_scheme
						if(is_array(${$var_global})){
						    $query = str_replace("!!".$var_global."!!", implode(',',${$var_global}), $query);
						}else{
                            $query = str_replace("!!".$var_global."!!", ${$var_global}, $query);
						}
					}
				}
				if (isset($field["INPUT_OPTIONS"]["MULTIPLE"]) && $field["INPUT_OPTIONS"]["MULTIPLE"] == 'yes') {
					$multiple = true;
				} else {
					$multiple = false;
				}
				$display .= "<select name='".$field_name."[]' style='width:40em;' ".($multiple ? "multiple=''" : "").">";
				if(isset($field["INPUT_OPTIONS"]["QUERY"][0]["ALLCHOICE"]) && $field["INPUT_OPTIONS"]["QUERY"][0]["ALLCHOICE"] == "yes"){
					$display .= "<option value='".(isset($field["INPUT_OPTIONS"]["QUERY"][0]["VALUEALLCHOICE"]) ? $field["INPUT_OPTIONS"]["QUERY"][0]["VALUEALLCHOICE"] : "")."'>".htmlentities(get_msg_to_display($field["INPUT_OPTIONS"]["QUERY"][0]["TITLEALLCHOICE"]), ENT_QUOTES, $charset)."</option>";
				}
				$result=pmb_mysql_query($query);
				while ($row=pmb_mysql_fetch_array($result)) {
					$display.="<option value='".htmlentities($row[0],ENT_QUOTES,$charset)."' ";
					$as=array_search($row[0],$values);
					if (($as!==null)&&($as!==false)) $display.=" selected";
						$display.=">".htmlentities(get_msg_to_display($row[1]),ENT_QUOTES,$charset)."</option>";
				}
				$display.="</select>";
				break;
			case "list":
				$options=$field["INPUT_OPTIONS"]["OPTIONS"][0];
				if (isset($field["INPUT_OPTIONS"]["MULTIPLE"]) && $field["INPUT_OPTIONS"]["MULTIPLE"] == 'yes') {
					$multiple = true;
				} else {
					$multiple = false;
				}
				$display .= "<select name='".$field_name."[]' style='width:40em;' ".($multiple ? "multiple=''" : "").">";
				sort($options["OPTION"]);
				for ($i=0; $i<count($options["OPTION"]); $i++) {
					$display .= "<option value='".htmlentities($options["OPTION"][$i]["VALUE"],ENT_QUOTES,$charset)."' ";
					$as=array_search($options["OPTION"][$i]["VALUE"],$values);
					if (($as!==null)&&($as!==false)) $display .= " selected";
					if (substr($options["OPTION"][$i]["value"],0,4)=="msg:") {
						$display .= ">".htmlentities($msg[substr($options["OPTION"][$i]["value"],4,strlen($options["OPTION"][$i]["value"])-4)],ENT_QUOTES,$charset)."</option>";
					} else {
						$display .= ">".htmlentities($options["OPTION"][$i]["value"],ENT_QUOTES,$charset)."</option>";
					}
				}
				$display .= "</select>";
				break;
			case "marc_list":
				$options=marc_list_collection::get_instance($field["INPUT_OPTIONS"]["NAME"][0]["value"]);
				$tmp=array();
				$tmp = $options->table;
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
				if ($field["INPUT_OPTIONS"]["RESTRICTQUERY"][0]["value"]) {
					$restrictquery=pmb_mysql_query($field["INPUT_OPTIONS"]["RESTRICTQUERY"][0]["value"]);
					if ($restrictqueryrow=@pmb_mysql_fetch_row($restrictquery)) {
						if ($restrictqueryrow[0]) {
							$restrictqueryarray=explode(",",$restrictqueryrow[0]);
							$existrestrict=true;
						}
					}
				}
				if (isset($field["INPUT_OPTIONS"]["MULTIPLE"]) && $field["INPUT_OPTIONS"]["MULTIPLE"] == 'yes') {
					$multiple = true;
				} else {
					$multiple = false;
				}
				$display .= "<select name='".$field_name."[]' class=\"ext_search_txt\" ".($multiple ? "multiple=''" : "").">";
				if($field["INPUT_OPTIONS"]["RESTRICTQUERY"][0]["ALLCHOICE"] == "yes"){
					$display .= "<option value=''>".htmlentities(get_msg_to_display($field["INPUT_OPTIONS"]["RESTRICTQUERY"][0]["TITLEALLCHOICE"]), ENT_QUOTES, $charset)."</option>";
				}
				foreach ($options->table as $key => $val) {
					if ($existrestrict && array_search($key,$restrictqueryarray)!==false) {
						$display .= "<option value='".htmlentities($key,ENT_QUOTES,$charset)."' ";
						$as=array_search($key,$values);
						if (($as!==null)&&($as!==false)) $display .= " selected";
						$display .= ">".htmlentities($val,ENT_QUOTES,$charset)."</option>";
					} elseif (!$existrestrict) {
						$display .= "<option value='".htmlentities($key,ENT_QUOTES,$charset)."' ";
						$as=array_search($key,$values);
						if (($as!==null)&&($as!==false)) $display .= " selected";
						$display .= ">".htmlentities($val,ENT_QUOTES,$charset)."</option>";
					}
				}
				$display .= "</select>";
				break;
			case "date":
				$input_placeholder = '';
				if(isset($field['INPUT_OPTIONS']['PLACEHOLDER'])) {
					if (substr($field['INPUT_OPTIONS']["PLACEHOLDER"],0,4)=="msg:") {
						$input_placeholder = $msg[substr($field['INPUT_OPTIONS']["PLACEHOLDER"],4,strlen($field['INPUT_OPTIONS']["PLACEHOLDER"])-4)];
					} else {
						$input_placeholder = $field['INPUT_OPTIONS']["PLACEHOLDER"];
					}
				}
				if(empty($values)){
				    $values[0] = "";
				}
				$display .= get_input_date($field_name."[]", $field_name, htmlentities($values[0],ENT_QUOTES,$charset));
				break;
			case "date_flot":
			    $input_placeholder = '';
			    if(isset($field['INPUT_OPTIONS']['PLACEHOLDER'])) {
			        if (substr($field['INPUT_OPTIONS']["PLACEHOLDER"],0,4)=="msg:") {
			            $input_placeholder = $msg[substr($field['INPUT_OPTIONS']["PLACEHOLDER"],4,strlen($field['INPUT_OPTIONS']["PLACEHOLDER"])-4)];
			        } else {
			            $input_placeholder = $field['INPUT_OPTIONS']["PLACEHOLDER"];
			        }
			    }
			    if(empty($values)){
			        $values[0] = "";
			    }
			    $display .= get_input_date_flot($field_name."[0]", $field_name, htmlentities($values[0],ENT_QUOTES,$charset));
			    break;
    	}
    	$display .= "</div>";
    	
    	return $display;
    }
    
    public function get_script_js_form($form_name='') {
    	$form_name = $form_name ? $form_name : 'search_'.$this->xml_file;
    	return "
    		<script>
    			document.forms['".$form_name."'].elements[0].focus();
	    		function searcher_tabs_check_form(obj) {
	    			var searchIsEmpty = true;
	    			".($this->js_dynamic_check_form != "" ? "var has_js_dynamic = true;" : "var has_js_dynamic = false;")."
	    			var query = obj.querySelectorAll('input[type=text]');
	    			for(var i = 0; i < query.length; i++) {
	    				if(query[i].id.substring(0,6) == 'search' && query[i].value != '') {
	    					searchIsEmpty = false;
	    				}
    				}
                    var query = obj.querySelectorAll('input[type=date]');
	    			for(var i = 0; i < query.length; i++) {
	    				if(query[i].id.substring(0,6) == 'search' && query[i].value != '') {
	    					searchIsEmpty = false;
	    				}
    				}
	    			if(searchIsEmpty) {
	    				if(!has_js_dynamic) {
	    					return false;
    					} else {
	    					".$this->js_dynamic_check_form."
    					}
    				}
	    			return true;
    			}
				function switch_input(field_label) {
					document.getElementById(field_label).setAttribute('class','saisie-80emr');	
				}
				function reset_input(field_label) {
					if(document.getElementById(field_label+'_id').value){
						document.getElementById(field_label+'_id').value = 0;
						document.getElementById(field_label).setAttribute('class','saisie-80em');
					}
				}
		</script>
    		<script src='./javascript/ajax.js'></script>
			<script>ajax_parse_dom();</script>";
    }
    
    public function get_content_form() {
    	global $msg;
    	
    	$form = "";
    	$tab=$this->get_current_tab();
    	if (!is_null($tab['SEARCHFIELDS'])) {
	    	foreach ($tab['SEARCHFIELDS'] as $search_field) {
	    		if($this->visibility($search_field)) {
	    			$form .= $this->get_field($search_field, 'search');
	    		}
	    	}    		
    	}
    	$form .= "
    		<div class='row'>
    			<span class='saisie-contenu'>
    				$msg[155]&nbsp;<a class='aide' title='$msg[1900]$msg[1901]$msg[1902]' onclick='aide_regex();return false;'>$msg[1550]</a>
    			</span>
    		</div>";
    	$i=0;
    	if (!is_null($tab['FILTERFIELDS'])) {
	    	foreach ($tab['FILTERFIELDS'] as $filter_field) {
	    		if($this->visibility($filter_field)) {
	    			if($i%2 == 0) $form .= "<div class='colonne2'>";
	    			else $form .= "<div class='colonne_suite'>";
	    			$form .= $this->get_field($filter_field, 'filter');
	    			$form .= "</div>";
	    			$i++;
	    		}
	    	}
    	}
    	return $form;
    }
    
    public function get_form() {
    	global $msg;
    	global $current_module;
    	
    	$form = "";
    	$tab=$this->get_current_tab();
    	$form .= "
    		<form id='search_".$this->xml_file."' class='form-".$current_module."' action='".$this->url_target."&mode=".$tab['MODE']."' method='post' onSubmit='return searcher_tabs_check_form(this);'>
    		<h3>".get_msg_to_display($tab['TITLE'])."</h3>
    		<div class='form-contenu'>";
    	$form .= $this->get_content_form();
    	$form .= "
    				<div class='row'></div>
	    		</div>
	    		<div class='row'>
	    			<input type='hidden' name='action' value='search' />
	    			<input class='bouton' type='submit' value='".$msg['search']."' />";
    	if(isset($tab['ADD'])){
    	    $form .= "
                     <input class='bouton' type='button' onclick='document.location=\"".$tab['ADD'][0]['value']."\"' value='".$msg['ajouter']."' />";
    	}
    	$form .= "
	    		</div>
    		</form>";
    	$form .= $this->get_script_js_form();
			
    	return $form;
    }

    public function get_title() {
		global $msg;
		
    	$title = "<h1>";
    	$title .= $msg['search']." : ".get_msg_to_display($this->tabs[$this->current_mode]['TITLE']);    		
    	$title .= "</h1>";
    	 
    	return $title;
    }
    
    public function get_tabs($display_mode='') {
    	
    	$tabs = "<div class='hmenu'>";
    	foreach ($this->tabs as $tab) {
    		if($this->visibility($tab,$display_mode)) {
    			$tabs .= "<span ".($this->current_mode == $tab['MODE'] ? "class='selected'" : "")." data-pmb-mode=".$tab['MODE']." data-pmb-object-type=".$tab['OBJECTS_TYPE']."><a href='".$this->url_target."&mode=".$tab['MODE']."'>".get_msg_to_display($tab['TITLE'])."</a></span>";
    		}
    	}
    	$tabs .= "</div>";
    	
    	return $tabs;
    }
    
    public function get_tab($mode=0) {
    	return isset($this->tabs[$mode]) ? $this->tabs[$mode] : null;
    }

    protected function get_values_from_field($field_name) {
        global ${$field_name};
        //pour le cas des champs autocomplés
        global ${$field_name."_id"};
        if(!empty(${$field_name."_id"})){
            return array(
                'id' => ${$field_name."_id"},
                'values' => ${$field_name}
            );
        }
        if(is_array(${$field_name})) {
            //cas tres particulier pour les dates flottantes
            if (isset(${$field_name}[0]["date_begin"])) {
                if (${$field_name}[0]["date_begin"] === "") {
                    return array();
                }
            }
            return ${$field_name};
        } else {
            return array();
        }
    }
    
    private function get_field_name($field, $type = 'search') {
    	if($type == 'filter') {
    		$field_name="filter_field_tab_".$field['ID'];
    	} else {
    		$field_name="search_field_tab_".$field['ID'];
    	}
    	return $field_name;
    }
    
    private function get_values_from_form() {
        
        $search_empty = 0;
        
    	$data = array();
    	$tab=$this->get_current_tab();
    	foreach ($tab['SEARCHFIELDS'] as $search_field) {
    		$t = array();
    		$t['id'] = $search_field['ID'];
    		$t['values'] = $this->get_values_from_field($this->get_field_name($search_field, 'search'));
    		if (empty($t['values'])) {
    		    $search_empty++;
    		}
    		$t['class'] = (isset($search_field['CLASS']) ? $search_field['CLASS'] : '');
    		$t['type'] = $search_field['TYPE'] ?? "";
    		$t['mode'] = (isset($search_field['MODE']) ? $search_field['MODE'] : '');
    		$t['query'] = (isset($search_field['QUERY']) ? $search_field['QUERY'] : '');
    		if(isset($search_field['FIELDRESTRICT']) && is_array($search_field['FIELDRESTRICT'])) {
    			$t['fieldrestrict'] = $search_field['FIELDRESTRICT'];
    		}
    		if(isset($search_field['QUERYID'])) {
    		    $t['queryid'] = $search_field['QUERYID'];
    		}
    		if(isset($search_field['QUERYFILTER'])) {
    		    $t['queryfilter'] = $search_field['QUERYFILTER'];
    		}
    		if(isset($search_field['DETAILS'])) {
    		    $t['details'] = $search_field['DETAILS'];
    		}
    		$data['SEARCHFIELDS'][]= $t;
    	}
    	if ($search_empty === count($tab['SEARCHFIELDS'])) {
    	    // On a récupèrer aucune valeur rechercher, il faut rediriger sur le formulaire.
    	    $this->search_failed = true;
    	}
    	foreach ($tab['FILTERFIELDS'] as $filter_field) {
    		$t = array();
    		$t['id'] = $filter_field['ID'];
    		if($filter_field['HIDDEN']){
    			$t['values'] = explode(',', $filter_field['VALUE'][0]['value']);
    		}else{
    			$t['values'] = $this->get_values_from_field($this->get_field_name($filter_field, 'filter'));
    		}
    		$t['globalvar'] = $filter_field['GLOBALVAR'][0]['value'] ??'';
    		$t['multiple'] = (isset($filter_field['INPUT_OPTIONS']['MULTIPLE']) ? $filter_field['INPUT_OPTIONS']['MULTIPLE'] : '');
    		if(isset($filter_field['FIELDRESTRICT']) && is_array($filter_field['FIELDRESTRICT'])) {
    		    $t['fieldrestrict'] = $filter_field['FIELDRESTRICT'];
    		}
    		$t['type'] = $filter_field['TYPE'] ?? "";
    		$t['mode'] = $filter_field['MODE'] ?? "";
    		$t['query'] = $filter_field['QUERY'] ?? "";
    		$t['keep_empty_words'] = $filter_field['KEEP_EMPTY_WORDS'] ?? "";
    		if(isset($search_field['QUERYFILTER'])) {
    		    $t['queryfilter'] = $search_field['QUERYFILTER'];
    		}
    		if(isset($search_field['DETAILS'])) {
    		    $t['details'] = $search_field['DETAILS'];
    		}
    		$data['FILTERFIELDS'][]= $t;
    	}
    	return $data;
    }
    
    protected function search() {
    	global $page;
    	global $pmb_show_authority_id, $mode, $msg;
    	
    	$values = $this->get_values_from_form();
    	// On test si on a le champ pour recherche sur l'identifiant d'une authorité
    	if ($pmb_show_authority_id) {
    	    $tab = $this->get_current_tab();
        	$filters = $values["FILTERFIELDS"];
        	$index = count($filters);
        	for ($i = 0; $i < $index; $i++) {
        	    if (!empty($filters[$i]["globalvar"]) && "f_authority_id" == $filters[$i]["globalvar"]) {
        	        $id = intval($filters[$i]["values"][0]);
        	        if (empty($id)) {
        	            break;
        	        }
        	        $aut_const = authority::get_const_type_object($tab['OBJECTS_TYPE']);
        	        if (authority::check_available_autority($id, $aut_const)) {
            	        $url = authority::get_url_from_type($tab['OBJECTS_TYPE']);
            	        $this->redirect_link = str_replace("!!id!!", $id, $url);
            	        return;
        	        } else {
        	            error_message($msg[235], $msg['authority_id_query_failed']." ".$id, 1, "./autorites.php?categ=search&mode=".$mode);
        	            die();
        	        }
        	    }
        	}
    	}
    	
    	if (!$this->search_failed) {    
    		$searcher_entities_tab = $this->get_instance_entities_tab($values);
    		if(static::class == 'searcher_selectors_tabs' && method_exists($searcher_entities_tab, 'add_context_parameter')) {
        		$searcher_entities_tab->add_context_parameter('in_selector', true);
        	}
        	$page = intval($page);
        	if($page) {
        	    $start_page = $this->get_nb_per_page() * ($page-1);
        	} else {
        	    $start_page = 0;
        	}
        	$this->objects_ids = $searcher_entities_tab->get_sorted_result("default", $start_page, $this->get_nb_per_page());
        	$this->objects_all_ids = $searcher_entities_tab->get_objects_ids();
        	$this->search_nb_results = $searcher_entities_tab->get_nb_results();
    	}
    }
    
    protected function get_current_tab(){
    	return $this->tabs[$this->current_mode];
    }

    protected function make_hidden_form() {
    	global $mode;
    	
    	$tab=$this->get_current_tab();
    	$form = "<form name='store_search'  action='".$this->url_target."&mode=".$tab['MODE']."' method='post' style='display : none'>";
    	foreach ($tab['SEARCHFIELDS'] as $search_field) {
    		$field_name = $this->get_field_name($search_field, 'search');
    		$values = $this->get_values_from_field($field_name);
    		$form.= $this->gen_hidden_field($field_name, $values);
    	
    	}
    	foreach ($tab['FILTERFIELDS'] as $filter_field) {
    		$field_name = $this->get_field_name($filter_field, 'filter');
    		$values = $this->get_values_from_field($field_name);
    		$form.= $this->gen_hidden_field($field_name, $values);
    	}
    	$form .= "<input type='hidden' name='action' value='search' />
    			<input type='hidden' name='mode' value='".$mode."' />
    		<input type='hidden' name='page' value='".$this->page."'/>
    		</form>";
    	return $form;
    }
    
    private function gen_hidden_field($field_name,$values){
        global $charset;
        $form = '';
        if(isset($values['id'])){
            for($i=0; $i < count($values['id']); $i++) {
                $form .= "<input type='hidden' name='".$field_name."_id[]' value='".htmlentities(stripslashes($values['id'][$i]),ENT_QUOTES,$charset)."' />";
            }
            for($i=0; $i < count($values['values']); $i++) {
                $form .= "<input type='hidden' name='".$field_name."[]' value='".htmlentities(stripslashes($values['values'][$i]),ENT_QUOTES,$charset)."' />";
            }
        }else{
            for($i=0; $i < count($values); $i++) {
                $form .= "<input type='hidden' name='".$field_name."[]' value='".htmlentities(stripslashes($values[$i]),ENT_QUOTES,$charset)."' />";
            }
        }
        return $form;
    }
    
    protected function get_human_field($field, $values) {
    	global $msg;
    	
    	$field_aff = [];
		switch ($field["INPUT_TYPE"]) {
			case "list":
				$options=$field["INPUT_OPTIONS"]["OPTIONS"][0];
				$opt=array();
				for ($j=0; $j<count($options["OPTION"]); $j++) {
					if (substr($options["OPTION"][$j]["value"],0,4)=="msg:") {
						$opt[$options["OPTION"][$j]["VALUE"]]=$msg[substr($options["OPTION"][$j]["value"],4,strlen($options["OPTION"][$j]["value"])-4)];
					} else {
						$opt[$options["OPTION"][$j]["VALUE"]]=$options["OPTION"][$j]["value"];
					}
				}
				for ($j=0; $j<count($values); $j++) {
					$field_aff[$j]=$opt[$values[$j]];
				}
				break;
			case "query_list":
				$requete = $field["INPUT_OPTIONS"]["QUERY"][0]["value"];
				if (isset($field["FILTERING"]) && $field["FILTERING"] == "yes") {
					$requete = str_replace("!!acces_j!!", "", $requete);
					$requete = str_replace("!!statut_j!!", "", $requete);
					$requete = str_replace("!!statut_r!!", "", $requete);
				}
				
				if (!empty($field["INPUT_OPTIONS"]["QUERY"][0]["USE_GLOBAL"])) {
					$use_global = explode(",", $field["INPUT_OPTIONS"]["QUERY"][0]["USE_GLOBAL"]);
					$nb_globals = count($use_global);
					for ($j = 0; $j < $nb_globals; $j++) {
						$var_global = $use_global[$j];
						global ${$var_global};
						$requete = str_replace("!!$var_global!!", ${$var_global}, $requete);
					}
				}
				$resultat = pmb_mysql_query($requete);
				
				$opt = array();
				while ($r_ = @pmb_mysql_fetch_row($resultat)) {
					$opt[$r_[0]] = $r_[1];
				}
				
				$nb_values = count($values);
				for ($j = 0; $j < $nb_values; $j++) {
				    $field_aff[$j] = '';
				    if ($values[$j] >= 0) {
			            // $opt[$values[$j]] peut être à -1
				        $value = html_entity_decode($values[$j]);
				        $field_aff[$j] = $opt[$value];
				        if (substr($opt[$values[$j]], 0, 4) == "msg:") {
				            // Cas particulier du message "Sans schéma"
				            $field_aff[$j] = get_msg_to_display($opt[$values[$j]]);
				        }
				    }
				}
				break;
			case "marc_list":
				$opt=marc_list_collection::get_instance($field["INPUT_OPTIONS"]["NAME"][0]["value"]);
				for ($j=0; $j<count($values); $j++) {
    				$field_aff[$j]=$opt->table[$values[$j]];
				}
				break;
			case "date":
				$field_aff[0]=format_date($values[0]);
				break;
			case "date_flot":
			    $field_aff[0] = "";
			    switch($values[0]["value"]) {
			        case "NEAR":
			            $field_aff[0] = $msg['parperso_option_duration_type0']." ".$values[0]["date_begin"];
			            break;
			        case "LTEQ":
			            $field_aff[0] = $msg['parperso_option_duration_type1']." ".$values[0]["date_begin"];
			            break;
			        case "GTEQ":
			            $field_aff[0] = $msg['parperso_option_duration_type2']." ".$values[0]["date_begin"];
			            break;
			        case "EQ":
			            $field_aff[0] = $msg['parperso_option_duration_type3']." ".$values[0]["date_begin"];
			            break;
			        case "BETWEEN":
			            $field_aff[0] = $msg['parperso_option_duration_type4']." ".$values[0]["date_begin"]." - ".$values[0]["date_end"];
			            break;
			    }
				break;
			case "authoritie":
			    if(!empty($values['values'])){
			        $field_aff= $values['values'];
			    }else{
			        $nb_values = count($values);
			        for ($j = 0; $j < $nb_values; $j++) {
    					if(is_numeric($values[$j]) && (${$op} == "AUTHORITY")){
    						switch ($field['INPUT_OPTIONS']['SELECTOR']){
    							case "categorie" :
    								$thes = thesaurus::getByEltId ( $values [$j] );
    								$values [$j] = categories::getlibelle ( $values [$j], $lang ) . " [" . $thes->libelle_thesaurus . "]";
    								if (isset ( $fieldvar ["id_thesaurus"] )) {
    									unset ( $fieldvar ["id_thesaurus"] );
    								}
    								break;
    							case "auteur" :
    								$aut = new auteur ( $values [$j] );
    								$values [$j] = $aut->get_isbd();
    								break;
    							case "editeur" :
    								$ed = new editeur ( $values [$j] );
    								$values [$j] = $ed->get_isbd();
    								break;
    							case "collection" :
    								$coll = new collection ( $values [$j] );
    								$values [$j] = $coll->get_isbd();
    								break;
    							case "subcollection" :
    								$coll = new subcollection ( $values [$j] );
    								$values [$j] = $coll->get_isbd();
    								break;
    							case "serie" :
    								$serie = new serie ( $values [$j] );
    								$values [$j] = $serie->get_isbd();
    								break;
    							case "indexint" :
    								$indexint = new indexint ( $values [$j] );
    								$values [$j] = $indexint->get_isbd();
    								break;
    							case "titre_uniforme" :
    								$tu = new titre_uniforme ( $values [$j] );
    								$values [$j] = $tu->get_isbd();
    								break;
    							case "notice" :
    								$values [$j] = notice::get_notice_title($values [$j]);
    								break;
    						}
    					}
    				}	
    				$field_aff= $values;
			    }
				break;
			default:
				$field_aff[0]=$values[0];
				break;
		}
		return '<b>'.get_msg_to_display($field['TITLE']).'</b> '.implode(' ', $field_aff);
    }
    
    protected function make_human_query($without_tags = false) {
    	global $msg;
    	
    	$human_queries = array();
    	$tab = $this->get_current_tab();
    	foreach ($tab['SEARCHFIELDS'] as $search_field) {
    		$values = $this->clean_array($this->get_values_from_field($this->get_field_name($search_field, 'search')));
    		//Cas classique
    		if(is_array($values) && isset($values[0]) && ($values[0] != '')) {
    			$human_queries[] = $this->get_human_field($search_field, $values);
    		}
    		//Autcomplétion
    		if(is_array($values) && isset($values['values'][0]) && ($values['values'][0] != '')) {
                $human_queries[] = $this->get_human_field($search_field, $values);
    		}
    		
    	}
    	foreach ($tab['FILTERFIELDS'] as $filter_field) {
    		$values = $this->clean_array($this->get_values_from_field($this->get_field_name($filter_field, 'filter')));
    		//Cas classique
    		if(is_array($values) && isset($values[0]) && ($values[0] != '')) {
    		    $human_queries[] = $this->get_human_field($filter_field, $values);
    		}
    		//Autcomplétion
    		if(is_array($values) && isset($values['values'][0]) && ($values['values'][0] != '')) {
    		    $human_queries[] = $this->get_human_field($filter_field, $values);
    		}
    	}
    	
    	$research = implode(', ', $human_queries);
    	if($this->search_nb_results) {
    		$research .= " => ".sprintf($msg["searcher_results"], $this->search_nb_results);
    	} else {
    		$research .= " => ".sprintf($msg[1915], $this->search_nb_results);
    	}
    	if ($without_tags) {
    		return $research;
    	}
    	return "<div class='othersearchinfo'>".$research."</div>";
    }
    	
    protected function get_nb_per_page() {
    	global $nb_per_page_search;
    	
    	$nb_per_page_search = intval($nb_per_page_search);
    	return $nb_per_page_search;
    }
    
    protected function pager() {
    	global $page;
    
    	if (!$this->search_nb_results) {
    	    return;
    	}
    	$page = intval($page);
    	if($page) {
    	    $this->page = $page;
    	    $current_page = $page;
    	} else {
    	    $current_page = 1 ;
    	}
    	$navbar = new Navbar($current_page, $this->search_nb_results, $this->get_nb_per_page());
    	$navbar->setHiddenFormName('store_search');
    	// affichage de la barre de navigation
    	print "<div id='results_pager' class='center'>".$navbar->render()."</div>";
    }
    
    public function show_result() {
    	global $begin_result_liste;
    	global $end_result_liste;
    	
    	print $this->make_hidden_form();
    	print $this->make_human_query();
    	if(is_array($this->objects_ids) && count($this->objects_ids)) {
    		$instance_elements_list_ui = $this->get_instance_elements_list_ui();
    		$instance_elements_list_ui->add_context_parameter('in_search', '1');
    		$elements = $instance_elements_list_ui->get_elements_list();
    		print $begin_result_liste;
    		switch($this->xml_file){
    		    case "records" :
    		        searcher_records::get_caddie_link();
    		        print searcher::get_quick_actions();
            		print searcher::get_search_back_button('', 'NOTI');
    		        break;
    		    default :
    		        $tab = $this->get_current_tab();
    		        search_authorities::get_caddie_link();
    		        search_authorities::get_sort_link($this->search_nb_results, $tab['OBJECTS_TYPE']);
    		        print searcher::get_quick_actions('AUT');
            		print searcher::get_search_back_button('', 'AUT');
    		        break;
    		}
    		print searcher::get_check_uncheck_all_buttons();
    		
    		//Réinitialisation des facettes
    		facettes::destroy_global_env();
    		
    		if($this->is_multi_search_criteria()){
    		    session::set_value('search', ['authorities' => ['extended_search' => $this->objects_all_ids]]);
    		    facettes::set_facet_type('authorities');
    		} else {
    		    $tab=$this->get_current_tab();
    		    facettes::set_search_mode('simple_search');
    		    if($tab['OBJECTS_TYPE'] == 'mixed') {
    		        facettes::set_facet_type('authorities');
    		        session::set_value('search', ['authorities' => ['simple_search' => $this->objects_all_ids]]);
    		    } else {
    		        facettes::set_facet_type($tab['OBJECTS_TYPE']);
    		        session::set_value('search', [$tab['OBJECTS_TYPE'] => ['simple_search' => $this->objects_all_ids]]);
    		    }
    		}
    		print "
            <div class='content_details'>
        		<div id='facettes_list' class='facettes_list'>".facettes::call_ajax_facettes()."</div>
            		<div id='results_list' class='results_list'>
            		".$elements."
        		</div>
    		</div>";
    		print $end_result_liste;
    		$this->pager();
    	}
    }
    
    protected function is_multi_search_criteria(){

    	$tab=$this->get_current_tab();
    	return $tab['MULTISEARCHCRITERIA'];
    }
    
	public function proceed($mode=0, $action="",$display_mode='') {
    	$this->set_current_mode($mode);
    	print $this->get_title();
    	print $this->get_tabs($display_mode);
		
    	switch($action){
    		case 'search':
    			$this->proceed_search();
    			break;
    		default:
    			$this->proceed_form();
    			break;
    	}
    }
    
    public function proceed_form() {
    	global $search;
    	global $pmb_extended_search_dnd_interface;
    	
    	$tab=$this->get_current_tab();
    	if($this->is_multi_search_criteria()){
    		$sc=$this->get_instance_search();
    		if($tab['PREDEFINEDSEARCH'] && !(is_array($search) && count($search))) {
    			$search_perso = $this->get_instance_search_perso($tab['PREDEFINEDSEARCH']);
    			$sc->unserialize_search($search_perso->query);
    		}
    		print $sc->show_form($this->url_target."&mode=".$tab['MODE'], $this->url_target."&mode=".$tab['MODE']."&action=search", "", $this->url_target."_perso&sub=form");
    		if ($pmb_extended_search_dnd_interface){
    			if(!isset($search_perso) || !is_object($search_perso)) {
    				$search_perso = $this->get_instance_search_perso();
    			}
    			print '<div id="search_perso" style="display:none">'.$search_perso->get_forms_list().'</div>';
    		}
    	} else {
    		print $this->get_form();
    	}
    }
    
    public function proceed_search() {
    	$tab=$this->get_current_tab();
    	if($this->is_multi_search_criteria()){
    		$sc=$this->get_instance_search();
    		$sc->reduct_search();
    		$this->set_session_history($sc->make_human_query(), $tab, "QUERY");
    		print $sc->show_results($this->url_target."&mode=".$tab['MODE']."&action=search", $this->url_target."&mode=".$tab['MODE'], true, '', true );
    		$this->set_session_history($sc->make_human_query(), $tab, $this->get_type());
    	} else {
    		$this->search();
    		// si lien de redirection on redirige
    		if ($this->redirect_link) {
    		    print "<script type=\"text/javascript\">";
    		    print "document.location = \"".$this->redirect_link."\"";
    		    print "</script>";
    		}
    		if ($this->search_failed) {
    		    // échec à la recherche on redirige sur le formulaire.
    		    $this->proceed_form();    	
    		} else {
        		$this->set_session_history($this->make_human_query(true), $tab, "QUERY");
        		print $this->show_result();
        		$this->set_session_history($this->make_human_query(true), $tab, $this->get_type(), "simple");
    		}
    	}
    }
    
    protected function clean_array($values_array){
    	global $charset;
    	$temp = array();
    	foreach($values_array as $key => $value){
    	    if(is_array($value)){
    	        $temp[$key] = $this->clean_array($value);
    	    }else{
                $temp[$key] = htmlentities(stripslashes($value), ENT_QUOTES, $charset);
    	    }
    	}
    	return $temp;
    }
    
    public function get_mode_multi_search_criteria($id_predefined_search=0){
    	$mode = 0;
    	$founded_predefined_search = false;
    	foreach ($this->tabs as $tab) {
    		if($tab['MULTISEARCHCRITERIA'] && $tab['PREDEFINEDSEARCH']) {
    			if($tab['PREDEFINEDSEARCH'] == $id_predefined_search) {
    				$mode = $tab['MODE'];
    				$founded_predefined_search = true;
    			}
    		} elseif(!$founded_predefined_search && $tab['MULTISEARCHCRITERIA']) {
    			$mode = $tab['MODE'];
    		}
    	}
    	return $mode;
    }
    
    public function get_mode_objects_type($objects_type=''){
    	$mode = 0;
    	foreach ($this->tabs as $tab) {
    		if($tab['OBJECTS_TYPE'] == $objects_type) {
    			$mode = $tab['MODE'];
    		}
    	}
    	return $mode;
    }
    
	// fonction de calcul de la visibilite d'un onglet / d'un champ de recherche
    protected function visibility($element,$display_mode='') {
    	if($display_mode === 'selector'){
    		if((!isset($element['SHOW_IN_SELECTOR'])) || ($element['SHOW_IN_SELECTOR']!='yes' && $element['SHOW_IN_SELECTOR']!='only')){
    			return false;
    		}
    	}else{
    		if(isset($element['SHOW_IN_SELECTOR']) && $element['SHOW_IN_SELECTOR']=='only'){
    			return false;
    		}
    	}
    	if(isset($element['HIDDEN']) && $element['HIDDEN']){
    		return false;
    	}
    	if (!count($element['VARVIS'])) return true;
    	$result = false;
    	
    	//on peut en avoir plusieurs, ca fonctionne avec un ET
    	for ($i=0; $i<count($element['VARVIS']); $i++) {
    	    //récupération du nom de la variable
    	    $name=$element['VARVIS'][$i]["NAME"] ;
    		global ${$name};
    		//quelle est la visibilité standard sur cette variable
            $result=$element['VARVIS'][$i]["VISIBILITY"];
            //si la variable n'est pas présente ou que le tableau est vide, on inverse la visiblité
            if(empty(${$name}) ){
                $result = !$result;
    		}
    		//on peut avoir un changement de visibilité sur des valeurs particulières
    		if (isset($element['VARVIS'][$i]["VALUE"])){
    		    // si la variable est un tableau, on veut checker pour chacune de ses valeurs avec un OU
        		if(is_array(${$name})){
        		    $sub_result = false;
        		    foreach(${$name} as $elem){
        		        // si l'une des valeurs de la variable est dans les valeurs spécifiques
        		        if (isset($element['VARVIS'][$i]["VALUE"][$elem])) {
        		            //on récupère la visiblité associée
        		            $sub_result = $element['VARVIS'][$i]["VALUE"][$elem];
        		            //si vrai, on sort parce qu'on veut faire un ou
        		            if($sub_result){
        		                $result = $sub_result;
        		                break;
        		            }
        		        }
        		        //on affecte
        		        $result = $sub_result;
        		    }
        		}else {
        		    if (!isset(${$name})) {
        		        if(isset($element['VARVIS'][$i]["VALUE"][0])){
                            $result = $element['VARVIS'][$i]["VALUE"][0] ;
        		        }
        		    }else if (isset($element['VARVIS'][$i]["VALUE"][${$name}])) {
            		    //on récupère la visiblité associée
            		    $result = $element['VARVIS'][$i]["VALUE"][${$name}] ;
        		    }
        		}
    		} 
    		//si pas visible, alors on ne teste pas la suite.
    		if(!$result) return false;
    	} // fin for
    	// normalement, à ce moment, $result vaut toujours true
    	return $result;
    }
    
    public function build_default_tab($mode, $objects_type) {
    	$this->tabs[$mode] = array(
    			'TITLE' => '',
    			'MODE' => $mode,
    			'OBJECTS_TYPE' => $objects_type,
    			'SHOW_IN_SELECTOR' => 'yes',
    			'MULTISEARCHCRITERIA' => 0,
    			'PREDEFINEDSEARCH' => 0,
    			'VARVIS' => array(),
    			'SEARCHFIELDS' => array($this->get_default_search_field($mode, $objects_type)),
    			'FILTERFIELDS' => array()
    	);
    }
    
    public function get_default_search_field($mode, $objects_type) {
    	return array(
    			'ID' => 1,
    			'TITLE' => 'msg:global_search',
    			'HIDDEN' => '',
    			'INPUT_TYPE' => 'text',
    			'INPUT_OPTIONS' => array('TYPE' => 'text', 'value' => '', 'IFSEARCHEMPTY' => '*'),
    			'GLOBALVAR' => '',
				'VALUE' => '',
				'TYPE' => $objects_type,
				'FIELDRESTRICT' => array(array('field' => 'code_champ','values' => array($mode.'100'), 'op' => 'and', 'not' => '', 'sub' => array())),
				'VARVIS' => array()
		);
    }
    
    /**
     * 
     * @param string $human_query
     * @param array $tab
     * @param string $type
     * @param string $search_type
     */
    protected function set_session_history($human_query,$tab, $type, $search_type = "extended") {
    	global $page, $msg;
    	
    	if(!isset($_SESSION["session_history"])) $_SESSION["session_history"] = array();
    	switch ($type) {
    		case 'QUERY' :
    			if ((string) $page == "" || $page == 0) {
    				$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["URI"] = $this->url_target."&mode=".$tab["MODE"];
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["POST"] = $_POST;
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["GET"] = $_GET;
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["GET"]["sub"] = "";
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["POST"]["sub"] = "";
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["POST"]["action"] = "";
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["HUMAN_QUERY"] = $human_query;
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["HUMAN_TITLE"] = "[".$msg["132"]."] ".get_msg_to_display($tab["TITLE"]);
    				$_POST["page"] = 1;
    				$page = 1;
    			}
    			break;
    		case 'AUT' :
    		case 'NOTI' :
    			if ($_SESSION["CURRENT"] !== false) {
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["URI"] = $this->url_target."&mode=".$tab["MODE"]."&action=search";
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["PAGE"] = $page;
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["POST"] = $_POST;
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["GET"] = $_GET;
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["HUMAN_QUERY"] = $human_query;
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["SEARCH_TYPE"] = $search_type;
    				if(strtoupper($this->get_current_tab()['OBJECTS_TYPE'])) {
    					$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["SEARCH_OBJECTS_TYPE"] = strtoupper($this->get_current_tab()['OBJECTS_TYPE']);
    				} else {
    					$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["SEARCH_OBJECTS_TYPE"] = "MIXED";
    				}
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["FORM_VALUES"] = $this->get_values_from_form();
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]['TEXT_LIST_QUERY']='';
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["TEXT_QUERY"] = "";
    			}
    			break;
    	}    	
    }
    
    public function set_current_mode($mode='') {
        global $what;
    	if(!$mode) {
    	    if ($what) {
    	        //on converti what en constante PMB
    	        $entity_type = entities::get_constant_from_what_parameter($what);
    	        if (is_numeric($entity_type)) {
        	        //puis en chaine
        	        $entity_type = entities::get_string_from_const_type($entity_type);
        	        $this->current_mode=$this->default_authority_selector_mode[$entity_type];
    	        } else {
    	            $this->current_mode=$this->default_mode;
    	        }
    	    }
    	    else {
        		$this->current_mode=$this->default_mode;
    	    }
    	} else {
    		$this->current_mode=$mode;
    	}
    }
    
    public function set_url_target($url_target) {
    	$this->url_target = $url_target;
    }
    
    public function get_instance_search() {
    	switch ($this->xml_file) {
    		case 'authorities':
    			return new search_authorities(true, 'search_fields_authorities');
    			break;
    		case 'records':
    			return new search(true);
    			break;
    	}
    }
    
    public function get_instance_search_perso($id=0) {
    	switch ($this->xml_file) {
    		case 'authorities':
    			return new search_perso($id, 'AUTHORITIES');
    			break;
    		case 'records':
    			return new search_perso($id);
    			break;
    	}
    }
    
    public function get_instance_entities_tab($values) {
    	switch ($this->xml_file) {
    		case 'authorities':
    			$searcher = new searcher_authorities_tab($values);
    			break;
    		case 'records':
    			$searcher = new searcher_records_tab($values);
    			break;
    	}
    	$searcher->add_restrict_no_display();
    	return $searcher;
    }
    
    public function get_instance_elements_list_ui() {
    	switch ($this->xml_file) {
    		case 'authorities':
    			return new elements_authorities_list_ui($this->objects_ids, $this->search_nb_results, 1);
    			break;
    		case 'records':
    			return new elements_records_list_ui($this->objects_ids, $this->search_nb_results, 1);
    			break;
    	}
    }
    
    public function get_type() {
    	switch ($this->xml_file) {
    		case 'authorities':
    			return "AUT";
    			break;
    		case 'records':
    		    return "NOTI";
    			break;
    	}
    }
    
    public function get_default_selector_mode(){
    	return $this->default_selector_mode;
    }
    
    public function get_search_nb_results() {
        return $this->search_nb_results;
    }
}