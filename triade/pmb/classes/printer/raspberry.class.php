<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: raspberry.class.php,v 1.4 2019/12/30 15:58:50 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path, $class_path;
require_once($include_path."/h2o/h2o.php");
require_once($class_path."/printer/escPos.class.php");

class raspberry {
	
	public $data; // info biblo, empr, expl utile à l'impression
	
	public function __construct(){
	}
	
	private function get_tpls($data, $tpl) {
		global $charset;
		
		$tpl = H2o::parseString($tpl)->render($data);

		$templates = array(
				'epson' => escPos::parseTpl($tpl, 'epson'),
				'star' => escPos::parseTpl($tpl, 'star')
		);
		
		return  $templates;
	}
	
	public function gen_print($data,$tpl=''){
		global $msg;

		$default_template = "[[printer.txt_2height]]{{biblio.name}}[[printer.txt_normal]]
{{biblio.adr1}}
{{biblio.town}}
{{biblio.phone}}
{{biblio.email}}

".$msg["fpdf_edite"]." ".formatdate(date("Y-m-d",time()))."

Emprunteur:
{% for empr in empr_list %}
 {{empr.name}} {{empr.fistname}}
{% endfor %}
{% for expl in expl_list %}

{{expl.tit}} 
 {{expl.cb}}
 {{expl.location}} / {{expl.section}} / {{expl.cote}}
 ".$msg["printer_pret_date"]."{{expl.date_pret}}. [[printer.txt_2height]]".$msg["printer_retour_date"]."{{expl.date_retour}} [[printer.txt_normal]]
 ______________________________________
{% endfor %}
";
		
		if(!$tpl) {
			$tpl = $default_template;
		}
		
		return $this->get_tpls($data, $tpl);
		
	}
	
	public function gen_print_transactions($data,$tpl=''){
		global $msg,$pmb_gestion_devise;
	
		$default_template = "[[printer.txt_2height]]{{biblio.name}}[[printer.txt_normal]]
{{biblio.adr1}}
{{biblio.town}}
{{biblio.phone}}
{{biblio.email}}

".$msg["fpdf_edite"]." ".formatdate(date("Y-m-d",time()))."

Emprunteur:
{% for empr in empr_list %}
{{empr.name}} {{empr.fistname}}
{% endfor %}

{% for data in empr_list %}
".$msg["transactype_ticket_solde"]."{{compte_autre.solde}} ".$pmb_gestion_devise."
{% endfor %}


";
	
		if(!$tpl) {
			$tpl = $default_template;
		}	
	
		return $this->get_tpls($data, $tpl);
	}
	
	public function gen_print_card($data,$tpl=''){
		global $msg;
		
		$default_template = 
"
{% for empr in empr_list %}
[[printer.txt_2height]]
{{empr.name}} {{empr.fistname}}
[[printer.txt_normal]]
[[printer.barcode_txt_blw]][[printer.barcode_font_b]][[printer.barcode_height_a0]][[printer.barcode_code39]]{{empr.cb}}[[printer.no_barcode]]
{% endfor %}
";
		
		if(!$tpl) {
			$tpl = $default_template;
		}
		
		return $this->get_tpls($data, $tpl);
	}

	public static function get_selector_options($selected=0) {
		global $pmb_printer_list, $charset, $msg;
		
		$options = "<option value='0' ".(!$selected ? "selected='selected'" : "").">".$msg["user_deflt_printer_not_selected"]."</option>";
		if (trim($pmb_printer_list)) {
			$list_printers = explode(";", $pmb_printer_list);
			foreach ($list_printers as $printer) {
				$printer = trim($printer);
				if (preg_match('#^ *(\d+) *\_ *(.+?) *(\(([\d\.:]+)\))? *$#',$printer,$out)) {
					$options.= "
						<option value='".$out[1]."' ".($out[1]==$selected ? "selected='selected'" : "").">".htmlentities($out[2],ENT_QUOTES,$charset)."</option>";
				}
			}
		}
		
		return $options;
	}
	
}