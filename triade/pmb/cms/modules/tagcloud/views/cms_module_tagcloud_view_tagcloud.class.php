<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_tagcloud_view_tagcloud.class.php,v 1.6 2021/02/02 10:21:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_tagcloud_view_tagcloud extends cms_module_common_view{
	
	
	public function __construct($id=0){
		parent::__construct($id);
	}

	public function get_form(){
		$form = parent::get_form();
		$form.="
		<div class='row'>
			<div class='colonne3'>
				<label for='".$this->get_form_value_name("nb_weight")."'>".$this->format_text($this->msg['cms_module_tagcloud_view_tagcloud_nb_weight'])."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' name='".$this->get_form_value_name("nb_weight")."' onchange='load_form_".$this->get_form_value_name("weight_styles")."(this.value)' value='".$this->format_text($this->parameters['nb_weight'])."'/>
			</div>
		</div>
		<script type='text/javascript'>
			function load_form_".$this->get_form_value_name("weight_styles")."(nb_weight){
				dojo.xhrGet({
					url : '".$this->get_ajax_link(array($this->class_name."_hash[]" => $this->hash))."&nb_weight='+nb_weight,
					handelAs : 'text/html',
					load : function(data){
						dojo.byId('".$this->get_form_value_name("weight_styles")."_values').innerHTML = data;
					}
				});	
			}
		</script>
		<div id='".$this->get_form_value_name("weight_styles")."_values'>";
		if($this->parameters['nb_weight']){
			$form.= "
			<script type='text/javascript'>
				load_form_".$this->get_form_value_name("weight_styles")."(".$this->parameters['nb_weight'].");
			</script>";
		}
		$form.="
		</div>";
		return $form;
	}
	
	public function get_headers($datas=array()){
		$headers = parent::get_headers($datas);
		$headers[] = "<link rel='stylesheet' type='text/css' href='".$this->get_ajax_link(array('do'=>"generate_css"), 'css')."'/>";
		return $headers;
	}
	
	
	public function save_form(){
		$this->parameters['nb_weight'] = $this->get_value_from_form("nb_weight");
		for ($i=0 ; $i<$this->parameters['nb_weight'] ; $i++){
			$this->parameters['weight'][$i]['size'] = $this->get_value_from_form("size_".$i);
			$this->parameters['weight'][$i]['color'] = $this->get_value_from_form("color_".$i);
		}
		return parent::save_form();
	}
	
	public function render($datas){
		$min = $max = 0;
		for($i=0 ; $i<count($datas) ; $i++){
			if($datas[$i]['weight'] < $min){
				$min = $datas[$i]['weight'];
			}
			if($datas[$i]['weight'] > $max){
				$max = $datas[$i]['weight'];
			}
		}
		for($i=0 ; $i<count($datas) ; $i++){
			$datas[$i]['weight'] = round((($datas[$i]['weight']-$min)/($max-$min))*$this->parameters['nb_weight']);
		}
		
		$html_to_display = "
		<div>
			<ul>";
		foreach($datas as $tag){
			$html_to_display.="
				<li class='tag_".$tag['weight']."'><a href='".$tag['link']."'>".$this->format_text($tag['label'])."</a></li>";
		}
		$html_to_display.= "
			</ul>
		</div>";
		return $html_to_display;
	}
	
	public function execute_ajax(){
		global $nb_weight;
		global $do;
		switch($do){
			case "generate_css" :
				$response['content-type'] = "text/css";
				$response['content'].= "
				#".$this->get_module_dom_id()." li,#".$this->get_module_dom_id()." li a  {
					display: inline-block;
					margin: 0px;
					margin-left: 0px;
					margin-right: 0px;
					padding: 2px;
				}";
				for($i=0; $i<$this->parameters['nb_weight'] ; $i++ ){
					$response['content'].= "
					#".$this->get_module_dom_id()." li.tag_".($i+1).", #".$this->get_module_dom_id()." li.tag_".($i+1)." a {
						".($this->parameters['weight'][$i]['color'] ? "color: ".$this->parameters['weight'][$i]['color'].";" : "")."
						".($this->parameters['weight'][$i]['size'] ? "font-size: ".$this->parameters['weight'][$i]['size'].";" : "")."
					}";
				}
				break;
			default :
				$response['content-type'] = "text/html";
				for ($i=0 ; $i<$nb_weight ; $i++){
					$response['content'].="
				<div class='row'>
					<div class='colonne3'>
						<label for='".$this->get_form_value_name("weight_".$i)."'>".$this->format_text(sprintf($this->msg['cms_module_tagcloud_view_tagcloud_weight_style'],$i+1)) ."</label>
					</div>
					<div class='colonne3'>
						color : <input type='text' name='".$this->get_form_value_name("color_".$i)."' value='".$this->format_text($this->parameters['weight'][$i]['color'])."'/>
					</div>
					<div class='colonne_suite'>
						size : <input type='text' name='".$this->get_form_value_name("size_".$i)."' value='".$this->format_text($this->parameters['weight'][$i]['size'])."'/>
					</div>
				</div>";
				}
				break;
		}
		return $response;
	}
}