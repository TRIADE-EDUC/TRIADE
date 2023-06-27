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
require ("../common/display_errors.inc.php"); 
//
if (isset($_GET['tri'])) $tri = $_GET['tri'];  else  $tri = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
if (isset($_COOKIE['im_option_list_option_name'])) $option_show_option_name = $_COOKIE['im_option_list_option_name'];  else  $option_show_option_name = '';
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_roles);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_role_dashboard . "</title>";
display_header();
echo '<META http-equiv="refresh" content="120;url="> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
echo "<font face=verdana size=2>";
if ( _ROLES_TO_OVERRIDE_PERMISSIONS != '' )
{
  require ("../common/roles.inc.php");
  require ("../common/sql.inc.php");
  //
  //
  $nb_roles = 0;
  $requete  = " SELECT ID_ROLE, ROL_NAME, ROL_DEFAULT ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "ROL_ROLE ";
  //$requete .= " WHERE ROL_DEFAULT <> 'D' ";
  //$requete .= " ORDER BY UPPER(ROL_NAME) ";
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-G6m]", $requete);
  if ( mysqli_num_rows($result) > 0 )
  {
    while( list ($id_role, $role, $role_def) = mysqli_fetch_row ($result) )
    {
      $nb_roles++;
      $role_name[$nb_roles] = $role;
      $role_id[$nb_roles] = $id_role;
      $role_default[$nb_roles] = $role_def;
      $roles_interdits[$id_role] = "#";
      $roles_autorises[$id_role] = "#";
      $roles_value[$id_role] = "#";
    }
  }
  //
  //
  $requete  = " select MDL.MDL_NAME, RLM.ID_ROLE, RLM.RLM_STATE, RLM.RLM_VALUE ";
  $requete .= " FROM " . $PREFIX_IM_TABLE . "MDL_MODULE MDL, " . $PREFIX_IM_TABLE . "RLM_ROLEMODULE RLM ";
  $requete .= " WHERE MDL.ID_MODULE = RLM.ID_MODULE ";
  //$requete .= " ORDER BY ID_MODULE "; // MDL_NAME 
  $result = mysqli_query($id_connect, $requete);
  if (!$result) error_sql_log("[ERR-G6n]", $requete);
  if ( mysqli_num_rows($result) > 0 )
  {
    while( list ($mdl_name, $id_role, $t_state, $t_value) = mysqli_fetch_row ($result) )
    {
      if ($t_state == 1) $roles_interdits[$id_role] .= $mdl_name . "#";
      if ($t_state == 2) $roles_autorises[$id_role] .= $mdl_name . "#";
      if ($t_state == 3) $roles_value[$id_role] .= $mdl_name . ":" . $t_value . "#";  
    }
  }

	//echo "<BR/>";
	echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
    echo "<TR>";
    //echo "<TD align='center' COLSPAN='3' class='catHead'>";
    echo "<TH align='center' COLSPAN='" . ($nb_roles + 2)  ."' class='thHead'>";
    echo "&nbsp;<font face='verdana' size='3'><b>" . $l_admin_role_dashboard . "</b></font>&nbsp;";
    echo "</TH>";
    echo "</TR>";
    $requete  = " select MDL_NAME, ID_MODULE, MDL_MAX_VALUE, MDL_OTHER, MDL_ROLE ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "MDL_MODULE ";
    $requete .= " WHERE 1 = 1 "; // pour juste en dessous !
    if (_SHOUTBOX == "") $requete .= " and MDL_NAME not like 'SHOUTBOX_%' ";  // on affiche que la première option (pour voir qu'elle n'est pas active)
    if (_BOOKMARKS == "") $requete .= " and MDL_NAME not like 'BOOKMARKS_%' ";  // on affiche que la première option (pour voir qu'elle n'est pas active)
    if (_SHARE_FILES == "") $requete .= " and MDL_NAME not like 'SHARE_FILES_%' ";  // on affiche que la première option (pour voir qu'elle n'est pas active)
    //$requete .= " ORDER BY ID_MODULE "; // MDL_NAME 
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-G6p]", $requete);
    if ( mysqli_num_rows($result) > 0 )
    {
      echo "<TR>";
        echo "<TD align='center' colspan='2' class='catHead'><b>";
          echo $l_menu_options;
        echo "</b></TD>";
        echo "<TD align='center' colspan='" . $nb_roles  ."' class='catHead'><b>";
          echo $l_admin_roles_title;
        echo "</b></TD>";
      echo "</TR>";
      //
      echo "<TR>";
        echo "<TD align='center' class='catHead'>&nbsp;<b>";
          echo $l_admin_users_col_etat;
        echo "</b>&nbsp;</TD>";
        echo "<TD align='center' class='catHead'><b>";
          echo $l_admin_options_col_option;
        echo "</b></TD>";
        for ($i = 1; $i <= $nb_roles; $i++) 
        {
          echo "<TD align='center' class='catHead'>&nbsp;";
            if ($role_default[$i] == "D") echo "<i>";
            echo $role_name[$i]; // $l_admin_users_col_etat;
          echo "</b>&nbsp;</TD>";
        }
      echo "</TR>";
      //
      $coul = "row2";
      while( list ($mdl_name, $id_module, $max_value, $t_other, $mdl_is_role) = mysqli_fetch_row ($result) )
      {
        if ($coul == "row2") $coul = "row1";  else  $coul = "row2"; // lignes paires/impaires
        $option_value = "";
        //
        echo "<TR>";
        if ($max_value > 0)
        {
          echo "<TD align='right' class='" . $coul . "'>";
          echo "<font face='verdana' size='1'>";
          $option_value = f_option_value($mdl_name);
          echo $option_value . "&nbsp;";
        }
        else
        {
          $state_option = f_option_activated($mdl_name);
          //echo "<TD align='center' class='row1'>";
          if ($mdl_is_role == "")  //if ( ($state_option == 1) or ($state_option == 2) )
            echo "<TD align='center' class='row1'>";
          else
            echo "<TD align='center' class='rowpic'>";  // 'row3'  ex: ROLE_GET_ADMIN_ALERT_MESSAGES
          //
            if ($state_option == 1) echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_gray.gif" . "' TITLE='" . $l_admin_options_legende_empty . "' WIDTH='16' HEIGHT='16'>";
            if ($state_option == 2) echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_green.gif" . "' TITLE='" . $l_admin_options_legende_not_empty . "' WIDTH='16' HEIGHT='16'>";
            //if (intval($state_option <= 0))  echo "&nbsp;"; // important pour hauteur de ligne // ($mdl_is_role != "")
            if ($mdl_is_role != "")  echo "&nbsp;"; // important pour hauteur de ligne (ROLE) 
        }
        echo "</TD>";
        //
        echo "<TD class='" . $coul . "'>";
          echo "<font face='verdana' size='1'>&nbsp;";
          $info = f_option_label($mdl_name);
          if ( ($option_show_option_name != "") and ($info != "")  and  ($mdl_is_role == "") )
          {
            if ($info != "") echo "<IMG SRC='" . _FOLDER_IMAGES . "information.png' WIDTH='16' HEIGHT='16' TITLE=\"" . $info . "\" />&nbsp;";
            echo $mdl_name . "&nbsp;";
          }
          else
          {
            if ($mdl_is_role == "")  echo "<IMG SRC='" . _FOLDER_IMAGES . "information.png' WIDTH='16' HEIGHT='16' TITLE=\"" . $mdl_name . "\" />&nbsp;";
            //
            echo $info . "&nbsp;";
          }
        echo "</TD>";
        //
        for ($i = 1; $i <= $nb_roles; $i++) 
        {
          $id_role = $role_id[$i];
          // Pour les permissions équivalentes aux options, donc inutiles :
          $couleur = $coul;
          if ( ($role_default[$i] == "D") and ($t_other == "") ) $couleur = 'rowpic'; //  'row3'    col "others"
          $add_info = "";
          // Si valeur numeric :
          if ($max_value > 0)
          {
            $tval = "";
            $t = strpos(" " . $roles_value[$id_role], "#" . $mdl_name . ":");
            if ($t > 0) 
            {
              $t2 = strpos(" " . $roles_value[$id_role], "#", $t+1);
              $tval = trim(substr($roles_value[$id_role], $t, $t2-$t-1));
              $tval = substr($tval, -1, strlen($tval) - strlen($mdl_name));
            }
            if ($tval == $option_value) $couleur = 'row3';
            echo "<TD align='center' class='" . $couleur . "'>";
              if ($tval <> "")
                echo "<font face='verdana' size='2'>" . $tval; 
              else
                echo "&nbsp;";
              //echo $tval; // . "&nbsp;";
            echo "</TD>";
          }
          else
          {
            if ( ( ($state_option == 1) and (strstr($roles_interdits[$id_role], "#" . $mdl_name . "#")) ) or
                 ( ($state_option == 2) and (strstr($roles_autorises[$id_role], "#" . $mdl_name . "#")) ) )
            {
              $couleur = 'row3';
              $add_info = " - " . $l_admin_role_useless_permission; // $l_admin_log_type_error; $l_admin_users_no_add_1;
            }
            //
            $timage = "";
            echo "<TD align='center' class='" . $couleur . "'>";
              if (strstr($roles_autorises[$id_role], "#" . $mdl_name . "#")) $timage = "<IMG SRC='" . _FOLDER_IMAGES . "b_ok_2.png' TITLE='" . $l_admin_role_permission_on . $add_info . "' WIDTH='16' HEIGHT='16' BORDER='0' />";
              if (strstr($roles_interdits[$id_role], "#" . $mdl_name . "#")) $timage = "<IMG SRC='" . _FOLDER_IMAGES . "b_disalow.png' TITLE='" . $l_admin_role_permission_off . $add_info  . "' WIDTH='16' HEIGHT='16' BORDER='0' />";
              if ($timage <> "")
                echo $timage; 
              else
                echo "&nbsp;";
            echo "</TD>";
          }
        }
        
        echo "</TR>";
      }
    }
    else
    {
      //require ("../common/roles.inc.php");
      fill_table_module();
    }
	//
	echo "</TABLE>";	//
  //
  mysqli_close($id_connect);
  //
  echo "<BR/>";
  echo "<BR/>";

  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
    echo "<FORM METHOD='GET' name='formulaire_cookies' ACTION ='set_cookies.php?'>";
    echo "<TR><TD COLSPAN='2' ALIGN='CENTER' class='catHead'>";
    echo "<B>" . $l_admin_display_title . "</B></TD></TR>";
    echo "</TD></TR>";
    echo "<TR><TD COLSPAN='2' class='row1'>";
      echo "<font face='verdana' size='2'>";
      echo "<INPUT name='option_show_option_name' id='option_show_option_name' TYPE='CHECKBOX' VALUE='1' class='genmed' ";
      if ($option_show_option_name <> '') echo "CHECKED";
      echo " />";
      echo "<label for='option_show_option_name'>" . $l_admin_options_show_option_name . "</label>"; //"<BR/>\n";
      echo "&nbsp;<IMG SRC='" . _FOLDER_IMAGES . "information.png' WIDTH='16' HEIGHT='16' TITLE='" . $l_admin_options_show_option_name ."' ALT='" . $l_admin_options_show_option_name ."' />&nbsp;";
    echo "</TD></TR>";
    echo "<TR><TD COLSPAN='2' ALIGN='CENTER' class='catBottom'>";
    echo "<input type='hidden' name='action' value = 'role_permissions_list' />"; // les paramètres de cette page, et y revenir ensuite
    echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
    echo "<INPUT class='liteoption' TYPE='submit' VALUE ='" . $l_admin_bt_update . "' />";
    echo "</TD>";
    echo "</TR>";
    echo "</FORM>";
  echo "</TABLE>";
}
else
{
  echo "<BR/>";
  echo "<div class='warning'>";
  echo $l_admin_roles_cannot_use . "<BR/>";
  echo "</div>";
}
//
display_menu_footer();
//
echo "</body></html>";
?>