<?php
    session_start();
    if ( (empty($_SESSION["nom"])) && (empty($_SESSION["membre"]) ) ) {  exit; }
    include_once "config.php";
    include_once "../../common/config.inc.php";
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    if(!empty($fname) && !empty($lname) && !empty($email) && !empty($password)){
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            $sql = mysqli_query($conn, "SELECT * FROM ".PREFIXE."users WHERE email = '{$email}'");
            if(mysqli_num_rows($sql) > 0){
                echo "$email - This email already exist!";
            }else{
                if(isset($_POST['image'])){
			    list($null,$null,$image)=preg_split('/\./',$_POST['image']); 
			    $time = time();
                            $new_img_name = "$time.$image";
                            if (copy("../../".$_POST['image'],"images/".$new_img_name)) {
                                $ran_id = rand(time(), 100000000);
                                $status = "Active now";
                                $encrypt_pass = md5($password);
				$sql="INSERT INTO ".PREFIXE."users (unique_id, fname, lname, email, password, img, status) VALUES ({$ran_id}, '{$fname}','{$lname}', '{$email}', '{$encrypt_pass}', '{$new_img_name}', '{$status}')";
                                $insert_query = mysqli_query($conn,$sql);
                                if($insert_query){
                                    $select_sql2 = mysqli_query($conn, "SELECT * FROM ".PREFIXE."users WHERE email = '{$email}'");
                                    if(mysqli_num_rows($select_sql2) > 0){
                                        $result = mysqli_fetch_assoc($select_sql2);
                                        $_SESSION['unique_id'] = $result['unique_id'];
                                        header("Location:../../intra-msn.php?ok");
                                    }else{
                                        echo "This email address not Exist!";
                                    }
                                }else{
                                    echo "Quelque chose s'est mal passé. Veuillez réessayer ! ";
                                }
                         }
                }
            }
        }else{
            echo "$email is not a valid email!";
        }
    }else{
	header("Location:../../intra-msn.php?error=1");
	exit;
    }
?>
