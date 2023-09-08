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
if (isset($_REQUEST['lang'])) $lang = $_REQUEST['lang']; else $lang = "";
if (isset($_REQUEST['step'])) $step = intval($_REQUEST['step']); else $step = 0;
if (isset($_REQUEST['lic'])) $lic = $_REQUEST['lic']; else $lic = "";
if (isset($_REQUEST['dbengine'])) $dbengine = $_REQUEST['dbengine']; else $dbengine = "";
if (isset($_REQUEST['external_auth'])) $external_auth = $_REQUEST['external_auth']; else $external_auth = "";
if (isset($_POST['adm_username'])) $adm_username = $_POST['adm_username'];  else $adm_username = "";
if (isset($_POST['adm_pass'])) $adm_pass = trim($_POST['adm_pass']);  else $adm_pass = "";
if (isset($_POST['adm_pass_2'])) $adm_pass_2 = trim($_POST['adm_pass_2']);  else $adm_pass_2 = "";
//
// class="error" (manque le rouge en couleur d'écriture)
//
define('INTRAMESSENGER',true);
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "\n";
echo "<html><head>";
echo "<title>IntraMessenger - Setup</title>";
echo '<LINK REL="SHORTCUT ICON" HREF="../images/favicon.ico">';
echo '<META NAME="ROBOTS" CONTENT="NOARCHIVE">';
echo '<META NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW">';
echo "\n";
echo '<META NAME="Author" CONTENT="THeUDS.com">';
echo '<META NAME="copyright" content="THeUDS.com">';
echo '<meta http-equiv="Content-Type" content="text/html;charset=iso-8859-1" />';
echo '<meta http-equiv="Content-Style-Type" content="text/css">';
require ("../common/styles/style.css.inc.php");
echo "<link href='../common/styles/subSilverPlus.css' rel='stylesheet' media='screen, print' type='text/css'/>";
echo "<link href='../common/styles/default/menu_class.css' rel='stylesheet' media='screen, print' type='text/css'/>";
?>

<script type="text/javascript">
<!--
function select_auth_mode() 
{
 if (document.formauth.auth_mode[1].checked == true)
 {
   document.getElementById('id_auth_mode_2').style.display="block";
 }
 else
 {
   document.getElementById('id_auth_mode_2').style.display="none";
 }
}
function select_external_auth() 
{
 if (document.formauth.external_auth.selectedIndex >= 0 )
 {
   document.formauth.auth_mode[1].checked = true;
   document.getElementById('id_auth_mode_2').style.display="block";
  }
}
//-->
</script>


<?php
echo "</head>";
echo "\n";
//echo "<body background='" . _FOLDER_IMAGES . f_background_image_color() . "background_left.png' bgcolor='#FCFDFF'>";
echo "<body background='" . _FOLDER_IMAGES . "blue/background_left.png' bgcolor='#FCFDFF'>";
require ("lang.inc.php");
#require ("../common/menu.inc.php"); // non, surtout pas ! 
//
	if ($lang == "FR")
    $steps = array(
            0 => 'Préambule',
            1 => 'Licence',
            2 => 'Vérification du système',
            3 => 'Type d&#146;installation',
            4 => 'Configuration serveur',
            5 => 'Connexion au serveur',
            6 => 'Création des tables',
            7 => 'Principales options',
            8 => 'Création administrateur',
            9 => 'Vérification de sécurité',
            10 => 'Informations importantes',
            11 => 'Fin de l\'installation',
            );
	else
    $steps = array(
            0 => 'Introduction',
            1 => 'Licence',
            2 => 'System Check',
            3 => 'Installation type',
            4 => 'Database Setup',
            5 => 'Connection to database',
            6 => 'Create tables',
            7 => 'Main settings',
            8 => 'Create ACP administrator',
            9 => 'Security Check',
            10 => 'Important information',
            11 => 'Finish Installation',
            ); // General settings
