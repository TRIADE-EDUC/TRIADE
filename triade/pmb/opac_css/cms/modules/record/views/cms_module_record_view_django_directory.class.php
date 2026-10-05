<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_record_view_django_directory.class.php,v 1.2 2017/06/06 15:26:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_record_view_django_directory extends cms_module_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		
		$this->default_template = "{{record.content}}";
	}
	
	public function get_form(){
		if(!isset($this->parameters['django_directory'])) $this->parameters['django_directory'] = '';
		$form = parent::get_form();
		$form.= "
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_record_view_django_directory'>".$this->format_text($this->msg['cms_module_record_view_django_directory'])."</label>
				</div>
				<div class='colonne-suite'>
					<select name='cms_module_record_view_django_directory'>";
		$form.= $this->get_directories_options($this->parameters['django_directory']);
		$form.= "
					</select>
				</div>
			</div>
		";
		return $form;
	}
	
	public function save_form(){
		global $cms_module_record_view_django_directory;
		
		$this->parameters['django_directory'] = $cms_module_record_view_django_directory;
		return parent::save_form();
	}
	
	public function render($datas){
		$render_datas = array();
		$render_datas['record'] = array();
		if ($datas) {
			$render_datas['record'] = array(
					'content' => record_display::get_display_extended($datas, $this->parameters['django_directory'])
			);
		}
		
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$format[] =	array(
			'var' => "records",
			'desc' => $this->msg['cms_module_record_view_records_desc'],
			'children' => array(
				array(
					'var' => "records[i].content",
					'desc'=> $this->msg['cms_module_record_view_record_content_desc']
				)
			)
		);
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
	
	public function get_directories_options($selected = '') {
		global $opac_notices_format_django_directory;
		
		if (!$selected) {
			$selected = $opac_notices_format_django_directory;
		}
		if (!$selected) {
			$selected = 'common';
		}
		$dirs = array_filter(glob('./opac_css/includes/templates/record/*'), 'is_dir');
		$tpl = "";
		foreach($dirs as $dir){
			if(basename($dir) != "CVS"){
				$tpl.= "<option ".(basename($dir) == basename($selected) ? "selected='selected'" : "")." value='".basename($dir)."'>
				".basename($dir)."</option>";
			}
		}
		return $tpl;
	}
}