<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_integrator_expl.class.php,v 1.5 2023/06/01 11:57:35 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_integration/rdf_entities_integrator.class.php');
require_once($class_path.'/expl.class.php');

class rdf_entities_integrator_expl extends rdf_entities_integrator {
	
	protected $table_name = 'exemplaires';
	
	protected $table_key = 'expl_id';
	
	protected $notice_id = 0;
	
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
		    if(!empty($this->notice_id)){
		        $rqt = "UPDATE exemplaires SET expl_notice = '{$this->notice_id}' WHERE expl_id = '$this->entity_id'";
		        pmb_mysql_query($rqt);
		    }
			//Gestion du cas ou on est sur la notice d'un bulletin
			$bulletin_id = $this->get_bull_id($this->base_query_elements['expl_notice']);
			if($bulletin_id !== false){
				$query = "UPDATE exemplaires SET expl_bulletin = '{$bulletin_id}' WHERE expl_id = '$this->entity_id'";
				pmb_mysql_query($query);
			}
			
			$query = 'insert into audit (type_obj, object_id, user_id, type_modif, info, type_user) ';
			$query.= 'values ("'.AUDIT_EXPL.'", "'.$this->entity_id.'", "'.$this->contributor_id.'", "'.$this->integration_type.'", "'.$this->create_audit_comment($uri).'", "'.$this->contributor_type.'")';
			pmb_mysql_query($query);
		}
	}
	/**
	 * Méthode retournant l'id de bulletin d'une notice s'il existe, renvoie false sinon
	 * @return int | boolean
	 */
	protected function get_bull_id($notice_id)
	{
		$query = "SELECT bulletin_id FROM bulletins WHERE num_notice = '".$notice_id . "'";
	    $result = pmb_mysql_query($query);
	    if(pmb_mysql_num_rows($result)){
	        $params = pmb_mysql_fetch_object($result);
	        return $params->bulletin_id;
	    } else {
	        return false;
	    }
	}
}