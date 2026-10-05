<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_password_rules.class.php,v 1.5.2.2 2024/09/09 07:15:57 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $class_path, $include_path;
global $admin_empr_password_rules_tpl;
global $lang, $charset;

require_once "{$class_path}/password/password.class.php";
require_once "{$include_path}/templates/empr_password_rules.tpl.php";


class empr_password_rules
{
    protected static $rules = [];
    protected static $msg = [];
    protected static $templates = [];
    protected static $enabled_rules = [];

    /**
     * Affiche le formulaire de definition des regles de mots de passe lecteur
     *
     */
    public static function get_form()
    {
        global $lang, $charset, $admin_empr_password_rules_tpl;

        static::$rules = password::get_password_rules('empr');
        static::$enabled_rules = password::get_enabled_rules('empr');

        static::$msg = password::get_messages('empr', $lang);
        static::$templates = $admin_empr_password_rules_tpl;

        $form = static::$templates['form'];

        foreach (static::$rules as $rule) {
            //description
            $form_row = static::$templates['row'];
            $form_row = str_replace('<!-- desc -->', static::$msg[$rule['id'].'_desc'], $form_row);

            //variable
            if (!empty($rule['var'])) {
				$form_var = "";
                foreach ($rule['var'] as $key => $var) {
                    $var_value = empty($var['default']) ? '' : $var['default'];
                    if (isset($var['name']) && !empty(static::$enabled_rules[$rule['id']]['value'][$var['name']])) {
						$var_value = static::$enabled_rules[$rule['id']]['value'][$var['name']];
					} elseif ($key == 0 && !empty(static::$enabled_rules[$rule['id']]['value'])) {
                        $var_value = static::$enabled_rules[$rule['id']]['value'][0];
                    }

                    if (('string' == $var['type']) || ('textarea' == $var['type'])) {
                        $var_value = htmlentities($var_value, ENT_QUOTES, $charset);
                    }
                    if ('integer' == $var['type']) {
                        $var_value = intval($var_value);
                    }

					$id = "empr_password_rule_".$rule['id']."[value]";
					$name = "empr_password_rule_".$rule['id']."[value]";
					if (isset($var['name'])) {
						$name .= "[".$var['name']."]";
						$id .= "[".$var['name']."]";
					}

					if (isset($var['mandatory'])) {
						$mandatory = 'required';
					} else {
						$mandatory = '';
					}

                    $form_var .= str_replace(
						['!!id!!', '!!name!!', '!!value!!', '!!mandatory!!'],
						[$id, $name, $var_value, $mandatory],
						static::$templates['var'][$var['type']]
					);
                }
                $form_row = str_replace('<!-- var -->', $form_var, $form_row);
            }

            //case a cocher
            $checked = '';
            if (!empty(static::$enabled_rules[$rule['id']]['enabled'])) {
                $checked = 'checked';
            }
            $form_checkbox = static::$templates['checkbox'];
            $form_checkbox = str_replace('!!id!!', "empr_password_rule_".$rule['id']."[chk]", $form_checkbox);
            $form_checkbox = str_replace('!!name!!', "empr_password_rule_".$rule['id']."[chk]", $form_checkbox);
            $form_checkbox = str_replace('!!checked!!', $checked, $form_checkbox);
            $form_row = str_replace('<!-- enabled -->', $form_checkbox, $form_row);
            $form = str_replace('<!-- rows -->', $form_row.'<!-- rows -->', $form);
        }

        // On affiche un message s'il y a une authentification externe
        $form = str_replace('!!admin_empr_password_no_rules_ext_auth!!', (password::check_external_authentication() ? "display:block" : "display:none"), $form);

        echo $form;
    }


    /**
     * Sauvegarde le formulaire de definition des regles de mots de passe lecteur
     */
    public static function save()
    {
        static::$rules = password::get_password_rules('empr');
        static::$enabled_rules = [];
        foreach (static::$rules as $k=>$rule) {
            $rule_form_id = 'empr_password_rule_'.$rule['id'];
            global ${$rule_form_id};

            static::$enabled_rules[$rule['id']]['enabled'] = 0;
            if (!empty(${$rule_form_id}['chk'])) {
                static::$enabled_rules[$rule['id']]['enabled'] = 1;
            }
            $value = '';
            if (!empty(${$rule_form_id}['value'])) {
                $value = stripslashes_array(${$rule_form_id}['value']);
            }
            static::$enabled_rules[$rule['id']]['value']  = $value;
            static::$enabled_rules[$rule['id']]['type'] = static::$rules[$k]['type'];
            switch (static::$rules[$k]['type']) {
                case 'class':
                    static::$enabled_rules[$rule['id']]['class'] = static::$rules[$k]['class'];
                    break;
                case 'regexp':
                    static::$enabled_rules[$rule['id']]['regexp'] = static::$rules[$k]['regexp'];
                    break;
                default:
                    trigger_error('Unknown rule type ' . static::$rules[$k]['type'], E_USER_WARNING);
                    break;
            }
        }
        password::save_enabled_rules('empr', static::$enabled_rules);
    }

    /**
     * Reinitialise les regles
     *
     * @return void
     */
    public static function reset()
    {
        static::$rules = password::get_password_rules('empr');
        static::$enabled_rules = [];

        foreach (static::$rules as $k => $rule) {
            if (!empty($rule['var'])) {
                $value = [];
                foreach ($rule['var'] as $key => $var) {
                    $var_value = empty($var['default']) ? '' : $var['default'];
                    if (!empty($var['name'])) {
                        $value[$var['name']] = $var_value;
                    } elseif ($key == 0 && !empty(static::$enabled_rules[$rule['id']]['value'])) {
                        $value[] = $var_value;
                    }
                }
            }

            static::$enabled_rules[$rule['id']]['value']  = $value;
            static::$enabled_rules[$rule['id']]['type'] = static::$rules[$k]['type'];
            switch (static::$rules[$k]['type']) {
                case 'class':
                    static::$enabled_rules[$rule['id']]['class'] = static::$rules[$k]['class'];
                    break;
                case 'regexp':
                    static::$enabled_rules[$rule['id']]['regexp'] = static::$rules[$k]['regexp'];
                    break;
                default:
                    trigger_error('Unknown rule type ' . static::$rules[$k]['type'], E_USER_WARNING);
                    break;
            }
        }

        password::save_enabled_rules('empr', static::$enabled_rules);
    }
}
