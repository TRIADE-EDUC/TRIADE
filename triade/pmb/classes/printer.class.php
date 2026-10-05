<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: printer.class.php,v 1.6.16.1 2025/04/03 10:09:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/printer/printer_data.class.php");
require_once($class_path."/printer/printer_data_converter.class.php");

class printer {
	
	public $printer_name='metapace';			// nom de l'imprimante
	public $printer_driver='metapace';			// driver imprimante
	public $printer_data=NULL;					// info d'impression
	public $printer_data_convert_to='';			// conversion des données
	public $printer_jzebra=true;
	public $printer_jzebra_url = '';

	public function __construct(){
	}
	
	public function initialize() {
		global $class_path, $pmb_opac_url;
		
		require_once($class_path.'/printer/'.$this->printer_driver.'.class.php');
		if(!$this->printer_jzebra_url && $pmb_opac_url) {
			$this->printer_jzebra_url = $pmb_opac_url."includes/javascript/printers/zebra/jzebra.jar";
		}
		$this->printer_driver=new $this->printer_driver();
		$this->printer_data= new printer_data();
	}
	
	public function get_script() {
		$script = '';
		if ($this->printer_jzebra) {
			$script = "<applet name='jzebra' code='jzebra.PrintApplet.class' archive= '".$this->printer_jzebra_url."' width='0px' height='0px'></applet>";
		}
		return $script;		
	}
	
	protected function fetch_data(){
	}
	
	private function gen_print($data,$tpl_perso='') {
		
		$r='';
		
		if($this->printer_data_convert_to) {
			$data = printer_data_converter::convert_to($data,$this->printer_data_convert_to);
		}
		$r = $this->printer_driver->gen_print($data,$tpl_perso);
		
		return $r;
	}
	
	public function print_pret($id_empr,$cb_doc,$tpl_perso=''){
		
		$r='';
		
		$this->printer_data->get_data_empr($id_empr);
		$this->printer_data->get_data_expl($cb_doc);
		
		$r = $this->gen_print($this->printer_data->data,$tpl_perso);
		
		return $r;
	}

	public function print_all_pret($id_empr,$tpl_perso=''){
		
		$r='';
		$this->printer_data->get_data_empr($id_empr);
		$query = "select expl_cb from pret,exemplaires  where pret_idempr=$id_empr and expl_id=pret_idexpl ";		
		$result = pmb_mysql_query($query);		
		while (($r= pmb_mysql_fetch_object($result))) {	
			$this->printer_data->get_data_expl($r->expl_cb,$tpl_perso);		
		}
		
		$query = "select * from resa where resa.resa_idempr=$id_empr ";
		$result = pmb_mysql_query($query);
		while($resa = pmb_mysql_fetch_object($result)) {
			$this->printer_data->get_data_resa($resa->id_resa);	
		}
		$r = $this->gen_print($this->printer_data->data,$tpl_perso);
		return $r;
	}
	
	public function transacash_ticket($transacash_id,$tpl_perso=''){
		
		$r='';
		//$this->printer_data->get_data_empr($id_empr);
		$r = $this->gen_print($this->printer_data->data,$tpl_perso);
		return $r;
	}
	
	public function print_card($id_empr,$tpl_perso=''){
		
		$r='';
		$this->printer_data->get_data_empr($id_empr);
		$r = $this->gen_print($this->printer_data->data,$tpl_perso);
		return $r;
	}

	public function get_selected_printer(){
		global $deflt_printer, $pmb_printer_name, $pmb_printer_list;
		
		$r='';
		if (substr($pmb_printer_name,0,9) == 'raspberry') {
			$raspberry_ip_to_call = '';
			$tmp_pmb_printer_name = explode('@', $pmb_printer_name);
			if (isset($tmp_pmb_printer_name[1])) {
				$raspberry_ip_to_call = $tmp_pmb_printer_name[1];
			}
			$list_printers = explode(";", $pmb_printer_list);
			foreach ($list_printers as $printer) {
				$printer = trim($printer);
				$out = [];
				if (preg_match('#^ *(\d+) *\_ *(.+?) *(\(([\d\.:]+)\))? *$#',$printer,$out)) {
				    if (!empty($out[1]) && $out[1] == $deflt_printer) {
						$r = $out[1]."@".(!empty($out[4]) ? $out[4] : $raspberry_ip_to_call);
						break;
					}
				}
			}
		}
		return $r;
	}
}