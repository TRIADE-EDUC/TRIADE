<?php 	
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2019 THeUDS           **
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
if (isset($_COOKIE['im_option_list_option_name'])) $option_show_option_name = $_COOKIE['im_option_list_option_name'];  else  $option_show_option_name = '';
//
if (isset($_GET['check'])) $check = $_GET['check']; else $check = "";
if (isset($_GET['nb_corr'])) $nb_corr = intval($_GET['nb_corr']); else $nb_corr = 0;
if (isset($_GET['list_corr'])) $list_corr = $_GET['list_corr']; else $list_corr = "";
if (isset($_REQUEST['lang'])) $lang = $_REQUEST['lang']; else $lang = "";
if (isset($_REQUEST['onglet'])) $onglet = $_REQUEST['onglet']; else $onglet = "";
//
if (!is_writeable("../common/config/config.inc.php"))
{
  header("location:check.php?lang=" . $lang . "&");
  die();
}
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_options);
require ("../common/menu.inc.php"); // après config.inc.php !
require ("../common/check_version.inc.php");
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_options_title . "</title>";
display_header();
echo "<link href='../common/styles/onglets.css' rel='stylesheet' media='screen, print' type='text/css'/>";
//echo '<META http-equiv="refresh" content="400;url="> ';
?>

<script type="text/javascript">
function show_only(tabonglet, onglet)
{
  document.getElementById('tab_1').style.display="none";
  document.getElementById('tab_2').style.display="none";
  document.getElementById('tab_3').style.display="none";
  document.getElementById('tab_4').style.display="none";
  document.getElementById('tab_5').style.display="none";
  document.getElementById('tab_6').style.display="none";
  document.getElementById('tab_7').style.display="none";
  document.getElementById('tab_8').style.display="none";
  document.getElementById('tab_9').style.display="none";
  document.getElementById('onglet_1').className="onglet";
  document.getElementById('onglet_2').className="onglet";
  document.getElementById('onglet_3').className="onglet";
  document.getElementById('onglet_4').className="onglet";
  document.getElementById('onglet_5').className="onglet";
  document.getElementById('onglet_6').className="onglet";
  document.getElementById('onglet_7').className="onglet";
  document.getElementById('onglet_8').className="onglet";
  document.getElementById('onglet_9').className="onglet";
  //
  document.getElementById(tabonglet).style.display="block";
  document.getElementById(onglet).className="onglet-actif";
  //document.getElementById(onglet).style.display=document.getElementById(onglet).style.display=="none"?"block":"none";
  //
} 

function show_only_bis(tabonglet, ztitle)
{
  document.getElementById('sharefile_a').style.display="none";
  document.getElementById('sharefile_b').style.display="none";
  document.getElementById('sharefile_c').style.display="none";
  document.getElementById('sharefile_d').style.display="none";
  document.getElementById('sharefile_title_a').style.display="block";
  document.getElementById('sharefile_title_b').style.display="block";
  document.getElementById('sharefile_title_c').style.display="block";
  document.getElementById('sharefile_title_d').style.display="block";
  //
  document.getElementById(tabonglet).style.display="block";
  document.getElementById(ztitle).style.display="none";
} 

<?php
echo '</script>';
echo "</head>";
//
switch ($onglet)
{
  case "2" :
    echo "<body onLoad=\"show_only('tab_2', 'onglet_2');\">";
    break;
  case "3" :
    echo "<body onLoad=\"show_only('tab_3', 'onglet_3');\">";
    break;
  case "4" :
    echo "<body onLoad=\"show_only('tab_4', 'onglet_4');\">";
    break;
  case "5" :
    echo "<body onLoad=\"show_only('tab_5', 'onglet_5');\">";
    break;
  case "6" :
    echo "<body onLoad=\"show_only('tab_6', 'onglet_6');\">";
    break;
  case "7" :
    echo "<body onLoad=\"show_only('tab_7', 'onglet_7');\">";
    break;
  case "8" :
    echo "<body onLoad=\"show_only('tab_8', 'onglet_8');\">";
    break;
  case "9" :
    echo "<body onLoad=\"show_only('tab_9', 'onglet_9');\">";
    break;
  default :
    echo "<body onLoad=\"show_only('tab_1', 'onglet_1');\">";
    break; 
  }
