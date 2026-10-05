<?php

// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: parse_format.class.php,v 1.24.2.1.2.1 2025/04/30 09:49:41 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $include_path;
require_once $include_path . "/misc.inc.php";

class parse_format
{
    public $func_format;
    public $var_format;
    public $var_return;
    public $in_relation;
    public $erreur; //Erreur
    public $var_set;
    public $cmd;
    public $environnement;

    public $var_set_name;

    /**
	 * Instances
	 *
	 * @var array
	 */
    protected static $instances;

    /**
	 * Constructeur
	 *
	 * @param string $filename (optional, default = "interpreter.inc.php")
	 * @param boolean $in_relation (optional, default = false)
	 */
    public function __construct($filename = 'interpreter.inc.php', $in_relation = false)
    {
        global $include_path, $func_format, $var_format;

        require_once $include_path."/interpreter/$filename";

        $this->func_format = $func_format;
        $this->var_format = $var_format;
        $this->var_return = '';
        $this->in_relation = $in_relation;
        $this->erreur = 0;

        // Je fais pas le teste en GESTION,
        // car on a des formulaire qui post des fonctions du parseur HTML
        // $this->check_request();
    }

    /**
     * Check if a value can be called as a function
     *
     * @return void
     */
    private function check_request()
    {
        $request = array_merge_recursive($_GET, $_POST, $_FILES);
        foreach ($request as $value) {
            if ($this->is_callable($value)) {
                // On supprime se qui a ete print
                ob_clean();

                // On stoppe toutes tentatives d'appel a des fonctions du parseur HTML
                // provenant du client (GET, POST, FILES)
                http_response_code(403);
                exit();
            }
        }
    }

    /**
     * Verify that a value can be called as a function
     *
     * @param array|string $val
     * @return boolean
     */
    private function is_callable($val)
    {
        if (is_array($val)) {
            reset($val);

            foreach ($val as $k => $_) {
                if ($this->is_callable($val[$k])) {
                    return true;
                }
            }
        } else {
            $functions = array_keys($this->func_format);
            // #set() est une fonction specifique au parseur HTML pour definir une global
            $functions[] = 'set';

            foreach ($functions as $function_name) {
                if (strpos($val, '#' . $function_name . '(') !== false) {
                    return true;
                }
            }
        }
        return false;
    }

	/**
	 * Executer une fonction
	 *
	 * @param string $function_name
	 * @param array $param_name
	 * @param mixed $param_number (not used)
	 * @return mixed
	 */
    public function exec_function($function_name, $param_name, $param_number)
    {
        if (empty($this->func_format[$function_name])) {
            trigger_error("Function $function_name not found", E_USER_WARNING);
        }

        if (!$this->in_relation || ($this->in_relation && $function_name != "get_childs_in_tpl" && $function_name != "get_parents_in_tpl")) {
            if(empty($this->func_format[$function_name])) {
                return " $function_name not found ";
            }
            return $this->func_format[$function_name]($param_name, $this);
        } else {
            return "";
        }
    }

