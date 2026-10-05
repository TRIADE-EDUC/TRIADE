<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entity_locking.class.php,v 1.14.8.1 2025/04/25 09:37:43 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path.'/authority.class.php');
require_once($class_path.'/notice.class.php');
require_once($include_path.'/templates/entity_locking.tpl.php');

class entity_locking {

    /**
     * Id et type de l'entité originelle
     */
	protected $type;
	protected $id;
	protected $linked_entities = array();
	protected $locked_date;
	protected $locked_username;
	protected $locked_user_id;
	protected $date;


	public function __construct($id, $type){
	    $this->id = intval($id);
	    $this->type = $type;
	}

	/**
	 * Fonction appellée à l'affichage d'un formulaire
	 * d'entité côté gestion
	 */
	public function is_locked(){
	    global $pmb_entity_locked_time, $PMBuserid;
	    if($pmb_entity_locked_time){
	        $query = 'SELECT * FROM locked_entities WHERE id_entity='.$this->id.' AND type="'.$this->type.'" LIMIT 1';
	        $result = pmb_mysql_query($query);
	        if(pmb_mysql_num_rows($result)){ //L'entité est verrouillée
	            $row = pmb_mysql_fetch_assoc($result);

	            if (!empty($row['empr_id'])) {
	                $query = "select id_empr, empr_nom from empr where id_empr=".$row['empr_id'];
	                $result = pmb_mysql_query($query);
	                if(pmb_mysql_num_rows($result)){
	                    $empr_row = pmb_mysql_fetch_assoc($result);
	                    $this->locked_user_id = $empr_row['id_empr'];
	                    $this->locked_username = $empr_row['empr_nom'];
	                    $this->date = $row['date'];
	                }
	            } elseif (!empty($row['user_id'])) {
	                $query = "select userid, username from users where userid=".$row['user_id'];
	                $result = pmb_mysql_query($query);
	                if(pmb_mysql_num_rows($result)){
	                    $user_row = pmb_mysql_fetch_assoc($result);
	                    $this->locked_user_id = $user_row['userid'];
	                    $this->locked_username = $user_row['username'];
	                    $this->date = $row['date'];
	                }
	            } else {
	                return false;
	            }

	            $timestamp_locked_time = $pmb_entity_locked_time * 60;
	            $current_time = time();
	            $date = new DateTime($this->date);
	            $date = $date->getTimestamp();
	            if (!$pmb_entity_locked_time || (($timestamp_locked_time + $date) < $current_time) || ($this->locked_user_id == $PMBuserid)) {
	                $this->unlock_entity(true);
	                return false;
	            }
	            $this->locked_date = $row['date'];

	            return true;
	        }
	    }
	    return false;
	}

	public function lock_entity(){
	    global $PMBuserid, $pmb_entity_locked_time;
	    if($pmb_entity_locked_time && $this->id){
	        $this->get_linked_entities();

	        //On commence par verrouiller l'entité de base.
	        $values = '("'.$this->id.'", "'.$this->type.'", "'.(date ("Y-m-d H:i:s")).'", "0", "0", "'.$PMBuserid.'")';
	        //On parcourt toutes les entités qui lui sont liées


	        /**
	         * TODO: appel récursif avec définition d'un niveau (à voir au moment de l'integration dans les contrib)
	         */
	        foreach($this->linked_entities as $entity_type => $ids){
	            foreach($ids as $id){
	                $values.= ', ("'.$id.'", "'.$entity_type.'", "'.(date ("Y-m-d H:i:s")).'", "'.$this->id.'", "'.$this->type.'", "'.$PMBuserid.'")';
	            }
	        }
	        $this->locked_user_id = $PMBuserid;
	        $query = 'insert into locked_entities (id_entity, type, date, parent_id, parent_type, user_id)
				values '.$values;
	        pmb_mysql_query($query);
	        $this->locked_date = date ("Y-m-d H:i:s");
	    }
	}

