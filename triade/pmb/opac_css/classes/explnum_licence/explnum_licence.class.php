<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_licence.class.php,v 1.8 2024/03/22 15:31:04 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Spipu\Html2Pdf\Html2Pdf;

global $class_path, $include_path;
require_once($include_path.'/templates/explnum_licence/explnum_licence.tpl.php');
require_once($class_path.'/explnum_licence/explnum_licence_profile.class.php');
require_once($class_path.'/explnum_licence/explnum_licence_right.class.php');
require_once($class_path.'/translation.class.php');

/**
 * Classe de gestion des régimes de licence
 * @author apetithomme, vtouchard
 *
 */
class explnum_licence {
	/**
	 * Identifiant
	 * @var int
	 */
	protected $id;
	
	/**
	 * Libellé du régime de licence
	 * @var string
	 */
	protected $label;
	
	/**
	 * URI
	 * @var string
	 */
	protected $uri;
	
	/**
	 * Profils associés
	 * @var explnum_licence_profile
	 */
	protected $profiles;
	
	/**
	 * Droits associés
	 * @var explnum_licence_right
	 */
	protected $rights;
	
	public static $script_included = false;
	
	public function __construct($id = 0) {
		$this->id = intval($id);
	}
	
	public function fetch_data() {
		if (!$this->id) {
			return false;
		}
		$query = 'select explnum_licence_label, explnum_licence_uri from explnum_licence where id_explnum_licence = '.$this->id;
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_assoc($result);
		if (count($row)) {
			$this->label = translation::get_text($this->id, 'explnum_licence', 'explnum_licence_label', $row['explnum_licence_label']);
			$this->uri = translation::get_text($this->id, 'explnum_licence', 'explnum_licence_uri', $row['explnum_licence_uri']);
		}
	}
	
	public function is_used() {
		$this->get_profiles();
		foreach ($this->profiles as $profile) {
			if ($profile->is_used()) {
				return true;
			}
		}
		return false;
	}
		
