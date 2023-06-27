<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesOPACAnonymous.class.php,v 1.21 2019-06-10 10:05:02 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/external_services.class.php");

define("LIST_LOAN_LATE",0);
define("LIST_LOAN_CURRENT",1);
define("LIST_LOAN_PRECEDENT",2);

class pmbesOPACAnonymous extends external_services_api_class{
	
	public function restore_general_config() {
		
	}
	
	public function form_general_config() {
		return false;
	}
	
	public function save_general_config() {
		
	}

	public function simpleSearch($searchType=0,$searchTerm="",$PMBUserId=-1, $OPACEmprId=-1) {
		return $this->proxy_parent->pmbesSearch_simpleSearch($searchType, $searchTerm, -1, 0);
	}
	
	public function simpleSearchLocalise($searchType=0,$searchTerm="",$PMBUserId=-1, $OPACEmprId=-1,$location,$section=0) {
		return $this->proxy_parent->pmbesSearch_simpleSearchLocalise($searchType, $searchTerm, -1, 0,$location,$section=0);
	}
	public function get_sort_types() {
		return $this->proxy_parent->pmbesSearch_get_sort_types();
	}
	
	public function fetchSearchRecords($searchId, $firstRecord, $recordCount, $recordFormat, $recordCharset='iso-8859-1') {
		return $this->proxy_parent->pmbesSearch_fetchSearchRecords($searchId, $firstRecord, $recordCount, $recordFormat, $recordCharset, true, true);
	}

	public function fetchSearchRecordsSorted($searchId, $firstRecord, $recordCount, $recordFormat, $recordCharset='iso-8859-1', $sort_type="") {
		return $this->proxy_parent->pmbesSearch_fetchSearchRecordsSorted($searchId, $firstRecord, $recordCount, $recordFormat, $recordCharset, true, true, $sort_type);
	}
	
	public function fetchSearchRecordsArray($searchId, $firstRecord, $recordCount, $recordCharset='iso-8859-1') {
		return $this->proxy_parent->pmbesSearch_fetchSearchRecordsArray($searchId, $firstRecord, $recordCount, $recordCharset, true, true);
	}
	
	public function fetchSearchRecordsArraySorted($searchId, $firstRecord, $recordCount, $recordCharset='iso-8859-1', $sort_type="") {
		return $this->proxy_parent->pmbesSearch_fetchSearchRecordsArraySorted($searchId, $firstRecord, $recordCount, $recordCharset, true, true, $sort_type);
	}
	
	public function getAdvancedSearchFields($lang, $fetch_values=false) {
		return $this->proxy_parent->pmbesSearch_getAdvancedSearchFields("opac|search_fields", $lang, $fetch_values);
	}
	
	public function getAdvancedExternalSearchFields($lang, $fetch_values=false) {
		return $this->proxy_parent->pmbesSearch_getAdvancedSearchFields("opac|search_fields_unimarc", $lang, $fetch_values);
	}
	
	public function advancedSearch($search_description) {
		return $this->proxy_parent->pmbesSearch_advancedSearch("opac|search_fields", $search_description, -1, 0);
	}
	
	public function advancedSearchExternal($search_description, $source_ids) {
	    array_walk($source_ids, function(&$a) {$a = intval($a);}); //Soyons sûr de ne stocker que des entiers dans le tableau.
		$source_ids = array_unique($source_ids);
		if (!$source_ids)
			return FALSE;
		return $this->proxy_parent->pmbesSearch_advancedSearch("opac|search_fields_unimarc|sources(".implode(',',$source_ids).")", $search_description, -1, 0);
	}

	public function fetch_notice_items($notice_id) {
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		return $this->proxy_parent->pmbesItems_fetch_notice_items($notice_id, 0);
	}
	
	public function listNoticeExplNums($notice_id) {
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		return $this->proxy_parent->pmbesNotices_listNoticeExplNums($notice_id, 0);
	}
	
	public function listBulletinExplNums($bulletinId) {
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		return $this->proxy_parent->pmbesNotices_listBulletinExplNums($bulletinId, 0);
	}
	
	public function fetchNoticeList($noticelist, $recordFormat, $recordCharset) {
		if (!is_array($noticelist))
			return array();
			
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage

		return $this->proxy_parent->pmbesNotices_fetchNoticeList($noticelist, $recordFormat, $recordCharset, true, true);
	}

	public function fetchExternalNoticeList($noticelist, $recordFormat, $recordCharset) {
		return $this->proxy_parent->pmbesNotices_fetchExternalNoticeList($noticelist, $recordFormat, $recordCharset);
	}
	
	public function fetchNoticeListArray($noticelist, $recordCharset) {
		if (!is_array($noticelist))
			return array();
			
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage

		return $this->proxy_parent->pmbesNotices_fetchNoticeListArray($noticelist, $recordCharset, false, false);
	}
	
	public function fetchNoticeListFull($noticelist, $recordFormat, $recordCharset, $includeLinks) {
		if (!is_array($noticelist))
			return array();

		if (!$noticelist)
			return array();
		
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC
		
		return $this->proxy_parent->pmbesNotices_fetchNoticeListFull($noticelist, $recordFormat, $recordCharset, $includeLinks);
	}
	
