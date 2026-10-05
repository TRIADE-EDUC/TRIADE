<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request.class.php,v 1.33 2024/04/10 14:26:32 pmallambic Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path.'/scan_request/scan_request_status.class.php');
require_once($class_path.'/scan_request/scan_request_priorities.class.php');
require_once($class_path.'/scan_request/scan_request_priority.class.php');
include_once($include_path.'/templates/scan_request.tpl.php');
include_once($include_path.'/notice_affichage.inc.php');
include_once($include_path.'/bulletin_affichage.inc.php');
require_once($class_path.'/scan_request/scan_requests.class.php');
require_once($class_path.'/acces.class.php');
require_once($class_path.'/docs_location.class.php');

class scan_request {
	protected $id;

	protected $title = '';

	protected $desc = '';

	protected $status = null;

	protected $priority = null;

	protected $create_date = null;

	protected $update_date = null;

	protected $date = null;

	protected $wish_date = null;

	protected $deadline_date = null;

	protected $comment = '';

	protected $elapsed_time = 0;

	protected $num_dest_empr = 0;

	protected $num_creator = 0;

	protected $type_creator = 0;

	protected $num_last_user = 0;

	protected $state = 0;
	
	protected $num_location = 0;
	
	protected $linked_records = array();

	protected $formatted_update_date = null;

	protected $formatted_date = null;

	protected $formatted_wish_date = null;

	protected $formatted_deadline_date = null;
	
	protected $nb_scanned_pages = 0;
	
	protected static $scripts_already_included = false;
	
	protected $scannable_linked_record = false;
	
	protected $nb_explnums = 0;
	
