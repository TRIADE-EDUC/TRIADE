<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_authoritieslist_view_carousel_uikit.class.php,v 1.3 2023/04/21 07:03:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_authoritieslist_view_carousel_uikit extends cms_module_common_view_carousel_uikit {
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "
<div id='carousel_{{id}}' data-uk-slider>
    <div class='uk-slider-container'>
        <ul class='uk-slider'>
            {% for authority in authorities %}
                <li>{{ authority.content }}</li>
            {% endfor %}
        </ul>
    </div>
</div>
";
	}
	
	public function get_form(){
		$form="
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_authoritieslist_view_link'>".$this->format_text($this->msg['cms_module_authoritieslist_view_link'])."</label>
			</div>
			<div class='colonne-suite'>";
		$form.= $this->get_constructor_link_form("authoritieslist");
		$form.="
			</div>
		</div>";
		$form.= parent::get_form();
		return $form;
	}
	
	public function save_form(){
		$this->save_constructor_link_form("notice");
		return parent::save_form();
	}
	
	public function render($datas){
		$render_datas = array(
			'title' => $datas['title'],
			'records' => $datas,
			'add_to_cart_link' => $add_to_cart_link
		);
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){
	    $format_datas = array();
 		$format_datas = array_merge($format_datas,parent::get_format_data_structure());
		return $format_datas;
	}
}