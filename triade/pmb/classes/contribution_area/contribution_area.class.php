<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area.class.php,v 1.18 2019-02-18 16:05:11 tsamson Exp $
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
	
	private static $onto;
	private static $graphstore;

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
		
		$query = 'SELECT contribution_area_areas.*, contribution_area_status_gestion_libelle AS status_label 
				FROM contribution_area_areas 
				LEFT JOIN contribution_area_status ON area_status = contribution_area_status_id   
				ORDER BY area_title';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$list = '';
			$pair = 'even';
			while ( $area = pmb_mysql_fetch_object($result) ) {
				if ($pair == 'odd') {
					$pair = 'even';
				} else {
					$pair = 'odd';
				}
				$list .= str_replace('!!odd_even!!', $pair, $contribution_area_list_line_tpl);
				$list = str_replace('!!id!!', $area->id_area, $list);
				$list = str_replace('!!area_title!!', $area->area_title, $list);
				$list = str_replace('!!area_color!!', $area->area_color, $list);
				$list = str_replace('!!area_status!!', ($area->status_label ? $area->status_label : ""), $list);
			}
			$table = str_replace('!!list!!', $list, $contribution_area_list_tpl);
			return $table . $contribution_area_add_button;
			// return $table;
		}
		return '<h2>' . $msg ['no_contribution_area_defined'] . '</h2>' . $contribution_area_add_button;
	}

	public function get_form() {
		global $contribution_area_form;
		global $contribution_area_delete_button;
		global $msg;
		global $charset;
		if($this->id){
			$contribution_area_form = str_replace('!!delete!!', $contribution_area_delete_button, $contribution_area_form);
			$contribution_area_form = str_replace('!!msg_title!!', $msg['contribution_area_form_edit'], $contribution_area_form);
			$contribution_area_form = str_replace('!!id!!', $this->id, $contribution_area_form);
			$contribution_area_form = str_replace('!!area_title!!', htmlentities($this->title, ENT_QUOTES, $charset), $contribution_area_form);
			$contribution_area_form = str_replace('!!area_comment!!', htmlentities($this->comment, ENT_QUOTES, $charset), $contribution_area_form);
			$contribution_area_form = str_replace('!!area_color!!', htmlentities($this->color, ENT_QUOTES, $charset), $contribution_area_form);
			$contribution_area_form = str_replace('!!area_status!!', $this->get_status_options(), $contribution_area_form);
			$contribution_area_form = str_replace('!!area_rights!!', $this->get_rights_form(), $contribution_area_form);
			return $contribution_area_form;
		}else{
			$contribution_area_form = str_replace('!!delete!!', '', $contribution_area_form);
			$contribution_area_form = str_replace('!!msg_title!!', $msg['contribution_area_form_create'], $contribution_area_form);
			$contribution_area_form = str_replace('!!id!!', 0, $contribution_area_form);
			$contribution_area_form = str_replace('!!area_title!!', '', $contribution_area_form);
			$contribution_area_form = str_replace('!!area_comment!!', '', $contribution_area_form);
			$contribution_area_form = str_replace('!!area_color!!', '', $contribution_area_form);
			$contribution_area_form = str_replace('!!area_status!!', $this->get_status_options(), $contribution_area_form);
			$contribution_area_form = str_replace('!!area_rights!!', $this->get_rights_form(), $contribution_area_form);
			return $contribution_area_form;
		}
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
		$requete = "delete from acces_res_4 where res_num=".$this->id;
		@pmb_mysql_query($requete);
		
		$query = 'delete from contribution_area_areas where id_area = "'.$this->id.'"';
		return pmb_mysql_query($query);
	}

	public function save_from_form(){
		global $area_title;
		global $area_comment;
		global $area_color;
		global $area_status;
		
		$this->title = stripslashes($area_title);
		$this->comment = stripslashes($area_comment);
		$this->color = stripslashes($area_color);
		$this->status = stripslashes($area_status);
	}
	
	public function save() {
		$query_clause = '';
		if($this->id){
			$update = true;
			$query_statement = 'update ';
			$query_clause = ' where id_area = '.$this->id;
		}else{
			$update = false;
			$query_statement = 'insert into ';
		}
		$query_statement.= ' contribution_area_areas set ';
		$query_statement.= 'area_title = "'.addslashes($this->title).'", ';
		$query_statement.= 'area_comment = "'.addslashes($this->comment).'", ';
		$query_statement.= 'area_color = "'.addslashes($this->color).'", ';
		$query_statement.= 'area_status = "'.addslashes($this->status).'" ';
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
	
	public static function get_ontology(){
		global $base_path;
		global $class_path;
		
		if(!isset(self::$onto)){
			$onto_store_config = array(
				/* db */
				'db_name' => DATA_BASE,
				'db_user' => USER_NAME,
				'db_pwd' => USER_PASS,
				'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'ontodemo',
				/* stop after 100 errors */
				'max_errors' => 100,
				'store_strip_mb_comp_str' => 0
			);
			$tab_namespaces = array(
				"skos"	=> "http://www.w3.org/2004/02/skos/core#",
				"dc"	=> "http://purl.org/dc/elements/1.1",
				"dct"	=> "http://purl.org/dc/terms/",
				"owl"	=> "http://www.w3.org/2002/07/owl#",
				"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
				"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
				"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
				"pmb"	=> "http://www.pmbservices.fr/ontology#"
			);
			
			$onto_store = new onto_store_arc2_extended($onto_store_config);
			$onto_store->set_namespaces($tab_namespaces);
			
 			//chargement de l'ontologie dans son store
			$reset = $onto_store->load($class_path."/rdf/ontologies_pmb_entities.rdf", onto_parametres_perso::is_modified());
			onto_parametres_perso::load_in_store($onto_store, $reset);
			
			self::$onto = new onto_ontology($onto_store);
		}
		return self::$onto;
	}
	
	public function get_graph_store_data() {
		$area_linked_entities = $this->get_attachment_detail($this->get_area_uri());
		return $area_linked_entities;
	}

	private function get_attachment_detail($source_uri,$source_id=""){
		$details = array();
		$attachments = $this->get_attachment($source_uri);
		for($i=0 ; $i<count($attachments) ; $i++){
			$infos = $this->get_infos($attachments[$i]->dest);
			 
			if(!empty($attachments[$i]->name)){
				$node = array(
					'type' => 'attachment',
					'name' => $attachments[$i]->name,
					'id' => $attachments[$i]->identifier,
					'entityType' => $infos['entityType']
				);
				if($source_id){
					$node['parent'] = $source_id;
				}
				if($attachments[$i]->property_pmb_name){
					$node['propertyPmbName'] = $attachments[$i]->property_pmb_name;
				} 
				$infos['parent'] = $attachments[$i]->identifier;
				$details[] = $node;			
			}else{				
				if($source_id){
					$infos['parent'] = $source_id;
				}
			}
			$details[] = $infos;
			$details = array_merge($details,$this->get_attachment_detail('<'.$attachments[$i]->dest.'>',$infos['id']));
		}
		return $details;
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
				?attachment pmb:name ?property_pmb_name
			}
		}');

		if($result){
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
				}
			}
		}
		return $infos;
	}
		
	public function save_graph($data, $current_scenario = 0){
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
		//on encadre les float avec des guillemets sinon json_decode arrondit l'id
		$data = json_decode(preg_replace('/:\s*(\-?\d+(\.\d+)?([e|E][\-|\+]\d+)?)/', ': "$1"', stripslashes($data)));
		$graph_data = $this->prepare_data($data);
		
		$query = 'insert into <pmb> {';
		for($i=0 ; $i<count($graph_data) ; $i++){
			$query.= '
			'.$graph_data[$i]['subject'].' '.$graph_data[$i]['predicat'].' '.$graph_data[$i]['value'].' .';			
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
			$id = $object->type.$object->id;
			if($object->type == 'attachment'){
				$id = $object->entityType.$object->id;
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
			if($data[$i]->type == "scenario" && !isset($data[$i]->parentScenario)){
				$node = $data[$i];
				if (!empty($data[$i]->startScenario)) {
					$node->children = $this->get_children($data[$i]->id,$data);				
				}
				$tree[]=$node;
			}
		}
		return $tree;
	}
	private function get_children($parent,$data){
		$children = array();
		for($i=0 ; $i<count($data) ; $i++){
			if(isset($data[$i]->parent) && $parent == $data[$i]->parent){
				$child =  $data[$i];
				$child->children = $this->get_children($child->id, $data);
				$children[] = $data[$i];
			}
		}
		return $children;
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
				$node_uri = str_replace('!!id!!',$data->entityType.$data->id,$attachment_uri);
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
				'value' => '"'.addslashes($data->entityType).'"'
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
	
	public function get_area_uri(){
		return "<http://www.pmbservices.fr/ca/Area#".$this->id.">";
	}
	
	public static function get_graphstore(){
		if(!isset(self::$graphstore)){
			$store_config = array(
					/* db */
					'db_name' => DATA_BASE,
					'db_user' => USER_NAME,
					'db_pwd' => USER_PASS,
					'db_host' => SQL_SERVER,
					/* store */
					'store_name' => 'contribution_area_graphstore',
					/* stop after 100 errors */
					'max_errors' => 100,
					'store_strip_mb_comp_str' => 0
			);
			$tab_namespaces = array(
					"dc"	=> "http://purl.org/dc/elements/1.1",
					"dct"	=> "http://purl.org/dc/terms/",
					"owl"	=> "http://www.w3.org/2002/07/owl#",
					"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
					"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
					"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
					"pmb"	=> "http://www.pmbservices.fr/ontology#",
					"ca"	=> "http://www.pmbservices.fr/ca/"
			);
				
			self::$graphstore = new onto_store_arc2($store_config);
			self::$graphstore->set_namespaces($tab_namespaces);
		}
		return self::$graphstore;		
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
					$p_rad = "<input type='radio' name='prf_rad[4]' value='R' ";
					if ($gestion_acces_empr_contribution_area_def != '1')
						$p_rad .= "checked='checked' ";
					$p_rad .= ">" . htmlentities($msg['dom_rad_calc'], ENT_QUOTES, $charset) . "</input><input type='radio' name='prf_rad[4]' value='C' ";
					if ($gestion_acces_empr_contribution_area_def == '1')
						$p_rad .= "checked='checked' ";
					$p_rad .= ">" . htmlentities($msg['dom_rad_def'], ENT_QUOTES, $charset) . " $p_sel</input>";
					$r_form = str_replace('<!-- prf_rad -->', $p_rad, $r_form);
				} else {
					$r_form = str_replace('<!-- prf_rad -->', htmlentities($dom_4->getResourceProfileName($res_prf), ENT_QUOTES, $charset), $r_form);
				}
					
				// droits/profils utilisateurs
				if ($user_rights & 1) {
					$r_rad = "<input type='radio' name='r_rad[4]' value='R' ";
					if ($gestion_acces_empr_contribution_area_def != '1')
						$r_rad .= "checked='checked' ";
					$r_rad .= ">" . htmlentities($msg['dom_rad_calc'], ENT_QUOTES, $charset) . "</input><input type='radio' name='r_rad[4]' value='C' ";
					if ($gestion_acces_empr_contribution_area_def == '1')
						$r_rad .= "checked='checked' ";
					$r_rad .= ">" . htmlentities($msg['dom_rad_def'], ENT_QUOTES, $charset) . "</input>";
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
									<td style='width:25px;' ><input type='checkbox' name='chk_rights[4][" . $k . "][" . $k2 . "]' value='1' ";
							if (isset($t_rights[$k]) && isset($t_rights[$k][$res_prf]) && ($t_rights[$k][$res_prf] & (pow(2, $k2 - 1)))) {
								$t_rows .= "checked='checked' ";
							}
							if (($user_rights & 1) == 0)
								$t_rows .= "disabled='disabled' ";
							$t_rows .= "/></td>
									<td>" . htmlentities($v2, ENT_QUOTES, $charset) . "</td>
								</tr>";
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
} // end of contribution_area
