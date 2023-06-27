<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: computed_field.class.php,v 1.12 2019-02-20 13:26:14 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// require_once($include_path.'/templates/computed_field.tpl.php');
require_once($class_path.'/contribution_area/computed_fields/computed_field_used.class.php');

class computed_field {
	
	/**
	 * Identifiant
	 * @var int
	 */
	protected $id;
	
	/**
	 * Champs utilisés
	 * @var computed_field_used
	 */
	protected $fields_used;
	
	/**
	 * Template
	 * @var string
	 */
	protected $template;
	
	/**
	 * Identifiant de l'espace de contribution associé
	 * @var int
	 */
	protected $area_num;
	
	/**
	 * Identifiant unique du champ dans l'arbre des scénarios
	 */
	protected $field_num;
	
	public function __construct($id) {
		$id*= 1;
		$this->id = $id;
	}
	
	protected function fetch_data() {
		$this->area_num = 0;
		$this->field_num = '';
		$this->template = '';
		if ($this->id) {
			$query = 'SELECT computed_fields_area_num, computed_fields_field_num, computed_fields_template FROM contribution_area_computed_fields where id_computed_fields = '.$this->id;
			$result = pmb_mysql_query($query);
			if ($row = pmb_mysql_fetch_assoc($result)) {
				$this->area_num = $row['computed_fields_area_num'];
				$this->field_num = $row['computed_fields_field_num'];
				$this->template = $row['computed_fields_template'];
			}
		}
	}
	
	public function get_fields_used() {
		if (isset($this->fields_used)) {
			return $this->fields_used;
		}
		$this->fields_used = array();
		
		$query = 'SELECT id_computed_fields_used FROM contribution_area_computed_fields_used where computed_fields_used_origine_field_num = '.$this->id;
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			foreach ($result as $row) {
				$this->fields_used[$row['id_computed_fields_used']] = new computed_field_used($row['id_computed_fields_used']);
			}
		}
		return $this->fields_used;
	}
	
	public function get_field_num() {
		if (isset($this->field_num)) {
			return $this->field_num;
		}
		$this->fetch_data();
		return $this->field_num;
	}
	
	public function get_template() {
		if (isset($this->template)) {
			return $this->template;
		}
		$this->fetch_data();
		return $this->template;
	}
	
	public function get_data() {
		$data = array(
				'area_num' => $this->area_num,
				'field_num' => $this->field_num,
				'id' => $this->id,
				'template' => $this->template
		);
		$this->get_fields_used();
		$data['fields_used'] = array();
		foreach ($this->fields_used as $field_used) {
			$data['fields_used'][] = $field_used->get_data();
		}
		return $data;
	}
	
	public function save() {
		if (!$this->id) {
			$query= 'INSERT INTO contribution_area_computed_fields (computed_fields_area_num, computed_fields_field_num, computed_fields_template)
				 VALUES ("'.$this->area_num.'", "'.addslashes($this->field_num).'", "'.addslashes($this->template).'")';
			pmb_mysql_query($query);
			$this->id = pmb_mysql_insert_id();
		} else {
			$query= 'UPDATE contribution_area_computed_fields 
				SET computed_fields_area_num = "'.$this->area_num.'", 
				computed_fields_field_num = "'.addslashes($this->field_num).'", 
				computed_fields_template = "'.addslashes($this->template).'"
				WHERE 	id_computed_fields = "'.$this->id.'"';
			pmb_mysql_query($query);
			
			if (count($this->fields_used)) {
				$fields_used_ids = array();
				foreach ($this->fields_used as $field_used) {
					$fields_used_ids[] = $field_used->get_id();
				}
				
				$query = 'delete from contribution_area_computed_fields_used where computed_fields_used_origine_field_num = '.$this->id.' and id_computed_fields_used not in ('.implode(',', $fields_used_ids).')';
				pmb_mysql_query($query);
			}
		}
		foreach ($this->fields_used as $field_used) {
			$field_used->set_origine_field_num($this->id);
			$field_used->save();
		}
	}
	
	public function set_from_form() {
		global $computed_field_area_num, $computed_field_id, $computed_field_field_num, $computed_field_template, $computed_field_fields_used;
		
		$this->area_num = $computed_field_area_num*1;
		$this->field_num = $computed_field_field_num;
		$this->template = stripslashes($computed_field_template);
		$this->fields_used = array();
		$computed_field_fields_used = encoding_normalize::json_decode(stripslashes($computed_field_fields_used), true);
		foreach ($computed_field_fields_used as $computed_field_field_used) {
			$field = new computed_field_used($computed_field_field_used['id']);
			$field->set_field_num($computed_field_field_used['field_num']);
			$field->set_label($computed_field_field_used['label']);
			$field->set_origine_field_num($this->id);
			$field->set_alias($computed_field_field_used['alias']);
			$this->fields_used[] = $field;
		}
	}
	
	public static function get_computed_field_from_field_num($field_num) {
		$id_computed_field = 0;
		$query = 'SELECT id_computed_fields FROM contribution_area_computed_fields WHERE computed_fields_field_num = "'.$field_num.'"';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$id_computed_field = pmb_mysql_result($result, 0, 0);
		}
		return new computed_field($id_computed_field);
	}
	
	public function set_field_num($field_num) {
		$this->field_num = $field_num;
	}
	
	public static function get_area_computed_fields_num($area_id) {
		$area_id*= 1;
		if (!$area_id) {
			return array();
		}
		$fields_num = array();
		$query = 'SELECT computed_fields_field_num from contribution_area_computed_fields where computed_fields_area_num = '.$area_id;
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_assoc($result)) {
				$fields_num[] = $row['computed_fields_field_num'];
			}
		}
		return $fields_num;
	}
	
	public static function get_environment_fields() {
	    global $msg;
	    
	    $fields = array(
//     	        'empr_environment' => array(
//                         'properties' => array(
//                 	            'empr_nom' => $msg[67], 
//                 	            'empr_prenom' => $msg[68], 
//                 	            'empr_codestat' => $msg['codestat_empr'], 
//                 	            'empr_location' => $msg[298]
//                         ),
//                         'label' => $msg['empr_environment']
//     	        )
	    );
	    return $fields;
	}
	
	public static function get_empr_fields() {
		global $msg, $pmb_sur_location_activate;
		
		$p_perso = new parametres_perso("empr");
		$t_fields = $p_perso->get_t_fields();
		$properties = array(
				'nom' => $msg[67],
				'prenom' => $msg[68],
				'codestat' => $msg['codestat_empr'],
				'location' => $msg[298]
		);
		if ($pmb_sur_location_activate) {
			$properties['surloc'] = $msg['sur_location_select_surloc'];
		}
		foreach ($t_fields as $field) {
			$properties["cp_".$field['NAME']] = $field['TITRE'];
		}
		
		$fields = array(
				'properties' => $properties,
				'label' => $msg['empr_environment']
		);
		return $fields;
	}
}
