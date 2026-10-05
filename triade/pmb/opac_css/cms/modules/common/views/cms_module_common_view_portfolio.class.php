<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_portfolio.class.php,v 1.10.2.2 2025/01/17 10:40:43 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_view_portfolio extends cms_module_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div class='row document_list'>
 {% for document in documents %}
  <div class='document_item'>
   &nbsp;
   <div class='document_item_content'>
    <a target='_blank' href='{{document.url}}' title='{% if document.name %}{{document.name}}{% else %}{{document.filename}}{% endif %}'>
     <img src='{{document.thumbnails_url}}' alt=''/>
     <br />
     <p>{% if document.name %}{{document.name|limitstring 30 \"[...]\"}}{% else %}{{document.filename|limitstring 30 \"[...]\"}}{% endif %}
     <br />
     <span style='font-size:.8em;'>{{document.mimetype}}{%if document.filesize.human %} - ({{document.filesize.human}}){% endif %}</span></p>
    </a>
   </div>
   &nbsp;
  </div>
 {% endfor %}
</div>
<div class='row'></div>";
	}
	
	
	public function get_form(){
		$form = "
		<div class='row'>
			<div class='colonne3'>
				<label for='".$this->get_form_value_name("visionneuse")."' >".$this->format_text($this->msg['cms_module_common_view_portfolio_visionneuse'])."</label>
			</div>
			<div class'colonne_suite'>
				<input type='radio' name='".$this->get_form_value_name("visionneuse")."' value='1'".($this->parameters['visionneuse'] ? " checked='checked' " : "")."/>&nbsp;".$this->format_text($this->msg['yes'])."
				<input type='radio' name='".$this->get_form_value_name("visionneuse")."' value='0'".($this->parameters['visionneuse'] ? "" : " checked='checked' ")."/>&nbsp;".$this->format_text($this->msg['no'])."
			</div>
		</div>
		<div class='row'>&nbsp;</div>";
		$form.= parent::get_form();
		return $form;
	}
	
	
	public function save_form(){
		$this->parameters['visionneuse'] = $this->get_value_from_form("visionneuse");
		$this->parameters['visionneuse'] = 0;
		return parent::save_form();
	}
	
	
	public function get_headers($datas=array()){
		
		$headers = array();
		$headers[] = "<script src='visionneuse/javascript/visionneuse.js'></script>";
		return $headers;
	}
	
	
	public function render($datas){
		$render =  parent::render($datas);
		
		if($this->parameters['visionneuse'] && is_countable($datas['documents'])){
			$documents = array_reverse($datas['documents']);
			for($i=0 ; $i<count($documents) ; $i++){
				$str_to_replace = substr($render,strpos($render,$documents[$i]['url'])-1,strlen($documents[$i]['url'])+2);
				if(trim($str_to_replace)){
					$render = str_replace($str_to_replace, "'#' onclick='open_visionneuse(open_cms_visionneuse_".$this->id.",".$documents[$i]['id'].",".$i.");return false;'", $render);
				}
			}
			$render.= "
			<script>
				function open_cms_visionneuse_".$this->id."(id, position){
					var url = 'visionneuse.php?driver=pmb_document&lvl=visionneuse&cms_type=".$datas['type_object']."&num_type=".$datas['num_object']."';
					if(id){
						url+='&explnum='+id;
					}

					if(position >= 0) {
						url+='&position='+position;
					}

					document.getElementById('visionneuseIframe').src = url;
				}
			</script>";
		}
		return $render;
	}

	
	public function get_format_data_structure(){
		$datasource = new cms_module_common_datasource_portfolio();
		$format = $datasource->get_format_data_structure();
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
		return array_merge($format,parent::get_format_data_structure());
	}
}
