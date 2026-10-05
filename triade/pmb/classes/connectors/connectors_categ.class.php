<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: connectors_categ.class.php,v 1.2 2023/07/07 07:25:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class connectors_categ {

	/* ---------------------------------------------------------------
		propriétés de la classe
   --------------------------------------------------------------- */

	public $id=0;
	public $name='';
	public $opac_expanded=false;

	public function __construct($id=0) {
		$this->id = intval($id);
		$this->getData();
	}

	/* ---------------------------------------------------------------
		getData() : récupération des propriétés
   --------------------------------------------------------------- */
	public function getData() {
		if(!$this->id) return;
	
		$query = 'SELECT * FROM connectors_categ WHERE connectors_categ_id='.$this->id;
		$result = pmb_mysql_query($query);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		$data = pmb_mysql_fetch_object($result);
		$this->name = $data->connectors_categ_name;
		$this->opac_expanded = $data->opac_expanded;
	}

	public function get_content_form() {
		global $msg, $charset;
		
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('categ_name', 'connecteurs_categ_caption')
		->add_input_node('text', $this->name);
		$interface_content_form->add_element('categ_opac_expanded', 'connecteurs_categ_opac_expanded')
		->add_input_node('boolean', $this->opac_expanded);
		
		$sources = array();
		$sources_sql = 'SELECT connectors_sources.source_id, connectors_sources.name, connectors_categ_sources.num_categ, id_connector
		FROM connectors_sources LEFT JOIN connectors_categ_sources ON (connectors_sources.source_id = connectors_categ_sources.num_source AND connectors_categ_sources.num_categ='.$this->id.')
		order by connectors_sources.id_connector, connectors_sources.name';
		$resultat = pmb_mysql_query($sources_sql);
		while ($row=pmb_mysql_fetch_object($resultat)) {
			$sources[] = $row;
		}
		$nbsources=count($sources);
		$content_input = '<select MULTIPLE name="categ_content[]" size="'.($nbsources+4).'">';
		if (!$nbsources) {
			$content_input .= '<option value="">'.($msg["connecteurs_categories_none"]).'</option>';
		}
		$idconnectorconserve="";
		foreach ($sources as $source) {
			if ($source->id_connector!=$idconnectorconserve) {
				$idconnectorconserve=$source->id_connector;
				$content_input .= '<optgroup label="'.$idconnectorconserve.'" class="erreur">';
			}
			$content_input .= '<option value="'.$source->source_id.'" '.($source->num_categ ? 'SELECTED' : '').' style="color: rgb(0, 0, 0);">'.htmlentities($source->name ,ENT_QUOTES, $charset).'</option>';
		}
		$content_input .= '</select>';
		$interface_content_form->add_element('categ_content', 'connecteurs_included_sources')
		->add_html_node($content_input);
		
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
			
		$interface_form = new interface_admin_form('form_categ');
		if(!$this->id){
			$interface_form->set_label($msg['connecteurs_categ_add']);
		}else{
			$interface_form->set_label($msg['connecteurs_categ_edit']);
		}
		
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->name." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('connectors_categ')
		->set_field_focus('categ_name');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $categ_name, $categ_opac_expanded;
		
		$this->name = stripslashes($categ_name);
		$this->opac_expanded = ($categ_opac_expanded ? 1 : 0);
	}
	
	public function save() {
		global $categ_content;
		
		//Mettons a jours la catégorie
		if ($this->id == 0) {
			$sql = "INSERT INTO connectors_categ (connectors_categ_name, opac_expanded) VALUES ('".addslashes($this->name)."', ".$this->opac_expanded.");";
			pmb_mysql_query($sql);
			$this->id = pmb_mysql_insert_id();
		} else {
			$sql = "UPDATE connectors_categ SET connectors_categ_name = '".addslashes($this->name)."', opac_expanded = ".$this->opac_expanded." WHERE connectors_categ_id = ".$this->id;
			pmb_mysql_query($sql);
		}
		
		$sql = "DELETE FROM connectors_categ_sources WHERE num_categ = ".$this->id;
		pmb_mysql_query($sql);
		if (!empty($categ_content)) {
			$values = array();
			foreach($categ_content as $asource_id) {
				$values[] = "(".$this->id.", ".addslashes($asource_id).")";
			}
			$values = implode(",", $values);
			$sql = "INSERT INTO connectors_categ_sources (num_categ, num_source) VALUES ".$values;
			pmb_mysql_query($sql);
		}
	}

	public static function check_data_from_form() {
		global $categ_name;
		
		if(empty($categ_name)) {
			return false;
		}
		return true;
	}
	
	public static function delete($id) {
		$id = intval($id);
		if ($id) {
			$sql = "DELETE FROM connectors_categ WHERE connectors_categ_id=".$id;
			pmb_mysql_query($sql);
			$sql = "DELETE FROM connectors_categ_sources WHERE num_categ = ".$id;
			pmb_mysql_query($sql);
		}
		return true;
	}
} /* fin de définition de la classe */


