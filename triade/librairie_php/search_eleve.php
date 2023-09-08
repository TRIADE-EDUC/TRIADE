<?php  
include_once("../common/config.inc.php");
$connect = mysqli_connect(HOST,USER,PWD,DB);  
if(isset($_POST["query"])){  
	$output = '';  
        $query = "SELECT * FROM ".PREFIXE."eleves WHERE nom LIKE '".$_POST["query"]."%'";  
        $result = mysqli_query($connect, $query);  
        $output = '<ul class="list-unstyled">';  
        
        if(mysqli_num_rows($result) > 0){  
            while($row = mysqli_fetch_array($result)){  
                $output .= '<li>'.$row["nom"].'</li>';  
            }  
        }else{  
            $output .= '<li>non trouvé</li>';  
        }  
	$output .= '</ul>';  
    	echo $output;  
} 
?>