//
if ($check == "") display_menu();
//
$si_not_ok = "OK";
//
$repertoire  = getcwd() . "/"; 
$demo_acp = "";
if ( (substr_count($repertoire, "/admin_demo/") > 0) or (substr_count($repertoire, "\admin_demo/") > 0) ) $demo_acp = "X";
//
function aff_conf_readonly()
{
  GLOBAL $l_admin_options_conf_file, $l_admin_check_not_writeable;
  //
  echo "<div class='warning'><font color='red'>" . $l_admin_options_conf_file . " <I>/common/config/config.inc.php</I> : " . $l_admin_check_not_writeable . " !</font></div>";
}
//
$g_link_doc = "";
if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
{
  $file_doc = "../doc/fr/liste_options.html";
  if (is_readable($file_doc)) 
    $g_link_doc = $file_doc;
  else
    $g_link_doc = "http://www.intramessenger.net/doc/liste_options.html";
}
else
{
  $file_doc = "../doc/en/options_list.html";
  if (is_readable($file_doc)) 
    $g_link_doc = $file_doc;
  else
    $g_link_doc = "http://www.intramessenger.net/doc/options_list.html";
}
//
//Afficher une ligne de séparation
function display_separe()
{
  GLOBAL $check;
  //
  if ($check != "update")
  {
    echo "<TR>";
    echo "<TD COLSPAN='4' class='row3'>";
    echo "</TD>";
    echo "</TR>";
  }
}
//
// Afficher une ligne d'option.
function display_row($var1, $var2, $max_size, $comment, $lan, $wan, $enabled)  
{
	GLOBAL $si_not_ok, $check, $lang, $option_show_option_name, $demo_acp, $g_link_doc;
	GLOBAL $l_admin_options_legende_not_empty, $l_admin_options_legende_empty, $l_admin_options_legende_up2u, $l_admin_options_title_2;
	GLOBAL $l_admin_options_info_8, $l_admin_options_info_book, $l_admin_options_new, $l_admin_options_enable_options;
	GLOBAL $l_admin_options_status_reasons_separated;
  //
	$var1 = trim($var1);
	$nm_option = $var2;
	$info_is_on = $l_admin_options_legende_not_empty; // "On : " . 
	$info_is_off = $l_admin_options_legende_empty; // "Off : " . 
	$info_should_be_on = $l_admin_options_title_2 . " : " . $l_admin_options_legende_not_empty; // "Should be activated";
	$info_should_be_off = $l_admin_options_title_2 . " : " . $l_admin_options_legende_empty; // "Should not be activated";
	$info_should_be_up2u = $l_admin_options_legende_up2u; // "Should be... up to you...";
  //
  if ($check == "update")
       echo "<input type='hidden' name='T" . $var2 . "' value='" . $var1 . "' />";
  else
  {
    // On inverse (d'ou l'utilité de $nm_option !!!)
    if ($option_show_option_name != "")
    {
      $t_comment = $comment;
      $comment = $var2;
      $var2 = $t_comment;
      //$var2 = htmlspecialchars($t_comment);
      //$var2 = str_replace("'", '"', $var2);
    }
    //
    echo "<TR>";
    //
    echo "<TD class='row2' align='right'>";
      switch ($nm_option)
      {
        case "_LANG" :
          echo " <select name='T" . $nm_option . "'> ";
            echo "<option value='EN' class='genmed' ";
            if (_LANG == "EN") echo "SELECTED";
            echo ">EN</option>" ;
            //
            echo "<option value='FR' class='genmed' ";
            if (_LANG == "FR") echo "SELECTED";
            echo ">FR</option>" ;
            //
            echo "<option value='IT' class='genmed' ";
            if (_LANG == "IT") echo "SELECTED";
            echo ">IT</option>" ;
            //
            echo "<option value='ES' class='genmed' ";
            if (_LANG == "ES") echo "SELECTED";
            echo ">ES</option>" ;
            //
            echo "<option value='PT' class='genmed' ";
            if (_LANG == "PT") echo "SELECTED";
            echo ">PT</option>" ;
            //
            echo "<option value='BR' class='genmed' ";
            if (_LANG == "BR") echo "SELECTED";
            echo ">BR</option>" ;
            //
            echo "<option value='RO' class='genmed' ";
            if (_LANG == "RO") echo "SELECTED";
            echo ">RO</option>" ;
            //
            echo "<option value='DE' class='genmed' ";
            if (_LANG == "DE") echo "SELECTED";
            echo ">DE</option>" ;
            //
            echo "<option value='NL' class='genmed' ";
            if (_LANG == "NL") echo "SELECTED";
            echo ">NL</option>" ;
            //
          echo " </select> ";
          break;
        //
        case "_EXTERNAL_AUTHENTICATION" :
          $extern_auth_list = array();
          $extern_auth_list = f_extern_auth_list();
          echo " <select name='T" . $nm_option . "'> ";
            echo "<option value='' class='genmed'></option>";
            foreach ($extern_auth_list as $name) 
            {
              echo "<option value='" . $name . "' class='genmed' ";
              if (_EXTERNAL_AUTHENTICATION == $name) echo "SELECTED";
              echo ">" . $name . "</option>" ;
            }
            echo " </select> ";
          break;
        //
        default : 
          if (intval($max_size) > 0)
          {
            if ( ($demo_acp != "") and 
              ( ($nm_option == "_SHARE_FILES_FTP_PASSWORD_CRYPT") or ($nm_option == "_SHARE_FILES_FTP_PASSWORD") or ($nm_option == "_BACKUP_FILES_FTP_PASSWORD_CRYPT") or ($nm_option == "_BACKUP_FILES_FTP_PASSWORD") ) ) $var1 = "Not in demo version";
            //
            if ($enabled == "") // désactivée /*-  (si grisée, la valeur de l'option n'est plus transmise)
              echo "<input name='T" . $nm_option . "_bis' value='' ";
            else
              echo "<input name='T" . $nm_option . "' maxlength='" . $max_size . "' value='" . $var1 . "' ";
            //
            //if ($enabled == "") echo " disabled ";
            if ($enabled == "") echo " readonly ";
            //
            if (intval($max_size) < 10) echo "size='" . $max_size . "' ";
            if ( (intval($max_size) >= 10) and (intval($max_size) < 100) ) echo "size='20' ";
            if (intval($max_size) >= 100) echo "size='37' ";
//            if ($nm_option == '_IM_ADDRESS_BOOK_PASSWORD') 
//              echo "type='password' ";
//            else
            $typ = "text";
            if ( (intval($max_size) > 0) and (intval($max_size) < 4) ) $typ = "number"; // html5
            if ( ($nm_option == "_SITE_URL_TO_SHOW") or ($nm_option == "_EXTERN_URL_TO_REGISTER") or ($nm_option == "_EXTERN_URL_FORGET_PASSWORD") or ($nm_option == "_EXTERN_URL_CHANGE_PASSWORD") ) $typ = "url";
            if ($nm_option == "_ADMIN_EMAIL") $typ = "email";
            //
            //echo "type='text' ";
            echo "type='" . $typ . "' ";
            //
            if ($enabled == "") // désactivée /*-  (si grisée, la valeur de l'option n'est plus transmise)
              echo "class='disabled' />"; // CSS, voir : GIC
            else
              echo "class='post' />";
          }
          else
          {
            echo "<input type='checkbox' name='T" . $nm_option . "' ";
            if ($var1 != "") echo "checked ";
            if ($enabled == "") echo " disabled ";
            //if ($enabled == "") echo " readonly "; ca ne marche pas
            //
            echo " class='post' />";
          }
          //
          // case à cocher ou valeur numérique :
          if ($enabled == "") // désactivée /*-  (si grisée, la valeur de l'option n'est plus transmise)
            echo "<input type='hidden' name='T" . $nm_option . "' value='" . $var1 . "' ";
          //
          break; 
      }
    echo "</TD>";
    if ($comment == '')
    {
      echo "<TD class='row2'>";
      echo " &nbsp; ";
    }
    else
    {
      $link_doc = $g_link_doc . "#" . $nm_option;
      echo "<TD align='LEFT' class='row3'>";
      //if (substr_count(" _TIME_ZONES _SHOUTBOX_PUBLIC ", $nm_option) > 0)  NON, il faut ajout : and ($nm_option != "_SHOUTBOX") !!!
      if ($link_doc <> "") echo "<A HREF='" . $link_doc . "' target='_blank'>"; 
      //if ( (substr_count(" _ALLOW_HIDDEN_STATUS  ", $nm_option) > 0) or (substr_count($nm_option, "_SHARE_FILES") > 0) )
      if ( (substr_count(" _ALLOW_HIDDEN_STATUS _ROLE_ID_DEFAULT_FOR_NEW_USER _ACP_PROTECT_BY_HTACCESS _ACP_ALLOW_MEMORY_AUTH _ALLOW_HISTORY_MESSAGES_EXPORT _SHARE_FILES_ALLOW_ACCENT  ", $nm_option . " ") > 0) or (substr_count($nm_option, "_BACKUP_FILES") > 0) )
        echo "<IMG SRC='" . _FOLDER_IMAGES . "new.gif' WIDTH='30' HEIGHT='13' BORDER='0' TITLE=\" $l_admin_options_new  : $var2 \" />";
      else
        echo "<IMG SRC='" . _FOLDER_IMAGES . "information.png' WIDTH='16' BORDER='0' HEIGHT='16' TITLE=\"" . $var2 . "\" />";
      //
      if ($link_doc <> "") echo "</A>";
      //
      echo "&nbsp;";
      if ($nm_option == "_FORCE_UPDATE_BY_SERVER") 
      {
        if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
          echo "<A HREF='../doc/fr/comment_mettre_a_jour_les_postes.html' target='_blank'>";
        else
          echo "<A HREF='../doc/en/how_to_update_clients.html' target='_blank'>";
      }
      if ($nm_option == "_SERVERS_STATUS")
      {
        if ($var1 != "")
          echo "<A HREF='list_servers_status.php?lang=" . $lang  . "'>";
        else
        {
          if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
            echo "<A HREF='http://www.intramessenger.com/forum/viewtopic.php?p=2632#p2632' target='_blank'>";
          else
            echo "<A HREF='http://www.intramessenger.com/forum/viewtopic.php?p=2730#p2730' target='_blank'>";
        }
      }
      if ($nm_option == "_SHOUTBOX")
      {
        if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
          echo "<A HREF='http://www.intramessenger.com/forum/viewtopic.php?p=2671#p2671' target='_blank'>";
        else
          echo "<A HREF='http://www.intramessenger.com/forum/viewtopic.php?p=2731#p2731' target='_blank'>";
      }
      if ($nm_option == "_SHOUTBOX_VOTE")
      {
        if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
          echo "<A HREF='http://www.intramessenger.com/forum/pictures/im_sbx_2.png' target='_blank'>";
        else
          echo "<A HREF='http://www.intramessenger.com/forum/pictures/im_sbx_2_EN.png' target='_blank'>";
      }
      if ($nm_option == "_GROUP_USER_CAN_JOIN")
      {
        if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
          echo "<A HREF='http://www.intramessenger.com/forum/viewtopic.php?p=2698#p2698' target='_blank'>";
        else
          echo "<A HREF='http://www.intramessenger.com/forum/viewtopic.php?p=2732#p2732' target='_blank'>";
      }
      if ($nm_option == "_GROUP_FOR_SBX_AND_ADMIN_MSG")
      {
        if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
          echo "<A HREF='http://www.intramessenger.com/forum/viewtopic.php?p=2684#p2684' target='_blank'>";
        else
          echo "<A HREF='http://www.intramessenger.com/forum/pictures/im_srv_grp_sbx_EN.png' target='_blank'>";
      }
      if ($nm_option == "_SHOUTBOX_NEED_APPROVAL")
      {
        echo "<A HREF='http://www.intramessenger.com/forum/pictures/im_srv_sbx_2.png' target='_blank'>";
      }
      if ($nm_option == "_IM_ADDRESS_BOOK_PASSWORD")
      {
        echo "<A HREF='http://www.intramessenger.net/list/servers/' target='_blank'>";
      }
      if ( ($nm_option == "_PUBLIC_OPTIONS_LIST") and (is_readable("../" . _PUBLIC_FOLDER . "/options.php")) )
      {
        if ($var1 != "")
          echo "<A HREF='../" . _PUBLIC_FOLDER . "/options.php' target='_blank'>";
        else
          echo "<A HREF='http://www.intramessenger.com/demo/public/options.php' target='_blank'>";
      }
      if ( ($nm_option == "_PUBLIC_POST_AVATAR") and (is_readable("../" . _PUBLIC_FOLDER . "/avatar.php")) )
      {
        if ($var1 != "")
          echo "<A HREF='../" . _PUBLIC_FOLDER . "/avatar.php' target='_blank'>";
        else
          echo "<A HREF='http://www.intramessenger.com/demo/public/avatar.php' target='_blank'>";
      }
      if ( ($nm_option == "_EXTERN_URL_TO_REGISTER") or ($nm_option == "_EXTERN_URL_FORGET_PASSWORD") )
      {
        if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
          echo "<A HREF='http://www.intramessenger.com/forum/pictures/im_startup_extern_fr.png' target='_blank'>";
        else
          echo "<A HREF='http://www.intramessenger.com/forum/pictures/im_startup_extern_en.png' target='_blank'>";
      }
      if ( ($nm_option == "_EXTERNAL_AUTHENTICATION") and ($comment == "_EXTERNAL_AUTHENTICATION") )
      {
        if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
          echo "<A HREF='http://www.intramessenger.net/exter_auth.php?lang=FR&' target='_blank'>";
        else
          echo "<A HREF='http://www.intramessenger.net/exter_auth.php?lang=EN&' target='_blank'>";
      }
      if ($nm_option == "_PUBLIC_USERS_LIST")
      {
          echo "<A HREF='http://www.intramessenger.com/forum/pictures/im_public_user_list_en.png' target='_blank'>";
      }
      if ($nm_option == "_SITE_URL_TO_SHOW")
      {
          echo "<A HREF='http://www.intramessenger.com/forum/pictures/im_splashscreen.png' target='_blank'>";
      }
      if ($nm_option == "_SITE_TITLE_TO_SHOW")
      {
          echo "<A HREF='http://www.intramessenger.com/forum/pictures/im_splashscreen.png' target='_blank'>";
      }
      if ( ($nm_option == "_SHOUTBOX_PUBLIC") and (is_readable("../" . _PUBLIC_FOLDER . "/shoutbox_sticker.php")) )
      {
        if ($var1 != "")
          echo "<A HREF='../" . _PUBLIC_FOLDER . "/shoutbox_sticker.php' target='_blank'>";
        //else
          //echo "<A HREF='http://www.intramessenger.com/demo/public/shoutbox_sticker.php' target='_blank'>";
      }
      if ( ($nm_option == "_BOOKMARKS_PUBLIC") and (is_readable("../" . _PUBLIC_FOLDER . "/bookmarks.php")) )
      {
        if ($var1 != "")
          echo "<A HREF='../" . _PUBLIC_FOLDER . "/bookmarks.php' target='_blank'>";
        else
          echo "<A HREF='http://www.intramessenger.com/demo/public/bookmarks.php' target='_blank'>";
      }
      if ( ($nm_option == "_BOOKMARKS") or ($nm_option == "_BOOKMARKS_NEED_APPROVAL") )
      {
        if ($var1 != "")
          echo "<A HREF='list_bookmarks.php?lang=" . $lang  . "'>";
        else
        {
          if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
            echo "<A HREF='http://www.intramessenger.com/forum/viewtopic.php?f=12&t=846&' target='_blank'>";
          else
            echo "<A HREF='http://www.intramessenger.com/forum/pictures/im_bookmarks.png' target='_blank'>";
        }
      }
      if ($nm_option == "_ROLES_TO_OVERRIDE_PERMISSIONS")
      {
        if ($var1 != "")
          echo "<A HREF='list_roles.php?lang=" . $lang  . "'>";
        else
        {
          if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
            echo "<A HREF='http://www.intramessenger.com/forum/viewtopic.php?t=707&p=3287#p3287' target='_blank'>";
          else
            echo "<A HREF='http://www.intramessenger.com/forum/viewtopic.php?t=868&p=3288#p3288' target='_blank'>";
        }
      }
      if ($nm_option == "_ALLOW_SKIN")
      {
        if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
            echo "<A HREF='http://www.intramessenger.com/forum/viewtopic.php?f=12&t=968&' target='_blank'>";
          else
            echo "<A HREF='http://www.intramessenger.com/forum/viewtopic.php?f=12&t=969&' target='_blank'>";
      }
      if ($nm_option == "_AWAY_REASONS_LIST")
      {
          echo "<A HREF='http://www.intramessenger.com/forum/pictures/im_statereasondoublemenu_2.png' target='_blank'>";
      }
      if ($nm_option == "ZZZZZZZZZZZZZZZZZZZZ")
      {
        if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
          echo "<A HREF='xxxxxxxxxxxxxxxxxxxxx' target='_blank'>";
        else
          echo "<A HREF='xxxxxxxxxxxxxxxxxxxxx' target='_blank'>";
      }
      if ($nm_option == "ZZZZZZZZZZZZZZZZZZZZ")
      {
        if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
          echo "<A HREF='xxxxxxxxxxxxxxxxxxxxx' target='_blank'>";
        else
          echo "<A HREF='xxxxxxxxxxxxxxxxxxxxx' target='_blank'>";
      }
      if ($nm_option == "ZZZZZZZZZZZZZZZZZZZZ")
      {
        if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
          echo "<A HREF='xxxxxxxxxxxxxxxxxxxxx' target='_blank'>";
        else
          echo "<A HREF='xxxxxxxxxxxxxxxxxxxxx' target='_blank'>";
      }
      if ($nm_option == "ZZZZZZZZZZZZZZZZZZZZ")
      {
        if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
          echo "<A HREF='xxxxxxxxxxxxxxxxxxxxx' target='_blank'>";
        else
          echo "<A HREF='xxxxxxxxxxxxxxxxxxxxx' target='_blank'>";
      }
      //
      //
      if ( ($nm_option == "_MAINTENANCE_MODE") and (_MAINTENANCE_MODE != "") ) echo "<span style='BACKGROUND-COLOR: #ff4400; COLOR:#FFFFFF;'>";
      
      // font-size:0.8em;
      echo "<font face='verdana' size='2'>" . $comment . "</font>";
      echo "</A>";
      //
      //
      //
      if ($nm_option == "_SHARE_FILES_FTP_PASSWORD")
      {
        echo " &nbsp; <A HREF='files_sharing_ftp_test.php?lang=" . $lang . "&'>";
        if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
          echo "Tester</A>";
        else
          echo "Test</A>";
      }
      //
      if ($nm_option == "_BACKUP_FILES_FTP_PASSWORD")
      {
        echo " &nbsp; <A HREF='files_backup_ftp_test.php?lang=" . $lang . "&'>";
        if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
          echo "Tester</A>";
        else
          echo "Test</A>";
      }
      //
      if ( ($nm_option == "_ONLINE_REASONS_LIST") or ($nm_option == "_AWAY_REASONS_LIST") or ($nm_option == "_BUSY_REASONS_LIST") or ($nm_option == "_DONOTDISTURB_REASONS_LIST") )
      {
        echo "&nbsp; <font face='verdana' size='1' color='gray'>" . $l_admin_options_status_reasons_separated . "</font>";
      }
      //
      // Options principales (en activent d'autres) :
      if (substr_count(" _SHARE_FILES _SHARE_FILES_EXCHANGE _SHARE_FILES_COMPRESS _BACKUP_FILES _BOOKMARKS _SHOUTBOX _SHOUTBOX_NEED_APPROVAL _SHOUTBOX_VOTE _USER_NEED_PASSWORD _EXTERNAL_AUTHENTICATION _ROLES_TO_OVERRIDE_PERMISSIONS _ALLOW_MANAGE_CONTACT_LIST _ALLOW_MANAGE_OPTIONS _ALLOW_MANAGE_PROFILE _ALLOW_AUTO_ADD_NEW_USER_ON_SERVER ", $nm_option) > 0) 
        echo " <IMG SRC='" . _FOLDER_IMAGES . "state_on.png' style='vertical-align: middle;' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_options_enable_options . "' TITLE='" . $l_admin_options_enable_options . "'> ";
      //
      //
      $bad_config_to_public_book = "";
      if ( ($nm_option == "_IM_ADDRESS_BOOK_PASSWORD") and ($var1 == "") ) $bad_config_to_public_book = "X";
      if ( ($nm_option == "_ENTERPRISE_SERVER") and ($var1 != "") ) $bad_config_to_public_book = "X";
      if ( ($nm_option == "_PASSWORD_FOR_PRIVATE_SERVER") and ($var1 != "") ) $bad_config_to_public_book = "X";
      if ( ($nm_option == "_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER") and ($var1 == "") ) $bad_config_to_public_book = "X";
      if ( ($nm_option == "_PENDING_USER_ON_COMPUTER_CHANGE") and ($var1 != "") ) $bad_config_to_public_book = "X";
      if ( ($nm_option == "_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN") and ($var1 != "") ) $bad_config_to_public_book = "X";
      if ( ($nm_option == "_FORCE_USERNAME_TO_PC_SESSION_NAME") and ($var1 != "") ) $bad_config_to_public_book = "X";
      if ( ($nm_option == "_FORCE_UPDATE_BY_SERVER") and ($var1 != "") ) $bad_config_to_public_book = "X";
      if ( ($nm_option == "_FORCE_UPDATE_BY_INTERNET") and ($var1 == "") ) $bad_config_to_public_book = "X";
      if ( ($nm_option == "_USER_NEED_PASSWORD") and ($var1 == "") ) $bad_config_to_public_book = "X";
      if ( ($nm_option == "_HISTORY_MESSAGES_ON_ACP") and ($var1 != "") ) $bad_config_to_public_book = "X";
      if ( ($nm_option == "_CHECK_VERSION_INTERNET") and ($var1 == "") ) $bad_config_to_public_book = "X";
      if ( ($nm_option == "_MAX_NB_USER") and (intval($var1) < 100) and (intval($var1) > 0) ) $bad_config_to_public_book = "X";
      if ( ($nm_option == "_MAX_NB_SESSION") and (intval($var1) < 50) and (intval($var1) > 0) ) $bad_config_to_public_book = "X";
      if ($bad_config_to_public_book != "") 
        echo " <A HREF='register_to_public_servers_list.php?lang=" . $lang . "&'><IMG SRC='" . _FOLDER_IMAGES . "annu_config_error.png' WIDTH='16' HEIGHT='16' BORDER='0' style='vertical-align: middle;' ALT='" . $l_admin_options_info_8 . " " . $l_admin_options_info_book . "' TITLE='" . $l_admin_options_info_8 . " " . $l_admin_options_info_book . "'></A>"; 
    }
    echo "</TD>";
    //
    if ( ($wan == "+") and ($lan == "+") ) // obligatoire
    {
      echo "<TD align='CENTER' class='row3' colspan='2'>";
      echo "&nbsp;";
    }
    else
    {
      echo "<TD align='CENTER' class='row2'>";
      if (intval($lan) > 0)
        echo "<font face='verdana' size='2'>" . $lan . "</font>";
      else
      {
        if ($lan == "X") echo "<IMG SRC='" . _FOLDER_IMAGES . "thumb_up.png' WIDTH='16' HEIGHT='16' ALT='" . $info_should_be_on . "' TITLE='" . $info_should_be_on . "'>";
        if ($lan == "-") echo "<IMG SRC='" . _FOLDER_IMAGES . "thumb_down.png' WIDTH='16' HEIGHT='16' ALT='" . $info_should_be_off . "' TITLE='" . $info_should_be_off . "'>";
        if ($lan == "")  echo "&nbsp;"; //"<IMG SRC='" . _FOLDER_IMAGES . "bt_yellow.gif' WIDTH='18' HEIGHT='18' ALT='" . $info_should_be_up2u . "' TITLE='" . $info_should_be_up2u . "'>";
      }
      //
      echo "<TD align='CENTER' class='row2'>";
      if (intval($wan) > 0)
        echo "<font face='verdana' size='2'>" . $wan . "</font>";
      else
      {
        if ($wan == "X") echo "<IMG SRC='" . _FOLDER_IMAGES . "thumb_up.png' WIDTH='16' HEIGHT='16' ALT='" . $info_should_be_on . "' TITLE='" . $info_should_be_on . "'>";
        if ($wan == "-") echo "<IMG SRC='" . _FOLDER_IMAGES . "thumb_down.png' WIDTH='16' HEIGHT='16' ALT='" . $info_should_be_off . "' TITLE='" . $info_should_be_off . "'>";
        if ($wan == "")  echo "&nbsp;"; //"<IMG SRC='" . _FOLDER_IMAGES . "bt_yellow.gif' WIDTH='18' HEIGHT='18' ALT='" . $info_should_be_up2u . "' TITLE='" . $info_should_be_up2u . "'>";
      }
    }
    echo "</TD>";
    //
    echo "</TR>";
  }
	echo "\n";
}

