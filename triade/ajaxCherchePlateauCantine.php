<?php
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
if (isset($_POST["id"])) {
	$cnx=cnx();
	$data=cherchePlateauCantine($_POST["id"]); // id,libelle,prix,attribue
	PgClose();
	if (count($data) > 0) {
		for($i=0;$i<count($data);$i++) {
			$id=$data[$i][0];
			$libelle=$data[$i][1];
			$prix=$data[$i][2]." ".unitemonnaie();
			$attribue=$data[$i][3];
			$data2[$i][0]=$id;
			$data2[$i][1]=rawurlencode($libelle);
			$data2[$i][2]=$prix;
			$data2[$i][3]=$attribue;
			$data2[$i][4]=$data[$i][2];
		}
		echo serialize($data2);
	}else{
		echo "";
	}
}else{
	print "";
}
exit;
?>
