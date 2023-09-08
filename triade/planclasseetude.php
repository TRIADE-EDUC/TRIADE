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
 * <script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Trombinoscope</title>
</head>
<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<script type="text/javascript" src="./librairie_js/wz_dragdrop.js"></script>
<?php
// connexion
include("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
if (isset($_GET["id"])) {
	$idetude=$_GET["id"];
}
if (isset($_POST["newcoord"])) {
	$idetude=$_POST["id"];
	eregist_planclasse("-$idetude",$_POST["newcoord"]);
	$data2=liste_etude_2($_POST["id"]);
	//id,jour_semaine,heure,salle,pion,nom_etude,duree
	history_cmd($_SESSION["nom"],"VALIDATION","Plan Etude : ".$data2[0][5]." ");
	alertJs(LANGDONENR);
}
$data=liste_eleve_etude_trombi($idetude); //id_eleve,id_etude,information,auto_exit
for($i=0;$i<count($data);$i++) {
	$idEleve=$data[$i][0];
	$idclasse="-".$data[$i][1];
	if ($data[$i][0] > 0) {
		$x=cherchePlanX($data[$i][0],"$idclasse");
		$y=cherchePlanY($data[$i][0],"$idclasse");
		if ($y == "-1") {
			$y=45;	
			$x=40*$i;
			if ($x == 0) {$x=10;}
		}
		$idid="E".$data[$i][0];
	}
?>
	<div align="center" id="<?php print $idid ?>" style="position:absolute;top:<?php print $y?>;left:<?php print $x?>;z-index:1;">
	<img src="image_trombi.php?idE=<?php print $data[$i][0]?>" /><br><br><?php print strtoupper(recherche_eleve_nom($data[$i][0]))."<br>".ucwords(recherche_eleve_prenom($data[$i][0]))?>
	</div>
<?php 
	$listeDiv.="\"$idid\",";
}
unset($idid);

$x=cherchePlanX("-$idclasse","$idclasse");
$y=cherchePlanY("-$idclasse","$idclasse");
if ($y == "-1") {
	$y=20;	
	$x=600;
}
$idid="B".$idclasse;
?>
	<div id="<?php print $idid ?>" style="position:absolute;top:<?php print $y?>;left:<?php print $x?>;z-index:1;width:130px;height:50px;background-color:#CCCCCC; border: solid #000 1px; text-align: center;text-valign: center;font-weight: bold;" ><br>BUREAU</div>

<?php
// deconnexion en fin de fichier
Pgclose();
?><ul><br>
<form name="formulaire" method="post" action="planclasseetude.php">
<input type=hidden name="newcoord" size=50 >
<input type=hidden name="id" value="<?php print $idetude ?>" >
<input type=button onclick="capture_position()" value="Enregistrer l'emplacement" class="BUTTON" > 
<a href="javascript:window.print();"><img src="image/commun/print.gif" border=0 align=center></a>
<i>Déplacer les photos en fonction du plan de la classe</i>
</form>
</ul>

<?php
$listeDiv.="\"$idid\"";
$listeDiv=preg_replace('/,$/',"",$listeDiv);
?>
<script type="text/javascript">
<!--
SET_DHTML(CURSOR_MOVE, TRANSPARENT, <?php print $listeDiv ?>);
-->
</script>

<script language="JavaScript" >
function capture_position() {
	var valeur="";
	<?php
	for($i=0;$i<count($data);$i++) {
		print "\tval0=\"E".$data[$i][0]."\";\n";
		//o0.x; o0.y;
		print "\to0=dd.elements[val0]; \n";
		print "\tvaleur+=val0+\"(\"+o0.x+\";\"+o0.y+\"),\";\n";
	}
	print "\tval0=\"B".$idclasse."\";\n";
	print "\to0=dd.elements[val0]; \n";
	print "\tvaleur+=val0+\"(\"+o0.x+\";\"+o0.y+\"),\";\n";


	print "\tdocument.formulaire.newcoord.value=valeur;\n";
	print "\tdocument.formulaire.submit()";
	?>
}

</script>





</BODY>
</HTML>
