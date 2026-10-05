<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: external_services_caches.class.php,v 1.14 2022/03/25 09:05:41 dgoron Exp $

define("CACHE_TYPE_NOTICE", 1);
define("CACHE_TYPE_OPACEMPRSESSION", 2);
define("CACHE_TYPE_MISC", 3);
define("CACHE_TYPE_EXTERNAL_NOTICE", 4);
define("CACHE_TYPE_CMS_EDITORIAL_ARTICLE", 5);
define("CACHE_TYPE_SHELF", 6);

class external_services_cache {
	public $table_name='';
	public $objects=array(); //Les objects que manipule le cache
	public $cache_duration=3600; //La durée de vie du cache, en secondes

	//Constructeur
	public function __construct($table_name, $cache_duration) {
		$this->table_name = $table_name;
		$this->cache_duration = intval($cache_duration);
	}

	//Vide les valeurs trop vielles
	public function delete_expired() {
		//Deletons les valeurs trop vielles
		$sql = "DELETE FROM ".$this->table_name." WHERE es_cache_expirationdate < NOW()";
		pmb_mysql_query($sql);
	}
	
	/*
		get_objectref_list, delete_objectref_list et encache_objectref_list:
		  ces fonction servent à mettre en cache des listes d'identifiants d'objets, par exemple le resultat d'une recherche
		  il faut être bien sûr lorsqu'on va récupérer cette liste dans le cache qu'elle y est encore
		  on retrouve normalement toujours exactement ce qu'on a rentré
	*/
	
	public function get_objectref_list($object_type, $object_owner, $object_format, $limit_from=false, $limit_count=false, $delete_expired=true) {
		//Deletons les valeurs trop vieilles
		if ($delete_expired) {
			$this->delete_expired();
		}

		//Allons chercher les valeurs
		$limit_from = intval($limit_from);
		$limit_count = intval($limit_count);
		if ($limit_from !== false && $limit_count) {
			$limit = " LIMIT ".$limit_from.','.$limit_count;
		}
		else {
			$limit = "";
		}
			
		$sql = "SELECT es_cache_objectref FROM ".$this->table_name." WHERE es_cache_owner = '".addslashes($object_owner)."' AND es_cache_objectformat = '".addslashes($object_format)."' AND es_cache_objecttype = '".addslashes($object_type)."'".$limit;
		$res = pmb_mysql_query($sql);
		while($row=pmb_mysql_fetch_assoc($res)) {
			$this->objects[] = $row["es_cache_objectref"];
		}
		return $this->objects;
	}
	
	public function get_objectref_list_sql($object_type, $object_owner, $object_format, $field_name, $limit_from=false, $limit_count=false, $delete_expired=true) {
		//Deletons les valeurs trop vieilles
		if ($delete_expired) {
			$this->delete_expired();
		}

		//Allons chercher les valeurs
		$limit_from = intval($limit_from);
		$limit_count = intval($limit_count);
		if ($limit_from !== false && $limit_count) {
			$limit = " LIMIT ".$limit_from.','.$limit_count;
		}
		else {
			$limit = "";
		}
			
		$sql = "SELECT es_cache_objectref AS ".$field_name." FROM ".$this->table_name." WHERE es_cache_owner = '".addslashes($object_owner)."' AND es_cache_objectformat = '".addslashes($object_format)."' AND es_cache_objecttype = '".addslashes($object_type)."'".$limit;
		return $sql;
	}
	
	public function get_objectref_list_with_content_sql($object_type, $object_owner, $object_format, $field_name, $fieldcontent_name, $limit_from=false, $limit_count=false, $delete_expired=true) {
		//Deletons les valeurs trop vieilles
		if ($delete_expired) {
			$this->delete_expired();
		}
		
		//Allons chercher les valeurs
		$limit_from = intval($limit_from);
		$limit_count = intval($limit_count);
		if ($limit_from !== false && $limit_count) {
			$limit = " LIMIT ".$limit_from.','.$limit_count;
		}
		else {
			$limit = "";
		}
		if($fieldcontent_name == "pert"){
		    $fieldcontent = "(es_cache_content * 1) AS ".$fieldcontent_name;
		} else {
		    $fieldcontent = "(es_cache_content) AS ".$fieldcontent_name;
		    
		}
		$sql = "SELECT es_cache_objectref AS ".$field_name.", ".$fieldcontent." FROM ".$this->table_name." WHERE es_cache_owner = '".addslashes($object_owner)."' AND es_cache_objectformat = '".addslashes($object_format)."' AND es_cache_objecttype = '".addslashes($object_type)."'".$limit;
		return $sql;
	}
	
