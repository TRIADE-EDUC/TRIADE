<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: escPos.class.php,v 1.1 2018/02/13 15:02:30 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/printer/escpos-php-development/autoload.php");

class escPos {
	
	public static $connector;
	public static $printers;
	public static $barcode;
	
	public function __construct(){
	}
	
	public static function init() {
		if (isset(static::$connector)) {
			return;
		}
		static::$connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();

		static::$printers = array(
			'epson' => new \Mike42\Escpos\Printer(static::$connector),
			'star' => new \Mike42\Escpos\Printer(static::$connector, \Mike42\Escpos\CapabilityProfile::load("SP2000"))
		);
	}
	
	public static function parseTpl($tpl, $printer = 'epson') {
		static::init();
		
		$tpl_array = explode("\n",$tpl);
		self::initialize($printer);

		foreach ($tpl_array as $line) {
			$line_array = explode("[[printer.",$line);
			if (count($line_array)>1) {
				foreach ($line_array as $k=>$line_part) {
					$text = '';
					if ((!$k) && (trim($line_part))) {
						$text = $line_part;
					} elseif (preg_match('#^([a-z0-9\_\-]+)\]\](.*)$#', $line_part, $out)) {
						if (method_exists(static::$printers[$printer], $out[1])) {
							call_user_func_array(array(static::$printers[$printer], $out[1]), array());
						} elseif (method_exists("escPos", $out[1])) {
							call_user_func_array(array("escPos", $out[1]), array($printer));
						}
						$text = $out[2];
					} else {
						$text = $line_part;
					}
					if ($text) {
						self::print_txt($printer, $text);
					}
				}
			} else {
				self::print_txt($printer, $line);
			}
			self::print_txt($printer, "\n");
		}
		static::$printers[$printer] -> feed();
		static::$printers[$printer] -> cut();
		
		$parsedTpl = static::$connector->getData();
		static::$printers[$printer] -> close();

		return $parsedTpl;
	}
	
	public static function initialize($printer) {
		static::$printers[$printer] -> initialize();
		//Attention, cette commande sélectionne une table de caractères bien spécifique
		//Demandé par Florent lors des tests avec Christophe
		static::$printers[$printer] -> selectCharacterTable(16);
	}
	
	public static function print_txt($printer, $text) {
		if (!empty(static::$barcode)) {
			static::$printers[$printer] -> barcode($text, static::$barcode);
		} else {

			static::$printers[$printer] -> textRaw($text);
		}
		
	}
	
	public static function txt_2height($printer) {
		static::$printers[$printer] -> setTextSize(1, 2);
	}
	
	public static function txt_normal($printer) {
		static::$printers[$printer] -> setTextSize(1, 1);
	}
	
	public static function barcode_txt_blw($printer) {
		static::$printers[$printer] -> setBarcodeTextPosition(\Mike42\Escpos\Printer::BARCODE_TEXT_BELOW);
	}
	
	public static function barcode_font_b($printer) {
		static::$printers[$printer] -> setFont(\Mike42\Escpos\Printer::FONT_B);
	}
	
	public static function barcode_height_a0($printer) {
		//0xA0 = 160 mais cela me parait un peu grand... ?
		static::$printers[$printer] -> setBarcodeHeight(160);
	}
	
	public static function barcode_code39($printer) {
		static::$barcode = \Mike42\Escpos\Printer::BARCODE_CODE39;
	}
	
	public static function no_barcode($printer) {
		static::$barcode = '';
	}
	
}