//
//
echo "<font face='verdana' size='2'>";
//  
echo "<FORM METHOD='POST' ACTION='list_options_update.php?'>";
echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
echo "<INPUT TYPE='hidden' name='check' value = '" . $check . "' />";
//echo "<INPUT TYPE='hidden' name='T_FORCE_STATUS_LIST_FROM_SERVER' value = '" . _FORCE_STATUS_LIST_FROM_SERVER . "' />";
echo "<INPUT TYPE='hidden' name='T_SEND_ADMIN_ALERT' value = '" . _SEND_ADMIN_ALERT . "' />";
//echo "<INPUT TYPE='hidden' name='T_IM_ADDRESS_BOOK_PASSWORD' value = '" . _IM_ADDRESS_BOOK_PASSWORD . "' />";
echo "<INPUT TYPE='hidden' name='T_PASSWORD_FOR_PRIVATE_SERVER' value = '" . _PASSWORD_FOR_PRIVATE_SERVER . "' />";
echo "<INPUT TYPE='hidden' name='T_ALLOW_COL_FUNCTION_NAME' value = '" . _ALLOW_COL_FUNCTION_NAME . "' />";
echo "<INPUT TYPE='hidden' name='T_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL' value = '" . _STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL . "' />";
echo "<INPUT TYPE='hidden' name='T_PUBLIC_FOLDER' value = '" . _PUBLIC_FOLDER . "' />";
//echo "<INPUT TYPE='hidden' name='T_LOCK_AFTER_NO_ACTIVITY_DURATION' value = '" . _LOCK_AFTER_NO_ACTIVITY_DURATION . "' />";
echo "<INPUT TYPE='hidden' name='T_LOCK_AFTER_NO_CONTACT_DURATION' value = '" . _LOCK_AFTER_NO_CONTACT_DURATION . "' />";
echo "<INPUT TYPE='hidden' name='T_FORCE_LAUNCH_ON_STARTUP' value = '" . _FORCE_LAUNCH_ON_STARTUP . "' />";
echo "<INPUT TYPE='hidden' name='T_SKIN_FORCED_COLOR_CUSTOM_VERSION' value = '" . _SKIN_FORCED_COLOR_CUSTOM_VERSION . "' />";
echo "<INPUT TYPE='hidden' name='T_STATISTICS' value = '" . _STATISTICS . "' />";
echo "<INPUT TYPE='hidden' name='T_AUTO_ADD_CONTACT_USER_ID' value = '" . _AUTO_ADD_CONTACT_USER_ID . "' />";
//echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_EXCHANGE_UNREAD_VALIDITY' value = '" . _SHARE_FILES_EXCHANGE_UNREAD_VALIDITY . "' />";
//echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_SCREENSHOT' value = '" . _SHARE_FILES_SCREENSHOT . "' />";
//echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_EXCHANGE_SCREENSHOT' value = '" . _SHARE_FILES_EXCHANGE_SCREENSHOT . "' />";
//echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_WEBCAM' value = '" . _SHARE_FILES_WEBCAM . "' />";
//echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_EXCHANGE_WEBCAM' value = '" . _SHARE_FILES_EXCHANGE_WEBCAM . "' />";
echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_ALLOW_UPPERCASE' value = '" . _SHARE_FILES_ALLOW_UPPERCASE . "' />";
echo "<INPUT TYPE='hidden' name='T_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL_AFTER_LOGIN' value = '" . _STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL_AFTER_LOGIN . "' />";
echo "<INPUT TYPE='hidden' name='T_ROLE_ID_DEFAULT_FOR_NEW_USER' value = '" . _ROLE_ID_DEFAULT_FOR_NEW_USER . "' />";
echo "<INPUT TYPE='hidden' name='T_ACP_PROTECT_BY_HTACCESS' value = '" . _ACP_PROTECT_BY_HTACCESS . "' />";
echo "<INPUT TYPE='hidden' name='T_ACP_ALLOW_MEMORY_AUTH' value = '" . _ACP_ALLOW_MEMORY_AUTH . "' />";
//echo "<INPUT TYPE='hidden' name='T_ALLOW_HISTORY_MESSAGES_EXPORT' value = '" . _ALLOW_HISTORY_MESSAGES_EXPORT . "' />";
echo "<INPUT TYPE='hidden' name='T_ALLOW_REDUCE_MAIN_SCREEN' value = '" . _ALLOW_REDUCE_MAIN_SCREEN . "' />";
echo "<INPUT TYPE='hidden' name='T_ALLOW_REDUCE_MESSAGE_SCREEN' value = '" . _ALLOW_REDUCE_MESSAGE_SCREEN . "' />";
echo "<INPUT TYPE='hidden' name='T_ALLOW_CLOSE_IM' value = '" . _ALLOW_CLOSE_IM . "' />";
echo "<INPUT TYPE='hidden' name='T_ALLOW_UPPERCASE_SPACE_USERNAME' value = '" . _ALLOW_UPPERCASE_SPACE_USERNAME . "' />";
echo "<INPUT TYPE='hidden' name='T_BACKUP_FILES_THIS_LOCAL_FOLDER_ONLY' value = '" . _BACKUP_FILES_THIS_LOCAL_FOLDER_ONLY . "' />";
echo "<INPUT TYPE='hidden' name='T_BACKUP_FILES_FORCE_EVERY_DAY_AT' value = '" . _BACKUP_FILES_FORCE_EVERY_DAY_AT . "' />";
echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_ALLOW_ACCENT' value = '" . _SHARE_FILES_ALLOW_ACCENT . "' />";
echo "<INPUT TYPE='hidden' name='T_GROUP_ID_DEFAULT_FOR_NEW_USER' value = '" . _GROUP_ID_DEFAULT_FOR_NEW_USER . "' />";
echo "<INPUT TYPE='hidden' name='T_FORCE_OPTION_FILE_FROM_SERVER' value = '" . _FORCE_OPTION_FILE_FROM_SERVER . "' />";
echo "<INPUT TYPE='hidden' name='T_SHOUTBOX_ALLOW_SCROLLING' value = '" . _SHOUTBOX_ALLOW_SCROLLING . "' />";
//echo "<INPUT TYPE='hidden' name='T_' value = '" . _XXXXXXXXXXXXX . "' />";
//echo "<INPUT TYPE='hidden' name='T_' value = '" . _XXXXXXXXXXXXX . "' />";
//echo "<INPUT TYPE='hidden' name='T_' value = '" . _XXXXXXXXXXXXX . "' />";
//echo "<INPUT TYPE='hidden' name='T_' value = '" . _XXXXXXXXXXXXX . "' />";


//
#$nb_corr = 4;
if ($check != "update")
{
  if (!is_writeable("../common/config/config.inc.php"))    aff_conf_readonly();
  //
  //if ($nb_corr > 0) echo "<div class='info'><B>" . $nb_corr . "</B> " . $l_admin_options_auto_corrected . "</div>";
  if ($nb_corr > 0) 
  {
    echo "<div name='optionsfixed' class='info'><B>" . $nb_corr . "</B> " . $l_admin_options_auto_corrected;
    if ($list_corr != "")
    {
      $list_corr = f_decode64_wd($list_corr);
      $list_corr = substr($list_corr, 0, (strlen($list_corr) - 1) ); // enlever le dernier #
      $list_corr = str_replace("#", " - ", $list_corr);
      echo "<BR/>" . $list_corr;
    }
    echo "</div>";
  }
  //
  echo "<div class='menu_onglet'>";
  echo "<a class='onglet' id='onglet_1' href='#' onclick=\"show_only('tab_1', 'onglet_1');\" TITLE='" . $l_admin_options_general_options . "'>" . $l_admin_options_general_options_short . "</a> &nbsp;";
  echo "<a class='onglet' id='onglet_2' href='#' onclick=\"show_only('tab_2', 'onglet_2');\" TITLE='" . $l_admin_options_user_restrictions_options . "'>" . $l_admin_options_user_restrictions_options_short . "</a> &nbsp;";
  echo "<a class='onglet' id='onglet_3' href='#' onclick=\"show_only('tab_3', 'onglet_3');\" TITLE='" . $l_admin_options_security_options . "'>" . $l_admin_options_security . "</a> &nbsp;";
  echo "<a class='onglet' id='onglet_4' href='#' onclick=\"show_only('tab_4', 'onglet_4');\" TITLE='" . $l_admin_options_shoutbox_title_long . "'>" . $l_admin_options_shoutbox_title_short . "</a> &nbsp;";
  echo "<a class='onglet' id='onglet_5' href='#' onclick=\"show_only('tab_5', 'onglet_5');\" TITLE='" . $l_admin_options_bookmarks . "'>" . $l_menu_bookmarks . "</a> &nbsp;";
  echo "<a class='onglet' id='onglet_8' href='#' onclick=\"show_only('tab_8', 'onglet_8');\" TITLE='" . $l_admin_options_share_files_title . "'>" . $l_admin_options_share_files . "</a> &nbsp;";
  echo "<a class='onglet' id='onglet_9' href='#' onclick=\"show_only('tab_9', 'onglet_9');\" TITLE='" . $l_admin_options_backup_files_title . "'>" . $l_admin_options_backup_files . "</a> &nbsp;";
  echo "<a class='onglet' id='onglet_6' href='#' onclick=\"show_only('tab_6', 'onglet_6');\" TITLE='" . $l_admin_options_special_modes . " - " . $l_admin_options_info_10 . "'>" . $l_admin_options_special_modes . "</a> &nbsp;";
  echo "<a class='onglet' id='onglet_7' href='#' onclick=\"show_only('tab_7', 'onglet_7');\" TITLE='" . $l_admin_options_other_options_options . "'>" . $l_admin_options_other_options . "</a> &nbsp;";
  echo "<BR/>";
  //echo "<div class='spacer'></div>";
  echo "</div>";

#
##
###
#######>------------------------------------------------ TAB 1 ------------------------------------------------
###
##
#
  echo "<div id='tab_1' style='display:none' ";
  //if ($javactif != '') echo "style='display:none'";
  echo ">";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='98%'>";
  echo "<TR>";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    //echo "<font face='verdana' size=3><b>" . $l_admin_options_title . "</b></font></TH>";
    echo "<font face='verdana' size=3><b>" . $l_admin_options_general_options . "</b></font></TH>";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    echo "<font face='verdana' size=3><b>" . $l_admin_options_title_2 . "</b></font></TH>";
  echo "</TR>";
  echo "<TR>";
    //display_row_table($l_admin_options_col_option, '');
    display_row_table("&nbsp;" . $l_admin_options_col_value . "&nbsp;", '');
    display_row_table($l_admin_options_col_description, '');
    //display_row_table("&nbsp;LAN&nbsp;", '');
    if ($lang == "FR")
      display_row_table("&nbsp;<acronym title='Réseau local'>LAN</acronym>&nbsp;", '40');
    else
      display_row_table("&nbsp;<acronym title='Local Area Network'>LAN</acronym>&nbsp;", '40');
    //
    display_row_table("Internet", '40');
  echo "</TR>";
}
//echo "<TR>";
//echo "<TD colspan='5' align='center' class='catHead'>";
//echo "<font face='verdana' size='2'><B>" . $l_admin_options_admin_options . " :</B></font>"; /////////////////////////////////////////////
//echo "</TD>";
//echo "</TR>";


display_row(_MAINTENANCE_MODE, "_MAINTENANCE_MODE", 0, $l_admin_options_maintenance_mode, "-", "-", "£");
display_row(_LANG, "_LANG", 2, $l_language, "", "", "£");
display_row(_MAX_NB_USER, "_MAX_NB_USER", 5, $l_admin_options_nb_max_user, "-", "-", "£");
display_row(_MAX_NB_SESSION, "_MAX_NB_SESSION", 4, $l_admin_options_nb_max_session, "-", "-", "£");
display_row(_MAX_NB_CONTACT_BY_USER, "_MAX_NB_CONTACT_BY_USER", 3, $l_admin_options_nb_max_contact_by_user, "", "", "£");
display_row(_MAX_NB_IP, "_MAX_NB_IP", 2, $l_admin_options_max_simultaneous_ip_addresses, "-", "", "£");
display_row(_OUTOFDATE_AFTER_NOT_USE_DURATION, "_OUTOFDATE_AFTER_NOT_USE_DURATION", 3, $l_admin_options_del_user_after_x_days_not_use, "80", "50", "£");
display_row(_CHECK_NEW_MSG_EVERY, "_CHECK_NEW_MSG_EVERY", 2, $l_admin_options_check_new_msg_every, "20", "30", "£");
display_row(_SLOW_NOTIFY, "_SLOW_NOTIFY", 0, $l_admin_options_full_check, "X", "X", "£");
//
display_row(_ALLOW_USE_PROXY, "_ALLOW_USE_PROXY", 0, $l_admin_options_allow_use_proxy, "", "X", "£");
display_row(_PROXY_PORT_NUMBER, "_PROXY_PORT_NUMBER", 5, $l_admin_options_proxy_port_number, "", "-", "£");
display_row(_PROXY_ADDRESS, "_PROXY_ADDRESS", 23, $l_admin_options_proxy_address, "", "-", "£");
display_row(_ALLOW_EMAIL_NOTIFIER, "_ALLOW_EMAIL_NOTIFIER", 0, $l_admin_options_allow_email_notifier, "", "X", _ALLOW_MANAGE_OPTIONS);
display_row(_INCOMING_EMAIL_SERVER_ADDRESS, "_INCOMING_EMAIL_SERVER_ADDRESS", 90, $l_admin_options_force_email_server, "", "-", "£");
display_row(_ADMIN_EMAIL, "_ADMIN_EMAIL", 200, $l_admin_options_admin_email, "X", "X", "£");
display_row(_ADMIN_PHONE, "_ADMIN_PHONE", 30, $l_admin_options_admin_phone, "X", "-", "£");
display_row(_SCROLL_TEXT, "_SCROLL_TEXT", 90, $l_admin_options_scroll_text, "", "", "£");
//
if ($option_show_option_name != "")
  display_row(_IM_ADDRESS_BOOK_PASSWORD, "_IM_ADDRESS_BOOK_PASSWORD", 90, $l_admin_options_pass_register_book . " " . $l_admin_options_info_book, "", "", "£");
