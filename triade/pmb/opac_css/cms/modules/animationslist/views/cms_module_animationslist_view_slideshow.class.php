<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_animationslist_view_slideshow.class.php,v 1.2 2023/04/21 12:53:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Animations\Models\AnimationModel;

class cms_module_animationslist_view_slideshow extends cms_module_common_view_slideshow {
	
	public function __construct($id = 0) {
	    parent::__construct($id);
	    $this->default_template = "
<div id='carousel_{{id}}' data-uk-slideshow>
    <ul class='uk-slideshow'>
    	{% for animation in animations %}
    		<li>{{ animation.name }}</li>
    	{% endfor %}
    </ul>
</div>
";
	}
	
	public function get_form() {
		$form = "
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_animationslist_view_slideshow_link'>".$this->format_text($this->msg['cms_module_animationslist_view_slideshow_link'])."</label>
			</div>
			<div class='colonne-suite'>";
		$form .= $this->get_constructor_link_form('animation');
		$form .= "
			</div>
		</div>";
		$form .= parent::get_form();
		
		return $form;
	}
	
	public function save_form() {
		$this->save_constructor_link_form('animation');
		
		return parent::save_form();
	}
	
	public function render($datas) {
	    $animation_data = [];
	    if (! empty($datas['animations'])) {
    	    foreach ($datas['animations'] as $animation_id) {
    	        if (! empty($animation_id)) {
    	            $animation = new AnimationModel($animation_id);
    	            $animation->getViewData();
    	            $animation_data[] = $animation;
    	        }
    	    }
	    }
	    
	    $render_data = array(
	        'title' => $datas['title'] ?? '',
	        'animations' => $animation_data,
	    );
	    
	    return parent::render($render_data);
	}
	
	public function get_format_data_structure() {
	    $animation = new AnimationModel();
	    
	    return array_merge($animation->getCmsStructure('animation'), parent::get_format_data_structure());
	}
}