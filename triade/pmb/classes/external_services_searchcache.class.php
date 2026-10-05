<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: external_services_searchcache.class.php,v 1.24.4.1 2025/02/12 12:34:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
global $pmb_external_service_search_cache, $search;
global $lang, $msg;
global $op_0_s_2, $field_0_s_2;
global $gestion_acces_active, $gestion_acces_empr_notice;

require_once $class_path."/external_services_rights.class.php";
require_once $class_path."/external_services_converters.class.php";
require_once $class_path."/acces.class.php";
require_once $class_path."/external_services_caches.class.php";
require_once $class_path."/search.class.php";


class external_services_searchcache {

	public $search_unique_id;
	public $search=NULL;
	public $cache_date=0;
	public $outdated=true;
	public $serialized_search = "";
	public $search_realm="";
	public $PMBUserId=-1; //-1 : ne pas tenir compte; 0 : utilisateur par défaut; x: utilisateur  d'id x
	public $OPACEmprId=-1; //-1: ne pas tenir compte; 0 : utilisateur par défaut; x: emprunteur d'id x
	public $cache_duration=3600;
	public $id_prefix="";//Prefixe pour les IDs, dès fois qu'on veuille faire la même recherche avec des durées de cache différentes
	public $cache = NULL;
	public $external_search = false;
	public $source_ids = array();

