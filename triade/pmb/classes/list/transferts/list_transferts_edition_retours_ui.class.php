<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_transferts_edition_retours_ui.class.php,v 1.2 2020/11/05 09:39:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_transferts_edition_retours_ui extends list_transferts_edition_ui {
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('site_origine');
		$this->add_selected_filter('site_destination');
		$this->add_selected_filter('f_etat_date');
	}
}