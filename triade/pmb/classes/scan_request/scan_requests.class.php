<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_requests.class.php,v 1.23 2023/07/10 11:47:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path.'/scan_request/scan_request.class.php');
require_once($class_path.'/scan_request/scan_request_statuses.class.php');
require_once($class_path.'/scan_request/scan_request_priorities.class.php');
require_once($include_path.'/h2o/pmb_h2o.inc.php');
require_once($include_path."/templates/scan_request/scan_requests.tpl.php");
require_once($include_path."/templates/scan_request/scan_request_parameters.tpl.php");

class scan_requests {
	
	/**
	 * Tableau des scan_requests de la liste
	 * @var scan_request
	 */
	protected $scan_requests;
	
	private $using_session_cache = true;
	
	public function __construct($using_session_cache=true) {
		$this->using_session_cache = $using_session_cache;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$this->scan_requests = array();
		
		$query = $this->get_query();
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				$this->scan_requests[] = new scan_request($row->id_scan_request);
			}
		}
	}

	protected function get_filters() {
		global $status_search, $priority_search, $user_input;
		global $scan_request_date_start, $scan_request_date_end;
		global $scan_request_wish_date_start, $scan_request_wish_date_end;
		global $scan_request_deadline_date_start, $scan_request_deadline_date_end;
		global $scan_request_num_location, $scan_request_user_only;
		global $PMBuserid;
		
		if(!isset($_SESSION['scan_requests_filter']['priority_search'])) {
			$_SESSION['scan_requests_filter']['priority_search'] = '';
		}
		if(!isset($_SESSION['scan_requests_filter']['scan_request_date_start'])) {
			$_SESSION['scan_requests_filter']['scan_request_date_start'] = '';
		}
		if(!isset($_SESSION['scan_requests_filter']['scan_request_date_end'])) {
			$_SESSION['scan_requests_filter']['scan_request_date_end'] = '';
		}
		if(!isset($_SESSION['scan_requests_filter']['user_input'])) {
			$_SESSION['scan_requests_filter']['user_input'] = '';
		}
		if(!isset($_SESSION['scan_requests_filter']['scan_request_wish_date_start'])) {
			$_SESSION['scan_requests_filter']['scan_request_wish_date_start'] = '';
		}
		if(!isset($_SESSION['scan_requests_filter']['scan_request_wish_date_end'])) {
			$_SESSION['scan_requests_filter']['scan_request_wish_date_end'] = '';
		}
		if(!isset($_SESSION['scan_requests_filter']['scan_request_deadline_date_start'])) {
			$_SESSION['scan_requests_filter']['scan_request_deadline_date_start'] = '';
		}
		if(!isset($_SESSION['scan_requests_filter']['scan_request_deadline_date_end'])) {
			$_SESSION['scan_requests_filter']['scan_request_deadline_date_end'] = '';
		}
		if(!isset($_SESSION['scan_requests_filter']['scan_request_num_location'])) {
			$_SESSION['scan_requests_filter']['scan_request_num_location'] = '';
		}
		
		if(!$this->using_session_cache){
			$tmp_buffer = $_SESSION['scan_requests_filter'];
		}
		
		$filter=' where 1 ';
		if(!isset($status_search) && !isset($_SESSION['scan_requests_filter']['status_search'])){
			$status_search = -1;
		}
		if(!isset($status_search)) $status_search = $_SESSION['scan_requests_filter']['status_search'];
		else $_SESSION['scan_requests_filter']['status_search'] = $status_search;
		if ($status_search == -1) {
			$filter.= ' and scan_request_num_status in (select id_scan_request_status from scan_request_status where scan_request_status_is_closed = 0)';
		} else if($status_search){
			$filter.= ' and scan_request_num_status = "'.$status_search.'" ';
		}
		if(isset($priority_search) || $_SESSION['scan_requests_filter']['priority_search']){
			if(!isset($priority_search)) $priority_search = $_SESSION['scan_requests_filter']['priority_search'];
			else $_SESSION['$priority_search']['priority_search'] = $priority_search;
			if($priority_search){
				$filter.=' and scan_request_num_priority = "'.$priority_search.'" ';
			}
		}
		if($scan_request_date_start || $_SESSION['scan_requests_filter']['scan_request_date_start']){
			if(!isset($scan_request_date_start)) $scan_request_date_start = $_SESSION['scan_requests_filter']['scan_request_date_start'];
			else $_SESSION['scan_requests_filter']['scan_request_date_start'] = $scan_request_date_start;
			$filter.=' and scan_request_date >= "'.$scan_request_date_start.'" ';
		}
		if($scan_request_date_end || $_SESSION['scan_requests_filter']['scan_request_date_end']){
			if(!isset($scan_request_date_end)) $scan_request_date_end = $_SESSION['scan_requests_filter']['scan_request_date_end'];
			else $_SESSION['scan_requests_filter']['scan_request_date_end'] = $scan_request_date_end;
			$filter.=' and scan_request_date <= "'.$scan_request_date_end.'" ';
		}
		if($scan_request_wish_date_start || $_SESSION['scan_requests_filter']['scan_request_wish_date_start']){
			if(!isset($scan_request_wish_date_start)) $scan_request_wish_date_start = $_SESSION['scan_requests_filter']['scan_request_wish_date_start'];
			else $_SESSION['scan_requests_filter']['scan_request_wish_date_start'] = $scan_request_wish_date_start;
			$filter.=' and scan_request_wish_date >= "'.$scan_request_wish_date_start.'" ';
		}
		if($scan_request_wish_date_end || $_SESSION['scan_requests_filter']['scan_request_wish_date_end']){
			if(!isset($scan_request_wish_date_end)) $scan_request_wish_date_end = $_SESSION['scan_requests_filter']['scan_request_wish_date_end'];
			else $_SESSION['scan_requests_filter']['scan_request_wish_date_end'] = $scan_request_wish_date_end;
			$filter.=' and scan_request_wish_date <= "'.$scan_request_wish_date_end.'" ';
		}
		if($scan_request_deadline_date_start || $_SESSION['scan_requests_filter']['scan_request_deadline_date_start']){
			if(!isset($scan_request_deadline_date_start)) $scan_request_deadline_date_start = $_SESSION['scan_requests_filter']['scan_request_deadline_date_start'];
			else $_SESSION['scan_requests_filter']['scan_request_deadline_date_start'] = $scan_request_deadline_date_start;
			$filter.=' and scan_request_deadline_date >= "'.$scan_request_deadline_date_start.'" ';
		}		
		if($scan_request_deadline_date_end || $_SESSION['scan_requests_filter']['scan_request_deadline_date_end']){
			if(!isset($scan_request_deadline_date_end)) $scan_request_deadline_date_end = $_SESSION['scan_requests_filter']['scan_request_deadline_date_end'];
			else $_SESSION['scan_requests_filter']['scan_request_deadline_date_end'] = $scan_request_deadline_date_end;
			$filter.=' and scan_request_deadline_date <= "'.$scan_request_deadline_date_end.'" ';
		}
		if($user_input || $_SESSION['scan_requests_filter']['user_input']){
			if(!isset($user_input)) $user_input = $_SESSION['scan_requests_filter']['user_input'];
			else $_SESSION['scan_requests_filter']['user_input'] = $user_input;
			$filter.=' and scan_request_title like "%'.$user_input.'%" ';
		}	
		if(isset($scan_request_num_location)) $_SESSION['scan_requests_filter']['scan_request_num_location']=$scan_request_num_location;	
		if($scan_request_num_location || $_SESSION['scan_requests_filter']['scan_request_num_location']){
			if(!isset($scan_request_num_location)) $scan_request_num_location = $_SESSION['scan_requests_filter']['scan_request_num_location'];
			else $_SESSION['scan_requests_filter']['scan_request_num_location'] = $scan_request_num_location;
			$filter.=' and scan_request_num_location = "'.$scan_request_num_location.'" ';
		}		
		if(!isset($scan_request_user_only)) $_SESSION['scan_requests_filter']['scan_request_user_only']='';
		if($scan_request_user_only || $_SESSION['scan_requests_filter']['scan_request_user_only']){
			if(!isset($scan_request_user_only)) $scan_request_user_only = $_SESSION['scan_requests_filter']['scan_request_user_only'];
			else $_SESSION['scan_requests_filter']['scan_request_user_only'] = $scan_request_user_only;
			$filter.=' and scan_request_num_creator = "'.$PMBuserid.'" and scan_request_type_creator=1';
		}
		if(!$this->using_session_cache){
			$_SESSION['scan_requests_filter'] = $tmp_buffer;
		}
		
		return $filter;
	}

	protected function get_query() {
		global $scan_request_order_by,  $scan_request_order_by_sens;
		
		if(!isset($_SESSION['scan_requests_filter']['scan_request_order_by'])) {
			$_SESSION['scan_requests_filter']['scan_request_order_by'] = '';
		}
		if(!isset($_SESSION['scan_requests_filter']['scan_request_order_by_sens'])) {
			$_SESSION['scan_requests_filter']['scan_request_order_by_sens'] = '';
		}
		
		if(!isset($scan_request_order_by)) $scan_request_order_by = $_SESSION['scan_requests_filter']['scan_request_order_by'];
		else $_SESSION['scan_requests_filter']['scan_request_order_by'] = $scan_request_order_by;
		if(!isset($scan_request_order_by_sens)) $scan_request_order_by_sens = $_SESSION['scan_requests_filter']['scan_request_order_by_sens'];
		else $_SESSION['scan_requests_filter']['scan_request_order_by_sens'] = $scan_request_order_by_sens;
		
		switch ($scan_request_order_by){
			case 'title':
				$query='select id_scan_request from scan_requests '.$this->get_filters().' order by scan_request_title ';
				break;
			case 'date':
				$query='select id_scan_request from scan_requests '.$this->get_filters().' order by scan_request_date ';
				break;
			case 'wish_date':
				$query='select id_scan_request from scan_requests '.$this->get_filters().' order by scan_request_wish_date ';
				break;
			case 'deadline_date':
				$query='select id_scan_request from scan_requests '.$this->get_filters().' order by scan_request_deadline_date ';
				break;
			case 'priority':
				$query='select id_scan_request from scan_requests join scan_request_priorities on id_scan_request_priority = scan_request_num_priority '.$this->get_filters().' order by scan_request_priority_weight ';
				break;
			case 'empr':
				$query='select id_scan_request from scan_requests left join empr on scan_request_num_dest_empr = id_empr '.$this->get_filters().' order by empr_nom '.$scan_request_order_by_sens.', empr_prenom ';		
				break;
			case 'status':
			default:
				$query='select id_scan_request from scan_requests join scan_request_status on id_scan_request_status = scan_request_num_status '.$this->get_filters().' order by scan_request_status_label asc, scan_request_wish_date ';
				$scan_request_order_by_sens = 'desc';
				break;						 
		}
		$query.= $scan_request_order_by_sens;
		return $query;		
	}
	
	public function get_display_list() {
		global $msg, $include_path;
		global $scan_requests_list;
		global $status_search, $priority_search, $user_input;
		global $scan_request_date_start, $scan_request_date_end;
		global $scan_request_wish_date_start, $scan_request_wish_date_end;
		global $scan_request_deadline_date_start, $scan_request_deadline_date_end;
		global $scan_request_order_by, $scan_request_order_by_sens;
		global $pmb_scan_request_location_activate, $scan_request_num_location, $scan_request_user_only;		
		
		$display=$scan_requests_list;
		$display = str_replace('!!option_status_search!!', '<option value="-1" '.(($status_search == -1) ? 'selected="selected"' : '').'>'.$msg['scan_request_list_statuses_selector_open'].'</option><option value="0" '.((!$status_search) ? 'selected="selected"' : '').'>'.$msg['scan_request_list_statuses_selector_all'].'</option>'.scan_request_statuses::get_options($status_search), $display);
		$display = str_replace('!!option_priority_search!!', '<option value="0">'.$msg['scan_request_list_priorities_selector_all'].'</option>'.scan_request_priorities::get_options($priority_search), $display);
		$display = str_replace('!!scan_request_date_start!!', $scan_request_date_start, $display);
		$display = str_replace('!!scan_request_date_end!!', $scan_request_date_end, $display);
		$display = str_replace('!!scan_request_wish_date_start!!', $scan_request_wish_date_start, $display);
		$display = str_replace('!!scan_request_wish_date_end!!', $scan_request_wish_date_end, $display);
		$display = str_replace('!!scan_request_deadline_date_start!!', $scan_request_deadline_date_start, $display);
		$display = str_replace('!!scan_request_deadline_date_end!!', $scan_request_deadline_date_end, $display);
		$display = str_replace('!!scan_request_order_by!!', $scan_request_order_by, $display);
		if(!$scan_request_order_by_sens)	$scan_request_order_by_sens='asc';
		$display = str_replace('!!scan_request_order_by_sens!!', $scan_request_order_by_sens, $display);
		
		if($scan_request_user_only) {
			$display = str_replace('!!scan_request_user_only!!', "checked='checked'", $display);
		} else {
			$display = str_replace('!!scan_request_user_only!!', "", $display);			
		}
		$display = str_replace('!!user_input!!', $user_input, $display);
		$display = str_replace('!!action!!', './circ.php?categ=scan_request&sub=list', $display);
		
		if($pmb_scan_request_location_activate) {
			$display = str_replace("!!scan_request_location_selector!!",gen_liste("select idlocation, location_libelle from docs_location order by location_libelle ", "idlocation", "location_libelle", 'scan_request_num_location', "", $scan_request_num_location+0, "", "", "0", $msg['all_location'],0),$display);
		}else {
			$display = str_replace("!!scan_request_location_selector!!", "", $display);
		}
		if(count($this->scan_requests)) {
			$tpl = $include_path.'/templates/scan_request/scan_requests_list.tpl.html';
			if (file_exists($include_path.'/templates/scan_request/scan_requests_list_subst.tpl.html')) {
				$tpl = $include_path.'/templates/scan_request/scan_requests_list_subst.tpl.html';
			}
			$h2o = H2o_collection::get_instance($tpl);
			$list = $h2o->render(array('scan_requests' => $this));
		} else {
			$list = $msg['scan_request_list_empty'];	
		}
		$display = str_replace('!!scan_requests_list!!', $list, $display);
		return $display;
	}
	
	public function get_scan_requests() {
		return $this->scan_requests;
	}
	
	public function has_scan_requests_on_record($record_id, $record_type) {
		foreach ($this->scan_requests as $scan_request) {
			$linked_records = $scan_request->get_linked_records();
			foreach ($linked_records as $linked_record) {
				if(($record_type == 'bulletins') && ($record_id == $linked_record['bulletin_id'])) {
					return true;
				} elseif(($record_type == 'notices') && ($record_id == $linked_record['notice_id'])) {
					return true;
				}
			}
		}
		return false;
	}	
	
	public static function clean_scan_requests_on_delete_record($notice_id = 0, $bulletin_id = 0) {
		if($notice_id){
			$linked_query = 'delete from scan_request_linked_records where scan_request_linked_record_num_notice ='.$notice_id;			
		}elseif($bulletin_id){
			$linked_query = 'delete from scan_request_linked_records where scan_request_linked_record_num_bulletin ='.$bulletin_id;			
		}
		if($linked_query)pmb_mysql_query($linked_query);		
	}
	
	public static function get_admin_content_form(){
		global $charset;
		global $msg;
		global $pmb_scan_request_explnum_folder;
		
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->set_grid_model('flat_column_3');
		$req="select repertoire_id, repertoire_nom from upload_repertoire order by repertoire_nom";
		$res = pmb_mysql_query($req);
		if(pmb_mysql_num_rows($res)){
			$interface_content_form->add_element('scan_request_folder_param', 'upload_repertoire_selection')
			->add_query_node('select', 'select repertoire_id as id, repertoire_nom as label from upload_repertoire order by label', $pmb_scan_request_explnum_folder);
		} else {
			$interface_content_form->add_element('scan_request_folder_param', 'upload_repertoire_selection')
			->add_html_node("<label>".htmlentities($msg['upload_repertoire_undefined'],ENT_QUOTES,$charset)."</label>");
		}
		return $interface_content_form->get_display();
	}
	
	public static function get_admin_form(){
		global $msg;
		
		$interface_form = new interface_admin_form('scan_request_parameters_form');
		$interface_form->set_label($msg['scan_request_admin_parameters_form'])
		->set_content_form(static::get_admin_content_form());
		return $interface_form->get_display_parameters();
	}
	
	public static function save_admin_form(){
		global $scan_request_folder_param;
		global $pmb_scan_request_explnum_folder;
		
		$scan_request_folder_param = intval($scan_request_folder_param); 
		$query = 'update parametres set valeur_param="'.$scan_request_folder_param.'" where type_param = "pmb" and sstype_param= "scan_request_explnum_folder"; ';
		$result = pmb_mysql_query($query);
		if($result){
			$pmb_scan_request_explnum_folder = $scan_request_folder_param; 
			return true;
		}
		return false;
	}
	
	/**
	 * Suppression des demandes associées
	 * @param number $num_creator
	 * @param number $type_creator (1 = User, 2 = Empr)
	 */
	public static function delete_from_creator($num_creator=0, $type_creator=2) {
	    $num_creator = intval($num_creator);
	    $type_creator = intval($type_creator);
	    $query = "select id_scan_request from scan_requests where scan_request_num_creator = ".$num_creator." and scan_request_type_creator = ".$type_creator;
	    $result = pmb_mysql_query($query);
	    while($row = pmb_mysql_fetch_object($result)) {
	        $scan_request = new scan_request($row->id_scan_request);
	        $scan_request->delete(true);
	    }
	}
}