//
function table_title($title)
{
	echo "<SMALL><BR/></SMALL>";
	echo "<table width='90%' class='forumline' cellspacing='1' cellpadding='1'>";
	echo "<TR>";
	echo "<TH colspan='2' class='thHead'>";
	echo "<FONT size='3'>";
	echo $title;
	echo "</TH>";
	echo "</TR>";
}
//
function table_col_1($text)
{
	echo "<TR>";
	echo "<TD width='' class='row2'>";
	echo "<FONT size='2'>";
	echo $text;
	echo "</TD>";
}
//
function table_col_2($etat)
{
	echo "<TD width='20' class='row1' ALIGN='CENTER'>";
	if ($etat == 'OK')
		echo "<IMG SRC='" . _FOLDER_IMAGES . "ok.gif' WIDTH='16' HEIGHT='17' ALT='OK' TITLE='OK'>";
	else
		echo "<IMG SRC='" . _FOLDER_IMAGES . "ko.gif' WIDTH='17' HEIGHT='17' ALT='Not OK !' TITLE='Not OK !'>";
	//
	echo "</TD>";
	echo "</TR>";
}
//
function table_col_vide()
{
	echo "<TD width='20' class='row1' ALIGN='CENTER'>";
  echo "&nbsp;";
	echo "</TD>";
	echo "</TR>";
}
//
function f_add_file_missing($const, $dt_add)
{
	$t = "<I>" . $const . "</I> (added : " . $dt_add . ") : <FONT color='RED'><B>missing</B></FONT><BR/>";
	//
	return $t;
}
//
function steps_list($step)
{
  global $lang, $steps;
  //
	$html = "<br/>";
	
	//while( list( $num, $name ) = each( $steps ) )
	foreach ($steps as $num => $name)
	{
    if ( $step == $num )
    {
      $html .= "&nbsp;<b><span class='select'>". $name ."</span></b><br/><br/>"; // <a href='install.php?step=". $num ."'>
    }
    else
    {
      $html .= "&nbsp;" . $name ."<br/><br/>"; // "<a href='install.php?step=". $num ."'>". 
    }
	}
   //
	return $html;
}

$c_OK = "<B><FONT COLOR='GREEN'>OK</B></FONT>";
$c_not_found = "<B><FONT COLOR='RED'>" .$l_admin_check_not_found . "</FONT></B>";
$c_found = "<B><FONT COLOR='GREEN'>" . $l_admin_check_found . "</FONT></B>";
$c_on_ok = "<B><FONT COLOR='GREEN'>" . $l_admin_check_on . "</FONT></B>";
$c_on_ko = "<B><FONT COLOR='RED'>" . $l_admin_check_on . "</FONT></B>";
$c_off_ko = "<B><FONT COLOR='RED'>" . $l_admin_check_off . "</FONT></B>";
$c_off_ok = "<B><FONT COLOR='GREEN'>" . $l_admin_check_off . "</FONT></B>";
//$if_prob = "OK";
//
require ("install.inc.php");
//
//
echo "<TABLE BORDER='0' WIDTH='100%' height='100%' cellspacing='0' cellpadding='2'>";
echo "<TR>";
//echo "<TD COLSPAN='2' BGCOLOR='#709BC8' ALIGN='CENTER' HEIGHT='55' background='" . _FOLDER_IMAGES . f_background_image_color() . "background_top.png'>";
echo "<TD COLSPAN='2' BGCOLOR='#709BC8' ALIGN='CENTER' HEIGHT='55' background='" . _FOLDER_IMAGES . "blue/background_top.png'>";
  echo "<font face='verdana' size='6' color='blue'>";
  if ($lang == "FR")
    echo "Installation serveur IntraMessenger";
  else
    echo "Install IntraMessenger server";
echo "</TD>";
echo "</TR>";

