<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: api.inc.php,v 1.24.4.1 2025/04/30 11:58:52 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//Liste de fonctions utiles
// connectFtp($url,$user,$password) renvoie un identifiant de connexion ou rien si erreur de connexion

global $msg;

function connectFtp($url = "", $user = "", $password = "", $chemin = "", &$msg_ = "") {
    global $msg;

    $conn_id = @ftp_connect($url);
    if ($conn_id) {
        // login with username and password
        $login_result = @ftp_login($conn_id, $user, $password);
        if (!$login_result) {
            $msg_=$msg["sauv_api_connect_failed"];
            return "";
        }
        $chdir_result=@ftp_chdir($conn_id,$chemin);
        if (!$chdir_result) {
            $msg_=$msg["sauv_api_failed_path"];
            return "";
        }
    } else {
        $msg_=$msg["sauv_api_failed_host"];
        return "";
    }
    return $conn_id;
}

function abort($msg_,$logid) {
    global $msg;
    $requete="update sauv_log set sauv_log_messages=concat(sauv_log_messages,'Abort : ".addslashes($msg_)."') where sauv_log_id=".$logid;
    @pmb_mysql_query($requete);
    echo sprintf($msg["sauv_api_failed_cancel"],$msg_);
    exit();
}

/*
 Plus tard pour Dimitri & le planificateur des tâches
 function stop($msg_,$logid) {
 global $msg;
 $requete="update sauv_log set sauv_log_messages=concat(sauv_log_messages,'Abort : ".addslashes($msg_)."') where sauv_log_id=".$logid;
 @pmb_mysql_query($requete);
 return sprintf($msg["sauv_api_failed_cancel"],$msg_);
 }
 */
function stop($msg_, $logid) {
    global $msg;
    return sprintf($msg["sauv_api_failed_cancel"],$msg_);
}

function abort_copy($msg_,$logid) {
    global $msg;
    $requete="update sauv_log set sauv_log_messages=concat(sauv_log_messages,'Abort Copy : ".addslashes($msg_)."') where sauv_log_id=".$logid;
    @pmb_mysql_query($requete);
    echo sprintf($msg["sauv_api_copy_failed_cancel"],$msg_);
    exit();
}

function abort_critical($message) {
    echo "<script>alert(\"$message\"); history.go(-1);</script>";
    exit();
}

/*
 Plus tard pour Dimitri & le planificateur des tâches
 function stop_copy($msg_,$logid) {
 global $msg;
 $requete="update sauv_log set sauv_log_messages=concat(sauv_log_messages,'Abort Copy : ".addslashes($msg_)."') where sauv_log_id=".$logid;
 @pmb_mysql_query($requete);
 return sprintf($msg["sauv_api_copy_failed_cancel"],$msg_);
 }
 */
function stop_copy($msg_, $logid) {
    global $msg;
    return sprintf($msg["sauv_api_copy_failed_cancel"],$msg_);
}

function write_log($msg_,$logid) {
    $requete="update sauv_log set sauv_log_messages=concat(sauv_log_messages,'Log : ".addslashes($msg_)."\n') where sauv_log_id=".$logid;
    pmb_mysql_query($requete);
}

function create_statement($table) {
    $requete = "SHOW CREATE TABLE $table";
    $result = pmb_mysql_query($requete);
    if ($result) {
        $create = pmb_mysql_fetch_row($result);
        $create[1] = str_replace("\r"," ", $create[1]);
        $create[1] = str_replace("\n"," ", $create[1]);
        $create[1] .= ";";
        return $create[1];
    } else {
        return "";
    }
}

