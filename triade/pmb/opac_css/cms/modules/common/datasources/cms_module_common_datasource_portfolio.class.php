<?php

// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_portfolio.class.php,v 1.14.2.1 2024/12/17 14:36:16 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_common_datasource_portfolio extends cms_module_common_datasource_list
{
    /**
     * Constructeur
     *
     * @param integer $id (optional, default: 0)
     */
    public function __construct($id = 0)
    {
        parent::__construct($id);
        $this->sortable = true;
        if(!isset($this->parameters['sort_by']) || !$this->parameters['sort_by']) {
            $this->parameters['sort_by'] = "document_create_date";
        }
        if(!isset($this->parameters['sort_order']) || !$this->parameters['sort_order']) {
            $this->parameters['sort_order'] = "desc";
        }
        $this->limitable = true;
        $this->paging = true;
    }

    /**
     * On défini les sélecteurs utilisable pour cette source de donnée
     *
     * @return void
     */
    public function get_available_selectors()
    {
        return [
            "cms_module_common_selector_documents",
        ];
    }

    /**
     * Récupération des critères de tri
     *
     * @return array
     */
    protected function get_sort_criterias()
    {
        return [
            "document_create_date",
            "document_filesize",
            "document_title",
            "document_filename",
            "document_mimetype",
        ];
    }

    /**
     * Récupération des données de la source...
     *
     * @return array
     */
    public function get_datas()
    {
        $documents = [];
        //on commence par récupérer l'identifiant retourné par le sélecteur...
        $selector = $this->get_selected_selector();
        if($selector) {
            $docs = $selector->get_value();
            $valid = $this->filter_datas($docs['type_object'], [$docs['num_object']]);
            if(!empty($valid) && ($docs['num_object'] == $valid[0]) && isset($docs['ids']) && is_array($docs['ids'])) {
                $docs['ids'] = $this->array_int_caster($docs['ids']);
                if($this->parameters['sort_by']) {
                    $query = "select id_document from cms_documents where id_document in ('".implode("','", $docs['ids'])."') order by ".$this->parameters['sort_by']." ".$this->parameters['sort_order'];

                    // Tri sur l'identifiant à valeur égale du premier tri
                    $query .= ", id_document ".$this->parameters['sort_order'];

                    $this->parameters['nb_max_elements'] = intval($this->parameters['nb_max_elements'] ?? 0);
                    if ($this->parameters['nb_max_elements']) {
                        $query .= ' limit '.$this->parameters['nb_max_elements'];
                    }
                    $result = pmb_mysql_query($query);
                    if(pmb_mysql_num_rows($result)) {
                        $docs['ids'] = [];
                        while($row = pmb_mysql_fetch_object($result)) {
                            $docs['ids'][] = $row->id_document;
                        }
                    }
                }
                foreach($docs['ids'] as $document_linked) {
                    $document = new cms_document($document_linked);
                    $documents[] = $document->format_datas();
                }
            }
        }

        // Pagination
        if ($this->paging && isset($this->parameters['paging_activate']) && $this->parameters['paging_activate'] == "on") {
            $paging = $this->inject_paginator($documents);
            $documents = $this->cut_paging_list($documents, $paging);
        } elseif (isset($this->parameters["nb_max_elements"]) && $this->parameters["nb_max_elements"] > 0) {
            $documents = array_slice($documents, 0, $this->parameters["nb_max_elements"]);
        }

        return [
            'documents' => $documents,
            'nb_documents' => count($documents),
            'type_object' => $docs['type_object'],
            'num_object' => $docs['num_object'],
            'paging' => $paging ?? [],
        ];
    }

    /**
     * Retourne la structure des données
     *
     * @return array
     */
    public function get_format_data_structure()
    {
        return [
            [
                'var' => "documents",
                'desc' => $this->msg['cms_module_common_datasource_portfolio_documents'],
                'children' => $this->prefix_var_tree(cms_document::get_format_data_structure(), "documents[i]"),
            ],
            [
                'var' => "nb_documents",
                'desc' => $this->msg['cms_module_common_datasource_portfolio_nb_documents'],
            ],
            [
                'var' => "type_object",
                'desc' => $this->msg['cms_module_common_datasource_portfolio_type_object'],
            ],
            [
                'var' => "num_object",
                'desc' => $this->msg['cms_module_common_datasource_portfolio_num_object'],
            ],
        ];
    }
}
