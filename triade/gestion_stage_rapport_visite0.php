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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete(3);
$cnx=cnx();
error($cnx);
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1'><?php print "Compte rendu de rapport de visite" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top><br>
<?php




if (isset($_GET["id"])) {
	$eid=$_GET["id"];
	$prenom=recherche_eleve_prenom($eid);
	$nom=recherche_eleve_nom($eid);
	$idclasse=chercheIdClasseDunEleve($eid);
	$nomprenomeleve="$nom $prenom";
}



$fichier=$_FILES['fichier']['name'];
$type=$_FILES['fichier']['type'];
$tmp_name=$_FILES['fichier']['tmp_name'];
$size=$_FILES['fichier']['size'];
//alertJs($type);

if (UPLOADIMG == "oui") {
	$taille=8000000;
}else{
	$taille=2000000;
}

$ficmd5="";

if ((!empty($fichier)) &&  ($size <= $taille)) {
	if  ((preg_match('/text/i',$type))||(preg_match('/pdf/i',$type))||(preg_match('/msword/i',$type))||(preg_match('/opendocument/i',$type))||($type == "application/force-download")) {
	//	print "Nom du fichier :".$fichier." ".$type." ".$size." ".$tmp_name." ";
		$ficmd5=md5(time().rand('0000','9999'));
		if (!is_dir("./data/pdf_stage")) { mkdir("./data/pdf_stage"); }
		move_uploaded_file($tmp_name,"data/pdf_stage/$ficmd5");
	}
}

if (isset($_POST["createcontrerendu"])) {
	$identreprise=$_POST["identreprise"];
	$ideleve=$_POST["ideleve"];
	$idstage=$_POST["idstage"];
	$eid=$ideleve;
	$prenom=recherche_eleve_prenom($eid);
	$nom=recherche_eleve_nom($eid);
	$idclasse=chercheIdClasseDunEleve($eid);
	$nomprenomeleve="$nom $prenom";
	$contrerendu=$_POST["contrerendu"];
	$heurevisite=$_POST["heurevisite"];
	$datevisite=$_POST["datevisite"];
	$idprofvisiteur=$_POST["idprofvisiteur"];
	$cr=enrContreRendu($identreprise,$ideleve,$idstage,$contrerendu,$heurevisite,$datevisite,$_SESSION["nom"],$_SESSION["prenom"],$ficmd5,$fichier,$idprofvisiteur);
	if ($cr) history_cmd($_SESSION["nom"],"AJOUTER","Contre rendu $nomprenomeleve");

}

if (isset($_POST["modifcontrerendu"])) {
	$identreprise=$_POST["identreprise"];
	$ideleve=$_POST["ideleve"];
	$eid=$ideleve;
	$prenom=recherche_eleve_prenom($eid);
	$nom=recherche_eleve_nom($eid);
	$idclasse=chercheIdClasseDunEleve($eid);
	$nomprenomeleve="$nom $prenom";
	$idstage=$_POST["idstage"];
	$contrerendu=$_POST["contrerendu"];
	$heurevisite=$_POST["heurevisite"];
	$datevisite=$_POST["datevisite"];
	$idcontrerendu=$_POST["idcontrerendu"];
	$idprofvisiteur=$_POST["idprofvisiteur"];
	$cr=modifContreRendu($identreprise,$ideleve,$idstage,$contrerendu,$heurevisite,$datevisite,$_SESSION["nom"],$_SESSION["prenom"],$idcontrerendu,$ficmd5,$fichier,$idprofvisiteur);
	if ($cr) history_cmd($_SESSION["nom"],"MODIFIER","Contre rendu $nomprenomeleve");

}



if (isset($_GET["nc"])) {
	$nc="?nc";
	$nc2="&nc";
}
print "<form method=post action='gestion_stage_visu_eleve.php$nc'>";
print "<table border=0 width=100%><tr><td valign=top><b>".ucwords($prenom)." ".strtoupper($nom)."</b></td><td valign=top>";
print "<input type=submit value='".LANGSTAGE73."' class='BUTTON' >";
print "<input type=hidden name=saisie_classe value='".chercheIdClasseDunEleve($eid)."'></form>";
print "</td></tr></table><table width=100% border=1 >";
print "<tr ><td width=5 bgcolor='yellow' >&nbsp;N°&nbsp;Stage&nbsp;</td>";
print "<td align=center  bgcolor='yellow' >".LANGSTAGE72."</td>";
print "<td align=center  bgcolor='yellow'  width=40%>&nbsp;".LANGSTAGE39."&nbsp;</td>";
print "<td align=center  bgcolor='yellow'  >&nbsp;".LANGSTAGE37."&nbsp;</td></tr>";




$data=recherche_stage_eleve($eid);
// id_eleve,id_entreprise,lieu_stage,ville_stage,id_prof_visite,date_visite_prof,loger,nourri,passage_x_service,raison,info_plus,num_stage,code_p,id,tuteur_stage,tel,compte_tuteur_stage,alternance,jour_alternance,dateDebutAlternance,dateFinAlternance
for($i=0;$i<count($data);$i++) {
	if ($data[$i][17] == 1) { 
		$etat="Alternance"; 
		$date=dateForm($data[$i][19]).' au '.dateForm($data[$i][20]);
	}else{ 
		$etat=LANGSTAGE50; 
		$date=recherchedatestage2($data[$i][11],$idclasse);
	}
?>

	<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<td><?php print $etat ?> <?php print rechercheNumStage($data[$i][11]) ?></td>
	<td align=center><?php print $date ?></td>
	<td>&nbsp;<?php $numstage=rechercheNumStage($data[$i][11]); $identr=$data[$i][1]; print trunchaine(recherche_entr_nom_via_id($identr),20); ?> </td>
	<td width=5 align=center><input type=button onclick="open('gestion_stage_rapport_visite.php?eid=<?php print $eid?>&idclasse=<?php print $idclasse?>&idstage=<?php print $data[$i][13]?>&identreprise=<?php print $data[$i][1] ?>','_parent','')" value="<?php print "Compte rendu" ?>" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"></td>
	</tr>
<?php
}
?>
</table>
</td></tr></table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION["membre"] == "menuadmin") :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
print "</SCRIPT>";
else :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
print "</SCRIPT>";
top_d();
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
print "</SCRIPT>";
endif ;
?>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
