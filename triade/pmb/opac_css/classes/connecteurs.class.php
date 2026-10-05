<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: connecteurs.class.php,v 1.44.2.2.2.6 2025/05/07 14:25:38 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $include_path;
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

    public $n_recu;
    public $n_total;

    public $noticeToEnrich = 0;
    public $enrichPage = 0;
    public $typeOfEnrichment = '';

    public $last_queries = [];
    public $types;

    protected $buffer = [];

    protected static $sources_params = array();

    //Calcul ISBD
    protected static $xml_indexation;
    protected static $isbd_ask_list = array();
    protected static $ufields = array();

    /**
     * Indique que c'est un connecteur avec entrepot
     */
    public const REPOSITORY_YES = 1;

    /**
     * Indique que c'est un connecteur sans entrepot
     */
    public const REPOSITORY_NO = 2;

    /**
     * Indique que c'est un connecteur avec ou sans entrepot
     */
    public const REPOSITORY_CHOOSE = 3;

    /**
     * Indique que l'enrichissement est possible
     */
    public const ENRICHMENT_YES = true;

    /**
     * Indique que l'enrichissement n'est pas possible
     */
    public const ENRICHMENT_NO = false;


    //Variables internes pour la progression de la récupération des notices
    public $callback_progress;		//Nom de la fonction de callback progression passée par l'appellant
    public $source_id;				//Numéro de la source en cours de synchro
    public $del_old;				//Supression ou non des notices dejà existantes

    //Résultat de la synchro
    public $error;					//Y-a-t-il eu une erreur
    public $error_message;			//Si oui, message correspondant
    public $error_msg = [];

    public function __construct($connector_path="")
    {
        $this->fetch_global_properties();
        $this->get_messages($connector_path);
        $this->connector_path=$connector_path;
    }

    /**
     * Renvoie l'identifiant du connecteur
     *
     * @return string
     */
    public function get_id()
    {
        return "";
    }

    /**
     * Est-ce un entrepot ?
     *
     * @return int (1: Oui, 2: Non, 3: Au choix)
     */
    public function is_repository()
    {
        return connector::REPOSITORY_NO;
    }

    public function get_libelle($message) {
        if (substr($message,0,4)=="msg:") return $this->msg[substr($message,4)]; else return $message;
    }


    /**
     * Retourne une liste des champs specifiques interrogeables en recherche externe
     * (tableau vide => tous les champs peuvent etre interroges)
     *
     * Les donnees retournees sont les id et valeurs des attributs unimarcField decrits dans
     * - includes/search_queries/search_fields_unimarc.xml
     * - includes/search_queries/search_simple_fields_unimarc.xml
     *
     * @return string []
     */
    public static function getSpecificUnimarcSearchFields()
    {
        return [];
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

        $file_name = '';
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

    //Recuperation de la liste des sources d'un connecteur
    public function get_sources() {
        if(!isset($this->sources) || !count($this->sources)) {
            $sources=array();
            $requete="select * from connectors_sources where id_connector='".addslashes($this->get_id())."' and opac_allowed=1 order by connectors_sources.name";
            $resultat=pmb_mysql_query($requete);
            if (pmb_mysql_num_rows($resultat)) {
                while ($r=pmb_mysql_fetch_object($resultat)) {
                    $s=array();
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

        $s = [];
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
        
        //suppression des documents numériques intégrés en tant que fichiers
        $this->del_explnums($source_id);
        
        pmb_mysql_query("TRUNCATE TABLE entrepot_source_".$source_id);
        
        pmb_mysql_query("DELETE FROM external_count WHERE source_id = '".$source_id."'");
        
        pmb_mysql_query("delete from source_sync where source_id=".$source_id);
        
        return true;
    }

    //Suppression des documents numériques intégrés en tant que fichiers
    public function del_explnums($source_id) {
        $q = "select value as file_name from entrepot_source_$source_id where ufield='897' and usubfield='a' and value like '/%' ";
        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            while ($row = pmb_mysql_fetch_object($r)) {
                @unlink($row->file_name);
            }
        }
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

    /**
     * Recuperation du formulaire de synchronisation
     *
     * @param int $source_id : id source
     * @param string $form : nom formulaire
     * @return string
     */
    public function getSynchroForm($source_id, $sync_form = "sync_form")
    {
        $source_id = intval($source_id);
        $sync_form = in_array($sync_form, [ "sync_form", "planificateur_form"]) ? $sync_form : "sync_form";

        return "";
    }

    /**
     * Recuperation du formulaire de synchronisation pour les taches planifiees
     *
     * @param int $source_id
     * @param array $taskParams : parametres requis pour la tache planifiee
     * @return string
     */
    public function getScheduledTaskSynchroForm($source_id, $taskParams = [])
    {
        return $this->getSynchroForm($source_id, "planificateur_form");
    }

    /**
     * Recuperation de l'environnement de synchronisation pour les taches planifiees
     *
     * @param int $source_id
     * @param array $taskParams : parametres requis pour la tache planifiee
     * @return array
     */
    public function getScheduledTaskEnvironment($source_id, $taskParams = [])
    {
        return $this->get_maj_environnement($source_id);
    }

    /**
     * Methode pour passer les valeurs obtenues dans getSynchroForm au javascript asynchrone
     *
     * @param int $source_id
     * @return array
     */
    public function get_maj_environnement($source_id) {
        return array();
    }

    /**
     * Permet de verifier les donnees passees dans l'environnement
     *
     * @param int $source_id
     * @param array $env
     * @return array
     */
    public function check_environnement($source_id, $env) {
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
            case "214\$c":
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
            if (empty($fields[$ufield]["TITLE"])) {
                $fields[$ufield]["TITLE"] = $values["TITLE"];
            } else {
                foreach($values["TITLE"] as $title) {
                    if (array_search($title,$fields[$ufield]["TITLE"])===false) {
                        $fields[$ufield]["TITLE"][] = $title;
                    }
                }
            }
            foreach($values["OPERATORS"] as $op=>$top) {
                $fields[$ufield]["OPERATORS"][$op]=$top;
            }
        }
        return $fields;
    }

    /**
     * Indique si l'enrichissement est possible
     *
     * @return bool
     */
    public function enrichment_is_allow()
    {
        return connector::ENRICHMENT_NO;
    }

    protected function delete_from_external_count($source_id, $ref) {
        $requete="delete from external_count where recid='".addslashes($this->get_id()." ".$source_id." ".$ref)."' and source_id = ".$source_id;
        pmb_mysql_query($requete);
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
        $query = "insert ignore into entrepot_source_".$source_id." (connector_id,source_id,ref,date_import,ufield,usubfield,field_order,subfield_order,value,i_value,recid, search_id) values(
            '".addslashes($this->get_id())."',".$source_id.",'".addslashes($ref)."','".addslashes($date_import)."',
            '".$ufield."','',0,0,'".addslashes($value)."','',$recid, '$search_id')";
        pmb_mysql_query($query);
    }

    protected function insert_content_into_entrepot($source_id, $ref, $date_import, $ufield, $usubfield, $field_order, $subfield_order, $value, $recid, $search_id = '') {
        $query = "insert ignore into entrepot_source_".$source_id." (connector_id,source_id,ref,date_import,ufield,usubfield,field_order,subfield_order,value,i_value,recid, search_id) values(
            '".addslashes($this->get_id())."',".$source_id.",'".addslashes($ref)."','".addslashes($date_import)."',
            '".addslashes($ufield)."','".addslashes($usubfield)."',".$field_order.",".$subfield_order.",'".addslashes($value)."',
            ' ".addslashes(strip_empty_words($value))." ',$recid, '$search_id')";
        pmb_mysql_query($query);
    }

    protected function update_content_into_entrepot($source_id, $ref, $ufield, $usubfield, $field_order, $subfield_order, $search_id = '', $value = '') {
        $query = "update ignore entrepot_source_".$source_id." ";
        $query.= "set value = '".addslashes($value)."', i_value = '".addslashes(strip_empty_words($value))."' ";
        $query.= "where source_id = {$source_id} ";
        $query.= "and ref ='".addslashes($ref)."' ";
        $query.= "and ufield = '".addslashes($ufield)."' ";
        $query.= "and usubfield = '".addslashes($usubfield)."' ";
        $query.= "and field_order = {$field_order} ";
        $query.= "and subfield_order = {$subfield_order} ";
        $query.= "and search_id = '{$search_id}' ";
        $query.= "limit 1";
        pmb_mysql_query($query);
    }

    protected function insert_content_into_entrepot_multiple($records) {
        $query = "insert ignore into entrepot_source_".$records[0]["source_id"]." (connector_id,source_id,ref,date_import,ufield,usubfield,field_order,subfield_order,value,i_value,recid, search_id) values";
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
        $this->insert_content_into_entrepot($source_id, $ref, $date_import, '801', 'a', 0, 0, 'FR', $recid, $search_id);
        $this->insert_content_into_entrepot($source_id, $ref, $date_import, '801', 'b', 0, 0, $this->get_sources()[$source_id]["NAME"], $recid, $search_id);
    }

    protected function insert_human_query_into_entrepot($source_id, $ref, $date_import, $value, $recid, $search_id = '') {
        $query = "insert ignore into entrepot_source_".$source_id." (connector_id,source_id,ref,date_import,ufield,usubfield,field_order,subfield_order,value,i_value,recid, search_id) values(
            '".addslashes($this->get_id())."',".$source_id.",'".addslashes($ref)."','".addslashes($date_import)."',
            'hum','',0,0,'".addslashes($value)."',' ".addslashes(strip_empty_words($value))." ',$recid, '$search_id')";
        pmb_mysql_query($query);
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

    protected function get_ref($source_id, $ref, $search_id='') {
        $query = "select * from entrepot_source_".$source_id." where ref='".addslashes($ref)."'";
        if($search_id) {
            $query .= " and search_id='".addslashes($search_id)."'";
        }
        $query.= ' order by ufield, usubfield, field_order, subfield_order asc';
        $result = pmb_mysql_query($query);
        if(!pmb_mysql_num_rows($result)) {
            return [];
        }
        $ret = [];

        while( $row = pmb_mysql_fetch_assoc($result)) {
            if($row['usubfield'] ==='') {
                $ret[$row['ufield']] = $row;
            } else {
                $ret[$row['ufield']][$row['usubfield']][] = $row;
            }
        }
        return $ret;
    }

    public function apply_xsl_to_xml($xml, $xsl) {
        global $charset;

        $result = null;
        if (!empty($xml) && !empty($xsl)) {
            $xh = xslt_create();
            xslt_set_encoding($xh, $charset);
            $arguments = ['/_xml' => $xml, '/_xsl' => $xsl];
            $result = xslt_process($xh, 'arg:/_xml', 'arg:/_xsl', null, $arguments);
            xslt_free($xh);
        }
        return $result;
    }

    /**
     * ISBD d'une personne physique
     */
    protected function get_isbd_physical_author($unimarcKey, $field_order, $subfield_order) {
        $name = static::$ufields[$unimarcKey][$field_order][$subfield_order];
        if(isset(static::$ufields[substr($unimarcKey, 0, 3).'$b'][$field_order][$subfield_order])) {
            $rejete = static::$ufields[substr($unimarcKey, 0, 3).'$b'][$field_order][$subfield_order];
        } else {
            $rejete = '';
        }
        if(isset(static::$ufields[substr($unimarcKey, 0, 3).'$f'][$field_order][$subfield_order])) {
            $date = static::$ufields[substr($unimarcKey, 0, 3).'$f'][$field_order][$subfield_order];
        } else {
            $date = '';
        }
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
        if(!empty(static::$ufields[substr($unimarcKey, 0, 3).'$b'][$field_order][$subfield_order])) {
            $subdivision = static::$ufields[substr($unimarcKey, 0, 3).'$b'][$field_order][$subfield_order];
        } else {
            $subdivision = '';
        }
        if(!empty(static::$ufields[substr($unimarcKey, 0, 3).'$d'][$field_order][$subfield_order])) {
            $numero = static::$ufields[substr($unimarcKey, 0, 3).'$d'][$field_order][$subfield_order];
        } else {
            $numero = '';
        }
        if(!empty(static::$ufields[substr($unimarcKey, 0, 3).'$f'][$field_order][$subfield_order])) {
            $date = static::$ufields[substr($unimarcKey, 0, 3).'$f'][$field_order][$subfield_order];
        } else {
            $date = '';
        }
        if(!empty(static::$ufields[substr($unimarcKey, 0, 3).'$g'][$field_order][$subfield_order])) {
            $rejete = static::$ufields[substr($unimarcKey, 0, 3).'$g'][$field_order][$subfield_order];
        } else {
            $rejete = '';
        }
        if(!empty(static::$ufields[substr($unimarcKey, 0, 3).'$k'][$field_order][$subfield_order])) {
            $lieu = static::$ufields[substr($unimarcKey, 0, 3).'$k'][$field_order][$subfield_order];
        } else {
            $lieu = '';
        }
        if(!empty(static::$ufields[substr($unimarcKey, 0, 3).'$l'][$field_order][$subfield_order])) {
            $ville = static::$ufields[substr($unimarcKey, 0, 3).'$l'][$field_order][$subfield_order];
        } else {
            $ville = '';
        }
        if(!empty(static::$ufields[substr($unimarcKey, 0, 3).'$m'][$field_order][$subfield_order])) {
            $pays = static::$ufields[substr($unimarcKey, 0, 3).'$m'][$field_order][$subfield_order];
        } else {
            $pays = '';
        }
        
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
                                $address = '';
                                if(isset(static::$ufields['210$b'][$field_order][$subfield_order])) {
                                    $address = static::$ufields['210$b'][$field_order][$subfield_order];
                                }
                                $city = '';
                                if(isset(static::$ufields['210$a'][$field_order][$subfield_order])) {
                                    $city = static::$ufields['210$a'][$field_order][$subfield_order];
                                }
                                // Determine le lieu de publication
                                $l = '';
                                if ($address) $l = $address;
                                if ($city) $l = ($l=='') ? $city : $city.' ('.$l.')';
                                if ($l=='') $l = '[S.l.]';
                                $external_isbd[$field_order][$subfield_order] = $l.' : '.$name;
                            }
                        }
                    }
                    break;
                case 'indexint':
                    if ($unimarcKey == '676$a' || $unimarcKey == '686$a') {
                        foreach ($ufield as $field_order=>$subfield) {
                            foreach ($subfield as $subfield_order=>$name) {
                                $current_ufield = static::$ufields[substr($unimarcKey, 0, 3).'$l'] ?? null;
                                if(!$current_ufield) {
                                    $external_isbd[$field_order][$subfield_order] = $name;
                                    continue;
                                }

                                $comment = $current_ufield[$field_order][$subfield_order];
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
                                $issn = '';
                                if ( isset(static::$ufields['410$x'][$field_order][$subfield_order]) ) {
                                    $issn = static::$ufields['410$x'][$field_order][$subfield_order];
                                } elseif ( isset(static::$ufields['225$x'][$field_order][$subfield_order]) ) {
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
                                $issn = '';
                                if( isset(static::$ufields['411$x'][$field_order][$subfield_order]) ) {
                                    $issn = static::$ufields['411$x'][$field_order][$subfield_order];
                                } elseif ( isset(static::$ufields['225$i'][$field_order][$subfield_order]) ) {
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

        $this->get_xml_indexation();
        $query = "select * from entrepot_source_".$source_id." where ref='".addslashes($ref)."'";
        $result = pmb_mysql_query($query);
        static::$ufields = array();
        if($result) {
            while($row = pmb_mysql_fetch_object($result)) {
                static::$ufields[$row->ufield.($row->usubfield ? "$".$row->usubfield : "")][$row->field_order][$row->subfield_order] = $row->value;
            }
        }
        foreach(self::$isbd_ask_list as $infos){
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


    /**
     * Insertion de lots d'enregistrements avec header,content,origine,isbd
     *
     * @param array $records = [
     * 	[search_id] => 0ed5b1b97e7d91f2baa7d9372210f421,
     * 	[source_id] => 11,
     * 	[date_import] => 2020-09-16 08:25:12,
     * 	[records] => [
     * 		[$ref] => [
     * 			[header] => [
     * 				[rs] => *
     * 				[ru] => *
     * 				[el] => *
     * 				[bl] => a
     * 				[hl] => 2
     * 				[dt] => a
     * 			],
     * 			[content] => [
     * 				[0] => [
     * 					[ufield] => 001
     * 					[usubfield] =>
     * 					[value] => S2352250X20301147
     * 					[field_order] => 0
     * 					[subfield_order] => 0
     * 				],
     * 				[1] => [
     * 					...
     * 				],
     * 			[recid] => 103055
     * 		],
     *
     * 	]
     *
     */
    protected function insert_records_into_entrepot($records) {

        $q = "insert ignore into entrepot_source_".$records['source_id']." ";
        $q.= "(connector_id,source_id,ref,date_import,ufield,usubfield,field_order,subfield_order,value,i_value,recid, search_id) ";
        $q.= "values ";
        $i = 0;

        //ISBD
        $this->get_xml_indexation();

        $addslashed_date_import = addslashes($records['date_import']);

        foreach($records['records'] as $ref=>$record) {

            $addslashed_ref = addslashes($ref);
            $addslashed_id = addslashes($this->get_id());

            //header
            foreach($record['header'] as $ufield=>$v) {
                if($i) {
                    $q.= ", ";
                }
                $q.= "('".$addslashed_id."', ".$records['source_id'].", '".$addslashed_ref."', '".$addslashed_date_import."', ";
                $q.= "'".$ufield."', '', 0, 0, '".addslashes($v)."', '', ".$record['recid'].", '".$records['search_id']."')";
                $i = 1;

                //prepare isbd
                $records['records'][$ref]['isbd'][$ufield][0][0] = $v;

            }

            //content
            foreach($record['content'] as $v) {
                $q.= ", ";
                $q.= "('".$addslashed_id."', ".$records['source_id'].", '".$addslashed_ref."', '".$addslashed_date_import."', ";
                $q.= "'".$v['ufield']."', '".$v['usubfield']."', ".$v['field_order'].", ".$v['subfield_order'].", ";
                $q.= "'".addslashes($v['value'])."', ' ".addslashes(strip_empty_words($v["value"]))." ', ".$record['recid'].", '".$records['search_id']."')";

                //prepare isbd
                $records['records'][$ref]['isbd'][$v['ufield'].($v['usubfield'] ? '$'.$v['usubfield'] : '')][$v['field_order']][$v['subfield_order']] = $v["value"];
            }

            //origine
            $q.= ", ('".$addslashed_id."', ".$records['source_id'].", '".$addslashed_ref."', '".$addslashed_date_import."', ";
            $q.= "'801', 'a', 0, 0, 'FR', '', ".$record['recid'].", '".$records['search_id']."')";
            $q.= ", ('".$addslashed_id."', ".$records['source_id'].", '".$addslashed_ref."', '".$addslashed_date_import."', ";
            $q.= "'801', 'b', 0, 0, '".addslashes($this->get_sources()[$records['source_id']]["NAME"])."', '', ".$record['recid'].", '".$records['search_id']."')";

            //ISBD
            static::$ufields = $records['records'][$ref]['isbd'];
            foreach(static::$isbd_ask_list as $infos){
                $isbd = $this->get_external_isbd($infos['class_name'], $infos['type']);
                if(!empty($isbd)) {
                    foreach ($isbd as $field_order=>$authority) {
                        foreach ($authority as $subfield_order=>$value) {
                            $q.= ", ";
                            $q.= "('".$addslashed_id."', ".$records['source_id'].", '".$addslashed_ref."', '".$addslashed_date_import."', ";
                            $q.= "'".substr($infos['class_name'],0,3)."', 'i', ".$field_order.", ".$subfield_order.", ";
                            $q.= "'".addslashes($value)."', ' ".addslashes(strip_empty_words($value))." ', ".$record['recid'].", '".$records['search_id']."')";
                        }
                    }
                }
            }
        }
        pmb_mysql_query($q);

    }


    protected function get_xml_indexation() {

        global $include_path;
        $type = 'notices_externes';
        if(!isset(static::$xml_indexation[$type])) {
            $file = $include_path."/indexation/".$type."/champs_base_subst.xml";
            if(!file_exists($file)){
                $file = $include_path."/indexation/".$type."/champs_base.xml";
            }
            $fp=fopen($file,"r");
            if ($fp) {
                $xml=fread($fp,filesize($file));
            }
            fclose($fp);
            static::$xml_indexation[$type] = _parser_text_no_function_($xml,"INDEXATION",$file);

            for ($i=0;$i<count(static::$xml_indexation[$type]['FIELD']);$i++) { //pour chacun des champs decrits
                if(isset(static::$xml_indexation[$type]['FIELD'][$i]['ISBD']) && static::$xml_indexation[$type]['FIELD'][$i]['ISBD']){ // isbd autorités
                    static::$isbd_ask_list[static::$xml_indexation[$type]['FIELD'][$i]['ID']]= array(
                            'champ' => static::$xml_indexation[$type]['FIELD'][$i]['ID'],
                            'ss_champ' => static::$xml_indexation[$type]['FIELD'][$i]['ISBD'][0]['ID'],
                            'pond' => (isset(static::$xml_indexation[$type]['FIELD'][$i]['ISBD'][0]['POND']) ? static::$xml_indexation[$type]['FIELD'][$i]['ISBD'][0]['POND'] : ''),
                            'class_name' => static::$xml_indexation[$type]['FIELD'][$i]['ISBD'][0]['CLASS_NAME'],
                            'type' => (isset(static::$xml_indexation[$type]['FIELD'][$i]['ISBD'][0]['TYPE']) ? static::$xml_indexation[$type]['FIELD'][$i]['ISBD'][0]['TYPE'] : '')
                    );
                }
            }
        }
    }

    /**
     * Liste les appels de fonctions autorisés en ajax
     * @return array
     */
    public function get_ajax_allowed_methods() {
        return [];
    }

    /**
     * Retourne la configuration d'une source d'authentification (connecteur sortant) a partir de son id
     *
     * @param int $id
     * @return array
     */
    protected static function get_authentication_source_by_id($id) {
        $source = [];
        $id = intval($id);
        $q = "SELECT connectors_out_source_config FROM `connectors_out_sources` where connectors_out_source_id=".$id;
        $r = pmb_mysql_query($q);
        if(pmb_mysql_num_rows($r)) {
            $source = unserialize(pmb_mysql_result($r, 0, 0));
        }
        return $source;
    }
}

class connecteurs {

    /* Liste des connecteurs declares */
    public $catalog=array();

    /* Instance de la classe */
    private static $instance;

    /* Tableau liste des sources */
    protected static $source_list = null;

    /* Tableau liste des sources dans lesquelles on peut chercher */
    protected static $searchable_source_list = null;


    public function __construct()
    {
        global $base_path;
        if (file_exists($base_path . "/admin/connecteurs/in/catalog_subst.xml")) {
            $catalog = $base_path . "/admin/connecteurs/in/catalog_subst.xml";
        } else {
            $catalog = $base_path . "/admin/connecteurs/in/catalog.xml";
        }
        $this->parse_catalog($catalog);
    }


    public static function get_instance() {
        if(!isset(static::$instance)) {
            static::$instance = new connecteurs();
        }
        return static::$instance;
    }

    public static function get_class_name($source_id) {

        $source_id = intval($source_id);
        if( !$source_id) {
            return '';
        }

        $connector_id="";
        $requete="select id_connector from connectors_sources where source_id=".$source_id;
        $resultat=pmb_mysql_query($requete);
        if (@pmb_mysql_num_rows($resultat)) {
            $connector_id=pmb_mysql_result($resultat,0,0);
        }
        return $connector_id;
    }

    /**
     * Construction du tableau des connecteurs disponibles
     *
     * @param string $catalog : fichier catalog
     * @return void
     */
    public function parse_catalog($catalog)
    {
        global $base_path, $charset;

        if (!file_exists($catalog)) {
            trigger_error('File not found : ' . $catalog, E_USER_ERROR);
            return;
        }

        /**
         * @var cache_apcu|false $cache_php
         */
        $cache_php = cache_factory::getCache();
        $tmp = $base_path . '/temp/XML' . preg_replace("/[^a-z0-9]/i", "", $catalog . $charset) . '.tmp';

        $already_parse = false;
        if ($cache_php) {
            global $KEY_CACHE_FILE_XML;
            $key_file = $catalog.filemtime($catalog);
            $key_file = $KEY_CACHE_FILE_XML . md5($key_file);

            $cache = $cache_php->getFromCache($key_file);
            if ($cache) {
                $already_parse = true;
            }
        } else if (file_exists($tmp) && filemtime($tmp) > filemtime($catalog)) {
            $resource = fopen($tmp, "r");
            $cache = json_decode(fread($resource, filesize($tmp)), true);
            fclose($resource);

            if (!empty($cache) && is_array($cache)) {
                $already_parse = true;
            }
        } else if (file_exists($tmp)) {
            // le fichier de cache est trop ancien, on le supprime
            unlink($tmp);
        }

        if ($already_parse) {
            $this->catalog = $cache;
        } else {
            $this->parse_catalog_xml($catalog);
            if ($cache_php) {
                $cache_php->setInCache($key_file, $this->catalog);
            } else {
                file_put_contents($tmp, json_encode($this->catalog));
            }
        }
    }

    /**
     * Traitement d'un fichier XML et
     *
     * @param string $catalog
     * @return void
     */
    protected function parse_catalog_xml($catalog) {
        global $base_path, $lang, $charset;
        global $KEY_CACHE_FILE_XML;

        if (!file_exists($catalog)) {
            trigger_error('File not found : ' . $catalog, E_USER_ERROR);
            return;
        }

        /**
         * @var cache_apcu|false $cache_php
         */
        $cache_php = cache_factory::getCache();

        // Construction du tableau des connecteurs disponbibles
        $xml = file_get_contents($catalog);
        $param = _parser_text_no_function_($xml, "CATALOG", $catalog);

        for ($i = 0; $i < count($param["ITEM"]); $i++) {
            $item = $param["ITEM"][$i];

            $t = array();
            $t["PATH"] = $item["PATH"];

            $already_parse = false;
            $manifest_path = $base_path . "/admin/connecteurs/in/" . $item["PATH"] . "/manifest.xml";
            $tmp = $base_path . '/temp/XML' . preg_replace("/[^a-z0-9]/i", "", $manifest_path . $charset) . '.tmp';
            if ($cache_php) {
                $key_file = $manifest_path . filemtime($manifest_path);
                $key_file = $KEY_CACHE_FILE_XML . md5($key_file);

                $cache = $cache_php->getFromCache($key_file);
                if ($cache) {
                    $already_parse = true;
                }
            } else if (file_exists($tmp) && filemtime($tmp) > filemtime($manifest_path)) {
                $resource = fopen($tmp, "r");
                $cache = json_decode(fread($resource, filesize($tmp)), true);
                fclose($resource);

                if (!empty($cache) && is_array($cache)) {
                    $already_parse = true;
                }
            } else if (file_exists($tmp)) {
                // le fichier de cache est trop ancien, on le supprime
                unlink($tmp);
            }

            if ($already_parse && !empty($cache)) {
                $manifest = $cache;
            } else {
                // Parse du manifest du connecteur!
                $xml_manifest = file_get_contents($manifest_path);
                $manifest = _parser_text_no_function_($xml_manifest,"MANIFEST");
                if ($cache_php){
                    $cache_php->setInCache($key_file, $manifest);
                } else {
                    file_put_contents($tmp, json_encode($manifest));
                }
            }

            $t["NAME"] = $manifest["NAME"][0]["value"];
            $t["AUTHOR"] = $manifest["AUTHOR"][0]["value"];
            $t["ORG"] = $manifest["ORG"][0]["value"];
            $t["DATE"] = $manifest["DATE"][0]["value"];
            $t["STATUS"] = $manifest["STATUS"][0]["value"];
            $t["URL"] = $manifest["URL"][0]["value"];
            $t["REPOSITORY"] = $manifest["REPOSITORY"][0]["value"];

            $t["SEARCH"] = 'no';
            if (isset($manifest["SEARCH"]) && isset($manifest["SEARCH"][0]["value"])) {
                $t["SEARCH"] = ('yes' == $manifest["SEARCH"][0]["value"]) ? 'yes' : 'no';
            }

            $t["ENRICHMENT"] = 'no';
            if (isset($manifest["ENRICHMENT"]) && isset($manifest["ENRICHMENT"][0]["value"])) {
                $t["ENRICHMENT"] = ('yes' == $manifest["ENRICHMENT"][0]["value"]) ? 'yes' : 'no';
            }

            // Commentaires
            $comment = array();
            for ($j = 0; $j < count($manifest["COMMENT"]); $j++) {
                if (!isset($manifest["COMMENT"][$j]["lang"])) {
                    $manifest["COMMENT"][$j]["lang"] = '';
                }

                if ($manifest["COMMENT"][$j]["lang"] == $lang) {
                    $comment = $manifest["COMMENT"][$j]["value"];
                    break;
                } else if (!$manifest["COMMENT"][$j]["lang"]) {
                    $c_default = $manifest["COMMENT"][$j]["value"];
                }
            }

            if ($j == count($manifest["COMMENT"])) {
                $comment = $c_default;
            }

            $t["COMMENT"] = $comment;
            $this->catalog[$item["ID"]] = $t;
        }
    }

    /**
     * Recupere la liste des sources
     *
     * @return []
     */
    public function getSourceList()
    {
        if (!is_null(static::$source_list)) {
            return static::$source_list;
        }
        static::$source_list = [];
        $q = "SELECT * FROM connectors_sources";
        $r = pmb_mysql_query($q);
        if (!pmb_mysql_num_rows($r)) {
            return static::$source_list;
        }
        while ($row = pmb_mysql_fetch_assoc($r)) {
            static::$source_list[$row['source_id']] = $row;
        }
        return static::$source_list;
    }


    /**
     * Recupere la liste des sources dans lesquelles on peut chercher
     *
     * @return []
     */
    public function getSearchableSourceList()
    {
        if (!is_null(static::$searchable_source_list)) {
            return static::$searchable_source_list;
        }
        static::$searchable_source_list = [];

        // Parcours du catalogue des connecteurs pour trouver ceux dans lesquels on peut chercher
        $searchable_connectors = [];
        foreach($this->catalog as $k => $connector) {
            if ( ('yes' == $connector['SEARCH']) || ('yes' == $connector['REPOSITORY']) ) {
                $searchable_connectors[] = $connector['PATH'];
            }
        }

        //Parcours des sources
        $sources = $this->getSourceList();
        foreach($sources as $source) {
            if( in_array($source['id_connector'], $searchable_connectors) ) {
                static::$searchable_source_list[$source['source_id']] = $source;
            }
        }
        return static::$searchable_source_list;
    }

}
