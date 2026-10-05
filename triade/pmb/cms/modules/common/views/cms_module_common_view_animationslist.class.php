<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_animationslist.class.php,v 1.5 2023/04/21 06:42:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Animations\Models\AnimationModel;

class cms_module_common_view_animationslist extends cms_module_common_view_django {
    
	public function __construct($id = 0) {
		parent::__construct($id);
		
		$this->default_template = "{% for animation in animations %}
    <p>{{ animation.name }}</p>
    <blockquote>{{ animation.description }}</blockquote>
{% endfor %}";
	}
	
	public function get_form() {
		if (!isset($this->parameters['used_template'])) $this->parameters['used_template'] = '';
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_animationslist_link'>".$this->format_text($this->msg['cms_module_common_view_animationslist_link'])."</label>
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
		global $cms_module_common_view_animationslist_used_template;
		
		$this->save_constructor_link_form('animation');
		$this->parameters['used_template'] = $cms_module_common_view_animationslist_used_template;
		
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
	    
	    // Données de la pagination
	    if(isset($datas['paging']) && $datas['paging']['activate']) {
	        $render_data['paging'] = $datas['paging'];
	    }
	    
	    return parent::render($render_data);
	}
	
	public function get_format_data_structure() {
        $animation = new AnimationModel();
        $format = $animation->getCmsStructure('animation');
        $format[] = array(
            'var' => "paginator",
            'desc' => $this->msg['cms_module_common_view_list_paging_title'],
            'children' => array(
                array(
                    'var' => "paginator.paginator",
                    'desc' => $this->msg['cms_module_common_view_list_paging_paginator_title']
                ),
                array(
                    'var' => "paginator.nbPerPageSelector",
                    'desc' => $this->msg['cms_module_common_view_list_paging_nb_per_page_title']
                ),
                array(
                    'var' => "paginator.navigator",
                    'desc' => $this->msg['cms_module_common_view_list_paging_navigator_title']
                )
            )
        );
        return array_merge($format, parent::get_format_data_structure());
	}
}