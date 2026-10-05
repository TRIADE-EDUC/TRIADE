<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_bannette_view_bannette.class.php,v 1.6 2021/11/19 10:22:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/bannette.class.php") ;

class cms_module_bannette_view_bannette extends cms_module_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "
{{info.header}}
<br /><br />
<div class=summary>
<ul>
{% for sommaire in sommaires %}
{% if sommaire.level==1 %}
<li>
<a href=#{{loop.counter}}>
{{sommaire.title}}
</a>
</li>
{% endif %}
{% endfor %}
</ul>
</div>
<hr/>
{% for sommaire in sommaires %}
{% if sommaire.level==1 %}
<h4 id={{loop.counter}}>{{sommaire.title}}</h4>
{% endif %}
{% if sommaire.level==2 %}
<h5>{{sommaire.title}}</h5>
{% endif %}
{% if sommaire.level==3 %}
<h6>{{sommaire.title}}</h6>
{% endif %}
{% for record in sommaire.records %}
{{record.render}}
<hr/>
{% endfor %}
<br />
{% endfor %}
{{info.footer}}
";
	}
	
	public function get_form(){
		if(!isset($this->parameters['used_template'])) $this->parameters['used_template'] = '';
		if(!isset($this->parameters['nb_notices'])) $this->parameters['nb_notices'] = '';
		
		$form = parent::get_form()
				."
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_bannette_view_django_template_record_content'>".$this->format_text($this->msg['cms_module_bannette_view_django_template_record_content'])."</label>
			</div>
			<div class='colonne-suite'>
				".notice_tpl::gen_tpl_select("cms_module_bannette_view_django_template_record_content",$this->parameters['used_template'])."
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_bannette_view_bannette_nb_notices'>".$this->format_text($this->msg['cms_module_bannette_view_bannette_build_nb_notices'])."</label>
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
		
		$this->parameters['nb_notices'] = (int) $cms_module_bannette_view_bannette_nb_notices;
		$this->parameters['used_template'] = $cms_module_bannette_view_django_template_record_content;
		return parent::save_form();
	}
		
	public function render($datas){
		if($datas['id']) {
			$bannette = new bannette($datas['id']);
			$info_header = $bannette->construit_liens_HTML();
			$datas['info']['header'] = $info_header;
			$bannette->notice_tpl = $this->parameters['used_template'];
			$bannette->document_notice_tpl = $this->parameters['used_template'];
			if(!empty($this->parameters['nb_notices'])) {
				$bannette->nb_notices_diff = $this->parameters['nb_notices'];
			}
			$bannette->get_datas_content();
			$datas = array_merge($datas,$bannette->data_document);
		}
		return parent::render($datas);
	}
	
	
	
	public function get_format_data_structure(){
		return array_merge(array(
				array(
					'var' => "name",
					'desc'=> $this->msg['cms_module_bannette_view_bannette_name_desc']
				),
				array(
					'var' => "comment",
					'desc'=> $this->msg['cms_module_bannette_view_bannette_comment_desc']
				),
				array(
						'var' => "info",
						'desc' => $this->msg['cms_module_bannette_view_bannette_info_desc'],
						'children' => array(
								array(
										'var' => "info.header",
										'desc'=> $this->msg['cms_module_bannette_view_bannette_info_header_desc']
								),
								array(
										'var' => "info.footer",
										'desc'=> $this->msg['cms_module_bannette_view_bannette_info_footer_desc']
								)
						)
				),
				array(
						'var' => "sommaires",
						'desc' => $this->msg['cms_module_bannette_view_bannette_sommaires_desc'],
						'children' => array(
								array(
										'var' => "sommaires[i].title",
										'desc'=> $this->msg['cms_module_bannette_view_bannette_sommaire_title_desc']
								),
								array(
										'var' => "sommaires[i].level",
										'desc'=> $this->msg['cms_module_bannette_view_bannette_sommaire_level_desc']
								),
								array(
										'var' => "sommaires[i].records",
										'desc' => $this->msg['cms_module_bannette_view_bannette_records_desc'],
										'children' => array(
												array(
														'var' => "sommaires[i].records[j].id",
														'desc'=> $this->msg['cms_module_bannette_view_bannette_record_id_desc']
												),
												array(
														'var' => "sommaires[i].records[j].title",
														'desc'=> $this->msg['cms_module_bannette_view_bannette_record_title_desc']
												),
												array(
														'var' => "sommaires[i].records[j].link",
														'desc'=> $this->msg['cms_module_bannette_view_bannette_record_link_desc']
												),
												array(
														'var' => "sommaires[i].records[j].url_vign",
														'desc'=> $this->msg['cms_module_bannette_view_bannette_record_url_vign_desc']
												),
												array(
														'var' => "sommaires[i].records[j].render",
														'desc'=> $this->msg['cms_module_bannette_view_bannette_notices_record_render_desc']
												)
										)
								)
						)
				),
				array(
					'var' => "record_number",
					'desc'=> $this->msg['cms_module_bannette_view_bannette_record_number_desc']
				),
				array(
					'var' => "records",		
					'desc' => $this->msg['cms_module_bannette_view_bannette_records_desc'],
					'children' => array(
						array(
							'var' => "records[i].id",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_record_id_desc']
						),
						array(
							'var' => "records[i].title",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_record_title_desc']
						),
						array(
							'var' => "records[i].link",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_record_link_desc']
						),
						array(
							'var' => "records[i].url_vign",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_record_url_vign_desc']
						),
						array(
							'var' => "records[i].render",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_notices_record_render_desc']
						)
					)									
				),
				array(
					'var' => "flux_rss",
					'desc' => $this->msg['cms_module_bannette_view_bannette_flux_rss_desc'],
					'children' => array(
						array(
							'var' => "flux_rss[i].id",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_flux_rss_id_desc']
						),	
						array(
							'var' => "flux_rss[i].name",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_flux_rss_name_desc']
						),	
						array(
							'var' => "flux_rss[i].opac_link",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_flux_rss_opac_link_desc']
						),	
						array(
							'var' => "flux_rss[i].link",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_flux_rss_link_desc']
						),	
						array(
							'var' => "flux_rss[i].lang",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_flux_rss_lang_desc']
						),	
						array(
							'var' => "flux_rss[i].copy",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_flux_rss_copy_desc']
						),	
						array(
							'var' => "flux_rss[i].editor_mail",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_flux_rss_editor_mail_desc']
						),	
						array(
							'var' => "flux_rss[i].webmaster_mail",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_flux_rss_webmaster_mail_desc']
						),	
						array(
							'var' => "flux_rss[i].ttl",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_flux_rss_ttl_desc']
						),	
						array(
							'var' => "flux_rss[i].img_url",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_flux_rss_img_url_desc']
						),	
						array(
							'var' => "flux_rss[i].img_title",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_flux_rss_img_title_desc']
						),	
						array(
							'var' => "flux_rss[i].img_link",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_flux_rss_img_link_desc']
						),	
						array(
							'var' => "flux_rss[i].format",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_flux_rss_format_desc']
						),	
						array(
							'var' => "flux_rss[i].content",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_flux_rss_content_desc']
						),	
						array(
							'var' => "flux_rss[i].date_last",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_flux_rss_date_last_desc']
						),	
						array(
							'var' => "flux_rss[i].export_court",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_flux_rss_export_court_desc']
						),	
						array(
							'var' => "flux_rss[i].template",
							'desc'=> $this->msg['cms_module_bannette_view_bannette_flux_rss_template_desc']
						)															
					)
			)
		),parent::get_format_data_structure());
		
	}
}