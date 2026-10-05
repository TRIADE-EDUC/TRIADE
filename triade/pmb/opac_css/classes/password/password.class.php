<?php

// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: password.class.php,v 1.14.2.2.2.3 2025/04/23 14:06:12 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $opac_empr_password_salt, $base_path;

use Pmb\Authentication\Models\AuthenticationConfig;

class password
{
    public const BLOWFISH_PREFIX = '$2a$';
    public const BLOWFISH_PREFIX_PHP = '$2y$';
    public const BLOWFISH_STRENGTH = '10$';
    public const BLOWFISH_LENGTH = 60;

    public const PASSWORD_RULES_TYPE_AVAILABLE_VALUES = [
		'empr',
    ];
    public const PASSWORD_RULES_TYPE_DEFAULT = 'empr';

    public static $password_rules = [
		'empr'	=> null,
		'user'	=> null,
    ];

    public static $messages = [];

    private function __construct()
    {
    }


    /**
     * Génération hash ancienne version
     *
     * @param string $password : mot de passe
     * @param string|int $salt : identifiant emprunteur
     * @return string
     */
    public static function gen_previous_hash($password, $salt)
    {
        global $opac_empr_password_salt;
        if ('' == $opac_empr_password_salt) {
            password::gen_salt_base();
        }
        return crypt($password.$opac_empr_password_salt.$salt, substr($opac_empr_password_salt, 0, 2));
    }


    /**
     * Generation phrase de salage OPAC
     * nécessaire pour mots de passe ancien format
     *
     * @return boolean
     */
    public static function gen_salt_base()
    {
        global $opac_empr_password_salt;
        $salt=md5(str_replace(array(" ","0."), "", microtime()));
        $query = "update parametres set valeur_param='".$salt."'
  				where type_param='opac' and sstype_param='empr_password_salt'";
        $result = pmb_mysql_query($query);
        if ($result) {
            $opac_empr_password_salt = $salt;
            return true;
        } else {
            return false;
        }
    }


    /**
     * Generation hash
     *
     * @param string $password
     * @return string
     */
    public static function gen_hash(string $password)
    {
        return static::gen_bcrypt_hash($password);
    }


