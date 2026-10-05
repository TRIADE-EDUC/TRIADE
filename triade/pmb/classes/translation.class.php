<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: translation.class.php,v 1.20.2.1.2.2 2025/02/17 15:30:21 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))	die("no access");

global $include_path;
require_once($include_path."/templates/translation.tpl.php");

/**
 * Classe permettant de gerer les traductions de libelles
 * Utilise la table translation, croisee avec le nom de la table et du champ a traduire
 * Memorise et recupere le texte dans la langue voulue
 * 
 "CREATE TABLE translation (
    trans_table VARCHAR( 255 ) NOT NULL default '',
    trans_field VARCHAR( 255 ) NOT NULL default '',
    trans_lang VARCHAR( 255 ) NOT NULL default '',
   	trans_num INT( 8 ) UNSIGNED NOT NULL default 0 ,
    trans_text VARCHAR( 255 ) NOT NULL default '',
    PRIMARY KEY trans (trans_table,trans_field,trans_lang,trans_num),
    index i_lang(trans_lang)
   )";  
 */
	
class translation {

	protected $num_field;
	
	protected $table_name;
	
	protected static $languages;
	
	protected $data;
	
	protected static $text_fields = array();
	/**
	 * Type de donnée (small_text, text)
	 * @var string
	 */
	protected $type;
	
	public function __construct($num_field, $table_name) {
		$this->num_field = intval($num_field);
		$this->table_name = $table_name;
		$this->fetch_data();
	}
	
	protected static function _init_languages() {
		global $opac_show_languages;
		global $include_path;
		
		if(!isset(static::$languages)) {
			static::$languages = array();
			if(!empty($opac_show_languages)) {
				$languages = explode(',', explode(' ', trim($opac_show_languages))[1]);
				if(count($languages)) {
					$langues = new XMLlist($include_path."/messages/languages.xml");
					$langues->analyser();
					$clang = $langues->table;
					foreach ($languages as $language) {
// 						if(static::get_user_lang() != $language) {
							static::$languages[] = array(
									'code' => $language,
									'label' => (!empty($clang[$language]) ? $clang[$language] : $language),
                                    'is_current_lang' => (static::get_user_lang() == $language ? true : false)
							);
// 						}
					}
				}
			}
		}
	}
	
