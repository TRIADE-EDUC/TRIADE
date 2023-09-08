<?php
/**
 * Copyright (c) 2003-2007, Klaus Guenther <klaus@capitalfocus.org>
 *                          Laurent Laville <pear@laurent-laville.org>
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the authors nor the names of its contributors
 *       may be used to endorse or promote products derived from this software
 *       without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   HTML
 * @package    HTML_CSS
 * @author     Klaus Guenther <klaus@capitalfocus.org>
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  2003-2007 Klaus Guenther, Laurent Laville
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    CVS: $Id: CSS.php,v 1.59 2007/06/12 15:09:13 farell Exp $
 * @link       http://pear.php.net/package/HTML_CSS
 * @since      File available since Release 0.2.0
 */

require_once 'HTML/Common.php';

/**#@+
 * Basic error codes
 *
 * @var        integer
 * @since      0.3.3
 */
define ('HTML_CSS_ERROR_UNKNOWN',                 -1);
define ('HTML_CSS_ERROR_INVALID_INPUT',         -100);
define ('HTML_CSS_ERROR_INVALID_GROUP',         -101);
define ('HTML_CSS_ERROR_NO_GROUP',              -102);
define ('HTML_CSS_ERROR_NO_ELEMENT',            -103);
define ('HTML_CSS_ERROR_NO_ELEMENT_PROPERTY',   -104);
define ('HTML_CSS_ERROR_NO_FILE',               -105);
define ('HTML_CSS_ERROR_WRITE_FILE',            -106);
/**#@-*/

/**
 * Base class for CSS definitions
 *
 * This class handles the details for creating properly
 * constructed CSS declarations.
 *
 * @category   HTML
 * @package    HTML_CSS
 * @author     Klaus Guenther <klaus@capitalfocus.org>
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  2003-2007 Klaus Guenther, Laurent Laville
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: 1.2.0
 * @link       http://pear.php.net/package/HTML_CSS
 * @since      Class available since Release 0.2.0
 */

class HTML_CSS extends HTML_Common
{
    /**
     * Contains the CSS definitions.
     *
     * @var        array
     * @since      0.2.0
     * @access     private
     */
    var $_css = array();

    /**
     * Contains "alibis" (other elements that share a definition) of an element defined in CSS
     *
     * @var        array
     * @since      0.2.0
     * @access     private
     */
    var $_alibis = array();

    /**
     * Controls caching of the page
     *
     * @var        bool
     * @since      0.2.0
     * @access     private
     * @see        setCache()
     */
    var $_cache = true;

    /**
     * Contains the character encoding string
     *
     * @var        string
     * @since      0.2.0
     * @access     private
     * @see        setCharset()
     */
    var $_charset = 'iso-8859-1';

    /**
     * Contains last assigned index for duplicate styles
     *
     * @var        array
     * @since      0.3.0
     * @access     private
     */
    var $_duplicateCounter = 0;

    /**
     * Contains grouped styles
     *
     * @var        array
     * @since      0.3.0
     * @access     private
     */
    var $_groups = array();

    /**
     * Determines whether groups are output prior to elements
     *
     * @var        array
     * @since      1.0.0
     * @access     private
     * @see        setOutputGroupsFirst()
     */
    var $_groupsFirst = true;

    /**
     * Number of CSS definition groups
     *
     * @var        int
     * @since      0.3.0
     * @access     private
     */
    var $_groupCount = 0;

    /**
     * Defines whether to output all properties on one line
     *
     * @var        bool
     * @since      0.3.3
     * @access     private
     * @see        setSingleLineOutput()
     */
    var $_singleLine = false;

    /**
     * Defines whether element selectors should be automatically lowercased.
     * Determines how parseSelectors treats the data.
     *
     * @var        bool
     * @since      0.3.2
     * @access     private
     * @see        setXhtmlCompliance()
     */
    var $_xhtmlCompliant = true;

    /**
     * Allows to have duplicate rules in selector
     * Useful for IE hack.
     *
     * @var        bool
     * @since      1.0.0
     * @access     private
     */
    var $_allowDuplicates = false;

    /**
     * Error message callback.
     * This will be used to generate the error message
     * from the error code.
     *
     * @var        false|string|array
     * @since      1.0.0
     * @access     private
     * @see        _initErrorStack()
     */
    var $_callback_message = false;

    /**
     * Error context callback.
     * This will be used to generate the error context for an error.
     *
     * @var        false|string|array
     * @since      1.0.0
     * @access     private
     * @see        _initErrorStack()
     */
    var $_callback_context = false;

    /**
     * Error push callback.
     * The return value will be used to determine whether to allow
     * an error to be pushed or logged.
     *
     * @var        false|string|array
     * @since      1.0.0
     * @access     private
     * @see        _initErrorStack()
     */
    var $_callback_push = false;

    /**
     * Error handler callback.
     * This will handle any errors raised by this package.
     *
     * @var        false|string|array
     * @since      1.0.0
     * @access     private
     * @see        _initErrorStack()
     */
    var $_callback_errorhandler = false;

    /**
     * Associative array of key-value pairs
     * that are used to specify any handler-specific settings.
     *
     * @var        array
     * @since      1.0.0
     * @access     private
     * @see        _initErrorStack()
     */
    var $_errorhandler_options = array();

    /**
     * Last error that might occured
     *
     * @var        false|mixed
     * @since      1.0.0RC2
     * @access     private
     * @see        isError(), raiseError()
     */
    var $_lastError = false;


