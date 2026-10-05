<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alerts_empr_categ.class.php,v 1.2 2024/02/21 08:24:37 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class alerts_empr_categ extends alerts {

	protected function get_module() {
		return 'edit';
	}

	protected function get_section() {
		return 'empr_categ_alert';
	}

	protected function fetch_data() {
		global $deflt2docs_location,$pmb_lecteurs_localises;

		$this->data = array();

		// comptage des emprunteurs qui n'ont pas le droit d'être dans la catégorie
		$query = "select count(*) from empr left join empr_categ on empr_categ = id_categ_empr ";
		$query .= " where ((((age_min<> 0) || (age_max <> 0)) && (age_max >= age_min)) && (((DATE_FORMAT( curdate() , '%Y' )-empr_year) < age_min) || ((DATE_FORMAT( curdate() , '%Y' )-empr_year) > age_max)))";
		// restriction localisation le cas échéant
		if ($pmb_lecteurs_localises) {
			$query .= " AND empr_location='$deflt2docs_location' ";
		}
		$result = pmb_mysql_query($query);
		$number = pmb_mysql_result($result, 0, 0);
		if($number) {
		    $this->add_data('empr', 'empr_change_categ_todo', 'categ_change', '', $number);
		}
	}

}