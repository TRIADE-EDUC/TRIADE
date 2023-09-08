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
if (isset($_GET['tri'])) $tri = $_GET['tri'];  else $tri = "";
if (isset($_GET['only_status'])) $only_status = $_GET['only_status'];  else $only_status = "";
if (isset($_GET['page'])) $page = $_GET['page']; else $page = "";
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
check_acp_rights(_C_ACP_RIGHT_user_contacts);
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_admin_contact_add_contact . "</title>";
display_header();
//echo '<META http-equiv="refresh" content="60;url="> ';
//echo "<link href='../common/styles/defil.css' rel='stylesheet' media='screen, print' type='text/css'/>";
echo "</head>";
echo "<body>";
//
display_menu();
//
//echo "<font face=verdana size=2>";
//
if ( (_SPECIAL_MODE_GROUP_COMMUNITY == '') and (_SPECIAL_MODE_OPEN_COMMUNITY == '') and (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY == '') )
{
		echo "<SMALL><BR/></SMALL>";
		
		echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>";
		echo "<TR>";
		echo "<TD align='center' COLSPAN='3' class='catHead'>";
		echo "<font face='verdana' size='3'> &nbsp; &nbsp; <b>" . $l_admin_contact_add_contact . "</b> </font><font face='verdana' size='2'>";
		echo $l_admin_contact_auto_add . " &nbsp; &nbsp; </font>";
		echo "</TD>";
		echo "</TR>";
		echo "<TR>";
		
		if (_ALLOW_MANAGE_CONTACT_LIST == '')
		{
      require ("../common/sql.inc.php");
      //
      $form_list_users = "";
			$requete  = " select USR_USERNAME, USR_NICKNAME, USR_NAME, ID_USER ";
			$requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
			$requete .= " WHERE USR_STATUS = 1 ";
			$requete .= " ORDER BY USR_USERNAME, USR_NAME ";
			$result = mysqli_query($id_connect, $requete);
			if (!$result) error_sql_log("[ERR-A1c]", $requete);
			if ( mysqli_num_rows($result) != 0 )
			{
				while( list ($username, $nickname, $nom, $id_user) = mysqli_fetch_row ($result) )
				{
					if ( ($nickname != '') and (_ALLOW_UPPERCASE_SPACE_USERNAME != '') ) $username = $nickname;
					$form_list_users .= "<option value='" . $id_user . "' >" . $username . " &nbsp; ";
					if ($nom != '')
						$form_list_users .= "[" . $nom . "]";
					//
					$form_list_users .= "</option>";
				}
			}
      //
      mysqli_close($id_connect);
      //
      //
			echo "<FORM METHOD='POST' ACTION ='contact_add.php?'>";
			echo "<TD class='row2'>";
			echo "<font face='verdana' size='2'>";
			echo " &nbsp; ";
			echo "<select name='id_user_1'>";
			echo $form_list_users;
			echo "</select>";
		
			echo "</TD><TD class='row2'>";
			echo "<font face=verdana size=2>";
			echo " &nbsp; ";
			echo "<select name='id_user_2'>";
			echo $form_list_users;
			echo "</select>";
			

			echo "</TD><TD class='row2'>";
			//echo " ";
			echo "<INPUT TYPE='submit' VALUE = 'Ajouter' class='liteoption' />";
			echo "<input type='hidden' name='tri' value = '" . $tri . "' />";
      echo "<INPUT TYPE='hidden' name='page' value = '" . $page . "' />";
			echo "<INPUT TYPE='hidden' name='lang' value = '" . $lang . "' />";
			echo "</TD></TR>";
			echo "</FORM>";
		}
		else
		{
			echo "<TD align='center' COLSPAN='3' class='row1'>";
			echo "<font face=verdana size='2'>";
			echo "<font color='RED'>" . $l_admin_contact_no_add_1 . "</FONT> : ";
			echo $l_admin_contact_no_add_2b;
			echo "</TD></TR>";
			echo "<TR>";
			echo "<TD align='center' COLSPAN='3' class='catBottom'>";
			echo "<font face=verdana size=2>";
			echo $l_admin_contact_no_add_3b;
			echo "</TD></TR>";
		}
		echo "</TABLE>";
}
else
{
   echo "<BR/><B>";
   $msg = $l_admin_contact_cannot_use;
   if (_SPECIAL_MODE_OPEN_COMMUNITY != '')  $msg = str_replace("_SPECIAL_MODE_GROUP_COMMUNITY", "_SPECIAL_MODE_OPEN_COMMUNITY", $msg);
   if (_SPECIAL_MODE_OPEN_GROUP_COMMUNITY != '')  $msg = str_replace("_SPECIAL_MODE_GROUP_COMMUNITY", "_SPECIAL_MODE_OPEN_GROUP_COMMUNITY", $msg);
   //
   echo $msg . "</B><BR/>";
}
//
display_menu_footer();
//
echo "</body></html>";
?>