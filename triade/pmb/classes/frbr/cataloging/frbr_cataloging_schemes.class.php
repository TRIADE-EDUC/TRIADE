<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_cataloging_schemes.class.php,v 1.4 2018/03/13 08:57:25 tsamson Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php"))
	die("no access");

include_once($include_path.'/templates/frbr/cataloging/frbr_cataloging_schemes.tpl.php');
require_once($class_path.'/elements_list/elements_cataloging_schemes_list_ui.class.php');

class frbr_cataloging_schemes {
	
	/**
	 * 
	 * @var frbr_cataloging_scheme
	 */
	static protected $schemes;
	
	public function get_schemes_list() {
		global $frbr_cataloging_schemes_table, $frbr_cataloging_schemes_table_line;
		//$module_frbr_cataloging_schemes
		$html = $frbr_cataloging_schemes_table;
		
		$table_lines = '';
		if (is_array($this->get_schemes())) {
			foreach ($this->get_schemes() as $scheme) {
				$table_line = $frbr_cataloging_schemes_table_line;
				$table_line = str_replace('!!scheme_id!!', $scheme->get_id(), $table_line);
				$table_line = str_replace('!!scheme_name!!', $scheme->get_name(), $table_line);
				$table_line = str_replace('!!scheme_start_entity!!', frbr_cataloging_entities::get_label($scheme->get_start_entity_type_uri()), $table_line);
				$table_lines.= $table_line;
			}
		}
		
		$html = str_replace("!!schemes_tab!!", $table_lines, $html);
		
		return $html;
	}
	
	/**
	 * 
	 * @return frbr_cataloging_scheme
	 */
	public function get_schemes() {
		if (isset(static::$schemes)) {
			return static::$schemes;
		}
		static::$schemes = array();
		$query = 'select * where {
				?scheme rdfs:type pmb:cataloging_scheme
		}';
		frbr_cataloging_datastore::query($query);
		
		if (frbr_cataloging_datastore::num_rows()) {
			foreach (frbr_cataloging_datastore::get_result() as $result) {
				static::$schemes[] = new frbr_cataloging_scheme(0, $result->scheme);
			}
		}
		return static::$schemes;
	}
	
	public function get_schemes_select() {
		$schemes = array();
		if (is_array($this->get_schemes())) {
			foreach ($this->get_schemes() as $scheme) {
				$schemes[] =  $scheme->get_id();
			}
		}
		$elements_cataloging_schemes_list_ui = new elements_cataloging_schemes_list_ui($schemes, count($schemes), false);
		return $elements_cataloging_schemes_list_ui->get_elements_list();
	}
}