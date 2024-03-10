<?php 
    session_start();
    if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) {  exit; }
    include_once("../../common/config.inc.php");
    if(isset($_SESSION['unique_id'])){
        include_once "config.php";
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
        $message = mysqli_real_escape_string($conn, $_POST['message']);
        if(!empty($message)){
            $date=time();
	    $sql = "UPDATE ".PREFIXE."users SET update_sync = '$date' , status = 'Active now' WHERE unique_id= '{$outgoing_id}' ";
	    mysqli_query($conn, $sql) or die();
            $sql = mysqli_query($conn, "INSERT INTO ".PREFIXE."messages (incoming_msg_id, outgoing_msg_id, msg)
                                        VALUES ({$incoming_id}, {$outgoing_id}, '{$message}')") or die();
        }
    }else{
        header("location: ../login.php");
    }


    
?>
