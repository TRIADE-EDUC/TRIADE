<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: verif.class.php,v 1.9 2023/04/07 14:25:37 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class verif
{
    private $php_extensions;
    private $php_requirements;
    private $mysql_requirements;
    private $install_msg;
    private $units = array("k"=>"000", "M"=>"000000", "G"=>"000000000");
    private $toogle = array(
        "<img src='images/error.gif' style='height:16px;' />",
        "<img src='images/tick.gif' style='height:16px;'/>",
        "<img src='images/warning.gif' style='height:16px;' />"
    );
    
    const KO = 0;
    const OK = 1;
    const WARN = 2;
    
    public function __construct($messages = [], $php_extensions = [], $php_requirements = [], $mysql_requirements= [])
    {
        if(empty($messages)) {
            require_once __DIR__.'/../../fr/messages.php';
        }
        $this->install_msg = $messages;
        if(empty($php_extensions)) {
            require_once __DIR__.'/../php_extensions.php';
        }
        $this->php_extensions = $php_extensions;
        if(empty($php_requirements)) {
            require_once __DIR__.'/../php_requirements.php';
        }
        $this->php_requirements = $php_requirements;
        if(empty($mysql_requirements)) {
            require_once __DIR__.'/../mysql_requirements.php';
        }
        $this->mysql_requirements = $mysql_requirements;
    }
    
    
    public function checkPhpVersion()
    {
        $phpVersion = $this->php_requirements['version'];
        
        $check = false;
        $version = PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;
        
        if(version_compare($version, $phpVersion['min'], '>=') && version_compare($version, $phpVersion['max'], '<=')){
            $check = true;
        }
        return [
            "check" => $check,
            "required_version" => $phpVersion
        ];
    }
    
    public function checkMySQLVersion($connexion = null)
    {
        $mysqlVersion = $this->mysql_requirements['mysqlVersion'];
        $mariadbVersion = $this->mysql_requirements['mariadbVersion'];
        
        $checked = false;
        is_null($connexion) ? $result = pmb_mysql_query('select VERSION()') : $result = pmb_mysql_query('select VERSION()', $connexion);
        
        $row = pmb_mysql_fetch_all($result);
        
        $explodedVersion = explode("-", $row[0][0]);
        $version = $explodedVersion[0];
        //Gestion du type de serveur de bdd
        switch($explodedVersion[1]){
            case "MariaDB" :
                if( version_compare($version, $mariadbVersion['min'], ">=") && ( empty($mariadbVersion['max']) || version_compare($version, $mariadbVersion['max'], "<=") ) ) {
                    $checked = true;
                }
                $engineType = $explodedVersion[1];
                $requiredVersion = $mariadbVersion;
                break;
            default :
                if( version_compare($version, $mysqlVersion['min'], ">=") && ( empty($mysqlVersion['max']) || version_compare($version, $mysqlVersion['max'], "<=") ) ) {
                    $checked = true;
                }
                $engineType = "MySQL";
                $requiredVersion = $mysqlVersion;
                break;
        }
        return [
            "user_version" => $version,
            "engine_type" => $engineType,
            "checked" => $checked,
            "required_version" => $requiredVersion
        ];
    }
    
    public function checkExtensions()
    {
        $required = $this->php_extensions;
        $extensions = get_loaded_extensions();
        foreach($required as $extName => $ext) {
            $state = (in_array($extName, $extensions) ? verif::OK : verif::KO);
            if( ($state == verif::KO) && !$ext['required'] ) {
                $state = verif::WARN;
            }
            //Vérification de la version
            if( $state != verif::KO && !empty($ext['version']) ) {
                $neededVersion = explode(" ", $ext['version']);
                $userVersion = phpversion($extName);
                //Gestion particulière de libxml car phpversion ne fonctionne pas dessus
                if($extName == "libxml"){
                    $userVersion = LIBXML_DOTTED_VERSION;
                }
                //Si la version utilisateur n'est pas >= à la version recommandée
                if(!version_compare($userVersion, $neededVersion[1], $neededVersion[0])){
                    $state = verif::KO;
                }
            }
            $requirements[] = [
                "name" => $extName,
                "state" => $state,
                "version" => empty($ext['version']) ? '' :$ext['version'],
                "required" => $ext['required']
            ];
        }
        return $requirements;
    }
    
    public function checkPHP()
    {
        $checks = array();
        $php_requirements = $this->php_requirements['options'];
        
        //Un peu de formatage
        foreach($php_requirements as $setupName => &$setupValue){
            
            $setupValue['suggested_value'] = empty($setupValue['suggested_value']) ? '' : $setupValue['suggested_value'];
            $setupValue['required'] = empty($setupValue['required']) ? 0 : 1;
            $setupValue['extension'] = empty($setupValue['extension']) ? '' : $setupValue['extension'];
            
            if( 'integer' == $setupValue['type']) {
                $setupValue['min_value'] = !isset($setupValue['min_value']) ? 'none' : $this->toDecimal($setupValue['min_value']);
                $setupValue['max_value'] = !isset($setupValue['max_value']) ? 'none' : $this->toDecimal($setupValue['max_value']);
            }
            
        }
        
        foreach($php_requirements as $setupName => &$setupValue) {
            
            $state = verif::OK;
            $numeric_userValue = 0;
            $userValue = ini_get($setupName);
            if(false === $userValue) {
                $userValue = 'none';
            }
            if( 'boolean' == $setupValue['type']) {
                $userValue = boolval($userValue) ? "On" : "Off";
            }
            if( 'integer' == $setupValue['type']) {
                $numeric_userValue = $this->toDecimal($userValue);
            }
            // echo "$setupName = $userValue) <br/>";
            
            $done = false;
            
            //Le parametre n'existe pas
            if ( !$done && ('none' == $userValue) ) {
                //Si extension liee non chargee, pas de souci
                if( '' !==  $setupValue['extension'] && !extension_loaded($setupValue['extension']) ) {
                    $userValue = $this->install_msg['req_ext_not_installed'];
                    $state = verif::OK;
                    $done = true;
                }
                if( '' === $setupValue['extension'] ) {
                    $userValue = $this->install_msg['req_no_sql_variable_value'];
                    $state = verif::WARN;
                    $done = true;
                }
            }
            
            // Le parametre doit avoir une valeur
            if( !$done &&  $setupValue['required'] && ( '' == $userValue ) ) {
                $userValue = $this->install_msg['req_no_sql_variable_value'];
                $state = verif::KO;
                $done = true;
            }
            
            //Le parametre est un booleen
            if( !$done && ( 'boolean' == $setupValue['type']) ) {
                if( $userValue != $setupValue['suggested_value']) {
                    $state = verif::WARN;
                }
                $done = true;
            }
            
            //Le parametre doit avoir une valeur minimale
            if( !$done && ( 'integer' == $setupValue['type']) && ('none' !== $setupValue['min_value']) ) {
                if ( (int) $numeric_userValue  < (int) $setupValue['min_value'] ){
                    $state = verif::WARN;
                    $done = true;
                }
            }
            
            //Le parametre doit avoir une valeur maximale
            if( !$done && ( 'integer' == $setupValue['type']) && ('none' !== $setupValue['max_value']) ) {
                if ( (int) $numeric_userValue  > (int) $setupValue['max_value'] ){
                    $state = verif::WARN;
                    $done = true;
                }
            }
            
            //Le parametre est une chaine et doit faire partie d'un ensemble de valeurs
            if(!$done && ( 'string' == $setupValue['type']) && !empty($setupValue['allowed_values']) ) {
                if( !in_array($userValue, $setupValue['allowed_values'])) {
                    $state = verif::WARN;
                    $done = true;
                }
            }
            
            //Le parametre est un set et doit contenir uniquement certaines valeurs
            if(!$done && ( 'set' == $setupValue['type']) && !empty($setupValue['allowed_values']) ) {
                $values = explode(',', $userValue);
                while(count($values) ) {
                    if( !in_array(array_shift($values), $setupValue['allowed_values']) ) {
                        $state = verif::WARN;
                    }
                }
            }
            
            $checks[] = [
                "name" => $setupName,
                "value" => $userValue,
                "suggestedValue" => $setupValue['suggested_value'],
                'state' => $state,
            ];
        }
        return $checks;
    }
    
    
    public function checkMySQL($connexion = null)
    {
        
        $checks = array();
        $mysql_requirements = $this->mysql_requirements['variables'];
        
        //Un peu de formatage
        foreach($mysql_requirements as $setupName => &$setupValue){
            
            $setupValue['mode'] = empty($setupValue['mode']) ? 'session' : $setupValue['mode'];
            $setupValue['suggested_value'] = empty($setupValue['suggested_value']) ? '' : $setupValue['suggested_value'];
            $setupValue['required'] = empty($setupValue['required']) ? 0 : 1;
            
            if( 'integer' == $setupValue['type']) {
                $setupValue['min_value'] = !isset($setupValue['min_value']) ? 'none' : $this->toDecimal($setupValue['min_value']);
                $setupValue['max_value'] = !isset($setupValue['max_value']) ? 'none' : $this->toDecimal($setupValue['max_value']);
            }
        }
        
        foreach($mysql_requirements as $setupName => &$setupValue){
            
            $state = verif::OK;
            
            $query = 'show variables like "' . $setupName . '"';
            $result = pmb_mysql_query($query, $connexion);
            if(pmb_mysql_num_rows($result)) {
                $row = pmb_mysql_fetch_all($result);
                $userValue = $row[0][1];
            } else {
                $userValue = 'none';
            }
            //echo "$setupName = $userValue <br/>";
            $done = false;
            
            //Le parametre n'existe pas
            if ( !$done && 'none' == $userValue) {
                $userValue = $this->install_msg['req_no_sql_variable_value'];
                $state = verif::WARN;
                $done = true;
            }
            
            // Le parametre doit avoir une valeur
            if( !$done &&  $setupValue['required'] && ( '' === $userValue ) ) {
                $state = verif::KO;
                $done = true;
            }
            
            //Le parametre doit avoir une valeur minimale
            if( !$done && ( 'integer' == $setupValue['type']) && ('none' !== $setupValue['min_value']) ) {
                if ( (int) $userValue  < (int) $setupValue['min_value'] ){
                    $state = verif::WARN;
                    $done = true;
                }
            }
            
            //Le parametre doit avoir une valeur maximale
            if( !$done && ( 'integer' == $setupValue['type']) && ('none' != $setupValue['max_value']) ) {
                if ( (int) $userValue  > (int) $setupValue['max_value'] ){
                    $state = verif::WARN;
                    $done = true;
                }
            }
            
            //Le parametre est une chaine et doit faire partie d'un ensemble de valeurs
            if( !$done && ( 'string' == $setupValue['type']) && !empty($setupValue['allowed_values']) ) {
                if( !in_array($userValue, $setupValue['allowed_values'])) {
                    $state = verif::WARN;
                    $done = true;
                }
            }
            
            //Le parametre est un set et doit contenir uniquement certaines valeurs
            if( !$done && ( 'set' == $setupValue['type']) && !empty($setupValue['allowed_values']) ) {
                $values = explode(',', $userValue);
                while(count($values) ) {
                    if( !in_array(array_shift($values), $setupValue['allowed_values']) ) {
                        $state = verif::WARN;
                    }
                }
            }
            
            //Commentaire
            $comment = '';
            switch (true) {
                //Erreur
                case (verif::KO == $state) :
                    $comment = 'req_mysql_variable_error';
                    break;
                    // Warning sur parametre session
                case ( (verif::WARN == $state) && ('session' == $setupValue['mode']) ) :
                    $comment = 'req_mysql_session_variable_warning';
                    break;
                    //Warning sur parametre global
                case ( (verif::WARN == $state) && ('global' == $setupValue['mode']) ) :
                    $comment = 'req_mysql_global_variable_warning';
                    break;
                    //Warning sur parametre statique
                case ( (verif::WARN == $state) && ('static' == $setupValue['mode']) ) :
                    $comment = 'req_mysql_static_variable_warning';
                    break;
                case (verif::OK == $state) :
                default :
                    break;
            }
            
            $checks[] = [
                'name' => $setupName,
                'value' => ('' === $userValue) ? $this->install_msg['req_no_sql_variable_value'] : $userValue,
                'state' => $state,
                'comment' => $comment,
                
                'type' => $setupValue['type'],
                'mode' => $setupValue['mode'],
                'suggestedValue' =>  $setupValue['suggested_value'],
                'min_value' => empty($setupValue['min_value']) ? '' : $setupValue['min_value'],
                'max_value' => empty($setupValue['max_value']) ? '' : $setupValue['max_value'],
                'allowed_values' => empty($setupValue['allowed_values']) ? [] : $setupValue['allowed_values'],
                'required' => $setupValue['required'],
                'pmb_var' => empty($setupValue['pmb_var']) ? '' : $setupValue['pmb_var'],
            ];
        }
        return $checks;
    }
    
    
    protected function toDecimal($val = '')
    {
        if(preg_match("/^[0-9]+$/", $val)) {
            return $val;
        }
        $val = (string) $val;
        $unit = (string) $val;
        $val = (int) preg_replace("/[^0-9]/", "", $val);
        $unit = preg_replace("/[0-9]/", "", $unit);
        switch($unit) {
            case 'k' :
                $val = $val * 1024;
                break;
            case 'M' :
                $val = $val * 1024 * 1024;
                break;
            case 'G' :
                $val = $val * 1024 * 1024 * 1024;
                break;
            case 'T' :
                $val = $val * 1024 * 1024 * 1024 * 1024;
                break;
        }
        return $val;
    }
    
}
