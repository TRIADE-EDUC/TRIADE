<?php
    session_start();
    if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) {  exit; }
    include_once("../../common/config.inc.php");
    if(isset($_SESSION['unique_id'])){
        include_once "config.php";
        $logout_id = mysqli_real_escape_string($conn, $_GET['logout_id']);
        if(isset($logout_id)){
            $status = "Offline now";
            $sql = mysqli_query($conn, "UPDATE ".PREFIXE."users SET status = '{$status}' WHERE unique_id={$_GET['logout_id']}");
            if($sql){
                session_unset();
                session_destroy();
                header("location: ../login.php");
            }
        }else{
            header("location: ../users.php");
        }
    }else{  
        header("location: ../login.php");
    }
?>