    /**
     * Class constructor
     *
     * @param      array     $attributes    (optional) Pass options to the constructor.
     *                                       Valid options are :
     *                                       - xhtml (sets xhtml compliance),
     *                                       - tab (sets indent string),
     *                                       - filename (name of file to be parsed),
     *                                       - cache (determines whether the nocache headers are sent),
     *                                       - oneline (whether to output each definition on one line),
     *                                       - groupsfirst (determines whether to output groups before elements)
     *                                       - allowduplicates (allow to have duplicate rules in selector)
     * @param      array     $errorPrefs    (optional) has to configure error handler
     *
     * @since      0.2.0
     * @access     public
     */
    function HTML_CSS($attributes = array(), $errorPrefs = array())
    {
        $this->_initErrorStack($errorPrefs);

        if ($attributes) {
            $attributes = $this->_parseAttributes($attributes);
        }
        if ((isset($attributes['xhtml']))
            && (is_bool($attributes['xhtml']))) {
            $this->setXhtmlCompliance($attributes['xhtml']);
        }
        if (isset($attributes['tab'])
            && (is_string($attributes['tab']))) {
            $this->setTab($attributes['tab']);
        }
        if (isset($attributes['filename'])) {
            $this->parseFile($attributes['filename']);
        }
        if ((isset($attributes['cache']))
            && (is_bool($attributes['cache']))) {
            $this->setCache($attributes['cache']);
        }
        if ((isset($attributes['oneline']))
            && (is_bool($attributes['oneline']))) {
            $this->setSingleLineOutput($attributes['oneline']);
        }
        if ((isset($attributes['groupsfirst']))
            && (is_bool($attributes['groupsfirst']))) {
            $this->setOutputGroupsFirst($attributes['groupsfirst']);
        }
        if ((isset($attributes['allowduplicates']))
            && (is_bool($attributes['allowduplicates'])))  {
            $this->_allowDuplicates = $attributes['allowduplicates'];
        }
    }

    /**
     * Returns the current API version
     * Since 1.0.0 a string is returned rather than a float (for previous versions).
     *
     * @return     string                   compatible with php.version_compare()
     * @since      0.2.0
     * @access     public
     */
    function apiVersion()
    {
        return '1.2.0';
    }

