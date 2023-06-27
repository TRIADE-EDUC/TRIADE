<?php
session_start();


include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");

if(isset($_GET['saveAnItem'])){
	$cnx=cnx();

	$description = $_GET['description'];
	$id = $_GET['id'];
	$description = str_replace("'","&#039;",$_GET['description']);
	$description = str_ireplace("<script","&lt;script",$_GET['description']);
	#$description = mysql_real_escape_string($description);	// If you have support for mysql_real_escape_string


	//$dateDebut=strftime("%Y-%m-%d %H:%M:%S",strtotime($_GET['eventStartDate'])+(TIMEZONE*3600));
	$dateDebut=strftime("%Y-%m-%d %H:%M:%S",strtotime($_GET['eventStartDate']));
	//$dateFin=strftime("%Y-%m-%d %H:%M:%S",strtotime($_GET['eventEndDate'])+(TIMEZONE*3600));
	$dateFin=strftime("%Y-%m-%d %H:%M:%S",strtotime($_GET['eventEndDate']));

//	$dateDebut=date("Y-m-d H:i:s",strtotime($_GET['eventStartDate']));
//	$dateFin=date("Y-m-d H:i:s",strtotime($_GET['eventEndDate']));
	$bgColor=$_GET['bgColorCode'];



	if(isset($_GET['newItem'])){	// This is a new item 
		// Save the item to the server and 
		$code=ajoutEdt($id,$description,$dateDebut,$dateFin,$bgColor);
		echo $code;	// The id is sent back to ajax so that it could update the id of the entry, i.e. update
			// it next time instead of saving another new item.
	}else{
		miseEdt2($id,$dateDebut,$dateFin,$bgColor);
	}	
	Pgclose();
}
?>
