<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_connecteurs_in_ui.class.php,v 1.8.4.1 2025/02/20 09:18:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_connecteurs_in_ui extends list_configuration_connecteurs_ui {
	
	protected $connector_instances;
	
	protected function fetch_data() {
		$this->objects = array();
		
		$contrs=new connecteurs();
		foreach ($contrs->catalog as $id=>$prop) {
			if(empty($prop["ID"])) {
				$prop["ID"] = $id;
			}
			$prop = array_change_key_case($prop);
			$this->add_object((object) $prop);
		}
		$this->messages = "";
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'service' => 'connector_out_service',
				'sources' => 'connector_out_sources',
		);
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('sources', 'align', 'center');
		$this->set_setting_column('add_source', 'align', 'right');
	}
	
	protected function init_default_columns() {
		$this->add_column_expand();
		parent::init_default_columns();
		$this->add_column_add_source();
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'expand', 'service', 'sources', 'add_source',
		);
	}
	
	protected function add_column_add_source() {
		global $msg;
		
		$html_properties = array(
				'value' => $msg['connecteurs_add_source'],
				'link' => static::get_controller_url_base().'&act=add_source&id=!!id!!',
				'align' => $this->get_selected_setting_column('add_source', 'align')
		);
		$this->add_column_simple_action('add_source', '', $html_properties);
	}
	
	protected function get_display_content_sources_object_list($object, $indice) {
		global $msg, $charset;
		
		$display = "<tr class='".($indice % 2 ? 'odd' : 'even')."' style='display:none' id='".$object->name."'><td>&nbsp;</td><td colspan='3'><table style='border:1px solid'>";
		$parity_source = 0;
		$conn = $this->get_connector_instance($object);
		foreach($conn->sources as $source_id=>$s) {
			if ($parity_source % 2) {
				$pair_impair_source = "even";
			} else {
				$pair_impair_source = "odd";
			}
			$parity_source += 1;
			$tr_javascript_source=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair_source'\" onmousedown=\"if (event) e=event; else e=window.event; if (e.srcElement) target=e.srcElement; else target=e.target; if (target.nodeName!='INPUT') document.location='".static::get_controller_url_base()."&act=add_source&id=".$object->id."&source_id=".$s["SOURCE_ID"]."';\" ";
			$display .= "<tr style='cursor: pointer' class='$pair_impair_source' $tr_javascript_source>
					<td>".htmlentities($s["NAME"],ENT_QUOTES,$charset)."</td>
					<td>".htmlentities(substr($s["COMMENT"],0,60),ENT_QUOTES,$charset)."</td>
					<td>";
			if (($s["REPOSITORY"]==1)||($s["REPOSITORY"]==2)) {
				$requete="select count(distinct recid) from entrepot_source_".$source_id." where 1";
				$rnn=pmb_mysql_query($requete);
				$scount = pmb_mysql_result($rnn,0,0); //$counts[$source_id]; //)
				if (!$scount) $scount = 0;
				$display .= "<td>".sprintf($msg["connecteurs_count_notices"],$scount)."</td>";
			}
			if ($s["REPOSITORY"]==1) {
				$display .= "<td>";
				if ($s["CANCELLED"]) {
					$display .= $this->get_interface_button_small($msg["connecteurs_sync_resume"], ['location' => static::get_controller_url_base()."&act=sync&go=1&source_id=".$s["SOURCE_ID"]."&id=".$object->id])."&nbsp;";
					$display .= $this->get_interface_button_small($msg["connecteurs_sync_cancel"], ['location' => static::get_controller_url_base()."&act=cancel_sync&source_id=".$s["SOURCE_ID"]."&id=".$object->id]);
				}
				else if ($s["DATESYNC"]) {
					$display .= sprintf($msg["connecteurs_sync_exists_menu"],$s["PERCENT"]);
					$display .= "&nbsp;".$this->get_interface_button_small($msg["connecteurs_sync_abort"], ['location' => static::get_controller_url_base()."&act=abort_sync&source_id=".$s["SOURCE_ID"]."&id=".$object->id]);
				}
				else {
					$display .= $this->get_interface_button_small($msg["connecteurs_sync"], ['location' => static::get_controller_url_base()."&act=sync&source_id=".$s["SOURCE_ID"]."&id=".$object->id]);
					$display .= $s["LASTSYNCDATE"] != 0 ? "&nbsp;&nbsp;(".sprintf($msg["connecteurs_sync_lastdate"], format_date($s["LASTSYNCDATE"]), 1).")" : "";
				}
				$display .= "</td>";
				
				$display .= "<td>";
				$display .= $this->get_interface_button_small($msg["connecteurs_empty"], ['location' => static::get_controller_url_base()."&act=empty&source_id=".$s["SOURCE_ID"]."&id=".$object->id, 'confirm_msg' => $msg["connecteurs_del_notice_confirm"]]);
				$display .= "</td>";
			} else {
				$display .= "<td>&nbsp;</td><td>&nbsp;</td>";
			}
			$display .= "</tr>";
		}
		$display .= "</table></td></tr>";
		return $display;
	}
	
	protected function get_connector_instance($object) {
		global $base_path;
		
		if(!isset($this->connector_instances[$object->id])) {
			if (is_file($base_path."/admin/connecteurs/in/".$object->path."/".$object->name.".class.php")) {
				require_once($base_path."/admin/connecteurs/in/".$object->path."/".$object->name.".class.php");
				eval("\$conn=new ".$object->name."(\"".$base_path."/admin/connecteurs/in/".$object->path."\");");
				$conn->get_sources();
				$this->connector_instances[$object->id] = $conn;
			}
		}
		return $this->connector_instances[$object->id];
	}
	
	protected function get_n_sources($object) {
		global $base_path;
		
		//Recherche du nombre de sources
		$n_sources=0;
		if (is_file($base_path."/admin/connecteurs/in/".$object->path."/".$object->name.".class.php")) {
			$conn = $this->get_connector_instance($object);
			$n_sources=count($conn->sources);
		}
		return $n_sources;
	}
	
	protected function get_display_content_object_list($object, $indice) {
		global $charset;
		
		$sign=$object->name." : ".$object->comment." - ";
		if ($object->status!="open") $sign.="(c) ";
		$sign.="Auteur : ".$object->author." - ".$object->org." - ";
		$sign.=formatdate($object->date);
		
		$display = "
					<tr class='".($indice % 2 ? 'odd' : 'even')."' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".($indice % 2 ? 'odd' : 'even')."'\" 
						title='".htmlentities($sign,ENT_QUOTES,$charset)."' alter='".htmlentities($sign,ENT_QUOTES,$charset)."' id='tr".$object->id."'>";
		foreach ($this->columns as $column) {
			if($column['html']) {
				if($column['property'] != 'expand' || ($column['property'] == 'expand' && $this->get_n_sources($object))) {
					$display .= $this->get_display_cell_html_value($object, $column['html']);
				} else {
					$display .= "<td></td>";
				}
			} else {
				$display .= $this->get_display_cell($object, $column['property']);
			}
		}
		$display .= "</tr>";
		if ($this->get_n_sources($object)) {
			$display .= $this->get_display_content_sources_object_list($object, $indice);
		}
		return $display;
	}
	
	protected function _get_object_property_service($object) {
		return $object->comment;
	}
	
	protected function _get_object_property_sources($object) {
		global $msg;
		
		$n_sources=$this->get_n_sources($object);
		return sprintf($msg["connecteurs_count_sources"],$n_sources);
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		return array(
				'onclick' => "document.location=\"".$this->get_edition_link($object)."\""
		);
	}
	
	protected function get_display_cell_html_value($object, $value) {
		$value = str_replace('!!node_name!!', $object->name, $value);
		return parent::get_display_cell_html_value($object, $value);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&act=modif&id='.$object->id;
	}
	
	protected function get_button_add() {
		return "";
	}
}