	/**
	 * Récupération de toutes les entités liées à l'entité de départ
	 */
	protected function get_linked_entities(){
	    switch($this->type){
	        case TYPE_AUTHOR :
	            $this->get_authorities_linked();
	            break;
	        case TYPE_CATEGORY :
	            $this->get_authorities_linked();
	            $this->get_linked_categories();
	            break;
	        case TYPE_COLLECTION :
	            $this->get_authorities_linked();
	            break;
	        case TYPE_CONCEPT :
	            $this->get_authorities_linked();

	            break;
	        case TYPE_INDEXINT :
	            $this->get_authorities_linked();
	            break;
	        case TYPE_NOTICE :
	            //Récupération des identifiants de notices liées réciproquement
	            $this->execute_query(
	               'select linked_notice from notices_relations
                    where num_notice ='.$this->id.'
                    and num_reverse_link!=0',
                    'linked_notice',
	               TYPE_NOTICE
	            );

	            //Récupération des titres uniformes associés à la notice
	            $this->execute_query(
	                'select ntu_num_tu from notices_titres_uniformes
                    where ntu_num_notice='.$this->id,
	                'ntu_num_tu',
	                TYPE_TITRE_UNIFORME
	            );
	            break;
	        case TYPE_PUBLISHER :
	            $this->get_authorities_linked();
	            break;
	        case TYPE_SERIE :
	            $this->get_authorities_linked();
	            break;
	        case TYPE_SUBCOLLECTION :
	            $this->get_authorities_linked();
	            break;
	        case TYPE_TITRE_UNIFORME :
	            $this->get_authorities_linked();
	            $this->get_linked_works();

	            //Récupération des notices associées au titre uniforme
	            $this->execute_query(
	                'select ntu_num_notice from notices_titres_uniformes
                    where ntu_num_tu='.$this->id,
	                'ntu_num_notice',
	                TYPE_NOTICE
                );
	            break;
	        case TYPE_CMS_ARTICLE:
	        case TYPE_CMS_SECTION:
	            break;
	    }
	    return $this->linked_entities;
	}

	/**
	 * Wrapper SQL basic queries
	 */
	protected function execute_query($query, $field, $type){
	    if(!isset($this->linked_entities[$type])){
	        $this->linked_entities[$type] = array();
	    }
	    $result = pmb_mysql_query($query);
	    if(pmb_mysql_num_rows($result)){
	        while($row = pmb_mysql_fetch_assoc($result)){
	            $this->linked_entities[$type][] = $row[$field];
	        }
	    }
	}

	protected function get_linked_categories(){
	    $query = 'select num_noeud_dest from voir_aussi where num_noeud_orig = ' . $this->id;
	    $result = pmb_mysql_query($query);
	    $linked_categories = array();
	    if(pmb_mysql_num_rows($result)){
	        while($row = pmb_mysql_fetch_assoc($result)) {
	            $linked_categories[] = $row['num_noeud_dest'];
	        }
	    }
	    foreach($linked_categories as $categ) {
	        $query = 'select distinct num_noeud_dest from voir_aussi where num_noeud_orig = ' . $categ . ' and num_noeud_dest = ' . $this->id;
	        $result = pmb_mysql_query($query);
	        $linked_categories = array();
	        if(pmb_mysql_num_rows($result)){
	            if(!isset($this->linked_entities[TYPE_CATEGORY])){
	                $this->linked_entities[TYPE_CATEGORY] = array();
	            }
	            $this->linked_entities[TYPE_CATEGORY][] = $categ;
	        }
	    }
	}

