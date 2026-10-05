<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sticks_sheet.class.php,v 1.10 2022/01/21 08:37:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/templates/sticks_sheet/sticks_sheet.tpl.php");
require_once($class_path."/encoding_normalize.class.php");

/**
 * Planche d'étiquettes
 */
class sticks_sheet {
	
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
	 * Nombre d'étiquettes en largeur
	 * @var int
	 */
	protected $nbr_x_sticks;
	
	/**
	 * Nombre d'étiquettes en hauteur
	 * @var int
	 */
	protected $nbr_y_sticks;
	
	/**
	 * Largeur de l'étiquette
	 * @var float
	 */
	protected $stick_width;
	
	/**
	 * Hauteur de l'étiquette
	 * @var float
	 */
	protected $stick_height;
	
	/**
	 * Marge de gauche
	 * @var float
	 */
	protected $left_margin;
	
	/**
	 * Marge du haut
	 * @var float
	 */
	protected $top_margin;

	/**
	 * Espace horizontal entre 2 étiquettes
	 * @var float
	 */
	protected $x_sticks_spacing;
	
	/**
	 * Espacement vertical entre 2 étiquettes
	 * @var float
	 */
	protected $y_sticks_spacing;
	
	/**
	 * Position courante de l'étiquette (unité : étiquette)
	 * @var int
	 */
	protected $x_stick;
	
	/**
	 * Position courante de l'étiquette (unité : étiquette)
	 */
	protected $y_stick;
	
	/**
	 * Numéro d'ordre
	 */
	protected $order;
	
	/**
	 * Tailles du format de la page
	 */
	protected $page_sizes;
	
	protected $cote_coords;
	