	public function get_rights() {
		if (isset($this->rights)) {
			return $this->rights;
		}
		$this->rights = array();
		$query = 'select id_explnum_licence_right from explnum_licence_rights where explnum_licence_right_explnum_licence_num = "'.$this->id.'" order by explnum_licence_right_label';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_assoc($result)){
				$this->rights[$row['id_explnum_licence_right']] = new explnum_licence_right($row['id_explnum_licence_right']);
			}
		}
		return $this->rights;
	}
	
	public static function get_explnum_licence_profiles($explnum_id){
		$explnum_id = intval($explnum_id);
		$ids = array();
		
		if(!$explnum_id){
			return $ids;
		}
		$result = pmb_mysql_query('
			select explnum_licence_profile_explnums.explnum_licence_profile_explnums_profile_num, explnum_licence_profiles.explnum_licence_profile_explnum_licence_num
			from explnum_licence_profile_explnums join explnum_licence_profiles
			on explnum_licence_profiles.id_explnum_licence_profile = explnum_licence_profile_explnums.explnum_licence_profile_explnums_profile_num
			where explnum_licence_profile_explnums.explnum_licence_profile_explnums_explnum_num = '.$explnum_id);
		$ids = array();
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_assoc($result)){
				if(!isset($ids[$row['explnum_licence_profile_explnum_licence_num']])){
					$ids[$row['explnum_licence_profile_explnum_licence_num']] = array();
				}
				$ids[$row['explnum_licence_profile_explnum_licence_num']][] = $row['explnum_licence_profile_explnums_profile_num'];
			}
		}	
		return $ids;
	}
	
	public function get_profiles() {
		if (isset($this->profiles)) {
			return $this->profiles;
		}
		$this->profiles = array();
		$query = 'select id_explnum_licence_profile from explnum_licence_profiles where explnum_licence_profile_explnum_licence_num = "'.$this->id.'" order by explnum_licence_profile_label';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_assoc($result)) {
				$this->profiles[$row['id_explnum_licence_profile']] = new explnum_licence_profile($row['id_explnum_licence_profile']);
			}
		}
		return $this->profiles;
	}
	
	public function get_label(){
		if(!isset($this->label)){
			$this->fetch_data();
		}
		return $this->label;
	}
	
	public function get_uri(){
		if(!isset($this->uri)){
			$this->fetch_data();
		}
		return $this->uri;
	}
	
	public static function get_explnum_licence_picto($explnum_id) {
		if (!$explnum_id) {
			return '';
		}
		global $explnum_licence_info_picto, $explnum_licence_script_dialog;
		$html = '';
		$profiles = self::get_explnum_licence_profiles($explnum_id);
		if (!count($profiles)) {
			return $html;
		}
		if(!self::$script_included){
			$html = $explnum_licence_script_dialog;
			self::$script_included = true;
		}
		$html.= str_replace('!!explnum_id!!', $explnum_id, $explnum_licence_info_picto);
		return $html;
	}
	
	public static function get_explnum_licence_details($explnum_id) {
		if (!$explnum_id) {
			return '';
		}
		global $msg;
		global $charset;
		global $explnum_licence_profile_details, $explnum_licence_right_details;
		$html = '';
		$profiles = self::get_explnum_licence_profiles($explnum_id);
		if (!count($profiles)) {
			return $html;
		}
		foreach ($profiles as $licence_id => $profiles_id) {
			$explnum_licence = new explnum_licence($licence_id);
			foreach($profiles_id as $profile_id){
				$profile_detail = str_replace('!!explnum_licence_label!!', htmlentities($explnum_licence->get_label(), ENT_QUOTES, $charset), $explnum_licence_profile_details);
				$profile_detail = str_replace('!!explnum_licence_uri!!', htmlentities($explnum_licence->get_uri(), ENT_QUOTES, $charset), $profile_detail);
				
				$profile = new explnum_licence_profile($profile_id);
				$profile_detail = str_replace('!!explnum_licence_profile_label!!', htmlentities($profile->get_label(), ENT_QUOTES, $charset), $profile_detail);
				$profile_detail = str_replace('!!explnum_licence_profile_uri!!', htmlentities($profile->get_uri(), ENT_QUOTES, $charset), $profile_detail);
				$profile_detail = str_replace('!!explnum_licence_profile_logo_url!!', htmlentities($profile->get_logo_url(), ENT_QUOTES, $charset), $profile_detail);
				
				$profile_detail = str_replace('!!explnum_licence_profile_image!!', $profile->get_logo_url() ? "<img style='height:30px;' src='".$profile->get_logo_url()."' alt='".htmlentities($profile->get_label(), ENT_QUOTES, $charset)."'/>" : '' , $profile_detail);
				$profile_detail = str_replace('!!explnum_licence_profile_explanation!!', htmlentities($profile->get_explanation(), ENT_QUOTES, $charset), $profile_detail);
				$prohibitions = $authorizations = '';
				foreach ($profile->get_rights() as $right) {
					$right_detail = str_replace('!!explnum_licence_right_image!!', $right->get_logo_url() ? "<img style='height:30px;' src='".$right->get_logo_url()."' alt='".htmlentities($right->get_label(), ENT_QUOTES, $charset)."' />" : '', $explnum_licence_right_details);
					$right_detail = str_replace('!!explnum_licence_right_label!!', htmlentities($right->get_label(), ENT_QUOTES, $charset), $right_detail);
					$right_detail = str_replace('!!explnum_licence_right_explanation!!', htmlentities($right->get_explanation(), ENT_QUOTES, $charset), $right_detail);
					if ($right->get_type()) {
						$authorizations.= $right_detail;
					} else {
						$prohibitions.= $right_detail;
					}
				}
				$explnum_licence_rights_details = '';
				if ($prohibitions) {
					$explnum_licence_rights_details.= '<h4>'.$msg['explnum_licence_right_prohibitions'].'</h4>';
					$explnum_licence_rights_details.= $prohibitions;
				}
				if ($authorizations) {
					$explnum_licence_rights_details.= '<h4>'.$msg['explnum_licence_right_authorisations'].'</h4>';
					$explnum_licence_rights_details.= $authorizations;
				}
				$profile_detail = str_replace('!!explnum_licence_rights_details!!', $explnum_licence_rights_details, $profile_detail);
				$html.= $profile_detail;
			}
		}
		return $html;
	}
	
	public static function get_explnum_licence_as_pdf($explnum_id){
		if (!$explnum_id) {
			return '';
		}
		global $class_path;
		global $explnum_licence_pdf_container_template;
		
		$template = str_replace('!!explnum_licence_profiles_details!!', self::get_explnum_licence_details($explnum_id), $explnum_licence_pdf_container_template);
		$html2pdf = new Html2Pdf('P','A4','fr');
		$html2pdf->writeHTML($template);
		$html2pdf->output('licence_'.$explnum_id.'.pdf');
	}
	
	public static function get_explnum_licence_tooltip($explnum_id){
		if (!$explnum_id) {
			return '';
		}
		$html = '';
		$profiles = self::get_explnum_licence_profiles($explnum_id);
		if (!count($profiles)) {
			return $html;
		}
		foreach ($profiles as $licence_id => $profiles_id) {
			$explnum_licence = new explnum_licence($licence_id);
			$html.= $explnum_licence->get_label().'<br/>';
			foreach($profiles_id as $profile_id){
				$profile = new explnum_licence_profile($profile_id);
				$html.= ($profile->get_logo_url() ? '<img src="'.$profile->get_logo_url().'" height="30px;"/><br/>' : '').$profile->get_label().'<br/>';
			}
		}
		return $html;
	}
	
	public static function get_explnum_licence_quotation($explnum_id){
		global $explnum_licence_profile_quotation;
		
		if (!$explnum_id) {
			return '';
		}
		$html = '';
		$profiles = self::get_explnum_licence_profiles($explnum_id);
		if (!count($profiles)) {
			return $html;
		}
		foreach ($profiles as $profiles_id) {
			foreach($profiles_id as $profile_id){
				$profile = new explnum_licence_profile($profile_id);
				$html.= str_replace('!!profile_quotation!!', $profile->get_quotation_rights_for_explnum($explnum_id), $explnum_licence_profile_quotation);
			}
		}
		return $html;
	}
}