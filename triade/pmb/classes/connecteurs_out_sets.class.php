<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: connecteurs_out_sets.class.php,v 1.29.2.3.2.3 2025/03/12 14:41:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//There be komodo dragons

/*
 ====================================================================================================================================
 Comment ça marche toutes ces classes ?

            .------------------------.           .------------------------.       .-------------------------.
            |   connector_out_sets   |           | connector_out_setcateg |       | connector_out_setcategs |
            |------------------------|           |------------------------|[all]  |-------------------------|
            | contient tous les sets |   .-------| contient un certain    |<------| contient toutes les     |
            '------------------------'   |       | nombre de sets         |       | catégories              |
                         |               |       '------------------------'       '-------------------------'
                         |               |
                   [all] v               v [0..all]
     .--------------------------------------.
     |          connector_out_set           |
     |--------------------------------------|
     | gère un set d'élements dans un cache |<-----------------------------------------------------------------------------
     | mis à jour régulièrement             | Héritent de                                                                 ^
     '--------------------------------------'                                                                             |
                         |                                                                                                |
                         |                                       .---------------------------------------------.          |
             contient un v                                       |       connector_out_set_noticecaddie        |          |
           .--------------------------.                          |---------------------------------------------|          |
           |  connector_out_setcache  |          .-------------->| gère un set contenant les notices contenues |--------->|
           |--------------------------|          |               | dans des paniers de notices                 |          |
           | gère un cache de valeurs |          |               '---------------------------------------------'          |
           '--------------------------'          |                                                                        |
               hérite de ^                       |               .---------------------------------------------.          |
                         |                       |               |        connector_out_set_explcaddie         |          |
                         |                       |               |---------------------------------------------|          |
         .-------------------------------.       |-------------->| gère un set contenant les notices contenues |--------->|
         | connector_out_setcache_notice |       |               | dans des paniers d'exemplaires              |          |
         |-------------------------------|       |               '---------------------------------------------'          |
         | gère un cache d'une           |       |                                                                        |
         | liste de notices              |       |               .------------------------------------------------.       |
         '-------------------------------'       |               |          connector_out_set_emprcaddie          |       |
                                                 |               |------------------------------------------------|       |
                                                 |-------------->| gère un set contenant les emprunteurs contenus |------>|
                                                 |               | dans des paniers d'emprunteurs                 |       |
                                                 |               '------------------------------------------------'       |
                                                 |                                                                        |
 .--------------------------------------.        |               .---------------------------------------------------.    |
 | function:new_connector_out_set_typed |        |               |       connector_out_set_noticemulticritere        |    |
 .--------------------------------------.        |               |---------------------------------------------------|    |
 | instantie la bonne classe d'un       |----------------------->| gère un set contenant les notices contenues       |----'
 | set selon son id                     |  instantie au          | dans les résultats d'une recherche multi-critères |
 .--------------------------------------.  choix                 '---------------------------------------------------'
                                                                                                          |
                                                                  .--------------------------------.      |
                                                                  | external_services_searchcache  |      |
                                                                  |--------------------------------|      |
                                                                  | effectue des recherches et les |<-----'
                                                                  |. met en cache                  | utilise
                                                                  .--------------------------------.


 ====================================================================================================================================
 */
global $class_path, $include_path;

require_once ($class_path."/caddie.class.php");
require_once ($class_path."/empr_caddie.class.php");
require_once ($class_path."/search.class.php");
require_once ($class_path."/external_services_searchcache.class.php");
require_once ($class_path."/search_perso.class.php");
require_once ($class_path."/equation.class.php");
require_once($include_path."/connecteurs_out_common.inc.php");

$connector_out_set_types = array(
    1, //Paniers de notices
    2,  //Recherche multi-critères de notices
    3,  //Paniers d'exemplaires
    4  //Paniers de lecteurs
);

$connector_out_set_types_msgs = array(
    1 => "connector_out_set_types_msg_1",
    2 => "connector_out_set_types_msg_2",
    3 => "connector_out_set_types_msg_3",
    4 => "connector_out_set_types_msg_4"
);

$connector_out_set_types_classes = array(
    1 => "connector_out_set_noticecaddie",
    2 => "connector_out_set_noticemulticritere",
    3 => "connector_out_set_explcaddie",
    4 => "connector_out_set_emprcaddie"
);

class connector_out_set
{
    public $id=0;
    public $caption="";
    public $type=0;
    public $config=array();
    public $cache=NULL;
    public $error="";
    protected static $already_included_sets = array();

    public function __construct($id=0, $nocache=false) {
        //nocache permet de ne pas instancier le cache, si une classe fille veut le faire d'elle même
        $id=intval($id);
        $sql = "SELECT * FROM connectors_out_sets WHERE connector_out_set_id = ".$id;
        $row = pmb_mysql_fetch_assoc(pmb_mysql_query($sql));
        $this->id = $row["connector_out_set_id"];
        $this->caption = $row["connector_out_set_caption"];
        $this->type = $row["connector_out_set_type"];
        $this->config = unserialize($row["connector_out_set_config"]);
        $this->config = stripslashes_array($this->config);
        if (!$nocache)
            $this->cache = new connector_out_setcache($id);
    }

