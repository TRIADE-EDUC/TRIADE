<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
        setcookie("anneeScolaire",$_POST["anneeScolaire"],time()+36000*24*30);
        $anneeScolaire=$_POST["anneeScolaire"];
}
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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/jsProgressBarHandler.js"></script>

<title>Vérification Bulletin</title>
</head>
<body  id='coulfond1' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("profadmin");
include_once('librairie_php/recupnoteperiode.php');
$cnx=cnx();
$idclasse=$_POST["saisie_classe"];
$trimes=$_POST["saisie_trimestre"];
$anneeScolaire=$_POST["anneeScolaire"];
$nomclasse=chercheClasse_nom($idclasse);
$nommatiere=chercheMatiereNom($idmatiere);
history_cmd($_SESSION["nom"],"LISTE","Com. Bull. $nomclasse $trimes");

?>
<table border=0 width=100% align=center  height="100%">
<tr><td valign=top height=10 ><br /><ul>
<font class=T2><?php print LANGBULL46 ?> <b><?php print $nomclasse ?></b> <?php print LANGDST5 ?> <b><?php print $trimes ?></b> </font></ul></td></tr>
<tr><td valign=top  ><ul>
<form method=post action="liste_bulletin_com2.php" >
<table border='1' style='border-collapse: collapse;'  >
<tr bgcolor='yellow' ><td>Matière</td><td>Nombre effectué</td><td>Taux effectué</td><td>Signaler</td></tr>
<?php // ---------------------------------------------------------- 

include_once('librairie_php/recupnoteperiode.php');
$idClasse=$idclasse;
$ordre=ordre_matiere_visubull_trim($idclasse,$trimes,$anneeScolaire);

for($i=0;$i<count($ordre);$i++) {
	$matiere=chercheMatiereNom($ordre[$i][0]);
	$nomprof=recherche_personne($ordre[$i][1]);
	$idMatiere=$ordre[$i][0];
	// mise en place du nom du prof
        $idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
        $profAff=recherche_personne($ordre[$i][1]);
        // mise en place des coeff
	$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$i][2],$anneeScolaire);
	$verifgrp=verifMatierAvecGroupeRecupId2($idMatiere,$idClasse,$i,$anneeScolaire);
	if ($verifgrp == -1) {
		$idgroupe=0;
	}else{
		$idgroupe=$verifgrp;
	}

	// mise en place des matieres
	print "<tr class=\"tabnormal\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\" >";
	print "<td valign=top ><font size=2><input type=text readonly value='".trunchaine(strtoupper($matiere),15)." (".$coeffaff.")' size=30 title=\"$matiere\" class='BUTTON' ></font>";
	print "<br><i><font size=1> ".trunchaine(trim($profAff),20)." </font></i></td>";
	// mise en place moyenne eleve
	// mise en place des notes
	
	
	$nbcommentaire=0;
	$nbeleve=0;
	$nbcommentaire=nb_de_commentaire($idMatiere,$idClasse,$trimes,$idprof,$idgroupe,$anneeScolaire);
	if ($idgroupe <= 0) {
		$nbeleve=nbEleve($idClasse,$anneeScolaire);
	}else{
		$nbeleve=nb_eleve_groupe($idgroupe,$anneeScolaire);
	}

	$taux=($nbcommentaire/$nbeleve)*100;

    	print "<td>&nbsp;";
	print "<font size=3 color=$couleur>&nbsp;<b>$nbcommentaire</b> commentaire(s) enregistré(s) sur <b>$nbeleve</b> élève(s) &nbsp;</font></td>";
	print "<td><span class='progressBar' id='myElementId$i'> ${taux}%</span></td>";
	print "<td><input type=checkbox name=idprof[] value='$idprof'></td>";
	print "</tr>";
	
}
//
?>
</table>
<br />
<input type=hidden name="nbprof" value="<?php print $i?>" >
<input type=hidden name="saisie_classe" value="<?php print $idclasse ?>" >
<font class="T2"><?php print LANGBULL45 ?></font><br><br>
<script language=JavaScript>buttonMagicSubmitAtt("<?php print "Envoi message" ?>","mail","");</script>
<script language=JavaScript>buttonMagicPrecedent2();</script>
</form>
<br><br><br>
</tr></td>
</td></tr></table>
<?php Pgclose(); ?>
</body>
</html>
