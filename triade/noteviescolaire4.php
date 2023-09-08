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

validerequete("2");

if(isset($_POST["nb"])) {
	$cnx=cnx();
	$nb=$_POST["nb"];
	$tri=$_POST["choix_trimestre"];
	$idclasse=$_POST["saisie_classe"];
	$idmatiere=$_POST["saisie_matiere"];
	$examen=$_POST["examen"];
	$anneeScolaire=$_POST["anneeScolaire"];
	$idgroupe=$_POST["saisie_groupe"];
	for($i=0;$i<$nb;$i++) {
		$saisie_eleve="saisie_eleve_".$i;
		$saisie_note="notescol_".$i;
		$saisie_com="comscol_".$i;
		$idEleve=$_POST[$saisie_eleve];
		$note=$_POST[$saisie_note];
		if (isset($_POST[$saisie_com])) {
			$commentaire=$_POST[$saisie_com];
		}else{
			$commentaire="";
		}
		enregistrement_note_scolaire_cpe($idmatiere,$idclasse,$tri,$idEleve,trim($note),$_SESSION["id_pers"],$idgroupe,$commentaire,$examen,$anneeScolaire);
	}
	
	$bullpers=$_POST["saisie_per"];
	$coef_bulletin=$_POST["coef_bulletin"];
	$coefProf=$_POST["coef_prof"];
	$coefScolaire=$_POST["coef_scolaire"];
	ajoutModifCaracVieScolaire($bullpers,$coef_bulletin,$coefProf,$coefScolaire,$idclasse,$anneeScolaire);

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
<center><font class="T2">Enregistrement terminé.</font><br><br>

<table align=center><tr><td>
<script language=JavaScript>buttonMagic("<?php print "Saisir autres notes scolaires" ?>","note_scolaire.php","_parent","","");</script>
</td></tr></table>
</center>
</br>
</body>
</html>
