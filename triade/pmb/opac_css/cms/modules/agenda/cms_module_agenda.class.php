<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_agenda.class.php,v 1.9.2.1.2.1 2025/02/12 12:34:07 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_agenda extends cms_module_common_module {

	public function __construct($id=0){
		$this->module_path = str_replace(basename(__FILE__),"",__FILE__);
		parent::__construct($id);
	}

	public function get_manage_form(){
		global $base_path;
		//variables persos...
		global $calendar;

		$form="
		<h3>".$this->format_text($this->msg['cms_module_agenda_manage_title'])."</h3>
		<div dojoType='dijit.layout.BorderContainer' style='width: 100%; height: 800px;'>
			<div dojoType='dijit.layout.ContentPane' region='left' splitter='true' style='width:300px;' >";
		if($this->managed_datas['module']['calendars']){
			foreach($this->managed_datas['module']['calendars'] as $key => $cal){
				$form.="
					<p>
						<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->class_name)."&quoi=module&calendar=".$key."&action=get_form'>".$this->format_text($cal['name'])."</a>
					&nbsp;
						<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->class_name)."&quoi=module&calendar_delete=".$key."&action=save_form' onclick='return confirm(\"".$this->format_text($this->msg['cms_module_agenda_delete_calendar'])."\")'>
							<img src='".get_url_icon('trash.png')."' alt='".$this->format_text($this->msg['cms_module_root_delete'])."' title='".$this->format_text($this->msg['cms_module_root_delete'])."'/>
						</a>
					</p>";
			}
		}
			$form.="
				<a href='".$base_path."/cms.php?categ=manage&sub=".str_replace("cms_module_","",$this->class_name)."&quoi=module&calendar=new'>".$this->format_text($this->msg['cms_module_agenda_add_calendar'])."</a>
			";
		$form.="
			</div>
			<div dojoType='dijit.layout.ContentPane' region='center' >";
		if($calendar){
			$form.=$this->get_managed_form_start(array('calendar'=>$calendar));
			$form.=$this->get_managed_calendar_form($calendar);
			$form.=$this->get_managed_form_end();
		}
		$form.="
			</div>
		</div>";
		return $form;
	}

	protected function get_managed_calendar_form($calendar){
		if($calendar != "new"){
			$infos = $this->managed_datas['module']['calendars'][$calendar];
		}else{
			$infos = array(
				'name' => "",
				'color' => "",
				'type' => ""
			);
		}
		$form = "";

		//nom
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_agenda_calendar_name'>".$this->format_text($this->msg['cms_module_agenda_calendar_name'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_agenda_calendar_name' value='".$this->format_text($infos['name'])."'/>
				</div>
			</div>";
		//couleur
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_agenda_calendar_color'>".$this->format_text($this->msg['cms_module_agenda_calendar_color'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_agenda_calendar_color' value='".$this->format_text($infos['color'])."'/>
				</div>
			</div>";
		//type de contenu à prendre en compte...
		$form.="
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_agenda_calendar_type'>".$this->format_text($this->msg['cms_module_agenda_calendar_type'])."</label>
				</div>
				<div class='colonne-suite'>
					<select name='cms_module_agenda_calendar_type' onchange='load_date_form(this.value)'>
						<option value='0' ".(!$infos['type'] ? "selected='selected'" : "").">".$this->format_text($this->msg['cms_module_agenda_type_choice'])."</option>";
		$query = "select id_editorial_type, editorial_type_label from cms_editorial_types where editorial_type_element = 'article' order by 2 asc";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row =  pmb_mysql_fetch_object($result)){
				$form.="
						<option value='".$this->format_text($row->id_editorial_type)."'  ".($infos['type'] == $row->id_editorial_type ? "selected='selected'" : "").">".$this->format_text($row->editorial_type_label)."</option>";
			}
		}
		$form.="
			</select>
			<script type='text/javascript'>";
		if($infos['type']) $form.="
				load_date_form(".$infos['type'].");";
		$form.="
				function load_date_form(id_type){
					dojo.xhrGet({
						url : '".$this->get_ajax_link(array('calendar'=>$calendar))."&id_type='+id_type,
						handelAs : 'text/html',
						load : function(data){
							dojo.byId('cms_module_agenda_dates_form').innerHTML = data;
						}
					});
				}
			</script>";
		//date évènement
		$form.="
			<div class='row' id='cms_module_agenda_dates_form'>
			</div>";


		return $form;
	}

	public function save_manage_form(){
		global $calendar;
		global $calendar_delete;
		global $cms_module_agenda_calendar_name;
		global $cms_module_agenda_calendar_color;
		global $cms_module_agenda_calendar_type;
		global $cms_module_agenda_calendar_start_date;
		global $cms_module_agenda_calendar_end_date;


		$params = $this->managed_datas['module'];


		if($calendar_delete){
			unset($params['calendars'][$calendar_delete]);
		}else{
			if($calendar == "new"){
				$calendar = "calendar".(cms_module_agenda::get_max_calendar_id($params['calendars'])+1);
			}
			$params['calendars'][$calendar] = array(
					'name' => stripslashes($cms_module_agenda_calendar_name),
					'color' => stripslashes($cms_module_agenda_calendar_color),
					'type' => stripslashes($cms_module_agenda_calendar_type),
					'start_date' => stripslashes($cms_module_agenda_calendar_start_date),
					'end_date' => stripslashes($cms_module_agenda_calendar_end_date)
			);
		}
		return $params;
	}

	protected static function get_max_calendar_id($datas){
		$max = 0;
		if(is_countable($datas) && count($datas)){
			foreach	($datas as $key => $val){
			    $key = intval(str_replace("calendar","",$key));
				if($key>$max) $max = $key;
			}
		}
		return $max;
	}

	public function execute_ajax(){
		global $calendar,$id_type;
		$response = array();
		$fields = new cms_editorial_parametres_perso($id_type);
		$select="
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_agenda_calendar_start_date'>".$this->format_text($this->msg['cms_module_agenda_calendar_start_date'])."</label>
			</div>
			<div class='colonne-suite'>
				<select name='cms_module_agenda_calendar_start_date' >";
		$select.= $fields->get_selector_options($this->managed_datas['module']['calendars'][$calendar]['start_date']);
		$select.= "
				</select>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_agenda_calendar_end_date'>".$this->format_text($this->msg['cms_module_agenda_calendar_end_date'])."</label>
			</div>
			<div class='colonne-suite'>
				<select name='cms_module_agenda_calendar_end_date' >";
		$select.= $fields->get_selector_options($this->managed_datas['module']['calendars'][$calendar]['end_date']);
		$select.= "
				</select>
			</div>
		</div>";
		$response['content'] = $select;
		$response['content-type'] = 'text/html';

		return $response;
	}
}