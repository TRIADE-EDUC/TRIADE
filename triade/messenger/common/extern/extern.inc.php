<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2015 THeUDS           **
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
if ( !defined('INTRAMESSENGER') )
{
  exit;
}

function prevent_error_extern_option_missing()
{
  if (!defined("_EXTERNAL_AUTHENTICATION"))  define("_EXTERNAL_AUTHENTICATION", "");
}


function f_check_if_auth_exten_ok($t_user, $t_pass, $test_only = "")
{
  $t_verif_pass = "";
  if (defined("_EXTERNAL_AUTHENTICATION"))
  {
    if (_EXTERNAL_AUTHENTICATION != "")
    {
      $f = f_clean_username(_EXTERNAL_AUTHENTICATION);
      if (file_exists("../common/extern/" . $f . ".auth.inc.php"))
      {
        if (!defined("_EXTERNAL_AUTHENTICATION_NAME")) 
        {
          require("../common/extern/" . $f . ".auth.inc.php");
        }
        if (defined("_EXTERNAL_AUTHENTICATION_NAME")) 
        {
          if ($test_only != "") $t_verif_pass = "Ko";
          $t_verif_pass = f_external_authentication(strtolower($t_user), $t_pass);
          if ($t_verif_pass != 'OK') $t_verif_pass = f_external_authentication(f_clean_username($t_user), $t_pass);
        }
      }
      //
      // si include depuis /index.php :
      if (file_exists("common/extern/" . $f . ".auth.inc.php"))
      {
        if (!defined("_EXTERNAL_AUTHENTICATION_NAME")) 
        {
          require("common/extern/" . $f . ".auth.inc.php");
        }
        if (defined("_EXTERNAL_AUTHENTICATION_NAME")) 
        {
          if ($test_only != "") $t_verif_pass = "Ko";
          $t_verif_pass = f_external_authentication(strtolower($t_user), $t_pass);
          if ($t_verif_pass != 'OK') $t_verif_pass = f_external_authentication(f_clean_username($t_user), $t_pass);
        }
      }
    }
  }
  //
  if ($test_only == "")
  {
    if ($t_verif_pass != "OK") $t_verif_pass = 'KO-AUTH-EXT';
  }
  //
  return $t_verif_pass;
}


function f_nb_auth_extern()
{
  $nb_ext = 0;
  //
  if (defined("_EXTERNAL_AUTHENTICATION"))
  {
    if (_EXTERNAL_AUTHENTICATION != "")
    {
      $f = f_clean_username(_EXTERNAL_AUTHENTICATION);
      if (file_exists("../common/extern/" . $f . ".auth.inc.php"))  $nb_ext = 1;
      //
      // si include depuis /index.php :
      if (file_exists("common/extern/" . $f . ".auth.inc.php"))  $nb_ext = 1;
    }
  }
  //
  return $nb_ext;
}


function f_type_auth_extern()
{
  $typ_auth = "";
  //
  if (_EXTERNAL_AUTHENTICATION != "")
  {
    $f = f_clean_username(_EXTERNAL_AUTHENTICATION);
    //
    if (file_exists("../common/extern/" . $f . ".auth.inc.php"))
    {
      if (!defined("_EXTERNAL_AUTHENTICATION_NAME")) include("../common/extern/" . $f . ".auth.inc.php");
    }
    //
    // si include depuis /index.php :
    if (file_exists("common/extern/" . $f . ".auth.inc.php"))
    {
      if (!defined("_EXTERNAL_AUTHENTICATION_NAME")) include("common/extern/" . $f . ".auth.inc.php");
    }
    //
    if (defined("_EXTERNAL_AUTHENTICATION_NAME")) $typ_auth = _EXTERNAL_AUTHENTICATION_NAME;
  }
  //
  return $typ_auth;
}


function f_nb_unread_pm_extern($id_user)
{
  $nb_pm = 0;
  //
  $f = f_clean_username(_EXTERNAL_AUTHENTICATION);
  if ($f != "")
  {
    if (strstr("#phpbb2#phpbb3#smf#mybb#phorum#fudforum#joomla#pragmamx#collabtive####", $f))
    {
      if (file_exists("../common/extern/" . $f . ".auth.inc.php"))
      {
        if (!defined("_EXTERNAL_AUTHENTICATION_NAME")) include("../common/extern/" . $f . ".auth.inc.php");
        $t_user = f_get_username_of_id($id_user);
        $nb_pm = f_extern_nb_unread_pm($t_user);
      }
    }
  }
  //
  return $nb_pm;
}