    public function get_form() {
        global $msg, $charset;
        global $connector_out_set_types, $connector_out_set_types_msgs, $out_of_form_result;

        if($this->id) {
            $config_form=array($this, 'get_config_form');
            $cache_config_form=array($this->cache, 'get_config_form');
        } else {
            $config_form=NULL;
            $cache_config_form=NULL;
        }
        //caption
        $content_form = '<div class=row><label class="etiquette" for="set_caption">'.$msg["admin_connecteurs_sets_setcaption"].'</label><br />';
        $content_form .= '<input name="set_caption" type="text" value="'.htmlentities($this->caption ?? "", ENT_QUOTES, $charset).'" class="saisie-80em">
			</div>';

        //type
        if (!$this->id) {
            $type_input = '<select name="set_type">';
            foreach ($connector_out_set_types as $aconnector_out_set_type) {
                $type_input .= '<option '.($aconnector_out_set_type==$this->type ? ' selected ' : "").' value="'.$aconnector_out_set_type.'">'.htmlentities($msg[$connector_out_set_types_msgs[$aconnector_out_set_type]] ,ENT_QUOTES, $charset).'</option>';
            }
            $type_input .= '</select>';
        }
        else {
            $type_input = htmlentities($msg[$connector_out_set_types_msgs[$this->type]] ,ENT_QUOTES, $charset);
            $type_input .= '<input type="hidden" name="set_type" value="'.$this->type.'">';
        }
        $content_form .= '<div class=row><label class="etiquette" for="set_type">'.$msg["admin_connecteurs_sets_settype"].'</label><br />';
        $content_form .= $type_input;
        $content_form .= '</div>';

        if ($config_form) {
            $content_form .= '<div class=row>';
            $content_form .= call_user_func_array($config_form, array(&$out_of_form_result));
            $content_form .= '</div>';
        }

        if ($cache_config_form) {
            $content_form .= '<div class=row>';
            $content_form .= call_user_func($cache_config_form);
            $content_form .= '</div>';
        }

        $interface_form = new interface_admin_form('form_outset');
        if(!$this->id){
            $interface_form->set_label($msg['admin_connecteurs_set_add']);
        }else{
            $interface_form->set_label($msg['admin_connecteurs_set_edit']);
        }

        $interface_form->set_object_id($this->id)
        ->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->caption." ?")
        ->set_content_form($content_form)
        ->set_table_name('connectors_out_sets')
        ->set_field_focus('set_caption');
        $display = $interface_form->get_display();
        if (!empty($out_of_form_result)) {
            //Formulaire caché à placer en dehors du formulaire principal
            $display .= $out_of_form_result;
        }
        return $display;
    }

    public function set_properties_from_form() {
        global $set_type, $set_caption;

        $this->type = $set_type;
        $this->caption = stripslashes($set_caption);
    }

    public function get_query_if_exists() {
        return " SELECT count(1) FROM connectors_out_sets WHERE (connector_out_set_caption='".addslashes($this->caption)."' AND connector_out_set_id!='".$this->id."' )";
    }

    public static function caption_exists($caption) {
        $sql = "SELECT connector_out_set_id FROM connectors_out_sets WHERE connector_out_set_caption = '".addslashes($caption)."'";
        $res = pmb_mysql_query($sql);
        return pmb_mysql_num_rows($res) > 0 ? pmb_mysql_result($res, 0, 0) : 0;
    }

    public static function add_new() {
        $sql = "INSERT INTO connectors_out_sets () VALUES ()";
        pmb_mysql_query($sql);
        $new_set_id = pmb_mysql_insert_id();
        connector_out_setcache::add($new_set_id);
        return new connector_out_set($new_set_id);
    }

    public function commit_to_db() {
        //on oublie pas que includes/global_vars.inc.php s'amuse à tout addslasher tout seul donc on le fait pas ici
        $this->type = intval($this->type);
        $this->config = addslashes_array($this->config);
        $serialized = serialize($this->config);
        $sql = "UPDATE connectors_out_sets SET connector_out_set_caption = '".addslashes($this->caption)."', connector_out_set_type = ".$this->type.", connector_out_set_config = '".addslashes($serialized)."' WHERE connector_out_set_id = ".$this->id."";
        pmb_mysql_query($sql);
    }

    public static function check_data_from_form() {
        global $set_caption;

        if(empty($set_caption)) {
            return false;
        }
        return true;
    }

    public static function delete($id) {
        $id = intval($id);
        if($id) {
            //Deletons le set
            $sql = "DELETE FROM connectors_out_sets WHERE connector_out_set_id = ".$id;
            pmb_mysql_query($sql);
        }
    }

    public function get_config_form(&$out_of_form_result)
    {
        // $out_of_form_result: résultat à renvoyer qui devra être placé à l'extérieur du formulaire. Exemple: un autre formulaire.
        // rien
        return "";
    }

    public function update_config_from_form()
    {
        //rien
        return;
    }

    public function get_third_column_info()
    {
        //rien
        return "";
    }


    public function update_if_expired($caller = null)
    {
        if ( $this->cache->is_cache_expired() ) {
            $this->update_cache($caller);
        }
    }

    public function update_cache($caller = null)
    {
        //rien
        return "";
    }

    public function clear_cache($also_clear_date=false)
    {
        $this->cache->clear($also_clear_date);
    }

    public function get_values($first=false, $count=false) {
        return $this->cache->get_values($first, $count);
    }

    public function get_value_count() {
        return $this->cache->get_value_count();
    }

    public static function set_already_included_sets($already_included_sets = array()) {
        static::$already_included_sets = $already_included_sets;
    }

    public static function get_already_included_sets() {
        return static::$already_included_sets;
    }

    /**
     * Marque le set comme en cours de rafraichissement
     */
    protected function markAsBeingRefreshed()
    {

        if( !$this->id ) {
            return ;
        }
        $query = "update connectors_out_sets set being_refreshed = 1 where connector_out_set_id = ".$this->id;
        pmb_mysql_query($query);
    }

    /**
     * Marque le set comme mis a jour
     */
    protected function markAsUpdated()
    {
        if( !$this->id ) {
            return ;
        }
        $query = "update connectors_out_sets set being_refreshed = 0 where connector_out_set_id = ".$this->id;
        pmb_mysql_query($query);
    }

    /**
     * Liste les notices appartenant à un ensemble de sets
     *
     * @param array $notices_ids : tableau d'id de notices
     * @param array $set_ids : tableau d'ids de sets
     *
     * @return [ notice_ids ]
     */
    public static function listNoticesInSets(array $notice_ids = [], array $set_ids = [])
    {
        if (empty($notice_ids) || empty($set_ids)) {
            return [];
        }
        $sql = "SELECT distinct(connectors_out_setcache_values_value) as notice_id FROM connectors_out_setcache_values
WHERE connectors_out_setcache_values_cachenum in (" . implode(',', $set_ids) . ")
AND connectors_out_setcache_values_value in (" . implode(',', $notice_ids) . ")";

        $res = pmb_mysql_query($sql);
        $results = [];
        while ($row = pmb_mysql_fetch_assoc($res)) {
            $results[] = $row["notice_id"];
        }
        return $results;
    }


    /**
     * Liste les sets auxquels appartient une notice
     * @param int $notice_id
     *
     * @return [ set_id ]
     */
    public static function get_notice_setlist($notice_id)
    {
        $notice_id = intval($notice_id);
        if(!$notice_id) {
            return [];
        }
        $results = [];
        $query = "SELECT connectors_out_setcache_setnum FROM connectors_out_setcache_values ";
        $query.= "LEFT JOIN connectors_out_setcaches ON (connectors_out_setcache_id =connectors_out_setcache_values_cachenum) ";
        $query.= "WHERE connectors_out_setcache_values_value = ".$notice_id;
        $res = pmb_mysql_query($query);
        while($row=pmb_mysql_fetch_assoc($res)) {
            $results[] = $row["connectors_out_setcache_setnum"];
        }
        return $results;
    }

}

//Set correspondant a des paniers de notices.
class connector_out_set_noticecaddie extends connector_out_set {

    public function __construct($id) {
        parent::__construct($id, true);
        $this->cache = new connector_out_setcache_notice($id);
    }

    public function get_config_form(&$out_of_form_result) {
        global $msg, $charset;
        $config_form="";

        $caddies=array();
        $caddies_sql = "SELECT idcaddie FROM caddie WHERE type = 'NOTI'";
        $caddies_res = pmb_mysql_query($caddies_sql);
        while($row=pmb_mysql_fetch_assoc($caddies_res)) {
            $acaddie = new caddie($row["idcaddie"]);
            $caddies[] = $acaddie;
        }

        $config_form .= '<div class=row><input name="set_includefullbase" type="checkbox" '.($this->config["include_full_base"] ? 'checked' : '').' onclick="document.getElementById(\'set_included_caddies\').disabled=document.getElementById(\'set_includefullbase\').checked" id="set_includefullbase"><label class="etiquette" for="set_includefullbase">'.htmlentities($msg["admin_connecteurs_set_noticecaddie_includefullbase"] ,ENT_QUOTES, $charset).'</label><br />';
        $config_form .= '</div><br>';

        if (!isset($this->config["included_caddies"]))
            $this->config["included_caddies"]=array();
            if (!isset($this->config["include_full_base"]))
                $this->config["include_full_base"] = false;

                $config_form .= '<div class=row><label class="etiquette" for="set_included_caddies">'.$msg["admin_connecteurs_set_noticecaddie_included"].'</label><br />';
                $config_form .= '<select '.($this->config["include_full_base"] ? 'disabled' : '').' MULTIPLE id="set_included_caddies" name="set_included_caddies[]">';
                foreach($caddies as &$acadie) {
                    $config_form .= '<option '.(in_array($acadie->idcaddie, $this->config["included_caddies"]) ? 'selected' : '').' value="'.($acadie->idcaddie).'">'.htmlentities($acadie->name ,ENT_QUOTES, $charset).'</option>';
                }
                $config_form .= '</select>';
                $config_form .= '</div>';

                return $config_form;
    }

