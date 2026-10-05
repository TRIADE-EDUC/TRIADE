<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_animationslist_view_django_directory.class.php,v 1.3 2023/04/21 12:53:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Animations\Models\AnimationModel;

class cms_module_animationslist_view_django_directory extends cms_module_common_view_django {
	
	public function __construct($id = 0) {
		parent::__construct($id);
		
		$this->default_template = "{% for animation in animations %}
	{{ animation.content }}
{% endfor %}";
	}
	
	public function get_form() {
		if (!isset($this->parameters['django_directory'])) $this->parameters['django_directory'] = '';
		
		$options = $this->get_directories_options($this->parameters['django_directory']);
		$form = parent::get_form();
		$form .= "
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_animationslist_view_django_directory'>".$this->format_text($this->msg['cms_module_animationslist_view_django_directory'])."</label>
				</div>
				<div class='colonne-suite'>
					<select name='cms_module_animationslist_view_django_directory'>
                        $options
					</select>
				</div>
			</div>
		";
		
		return $form;
	}
	
	public function save_form() {
		global $cms_module_animationslist_view_django_directory;
		
		$this->parameters['django_directory'] = $cms_module_animationslist_view_django_directory;
		return parent::save_form();
	}
	
	public function render($datas) {
	    global $base_path;
	    
		$render_data = array();
		$render_data['animations'] = array();
		$directory = $this->parameters['django_directory'];
		
		$template_path = "$base_path/includes/templates/animations/$directory/animation_display.tpl.html";
		if(file_exists("$base_path/includes/templates/animations/$directory/animation_display_subst.tpl.html")){
		    $template_path = "$base_path/includes/templates/animations/$directory/animation_display_subst.tpl.html";
		}
		$H2o = H2o_collection::get_instance($template_path);
		
		if (is_array($datas['animations'])) {
			foreach ($datas['animations'] as $animation_id) {
			    $animation = new AnimationModel($animation_id);
			    $animation->getViewData();
			    $render_data['animations'][] = array(
			        'content' => $H2o->render(['animation' => $animation])
				);
			}
		}
		
		return parent::render($render_data);
	}
	
	public function get_format_data_structure() {
		$format = array();
		$format[] =	array(
			'var' => 'animations',
			'desc' => $this->msg['cms_module_animationslist_view_django_directory_animations_desc'],
			'children' => array(
				array(
					'var' => 'animations[i].content',
					'desc' => $this->msg['cms_module_animationslist_view_django_directory_animation_content_desc']
				)
			)
		);
		$format = array_merge($format, parent::get_format_data_structure());
		
		return $format;
	}
	
	public function get_directories_options($selected = '') {
		if (empty($selected)) {
			$selected = 'common';
		}
		
		$tpl = '';
		$dirs = array_filter(glob('./opac_css/includes/templates/animations/*'), 'is_dir');
		foreach ($dirs as $dir) {
		    $basename_dir = basename($dir);
		    if ($basename_dir != "CVS") {
		        $tpl .= "<option ".($basename_dir == basename($selected) ? "selected='selected'" : "")." value='$basename_dir'>$basename_dir</option>";
			}
		}
		
		return $tpl;
	}
}