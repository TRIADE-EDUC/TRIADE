<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_authperso_datasource_responsabilities.class.php,v 1.1.2.2 2025/04/25 14:28:30 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_authperso_datasource_responsabilities extends frbr_entity_common_datasource_authors
{
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas = array())
	{
		$query = "SELECT responsability_authperso_author as id, responsability_authperso_num as parent FROM responsability_authperso 
			WHERE responsability_authperso_num IN (" . implode(',', $datas) . ")";

		if (!empty($this->parameters->author_function)) {
			if (is_array($this->parameters->author_function)) {
				$query .= " AND responsability_authperso_fonction IN ('" . implode("','", $this->parameters->author_function) . "')";
			} else {
				$query .= " AND responsability_authperso_fonction = '" . $this->parameters->author_function . "'";
			}
		}

		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);

		return $datas;
	}

	public function get_form()
	{
		if (!isset($this->parameters->author_function)) {
			$this->parameters->author_function = '';
		}
		$form = parent::get_form();
		$form .= "
            <div class='row'>
				<div class='colonne3'>
					<label for='datanode_work_link_type'>" . $this->format_text($this->msg['frbr_entity_authperso_datasource_authors_function']) . "</label>
				</div>
				<div class='colonne-suite'>
					" . $this->get_author_function_selector($this->parameters->author_function) . "
				</div>
			</div>";
		return $form;
	}
}
