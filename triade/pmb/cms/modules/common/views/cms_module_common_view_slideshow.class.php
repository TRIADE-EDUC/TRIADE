<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_slideshow.class.php,v 1.5.8.1 2025/01/17 10:40:45 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($include_path."/h2o/h2o.php");

class cms_module_common_view_slideshow extends cms_module_common_view_django {
	
	public function __construct($id = 0) {
		$this->use_jquery = true;
		$this->default_template = "
<div id='carousel_{{id}}' data-uk-slideshow>
    <ul class='uk-slideshow'>
    	{% for record in records %}
    		<li>{{ record.content }}</li>
    	{% endfor %}
    </ul>
</div>
";
		parent::__construct($id);
	}
	
	public function get_form(){
	    if (!isset($this->parameters["used_template"])) $this->parameters["used_template"] = "";
	    if (!isset($this->parameters["no_image"])) $this->parameters["no_image"] = "";
	    
	    if (!isset($this->parameters["arrows"])) $this->parameters["arrows"] = true;
	    if (!isset($this->parameters["dotnav"])) $this->parameters["dotnav"] = true;
	    
	    if (!isset($this->parameters["animation"])) $this->parameters["animation"] = 'fade';
	    if (!isset($this->parameters["duration"])) $this->parameters["duration"] = 500;
	    if (!isset($this->parameters["height"])) $this->parameters["height"] = 'auto';
	    if (!isset($this->parameters["start"])) $this->parameters["start"] = 0;
	    if (!isset($this->parameters["autoplay"])) $this->parameters["autoplay"] = false;
	    if (!isset($this->parameters["pauseonhover"])) $this->parameters["pauseonhover"] = true;
	    if (!isset($this->parameters["autoplayinterval"])) $this->parameters["autoplayinterval"] = 7000;
	    if (!isset($this->parameters["videoautoplay"])) $this->parameters["videoautoplay"] = true;
	    if (!isset($this->parameters["videomute"])) $this->parameters["videomute"] = true;
	    if (!isset($this->parameters["kenburns"])) $this->parameters["kenburns"] = false;
	    if (!isset($this->parameters["kenburnsanimations"])) $this->parameters["kenburnsanimations"] = '';
	    if (!isset($this->parameters["slices"])) $this->parameters["slices"] = 15;
	    
		$general_form = "
		    <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_slideshow_arrows'>".$this->format_text($this->msg['cms_module_common_view_slideshow_arrows'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_common_view_slideshow_arrows' value='1' ".($this->parameters['arrows'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_slideshow_yes'])."&nbsp;
				    <input type='radio' name='cms_module_common_view_slideshow_arrows' value='0' ".(!$this->parameters['arrows'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_slideshow_no'])."
				</div>
			</div>
		    <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_slideshow_dotnav'>".$this->format_text($this->msg['cms_module_common_view_slideshow_dotnav'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_common_view_slideshow_dotnav' value='1' ".($this->parameters['dotnav'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_slideshow_yes'])."&nbsp;
				    <input type='radio' name='cms_module_common_view_slideshow_dotnav' value='0' ".(!$this->parameters['dotnav'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_slideshow_no'])."
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_slideshow_no_image'>".$this->format_text($this->msg['cms_module_common_view_slideshow_no_image'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_common_view_slideshow_no_image' value='".$this->format_text($this->parameters['no_image'])."'/>
				</div>
			</div>";

		$advanced_parameters = "
		    <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_slideshow_animation'>".$this->format_text($this->msg['cms_module_common_view_slideshow_animation'])."</label>
				</div>
				<div class='colonne-suite'>
			        ".$this->get_animations_list()."
				</div>
			</div>
	        <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_slideshow_duration'>".$this->format_text($this->msg['cms_module_common_view_slideshow_duration'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_common_view_slideshow_duration' value='".$this->format_text($this->parameters['duration'])."'/>
				</div>
			</div>
		    <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_slideshow_height'>".$this->format_text($this->msg['cms_module_common_view_slideshow_height'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_common_view_slideshow_height' value='".$this->format_text($this->parameters['height'])."'/>
				</div>
			</div>
	        <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_slideshow_start'>".$this->format_text($this->msg['cms_module_common_view_slideshow_start'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_common_view_slideshow_start' value='".$this->format_text($this->parameters['start'])."'/>
				</div>
			</div>
		    <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_slideshow_autoplay'>".$this->format_text($this->msg['cms_module_common_view_slideshow_autoplay'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_common_view_slideshow_autoplay' value='1' ".($this->parameters['autoplay'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_slideshow_yes'])."&nbsp;
				    <input type='radio' name='cms_module_common_view_slideshow_autoplay' value='0' ".(!$this->parameters['autoplay'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_slideshow_no'])."
				</div>
			</div>
		    <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_slideshow_pauseonhover'>".$this->format_text($this->msg['cms_module_common_view_slideshow_pauseonhover'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_common_view_slideshow_pauseonhover' value='1' ".($this->parameters['pauseonhover'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_slideshow_yes'])."&nbsp;
				    <input type='radio' name='cms_module_common_view_slideshow_pauseonhover' value='0' ".(!$this->parameters['pauseonhover'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_slideshow_no'])."
				</div>
			</div>
	        <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_slideshow_autoplayinterval'>".$this->format_text($this->msg['cms_module_common_view_slideshow_autoplayinterval'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_common_view_slideshow_autoplayinterval' value='".$this->format_text($this->parameters['autoplayinterval'])."'/>
				</div>
			</div>
		    <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_slideshow_videoautoplay'>".$this->format_text($this->msg['cms_module_common_view_slideshow_videoautoplay'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_common_view_slideshow_videoautoplay' value='1' ".($this->parameters['videoautoplay'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_slideshow_yes'])."&nbsp;
				    <input type='radio' name='cms_module_common_view_slideshow_videoautoplay' value='0' ".(!$this->parameters['videoautoplay'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_slideshow_no'])."
				</div>
			</div>
		    <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_slideshow_videomute'>".$this->format_text($this->msg['cms_module_common_view_slideshow_videomute'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_common_view_slideshow_videomute' value='1' ".($this->parameters['videomute'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_slideshow_yes'])."&nbsp;
				    <input type='radio' name='cms_module_common_view_slideshow_videomute' value='0' ".(!$this->parameters['videomute'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_slideshow_no'])."
				</div>
			</div>
		    <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_slideshow_kenburns'>".$this->format_text($this->msg['cms_module_common_view_slideshow_kenburns'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_common_view_slideshow_kenburns' value='1' ".($this->parameters['kenburns'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_slideshow_yes'])."&nbsp;
				    <input type='radio' name='cms_module_common_view_slideshow_kenburns' value='0' ".(!$this->parameters['kenburns'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_slideshow_no'])."
				</div>
			</div>
	        <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_slideshow_kenburnsanimations'>".$this->format_text($this->msg['cms_module_common_view_slideshow_kenburnsanimations'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_common_view_slideshow_kenburnsanimations' value='".$this->format_text($this->parameters['kenburnsanimations'])."'/>
				</div>
			</div>
	        <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_slideshow_slices'>".$this->format_text($this->msg['cms_module_common_view_slideshow_slices'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_common_view_slideshow_slices' value='".$this->format_text($this->parameters['slices'])."'/>
				</div>
			</div>";
		
		$form = gen_plus("general_parameters", $this->format_text($this->msg['cms_module_common_view_slideshow_general_parameters']),$general_form,true);
		$form.= gen_plus("advanced_parameters", $this->format_text($this->msg['cms_module_common_view_slideshow_advanced_parameters']),$advanced_parameters);
		$form.= parent::get_form() . $this->get_view_django_template_record();
		return $form;
	}
	
