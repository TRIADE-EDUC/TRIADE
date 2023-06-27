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
if ( !defined('INTRAMESSENGER') )
{
  exit;
}

// ---------------------------------------------------------- STEP 0 ----------------------------------------------------------
function step_0()
{
  GLOBAL $lang, $l_install_bt_next;
  //
  
  echo "<BR/>";
  echo "<BR/>";
  echo "<BR/>";
  echo "<font size='4' color='white'>";
  if ($lang == "FR")
    echo "Bienvenu dans l'installation du serveur IntraMessenger";
  else
    echo "Welcome to IntraMessenger server setup";
  echo "</font>";

//  echo "<CENTER>";

  echo "<BR/>";
  //echo "<BR/><BR/>";
  echo "<BR/><BR/>";
  echo "<BR/><BR/>";
  if ($lang == "FR")
  {
    echo "<FONT color='#0000FF'>Vous trouverez la documentation pour l'installation en manuel <I>";
    if (is_readable("../doc/fr/install.html")) echo  "<A HREF='../doc/fr/install.html' target='_blank'>";
    echo "../doc/fr/install.html</I></A></FONT>";
  }
  else
  {
    echo "<FONT color='yellow'>You can find doc how to manual install and configure in <I>";
    if (is_readable("../doc/en/install.html")) echo  "<A HREF='../doc/en/install.html' target='_blank'>";
    echo "../doc/en/install.html</I></A></FONT>";
  }
  //
  echo "<BR/><BR/>";
  echo "<BR/><BR/>";
  //echo "<BR/>";
  echo "<BR/>";

  if ($lang == "FR")
    echo "Choisir le language pour l'installation : ";
  else
    echo "Choose installation language: ";
	
  //echo " <A HREF='?lang=EN&' TITLE='English'><IMG SRC='../images/flags/us.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
  //echo " <A HREF='?lang=FR&' TITLE='Français'><IMG SRC='../images/flags/fr.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
  //if ($lang != 'EN') 
  //
  echo "<FORM METHOD='POST' ACTION='install.php?'>";
  //echo "<BR/>";
  echo "<input type='radio' name='lang' id='lang_en' value='EN'";
  if ($lang != "FR") echo "CHECKED ";
  echo " /><label for='lang_en'> <A HREF='?lang=EN&' TITLE='English'><IMG SRC='../images/flags/us.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A> <small>English</small> &nbsp;</label>";
  echo "<BR/>";
  echo "<input type='radio' name='lang' id='lang_fr' value='FR' ";
  if ($lang == "FR") echo "CHECKED ";
  echo "/><label for='lang_fr'> <A HREF='?lang=FR&' TITLE='Français'><IMG SRC='../images/flags/fr.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A> <small>Français</small></label>";

  echo "<BR/>";
  echo "<BR/>";
  echo "<BR/>";
  if (phpversion() == "5.3.0")
  {
    echo "<BR/>";
    echo "<font color='red' size='6'>";
    if ($lang == "FR")
    {
      echo "Impossible d'utiliser avec la <B>version 5.3.0 de PHP</B><BR/>";
      echo "<BR/>";
      echo "Si vous utilisez WampServer 2.0i veuillez télécharger : <a HREF='http://sourceforge.net/projects/wampserver/files/' target='_blank'>http://sourceforge.net/projects/wampserver/files/</A>";
    }
    else
    {
      echo "Cannot use with <B>PHP version 5.3.0</B><BR/>";
      echo "<BR/>";
      echo "If you are using WampServer 2.0i please download: <a HREF='http://sourceforge.net/projects/wampserver/files/' target='_blank'>http://sourceforge.net/projects/wampserver/files/</A>";
    }
  }
  else
  {
    //echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_add . "' class='liteoption' />";
    echo "<INPUT TYPE='submit' VALUE = '" . $l_install_bt_next . "' class='mainoption' />";
    echo "<input type='hidden' name='step' value = '1' />";
    //echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
    echo "</FORM>";
    echo "<BR/>";
  }
}


// ---------------------------------------------------------- STEP 1 ----------------------------------------------------------
function step_1()
{
  GLOBAL $lang, $c_OK, $c_not_found, $c_found, $c_on_ok, $c_on_ko, $c_off_ko, $c_off_ok, $steps;
  GLOBAL $l_install_bt_next, $l_install_step;
  //
  $if_prob = "OK";
  //
  echo "<font face='verdana' size='5' color='white'>";
  echo $l_install_step . " 1 : " . $steps[1] . " <BR/>";
  echo "</font>";
  
  if ($lang == "FR")
    table_title("Pour utiliser ce logiciel, vous devez d'abord lire et accepter la licence GPL");
  else
    table_title("To use this software, you must first understand and accept the GPL licence");
  //
	echo "<TR>";
	echo "<TD width='' class='row2'>";
	echo "<FONT size='2'>";
	$licence = "http://opensource.org/licenses/gpl-license.php";
	if (file_exists('licence.txt'))
	{
    $licence = nl2br(utf8_encode(file_get_contents('licence.txt')));
  }
  echo "<div style='overflow:auto;z-index:-10;height:300px;background:#FFF;'>";
  //include("licence.txt");
  echo $licence;
  echo "</div>";
	echo "</TD>";
  table_col_vide();
  //
  //
  echo "</TABLE>";
  //
  echo "<BR/>";
  echo "<FORM METHOD='POST' ACTION='install.php?'>";
  //echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_add . "' class='liteoption' />";
  echo "<INPUT TYPE='checkbox' name='lic' id='lic' value = 'ok' /> ";
  echo "<label for='lic'>";
  if ($lang == "FR")
    echo "J'ai bien lu la licence et j'en accepte les termes<BR/>";
  else
    echo "I have read and I accept the licence terms<BR/>"; // / I agree to the License Agreement
  echo "</label>";
  echo "<BR/>";
  echo "<INPUT TYPE='submit' VALUE = '" . $l_install_bt_next . "' class='liteoption' />"; // class='mainoption'
  echo "<input type='hidden' name='step' value = '2' />";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
  echo "</FORM>";
  echo "<BR/>";
}



// ---------------------------------------------------------- STEP 2 ----------------------------------------------------------
function step_2()
{
  GLOBAL $lang, $c_OK, $c_not_found, $c_found, $c_on_ok, $c_on_ko, $c_off_ko, $c_off_ok, $lic, $steps;
  GLOBAL $l_install_bt_next, $l_install_step, $l_admin_check_folders, $l_admin_check_folder, $l_admin_check_system_info, $l_admin_check_not_writeable;
  GLOBAL $l_admin_check_not_found, $l_install_check_cannot_continue;
  //
  $if_prob = "OK";
  //
  echo "<font face='verdana' size='5' color='white'>";
  echo $l_install_step . " 2 : " . $steps[2] . "<BR/>";
  echo "</font>";
  
  table_title($l_admin_check_folders);
  //
  //
  $txt = "";  // '../common/config/extern/',
  $arrFolders = array('../common/', '../common/extern/', '../common/config/', '../common/library/', '../common/lang/', '../distant/', '../distant/log/', '../distant/avatar/', '../distant/include/', '../images/', '../common/styles/default/images/','../public/log/', '../public/upload/', );
  foreach ($arrFolders as $folder) 
  {
    if (is_dir($folder)) 
    {
      if (!is_readable($folder)) 
      {
        $txt .= $l_admin_check_folder . " <I>" . $folder . "</I> : ";
        $txt .= "<FONT COLOR='RED'><B> " . $l_admin_check_not_found . " !</B></FONT><BR/>";
      }
    } 
    else 
    {
      $txt .= $l_admin_check_folder . " <I>" . $folder . "</I> : ";
      $txt .= "<FONT COLOR='RED'><B> " . $l_admin_check_not_found . " !</B></FONT><BR/>";
    }
  }
  //
  $arrFolders = array('../distant/log/', '../distant/avatar/', '../public/log/', '../public/rss/','../public/upload/', '../admin/save/', '../common/library/sypex_dumper/sxd/backup/');
  foreach ($arrFolders as $folder) 
  {
    if (is_dir($folder)) 
    {
      if (!is_writeable($folder)) 
      {
        $txt .= $l_admin_check_folder . " <I>" . $folder . "</I> : ";
        $txt .= "<FONT COLOR='RED'><B> " . $l_admin_check_not_writeable . " !</B></FONT> (<B>chmod</B>)<BR/>";
      }
    } 
  }
  //
  if ($txt != "")
    $if_prob = "KO";
  else
  {
    if ($lang == "FR")
      $txt = "Tous les répertoires sont " . $c_OK;
    else
      $txt = "All folders are " . $c_OK;
  }
  //
  table_col_1($txt);
  table_col_2($if_prob);
  //
  echo "</TABLE>";
  echo "\n";
  //
  if ($if_prob != "OK") 
  {
    echo "<div class='warning'><p class='error'>" . $l_install_check_cannot_continue;
    if ($lang == "FR")
       echo " l'ensemble des répertoires</p></div>";
    else
      echo " all folders</p></div>";
    die();
  }
  
  
  table_title($l_admin_check_system_info);
  //
  //
  $txt = "Server Software : <I>" . $_SERVER["SERVER_SOFTWARE"] . "</I><BR/>";
  $txt .= "PHP Version : <I>" . phpversion() . "</I><BR/>";

  $txt .= "Register Globals : <I>";
  $txt .= ini_get("register_globals") == 1 ? $c_on_ko : $c_off_ok;
  $txt .= "</I><BR/>";

  $txt .= "Display errors : <I>";
  $txt .= ini_get("display_errors") == 1 ? $c_on_ko : $c_off_ok;
  if ($lang == "FR")
    $txt .= "</I> (laisser sur <I>on</I> seulement pour les serveurs de tests)<BR/>";
  else
    $txt .= "</I> (keep <I>on</I> only on test server)<BR/>";

  $txt .= "Log errors : <I>";
  $txt .= ini_get("log_errors") == 1 ? "on" : "off";
  $txt .= "</I><BR/>";

  $txt .= "Safe Mode : <I>";
  $txt .= ini_get("safe_mode") == 1 ? "on" : "off";
  $txt .= "</I><BR/>";

  $txt .= "Open Basedir : <I>";
  $txt .= ini_get("open_basedir") != '' ? "on" : "off";
  $txt .= "</I><BR/>";

  $txt .= "Memory limit : <I>";
  $txt .= ini_get("file_uploads") != '' ? $c_on_ok : $c_off_ko;
  $txt .= "</I><BR/>";

  $txt .= "File upload : <I>";
  $txt .= ini_get("file_uploads") == 1 ? $c_on_ok : $c_off_ko;
  if ($lang == "FR")
    $txt .= "</I> (doit être <I><B>activé</B></I> pour permettre aux utilisateurs de proposer des avatars)<BR/>";
  else
    $txt .= "</I> (must be <I><B>on</B></I> to allow user to propose (upload) avatars)<BR/>"; // to <I>/public/upload/</I> folder

  $txt .= "Allow url fopen : <I>";
  $txt .= ini_get("allow_url_fopen") == 1 ? $c_on_ok : $c_off_ko;
  $txt .= "</I>";
  if ($lang == "FR")
    $txt .= " (doit être <I><B>activé</B></I> pour s'incrire à <A HREF='http://www.intramessenger.net/list/servers/' target='_blank'>l'annuaire internet serveurs publiques</A>)<BR/>";
  else
    $txt .= " (must be <I><B>on</B></I> to register on <A HREF='http://www.intramessenger.net/list/servers/' target='_blank'>internet public servers directory</A>)<BR/>";
  //
  table_col_1($txt);
  table_col_vide();
  //
  //
  echo "</TABLE>";
  //
  if (ini_get("disable_functions") != "") 
  {
    if ($lang == "FR")
      echo "<BR/>" . "Fonctions désactivées : " . ini_get("disable_functions") . "<BR/>";
    else
      echo "<BR/>" . "Disabled functions: " . ini_get("disable_functions") . "<BR/>";
  }


  if ($lang == "FR")
    table_title("Liste des langues installées");
  else
    table_title("Languages list");
  //
  //
  $l_install_bt_next_orig = $l_install_bt_next;
  //
  $txt = "";
  $if_prob_lang = "OK";
  $one_or_more = false;
  $noDir = "no folder";
  $rep = opendir('../common/lang/');
  while ($file = readdir($rep))
  {
    if ( ($file != "..") && ($file != ".") && ($file != "") && (strpos(strtolower($file), ".inc.php")) ) // .inc.php
    {
      if (!is_dir($file))
      {
        unset($l_lang_name);
        unset($charset);
        //
        include ("../common/lang/" . $file);
        if ( isset($l_lang_name) and isset($charset) )
        {
          $one_or_more = true;
          $txt .= "<B>" . $l_lang_name . " </B> (" . $charset . ") : ";
        }
        $txt .= $file . "<BR/>";
      }
    }
  }
  closedir($rep);
  if ($one_or_more == false) 
    $if_prob_lang = "KO";
  //
  table_col_1($txt);
  table_col_2($if_prob_lang);
  echo "<TR><TD COLSPAN='2' class='catBottom'>";
  echo "<FONT size='2'>To add more language (or just update), please read <I>";
  if (is_readable("../common/lang/translate.txt")) 
    echo "<A HREF='../common/lang/translate.txt' target='blank'>";
  //
  echo "../common/lang/translate.txt</I></A></FONT>";
  echo "</TD></TR>";
  //
  //
  echo "</TABLE>";
  //
  //
  if ($if_prob = "OK") 
  {
    $l_install_bt_next = $l_install_bt_next_orig;
    echo "<BR/>";
    echo "<FORM METHOD='POST' ACTION='install.php?'>";
    //echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_add . "' class='liteoption' />";
    echo "<INPUT TYPE='submit' VALUE = '" . $l_install_bt_next . "' class='liteoption' />";
    echo "<input type='hidden' name='lic' value = '" . $lic . "' />";
    echo "<input type='hidden' name='step' value = '3' />";
    echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
    echo "</FORM>";
    echo "<BR/>";
  }
}




