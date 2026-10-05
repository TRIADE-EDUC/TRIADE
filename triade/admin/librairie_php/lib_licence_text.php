<?php
//----------------------------------------------------------------------------
// module pour affichage de la license
// pour Internet Explorer
include_once("../common/version.php");
include_once("../common/lib_admin.php");
include_once("../common/lib_ecole.php");
include_once("../common/config-md5.php");
include_once("../librairie_php/licence_triade.php");

if (file_exists("../common/lib_patch.php")){
	include_once('../common/lib_patch.php');
	if (defined("VERSIONPATCH")) $versionpatch=VERSIONPATCH;
	if (defined("VERSIONMD5")) $versionmd5=VERSIONMD5;
	
	$rev="<br>Rev : <i>".$versionpatch."</i>  - <i>".$versionmd5."</i>";
}

define("PIEDPAGE","<p>La <b>T</b>ransparence et la <b>R</b>apidité de l'<b>I</b>nformatique <b>A</b>u service <b>D</b>e l'<b>E</b>nseignement<br> Acessibilité : Non conforme - T.R.I.A.D.E &copy;  - Tous droits réservés</p>");

?>
