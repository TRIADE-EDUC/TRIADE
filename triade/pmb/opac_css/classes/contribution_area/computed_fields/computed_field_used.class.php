<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: computed_field_used.class.php,v 1.7 2024/03/22 15:31:05 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class computed_field_used {
	
	/**
	 * Identifiant
	 * @var 
	 */
	protected $id;
	
	/**
	 * Identifiant unique du champ dans l'arbre des scénarios
	 */
	protected $field_num;
	
	protected $alias;
	
	protected $label;
	
	protected $value;
	
	/**
	 * Identifiant du champ calculé associé
	 * @var int
	 */
	protected $origine_field_num;
	
	/**
	 * Valeurs associées à l'emprunteur connecté
	 * @var array
	 */
	protected static $empr_values;
	
	public function __construct($id) {
		$id = intval($id);
		$this->id = $id;
		$this->fetch_data();
	}
	
	private function fetch_data() {
		if ($this->id) {
			$query = "SELECT computed_fields_used_origine_field_num, computed_fields_used_label, computed_fields_used_num, computed_fields_used_alias
				FROM contribution_area_computed_fields_used
				WHERE id_computed_fields_used = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_assoc($result);
				$this->label = $row['computed_fields_used_label'];
				$this->alias = $row['computed_fields_used_alias'];
				$this->field_num = $row['computed_fields_used_num'];
				$this->origine_field_num = $row['computed_fields_used_origine_field_num'];
			}
		}
	}
	
	public function set_field_num($field_num) {
		$this->field_num = $field_num;
	}
	
	public function set_alias($alias) {
		$this->alias = $alias;
	}
	
	public function set_label($label) {
		$this->label = $label;
	}
	
	public function set_origine_field_num($origine_field_num) {
		$this->origine_field_num = $origine_field_num;
	}
	
	public function save() {
		if (!$this->id) {
			$query = "INSERT INTO contribution_area_computed_fields_used";
			$clause = "";
		} else {
			$query = "UPDATE contribution_area_computed_fields_used";
			$clause = " WHERE id_computed_fields_used = '".$this->id."'";
		}
		$query.= " SET computed_fields_used_origine_field_num = '".$this->origine_field_num."',
					computed_fields_used_label = '".$this->label."',
					computed_fields_used_num = '".$this->field_num."',
					computed_fields_used_alias = '".$this->alias."'";
		pmb_mysql_query($query.$clause);
		if (!$this->id) {
			$this->id = pmb_mysql_insert_id();
		}
	}
	
	public function get_data() {
		$data = array(
				'id' => $this->id,
				'label' => $this->label,
				'alias' => $this->alias,
				'field_num' => $this->field_num,
				'value' => $this->get_value()
		);
		return $data;
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_value() {
		if (isset($this->value)) {
			return $this->value;
		}
		$this->value = "";
		
		if (strpos($this->field_num, "env_") === 0) {
			$global_var = substr($this->field_num, 4);
			//variables d'environnement
			global ${$global_var};
			
			if (isset(${$global_var})) {
				$this->value = array(
						'value' => ${$global_var},
						'display_label' => ${$global_var}
				);
			}
			return $this->value;
		}
		if (strpos($this->field_num, "empr_") === 0) {
			return $this->get_empr_value();
		}
	}
	
	protected function get_empr_value() {
		global $pmb_sur_location_activate;
		if (empty($_SESSION["user_code"])) {
			return '';
		}
		$field_num = $this->field_num;
		if (strpos($field_num, '-') !== false) {
			$field_num = substr($field_num, 0, strpos($field_num, '-'));
		}
		if (isset(self::$empr_values)) {
			$this->value = self::$empr_values[$field_num];
			return $this->value;
		}
		self::$empr_values = array();
		$query = "SELECT id_empr, empr_nom, empr_prenom, empr_codestat, empr_location FROM empr WHERE empr_login = '".$_SESSION["user_code"]."'";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$empr_values = pmb_mysql_fetch_assoc($result);
			foreach ($empr_values as $key => $empr_value) {
				switch ($key) {
					case "empr_codestat":
						$query = "SELECT libelle FROM empr_codestat WHERE idcode=".$empr_value;
						self::$empr_values[$key] = array(
								"value" => $empr_value,
								"display_label" => pmb_mysql_result(pmb_mysql_query($query), 0, 0)
						);
						break;
					case "empr_location":
						if ($pmb_sur_location_activate) {
							$query = "SELECT surloc_locations.idlocation as surloc_locid, 
                                            empr_location.surloc_num, 
                                            surloc_locations.location_libelle, 
                                            sur_location.surloc_libelle, 
                                            surloc_locations.locdoc_owner AS owner_id, 
                                            lenders.lender_libelle AS owner_libelle, 
                                            surloc_locations.locdoc_codage_import, 
                                            empr_location.locdoc_codage_import AS empr_loc_codage_import,
                                            empr_location.location_libelle AS empr_loc_libelle
										FROM docs_location AS surloc_locations
										JOIN docs_location AS empr_location ON surloc_locations.surloc_num = empr_location.surloc_num
										LEFT JOIN sur_location ON surloc_locations.surloc_num = sur_location.surloc_id
										LEFT JOIN lenders ON surloc_locations.locdoc_owner = lenders.idlender
										WHERE empr_location.idlocation = ".$empr_value."
										ORDER BY surloc_locations.location_libelle";
							$result = pmb_mysql_query($query);
							
							$surloc = array(
									'value' => 0,
									'display_label' => '',
									'locations' => array()
							);
							if (pmb_mysql_num_rows($result)) {
								while ($row = pmb_mysql_fetch_assoc($result)) {
									if (empty($surloc['value'])) {
										$surloc['value'] = $row['surloc_num'];
										$surloc['display_label'] = $row['surloc_libelle'];
										self::$empr_values[$key] = array(
										    "value" => $empr_value,
										    "display_label" => $row['empr_loc_libelle'],
										    "codage_import" => $row['empr_loc_codage_import']
										);
									}
									$surloc['locations'][] = array(
											'value' => $row['surloc_locid'],
											'display_label' => $row['location_libelle'],
											'lender' => array(
													'value' => $row['owner_id'],
													'display_label' => (!empty($row['owner_libelle']) ? $row['owner_libelle'] : '')
											),
											'codage_import' => $row['locdoc_codage_import']
									);
								}
							}
							self::$empr_values['empr_surloc'] = $surloc;
							break;
						}
						
						self::$empr_values[$key] = array(
						    "value" => $empr_value
					    );
						$query = "SELECT location_libelle, locdoc_codage_import FROM docs_location
									WHERE idlocation = ".$empr_value;
						if (pmb_mysql_num_rows($result)) {
						    $row = pmb_mysql_fetch_assoc($result);
						    self::$empr_values[$key]["display_label"] = $row['empr_loc_libelle'];
						    self::$empr_values[$key]["codage_import"] = $row['empr_loc_codage_import'];
						}
						break;
					default :
						self::$empr_values[$key] = array(
								"value" => $empr_value,
								"display_label" => $empr_value
						);
						break;
				}
			}
		}
		$ppersos = new parametres_perso("empr");
		$out_values = $ppersos->get_out_values(self::$empr_values["id_empr"]["value"]);
		foreach ($out_values as $cp_name => $cp_values) {
			self::$empr_values["empr_cp_".$cp_name] = array(
					"value" => $cp_values["values"][0]["value"],
					"display_label" => $cp_values["values"][0]["format_value"]
			);
		}
		$this->value = self::$empr_values[$field_num];
		return $this->value;
	}
}
