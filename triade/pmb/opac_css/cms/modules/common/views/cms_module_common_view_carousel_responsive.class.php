<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_carousel_responsive.class.php,v 1.27.2.1 2025/01/17 10:40:43 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/h2o/h2o.php");

class cms_module_common_view_carousel_responsive extends cms_module_common_view_django{
	
	
	public function __construct($id=0){
		$this->use_jquery = true;
		$this->default_template = "
<ul id='carousel_{{id}}'>
	{% for record in records %}
		<li class='{{id}}_item'>
			<a href='{{record.link}}' title='{{record.title}}'>
				<img src='{{record.vign}}' alt=''/>
				<br />
			</a>
		</li>
	{% endfor %}
</ul>
";
		parent::__construct($id);
	}
	
	public function get_form(){
		if (!isset($this->parameters["no_image"]))				$this->parameters["no_image"] = "no_image_carousel.jpg";
		//valeur par défaut des paramètres généraux
		if (!isset($this->parameters["mode"]))					$this->parameters["mode"] = "horizontal";
		if (!isset($this->parameters["speed"]))					$this->parameters["speed"] = 500;
		if (!isset($this->parameters["pause"]))					$this->parameters["pause"] = 4000;
		if (!isset($this->parameters["pager"])) 				$this->parameters["pager"] = true;
		if (!isset($this->parameters["autostart"])) 			$this->parameters["autostart"] = true;
		if (!isset($this->parameters["autotransition"]))		$this->parameters["autotransition"] = true;
		if (!isset($this->parameters["autohover"]))				$this->parameters["autohover"] = true;
		if (!isset($this->parameters["display_min_quantity"])) 	$this->parameters["display_min_quantity"] = 2;
		if (!isset($this->parameters["display_max_quantity"])) 	$this->parameters["display_max_quantity"] = 3;
		if (!isset($this->parameters["slide_quantity"]))		$this->parameters["slide_quantity"] = 0;
		if (!isset($this->parameters["slide_width"]))			$this->parameters["slide_width"] = 90;
		//valeur par défaut des paramètres avancés
		if (!isset($this->parameters["slide_margin"]))			$this->parameters["slide_margin"] = 0;
		if (!isset($this->parameters["random_start"]))			$this->parameters["random_start"] = false; 
		if (!isset($this->parameters["easing"]))				$this->parameters["easing"] = null;
		if (!isset($this->parameters["captions"]))				$this->parameters["captions"] = false;
		if (!isset($this->parameters["adaptive_height"]))		$this->parameters["adaptive_height"] = false;
		if (!isset($this->parameters["adaptive_height_speed"]))	$this->parameters["adaptive_height_speed"] = "500";
		if (!isset($this->parameters["pager_type"]))			$this->parameters["pager_type"] = "full";
		if (!isset($this->parameters["pager_short_separator"]))	$this->parameters["pager_short_separator"] = "/";
		if (!isset($this->parameters["controls"]))				$this->parameters["controls"] = true;
		if (!isset($this->parameters["next_text"]))				$this->parameters["next_text"] = "Suivant";
		if (!isset($this->parameters["previous_text"]))			$this->parameters["previous_text"] = "Précédent";
		if (!isset($this->parameters["auto_controls"]))			$this->parameters["auto_controls"] = false;
		if (!isset($this->parameters["start_text"]))			$this->parameters["start_text"] = "Jouer";
		if (!isset($this->parameters["stop_text"]))				$this->parameters["stop_text"] = "Stop";
		if (!isset($this->parameters["autocontrols_combine"]))	$this->parameters["autocontrols_combine"] = false;
		if (!isset($this->parameters["auto_direction"]))		$this->parameters["auto_direction"] = "next";
		if (!isset($this->parameters["auto_delay"]))			$this->parameters["auto_delay"] = 0;			
		if (!isset($this->parameters["used_template"]))			$this->parameters["used_template"] = "";
		
		$general_form = "
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_mode'>".$this->format_text($this->msg['cms_module_common_view_carousel_mode'])."</label>
					</div>
					<div class='colonne-suite'>
						<select name='cms_module_common_view_carousel_mode'>
							<option value='horizontal' ".($this->parameters['mode'] == "horizontal" ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_common_view_carousel_mode_horizontal'])."</option>
							<option value='vertical' ".($this->parameters['mode'] == "vertical" ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_common_view_carousel_mode_vertical'])."</option>
							<option value='fade' ".($this->parameters['mode'] == "fade" ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_common_view_carousel_mode_fade'])."</option>
						</select>
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_speed'>".$this->format_text($this->msg['cms_module_common_view_carousel_speed'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='cms_module_common_view_carousel_speed' value='".$this->format_text($this->parameters['speed'])."'/>
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_pause'>".$this->format_text($this->msg['cms_module_common_view_carousel_pause'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='cms_module_common_view_carousel_pause' value='".$this->format_text($this->parameters['pause'])."'/>
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_display_max_quantity'>".$this->format_text($this->msg['cms_module_common_view_carousel_display_max_quantity'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='cms_module_common_view_carousel_display_max_quantity' value='".$this->format_text($this->parameters['display_max_quantity'])."'/>
					</div>
				</div>
					<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_display_min_quantity'>".$this->format_text($this->msg['cms_module_common_view_carousel_display_min_quantity'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='cms_module_common_view_carousel_display_min_quantity' value='".$this->format_text($this->parameters['display_min_quantity'])."'/>
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_slide_quantity'>".$this->format_text($this->msg['cms_module_common_view_carousel_slide_quantity'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='cms_module_common_view_carousel_slide_quantity' value='".$this->format_text($this->parameters['slide_quantity'])."'/>
					</div>
				</div>	
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_slide_width'>".$this->format_text($this->msg['cms_module_common_view_carousel_slide_width'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='cms_module_common_view_carousel_slide_width' value='".$this->format_text($this->parameters['slide_width'])."'/>
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_autostart'>".$this->format_text($this->msg['cms_module_common_view_carousel_autostart'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='radio' name='cms_module_common_view_carousel_autostart' value='1' ".($this->parameters['autostart'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_yes'])."
				  &nbsp;<input type='radio' name='cms_module_common_view_carousel_autostart' value='0' ".(!$this->parameters['autostart'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_no'])."
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_autotransition'>".$this->format_text($this->msg['cms_module_common_view_carousel_autotransition'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='radio' name='cms_module_common_view_carousel_autotransition' value='1' ".($this->parameters['autotransition'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_yes'])."
				  &nbsp;<input type='radio' name='cms_module_common_view_carousel_autotransition' value='0' ".(!$this->parameters['autotransition'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_no'])."
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_autohover'>".$this->format_text($this->msg['cms_module_common_view_carousel_autohover'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='radio' name='cms_module_common_view_carousel_autohover' value='1' ".($this->parameters['autohover'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_yes'])."
				  &nbsp;<input type='radio' name='cms_module_common_view_carousel_autohover' value='0' ".(!$this->parameters['autohover'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_no'])."
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_pager'>".$this->format_text($this->msg['cms_module_common_view_carousel_pager'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='radio' name='cms_module_common_view_carousel_pager' value='1' ".($this->parameters['pager'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_yes'])."
				  &nbsp;<input type='radio' name='cms_module_common_view_carousel_pager' value='0' ".(!$this->parameters['pager'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_no'])."
					</div>
 				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_no_image'>".$this->format_text($this->msg['cms_module_common_view_carousel_no_image'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='cms_module_common_view_carousel_no_image' value='".$this->format_text($this->parameters['no_image'])."'/>
					</div>
				</div>";

		
		$advanced_parameters = "
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_slide_margin'>".$this->format_text($this->msg['cms_module_common_view_carousel_slide_margin'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='cms_module_common_view_carousel_slide_margin' value='".$this->format_text($this->parameters['slide_margin'])."'/>
					</div>
 				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_random_start'>".$this->format_text($this->msg['cms_module_common_view_carousel_random_start'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='radio' name='cms_module_common_view_carousel_random_start' value='1' ".($this->parameters['random_start'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_yes'])."
				  &nbsp;<input type='radio' name='cms_module_common_view_carousel_random_start' value='0' ".(!$this->parameters['random_start'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_no'])."
					</div>
 				</div>	
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_auto_delay'>".$this->format_text($this->msg['cms_module_common_view_carousel_auto_delay'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='cms_module_common_view_carousel_auto_delay' value='".$this->format_text($this->parameters['auto_delay'])."'/>
					</div>
 				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_auto_direction'>".$this->format_text($this->msg['cms_module_common_view_carousel_auto_direction'])."</label>
					</div>
					<div class='colonne-suite'>
						<select name='cms_module_common_view_carousel_auto_direction'>
								<option value='next' ".($this->parameters['auto_direction'] == "next" ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_common_view_carousel_auto_direction_next'])."</option>
								<option value='prev' ".($this->parameters['auto_direction'] == "prev" ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_common_view_carousel_auto_direction_prev'])."</option>
						</select>
					</div>
 				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_easing'>".$this->format_text($this->msg['cms_module_common_view_carousel_easing'])."</label>
					</div>
					<div class='colonne-suite'>
						<select name='cms_module_common_view_carousel_easing'>
								<option value='linear' ".($this->parameters['easing'] == "linear" ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_common_view_carousel_easing_linear'])."</option>
								<option value='ease' ".($this->parameters['easing'] == "ease" ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_common_view_carousel_easing_ease'])."</option>
								<option value='ease-in' ".($this->parameters['easing'] == "ease-in" ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_common_view_carousel_easing_ease_in'])."</option>
								<option value='ease-out' ".($this->parameters['easing'] == "ease-out" ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_common_view_carousel_easing_ease_out'])."</option>
								<option value='ease-in-out' ".($this->parameters['easing'] == "ease-in-out" ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_common_view_carousel_easing_ease_in_out'])."</option>
						</select>
					</div>
 				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_captions'>".$this->format_text($this->msg['cms_module_common_view_carousel_captions'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='radio' name='cms_module_common_view_carousel_captions' value='1' ".($this->parameters['captions'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_yes'])."
				  &nbsp;<input type='radio' name='cms_module_common_view_carousel_captions' value='0' ".(!$this->parameters['captions'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_no'])."
					</div>
 				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_adaptive_height'>".$this->format_text($this->msg['cms_module_common_view_carousel_adaptive_height'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='radio' name='cms_module_common_view_carousel_adaptive_height' value='1' ".($this->parameters['adaptive_height'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_yes'])."
				  &nbsp;<input type='radio' name='cms_module_common_view_carousel_adaptive_height' value='0' ".(!$this->parameters['adaptive_height'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_no'])."
					</div>
 				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_adaptive_height_speed'>".$this->format_text($this->msg['cms_module_common_view_carousel_adaptive_height_speed'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='cms_module_common_view_carousel_adaptive_height_speed' value='".$this->format_text($this->parameters['adaptive_height_speed'])."'/>
					</div>
 				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_page_type'>".$this->format_text($this->msg['cms_module_common_view_carousel_page_type'])."</label>
					</div>
					<div class='colonne-suite'>
						<select name='cms_module_common_view_carousel_page_type'>
								<option value='full' ".($this->parameters['pager_type'] == "full" ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_common_view_carousel_page_type_full'])."</option>
								<option value='short' ".($this->parameters['pager_type'] == "short" ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_common_view_carousel_page_type_short'])."</option>
						</select>
					</div>
 				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_pager_short_separator'>".$this->format_text($this->msg['cms_module_common_view_carousel_pager_short_separator'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='cms_module_common_view_carousel_pager_short_separator' value='".$this->format_text($this->parameters['pager_short_separator'])."'/>
					</div>
 				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_controls'>".$this->format_text($this->msg['cms_module_common_view_carousel_controls'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='radio' name='cms_module_common_view_carousel_controls' value='1' ".($this->parameters['controls'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_yes'])."
				  &nbsp;<input type='radio' name='cms_module_common_view_carousel_controls' value='0' ".(!$this->parameters['controls'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_no'])."
					</div>
 				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_next_text'>".$this->format_text($this->msg['cms_module_common_view_carousel_next_text'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='cms_module_common_view_carousel_next_text' value='".$this->format_text($this->parameters['next_text'])."'/>
					</div>
 				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_previous_text'>".$this->format_text($this->msg['cms_module_common_view_carousel_previous_text'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='cms_module_common_view_carousel_previous_text' value='".$this->format_text($this->parameters['previous_text'])."'/>
					</div>
 				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_auto_controls'>".$this->format_text($this->msg['cms_module_common_view_carousel_auto_controls'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='radio' name='cms_module_common_view_carousel_auto_controls' value='1' ".($this->parameters['auto_controls'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_yes'])."
				  &nbsp;<input type='radio' name='cms_module_common_view_carousel_auto_controls' value='0' ".(!$this->parameters['auto_controls'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_no'])."
					</div>
 				</div>								
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_start_text'>".$this->format_text($this->msg['cms_module_common_view_carousel_start_text'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='cms_module_common_view_carousel_start_text' value='".$this->format_text($this->parameters['start_text'])."'/>
					</div>
 				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_stop_text'>".$this->format_text($this->msg['cms_module_common_view_carousel_stop_text'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='cms_module_common_view_carousel_stop_text' value='".$this->format_text($this->parameters['stop_text'])."'/>
					</div>
 				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_view_carousel_auto_controls_combine'>".$this->format_text($this->msg['cms_module_common_view_carousel_auto_controls_combine'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='radio' name='cms_module_common_view_carousel_auto_controls_combine' value='1' ".($this->parameters['autocontrols_combine'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_yes'])."
				  &nbsp;<input type='radio' name='cms_module_common_view_carousel_auto_controls_combine' value='0' ".(!$this->parameters['autocontrols_combine'] ? "checked='checked'" : "")."/>&nbsp;".$this->format_text($this->msg['cms_module_common_view_carousel_no'])."
					</div>
 				</div>";
		
		$form = gen_plus("general_parameters", $this->format_text($this->msg['cms_module_common_view_carousel_general_parameters']),$general_form,true);
		$form.= gen_plus("advanced_parameters", $this->format_text($this->msg['cms_module_common_view_carousel_advanced_parameters']),$advanced_parameters);
		$form.=
				parent::get_form()
				."
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
		return $form;
	}
	
	public function save_form(){
 		global $cms_module_common_view_carousel_mode;
 		global $cms_module_common_view_carousel_speed;
 		global $cms_module_common_view_carousel_pause;
  		global $cms_module_common_view_carousel_display_max_quantity;
  		global $cms_module_common_view_carousel_display_min_quantity;
 		global $cms_module_common_view_carousel_slide_quantity;
 		global $cms_module_common_view_carousel_autostart;
 		global $cms_module_common_view_carousel_autotransition;
 		global $cms_module_common_view_django_template_record_content;
 		global $cms_module_common_view_carousel_autohover;
 		global $cms_module_common_view_carousel_pager;
 		global $cms_module_common_view_carousel_slide_width;
 		global $cms_module_common_view_carousel_no_image;
 		
 		global $cms_module_common_view_carousel_slide_margin;
 		global $cms_module_common_view_carousel_random_start;
 		global $cms_module_common_view_carousel_auto_delay;
 		global $cms_module_common_view_carousel_auto_direction;
 		global $cms_module_common_view_carousel_easing;
 		global $cms_module_common_view_carousel_captions;
 		global $cms_module_common_view_carousel_adaptive_height;
 		global $cms_module_common_view_carousel_adaptive_height_speed;
 		global $cms_module_common_view_carousel_page_type;
 		global $cms_module_common_view_carousel_pager_short_separator;
 		global $cms_module_common_view_carousel_controls;
 		global $cms_module_common_view_carousel_next_text;
 		global $cms_module_common_view_carousel_previous_text;
 		global $cms_module_common_view_carousel_auto_controls;
 		global $cms_module_common_view_carousel_start_text;
 		global $cms_module_common_view_carousel_stop_text;
 		global $cms_module_common_view_carousel_auto_controls_combine;
 		
 		//template
 		$this->parameters['used_template'] = $cms_module_common_view_django_template_record_content;
 		$this->parameters['no_image'] = $cms_module_common_view_carousel_no_image;
 		
 		//parametres généraux
 		$this->parameters['mode'] = $cms_module_common_view_carousel_mode;
 		$this->parameters['speed'] = (int) $cms_module_common_view_carousel_speed;
 		$this->parameters['pause'] = (int) $cms_module_common_view_carousel_pause;
 		$this->parameters['display_max_quantity'] = (int) $cms_module_common_view_carousel_display_max_quantity;
 		$this->parameters['display_min_quantity'] = (int) $cms_module_common_view_carousel_display_min_quantity;
 		$this->parameters['slide_quantity'] = (int) $cms_module_common_view_carousel_slide_quantity;
 		$this->parameters['slide_width'] = (int) $cms_module_common_view_carousel_slide_width;
 		$this->parameters['autostart'] = $cms_module_common_view_carousel_autostart==1 ? true : false;
 		$this->parameters['autotransition'] = $cms_module_common_view_carousel_autotransition==1 ? true : false;
 		$this->parameters['autohover'] = $cms_module_common_view_carousel_autohover==1 ? true : false;
 		$this->parameters['pager'] = $cms_module_common_view_carousel_pager==1 ? true : false;
		//paramètres avancés
 		$this->parameters["slide_margin"] = (int) $cms_module_common_view_carousel_slide_margin;
 		$this->parameters["random_start"] = $cms_module_common_view_carousel_random_start == 1 ? true : false;
 		$this->parameters["easing"] = $cms_module_common_view_carousel_easing;
 		$this->parameters["captions"] = $cms_module_common_view_carousel_captions == 1 ? true : false;
 		$this->parameters["adaptive_height"] = $cms_module_common_view_carousel_adaptive_height == 1 ? true : false;
 		$this->parameters["adaptive_height_speed"] = $cms_module_common_view_carousel_adaptive_height_speed;
 		$this->parameters["pager_type"] = $cms_module_common_view_carousel_page_type;
 		$this->parameters["pager_short_separator"] = $cms_module_common_view_carousel_pager_short_separator!= "" ? $cms_module_common_view_carousel_pager_short_separator : "/";
 		$this->parameters["controls"] = $cms_module_common_view_carousel_controls == 1 ? true : false;
 		$this->parameters["next_text"] = $cms_module_common_view_carousel_next_text;
 		$this->parameters["previous_text"] = $cms_module_common_view_carousel_previous_text;
 		$this->parameters["auto_controls"] = $cms_module_common_view_carousel_auto_controls == 1 ? true : false;
 		$this->parameters["start_text"] = $cms_module_common_view_carousel_start_text;
 		$this->parameters["stop_text"] = $cms_module_common_view_carousel_stop_text;
 		$this->parameters["autocontrols_combine"] = $cms_module_common_view_carousel_auto_controls_combine == 1 ? true : false;
 		$this->parameters["auto_direction"] = $cms_module_common_view_carousel_auto_direction;
 		$this->parameters["auto_delay"] = (int) $cms_module_common_view_carousel_auto_delay;
		return parent::save_form();	
	}
	
	public function get_headers($datas=array()){
		global $base_path;
		$headers = parent::get_headers($datas);		
		$headers[]= "<script src='".$base_path."/cms/modules/common/includes/javascript/jquery.bxsliderv4.min.js'></script>";
		$headers[]= "<script>
		document.addEventListener('DOMContentLoaded', function(){
		if (navigator.userAgent.search(\"Firefox\") >= 0) {
		    var ff_version = navigator.userAgent.match(/Firefox\/([\d]+\.[\d])+/);
		    ff_version = parseFloat(ff_version[1]);
		    if(ff_version == 0 || ff_version >= 59) {
		        $('body').on('mousedown', '.bx-viewport a', function() {
		            var ff_link = $(this);
		            var ff_href = ff_link.attr('href');
		            if(ff_href) {
		                location.href = ff_href;
		                return false;
		            }
		        });
		    }
		}})</script>";
		$headers[]= "<link rel='stylesheet' type='text/css' href='".$base_path."/cms/modules/common/includes/css/jquery.bxslider.css'/>";
		return $headers;
	}
	
	public function render($datas){
	    global $opac_default_style, $base_path;
		$html2return = "";
		
		//TODO VERIF DOM ET APPEL AU JS
		if(is_countable($datas['records']) && count($datas['records'])){
			$id = "carousel_".$this->get_module_dom_id();
			$datas['id']=$this->get_module_dom_id();
			if(!isset($datas['get_vars']) || !$datas['get_vars']){
				$datas['get_vars'] = $_GET;
			}
			if(!isset($datas['post_vars']) || !$datas['post_vars']){
				$datas['post_vars'] = $_POST;
			}
			//pour la no-image, on cherche celle du style, du common, du dossier image de base, sinon on sert celle par défaut
			$path = "./styles/".$opac_default_style."/images/";
			if(!file_exists(realpath($path)."/".$this->parameters['no_image'])){
				$path = "./styles/common/images/";  
				if(!file_exists(realpath($path)."/".$this->parameters['no_image'])){
					$path = "./images/";
					if(!file_exists(realpath($path)."/".$this->parameters['no_image'])){
						$path = "./images/";
						$this->parameters['no_image'] = "no_image_carousel.jpg";
					}
				}
			}
			$datas['no_image_url'] = $path.$this->parameters['no_image'];
			if (is_countable($datas['records'])) {
    			for($i=0 ; $i<count($datas['records']) ; $i++){
    			    if(is_array($datas['records'][$i]) && (empty($datas['records'][$i]['vign']))){
    					$datas['records'][$i]['vign'] = $datas['no_image_url'];
    				}
    			}
			}
			
			$template_path = $base_path.'/temp/'.LOCATION.'_cms_carousel_responsive_view_'.$this->id;
			if(!file_exists($template_path) || (md5($this->parameters['active_template']) != md5_file($template_path))){
			    file_put_contents($template_path, $this->parameters['active_template']);
			}
			try{
                $H2o = H2o_collection::get_instance($template_path);
                $html2return.= $H2o->render($datas);
			}catch(Exception $e){
			    $html2return = '<!-- '.$e->getMessage().' -->';
			    $html2return .= '<div class="error_on_template" title="' . htmlspecialchars($e->getMessage(), ENT_QUOTES) . '">';
			    $html2return .= $this->msg["cms_module_common_view_error_template"];
			    $html2return .= '</div>';
			}
			$html2return.= "
		<script>
			jQuery(document).ready(function() {
				jQuery('#".$id."').bxSlider({
					//parametres generaux
					mode: 					'".(isset($this->parameters['mode']) ? $this->parameters['mode'] : '')."',
					speed: 					'".(isset($this->parameters['speed']) ? $this->parameters['speed'] : '')."',
					pause: 					'".(isset($this->parameters['pause']) ? $this->parameters['pause'] : '')."',	
					autoStart: 				".(isset($this->parameters['autostart']) && $this->parameters['autostart'] ? "true" : "false").",
					autoHover: 				".(isset($this->parameters['autohover']) && $this->parameters['autohover'] ? "true" : "false").",	
					pager: 					".(isset($this->parameters['pager']) && $this->parameters['pager'] ? "true" : "false").",	
					moveSlides : 			'".(isset($this->parameters['slide_quantity']) ? $this->parameters['slide_quantity'] : '')."',	
					minSlides: 				'".(isset($this->parameters['display_min_quantity']) ? $this->parameters['display_min_quantity'] : '')."',	
					maxSlides: 				'".(isset($this->parameters['display_max_quantity']) ? $this->parameters['display_max_quantity'] : '')."',
					slideWidth: 			'".(isset($this->parameters['slide_width']) ? $this->parameters['slide_width'] : '')."',
					//parametres avances
					slideMargin: 			'".(isset($this->parameters['slide_margin']) ? $this->parameters['slide_margin'] : '')."',
					randomStart: 			".(isset($this->parameters['randomStart']) && $this->parameters['randomStart'] ? "true" : "false").",
					easing: 				'".(isset($this->parameters["easing"]) ? $this->parameters["easing"] : '')."',
					captions: 				".(isset($this->parameters['captions']) && $this->parameters['captions'] ? "true" : "false").",
					adaptiveHeight:			".(isset($this->parameters['adaptive_height']) && $this->parameters['adaptive_height'] ? "true" : "false").",
					adaptiveHeightSpeed:	'".(isset($this->parameters["adaptive_height_speed"]) ? $this->parameters["adaptive_height_speed"] : '')."',
					pagerType:				'".(isset($this->parameters["pager_type"]) ? $this->parameters["pager_type"] : '')."',
					pagerShortSeparator:	'".(isset($this->parameters["pager_short_separator"]) ? $this->parameters["pager_short_separator"] : '')."',
					controls:				".(isset($this->parameters['controls']) && $this->parameters['controls'] ? "true" : "false").",
					nextText:				'".(isset($this->parameters["next_text"]) ? $this->parameters["next_text"] : '')."',
					prevText:				'".(isset($this->parameters["previous_text"]) ? $this->parameters["previous_text"] : '')."',
					autoControls: 			".(isset($this->parameters['auto_controls']) && $this->parameters['auto_controls'] ? "true" : "false").",
					startText: 				'".(isset($this->parameters["start_text"]) ? $this->parameters["start_text"] : '')."',
					stopText: 				'".(isset($this->parameters["stop_text"]) ? $this->parameters["stop_text"] : '')."',
					autoControlsCombine: 	".(isset($this->parameters['autocontrols_combine']) && $this->parameters['autocontrols_combine'] ? "true" : "false").",
					autoDirection: 			'".(isset($this->parameters["auto_direction"]) ? $this->parameters["auto_direction"] : '')."',
					autoDelay: 				'".(isset($this->parameters["auto_delay"]) ? $this->parameters["auto_delay"] : '')."',
					auto: 					".(isset($this->parameters['autotransition']) && $this->parameters['autotransition'] ? "true" : "false")."
				});
			});
		</script>";
		}
		return $html2return;
	}

	protected function get_managed_template_form($cms_template){

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
		$format_datas=array();
		$format_datas[]= array(
			 'var' => "no_image_url",
			 'desc'=> $this->msg['cms_module_common_view_carousel_no_image_desc']
		);
		$format_datas = array_merge($format_datas,parent::get_format_data_structure());
		return $format_datas;
	}
}
