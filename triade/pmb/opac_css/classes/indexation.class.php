<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexation.class.php,v 1.31 2019-06-11 08:53:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/double_metaphone.class.php");
require_once($class_path."/stemming.class.php");
require_once($class_path."/authorities_collection.class.php");


//classe générique de calcul d'indexation...
class indexation {
	public static $xml_indexation =array();
	public $table_prefix ="";
	protected $type = 0;
	public $temp_not=array();
	public $temp_ext=array();
	public $temp_marc=array();
	public $champ_trouve=false;
	public $tab_code_champ = array();
	public $tab_languages=array();
	public $tab_keep_empty = array();
	public $tab_pp=array();
	public $tab_authperso=array();
	public $authperso_code_champ_start = 0;
	public $tab_authperso_link=array();
	public $authperso_link_code_champ_start = 0;
	public $isbd_ask_list=array();	
	protected $initialized = false;
	protected $queries = array();
	protected $queries_lang= array();
	protected $datatypes = array();
	protected $reference_key = "";
	protected $reference_table = "";
	protected static $marclist_languages;
	protected $marclist_instance;
	protected static $marclist_liste_mots;
	protected static $languages;
	protected static $languages_messages;
	protected static $num_words = array();
	protected $deleted_index = false;
	protected static $authpersos=array();
	protected static $parametres_perso=array();
	public $callables = array();
	
	public function __construct($xml_filepath, $table_prefix, $type = 0){
		$this->table_prefix = $table_prefix;
		$this->type = $type;
		
		//recuperation du fichier xml de configuration
		if(!isset(static::$xml_indexation[$this->type]) || !count(static::$xml_indexation[$this->type])) {
			if(!file_exists($xml_filepath)) return false;
			
			$subst_file = str_replace(".xml","_subst.xml",$xml_filepath);
			if(file_exists($subst_file)){
				$file = $subst_file;
			}else $file = $xml_filepath ;
		
			$fp=fopen($file,"r");
			if ($fp) {
				$xml=fread($fp,filesize($file));
			}
			fclose($fp);
			static::$xml_indexation[$this->type]=_parser_text_no_function_($xml,"INDEXATION",$file);
		}
	}
	
	public function get_type(){
		return $this->type;
	}
	
	public function set_type($type){
		$this->type = $type;
	}
	