// ---------------------------------------------------------- STEP 3 ----------------------------------------------------------
function step_3()
{
  GLOBAL $lang, $c_OK, $c_not_found, $c_found, $c_on_ok, $c_on_ko, $c_off_ko, $c_off_ok, $lic, $steps;
  GLOBAL $l_install_bt_next, $l_install_step, $step_3_ok;
  //
  $if_prob = "OK";
  //
  echo "<font face='verdana' size='5' color='white'>";
  echo $l_install_step . " 3 : " . $steps[3] . "<BR/>";
  echo "</font>";
  
  if ($lang == "FR")
    table_title("Choix du type d'installation (application tierce ou non)");
  else
    table_title("Choose installation type (3rd-Party Extension or not)");

    echo "<FORM METHOD='POST' ACTION='install.php?' name='formauth'>";
  	echo "<TR>";
    echo "<TD class='row1' align='left'>"; // width='' 
    echo "<FONT size='2'>";
    echo "<br/>";
    echo "<INPUT name='auth_mode' TYPE='radio' VALUE='1' class='genmed' id='auth_mode_1' onClick='select_auth_mode()' ";
    if ($step_3_ok == "")  echo "CHECKED";
    echo " ><label for='auth_mode_1'>";
    if ($lang == "FR")
      echo "<strong>Autonome</strong> <small>(ou configurer l'authentification externe plus tard)</small>";
    else
      echo "<strong>Standalone</strong> <small>(or setup extern authentication later)</small>";
    echo "</label></INPUT>";
    echo "<br/>";
    echo "<br/>";
    echo "</TD>";
  	echo "</TR>";

  	echo "<TR>";
    echo "<TD class='row2' align='left'>"; // width='' 
      echo "<FONT size='2'>";
      echo "<br/>";
      echo "<INPUT name='auth_mode' TYPE='radio' VALUE='2' class='genmed' id='auth_mode_2' onClick='select_auth_mode()' ";
      if ($step_3_ok != "")  echo "CHECKED";
      echo " ><label for='auth_mode_2'>";
      if ($lang == "FR")
        echo "Extension/mod/addon/plugin (<A HREF='http://www.intramessenger.net/exter_auth.php?lang=FR&' target='_blank'>authentification externe</A>) de l'application suivante :";
      else
        echo "Mod/addon/plugin (<A HREF='http://www.intramessenger.net/exter_auth.php?lang=EN&' target='_blank'>external authentication</A>) to the following application:";
      //
      echo "</label></INPUT>";

      //echo "<br/>";
      //echo "<br/>&nbsp; &nbsp; ";
      require ("../common/extern/extern.inc.php");
      prevent_error_extern_option_missing();
      $extern_auth_list = array();
      $extern_auth_list = f_extern_auth_list();
      echo " <select name='external_auth' onChange='select_external_auth()' > ";
        echo "<option value='' class='genmed'></option>";
        foreach ($extern_auth_list as $name) 
        {
          echo "<option value='" . $name . "' class='genmed' >" . $name . "</option>" ;
        }
      echo "</select> (forum/blog/CMS/...) ";
      //
      echo "<div id='id_auth_mode_2' style='display:none'>";
        echo "<br/>";
        echo "<br/>&nbsp; &nbsp;";
        if ($lang == "FR")
        {
          $q = "Où se trouve IntraMessenger par rapport à votre application :";
          $q1 = "Dans votre application :";
          $q2 = "Au même niveau que votre application :";
          $t1 = "http://votre-serveur/<font color='blue'>votre-application</font>/<font color='green'>intramessenger</font>/";
          $t2 = "http://votre-serveur/<font color='green'>intramessenger</font>/";
          //$t3 = "http://votre-serveur/<font color='blue'>votre-application</font>/";
          $t3 = "http://votre-serveur/";
          $t4 = "votre-application";
        }
        else
        {
          $q = "Where is IntraMessenger in relation to your application:";
          $q1 = "Inside your application";
          $q2 = "Same level your application:";
          $t1 = "http://your-server/<font color='blue'>your-application</font>/<font color='green'>intramessenger</font>/";
          $t2 = "http://your-server/<font color='green'>intramessenger</font>/";
          $t3 = "http://your-server/";
          $t4 = "your-application";
        }
        
        echo $q . "<br/>&nbsp; &nbsp;";
        echo "<INPUT name='external_path' TYPE='radio' VALUE='1' id='chemin_external_auth_1' class='genmed'><label for='chemin_external_auth_1'>" . $q1 . "</INPUT> <i>"; // . $t1 . "</i>";
        echo "<br/>&nbsp; &nbsp; &nbsp; &nbsp;" . $t1 . "</i></label>";
        echo "<br/>&nbsp; &nbsp;";
        echo "<INPUT name='external_path' TYPE='radio' VALUE='2' id='chemin_external_auth_2' class='genmed'><label for='chemin_external_auth_2'>" . $q2 . "</INPUT> <i>";
        echo "<br/>&nbsp; &nbsp; &nbsp; &nbsp;" . $t2;
        //echo "<br/>&nbsp; &nbsp; &nbsp; &nbsp;" . $t3;
        echo "<br/>&nbsp; &nbsp; &nbsp; &nbsp;" . $t3 . "</label><input type='text' name='external_path_value' value='" . $t4 . "' size='20' class='post' />/";
        echo "</i>";
      echo "</div>";
      echo "<br/>";
      echo "<br/>";
    echo "</TD>";
  	echo "</TR>";
  	
  echo "</TABLE>";

  echo "<br/>";
  echo "<INPUT TYPE='submit' VALUE = '" . $l_install_bt_next . "' class='liteoption' />";
  echo "<input type='hidden' name='lic' value = '" . $lic . "' />";
  echo "<input type='hidden' name='step' value = '4' />";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
  echo "</FORM>";
  echo "<br/>";
  echo "<br/>";

}



// ---------------------------------------------------------- STEP 4 ----------------------------------------------------------
function step_4()
{
  GLOBAL $lang, $c_OK, $c_not_found, $c_found, $c_on_ok, $c_on_ko, $c_off_ko, $c_off_ok, $lic, $steps;
  GLOBAL $step_4_ok;
  GLOBAL $l_install_bt_next, $l_install_step, $l_admin_check_not_writeable, $l_admin_check_not_found, $l_install_check_cannot_continue;
  GLOBAL $l_install_check_cannot_continue;
  GLOBAL $auth_mode, $external_auth, $external_path;
  if ( ($auth_mode == 2) and ($external_auth != "") ) 
    GLOBAL $dbhost, $database, $dbuname, $dbpass;
  else
    $external_auth = "";
  //
  if ($external_path == "KO") $auth_mode = 1; // Echec tentative assistant ext aut. On repasse à 1 pour inclure le fichier de config mysql par défaut, tout en conservant le nom de l'authentification externe sélectionnée.
  //
  $if_prob = "OK";
  $PREFIX_IM_TABLE = "IM_";
  //
  //
  echo "<font face='verdana' size='5' color='white'>";
  echo $l_install_step . " 4 : " . $steps[4] . "<BR/>";
  echo "</font>";
  
  if ($lang == "FR")
    table_title("Fichier de configuration de la base de données");
  else
    table_title("Database configuration file");
  //
  if (!is_readable("../common/config/mysql.config.inc.php")) 
    $if_prob = "KO";
  //
  $txt = "<I>/common/config/mysql.config.inc.php</I> : ";
  if ($if_prob == "OK") 
  {
    if (!is_writeable("../common/config/mysql.config.inc.php"))
      $if_prob = "KO";
    //
    if ($if_prob == "OK") 
      $txt .= $c_OK;
    else
      $txt .= "<FONT COLOR='RED'><B> " . $l_admin_check_not_writeable . " ! </font></B>(<B>chmod</B>)";
  }
  else
    $txt .= "<FONT COLOR='RED'><B> " . $l_admin_check_not_found . " !";
  //
  table_col_1($txt);
  table_col_2($if_prob);

  echo "</TABLE>";
  //
  if ($if_prob != "OK") 
  {
    echo "<div class='warning'><p class='error'>" . $l_install_check_cannot_continue;
    if ($lang == "FR")
      echo " droits d'écriture sur le fichier de configuration de la base de données</p></div>";
    else
      echo " write access to database configuration file</p></div>";
    //die();
  }
  else
  {
    if ($step_4_ok == "KO")
    {
      if ($lang == "FR")
        echo "<div class='warning'><p class='error'>Paramètres manquants, merci d'essayez à nouveau !</p></div>";
      else
        echo '<div class="warning"><p class="error">Parameters missing, please do it again !</p></div>';
    }
    
    echo "<BR/>";
    if ($lang == "FR")
      table_title("Configuration de la base de données");
    else
      table_title("Database configuration");
    //
    if ( ($auth_mode == 1) or ($external_auth == "") )  require("../common/config/mysql.config.inc.php");
    echo "<FORM METHOD='POST' ACTION='install.php?'>";
  	echo "<TR>";
    echo "<TD width='' class='row2'>";
    echo "<FONT size='2'>";
    if ($lang == "FR")
      echo "Nom d'hôte du serveur MySQL (exemple : <I>localhost</I>)";
    else
      echo "MySQL host (example: <I>localhost</I>)";
    echo "</TD>";
    echo "<TD width='' class='row1'>";
    echo "<FONT size='2'>";
    echo "<input type='text' name='dbhost' value='" . $dbhost . "' size='20' class='post' />";
    echo "</TD>";
  	echo "</TR>";

  	echo "<TR>";
    echo "<TD width='' class='row2'>";
    echo "<FONT size='2'>";
    if ($lang == "FR")
      echo "Nom d'utilisateur MySQL (exemple : <I>root</I>)";
    else
      echo "MySQL username (example: <I>root</I>)";
    echo "</TD>";
    echo "<TD width='' class='row1'>";
    echo "<FONT size='2'>";
    echo "<input type='text' name='dbuname' value='" . $dbuname . "' size='20' class='post' />";
    echo "</TD>";
  	echo "</TR>";

  	echo "<TR>";
    echo "<TD width='' class='row2'>";
    echo "<FONT size='2'>";
    if ($lang == "FR")
      echo "Mot de passe de l'utilisateur";
    else
      echo "MySQL password";
    echo "</TD>";
    echo "<TD width='' class='row1'>";
    echo "<FONT size='2'>";
    echo "<input type='password' name='dbpass' value='" . $dbpass . "' size='20' class='post' />";
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
    echo "<input type='password' name='dbpass2' value='" . $dbpass . "' size='20' class='post' />";
    echo "</TD>";
  	echo "</TR>";

  	echo "<TR>";
    echo "<TD width='' class='row2'>";
    echo "<FONT size='2'>";
    if ($lang == "FR")
      echo "Nom de la base de données MySQL (exemple : <I>intramessenger</I>)";
    else
      echo "MySQL database (example: <I>intramessenger</I>)";
    echo "</TD>";
    echo "<TD width='' class='row1'>";
    echo "<FONT size='2'>";
    echo "<input type='text' name='database' value='" . $database . "' size='20' class='post' />";
    echo "</TD>";
  	echo "</TR>";

  	echo "<TR>";
    echo "<TD width='' class='row2'>";
    echo "<FONT size='2'>";
    if ($lang == "FR")
      echo "Préfixes des tables (exemple : <I>IM_</I>)";
    else
      echo "Tables prefix (example: <I>IM_</I>)";
    echo "</TD>";
    echo "<TD width='' class='row1'>";
    echo "<FONT size='2'>";
    echo "<input type='text' name='prefix' value='" . $PREFIX_IM_TABLE . "' size='5' class='post' />";
    echo "</TD>";
  	echo "</TR>";
  
  	echo "<TR>";
    echo "<TD width='' class='catBottom' COLSPAN='2' ALIGN='center'>";
    echo "<FONT size='2' color='#FF8800'>";
    if ($lang == "FR")
      echo "Attention au paramètrage de CHARSET et COLLATION <u>si</u> les utilisateurs utilisent des langues non occidentales.";
    else
      echo "Beware of CHARSET and COLLATION parameters setting <u>if</u> users use non-Western languages.";
    echo "</TD>";
  	echo "</TR>";
    //
    echo "</TABLE>";
  }
  //
  //echo "</TABLE>";
  //
  if ($if_prob == "OK") 
  {
    echo "<BR/>";
    //echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_add . "' class='liteoption' />";
    echo "<INPUT TYPE='submit' VALUE = '" . $l_install_bt_next . "' class='liteoption' />";
    echo "<input type='hidden' name='lic' value = '" . $lic . "' />";
    echo "<input type='hidden' name='external_auth' value = '" . $external_auth . "' />";
    echo "<input type='hidden' name='step' value = '5' />";
    echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
    echo "</FORM>";
    echo "<BR/>";
    echo "<BR/>";
  }

}