    /**
     * Generation hash avec BCRYPT / hash auto / force 10
     *
     * @param string $password
     * @return string (60 car.)
     *
     */
    protected static function gen_bcrypt_hash(string $password)
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        //pour compatibilite
        $hash = self::BLOWFISH_PREFIX.substr($hash, 4);
        return $hash;
    }


    /**
     * Verification hash
     * @param string $password
     * @param string $hash
     * @return bool
     *
     */
    public static function verify_hash(string $password, string $hash)
    {
        return static::verify_bcrypt_hash($password, $hash);
    }


    /**
     * Verification hash avec BCRYPT
     *
     * @param string $password
     * @param string $hash
     * @return bool
     *
     */
    protected static function verify_bcrypt_hash(string $password, string $hash)
    {
        $check = password_verify($password, $hash);
        return $check;
    }


    /**
     * Comparaison de hashes
     *
     * @param string $password_1
     * @param string $password_2
     * @return boolean
     */
    public static function compare_hashes(string $password_1, string $password_2)
    {
        return static::compare_bcrypt_hashes($password_1, $password_2);
    }


    /**
     * Comparaison de hashes avec BCRYPT
     *
     * @param string $password_1
     * @param string $password_2
     * @return boolean
     */
    protected static function compare_bcrypt_hashes(string $password_1, string $password_2)
    {
        $check = hash_equals($password_1, $password_2);
        return $check;
    }


    /**
     * Recuperation du format du hash
     *
     * @param string $hash
     *
     * @return string (bcrypt | undefined)
     *
     */
    public static function get_hash_format(string $hash)
    {
        if (static::check_hash_format_is_bcrypt($hash)) {
            return 'bcrypt';
        }
        return 'undefined';
    }


    /**
     * Verification hash avec BCRYPT
     *
     * @param string $hash
     * @return bool
     *
     */
    protected static function check_hash_format_is_bcrypt(string $hash)
    {
        $hash = self::BLOWFISH_PREFIX_PHP.substr($hash, 4);
        $hash_infos = password_get_info($hash);
        if (empty($hash_infos['algoName']) || ('bcrypt' != $hash_infos['algoName'])) {
            return false;
        }
        return true;
    }


    /**
     * Recuperation des regles de definition de mot de passe
     * a partir des fichiers classes/password/rules/[empr|user].xml
     *
     * @param string $type
     * @return array
     */
    public static function get_password_rules($type = self::PASSWORD_RULES_TYPE_DEFAULT)
    {
        if (!in_array($type, self::PASSWORD_RULES_TYPE_AVAILABLE_VALUES)) {
            return [];
        }

  		if (!is_null(static::$password_rules[$type])) {
            return static::$password_rules[$type];
        }

        $password_rules_filename = __DIR__."/rules/{$type}.xml";
        $password_rules_filename_subst = __DIR__."/rules/{$type}_subst.xml";

        if (is_readable($password_rules_filename_subst)) {
            $password_rules_filename = $password_rules_filename_subst;
        } else {
            if (!is_readable($password_rules_filename)) {
                static::$password_rules = [];
                return [];
            }
        }
        $password_rules = json_decode(json_encode(simplexml_load_file($password_rules_filename, "SimpleXMLElement", LIBXML_NOCDATA | LIBXML_COMPACT)), true);
        if (empty($password_rules['rule'])) {
            static::$password_rules = [];
            return [];
        }
        static::$password_rules[$type] = $password_rules['rule'];
        return static::$password_rules[$type];
    }


    /**
     * Lecture des messages
     */
    public static function get_messages($type = self::PASSWORD_RULES_TYPE_DEFAULT, $lang = 'fr_FR')
    {
        if (!empty(static::$messages[$type])) {
            return static::$messages[$type];
        }
        $msg_filename = __DIR__."/messages/{$type}_{$lang}.xml";
        if (!file_exists($msg_filename)) {
            $msg_filename = __DIR__."/messages/{$type}_fr_FR.xml";
        }
        $xmllist = new XMLlist($msg_filename);
        $xmllist->analyser();
        static::$messages[$type] = $xmllist->table;
        return static::$messages[$type];
    }


    /**
     * Lecture d'un message
     */
    public static function get_message($code = '')
    {
        if (!$code) {
            return '';
        }
    }

    /**
     * Recupere les regles validees
     *
     * @param string $type :(empr)
     * @return array
     */
    public static function get_enabled_rules(string $type = self::PASSWORD_RULES_TYPE_DEFAULT)
    {
        if (!in_array($type, self::PASSWORD_RULES_TYPE_AVAILABLE_VALUES)) {
            return [];
        }

        $enabled_rules = [];
        $q = "select valeur_param from parametres where type_param='$type' and sstype_param='password_enabled_rules' ";
        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            $json_rules = pmb_mysql_result($r, 0, 0);
            $enabled_rules = encoding_normalize::utf8_decode(json_decode($json_rules, true));
        }

        // On merge avec le XML de configuration
        foreach ($enabled_rules as $key => &$enabled_rule) {
            if ($enabled_rule["enabled"]) {
                foreach (self::get_password_rules($type) as $rule) {
                    if ($key == $rule["id"]) {
                        if (isset($rule["regexp"])) {
                            $enabled_rule["regexp"] = $rule["regexp"];
                        }
                        if (isset($rule["class"])) {
                            $enabled_rule["class"] = $rule["class"];
                        }
                    }
                }
            }
            if (!is_array($enabled_rule['value'])) {
                $enabled_rule['value'] = [$enabled_rule['value']];
            }
        }

        return $enabled_rules;
    }

    /**
     * Sauvegarde les regles validees
     *
     * @param string $type :(empr)
     * @param array rules
     * @return void
     */
    public static function save_enabled_rules(string $type = self::PASSWORD_RULES_TYPE_DEFAULT, array $rules = [])
    {
        if (!in_array($type, self::PASSWORD_RULES_TYPE_AVAILABLE_VALUES)) {
            return [];
        }
        $json_rules = json_encode(encoding_normalize::utf8_normalize($rules));
        $query = "update parametres set valeur_param='".addslashes($json_rules)."' where type_param='{$type}' and sstype_param='password_enabled_rules' ";
        pmb_mysql_query($query);
    }

    /**
     * check s'il y a une authentification externe
     *
     * @return boolean
     */
    public static function check_external_authentication()
    {
        global $security_authentification_external;
        $authenticationList = AuthenticationConfig::getConfigs(AuthenticationConfig::GESTION_MODEL);
        if (!empty($authenticationList)) {
            return true;
        }
        if ($security_authentification_external) {
            return true;
        }
        return false;
    }

    /**
     * Formatage d'une regex. On remplace les caracteres speciaux par des caracteres hexadecimaux.
     * Cela permet d'avoir un conflit avec le parseHTML.
     *
     * @param string $value
     * @return string
     */
    public static function format_regex_value(string $value)
    {
        return preg_replace_callback('/([^A-Za-z0-9\s])/', function ($matches) {
            return '\x' . strtoupper(dechex(ord($matches[0])));
        }, $value);
    }
}
