<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_recordslist.class.php,v 1.15 2017-07-26 07:57:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_view_recordslist extends cms_module_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		
		$this->default_template = "<h3>{{title}}</h3>
{% for record in records %}
<p>{{record.header}}</p>
<blockquote>{{record.content}}</blockquote>
{% endfor %}";
	}
	
	public function get_form(){
		if(!isset($this->parameters['used_template'])) $this->parameters['used_template'] = '';
		$form="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_recordslist_view_link'>".$this->format_text($this->msg['cms_module_recordslist_view_link'])."</label>
				</div>
				<div class='colonne-suite'>";
		$form.= $this->get_constructor_link_form("notice");
		$form.="
				</div>
			</div>";
		$form.= parent::get_form();
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_recordslist_used_template'>".$this->format_text($this->msg['cms_module_common_view_recordslist_used_template'])."</label>
				</div>
				<div class='colonne-suite'>";
		
		$form.= notice_tpl::gen_tpl_select("cms_module_common_view_recordslist_used_template",$this->parameters['used_template']);
		$form.="				
				</div>
			</div>
		";
		return $form;
	}
	
	public function save_form(){
		global $cms_module_common_view_recordslist_used_template;
		
		$this->save_constructor_link_form("notice");
		$this->parameters['used_template'] = $cms_module_common_view_recordslist_used_template;
		return parent::save_form();
	}
	
	public function render($datas){
		global $opac_notice_affichage_class;
		global $opac_show_book_pics;
		global $opac_book_pics_url;
		global $opac_book_pics_msg;
		global $opac_url_base;
		global $include_path;
		global $opac_notices_format, $opac_notices_format_django_directory;
		global $record_css_already_included; // Pour pas inclure la css 10 fois
	
		if(!$opac_notice_affichage_class){
			$opac_notice_affichage_class ="notice_affichage";
		}

		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $datas["title"];
		$render_datas['records'] = array();
		$add_to_cart_link = '';
		if(is_array($datas["records"])){
			foreach($datas["records"] as $notice){
				//on calcule les templates pour chaque notices...
				$notice_class = new $opac_notice_affichage_class($notice);
				$notice_class->do_header();
				if($notice_class->notice->niveau_biblio != "b"){
					$notice_id = $notice_class->notice_id;
					$is_bulletin = false;
				}else {
					$notice_id = $notice_class->bulletin_id;
					$is_bulletin = true;
				}
				$url_vign = "";
				if (($row->thumbnail_url || $row->code) && ($opac_show_book_pics=='1' && ($opac_book_pics_url || $row->thumbnail_url))) {
					$url_vign = getimage_url($row->code, $row->thumbnail_url);
				}
				$infos = array(
					'id' => $notice_id,
					'title' => $notice_class->notice->tit1,
					'vign' => $url_vign,
					'header' => $notice_class->notice_header,
					'link' => $this->get_constructed_link("notice",$notice_id,$is_bulletin),
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
				$render_datas['records'][]=$infos;
			}
			$add_to_cart_link = '<span class="addCart">
							<a title="'.$this->msg['cms_module_recordslist_view_add_cart_link'].'" target="cart_info" href="cart_info.php?notices='.implode(",",$datas['records']).'">'.$this->msg['cms_module_recordslist_view_add_cart_link'].'</a>
						  </span>';
		}
		$render_datas['add_to_cart_link'] = $add_to_cart_link;
		
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$format[] = array(
			'var' => "title",
			'desc' => $this->msg['cms_module_common_view_title']
		);
		$format[] =	array(
			'var' => "records",
			'desc' => $this->msg['cms_module_commom_view_records_desc'],
			'children' => array(
				array(
					'var' => "records[i].id",
					'desc'=> $this->msg['cms_module_common_view_record_id_desc']
				),
				array(
					'var' => "records[i].title",
					'desc'=> $this->msg['cms_module_common_view_record_title_desc']
				),
				array(
					'var' => "records[i].vign",
					'desc'=> $this->msg['cms_module_common_view_record_vign_desc']
				),
				array(
					'var' => "records[i].header",
					'desc'=> $this->msg['cms_module_common_view_record_header_desc']
				),	
				array(
					'var' => "records[i].content",
					'desc'=> $this->msg['cms_module_common_view_record_content_desc']
				),	
				array(
					'var' => "records[i].link",
					'desc'=> $this->msg['cms_module_common_view_record_link_desc']
				)
			)
		);
		$format[] = array(
			'var' => "add_to_cart_link",
			'desc' => $this->msg['cms_module_recordslist_view_add_cart_link_desc']
		);
		
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}