<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_tabs.class.php,v 1.8 2019-06-10 08:57:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

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
    	$parsed_field['INPUT_TYPE'] = $field['INPUT'][0]['TYPE'];
    	$parsed_field['INPUT_OPTIONS']=$field['INPUT'][0];
    	$parsed_field['GLOBALVAR']=(isset($field['GLOBALVAR']) ? $field['GLOBALVAR'] : '');
    	$parsed_field['VALUE']=(isset($field['VALUE']) ? $field['VALUE'] : '');
    	if(isset($field['CLASS']) && is_array($field['CLASS'])) {
	    	if(isset($field['CLASS'][0]['TYPE'])){
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
    	$parsed_field['VARVIS'] = $this->parse_visibility($field);
    	return $parsed_field;
    }
    
    //Parse du fichier de configuration
	protected function parse_search_file() {
    	global $base_path, $charset, $include_path;
    	global $msg, $KEY_CACHE_FILE_XML;
    	
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
    				if(count($cache) == 4){
    					$this->url_target = $cache[0];
		    			$this->default_mode = $cache[1];
		    			$this->default_selector_mode = $cache[2];
		    			$this->tabs = $cache[3];
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
	    		if(count($cache) == 4){
	    			$this->url_target = $cache[0];
	    			$this->default_mode = $cache[1];
	    			$this->default_selector_mode = $cache[2];
	    			$this->tabs = $cache[3];
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
			if($param['DEFAULT_MODE']) {
				$this->default_mode = $param['DEFAULT_MODE'][0]['value']; 
			}
			
			//Lecture du mode par défaut pour les sélécteurs de concepts 
			if($param['DEFAULT_SELECTOR_MODE']) {
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
			
			if($key_file){
				$key_file_content=$KEY_CACHE_FILE_XML.md5(serialize(array($this->url_target,$this->default_mode, $this->default_selector_mode,$this->tabs)));
				$cache_php->setInCache($key_file_content, array($this->url_target,$this->default_mode, $this->default_selector_mode,$this->tabs));
				$cache_php->setInCache($key_file,$key_file_content);
			}else{
				$tmp = fopen($tempFile, "wb");
				fwrite($tmp,serialize(array(
						$this->url_target,
						$this->default_mode,
						$this->default_selector_mode,
						$this->tabs,
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
				$display .= "<input type='text' id='".$field_name."' name='".$field_name."[]' autfield='".$field_name."_id' autocomplete='off' completion='".$field["INPUT_OPTIONS"]["AJAX"]."'  value='" . htmlentities ( $libelle, ENT_QUOTES, $charset ) . "' class='saisie-80em' $att_id_filter onkeyup='reset_input(this.id);' callback='switch_input'/>";
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
						$query = str_replace("!!".$var_global."!!", ${$var_global}, $query);
					}
				}
				
				$display .= "<select name='".$field_name."[]' style='width:40em;'>";
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
				$display .= "<select name='".$field_name."[]' style='width:40em;'>";
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
			
				$display .= "<select name='".$field_name."[]' class=\"ext_search_txt\">";
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
				$date_formatee = format_date_input($values[0]);
				$date_clic = "onClick=\"openPopUp('./select.php?what=calendrier&caller=search_form&date_caller=".str_replace('-', '', $values[0])."&param1=".$field_name."_date&param2=".$field_name."[]&auto_submit=NO&date_anterieure=YES&format_return=IN', 'calendar')\"  ";
				$input_placeholder = '';
				if(isset($field['INPUT_OPTIONS']['PLACEHOLDER'])) {
					if (substr($field['INPUT_OPTIONS']["PLACEHOLDER"],0,4)=="msg:") {
						$input_placeholder = $msg[substr($field['INPUT_OPTIONS']["PLACEHOLDER"],4,strlen($field['INPUT_OPTIONS']["PLACEHOLDER"])-4)];
					} else {
						$input_placeholder = $field['INPUT_OPTIONS']["PLACEHOLDER"];
					}
				}
				$display .= "<input type='hidden' name='".$field_name."_date' value='".str_replace('-','', $values[0])."' />
					<input type='text' name='".$field_name."[]' value='".htmlentities($date_formatee,ENT_QUOTES,$charset)."' ".($input_placeholder?"placeholder='".htmlentities($input_placeholder,ENT_QUOTES,$charset)."'":"")."/>
					<input class='bouton_small' type='button' name='".$field_name."_date_lib_bouton' value='".$msg["bouton_calendrier"]."' ".$date_clic." />";
				break;
    	}
    	$display .= "</div>";
    	
    	return $display;
    }
    
    public function get_script_js_form($form_name='') {
        global $base_path;
        
    	$form_name = $form_name ? $form_name : 'search_'.$this->xml_file;
    	return "
    		<script type='text/javascript'>
    			document.forms['".$form_name."'].elements[0].focus();
	    		function searcher_tabs_check_form(obj) {
	    			var searchIsEmpty = true;
	    			".($this->js_dynamic_check_form != "" ? "var has_js_dynamic = true;" : "var has_js_dynamic = false;")."
	    			var query = document.forms[obj].querySelectorAll('input[type=text]');
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
    		<script src='".$base_path."/includes/javascript/ajax.js'></script>
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
	    			<input class='bouton' type='submit' value='".$msg['autolevel1_search']."' />
	    		</div>
    		</form>";
    	$form .= $this->get_script_js_form();
			
    	return $form;
    }

    public function get_title() {
		global $msg;
		
    	$title = "<h1>";
    	$title .= $msg['autolevel1_search']." : ".get_msg_to_display($this->tabs[$this->current_mode]['TITLE']);    		
    	$title .= "</h1>";
    	 
    	return $title;
    }
    
    public function get_tabs($display_mode='') {
        global $module, $what;
    	
    	$tabs = "<div class='hmenu'>";
    	foreach ($this->tabs as $tab) {
    	    if($this->visibility($tab,$display_mode) && $module !== 'selectors' && $what !== 'ontology') {
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
    	if(is_array(${$field_name})) {
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

    	$data = array();
    	$tab=$this->get_current_tab();
    	foreach ($tab['SEARCHFIELDS'] as $search_field) {
    		$t = array();
    		$t['id'] = $search_field['ID'];
    		$t['values'] = $this->get_values_from_field($this->get_field_name($search_field, 'search'));
    		$t['class'] = (isset($search_field['CLASS']) ? $search_field['CLASS'] : '');
    		$t['type'] = $search_field['TYPE'];
    		$t['mode'] = (isset($search_field['MODE']) ? $search_field['MODE'] : '');
    		if(isset($search_field['FIELDRESTRICT']) && is_array($search_field['FIELDRESTRICT'])) {
    			$t['fieldrestrict'] = $search_field['FIELDRESTRICT'];
    		}
    		$data['SEARCHFIELDS'][]= $t;
    	}
    	foreach ($tab['FILTERFIELDS'] as $filter_field) {
    		$t = array();
    		$t['id'] = $filter_field['ID'];
    		if($filter_field['HIDDEN']){
    			$t['values'] = explode(',', $filter_field['VALUE'][0]['value']);
    		}else{
    			$t['values'] = $this->get_values_from_field($this->get_field_name($filter_field, 'filter'));
    		}
    		$t['globalvar'] = $filter_field['GLOBALVAR'][0]['value'];
    		$data['FILTERFIELDS'][]= $t;
    	}
    	return $data;
    }
    
    protected function search() {
    	global $page, $nb_per_page;
    	$values = $this->get_values_from_form();
    	$searcher_entities_tab = $this->get_instance_entities_tab($values);
    	$this->objects_ids = $searcher_entities_tab->get_sorted_result("default",$page*$nb_per_page,$nb_per_page);
    	$this->search_nb_results = $searcher_entities_tab->get_nb_results();
    }
    
    protected function get_current_tab(){
    	return $this->tabs[$this->current_mode];
    }

    protected function make_hidden_form() {
    	global $charset;
    	global $mode;
    	
    	$tab=$this->get_current_tab();
    	$form = "<form name='store_search'  action='".$this->url_target."&mode=".$tab['MODE']."' method='post' style='display : none'>";
    	foreach ($tab['SEARCHFIELDS'] as $search_field) {
    		$field_name = $this->get_field_name($search_field, 'search');
    		$values = $this->get_values_from_field($field_name);
    		for($i=0; $i < count($values); $i++) {
    			$form .= "<input type='hidden' name='".$field_name."[]' value='".htmlentities(stripslashes($values[$i]),ENT_QUOTES,$charset)."' />";
    		}
    	
    	}
    	foreach ($tab['FILTERFIELDS'] as $filter_field) {
    		$field_name = $this->get_field_name($filter_field, 'filter');
    		$values = $this->get_values_from_field($field_name);
    		for($i=0; $i < count($values); $i++) {
    			$form .= "<input type='hidden' name='".$field_name."[]' value='".htmlentities(stripslashes($values[$i]),ENT_QUOTES,$charset)."' />";
    		}
    	}
    	$form .= "<input type='hidden' name='action' value='search' />
    			<input type='hidden' name='mode' value='".$mode."' />
    		<input type='hidden' name='page' value='".$this->page."'/>
    		</form>";
    	return $form;
    }
    
    protected function get_human_field($field, $values) {
    	global $msg, $charset;
    	
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
				$requete=$field["INPUT_OPTIONS"]["QUERY"][0]["value"];
				if (isset($field["FILTERING"]) && $field["FILTERING"] == "yes") {
					$requete = str_replace("!!acces_j!!", "", $requete);
					$requete = str_replace("!!statut_j!!", "", $requete);
					$requete = str_replace("!!statut_r!!", "", $requete);
				}
				if (isset($field["INPUT_OPTIONS"]["QUERY"][0]["USE_GLOBAL"]) && $field["INPUT_OPTIONS"]["QUERY"][0]["USE_GLOBAL"]) {
					$use_global = explode(",", $field["INPUT_OPTIONS"]["QUERY"][0]["USE_GLOBAL"]);
					for($j=0; $j<count($use_global); $j++) {
						$var_global = $use_global[$j];
						global ${$var_global};
						$requete = str_replace("!!".$var_global."!!", ${$var_global}, $requete);
					}
				}
				$resultat=pmb_mysql_query($requete);
				$opt=array();
				while ($r_=@pmb_mysql_fetch_row($resultat)) {
					$opt[$r_[0]]=$r_[1];
				}
				for ($j=0; $j<count($values); $j++) {
					if($values[$j]>=0)	$field_aff[$j]=$opt[$values[$j]]; // $opt[$values[$j]] peut etre à -1
					else $field_aff[$j]='';
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
			case "authoritie":
				for($j=0 ; $j<sizeof($values) ; $j++){
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
				break;
			default:
				$field_aff[0]=$values[0];
				break;
		}
		return '<b>'.get_msg_to_display($field['TITLE']).'</b> '.implode(' ', $field_aff);
    }
    
    protected function make_human_query($without_tags = false) {
    	global $msg;
    	global $charset;
    	
    	$human_queries = array();
    	$tab = $this->get_current_tab();
    	foreach ($tab['SEARCHFIELDS'] as $search_field) {
    		$values = $this->clean_array($this->get_values_from_field($this->get_field_name($search_field, 'search')));
    		if(is_array($values) && isset($values[0]) && ($values[0] != '')) {
    			$human_queries[] = $this->get_human_field($search_field, $values);
    		}
    	}
    	foreach ($tab['FILTERFIELDS'] as $filter_field) {
    		$values = $this->clean_array($this->get_values_from_field($this->get_field_name($filter_field, 'filter')));
    		if(is_array($values) && isset($values[0]) && ($values[0] != '')) {
    			$human_queries[] = $this->get_human_field($filter_field, $values);
    		}
    	}
    	
    	$research = implode(', ', $human_queries);
    	
    	if($this->search_nb_results) {
    		$research .= " => ".sprintf($msg["searcher_results"], $this->search_nb_results);
    	} else {
    		$research .= " => ".$msg['no_result'];
    	}
    	if ($without_tags) {
    		return $research;
    	}
    	return "<div class='othersearchinfo'>".$research."</div>";
    }
    	
    protected function pager() {
    	global $msg;
    	global $page, $nb_per_page;
    
    	if (!$this->search_nb_results) return;
    	
    	if($page) $this->page = $page;
    	$this->nbepage=ceil($this->search_nb_results/($nb_per_page ? $nb_per_page : 20));
    	$suivante = $this->page+1;
    	$precedente = $this->page-1;
    	if (!$this->page) $page_en_cours=0 ;
    	else $page_en_cours=$this->page ;
    
    	// affichage du lien précédent si nécéssaire
    	$nav_bar = '';
    	if($precedente >= 0)
    		$nav_bar .= "<a href='#' onClick=\"document.store_search.page.value=$precedente; document.store_search.submit(); return false;\"><img src='".get_url_icon('left.gif')."' style='border:0px; margin:3px 3px'  title='$msg[48]' alt='[$msg[48]]' class='align_middle'></a>";
    
    	$deb = $page_en_cours - 10 ;
    	if ($deb<0) $deb=0;
    	for($i = $deb; ($i < $this->nbepage) && ($i<$page_en_cours+10); $i++) {
    		if($i==$page_en_cours) $nav_bar .= "<strong>".($i+1)."</strong>";
    		else $nav_bar .= "<a href='#' onClick=\"document.store_search.page.value=$i; document.store_search.submit(); return false;\">".($i+1)."</a>";
    		if($i<$this->nbepage) $nav_bar .= " ";
    	}
    
    	if($suivante<$this->nbepage)
    		$nav_bar .= "<a href='#' onClick=\"document.store_search.page.value=$suivante; document.store_search.submit(); return false;\"><img src='".get_url_icon('right.gif')."' style='border:0px; margin:3px 3px' title='$msg[49]' alt='[$msg[49]]' class='align_middle'></a>";
    
    	// affichage de la barre de navigation
    	print "<div class='center'>$nav_bar</div>";
    }
    
    public function show_result() {
    	global $begin_result_liste;
    	global $end_result_liste;
    	
    	print $this->make_hidden_form();
    	print $this->make_human_query();
    	if(is_array($this->objects_ids) && count($this->objects_ids)) {
    		elements_list_ui::add_context_parameter('in_search', '1');
    		$instance_elements_list_ui = $this->get_instance_elements_list_ui();
    		$elements = $instance_elements_list_ui->get_elements_list();
    		print $begin_result_liste;
    		search_authorities::get_caddie_link();    		
    		print $elements;
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
    		$this->set_session_history($this->make_human_query(true), $tab, "QUERY");
    		print $this->show_result();
    		$this->set_session_history($this->make_human_query(true), $tab, $this->get_type(), "simple");
    	}
    }
    
    protected function clean_array($values_array){
    	global $charset;
    	$temp = array();
    	foreach($values_array as $key => $value){
    		$temp[$key] = htmlentities(stripslashes($value), ENT_QUOTES, $charset);
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
    	 
    	for ($i=0; $i<count($element['VARVIS']); $i++) {
    		$name=$element['VARVIS'][$i]["NAME"] ;
    		global ${$name};
    		$visibilite=$element['VARVIS'][$i]["VISIBILITY"] ;
    		if (isset($element['VARVIS'][$i]["VALUE"][${$name}])) {
    			if ($visibilite)
    				$test = $element['VARVIS'][$i]["VALUE"][${$name}] ;
    			else
    				$test = $visibilite || $element['VARVIS'][$i]["VALUE"][${$name}] ;
    			return $test ;
    		}
    	} // fin for
    	// aucune condition verifiee : on retourne la valeur par defaut
    	return true;
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
    			if ((string) $page == "") {
    				$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["URI"] = $this->url_target."&mode=".$tab["MODE"];
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["POST"] = $_POST;
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["GET"] = $_GET;
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["GET"]["sub"] = "";
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["POST"]["sub"] = "";
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["HUMAN_QUERY"] = $human_query;
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["HUMAN_TITLE"] = "[".$msg["authorities"]."] ".get_msg_to_display($tab["TITLE"]);
    				$_POST["page"] = 0;
    				$page = 0;
    			}
    			break;
    		case 'AUT' :
    			if ($_SESSION["CURRENT"] !== false) {
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["URI"] = $this->url_target."&mode=".$tab["MODE"]."&action=search";
    				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["PAGE"] = $page+1;
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
    	if(!$mode) {
    		$this->current_mode=$this->default_mode;
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
    			return new search_authorities('search_fields_authorities');
    			break;
    		case 'records':
    			return new search();
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
    			return new searcher_authorities_tab($values);
    			break;
    		case 'records':
    			return new searcher_records_tab($values);
    			break;
    	}
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
    			break;
    	}
    }
    
    public function get_default_selector_mode(){
    	return $this->default_selector_mode;
    }
}
?>