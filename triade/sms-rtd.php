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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();

// affichage de la liste d'élèves trouvées
if (isset($_POST["joursms"])) {

	$datesms=dateDMY();
	$datesms=datemoinsn($datesms,$_POST["joursms"]);

}else{
	$datesms=dateDMY2();
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGSMS1 ?> <?php print dateForm($datesms)?> </font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign="top" >
<!-- // fin  -->
<br>
<form method=post name="formulaire" >
<font class=T2><?php print LANGEDIT19 ?>  </font>
<select name="joursms" onchange="document.forms.formulaire.submit()">
<option value="0" id='select0'><?php print LANGCHOIX ?></option>
<option value="0" id='select1'><?php print LANGEDIT14 ?></option>
<option value="1" id='select1'><?php print LANGEDIT15 ?></option>
<option value="2" id='select1'><?php print LANGEDIT16 ?></option>
<option value="3" id='select1'><?php print LANGEDIT17 ?></option>
<option value="4" id='select1'><?php print LANGEDIT18 ?></option>
<option value="5" id='select1'>Depuis 5 jours</option>
<option value="6" id='select1'>Depuis 6 jours</option>
<option value="7" id='select1'>Depuis 7 jours</option>
<option value="8" id='select1'>Depuis 8 jours</option>
<option value="9" id='select1'>Depuis 9 jours</option>
<option value="10" id='select1'>Depuis 10 jours</option>
<option value="11" id='select1'>Depuis 11 jours</option>
<option value="12" id='select1'>Depuis 12 jours</option>
<option value="13" id='select1'>Depuis 13 jours</option>
<option value="14" id='select1'>Depuis 14 jours</option>
</select>
</form>
</ul>
<form method=post action="sms-envoi.php" name=formulaire2 onSubmit="document.formulaire2.create.disabled=true" >
<input type='hidden' value="en retard" name="type" />
<table border="1" bordercolor="#000000" width="100%">
<tr>
<TD bgcolor=yellow ><?php print LANGNA1?> <?php print LANGNA2?> / <?php print LANGABS22?></TD>
<TD bgcolor=yellow width=5% align=center><?php print "SMS" ?></TD>
<?php
	$nb=0;
	$data_2=affRetardNonJustifieSms($datesms);
	//  elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere  , smsenvoye, heure_saisie
	// $data : tab bidim - soustab 3 champs
	for($j=0;$j<count($data_2);$j++) {
		$couleur="class=\"tabnormal\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\"";
		$ideleve=$data_2[$j][0];
		$idmatiere=$data_2[$j][7];
		$smsenvoye=$data_2[$j][8];
		$date_ret=$data_2[$j][2];
		$time=$data_2[$j][9];
		$etude="";
		if ($idmatiere < 0) { $etude=" / Etude "; }

		if ($idmatiere != null) {
			$nomMatiere=chercheMatiereNom($idmatiere);
		}
		if (($data_2[$j][6] != "inconnu") && ($data_2[$j][5] != 0 ) ){
			$couleur="bgcolor='#FFFF99'";
		}
		$classe=chercheIdClasseDunEleve($ideleve);
		$classe=chercheClasse($classe);

		if ($smsenvoye == '1') {
			$smsenvoye="<br><b><i><font color='colort1'>SMS envoyé !</font></i></b>";
		}else{
			$smsenvoye="";
		}

		if ($nomMatiere == "") { $nomMatiere=LANGSMS2; }
?>
	<TR <?php print $couleur ?> >
	<td id='bordure' valign="top" ><img src="image_trombi.php?idE=<?php print $ideleve ?>"  align='left' border=0 ><b><?php print strtoupper(recherche_eleve_nom($ideleve))?></b> <?php print ucwords(strtolower(trunchaine(recherche_eleve_prenom($ideleve),10)))?> <br><br>
	En retard <?php print $etude ?> (<?php print trunchaine($nomMatiere,50) ?>) <br><br>
	<?php $motiftext=$data_2[$j][6]; if (($data_2[$j][6] == "inconnu") || ($data_2[$j][6] == "0")) { $motiftext=LANGINCONNU; } $motiftext=preg_replace('/"/',"",$motiftext); $motiftext=preg_replace("/'/","\'",$motiftext);?>
	Motif : <?php print $motiftext ?>
	</td>
	<td align=left id='bordure'  valign="top" >
<?php 

		$filtreSMS=config_param_visu('smsfiltre');
		$filtreSMS=$filtreSMS[0][0];

		$telok=0;
		$telsms=trim(cherchetel($ideleve));
		if (preg_match("/^$filtreSMS/",$telsms)) {
			$telsms=preg_replace('/ /',"",$telsms);
			$telsms=preg_replace('/\./',"",$telsms);
			$telsms=preg_replace('/\//',"",$telsms);
			$telsms=preg_replace('/-/',"",$telsms);
			$telsms=preg_replace('/_/',"",$telsms);
			if (is_numeric($telsms)) { 
				$nb++; 
				print "<u>Principal:</u><br>".$telsms."&nbsp;<input type=checkbox name='sms[]' value='$ideleve#$telsms#$date_ret#$time' ><br>";
				$telok=1;  
			}
		}

		$telsms=trim(cherchetelpere($ideleve));
		if (preg_match("/^$filtreSMS/",$telsms)) {
			$telsms=preg_replace('/ /',"",$telsms);
			$telsms=preg_replace('/\./',"",$telsms);
			$telsms=preg_replace('/\//',"",$telsms);
			$telsms=preg_replace('/-/',"",$telsms);
			$telsms=preg_replace('/_/',"",$telsms);
			if (is_numeric($telsms)) { 
				$nb++; 
				print "<u>Tel Prof. Père:</u><br>".$telsms."&nbsp;<input type=checkbox name='sms[]' value='$ideleve#$telsms#$date_ret#$time' ><br>";
				$telok=1;  
			}
		}

		
		$telsms=trim(cherchetelmere($ideleve));
		if (preg_match("/^$filtreSMS/",$telsms)) {
			$telsms=preg_replace('/ /',"",$telsms);
			$telsms=preg_replace('/\./',"",$telsms);
			$telsms=preg_replace('/\//',"",$telsms);
			$telsms=preg_replace('/-/',"",$telsms);
			$telsms=preg_replace('/_/',"",$telsms);
			if (is_numeric($telsms)) { 
				$nb++; 
				print "<u>Tel Prof. Mère:</u><br>".$telsms."&nbsp;<input type=checkbox name='sms[]' value='$ideleve#$telsms#$date_ret#$time' ><br>";
				$telok=1;  
			}
		}

		$telsms=trim(cherchetelportable1($ideleve));
		if (preg_match("/^$filtreSMS/",$telsms)) {
			$telsms=preg_replace('/ /',"",$telsms);
			$telsms=preg_replace('/\./',"",$telsms);
			$telsms=preg_replace('/\//',"",$telsms);
			$telsms=preg_replace('/-/',"",$telsms);
			$telsms=preg_replace('/_/',"",$telsms);
			if (is_numeric($telsms)) { 
				$nb++; 
				print "<u>Portable 1:</u><br>".$telsms."&nbsp;<input type=checkbox name='sms[]' value='$ideleve#$telsms#$date_ret#$time' ><br>";
				$telok=1;  
			}
		}



		$telsms=trim(cherchetelportable2($ideleve));
		if (preg_match("/^$filtreSMS/",$telsms)) {
			$telsms=preg_replace('/ /',"",$telsms);
			$telsms=preg_replace('/\./',"",$telsms);
			$telsms=preg_replace('/\//',"",$telsms);
			$telsms=preg_replace('/-/',"",$telsms);
			$telsms=preg_replace('/_/',"",$telsms);
			if (is_numeric($telsms)) { 
				$nb++; 
				print "<u>Portable 2:</u><br>".$telsms."&nbsp;<input type=checkbox name='sms[]' value='$ideleve#$telsms#$date_ret#$time' ><br>";
				$telok=1;  
			}
		}

		$telsms=trim(cherchetelEleve($ideleve));
		if (preg_match("/^$filtreSMS/",$telsms)) {
			$telsms=preg_replace('/ /',"",$telsms);
			$telsms=preg_replace('/\./',"",$telsms);
			$telsms=preg_replace('/\//',"",$telsms);
			$telsms=preg_replace('/-/',"",$telsms);
			$telsms=preg_replace('/_/',"",$telsms);
			if (is_numeric($telsms)) { 
				$nb++; 
				print "<u>Port Elève:</u><br>".$telsms."&nbsp;<input type=checkbox name='sms[]' value='$ideleve#$telsms#$date_ret#$time' ><br>";
				$telok=1; 
			}
		}

		print "<center>$smsenvoye</center>";	
		if ($telok==0) { print "<i>Aucun numéro</i>"; }
 	?>
	</td>
	</TR>
<?php
}
print "</table><br /><br />";
print "<input type=hidden name='nb' value='$nb' >";
print "<table align=center border='0'><tr><td><script language=JavaScript>buttonMagicSubmit(\"ENVOI SMS\",\"create\"); </script></td></tr></table>";
	
	
	print "</form>";
?>
<BR><BR>
     <!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if ($_SESSION["membre"] == "menuadmin") :
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
     ?>
   <?php
// deconnexion en fin de fichier
Pgclose();
?>
<SCRIPT language="JavaScript">InitBulle("#FFFFFF","#009999","#FFFFFF",1);</SCRIPT>
</BODY></HTML>