function table_dump($table_name,$fp) {
    $ina = array();

    fwrite($fp,"#".$table_name."\r\n");

    fwrite($fp,"drop table if exists ".$table_name.";\r\n");

    //Get strucutre
    fwrite($fp,create_statement($table_name)."\n");

    //enumerate tables

    $update_a_faire=0; /* permet de gérer les id auto_increment qui auraient pour valeur 0 */
    //parse the field info first
    $res2=pmb_mysql_query("select * from {$table_name} order by 1 ");
    if ($res2) {
        $nf=pmb_mysql_num_fields($res2);
        $nr=pmb_mysql_num_rows($res2);
    }
    $fields = '';
    $values = '';



    if ($nf) {
        for ($b=0;$b<$nf;$b++) {
            $fn=pmb_mysql_field_name($res2,$b);
            $ft=pmb_mysql_field_type($res2,$b);
            //			$fs=pmb_mysql_field_len($res2,$b);
            // 			$ff=pmb_mysql_field_flags($res2,$b);

            $is_numeric=false;

            switch($ft)	{

                case "BIT" :
                case "DECIMAL" :
                case "DOUBLE" :
                case "FLOAT" :
                case "INT24" :
                case "LONG" :
                case "LONGLONG" :
                case "NEWDECIMAL" :
                case "SHORT" :
                case "TINY" :

                case "NULL" :
                    $is_numeric=true;
                    break;


                default:
                    break;
            }

            if ((string) $fields != "") {
                $fields .= ', ' . $fn;
            } else {
                $fields .= $fn;
            }
            // 			$fna[$b] = $fn;
            $ina[$b] = $is_numeric;
        }
    }

    //parse out the table's data and generate the SQL INSERT statements in order to replicate the data itself...
    if ($nr) {
        for ($c=0;$c<$nr;$c++) {
            $row=pmb_mysql_fetch_row($res2);
            $values = '';
            for ($d=0;$d<$nf;$d++) {
                $data=strval($row[$d]);
                if ($ina[$d]==true) {
                    if ((string) $values != "") {
                        $values .= ', '.floatval($data);
                    } else {
                        $values .= floatval($data);
                    }
                } else {
                    if ((string) $values != "") {
                        $values .=", \"".pmb_mysql_real_escape_string($data)."\"";
                    } else {
                        $values .="\"".pmb_mysql_real_escape_string($data)."\"";
                    }
                }
            }
            fwrite($fp,"insert into $table_name ($fields) values ($values);\r\n");
            if ($update_a_faire==1) {
                $update_a_faire=0;
                // 				fwrite($fp,"update $table_name set ".$cle_update."='0' where ".$cle_update."='1';\r\n");
            }
        }
    }
    if ($res2) pmb_mysql_free_result($res2);
}

function read_infos($filepath = '') {
    $infos = [
        'filepath' => $filepath,
        'error' => 0,
        'error_msg' => [],

    ];
    if( !is_file($filepath) || !is_readable($filepath) ){
        $infos['error'] = 404;
        $infos['error_msg'][] = 'Wrong file';
        return $infos;
    }

    $f = fopen($filepath,"r");
    $line = rtrim(fgets($f,8192));
    while ( (!feof($f)) && ($line != "#data-section") ) {
        $tline = explode(" : ", $line);
        $index = trim(substr($tline[0], 1));
        $infos[$index] = empty($tline[1]) ? '' : $tline[1];
        $line = rtrim(fgets($f, 8192));
    }
    fclose($f);

    if( empty($infos['Name']) ) {
        $infos['error'] = 400;
        $infos['error_msg'][] = 'No Name';
        return $infos;
    }
    if( empty($infos['Date']) ) {
        $infos['error'] = 400;
        $infos['error_msg'][] = 'No Date';
        return $infos;
    }
    if( empty($infos['Groups']) ) {
        $infos['error'] = 400;
        $infos['error_msg'][] = 'No Groups';
        return $infos;
    }
    if( empty($infos['Tables']) ) {
        $infos['error'] = 400;
        $infos['error_msg'][] = 'No Tables';
        return $infos;
    }
    if( empty($infos['Compress']) ) {
        $infos['Compress'] = 0;
    }
    if( empty($infos['Compress commands']) ) {
        $infos['Compress commands'] = '';
    }

    $infos['compress_type'] = '';
    $infos['decompress_cmd'] = '';
    $infos['decompress_ext'] = '';
    if( !empty($infos['Compress commands']) ) {

        $commands = explode(':', $infos['Compress commands']);

        //compression = internal ou external
        if( empty($commands[0]) || !in_array($commands[0] , ['external', 'internal']) ) {
            $infos['error'] = 400;
            $infos['error_msg'][] = 'Wrong Compress type';
            return $infos;
        }
        //compression interne
        $infos['compress_type'] = $commands[0];
        if( 'internal' == $infos['compress_type']) {
            return $infos;
        }
        //compression / decompression externe
        //la commande de compression n'est pas vide et contient %s
        if( empty($commands[1]) || (false === strpos($commands[1], '%s')) ) {
            $infos['error'] = 400;
            $infos['error_msg'][] = 'Wrong Compress cmd';
            return $infos;
        }
        //la commande de decompression n'est pas vide et contient %s
        if( empty($commands[2]) || (false === strpos($commands[2], '%s')) ) {
            $infos['error'] = 400;
            $infos['error_msg'][] = 'Wrong Decompress cmd';
            return $infos;
        }
        $infos['decompress_cmd'] = $commands[2];
        //extension fichier
        if( !empty($commands[3]) ) {
            $infos['decompress_ext'] = $commands[3];
        }
    }

    return $infos;
}
