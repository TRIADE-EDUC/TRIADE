<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: metapace.class.php,v 1.6 2019/12/30 15:58:50 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
global $include_path;

require_once($include_path."/h2o/h2o.php");

class metapace {
	
	public $data;	// info biblo, empr, expl utile à l'impression

	
	public function gen_print($data,$tpl=''){
	global $msg;

	$default_template = 
"\x1B\x40\x1B\x21\x16{{biblio.name}}\x1B\x21\x04
{{biblio.adr1}}
{{biblio.town}}
{{biblio.phone}}
{{biblio.email}}

".$msg["fpdf_edite"]." ".formatdate(date("Y-m-d",time()))."\n
Emprunteur:
{% for empr in empr_list %}
 {{empr.name}} {{empr.fistname}}
{% endfor %}
{% for expl in expl_list %}

{{expl.tit}} 
 {{expl.cb}}
 {{expl.location}} / {{expl.section}} / {{expl.cote}}
 ".$msg["printer_pret_date"]."{{expl.date_pret}}. \x1B\x21\x14".$msg["printer_retour_date"]."{{expl.date_retour}} \x1B\x21\x04
 ______________________________________
{% endfor %}
\x1D\x56\x41 \x1B\x40";	

		if(!$tpl) {
			$tpl = $default_template;
		}	
	
		return H2o::parseString($tpl)->render($data);	
	}

	
	public function gen_print_transactions($data,$tpl=''){
		global $msg,$pmb_gestion_devise;
	
		$default_template = 
"\x1B\x40\x1B\x21\x16{{biblio.name}}\x1B\x21\x04
{{biblio.adr1}}
{{biblio.town}}
{{biblio.phone}}
{{biblio.email}}

".$msg["fpdf_edite"]." ".formatdate(date("Y-m-d",time()))."\n
Emprunteur:
{% for empr in empr_list %}
{{empr.name}} {{empr.fistname}}
{% endfor %}

{% for data in empr_list %}
".$msg["transactype_ticket_solde"]."{{compte_autre.solde}} ".$pmb_gestion_devise."
{% endfor %}


\x1D\x56\x41 \x1B\x40";
	
		if(!$tpl) {
			$tpl = $default_template;
		}	
	
		return H2o::parseString($tpl)->render($data);	
	}
	
	
	public function gen_print_card($data,$tpl=''){
		global $msg;

		$default_template = 
"\x1B\x40
{% for empr in empr_list %}
\x1B\x21\x16
{{empr.name}} {{empr.fistname}}
\x1B\x21\x04
\x1D\x48\x2\x1D\x66\x1\x1D\x68\xA0\x1D\x6B\x04{{empr.cb}}\x00
{% endfor %}
\x1D\x56\x41 \x1B\x40";
		
		if(!$tpl) {
			$tpl = $default_template;
		}
		return H2o::parseString($tpl)->render($data);
		
	}
	
}