<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_artecampus_datasource_artecampus.class.php,v 1.1.2.1 2024/07/18 12:38:21 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_artecampus_datasource_artecampus extends cms_module_common_datasource_list
{
    public function __construct($id = 0)
    {
        parent::__construct($id);
        $this->sortable = false;
        $this->limitable = false;
    }

    /**
     * Retourne les sélecteurs utilisable pour cette source de donnée
     *
     * @return cms_module_common_selector[]
     */
    public function get_available_selectors()
    {
        return [
            'cms_module_artecampus_selector_artecampus'
        ];
    }

    /**
     * Retourne les critères de tri utilisable pour cette source de donnée
     *
     * @return array
     */
    protected function get_sort_criterias()
    {
        return [];
    }

    /**
     * Récupération des données de la source...
     *
     * @return false|array{connectors: int}
     */
    public function get_datas()
    {
        $selector = $this->get_selected_selector();
        if ($selector) {
            $source_id = $selector->get_value();
            $source_id = intval($source_id);

            $result = pmb_mysql_query('SELECT 1 FROM connectors_sources WHERE id_connector = "artecampus" AND source_id = ' . $source_id);
            if (pmb_mysql_num_rows($result)) {
                return [
                    'connector' => $source_id
                ];
            }
        }
        return false;
    }
}
