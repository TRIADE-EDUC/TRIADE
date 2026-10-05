<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: translation_ajax.class.php,v 1.1 2023/06/22 14:23:07 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))	die("no access");

global $include_path;
require_once($include_path."/templates/translation.tpl.php");
	
class translation_ajax extends translation {

	protected $form_data;
	
	public function update($field_name, $input_field = '', $type = 'small_text') {
		if(!$input_field) {
			$input_field = $field_name;
		}
		// effacer les anciens
		static::delete($this->num_field, $this->table_name, $field_name);
		
		// enregistrement du champ par défaut dans la langue traduite de l'utilisateur
		$field = $input_field;
		if(is_array($this->form_data[$field])) {
			foreach ($this->form_data[$field] as $value) {

				$this->save($field_name, static::get_user_lang(), $type, $value);
			}
		} else {
			$this->save($field_name, static::get_user_lang(), $type, $this->form_data[$field]);
		}
		
		static::_init_languages();
		foreach(static::$languages as $langue) {
			$field = $langue['code'].'_'.$input_field;
			if(is_array($this->form_data[$field])) {
				foreach ($this->form_data[$field] as $value) {
					$this->save($field_name, $langue['code'], $type, $value);
				}
			} else {
				$this->save($field_name, $langue['code'], $type, $this->form_data[$field]);
			}
		}
	}

	public function get_form_data() {
		return $this->form_data;
	}

	public function set_form_data($form_data) {
		return $this->form_data = $form_data;
	}
}
