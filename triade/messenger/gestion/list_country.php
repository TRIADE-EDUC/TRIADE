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
if (isset($_GET['lang'])) $lang = $_GET['lang']; else $lang = "";
//
define('INTRAMESSENGER',true);
require ("../common/styles/style.css.inc.php"); 
require ("../common/config/config.inc.php");
require ("lang.inc.php");
require ("../common/acp_sessions.inc.php");
require ("../common/menu.inc.php"); // après config.inc.php !
//echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head>";
echo "<title>[IM] " . $l_menu_users_by_country . "</title>";
display_header();
echo '<META http-equiv="refresh" content="60;url="> ';
//echo "<link href='../common/styles/defil.css' rel='stylesheet' media='screen, print' type='text/css'/>";
echo "</head>";
echo "<body>";
//
display_menu();
//
require ("../common/sql.inc.php");
//
$display_flag_country = "";
if (_FLAG_COUNTRY_FROM_IP != "")
{
	if (is_readable("../common/library/geoip/geoip_2.inc.php"))
	{
		require("../common/library/geoip/geoip_2.inc.php");
		$display_flag_country = "X";
  }
}
//
//
echo "<font face=verdana size=2>";
if ($display_flag_country == "")
{
  echo "<BR/>";
  echo "<div class='warning'>";
  echo "No display flag country (_FLAG_COUNTRY_FROM_IP)...";
  echo "</div>";
  die();
}
//
//
$requete  = " SELECT distinct(USR_COUNTRY_CODE), count(*) as NB";
$requete .= " FROM " . $PREFIX_IM_TABLE . "USR_USER ";
$requete .= " GROUP by USR_COUNTRY_CODE ";
$requete .= " ORDER by NB desc, USR_COUNTRY_CODE ";
$result = mysqli_query($id_connect, $requete);
if (!$result) error_sql_log("[ERR-A3zzzzb1]", $requete);
if ( mysqli_num_rows($result) > 0 )
{
  echo "<TABLE cellspacing='1' cellpadding='1' class='forumline'>\n";
  echo "<THEAD>";
  echo "<TR>";
    display_row_table($l_country, '300');
    display_row_table("", '50');
  echo "</TR>";
  echo "</THEAD>\n";

  echo "\n";
  echo "<TBODY>";
  //
  while( list ($country_code, $nb) = mysqli_fetch_row ($result) )
  {
    if (is_readable("../images/flags/" . strtolower($country_code) . ".png")) 
    {
      echo "<TR>";
      echo "<TD align='left' class='row1'>";
        $country_id = $GEOIP_COUNTRY_CODE_TO_NUMBER[$country_code];
        $country_name = f_quote($GEOIP_COUNTRY_NAMES[$country_id]);
        echo "&nbsp;<IMG SRC='../images/flags/" . strtolower($country_code) . ".png' WIDTH='18' HEIGHT='12' ALIGN='BASELINE' ALT='" . $country_name . "' TITLE='" . $country_name . "'>&nbsp;";
        echo $country_name;
      echo "</font></TD>";
      echo "<TD class='row1' align='right'>";
      echo "&nbsp;<font face='verdana' size='2'>";
      echo $nb;
      echo "</font></TD>";
      echo "</TR>";
      echo "\n";
    }
  }
  //
  echo "</TABLE>";
  echo "</TBODY>\n";
}
else
{
  echo "<BR/>";
  echo "<div class='info'>";
  echo $l_admin_acp_admin_list_empty;
  echo "</div>";
}
//
mysqli_close($id_connect);
//
//
display_menu_footer();
//
echo "</body></html>";
?>