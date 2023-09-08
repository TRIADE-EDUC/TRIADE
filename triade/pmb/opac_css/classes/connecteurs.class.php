<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: connecteurs.class.php,v 1.27 2019-06-03 07:55:18 pmbs Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/parser.inc.php");

class connector {
	public $repository;				//Est-ce un entrepot ?
	public $timeout;					//Time-out
	public $retry;						//Nombre de réessais
	public $ttl;						//Time to live
	public $parameters;				//Paramètres propres au connecteur
	public $sources;					//Sources disponibles
	public $msg;						//Messages propres au connecteur
	public $connector_path;
	
	//Calcul ISBD
	protected static $xml_indexation;
	protected static $isbd_ask_list = array();
	protected static $ufields = array();
	
	//Variables internes pour la progression de la récupération des notices
	public $callback_progress;		//Nom de la fonction de callback progression passée par l'appellant
	public $source_id;				//Numéro de la source en cours de synchro
	public $del_old;				//Supression ou non des notices dejà existantes
	
	//Résultat de la synchro
	public $error;					//Y-a-t-il eu une erreur
	public $error_message;			//Si oui, message correspondant
	
	public function __construct($connector_path="") {
		$this->fetch_global_properties();
		$this->get_messages($connector_path);
		$this->connector_path=$connector_path;
	}
	
	//Signature de la classe
	public function get_id() {
		return "";
	}
	
	//Est-ce un entrepot ?
	public function is_repository() {
		return 0;
	}
	
	public function get_libelle($message) {
		if (substr($message,0,4)=="msg:") return $this->msg[substr($message,4)]; else return $message;
	}
	