	public function get_view_django_template_record () 
	{
	    return "
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_django_template_record_content'>".$this->format_text($this->msg['cms_module_common_view_django_template_record_content'])."</label>
					</div>
					<div class='colonne-suite'>
						".notice_tpl::gen_tpl_select("cms_module_common_view_django_template_record_content",$this->parameters['used_template'])."
					</div>
				</div>
			</div>
		";
	}
	
	public function get_animations_list() {
	    $animations = ['fade', 'scroll', 'scale', 'swipe', 'slice-down', 'slice-up', 'slice-up-down', 'fold', 'puzzle', 'boxes', 'boxes-reverse', 'random-fx'];
	    $select = "<select name='cms_module_common_view_slideshow_animation'>";
	    foreach ($animations as $animation) {
	        $selected = $this->parameters['animation'] == $animation ? 'selected' : '';
	        $select .= "<option value='$animation' $selected>".$this->format_text($this->msg["cms_module_common_view_slideshow_$animation"])."</option>";
	    }
	    $select .= "</select>";
	    return $select;
	}
	
	public function save_form() {
	    global $cms_module_common_view_django_template_record_content;
	    global $cms_module_common_view_slideshow_no_image;
	    
	    global $cms_module_common_view_slideshow_arrows;
	    global $cms_module_common_view_slideshow_dotnav;
	    
	    global $cms_module_common_view_slideshow_animation;
	    global $cms_module_common_view_slideshow_duration;
	    global $cms_module_common_view_slideshow_height;
	    global $cms_module_common_view_slideshow_start;
	    global $cms_module_common_view_slideshow_autoplay;
	    global $cms_module_common_view_slideshow_pauseonhover;
	    global $cms_module_common_view_slideshow_autoplayinterval;
	    global $cms_module_common_view_slideshow_videoautoplay;
	    global $cms_module_common_view_slideshow_videomute;
	    global $cms_module_common_view_slideshow_kenburns;
	    global $cms_module_common_view_slideshow_kenburnsanimations;
	    global $cms_module_common_view_slideshow_slices;
	    
	    $this->parameters['used_template'] = $cms_module_common_view_django_template_record_content;
	    $this->parameters['no_image'] = $cms_module_common_view_slideshow_no_image;
	    
	    $this->parameters["arrows"] = ($cms_module_common_view_slideshow_arrows == 1 ? true : false);
	    $this->parameters["dotnav"] = ($cms_module_common_view_slideshow_dotnav == 1 ? true : false);
	    
	    $this->parameters["animation"] = $cms_module_common_view_slideshow_animation;
	    $this->parameters["duration"] = $cms_module_common_view_slideshow_duration;
	    $this->parameters["height"] = $cms_module_common_view_slideshow_height;
	    $this->parameters["start"] = $cms_module_common_view_slideshow_start;
	    $this->parameters["autoplay"] = ($cms_module_common_view_slideshow_autoplay == 1 ? true : false);
	    $this->parameters["pauseonhover"] = ($cms_module_common_view_slideshow_pauseonhover == 1 ? true : false);
	    $this->parameters["autoplayinterval"] = $cms_module_common_view_slideshow_autoplayinterval;
	    $this->parameters["videoautoplay"] = ($cms_module_common_view_slideshow_videoautoplay == 1 ? true : false);
	    $this->parameters["videomute"] = ($cms_module_common_view_slideshow_videomute == 1 ? true : false);
	    $this->parameters["kenburns"] = ($cms_module_common_view_slideshow_kenburns == 1 ? true : false);
	    $this->parameters["kenburnsanimations"] = $cms_module_common_view_slideshow_kenburnsanimations;
	    $this->parameters["slices"] = $cms_module_common_view_slideshow_slices;
	    
		return parent::save_form();	
	}
	