	// récupération des infos en base
	public function fetch_data() {
		$this->data = array();
		
		$query = "SELECT * FROM translation WHERE trans_table='".addslashes($this->table_name)."' and trans_num='".addslashes($this->num_field)."' ";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){		
			while(($row = pmb_mysql_fetch_object($result))) {
			    if(empty($this->data[$row->trans_field])) {
			        $this->data[$row->trans_field] = array();
			    }
				$this->data[$row->trans_field][$row->trans_lang] = ($row->trans_small_text ? $row->trans_small_text : $row->trans_text);
			}	
		}
	}
	
	/**
	 * 
	 * @param string $dom_node_id
	 */
	public function connect($dom_node_id) {

	    static::_init_languages();
	    if(empty(static::$languages)) {
	        return '';
	    }
		return "
		<script>
			require(['apps/pmb/Translations', 'dojo/domReady!'], function(Translations){
				new Translations('".$dom_node_id."', '".addslashes($this->get_json_data())."', '".addslashes($this->get_json_languages())."');
			});
		</script>";
	}
	
	/**
	 * A ne plus utiliser à l'avenir
	 * @param string $label
	 * @param integer $field_id
	 * @param string $field_name
	 * @param string $field_value
	 * @param string $class_saisie
	 * @param string $style_form
	 */	
	public function get_form($label, $field_id, $field_name, $field_value, $class_saisie, $style_form="display: none;") {
		global $msg, $charset;
		global $translation_tpl_form_javascript, $translation_tpl_form, $translation_tpl_line_form;
		global $translation_tpl_form_javascript_flag;
		global $lang, $include_path;
				
		$input_tpl_name = 'translation_tpl_form_input_'.$this->type;
		global ${$input_tpl_name};
		
		$langues = new XMLlist($include_path."/messages/languages.xml");
		$langues->analyser();
		$clang = $langues->table;
		
		$line = "";
		$nb = 0;
		static::_init_languages();
		foreach(static::$languages as $langue) {
			if($langue != $lang) {
				$line.= str_replace("!!libelle_lang!!", $clang[$langue], $translation_tpl_line_form);		
				$line = str_replace("!!translation_form_line_input!!", ${$input_tpl_name}, $line);
				$line = str_replace("!!lang!!", $langue.'_', $line);
				$line = str_replace("!!field_value!!", htmlentities((isset($this->data[$field_name][$langue]) ? $this->data[$field_name][$langue] : ''), ENT_QUOTES, $charset), $line);
				$nb++;
			}
		}		
		$form = str_replace("!!lang_list!!", $line, $translation_tpl_form);
		if($nb) {
			$translation_button = "<input class='bouton_small' value='".$msg["translation_button"]."' onclick=\"translation_view('lang_!!field_id!!')\" type='button'>";
		}else {
			$translation_button = "";
		}
		if($label) {
			$form = str_replace("!!translation_button!!", $translation_button, $form);
			$form = str_replace("!!translation_button_no_label!!", '', $form);		
		}else {
			$form = str_replace("!!translation_button!!", '', $form);	
			$form = str_replace("!!translation_button_no_label!!", $translation_button, $form);			
		}
		$form = str_replace("!!translation_form_input!!", ${$input_tpl_name}, $form);
		$form = str_replace("!!lang!!", '', $form);
		$form = str_replace("!!label!!", $label, $form);
		$form = str_replace("!!class_saisie!!", $class_saisie, $form);
		$form = str_replace("!!field_id!!", $field_id, $form);
		$form = str_replace("!!field_name!!", $field_name, $form);
		$form = str_replace("!!field_value!!", htmlentities($field_value, ENT_QUOTES, $charset), $form);
		$form = str_replace("!!class_form!!", "class_form", $form);
		$form = str_replace("!!style_form!!", $style_form, $form);	
	
		if(!$translation_tpl_form_javascript_flag) {
			$form = $translation_tpl_form_javascript.$form;
			$translation_tpl_form_javascript_flag++;
		}
		return $form;
	}
	
	protected function save_from_field_values($field_name, $field_values, $langue, $type) {
	    if(is_array($field_values)) {
	        foreach ($field_values as $value) {
	            $this->save($field_name, $langue, $type, stripslashes($value ?? ''));
	        }
	    } else {
	        $this->save($field_name, $langue, $type, stripslashes($field_values ?? ''));
	    }
	}
	
	public function update($field_name, $input_field = '', $type = 'small_text') {
		if(!$input_field) {
			$input_field = $field_name;
		}
		// effacer les anciens
		static::delete($this->num_field, $this->table_name, $field_name);
		
		// enregistrement du champ par défaut dans la langue traduite de l'utilisateur
		$field = $input_field;
		global ${$field};
		$this->save_from_field_values($field_name, ${$field}, static::get_user_lang(), $type);
		
		static::_init_languages();
		foreach(static::$languages as $langue) {
			$field = $langue['code'].'_'.$input_field;
			global ${$field};
			$this->save_from_field_values($field_name, ${$field}, $langue['code'], $type);
		}
	}
	
	public function update_small_text($field_name, $input_field = '') {
		$this->update($field_name, $input_field, 'small_text');
	}
	
	public function update_text($field_name, $input_field = '') {
		$this->update($field_name, $input_field, 'text');
	}
	
	public function update_array($field_name, $input_field = '', $type = 'small_text') {
	    if(!$input_field) {
	        $input_field = $field_name;
	    }
	    // effacer les anciens
	    static::delete($this->num_field, $this->table_name, $field_name);
	    
	    if(strpos($input_field, '[') !== false) {
	        $field = pmb_substr($input_field, 0, strpos($input_field, '['));
	        $indice = pmb_substr($input_field, strpos($input_field, '[')+1, strpos(pmb_substr($input_field, strpos($input_field, '[')+1), ']'));
	        
	        // enregistrement du champ par défaut dans la langue traduite de l'utilisateur
	        global ${$field};
	        $this->save_from_field_values($field_name, ${$field}[$indice], static::get_user_lang(), $type);
	        
	        static::_init_languages();
	        foreach(static::$languages as $langue) {
	            $field = $langue['code'].'_'.pmb_substr($input_field, 0, strpos($input_field, '['));
	            $indice = pmb_substr($input_field, strpos($input_field, '[')+1, strpos(pmb_substr($input_field, strpos($input_field, '[')+1), ']'));
	            global ${$field};
	            $this->save_from_field_values($field_name, ${$field}[$indice], $langue['code'], $type);
	        }
	    }
	}
		
	public function save($field_name, $langue, $type, $text) {
		if($text) {
			$query = "INSERT into translation set trans_table='".$this->table_name."', trans_field='".$field_name."', trans_lang='".$langue."', trans_num='".$this->num_field."', trans_".$type."='".addslashes($text)."' ";
			pmb_mysql_query($query);
		}
	}
	
	public static function delete($num, $table, $field='') {
		$query = "delete from translation WHERE trans_num='".$num."' AND trans_table='".$table."'";
		if($field) {
			$query .= " AND trans_field='".$field."'";
		}
		pmb_mysql_query($query);
	}
	
	public function get_data() {
		return $this->data;
	}
	
	public function set_data($data) {
	    $this->data = $data;
	    return $this;
	}
	
	public function get_json_data() {
		/*
		Cela n'est plus nécessaire : conservation temporaire au cas où
		$data = $this->data;
		foreach ($data as $field_name=>$field_data) {
			foreach ($field_data as $lang=>$field_value) {
				// on addslashes seulement les doubles quotes sinon il y a une erreur de JSON.parse dans Translation.js
				$value = addcslashes($field_value, '"');
				$value = str_replace(array("\n", "\r", "\t"), array("\\n", "\\r", "\\t"), $value);
				$data[$field_name][$lang] = $value;
			}
		}*/
		return encoding_normalize::json_encode($this->data);
	}
	
	public function get_json_languages() {
	    return encoding_normalize::json_encode(static::get_languages());
	}
	
	/**
	 * Retourne la traduction dans la langue voulue
	 */
	public function get_text($field_name, $langue) {
		return $this->data[$field_name][$langue];
	}
	
	public function set_text($field_name, $langue, $text) {
		$this->data[$field_name][$langue] = $text;
	}
	
	public static function get_languages() {
		if(!isset(static::$languages)) {
			static::_init_languages();
		}
		return static::$languages;
	}
	
	public static function get_user_lang() {
		global $lang;
		return $lang;
	}
	
	/**
	 * Retourne la traduction d'un champ dans la langue voulue, ou le libellé par défaut
	 * @param int $id Identifiant de l'entité
	 * @param string $trans_table Table de référence
	 * @param string $trans_field Champ de référence
	 * @param string $text Libellé par défaut
	 * @param string $mylang Langue voulue
	 * @return string
	 */
	public static function get_translated_text($id, $trans_table, $trans_field, $text="", $mylang="") {
		global $lang;
	
		if(!$mylang) {
			$mylang = $lang;
		}
		self::get_text_fields($trans_table, $mylang, $id);
		if (!isset(self::$text_fields[$trans_table][$id][$mylang][$trans_field])) {
			self::$text_fields[$trans_table][$id][$mylang][$trans_field] = $text;
		}
		return self::$text_fields[$trans_table][$id][$mylang][$trans_field];
	}
	
	public static function get_text_fields($table, $lang, $num){
		if(!isset(self::$text_fields[$table])){
			self::$text_fields[$table] = array();
		}
		if(!isset(self::$text_fields[$table][$num])){
			self::$text_fields[$table][$num] = array();
		}
		if(isset(self::$text_fields[$table][$num][$lang])){
			return self::$text_fields[$table][$num][$lang]; 
		}
		self::$text_fields[$table][$num][$lang] = array();
		
		$query = "SELECT trans_field, trans_small_text, trans_text FROM translation WHERE trans_table='".$table."' and trans_lang='".$lang."' and trans_num='".$num."' ";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while(($row = pmb_mysql_fetch_assoc($result))) {
				self::$text_fields[$table][$num][$lang][$row['trans_field']] = ($row['trans_small_text'] ? $row['trans_small_text'] : $row['trans_text']); 
			}
			pmb_mysql_free_result($result);
		}
		return self::$text_fields[$table][$num][$lang];
	}
}
