<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: webdav_group_standard.class.php,v 1.5 2023/03/09 15:23:39 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path.'/admin/connecteurs/out/webdav/groups/webdav_group.class.php');
require_once($class_path."/thesaurus.class.php");

class webdav_group_standard extends webdav_group {
	
	public function get_config_form(){
		global $charset;
		global $thesaurus_default; 
		global $deflt_concept_scheme;
		global $class_path;
		
		if(empty($this->config['used_thesaurus'])){
			$this->config['used_thesaurus'] = $thesaurus_default;
		}
		if(empty($this->config['only_with_notices'])){
		    $this->config['only_with_notices'] = 0;
		}
		if(empty($this->config['used_schema'])){
		    $this->config['used_schema'] = $deflt_concept_scheme;
		}
		
		$result = "
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label for='used_thesaurus'>".htmlentities($this->msg['webdav_user_thesaurus'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				<select name='used_thesaurus'>";
		$liste_thesaurus = thesaurus::getThesaurusList();
		foreach($liste_thesaurus as $id_thesaurus=>$libelle_thesaurus) {
			$result.= "
					<option value='".$id_thesaurus."' ".($id_thesaurus == $this->config['used_thesaurus'] ? "selected='selected'" : "").">".htmlentities($libelle_thesaurus,ENT_QUOTES,$charset)."</option>";
		}
		$result.= "
				</select>
			</div>
			<div class='row'>&nbsp;</div>
        	<div class='row'>
				<label for='used_schema'>".htmlentities($this->msg['webdav_user_schema'],ENT_QUOTES,$charset)."</label>
			</div>
            <div class='row'>";
		$onto_store_config = array(
				    /* db */
				    'db_name' => DATA_BASE,
				    'db_user' => USER_NAME,
				    'db_pwd' => USER_PASS,
				    'db_host' => SQL_SERVER,
				    /* store */
				    'store_name' => 'ontology',
				    /* stop after 100 errors */
				    'max_errors' => 100,
				    'store_strip_mb_comp_str' => 0
				);
		$data_store_config = array(
		    /* db */
		    'db_name' => DATA_BASE,
		    'db_user' => USER_NAME,
		    'db_pwd' => USER_PASS,
		    'db_host' => SQL_SERVER,
		    /* store */
		    'store_name' => 'rdfstore',
		    /* stop after 100 errors */
		    'max_errors' => 100,
		    'store_strip_mb_comp_str' => 0
		);
		$onto_handler = new onto_handler($class_path."/rdf/skos_pmb.rdf", "arc2", $onto_store_config, "arc2", $data_store_config, ONTOLOGY_NAMESPACE, 'http://www.w3.org/2004/02/skos/core#prefLabel', 'http://www.w3.org/2004/02/skos/core#ConceptScheme');
		$params = new onto_param();
		$params->concept_scheme = [$this->config['used_schema']];
		$onto_controler = new onto_skos_controler($onto_handler, $params);
		
		$result .= onto_skos_concept_ui::get_scheme_list_selector($onto_controler, $params, false,  '', 'used_schema');
	
		$result.= "

			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label for='only_with_notices'>".htmlentities($this->msg['webdav_only_with_notices'],ENT_QUOTES,$charset)."</label>
			</div>
			<div class='row'>
				".$this->msg['webdav_yes']."&nbsp;<input type='radio' value='1' name='only_with_notices' ".($this->config['only_with_notices'] ? "checked='checked'" : "")."/>
				".$this->msg['webdav_no']."&nbsp;<input type='radio' value='0' name='only_with_notices' ".($this->config['only_with_notices'] ? "" : "checked='checked'")."/>
			</div>";
		
		$result.= $this->get_collections_tree();
		return $result;
	}
	
	public function get_config_form_script() {
		return $this->get_collections_tree_script();
	}
	
	public static function update_config_from_form(){
		global $used_thesaurus;
		global $only_with_notices;
		global $used_schema;
		
		return array_merge(parent::update_config_from_form(), array(
				'used_thesaurus' => $used_thesaurus,
				'only_with_notices' => $only_with_notices,
                'used_schema' => $used_schema
		));
	}
}