    public function update_config_from_form() {
        global $set_included_caddies, $set_includefullbase;
        if (!is_array($set_included_caddies))
            $set_included_caddies=array($set_included_caddies);

            array_walk($set_included_caddies, function(&$a) {$a = intval($a);});//Soyons sûr de ne stocker que des entiers dans le tableau.
            $this->config["included_caddies"] = !$this->config["include_full_base"] ? $set_included_caddies : array();
            $this->config["include_full_base"] = isset($set_includefullbase);
            return;
    }

    public function get_third_column_info() {
        global $msg;
        if (isset($this->config["include_full_base"]) && $this->config["include_full_base"]) {
            return sprintf($msg["admin_connecteurs_set_noticecaddie_includedcount_fullbase"], $this->cache->get_value_count());
        } else if( !empty($this->config["included_caddies"]) && is_array($this->config["included_caddies"])){
            return sprintf($msg["admin_connecteurs_set_noticecaddie_includedcount"], count($this->config["included_caddies"]), $this->cache->get_value_count());
        }
    }

    public function update_cache($caller = null)
    {
        if( empty($this->config["included_caddies"]) && empty($this->config["include_full_base"]) ) {
            return;
        }

        // Marque le set comme en cours de rafraichissement
        $this->markAsBeingRefreshed();

        // Enregistre l'etat du cache dans une table temporaire
        $this->cache->backupCachedValues();

        // Supprime les valeurs cachees
        $this->cache->clearCachedValues();

        // Genere la requete de selection et insere les ids dans le cache
        // Base entiere
        if ( $this->config["include_full_base"] ) {
            $from_query = "SELECT ".$this->cache->id.", notice_id FROM notices";
            // Jeu de paniers
        } else {
            array_walk($this->config["included_caddies"], function(&$a) {$a = intval($a);});//Soyons sûr de ne stocker que des entiers dans le tableau.
            $from_query = "SELECT distinct ".$this->cache->id.", object_id FROM caddie_content WHERE caddie_id IN (".implode(",", $this->config["included_caddies"]).")";
        }
        $this->cache->updateCachedValuesFromSQLQuery($from_query);

        //Met a jour la liste des enregistrements supprimes
        $this->cache->updateDeletedRecords( $caller );

        //Met à jour la date du cache
        $this->cache->updateCacheDate();

        // Marque le set comme mis a jour
        $this->markAsUpdated();
    }


    public function get_values($first=false, $count=false, $datefrom=false, $dateuntil=false, $use_items_update_date=false) {
        return $this->cache->get_values($first, $count, $datefrom, $dateuntil, $use_items_update_date);
    }

    public function get_value_count($datefrom=false, $dateuntil=false, $use_items_update_date=false) {
        return $this->cache->get_value_count($datefrom, $dateuntil, $use_items_update_date);
    }

    public function get_deleted_value_count($datefrom=false, $dateuntil=false, $use_items_update_date=false) {
        return $this->cache->get_deleted_value_count($datefrom, $dateuntil, $use_items_update_date);
    }

    //Fonction qui renvoie la date de modification la plus vieille (pour l'oai)
    public function get_earliest_updatedate() {
        return $this->cache->get_earliest_updatedate();
    }
    
    //Fonction qui renvoie la date de création la plus vieille (pour l'oai)
    public function get_earliest_createdate() {
        return $this->cache->get_earliest_createdate();
    }
}

//Set correspondant a une recherche MC
class connector_out_set_noticemulticritere extends connector_out_set {

    public function __construct($id) {
        parent::__construct($id, true);
        $this->cache = new connector_out_setcache_notice($id);
    }

    public function get_config_form(&$out_of_form_result) {
        global $msg, $charset;
        $config_form="";

        if (!isset($this->config["search"])) {
            $this->config["search"] = "";
        }
        $serialized_search = $this->config["search"];
        if ($serialized_search) {
            $sc = new search(false);
            $sc->unserialize_search($serialized_search);
            $human_query = $sc->make_human_query();
        } else {
            $human_query = $msg["admin_connecteurs_set_multicritere_searchis_none"];
        }
        //Recherche humaine
        $button_modif_requete = "<input type='button' class='bouton' value=\"$msg[admin_connecteurs_set_multicritere_editsearch]\" onClick=\"document.modif_requete_form_.submit();\">";
        $config_form .= '<div class=row><label class="etiquette" for="set_included_caddies">'.$msg["admin_connecteurs_set_multicritere_searchis"].'</label><br />';
        $config_form .= $human_query."&nbsp;".$button_modif_requete;
        $config_form .= '</div><br />';

        //Changer la recherche
        $config_form .= '<div class=row><label class="etiquette" for="set_included_caddies">'.$msg["search_notice_to_connector_out_set_editsearch"].'</label><br />';
        $config_form .= '<blockquote>';

        //Garder la recherche actuelle
        $config_form .= '<input name="search_updateaction" value="none" checked type="radio" id="search_updateaction_none"><label class="etiquette" for="search_updateaction_none">'.$msg["search_notice_to_connector_out_set_editsearch_keepit"].'</label><br />';
        $config_form .= '<br />';

        //Copier une équation DSI
        $sql = "SELECT id_equation, nom_equation FROM equations WHERE proprio_equation = 0";
        $res = pmb_mysql_query($sql);
        while($row=pmb_mysql_fetch_assoc($res)) {
            $config_form .= '<input name="search_updateaction" value="dsieq_'.$row["id_equation"].'" type="radio" id="search_updateaction_dsieq_'.$row["id_equation"].'"><label class="etiquette" for="search_updateaction_dsieq_'.$row["id_equation"].'">'.$msg["search_notice_to_connector_out_set_editsearch_copydsiequation"].'</label>: '.htmlentities($row["nom_equation"] ,ENT_QUOTES, $charset);
            $config_form .= '<br />';
        }

        $config_form .= '</blockquote>';
        $config_form .= '</div>';

        //Form caché de la recherche
        $form_modif_requete = $this->make_hidden_search_form($serialized_search);
        //Mettons le dans $out_of_form_result comme ça il sera placé en dehors du formulaire
        $out_of_form_result = $form_modif_requete;

        return $config_form;
    }

