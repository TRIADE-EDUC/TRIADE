<?php
/*******************************************************
 **                  IntraMessenger - server          **
 **                                                   **
 **  Copyright:      (C) 2006 - 2013 THeUDS           **
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

function textFilter($text, $file)
{
	$text = supSpecialChar($text);
	$tab = array();
	if (file_exists($file))
	{
		$tab = file($file);
		if (count($tab) > 0 )
		{
      for ($i=0;$i < count($tab);$i++)
      {
        $motformat = supSpecialChar(trim($tab[$i]));
        if (preg_match('/$motformat/i', $text))
        {
          return true;
        }
      }
    }
	}
	//
	return false;
}


function textCensure($text, $file)
{
	//$text = supSpecialChar($text);
	$tab = array();
	if (file_exists($file))
	{
		$tab = file($file);
		if (count($tab) > 0 )
		{
      for ($i=0;$i < count($tab);$i++)
      {
        $text = str_ireplace(trim($tab[$i]), "...", $text);  //    "..." ou "[censur�]"
      }
    }
	}
  //
	return $text;
}


function supSpecialChar($string)
{
	$start = array("/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/",
	"/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/",
	"/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/",
	"/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/","/�/",
	"/�/","/�/","/�/","/�/","/�/","/�/","/�/");
	$end = array("A","A","A","A","A","A","A","a","a","a","a","a","a","a","E","E",
	"E","E","e","e","e","e","I","I","I","I","i","i","i","i","O","O","O","O","O","O",
	"o","o","o","o","o","o","U","U","U","U","u","u","u","u","B","C","c","D","d","N",
	"n","P","p","Y");
	//
	$newString = preg_replace($start, $end, $string);
	//
	return strtolower($newString);
}

?>
