<?php
if ($_FILES['Filedata']['name']) {
   if (!is_dir("../data/audio")) { mkdir("../data/audio"); } 
   $uploadDir = "../data/audio/";
   // $uploadFile = $uploadDir . basename($_FILES['Filedata']['name']);
   $uploadFile="${uploadDir}actu.mp3";
   move_uploaded_file($_FILES['Filedata']['tmp_name'], $uploadFile);
}
?>
