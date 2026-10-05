<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sticks_sheet_output.class.php,v 1.3 2022/03/10 15:19:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/fpdf_etiquette.class.php");

/**
 * Classe de génération d'une planche
 */
class sticks_sheet_output {
	
	/**
	 * 
	 * @var sticks_sheet
	 */
	protected $sticks_sheet;
	
	/**
	 * 
	 * @var sticks_sheet_stick
	 */
	protected $sticks_sheet_stick;
	
	/**
	 * 
	 * @param sticks_sheet $sticks_sheet
	 * @param sticks_sheet_stick $sticks_sheet_stick
	 */
	public function __construct($id, $display_class) {
		global $class_path;
		$this->sticks_sheet = new sticks_sheet($id);
		$this->sticks_sheet_stick = null;
		if(file_exists($class_path."/sticks_sheet/stick/".$display_class.".class.php")) {
			require_once($class_path."/sticks_sheet/stick/".$display_class.".class.php");
			$this->sticks_sheet_stick = new $display_class();
			$this->sticks_sheet_stick->set_width($this->sticks_sheet->get_stick_width());
			$this->sticks_sheet_stick->set_height($this->sticks_sheet->get_stick_height());
		}
	}
	
	/**
	 * 
	 * @param string $type Type de sortie
	 * @param integer $first_row Indice horizontal de la première étiquette
	 * @param integer $first_col Indice vertical de la première étiquette
	 */
	public function output($type, $data=array(), $first_row=1, $first_col=1) {
		switch ($type) {
			case 'PDF':
				$this->output_PDF($data, $first_row, $first_col);
				break;
		}
	}
	
	protected function output_PDF($data, $first_row, $first_col) {
		global $fpdf;
		global $pmb_pdf_fontfixed;
		
		// Démarrage et configuration du pdf
		$nom_classe = $fpdf . "_Etiquette";
		$pdf = new $nom_classe ($this->sticks_sheet->get_nbr_x_sticks(), $this->sticks_sheet->get_nbr_y_sticks(), $this->sticks_sheet->get_page_orientation(), $this->sticks_sheet->get_unit() , $this->sticks_sheet->get_page_format());
		$pdf->Open();
		$pdf->SetPageMargins($this->sticks_sheet->get_top_margin(), '0', $this->sticks_sheet->get_left_margin(), '0');
		$pdf->SetSticksMargins(0, 0, 0, 0);
		$pdf->SetSticksPadding($this->sticks_sheet->get_x_sticks_spacing(),$this->sticks_sheet->get_y_sticks_spacing());
		//Saut Etiquettes
		$pos = (($first_row-1)*$this->sticks_sheet->get_nbr_x_sticks()) + ($first_col);
		for ($i=1;$i<$pos;$i++) {
			$pdf->AddStick();
		}
 		//Impression etiquettes
		for ($i=0; $i<count($data); $i++) {
			$pdf->AddStick();
			$pdf->SetFont($pmb_pdf_fontfixed,'' ,'8');
			$pdf->SetXY($pdf->GetStickX(), $pdf->GetStickY());
			$this->sticks_sheet_stick->render($pdf, $data[$i]);
		}
		$pdf->Output('planche_etiquette.pdf', 'I');
	}
}