	public function __construct($search_realm, $search_unique_id='', $PMBUserId=-1, $OPACEmprId=-1, $cache_duration=false, $id_prefix="", $newsearch=false) {

		global $pmb_external_service_search_cache, $search;

		$opac_realm=false;
		$full_path='';
		if (substr($search_realm, 0, 5) == 'opac|') {
			$search_realm = substr($search_realm, 5);
			global $base_path;
			$full_path = $base_path."/includes/search_queries/";
			$opac_realm = true;
		}

		if ($opac_realm) {
			global $lang;
			if (file_exists("$base_path/includes/messages/$lang.xml")) {
				//Allons chercher les messages
				global $class_path;
				include_once("$class_path/XMLlist.class.php");
				$messages = new XMLlist("$base_path/includes/messages/$lang.xml", 0);
				$messages->analyser();
				global $msg;
				$msg = $messages->table;
			}
		}

		$this->source_ids = array();
		if (preg_match('/\|sources\([0-9]+(,[0-9]+)*\)$/', $search_realm)) {
			preg_match_all('/(\d+)(?=,|\))/', $search_realm, $m);
			if (isset($m[1]))
				$this->source_ids = array_values($m[1]);
			$search_realm = substr($search_realm, 0, strpos($search_realm, '|'));
		}
		array_walk($this->source_ids, function(&$a) {$a = intval($a);});
		$this->source_ids = array_unique($this->source_ids);
		if ($this->source_ids) {
			$this->external_search = true;
			//Il n'y a pas de droits sur les notices externes
			$PMBUserId = -1;
			$OPACEmprId = -1;

			//On décale tout
			global $search;
			for ($i=count($search)-1; $i>=0; $i--) {
				$search[$i+1]=$search[$i];
				$this->decale("field_".$i."_".$search[$i],"field_".($i+1)."_".$search[$i]);
				$this->decale("op_".$i."_".$search[$i],"op_".($i+1)."_".$search[$i]);
				$this->decale("inter_".$i."_".$search[$i],"inter_".($i+1)."_".$search[$i]);
				$this->decale("fieldvar_".$i."_".$search[$i],"fieldvar_".($i+1)."_".$search[$i]);
			}

			$search[0]="s_2";
			global $op_0_s_2;
			$op_0_s_2="EQ";
			global $field_0_s_2;
			$field_0_s_2=$this->source_ids;
			$inter="inter_1_".$search[1];
			global ${$inter};
			${$inter}="and";

		}

		$this->search = new search(false, $search_realm, $full_path);
		if (!isset($search) || !$search)
			$current_search_uniqueid = "";
		else
			$current_search_uniqueid = md5($this->search->serialize_search());
		$this->search_realm = $search_realm;
		$this->PMBUserId = intval($PMBUserId);
		$this->OPACEmprId = intval($OPACEmprId);
		$this->id_prefix = $id_prefix;
		$found = false;
		$pmb_external_service_search_cache = intval($pmb_external_service_search_cache);
		if ($cache_duration === false) {
			if ($pmb_external_service_search_cache === "")
				$this->cache_duration = 3600;
			else
				$this->cache_duration = $pmb_external_service_search_cache;
		}
		else if (!$cache_duration)
			$this->cache_duration = '0';
		else
			$this->cache_duration = $cache_duration;
		$this->cache = new external_services_cache('es_cache_blob', $this->cache_duration);

		$sql = "SELECT es_searchcache_searchid FROM es_searchcache WHERE es_searchcache_date + INTERVAL 1 WEEK < NOW()";
		$res = pmb_mysql_query($sql);
		if (pmb_mysql_num_rows($res)) {
			$array_id = array();
			while ($row = pmb_mysql_fetch_object($res)) {
				$array_id[] = $row->es_searchcache_searchid;
			}
			$this->cache->delete_objectref_list_multiple(CACHE_TYPE_NOTICE, $array_id, 'pmbesSearch');
		}
		$sql2 = "DELETE FROM es_searchcache WHERE es_searchcache_date + INTERVAL ".$this->cache_duration." SECOND < NOW()";
		pmb_mysql_query($sql2);

		//Cherchons avec le paramètre
		if ($search_unique_id) {
			$sql = "SELECT es_searchcache.*, (es_searchcache_date + INTERVAL ".$this->cache_duration." SECOND <= NOW()) AS outdated FROM es_searchcache WHERE es_searchcache_searchid = '".addslashes($this->search_realm)."_".$search_unique_id."'";
			$res = pmb_mysql_query($sql);
			if (!pmb_mysql_num_rows($res)) {
				$search_unique_id = "";
			}
			else {
				$row = pmb_mysql_fetch_assoc($res);
				$this->serialized_search = $row["es_searchcache_serializedsearch"];
				$this->cache_date = $row["es_searchcache_date"];
				$this->outdated = $row["outdated"];
				$found = true;
				$ids = array("'".$row["es_searchcache_searchid"]."'");
				if ($this->OPACEmprId != -1)
					$ids[] = "'".$row["es_searchcache_searchid"]."_E".$this->OPACEmprId."'";
				if ($this->PMBUserId != -1)
					$ids[] = "'".$row["es_searchcache_searchid"]."_".$this->PMBUserId."'";
				$sql = "UPDATE es_searchcache SET es_searchcache_date = NOW() WHERE es_searchcache_searchid IN (".implode(',', $ids).")";
				//pmb_mysql_query($sql); Si on repousse la date il faudrait aussi le faire pour ce qui est dans la table es_cache_blob sinon cela pose problème
			}
		}

		//Pas trouvé? Cherchons avec la recherche nue sans filtrage
		if (!$newsearch && !$found && !$search_unique_id && $current_search_uniqueid && ($this->PMBUserId == -1)) {
			$sql = "SELECT es_searchcache.*, (es_searchcache_date + INTERVAL ".$this->cache_duration." SECOND <= NOW()) AS outdated FROM es_searchcache WHERE es_searchcache_searchid = '".addslashes($this->search_realm)."_".$id_prefix.$current_search_uniqueid."'";
			$res = pmb_mysql_query($sql);
			if (!pmb_mysql_num_rows($res)) {
				$search_unique_id = "";
			}
			else {
				$row = pmb_mysql_fetch_assoc($res);
				$this->serialized_search = $row["es_searchcache_serializedsearch"];
				$this->cache_date = $row["es_searchcache_date"];
				$this->outdated = $row["outdated"];
				$ids = array("'".$row["es_searchcache_searchid"]."'");
				if ($this->OPACEmprId != -1)
					$ids[] = "'".$row["es_searchcache_searchid"]."_E".$this->OPACEmprId."'";
				if ($this->PMBUserId != -1)
					$ids[] = "'".$row["es_searchcache_searchid"]."_".$this->PMBUserId."'";
				$sql = "UPDATE es_searchcache SET es_searchcache_date = NOW() WHERE es_searchcache_searchid es_searchcache_searchid IN (".implode(',', $ids).")";
				//pmb_mysql_query($sql); Comme la requete est mauvaise ça ne sert à rien de la faire
			}
		}

		if ($newsearch) {
			$this->search_unique_id = '';
		}
		else if ($search_unique_id) {
			$this->search->unserialize_search($this->serialized_search);
			$this->search_unique_id = $search_unique_id;
		}
		else {
			$this->serialized_search = $this->search->serialize_search();
			$this->search_unique_id = $id_prefix.md5($this->serialized_search);
		}

	}

