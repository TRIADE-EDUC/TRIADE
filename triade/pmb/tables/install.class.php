<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: install.class.php,v 1.6 2023/08/14 10:31:32 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once "requirements/classes/verif.class.php";

class install
{

    protected static $language = null;

    protected static $messages = [];

    protected static $accepted_languages = null;

    protected const LANGUAGE_DEFAULT = 'fr';

    protected const LANGUAGES_AVAILABLE = [
        'ca',
        'en',
        'es',
        'fr',
        'it',
        'pt'
    ];

    public static $mysql_modified_variables = null;

    /**
     * Constructeur privé pour éviter l'instanciation
     */
    private function __construct()
    {}

    /**
     * Récupère les messages en fonction de la langue
     *
     * @param string $lang
     * @return []
     */
    public static function getMessages($lang)
    {
        if (! empty(static::$messages[$lang])) {
            return static::$messages[$lang];
        }

        $install_msg = [];
        $install_msg_fr = [];
        $install_msg_lang = [];
        if (is_readable(__DIR__ . "/fr/messages.php")) {
            require_once __DIR__ . "/fr/messages.php";
            $install_msg_fr = $install_msg['fr'];
        }
        if (('fr' != $lang) && (is_readable(__DIR__ . "/{$lang}/messages.php"))) {
            require_once __DIR__ . "/{$lang}/messages.php";
            $install_msg_lang = $install_msg[$lang];
        }
        static::$messages[$lang] = array_merge($install_msg_fr, $install_msg_lang);
        return static::$messages[$lang];
    }

    /**
     * Récupère la page de language en fonction de la langue
     *
     * @param string $lang
     * @return string
     */
    public static function getLanguagePage($lang)
    {
        $language_page = "";
        if (is_readable(__DIR__ . "/{$lang}/language.tpl.php")) {
            require_once __DIR__ . "/{$lang}/language.tpl.php";
        } else {
            require_once __DIR__ . "/fr/language.tpl.php";
        }
        return $language_page;
    }

    /**
     * Récupère la page d'installation en fonction de la langue
     *
     * @param string $lang
     * @return string
     */
    public static function getInstallPage($lang)
    {
        $install_page = "";
        if (is_readable(__DIR__ . "/{$lang}/install.tpl.php")) {
            require_once __DIR__ . "/{$lang}/install.tpl.php";
        } else {
            require_once __DIR__ . "/fr/install.tpl.php";
        }
        return $install_page;
    }

    /**
     * Récupére les templates de la page de compte rendu en fonction de la langue
     *
     * @param string $lang
     * @return [string]
     */
    public static function getReportTemplates($lang)
    {
        $report_tpl = [];
        if (is_readable(__DIR__ . "/{$lang}/report.tpl.php")) {
            require_once __DIR__ . "/{$lang}/report.tpl.php";
        } else {
            require_once __DIR__ . "/fr/report.tpl.php";
        }
        return $report_tpl;
    }

    /**
     * Restaure un fichier SQL
     *
     * @param string $src
     * @param string $lang
     * @param resource $dbh
     * @return boolean
     */
    public static function restore($src, $lang, $dbh)
    {
        if (empty($src)) {
            return false;
        }

        switch (true) {

            // On cherche le fichier dans la langue definie
            case (is_readable("./{$lang}/{$src}")):
                $src = "./{$lang}/{$src}";
                break;

            // Ou en Francais
            case (is_readable("./fr/{$src}")):
                $src = "./fr/{$src}";
                break;

            // Ou dans le répertoire courant
            case (is_readable("./{$src}")):
                $src = "./{$src}";
                break;

            default:
                return false;
                break;
        }

        $buffer_sql = file_get_contents($src);
        if (empty($buffer_sql)) {
            return false;
        }
        if (! empty($src)) {
            // open source file
            $SQL = preg_split('/;\s*\n|;\n/m', $buffer_sql);
            $nb_queries = count($SQL);
            for ($i = 0; $i < $nb_queries; $i ++) {
                if (! empty($SQL[$i])) {
                    pmb_mysql_query($SQL[$i], $dbh);
                }
            }
        }
        return true;
    }