	/**
	 * Récupération des autorités liées (en fonction du type de celle de départ)
	 */
	protected function get_authorities_linked(){
	    $query = 'select aut_link_to, aut_link_to_num
                from aut_link where
                and aut_link_from_num='.$this->id.'
                and aut_link_from = '.authority::$type_table[$this->type];

	    $result = pmb_mysql_query($query);

	    if(pmb_mysql_num_rows($result)){
	        while($row = pmb_mysql_fetch_assoc($result)){
	            if(!isset($this->linked_entities[array_search($row['aut_link_to'], authority::$type_table)])){
	                $this->linked_entities[array_search($row['aut_link_to'], authority::$type_table)] = array();
	            }
	            $this->linked_entities[array_search($row['aut_link_to'], authority::$type_table)][] = $row['aut_link_to_num'];
	        }
	    }
	}

	protected function get_linked_works(){
	    $oeuvre_link = marc_list_collection::get_instance('oeuvre_link');
	    $query = 'select oeuvre_link_to, oeuvre_link_type from tu_oeuvres_links where oeuvre_link_from = '. $this->id;
	    $result = pmb_mysql_query($query);
	    if(pmb_mysql_num_rows($result)){
	        while($row = pmb_mysql_fetch_assoc($result)) {
	            $query = 'select oeuvre_link_to from tu_oeuvres_links where oeuvre_link_from ='.$this->id.'
                        and oeuvre_link_to ='.$row['oeuvre_link_to'].' and oeuvre_link_type = "'.$oeuvre_link->inverse_of[$row['oeuvre_link_type']].'"';
	            $result2 = pmb_mysql_query($query);
	            if(pmb_mysql_num_rows($result2)){
	                if(!isset($this->linked_entities[TYPE_TITRE_UNIFORME])){
	                    $this->linked_entities[TYPE_TITRE_UNIFORME] = array();
	                }
	                $this->linked_entities[TYPE_TITRE_UNIFORME][] = $row['oeuvre_link_to'];
	            }
	        }
	    }
	}

