<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_bulletins_a2z_ui.class.php,v 1.2.2.1 2024/11/15 10:37:26 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_opac_bulletins_a2z_ui extends list_opac_bulletins_ui {
	
    protected function get_display_pager() {
        global $nb_per_page_custom;
        // constitution des liens
        if (isset($nb_per_page_custom)) {
            $this->pager['nb_per_page'] = intval($nb_per_page_custom);
        }
        $url_page = "javascript:changepage(!!page!!,".$this->filters['serial_id'].",this)";
        $nb_per_page_custom_url = "javascript:nbPerPage(!!nb_per_page_custom!!, ".$this->filters['serial_id'].")";
        $action = "show_perio(".$this->filters['serial_id'].");return false;";
        if ($this->pager['nb_page']>1) {
            $navBar = getNavbar($this->pager['page'], $this->pager['nb_results'], $this->pager['nb_per_page'], $url_page, $nb_per_page_custom_url, '#');
            $navBar->setOnsubmit($action);
            return $navBar->getPaginatorPerio();
        }
    }
    
	protected function _cell_is_sortable($name) {
	    return false;
	}
	
	protected function get_js_sort_script_sort() {
	    return '';
	}
}