    public function update_config_from_form() {
        global $search_updateaction;
        //Si on ne change rien, on ne change rien
        if (!$search_updateaction || $search_updateaction == 'none') {
            return;
        }
        $m = [];
        if (preg_match("/dsieq_(\d+)/", $search_updateaction, $m)) {
            $dsi_eq_id = $m[1];
            $equation = new equation($dsi_eq_id);
            $this->config["search"] = $equation->requete;
            $this->clear_cache(true);
        }

        return;
    }

    public function get_third_column_info() {
        global $msg;
        global $msg;
        return sprintf($msg["admin_connecteurs_set_multicritere_includedcount"], $this->cache->get_value_count());
    }

    public function update_cache($caller = null)
    {
        if ( empty($this->config["search"]) ) {
            return;
        }

        // Marque le set comme en cours de rafraichissement
        $this->markAsBeingRefreshed();

        // Enregistre l'etat du cache dans une table temporaire
        $this->cache->backupCachedValues();

        // Supprime les valeurs cachees
        $this->cache->clearCachedValues();

        //Utilisons la classe de caches de recherche pour effectuer la recherche.
        $cache_duration = $this->cache->cache_duration_in_seconds();
        $es_search_cache = new external_services_searchcache('search_fields', '', -1, -1, $cache_duration, 'conset', true);
        $es_search_cache->unserialize_search($this->config["search"]);
        $es_search_cache->update();

        $values = $es_search_cache->get_results(0, $es_search_cache->get_result_count(false),'',false);
        $this->cache->values = $values;
        $this->cache->updateCachedValues();

        //Met a jour la liste des enregistrements supprimes
        $this->cache->updateDeletedRecords( $caller );

        //Met à jour la date du cache
        $this->cache->updateCacheDate();

        // Marque le set comme mis a jour
        $this->markAsUpdated();
    }


    public function get_values($first=false, $count=false, $datefrom=false, $dateuntil=false, $use_items_update_date=false) {
        return $this->cache->get_values($first, $count, $datefrom, $dateuntil, $use_items_update_date);
    }

    public function get_value_count($datefrom=false, $dateuntil=false, $use_items_update_date=false) {
        return $this->cache->get_value_count($datefrom, $dateuntil, $use_items_update_date);
    }

    //Fonction qui renvoie la date de modification la plus vieille (pour l'oai)
    public function get_earliest_updatedate() {
        return $this->cache->get_earliest_updatedate();
    }
    
    //Fonction qui renvoie la date de création la plus vieille (pour l'oai)
    public function get_earliest_createdate() {
        return $this->cache->get_earliest_createdate();
    }

    //copié et adapté de equation.class.php
    public function make_hidden_search_form($serialized_search) {
        global $search;
        global $charset;

        $url = "./catalog.php?categ=search&mode=6" ;
        // remplir $search
        $sc = new search(false);
        $sc->unserialize_search($serialized_search);

        $r="<form name='modif_requete_form_' action='$url' style='display:none' method='post'>";
        if(is_countable($search)) {
            for ($i=0; $i<count($search); $i++) {
                $inter="inter_".$i."_".$search[$i];
                global ${$inter};
                $op="op_".$i."_".$search[$i];
                global ${$op};
                $field_="field_".$i."_".$search[$i];
                global ${$field_};
                $field=${$field_};
                //Récupération des variables auxiliaires
                $fieldvar_="fieldvar_".$i."_".$search[$i];
                global ${$fieldvar_};
                $fieldvar=${$fieldvar_};
                if (!is_array($fieldvar)) $fieldvar=array();

                $r.="<input type='hidden' name='search[]' value='".htmlentities($search[$i],ENT_QUOTES,$charset)."'/>";
                $r.="<input type='hidden' name='".$inter."' value='".htmlentities(${$inter},ENT_QUOTES,$charset)."'/>";
                $r.="<input type='hidden' name='".$op."' value='".htmlentities(${$op},ENT_QUOTES,$charset)."'/>";
                for ($j=0; $j<count($field); $j++) {
                    $r.="<input type='hidden' name='".$field_."[]' value='".htmlentities($field[$j],ENT_QUOTES,$charset)."'/>";
                }
                reset($fieldvar);
                foreach ($fieldvar as $var_name => $var_value) {
                    for ($j=0; $j<count($var_value); $j++) {
                        $r.="<input type='hidden' name='".$fieldvar_."[".$var_name."][]' value='".htmlentities($var_value[$j],ENT_QUOTES,$charset)."'/>";
                    }
                }
            }
        }
        $r.="<input type='hidden' name='id_connector_set' value='$this->id'/>";
        $r.="</form>";
        return $r;
    }
}

//Set correspondant à des paniers d'exemplaires.
class connector_out_set_explcaddie extends connector_out_set {
    public function get_config_form(&$out_of_form_result) {
        global $msg, $charset;
        $config_form="";

        $caddies=array();
        $caddies_sql = "SELECT idcaddie FROM caddie WHERE type = 'EXPL'";
        $caddies_res = pmb_mysql_query($caddies_sql);
        while($row=pmb_mysql_fetch_assoc($caddies_res)) {
            $acaddie = new caddie($row["idcaddie"]);
            $caddies[] = $acaddie;
        }

        $config_form .= '<div class=row><input name="set_includefullbase" type="checkbox" '.($this->config["include_full_base"] ? 'checked' : '').' onclick="document.getElementById(\'set_included_caddies\').disabled=document.getElementById(\'set_includefullbase\').checked" id="set_includefullbase"><label class="etiquette" for="set_includefullbase">'.htmlentities($msg["admin_connecteurs_set_explcaddie_includefullbase"] ,ENT_QUOTES, $charset).'</label><br />';
        $config_form .= '</div>';

        if (!isset($this->config["included_caddies"]))
            $this->config["included_caddies"]=array();
            if (!isset($this->config["include_full_base"]))
                $this->config["include_full_base"] = false;

                $config_form .= '<div class=row><label class="etiquette" for="set_included_caddies">'.$msg["admin_connecteurs_set_explcaddie_included"].'</label><br />';
                $config_form .= '<select '.($this->config["include_full_base"] ? 'disabled' : '').' MULTIPLE id="set_included_caddies" name="set_included_caddies[]">';
                foreach($caddies as &$acadie) {
                    $config_form .= '<option '.(in_array($acadie->idcaddie, $this->config["included_caddies"]) ? 'selected' : '').' value="'.($acadie->idcaddie).'">'.htmlentities($acadie->name ,ENT_QUOTES, $charset).'</option>';
                }
                $config_form .= '</select>';
                $config_form .= '</div>';

                return $config_form;
    }

    public function update_config_from_form() {
        global $set_included_caddies, $set_includefullbase;
        if (!is_array($set_included_caddies))
            $set_included_caddies=array($set_included_caddies);

            array_walk($set_included_caddies, function(&$a) {$a = intval($a);});//Soyons sûr de ne stocker que des entiers dans le tableau.
            $this->config["included_caddies"] = !$this->config["include_full_base"] ? $set_included_caddies : array();
            $this->config["include_full_base"] = isset($set_includefullbase);
            return;
    }

