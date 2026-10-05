<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
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
	$idclasse=chercheIdClasseDunEleve($mySession['Spid']);
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
<title>Triade - Compte de <?php print ucwords($mySession['Sp'])." ".strtoupper($mySession['Sn'])?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<style>
.visu-form { background:#fff; border:1px solid #dde0f0; border-radius:10px; padding:10px 14px; margin-bottom:8px; display:flex; align-items:center; gap:10px; flex-wrap:wrap; font-family:Electrolize,Trebuchet MS,Arial,sans-serif; }
.visu-lbl  { font-size:13px; font-weight:700; color:#080A66; white-space:nowrap; }
.btn-action { font-size:12px; font-weight:700; color:#fff; border:none; border-radius:6px; padding:7px 14px; cursor:pointer; transition:background .12s; font-family:Electrolize,Trebuchet MS,Arial,sans-serif; }
.btn-vdevoir   { background:#1565c0; }
.btn-vcontenu  { background:#283593; }
.btn-vobjectif { background:#4527a0; }
.btn-vprint    { background:#37474f; }
.btn-action:hover { filter:brightness(1.12); }
.nav-row { display:flex; justify-content:space-between; align-items:center; margin:10px 4px; }
.btn-nav { font-size:12px; font-weight:700; color:#fff; background:#1565c0; border:none; border-radius:6px; padding:7px 16px; cursor:pointer; font-family:Electrolize,Trebuchet MS,Arial,sans-serif; }
.btn-nav:hover { background:#1976d2; }
</style>
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
<form method="post" name="formulaire" action="cahiertext_visu_global.php">
<div class="visu-form">
  <span class="visu-lbl"><?php print LANGPROFN ?></span>
  <div style="display:inline-flex;align-items:center;gap:5px;">
    <input type="text" value="<?php print $date ?>" name="saisie_date" size="13" class="bouton2">
    <?php include_once("librairie_php/calendar.php"); calendar('id1','document.formulaire.saisie_date',$_SESSION["langue"],"0"); ?>
  </div>
  <button type="submit" name="create"   value="1" class="btn-action btn-vdevoir"><?php print LANGMESS98 ?></button>
  <button type="submit" name="contenu"  value="1" class="btn-action btn-vcontenu"><?php print LANGMESS92 ?></button>
  <button type="submit" name="objectif" value="1" class="btn-action btn-vobjectif"><?php print LANGMESS95 ?></button>
  <button type="button" onclick="window.print()" class="btn-action btn-vprint">Imprimer</button>
</div>
<input type="hidden" name="saisie_classe" value="<?php print $idclasse ?>">
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
		$sujet=LANGMESS92;
		$devoirvisu=0;
	}elseif ($devoirvisu == 2) {
		$data=affobjectifScolaireParent($idclasse,$date2,"date_contenu");
		$sujet=LANGMESS95;
		$devoirvisu=2;
	}else{
		$data=affdevoirScolaireParent($idclasse,$date2,"date_devoir");
		$sujet=LANGMESS98;
		$devoirvisu=1;
		$devoir=1;
	}
	print "<td valign=top width='20%'><br>";
	print "<div style=\" height:30;  border:solid 0px black;\">";
	print "<img src='image/commun/on1.gif' align=center width=8 height=8> <b><u>$sujet</u> :</b><br>";
	print "</div>";
	$cumultempsestime=0;
	$bgcolor="#CCCCCC";
	$affiche=0;
	for($j=0;$j<countTriade($data);$j++) {
		$tempsestime=$data[$j][10] ?? null;
		if ($devoirvisu == 1 && isset($data[$j][10]) && trim((string)$data[$j][10]) != '') {
			$cumultempsestime+=conv_en_seconde($data[$j][10]);
		}
		if (($tempsestime != "00:00:00") && ($devoirvisu==1) && (trim($tempsestime) != "") ) {
			$tempsestime="<br /><font class='T1'>".LANGMESS104." ".timeForm($tempsestime)."</font>";
		}
		$contenu=$data[$j][5];
		if (trim($contenu) == "") { continue; }
		$affiche=1;

		$number=$data[$j][8];
		if ($bgcolor == "#CCCCCC") {
			$bgcolor="#F1CFCF";
		}else{
			$bgcolor="#CCCCCC";
		}

		$datafile=recupPieceJointe($number); //md5,nom,etat,idpiecejointe
		$lienFichier="<br>";
		for($F=0;$F<countTriade($datafile);$F++) {
			$fichier=$datafile[$F][1];
			$md5=$datafile[$F][0];
			$lienFichier.="<img src='image/stockage/defaut.gif' align='center'> ".LANGMESS105." : <a href='telecharger.php?fichier=data/DevoirScolaire/${md5}&fichiername=$fichier' target='_blank' >".trunchaine($fichier,20)."</a><br>";

		}
		print "<div style=\"  border:solid 1px black;background-color:$bgcolor;padding:3px \"> ";
		if ($_SESSION["membre"] == "menuprof") {
			$id=$data[$j][7];
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
	if ($affiche == 0) {
		print "<font class='T1'><i>-</i></font>";
	}
	if ($devoir == 1)
	print "<br><div style=' height:20; border:solid 1px black;' id='coulBar0' ><font class='T1' id='menumodule1' >&nbsp;<font size=1>".LANGMESS108."&nbsp;:&nbsp;".timeForm(calcul_hours($cumultempsestime))."</font></font></div>";
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
<div class="nav-row">
  <button type="button" class="btn-nav" onclick="open('cahiertext_visu_global.php?iddate=<?php print $dateP ?>&id=<?php print $idclasse?>&devoirvisu=<?php print $devoirvisu?>','devoir','')">&#8592; <?php print LANGPROF35 ?></button>
  <button type="button" class="btn-nav" onclick="open('cahiertext_visu_global.php?iddate=<?php print $dateS ?>&id=<?php print $idclasse?>&devoirvisu=<?php print $devoirvisu?>','devoir','')"> <?php print LANGPROF36 ?> &#8594;</button>
</div>
</td></tr></table>
</BODY>
</HTML>
<?php @Pgclose() ?>
