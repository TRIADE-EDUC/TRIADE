<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_view_timeline.class.php,v 1.3 2023/08/17 09:47:55 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_view_timeline extends frbr_entity_common_view {
	
	protected static $prefix = ''; /** Préfixe à dériver selon les entités enfants **/
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->init_usable_fields();
	}
	
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
	
	public function render($datas, $grouped_datas = []){
	    global $base_path;
	    
	    $this->parameters->height = 500;
	    $json = $this->get_JSON($datas);
	    
	    
	    if(count($json['events']) == 0){
	        $json['events'][] = array(
	            'start_date' => array(
	                'year' => 1985,
	                'month' => 06,
	                'day' => 17,
	            ),
	            'text' => array(
	                'headline' => 'headline',
	                'text' => 'text'
	            )
	        );
	    }
	    $html = '
<script src="'.$base_path.'/cms/modules/common/includes/javascript/timeline/timeline.js"></script>
<link rel="stylesheet" type="text/css" href="'.$base_path.'/cms/modules/common/includes/css/timeline/timeline.css"/>
<div id="frbr_timeline" style="height:'.$this->parameters->height.'px;"></div>';
	    $html.= "<script>
		var timeline = new TL.Timeline('frbr_timeline', ".encoding_normalize::json_encode($json).", {
            start_at_slide: ".$this->parameters->timeline_fields->start_at_slide.",
   			start_at_end : ".($this->parameters->timeline_fields->start_at_end == '1' ? 'true' : 'false')." ,
			language : 'fr',
			width: 800,
			height: 750
		});
			</script>";
	    return $html;
	}
	
	protected function get_JSON($infos){
	    $json = array();
	    $title = array(
	        'start_date' => array(
	        ),
	        'text' => array(
	            'headline'=> $this->cadre->get_name(),
	            'text' => $this->cadre->get_comment()
	        )
	    );
	    $events = $eras = array();
	    for($i=0 ; $i<count($infos) ; $i++){
	        $event = array();
	        if($infos[$i]['start_date']){
	            $infos[$i]['start_date'] = detectFormatDate($infos[$i]['start_date']);
	        }
	        if(isset($infos[$i]['end_date']) && $infos[$i]['end_date']){
	            $infos[$i]['end_date'] = detectFormatDate($infos[$i]['end_date']);
	        }
	        $event = array(
	            'start_date' => array(
	                'year' => date('Y',strtotime($infos[$i]['start_date'])),
	                'month' => date('m',strtotime($infos[$i]['start_date'])),
	                'day' =>date('d',strtotime($infos[$i]['start_date'])),
	            ),
	            'text' => array(
	                'headline' => $infos[$i]['title'],
	                'text' => (!empty($infos[$i]['resume']) ? $infos[$i]['resume'] : '')
	            )
	        );
	        
	        if(isset($infos[$i]['end_date']) && $infos[$i]['end_date']){
	            $event['end_date'] = array(
	                'year' => date('Y',strtotime($infos[$i]['end_date'])),
	                'month' => date('m',strtotime($infos[$i]['end_date'])),
	                'day' =>date('d',strtotime($infos[$i]['end_date'])),
	            );
	        }
	        if($infos[$i]['image']){
	            $event['media'] = array(
	                'url' => $infos[$i]['image'],
	                'thumbnail' => $infos[$i]['image']
	            );
	        }
	        $events[]= $event;
	    }
	    $json = array(
	        'title' => $title,
	        'events' => $events,
	        'eras' => $eras
	    );
	    return $json;
	}
	
	protected function get_cp_value($field_name, $entity_id){
	    $query = 'select idchamp from '.static::$prefix.'_custom where name = "'.$field_name.'"';
	    $result = pmb_mysql_query($query);
	    
	    $field_id = pmb_mysql_fetch_object($result)->idchamp;
	    
	    $pperso = new parametres_perso(static::$prefix);
	    return $pperso->get_val_field($entity_id, $field_name);
	}
	
 	public function get_form(){
 	    
 	    if(!isset($this->parameters)){
 	        $this->parameters->timeline_fields = new stdClass();
 	    }
 	    
 	    
 	    
 		$form.= "<hr/>";
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
		$form.="
					</div>
				</div>";
		return $form;
 	}
	
	public function save_form(){
		if(!isset($this->parameters->timeline_fields)){
			$this->parameters->timeline_fields = [];
		}
		$this->parameters->timeline_fields->title = $this->get_value_from_form('title');
		$this->parameters->timeline_fields->resume = $this->get_value_from_form('resume');
		$this->parameters->timeline_fields->start_date = $this->get_value_from_form('start_date');
		$this->parameters->timeline_fields->end_date = $this->get_value_from_form('end_date');		
		$this->parameters->timeline_fields->image = $this->get_value_from_form('image');
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