	public function decale($var,$var1) {
		global ${$var};
		global ${$var1};
		${$var1}=${$var};
	}

	public function unserialize_search($ssearch) {
		$this->search->unserialize_search($ssearch);
		$this->serialized_search = $this->search->serialize_search();
		if (!$this->search_unique_id) {
			$this->search_unique_id = $this->id_prefix.md5($this->serialized_search);
		}
	}

	public function update() {
		global $gestion_acces_active, $gestion_acces_empr_notice;
		//Si la recherche est encore bonne, on la garde.
		if (!$this->outdated)
			return;

		$table=$this->search->make_search();
		if ($table) {

			//Mise en cache de la recherche brute
			$sql = "REPLACE INTO es_searchcache (es_searchcache_searchid, es_searchcache_date, es_searchcache_serializedsearch) VALUES ('".addslashes($this->search_realm)."_".$this->search_unique_id."', NOW(), '".addslashes($this->serialized_search)."')";
			pmb_mysql_query($sql);

			//Et on vide le cache!
			$this->cache->delete_objectref_list(CACHE_TYPE_NOTICE, $this->search_realm."_".$this->search_unique_id, 'pmbesSearch');

			//Et on remplit le cache!

			//Vérifions si le champs de pertinence existe bien
			$has_pert = false;
			$result_fields = pmb_mysql_query("SHOW COLUMNS FROM ".$table);
			while ($row_field = pmb_mysql_fetch_assoc($result_fields)) {
				if ($row_field["Field"] == "pert")
					$has_pert = true;
    		}
    		//Adaptons la requete en fonction de la présence de la pertinence
    		if ($has_pert) {
    			if ($this->external_search)
					$requete="select $table.notice_id, $table.pert from $table ";
				else
					$requete="select $table.notice_id, $table.pert from $table, notices where $table.notice_id=notices.notice_id ";
    		}
			else {
				if ($this->external_search)
					$requete="select $table.notice_id, '' from $table ";
				else
					$requete="select $table.notice_id, '' from $table, notices where $table.notice_id=notices.notice_id ";
			}

			$this->cache->encache_objectref_list_from_select_with_content(CACHE_TYPE_NOTICE, $this->search_realm."_".$this->search_unique_id, 'pmbesSearch', $requete);

			//Si on a un utilisateur, on doit filtrer les résultats pour gérer les histoires de droits, ce qui créer une nouvelle recherche.
			if ($this->PMBUserId != -1) {
				$this->search->filter_searchtable_from_accessrights($table, $this->PMBUserId);

				//Et rebelote pour les trois requetes
				$this->search_unique_id .= "_".$this->PMBUserId;

				//Mise en cache de la recherche brute
				$sql = "REPLACE INTO es_searchcache (es_searchcache_searchid, es_searchcache_date, es_searchcache_serializedsearch) VALUES ('".addslashes($this->search_realm)."_".$this->search_unique_id."', NOW(), '".addslashes($this->serialized_search)."')";
				pmb_mysql_query($sql);

				//Et on vide le cache!
				$this->cache->delete_objectref_list(CACHE_TYPE_NOTICE, $this->search_realm."_".$this->search_unique_id, 'pmbesSearch');

				//Et on remplit le cache!
				//Vérifions si le champs de pertinence existe bien
				$has_pert = false;
				$result_fields = pmb_mysql_query("SHOW COLUMNS FROM ".$table);
				while ($row_field = pmb_mysql_fetch_assoc($result_fields)) {
					if ($row_field["Field"] == "pert")
						$has_pert = true;
	    		}
	    		//Adaptons la requete en fonction de la présence de la pertinence
	    		if ($has_pert)
					$requete="select $table.notice_id, $table.pert from $table, notices where $table.notice_id=notices.notice_id ";
				else
					$requete="select $table.notice_id, '' from $table, notices where $table.notice_id=notices.notice_id ";
				$this->cache->encache_objectref_list_from_select_with_content(CACHE_TYPE_NOTICE, $this->search_realm."_".$this->search_unique_id, 'pmbesSearch', $requete);
			}

			//Si on a un emprunteur on doit filtrer aussi les résultats et donc créer encore une nouvelle recherche
			if ($this->OPACEmprId != -1) {
				//Partie copiée depuis les fichiers de recherche de l'opac:
				$acces_j='';
				if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
					$ac= new acces();
					$dom_2= $ac->setDomain(2);
					$acces_j = $dom_2->getJoin($this->OPACEmprId,4,'notice_id');
				}
				if($acces_j) {
					$statut_j='';
					$statut_r='';
				} else {
					$user_code = pmb_mysql_result(pmb_mysql_query("SELECT empr_login FROM empr WHERE id_empr = ".$this->OPACEmprId), 0, 0);
					$statut_j=',notice_statut';
					$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($user_code ?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
				}

				//Et rebelote pour les trois requetes
				$this->search_unique_id .= "_E".$this->OPACEmprId;

				//Mise en cache de la recherche brute
				$sql = "REPLACE INTO es_searchcache (es_searchcache_searchid, es_searchcache_date, es_searchcache_serializedsearch) VALUES ('".addslashes($this->search_realm)."_".$this->search_unique_id."', NOW(), '".addslashes($this->serialized_search)."')";
				pmb_mysql_query($sql);

				//Et on vide le cache!
				$this->cache->delete_objectref_list(CACHE_TYPE_NOTICE, $this->search_realm."_".$this->search_unique_id, 'pmbesSearch');

				//Et on remplit le cache!
				//Vérifions si le champs de pertinence existe bien
				$has_pert = false;
				$result_fields = pmb_mysql_query("SHOW COLUMNS FROM ".$table);
				while ($row_field = pmb_mysql_fetch_assoc($result_fields)) {
					if ($row_field["Field"] == "pert")
						$has_pert = true;
	    		}
	    		//Adaptons la requete en fonction de la présence de la pertinence
	    		if ($has_pert)
					$requete="select $table.notice_id, $table.pert from $table, notices $acces_j $statut_j where $table.notice_id=notices.notice_id $statut_r ";
				else
					$requete="select $table.notice_id, '' from $table, notices $acces_j $statut_j where $table.notice_id=notices.notice_id $statut_r ";
				$this->cache->encache_objectref_list_from_select_with_content(CACHE_TYPE_NOTICE, $this->search_realm."_".$this->search_unique_id, 'pmbesSearch', $requete);
			}
		}
		pmb_mysql_query("drop table if exists $table");
		$this->outdated = false;
	}