	public function get_headers($datas = array()) {
		$headers = parent::get_headers($datas);
		return $headers;
	}
	
	public function render($datas){
	    global $base_path;
	    
	    $html2return = '';
	    if (!empty($datas['records']) && (is_countable($datas['records']) && count($datas['records']))) {
	        $datas['id'] = $this->get_module_dom_id();
		    if(!isset($datas['get_vars']) || !$datas['get_vars']){
		        $datas['get_vars'] = $_GET;
		    }
		    if(!isset($datas['post_vars']) || !$datas['post_vars']){
		        $datas['post_vars'] = $_POST;
		    }
			$datas['id'] = $this->get_module_dom_id();
	        $id = "carousel_".$datas['id'];
	        $datas['no_image_url'] = $this->get_no_image_url();
    		$template_path = $base_path.'/temp/'.LOCATION.'_cms_slideshow_view_'.$this->id;
    		if(!file_exists($template_path) || (md5($this->parameters['active_template']) != md5_file($template_path))){
    		    file_put_contents($template_path, $this->parameters['active_template']);
    		}
			try{
    			$H2o = H2o_collection::get_instance($template_path);
    			$html2return = $H2o->render($datas);
    			$html2return .= $this->get_script_slider($id);
	    		}catch(Exception $e){
    		    $html2return = '<!-- '.$e->getMessage().' -->';
    		    $html2return .= '<div class="error_on_template" title="' . htmlspecialchars($e->getMessage(), ENT_QUOTES) . '">';
    		    $html2return .= $this->msg["cms_module_common_view_error_template"];
    		    $html2return .= '</div>';
			}
   		}
	return $html2return;
	}
	
	public function get_no_image_url() {
	    global $opac_default_style;
	    
	    $path = "./styles/$opac_default_style/images/";
	    if (!file_exists(realpath($path)."/".$this->parameters['no_image'])) {
	        $path = "./styles/common/images/";
	        if (!file_exists(realpath($path)."/".$this->parameters['no_image'])) {
	            $path = "./images/";
	            if (!file_exists(realpath($path)."/".$this->parameters['no_image'])) {
	                $path = "./images/";
	                $this->parameters['no_image'] = "no_image_carousel.jpg";
	            }
	        }
	    }
	    
	    return $path.$this->parameters['no_image'];
	}
	