    /**
     * Determines whether definitions are output single line or multiline
     *
     * @param      bool      $value
     *
     * @return     void|PEAR_Error
     * @since      0.3.3
     * @access     public
     * @throws     HTML_CSS_ERROR_INVALID_INPUT
     */
    function setSingleLineOutput($value)
    {
        if (!is_bool($value)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$value',
                      'was' => gettype($value),
                      'expected' => 'boolean',
                      'paramnum' => 1));
        }
        $this->_singleLine = $value;
    }

    /**
     * Determines whether groups are output before elements or not
     *
     * @param      bool      $value
     *
     * @return     void|PEAR_Error
     * @since      0.3.3
     * @access     public
     * @throws     HTML_CSS_ERROR_INVALID_INPUT
     */
    function setOutputGroupsFirst($value)
    {
        if (!is_bool($value)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$value',
                      'was' => gettype($value),
                      'expected' => 'boolean',
                      'paramnum' => 1));
        }
        $this->_groupsFirst = $value;
    }

    /**
     * Parses a string containing selector(s).
     * It processes it and returns an array or string containing
     * modified selectors (depends on XHTML compliance setting;
     * defaults to ensure lowercase element names)
     *
     * @param      string    $selectors     Selector string
     * @param      int       $outputMode    (optional) 0 = string; 1 = array; 2 = deep array
     *
     * @return     mixed|PEAR_Error
     * @since      0.3.2
     * @access     protected
     * @throws     HTML_CSS_ERROR_INVALID_INPUT
     */
    function parseSelectors($selectors, $outputMode = 0)
    {
        if (!is_string($selectors)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$selectors',
                      'was' => gettype($selectors),
                      'expected' => 'string',
                      'paramnum' => 1));

        } elseif (!is_int($outputMode)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$outputMode',
                      'was' => gettype($outputMode),
                      'expected' => 'integer',
                      'paramnum' => 2));

        } elseif ($outputMode < 0 || $outputMode > 3) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'error',
                array('var' => '$outputMode',
                      'was' => $outputMode,
                      'expected' => '0 | 1 | 2 | 3',
                      'paramnum' => 2));
        }

        $selectors_array =  explode(',', $selectors);
        $i = 0;
        foreach ($selectors_array as $selector) {
            // trim to remove possible whitespace
            $selector = trim($this->collapseInternalSpaces($selector));
            if (strpos($selector, ' ')) {
                $sel_a = array();
                foreach(explode(' ', $selector) as $sub_selector) {
                    $sel_a[] = $this->parseSelectors($sub_selector, $outputMode);
                }
                if ($outputMode === 0) {
                        $array[$i] = implode(' ', $sel_a);
                } else {
                    $sel_a2 = array();
                    foreach ($sel_a as $sel_a_temp) {
                        $sel_a2 = array_merge($sel_a2, $sel_a_temp);
                    }
                    if ($outputMode == 2) {
                        $array[$i]['inheritance'] = $sel_a2;
                    } else {
                        $array[$i] = implode(' ', $sel_a2);
                    }
                }
                $i++;
            } else {
                // initialize variables
                $element = '';
                $id      = '';
                $class   = '';
                $pseudo  = '';

                if (strpos($selector, ':') !== false) {
                    $pseudo   = strstr($selector, ':');
                    $selector = substr($selector, 0 , strpos($selector, ':'));
                }
                if (strpos($selector, '.') !== false){
                    $class    = strstr($selector, '.');
                    $selector = substr($selector, 0 , strpos($selector, '.'));
                }
                if (strpos($selector, '#') !== false) {
                    $id       = strstr($selector, '#');
                    $selector = substr($selector, 0 , strpos($selector, '#'));
                }
                if ($selector != '') {
                    $element  = $selector;
                }
                if ($this->_xhtmlCompliant){
                    $element  = strtolower($element);
                    $pseudo   = strtolower($pseudo);
                }
                if ($outputMode == 2) {
                    $array[$i]['element'] = $element;
                    $array[$i]['id']      = $id;
                    $array[$i]['class']   = $class;
                    $array[$i]['pseudo']  = $pseudo;
                } else {
                    $array[$i] = $element.$id.$class.$pseudo;
                }
                $i++;
            }
        }
        if ($outputMode == 0) {
            $output = implode(', ', $array);
            return $output;
        } else {
            return $array;
        }
    }

    /**
     * Strips excess spaces in string.
     *
     * @param      string    $subject       string to format
     *
     * @return     string
     * @since      0.3.2
     * @access     protected
     */
    function collapseInternalSpaces($subject)
    {
        $string = preg_replace('/\s+/', ' ', $subject);
        return $string;
    }

    /**
     * Sets XHTML compliance
     *
     * @param      bool      $value         Boolean value
     *
     * @return     void|PEAR_Error
     * @since      0.3.2
     * @access     public
     * @throws     HTML_CSS_ERROR_INVALID_INPUT
     */
    function setXhtmlCompliance($value)
    {
        if (!is_bool($value)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$value',
                      'was' => gettype($value),
                      'expected' => 'boolean',
                      'paramnum' => 1));
        }
        $this->_xhtmlCompliant = $value;
    }

    /**
     * Creates a new CSS definition group. Returns an integer identifying the group.
     *
     * @param      string    $selectors     Selector(s) to be defined, comma delimited.
     * @param      mixed     $group        (optional) Group identifier. If not passed,
     *                                      will return an automatically assigned integer.
     *
     * @return     mixed|PEAR_Error
     * @since      0.3.0
     * @access     public
     * @throws     HTML_CSS_ERROR_INVALID_INPUT, HTML_CSS_ERROR_INVALID_GROUP
     * @see        unsetGroup()
     */
    function createGroup($selectors, $group = null)
    {
        if (!is_string($selectors)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$selectors',
                      'was' => gettype($selectors),
                      'expected' => 'string',
                      'paramnum' => 1));
        }

        if (!isset($group)) {
            $this->_groupCount++;
            $group = $this->_groupCount;
        } else {
            if (isset($this->_groups['@-'.$group])){
                return $this->raiseError(HTML_CSS_ERROR_INVALID_GROUP, 'error',
                    array('identifier' => $group));
            }
        }

        $groupIdent = '@-'.$group;

        $selectors = $this->parseSelectors($selectors, 1);
        foreach ($selectors as $selector) {
            $this->_alibis[$selector][] = $groupIdent;
        }

        $this->_groups[$groupIdent] = $selectors;

        return $group;
    }

    /**
     * Sets or adds a CSS definition for a CSS definition group
     *
     * @param      mixed     $group         CSS definition group identifier
     *
     * @return     void|PEAR_Error
     * @since      0.3.0
     * @access     public
     * @throws     HTML_CSS_ERROR_INVALID_INPUT, HTML_CSS_ERROR_NO_GROUP
     * @see        createGroup()
     */
    function unsetGroup($group)
    {
        if (!is_int($group) && !is_string($group)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$group',
                      'was' => gettype($group),
                      'expected' => 'integer | string',
                      'paramnum' => 1));
        }
        $groupIdent = '@-'.$group;
        if ($group < 0 || $group > $this->_groupCount ||
            !isset($this->_groups[$groupIdent])) {
            return $this->raiseError(HTML_CSS_ERROR_NO_GROUP, 'error',
                array('identifier' => $group));
        }

        $alibis = $this->_alibis;
        foreach ($alibis as $selector => $data) {
            foreach ($data as $key => $value) {
                if ($value == $groupIdent) {
                    unset($this->_alibis[$selector][$key]);
                    break;
                }
            }
            if (count($this->_alibis[$selector]) == 0) {
                unset($this->_alibis[$selector]);
            }
        }
        unset($this->_groups[$groupIdent]);
        unset($this->_css[$groupIdent]);
    }

    /**
     * Sets or adds a CSS definition for a CSS definition group
     *
     * @param      mixed     $group         CSS definition group identifier
     * @param      string    $property      Property defined
     * @param      string    $value         Value assigned
     * @param      bool      $duplicates    (optional) Allow or disallow duplicates.
     *
     * @return     void|int|PEAR_Error     Returns an integer if duplicates
     *                                     are allowed.
     * @since      0.3.0
     * @access     public
     * @throws     HTML_CSS_ERROR_INVALID_INPUT, HTML_CSS_ERROR_NO_GROUP
     * @see        getGroupStyle()
     */
    function setGroupStyle($group, $property, $value, $duplicates = null)
    {
        if (!is_int($group) && !is_string($group)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$group',
                      'was' => gettype($group),
                      'expected' => 'integer | string',
                      'paramnum' => 1));

        } elseif (!is_string($property)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$property',
                      'was' => gettype($property),
                      'expected' => 'string',
                      'paramnum' => 2));

        } elseif (!is_string($value)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$value',
                      'was' => gettype($value),
                      'expected' => 'string',
                      'paramnum' => 3));

        } elseif (isset($duplicates) && !is_bool($duplicates)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$duplicates',
                      'was' => gettype($duplicates),
                      'expected' => 'bool',
                      'paramnum' => 4));
        }

        if (!isset($duplicates)) {
            $duplicates = $this->_allowDuplicates;
        }

        $groupIdent = '@-'.$group;
        if ($group < 0 || $group > $this->_groupCount ||
            !isset($this->_groups[$groupIdent])) {
            return $this->raiseError(HTML_CSS_ERROR_NO_GROUP, 'error',
                array('identifier' => $group));
        }

        if ($duplicates === true) {
            $this->_duplicateCounter++;
            $this->_css[$groupIdent][$this->_duplicateCounter][$property]= $value;
            return $this->_duplicateCounter;
        } else {
            $this->_css[$groupIdent][$property]= $value;
        }
    }

    /**
     * Returns a CSS definition for a CSS definition group
     *
     * @param      mixed     $group         CSS definition group identifier
     * @param      string    $property      Property defined
     *
     * @return     mixed|PEAR_Error
     * @since      0.3.0
     * @access     public
     * @throws     HTML_CSS_ERROR_INVALID_INPUT, HTML_CSS_ERROR_NO_GROUP,
     *             HTML_CSS_ERROR_NO_ELEMENT
     * @see        setGroupStyle()
     */
    function getGroupStyle($group, $property)
    {
        if (!is_int($group) && !is_string($group)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$group',
                      'was' => gettype($group),
                      'expected' => 'integer | string',
                      'paramnum' => 1));

        } elseif (!is_string($property)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$property',
                      'was' => gettype($property),
                      'expected' => 'string',
                      'paramnum' => 2));
        }
        $groupIdent = '@-'.$group;
        if ($group < 0 || $group > $this->_groupCount ||
            !isset($this->_groups[$groupIdent])) {
            return $this->raiseError(HTML_CSS_ERROR_NO_GROUP, 'error',
                array('identifier' => $group));
        }

        $styles = array();

        foreach ($this->_css[$groupIdent] as $rank => $prop) {
            // if the style is not duplicate
            if (!is_numeric($rank)) {
                $prop = array($rank => $prop);
            }
            foreach ($prop as $key => $value) {
                if ($key == $property) {
                    $styles[] = $value;
                }
            }
        }

        if (count($styles) < 2) {
            $styles = array_shift($styles);
        }
        return $styles;
    }

    /**
     * Adds a selector to a CSS definition group.
     *
     * @param    mixed   $group       CSS definition group identifier
     * @param    string  $selectors   Selector(s) to be defined, comma delimited.
     *
     * @return   void|PEAR_Error
     * @since    0.3.0
     * @access   public
     * @throws   HTML_CSS_ERROR_NO_GROUP, HTML_CSS_ERROR_INVALID_INPUT
     */
    function addGroupSelector($group, $selectors)
    {
        $groupIdent = '@-'.$group;
        if ($group < 0 || $group > $this->_groupCount ||
            !isset($this->_groups[$groupIdent])) {
            return $this->raiseError(HTML_CSS_ERROR_NO_GROUP, 'error',
                array('identifier' => $group));

        } elseif (!is_string($selectors)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$selectors',
                      'was' => gettype($selectors),
                      'expected' => 'string',
                      'paramnum' => 2));
        }

        $newSelectors = $this->parseSelectors($selectors, 1);
        foreach ($newSelectors as $selector) {
            $this->_alibis[$selector][] = $groupIdent;
        }

        $oldSelectors = $this->_groups[$groupIdent];
        $this->_groups[$groupIdent] = array_merge($oldSelectors, $newSelectors);
    }

    /**
     * Removes a selector from a group.
     *
     * @param    mixed   $group       CSS definition group identifier
     * @param    string  $selectors   Selector(s) to be removed, comma delimited.
     *
     * @return   void|PEAR_Error
     * @since    0.3.0
     * @access   public
     * @throws   HTML_CSS_ERROR_NO_GROUP, HTML_CSS_ERROR_INVALID_INPUT
     */
    function removeGroupSelector($group, $selectors)
    {
        $groupIdent = '@-'.$group;
        if ($group < 0 || $group > $this->_groupCount ||
            !isset($this->_groups[$groupIdent])) {
            return $this->raiseError(HTML_CSS_ERROR_NO_GROUP, 'error',
                array('identifier' => $group));

        } elseif (!is_string($selectors)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$selectors',
                      'was' => gettype($selectors),
                      'expected' => 'string',
                      'paramnum' => 2));
        }

        $oldSelectors = $this->_groups[$groupIdent];
        $selectors =  $this->parseSelectors($selectors, 1);
        foreach ($selectors as $selector) {
            foreach ($oldSelectors as $key => $value) {
                if ($value == $selector) {
                    unset($this->_groups[$groupIdent][$key]);
                }
            }
            foreach ($this->_alibis[$selector] as $key => $value) {
                if ($value == $groupIdent) {
                    unset($this->_alibis[$selector][$key]);
                }
            }
        }
    }

    /**
     * Sets or adds a CSS definition
     *
     * @param      string    $element       Element (or class) to be defined
     * @param      string    $property      Property defined
     * @param      string    $value         Value assigned
     * @param      bool      $duplicates    (optional) Allow or disallow duplicates.
     *
     * @return     void|PEAR_Error
     * @since      0.2.0
     * @access     public
     * @throws     HTML_CSS_ERROR_INVALID_INPUT
     * @see        getStyle()
     */
    function setStyle($element, $property, $value, $duplicates = null)
    {
        if (!is_string($element)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$element',
                      'was' => gettype($element),
                      'expected' => 'string',
                      'paramnum' => 1));

        } elseif (!is_string($property)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$property',
                      'was' => gettype($property),
                      'expected' => 'string',
                      'paramnum' => 2));

        } elseif (!is_string($value)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$value',
                      'was' => gettype($value),
                      'expected' => 'string',
                      'paramnum' => 3));

        } elseif (strpos($element, ',')) {
            // Check if there are any groups.
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'error',
                array('var' => '$element',
                      'was' => $element,
                      'expected' => 'string without comma',
                      'paramnum' => 1));

        } elseif (isset($duplicates) && !is_bool($duplicates)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$duplicates',
                      'was' => gettype($duplicates),
                      'expected' => 'bool',
                      'paramnum' => 4));
        }

        if (!isset($duplicates)) {
            $duplicates = $this->_allowDuplicates;
        }

        $element = $this->parseSelectors($element);

        if ($duplicates === true) {
            $this->_duplicateCounter++;
            $this->_css[$element][$this->_duplicateCounter][$property]= $value;
            return $this->_duplicateCounter;
        } else {
            $this->_css[$element][$property]= $value;
        }
    }

    /**
     * Retrieves the value of a CSS property
     *
     * @param      string    $element       Element (or class) to be defined
     * @param      string    $property      Property defined
     *
     * @return     mixed|PEAR_Error
     * @since      0.3.0
     * @access     public
     * @throws     HTML_CSS_ERROR_INVALID_INPUT,
     *             HTML_CSS_ERROR_NO_ELEMENT, HTML_CSS_ERROR_NO_ELEMENT_PROPERTY
     * @see        setStyle()
     */
    function getStyle($element, $property)
    {
        if (!is_string($element)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$element',
                      'was' => gettype($element),
                      'expected' => 'string',
                      'paramnum' => 1));

        } elseif (!is_string($property)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$property',
                      'was' => gettype($property),
                      'expected' => 'string',
                      'paramnum' => 2));
        }
        if (!isset($this->_css[$element]) && !isset($this->_alibis[$element])) {
            return $this->raiseError(HTML_CSS_ERROR_NO_ELEMENT, 'error',
                array('identifier' => $element));
        }

        if (isset($this->_alibis[$element])) {
            $lastImplementation = array_keys($this->_alibis[$element]);
            $lastImplementation = array_pop($lastImplementation);
            $group = substr($this->_alibis[$element][$lastImplementation], 2);
            $property_value = $this->getGroupStyle($group, $property);
        }
        if (isset($this->_css[$element]) && !isset($property_value)) {
            $property_value = array();
            foreach ($this->_css[$element] as $rank => $prop) {
                if(!is_numeric($rank)) {
                    $prop = array($rank => $prop);
                }
                 foreach ($prop as $key => $value) {
                     if ($key == $property) {
                         $property_value[] = $value;
                     }
                 }
            }
            if (count($property_value) == 1) {
                $property_value = $property_value[0];
            } elseif (count($property_value) == 0) {
                unset($property_value);
            }
        }

        if (!isset($property_value)) {
            return $this->raiseError(HTML_CSS_ERROR_NO_ELEMENT_PROPERTY, 'error',
                array('identifier' => $element,
                      'property'   => $property));
        }
        return $property_value;
    }

    /**
     * Return array entries of styles that match patterns (Perl compatible)
     *
     * @param      string    $elmPattern    Element or class pattern to retrieve
     * @param      string    $proPattern   (optional) Property pattern to retrieve
     *
     * @return     array|PEAR_Error
     * @since      1.1.0
     * @access     public
     * @throws     HTML_CSS_ERROR_INVALID_INPUT
     * @link       http://www.php.net/en/ref.pcre.php
     *             Regular Expression Functions (Perl-Compatible)
     */
    function grepStyle($elmPattern, $proPattern = null)
    {
        if (!is_string($elmPattern)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$elmPattern',
                      'was' => gettype($elmPattern),
                      'expected' => 'string',
                      'paramnum' => 1));

        } elseif (isset($proPattern) && !is_string($proPattern)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$proPattern',
                      'was' => gettype($proPattern),
                      'expected' => 'string',
                      'paramnum' => 2));
        }

        $styles = array();

        // first, search inside alibis
        $alibis = array_keys($this->_alibis);
        $alibis = preg_grep($elmPattern, $alibis);
        foreach ($alibis as $a) {
            foreach ($this->_alibis[$a] as $g) {
                if (isset($proPattern)) {
                    $properties = array_keys($this->_css[$g]);
                    $properties = preg_grep($proPattern, $properties);
                    if (count($properties) == 0) {
                        // this group does not have a such property pattern
                        continue;
                    }
                }
                if (isset($styles[$a])) {
                    $styles[$a] = array_merge($styles[$a], $this->_css[$g]);
                } else {
                    $styles[$a] = $this->_css[$g];
                }
            }
        }

        // second, search inside elements
        $elements = array_keys($this->_css);
        $elements = preg_grep($elmPattern, $elements);
        foreach ($elements as $e) {
            if (substr($e, 0, 1) == '@' ) {
                // excludes groups (already found with alibis)
                continue;
            }
            if (isset($proPattern)) {
                $properties = array_keys($this->_css[$e]);
                $properties = preg_grep($proPattern, $properties);
                if (count($properties) == 0) {
                    // this element does not have a such property pattern
                    continue;
                }
            }
            if (isset($styles[$e])) {
                $styles[$e] = array_merge($styles[$e], $this->_css[$e]);
            } else {
                $styles[$e] = $this->_css[$e];
            }
        }
        return $styles;
    }

    /**
     * Sets or changes the properties of new selectors to the values of an existing selector
     *
     * @param      string    $old           Selector that is already defined
     * @param      string    $new           New selector(s) that should share the same
     *                                      definitions, separated by commas
     * @return     void|PEAR_Error
     * @since      0.2.0
     * @access     public
     * @throws     HTML_CSS_ERROR_INVALID_INPUT, HTML_CSS_ERROR_NO_ELEMENT
     */
    function setSameStyle($new, $old)
    {
        if (!is_string($new)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$new',
                      'was' => gettype($new),
                      'expected' => 'string',
                      'paramnum' => 1));

        } elseif (!is_string($old)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$old',
                      'was' => gettype($old),
                      'expected' => 'string',
                      'paramnum' => 2));
        }

        $old = $this->parseSelectors($old);
        if (!isset($this->_css[$old])) {
            return $this->raiseError(HTML_CSS_ERROR_NO_ELEMENT, 'error',
                array('identifier' => $old));
        }

        $selector = implode(', ', array($old, $new));
        $grp = $this->createGroup($selector, 'samestyleas_'.$old);

        $others = $this->parseSelectors($new, 1);
        foreach ($others as $other) {
            $other = trim($other);
            foreach ($this->_css[$old] as $rank => $property) {
                if (!is_numeric($rank)) {
                    $property = array($rank => $property);
                }
                foreach ($property as $key => $value) {
                    $this->setGroupStyle($grp, $key, $value);
                }
            }
            unset($this->_css[$old]);
        }
    }

    /**
     * Defines if the document should be cached by the browser. Defaults to false.
     *
     * @param      bool      $cache         (optional)
     *
     * @return     void|PEAR_Error
     * @since      0.2.0
     * @access     public
     * @throws     HTML_CSS_ERROR_INVALID_INPUT
     */
    function setCache($cache = true)
    {
        if (!is_bool($cache)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$cache',
                      'was' => gettype($cache),
                      'expected' => 'boolean',
                      'paramnum' => 1));
        }

        $this->_cache = $cache;
    }

    /**
     * Defines the charset for the file. defaults to ISO-8859-1 because of CSS1
     * compatability issue for older browsers.
     *
     * @param      string    $type          (optional) Charset encoding; defaults to ISO-8859-1.
     *
     * @return     void|PEAR_Error
     * @since      0.2.0
     * @access     public
     * @throws     HTML_CSS_ERROR_INVALID_INPUT
     * @see        getCharset()
     */
    function setCharset($type = 'iso-8859-1')
    {
        if (!is_string($type)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$type',
                      'was' => gettype($type),
                      'expected' => 'string',
                      'paramnum' => 1));
        }

        $this->_charset = $type;
    }

    /**
     * Returns the charset encoding string
     *
     * @return     string
     * @since      0.2.0
     * @access     public
     * @see        setCharset()
     */
    function getCharset()
    {
        return $this->_charset;
    }

    /**
     * Parse a textstring that contains css information
     *
     * @param      string    $str           text string to parse
     * @param      bool      $duplicates    (optional) Allows or disallows duplicate style definitions
     *
     * @return     void|PEAR_Error
     * @since      0.3.0
     * @access     public
     * @throws     HTML_CSS_ERROR_INVALID_INPUT
     * @see        createGroup(), setGroupStyle(), setStyle()
     */
    function parseString($str, $duplicates = null)
    {
        if (!is_string($str)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$str',
                      'was' => gettype($str),
                      'expected' => 'string',
                      'paramnum' => 1));

        } elseif (isset($duplicates) && !is_bool($duplicates)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$duplicates',
                      'was' => gettype($duplicates),
                      'expected' => 'bool',
                      'paramnum' => 2));
        }

        if (!isset($duplicates)) {
            $duplicates = $this->_allowDuplicates;
        }

        // Remove comments
        $str = preg_replace("/\/\*(.*)?\*\//Usi", '', $str);

        // Protect parser vs IE hack
        $str = str_replace('"\"}\""', '#34#125#34', $str);

        // Parse each element of csscode
        $parts = explode("}",$str);
        foreach($parts as $part) {
            $part = trim($part);
            if (strlen($part) > 0) {

                // Parse each group of element in csscode
                list($keystr,$codestr) = explode("{",$part);
                $key_a = $this->parseSelectors($keystr, 1);
                $keystr = implode(', ', $key_a);
                // Check if there are any groups.
                if (strpos($keystr, ',')) {
                    $group = $this->createGroup($keystr);

                    // Parse each property of an element
                    $codes = explode(";",trim($codestr));
                    foreach ($codes as $code) {
                        if (strlen(trim($code)) > 0) {
                            // find the property and the value
                            $property = trim(substr($code, 0 , strpos($code, ':', 0)));
                            $value    = trim(substr($code, strpos($code, ':', 0) + 1));
                            // IE hack only
                            if (strcasecmp($property, 'voice-family') == 0) {
                                $value = str_replace('#34#125#34', '"\"}\""', $value);
                            }
                            $this->setGroupStyle($group, $property, $value, $duplicates);
                        }
                    }
                } else {

                    // let's get on with regular definitions
                    $key = trim($keystr);
                    if (strlen($key) > 0) {
                        // Parse each property of an element
                        $codes = explode(";",trim($codestr));
                        foreach ($codes as $code) {
                            if (strlen(trim($code)) > 0) {
                                $property = trim(substr($code, 0 , strpos($code, ':')));
                                $value    = substr($code, strpos($code, ':') + 1);
                                // IE hack only
                                if (strcasecmp($property, 'voice-family') == 0) {
                                    $value = str_replace('#34#125#34', '"\"}\""', $value);
                                }
                                $this->setStyle($key, $property, trim($value), $duplicates);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Parse a file that contains CSS information
     *
     * @param      string    $filename      file to parse
     * @param      bool      $duplicates    (optional) Allow or disallow duplicates.
     *
     * @return     void|PEAR_Error
     * @since      0.3.0
     * @access     public
     * @throws     HTML_CSS_ERROR_INVALID_INPUT, HTML_CSS_ERROR_NO_FILE
     * @see        parseString()
     */
    function parseFile($filename, $duplicates = null)
    {
        if (!is_string($filename)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$filename',
                      'was' => gettype($filename),
                      'expected' => 'string',
                      'paramnum' => 1));

        } elseif (!file_exists($filename)) {
            return $this->raiseError(HTML_CSS_ERROR_NO_FILE, 'error',
                    array('identifier' => $filename));

        } elseif (isset($duplicates) && !is_bool($duplicates)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$duplicates',
                      'was' => gettype($duplicates),
                      'expected' => 'bool',
                      'paramnum' => 2));
        }

        if (!isset($duplicates)) {
            $duplicates = $this->_allowDuplicates;
        }

        if (function_exists('file_get_contents')){
            $this->parseString(file_get_contents($filename), $duplicates);
        } else {
            $file = fopen("$filename", "rb");
            $this->parseString(fread($file, filesize($filename)), $duplicates);
            fclose($file);
        }
    }

    /**
     * Parse data sources, file(s) or string(s), that contains CSS information
     *
     * @param      array     $styles        data sources to parse
     * @param      bool      $duplicates    (optional) Allow or disallow duplicates.
     *
     * @return     void|PEAR_Error
     * @since      1.0.0RC2
     * @access     public
     * @throws     HTML_CSS_ERROR_INVALID_INPUT
     * @see        parseString(), parseFile()
     */
    function parseData($styles, $duplicates = null)
    {
        if (!is_array($styles)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$styles',
                      'was' => gettype($styles),
                      'expected' => 'array',
                      'paramnum' => 1));

        } elseif (isset($duplicates) && !is_bool($duplicates)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$duplicates',
                      'was' => gettype($duplicates),
                      'expected' => 'bool',
                      'paramnum' => 2));
        }

        if (!isset($duplicates)) {
            $duplicates = $this->_allowDuplicates;
        }

        foreach($styles as $style) {
            if (strcasecmp(substr($style, -4,4), '.css') == 0) {
                $res = $this->parseFile($style, $duplicates);
            } else {
                $res = $this->parseString($style, $duplicates);
            }
            if (!is_bool($this->_lastError)) {
                return $res;
            }
        }
    }

    /**
     * Returns the array of CSS properties
     *
     * @return     array
     * @since      0.2.0
     * @access     public
     */
    function toArray()
    {
        $css = array();
        foreach ($this->_css as $key => $value) {
            if (strpos($key, '@-') === 0) {
                $key = implode(', ', $this->_groups[$key]);
            }
            $css[$key] = $value;
        }
        return $css;
    }

    /**
     * Generates and returns the CSS properties of an element or class as a string for inline use.
     *
     * @param      string    $element       Element or class for which inline CSS should be generated
     *
     * @return     string|PEAR_Error
     * @since      0.2.0
     * @access     public
     * @throws     HTML_CSS_ERROR_INVALID_INPUT
     */
    function toInline($element)
    {
        if (!is_string($element)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$element',
                      'was' => gettype($element),
                      'expected' => 'string',
                      'paramnum' => 1));
        }

        $strCss = '';
        $newCssArray = array();

        // This allows for grouped elements definitions to work
        if (isset($this->_alibis[$element])) {
            $alibis = $this->_alibis[$element];

            // All the groups must be run through to be able to
            // properly assign the value to the inline.
            foreach ($alibis as $alibi) {
                foreach ($this->_css[$alibi] as $key => $value) {
                    $newCssArray[$key] = $value;
                }
            }
        }

        // This allows for single elements definitions to work
        if (isset($this->_css[$element])) {
            foreach ($this->_css[$element] as $rank => $property) {
                if (!is_numeric($rank)) {
                    $property = array($rank => $property);
                }
                foreach ($property as $key => $value) {
                    if ($key != 'other-elements') {
                        $newCssArray[$key] = $value;
                    }
                }
            }
        }

        foreach ($newCssArray as $key => $value) {
            $strCss .= $key . ':' . $value . ";";
        }

        return $strCss;
    }

    /**
     * Generates CSS and stores it in a file.
     *
     * @param      string    $filename      Name of file that content the stylesheet
     *
     * @return     void|PEAR_Error
     * @since      0.3.0
     * @access     public
     * @throws     HTML_CSS_ERROR_INVALID_INPUT, HTML_CSS_ERROR_WRITE_FILE
     * @see        toString()
     */
    function toFile($filename)
    {
        if (!is_string($filename)) {
            return $this->raiseError(HTML_CSS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$filename',
                      'was' => gettype($filename),
                      'expected' => 'string',
                      'paramnum' => 1));
        }

        if (function_exists('file_put_contents')){
            file_put_contents($filename, $this->toString());
        } else {
            $file = fopen($filename,'wb');
            fwrite($file, $this->toString());
            fclose($file);
        }
        if (!file_exists($filename)){
            return $this->raiseError(HTML_CSS_ERROR_WRITE_FILE, 'error',
                    array('filename' => $filename));
        }
    }

    /**
     * Generates and returns the complete CSS as a string.
     *
     * @return     string
     * @since      0.2.0
     * @access     public
     */
    function toString()
    {
        // get line endings
        $lnEnd = $this->_getLineEnd();
        $tabs  = $this->_getTabs();
        $tab   = $this->_getTab();

        // initialize $alibis
        $alibis = array();

        $strCss = '';

        // Allow a CSS comment
        if ($this->_comment) {
            $strCss = $tabs . '/* ' . $this->getComment() . ' */' . $lnEnd;
        }

        // If groups are to be output first, initialize a special variable
        if ($this->_groupsFirst) {
            $strCssElements = '';
        }

        // Iterate through the array and process each element
        foreach ($this->_css as $identifier => $rank) {

            // Groups are handled separately
            if (strpos($identifier, '@-') !== false) {
                // its a group
                $element = implode (', ', $this->_groups[$identifier]);
            } else {
                $element = $identifier;
            }

            // Start CSS element definition
            $definition = $element . ' {' . $lnEnd;

            // Iterate through the array of properties
            foreach ($rank as $pos => $property) {
                // check to see if it is a duplicate
                if (!is_numeric($pos)) {
                    $property = array($pos => $property);
                    unset($pos);
                }
                foreach ($property as $key => $value) {
                    $definition .= $tabs . $tab . $key . ': ' . $value . ';' . $lnEnd;
                }
            }

            // end CSS element definition
            $definition .= $tabs . '}';

            // if this is to be on a single line, collapse
            if ($this->_singleLine) {
                $definition = $this->collapseInternalSpaces($definition);
            }

            // if groups are to be output first, elements must be placed in a
            // different string which will be appended in the end
            if ($this->_groupsFirst === true && strpos($identifier, '@-') === false) {
                // add to elements
                $strCssElements .= $lnEnd . $tabs . $definition . $lnEnd;
            } else {
                // add to strCss
                $strCss .= $lnEnd . $tabs . $definition . $lnEnd;
            }
        }

        if ($this->_groupsFirst) {
            $strCss .= $strCssElements;
        }

        if ($this->_singleLine) {
            $strCss = str_replace($lnEnd.$lnEnd, $lnEnd, $strCss);
        }

        $strCss = preg_replace('/^(\n|\r\n|\r)/', '', $strCss);
        return $strCss;
    }

    /**
     * Outputs the stylesheet to the browser.
     *
     * @return     void
     * @since      0.2.0
     * @access     public
     * @see        toString()
     */
    function display()
    {
        if($this->_cache !== true) {
            header("Expires: Tue, 1 Jan 1980 12:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-cache");
            header("Pragma: no-cache");
        }

        // set character encoding
        header("Content-Type: text/css; charset=" . $this->_charset);

        $strCss = $this->toString();
        print $strCss;
    }

    /**
     * Initialize Error engine preferences
     *
     * @param      array     $prefs         (optional) hash of params to customize error generation
     * @return     void
     * @since      0.3.3
     * @access     private
     */
    function _initErrorStack($prefs = array())
    {
        // error message mapping callback
        if (isset($prefs['message_callback']) && is_callable($prefs['message_callback'])) {
            $this->_callback_message = $prefs['message_callback'];
        } else {
            $this->_callback_message = array('HTML_CSS_Error', '_msgCallback');
        }

        // error context mapping callback
        if (isset($prefs['context_callback']) && is_callable($prefs['context_callback'])) {
            $this->_callback_context = $prefs['context_callback'];
        } else {
            $this->_callback_context = array('HTML_CSS_Error', 'getBacktrace');
        }

        // determine whether to allow an error to be pushed or logged
        if (isset($prefs['push_callback']) && is_callable($prefs['push_callback'])) {
            $this->_callback_push = $prefs['push_callback'];
        } else {
            $this->_callback_push = array('HTML_CSS_Error', '_handleError');
        }

        // default error handler will use PEAR_Error
        if (isset($prefs['error_handler']) && is_callable($prefs['error_handler'])) {
            $this->_callback_errorhandler = $prefs['error_handler'];
        } else {
            $this->_callback_errorhandler = array(&$this, '_errorHandler');
        }

        // any handler-specific settings
        if (isset($prefs['handler'])) {
            $this->_errorhandler_options = $prefs['handler'];
        }
    }

    /**
     * Standard error handler that will use PEAR_Error object
     *
     * To improve performances, the PEAR.php file is included dynamically.
     * The file is so included only when an error is triggered. So, in most
     * cases, the file isn't included and perfs are much better.
     *
     * @param      integer   $code       Error code.
     * @param      string    $level      The error level of the message.
     * @param      array     $params     Associative array of error parameters
     *
     * @return     PEAR_Error
     * @since      1.0.0
     * @access     private
     */
    function _errorHandler($code, $level, $params)
    {
        include_once 'HTML/CSS/Error.php';

        $mode = call_user_func($this->_callback_push, $code, $level);

        $message = call_user_func($this->_callback_message, $code, $params);
        $userinfo['level'] = $level;

        if (isset($this->_errorhandler_options['display'])) {
            $userinfo['display'] = $this->_errorhandler_options['display'];
        } else {
            $userinfo['display'] = array();
        }
        if (isset($this->_errorhandler_options['log'])) {
            $userinfo['log'] = $this->_errorhandler_options['log'];
        } else {
            $userinfo['log'] = array();
        }

        return PEAR::raiseError($message, $code, $mode, null, $userinfo, 'HTML_CSS_Error');
    }

    /**
     * A basic wrapper around the default PEAR_Error object
     *
     * @return     object                PEAR_Error when default error handler is used
     * @since      0.3.3
     * @access     public
     * @see        _errorHandler()
     */
    function raiseError()
    {
        $args = func_get_args();
        $this->_lastError = call_user_func_array($this->_callback_errorhandler, $args);
        return $this->_lastError;
    }

    /**
     * Determine whether there is an error
     *
     * @return     boolean               TRUE if error raised, FALSE otherwise
     * @since      1.0.0RC2
     * @access     public
     */
    function isError()
    {
         $res = (!is_bool($this->_lastError));
         $this->_lastError = false;
         return $res;
    }
}
?>