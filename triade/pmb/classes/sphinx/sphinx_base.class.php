<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sphinx_base.class.php,v 1.12 2019-01-10 13:39:32 arenou Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php"))
	die("no access");
class sphinx_base {
	protected $champBaseFilepath;
	protected $separator = ' $#|#! ';
	protected $indexes = array();
	protected $insertIndex = array();
	protected $fields_pond = array();
	protected $default_index = 'records';
	protected $multiple = 1;
	protected static $DBHandler = null;
	protected $filters = array();
	protected $datatypes = array();

	public function __construct() {
	}

	public function getDBHandler() {
		if (self::$DBHandler === null) {
			$this->setDBHandler($this->resolveDBHandler());
		}
		return self::$DBHandler;
	}

	public function setDBHandler($DBHandler) {
		if (self::$DBHandler === null) {
			self::$DBHandler = $DBHandler;
		}
		return $this;
	}
	
	protected function resolveDBHandler() {
		global $sphinx_mysql_connect, $dbh;
		if (!$sphinx_mysql_connect) {
			return $dbh;
		}
		$connect_params = explode(',', $sphinx_mysql_connect);
		if ($connect_params[1]) {
			return pmb_mysql_connect($connect_params[0], $connect_params[2], $connect_params[3]);
		} else {
			return pmb_mysql_connect($connect_params[0]);
		}
	}

	public function getChampBaseFilepath() {
		return $this->champBaseFilepath;
	}

	public function setChampBaseFilepath($champBaseFilepath) {
		if ($this->champBaseFilepath != $champBaseFilepath) {
			$this->indexes = array();
			$this->champBaseFilepath = $champBaseFilepath;
			// Recherche de subst
			$champBaseFilepath = str_replace(basename($champBaseFilepath), basename($champBaseFilepath, ".xml") . '_subst.xml', $champBaseFilepath);
			if (file_exists($champBaseFilepath)) {
				$this->champBaseFilepath = $champBaseFilepath;
			}
			$this->parse_file();
		}
		return $this;
	}

	public function getDefaultIndex() {
		return $this->default_index;
	}

	public function setDefaultIndex($defaultIndex) {
		$this->default_index = $defaultIndex;
		return $this;
	}

	/**
	 * Retourne la liste des langues pour l'indexation
	 * TODO Aller lire un paramètre proprement
	 * 
	 * @return array()
	 */
	public function getAvailableLanguages() {
		// TODO A FAIRE PROPREMENT
		return array(
				'',
				'fr_FR',
				'en_UK' 
		);
	}

	public function getSeparator() {
		return $this->separator;
	}

	public function setSeparator($separator) {
		$this->separator = $separator;
		return $this;
	}

	public function getIndexes() {
		return $this->indexes;
	}

