<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Moment.php,v 1.13.4.1 2025/03/07 15:33:04 dbellamy Exp $

namespace Sabre\PMB\Music;

use Sabre\DAV;
use encoding_normalize;

class Moment extends Collection {
	protected $concept;

	public function __construct($name,$config) {
		parent::__construct($config);
		$this->concept =  new \concept(substr($this->get_code_from_name($name),1));
		$this->type = "moment";
	}

	public function getName() {
		return $this->format_name($this->concept->get_display_label()." (C".$this->concept->get_id().")");
	}

	public function getChildren() {
		$children = array();
		$moment_concept_id = $this->get_moment_concept()->get_id();
		$query = "select explnum_id from explnum join index_concept on explnum_id = num_object and type_object = ".TYPE_EXPLNUM." where explnum_mimetype!= 'URL' and explnum_notice = ".$this->parentNode->get_notice_id()." and num_concept = ".$moment_concept_id;
		$query = $this->filterExplnums($query);
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$children[] = $this->getChild("(E".$row->explnum_id.")");
			}
		}
		return $children;
	}

	public function createFile($name, $data = null) {
		global $charset,$base_path,$id_rep;

		if($this->check_write_permission()){
			if($charset !=='utf-8'){
				$name = encoding_normalize::utf8_decode($name);
			}

			$filename = realpath($base_path."/temp/")."/webdav_".md5($name.time()).".".extension_fichier($name);
			$fp = fopen($filename, "w");
			if(!$fp){
				//on a pas le droit d'écriture
				throw new DAV\Exception\Forbidden('Permission denied to create file (filename ' . $filename . ')');
			}

			while ($buf = fread($data, 1024)){
				fwrite($fp, $buf);
			}
			fclose($fp);
			if(!file_exists($filename)){
				//Erreur de copie du fichier
				unlink($filename);
				throw new DAV\Exception\NotFound('Empty file (filename ' . $filename . ')');
			}
			if(!filesize($filename)){
				//Premier PUT d'un client Windows...
				unlink($filename);
				return;
			}

			$notice_id = $this->parentNode->get_notice_id();
			$explnum = new \explnum(0, $notice_id, 0);
			$id_rep = $this->config['upload_rep'];
			$explnum->get_file_from_temp($filename,$name,$this->config['up_place']);
			$explnum->params['explnum_statut'] = $this->config['default_docnum_statut'];
			$explnum->update();

			$concept = $this->get_moment_concept();
			$index_concept = new \index_concept($explnum->explnum_id, TYPE_EXPLNUM);
			$index_concept->add_concept($concept);
			$index_concept->save(false);

			$this->update_notice($notice_id);
			if(file_exists($filename)){
				unlink($filename);
			}
		}else{
		//on a pas le droit d'écriture
			throw new DAV\Exception\Forbidden('Permission denied to create file (filename ' . $name . ')');
    		return false;
    	}
    }

    protected function get_moment_concept(){
    	$execution_id = $this->get_parent_by_type('work')->get_titre_uniforme()->id;
    	$vedette_ids = \vedette_composee::get_vedettes_built_with_elements(array(
    			array(
    					'type' => TYPE_TITRE_UNIFORME,
    					'id' => $execution_id
    			),
    			array(
    					'type' => TYPE_CONCEPT,
    					'id' => $this->concept->get_id()
    			)
    	), 'music_explnum');
    	if (count($vedette_ids)) {
    		$concept_id = \vedette_composee::get_object_id_from_vedette_id($vedette_ids[0], TYPE_CONCEPT_PREFLABEL);
    		if (!$concept_id) {
    			$concept_id = $this->create_concept_from_vedette(new \vedette_composee($vedette_ids[0]));
    		}
    	} else {
    		$vedette = new \vedette_composee(0, 'music_explnum');
    		$vedette_tu_field = $vedette->get_at_available_field_type('titre_uniforme');
    		$vedette->add_element(new \vedette_titres_uniformes($vedette_tu_field['num'], $execution_id), 'subdivision_execution', 0);
    		$vedette_concept_field = $vedette->get_at_available_field_type('concept');
    		$vedette->add_element(new \vedette_concepts($vedette_concept_field['num'], $this->concept->get_id()), 'subdivision_moment', 0);
    		$vedette->update_label();
    		$vedette->save();
    		$concept_id = $this->create_concept_from_vedette($vedette);
    	}
    	return new \concept($concept_id);
    }

	protected function create_concept_from_vedette($vedette){
		global $base_path;
		global $opac_url_base;
		global $dbh;
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
				'store_strip_mb_comp_str' => 0,
				'endpoint_features' => array(
						'select',
				)
		);

		$tab_namespaces=array(
				"skos"	=> "http://www.w3.org/2004/02/skos/core#",
				"dc"	=> "http://purl.org/dc/elements/1.1",
				"dct"	=> "http://purl.org/dc/terms/",
				"owl"	=> "http://www.w3.org/2002/07/owl#",
				"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
				"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
				"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
				"pmb"	=> "http://www.pmbservices.fr/ontology#"
		);
		$store = \ARC2::getStore($data_store_config);

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
		$handler = new \onto_handler($base_path."/classes/rdf/skos_pmb.rdf", "arc2", $onto_store_config, "arc2", $data_store_config,$tab_namespaces,'http://www.w3.org/2004/02/skos/core#prefLabel');
		$uri = \onto_common_uri::get_new_uri("",$opac_url_base."concept#");
		$num_concept = \onto_common_uri::get_id($uri);

		$query = "insert into <pmb> {
				 		<".$uri."> rdf:type skos:Concept .
				 		<".$uri."> pmb:showInTop owl:Nothing .
		 				<".$uri."> skos:prefLabel \"".addslashes($vedette->get_label())."\" .
					}";
// 		<".$uri."> skos:inScheme <".$scheme."> .
		$handler->data_query($query);
		$query = "insert into vedette_link set
						num_object = ".$num_concept.",
						num_vedette = ".$vedette->get_id().",
						type_object = 1";
		$result = pmb_mysql_query($query,$dbh);

		$onto_index = new \onto_index();
		$onto_index->set_handler($handler);
		$onto_index->init();

		$onto_index->maj(0, $uri);

		return $num_concept;
	}

	public function hasChildren() {
		$moment_concept_id = $this->get_moment_concept()->get_id();
		$query = "select explnum_id from explnum join index_concept on explnum_id = num_object and type_object = ".TYPE_EXPLNUM." where explnum_mimetype!= 'URL' and explnum_notice = ".$this->parentNode->get_notice_id()." and num_concept = ".$moment_concept_id;
		$query = $this->filterExplnums($query);
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			return true;
		}
		return false;
	}
}