<?php
// {{{ class HTML_AJAX_SErialize_Urlencoded
// $Id$
/**
 * URL Encoding Serializer
 *
 * @category   HTML
 * @package    AJAX
 * @author     Arpad Ray <arpad@php.net>
 * @author     David Coallier <davidc@php.net>
 * @copyright  2005 Arpad Ray
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/HTML_AJAX
 */
class HTML_AJAX_Serialize_Urlencoded
{
    // {{{ serialize
    function serialize($input) 
    {
        if (!function_exists('http_build_query')) {
            return $this->local_build_query(array('_HTML_AJAX' => $input));
        }
        return http_build_query(array('_HTML_AJAX' => $input));
        //return http_build_query($input);
    }
    // }}}
    // {{{ unserialize
    function unserialize($input) 
    {
        parse_str($input, $ret);
        return (isset($ret['_HTML_AJAX']) ? $ret['_HTML_AJAX'] : $ret);
    }
    // }}}
    // {{{ local_build_query
    /**
     * This function will be called when the function
     * http_build_query doesn't exist.
     *
     * @access public
     * @credit vlad_mustafin@ukr.net
     * @param  array $datum    The data to aggregate
     * @param  string $numeric_prefix
     * @param  string $key
     * @return array Imploaded values, a built url query.
     */
    function local_build_query($formdata, 
                               $numeric_prefix = null, 
                               $key = null ) 
    {
        $res = array();

        foreach ((array)$formdata as $k=>$v) {
            $tmp_key = urlencode(is_int($k) ? $numeric_prefix.$k : $k);
            if ($key) {
               $tmp_key = $key.'['.$tmp_key.']';
            }
            
            if (is_array($v) || is_object($v)) {
                $res[] = $this->local_build_query($v, null ,$tmp_key);
            } else {
                $res[] = $tmp_key."=".urlencode($v);
            } 
        }
        $separator = ini_get('arg_separator.output') ? 
                            ini_get('arg_separator.output') : 
                            '&';
        return implode($separator, $res);
    }
    // }}}
}
// }}}
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
?>