function f_extern_auth_list()
{
  $ext = array();
  $ext[] = "68kb";
  $ext[] = "achievo";
  $ext[] = "activecollab";
  $ext[] = "adheo";
  $ext[] = "admidio";
  $ext[] = "aef";
  $ext[] = "agora";
  $ext[] = "aphpkb";
  $ext[] = "artiphp";
  $ext[] = "atutor";
  $ext[] = "b2evolution";
  $ext[] = "bewelcome";
  $ext[] = "bitweaver";
  $ext[] = "bigace";
  $ext[] = "bonfire";
  $ext[] = "chamilo";
  $ext[] = "claroline";
  $ext[] = "cmsmadesimple";
  $ext[] = "collabtive";
  $ext[] = "concrete";
  $ext[] = "connectixboards";
  $ext[] = "contao";
  $ext[] = "cotonti";
  $ext[] = "cpg";
  $ext[] = "cscart";
  $ext[] = "cuteflow";
  $ext[] = "dmanager";
  $ext[] = "docebo";
  $ext[] = "dokeos";
  $ext[] = "dolibarr";
  $ext[] = "dolphin";
  $ext[] = "dotclear1";
  $ext[] = "dotclear2";
  $ext[] = "dotproject";
  $ext[] = "dragonflycms";
  $ext[] = "drupal";
  $ext[] = "drupal7";
  $ext[] = "e107";
  $ext[] = "egroupware";
  $ext[] = "elgg";
  $ext[] = "etano";
  $ext[] = "etraxis";
  $ext[] = "epesi";
  $ext[] = "ezpublish";
  $ext[] = "fengoffice";
  $ext[] = "fluxbb";
  $ext[] = "freeway";
  $ext[] = "friendika";
  $ext[] = "frontaccount";
  $ext[] = "fsb2";
  $ext[] = "fudforum";
  $ext[] = "galette";
  $ext[] = "geeklog";
  $ext[] = "gepi";
  $ext[] = "groupoffice";
  $ext[] = "helpcenterlive";
  $ext[] = "hesk";
  $ext[] = "impresscms";
  $ext[] = "ipboard";
  $ext[] = "ipboard3";
  $ext[] = "issuemanager";
  $ext[] = "joomla";
  $ext[] = "kimai";
  $ext[] = "livecart";
  $ext[] = "ldap";
  $ext[] = "lodel";
  $ext[] = "malleo";
  $ext[] = "magento";
  $ext[] = "mahara";
  $ext[] = "mambo";
  $ext[] = "mantisbt";
  $ext[] = "minibb";
  $ext[] = "modx";
  $ext[] = "moodle";
  $ext[] = "mound";
  $ext[] = "mybb";
  $ext[] = "npds";
  $ext[] = "nucleus";
  $ext[] = "nukedklan";
  $ext[] = "obm";
  $ext[] = "ocportal";
  $ext[] = "oozaims";
  $ext[] = "opengoo";
  $ext[] = "openrealty";
  $ext[] = "oscmax";
  $ext[] = "oscommerce";
  $ext[] = "osticket";
  $ext[] = "ovidentia";
  $ext[] = "owl";
  $ext[] = "oxwall";
  $ext[] = "pbboard";
  $ext[] = "pcpin_chat";
  $ext[] = "phenix";
  $ext[] = "phorum";
  $ext[] = "phpbb2";
  $ext[] = "phpbb3";
  $ext[] = "phpbms";
  $ext[] = "phpboost";
  $ext[] = "phpcollab";
  $ext[] = "phpdug";
  $ext[] = "php_fusion";
  $ext[] = "phpgroupware";
  $ext[] = "phpfox";
  $ext[] = "phpfox-konsort";
  $ext[] = "phpizabi";
  $ext[] = "phpmyfaq";
  $ext[] = "phpnuke";
  $ext[] = "phprojekt";
  $ext[] = "phprojekt6";
  $ext[] = "phpscheduleit";
  $ext[] = "phpwcms";
  $ext[] = "pligg";
  $ext[] = "pms";
  $ext[] = "pragmamx";
  $ext[] = "prestashop";
  $ext[] = "projectorria";
  $ext[] = "projeqtor";
  $ext[] = "projectpier";
  $ext[] = "projelead";
  $ext[] = "promethee";
  $ext[] = "punbb1.2";
  $ext[] = "punbb1.4";
  $ext[] = "pyrocms";
  $ext[] = "qdpm";
  $ext[] = "question2answer";
  $ext[] = "serendipity";
  $ext[] = "sharetronix";
  $ext[] = "silverstripe";
  $ext[] = "simplegroupware";
  $ext[] = "sit";
  $ext[] = "skadate";
  $ext[] = "smf";
  //$ext[] = "smf_1.0";
  $ext[] = "socialengine";
  $ext[] = "spip";
  $ext[] = "statusnet";
  $ext[] = "streber";
  $ext[] = "sugarcrm";
  $ext[] = "taskfreak";
  $ext[] = "textcube";
  $ext[] = "textpattern";
  $ext[] = "thebuggenie";
  $ext[] = "thelia";
  $ext[] = "tikiwiki";
  $ext[] = "tine";
  $ext[] = "todoyu";
  $ext[] = "tomatocart";
  $ext[] = "toutateam";
  $ext[] = "traq";
  $ext[] = "trellisdesk";
  $ext[] = "triade";
  $ext[] = "typo3";
  $ext[] = "typolight";
  $ext[] = "ucenter";
  $ext[] = "vanilla";
  $ext[] = "vbulletin";
  $ext[] = "vcalendar";
  $ext[] = "vtigercrm";
  $ext[] = "wbblite";
  $ext[] = "web2project";
  $ext[] = "webcalendar";
  $ext[] = "webcollab";
  $ext[] = "weberp";
  $ext[] = "webissues";
  $ext[] = "websitebaker";
  $ext[] = "wordpress";
  $ext[] = "xmb";
  $ext[] = "xoops";
  $ext[] = "yacs";
  $ext[] = "zazavi";
  $ext[] = "zencart";
  $ext[] = "zikula";
  //
  return $ext;
}
?>