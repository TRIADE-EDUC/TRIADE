<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;

// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: spreadsheetPMB.class.php,v 1.3 2019/12/30 15:40:43 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class spreadsheetPMB {
	
    private $objPHPSpreadsheet;
	
	protected $active_sheet;
	
	public function __construct(){
		global $base_path;
		
		$cache_dir = $base_path."/temp/";
		$this->clear_cache($cache_dir);
		$this->objPHPSpreadsheet = new Spreadsheet();
		$this->objPHPSpreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(18);
		$this->active_sheet = 0;
	}
	
	public function clear_cache($cache_dir) {
		//Existence du répertoire
		if(file_exists($cache_dir)){
			$array_files = scandir($cache_dir);
			if ((is_array($array_files)) && (count($array_files))) {
				foreach ($array_files as $file) {
				    //Le fichier est-il un cache de la classe spreadsheetPMB ?
					if (preg_match('#^spreadsheetPMB\..+\.cache$#',$file)) {
						//Le fichier a-t-il plus d'une heure ?
						$time_file = filemtime($cache_dir.$file);
						if((time()-$time_file)>=3600){
							//On le supprime
							unlink($cache_dir.$file);
						}
					}
				}
			}
		}
	}
	
	public function get_active_sheet() {
		return $this->active_sheet;
	}
	
	public function set_active_sheet($sheet = 0) {
		$this->active_sheet = $sheet;
	}
	
	public function set_column($first, $last, $width) {
		for ($i=$first; $i<=$last; $i++) {
		    $this->objPHPSpreadsheet->setActiveSheetIndex($this->active_sheet)->getColumnDimensionByColumn($i)->setWidth($width);
		}
	}
	
	public function merge_cells($row1, $col1, $row2, $col2) {
	    if ($col1 == 0) $col1 = 1;
	    if ($col2 == 0) $col2 = 1;
	    
	    $this->objPHPSpreadsheet->setActiveSheetIndex($this->active_sheet)->mergeCellsByColumnAndRow($col1, $row1+1, $col2, $row2+1);
	}
	
	public function write_string($row, $col, $value, $styleArray=array()) {
		global $charset;
		if($charset != 'utf-8'){
			$value = iconv("CP1252", "UTF-8//TRANSLIT", $value);
		}
		if (trim($value)) {
		    $value = html_entity_decode($value, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES, 'utf-8');
		    $this->objPHPSpreadsheet->setActiveSheetIndex($this->active_sheet)->setCellValueExplicitByColumnAndRow($col+1, $row+1, $value, DataType::TYPE_STRING);
		}
		if (count($styleArray)) {
		    $this->objPHPSpreadsheet->setActiveSheetIndex($this->active_sheet)->getStyleByColumnAndRow($col+1, $row+1)->applyFromArray($styleArray);
		}
	}
	
	public function write($row, $col, $value, $styleArray=array()){
		global $charset;
		
		if($charset != 'utf-8'){
			$value = iconv("CP1252", "UTF-8//TRANSLIT", $value);
		}
		if (trim($value)) {
		    $this->objPHPSpreadsheet->setActiveSheetIndex($this->active_sheet)->setCellValueByColumnAndRow($col+1, $row+1, $value);
		}
		if (count($styleArray)) {
		    $this->objPHPSpreadsheet->setActiveSheetIndex($this->active_sheet)->getStyleByColumnAndRow($col+1, $row+1)->applyFromArray($styleArray);
		}
	}
	
	public function download($filename){
		//On force en xlsx pour compatibilité avec les tableurs
		$extension = pathinfo($filename, PATHINFO_EXTENSION);
		if ($extension == "xls") {
			$filename = substr($filename,0,strlen($filename)-4).'.xlsx';
		}
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Cache-Control: cache, must-revalidate');
		header('Pragma: public');
			
		$objWriter = IOFactory::createWriter($this->objPHPSpreadsheet, 'Xlsx');
		$objWriter->save('php://output');
		$this->objPHPSpreadsheet->setActiveSheetIndex($this->active_sheet)->disconnectCells();
		exit;
	}
	
	public function save_file($filename){
	    $objWriter = IOFactory::createWriter($this->objPHPSpreadsheet, 'Xlsx');
		$objWriter->save($filename);
		$this->objPHPSpreadsheet->setActiveSheetIndex($this->active_sheet)->disconnectCells();
	}
}