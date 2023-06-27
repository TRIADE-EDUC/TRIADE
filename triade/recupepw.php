<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - F. ORY
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
include("librairie_php/lib_licence.php");
?>
<html>
<head>
<!-- /************************************************************
Last updated: 17.08.2004    par Taesch  Eric
*************************************************************/ -->
</head>
<body>
<?php
$fic="./data/fic_pass.txt";
if (file_exists($fic)) {
?>
<center><u><?php print LANGBASE13?></u></center>
<br>
<ul><?php print LANGBASE14?>
<br>
<br>
<?php print LANGBASE15?>
<br>
<br>
<?php print LANGBASE16?>
<br />
<br />
--> Seul les comptes affectés à une classe sont autorisés à se connecter.
</ul>
<br>
<br>
<?php
    $fichier=fopen("$fic","r");
    $donnee=fread($fichier,filesize("$fic"));
    print "$donnee";
    fclose($fichier);
}else {
?>
<center><?php print LANGBASE18?></center>
<?php
}
@unlink($fic);
?>
</body>
</html>
