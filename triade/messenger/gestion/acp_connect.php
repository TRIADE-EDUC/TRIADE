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
define('INTRAMESSENGER',true);
//
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
if ( (isset($_SESSION['acp_init'])) and (isset($_SESSION['acp_login'])) )
{
  //header("location:acp_login.php");
  //die();
}
//
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
//
if (_ACP_ALLOW_MEMORY_AUTH != "")
{
  if (isset($_COOKIE['im_acp_login'])) $cook_acp_login = $_COOKIE['im_acp_login'];  else  $cook_acp_login = '';
  if (isset($_COOKIE['im_acp_pass'])) $cook_acp_pass = $_COOKIE['im_acp_pass'];  else  $cook_acp_pass = '';
}
else
{
  $cook_acp_login = '';
  $cook_acp_pass = '';
}
//
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_acp_auth_title . "</title>";
display_header();
echo "</head>";
//echo "<body>";
echo "<body background='" . _FOLDER_IMAGES . f_background_image_color() . "background.jpg'>";


//
if (_ACP_PROTECT_BY_HTACCESS == "")
{
  //session_start();
  ##session_destroy();
  //


  if (!isset($_POST['acp_login']))
  {
    echo "&nbsp;";
    if ($lang != "EN") echo "<A href='?lang=EN&'><IMG SRC='../images/flags/gb.png' WIDTH='18' HEIGHT='12' BORDER='0'></A> "; // <A href='?lang=EN&'>English</A> 
    if ($lang != "FR") echo "<A href='?lang=FR&'><IMG SRC='../images/flags/fr.png' WIDTH='18' HEIGHT='12' BORDER='0'></A> "; // <A href='?lang=FR&'>En français</A> 
    if ($lang != "IT") echo "<A href='?lang=IT&'><IMG SRC='../images/flags/it.png' WIDTH='18' HEIGHT='12' BORDER='0'></A>&nbsp;"; // <A href='?lang=IT&'>Italiano</A>
    if ($lang != 'ES') echo "<A HREF='?lang=ES&'><IMG SRC='../images/flags/es.png' WIDTH='18' HEIGHT='12' BORDER='0'></A>&nbsp;";
    if ($lang != "PT") echo "<A href='?lang=PT&'><IMG SRC='../images/flags/pt.png' WIDTH='18' HEIGHT='12' BORDER='0'></A>&nbsp;";
    if ($lang != "BR") echo "<A href='?lang=BR&'><IMG SRC='../images/flags/br.png' WIDTH='18' HEIGHT='12' BORDER='0'></A>&nbsp;";
    if ($lang != "RO") echo "<A href='?lang=RO&'><IMG SRC='../images/flags/ro.png' WIDTH='18' HEIGHT='12' BORDER='0'></A>&nbsp;";
    if ($lang != "DE") echo "<A href='?lang=DE&'><IMG SRC='../images/flags/de.png' WIDTH='18' HEIGHT='12' BORDER='0'></A>&nbsp;";
    if ($lang != "NL") echo "<A href='?lang=NL&'><IMG SRC='../images/flags/nl.png' WIDTH='18' HEIGHT='12' BORDER='0'></A>&nbsp;";
    //
    echo "<TABLE WIDTH='100%' HEIGHT='95%'>";
    echo "<TR>";
    echo "<TD ALIGN='CENTER'>";

      echo "<font color='#F2F7FB'>";
      echo "<H1>" . $l_home_welcome  . "</H1>";
      echo "</font>";
      echo '<BR/>';
      echo '<BR/>';
      echo '<BR/>';

      echo "<form name='form1' method='post' action='acp_login.php'>";
      echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
      echo "<THEAD>";
        echo "<TR>";
          echo "<TH align='center' COLSPAN='2' class='thHead'>";
          echo "<font face=verdana size=3><b>" . $l_admin_acp_auth_title . " </B></font></TH>";
        echo "</TR>";
        echo "<TR>";
          echo "<TD align='center' COLSPAN='2' class='row3'>";
            echo "<font face='verdana' size='2'>";
            echo $l_menu_acp_auth;
          echo "</TD>";
        echo "</TR>";
      echo "</THEAD>";
      echo "<TBODY>";
        echo "<TR>";
          echo "<TD align='center' class='row1'>&nbsp;";
            echo "<font face='verdana' size='2'>";
            echo $l_admin_acp_auth_username . "&nbsp;";
          echo "</TD>";
          echo "<TD class='row1'>";
            echo "<input name='acp_login' type='text' id='acp_login' ";
            if ( (_ACP_ALLOW_MEMORY_AUTH != "") and ($cook_acp_login <> '') and ($cook_acp_pass <> '') ) echo "VALUE='" . $cook_acp_login . "' ";
            echo " />";
          echo "</TD>";
        echo "</TR>";
        echo "<TR>";
          echo "<TD class='row1'>&nbsp;";
            echo "<font face='verdana' size='2'>";
            echo $l_admin_acp_auth_password . "&nbsp;";
          echo "</TD>";
          echo "<TD align='center' class='row1'>";
            echo " <input name='acp_pass' type='password' id='acp_pass' ";
            if ( (_ACP_ALLOW_MEMORY_AUTH != "") and ($cook_acp_login <> '') and ($cook_acp_pass <> '') ) echo "VALUE='" . $cook_acp_pass . "' "; 
            echo " />";
          echo "</TD>";
        echo "</TR>";
        if (_ACP_ALLOW_MEMORY_AUTH != "")
        {
          echo "<TR>";
            echo "<TD class='row2' align='center' COLSPAN='2'>&nbsp;";
              echo "<font face='verdana' size='2'>";
              //echo $l_admin_acp_ . "&nbsp;";
            //echo "</TD>";
            //echo "<TD align='center' class='row1'>";
              echo "<font face='verdana' size='2'>";
              echo " <input name='acp_pass_mem' id='acp_pass_mem' type='CHECKBOX' VALUE='1' id='acp_pass' ";
              if ( ($cook_acp_login <> '') and ($cook_acp_pass <> '') ) echo "CHECKED";
              echo " />";
              echo "<label for='acp_pass_mem'>" . $l_admin_remember_me . "</label>";
            echo "</TD>";
          echo "</TR>";
        }
        echo "<TR>";
          echo "<TD align='center' COLSPAN='2' class='catBottom'>";
            echo "<input type='submit' name='Submit' value='" . $l_admin_acp_auth_login . "' class='mainoption' /><br/>";
          echo "</TD>";
        echo "</TR>";

      echo "</TBODY>";
      echo "</TABLE>";
      echo "</form><br/>";

      echo '<BR/>';
      echo '<BR/>';
      echo '<BR/>';
      echo '<BR/>';
      
    echo "</TD>";
    echo "</TR>";
    echo "</TABLE>";
    
    //echo "<BR/>";

  }
}
else
{
  echo "<BR/>";
  echo "<BR/>";
  echo "<div class='warning'>";
  echo $l_admin_htaccess_cannot;
  echo "</div>";
}

?>