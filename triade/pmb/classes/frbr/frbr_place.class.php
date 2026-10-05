<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_place.class.php,v 1.9.4.1 2025/04/24 12:37:04 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_place {

	/**
	 * Identifiant de la page associée
	 */
	protected $num_page;

	/**
	 * Cadres associés à la page
	 * @var array
	 */
	protected $cadres;

	public $num_cadre;

	public $visibility;

	public $order;

	public function __construct($num_page=0) {
	    $this->num_page = (int) $num_page;
		$this->fetch_data();
	}

	protected function fetch_data() {
		$this->num_cadre = 0;
		$this->visibility = 1;
		$this->order = 1;
		if($this->num_page) {
			$query = 'select * from frbr_place where place_num_page ='.$this->num_page.' order by place_order';
			$result = pmb_mysql_query($query);
			$row = pmb_mysql_fetch_object($result);
			while ($row = pmb_mysql_fetch_object($result)) {
				$this->cadres[] = array(
						'num_cadre' => $row->place_num_cadre,
						'visibility' => $row->place_visibility,
						'order' => $row->place_order
				);
			}
		}
	}

	public function get_form() {
		global $frbr_page_place_tpl;
		$form = $frbr_page_place_tpl;
		return $form;
	}

	/**
	 * Sauvegarde
	 */
	public function save(){
		$query = "delete from frbr_place where place_num_page = ".$this->num_page;
		pmb_mysql_query($query);
		$page = new frbr_entity_common_entity_page($this->num_page);
		foreach ($this->cadres as $cadre) {
			//maj des parametres de la page
			if ($cadre->cadre_type == "isbd" || $cadre->cadre_type == "records_list" || $cadre->cadre_type == "frbr_graph") {
				$parameter = new stdClass();
				$parameter->{$cadre->cadre_type} = new stdClass();
				$parameter->{$cadre->cadre_type}->value = $cadre->visibility;
				$page->set_parameter($parameter);
			}
			$query = "insert into frbr_place set
				place_num_page = '".$cadre->page."',
				place_num_cadre = '".$cadre->id."',
				place_cadre_type = '".$cadre->cadre_type."',
				place_visibility = '".$cadre->visibility."',
				place_order = '".$cadre->order."'";
			pmb_mysql_query($query);
		}
		self::update_page_parameters_from_placement($page);
		return array(
				'state' => true
		);
	}

	/**
	 * Suppression
	 */
	public static function delete($num_cadre = 0, $num_page=0){
		$num_page = intval($num_page);
		if($num_page) {
			$query = "DELETE FROM frbr_place WHERE place_num_page = '".$num_page."'";
			pmb_mysql_query($query);
			return true;
		} else {
			$num_cadre = intval($num_cadre);
			self::reorder_places($num_cadre);
			$query = "DELETE FROM frbr_place WHERE place_num_cadre = '".$num_cadre."'";
			pmb_mysql_query($query);
			return true;
		}
		return false;
	}

	/**
	 * reordonnement des placements de cadres
	 * @param integer $id_deleted_cadre
	 * @return boolean
	 */
	public static function reorder_places($id_deleted_cadre) {
		$id_deleted_cadre = intval($id_deleted_cadre);
		if ($id_deleted_cadre) {
			$query = "	UPDATE frbr_place
						SET place_order = place_order-1
						WHERE place_num_page =
							(SELECT cadre_num_page
							FROM frbr_cadres
							WHERE id_cadre = '".$id_deleted_cadre."'
							LIMIT 1)
						AND place_order >
							(SELECT old_place_order
							FROM (
								SELECT place_order AS old_place_order
								FROM frbr_place
								WHERE place_num_cadre = '".$id_deleted_cadre."'
								LIMIT 1
								) old_frbr_place
							)";
			pmb_mysql_query($query);
			return true;
		}
		return false;
	}

	public function get_num_page() {
		return $this->num_page;
	}

	public function set_cadres($cadres) {
		$this->cadres = $cadres;
	}

	/**
	 * mise a jour des parametres de la page
	 * @param frbr_entity_common_entity_page $page
	 */
	public static function update_page_parameters_from_placement($page) {
		if ($page && is_object($page)) {
			$query = '	UPDATE frbr_pages SET
						page_parameters = "'.addslashes(encoding_normalize::json_encode($page->get_parameters())).'"
						WHERE id_page= "'.$page->get_id().'"';
			$result = pmb_mysql_query($query);
			if($result) {
				return true;
			}
			return false;
		}
	}
}