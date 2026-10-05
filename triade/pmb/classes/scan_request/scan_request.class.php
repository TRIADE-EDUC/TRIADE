<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request.class.php,v 1.57.4.1 2025/03/04 15:50:02 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path.'/scan_request/scan_request_status.class.php');
require_once($class_path.'/scan_request/scan_request_priorities.class.php');
require_once($class_path.'/scan_request/scan_request_priority.class.php');
require_once($class_path.'/scan_request/scan_request_admin_status.class.php');
require_once($include_path."/templates/scan_request/scan_request.tpl.php");
require_once($class_path.'/expl.class.php');
require_once($class_path.'/caddie.class.php');
require_once($class_path.'/explnum.class.php');
require_once($class_path.'/notice.class.php');
require_once($class_path.'/audit.class.php');
require_once($class_path.'/acces.class.php');
require_once($class_path.'/encoding_normalize.class.php');
require_once($class_path.'/concept.class.php');
require_once($class_path.'/event/events/event_scan_request.class.php');
require_once($class_path.'/mono_display.class.php');
require_once($class_path.'/file_uploader.class.php');

class scan_request {

	protected $id;

	protected $title;

	protected $desc;

	protected $status;

	protected $priority;

	protected $create_date;

	protected $update_date;

	protected $date;

	protected $wish_date;

	protected $deadline_date;

	protected $comment;

	protected $elapsed_time;

	protected $num_dest_empr;

	protected $empr;

	protected $num_creator;

	protected $creator_name;

	protected $type_creator;

	protected $num_last_user;

	protected $state;

	protected $linked_records;

	protected $linked_bulletin;

	protected $as_folder;

	protected $formatted_update_date = null;

	protected $formatted_date = null;

	protected $formatted_wish_date = null;

	protected $formatted_deadline_date = null;

	protected $folder_num_notice;

	protected $explnum_number = 0;

	protected $concept_uri = '';

	protected $nb_scanned_pages = 0;

	protected $num_location = 0;
	protected $location_name = '';

	protected $loc_updated = false;

	public function __construct($id = 0) {
		$this->id = intval($id);
		$this->fetch_data();
	}

	protected function fetch_data() {
		/**
		 * TODO: Test sur les droits des documents numériques en gestion
		 * Vu avec AR -> write as todo
		 */

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

				$creator_name = '';
				if ($this->type_creator ==2){ // empr issu de l'opac
					$query_creator = 'select empr_nom, empr_prenom from empr where id_empr = '.$this->num_creator;
					$result_creator = pmb_mysql_query($query_creator);
					if (pmb_mysql_num_rows($result_creator)) {
						$row_creator = pmb_mysql_fetch_object($result_creator);
						$creator_name = $row_creator->empr_nom;
						if($row_creator->empr_prenom) $creator_name .= ' '.$row_creator->empr_prenom;
						$creator_name.= " (Opac)";
					}
				} else{ // user de gestion
					$query_creator = 'select username, prenom, nom from users where userid = '.$this->num_creator;
					$result_creator = pmb_mysql_query($query_creator);
					if (pmb_mysql_num_rows($result_creator)) {
						$row_creator = pmb_mysql_fetch_object($result_creator);
						$creator_name = $row_creator->username;
						if($row_creator->nom) $creator_name .= ', '.$row_creator->nom;
						if($row_creator->prenom) $creator_name .= ' '.$row_creator->prenom;
					}
				}
				$this->creator_name = $creator_name;

				$this->num_last_user = $row->scan_request_num_last_user;
				$this->state = $row->scan_request_state;
				$this->as_folder = $row->scan_request_as_folder;
				$this->formatted_update_date = formatdate($this->update_date);
				$this->formatted_date = formatdate($this->date);
				$this->formatted_wish_date = formatdate($this->wish_date);
				$this->formatted_deadline_date = formatdate($this->deadline_date);
				$this->linked_records = array();
				$this->linked_bulletin = array();
				$this->folder_num_notice = $row->scan_request_folder_num_notice;
				$this->concept_uri = $row->scan_request_concept_uri;
				$this->nb_scanned_pages = $row->scan_request_nb_scanned_pages;
				$this->num_location = $row->scan_request_num_location;
				$this->location_name = '';
				if($this->num_location) {
					$query_loc = 'select location_libelle from docs_location where idlocation = '.$this->num_location;
					$result_loc = pmb_mysql_query($query_loc);
					if (pmb_mysql_num_rows($result_loc)) {
						$row_loc = pmb_mysql_fetch_object($result_loc);
						$this->location_name = $row_loc->location_libelle;
					} else {
						$this->num_location = 0;
					}
				}
				$linked_records_query = 'select * from scan_request_linked_records where scan_request_linked_record_num_request ='.$this->id.' order by scan_request_linked_record_order';
				$query_result = pmb_mysql_query($linked_records_query);
				if(pmb_mysql_num_rows($query_result)){
					while($row = pmb_mysql_fetch_object($query_result)){
						if($row->scan_request_linked_record_num_notice){
							$this->linked_records[] = array(
									'id'=> $row->scan_request_linked_record_num_notice,
									'comment'=> $row->scan_request_linked_record_comment,
									'order'=> $row->scan_request_linked_record_order,
									'explnum' => $this->fetch_explnum($row->scan_request_linked_record_num_notice, 0)
							);
						}else{
							$this->linked_bulletin[] = array(
									'id'=> $row->scan_request_linked_record_num_bulletin,
									'comment'=> $row->scan_request_linked_record_comment,
									'order'=> $row->scan_request_linked_record_order,
									'explnum' => $this->fetch_explnum(0, $row->scan_request_linked_record_num_bulletin)
							);
						}
					}
				}
			}
		}
	}

