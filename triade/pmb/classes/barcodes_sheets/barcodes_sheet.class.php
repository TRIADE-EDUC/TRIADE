<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: barcodes_sheet.class.php,v 1.6.4.1 2025/02/12 12:34:07 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/templates/barcodes_sheets/barcodes_sheet.tpl.php");
require_once($class_path."/encoding_normalize.class.php");

/**
 * Planche de codes-barres
 */
class barcodes_sheet {

	/**
	 * Identifiant
	 * @var int
	 */
	protected $id;

	/**
	 * Libellé
	 * @var string
	 */
	protected $label;

	/**
	 * Format (ex : A4)
	 * @var string
	 */
	protected $page_format;

	/**
	 * Portrait / Paysage
	 * @var string
	 */
	protected $page_orientation;

	/**
	 * Unité
	 * @var float
	 */
	protected $unit;

	/**
	 * Nombre de codes-barres en largeur
	 * @var int
	 */
	protected $CBG_NBR_X_CELLS;

	/**
	 * Nombre de codes-barres en hauteur
	 * @var int
	 */
	protected $CBG_NBR_Y_CELLS;

	/**
	 * Marge de gauche
	 * @var float
	 */
	protected $CBG_LEFT_MARGIN;

	/**
	 * Marge de droite
	 * @var float
	 */
	protected $CBG_RIGHT_MARGIN;

	/**
	 * Marge du haut
	 * @var float
	 */
	protected $CBG_TOP_MARGIN;

	/**
	 * Marge du bas
	 * @var float
	 */
	protected $CBG_BOTTOM_MARGIN;

	/**
	 * Marge intérieure de gauche
	 * @var float
	 */
	protected $CBG_INNER_LEFT_MARGIN;

	/**
	 * Marge intérieure de droite
	 * @var float
	 */
	protected $CBG_INNER_RIGHT_MARGIN;

	/**
	 * Marge intérieure du haut
	 * @var float
	 */
	protected $CBG_INNER_TOP_MARGIN;

	/**
	 * Marge intérieure du bas
	 * @var float
	 */
	protected $CBG_INNER_BOTTOM_MARGIN;

	protected $CBG_TEXT_HEIGHT;

	protected $CBG_TEXT_FONT_SIZE;

	protected $CBG_CB_TEXT_SIZE;

	protected $CBG_CB_RES;

	/**
	 * Numéro d'ordre
	 */
	protected $order;

	/**
	 * Tailles du format de la page
	 */
	protected $page_sizes;

	public function __construct($id=0) {
		$this->id = intval($id);
		$this->fetch_data();
	}

	protected function fetch_data() {
// 		$this->label = 'AVERY 65';
		$this->page_format = 'A4';
		$this->page_orientation = 'P';
		$this->unit = 'mm';
		$this->CBG_NBR_X_CELLS = 4;
		$this->CBG_NBR_Y_CELLS = 19;

		// marges, mesures en mm
		$this->CBG_LEFT_MARGIN = 6;
		$this->CBG_RIGHT_MARGIN = 6;
		$this->CBG_TOP_MARGIN = 13;
		$this->CBG_BOTTOM_MARGIN = 13;

		// marges intérieures du bord de l'étiquette au code barre, mesures en mm
		$this->CBG_INNER_LEFT_MARGIN = 4;
		$this->CBG_INNER_RIGHT_MARGIN = 4;
		$this->CBG_INNER_TOP_MARGIN = 1;
		$this->CBG_INNER_BOTTOM_MARGIN = 1;

		// place allouée au nom de la bibliothèque, mesure en mm
		$this->CBG_TEXT_HEIGHT = 2;
		// Taille de la police, en points
		$this->CBG_TEXT_FONT_SIZE = 6;
		// Taille du texte du code-barre, 1 : le plus petit ; 5 : le plus grand
		$this->CBG_CB_TEXT_SIZE = 3;
		// Résolution du code barre. Si vous augmentez ce paramètre, il faudra peut-être
		// augmenter la taille de la police. Une valeur faible produit un fichier moins volumineux
		$this->CBG_CB_RES = 1;
		$this->order = 0;
		$this->page_sizes = array('210','297');
		if($this->id) {
			$query = "select * from barcodes_sheets where id_barcodes_sheet = ".$this->id;
			$result = pmb_mysql_query($query);
			$row = pmb_mysql_fetch_object($result);
			$this->label = $row->barcodes_sheet_label;
			$this->set_data(json_decode($row->barcodes_sheet_data, true));
			$this->order = $row->barcodes_sheet_order;
			$this->set_page_sizes();
		}
	}

