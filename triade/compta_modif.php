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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
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
validerequete("menuadmin");
$cnx=cnx();

$idversement=$_GET["idvers"];
$datevers=$_GET["date"];
$ideleve=$_GET["ideleve"];
$montant=chercheMontantVersement($ideleve,$datevers,$idversement);
if (trim($montant) == "") {
	print "<script>location.href='compta_liste2.php?ideleve=".$ideleve."&err'</script>";
}
$nomVersement=chercheNomVersement($idversement);
$modepaiement=chercheModePaiement($ideleve,$datevers,$idversement);
$numcheque=chercheNumCheque($ideleve,$datevers,$idversement);
$banque=html_quotes(chercheEtabBancaire($ideleve,$datevers,$idversement));
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Gestion encaissement" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >

<?php
if (!isset($_GET["ideleve"])) {
	$nomEleve=$_POST["saisie_nom_eleve"];
	$sql="SELECT elev_id,nom,prenom,classe FROM  ${prefixe}eleves  WHERE  nom='$nomEleve' ";
	$res=execSql($sql);
	$data=ChargeMat($res);
	if (count($data) > 1) {
		print "<table border='1' width='100%' bordercolor='#000000' >";
		print "<tr><td bgcolor='yellow'>Nom Prénom</td><td bgcolor='yellow' >Classe</td>";
		print "<td bgcolor='yellow' align='center' >Sélectionner</td>";
		print "</tr>";
		for($i=0;$i<count($data);$i++) {
			print "<tr  class='tabnormal' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\">";
			print "<td>".$data[$i][1]." ".$data[$i][2]."</td><td>".chercheClasse_nom($data[$i][3])."</td>";
			print "<td width='5%'><input type='button' onclick=\"open('compta_modif.php?ideleve=".$data[$i][0]."','_parent','')\" class='button' value='Sélectionner' /></td>";
			print "</tr>";
		}
		print "</table>";
	}else{
		$ideleve=$data[0][0];
	}
}else{
	$ideleve=$_GET["ideleve"];
}

if ($ideleve > 0) {
	$nomeleve=recherche_eleve_nom($ideleve);
	$prenomeleve=recherche_eleve_prenom($ideleve);
	$idclasse=chercheClasseEleve($ideleve);
	$classe=chercheClasse_nom($idclasse);

?>
	<form name="formulaire" method="post" action='compta_modif2.php' >
	<input type="hidden" name="ideleve" value="<?php print $ideleve ?>" />
	<input type="hidden" name="oldidvers" value="<?php print $idversement ?>" />
	<input type="hidden" name="oldiddate" value="<?php print $datevers ?>" />
	<table>
	<tr><td valign='top' ><img src="image_trombi.php?idE=<?php print $ideleve ?>" border=0 ></td>
	<td valign="top">
	&nbsp;&nbsp;<font class=T2>Nom : <?php print $nomeleve ?></font>
	<br><br>
	&nbsp;&nbsp;<font class=T2>Prénom : <?php print $prenomeleve ?></font>
	<br><br>
	&nbsp;&nbsp;<font class=T2>Classe : <?php print ucwords($classe) ?></font>
	</td></tr>
	</table>
	<br><br>
	<table width=100% >
	<tr><td align='right' ><font class='T2'> Type&nbsp;de&nbsp;versement&nbsp;:&nbsp;</font></td><td> <select name='typeversement' >
				<option id='select1' value='<?php print $idversement ?>' ><?php print $nomVersement ?></option>
				<?php 
					if (trim($idversement) == "") {
						print selectVersement($idclasse);
						print selectVersementEleve($ideleve);
					} ?>
				</select></td></tr>
							<tr><td align='right' ><font class='T2'>Montant&nbsp;réglé&nbsp;:&nbsp;</font></td>
							<td><input type=text name='montant' value='<?php print preg_replace('/ /','',affichageFormatMonnaie($montant)) ?>' ></td></tr>
							<tr><td align='right' ><font class='T2'>N°&nbsp;chèque&nbsp;:&nbsp;</font></td>
							<td><input type=text name='numcheque' value="<?php print $numcheque ?>" ></td></tr>

							<tr><td align='right' ><font class='T2'>Etablissement&nbsp;bancaire&nbsp;:&nbsp;</font></td>
							<td><input type=text name='banque' value="<?php print $banque ?>" ></td></tr>

							<tr><td align='right' valign='top' ><font class='T2'>Mode&nbsp;de&nbsp;paiement&nbsp;:&nbsp;</font></td>
							<td><textarea name='modepaiement' cols='50' rows='3' onkeypress="compter(this,'145', this.form.CharRestant)"  ><?php print $modepaiement ?></textarea>&nbsp;
							<?php $nbcar=strlen($modepaiement) ?>
							<input type=text name='CharRestant' size=3 disabled='disabled' value='<?php print $nbcar ?>' /></td></tr>
							<tr><td align='right' ><font class='T2'>Date&nbsp;d'encaissement&nbsp;:&nbsp;</font></td><td><input type="text" name="dateversement" value="<?php print dateForm($datevers) ?>" size=12 readonly> 
	<?php include_once("librairie_php/calendar.php"); calendarDim('id1','document.formulaire.dateversement',$_SESSION["langue"],"0","0");?>
	</td></tr>
	<tr><td height=20></td></tr>
	<tr><td colspan=2 align=center ><table><tr><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGPER30?>","create"); //text,nomInput</script></td><td><script language=JavaScript>buttonMagicRetour2("compta_liste2.php?ideleve=<?php print $ideleve ?>","_parent","Autre modification")</script></td></tr></table></td></tr>
	</table>
	</form>
<?php
}
?>


<br /><br />
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
   </BODY></HTML>
