<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: emergency.php,v 1.8 2023/03/02 08:45:38 dbellamy Exp $

//Restauration d'urgence
?>

<html>
<head>
<meta charset="utf-8">
<title>Emergency restore database</title>
</head>
<body>
<h1>Emergency restore</h1>
<br /><br />
<form class='form-$current_module' action="emergency_upload.php" method="post" enctype="multipart/form-data">
<table>
<tr><td>Upload archive file</td><td><input type="file" name="archive_file"></td></tr>
<tr><td colspan=2 class='center'><input type="submit" value="Click here to start restoring datas"></td></tr>
</table>
</form>
</body>
</html>