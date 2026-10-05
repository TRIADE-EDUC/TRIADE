<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmb.php,v 1.4.4.1 2025/02/07 13:49:04 qvarin Exp $

$base_path = ".";
require_once "{$base_path}/includes/init.inc.php";

// fichiers nécessaires au bon fonctionnement de l'environnement
require_once "{$base_path}/includes/common_includes.inc.php";

global $class_path, $from, $opac_empr_password_salt;
global $hash, $url, $id;

if ('' == $opac_empr_password_salt) {
    password::gen_salt_base();
}

if (!empty($hash) && !empty($url) && !empty($id)) {
    require_once "{$class_path}/campaigns/campaigns_controller.class.php";
    campaigns_controller::proceed($hash, $url, $id);
} elseif (!empty($hash) && !empty($url)) {
    if (!isset($from)) {
        $from = '';
    }

    if ($hash == md5("{$opac_empr_password_salt}_{$url}_{$from}")) {
        //Enregistrement du log
        generate_log('pmb', [], true);

        header('Location: '.html_entity_decode($url));
    } else {
        http_response_code(404);
    }
} else {
    http_response_code(404);
}