	public function fetchBulletinListFull($bulletinlist, $recordFormat, $recordCharset) {
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC
		
		return $this->proxy_parent->pmbesNotices_fetchBulletinListFull($bulletinlist, $recordFormat, $recordCharset);
	}
	
	public function findNoticeBulletinId($noticeId) {
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC
		
		return $this->proxy_parent->pmbesNotices_findNoticeBulletinId($noticeId);
	}
	
	public function fetchNoticeByExplCb($explCb, $recordFormat, $recordCharset) {
		global $dbh;
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC
		
		return $this->proxy_parent->pmbesNotices_fetchNoticeByExplCb(0,$explCb, $recordFormat, $recordCharset, true, true);
	}
	
	public function get_author_information_and_notices($author_id) {
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC
		
		return $this->proxy_parent->pmbesAuthors_get_author_information_and_notices($author_id, 0);
	}

	public function get_collection_information_and_notices($collection_id) {
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC
		
		return $this->proxy_parent->pmbesCollections_get_collection_information_and_notices($collection_id, 0);
	}
	
	public function get_subcollection_information_and_notices($subcollection_id) {
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC
		
		return $this->proxy_parent->pmbesCollections_get_subcollection_information_and_notices($subcollection_id, 0);
	}
	
	public function get_publisher_information_and_notices($publisher_id) {
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC
		
		return $this->proxy_parent->pmbesPublishers_get_publisher_information_and_notices($publisher_id, 0);
	}
	
	public function list_thesauri() {
		return $this->proxy_parent->pmbesThesauri_list_thesauri(0);
	}
	
	public function fetch_thesaurus_node_full($node_id) {
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC
		
		return $this->proxy_parent->pmbesThesauri_fetch_node_full($node_id, 0);
	}
	
	public function fetch_notices_bulletins($noticelist){
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC
		
		return $this->proxy_parent->pmbesNotices_fetch_notices_bulletins($noticelist);	
	}
	
	public function fetchNoticesCollstates($serialIds){
		
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC

		return $this->proxy_parent->pmbesNotices_fetchNoticesCollstates($serialIds);
	}
	
	public function list_shelves() {
		return $this->proxy_parent->pmbesOPACGeneric_list_shelves(0);
	}
	
	public function retrieve_shelf_content( $shelf_id) {
		return $this->proxy_parent->pmbesOPACGeneric_retrieve_shelf_content($shelf_id, 0);
	}	
	
	public function fetchNoticeListFullWithBullId($noticelist, $recordFormat, $recordCharset, $includeLinks=true) {
		if (!is_array($noticelist))
			return array();

		if (!$noticelist)
			return array();
			
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC
		
		return $this->proxy_parent->pmbesNotices_fetchNoticeListFullWithBullId($noticelist, $recordFormat, $recordCharset, $includeLinks);
	}

	public function fetchNoticesBulletinsList($noticelist){
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC
		
		return $this->proxy_parent->pmbesNotices_fetchNoticesBulletinsList($noticelist);	
	}
	
	public function fetchSearchRecordsFull($searchId, $firstRecord, $recordCount,  $recordCharset='iso-8859-1') {
		return $this->proxy_parent->pmbesSearch_fetchSearchRecordsFull($searchId, $firstRecord, $recordCount, $recordCharset, true, true);
	}

	public function fetchSearchRecordsFullSorted($searchId, $firstRecord, $recordCount, $recordCharset='iso-8859-1', $sort_type="") {
		return $this->proxy_parent->pmbesSearch_fetchSearchRecordsFullSorted($searchId, $firstRecord, $recordCount, $recordCharset, true, true, $sort_type);
	}
	
	public function fetchSearchRecordsFullWithBullId($searchId, $firstRecord, $recordCount,  $recordCharset='iso-8859-1') {
		return $this->proxy_parent->pmbesSearch_fetchSearchRecordsFullWithBullId($searchId, $firstRecord, $recordCount, $recordCharset, true, true);
	}

	public function fetchSearchRecordsFullWithBullIdSorted($searchId, $firstRecord, $recordCount, $recordCharset='iso-8859-1', $sort_type="") {
		return $this->proxy_parent->pmbesSearch_fetchSearchRecordsFullWithBullIdSorted($searchId, $firstRecord, $recordCount, $recordCharset, true, true, $sort_type);
	}
	
	public function fetchSerialList() {
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC
		return $this->proxy_parent->pmbesNotices_fetchSerialList(0);
	}
	
	public function listExternalSources() {
		return $this->proxy_parent->pmbesSearch_listExternalSources(0);
	}
	
	public function listFacets($searchId, $fields = array(), $filters = array()) {
		return $this->proxy_parent->pmbesSearch_listFacets($searchId, $fields, $filters);
	}
	
	public function listRecordsFromFacets($searchId, $filters = array()) {
		return $this->proxy_parent->pmbesSearch_listRecordsFromFacets($searchId, $filters);
	}
}
?>