else
  display_row(_IM_ADDRESS_BOOK_PASSWORD, "_IM_ADDRESS_BOOK_PASSWORD", 90, $l_admin_options_pass_register_book . " <A HREF='http://www.intramessenger.net/list/servers/' target='_blank'>" . $l_admin_options_info_book . "</A>", "", "", "£");
//
if ($check != "update")
{
  //echo "</TR>";
  echo "</table>";
  //echo "</BR>";
  echo "</div>";
}

#
##
###
#######>------------------------------------------------ TAB 2 ------------------------------------------------
###
##
#

if ($check != "update")
{
  echo "<div id='tab_2' style='display:none' ";
  //if ($javactif != '') echo "style='display:none'";
  echo ">";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='98%'>";
  echo "<TR>";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    //echo "<font face='verdana' size=3><b>" . $l_admin_options_title . "</b></font></TH>";
    echo "<font face='verdana' size=3><b>" . $l_admin_options_user_restrictions_options . "</b></font></TH>";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    echo "<font face='verdana' size=3><b>" . $l_admin_options_title_2 . "</b></font></TH>";
  echo "</TR>";
  echo "<TR>";
    //display_row_table($l_admin_options_col_option, '');
    display_row_table("&nbsp;" . $l_admin_options_col_value . "&nbsp;", '');
    display_row_table($l_admin_options_col_description, '');
    //display_row_table("&nbsp;LAN&nbsp;", '');
    if ($lang == "FR")
      display_row_table("&nbsp;<acronym title='Réseau local'>LAN</acronym>&nbsp;", '40');
    else
      display_row_table("&nbsp;<acronym title='Local Area Network'>LAN</acronym>&nbsp;", '40');
    //
    display_row_table("Internet", '40');
  echo "</TR>";
  
  //echo "<TR>";
  //echo "<TD colspan='5' align='center' class='catHead'>";
  //echo "<font face='verdana' size='2'><B>" . $l_admin_options_user_restrictions_options . " :</B></font>";  ////////////////////////////////////////
  //echo "</TD>";
  //echo "</TR>";
}
display_row(_ALLOW_CONFERENCE, "_ALLOW_CONFERENCE", 0, $l_admin_option_allow_conference, "X", "X", "£");
display_row(_ALLOW_SMILEYS, "_ALLOW_SMILEYS", 0, $l_admin_options_allow_smiley, "X", "X", "£");
display_row(_ALLOW_SEND_TO_OFFLINE_USER, "_ALLOW_SEND_TO_OFFLINE_USER", 0, $l_admin_option_send_offline, "X", "X", "£");
display_row(_ALLOW_HIDDEN_STATUS, "_ALLOW_HIDDEN_STATUS", 0, $l_admin_options_hidden_status, "X", "X", "£");
display_row(_ALLOW_HISTORY_MESSAGES, "_ALLOW_HISTORY_MESSAGES", 0, $l_admin_options_user_history_messages, "", "", "£");
display_row(_ALLOW_HISTORY_MESSAGES_EXPORT, "_ALLOW_HISTORY_MESSAGES_EXPORT", 0, $l_admin_options_user_history_messages_export, "", "", _ALLOW_HISTORY_MESSAGES);
display_row(_ALLOW_POST_IT, "_ALLOW_POST_IT", 0, $l_admin_options_allow_postit, "", "", "£");
//
display_separe();
display_row(_ALLOW_MANAGE_CONTACT_LIST, "_ALLOW_MANAGE_CONTACT_LIST", 0, $l_admin_options_allow_change_contact_list, "X", "X", "£");
display_row(_ALLOW_HIDDEN_TO_CONTACTS, "_ALLOW_HIDDEN_TO_CONTACTS", 0, $l_admin_options_allow_invisible, "X", "X", _ALLOW_MANAGE_CONTACT_LIST);
display_row(_ALLOW_CHANGE_CONTACT_NICKNAME, "_ALLOW_CHANGE_CONTACT_NICKNAME", 0, $l_admin_options_can_change_contact_nickname, "X", "X", _ALLOW_MANAGE_CONTACT_LIST);
display_row(_ALLOW_CONTACT_RATING, "_ALLOW_CONTACT_RATING", 0, $l_admin_options_allow_rating, "-", "", _ALLOW_MANAGE_CONTACT_LIST);
//
display_separe();
display_row(_ALLOW_MANAGE_OPTIONS, "_ALLOW_MANAGE_OPTIONS", 0, $l_admin_options_allow_change_options, "X", "X", "£");
display_row(_ALLOW_SKIN, "_ALLOW_SKIN", 0, $l_admin_options_allow_skin, "X", "X", _ALLOW_MANAGE_OPTIONS);
display_row(_ALLOW_SOUND_USAGE, "_ALLOW_SOUND_USAGE", 0, $l_admin_options_allow_sound_usage, "X", "X", _ALLOW_MANAGE_OPTIONS);
display_separe();
//
display_row(_ALLOW_MANAGE_PROFILE, "_ALLOW_MANAGE_PROFILE", 0, $l_admin_options_allow_change_profile, "X", "X", "£");
display_row(_ALLOW_CHANGE_EMAIL_PHONE, "_ALLOW_CHANGE_EMAIL_PHONE", 0, $l_admin_options_allow_change_email_phone, "X", "X", _ALLOW_MANAGE_PROFILE);
display_row(_ALLOW_CHANGE_FUNCTION_NAME, "_ALLOW_CHANGE_FUNCTION_NAME", 0, $l_admin_options_allow_change_function_name, "X", "X", _ALLOW_MANAGE_PROFILE);
display_row(_ALLOW_CHANGE_AVATAR, "_ALLOW_CHANGE_AVATAR", 0, $l_admin_options_allow_change_avatar, "X", "X", _ALLOW_MANAGE_PROFILE);
//
//display_separe();
//display_row(_PUBLIC_FOLDER, "_PUBLIC_FOLDER", 30, $l_admin_options_public_folder, "X", "X", "£");
//display_row(_ALLOW_UPPERCASE_SPACE_USERNAME, "_ALLOW_UPPERCASE_SPACE_USERNAME", 0, $l_admin_options_uppercase_space_nickname, "X", "X", "£");
//display_row(_ALLOW_REDUCE_MAIN_SCREEN, "_ALLOW_REDUCE_MAIN_SCREEN", 0, $l_admin_options_allow_reduce_main_screen, "X", "X", "£");
//display_row(_ALLOW_REDUCE_MESSAGE_SCREEN, "_ALLOW_REDUCE_MESSAGE_SCREEN", 0, $l_admin_options_allow_reduce_message_screen, "X", "X", "£");
//display_row(_ALLOW_CLOSE_IM, "_ALLOW_CLOSE_IM", 0, $l_admin_options_allow_close_im, "X", "X", "£");
//
if ($check != "update")
{
  //echo "</TR>";
  echo "</table>";
  //echo "</BR>";
  echo "</div>";
}

#
##
###
#######>------------------------------------------------ TAB 3 ------------------------------------------------
###
##
#

if ($check != "update")
{
  echo "<div id='tab_3' style='display:none' ";
  //if ($javactif != '') echo "style='display:none'";
  echo ">";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='98%'>";
  echo "<TR>";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    //echo "<font face='verdana' size=3><b>" . $l_admin_options_title . "</b></font></TH>";
    echo "<font face='verdana' size=3><b>" . $l_admin_options_security_options . "</b></font></TH>";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    echo "<font face='verdana' size=3><b>" . $l_admin_options_title_2 . "</b></font></TH>";
  echo "</TR>";
  echo "<TR>";
    //display_row_table($l_admin_options_col_option, '');
    display_row_table("&nbsp;" . $l_admin_options_col_value . "&nbsp;", '');
    display_row_table($l_admin_options_col_description, '');
    //display_row_table("&nbsp;LAN&nbsp;", '');
    if ($lang == "FR")
      display_row_table("&nbsp;<acronym title='Réseau local'>LAN</acronym>&nbsp;", '40');
    else
      display_row_table("&nbsp;<acronym title='Local Area Network'>LAN</acronym>&nbsp;", '40');
    //
    display_row_table("Internet", '40');
  echo "</TR>";
  
  //echo "<TR>";
  //echo "<TD colspan='5' align='center' class='catHead'>";
  //echo "<font face='verdana' size='2'><B>" . $l_admin_options_security_options . " :</B></font>";  ////////////////////////////////////////
  //echo "</TD>";
  //echo "</TR>";
}
//
display_row(_USER_NEED_PASSWORD, "_USER_NEED_PASSWORD", 0, $l_admin_options_password_user, "X", "X", "£");
#if (_USER_NEED_PASSWORD != "")
#{
  display_row(_MINIMUM_PASSWORD_LENGTH, "_MINIMUM_PASSWORD_LENGTH", 2, $l_admin_options_minimum_length_of_password, "6", "8", "£");
  display_row(_MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER, "_MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER", 2, $l_admin_options_max_pwd_error_lock, "5", "5", _USER_NEED_PASSWORD);
  display_row(_LOCK_DURATION, "_LOCK_DURATION", 2, $l_admin_options_lock_duration, "10", "20", _USER_NEED_PASSWORD);
  display_row(_PASSWORD_VALIDITY, "_PASSWORD_VALIDITY", 3, $l_admin_options_password_validity, "180", "90", _USER_NEED_PASSWORD);
#}
#else
#{
#  echo "<INPUT TYPE='hidden' name='T_MINIMUM_PASSWORD_LENGTH' value = '" . _MINIMUM_PASSWORD_LENGTH . "' />";
#  echo "<INPUT TYPE='hidden' name='T_MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER' value = '" . _MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER . "' />";
#}
display_row(_PWD_NEED_DIGIT_LETTER, "_PWD_NEED_DIGIT_LETTER", 0, $l_admin_options_pass_need_digit_and_letter, "X", "X", _USER_NEED_PASSWORD);
display_row(_PWD_NEED_UPPER_LOWER, "_PWD_NEED_UPPER_LOWER", 0, $l_admin_options_pass_need_upper_and_lower, "", "X", _USER_NEED_PASSWORD);
display_row(_PWD_NEED_SPECIAL_CHARACTER, "_PWD_NEED_SPECIAL_CHARACTER", 0, $l_admin_options_pass_need_special_character, "", "", _USER_NEED_PASSWORD);
display_separe();
//
if (_CRYPT_MESSAGES == "") $enable_option = "£";   else   $enable_option = "";
display_row(_CRYPT_MESSAGES, "_CRYPT_MESSAGES", 0, $l_admin_options_crypt_msg, "", "X", "£");
display_row(_CENSOR_MESSAGES, "_CENSOR_MESSAGES", 0, $l_admin_options_censor_messages, "-", "-", $enable_option);
display_row(_HISTORY_MESSAGES_ON_ACP, "_HISTORY_MESSAGES_ON_ACP", 0, $l_admin_options_log_messages, "", "-", $enable_option);

/*
if (_PASSWORD_FOR_PRIVATE_SERVER != "")
  display_row("X", "_PASSWORD_FOR_PRIVATE_SERVER", -20, $l_admin_options_password_for_private_server, "-", "", "£");
else
  display_row("", "_PASSWORD_FOR_PRIVATE_SERVER", -20, $l_admin_options_password_for_private_server, "-", "", "£");
*/

if ($check != "update")
{
  echo "<TR>";
  echo "<TD colspan='5' align='center' class='catHead'>";
  echo "<font face='verdana' size='2'><B>" . $l_admin_options_special_options . " :</B></font>";
  echo "</TD>";
  echo "</TR>";
}
display_row(_FORCE_USERNAME_TO_PC_SESSION_NAME, "_FORCE_USERNAME_TO_PC_SESSION_NAME", 0, $l_admin_options_is_usernamePC, "", "-", "£");
//if (_FORCE_USERNAME_TO_PC_SESSION_NAME == "")
if (_FORCE_USERNAME_TO_PC_SESSION_NAME == "") $enable_option = "£";   else   $enable_option = "";
display_row(_MINIMUM_USERNAME_LENGTH, "_MINIMUM_USERNAME_LENGTH", 2, $l_admin_options_minimum_length_of_username, "4", "6", $enable_option);
//
if ( (_FORCE_USERNAME_TO_PC_SESSION_NAME != "") or (_USER_NEED_PASSWORD == "") ) $enable_option = "£";   else   $enable_option = "";
display_row(_PENDING_USER_ON_COMPUTER_CHANGE, "_PENDING_USER_ON_COMPUTER_CHANGE", 0, $l_admin_options_need_admin_if_chang_check, "", "-", $enable_option);
display_separe();
display_row(_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER, "_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER", 0, $l_admin_options_auto_add_user, "X", "X", "£");
display_row(_NEED_QUICK_REGISTER_TO_AUTO_ADD_NEW_USER, "_NEED_QUICK_REGISTER_TO_AUTO_ADD_NEW_USER", 0, $l_admin_options_quick_register, "-", "-", _ALLOW_AUTO_ADD_NEW_USER_ON_SERVER);
display_row(_PENDING_NEW_AUTO_ADDED_USER, "_PENDING_NEW_AUTO_ADDED_USER", 0, $l_admin_options_need_admin_after_add, "-", "-", _ALLOW_AUTO_ADD_NEW_USER_ON_SERVER);

if ($check != "update")
{
  //echo "</TR>";
  echo "</table>";
  //echo "</BR>";
  echo "</div>";
}

#
##
###
#######>------------------------------------------------ TAB 4 ------------------------------------------------
###
##
#

