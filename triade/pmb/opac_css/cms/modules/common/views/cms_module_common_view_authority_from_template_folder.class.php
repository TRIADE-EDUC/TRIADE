<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_authority_from_template_folder.class.php,v 1.2 2017/06/06 15:26:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_view_authority_from_template_folder extends cms_module_common_view_django{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "{{ authority.content }}";
	}
	
	public function get_form(){
		if(!isset($this->parameters['used_template_folder'])) $this->parameters['used_template_folder'] = '';
		$form = parent::get_form();
		$form.= "
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_authority_used_template_folder'>".$this->format_text($this->msg['cms_module_common_view_authority_used_template_folder'])."</label>
				</div>
				<div class='colonne-suite'>
					<select name='cms_module_common_view_authority_used_template_folder'>";
		
		$form.= auth_templates::get_directories_options($this->parameters['used_template_folder']);
		$form.= "
					</select>
				</div>
			</div>
		";
		return $form;
	}
	
	public function save_form(){
		global $cms_module_common_view_authority_used_template_folder;
		
		$this->parameters['used_template_folder'] = $cms_module_common_view_authority_used_template_folder;
		return parent::save_form();
	}
	
	public function render($datas){
		if (is_object($datas)) {
			$render_datas = array(
					'authority' => array(
							'content' => $datas->render(array(), $this->parameters['used_template_folder'])
					)
			);
		}
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$authority = array(
			'var' => "authority.content",
			'desc' => $this->msg['cms_module_common_view_authority_content_desc']
		);
		$format[] = $authority;
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}