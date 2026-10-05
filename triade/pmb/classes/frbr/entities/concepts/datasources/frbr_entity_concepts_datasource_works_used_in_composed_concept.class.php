<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_concepts_datasource_works_used_in_composed_concept.class.php,v 1.1 2019/01/10 15:39:47 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_concepts_datasource_works_used_in_composed_concept extends frbr_entity_concepts_datasource_entities_used_in_composed_concept{
	
	public function __construct($id=0){
		$this->entity_type = 'works';
		$this->object_type = TYPE_TITRE_UNIFORME;
		parent::__construct($id);
	}
}