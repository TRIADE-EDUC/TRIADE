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
if (isset($_COOKIE['im_messagerie_show_order'])) $im_messagerie_show_order = $_COOKIE['im_messagerie_show_order'];  else  $im_messagerie_show_order = '1';
if (intval($im_messagerie_show_order) <= 0) $im_messagerie_show_order = "";
//
//if (isset($_GET['tri'])) $tri = $_GET['tri'];  else  $tri = "";
if (isset($_GET['id_user_select'])) $id_user_select = intval($_GET['id_user_select']);  else  $id_user_select = 0;
if (isset($_GET['send_ok'])) $send_ok = $_GET['send_ok'];  else  $send_ok = "";
if (isset($_GET['send_nb'])) $send_nb = $_GET['send_nb'];  else  $send_nb = "";
//if (isset($_GET['delete_ok'])) $delete_ok= $_GET['delete_ok'];  else  $delete_ok = "";
if (isset($_GET['nm_image'])) $nm_image = $_GET['nm_image']; else $nm_image = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['action'])) $action = $_GET['action']; else $action = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_admin_messages);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_mess_title . "</title>";
display_header();
if ( ($send_ok == 'ok') and ($send_nb > 0) )
	echo '<META http-equiv="refresh" content="40;url="> ';
echo "</head>";
echo "<body>";
//
display_menu();
//
require ("../common/sql.inc.php");
//
?>

<script type="text/javascript">
<!--
function verification_choix() 
{
 if (document.formulaire.id_dest.selectedIndex >= 0 )  document.formulaire.dest[0].checked = true
}
function verification_choix_2() 
{
 if (document.formulaire_2.id_dest.selectedIndex >= 0 )  document.formulaire_2.dest[0].checked = true
}
function verification_choix_3a() 
{
 if (document.formulaire.id_group_dest1.selectedIndex >= 0 )  document.formulaire.dest[1].checked = true
}
function verification_choix_3b() 
{
 if (document.formulaire.id_group_dest2.selectedIndex >= 0 )  document.formulaire.dest[2].checked = true
}
function verification_choix_4() 
{
 if (document.formulaire_2.id_group_dest.selectedIndex >= 0 )  document.formulaire_2.dest[2].checked = true
}
//-->
</script>

<?php
$hide_ip = "";
$repertoire  = getcwd() . "/"; 
if ( (substr_count($repertoire, "/admin_demo/") > 0) or (substr_count($repertoire, "\admin_demo/") > 0) ) $hide_ip = "X";
//
echo "<BR/>";
echo "<table width='650' cellspacing='1' cellpadding='1' class='forumline'>";
echo "<FORM METHOD='POST' name='formulaire' ACTION ='message_send.php?'>";
echo "<TR>";
echo "<TH colspan='3' class='thHead'>";
echo "<FONT size='3'>";
echo $l_admin_mess_title_2;
echo "</TH>";
echo "</TR>";

echo "<TR>";
echo "<td width='25%' class='row2'><FONT size='2'>&nbsp;<b>" . $l_admin_mess_message . " :</b>";
echo "</TD>";
echo "<TD width='70%' class='row1' VALIGN='MIDDLE'>";
//echo "&nbsp;<input name='txt' size='66' type='text' class='post' tabindex='1'"; // maxlength='200' 
if ($nm_image != '')
{
  echo "&nbsp;<input name='txt' size='66' type='text' class='post' tabindex='1'"; // maxlength='200' 
	echo "value='SendImage:" . $nm_image . "' />";
}
else
{
  echo "&nbsp;<TEXTAREA name='txt' type='text' class='post' cols='66' rows='3' tabindex='1' >"; // maxlength='200' 
  echo "</TEXTAREA>";
	//echo "value=''>";
}
echo "</TD>";

