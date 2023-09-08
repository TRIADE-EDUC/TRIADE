<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET -
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
include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
if ((VIESCOLAIRENOTEENSEIGNANT == "oui") && ($_SESSION["membre"] != "menupersonnel"))  {
	validerequete("3");
}else{
	if (($_SESSION["membre"] != "menuadmin") && ($_SESSION["membre"] != "menuprof")) {
		$cnx=cnx();
		if (!verifDroit($_SESSION["id_pers"],"carnetnotes")) {
			accesNonReserveFen();
			exit();
		}
		Pgclose();
	}else{
		validerequete("profadmin");
	}
}
$retour="noteajout.php";
$idprof=$_SESSION["id_pers"];
if (isset($_POST["adminIdprof"])) { $adminIdprof=$_POST["adminIdprof"]; }
if ($adminIdprof != "") { $idprof=$adminIdprof; $retour="carnetnote.php"; }

if(isset($_POST["nb"])) {
	$cnx=cnx();
	error($cnx);
	$nb=$_POST["nb"];
	$tri=$_POST["choix_trimestre"];
	$idclasse=$_POST["saisie_classe"];
	$idmatiere=$_POST["saisie_matiere"];
	$anneeScolaire=$_POST["anneeScolaire"];
	$idgroupe=$_POST["saisie_groupe"];
	for($i=0;$i<$nb;$i++) {
		$saisie_eleve="saisie_eleve_".$i;
		$saisie_note="notescol_".$i;
		$idEleve=$_POST[$saisie_eleve];
		$note=$_POST[$saisie_note];
		enregistrement_note_scolaire($idmatiere,$idclasse,$tri,$idEleve,$note,$idprof,$idgroupe,"",$anneeScolaire);
	}
	$nomclasse=chercheClasse_nom($idclasse);
	$nommatiere=chercheMatiereNom($idmatiere);
	history_cmd($_SESSION["nom"],"CREATION","Notes Scolaires $nomclasse $nommatiere");
	Pgclose();
}
?>
<HTML>
<HEAD>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<br><br>
	<center><font class="T2"><?php print LANGRESA69 ?>.</font><br><br>

<table align=center><tr><td>
<script language=JavaScript>buttonMagic("<?php print "Saisir autres notes scolaires" ?>","<?php print $retour ?>","_parent","","");</script>
</td></tr></table>
</center>
</br>
</body>
</html>