    /**
     * Supprime les fichiers temporaires (XML*.tmp)
     *
     * @param string $dir
     * @return void
     */
    public static function delTemporaryFiles($dir)
    {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if (file_exists($dir . $file) && preg_match("/^XML.*?\.tmp$/i", $file)) {
                    @unlink($dir . $file);
                }
            }
            closedir($dh);
        }
    }

    /**
     * Cree les fichiers de connexion a la base de donnees
     *
     * @param string $dbhost
     * @param string $dbuser
     * @param string $dbpassword
     * @param string $dbname
     * @param string $charset
     * @param array $mysql_variables
     * @param boolean $alter_session_variables
     * @return void
     */
    public static function createDbParam(string $dbhost, string $dbuser, string $dbpassword, string $dbname, string $charset = 'utf-8', array $mysql_variables = [], bool $alter_session_variables = false)
    {
        $buffer_fic = file_get_contents(__DIR__."/db_param.model.php") ;
        $opac_buffer_fic = file_get_contents(__DIR__."/opac_db_param.model.php") ;

        $buffer_fic = str_replace('!!DB_HOST!!', $dbhost, $buffer_fic);
        $buffer_fic = str_replace('!!DB_USER!!', $dbuser, $buffer_fic);
        $buffer_fic = str_replace('!!DB_PASSWORD!!', addcslashes($dbpassword, "'"), $buffer_fic);
        $buffer_fic = str_replace('!!DB_NAME!!', $dbname, $buffer_fic);
        $buffer_fic = str_replace('!!DB_LABEL!!', $dbname, $buffer_fic);
        $buffer_fic = str_replace('!!CHARSET!!', $charset, $buffer_fic);

        $opac_buffer_fic = str_replace('!!DB_HOST!!', $dbhost, $opac_buffer_fic);
        $opac_buffer_fic = str_replace('!!DB_USER!!', $dbuser, $opac_buffer_fic);
        $opac_buffer_fic = str_replace('!!DB_PASSWORD!!', addcslashes($dbpassword, "'"), $opac_buffer_fic);
        $opac_buffer_fic = str_replace('!!DB_NAME!!', $dbname, $opac_buffer_fic);
        $opac_buffer_fic = str_replace('!!DB_LABEL!!', $dbname, $opac_buffer_fic);
        $opac_buffer_fic = str_replace('!!CHARSET!!', $charset, $opac_buffer_fic);

        //Modification des variables
        install::alterVariables($buffer_fic, $mysql_variables, $alter_session_variables);
        install::alterVariables($opac_buffer_fic, $mysql_variables, $alter_session_variables);

        @copy("../includes/db_param_old_01.inc.php", "../includes/db_param_old_02.inc.php");
        @copy("../includes/db_param.inc.php", "../includes/db_param_old_01.inc.php");
        $fptr = fopen("../includes/db_param.inc.php", 'w');
        fwrite($fptr, $buffer_fic);
        fclose($fptr);

        @copy("../opac_css/includes/opac_db_param_old_01.inc.php", "../opac_css/includes/opac_db_param_old_02.inc.php");
        @copy("../opac_css/includes/opac_db_param.inc.php", "../opac_css/includes/opac_db_param_old_01.inc.php");
        $fptr = fopen("../opac_css/includes/opac_db_param.inc.php", 'w');
        fwrite($fptr, $opac_buffer_fic);
        fclose($fptr);
    }

    /**
     * Modifie les variables MySQL
     *
     * @param string $buffer
     * @param array $m
     * @param bool $alter_session_variables
     *
     * @return string
     */
    protected static function alterVariables(string &$buffer, array $mysql_variables = [], bool $alter_session_variables = false)
    {
        $mysql_modified_variables = [];
        if(! is_null (static::$mysql_modified_variables) ){

            $mysql_modified_variables = static::$mysql_modified_variables;

        } else {

            foreach ($mysql_variables as &$var) {

                if( ($var['state'] !== verif::OK)
                    && ( ($var['mode'] == 'session' && $alter_session_variables) )
                    && (!empty($var['pmb_var'])) ) {

                    $value = null;

                    switch($var['type']) {

                        case 'integer' :
                            if ( isset($var['min_value']) && ($var['min_value'] != 'none') ) {
                                $value = $var['min_value'];
                            }
                            if(is_null($value)) {
                                if ( isset($var['max_value']) && ($var['max_value'] != 'none') ) {
                                    $value = $var['max_value'];
                                }
                            }
                            break;

                        case 'string' :
                            if( !empty($var['allowed_values']) && is_array($var['allowed_values']) ) {
                                $value = $var['allowed_values'][0];
                            }
                            break;

                        case 'set' :
                            $current_value = explode(',', $var['value']);
                            if( !empty($var['allowed_values']) && is_array($var['allowed_values']) ) {
                                $value = array_intersect($current_value, $var['allowed_values']);
                                $value = implode(',', $value);
                            }
                            break;

                        default :
                            break;
                    }

                    if(!is_null($value)) {
                        $tmp_var = ( 'global' == $var['mode'] ) ? 'global ' : 'session ';
                        $tmp_var = 'session '.$var['name']. "=";
                        if( 'integer' == $var['type'] ) {
                            $tmp_var.= intval($value);
                        } else {
                            $tmp_var.= "'$value'";
                        }
                        $mysql_modified_variables[] = $tmp_var;
                    }
                }

            }
            static::$mysql_modified_variables = $mysql_modified_variables;
        }

        $value = '';
        $pattern = "/* SQL_VARIABLES */";
        if (count($mysql_modified_variables) ) {
            $value = $pattern.PHP_EOL.'        $SQL_VARIABLES = "'.implode(', ', $mysql_modified_variables).'";' ;
        } else {
            $value = $pattern.PHP_EOL.'        $SQL_VARIABLES = "";';
        }
        $buffer = str_replace($pattern, $value, $buffer);
        return $buffer;
    }


    /**
     * Recupere la langue du navigateur ou la langue par défaut si non gérée
     *
     * @return string
     */
    public static function getLanguage()
    {
        if (! is_null(static::$language)) {
            return static::$language;
        }

        static::$language = static::LANGUAGE_DEFAULT;
        static::getAcceptedLanguages();
        if (empty(static::$accepted_languages)) {
            return static::$language;
        }
        foreach (static::$accepted_languages as $language) {
            if (in_array($language, static::LANGUAGES_AVAILABLE)) {
                static::$language = $language;
                return static::$language;
            }
        }
        return static::$language;
    }

    /**
     * Retourne les langages acceptés depuis l'entête "Accept-Language" par ordre de préférence
     *
     * @return array
     *
     */
    protected static function getAcceptedLanguages()
    {
        if (! is_null(static::$accepted_languages)) {
            return static::$accepted_languages;
        }
        static::$accepted_languages = [];
        if (empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return static::$accepted_languages;
        }
        $accept_headers = explode(',', str_replace(' ', '', $_SERVER['HTTP_ACCEPT_LANGUAGE']));
        $tmp1 = [];
        foreach ($accept_headers as $header) {
            $tmp2 = explode(';', $header);
            $value = str_replace('-', '_', $tmp2[0]);
            $q = '1';
            if (count($tmp2) > 1) {
                $last = array_pop($tmp2);
                if (false !== $pos = strpos($last, 'q=')) {
                    $q = substr($last, $pos + 2);
                }
            }
            $tmp1[$value] = $q;
        }
        arsort($tmp1);
        static::$accepted_languages = array_keys($tmp1);

        return static::$accepted_languages;
    }

    /**
     * Definit les parametres necessaires avant remplissage de la pile d'indexation
     *
     * @param resource $dbh
     * @return void
     */
    public static function setPreFillIndexationStackParameters($dbh)
    {
        // génération des URLs
        $isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
        $port = (isset($_SERVER["SERVER_PORT"]) && ((! $isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
        $port = ($port) ? ':' . $_SERVER["SERVER_PORT"] : '';
        $tmp = explode('/', $_SERVER["PHP_SELF"]);
        array_pop($tmp);
        array_pop($tmp);
        $pmb_url_base = ($isHTTPS ? 'https://' : 'http://') . $_SERVER["SERVER_NAME"] . $port . implode('/', $tmp) . '/';

        $q_pmb_url_base = "update parametres set valeur_param = '" . addslashes($pmb_url_base) . "' where type_param='pmb' and sstype_param='url_base' ";
        @pmb_mysql_query($q_pmb_url_base, $dbh);

        $q_pmb_url_internal = "update parametres set valeur_param = '" . addslashes($pmb_url_base) . "' where type_param='pmb' and sstype_param='url_internal' ";
        @pmb_mysql_query($q_pmb_url_internal, $dbh);

        $q_cms_url_base_cms_build = "update parametres set valeur_param = '" . addslashes($pmb_url_base . 'opac_css/') . "' where type_param='cms' and sstype_param='url_base_cms_build' ";
        @pmb_mysql_query($q_cms_url_base_cms_build, $dbh);

        $q_pmb_opac_url = "update parametres set valeur_param = '" . addslashes($pmb_url_base . 'opac_css/') . "' where type_param='opac' and sstype_param='url_base' ";
        @pmb_mysql_query($q_pmb_opac_url, $dbh);

        $q_opac_url_base = "update parametres set valeur_param = '" . addslashes($pmb_url_base . 'opac_css/') . "' where type_param='opac' and sstype_param='url_base' ";
        @pmb_mysql_query($q_opac_url_base, $dbh);

        $q_pmb_indexation_in_progress = "update parametres set valeur_param='0' where type_param='pmb' and sstype_param='indexation_in_progress' ";
        @pmb_mysql_query($q_pmb_indexation_in_progress, $dbh);

        $q_pmb_indexation_needed = "update parametres set valeur_param='0' where type_param='pmb' and sstype_param='indexation_needed' ";
        @pmb_mysql_query($q_pmb_indexation_needed, $dbh);

        $q_pmb_indexation_last_entity = "update parametres set valeur_param='0' where type_param='pmb' and sstype_param='indexation_last_entity' ";
        @pmb_mysql_query($q_pmb_indexation_last_entity, $dbh);
    }

    /**
     * Remplit la pile d'indexation
     *
     * @param resource $dbh
     * @return void
     */
    public static function fillIndexationStack($dbh)
    {
        // redefini ici car init.inc.php n'a pas ete charge
        if (! defined('TYPE_CATEGORY')) {
            define('TYPE_CATEGORY', 3);
        }
        if (! defined('TYPE_INDEXINT')) {
            define('TYPE_INDEXINT', 9);
        }

        $q = "insert ignore into indexation_stack
(indexation_stack_entity_id, indexation_stack_entity_type, indexation_stack_datatype, indexation_stack_timestamp, indexation_stack_parent_id, indexation_stack_parent_type)
select num_noeud, " . TYPE_CATEGORY . ", 'all', now(), num_noeud,  " . TYPE_CATEGORY . " from categories";
        @pmb_mysql_query($q, $dbh);

        $q = "insert ignore into indexation_stack
(indexation_stack_entity_id, indexation_stack_entity_type, indexation_stack_datatype, indexation_stack_timestamp, indexation_stack_parent_id, indexation_stack_parent_type)
select indexint_id, " . TYPE_INDEXINT . ", 'all', now(), indexint_id,  " . TYPE_INDEXINT . " from indexint";
        @pmb_mysql_query($q, $dbh);
    }

    /**
     * Definit les parametres necessaires apres remplissage de la pile d'indexation
     *
     * @param resource $dbh
     * @return void
     */
    public static function setPostFillIndexationStackParameters($dbh)
    {
        $q_pmb_indexation_needed = "update parametres set valeur_param='1' where type_param='pmb' and sstype_param='indexation_needed' ";
        @pmb_mysql_query($q_pmb_indexation_needed, $dbh);
    }
}
