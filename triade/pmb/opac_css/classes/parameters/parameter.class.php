<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: parameter.class.php,v 1.2.2.1 2024/06/07 14:04:37 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

class parameter
{

    protected static $languages_parameters = array();
    
    public static function update($type_param, $sstype_param, $valeur_param)
    {
        if (empty($type_param) || empty($sstype_param)) {
            return false;
        }

        $varGlobal = $type_param . "_" . $sstype_param;
        global ${$varGlobal};

        if (! isset(${$varGlobal})) {
            return false;
        }

        // on enregistre dans la variable globale
        ${$varGlobal} = $valeur_param;

        // puis dans la base
        $query = "update parametres set valeur_param='" . addslashes($valeur_param) . "' where type_param='" . addslashes($type_param) . "' and sstype_param='" . addslashes($sstype_param) . "'";
        pmb_mysql_query($query);
    }
    
    public static function get_language_parameters($language) {
        global $lang;
        // Tableau de surcharge par le config_local !
        global $overload_global_parameters;
        
        if(!isset(static::$languages_parameters[$lang])) {
            static::$languages_parameters[$lang] = [];
        }
        if(!isset(static::$languages_parameters[$language])) {
            $parameters = [];
            //parametres traduits
            $query = "SELECT type_param, sstype_param, trans_small_text, trans_text FROM translation
                JOIN parametres ON parametres.id_param = translation.trans_num
                WHERE trans_table='parametres' AND trans_field='valeur_param' AND trans_lang='".$language."'";
            $result = pmb_mysql_query($query);
            if($result && pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_object($result)) {
                    $field = $row->type_param . "_" . $row->sstype_param;
                    if (!isset($overload_global_parameters[$field])) {
                        $parameters[$field] = (!empty($row->trans_text) ? $row->trans_text : $row->trans_small_text);
                        //on stocke la valeur d'origine dans la langue par défaut pour éviter les erreurs de restitution
                        if(!isset(static::$languages_parameters[$lang][$field])) {
                            global ${$field};
                            static::$languages_parameters[$lang][$field] = ${$field};
                        }
                    }
                }
            }
            static::$languages_parameters[$language] = $parameters;
        }
        return static::$languages_parameters[$language];
    }
    
    public static function set_language_parameters($language) {
        $parameters = static::get_language_parameters($language);
        if(!empty($parameters)) {
            foreach ($parameters as $parameter_name=>$parameter_value) {
                global ${$parameter_name};
                ${$parameter_name} = $parameter_value;
            }
        }
    }
}

