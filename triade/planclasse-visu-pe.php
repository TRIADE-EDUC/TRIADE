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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<title>Triade - Trombinoscope</title>
</head>
<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php
include("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");

if (($_SESSION["membre"] == "menuparent") && (PLANCLASSEPARENT == "non")) {
	print "<br><br><center><font class=T2 color='red'>".LANGMESS37."</font></center>";

}else{


// connexion


$cnx=cnx();
$idclasse=chercheIdClasseDunEleve($_SESSION["id_pers"]);

$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);
$cl=$data[0][0];

for($i=0;$i<count($data);$i++) {

	$x=cherchePlanX($data[$i][1],$idclasse);
	$y=cherchePlanY($data[$i][1],$idclasse);
	if ($y == "-1") {
		$y=45;

		$x=40*$i;
		if ($x == 0) {$x=10;}
	}
?>
	<div align="center" id="E<?php print $data[$i][1] ?>" style="position:absolute;top:<?php print $y?>;left:<?php print $x?>;z-index:1;<?php print $visibility ?>">
	<?php
	if (  ((TROMBIELEVE == "non") && ($_SESSION["membre"] == "menueleve") && ($_SESSION["id_pers"] != $data[$i][1])) ||  ((TROMBIPARENT == "non") && ($_SESSION["membre"] == "menuparent") && ($_SESSION["id_pers"] != $data[$i][1])) ) {
		print "<img src='./image/commun/photo_vide.jpg' /><br>";
		print strtoupper($data[$i][2])."<br>".ucwords($data[$i][3]);
	}else{
		print "<img src='image_trombi.php?idE=".$data[$i][1]."' /><br>";
		print strtoupper($data[$i][2])."<br>".ucwords($data[$i][3]);
	}
?>
	


	</div>
<?php 
}

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

}

?><ul><br>
<!-- 
<form name="formulaire" method="post" >
<a href="javascript:window.print();"><img src="image/commun/print.gif" border=0 align=center></a>
</form>
-->
</ul>
</BODY>
</HTML>