    public function exec($cmd, &$i)
    {
        $state = 0;
        $ret = "";
        $function_name = '';

        $param_uneval = '';
        $strlen_cmd = strlen($cmd);

        for ($i; $i < $strlen_cmd; $i++) {
            switch ($state) {
                case '0': //state Normal
                    switch ($cmd[$i]) {
                        case '$':
                            $state = "get_param_name";
                            $param_name = '';
                            break;
                        case '#':
                            $state = "get_token";
                            break;
                        default:
                            break;
                    }
                    break;
                case 'get_token': //get param name
                    if (
                        isset($cmd[$i - 2]) &&
                        ($cmd[$i - 2] == '&') &&
                        ($cmd[$i - 1] == '#') &&
						preg_match('/\d/', $cmd[$i])
                    ) {
                        //Il n'y a pas de fonction commençant par un chiffre, il s'agit d'une entité html
                        return "#".$cmd[$i];
                    }
                    switch ($cmd[$i]) {
                        case ' ':
                        case '"':
                        case "'":
                        case '<':
                        case '>':
                        case "\r":
                        case "\n":
                        case "\t":
                            return "#".$function_name.$cmd[$i];  //La liste de caractères précédente permet de déterminer qu'on doit sortir de l'interpretation
                            break;
                        case '{':
                            if ($cmd[$i - 1] == '#') { // fin d'un parametre à ne pas évaluer par };
                                $state = 'get_sub_param';
                            } else {
                                return	$function_name.'{';
                            }
                            break;
                        case '(':
                            $param_number = 0;
                            $param_name[$param_number] = '';
                            $state = "get_function_param";
                            if (($function_name == "SET") || ($function_name == "set")) {
                                $this->var_set = 1;
                            } elseif (empty($this->func_format[$function_name])) {
                                return "#".$function_name.$cmd[$i];
                            } // ce n'est pas une fonction existante, on retourne la chaine
                            break;
                        default:
                            $function_name .= $cmd[$i];
                            break;
                    }
                    break;
                case 'get_sub_param':
                    switch ($cmd[$i]) {
                        case '}' :
                            if ($cmd[$i + 1] == ';') { // fin d'un parametre à ne pas évaluer par };
                                $i++;
                                return $param_uneval;
                            }
                            //break; -> Il faut garder le } si il n'est pas suivit d'un ; voir expl de {explnum_id} ci-dessous
                            //Expl: #group(#linked_id(down,a,d);,0,<br />,#{- <b><a href="#opac_url();doc_num.php?explnum_id=#expl_num_with_tpl(1,/,0,{explnum_id},1,);">#title();</a></b>};);
                            // no break
                        default:
                            $param_uneval .= $cmd[$i];
                            $state = 'get_sub_param';
                            break;
                    }
                    break;
                case 'get_param_name': //get param name

                    switch ($cmd[$i]) {
                        case ' ':
                        case '"':
                        case "'":
                        case '<':
                        case '>':
                        case "\r":
                        case "\n":
                        case "\t":
                            return "$".$param_name.$cmd[$i];
                            break;
                        case ';':
                            if($this->var_set) {
                                $this->var_set_name = $param_name;
                                $this->var_set = 0;
                            }
                            return $this->var_format[$param_name];
                            break;
                        case '=':
                            $this->var_return = $param_name;
                            return $param_name;
                            break;
                        default:
                            $param_name .= $cmd[$i];
                            break;
                    }
                    break;
                case 'get_function_name': //get param name

                    switch ($cmd[$i]) {
                        case ' ':
                        case '"':
                        case "'":
                        case '<':
                        case '>':
                        case "\r":
                        case "\n":
                        case "\t":
                        case ";":
                            return "#".$function_name.$cmd[$i];
                            break;
                        case '(':
                            $param_number = 0;
                            $param_name[$param_number] = '';
                            $state = "get_function_param";
                            if(($function_name == "SET") || ($function_name == "set")) {
                                $this->var_set = 1;
                            }
                            break;
                        default:
                            $function_name .= $cmd[$i];
                            break;
                    }
                    break;
                case 'get_function_param': //get param name

                    switch ($cmd[$i]) {
                        case '$':
                        case '#':
                            if ($param_name[$param_number]) {
                                $param_name[$param_number] .= $this->exec($cmd, $i);
                            } else {
                                $param_name[$param_number] = $this->exec($cmd, $i);
                            }
                            break;
                        case ')':
                            if ($cmd[$i + 1] == ';') { // fin d'une fonction par );
                                $i++;
                                if(($function_name == "SET") || ($function_name == "set")) {
                                    $this->var_format[$this->var_set_name] = $param_name[1];
                                    $this->set_global($param_name[0], $param_name[1] ?? '');
                                    $this->var_set = 0;
                                    return '';
                                } else {
                                    return ($this->exec_function($function_name, $param_name, $param_number));
                                }

                            } else {
                                $param_name[$param_number] .= ')';
                            }
                            break;
                        case ',':
                            $param_number++;
                            $param_name[$param_number] = '';
                            break;
                        default:
                            if(($cmd[$i] == '\\') && (($i + 1) < $strlen_cmd)) {
                                $i++;
                            }
                            $param_name[$param_number] .= $cmd[$i];
                            break;
                    }
                    break;
                default:
                    break;
            }
        }
        return $ret;
    }