// ---------------------------------------------------------- STEP 5 ----------------------------------------------------------
function step_5()
{
  GLOBAL $lang, $c_OK, $c_not_found, $c_found, $c_on_ok, $c_on_ko, $c_off_ko, $c_off_ok, $lic, $external_auth, $steps;
  GLOBAL $l_install_bt_next, $l_install_step, $l_admin_check_mysql, $l_admin_check_not_found, $l_admin_check_connect_server;
  GLOBAL $l_admin_check_connect_database, $l_admin_check_version, $l_install_check_cannot_continue, $l_admin_check_failed;
  GLOBAL $l_admin_check_connect_to_server;
  //
  $if_prob = "OK";
  //
  echo "<font face='verdana' size='5' color='white'>";
  echo $l_install_step . " 5 : " . $steps[5] . "<BR/>";
  echo "</font>";
  
  if ($lang == "FR")
    table_title("Vérification de la configuration MySQL ");
  else
    table_title("Check MySQL setup");
  //
  if (!is_readable("../common/config/mysql.config.inc.php")) 
    $if_prob = "KO";
  //
  if ($lang == "FR")
    $txt = "Fichier de configuration de la base de données (<I>/common/config/mysql.config.inc.php</I>) : ";
  else
    $txt = "Database configuration file (<I>/common/config/mysql.config.inc.php</I>): ";
    
  if ($if_prob == "OK") 
    $txt .= $c_OK;
  else
    $txt .= "<FONT COLOR='RED'><B> " . $l_admin_check_not_found . " !";
  //
  table_col_1($txt);
  table_col_2($if_prob);
  //
  if ($if_prob != "OK") 
  {
    echo "</TABLE><div class='warning'><p class='error'>" . $l_install_check_cannot_continue;
    if ($lang == "FR")
      echo " droits de lecture sur le fichier de configuration de la base de données</p></div>";
    else
      echo " read access to database configuration file</p></div>";
    die();
  }
  //
  //
  require ("../common/config/mysql.config.inc.php");
  if ( isset($database) )
  {
    if ($database == "")
      $if_prob = "KO";
  }
  else
    $if_prob = "KO";
  //
  if ($lang == "FR")
    $txt = "Base de données : ";
  else
    $txt = "Database: ";
  if ($if_prob == "OK") 
    $txt .= "<FONT COLOR='GREEN'><B><I>" . $database . "</B>";
  else
    $txt .= "<FONT COLOR='RED'><B>" . $l_admin_check_not_found . " !";
  //
  table_col_1($txt);
  table_col_2($if_prob);
  //
  if ($if_prob != "OK") 
  {
    echo "</TABLE><div class='warning'><p class='error'>" . $l_install_check_cannot_continue . " database</p></div>";
    echo "</body></html>";
    die();
  }
  //
  //
  if ( isset($dbhost) and isset($dbuname) )
  {
    if ( ($dbhost == "") or ($dbuname == "") )
      $if_prob = "KO";
  }
  else
    $if_prob = "KO";
  //
  if ($lang == "FR")
    $txt = "Nom d'utilisateur @ nom d'hôte : ";
  else
    $txt = "Database username @ hostname: ";
  if ($if_prob == "OK") 
    $txt .= "<FONT COLOR='GREEN'><B><I>" . $dbuname . " </B>@<B> " . $dbhost;
  else
    $txt .= "<FONT COLOR='RED'><B>not found !" . $dbuname . " </B>@<B> " . $dbhost;
  //
  table_col_1($txt);
  table_col_2($if_prob);
  //
  if ($if_prob == "OK") 
  {
    if (!isset($PREFIX_IM_TABLE))
    {
      $txt = f_add_file_missing("PREFIX_IM_TABLE", "01/03/2008");
      table_col_1($txt);
      table_col_2("KO");
      echo "<TR><TD COLSPAN='2' class='catBottom'>";
      echo "<FONT size='2' color='BLUE'>Option missing in file <I>../common/config/mysql.config.inc.php</I>";
      echo "</TD></TR>";
    }
  }
  //
  echo "</TABLE>";
  if ($if_prob != "OK") 
  {
    echo "<div class='warning'><p class='error'>" . $l_install_check_cannot_continue . " server config</p></div>";
    die();
  }
  //
  //
  //error_reporting(0);
  error_reporting(E_ALL);
  table_title($l_admin_check_mysql);
  //$id_connect = mysql_connect($dbhost, $dbuname, $dbpass) or $if_prob = "KO";
  $id_connect = mysqli_connect($dbhost, $dbuname, $dbpass) or $if_prob = "KO";
  //die("Unable to connect to MySQL server : " . mysql_error());
  //
  $txt = $l_admin_check_connect_server . " : ";
  if ($if_prob == "OK") 
    $txt .= "<FONT COLOR='GREEN'><B><I>" . $c_OK . "</B>";
  else
  {
    if ($lang == "FR")
    {
      $txt .= "<FONT COLOR='RED'>Echec de connexion au serveur MySQL : <I>" . mysqli_connect_error() . "</I>";
      $txt .= "<BR/><B>Vérifier : nom d'hôte, nom d'utilisateur et mot de passe </B> (et essayer à nouveau)";
    }
    else
    {
      $txt .= "<FONT COLOR='RED'>Unable to connect to MySQL server: <I>" . mysqli_error($id_connect) . "</I>";
      $txt .= "<BR/><B>Verify: hostname, login and password </B> (and try again)";
    }
  }
  //
  table_col_1($txt);
  table_col_2($if_prob);
  //
  if ($if_prob == "OK") 
  {
    //mysql_select_db($database, $id_connect) or mysqli_query($id_connect, " CREATE DATABASE IF NOT EXISTS `" . $database . "` ");
    mysqli_select_db($id_connect, $database) or mysqli_query($id_connect, " CREATE DATABASE IF NOT EXISTS `" . $database . "` ");
    //
    //mysql_select_db($database, $id_connect) or $if_prob = "KO";
    mysqli_select_db($id_connect, $database) or $if_prob = "KO";
    //die("Unable to select database : " . mysql_error());
    $txt = $l_admin_check_connect_database . " : ";
    if ($if_prob == "OK") 
      $txt .= "<FONT COLOR='GREEN'><B><I>" . $c_OK . "</B>";
    else
    {
      if ($lang == "FR")
        $txt .= "<FONT COLOR='RED'><B>Echec d'accès à la base de données : " . mysqli_error($id_connect);
      else
        $txt .= "<FONT COLOR='RED'><B>Unable to select database: " . mysqli_error($id_connect);
    }
    //
    unset($dbpass);
    //
    table_col_1($txt);
    table_col_2($if_prob);
  }
  //
  if ($if_prob == "OK") 
  {
    $requete = "SELECT VERSION()";
    $result = mysqli_query($id_connect, $requete);
    $txt = $l_admin_check_version . " : ";
    if (!$result) 
    {
      $if_prob = "KO";
      $txt .= "<FONT COLOR='RED'><B>" . $l_admin_check_failed . "</B> : " . mysqli_error($id_connect);
    }
    else
    {
      if ( mysqli_num_rows($result) == 1 )
      {
        list ($version) = mysqli_fetch_row ($result);
        $txt .= "<FONT COLOR='GREEN'><I>" . $version . "</I></FONT>";
      }
      else
      {
        $if_prob = "KO";
        $txt .= "<FONT COLOR='RED'><B>" . $l_admin_check_failed . "</B> : " . mysqli_error($id_connect);
      }
    }
    table_col_1($txt);
    table_col_2($if_prob);
    //mysql_close();  // enlevé car bug de PHP 5.3.0 (ex: WampServer 2.0i).
  }
  //
  echo "</TABLE>";
  //
  if ($if_prob != "OK") 
  {
    echo "<div class='warning'><p class='error'>" . $l_install_check_cannot_continue . " " . $l_admin_check_connect_to_server . "</p></div>"; // 
  }
  else
  {
    //
    echo "<FORM METHOD='POST' ACTION='install.php?'>";
    //
    if ($lang == "FR")
      table_title("Choix du moteur de base de données");
    else
      table_title("Select database engine");
    //
    echo "<TR>";
    echo "<TD class='row1'><FONT size='2'>";
    echo "<input type='radio' name='dbengine' CHECKED value='zZ' /><B>Default</B> <BR/>";
    echo "<input type='radio' name='dbengine' value='myisam' />MyISAM <BR/>";
    echo "<input type='radio' name='dbengine' value='innodb' />InnoDB <BR/>";
    echo "</>";
    echo "</TD>";
    echo "</TR>";
    echo "</TABLE>";
    //
    echo "<BR/>";
    //echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_add . "' class='liteoption' />";
    echo "<INPUT TYPE='submit' VALUE = '" . $l_install_bt_next . "' class='liteoption' />";
    echo "<input type='hidden' name='lic' value = '" . $lic . "' />";
    echo "<input type='hidden' name='external_auth' value = '" . $external_auth . "' />";
    echo "<input type='hidden' name='step' value = '6' />";
    echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
    echo "</FORM>";
    echo "<BR/>";
  }
}






