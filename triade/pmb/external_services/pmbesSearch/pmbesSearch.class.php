<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesSearch.class.php,v 1.49 2023/08/28 14:01:14 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
global $charset, $msg, $lang;
global $pmb_external_service_session_duration;
global $search;

require_once $class_path."/external_services.class.php";
require_once $class_path."/external_services_common.class.php";

class pmbesSearch extends external_services_api_class {
	
	public function update_session_date($session_id) {
		$sql = "UPDATE es_searchsessions SET es_searchsession_lastseendate = NOW() WHERE es_searchsession_id = '".addslashes($session_id)."'";
		pmb_mysql_query($sql);
	}
	
	public function noticeids_to_recordformats($noticesids, $record_format, $recordcharset='iso-8859-1', $includeLinks=true, $includeItems=false, $cleanHTML=false) {
		$converter = new external_services_converter_notices(1, 600);
		$converter->set_params(array("map" => true, "include_links" => $includeLinks, "include_items" => $includeItems, "clean_html" => $cleanHTML, "include_authorite_ids" => true));
		return $converter->convert_batch($noticesids, $record_format, $recordcharset);
	}

	public function external_noticeids_to_recordformats($noticesids, $record_format, $recordcharset='iso-8859-1') {
		$converter = new external_services_converter_external_notices(4, 600);
		$converter->set_params(array());
		return $converter->convert_batch($noticesids, $record_format, $recordcharset);
	}
	
	public function make_search($search_realm, $PMBUserId, $OPACEmprId) {

		global $pmb_external_service_session_duration;
		$pmb_external_service_session_duration = intval($pmb_external_service_session_duration);
		$PMBUserId = intval($PMBUserId);
		$OPACEmprId = intval($OPACEmprId);

		$search_cache = new external_services_searchcache($search_realm, '', $PMBUserId, $OPACEmprId);
		$search_cache->update();
		$search_unique_name = $search_cache->search_unique_id;
		$result_count = $search_cache->get_result_count();
		$result_typdoc_list = $search_cache->get_typdoc_list();

		if (!$search_unique_name) {
			return array("searchId"=>0,"nbResults"=>0,"nPerPages"=>20);
		}
		
		//Deletons les sessions trop vieilles
		$sql = "DELETE FROM es_searchsessions WHERE es_searchsession_lastseendate + INTERVAL ".$pmb_external_service_session_duration." SECOND <= NOW()";
		pmb_mysql_query($sql);
		
		//Générons un numéro de session
		$session_id = md5(microtime());
		$sql = "INSERT INTO es_searchsessions (es_searchsession_id, es_searchsession_searchnum, es_searchsession_searchrealm, es_searchsession_pmbuserid, es_searchsession_opacemprid, es_searchsession_lastseendate) VALUES ('".$session_id."', '".$search_unique_name."', '".addslashes($search_realm)."', ".$PMBUserId.", ".$OPACEmprId.", NOW())";
		pmb_mysql_query($sql);

		return array("searchId"=>$session_id,"nbResults"=>$result_count,"typdocs"=>$result_typdoc_list);
	}
	

	public function simpleSearch($searchType=0,$searchTerm="",$PMBUserId=-1, $OPACEmprId=-1) {
		
		global $charset;
		if ($this->proxy_parent->input_charset!='utf-8' && $charset == 'utf-8') {
			$searchTerm = encoding_normalize::utf8_normalize($searchTerm);
		}
		else if ($this->proxy_parent->input_charset=='utf-8' && $charset != 'utf-8') {
			$searchTerm = encoding_normalize::utf8_decode($searchTerm);	
		}
		
		switch ($searchType) {
			case external_services_common::SIMPLE_SEARCH_TYPES['ALL'] :
				$searchId=7;
				break;
			case external_services_common::SIMPLE_SEARCH_TYPES['TITLE'] :
				$searchId=6;
				break;
			case external_services_common::SIMPLE_SEARCH_TYPES['AUTHOR'] :
				$searchId=8;
				break;
			case external_services_common::SIMPLE_SEARCH_TYPES['EDITOR'] :
				$searchId=3;
				break;
			case external_services_common::SIMPLE_SEARCH_TYPES['COLLECTION'] :
				$searchId=4;
				break;
			case external_services_common::SIMPLE_SEARCH_TYPES['CATEGORIES'] :
				$searchId=1;
				break;
			default:
				$this->error = external_services_common::UNKNOWN_FIELD_ERROR;
				$this->error_message=$this->msg["unknown_field"];
				$searchId=0;
				break;
		}
		if ($searchId) {
			global $search;
			$search=array();
			$search[0]="f_".$searchId;
			$field="field_0_".$search[0];
			global ${$field};
			${$field}=array($searchTerm);
			$op="op_0_".$search[0];
			global ${$op};
			${$op}="BOOLEAN";
			
			return $this->make_search('search_simple_fields', $PMBUserId, $OPACEmprId);
			
		} else return "";

	}
	
