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
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_compta.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Consultation des versements" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >

<?php
$unite=unitemonnaie();
$ideleve=$_SESSION["id_pers"];

if ($ideleve > 0) {
	$nomeleve=recherche_eleve_nom($ideleve);
	$prenomeleve=recherche_eleve_prenom($ideleve);
	$idclasse=chercheClasseEleve($ideleve);
	$classe=chercheClasse_nom($idclasse);
?>
	
	<input type="hidden" name="ideleve" value="<?php print $ideleve ?>" />
	<table>
	<tr><td valign='top' ><img src="image_trombi.php?idE=<?php print $ideleve ?>" border=0 ></td>
	<td valign="top">
	&nbsp;&nbsp;<font class=T2>Nom : <?php print $nomeleve ?></font>
	<br><br>
	&nbsp;&nbsp;<font class=T2>Prénom : <?php print $prenomeleve ?></font>
	<br><br>
	&nbsp;&nbsp;<font class=T2>Classe : <?php print ucwords($classe) ?></font>
	</td></tr>
	<?php
	$nbmoisindemnite=nbmoisindemnite($ideleve);
	?>
	<tr><td colspan='2'>&nbsp;&nbsp;<font class=T2>Boursier : <?php print etatBoursier($ideleve) ?> (<?php print montantBourse($ideleve) ?>) </font></td></tr>
	<tr><td colspan='2'>&nbsp;&nbsp;<font class=T2>Indemnité de stage : <?php print montantIndemniteStage($ideleve) ?> <i>(<?php print $nbmoisindemnite ?> mois)</i> </font></td></tr>
	</table>


	<?php
	$filtre="";
	if (isset($_POST["anneescolairefiltre"])) {
		$filtre=$_POST["anneescolairefiltre"];
	}else{
		$filtre=anneeScolaire();
	}
	?>
	<br>
	<form method=post action="compta_consulte3.php" >
	&nbsp;&nbsp;<font class=T2>Filtre : </font><select onChange='this.form.submit()' name='anneescolairefiltre' > <?php filtreAnneeScolaireSelect($filtre) ?> </select><br><br>
	</form>
	
	<table width=100% border='1' bordercolor='#000000' >
	<tr>
	<td bgcolor='yellow' align='center' width=5% >&nbsp;Date&nbsp;d'appel&nbsp;</td>
	<td bgcolor='yellow' align='center'  >&nbsp;Versement&nbsp;</td>
	<td bgcolor='yellow' align='center' width=15% >&nbsp;Montant&nbsp;</td>
	<td bgcolor='yellow' align='center'>&nbsp;Détail&nbsp;</td>
	</tr>
<?php
	$dataV=recupConfigVersement($idclasse,$filtre); //id,idclasse,libellevers,montantvers,datevers
	if ($dataV == "") { $dataV=array(); }
	$dataVE=recupConfigVersementEleve($ideleve,$filtre);
	if ($dataVE == "") { $dataVE=array(); }
	$dataV=array_merge($dataV,$dataVE);
	for($j=0;$j<count($dataV);$j++) {
		$id=$dataV[$j][0];

		if(verifcomptaExclu($id,$ideleve)) { 
			$s="<s>";$ss="</s>";$exclu=true;
		}else{
			$s="";$ss="";$exclu=false;
		}

		$data=recupInfoVersement($ideleve,$id); // ideleve,idversement,montantvers,datevers,modepaiement
		$dateVersement=$data[0][3];
		$idvers=$data[0][1];
		if ($dateVersement != "") { $dateVersement=dateForm($dateVersement); }
		$montantVers=number_format($data[0][2],2,'.','');
		$modepaiement=nl2br($data[0][4]);
		$dateVersOr=$dataV[$j][4];
		$montantavers=$dataV[$j][3];
	
		print "<tr class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\">";
		print "<td valign='top'>&nbsp;$s".dateForm($dataV[$j][4])."$ss&nbsp;</td>";
		print "<td valign='top'>&nbsp;$s".$dataV[$j][2]."$ss</td>";
		print "<td valign='top' align='right' >&nbsp;<b>$s".preg_replace('/ /','&nbsp;',affichageFormatMonnaie($dataV[$j][3]))."$ss</b></td>";
		print "<td valign='top'>";

		if (!$exclu) {	
			print "&nbsp;&nbsp;<a href='#' onmouseover=\"AffBulle3('Informations / Détails','./image/commun/info.jpg','$listeHoraire'); searchVersement('$ideleve','$id','$dateVersOr','$montantavers');\" onmouseout=\"HideBulle();\" ><img src='image/commun/show.png' align='center' border='0' ></a>";
		}else{
			print "&nbsp;&nbsp;<a href='#' onmouseover=\"AffBulle3('Informations / Détails','./image/commun/info.jpg','<b>Versement exonéré</b>');\" onmouseout=\"HideBulle();\" ><img src='image/commun/show.png' align='center' border='0' ></a>";
		}

		$dateduJour=date("Ymd");
		$dateVersOr=preg_replace('/-/',"",$dateVersOr);
		$dateVersement=dateFormBase($dateVersement);
		$dateVersement=preg_replace('/-/',"",$dateVersement);

		if ($exclu) {
			print "";
		}else{
			if (($montantVers == "0.00") && ($dateduJour > $dateVersOr)) {
				print "&nbsp;&nbsp;<img src='image/commun/important.png' align='center' border='0' alt='Retard paiement' >";
			}

			if (($montantVers < $dataV[$j][3] ) && ($dateduJour > $dateVersOr)  && ($montantVers != "0.00") ) {
				$montantregle+=$montantVers;
				print "&nbsp;&nbsp;<img src='image/commun/warning.gif' align='center' border='0' alt='Paiement incomplet' >";
			}

			if (($montantVers >= $dataV[$j][3] ) && ($dateduJour >= $dateVersement )) {
				$montantregle+=$montantVers;
				print "&nbsp;&nbsp;<img src='image/commun/valid.gif' align='center' border='0' alt='Paiement effectué' >";
			}

			if  ($dateduJour < $dateVersement) {
				$nbj=$dateVersement-$dateduJour;
				print "&nbsp;&nbsp;<img src='image/commun/antenne.png' align='center' border='0' alt='Encaissement dans $nbj jour(s) ' >";
			}
		}

		print "</td>";
		print "</tr>";

		if (!$exclu) {
			$montantScol+=$dataV[$j][3];
		}

	}
	$montantScol=affichageFormatMonnaie($montantScol);
	$montantregle=affichageFormatMonnaie($montantregle);
?>
	</table>
<br>&nbsp;&nbsp;
<img src='image/commun/valid.gif' align='center' border='0' alt='Paiement effectué' > Paiement effectué / 
<img src='image/commun/warning.gif' align='center' border='0' alt='Paiement incomplet' > Paiement incomplet /
<img src='image/commun/important.png' align='center' border='0' alt='Retard paiement' > Retard paiement
<br><br>
<font class=T2>&nbsp;&nbsp;Montant scolarité : <?php print $montantScol ?> <?php print $unite ?> </font> <br><br>
<font class=T2>&nbsp;&nbsp;Montant réglé : <?php print $montantregle ?> <?php print $unite ?> à ce jour.</font> <br><br>
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
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
