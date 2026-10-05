<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_planificateur_manager_ui.class.php,v 1.5 2023/05/26 14:05:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_planificateur_manager_ui extends list_configuration_planificateur_ui {
	
	protected function fetch_data() {
		$this->objects = array();
		
		scheduler_tasks::parse_catalog();
		foreach (scheduler_tasks::$xml_catalog["ACTION"] as $anitem) {
			$scheduler_tasks_type = new scheduler_tasks_type($anitem['ID']);
			$scheduler_tasks_type->set_name($anitem['NAME']);
			$scheduler_tasks_type->set_path($anitem['PATH']);
			$scheduler_tasks_type->set_comment($anitem['COMMENT']);
			$this->add_object($scheduler_tasks_type);
		}
		$this->messages = "";
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'comment' => 'planificateur_type_task',
				'number' => 'planificateur_task',
		);
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('number', 'align', 'center');
	}
	
	protected function init_default_columns() {
		$this->add_column_expand();
		parent::init_default_columns();
		$this->add_column_add_task();
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'expand', 'comment', 'number', 'add_task',
		);
	}
	
	protected function add_column_expand() {
		$this->columns[] = array(
				'property' => 'expand',
				'label' => '',
				'html' => "<img src='".get_url_icon('plus.gif')."' class='img_plus' onClick='if (event) e=event; else e=window.event; e.cancelBubble=true; if (e.stopPropagation) e.stopPropagation(); show_taches(\"".addslashes('!!node_name!!')."\"); ' style='cursor:pointer;'/>",
				'exportable' => false
		);
	}
	
	protected function add_column_add_task() {
		global $msg;
		
		$html_properties = array(
				'value' => $msg["planificateur_task_add"],
				'link' => static::get_controller_url_base().'&action=edit&type_id=!!id!!',
				'align' => 'right'
		);
		$this->add_column_simple_action('add_task', '', $html_properties);
	}
	
	protected function get_display_content_tasks_object_list($object, $indice) {
		$display = "<tr class='".($indice % 2 ? 'odd' : 'even')."' style='display:none' id='".$object->get_name()."'><td>&nbsp;</td><td colspan='3' style='border:1px solid'>";
		$display .= list_scheduler_planning_ui::get_instance(array('type' => $object->get_id()))->get_display_list();
		$display .= "</td></tr>";
		return $display;
	}
		
	protected function get_display_content_object_list($object, $indice) {
		global $charset;
		
		$display = "
					<tr class='".($indice % 2 ? 'odd' : 'even')."' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".($indice % 2 ? 'odd' : 'even')."'\" 
						title='".htmlentities($object->get_comment(),ENT_QUOTES,$charset)."' alter='".htmlentities($object->get_comment(),ENT_QUOTES,$charset)."' id='tr".$object->get_id()."'>";
		foreach ($this->columns as $column) {
			if($column['html']) {
				if($column['property'] != 'expand' || ($column['property'] == 'expand' && $object->get_number())) {
					$display .= $this->get_display_cell_html_value($object, $column['html']);
				} else {
					$display .= "<td></td>";
				}
			} else {
				$display .= $this->get_display_cell($object, $column['property']);
			}
		}
		$display .= "</tr>";
		if ($object->get_number()) {
			$display .= $this->get_display_content_tasks_object_list($object, $indice);
		}
		return $display;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		
		$content = '';
		switch($property) {
			case 'number':
				$content .= $object->get_number()." ".$msg["planificateur_count_tasks"];
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		return array(
				'onclick' => "document.location=\"".$this->get_edition_link($object)."\""
		);
	}
	
	protected function get_display_cell_html_value($object, $value) {
		$value = str_replace('!!node_name!!', $object->get_name(), $value);
		return parent::get_display_cell_html_value($object, $value);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=type_edit&id='.$object->get_id();
	}
	
	protected function get_button_add() {
		return "";
	}
	
	public function get_display_list() {
		$display = "
		<script type='text/javascript' >
			function show_taches(id) {
				if (document.getElementById(id).style.display=='none') {
					document.getElementById(id).style.display='';
				} else {
					document.getElementById(id).style.display='none';
				}
			}
		</script>";
		$display .= parent::get_display_list();
		return $display;
	}
}