	protected function unserialize_source_params($source_id) {
		$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			$vars=unserialize($params["PARAMETERS"]);
			$params["PARAMETERS"]=$vars;
		}
		return $params;
	}
	
	public function get_messages($connector_path) {
		global $lang;
		
		if (file_exists($connector_path."/messages/".$lang.".xml")) {
			$file_name=$connector_path."/messages/".$lang.".xml";
		} else if (file_exists($connector_path."/messages/fr_FR.xml")) {
			$file_name=$connector_path."/messages/fr_FR.xml";
		}
		if ($file_name) {
			$xmllist=new XMLlist($file_name);
			$xmllist->analyser();
			$this->msg=$xmllist->table;
		}
	}
	
	//Récupération de la liste des sources
	public function get_sources() {
		if(!isset($this->sources) || !count($this->sources)) {
			$sources=array();
			$requete="select * from connectors_sources where id_connector='".addslashes($this->get_id())."' and opac_allowed=1 order by connectors_sources.name";
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)) {
				while ($r=pmb_mysql_fetch_object($resultat)) {
					$s["SOURCE_ID"]=$r->source_id;
					$s["PARAMETERS"]=$r->parameters;
					$s["NAME"]=$r->name;
					$s["COMMENT"]=$r->comment;
					$s["RETRY"]=$r->retry;
					$s["REPOSITORY"]=$r->repository;
					$s["TTL"]=$r->ttl;
					$s["TIMEOUT"]=$r->timeout;
					$s["OPAC_ALLOWED"]=$r->opac_allowed;
					$s["UPLOAD_DOC_NUM"]=$r->upload_doc_num;
					$s["REP_UPLOAD"] = $r->rep_upload;
					$s["ENRICHMENT"] = $r->enrichment;
					$s["OPAC_AFFILIATE_SEARCH"] = $r->opac_affiliate_search;
					$s["OPAC_SELECTED"] = $r->opac_selected;
					$s["GESTION_SELECTED"] = $r->gestion_selected;
					$s["TYPE_ENRICHEMENT_ALLOWED"]=unserialize($r->type_enrichment_allowed);
					$sources[$r->source_id]=$s;
				}
			}
			$this->sources=$sources;
		}
		return $this->sources;
	}
	
	//Récupération des paramètres d'une source
	public function get_source_params($source_id) {
		if ($source_id) {
			$requete="select * from connectors_sources where id_connector='".addslashes($this->get_id())."' and source_id=".$source_id;
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)) {
				$r=pmb_mysql_fetch_object($resultat);
				$s["SOURCE_ID"]=$r->source_id;
				$s["PARAMETERS"]=$r->parameters;
				$s["NAME"]=$r->name;
				$s["COMMENT"]=$r->comment;
				$s["RETRY"]=$r->retry;
				$s["REPOSITORY"]=$r->repository;
				$s["TTL"]=$r->ttl;
				$s["TIMEOUT"]=$r->timeout;
				$s["OPAC_ALLOWED"]=$r->opac_allowed;
				$s["UPLOAD_DOC_NUM"]=$r->upload_doc_num;
				$s["REP_UPLOAD"] = $r->rep_upload;
				$s["ENRICHMENT"] = $r->enrichment;
				$s["OPAC_AFFILIATE_SEARCH"] = $r->opac_affiliate_search;
				$s["OPAC_SELECTED"]=$r->opac_selected;
				$s["GESTION_SELECTED"] = $r->gestion_selected;
				if($r->type_enrichment_allowed == ""){
					$s["TYPE_ENRICHMENT_ALLOWED"] = array();
				}else{
					$s["TYPE_ENRICHMENT_ALLOWED"]=unserialize($r->type_enrichment_allowed);
				}
			} 
		} else {
			$s["SOURCE_ID"]="";
			$s["PARAMETERS"]="";
			$s["NAME"]="Nouvelle source";
			$s["COMMENT"]="";
			$s["RETRY"]=$this->retry;
			$s["REPOSITORY"]=$this->repository;
			$s["TTL"]=$this->ttl;
			$s["TIMEOUT"]=$this->timeout;
			$s["OPAC_ALLOWED"]=0;
			$s["UPLOAD_DOC_NUM"]=1;
			$s["REP_UPLOAD"] = 0;
			$s["ENRICHMENT"] = 0;
			$s["OPAC_AFFILIATE_SEARCH"] = 0;
			$s["OPAC_SELECTED"]=0;
			$s["GESTION_SELECTED"]=0;
			$s["TYPE_ENRICHMENT_ALLOWED"]=array();
		}
		//Gestion du timeout au niveau de mysql pour ne pas perdre la connection
		if($s["TIMEOUT"]){
			$res=pmb_mysql_query("SHOW SESSION VARIABLES like 'wait_timeout'");
			$timeout_default=0;
			if($res && pmb_mysql_num_rows($res)){
				$timeout_default=pmb_mysql_result($res,0,1);
			}
			pmb_mysql_query("SET SESSION wait_timeout=".($timeout_default+(($s["TIMEOUT"])*1)));
		}
		return $s;
	}
	
	//Formulaire des propriétés d'une source
	public function source_get_property_form($source_id) {
		return "";
	}
	
	public function make_serialized_source_properties($source_id) {
		$this->sources[$source_id]["PARAMETERS"]="";
	}
	
	//Formulaire de sauvegarde des propriétés d'une source
	public function source_save_property_form($source_id) {
		$this->make_serialized_source_properties($source_id);
		$requete="replace into connectors_sources (source_id,id_connector,parameters,comment,name,repository,retry,ttl,timeout,opac_allowed,upload_doc_num,rep_upload,enrichment,opac_affiliate_search,opac_selected,gestion_selected) values('".$source_id."','".addslashes($this->get_id())."','".addslashes($this->sources[$source_id]["PARAMETERS"])."','".addslashes($this->sources[$source_id]["COMMENT"])."','".addslashes($this->sources[$source_id]["NAME"])."','".addslashes($this->sources[$source_id]["REPOSITORY"])."','".addslashes($this->sources[$source_id]["RETRY"])."','".addslashes($this->sources[$source_id]["TTL"])."','".addslashes($this->sources[$source_id]["TIMEOUT"])."','".addslashes($this->sources[$source_id]["OPAC_ALLOWED"])."','".addslashes($this->sources[$source_id]["UPLOAD_DOC_NUM"])."','".addslashes($this->sources[$source_id]["REP_UPLOAD"])."','".addslashes($this->sources[$source_id]["ENRICHMENT"])."','".addslashes($this->sources[$source_id]["OPAC_AFFILIATE_SEARCH"])."','".addslashes($this->sources[$source_id]["OPAC_SELECTED"])."','".addslashes($this->sources[$source_id]["GESTION_SELECTED"])."')";
		return pmb_mysql_query($requete);
	}
	
	//Suppression d'une source
	public function del_source($source_id) {
		$requete="delete from connectors_sources where source_id=$source_id and id_connector='".addslashes($this->get_id())."'";
		return pmb_mysql_query($requete);
	}
	
	//Récupération  des propriétés globales par défaut du connecteur (timeout, retry, repository, parameters)
	public function fetch_default_global_values() {
		$this->timeout=5;
		$this->repository=2;
		$this->retry=3;
		$this->ttl=1800;
		$this->parameters="";
	}
	
	//Récupération  des propriétés globales du connecteur (timeout, retry, repository, parameters)
	public function fetch_global_properties() {
		$requete="select * from connectors where connector_id='".addslashes($this->get_id())."'";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);
			$this->repository=$r->repository;
			$this->timeout=$r->timeout;
			$this->retry=$r->retry;
			$this->ttl=$r->ttl;
			$this->parameters=$r->parameters;
		} else {
			$this->fetch_default_global_values();
		}
	}
	
	//Formulaire des propriétés générales
	public function get_property_form() {
		$this->fetch_global_properties();
		return "";	
	}
	
	public function make_serialized_properties() {
		//Mise en forme des paramètres à partir de variables globales (mettre le résultat dans $this->parameters)
		$this->parameters="";
	}
	
	//Sauvegarde des propriétés générales
	public function save_property_form() {
		$this->make_serialized_properties();
		$requete="replace into connectors (connector_id,parameters, retry, timeout, ttl, repository) values('".addslashes($this->get_id())."',
		'".addslashes($this->parameters)."','".$this->retry."','".$this->timeout."','".$this->ttl."','".$this->repository."')";
		return pmb_mysql_query($requete);
	}
	
	//Supression des notices dans l'entrepot !
	public function del_notices($source_id) {
		$requete="select * from source_sync where source_id=".$source_id;
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);
			if (!$r->cancel) return false;
		}
		pmb_mysql_query("TRUNCATE TABLE entrepot_source_".$source_id);
		pmb_mysql_query("delete from source_sync where source_id=".$source_id);
		return true;
	}
	
	//Annulation de la mise à jour (faux = synchro conservée dans la table, vrai = synchro supprimée dans la table)
	public function cancel_maj($source_id) {
		return false;
	}
	
	//Annulation de la mise à jour (faux = synchro conservée dans la table, vrai = synchro supprimée dans la table)
	public function break_maj($source_id) {
		return false;
	}
	
	public function sync_custom_page($source_id) {
		return '';
	}
	
	//Formulaire complementaire facultatif pour la synchronisation
	public function form_pour_maj_entrepot($source_id) {
		return false;
	}
	
	//Nécessaire pour passer les valeurs obtenues dans form_pour_maj_entrepot au javascript asynchrone
	public function get_maj_environnement($source_id) {
		return array();
	}
	
	//M.A.J. Entrepôt lié à une source
	public function maj_entrepot($source_id,$callback_progress="",$recover=false,$recover_env="") {
		return 0;
	}
	
	//Export d'une notice en UNIMARC
	public function to_unimarc($notice) {
	}
	
	//Export d'une notice en Dublin Core (c'est le minimum)
	public function to_dublin_core($notice) {
	}
	
	//Fonction de recherche
	public function search($source_id,$query,$search_id) {
	}
	
	//Recherche d'une page de résultat
	public function get_page_result($search_id,$page, $n_per_page) {
	}
	
	//Nombre de résultats d'une recherche
	public function get_n_results($search_id) {
	}
	
	//Récupération de la valeur d'une autorité
	public function get_values_from_id($id,$ufield) {
		$r="";
		switch ($ufield) {
			//Categorie
			case "60X":
				$requete="select libelle_categorie from categories where num_noeud=".$id;
				$r_cat=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_cat)) {
					$r=pmb_mysql_result($r_cat,0,0);
				}
				break;
			//Dewey
			case "676\$a686\$a":
				$requete="select indexint_name from indexint where indexint_id=".$id;
				$r_indexint=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_indexint)) {
					$r=pmb_mysql_result($r_indexint,0,0);
				}
				break;
			//Editeur
			case "210\$c":
				$requete="select ed_name from publishers where ed_id=".$id;
				$r_pub=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_pub)) {
					$r=pmb_mysql_result($r_pub,0,0);
				}
				break;
			//Collection
			case "225\$a410\$t":
				$requete="select collection_name from collections where collection_id=".$id;
				$r_coll=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_coll)) {
					$r=pmb_mysql_result($r_coll,0,0);
				}
				break;
			//Sous collection
			case "225\$i411\$t":
				$requete="select sub_coll_name from sub_collections where sub_coll_id=".$id;
				$r_subcoll=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_subcoll)) {
					$r=pmb_mysql_result($r_subcoll,0,0);
				}
				break;
			//Auteur
			case "7XX":
				$requete="select concat(author_name,', ',author_rejete) from authors where author_id=".$id;
				$r_author=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_author)) {
					$r=pmb_mysql_result($r_author,0,0);
				}
				break;
		}
		return $r;
	}
	
	public function get_unimarc_search_fields() {
    	$fields=array();
    	//Calcul de la liste des champs disponibles
		$sc=new search(false,"search_fields_unimarc");
		$lf=$sc->get_unimarc_fields();
		$sc=new search(false,"search_simple_fields_unimarc");
		$lfs=$sc->get_unimarc_fields();
		//On fusionne les deux listes
		foreach($lf as $ufield=>$values) {
			if (substr($ufield,0,3)=="id:") {
				$ufield=substr($ufield,3);
			}
			$fields[$ufield]["TITLE"]=$values["TITLE"];
			foreach($values["OPERATORS"] as $op=>$top) {
				$fields[$ufield]["OPERATORS"][$op]=$top;
			}
		}
		foreach($lfs as $ufield=>$values) {
			if (substr($ufield,0,3)=="id:") {
				$ufield=substr($ufield,3);
			}
			if (!$fields[$ufield]["TITLE"])
				$fields[$ufield]["TITLE"]=$values["TITLE"];
			else {
				foreach($values["TITLE"] as $key=>$title) {
					if (array_search($title,$fields[$ufield]["TITLE"])===false) {
						$fields[$ufield]["TITLE"][]=$title;
					}
				}
			}
			foreach($values["OPERATORS"] as $op=>$top) {
				$fields[$ufield]["OPERATORS"][$op]=$top;
			}
		}
		return $fields;
    }
    
    public function enrichment_is_allow(){
    	return false;
    }
    
    protected function delete_from_external_count($source_id, $ref) {
    	global $dbh;
    
    	$requete="delete from external_count where recid='".addslashes($this->get_id()." ".$source_id." ".$ref)."' and source_id = ".$source_id;
    	pmb_mysql_query($requete, $dbh);
    }
    
    protected function is_into_external_count($source_id, $ref) {
    	$rid = 0;
    	$query = "select rid from external_count where source_id=".$source_id." and recid='".addslashes($this->get_id()." ".$source_id." ".$ref)."' limit 1";
    	$result = pmb_mysql_query($query);
    	if($result && pmb_mysql_num_rows($result)) {
    		$rid = pmb_mysql_result($result, 0, 0);
    	}
    	return $rid;
    }
    
    protected function insert_into_external_count($source_id, $ref) {
    	$recid = 0;
    	$query = "insert into external_count (recid, source_id) values('".addslashes($this->get_id()." ".$source_id." ".$ref)."', ".$source_id.")";
    	$rid=pmb_mysql_query($query);
    	if ($rid) $recid=pmb_mysql_insert_id();
    	return $recid;
    }
    
    protected function insert_header_into_entrepot($source_id, $ref, $date_import, $ufield, $value, $recid, $search_id = '') {
    	$query = "insert into entrepot_source_".$source_id." (connector_id,source_id,ref,date_import,ufield,usubfield,field_order,subfield_order,value,i_value,recid, search_id) values(
			'".addslashes($this->get_id())."',".$source_id.",'".addslashes($ref)."','".addslashes($date_import)."',
			'".$ufield."','',-1,0,'".addslashes($value)."','',$recid, '$search_id')";
    	pmb_mysql_query($query);
    }
    
    protected function insert_content_into_entrepot($source_id, $ref, $date_import, $ufield, $usubfield, $field_order, $subfield_order, $value, $recid, $search_id = '') {
        $query = "insert into entrepot_source_".$source_id." (connector_id,source_id,ref,date_import,ufield,usubfield,field_order,subfield_order,value,i_value,recid, search_id) values(
			'".addslashes($this->get_id())."',".$source_id.",'".addslashes($ref)."','".addslashes($date_import)."',
			'".addslashes($ufield)."','".addslashes($usubfield)."',".$field_order.",".$subfield_order.",'".addslashes($value)."',
			' ".addslashes(strip_empty_words($value))." ',$recid, '$search_id')";
    	pmb_mysql_query($query);
    }
    
    protected function insert_content_into_entrepot_multiple($records) {
        $query = "insert into entrepot_source_".$records[0]["source_id"]." (connector_id,source_id,ref,date_import,ufield,usubfield,field_order,subfield_order,value,i_value,recid, search_id) values";
        for ($i=0; $i<count($records);$i++) {
            $record=$records[$i];
            if ($i>0) $query.=","; 
            $query.="(
                            '".addslashes($this->get_id())."',".$record["source_id"].",'".addslashes($record["ref"])."','".addslashes($record["date_import"])."',
                            '".addslashes($record["ufield"])."','".addslashes($record["usubfield"])."',".$record["field_order"].",".$record["subfield_order"].",'".addslashes($record["value"])."',
                            ' ".addslashes(strip_empty_words($record["value"]))." ',".$record["recid"].", '".$record["search_id"]."')";
        }
        pmb_mysql_query($query);
    }
    
	protected function insert_origine_into_entrepot($source_id, $ref, $date_import, $recid, $search_id = '') {
    	$query = "select count(*) from entrepot_source_".$source_id." where ref = '".$ref."' and ufield='801' and usubfield='b'";
    	$result = pmb_mysql_query($query);
    	if($result && !pmb_mysql_result($result, 0, 0)) {
    		$this->insert_content_into_entrepot($source_id, $ref, $date_import, '801', 'a', 0, 0, 'FR', $recid, $search_id);
    		$this->insert_content_into_entrepot($source_id, $ref, $date_import, '801', 'b', 0, 0, $this->get_sources()[$source_id]["NAME"], $recid, $search_id);
    	}
    }
    
    protected function delete_from_entrepot($source_id, $ref, $search_id = '') {
    	$query = "delete from entrepot_source_".$source_id." where ref='".addslashes($ref)."'";
    	if($search_id) {
    		$query .= " and search_id='".addslashes($search_id)."'";
    	}
    	pmb_mysql_query($query);
    }
    
    protected function has_ref($source_id, $ref, $search_id = '') {
    	$query = "select count(*) from entrepot_source_".$source_id." where ref='".addslashes($ref)."'";
    	if($search_id) {
    		$query .= " and search_id='".addslashes($search_id)."'";
    	}
    	$result = pmb_mysql_query($query);
    	if($result) {
    		return pmb_mysql_result($result, 0, 0);
    	}
    	return 0;
    }
    
    public function apply_xsl_to_xml($xml, $xsl) {
    	global $charset;
    	$xh = xslt_create();
    	xslt_set_encoding($xh, $charset);
    	$arguments = array(
    			'/_xml' => $xml,
    			'/_xsl' => $xsl
    	);
    	$result = xslt_process($xh, 'arg:/_xml', 'arg:/_xsl', NULL, $arguments);
    	xslt_free($xh);
    	return $result;
    }
    
	/**
	 * ISBD d'une personne physique
	 */
	protected function get_isbd_physical_author($unimarcKey, $field_order, $subfield_order) {
		$name = static::$ufields[$unimarcKey][$field_order][$subfield_order];
		$rejete = static::$ufields[substr($unimarcKey, 0, 3).'$b'][$field_order][$subfield_order];
		$date = static::$ufields[substr($unimarcKey, 0, 3).'$f'][$field_order][$subfield_order];
		$isbd = '';
		if($rejete) {
			$isbd = $name.", ".$rejete.($date ? " (".$date.")" : "");
		} else {
			$isbd = $name.($date ? " (".$date.")" : "");
		}
		return $isbd;
	}
	
	/**
	 * ISBD d'une collectivité / d'un congrès
	 */
	protected function get_isbd_coll_congres_author($unimarcKey, $field_order, $subfield_order) {
		$name = static::$ufields[$unimarcKey][$field_order][$subfield_order];
		$subdivision = static::$ufields[substr($unimarcKey, 0, 3).'$b'][$field_order][$subfield_order];
		$comment = static::$ufields[substr($unimarcKey, 0, 3).'$c'][$field_order][$subfield_order];
		$numero = static::$ufields[substr($unimarcKey, 0, 3).'$d'][$field_order][$subfield_order];
		$date = static::$ufields[substr($unimarcKey, 0, 3).'$f'][$field_order][$subfield_order];
		$rejete = static::$ufields[substr($unimarcKey, 0, 3).'$g'][$field_order][$subfield_order];
		$lieu = static::$ufields[substr($unimarcKey, 0, 3).'$k'][$field_order][$subfield_order];
		$ville = static::$ufields[substr($unimarcKey, 0, 3).'$l'][$field_order][$subfield_order];
		$pays = static::$ufields[substr($unimarcKey, 0, 3).'$m'][$field_order][$subfield_order];
			
		$isbd = $name;
		if ($rejete) {
			$isbd .= ", " .$rejete;
		}
		$liste_field = $liste_lieu = array();
		if ($subdivision) {
			$liste_field[] = $subdivision;
		}
		if ($numero) {
			$liste_field[] = $numero;
		}
		if ($date) {
			$liste_field[] = $date;
		}
		if ($lieu) {
			$liste_lieu[] = $lieu;
		}
		if ($ville) {
			$liste_lieu[] = $ville;
		}
		if ($pays) {
			$liste_lieu[] = $pays;
		}
		if (count($liste_lieu))
			$liste_field[] = implode(", ", $liste_lieu);
		if (count($liste_field)) {
			$liste_field = implode("; ", $liste_field);
			$isbd .= ' (' .$liste_field .')';
		}
		return $isbd;
	}
	
    public function get_external_isbd($class_name, $type = '') {
    	$external_isbd = array();
    	foreach (static::$ufields as $unimarcKey=>$ufield) {
    		switch ($class_name){
    			case 'author':
    				foreach ($ufield as $field_order=>$subfield) {
    					foreach ($subfield as $subfield_order=>$name) {
    						switch ($type) {
    							case '0':
    								if ($unimarcKey == '700$a') {
    									$external_isbd[$field_order][$subfield_order] = $this->get_isbd_physical_author($unimarcKey, $field_order, $subfield_order);
    								} elseif($unimarcKey == '710$a') {
    									$external_isbd[$field_order][$subfield_order] = $this->get_isbd_coll_congres_author($unimarcKey, $field_order, $subfield_order);
    								}
    								break;
    							case '1':
    								if ($unimarcKey == '701$a') {
    									$external_isbd[$field_order][$subfield_order] = $this->get_isbd_physical_author($unimarcKey, $field_order, $subfield_order);
    								} elseif($unimarcKey == '711$a') {
    									$external_isbd[$field_order][$subfield_order] = $this->get_isbd_coll_congres_author($unimarcKey, $field_order, $subfield_order);
    								}
    								break;
    							case '2':
    								if ($unimarcKey == '702$a') {
    									$external_isbd[$field_order][$subfield_order] = $this->get_isbd_physical_author($unimarcKey, $field_order, $subfield_order);
    								} elseif($unimarcKey == '712$a') {
    									$external_isbd[$field_order][$subfield_order] = $this->get_isbd_coll_congres_author($unimarcKey, $field_order, $subfield_order);
    								}
    								break;
    						}
    					}
    				}
    				break;
    			case 'editeur':
    				if ($unimarcKey == '210$c') {
    					foreach ($ufield as $field_order=>$subfield) {
    						foreach ($subfield as $subfield_order=>$name) {
    							$address = static::$ufields['210$b'][$field_order][$subfield_order];
    							$city = static::$ufields['210$a'][$field_order][$subfield_order];
    
    							// Determine le lieu de publication
    							$l = '';
    							if ($address) $l = $address;
    							if ($city) $l = ($l=='') ? $city : $city.' ('.$l.')';
    							if ($l=='') $l = '[S.l.]';
    							$external_isbd[$field_order][$subfield_order] = $l.'&nbsp;: '.$name;
    						}
    					}
    				}
    				break;
    			case 'indexint':
    				if ($unimarcKey == '676$a' || $unimarcKey == '686$a') {
    					foreach ($ufield as $field_order=>$subfield) {
    						foreach ($subfield as $subfield_order=>$name) {
    							$comment = static::$ufields[substr($unimarcKey, 0, 3).'$l'][$field_order][$subfield_order];
    							if($comment) {
    								$external_isbd[$field_order][$subfield_order] = $name." (".$comment.")";
    							} else {
    								$external_isbd[$field_order][$subfield_order] = $name;
    							}
    						}
    					}
    				}
    				break;
    			case 'collection':
    				if ($unimarcKey == '410$t' || $unimarcKey == '225$a') {
    					foreach ($ufield as $field_order=>$subfield) {
    						foreach ($subfield as $subfield_order=>$name) {
    							if(static::$ufields['410$x'][$field_order][$subfield_order]) {
    								$issn = static::$ufields['410$x'][$field_order][$subfield_order];
    							} else {
    								$issn = static::$ufields['225$x'][$field_order][$subfield_order];
    							}
    							$external_isbd[$field_order][$subfield_order] = $name.($issn ? ', ISSN '.$issn : '');
    						}
    					}
    
    				}
    				break;
    			case 'subcollection':
    				if ($unimarcKey == '411$t' || $unimarcKey == '225$i') {
    					foreach ($ufield as $field_order=>$subfield) {
    						foreach ($subfield as $subfield_order=>$name) {
    							if(static::$ufields['411$x'][$field_order][$subfield_order]) {
    								$issn = static::$ufields['411$x'][$field_order][$subfield_order];
    							} else {
    								$issn = static::$ufields['225$i'][$field_order][$subfield_order];
    							}
    							$external_isbd[$field_order][$subfield_order] = $name.($issn ? ', ISSN '.$issn : '');
    						}
    					}
    				}
    				break;
    			case 'serie':
    				if ($unimarcKey == '461$t' || $unimarcKey == '200$i') {
    					foreach ($ufield as $field_order=>$subfield) {
    						foreach ($subfield as $subfield_order=>$name) {
    							$external_isbd[$field_order][$subfield_order] = $name;
    						}
    					}
    				}
    				break;
    			case 'categories':
    					
    				break;
    			case 'titre_uniforme':
    				if ($unimarcKey == '500$a') {
    					foreach ($ufield as $field_order=>$subfield) {
    						foreach ($subfield as $subfield_order=>$name) {
    							$external_isbd[$field_order][$subfield_order] = $name;
    						}
    					}
    				}
    				break;
    		}
    	}
    	return $external_isbd;
    }
    
    public function rec_isbd_record($source_id, $ref, $recid) {
    	global $include_path;
    	$type = 'notices_externes';
    	if(!isset(self::$xml_indexation[$type])) {
    		$file = $include_path."/indexation/".$type."/champs_base_subst.xml";
    		if(!file_exists($file)){
    			$file = $include_path."/indexation/".$type."/champs_base.xml";
    		}
    		$fp=fopen($file,"r");
    		if ($fp) {
    			$xml=fread($fp,filesize($file));
    		}
    		fclose($fp);
    		self::$xml_indexation[$type] = _parser_text_no_function_($xml,"INDEXATION",$file);
    			
    		for ($i=0;$i<count(self::$xml_indexation[$type]['FIELD']);$i++) { //pour chacun des champs decrits
    			if(self::$xml_indexation[$type]['FIELD'][$i]['ISBD']){ // isbd autorités
    				self::$isbd_ask_list[self::$xml_indexation[$type]['FIELD'][$i]['ID']]= array(
    						'champ' => self::$xml_indexation[$type]['FIELD'][$i]['ID'],
    						'ss_champ' => self::$xml_indexation[$type]['FIELD'][$i]['ISBD'][0]['ID'],
    						'pond' => self::$xml_indexation[$type]['FIELD'][$i]['ISBD'][0]['POND'],
    						'class_name' => self::$xml_indexation[$type]['FIELD'][$i]['ISBD'][0]['CLASS_NAME'],
    						'type' => self::$xml_indexation[$type]['FIELD'][$i]['ISBD'][0]['TYPE']
    				);
    			}
    		}
    	}
    	$query = "select * from entrepot_source_".$source_id." where ref='".addslashes($ref)."'";
    	$result = pmb_mysql_query($query);
    	static::$ufields = array();
    	if($result) {
    		while($row = pmb_mysql_fetch_object($result)) {
    			static::$ufields[$row->ufield.($row->usubfield ? "$".$row->usubfield : "")][$row->field_order][$row->subfield_order] = $row->value;
    		}
    	}
    	foreach(self::$isbd_ask_list as $k=>$infos){
    		$isbd = $this->get_external_isbd($infos['class_name'], $infos['type']);
    		if(count($isbd)) {
    			foreach ($isbd as $field_order=>$authority) {
    				foreach ($authority as $subfield_order=>$value) {
    					$this->insert_content_into_entrepot($source_id, $ref, date("Y-m-d H:i:s",time()), substr($infos['class_name'],0,3), 'i', $field_order, $subfield_order, $value, $recid);
    				}
    			}
    		}
    	}
    }
} 

