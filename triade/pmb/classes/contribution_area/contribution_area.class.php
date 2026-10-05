<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area.class.php,v 1.47.4.2 2025/04/29 09:51:00 tsamson Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php"))
	die("no access");

require_once($class_path.'/contribution_area/contribution_area_forms_controller.class.php');
require_once ($include_path . '/templates/contribution_area/contribution_area.tpl.php');
require_once($class_path.'/onto/onto_parametres_perso.class.php');
require_once($class_path.'/onto/common/onto_common_uri.class.php');

/**
 * class contribution_area
 * Représente un espace de contribution
 */
class contribution_area {
	
	/**
	 * Nom de l'espace de contribution
	 * 
	 * @access protected
	 */
	protected $title;
	
	/**
	 * Id de l'espace de contribution
	 * 
	 * @access protected
	 */
	protected $id;
	
	/**
	 * Commentaire
	 * @var string
	 */
	protected $comment;
	
	/**
	 * Couleur
	 * @var string $color
	 */
	protected $color;

	/**
	 * Statut
	 * @var int $status
	 */
	protected $status;
	
	/**
	 * Ordre
	 * @var int $order
	 */
	protected $order;
	
	/**
	 * Répertoire de template d'autorités
	 * @var string $repo_template
	 */
	protected $repo_template_authorities;
	
	/**
	 * Répertoire de template de notices
	 * @var string $repo_template
	 */
	protected $repo_template_records;
	
	/**
	 * Espace utilisé pour la modification d'entité
	 * @var int $repo_template
	 */
	protected $editing_entity;
	
	/**
	 * parametre de visibilité de l'espace à l'opac
	 * @var string $opac_visibility
	 */
	protected $opac_visibility;

	/**
	 * parametre pour mettre une image de fond en opac
	 * @var string $area_logo
	 */
	protected $area_logo;

	/**
	 * parametre si on doit supprimer l'image de fond en opac
	 * @var string $area_del_logo
	 */
	protected $area_del_logo;
	
	private static $onto;

	/**
	 * store de parametrage des espaces de contribution
	 *
	 * @var onto_store_arc2
	 */
	private static $graphstore;

	/**
	 * store des donnees de contribution
	 *
	 * @var onto_store_arc2
	 */
	private static $datastore;

	public function __construct($id = 0) {
		if ($id) {
			$this->id = $id * 1;
			$this->fetch_datas();
		}
	} // end of member function __construct
	
