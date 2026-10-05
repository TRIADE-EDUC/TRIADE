<?php
    session_start();
    if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) {  exit; }
    include_once("../../common/config.inc.php");
    include_once "config.php";

    // Rate limiting par session : max 10 tentatives
    if (!isset($_SESSION['tchat_login_attempts'])) $_SESSION['tchat_login_attempts'] = 0;
    if (!isset($_SESSION['tchat_login_locked_until'])) $_SESSION['tchat_login_locked_until'] = 0;

    if ($_SESSION['tchat_login_locked_until'] > time()) {
        echo "Trop de tentatives. Réessayez dans " . ceil(($_SESSION['tchat_login_locked_until'] - time()) / 60) . " minute(s).";
        exit;
    }

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    if(!empty($email) && !empty($password)){
        $sql = mysqli_query($conn, "SELECT * FROM ".PREFIXE."users WHERE email = '{$email}'");
        if(mysqli_num_rows($sql) > 0){
            $row = mysqli_fetch_assoc($sql);
            $user_pass = md5($password);
            $enc_pass = $row['password'];
            if(hash_equals($enc_pass, $user_pass)){
                $_SESSION['tchat_login_attempts'] = 0;
                $_SESSION['tchat_login_locked_until'] = 0;
                $status = "Active now";
                $sql2 = mysqli_query($conn, "UPDATE ".PREFIXE."users SET status = '{$status}' WHERE unique_id = {$row['unique_id']}");
                if($sql2){
                    $_SESSION['unique_id'] = $row['unique_id'];
                    echo "success";
                }else{
                    echo "Something went wrong. Please try again!";
                }
            }else{
                $_SESSION['tchat_login_attempts']++;
                if ($_SESSION['tchat_login_attempts'] >= 10) {
                    $_SESSION['tchat_login_locked_until'] = time() + 900; // 15 min
                }
                echo "Email ou mot de passe incorrect.";
            }
        }else{
            $_SESSION['tchat_login_attempts']++;
            if ($_SESSION['tchat_login_attempts'] >= 10) {
                $_SESSION['tchat_login_locked_until'] = time() + 900;
            }
            echo "Email ou mot de passe incorrect.";
        }
    }else{
        echo "All input fields are required!";
    }
?>