	protected function get_linked_concepts(){
	    /**
	     * TODO: skos, bloquer tous les concepts associés au concept courant
	     */
	    $concept_uri = onto_common_uri::get_uri($this->id);
	    $query = 'select ?uri where {
                    <'.$concept_uri.'> ?p ?uri .
	                ?uri rdf:type skos:Concept
				}';
	    skos_datastore::query($query);
	    if(skos_datastore::num_rows()){
	        $related_concept = skos_datastore::get_result();
	        foreach($related_concept as $concept_found){
	            if(!isset($this->linked_entities[TYPE_CONCEPT])){
	                $this->linked_entities[TYPE_CONCEPT] = array();
	            }
	            $this->linked_entities[TYPE_CONCEPT][] = $concept_found->uri;
	        }
	    }
	}

	public function unlock_entity($force = false){
	    global $PMBuserid;
	    if (isset($this->id) && (($this->locked_user_id == $PMBuserid) || $force)) {
    	    $query = 'delete from locked_entities where id_entity ='.$this->id.' and type='.$this->type;
    	    pmb_mysql_query($query);
    	    file_put_contents('/tmp/mysql.txt', 'query : '.$query."\n", FILE_APPEND);
    	    $query = 'delete from locked_entities where parent_id ='.$this->id.' and parent_type='.$this->type;
    	    pmb_mysql_query($query);
	    }
	}

	public function get_locked_form(){
	    global $entity_locked_form;
// 	    global $pmb_entity_locked_time;
	    global $pmb_entity_locked_refresh_time;
	    $form = str_replace('!!entity_force_edition!!', '', $entity_locked_form);
	    $form = str_replace('!!action!!', '', $form);
	    $form = str_replace('!!entity_locked_username!!', $this->locked_username, $form);
	    $form = str_replace('!!entity_locked_date!!', formatdate($this->locked_date, 0), $form);
	    $form = str_replace('!!entity_id!!', $this->id, $form);
	    $form = str_replace('!!entity_type!!', $this->type, $form);
	    $form = str_replace('!!entity_locked_userid!!', $this->locked_user_id, $form);
// 	    $form = str_replace('!!entity_locking_time!!', strtotime($this->locked_date)+($pmb_entity_locked_time*60), $form);
	    $form = str_replace('!!entity_locked_refresh_time!!', ($pmb_entity_locked_refresh_time*60*1000), $form);
	    return $form;
	}

	public function get_polling_script(){
	    global $entity_polling_script;
	    global $pmb_entity_locked_refresh_time;
	    global $pmb_entity_locked_time;
	    if($pmb_entity_locked_time){
	        $script = str_replace('!!entity_id!!', $this->id, $entity_polling_script);
	        $script = str_replace('!!entity_type!!', $this->type, $script);
	        $script = str_replace('!!entity_locked_userid!!', $this->locked_user_id ?? '', $script);
	        $script = str_replace('!!entity_locked_refresh_time!!', ($pmb_entity_locked_refresh_time*60*1000), $script);
	        return $script;
	    }
	    return '';
	}

	public function get_locked_username() {
	    return $this->locked_username;
	}

	public function get_save_error_message() {
	    global $pmb_entity_locked_time;
	    global $save_error_message;
	    $message = str_replace('!!entity_locked_username!!', $this->locked_username, $save_error_message);
	    $message = str_replace('!!entity_locking_time!!', strtotime($this->locked_date)+($pmb_entity_locked_time*60), $message);
	    $message = str_replace('!!action!!', '', $message);

	    return $message;
	}

	public function set_user_id($user_id){
	    $this->locked_user_id = ($user_id*1);
	}

	public function get_locked_user_id(){
	    return $this->locked_user_id;
	}

	public function refresh_date(){
	    $query = 'update locked_entities set date="'.(date ("Y-m-d H:i:s")).'"
        where id_entity='.$this->id.' and type='.$this->type.' and user_id = '.$this->locked_user_id.'
        or (parent_id='.$this->id.' and parent_type='.$this->type.' and user_id = '.$this->locked_user_id.')';
	    pmb_mysql_query($query);
	}

	public function get_locked_date(){
	   return $this->locked_date;
	}

	public function is_available(){
	    global $pmb_entity_locked_time, $msg;

	    $query = 'SELECT * FROM locked_entities WHERE id_entity='.$this->id.' AND type="'.$this->type.'" LIMIT 1';

	    $result = pmb_mysql_query($query);
	    if(pmb_mysql_num_rows($result)){
	        $row = pmb_mysql_fetch_assoc($result);
	        $locked_date = new DateTime($row['date']);
	        $locked_date = $locked_date->getTimestamp();

	        $current_date = new DateTime();
	        $current_date = $current_date->getTimestamp();
	        if(($locked_date + (60*$pmb_entity_locked_time)) < $current_date){
	            $this->unlock_entity(true);
	            return encoding_normalize::json_encode(array('status'=> true, 'message'=>$msg['entity_unlocked']));
	        }

	        if (!empty($row['empr_id'])) {
	            $query = "select empr_nom from empr where id_empr=".$this->locked_user_id;
	            $result = pmb_mysql_query($query);
	            if(pmb_mysql_num_rows($result)){
	                $row = pmb_mysql_fetch_assoc($result);
	                $this->locked_username = $row['empr_nom'];
	            }
	        } elseif (!empty($row['user_id'])) {
	            $query = "select username from users where userid=".$this->locked_user_id;
	            $result = pmb_mysql_query($query);
	            if(pmb_mysql_num_rows($result)){
	                $row = pmb_mysql_fetch_assoc($result);
	                $this->locked_username = $row['username'];
	            }
	        } else {
	            return encoding_normalize::json_encode(array('status'=> true, 'message'=>$msg['entity_unlocked']));
	        }

	        return encoding_normalize::json_encode(array('status'=> false, 'message' => str_replace('!!entity_locked_username!!', $this->locked_username, $msg['entity_currently_locked'])));
	    }
	    return encoding_normalize::json_encode(array('status'=> true, 'message'=>$msg['entity_unlocked']));
	}

}