if ($check != "update")
{
  echo "<div id='tab_4' style='display:none' ";
  //if ($javactif != '') echo "style='display:none'";
  echo ">";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='98%'>";
  echo "<TR>";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    echo "<font face='verdana' size=3><b>" . $l_admin_options_shoutbox_title_long . "</b></font></TH>";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    echo "<font face='verdana' size=3><b>" . $l_admin_options_title_2 . "</b></font></TH>";
  echo "</TR>";

  echo "<TR>";
  /*
  echo "<TD colspan='2' align='center' class='catHead'>";
  echo "<font face='verdana' size='2'><B>" . $l_admin_options_shoutbox_title_long . " :</B></font>";
  echo "</TD>";
  */
  display_row_table("&nbsp;" . $l_admin_options_col_value . "&nbsp;", '');
  display_row_table($l_admin_options_col_description, '');
	if ($lang == "FR")
    display_row_table("&nbsp;<acronym title='Réseau local'>LAN</acronym>&nbsp;", '40');
  else
    display_row_table("&nbsp;<acronym title='Local Area Network'>LAN</acronym>&nbsp;", '40');
  //
  display_row_table("Internet", '40');
  echo "</TR>";
}
display_row(_SHOUTBOX, "_SHOUTBOX", 0, $l_admin_options_shoutbox_title_long, "", "", "£");
display_row(_SHOUTBOX_REFRESH_DELAY, "_SHOUTBOX_REFRESH_DELAY", 3, $l_admin_options_shoutbox_refresh_delay, "60", "60", _SHOUTBOX);
display_row(_SHOUTBOX_STORE_MAX, "_SHOUTBOX_STORE_MAX", 3, $l_admin_options_shoutbox_store_max, "200", "300", _SHOUTBOX);
display_row(_SHOUTBOX_STORE_DAYS, "_SHOUTBOX_STORE_DAYS", 2, $l_admin_options_shoutbox_store_days, "10", "20", _SHOUTBOX);
display_row(_SHOUTBOX_QUOTA_USER_DAY, "_SHOUTBOX_QUOTA_USER_DAY", 2, $l_admin_options_shoutbox_day_user_quota, "15", "30", _SHOUTBOX);
display_row(_SHOUTBOX_QUOTA_USER_WEEK, "_SHOUTBOX_QUOTA_USER_WEEK", 3, $l_admin_options_shoutbox_week_user_quota, "30", "50", _SHOUTBOX);
display_row(_SHOUTBOX_PUBLIC, "_SHOUTBOX_PUBLIC", 0, $l_admin_options_shoutbox_public . " + RSS", "", "", _SHOUTBOX);
display_separe();
if ( (_SHOUTBOX != "") and (_SHOUTBOX_NEED_APPROVAL != "") ) $enable_option = "£";   else   $enable_option = "";
display_row(_SHOUTBOX_NEED_APPROVAL, "_SHOUTBOX_NEED_APPROVAL", 0, $l_admin_options_shoutbox_need_approval, "", "", _SHOUTBOX);
display_row(_SHOUTBOX_APPROVAL_QUEUE, "_SHOUTBOX_APPROVAL_QUEUE", 2, $l_admin_options_shoutbox_approval_queue, "10", "20", $enable_option);
display_row(_SHOUTBOX_APPROVAL_QUEUE_USER, "_SHOUTBOX_APPROVAL_QUEUE_USER", 1, $l_admin_options_shoutbox_approval_queue_user, "3", "3", $enable_option);
display_row(_SHOUTBOX_LOCK_USER_APPROVAL, "_SHOUTBOX_LOCK_USER_APPROVAL", 2, $l_admin_options_shoutbox_lock_user_approval, "", "", $enable_option);
display_separe();
if ( (_SHOUTBOX != "") and (_SHOUTBOX_VOTE != "") ) $enable_option = "£";   else   $enable_option = "";
display_row(_SHOUTBOX_VOTE, "_SHOUTBOX_VOTE", 0, $l_admin_options_shoutbox_can_vote, "X", "X", _SHOUTBOX);
display_row(_SHOUTBOX_MAX_NOTES_USER_DAY, "_SHOUTBOX_MAX_NOTES_USER_DAY", 3, $l_admin_options_shoutbox_day_votes_quota, "", "", $enable_option);
display_row(_SHOUTBOX_MAX_NOTES_USER_WEEK, "_SHOUTBOX_MAX_NOTES_USER_WEEK", 3, $l_admin_options_shoutbox_week_votes_quota, "", "", $enable_option);
display_row(_SHOUTBOX_REMOVE_MESSAGE_VOTES, "_SHOUTBOX_REMOVE_MESSAGE_VOTES", 2, $l_admin_options_shoutbox_remove_msg_votes, "5", "10", $enable_option);
display_row(_SHOUTBOX_LOCK_USER_VOTES, "_SHOUTBOX_LOCK_USER_VOTES", 2, $l_admin_options_shoutbox_lock_user_votes, "15", "30", $enable_option);
//
if ($check != "update")
{
  //echo "</TR>";
  echo "</table>";
  //echo "</BR>";
  echo "</div>";
}

#
##
###
#######>------------------------------------------------ TAB 5 ------------------------------------------------
###
##
#

if ($check != "update")
{
  echo "<div id='tab_5' style='display:none' ";
  //if ($javactif != '') echo "style='display:none'";
  echo ">";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='98%'>";
  echo "<TR>";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    echo "<font face='verdana' size=3><b> &nbsp; " . $l_admin_options_bookmarks . " &nbsp; </b></font> ";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    echo "<font face='verdana' size=3><b>" . $l_admin_options_title_2 . "</b></font></TH>";    
    echo "</TH>";
  echo "</TR>";
  echo "<TR>";
    display_row_table("&nbsp;" . $l_admin_options_col_value . "&nbsp;", '60');
    display_row_table($l_admin_options_col_description, '');
    if ($lang == "FR")
      display_row_table("&nbsp;<acronym title='Réseau local'>LAN</acronym>&nbsp;", '40');
    else
      display_row_table("&nbsp;<acronym title='Local Area Network'>LAN</acronym>&nbsp;", '40');
    //
    display_row_table("Internet", '40');
  echo "</TR>";
}
display_row(_BOOKMARKS, "_BOOKMARKS", 0, $l_admin_options_bookmarks, "", "", "£");
display_row(_BOOKMARKS_VOTE, "_BOOKMARKS_VOTE", 0, $l_admin_options_bookmarks_can_vote, "X", "X", _BOOKMARKS);
display_row(_BOOKMARKS_PUBLIC, "_BOOKMARKS_PUBLIC", 0, $l_admin_options_bookmarks_public . " + RSS", "X", "X", _BOOKMARKS);
display_row(_BOOKMARKS_NEED_APPROVAL, "_BOOKMARKS_NEED_APPROVAL", 0, $l_admin_options_bookmarks_need_approval, "", "X", _BOOKMARKS);

if ($check != "update")
{
  echo "</TABLE>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='98%'>";
  echo "<TR>";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    echo "<font face='verdana' size=3><b> &nbsp; " . $l_admin_options_title_table_2 . " &nbsp; </b></font> ";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    echo "<font face='verdana' size=3><b>" . $l_admin_options_title_2 . "</b></font></TH>";
    echo "</TH>";
  echo "</TR>";
  echo "<TR>";
    //display_row_table($l_admin_options_col_option, '');
    display_row_table($l_admin_options_col_value, '30%');
    //display_row_table($l_admin_options_col_comment, '');
    display_row_table($l_admin_options_col_description, '');
    //display_row_table("&nbsp;LAN&nbsp;", '');
    if ($lang == "FR")
      display_row_table("&nbsp;<acronym title='Réseau local'>LAN</acronym>&nbsp;", '40');
    else
      display_row_table("&nbsp;<acronym title='Local Area Network'>LAN</acronym>&nbsp;", '40');
    //
    display_row_table("Internet", '40');
  echo "</TR>";
}
display_row(_SITE_URL_TO_SHOW, "_SITE_URL_TO_SHOW", 100, $l_admin_options_site_url, "-", "X", "£");
display_row(_SITE_TITLE_TO_SHOW, "_SITE_TITLE_TO_SHOW", 100, $l_admin_options_site_title, "-", "X", "£");

if ($check != "update")
{
  echo "</TR>";
  echo "<TR>";
    echo "<TD align='center' COLSPAN='5' class='catBottom'>";
    echo "<font face='verdana' size='2'>";
    echo $l_admin_options_info_1 . "</font>";
    echo "</TD>";
  echo "</TR>";
  //echo "</table>";
  //echo "</div>";
}



if ($check != "update")
{
  echo "</TABLE>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='98%'>";
  echo "<TR>";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    echo "<font face='verdana' size=3><b> &nbsp; " . $l_admin_options_status_reasons_list . " &nbsp; </b></font> ";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    echo "<font face='verdana' size=3><b>" . $l_admin_options_title_2 . "</b></font></TH>";
    echo "</TH>";
  echo "</TR>";
  echo "<TR>";
    display_row_table($l_admin_options_col_value, '250');
    display_row_table($l_admin_options_col_description, '');
    if ($lang == "FR")
      display_row_table("&nbsp;<acronym title='Réseau local'>LAN</acronym>&nbsp;", '40');
    else
      display_row_table("&nbsp;<acronym title='Local Area Network'>LAN</acronym>&nbsp;", '40');
    //
    display_row_table("Internet", '40');
  echo "</TR>";
}
display_row(_ONLINE_REASONS_LIST, "_ONLINE_REASONS_LIST", 150, $l_admin_options_status_reason . " <I><font color='green'>" . $l_admin_session_info_online . "</font></i>", "", "", "£");
display_row(_AWAY_REASONS_LIST, "_AWAY_REASONS_LIST", 150, $l_admin_options_status_reason . " <I><font color='#F2C354'>" . $l_admin_session_info_away . "</font></i>", "", "", "£");
display_row(_BUSY_REASONS_LIST, "_BUSY_REASONS_LIST", 150, $l_admin_options_status_reason . " <I><font color='#F2A100'>" . $l_admin_session_info_busy . "</font></i>", "", "", "£");
display_row(_DONOTDISTURB_REASONS_LIST, "_DONOTDISTURB_REASONS_LIST", 150, $l_admin_options_status_reason . " <I><font color='red'>" . $l_admin_session_info_do_not_disturb . "</font></i>", "", "", "£");
display_row(_FORCE_STATUS_LIST_FROM_SERVER, "_FORCE_STATUS_LIST_FROM_SERVER", 0, $l_admin_options_force_status_list . ": <small><i>" . $l_admin_session_info_online . " / " . $l_admin_session_info_away . " / </i></small>...", "", "", "£");

if ($check != "update")
{
  echo "</table>";
  echo "</div>";
}


#
##
###
#######>------------------------------------------------ TAB 6 ------------------------------------------------
###
##
#

if ($check != "update")
{
  echo "<div id='tab_6' style='display:none' ";
  //if ($javactif != '') echo "style='display:none'";
  echo ">";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='98%'>";
  echo "<TR>";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    //echo "<font face='verdana' size=3><b>" . $l_admin_options_title . "</b></font></TH>";
    echo "<font face='verdana' size=3><b>" . $l_admin_options_special_modes . "</b></font></TH>";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    echo "<font face='verdana' size=3><b>" . $l_admin_options_title_2 . "</b></font></TH>";
  echo "</TR>";

  echo "<TR>";
  //echo "<TD colspan='2' align='center' class='catHead'>";
  //echo "<font face='verdana' size='2'><B>" . $l_admin_options_special_modes . " :</B></font>";
  //echo "</TD>";
  display_row_table("&nbsp;" . $l_admin_options_col_value . "&nbsp;", '280');
  display_row_table($l_admin_options_col_description, '');
	//display_row_table("&nbsp;LAN&nbsp;", '');
	if ($lang == "FR")
    display_row_table("&nbsp;<acronym title='Réseau local'>LAN</acronym>&nbsp;", '40');
  else
    display_row_table("&nbsp;<acronym title='Local Area Network'>LAN</acronym>&nbsp;", '40');
  //
  display_row_table("Internet", '40');
  echo "</TR>";
  echo "<TR>";
    echo "<TD class='row2' align='right'>";
      echo "<font face='verdana' size='1'>Normal ";
      echo "<INPUT name='special_mode' TYPE='radio' VALUE='1' class='genmed' ";
      if ( (_SPECIAL_MODE_GROUP_COMMUNITY == '') and (_SPECIAL_MODE_OPEN_COMMUNITY == '') ) echo "checked";
      echo ">";
    echo "</TD>";
    echo "<TD align='LEFT' class='row3'>";
      echo "<font face='verdana' size='2'>&nbsp;" . $l_admin_options_normal_mode . "</font>";
    echo "</TD>";
    echo "<TD align='CENTER' class='row2'>";
      echo "<IMG SRC='" . _FOLDER_IMAGES . "thumb_up.png' WIDTH='16' HEIGHT='16' ALT='' TITLE=''>";
    echo "<TD align='CENTER' class='row2'>";
      echo "<IMG SRC='" . _FOLDER_IMAGES . "thumb_up.png' WIDTH='16' HEIGHT='16' ALT='' TITLE=''>";
    echo "</TD>";
  echo "</TR>";
  echo "<TR>";
    echo "<TD class='row2' align='right'>";
      if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
        echo "<A HREF='http://www.intramessenger.net/doc/liste_options.html#_SPECIAL_MODE_GROUP_COMMUNITY' target='_blank'>";
      else
        echo "<A HREF='http://www.intramessenger.net/doc/options_list.html#_SPECIAL_MODE_GROUP_COMMUNITY' target='_blank'>";
      //
      echo "<font face='verdana' size='1'>SPECIAL_MODE_GROUP_COMMUNITY</A> ";
      echo "<INPUT name='special_mode' TYPE='radio' VALUE='2' class='genmed' ";
      if ( (_SPECIAL_MODE_GROUP_COMMUNITY != '') and (_SPECIAL_MODE_OPEN_COMMUNITY == '') ) echo "checked";
      echo ">";
    echo "</TD>";
    echo "<TD align='LEFT' class='row3'>";
      echo "<font face='verdana' size='2'>&nbsp;" . $l_admin_options_groupcommunity . "</font>";
    echo "</TD>";
    echo "<TD align='CENTER' class='row2'>";
    echo "</TD>";
    echo "<TD align='CENTER' class='row2'>";
    echo "</TD>";
  echo "</TR>";
  echo "<TR>";
    echo "<TD class='row2' align='right'>";
      if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
        echo "<A HREF='http://www.intramessenger.net/doc/liste_options.html#_SPECIAL_MODE_OPEN_COMMUNITY' target='_blank'>";
      else
        echo "<A HREF='http://www.intramessenger.net/doc/options_list.html#_SPECIAL_MODE_OPEN_COMMUNITY' target='_blank'>";
      //
      echo "<font face='verdana' size='1'>SPECIAL_MODE_OPEN_COMMUNITY</A> ";
      echo "<INPUT name='special_mode' TYPE='radio' VALUE='3' class='genmed' ";
      if ( (_SPECIAL_MODE_GROUP_COMMUNITY == '') and (_SPECIAL_MODE_OPEN_COMMUNITY != '') ) echo "checked";
      echo ">";
    echo "</TD>";
    echo "<TD align='LEFT' class='row3'>";
      echo "<font face='verdana' size='2'>&nbsp;" . $l_admin_options_opencommunity . "</font>";
    echo "</TD>";
    echo "<TD align='CENTER' class='row2'>";
      //echo "<IMG SRC='" . _FOLDER_IMAGES . "thumb_down.png' WIDTH='16' HEIGHT='16' ALT='' TITLE=''>";
    echo "<TD align='CENTER' class='row2'>";
      echo "<IMG SRC='" . _FOLDER_IMAGES . "thumb_down.png' WIDTH='16' HEIGHT='16' ALT='' TITLE=''>";
    echo "</TD>";
  echo "</TR>";
  echo "<TR>";
    echo "<TD class='row2' align='right'>";
      if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
        echo "<A HREF='http://www.intramessenger.net/doc/liste_options.html#_SPECIAL_MODE_OPEN_GROUP_COMMUNITY' target='_blank'>";
      else
        echo "<A HREF='http://www.intramessenger.net/doc/options_list.html#_SPECIAL_MODE_OPEN_GROUP_COMMUNITY' target='_blank'>";
      //
      echo "<font face='verdana' size='1'>SPECIAL_MODE_OPEN_GROUP_COMMUNITY</A> ";
      echo "<INPUT name='special_mode' TYPE='radio' VALUE='4' class='genmed' ";
      if ( (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '') and (_SPECIAL_MODE_GROUP_COMMUNITY == '') and (_SPECIAL_MODE_OPEN_COMMUNITY == '') ) echo "checked";
      echo ">";
    echo "</TD>";
    echo "<TD align='LEFT' class='row3'>";
      echo "<font face='verdana' size='2'>&nbsp;";
      //echo "<IMG SRC='" . _FOLDER_IMAGES . "new.gif' WIDTH='30' HEIGHT='13' ALT='" . $l_admin_options_new . "' TITLE='" . $l_admin_options_new . "' /> ";
      echo $l_admin_options_opengroupcommunity . "</font>";
    echo "</TD>";
    echo "<TD align='CENTER' class='row2'>";
    echo "</TD>";
    echo "<TD align='CENTER' class='row2'>";
    echo "</TD>";
  echo "</TR>";
}
else
{
  $special_mode = "1";
  if ( (_SPECIAL_MODE_GROUP_COMMUNITY != '') and (_SPECIAL_MODE_OPEN_COMMUNITY == '') ) $special_mode == "2";
  if ( (_SPECIAL_MODE_OPEN_COMMUNITY != '') and (_SPECIAL_MODE_GROUP_COMMUNITY == '') ) $special_mode == "3";
  if ( (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '') and (_SPECIAL_MODE_GROUP_COMMUNITY == '') and (_SPECIAL_MODE_OPEN_COMMUNITY == '') ) $special_mode == "4";
  echo "<INPUT TYPE='hidden' name='special_mode' value = '" . $special_mode . "' />";
}

