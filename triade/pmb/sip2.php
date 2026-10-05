<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sip2.php,v 1.9.6.1.2.1 2025/04/03 14:55:10 dgoron Exp $
$time_log_start = microtime(true);

// définition du minimum nécéssaire
$base_path = ".";
$base_auth = "CIRCULATION_AUTH";
$base_noheader = 1;

require_once("$base_path/includes/init.inc.php");

global $class_path, $include_path, $charset;
global $message, $debug, $id;

require_once("$class_path/sip2_protocol.class.php");
require_once("$class_path/sip2_trame.class.php");
require_once("$include_path/sip2/sip2_functions.inc.php");

$message = stripslashes($message);

$protocol_filename = $include_path . '/sip2/protocol.xml';
if (file_exists($include_path . '/sip2/protocol_subst.xml')) {
	$protocol_filename = $include_path . '/sip2/protocol_subst.xml';
}

$protocol = new sip2_protocol($protocol_filename, $charset);
if ($debug) {
    $fp_debug_rfid = fopen("temp/messages.log", "a+");
} else {
    $fp_debug_rfid = '';
}

$info_debug_rfid = "Date (".$automate."): ".date("Y-m-d H:i:s")."\n";
$info_debug_rfid .= "Trame recue: ".$message."\n";
if ($fp_debug_rfid) {
    fwrite($fp_debug_rfid, $info_debug_rfid);
    $info_debug_rfid = "";
}

// Analyse de la trame
$trame = new sip2_trame($message, $protocol);

$last_trame = "";
$message_pair = "";

// Si il y a une erreur ?
if ($trame->error) {
    $info_debug_rfid .= "Erreur trame reçue: ".$trame->error_message."\n";
	print $trame->error_message;

	// Si c'est une erreur on redemande le message
    $message_pair = 96;
    $values = [];
} else {
    //Sinon tout va bien
    $message_pair = $trame->message_pair;
    $values = $trame->message_values;
    if ($trame->message_id == 97) {
        // Demande du dernier message
        if ($_SESSION[$id]["ltrame"]) {
            //Si dernier message pas vide
            $last_trame = $_SESSION[$id]["ltrame"];
            $info_debug_rfid .= "Trame reponse session: ".$_SESSION[$id]["ltrame"]."\n";
            print $_SESSION[$id]["ltrame"];
            $message_pair = "";
        } else {
            //Si dernier message vide, on envoie une redemande
            $message_pair = 96;
            $values = [];
        }
    }
}
if ($message_pair) {
    $tramer = new sip2_trame("", $protocol);
    $tramer->set_message_id($message_pair);
    $tramer->set_checksum(true);
    $tramer->set_sequence_number(intval($trame->sequence_number));

    // Appel de la fonction
    $func_response = "_".strtolower($protocol->messages[$message_pair]["NAME"])."_";
	if (!function_exists($func_response)) {
		trigger_error("La fonction ".$func_response." n'existe pas", E_USER_ERROR);
	}

    $values = $func_response($values);
    $tramer->set_message_values($values);

    //Si il y a une erreur, erreur définitive !
    if ($tramer->error) {
        $info_debug_rfid .= "Function: ".$func_response."\n";
        $info_debug_rfid .= "Erreur trame n1: ".$tramer->error_message."\n";
        print $tramer->error_message;
        print "exit";
    } else {
        //On construit la trame
        $tramer->make_trame();
        //Si il y a une erreur
        if ($tramer->error) {
            $info_debug_rfid .= "Erreur trame n2: ".$tramer->error_message."\n";
            print $tramer->error_message;
            print "exit";
        } else {
            if($rtim || $rtrim) {//erreur sur le nomage du paramètre 'rtim' la 1ere fois
                $tramer->trame = rtrim($tramer->trame);
            }
            $info_debug_rfid .= "Trame reponse: ".$tramer->trame."\n";
            print $tramer->trame;
            $last_trame = $tramer->trame;
        }
    }
}
if($fp_debug_rfid) {
    $time_log_end = microtime(true);
    $duree = number_format(($time_log_end - $time_log_start), 3);
    if($duree > 15) {
        $info_debug_rfid .= "Lenteur traitement\n";
    }
    $info_debug_rfid .= "Script execute en: ".$duree." sec\n\n";
    fwrite($fp_debug_rfid, $info_debug_rfid);
    fclose($fp_debug_rfid);
}
$_SESSION[$id]["ltrame"] = $last_trame;
