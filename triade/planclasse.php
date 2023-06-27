<?php
session_start();
if (isset($_POST["formattrombi"])) {
	setcookie("formattrombi",$_POST["formattrombi"],time()+36000*24*30);
	$formattrombi=$_POST["formattrombi"];
}else{
	$formattrombi=$_COOKIE["formattrombi"];	
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
if ($formattrombi == "") $formattrombi="paysage"; 

include("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");

validerequete("profadmin");
$cnx=cnx();

if (isset($_POST["sClasseGrp"])) {
	$idclasse=$_POST["sClasseGrp"];
}

if (isset($_GET["idclasse"])) {
	$idclasse=$_GET["idclasse"];
	if ($_SESSION["membre"] == "menuprof") {
		verif_profp_class($_SESSION["id_pers"],$idclasse);
	}
}

if (isset($_POST["newcoord"])) {
	$idclasse=$_POST["saisie_classe"];
	if ($_SESSION["membre"] == "menuprof") { verif_profp_class($_SESSION["id_pers"],$idclasse); }
	eregist_planclasse($idclasse,$_POST["newcoord"]);
	$nomclasse=chercheClasse_nom($idclasse);
	history_cmd($_SESSION["nom"],"VALIDATION","Plan Classe : $nomclasse");
	alertJs(LANGDONENR);
	
}

$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);
$cl=$data[0][0];

for($i=0;$i<count($data);$i++) {

	if ($data[$i][1] > 0) {
		$x=cherchePlanX($data[$i][1],$idclasse);
		$y=cherchePlanY($data[$i][1],$idclasse);
		if ($y == "-1") {
			$y=45;	
			$x=40*$i;
			if ($x == 0) {$x=10;}
		}
		$idid="E".$data[$i][1];
	        $width="96";$height="96";
	        $photoLocal=recherche_photo_eleve($data[$i][1]);
	        $fic="./data/image_eleve/$photoLocal";
	        list($width, $height, $type, $attr) = getimagesize("$fic");
	        if ($width == "") { $width="96"; $height="96"; }
							
	}
?>
	<a><div align="center" id="<?php print $idid ?>" style="position:absolute;top:<?php print $y?>;left:<?php print $x?>;z-index:1;width:<?php print $width ?>;height:<?php print $height ?>"><img <?php print "$width $height" ?>  src="image_trombi.php?idE=<?php print $data[$i][1]?>" /><div style='background-color:#CCCCCC;width:<?php print $width ?>; ' ><?php print strtoupper($data[$i][2])."<br>".ucwords($data[$i][3])?></div></a>
	</div>
<?php 
	$listeDiv.="\"$idid\",";
}
unset($idid);

$x=cherchePlanX("-$idclasse",$idclasse);
$y=cherchePlanY("-$idclasse",$idclasse);
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
<?php 
$checkedformatpaysage="";$checkedformatportrait="";
if ($formattrombi == "paysage") $checkedformatpaysage="checked='checked'"; 
if ($formattrombi == "portrait") $checkedformatportrait="checked='checked'"; 
?>
<table><tr>
<form method="post"><td>
<input type='radio' name='formattrombi' value='paysage' <?php print $checkedformatpaysage ?> onclick="this.form.submit()" > Paysage 
<input type='radio' name='formattrombi' value='portrait' <?php print $checkedformatportrait ?> onclick="this.form.submit()" > Portrait 
</td></form>
<form name="formulaire" method="post" >
<td><input type=hidden name="newcoord" size=50 ><input type=hidden name="saisie_classe" value="<?php print $idclasse ?>" >
<input type=button onclick="capture_position()" value="Enregistrer l'emplacement" class="BUTTON" > 
</td>
</form>
<form action="planclasse-visu0.php" method="post">
<td><input type="image" src="image/commun/print.gif" onclick="this.form.submit();" >
<input type="hidden" name="print" >
<input type="hidden" name="sClasseGrp" value="<?php print $idclasse ?>" ></td>
<input type="hidden" name="format" value="<?php print $formattrombi ?>" ></td>
</form>
</tr></table>
<i>Déplacer les photos en fonction du plan de la classe</i>

</ul>
<?php  if ($formattrombi == "paysage") { ?>
	<div style="position:relative;width:29.7cm;height:21cm;border: 1px black solid;">
<?php }else { ?>
	<div style="position:relative;width:21cm;height:29.7cm;border: 1px black solid;">
<?php } ?>
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
		print "\tval0=\"E".$data[$i][1]."\";\n";
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

</div>



</BODY>
</HTML>
