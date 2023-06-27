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
include_once("./common/config.inc.php"); // futur : auto_prepend_file
include_once("./librairie_php/db_triade.php");
$cnx=cnx();

// Sn : variable de Session nom
// Sp : variable de Session prenom
// Sm : variable de Session membre
// Spid : variable de Session pers_id
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);
if (($_SESSION["membre"] == "menueleve") || ($_SESSION["membre"] == "menuparent")) {
	$idclasse=chercheIdClasseDunEleve($mySession[Spid]);
}
if (isset($_GET["saisie_classe"])) { 
	$idclasse=$_GET["saisie_classe"]; 
}
if (isset($_GET["id"])) {
	$idclasse=$_GET["id"];
}
if (isset($_POST["saisie_classe"])) {
	$idclasse=$_POST["saisie_classe"];
}
$nomclasse=chercheClasse($idclasse);
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<title>Triade - Compte de <?php print ucwords($mySession[Sp])." ".strtoupper($mySession[Sn])?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/scriptaculous.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_visadirec.js"></script>
<script type="text/javascript" src="./FCKeditor/fckeditor.js"></script>
<script type="text/javascript">
window.onload = function()
{
	<?php
	$typedefen="cahierdetext";
	?>
	var oFCKeditor = new FCKeditor('saisie_contenu','97%','200','<?php print $typedefen?>','') ;
	oFCKeditor.BasePath = './FCKeditor/' ;
	oFCKeditor.ReplaceTextarea() ;
}
</script>
</head>
<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<table border="0" cellpadding="3" cellspacing="1" width="100%"  height="100%">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1'><?php print LANGPROF37 ?> - </b><font id="color2"><?php print ucwords($nomclasse[0][1])?></font></td></tr>
<tr >
<td valign='top'>
<!-- // fin  -->
<?php
$date=dateDMY();
if (isset($_GET["iddate"])) {
	$date=dateForm($_GET["iddate"]);
}
if (isset($_POST["saisie_date"])) {
	$date=$_POST["saisie_date"];
}
$devoirvisu=0;

if (isset($_POST["contenu"])) {
	$devoirvisu=0;	
}
if (isset($_POST["objectif"])) {
	$devoirvisu=2;	
}
if (isset($_POST["create"])) {
	$devoirvisu=1;	
}
if (isset($_GET["devoirvisu"])) {
	$devoirvisu=$_GET["devoirvisu"];
}
?>
<table width='100%' border='0' >
<ul>
<tr><td colspan=2>
<form method=post name="formulaire" action="cahiertext_visu_global.php">
<table border=0>
<tr><td>
<?php print LANGPROFN ?> <input type=text value="<?php print $date ?>" name='saisie_date' size='12' class='bouton2' />
<?php
include_once("librairie_php/calendar.php");
calendar('id1','document.formulaire.saisie_date',$_SESSION["langue"],"0");
?>
</td><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGMESS98 ?>","create"); //text,nomInput</script>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGMESS92 ?>","contenu"); //text,nomInput</script>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGMESS95 ?>","objectif"); //text,nomInput</script>
<script language=JavaScript>buttonMagicImprimer(); //text,nomInput</script>&nbsp;&nbsp;
</td></tr></table>
<input type="hidden" name="saisie_classe" value="<?php print $idclasse?>" />
</form>
</ul>
<?php 
$hauteur=240;
if ($_SESSION["navigateur"] == "NONIE") { $hauteur=340; }
?>
<div id="visdir" style="position:absolute;top:140;left:330;display:none;width:550px;height:<?php print $hauteur?>px;padding:1px;border:1px #666 solid;background-color:#4FB091;z-index:1000">
<form name="form11">
<input type='hidden' name='iddevoir' id='devoir' >
<div style="position:absolute;top:5;left:0;width:550px" ><a href='#' onclick="supprDevoir(document.form11.iddevoir.value,'retourenr0','<?php print $devoirvisu ?>')" title="<?php print LANGMESS107 ?>"><img src="image/commun/trash.png"   border='0' /></a></div>
<br /><br />
<?php
$cols1=70;
if ($_SESSION["navigateur"] == "NONIE") { $cols1=45; }
print "<font class=T2><b>&nbsp;&nbsp;".LANGMESS106."</b> : </font><br /><br />";
if (($_SESSION["navigateur"] != "IE") || ($_SESSION["navigateur"] != "MO")) {
	print "&nbsp;&nbsp;<textarea name='saisie_contenu' id='editor' cols=$cols1 rows=7 STYLE='font-family: Arial;font-size:12px;background-color:#FCE4BA;' ></textarea><br /><br />";
}else{
	print "&nbsp;&nbsp;<textarea name='saisie_contenu' id='editor' cols=$cols1 STYLE='font-family: Arial;font-size:12px;background-color:#FCE4BA;' ></textarea>";
}
print "<br><br>";

?>
	&nbsp;&nbsp;&nbsp;<input type='button' onclick="envoiDevoir(this.form.iddevoir.value)" value="<?php print LANGENR ?>" class='bouton2' />
	&nbsp;&nbsp;<input type='button' value='<?php print LANGFERMERFEN ?>' class='button' onclick="new Effect.Shrink('visdir', 1)" />

	 <span id='retourenr0' ></span>
	</form>
</div>

<script>
function envoiDevoir(iddevoir) {
        var commentaire = FCKeditorAPI.GetInstance( 'saisie_contenu' ).GetXHTML();
        enrDevoir(iddevoir,commentaire,'retourenr0','<?php print $devoirvisu ?>');

}

</script>
		