    public function get_third_column_info() {
        global $msg;
        if (isset($this->config["include_full_base"]) && $this->config["include_full_base"]) {
            return sprintf($msg["admin_connecteurs_set_explcaddie_includedcount_fullbase"], $this->cache->get_value_count());
        } else {
            return sprintf($msg["admin_connecteurs_set_explcaddie_includedcount"], (is_array($this->config["included_caddies"]) ? count($this->config["included_caddies"]) : 0), $this->cache->get_value_count());
        }
    }

    public function update_cache($caller = null) {
        //Valeurs par défault
        if (!isset($this->config["included_caddies"]))
            $this->config["included_caddies"]=array();
            if (!isset($this->config["include_full_base"]))
                $this->config["include_full_base"] = false;

                //On remplit
                if ($this->config["include_full_base"]) {
                    $sql = "SELECT ".$this->cache->id.", expl_id FROM exemplaires";
                } else {
                    array_walk($this->config["included_caddies"], function(&$a) {$a = intval($a);});//Soyons sûr de ne stocker que des entiers dans le tableau.
                    $sql = "SELECT distinct ".$this->cache->id.", object_id FROM caddie_content WHERE caddie_id IN (".implode(",", $this->config["included_caddies"]).")";
                }

                $this->cache->updatedb_from_sqlselect($sql);
    }
}

//Set correspondant à des paniers d'emprunteurs.
class connector_out_set_emprcaddie extends connector_out_set {
    public function get_config_form(&$out_of_form_result) {
        global $msg, $charset;
        $config_form="";

        $caddies=array();
        $caddies_sql = "SELECT idemprcaddie FROM empr_caddie";
        $caddies_res = pmb_mysql_query($caddies_sql);
        while($row=pmb_mysql_fetch_assoc($caddies_res)) {
            $acaddie = new empr_caddie($row["idemprcaddie"]);
            $caddies[] = $acaddie;
        }

        if (!isset($this->config["included_caddies"]))
            $this->config["included_caddies"]=array();
            $config_form .= '<div class=row><label class="etiquette" for="set_included_caddies">'.$msg["admin_connecteurs_set_emprcaddie_included"].'</label><br />';
            $config_form .= '<select MULTIPLE name="set_included_caddies[]">';
            foreach($caddies as &$acadie) {
                $config_form .= '<option '.(in_array($acadie->idemprcaddie, $this->config["included_caddies"]) ? 'selected' : '').' value="'.($acadie->idemprcaddie).'">'.htmlentities($acadie->name ,ENT_QUOTES, $charset).'</option>';
            }
            $config_form .= '</select>';
            $config_form .= '</div>';

            return $config_form;
    }

    public function update_config_from_form() {
        global $set_included_caddies;
        if (!is_array($set_included_caddies))
            $set_included_caddies=array($set_included_caddies);

            array_walk($set_included_caddies, function(&$a) {$a = intval($a);});//Soyons sûr de ne stocker que des entiers dans le tableau.
            $this->config["included_caddies"] = $set_included_caddies;
            return;
    }

    public function get_third_column_info() {
        global $msg;
        return sprintf($msg["admin_connecteurs_set_emprcaddie_includedcount"], (is_array($this->config["included_caddies"]) ? count($this->config["included_caddies"]) : 0), $this->cache->get_value_count());
    }

    public function update_cache($caller = null) {
        array_walk($this->config["included_caddies"], function(&$a) {$a = intval($a);});//Soyons sûr de ne stocker que des entiers dans le tableau.
        $sql = "SELECT distinct ".$this->cache->id.", object_id FROM empr_caddie_content WHERE empr_caddie_id IN (".implode(",", $this->config["included_caddies"]).")";

        $this->cache->updatedb_from_sqlselect($sql);
    }
}

function class_connector_out_set_typed($id, $type=0) {
    global $connector_out_set_types_classes;

    if (!$type) {
        $sql = "SELECT connector_out_set_type FROM connectors_out_sets WHERE connector_out_set_id = ".($id+0);
        $type = pmb_mysql_result(pmb_mysql_query($sql), 0, 0);
    }
    if (!$type) $type=1;
    return $connector_out_set_types_classes[$type];
}

function new_connector_out_set_typed($id, $type=0) {
    $class = class_connector_out_set_typed($id, $type);
    return new $class($id);
}

class connector_out_sets {
    public $sets=array();

    public function __construct() {
        $sql = "SELECT connector_out_set_id, connector_out_set_type FROM connectors_out_sets ORDER BY connector_out_set_type";
        $res = pmb_mysql_query($sql);
        while ($row=pmb_mysql_fetch_assoc($res)) {
            $aesuser = new_connector_out_set_typed($row["connector_out_set_id"], $row["connector_out_set_type"]);
            $this->sets[] = clone $aesuser;
        }
    }

    public static function get_typed_set_count($type) {
        $type = intval($type);
        $sql = "SELECT COUNT(1) FROM connectors_out_sets WHERE connector_out_set_type = ".$type;
        $result = pmb_mysql_result(pmb_mysql_query($sql), 0, 0);
        return $result;
    }
}

/*
 * Catégories
 * */

class connector_out_setcateg {
    public $id=0;
    public $name="";
    public $sets=array();

    public function __construct($id) {
        $this->id = intval($id);

        //Main information
        if ($this->id) {
            $sql = "SELECT * FROM connectors_out_setcategs WHERE connectors_out_setcateg_id = ".$this->id;
            $res = pmb_mysql_query($sql);
            $row = pmb_mysql_fetch_assoc($res);
            $this->name = $row["connectors_out_setcateg_name"];
        }

        //Categ content
        $sql = "SELECT connectors_out_setcategset_setnum FROM connectors_out_setcateg_sets WHERE connectors_out_setcategset_categnum = ".$this->id;
        $res=pmb_mysql_query($sql);
        while($row=pmb_mysql_fetch_assoc($res)) {
            $this->sets[] = $row["connectors_out_setcategset_setnum"];
        }
    }

    public function get_content_form() {
    	global $msg, $charset;

    	$interface_content_form = new interface_content_form(static::class);
    	$interface_content_form->add_element('setcateg_name', 'admin_connecteurs_setcateg_name')
    	->add_input_node('text', $this->name);

    	//included sets
    	$out_sets = new connector_out_sets();
    	$included_sets = '<select MULTIPLE name="setcateg_sets[]">';
    	$included_sets .= '<option value="">'.$msg["admin_connecteurs_setcateg_none"].'</option>';
    	foreach ($out_sets->sets as &$aset) {
    		$included_sets .= '<option '.(in_array($aset->id, $this->sets) ? ' selected ' : '').' value="'.$aset->id.'">'.htmlentities($aset->caption ,ENT_QUOTES, $charset).'</option>';
    	}
    	$included_sets .= '</select>';
    	$interface_content_form->add_element('setcateg_sets', 'admin_connecteurs_setcateg_includedsets')
    	->add_html_node($included_sets);

		return $interface_content_form->get_display();
    }

