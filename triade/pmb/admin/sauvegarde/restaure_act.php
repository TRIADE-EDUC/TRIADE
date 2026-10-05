<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: restaure_act.php,v 1.12 2023/03/02 08:45:38 dbellamy Exp $

//Restauration d'un jeu

global $msg;

$base_path = "../..";
$base_auth = "SAUV_AUTH|ADMINISTRATION_AUTH";

$base_title = $msg['sauv_misc_ract_title'];

//Initialisation variables
$critical = !empty($_POST["critical"]) ? 1 : 0;
$logid = 0;
$filename = '';
$displayed_filename = '';
$tables = [];
$host = '';
$db_user = '';
$db_password = '';
$db = '';
$charset = 'iso-8859-1';

//Est-ce une restauration critique (dans ce cas, pas de vérification d'utilisateur)?
if ($critical) {
    
    if ( is_readable("../backup/backups/critical_upload.php") ) {
        $fc = file_get_contents("../backup/backups/critical_upload.php");
        if(false !== $fc) {
            $filename = trim(explode('//', $fc)[1]);
        }
    }
    include "emergency/messages_env_ract.inc.php";
    $class_path = "../../classes";
    include '../../includes/mysql_functions.inc.php';
    
    $displayed_filename = isset($_POST['filename']) ? $_POST['filename'] : '';
    $tables = isset($_POST["tables"]) ? $_POST["tables"] : [];
    $host = isset($_POST["host"]) ? $_POST["host"] : '';
    $db_user = isset($_POST["db_user"]) ? $_POST["db_user"] : '';;
    $db_password = isset($_POST["db_password"]) ? $_POST["db_password"] : '';
    $db = isset($_POST["db"]) ? $_POST["db"] : '';
    
} else {
    require $base_path."/includes/init.inc.php";
    $critical = 0;
    $displayed_filename = $filename;
}

require "lib/api.inc.php";

//En mode restauration critique on verifie la connexion a la base de donnees
if($critical) {
    $dbh = pmb_mysql_connect($host, $db_user, $db_password) or abort_critical($msg["sauv_misc_ract_cant_connect"]);
    pmb_mysql_select_db($db, $dbh) or abort_critical(sprintf($msg["sauv_misc_ract_db_dont_exists"], $db));
}

//Verification du fichier
$infos = read_infos("../backup/backups/".$filename);
if ( !empty($infos['error']) ) {
    @unlink ( "../backup/backups/".$filename);
    abort_critical($msg['sauv_misc_ract_no_sauv']);
    exit();
}

//Info compression/decompression
$compress = $infos['Compress'];
$compress_type = $infos['compress_type'];
$decompress_cmd = $infos['decompress_cmd'];
$decompress_ext = $infos['decompress_ext'];

/*
 require_once "../../classes/crypt.class.php";
 
 $datas=fread($f,filesize($filename));
 
 fclose($f);
 
 //Si crypté
 if ($crypt==1) {
 echo "<b>".$msg["sauv_misc_ract_decryt_msg"]."</b><br />";
 flush();
 $c=new Crypt(md5($phrase1),md5($phrase2));
 $sign=substr($datas,0,8);
 $dSign=$c->getDecrypt($sign);
 if ($dSign!="PMBCrypt") abort($msg["sauv_misc_ract_bad_keys"]);
 $datas=substr($datas,8);
 $datas=$c->getDecrypt($datas);
 }
 */
if($critical) {
    echo '<!DOCTYPE html><html><head><meta charset="'.$charset.'" ></head><body>';
}
echo '<div id="contenu-frame">
    <h1>'.htmlentities(sprintf($msg["sauv_misc_restaure"], $displayed_filename), ENT_QUOTES, $charset).'</h1>';

//Nom du fichier temporaire
$tempfile = "../backup/backups/temp_restaure";
if( $compress == 1 ) {
    if ($compress_type == 'internal') {
        $tempfile.= '.bz2';
    }
    if ($compress_type == 'external') {
        $tempfile.=".sql".(!empty($decompress_ext) ? '.'.$decompress_ext : '');
    }
}  else {
    $tempfile.=".sql";
}

