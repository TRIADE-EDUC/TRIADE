<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_authperso_datasource_concepts_indexing.class.php,v 1.2 2021/03/01 14:02:12 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_authperso_datasource_concepts_indexing extends frbr_entity_common_datasource_concept {
	
    protected $origin_type = TYPE_AUTHPERSO;
    
	public function __construct($id=0){
		$this->entity_type = 'concepts';
		parent::__construct($id);
	}
	
	public function get_sub_form() {
	    $form= "
				<div class='row'>
					<div class='colonne3'>
						<label for='datanode_datasource_nb_max_elements'>".$this->format_text($this->msg['frbr_entity_common_datasource_nb_max_elements'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='datanode_datasource_nb_max_elements' value='".(isset($this->parameters->nb_max_elements) ? $this->parameters->nb_max_elements : '15')."'/>
					</div>
				</div>
                <div id='sub_datasource_form'>
					<input type='hidden' name='datanode_entity_type' id='datanode_entity_type' value='".$this->get_entity_type()."' />
				</div>";
	    return $form;
	}
}