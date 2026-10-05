<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: external_services_common.class.php,v 1.5 2024/04/08 09:57:15 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $include_path, $class_path;
global $msg, $lang;

require_once $include_path."/parser.inc.php";
require_once $include_path."/connecteurs_out_common.inc.php";
require_once $class_path."/external_services.class.php";
require_once $class_path."/external_services_caches.class.php";
require_once $class_path."/search.class.php";
require_once $class_path."/parametres_perso.class.php";


class external_services_common {


	public const SIMPLE_SEARCH_TYPES = [
			'ALL'			=> 0,
			'TITLE'			=> 1,
			'AUTHOR' 		=> 2,
			'EDITOR'		=> 3,
			'COLLECTION'	=> 4,
			'CATEGORIES'	=> 5,
	];

	public const UNKNOWN_FIELD_ERROR = 1;


	/**
	 * Retourne la liste des tris disponibles pour les notices
	 *
	 * @return array[]
	 */
	public static function getRecordSortTypes() {

		global $include_path, $msg;

		$result = [];

		$file = $include_path."/sort/notices/sort.xml";
		$subst_file = $include_path."/sort/notices/sort_subst.xml";
		if ( is_readable($subst_file) ) {
			$file = $subst_file;
		}

		$fp = fopen($file, "r");
		if ($fp) {
			$xml = fread($fp, filesize($file));
			fclose($fp);
			$params = _parser_text_no_function_($xml, "SORT", $file);
		}
		$params = _parser_text_no_function_($xml, "SORT",$file);
		foreach ($params["FIELD"] as $aparam) {
			$result[] = array(
					"sort_name" => $aparam["TYPE"]."_".$aparam["ID"],
					"sort_caption" =>  $msg[$aparam["NAME"]]
			);
		}

		return $result;
	}


	/**
	 * Retourne la liste des champs de recherche avancée
	 *
	 * @param string $search_realm : royaume de recherche (search_simple_fields, opac|search_fields)
	 * @param string $vlang : langue des résultats (fr_FR, en_UK, ...)
	 * @param boolean $fetch_values : retourner les valeurs possibles
	 *
	 * @return array
	 */
	public static function getAdvancedSearchFields($search_realm, $vlang, $fetch_values) {

		global $msg, $lang, $base_path, $include_path, $class_path;

		//Allons chercher les infos dans le cache si elles existent
		if ($fetch_values) {
			$cache_ref = "getAdvancedSearchFields_results_valued_".(!empty($vlang) ? $vlang : $lang)."_".$search_realm;
		} else {
			$cache_ref = "getAdvancedSearchFields_results_".(!empty($vlang) ? $vlang : $lang)."_".$search_realm;
		}
		$es_cache = new external_services_cache('es_cache_blob', 86400);
		$cached_result = $es_cache->decache_single_object($cache_ref, CACHE_TYPE_MISC);
		if ($cached_result !== false) {
			$cached_result = unserialize(base64_decode($cached_result));
			return $cached_result;
		}

		$opac_realm=false;
		$full_path='';
		if (substr($search_realm, 0, 5) == 'opac|') {
			$search_realm = substr($search_realm, 5);
			$full_path = $base_path."/includes/search_queries/";
			$opac_realm = true;
		}

		//Ajoutons la langue demandée à l'environnement
		if ($opac_realm) {
			if (is_readable("$base_path/includes/messages/$vlang.xml")) {
				//Allons chercher les messages
				include_once $class_path."/XMLlist.class.php";
				$messages = new XMLlist($base_path."/includes/messages/$vlang.xml", 0);
				$messages->analyser();
				$msg = $messages->table;
			}
		} else {
			if ($vlang != $lang && is_readable("$include_path/messages/$vlang.xml")) {
				//Allons chercher les messages
				include_once $class_path."/XMLlist.class.php";
				$messages = new XMLlist($include_path."/messages/$vlang.xml", 0);
				$messages->analyser();
				$msg = $messages->table;
			}
		}

		//Modifions la globale $lang pour la construction dans la classe search.class.php
		//Attention : à ne pas remonter plus haut car il faut que les messages soient dans la bonne langue
		if(!empty($vlang)) {
			$lang = $vlang;
		}
		$s=new search(false, $search_realm, $full_path);
		$results=array();
		//les champs statiques
		foreach ($s->fixedfields as $id => $content) {
			$results[] = static::getAdvancedSearchField($id, $search_realm, $vlang, $fetch_values, $s, true);
		}
		//les champs dynamiques
		foreach ($s->dynamicfields as $prefix => $content) {
			$pp = new parametres_perso($content['TYPE']);
			foreach($pp->t_fields as $id=>$field){
				if((!$opac_realm || ($opac_realm && $field['OPAC_SHOW'])) && $field['SEARCH']) {
					$results[] = static::getAdvancedSearchField($prefix.$id, $search_realm, $vlang, $fetch_values, $s, true);
				}
			}
		}

		//Mettons le resultat dans le cache
		$es_cache = new external_services_cache('es_cache_blob', 86400);
		$es_cache->encache_single_object($cache_ref, CACHE_TYPE_MISC, base64_encode(serialize($results)));

		return $results;
	}

