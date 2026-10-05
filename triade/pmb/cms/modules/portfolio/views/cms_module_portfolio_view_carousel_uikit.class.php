<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_portfolio_view_carousel_uikit.class.php,v 1.3 2021/02/12 10:48:04 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_portfolio_view_carousel_uikit extends cms_module_common_view_carousel_uikit {
    
    public function __construct($id=0){
        parent::__construct($id);
        $this->default_template = "
{% if documents %}
<div id='carousel_{{id}}' data-uk-slider>
    <div class='uk-slider-container'>
        <ul class='uk-slider'>
            {% for document in documents %}
                <li>
                    <a target='_blank' href='{{document.url}}' title='{% if document.name %}{{document.name}}{% else %}{{document.filename}}{% endif %}'>
                        <img src='{{ document.thumbnails_url }}' alt='{% if document.name %}{{document.name}}{% else %}{{document.filename}}{% endif %}'>
                    </a>
                </li>
            {% endfor %}
	    </ul>
    </div>
</div>
{% endif %}
";
    }
    
    public function get_form(){
        $form = parent::get_form();
        return $form;
    }
    
    public function save_form(){
        return parent::save_form();
    }
    
    public function get_view_django_template_record ()
    {
        // Champs template de notice à utiliser pour le content
        return "";
    }
    
    public function render($datas) {
        global $base_path;
        
        $html2return = '';
        if (!empty($datas['documents'])) {
            $datas['id'] = $this->get_module_dom_id();
            $id = "carousel_".$datas['id'];
            $datas['no_image_url'] = $this->get_no_image_url();
            $template_path = $base_path.'/temp/'.LOCATION.'_cms_carousel_uikit_view_'.$this->id;
            if(!file_exists($template_path) || (md5($this->parameters['active_template']) != md5_file($template_path))){
                file_put_contents($template_path, $this->parameters['active_template']);
            }
            $H2o = H2o_collection::get_instance($template_path);
            $html2return = $H2o->render($datas);
            $html2return .= $this->get_script_slider($id);
        }
        
        return $html2return;
    }
    
    public function get_format_data_structure(){
        $datasource = new cms_module_common_datasource_portfolio();
        $datas = $datasource->get_format_data_structure();
        
        $format_datas = array_merge($datas,parent::get_format_data_structure());
        return $format_datas;
    }
}