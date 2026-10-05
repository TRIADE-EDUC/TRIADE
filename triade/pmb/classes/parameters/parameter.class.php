<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: parameter.class.php,v 1.9.2.4 2024/10/03 08:46:58 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class parameter
{

    /* Liste des parametres pouvant etre traduits */
    const TRANSLATED_PARAMETERS = [

        'pdflettreretard_1after_list',
        'pdflettreretard_1after_list_group',
        'pdflettreretard_1after_sign',
        'pdflettreretard_1before_list',
        'pdflettreretard_1before_list_group',
        'pdflettreretard_1fdp',
        'pdflettreretard_1fdp_group',
        'pdflettreretard_1madame_monsieur',
        'pdflettreretard_1madame_monsieur_group',
        'pdflettreretard_1objet',
        'pdflettreretard_1title_list',

        'pdflettreretard_2after_list',
        'pdflettreretard_2after_sign',
        'pdflettreretard_2before_list',
        'pdflettreretard_2fdp',
        'pdflettreretard_2footer',
        'pdflettreretard_2madame_monsieur',
        'pdflettreretard_2objet',
        'pdflettreretard_2title_list',

        'pdflettreretard_3after_list',
        'pdflettreretard_3after_recouvrement',
        'pdflettreretard_3before_list',
        'pdflettreretard_3before_recouvrement',
        'pdflettreretard_3fdp',
        'pdflettreretard_3footer',
        'pdflettreretard_3madame_monsieur',
        'pdflettreretard_3objet',
        'pdflettreretard_3title_list',

        'pdflettreresa_after_list',
        'pdflettreresa_before_list',
        'pdflettreresa_fdp',
        'pdflettreresa_madame_monsieur',

        'pdflettreadhesion_fdp',
        'pdflettreadhesion_madame_monsieur',
        'pdflettreadhesion_texte',

        'acquisition_pdfcde_obj_mail',
        'acquisition_pdfcde_text_after',
        'acquisition_pdfcde_text_before',
        'acquisition_pdfcde_text_mail',
        'acquisition_pdfcde_text_sign',

        'mailretard_1after_list',
        'mailretard_1after_list_group',
        'mailretard_1before_list',
        'mailretard_1before_list_group',
        'mailretard_1fdp',
        'mailretard_1fdp_group',
        'mailretard_1madame_monsieur',
        'mailretard_1madame_monsieur_group',
        'mailretard_1objet',
        'mailretard_1objet_group',
        'mailretard_1title_list',

        'mailretard_2after_list',
        'mailretard_2before_list',
        'mailretard_2fdp',
        'mailretard_2madame_monsieur',
        'mailretard_2objet',
        'mailretard_2title_list',

        'mailretard_3after_list',
        'mailretard_3before_list',
        'mailretard_3before_recouvrement',
        'mailretard_3fdp',
        'mailretard_3madame_monsieur',
        'mailretard_3objet',
        'mailretard_3title_list',

        'mailrelanceadhesion_fdp',
        'mailrelanceadhesion_madame_monsieur',
        'mailrelanceadhesion_objet',
        'mailrelanceadhesion_sign_address',
        'mailrelanceadhesion_texte',
        
        'empr_send_pwd_mail_obj',
        'empr_send_pwd_mail_text'
    ];
    
    protected static $languages_parameters = array();

    public static function update($type_param, $sstype_param, $valeur_param)
    {
        if (empty($type_param) || empty($sstype_param)) {
            return false;
        }

        $param = $type_param . '_' . $sstype_param;
        global ${$param};

        if (! isset(${$param})) {
            return false;
        }

        // on enregistre dans la variable globale
        ${$param} = $valeur_param;

        // puis dans la base
        $query = "update parametres set valeur_param='" . addslashes($valeur_param) . "' where type_param='" . addslashes($type_param) . "' and sstype_param='" . addslashes($sstype_param) . "'";
        pmb_mysql_query($query);
    }

    public static function get_input_activation($type_param, $sstype_param, $valeur_param)
    {
        global $msg, $javascript_path;

        $display = "
            <script type='text/javascript' src='" . $javascript_path . "/parameter.js'></script>
            <input type='checkbox' class='switch' id='parameter_" . $type_param . "_" . $sstype_param . "' name='parameter_" . $type_param . "_" . $sstype_param . "' value='1' " . ($valeur_param ? "checked='checked'" : "") . " onclick=\"parameter_update('" . $type_param . "', '" . $sstype_param . "', (this.checked ? 1 : 0));\"/>
            <label for='parameter_" . $type_param . "_" . $sstype_param . "'>
                <span id='parameter_" . $type_param . "_" . $sstype_param . "_activated' style='color:green;" . (! $valeur_param ? " display:none;" : "") . "'>" . $msg['activated'] . "</span>
                <span id='parameter_" . $type_param . "_" . $sstype_param . "_disabled' style='color:red;" . ($valeur_param ? " display:none;" : "") . "'>" . $msg['disabled'] . "</span>
            </label>";
        return $display;
    }

    public static function is_translated(string $type_param = "", string $sstype_param = "", int $id_param = 0)
    {
        if (empty($type_param) && empty($sstype_param)) {
            if (! $id_param) {
                return false;
            }
            $q = "SELECT type_param, sstype_param FROM parametres WHERE id_param = " . $id_param;
            $r = pmb_mysql_query($q);
            if (! pmb_mysql_num_rows($r)) {
                return false;
            }
            $row = pmb_mysql_fetch_assoc($r);
            $type_param = $row['type_param'];
            $sstype_param = $row['sstype_param'];
        }

        $param = $type_param . '_' . $sstype_param;
        if (! in_array($param, parameter::TRANSLATED_PARAMETERS)) {
            return false;
        }
        
        return true;
    }

    /**
     * Recuperation des parametres traduits dans la langue de l'utilisateur
     *
     * @return void
     */
    public static function get_translated_parameters()
    {
        global $lang, $overload_global_parameters;
        $q = "SELECT type_param, sstype_param, trans_text FROM translation
        JOIN parametres ON parametres.id_param = translation.trans_num
        WHERE trans_table='parametres' AND trans_field='valeur_param' AND trans_lang='" . addslashes($lang) . "'
        AND concat(type_param, '_', sstype_param) in  ('" . implode("','", parameter::TRANSLATED_PARAMETERS) . "')";
        $r = pmb_mysql_query($q);
        if ($r && pmb_mysql_num_rows($r)) {
            while ($row = pmb_mysql_fetch_assoc($r)) {
                $type_param = $row['type_param'];
                $sstype_param = $row['sstype_param'];
                $field = $type_param . "_" . $sstype_param;
                if (! isset($overload_global_parameters[$field])) {
                    global ${$field};
                    ${$field} = $row['trans_text'];
                }
            }
        }
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

    public static function get_comment_param($type_param, $sstype_param) {
        $query = "SELECT comment_param FROM parametres WHERE type_param='".addslashes($type_param)."' and sstype_param='".addslashes($sstype_param)."'";
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)) {
            return pmb_mysql_result($result, 0, 'comment_param');
        }
        return '';
    }
}

