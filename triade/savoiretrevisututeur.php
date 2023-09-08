<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
        $anneeScolaire=$_POST["anneeScolaire"];
        setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
<?php 
include("./librairie_php/lib_licence.php"); 
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
 
if (trim($anneeScolaire) == "") $anneeScolaire=anneeScolaireViaIdClasse($idClasse);

if ($_SESSION["membre"] == "menututeur") { $Seid=""; } 

if (isset($_SESSION["idClasse"])) $idClasse=$_SESSION["idClasse"];

if (isset($_POST["idelevetuteur"])) {
        $Seid=$_POST["idelevetuteur"];
        $_SESSION["idelevetuteur"]=$Seid;
        $Scid=chercheClasseEleve($Seid);
	$idClasse=$Scid;
        $_SESSION["idClasse"]=$Scid;
}

if (isset($_SESSION["idelevetuteur"])) {
        $Seid=$_SESSION["idelevetuteur"];
        $Scid=chercheClasseEleve($Seid);
	$idClasse=$Scid;
}

if ((trim($Seid) == "") && ($_SESSION["membre"] == "menututeur")) {
         $list=listEleveTuteur2($_SESSION["id_pers"]);
         if (count($list) == 1) {
		$Seid=$list[0][0];
        	$Scid=chercheClasseEleve($Seid);
		$idClasse=$Scid;
	}
}

if (($_SESSION["membre"] == "menututeur") && (isset($_POST["create"]))) {
	$idclasse=$_POST["idclasse"];
	$idpers=$_SESSION["id_pers"];
        $ideleve=$_POST["ideleve"];
        $ponct=$_POST["ponct"];
        $motiv=$_POST["motiv"];
        $dynam=$_POST["dynam"];
        if ((trim($ponct) == "") && (trim($motiv) == "") && (trim($dynam) == "")) {

	}else{ 
        	if ($ideleve > 0) {
			saveSavoirEtre($ideleve,$idclasse,$anneeScolaire,$ponct,$motiv,$dynam,$idpers);
			$nomEtudiant=recherche_eleve_nom($ideleve);
			$sujet="Mr ".$_SESSION["nom"]." a saisi de nouvelles informations concernant le savoir-être de l'étudiant(e) $nomEtudiant";
			$message="<br>Vous pouvez consulter ces informations dans le menu savoir-être.<br><br>L'Equipe Triade.";
			$email_expediteur=recupEmail($_SESSION["membre"],$idpers,'');
			$nom_expediteur=strtoupper($_SESSION["nom"])." ".ucfirst($_SESSION["prenom"]);
			$data=rechercheprofpMulti($idclasse);
			for($i=0;$i<count($data);$i++) {
				$idprofp=$data[$i][0];
				$to=recupEmail("menuprof",$idprofp,'');
				mailTriade($sujet,$message,$message,$to,$email_expediteur,$email_expediteur,$nom_expediteur,"");
//		print	"test->	mailTriade($sujet,$message,$message,$to,$email_expediteur,$email_expediteur,$nom_expediteur,'');";
			}
		}
	}
}


?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method='post' action='savoiretrevisututeur.php' >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Savoir / être" ?></font></b>
<?php
if ($_SESSION["membre"] == "menututeur") {
?>
        &nbsp;&nbsp;
        <select name='idelevetuteur' onchange="this.form.submit()" >
                <?php
                if ($Seid != "") {
                        $nom=recherche_eleve_nom($Seid);
                        $prenom=recherche_eleve_prenom($Seid);
                        print "<option id='select1' value='$Seid' title=\"".strtoupper($nom)." $prenom\" >".trunchaine(strtoupper($nom)." ".$prenom,30)."</option>\n";
                }else{
                        print "<option id='select0' >".LANGCHOIX."</option>";
                }
                listEleveTuteur($_SESSION["id_pers"],30)
                ?>
        </select>
<?php
}
?>