if ($check != "update")
{
  echo "<TR>";
  echo "<TD colspan='5' align='center' class='catHead'>";
  echo "<font face='verdana' size='2'><B>" . $l_admin_options_info_10 . " :</B></font>";
  echo "</TD>";
  echo "</TR>";
}
$external_authentication_name = "";
$external_authentication = _EXTERNAL_AUTHENTICATION;
if ($external_authentication != "") 
{
  $external_authentication_name = f_type_auth_extern();
  if ($external_authentication_name == "") $external_authentication = "";
}
$external_auth_link = "<A HREF='list_options_auth_updating.php?lang=" . $lang . "&'>" . $l_configure . "</A>";
if ($option_show_option_name != "") $external_auth_link = "";
if ( ($lang == "FR") or ( ($lang == "") and (_LANG == "FR") ) )
  $libelle = "<A HREF='http://www.intramessenger.net/exter_auth.php?lang=FR&' target='_blank'>";
else
  $libelle = "<A HREF='http://www.intramessenger.net/exter_auth.php?lang=EN&' target='_blank'>";
//
if ($external_authentication_name != "")
{
  $libelle .= trim($l_admin_authentication_extern) . "</A> ";
  if ($external_authentication_name != "LDAP")
    display_row($external_authentication, "_EXTERNAL_AUTHENTICATION", 0, $libelle . " <I>" . $external_authentication_name . "</I>&nbsp; : &nbsp;<B>" . $external_auth_link ."</B>", "", "", "£");
  else
    display_row($external_authentication, "_EXTERNAL_AUTHENTICATION", 0, $libelle . " <I>" . $external_authentication_name . "</I>. To configure: /common/config/ldap.config.inc.php" . "", "", "", "£");
}
else
{
  $libelle .= $l_admin_options_info_10 . "</A> ";
  display_row($external_authentication, "_EXTERNAL_AUTHENTICATION", 0, $libelle . " : <font color='gray'>" . $l_admin_check_off . "</font>&nbsp; - &nbsp;" . $external_auth_link, "", "", "£");
}
//
display_row(_EXTERN_URL_TO_REGISTER, "_EXTERN_URL_TO_REGISTER", 150, $l_admin_extern_url_to_register, "", "", _EXTERNAL_AUTHENTICATION);
display_row(_EXTERN_URL_FORGET_PASSWORD, "_EXTERN_URL_FORGET_PASSWORD", 150, $l_admin_extern_url_password_forget, "", "", _EXTERNAL_AUTHENTICATION);
display_row(_EXTERN_URL_CHANGE_PASSWORD, "_EXTERN_URL_CHANGE_PASSWORD", 150, $l_admin_extern_url_change_password, "", "", _EXTERNAL_AUTHENTICATION);
display_row(_SITE_TITLE, "_SITE_TITLE", 150, $l_admin_options_site_title, "", "", _EXTERNAL_AUTHENTICATION);
//

if ($check != "update")
{
  echo "</table>";
  echo "<table cellspacing='1' cellpadding='1' class='forumline' width='98%'>";
  //
  echo "<TR>";
  echo "<TD colspan='2' align='center' class='catHead'>";
  echo "<font face='verdana' size='2'><B>" . $l_admin_options_special_options . " :</B></font>";
  echo "</TD>";
  //display_row_table("&nbsp;" . $l_admin_options_col_value . "&nbsp;", '');
  //display_row_table($l_admin_options_special_options, '');
  //display_row_table($l_admin_options_col_description, '');
	if ($lang == "FR")
    display_row_table("&nbsp;<acronym title='Réseau local'>LAN</acronym>&nbsp;", '40');
  else
    display_row_table("&nbsp;<acronym title='Local Area Network'>LAN</acronym>&nbsp;", '40');
  //
  display_row_table("Internet", '40');
  //display_row_table("", '40');
  //display_row_table("&nbsp;", '40');
  
  echo "</TR>";
}
display_row(_ROLES_TO_OVERRIDE_PERMISSIONS, "_ROLES_TO_OVERRIDE_PERMISSIONS", 0, $l_admin_options_roles_to_override_permissions, "", "", "£");
display_separe();
//if (_SPECIAL_MODE_GROUP_COMMUNITY == "") 
//
if ( (_SPECIAL_MODE_GROUP_COMMUNITY == "") and (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY == "") ) $enable_option = "£";   else   $enable_option = "";
  display_row(_GROUP_FOR_SBX_AND_ADMIN_MSG, "_GROUP_FOR_SBX_AND_ADMIN_MSG", 0, $l_admin_options_group_for_sbx_and_admin_messages, "", "", $enable_option);
/*
}
else
{
  echo "<INPUT TYPE='hidden' name='T_GROUP_FOR_SBX_AND_ADMIN_MSG' value = '' />"; // force à vide
}
*/
//if ( (_SPECIAL_MODE_GROUP_COMMUNITY != "") or (_GROUP_FOR_SBX_AND_ADMIN_MSG != "") )
if ( (_SPECIAL_MODE_GROUP_COMMUNITY != "") xor (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY != "") xor (_GROUP_FOR_SBX_AND_ADMIN_MSG != "") ) $enable_option = "£";   else   $enable_option = "";
  display_row(_GROUP_USER_CAN_JOIN, "_GROUP_USER_CAN_JOIN", 0, $l_admin_options_group_user_can_join, "", "", $enable_option);
//
display_separe();
display_row(_ENTERPRISE_SERVER, "_ENTERPRISE_SERVER", 0, $l_admin_options_enterprise_server, "", "-", "£");
display_row(_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN , "_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN", 0, $l_admin_options_hierachic_management, "", "-", "£");

/*
if ($si_not_ok != "OK") 
{
	echo "<TR>";
		echo "<TD align='center' COLSPAN='10' class='catBottom'>";
		echo "<font face='verdana' size='2' color='RED'><B>";
		echo $l_admin_options_missing_option;
		echo " <A HREF='check.php' alt='' title=''>" . $l_admin_options_conf_file  . "</A> !</B>";
		echo "</TD>";
	echo "</TR>";
}
*/
//

// Infos :
//if ( (_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER != '') and (_PENDING_NEW_AUTO_ADDED_USER == '') ) 
//  echo "" . $l_admin_options_info_6 . " : <I>/common/config/ban_nickname.txt</I><BR/>";
//
//  

if ($check != "update")
{
  //echo "</TR>";
  echo "</table>";
  //echo "</BR>";
  echo "</div>";
}

#
##
###
#######>------------------------------------------------ TAB 7 ------------------------------------------------
###
##
#

if ($check != "update")
{
  echo "<div id='tab_7' style='display:none' ";
  //if ($javactif != '') echo "style='display:none'";
  echo ">";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='98%'>";
  echo "<TR>";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    echo "<font face='verdana' size=3><b> &nbsp; " . $l_admin_options_other_options_options . " &nbsp; </b></font> ";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    echo "<font face='verdana' size=3><b>" . $l_admin_options_title_2 . "</b></font></TH>";
    echo "</TH>";
  echo "</TR>";
/*
  echo "<TR>";
    //display_row_table($l_admin_options_col_option, '');
    display_row_table($l_admin_options_col_value, '');
    //display_row_table($l_admin_options_col_comment, '');
    display_row_table($l_admin_options_col_description, '');
    //display_row_table("&nbsp;LAN&nbsp;", '');
    if ($lang == "FR")
      display_row_table("&nbsp;<acronym title='Réseau local'>LAN</acronym>&nbsp;", '40');
    else
      display_row_table("&nbsp;<acronym title='Local Area Network'>LAN</acronym>&nbsp;", '40');
    //
    display_row_table("Internet", '40');
  echo "</TR>";
*/  
}
/*
display_row(_SITE_URL_TO_SHOW, "_SITE_URL_TO_SHOW", 100, $l_admin_options_site_url, "-", "X", "£");
display_row(_SITE_TITLE_TO_SHOW, "_SITE_TITLE_TO_SHOW", 100, $l_admin_options_site_title, "-", "X", "£");
*/
if ($check != "update")
{
/*
  echo "<TR>";
    echo "<TD align='center' COLSPAN='5' class='catBottom'>";
    echo "<font face='verdana' size='2'>";
    echo $l_admin_options_info_1 . "</font>";
    echo "</TD>";
  echo "</TR>";
  echo "</TABLE>";
  //
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='98%'>";
*/
  
/*
  echo "<TR>";
  echo "<TD colspan='5' align='center' class='catHead'>";
  //echo "<font face='verdana' size='2'><B>" . $l_admin_options_info_10 . " :</B></font>";
  echo "</TD>";
  echo "</TR>";
*/  
  echo "<TR>";
    //display_row_table($l_admin_options_col_option, '');
    display_row_table($l_admin_options_col_value, '');
    display_row_table($l_admin_options_col_description, '');
    //display_row_table("&nbsp;LAN&nbsp;", '');
    if ($lang == "FR")
      display_row_table("&nbsp;<acronym title='Réseau local'>LAN</acronym>&nbsp;", '40');
    else
      display_row_table("&nbsp;<acronym title='Local Area Network'>LAN</acronym>&nbsp;", '40');
    //
    display_row_table("Internet", '40');
  echo "</TR>";
}
display_row(_SERVERS_STATUS, "_SERVERS_STATUS", 0, $l_admin_options_servers_status, "", "", "£");
display_row(_FORCE_UPDATE_BY_SERVER, "_FORCE_UPDATE_BY_SERVER", 0, $l_admin_options_force_update_by_server, "X", "-", "£");
display_row(_FORCE_UPDATE_BY_INTERNET, "_FORCE_UPDATE_BY_INTERNET", 0, $l_admin_options_force_update_by_internet, "", "X", "£");
display_row(_PUBLIC_USERS_LIST, "_PUBLIC_USERS_LIST", 0, $l_admin_options_public_see_users, "", "-", "£");
display_row(_PUBLIC_POST_AVATAR, "_PUBLIC_POST_AVATAR", 0, $l_admin_options_public_upload_avatar, "X", "X", "£");
display_row(_PUBLIC_OPTIONS_LIST, "_PUBLIC_OPTIONS_LIST", 0, $l_admin_options_public_see_options, "X", "", "£");
display_row(_SEND_ADMIN_ALERT_EMAIL, "_SEND_ADMIN_ALERT_EMAIL", 0, $l_admin_options_send_admin_alert_by_email, "", "", "£");
display_row(_FLAG_COUNTRY_FROM_IP, "_FLAG_COUNTRY_FROM_IP", 0, $l_admin_options_flag_country, "-", "X", "£");
display_row(_TIME_ZONES, "_TIME_ZONES", 0, $l_admin_options_time_zones, "-", "X", "£");
display_row(_INVITE_FILL_PROFILE_ON_FIRST_LOGIN, "_INVITE_FILL_PROFILE_ON_FIRST_LOGIN", 0, $l_admin_options_profile_first_register, "", "", "£");
display_row(_LOG_SESSION_OPEN, "_LOG_SESSION_OPEN", 0, $l_admin_options_log_session_open, "", "X", "£");
//display_row(_STATISTICS, "_STATISTICS", 0, $l_admin_options_statistics, "X", "X", "£");
display_row(_FORCE_AWAY_ON_SCREENSAVER, "_FORCE_AWAY_ON_SCREENSAVER", 0, $l_admin_options_force_away, "X", "X", "£");
if ( ( ($last_check_version != "OK.") and ($last_check_version != "NEW") ) or (_CHECK_VERSION_INTERNET == "") )
  display_row(_CHECK_VERSION_INTERNET, "_CHECK_VERSION_INTERNET", 0, $l_admin_options_check_version_internet, "-", "X", "£");