    public function get_form() {
        global $msg;

        $interface_form = new interface_admin_form('form_outsetcateg');
        if(!$this->id){
            $interface_form->set_label($msg['admin_connecteurs_setcateg_add']);
        }else{
            $interface_form->set_label($msg['admin_connecteurs_setcateg_edit']);
        }

        $interface_form->set_object_id($this->id)
        ->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->name." ?")
        ->set_content_form($this->get_content_form())
        ->set_table_name('connectors_out_setcategs')
        ->set_field_focus('setcateg_name');
        return $interface_form->get_display();
    }

    public function set_properties_from_form() {
        global $setcateg_name, $setcateg_sets;

        $this->name = stripslashes($setcateg_name);
        $this->sets = $setcateg_sets;
    }

    public function get_query_if_exists() {
        return " SELECT count(1) FROM connectors_out_setcategs WHERE (connectors_out_setcateg_name='".addslashes($this->name)."' AND connectors_out_setcateg_id!='".$this->id."' )";
    }

    public function save() {
        if ($this->id == 0) {
            $sql = "INSERT INTO connectors_out_setcategs (connectors_out_setcateg_name) VALUES ('".addslashes($this->name)."')";
            pmb_mysql_query($sql);
            $this->id = pmb_mysql_insert_id();
        } else {
            $sql = "UPDATE connectors_out_setcategs SET connectors_out_setcateg_name = '".addslashes($this->name)."' WHERE connectors_out_setcateg_id = ".$this->id."";
            pmb_mysql_query($sql);
        }
        //Vidage de la catégorie
        $sql = "DELETE FROM connectors_out_setcateg_sets WHERE connectors_out_setcategset_categnum = ".$this->id;
        pmb_mysql_query($sql);

        //Remplissage de la catégorie
        if (count($this->sets)) {
            $sql = "INSERT INTO connectors_out_setcateg_sets (connectors_out_setcategset_setnum ,connectors_out_setcategset_categnum) VALUES ";
            $values=array();
            foreach ($this->sets as $asetid) {
                $asetid = intval($asetid);//Conversion en int.
                if (!$asetid) continue;
                $values[] = '('.$asetid.', '.$this->id.')';
            }
            $sql .= implode(",", $values);
            pmb_mysql_query($sql);
        }
    }

    public static function add_new() {
        $sql = "INSERT INTO connectors_out_setcategs () VALUES ()";
        pmb_mysql_query($sql);
        $new_setcateg_id = pmb_mysql_insert_id();
        return new connector_out_setcateg($new_setcateg_id);
    }

    public static function check_data_from_form() {
        global $setcateg_name;

        if(empty($setcateg_name)) {
            return false;
        }
        return true;
    }

    public static function delete($id) {
        $id = intval($id);
        if($id) {
            //Deletons l'user
            $sql = "DELETE FROM connectors_out_setcategs WHERE connectors_out_setcateg_id = ".$id;
            pmb_mysql_query($sql);

            //Vidons la catégorie
            $sql = "DELETE FROM connectors_out_setcateg_sets WHERE connectors_out_setcategset_categnum = ".$id;
            pmb_mysql_query($sql);
        }
    }
}

class connector_out_setcategs {
    public $categs=array();

    public function __construct() {
        $sql = "SELECT connectors_out_setcateg_id FROM connectors_out_setcategs";
        $res = pmb_mysql_query($sql);
        while ($row=pmb_mysql_fetch_assoc($res)) {
            $acateg = new connector_out_setcateg($row["connectors_out_setcateg_id"]);
            $this->categs[] = clone $acateg;
        }
    }
}

/*
 * Cache  d'un set
 * */

class connector_out_setcache
{
    public $id = 0;
    public $set_id = 0;
    public $last_updated_date = '';
    public $life_lifeduration = 0;
    public $life_lifeduration_unit = 'seconds';

    /* Ids en cache */
    public $values = array();

    /* Ids supprimes */
    protected $deleted_values = [];

    protected $temporary_table = "";

    public function __construct($set_id) {
        $set_id=intval($set_id);
        //Main information
        $sql = "SELECT * FROM connectors_out_setcaches WHERE connectors_out_setcache_setnum=".$set_id;
        $res = pmb_mysql_query($sql);
        if (pmb_mysql_num_rows($res)) {
            $row = pmb_mysql_fetch_assoc($res);
            $this->id = $row["connectors_out_setcache_id"];
            $this->set_id = $set_id;
            $this->last_updated_date = $row["connectors_out_setcache_lastupdatedate"];
            $this->life_lifeduration = intval($row["connectors_out_setcache_lifeduration"]);
            $this->life_lifeduration_unit = $row["connectors_out_setcache_lifeduration_unit"];
        }
    }

    public static function add($set_id) {
        $sql = "INSERT INTO connectors_out_setcaches (connectors_out_setcache_setnum) VALUES (".$set_id.")";
        pmb_mysql_query($sql);
        return new connector_out_setcache($set_id);
    }

    public function delete() {
        //Deletons le cache
        $sql = "DELETE FROM connectors_out_setcaches WHERE connectors_out_setcache_id = ".$this->id;
        pmb_mysql_query($sql);

        //Vidons le cache
        $sql = "DELETE FROM connectors_out_setcache_values WHERE connectors_out_setcache_values_cachenum = ".$this->id;
        pmb_mysql_query($sql);
    }

    public function clear($also_clear_date=false) {
        //Vidons le cache
        $sql = "DELETE FROM connectors_out_setcache_values WHERE connectors_out_setcache_values_cachenum = ".$this->id;
        pmb_mysql_query($sql);

        if ($also_clear_date) {
            $sql = "UPDATE connectors_out_setcaches SET connectors_out_setcache_lastupdatedate = '0000-00-00 00:00:00' WHERE connectors_out_setcache_id = ".$this->id;
            pmb_mysql_query($sql);
        }
    }

    public function get_values($first = false, $count = false)
    {
        if ($this->values) {
            return $this->values;
        }

        if ($first !== false && $count !== false) {
            $limit = "LIMIT ".$first.", ".$count;
        } else {
                $limit = "";
        }
        $sql = "SELECT connectors_out_setcache_values_value FROM connectors_out_setcache_values WHERE connectors_out_setcache_values_cachenum = " . $this->id . " " . $limit;

        $res = pmb_mysql_query($sql);
        while ($row = pmb_mysql_fetch_assoc($res)) {
            $this->values[] = $row["connectors_out_setcache_values_value"];
        }
        return $this->values;
    }

    public function get_value_count() {
        $sql = "SELECT count(1) FROM connectors_out_setcache_values WHERE connectors_out_setcache_values_cachenum = ".$this->id;
        if (count(connector_out_set::get_already_included_sets())) {
            $sql.= " and connectors_out_setcache_values_value not in (select connectors_out_setcache_values_value from connectors_out_setcache_values where connectors_out_setcache_values_cachenum in (".implode(",", connector_out_set::get_already_included_sets())."))";
        }
        return pmb_mysql_result(pmb_mysql_query($sql), 0, 0);
    }

    public function commit_to_db($also_commit_values=false) {
        $sql = "UPDATE connectors_out_setcaches SET connectors_out_setcache_lifeduration = ".$this->life_lifeduration.", connectors_out_setcache_lastupdatedate = '".$this->last_updated_date."', connectors_out_setcache_lifeduration_unit = '".$this->life_lifeduration_unit."' WHERE connectors_out_setcache_id = ".$this->id;
        pmb_mysql_query($sql);
    }

