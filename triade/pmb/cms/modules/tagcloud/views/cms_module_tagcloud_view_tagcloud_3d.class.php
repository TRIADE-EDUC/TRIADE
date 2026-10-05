<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_tagcloud_view_tagcloud_3d.class.php,v 1.3 2017/07/12 15:15:02 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_tagcloud_view_tagcloud_3d extends cms_module_common_view{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->use_jquery = true;
	}

	public function get_headers($datas=array()){
		$headers = parent::get_headers($datas);
		$headers[]= "<script type='text/javascript' src='".$this->get_module_folder()."includes/javascript/jquery.tagSphere.js'></script>";
		return $headers;
	}
	
	public function get_form(){
		$form=parent::get_form();
		$form.= "
		<div class='row'>
			<div class='colonne3'>
				<label for='".$this->get_form_value_name("height")."'>".$this->format_text($this->msg['cms_module_tagcloud_view_tagcloud_3d_height'])."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' name='".$this->get_form_value_name("height")."' value='".$this->format_text($this->parameters['height'])."'/>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='".$this->get_form_value_name("width")."'>".$this->format_text($this->msg['cms_module_tagcloud_view_tagcloud_3d_width'])."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' name='".$this->get_form_value_name("width")."' value='".$this->format_text($this->parameters['width'])."'/>
			</div>
		</div>";
		return $form;
	}
	

	public function save_form(){
		$this->parameters['height'] = $this->get_value_from_form("height");
		$this->parameters['width'] = $this->get_value_from_form("width");
		return parent::save_form();
	}
	
	public function render($datas){
		$html_to_display = "
		<div id='tagsphere' style='width:".$this->parameters['width'].";height:".$this->parameters['height'].";overflow: hidden; position: relative;'>
 			<ul>";
		foreach($datas as $tag){
			$html_to_display.= "
				<li><a href='".${$tag['link']}."'>".$this->format_text($tag['label'])."</a></li>";
		}
		$html_to_display.= "
			</ul>
		</div>
		<script>
			$(document).ready(function(){
    			$('#tagsphere').tagSphere({
        			height: ".$this->parameters['height'].",
        			width: ".$this->parameters['width'].",
        			slower: 0.8,
        			radius: ".(min(array($this->parameters['height'],$this->parameters['width']))/3).",
        			timer: 50,
        			fontMultiplier: 20
    			});
			});
		</script>

				";
		return $html_to_display;
	}
}