// ---------------------------------------------------------- STEP 6 ----------------------------------------------------------
function step_6()
{
  GLOBAL $lang, $c_OK, $c_not_found, $c_found, $c_on_ok, $c_on_ko, $c_off_ko, $c_off_ok, $lic, $external_auth, $steps;
  GLOBAL $l_install_bt_next, $l_install_step, $l_admin_check_tables_list, $l_admin_check_table, $l_install_check_cannot_continue;
  GLOBAL $dbengine, $id_connect;
  //
  $if_prob = "OK";
  //
  echo "<font face='verdana' size='5' color='white'>";
  echo $l_install_step . " 6 : " . $steps[6] . "<BR/>";
  echo "</font>";
  //
  require ("../common/sql.inc.php");  
  table_title($l_admin_check_tables_list);

  $txt = "";
  $arrTableInit = array("#" . $PREFIX_IM_TABLE . "CNT_CONTACT#", "#" . $PREFIX_IM_TABLE . "MSG_MESSAGE#", "#" . $PREFIX_IM_TABLE . "SES_SESSION#", 
                        "#" . $PREFIX_IM_TABLE . "USR_USER#", "#" . $PREFIX_IM_TABLE . "USG_USERGRP#", "#" . $PREFIX_IM_TABLE . "GRP_GROUP#", 
                        "#" . $PREFIX_IM_TABLE . "STA_STATS#", "#" . $PREFIX_IM_TABLE . "CNF_CONFERENCE#", "#" . $PREFIX_IM_TABLE . "USC_USERCONF#", 
                        "#" . $PREFIX_IM_TABLE . "BAN_BANNED#", "#" . $PREFIX_IM_TABLE . "SRV_SERVERSTATE#", "#" . $PREFIX_IM_TABLE . "SBX_SHOUTBOX#", 
                        "#" . $PREFIX_IM_TABLE . "SBS_SHOUTSTATS#", "#" . $PREFIX_IM_TABLE . "SBV_SHOUTVOTE#",
                        "#" . $PREFIX_IM_TABLE . "BMC_BOOKMCATEG#", "#" . $PREFIX_IM_TABLE . "BMK_BOOKMARK#", "#" . $PREFIX_IM_TABLE . "BMV_BOOKMVOTE#",
                        "#" . $PREFIX_IM_TABLE . "ROL_ROLE#", "#" . $PREFIX_IM_TABLE . "MDL_MODULE#", "#" . $PREFIX_IM_TABLE . "RLM_ROLEMODULE#",
                        "#" . $PREFIX_IM_TABLE . "FMD_FILEMEDIA#", "#" . $PREFIX_IM_TABLE . "FPJ_FILEPROJET#", "#" . $PREFIX_IM_TABLE . "FIL_FILE#", 
                        "#" . $PREFIX_IM_TABLE . "FLV_FILEVOTE#", "#" . $PREFIX_IM_TABLE . "FST_FILESTATS#", "#" . $PREFIX_IM_TABLE . "FSD_FILESTATSDOWNLOAD#",
                        "#" . $PREFIX_IM_TABLE . "ADM_ADMINACP#", "#" . $PREFIX_IM_TABLE . "FIB_FILEBACKUP#");  
                        //   ,"#" . $PREFIX_IM_TABLE . "_______#"
  //
  //$requete = "SHOW TABLES";
  $requete = "SHOW TABLES LIKE '" . $PREFIX_IM_TABLE . "%' " ; // auth extern : peut être déjà des tables !
  $result = mysqli_query($id_connect, $requete);
  if (!$result) 
    $txt = '<span class="error">cannot retreive tables list</span></li>';
  else
    if ( mysqli_num_rows($result) == 0 )
    {
      require ("../common/create_tables.inc.php");
      //
      //
      //$requete = "SHOW TABLES";
      $requete = "SHOW TABLES LIKE '" . $PREFIX_IM_TABLE . "%' " ;
      $result = mysqli_query($id_connect, $requete);
    }
  //
  $table_exists = "##"; // pas vide ! (2 sinon, trouve pas la 1ère table
  if ( mysqli_num_rows($result) > 0 )
  {
    while ( list ($table1) = mysqli_fetch_row ($result) )
    {
      $table_exists .= $table1 . "#";
    }
  }
  foreach ($arrTableInit as $table) 
  {
    $table_aff = str_replace("#", "", $table); // enlever les #
    $txt .= $l_admin_check_table . " <I>" . $table_aff . "</I> : ";
    if ( strstr(strtolower($table_exists), strtolower($table)) )
      $txt .= $c_OK;
    else
    {
      $txt .= $c_not_found;
      $if_prob = "KO";
    }
    $txt .= '<BR/>';
  }
  if ($txt == "") $txt = "All tables exist";
  table_col_1($txt);
  table_col_2($if_prob);
  if ($if_prob != "OK")
  {
    //echo "<TR><TD COLSPAN='2' class='catBottom'>";
    echo "<TR><TD COLSPAN='2' class='row1'>";
    echo "<FONT color='blue' size='2'><B>";
    if ($lang == "FR")
    {
      echo "Essayer maintenant : <A HREF='create_tables.php'>création des tables</A><BR/>";
      echo "Si ne fonctionne pas, utiliser <I>";
    }
    else
    {
      echo "Try now: <A HREF='create_tables.php'>create tables</A><BR/>";
      echo "If not work use <I>";
    }
    if (is_readable("../install/install.sql")) 
      echo "<A HREF='../install/install.sql' target='blank'>";
    //
    if ($lang == "FR")
      echo "../install/install.sql</I></A> dans l'interface d'administration de MySQL (ex: PHPMyAdmin).</FONT>"; // to create tables
    else
      echo "../install/install.sql</I></A> in MySQL admin (e.g. PHPMyAdmin).</FONT>"; // to create tables
    echo "</TD>";
    echo "</TR>";
  }


  echo "</TABLE>";
  //
  if ($if_prob != "OK") 
  {
    echo "<div class='warning'><p class='error'>" . $l_install_check_cannot_continue . " table structure</p></div>";
  }
  else
  {
    echo "<BR/>";
    echo "<FORM METHOD='POST' ACTION='install.php?'>";
    echo "<INPUT TYPE='submit' VALUE = '" . $l_install_bt_next . "' class='liteoption' />";
    echo "<input type='hidden' name='lic' value = '" . $lic . "' />";
    echo "<input type='hidden' name='external_auth' value = '" . $external_auth . "' />";
    echo "<input type='hidden' name='step' value = '7' />";
    echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
    echo "</FORM>";
    echo "<BR/>";
  }
}








