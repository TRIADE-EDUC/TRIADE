<?php
    session_start();
    if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) {  exit; }
    include_once "config.php";
    include_once("../../common/config.inc.php");

    $outgoing_id = $_SESSION['unique_id'];
    $searchTerm = mysqli_real_escape_string($conn, $_POST['searchTerm']);

    $sql = "SELECT * FROM ".PREFIXE."users WHERE NOT unique_id = {$outgoing_id} AND (fname LIKE '%{$searchTerm}%' OR lname LIKE '%{$searchTerm}%') ";
    $output = "";
    $query = mysqli_query($conn, $sql);
    if(mysqli_num_rows($query) > 0){
        include_once "data.php";
    }else{
        $output .= 'Aucune personne de trouvé';
    }
    echo $output;
?>