else
  echo "<INPUT TYPE='hidden' name='T_CHECK_VERSION_INTERNET' value = '" . _CHECK_VERSION_INTERNET . "' />";
//
display_row(_WAIT_STARTUP_IF_SERVER_UNAVAILABLE, "_WAIT_STARTUP_IF_SERVER_UNAVAILABLE", 0, $l_admin_options_wait_startup_if_server_hs, "X", "", "£");
display_row(_UNREAD_MESSAGE_VALIDITY, "_UNREAD_MESSAGE_VALIDITY", 2, $l_admin_options_unread_message_validity, "30", "40", "£");
display_row(_LOCK_AFTER_NO_ACTIVITY_DURATION, "_LOCK_AFTER_NO_ACTIVITY_DURATION", 2, $l_admin_options_lock_after_no_activity_duration, "50", "60", "£");
//
if ($check != "update")
{
  //echo "</TR>";
  echo "</table>";
  //echo "</BR>";
  echo "</div>";
}

#
##
###
#######>------------------------------------------------ TAB 8 ------------------------------------------------
###
##
#

if ($check != "update")
{
  echo "<div id='tab_8' style='display:none' ";
  //if ($javactif != '') echo "style='display:none'";
  echo ">";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='98%'>";
  echo "<TR>";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    echo "<font face='verdana' size=3><b> &nbsp; " . $l_admin_options_share_files_title . " &nbsp; </b></font> ";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    echo "<font face='verdana' size=3><b>" . $l_admin_options_title_2 . "</b></font></TH>";
    echo "</TH>";
  echo "</TR>";
  echo "<TR>";
    display_row_table($l_admin_options_col_value, '');
    display_row_table($l_admin_options_col_description, '');
    if ($lang == "FR")
      display_row_table("&nbsp;<acronym title='Réseau local'>LAN</acronym>&nbsp;", '40');
    else
      display_row_table("&nbsp;<acronym title='Local Area Network'>LAN</acronym>&nbsp;", '40');
    //
    display_row_table("Internet", '40');
  echo "</TR>";
}
$ref = "<font color='#FF5555'>[<acronym title='" . $l_admin_options_share_files_options_to_active . "'>§</acronym>]</font> ";
if ( (_SHARE_FILES_FTP_ADDRESS != "") and (_SHARE_FILES_FTP_LOGIN != "") and (_SHARE_FILES_FTP_PASSWORD_CRYPT != "") ) $ref = "";
//
display_row(_SHARE_FILES, "_SHARE_FILES", 0, $l_admin_options_share_files_allow . " " . $ref, "", "", "£");
display_row(_SHARE_FILES_NEED_APPROVAL, "_SHARE_FILES_NEED_APPROVAL", 0, $l_admin_options_share_files_need_approval, "", "X", _SHARE_FILES);
display_row(_SHARE_FILES_TRASH, "_SHARE_FILES_TRASH", 0, $l_admin_options_share_files_trash, "", "-", _SHARE_FILES);
display_row(_SHARE_FILES_SCREENSHOT, "_SHARE_FILES_SCREENSHOT", 0, $l_admin_options_share_files_screenshot, "", "", _SHARE_FILES);
display_row(_SHARE_FILES_WEBCAM, "_SHARE_FILES_WEBCAM", 0, $l_admin_options_share_files_webcam, "", "", _SHARE_FILES);
display_row(_SHARE_FILES_VOTE, "_SHARE_FILES_VOTE", 0, $l_admin_options_share_files_can_vote, "", "", _SHARE_FILES);
//display_separe();
//
if ($check != "update")
{
  echo "</TABLE>";
  echo "<div id='sharefile_title_a'>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='98%'>";
    echo "<TR>";
      echo "<TD align=center COLSPAN='4' class='row3'>";
        echo "<font face='verdana' size='2'>";
        echo "<a href='#' onclick=\"show_only_bis('sharefile_a', 'sharefile_title_a');\" TITLE='" . $l_admin_options_more . "'>" . $l_admin_options_more . "</a> &nbsp;";
      echo "</TD>";
    echo "</TR>";
  echo "</TABLE>";
  echo "</div>";
  //
  echo "<div id='sharefile_a' style='display:none'>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='98%'>";
}
if ( (_SHARE_FILES != "") and (_SHARE_FILES_EXCHANGE != "") ) $enable_option = "£";   else   $enable_option = "";
display_row(_SHARE_FILES_EXCHANGE, "_SHARE_FILES_EXCHANGE", 0, $l_admin_options_share_files_exchange . " ", "", "", _SHARE_FILES);
display_row(_SHARE_FILES_EXCHANGE_NEED_APPROVAL, "_SHARE_FILES_EXCHANGE_NEED_APPROVAL", 0, $l_admin_options_share_files_exchange_need_approval, "-", "-", $enable_option);
display_row(_SHARE_FILES_EXCHANGE_TRASH, "_SHARE_FILES_EXCHANGE_TRASH", 0, $l_admin_options_share_files_exchange_trash, "-", "-", $enable_option);
display_row(_SHARE_FILES_EXCHANGE_SCREENSHOT, "_SHARE_FILES_EXCHANGE_SCREENSHOT", 0, $l_admin_options_share_files_screenshot_exchange, "", "", $enable_option);
display_row(_SHARE_FILES_EXCHANGE_WEBCAM, "_SHARE_FILES_EXCHANGE_WEBCAM", 0, $l_admin_options_share_files_webcam_exchange, "", "", $enable_option);
display_row(_SHARE_FILES_EXCHANGE_UNREAD_VALIDITY, "_SHARE_FILES_EXCHANGE_UNREAD_VALIDITY", 2, $l_admin_options_share_files_exchange_unread_validity, "60", "30", _SHARE_FILES_EXCHANGE);
//display_separe();

if ($check != "update")
{
  echo "</TABLE>";
  echo "</div>";
  //
  echo "<div id='sharefile_title_b' style='display:none'>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='98%'>";
    echo "<TR>";
      echo "<TD align=center COLSPAN='4' class='row3'>";
        echo "<font face='verdana' size='2'>";
        echo "<a href='#' onclick=\"show_only_bis('sharefile_b', 'sharefile_title_b');\" TITLE='" . $l_admin_options_more . "'>" . $l_admin_options_more . "</a> &nbsp;";
      echo "</TD>";
    echo "</TR>";
  echo "</TABLE>";
  echo "</div>";
  //
  echo "<div id='sharefile_b' style='display:block'>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='98%'>";
}
display_row(_SHARE_FILES_FTP_ADDRESS, "_SHARE_FILES_FTP_ADDRESS", 60, $l_admin_options_share_files_ftp_address, "", "", "£");
display_row(_SHARE_FILES_FTP_LOGIN, "_SHARE_FILES_FTP_LOGIN", 30, $l_admin_options_share_files_ftp_login, "", "", "£");
display_row(_SHARE_FILES_FTP_PASSWORD_CRYPT, "_SHARE_FILES_FTP_PASSWORD_CRYPT", 60, $l_admin_options_share_files_ftp_password_crypt, "", "", "£");
display_row(_SHARE_FILES_FTP_PASSWORD, "_SHARE_FILES_FTP_PASSWORD", 60, $l_admin_options_share_files_ftp_password, "", "", _SHARE_FILES);
display_row(_SHARE_FILES_FOLDER, "_SHARE_FILES_FOLDER", 60, $l_admin_options_share_files_folder, "", "", _SHARE_FILES);
display_row(_SHARE_FILES_FTP_PORT_NUMBER, "_SHARE_FILES_FTP_PORT_NUMBER", 4, $l_admin_options_share_files_ftp_port_number, "21", "21", _SHARE_FILES);

//display_separe();
//
if ($check != "update")
{
  echo "</TABLE>";
  echo "</div>";
  //
  echo "<div id='sharefile_title_c'>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='98%'>";
    echo "<TR>";
      echo "<TD align=center COLSPAN='4' class='row3'>"; // thHead
        echo "<font face='verdana' size='2'>";
        echo "<a href='#' onclick=\"show_only_bis('sharefile_c', 'sharefile_title_c');\" TITLE='" . $l_admin_options_more . "'>" . $l_admin_options_more . "</a> &nbsp;";
      echo "</TD>";
    echo "</TR>";
  echo "</TABLE>";
  echo "</div>";
  //
  echo "<div id='sharefile_c' style='display:none'>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='98%'>";
}
display_row(_SHARE_FILES_MAX_FILE_SIZE, "_SHARE_FILES_MAX_FILE_SIZE", 6, $l_admin_options_share_files_max_file_size, "", "", _SHARE_FILES);
display_row(_SHARE_FILES_MAX_NB_FILES_TOTAL, "_SHARE_FILES_MAX_NB_FILES_TOTAL", 5, $l_admin_options_share_files_max_nb_files_total, "", "", _SHARE_FILES);
display_row(_SHARE_FILES_MAX_NB_FILES_USER, "_SHARE_FILES_MAX_NB_FILES_USER", 4, $l_admin_options_share_files_max_nb_files_user, "", "", _SHARE_FILES);
display_row(_SHARE_FILES_MAX_SPACE_SIZE_TOTAL, "_SHARE_FILES_MAX_SPACE_SIZE_TOTAL", 6, $l_admin_options_share_files_max_space_size_total, "X", "X", _SHARE_FILES);
display_row(_SHARE_FILES_MAX_SPACE_SIZE_USER, "_SHARE_FILES_MAX_SPACE_SIZE_USER", 5, $l_admin_options_share_files_max_space_size_user, "", "", _SHARE_FILES);
display_row(_SHARE_FILES_APPROVAL_QUEUE, "_SHARE_FILES_APPROVAL_QUEUE", 2, $l_admin_options_share_files_approval_queue, "", "", _SHARE_FILES);
display_row(_SHARE_FILES_QUOTA_FILES_USER_WEEK, "_SHARE_FILES_QUOTA_FILES_USER_WEEK", 3, $l_admin_options_share_files_quota_files_user_week, "", "", _SHARE_FILES);
if ($check != "update")
{

  echo "</TABLE>";
  echo "</div>";
  //
  echo "<div id='sharefile_title_d'>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='98%'>";
    echo "<TR>";
      echo "<TD align=center COLSPAN='4' class='row3'>"; // thHead
        echo "<font face='verdana' size='2'>";
        echo "<a href='#' onclick=\"show_only_bis('sharefile_d', 'sharefile_title_d');\" TITLE='" . $l_admin_options_more . "'>" . $l_admin_options_more . "</a> &nbsp;";
      echo "</TD>";
    echo "</TR>";
  // ---- début ----
  echo "<TR>";
    echo "<TD align='center' COLSPAN='5' class='catBottom'>";
    echo "<font face='verdana' size='2' color='GRAY'>";
    echo $l_admin_options_share_files_info . "**</font>";
    echo "</TD>";
  echo "</TR>";
  // ---- fin ----

  echo "</TABLE>";
  echo "</div>";
  //
  echo "<div id='sharefile_d' style='display:none'>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='98%'>";
}
display_row(_SHARE_FILES_COMPRESS, "_SHARE_FILES_COMPRESS", 0, $l_admin_options_share_files_compress, "", "X", _SHARE_FILES);
//if (_SHARE_FILES_COMPRESS != "")
//{
  display_row(_SHARE_FILES_PROTECT, "_SHARE_FILES_PROTECT", 0, $l_admin_options_share_files_protect, "", "", _SHARE_FILES_COMPRESS);
  display_row(_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_DAY, "_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_DAY", 3, $l_admin_options_share_files_download_quota_day, "-", "", _SHARE_FILES);
  display_row(_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_WEEK, "_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_WEEK", 4, $l_admin_options_share_files_download_quota_week, "-", "X", _SHARE_FILES);
  display_row(_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_MONTH, "_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_MONTH", 5, $l_admin_options_share_files_download_quota_month, "", "X", _SHARE_FILES);
  display_row(_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_DAY, "_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_DAY", 4, $l_admin_options_share_files_download_quota_mb_day, "-", "", _SHARE_FILES);
  display_row(_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_WEEK, "_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_WEEK", 5, $l_admin_options_share_files_download_quota_mb_week, "-", "X", _SHARE_FILES);
  display_row(_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_MONTH, "_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_MONTH", 5, $l_admin_options_share_files_download_quota_mb_month, "", "X", _SHARE_FILES);
/*
}
else
{
  echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_DAY' value = '" . _SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_DAY . "' />";
  echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_WEEK' value = '" . _SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_WEEK . "' />";
  echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_MONTH' value = '" . _SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_MONTH . "' />";
  echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_DAY' value = '" . _SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_DAY . "' />";
  echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_WEEK' value = '" . _SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_WEEK . "' />";
  echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_MONTH' value = '" . _SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_MONTH . "' />";
}
*/
//
//
if ($check != "update")
{


  //echo "</TR>";
  echo "</table>";
  //echo "</BR>";
  echo "</div>";
  //
  echo "</div>";
}


#
##
###
#######>------------------------------------------------ TAB 9 ------------------------------------------------
###
##
#

