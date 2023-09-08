<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2011 THeUDS           **
 **  Web:            http://www.theuds.com            **
 **                  http://www.intramessenger.net    **
 **  Licence :       GPL (GNU Public License)         **
 **  http://opensource.org/licenses/gpl-license.php   **
 *******************************************************/

/*******************************************************
 **       This file is part of IntraMessenger-server  **
 **                                                   **
 **  IntraMessenger is a free software.               **
 **  IntraMessenger is distributed in the hope that   **
 **  it will be useful, but WITHOUT ANY WARRANTY.     **
 *******************************************************/
//
if ( !defined('INTRAMESSENGER') ) die(); 

define('_UP_FOLDER_AUTH_EXT', '');

#
#
#  define('_UP_FOLDER_AUTH_EXT', '');
#  Not on addon/plugin mode.
#
#
#  define('_UP_FOLDER_AUTH_EXT', '../../');
#  If IntraMessenger INSIDE your forum/CMS, example : http://yourserver/forum/intramessenger/
#
#
#  define('_UP_FOLDER_AUTH_EXT', '../../forum/');
#  If IntraMessenger OUTSIDE your forum/CMS, example: http://yourserver/intramessenger/ (forum is in http://yourserver/forum/)
#
#
#  define('_UP_FOLDER_AUTH_EXT', '../../../');
#  If IntraMessenger OUTSIDE your forum/CMS, example: http://yourserver/joomla/modules/intramessenger/
#
?>