	public function get_result_count($delete_expired=true) {

		$count = $this->cache->get_objectref_listcount(CACHE_TYPE_NOTICE, $this->search_realm."_".$this->search_unique_id, 'pmbesSearch',$delete_expired);
		return $count;
	}

	public function get_results($first_index, $number_to_fetch, $sort_type="",$delete_expired=true) {

		$this->update();

		$records = array();
		$requete = $this->cache->get_objectref_list_with_content_sql(CACHE_TYPE_NOTICE, $this->search_realm."_".$this->search_unique_id, 'pmbesSearch', "notice_id", "pert",false,false,$delete_expired);

		if (!$this->external_search && $sort_type) {
			global $class_path;
			require_once $class_path.'/sort.class.php';
			$sort=new sort('notices','base');
			$tri = array("nom_tri" => "", "tri_par" => $sort_type);
			$requete=$sort->appliquer_tri($tri,$requete,"notice_id",$first_index,$number_to_fetch);
		} else {
			$limit_from = intval($first_index);
			$limit_count = intval($number_to_fetch);
			if ($limit_from !== false && $limit_count) {
				$limit = " LIMIT ".$limit_from.','.$limit_count;
			}
			else {
				$limit = "";
			}
			$requete .= $limit;
		}

		$res = pmb_mysql_query($requete);
		while($row=pmb_mysql_fetch_assoc($res)) {
			$records[] = $row["notice_id"];
		}

		return $records;

	}

	public function get_typdoc_list() {

		if ($this->external_search) {
			return array();
		}
		$requete = $this->cache->get_objectref_list_with_content_sql(CACHE_TYPE_NOTICE, $this->search_realm."_".$this->search_unique_id, 'pmbesSearch', "notice_id","pert");
		$req = "SELECT distinct(typdoc) as docType FROM (".$requete.") as every_derived_table_must_have_its_own_alias LEFT JOIN notices ON every_derived_table_must_have_its_own_alias.notice_id = notices.notice_id";
		$res = pmb_mysql_query($req);
		while($row=pmb_mysql_fetch_assoc($res)) {
			$records[] = $row["docType"];
		}
		return $records;
	}


}
