<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_contributionareaslist_view_django.class.php,v 1.2 2021/03/23 09:26:18 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_contributionareaslist_view_django extends cms_module_common_view_django{
	
    public function __construct($id=0){
        parent::__construct($id);
        $this->default_template = "
<div>
    {% for item in items%}
        {{ item.title }}<br />
    {% endfor%}
</div>";
    }
	
	public function get_form(){
		$form="";
		$form.= parent::get_form();
		return $form;
	}
	
	public function save_form(){
		return parent::save_form();
	}
	
	public function render($datas){
		return parent::render($datas);
	}
	
	public function get_format_data_structure(){
	    $datasource = new cms_module_contributionareaslist_datasource_areas();
		$datas = $datasource->get_format_data_structure();
		
		$format_datas = array_merge($datas,parent::get_format_data_structure());
		return $format_datas;
	}
}