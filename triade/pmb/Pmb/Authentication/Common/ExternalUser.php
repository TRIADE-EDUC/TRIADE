<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ExternalUser.php,v 1.2 2023/06/23 12:38:09 dbellamy Exp $

namespace Pmb\Authentication\Common;

use Pmb\Authentication\Common\AbstractLogger;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

global $pmb_url_base;

class ExternalUser extends AbstractLogger
{

    protected $userid = 0;
    protected $username = '';
    protected $pwd = '';
    protected $nom = '';
    protected $prenom = '';
    protected $user_email = '';
    protected $default_user_template = array();
    protected $default_user_json_template = '[{"userid":"","create_dt":"","last_updated_dt":"","username":"","pwd":"","user_digest":"","nom":"","prenom":"","rights":"3","user_lang":"fr_FR","nb_per_page_search":"20","nb_per_page_select":"10","nb_per_page_gestion":"20","param_popup_ticket":"0","param_sounds":"0","param_rfid_activate":"0","param_licence":"1","deflt_notice_statut":"1","deflt_notice_statut_analysis":"0","deflt_integration_notice_statut":"1","xmlta_indexation_lang":"","deflt_docs_type":"38","deflt_lenders":"1","deflt_styles":"light","deflt_docs_statut":"19","deflt_docs_codestat":"10","value_deflt_lang":"fre","value_deflt_fonction":"070","value_deflt_relation":"j-up","value_deflt_relation_serial":"j-up","value_deflt_relation_bulletin":"j-up","value_deflt_relation_analysis":"j-up","deflt_docs_location":"1","deflt_collstate_location":"0","deflt_bulletinage_location":"0","deflt_resas_location":"0","deflt_docs_section":"8","value_deflt_module":"circu","user_email":"","user_alert_resamail":"0","user_alert_demandesmail":"0","user_alert_subscribemail":"0","user_alert_serialcircmail":"0","deflt2docs_location":"1","deflt_empr_statut":"1","deflt_empr_categ":"1","deflt_empr_codestat":"2","deflt_thesaurus":"1","deflt_concept_scheme":"0","deflt_import_thesaurus":"1","value_prefix_cote":"","xmlta_doctype":"a","xmlta_doctype_serial":"a","xmlta_doctype_bulletin":"0","xmlta_doctype_analysis":"0","speci_coordonnees_etab":"","value_email_bcc":"","value_deflt_antivol":"0","explr_invisible":"0","explr_visible_mod":"0","explr_visible_unmod":"0","deflt3bibli":"0","deflt3exercice":"0","deflt3rubrique":"0","deflt3type_produit":"0","deflt3dev_statut":"-1","deflt3cde_statut":"-1","deflt3liv_statut":"-1","deflt3fac_statut":"-1","deflt3sug_statut":"-1","environnement":"","param_allloc":"0","grp_num":"0","deflt_arch_statut":"1","deflt_arch_emplacement":"1","deflt_arch_type":"1","deflt_upload_repertoire":"1","deflt_short_loan_activate":"0","deflt3lgstatdev":"1","deflt3lgstatcde":"1","deflt3receptsugstat":"32","deflt_cashdesk":"0","user_alert_suggmail":"0","deflt_explnum_statut":"1","deflt_notice_replace_keep_categories":"1","deflt_notice_is_new":"0","deflt_agnostic_warehouse":"0","deflt_cms_article_statut":"0","deflt_cms_article_type":"0","deflt_cms_section_type":"0","deflt_catalog_expanded_caddies":"1","deflt_scan_request_status":"0","xmlta_doctype_scan_request_folder_record":"","deflt_camera_empr":"0","deflt_notice_replace_links":"0","deflt_printer":"0","deflt_opac_visible_bulletinage":"1"}]';
    protected static $user_table_structure = NULL;

    protected $search_result = [];

