<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2010 THeUDS           **
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

function display_row_table($text, $width)  
{
	//echo "<TH align='center' width='" . $width . "' bgcolor='" . $color . "' class='row1'><font face='verdana' size='2'><b>" . $text . "</b></font></TH>";
	echo "<TD align='center' width='" . $width . "' class='catHead'> <font face='verdana' size='2'><b>" . $text . "</b></font> </TD>\n";
}


function display_row($var1, $var2, $max_size, $comment, $lan, $wan)  
{
	GLOBAL $si_not_ok;
	GLOBAL $l_admin_options_legende_not_empty, $l_admin_options_legende_empty, $l_admin_options_legende_up2u, $l_admin_options_title_2;
	$var1 = trim($var1);
	$info_is_on = $l_admin_options_legende_not_empty; // "On : " . 
	$info_is_off = $l_admin_options_legende_empty; // "Off : " . 
	$info_should_be_on = $l_admin_options_title_2 . " : " . $l_admin_options_legende_not_empty; // "Should be activated";
	$info_should_be_off = $l_admin_options_title_2 . " : " . $l_admin_options_legende_empty; // "Should not be activated";
	$info_should_be_up2u = $l_admin_options_legende_up2u; // "Should be... up to you...";
	echo "<TR>";
	//
	echo "<TD class='row2' align='right'>";
    if ($var2 == "_LANG")
    {
      echo " <select name='T" . $var2 . "'> ";
      //
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
      echo "<option value='PT' class='genmed' ";
      if (_LANG == "PT") echo "SELECTED";
      echo ">PT</option>" ;
      //
      echo "<option value='BR' class='genmed' ";
      if (_LANG == "BR") echo "SELECTED";
      echo ">BR</option>" ;
      //
      echo "<option value='DE' class='genmed' ";
      if (_LANG == "DE") echo "SELECTED";
      echo ">DE</option>" ;
      //
      echo "<option value='RO' class='genmed' ";
      if (_LANG == "RO") echo "SELECTED";
      echo ">RO</option>" ;
      //
      echo " </select> ";
    }
    else
    {
      //echo "<font face='verdana' size='1'>&nbsp;" . $var2 . "&nbsp;</font>";
      if (intval($max_size) > 0)
      {
        echo "<input type='text' name='T" . $var2 . "' maxlength='" . $max_size . "' value='" . $var1 . "' ";
        if (intval($max_size) < 10) echo "size='" . $max_size . "' ";
        if ( (intval($max_size) >= 10) and (intval($max_size) < 100) ) echo "size='20' ";
        if (intval($max_size) >= 100) echo "size='40' ";
        //
        echo "class='post' />";
      }
      else
      {
        echo "<input type='checkbox' name='T" . $var2 . "' ";
        if ($var1 != "") echo "checked "; // echo "checked='0' ";
        echo " class='post' />";
      }
    }
  echo "</TD>";
	if ($comment == '')
	{
		echo "<TD class='row2'>";
		echo " &nbsp; ";
	}
	else
	{
		echo "<TD align='LEFT' class='row3'>";
		echo "<font face='verdana' size='2'>&nbsp;" . $comment . "</font>";
	}

  echo "<TD align='CENTER' class='row2'>";
  if (intval($lan) > 0)
    echo "<font face='verdana' size='2'>" . $lan . "</font>";
  else
  {
    if ($lan == "X") echo "<IMG SRC='" . _FOLDER_IMAGES . "thumb_up.png' WIDTH='16' HEIGHT='16' ALT='" . $info_should_be_on . "' TITLE='" . $info_should_be_on . "'>";
    if ($lan == "-") echo "<IMG SRC='" . _FOLDER_IMAGES . "thumb_down.png' WIDTH='16' HEIGHT='16' ALT='" . $info_should_be_off . "' TITLE='" . $info_should_be_off . "'>";
    if ($lan == "")  echo " "; //"<IMG SRC='" . _FOLDER_IMAGES . "bt_yellow.gif' WIDTH='18' HEIGHT='18' ALT='" . $info_should_be_up2u . "' TITLE='" . $info_should_be_up2u . "'>";
  }
  //
  echo "<TD align='CENTER' class='row2'>";
  if (intval($wan) > 0)
    echo "<font face='verdana' size='2'>" . $wan . "</font>";
  else
  {
    if ($wan == "X") echo "<IMG SRC='" . _FOLDER_IMAGES . "thumb_up.png' WIDTH='16' HEIGHT='16' ALT='" . $info_should_be_on . "' TITLE='" . $info_should_be_on . "'>";
    if ($wan == "-") echo "<IMG SRC='" . _FOLDER_IMAGES . "thumb_down.png' WIDTH='16' HEIGHT='16' ALT='" . $info_should_be_off . "' TITLE='" . $info_should_be_off . "'>";
    if ($wan == "")  echo " "; //"<IMG SRC='" . _FOLDER_IMAGES . "bt_yellow.gif' WIDTH='18' HEIGHT='18' ALT='" . $info_should_be_up2u . "' TITLE='" . $info_should_be_up2u . "'>";
  }
	echo "</TD>";
	//
	echo "</TR>";
	echo "\n";
}
//


?>