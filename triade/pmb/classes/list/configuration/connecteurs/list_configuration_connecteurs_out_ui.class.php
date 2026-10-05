<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_connecteurs_out_ui.class.php,v 1.8 2023/03/24 07:44:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_connecteurs_out_ui extends list_configuration_connecteurs_ui {
	
	protected function fetch_data() {
		global $base_path;
		
		$this->objects = array();
		$filename = $base_path."/admin/connecteurs/out/catalog.xml";
		$xml=file_get_contents($filename);
		$param=_parser_text_no_function_($xml,"CATALOG",$filename);
		foreach ($param["ITEM"] as $anitem) {
			$this->add_object($anitem);
		}
		$this->messages = "";
	}
	
	protected function get_object_instance($row) {
		return new connecteur_out($row["ID"], $row["PATH"]);
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
				'value' => $msg['connector_out_sourceadd'],
				'link' => static::get_controller_url_base().'&action=source_add&connector_id=!!id!!',
				'align' => $this->get_selected_setting_column('add_source', 'align')
		);
		$this->add_column_simple_action('add_source', '', $html_properties);
	}
	
	protected function get_display_content_sources_object_list($object, $indice) {
		global $charset;
		
		$display = "<tr class='".($indice % 2 ? 'odd' : 'even')."' style='display:none' id='".$object->path."'><td>&nbsp;</td><td colspan='3'><table style='border:1px solid'>";
		$parity_source=0;
		foreach ($object->sources as $asource) {
			$pair_impair_source = $parity_source++ % 2 ? "even" : "odd";
			$tr_javascript_source=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair_source'\" onmousedown=\"if (event) e=event; else e=window.event; if (e.srcElement) target=e.srcElement; else target=e.target; if (target.nodeName!='INPUT') document.location='".static::get_controller_url_base()."&action=source_edit&connector_id=".$object->id."&source_id=".$asource->id."';return false;\" ";
			$display .= "<tr style='cursor: pointer' class='$pair_impair_source' $tr_javascript_source>
				<td>".htmlentities($asource->name,ENT_QUOTES,$charset)."</td>
				<td>".htmlentities(substr($asource->comment,0,60),ENT_QUOTES,$charset)."</td>
				<td></td><td></td></tr>";
		}
		$display .= "</table></td></tr>";
		return $display;
	}
	
	protected function get_display_content_object_list($object, $indice) {
		global $charset;
		
		$sign=$object->name." : ".$object->comment." - ";
		$sign.="Auteur : ".$object->author." - ".$object->org." - ";
		$sign.=formatdate($object->date);
		
		$display = "
					<tr class='".($indice % 2 ? 'odd' : 'even')."' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".($indice % 2 ? 'odd' : 'even')."'\"
						title='".htmlentities($sign,ENT_QUOTES,$charset)."' alter='".htmlentities($sign,ENT_QUOTES,$charset)."' id='tr".$object->id."'>";
		foreach ($this->columns as $column) {
			if($column['html']) {
				if($column['property'] != 'expand' || ($column['property'] == 'expand' && count($object->sources))) {
					$display .= $this->get_display_cell_html_value($object, $column['html']);
				} else {
					$display .= "<td></td>";
				}
			} else {
				$display .= $this->get_display_cell($object, $column['property']);
			}
		}
		$display .= "</tr>";
		$display .= $this->get_display_content_sources_object_list($object, $indice);
		return $display;
	}
	
	protected function _get_object_property_service($object) {
		return $object->comment;
	}
	
	protected function _get_object_property_sources($object) {
		global $msg;
		
		return sprintf($msg["connecteurs_count_sources"],count($object->sources));
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		return array(
				'onclick' => "document.location=\"".$this->get_edition_link($object)."\""
		);
	}
	
	protected function get_display_cell_html_value($object, $value) {
		$value = str_replace('!!node_name!!', $object->path, $value);
		return parent::get_display_cell_html_value($object, $value);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=edit&id='.$object->id;
	}
	
	protected function get_button_add() {
		return "";
	}
}