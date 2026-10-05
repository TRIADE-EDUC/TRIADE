<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_serials.class.php,v 1.3 2024/02/29 15:42:31 tsamson Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
	die("no access");

require_once ($class_path . '/searcher/searcher_records.class.php');

class searcher_serials extends searcher_records
{

	/**
	 *
	 * {@inheritdoc}
	 * @see searcher_records::get_full_results_query()
	 */
	protected function get_full_results_query()
	{
		return "SELECT notice_id AS $this->object_index_key FROM notices WHERE niveau_biblio = 's' AND niveau_hierar = '1'";
	}

	/**
	 *
	 * @param boolean $on_notice
	 * @return string
	 */
	protected static function _get_typdoc_filter($on_notice = false)
	{
		return "";
	}

	/**
	 *
	 * @param boolean $on_notice
	 * @return string
	 */
	protected function _get_clause_filter()
	{
	    return " WHERE $this->object_index_key IN (SELECT notice_id FROM notices WHERE niveau_biblio = 's' AND niveau_hierar = '1') ";
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see searcher_records::_get_search_query()
	 */
	protected function _get_search_query()
	{
		$this->_calc_query_env();
		if ($this->user_query !== "*") {
			$query = $this->aq->get_query_mot($this->object_index_key, $this->object_words_table, $this->object_words_value, $this->object_fields_table, $this->object_fields_value, $this->field_restrict);
			$query .= $this->_get_clause_filter();
		} else {
			$query = $this->get_full_results_query();
		}
		return $query;
	}
}