	protected function init(){
		$this->temp_not=array();
		$this->temp_ext=array();
		$this->temp_marc=array();
		$this->champ_trouve=false;
		$this->tab_code_champ = array();
		$this->tab_languages=array();
		$this->tab_keep_empty = array();
		$this->tab_pp=array();
		$this->tab_authperso_link=array();
		$this->authperso_link_code_champ_start = 0;
		$this->isbd_ask_list=array();	
		$this->reference_key = static::$xml_indexation[$this->type]['REFERENCEKEY'][0]['value'];
		$this->reference_table = static::$xml_indexation[$this->type]['REFERENCE'][0]['value'];
		$this->callables = array();
		
		for ($i=0;$i<count(static::$xml_indexation[$this->type]['FIELD']);$i++) { //pour chacun des champs decrits
			if(!isset(static::$xml_indexation[$this->type]['FIELD'][$i]['DATATYPE'])){
				$datatype = "undefined";
			} else {
				$datatype = static::$xml_indexation[$this->type]['FIELD'][$i]['DATATYPE'];
			}
			$this->datatypes[$datatype][] = static::$xml_indexation[$this->type]['FIELD'][$i]['ID'];
			
			//recuperation de la liste des informations a mettre a jour
			//conservation des mots vides
			if(isset(static::$xml_indexation[$this->type]['FIELD'][$i]['KEEPEMPTYWORD']) && static::$xml_indexation[$this->type]['FIELD'][$i]['KEEPEMPTYWORD'] == "yes"){
				$this->tab_keep_empty[]=static::$xml_indexation[$this->type]['FIELD'][$i]['ID'];
			}
			//champ perso
			if(isset(static::$xml_indexation[$this->type]['FIELD'][$i]['DATATYPE']) && static::$xml_indexation[$this->type]['FIELD'][$i]['DATATYPE'] == "custom_field"){
				$this->tab_pp[static::$xml_indexation[$this->type]['FIELD'][$i]['ID']]=static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['value'];
			//autorité perso
			}else if(isset(static::$xml_indexation[$this->type]['FIELD'][$i]['DATATYPE']) && static::$xml_indexation[$this->type]['FIELD'][$i]['DATATYPE'] == "authperso"){
				$this->tab_authperso[static::$xml_indexation[$this->type]['FIELD'][$i]['ID']]=static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['value'];	
				$this->authperso_code_champ_start=static::$xml_indexation[$this->type]['FIELD'][$i]['ID'];
			}else if(isset(static::$xml_indexation[$this->type]['FIELD'][$i]['DATATYPE']) && static::$xml_indexation[$this->type]['FIELD'][$i]['DATATYPE'] == "authperso_link"){
				$this->tab_authperso_link[static::$xml_indexation[$this->type]['FIELD'][$i]['ID']]=static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['value'];
				$this->authperso_link_code_champ_start = static::$xml_indexation[$this->type]['FIELD'][$i]['ID'];
			}else if (isset(static::$xml_indexation[$this->type]['FIELD'][$i]['EXTERNAL']) && (static::$xml_indexation[$this->type]['FIELD'][$i]['EXTERNAL'] == "yes")) {
				//champ externe à la table
				//Stockage de la structure pour un accès plus facile
				$this->temp_ext[static::$xml_indexation[$this->type]['FIELD'][$i]['ID']] = static::$xml_indexation[$this->type]['FIELD'][$i];
			} else if (isset(static::$xml_indexation[$this->type]['FIELD'][$i]['CALLABLE'])) {
				// Callables
				$this->callables[static::$xml_indexation[$this->type]['FIELD'][$i]['ID']] = array();
				for ($j = 0; $j < count(static::$xml_indexation[$this->type]['FIELD'][$i]['CALLABLE']); $j++) {
					$this->callables[static::$xml_indexation[$this->type]['FIELD'][$i]['ID']][] = array(
							'champ' => static::$xml_indexation[$this->type]['FIELD'][$i]['ID'],
							'ss_champ' => static::$xml_indexation[$this->type]['FIELD'][$i]['CALLABLE'][$j]['ID'],
							'pond' => static::$xml_indexation[$this->type]['FIELD'][$i]['CALLABLE'][$j]['POND'],
							'class_path' => static::$xml_indexation[$this->type]['FIELD'][$i]['CALLABLE'][$j]['CLASS_PATH'],
							'class_name' => static::$xml_indexation[$this->type]['FIELD'][$i]['CALLABLE'][$j]['CLASS_NAME'],
							'method' => static::$xml_indexation[$this->type]['FIELD'][$i]['CALLABLE'][$j]['METHOD'],
							'parameters' => static::$xml_indexation[$this->type]['FIELD'][$i]['CALLABLE'][$j]['PARAMETERS']
					);
				}
			} else {
				//champ de la table
				$this->temp_not['f'][0][static::$xml_indexation[$this->type]['FIELD'][$i]['ID']] = static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value'];
				$this->tab_code_champ[0][static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value']] = array(
						'champ' => static::$xml_indexation[$this->type]['FIELD'][$i]['ID'],
						'ss_champ' => 0,
						'pond' => static::$xml_indexation[$this->type]['FIELD'][$i]['POND'],
						'no_words' => (isset(static::$xml_indexation[$this->type]['FIELD'][$i]['DATATYPE']) && static::$xml_indexation[$this->type]['FIELD'][$i]['DATATYPE'] == "marclist" ? true : false),
						'internal' => 1,
						'use_global_separator' => (isset(static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['USE_GLOBAL_SEPARATOR']) ?static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['USE_GLOBAL_SEPARATOR'] : '')
				);
				if(isset(static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['MARCTYPE'])){
					$this->tab_code_champ[0][static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value']]['marctype']=static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['MARCTYPE'];
					$this->temp_not['f'][0][static::$xml_indexation[$this->type]['FIELD'][$i]['ID']."_marc"]=static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['value']." as "."subst_for_marc_".static::$xml_indexation[$this->type]['FIELD'][$i]['TABLE'][0]['TABLEFIELD'][0]['MARCTYPE'];
				}
			}
			if(isset(static::$xml_indexation[$this->type]['FIELD'][$i]['ISBD'])){ // isbd autorités
				$this->isbd_ask_list[static::$xml_indexation[$this->type]['FIELD'][$i]['ID']]= array(
						'champ' => static::$xml_indexation[$this->type]['FIELD'][$i]['ID'],
						'ss_champ' => static::$xml_indexation[$this->type]['FIELD'][$i]['ISBD'][0]['ID'],
						'pond' => static::$xml_indexation[$this->type]['FIELD'][$i]['ISBD'][0]['POND'],
						'class_name' => static::$xml_indexation[$this->type]['FIELD'][$i]['ISBD'][0]['CLASS_NAME']
				);
			}
			$this->champ_trouve=true;
		}
		
		foreach($this->temp_ext as $k=>$v) {
			$isbd_tab_req=array();
			$no_word_field=false;
			//Construction de la requete
			//Champs pour le select
			$select=array();
			//on harmonise les fichiers XML décrivant des requetes...
			for ($i = 0; $i<count($v["TABLE"]); $i++) {
				$table = $v['TABLE'][$i];
				$select=array();
				if(count($table['TABLEFIELD'])){
					$use_word=true;
				}else{
					$use_word=false;
				}
				if(isset($table['IDKEY'][0])){
					$select[]=(isset($table['IDKEY'][0]['ALIAS'])?$table['IDKEY'][0]['ALIAS']:$table['NAME']).".".$table['IDKEY'][0]['value']." as subst_for_autorite_".$table['IDKEY'][0]['value'];
				}
				for ($j=0;$j<count($table['TABLEFIELD']);$j++) {
					$select[]=((isset($table['ALIAS']) && (strpos($table['TABLEFIELD'][$j]["value"],".")=== false)) ? $table['ALIAS']."." : "").$table['TABLEFIELD'][$j]["value"];
					if(isset($table['LANGUAGE'])){
						$select[]=$table['LANGUAGE'][0]['value'].(isset($table['LANGUAGE'][0]['ALIAS']) ? ' as '.$table['LANGUAGE'][0]['ALIAS'] : '');
						$this->tab_languages[$k]=(isset($table['LANGUAGE'][0]['ALIAS']) ? $table['LANGUAGE'][0]['ALIAS'] : $table['LANGUAGE'][0]['value']);
					}
					$field_name = $table['TABLEFIELD'][$j]["value"];
					if(strpos(strtolower($table['TABLEFIELD'][$j]["value"])," as ")!== false){ //Pour le cas où l'on a besoin de nommer un champ et d'utiliser un alias
						$field_name = substr($table['TABLEFIELD'][$j]["value"],strpos(strtolower($table['TABLEFIELD'][$j]["value"])," as ")+4);
					}elseif(strpos($table['TABLEFIELD'][$j]["value"],".")!== false){
						$field_name = substr($table['TABLEFIELD'][$j]["value"],strpos($table['TABLEFIELD'][$j]["value"],".")+1);
					}
					$field_name=trim($field_name);
					$this->tab_code_champ[$v['ID']][$field_name] = array(
							'champ' => $v['ID'],
							'ss_champ' => $table['TABLEFIELD'][$j]["ID"],
							'pond' => $table['TABLEFIELD'][$j]['POND'],
							'no_words' => (isset($v['DATATYPE']) && $v['DATATYPE'] == "marclist" ? true : false),
							'autorite' =>  (isset($table['IDKEY'][0]['value']) ? $table['IDKEY'][0]['value'] : '')
					);
					if(isset($table['TABLEFIELD'][$j]['MARCTYPE'])){
						$this->tab_code_champ[$v['ID']][$table['TABLEFIELD'][$j]["value"]]['marctype']=$table['TABLEFIELD'][$j]['MARCTYPE'];
						$select[]=(strpos($table['TABLEFIELD'][$j]["value"],".")=== false ? $table['NAME']."." : "").$table['TABLEFIELD'][$j]["value"]." as subst_for_marc_".$table['TABLEFIELD'][$j]['MARCTYPE'];
					}
				}
				$query="select ".implode(",",$select)." from ".$this->reference_table;
				$jointure="";
				for( $j=0 ; $j<count($table['LINK']) ; $j++){
						
					$link = $table['LINK'][$j];
		
					if(isset($link["TABLE"][0]['ALIAS'])){
						$alias = $link["TABLE"][0]['ALIAS'];
					}else{
						$alias = (isset($link["TABLE"][0]['value']) ? $link["TABLE"][0]['value'] : '');
					}
					if(!isset($link["LINKRESTRICT"][0]['value'])) {
						$link["LINKRESTRICT"][0]['value'] = '';
					}
					switch ($link["TYPE"]) {
						case "n0" :
							if (isset($link["TABLEKEY"][0]['value'])) {
								$jointure .= " LEFT JOIN " . $link["TABLE"][0]['value'].($link["TABLE"][0]['value'] != $alias  ? " AS ".$alias : "");
								if(isset($link["EXTERNALTABLE"][0]['value'])){
									$jointure .= " ON " . $link["EXTERNALTABLE"][0]['value'] . "." . $link["EXTERNALFIELD"][0]['value'];
								}else{
									$jointure .= " ON " . (isset($table['ALIAS'])? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value'];
								}
								$jointure .= "=" . $alias . "." . $link["TABLEKEY"][0]['value']. " ".$link["LINKRESTRICT"][0]['value'];
							} else {
								$jointure .= " LEFT JOIN " . $table['NAME'] . (isset($table['ALIAS'])? " as ".$table['ALIAS'] :"");
								$jointure .= " ON " . $this->reference_table . "." . $this->reference_key;
								$jointure .= "=" . (isset($table['ALIAS'])? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value']. " ".$link["LINKRESTRICT"][0]['value'];
							}
							break;
						case "n1" :
							if (isset($link["TABLEKEY"][0]['value'])) {
								$jointure .= " JOIN " . $link["TABLE"][0]['value'].($link["TABLE"][0]['value'] != $alias  ? " AS ".$alias : "");
								if(isset($link["EXTERNALTABLE"][0]['value'])){
									$jointure .= " ON " . $link["EXTERNALTABLE"][0]['value'] . "." . $link["EXTERNALFIELD"][0]['value'];
								}else{
									$jointure .= " ON " . (isset($table['ALIAS'])? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value'];
								}
								$jointure .= "=" . $alias . "." . $link["TABLEKEY"][0]['value']. " ".$link["LINKRESTRICT"][0]['value'];
							} else {
								$jointure .= " JOIN " . $table['NAME'] . (isset($table['ALIAS'])? " as ".$table['ALIAS'] :"");
								$jointure .= " ON " . $this->reference_table . "." . $this->reference_key;
								$jointure .= "=" . (isset($table['ALIAS'])? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value']. " ".$link["LINKRESTRICT"][0]['value'];
							}
							break;
						case "1n" :
							$jointure .= " JOIN " . $table['NAME'] . (isset($table['ALIAS'])? " as ".$table['ALIAS'] :"");
							$jointure .= " ON (" . (isset($table['ALIAS'])? $table['ALIAS'] : $table['NAME']) . "." . $table["TABLEKEY"][0]['value'];
							$jointure .= "=" . $this->reference_table . "." . $link["REFERENCEFIELD"][0]['value'] . " ".$link["LINKRESTRICT"][0]['value']. ") ";
								
								
							break;
						case "nn" :
							$jointure .= " JOIN " . $link["TABLE"][0]['value'].($link["TABLE"][0]['value'] != $alias  ? " AS ".$alias : "");
							$jointure .= " ON (" . $this->reference_table . "." .  $this->reference_key;
							$jointure .= "=" . $alias . "." . $link["REFERENCEFIELD"][0]['value'] . ") ";
							if (isset($link["TABLEKEY"][0]['value'])) {
								$jointure .= " JOIN " . $table['NAME'] . (isset($table['ALIAS'])? " as ".$table['ALIAS'] :"");
								$jointure .= " ON (" . $alias . "." . $link["TABLEKEY"][0]['value'];
								$jointure .= "=" . (isset($table['ALIAS'])? $table['ALIAS'] : $table['NAME']) . "." . $link["EXTERNALFIELD"][0]['value'] ." ".$link["LINKRESTRICT"][0]['value']. ") ";
							} else {
								if(isset($link['LINK'][0])) {
									$current_link = $link;
									do {
										$jointure .= self::get_indexation_sub_join($current_link);
										$link = $current_link;
										$current_link = $current_link['LINK'][0];
									} while (isset($current_link['LINK'][0]) && $current_link['LINK'][0]);
									$jointure .= " JOIN " . $table['NAME'] . (isset($table['ALIAS'])? " as ".$table['ALIAS'] :"");
									$jointure .= " ON (" . (isset($link['LINK'][0]['TABLE'][0]['ALIAS']) ? $link['LINK'][0]['TABLE'][0]['ALIAS'] : $link['LINK'][0]['TABLE'][0]['value']) . "." . $link['LINK'][0]['EXTERNALFIELD'][0]['value'];
									$jointure .= "=" . (isset($table['ALIAS'])? $table['ALIAS'] : $table['NAME']) . "." . $table["TABLEKEY"][0]['value'] . " ".(isset($link['LINK'][0]["LINKRESTRICT"][0]['value']) ? $link['LINK'][0]["LINKRESTRICT"][0]['value'] : '').") ";
								} else {
									$jointure .= " JOIN " . $table['NAME'] . (isset($table['ALIAS'])? " as ".$table['ALIAS'] :"");
									$jointure .= " ON (" . $alias . "." . $link["EXTERNALFIELD"][0]['value'];
									$jointure .= "=" . (isset($table['ALIAS'])? $table['ALIAS'] : $table['NAME']) . "." . $table["TABLEKEY"][0]['value'] . " ".$link["LINKRESTRICT"][0]['value'].") ";
								}
							}
							break;
					}
				}
				$where=" where ".$this->reference_table.".".$this->reference_key."=!!object_id!!";
				if(isset($table['FILTER'])){
					foreach ( $table['FILTER'] as $filter ) {
						if($tmp=trim($filter["value"])){
							$where.=" AND (".$tmp.")";
						}
					}
				}
				if(isset($table['LANGUAGE'])){
					$this->queries_lang[$k]= "select ".$table['LANGUAGE'][0]['value'].(isset($table['LANGUAGE'][0]['ALIAS']) ? ' as '.$table['LANGUAGE'][0]['ALIAS'] : '')." from ";
				}
				$query.=$jointure.$where;
				if(isset($table['LANGUAGE'])){
					$this->queries_lang[$k].=$jointure.$where;
				}
				if($use_word){
					$this->queries[$k]["new_rqt"]['rqt'][]=$query;
				}
				if(isset($this->isbd_ask_list[$k])){ // isbd  => memo de la requete pour retrouver les id des autorités
					if(isset($table['ALIAS'])){
						$id_aut=$table['ALIAS'].".".$table["TABLEKEY"][0]['value'];
					} else {
						$id_aut=$table['NAME'].".".$table["TABLEKEY"][0]['value'];
					}
					$req="select $id_aut as id_aut_for_isbd from ".$this->reference_table.$jointure.$where;
					$isbd_tab_req[]=$req;
				}
		
			}
			if($use_word){
				$this->queries[$k]["rqt"] = implode(" union ",$this->queries[$k]["new_rqt"]['rqt']);
			}
			if(isset($this->isbd_ask_list[$k])){ // isbd  => memo de la requete pour retrouver les id des autorités
				$req=implode(" union ",$isbd_tab_req);
				$this->isbd_ask_list[$k]['req']=  $req;
			}
		}
		$this->initialized = true;
	}
		
	protected function get_indexation_lang() {
		//il existe une spécificité pour les notices (langue d'indexation) - classe dérivée
		return "";
	}
	
	protected function get_languages() {
		global $opac_show_languages;
		
		$languages = array();
		$query_languages = "select distinct user_lang from users";
		$result_languages = pmb_mysql_query($query_languages);
		while ($row_languages = pmb_mysql_fetch_object($result_languages)) {
			$languages[] = $row_languages->user_lang;
		}
		$query_languages = "select distinct empr_lang from empr";
		$result_languages = pmb_mysql_query($query_languages);
		while ($row_languages = pmb_mysql_fetch_object($result_languages)) {
			$languages[] = $row_languages->empr_lang;
		}
		$opac_languages = explode(' ', $opac_show_languages);
		if(isset($opac_languages[1])) {
			$exploded = explode(',', $opac_languages[1]);
			foreach ($exploded as $value) {
				if(trim($value)) {
					$languages[] = trim($value);
				}
			}
		}
		$languages = array_values(array_unique($languages));
		return $languages;
	}
	
	public function maj($object_id,$datatype='all'){	
		global $dbh, $lang, $include_path, $charset;
		
		//on s'assure qu'on a lu le XML et initialisé ce qu'il faut...
		if(!$this->initialized) {
			$this->init();
		}
		
		//on réinitialise les tableaux d'injection
		$tab_insert=array();
		$tab_field_insert=array();
		
		
		//on a des éléments à indexer...
		if ($this->champ_trouve) {
			//Recherche des champs directs
			if(($datatype=='all') && isset($this->temp_not['f']) && count($this->temp_not['f'])) {
				$this->queries[0]["rqt"]= "select ".implode(',',$this->temp_not['f'][0])." from ".$this->reference_table;
				$this->queries[0]["rqt"].=" where ".$this->reference_key."='".$object_id."'";
				$this->queries[0]["table"]=$this->reference_table;
			}
			//qu'est-ce qu'on efface?
			if(!$this->deleted_index) {
				$this->delete_index($object_id, $datatype);
			}
			
			//qu'est-ce qu'on met a jour ?
			$tab_insert=array();
			$tab_field_insert=array();
			
			foreach($this->queries as $k=>$v) {
				if($datatype == 'all' || (isset($this->datatypes[$datatype]) && in_array($k,$this->datatypes[$datatype]))){
				
					$v['rqt'] = str_replace("!!object_id!!",$object_id,$v['rqt']);
					$r=pmb_mysql_query($v["rqt"],$dbh) or die("Requete echouee.");
			
					$tab_mots=array();
					$tab_fields=array();
					if (pmb_mysql_num_rows($r)) {
						while(($tab_row=pmb_mysql_fetch_array($r,PMB_MYSQL_ASSOC))) {
							$langage="";
							if(isset($this->tab_languages[$k]) && isset($tab_row[$this->tab_languages[$k]])){
								switch ($tab_row[$this->tab_languages[$k]]) {
									case "fr" :
										$tab_row[$this->tab_languages[$k]] = 'fr_FR';
										break;
									case "en" :
										$tab_row[$this->tab_languages[$k]] = 'en_UK';
								}
								$langage = $tab_row[$this->tab_languages[$k]];
								unset($tab_row[$this->tab_languages[$k]]);
							}
							foreach($tab_row as $nom_champ => $liste_mots) {
								if(substr($nom_champ,0,10)=='subst_for_'){
									continue;
								}
								if(isset($this->tab_code_champ[$k][$nom_champ]['internal']) && $this->tab_code_champ[$k][$nom_champ]['internal']){
									$langage=$this->get_indexation_lang();
								}
								if(isset($this->tab_code_champ[$k][$nom_champ]['marctype']) && $this->tab_code_champ[$k][$nom_champ]['marctype']){
									//on veut toutes les langues, pas seulement celle de l'interface...
									$saved_lang = $lang;
									$code = $liste_mots;
									if($code){
										if(!isset(static::$marclist_languages)) {
 											static::$marclist_languages = $this->get_languages();
										}
										foreach (static::$marclist_languages as $language) {
											$lang = $language;
											if(!isset($this->marclist_instance[$lang][$this->tab_code_champ[$k][$nom_champ]['marctype']])) {
												$this->marclist_instance[$lang][$this->tab_code_champ[$k][$nom_champ]['marctype']] = new marc_list($this->tab_code_champ[$k][$nom_champ]['marctype']);
											}
											if(isset($this->marclist_instance[$lang][$this->tab_code_champ[$k][$nom_champ]['marctype']]->table[$code])) {
												$liste_mots = $this->marclist_instance[$lang][$this->tab_code_champ[$k][$nom_champ]['marctype']]->table[$code];
												$tab_fields[$nom_champ][] = array(
														'value' => trim($liste_mots),
														'lang' => $lang,
														'autorite' => $tab_row["subst_for_marc_".$this->tab_code_champ[$k][$nom_champ]['marctype']]
												);
												//Etait présent dans la méthode d'indexation de la classe notice
												if(static::class == 'indexation_record') {
													
													//gestion de la recherche tous champs pour les marclist
													if(!isset(static::$marclist_liste_mots[$liste_mots])) {
														$tab_tmp=array();
														$liste_mots = strip_tags($liste_mots);
														if(!in_array($k,$this->tab_keep_empty)){
															$tab_tmp=explode(' ',strip_empty_words($liste_mots));
														}else{
															$tab_tmp=explode(' ',strip_empty_chars(clean_string($liste_mots)));
														}
														static::$marclist_liste_mots[$liste_mots] = $tab_tmp;
													}
													foreach(static::$marclist_liste_mots[$liste_mots] as $mot) {
														if(trim($mot)){
															$langageKey = $langage;
															if (!trim($langageKey)) {
																$langageKey = "empty";
															}
															$tab_mots[$nom_champ][$lang][]=$mot;
														}
													}
												}
											}
										}
									}
									$lang = $saved_lang;
									$liste_mots = "";
								}
								if(substr($liste_mots, 0, 4) == "msg:") {
									//on veut toutes les langues, pas seulement celle de l'interface...
									$code = substr($liste_mots, 4);
									if(!isset(static::$languages)) {
										$langues = new XMLlist($include_path."/messages/languages.xml");
										$langues->analyser();
										static::$languages = array_intersect_key(array_flip($this->get_languages()), $langues->table);
									}
									foreach(static::$languages as $cle=>$value){
										// arabe seulement si on est en utf-8
										if (($charset != 'utf-8' and $lang != 'ar') or ($charset == 'utf-8')) {
											if(!isset(static::$languages_messages[$cle])) {
												$messages_instance = new XMLlist($include_path."/messages/".$cle.".xml");
												$messages_instance->analyser();
												static::$languages_messages[$cle] = $messages_instance->table;
											}
											$liste_mots = static::$languages_messages[$cle][$code];
											$tab_fields[$nom_champ][] = array(
													'value' => trim($liste_mots),
													'lang' => $cle
											);
										}
									}
									$liste_mots = "";
								}
								if($liste_mots!='') {
									$tab_tmp=array();
									$liste_mots = strip_tags($liste_mots);
									if(!in_array($k,$this->tab_keep_empty)){
										$tab_tmp=explode(' ',strip_empty_words($liste_mots));
									}else{
										$tab_tmp=explode(' ',strip_empty_chars(clean_string($liste_mots)));
									}
									//	if($lang!="") $tab_tmp[]=$lang;
									//la table pour les recherche exacte
									if(!isset($tab_fields[$nom_champ])) $tab_fields[$nom_champ]=array();
									if(!isset($this->tab_code_champ[$k][$nom_champ]['use_global_separator']) || !($this->tab_code_champ[$k][$nom_champ]['use_global_separator'])){
										$tab_fields[$nom_champ][] = array(
												'value' =>trim($liste_mots),
												'lang' => $langage,
												'autorite' => (isset($this->tab_code_champ[$k][$nom_champ]['autorite']) && isset($tab_row["subst_for_autorite_".$this->tab_code_champ[$k][$nom_champ]['autorite']]) ? $tab_row["subst_for_autorite_".$this->tab_code_champ[$k][$nom_champ]['autorite']] : '0')
										);
									} else {
										$var_global_sep = $this->tab_code_champ[$k][$nom_champ]['use_global_separator'];
										global ${$var_global_sep};
										$tab_liste_mots = explode(${$var_global_sep},$liste_mots);
										if(count($tab_liste_mots)){
											foreach ($tab_liste_mots as $mot) {
												$tab_fields[$nom_champ][] = array(
														'value' =>trim($mot),
														'lang' => $langage,
														'autorite' => (isset($this->tab_code_champ[$k][$nom_champ]['autorite']) && isset($tab_row["subst_for_autorite_".$this->tab_code_champ[$k][$nom_champ]['autorite']]) ? $tab_row["subst_for_autorite_".$this->tab_code_champ[$k][$nom_champ]['autorite']] : '0')
												);
											}
										}
									}
									if(!$this->tab_code_champ[$k][$nom_champ]['no_words']){
										foreach($tab_tmp as $mot) {
											if(trim($mot)){
												$langageKey = $langage;
												if (!trim($langageKey)) {
													$langageKey = "empty";
												}
												$tab_mots[$nom_champ][$langageKey][]=$mot;
											}
										}
									}
								}
							}
						}
					}
					foreach ($tab_mots as $nom_champ=>$tab) {
						$memo_ss_champ="";
						$order_fields=1;
						$pos=1;
						foreach ( $tab as $langage => $mots ) {
							if ($langage == "empty") {
								$langage = "";
							}
							foreach ($mots as $mot) {
								$num_word = indexation::add_word($mot, $langage);
								if($num_word != 0){
									$tab_insert[] = $this->get_tab_insert($object_id, $this->tab_code_champ[$k][$nom_champ], $num_word, $order_fields, $pos);
									$pos++;
									if($this->tab_code_champ[$k][$nom_champ]['ss_champ']!= $memo_ss_champ) $order_fields++;
									$memo_ss_champ=$this->tab_code_champ[$k][$nom_champ]['ss_champ'];
								}
							}
						}
					}
					//la table pour les recherche exacte
					foreach ($tab_fields as $nom_champ=>$tab) {
						foreach($tab as $order => $values){
							$tab_field_insert[] = $this->get_tab_field_insert($object_id, $this->tab_code_champ[$k][$nom_champ], $order+1, $values['value'], $values['lang'], $values['autorite']);
						}
					}
					
				}
			}
			
			// Les champs perso
			if(count($this->tab_pp) && ($datatype == 'all' || (isset($this->datatypes[$datatype]) && in_array('custom_field',$this->datatypes[$datatype])))){
				foreach ( $this->tab_pp as $code_champ => $table ) {
					$p_perso = $this->get_parametres_perso_class($table);
					//on doit retrouver l'id des eléments...
					$ids = array();
					switch($table){
						case "expl" :
							$rqt = "select expl_id from notices join exemplaires on expl_notice = notice_id and expl_notice!=0 where notice_id = ".$object_id." union select expl_id from notices join bulletins on num_notice = notice_id join exemplaires on expl_bulletin = bulletin_id and expl_bulletin != 0 where notice_id = ".$object_id;
							$res = pmb_mysql_query($rqt);
							if(pmb_mysql_num_rows($res)) {
								while($row= pmb_mysql_fetch_object($res)){
									$ids[] =$row->expl_id;
								}
							}
							break;
						default :
							$ids = array($object_id);
					}
					if(count($ids)){
						for($i=0 ; $i<count($ids) ; $i++) {
							$data=$p_perso->get_fields_recherche_mot_array($ids[$i]);
							$j=0;
							$order_fields=1;
							foreach ( $data as $code_ss_champ => $value ) {
								$tab_mots=array();
								//la table pour les recherche exacte
								$infos = array(
										'champ' => $code_champ,
										'ss_champ' => $code_ss_champ,
										'pond' => $p_perso->get_pond($code_ss_champ)
								);
								foreach($value as $val) {
									//Elimination des balises HTML - Y compris celles mal formées
									$val = preg_replace('#<[^>]+>#','',$val);
									if($val != ''){
										$tab_field_insert[] = $this->get_tab_field_insert($object_id, $infos, $j, $val);
										$j++;
										$tab_tmp=explode(' ',strip_empty_words($val));
										foreach($tab_tmp as $mot) {
											if(trim($mot)){
												$tab_mots[$mot]= "";
											}
										}
									}
								}
								$pos=1;
								foreach ( $tab_mots as $mot => $langage ) {
									$num_word = indexation::add_word($mot, $langage);
									$infos = array(
											'champ' => $code_champ,
											'ss_champ' => $code_ss_champ,
											'pond' => $p_perso->get_pond($code_ss_champ)
									);
									$tab_insert[] = $this->get_tab_insert($object_id, $infos, $num_word, $order_fields, $pos);
									$pos++;
								}
								$order_fields++;
							}
						}
					}
				}
			}
		
			//Les autorités perso
			if(count($this->tab_authperso) && ($datatype == 'all' || (isset($this->datatypes[$datatype]) && in_array('authperso',$this->datatypes[$datatype])))){
				$order_fields=1;
				$authpersos = new authperso_notice($object_id);
				$index_fields=$authpersos->get_index_fields($object_id);
				foreach ( $index_fields as $code_champ => $auth ) {
					$code_champ+=$this->authperso_code_champ_start;
					foreach ($auth['ss_champ'] as $ss_field){
						$j=1;
						foreach ($ss_field as $code_ss_champ =>$val){
							//Elimination des balises HTML - Y compris celles mal formées
							$val = preg_replace('#<[^>]+>#','',$val);
							$infos = array(
									'champ' => $code_champ,
									'ss_champ' => $code_ss_champ,
									'pond' => (isset($auth['pond']) ? $auth['pond'] : 0)
							);
							$tab_field_insert[] = $this->get_tab_field_insert($object_id, $infos, $j, $val);
							$j++;
							$tab_mots=array();
							$tab_tmp=explode(' ',strip_empty_words($val));
							foreach($tab_tmp as $mot) {
								if(trim($mot)){
									$tab_mots[$mot]= "";
								}
							}
							$pos=1;
							foreach ( $tab_mots as $mot => $langage ) {
								$num_word = indexation::add_word($mot, $langage);
								if($num_word != 0){
									$infos = array(
											'champ' => $code_champ,
											'ss_champ' => $code_ss_champ,
											'pond' => (isset($auth['pond']) ? $auth['pond'] : 0)
									);
									$tab_insert[] = $this->get_tab_insert($object_id, $infos, $num_word, $order_fields, $pos);
									$pos++;
								}
							}
							$order_fields++;
						}
					}
				}
			}
			
			// Les autorités liées
			if(count($this->tab_authperso_link) && ($datatype == 'all' || in_array('authperso_link',$this->datatypes[$datatype]))){
				$authority_type = 0;
				switch ($this->reference_table){
					case 'authors':
						$authority_type = AUT_TABLE_AUTHORS;
						break;
					case 'publishers':
						$authority_type = AUT_TABLE_PUBLISHERS;
						break;
					case 'indexint':
						$authority_type = AUT_TABLE_INDEXINT;
						break;
					case 'collections':
						$authority_type = AUT_TABLE_COLLECTIONS;
						break;
					case 'sub_collections':
						$authority_type = AUT_TABLE_SUB_COLLECTIONS;
						break;
					case 'series':
						$authority_type = AUT_TABLE_SERIES;
						break;
					case 'noeuds':
						$authority_type = AUT_TABLE_CATEG;
						break;
					case 'titres_uniformes':
						$authority_type = AUT_TABLE_TITRES_UNIFORMES;
						break;
				}
				$query = "SELECT id_authperso_authority, authperso_authority_authperso_num
					FROM ".$this->reference_table." 
					JOIN aut_link ON (".$this->reference_table.".".$this->reference_key."=aut_link.aut_link_from_num and aut_link_from = ".$authority_type." or (".$this->reference_table.".".$this->reference_key." = aut_link_to_num and aut_link_to = ".$authority_type." ))  
					JOIN authperso_authorities ON (aut_link.aut_link_to_num=authperso_authorities.id_authperso_authority or ( aut_link_from_num=authperso_authorities.id_authperso_authority )) 
					WHERE ".$this->reference_table.".".$this->reference_key."=".$object_id." AND ((aut_link.aut_link_to > 1000))";
				$result = pmb_mysql_query($query,$dbh);
				while(($row=pmb_mysql_fetch_object($result))) {
					$authperso = $this->get_authperso_class($row->authperso_authority_authperso_num);
					$index_fields = array();
					$infos_fields = $authperso->get_info_fields($row->id_authperso_authority);
					foreach($infos_fields as $field){
						if($field['search'] ){
							$index_fields[$field['code_champ']]['pond']=$field['pond'];
							if($field['all_format_values'])
								$index_fields[$field['code_champ']]['ss_champ'][][$field['code_ss_champ']].=$field['all_format_values'];
						}
					}
					foreach ( $index_fields as $code_champ => $auth ) {
						$order_fields=1;
						$code_champ+=$this->authperso_link_code_champ_start;
						foreach ($auth['ss_champ'] as $ss_field){
							$j=1;
							foreach ($ss_field as $code_ss_champ =>$val){
								$infos = array(
										'champ' => $code_champ,
										'ss_champ' => $code_ss_champ,
										'pond' => $auth['pond']
								);
								$tab_field_insert[] = $this->get_tab_field_insert($object_id,$infos,$j,$val);
								$j++;
								$tab_mots=array();
								$tab_tmp=explode(' ',strip_empty_words($val));
								foreach($tab_tmp as $mot) {
									if(trim($mot)){
										$tab_mots[$mot]= "";
									}
								}
								$pos=1;
								foreach ( $tab_mots as $mot => $langage ) {
									$num_word = indexation::add_word($mot, $langage);
									if($num_word != 0){
										$infos = array(
												'champ' => $code_champ,
												'ss_champ' => $code_ss_champ,
												'pond' => $auth['pond']
										);
										$tab_insert[] = $this->get_tab_insert($object_id, $infos, $num_word, $order_fields, $pos);
										$pos++;
									}
								}
								$order_fields++;
							}
						}
					}
				}
			}
			
			if(count($this->isbd_ask_list)){
				// Les isbd d'autorités
				foreach($this->isbd_ask_list as $k=>$infos){
					$isbd_s=array(); // cumul des isbd
					if($datatype == "all" || (isset($this->datatypes[$datatype]) && in_array($k,$this->datatypes[$datatype]))){
						$query = str_replace("!!object_id!!",$object_id,$infos["req"]);
						$res = pmb_mysql_query($query) or die($query);
						if(pmb_mysql_num_rows($res)) {
							switch ($infos["class_name"]){
								case 'author':
								case 'publisher':
								case 'collection':
								case 'subcollection':
								case 'indexint':
								case 'serie':
								case 'titre_uniforme':
									while($row= pmb_mysql_fetch_object($res)){
										$aut= authorities_collection::get_authority($infos["class_name"], $row->id_aut_for_isbd);
										$isbd_s[]=$aut->get_isbd();
									}
									break;
								case 'categories':
									while($row= pmb_mysql_fetch_object($res)){
										$aut=new categories($row->id_aut_for_isbd,$lang,true);
										$isbd_s[]=$aut->libelle_categorie;
									}
									break;
								case 'authperso':
									while($row= pmb_mysql_fetch_object($res)){
										$isbd_s[]=authperso::get_isbd($row->id_aut_for_isbd);
									}
									break;
							}
						}
					}
					$order_fields = 1;
					for($i=0 ; $i<count($isbd_s) ; $i++) {
						$tab_mots=array();
						$tab_field_insert[] = $this->get_tab_field_insert($object_id,$infos,$order_fields,$isbd_s[$i]);
					
						$tab_tmp=explode(' ',strip_empty_words($isbd_s[$i]));
						foreach($tab_tmp as $mot) {
							if(trim($mot)){
								$tab_mots[$mot]= "";
							}
						}
						$pos=1;
						foreach ( $tab_mots as $mot => $langage ) {
							$num_word = indexation::add_word($mot, $langage);
							if($num_word != 0){
								$tab_insert[] = $this->get_tab_insert($object_id, $infos, $num_word, $order_fields, $pos);
								$pos++;
							}
						}
						$order_fields++;
					}
				}
			}
			if (count($this->callables)) {
				foreach ($this->callables as $k => $callable_data) {
					for ($i = 0; $i < count($callable_data); $i++) {
						if (!file_exists($callable_data[$i]['class_path'])) {
							continue;
						}
						require_once($callable_data[$i]['class_path']);
						$callback_parameters = array($object_id);
						if (!empty($callable_data[$i]['parameters'])) {
							$callback_parameters = array_merge($callback_parameters, explode(',', $callable_data[$i]['parameters']));
						}
						$callback_return = call_user_func_array(array($callable_data[$i]['class_name'], $callable_data[$i]['method']), $callback_parameters);
						
						$order_fields = 1;
						for($j=0 ; $j<count($callback_return) ; $j++) {
							$tab_mots = array();
							$tab_field_insert[] = $this->get_tab_field_insert($object_id, $callable_data[$i], $order_fields, $callback_return[$j]);
								
							$tab_tmp = explode(' ', strip_empty_words($callback_return[$j]));
							foreach ($tab_tmp as $mot) {
								if (trim($mot)) {
									$tab_mots[$mot] = "";
								}
							}
							$pos = 1;
							foreach ($tab_mots as $mot => $langage) {
								$num_word = indexation::add_word($mot, $langage);
								if ($num_word != 0) {
									$tab_insert[] = $this->get_tab_insert($object_id, $callable_data[$i], $num_word, $order_fields, $pos);
									$pos++;
								}
							}
							$order_fields++;
						}
					}
				}
			}
			$this->save_elements($tab_insert, $tab_field_insert);
		}
	}
	
	
	//compile les tableaux et lance les requetes
	protected function save_elements($tab_insert, $tab_field_insert){
		global $dbh;
		if($tab_insert && count($tab_insert)){
			$req_insert = "insert into ".$this->table_prefix."_words_global_index(".$this->reference_key.",code_champ,code_ss_champ,num_word,pond,position,field_position) values ".implode(',',$tab_insert);
			pmb_mysql_query($req_insert,$dbh);
		}
		if($tab_field_insert && count($tab_field_insert)){
			//la table pour les recherche exacte
			$req_insert = "insert into ".$this->table_prefix."_fields_global_index(".$this->reference_key.",code_champ,code_ss_champ,ordre,value,lang,pond,authority_num) values ".implode(',',$tab_field_insert);
			pmb_mysql_query($req_insert,$dbh);
		}
	}
	
	//vérifie l'utilisation d'un mot dans les tables d'index.
	public static function check_word_use($id_word){
		//TODO
		return true;
	}
	
	public static function calc_stem($word,$lang){
		$stemming = new stemming($word);
		return $stemming->stem;
	}
	
	public static function calc_double_metephone($word,$lang){
		$dmeta = new DoubleMetaPhone($word);
		if($dmeta->primary || $dmeta->secondary){
			return $dmeta->primary." ".$dmeta->secondary;
		}else{
			return "";
		}
	}
	
	public static function add_word($word,$lang){
		global $dbh;
		
		if (!$lang) {
			$word_langage = 'common';
		} else {
			$word_langage = $lang;
		}
		if (!isset(static::$num_words[$word_langage][$word])) {		
			if(isset(static::$num_words[$word_langage]) && count(static::$num_words[$word_langage]) > 1000) { // Parade pour éviter le dépassement de mémoire
				static::$num_words[$word_langage] = array();
			}
			$query = "select id_word from words where word = '".$word."' and lang = '".$lang."'";
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				static::$num_words[$word_langage][$word] = pmb_mysql_result($result,0,0);
			}else{
				$double_metaphone = indexation::calc_double_metephone($word, $lang);
				$stem = indexation::calc_stem($word, $lang);
				$element_to_update = "";
				if($double_metaphone){
					$element_to_update.="double_metaphone = '".$double_metaphone."'";
				}
				if($element_to_update) $element_to_update.=",";
				$element_to_update.="stem = '".$stem."'";
					
				$query = "insert into words set word = '".$word."', lang = '".$lang."'".($element_to_update ? ", ".$element_to_update : "");
				pmb_mysql_query($query,$dbh);
				static::$num_words[$word_langage][$word] = pmb_mysql_insert_id($dbh);
			}
		}
		return static::$num_words[$word_langage][$word];
	}

	protected function delete_index($object_id,$datatype="all"){
		global $dbh;
		//qu'est-ce qu'on efface?
		if($datatype=='all') {
			$req_del="delete from ".$this->table_prefix."_words_global_index where ".$this->reference_key."='".$object_id."' ";
			pmb_mysql_query($req_del,$dbh);
			//la table pour les recherche exacte
			$req_del="delete from ".$this->table_prefix."_fields_global_index where ".$this->reference_key."='".$object_id."' ";
			pmb_mysql_query($req_del,$dbh);
		}else{
			foreach($this->datatypes as $xml_datatype=> $codes){
				if($xml_datatype == $datatype){
					foreach($codes as $code_champ){
						$req_del="delete from ".$this->table_prefix."_words_global_index where ".$this->reference_key."='".$object_id."' and code_champ='".$code_champ."'";
						pmb_mysql_query($req_del,$dbh);
						//la table pour les recherche exacte
						$req_del="delete from ".$this->table_prefix."_fields_global_index where ".$this->reference_key."='".$object_id."' and code_champ='".$code_champ."'";
						pmb_mysql_query($req_del,$dbh);
					}
				}
			}
		}
	}
	
	protected function get_tab_field_insert($object_id,$infos,$order_fields,$isbd, $lang = '', $autorite = 0) {
		return "(".$object_id.",".$infos["champ"].",".$infos["ss_champ"].",".$order_fields.",'".addslashes(trim($isbd))."','".addslashes(trim($lang))."',".$infos["pond"].",".(intval($autorite)).")";
	}
	
	protected function get_tab_insert($object_id, $infos, $num_word, $order_fields, $pos) {
		return "(".$object_id.", ".$infos["champ"].", ".$infos["ss_champ"].", ".$num_word.", ".$infos["pond"].", ".$order_fields.", ".$pos.")";
	}
	
	public static function delete_all_index($object_id, $table_prefix, $reference_key, $type = ""){
		global $dbh;
		$req_del="delete from ".$table_prefix."_words_global_index where ".$reference_key."='".$object_id."' ";
		pmb_mysql_query($req_del,$dbh);
		//la table pour les recherche exacte
		$req_del="delete from ".$table_prefix."_fields_global_index where ".$reference_key."='".$object_id."' ";
		pmb_mysql_query($req_del,$dbh);
	}
	
	public function delete_objects_index($objects_ids=array(),$datatype="all"){
		global $dbh;
		
		//on s'assure qu'on a lu le XML et initialisé ce qu'il faut...
		if(!$this->initialized) {
			$this->init();
		}
		
		//qu'est-ce qu'on efface?
		if($datatype=='all') {
			$join_temporary_table = gen_where_in($this->reference_key, $objects_ids);
			$req_del="delete ".$this->table_prefix."_words_global_index from ".$this->table_prefix."_words_global_index ".$join_temporary_table;
			pmb_mysql_query($req_del,$dbh);
			//la table pour les recherche exacte
			$req_del="delete ".$this->table_prefix."_fields_global_index from ".$this->table_prefix."_fields_global_index ".$join_temporary_table;
			pmb_mysql_query($req_del,$dbh);
		}else{
			foreach($this->datatypes as $xml_datatype=> $codes){
				if($xml_datatype == $datatype){
					$join_temporary_table = gen_where_in($this->reference_key, $objects_ids);
					foreach($codes as $code_champ){
						$req_del="delete ".$this->table_prefix."_words_global_index from ".$this->table_prefix."_words_global_index ".$join_temporary_table." and code_champ='".$code_champ."'";
						pmb_mysql_query($req_del,$dbh);
						//la table pour les recherche exacte
						$req_del="delete ".$this->table_prefix."_fields_global_index from ".$this->table_prefix."_fields_global_index ".$join_temporary_table." and code_champ='".$code_champ."'";
						pmb_mysql_query($req_del,$dbh);
					}
				}
			}
		}
	}
	
	public static function get_indexation_sub_join($link) {
		$jointure = "";
		if(isset($link["TABLE"][0]['ALIAS'])){
			$alias = $link["TABLE"][0]['ALIAS'];
		}else{
			$alias = $link["TABLE"][0]['value'];
		}
		$sub_link = $link['LINK'][0];
		if(isset($sub_link["TABLE"][0]['ALIAS'])){
			$sub_alias = $sub_link["TABLE"][0]['ALIAS'];
		}else{
			$sub_alias = $sub_link["TABLE"][0]['value'];
		}
		switch ($link["TYPE"]) {
			case "n0" :
				break;
			case "n1" :
				break;
			case "1n" :
				break;
			case "nn" :
				$jointure .= " JOIN " . $sub_link["TABLE"][0]['value'].($sub_link["TABLE"][0]['value'] != $sub_alias  ? " AS ".$sub_alias : "");
				$jointure .= " ON (" . $alias . "." .  $link['EXTERNALFIELD'][0]['value'];
				$jointure .= "=" . $sub_alias . "." . $sub_link["REFERENCEFIELD"][0]['value'] . " ".$link["LINKRESTRICT"][0]['value']. ") ";
				break;
		}
		return $jointure;
	}
	
	public function set_deleted_index($deleted_index) {
		$this->deleted_index = $deleted_index;
	}
	
	protected function get_authperso_class($id_type_authperso){
		if(!isset(self::$authpersos[$id_type_authperso])){
			self::$authpersos[$id_type_authperso] = new authperso($id_type_authperso);
		}
		return self::$authpersos[$id_type_authperso];
	}
	
	protected function get_parametres_perso_class($type){
		if(!isset(self::$parametres_perso[$type])){
			self::$parametres_perso[$type] = new parametres_perso($type);
		}
		return self::$parametres_perso[$type];
	}
}