	public function simpleSearchLocalise($searchType=0,$searchTerm="",$PMBUserId=-1, $OPACEmprId=-1,$location=0,$section=0) {
		
		global $charset;
		if ($this->proxy_parent->input_charset!='utf-8' && $charset == 'utf-8') {
			$searchTerm = encoding_normalize::utf8_normalize($searchTerm);
		}
		else if ($this->proxy_parent->input_charset=='utf-8' && $charset != 'utf-8') {
			$searchTerm = encoding_normalize::utf8_decode($searchTerm);	
		}
		
		$req = "select count(1) from docsloc_section where num_section='".$section."' and num_location='".$location."'";
		$res = pmb_mysql_query($req);
		
		$sec_valide = false;
		if(pmb_mysql_num_rows($res)){
			$sec_valide = true;
		}
		
		switch ($searchType) {
			case external_services_common::SIMPLE_SEARCH_TYPES['ALL'] :
				$searchId=(($section && $sec_valide) ? 25 : 20);
				break;
			case external_services_common::SIMPLE_SEARCH_TYPES['TITLE'] :
				$searchId=(($section&& $sec_valide) ? 24 : 19);
				break;
			case external_services_common::SIMPLE_SEARCH_TYPES['AUTHOR'] :
				$searchId=(($section && $sec_valide) ? 26 : 21);
				break;
			case external_services_common::SIMPLE_SEARCH_TYPES['EDITOR'] :
				$searchId=(($section && $sec_valide) ? 22 : 17);
				break;
			case external_services_common::SIMPLE_SEARCH_TYPES['COLLECTION'] :
				$searchId=(($section && $sec_valide) ? 23 : 18);
				break;
			case external_services_common::SIMPLE_SEARCH_TYPES['CATEGORIES'] :
				$searchId=(($section && $sec_valide) ? 28 : 27);
				break;
			default:
				$this->error = external_services_common::UNKNOWN_FIELD_ERROR;
				$this->error_message=$this->msg["unknown_field"];
				$searchId=0;
				break;
		}
		if ($searchId) {
			global $search;
			$search=array();
			$search[0]="f_".$searchId;
			$field="field_0_".$search[0];
			global ${$field};
			${$field}=array($searchTerm);
			$op="op_0_".$search[0];
			global ${$op};
			${$op}="BOOLEAN";
			$fieldvar="fieldvar_0_".$search[0];
			global ${$fieldvar};
			${$fieldvar}["location"][0] = $location;
			if($section){
				${$fieldvar}["section"][0] = $section;
			}			
			
			return $this->make_search('search_simple_fields', $PMBUserId, $OPACEmprId);
			
		} else return "";

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
	public function getAdvancedSearchFields($search_realm, $vlang, $fetch_values) {

		$result = external_services_common::getAdvancedSearchFields($search_realm, $vlang, $fetch_values);
		return encoding_normalize::utf8_normalize($result);
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
	public function getAdvancedSearchField($field_id, $search_realm, $vlang, $fetch_values, $search_object=NULL, $nocache=false) {
		
		$result = external_services_common::getAdvancedSearchField($field_id, $search_realm, $vlang, $fetch_values, $search_object, $nocache);
		return encoding_normalize::utf8_normalize($result);
	}
	
	
	public function advancedSearch($search_realm, $search_description, $PMBUserId=-1, $OPACEmprId=-1) {
		global $search;

		object_to_array($search_description);
		
		global $charset;
		if ($this->proxy_parent->input_charset!='utf-8' && $charset == 'utf-8') {
			foreach ($search_description as $index => $afield_s) {
				if (!is_array($search_description[$index]["value"]))
					$search_description[$index]["value"] = encoding_normalize::utf8_normalize($search_description[$index]["value"]);
				else {
					foreach($search_description[$index]["value"] as $value_index => $value) {
						$search_description[$index]["value"][$value_index] = encoding_normalize::utf8_normalize($search_description[$index]["value"][$value_index]);
					}
				}
			}
		}
		else if ($this->proxy_parent->input_charset=='utf-8' && $charset != 'utf-8') {
			foreach ($search_description as $index => $afield_s) {
				if (!is_array($search_description[$index]["value"]))
					$search_description[$index]["value"] = encoding_normalize::utf8_decode($search_description[$index]["value"]);
				else {
					foreach($search_description[$index]["value"] as $value_index => $value) {
						$search_description[$index]["value"][$value_index] = encoding_normalize::utf8_decode($search_description[$index]["value"][$value_index]);
					}
				}
			}	
		}

		$count=0;
		foreach ($search_description as $afield_s) {
			if(preg_match("/^[a-z]/",$afield_s['field'])){
				$search[$count]=$afield_s["field"];	
			}else{
				$search[$count]="f_".$afield_s["field"];
			}
			$field="field_".$count."_".$search[$count];
			$var[]=$field;
			global ${$field};
			if (is_array($afield_s["value"])) {
				${$field}=$afield_s["value"];
			} else {
				${$field}=array($afield_s["value"]);
			}
			$op="op_".$count."_".$search[$count];
			global ${$op};
			${$op}=$afield_s["operator"];
			if ($count) {
				$inter="inter_".$count."_".$search[$count];
				global ${$inter};
				${$inter} = $afield_s["inter"];
			}
			$fieldvar ="fieldvar_".$count."_".$search[$count];
			global ${$fieldvar};
			${$fieldvar}=$afield_s["fieldvar"];
			
			global $explicit_search;
			$explicit_search=1;
			$count++;
		}
		return $this->make_search($search_realm, $PMBUserId, $OPACEmprId);
	}
	
	public function get_sort_types() {
		
		return external_services_common::getRecordSortTypes();
	}
	
	public function fetchSearchRecords($searchId, $firstRecord, $recordCount, $recordFormat, $recordCharset='iso-8859-1', $includeLinks=true, $includeItems=false) {
		//On tri par défaut selon la pertinence des résultats
		return $this->proxy_parent->pmbesSearch_fetchSearchRecordsSorted($searchId, $firstRecord, $recordCount, $recordFormat, $recordCharset, $includeLinks, $includeItems, "d_num_6");
	}

	public function fetchSearchRecordsSorted($searchId, $firstRecord, $recordCount, $recordFormat, $recordCharset='iso-8859-1', $includeLinks=true, $includeItems=false, $sort_type="") {

		$firstRecord = intval($firstRecord);
		$recordCount = intval($recordCount);

		//Cherchons la session
		$sql = "SELECT * FROM es_searchsessions WHERE es_searchsession_id = '".addslashes($searchId)."'";
		$res = pmb_mysql_query($sql);
		if (!pmb_mysql_num_rows($res)) {
			return array();
		}
		$row = pmb_mysql_fetch_assoc($res);
		$this->update_session_date($searchId);

		$search_unique_id = $row["es_searchsession_searchnum"];
		$search_realm = $row["es_searchsession_searchrealm"];
		$pmbuserid = $row["es_searchsession_pmbuserid"];
		$opacemprid = $row["es_searchsession_opacemprid"];
		
		if (!$search_unique_id) {
			return array();
		}

		$search_cache = new external_services_searchcache($search_realm, $search_unique_id, $pmbuserid, $opacemprid);
		$notice_ids = $search_cache->get_results($firstRecord, $recordCount, $sort_type);

		if ($search_cache->external_search) {
			$records = $this->external_noticeids_to_recordformats($notice_ids, $recordFormat, $recordCharset);
		}
		else {
			$records = $this->noticeids_to_recordformats($notice_ids, $recordFormat, $recordCharset, $includeLinks, $includeItems);
		}
		
		$results = array();
		foreach ($records as $notice_id => $record_value) {
			$results[] = array(
				"noticeId" => $notice_id,
				"noticeContent" => $record_value
			);
		}

		return $results;
	}
	
	public function fetchSearchRecordsArray($searchId, $firstRecord, $recordCount, $recordCharset='iso-8859-1', $includeLinks=true, $includeItems=false) {
		//On tri par défaut selon la pertinence des résultats
		return $this->proxy_parent->pmbesSearch_fetchSearchRecordsArraySorted($searchId, $firstRecord, $recordCount, $recordCharset, $includeLinks, $includeItems, "d_num_6");
	}
	
	public function fetchSearchRecordsArraySorted($searchId, $firstRecord, $recordCount, $recordCharset='iso-8859-1', $includeLinks=true, $includeItems=false, $sort_type="") {

		$firstRecord = intval($firstRecord);
		$recordCount = intval($recordCount);

		//Cherchons la session
		$sql = "SELECT * FROM es_searchsessions WHERE es_searchsession_id = '".addslashes($searchId)."'";
		$res = pmb_mysql_query($sql);
		if (!pmb_mysql_num_rows($res)) {
			return array();
		}
		$row = pmb_mysql_fetch_assoc($res);
		$this->update_session_date($searchId);

		$search_unique_id = $row["es_searchsession_searchnum"];
		$search_realm = $row["es_searchsession_searchrealm"];
		$pmbuserid = $row["es_searchsession_pmbuserid"];
		$opacemprid = $row["es_searchsession_opacemprid"];
		
		if (!$search_unique_id) {
			return array();
		}

		$search_cache = new external_services_searchcache($search_realm, $search_unique_id, $pmbuserid, $opacemprid);
		$notice_ids = $search_cache->get_results($firstRecord, $recordCount, $sort_type);
		
		$records = $this->noticeids_to_recordformats($notice_ids, "raw_array", $recordCharset, $includeLinks, $includeItems);
		$array_results = array_values($records);
		
		return $array_results;
	}
	
	public function listExternalSources($OPACUserId=-1) {
		global $msg;
		$sql = 'SELECT connectors_sources.source_id, connectors_sources.name, connectors_sources.comment, connectors_categ.connectors_categ_name FROM connectors_sources LEFT JOIN connectors_categ_sources ON (connectors_categ_sources.num_source = connectors_sources.source_id) LEFT JOIN connectors_categ ON (connectors_categ.connectors_categ_id = connectors_categ_sources.num_categ) WHERE 1 '.($OPACUserId != -1 ? 'AND connectors_sources.opac_allowed = 1' : '').' ORDER BY connectors_categ.connectors_categ_name, connectors_sources.name';
		$res = pmb_mysql_query($sql);
		$results = array();
		$categs = array();
		while($row = pmb_mysql_fetch_assoc($res)) {
			$categs[$row['connectors_categ_name'] ? $row['connectors_categ_name'] : $msg['source_no_category']][] = array(
				'source_id' => $row['source_id'],
				'source_caption' => encoding_normalize::utf8_normalize($row['name']),
				'source_comment' => encoding_normalize::utf8_normalize($row['comment']),
			);
		}
		foreach($categs as $categ_name => $categ_content) {
			$results[] = array(
				'category_caption' => encoding_normalize::utf8_normalize($categ_name),
				'sources' => $categ_content,
			);
		}
		return $results;
	}
	
	public function fetchSearchRecordsFull($searchId, $firstRecord, $recordCount, $recordCharset='iso-8859-1', $includeLinks=true, $includeItems=false) {
		//On tri par défaut selon la pertinence des résultats
		return $this->proxy_parent->pmbesSearch_fetchSearchRecordsFullSorted($searchId, $firstRecord, $recordCount, $recordCharset, $includeLinks, $includeItems, "d_num_6");
	}
	
	public function fetchSearchRecordsFullSorted($searchId, $firstRecord, $recordCount, $recordCharset='iso-8859-1', $includeLinks=true, $includeItems=false,$sort_type='') {

		$firstRecord = intval($firstRecord);
		$recordCount = intval($recordCount);

		//Cherchons la session
		$sql = "SELECT * FROM es_searchsessions WHERE es_searchsession_id = '".addslashes($searchId)."'";
		$res = pmb_mysql_query($sql);
		if (!pmb_mysql_num_rows($res)) {
			return array();
		}
		$row = pmb_mysql_fetch_assoc($res);
		$this->update_session_date($searchId);

		$search_unique_id = $row["es_searchsession_searchnum"];
		$search_realm = $row["es_searchsession_searchrealm"];
		$pmbuserid = $row["es_searchsession_pmbuserid"];
		$opacemprid = $row["es_searchsession_opacemprid"];
		
		if (!$search_unique_id) {
			return array();
		}

		$search_cache = new external_services_searchcache($search_realm, $search_unique_id, $pmbuserid, $opacemprid);
		$notice_ids = $search_cache->get_results($firstRecord, $recordCount, $sort_type);
		
		
		$records = $this->proxy_parent->pmbesNotices_fetchNoticeListFull($notice_ids,"raw_array_assoc", $recordCharset, $includeLinks, $includeItems);	
		return $records;
		$array_results = array_values($records);
		
		return $array_results;
	}
	
	public function fetchSearchRecordsFullWithBullId($searchId, $firstRecord, $recordCount, $recordCharset='iso-8859-1', $includeLinks=true, $includeItems=false) {
		//On tri par défaut selon la pertinence des résultats
		return $this->proxy_parent->pmbesSearch_fetchSearchRecordsFullWithBullIdSorted($searchId, $firstRecord, $recordCount, $recordCharset, $includeLinks, $includeItems, "d_num_6");
	}
	
	public function fetchSearchRecordsFullWithBullIdSorted($searchId, $firstRecord, $recordCount, $recordCharset='iso-8859-1', $includeLinks=true, $includeItems=false,$sort_type='') {

		$firstRecord = intval($firstRecord);
		$recordCount = intval($recordCount);

		//Cherchons la session
		$sql = "SELECT * FROM es_searchsessions WHERE es_searchsession_id = '".addslashes($searchId)."'";
		$res = pmb_mysql_query($sql);
		if (!pmb_mysql_num_rows($res)) {
			return array();
		}
		$row = pmb_mysql_fetch_assoc($res);
		$this->update_session_date($searchId);

		$search_unique_id = $row["es_searchsession_searchnum"];
		$search_realm = $row["es_searchsession_searchrealm"];
		$pmbuserid = $row["es_searchsession_pmbuserid"];
		$opacemprid = $row["es_searchsession_opacemprid"];
		
		if (!$search_unique_id) {
			return array();
		}

		$search_cache = new external_services_searchcache($search_realm, $search_unique_id, $pmbuserid, $opacemprid);
		$notice_ids = $search_cache->get_results($firstRecord, $recordCount, $sort_type);
		
		
		$records = $this->proxy_parent->pmbesNotices_fetchNoticeListFullWithBullId($notice_ids,"raw_array_assoc", $recordCharset, $includeLinks, $includeItems);	
		return $records;
		$array_results = array_values($records);
		
		return $array_results;
	}
	
	public function listFacets($searchId, $fields = array(), $filters = array()) {
	    global $lang, $msg;
        
		object_to_array($fields);
 		object_to_array($filters);
		$facets = array();
		if(is_array($fields) && count($fields)) {
			if(is_array($filters) && count($filters)) {
				$notice_ids = $this->listRecordsFromFacets($searchId, $filters);
			} else {
				//Cherchons la session
				$sql = "SELECT * FROM es_searchsessions WHERE es_searchsession_id = '".addslashes($searchId)."'";
				$res = pmb_mysql_query($sql);
				if (!pmb_mysql_num_rows($res)) {
					return array();
				}
				
				$row = pmb_mysql_fetch_assoc($res);
				$this->update_session_date($searchId);
				
				$search_unique_id = $row["es_searchsession_searchnum"];
				$search_realm = $row["es_searchsession_searchrealm"];
				$pmbuserid = $row["es_searchsession_pmbuserid"];
				$opacemprid = $row["es_searchsession_opacemprid"];
				if (!$search_unique_id) {
					return array();
				}
				
				$search_cache = new external_services_searchcache($search_realm, $search_unique_id, $pmbuserid, $opacemprid);
				$notice_ids = $search_cache->get_results(0,0);
			}
			if(is_array($notice_ids) && count($notice_ids)) {
				foreach ($fields as $field) {
				    $code_champ = (int) $field['code_champ'];
				    $code_ss_champ = (int) $field['code_ss_champ'];
					if($code_champ) {
						$query = "select count(distinct id_notice) as nb_records, value from notices_fields_global_index where code_champ = '".$code_champ."'";
						if($code_ss_champ) {
							$query .= " and code_ss_champ = '".$code_ss_champ."'";
						}
						$query .= " and lang in ('','".$lang."','".substr($lang,0,2)."')";
						$query .= " and id_notice in (".implode(',', $notice_ids).") group by value";
						
						if (isset($field['type_sort']) && $field['type_sort'] == 0) {
						    $query .= " ORDER BY nb_records";
						} elseif (!empty($field['datatype_sort'])) {
						    switch ($field['datatype_sort']) {
						        case "date":
						            $query .= " ORDER BY STR_TO_DATE(value,'".$msg['format_date']."')";
                                    break;
                                    
						        case "num":
						            $query .= " ORDER BY value*1";
                                    break;
						        
						        default:
						            $query .= " ORDER BY value";
                                    break;
						    }
						}
						
						if (isset($field['order_sort'])) {						    
    						if (0 == $field['order_sort']) {
    						    $query .= " ASC";						    
    						} else {
    						    $query .= " DESC";						    
    						}
						}
						
						
						if(isset($field['nb_result']) &&  0 < $field['nb_result']){
						    $query .= " LIMIT"." ".intval($field['nb_result']);
						}
						
						$result = pmb_mysql_query($query);
						while($row = pmb_mysql_fetch_object($result)) {
							$facets[] = array(
									'code_champ' => $code_champ,
									'code_ss_champ' => $code_ss_champ,
									'value' => encoding_normalize::utf8_normalize($row->value),
									'count' => $row->nb_records
							);
						}
					}
				}
			}
		}
		return $facets;
	}
	
	public function listRecordsFromFacets($searchId, $filters = array()) {
	    
	    global $charset;
	    global $opac_facettes_operator;
	    
	    object_to_array($filters);
	    
	    $notice_ids = array();
	    //Cherchons la session
	    $sql = "SELECT * FROM es_searchsessions WHERE es_searchsession_id = '".addslashes($searchId)."'";
	    $res = pmb_mysql_query($sql);
	    if (!pmb_mysql_num_rows($res)) {
	        return array();
	    }
	    $row = pmb_mysql_fetch_assoc($res);
	    $this->update_session_date($searchId);
	    
	    $search_unique_id = $row["es_searchsession_searchnum"];
	    $search_realm = $row["es_searchsession_searchrealm"];
	    $pmbuserid = $row["es_searchsession_pmbuserid"];
	    $opacemprid = $row["es_searchsession_opacemprid"];
	    
	    if (!$search_unique_id) {
	        return array();
	    }
	    $search_cache = new external_services_searchcache($search_realm, $search_unique_id, $pmbuserid, $opacemprid);
	    $notice_ids = $search_cache->get_results(0,0);
	    $ret = [];
	    
	    //On trie les filtres selon leur code champ puis leur code sous-champ
	    usort($filters, function($a, $b){
	        if($a['code_champ'] != $b['code_champ']){
	            return $a['code_champ'] < $b['code_champ'] ? -1 : 1;
	        } else if ($a['code_ss_champ'] != $b['code_ss_champ']){
	            return $a['code_ss_champ'] < $b['code_ss_champ'] ? -1 : 1;
	        } else {
	            return 0;
	        }
	    });
	        
        //On parcourt les filtres triés, en ajoutant les values en fonction du paramètre opac $opac_facettes_operator entre facettes et
        // en ajoutant l'opérateur "or" entre segments
        if(count($notice_ids) && is_array($filters) && count($filters)) {
            for ($i = 0; $i < count($filters); $i++) {
                if ($this->proxy_parent->input_charset!='utf-8' && $charset == 'utf-8') {
                    $value = encoding_normalize::utf8_normalize($filters[$i]['value']);
                } else if ($this->proxy_parent->input_charset=='utf-8' && $charset != 'utf-8') {
                    $value = encoding_normalize::utf8_decode($filters[$i]['value']);
                } else {
                    $value = $filters[$i]['value'];
                }
                $code_champ = $filters[$i]['code_champ'];
                $code_ss_champ = $filters[$i]['code_ss_champ'];
                
                if($i == 0){
                    $query = "select distinct id_notice from notices_fields_global_index where id_notice in (select id_notice from notices_fields_global_index where code_champ = '".$code_champ."'";
                    if($code_ss_champ) {
                        $query .= " and code_ss_champ = '".$code_ss_champ."'";
                    }
                    $query .= " and value = '".addslashes($value)."'";
                } else {
                    $prev_code_champ = $filters[$i-1]['code_champ'];
                    $prev_code_ss_champ = $filters[$i-1]['code_ss_champ'];
                    if(($code_champ == $prev_code_champ) && ($code_ss_champ == $prev_code_ss_champ)){
                        $query .= "or value = '".addslashes($value)."'";
                    } else {
                        $query .= ") $opac_facettes_operator id_notice in (select id_notice from notices_fields_global_index where code_champ = '".$code_champ."'";
                        if($code_ss_champ) {
                            $query .= " and code_ss_champ = '".$code_ss_champ."'";
                        }
                        $query .= " and value = '".addslashes($value)."'";
                    }
                    
                }
            }
            $query .= ") and id_notice in (".implode(',', $notice_ids).")";
            $result = pmb_mysql_query($query);
            while($row = pmb_mysql_fetch_object($result)) {
                $ret[] = $row->id_notice;
            }
        }
        return $ret;
	}
}
