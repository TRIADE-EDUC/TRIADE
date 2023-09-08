<?php
/*
* Restore MySQL dump using PHP
* (c) 2006 Daniel15
* Last Update: 9th December 2006
* Version: 0.2
* Edited: Cleaned up the code a bit. 
*
* Please feel free to use any part of this, but please give me some credit :-)
*/
// Name of the file

// MySQL host


if (file_exists("../data/install_log/install.inc")) { exit ; }


$filename = './sql/ecole_dev.sql';

include("../common/config.inc.php");

$mysql_host = HOST ;
// MySQL username
$mysql_username = USER ; 
// MySQL password
$mysql_password = PWD ;
// Database name
$mysql_database = DB ;
//////////////////////////////////////////////////////////////////////////////////////////////
// Connect to MySQL server
$cn=mysqli_connect($mysql_host, $mysql_username, $mysql_password) or die('Error connecting to MySQL server: ' . mysqli_error($cn));
// Select database
mysqli_select_db($cn,$mysql_database) or die('Error selecting MySQL database: ' . mysqli_error($cn));

// Temporary variable, used to store current query
$templine = '';
// Read in entire file
$lines = file($filename);
// Loop through each line
foreach ($lines as $line){
	// Skip it if it's a comment
	if (substr($line, 0, 2) == '--' || $line == '')
	continue;
	// Add this line to the current segment
	$templine .= $line;
	// If it has a semicolon at the end, it's the end of the query
	if (substr(trim($line), -1, 1) == ';')
	{
		// Perform the query
		mysqli_query($cn,$templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysqli_error($cn) . '<br /><br />');
		// Reset temp variable to empty
		$templine = '';
	}
}
?>
