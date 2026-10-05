<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_records_view.class.php,v 1.6 2023/05/03 15:15:15 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/notice_tpl.class.php");

class frbr_entity_records_view extends frbr_entity_common_view_django{
	
	
	public $default_template;

	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "{{record.content}}";
	}
		
	public function render($datas, $grouped_datas = []){	
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_records_view_title"];
		$render_datas['record']['content'] = record_display::get_display_extended($datas[0], (isset($this->parameters->django_directory) ? $this->parameters->django_directory : ""));
		$render_datas['record']['object'] = new record_datas($datas[0]);
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$format[] = array(
			'var' => "title",
			'desc' => $this->msg['frbr_entity_records_view_title']
		);
		$record = array(
			'var' => "record",
			'desc' => $this->msg['frbr_entity_records_view_label'],
			'children' => array(
					array(
						'var' => "record.content",
						'desc'=> $this->msg['frbr_entity_records_view_record_content_desc']
					),
					array(
						'var' => "record.object",
						'desc'=> $this->msg['frbr_entity_records_view_record_object_desc']
					),
				)
		);
		$format[] = $record;
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}

	public function save_form(){
		global $frbr_entity_records_view_django_directory;
	
		if (isset($frbr_entity_records_view_django_directory)) {
			$this->parameters->django_directory = stripslashes($frbr_entity_records_view_django_directory);
		}
		return parent::save_form();
	}
	
	public function get_form() {
		$django_directory = "";
		if(isset($this->parameters->django_directory)) {
			$django_directory = $this->parameters->django_directory;
		}
		$form = "
		<div class='row'>
			<div class='colonne3'>
				<label for='frbr_entity_records_view_django_directory'>".$this->msg["frbr_entity_records_view_django_directory"]."</label>
			</div>
			<div class='colonne-suite'>
				<select id='frbr_entity_records_view_django_directory' name='frbr_entity_records_view_django_directory'>
					".notice_tpl::get_directories_options($django_directory)."
				</select>
			</div>
		</div>
		&nbsp;";
	
		$form .= parent::get_form();
		return $form;
	}
}