    public function exec_cmd($no_escape = false)
    {
        $cmd = $this->cmd;
        $strlen_cmd = strlen($cmd);
        $ret = "";

        for ($i = 0; $i < $strlen_cmd; $i++) {
            switch ($cmd[$i]) {
                case '$':
                    if (isset($cmd[$i + 1]) && ($cmd[$i + 1] == '(' || $cmd[$i + 1] == '=')) {
                        $ret .= '$'; // cas de jQuery de type : "$(this).hide();" ou d'une règle CSS de type "[id$=_footer]"
                        break;
                    }
                    // no break
                case '#':
                    $return = $this->exec($cmd, $i);

                    if(!$this->var_return) {
                        //C'est le retour pour afficher
                        $ret .= $return;

                    } else {
                        //C'est une affectation d'une variable
                        $this->var_format[$this->var_return] = $this->exec($cmd, $i);
                        $this->var_return = '';
                    }
                    if ($this->erreur == 1) {
                        return -1;
                    }
                    break;
                default:
                    if (!$no_escape) {
                        if(($cmd[$i] == '\\') && (($i + 1) < $strlen_cmd)) {
                            $i++;
                        }
                    }
                    $ret .= $cmd[$i];
                    break;
            }
        }
        return $ret;
    }


    public function exec_cmd_conso()
    {
        $ret = '';
        $cmd = $this->cmd;
        $strlen_cmd = strlen($cmd);
        for ($i = 0; $i < $strlen_cmd; $i++) {
            switch ($cmd[$i]) {
                case '$':
                case '#':
                    $return = $this->exec($cmd, $i);

                    if(!$this->var_return && !is_array($return)) {
                        //C'est le retour pour afficher
                        $ret .= $return;

                    } elseif(is_array($return)) {
                        $ret = $return;
                    } else {
                        //C'est une affectation d'une variable
                        $this->var_format[$this->var_return] = $this->exec($cmd, $i);
                        $this->var_return = '';
                    }
                    if ($this->erreur == 1) {
                        return -1;
                    }
                    break;
                default:
                    if(($cmd[$i] == '\\') && (($i + 1) < $strlen_cmd)) {
                        $i++;
                    }
                    $ret .= $cmd[$i];
                    break;
            }
        }
        return $ret;
    }

    /**
	 * Get a instance
	 *
	 * @param string $filename (optional, default = "interpreter.inc.php")
	 * @param boolean $in_relation (optional, default = false)
	 * @return parse_format
	 */
    public static function get_instance($filename = 'interpreter.inc.php', $in_relation = false)
    {
        if(!isset(static::$instances[$filename][$in_relation])) {
            static::$instances[$filename][$in_relation] = new parse_format($filename, $in_relation);
        }
        return static::$instances[$filename][$in_relation];
    }

    /**
     * Set a global variable
     *
     * @param string $name
     * @param string $value (optional, default = "")
     * @return void|false (false on error)
     */
    private function set_global(string $name, string $value = "")
    {
        global $forbidden_overload, $known_int_variables;

        // Le nom de la variable n'est pas autorise a etre definie
        if (in_array($name, $forbidden_overload)) {
            trigger_error("Forbidden variable $name", E_USER_ERROR);
            return false;
        }

        // Le nom de la variable ne respecte pas la norme de PHP
        if (!preg_match("/^[a-zA-Z_]\w{0,255}$/", $name)) {
            if (strlen($name) > 10) {
                $error_name_var = substr($name, 0, 10) . "...";
            } else {
                $error_name_var = $name;
            }

            trigger_error("Variable name invalid: #set(" . $error_name_var . ")", E_USER_ERROR);
            return false;
        }

        // Gestion de la valeur
        if (in_array($name, $known_int_variables) && $value != "") {
            $value = intval($value);
        }

        global ${$name};
        ${$name} = $value;
    }
}