echo "<TD width='5%' class='row1' VALIGN='MIDDLE'>";
echo "&nbsp;<A HREF='messagerie_image.php?id_user_select=" . $id_user_select . "&lang=" . $lang . "&' BORDER='0'>";
//echo "<IMG SRC='" . _FOLDER_IMAGES . "b_image.png' WIDTH='14' HEIGHT='13' ALT='" . $l_admin_mess_title_4 . "' TITLE='" . $l_admin_mess_title_4 . "' BORDER='0' VALIGN='MIDDLE'></A>";
echo "<IMG SRC='" . _FOLDER_IMAGES . "bt_image.png' WIDTH='22' HEIGHT='22' ALT='" . $l_admin_mess_title_4 . "' TITLE='" . $l_admin_mess_title_4 . "' BORDER='0' VALIGN='MIDDLE'></A>";
echo "</TD>";

echo "</TR>";

echo "<TR>";
echo "<td VALIGN='MIDDLE' class='row2'><FONT size='2'>&nbsp;<b>" . $l_admin_mess_to . " :</b>";
echo "</TD>";
echo "<td class='row1' colspan='2'>";
echo "<FONT size='2'>";
echo "<INPUT name='dest' id='dest_1' TYPE='radio' VALUE='1' class='genmed' tabindex='2'";
if ($id_user_select > 0)
	echo "CHECKED";
echo " /> ";
echo "<label for='dest_1'>" . $l_admin_mess_only . "</label> : ";
echo "\n";
echo " <select name='id_dest' tabindex='3' onChange='verification_choix()'> ";

		$requete  = " SELECT SQL_CACHE USR_USERNAME, USR_NICKNAME, USR_NAME, ID_USER ";
		$requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
		//$requete .= " WHERE ( (USR_CHECK <> 'WAIT' and USR_CHECK <> '') or USR_STATUS = 1 ) ";
		$requete .= " WHERE USR_STATUS = 1 ";
		$requete .= " ORDER BY USR_USERNAME, USR_NAME ";
		$result = mysqli_query($id_connect, $requete);
		if (!$result) error_sql_log("[ERR-C1a]", $requete);
		if ( mysqli_num_rows($result) != 0 )
		{
			while( list ($username, $nickname, $nom, $id_user) = mysqli_fetch_row ($result) )
			{
				if ( ($nickname != '') and (_ALLOW_UPPERCASE_SPACE_USERNAME != '') ) $username = $nickname;
				echo "<option value='" . $id_user . "' class='genmed' ";
				if ($id_user_select == $id_user)
					echo "SELECTED";
				echo ">" . $username;
				if ( ($nom != '') and ($nom != 'HIDDEN') )
					echo " &nbsp; [" . $nom . "]";
				//
				echo "</option>";
			}
		}
		
echo "</select>";
echo "<BR/>";
echo "\n";

if ( ( _SPECIAL_MODE_GROUP_COMMUNITY != '' ) or ( _SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '' ) or ( _GROUP_FOR_SBX_AND_ADMIN_MSG != '') ) // /*-
{
  echo "<INPUT name='dest' id='dest_2' TYPE='radio' VALUE='2' class='genmed' /> ";
  echo "<label for='dest_2'>" . $l_admin_mess_group_connected . "</label> : ";
  echo " <select name='id_group_dest1' onChange='verification_choix_3a()'> ";
    $liste_groupes = "";
    $requete  = " SELECT SQL_CACHE distinct(GRP_NAME), GRP.ID_GROUP ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "GRP_GROUP GRP, " . $PREFIX_IM_TABLE . "USG_USERGRP USG ";
    $requete .= " WHERE GRP.ID_GROUP = USG.ID_GROUP ";
    $requete .= " order by GRP_NAME ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-C1e]", $requete);
    if ( mysqli_num_rows($result) != 0 )
    {
      while( list ($group, $id_group) = mysqli_fetch_row ($result) )
      {
        $liste_groupes .= "<option value='" . $id_group . "' class='genmed' >" . $group;
        $liste_groupes .= "</option>";
      }
      echo $liste_groupes;
    }
  echo "</select>";
  echo "<BR/>";
  //
  //
  echo "<INPUT name='dest' id='dest_3' TYPE='radio' VALUE='3' class='genmed'  /> ";
  echo "<label for='dest_3'>" . $l_admin_mess_group . "</label> : ";
  echo " <select name='id_group_dest2' onChange='verification_choix_3b()'> " . $liste_groupes;
    /*
    $requete  = " SELECT SQL_CACHE distinct(GRP_NAME), GRP.ID_GROUP ";
    $requete .= " FROM " . $PREFIX_IM_TABLE . "GRP_GROUP GRP, " . $PREFIX_IM_TABLE . "USG_USERGRP USG ";
    $requete .= " WHERE GRP.ID_GROUP = USG.ID_GROUP ";
    $requete .= " order by GRP_NAME ";
    $result = mysqli_query($id_connect, $requete);
    if (!$result) error_sql_log("[ERR-C1e]", $requete);
    if ( mysqli_num_rows($result) != 0 )
    {
      while( list ($group, $id_group) = mysqli_fetch_row ($result) )
      {
        echo "<option value='" . $id_group . "' class='genmed' >" . $group;
        echo "</option>";
      }
    }
    */
  echo "</select>";
  echo "<BR/>";
}
	

