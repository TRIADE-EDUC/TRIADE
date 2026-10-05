<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_loans_archives_edition_ui.class.php,v 1.4 2021/04/21 08:10:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_loans_archives_edition_ui extends list_loans_archives_ui {
		
	protected function get_form_title() {
		global $msg;
		
		$form_title = $msg['loans_archives'];
		return $form_title;
	}
		
	protected function init_default_columns() {
		$this->add_column('cb_expl');
		$this->add_column('arc_expl_cote');
		$this->add_column('arc_expl_typdoc');
		$this->add_column('record');
		$this->add_column('empr');
		$this->add_column('arc_debut');
		$this->add_column('arc_fin');
	}
	
	protected function get_display_spreadsheet_title() {
		global $msg;
		$this->spreadsheet->write_string(0,0,$msg[1110]." : ".$msg['loans_archives']);
	}
	
	protected function get_html_title() {
		global $msg;
		return "<h1>".$msg[1110]." : ".$msg['loans_archives']."</h1>";
	}
}