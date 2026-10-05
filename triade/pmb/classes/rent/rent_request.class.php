<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_request.class.php,v 1.7 2023/08/28 14:01:12 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Spipu\Html2Pdf\Html2Pdf;

require_once($class_path."/rent/rent_account.class.php");

class rent_request extends rent_account {
	
	public function __construct($id) {
		parent::__construct($id);
		$this->object_type = 'request';
	}
	
	/**
	 * Retourne la fonction JS d'initialisation du formulaire (display)
	 */
	protected function get_function_form_hide_fields() {
		return 'request_form_hide_fields();';
	}
		
	public function gen_command() {
		global $msg, $include_path, $charset;
	
		$tpl = $include_path.'/templates/rent/rent_account_command.tpl.html';
		if (file_exists($include_path.'/templates/rent/rent_account_command_subst.tpl.html')) {
			$tpl = $include_path.'/templates/rent/rent_account_command_subst.tpl.html';
		}
		$h2o = H2o_collection::get_instance($tpl);
		$command_tpl = $h2o->render(array('account' => $this));
		if($charset != "utf-8"){
			$command_tpl=encoding_normalize::utf8_normalize($command_tpl);
		}
		$html2pdf = new Html2Pdf('L','A4','fr');
		$html2pdf->writeHTML($command_tpl);
		$html2pdf->output(sprintf($msg['acquisition_request_pdf_filename'], $this->get_supplier()->raison_sociale, $this->get_id()).'.pdf','D');
	}
}