//echo "<INPUT name='dest' TYPE='radio' VALUE='4' class='genmed' /> " . $l_admin_mess_all_connected . " : <font color='green'><I>" . $l_admin_session_info_online . "</I></font> ";
echo "<INPUT name='dest' id='dest_4' TYPE='radio' VALUE='4' class='genmed' /> ";
echo "<label for='dest_4'>" . $l_admin_mess_all_connected . "</label> : "; //<I>" . $l_admin_session_info_online . "</I></font> ";
echo "<A HREF='list_sessions.php?tri=&only_status=o&lang=" . $lang . "&'>" . $l_admin_session_info_online . "</A>";
//echo " <IMG SRC='" . _FOLDER_IMAGES . "bt_green.gif' BORDER='0' WIDTH='16' HEIGHT='16' ALT='" . $l_admin_session_info_online . "' TITLE='" . $l_admin_session_info_online . "'></A>";
echo "<BR/>";

echo "<INPUT name='dest' id='dest_5' TYPE='radio' VALUE='5' class='genmed'";
if ($id_user_select <= 0)
	echo "CHECKED";
echo " /> ";
echo "<label for='dest_5'>" . $l_admin_mess_all_connected . "</label>";
echo "<BR/>";

echo "<INPUT name='dest' id='dest_6' TYPE='radio' VALUE='6' class='genmed' /> ";
echo "<label for='dest_6'>" . $l_admin_mess_all . "</label>";
echo "<BR/>";

echo "</TD>";
echo "</TR>";

echo "<TR>";
echo "<TD colspan='3' ALIGN='CENTER' class='catBottom'>";
echo "<INPUT class='mainoption' TYPE='submit' tabindex='6' VALUE ='" . $l_admin_mess_bt_send . "' />";
echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
echo "</TD>";
echo "</TR>";

echo "</FORM>";
echo "</TABLE>";


// si message(s) tout juste expédié
if ($send_ok == 'ok') 
{
	echo "<BR/>";
	if (intval($send_nb) > 0)
	{
		echo "<font face='verdana' size='2' color='green'><B>";
		echo $send_nb . " " . $l_admin_mess_nb_send;
	}
	else
	{
		echo "<font face='verdana' size='2' color='red'><B>";
		echo "0 " . $l_admin_mess_nb_send . " !";
	}
	echo "<BR/>";
}	
//
echo "<BR/>\n";