	protected $image_coords;
	
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$this->label = 'Standard - 38.1x21.2mm - Avery J8651';
		$this->page_format = 'A4';
		$this->page_orientation = 'P';
		$this->unit = 'mm';
		$this->nbr_x_sticks = '5';
		$this->nbr_y_sticks = '13';
		$this->stick_width = '38.1';
		$this->stick_height = '21.2';
		$this->left_margin = '5.5';
		$this->top_margin = '11.3';
		$this->x_sticks_spacing = '40.75';
		$this->y_sticks_spacing = '21.2';
		$this->order = 0;
		$this->page_sizes = array('210','297');
		$this->init_cote_coords();
		$this->init_image_coords();
		if($this->id) {
			$query = "select * from sticks_sheets where id_sticks_sheet = ".$this->id;
			$result = pmb_mysql_query($query);
			$row = pmb_mysql_fetch_object($result);
			$this->label = $row->sticks_sheet_label;
			$this->set_data(json_decode($row->sticks_sheet_data, true));
			$this->order = $row->sticks_sheet_order;
			$this->set_page_sizes();
		}
	}
	
	protected function init_cote_coords() {
		global $pmb_pdf_fontfixed;
		
		$this->cote_coords = array(
				'width' => '22.1',
				'height' => '18',
				'from_top' => '1.6',
				'from_left' => '10',
				'font' => $pmb_pdf_fontfixed,
				'font_size' => '14',
				'font_style' => 'B',
				'font_color' => '000000',
				'align' => 'C',
				'rotation' => '0',
		        'border_size' => '0',
                'character_line_break' => ''
		);
	}
	
	protected function init_image_coords() {
		$this->image_coords = array(
				'source' => 'pmb.png',
				'width' => '8',
				'height' => '5',
				'from_top' => '2',
				'from_left' => '2',
				'rotation' => '0'
		);
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
	
	protected function get_display_line_unit_parameter($property, $type) {
		global $msg, $charset;
		
		$display = "
		<div class='row'>
			<div class='colonne25'>".htmlentities($msg[str_replace('_coords', '', $type)."_".$property].' (mm)', ENT_QUOTES, $charset)."</div>
			<div class='colonne_suite'>
				<input type='text' id='sticks_sheet_".$type."_".$property."' name='sticks_sheet_".$type."[".$property."]' class='saisie-5em' style='text-align:right;' value='".$this->{$type}[$property]."' />
			</div>
		</div>";
		return $display;
	}
	
	protected function get_display_line_font_parameter($property, $type) {
		global $msg, $charset;
	
		$display = "
		<div class='row'>
			<div class='colonne25'>".htmlentities($msg[$property], ENT_QUOTES, $charset)."</div>
			<div class='colonne_suite'>
				<input type='text' id='sticks_sheet_".$type."_".$property."' name='sticks_sheet_".$type."[".$property."]' class='saisie-5em' style='text-align:right;' value='".$this->{$type}[$property]."' />
			</div>
		</div>";
		return $display;
	}
		
	protected function get_display_align() {
		global $msg, $charset;
		
		$display = "
		<div class='row'>
			<div class='colonne25'>".htmlentities($msg['align'], ENT_QUOTES, $charset)."</div>
			<div class='colonne_suite'>
				<select id='sticks_sheet_cote_coords_align' name='sticks_sheet_cote_coords[align]' >";
		switch ($this->cote_coords['align']) {
			case 'L' :
				$display.="	<option value='C' >".htmlentities($msg['centered'], ENT_QUOTES, $charset)."</option>
					<option value='L' selected='selected' >".htmlentities($msg['left'], ENT_QUOTES, $charset)."</option>
					<option value='R' >".htmlentities($msg['right'], ENT_QUOTES, $charset)."</option>
					<option value='J' >".htmlentities($msg['justified'], ENT_QUOTES, $charset)."</option>";
				break;
			case 'R' :
				$display.="	<option value='C' >".htmlentities($msg['centered'], ENT_QUOTES, $charset)."</option>
					<option value='L' >".htmlentities($msg['left'], ENT_QUOTES, $charset)."</option>
					<option value='R' selected='selected' >".htmlentities($msg['right'], ENT_QUOTES, $charset)."</option>
					<option value='J' >".htmlentities($msg['justified'], ENT_QUOTES, $charset)."</option>";
				break;
			case 'J' :
				$display.="	<option value='C' >".htmlentities($msg['centered'], ENT_QUOTES, $charset)."</option>
					<option value='L' >".htmlentities($msg['left'], ENT_QUOTES, $charset)."</option>
					<option value='R' >".htmlentities($msg['right'], ENT_QUOTES, $charset)."</option>
					<option value='J' selected='selected' >".htmlentities($msg['justified'], ENT_QUOTES, $charset)."</option>";
				break;
			case 'C':
			default :
				$display.="	<option value='C' selected='selected' >".htmlentities($msg['centered'], ENT_QUOTES, $charset)."</option>
					<option value='L' >".htmlentities($msg['left'], ENT_QUOTES, $charset)."</option>
					<option value='R' >".htmlentities($msg['right'], ENT_QUOTES, $charset)."</option>
					<option value='J' >".htmlentities($msg['justified'], ENT_QUOTES, $charset)."</option>";
				break;
		}
		$display.= "		</select>
			</div>
		</div>";
		return $display;
	}
	
	protected function get_display_cote_coords() {
		global $msg, $charset;
		
		$display = "
		<div class='row'>
			<label class='etiquette'>".htmlentities($msg[296], ENT_QUOTES, $charset)."</label>
		</div>";
	
		$display .= $this->get_display_line_unit_parameter('width', 'cote_coords');
		$display .= $this->get_display_line_unit_parameter('height', 'cote_coords');
		$display .= $this->get_display_line_unit_parameter('from_top', 'cote_coords');
		$display .= $this->get_display_line_unit_parameter('from_left', 'cote_coords');
	
		$display.= $this->get_display_line_font_parameter('font', 'cote_coords');
		$display.= $this->get_display_line_font_parameter('font_size', 'cote_coords');
	
		$display.= "
		<div class='row'>
			<div class='colonne25'>".htmlentities($msg['font_style'], ENT_QUOTES, $charset)."</div>
			<div class='colonne_suite'>
				<select id='sticks_sheet_cote_coords_font_style' name='sticks_sheet_cote_coords[font_style]' >";
		if ($this->cote_coords['font_style'] == '') {
			$display.="		<option value='' selected='selected' >".htmlentities($msg['font_style_normal'], ENT_QUOTES, $charset)."</option>
					<option value='B' >".htmlentities($msg['font_style_bold'], ENT_QUOTES, $charset)."</option>";
		} else {
			$display.="		<option value='' >".htmlentities($msg['font_style_normal'], ENT_QUOTES, $charset)."</option>
					<option value='B' selected='selected' >".htmlentities($msg['font_style_bold'], ENT_QUOTES, $charset)."</option>";
		}
		$display.= "		</select>
			</div>
		</div>";
	
		$display.= $this->get_display_line_font_parameter('font_color', 'cote_coords');
	
		$display.= $this->get_display_align();
	
		$display.= "
		<div class='row'>
			<div class='colonne25'>".htmlentities($msg['rotation'], ENT_QUOTES, $charset)."</div>
			<div class='colonne_suite'>
				<input type='text' id='sticks_sheet_cote_coords_rotation' name='sticks_sheet_cote_coords[rotation]' class='saisie-5em' style='text-align:right;' value='".$this->cote_coords['rotation']."' />
			</div>
		</div>";
		$display .= $this->get_display_line_unit_parameter('border_size', 'cote_coords');
		$display.= "
		<div class='row'>
			<div class='colonne25'>".htmlentities($msg['cote_character_line_break'], ENT_QUOTES, $charset)."</div>
			<div class='colonne_suite'>
				<input type='text' id='sticks_sheet_cote_coords_character_line_break' name='sticks_sheet_cote_coords[character_line_break]' class='saisie-5em' style='text-align:right;' value='".$this->cote_coords['character_line_break']."' />
			</div>
		</div>";
	
		return $display;
	}
	
	protected function get_display_image_coords() {
		global $msg, $charset;
		
		$display = "<div class='row'>
			<label class='etiquette'>".htmlentities($msg['image'], ENT_QUOTES, $charset)."</label>
		</div>";
		
		
		$display.= "<div class='row'>
			<div class='colonne25'>".htmlentities($msg['image_source'], ENT_QUOTES, $charset)."</div>
			<div class='colonne_suite'>
				<input type='text' id='sticks_sheet_image_coords_source' name='sticks_sheet_image_coords[source]' class='saisie-10em' value='".$this->image_coords['source']."' />
			</div>
		</div>";
		
		$display.=$this->get_display_line_unit_parameter('width', 'image_coords');
		$display.=$this->get_display_line_unit_parameter('height', 'image_coords');
		$display.=$this->get_display_line_unit_parameter('from_top', 'image_coords');
		$display.=$this->get_display_line_unit_parameter('from_left', 'image_coords');
		
		$display.= "<div class='row'>
			<div class='colonne25'>".htmlentities($msg['rotation'], ENT_QUOTES, $charset)."</div>
			<div class='colonne_suite'>
				<input type='text' id='sticks_sheet_image_coords_rotation' name='sticks_sheet_image_coords[rotation]' class='saisie-5em' style='text-align:right;' value='".$this->image_coords['rotation']."' />
			</div>
		</div>";
		
		return $display;
	}
	
	public function get_form() {
		global $msg;
		global $sticks_sheet_content_form;
		
		$content_form = $sticks_sheet_content_form;
		$interface_form = new interface_form('sticks_sheet_form');
		$interface_form->set_label($msg['sticks_sheet_form_edit']);
		
		$content_form = str_replace('!!label!!', $this->label, $content_form);
		$content_form = str_replace('!!unit!!', $this->unit, $content_form);
		$content_form = str_replace('!!page_format!!', $this->gen_selector_page_format(), $content_form);
		$content_form = str_replace('!!page_orientation!!', $this->gen_selector_page_orientation(), $content_form);
		$content_form = str_replace('!!nbr_x_sticks!!', $this->nbr_x_sticks, $content_form);
		$content_form = str_replace('!!nbr_y_sticks!!', $this->nbr_y_sticks, $content_form);
		$content_form = str_replace('!!stick_width!!', $this->stick_width, $content_form);
		$content_form = str_replace('!!stick_height!!', $this->stick_height, $content_form);
		$content_form = str_replace('!!left_margin!!', $this->left_margin, $content_form);
		$content_form = str_replace('!!top_margin!!', $this->top_margin, $content_form);
		$content_form = str_replace('!!x_sticks_spacing!!', $this->x_sticks_spacing, $content_form);
		$content_form = str_replace('!!y_sticks_spacing!!', $this->y_sticks_spacing, $content_form);
		$content_form = str_replace('!!cote_coords!!', $this->get_display_cote_coords(), $content_form);
		$content_form = str_replace('!!image_coords!!', $this->get_display_image_coords(), $content_form);
		$content_form = str_replace('!!id!!', $this->id, $content_form);
		
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['sticks_sheet_delete_confirm'])
		->set_content_form($content_form)
		->set_table_name('sticks_sheets');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $sticks_sheet_label;
		global $sticks_sheet_page_format;
		global $sticks_sheet_page_orientation;
		global $sticks_sheet_unit;
		global $sticks_sheet_nbr_x_sticks;
		global $sticks_sheet_nbr_y_sticks;
		global $sticks_sheet_stick_width;
		global $sticks_sheet_stick_height;
		global $sticks_sheet_left_margin;
		global $sticks_sheet_top_margin;
		global $sticks_sheet_x_sticks_spacing;
		global $sticks_sheet_y_sticks_spacing;
		global $sticks_sheet_cote_coords;
		global $sticks_sheet_image_coords;
		
		$this->label = stripslashes($sticks_sheet_label);
		$this->page_format = $sticks_sheet_page_format;
		$this->page_orientation = $sticks_sheet_page_orientation;
		$this->unit = $sticks_sheet_unit;
		$this->nbr_x_sticks = $sticks_sheet_nbr_x_sticks;
		$this->nbr_y_sticks = $sticks_sheet_nbr_y_sticks;
		$this->stick_width = $sticks_sheet_stick_width;
		$this->stick_height = $sticks_sheet_stick_height;
		$this->left_margin = $sticks_sheet_left_margin;
		$this->top_margin = $sticks_sheet_top_margin;
		$this->x_sticks_spacing = $sticks_sheet_x_sticks_spacing;
		$this->y_sticks_spacing = $sticks_sheet_y_sticks_spacing;
		$this->cote_coords = $sticks_sheet_cote_coords;
		$this->image_coords = $sticks_sheet_image_coords;
		$this->set_page_sizes();
	}
	
	public function get_data() {
		return array(
			'id' => $this->id,
			'label' => $this->label,
			'page_format' => $this->page_format,
			'page_orientation' => $this->page_orientation,
			'unit' => $this->unit,
			'nbr_x_sticks' => $this->nbr_x_sticks,
			'nbr_y_sticks' => $this->nbr_y_sticks,
			'stick_width' => $this->stick_width,
			'stick_height' => $this->stick_height,
			'left_margin' => $this->left_margin,
			'top_margin' => $this->top_margin,
			'x_sticks_spacing' => $this->x_sticks_spacing,
			'y_sticks_spacing' => $this->y_sticks_spacing,
			'page_sizes' => $this->page_sizes,
			'cote_coords' => $this->cote_coords,
			'image_coords' => $this->image_coords
		);
	}
	
	protected function get_next_order() {
		$query = "select max(sticks_sheet_order)+1 as next_order from sticks_sheets";
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		return intval($row->next_order);
	}
	
	public function save() {
		if($this->id) {
			$query = "update sticks_sheets set ";
			$clause = "where id_sticks_sheet = ".$this->id;
		} else {
			$query = "insert into sticks_sheets set ";
			$clause = "";
			$this->order = $this->get_next_order();
		}
		$data = $this->get_data();
		unset($data['id']);
		unset($data['label']);
		$query .= "sticks_sheet_label = '".addslashes($this->label)."',
				sticks_sheet_data = '".encoding_normalize::json_encode($data)."',
				sticks_sheet_order = '".$this->order."' ";
		$query .= $clause;
		pmb_mysql_query($query);
	}
	
	public static function delete($id) {
		if($id) {
			$query = "delete from sticks_sheets where id_sticks_sheet =".$id;
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
	
	public function get_nbr_x_sticks() {
		return $this->nbr_x_sticks;
	}
	
	public function get_nbr_y_sticks() {
		return $this->nbr_y_sticks;
	}
	
	public function get_stick_width() {
		return $this->stick_width;
	}
	
	public function get_stick_height() {
		return $this->stick_height;
	}
	
	public function get_left_margin() {
		return $this->left_margin;
	}
	
	public function get_top_margin() {
		return $this->top_margin;
	}
	
	public function get_x_sticks_spacing() {
		return $this->x_sticks_spacing;
	}
	
	public function get_y_sticks_spacing() {
		return $this->y_sticks_spacing;
	}
	
	public function get_cote_coords() {
		return $this->cote_coords;
	}
	
	public function get_image_coords() {
		return $this->image_coords;
	}
	
	/**
	 * Retourne le bouton de sélection des planches d'étiquettes
	 * @param string $dialog_title Titre du dialog à ouvrir
	 * @param string $button_label Libellé du bouton
	 * @param string $source Source
	 * @param int $sticks_sheet_selected Identifiant de la plache d'étiquette à utiliser par défaut
	 * @return mixed[]
	 */
	public function get_display_stick_select_button ($dialog_title, $button_label, $source) {
		global $stick_sheet_stick_select_button, $stick_sheet_stick_select_button_script, $charset;
		
		$display = $stick_sheet_stick_select_button;
		$display = str_replace('!!button_label!!', htmlentities($button_label, ENT_QUOTES, $charset), $display);
		$display = str_replace('!!source!!', htmlentities($source, ENT_QUOTES, $charset), $display);
		$display = str_replace('!!sticksSheetSelected!!', $this->id, $display);
		
		$script = $stick_sheet_stick_select_button_script;
		$script = str_replace('!!dialog_title!!', htmlentities($dialog_title, ENT_QUOTES, $charset), $script);
		
		return array(
				'display' => $display,
				'script' => $script
		);
	}
	
	public function generate_globals() {
		global $msg, $charset;
		global $label_fmt;
		global $label_con;
	
		$key = 'stick_sheet_'.$this->get_id();
	
		$label_fmt[$key]['label_name'] 				= $this->get_label();
		$label_fmt[$key]['page_format'] 				= $this->get_page_format();
		$label_fmt[$key]['page_orientation']			= $this->get_page_orientation();
		$label_fmt[$key]['unit'] 						= $this->get_unit();
		$label_fmt[$key]['label_grid_nb_per_row']		= $this->get_nbr_x_sticks();
		$label_fmt[$key]['label_grid_nb_per_col']		= $this->get_nbr_y_sticks();
		$label_fmt[$key]['label_width'] 				= $this->get_stick_width();
		$label_fmt[$key]['label_height'] 				= $this->get_stick_height();
		$label_fmt[$key]['label_grid_from_top'] 		= $this->get_top_margin();
		$label_fmt[$key]['label_grid_from_left'] 		= $this->get_left_margin();
		$label_fmt[$key]['label_grid_h_spacing']		= $this->get_x_sticks_spacing();
		$label_fmt[$key]['label_grid_v_spacing'] 		= $this->get_y_sticks_spacing();
	
		$label_con[$key]['content_type'][0] 	= "cote";
		$label_con[$key]['comment'][0]			= htmlentities($msg[296], ENT_QUOTES, $charset);
	
		$cote_coords = $this->get_cote_coords();
		foreach ($cote_coords as $index=>$data) {
			$label_con[$key][$index][0] = $data;
		}
	
		$label_con[$key]['content_type'][1] 	= "image";
		$label_con[$key]['comment'][1] 		= htmlentities($msg['image'], ENT_QUOTES, $charset);
		$image_coords = $this->get_image_coords();
		foreach ($image_coords as $index=>$data) {
			$label_con[$key][$index][1] = $data;
		}
	}
}