	protected function set_data($data) {
		if (is_array($data)) {
			foreach ($data as $property=>$value) {
				if(property_exists($this, $property)) {
					$this->{$property} = $value;
				}
			}
		}
	}

	protected function set_page_sizes() {
		switch ($this->page_format) {
			case 'A3':
				$this->page_sizes = array('297','420');
				break;
			case 'A4':
				$this->page_sizes = array('210','297');
				break;
			case 'A5':
				$this->page_sizes = array('148','210');
				break;
			case 'Letter':
				$this->page_sizes = array('215.9','279.4');
				break;
			case 'Legal':
				$this->page_sizes = array('355.6','216');
				break;
		}
		if($this->page_orientation == 'L') {
			$this->page_sizes = array_reverse($this->page_sizes);
		}
	}

	protected function gen_selector_page_format() {
		global $charset;
		$selector = '';
		$page_size=array("A3","A4","A5","Letter","Legal");
		foreach ($page_size as $size) {
			$selector .="<option value='".$size."' ".($this->page_format == $size ? "selected='selected'" : "").">".htmlentities($size, ENT_QUOTES, $charset)."</option>";
		}
		return $selector;
	}

	protected function gen_selector_page_orientation() {
		global $msg, $charset;
		$selector = '';
		$page_orientation=array('P' => $msg['edit_cbgen_mep_portrait'], 'L' => $msg['edit_cbgen_mep_paysage']);
		foreach ($page_orientation as $key=>$orientation) {
			$selector .="<option value='".$key."' ".($this->page_orientation == $key ? "selected='selected'" : "").">".htmlentities($orientation, ENT_QUOTES, $charset)."</option>";
		}
		return $selector;
	}

	protected function get_display_line_margin_parameter($name) {
		global $msg, $charset;

		$position = strtolower(str_replace(array('CBG_', 'INNER_', '_MARGIN'), '', $name));
		$display = "
		<div class='row'>
			<input class='saisie-20em' id='".$name."' type='text' class='text' name='".$name."' value=\"".htmlentities($this->{$name}, ENT_QUOTES, $charset)."\" /> ".$msg['edit_cbgen_mep_'.$position]."
		</div>
		";
		return $display;
	}

	protected function get_display_margins() {
		global $msg, $charset;

		$display = "<label class='etiquette'>".htmlentities($msg['edit_cbgen_mep_margin'], ENT_QUOTES, $charset)."</label><br />";
		$display.=$this->get_display_line_margin_parameter('CBG_LEFT_MARGIN');
		$display.=$this->get_display_line_margin_parameter('CBG_RIGHT_MARGIN');
		$display.=$this->get_display_line_margin_parameter('CBG_TOP_MARGIN');
		$display.=$this->get_display_line_margin_parameter('CBG_BOTTOM_MARGIN');
		return $display;
	}

	protected function get_display_inner_margins() {
		global $msg, $charset;

		$display = "<label class='etiquette'>".htmlentities($msg['edit_cbgen_mep_inner_margin'], ENT_QUOTES, $charset)."</label><br />";
		$display.=$this->get_display_line_margin_parameter('CBG_INNER_LEFT_MARGIN');
		$display.=$this->get_display_line_margin_parameter('CBG_INNER_RIGHT_MARGIN');
		$display.=$this->get_display_line_margin_parameter('CBG_INNER_TOP_MARGIN');
		$display.=$this->get_display_line_margin_parameter('CBG_INNER_BOTTOM_MARGIN');
		return $display;
	}

	protected function get_display_line_size_parameter($name) {
		global $charset;

		$display = "
		<div class='row'>
			<input class='saisie-20em' id='".$name."' type='text' class='text' name='".$name."' value=\"".htmlentities($this->{$name}, ENT_QUOTES, $charset)."\" />
		</div>";
		return $display;
	}

	protected function get_display_text_sizes() {
		global $msg;

		$display = "<label class='etiquette'>".$msg['edit_cbgen_mep_text_height']."</label><br />";
		$display .= $this->get_display_line_size_parameter('CBG_TEXT_HEIGHT');
		$display .= "<label class='etiquette'>".$msg['edit_cbgen_mep_text_font_size']."</label><br />";
		$display .= $this->get_display_line_size_parameter('CBG_TEXT_FONT_SIZE');
		$display .= "<label class='etiquette'>".$msg['edit_cbgen_mep_text_size']."</label><br />";
		$display .= $this->get_display_line_size_parameter('CBG_CB_TEXT_SIZE');
		$display .= "<label class='etiquette'>".$msg['edit_cbgen_mep_cb_res']."</label><br />";
		$display .= $msg['edit_cbgen_mep_cb_res_details']."<br />";
		$display .= $this->get_display_line_size_parameter('CBG_CB_RES');
		$display .= $msg['edit_cbgen_mep_cb_res_note']."<br />";
		return $display;
	}