if ( ( (_ENTERPRISE_SERVER != "") and (f_check_acp_rights(_C_ACP_RIGHT_admin_messages_orders) == "OK") ) or ($full_menu != "") )
{
  echo "<table width='650' cellspacing='1' cellpadding='1' class='forumline'>";
  echo "<FORM METHOD='POST' name='formulaire_2' ACTION ='message_send_order.php?'>";
  echo "<TR>";
  echo "<TH colspan='3' class='thHead'>";
  echo "<FONT size='3'>";
  echo $l_admin_mess_title_5;
  if ( (_ENTERPRISE_SERVER != "") and (f_check_acp_rights(_C_ACP_RIGHT_admin_messages_orders) == "OK") )
  {
    if ($im_messagerie_show_order > 0)
    {
      echo " <A HREF='set_cookies.php?lang=" . $lang . "&tri=" . $tri . "&action=messagerie_show_order&im_messagerie_show_order=0&'>";
      echo "<IMG SRC='" . _FOLDER_IMAGES . "minimize.png' ALT='" . $l_hide . "' TITLE='" . $l_hide . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
    }
    else
    {
      echo " <A HREF='set_cookies.php?lang=" . $lang . "&tri=" . $tri . "&action=messagerie_show_order&im_messagerie_show_order=1&'>";
      echo "<IMG SRC='" . _FOLDER_IMAGES . "maximize.png' ALT='" . $l_display . "' TITLE='" . $l_display . "' ALIGN='RIGHT' WIDTH='16' HEIGHT='16' BORDER='0' /></A>";
    }
  }
  echo "</TH>";
  echo "</TR>";
}
if ( (_ENTERPRISE_SERVER != "") and (f_check_acp_rights(_C_ACP_RIGHT_admin_messages_orders) == "OK") and ($im_messagerie_show_order > 0) ) // _FORCE_USERNAME_TO_PC_SESSION_NAME 
{
  echo "<TR>";
  echo "<td width='25%' class='row2'><FONT size='2'>&nbsp;<b>" . $l_admin_mess_order . " :</b>";
  echo "</TD>";
  echo "<TD width='70%' class='row1' VALIGN='MIDDLE'>";

  echo "<FONT size='2'>";
  echo "<INPUT name='action' TYPE='radio' VALUE='STOPCNOW' id='STOPCNOW' class='genmed' ";
  if ($action == "stop") echo "CHECKED ";
  echo "/> <label for='STOPCNOW'>" . $l_admin_mess_stop_pc . "</label><BR/>";
  echo "<INPUT name='action' TYPE='radio' VALUE='BOOTPCNOW' id='BOOTPCNOW' class='genmed' /> <label for='BOOTPCNOW'>" . $l_admin_mess_boot_pc . "</label><BR/>";
  echo "<INPUT name='action' TYPE='radio' VALUE='BOOTIMNOW' id='BOOTIMNOW' class='genmed' ";
  if ($action == "") echo "CHECKED ";
  echo "/> <label for='BOOTIMNOW'>" . $l_admin_mess_boot_im . "</label><BR/>";

  echo "</TD>";

  echo "</TR>";

  echo "<TR>";
  echo "<td VALIGN='MIDDLE' class='row2'><FONT size='2'>&nbsp;<b>" . $l_admin_mess_to . " :</b>";
  echo "</TD>";
  echo "<td class='row1' colspan='2'>";
  echo "<FONT size='2'>";
  echo "<INPUT name='dest' id='dest_order_1' TYPE='radio' VALUE='1' class='genmed' ";
  if ($action == "stop") echo "CHECKED ";
  echo "/>";
  echo " <label for='dest_order_1'>" .$l_admin_mess_only . "</label> : ";
  echo "\n";
  echo " <select name='id_dest' onChange='verification_choix_2()'> ";

      $requete  = " SELECT USR.USR_USERNAME, USR.USR_NICKNAME, USR.USR_NAME, USR.ID_USER ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER USR, " . $PREFIX_IM_TABLE . "SES_SESSION SES ";
      $requete .= " WHERE SES.ID_USER = USR.ID_USER ";
      //$requete .= " AND ( (USR_CHECK <> 'WAIT' and USR_CHECK <> '') or USR_STATUS = 1 ) ";
      $requete .= " AND USR.USR_STATUS = 1 ";
      $requete .= " ORDER BY USR_USERNAME, USR_NAME ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-C1c]", $requete);
      if ( mysqli_num_rows($result) != 0 )
      {
        while( list ($username, $nickname, $nom, $id_user) = mysqli_fetch_row ($result) )
        {
          if ( ($nickname != '') and (_ALLOW_UPPERCASE_SPACE_USERNAME != '') ) $username = $nickname;
          //echo "<option value='" . $id_user . "' class='genmed'>" . $username;
          echo "<option value='" . $id_user . "' class='genmed' ";
          if ($id_user_select == $id_user)
            echo "SELECTED";
          echo ">" . $username;
          if ( ($nom != '') and ($nom != 'HIDDEN') )
            echo " &nbsp; [" . $nom . "]";
          //
          echo "</option>";
        }
      }
  echo "</select>";
  echo "<BR/>";
  echo "\n";

  echo "<INPUT name='dest' id='dest_order_2' TYPE='radio' VALUE='2' class='genmed' ";
  if ($id_user_select <= 0)
    echo "CHECKED";
  echo " /> <label for='dest_order_2'>" . $l_admin_mess_all_connected . "</label>";
  echo "<BR/>";

  if ( ( _SPECIAL_MODE_GROUP_COMMUNITY != '' ) or ( _SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '' ) or ( _GROUP_FOR_SBX_AND_ADMIN_MSG != '') )
  {
    echo "<INPUT name='dest' TYPE='radio' VALUE='3' class='genmed' /> " . $l_admin_mess_group_connected . " : ";
    echo " <select name='id_group_dest' onChange='verification_choix_4()'> ";
      $requete  = " SELECT distinct(GRP_NAME), GRP.ID_GROUP ";
      $requete .= " FROM " . $PREFIX_IM_TABLE . "GRP_GROUP GRP, " . $PREFIX_IM_TABLE . "USG_USERGRP USG ";
      $requete .= " WHERE GRP.ID_GROUP = USG.ID_GROUP ";
      $requete .= " order by GRP_NAME ";
      $result = mysqli_query($id_connect, $requete);
      if (!$result) error_sql_log("[ERR-C1d]", $requete);
      if ( mysqli_num_rows($result) != 0 )
      {
        while( list ($group, $id_group) = mysqli_fetch_row ($result) )
        {
          echo "<option value='" . $id_group . "' class='genmed' >" . $group;
          echo "</option>";
        }
      }
    echo "</select>";
    echo "<BR/>";
  }
      
  echo "</TD>";
  echo "</TR>";

  echo "<TR>";
  echo "<TD colspan='3' ALIGN='CENTER' class='catBottom'>";
  echo "<INPUT class='mainoption' TYPE='submit' VALUE ='" . $l_admin_mess_bt_send . "' />";
  echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
  echo "</TD>";
  echo "</TR>";

  echo "</FORM>";
  echo "</TABLE>";
  echo "<BR/>\n";
}
else
{
  if ($full_menu != "")
  {
    echo "<TR>";
    echo "<TD colspan='4' ALIGN='CENTER' class='row2'>";
      echo "<font face='verdana' size='2'>" . $l_admin_mess_cannot_order;
    echo "</TD>";
    echo "</TR>";
  }
  if ( ($full_menu != "") or ( ($im_messagerie_show_order <= 0) and (_ENTERPRISE_SERVER != "") and (f_check_acp_rights(_C_ACP_RIGHT_admin_messages_orders) == "OK") ) )
  {
    echo "</TABLE>";
    echo "<BR/>\n";
  }
}

