<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_sectionslist_view_carousel_uikit.class.php,v 1.5.2.1 2025/01/17 10:40:42 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_sectionslist_view_carousel_uikit extends cms_module_common_view_carousel_uikit{


	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "
<div id='carousel_{{id}}' data-uk-slider>
    <div class='uk-slider-container'>
	    <ul class='uk-slider'>
        	{% for record in records %}
        		<li class='{{id}}_item'>
    				<img src='{% if record.logo.exists %}{{record.logo.vign}}{% else %}{{no_image_url}}{% endif %}'/>
        		</li>
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
				<label for='cms_module_sectionslist_view_link'>".$this->format_text($this->msg['cms_module_sectionslist_view_link'])."</label>
			</div>
			<div class='colonne-suite'>";
		$form.= $this->get_constructor_link_form("section");
		$form.="
			</div>
		</div>";
		$form.= parent::get_form();
		return $form;
	}

	public function save_form(){
		$this->save_constructor_link_form("section");
		return parent::save_form();
	}

	public function render($datas){
		$records = array();
		$links = [
		    "article" => $this->get_constructed_link("article", "!!id!!"),
		    "section" => $this->get_constructed_link("section", "!!id!!")
		];
		$local_ids = $datas;
		if (isset($datas["sections"])) {
		    $local_ids = $datas["sections"];
		}

		if (is_countable($local_ids)) {
			for($i=0 ; $i<count($local_ids) ; $i++){
				$section = new cms_section($local_ids[$i]);
				$infos = $section->format_datas($links);
				$records[]= $infos;
			}
		}
		return parent::render(array('records' => $records));
	}

	public function get_format_data_structure(){
		$datas = cms_section::get_format_data_structure(false,false);
		$datas[] = array(
			'var' => "link",
			'desc'=> $this->msg['cms_module_sectionslist_view_carousel_link_desc']
		);

		$format_datas = array(
			array(
				'var' => "records",
				'desc' => $this->msg['cms_module_carousel_view_carousel_records_desc'],
				'children' => $this->prefix_var_tree($datas,"records[i]")
			)
		);
		$format_datas = array_merge($format_datas,parent::get_format_data_structure());
		return $format_datas;
	}
}