<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_timeline_view_timeline.class.php,v 1.7.2.1.2.1 2025/02/12 12:34:07 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_timeline_view_timeline extends cms_module_common_view {


	public function __construct($id=0){
		parent::__construct($id);

		$this->default_template = "
<div>{{record.header}}</div>
<div>{{record.content}}</div>
";
	}


	public function get_form(){
		if(!isset($this->parameters['height'])) $this->parameters['height'] = '500';
		if(!isset($this->parameters['last_event'])) $this->parameters['last_event'] = 0;
		$last_event_checked = '';
		if ($this->parameters['last_event']) {
		    $last_event_checked = 'checked';
		}
		$form = parent::get_form();
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='".$this->get_form_value_name('height')."'>".$this->format_text($this->msg['cms_module_timeline_view_timeline_height'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='".$this->get_form_value_name('height')."' value='".$this->parameters['height']."'/>
				</div>
			</div>
            <div class='row'>
				<div class='colonne3'>
					<label for='".$this->get_form_value_name('last_event')."'>".$this->format_text($this->msg['cms_module_timeline_view_timeline_last_event'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='checkbox' name='".$this->get_form_value_name('last_event')."' value='1' $last_event_checked/>
				</div>
			</div>";
		return $form;
	}


	public function save_form(){
	    $this->parameters['height'] = intval($this->get_value_from_form('height'));
		if($this->parameters['height'] == 0){
			$this->parameters['height'] = 500;
		}
		if ($this->get_value_from_form('last_event')) {
    		$this->parameters['last_event'] = $this->get_value_from_form('last_event');
		} else {
    		$this->parameters['last_event'] = 0;
		}
		return parent::save_form();
	}


	public function get_headers($datas = array()){

		global $base_path;
		$headers = parent::get_headers($datas);
		$headers[]= "<script src='".$base_path."/cms/modules/common/includes/javascript/timeline/timeline.js'></script>";
		$headers[]= "<link rel='stylesheet' type='text/css' href='".$base_path."/cms/modules/common/includes/css/timeline/timeline.css'/>";
		return $headers;
	}


	public function render($datas){

		$json = $this->get_JSON($datas['items']);

		if(is_countable($json['events']) && count($json['events']) == 0){
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
		$html = '<div id="'.$this->get_module_dom_id().'_timeline" style="height:'.$this->parameters['height'].'px;"></div>';
		$html.= "<script>
		var timeline = new TL.Timeline('".$this->get_module_dom_id()."_timeline', ".encoding_normalize::json_encode($json).", {
			start_at_end : ".($this->parameters['last_event']? 'true': 'false' ) .",
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
				'text' => "TIMELINE"
			)
		);
		$events = $eras = array();
		if (is_countable($infos)) {
    		for($i=0 ; $i<count($infos) ; $i++){
    			$event = array();
    			if($infos[$i]['start_date']){
    				$infos[$i]['start_date'] = detectFormatDate($infos[$i]['start_date']);
    			}
    			if($infos[$i]['end_date']){
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

    			if($infos[$i]['end_date']){
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
		}
		$json = array(
			'title' => $title,
			'events' => $events,
			'eras' => $eras
		);
		return $json;
	}


	public function get_format_data_structure(){
		return array_merge(array(
			array(
				'var' => "record",
				'desc'=> "",
				'children' => array(
					array(
						'var' => "record.header",
						'desc'=> $this->msg['cms_module_common_view_record_header_desc']
					),
					array(
						'var' => "record.content",
						'desc'=> $this->msg['cms_module_common_view_record_content_desc']
					),
					array(
						'var' => "record.link",
						'desc'=> $this->msg['cms_module_common_view_record_link_desc']
					)
				)
			)
		),parent::get_format_data_structure());
	}
}