//
echo "<table width='650' cellspacing='1' cellpadding='1' class='forumline'>";
echo "<THEAD>";
echo "<TR>";
echo "<TH colspan='4' class='thHead'>";
//echo "<TD colspan='4' class='catHead' align='CENTER'>";
echo "<FONT size='3'><B>&nbsp;";
echo $l_admin_mess_title_3 . "&nbsp;";
echo "</TH>";
echo "</TR>";
echo "\n";
$requete  = " SELECT MSG.MSG_TEXT, MSG.MSG_TIME, MSG.MSG_DATE, MSG.ID_MESSAGE, USR.USR_USERNAME, MSG.MSG_CR ";
$requete .= " FROM " . $PREFIX_IM_TABLE . "MSG_MESSAGE MSG, " . $PREFIX_IM_TABLE . "USR_USER USR ";
$requete .= " where MSG.ID_USER_DEST = USR.ID_USER ";
$requete .= " and MSG.ID_USER_AUT = -99 ";
$requete .= " order by ID_MESSAGE ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-C1b]", $requete);
if ( (mysqli_num_rows($result) != 0) and ($hide_ip == "") )
{
	echo "<TR>";
		display_row_table($l_admin_mess_to, '150');
		display_row_table($l_admin_mess_time, '50');
		display_row_table($l_admin_mess_message, '400');
		//display_row_table("<B>X</B>", '20');
    echo "<TD align='center' width='20' class='catHead'>";
    echo "<A HREF='message_delete.php?id_msg=KILL-THEM-ALL&tri=" . $tri . "&lang=" . $lang . "&'>";
		echo "<IMG SRC='" . _FOLDER_IMAGES . "b_drop.png' alt='" . $l_admin_bt_delete . "' title='" . $l_admin_bt_delete . "' width='16' height='16' border='0'></A>";
    echo "</TD>\n";
	echo "</TR>";
  echo "</THEAD>";
  echo "<TBODY>";
  //
	while( list ($msg, $heure, $date, $id_msg, $usrname, $msgcr) = mysqli_fetch_row ($result) )
	{
		//if ($date != '0000-00-00') $date = date($l_date_format_display, strtotime($date));
		
		echo "<TR>";
		echo "<TD class='row1' valign='center'>";
			echo "<font face='verdana' size='2'>" . $usrname . "</font>";
		echo "</TD>";

		echo "<TD class='row2' ALIGN='CENTER' valign='center'>";
			echo "<font face='verdana' size='2'>" . $heure . "</font>";
		echo "</TD>";

		echo "<TD class='row2'>";
      if ($msgcr == "64") $msg = base64_decode($msg);
      $msg = str_replace("SendImage:", "", $msg);
      if (strstr($msg, "SendOrder:")) $msg = "Send order !";
			echo "<font face='verdana' size='2'>" . $msg . "</font>";
		echo "</TD>";

		echo "<TD ALIGN='CENTER' valign='center' class='row2'>";
			echo "<A HREF='message_delete.php?id_msg=" . $id_msg . "&tri=" . $tri . "&lang=" . $lang . "&'>";
			echo "<IMG SRC='" . _FOLDER_IMAGES . "b_drop.png' alt='" . $l_admin_bt_delete . "' title='" . $l_admin_bt_delete . "' width='16' height='16' border='0'></A>";
		echo "</TD>";
		echo "</TR>\n";
	}
  echo "</TBODY>";
}
else
{
	echo "<TR>";
	echo "<TD colspan='4' ALIGN='CENTER' class='row2'>";
		echo "<font face='verdana' size='2' color='gray'>&nbsp;" . $l_admin_mess_no_wait . "&nbsp;";
	echo "</TD>";
	echo "</TR>";
}
echo "\n";
echo "<TFOOT>";
echo "<FORM METHOD='GET' ACTION ='messagerie.php?'>";
echo "<TR>";
echo "<TD colspan='4' ALIGN='CENTER' class='catBottom'>";
echo "<INPUT class='liteoption' TYPE='submit' VALUE ='" . $l_admin_mess_bt_refresh . "' />";
echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
echo "</TD>";
echo "</FORM>";
echo "</TR>";
echo "</TFOOT>";

echo "</TABLE>";
//
mysqli_close($id_connect);
//
display_menu_footer();
//
echo "</body></html>";
?>