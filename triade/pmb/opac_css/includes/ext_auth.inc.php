<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ext_auth.inc.php,v 1.3 2023/07/13 13:14:58 dbellamy Exp $
//
// +-------------------------------------------------+
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die('no access');
}

use Pmb\Authentication\Models\AuthenticationHandler;

global $ext_auth, $empty_pwd;
$ext_auth = false;
$empty_pwd = false;

try {

    $ext_auth_configs = AuthenticationHandler::getConfigs('opac');
    if(empty($ext_auth_configs)) {
        throw new Exception("no configuration enabled");
    }
    foreach($ext_auth_configs as $config) {
        $ext_auth_handler = new AuthenticationHandler(
            [
                'env' => 'opac',
                'config' => $config,
                'log' => 0,
            ]
        );
        $ext_auth_handler->run();
        if($ext_auth) {
            continue;
        }
    }

} catch (Exception $e) {
}