class connecteurs {
	
	public $catalog=array();			//Liste des connecteurs déclarés
	private static $instance;			//Instance de la classe
	
	//Constructeur
	public function __construct() {
		global $base_path;
		if (file_exists($base_path."/admin/connecteurs/in/catalog_subst.xml")) 
			$catalog=$base_path."/admin/connecteurs/in/catalog_subst.xml";
		else
			$catalog=$base_path."/admin/connecteurs/in/catalog.xml";
		$this->parse_catalog($catalog);
	}
	
	public static function get_class_name($source_id) {
		$connector_id="";
		$requete="select id_connector from connectors_sources where source_id=".$source_id;
		$resultat=pmb_mysql_query($requete);
		if (@pmb_mysql_num_rows($resultat)) {
			$connector_id=pmb_mysql_result($resultat,0,0);
		}
		return $connector_id;
	}
	
	public function parse_catalog($catalog) {
		global $base_path,$lang;
		//Construction du tableau des connecteurs disponbibles
		$xml=file_get_contents($catalog);
		$param=_parser_text_no_function_($xml,"CATALOG",$catalog);
		for ($i=0; $i<count($param["ITEM"]); $i++) {
			$item=$param["ITEM"][$i];
			$t=array();
			$t["PATH"]=$item["PATH"];
			//Parse du manifest du connecteur!
			$xml_manifest=file_get_contents($base_path."/admin/connecteurs/in/".$item["PATH"]."/manifest.xml");
			$manifest=_parser_text_no_function_($xml_manifest,"MANIFEST");
			$t["NAME"]=$manifest["NAME"][0]["value"];
			$t["AUTHOR"]=$manifest["AUTHOR"][0]["value"];
			$t["ORG"]=$manifest["ORG"][0]["value"];
			$t["DATE"]=$manifest["DATE"][0]["value"];
			$t["STATUS"]=$manifest["STATUS"][0]["value"];
			$t["URL"]=$manifest["URL"][0]["value"];
			$t["REPOSITORY"]=$manifest["REPOSITORY"][0]["value"];
			//Commentaires
			$comment=array();
			for ($j=0; $j<count($manifest["COMMENT"]); $j++) {
				if(!isset($manifest["COMMENT"][$j]["lang"])) $manifest["COMMENT"][$j]["lang"] = '';
				if ($manifest["COMMENT"][$j]["lang"]==$lang) { 
					$comment=$manifest["COMMENT"][$j]["value"];
					break;
				} else if (!$manifest["COMMENT"][$j]["lang"]) {
					$c_default=$manifest["COMMENT"][$j]["value"];	
				}
			}
			if ($j==count($manifest["COMMENT"])) $comment=$c_default;
			$t["COMMENT"]=$comment;
			$this->catalog[$item["ID"]]=$t;
		}
	}
	
	public static function get_instance() {
		if(!isset(static::$instance)) {
			static::$instance = new connecteurs();
		}
		return static::$instance;
	}
}
?>