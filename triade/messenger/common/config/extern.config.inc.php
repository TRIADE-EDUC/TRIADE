<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2012 THeUDS           **
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

if ( !defined('INTRAMESSENGER') ) die();


## [FR] laisser vide si paramètres identiques à ceux d'IntraMessenger
## [EN] keep empty if parameters same IntraMessenger (same database).


# Mysql host (maybe : 'localhost') :
$extern_dbhost    = 'localhost';

# MySQL port number :
$extern_dbport = ''; 

# Mysql database :
$extern_database  = '';

# Mysql username :
$extern_dbuname   = '';                

# Mysql password :
$extern_dbpass    = '';

# Table prefix :
$extern_prefix    = '';


## Table prefix examples : 
#     acx_ (activeCollab)     aef_ (AEF)          llx_ (Dolibarr)     dc_ (Dotclear)        elgg (elgg)  
#     ez (ezpublish)          fud26_ (fudforum)   ibf_  (IP-Board)    jos_ (Joomla)         mos_ (Mambo)
#     minibbtable_ (minibb)   modx_ (modx)        mybb_ (mybb)        nucleus_ (Nucleus)    og_ (OpenGoo)
#     px_ (Phenix)            phorum (phorum)     phpbb_ (phpBB)      phpboost_ (phpboost)  fusion_ (php-fusion)
#     nuke (php-nuke)         phpr_ (phprojekt)   pp_ (ProjectPier)   smf_ (SMF)            frk (TaskFreak)
#     tc_ (textcube)          td_ (Trellis Desk)  tria_ (Triade)      tl_ (typolight)       webcal_ (webcalendar)
#     wp_ (WordPress)         xe61 (Xoops)        xmb_ (XMB)          yacs_ (YACS)          cms_ (CMS Made Simple)
#     cl_ (Claroline)         pligg_ (Pligg)      a_ (Malleo)         mdl_ (Moodle)         cb_ (Connectix-Boards) 
#     e107_ (e107)            phpizabi_ (PHPizabi) tine20_ (Tine)     phpmyfaq (phpMyFAQ)
#
#     ImpressCMS : see 'SDATA_DB_PREFIX' (before 'SDATA_DB_SALT')


# --------------- activeCollab ---------------
# Licence number (from file license.php)
if (!defined('LICENSE_KEY'))  define('LICENSE_KEY', '');


# --------------- Concrete ---------------
if (!defined('PASSWORD_SALT'))  define('PASSWORD_SALT', '');  
# see the file concrete/config/site.php


# --------------- Dotclear 2 ---------------
if (!defined('DC_MASTER_KEY'))  define('DC_MASTER_KEY','');
# see the file dotclear/inc/config.php


# --------------- ImpressCMS ---------------
if (!defined('SDATA_DB_SALT'))  define('SDATA_DB_SALT', '');


# --------------- Prestashop ---------------
if (!defined('_COOKIE_KEY_'))  define('_COOKIE_KEY_', '');


# --------------- Oxwall ---------------
if (!defined('OW_PASSWORD_SALT'))   define('OW_PASSWORD_SALT', '');


# --------------- typolight ---------------
# Only members :
$do_not_use_users = '';  

# OR (OU)

# Only users :
$do_not_use_members = '';  


# --------------- Triade ---------------
# Use Phenix in Triade :
$phenix_include_in_triade = 'X';

# Phenix table prefix :
$phenix_table_prefix     = 'tria_px_';

# Only school (seulement le personnel scolaire) :
$do_not_use_student = '';

# OR (OU)

# Only student (seulement les élèves) :
$do_not_use_school_members = '';  

# --------------- Kimai ---------------
# see the file kimai/includes/autoconf.php
$password_salt   = "";


# ---------------  ---------------
# ---------------  ---------------
# ---------------  ---------------


?>