// ---------------------------------------------------------- STEP 7 ----------------------------------------------------------
function step_7()
{
  GLOBAL $lang, $c_OK, $c_not_found, $c_found, $c_on_ok, $c_on_ko, $c_off_ko, $c_off_ok, $lic, $external_auth, $steps;
  GLOBAL $l_install_bt_next, $l_install_step, $l_admin_check_conf_file, $l_admin_check_not_found, $l_install_check_cannot_continue;
  GLOBAL $l_admin_check_missing_option, $l_admin_check_in_conf_file;

  //
  $if_prob = "OK";
  //
  echo "<font face='verdana' size='5' color='white'>";
  echo $l_install_step . " 7 : " . $steps[7] . "<BR/>";
  echo "</font>";
  if ($lang == "FR")
    table_title("Vérification de la configuration");
  else
    table_title("Check configuration");
    
  if (!is_readable("../common/config/config.inc.php")) 
  $if_prob = "KO";
  //
  $txt = $l_admin_check_conf_file . " (<I>/common/config/config.inc.php</I>) : ";
  if ($if_prob == "OK") 
    $txt .= $c_OK;
  else
    $txt .= "<FONT COLOR='RED'><B> " . $l_admin_check_not_found . "  !";
  //
  table_col_1($txt);
  table_col_2($if_prob);
  //
  if ($if_prob != "OK") 
  {
    echo "</TABLE><div class='warning'><p class='error'>" . $l_install_check_cannot_continue;
    if ($lang == "FR")
      echo " droits de lecture au fichier de configuration</p></div>";
    else
      echo " read access to configuration file</p></div>";
    die();
  }
  //
  //
  require ("../common/config/config.inc.php");
  if (defined("_LANG"))
  {
    if (_LANG == "")
      $if_prob = "KO";
  }
  else
    $if_prob = "KO";
  //
  $txt = "Language : ";
  if ($if_prob == "OK") 
    $txt .= "<FONT COLOR='GREEN'><I><B>" . _LANG;
  else
    $txt .= "<FONT COLOR='RED'><B> " . $l_admin_check_not_found . " !";
  //
  table_col_1($txt);
  table_col_2($if_prob);
  //
  if ($if_prob != "OK") 
  {
    echo "</TABLE><div class='warning'><p class='error'>" . $l_install_check_cannot_continue;
    echo $l_admin_check_missing_option . "<I>_LANG</I>" . $l_admin_check_in_conf_file . "</p></div>";
    echo "</body></html>";
    die();
  }

  //
  require ("../common/lang.inc.php"); // ne pas modifier ! 
  if ( isset($l_lang_name) and isset($charset) )
  {
    if ( ($l_lang_name == "") or ($charset == "") )
      $if_prob = "KO";
  }
  else
    $if_prob = "KO";
  //
  $txt = "Language (charset) : ";
  if ($if_prob == "OK") 
    $txt .= "<FONT COLOR='GREEN'><I><B>" . $l_lang_name . " (" . $charset . ")";
  else
    $txt .= "<FONT COLOR='RED'><B>" . $l_admin_check_not_found . " !";
  //
  table_col_1($txt);
  table_col_2($if_prob);
  //
  require ("lang.inc.php"); // ne pas modifier !  on require à nouveau, pas le même chemin, vu que la langue d'install peut être différente de la langue configurée.
  //
  //
  echo "</TABLE>";
  //
  if (!is_writeable("../common/config/config.inc.php"))
  {
    echo "<BR/>";
    if ($lang == "FR")
      echo "<FONT COLOR='RED'><B>Impossible d'écrire dans le fichier : <I>/common/config/config.inc.php</I> !<BR/></font>";
    else
      echo "<FONT COLOR='RED'><B>Cannot write in file: <I>/common/config/config.inc.php</I> !<BR/></font>";
    echo "<BR/>";
    if ($lang == "FR")
      echo "Changer les droits puis actualiser la page [F5],<BR/>ou éditer le fichier manuellement pour changer les options.</B><BR/>";
    else
      echo "Change rights and then refresh [F5],<BR/>or you have to edit it to change options.</B><BR/>";
    echo "<BR/>";
    //
    if ($if_prob != "OK") 
    {
      echo "</TABLE><div class='warning'><p class='error'>" . $l_install_check_cannot_continue . " language file</p></div>";
      echo "</body></html>";
      die();
    }
    //
    echo "<BR/>";
    echo "<FORM METHOD='POST' ACTION='install.php?'>";
    echo "<INPUT TYPE='submit' VALUE = '" . $l_install_bt_next . "' class='liteoption' />";
    echo "<input type='hidden' name='lic' value = '" . $lic . "' />";
    echo "<input type='hidden' name='external_auth' value = '" . $external_auth . "' />";
    echo "<input type='hidden' name='step' value = '8' />";
    echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
    echo "</FORM>";
    echo "<BR/>";
  }
  
  if (!is_readable("../admin/list_options_update.php")) 
  {
    echo '<div class="warning"><p class="error">';
    if ($lang == "FR")
      echo "Impossible de modifier le fichier de configuration : renommer le répertoire admin en /admin/ et actualiser</p></div>";
    else
      echo "Cannot edit options: change back admin folder name to /admin/ and refresh</p></div>";
    //
    echo "<BR/>";
    echo "<FORM METHOD='POST' ACTION='install.php?'>";
    echo "<INPUT TYPE='submit' VALUE = '" . $l_install_bt_next . "' class='liteoption' />";
    echo "<input type='hidden' name='lic' value = '" . $lic . "' />";
    echo "<input type='hidden' name='external_auth' value = '" . $external_auth . "' />";
    echo "<input type='hidden' name='step' value = '7' />";
    echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
    echo "</FORM>";
    echo "<BR/>";
  }
  //
  //
  if ( (is_writeable("../common/config/config.inc.php")) and (is_readable("../admin/list_options_update.php")) )
  {
    require ("common.inc.php");
    //
    echo "<font face='verdana' size='2'>";
    echo "<BR/>";
    //  
    //
    echo "<FORM METHOD='POST' ACTION='../admin/list_options_update.php?'>";
    echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
    echo "<INPUT TYPE='hidden' name='T_MAINTENANCE_MODE' value = '" . _MAINTENANCE_MODE . "' />";
    //echo "<INPUT TYPE='hidden' name='T_MAINTENANCE_MODE' value = 'X' />";
    echo "<INPUT TYPE='hidden' name='T_FORCE_STATUS_LIST_FROM_SERVER' value = '" . _FORCE_STATUS_LIST_FROM_SERVER . "' />";
    echo "<INPUT TYPE='hidden' name='T_AWAY_REASONS_LIST' value = '" . _AWAY_REASONS_LIST . "' />";
    echo "<INPUT TYPE='hidden' name='T_SEND_ADMIN_ALERT' value = '" . _SEND_ADMIN_ALERT . "' />";
    echo "<INPUT TYPE='hidden' name='T_IM_ADDRESS_BOOK_PASSWORD' value = '" . _IM_ADDRESS_BOOK_PASSWORD . "' />";
    echo "<INPUT TYPE='hidden' name='T_PASSWORD_FOR_PRIVATE_SERVER' value = '" . _PASSWORD_FOR_PRIVATE_SERVER . "' />";
    //echo "<INPUT TYPE='hidden' name='T_SPECIAL_MODE_OPEN_COMMUNITY' value = '" . _SPECIAL_MODE_OPEN_COMMUNITY . "' />";
    //echo "<INPUT TYPE='hidden' name='T_SPECIAL_MODE_GROUP_COMMUNITY' value = '" . _SPECIAL_MODE_GROUP_COMMUNITY . "' />";
    echo "<INPUT TYPE='hidden' name='special_mode' value = '' />";
    echo "<INPUT TYPE='hidden' name='T_EXTERN_URL_TO_REGISTER' value = '" . _EXTERN_URL_TO_REGISTER . "' />";
    echo "<INPUT TYPE='hidden' name='T_EXTERN_URL_FORGET_PASSWORD' value = '" . _EXTERN_URL_FORGET_PASSWORD . "' />";
    echo "<INPUT TYPE='hidden' name='T_EXTERN_URL_CHANGE_PASSWORD' value = '" . _EXTERN_URL_CHANGE_PASSWORD . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_CONTACT_RATING' value = '" . _ALLOW_CONTACT_RATING . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_COL_FUNCTION_NAME' value = '" . _ALLOW_COL_FUNCTION_NAME . "' />";
    echo "<INPUT TYPE='hidden' name='T_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL' value = '" . _STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL . "' />";
    #
    //echo "<INPUT TYPE='hidden' name='T_MAX_NB_IP' value = '" . _MAX_NB_IP . "' />";
    echo "<INPUT TYPE='hidden' name='T_MAX_NB_CONTACT_BY_USER' value = '" . _MAX_NB_CONTACT_BY_USER . "' />";
    echo "<INPUT TYPE='hidden' name='T_MAX_NB_SESSION' value = '" . _MAX_NB_SESSION . "' />";
    echo "<INPUT TYPE='hidden' name='T_MAX_NB_USER' value = '" . _MAX_NB_USER . "' />";
    //echo "<INPUT TYPE='hidden' name='T_FLAG_COUNTRY_FROM_IP' value = '" . _FLAG_COUNTRY_FROM_IP . "' />";
    echo "<INPUT TYPE='hidden' name='T_OUTOFDATE_AFTER_NOT_USE_DURATION' value = '" . _OUTOFDATE_AFTER_NOT_USE_DURATION . "' />";
    echo "<INPUT TYPE='hidden' name='T_CHECK_NEW_MSG_EVERY' value = '" . _CHECK_NEW_MSG_EVERY . "' />";
    echo "<INPUT TYPE='hidden' name='T_SLOW_NOTIFY' value = '" . _SLOW_NOTIFY . "' />";
    echo "<INPUT TYPE='hidden' name='T_STATISTICS' value = '" . _STATISTICS . "' />";
    echo "<INPUT TYPE='hidden' name='T_ADMIN_EMAIL' value = '" . _ADMIN_EMAIL . "' />";
    echo "<INPUT TYPE='hidden' name='T_ADMIN_PHONE' value = '" . _ADMIN_PHONE . "' />";
    echo "<INPUT TYPE='hidden' name='T_SCROLL_TEXT' value = '" . _SCROLL_TEXT . "' />";
    echo "<INPUT TYPE='hidden' name='T_PROXY_ADDRESS' value = '" . _PROXY_ADDRESS . "' />";
    echo "<INPUT TYPE='hidden' name='T_PROXY_PORT_NUMBER' value = '" . _PROXY_PORT_NUMBER . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_CONFERENCE' value = '" . _ALLOW_CONFERENCE . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_HIDDEN_TO_CONTACTS' value = '" . _ALLOW_HIDDEN_TO_CONTACTS . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_SMILEYS' value = '" . _ALLOW_SMILEYS . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_CHANGE_CONTACT_NICKNAME' value = '" . _ALLOW_CHANGE_CONTACT_NICKNAME . "' />";
    //echo "<INPUT TYPE='hidden' name='T_ALLOW_CHANGE_EMAIL_PHONE' value = '" . _ALLOW_CHANGE_EMAIL_PHONE . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_CHANGE_FUNCTION_NAME' value = '" . _ALLOW_CHANGE_FUNCTION_NAME . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_SEND_TO_OFFLINE_USER' value = '" . _ALLOW_SEND_TO_OFFLINE_USER . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_HISTORY_MESSAGES' value = '" . _ALLOW_HISTORY_MESSAGES . "' />";
    echo "<INPUT TYPE='hidden' name='T_INCOMING_EMAIL_SERVER_ADDRESS' value = '" . _INCOMING_EMAIL_SERVER_ADDRESS . "' />";
    echo "<INPUT TYPE='hidden' name='T_FORCE_AWAY_ON_SCREENSAVER' value = '" . _FORCE_AWAY_ON_SCREENSAVER . "' />";
    echo "<INPUT TYPE='hidden' name='T_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN' value = '" . _USER_HIEARCHIC_MANAGEMENT_BY_ADMIN . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_MANAGE_CONTACT_LIST' value = '" . _ALLOW_MANAGE_CONTACT_LIST . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_MANAGE_OPTIONS' value = '" . _ALLOW_MANAGE_OPTIONS . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_MANAGE_PROFILE' value = '" . _ALLOW_MANAGE_PROFILE . "' />";
    echo "<INPUT TYPE='hidden' name='T_PUBLIC_FOLDER' value = '" . _PUBLIC_FOLDER . "' />";
    echo "<INPUT TYPE='hidden' name='T_PUBLIC_OPTIONS_LIST' value = '" . _PUBLIC_OPTIONS_LIST . "' />";
    //echo "<INPUT TYPE='hidden' name='T_PUBLIC_USERS_LIST' value = '" . _PUBLIC_USERS_LIST . "' />";
    echo "<INPUT TYPE='hidden' name='T_PUBLIC_POST_AVATAR' value = '" . _PUBLIC_POST_AVATAR . "' />";
    echo "<INPUT TYPE='hidden' name='T_FORCE_UPDATE_BY_SERVER' value = '" . _FORCE_UPDATE_BY_SERVER . "' />";
    echo "<INPUT TYPE='hidden' name='T_FORCE_UPDATE_BY_INTERNET' value = '" . _FORCE_UPDATE_BY_INTERNET . "' />";
    echo "<INPUT TYPE='hidden' name='T_HISTORY_MESSAGES_ON_ACP' value = '" . _HISTORY_MESSAGES_ON_ACP . "' />";
    echo "<INPUT TYPE='hidden' name='T_LOG_SESSION_OPEN' value = '" . _LOG_SESSION_OPEN . "' />";
    echo "<INPUT TYPE='hidden' name='T_SITE_URL_TO_SHOW' value = '" . _SITE_URL_TO_SHOW . "' />";
    echo "<INPUT TYPE='hidden' name='T_SITE_TITLE_TO_SHOW' value = '" . _SITE_TITLE_TO_SHOW . "' />";
    //echo "<INPUT TYPE='hidden' name='T_EXTERNAL_AUTHENTICATION' value = '" . _EXTERNAL_AUTHENTICATION . "' />";
    echo "<INPUT TYPE='hidden' name='T_EXTERNAL_AUTHENTICATION' value = '" . $external_auth  . "' />";
    echo "<INPUT TYPE='hidden' name='T_NEED_QUICK_REGISTER_TO_AUTO_ADD_NEW_USER' value = '" . _NEED_QUICK_REGISTER_TO_AUTO_ADD_NEW_USER . "' />";
    echo "<INPUT TYPE='hidden' name='T_SITE_TITLE' value = '" . _SITE_TITLE . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_UPPERCASE_SPACE_USERNAME' value = '" . _ALLOW_UPPERCASE_SPACE_USERNAME . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_EMAIL_NOTIFIER' value = '" . _ALLOW_EMAIL_NOTIFIER . "' />";
    echo "<INPUT TYPE='hidden' name='T_CENSOR_MESSAGES' value = '" . _CENSOR_MESSAGES . "' />";
    echo "<INPUT TYPE='hidden' name='T_PWD_NEED_DIGIT_LETTER' value = '" . _PWD_NEED_DIGIT_LETTER . "' />";
    echo "<INPUT TYPE='hidden' name='T_PWD_NEED_UPPER_LOWER' value = '" . _PWD_NEED_UPPER_LOWER . "' />";
    echo "<INPUT TYPE='hidden' name='T_PWD_NEED_SPECIAL_CHARACTER' value = '" . _PWD_NEED_SPECIAL_CHARACTER . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHOUTBOX' value = '" . _SHOUTBOX . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHOUTBOX_REFRESH_DELAY' value = '" . _SHOUTBOX_REFRESH_DELAY . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHOUTBOX_STORE_DAYS' value = '" . _SHOUTBOX_STORE_DAYS . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHOUTBOX_STORE_MAX' value = '" . _SHOUTBOX_STORE_MAX . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHOUTBOX_QUOTA_USER_DAY' value = '" . _SHOUTBOX_QUOTA_USER_DAY . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHOUTBOX_QUOTA_USER_WEEK' value = '" . _SHOUTBOX_QUOTA_USER_WEEK . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHOUTBOX_NEED_APPROVAL' value = '" . _SHOUTBOX_NEED_APPROVAL . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHOUTBOX_APPROVAL_QUEUE_USER' value = '" . _SHOUTBOX_APPROVAL_QUEUE_USER . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHOUTBOX_APPROVAL_QUEUE' value = '" . _SHOUTBOX_APPROVAL_QUEUE . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHOUTBOX_LOCK_USER_APPROVAL' value = '" . _SHOUTBOX_LOCK_USER_APPROVAL . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHOUTBOX_VOTE' value = '" . _SHOUTBOX_VOTE . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHOUTBOX_MAX_NOTES_USER_DAY' value = '" . _SHOUTBOX_MAX_NOTES_USER_DAY . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHOUTBOX_MAX_NOTES_USER_WEEK' value = '" . _SHOUTBOX_MAX_NOTES_USER_WEEK . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHOUTBOX_REMOVE_MESSAGE_VOTES' value = '" . _SHOUTBOX_REMOVE_MESSAGE_VOTES . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHOUTBOX_LOCK_USER_VOTES' value = '" . _SHOUTBOX_LOCK_USER_VOTES . "' />";
    echo "<INPUT TYPE='hidden' name='T_GROUP_USER_CAN_JOIN' value = '" . _GROUP_USER_CAN_JOIN . "' />";
    echo "<INPUT TYPE='hidden' name='T_GROUP_FOR_SBX_AND_ADMIN_MSG' value = '" . _GROUP_FOR_SBX_AND_ADMIN_MSG . "' />";
    echo "<INPUT TYPE='hidden' name='T_SERVERS_STATUS' value = '" . _SERVERS_STATUS . "' />";
    echo "<INPUT TYPE='hidden' name='T_ENTERPRISE_SERVER' value = '" . _ENTERPRISE_SERVER . "' />";
    echo "<INPUT TYPE='hidden' name='T_CHECK_VERSION_INTERNET' value = '" . _CHECK_VERSION_INTERNET . "' />";
    //echo "<INPUT TYPE='hidden' name='T_TIME_ZONES' value = '" . _TIME_ZONES . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHOUTBOX_PUBLIC' value = '" . _SHOUTBOX_PUBLIC . "' />";
    echo "<INPUT TYPE='hidden' name='T_BOOKMARKS' value = '" . _BOOKMARKS . "' />";
    echo "<INPUT TYPE='hidden' name='T_BOOKMARKS_VOTE' value = '" . _BOOKMARKS_VOTE . "' />";
    echo "<INPUT TYPE='hidden' name='T_BOOKMARKS_PUBLIC' value = '" . _BOOKMARKS_PUBLIC . "' />";
    echo "<INPUT TYPE='hidden' name='T_BOOKMARKS_NEED_APPROVAL' value = '" . _BOOKMARKS_NEED_APPROVAL . "' />";
    echo "<INPUT TYPE='hidden' name='T_LOCK_DURATION' value = '" . _LOCK_DURATION . "' />";
    echo "<INPUT TYPE='hidden' name='T_UNREAD_MESSAGE_VALIDITY' value = '" . _UNREAD_MESSAGE_VALIDITY . "' />";
    echo "<INPUT TYPE='hidden' name='T_LOCK_AFTER_NO_CONTACT_DURATION' value = '" . _LOCK_AFTER_NO_CONTACT_DURATION . "' />";
    echo "<INPUT TYPE='hidden' name='T_LOCK_AFTER_NO_ACTIVITY_DURATION' value = '" . _LOCK_AFTER_NO_ACTIVITY_DURATION . "' />";
    echo "<INPUT TYPE='hidden' name='T_INVITE_FILL_PROFILE_ON_FIRST_LOGIN' value = '" . _INVITE_FILL_PROFILE_ON_FIRST_LOGIN . "' />";
    echo "<INPUT TYPE='hidden' name='T_ROLES_TO_OVERRIDE_PERMISSIONS' value = '" . _ROLES_TO_OVERRIDE_PERMISSIONS . "' />";
    echo "<INPUT TYPE='hidden' name='T_WAIT_STARTUP_IF_SERVER_UNAVAILABLE' value = '" . _WAIT_STARTUP_IF_SERVER_UNAVAILABLE . "' />";
    echo "<INPUT TYPE='hidden' name='T_ONLINE_REASONS_LIST' value = '" . _ONLINE_REASONS_LIST . "' />";
    echo "<INPUT TYPE='hidden' name='T_BUSY_REASONS_LIST' value = '" . _BUSY_REASONS_LIST . "' />";
    echo "<INPUT TYPE='hidden' name='T_DONOTDISTURB_REASONS_LIST' value = '" . _DONOTDISTURB_REASONS_LIST . "' />";
    echo "<INPUT TYPE='hidden' name='T_SPECIAL_MODE_OPEN_GROUP_COMMUNITY' value = '" . _SPECIAL_MODE_OPEN_GROUP_COMMUNITY . "' />";
    echo "<INPUT TYPE='hidden' name='T_FORCE_LAUNCH_ON_STARTUP' value = '" . _FORCE_LAUNCH_ON_STARTUP . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_SKIN' value = '" . _ALLOW_SKIN . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_CLOSE_IM' value = '" . _ALLOW_CLOSE_IM . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_SOUND_USAGE' value = '" . _ALLOW_SOUND_USAGE . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_REDUCE_MAIN_SCREEN' value = '" . _ALLOW_REDUCE_MAIN_SCREEN . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_REDUCE_MESSAGE_SCREEN' value = '" . _ALLOW_REDUCE_MESSAGE_SCREEN . "' />";
    echo "<INPUT TYPE='hidden' name='T_SKIN_FORCED_COLOR_CUSTOM_VERSION' value = '" . _SKIN_FORCED_COLOR_CUSTOM_VERSION . "' />";
    echo "<INPUT TYPE='hidden' name='T_SEND_ADMIN_ALERT_EMAIL' value = '" . _SEND_ADMIN_ALERT_EMAIL . "' />";
    echo "<INPUT TYPE='hidden' name='T_AUTO_ADD_CONTACT_USER_ID' value = '" . _AUTO_ADD_CONTACT_USER_ID . "' />";
    echo "<INPUT TYPE='hidden' name='T_PASSWORD_VALIDITY' value = '" . _PASSWORD_VALIDITY . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_POST_IT' value = '" . _ALLOW_POST_IT . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES' value = '" . _SHARE_FILES . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_EXCHANGE' value = '" . _SHARE_FILES_EXCHANGE . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_EXCHANGE_NEED_APPROVAL' value = '" . _SHARE_FILES_EXCHANGE_NEED_APPROVAL . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_EXCHANGE_TRASH' value = '" . _SHARE_FILES_EXCHANGE_TRASH . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_FTP_ADDRESS' value = '" . _SHARE_FILES_FTP_ADDRESS . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_FTP_LOGIN' value = '" . _SHARE_FILES_FTP_LOGIN . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_FTP_PASSWORD' value = '" . _SHARE_FILES_FTP_PASSWORD . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_FTP_PASSWORD_CRYPT' value = '" . _SHARE_FILES_FTP_PASSWORD_CRYPT . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_FTP_PORT_NUMBER' value = '" . _SHARE_FILES_FTP_PORT_NUMBER . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_MAX_FILE_SIZE' value = '" . _SHARE_FILES_MAX_FILE_SIZE . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_MAX_NB_FILES_TOTAL' value = '" . _SHARE_FILES_MAX_NB_FILES_TOTAL . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_MAX_NB_FILES_USER' value = '" . _SHARE_FILES_MAX_NB_FILES_USER . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_MAX_SPACE_SIZE_TOTAL' value = '" . _SHARE_FILES_MAX_SPACE_SIZE_TOTAL . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_MAX_SPACE_SIZE_USER' value = '" . _SHARE_FILES_MAX_SPACE_SIZE_USER . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_NEED_APPROVAL' value = '" . _SHARE_FILES_NEED_APPROVAL . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_APPROVAL_QUEUE' value = '" . _SHARE_FILES_APPROVAL_QUEUE . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_QUOTA_FILES_USER_WEEK' value = '" . _SHARE_FILES_QUOTA_FILES_USER_WEEK . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_TRASH' value = '" . _SHARE_FILES_TRASH . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_VOTE' value = '" . _SHARE_FILES_VOTE . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_FOLDER' value = '" . _SHARE_FILES_FOLDER . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_EXCHANGE_UNREAD_VALIDITY' value = '" . _SHARE_FILES_EXCHANGE_UNREAD_VALIDITY . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_SCREENSHOT' value = '" . _SHARE_FILES_SCREENSHOT . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_EXCHANGE_SCREENSHOT' value = '" . _SHARE_FILES_EXCHANGE_SCREENSHOT . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_WEBCAM' value = '" . _SHARE_FILES_WEBCAM . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_EXCHANGE_WEBCAM' value = '" . _SHARE_FILES_EXCHANGE_WEBCAM . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_COMPRESS' value = '" . _SHARE_FILES_COMPRESS . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_DAY' value = '" . _SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_DAY . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_WEEK' value = '" . _SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_WEEK . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_MONTH' value = '" . _SHARE_FILES_DOWNLOAD_QUOTA_FILES_USER_MONTH . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_DAY' value = '" . _SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_DAY . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_WEEK' value = '" . _SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_WEEK . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_MONTH' value = '" . _SHARE_FILES_DOWNLOAD_QUOTA_MB_USER_MONTH . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_PROTECT' value = '" . _SHARE_FILES_PROTECT . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_ALLOW_UPPERCASE' value = '" . _SHARE_FILES_ALLOW_UPPERCASE . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHARE_FILES_ALLOW_ACCENT' value = '" . _SHARE_FILES_ALLOW_ACCENT . "' />";
    echo "<INPUT TYPE='hidden' name='T_STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL_AFTER_LOGIN' value = '" . _STOP_USE_THIS_SERVER_ADDRESS_NOW_USE_THIS_URL_AFTER_LOGIN . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_HIDDEN_STATUS' value = '" . _ALLOW_HIDDEN_STATUS . "' />";
    echo "<INPUT TYPE='hidden' name='T_ROLE_ID_DEFAULT_FOR_NEW_USER' value = '" . _ROLE_ID_DEFAULT_FOR_NEW_USER . "' />";
    echo "<INPUT TYPE='hidden' name='T_ACP_PROTECT_BY_HTACCESS' value = '" . _ACP_PROTECT_BY_HTACCESS . "' />";
    echo "<INPUT TYPE='hidden' name='T_ACP_ALLOW_MEMORY_AUTH' value = '" . _ACP_ALLOW_MEMORY_AUTH . "' />";
    echo "<INPUT TYPE='hidden' name='T_ALLOW_HISTORY_MESSAGES_EXPORT' value = '" . _ALLOW_HISTORY_MESSAGES_EXPORT . "' />";
    echo "<INPUT TYPE='hidden' name='T_BACKUP_FILES' value = '" . _BACKUP_FILES . "' />";
    echo "<INPUT TYPE='hidden' name='T_BACKUP_FILES_MAX_ARCHIVE_SIZE' value = '" . _BACKUP_FILES_MAX_ARCHIVE_SIZE . "' />";
    echo "<INPUT TYPE='hidden' name='T_BACKUP_FILES_MAX_NB_ARCHIVES_USER' value = '" . _BACKUP_FILES_MAX_NB_ARCHIVES_USER . "' />";
    echo "<INPUT TYPE='hidden' name='T_BACKUP_FILES_MAX_SPACE_SIZE_USER' value = '" . _BACKUP_FILES_MAX_SPACE_SIZE_USER . "' />";
    echo "<INPUT TYPE='hidden' name='T_BACKUP_FILES_MAX_SPACE_SIZE_TOTAL' value = '" . _BACKUP_FILES_MAX_SPACE_SIZE_TOTAL . "' />";
    echo "<INPUT TYPE='hidden' name='T_BACKUP_FILES_THIS_LOCAL_FOLDER_ONLY' value = '" . _BACKUP_FILES_THIS_LOCAL_FOLDER_ONLY . "' />";
    echo "<INPUT TYPE='hidden' name='T_BACKUP_FILES_ALLOW_MULTI_FOLDERS' value = '" . _BACKUP_FILES_ALLOW_MULTI_FOLDERS . "' />";
    echo "<INPUT TYPE='hidden' name='T_BACKUP_FILES_ALLOW_SUB_FOLDERS' value = '" . _BACKUP_FILES_ALLOW_SUB_FOLDERS . "' />";
    echo "<INPUT TYPE='hidden' name='T_BACKUP_FILES_FOLDER' value = '" . _BACKUP_FILES_FOLDER . "' />";
    echo "<INPUT TYPE='hidden' name='T_BACKUP_FILES_FTP_ADDRESS' value = '" . _BACKUP_FILES_FTP_ADDRESS . "' />";
    echo "<INPUT TYPE='hidden' name='T_BACKUP_FILES_FTP_LOGIN' value = '" . _BACKUP_FILES_FTP_LOGIN . "' />";
    echo "<INPUT TYPE='hidden' name='T_BACKUP_FILES_FTP_PASSWORD' value = '" . _BACKUP_FILES_FTP_PASSWORD . "' />";
    echo "<INPUT TYPE='hidden' name='T_BACKUP_FILES_FTP_PASSWORD_CRYPT' value = '" . _BACKUP_FILES_FTP_PASSWORD_CRYPT . "' />";
    echo "<INPUT TYPE='hidden' name='T_BACKUP_FILES_FTP_PORT_NUMBER' value = '" . _BACKUP_FILES_FTP_PORT_NUMBER . "' />";
    echo "<INPUT TYPE='hidden' name='T_BACKUP_FILES_FORCE_EVERY_DAY_AT' value = '" . _BACKUP_FILES_FORCE_EVERY_DAY_AT . "' />";
    echo "<INPUT TYPE='hidden' name='T_GROUP_ID_DEFAULT_FOR_NEW_USER' value = '" . _GROUP_ID_DEFAULT_FOR_NEW_USER . "' />";
    echo "<INPUT TYPE='hidden' name='T_FORCE_OPTION_FILE_FROM_SERVER' value = '" . _FORCE_OPTION_FILE_FROM_SERVER . "' />";
    echo "<INPUT TYPE='hidden' name='T_SHOUTBOX_ALLOW_SCROLLING' value = '" . _SHOUTBOX_ALLOW_SCROLLING . "' />";
    //echo "<INPUT TYPE='hidden' name='T' value = '" . _xxxxxxxxxx . "' />";
    //echo "<INPUT TYPE='hidden' name='T' value = '" . _xxxxxxxxxx . "' />";
    //echo "<INPUT TYPE='hidden' name='T' value = '" . _xxxxxxxxxx . "' />";
    //echo "<INPUT TYPE='hidden' name='T' value = '" . _xxxxxxxxxx . "' />";
    //
    echo "<TABLE cellspacing='1' cellpadding='1' class='forumline' width='90%'>";
    echo "<TR>";
      echo "<TH align=center COLSPAN='2' class='thHead'>";
      echo "<font face='verdana' size='3'><b>" . $l_admin_options_title . "</b></font></TH>";
      echo "<TH align=center COLSPAN='2' class='thHead'>";
      echo "<font face='verdana' size='3'><b>" . $l_admin_options_title_2 . "</b></font></TH>";
    echo "</TR>";
    echo "<TR>";
      //display_row_table($l_admin_options_col_option, '');
      display_row_table("&nbsp;" . $l_admin_options_col_value . "&nbsp;", '');
      display_row_table($l_admin_options_col_comment, '');
      //display_row_table("&nbsp;LAN&nbsp;", '');
      if ($lang == "FR")
        display_row_table("&nbsp;<acronym title='Réseau local'>LAN</acronym>&nbsp;", '');
      else
        display_row_table("&nbsp;<acronym title='Local Area Network'>LAN</acronym>&nbsp;", '');
      //
      display_row_table("Internet", '');
    echo "</TR>";

    //echo "<TR>";
    //echo "<TD colspan='5' align='center' class='catHead'>";
    //echo "<font face='verdana' size='2'><B>" . $l_admin_options_admin_options . " :</B></font>"; /////////////////////////////////////////////
    //echo "</TD>";
    //echo "</TR>";

    display_row(_LANG, "_LANG", 2, $l_language . " : EN / FR / IT / PT / BR / RO / DE / NL" , "", "");
    //display_row(_MAINTENANCE_MODE, "_MAINTENANCE_MODE", 0, "Maintenance", "", "");
    //display_row(_MAX_NB_USER, "_MAX_NB_USER", 5, $l_admin_options_nb_max_user, "", "");
    //display_row(_MAX_NB_SESSION, "_MAX_NB_SESSION", 4, $l_admin_options_nb_max_session, "", "");
    //display_row(_MAX_NB_CONTACT_BY_USER, "_MAX_NB_CONTACT_BY_USER", 3, $l_admin_options_nb_max_contact_by_user, "", "");
    display_row(_MAX_NB_IP, "_MAX_NB_IP", 2, $l_admin_options_max_simultaneous_ip_addresses, "", "");
    display_row(_PUBLIC_USERS_LIST, "_PUBLIC_USERS_LIST", 0, $l_admin_options_public_see_users, "", "-");
    display_row(_FLAG_COUNTRY_FROM_IP, "_FLAG_COUNTRY_FROM_IP", 0, $l_admin_options_flag_country, "-", "X");
    display_row(_TIME_ZONES, "_TIME_ZONES", 0, $l_admin_options_time_zones, "-", "X");
    #display_row(_OUTOFDATE_AFTER_NOT_USE_DURATION, "_OUTOFDATE_AFTER_NOT_USE_DURATION", 3, $l_admin_options_del_user_after_x_days_not_use, "80", "50");
    #display_row(_CHECK_NEW_MSG_EVERY, "_CHECK_NEW_MSG_EVERY", 2, $l_admin_options_check_new_msg_every, "20", "30");
    #display_row(_ADMIN_EMAIL, "_ADMIN_EMAIL", 50, $l_admin_options_admin_email, "X", "X");
    #display_row(_ADMIN_PHONE, "_ADMIN_PHONE", 30, $l_admin_options_admin_phone, "X", "-");
    #display_row(_PROXY_ADDRESS, "_PROXY_ADDRESS", 23, $l_admin_options_proxy_address, "", "-");
    #display_row(_PROXY_PORT_NUMBER, "_PROXY_PORT_NUMBER", 5, $l_admin_options_proxy_port_number, "", "-");
    #display_row(_GROUP_FOR_SBX_AND_ADMIN_MSG, "_GROUP_FOR_SBX_AND_ADMIN_MSG", 0, $l_admin_options_group_for_sbx_and_admin_messages, "", "");
    #display_row(_ENTERPRISE_SERVER, "_ENTERPRISE_SERVER", 0, $l_admin_options_enterprise_server, "", "-");

    echo "<TR>";
    echo "<TD colspan='5' align='center' class='catHead'>";
    echo "<font face='verdana' size='2'><B>" . $l_admin_options_user_restrictions_options . " :</B></font>";  ////////////////////////////////////////
    echo "</TD>";
    echo "</TR>";

    display_row(_FORCE_USERNAME_TO_PC_SESSION_NAME, "_FORCE_USERNAME_TO_PC_SESSION_NAME", 0, $l_admin_options_is_usernamePC . " {*}", "", "-");
    #display_row(_ALLOW_CONFERENCE, "_ALLOW_CONFERENCE", 0, $l_admin_option_allow_conference, "X", "X");
    #display_row(_ALLOW_HIDDEN_TO_CONTACTS, "_ALLOW_HIDDEN_TO_CONTACTS", 0, $l_admin_options_allow_invisible, "", "");
    #display_row(_ALLOW_SMILEYS, "_ALLOW_SMILEYS", 0, $l_admin_options_allow_smiley, "X", "X");
    #display_row(_ALLOW_CHANGE_CONTACT_NICKNAME, "_ALLOW_CHANGE_CONTACT_NICKNAME", 0, $l_admin_options_can_change_contact_nickname, "X", "X");
    display_row(_ALLOW_CHANGE_EMAIL_PHONE, "_ALLOW_CHANGE_EMAIL_PHONE", 0, $l_admin_options_allow_change_email_phone, "X", "X");
    #display_row(_ALLOW_CHANGE_FUNCTION_NAME, "_ALLOW_CHANGE_FUNCTION_NAME", 0, $l_admin_options_allow_change_function_name, "X", "X");
    display_row(_ALLOW_CHANGE_AVATAR, "_ALLOW_CHANGE_AVATAR", 0, $l_admin_options_allow_change_avatar, "X", "X");
    #display_row(_ALLOW_SEND_TO_OFFLINE_USER, "_ALLOW_SEND_TO_OFFLINE_USER", 0, $l_admin_option_send_offline, "X", "X");
    #display_row(_ALLOW_HISTORY_MESSAGES, "_ALLOW_HISTORY_MESSAGES", 0, $l_admin_options_user_history_messages, "", "");
    display_row(_ALLOW_USE_PROXY, "_ALLOW_USE_PROXY", 0, $l_admin_options_allow_use_proxy, "", "X");
    //display_row(_ALLOW_CONTACT_RATING, "_ALLOW_CONTACT_RATING", 0, $l_admin_options_allow_rating, "-", "");
    //display_row(_ALLOW_UPPERCASE_SPACE_USERNAME, "_ALLOW_UPPERCASE_SPACE_USERNAME", 0, $l_admin_options_uppercase_space_nickname, "X", "X");
    //display_row(_ALLOW_EMAIL_NOTIFIER, "_ALLOW_EMAIL_NOTIFIER", 0, $l_admin_options_allow_email_notifier, "", "X");
    #display_row(_INCOMING_EMAIL_SERVER_ADDRESS, "_INCOMING_EMAIL_SERVER_ADDRESS", 90, $l_admin_options_force_email_server, "", "-");
    #display_row(_FORCE_AWAY_ON_SCREENSAVER, "_FORCE_AWAY_ON_SCREENSAVER", 0, $l_admin_options_force_away, "X", "");
    //display_row(_ALLOW_COL_FUNCTION_NAME, "_ALLOW_COL_FUNCTION_NAME", 0, $l_admin_options_col_name_hide, "", "X");
    #display_row(_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN , "_USER_HIEARCHIC_MANAGEMENT_BY_ADMIN", 0, $l_admin_options_hierachic_management, "", "");
    //display_row(_FORCE_STATUS_LIST_FROM_SERVER, "_FORCE_STATUS_LIST_FROM_SERVER", 0, $xxxxxxx, "X", "");
    #display_row(_PUBLIC_FOLDER, "_PUBLIC_FOLDER", 30, $l_admin_options_public_folder, "X", "");
    //
    echo "<TR>";
    echo "<TD colspan='5' align='center' class='catHead'>";
    echo "<font face='verdana' size='2'><B>" . $l_admin_options_security_options . " :</B></font>";  ////////////////////////////////////////
    echo "</TD>";
    echo "</TR>";
    if (_FORCE_USERNAME_TO_PC_SESSION_NAME == "")
      display_row(_MINIMUM_USERNAME_LENGTH, "_MINIMUM_USERNAME_LENGTH", 2, $l_admin_options_minimum_length_of_username, "4", "6");
    else
      echo "<INPUT TYPE='hidden' name='T_MINIMUM_USERNAME_LENGTH' value = '" . _MINIMUM_USERNAME_LENGTH . "' />";
    //
    display_row(_USER_NEED_PASSWORD, "_USER_NEED_PASSWORD", 0, $l_admin_options_password_user . " {*}", "X", "X");
    if (_USER_NEED_PASSWORD != "")
    {
      display_row(_MINIMUM_PASSWORD_LENGTH, "_MINIMUM_PASSWORD_LENGTH", 2, $l_admin_options_minimum_length_of_password, "6", "8");
      display_row(_MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER, "_MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER", 2, $l_admin_options_max_pwd_error_lock, "5", "5");
    }
    else
    {
      echo "<INPUT TYPE='hidden' name='T_MINIMUM_PASSWORD_LENGTH' value = '" . _MINIMUM_PASSWORD_LENGTH . "' />";
      echo "<INPUT TYPE='hidden' name='T_MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER' value = '" . _MAX_PASSWORD_ERRORS_BEFORE_LOCK_USER . "' />";
    }
    display_row(_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER, "_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER", 0, $l_admin_options_auto_add_user . " {*}", "X", "X");
    if (_ALLOW_AUTO_ADD_NEW_USER_ON_SERVER != "")
      display_row(_PENDING_NEW_AUTO_ADDED_USER, "_PENDING_NEW_AUTO_ADDED_USER", 0, $l_admin_options_need_admin_after_add . " {*}", "-", "-");
    else
      echo "<INPUT TYPE='hidden' name='T_PENDING_NEW_AUTO_ADDED_USER' value = '" . _PENDING_NEW_AUTO_ADDED_USER . "' />";
    //
    display_row(_PENDING_USER_ON_COMPUTER_CHANGE, "_PENDING_USER_ON_COMPUTER_CHANGE", 0, $l_admin_options_need_admin_if_chang_check . " {*}", "X", "-");
    #display_row(_FORCE_UPDATE_BY_SERVER, "_FORCE_UPDATE_BY_SERVER", 0, $l_admin_options_force_update_by_server, "", "-");
    #display_row(_FORCE_UPDATE_BY_INTERNET, "_FORCE_UPDATE_BY_INTERNET", 0, $l_admin_options_force_update_by_internet, "", "X");
    display_row(_CRYPT_MESSAGES, "_CRYPT_MESSAGES", 0, $l_admin_options_crypt_msg, "", "X");
    #display_row(_HISTORY_MESSAGES_ON_ACP, "_HISTORY_MESSAGES_ON_ACP", 0, $l_admin_options_log_messages, "-", "-");
    #display_row(_LOG_SESSION_OPEN, "_LOG_SESSION_OPEN", 0, $l_admin_options_log_session_open, "", "X");
    //
    echo "</TABLE>";
    //
    //echo "<BR/>";
    //
    /*
    echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
    echo "<TR>";
      echo "<TH align=center COLSPAN='3' class='thHead'>";
      echo "<font face='verdana' size='3'><b> &nbsp; " . $l_admin_options_title_table_2 . " &nbsp; </b></font> ";
      echo "<TH align=center COLSPAN='2' class='thHead'>";
      echo "<font face='verdana' size='3'><b>" . $l_admin_options_title_2 . "</b></font></TH>";
      echo "</TH>";
    echo "</TR>";
    echo "<TR>";
      //display_row_table($l_admin_options_col_option, '');
      display_row_table($l_admin_options_col_value, '');
      display_row_table($l_admin_options_col_comment, '');
      display_row_table("&nbsp;LAN&nbsp;", '');
      display_row_table("Internet", '');
    echo "</TR>";
    display_row(_SITE_URL_TO_SHOW, "_SITE_URL_TO_SHOW", 100, $l_admin_options_site_url, "", "X");
    display_row(_SITE_TITLE_TO_SHOW, "_SITE_TITLE_TO_SHOW", 100, $l_admin_options_site_title, "", "X");
    echo "<TR>";
      echo "<TD align='center' COLSPAN='5' class='catBottom'>";
      echo "<font face='verdana' size='2'>";
      echo $l_admin_options_info_1 . "</font>";
      echo "</TD>";
    echo "</TR>";
    //
    echo "</TABLE>";
    */
    //
    
    //
    echo "<br/><div class='notice'>";
    if ($lang == "FR")
    {
      echo "<strong><u>Si</u></strong> l'erreur suivante apparait : <br/>";
      echo "<p class='error'><I>Strict Standards: date(): It is not safe to rely on the system's timezone settings.</I></P>";
      echo "Veuillez consulter le forum officiel à la page : <A HREF='http://www.intramessenger.com/forum/viewtopic.php?p=6604#p6604' target='_blank'>http://www.intramessenger.com/forum/viewtopic.php?p=6604#p6604</A><br/>";
    }
    else
    {
      echo "<strong><u>If</u></strong> the following error appears: <br/>";
      echo "<p class='error'><I>Strict Standards: date(): It is not safe to rely on the system's timezone settings.</I></P>";
      echo "Please see the official forum at: <A HREF='http://www.intramessenger.com/forum/viewtopic.php?p=6606#p6606' target='_blank'>http://www.intramessenger.com/forum/viewtopic.php?p=6606#p6606</A><br/>";
    }
    echo "</div>";
    //

    //
    echo "<BR/>";
    echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_update . "' class='mainoption' />";
    echo "<input type='hidden' name='lic' value = '" . $lic . "' />";
    echo "<input type='hidden' name='external_auth' value = '" . $external_auth . "' />";
    echo "<input type='hidden' name='step' value = '8' />";
    echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
    echo "</FORM>";
    echo "<BR/>";
    
    
  }
}



