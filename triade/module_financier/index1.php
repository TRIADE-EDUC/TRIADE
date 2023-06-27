<?php
$parametres = '';
$separateur = '';
foreach($_REQUEST as $champ => $valeur) {
	$parametres .= $separateur . $champ;
	if($valeur != '') {
		$parametres .= '=' . $valeur;
	}
}
header('Location: ../index1.php?' . $parametres)
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Deconnexion</title>
</head>
<body>
	<script language="javascript">
		top.location = '../index1.php?<?php echo $parametres; ?>';
	</script>
</body>
</html>
