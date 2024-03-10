<?php 
    session_start();
    if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) {  exit; }
    include_once("../../common/config.inc.php");
    if(isset($_SESSION['unique_id'])){
        include_once "config.php";
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
        $output = "";
	$date=time();
        $sql = "UPDATE ".PREFIXE."users SET update_sync = '$date' , status = 'Active now' WHERE unique_id= '{$outgoing_id}' ";
        mysqli_query($conn, $sql) or die();
        $sql = "SELECT * FROM ".PREFIXE."messages LEFT JOIN ".PREFIXE."users ON ".PREFIXE."users.unique_id = ".PREFIXE."messages.outgoing_msg_id
                WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
                OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id}) ORDER BY msg_id";
        $query = mysqli_query($conn, $sql);
        if(mysqli_num_rows($query) > 0){
            while($row = mysqli_fetch_assoc($query)){
                if($row['outgoing_msg_id'] === $outgoing_id){
                    $output .= '<div class="chat outgoing">
                                <div class="details">
                                    <p>'. $row['msg'] .'</p>
                                </div>
                                </div>';
                }else{
                    $output .= '<div class="chat incoming">
                                <img src="php/images/'.$row['img'].'" alt="">
                                <div class="details">
                                    <p>'. $row['msg'] .'</p>
                                </div>
                                </div>';
                }
            }
        }else{
            $output .= '<div class="text">Une fois que vous aurez envoyé vos messages, ils apparaîtront ici.</div>';
        }
        echo $output;
    }else{
        header("location: ../login.php");
    }

?>
