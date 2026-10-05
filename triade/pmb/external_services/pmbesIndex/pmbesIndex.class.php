<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesIndex.class.php,v 1.11 2023/07/26 15:07:58 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/external_services.class.php");
require_once("$include_path/mysql_connect.inc.php");

/*
ATTENTION: Si vous modifiez de fichier vous devez aussi modifier le fichier pmbesClean.class.php 
*/

class pmbesIndex extends external_services_api_class {
	
	public function reindexRecords($list_notices=array()){
		$result = array();
		$result['nb_reindexed_records'] = 0;
		if (SESSrights & ADMINISTRATION_AUTH) {
			pmb_mysql_query("set wait_timeout=3600");
			$req="SELECT notice_id FROM notices ";
			if(count($list_notices)){
				$req.="WHERE notice_id IN (".implode(",",$list_notices).") ";
			}else{//Si on fait toute la base
				//remise a zero de la table au début
				//pmb_mysql_query("truncate notices_global_index");
				//pmb_mysql_query("truncate notices_mots_global_index");
			}
			$req.=" ORDER BY notice_id";
			$query = pmb_mysql_query($req);
			if($query && pmb_mysql_num_rows($query)) {
				$result['nb_total_records'] = pmb_mysql_num_rows($query);
				while($mesNotices = pmb_mysql_fetch_assoc($query)) {
					notice::majNoticesTotal($mesNotices['notice_id']);
				}
				pmb_mysql_free_result($query);
			}
			
			$req="SELECT count(1) FROM notices_global_index ";
			if(count($list_notices)){
				$req.="WHERE num_notice IN (".implode(",",$list_notices).") ";
			}
			$not = pmb_mysql_query($req);
			$result['nb_reindexed_records'] = pmb_mysql_result($not, 0, 0);
		}
		return $result;
	}
	
	public function indexGlobal() {
		global $msg, $charset, $PMBusername;
		$result = '';
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_reindex_global"], ENT_QUOTES, $charset)."</h3>";
			pmb_mysql_query("set wait_timeout=3600");
			//remise a zero de la table au début
			pmb_mysql_query("delete from notices_global_index");
			pmb_mysql_query("delete from notices_mots_global_index");
				
			$query = pmb_mysql_query("select notice_id from notices order by notice_id");
			if(pmb_mysql_num_rows($query)) {
				while($mesNotices = pmb_mysql_fetch_assoc($query)) {
					// Mise à jour de la table "notices_global_index"
			    	notice::majNoticesGlobalIndex($mesNotices['notice_id']);
			    	// Mise à jour de la table "notices_mots_global_index"
			    	notice::majNoticesMotsGlobalIndex($mesNotices['notice_id']);             		   	
				}
				pmb_mysql_free_result($query);
			}
			
			$not = pmb_mysql_query("SELECT count(1) FROM notices_global_index");
			$count = pmb_mysql_result($not, 0, 0);
			$result .= $count." ".htmlentities($msg["nettoyage_res_reindex_global"], ENT_QUOTES, $charset);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}

		return $result;	
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
		
			$query = pmb_mysql_query("SELECT author_id as id,concat(author_name,' ',author_rejete,' ', author_lieu, ' ',author_ville,' ',author_pays,' ',author_numero,' ',author_subdivision) as auteur from authors LIMIT $start, $lot");
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
			
			$query = pmb_mysql_query("SELECT serie_id as id, serie_name from series LIMIT $start, $lot");
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
			
