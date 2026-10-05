<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: avis_records.class.php,v 1.6.8.1 2024/12/23 14:59:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/avis.class.php");

class avis_records extends avis {
	
	public function __construct($object_id = 0) {
		$this->object_type = AVIS_RECORDS;
		parent::__construct($object_id);
	}

	protected function _get_select_query() {
		return ", notice_id, niveau_biblio ";
	}
	
	protected function _get_join_query() {
		return "left join notices on notices.notice_id=avis.num_notice ".self::check_records_read_rights()." ";
	}
	
	protected function _get_sort_query() {
		return "order by index_serie, tnvol, index_sew ,dateAjout desc ";
	}
	
	public function get_display_list() {
		global $pmb_javascript_office_editor;
		global $begin_result_liste;
	
		$query = $this->get_query();
		$result = pmb_mysql_query($query);
		$display = '';
		if (pmb_mysql_num_rows($result)) {
			//affichage des notices
			$display .= "<script type=\"text/javascript\" src='./javascript/dyn_form.js'></script>";
			$display .= "<script type=\"text/javascript\" src='./javascript/http_request.js'></script>";
			$display .= $begin_result_liste;
			$notice_id=0;
			while ($row = pmb_mysql_fetch_object($result)) {
				if ($notice_id!=$row->notice_id) {
					if ($notice_id!=0) $display .=  "</ul><br />" ;
					$notice_id=$row->notice_id;
					if($row->niveau_biblio != 's' && $row->niveau_biblio != 'a') {
						// notice de monographie
						$link = notice::get_pattern_link();
						$link_expl = exemplaire::get_pattern_link();
						$link_explnum = explnum::get_pattern_link();
						$mono = new mono_display($row->notice_id, 6, $link, 1, $link_expl, '', $link_explnum,1, 0, 1, 1, array(), 0, false, true, 0, 0, 1, 0, array(), 0);
						$display .= pmb_bidi($mono->result);
					} else {
						// on a affaire à un périodique
						$link_serial = serial::get_pattern_link();
						$link_analysis = analysis::get_pattern_link();
						$link_bulletin = bulletinage::get_pattern_link();
						$link_explnum = "./catalog.php?categ=serials&sub=analysis&action=explnum_form&bul_id=!!bul_id!!&analysis_id=!!analysis_id!!&explnum_id=!!explnum_id!!";
						$link_explnum_serial = "./catalog.php?categ=serials&sub=explnum_form&serial_id=!!serial_id!!&explnum_id=!!explnum_id!!";
						$serial = new serial_display($row->notice_id, 6, $link_serial, $link_analysis, $link_bulletin, "", $link_explnum_serial, 0, 0, 1, 1, true, 1, 0, array(), false, 1, 0, 0, 0, array(), 0);
						$display .= pmb_bidi($serial->result);
					}
					$display .=  "<ul>" ;
				}
				if($pmb_javascript_office_editor)	{
					$office_editor_cmd=" if (typeof(tinyMCE) != 'undefined') tinyMCE_execCommand('mceAddControl', true, 'avis_desc_".$row->id_avis."');	 ";
				} else {
					$office_editor_cmd="";
				}
				$display .= "<div id='avis_".$row->id_avis."' onclick=\"make_form('".$row->id_avis."'); $office_editor_cmd\">";
				$display .= self::get_display_review($row);
				$display .= "</div><div id='update_".$row->id_avis."'></div>
				<br />";
			}
			$display .=  "</ul><br />" ;
		}
		return $display;
	}
	
	public static function check_records_edit_rights($id) {
		global $gestion_acces_active, $gestion_acces_user_notice;
		global $PMBuserid;
		global $class_path;
	
		//droits d'acces utilisateur/notice
		$acces_m=1;
		$acces_jm='';
		if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
			require_once("$class_path/acces.class.php");
			$ac= new acces();
			$dom_1= $ac->setDomain(1);
			$acces_jm = $dom_1->getJoin($PMBuserid,8,'num_notice');	//modification
			if ($acces_jm) {
				$query = "select count(1) from avis $acces_jm where id_avis=".$id;
				$result = pmb_mysql_query($query);
				if(pmb_mysql_result($result,0,0)==0) {
					$acces_m=0;
				}
			}
		}
		return $acces_m;
	}
	
	public static function check_records_read_rights() {
		global $gestion_acces_active, $gestion_acces_user_notice;
		global $PMBuserid;
		global $class_path;
	
		//droits d'acces utilisateur/notice
		$acces_jl='';
		if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
			require_once("$class_path/acces.class.php");
			$ac= new acces();
			$dom_1= $ac->setDomain(1);
			$acces_jl = $dom_1->getJoin($PMBuserid,4,'num_notice');	//lecture
		}
		return $acces_jl;
	}
	
	public static function delete_from_object($id) {
		$query = "delete from avis where num_notice=".$id." and type_object = ".AVIS_RECORDS;
		pmb_mysql_query($query);
	}
}