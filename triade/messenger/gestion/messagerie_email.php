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
if (isset($_GET['id_user_select'])) $id_user_select = intval($_GET['id_user_select']);  else  $id_user_select = 0;
if (isset($_GET['send_ok'])) $send_ok = $_GET['send_ok'];  else  $send_ok = "";
if (isset($_GET['send_nb'])) $send_nb = $_GET['send_nb'];  else  $send_nb = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
if (isset($_GET['action'])) $action = $_GET['action']; else $action = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_admin_messages_emails);
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
function verification_choix_3b() 
{
 if (document.formulaire.id_group_dest2.selectedIndex >= 0 )  document.formulaire.dest[2].checked = true
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
echo "<FORM METHOD='POST' name='formulaire' ACTION ='message_email_send.php?'>";
echo "<TR>";
echo "<TH colspan='2' class='thHead'>";
echo "<FONT size='3'>";
echo $l_admin_mess_email_title;
echo "</TH>";
echo "</TR>";

echo "<TR>";
echo "<td width='25%' class='row2'><FONT size='2'>&nbsp;<b>" . $l_admin_bookmarks_url_title . " :</b>";
echo "</TD>";
echo "<TD width='70%' class='row1' VALIGN='MIDDLE'>";
echo "&nbsp;<input name='titre' size='69' maxlength='200' type='text' class='post' tabindex='1' value='[IntraMessenger] Admin info' />";
echo "</TD>";
echo "</TR>";

echo "<TR>";
echo "<td width='25%' class='row2'><FONT size='2'>&nbsp;<b>" . $l_admin_mess_message . " :</b>";
echo "</TD>";
echo "<TD width='70%' class='row1' VALIGN='MIDDLE'>";
echo "&nbsp;<TEXTAREA name='msg' rows='5' cols='67' maxlength='2000' class='post' tabindex='2'></TEXTAREA>";
echo "</TD>";
echo "</TR>";

echo "<TR>";
echo "<td VALIGN='MIDDLE' class='row2'><FONT size='2'>&nbsp;<b>" . $l_admin_mess_to . " :</b>";
echo "</TD>";
echo "<td class='row1' colspan='1'>";
echo "<FONT size='2'>";
echo "<INPUT name='dest' id='dest_u' TYPE='radio' VALUE='U' class='genmed' tabindex='3'";
if ($id_user_select > 0)
	echo "CHECKED";
echo " /> ";
echo "<label for='dest_u'>" . $l_admin_mess_only . "</label> : ";
echo "\n";
echo " <select name='id_dest' onChange='verification_choix()'> ";

		$requete  = " SELECT SQL_CACHE USR_USERNAME, USR_NICKNAME, USR_NAME, ID_USER ";
		$requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
		$requete .= " WHERE USR_STATUS = 1 ";
		$requete .= " and USR_EMAIL <> '' "; // important
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
  }
  //
  //
  echo "<INPUT name='dest' id='dest_g' TYPE='radio' VALUE='G' class='genmed'  /> ";
  echo "<label for='dest_g'>" . $l_admin_mess_group . "</label> : ";
  echo " <select name='id_group_dest2' onChange='verification_choix_3b()'> ";
    echo $liste_groupes;
  echo "</select>";
  echo "<BR/>";
}
mysqli_close($id_connect);
//
//
echo "<INPUT name='dest' id='dest_c' TYPE='radio' VALUE='C' class='genmed'";
//if ($id_user_select <= 0) echo "CHECKED";
echo " /> ";
echo "<label for='dest_c'>" . $l_admin_mess_all_connected . "</label>";
echo "<BR/>";
//
//
echo "<INPUT name='dest' id='dest_a' TYPE='radio' VALUE='A' class='genmed'";
if ($id_user_select <= 0) echo "CHECKED";
echo " /> ";
echo "<label for='dest_a'>" . $l_admin_mess_all . "</label>";
echo "<BR/>";

echo "</TD>";
echo "</TR>";


echo "<TR>";
echo "<TD colspan='2' ALIGN='CENTER' class='catBottom'>";
if (!function_exists('mail')) 
{
  echo "<font face='verdana' size='2' color='red'><B><U>Mail()</U> function not enabled!</B></font>";
}
else
{
  if (strlen(_ADMIN_EMAIL) > 5) 
  {
    echo "<INPUT class='mainoption' TYPE='submit' tabindex='6' VALUE ='" . $l_admin_mess_bt_send . "' />";
  }
  else
  {
    $txt = str_replace("_ROLES_TO_OVERRIDE_PERMISSIONS", "_ADMIN_EMAIL", $l_admin_roles_cannot_use);
    echo "<font face='verdana' size='2' color='red'><B>" . $txt . "</B></font>";
  }
}
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
//
//
//
display_menu_footer();
//
echo "</body></html>";
?>