	public function get_script_slider($id) {
	    return "
    		<script type='text/javascript'>
    		    jQuery(document).ready(function() {
                    if (document.getElementById('$id') && typeof UIkit.slideshow == 'function' ) {
        		        var slider = new UIkit.slideshow('#$id', {
        		            animation: '".(!empty($this->parameters['animation']) ? $this->parameters['animation'] : 'fade')."',
        		            duration: ".(!empty($this->parameters['duration']) ? $this->parameters['duration'] : 500).",
        		            height: '".(!empty($this->parameters['height']) ? $this->parameters['height'] : 'auto')."',
        		            start: ".(!empty($this->parameters['start']) ? $this->parameters['start'] : 0).",
        		            autoplay: ".(!empty($this->parameters['autoplay']) ? 'true' : 'false').",
        		            pauseOnHover: ".(!empty($this->parameters['pauseonhover']) ? 'true' : 'false').",
        		            autoplayInterval: ".(!empty($this->parameters['autoplayinterval']) ? $this->parameters['autoplayinterval'] : 7000).",
        		            videoautoplay: ".(!empty($this->parameters['videoautoplay']) ? 'true' : 'false').",
        		            videomute: ".(!empty($this->parameters['videomute']) ? 'true' : 'false').",
        		            kenburns: ".(!empty($this->parameters['kenburns']) ? 'true' : 'false').",
        		            kenburnsanimations: '".(!empty($this->parameters['kenburnsanimations']) ? $this->parameters['kenburnsanimations'] : '')."',
        		            slices: ".(!empty($this->parameters['slices']) ? $this->parameters['slices'] : 15)."
                        });

                        if (slider) {
                            if ('".!empty($this->parameters['dotnav'])."' && slider.element[0]) {
        	                    var dotnav = '';
                                for (let i = 0; i < slider.element[0].children[0].children.length; i++) {
                                    dotnav += '<li data-uk-slideshow-item=\"'+i+'\"><a href=\"\"></a></li>';
                                }
        	                    $(slider.element[0]).append('<ul class=\"uk-dotnav uk-dotnav-contrast uk-flex uk-flex-center\">'+dotnav+'</ul>');
        	                }
        	                if ('".!empty($this->parameters['arrows'])."') {
        		                $(slider.element[0]).addClass('uk-slidenav-position');
        	                    $(slider.element[0]).append('<a href=\"\" class=\"uk-slidenav uk-slidenav-contrast uk-slidenav-previous\" data-uk-slideshow-item=\"previous\"></a>');
        	                    $(slider.element[0]).append('<a href=\"\" class=\"uk-slidenav uk-slidenav-contrast uk-slidenav-next\" data-uk-slideshow-item=\"next\"></a>');
                            }
                        }
                    }
	            });
            </script>";
	}

	protected function get_managed_template_form($cms_template){
		global $opac_url_base;

		$form ="";
		if($cms_template != "new"){
			$infos = $this->managed_datas['templates'][$cms_template];
		}else{
			$infos = array(
				'name' => "Nouveau Template",
				'content' => $this->default_template
			);
		}
		if(!$this->managed_datas) $this->managed_datas = array();
		//nom
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_django_template_name'>".$this->format_text($this->msg['cms_module_common_view_django_template_name'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_common_view_django_template_name' value='".$this->format_text($infos['name'])."'/>
				</div>
			</div>";
		//contenu	
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_django_template_content'>".$this->format_text($this->msg['cms_module_common_view_template_item'])."</label>
					".$this->get_format_data_structure_tree("cms_module_common_view_django_template_content")."
				</div>
				<div class='colonne-suite'>
					<textarea name='cms_module_common_view_django_template_content'>".$this->format_text($infos['content'])."</textarea>
				</div>
			</div>";		
		return $form;
	}
		
	public function save_manage_form($managed_datas){
		global $cms_template;
		global $cms_template_delete;
		global $cms_module_common_view_django_template_name,$cms_module_common_view_django_template_content;
		
		if($cms_template_delete){
			unset($managed_datas['templates'][$cms_template_delete]);
		}else{
			if($cms_template == "new"){
				$cms_template = "template".(cms_module_common_view_django::get_max_template_id($managed_datas['templates'])+1);
			}
			$managed_datas['templates'][$cms_template] = array(
					'name' => stripslashes($cms_module_common_view_django_template_name),
					'content' => stripslashes($cms_module_common_view_django_template_content)
			);
		}		
		return $managed_datas;
	}	

	public function get_format_data_structure(){
	    $format_datas = array();
		$format_datas[] = array(
			 'var' => "no_image_url",
			 'desc'=> $this->msg['cms_module_common_view_slideshow_no_image_desc']
		);
		$format_datas = array_merge($format_datas, parent::get_format_data_structure());
		return $format_datas;
	}
}