<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MySQL.php,v 1.2 2023/09/06 09:25:17 dgoron Exp $

namespace Pmb\Common\Helper;

class MySQL 
{
    
    /**
     * Compatibilité MySQL 8.x
     * Similaire à la fonction password dans SQL
     *
     * @param string $pass
     * @return string
     */
    public static function password($pass) {
        if($pass === '') {
            return "";
        }
        return "*" . mb_convert_case(sha1(hex2bin(sha1($pass))), MB_CASE_UPPER);
    }
}
