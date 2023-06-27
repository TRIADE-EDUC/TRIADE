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
//
require ("../common/display_errors.inc.php"); 
//
if (isset($_REQUEST['lang'])) $lang = $_REQUEST['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
//
$extern_prefix = ''; 
$extern_dbhost = ''; 
$extern_database = ''; 
$extern_dbuname = ''; 
$extern_dbpass = ''; 
$do_not_use_users = ''; 
$do_not_use_members = ''; 
$phenix_include_in_triade = ''; 
$phenix_table_prefix = ''; 
$do_not_use_student = ''; 
$do_not_use_school_members = ''; 
require ("../common/config/extern.config.inc.php");
if (!defined('LICENSE_KEY'))        define('LICENSE_KEY', ''); 
if (!defined('PASSWORD_SALT'))      define('PASSWORD_SALT', ''); 
if (!defined('DC_MASTER_KEY'))      define('DC_MASTER_KEY', ''); 
if (!defined('SDATA_DB_SALT'))      define('SDATA_DB_SALT', ''); 
if (!defined('_COOKIE_KEY_'))       define('_COOKIE_KEY_', ''); 
if (!defined('OW_PASSWORD_SALT'))   define('OW_PASSWORD_SALT', ''); 
//
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_options);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_options_info_10 . "</title>";
display_header();
//echo '<META http-equiv="refresh" content="400;url="> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<font face='verdana' size='2'>";
//  
$external_authentication_name = "";
$external_authentication = _EXTERNAL_AUTHENTICATION;
if ($external_authentication != "") 
{
  $external_authentication_name = f_type_auth_extern();
  if ($external_authentication_name == "") $external_authentication = "";
}
//
$lst = "";
$extern_auth_list = array();
$extern_auth_list = f_extern_auth_list();
foreach ($extern_auth_list as $name) 
{
  $lst .= $name . ", ";
}
$lst = substr($lst, 0, (strlen($lst)-2) ) . "...";
//
function f_example_prefix($ext_auth)
{
  $ret = "";
  //
  if ($ext_auth == "activecollab")    $ret = "acx_";
  if ($ext_auth == "aef")             $ret = "aef_";
  if ($ext_auth == "artiphp")         $ret = "aphp5_";
  if ($ext_auth == "atutor")          $ret = "AT_";
  if ($ext_auth == "b2evolution")     $ret = "evo_";
  if ($ext_auth == "bigace")          $ret = "cms_";
  if ($ext_auth == "bonfire")         $ret = "bf_";
  if ($ext_auth == "claroline")       $ret = "cl_";
  if ($ext_auth == "cmsmadesimple")   $ret = "cms_";
  if ($ext_auth == "connectixboards") $ret = "cb_";
  if ($ext_auth == "cotonti")         $ret = "cot_";
  if ($ext_auth == "cpg")             $ret = "cpg15x_";
  if ($ext_auth == "cscart")          $ret = "cscart_";
  if ($ext_auth == "dmanager")        $ret = "dm_";
  if ($ext_auth == "dolibarr")        $ret = "llx_";
  if ($ext_auth == "dotclear1")       $ret = "dc_";
  if ($ext_auth == "dotclear2")       $ret = "dc_";
  if ($ext_auth == "dragonflycms")    $ret = "cms";
  if ($ext_auth == "e107")            $ret = "e107_";
  if ($ext_auth == "elgg")            $ret = "elgg";
  if ($ext_auth == "etano")           $ret = "dsb_";
  if ($ext_auth == "ezpublish")       $ret = "ez";
  if ($ext_auth == "fengoffice")      $ret = "og_";
  if ($ext_auth == "frontaccount")    $ret = "0_";
  if ($ext_auth == "fsb2")            $ret = "fsb2_";
  if ($ext_auth == "fudforum")        $ret = "fud30_";
  if ($ext_auth == "galette")         $ret = "galette_";
  if ($ext_auth == "geeklog")         $ret = "gl_";
  if ($ext_auth == "helpcenterlive")  $ret = "hcl_";
  if ($ext_auth == "ipboard")         $ret = "ibf_";
  if ($ext_auth == "joomla")          $ret = "jos_";
  if ($ext_auth == "kimai")           $ret = "kimai_";
  if ($ext_auth == "lodel")           $ret = "lodel_";
  if ($ext_auth == "malleo")          $ret = "a_";
  if ($ext_auth == "mambo")           $ret = "mos_";
  if ($ext_auth == "mantisbt")        $ret = "mantis_";
  if ($ext_auth == "minibb")          $ret = "minibbtable_";
  if ($ext_auth == "modx")            $ret = "modx_";
  if ($ext_auth == "moodle")          $ret = "mdl_";
  if ($ext_auth == "mybb")            $ret = "mybb_";
  if ($ext_auth == "nucleus")         $ret = "nucleus_";
  if ($ext_auth == "nukedklan")       $ret = "nuked";
  if ($ext_auth == "ocportal")        $ret = "ocp_";
  if ($ext_auth == "oozaims")         $ret = "aims_";
  if ($ext_auth == "opengoo")         $ret = "og_";
  if ($ext_auth == "openrealty")      $ret = "default_";
  if ($ext_auth == "oxwall")          $ret = "ox_";
  if ($ext_auth == "pbboard")         $ret = "pbb_";
  if ($ext_auth == "pcpin_chat")      $ret = "pcpin_";
  if ($ext_auth == "phenix")          $ret = "px_";
  if ($ext_auth == "phorum")          $ret = "phorum";
  if ($ext_auth == "php_fusion")      $ret = "fusion_";
  if ($ext_auth == "phpbb2")          $ret = "phpbb_";
  if ($ext_auth == "phpbb3")          $ret = "phpbb_";
  if ($ext_auth == "phpboost")        $ret = "phpboost_";
  if ($ext_auth == "phpdug")          $ret = "dug_";
  if ($ext_auth == "phpizabi")        $ret = "phpizabi_";
  if ($ext_auth == "phpmyfaq")        $ret = "phpmyfaq";
  if ($ext_auth == "phpnuke")         $ret = "nuke";
  if ($ext_auth == "phprojekt")       $ret = "phpr_";
  if ($ext_auth == "phpwcms")         $ret = "phpwcms_";
  if ($ext_auth == "pligg")           $ret = "pligg_";
  if ($ext_auth == "projectpier")     $ret = "pp_";
  if ($ext_auth == "question2answer") $ret = "qa_";
  if ($ext_auth == "serendipity")     $ret = "serendipity_";
  if ($ext_auth == "socialengine")    $ret = "se_";
  if ($ext_auth == "smf")             $ret = "smf_";
  if ($ext_auth == "smf_1.0")         $ret = "smf_";
  if ($ext_auth == "taskfreak")       $ret = "frk";
  if ($ext_auth == "textcube")        $ret = "tc_";
  if ($ext_auth == "tine")            $ret = "tine20_";
  if ($ext_auth == "tomatocart")      $ret = "toc_";
  if ($ext_auth == "trellisdesk")     $ret = "td_";
  if ($ext_auth == "triade")          $ret = "tria_";
  if ($ext_auth == "typolight")       $ret = "tl_";
  if ($ext_auth == "vanilla")         $ret = "LUM_";
  if ($ext_auth == "webcalendar")     $ret = "webcal_";
  if ($ext_auth == "wordpress")       $ret = "wp_";
  if ($ext_auth == "xmb")             $ret = "xmb_";
  if ($ext_auth == "xoops")           $ret = "xe61";
  if ($ext_auth == "yacs")            $ret = "yacs_";
  //
  return $ret;
}
//
function empty_table_row()
{
  echo "<TR>";
  echo "<TD colspan='2' class='row3'><font size='1'>&nbsp;";
  echo "</TD>";
  echo "</TR>";
}
//
if (!is_writeable("../common/config/extern.config.inc.php"))
{
  echo "<I>/common/config/extern.config.inc.php</I> : ";
  echo "<FONT COLOR='RED'><B> " . $l_admin_check_not_writeable . " !";
  //header("location:list_options.php?lang=" . $lang . "&");
  die();
}
//

echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='700'>";
  echo "<FORM METHOD='POST' ACTION='list_options_updating.php?'>";
  echo "<TR>";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    //echo "<font face=verdana size=3><b>" . $l_admin_options_title . "</b></font></TH>";
    echo "<font face=verdana size=3><b>" . $l_admin_options_info_10 . " : ";
    if ($external_authentication_name != "") 
      echo $external_authentication_name;
    else
    {
      echo "<font color='white'>" . $l_admin_check_off . "</font>&nbsp; : &nbsp;";
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
      echo "<INPUT TYPE='hidden' name='onglet' value = '4' />";
      echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_update . "' class='liteoption' />";
    }
    echo "</b></font></TH>";
  echo "</TR>";
  echo "</FORM>";
  //
  echo "<TR>";
    //display_row_table($l_admin_options_col_option, '');
    display_row_table($l_admin_options_col_description, '');
    display_row_table("&nbsp;" . $l_admin_options_col_value . "&nbsp;", '');
  echo "</TR>";

  echo "<FORM METHOD='POST' ACTION='list_options_auth_update.php?'>";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";

  echo "<TR>";
  echo "<TD width='' class='row2'>";
  echo "<FONT size='2'>";
  $t = f_example_prefix($external_authentication);
  if ($lang == "FR")
  {
    echo "Préfixes des tables ";
    if ($t != "") echo "(exemple : <I>" . $t . "</I>)";
  }
  else
  {
    echo "Tables prefix ";
    if ($t != "") echo "(example : <I>" . $t . "</I>)";
  }
  echo "</TD>";
  echo "<TD width='' class='row1'>";
  echo "<FONT size='2'>";
  echo "<input type='text' name='extern_prefix' value='" . $extern_prefix . "' size='15' class='post' />";
  echo "</TD>";
  echo "</TR>";

  empty_table_row();

  echo "<TR>";
  echo "<TD width='' class='row2'>";
  echo "<FONT size='2'>";
  if ($lang == "FR")
    echo "Nom d'hôte du serveur MySQL ";
  else
    echo "MySQL host ";
  echo "</TD>";
  echo "<TD width='' class='row1'>";
  echo "<FONT size='2'>";
  echo "<input type='text' name='extern_dbhost' value='" . $extern_dbhost . "' size='20' class='post' /> <font color='blue'>[*]";
  echo "</TD>";
  echo "</TR>";

  echo "<TR>";
  echo "<TD width='' class='row2'>";
  echo "<FONT size='2'>";
  if ($lang == "FR")
    echo "Nom de la base de données MySQL";
  else
    echo "MySQL database";
  echo "</TD>";
  echo "<TD width='' class='row1'>";
  echo "<FONT size='2'>";
  echo "<input type='text' name='extern_database' value='" . $extern_database . "' size='20' class='post' /> <font color='blue'>[*]";
  echo "</TD>";
  echo "</TR>";

  echo "<TR>";
  echo "<TD width='' class='row2'>";
  echo "<FONT size='2'>";
  if ($lang == "FR")
    echo "Nom d'utilisateur MySQL";
  else
    echo "MySQL username";
  echo "</TD>";
  echo "<TD width='' class='row1'>";
  echo "<FONT size='2'>";
  echo "<input type='text' name='extern_dbuname' value='" . $extern_dbuname . "' size='20' class='post' /> <font color='blue'>[*]";
  echo "</TD>";
  echo "</TR>";

  echo "<TR>";
  echo "<TD width='' class='row2'>";
  echo "<FONT size='2'>";
  if ($lang == "FR")
    echo "Mot de passe de l'utilisateur MySQL";
  else
    echo "MySQL password";
  echo "</TD>";
  echo "<TD width='' class='row1'>";
  echo "<FONT size='2'>";
  echo "<input type='password' name='extern_dbpass' value='" . $extern_dbpass . "' size='20' class='post' /> <font color='blue'>[*]";
  echo "</TD>";
  echo "</TR>";

  echo "<TR>";
  echo "<TD width='' class='row2'>";
  echo "<FONT size='2'>";
  if ($lang == "FR")
    echo "Confirmation du mot de passe";
  else
    echo "MySQL password confirm";
  echo "</TD>";
  echo "<TD width='' class='row1'>";
  echo "<FONT size='2'>";
  echo "<input type='password' name='extern_dbpass2' value='" . $extern_dbpass . "' size='20' class='post' /> <font color='blue'>[*]";
  echo "</TD>";
  echo "</TR>";

  //
  echo "<TR>";
    echo "<TD align='center' COLSPAN='5' class='catBottom'>";
    echo "<font face=verdana size=2><font color='blue'>[*]</FONT> ";
    echo $l_admin_options_auth_if_not_same . "</font>";
    echo "</TD>";
  echo "</TR>";
  //
  //
  //
  if ($external_authentication == "activecollab")
  {
    empty_table_row();
    echo "<TR>";
    echo "<TD width='' class='row2'>";
    echo "<FONT size='2'>";
    if ($lang == "FR")
      echo "LICENSE_KEY : Numéro de licence (du fichier license.php)";
    else
      echo "LICENSE_KEY : Licence number (from file license.php)";
    echo "</TD>";
    echo "<TD width='' class='row1'>";
    echo "<FONT size='2'>";
    echo "<input type='text' name='LICENSE_KEY' value='" . LICENSE_KEY . "' size='20' class='post' />";
    echo "</TD>";
    echo "</TR>";
  }
  else
    echo "<INPUT TYPE='hidden' name='LICENSE_KEY' value = '' />";
  //
  if ($external_authentication == "concrete")
  {
    empty_table_row();
    echo "<TR>";
    echo "<TD width='' class='row2'>";
    echo "<FONT size='2'>";
    if ($lang == "FR")
      echo "PASSWORD_SALT : voir fichier concrete/config/site.php";
    else
      echo "PASSWORD_SALT : see the file concrete/config/site.php";
    echo "</TD>";
    echo "<TD width='' class='row1'>";
    echo "<FONT size='2'>";
    echo "<input type='text' name='PASSWORD_SALT' value='" . PASSWORD_SALT . "' size='20' class='post' />";
    echo "</TD>";
    echo "</TR>";
  }
  else
    echo "<INPUT TYPE='hidden' name='PASSWORD_SALT' value = '" . PASSWORD_SALT . "' />";
  //
  if ($external_authentication == "dotclear2")
  {
    empty_table_row();
    echo "<TR>";
    echo "<TD width='' class='row2'>";
    echo "<FONT size='2'>";
    if ($lang == "FR")
      echo "DC_MASTER_KEY : voir fichier dotclear/inc/config.php";
    else
      echo "DC_MASTER_KEY : see the file dotclear/inc/config.php";
    echo "</TD>";
    echo "<TD width='' class='row1'>";
    echo "<FONT size='2'>";
    echo "<input type='text' name='DC_MASTER_KEY' value='" . DC_MASTER_KEY . "' size='20' class='post' />";
    echo "</TD>";
    echo "</TR>";
  }
  else
    echo "<INPUT TYPE='hidden' name='DC_MASTER_KEY' value = '" . DC_MASTER_KEY . "' />";
  //
  if ($external_authentication == "impresscms")
  {
    empty_table_row();
    echo "<TR>";
    echo "<TD width='' class='row2'>";
    echo "<FONT size='2'>";
    echo "SDATA_DB_SALT";
    echo "</TD>";
    echo "<TD width='' class='row1'>";
    echo "<FONT size='2'>";
    echo "<input type='text' name='SDATA_DB_SALT' value='" . SDATA_DB_SALT . "' size='20' class='post' />";
    echo "</TD>";
    echo "</TR>";
  }
  else
    echo "<INPUT TYPE='hidden' name='SDATA_DB_SALT' value = '" . SDATA_DB_SALT . "' />";
  //
  if ($external_authentication == "prestashop")
  {
    empty_table_row();
    echo "<TR>";
    echo "<TD width='' class='row2'>";
    echo "<FONT size='2'>";
    if ($lang == "FR")
      echo "_COOKIE_KEY_ : voir fichier prestashop/config/settings.inc.php";
    else
      echo "_COOKIE_KEY_ : see the file prestashop/config/settings.inc.php";
    echo "</TD>";
    echo "<TD width='' class='row1'>";
    echo "<FONT size='2'>";
    echo "<input type='text' name='_COOKIE_KEY_' value='" . _COOKIE_KEY_ . "' size='20' class='post' />";
    echo "</TD>";
    echo "</TR>";
  }
  else
    echo "<INPUT TYPE='hidden' name='_COOKIE_KEY_' value = '" . _COOKIE_KEY_ . "' />";
  //
  if ($external_authentication == "oxwall")
  {
    empty_table_row();
    echo "<TR>";
    echo "<TD width='' class='row2'>";
    echo "<FONT size='2'>";
    if ($lang == "FR")
      echo "OW_PASSWORD_SALT : voir fichier oxwall/ow_includes/config.php";
    else
      echo "OW_PASSWORD_SALT : see the file oxwall/ow_includes/config.php";
    echo "</TD>";
    echo "<TD width='' class='row1'>";
    echo "<FONT size='2'>";
    echo "<input type='text' name='OW_PASSWORD_SALT' value='" . OW_PASSWORD_SALT . "' size='20' class='post' />";
    echo "</TD>";
    echo "</TR>";
  }
  else
    echo "<INPUT TYPE='hidden' name='OW_PASSWORD_SALT' value = '" . OW_PASSWORD_SALT . "' />";
  //
  if ($external_authentication == "typolight")
  {
    empty_table_row();
    echo "<TR>";
      echo "<TD align='LEFT' class='row3'>";
      echo "</TD>";
      echo "<TD class='row1' align='left'>";
        echo "<font face='verdana' size='2'>";
        echo "<INPUT name='typolight' TYPE='radio' VALUE='1' class='genmed' ";
        if ( ($do_not_use_users == '') and ($do_not_use_members == '') ) echo "checked";
        echo "> All users";
        echo "<BR/>";
        echo "<INPUT name='typolight' TYPE='radio' VALUE='2' class='genmed' ";
        if ( ($do_not_use_users != '') and ($do_not_use_members == '') ) echo "checked";
        echo "> Only members";
        echo "<BR/>";
        echo "<INPUT name='typolight' TYPE='radio' VALUE='3' class='genmed' ";
        if ( ($do_not_use_users == '') and ($do_not_use_members != '') ) echo "checked";
        echo "> Only users";
        echo "<BR/>";
      echo "</TD>";
    echo "</TR>";
  }
  else
    echo "<INPUT TYPE='hidden' name='typolight' value = '' />";
  //
  if ($external_authentication == "triade")
  {
    empty_table_row();
    echo "<TR>";
      echo "<TD align='LEFT' class='row2'>";
        echo "<font face='verdana' size='2'>";
        echo "Accès à IntraMessenger";
      echo "</TD>";
      echo "<TD class='row1' align='left'>";
        echo "<font face='verdana' size='2'>";
        echo "<INPUT name='triade' TYPE='radio' VALUE='1' class='genmed' ";
        if ( ($do_not_use_student == '') and ($do_not_use_school_members == '') ) echo "checked";
        echo "> Tout le monde";
        echo "<BR/>";
        echo "<INPUT name='triade' TYPE='radio' VALUE='2' class='genmed' ";
        if ( ($do_not_use_student != '') and ($do_not_use_school_members == '') ) echo "checked";
        echo "> Seulement le personnel scolaire";
        echo "<BR/>";
        echo "<INPUT name='triade' TYPE='radio' VALUE='3' class='genmed' ";
        if ( ($do_not_use_student == '') and ($do_not_use_school_members != '') ) echo "checked";
        echo "> Seulement les élèves";
        echo "<BR/>";
      echo "</TD>";
    echo "</TR>";
    //
    echo "<TR>";
    echo "<TD width='' class='row2'>";
    echo "<FONT size='2'>";
    if ($lang == "FR")
      echo "Phenix dans Triade";
    else
      echo "Phenix in Triade";
    echo "</TD>";
    echo "<TD width='' class='row1'>";
      echo "<input type='checkbox' name='phenix_include_in_triade' ";
      if ($phenix_include_in_triade != "") echo "checked "; // echo "checked='0' ";
      echo " />";
    echo "</TD>";
    echo "</TR>";
    //
    echo "<TR>";
    echo "<TD width='' class='row2'>";
    echo "<FONT size='2'>";
    if ($lang == "FR")
      echo "Préfixe tables Phenix";
    else
      echo "Phenix prefix tables";
    echo "</TD>";
    echo "<TD width='' class='row1'>";
    echo "<FONT size='2'>";
    echo "<input type='text' name='phenix_table_prefix' value='" . $phenix_table_prefix . "' size='20' class='post' />";
    echo "</TD>";
    echo "</TR>";
  }
  else
  {
    echo "<INPUT TYPE='hidden' name='triade' value = '' />";
    echo "<INPUT TYPE='hidden' name='phenix_include_in_triade' value = '" . $phenix_include_in_triade . "' />";
    echo "<INPUT TYPE='hidden' name='phenix_table_prefix' value = '" . $phenix_table_prefix . "' />";
  }
  //
  
  
echo "</TABLE>";



//
//
echo "<P id='save'/>";
//echo "<BR/>";
echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_options_bt_update . "' class='mainoption' />";
echo "</FORM>";

echo "<BR/>";
echo "<BR/>";
echo "<BR/>";
//echo "<A HREF='list_options_auth_test.php'>Test !</A>";
echo "<FORM METHOD='POST' ACTION='list_options_auth_test.php?'>";
echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
echo "<INPUT TYPE='submit' VALUE = 'Test !' class='liteoption' />";
echo "</FORM>";
//
if ($lst != "")
{
  echo "<BR/>";
  echo "<BR/>";
  echo "<BR/>";
  echo "<BR/>";
  echo "<BR/>";
  echo "<BR/>";
  echo "<BR/>";
  echo "<BR/>";
  echo "</CENTER>";
  echo "<font face='verdana' size='1'>";
  echo "<B>[*]</B> <U>" . $l_admin_authentication_extern . "</U> : <BR/>";
  echo $lst . "<BR/>";
}
//
//
display_menu_footer();

echo "</body></html>";
?>