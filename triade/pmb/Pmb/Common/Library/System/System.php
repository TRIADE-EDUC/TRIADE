<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: System.php,v 1.2 2024/04/11 08:26:23 dbellamy Exp $

namespace Pmb\Common\Library\System;

class System
{

    static protected $os = null;
    static protected $hostname = null;

    /**
     * Retourne l'OS du serveur
     *
     * @return string
     */
    public static function getOS()
    {
        if( !is_null(static::$os) ) {
            return static::$os;
        }
        if ( (!empty($_SERVER['SERVER_SOFTWARE']) && stripos($_SERVER['SERVER_SOFTWARE'], "win") !== false) || stripos(PHP_OS, "win") !== false) {
            static::$os = "Windows";
        } elseif ( (!empty($_SERVER['SERVER_SOFTWARE']) && stripos($_SERVER['SERVER_SOFTWARE'], "mac") !== false) || stripos(PHP_OS, "mac") !== false
            || (!empty($_SERVER['SERVER_SOFTWARE']) && stripos($_SERVER['SERVER_SOFTWARE'], "ppc") !== false)  || stripos(PHP_OS, "ppc") !== false) {
                static::$os = "Mac";
        } elseif ( (!empty($_SERVER['SERVER_SOFTWARE']) && stripos($_SERVER['SERVER_SOFTWARE'], "linux") !== false) || stripos(PHP_OS, "linux") !== false) {
            static::$os = "Linux";
        } elseif ( (!empty($_SERVER['SERVER_SOFTWARE']) && stripos($_SERVER['SERVER_SOFTWARE'], "freebsd") !== false)  || stripos(PHP_OS, "freebsd") !== false) {
            static::$os = "FreeBSD";
        } elseif ( (!empty($_SERVER['SERVER_SOFTWARE']) && stripos($_SERVER['SERVER_SOFTWARE'], "sunos") !== false) || stripos(PHP_OS, "sunos") !== false) {
                static::$os = "SunOS";
        } elseif ( (!empty($_SERVER['SERVER_SOFTWARE']) && stripos($_SERVER['SERVER_SOFTWARE'], "irix") !== false) || stripos(PHP_OS, "irix") !== false) {
                static::$os = "IRIX";
        } elseif ( (!empty($_SERVER['SERVER_SOFTWARE']) && stripos($_SERVER['SERVER_SOFTWARE'], "beos") !== false) || stripos(PHP_OS, "beos") !== false) {
                static::$os = "BeOS";
        } elseif ( (!empty($_SERVER['SERVER_SOFTWARE']) && stripos($_SERVER['SERVER_SOFTWARE'], "os/2") !== false) || stripos(PHP_OS, "os/2") !== false) {
                static::$os = "OS/2";
        } elseif ( (!empty($_SERVER['SERVER_SOFTWARE']) && stripos($_SERVER['SERVER_SOFTWARE'], "aix") !== false) || stripos(PHP_OS, "aix") !== false) {
            static::$os = "AIX";
        } else {
            static::$os = "Autre";
        }
        return static::$os;
    }


    /**
     * Retourne le nom d'hote du serveur
     *
     * @return string
     */
    public static function getHostName()
    {
        if( !is_null(static::$hostname) ) {
            return static::$hostname;
        }
        static::$hostname = gethostname();
        if(false === static::$hostname) {
            static::$hostname = 'localhost';
        }
        return static::$hostname;
    }


    /**
     * Lance un process sur le serveur
     *
     * @param string $path_file :
     * @return integer
     */
    public static function runProcess(string $path_file = '')
    {
        static::getOS();
        global $pmb_psexec_cmd, $pmb_path_php;

        $output = [];
        switch (static::$os) {
            case 'Windows':
                $psexec_cmd = 'psexec -d';
                if ($pmb_psexec_cmd) {
                    $psexec_cmd = $pmb_psexec_cmd;
                }
                exec("$psexec_cmd $pmb_path_php " . $path_file . " 2>&1 ", $output);
                $matches = [];
                if ( (count($output) > 5) && preg_match('/ID (\d+)/', $output[5], $matches) ) {
                    $output[0] = $matches[1];
                }
                break;
            case 'Linux':
            case 'Mac':
            default:
                exec("nohup $pmb_path_php  " . $path_file . " > /dev/null 2>&1 & echo $!", $output);
                break;
        }
        return (int) $output[0];
    }


    /**
     * Verifie qu'un process existe sur la machine
     *
     * @param int $process_id : Id process
     * @return boolean
     */
    public static function checkProcess(int $process_id)
    {
        if(!$process_id) {
            return false;
        }
        static::getOS();
        $command = 'ps -p ' . $process_id;
        if (static::$os == "Windows") {
            $command = 'tasklist /FI "PID eq ' . $process_id . '" ';
        }
        $output = [];
        exec($command, $output);

        if (!isset($output[1])) {
            return false;
        }
        return true;
    }

}