    /**
     * Met a jour les valeurs cachees depuis la propriete $this->values
     */
    public function updateCachedValues()
    {
        //Remplit le cache par paquets de 100.
        if($this->id && !empty($this->values)) {
            $davalues = array_chunk($this->values, 100);
            foreach ($davalues as $some_values) {
                $sql = "INSERT INTO connectors_out_setcache_values (connectors_out_setcache_values_cachenum, connectors_out_setcache_values_value) VALUES ";
                $values = array();
                foreach ($some_values as $avalue) {
                    if (!$avalue) continue;
                    $values[] = '('.$this->id.', '.$avalue.')';
                }
                $sql .= implode(",", $values);
                pmb_mysql_query($sql);
            }
        }
        $this->values = [];

    }

    /**
     * Met a jour les valeurs cachees depuis une requete SQL
     */
    public function updateCachedValuesFromSQLQuery($from_query = '')
    {
        if($from_query) {
            $query = "INSERT INTO connectors_out_setcache_values (connectors_out_setcache_values_cachenum, connectors_out_setcache_values_value) ".$from_query;
            pmb_mysql_query($query);
        }
        $this->values = [];

    }


    /*
     * Enregistre l'etat du cache dans une table temporaire
     */
    public function backupCachedValues()
    {
        if(!$this->id) {
            return;
        }
        $this->temporary_table = "tmp_connectors_out_setcache_values_".$this->id;
        $query = "drop table if exists ".$this->temporary_table." ";
        pmb_mysql_query($query);

        $query = "create temporary table ".$this->temporary_table." ";
        $query.= "select connectors_out_setcache_values_value from connectors_out_setcache_values where connectors_out_setcache_values_cachenum = ".$this->id;
        pmb_mysql_query($query);
    }

    /*
     * Supprime les valeurs cachees
     */
    public function clearCachedValues()
    {
        if(!$this->id) {
            return;
        }
        $query = "DELETE FROM connectors_out_setcache_values WHERE connectors_out_setcache_values_cachenum = ".$this->id;
        pmb_mysql_query($query);
    }

    /**
     * Met a jour la date du cache
     */
    public function updateCacheDate()
    {
        if( !$this->id ) {
            return;
        }
        $query = "UPDATE connectors_out_setcaches SET connectors_out_setcache_lastupdatedate = NOW() WHERE connectors_out_setcache_id = ".$this->id;
        pmb_mysql_query($query);
    }

    /**
     * Met a jour les enregistrements supprimes
     */
    public function updateDeletedRecords( $caller = null )
    {
        if( !$this->id || !$this->set_id || !$this->temporary_table ) {
            return;
        }

        if( is_null($caller) ) {
            return;
        }

        if ( !method_exists($caller, 'updateDeletedRecordsCacheCallback') ) {
            return;
        }

        // On recupere les enregistrements qui sont dans le cache et on les enleve de la table des enregistrements supprimes
        $caller->updateDeletedRecordsCacheCallback('removeFromDeleteds', $this->set_id, $this->id);

        // On recupere les enregistrements qui ne sont plus dans le cache et on les ajoute a la table des enregistrements supprimes
        $query = "select connectors_out_setcache_values_value from ".$this->temporary_table." where connectors_out_setcache_values_value not in ";
        $query.= "(select connectors_out_setcache_values_value from connectors_out_setcache_values where connectors_out_setcache_values_cachenum = ".$this->id.")";
        $result = pmb_mysql_query($query);
        $ids = [];
        if (pmb_mysql_num_rows($result)) {
            while ($record = pmb_mysql_fetch_assoc($result)) {
                $ids[] = $record['connectors_out_setcache_values_value'];
            }
            $caller->updateDeletedRecordsCacheCallback('addToDeleteds', $this->set_id, $this->id, $ids);

        }
        pmb_mysql_free_result($result);
    }


    public function updatedb_from_sqlselect($sql_select) {
        //on marque le set comme en cours de rafraississement
        $query = "update connectors_out_sets set being_refreshed = 1 where connector_out_set_id = ".$this->id;
        pmb_mysql_query($query);

        //On vide
        $sql = "DELETE FROM connectors_out_setcache_values WHERE connectors_out_setcache_values_cachenum = ".$this->id;
        pmb_mysql_query($sql);

        $sql = "INSERT INTO connectors_out_setcache_values (connectors_out_setcache_values_cachenum, connectors_out_setcache_values_value) ".$sql_select;
        pmb_mysql_query($sql);

        //On met à jour la date
        $sql = "UPDATE connectors_out_setcaches SET connectors_out_setcache_lastupdatedate = NOW() WHERE connectors_out_setcache_id = ".$this->id;
        pmb_mysql_query($sql);

        //rafraississement terminé, on retire le marqueur
        $query = "update connectors_out_sets set being_refreshed = 0 where connector_out_set_id = ".$this->id;
        pmb_mysql_query($query);

        //Et on vide le cache actuel
        $this->values = array();
    }

    public function get_config_form() {
        global $msg, $charset;
        $this->life_lifeduration = intval($this->life_lifeduration);
        $config_form = '';
        $config_form .= '<div class=row><label class="etiquette" for="setcache_duration_value">'.$msg["admin_connecteurs_sets_cache_config_duration"].'</label><br />';
        $config_form .= '<input size="5" name="setcache_duration_value" type="text" value="'.htmlentities($this->life_lifeduration ,ENT_QUOTES, $charset).'">&nbsp;';
        $config_form .= '<select name="setcache_duration_unit" type="text">';
        $config_form .= '<option '.($this->life_lifeduration_unit == 'seconds' ? 'selected' : '').' value="seconds">'.htmlentities($msg["admin_connecteurs_sets_cache_config_duration_seconds"] ,ENT_QUOTES, $charset).'</option>';
        $config_form .= '<option '.($this->life_lifeduration_unit == 'minutes' ? 'selected' : '').' value="minutes">'.htmlentities($msg["admin_connecteurs_sets_cache_config_duration_minutes"] ,ENT_QUOTES, $charset).'</option>';
        $config_form .= '<option '.($this->life_lifeduration_unit == 'hours' ? 'selected' : '').' value="hours">'.htmlentities($msg["admin_connecteurs_sets_cache_config_duration_hours"] ,ENT_QUOTES, $charset).'</option>';
        $config_form .= '<option '.($this->life_lifeduration_unit == 'days' ? 'selected' : '').' value="days">'.htmlentities($msg["admin_connecteurs_sets_cache_config_duration_days"] ,ENT_QUOTES, $charset).'</option>';
        $config_form .= '<option '.($this->life_lifeduration_unit == 'weeks' ? 'selected' : '').' value="weeks">'.htmlentities($msg["admin_connecteurs_sets_cache_config_duration_weeks"] ,ENT_QUOTES, $charset).'</option>';
        $config_form .= '<option '.($this->life_lifeduration_unit == 'months' ? 'selected' : '').' value="months">'.htmlentities($msg["admin_connecteurs_sets_cache_config_duration_months"] ,ENT_QUOTES, $charset).'</option>';
        $config_form .= '</select>';
        $config_form .= '</div>';

        return $config_form;
    }

    public function update_from_form() {
        global $setcache_duration_unit, $setcache_duration_value;
        $this->life_lifeduration = intval($setcache_duration_value);
        $this->life_lifeduration_unit = $setcache_duration_unit;
    }