// ---------------------------------------------------------- STEP 8 ----------------------------------------------------------
function step_8()
{
  GLOBAL $lang, $step_8_ok, $steps, $lic, $l_install_step, $external_auth; //      $c_OK, $c_not_found, $c_found, $c_on_ok, $c_on_ko, $c_off_ko, $c_off_ok, $external_auth;
  GLOBAL $l_admin_acp_admin_title, $l_admin_acp_admin_create, $l_admin_acp_auth_username, $l_admin_acp_auth_password, $l_admin_bt_add;
  GLOBAL $l_admin_acp_admin_at_least, $l_admin_acp_pass_3;
  //
  $if_prob = "OK";
  //
  echo "<font face='verdana' size='5' color='white'>";
  echo $l_install_step . " 8 : " . $steps[8] . "<BR/>";
  echo "</font>";
  //
  if ($step_8_ok == "KO")
  {
    if ($lang == "FR")
      echo "<br/><div class='warning'><p class='error'>Pseudo ou mot de passe trop courts<br/> ou erreur de mot vérification de mot de passe<br/> ou compte existant : essayer à nouveau.</p></div>";
    else
      echo '<br/><div class="warning"><p class="error">Username or password to short<br/> or password confirm error<br/> or username exist already: try again.</p></div>';
  }
  //
  echo "<BR/>";
  echo "<FORM METHOD='POST' ACTION='install.php?'>";
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<TR>";
  echo "<TH align='center' COLSPAN='3' class='thHead'>";
  echo "<font face=verdana size=3><b>&nbsp;" . $l_admin_acp_admin_create . "&nbsp;</b></font>";
  echo "</TH>";
  echo "</TR>";
  //
  echo "<TR>";
  echo "<TD class='row1' align='left'>";
  echo "<font face=verdana size=2>&nbsp;";
  echo $l_admin_acp_auth_username;
  echo "</TD>";
  echo "<TD class='row1' align='center'>";
  echo "<input type='text' name='adm_username' maxlength='20' value='admin' size='30' class='post' />";
  //echo "<BR/>";
  echo "</TD>";
  echo "</TR>";
  //
  echo "<TR>";
  echo "<TD class='row1' align='left'>";
  echo "<font face=verdana size=2>&nbsp;";
  echo $l_admin_acp_auth_password . " [*]";
  echo "</TD>";
  echo "<TD class='row1'align='center'>";
  echo "<input type='password' name='adm_pass' maxlength='20' value='' size='30' class='post' />";
  echo "</TD>";
  echo "</TR>";
  //
  echo "<TR>";
  echo "<TD class='row1' align='left'>";
  echo "<font face=verdana size=2>&nbsp;";
  echo $l_admin_acp_pass_3 . " [*]";
  echo "</TD>";
  echo "<TD class='row1'align='center'>";
  echo "<input type='password' name='adm_pass_2' maxlength='20' value='' size='30' class='post' />";
  echo "</TD>";
  echo "</TR>";
  //
  //
  echo "<TR>";
  echo "<TD align='center' COLSPAN='3' class='row2'>";
  echo "<INPUT TYPE='submit' VALUE = '" . $l_admin_bt_add . "' class='liteoption' />";
  echo "<input type='hidden' name='lic' value = '" . $lic . "' />";
  echo "<input type='hidden' name='external_auth' value = '" . $external_auth . "' />";
  echo "<input type='hidden' name='step' value = '9' />";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
  echo "</TD>";
  echo "</TR>";
  echo "<TR>";
  //
  echo "<TR>";
  echo "<TD align='center' COLSPAN='3' class='catBottom'>";
  echo "<font face=verdana size=2>&nbsp;" . "[*] " . $l_admin_acp_admin_at_least . "&nbsp;</font>";
  echo "</TD>";
  echo "</TR>";
  //
  echo "</TABLE>";	//
  echo "</FORM>";
}







