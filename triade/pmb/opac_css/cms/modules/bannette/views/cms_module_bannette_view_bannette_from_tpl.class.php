<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_bannette_view_bannette_from_tpl.class.php,v 1.5 2022/02/18 08:53:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/bannette.class.php") ;

class cms_module_bannette_view_bannette_from_tpl extends cms_module_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template =
"<div>
<h3>{{comment}}</h3>
{% for flux in flux_rss %}
	<a href='{{flux.link}}'>{{flux.name}}</a>
{% endfor %}
{{content}}
</div>
";

	}
	
	public function get_form(){
		if(!isset($this->parameters['used_bannette_template'])) $this->parameters['used_bannette_template'] = '';
		if(!isset($this->parameters['used_record_template'])) $this->parameters['used_record_template'] = '';
		if(!isset($this->parameters['nb_notices'])) $this->parameters['nb_notices'] = '';
		
		$form = parent::get_form()
				."
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_bannette_view_django_template_bannette_content'>".$this->format_text($this->msg['cms_module_bannette_view_django_template_bannette_content'])."</label>
			</div>
			<div class='colonne-suite'>
				".bannette_tpl::gen_tpl_select("cms_module_bannette_view_django_template_bannette_content",$this->parameters['used_bannette_template'], "", 1)."
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_bannette_view_django_template_record_content'>".$this->format_text($this->msg['cms_module_bannette_view_django_template_record_content'])."</label>
			</div>
			<div class='colonne-suite'>
				".notice_tpl::gen_tpl_select("cms_module_bannette_view_django_template_record_content",$this->parameters['used_record_template'])."
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_common_bannette_view_nb_notices'>".$this->format_text($this->msg['cms_module_bannette_view_bannette_build_nb_notices'])."</label>
			</div>
			<div class='colonne_suite'>
				<input type='number' name='cms_module_bannette_view_bannette_nb_notices' value='".$this->parameters["nb_notices"]."'/>
			</div>
		</div>";
		return $form;
	}
	
	public function save_form(){
		global $cms_module_bannette_view_bannette_nb_notices;
		global $cms_module_bannette_view_django_template_record_content;
		global $cms_module_bannette_view_django_template_bannette_content;
		
		$this->parameters['nb_notices'] = (int) $cms_module_bannette_view_bannette_nb_notices;
		$this->parameters['used_record_template'] = $cms_module_bannette_view_django_template_record_content;
		$this->parameters['used_bannette_template'] = $cms_module_bannette_view_django_template_bannette_content;
		return parent::save_form();
	}
		
	public function render($datas){
		if($datas['id']) {
			$bannette = new bannette($datas['id']);
			$info_header = $bannette->construit_liens_HTML();
			$datas['info']['header'] = $info_header;
			$bannette->notice_tpl = $this->parameters['used_record_template'];
			$bannette->document_notice_tpl = $this->parameters['used_record_template'];
			$bannette->bannette_tpl_num = $this->parameters['used_bannette_template'];
			if(!empty($this->parameters['nb_notices'])) {
				$bannette->nb_notices_diff = $this->parameters['nb_notices'];
			}
			$bannette->get_datas_content();
			$datas = array_merge($datas,$bannette->data_document);
			$datas["content"] = bannette_tpl::render($bannette->bannette_tpl_num,$datas);
		}
		return parent::render($datas);
	}
	
	public function get_format_data_structure(){
		return array_merge(array(
				array(
					'var' => "name",
					'desc'=> $this->msg['cms_module_bannette_view_bannette_from_tpl_name_desc']
				),
				array(
					'var' => "comment",
					'desc'=> $this->msg['cms_module_bannette_view_bannette_from_tpl_comment_desc']
				),
				array(
						'var' => "content",
						'desc'=> $this->msg['cms_module_bannette_view_bannette_from_tpl_content_desc']
				),
				array(
					'var' => "record_number",
					'desc'=> $this->msg['cms_module_bannette_view_bannette_from_tpl_record_number_desc']
				),
				array(
					'var' => "flux_rss",
					'desc' => $this->msg['cms_module_bannette_view_bannette_from_tpl_flux_rss_desc'],
					'children' => array(
						array(
							'var' => "flux_rss[i].id",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_from_tpl_flux_rss_id_desc']
						),	
						array(
							'var' => "flux_rss[i].name",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_from_tpl_flux_rss_name_desc']
						),	
						array(
							'var' => "flux_rss[i].opac_link",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_from_tpl_flux_rss_opac_link_desc']
						),	
						array(
							'var' => "flux_rss[i].link",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_from_tpl_flux_rss_link_desc']
						),	
						array(
							'var' => "flux_rss[i].lang",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_from_tpl_flux_rss_lang_desc']
						),	
						array(
							'var' => "flux_rss[i].copy",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_from_tpl_flux_rss_copy_desc']
						),	
						array(
							'var' => "flux_rss[i].editor_mail",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_from_tpl_flux_rss_editor_mail_desc']
						),	
						array(
							'var' => "flux_rss[i].webmaster_mail",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_from_tpl_flux_rss_webmaster_mail_desc']
						),	
						array(
							'var' => "flux_rss[i].ttl",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_from_tpl_flux_rss_ttl_desc']
						),	
						array(
							'var' => "flux_rss[i].img_url",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_from_tpl_flux_rss_img_url_desc']
						),	
						array(
							'var' => "flux_rss[i].img_title",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_from_tpl_flux_rss_img_title_desc']
						),	
						array(
							'var' => "flux_rss[i].img_link",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_from_tpl_flux_rss_img_link_desc']
						),	
						array(
							'var' => "flux_rss[i].format",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_from_tpl_flux_rss_format_desc']
						),	
						array(
							'var' => "flux_rss[i].content",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_from_tpl_flux_rss_content_desc']
						),	
						array(
							'var' => "flux_rss[i].date_last",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_from_tpl_flux_rss_date_last_desc']
						),	
						array(
							'var' => "flux_rss[i].export_court",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_from_tpl_flux_rss_export_court_desc']
						),	
						array(
							'var' => "flux_rss[i].template",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_from_tpl_flux_rss_template_desc']
						)															
					)
				)									
		),parent::get_format_data_structure());
		
		
	}
}