    public function is_cache_expired() {
        $config_mysql_timemapping = array(
            "seconds" => "SECOND",
            "minutes" => "MINUTE",
            "hours" => "HOUR",
            "days" => "DAY",
            "weeks" => "WEEK",
            "months" => "MONTH"
        );
        if ($this->id) {
            $sql = "SELECT IFNULL(DATE_ADD(connectors_out_setcache_lastupdatedate, INTERVAL ".$this->life_lifeduration." ".$config_mysql_timemapping[$this->life_lifeduration_unit].") < NOW(), 1) FROM connectors_out_setcaches WHERE connectors_out_setcache_id = ".$this->id;
            $expired = pmb_mysql_result(pmb_mysql_query($sql), 0, 0);
        } else {
            $expired = 1;
        }
        return $expired;
    }

    public function cache_duration_in_seconds() {
        $config_mysql_timemapping = array(
            "seconds" => "SECOND",
            "minutes" => "MINUTE",
            "hours" => "HOUR",
            "days" => "DAY",
            "weeks" => "WEEK",
            "months" => "MONTH"
        );
        $sql = "SELECT UNIX_TIMESTAMP(NOW() + INTERVAL ".$this->life_lifeduration." ".$config_mysql_timemapping[$this->life_lifeduration_unit].") - UNIX_TIMESTAMP(NOW())";
        $seconds = pmb_mysql_result(pmb_mysql_query($sql), 0, 0);
        return $seconds;
    }

}

class connector_out_setcache_notice extends connector_out_setcache
{
    public function get_values($first=false, $count=false, $datefrom=false, $dateuntil=false, $use_items_update_date=false) {
        if ($this->values) {
            return $this->values;
        }
        //Values

        if ($first !== false && $count !== false) {
            $limit = "LIMIT ".$first.", ".$count;
        } else {
            $limit = "";
        }
        if ($datefrom or $dateuntil) {
            $where_clauses0 = array();
            $where_clauses1 = array();
            if ($datefrom){
                $where_clauses0[] = "notices.update_date > FROM_UNIXTIME(".$datefrom.")";
                $where_clauses1[] = "exemplaires.update_date > FROM_UNIXTIME(".$datefrom.")";
            }
            if ($dateuntil){
                $where_clauses0[] = "notices.update_date < FROM_UNIXTIME(".$dateuntil.')';
                $where_clauses1[] = "exemplaires.update_date < FROM_UNIXTIME(".$dateuntil.')';
            }
            $where_clause0 = implode(" AND ", $where_clauses0);
            $where_clause1 = implode(" AND ", $where_clauses1);
            $sql0 = "SELECT connectors_out_setcache_values_value FROM connectors_out_setcache_values LEFT JOIN notices ON (notices.notice_id = connectors_out_setcache_values_value) WHERE ".$where_clause0.($where_clause0 ? ' AND ' : '')." connectors_out_setcache_values_cachenum = ".$this->id;
            $sql1 = "SELECT connectors_out_setcache_values_value FROM connectors_out_setcache_values LEFT JOIN notices ON (notices.notice_id = connectors_out_setcache_values_value) LEFT JOIN exemplaires ON expl_notice = notices.notice_id WHERE ".$where_clause1.($where_clause1 ? ' AND ' : '')." connectors_out_setcache_values_cachenum = ".$this->id;
            if (!$use_items_update_date) {
                $sql = $sql0;
            } else {
                $sql = $sql0." UNION ".$sql1;
            }
            $sql .= " ".$limit;
        } else {
            $sql = "SELECT connectors_out_setcache_values_value FROM connectors_out_setcache_values WHERE connectors_out_setcache_values_cachenum = ".$this->id." ".$limit;
        }

        $res = pmb_mysql_query($sql);
        while($row=pmb_mysql_fetch_assoc($res)) {
            $this->values[] = $row["connectors_out_setcache_values_value"];
        }
        return $this->values;
    }

    public function get_value_count($datefrom=false, $dateuntil=false, $use_items_update_date=false) {
        if ($datefrom or $dateuntil) {
            $where_clauses0 = array();
            $where_clauses1 = array();
            if ($datefrom){
                $where_clauses0[] = "notices.update_date > FROM_UNIXTIME(".$datefrom.")";
                $where_clauses1[] = "exemplaires.update_date > FROM_UNIXTIME(".$datefrom.")";
            }
            if ($dateuntil){
                $where_clauses0[] = "notices.update_date < FROM_UNIXTIME(".$dateuntil.')';
                $where_clauses1[] = "exemplaires.update_date < FROM_UNIXTIME(".$dateuntil.')';
            }
            $where_clause0 = implode(" AND ", $where_clauses0);
            $where_clause1 = implode(" AND ", $where_clauses1);
        } else {
            $where_clause0 = "";
            $where_clause1 = "";
        }
        $sql0 = "SELECT count(1) FROM connectors_out_setcache_values LEFT JOIN notices ON (notices.notice_id = connectors_out_setcache_values_value) WHERE ".$where_clause0.($where_clause0 ? ' AND ' : '')." connectors_out_setcache_values_cachenum = ".$this->id;
        $sql1 = "SELECT count(1) FROM connectors_out_setcache_values LEFT JOIN notices ON (notices.notice_id = connectors_out_setcache_values_value) LEFT JOIN exemplaires ON expl_notice = notices.notice_id WHERE ".$where_clause1.($where_clause1 ? ' AND ' : '')." connectors_out_setcache_values_cachenum = ".$this->id;
        if (!$use_items_update_date) {
            $sql = $sql0;
        } else {
            $sql = $sql0." UNION ".$sql1;
        }
        if (count(connector_out_set::get_already_included_sets())) {
            $sql.= " and connectors_out_setcache_values_value not in (select connectors_out_setcache_values_value from connectors_out_setcache_values where connectors_out_setcache_values_cachenum in (".implode(",", connector_out_set::get_already_included_sets())."))";
        }
        return pmb_mysql_result(pmb_mysql_query($sql), 0, 0);
    }

    public function get_deleted_value_count($datefrom=false, $dateuntil=false, $use_items_update_date=false) {
        $deleted_values_count = count($this->deleted_values);
        return $deleted_values_count;
    }

    //Fonction qui renvoie la date de modification la plus vieille (pour l'oai)
    public function get_earliest_updatedate() {
        $sql = "SELECT UNIX_TIMESTAMP(MIN(update_date)) FROM connectors_out_setcache_values LEFT JOIN notices ON (notices.notice_id = connectors_out_setcache_values_value) WHERE connectors_out_setcache_values_cachenum = ".$this->id;
        $result = pmb_mysql_result(pmb_mysql_query($sql), 0, 0);
        return $result;
    }
    
    //Fonction qui renvoie la date de création la plus vieille (pour l'oai)
    public function get_earliest_createdate() {
        $sql = "SELECT UNIX_TIMESTAMP(MIN(create_date)) FROM connectors_out_setcache_values LEFT JOIN notices ON (notices.notice_id = connectors_out_setcache_values_value) WHERE connectors_out_setcache_values_cachenum = ".$this->id;
        $result = pmb_mysql_result(pmb_mysql_query($sql), 0, 0);
        return $result;
    }
}
