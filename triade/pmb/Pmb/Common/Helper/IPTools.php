<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: IPTools.php,v 1.2.4.1 2025/05/20 14:05:00 qvarin Exp $

namespace Pmb\Common\Helper;

class IPTools
{

    /* Tableau des adresses IP autorisees */
    static $authorized_ips = null;
    /**
     * Lecture configuration
     * definie dans includes/config_local.inc.php et opac_css/includes/opac_config_local.inc.php
     *
     * @return []
     */
    protected static function getAuthorizedIPs ()
    {
        if( !is_null(static::$authorized_ips) ) {
            return static::$authorized_ips;
        }

        global $overload_global_parameters;
        static::$authorized_ips = [];
        if( !empty ($overload_global_parameters['config_authorized_ips']) && is_array($overload_global_parameters['config_authorized_ips']) ) {
            static::$authorized_ips = $overload_global_parameters['config_authorized_ips'];
        }
        return static::$authorized_ips;
    }


    /**
     * Verifie que l'acces est autorise selon l'IP de la requete
     *
     * @return number: (1 = IP autorisee, 2 = IP non autorisee)
     */
    public static function isIPAuthorized()
    {
        static::getAuthorizedIPs();

        if(empty(static::$authorized_ips)) {
            return 2;
        }
        $user_ip = IPTools::getIP();
        if ( !empty($user_ip) && in_array($user_ip, static::$authorized_ips) ) {
            return 1;
        }
        return 2;
    }

    
    /**
     * Retourne l'IP client
     * @ return string
     */
    public static function getIP()
    {
        $ip = $_SERVER["REMOTE_ADDR"];
        if( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $ip;
    }
    
    
    /**
     * Retourne les profils possibles pour les adresses IPs (autorise / interdit)
     *
     * @return []
     */
    public static function getIPProfiles()
    {
        global $msg;
        return [
            1 => $msg["iptools_authorized_ip"],
            2 => $msg["iptools_unauthorized_ip"],
        ];
    }
}
