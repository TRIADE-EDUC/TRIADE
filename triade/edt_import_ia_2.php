<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  - 
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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/docopy.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/ajaxIA.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
if ($_SESSION["membre"] == "menupersonnel") {
        if ((!verifDroit($_SESSION["id_pers"],"edt"))  && (!verifDroit($_SESSION["id_pers"],"AESH")) )  {
                accesNonReserveFen();
                exit;
        }
}else{
	validerequete("2");
}
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Importer une image pour analyse à votre EDT"  ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<!-- // fin  -->
<br />
<font class='T2'>
<?php

$idclasse=$_POST['saisie_classe'];
$date_debut=$_POST['date_debut'];


$json=$_POST['edt'];


$json=stripslashes($json);
$json=stripslashes($json);
$json=preg_replace('/```",/',"",$json);
$json=strip_tags($json);
//print $json;
//print "<hr>";
//$json=preg_replace('/^[^:]*:/',"",$json);
//$json=preg_replace('/\}$/', '', $json);
$data = json_decode($json,true);
$error=0;
if (json_last_error() === JSON_ERROR_NONE) {
    //	echo "&nbsp;&nbsp;Image valide <br><br>";
//	inspect_json_structure($data);

} else {
    //$messerror="Erreur Image : " . json_last_error_msg();
    $messerror="Vous ne disposez pas de token suffisant !! ";
    $error=1;
}

print "<br><center>Pour la classe ".chercheClasse_nom($idclasse)." à partir du $date_debut </center><br> ";

$date=$date_debut;
$i=0;
foreach ($data as $jour=>$val) {
    foreach ($val as $seance) {
	if ($i != 0) {	    
		if ($val["jour_de_semaine"] != $journ) $date=dateForm(dateplusn($date,1));
	}
	$i=1;

	$jour=$val["jour_de_semaine"];
	$debut=$val["debut"];
	$jusquau=$_POST["jusquau"];
	$duree=$val["duree"];
	$matiere=$val["matiere"];
	$couleur=$val["couleur"];

/*
        echo "  Jour : " . $jour  . "<br>"; // Affiche le jour
        echo "  date : " . $date . "<br>";
        echo "  Début : " . $debut . "<br>";
        echo "  jusqu'au : " . $jusquau . "<br>";
        echo "  Durée : " . $duree . "<br>";
        echo "  Matière : " . $matiere . "<br>";
        echo "  Code couleur : " . $couleur . "<br>";
        echo "<br>"; // Ajoute une ligne vide entre chaque séance
 */
	$donnee["$jour#$date#$debut#$matiere#$duree"]="$couleur:$jusquau";
 
	$journ=$val["jour_de_semaine"];
    }
}


//print_r($donnee);


foreach($donnee as $key=>$value) {

	list($jour,$date,$debut,$matiere,$duree) = preg_split('/#/',$key);
	list($couleur,$jusquau) = preg_split('/:/',$value);

	$color=$couleur;
	$idmatiere=chercheIdMatiere2(addslashes($matiere));
	if ($idmatiere <= 0) { $eventDescription=addslashes($matiere); }
	$idprof="";
	$prestation="";
	$day=substr($jour,0,2);
	$recursive="";
	$coursannule="";
	$docdst="";
	$emargement="";
	$idressource="";
	$dureehoraire="";
	$heure=$debut;
	$duree=$duree;
	$affichehoraire="";

	switch(strtolower($day)) {
       		case "di" :  $day="7" ; break;
                case "lu" :  $day="1" ; break;
                case "ma" :  $day="2" ; break;
                case "me" :  $day="3" ; break;
                case "je" :  $day="4" ; break;
                case "ve" :  $day="5" ; break;
                case "sa" :  $day="6" ; break;
        }
	$tabJours[]=$day;

	CreateEdt($eventDescription,$color,$idclasse,$idprof,$jusquau,$prestation,$tabJours,$recursive,$idmatiere,$coursannule,$docdst,$emargement,$idressource,$duree,$heure,$date,$affichehoraire);
	unset($tabJours);
	
}

?>
<?php
if ($error == 0) { ?>
	<br><center>Importation terminée
	<br><br><br>
<?php
}else{
?>
	<br><center><font color='red'>Importation Error</font>
	<br><?php print $messerror ?>
	<br><br><br>
	

<?php } ?>
<input type=button class="bouton2" onclick="open('edt.php','_parent','')" value="<?php print "Retour menu EDT"  ?>" />
</center>

</font>

<br />
<br />
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php PgClose();  ?>
</BODY></HTML>