    public function __set($name = '', $value = '')
    {
        static::$logger->debug(__METHOD__ . "( {$name}, {$value})");

        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    public function __get($name = '')
    {
        static::$logger->debug(__METHOD__ . "({$name})");

        if (property_exists($this, $name)) {
            return ($this->$name);
        }
        return false;
    }

    public function setDefaultUserJsonTemplate($default_user_json_template = '')
    {
        static::$logger->debug(__METHOD__);

        $tmp = json_decode($default_user_json_template, TRUE);

        if (is_null($tmp) || ! is_array($tmp[0]) || ! count($tmp[0])) {
            static::$error = true;
            static::$logger->error('Template JSON utilisateur incorrect');
            return;
        }

        static::fetchUserTableStructure();
        foreach ($tmp[0] as $k => $v) {
            if (! in_array($k, static::$user_table_structure)) {
                static::$error = true;
                static::$logger->error("Template JSON utilisateur incorrect, champ '$k' non défini dans la table 'users'.");
                return;
            }
        }
        foreach (static::$user_table_structure as $k => $v) {
            if (! array_key_exists($v, $tmp[0])) {
                static::$error = true;
                static::$logger->error("Template JSON utilisateur incomplet, champ '$v' non défini dans le template.");
                return;
            }
        }
        $this->default_user_json_template = $default_user_json_template;
        $this->default_user_template = $tmp[0];
        static::$logger->debug('Template Utilisateur = ');
        static::$logger->debug(print_r($this->default_user_template, true));
    }

    public function getDefaultUserJsonTemplate()
    {
        static::$logger->debug(__METHOD__);

        return $this->default_user_json_template;
    }

    public function getDefaultUserTemplate()
    {
        static::$logger->debug(__METHOD__);

        return $this->default_user_template;
    }

    protected static function fetchUserTableStructure()
    {
        static::$logger->debug(__METHOD__);

        if (is_null(static::$user_table_structure)) {
            // Recuperation de la structure de la table user pour construire le template
            static::$user_table_structure = array();
            $q = 'show columns from users';
            $r = pmb_mysql_query($q);
            if (pmb_mysql_num_rows($r)) {
                while ($row = pmb_mysql_fetch_row($r)) {
                    static::$user_table_structure[] = $row[0];
                }
            }
            static::$logger->debug('Structure de la table "users" = ');
            static::$logger->debug(print_r(static::$user_table_structure, true));
        }
    }

    /**
     * Recherche utilisateur à partir d'un login
     * retourne identifiant utilisateur si trouvé, 0 sinon
     * peuple $this->search_result avec tableau [userid, username]
     *
     * @param string|array $username
     *
     * @return int
     */
    public function searchUserByUsername($username = '')
    {
        static::$logger->debug(__METHOD__. " >> username = ".print_r($username, true));

        if( is_array($username) ) {
            $username = array_shift($username);
        }
        $username = trim($username);

        $ret = 0;
        $this->search_result = [];

        if ($username) {
            $q = 'select userid, username from users where username="' . addslashes($username) . '" limit 1';
            $r = pmb_mysql_query($q);
            if ( pmb_mysql_num_rows($r) ) {
                $this->search_result = pmb_mysql_fetch_assoc($r);
                $ret = $this->search_result['userid'];
            }
        }
        static::$logger->debug(__METHOD__. ">> userid = $ret");
        return $ret;
    }

    public function createUser(&$nb = 0)
    {
        static::$logger->debug(__METHOD__);

        global $pmb_url_base;

        if (! count($this->default_user_template)) {
            static::$error = 1;
            static::$logger->error('Template Utilisateur non défini');
            return;
        }

        $this->username = trim($this->username);
        if (! $this->username) {
            static::$logger->error('"username" non défini');
            return;
        }
        $this->nom = trim($this->nom);
        if (! $this->nom) {
            static::$logger->error('"Nom" non défini');
            return;
        }

        // create
        $user_template = $this->default_user_template;
        unset($user_template['userid']);
        $current_date = date('Y-m-d');
        $user_template['create_dt'] = $current_date;
        $user_template['last_updated_dt'] = $current_date;

        $user_template['username'] = $this->username;
        if ($this->pwd) {
            $user_template['pwd'] = $this->pwd;
        }
        $user_template['nom'] = $this->nom;
        $user_template['prenom'] = $this->prenom;
        $user_template['user_email'] = $this->user_email;

        $user_template['user_digest'] = md5($this->username . ":" . md5($pmb_url_base) . ":" . $this->pwd);

        foreach ($user_template as $k => $v) {
            $user_template[$k] = addslashes($v);
        }
        $q = "insert into users (" . implode(",", array_keys($user_template)) . ") values ('" . implode("','", $user_template) . "') ";
        static::$logger->debug($q);

        $r = pmb_mysql_query($q);
        if ($r) {
            $this->userid = pmb_mysql_insert_id();
            $nb ++;
            static::$logger->info("Ajout Utilisateur " . $this->username . " (" . (($this->prenom) ? "{$this->prenom} " : "") . $this->nom . ")");
        } else {
            static::$logger->error("Ajout Utilisateur " . $this->username . " (" . (($this->prenom) ? "{$this->prenom} " : "") . $this->nom . ")");
        }
    }

    public function updateUser(&$nb = 0)
    {
        static::$logger->debug(__METHOD__);

        global $pmb_url_base;

        $this->nom = trim($this->nom);
        if (! $this->nom) {
            static::$logger->error('"Nom" non défini');
            return;
        }
        if (! $this->userid) {
            static::$logger->error('"userid" non défini');
            return;
        }

        // update
        $user_template = array();
        $current_date = date('Y-m-d');
        $user_template['last_updated_dt'] = $current_date;
        if ($this->pwd) {
            $user_template['pwd'] = $this->pwd;
        }
        $user_template['nom'] = $this->nom;
        $user_template['prenom'] = $this->prenom;
        $user_template['user_email'] = $this->user_email;
        $user_template['user_digest'] = md5($this->username . ":" . md5($pmb_url_base) . ":" . $this->pwd);

        $q_template = array();
        foreach ($user_template as $k => $v) {
            $q_template[] = $k . "= '" . addslashes($v) . "'";
        }
        $q = "update users set " . implode(", ", $q_template);
        $q .= " where userid={$this->userid}";
        static::$logger->debug($q);

        $r = pmb_mysql_query($q);
        if ($r) {
            $nb ++;
            static::$logger->info("Mise à jour Utilisateur " . $this->username . " (" . (($this->prenom) ? "{$this->prenom} " : "") . $this->nom . ")");
        } else {
            static::$logger->error("Mise à jour Utilisateur " . $this->username . " (" . (($this->prenom) ? "{$this->prenom} " : "") . $this->nom . ")");
        }
    }
}
