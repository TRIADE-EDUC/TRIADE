<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_record.class.php,v 1.9 2023/04/28 07:53:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_view_record extends cms_module_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		
		$this->default_template = "
<p>{{record.header}}</p>
<blockquote>{{record.content}}</blockquote>
";
	}
	
	public function get_form(){
		if(!isset($this->parameters['used_template'])) $this->parameters['used_template'] = '';
		$form = parent::get_form();
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_record_used_template'>".$this->format_text($this->msg['cms_module_common_view_record_used_template'])."</label>
				</div>
				<div class='colonne-suite'>";
		
		$form.= notice_tpl::gen_tpl_select("cms_module_common_view_record_used_template",$this->parameters['used_template']);
		$form.="				
				</div>
			</div>
		";
		return $form;
	}
	
	public function save_form(){
		global $cms_module_common_view_record_used_template;
		
		$this->parameters['used_template'] = $cms_module_common_view_record_used_template;
		return parent::save_form();
	}
	
	public function render($datas){
		global $opac_notice_affichage_class;
		global $opac_url_base;
		global $include_path;
		global $opac_notices_format, $opac_notices_format_django_directory;
		global $record_css_already_included; // Pour pas inclure la css 10 fois
	
		if(empty($opac_notice_affichage_class)){
			$opac_notice_affichage_class ="notice_affichage";
		}
		// $datas => id de la notice
		$notice=$datas;
		$render_datas = array();
		$render_datas['record'] = array();
		if($notice){
			//on calcule le template de notices...
			$notice_class = new $opac_notice_affichage_class($notice);
			$notice_class->do_header();
			if($notice_class->notice->niveau_biblio != "b"){
				$permalink = $opac_url_base."index.php?lvl=notice_display&id=".$notice_class->notice_id;
			}else {
				$permalink = $opac_url_base."index.php?lvl=bulletin_display&id=".$notice_class->bulletin_id;
			}
			
			$infos = array(
				'header' => $notice_class->notice_header,
				'link' => $permalink
			);
			if($this->parameters['used_template']){
				$tpl = notice_tpl_gen::get_instance($this->parameters['used_template']);
				$infos['content'] = $tpl->build_notice($notice);
			}else{
				if($opac_notices_format == AFF_ETA_NOTICES_TEMPLATE_DJANGO){						
					if (!$opac_notices_format_django_directory) $opac_notices_format_django_directory = "common";						
					if (!$record_css_already_included) {
						if (file_exists($include_path."/templates/record/".$opac_notices_format_django_directory."/styles/style.css")) {
							$infos['content'] .= "<link type='text/css' href='./includes/templates/record/".$opac_notices_format_django_directory."/styles/style.css' rel='stylesheet'></link>";
						}
						$record_css_already_included = true;
					}
					$infos['content'] .= record_display::get_display_extended($notice_class->notice_id);
				}else {
					$notice_class->do_isbd();
					$infos['content'] = $notice_class->notice_isbd;
				}
			}
			$render_datas['record']=$infos;
		}
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){
		return array_merge(array(
			array(
				'var' => "record",
				'desc'=> "",
				'children' => array(
					array(
						'var' => "record.header",
						'desc'=> $this->msg['cms_module_common_view_record_header_desc']
					),	
					array(
						'var' => "record.content",
						'desc'=> $this->msg['cms_module_common_view_record_content_desc']
					),	
					array(
						'var' => "record.link",
						'desc'=> $this->msg['cms_module_common_view_record_link_desc']
					)
				)
			)
		),parent::get_format_data_structure());
	}
}