//Ouverture fichier temporaire
$ftemp = fopen($tempfile, "w+") or abort_critical($msg["sauv_misc_ract_create"]);

//Ouverture fichier sauvegarde
$f = fopen( "../backup/backups/".$filename, "r") or abort_critical($msg["sauv_misc_ract_cant_open_file"]);

//Recuperation partie data et copie dans le fichier temporaire
$line = rtrim(fgets($f,8192));
while ( (!feof($f)) && ($line != "#data-section") ) {
    $line = rtrim(fgets($f, 8192));
}
while (!feof($f) ) {
    fwrite($ftemp, fread($f, 8192));
}
fclose($ftemp);
fclose($f);

//Decompression fichier temporaire vers fichier sql
$tempfiledest = "../backup/backups/temp_restaure.sql";
if ( $compress == 1 ) {
    echo "<b>".htmlentities($msg["sauv_misc_ract_decompress"], ENT_QUOTES, $charset)."</b><br />";
    flush();
    
    if ( $compress_type == "external" ) {
        $decompress_cmd = str_replace("%sd", $tempfiledest, $decompress_cmd);
        $decompress_cmd = str_replace("%s", $tempfile, $decompress_cmd);
        exec($decompress_cmd);
    }
    if( $compress_type == 'internal' ) {
        
        $ftempin = bzopen($tempfile, "r") or abort_critical($msg["sauv_misc_ract_not_bz2"]);
        $ftempout = fopen($tempfiledest, "w+") or abort_critical($msg["sauv_misc_ract_create"]);
        while (!feof($ftempin)) {
            $datas = bzread($ftempin, 2048);
            fwrite($ftempout ,$datas);
        }
        bzclose($ftempin);
        fclose($ftempout);
        @unlink($tempfile);
    }
}

//Application des requetes
echo "<b>".htmlentities($msg["sauv_misc_ract_restaure_tables"], ENT_QUOTES, $charset)."</b><br /><br />";

//Ouverture fichier SQL
$fsql=fopen($tempfiledest, "r") or abort_critical($msg["sauv_misc_ract_open_failed"]);

$mod_query = 0;
$currentTable = "";

while ( !feof($fsql )) {
    $line = "";
    while ( (substr($line, strlen($line)-1, 1) != "\n") && (!feof($fsql)) ) {
        $line.= fgets($fsql,4096);
    }
    $line = rtrim($line);
    if ($line != "") {
        if (substr($line, 0, 1) == "#") {
            if ( ($currentTable != "") && ($mod_query == 1) ) {
                echo htmlentities(sprintf($msg["sauv_misc_ract_restaured_t"], $currentTable), ENT_QUOTES, $charset)."<br />";
                flush();
            }
            $currentTable = substr($line, 1);
            $as=array_search($currentTable, $tables);
            if ( ($as !== false) && ($as !== null) ) {
                $mod_query = 1;
                echo htmlentities(sprintf($msg["sauv_misc_ract_start_restaure"], $currentTable), ENT_QUOTES, $charset)."<br />";
            } else {
                $mod_query = 0;
                echo sprintf($msg["sauv_misc_ract_ignore"], $currentTable)."<br />";
            }
            flush();
        } else {
            if ($mod_query == 1) {
                pmb_mysql_query($line) or abort_critical(sprintf($msg["sauv_misc_ract_invalid_request"], $line));
            }
        }
    }
}
fclose($fsql);
@unlink($tempfiledest);

/* Succeed - Gestion du cas particulier :
 * Dernière sauvegarde effectuée <=> sauv_log_succeed valorisé à 1 après sauvegarde
 * Lors de la restauration, on récupère la valeur 0, enregistrée avant la fin de la sauvegarde.
 */
$requete = "update sauv_log set sauv_log_succeed=1 where sauv_log_id=".$logid;
@pmb_mysql_query($requete);

echo "<h2>".htmlentities($msg["sauv_misc_ract_correct"], ENT_QUOTES, $charset)."</h2>";
echo "</div>";
if ( $critical ) {
    echo '</body></html>';
    unlink( "../backup/backups/".$filename);
}