if ($check != "update")
{
  echo "<div id='tab_9' style='display:none' ";
  //if ($javactif != '') echo "style='display:none'";
  echo ">";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='98%'>";
  echo "<TR>";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    echo "<font face='verdana' size=3><b> &nbsp; " . $l_admin_options_backup_files_title . " &nbsp; </b></font> ";
    echo "<TH align=center COLSPAN='2' class='thHead'>";
    echo "<font face='verdana' size=3><b>" . $l_admin_options_title_2 . "</b></font></TH>";
    echo "</TH>";
  echo "</TR>";
  echo "<TR>";
    display_row_table($l_admin_options_col_value, '');
    display_row_table($l_admin_options_col_description, '');
    if ($lang == "FR")
      display_row_table("&nbsp;<acronym title='Réseau local'>LAN</acronym>&nbsp;", '40');
    else
      display_row_table("&nbsp;<acronym title='Local Area Network'>LAN</acronym>&nbsp;", '40');
    //
    display_row_table("Internet", '40');
  echo "</TR>";
}
$ref = "<font color='#FF5555'>[<acronym title='" . $l_admin_options_backup_files_options_to_active . "'>§</acronym>]</font> ";
if ( (_BACKUP_FILES_FTP_ADDRESS != "") and (_BACKUP_FILES_FTP_LOGIN != "") and (_BACKUP_FILES_FTP_PASSWORD_CRYPT != "") ) $ref = "";
display_row(_BACKUP_FILES, "_BACKUP_FILES", 0, $l_admin_options_backup_files_allow. " " . $ref, "", "", "£");
//
display_row(_BACKUP_FILES_ALLOW_MULTI_FOLDERS, "_BACKUP_FILES_ALLOW_MULTI_FOLDERS", 0, $l_admin_options_backup_files_multi_folders, "X", "X", _BACKUP_FILES);
display_row(_BACKUP_FILES_ALLOW_SUB_FOLDERS, "_BACKUP_FILES_ALLOW_SUB_FOLDERS", 0, $l_admin_options_backup_files_sub_folders, "X", "X", _BACKUP_FILES);
display_row(_BACKUP_FILES_MAX_NB_ARCHIVES_USER, "_BACKUP_FILES_MAX_NB_ARCHIVES_USER", 1, $l_admin_options_backup_files_max_nb_backup_user, "3", "2", _BACKUP_FILES);
display_row(_BACKUP_FILES_MAX_ARCHIVE_SIZE, "_BACKUP_FILES_MAX_ARCHIVE_SIZE", 6, $l_admin_options_backup_files_max_file_size, "", "", _BACKUP_FILES);
display_row(_BACKUP_FILES_MAX_SPACE_SIZE_USER, "_BACKUP_FILES_MAX_SPACE_SIZE_USER", 6, $l_admin_options_share_files_max_space_size_user, "", "", _BACKUP_FILES);
display_row(_BACKUP_FILES_MAX_SPACE_SIZE_TOTAL, "_BACKUP_FILES_MAX_SPACE_SIZE_TOTAL", 6, $l_admin_options_share_files_max_space_size_total, "", "", _BACKUP_FILES);
#display_row(_BACKUP_FILES_THIS_LOCAL_FOLDER_ONLY, "_BACKUP_FILES_THIS_LOCAL_FOLDER_ONLY", 200, $l_admin_options_backup_files_this_local_folder, "", "", "£");
#display_row(_BACKUP_FILES_FORCE_EVERY_DAY_AT, "_BACKUP_FILES_FORCE_EVERY_DAY_AT", 200, $l_admin_options_backup_, "", "", "£");
display_row(_BACKUP_FILES_FTP_ADDRESS, "_BACKUP_FILES_FTP_ADDRESS", 60, $l_admin_options_share_files_ftp_address, "", "", "£");
display_row(_BACKUP_FILES_FTP_LOGIN, "_BACKUP_FILES_FTP_LOGIN", 20, $l_admin_options_share_files_ftp_login, "", "", "£");
display_row(_BACKUP_FILES_FTP_PASSWORD_CRYPT, "_BACKUP_FILES_FTP_PASSWORD_CRYPT", 60, $l_admin_options_share_files_ftp_password_crypt, "", "", "£");
display_row(_BACKUP_FILES_FTP_PASSWORD, "_BACKUP_FILES_FTP_PASSWORD", 60, $l_admin_options_share_files_ftp_password, "", "", _BACKUP_FILES);
display_row(_BACKUP_FILES_FOLDER, "_BACKUP_FILES_FOLDER", 60, $l_admin_options_share_files_folder, "", "", _BACKUP_FILES);
display_row(_BACKUP_FILES_FTP_PORT_NUMBER, "_BACKUP_FILES_FTP_PORT_NUMBER", 4, $l_admin_options_share_files_ftp_port_number, "21", "21", _BACKUP_FILES);


if ($check != "update")
{
  echo "<TR>";
    echo "<TD align='center' COLSPAN='5' class='catBottom'>";
    echo "<font face='verdana' size='2' color='GRAY'>";
    echo $l_admin_options_share_files_info . "</font>";
    echo "</TD>";
  echo "</TR>";
  echo "</table>";
  //echo "</BR>";
  echo "</div>";
}



#
##
###
#######>------------------------------------------------  ------------------------------------------------
###
##
#

if ($check == "update")
{
  echo "<BR/><BR/>";
  echo "<BR/><BR/>";
  echo "<BR/><BR/>";
  echo "<BR/><BR/>";
  echo "<BR/><BR/>";
  echo "<BR/><BR/>";
  echo "<CENTER>";
}
echo "</div>";
//
//
//
echo "<P id='save'/>";
if (!is_writeable("../common/config/config.inc.php"))
{
  aff_conf_readonly();
}
else
{
  if ( (is_readable("log/log_options_update.txt")) and (!is_writeable("log/log_options_update.txt")) )
  {
    echo "<div class='warning'>" . $l_install_file . " <I>/log/log_options_update.txt</I> : " . $l_admin_check_not_writeable . " !</div>";
    echo "<BR/>";
  }
  //echo "<BR/>";
  echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_options_bt_update . "' name='submit[updateoptions]' class='mainoption' />";
  //echo " &nbsp; &nbsp; &nbsp; ";
  //echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_check_last_options . "' name='submit[checkoptions]' class='liteoption' />";
  //echo "<INPUT TYPE='submit' VALUE = '" . $l_display . "' name='submit[checkoptions]' class='liteoption' />";
  echo "</FORM>";
  //
}
//echo "<SMALL><BR/></SMALL>";



if ($check != "update")
{


  echo "<TABLE WIDTH='98%' cellspacing='0' cellpadding='0' BORDER='0'>";
  //echo "<TR><TD WITH='50%' VALIGN='BOTTOM'>"; // VALIGN='TOP'
  echo "<TR><TD WITH='40%' VALIGN='TOP'>"; // VALIGN='TOP'


    echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
      echo "<FORM METHOD='GET' name='formulaire_cookies' ACTION ='set_cookies.php?'>";
      echo "<TR><TD COLSPAN='2' ALIGN='CENTER' class='catHead'>";
        //echo "<IMG SRC='" . _FOLDER_IMAGES . "new.gif' WIDTH='30' HEIGHT='13' ALT='" . $l_admin_options_new . "' TITLE='" . $l_admin_options_new . "' /> &nbsp; ";
      echo "<B>" . $l_admin_display_title . "</B></TD></TR>";
      //echo "<TR><TD COLSPAN='2' class='row3'>";
      echo "</TD></TR>";
      echo "<TR><TD COLSPAN='2' class='row1'>";
        echo "<font face='verdana' size='2'>";
        echo "<INPUT name='option_show_option_name' id='option_show_option_name' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
        //if ( (_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN == '') or (_SPECIAL_MODE_GROUP_COMMUNITY != '') )  echo " disabled ";
        if ($option_show_option_name <> '') echo "CHECKED";
        echo " />";
        echo "<label for='option_show_option_name'>" . $l_admin_options_show_option_name . "</label>"; //"<BR/>\n";
        echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "information.png' WIDTH='16' HEIGHT='16' TITLE='" . $l_admin_options_show_option_name ."' ALT='" . $l_admin_options_show_option_name ."' />&nbsp;";
      echo "</TD></TR>";
      echo "<TR><TD COLSPAN='2' ALIGN='CENTER' class='catBottom'>";
      echo "<input type='hidden' name='action' value = 'list_options_updating' />"; // les paramètres de cette page, et y revenir ensuite
      echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
      echo "<INPUT class='liteoption' TYPE='submit' VALUE ='" . $l_admin_bt_update . "' />";
      echo "</TD>";
      echo "</TR>";
      echo "</FORM>";
    echo "</TABLE>";
    



  echo "</TD><TD ALIGN='CENTER' VALIGN='MIDDLE'>";
  


    //
    //echo "<IMG SRC='" . _FOLDER_IMAGES . "question.png' WIDTH='32' HEIGHT='32' BORDER='0'>";
    //echo "</A><BR/>";
    //echo "../doc/intramessenger_options.pdf</I></A>";

    echo "<BR/>";
    echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
      echo "<TR><TD COLSPAN='2' ALIGN='CENTER' class='catHead'>";
      echo "<IMG SRC='" . _FOLDER_IMAGES . "help.png' WIDTH='16' HEIGHT='16' TITLE='" . $l_admin_options_doc_title ."' ALT='" . $l_admin_options_doc_title ."' /> ";
      echo "<B>" . $l_admin_options_doc_title . "</B>";
      //echo "&nbsp; <IMG SRC='" . _FOLDER_IMAGES . "new.gif' WIDTH='30' HEIGHT='13' /> &nbsp; "; // -->> en enlevant new, remettre help.png ci-dessus !
      echo "</TD></TR>";

      echo "</TR><TR>";
      echo "</TD><TD class='row1'><font face='verdana' size='2'>&nbsp;";
        if (is_readable("../doc/intramessenger_options.pdf")) 
          echo  "<A HREF='../doc/intramessenger_options.pdf' TITLE='' target='_blank'>";
        else
          echo  "<A HREF='http://www.intramessenger.net/doc/intramessenger_options.pdf' TITLE='' target='_blank'>";
        //
        echo $l_admin_options_doc_view . "</A>&nbsp;";
      echo "</TD>";

      echo "</TR><TR>"; //---------------

      echo "<TD class='row1'><font face='verdana' size='2'>&nbsp;";
        echo "<A HREF='" . $g_link_doc . "' title='' target='blank'>";
        echo $l_admin_options_doc_list . "</A>&nbsp;";
      //echo "<IMG SRC='" . _FOLDER_IMAGES . "new.gif' WIDTH='30' HEIGHT='13' />&nbsp;";
      echo "</TD></TR>";
    
    echo "</TABLE>";

  
  
  echo "</TD><TD WITH='40%' ALIGN='RIGHT' VALIGN='TOP'>";



    echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
    echo "<TR><TD COLSPAN='2' ALIGN='CENTER' class='catHead'><B>" . $l_legende . " : " . $l_admin_options_title_2 . "</B></TD></TR>";

    echo "</TR><TR><TD ALIGN='CENTER' WIDTH='25' class='row1'>";
    echo "<IMG SRC='" . _FOLDER_IMAGES . "thumb_up.png' WIDTH='16' HEIGHT='16'>";
    echo "</TD><TD class='row3'><font face='verdana' size='2'>&nbsp;" . $l_admin_options_title_2 . " : " . $l_admin_options_legende_not_empty . "&nbsp;";
    echo "</TD>";

    echo "</TD></TR><TR><TD ALIGN='CENTER' class='row1'>";
    //echo "&nbsp;";
    echo "<IMG SRC='" . _FOLDER_IMAGES . "thumb_down.png' WIDTH='16' HEIGHT='16'>";
    echo "</TD><TD class='row3'><font face='verdana' size='2'>&nbsp;" . $l_admin_options_title_2 . " : " . $l_admin_options_legende_empty . "&nbsp;";
    echo "</TD>";
    //
    echo "</TD></TR><TR><TD ALIGN='CENTER' class='row1'>";
    echo " "; //"<IMG SRC='" . _FOLDER_IMAGES . "bt_yellow.gif' WIDTH='18' HEIGHT='18' ALT='" . $l_admin_options_legende_up2u . "' TITLE='" . $l_admin_options_legende_up2u . "'>";
    echo "</TD><TD class='row3'><font face='verdana' size='2'>&nbsp;" . $l_admin_options_legende_up2u;

    echo "</TD></TR>";
    
    echo "</TABLE>";


  echo "</TD></TR>";
  

  echo "<TR><TD COLSPAN='3' ALIGN='CENTER'>";
    //if ( (_ENTERPRISE_SERVER == "") and ($check != "update") )
    if ( (_PASSWORD_FOR_PRIVATE_SERVER == "") and (_ENTERPRISE_SERVER == "") and ($check != "update") )
    {
      $bad_config_to_public_book = "";
      if (_IM_ADDRESS_BOOK_PASSWORD == "") $bad_config_to_public_book = "X";
      if (_ENTERPRISE_SERVER != "") $bad_config_to_public_book = "X";
      if (_PASSWORD_FOR_PRIVATE_SERVER != "") $bad_config_to_public_book = "X";
      if (_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER == "") $bad_config_to_public_book = "X";
      if (_PENDING_USER_ON_COMPUTER_CHANGE != "") $bad_config_to_public_book = "X";
      if (_FORCE_USERNAME_TO_PC_SESSION_NAME != "") $bad_config_to_public_book = "X";
      if (_PENDING_USER_ON_COMPUTER_CHANGE != "") $bad_config_to_public_book = "X";
      if (_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN != "") $bad_config_to_public_book = "X";
      if (_FORCE_UPDATE_BY_SERVER != "") $bad_config_to_public_book = "X";
      if (_FORCE_UPDATE_BY_INTERNET == "") $bad_config_to_public_book = "X";
      if (_USER_NEED_PASSWORD == "") $bad_config_to_public_book = "X";
      if (_HISTORY_MESSAGES_ON_ACP != "") $bad_config_to_public_book = "X";
      //if (_CHECK_VERSION_INTERNET == "") $bad_config_to_public_book = "X";
      if ( (intval(_MAX_NB_USER) < 100) and (intval(_MAX_NB_USER) >0) ) $bad_config_to_public_book = "X";
      if ( (intval(_MAX_NB_SESSION) < 50) and (intval(_MAX_NB_SESSION) > 0) ) $bad_config_to_public_book = "X";
      //echo "<BR/>";
      echo "<font size='1'>";
      echo "<BR/>";
      //echo $l_admin_options_info_7 . " : <A HREF='http://www.intramessenger.net/list/servers/' target='_blank'>" . $l_admin_options_info_book . "</A><BR/>";
      if ($bad_config_to_public_book == "") 
        echo "<A HREF='register_to_public_servers_list.php?lang=" . $lang . "&'>" . $l_admin_options_info_7 . "</A> " . $l_admin_options_info_book . "<BR/>";
      else
        echo "<A HREF='register_to_public_servers_list.php?lang=" . $lang . "&'>" . $l_admin_options_info_8 . "</A> " . " : <A HREF='http://www.intramessenger.net/list/servers/' target='_blank'>" . $l_admin_options_info_book . "</A><BR/>";
      echo "</font>";
    }
  echo "</TD></TR>";

  echo "</TABLE>";
  //
  if (is_writeable("../common/config/config.inc.bak.php"))
  {
    if (filesize("../common/config/config.inc.bak.php") > 100)
    {
      if (date($l_date_format_display, filemtime("../common/config/config.inc.bak.php")) == date($l_date_format_display))
      {
        echo "<br/>";
        echo "<FORM METHOD='POST' ACTION='list_options_restore.php?'>";
        echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
        echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_options_restore_options . "' name='submit' class='liteoption' />";
        echo "</FORM>";
      }
    }
  }
  //
  display_menu_footer();
}
//
echo "</body></html>";
?>