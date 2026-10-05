<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_carousel_uikit.class.php,v 1.10 2023/08/17 09:47:55 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($include_path."/h2o/h2o.php");

class cms_module_common_view_carousel_uikit extends cms_module_common_view_django {
	
	public function __construct($id = 0) {
		$this->use_jquery = true;
		$this->default_template = "
<div id='carousel_{{id}}' data-uk-slider>
    <div class='uk-slider-container'>
        <ul class='uk-slider'>
        	{% for record in records %}
        		<li>{{ record.content }}</li>
        	{% endfor %}
        </ul>
    </div>
</div>
";
		parent::__construct($id);
	}
	
	public function get_form(){
	    if (!isset($this->parameters["used_template"])) $this->parameters["used_template"] = "";
	    if (!isset($this->parameters["no_image"])) $this->parameters["no_image"] = "";
	    
	    if (!isset($this->parameters["arrows"])) $this->parameters["arrows"] = true;
	    if (!isset($this->parameters["nblarge"])) $this->parameters["nblarge"] = 4;
	    if (!isset($this->parameters["nbmedium"])) $this->parameters["nbmedium"] = 2;
	    if (!isset($this->parameters["nbsmall"])) $this->parameters["nbsmall"] = 2;
	    
	    if (!isset($this->parameters["center"])) $this->parameters["center"] = false;
	    if (!isset($this->parameters["threshold"])) $this->parameters["threshold"] = 10;
	    if (!isset($this->parameters["infinite"])) $this->parameters["infinite"] = true;
	    if (!isset($this->parameters["activecls"])) $this->parameters["activecls"] = 'uk-active';
	    if (!isset($this->parameters["autoplay"])) $this->parameters["autoplay"] = false;
	    if (!isset($this->parameters["pauseonhover"])) $this->parameters["pauseonhover"] = false;
	    if (!isset($this->parameters["autoplayinterval"])) $this->parameters["autoplayinterval"] = 7000;
	    
		$general_form = "
		    <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_carousel_uikit_arrows'>".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_arrows'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_common_view_carousel_uikit_arrows' value='1' ".($this->parameters['arrows'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_yes'])."&nbsp;
				    <input type='radio' name='cms_module_common_view_carousel_uikit_arrows' value='0' ".(!$this->parameters['arrows'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_no'])."
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_carousel_uikit_nblarge'>".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_nblarge'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_common_view_carousel_uikit_nblarge' value='" . $this->parameters['nblarge'] . "'/>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_carousel_uikit_nbmedium'>".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_nbmedium'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_common_view_carousel_uikit_nbmedium' value='" . $this->parameters['nbmedium'] . "'>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_carousel_uikit_nbsmall'>".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_nbsmall'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_common_view_carousel_uikit_nbsmall' value='" . $this->parameters['nbsmall'] . "'>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_carousel_uikit_no_image'>".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_no_image'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_common_view_carousel_uikit_no_image' value='".$this->format_text($this->parameters['no_image'])."'/>
				</div>
			</div>";

		$advanced_parameters = "
		    <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_carousel_uikit_center'>".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_center'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_common_view_carousel_uikit_center' value='1' ".($this->parameters['center'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_yes'])."&nbsp;
				    <input type='radio' name='cms_module_common_view_carousel_uikit_center' value='0' ".(!$this->parameters['center'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_no'])."
				</div>
			</div>
	        <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_carousel_uikit_threshold'>".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_threshold'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_common_view_carousel_uikit_threshold' value='".$this->format_text($this->parameters['threshold'])."'/>
				</div>
			</div>
		    <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_carousel_uikit_infinite'>".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_infinite'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_common_view_carousel_uikit_infinite' value='1' ".($this->parameters['infinite'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_yes'])."&nbsp;
				    <input type='radio' name='cms_module_common_view_carousel_uikit_infinite' value='0' ".(!$this->parameters['infinite'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_no'])."
				</div>
			</div>
	        <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_carousel_uikit_activecls'>".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_activecls'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_common_view_carousel_uikit_activecls' value='".$this->format_text($this->parameters['activecls'])."'/>
				</div>
			</div>
		    <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_carousel_uikit_autoplay'>".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_autoplay'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_common_view_carousel_uikit_autoplay' value='1' ".($this->parameters['autoplay'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_yes'])."&nbsp;
				    <input type='radio' name='cms_module_common_view_carousel_uikit_autoplay' value='0' ".(!$this->parameters['autoplay'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_no'])."
				</div>
			</div>
		    <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_carousel_uikit_pauseonhover'>".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_pauseonhover'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='radio' name='cms_module_common_view_carousel_uikit_pauseonhover' value='1' ".($this->parameters['pauseonhover'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_yes'])."&nbsp;
				    <input type='radio' name='cms_module_common_view_carousel_uikit_pauseonhover' value='0' ".(!$this->parameters['pauseonhover'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_no'])."
				</div>
			</div>
	        <div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_carousel_uikit_autoplayinterval'>".$this->format_text($this->msg['cms_module_common_view_carousel_uikit_autoplayinterval'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_common_view_carousel_uikit_autoplayinterval' value='".$this->format_text($this->parameters['autoplayinterval'])."'/>
				</div>
			</div>";
		
		$form = gen_plus("general_parameters", $this->format_text($this->msg['cms_module_common_view_carousel_uikit_general_parameters']),$general_form,true);
		$form.= gen_plus("advanced_parameters", $this->format_text($this->msg['cms_module_common_view_carousel_uikit_advanced_parameters']),$advanced_parameters);
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
	
	public function save_form(){
	    global $cms_module_common_view_django_template_record_content;
	    global $cms_module_common_view_carousel_uikit_no_image;
	    
	    global $cms_module_common_view_carousel_uikit_arrows;
	    global $cms_module_common_view_carousel_uikit_nblarge;
	    global $cms_module_common_view_carousel_uikit_nbmedium;
	    global $cms_module_common_view_carousel_uikit_nbsmall;
	    
	    global $cms_module_common_view_carousel_uikit_center;
	    global $cms_module_common_view_carousel_uikit_threshold;
	    global $cms_module_common_view_carousel_uikit_infinite;
	    global $cms_module_common_view_carousel_uikit_activecls;
	    global $cms_module_common_view_carousel_uikit_autoplay;
	    global $cms_module_common_view_carousel_uikit_pauseonhover;
	    global $cms_module_common_view_carousel_uikit_autoplayinterval;
	    
	    $this->parameters['used_template'] = $cms_module_common_view_django_template_record_content;
	    $this->parameters['no_image'] = $cms_module_common_view_carousel_uikit_no_image;
	    
	    $this->parameters["arrows"] = ($cms_module_common_view_carousel_uikit_arrows == 1 ? true : false);
	    $this->parameters["nblarge"] = $cms_module_common_view_carousel_uikit_nblarge;
	    $this->parameters["nbmedium"] = $cms_module_common_view_carousel_uikit_nbmedium;
	    $this->parameters["nbsmall"] = $cms_module_common_view_carousel_uikit_nbsmall;
	    
	    $this->parameters["center"] = ($cms_module_common_view_carousel_uikit_center == 1 ? true : false);
	    $this->parameters["threshold"] = $cms_module_common_view_carousel_uikit_threshold;
	    $this->parameters["infinite"] = ($cms_module_common_view_carousel_uikit_infinite == 1 ? true : false);
	    $this->parameters["activecls"] = $cms_module_common_view_carousel_uikit_activecls;
	    $this->parameters["autoplay"] = ($cms_module_common_view_carousel_uikit_autoplay == 1 ? true : false);
	    $this->parameters["pauseonhover"] = ($cms_module_common_view_carousel_uikit_pauseonhover == 1 ? true : false);
	    $this->parameters["autoplayinterval"] = $cms_module_common_view_carousel_uikit_autoplayinterval;
	    
		return parent::save_form();	
	}
	
	public function get_headers($datas = array()) {
		$headers = parent::get_headers($datas);
		
		return $headers;
	}
	
	public function render($datas){
		global $base_path;
		
		$html2return = '';
		if (!empty($datas)) {
		    $datas['id'] = $this->get_module_dom_id();
		    if(!isset($datas['get_vars']) || !$datas['get_vars']){
		        $datas['get_vars'] = $_GET;
		    }
		    if(!isset($datas['post_vars']) || !$datas['post_vars']){
		        $datas['post_vars'] = $_POST;
		    }
	        $id = "carousel_".$datas['id'];
	        $datas['no_image_url'] = $this->get_no_image_url();
    		$template_path = $base_path.'/temp/'.LOCATION.'_cms_carousel_uikit_view_'.$this->id;
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
    		<script>
    		    jQuery(document).ready(function() {
                    if (document.getElementById('$id') && typeof UIkit.slider == 'function' ) {
        		        var slider = new UIkit.slider('#$id', {
        		            center: ".(!empty($this->parameters['center']) ? 'true' : 'false').",
        		            threshold: ".(!empty($this->parameters['threshold']) ? $this->parameters['threshold'] : 10).",
        		            infinite: ".(!empty($this->parameters['infinite']) ? 'true' : 'false').",
        		            activecls: '".(!empty($this->parameters['activecls']) ? $this->parameters['activecls'] : "uk-active")."',
        		            autoplay: ".(!empty($this->parameters['autoplay']) ? 'true' : 'false').",
        		            pauseOnHover: ".(!empty($this->parameters['pauseonhover']) ? 'true' : 'false').",
        		            autoplayInterval: ".(!empty($this->parameters['autoplayinterval']) ? $this->parameters['autoplayinterval'] : 7000)."
                        });
                        
                        if (slider) {
        	                if ('".!empty($this->parameters['arrows'])."' && slider.element[0]) {
        		                $(slider.element[0]).addClass('uk-slidenav-position');
        	                    $(slider.element[0]).append('<a href=\"\" class=\"uk-slidenav uk-slidenav-contrast uk-slidenav-previous\" data-uk-slider-item=\"previous\"></a>');
        	                    $(slider.element[0]).append('<a href=\"\" class=\"uk-slidenav uk-slidenav-contrast uk-slidenav-next\" data-uk-slider-item=\"next\"></a>');
                            }
        	                $(slider.container[0]).addClass('uk-grid-width-small-1-".$this->parameters['nbsmall']."');
        	                $(slider.container[0]).addClass('uk-grid-width-medium-1-".$this->parameters['nbmedium']."');
        	                $(slider.container[0]).addClass('uk-grid-width-large-1-".$this->parameters['nblarge']."');
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
			 'desc'=> $this->msg['cms_module_common_view_carousel_uikit_no_image_desc']
		);
		$format_datas = array_merge($format_datas, parent::get_format_data_structure());
		return $format_datas;
	}
}