echo "<TR>";
//echo "<TD WIDTH='200' VALIGN='TOP' BGCOLOR='#D9E2EC' class='menu_left' background='" . _FOLDER_IMAGES . f_background_image_color() . "background_left.png'>"; // Menu à gauche
echo "<TD WIDTH='200' VALIGN='TOP' BGCOLOR='#D9E2EC' class='menu_left' background='" . _FOLDER_IMAGES . "blue/background_left.png'>"; // Menu à gauche
  echo "<CENTER>";
    echo "<font face='verdana' size='2'>";
    /*
		//if ($lang != 'FR') 
		echo " <A HREF='?lang=FR&step=" . $step . "&' TITLE='Français'><IMG SRC='../images/flags/fr.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
		//if ($lang != 'EN') 
		echo " <A HREF='?lang=EN&step=" . $step . "&' TITLE='English'><IMG SRC='../images/flags/us.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
		//if ($lang != 'IT') echo " <A HREF='?lang=IT&' TITLE='Italian'><IMG SRC='../images/flags/it.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
		//if ($lang != 'PT') echo " <A HREF='?lang=PT&' TITLE='Portuguese'><IMG SRC='../images/flags/pt.png' WIDTH='18' HEIGHT='12' BORDER='0' ALIGN=''></A>";
    echo "<BR/>";
    */

  if ( (intval($step) >= 2) and ($lic != "ok") ) $step = 1;

  echo "</CENTER>";
  echo steps_list($step);
  echo "<BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/><BR/>";

	echo "</TD>";
	//
	//echo "<TD VALIGN='TOP' BGCOLOR='#EAEDF4' background='" . _FOLDER_IMAGES . f_background_image_color() . "background.jpg'>"; // La page...
	echo "<TD VALIGN='TOP' BGCOLOR='#EAEDF4' background='" . _FOLDER_IMAGES . "blue/background.jpg'>"; // La page...
    echo "<CENTER>"; 

    //echo "<div class='notice'><FONT COLOR='RED'>zzzzzzzzz</font></div>";
    //echo "<div class='notice'><FONT COLOR='RED'>zzzzzzzzz</font></div>";
    //
    $step_3_ok = "";
    if (intval($step) == 4)
    {
      if (isset($_POST['auth_mode'])) $auth_mode = intval($_POST['auth_mode']); else $auth_mode = 0;
      if (isset($_POST['external_path'])) $external_path = intval($_POST['external_path']); else $external_path = 0;
      if (isset($_POST['external_auth'])) $external_auth = $_POST['external_auth']; else $external_auth = "";
      if (isset($_POST['external_path_value'])) $external_path_value = $_POST['external_path_value']; else $external_path_value = "";
      if ($auth_mode <> 1)
      {
        if ($external_auth == "") $step_3_ok = "KO";
        if ($external_path_value == "") $step_3_ok = "KO";
        if (($external_path <> 1) and ($external_path <> 2)) $step_3_ok = "KO";
        //
        if ($step_3_ok == "") 
        {
          require("extauth.inc.php");
          if ($fichier == "")  $external_path = "KO";
          if ($chemin == "KO") 
          {
            echo "<div class='notice'><FONT COLOR='RED'>Cannot find file: </font>" . $fichier . "</div>";
            $step_3_ok = "KO";
          }
          if ( ($fichier != "") and ($chemin != "KO") )
          {
            if ( ($dbhost == "") or ($database == "") or ($dbuname == "") )   $external_path = "KO";
          }
          //
          if ($external_path == "KO") echo "<div class='notice'>You may manualy set this external authentication AFTER finish install IntraMessenger...</div>";
        }
        else
          echo "<div class='notice'><FONT COLOR='RED'>Error, choose again...</font></div>";
      }
      if ($step_3_ok != "") 
      {
        $step = 3; // echec 
      }
    }
    //
    //
    $step_4_ok = "";
    if (intval($step) == 5)
    {
      if (isset($_POST['dbhost'])) $dbhost = $_POST['dbhost']; else $dbhost = "";
      if (isset($_POST['dbuname'])) $dbuname = $_POST['dbuname']; else $dbuname = "";
      if (isset($_POST['dbpass'])) $dbpass = $_POST['dbpass']; else $dbpass = "";
      if (isset($_POST['dbpass2'])) $dbpass2 = $_POST['dbpass2']; else $dbpass2 = "";
      if (isset($_POST['database'])) $database = $_POST['database']; else $database = "";
      if (isset($_POST['prefix'])) $prefix = $_POST['prefix']; else $prefix = "";
      if ( ($dbpass == $dbpass2) and ($dbhost != "") and ($dbuname != "") and ($database != "") and ($prefix != "") )
      {
    		$fp = fopen("../common/config/mysql.config.inc.php", "w"); 
        if (flock($fp, 2)); 
        { 
          fputs($fp, "<?php " . "\r\n"); 
          fputs($fp, "/*******************************************************" . "\r\n"); 
          fputs($fp, " **                  IntraMessenger - server          **" . "\r\n"); 
          fputs($fp, " **                                                   **" . "\r\n"); 
          fputs($fp, " **  Copyright:      (C) 2006 - 2019 THeUDS           **" . "\r\n"); 
          fputs($fp, " **  Web:            http://www.theuds.com            **" . "\r\n"); 
          fputs($fp, " **                  http://www.intramessenger.net    **" . "\r\n"); 
          fputs($fp, " **  Licence :       GPL (GNU Public License)         **" . "\r\n"); 
          fputs($fp, " **  http://opensource.org/licenses/gpl-license.php   **" . "\r\n"); 
          fputs($fp, " *******************************************************/" . "\r\n"); 
          fputs($fp, "" . "\r\n"); 
          fputs($fp, "/*******************************************************" . "\r\n"); 
          fputs($fp, " **       This file is part of IntraMessenger-server  **" . "\r\n"); 
          fputs($fp, " **                                                   **" . "\r\n"); 
          fputs($fp, " **  IntraMessenger is a free software.               **" . "\r\n"); 
          fputs($fp, " **  IntraMessenger is distributed in the hope that   **" . "\r\n"); 
          fputs($fp, " **  it will be useful, but WITHOUT ANY WARRANTY.     **" . "\r\n"); 
          fputs($fp, " *******************************************************/" . "\r\n"); 
          fputs($fp, "" . "\r\n"); 
          fputs($fp, "if ( !defined('INTRAMESSENGER') ) die(); ");
          fputs($fp, "" . "\r\n"); 
          fputs($fp, "" . "\r\n"); 
          fputs($fp, "$" . "dbhost = '" . $dbhost . "'; \r\n"); 
          fputs($fp, "$" . "dbport = ''; \r\n"); 
          fputs($fp, "$" . "dbuname = '" . $dbuname . "'; \r\n"); 
          fputs($fp, "$" . "dbpass = '" . $dbpass . "'; \r\n"); 
          fputs($fp, "$" . "database = '" . $database . "'; \r\n"); 
          fputs($fp, "$" . "PREFIX_IM_TABLE = '" . $prefix . "'; \r\n"); 
          fputs($fp, "" . "\r\n"); 
          fputs($fp, "?>"); 
          flock($fp, 3); 
        } 
        fclose($fp); 
      }
      else
      {
        $step = 4; // echec 
        $step_4_ok = "KO";
      }
    }
    //
    //
    $step_8_ok = "";
    if (intval($step) == 9)
    {
      require ("../common/functions.inc.php");
      require ("../common/acp_auth.inc.php");
      //
      $adm_username = f_clean_username($adm_username);
      if ( (strlen($adm_username) > 2) and (strlen($adm_pass) > 5) and ($adm_pass == $adm_pass_2) )
      {
        require ("../common/sql.inc.php");
        //
        // Voir aussi /admin/admin_acp_add.php
        //
        $cannot = "";
        $requete  = " select LOWER(ADM_USERNAME) ";
        $requete .= " from " . $PREFIX_IM_TABLE . "ADM_ADMINACP ";
        $requete .= " WHERE LOWER(ADM_USERNAME) like '" . strtolower($adm_username) . "' ";
        $result = mysqli_query($id_connect, $requete);
        if (!$result) error_sql_log("[ERR-P1b]", $requete);
        if ( mysqli_num_rows($result) > 0 )
        {
          while( list ($t_name) = mysqli_fetch_row ($result) )
          {
            if ($t_name == strtolower($adm_username)) $cannot = "X";
          }
        }
        if ($cannot == "")
        {
          $adm_salt = random(20);
          $pass_cr = chiffrer_pass($adm_pass, $adm_salt);
          //
          $requete  = " insert into " . $PREFIX_IM_TABLE . "ADM_ADMINACP ";
          $requete .= " (ADM_USERNAME, ADM_PASSWORD, ADM_SALT, ADM_LEVEL, ADM_DATE_CREAT, ADM_DATE_PASSWORD ) ";
          $requete .= " values ('" . $adm_username . "', '" . $pass_cr . "', '" . $adm_salt . "', 1048575, CURDATE(), CURDATE() ) ";
          $result = mysqli_query($id_connect, $requete);
          if (!$result) error_sql_log("[ERR-P1c]", $requete);
        }
        else
        {
          $step = 8; // echec 
          $step_8_ok = "KO";
        }
      }
      else
      {
        $step = 8; // echec 
        $step_8_ok = "KO";
      }
    }
    //
    //
    
    
    //
    //echo $external_auth;
    switch(intval($step))
    {
      case 1:
        step_1();
        break;
      case 2:
        step_2();
        break;
      case 3:
        step_3();
        break;
      case 4:
        step_4();
        break;
      case 5:
        step_5();
        break;
      case 6:
        step_6();
        break;
      case 7:
        step_7();
        break;
      case 8:
        step_8();
        break;
      case 9:
        step_9();
        break;
      case 10:
        step_10();
        break;
      case 11:
        step_11();
        break;

      default:
        step_0();
        break;
    }

  echo "</TD>";
