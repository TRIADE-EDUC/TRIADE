<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SynchroCommons.php,v 1.2 2023/08/28 14:04:12 tsamson Exp $
//
// V73 (DB-13/07/2022) Maj packages + Ajout autoload + namespace
// TODO Protection + inclusions PMB à voir
// +-------------------------------------------------+

namespace Pmb\Authentication\Synchro;


global $class_path;

if (! defined('UTF16_BIG_ENDIAN_BOM')) {
    define('UTF16_BIG_ENDIAN_BOM', chr(0xFE) . chr(0xFF));
}
if (! defined('UTF16_LITTLE_ENDIAN_BOM')) {
    define('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE));
}
if (! defined('UTF8_BOM')) {
    define('UTF8_BOM', chr(0xEF) . chr(0xBB) . chr(0xBF));
}
if (! defined('LDAP_ACCOUNT_NEVER_EXPIRES')) {
    define('LDAP_ACCOUNT_NEVER_EXPIRES', 9223372036854775807);
}

use Exception;
use DateTime;

class SynchroCommons
{

    protected static $pmb_charset = 'utf-8';

    // historique en jours
    protected static $history = 7;

    public static function setCharset($charset = '')
    {
        if ($charset) {
            self::$pmb_charset = $charset;
        }
    }

    public static function setEncoding($encoding = 'utf-8')
    {
        try {
            mb_internal_encoding($encoding);
            mb_regex_encoding($encoding);
        } catch (Exception $e) {
            mb_internal_encoding('utf-8');
            mb_regex_encoding('utf8');
        }
    }

    public static function setHistory($history)
    {
        $history = intval($history);
        if (! $history) {
            $history = 7;
        }
        self::$history = $history;
    }

    public static function getHistory()
    {
        return self::$history;
    }

    public static function charsetDecode($value = '')
    {
        $r = '';
        $r = trim($value);
        if (self::$pmb_charset != 'utf-8') {
            $r = encoding_normalize::utf8_decode($r);
        }
        return $r;
    }

    public static function toCamelCase($value = '')
    {
        $chars = ['-', '_'];
        $value = ucwords(str_replace($chars, ' ', $value));
        $value = str_replace(' ', '', $value);
        return lcfirst($value);
    }

    public static function detectEncoding($str = '')
    {
        $first2 = substr($str, 0, 2);
        $first3 = substr($str, 0, 3);

        if ($first3 == UTF8_BOM) {
            return 'utf-8';
        } elseif ($first2 == UTF16_BIG_ENDIAN_BOM) {
            return 'utf-16be';
        } elseif ($first2 == UTF16_LITTLE_ENDIAN_BOM) {
            return 'utf-16le';
        }

        $mbde = mb_detect_encoding($str, 'utf-8,iso-8859-15,iso-8859-1,cp1252');
        if ($mbde) {
            return $mbde;
        }
        return 'iso-8859-1';
    }

    protected static function getValue($value = '')
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_array($value) && count($value) && isset($value[0])) {
            $value = $value[0];
            return $value;
        }
        return '';
    }

    public static function concat($sep = '', $val1 = '', $val2 = '')
    {
        $ret = '';
        if (! is_string($sep)) {
            return $ret;
        }

        $val1 = static::getValue($val1);
        $val2 = static::getValue($val2);

        // 2 chaines
        if (is_string($val1) && is_string($val2)) {
            if ($val1 !== '' && $val2 !== '') {
                $ret = $val1 . $sep . $val2;
            } elseif ($val1 !== '' && $val2 === '') {
                $ret = $val1;
            } elseif ($val1 === '' && $val2 !== '') {
                $ret = $val2;
            }
            return $ret;
        }

        // 1 tableau
        if (is_array($val1) && count($val1)) {
            foreach ($val1 as $v) {

                if (is_string($v)) {
                    if ($ret === '') {
                        $ret = $v;
                    } elseif ($ret !== '' && $v !== '') {
                        $ret = $ret . $sep . $v;
                    }
                }
            }
        }

        return $ret;
    }

    public static function firstDefined($val1 = '', $val2 = '')
    {
        $ret = '';

        $val1 = static::getValue($val1);
        $val2 = static::getValue($val2);

        if (! is_string($val1) || ! is_string($val2)) {
            return $ret;
        }

        if ($val1 !== '') {
            $ret = $val1;
        } elseif ($val2 !== '') {
            $ret = $val2;
        }
        return $ret;
    }

    public static function toDate($value = '', $format = 'Y-m-d')
    {
        $ret = '';

        $value = static::getValue($value);

        $date = static::validateDate($value);
        if ($date) {
            $ret = $date->format($format);
        }
        return $ret;
    }

    public static function validateDate($value = '')
    {
        $ret = false;

        $value = static::getValue($value);

        if (! is_string($value)) {
            return $ret;
        }

        $tab_formats = array(
            'Y-m-d\TH:i:s',
            'd/m/Y H:i:s',
            'd/m/y H:i:s',
            'd/m/Y',
            'd/m/y',
            'Y-m-d',
        );

        $value = trim($value);
        foreach ($tab_formats as $format) {
            $date = DateTime::createFromFormat($format, $value);
            if ($date) {
                return $date;
            }
        }
        return false;
    }

    public static function toYear($value = '', $format = 'Y')
    {
        $ret = '';

        $value = static::getValue($value);

        if (! is_string($value)) {
            return $ret;
        }

        if (preg_match('#^[0-9]{4}#', $value)) {
            $ret = $value;
        } elseif (preg_match('#^[0-9]{2}#', $value)) {
            $ret = '20' . $value;
        }
        return $ret;
    }

    public static function toGender($value = '')
    {
        $ret = 0;

        $value = static::getValue($value);

        if (! is_string($value)) {
            return $ret;
        }

        $value = strtolower(trim($value));
        $tm = array(
            'm',
            'mr',
            'm.',
            'monsieur',
            '1'
        );
        $tf = array(
            'f',
            'mme',
            'mlle',
            'madame',
            'mademoiselle',
            '2'
        );
        if (in_array($value, $tm)) {
            $ret = 1;
        } elseif (in_array($value, $tf)) {
            $ret = 2;
        }

        return $ret;
    }

    public static function toCountry($value = '')
    {
        $ret = '';
        
        $value = static::getValue($value);

        if (! is_string($value)) {
            return $ret;
        }

        global $class_path;
        require_once __DIR__.'/../../classes/marc_table.class.php';
        
        $ml = new \marc_list('country');
        $tab_countries = $ml->table;
        if (array_key_exists($value, $tab_countries)) {
            $ret = $tab_countries[$value];
        }
        return $ret;
    }

    public static function toLocationId($location_name = '')
    {
        $ret = 0;

        $location_name = static::getValue($location_name);

        if (! is_string($location_name)) {
            return $ret;
        }

        $q = 'select idlocation from docs_location where location_libelle="' . addslashes($location_name) . '"';
        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            $ret = pmb_mysql_result($r, 0, 0);
        }
        return $ret;
    }

    public static function toCategId($categ_name = '')
    {
        $ret = 0;

        $categ_name = static::getValue($categ_name);

        if (! is_string($categ_name)) {
            return $ret;
        }

        $q = 'select id_categ_empr from empr_categ where libelle="' . addslashes($categ_name) . '"';
        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            $ret = pmb_mysql_result($r, 0, 0);
        }
        return $ret;
    }

    /**
     * Association libellé code statistique > id
     *
     * @param string $codestat_name
     *            Libellé code statistique
     * @param boolean $create
     *            Créer le code statistique si inexistant (0/1)
     * @return int Identifiant code statistique (0 si erreur)
     */
    public static function toCodeStatId($codestat_name = '', $create = 0)
    {
        $ret = 0;

        $codestat_name = static::getValue($codestat_name);

        if (! is_string($codestat_name) || empty($codestat_name) ) {
            return $ret;
        }

        $q = 'select idcode from empr_codestat where libelle="' . addslashes($codestat_name) . '"';
        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            $ret = pmb_mysql_result($r, 0, 0);
            return $ret;
        }
        if ($create == 1) {
            $qc = 'insert into empr_codestat (libelle) values ("' . addslashes($codestat_name) . '") ';
            $rc = pmb_mysql_query($qc);
            if ($rc) {
                $ret = pmb_mysql_insert_id();
            }
        }

        return $ret;
    }

    public static function nameToId($name = '', $tab_values = array())
    {
        $ret = 0;

        $name = static::getValue($name);
        if (! is_string($name) || $name == '' || ! is_array($tab_values) || ! count($tab_values)) {
            return $ret;
        }

        foreach ($tab_values as $v) {
            if ($v['value'] == $name && $v['id']) {
                return $v['id'];
            }
        }
        return $ret;
    }

    public static function getFiles($directory_path = '', $pattern = "")
    {
        $directory = @opendir($directory_path);
        $f_tab = array();
        if ($directory !== false) {
            while (($fichier = readdir($directory))) {
                if (is_file($directory_path . $fichier) ) {
                    $f_tab[] = $directory_path . $fichier;
                }
            }
            closedir($directory);
        }
        if(count($f_tab) && $pattern !== "") {
            $f_tab = preg_grep($pattern, $f_tab);
        }
        return $f_tab;
    }

    // Transforme donnee xml en valeur utilisable par le script
    public static function xmltoValue($value = '', $file = '')
    {
        $ret = '';
        $value = static::getValue($value);
        if (! $value) {
            return $ret;
        }
        if (! is_file($file) || ! is_readable($file)) {
            return $ret;
        }

        $class_path = __DIR__.'/../../classes';
        require_once $class_path.'/marc_table.class.php';
        
        $ml = new \marc_list('');        
        $ml->parser = new \XMLlist($file);
        $ml->parser->analyser();
        $ml->table = $ml->parser->table;
        $table = $ml->table;
        
        if (array_key_exists($value, $table)) {
            $ret = $table[$value];
        }
        return $ret;
    }

    public static function cleanDir($directory_path = '', $older_than = 0)
    {
        $directory_path = static::addTrailingSlash($directory_path);

        $directory = @opendir($directory_path);
        $d1 = strtotime(date('m/d/Y')) / 86400;
        $older_than = intval($older_than);
        if (! $older_than) {
            $older_than = self::$history;
        }

        if ($older_than && $directory !== false) {
            while (($fichier = readdir($directory))) {
                if (is_file($directory_path . $fichier) && ($fichier != "dummy.txt")) {
                    $d2 = filemtime($directory_path . $fichier) / 86400;
                    if (($d1 - $d2) * 1 > $older_than) {
                        @unlink($directory_path . $fichier);
                    }
                }
            }
            closedir($directory);
        }
        return;
    }

    public static function encodeFile($file_path = '', $dest_dir = '', $charset = '', $no_bom = true)
    {
        $ret = false;
        if ($file_path && file_exists($file_path) && $dest_dir) {

            if (! $charset) {
                $charset = self::$pmb_charset;
            }
            $f_str = file_get_contents($file_path);
            $f_encoding = SynchroCommons::detectEncoding($f_str);

            if ($f_encoding != $charset) {
                $f_str = mb_convert_encoding($f_str, $charset, $f_encoding);
            }

            if ($no_bom) {
                $f_str = SynchroCommons::removeBom($f_str);
            }

            if ($f_str) {
                $f_name = 'file_' . md5(microtime(true));
                if (file_put_contents($dest_dir . $f_name, $f_str)) {
                    $ret = $dest_dir . $f_name;
                }
            }
        }
        return $ret;
    }

    public static function removeBom($str = '')
    {
        $first2 = substr($str, 0, 2);
        $first3 = substr($str, 0, 3);

        if ($first3 == UTF8_BOM) {
            $str = substr($str, 3);
        } elseif ($first2 == UTF16_BIG_ENDIAN_BOM || $first2 == UTF16_LITTLE_ENDIAN_BOM) {
            $str = substr($str, 2);
        }
        return $str;
    }

    public static function getArrayFromCsvFile($file_in = '', $sep = ';', $header = 0, $attrs = [])
    {
        if(!$file_in || !is_readable($file_in)) {
            return false;
        }
        
        $fp_in = @fopen($file_in, 'r');
        if(false === $fp_in) {
            return false;
        }
        
        if (!$sep) {
            $sep = ';';
        }
        
        if(is_array($attrs) && count($attrs)) {
            foreach($attrs as $k=>$attr) {
                $attr = mb_strtolower($attr);
                $attr = preg_replace(
                    ["#à#u", "#ç#u", "#è#u", "#é#u", "#ê#u"],
                    ["a",    "c"   , "e"   , "e"   , "e"],
                    $attr);
                $attr = preg_replace("#[^a-z|0-9]#u","_",$attr);
                $attrs[$k] = $attr;
            }
        } else {
            $attrs = [];
        }
        
        $ret = [];
        $i = 0;
        
        // lecture des lignes valides du tableau
        $keys = [];
        while (! feof($fp_in)) {
            $tline = fgetcsv($fp_in, 0, $sep, '"');
            
            // est-ce une ligne non vide et valide ?
            if ($tline[0]) {
                
                if ($i===0 && $header) {
                    
                    foreach($tline as $k => $key) {
                        $key = mb_strtolower($key);
                        $key = preg_replace(
                            ["#à#u", "#ç#u", "#è#u", "#é#u", "#ê#u"],
                            ["a",    "c"   , "e"   , "e"   , "e"],
                            $key);
                        $key = preg_replace("#[^a-z|0-9]#u","_",$key);
                        $keys[$k] = $key;
                    }
                    
                } elseif($i!==0) {

                    foreach($tline as $k=>$v) {
                        if( $header) {
                            if( isset($keys[$k]) && in_array($keys[$k], $attrs) ) {
                                $ret[$i][$keys[$k]] = trim($v);
                            }
                        } else {
                            if( $attrs[$k] ) {
                                $ret[$i][$attrs[$k]] = trim($v);
                            }
                        }
                    }
                    
                }
            }
            $i++;
        }
        fclose($fp_in);
        return $ret;
    }
    
    
   public static function getArrayFromLdifFile($file_path = '')
    {
        $ret = array();
        $fp = false;
        $i = 0;

        if ($file_path && file_exists($file_path)) {
            $fp = @fopen($file_path, 'r');
        }
        if ($fp) {
            // lecture des lignes valides du tableau
            while (! feof($fp)) {
                $tline = fgets($fp);
                $trimed_tline = trim($tline);
                if ($tline[0] == "\r" || $trimed_tline === '' || $trimed_tline[0] == '#' || stripos($trimed_tline, 'search:') === 0 || stripos($trimed_tline, 'result:') === 0) {} else {
                    $tline = str_replace("\r\n", "", $tline);
                    if ($tline[0] == " ") {
                        $ret[$i - 1] .= $trimed_tline;
                    } else {
                        $ret[$i] = $tline;
                        $i ++;
                    }
                }
            }
            fclose($fp);
            // SUPPRIMER LES SAUT DE LIGNES DANS UN ENREGISTREMENT
            return $ret;
        }
    }

    public static function trimArrayValues(&$a = array())
    {
        if (is_array($a)) {
            foreach ($a as $k => $v) {
                SynchroCommons::trimArrayValues($a[$k]);
            }
        } else {
            $a = trim($a);
        }
    }

    public static function addTrailingSlash($path = '')
    {
        $path = trim($path);
        if ($path[strlen($path) - 1] != '/') {
            $path .= '/';
        }
        return $path;
    }

    public static function toUCWords($value = '', $encoding = 'utf-8')
    {
        $ret = '';

        $value = static::getValue($value);

        if (! is_string($value) || $value == '') {
            return $ret;
        }

        static::setEncoding($encoding);
        $ret = mb_convert_case($value, MB_CASE_TITLE, $encoding);

        return $ret;
    }

    public static function toUpper($value = '', $encoding = 'utf-8')
    {
        $ret = '';

        $value = static::getValue($value);

        if (! is_string($value) || $value == '') {
            return $ret;
        }

        static::setEncoding($encoding);
        $ret = mb_convert_case($value, MB_CASE_UPPER, $encoding);

        return $ret;
    }

    public static function toLower($value = '', $encoding = 'utf-8')
    {
        $ret = '';

        $value = static::getValue($value);

        if (! is_string($value) || $value == '') {
            return $ret;
        }

        static::setEncoding($encoding);
        $ret = mb_convert_case($value, MB_CASE_LOWER, $encoding);

        return $ret;
    }

    public static function isLdapAccountExpired($value = '')
    {
        $value = static::getValue($value);

        if (! is_string($value)) {
            return 0;
        }

        if (! $value) {
            return 0;
        }

        if ($value == LDAP_ACCOUNT_NEVER_EXPIRES) {
            return 0;
        }

        $current_date = date('Ymd');
        $unix_timestamp = ($value / 10000000) - 11644560000;
        $expire_date = date('Ymd', $unix_timestamp);

        if ($expire_date > $current_date) {
            return 0;
        }
        return 1;
    }

    public static function ldapAccountExpireToDate($value = '')
    {
        $value = static::getValue($value);

        if (! is_string($value) || ! $value) {
            return '';
        }
        if ($value == LDAP_ACCOUNT_NEVER_EXPIRES) {
            return '';
        }

        $unix_timestamp = ($value / 10000000) - 11644560000;
        $my_expire_date = date('Y-m-d', $unix_timestamp);

        return $my_expire_date;
    }

    public static function contains($value = '', $key = '')
    {
        if (is_array($key) && ! count($key)) {
            return 0;
        }

        if (is_string($key) && $key == '') {
            return 0;
        }

        $value = static::getValue($value);

        if (is_array($value) && is_string($key) && in_array($key, $value)) {
            return 1;
        }

        if (is_array($value) && is_array($key)) {
            foreach ($key as $v) {
                if (in_array($v, $value)) {
                    return 1;
                }
            }
        }

        if (is_string($value) && is_string($key) && (stripos($value, $key) !== false)) {
            return 1;
        }

        if (is_string($value) && is_array($key)) {
            foreach ($key as $v) {
                if (stripos($value, $v) !== false) {
                    return 1;
                }
            }
        }

        return 0;
    }

    public static function sidToString($value = '')
    {
        $value = static::getValue($value);

        if (! is_string($value) || ! $value) {
            return '';
        }
        $sid = @unpack('C1rev/C1count/x2/N1id', $value);
        if (! isset($sid['id']) || ! isset($sid['rev'])) {
            return '';
        }
        $revisionLevel = $sid['rev'];
        $identifierAuthority = $sid['id'];
        $subs = isset($sid['count']) ? $sid['count'] : 0;
        $sidHex = $subs ? bin2hex($value) : '';
        for ($i = 0; $i < $subs; $i ++) {
            $subAuthorities[] = hexdec(implode('', array_reverse(str_split(substr($sidHex, 16 + ($i * 8), 8), 2))));
        }
        return 'S-' . $revisionLevel . '-' . $identifierAuthority . implode(preg_filter('/^/', '-', $subAuthorities));
    }

    public static function toTokens($value = '', $pattern = ',', $encoding = 'utf-8')
    {
        $ret = array();

        $value = static::getValue($value);

        if (! is_string($value) || $value == '') {
            return $ret;
        }

        if (! is_string($pattern) || $pattern == '') {
            return $ret;
        }

        static::setEncoding($encoding);
        $ret = mb_split($pattern, $value);

        return $ret;
    }

    public static function leftOf($value = '', $sep = '', $last = 0, $encoding = 'utf-8')
    {
        $value = static::getValue($value);

        if (! is_string($sep) || $sep == '') {
            return $value;
        }

        static::setEncoding($encoding);
        if (is_array($value)) {
            $ret = array();
            foreach ($value as $k => $v) {
                if ($last) {
                    $pos = mb_strripos($v, $sep);
                } else {
                    $pos = mb_stripos($v, $sep);
                }
                if ($pos) {
                    $ret[$k] = trim(mb_substr($v, 0, $pos));
                } else {
                    $ret[$k] = $v;
                }
            }
            return $ret;
        }

        if (is_string($value)) {
            $ret = '';
            if ($last) {
                $pos = mb_strripos($value, $sep);
            } else {
                $pos = mb_stripos($value, $sep);
            }
            if ($pos) {
                $ret = trim(mb_substr($value, 0, $pos));
            } else {
                $ret = $value;
            }
            return $ret;
        }

        return '';
    }

    public static function rightOf($value = '', $sep = '', $last = 0, $encoding = 'utf-8')
    {
        $value = static::getValue($value);

        if (! is_string($sep) || $sep == '') {
            return $value;
        }

        static::setEncoding($encoding);

        if (is_array($value)) {
            $ret = array();
            foreach ($value as $k => $v) {
                if ($last) {
                    $pos = mb_strripos($v, $sep);
                } else {
                    $pos = mb_stripos($v, $sep);
                }
                if ($pos) {
                    $pos = $pos + mb_strlen($sep);
                    $ret[$k] = trim(mb_substr($v, $pos));
                } else {
                    $ret[$k] = $v;
                }
            }
            return $ret;
        }

        if (is_string($value)) {
            $ret = '';
            if ($last) {
                $pos = mb_strripos($value, $sep);
            } else {
                $pos = mb_stripos($value, $sep);
            }
            if ($pos) {
                $pos = $pos + mb_strlen($sep);
                $ret = trim(mb_substr($value, $pos));
            } else {
                $ret = $value;
            }
            return $ret;
        }

        return '';
    }

    public static function reverse($value = array(), $preserve_keys = 0)
    {
        $ret = array();

        $value = static::getValue($value);

        if (! is_array($value) || ! count($value)) {
            return $ret;
        }

        $ret = array_reverse($value, $preserve_keys);
        return $ret;
    }
}
