<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_opac_maintenance_planning.class.php,v 1.1 2019/10/09 09:54:41 dgoron Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_planning.class.php");

class scheduler_opac_maintenance_planning extends scheduler_planning {
	
	//formulaire spécifique au type de tâche
	public function show_form ($param=array()) {
        global $charset;
        
	    if(!isset($param['opac_maintenance_default_page'])) $param['opac_maintenance_default_page'] = 1;
	    if(!isset($param['opac_maintenance_duration'])) $param['opac_maintenance_duration'] = 60;
	    if(!isset($param['opac_maintenance_title'])) $param['opac_maintenance_title'] = '';
	    if(!isset($param['opac_maintenance_content'])) $param['opac_maintenance_content'] = '';
	    if(!isset($param['opac_maintenance_css_style'])) $param['opac_maintenance_css_style'] = '';
	    
		$form = "
		<div class='row'>
			<div class='colonne3'>
				<label>".$this->msg["scheduler_opac_maintenance_default_page"]."</label>
			</div>
			<div class='colonne_suite'>
                <input type='checkbox' id='scheduler_opac_maintenance_default_page' name='scheduler_opac_maintenance_default_page' class='switch' value='1' ".($param['opac_maintenance_default_page'] ? "checked='checked'" : "").">
                <label for='scheduler_opac_maintenance_default_page'>&nbsp;</label>
			</div>
		</div>
        <div class='row'>
			<div class='colonne3'>
				<label>".$this->msg["scheduler_opac_maintenance_duration"]."</label>
			</div>
			<div class='colonne_suite'>
                <input type='number' id='scheduler_opac_maintenance_duration' name='scheduler_opac_maintenance_duration' value='".htmlentities($param['opac_maintenance_duration'], ENT_QUOTES, $charset)."' />
			</div>
		</div>
        <div class='row'>&nbsp;</div>
        <div class='row'>
            <div class='colonne3'>
                &nbsp;
            </div>
            <div class='colonne_suite'>
                <h3>".$this->msg["scheduler_opac_maintenance_customize"]."</h3>
            </div>
		</div>
        <div class='row'>&nbsp;</div>
        <div class='row'>
			<div class='colonne3'>
				<label>".$this->msg["scheduler_opac_maintenance_title"]."</label>
			</div>
			<div class='colonne_suite'>
                <input type='text' name='scheduler_opac_maintenance_title' value='".htmlentities($param['opac_maintenance_title'], ENT_QUOTES, $charset)."'/>
			</div>
		</div>
        <div class='row'>
			<div class='colonne3'>
				<label>".$this->msg["scheduler_opac_maintenance_content"]."</label>
			</div>
			<div class='colonne_suite'>
                <textarea id='scheduler_opac_maintenance_content' name='scheduler_opac_maintenance_content' cols='120' rows='40'>".htmlentities($param['opac_maintenance_content'], ENT_QUOTES, $charset)."</textarea>
			</div>
		</div>
        <div class='row'>
			<div class='colonne3'>
				<label>".$this->msg["scheduler_opac_maintenance_css_style"]."</label>
			</div>
			<div class='colonne_suite'>
                <textarea id='scheduler_opac_maintenance_css_style' name='scheduler_opac_maintenance_css_style' cols='120' rows='20'>".htmlentities($param['opac_maintenance_css_style'], ENT_QUOTES, $charset)."</textarea>
			</div>
		</div>
		<div class='row'>&nbsp;</div>";
		
		return $form;
	}

	public function make_serialized_task_params() {
	    global $scheduler_opac_maintenance_default_page;
	    global $scheduler_opac_maintenance_duration;
	    global $scheduler_opac_maintenance_title;
	    global $scheduler_opac_maintenance_content;
	    global $scheduler_opac_maintenance_css_style;
	    
		$t = parent::make_serialized_task_params();
		
		$t['opac_maintenance_default_page'] = (int) $scheduler_opac_maintenance_default_page;
		$t['opac_maintenance_duration'] = (int) $scheduler_opac_maintenance_duration;
		$t['opac_maintenance_title'] = stripslashes($scheduler_opac_maintenance_title);
		$t['opac_maintenance_content'] = stripslashes($scheduler_opac_maintenance_content);
		$t['opac_maintenance_css_style'] = stripslashes($scheduler_opac_maintenance_css_style);
    	return serialize($t);
	}
}