	public function get_display_bibli_name_content_form() {
		global $msg, $charset, $biblio_name;

		return "
		<div class='row'>
			<label class='etiquette' for='bibli_name'>$msg[800]</label><br />
			<input class='saisie-80em' id='bibli_name' type='text' name='bibli_name' value=\"".htmlentities($biblio_name, ENT_QUOTES, $charset)."\" />
		</div>";
	}

	public function get_display_label_content_form() {
		global $msg, $charset;

		return "
		<div class='row'>
			<label class='etiquette'>$msg[edit_cbgen_type_cb_libelle] </label>
			<input class='saisie-20em' id='type_cb_libelle' type='text' class='text' name='type_cb_libelle' value=\"".htmlentities($this->label ?? '', ENT_QUOTES, $charset)."\" />
		</div>
		";
	}

	public function get_display_orientation_content_form() {
		global $msg;

		return "
		<div class='row'>
			<label class='etiquette'>$msg[edit_cbgen_mep_orientation] </label>
			<select name='ORIENTATION' size='1'>
			  ".$this->gen_selector_page_orientation()."
			</select>
		</div>";
	}

	public function get_display_nbr_content_form() {
		global $msg;

		return "
		<div class='row'>
			<label class='etiquette'>$msg[edit_cbgen_mep_nbr_x_cells]</label><br />
			<input class='saisie-20em' id='CBG_NBR_X_CELLS' type='text' class='text' name='CBG_NBR_X_CELLS' value=\"".$this->CBG_NBR_X_CELLS."\"/><br />

			<label class='etiquette'>$msg[edit_cbgen_mep_nbr_y_cells]</label><br />
			<input class='saisie-20em' id='CBG_NBR_Y_CELLS' type='text' class='text' name='CBG_NBR_Y_CELLS' value=\"".$this->CBG_NBR_Y_CELLS."\" />
		</div>";
	}

	public function get_content_units_form() {
		$content_form = $this->get_display_orientation_content_form();
		$content_form .= $this->get_display_nbr_content_form();
		$content_form .= $this->get_display_margins();
		$content_form .= $this->get_display_inner_margins();
		$content_form .= $this->get_display_text_sizes();
		return $content_form;
	}

	public function get_content_form() {
		$content_form = $this->get_display_label_content_form();
		$content_form .= $this->get_content_units_form();
		return $content_form;
	}

