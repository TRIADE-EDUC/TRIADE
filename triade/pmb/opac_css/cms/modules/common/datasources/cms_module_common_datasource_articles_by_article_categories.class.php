<?php

// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_articles_by_article_categories.class.php,v 1.9.2.1 2024/12/17 14:36:16 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_common_datasource_articles_by_article_categories extends cms_module_common_datasource_list
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
        $this->limitable = true;
        $this->paging = true;
    }

    /**
     * On défini les sélecteurs utilisable pour cette source de donnée
     *
     * @return array
     */
    public function get_available_selectors()
    {
        return [
            "cms_module_common_selector_article",
            "cms_module_common_selector_env_var",
        ];
    }

    /**
     * On défini les critères de tri utilisable pour cette source de donnée
     *
     * @return array
     */
    protected function get_sort_criterias()
    {
        return  [
            "publication_date",
            "id_article",
            "article_title",
            "article_order",
            "rand()",
        ];
    }

    public function get_form()
    {
        $form = parent::get_form();
        if(!isset($this->parameters['operator_between_authorities'])) {
            $this->parameters['operator_between_authorities'] = 'or';
        }
        $form .= '
        <div class="row">
            <div class="colonne3"><label for="'.$this->get_form_value_name('autopostage').'">'.$this->format_text($this->msg['cms_module_common_datasource_categories_use_autopostage']).'</label></div>
            <div class="colonne_suite">
                '.$this->format_text($this->msg['yes']).' <input type="radio" '.($this->parameters['autopostage'] == 1 ? 'checked="checked"' : '').' name="'.$this->get_form_value_name('autopostage').'" value="1"/>
                '.$this->format_text($this->msg['no']).' <input type="radio" '.($this->parameters['autopostage'] == 0 ? 'checked="checked"' : '').' name="'.$this->get_form_value_name('autopostage').'" value="0"/></div>
        </div>
        <div class="row">
            <div class="colonne3"><label for="'.$this->get_form_value_name('operator_between_authorities').'">'.$this->format_text($this->msg['cms_module_common_datasource_operator_between_authorities']).'</label></div>
            <div class="colonne_suite">
                '.$this->format_text($this->msg['cms_module_common_datasource_operator_between_authorities_or']).' <input type="radio" '.($this->parameters['operator_between_authorities'] == 'or' ? 'checked="checked"' : '').' name="'.$this->get_form_value_name('operator_between_authorities').'" value="or"/>
                '.$this->format_text($this->msg['cms_module_common_datasource_operator_between_authorities_and']).' <input type="radio" '.($this->parameters['operator_between_authorities'] == 'and' ? 'checked="checked"' : '').' name="'.$this->get_form_value_name('operator_between_authorities').'" value="and"/></div>
        </div>';

        return $form;
    }

    /**
     * Sauvegarde des données de configuration de la source
     *
     * @return boolean
     */
    public function save_form()
    {
        $this->parameters['autopostage'] = $this->get_value_from_form('autopostage');
        $this->parameters['operator_between_authorities'] = $this->get_value_from_form('operator_between_authorities');
        return parent::save_form();
    }


    /**
     * Récupération des données de la source...
     *
     * @return array|false
     */
    public function get_datas()
    {
        $selector = $this->get_selected_selector();
        if (!$selector) {
            return false;
        }

        if (!isset($this->parameters['operator_between_authorities'])) {
            $this->parameters['operator_between_authorities'] = 'or';
        }

        $num_article = intval($selector->get_value() ?? 0);
        switch ($this->parameters["operator_between_authorities"]) {
            case 'and':
                if($this->parameters['autopostage']) {
                    $query = "select distinct cms_articles_descriptors.num_noeud
					from cms_articles_descriptors
					join noeuds as article_noeuds on article_noeuds.id_noeud = cms_articles_descriptors.num_noeud
					join noeuds as categ_noeuds on categ_noeuds.path like concat(article_noeuds.path,'%') and article_noeuds.id_noeud != categ_noeuds.id_noeud
					where cms_articles_descriptors.num_article='". $num_article ."'";
                } else {
                    $query = "select distinct cms_articles_descriptors.num_noeud
					from cms_articles_descriptors
					where cms_articles_descriptors.num_article = '". $num_article ."'";
                }

                $result = pmb_mysql_query($query);
                $descriptors = [];

                if ($result && (pmb_mysql_num_rows($result) > 0)) {
                    while($row = pmb_mysql_fetch_object($result)) {
                        $descriptors[] = $row->num_noeud;
                    }
                }

                if (count($descriptors)) {
                    $query = "select id_article,if(article_start_date != '0000-00-00 00:00:00',article_start_date,article_creation_date) as publication_date
					from cms_articles join cms_articles_descriptors on id_article=num_article
					where cms_articles_descriptors.num_noeud IN (".implode(',', $descriptors).")
					group by id_article
					having count(id_article) = ".count($descriptors);
                    if ($this->parameters["sort_by"] != "") {
                        $query .= " order by ".$this->parameters["sort_by"];
                        if ($this->parameters["sort_order"] != "") {
                            $query .= " ".$this->parameters["sort_order"];
                        }
                    }
                    $result = pmb_mysql_query($query);
                } else {
                    $result = false;
                }
                break;

            case 'or':
            default:
                if (isset($this->parameters['autopostage']) && $this->parameters['autopostage']) {
                    $query = 'select id_article,if(article_start_date != "0000-00-00 00:00:00",article_start_date,article_creation_date) as publication_date
					from cms_articles_descriptors
					join noeuds as articles_noeuds on articles_noeuds.id_noeud = cms_articles_descriptors.num_noeud
					join noeuds as categ_noeuds on categ_noeuds.path like concat(articles_noeuds.path,"%") and articles_noeuds.id_noeud != categ_noeuds.id_noeud
					join cms_articles_descriptors as cmd on categ_noeuds.id_noeud = cmd.num_noeud
					join cms_articles on cmd.num_article = id_article
					where cms_articles_descriptors.num_article='. $num_article .' group by id_article';
                } else {
                    $query = "select distinct id_article,if(article_start_date != '0000-00-00 00:00:00',article_start_date,article_creation_date) as publication_date
					from cms_articles
					join cms_articles_descriptors on id_article=num_article
					where num_article != '". $num_article ."' and num_noeud in (select num_noeud from cms_articles_descriptors where num_article = '". $num_article ."')";
                }
                if ($this->parameters["sort_by"] != "") {
                    $query .= " order by ".$this->parameters["sort_by"];
                    if ($this->parameters["sort_order"] != "") {
                        $query .= " ".$this->parameters["sort_order"];
                    }
                }
                $result = pmb_mysql_query($query);
                break;
        }

        $return = [];
        $articles = [];

        if ($result && pmb_mysql_num_rows($result) > 0) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $articles[] = $row->id_article;
            }
        }
        $return['articles'] = $this->filter_datas("articles", $articles);

        // Pagination
        if ($this->paging && isset($this->parameters['paging_activate']) && $this->parameters['paging_activate'] == "on") {
            $return["paging"] = $this->inject_paginator($return['articles']);
            $return['articles'] = $this->cut_paging_list($return['articles'], $return["paging"]);
        } elseif ($this->parameters["nb_max_elements"] > 0) {
            $return['articles'] = array_slice($return['articles'], 0, $this->parameters["nb_max_elements"]);
        }

        return $return;
    }
}
