<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_recordslist.class.php,v 1.21 2023/06/07 10:27:46 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Thumbnail\Models\ThumbnailSourcesHandler;

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
		global $include_path;
		global $opac_notices_format, $opac_notices_format_django_directory;
		global $record_css_already_included; // Pour pas inclure la css 10 fois
	
		if(empty($opac_notice_affichage_class)){
			$opac_notice_affichage_class ="notice_affichage";
		}
		
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $datas["title"] ?? "";
		$render_datas['source_infos'] = isset($datas["source_infos"]) ? $datas["source_infos"] : "";
		
		// Données de la pagination
		if(isset($datas['paging']) && $datas['paging']['activate']) {
		    $render_datas['paging'] = $datas['paging'];
		}
		
		$render_datas['records'] = array();
		$add_to_cart_link = '';
		if (isset($datas["records"]) && is_array($datas["records"])) {
		    $records = isset($datas["records"]) ? $datas["records"] : $datas;
		    $thumbnailSourcesHandler = new ThumbnailSourcesHandler();
		    foreach($records as $notice){
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
				$url_vign = $thumbnailSourcesHandler->generateUrl(TYPE_NOTICE, $notice_id);
				$infos = array(
					'id' => $notice_id,
					'title' => $notice_class->notice->tit1,
					'vign' => $url_vign,
					'header' => $notice_class->notice_header,
					'link' => $this->get_constructed_link("notice",$notice_id,$is_bulletin),
				    'parent' => []
				);
				
				if (!empty($notice_class->parent_id)) {
				    $url_parent_vign = "";
				    $notice_parent_class = new $opac_notice_affichage_class($notice_class->parent_id);
				    
			        $parent_notice_id = $notice_parent_class->notice_id;
			        $is_parent_bulletin = false;
				    if ($notice_parent_class->notice->niveau_biblio == 'b') {
				        $parent_notice_id = $notice_parent_class->bulletin_id;
				        $is_parent_bulletin = true;
				    }
				    $url_parent_vign = $thumbnailSourcesHandler->generateUrl(TYPE_NOTICE, $parent_notice_id);
				    $infos['parent'] = [
				        'id' => $parent_notice_id,
				        'title' => $notice_parent_class->notice->tit1,
				        'vign' => $url_parent_vign,
				        'header' => $notice_parent_class->notice_header,
				        'link' => $this->get_constructed_link('notice', $notice_parent_class->notice_id, $is_parent_bulletin)
				    ];
				}
				
				if($this->parameters['used_template']){
					$tpl = notice_tpl_gen::get_instance($this->parameters['used_template']);
					$infos['content'] = $tpl->build_notice($notice);
				}else{
				    if(!isset($infos['content'])) {
				        $infos['content'] = "";
				    }
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
		$format[] = array(
			'var' => "source_infos",
			'desc' => $this->msg['cms_module_common_view_source_infos_desc']
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
				),
			    array(
					'var' => "records[i].parent",
					'desc'=> $this->msg['cms_module_common_view_record_parent_desc'],
			        'children' => array(
			            array(
			                'var' => "parent.id",
			                'desc'=> $this->msg['cms_module_common_view_record_id_desc']
			            ),
			            array(
			                'var' => "parent.title",
			                'desc'=> $this->msg['cms_module_common_view_record_title_desc']
			            ),
			            array(
			                'var' => "parent.vign",
			                'desc'=> $this->msg['cms_module_common_view_record_vign_desc']
			            ),
			            array(
			                'var' => "parent.header",
			                'desc'=> $this->msg['cms_module_common_view_record_header_desc']
			            ),
			            array(
			                'var' => "parent.content",
			                'desc'=> $this->msg['cms_module_common_view_record_content_desc']
			            ),
			            array(
			                'var' => "parent.link",
			                'desc'=> $this->msg['cms_module_common_view_record_link_desc']
			            )
			        )
			    )
			)
		);
		$format[] = array(
			'var' => "add_to_cart_link",
			'desc' => $this->msg['cms_module_recordslist_view_add_cart_link_desc']
		);
		$format[] = array(
		    'var' => "paginator",
		    'desc' => $this->msg['cms_module_common_view_list_paging_title'],
		    'children' => array(
		        array(
		            'var' => "paginator.paginator",
		            'desc' => $this->msg['cms_module_common_view_list_paging_paginator_title']
		        ),
		        array(
		            'var' => "paginator.nbPerPageSelector",
		            'desc' => $this->msg['cms_module_common_view_list_paging_nb_per_page_title']
		        ),
		        array(
		            'var' => "paginator.navigator",
		            'desc' => $this->msg['cms_module_common_view_list_paging_navigator_title']
		        )
		    )
		);
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}