	public function get_form() {
		global $msg;

		$interface_form = new interface_form('barcodes_sheet_form');
		$interface_form->set_label($msg['barcodes_sheet_form_edit']);
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['barcodes_sheet_delete_confirm'])
		->set_content_form($this->get_content_form())
		->set_table_name('barcodes_sheets')
		->set_field_focus('type_cb_libelle');
		return $interface_form->get_display();
	}

	public function set_properties_from_form() {
		global $type_cb_libelle;
		global $ORIENTATION;
		global $CBG_NBR_X_CELLS;
		global $CBG_NBR_Y_CELLS;
		global $CBG_LEFT_MARGIN;
		global $CBG_RIGHT_MARGIN;
		global $CBG_TOP_MARGIN;
		global $CBG_BOTTOM_MARGIN;
		global $CBG_INNER_LEFT_MARGIN;
		global $CBG_INNER_RIGHT_MARGIN;
		global $CBG_INNER_TOP_MARGIN;
		global $CBG_INNER_BOTTOM_MARGIN;
		global $CBG_TEXT_HEIGHT;
		global $CBG_TEXT_FONT_SIZE;
		global $CBG_CB_TEXT_SIZE;
		global $CBG_CB_RES;

		$this->label = stripslashes($type_cb_libelle);
		$this->page_orientation = $ORIENTATION;
		$this->CBG_NBR_X_CELLS = stripslashes($CBG_NBR_X_CELLS);
		$this->CBG_NBR_Y_CELLS = stripslashes($CBG_NBR_Y_CELLS);
		$this->CBG_LEFT_MARGIN = stripslashes($CBG_LEFT_MARGIN);
		$this->CBG_RIGHT_MARGIN = stripslashes($CBG_RIGHT_MARGIN);
		$this->CBG_TOP_MARGIN = stripslashes($CBG_TOP_MARGIN);
		$this->CBG_BOTTOM_MARGIN = stripslashes($CBG_BOTTOM_MARGIN);
		$this->CBG_INNER_LEFT_MARGIN = stripslashes($CBG_INNER_LEFT_MARGIN);
		$this->CBG_INNER_RIGHT_MARGIN = stripslashes($CBG_INNER_RIGHT_MARGIN);
		$this->CBG_INNER_TOP_MARGIN = stripslashes($CBG_INNER_TOP_MARGIN);
		$this->CBG_INNER_BOTTOM_MARGIN = stripslashes($CBG_INNER_BOTTOM_MARGIN);
		$this->CBG_TEXT_HEIGHT = stripslashes($CBG_TEXT_HEIGHT);
		$this->CBG_TEXT_FONT_SIZE = stripslashes($CBG_TEXT_FONT_SIZE);
		$this->CBG_CB_TEXT_SIZE = stripslashes($CBG_CB_TEXT_SIZE);
		$this->CBG_CB_RES = stripslashes($CBG_CB_RES);

		$this->set_page_sizes();
	}

	public function get_data() {
		return array(
			'id' => $this->id,
			'label' => $this->label,
			'page_format' => $this->page_format,
			'page_orientation' => $this->page_orientation,
			'unit' => $this->unit,
			'CBG_NBR_X_CELLS' => $this->CBG_NBR_X_CELLS,
			'CBG_NBR_Y_CELLS' => $this->CBG_NBR_Y_CELLS,
			'CBG_LEFT_MARGIN' => $this->CBG_LEFT_MARGIN,
			'CBG_RIGHT_MARGIN' => $this->CBG_RIGHT_MARGIN,
			'CBG_TOP_MARGIN' => $this->CBG_TOP_MARGIN,
			'CBG_BOTTOM_MARGIN' => $this->CBG_BOTTOM_MARGIN,
			'CBG_INNER_LEFT_MARGIN' => $this->CBG_INNER_LEFT_MARGIN,
			'CBG_INNER_RIGHT_MARGIN' => $this->CBG_INNER_RIGHT_MARGIN,
			'CBG_INNER_TOP_MARGIN' => $this->CBG_INNER_TOP_MARGIN,
			'CBG_INNER_BOTTOM_MARGIN' => $this->CBG_INNER_BOTTOM_MARGIN,
			'CBG_TEXT_HEIGHT' => $this->CBG_TEXT_HEIGHT,
			'CBG_TEXT_FONT_SIZE' => $this->CBG_TEXT_FONT_SIZE,
			'CBG_CB_TEXT_SIZE' => $this->CBG_CB_TEXT_SIZE,
			'CBG_CB_RES' => $this->CBG_CB_RES
		);
	}

	protected function get_next_order() {
		$query = "select max(barcodes_sheet_order)+1 as next_order from barcodes_sheets";
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		return intval($row->next_order);
	}

	public function save() {
		if($this->id) {
			$query = "update barcodes_sheets set ";
			$clause = "where id_barcodes_sheet = ".$this->id;
		} else {
			$query = "insert into barcodes_sheets set ";
			$clause = "";
			$this->order = $this->get_next_order();
		}
		$data = $this->get_data();
		unset($data['id']);
		unset($data['label']);
		$query .= "barcodes_sheet_label = '".addslashes($this->label)."',
				barcodes_sheet_data = '".encoding_normalize::json_encode($data)."',
				barcodes_sheet_order = '".$this->order."' ";
		$query .= $clause;
		pmb_mysql_query($query);
	}

	public static function delete($id) {
		if($id) {
			$query = "delete from barcodes_sheets where id_barcodes_sheet =".$id;
			pmb_mysql_query($query);
			return true;
		}
		return false;
	}

	public function get_json_data() {
		return encoding_normalize::json_encode($this->get_data());
	}

	public function get_id() {
		return $this->id;
	}

	public function get_label() {
		return $this->label;
	}

	public function get_page_format() {
		return $this->page_format;
	}

	public function get_page_orientation() {
		return $this->page_orientation;
	}

	public function get_page_orientation_label() {
		global $msg;

		$label = '';
		switch ($this->page_orientation) {
			case 'P':
				$label = $msg['edit_cbgen_mep_portrait'];
				break;
			case 'L':
				$label = $msg['edit_cbgen_mep_paysage'];
				break;
		}
		return $label;
	}

	public function get_unit() {
		return $this->unit;
	}

	public function get_CBG_NBR_X_CELLS() {
		return $this->CBG_NBR_X_CELLS;
	}

	public function get_CBG_NBR_Y_CELLS() {
		return $this->CBG_NBR_Y_CELLS;
	}

	public function get_CBG_LEFT_MARGIN() {
		return $this->CBG_LEFT_MARGIN;
	}

	public function get_CBG_RIGHT_MARGIN() {
		return $this->CBG_RIGHT_MARGIN;
	}

	public function get_CBG_TOP_MARGIN() {
		return $this->CBG_TOP_MARGIN;
	}

	public function get_CBG_BOTTOM_MARGIN() {
		return $this->CBG_BOTTOM_MARGIN;
	}

	public function get_CBG_INNER_LEFT_MARGIN() {
		return $this->CBG_INNER_LEFT_MARGIN;
	}

	public function get_CBG_INNER_RIGHT_MARGIN() {
		return $this->CBG_INNER_RIGHT_MARGIN;
	}

	public function get_CBG_INNER_TOP_MARGIN() {
		return $this->CBG_INNER_TOP_MARGIN;
	}

	public function get_CBG_INNER_BOTTOM_MARGIN() {
		return $this->CBG_INNER_BOTTOM_MARGIN;
	}

	public function get_CBG_TEXT_HEIGHT() {
		return $this->CBG_TEXT_HEIGHT;
	}

	public function get_CBG_TEXT_FONT_SIZE() {
		return $this->CBG_TEXT_FONT_SIZE;
	}

	public function get_CBG_CB_TEXT_SIZE() {
		return $this->CBG_CB_TEXT_SIZE;
	}

	public function get_CBG_CB_RES() {
		return $this->CBG_CB_RES;
	}

	public function generate_globals() {
		global $mep_etiq_cb, $biblio_name;

		$sel_type = 'barcodes_sheet_'.$this->id;
		$mep_etiq_cb[$sel_type]=array();
		$mep_etiq_cb[$sel_type]['bibli_name']=stripslashes($biblio_name);
		$mep_etiq_cb[$sel_type]['nbr_cb']=50;
		$mep_etiq_cb[$sel_type]['type_cb_name'] = $this->get_label();
		$mep_etiq_cb[$sel_type]['type_cb_libelle'] = $sel_type;
		$mep_etiq_cb[$sel_type]['ORIENTATION'] = $this->get_page_orientation();
		$mep_etiq_cb[$sel_type]['CBG_NBR_X_CELLS'] = $this->get_CBG_NBR_X_CELLS();
		$mep_etiq_cb[$sel_type]['CBG_NBR_Y_CELLS'] = $this->get_CBG_NBR_Y_CELLS();
		$mep_etiq_cb[$sel_type]['CBG_LEFT_MARGIN'] = $this->get_CBG_LEFT_MARGIN();
		$mep_etiq_cb[$sel_type]['CBG_RIGHT_MARGIN'] = $this->get_CBG_RIGHT_MARGIN();
		$mep_etiq_cb[$sel_type]['CBG_TOP_MARGIN'] = $this->get_CBG_TOP_MARGIN();
		$mep_etiq_cb[$sel_type]['CBG_BOTTOM_MARGIN'] = $this->get_CBG_BOTTOM_MARGIN();
		$mep_etiq_cb[$sel_type]['CBG_INNER_LEFT_MARGIN'] = $this->get_CBG_INNER_LEFT_MARGIN();
		$mep_etiq_cb[$sel_type]['CBG_INNER_RIGHT_MARGIN'] = $this->get_CBG_INNER_RIGHT_MARGIN();
		$mep_etiq_cb[$sel_type]['CBG_INNER_TOP_MARGIN'] = $this->get_CBG_INNER_TOP_MARGIN();
		$mep_etiq_cb[$sel_type]['CBG_INNER_BOTTOM_MARGIN'] = $this->get_CBG_INNER_BOTTOM_MARGIN();
		$mep_etiq_cb[$sel_type]['CBG_TEXT_HEIGHT'] = $this->get_CBG_TEXT_HEIGHT();
		$mep_etiq_cb[$sel_type]['CBG_TEXT_FONT_SIZE'] = $this->get_CBG_TEXT_FONT_SIZE();
		$mep_etiq_cb[$sel_type]['CBG_CB_TEXT_SIZE'] = $this->get_CBG_CB_TEXT_SIZE();
		$mep_etiq_cb[$sel_type]['CBG_CB_RES'] = $this->get_CBG_CB_RES();
	}
}