// ---------------------------------------------------------- STEP 9 ----------------------------------------------------------
function step_9()
{
  GLOBAL $lang, $c_OK, $c_not_found, $c_found, $c_on_ok, $c_on_ko, $c_off_ko, $c_off_ok, $lic, $external_auth, $steps;
  GLOBAL $l_install_file, $l_install_check_files, $l_admin_check_not_found, $l_admin_check_not_writeable, $l_admin_options_autentification;
  GLOBAL $l_install_bt_next, $l_install_step, $l_install_check_cannot_continue;
  //
  $if_prob = "OK";
  //
  echo "<font face='verdana' size='5' color='white'>";
  echo $l_install_step . " 9 : " . $steps[9] . "<BR/>";
  echo "</font>";

  require ("../common/config/config.inc.php");
  //
  $txt = "";
  $arr = array('../common/config/version.tmp', '../admin/log/lastcheck.tmp', '../admin/log/error_log.txt', '../distant/log/error_log.txt', '../distant/log/log_open_session.txt', '../distant/log/log_password_errors.txt', '../distant/log/log_lock_user_for_password_errors.txt', '../distant/log/log_user_change_nickname.txt', '../distant/log/log_user_check_change.txt', '../' . _PUBLIC_FOLDER . '/log/log_upload_avatar.txt');
  foreach ($arr as $fichier) 
  {
    if (!is_readable($fichier))
    {
      // on essai de le créer :
      touch($fichier);
      // on vérifie :
      if (!is_readable($fichier))
      {
        $txt .= $l_install_file . " <I>" . $fichier . "</I> : ";
        $txt .= "<FONT COLOR='RED'><B> " . $l_admin_check_not_found . "</B></FONT><BR/>";
      }
    }
    else
    {
      if (!is_writeable($fichier))
      {
        $txt .= $l_install_file . " <I>" . $fichier . "</I> : ";
        $txt .= "<FONT COLOR='RED'><B> " . $l_admin_check_not_writeable . " !</B></FONT> (<B>chmod</B>)<BR/>";
      }
    } 
  }
  //
  if ($txt != "")
  {
    if (strpos($txt, _PUBLIC_FOLDER)) $txt .= "<FONT COLOR='RED'>Option <B>_PUBLIC_FOLDER</B> must be <B>/public/</B> folder !</FONT><BR/>";
    $if_prob = "KO";
    table_title($l_install_check_files);
    table_col_1($txt);
    table_col_2($if_prob);
    echo "</TABLE>";
    echo "\n";
  }
  //
  //
  table_title($l_admin_options_autentification);
  //require ("../common/config/auth.inc.php");
  //if ($password_pepper == "")
  if ($lang == "FR")
  {
    $txt  = "Si vous n'utilisez <U>pas</U> l'authentification externe et effectuez une installation nouvelle,<BR/>";
    $txt .= "vous devriez choisir un mot de passe 'fort' pour améliorer encore la sécurité <BR/>";
    $txt .= "dans le fichier /common/config/auth.inc.php<BR/>";
    $txt .= "<BR/>";
    $txt .= "Exemple : <I>\$password_pepper = 'ed@d*ùA18£'; </I><BR/>";
  }
  else
  {
    $txt  = "If you <U>not</U> use extern authentication, and a new install,<BR/>";
    $txt .= "you may choose a strong password to improve security <BR/>";
    $txt .= "in file /common/config/auth.inc.php<BR/>";
    $txt .= "<BR/>";
    $txt .= "Example: <I>\$password_pepper = 'ed@d*ùA18£'; </I><BR/>";
  }
  table_col_1($txt);
  echo "</TABLE>";
  echo "\n";
  //
  //
  echo "<BR/>";
  echo "<FORM METHOD='POST' ACTION='install.php?'>";
  echo "<INPUT TYPE='submit' VALUE = '" . $l_install_bt_next . "' class='liteoption' />";
  echo "<input type='hidden' name='lic' value = '" . $lic . "' />";
  echo "<input type='hidden' name='external_auth' value = '" . $external_auth . "' />";
  echo "<input type='hidden' name='step' value = '10' />";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
  echo "</FORM>";
  echo "<BR/>";
}





