<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: watch_task.php,v 1.2 2024/04/11 08:26:23 dbellamy Exp $


//file_put_contents("/tmp/watch.log", "Demarrage watch_task".PHP_EOL);

if('cli' != PHP_SAPI) {
    //file_put_contents("/tmp/watch.log", "ERREUR > Not in cli mode".PHP_EOL, FILE_APPEND);
    die;
}

use Pmb\Common\Library\System\System;


if(!isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = '';
}

$base_path="..";
$base_title="";
$base_noheader=1;
$base_nocheck=1;
$base_nobody=1;
$base_nosession=1;

$include_path = $base_path."/includes";

$id_tache = 0;
if(isset($argv[1])) {
    $id_tache = intval($argv[1]);
}
if(!$id_tache) {
    //file_put_contents("/tmp/watch.log", "ERREUR > id_tache = $id_tache".PHP_EOL, FILE_APPEND);
    die;
}

$user_id = 0;
if (isset($argv[2])) {
    $user_id = intval($argv[2]);
}
if(!$user_id) {
    //file_put_contents("/tmp/watch.log", "ERREUR > user_id = $user_id".PHP_EOL, FILE_APPEND);
    die;
}

$connectors_out_source_id = 0;
if (isset($argv[3])) {
    $connectors_out_source_id = intval($argv[3]);
}
if(!$connectors_out_source_id) {
    //file_put_contents("/tmp/watch.log", "ERREUR > connectors_out_source_id = $connectors_out_source_id".PHP_EOL, FILE_APPEND);
    die;
}

$database = '';
if (isset($argv[4]) && $tmp = trim($argv[4])) {
    $database = $tmp;
}

require_once $include_path . "/init.inc.php";
global $dbh;
if(0 == $dbh) {
    //file_put_contents("/tmp/watch.log", "ERREUR > no database".PHP_EOL, FILE_APPEND);
    die;
}

$host_name = System::getHostName();

//Verification tache_id
$checked_id_process = 0;
$checked_id_tache = 0;
$checked_status = 0;
$checked_host_name = '';

$row = '';
$q = 'select id_tache, id_process, status, host_name from taches where id_tache=' . $id_tache;
$r= pmb_mysql_query($q);
if(pmb_mysql_num_rows($r)) {
    $row = pmb_mysql_fetch_assoc($r);
    $checked_id_tache = $row['id_tache'];
    $checked_id_process = $row['id_process'];
    $checked_status = $row['status'];
    $checked_host_name = $row['host_name'];

}

if(!$checked_id_tache || !$checked_id_process) {
    //file_put_contents("/tmp/watch.log", "ERREUR > checked_id_tache = $checked_id_tache".PHP_EOL, FILE_APPEND);
    //file_put_contents("/tmp/watch.log", "ERREUR > checked_id_process = $checked_id_process".PHP_EOL, FILE_APPEND);
    die;
}
if($checked_host_name != $host_name) {
    //file_put_contents("/tmp/watch.log", "ERREUR > checked_host_name = $checked_host_name".PHP_EOL, FILE_APPEND);
    die;
}

//Verification user_id
$checked_user_id = 0;
$q = "SELECT userid FROM users
    LEFT JOIN es_esgroups on userid=esgroup_pmbusernum
    LEFT JOIN es_esusers on esgroup_id=esuser_groupnum
    WHERE esuser_id = $user_id";
$r = pmb_mysql_query($q);
if(pmb_mysql_num_rows($r)) {
    $checked_user_id = pmb_mysql_result($r, 0, 0);
}
if(!$checked_user_id) {
    //file_put_contents("/tmp/watch.log", "ERREUR > checked_user_id = $checked_user_id".PHP_EOL, FILE_APPEND);
    die;
}

//Verification connector_out_source_id
$checked_connectors_out_source_id = 0;
$q = "select connectors_out_source_id from connectors_out_sources where connectors_out_source_id=" . $connectors_out_source_id;
$r = pmb_mysql_query($q);
if(pmb_mysql_num_rows($r)) {
    $checked_connectors_out_source_id = pmb_mysql_result($r, 0, 0);
}
if(!$checked_connectors_out_source_id) {
    //file_put_contents("/tmp/watch.log", "ERREUR > checked_connectors_out_source_id = $checked_connectors_out_source_id".PHP_EOL, FILE_APPEND);
    die;
}




$done = false;
/* temps d'attente entre 2 verifications */
$waiting_time = 60;
/* temps de fonctionnement */
$running_time = 0;

while(!$done) {

    sleep($waiting_time);
    $running_time += $waiting_time;

    $status = 0;
    $current_id_process = 0;
    $q = 'select status, id_process from taches where id_tache=' . $id_tache;
    $r= pmb_mysql_query($q);
    if(pmb_mysql_num_rows($r)) {
        $row = pmb_mysql_fetch_assoc($r);
        $status = $row['status'];
        $current_id_process = $row['id_process'];
    }

    switch($status) {

        case scheduler_task::WAITING :

            if($running_time > $waiting_time) {
                //Temps maximum d'attente de demarrage de la tache atteint (> 60s)
                //file_put_contents("/tmp/watch.log", "ERREUR > Temps maximum d'attente de demarrage de la tache atteint (> 60s)".PHP_EOL, FILE_APPEND);
                $done = true;
            }
            break;

        case scheduler_task::RUNNING :

            $check_process = System::checkProcess($current_id_process);
            if (!$check_process) {
                //Processus non trouve
                //file_put_contents("/tmp/watch.log", "ERREUR > Processus non trouve".PHP_EOL, FILE_APPEND);
                $done = true;
            } else {
                //Processus trouve, mise a jour du timestamp alive_at
                $alive_at = date('Y-m-d H:i:s');
                $q = "update taches set alive_at = '".$alive_at."' where id_tache=".$id_tache;
                pmb_mysql_query($q);
                //file_put_contents("/tmp/watch.log", "mise a jour du timestamp alive_at = ".$alive_at.PHP_EOL, FILE_APPEND);
            }
            break;

        default :
            // Tache terminee ou inexistante
            //file_put_contents("/tmp/watch.log", "ERREUR >  Tache terminee ou inexistante".PHP_EOL, FILE_APPEND);
            $done = true;

            break;
    }
}

pmb_mysql_close();

