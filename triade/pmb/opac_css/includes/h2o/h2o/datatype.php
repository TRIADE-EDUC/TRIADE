<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: datatype.php,v 1.10.4.1 2025/03/07 15:33:04 dbellamy Exp $

class StreamWriter {
    public $buffer = array();
    public $close;

    public function __construct() {
        $this->close = false;
    }

    public function write($data) {
        if ($this->close)
            new Exception('tried to write to closed stream');
        $this->buffer[] = $data;
    }

    public function close() {
        $this->close = true;
        return implode('', $this->buffer);
    }
}

class Evaluator
{

    static public function gt($l, $r)
    {
        return $l > $r;
    }

    static public function ge($l, $r)
    {
        return $l >= $r;
    }

    static public function lt($l, $r)
    {
        return $l < $r;
    }

    static public function le($l, $r)
    {
        return $l <= $r;
    }

    static public function eq($l, $r)
    {
        return $l == $r;
    }

    static public function ne($l, $r)
    {
        return $l != $r;
    }

    static public function not_($bool)
    {
        return ! $bool;
    }

    static public function and_($l, $r)
    {
        return ($l && $r);
    }

    static public function or_($l, $r)
    {
        return ($l || $r);
    }

    // Currently only support single expression with no preceddence ,no boolean expression
    // [expression] = [optional binary] ? operant [ optional compare operant]
    // [operant] = variable|string|numeric|boolean
    // [compare] = > | < | == | >= | <=
    // [binary] = not | !
    static public function exec($args, $context)
    {
        $argc = count($args);
        // AR - On garde tout si plus de 3 arguments
        if ($argc <= 3) {
            $first = array_shift($args);
            $first = $context->resolve($first);
        }
        switch ($argc) {
            case 1:
                return $first;
            case 2:
                if (is_array($first) && isset($first['operator']) && $first['operator'] == 'not') {
                    $operant = array_shift($args);
                    $operant = $context->resolve($operant);
                    return ! ($operant);
                }
            case 3:
                list ($op, $right) = $args;
                $right = $context->resolve($right);
                if (in_array($op['operator'], ['not','and','or'])) {
                    $op['operator'] .= "_";
                }
                return call_user_func(array(
                    "Evaluator",
                    $op['operator']
                ), $first, $right);
            default:
                if($argc > 0){
                    // AR - Aller on la tente comme ça
                    $state = "start";
                    for ($i = 0; $i < $argc; $i ++) {
                        switch ($state) {
                            case "start":
                                $sub = [];
                                $operant = null;
                                $result = null;
                                $left = null;
                                $right = null;
                                $i --;
                                if ((!empty($args[$i]) && is_array($args[$i]) && isset($args[$i]['operator'])) && in_array($args[$i]['operator'], ['not','and','or'])) {
                                    $state = 'operator';
                                } else {
                                    $state = "start_expression";
                                }
                                break;
                            case "start_expression" :
                                if ((is_array($args[$i]) && isset($args[$i]['operator'])) && in_array($args[$i]['operator'], ['not','and','or'])) {
                                    $state = 'operator';
                                    $i--;
                                }else{
                                    $sub[] = $args[$i];
                                    $state = "expression";
                                }
                                break;
                            case "expression":
                                if ((is_array($args[$i]) && isset($args[$i]['operator'])) && in_array($args[$i]['operator'], ['not','and','or'])) {
                                    $state = 'operator';
                                    $i --;
                                } else {
                                    $sub[] = $args[$i];
                                }
                                break;
                            case "operator":
                                $result = self::exec($sub, $context);
                                $sub = [];
                                if ($left === null) {
                                    $left = $result;
                                } else if ($right === null) {
                                    $right = $result; 
                                    if (in_array($operant['operator'], ['not','and','or'])) {
                                        $operant['operator'] .= "_";
                                    }
                                    $left = call_user_func(array("Evaluator",$operant['operator']), $left, $right);
                                    $right = null;
                                }
                                $operant = $args[$i];
                                $state = "start_expression";
                                break;
                        }
                    }
                    if (count($sub) > 0) {
                        if (in_array($operant['operator'], ['not','and','or'])) {
                            $operant['operator'] .= "_";
                        }
                        if (isset($operant) && isset($operant['operator'])) {
                            $left = call_user_func(array("Evaluator",$operant['operator']), $left, self::exec($sub, $context));
                        }
                    }
                    return $left;
                }
                return false;
        }
    }
}

/**
 * $type of token, Block | Variable
 */
class H2o_Token {
    public $type;
    public $content;
    public $result;
    public $position;

    public function __construct ($type, $content, $position) {
        $this->type = $type;
        $this->content = $content;
        $this->result='';
        $this->position = $position;
    }

    public function write($content){
        $this->result= $content;
    }
}

/**
 * a token stream
 */
class TokenStream  {
    public $pushed;
    public $stream;
    public $closed;
    public $c;

    public function __construct() {
        $this->pushed = array();
        $this->stream = array();
        $this->closed = false;
    }

    public function pop() {
        if (count($this->pushed))
        return array_pop($this->pushed);
        return array_pop($this->stream);
    }

    public function feed($type, $contents, $position) {
        if ($this->closed)
            throw new Exception('cannot feed closed stream');
        $this->stream[] = new H2o_Token($type, $contents, $position);
    }

    public function push($token) {
        if (is_null($token))
            throw new Exception('cannot push NULL');
        if ($this->closed)
            $this->pushed[] = $token;
        else
            $this->stream[] = $token;
    }

    public function close() {
        if ($this->closed)
        new Exception('cannot close already closed stream');
        $this->closed = true;
        $this->stream = array_reverse($this->stream);
    }

    public function isClosed() {
        return $this->closed;
    }

    public function current() {
        return $this->c ;
    }

    public function next() {
        return $this->c = $this->pop();
    }
}

class H2o_Info {
    public $h2o_safe = array('filters', 'extensions', 'tags');
    public $name = 'H2o Template engine';
    public $description = "Django inspired template system";
    public $version = H2O_VERSION;

    public function filters() {
        return array_keys(H2o::$filters);
    }
    
    public function tags() {
        return array_keys(H2o::$tags);
    }
    
    public function extensions() {
        return array_keys(H2o::$extensions);
    }
}

/**
 * Functions
 */
function sym_to_str($string) {
    return substr($string, 1);
}

function is_sym($string) {
    return isset($string[0]) && $string[0] === ':';
}

function symbol($string) {
    return ':'.$string;
}

function strip_regex($regex, $delimiter = '/') {
    return substr($regex, 1, strrpos($regex, $delimiter)-1);
}
?>