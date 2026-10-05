<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesClean.class.php,v 1.59.2.8 2024/12/12 15:55:23 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Ark\Models\ArkModel;

global $class_path, $include_path;
require_once($class_path."/external_services.class.php");
require_once("$include_path/mysql_connect.inc.php");

/*
 ATTENTION: Si vous modifiez de fichier vous devez probablement modifier le fichier pmbesIndex.class.php
*/
class pmbesClean extends external_services_api_class {
	
	protected static $progression = 0; // entre 0 et 100
	protected static $packet_size = 0;
	public static $indexation_by_fields = false;
	protected $index_step_name = '';
	protected $index_fields = '';
	protected static $index_fields_number = 1;
	protected static $index_fields_position = 1;
	
	public function __construct($external_services, $group_name, &$proxy_parent) {
		parent::__construct($external_services, $group_name, $proxy_parent);
		
	}
	
	protected function getFormattedResponse($title, $message, $affected, $bad_user_rights=0) {
		return array(
				'title' => $title,
				'message' => $message,
				'affected' => $affected,
				'bad_user_rights' => $bad_user_rights
		);
	}
	
	protected function getFormattedIndexResponse($name, $affected, $bad_user_rights=0) {
		global $msg;
		
		$title = $msg['nettoyage_reindex_'.$name];
// 		if($name == 'global' || $name == 'faq') {
			$message = $affected.' '.$msg['nettoyage_res_reindex_'.$name];
// 		} else {
// 			$message = $msg['nettoyage_reindex_'.$name]." $affected ".$msg['nettoyage_res_reindex_'.$name];
// 		}
		return $this->getFormattedResponse($title, $message, $affected, $bad_user_rights);
	}
	
	protected function getFormattedSphinxIndexResponse($name, $affected, $bad_user_rights=0) {
	    global $msg;
	    
	    $title = "[Sphinx] ".$msg['nettoyage_reindex_'.$name];
	    $message = $affected.' '.$msg['nettoyage_res_reindex_'.$name];
	    return $this->getFormattedResponse($title, $message, $affected, $bad_user_rights);
	}
	
	protected function getFormattedCleanResponse($name, $affected, $bad_user_rights=0) {
		global $msg;
		
		$title = $msg['nettoyage_suppr_'.$name];
		$message = $affected.' '.$msg['nettoyage_res_suppr_'.$name];
		return $this->getFormattedResponse($title, $message, $affected, $bad_user_rights);
	}

