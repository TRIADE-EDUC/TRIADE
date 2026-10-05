<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_bannetteslist_view_django_from_tpl.class.php,v 1.1 2019/07/25 13:59:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_bannetteslist_view_django_from_tpl extends cms_module_common_view_bannetteslist{

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
		if(!isset($this->parameters['template_bannette_content'])) $this->parameters['template_bannette_content'] = '';
		if(!isset($this->parameters['template_record_content'])) $this->parameters['template_record_content'] = '';
		$form = "
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_bannetteslist_view_django_template_bannette_content'>".$this->format_text($this->msg['cms_module_bannetteslist_view_django_template_bannette_content'])."</label>
			</div>
			<div class='colonne-suite'>
                ".bannette_tpl::gen_tpl_select("cms_module_bannetteslist_view_django_template_bannette_content",$this->parameters['template_bannette_content'], "", 1)."
			</div>
		</div>
        <div class='row'>
			<div class='colonne3'>
				<label for='cms_module_bannetteslist_view_django_template_record_content'>".$this->format_text($this->msg['cms_module_bannetteslist_view_django_template_record_content'])."</label>
			</div>
			<div class='colonne-suite'>
                ".notice_tpl::gen_tpl_select("cms_module_bannetteslist_view_django_template_record_content",$this->parameters['template_record_content'])."
			</div>
		</div>";
		return $form;
	}
	
	public function save_form(){
	    global $cms_module_bannetteslist_view_django_template_bannette_content;
	    global $cms_module_bannetteslist_view_django_template_record_content;
	
	    $this->parameters['template_bannette_content'] = intval($cms_module_bannetteslist_view_django_template_bannette_content);
	    $this->parameters['template_record_content'] = intval($cms_module_bannetteslist_view_django_template_record_content);
	    return parent::save_form();
	}
	
	public function render($datas){
		//on gère l'affichage des banettes				
		foreach($datas["bannettes"] as $i => $data) {
		    $bannette = new bannette($data['id']);
		    $info_header = $bannette->construit_liens_HTML();
		    $datas["bannettes"][$i]['info']['header'] = $info_header;
		    $bannette->notice_tpl = $this->parameters['template_record_content'];
		    $bannette->document_notice_tpl = $this->parameters['template_record_content'];
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
}