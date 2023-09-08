<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sphinx_authperso_indexer.class.php,v 1.6 2019-02-18 13:45:53 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once $class_path.'/sphinx/sphinx_indexer.class.php';

class sphinx_authperso_indexer extends sphinx_authorities_indexer {
	
	public function __construct() {
		global $include_path;
		$this->type = AUT_TABLE_AUTHPERSO;
		$this->default_index = "authperso";
		parent::__construct();
		$this->setChampBaseFilepath($include_path."/indexation/authorities/authperso/champs_base.xml");
	}
	
	protected function addSpecificsFilters($id, $filters =array()){
		$filters = parent::addSpecificsFilters($id, $filters);

		//Récupération du statut
		$query = "select num_statut from authorities where id_authority = ".$id." and type_object = ".$this->type;
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		$filters['status'] = $row->num_statut;
		return $filters;
	}
	
	protected function parse_file()
	{
		if(!is_array($this->indexes) || !count($this->indexes)){
			$params=_parser_text_no_function_(file_get_contents($this->getChampBaseFilepath()), 'INDEXATION');
			$this->indexes = array();
			$result = pmb_mysql_query('select id_authperso from authperso');
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					$index_name = $this->default_index.'_'.$row->id_authperso;
					for($i=0 ; $i<count($params['FIELD']) ; $i++){
						$field = 'f';
						$fields = $attributes = array();
						// On s'assure juste d'avoir un index
						if(!isset($params["FIELD"][$i]['INDEX_NAME'])){
							$params["FIELD"][$i]['INDEX_NAME'] = $index_name;
						}
						// On initialise le tableau
						if(!isset($this->indexes[$params["FIELD"][$i]['INDEX_NAME']])){
							$this->indexes[$params["FIELD"][$i]['INDEX_NAME']] = array(
									'fields' => array(),
									'attributes' => array('dummy')
							);
						}
						// Pas d'infos viables, on ne perd de temps...
						if(!isset($params["FIELD"][$i]['TABLE'])){
							continue;
						}
						// On récupère l'identifiant
						if(isset($params["FIELD"][$i]['ID'])){
							$field.= '_'.str_replace('!!id_authperso!!', $row->id_authperso, $params["FIELD"][$i]['ID']);;
						}
						// Si pas de tablefield, on regarde si c'est pas des éléments externes avec de sortir
						if(!isset($params["FIELD"][$i]['TABLE'][0]['TABLEFIELD'])){
					
							switch($params["FIELD"][$i]['DATATYPE']){
								case 'custom_field' :
									//Traitement des champs perso !
									switch($params["FIELD"][$i]['TABLE']){
										case 'notices' :
										default :
											$pperso = new parametres_perso($params["FIELD"][$i]['TABLE'][0]['value']);
											break;
									}
									// Pour chaque champ perso
									foreach($pperso->t_fields as $pperso_id => $pperso_infos){
										// Si le champs est déclaré recherchable
										if($pperso_infos['SEARCH']){
											$fields[] = $field.'_'.str_pad($pperso_id, 2,"0",STR_PAD_LEFT);
// 											$attributes[] = $field.'_'.$pperso_id;
											$this->insert_index[$field.'_'.str_pad($pperso_id, 2,"0",STR_PAD_LEFT)] = $params["FIELD"][$i]['INDEX_NAME'];
											$this->fields_pond[$field.'_'.str_pad($pperso_id, 2,"0",STR_PAD_LEFT)] = $pperso_infos['POND']*$this->multiple;
										}
									}
									break;
								case 'authperso' :
									//TODO Sortir l'ISDB de l'autorité perso comme attribut!
									$authpersos = authpersos::get_instance();
									foreach($authpersos->info as $authperso_id => $authperso_info){
										for($j=0 ; $j<count($authperso_info['fields']) ; $j++){
											$field = 'f_'.($params["FIELD"][$i]['ID']+$authperso_id);
											if($authperso_info['fields'][$j]['search']){
												$fields[] = $field.'_'.str_pad($authperso_info['fields'][$j]['id'], 2,"0",STR_PAD_LEFT);
// 												$attributes[] = $field.'_'.str_pad($authperso_info['fields'][$j]['id'], 2,"0",STR_PAD_LEFT);
												$this->insert_index[$field.'_'.str_pad($authperso_info['fields'][$j]['id'], 2,"0",STR_PAD_LEFT)] = $params["FIELD"][$i]['INDEX_NAME'];
												$this->fields_pond[$field.'_'.str_pad($authperso_info['fields'][$j]['id'], 2,"0",STR_PAD_LEFT)] = $authperso_info['fields'][$j]['pond']*$this->multiple;
											}
										}
									}
									break;
								default :
									break; //useless
							}
						}else{
							// Pour chaque table cité
							for($j=0 ; $j<count($params["FIELD"][$i]['TABLE']) ; $j++){
								//Pour chaque colonne cité dans la table courante
								for($k=0 ; $k<count($params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD']) ; $k++){
									// Pas d'id à ce niveau = code_ss_champ = 00
									if(!isset($params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['ID'])){
										$params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['ID'] = "00";
									}
									// Pondération nul, c'est un champ de facette pur... pas de recherche
									if(!isset($params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['POND']) || isset($params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['POND'])*1 > 0 ){
										$fields[] = $field.'_'.$params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['ID'];
									}
									//TODO Lire un paramètres qui nous dit on veut ou non du champ en attribut
// 									$attributes[] = $field.'_'.$params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['ID'];
					
									$this->insert_index[$field.'_'.$params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['ID']] = $params["FIELD"][$i]['INDEX_NAME'];
									if (isset($params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['POND'])) {
										$this->fields_pond[$field.'_'.$params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['ID']] = $params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['POND']*$this->multiple;
									}
								}
							}
							if($params["FIELD"][$i]['ISBD']){
								$attributes[] = $field.'_'.$params["FIELD"][$i]['ISBD'][0]['ID'];
								$this->insert_index[$field.'_'.$params["FIELD"][$i]['ISBD'][0]['ID']] = $params["FIELD"][$i]['INDEX_NAME'];
							}
						}
						$this->indexes[$params["FIELD"][$i]['INDEX_NAME']]['fields']=array_unique(array_merge($this->indexes[$params["FIELD"][$i]['INDEX_NAME']]['fields'],$fields));
						$this->indexes[$params["FIELD"][$i]['INDEX_NAME']]['attributes']=array_unique(array_merge($this->indexes[$params["FIELD"][$i]['INDEX_NAME']]['attributes'],$attributes));
						// On unset le nom de l'index pour le parcours des autres autorités persos 
						unset($params["FIELD"][$i]['INDEX_NAME']);
					}
					for($z=0 ; $z<count($this->filters) ; $z++){
					    $this->indexes[$index_name]['attributes'][] = $this->filters[$z];
					}
				}
			}
		}
	}
}