			$query = pmb_mysql_query("SELECT indexint_id as id, concat(indexint_name,' ',indexint_comment) as index_indexint from indexint LIMIT $start, $lot");
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
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		
		return $result;
	}
	
	public function cleanAuthors() {
		global $msg, $charset, $PMBusername;
		
		$result = '';
		if (SESSrights & ADMINISTRATION_AUTH) {
			//1er passage
			$result .= "<h3>".htmlentities($msg["nettoyage_suppr_auteurs"], ENT_QUOTES, $charset)."</h3>";
			$affected = 0;
			pmb_mysql_query("delete authors from authors left join responsability on responsability_author=author_id where responsability_author is null and author_see=0 ");
			$affected += pmb_mysql_affected_rows();
			
			//2eme passage
			$result .= "<h3>".htmlentities($msg["nettoyage_renvoi_auteurs"], ENT_QUOTES, $charset)."</h3>";
	
			pmb_mysql_query("update authors A1 left join authors A2 on A1.author_see=A2.author_id set A1.author_see=0 where A2.author_id is null");
			$affected += pmb_mysql_affected_rows();
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_auteurs"], ENT_QUOTES, $charset);
			pmb_mysql_query('OPTIMIZE TABLE authors');
			
			$affected = 0;
			//3eme passage
			$result .= "<h3>".htmlentities($msg["nettoyage_responsabilites"], ENT_QUOTES, $charset)." : 1</h3>";
	
			pmb_mysql_query("delete responsability from responsability left join notices on responsability_notice=notice_id where notice_id is null ");
			$affected += pmb_mysql_affected_rows();
			
			//4eme passage
			$notices = pmb_mysql_query("SELECT count(1) FROM responsability where responsability_author<>0 ");
			$count = pmb_mysql_result($notices, 0, 0) ;
	
			$result .= "<h3>".htmlentities($msg["nettoyage_responsabilites"], ENT_QUOTES, $charset)." : 2</h3>";
	
			pmb_mysql_query("delete responsability from responsability left join authors on responsability_author=author_id where author_id is null ");
			$affected += pmb_mysql_affected_rows();
	
			$result .= $affected." ".htmlentities($msg["nettoyage_res_responsabilites"], ENT_QUOTES, $charset);
			pmb_mysql_query('OPTIMIZE TABLE authors');
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	public function cleanPublishers() {
		global $msg, $charset, $PMBusername;
		
		$result = '';
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_suppr_editeurs"], ENT_QUOTES, $charset)."</h3>";
			
			// creation table tempo contenant les id des publishers utilisés
			pmb_mysql_query("create temporary table tmppub as select distinct ed1_id as edid from notices  where ed1_id!=0 union select distinct ed2_id as edid from notices where ed2_id!=0");
			pmb_mysql_query("alter table tmppub add index (edid)");
	
			// supp des pub non utilisés dans les collections, sous-collections et notices !
			pmb_mysql_query("delete publishers from publishers left join tmppub on ed_id=edid left join sub_collections on ed_id=sub_coll_parent left join collections on ed_id=collection_parent where sub_coll_parent is null and collection_parent is null and edid is null ");
			$affected = pmb_mysql_affected_rows();
	
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_editeurs"], ENT_QUOTES, $charset);
			pmb_mysql_query('OPTIMIZE TABLE publishers');
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
			return $result;
	}
	
	public function cleanCollections() {
		global $msg, $charset, $PMBusername;
		
		$result = '';
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_suppr_collections"], ENT_QUOTES, $charset)."</h3>";
			
			pmb_mysql_query("delete collections from collections left join notices on collection_id=coll_id left join sub_collections on sub_coll_parent=collection_id where coll_id is null and sub_coll_parent is null ");
			$affected = pmb_mysql_affected_rows();
			
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_collections"], ENT_QUOTES, $charset);
			pmb_mysql_query('OPTIMIZE TABLE collections');
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	public function cleanSubcollections() {
		global $msg, $charset, $PMBusername;
		
		$result = '';
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_suppr_subcollections"], ENT_QUOTES, $charset)."</h3>";
					
			pmb_mysql_query("delete sub_collections from sub_collections left join notices on sub_coll_id=subcoll_id where subcoll_id is null ");
			$affected = pmb_mysql_affected_rows();
						
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_subcollections"], ENT_QUOTES, $charset);
			pmb_mysql_query('OPTIMIZE TABLE sub_collections');
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	public function cleanCategories() {
		global $msg, $charset, $PMBusername;
		
		if ($deleted=="") $deleted=0 ;

		$result = '';
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_suppr_categories"], ENT_QUOTES, $charset)."</h3>";
			
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

			$result .= $deleted." ".htmlentities($msg["nettoyage_res_suppr_categories"], ENT_QUOTES, $charset);

			noeuds::optimize();
			categories::optimize();
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	public function cleanSeries() {
		global $msg, $charset, $PMBusername;
		
		$result = '';
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_suppr_series"], ENT_QUOTES, $charset)."</h3>";
			
			pmb_mysql_query("delete series from series left join notices on tparent_id=serie_id where tparent_id is null");
			$affected = pmb_mysql_affected_rows();
			
			pmb_mysql_query("update notices left join series on tparent_id=serie_id set tparent_id=0 where serie_id is null");
			
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_series"], ENT_QUOTES, $charset);
			pmb_mysql_query('OPTIMIZE TABLE series');
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	public function cleanTitresUniformes() {
		global $msg, $charset, $PMBusername;
		
		$result = '';
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_suppr_titres_uniformes"], ENT_QUOTES, $charset)."</h3>";
			
			$query = pmb_mysql_query("SELECT tu_id from titres_uniformes left join notices_titres_uniformes on ntu_num_tu=tu_id where ntu_num_tu is null");
			$affected=0;
			if($affected = pmb_mysql_num_rows($query)){
				while ($ligne = pmb_mysql_fetch_object($query)) {
					$tu = new titre_uniforme($ligne->tu_id);
					$tu->delete();
				}
			}

			//Nettoyage des informations d'autorités pour les sous collections
			titre_uniforme::delete_autority_sources();

			$query = pmb_mysql_query("delete notices_titres_uniformes from notices_titres_uniformes left join titres_uniformes on ntu_num_tu=tu_id where tu_id is null");
			$affected = pmb_mysql_affected_rows();
			
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_titres_uniformes"], ENT_QUOTES, $charset);
			pmb_mysql_query('OPTIMIZE TABLE titres_uniformes');
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	
	public function cleanIndexint() {
		global $msg, $charset, $PMBusername;
		
		$result = '';
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["nettoyage_suppr_indexint"], ENT_QUOTES, $charset)."</h3>";
			
			$query = pmb_mysql_query("SELECT indexint_id from indexint left join notices on indexint=indexint_id where notice_id is null");
			$affected=0;
			if($affected = pmb_mysql_num_rows($query)){
				while ($ligne = pmb_mysql_fetch_object($query)) {
					$tu = new indexint($ligne->indexint_id);
					$tu->delete();
				}
			}
			$query = pmb_mysql_query("update notices left join indexint ON indexint=indexint_id SET indexint=0 WHERE indexint_id is null");
			
			$result .= $affected." ".htmlentities($msg["nettoyage_res_suppr_indexint"], ENT_QUOTES, $charset);
			pmb_mysql_query('OPTIMIZE TABLE indexint');
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
	

	
	public function cleanNotices() {
		global $msg, $charset, $PMBusername;
		
		$result = '';
		if (SESSrights & ADMINISTRATION_AUTH) {	
			$result .= "<h3>".htmlentities($msg["nettoyage_suppr_notices"], ENT_QUOTES, $charset)."</h3>";
			pmb_mysql_query("set wait_timeout=3600");
			// La routine ne nettoie pour l'instant que les monographies
			pmb_mysql_query("delete notices  
				FROM notices left join exemplaires on expl_notice=notice_id  
					left join explnum on explnum_notice=notice_id 
					left join notices_relations NRN on NRN.num_notice=notice_id  
					left join notices_relations NRL on NRL.linked_notice=notice_id 
				WHERE niveau_biblio='m' AND niveau_hierar='0' and explnum_notice is null and expl_notice is null and NRN.num_notice is null and NRL.linked_notice is null");
			$affected = pmb_mysql_affected_rows();
			$result .= "".$affected." ".htmlentities($msg["nettoyage_res_suppr_notices"], ENT_QUOTES, $charset)."";
			pmb_mysql_query('OPTIMIZE TABLE notices');
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
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

	public function indexDocnum() {
		global $msg, $dbh, $charset, $PMBusername;
		
		$result = '';
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result .= "<h3>".htmlentities($msg["docnum_reindexation"], ENT_QUOTES, $charset)."</h3>";
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
			$count = pmb_mysql_result($explnum, 0, 0);
			$result .= $count." ".htmlentities($msg['docnum_reindex_expl'], ENT_QUOTES, $charset);
		} else {
			$result .= sprintf($msg["planificateur_rights_bad_user_rights"], $PMBusername);
		}
		return $result;
	}
}
?>