	protected function indexAllEntities($entity_type) {
		$indexed = 0;
		switch ($entity_type) {
			case TYPE_NOTICE:
				notice::set_deleted_index(true);
				$query = $this->getQueryEntities($entity_type);
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)) {
					while($mesNotices = pmb_mysql_fetch_assoc($result)) {
						$info = notice::indexation_prepare($mesNotices['id']);
						
						// Mise à jour de la table "notices_global_index"
						notice::majNoticesGlobalIndex($mesNotices['id']);
						
						// Mise à jour de la table "notices_mots_global_index"
						notice::majNoticesMotsGlobalIndex($mesNotices['id']);
						notice::indexation_restaure($info);
						
						$mesNotices = null;
					}
					pmb_mysql_free_result($result);
					$indexed = pmb_mysql_result(pmb_mysql_query("SELECT count(1) FROM notices_global_index"), 0);
				}
				break;
			default:
				$query = $this->getQueryEntities($entity_type);
				$indexed = netbase_authorities::index_from_query($query, authority::$type_table[$entity_type]);
				break;
		}
		//mise à jour de la progression sur ce type d'entité
		static::$progression = 100;
		return $indexed;
	}
	
	protected function indexEntitiesByFields($entity_type) {
	    switch ($entity_type) {
	        case TYPE_NOTICE:
	            if(!static::$progression) {
    	            // Remplissage de la table notices_global_index au debut
    	            notice::set_deleted_index(true);
    	            $query = $this->getQueryEntities($entity_type);
    	            $result = pmb_mysql_query($query);
    	            if(pmb_mysql_num_rows($result)) {
    	                while($mesNotices = pmb_mysql_fetch_assoc($result)) {
    	                    $info = notice::indexation_prepare($mesNotices['id']);
    	                    
    	                    // Mise à jour de la table "notices_global_index"
    	                    notice::majNoticesGlobalIndex($mesNotices['id']);
    	                    
    	                    notice::indexation_restaure($info);
    	                    
    	                    $mesNotices = null;
    	                }
    	                pmb_mysql_free_result($result);
    	            }
	            }
                if (!empty($this->index_step_name) && !empty($this->index_fields)) {
                    netbase_records::index_by_fields($this->index_step_name, $this->index_fields);
                    static::$progression = (static::$index_fields_position/static::$index_fields_number)*100;
                } else {
                    netbase_records::index_steps_fields();
                }
                $indexed = pmb_mysql_result(pmb_mysql_query("SELECT count(1) FROM notices_global_index"), 0);
	            break;
	        default:
	            netbase_authorities::set_object_type(authority::$type_table[$entity_type]);
	            if (!empty($this->index_step_name) && !empty($this->index_fields)) {
	                netbase_authorities::index_by_fields($this->index_step_name, $this->index_fields);
	                static::$progression = (static::$index_fields_position/static::$index_fields_number)*100;
	            } else {
	                netbase_authorities::index_steps_fields();
	            }
	            $query = $this->getQueryEntities($entity_type);
	            $result = pmb_mysql_query($query);
	            $indexed = pmb_mysql_num_rows($result);
	            break;
	    }
	    return $indexed;
	}
	
	protected function indexStackEntities($entity_type) {
		$indexed = 0;
		switch ($entity_type) {
			case TYPE_NOTICE:
				notice::set_deleted_index(true);
				break;
		}
		$entity_type = intval($entity_type);
		$query = "SELECT *
				FROM indexation_stack
				WHERE indexation_stack_entity_type = ".$entity_type."
				AND indexation_stack_datatype = 'scheduler'
				ORDER BY indexation_stack_timestamp, indexation_stack_entity_id, indexation_stack_entity_type, indexation_stack_datatype, indexation_stack_informations
				LIMIT ".static::$packet_size;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_assoc($result)){
				scheduler_indexation_stack::index_entity($row['indexation_stack_entity_id'], $row['indexation_stack_entity_type'], $row['indexation_stack_datatype'], $row['indexation_stack_informations']);
				$indexed++;
			}
		}
		//mise à jour de la progression sur ce type d'entité
		$query = "SELECT count(*) FROM indexation_stack WHERE indexation_stack_entity_type = ".$entity_type." AND indexation_stack_datatype = 'scheduler'";
		$not_indexed = pmb_mysql_result(pmb_mysql_query($query), 0);
		if($not_indexed) {
			$query = $this->getQueryEntities($entity_type);
			if($query) {
				$total_to_index = pmb_mysql_num_rows(pmb_mysql_query($query));
				static::$progression = round((($total_to_index - $not_indexed) / $total_to_index)*100, 2);
				if(static::$progression < 0.01) {
					static::$progression = 0.01;
				}
			}
			//assurons-nous de ne pas rester à 0% pour rejouer une opération de départ
			//ex : truncate des tables d'indexation
			if(empty(static::$progression) || static::$progression < 0.01) {
				static::$progression = 0.01;
			}
			if(static::$progression >= 100) {
			    static::$progression = 99.99;
			}
		} else {
			static::$progression = 100;
		}
		return $indexed;
	}
	
	protected function indexEntities($entity_type){
		$entity_type = intval($entity_type);
		if(static::$indexation_by_fields && !empty($this->index_step_name) && !empty($this->index_fields)) {
		    $indexed = $this->indexEntitiesByFields($entity_type);
		} elseif(static::$packet_size) {
			$indexed = $this->indexStackEntities($entity_type);
		} else {
			$indexed = $this->indexAllEntities($entity_type);
		}
		return $indexed;
	}
	
	public function indexGlobal($step_name='', $fields = []) {
	    global $pmb_clean_mode;
	    
	    // Indexation par champ activée ? (sera activée par défaut par la suite))
	    if(!empty($pmb_clean_mode)) {
	        static::$indexation_by_fields = true;
	    }
	    $this->index_step_name = $step_name;
	    $this->index_fields = $fields;
		if (SESSrights & ADMINISTRATION_AUTH) {
			//modifions les limites lorsque l'on indexe tout d'un coup
			if(static::$packet_size == 0) {
				ini_set("memory_limit","-1");
				pmb_mysql_query("set wait_timeout=3600");
			} else {
				$max_execution_time = intval(ini_get('max_execution_time'));
				if(!$max_execution_time) {
					@set_time_limit(600); // on limite à 600 secondes
				}
				pmb_mysql_query("set wait_timeout=300");
			}
			if(!static::$progression) {
				//remise a zero de la table au début
				pmb_mysql_query("TRUNCATE TABLE notices_global_index");
				pmb_mysql_query("ALTER TABLE notices_global_index DISABLE KEYS");
				
				netbase_records::raz_index();
				
				//Indexation par paquet : on ajoute toutes les notices dans la pile d'indexation
				if(static::$packet_size) {
					$query = $this->getQueryEntities(TYPE_NOTICE);
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)) {
						while($mesNotices = pmb_mysql_fetch_assoc($result)) {
							scheduler_indexation_stack::push($mesNotices['id'], TYPE_NOTICE);
						}
						scheduler_indexation_stack::push_database();
					}
				}
			}
			
			$affected = $this->indexEntities(TYPE_NOTICE);
			
			if(static::$progression == 100) {
				pmb_mysql_query("ALTER TABLE notices_global_index ENABLE KEYS");
				netbase_records::enable_index();
			}
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedIndexResponse(
				'global',
				$affected,
				$bad_user_rights
		);
	}
	
	public function indexSphinxRecords() {
	    global $dbh, $sphinx_active;
	    
	    if (!$sphinx_active) {
	        return array();
	    }
	    $affected = 0;
	    if (SESSrights & ADMINISTRATION_AUTH) {
	        $index_class = 'sphinx_records_indexer';
	        if(class_exists($index_class)){
	            $sconf = new $index_class();
	            $sconf->checkExistingIndexes();
	            $sconf->fillIndexes([]);
	            
	            $affected = pmb_mysql_result(pmb_mysql_query("SELECT count(DISTINCT id_notice) FROM notices_fields_global_index"), 0);
	        }
	        $bad_user_rights = 0;
	        
	        //Re-ouverture d'une connexion MySQL en cas d'operation longue sur Sphinx
	        pmb_mysql_close($dbh);
	        $dbh = connection_mysql();
	    } else {
	        $bad_user_rights = 1;
	    }
	    
	    return $this->getFormattedIndexResponse(
	        'sphinxRecords',
	        $affected,
	        $bad_user_rights
	        );
	}
	
	public function indexNotices() {
		global $msg, $charset, $PMBusername;
		
		$result = '';
		if (SESSrights & ADMINISTRATION_AUTH) {
			//NOTICES
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_notices"], ENT_QUOTES, $charset)."</h3>";
			pmb_mysql_query("set wait_timeout=3600");
			$query = pmb_mysql_query("SELECT notice_id FROM notices");
			if(pmb_mysql_num_rows($query)) {
				while(($row = pmb_mysql_fetch_object($query))) {
					// constitution des pseudo-indexes
					notice::majNotices($row->notice_id);
				}
				pmb_mysql_free_result($query);
			}
			$notices = pmb_mysql_query("SELECT count(1) FROM notices");
			$count = pmb_mysql_result($notices, 0, 0);
			$result .= "".htmlentities($msg["nettoyage_reindex_notices"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_notices"], ENT_QUOTES, $charset);
			
			//AUTEURS
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_authors"], ENT_QUOTES, $charset)."</h3>";
			
			$query = pmb_mysql_query("SELECT author_id as id,concat(author_name,' ',author_rejete,' ', author_lieu, ' ',author_ville,' ',author_pays,' ',author_numero,' ',author_subdivision) as auteur from authors");
			if (pmb_mysql_num_rows($query)) {
				while(($row = pmb_mysql_fetch_object($query))) {
					// constitution des pseudo-indexes
					$ind_elt = strip_empty_chars($row->auteur);
					$req_update = "UPDATE authors ";
					$req_update .= " SET index_author=' {$ind_elt} '";
					$req_update .= " WHERE author_id=$row->id ";
					pmb_mysql_query($req_update);
				}
				pmb_mysql_free_result($query);
			}
			$elts = pmb_mysql_query("SELECT count(1) FROM authors");
			$count = pmb_mysql_result($elts, 0, 0);
			$result .= "".htmlentities($msg["nettoyage_reindex_authors"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_authors"], ENT_QUOTES, $charset);
			
			//EDITEURS
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_publishers"], ENT_QUOTES, $charset)."</h3>";
			
			$query = pmb_mysql_query("SELECT ed_id as id, ed_name as publisher, ed_ville, ed_pays from publishers");
			if (pmb_mysql_num_rows($query)) {
				while(($row = pmb_mysql_fetch_object($query))) {
					// constitution des pseudo-indexes
					$ind_elt = strip_empty_chars($row->publisher." ".$row->ed_ville." ".$row->ed_pays);
					$req_update = "UPDATE publishers ";
					$req_update .= " SET index_publisher=' {$ind_elt} '";
					$req_update .= " WHERE ed_id=$row->id ";
					pmb_mysql_query($req_update);
				}
				pmb_mysql_free_result($query);
			}
			$elts = pmb_mysql_query("SELECT count(1) FROM publishers");
			$count = pmb_mysql_result($elts, 0, 0);
			$result .= "".htmlentities($msg["nettoyage_reindex_publishers"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_publishers"], ENT_QUOTES, $charset);
			
			//CATEGORIES
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_categories"], ENT_QUOTES, $charset)."</h3>";
			
			$req = "select num_noeud, langue, libelle_categorie from categories";
			$query = pmb_mysql_query($req);
			if (pmb_mysql_num_rows($query)) {
				while($row = pmb_mysql_fetch_object($query)) {
					// constitution des pseudo-indexes
					$ind_elt = strip_empty_words($row->libelle_categorie, $row->langue);
					
					$req_update = "UPDATE categories ";
					$req_update.= "SET index_categorie=' {$ind_elt} '";
					$req_update.= "WHERE num_noeud='".$row->num_noeud."' and langue='".$row->langue."' ";
					pmb_mysql_query($req_update);
				}
				pmb_mysql_free_result($query);
			}
			$elts = pmb_mysql_query("SELECT count(1) FROM categories");
			$count = pmb_mysql_result($elts, 0, 0);
			$result .= "".htmlentities($msg["nettoyage_reindex_categories"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_categories"], ENT_QUOTES, $charset);
			
			//COLLECTIONS
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_collections"], ENT_QUOTES, $charset)."</h3>";
			
			$query = pmb_mysql_query("SELECT collection_id as id, collection_name as collection from collections");
			if (pmb_mysql_num_rows($query)) {
				while(($row = pmb_mysql_fetch_object($query))) {
					// constitution des pseudo-indexes
					$ind_elt = strip_empty_words($row->collection);
					
					$req_update = "UPDATE collections ";
					$req_update .= " SET index_coll=' {$ind_elt} '";
					$req_update .= " WHERE collection_id=$row->id ";
					pmb_mysql_query($req_update);
				}
				pmb_mysql_free_result($query);
			}
			$elts = pmb_mysql_query("SELECT count(1) FROM collections");
			$count = pmb_mysql_result($elts, 0, 0);
			$result .= "".htmlentities($msg["nettoyage_reindex_collections"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_collections"], ENT_QUOTES, $charset);
			
			//SOUSCOLLECTIONS
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_sub_collections"], ENT_QUOTES, $charset)."</h3>";
			
			$query = pmb_mysql_query("SELECT sub_coll_id as id, sub_coll_name as sub_collection from sub_collections");
			if (pmb_mysql_num_rows($query)) {
				while(($row = pmb_mysql_fetch_object($query))) {
					// constitution des pseudo-indexes
					$ind_elt = strip_empty_words($row->sub_collection);
					
					$req_update = "UPDATE sub_collections ";
					$req_update .= " SET index_sub_coll=' {$ind_elt} '";
					$req_update .= " WHERE sub_coll_id=$row->id ";
					pmb_mysql_query($req_update);
				}
				pmb_mysql_free_result($query);
			}
			$elts = pmb_mysql_query("SELECT count(1) FROM sub_collections");
			$count = pmb_mysql_result($elts, 0, 0);
			$result .= "".htmlentities($msg["nettoyage_reindex_sub_collections"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_sub_collections"], ENT_QUOTES, $charset);
			
			//SERIES
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_series"], ENT_QUOTES, $charset)."</h3>";
			
			$query = pmb_mysql_query("SELECT serie_id as id, serie_name from series");
			if (pmb_mysql_num_rows($query)) {
				while(($row = pmb_mysql_fetch_object($query))) {
					// constitution des pseudo-indexes
					$ind_elt = strip_empty_words($row->serie_name);
					
					$req_update = "UPDATE series ";
					$req_update .= " SET serie_index=' {$ind_elt} '";
					$req_update .= " WHERE serie_id=$row->id ";
					pmb_mysql_query($req_update);
				}
				pmb_mysql_free_result($query);
			}
			$elts = pmb_mysql_query("SELECT count(1) FROM series");
			$count = pmb_mysql_result($elts, 0, 0);
			$result .= "".htmlentities($msg["nettoyage_reindex_series"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_series"], ENT_QUOTES, $charset);
			
			//DEWEY
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_indexint"], ENT_QUOTES, $charset)."</h3>";
			
			$query = pmb_mysql_query("SELECT indexint_id as id, concat(indexint_name,' ',indexint_comment) as index_indexint from indexint");
			if (pmb_mysql_num_rows($query)) {
				while(($row = pmb_mysql_fetch_object($query))) {
					// constitution des pseudo-indexes
					$ind_elt = strip_empty_words($row->index_indexint);
					
					$req_update = "UPDATE indexint ";
					$req_update .= " SET index_indexint=' {$ind_elt} '";
					$req_update .= " WHERE indexint_id=$row->id ";
					pmb_mysql_query($req_update);
				}
				pmb_mysql_free_result($query);
			}
			$elts = pmb_mysql_query("SELECT count(1) FROM indexint");
			$count = pmb_mysql_result($elts, 0, 0);
			$result .= "".htmlentities($msg["nettoyage_reindex_indexint"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_indexint"], ENT_QUOTES, $charset);
			
			static::$progression = 100;
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		
		return $result;
	}
	
	public function cleanAuthors() {
		global $msg, $charset;
		
		$message = '';
		if (SESSrights & ADMINISTRATION_AUTH) {
			//1er passage
// 			$result .= "<h3>".htmlentities($msg["nettoyage_suppr_auteurs"], ENT_QUOTES, $charset)."</h3>";
			
			$res = pmb_mysql_query("SELECT author_id from authors left join responsability on responsability_author=author_id where responsability_author is null and author_see=0 ");
			$affected=0;
			if(pmb_mysql_num_rows($res)){
				while ($ligne=pmb_mysql_fetch_object($res)) {
					$auteur=new auteur($ligne->author_id);
					$deleted = $auteur->delete();
					if(!$deleted) {
						$affected++;
					}
				}
			}
			
			//Nettoyage des informations d'autorités pour les auteurs
			auteur::delete_autority_sources();
			
			//2eme passage
			$message .= "<h3>".htmlentities($msg["nettoyage_renvoi_auteurs"], ENT_QUOTES, $charset)."</h3>";
			
			pmb_mysql_query("update authors A1 left join authors A2 on A1.author_see=A2.author_id set A1.author_see=0 where A2.author_id is null");
			$affected += pmb_mysql_affected_rows();
			$message .= $affected." ".htmlentities($msg["nettoyage_res_suppr_auteurs"], ENT_QUOTES, $charset);
			pmb_mysql_query('OPTIMIZE TABLE authors');
			
			$affected = 0;
			//3eme passage
			$message .= "<h3>".htmlentities($msg["nettoyage_responsabilites"], ENT_QUOTES, $charset)." : 1</h3>";
			
			pmb_mysql_query("delete responsability from responsability left join notices on responsability_notice=notice_id where notice_id is null ");
			$affected += pmb_mysql_affected_rows();
			
			//4eme passage
			$message .= "<h3>".htmlentities($msg["nettoyage_responsabilites"], ENT_QUOTES, $charset)." : 2</h3>";
			
			pmb_mysql_query("delete responsability from responsability left join authors on responsability_author=author_id where author_id is null ");
			$affected += pmb_mysql_affected_rows();
			
			$message .= $affected." ".htmlentities($msg["nettoyage_res_responsabilites"], ENT_QUOTES, $charset);
			pmb_mysql_query('OPTIMIZE TABLE authors');
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedResponse(
				$msg["nettoyage_suppr_auteurs"],
				$message,
				$affected,
				$bad_user_rights
				);
	}
	
	public function cleanPublishers() {
		$affected = 0;
		if (SESSrights & ADMINISTRATION_AUTH) {
			$query = "SELECT DISTINCT ed_id FROM publishers LEFT JOIN notices n1 ON n1.ed1_id=ed_id LEFT JOIN notices n2 ON n2.ed2_id=ed_id LEFT JOIN collections ON ed_id=collection_parent WHERE n1.notice_id IS NULL AND  n2.notice_id IS NULL AND collection_id IS NULL";
			$res=pmb_mysql_query($query);
			if(pmb_mysql_num_rows($res)){
				while ($ligne = pmb_mysql_fetch_object($res)) {
					$editeur = new editeur($ligne->ed_id);
					$deleted = $editeur->delete();
					if(!$deleted) {
						$affected++;
					}
				}
			}
			pmb_mysql_query('OPTIMIZE TABLE publishers');
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedCleanResponse(
				'editeurs',
				$affected,
				$bad_user_rights
		);
	}
	
	public function cleanCollections() {
		$affected = 0;
		if (SESSrights & ADMINISTRATION_AUTH) {
			$query = pmb_mysql_query("SELECT collection_id from collections left join notices on collection_id=coll_id left join sub_collections on sub_coll_parent=collection_id where coll_id is null and sub_coll_parent is null ");
			if(pmb_mysql_num_rows($query)){
				while ($ligne = pmb_mysql_fetch_object($query)) {
					$coll = new collection($ligne->collection_id);
					$deleted = $coll->delete();
					if(!$deleted) {
						$affected++;
					}
				}
			}
			//Nettoyage des informations d'autorités pour les collections
			collection::delete_autority_sources();
			
			pmb_mysql_query("update notices left join collections ON collection_id=coll_id SET coll_id=0, subcoll_id=0 WHERE collection_id is null");
			pmb_mysql_query('OPTIMIZE TABLE collections');
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedCleanResponse(
				'collections',
				$affected,
				$bad_user_rights
		);
	}
	
	public function cleanSubcollections() {
		$affected = 0;
		if (SESSrights & ADMINISTRATION_AUTH) {
			$query = pmb_mysql_query("SELECT sub_coll_id from sub_collections left join notices on sub_coll_id=subcoll_id where subcoll_id is null ");
			if(pmb_mysql_num_rows($query)){
				while ($ligne = pmb_mysql_fetch_object($query)) {
					$subcoll = new subcollection($ligne->sub_coll_id);
					$deleted = $subcoll->delete();
					if(!$deleted) {
						$affected++;
					}
				}
			}
			
			//Nettoyage des informations d'autorités pour les sous collections
			subcollection::delete_autority_sources();
			
			pmb_mysql_query("update notices left join sub_collections ON sub_coll_id=subcoll_id SET subcoll_id=0 WHERE sub_coll_id is null");
			pmb_mysql_query('OPTIMIZE TABLE sub_collections');
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedCleanResponse(
				'subcollections',
				$affected,
				$bad_user_rights
		);
	}
	
	public function cleanCategories() {
		$affected=0 ;
		if (SESSrights & ADMINISTRATION_AUTH) {
			$list_thesaurus = thesaurus::getThesaurusList();
			foreach($list_thesaurus as $id_thesaurus=>$libelle_thesaurus) {
				$thes = new thesaurus($id_thesaurus);
				$noeud_rac =  $thes->num_noeud_racine;
				$r = noeuds::listChilds($noeud_rac, 0);
				while($row = pmb_mysql_fetch_object($r)){
					noeuds::process_categ($row->id_noeud);
				}
			}
			
			//TODO non repris >> Utilité ???
			//	$delete = pmb_mysql_query("delete from categories where categ_libelle='#deleted#'");
			noeuds::optimize();
			categories::optimize();
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedCleanResponse(
				'categories',
				$affected,
				$bad_user_rights
		);
	}
	
	public function cleanSeries() {
		$affected=0;
		if (SESSrights & ADMINISTRATION_AUTH) {
			$query = pmb_mysql_query("SELECT serie_id from series left join notices on tparent_id=serie_id where tparent_id is null");
			if(pmb_mysql_num_rows($query)){
				while ($ligne = pmb_mysql_fetch_object($query)) {
					$serie = new serie($ligne->serie_id);
					$deleted = $serie->delete();
					if(!$deleted) {
						$affected++;
					}
				}
			}
			
			pmb_mysql_query("update notices left join series on tparent_id=serie_id set tparent_id=0 where serie_id is null");
			pmb_mysql_query('OPTIMIZE TABLE series');
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedCleanResponse(
				'series',
				$affected,
				$bad_user_rights
		);
	}
	
	public function cleanTitresUniformes() {
		$affected = 0;
		if (SESSrights & ADMINISTRATION_AUTH) {
			$query = pmb_mysql_query("SELECT tu_id from titres_uniformes left join notices_titres_uniformes on ntu_num_tu=tu_id where ntu_num_tu is null");
			$affected=0;
			if(pmb_mysql_num_rows($query)){
				while ($ligne = pmb_mysql_fetch_object($query)) {
					$tu = new titre_uniforme($ligne->tu_id);
					$deleted = $tu->delete();
					if(!$deleted) {
						$affected++;
					}
				}
			}
			
			//Nettoyage des informations d'autorités pour les titres uniformes
			titre_uniforme::delete_autority_sources();
			
			$query = pmb_mysql_query("delete notices_titres_uniformes from notices_titres_uniformes left join titres_uniformes on ntu_num_tu=tu_id where tu_id is null");
			$affected = pmb_mysql_affected_rows();
			
			pmb_mysql_query('OPTIMIZE TABLE titres_uniformes');
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedCleanResponse(
				'titres_uniformes',
				$affected,
				$bad_user_rights
		);
	}
	
	public function cleanIndexint() {
		$affected = 0;
		if (SESSrights & ADMINISTRATION_AUTH) {
			$query = pmb_mysql_query("SELECT indexint_id from indexint left join notices on indexint=indexint_id where notice_id is null");
			$affected=0;
			if(pmb_mysql_num_rows($query)){
				while ($ligne = pmb_mysql_fetch_object($query)) {
					$indexint = new indexint($ligne->indexint_id);
					$deleted = $indexint->delete();
					if(!$deleted) {
						$affected++;
					}
				}
			}
			pmb_mysql_query("update notices left join indexint ON indexint=indexint_id SET indexint=0 WHERE indexint_id is null");
			pmb_mysql_query('OPTIMIZE TABLE indexint');
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedCleanResponse(
				'indexint',
				$affected,
				$bad_user_rights
		);
	}
	
	public function cleanRelations() {
		global $msg, $charset, $PMBusername;
		
		$result = '';
		if (SESSrights & ADMINISTRATION_AUTH) {
			//relation 1
			$result .= "<h3>".htmlentities($msg["nettoyage_clean_relations_ban"], ENT_QUOTES, $charset)."</h3>";
			$affected = 0;
			$query = pmb_mysql_query("DELETE bannettes FROM bannettes LEFT JOIN empr ON proprio_bannette = id_empr WHERE id_empr IS NULL AND proprio_bannette !=0");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("DELETE equations FROM equations LEFT JOIN empr ON proprio_equation = id_empr WHERE id_empr IS NULL AND proprio_equation !=0 ");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("DELETE bannette_equation FROM bannette_equation LEFT JOIN bannettes ON num_bannette = id_bannette WHERE id_bannette IS NULL ");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("DELETE bannette_equation FROM bannette_equation LEFT JOIN equations on num_equation=id_equation WHERE id_equation is null");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("DELETE bannette_abon FROM bannette_abon LEFT JOIN empr on num_empr=id_empr WHERE id_empr is null");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("DELETE bannette_abon FROM bannette_abon LEFT JOIN bannettes ON num_bannette=id_bannette WHERE id_bannette IS NULL ");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete caddie_content from caddie join caddie_content on (idcaddie=caddie_id and type='NOTI') left join notices on object_id=notice_id where notice_id is null");
			$affected = pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete bannette_contenu FROM bannette_contenu left join notices on num_notice=notice_id where notice_id is null ");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete bannette_contenu FROM bannette_contenu left join bannettes on num_bannette=id_bannette where id_bannette is null ");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("DELETE avis FROM avis LEFT JOIN notices ON num_notice=notice_id WHERE type_object = 1 AND notice_id IS NULL ");
			$query = pmb_mysql_query("DELETE avis FROM avis LEFT JOIN cms_articles ON num_notice=id_article WHERE type_object = 2 AND id_article IS NULL ");
			
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_relations_ban"], ENT_QUOTES, $charset);
			pmb_mysql_query('OPTIMIZE TABLE bannette_contenu');
			
			//relation 2
			$result .= "<h3>".htmlentities($msg["nettoyage_clean_relations_cat"], ENT_QUOTES, $charset)."</h3>";
			$affected = 0;
			$query = pmb_mysql_query("delete from notices_custom_values where notices_custom_champ not in (select idchamp from notices_custom)");
			$affected = pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete from expl_custom_values where expl_custom_champ not in (select idchamp from expl_custom)");
			$affected = pmb_mysql_affected_rows();
			$query = pmb_mysql_query("DELETE empr_custom_values FROM empr_custom_values LEFT JOIN empr ON id_empr=empr_custom_origine WHERE id_empr IS NULL ");
			$affected = pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete from empr_custom_values where empr_custom_champ not in (select idchamp from empr_custom)");
			$affected = pmb_mysql_affected_rows();
			//on pose ça la pour faire comme dans le fichier relations2.inc.php
			$query = "SELECT N1.id_noeud AS id, N1.num_parent AS parent, N3.id_noeud AS top_thes
            FROM noeuds N1
            JOIN noeuds N2
            ON N1.num_parent = N2.id_noeud
            JOIN thesaurus T
            ON T.id_thesaurus = N1.num_thesaurus
            JOIN noeuds N3
            ON N3.num_thesaurus = T.id_thesaurus
            AND N3.autorite = 'TOP'
            WHERE N2.autorite = 'TOP' AND N1.autorite != 'TOP'
            AND N1.num_thesaurus != N2.num_thesaurus";
			$res = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($res)) {
				while ($row = pmb_mysql_fetch_assoc($res)) {
					if ($row["parent"] != $row["top_thes"]) {
						pmb_mysql_query("UPDATE noeuds SET num_parent = ".$row["top_thes"]." WHERE id_noeud = ".$row["id"]);
						$affected++;
					}
				}
			}
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_relations_cat"], ENT_QUOTES, $charset);
			
			//relation 3
			$result .= "<h3>".htmlentities($msg["nettoyage_clean_relations_pan"], ENT_QUOTES, $charset)."</h3>";
			
			$affected = 0;
			$query = pmb_mysql_query("DELETE notices_custom_values FROM notices_custom_values LEFT JOIN notices ON notice_id=notices_custom_origine WHERE notice_id IS NULL ");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete notices from notices left join bulletins on num_notice=notice_id where num_notice is null and niveau_hierar='2' and niveau_biblio='b' ");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete notices_titres_uniformes from notices_titres_uniformes left join notices on ntu_num_notice=notice_id where notice_id is null ");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete notices_categories from notices_categories left join notices on notcateg_notice=notice_id where notice_id is null");
			$affected = pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete responsability from responsability left join notices on responsability_notice=notice_id where notice_id is null");
			$affected = pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete responsability from responsability left join authors on responsability_author=author_id where author_id is null");
			$affected = pmb_mysql_affected_rows();
			
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_relations_pan"], ENT_QUOTES, $charset);
			pmb_mysql_query('OPTIMIZE TABLE notices_categories');
			
			//relation 4
			$result .= "<h3>".htmlentities($msg["nettoyage_clean_relations_cat2"], ENT_QUOTES, $charset)."</h3>";
			
			$affected = 0;
			$query = pmb_mysql_query("delete notices_global_index from notices_global_index left join notices on num_notice=notice_id where notice_id is null");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete notices_mots_global_index from notices_mots_global_index left join notices on id_notice=notice_id where notice_id is null");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete audit from audit left join notices on object_id=notice_id where notice_id is null and type_obj=1");
			$affected += pmb_mysql_affected_rows();
			
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_relations_cat2"], ENT_QUOTES, $charset);
			
			//relation 5
			$result .= "<h3>".htmlentities($msg["nettoyage_clean_relations_pan2"], ENT_QUOTES, $charset)."</h3>";
			
			$affected = 0;
			$query = pmb_mysql_query("delete caddie_content from caddie join caddie_content on (idcaddie=caddie_id and type='EXPL') left join exemplaires on object_id=expl_id where expl_id is null");
			$affected = pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete explnum from explnum left join notices on notice_id=explnum_notice where notice_id is null and explnum_bulletin=0");
			$affected = pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete explnum from explnum left join bulletins on bulletin_id=explnum_bulletin where bulletin_id is null and explnum_notice=0 ");
			$affected = pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete acces_res_1 from acces_res_1 left join notices on res_num=notice_id where notice_id is null ");
			if($query) $affected = pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete acces_res_2 from acces_res_2 left join notices on res_num=notice_id where notice_id is null ");
			if($query) $affected = pmb_mysql_affected_rows();
			
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_relations_pan2"], ENT_QUOTES, $charset);
			
			//relation 6
			$result .= "<h3>".htmlentities($msg["nettoyage_clean_relations_dep1"], ENT_QUOTES, $charset)."</h3>";
			
			$affected = 0;
			$query = pmb_mysql_query("delete analysis from analysis left join notices on analysis_notice=notice_id where notice_id is null");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete notices from notices left join analysis on analysis_notice=notice_id where analysis_notice is null and niveau_hierar='2' and niveau_biblio='a'");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete analysis from analysis left join bulletins on analysis_bulletin=bulletin_id where bulletin_id is null");
			$affected += pmb_mysql_affected_rows();
			$query = pmb_mysql_query("delete bulletins from bulletins left join notices on bulletin_notice=notice_id where notice_id is null");
			$affected += pmb_mysql_affected_rows();
			$affected += notice_relations::clean_lost_links();
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_relations_dep1"], ENT_QUOTES, $charset);
			pmb_mysql_query('OPTIMIZE TABLE notices');
			
			//relation 7
			$result .= "<h3>".htmlentities($msg["nettoyage_clean_relations_pan3"], ENT_QUOTES, $charset)."</h3>";
			
			$affected = 0;
			$query = pmb_mysql_query("delete caddie_content from caddie join caddie_content on (idcaddie=caddie_id and type='BULL') left join bulletins on object_id=bulletin_id where bulletin_id is null");
			$affected = pmb_mysql_affected_rows();
			
			$query = pmb_mysql_query("delete notices_langues from notices_langues left join notices on num_notice=notice_id where notice_id is null");
			$affected += pmb_mysql_affected_rows();
			
			$query = pmb_mysql_query("delete abo_liste_lecture from abo_liste_lecture left join empr on num_empr=id_empr where id_empr is null");
			$affected += pmb_mysql_affected_rows();
			
			$query = pmb_mysql_query("delete abo_liste_lecture from abo_liste_lecture left join opac_liste_lecture on num_liste=id_liste where id_liste is null");
			$affected += pmb_mysql_affected_rows();
			
			$query = pmb_mysql_query("delete opac_liste_lecture from opac_liste_lecture left join empr on num_empr=id_empr where id_empr is null");
			$affected += pmb_mysql_affected_rows();
			
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_relations_pan3"], ENT_QUOTES, $charset);
			pmb_mysql_query('OPTIMIZE TABLE caddie_content');
			
			//relation 8
			$result .= "<h3>".htmlentities($msg["nettoyage_clean_blob"], ENT_QUOTES, $charset)."</h3>";
			
			$affected = 0;
			for($i=1;$i<=2;$i++){
				if($i==1){
					$table='logopac';
				}else{
					$table='statopac';
				}
				$query = "SELECT column_type FROM information_schema.columns WHERE table_schema = '".DATA_BASE."' AND table_name = '".$table."' AND column_name = 'empr_expl'";
				$res = pmb_mysql_query($query);
				$row = pmb_mysql_fetch_object($res);
				
				if ($row->column_type == 'blob') {
					$query = pmb_mysql_query("ALTER TABLE ".$table." CHANGE empr_expl empr_expl MEDIUMBLOB NOT NULL");
					$affected += pmb_mysql_affected_rows();
				}
			}
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_blob"], ENT_QUOTES, $charset);
			
			//relation 8
			$result .= "<h3>".htmlentities($msg["nettoyage_update_relations"], ENT_QUOTES, $charset)."</h3>";
			$affected = notice_relations::upgrade_notices_relations_table();
			$result .= $affected." ".htmlentities($msg["nettoyage_res_update_relations"], ENT_QUOTES, $charset);
			
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	public function cleanNotices() {
		$affected = 0;
		if (SESSrights & ADMINISTRATION_AUTH) {
			pmb_mysql_query("set wait_timeout=3600");
			// La routine ne nettoie pour l'instant que les monographies
			pmb_mysql_query("delete notices
				FROM notices left join exemplaires on expl_notice=notice_id
					left join explnum on explnum_notice=notice_id
					left join notices_relations NRN on NRN.num_notice=notice_id
					left join notices_relations NRL on NRL.linked_notice=notice_id
				WHERE niveau_biblio='m' AND niveau_hierar='0' and explnum_notice is null and expl_notice is null and NRN.num_notice is null and NRL.linked_notice is null");
			$affected = pmb_mysql_affected_rows();
			pmb_mysql_query('OPTIMIZE TABLE notices');
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedCleanResponse(
				'notices',
				$affected,
				$bad_user_rights
		);
	}
	
	public function indexAcquisitions() {
		global $msg, $charset, $PMBusername;
		
		$result = '';
		if (SESSrights & ADMINISTRATION_AUTH) {
			//SUGGESTIONS
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_sug"], ENT_QUOTES, $charset)."</h3>";
			
			$query = pmb_mysql_query("SELECT id_suggestion, titre, editeur, auteur, code, commentaires FROM suggestions");
			if(pmb_mysql_num_rows($query)) {
				while($row = pmb_mysql_fetch_object($query)) {
					// index acte
					$req_update = "UPDATE suggestions ";
					$req_update.= "SET index_suggestion = ' ".strip_empty_words($row->titre)." ".strip_empty_words($row->editeur)." ".strip_empty_words($row->auteur)." ".$row->code." ".strip_empty_words($row->commentaires)." ' ";
					$req_update.= "WHERE id_suggestion = ".$row->id_suggestion." ";
					pmb_mysql_query($req_update);
				}
				pmb_mysql_free_result($query);
			}
			$actes = pmb_mysql_query("SELECT count(1) FROM suggestions");
			$count = pmb_mysql_result($actes, 0, 0);
			$result .= htmlentities($msg["nettoyage_reindex_sug"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_sug"], ENT_QUOTES, $charset);
			
			//ENTITES
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_ent"], ENT_QUOTES, $charset)."</h3>";
			
			$query = pmb_mysql_query("SELECT id_entite, raison_sociale FROM entites");
			if(pmb_mysql_num_rows($query)) {
				while($row = pmb_mysql_fetch_object($query)) {
					// index acte
					$req_update = "UPDATE entites ";
					$req_update.= "SET index_entite = ' ".strip_empty_words($row->raison_sociale)." ' ";
					$req_update.= "WHERE id_entite = ".$row->id_entite." ";
					pmb_mysql_query($req_update);
				}
				pmb_mysql_free_result($query);
			}
			$entites = pmb_mysql_query("SELECT count(1) FROM entites");
			$count = pmb_mysql_result($entites, 0, 0);
			$result .= htmlentities($msg["nettoyage_reindex_ent"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_ent"], ENT_QUOTES, $charset);
			
			//ACTES
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_act"], ENT_QUOTES, $charset)."</h3>";
			
			$query = pmb_mysql_query("SELECT actes.id_acte, actes.numero, entites.raison_sociale, actes.commentaires, actes.reference, actes.nom_acte FROM actes, entites where num_fournisseur=id_entite");
			if(pmb_mysql_num_rows($query)) {
				while($row = pmb_mysql_fetch_object($query)) {
					// index acte
					$req_update = "UPDATE actes ";
					$req_update.= "SET index_acte = ' ".$row->numero." ".strip_empty_words($row->raison_sociale)." ".strip_empty_words($row->commentaires)." ".strip_empty_words($row->reference)." ".strip_empty_words($row->nom_acte)." ' ";
					$req_update.= "WHERE id_acte = ".$row->id_acte." ";
					pmb_mysql_query($req_update);
					
					//index lignes_actes
					$query_2 = pmb_mysql_query("SELECT id_ligne, code, libelle FROM lignes_actes where num_acte = '".$row->id_acte."' ");
					if (pmb_mysql_num_rows($query_2)){
						while ($row_2 = pmb_mysql_fetch_object($query_2)) {
							$req_update_2 = "UPDATE lignes_actes ";
							$req_update_2.= "SET index_ligne = ' ".strip_empty_words($row_2->libelle)." ' ";
							$req_update_2.= "WHERE id_ligne = ".$row_2->id_ligne." ";
							pmb_mysql_query($req_update_2);
						}
						pmb_mysql_free_result($query_2);
					}
				}
				pmb_mysql_free_result($query);
			}
			$actes = pmb_mysql_query("SELECT count(1) FROM actes");
			$count = pmb_mysql_result($actes, 0, 0);
			$result .= htmlentities($msg["nettoyage_reindex_act"], ENT_QUOTES, $charset)." $count ".htmlentities($msg["nettoyage_res_reindex_act"], ENT_QUOTES, $charset);
			
			//FINI
			$result .= htmlentities($msg["nettoyage_reindex_acq_fini"],ENT_QUOTES,$charset);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	public function genSignatureNotice() {
		global $msg;
		
		$affected = 0;
		if (SESSrights & ADMINISTRATION_AUTH) {
			$sign= new notice_doublon();
			
			$query = pmb_mysql_query("SELECT notice_id FROM notices");
			if(pmb_mysql_num_rows($query)) {
				while ($row = pmb_mysql_fetch_row($query) )  {
					$val= $sign->gen_signature($row[0]);
					pmb_mysql_query("update notices set signature='$val', update_date=update_date where notice_id=".$row[0]);
				}
				pmb_mysql_free_result($query);
			}
			$notices = pmb_mysql_query("SELECT count(1) FROM notices");
			$affected = pmb_mysql_result($notices, 0, 0);
			pmb_mysql_query('OPTIMIZE TABLE notices');
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedResponse(
				$msg["gen_signature_notice"],
				$affected.' '.$msg["gen_signature_notice_status_end"],
				$affected,
				$bad_user_rights
		);
	}
	
	public function genSignatureDocnum() {
		global $msg;
		global $pmb_set_time_limit;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$query = pmb_mysql_query("SELECT explnum_id FROM explnum");
			if(pmb_mysql_num_rows($query)) {
				while ($row = pmb_mysql_fetch_row($query) )  {
					$explnum = new explnum($row->explnum_id);
					pmb_mysql_query("update explnum set explnum_signature='".$explnum->gen_signature()."' where explnum_id=".$row->explnum_id);
				}
				pmb_mysql_free_result($query);
			}
			$explnum = pmb_mysql_query("SELECT count(1) FROM explnum");
			$affected = pmb_mysql_result($explnum, 0, 0);
			
			$max_execution_time = intval(ini_get('max_execution_time'));
			// Don't bother if unlimited
			if (0 != $max_execution_time and $pmb_set_time_limit > $max_execution_time) {
				@set_time_limit($pmb_set_time_limit);
			}
			pmb_mysql_query('OPTIMIZE TABLE explnum');
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedResponse(
				$msg["gen_signature_docnum"],
				$affected.' '.$msg["gen_signature_docnum_status_end"],
				$affected,
				$bad_user_rights
		);
	}
	
	public function genPhonetique() {
		global $msg;
		
		$affected = 0;
		if (SESSrights & ADMINISTRATION_AUTH) {
			$res_notices = pmb_mysql_query("SELECT id_word, word FROM words");
			if($res_notices){
				$affected = pmb_mysql_num_rows($res_notices);
				if($affected){
					while($row = pmb_mysql_fetch_object($res_notices)){
						$dmeta = new DoubleMetaPhone($row->word);
						$stemming = new stemming($row->word);
						$element_to_update = "";
						if($dmeta->primary || $dmeta->secondary){
							$element_to_update.="
						double_metaphone = '".$dmeta->primary." ".$dmeta->secondary."'";
						}
						if($element_to_update) $element_to_update.=",";
						$element_to_update.="stem = '".$stemming->stem."'";
						
						if ($element_to_update){
							pmb_mysql_query("update words set ".$element_to_update." where id_word = '".$row->id_word."'");
						}
					}
				}
			}
			pmb_mysql_query('OPTIMIZE TABLE words');
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedResponse(
				$msg["gen_phonetique"],
				$affected.' '.$msg["gen_phonetique_end"],
				$affected,
				$bad_user_rights
		);
	}
	
	public function genAutLink() {
		global $msg, $charset, $PMBusername;
		
		$result = '';
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["gen_aut_link_title"], ENT_QUOTES, $charset)."</h3>";
			
			$error = '';
			
			if (pmb_mysql_num_rows(pmb_mysql_query("show columns from aut_link like 'id_aut_link'")) == 0) {
				
				$relations = new marc_select("aut_link");
				$links = $relations->table['descendant'];
				
				$query = "SELECT * FROM aut_link";
				$result_sql = pmb_mysql_query($query);
				
				$error_codes = array();
				while ($row = pmb_mysql_fetch_object($result_sql)) {
					if (!array_key_exists($row->aut_link_type, $links)) {
						$error_codes[] = $row->aut_link_type;
					}
				}
				
				if (count($error_codes)) {
					$error_codes = array_unique($error_codes);
					asort($error_codes);
					$error = "<div class='erreur'>" . htmlentities($msg['gen_net_base_aut_link_error'] . implode(', ', $error_codes), ENT_QUOTES, $charset) . "</div>";
				}
				
				if (!$error) {
					$query = "RENAME TABLE aut_link TO aut_link_old";
					pmb_mysql_query($query);
					
					$query = "CREATE TABLE aut_link(
			            id_aut_link INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			    		aut_link_from INT(2) NOT NULL DEFAULT 0 ,
			    		aut_link_from_num INT(11) NOT NULL DEFAULT 0 ,
			    		aut_link_to INT(2) NOT NULL DEFAULT 0 ,
			    		aut_link_to_num INT(11) NOT NULL DEFAULT 0 ,
			    		aut_link_type VARCHAR(10) NOT NULL DEFAULT '',
			    		aut_link_comment VARCHAR(255) NOT NULL DEFAULT '',
			            aut_link_string_start_date VARCHAR(255) NOT NULL DEFAULT '',
			            aut_link_string_end_date VARCHAR(255) NOT NULL DEFAULT '',
			            aut_link_start_date DATE NOT NULL DEFAULT '0000-00-00',
			            aut_link_end_date DATE NOT NULL DEFAULT '0000-00-00',
			    		aut_link_rank INT(11) NOT NULL DEFAULT 0 ,
			    		aut_link_direction VARCHAR(4) NOT NULL DEFAULT '',
			    		aut_link_reverse_link_num INT(11) NOT NULL DEFAULT 0 ,
							
			            INDEX i_from(aut_link_from,aut_link_from_num),
			            INDEX i_to (aut_link_to,aut_link_to_num),
			    		KEY(aut_link_from, aut_link_from_num, aut_link_to, aut_link_to_num, aut_link_type))
			        ";
					
					$result_sql = pmb_mysql_query($query);
					
					$query = "SELECT * FROM aut_link_old";
					$result_sql = pmb_mysql_query($query);
					while ($row = pmb_mysql_fetch_object($result_sql)) {
						$query = "INSERT INTO aut_link SET
			                aut_link_from=" . $row->aut_link_from . ",
			                aut_link_from_num=" . $row->aut_link_from_num . ",
			                aut_link_to=" . $row->aut_link_to . ",
			                aut_link_to_num=" . $row->aut_link_to_num . ",
			                aut_link_type='" . addslashes($row->aut_link_type) . "',
			                aut_link_comment='" . addslashes($row->aut_link_comment) . "',
			                aut_link_string_start_date='" . addslashes($row->aut_link_string_start_date) . "',
			                aut_link_string_end_date='" . addslashes($row->aut_link_string_end_date) . "',
			                aut_link_start_date='" . $row->aut_link_start_date . "',
			                aut_link_end_date='" . $row->aut_link_end_date . "',
			                aut_link_direction='down'
			            ";
						pmb_mysql_query($query);
						$id_aut_link = pmb_mysql_insert_id();
						if ($row->aut_link_reciproc) {
							$query = "INSERT INTO aut_link SET
			                    aut_link_from=" . $row->aut_link_to . ",
			                    aut_link_from_num=" . $row->aut_link_to_num . ",
			                    aut_link_to=" . $row->aut_link_from . ",
			                    aut_link_to_num=" . $row->aut_link_from_num . ",
			                    aut_link_type='" . addslashes('i' . $row->aut_link_type) . "',
			                    aut_link_comment='" . addslashes($row->aut_link_comment) . "',
			                    aut_link_string_start_date='" . addslashes($row->aut_link_string_start_date) . "',
			                    aut_link_string_end_date='" . addslashes($row->aut_link_string_end_date) . "',
			                    aut_link_start_date='" . $row->aut_link_start_date . "',
			                    aut_link_end_date='" . $row->aut_link_end_date . "',
			                    aut_link_direction='up',
			                    aut_link_reverse_link_num = " . $id_aut_link . "
			                ";
							pmb_mysql_query($query);
							$id_aut_link_other = pmb_mysql_insert_id();
							
							$query = "UPDATE aut_link SET
			                    aut_link_reverse_link_num = " . $id_aut_link_other . "
			                    WHERE id_aut_link=" . $id_aut_link;
							pmb_mysql_query($query);
						}
					}
					//$query = "DROP TABLE aut_link_old";
					//$result_sql = pmb_mysql_query($query);
				}
			}
			$result .= htmlentities($msg["gen_aut_link_title"], ENT_QUOTES, $charset).$error;
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		
		return $result;
	}
	
	public function nettoyageCleanTags() {
		global $msg;
		
		$affected = 0;
		if (SESSrights & ADMINISTRATION_AUTH) {
			$query = pmb_mysql_query("SELECT notice_id FROM notices");
			if(pmb_mysql_num_rows($query)) {
				while ($row = pmb_mysql_fetch_row($query) )  {
					notice::majNotices_clean_tags($row[0]);
				}
				pmb_mysql_free_result($query);
			}
			$notices = pmb_mysql_query("SELECT count(1) FROM notices");
			$affected = pmb_mysql_result($notices, 0, 0);
			pmb_mysql_query('OPTIMIZE TABLE notices');
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedResponse(
				$msg["nettoyage_clean_tags"],
				$affected.' '.$msg["nettoyage_clean_tags_status_end"],
				$affected,
				$bad_user_rights
		);
	}
	
	public function cleanCategoriesPath() {
		global $msg;
		global $thesaurus_auto_postage_search;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			// Pour tous les thésaurus, on parcours les childs
			$list_thesaurus = thesaurus::getThesaurusList();
			
			foreach($list_thesaurus as $id_thesaurus=>$libelle_thesaurus) {
				$thes = new thesaurus($id_thesaurus);
				$noeud_rac =  $thes->num_noeud_racine;
				$r = noeuds::listChilds($noeud_rac, 0);
				while(($row = pmb_mysql_fetch_object($r))){
					noeuds::process_categ_path($row->id_noeud);
				}
			}
			if($thesaurus_auto_postage_search){
				categories::process_categ_index();
			}
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedResponse(
				'',
				$msg["clean_categories_path_end"],
				0,
				$bad_user_rights
		);
	}
	
	public function genDatePublicationArticle() {
		global $msg;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$req="select date_date,analysis_notice from analysis,bulletins where analysis_bulletin=bulletin_id";
			$res=pmb_mysql_query($req);
			if(pmb_mysql_num_rows($res))while (($row = pmb_mysql_fetch_object($res))) {
				$year=substr($row->date_date,0,4);
				if($year) {
					$req="UPDATE notices SET year='$year', update_date=update_date where notice_id=".$row->analysis_notice;
					pmb_mysql_query($req);
				}
			}
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedResponse(
				$msg["gen_date_publication_article"],
				$msg["gen_date_publication_article_end"],
				0,
				$bad_user_rights
		);
	}
	
	public function genDateTri() {
		global $msg;
		
		$affected = 0;
		if (SESSrights & ADMINISTRATION_AUTH) {
			$query = pmb_mysql_query("select notice_id, year, niveau_biblio, niveau_hierar from notices order by notice_id");
			if(pmb_mysql_num_rows($query)) {
				while($mesNotices = pmb_mysql_fetch_assoc($query)) {
					switch($mesNotices['niveau_biblio'].$mesNotices['niveau_hierar']){
						case 'a2':
							//Si c'est un article, on récupère la date du bulletin associé
							$reqAnneeArticle = "SELECT date_date FROM bulletins, analysis WHERE analysis_bulletin=bulletin_id AND analysis_notice='".$mesNotices['notice_id']."'";
							$queryArt=pmb_mysql_query($reqAnneeArticle);
							
							if(!pmb_mysql_num_rows($queryArt)) $dateArt = "";
							else $dateArt=pmb_mysql_result($queryArt,0,0);
							
							if($dateArt == '0000-00-00' || !isset($dateArt) || $dateArt == "") $annee_art_tmp = "";
							else $annee_art_tmp = substr($dateArt,0,4);
							
							//On met à jour, les notices avec la date de parution et l'année
							$reqMajArt = "UPDATE notices SET date_parution='".$dateArt."', year='".$annee_art_tmp."', update_date=update_date
										WHERE notice_id='".$mesNotices['notice_id']."'";
							pmb_mysql_query($reqMajArt);
							break;
							
						case 'b2':
							//Si c'est une notice de bulletin, on récupère la date pour connaitre l'année
							$reqAnneeBulletin = "SELECT date_date FROM bulletins WHERE num_notice='".$mesNotices['notice_id']."'";
							$queryAnnee=pmb_mysql_query($reqAnneeBulletin);
							
							if(!pmb_mysql_num_rows($queryAnnee)) $dateBulletin="";
							else $dateBulletin = pmb_mysql_result($queryAnnee,0,0);
							
							if($dateBulletin == '0000-00-00' || !isset($dateBulletin) || $dateBulletin == "") $annee_tmp = "";
							else $annee_tmp = substr($dateBulletin,0,4);
							
							//On met à jour date de parution et année
							$reqMajBull = "UPDATE notices SET date_parution='".$dateBulletin."', year='".$annee_tmp."', update_date=update_date
									WHERE notice_id='".$mesNotices['notice_id']."'";
							pmb_mysql_query($reqMajBull);
							
							break;
							
						default:
							// Mise à jour du champ date_parution des notices (monographie et pério)
							$date_parution = notice::get_date_parution($mesNotices['year']);
							$reqMaj = "UPDATE notices SET date_parution='".$date_parution."', update_date=update_date WHERE notice_id='".$mesNotices['notice_id']."'";
							pmb_mysql_query($reqMaj);
							break;
					}
				}
				pmb_mysql_free_result($query);
			}
			$not = pmb_mysql_query("SELECT count(1) FROM notices");
			$affected = pmb_mysql_result($not, 0, 0);
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedResponse(
				$msg["gen_date_tri_msg"],
				$affected.' '.$msg["gen_date_tri_msg"],
				$affected,
				$bad_user_rights
				);
	}
	
	public function indexDocnum() {
		global $msg, $dbh;
		
		$affected = 0;
		if (SESSrights & ADMINISTRATION_AUTH) {
			pmb_mysql_query("set wait_timeout=3600");
			$requete = "select explnum_id as id from explnum order by id";
			$res_explnum = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res_explnum)) {
				while(($explnum = pmb_mysql_fetch_object($res_explnum))){
					pmb_mysql_close($dbh);
					$dbh = connection_mysql();
					$index = new indexation_docnum($explnum->id);
					$index->indexer();
				}
			}
			$explnum = pmb_mysql_query("SELECT count(1) FROM explnum");
			$affected = pmb_mysql_result($explnum, 0, 0);
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedResponse(
				$msg["docnum_reindexation"],
				$affected.' '.$msg["docnum_reindex_expl"],
				$affected,
				$bad_user_rights
		);
	}
	
	public function cleanRdfStore() {
		global $msg;
		global $class_path;
		
		$affected = 0;
		if (SESSrights & ADMINISTRATION_AUTH) {
			//remise a zero de la table au début
			pmb_mysql_query("TRUNCATE rdfstore_index");
			
			$query = "select t.t as num_triple, s.val as subject_uri, p.val as predicat_uri, o.id as num_object, o.val as object_val, l.val as object_lang
				from rdfstore_triple t, rdfstore_s2val s, rdfstore_id2val p, rdfstore_o2val o, rdfstore_id2val l
				where t.o_type=2 and t.o_lang_dt=l.id and length(l.val)<3 and t.s=s.id and t.p=p.id and t.o=o.id
				order by t.t";
			$rdfStore = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($rdfStore)) {
				$op = new ontology_parser("$class_path/rdf/skos_pmb.rdf");
				$sh = new skos_handler($op);
				while(($triple = pmb_mysql_fetch_object($rdfStore))){
					$type=$sh->op->from_ns($sh->get_object_type($triple->subject_uri));
					$q_ins = "insert ignore into rdfstore_index ";
					$q_ins.= "set num_triple='".$triple->num_triple."', ";
					$q_ins.= "subject_uri='".addslashes($triple->subject_uri)."', ";
					$q_ins.= "subject_type='".addslashes($type)."', ";
					$q_ins.= "predicat_uri='".addslashes($triple->predicat_uri)."', ";
					$q_ins.= "num_object='".$triple->num_object."', ";
					$q_ins.= "object_val ='".addslashes($triple->object_val)."', ";
					$q_ins.= "object_index=' ".strip_empty_chars($triple->object_val)." ', ";
					$q_ins.= "object_lang ='".addslashes($triple->object_lang)."' ";
					
					pmb_mysql_query($q_ins);
				}
			}
			$rdfStore = pmb_mysql_query("select count(1) from rdfstore_triple where o_type=2");
			$affected = pmb_mysql_result($rdfStore, 0, 0);
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedResponse(
				$msg["nettoyage_rdfstore_reindexation"],
				$affected.' '.$msg["nettoyage_rdfstore_reindex_elt"],
				$affected,
				$bad_user_rights
		);
	}
	
	public function cleanSynchroRdfStore() {
		global $msg;
		
		$affected = 0;
		if (SESSrights & ADMINISTRATION_AUTH) {
			$synchro_rdf = new synchro_rdf();
			//remise a zero des tables de synchro rdf
			$synchro_rdf->truncateStore();
			//reindex
			$query = pmb_mysql_query("select notice_id from notices order by notice_id");
			if(pmb_mysql_num_rows($query)) {
				while($mesNotices = pmb_mysql_fetch_assoc($query)) {
					$synchro_rdf->addRdf($mesNotices['notice_id'],0);
					$notice=new notice($mesNotices['notice_id']);
					$niveauB=strtolower($notice->biblio_level);
					//Si c'est un article, il faut réindexer son bulletin
					if($niveauB=='a'){
						$bulletin=analysis::getBulletinIdFromAnalysisId($mesNotices['notice_id']);
						$synchro_rdf->addRdf(0,$bulletin);
					}
				}
				pmb_mysql_free_result($query);
			}
			$q ="SELECT *
			WHERE {
			   FILTER (!regex(?p, rdf:type,'i')) .
			   ?s ?p ?o
			}";
			$r = $synchro_rdf->store->query($q);
			if (is_array($r['result']['rows'])) {
				$affected=count($r['result']['rows']);
			}
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
			
		}
		return $this->getFormattedResponse(
				$msg["nettoyage_synchrordfstore_reindexation"],
				$affected.' '.$msg["nettoyage_synchrordfstore_reindex_total"],
				$affected,
				$bad_user_rights
		);
	}
	
	public function cleanFAQ() {
		global $include_path;
		
		$affected = 0;
		if (SESSrights & ADMINISTRATION_AUTH) {
			//remise a zero de la table au début
			pmb_mysql_query("TRUNCATE faq_questions_words_global_index");
			pmb_mysql_query("ALTER TABLE faq_questions_words_global_index DISABLE KEYS");
			pmb_mysql_query("TRUNCATE faq_questions_fields_global_index");
			pmb_mysql_query("ALTER TABLE faq_questions_fields_global_index DISABLE KEYS");
			
			$query = "select id_faq_question from faq_questions order by id_faq_question";
			$faq_questions = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($faq_questions)) {
				$indexation = new indexation($include_path."/indexation/faq/question.xml", "faq_questions");
				$indexation->set_deleted_index(true);
				while($row = pmb_mysql_fetch_object($faq_questions)) {
					$indexation->maj($row->id_faq_question);
				}
			}
			pmb_mysql_query("ALTER TABLE faq_questions_words_global_index ENABLE KEYS");
			pmb_mysql_query("ALTER TABLE faq_questions_fields_global_index ENABLE KEYS");
			$faq = pmb_mysql_query("SELECT count(1) FROM faq_questions");
			$affected = pmb_mysql_result($faq, 0, 0);
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedIndexResponse(
				'faq',
				$affected,
				$bad_user_rights
		);
	}
	
	public function cleanCMS() {
		global $msg;
		
		$affected = 0;
		if (SESSrights & ADMINISTRATION_AUTH) {
			//remise a zero de la table au début
			pmb_mysql_query("TRUNCATE cms_editorial_words_global_index");
			pmb_mysql_query("ALTER TABLE cms_editorial_words_global_index DISABLE KEYS");
			pmb_mysql_query("TRUNCATE cms_editorial_fields_global_index");
			pmb_mysql_query("ALTER TABLE cms_editorial_fields_global_index DISABLE KEYS");
			
			$query = "select id_article from cms_articles order by id_article";
			$articles = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($articles)) {
				while($row = pmb_mysql_fetch_object($articles)) {
					$article = new cms_article($row->id_article);
					$article->maj_indexation();
				}
			}
			global $champ_base;
			$champ_base = false;
			$query = "select id_section from cms_sections order by id_section";
			$sections = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($sections)) {
				while($row = pmb_mysql_fetch_object($sections)) {
					$section = new cms_section($row->id_section);
					$section->maj_indexation();
				}
			}
			pmb_mysql_query("ALTER TABLE cms_editorial_words_global_index ENABLE KEYS");
			pmb_mysql_query("ALTER TABLE cms_editorial_fields_global_index ENABLE KEYS");
			$articles = pmb_mysql_query("SELECT count(1) FROM cms_articles");
			$affected = pmb_mysql_result($articles, 0, 0);
			$sections = pmb_mysql_query("SELECT count(1) FROM cms_sections");
			$affected += pmb_mysql_result($sections, 0, 0);
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedResponse(
				$msg["nettoyage_reindex_cms"],
				$affected.' '.$msg["nettoyage_res_reindex_cms"],
				$affected,
				$bad_user_rights
		);
	}
	
	public function cleanConcept() {
		global $msg;
		global $pmb_clean_mode;
		
		// Indexation par champ activée ? (sera activée par défaut par la suite))
		if(!empty($pmb_clean_mode)) {
		    static::$indexation_by_fields = true;
		    netbase_concepts::set_indexation_by_fields(true);
		}
		$affected = 0;
		if (SESSrights & ADMINISTRATION_AUTH) {
			//remise a zero de la table au début
		    netbase_concepts::raz_index();
			
		    netbase_concepts::index();
		    
		    netbase_concepts::enable_index();
			
			$concepts = pmb_mysql_query("SELECT count(distinct id_item) FROM skos_words_global_index");
			$affected = pmb_mysql_result($concepts, 0, 0);
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedResponse(
				$msg["nettoyage_reindex_concept"],
				$affected." ".$msg['nettoyage_res_reindex_concept'],
				$affected,
				$bad_user_rights
		);
	}
	
	public function indexSphinxConcepts() {
	    global $dbh, $msg, $sphinx_active;
	    
	    if (!$sphinx_active) {
	        return array();
	    }
	    $affected = 0;
	    if (SESSrights & ADMINISTRATION_AUTH) {
	        $index_class = 'sphinx_concepts_indexer';
	        if(class_exists($index_class)){
	            $sconf = new $index_class();
	            $sconf->checkExistingIndexes();
	            $sconf->fillIndexes([]);
	            
	            $concepts = pmb_mysql_query("SELECT count(distinct id_item) FROM skos_words_global_index");
	            $affected = pmb_mysql_result($concepts, 0, 0);
	        }
	        $bad_user_rights = 0;
	        
	        //Re-ouverture d'une connexion MySQL en cas d'operation longue sur Sphinx
	        pmb_mysql_close($dbh);
	        $dbh = connection_mysql();
	    } else {
	        $bad_user_rights = 1;
	    }
	    return $this->getFormattedResponse(
	        "[Sphinx]". $msg["nettoyage_reindex_concept"],
	        $affected." ".$msg['nettoyage_res_reindex_concept'],
	        $affected,
	        $bad_user_rights
	        );
	}
	
	public function hashEmprPassword() {
		global $msg;
		
		$message = '';
		$affected = 0;
		if (SESSrights & ADMINISTRATION_AUTH) {
			$rqt = "SHOW COLUMNS FROM empr LIKE 'empr_password_is_encrypted'";
			$res = pmb_mysql_query($rqt);
			if(pmb_mysql_num_rows($res)) {
				$empr = pmb_mysql_query("SELECT count(1) FROM empr where empr_password_is_encrypted=0");
				$affected = pmb_mysql_result($empr, 0, 0);
				
				$query = pmb_mysql_query("SELECT id_empr, empr_password, empr_login FROM empr where empr_password_is_encrypted=0");
				if(pmb_mysql_num_rows($query)) {
					$requete = "CREATE TABLE if not exists empr_passwords (
					id_empr INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
					empr_password VARCHAR( 255 ) NOT NULL default '')";
					pmb_mysql_query($requete);
					$requete = "INSERT INTO empr_passwords SELECT id_empr, empr_password FROM empr where empr_password_is_encrypted=0";
					pmb_mysql_query($requete);
					
					while ($row = pmb_mysql_fetch_object($query) )  {
						emprunteur::update_digest($row->empr_login,$row->empr_password);
						emprunteur::hash_password($row->empr_login,$row->empr_password);
					}
				}
				$message .= $affected." ".$msg['hash_empr_password_status_end'];
			} else {
				$message .= $msg['pmb_v_db_pas_a_jour'];
			}
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedResponse(
				$msg["hash_empr_password"],
				$message,
				$affected,
				$bad_user_rights
		);
	}
	
	public static function getMethodsindexAuthorities() {
		return array(
				AUT_TABLE_AUTHORS => 'indexAuthors',			//AUTHORS
				AUT_TABLE_PUBLISHERS => 'indexPublishers',		//PUBLISHERS
                AUT_TABLE_CATEG => 'indexCategories',		//CATEGORIES
                AUT_TABLE_COLLECTIONS => 'indexCollections',		//COLLECTIONS
				AUT_TABLE_SUB_COLLECTIONS => 'indexSubCollections',	//SUBCOLLECTIONS
				AUT_TABLE_SERIES => 'indexSeries',			//SERIES
                AUT_TABLE_INDEXINT => 'indexIndexint',		//DEWEY
				AUT_TABLE_TITRES_UNIFORMES => 'indexTitresUniformes'	//TITRES_UNIFORMES
		);
	}
	
	public function indexAuthorities($filters = array(), $step_name='', $fields = []) {
		global $msg, $include_path;
		global $pmb_clean_mode;
		
		// Indexation par champ activée ? (sera activée par défaut par la suite))
		if(!empty($pmb_clean_mode)) {
		    static::$indexation_by_fields = true;
		}
		$this->index_step_name = $step_name;
		$this->index_fields = $fields;
		$result = array();
		if (SESSrights & ADMINISTRATION_AUTH) {
			//modifions les limites lorsque l'on indexe tout d'un coup
			if(static::$packet_size == 0) {
				pmb_mysql_query("set wait_timeout=3600");
				ini_set("memory_limit","-1");
			} else {
				$max_execution_time = intval(ini_get('max_execution_time'));
				if(!$max_execution_time) {
					@set_time_limit(600); // on limite à 600 secondes
				}
				pmb_mysql_query("set wait_timeout=300");
			}
			if(!static::$progression) {
				//remise a zero de la table au début
				netbase_authorities::raz_index();
			}
			
		    $number_indexed = 0;
		    $authorities_method = static::getMethodsindexAuthorities();
		    $number_authorities_method = count($authorities_method);
		    $progression_by_step = (100/$number_authorities_method);
		    if(!empty($filters)) {
		        $number_indexed = ($number_authorities_method - count($filters));
		    }
		    foreach ($authorities_method as $authority_method) {
		        if(empty($filters) || in_array($authority_method, $filters)) {
		            $result[$authority_method] = $this->{$authority_method}();
		            if(empty($this->index_step_name) && empty($this->index_fields)) {
    		            if(static::canGoNextStep()) {
    		                static::initProgresion();
    		                $number_indexed++;
    		            } else {
    		                //réajustement de la progression en fonction des précédentes étapes
    		                $progression_current_step = (static::$progression/100)*$progression_by_step;
    		                static::$progression = round(($progression_by_step*$number_indexed)+$progression_current_step, 2);
    		                break; //on sort de la boucle
    		            }
		            }
		        }
		    }
		    
		    //AUTORITES PERSO
		    if(empty($filters) || in_array('indexAuthperso', $filters)) {
		        $result['indexAuthperso'] = $this->indexAuthperso();
		        if(empty($this->index_step_name) && empty($this->index_fields)) {
		            if(static::canGoNextStep()) {
		                static::initProgresion();
		            } else {
		                //réajustement de la progression en fonction des précédentes étapes
		                static::$progression = 100;
		            }
		        }
		    }
			
		    //Indexation autorite par autorite - on passe la progression a 100 % a la fin
		    if(empty($filters) && empty($this->index_step_name) && empty($this->index_fields)) {
		        static::$progression = 100;
		    }
		    
			if(round(static::$progression) == 100) {
				netbase_authorities::enable_index();
			}
		} else {
			$result = $this->getFormattedResponse($msg['nettoyage_index_authorities'],	'',	0, 1);
		}
		return $result;
	}
	
	public function indexSphinxAuthorities($filters = array()) {
	    global $dbh, $msg, $sphinx_active;
	    
	    if (!$sphinx_active) {
            return array();
	    }
	    $result = array();
	    if (SESSrights & ADMINISTRATION_AUTH) {
	        $number_indexed = 0;
	        $entities = array(
	            TYPE_AUTHOR => 'authors',
	            TYPE_PUBLISHER => 'publishers',
	            TYPE_CATEGORY => 'categories',
	            TYPE_COLLECTION => 'collections',
	            TYPE_SUBCOLLECTION => 'subcollections',
	            TYPE_SERIE => 'series',
	            TYPE_INDEXINT => 'indexint',
	            TYPE_TITRE_UNIFORME => 'titres_uniformes',
	            TYPE_AUTHPERSO => 'authperso'
	        );
	        $number_entities = count($entities);
	        $progression_by_step = (100/$number_entities);
	        if(!empty($filters)) {
	            $number_indexed = ($number_entities - count($filters));
	        }
	        foreach ($entities as $entity_type=>$entity) {
	            if(empty($filters) || in_array($entity, $filters)) {
	                $indexed = 0;
	                $index_class = 'sphinx_' . $entity . '_indexer';
	                if(class_exists($index_class)){
	                    $sconf = new $index_class();
	                    $sconf->checkExistingIndexes();
	                    $sconf->fillIndexes([]);
	                    $indexed = pmb_mysql_result(pmb_mysql_query($this->getQueryEntities($entity_type)), 0);
	                }
	                $result[$entity] = $this->getFormattedSphinxIndexResponse($entity, $indexed, 0);
	                //réajustement de la progression en fonction des précédentes étapes
	                static::$progression = round(($progression_by_step*$number_indexed), 2);
	            }
	        }
	        //Re-ouverture d'une connexion MySQL en cas d'operation longue sur Sphinx
	        pmb_mysql_close($dbh);
	        $dbh = connection_mysql();
	    } else {
	        $result = $this->getFormattedResponse("[Sphinx] ".$msg['nettoyage_index_authorities'],	'',	0, 1);
	    }
	    return $result;
	}
	    
	protected function getQueryEntities($entity_type) {
		$query = '';
		switch ($entity_type) {
			case TYPE_NOTICE:
				$query = "SELECT notice_id as id FROM notices ORDER BY id";
				break;
			case TYPE_AUTHOR:
				$query = "SELECT author_id as id FROM authors ORDER BY id";
				break;
			case TYPE_PUBLISHER:
				$query = "SELECT ed_id as id FROM publishers ORDER BY id";
				break;
			case TYPE_CATEGORY:
				$query = "SELECT distinct num_noeud as id FROM categories ORDER BY id";
				break;
			case TYPE_COLLECTION:
				$query = "SELECT collection_id as id FROM collections ORDER BY id";
				break;
			case TYPE_SUBCOLLECTION:
				$query = "SELECT sub_coll_id as id FROM sub_collections ORDER BY id";
				break;
			case TYPE_SERIE:
				$query = "SELECT serie_id as id FROM series ORDER BY id";
				break;
			case TYPE_INDEXINT:
				$query = "SELECT indexint_id as id FROM indexint ORDER BY id";
				break;
			case TYPE_TITRE_UNIFORME:
				$query = "SELECT tu_id as id FROM titres_uniformes ORDER BY id";
				break;
			case TYPE_AUTHPERSO:
			    $query = "SELECT id_authperso_authority as id FROM authperso_authorities ORDER BY id";
		        break;
		}
		return $query;
	}
	
	protected function getQueryCleanEntitiesAuthorities($entity_type) {
		switch ($entity_type) {
			case TYPE_AUTHOR:
				return "SELECT id_authority FROM authorities LEFT JOIN authors ON num_object=author_id WHERE type_object='".AUT_TABLE_AUTHORS."' AND author_id IS NULL";
			case TYPE_PUBLISHER:
				return "SELECT id_authority FROM authorities LEFT JOIN publishers ON num_object=ed_id WHERE type_object='".AUT_TABLE_PUBLISHERS."' AND ed_id IS NULL";
			case TYPE_CATEGORY:
				return "SELECT id_authority FROM authorities LEFT JOIN categories ON num_object=num_noeud WHERE type_object='".AUT_TABLE_CATEG."' AND num_noeud IS NULL";
			case TYPE_COLLECTION:
				return "SELECT id_authority FROM authorities LEFT JOIN collections ON num_object=collection_id WHERE type_object='".AUT_TABLE_COLLECTIONS."' AND collection_id IS NULL";
			case TYPE_SUBCOLLECTION:
				return "SELECT id_authority FROM authorities LEFT JOIN sub_collections ON num_object=sub_coll_id WHERE type_object='".AUT_TABLE_SUB_COLLECTIONS."' AND sub_coll_id IS NULL";
			case TYPE_SERIE:
				return "SELECT id_authority FROM authorities LEFT JOIN series ON num_object=serie_id WHERE type_object='".AUT_TABLE_SERIES."' AND serie_id IS NULL";
			case TYPE_INDEXINT:
				return "SELECT id_authority FROM authorities LEFT JOIN indexint ON num_object=indexint_id WHERE type_object='".AUT_TABLE_INDEXINT."' AND indexint_id IS NULL";
			case TYPE_TITRE_UNIFORME:
				return "SELECT id_authority FROM authorities LEFT JOIN titres_uniformes ON num_object=tu_id WHERE type_object='".AUT_TABLE_TITRES_UNIFORMES."' AND tu_id IS NULL";
			case TYPE_AUTHPERSO:
			    return "SELECT id_authority FROM authorities LEFT JOIN authperso_authorities ON num_object=id_authperso_authority WHERE type_object ='".AUT_TABLE_AUTHPERSO."' AND id_authperso_authority IS NULL";
		}
	}
	
	protected function cleanEntitiesAuthorities($entity_type) {
		$query = $this->getQueryCleanEntitiesAuthorities($entity_type);
		if($query) {
			$res = pmb_mysql_query($query);
			if($res && pmb_mysql_num_rows($res)){
				while($aut = pmb_mysql_fetch_row($res)){
					$authority = new authority($aut[0]);
					$authority->delete();
				}
			}
		}
	}
	
	protected static function getSuffixFromType($entity_type){
		switch ($entity_type) {
			case TYPE_NOTICE :
				return 'notices';
			case TYPE_AUTHOR :
				return 'authors';
			case TYPE_CATEGORY :
				return 'categories';
			case TYPE_PUBLISHER :
				return 'publishers';
			case TYPE_COLLECTION :
				return 'collections';
			case TYPE_SUBCOLLECTION :
				return 'sub_collections';
			case TYPE_SERIE :
				return 'series';
			case TYPE_INDEXINT :
				return 'indexint';
			case TYPE_TITRE_UNIFORME :
				return 'titres_uniformes';
			default :
				return '';
		}
	}
	
	protected function indexEntitiesAuthorities($entity_type) {
		if (SESSrights & ADMINISTRATION_AUTH) {
			//On controle qu'il n'y a pas d'autorité à enlever
			$this->cleanEntitiesAuthorities($entity_type);
			
			//Indexation par paquet : on ajoute toutes les notices dans la pile d'indexation
			if(!static::$progression && static::$packet_size) {
				$query = $this->getQueryEntities($entity_type);
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)) {
					while($myAuthorities = pmb_mysql_fetch_assoc($result)) {
						scheduler_indexation_stack::push($myAuthorities['id'], $entity_type);
					}
					scheduler_indexation_stack::push_database();
				}
			}
			$indexed = $this->indexEntities($entity_type);
			$bad_user_rights = 0;
		} else {
			$indexed = 0;
			$bad_user_rights = 1;
		}
		return $this->getFormattedIndexResponse(
				static::getSuffixFromType($entity_type),
				$indexed,
				$bad_user_rights
		);
	}
	
	public function indexAuthors() {
		return $this->indexEntitiesAuthorities(TYPE_AUTHOR);
	}
	
	public function indexPublishers() {
		return $this->indexEntitiesAuthorities(TYPE_PUBLISHER);
	}
	
	public function indexCategories() {
		return $this->indexEntitiesAuthorities(TYPE_CATEGORY);
	}
	
	public function indexCollections() {
		return $this->indexEntitiesAuthorities(TYPE_COLLECTION);
	}
	
	public function indexSubCollections() {
		return $this->indexEntitiesAuthorities(TYPE_SUBCOLLECTION);
	}
	
	public function indexSeries() {
		return $this->indexEntitiesAuthorities(TYPE_SERIE);
	}
	
	public function indexIndexint() {
		return $this->indexEntitiesAuthorities(TYPE_INDEXINT);
	}
	
	public function indexTitresUniformes() {
		return $this->indexEntitiesAuthorities(TYPE_TITRE_UNIFORME);
	}
	
	public function indexAuthperso($id=0) {
		global $include_path;
		
		$id = intval($id);
		$indexed = 0;
		if (SESSrights & ADMINISTRATION_AUTH) {
			//On controle qu'il n'y a pas d'autorité à enlever
		    $this->cleanEntitiesAuthorities(TYPE_AUTHPERSO);
			
			if(static::$indexation_by_fields) {
			    $query = "SELECT id_authperso FROM authperso";
			    if ($id) {
			        $query .= " WHERE id_authperso = ".$id;
			    }
			    $result = pmb_mysql_query($query);
			    while ($row = pmb_mysql_fetch_object($result)) {
			        netbase_authperso::set_id_authperso($row->id_authperso);
			        netbase_authperso::index_steps_fields();
			    }
			    $query = "SELECT id_authperso_authority as id FROM authperso_authorities";
			    if ($id) {
			        $query .= " WHERE authperso_authority_authperso_num = ".$id;
			    }
			    $result = pmb_mysql_query($query);
			    $indexed = pmb_mysql_num_rows($result);
			} else {
			    $query = "SELECT id_authperso_authority as id, authperso_authority_authperso_num from authperso_authorities";
			    if ($id) {
			        $query .= " WHERE authperso_authority_authperso_num = ".$id;
			    }
			    $result = pmb_mysql_query($query);
			    $count = pmb_mysql_num_rows($result);
			    if ($count) {
			        $id_authperso = 0;
			        while($row = pmb_mysql_fetch_object($result)) {
			            if(!$id_authperso || ($id_authperso != $row->authperso_authority_authperso_num)) {
			                $indexation_authperso = new indexation_authperso($include_path."/indexation/authorities/authperso/champs_base.xml", "authorities", (1000+$row->authperso_authority_authperso_num), $row->authperso_authority_authperso_num);
			                $indexation_authperso->set_deleted_index(true);
			                $id_authperso = $row->authperso_authority_authperso_num;
			            }
			            $indexation_authperso->maj($row->id);
			            $indexed++;
			        }
			        pmb_mysql_free_result($result);
			    }
			}
			$bad_user_rights = 0;
		} else {
			$bad_user_rights = 1;
		}
		return $this->getFormattedIndexResponse(
				'authperso',
				$indexed,
				$bad_user_rights
		);
	}
	
	public function genArk() {
		global $msg, $include_path;
		
		$affected = 0;
		if (ArkModel::getNbEntitiesWithoutArk()) {
			$affected = ArkModel::getNbEntitiesWithoutArk();
			$limit = 100;
			while (ArkModel::getNbEntitiesWithoutArk()) {
				ArkModel::generateMassArk($limit);
			}
		}
		return $this->getFormattedResponse(
				$msg["ark_manage_generate"],
				$msg["ark_manage_generate"]." $affected ".$msg["pmb_entities"],
				$affected,
				0
		);
	}
	
	public function genDocnumThumbnail() {
		global $msg;
		
		$affected = netbase_explnum::gen_docnum_thumbnail();
		
		return $this->getFormattedResponse(
				$msg["gen_docnum_thumbnail"],
				$affected." ".$msg["gen_docnum_thumbnail_end"],
				$affected,
				0
		);
	}
	
	public static function getProgression() {
		return static::$progression;
	}
	
	public static function setProgression($progression) {
		static::$progression = $progression;
	}
	
	public static function canGoNextStep() {
		if(empty(static::$progression) || round(static::$progression) == 100) {
			return true;
		}
		return false;
	}
	
	public static function initProgresion() {
		static::$progression = 0;
	}
	
	public static function setPacketSize($packet_size) {
		static::$packet_size = intval($packet_size);
	}
	
	public static function setIndexFieldsNumber($index_fields_number) {
	    static::$index_fields_number = intval($index_fields_number);
	}
	
	public static function setIndexFieldsPosition($index_fields_position) {
	    static::$index_fields_position = intval($index_fields_position);
	}
	
	/*Fonction copiée du fichier ./admin/netbase/category.inc.php*/
	/*Ne doit être appelable*/
	//	public function process_categ($id_noeud) {
	//		global $dbh;
	//
	//		global $deleted;
	//		global $lot;
	//
	//		$res = noeuds::listChilds($id_noeud, 0);
	//		$total = pmb_mysql_num_rows($res);
	//		if ($total) {
	//			while ($row = pmb_mysql_fetch_object($res)) {
	//				// la categorie a des filles qu'on va traiter
	//				$this->process_categ ($row->id_noeud);
	//			}
	//
	//			// après ménage de ses filles, reste-t-il des filles ?
	//			$total_filles = noeuds::hasChild($id_noeud);
	//
	//			// categ utilisée en renvoi voir ?
	//			$total_see = noeuds::isTarget($id_noeud);
	//
	//			// est-elle utilisée ?
	//			$iuse = noeuds::isUsedInNotices($id_noeud) + noeuds::isUsedinSeeALso($id_noeud);
	//
	//			if(!$iuse && !$total_filles && !$total_see) {
	//				$deleted++ ;
	//				noeuds::delete($id_noeud);
	//			}
	//
	//		} else { // la catégorie n'a pas de fille on va la supprimer si possible
	//				// regarder si categ utilisée
	//				$iuse = noeuds::isUsedInNotices($id_noeud) + noeuds::isUsedinSeeALso($id_noeud);
	//				if(!$iuse) {
	//					$deleted++ ;
	//					noeuds::delete($id_noeud);
	//				}
	//		}
	//	}
	
	}