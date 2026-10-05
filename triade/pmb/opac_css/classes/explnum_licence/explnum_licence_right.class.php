<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_licence_right.class.php,v 1.3.8.1 2025/02/12 12:34:09 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/translation.class.php');

/**
 * Classe de gestion des profils de régimes de licence
 * @author apetithomme, vtouchard
 *
 */
class explnum_licence_right {
	/**
	 * Identifiant
	 * @var int
	 */
	protected $id;

	/**
	 * Libellé du profil de régime de licence
	 * @var string
	 */
	protected $label;

	/**
	 * Type (autorisation / interdiction)
	 * @var integer
	 */
	protected $type;

	/**
	 * Identifiant du régime de licence
	 * @var int $explnum_licence_num
	 */
	protected $explnum_licence_num;

	/**
	 * URL du logo
	 * @var string
	 */
	protected $logo_url;

	/**
	 * Phrase d'explication
	 * @var string
	 */
	protected $explanation;

	public function __construct($id = 0) {
		$this->id = intval($id);
	}

	public function fetch_data() {
		if (!$this->id) {
			return false;
		}
		$query = 'select explnum_licence_right_explnum_licence_num, explnum_licence_right_label, explnum_licence_right_logo_url, explnum_licence_right_explanation, explnum_licence_right_type
				from explnum_licence_rights where id_explnum_licence_right = '.$this->id;
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_assoc($result);
		if (count($row)) {
			$this->explnum_licence_num = $row['explnum_licence_right_explnum_licence_num'];
			$this->label = translation::get_text($this->id, 'explnum_licence_rights', 'explnum_licence_right_label', $row['explnum_licence_right_label']);
			$this->logo_url = translation::get_text($this->id, 'explnum_licence_rights', 'explnum_licence_right_logo_url', $row['explnum_licence_right_logo_url']);
			$this->explanation = translation::get_text($this->id, 'explnum_licence_rights', 'explnum_licence_right_explanation', $row['explnum_licence_right_explanation']);
			$this->type = $row['explnum_licence_right_type'];
		}
	}

	public function set_explnum_licence_num($explnum_licence_num) {
	    $this->explnum_licence_num = intval($explnum_licence_num);
		return $this;
	}

	public function get_label(){
		if(!isset($this->label)){
			$this->fetch_data();
		}
		return $this->label;
	}

	public function get_id(){
		return $this->id;
	}

	public function get_logo_url() {
		if (!isset($this->logo_url)) {
			$this->fetch_data();
		}
		return $this->logo_url;
	}

	public function get_explanation() {
		if (!isset($this->explanation)) {
			$this->fetch_data();
		}
		return $this->explanation;
	}

	public function get_type() {
		if (!isset($this->type)) {
			$this->fetch_data();
		}
		return $this->type;
	}
}