	public function get_objectref_listcount($object_type, $object_owner, $object_format, $delete_expired=true) {
		//Deletons les valeurs trop vieilles
		if ($delete_expired) {
			$this->delete_expired();
		}
		
		$sql = "SELECT COUNT(1) FROM ".$this->table_name." WHERE es_cache_owner = '".addslashes($object_owner)."' AND es_cache_objectformat = '".addslashes($object_format)."' AND es_cache_objecttype = '".addslashes($object_type)."'";
		$res = pmb_mysql_query($sql);
		$result = pmb_mysql_result($res, 0, 0);
		return $result;
	}

	public function delete_objectref_list($object_type, $object_owner, $object_format) {
		$sql = "DELETE FROM ".$this->table_name." WHERE es_cache_owner = '".addslashes($object_owner)."' AND es_cache_objectformat = '".addslashes($object_format)."' AND es_cache_objecttype = '".addslashes($object_type)."'";
		pmb_mysql_query($sql);		
	}

	public function delete_objectref_list_multiple($object_type, $object_owner, $object_format) {
		if (!$object_owner || !is_array($object_owner) || !count($object_owner))
			return;
		$in_clause = array();
		foreach ($object_owner as $aowner)
			$in_clause[] = "'".addslashes($aowner)."'";
		$in_clause = implode(",", $in_clause);
		$sql = "DELETE FROM ".$this->table_name." WHERE es_cache_owner IN (".$in_clause.") AND es_cache_objectformat = '".addslashes($object_format)."' AND es_cache_objecttype = '".addslashes($object_type)."'";
		pmb_mysql_query($sql);		
	}
	
	/* non utilisée
	public function delete_objectref_list_multiple_using_query($object_type, $object_owner_sql, $object_format) {
		$sql = "DELETE FROM ".$this->table_name." WHERE es_cache_owner IN (".$object_owner_sql.") AND es_cache_objectformat = '".addslashes($object_format)."' AND es_cache_objecttype = '".addslashes($object_type)."'";
		pmb_mysql_query($sql);		
	}*/
	
	public function encache_objectref_list($object_type, $object_owner, $object_format, $object_refs) {
		//Faisons ça par paquet de 1000 pour ne pas brusquer le serveur de base de donnée
		$paquets_de_1000_objets = array_chunk($object_refs, 1000);
		foreach ($paquets_de_1000_objets as $someobjects) {
			$information = $object_type.", '".$object_format."', '".$object_owner."', NOW(), NOW() + INTERVAL ".$this->cache_duration." SECOND, ''";
			$values = implode(", ".$information."),(", $someobjects);
			$values = '('.$values.','.$information.")";
			$sql = "INSERT IGNORE INTO ".$this->table_name." (es_cache_objectref, es_cache_objecttype, es_cache_objectformat, es_cache_owner, es_cache_creationdate, es_cache_expirationdate, es_cache_content) VALUES ".$values;
			pmb_mysql_query($sql);
		}
	}

	//Cette fonction remplit le cache à partir d'une sous requête renvoyant uniquement une colonne
	public function encache_objectref_list_from_select($object_type, $object_owner, $object_format, $ref_select) {
		$sql = "INSERT IGNORE INTO ".$this->table_name." (es_cache_objectref, es_cache_objecttype, es_cache_objectformat, es_cache_owner, es_cache_creationdate, es_cache_expirationdate) SELECT subquery.* , ".$object_type.", '".addslashes($object_format)."', '".addslashes($object_owner)."', NOW(), NOW() + INTERVAL ".$this->cache_duration." SECOND FROM (".$ref_select.") as subquery";
		pmb_mysql_query($sql);		
	}
	
	//Cette fonction remplit le cache à partir d'une sous requête renvoyant uniquement deux colonnes
	public function encache_objectref_list_from_select_with_content($object_type, $object_owner, $object_format, $ref_select) {
		$sql = "INSERT IGNORE INTO ".$this->table_name." (es_cache_objectref, es_cache_content, es_cache_objecttype, es_cache_objectformat, es_cache_owner, es_cache_creationdate, es_cache_expirationdate) SELECT subquery.* , ".$object_type.", '".addslashes($object_format)."', '".addslashes($object_owner)."', NOW(), NOW() + INTERVAL ".$this->cache_duration." SECOND FROM (".$ref_select.") as subquery";	
		pmb_mysql_query($sql);
	}
	
	/*
		get_objectref_contents, delete_objectref_contents et encache_objectref_contents:
		  ces fonction servent à mettre en cache des données relatif à des identifiants dans des formats spécifiques
		  elles servent par exemple à mettre en cache des notices converties sous des formats spécifiques
		  à partir d'une liste d'identifiant d'objets, on va chercher dans le cache ce qui existe, mais on ne sait pas combien de résultat on va trouver
	*/
	
