<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_bannetteslist_view_django_directory_from_tpl.class.php,v 1.1 2019/07/25 13:58:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_bannetteslist_view_django_directory_from_tpl extends cms_module_common_view_bannetteslist{

    public function __construct($id=0){
        parent::__construct($id);
        $this->default_template =
        "<div>
	{% for bannette in bannettes %}
		<h3>{{bannette.name}}</h3>
		{% for flux_rss in bannette.flux_rss %}
			<a href='{{flux_rss.link}}'>{{flux_rss.name}}</a>
		{% endfor %}
		{{bannette.content}}
	{% endfor %}
</div>
";
    }
    
	protected function get_record_template_form() {
		if(!isset($this->parameters['django_directory'])) $this->parameters['django_directory'] = '';
		if(!isset($this->parameters['template_bannette_content'])) $this->parameters['template_bannette_content'] = '';
		$form = "
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_bannetteslist_view_django_directory'>".$this->format_text($this->msg['cms_module_bannetteslist_view_django_directory'])."</label>
			</div>
			<div class='colonne-suite'>
				<select name='cms_module_bannetteslist_view_django_directory'>";
		$form.= $this->get_directories_options($this->parameters['django_directory']);
		$form.= "
				</select>
			</div>
		</div>
        <div class='row'>
			<div class='colonne3'>
				<label for='cms_module_bannetteslist_view_django_template_bannette_content'>".$this->format_text($this->msg['cms_module_bannetteslist_view_django_template_bannette_content'])."</label>
			</div>
			<div class='colonne-suite'>
                ".bannette_tpl::gen_tpl_select("cms_module_bannetteslist_view_django_template_bannette_content",$this->parameters['template_bannette_content'], "", 1)."
			</div>
		</div>";
		return $form;
	}
	
	public function save_form(){
		global $cms_module_bannetteslist_view_django_directory;
		global $cms_module_bannetteslist_view_django_template_bannette_content;
	
		$this->parameters['django_directory'] = $cms_module_bannetteslist_view_django_directory;
		$this->parameters['template_bannette_content'] = intval($cms_module_bannetteslist_view_django_template_bannette_content);
		return parent::save_form();
	}
	
	public function render($datas){
	    //on gère l'affichage des banettes
	    foreach($datas["bannettes"] as $i => $data) {
	        $bannette = new bannette($data['id']);
	        $info_header = $bannette->construit_liens_HTML();
	        $datas["bannettes"][$i]['info']['header'] = $info_header;
	        $bannette->notice_tpl = 0;
	        $bannette->document_notice_tpl = 0;
	        $bannette->django_directory = $this->parameters['django_directory'];
	        $bannette->bannette_tpl_num = $this->parameters['template_bannette_content'];
	        if(!empty($this->parameters['nb_notices'])) {
	            $bannette->nb_notices_diff = $this->parameters['nb_notices'];
	        }
	        $bannette->get_datas_content();
	        $datas["bannettes"][$i] = array_merge($datas["bannettes"][$i],$bannette->data_document);
	        $datas["bannettes"][$i]["content"] = bannette_tpl::render($bannette->bannette_tpl_num,$datas["bannettes"][$i]);
	        
	    }
	    $this->render_already_generated = true;
	    //on rappelle le tout...
	    return parent::render($datas);
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