// ---------------------------------------------------------- STEP 10 ----------------------------------------------------------
function step_10()
{
  GLOBAL $lang, $c_OK, $c_not_found, $c_found, $c_on_ok, $c_on_ko, $c_off_ko, $c_off_ok, $lic, $external_auth, $steps;
  GLOBAL $l_menu_need_reg, $l_install_bt_next, $l_install_step, $l_admin_authentication_extern;
  //
  $if_prob = "OK";
  //
  echo "<font face='verdana' size='5' color='white'>";
  echo $l_install_step . " 10 : " . $steps[10] . "<BR/>";
  echo "</font>";

  echo "<BR/>";
  echo "<BR/>";
  echo "<BR/>";
  echo "<BR/>";
  echo "<B>";
  if ($lang == "FR")
  {
    echo "Des lenteurs peuvent apparaitre sur certains postes (clients) : c'est du à un problème de DNS.<BR/>";
    echo "Pour le résoudre <A href='http://www.intramessenger.com/forum/viewtopic.php?t=370&' target='_blank'>consulter le forum</A>.<BR/>";
    echo "<BR/>";
    echo "<BR/>";
    echo "<BR/>";
    echo "<BR/>";
    echo "Si vous souhaitez utiliser l'authentification externe [*]</B><BR/>";
    echo "(via un forum, blog, CMS, CRM, PGI (ERP)... existant)<BR/>";
    echo "<B>veuillez consulter : ";
    echo "<A HREF='../doc/fr/authentification_externe.html' target='_blank'>/doc/fr/authentification_externe.html</A><BR/>";
    echo "</B><font face='verdana' size='2'>";
    echo "ou <A HREF='http://www.intramessenger.net/doc/authentification_externe.html' target='_blank'>www.intramessenger.net/doc/authentification_externe.html</A><BR/></FONT>";
  }
  else
  {
    echo "Delays can appear on certain computers (clients), it's a DNS problem.<BR/>";
    echo "To solve it <A href='http://www.intramessenger.com/forum/viewtopic.php?t=378&' target='_blank'>read the forum</A>.<BR/>";
    echo "<BR/>";
    echo "<BR/>";
    echo "<BR/>";
    echo "<BR/>";
    echo "If you want to use extern authentication [*]</B><BR/>";
    echo "(with an existing forum, blog, CMS, CRM, ECM, ERP...)<BR/>";
    echo "<B>please read: ";
    echo "<A HREF='../doc/en/extern_authentication.html' target='_blank'>/doc/en/extern_authentication.html</A><BR/>";
    echo "</B><font face='verdana' size='2'>";
    echo "or <A HREF='http://www.intramessenger.net/doc/extern_authentication.html' target='_blank'>www.intramessenger.net/doc/extern_authentication.html</A><BR/></FONT>";
  }
  echo "\n";
  //
  //
  echo "<BR/>";
  echo "<BR/>";
  echo "<BR/>";
  echo "<BR/>";
  echo "<FORM METHOD='POST' ACTION='install.php?'>";
  echo "<INPUT TYPE='submit' VALUE = '" . $l_install_bt_next . "' class='liteoption' />";
  echo "<input type='hidden' name='lic' value = '" . $lic . "' />";
  echo "<input type='hidden' name='external_auth' value = '" . $external_auth . "' />";
  echo "<input type='hidden' name='step' value = '11' />";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
  echo "</FORM>";
  echo "<BR/>";
  echo "<BR/>";
  echo "<BR/>";
  echo "<BR/>";
  echo "<BR/>";
  echo "<BR/>";
  echo "</font></B></center><SMALL>";
  echo "[*] " . $l_admin_authentication_extern . " :<BR/></SMALL>";
  echo "<font face='verdana' size='1'>";
  //
  //
  require ("../common/extern/extern.inc.php");
  $lst = "";
  $extern_auth_list = array();
  $extern_auth_list = f_extern_auth_list();
  foreach ($extern_auth_list as $name) 
  {
    $lst .= $name . ", ";
  }
  $lst = substr($lst, 0, (strlen($lst)-2) ) . "...";
  echo $lst;
}




// ---------------------------------------------------------- STEP 11 ----------------------------------------------------------
function step_11()
{
  GLOBAL $lang, $c_OK, $c_not_found, $c_found, $c_on_ok, $c_on_ko, $c_off_ko, $c_off_ok, $lic, $external_auth, $steps;
  GLOBAL $l_menu_need_reg, $l_install_bt_next, $l_install_step, $l_admin_authentication_extern;
  //
  $if_prob = "OK";
  //
  echo "<font face='verdana' size='5' color='white'>";
  echo $l_install_step . " 11 : " . $steps[11] . "<BR/>";
  echo "</font>";

  /*
  table_title("xxxxx");
  $txt  = "<BR/>";
  $txt .= "<BR/>";
  $txt .= "<BR/>";
  table_col_1($txt);
  echo "</TABLE>";
  //
  echo "<BR/>";
  */

  echo "<BR/>";
  echo "<BR/>";
  echo "<B>";
  if ($lang == "FR")
  {
    echo "Consulter régulièrement la <A href='http://www.intramessenger.com/forum/viewforum.php?f=12&' target='_blank'>liste des nouveautés</A></B> <BR/>";
    echo "<B>ainsi que la <A href='http://www.intramessenger.com/forum/viewtopic.php?t=341&' target='_blank'>liste des prochaines améliorations</A>.<BR/>";
    echo "<BR/>";
    echo "<BR/>";
    echo "<A HREF='http://www.intramessenger.com/forum/' target='_blank'>Le forum officiel</A></B> : ";
    echo "n'hésitez pas à venir y proposer vos améliorations ainsi que vos traductions.<BR/>";
    echo "<BR/>";
    echo "<BR/>";
    echo "<B>Pour les problèmes/suggestions : <A HREF='http://www.intramessenger.net/contact.php?lang=FR&' target='_blank'>contacter le support</A><BR/>";
    echo "<BR/>";
    echo "<BR/>";
    echo "Vous pourrez aussi inscrire (gratuitement) votre serveur sur <BR/>";
    echo "<A HREF='http://www.intramessenger.net/list/servers/' target='_blank'>l'annuaire internet des serveurs publics</A><BR/>";
    echo "<BR/>";
    echo "<BR/>";
    echo "<BR/>";
    echo "<font color='green'>"; // face='verdana' size='2' 
    echo "L'installation est maintenant terminée.<BR/>";
    echo "Vous devriez maintenant supprimer ce répertoire (/install/)<BR/>";
    echo "et vous rendre au ";
    if (is_readable("../admin/check.php")) echo "<A HREF='../admin/check.php?lang=FR&'>";
    //
    echo "panneau d'administration</A> (<acronym title='Admin Control Panel'>ACP</acronym>) et y vérifier les options...";
    echo "<BR/></font>";
  }
  else
  {
    echo "Read regularly the <A href='http://www.intramessenger.com/forum/viewtopic.php?t=390&start=0&' target='_blank'>news list</A> on internet.<BR/>";
    echo "<BR/>";
    echo "<BR/>";
    echo "<A HREF='http://www.intramessenger.com/forum/' target='_blank'>The official forum</A></B>: ";
    echo "thanks to come post your improvements and translations.<BR/>";
    echo "<BR/>";
    echo "<BR/>";
    echo "<B>";
    echo "For problem/suggestion: <A HREF='http://www.intramessenger.net/contact.php?lang=EN&' target='_blank'>contact the support</A><BR/>";
    echo "<BR/>";
    echo "<BR/>";
    echo "You can (free) register your server on <A HREF='http://www.intramessenger.net/list/servers/' target='_blank'>the internet public servers directory</A><BR/>";
    echo "<BR/>";
    echo "<BR/>";
    echo "<BR/>";
    echo "<font color='green'>"; // face='verdana' size='2' 
    echo "Installation is now finished.<BR/>";
    echo "You may now delete this directory (/install/)<BR/>";
    echo "and go to ";
    if (is_readable("../admin/check.php")) 
      echo "<A HREF='../admin/check.php?lang=EN&'>";
    //
    echo "admin control panel (<acronym title='Admin Control Panel'>ACP</acronym>) to check options</A>...";
    echo "<BR/></font>";
  }
  //
  echo "<BR/>";
  echo "<BR/>";
  echo "<BR/>";
  echo "<FORM METHOD='GET' ACTION='../admin/check.php?'>";
  echo "<INPUT TYPE='submit' VALUE = '" . $l_install_bt_next . "' class='liteoption' />";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "'/>";
  echo "</FORM>";

}
?>