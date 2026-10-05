<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alerts_contribution.class.php,v 1.4.4.1 2025/02/21 13:39:52 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class alerts_contribution extends alerts {

	protected function get_module() {
		return 'catalog';
	}

	protected function get_section() {
		return 'alert_contribution';
	}

	protected function fetch_data() {
        $this->contribution_to_moderate();
	}

	public function contribution_to_moderate(){
	    //on recherche les contributions à moderer
	    $store = new contribution_area_store();
	    $dataStore = $store->get_datastore();
	    $query = "SELECT * WHERE {
                    ?s <http://www.pmbservices.fr/ontology#has_contributor> ?contributor .
                    optional  {
                        ?s <http://www.pmbservices.fr/ontology#is_draft> ?draft .
                    } .
                    optional  {
                        ?s <http://www.pmbservices.fr/ontology#identifier> ?identifier .
                    } .
                 }";
	    $dataStore->query($query);
	    $results = $dataStore->get_result();
	    $number = 0;
	    foreach ($results as $contrib) {
	        if (empty($contrib->draft) && empty($contrib->identifier)) {
	            $number++;
	        }
	    }
	    //si on a des contributions on affiche l'alerte
	    if($number){
    		$this->add_data('contribution_area', 'alert_contribution_to_moderate', '', '', $number);
		}
	}
}