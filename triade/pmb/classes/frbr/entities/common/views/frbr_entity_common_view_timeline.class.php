<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_view_timeline.class.php,v 1.1.12.1 2025/04/24 12:37:05 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_view_timeline extends frbr_entity_common_view {

	protected static $prefix = ''; /** Préfixe à dériver selon les entités enfants **/

	public function __construct($id=0){
		parent::__construct($id);
		$this->init_usable_fields();
	}


	protected function init_usable_fields() {}

	protected function get_form_value_name($name){
	    return "timeline_".$name;
	}

	protected function get_value_from_form($name){
	    global ${"timeline_".$name};
	    return ${"timeline_".$name};
	}



	protected function get_perso_fields($type, $datatype){
	    $data = array();
	    $query = 'select name, titre, idchamp from '.static::$prefix.'_custom where datatype = "'.$datatype.'" and type="'.$type.'"';
	    $result = pmb_mysql_query($query);
	    if(pmb_mysql_num_rows($result)){
	        while($row = pmb_mysql_fetch_object($result)){
	            $data['c_perso_'.$row->name] = $row->titre;
	        }
	    }
	    return $data;
	}

 	public function get_form(){

 	    if(!isset($this->parameters)){
 	        $this->parameters->timeline_fields = new stdClass();
 	    }

 		$form = "<hr/>";
 		$form.= "<h3>".$this->format_text($this->msg['cms_module_timeline_datasource_generic_header'])."</h3>";
		$form.= "<div class='row'>
					<div class='colonne3'>
						<label for=''>".$this->msg['cms_module_timeline_datasource_generic_title']."</label>
					</div>
					<div class='colonne-suite'>";
		$form.= $this->gen_parameters_selector(
				'title_fields',
				$this->get_form_value_name('title'),
				(isset($this->parameters->timeline_fields->title) ? $this->parameters->timeline_fields->title : '')
		);
		$form.="
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for=''>".$this->format_text($this->msg['cms_module_timeline_datasource_generic_resume'])."</label>
					</div>
					<div class='colonne-suite'>";
		$form.= $this->gen_parameters_selector(
				'resume_fields',
				$this->get_form_value_name('resume'),
				(isset($this->parameters->timeline_fields->resume) ? $this->parameters->timeline_fields->resume : ''),
				true
		);
		$form.="
					</div>
				</div>";
		$form.= "<div class='row'>
					<div class='colonne3'>
						<label for=''>".$this->format_text($this->msg['cms_module_timeline_datasource_generic_start_date'])."</label>
					</div>
					<div class='colonne-suite'>";
		$form.= $this->gen_parameters_selector(
				'date_fields',
				$this->get_form_value_name('start_date'),
				(isset($this->parameters->timeline_fields->start_date) ? $this->parameters->timeline_fields->start_date : '')
		);
		$form.="
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for=''>".$this->format_text($this->msg['cms_module_timeline_datasource_generic_end_date'])."</label>
					</div>
					<div class='colonne-suite'>";
		$form.= $this->gen_parameters_selector(
				'date_fields',
				$this->get_form_value_name('end_date'),
				(isset($this->parameters->timeline_fields->end_date) ? $this->parameters->timeline_fields->end_date : ''),
				true
		);
		$form.="
					</div>
				</div>";
		$form.= "<div class='row'>
					<div class='colonne3'>
						<label for=''>".$this->format_text($this->msg['cms_module_timeline_datasource_generic_image'])."</label>
					</div>
					<div class='colonne-suite'>";
		$form.= $this->gen_parameters_selector(
				'image_fields',
				$this->get_form_value_name('image'),
				(isset($this->parameters->timeline_fields->image) ? $this->parameters->timeline_fields->image : ''),
				true
		);

		//Checkbox start_at_end
		$form.="
					</div>
				</div>";
		$form.= "<div class='row'>
					<div class='colonne3'>
						<label for=''>".$this->format_text($this->msg['cms_module_timeline_datasource_generic_start_at_end'])."</label>
					</div>
					<div class='colonne-suite'>";
		$form.= '<input type ="checkbox" name="timeline_start_at_end" value="1" '.(!empty($this->parameters->timeline_fields->start_at_end) ? "checked" : "").'>';
		$form.="
					</div>
				</div>";

		//Input start_at_slide
		$form.= "<div class='row'>
					<div class='colonne3'>
						<label for=''>".$this->format_text($this->msg['cms_module_timeline_datasource_generic_start_at_slide'])."</label>
					</div>
					<div class='colonne-suite'>";
		$form.= '<input type="number" name="timeline_start_at_slide" value="'.(isset($this->parameters->timeline_fields->start_at_slide) ? $this->parameters->timeline_fields->start_at_slide : 0).'" min="0">';
		$form.="
					</div>
				</div>";
		return $form;
 	}

	public function save_form(){
		if(!isset($this->parameters->timeline_fields)){
			$this->parameters->timeline_fields = new stdClass();
		}
		$this->parameters->timeline_fields->title = $this->get_value_from_form('title');
		$this->parameters->timeline_fields->resume = $this->get_value_from_form('resume');
		$this->parameters->timeline_fields->start_date = $this->get_value_from_form('start_date');
		$this->parameters->timeline_fields->end_date = $this->get_value_from_form('end_date');
		$this->parameters->timeline_fields->image = $this->get_value_from_form('image');
		$this->parameters->timeline_fields->start_at_end= (!empty($this->get_value_from_form('start_at_end')) ? $this->get_value_from_form('start_at_end') : '0') ;
		$this->parameters->timeline_fields->start_at_slide = $this->get_value_from_form('start_at_slide');
		return parent::save_form();
	}

	protected function gen_parameters_selector($property_name, $selector_name, $selected='', $empty_default_value=''){
		$selector = '<select name="'.$selector_name.'">';

		if($empty_default_value){
			$selector.= '<option value="">'.$this->format_text($this->msg['cms_module_timeline_datasource_generic_selector_default']).'</option>';
		}
		foreach($this->{$property_name} as $value => $option){
			$selector.= '<option '.($selected == $value ? ' selected="selected "' : '' ).' value="'.$value.'">'.$this->format_text($option).'</option>';
		}
		$selector.= '</select>';
		return $selector;
	}
}