<?php
error_reporting(0);
if (isset($_POST["pass"])) {
	$security=$_POST["level"];
	$mdp=$_POST["pass"];
	if (($security == 3 ) && (preg_match('/[a-z]/',$mdp)) && (preg_match('/[A-Z]/',$mdp))  && (preg_match('/[0-9]/',$mdp)) && (strlen("$mdp") >= 8) ) { 
		sleep(1);print "1"; 
	}elseif (($security == 2 ) && (preg_match('/[a-z]/',$mdp)) && (preg_match('/[0-9]/',$mdp)) && (strlen("$mdp") >= 8) ) { 
		sleep(1);print "1";
	}elseif (($security == 1 ) && (strlen("$mdp") >= 4)) {
		sleep(1);print "1"; 
	}else{
		sleep(1);print "0"; 
	}
}

if (isset($_POST["pass1"])) {
	$security=$_POST["level"];
	$mdp=$_POST["pass1"];
	$mdp2=$_POST["pass2"];
	if (($security == 3 ) && (preg_match('/[a-z]/',$mdp)) && (preg_match('/[A-Z]/',$mdp))  && (preg_match('/[0-9]/',$mdp)) && (strlen("$mdp") >= 8) && ($mdp == $mdp2) ) { 
		sleep(1);print "1"; 
	}elseif (($security == 2 ) && (preg_match('/[a-z]/',$mdp)) && (preg_match('/[0-9]/',$mdp)) && (strlen("$mdp") >= 8) && ($mdp == $mdp2) ) { 
		sleep(1);print "1";
	}elseif (($security == 1 ) && (strlen("$mdp") >= 4) && ($mdp == $mdp2) ) {
		sleep(1);print "1"; 
	}else{
		sleep(1);print "0"; 
	}
}

?>
