<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_store_arc2_extended.class.php,v 1.6 2023/02/24 14:45:26 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once "$class_path/onto/onto_store_arc2.class.php";

class onto_store_arc2_extended extends onto_store_arc2 {

	/**
	 * Charge un fichier RDF dans le store
	 *
	 * @param string onto_filepath Chemin du fichier RDF à  charger dans le store
	
	 * @return bool
	 * @access public
	 */
	public function load($onto_filepath, $reset = false){
		global $dbh,$thesaurus_ontology_filemtime;
		
		$bool = true;
	
		//evolution pour la possibilité d'avoir plusieurs fichier rdf
		if (!is_numeric($thesaurus_ontology_filemtime)) {
			$tab_file_rdf =  unserialize($thesaurus_ontology_filemtime);
			if (!isset($tab_file_rdf[$this->store->getName()])) {
				$tab_file_rdf[$this->store->getName()] = 0;
			}
		} else {
			$tab_file_rdf[$this->store->getName()] = $thesaurus_ontology_filemtime;
		}
		//on charge l'ontologie seulement si la date de modification du fichier est > à la date de dernière lecture
		if($reset || (filemtime($onto_filepath) > $tab_file_rdf[$this->store->getName()])){
			// le load ne fait qu'ajouter les nouveaux triplets sans supprimer les anciens, donc on purge avant...
			$this->store->reset();
			
			//LOAD n'accepte qu'un chemin absolu
			$res=$this->query('LOAD <file:///'.realpath($onto_filepath).'>');
				
			$tab_file_rdf[$this->store->getName()] = filemtime($onto_filepath);
				
			if($res){
				$query='UPDATE parametres SET valeur_param="'.addslashes(serialize($tab_file_rdf)).'" WHERE type_param="thesaurus" AND sstype_param="ontology_filemtime"';
				pmb_mysql_query($query,$dbh);
				$bool = true;
			}else{
				$bool = false;
			}
		}else{
			$bool = false;
		}
		
		if ($bool) {
			if (!empty($this->config['params'])) {
				foreach ($this->config['params'] as $uri => $object) {
				    if (empty($uri)) {
                        continue;
				    }
					$query = "insert into <pmb> " . $this->rdf_serialize($object,$uri);
					$this->query($query,$this->namespaces);
				}
			}
			return true;
		} else {
			return false;
		}
		
	} // end of member function load
	
	public function rdf_serialize($object, $uri = "", $level = 0) {
		if ($uri) {
			$query = "{ <". $uri ."> pmb:extended [\n"; 
		} else {
			$query = "";
		}
		
		if (is_object($object)) {
			foreach ($object as $key => $value) {
				$query .= "<http://www.pmbservices.fr/ontology#".$key."> ".$this->rdf_serialize($value,'',$level+1)." ;\n";
			}
			if ($level) {
				$query = "[ ". $query ." ]";
			}
		} elseif (is_array($object)) {
			for ($i=0; $i<count($object); $i++) {
				$query .= "rdf:_".$i." ". $this->rdf_serialize($object[$i],'',$level+1)." ;\n";
			}
			if ($level) {
				$query = "[ ". $query ." ]";
			}	
			
		} elseif (is_numeric($object)) {
			$query .= $object;
		}else {
			$query .= "\"". addslashes($object) ."\"";
		}
		
		if ($uri) {
			$query .= " \n]\n}";
		}
		return $query;
	}

	
} // end of onto_store_arc2