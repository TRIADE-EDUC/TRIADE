<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_integrator_expl.class.php,v 1.1 2018-12-19 13:05:02 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_integration/rdf_entities_integrator.class.php');
require_once($class_path.'/expl.class.php');

class rdf_entities_integrator_expl extends rdf_entities_integrator {
	
	protected $table_name = 'exemplaires';
	
	protected $table_key = 'expl_id';
	
	protected $ppersos_prefix = 'expl';
	
	protected function init_map_fields() {
		$this->map_fields = array_merge(parent::init_map_fields(), array(
				'http://www.pmbservices.fr/ontology#cb' => 'expl_cb',
				'http://www.pmbservices.fr/ontology#typdoc' => 'expl_typdoc',
				'http://www.pmbservices.fr/ontology#cote' => 'expl_cote',
				'http://www.pmbservices.fr/ontology#docs_section' => 'expl_section',
				'http://www.pmbservices.fr/ontology#has_expl_status' => 'expl_statut',
				'http://www.pmbservices.fr/ontology#expl_location' => 'expl_location',
				'http://www.pmbservices.fr/ontology#expl_codestat' => 'expl_codestat',
				'http://www.pmbservices.fr/ontology#note' => 'expl_note',
				'http://www.pmbservices.fr/ontology#price' => 'expl_prix',
				'http://www.pmbservices.fr/ontology#owner' => 'expl_owner',
				'http://www.pmbservices.fr/ontology#comment' => 'expl_comment'
		));
		return $this->map_fields;
	}
	
	protected function init_base_query_elements() {
		// On définit les valeurs par défaut
		$this->base_query_elements = parent::init_base_query_elements();
		if (!$this->entity_id) {
			$this->base_query_elements = array_merge($this->base_query_elements, array(
					'create_date' => date('Y-m-d H:i:s')
			));
		}
	}
	
	protected function init_foreign_fields() {
		$this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
				'http://www.pmbservices.fr/ontology#has_record' => 'expl_notice'
		));
		return $this->foreign_fields;
	}
	
	protected function post_create($uri) {
		if ($this->entity_id) {
			$query = 'insert into audit (type_obj, object_id, user_id, type_modif, info, type_user) ';
			$query.= 'values ("'.AUDIT_EXPL.'", "'.$this->entity_id.'", "'.$this->contributor_id.'", "'.$this->integration_type.'", "'.addslashes(json_encode(array("uri" => $uri))).'", "'.$this->contributor_type.'")';
			pmb_mysql_query($query);
			
			if ($this->integration_type == 1) {
				$expl = new exemplaire('', $this->entity_id);
				$cb = $expl->gen_cb();
				$query = 'UPDATE exemplaires SET expl_cb = "'.$cb.'" WHERE expl_id = '.$this->entity_id;
				pmb_mysql_query($query);
			}
		}
	}
}