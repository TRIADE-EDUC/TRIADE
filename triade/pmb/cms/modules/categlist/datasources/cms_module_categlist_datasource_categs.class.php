<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_categlist_datasource_categs.class.php,v 1.4 2022/05/31 08:23:04 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

class cms_module_categlist_datasource_categs extends cms_module_common_datasource_list {

    public function __construct($id = 0) {
        parent::__construct($id);
        $this->sortable = false;
        $this->limitable = false;
    }

    /*
     * On défini les sélecteurs utilisable pour cette source de donnée
     */
    public function get_available_selectors() {
        return array(
            "cms_module_common_selector_search_result"
        );
    }

    /*
     * Récupération des données de la source
     */
    public function get_datas() {
        global $lang;
        // Recuperation de la recherche
        $selector = $this->get_selected_selector();
        if ($selector) {
            $tab_word_query = array();
            $tab_word_query = explode(" ", $selector->get_value());
            if (count($tab_word_query) > 0) {
                $return = array();
                foreach ($tab_word_query as $word) {
                    // terms est un alias de la table categories permettant d'éviter toute
                    // ambiguité (récuperation d'un libelle à partir d'un libelle)
                    if ($word != '' && $word != '*') {
                        $word = $this->treatTroncature($word);
                        $query = "
                        select noeuds.num_renvoi_voir as id_retenue,
                        terms.libelle_categorie as libelle_retenue,
                        categories.num_noeud as id_rejetee,
                        categories.libelle_categorie as libelle_rejetee
                        from noeuds
                        join categories on noeuds.id_noeud = categories.num_noeud
                        join categories as terms on noeuds.num_renvoi_voir = terms.num_noeud
                        where categories.index_categorie like '" . $word . "'
                        and categories.langue = '" . $lang . "'
                        and terms.langue = '" . $lang . "'";
                        $result = pmb_mysql_query($query);
                        while ($row = pmb_mysql_fetch_object($result)) {
                            $return[] = array(
                                'libelle_rejetee' => $row->libelle_rejetee,
                                'id_rejetee' => $row->id_rejetee,
                                'libelle_retenue' => $row->libelle_retenue,
                                'id_retenue' => $row->id_retenue
                            );
                        }
                    }
                }
                return array(
                    'terms' => $return
                );
            }
        }
        return false;
    }

    public function get_format_data_structure() {
        return array(
            array(
                'var' => "terms",
                'desc' => $this->msg['cms_module_categlist_datasource_terms_nom_desc'],
                'children' => array(
                    array(
                        'var' => "terms[i].id_rejetee",
                        'desc' => $this->msg['cms_module_categlist_datasource_terms_id_rejetee']
                    ),
                    array(
                        'var' => "terms[i].libelle_rejetee",
                        'desc' => $this->msg['cms_module_categlist_datasource_terms_libelle_rejetee']
                    ),
                    array(
                        'var' => "terms[i].id_retenue",
                        'desc' => $this->msg['cms_module_categlist_datasource_terms_id_retenue']
                    ),
                    array(
                        'var' => "terms[i].libelle_retenue",
                        'desc' => $this->msg['cms_module_categlist_datasource_terms_libelle_retenue']
                    )
                )
            )
        )
        ;
    }

    /**
     * Fonction permettant de traiter la troncature sur un mot de la requete de recherche
     *
     * @param string $word            
     * @return string
     */
    private function treatTroncature($word) {
        global $opac_allow_term_troncat_search; // Variable globale définissant si la troncature automatique a droite est activée ou non
        if ($opac_allow_term_troncat_search) {
            if (substr($word, strlen($word) - 1, 1) != '*') {
                $word = $word . '*';
            }
        }
        if (strpos($word, '*') !== false) { // troncature à réaliser
            $word = (substr($word, 0, 1) == '*') ? preg_replace('/\*/', '%', $word, 1) : '% ' . $word;
            $word = (substr($word, strlen($word) - 1, 1) == '*') ? preg_replace('/\*/', '%', $word, 1) : $word . ' %';
        } else {
            $word = '% ' . $word . ' %';
        }
        return addslashes($word);
    }
}