<?php
$nb=4; // nombre de jour à afficher
print "<table border=0    align='center' height='100%' width='100%' >";
print "<tr >";
for($i=0;$i<=$nb;$i++) {
	$date2=dateplusn($date,$i);
	print "<td>&nbsp;&nbsp;&nbsp; ".dateform($date2)."</td>";
}
print "</tr><tr>";
$devoir=0;



for($i=0;$i<=$nb;$i++) {
	$date2=dateplusn($date,$i);
	$date2=dateForm($date2);
	if ($devoirvisu == 0) {
		$data=affcontenuScolaireParent($idclasse,$date2,"date_contenu");
		// id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_contenu, contenu, classorgrp, id, number, idprof
		$sujet=LANGMESS92;
		$devoirvisu=0;
	}elseif ($devoirvisu == 2) {
		$data=affobjectifScolaireParent($idclasse,$date2,"date_contenu");
		// id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_contenu, objectif, classorgrp, id, number_obj, idprof
		$sujet=LANGMESS95;
		$devoirvisu=2;
	}else{
		$data=affdevoirScolaireParent($idclasse,$date2,"date_devoir");
		// id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_devoir, texte, classorgrp, id, number, idprof,tempsestimedevoir
		$sujet=LANGMESS98;
		$devoirvisu=1;
		$devoir=1;
	}
	print "<td valign=top width='20%'><br>";
	print "<div style=\" height:30;  border:solid 0px black;\">";
	print "<img src='image/commun/on1.gif' align=center width=8 height=8> <b><u>$sujet</u> :</b><br>";
	print "</div>";
	$cumultempsestime=0;
	for($j=0;$j<count($data);$j++) {
		$tempsestime=$data[$j][11];
		$cumultempsestime+=conv_en_seconde($data[$j][10]);
		if (($tempsestime != "00:00:00") && ($devoirvisu==1) && (trim($tempsestime) != "") ) {
			$tempsestime="<br /><font class='T1'>".LANGMESS104." ".timeForm($tempsestime)."</font>";
		}
		if (isset($_POST["contenu"])) {
			$contenu=$data[$j][5];
			if (trim($contenu) == "") { continue; }
		}else{
			$contenu=$data[$j][5];
			if (trim($contenu) == "") { continue; }
		}

		$number=$data[$j][8];
		if ($bgcolor == "#CCCCCC") {
			$bgcolor="#F1CFCF";
		}else{
			$bgcolor="#CCCCCC";
		}

		$datafile=recupPieceJointe($number); //md5,nom,etat,idpiecejointe
		$lienFichier="<br>";
		for($F=0;$F<count($datafile);$F++) {
			$fichier=$datafile[$F][1];
			$md5=$datafile[$F][0];
			$lienFichier.="<img src='image/stockage/defaut.gif' align='center'> ".LANGMESS105." : <a href='telecharger.php?fichier=data/DevoirScolaire/${md5}&fichiername=$fichier' target='_blank' >".trunchaine($fichier,20)."</a><br>";
			
		}
		print "<div style=\"  border:solid 1px black;background-color:$bgcolor \"> ";
		if ($_SESSION["membre"] == "menuprof") {
			if ($devoirvisu == "0") $verifedit=verifEditeContenu($id,$_SESSION["id_pers"]);
			if ($devoirvisu == "2") $verifedit=verifEditeObjectif($id,$_SESSION["id_pers"]);
			if ($devoirvisu == "1") $verifedit=verifEditeDevoir($id,$_SESSION["id_pers"]);
			if ($verifedit) {
				print "&nbsp;<a href='#' onclick=\"new Effect.Grow('visdir', 1); afficheDevoir('$id','$devoirvisu'); return false;\" ><img src='image/commun/editer.gif' align='center' border='0'></a>";
			}
		}
		print "&nbsp;<font color=blue>".ucfirst(chercheMatiereNom($data[$j][1]))."</font> ";
		if ($tempsestime == "00:00:00") $tempsestime="";
		print "$tempsestime";
	        print "<br><font class='T1'><i>(".ucwords(LANGPROFK)." ".dateForm($data[$j][2]).")</i></font>";
		print "&nbsp;&nbsp;".$contenu;
		print "$lienFichier";
		print "</div>";
	}
	if ($devoir == 1)
	print "<br><div style=' height:20; border:solid 1px black;' id='coulBar0' ><font class='T1'  id='menumodule1' >&nbsp;".LANGMESS108."&nbsp;:&nbsp;".timeForm(calcul_hours($cumultempsestime))."</font></div>";
	print "</td>";
}
print "</tr>";
?>
</table>
<br>
<?php
$nb=$nb + 1;
$dateS=datesuivante_nb($date,$nb);
$dateP=dateprecedent_nb($date,$nb);
?>
<table border='0' width='100%' align='center' >
<tr><td align=left>
&nbsp;&nbsp;<input type=button value="<-- <?php print LANGPROF35 ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" onclick="open('cahiertext_visu_global.php?iddate=<?php print $dateP ?>&id=<?php print $idclasse?>&devoirvisu=<?php print $devoirvisu?>','devoir','')" >
</td>
<td align=right>
&nbsp;&nbsp;
<input type=button value="<?php print LANGPROF36 ?> --> "  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" onclick="open('cahiertext_visu_global.php?iddate=<?php print $dateS ?>&id=<?php print $idclasse?>&devoirvisu=<?php print $devoirvisu?>','devoir','')" >
</td></tr>
</table>
</td></tr></table>
</BODY>
</HTML>
<?php @Pgclose() ?>