/*
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
			if($dom_2->getRights($this->num_dest_empr,$id_for_right, 4)) {
				$rights['visible'] = true;
			}
			if($dom_2->getRights($this->num_dest_empr,$id_for_right, 32)) {
				$rights['scannable'] = true;
			}
		} else {
			$query = "SELECT notice_visible_opac, notice_visible_opac_abon, notice_scan_request_opac, notice_scan_request_opac_abon FROM notice_statut JOIN notices ON notices.statut = notice_statut.id_notice_statut WHERE notice_id='".$id_for_right."' ";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				if($row->notice_visible_opac && (!$row->notice_visible_opac_abon || ($row->notice_visible_opac_abon && $this->num_dest_empr))) {
					$rights['visible'] = true;
				}
				if($row->notice_scan_request_opac && (!$row->notice_scan_request_opac_abon || ($row->notice_scan_request_opac_abon && $this->num_dest_empr))) {
					$rights['scannable'] = true;
				}
			}
		}
		return $rights;
	}
*/


	public function get_selector_piece($id, $type, $comment, $explnum=array()){
		$id = intval($id);
		return array('id'=>$id,
		    'comment'=>$comment,
		    'label'=>(($type=='record')?strip_tags($this->get_record_display_header($id)):$this->get_bulletin_title($id)),
		    'explnums'=>$explnum,
		    'permalink'=>notice::get_gestion_link($id)
		);
	}

	public function preload_form_elements(){
		global $from_record, $from_bulletin, $from_caddie;
		global $elt_flag, $elt_no_flag;

		$form_elements=array();
		$form_elements['title'] = '';
		if($from_record) {
			$form_elements['records'][]=$this->get_selector_piece($from_record, 'record', '');
			$form_elements['bulletins'][]=$this->get_selector_piece(0, 'bulletin', '');
			$form_elements['title'] = strip_tags($this->get_record_display_header($from_record));
		}elseif ($from_bulletin) {
			$form_elements['bulletins'][]=$this->get_selector_piece($from_bulletin, 'bulletin', '');
			$form_elements['records'][]=$this->get_selector_piece(0, 'record', '');
			$form_elements['title'] = $this->get_bulletin_title($from_bulletin);
		}elseif ($from_caddie) {
			$liste_0=$liste_1=array();
			$caddie = new caddie($from_caddie);
			if ($elt_flag) {
				$liste_0 = $caddie->get_cart("FLAG") ;
			}
			if ($elt_no_flag) {
				$liste_1= $caddie->get_cart("NOFLAG") ;
			}
			$liste= array_merge($liste_0,$liste_1);
			$nb_elements_total=count($liste);
			if($caddie->type=='NOTI' && $nb_elements_total){
				foreach ($liste as $record_id){
					$form_elements['records'][]=$this->get_selector_piece($record_id, 'record', '');
				}
				$form_elements['bulletins'][]=$this->get_selector_piece(0, 'bulletin', '');
			}elseif($caddie->type=='BULL' && $nb_elements_total){
				foreach ($liste as $bulletin_id){
					$form_elements['bulletins'][]=$this->get_selector_piece($bulletin_id, 'bulletin', '');
				}
				$form_elements['records'][]=$this->get_selector_piece(0, 'record', '');
			}elseif($caddie->type=='EXPL' && $nb_elements_total){
				$count_notice=0;
				$count_bulletin=0;
				foreach ($liste as $expl_id){
					$expl=new exemplaire('',$expl_id);
					if($expl->id_notice){
						$form_elements['records'][]=$this->get_selector_piece($expl->id_notice, 'record', '');
						$count_notice++;
					}else{
						$form_elements['bulletins'][]=$this->get_selector_piece($expl->id_bulletin, 'bulletin', '');
						$count_bulletin++;
					}
				}
				if(!$count_notice){
					$form_elements['records'][]=$this->get_selector_piece(0, 'record', '');
				}elseif (!$count_bulletin){
					$form_elements['bulletins'][]=$this->get_selector_piece(0, 'bulletin', '');
				}
			}else{
				$form_elements['records'][]=$this->get_selector_piece(0, 'record', '');
				$form_elements['bulletins'][]=$this->get_selector_piece(0, 'bulletin', '');
			}
		}else {
			$form_elements['records'][]=$this->get_selector_piece(0, 'record', '');
			$form_elements['bulletins'][]=$this->get_selector_piece(0, 'bulletin', '');
		}
		return $form_elements;
	}

	public function get_form($url="./circ.php?categ=scan_request&sub=request",$cancel_action="./circ.php?categ=scan_request&sub=list"){
		global $msg,$charset;
		global $scan_request_form;
		global $record_id;
		global $bulletin_id;
		global $thesaurus_concepts_active, $scan_request_concept_part;
		global $deflt_docs_location;
		global $pmb_scan_request_location_activate;

		$form = str_replace("!!action!!",$url,$scan_request_form);
		$status_list = new scan_request_admin_status();
		$priorities_list = new scan_request_priorities();

		if($this->id){

			$form = str_replace("!!form_title!!",$msg['scan_request_edit'],$form);
			$form = str_replace("!!title!!",htmlentities($this->title,ENT_QUOTES,$charset),$form);
			$form = str_replace("!!scan_request_desc!!",htmlentities($this->desc,ENT_QUOTES,$charset),$form);
			$form = str_replace("!!scan_request_elapsed_time!!",$this->elapsed_time,$form);
			$form = str_replace("!!scan_request_nb_scanned_pages!!",$this->nb_scanned_pages,$form);
			$form = str_replace("!!scan_request_lib_empr!!",htmlentities($this->get_lib_empr($this->num_dest_empr*1),ENT_QUOTES,$charset),$form);
			$form = str_replace("!!scan_request_num_dest_empr!!",$this->num_dest_empr,$form);
			$form = str_replace("!!scan_request_status!!",$this->status->get_workflow_options() ,$form);
			$form = str_replace("!!scan_request_priority!!",$priorities_list->get_selector_options($this->priority->get_id()),$form);

			$form = str_replace("!!scan_request_as_folder!!",$this->as_folder?'checked':'',$form);
			$form = str_replace("!!scan_request_as_folder_disabled!!",($this->explnum_number || $this->folder_num_notice)?'disabled':'',$form);

			$form = str_replace("!!scan_request_date!!",explode(' ', $this->date)[0],$form);
			$form = str_replace("!!scan_request_wish_date!!",explode(' ', $this->wish_date)[0],$form);
			$form = str_replace("!!scan_request_deadline_date!!",explode(' ', $this->deadline_date)[0],$form);
			$form = str_replace("!!scan_request_comment!!",htmlentities($this->comment,ENT_QUOTES,$charset),$form);

			if($pmb_scan_request_location_activate) {
				$form = str_replace("!!scan_request_location_selector!!",gen_liste ("select idlocation, location_libelle from docs_location order by location_libelle ", "idlocation", "location_libelle", 'scan_request_num_location', "", $this->num_location, "", "", "", $msg['no_location'],0),$form);
			}else {
				$form = str_replace("!!scan_request_location_selector!!", "", $form);
			}
			$final_records_inputs = array();
			foreach($this->linked_records as $record){
				$final_records_inputs[] = $this->get_selector_piece($record['id'], 'record', $record['comment'], $record['explnum']);
			}
			$final_bulletin_inputs = array();
			foreach($this->linked_bulletin as $bulletin){
				$final_bulletin_inputs[] = $this->get_selector_piece($bulletin['id'], 'bulletin', $bulletin['comment'], $bulletin['explnum']);
			}
			$form = str_replace("!!associated_records!!",htmlentities(encoding_normalize::json_encode($final_records_inputs), ENT_QUOTES, "utf-8"),$form );
			$form = str_replace("!!associated_buls!!",htmlentities(encoding_normalize::json_encode($final_bulletin_inputs), ENT_QUOTES, "utf-8"),$form);
			$form = str_replace("!!all_explnum_datas!!",htmlentities(encoding_normalize::json_encode(array_merge($final_records_inputs,$final_bulletin_inputs)), ENT_QUOTES, "utf-8"),$form);
			$form = str_replace("!!scan_request_status_editable!!",$this->status->is_infos_editable(),$form);
			$form = str_replace("!!id!!",$this->id,$form);
			if($this->status->is_cancelable() || $this->status->is_closed()){
				$form = str_replace("!!bouton_supprimer!!",	"<input type='button' class='bouton' value=' ".$msg[63]." ' onclick='confirmation_delete(\"&action=delete&id=".$this->id."\",\"".htmlentities($this->title,ENT_QUOTES,$charset)."\")'/>",$form);
				$form.= confirmation_delete($url);
			}else{
				$form = str_replace("!!bouton_supprimer!!",	"",$form);
			}
		}else{

			$preloaded_elements=$this->preload_form_elements();
			$form = str_replace("!!form_title!!",$msg['scan_request_add'],$form);
			$form = str_replace("!!title!!",htmlentities($preloaded_elements['title'],ENT_QUOTES,$charset),$form);

			$form = str_replace("!!scan_request_elapsed_time!!","",$form);
			$form = str_replace("!!scan_request_nb_scanned_pages!!", "0", $form);
			$form = str_replace("!!scan_request_lib_empr!!","",$form);
			$form = str_replace("!!scan_request_num_dest_empr!!","0",$form);
			$form = str_replace("!!scan_request_comment!!","",$form);
			$form = str_replace("!!scan_request_status!!",$status_list->get_selector_options() ,$form);
			$form = str_replace("!!scan_request_priority!!",$priorities_list->get_selector_options(),$form);
			$form = str_replace("!!scan_request_as_folder!!","",$form);
			$form = str_replace("!!scan_request_as_folder_disabled!!",'',$form);

			$form = str_replace("!!associated_records!!",htmlentities(encoding_normalize::json_encode($preloaded_elements['records']), ENT_QUOTES, "utf-8"),$form );
			$form = str_replace("!!associated_buls!!",htmlentities(encoding_normalize::json_encode($preloaded_elements['bulletins']), ENT_QUOTES, "utf-8"),$form );

			$form = str_replace("!!all_explnum_datas!!",encoding_normalize::json_encode(array()),$form);

			if($pmb_scan_request_location_activate) {
				$form = str_replace("!!scan_request_location_selector!!",gen_liste ("select idlocation, location_libelle from docs_location order by location_libelle ", "idlocation", "location_libelle", 'scan_request_num_location', "", $deflt_docs_location, "", "", "" ,$msg['no_location'],0),$form);
			}else {
				$form = str_replace("!!scan_request_location_selector!!", "", $form);
			}
			$form = str_replace("!!scan_request_desc!!","",$form);
			$form = str_replace("!!scan_request_date!!",date('Y-m-d'),$form);
			$form = str_replace("!!scan_request_wish_date!!",date('Y-m-d'),$form);
			$form = str_replace("!!scan_request_deadline_date!!",date('Y-m-d'),$form);
			$form = str_replace("!!scan_request_status_editable!!",'1',$form);
			$form = str_replace("!!id!!",0,$form);
			$form = str_replace("!!bouton_supprimer!!","",$form);
		}

		/**
		 * TODO: generate the event here
		 */



		//Evenement publié à chaque fois que le formulaire admin scan_request est envoyé
		$evt_handler = events_handler::get_instance();
		$event = new event_scan_request("scan_request", "get_form");
		if($this->concept_uri){ //cas de l'édition d'une demande de numérisation
			$event->set_concept_uri($this->concept_uri);
		}
		$evt_handler->send($event);
		if($event->get_template_content()){
			$form = str_replace("!!scan_request_concept_part!!", $event->get_template_content(),$form);
		}else{
		    if($thesaurus_concepts_active == 1){
		        if($this->concept_uri){
		            $concept = new concept(0,$this->concept_uri);
		            $scan_request_concept_part = str_replace("!!scan_request_concept_label!!", htmlentities($concept->get_display_label(),ENT_QUOTES,$charset),$scan_request_concept_part);
		            $scan_request_concept_part = str_replace("!!scan_request_concept_uri_value!!", htmlentities($this->concept_uri,ENT_QUOTES,$charset),$scan_request_concept_part);
		        }else{
		            $scan_request_concept_part = str_replace("!!scan_request_concept_label!!", '',$scan_request_concept_part);
		            $scan_request_concept_part = str_replace("!!scan_request_concept_uri_value!!", '',$scan_request_concept_part);
		        }
		        $form = str_replace("!!scan_request_concept_part!!", $scan_request_concept_part, $form);
		    } else {
		        $form = str_replace("!!scan_request_concept_part!!", "", $form);
		    }
		}

		$form = str_replace("!!cancel_action!!",$cancel_action,$form);
		return $form;
	}

	public function get_values_from_form(){
		global $charset;
		global $scan_request_deadline_date;
		global $scan_request_desc;
		global $scan_request_title;
		global $scan_request_elapsed_time;
		global $scan_request_num_dest_empr;
		global $scan_request_status;
		global $scan_request_priority;
		global $scan_request_date;
		global $scan_request_wish_date;
		global $scan_request_deadline_date;
		global $associated_record_counter;
		global $associated_bul_counter;
		global $scan_request_comment;
		global $scan_request_record_comment, $scan_request_bul_comment;
		global $scan_request_record_code, $scan_request_bul_code;
		global $scan_request_as_folder;
		global $scan_request_concept_uri_value;
		global $scan_request_nb_scanned_pages;
		global $scan_request_num_location;

		$scan_request_num_location = intval($scan_request_num_location);

		if ($this->num_location != $scan_request_num_location && $this->id) {
			$this->loc_updated = true;
		}

		$this->title = strip_tags(stripslashes($scan_request_title));
		$this->elapsed_time = stripslashes($scan_request_elapsed_time);
		$this->num_dest_empr = stripslashes($scan_request_num_dest_empr);
		$this->priority = new scan_request_priority($scan_request_priority);
		$this->date = stripslashes($scan_request_date);
		$this->wish_date = stripslashes($scan_request_wish_date);
		$this->deadline_date = stripslashes($scan_request_deadline_date);
		$this->status = new scan_request_status($scan_request_status);
		$this->comment = stripslashes($scan_request_comment);
		$this->concept_uri = stripslashes($scan_request_concept_uri_value);
		$this->nb_scanned_pages = stripslashes($scan_request_nb_scanned_pages);
		$this->num_location = $scan_request_num_location;

		/**
		 * Todo -> affectation des notices et des bulletins liés à la demande
		 */

		if(isset($scan_request_as_folder)){
			$this->as_folder = $scan_request_as_folder ? 1 : 0;
		}

		$this->linked_records = $this->fetch_linked_elts($scan_request_record_code, $scan_request_record_comment);
		$this->linked_bulletin = $this->fetch_linked_elts($scan_request_bul_code, $scan_request_bul_comment);

		$this->desc = stripslashes($scan_request_desc);
	}


	public function get_ajax_form($url="./circ.php?categ=scan_request&sub=request",$cancel_action="./circ.php?categ=scan_request&sub=list"){
		global $msg,$charset;
		global $scan_request_ajax_form;
		global $record_id;
		global $bulletin_id;
		global $scan_request_associated_bulls_sub_template;
		global $scan_request_associated_records_sub_template;
		$form = str_replace("!!action!!",$url,$scan_request_ajax_form);

		if(!$this->id)return;

		$form = str_replace("!!form_title!!",$msg['scan_request_edit'],$form);
		$form = str_replace("!!scan_request_elapsed_time!!",htmlentities($this->elapsed_time,ENT_QUOTES,$charset),$form);
		$form = str_replace("!!scan_request_nb_scanned_pages!!",htmlentities($this->nb_scanned_pages,ENT_QUOTES,$charset),$form);
		$form = str_replace("!!scan_request_status!!",$this->status->get_workflow_options() ,$form);
		$form = str_replace("!!scan_request_comment!!",htmlentities($this->comment,ENT_QUOTES,$charset),$form);
		$form = str_replace("!!scan_request_concept_uri_value!!",($this->concept_uri?htmlentities($this->concept_uri,ENT_QUOTES,$charset):''),$form);

		if(count($this->linked_records)){
			$final_records_inputs = array();
			foreach($this->linked_records as $record){
				$final_records_inputs[] = $this->get_selector_piece($record['id'], 'record', $record['comment'], $record['explnum']);
			}
			$scan_request_associated_records_sub_template = str_replace("!!associated_records!!",encoding_normalize::json_encode($final_records_inputs),$scan_request_associated_records_sub_template);
			$form = str_replace("!!scan_request_associated_records_sub_template!!",$scan_request_associated_records_sub_template,$form);
		}else{
			$form = str_replace("!!scan_request_associated_records_sub_template!!",'',$form);
		}
		if(count($this->linked_bulletin)){
			$final_bulletin_inputs = array();
			foreach($this->linked_bulletin as $bulletin){
				$final_bulletin_inputs[] = $this->get_selector_piece($bulletin['id'], 'bulletin', $bulletin['comment'], $bulletin['explnum']);
			}
			$scan_request_associated_bulls_sub_template = str_replace("!!associated_buls!!",encoding_normalize::json_encode($final_bulletin_inputs),$scan_request_associated_bulls_sub_template);
			$form = str_replace("!!scan_request_associated_bulls_sub_template!!",$scan_request_associated_bulls_sub_template,$form);
		}else{
			$form = str_replace("!!scan_request_associated_bulls_sub_template!!",'',$form);
		}

		$form = str_replace("!!id!!",$this->id,$form);

		$form = str_replace("!!cancel_action!!",$cancel_action,$form);
		if($charset != "utf-8"){
			return encoding_normalize::utf8_normalize($form);
		}
		return $form;
	}

	public function save_ajax_form(){
		global $charset;
		global $PMBuserid;
		global $scan_request_elapsed_time;
		global $scan_request_nb_scanned_pages;
		global $scan_request_status;
		global $scan_request_comment;

		if(!$this->id)return;
		$query = "update scan_requests set ";
		$where = " where id_scan_request = ".$this->id;
		$query.= "
			scan_request_elapsed_time = '".$scan_request_elapsed_time."',
			scan_request_nb_scanned_pages = '".$scan_request_nb_scanned_pages."',
			scan_request_num_status = '".$scan_request_status."',
			scan_request_update_date = now(),
			scan_request_comment = '".$scan_request_comment."'
			";
		pmb_mysql_query($query.$where);

		$this->purge_linked_elts();
		$this->save_linked_elts();
		$this->fetch_data();

		$data= array(
			'id' => $this->id,
			'statut_id' => $this->status->get_id(),
			'statut_label' => stripslashes($this->status->get_label()),
			'statut_class_html' => stripslashes($this->status->get_class_html()),
			'elapsed_time' => stripslashes($scan_request_elapsed_time),
			'nb_scanned_pages' => stripslashes($scan_request_nb_scanned_pages),
			'comment' => stripslashes($scan_request_comment),
		);

		$this->send_mail(false);

		if($charset != "utf-8"){
			return json_encode(encoding_normalize::utf8_normalize($data));
		}
		return json_encode($data);
	}

	public function send_mail($request_creation = false){
		global $pmb_scan_request_location_activate, $opac_scan_request_send_mail_status;

		if ($request_creation || $this->loc_updated) {
			//En création de demande ou changement de localisation, on envoie à la localisation
			if ($pmb_scan_request_location_activate) {
				$mail_scan_request = new mail_scan_request();
				$mail_scan_request->set_mail_to_id($this->num_location);
				$mail_scan_request->set_scan_request($this);
				$mail_scan_request->send_mail();
			}
		}
		if (!$request_creation) {
			//En modification, on envoie à l'emprunteur
			if (trim($opac_scan_request_send_mail_status)) {
				$send_mail_status = json_decode($opac_scan_request_send_mail_status);
				if (is_array($send_mail_status) && count($send_mail_status) && in_array($this->status->get_id(),$send_mail_status)) {
					$mail_scan_request = new mail_scan_request();
					$mail_scan_request->set_scan_request($this);
					$mail_scan_request->set_request_creation(false);
					$mail_scan_request->send_mail();
				}
			}
		}
	}

	public function save(){
		global $PMBuserid;

		if($this->id){ //Faire la update date
			$query = "update scan_requests set ";
			$where = " where id_scan_request = ".$this->id;
		}else{
			$query = "insert into scan_requests set
					 scan_request_create_date = now(),
					 scan_request_num_creator = ".$PMBuserid.",
					 scan_request_type_creator = '1',";
			$where = "";
		}
		$query.= "
			scan_request_title = '".addslashes($this->title)."',
			scan_request_desc = '".addslashes($this->desc)."',
			scan_request_elapsed_time = '".addslashes($this->elapsed_time)."',
			scan_request_nb_scanned_pages = '".addslashes($this->nb_scanned_pages)."',
			scan_request_num_status = '".addslashes($this->status->get_id())."',
			scan_request_num_priority = '".addslashes($this->priority->get_id())."',
			scan_request_update_date = now(),
			scan_request_wish_date = '".addslashes($this->wish_date)."',
			scan_request_deadline_date = '".addslashes($this->deadline_date)."',
			scan_request_comment = '".addslashes($this->comment)."',
			scan_request_num_dest_empr = '".addslashes($this->num_dest_empr)."',
			scan_request_num_last_user = '".addslashes($this->num_dest_empr)."',
			scan_request_as_folder = '".$this->as_folder."',
			scan_request_folder_num_notice = '".$this->folder_num_notice."',
			scan_request_concept_uri = '".$this->concept_uri."',
			scan_request_date = '".addslashes($this->date)."',
			scan_request_num_location = '".$this->num_location."'";

		pmb_mysql_query($query.$where);
		$creation_for_send_mail = false;
		if(!$this->id){
			$this->id = pmb_mysql_insert_id();
			$creation_for_send_mail = true;
		}
		$this->purge_linked_elts();
		$this->save_linked_elts();
		$this->fetch_data();
		$this->send_mail($creation_for_send_mail);
	}

	public function delete($force=false){
		if($this->status->is_cancelable() || $this->status->is_closed() || $force){
			$this->purge_linked_elts();
			$query = "delete from scan_requests where id_scan_request= ".$this->id;
			pmb_mysql_query($query);
			$this->id = 0;
		}
	}

	protected function save_linked_elts(){
		foreach($this->linked_records as $record){
			$this->add_linked_elt($record, true);
		}
		foreach($this->linked_bulletin as $bulletin){
			$this->add_linked_elt($bulletin, false);
		}
	}

	protected function add_linked_elt($elt, $is_record){
		$start_query = 'insert into scan_request_linked_records set ';
		$insert_query =' scan_request_linked_record_num_request = "'.$this->id.'",';
		$insert_query.=(($is_record)?'scan_request_linked_record_num_notice = ':'scan_request_linked_record_num_bulletin = ');
		$insert_query.='"'.$elt['id'].'",';
		$insert_query.= '
					scan_request_linked_record_comment = "'.$elt['comment'].'",
					scan_request_linked_record_order = "'.$elt['order'].'"
					';
		pmb_mysql_query($start_query.$insert_query);
	}

	protected function purge_linked_elts(){
		$delete_query = 'delete from scan_request_linked_records where scan_request_linked_record_num_request = '.$this->id;
		pmb_mysql_query($delete_query);
	}

	public function get_list() {
		return 'get_list';
	}


	public function get_display() {
		return '';
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

	public function get_special() {
		global $include_path;

		$special_file = $include_path.'/templates/scan_request/special/scan_request_special.class.php';
		if (file_exists($special_file)) {
			require_once($special_file);
			return new scan_request_special($this);
		}
		return null;
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

	public function get_creator_name() {
		return $this->creator_name;
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

	public function get_linked_bulletin() {
		return $this->linked_bulletin;
	}

	public function get_display_link() {
		global $base_path;
		return $base_path.'/empr.php?tab=scan_requests&lvl=scan_request&sub=display&id='.$this->id;
	}

	public function get_edit_link() {
		global $base_path;
		return $base_path.'/circ.php?categ=scan_request&sub=request&action=edit&id='.$this->id;
	}

	public function get_cancel_link() {
		global $base_path;
		return $base_path.'/empr.php?tab=scan_requests&lvl=scan_request&sub=cancel&id='.$this->id;
	}

	public function get_folder_num_notice() {
		return $this->folder_num_notice;
	}

	public function get_num_location() {
		return $this->num_location;
	}

	public function get_location_name() {
		return $this->location_name;
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

	public function set_num_location($num_location) {
		$this->num_location = $num_location;
	}

	public function request_as_folder(){
		return $this->as_folder;
	}

	/**
	 * Fonction de merge des éléments liés envoyés depuis le formulaire
	 * @param array $elts_ids Tableau d'id d'éléments (notice ou bulletin)
	 * @param array $elts_comments Tableau de commentaires d'éléments (notice ou bulletin)
	 * @return array Array reconstitué à partir des infos récupérées du formulaire
	 */
	protected function fetch_linked_elts($elts_ids, $elts_comments){
		$linked_elts = array();
		if(!count($elts_ids)) return array();
		if(count($elts_ids) == count($elts_comments)){
			$i = 0;
			foreach($elts_ids as $elt_id){
				if(!$elt_id) continue;
				$linked_elts[] = array("id" => $elt_id, "order"=>$i+1, "comment"=> stripslashes($elts_comments[$i]));
				$i++;
			}
		}
		return $linked_elts;
	}

	public function get_mail_empr($id_empr){
		if($id_empr){
			$query = "select empr_mail from empr where id_empr= ".$id_empr;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				return $row->empr_mail;
			}
		}
		return '';
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

	public function get_record_title($record_id){
		$record_id = intval($record_id);
		$requete="select serie_name, tnvol, tit1, code from notices left join series on serie_id=tparent_id where notice_id=".$record_id;
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);
			return (($r->serie_name?$r->serie_name." ":"").($r->tnvol?$r->tnvol." ":"").$r->tit1.($r->code?" (".$r->code.")":""));
		}
		return '';
	}

	public function get_bulletin_title($bulletin_id){
		$bulletin_id = intval($bulletin_id);
		$requete = "select tit1, if(bulletin_titre is not null and bulletin_titre!='',concat(bulletin_titre,' - ',bulletin_numero),bulletin_numero) as bulletin_numero, bulletin_id from bulletins, notices where bulletin_notice=notice_id and bulletin_id= ".$bulletin_id;
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);
			return $r->tit1.' / '.$r->bulletin_numero;
		}
		return '';
	}

	public function add_explnum(){
		global $fnc;

		$protocol = $_SERVER["SERVER_PROTOCOL"];
		$uploadDir = "./temp/";

		switch ($fnc){
			case 'upl':
				if (is_dir($uploadDir)) {
					if (is_writable($uploadDir)) {
						print $this->get_file();
					}else{
						header($protocol.' 405 Method Not Allowed');
						exit('Upload directory is not writable.');
					}
				}else{
					header($protocol.' 404 Not Found');
					exit('Upload directory does not exist.');
				}
				break;
			case 'del':
				break;
			case 'resume':
				break;
			case 'getNumWrittenBytes':
				break;
		}
	}

	protected function get_file(){
		global $fnc;
		global $num_record;
		global $num_bul;
		global $pmb_scan_request_explnum_folder;
		global $id_rep;
		global $concept_uri;
		global $deflt_scan_request_explnum_status;

		$protocol = $_SERVER["SERVER_PROTOCOL"];

		$file= file_uploader::get_file();
		if(is_object($file)) {
			$this->fileName = $file->filename;
			$file->content = file_get_contents("php://input");

			$limit = file_uploader::get_limit();
			if (mb_strlen($file->content) > $limit) {
				header($protocol.' 403 Forbidden');
				return false;
			}
			$this->numWrittenBytes = file_put_contents("./temp/".$file->filename, $file->content);
			if ($this->numWrittenBytes !== false) {
				header($protocol.' 201 Created');
				$returned_num_record = 0;
				$returned_num_bulletin = 0;
				if($this->as_folder){ //C'est une demande groupée -> Les documents numériques doivent être associés à une notice créee a la volée
					if(!$this->folder_num_notice){// La notice de groupement n'est pas créee
						$this->create_folder_record();
					}
					$explnum = new explnum(0,$this->folder_num_notice,0);
					$returned_num_record = $this->folder_num_notice;
				}else{
					if($num_bul){
						$num_bul = intval($num_bul);
						$explnum = new explnum(0,0,$num_bul);
						$returned_num_bulletin = $num_bul;
					}else if($num_record){
						$num_record = intval($num_record);
						$explnum = new explnum(0,$num_record,0);
						$returned_num_record = $num_record;
					}else{
						return false;
					}
				}
				$id_rep = $pmb_scan_request_explnum_folder;
				$explnum->get_file_from_temp("./temp/".$file->filename, $file->name, true);
				if($deflt_scan_request_explnum_status) {
					$this->params["statut"] = $deflt_scan_request_explnum_status;
				}
				$explnum->update(false);
				if($concept_uri){
					$concept = new \concept(0,$concept_uri);
					$index_concept = new \index_concept($explnum->explnum_id, TYPE_EXPLNUM);
					$index_concept->add_concept($concept);
					$index_concept->save(false);
				}
				$this->link_explnum($explnum->explnum_id);
				$explnum = new explnum($explnum->explnum_id);
				return encoding_normalize::json_encode(array('id'=>$explnum->explnum_id, 'title'=>$explnum->explnum_nom, 'record_id'=>$returned_num_record, 'bulletin_id'=>$returned_num_bulletin, 'type'=>$explnum->explnum_mimetype, 'label'=>($num_record)?$this->get_record_display_header($num_record):$this->get_bulletin_title($num_bul)));
			}else {
				header($protocol.' 505 Internal Server Error');
				return false;
			}
		}
	}

	//Doit retourner un id de notice.
	protected function create_folder_record(){
		global $gestion_acces_active;
		global $gestion_acces_user_notice;
		global $gestion_acces_active;
		global $gestion_acces_user_notice;
		global $gestion_acces_empr_notice;
		global $xmlta_doctype_scan_request_folder_record;

		$record_title = $this->title.' - '.$this->formatted_date;
		$query = 'INSERT INTO notices SET create_date = sysdate(), update_date = sysdate(),  typdoc="'.$xmlta_doctype_scan_request_folder_record.'", tit1="'.clean_string($record_title).'" ;';
		$result = pmb_mysql_query($query);
		if($result){
			$folder_record_id = pmb_mysql_insert_id();
			$this->folder_num_notice = $folder_record_id;
			$this->save();
			audit::insert_creation(AUDIT_NOTICE, $folder_record_id);
			notice::majNoticesTotal($this->folder_num_notice);
			if ($gestion_acces_active==1) {
				$ac= new acces();
				//traitement des droits acces user_notice
				if ($gestion_acces_user_notice==1) {
					$dom_1= $ac->setDomain(1);
					$dom_1->storeUserRights(0, $this->folder_num_notice);
				}
				//traitement des droits acces empr_notice
				if ($gestion_acces_empr_notice==1) {
					$dom_2= $ac->setDomain(2);
					$dom_2->storeUserRights(0, $this->folder_num_notice);
				}
			}
		}

	}

	protected function link_explnum($num_explnum){
		global $num_record;
		global $num_bul;

		$query = 'insert into scan_request_explnum set scan_request_explnum_num_request = "'.$this->id.'",
				scan_request_explnum_num_notice = "'.($num_record*1).'",  scan_request_explnum_num_bulletin = "'.($num_bul*1).'",
 				scan_request_explnum_num_explnum = "'.$num_explnum.'"';
 		if(pmb_mysql_query($query)){
 			return true;
 		}
 		return false;
	}

	protected function fetch_explnum($record_id, $bulletin_id){
		$record_id = intval($record_id);
		$bulletin_id = intval($bulletin_id);
		$explnum_linked = array();
		$query = 'select scan_request_explnum_num_explnum from scan_request_explnum where scan_request_explnum_num_notice = '.$record_id.'
		and scan_request_explnum_num_bulletin = '.$bulletin_id.'
		and scan_request_explnum_num_request= '.$this->id;
		$result = pmb_mysql_query($query);
		if($result){
			while($row = pmb_mysql_fetch_object($result)){
				$explnum = new explnum($row->scan_request_explnum_num_explnum);
				$explnum_linked[] = array('id'=>$explnum->explnum_id, 'record_id'=>$explnum->explnum_notice, 'bulletin_id'=>$explnum->explnum_bulletin, 'title'=>$explnum->explnum_nom, 'type'=>$explnum->explnum_mimetype);
				$this->explnum_number++;
			}
		}
		return $explnum_linked;
	}

	protected function get_record_display_header($record_id){
		$record_id = intval($record_id);
		if(!$record_id) return '';
		$query = 'select niveau_biblio from notices where notice_id = '.$record_id;
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		switch ($row->niveau_biblio) {
			case 'm' :
			case 'b' :
				$displaying_class = new mono_display($record_id, 0, '', 0, '', '', '',0, 0, 0, 0,0,false, false, true, 0, 0, 0);
				break;
			case 's' :
				$displaying_class = new serial_display($record_id, 0, '', '', '', '', '', 0, 0, 0, 0, false, 0, 1, '', true, 0, 0, 0);
				break;
			case 'a' :
				$displaying_class = new serial_display($record_id, 0, '', '', '', '', '', 0, 0, 0, 0, false, 0, 1, '', true, 0, 0, 0);
				$displaying_class->header_texte.=" in ".$displaying_class->parent_title." (".$displaying_class->parent_numero." ".($displaying_class->parent_date?$displaying_class->parent_date:$displaying_class->parent_aff_date_date).")";
				break;
		}
		return $displaying_class->header_texte;
	}

	public function get_concept_uri(){
		return $this->concept_uri;
	}

	public function get_nb_scanned_pages(){
		return $this->nb_scanned_pages;
	}
}