	public function __construct($id = 0) {
		$this->id = intval($id);
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		global $empr_location;
		
		$this->num_location = $empr_location;
		if ($this->id) {
			$query = 'select * from scan_requests where id_scan_request = '.$this->id;
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->title = $row->scan_request_title;
				$this->desc = $row->scan_request_desc;
				$this->status = new scan_request_status($row->scan_request_num_status);
				$this->priority = new scan_request_priority($row->scan_request_num_priority);
				$this->create_date = $row->scan_request_create_date;
				$this->update_date = $row->scan_request_update_date;
				$this->date = $row->scan_request_date;
				$this->wish_date = $row->scan_request_wish_date;
				$this->deadline_date = $row->scan_request_deadline_date;
				$this->comment = $row->scan_request_comment;
				$this->elapsed_time = $row->scan_request_elapsed_time;
				$this->num_dest_empr = $row->scan_request_num_dest_empr;
				$this->num_creator = $row->scan_request_num_creator;
				$this->type_creator = $row->scan_request_type_creator;
				$this->num_last_user = $row->scan_request_num_last_user;
				$this->state = $row->scan_request_state;
				$this->num_location = $row->scan_request_num_location;
				$this->formatted_update_date = formatdate($this->update_date);
				$this->formatted_date = formatdate($this->date);
				$this->formatted_wish_date = formatdate($this->wish_date);
				$this->formatted_deadline_date = formatdate($this->deadline_date);
				$this->nb_scanned_pages = $row->scan_request_nb_scanned_pages;
				
				$query = 'select * from scan_request_linked_records where scan_request_linked_record_num_request = '.$this->id.' order by scan_request_linked_record_order';
				$result = pmb_mysql_query($query);
				while ($row = pmb_mysql_fetch_object($result)) {
					$this->add_linked_record($row->scan_request_linked_record_num_notice, $row->scan_request_linked_record_num_bulletin, $row->scan_request_linked_record_comment);
				}
			}
		}
	}
	
	protected function get_rights_linked_record($notice_id = 0, $bulletin_id = 0) {
		global $gestion_acces_active,$gestion_acces_empr_notice;
		
		$rights = array(
			'visible' => false,
			'scannable' => false		
		);
		$id_for_right = 0;
		if($bulletin_id) {
			$query = "select num_notice,bulletin_notice from bulletins where bulletin_id = ".$bulletin_id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$infos = pmb_mysql_fetch_object($result);
				if($infos->num_notice){
					//notice de bulletin
					$id_for_right = $infos->num_notice;
				}else{
					//notice de pério
					$id_for_right = $infos->bulletin_notice;
				}
			}
		} else {
			$id_for_right = $notice_id;
		}
		if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
			$ac = new acces();
			$dom_2= $ac->setDomain(2);
			if($dom_2->getRights($_SESSION['id_empr_session'],$id_for_right, 4)) {
				$rights['visible'] = true;
			}	
			if($dom_2->getRights($_SESSION['id_empr_session'],$id_for_right, 32)) {
				$rights['scannable'] = true;
			}
		} else {
			$query = "SELECT notice_visible_opac, notice_visible_opac_abon, notice_scan_request_opac, notice_scan_request_opac_abon FROM notice_statut JOIN notices ON notices.statut = notice_statut.id_notice_statut WHERE notice_id='".$id_for_right."' ";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				if($row->notice_visible_opac && (!$row->notice_visible_opac_abon || ($row->notice_visible_opac_abon && $_SESSION['id_empr_session']))) {
					$rights['visible'] = true;
				}
				if($row->notice_scan_request_opac && (!$row->notice_scan_request_opac_abon || ($row->notice_scan_request_opac_abon && $_SESSION['id_empr_session']))) {
					$rights['scannable'] = true;
				}
			}
		}
		return $rights;
	}
	
	protected function add_linked_record($notice_id, $bulletin_id, $comment = '') {
		global $opac_url_base;
		
		$rights = $this->get_rights_linked_record($notice_id, $bulletin_id);
		if($rights['visible']) {
			if ($notice_id) {
				$display = aff_notice($notice_id, 0, 1, 0, AFF_ETA_NOTICES_REDUIT, '', 1, 0);
				$explnums_datas = $this->get_explnums_from_record($notice_id);
			} else {
				$display = "<a href='".$opac_url_base."index.php?lvl=bulletin_display&id=".$bulletin_id."'>".bulletin_header($bulletin_id)."</a><br />";
				$explnums_datas = $this->get_explnums_from_record($bulletin_id, 'bulletins');
			}
			$this->linked_records[] = array(
					'notice_id' => $notice_id,
					'bulletin_id' => $bulletin_id,
					'display' => $display,
					'comment' => $comment,
					'explnums_datas' => $explnums_datas,
					'scannable' => $rights['scannable'],
					'order' => ''
			);
			$this->nb_explnums+= $explnums_datas['nb_explnums'];
			if ($rights['scannable'] && !$this->scannable_linked_record) {
				$this->scannable_linked_record = true;
			}
		}
	}
	
	public function add_linked_records($objects_ids) {
		if(is_array($objects_ids) && count($objects_ids)) {
			foreach($objects_ids as $type=>$object_ids) {
				foreach ($object_ids as $object_id) {
					if($type == 'notices') {
						$this->add_linked_record($object_id, 0);
					} elseif($type == 'bulletins') {
						$this->add_linked_record(0, $object_id);
					}
				}
			}
		}
	}
	
	public function get_values_from_form() {
	    global $scan_request_title, $scan_request_desc, $scan_request_nb_scanned_pages, $scan_request_num_location, $scan_request_comment;
		global $scan_request_priority, $scan_request_status, $scan_request_date, $scan_request_wish_date, $scan_request_deadline_date;
		global $scan_request_linked_records_notices, $scan_request_linked_records_bulletins;
		global $empr_location;
		
		$this->title = strip_tags(stripslashes($scan_request_title));
		$this->desc = strip_tags(stripslashes($scan_request_desc));
		$this->nb_scanned_pages = strip_tags(stripslashes($scan_request_nb_scanned_pages));
		$this->num_location = (!empty($scan_request_num_location) ? intval($scan_request_num_location) : $empr_location);
		$this->priority = new scan_request_priority(intval($scan_request_priority));
		$this->status = new scan_request_status(intval($scan_request_status));
		$this->date = $scan_request_date;
		$this->wish_date = $scan_request_wish_date;
		$this->deadline_date = $scan_request_deadline_date;
		$this->update_date = date('Y-m-d');
		$this->formatted_date = formatdate($this->date);
		$this->formatted_wish_date = formatdate($this->wish_date);
		$this->formatted_deadline_date = formatdate($this->deadline_date);
		$this->formatted_update_date = format_date($this->update_date);
		$this->num_dest_empr = $_SESSION['id_empr_session'];
		$this->linked_records = array();
		if(is_array($scan_request_linked_records_notices)) {
			foreach ($scan_request_linked_records_notices as $notice_id => $notice) {
				$this->add_linked_record($notice_id, 0, stripslashes($notice['comment'])); 
			}
		}
		if(is_array($scan_request_linked_records_bulletins)) {
			foreach ($scan_request_linked_records_bulletins as $bulletin_id => $bulletin) {
				$this->add_linked_record(0, $bulletin_id, stripslashes($bulletin['comment']));
			}
		}
	}
	
	public function send_mail(){
		$mail_opac_scan_request = new mail_opac_scan_request();
		$mail_opac_scan_request->set_mail_to_id($this->num_location);
		$mail_opac_scan_request->set_scan_request($this);
		$mail_opac_scan_request->send_mail();
	}
	
	public function save() {
		if($this->id) {
			$query = 'update scan_requests set ';
			$where = 'where id_scan_request='.$this->id;
		} else {
			$query = 'insert into scan_requests set ';
			$query .= 'scan_request_create_date=now(),
					scan_request_num_creator='.$this->num_dest_empr.',
					scan_request_num_location='.$this->num_location.',
					scan_request_num_dest_empr='.$this->num_dest_empr.',
					scan_request_type_creator=2,
					scan_request_num_status='.$this->status->get_id().',';
			$where = '';
		}
		$query .= 'scan_request_title="'.addslashes($this->title).'",
				scan_request_desc="'.addslashes($this->desc).'",
                scan_request_nb_scanned_pages = "'.addslashes($this->nb_scanned_pages).'",
				scan_request_num_priority="'.$this->priority->get_id().'",
				scan_request_date="'.$this->date.'",
				scan_request_wish_date="'.$this->wish_date.'",
				scan_request_deadline_date="'.$this->deadline_date.'",
				scan_request_update_date=now()
				';
		$query .= $where;
		$result = pmb_mysql_query($query);
		
		if ($result) {
			// On sauve les documents liés
			$is_new = false;
			if (!$this->id) {
				$is_new = true;
				$this->id = pmb_mysql_insert_id();
			}
			//Envoi du mail en création/modification
			$this->send_mail();
			foreach ($this->linked_records as $linked_record) {
				if($linked_record['scannable']) {
					$result = $this->_save_linked_record($linked_record, $is_new);
					if(!$result) return false;
				}
			} 
			return true;
		}
		return false;
	}
	
	protected function _save_linked_record($linked_record, $is_new) {
		
		if($is_new) {
			$query = 'insert into scan_request_linked_records set 
					scan_request_linked_record_num_request="'.$this->id.'",
					scan_request_linked_record_num_notice="'.$linked_record['notice_id'].'",
					scan_request_linked_record_num_bulletin="'.$linked_record['bulletin_id'].'",
					scan_request_linked_record_comment="'.addslashes($linked_record['comment']).'",
					scan_request_linked_record_order="'.$linked_record['order'].'"';
		} else {
			$query = 'update scan_request_linked_records set 
					scan_request_linked_record_comment="'.addslashes($linked_record['comment']).'",
					scan_request_linked_record_order="'.$linked_record['order'].'"
					where scan_request_linked_record_num_request="'.$this->id.'"
					and scan_request_linked_record_num_notice="'.$linked_record['notice_id'].'"
					and scan_request_linked_record_num_bulletin="'.$linked_record['bulletin_id'].'"';		
		}
		$result = pmb_mysql_query($query);
		return $result;
	}
	
	/**
	 * Ajoute les enregistrements en séparant notices et bulletins à partir d'un tableau d'identifiant de notices
	 * @param array $records_ids Tableau des identifiants de notices
	 */
	public function add_linked_records_from_notices_ids($notices_ids) {
		if (count($notices_ids)) {
			$query = 'select notice_id, bulletin_id, niveau_biblio from notices left join bulletins on notices.notice_id = bulletins.num_notice where notices.notice_id in ('.implode(',', $notices_ids).')';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					if ($row->niveau_biblio == 'b') {
						$this->add_linked_record(0, $row->bulletin_id);
					} else {
						$this->add_linked_record($row->notice_id, 0);
					}
				}
			}
		}
	}
	
	public function get_form() {
		global $charset, $msg;
		global $scan_request_form, $scan_request_form_scripts;
		
		$display = $scan_request_form;
		
		if($this->id){
			$display = str_replace('!!form_title!!', htmlentities($msg['scan_request_edit_form'], ENT_QUOTES, $charset), $display);
		} else {
			$display = str_replace('!!form_title!!', htmlentities($msg['scan_request_create_form'], ENT_QUOTES, $charset), $display);
		}
		$display = str_replace('!!id!!', $this->id, $display);
		
		$display = str_replace('!!form_content!!', $this->get_form_content(), $display);
		
		return $display.$scan_request_form_scripts;
	}
	
	public function get_link_in_record($record_id, $record_type = 'notices') {
		global $msg, $charset;
		global $base_path;
		global $scan_request_link_in_record;
		global $allow_scan_request;
		
		$display = '';
		if ($_SESSION['id_empr_session'] && $allow_scan_request) {
			$display = $scan_request_link_in_record;
			
			$scan_requests_on_record = scan_requests::get_scan_requests_on_record($_SESSION['id_empr_session'], $record_id, $record_type);
			$scan_requests_already_exist = '';
			if (count($scan_requests_on_record) == 1) {
				$scan_requests_already_exist = $msg['scan_request_on_record_already_exists'].' '.$msg['scan_request_saved_see_link'];
				$scan_requests_already_exist = str_replace('!!link!!', $base_path.'/empr.php?tab=scan_requests&lvl=scan_request&sub=display&id='.$scan_requests_on_record[0], $scan_requests_already_exist);
			} else if (count($scan_requests_on_record) > 1) {
				$scan_requests_already_exist = $msg['scan_requests_on_record_already_exist'].' '.$msg['scan_requests_saved_see_link'];
				$scan_requests_already_exist = str_replace('!!link!!', $base_path.'/empr.php?tab=scan_requests&lvl=scan_requests_list', $scan_requests_already_exist);
			}
			if($scan_requests_already_exist) {
				$display = str_replace('!!scan_requests_already_exist!!', "<div class='scan_requests_already_exist'>".$scan_requests_already_exist."</div>", $display);
			} else {
				$display = str_replace('!!scan_requests_already_exist!!', "", $display);
			}
			$display = str_replace('!!record_id!!', $record_id, $display);
			$id_suffix = '_'.$record_type.'_'.$record_id;
			$display = str_replace('!!id_suffix!!', htmlentities($id_suffix, ENT_QUOTES, $charset), $display);
			$display = str_replace('!!record_type!!', $record_type, $display);
		}
		return $display;
	}
	
	public function get_form_in_record($record_id, $record_type = 'notices') {
		global $charset, $msg;
		global $scan_request_form_in_record, $scan_request_form_in_record_scripts;
		global $base_path;
		global $opac_rgaa_active;
		
		$display = '';
		if ($_SESSION['id_empr_session']) {
			$display = $scan_request_form_in_record;
			if($opac_rgaa_active){
				$display = str_replace("<!--bouton close-->","<button type='button' class='button-unstylized close' onClick='parent.kill_scan_request_frame();return false;'><img src='".get_url_icon('close.gif')."' alt='' class='align_right'><span class='visually-hidden'>".$msg['close'] . $msg['rgaa_modal_close']."</span></button></div>", $display);
			}else{
				$display = str_replace("<!--bouton close-->","<a href='#' onClick='parent.kill_scan_request_frame();return false;'><img src='".get_url_icon('close.gif')."' alt='".$msg["close"]."' style='border:0px' class='align_right'></a></div>", $display);
			}
			if($this->id){
				$display = str_replace('!!form_title!!', htmlentities($msg['scan_request_edit_form'], ENT_QUOTES, $charset), $display);
			} else {
				$display = str_replace('!!form_title!!', htmlentities($msg['scan_request_create_form'], ENT_QUOTES, $charset), $display);
			}
			$id_suffix = '_'.$record_type.'_'.$record_id;
			$display = str_replace('!!id_suffix!!', htmlentities($id_suffix, ENT_QUOTES, $charset), $display);
			$display = str_replace('!!record_type!!', $record_type, $display);
			$display = str_replace('!!record_id!!', $record_id, $display);
			$display = str_replace('!!form_content!!', $this->get_form_content($id_suffix), $display);
		}
		if (!self::$scripts_already_included) {
 			$display.= $scan_request_form_in_record_scripts;
			self::$scripts_already_included = true;
		}
		return $display;
	}
	
	public function get_form_content($id_suffix = '') {
		global $charset, $msg;
		global $scan_request_form_content, $opac_scan_request_create_status, $scan_request_linked_record;
		global $opac_scan_request_location_activate, $empr_location;
		
		$display = $scan_request_form_content;

		$display = str_replace('!!title!!', htmlentities($this->title, ENT_QUOTES, $charset), $display);
		$display = str_replace('!!desc!!', htmlentities($this->desc, ENT_QUOTES, $charset), $display);
		
		$selected_priority = 0;
		if ($this->priority) {
			$selected_priority = $this->priority->get_id();
		}
		$scan_request_priorities = new scan_request_priorities();
		$display = str_replace('!!priority!!', $scan_request_priorities->get_selector_options($selected_priority), $display);
		$display = str_replace('!!date!!', ($this->date ? substr($this->date,0,10) : date('Y-m-d')), $display);
		$display = str_replace('!!wish_date!!', ($this->wish_date ? substr($this->wish_date,0,10) : date('Y-m-d')), $display);
		$display = str_replace('!!deadline_date!!', ($this->deadline_date ? substr($this->deadline_date,0,10) : date('Y-m-d')), $display);
		$display = str_replace('!!nb_scanned_pages!!', $this->nb_scanned_pages, $display);
		
		if($opac_scan_request_location_activate) {
			$display = str_replace("!!location_selector!!",gen_liste ("select idlocation, location_libelle from docs_location where location_visible_opac = 1 order by location_libelle ", "idlocation", "location_libelle", 'scan_request_num_location'.$id_suffix, "", $this->num_location, "", "", "", $msg['no_location'],0), $display);
		}else {
			$display = str_replace("!!location_selector!!", "", $display);
		}
		
		if($this->status) {
			$status = $this->status->get_id();
		} else {
			$status = $opac_scan_request_create_status;
		}
		$display = str_replace('!!status!!', $status, $display);
		$display = str_replace('!!id!!', $this->id, $display);
		
		$linked_records_display = '';
		if(count($this->linked_records)) {
			foreach ($this->linked_records as $linked_record) {
				$linked_record_display = $scan_request_linked_record;
				if(!$linked_record['scannable']) {
					$expand_invisible = 'style="visibility:hidden;"';
					$label = '<del title="'.$msg['scan_request_linked_record_no_scannable'].'">'.strip_tags($linked_record['display']).'</del>';
				} else {
					$expand_invisible = '';
					$label = strip_tags($linked_record['display']);
				}
				$linked_record_display = str_replace('!!expand_invisible!!', $expand_invisible, $linked_record_display);
				$linked_record_display = str_replace('!!linked_record_display!!', $label, $linked_record_display);
				if ($linked_record['notice_id']) {
					$linked_record_type = 'notices';
					$linked_record_id = $linked_record['notice_id'];
				} else {
					$linked_record_type = 'bulletins';
					$linked_record_id = $linked_record['bulletin_id'];
				}
				$linked_record_display = str_replace('!!linked_record_type!!', $linked_record_type, $linked_record_display);
				$linked_record_display = str_replace('!!linked_record_id!!', $linked_record_id, $linked_record_display);
				$linked_record_display = str_replace('!!linked_record_comment!!', $linked_record['comment'], $linked_record_display);
				$linked_records_display.= $linked_record_display;
			}
		} else {
			$linked_records_display = $msg['scan_request_linked_records_unavailable'];
		}
		$display = str_replace('!!linked_records!!', $linked_records_display, $display);
		$display = str_replace('!!id_suffix!!', htmlentities($id_suffix, ENT_QUOTES, $charset), $display);
		
		return $display;
	}
	
	public function get_display() {
		global $include_path;
		
		$tpl = $include_path.'/templates/scan_request/scan_request.tpl.html';
		if (file_exists($include_path.'/templates/scan_request/scan_request_subst.tpl.html')) {
			$tpl = $include_path.'/templates/scan_request/scan_request_subst.tpl.html';
		}
		$h2o = H2o_collection::get_instance($tpl);
		return $h2o->render(array('scan_request' => $this));
	}
	
	public function get_display_in_list() {
	    global $include_path;
	    
	    $tpl = $include_path.'/templates/scan_request/scan_request_in_list.tpl.html';
	    if (file_exists($include_path.'/templates/scan_request/scan_request_in_list_subst.tpl.html')) {
	        $tpl = $include_path.'/templates/scan_request/scan_request_in_list_subst.tpl.html';
	    }
	    $h2o = H2o_collection::get_instance($tpl);
	    $empr = '';
	    if ($this->num_dest_empr) {
	        $query = 'select empr_nom, empr_prenom from empr where id_empr = '.$this->num_dest_empr;
	        $result = pmb_mysql_query($query);
	        if (pmb_mysql_num_rows($result)) {
	            $row = pmb_mysql_fetch_object($result);
	            $empr = $row->empr_nom;
	            if($row->empr_prenom) $empr .= ', '.$row->empr_prenom;
	        }
	    }
	    return $h2o->render(array('scan_request' => $this, 'empr' => $empr));
	}
	
	public function delete() {
		global $opac_scan_request_cancel_status;
		
		if($this->id && $opac_scan_request_cancel_status){
			$query = 'update scan_requests set scan_request_num_status="'.$opac_scan_request_cancel_status.'" where id_scan_request = '.$this->id;
			pmb_mysql_query($query);
		}
	}
	
	public function get_explnums_from_record($record_id, $record_type = 'notices') {
		global $charset;
		global $opac_url_base;
		global $opac_visionneuse_allow;
		global $opac_photo_filtre_mimetype;
		global $opac_explnum_order;
		global $opac_show_links_invisible_docnums;
		global $gestion_acces_active,$gestion_acces_empr_notice,$gestion_acces_empr_docnum;
		
		//droits d'acces emprunteur/notice
		if ($gestion_acces_active==1 && ($gestion_acces_empr_notice || $gestion_acces_empr_docnum)) {
			$ac= new acces();
			if ($gestion_acces_empr_notice == 1) {
				if($record_type == 'notices') {
					$dom_2= $ac->setDomain(2);
					$rights= $dom_2->getRights($_SESSION['id_empr_session'], $record_id);
				}
			}
			if ($gestion_acces_empr_docnum == 1) {
				$dom_3= $ac->setDomain(3);
			}
		}
		
		$explnums = array(
				'nb_explnums' => 0,
				'explnums' => array(),
				'visionneuse_script' => '
							<script>
								if(typeof(sendToVisionneuse) == "undefined"){
									var sendToVisionneuse = function (infos){
										document.getElementById("visionneuseIframe").src = "visionneuse.php?mode=scan_request"+(typeof(infos.explnum_id) != "undefined" ? "&explnum_id="+infos.explnum_id : "")+(typeof(infos.id) != "undefined" ? "&id="+infos.id : "")+(typeof(infos.record_id) != "undefined" ? "&record_id="+infos.record_id : "")+(typeof(infos.record_type) != "undefined" ? "&record_type="+infos.record_type : "");
									}
					
								}
							</script>'
		);

		global $_mimetypes_bymimetype_, $_mimetypes_byext_ ;
		if (empty($_mimetypes_bymimetype_)) {
			create_tableau_mimetype();
		}
		
		// récupération du nombre d'exemplaires
		$query = "SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_vignette, explnum_nomfichier, explnum_extfichier, explnum_docnum_statut 
				FROM explnum 
				JOIN scan_request_explnum ON scan_request_explnum.scan_request_explnum_num_explnum = explnum.explnum_id 
				WHERE scan_request_explnum_num_request = '".$this->id."'";
		if($record_type == 'bulletins') {
			$query .= " and scan_request_explnum_num_bulletin = '".$record_id."'";
		} else {
			$query .= " and scan_request_explnum_num_notice = '".$record_id."'";
		}
		if ($opac_explnum_order) $query .= " order by ".$opac_explnum_order;
		else $query .= " order by explnum_mimetype, explnum_nom, explnum_id ";
		$res = pmb_mysql_query($query);
		$nb_explnums = pmb_mysql_num_rows($res);

		$docnum_visible = true;
		if($record_type == 'notices') {
			if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
				$docnum_visible = $dom_2->getRights($_SESSION['id_empr_session'],$record_id,16);
			} else {
				$query = "SELECT explnum_visible_opac, explnum_visible_opac_abon FROM notices, notice_statut WHERE notice_id ='".$record_id."' and id_notice_statut=statut ";
				$result = pmb_mysql_query($query);
				if($result && pmb_mysql_num_rows($result)) {
					$statut_temp = pmb_mysql_fetch_object($result);
					if(!$statut_temp->explnum_visible_opac)	$docnum_visible=false;
					if($statut_temp->explnum_visible_opac_abon && !$_SESSION['id_empr_session'])	$docnum_visible=false;
				} else 	$docnum_visible=false;
			}
		}

		if ($nb_explnums && ($docnum_visible || $opac_show_links_invisible_docnums)) {
			// on récupère les données des exemplaires
			global $search_terms;
			while (($expl = pmb_mysql_fetch_object($res))) {
				$explnum_docnum_visible = true;
				$explnum_docnum_consult = true;
				if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
					$explnum_docnum_visible = $dom_3->getRights($_SESSION['id_empr_session'],$expl->explnum_id,16);
					$explnum_docnum_consult = $dom_3->getRights($_SESSION['id_empr_session'],$expl->explnum_id,4);
				} else {
					$requete = "SELECT explnum_visible_opac, explnum_visible_opac_abon, explnum_consult_opac, explnum_consult_opac_abon FROM explnum, explnum_statut WHERE explnum_id ='".$expl->explnum_id."' and id_explnum_statut=explnum_docnum_statut ";
					$myQuery = pmb_mysql_query($requete);
					if(pmb_mysql_num_rows($myQuery)) {
						$statut_temp = pmb_mysql_fetch_object($myQuery);
						if(!$statut_temp->explnum_visible_opac)	{
							$explnum_docnum_visible=false;
						}
						if(!$statut_temp->explnum_consult_opac)	{
							$explnum_docnum_consult=false;
						}
						if($statut_temp->explnum_visible_opac_abon && !$_SESSION['id_empr_session'])	$explnum_docnum_visible=false;
						if($statut_temp->explnum_consult_opac_abon && !$_SESSION['id_empr_session'])	$explnum_docnum_consult=false;
					} else {
						$explnum_docnum_visible=false;
					}
				}
				if ($explnum_docnum_visible ||  $opac_show_links_invisible_docnums) {
					$explnums['nb_explnums']++;
					$explnum_datas = array(
							'id' => $expl->explnum_id,
							'name' => $expl->explnum_nom,
							'mimetype' => $expl->explnum_mimetype,
							'url' => $expl->explnum_url,
							'filename' => $expl->explnum_nomfichier,
							'extension' => $expl->explnum_extfichier,
							'statut' => $expl->explnum_docnum_statut,
							'consultation' => $explnum_docnum_consult
					);

					if ($expl->explnum_vignette) {
						$explnum_datas['thumbnail_url'] = $opac_url_base.'vig_num.php?explnum_id='.$expl->explnum_id;
					} else {
						// trouver l'icone correspondant au mime_type
						$explnum_datas['thumbnail_url'] = get_url_icon('mimetype/'.icone_mimetype($expl->explnum_mimetype, $expl->explnum_extfichier), 1);
					}
					$explnum_datas['access_datas'] = array(
							'script' => '',
							'href' => '#',
							'onclick' => ''
					);
					//si l'affichage du lien vers les documents numériques est forcé et qu'on est pas connecté, on propose l'invite de connexion!
					if(!$explnum_docnum_visible && $opac_show_links_invisible_docnums && !$_SESSION['id_empr_session']){
						if ($opac_visionneuse_allow) {
							$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
						}
						if ($allowed_mimetype && in_array($expl->explnum_mimetype,$allowed_mimetype)){
							$explnum_datas['access_datas']['script'] = "
							<script>
								function sendToVisionneuse_".$expl->explnum_id."(){
									open_visionneuse(sendToVisionneuse,{explnum_id : ".$expl->explnum_id.", id : ".$this->id.", record_id : ".$record_id.", record_type : '".$record_type."'});
								}
							</script>";
							$explnum_datas['access_datas']['onclick'] = "auth_popup('./ajax.php?module=ajax&categ=auth&callback_func=sendToVisionneuse_".$expl->explnum_id."');";
						}else{
							$explnum_datas['access_datas']['onclick'] = "auth_popup('./ajax.php?module=ajax&categ=auth&new_tab=1&callback_url=".rawurlencode($opac_url_base."doc_num.php?explnum_id=".$expl->explnum_id)."'); return false;";
						}
					}else{
						if ($opac_visionneuse_allow)
							$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
						if ($allowed_mimetype && in_array($expl->explnum_mimetype,$allowed_mimetype)){
							$explnum_datas['access_datas']['onclick'] = "open_visionneuse(sendToVisionneuse,{explnum_id : ".$expl->explnum_id.", id : ".$this->id.", record_id : ".$record_id.", record_type : '".$record_type."'});return false;";
						} else {
							$explnum_datas['access_datas']['href'] = $opac_url_base.'doc_num.php?explnum_id='.$expl->explnum_id;
						}
					}

					if ($_mimetypes_byext_[$expl->explnum_extfichier]["label"]) $explnum_datas['mimetype_label'] = $_mimetypes_byext_[$expl->explnum_extfichier]["label"] ;
					elseif ($_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"]) $explnum_datas['mimetype_label'] = $_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"] ;
					else $explnum_datas['mimetype_label'] = $expl->explnum_mimetype ;

					$explnums['explnums'][] = $explnum_datas;
				}
			}
			if($explnums['nb_explnums']) {
				$explnums['access_datas']['script'] = "
				<script>
					function sendToVisionneuse_".$this->id."_".$record_type."_".$record_id."(){
						open_visionneuse(sendToVisionneuse,{id : ".$this->id.", record_id : ".$record_id.", record_type : '".$record_type."'});
					}
				</script>";
				$explnums['access_datas']['onclick'] = "open_visionneuse(sendToVisionneuse,{id : ".$this->id.", record_id : ".$record_id.", record_type : '".$record_type."'});return false;";
			}
		}
		return $explnums;
	}
	
	public function get_id() {
		return $this->id;
	}

	public function get_title() {
		return $this->title;
	}

	public function get_desc() {
		return $this->desc;
	}

	public function get_status() {
		return $this->status;
	}

	public function get_priority() {
		return $this->priority;
	}

	public function get_create_date() {
		return $this->create_date;
	}

	public function get_update_date() {
		return $this->update_date;
	}

	public function get_date() {
		return $this->date;
	}

	public function get_wish_date() {
		return $this->wish_date;
	}

	public function get_deadline_date() {
		return $this->deadline_date;
	}

	public function get_comment() {
		return $this->comment;
	}

	public function get_elapsed_time() {
		return $this->elapsed_time;
	}

	public function get_num_dest_empr() {
		return $this->num_dest_empr;
	}

	public function get_empr() {
	    if (!isset($this->empr)) {
	        $this->empr = "";
	        if (!empty($this->num_dest_empr)) {
	            $query = 'select empr_nom, empr_prenom from empr where id_empr = '.$this->num_dest_empr;
	            $result = pmb_mysql_query($query);
	            if (pmb_mysql_num_rows($result)) {
	                $row = pmb_mysql_fetch_object($result);
	                $this->empr = $row->empr_nom;
	                if($row->empr_prenom) $this->empr .= ', '.$row->empr_prenom;
	            }
	        }
	    }
	    return $this->empr;
	}
	
	public function get_num_creator() {
		return $this->num_creator;
	}

	public function get_type_creator() {
		return $this->type_creator;
	}

	public function get_num_last_user() {
		return $this->num_last_user;
	}

	public function get_state() {
		return $this->state;
	}
	
	public function get_linked_records() {
		return $this->linked_records;
	}
	
	public function get_formatted_update_date() {
		return $this->formatted_update_date;
	}
	
	public function get_formatted_date() {
		return $this->formatted_date;
	}
	
	public function get_formatted_wish_date() {
		return $this->formatted_wish_date;
	}
	
	public function get_formatted_deadline_date() {
		return $this->formatted_deadline_date;
	}
	
	public function get_display_link() {
		global $base_path;
		return $base_path.'/empr.php?tab=scan_requests&lvl=scan_request&sub=display&id='.$this->id;
	}
	
	public function get_edit_link() {
		global $base_path;
		return $base_path.'/empr.php?tab=scan_requests&lvl=scan_request&sub=edit&id='.$this->id;
	}

	public function get_cancel_link() {
		global $base_path;
		return $base_path.'/empr.php?tab=scan_requests&lvl=scan_request&sub=cancel&id='.$this->id;
	}
	
	/**
	 * Indique si la demande à des documents liés numérisables
	 */
	public function has_scannable_linked_record() {
		return $this->scannable_linked_record;
	}
	
	public function get_nb_explnums() {
		return $this->nb_explnums;
	}
	
	public function set_id($id) {
		$this->id = $id;
	}

	public function set_title($title) {
		$this->title = $title;
	}

	public function set_desc($desc) {
		$this->desc = $desc;
	}

	public function set_status($status) {
		$this->status = $status;
	}

	public function set_priority($priority) {
		$this->priority = $priority;
	}

	public function set_create_date($create_date) {
		$this->create_date = $create_date;
	}

	public function set_update_date($update_date) {
		$this->update_date = $update_date;
	}

	public function set_date($date) {
		$this->date = $date;
	}

	public function set_wish_date($wish_date) {
		$this->wish_date = $wish_date;
	}

	public function set_deadline_date($deadline_date) {
		$this->deadline_date = $deadline_date;
	}

	public function set_comment($comment) {
		$this->comment = $comment;
	}

	public function set_elapsed_time($elapsed_time) {
		$this->elapsed_time = $elapsed_time;
	}

	public function set_num_dest_empr($num_dest_empr) {
		$this->num_dest_empr = $num_dest_empr;
	}

	public function set_num_creator($num_creator) {
		$this->num_creator = $num_creator;
	}

	public function set_type_creator($type_creator) {
		$this->type_creator = $type_creator;
	}

	public function set_num_last_user($num_last_user) {
		$this->num_last_user = $num_last_user;
	}

	public function set_state($state) {
		$this->state = $state;
	}
	
	public function get_lib_empr($id_empr){
		if($id_empr){
			$query = "select empr_prenom, empr_nom from empr where id_empr= ".$id_empr;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				return $row->empr_nom.($row->empr_prenom?', '.$row->empr_prenom:'');
			}
		}
		return '';
	}
	
	public function get_nb_scanned_pages(){
	    return $this->nb_scanned_pages;
	}
}