	public function get_objectref_contents($object_type, $object_owner, $object_format, $object_refs, $delete_expired=true) {
		//Deletons les valeurs trop vieilles
		if ($delete_expired) {
			$this->delete_expired();
		}
		
		//Faisons ça par paquet de 100 pour ne pas brusquer le serveur de base de donnée
		$object_owner=addslashes($object_owner);
		$object_format=addslashes($object_format);
		$paquets_de_100_objets = array_chunk($object_refs, 100);
		foreach ($paquets_de_100_objets as $someobjects) {
			$sql = "SELECT es_cache_objectref, es_cache_content FROM ".$this->table_name." WHERE es_cache_objecttype = ".$object_type." AND es_cache_objectformat = '".$object_format."' AND es_cache_objectref IN (".implode(",", $someobjects).") AND es_cache_owner = '".$object_owner."'";
			$res = pmb_mysql_query($sql);
			while ($row = pmb_mysql_fetch_assoc($res)) {
				$this->objects[$row["es_cache_objectref"]] = $row["es_cache_content"];
			}
		}
		return $this->objects;
	}

	public function delete_objectref_contents($object_type, $object_owner, $object_format, $object_refs) {
		//Faisons ça par paquet de 1000 pour ne pas brusquer le serveur de base de donnée
		$paquets_de_1000_objets = array_chunk($object_refs, 1000);
		foreach ($paquets_de_1000_objets as $someobjects) {
			$sql = "DELETE FROM ".$this->table_name." WHERE es_cache_objecttype = ".$object_type." AND es_cache_objectformat = '".$object_format."' AND es_cache_objectref IN (".implode(",", $someobjects).") AND   	es_cache_owner = ".$object_owner;
			pmb_mysql_query($sql);
		}
	}

	public function encache_objectref_contents($object_type, $object_owner, $object_format, $objects) {
		//Faisons ça par paquet de 50 pour ne pas brusquer le serveur de base de donnée
		$paquets_de_50_objets = array_chunk($objects, 50, true);
		foreach ($paquets_de_50_objets as $someobjects) {
			$values = array();
			foreach ($someobjects as $object_ref => $object_content) {
				$values[] = "(".$object_ref.",'".addslashes($object_content)."',".$object_type.", '".$object_format."', '".$object_owner."', NOW(), NOW() + INTERVAL ".$this->cache_duration." SECOND)";
			}
			$valuee = implode(',', $values);
			$sql = "INSERT IGNORE INTO ".$this->table_name." (es_cache_objectref, es_cache_content, es_cache_objecttype, es_cache_objectformat, es_cache_owner, es_cache_creationdate, es_cache_expirationdate) VALUES ".$valuee;
			pmb_mysql_query($sql);
		}		
	}
	
	/*
		encache_single_object, decache_single_object
		  ces fonction servent à mettre en cache une seule donnée
	*/
	
	public function encache_single_object($object_ref, $object_type, $object_value) {
		$object_type = intval($object_type);
		$sql = "REPLACE INTO ".$this->table_name." SET es_cache_objectref = '".addslashes($object_ref)."', es_cache_objecttype = ".$object_type.", es_cache_objectformat = 'none', es_cache_owner = 'single_cache', es_cache_creationdate = NOW(), es_cache_expirationdate = NOW() + INTERVAL ".$this->cache_duration." SECOND, es_cache_content = '".addslashes($object_value)."'";
		pmb_mysql_query($sql);
		return pmb_mysql_error() != '';
	}
	
	public function decache_single_object($object_ref, $object_type, $delete_expired=true) {
		//Deletons les valeurs trop vieilles
		if ($delete_expired) {
			$this->delete_expired();
		}
				
		$object_type = intval($object_type);
		$sql = "SELECT es_cache_content FROM ".$this->table_name." WHERE es_cache_objecttype = ".$object_type." AND es_cache_objectformat = 'none' AND es_cache_owner='single_cache' AND es_cache_objectref='".addslashes($object_ref)."'";
		$res = pmb_mysql_query($sql);
		if (!pmb_mysql_num_rows($res))
			return false;
		return pmb_mysql_result($res, 0, 0);
	}
	
	public function delete_single_object($object_ref, $object_type) {
		$object_type = intval($object_type);
		$sql = "DELETE FROM ".$this->table_name." WHERE es_cache_objecttype = ".$object_type." AND es_cache_objectformat = 'none' AND es_cache_owner='single_cache' AND es_cache_objectref='".addslashes($object_ref)."'";
		pmb_mysql_query($sql);
		
	}
}



?>