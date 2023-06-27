<?php

session_start();
$nom=$_SESSION["nom"];
$prenom=$_SESSION["prenom"];
$membre=$_SESSION["membre"];
$langue=$_SESSION["langue"];

//header('Location: ../verrou.php') ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Redirection vers le verrou</title>
</head>
<body>
	<script language="javascript">
		top.location = "../verrou.php";
	</script>
	<a href="../verrou.php">Allers vers le verrour</a>
</body>
</html>
