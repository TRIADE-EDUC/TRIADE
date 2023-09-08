<?php
include_once 'HTML/AJAX/JSON.php';
// $Id$
/**
 * JSON Serializer
 *
 * @category   HTML
 * @package    AJAX
 * @author     Joshua Eichorn <josh@bluga.net>
 * @copyright  2005 Joshua Eichorn
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/PackageName
 */
// {{{ class HTMLA_AJAX_Serialize_JSON
class HTML_AJAX_Serializer_JSON 
{
    // {{{ variables-properties
    /**
     * JSON instance
     * @var HTML_AJAX_JSON
     * @access private
     */
    var $_json;

    /**
     * use json php extension http://www.aurore.net/projects/php-json/
     * @access private
     */
    var $_jsonext;
    // }}}
    // {{{ constructor
    function HTML_AJAX_Serializer_JSON() 
    {
        $this->_jsonext = $this->_detect();
        if(!$this->_jsonext) {
            $this->_json = new HTML_AJAX_JSON();
        }
    }
    // }}}
    // {{{ serialize
    /**
     * This function serializes and input passed to it.
     *
     * @access public
     * @param  string $input   The input to serialize.
     * @return string $input   The serialized input.
     */
    function serialize($input) 
    {
        if($this->_jsonext) {
            return json_encode($input);
        } else {
            return $this->_json->encode($input);
        }
    }
    // }}}
    // {{{ unserialize
    /**
     * this function unserializes the input passed to it.
     *
     * @access public
     * @param  string $input   The input to unserialize
     * @return string $input   The unserialized input.
     */
    function unserialize($input) 
    {
        if($this->_jsonext) {
            return json_decode($input);
        } else {
            return $this->_json->decode($input);
        }
    }
    // }}}
    // {{{ _detect
    /**
     * detcts the loaded extension
     */
    function _detect()
    {
        return extension_loaded('json');
    }
    // }}}
}
// }}}
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
?>