<font id='menumodule1' ><?php print LANGBULL3 ?> :</font> <select name='anneeScolaire' onchange="this.form.submit()" >
                 <?php
                 filtreAnneeScolaireSelectNote($anneeScolaire,8);
                 ?>
</select>

</td></tr>
</form>
<tr id='cadreCentral0'>
<td valign='top' >
<table border='1' width='100%' style="border-collapse: collapse;" >
<tr >
<td bgcolor="yellow"><?php print "Date" ?></td>
<td bgcolor="yellow"><?php print "Enseignant" ?></td>
<td bgcolor="yellow"><?php print "Aptitude à manifester de l'intérêt pour son travail" ?></td>
<td bgcolor="yellow"><?php print "Aptitude à la méthode et au soin" ?></td>
<td bgcolor="yellow"><?php print "Aptitude à écouter" ?></td>
</tr>
<?php
$dataInfo=recupSavoirEtre($Seid,$idClasse,$anneeScolaire);
for($j=0;$j<count($dataInfo);$j++) { 
	$ponct=stripslashes($dataInfo[$j][0]);
	$motiv=stripslashes($dataInfo[$j][1]);
	$dynam=stripslashes($dataInfo[$j][2]);
	$nommatiere=chercheMatiereNom($dataInfo[$j][6]);
	$id=$dataInfo[$j][3];
        if (($ponct == "") && ($motiv == "") && ($dynam == "")) {
                deleteSavoirEtre2($id);
                continue;
        }
	$date=dateForm($dataInfo[$j][4]);
	$idpers=$dataInfo[$j][5];
        $personne=preg_replace('/ /','&nbsp;',recherche_personne2($idpers));

	$motiv=preg_replace('/"/',"&quot;",$motiv);
	$dynam=preg_replace('/"/',"&quot;",$dynam);
	$ponct=preg_replace('/"/',"&quot;",$ponct); 
	print "<tr bgcolor='#FFFFFF' >";
	print "<td width='10%' valign='top' ><font class='T1'>$date</font></td>";
	print "<td width='10%' valign='top' ><font class='T1'>$personne<br>Matière&nbsp;:&nbsp;$nommatiere</font></td>";
	print "<td width='30%' valign='top' ><font class='T2'>$ponct</font></td>";
	print "<td width='30%' valign='top' ><font class='T2'>$motiv</font></td>";			
	print "<td width='30%' valign='top' ><font class='T2'>$dynam</font></td>";
}
?>
</table>
<br><br>
<font class='T2 shadow'>Ajouter votre remarque pour l'année scolaire <?php print $anneeScolaire ?> </font><br><br>
<form method='post' action='savoiretrevisututeur.php' >
<table border='1' width='100%' style="border-collapse: collapse;" >
<tr >
<td bgcolor="yellow"><?php print "Aptitude à manifester de l'intérêt pour son travail" ?></td>
<td bgcolor="yellow"><?php print "Aptitude à la méthode et au soin" ?></td>
<td bgcolor="yellow"><?php print "Aptitude à écouter" ?></td>
</tr>
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
        <td ><input type='text' name='ponct' size='50' maxlength='250'  /></td>
        <td ><input type='text' name='motiv' size='35' maxlength='250'  /></td>
        <td ><input type='text' name='dynam' size='35' maxlength='250'  /></td>
</tr>
<input type='hidden' name='ideleve' value="<?php print "$Seid" ?>" />
<input type='hidden' name='idclasse' value="<?php print "$idClasse" ?>" />
<?php
print "<tr><td colspan='5' height='40' bgcolor='#FFFFFF' align='center'><table align='center'><tr><td><script language=JavaScript>buttonMagicSubmit('".VALIDER."','create');</script></td></tr></table></td></tr>";
?>
</table>
</form>
</td></tr></table>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")):
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
            print "</SCRIPT>";

       endif ;

// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