	public function fetch_datas() {
		if ($this->id) {
			$query = "select * from contribution_area_areas where id_area = ".$this->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$result = pmb_mysql_fetch_object($result);
				$this->id = $result->id_area;
				$this->title = $result->area_title;
				$this->comment = $result->area_comment;
				$this->color = $result->area_color;
				$this->order = $result->area_order;
				$this->status = $result->area_status;
				$this->opac_visibility = $result->area_opac_visibility;
				$this->repo_template_authorities = $result->area_repo_template_authorities;
				$this->repo_template_records = $result->area_repo_template_records;
				$this->area_logo = $result->area_logo;
			}
		}
	}

	public function get_title() {
		return $this->title;
	}

	public function set_title($title) {
		$this->title = $title;
	}

	/**
	 * Parcours les enregistrement en base et renvoi la liste (ou un message indiquant que nous n'en avons pas)
	 */
	public static function get_list() {
		global $msg;
		global $contribution_area_list_tpl;
		global $contribution_area_list_line_tpl;
		global $contribution_area_add_button;
		global $pmb_contribution_opac_edit_entity;
		global $contribution_area_edit_entity;
		
		$query = 'SELECT contribution_area_areas.*, contribution_area_status_gestion_libelle AS status_label 
				FROM contribution_area_areas 
				LEFT JOIN contribution_area_status ON area_status = contribution_area_status_id   
				ORDER BY area_order';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$list = '';
			$pair = 'even';
			while ( $area = pmb_mysql_fetch_object($result) ) {
			    if ($area->area_order == 0) {
			        SELF::update_order($area->id_area);
			    }
				if ($pair == 'odd') {
					$pair = 'even';
				} else {
					$pair = 'odd';
				}
				$list .= str_replace('!!odd_even!!', $pair, $contribution_area_list_line_tpl);
				$list = str_replace('!!area_title!!', $area->area_title, $list);
				$list = str_replace('!!area_color!!', $area->area_color, $list);
				$list = str_replace('!!area_status!!', ($area->status_label ? $area->status_label : ""), $list);
				$list = str_replace('!!disabled_default_area!!', ($pmb_contribution_opac_edit_entity ? $contribution_area_edit_entity : ""), $list);
				
				if ($pmb_contribution_opac_edit_entity) {
    				//Bouton utilisé par défaut
    				$message = str_replace('%f', $area->area_title, $msg['contribution_area_confirm_default_area']);
    				$list = str_replace('!!confirm_msg_default!!', addslashes($message), $list);
    				if ($area->area_editing_entity) {
    				    $list = str_replace('!!button_default_area!!', $msg['contribution_area_is_default_area'], $list);
    				    $list = str_replace('!!disabled_default_area!!', 'disabled', $list);
    				} else {
    				    $list = str_replace('!!button_default_area!!', $msg['contribution_area_default_area'], $list);
    				    $list = str_replace('!!disabled_default_area!!', '', $list);
    				}
				}
				
				$list = str_replace('!!id!!', $area->id_area, $list);
			}
			$table = str_replace('!!list!!', $list, $contribution_area_list_tpl);
			return $table . $contribution_area_add_button;
			// return $table;
		}
		return '<h2>' . $msg ['no_contribution_area_defined'] . '</h2>' . $contribution_area_add_button;
	}

	public function get_form() {
		global $contribution_area_form, $contribution_area_delete_button, $msg, $charset;
		if ($this->id) {
			$contribution_area_form = str_replace('!!delete!!', $contribution_area_delete_button, $contribution_area_form);
			$contribution_area_form = str_replace('!!msg_title!!', $msg['contribution_area_form_edit'], $contribution_area_form);
			$contribution_area_form = str_replace('!!id!!', $this->id, $contribution_area_form);
			$contribution_area_form = str_replace('!!area_title!!', htmlentities($this->title, ENT_QUOTES, $charset), $contribution_area_form);
			$contribution_area_form = str_replace('!!area_comment!!', htmlentities($this->comment, ENT_QUOTES, $charset), $contribution_area_form);
			$contribution_area_form = str_replace('!!area_color!!', htmlentities($this->color, ENT_QUOTES, $charset), $contribution_area_form);
			$contribution_area_form = str_replace('!!area_status!!', $this->get_status_options(), $contribution_area_form);
			$contribution_area_form = str_replace('!!area_rights!!', $this->get_rights_form(), $contribution_area_form);
			$contribution_area_form = str_replace('!!area_repo_template_authorities!!', htmlentities($this->repo_template_authorities, ENT_QUOTES, $charset), $contribution_area_form);
			$contribution_area_form = str_replace('!!area_repo_template_records!!', htmlentities($this->repo_template_records, ENT_QUOTES, $charset), $contribution_area_form);
			$contribution_area_form = str_replace('!!area_logo!!', $this->get_content_area_logo(), $contribution_area_form);
			$contribution_area_form = str_replace('!!area_logo_src_display!!', !empty($this->area_logo) ? "block" : "none", $contribution_area_form);
			$contribution_area_form = str_replace('!!area_opac_visibility!!', ($this->opac_visibility ? 'checked' : '' ), $contribution_area_form);
		} else {
			$contribution_area_form = str_replace('!!delete!!', '', $contribution_area_form);
			$contribution_area_form = str_replace('!!msg_title!!', $msg['contribution_area_form_create'], $contribution_area_form);
			$contribution_area_form = str_replace('!!id!!', 0, $contribution_area_form);
			$contribution_area_form = str_replace('!!area_title!!', '', $contribution_area_form);
			$contribution_area_form = str_replace('!!area_comment!!', '', $contribution_area_form);
			$contribution_area_form = str_replace('!!area_color!!', '', $contribution_area_form);
			$contribution_area_form = str_replace('!!area_status!!', $this->get_status_options(), $contribution_area_form);
			$contribution_area_form = str_replace('!!area_rights!!', $this->get_rights_form(), $contribution_area_form);
			$contribution_area_form = str_replace('!!area_repo_template_authorities!!', '', $contribution_area_form);
			$contribution_area_form = str_replace('!!area_repo_template_records!!', '', $contribution_area_form);
			$contribution_area_form = str_replace('!!area_logo!!', $this->get_content_area_logo(), $contribution_area_form);
			$contribution_area_form = str_replace('!!area_logo_src_display!!', !empty($this->area_logo) ? "block" : "none", $contribution_area_form);
			$contribution_area_form = str_replace('!!area_opac_visibility!!', 'checked', $contribution_area_form);
		}
		return $contribution_area_form;
	}

	public function get_definition_form(){
		global $contribution_area_form_definition;
		$form = str_replace("!!area_title!!", $this->title, $contribution_area_form_definition);	
		$form = str_replace ("!!available_entities_data!!",encoding_normalize::json_encode(contribution_area_forms_controller::get_store_data()),$form);
		$form = str_replace ("!!graph_data_store!!",encoding_normalize::json_encode($this->get_graph_store_data()),$form);
		$form = str_replace ("!!graph_shapes!!",encoding_normalize::json_encode($this->parse_graph_shapes()),$form);
		$form = str_replace ("!!id!!",$this->id,$form);
		print $form;
	}
	
	/**
	 * Non static (avoir pour des tests supp
	 *  sur les éléments de scénarii)
	 */
	public function delete() {		
 		//suppression des droits d'acces empr_contribution_area
	    $query_acces = "show tables like 'acces_res_4'";
	    $result_acces = pmb_mysql_query($query_acces);
	    if($result_acces && pmb_mysql_num_rows($result_acces)) {
    		$requete = "delete from acces_res_4 where res_num=".$this->id;
    		@pmb_mysql_query($requete);
	    }
		
	    $this->delete_in_stores();
	    $this->delete_uplodad_directory();
	    
		$query = "DELETE FROM contribution_area_areas WHERE id_area = '".$this->id."'";
		return pmb_mysql_query($query);
	}
	
	private function delete_in_stores() {
	    //suppression des donnees du graphstore
	    self::get_graphstore();
	    $succes = self::$graphstore->query('select * where { 
            ?attachment <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.pmbservices.fr/ca/Attachment> .
            ?attachment <http://www.pmbservices.fr/ca/inArea> '.$this->get_area_uri().' .
            ?attachment <http://www.pmbservices.fr/ca/attachmentSource> '.$this->get_area_uri().' .
            ?attachment <http://www.pmbservices.fr/ca/attachmentDest> ?scenario .
        }');
	    if ($succes) {
	        $results = self::$graphstore->get_result();
	        foreach ($results as $row) {
	            $scenario_id = onto_common_uri::get_id($row->scenario);
	            contribution_area_scenario::delete($scenario_id);
	        }
			$delete_query = "DELETE {
                ?s ?p ".$this->get_area_uri()." .
            }";
	        $succes = self::$graphstore->query($delete_query);
	        if (!$succes) {
	            var_dump($delete_query, self::$graphstore->get_errors());
	        }
	    } else {
	        var_dump(self::$graphstore->get_errors());
	    }

		//suppression des donnees du datastore
		self::get_datastore();
		$delete_query = "DELETE {?s ?p ?o} WHERE {
			?s ?p ?o .
			?s pmb:area '".$this->id."' .
		}";
		$success = self::$datastore->query($delete_query);
		if (!$success) {
			var_dump($delete_query, self::$datastore->get_errors());
		}
	}

	public function save_from_form() {
	    global $area_title, $area_comment, $area_color, $area_status, $area_repo_template_authorities, $area_repo_template_records, $area_opac_visibility, $area_del_logo;

	    $this->title = stripslashes($area_title);
		$this->comment = stripslashes($area_comment);
		$this->color = stripslashes($area_color);
		$this->status = stripslashes($area_status);
		$this->repo_template_authorities = stripslashes($area_repo_template_authorities);
		$this->repo_template_records = stripslashes($area_repo_template_records);
		$this->opac_visibility = stripslashes($area_opac_visibility);
		$this->area_del_logo = stripslashes($area_del_logo);
		$this->area_logo = stripslashes($this->get_path_area_logo());
	}
	
	public function save() {
		$query_clause = '';
		if ($this->id) {
			$update = true;
			$query_statement = 'update ';
			$query_clause = ' where id_area = '.$this->id;
		} else {
			$update = false;
			$query_statement = 'insert into ';
			$query = "SELECT MAX(area_order) FROM contribution_area_areas";
			$result = pmb_mysql_query($query);
			$max_order = pmb_mysql_result($result,0,0);
			$this->order = $max_order + 1; 
		}
		$query_statement .= ' contribution_area_areas set ';
		$query_statement .= 'area_title = "'.addslashes($this->title).'", ';
		$query_statement .= 'area_comment = "'.addslashes($this->comment).'", ';
		$query_statement .= 'area_color = "'.addslashes($this->color).'", ';
		$query_statement .= 'area_status = "'.addslashes($this->status).'", ';
		$query_statement .= 'area_opac_visibility = "'.addslashes($this->opac_visibility).'", ';
		$query_statement .= 'area_repo_template_authorities = "'.addslashes($this->repo_template_authorities).'", ';
		$query_statement .= 'area_repo_template_records = "'.addslashes($this->repo_template_records).'", ';
		$query_statement .= 'area_logo = "'.addslashes($this->area_logo).'", ';
		$query_statement .= 'area_order = '.$this->order;
		pmb_mysql_query($query_statement.$query_clause);
		if(!$this->id){
			$this->id = pmb_mysql_insert_id();
		}
		
		$this->save_rights($update);
	}
	
	protected function save_rights($update) {
		global $gestion_acces_active, $gestion_acces_empr_contribution_area;
		global $res_prf, $chk_rights, $prf_rad, $r_rad;
		
		// traitement des droits acces user_contribution_area
		if ($gestion_acces_active == 1 && $gestion_acces_empr_contribution_area == 1) {
			$ac = new acces();
			$dom_4 = $ac->setDomain(4);
			if ($update) {
				$dom_4->storeUserRights(1, $this->id, $res_prf, $chk_rights, $prf_rad, $r_rad);
			} else {
				$dom_4->storeUserRights(0, $this->id, $res_prf, $chk_rights, $prf_rad, $r_rad);
			}
		}
	}
	
	/**
	 * @return onto_ontology
	 */
	public static function get_ontology() {
		if(!isset(self::$onto)) {
            $contribution_area_store = new contribution_area_store();
            self::$onto = $contribution_area_store->get_ontology();
		}
		return self::$onto;
	}
	
	public function get_graph_store_data() {
	    return $this->get_attachment_detail($this->get_area_uri(false));
	}

	private function get_attachment_detail($source_uri, $source_id="") {
		$details = array();
		$attachments = $this->get_attachment("<{$source_uri}>");
		for($i=0 ; $i<count($attachments) ; $i++){
			$infos = $this->get_infos($attachments[$i]->dest);
			if (empty($infos)) {
			    continue;
			}
			
			if(!empty($attachments[$i]->name)){
			    $property = $this->get_property_attachment($source_uri, $attachments[$i]);
				$node = array(
					'type' => 'attachment',
					'name' => $attachments[$i]->name,
					'id' => $attachments[$i]->identifier,
				    'entityType' => $property['flag'] ?? $infos['entityType'],
				    'question' => !empty($attachments[$i]->question) ? $attachments[$i]->question : '',
				    'comment' => !empty($attachments[$i]->comment) ? $attachments[$i]->comment : '',
				);
				if($source_id){
					$node['parent'] = $source_id;
				}
				if($attachments[$i]->property_pmb_name){
					$node['propertyPmbName'] = $attachments[$i]->property_pmb_name;
				} 
				$infos['parent'] = $attachments[$i]->identifier;
				
				if (!in_array($node, $details)) {
    				$details[] = $node;			
				}
			}else{				
				if($source_id){
					$infos['parent'] = $source_id;
				}
			}
			$details[] = $infos;
			$details = array_merge($details, $this->get_attachment_detail($attachments[$i]->dest, $infos['id']));
			
		}
		return $details;
	}
	
	/**
	 * Retourne la première propriété qui matche avec un noeud d'attache et le formulaire
	 * 
	 * @param string $form_uri
	 * @param string $attachment
	 * @return array
	 */
	private function get_property_attachment($form_uri, $attachment) {
	    $formInfo = $this->get_infos($form_uri);
	    $matches = array_filter(contribution_area_forms_controller::get_store_data(), function ($item) use($formInfo, $attachment) {
	        if (!is_array($item) || $item['type'] != "property") {
	            return false;
	        }
	        if ($item['form_id'] != $formInfo['eltId'] && $item['parent_type'] != $formInfo['entityType']) {
	            return false;
	        }
	        return $item['name'] == $attachment->name;
	    });
        return !empty($matches) ? array_shift($matches) : [];
	}
	
	private function get_attachment($source_uri){
		$attachments = array();
		self::get_graphstore();
		$result = self::$graphstore->query('select * where {
			?attachment rdf:type ca:Attachment .
			?attachment ca:inArea '.$this->get_area_uri().' .
			?attachment ca:attachmentSource '.$source_uri.' .
			?attachment ca:attachmentDest ?dest .
			?attachment ca:rights ?rights .
			optional {
				?attachment rdf:label ?name .
				?attachment ca:identifier ?identifier .
				?attachment pmb:name ?property_pmb_name .
				optional {
					?attachment pmb:question ?question .
					?attachment pmb:comment ?comment
				}
			}
		}');
		if ($result) {
			$attachments = self::$graphstore->get_result();
		}
		return $attachments;
	}
        
	public static function get_pmb_entities() {
		$ontology = self::get_ontology();
		$classes_array = $ontology->get_classes_uri();
		$pmb_entities = array();
		foreach($classes_array as $entity){
			if (!isset($entity->flags) || !is_array($entity->flags) || !in_array("pmb_entity", $entity->flags)) {
				continue;
			}
			$pmb_entities[$entity->pmb_name] =  $entity->name;			
		}
		return $pmb_entities;
	}
        
	private function get_infos($uri){
		self::get_graphstore();
		$infos = array();
		$result = self::$graphstore->query('select * where {
			<'.$uri.'> ?p ?o .
		}');
		if($result){
			$results = self::$graphstore->get_result();
			for($i=0 ; $i<count($results) ; $i++){				
				switch($results[$i]->p){
					case 'http://www.pmbservices.fr/ca/eltId' :
						$infos['eltId'] = $results[$i]->o;
						break;
					case 'http://www.pmbservices.fr/ca/identifier' :
						$infos['id'] = $results[$i]->o;
						break;
					case 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type' :
						switch($results[$i]->o){
							case "http://www.pmbservices.fr/ca/Form" :
								$infos['type'] = 'form';
								break;
							case "http://www.pmbservices.fr/ca/Scenario" :
								$infos['type'] = 'scenario';
								break;
							default : 
								$infos['type'] = $results[$i]->o;
								break;
						}
						break;
					case 'http://www.w3.org/1999/02/22-rdf-syntax-ns#label' :
						$infos['name'] = $results[$i]->o;
						break;
					case 'http://www.pmbservices.fr/ontology#entity' :
						$infos['entityType'] = $results[$i]->o;
						break;
					case 'http://www.pmbservices.fr/ontology#startScenario' :
						$infos['startScenario'] = $results[$i]->o;
						break;
					case 'http://www.pmbservices.fr/ontology#displayed' :
						$infos['displayed'] = $results[$i]->o;
						break;
					case 'http://www.pmbservices.fr/ontology#parentScenario' :
						$infos['parentScenario'] = $results[$i]->o;
						break;
					case 'http://www.pmbservices.fr/ontology#question' :
						$infos['question'] = $results[$i]->o;
						break;
					case 'http://www.pmbservices.fr/ontology#comment' :
						$infos['comment'] = $results[$i]->o;
						break;
					case 'http://www.pmbservices.fr/ontology#status' :
						$infos['status'] = $results[$i]->o;
						break;
					case 'http://www.pmbservices.fr/ontology#response' :
						$infos['response'] = $results[$i]->o;
						break;
					case 'http://www.pmbservices.fr/ontology#orderResponse' :
						$infos['orderResponse'] = $results[$i]->o;
						break;
					case 'http://www.pmbservices.fr/ontology#equation' :
						$infos['equation'] = $results[$i]->o;
						break;
				}
			}
		}
		return $infos;
	}
	
	private function reset_graph() {
	    self::get_graphstore();
	    if ($this->id) {
	        // On commence par supprimer ce qui existe
	        $query = "
					select ?suj where {
						?suj ca:inArea <http://www.pmbservices.fr/ca/Area#".$this->id.">
					}
					";
	        $result = self::$graphstore->query($query);
	        if(!$result){
	            var_dump("Errors : ".self::$graphstore->get_errors());
	        } else {
	            $rows = self::$graphstore->get_result();
	            foreach ($rows as $row) {
	                $query = "delete {
						<".$row->suj."> ?prop ?obj
					}";
	                
	                $result_delete = self::$graphstore->query($query);
	                if(!$result_delete){
	                    var_dump("Errors : ".self::$graphstore->get_errors());
	                }
	            }
	        }
	    }
	}
		
	public function save_graph($data, $current_scenario = 0){
		self::get_graphstore();
		
		if (empty($data) && empty($current_scenario)) {
		    //dans ce cas on est surement sur un bug d'affichage (ex : APCA #157453)
		    return;
		}
		$this->reset_graph();
		//on encadre les float avec des guillemets sinon json_decode arrondit l'id
		$data = encoding_normalize::utf8_normalize(preg_replace('/:\s*(\-?\d+(\.\d+)?([e|E][\-|\+]\d+)?)/', ': "$1"', stripslashes($data)));
		$data = json_decode($data);
		$graph_data = $this->prepare_data($data);
		
		$query = 'delete {';
		for($i=0 ; $i<count($graph_data) ; $i++){
		    $query.= '
            '.$graph_data[$i]['subject'].' ?p ?o .';
		}
		$query.='
        }';
		$result = self::$graphstore->query($query);
		if(!$result){
		    var_dump(self::$graphstore->get_errors());
		}
		
		$query = 'insert into <pmb> {';
		for($i=0 ; $i<count($graph_data) ; $i++){
			$query.= '
			'.$graph_data[$i]['subject'].' '.$graph_data[$i]['predicat'].' '.encoding_normalize::charset_normalize($graph_data[$i]['value'],"utf-8").' .';			
		}		
		$query.='
		}';
		
		$result = self::$graphstore->query($query);
		if(!$result){
			var_dump(self::$graphstore->get_errors());
		}
		
		contribution_area_scenario::save_current_scenario($current_scenario);
	}
	
	private function prepare_data($data){
		
		$tree = $this->init_tree($data);
		
		$assertions = array();
		
		for($i=0 ; $i<count($tree) ; $i++){
			//attachment 
			$assertions = array_merge($assertions,$this->getAttachmentAssertions($this->getObjectUri($tree[$i],true),$this->get_area_uri() , $tree[$i]));
			// les infos de l'élément
			$assertions = array_merge($assertions,$this->get_node_assertions($tree[$i]));
			//la suite...
			if(!empty($tree[$i]->children)){
				$assertions = array_merge($assertions, $this->getChildrenAssertions($this->getObjectUri($tree[$i]),$tree[$i]->children));
			}	
		}
		return $assertions;
	}
	
	private function getChildrenAssertions($source,$children){
		$assertions = array();
		for($i=0 ; $i<count($children) ; $i++){
			// les infos de l'élément
			$assertions = array_merge($assertions,$this->get_node_assertions($children[$i]));
			//attachment
			if($children[$i]->type == 'attachment'){
				for($j=0 ; $j<count($children[$i]->children) ; $j++){
					$assertions = array_merge($assertions, $this->getAttachmentAssertions($this->getObjectUri($children[$i],true),$source , $children[$i]->children[$j]));	
				}
				$assertions = array_merge($assertions, $this->getChildrenAssertions($this->getObjectUri($children[$i]),$children[$i]->children));
			}else{
				if(!isset($children[$i]->parentType) || ($children[$i]->parentType != 'attachment')){
					$assertions = array_merge($assertions,$this->getAttachmentAssertions($this->getObjectUri($children[$i],true),$source , $children[$i]));
				}
				if(count($children[$i]->children)){
					$assertions = array_merge($assertions, $this->getChildrenAssertions($this->getObjectUri($children[$i]),$children[$i]->children));
				}
			}
		}
		return $assertions;
	}
	
	private function getAttachmentAssertions($attachment_uri,$source,$dest){
		$assertions = array();
		$assertions[]  =array(
			'subject' => $attachment_uri,
			'predicat' => 'rdf:type',
			'value' => 'ca:Attachment'
		);
		$assertions[]  =array(
			'subject' => $attachment_uri,
			'predicat' => 'ca:inArea',
			'value' => $this->get_area_uri()
		);
		$assertions[]  =array(
			'subject' => $attachment_uri,
			'predicat' => 'ca:attachmentSource',
			'value' => $source
		);
		$assertions[]  =array(
			'subject' => $attachment_uri,
			'predicat' => 'ca:attachmentDest',
			'value' => $this->getObjectUri($dest)
		);
		$assertions[]  =array(
			'subject' => $attachment_uri,
			'predicat' => 'ca:rights',
			'value' => '"TBD"'
		);
		
		return $assertions;
	}
	
	private function getObjectUri($object,$attachment=false){
		$uri = $this->get_uri($object,$attachment);
		return '<'.$uri.'>';
	}
	
	private function get_uri($object,$attachment=false){
		if($attachment){
			$uri = "http://www.pmbservices.fr/ca/Attachement#!!id!!";
			if ($object->type == 'attachment') {
			    // Petit hack horrible à mettre à la poubelle, mais bon ça marche
			    $id = is_array($object->entityType) ? "Array{$object->id}" : $object->entityType.$object->id;
			} else {
    			$id = $object->type.$object->id;
			}
			return str_replace('!!id!!',$id,$uri);
		}
		switch($object->type) {
			case 'form' :
				$uri = "http://www.pmbservices.fr/ca/Form#!!id!!";
				break;
			case 'attachment' :
				$uri = "http://www.pmbservices.fr/ca/Attachement#!!id!!";
				break;
			case 'startScenario' :
			case 'scenario' :
				$uri = "http://www.pmbservices.fr/ca/Scenario#!!id!!";
				break;
		}
		return str_replace('!!id!!',$object->id,$uri);
	}	
	
	private function init_tree($data){
		$tree = array();		
		//reformatage..
		for($i=0 ; $i<count($data) ; $i++){
		    if(isset($data[$i]->type) && $data[$i]->type == "scenario" && !isset($data[$i]->parentScenario)){
				$node = $data[$i];
				if (!empty($data[$i]->startScenario) && $this->has_children($data[$i]->id, $data)) {
					$node->children = $this->get_children($data[$i]->id, $data);				
				}
				$tree[]=$node;
			}
		}
		return $tree;
	}
	
	private function get_children($parent, $data) {
		$children = array();
		for($i=0 ; $i<count($data) ; $i++){
			if(isset($data[$i]->parent) && $parent == $data[$i]->parent){
				$child = $data[$i];
				$child->children = array();
				if ($this->has_children($child->id, $data)) {
    				$child->children = $this->get_children($child->id, $data);
				}
				$children[] = $data[$i];
			}
		}
		return $children;
	}	
	
	private function has_children($parent, $data) {
	    for($i=0 ; $i < count($data); $i++) {
	        if(isset($data[$i]->parent) && $parent == $data[$i]->parent){
			    return true;
			}
		}
	    return false;
	}	
	
	private function get_node_assertions($data){
		$scenario_uri = "<http://www.pmbservices.fr/ca/Scenario#!!id!!>";
		$attachment_uri = "<http://www.pmbservices.fr/ca/Attachement#!!id!!>";
		$form_uri = "<http://www.pmbservices.fr/ca/Form#!!id!!>";
		$assertions = array();
		// ON GERE LE GENERAL
		switch($data->type){
			case 'scenario':
				//l'URI du noeud en cours
				$node_uri = str_replace('!!id!!',$data->id,$scenario_uri);
				//le type de noeud
				$node_type = 'ca:Scenario';
				if(isset($data->equation)){
				    $assertions[] = array(
				        'subject' => $node_uri,
				        'predicat' => 'pmb:equation',
				        'value' => '"'.addslashes($data->equation).'"'
				    );
				}
				break;
			case 'form':
				//l'URI du noeud en cours
				$node_uri = str_replace('!!id!!',$data->id,$form_uri);
				//le type de noeud
				$node_type = 'ca:Form';
				//Propriétés communes é tous
				$assertions[]  =array(
					'subject' => $node_uri,
					'predicat' => 'ca:eltId',
					'value' => '"'.addslashes($data->eltId).'"'
				);
				break;	
			case 'attachment':
				//l'URI du noeud en cours
			    $id = is_array($data->entityType) ? "Array{$data->id}" : $data->entityType.$data->id;
			    $node_uri = str_replace('!!id!!', $id, $attachment_uri);
				//le type de noeud
				$node_type = 'ca:Attachment';
// 				//Propriétés communes é tous
// 				$assertions[]  =array(
// 					'subject' => $node_uri,
// 					'predicat' => 'ca:eltId',
// 					'value' => '"'.addslashes($data->eltId).'"'
// 				);
		}
		//Propriétés communes à tous
		$assertions[]  =array(
				'subject' => $node_uri,
				'predicat' => 'ca:identifier',
				'value' => '"'.addslashes($data->id).'"'
		);
		$assertions[]  =array(
				'subject' => $node_uri,
				'predicat' => 'rdf:type',
				'value' => $node_type
		);
		if(isset($data->name)){
			$assertions[]  =array(
					'subject' => $node_uri,
					'predicat' => 'rdf:label',
					'value' => '"'.addslashes($data->name).'"'
			);
		}
		if(isset($data->propertyPmbName)){
			$assertions[]  =array(
					'subject' => $node_uri,
					'predicat' => 'pmb:name',
					'value' => '"'.addslashes($data->propertyPmbName).'"'
			);
		}
		$assertions[]  =array(
		    'subject' => $node_uri,
		    'predicat' => 'pmb:entity',
		    'value' => is_array($data->entityType) ? '"'.addslashes(encoding_normalize::json_encode($data->entityType)).'"' : '"'.addslashes($data->entityType).'"'
		);
		
		if(isset($data->startScenario)){
			$assertions[]  =array(
					'subject' => $node_uri,
					'predicat' => 'pmb:startScenario',
					'value' => '"'.addslashes($data->startScenario).'"'
			);
		}
					
		if(isset($data->displayed)){
			$assertions[]  =array(
					'subject' => $node_uri,
					'predicat' => 'pmb:displayed',
					'value' => '"'.addslashes($data->displayed).'"'
			);
		}
		
		if(isset($data->parentScenario)){
			$assertions[]  =array(
					'subject' => $node_uri,
					'predicat' => 'pmb:parentScenario',
					'value' => '"'.addslashes($data->parentScenario).'"'
			);
		}

		if(isset($data->question)){
			$assertions[]  =array(
					'subject' => $node_uri,
					'predicat' => 'pmb:question',
					'value' => '"'.addslashes($data->question).'"'
			);
		}
		
		if(isset($data->response)){
			$assertions[]  =array(
					'subject' => $node_uri,
					'predicat' => 'pmb:response',
					'value' => '"'.addslashes($data->response).'"'
			);
		}

		if(isset($data->orderResponse)){
			$assertions[]  =array(
					'subject' => $node_uri,
					'predicat' => 'pmb:orderResponse',
			    'value' => '"'.addslashes($data->orderResponse).'"'
			);
		}

		if(isset($data->comment)){
			$assertions[]  =array(
					'subject' => $node_uri,
					'predicat' => 'pmb:comment',
					'value' => '"'.addslashes($data->comment).'"'
			);
		}
		
		if(isset($data->status)){
			$assertions[]  =array(
					'subject' => $node_uri,
					'predicat' => 'pmb:status',
					'value' => '"'.addslashes($data->status).'"'
			);
		}
			
		return $assertions;
	}
	
	public function get_area_uri($formated = true) {
	    $uri = "http://www.pmbservices.fr/ca/Area#{$this->id}";
        return $formated ? "<{$uri}>" : $uri;
	}
	
	/**
	 * @return onto_store_arc2
	 */
	public static function get_graphstore(){
		if(!isset(self::$graphstore)) { 
		    $contribution_area_store = new contribution_area_store();
		    self::$graphstore = $contribution_area_store->get_graphstore();
		}
		return self::$graphstore;		
	}
	
	/**
	 * @return onto_store_arc2
	 */
	public static function get_datastore(){
		if(!isset(self::$datastore)) {
		    $contribution_area_store = new contribution_area_store();
		    self::$datastore = $contribution_area_store->get_datastore();
		}
		return self::$datastore;
	}
	
	public static function search_datatype_ui_class_name($property, $onto_pmb_name, $onto_name='common'){
    	$pmb_datatype = substr($property->pmb_datatype,strpos($property->pmb_datatype,"#")+1);
    	$suffix = "_ui";
        $pmb_datatype_suffix = $suffix;
        if ($restriction && $restriction->get_max() != -1) {
           $pmb_datatype_suffix = "_card_ui";
        }
        $class_name = "onto_".$onto_name."_".$onto_pmb_name."_datatype_".$property->pmb_name.$suffix;
        if(!class_exists($class_name)){
                    $class_name = "onto_".$onto_name."_datatype_".$property->pmb_name.$suffix;
                    if(!class_exists($class_name)){
                            $class_name = "onto_".$onto_name."_".$onto_pmb_name."_datatype_".$pmb_datatype.$pmb_datatype_suffix;
                            if(!class_exists($class_name)){
                                    $class_name = "onto_".$onto_name."_datatype_".$pmb_datatype.$pmb_datatype_suffix;
                                    if(!class_exists($class_name)){
                                            if($onto_name == "common"){
                                                    $class_name = "onto_common_datatype_small_text_ui";
                                                    if(class_exists("onto_".$onto_name."_datatype_".$pmb_datatype."_ui")){
                                                            $class_name = "onto_".$onto_name."_datatype_".$pmb_datatype."_ui";
                                                    }
                                            }else{
                                                    $class_name = self::search_datatype_ui_class_name($property, $onto_pmb_name);
                                            }
                                    }
                            }
                    }
            }
            return $class_name;
	}
	
	protected function get_all_scenarios($area_linked_entities) {
		$scenarios = array();
		$result = self::$graphstore->query('SELECT * WHERE {
			?scenario rdf:type ca:Scenario .
		}');
	
		if($result){
			$scenarios = self::$graphstore->get_result();
		}
		
		$infos = array();
		for ($i=0; $i < count($scenarios); $i++) {
			$scenario = $this->get_infos($scenarios[$i]->scenario);
			if (isset($scenario['displayed']) && $scenario['displayed']) {
				$scenario['displayed'] = false;
			}
			$infos[] = $scenario;			
		}
		foreach ($infos as $info) {
			if (!isset($area_linked_entities[$info['id']])) {
				$area_linked_entities[$info['id']] = $info; 
			}
		}
		return array_values($area_linked_entities);
	}
	
	public function get_status_options() {
		global $charset;
		$query = "SELECT contribution_area_status_id, contribution_area_status_gestion_libelle FROM contribution_area_status";
		$result = pmb_mysql_query($query);
		$options = "";
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				$options .= "<option value='".$row->contribution_area_status_id."' ".($this->status==$row->contribution_area_status_id ? "selected='selected'" : "").">".htmlentities($row->contribution_area_status_gestion_libelle,ENT_QUOTES, $charset)."</option>";
			}
		}
		return $options;
	}
	
	public function get_rights_form() {
		global $msg, $charset;
		global $gestion_acces_active, $gestion_acces_empr_contribution_area;
		global $gestion_acces_empr_contribution_area_def;
			
		if ($gestion_acces_active != 1)
			return '';
		$ac = new acces();
			
		$form = '';
		$c_form = "
			<div class='row'>
				<label class='etiquette'><!-- domain_name --></label>
			</div>
			<div class='row'>
				<div class='colonne3'>" . htmlentities($msg['dom_cur_prf'], ENT_QUOTES, $charset) . "</div>
				<div class='colonne_suite'><!-- prf_rad --></div>
			</div>
			<div class='row'>
				<div class='colonne3'>" . htmlentities($msg['dom_cur_rights'], ENT_QUOTES, $charset) . "</div>
				<div class='colonne_suite'><!-- r_rad --></div>
				<div class='row'><!-- rights_tab --></div>
			</div>";
			
		if ($gestion_acces_empr_contribution_area == 1) {
	
			$r_form = $c_form;
			$dom_4 = $ac->setDomain(4);
			$r_form = str_replace('<!-- domain_name -->', htmlentities($dom_4->getComment('long_name'), ENT_QUOTES, $charset), $r_form);
			if ($this->id) {
				// profil ressource
				$def_prf = $dom_4->getComment('res_prf_def_lib');
				$res_prf = $dom_4->getResourceProfile($this->id);
				$q = $dom_4->loadUsedResourceProfiles();
					
				// Recuperation droits generiques utilisateur
				$user_rights = $dom_4->getDomainRights(0, $res_prf);
					
				if ($user_rights & 2) {
					$p_sel = gen_liste($q, 'prf_id', 'prf_name', 'res_prf[4]', '', $res_prf, '0', $def_prf, '0', $def_prf);
					$p_rad = "<input type='radio' id='prf_rad_4_R' name='prf_rad[4]' value='R' ";
					if ($gestion_acces_empr_contribution_area_def != '1')
						$p_rad .= "checked='checked' ";
					$p_rad .= "><label for='prf_rad_4_R' >" . htmlentities($msg['dom_rad_calc'], ENT_QUOTES, $charset) . "</label></input><input type='radio' id='prf_rad_4_C' name='prf_rad[4]' value='C' ";
					if ($gestion_acces_empr_contribution_area_def == '1')
						$p_rad .= "checked='checked' ";
					$p_rad .= "><label for='prf_rad_4_C' >" . htmlentities($msg['dom_rad_def'], ENT_QUOTES, $charset) . $p_sel. "</label></input>";
					$r_form = str_replace('<!-- prf_rad -->', $p_rad, $r_form);
				} else {
					$r_form = str_replace('<!-- prf_rad -->', htmlentities($dom_4->getResourceProfileName($res_prf), ENT_QUOTES, $charset), $r_form);
				}
					
				// droits/profils utilisateurs
				if ($user_rights & 1) {
					$r_rad = "<input type='radio' id='r_rad_4_R' name='r_rad[4]' value='R' ";
					if ($gestion_acces_empr_contribution_area_def != '1')
						$r_rad .= "checked='checked' ";
					$r_rad .= "><label for='r_rad_4_R' >" . htmlentities($msg['dom_rad_calc'], ENT_QUOTES, $charset) . "</label></input><input type='radio' id='r_rad_4_C' name='r_rad[4]' value='C' ";
					if ($gestion_acces_empr_contribution_area_def == '1')
						$r_rad .= "checked='checked' ";
					$r_rad .= "><label for='r_rad_4_C' >" . htmlentities($msg['dom_rad_def'], ENT_QUOTES, $charset) . "</label></input>";
					$r_form = str_replace('<!-- r_rad -->', $r_rad, $r_form);
				}
					
				// recuperation profils utilisateurs
				$t_u = array();
				$t_u[0] = $dom_4->getComment('user_prf_def_lib'); // niveau par defaut
				$qu = $dom_4->loadUsedUserProfiles();
				$ru = pmb_mysql_query($qu);
				if (pmb_mysql_num_rows($ru)) {
					while ( ($row = pmb_mysql_fetch_object($ru)) ) {
						$t_u[$row->prf_id] = $row->prf_name;
					}
				}
					
				// recuperation des controles dependants de l'utilisateur
				$t_ctl = $dom_4->getControls(0);
					
				// recuperation des droits
				$t_rights = $dom_4->getResourceRights($this->id);
					
				if (count($t_u)) {
					$h_tab = "<div class='dom_div'><table class='dom_tab'><tr>";
					foreach ( $t_u as $k => $v ) {
						$h_tab .= "<th class='dom_col'>" . htmlentities($v, ENT_QUOTES, $charset) . "</th>";
					}
					$h_tab .= "</tr><!-- rights_tab --></table></div>";
	
					$c_tab = '<tr>';
					foreach ( $t_u as $k => $v ) {
							
						$c_tab .= "<td><table style='border:1px solid;'><!-- rows --></table></td>";
						$t_rows = "";
						foreach ( $t_ctl as $k2 => $v2 ) {
	
							$t_rows .= "
								<tr>
									<td style='width:25px;' ><input type='checkbox' id='chk_rights_4_".$k."_".$k2."' name='chk_rights[4][" . $k . "][" . $k2 . "]' value='1' ";
							if (isset($t_rights[$k]) && isset($t_rights[$k][$res_prf]) && ($t_rights[$k][$res_prf] & (pow(2, $k2 - 1)))) {
								$t_rows .= "checked='checked' ";
							}
							if (($user_rights & 1) == 0) {
								$t_rows .= "disabled='disabled' /></td>
									<td>" . htmlentities($v2, ENT_QUOTES, $charset) . "</td>
								</tr>";
							} else {
							    $t_rows .= "/></td>
									<td><label for='chk_rights_4_".$k."_".$k2."' >" . htmlentities($v2, ENT_QUOTES, $charset) . "</td>
								</tr>";
							}
						}
						$c_tab = str_replace('<!-- rows -->', $t_rows, $c_tab);
					}
					$c_tab .= "</tr>";
				}
				$h_tab = str_replace('<!-- rights_tab -->', $c_tab, $h_tab);
				;
				$r_form = str_replace('<!-- rights_tab -->', $h_tab, $r_form);
			} else {
				$r_form = str_replace('<!-- prf_rad -->', htmlentities($msg['dom_prf_unknown'], ENT_QUOTES, $charset), $r_form);
				$r_form = str_replace('<!-- r_rad -->', htmlentities($msg['dom_rights_unknown'], ENT_QUOTES, $charset), $r_form);
			}
			$form .= $r_form;
		}
		return $form;
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_color() {
		return $this->color;
	}
	
	public function get_computed_form() {
		global $contribution_area_computed_form;
		$form = $contribution_area_computed_form;
		$search = array(
				"!!area_title!!",
				"!!available_entities_data!!",
				"!!environment_fields!!",
				"!!empr_fields!!",
				"!!graph_data_store!!",
				"!!computed_fields!!",
				"!!id!!"
		);
		$replace = array(
				$this->title,
				encoding_normalize::json_encode(encoding_normalize::utf8_decode(contribution_area_forms_controller::get_store_data())),
				encoding_normalize::json_encode(computed_field::get_environment_fields()),
				encoding_normalize::json_encode(computed_field::get_empr_fields()),
				encoding_normalize::json_encode($this->get_graph_store_data()),
				encoding_normalize::json_encode(computed_field::get_area_computed_fields_num($this->id)),
				$this->id
		);
		$form = str_replace($search, $replace, $form);
		return $form;
	}
	
	protected function parse_graph_shapes() {
		global $include_path;
		
		$objects = array();
		if (file_exists($include_path.'/contribution_area/scenario_graph_subst.xml')) {
			$parsed = simplexml_load_file($include_path.'/contribution_area/scenario_graph_subst.xml');
		}
		if (file_exists($include_path.'/contribution_area/scenario_graph.xml')) {
			$parsed = simplexml_load_file($include_path.'/contribution_area/scenario_graph.xml');
		}
		
		if (!$parsed) {
			return $objects;
		}
		
		foreach ($parsed->children() as $child) {
			$objects[] = $child; 
		}
		return $objects;
	}
	
	public function up_order() {
	    
	    $query_min_order = 'SELECT MIN(area_order) FROM contribution_area_areas';
	    $select_min_order = pmb_mysql_query($query_min_order);
	    $min_order = pmb_mysql_result($select_min_order,0,0);
		
		$query_contribution_current = "SELECT * FROM contribution_area_areas WHERE id_area = $this->id";
		$select_contribution_current = pmb_mysql_query($query_contribution_current);
		$contribution_current = pmb_mysql_fetch_assoc($select_contribution_current);
		$order = $this->order - 1;
		
		if ($order < $min_order) {
		    $order = $min_order;
		}
		
		$query_contribution_old = "SELECT * FROM contribution_area_areas WHERE area_order = $order";
		$select_contribution_old = pmb_mysql_query($query_contribution_old);
		$contribution_old = pmb_mysql_fetch_assoc($select_contribution_old);
		
		$query_update_contribution_current = "UPDATE contribution_area_areas SET area_order = $order WHERE id_area =".$contribution_current['id_area'] ;
		pmb_mysql_query($query_update_contribution_current);
		
		$query_update_contribution_old = "UPDATE contribution_area_areas SET area_order = $this->order WHERE id_area =".$contribution_old['id_area'] ;
		pmb_mysql_query($query_update_contribution_old);

	}
	
	public function down_order() {
	    
	    $query_max_order = 'SELECT MAX(area_order) FROM contribution_area_areas';
	    $select_max_order = pmb_mysql_query($query_max_order);
	    $max_order = pmb_mysql_result($select_max_order,0,0);
	    
	    $query_contribution_current = "SELECT * FROM contribution_area_areas WHERE id_area = $this->id";
	    $select_contribution_current = pmb_mysql_query($query_contribution_current);
	    $contribution_current = pmb_mysql_fetch_assoc($select_contribution_current);
	    $order = $this->order + 1;
	    
	    if ($order > $max_order) {
	        $order = $max_order;
	    }
	    
	    $query_contribution_old = "SELECT * FROM contribution_area_areas WHERE area_order = $order";
	    $select_contribution_old = pmb_mysql_query($query_contribution_old);
	    $contribution_old = pmb_mysql_fetch_assoc($select_contribution_old);
	    
	    $query_update_contribution_current = "UPDATE contribution_area_areas SET area_order = $order WHERE id_area =".$contribution_current['id_area'] ;
	    pmb_mysql_query($query_update_contribution_current);
	    
	    $query_update_contribution_old = "UPDATE contribution_area_areas SET area_order = $this->order WHERE id_area =".$contribution_old['id_area'] ;
	    pmb_mysql_query($query_update_contribution_old);
	}
	
	public static function update_order($id_area) {
	    $query_max_order = 'SELECT MAX(area_order) FROM contribution_area_areas';
	    $select_max_order = pmb_mysql_query($query_max_order);
	    $max_order = pmb_mysql_result($select_max_order,0,0) + 1;
	    
	    $query = "UPDATE contribution_area_areas SET area_order = $max_order WHERE id_area =".$id_area ;
	    pmb_mysql_query($query);
	}
	
	public static function get_list_ajax() {
	    
	    $area_list = array();
	    
	    $query = 'SELECT contribution_area_areas.*, contribution_area_status_gestion_libelle AS status_label
				FROM contribution_area_areas
				LEFT JOIN contribution_area_status ON area_status = contribution_area_status_id
				ORDER BY area_order';
	    $result = pmb_mysql_query($query);
	    
	    if (pmb_mysql_num_rows($result)) {
	        while ( $area = pmb_mysql_fetch_object($result) ) {
	            $area_list[$area->id_area] = $area;
	        }
	        $area_list["total"] = count($area_list);
	    }
	    return $area_list;
	}
	
	public function duplicate_scenario_to_area() {
	    global $data, $duplicate_forms, $source_area_id;
	    self::get_graphstore();
	    
	    //on encadre les float avec des guillemets sinon json_decode arrondit l'id
	    $data = json_decode(preg_replace('/:\s*(\-?\d+(\.\d+)?([e|E][\-|\+]\d+)?)/', ': "$1"', stripslashes($data)));
	    
	    // On modifie les "identifier"
	    $newNodeId = array();
	    foreach ($data as $node) {
	        unset($node->x, $node->y);
	        
	        $new_id = $this->generate_identifier();
	        $newNodeId[$node->id] = $new_id;
	        
	        if ('form' === $node->type && "true" === $duplicate_forms) {
	            $form = new contribution_area_form($node->entityType, $node->eltId);
	            $form->generate_duplication_form();
	            $node->eltId = $form->get_id();
	            $node->name = $form->get_name();
	            $node->comment = $form->get_comment();
	        }
	        
            computed_field::duplicate_all_computed_field($source_area_id, $node->id, $new_id, $this->id);
	        $node->id = $new_id;
	    }
	    
	    // Modifie les id des parent avec les nouveaux "identifier"
	    foreach ($data as $node) {
	        if (!empty($node->parent) && $newNodeId[$node->parent]) {
	            $node->parent = $newNodeId[$node->parent];
	        }
	        
	        if ('scenario' === $node->type && !empty($node->parentScenario)) {
	            if (!empty($newNodeId[$node->parentScenario])) {
	                $node->parentScenario = $newNodeId[$node->parentScenario];
	            } else {
    	            $newParentScenario = clone $node;
    	            $newParentScenario->displayed = false;
    	            unset($newParentScenario->parentScenario, $newParentScenario->parent);
    	            
    	            $newParentScenario->id = $this->generate_identifier();
    	            $node->parentScenario = $newParentScenario->id;
    	            
    	            $data[] = $newParentScenario;
	            }
	        }
	    }
	    
	    $graph_data = $this->prepare_data($data);
	    $query = 'insert into <pmb> {';
	    for ($i=0 ; $i < count($graph_data) ; $i++) {
	        $query.= '
			'.$graph_data[$i]['subject'].' '.$graph_data[$i]['predicat'].' '.$graph_data[$i]['value'].' .';
	    }
	    $query.='
		}';
	    
	    $succes = self::$graphstore->query($query);
	    if(!$succes){
	        var_dump(self::$graphstore->get_errors());
	    }
	}
	
	public function generate_identifier() {
	    self::get_graphstore();
	    
	    $temp_identifier = "0.".round(microtime(true)*10000);
	    $query = "select * where {
                    ?uri <http://www.pmbservices.fr/ca/identifier> ?identifier .
                    filter regex(?identifier, '".$temp_identifier."')
                  }";
	    $succes = self::$graphstore->query($query);
	    if($succes){
	       $results = self::$graphstore->get_result();
	       if(!empty($results)){
    	       $temp_identifier = $this->generate_identifier();
	       }else{
	           return $temp_identifier;
	       }
	    }else{
	        var_dump("Errors : ".self::$graphstore->get_errors());
	    }
	}
	
	public function set_area_default()
	{
	    if (empty($this->id)) {
	        return false;
	    }
	    $query = "UPDATE contribution_area_areas SET area_editing_entity = 0 WHERE area_editing_entity = 1";
	    pmb_mysql_query($query);
	    
	    $query = "UPDATE contribution_area_areas SET area_editing_entity = 1 WHERE id_area = '" . $this->id . "'";
	    pmb_mysql_query($query);
	    
	}
	
	public function get_acces_editing_entity() {
	    global $msg;
	    
	    return [ 
	        0 => $msg['contribution_area_is_default_area_not_use'], 
	        1 => $msg['contribution_area_is_default_area']
	    ];
	}
	
	public function get_normalized_item(){
	    $retour = array(
	        "id" => $this->id,
	        "title" => $this->title,
	        "comment" => (!empty($this->comment) ? $this->comment : ''),
	        "color" => (!empty($this->color) ? $this->color : ''),
	        "order" => (!empty($this->order) ? $this->order : ''),
	        "status" => (!empty($this->status) ? $this->status : ''),
	        "opac_visibility" => $this->opac_visibility,
	        "repo_template_authorities" => (!empty($this->repo_template_authorities) ? $this->repo_template_authorities : ''),
	        "repo_template_records" => (!empty($this->repo_template_records) ? $this->repo_template_records : ''),
	        "area_logo" => (!empty($this->area_logo) ? $this->area_logo : '')
	    );
	    
	    return $retour;
	}
	
	public function delete_uplodad_directory() {
	    global $pmb_contribution_opac_docnum_directory;
	    
	    if (empty($pmb_contribution_opac_docnum_directory)) {
	        return '';
	    }
	    
	    $upload_directory = new upload_folder($pmb_contribution_opac_docnum_directory);
	    $real_path = $upload_directory->repertoire_path . "espace_" . $this->id;
	    upload_folder::rrmdir($real_path);
	}
	
	public function get_path_area_logo() {
	    global $pmb_contribution_opac_docnum_directory;

	    if ('true' == $this->area_del_logo && empty($_FILES['area_logo']['tmp_name'])) {
	        return '';
	    }
	    
		// Récupère le chemin temporaire du fichier téléchargé
		$file_temp_path = $_FILES['area_logo']['tmp_name'];
	    if (!file_exists($file_temp_path)) {
	       return '';
	    }

		// Creation d'un objet FileInfo pour analyser le type MIME du fichier
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$fileMimeType = $finfo->file($file_temp_path);

		// Verifier si le type MIME est autorise
		if (!in_array(
			$fileMimeType, 
			["image/png", "image/jpg", "image/jpeg", "image/gif", "image/webp", "image/bmp", "image/svg+xml"])
			) {
			return '';
		}
	    
	    $upload_directory = new upload_folder($pmb_contribution_opac_docnum_directory);
	    $rep_path = $upload_directory->repertoire_path;

	    $blob =  file_get_contents($file_temp_path);
	    $filename = $_FILES['area_logo']['name'];
	    
	    // Vérifie si le répertoire existe :
	    $path = "espace_" . $this->id . "/wallpaper/";
	    upload_folder::rrmdir($rep_path . $path);
	    
	    if (!is_dir($rep_path . $path)) {
	        mkdir($rep_path . $path, 0777, true);
	    }
	    
	    $complete_path = $path . $filename;
	    file_put_contents($rep_path . $complete_path, $blob);

	    return $rep_path . $complete_path;
	}
	
	public function get_content_area_logo() {
	    if (empty($this->area_logo)) {
	        return '';
	    }
	    $content = "data:image/png;base64,".base64_encode(file_get_contents($this->area_logo));
	    return $content;
	}
} // end of contribution_area