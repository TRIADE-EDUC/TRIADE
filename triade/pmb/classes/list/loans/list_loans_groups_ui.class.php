<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_loans_groups_ui.class.php,v 1.8.6.1 2024/05/03 12:47:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_loans_groups_ui extends list_loans_ui {
    
    protected function init_default_applied_group() {
        $this->applied_group = array(0 => 'groups');
    }
    
    protected function _get_query_base() {
        $query = 'select pret_idempr, pret_idexpl, group_concat(libelle_groupe) as groups
			FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id )
				LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id)
				LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id)
				JOIN pret ON pret_idexpl = expl_id
				JOIN empr ON empr.id_empr = pret.pret_idempr
                JOIN empr_groupe ON empr_groupe.empr_id = empr.id_empr 
                JOIN groupe ON groupe.id_groupe = empr_groupe.groupe_id
				JOIN docs_type ON expl_typdoc = idtyp_doc
				';
        return $query;
    }

    /**
     * Initialisation des filtres disponibles
     */
    protected function init_available_filters() {
		global $empr_groupes_localises;
        
        parent::init_available_filters();
        if($empr_groupes_localises) {
            $this->available_filters['main_fields']['empr_resp_group_location'] = 'empr_resp_group_location';
        }
       $this->available_filters['custom_fields'] = array();
    }
    
    protected function init_default_selected_filters() {
        global $empr_groupes_localises;
        $this->selected_filters = array();
        if($empr_groupes_localises) {
            $this->add_selected_filter('empr_resp_group_location');
        }
    }
    
    protected function init_default_applied_sort() {
        $this->add_applied_sort('groups');
    }
    
    protected function _get_query_order() {
        $query_order = parent::_get_query_order();
        if(strpos($query_order, 'GROUP BY') !== false) {
            //GROUP BY déjà présent
            return $query_order;
        }
        return ' GROUP BY pret_idempr, pret_idexpl '.$query_order;
    }
    
    /**
     * Initialisation de la pagination par défaut
     */
    protected function init_default_pager() {
        parent::init_default_pager();
        $this->pager['nb_per_page'] = 10;
        $this->pager['nb_per_page_on_group'] = true;
    }
}