echo "</TR>";
echo "<TR>";
include ("../common/constant.inc.php");
echo "<TD COLSPAN='2' ALIGN='CENTER' BGCOLOR='#FCFDFF' HEIGHT='40'>"; // F4F4F4
  if ($lang == "FR")
    echo "<span class='copyright'>Installation d'<a href='http://www.intramessenger.net/' target='_blank' class='copyright' alt='THeUDS.com' title='THeUDS.com'>IntraMessenger</A>-serveur (" . _SERVER_VERSION . ")</SPAN>";
  else
    echo "<span class='copyright'><a href='http://www.intramessenger.net/' target='_blank' class='copyright' alt='THeUDS.com' title='THeUDS.com'>IntraMessenger</A> server (" . _SERVER_VERSION . ") setup </SPAN>";
echo "</TD>";
echo "</TR>";
echo "</TABLE>";
  




//
if (defined("_MAINTENANCE_MODE"))
{
  if (_MAINTENANCE_MODE == '')
  {
    //echo "<BR/>";
    //echo "<BR/>";
    //echo "<BR/>";
    //echo "<BR/>";
    echo '<p class="">' . $l_install_not_in_maintenance_mode . '<BR/>';
    echo $l_install_warning;
    //echo "use /admin/check.php to verify configuration</p></div>";
    //echo "</body></html>";
    //die();
  }
}

//
echo "<BR/>";
echo "</body></html>";
?>