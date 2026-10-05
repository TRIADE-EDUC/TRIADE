<?php
    include_once("../../common/config.inc.php");
    while($row = mysqli_fetch_assoc($query)){
        $sql2 = "SELECT * FROM ".PREFIXE."messages WHERE (incoming_msg_id = {$row['unique_id']}
                OR outgoing_msg_id = {$row['unique_id']}) AND (outgoing_msg_id = {$outgoing_id} 
                OR incoming_msg_id = {$outgoing_id}) ORDER BY msg_id DESC LIMIT 1";
        $query2 = mysqli_query($conn, $sql2);
        $row2 = mysqli_fetch_assoc($query2);
        (mysqli_num_rows($query2) > 0) ? $result = $row2['msg'] : $result ="<i>Pas de message</i>";
        (strlen($result) > 28) ? $msg =  substr($result, 0, 28) . '...' : $msg = $result;
        if(isset($row2['outgoing_msg_id'])){
            ($outgoing_id == $row2['outgoing_msg_id']) ? $you = "Toi: " : $you = "";
        }else{
            $you = "";
        }
        ($row['status'] == "Offline now") ? $offline = "offline" : $offline = "";
        ($outgoing_id == $row['unique_id']) ? $hid_me = "hide" : $hid_me = "";

        $msgId  = isset($row2['msg_id']) ? intval($row2['msg_id']) : 0;
        $fromMe = (isset($row2['outgoing_msg_id']) && $outgoing_id == $row2['outgoing_msg_id']) ? 1 : 0;

        $output .= '<a href="chat.php?user_id='. $row['unique_id'] .'" data-msg-id="'. $msgId .'" data-from-me="'. $fromMe .'">
                    <div class="content">
                    <img src="php/images/'. ((!empty($row['img']) && file_exists(__DIR__.'/images/'.$row['img'])) ? $row['img'] : 'photo_vide.jpg') .'" alt="" onerror="this.src=\'php/images/photo_vide.jpg\';this.onerror=null;">
                    <div class="details">
                        <span>'. $row['fname']. " " . $row['lname'] .'</span>
                        <p>'. $you . $msg .'</p>
                    </div>
                    </div>
                    <div class="status-dot '. $offline .'"><i class="fas fa-circle"></i></div>
                </a>';
    }
?>
