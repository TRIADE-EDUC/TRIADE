<?php
    session_start();
    if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) {  exit; }
    include_once "config.php";
    include_once("../../common/config.inc.php");
    $outgoing_id = $_SESSION['unique_id'];
    $date=time();
    $sql = "UPDATE ".PREFIXE."users SET update_sync = '$date' , status = 'Active now' WHERE unique_id= '{$outgoing_id}' ";
    mysqli_query($conn, $sql) or die();
    $sql = "SELECT * FROM ".PREFIXE."users WHERE NOT unique_id = {$outgoing_id} ORDER BY user_id DESC";
    $query = mysqli_query($conn, $sql);
    $output = "";
    if(mysqli_num_rows($query) == 0){
        $output .= "Personne de disponible actuellement";
    }elseif(mysqli_num_rows($query) > 0){
        include_once "data.php";
    }
    echo $output;
?>