	/**
	 * Retourne le détail d'un champ de recherche avancée
	 *
	 * @param int $field_id : identifiant du champ
	 * @param string $search_realm : royaume de recherche (search_simple_fields, opac|search_fields)
	 * @param string $vlang : langue des résultats (fr_FR, en_UK, ...)
	 * @param boolean $fetch_values : retourner les valeurs possibles
	 * @param object $search_object
	 * @param bool $nocache : ne pas utiliser le cache
	 *
	 * return array
	 */
	public static function getAdvancedSearchField($field_id, $search_realm, $vlang, $fetch_values, $search_object=NULL, $nocache=false) {

		global $msg, $lang, $base_path, $include_path, $class_path;

		if (!$nocache) {
			//Allons chercher les infos dans le cache si elles existent
			$cache_ref = "getAdvancedSearchField_result_".$field_id."_".(!empty($vlang) ? $vlang : $lang)."_".$search_realm;
			$es_cache = new external_services_cache('es_cache_blob', 86400);
			$cached_result = $es_cache->decache_single_object($cache_ref, CACHE_TYPE_MISC);
			if ($cached_result !== false) {
				$cached_result = unserialize(base64_decode($cached_result));
				return $cached_result;
			}
		}

		//Si on nous passe le $search_object, c'est que tout l'environnement est prêt
		if (!$search_object) {

			$opac_realm=false;
			$full_path='';
			if (substr($search_realm, 0, 5) == 'opac|') {
				$search_realm = substr($search_realm, 5);
				$full_path = $base_path."/includes/search_queries/";
				$opac_realm = true;
			}

			//Ajoutant la langue demandée à l'environnement
			if ($opac_realm) {
				if (is_readable($base_path."/includes/messages/$vlang.xml")) {
					//Allons chercher les messages
					include_once($class_path."/XMLlist.class.php");
					$messages = new XMLlist("$base_path/includes/messages/$vlang.xml", 0);
					$messages->analyser();
					$msg = $messages->table;
				}
			} else {
				if ($vlang != $lang && is_readable( $include_path."/messages/$vlang.xml")) {
					//Allons chercher les messages
					include_once($class_path."/XMLlist.class.php");
					$messages = new XMLlist("$include_path/messages/$vlang.xml", 0);
					$messages->analyser();
					$msg = $messages->table;
				}
			}
			//Modifions la globale $lang pour la construction dans la classe search.class.php
			//Attention : à ne pas remonter plus haut car il faut que les messages soient dans la bonne langue
			if(!empty($vlang)) {
				$lang = $vlang;
			}
			$search_object=new search(false, $search_realm, $full_path);
		}


		if (isset($search_object->fixedfields[$field_id])){
			$content = $search_object->fixedfields[$field_id];
			$aresult = array("operators" => array());
			$aresult["id"] = $field_id;
			$aresult["label"] = $content["TITLE"];
			$aresult["type"] = $content["INPUT_TYPE"];
			foreach($content["QUERIES"] as $aquery) {
				$aresult["operators"][] = array("id" => $aquery["OPERATOR"], "label" =>$search_object->operators[$aquery["OPERATOR"]]);
			}
			$aresult["values"] = array();
			$aresult["fieldvar"] = array();

			if ($fetch_values) {
				switch ($content["INPUT_TYPE"]) {
					case "query_list":
						$aresult["values"] = array();
						$requete=$content["INPUT_OPTIONS"]["QUERY"][0]["value"];
						$resultat=pmb_mysql_query($requete);
						while ($opt=pmb_mysql_fetch_row($resultat)) {
							$aresult["values"][] = array(
									"value_id" => $opt[0],
									"value_caption" => $opt[1]
							);
						}
						break;
					case "list":
						if (!isset($content["INPUT_OPTIONS"]["OPTIONS"][0]["OPTION"]))
							break;
							foreach ($content["INPUT_OPTIONS"]["OPTIONS"][0]["OPTION"] as $aoption) {
								if (substr($aoption["value"],0,4)=="msg:") {
									$aoption["value"] = $msg[substr($aoption["value"],4)];
								}
								$aresult["values"][] = array(
										"value_id" => $aoption["VALUE"],
										"value_caption" => $aoption["value"]
								);
							}
							break;
					case "marc_list":
						$options=new marc_list($content["INPUT_OPTIONS"]["NAME"][0]["value"]);
						asort($options->table);
						reset($options->table);

						// gestion restriction par code utilise.
						if ($content["INPUT_OPTIONS"]["RESTRICTQUERY"][0]["value"]) {
							$restrictquery=pmb_mysql_query($content["INPUT_OPTIONS"]["RESTRICTQUERY"][0]["value"]);
							if ($restrictqueryrow=@pmb_mysql_fetch_row($restrictquery)) {
								if ($restrictqueryrow[0]) {
									$restrictqueryarray=explode(",",$restrictqueryrow[0]);
									$existrestrict=true;
								} else $existrestrict=false;
							} else $existrestrict=false;
						} else $existrestrict=false;

						foreach ($options->table as $key => $val) {
							if ($existrestrict && array_search($key,$restrictqueryarray)!==false) {
								$aresult["values"][] = array(
										"value_id" => $key,
										"value_caption" => $val
								);
							} elseif (!$existrestrict) {
								$aresult["values"][] = array(
										"value_id" => $key,
										"value_caption" => $val
								);
							}
						}
						break;
					case "text":
					case "authoritie":
					default:
						$aresult["values"] = array();
						break;
				}
			}
			if($content['VAR']){
				$params = array();
				foreach($content['VAR'] as $variable){
					if($variable['TYPE'] == "input"){
						$input=$variable['OPTIONS']['INPUT'][0];
						$values = array();
						switch ($input['TYPE']) {
							case "query_list":
								$concat = "";
								$query_list_result=@pmb_mysql_query($input['QUERY'][0]['value']);
								while ($value=pmb_mysql_fetch_array($query_list_result)) {
									if($concat)$concat.=",";
									$concat.=$value[0];
									$values[]=array(
											'value_id' => $value[0],
											'value_caption' => $value[1]
									);
								}
								if($input['QUERY'][0]['ALLCHOICE'] == "yes"){
									$values[]=array(
											'value_id' => $concat,
											'value_caption' =>$msg[substr($input['QUERY'][0]['TITLEALLCHOICE'],4,strlen($input['QUERY'][0]['TITLEALLCHOICE'])-4)]
									);
								}
								break;
							case "checkbox" :
							case "hidden" :
								if(isset($input["VALUE"][0]["value"])) {
									$values = array($input["VALUE"][0]["value"]);
								}
								break;
						}
						$params[]=array(
								'label'=>$variable['COMMENT'],
								'name'=>$variable['NAME'],
								'type'=>$input['TYPE'],
								'values'=>$values,
						);
					}
					$aresult['fieldvar']=$params;
				}
			}
		}else{
			$aresult = array();
			foreach ($search_object->dynamicfields as $prefix => $content) {
				$pp = new parametres_perso($content['TYPE']);
				foreach($pp->t_fields as $id=>$field){
					if($field_id == $prefix.$id){
						if ((!$opac_realm || ($opac_realm && $field['OPAC_SHOW'])) && $field['SEARCH']){
							$field['ident']=$field_id;
							$field['ID']=$id;
							$field['PREFIX']="notices";
							$aresult= aff_empr_search($field);
							$aresult['id']=$prefix."_".$id;
							foreach($content['FIELD'] as $field_spec){
								if($field_spec['DATATYPE'] == $field['DATATYPE'])
									$queries = $field_spec['QUERIES'];
							}
							foreach($queries as $aquery) {
								$aresult['operators'][]= array("id" => $aquery["OPERATOR"], "label" => $search_object->operators[$aquery["OPERATOR"]]);
							}
						}
						break;
					}
				}
			}
		}

		if(!isset($aresult['values'])) {
			$aresult['values'] = array();
		}
		if(!isset($aresult['fieldvar'])) {
			$aresult['fieldvar'] = array();
		}
		if (!$nocache) {
			//Mettons le resultat dans le cache
			$es_cache = new external_services_cache('es_cache_blob', 86400);
			$es_cache->encache_single_object($cache_ref, CACHE_TYPE_MISC, base64_encode(serialize($aresult)));
		}

		return $aresult;

	}


}
