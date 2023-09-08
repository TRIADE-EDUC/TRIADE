<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
/**
 * Simplest possible usage of HTML_AJAX_Server
 *
 * The server responds to ajax calls and also serves the js client libraries, so they can be used directly from the PEAR data dir
 * 304 not modified headers are used when server client libraries so they will be cached on the browser reducing overhead
 *
 * @category   HTML
 * @package    AJAX
 * @author     Joshua Eichorn <josh@bluga.net>
 * @copyright  2005 Joshua Eichorn
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/HTML_AJAX
 */

 // include the server class

if (file_exists("../common/config.inc.php")) include_once("../common/config.inc.php");
if (file_exists("../../common/config.inc.php")) include_once("../../common/config.inc.php");
include_once('HTML/AJAX/Server.php');

// include the test class will be registering
// include 'support/test.class.php';

// create our new server
$server = new HTML_AJAX_Server();

// register an instance of the class were registering
// $test = new test();
// $server->registerClass($test);

// handle different types of requests possiblities are
// ?client=all - request for all javascript client files
// ?stub=classname - request for proxy stub for given class, can be combined with client but this may hurt caching unless stub=all is used
// ?c=classname&m=method - an ajax call, server handles serialization and handing things off to the proper method then returning the results
$server->handleRequest();


?>