	protected function parse_file() {
		if (! is_array($this->indexes) || ! count($this->indexes)) {
			$params = _parser_text_no_function_(file_get_contents($this->getChampBaseFilepath()), 'INDEXATION');
			$this->indexes = array();
			
			for($i = 0; $i < count($params['FIELD']); $i++) {
				$field = 'f';
				$fields = $attributes = array();
				// On s'assure juste d'avoir un index
				if (! isset($params["FIELD"][$i]['INDEX_NAME'])) {
					$params["FIELD"][$i]['INDEX_NAME'] = $this->default_index;
				}
				// On initialise le tableau
				if (! isset($this->indexes[$params["FIELD"][$i]['INDEX_NAME']])) {
					$this->indexes[$params["FIELD"][$i]['INDEX_NAME']] = array(
							'fields' => array(),
							'attributes' => array(
									'dummy' 
							) 
					);
				}
				// Pas d'infos viables, on ne perd de temps...
				if (! isset($params["FIELD"][$i]['TABLE'])) {
					continue;
				}
				// On récupère l'identifiant
				if (isset($params["FIELD"][$i]['ID'])) {
					$field .= '_' . $params["FIELD"][$i]['ID'];
				}
				// Si pas de tablefield, on regarde si c'est pas des éléments externes avec de sortir
				if (! isset($params["FIELD"][$i]['TABLE'][0]['TABLEFIELD'])) {
					
					switch ($params["FIELD"][$i]['DATATYPE']) {
						case 'custom_field' :
							// Traitement des champs perso !
							switch ($params["FIELD"][$i]['TABLE']) {
								case 'notices' :
								default :
									$pperso = new parametres_perso($params["FIELD"][$i]['TABLE'][0]['value']);
									break;
							}
							// Pour chaque champ perso
							foreach ( $pperso->t_fields as $pperso_id => $pperso_infos ) {
								// Si le champs est déclaré recherchable
								if ($pperso_infos['SEARCH']) {
									$fields[] = $field . '_' . str_pad($pperso_id, 2, "0", STR_PAD_LEFT);
									// $attributes[] = $field.'_'.$pperso_id;
									$this->insert_index[$field . '_' . str_pad($pperso_id, 2, "0", STR_PAD_LEFT)] = $params["FIELD"][$i]['INDEX_NAME'];
									$this->fields_pond[$field . '_' . str_pad($pperso_id, 2, "0", STR_PAD_LEFT)] = $pperso_infos['POND'] * $this->multiple;
									if($params["FIELD"][$i]['DATATYPE']){
									    $this->datatypes[$params["FIELD"][$i]['DATATYPE']][]=$field . '_' . str_pad($pperso_id, 2, "0", STR_PAD_LEFT);
									}
								}
							}
							break;
						case 'authperso' :
							// TODO Sortir l'ISDB de l'autorité perso comme attribut!
							$authpersos = authpersos::get_instance();
							foreach ( $authpersos->info as $authperso_id => $authperso_info ) {
								for($j = 0; $j < count($authperso_info['fields']); $j++) {
									$field = 'f_' . ($params["FIELD"][$i]['ID'] + $authperso_id);
									if ($authperso_info['fields'][$j]['search']) {
										$fields[] = $field . '_' . str_pad($authperso_info['fields'][$j]['id'], 2, "0", STR_PAD_LEFT);
										// $attributes[] = $field.'_'.str_pad($authperso_info['fields'][$j]['id'], 2,"0",STR_PAD_LEFT);
										$this->insert_index[$field . '_' . str_pad($authperso_info['fields'][$j]['id'], 2, "0", STR_PAD_LEFT)] = $params["FIELD"][$i]['INDEX_NAME'];
										$this->fields_pond[$field . '_' . str_pad($authperso_info['fields'][$j]['id'], 2, "0", STR_PAD_LEFT)] = $authperso_info['fields'][$j]['pond'] * $this->multiple;
									}
									if($params["FIELD"][$i]['DATATYPE']){
									    $this->datatypes[$params["FIELD"][$i]['DATATYPE']][]=$field . '_' . str_pad($authperso_info['fields'][$j]['id'], 2, "0", STR_PAD_LEFT);
									}
								}
							}
							break;
						default :
							break; // useless
					}
				} else {
					// Pour chaque table cité
					for($j = 0; $j < count($params["FIELD"][$i]['TABLE']); $j++) {
						// Pour chaque colonne cité dans la table courante
						for($k = 0; $k < count($params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD']); $k++) {
							// Pas d'id à ce niveau = code_ss_champ = 00
							if (! isset($params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['ID'])) {
								$params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['ID'] = "00";
							}
							// Pondération nul, c'est un champ de facette pur... pas de recherche
							if (! isset($params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['POND']) || isset($params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['POND']) * 1 > 0) {
								$fields[] = $field . '_' . $params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['ID'];
							}
							// TODO Lire un paramètres qui nous dit on veut ou non du champ en attribut
							// $attributes[] = $field.'_'.$params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['ID'];
							
							$this->insert_index[$field . '_' . $params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['ID']] = $params["FIELD"][$i]['INDEX_NAME'];
							if (isset($params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['POND'])) {
								$this->fields_pond[$field . '_' . $params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['ID']] = $params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['POND'] * $this->multiple;
							} else if (isset($params["FIELD"][$i]['POND'])) {
								$this->fields_pond[$field . '_' . $params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['ID']] = $params["FIELD"][$i]['POND'] * $this->multiple;
							}
							if(isset($params["FIELD"][$i]['DATATYPE'])){
							    $this->datatypes[$params["FIELD"][$i]['DATATYPE']][]= $field . '_' . $params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['ID'];
							}
						}
					}
					if (!empty($params["FIELD"][$i]['ISBD'])) {
						$attributes[] = $field . '_' . $params["FIELD"][$i]['ISBD'][0]['ID'];
						$this->insert_index[$field . '_' . $params["FIELD"][$i]['ISBD'][0]['ID']] = $params["FIELD"][$i]['INDEX_NAME'];
					}
				}
				$this->indexes[$params["FIELD"][$i]['INDEX_NAME']]['fields'] = array_unique(array_merge($this->indexes[$params["FIELD"][$i]['INDEX_NAME']]['fields'], $fields));
				$this->indexes[$params["FIELD"][$i]['INDEX_NAME']]['attributes'] = array_unique(array_merge($this->indexes[$params["FIELD"][$i]['INDEX_NAME']]['attributes'], $attributes));
			}
			// TODO FULLTEXT EXPLNUMS
			for($i=0 ; $i<count($this->filters) ; $i++){
                $this->indexes[$this->default_index]['attributes'][] = $this->filters[$i];
            }
		}
	}

	public function get_fields_pond() {
		$this->parse_file();
		return $this->fields_pond;
	}
	
	public function get_datatypes(){
	    return $this->datatypes;
	}
	public function get_datatype_indexes_from_mode($mode){
	    switch($mode){
	        case 'titres_uniformes':
	            return $this->datatypes['uniformtitle'];
	    }
	    return array();
	}
}