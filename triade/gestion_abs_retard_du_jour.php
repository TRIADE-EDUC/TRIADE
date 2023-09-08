<?php
session_start();
include_once("./librairie_php/verifEmailEnregistre.php");
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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
<?php include("./librairie_php/googleanalyse.php"); ?>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();

if (isset($_POST["modif_date"])) {
	$date=$_POST["saisie_date"];
}else{
	$date=dateDMY();
}

if (isset($_POST["sClasseGrp"])) {
	$filtreCLasse=$_POST["sClasseGrp"];
}else{
	$filtreCLasse="tous";
}	

?>
<script language="JavaScript" >
function print_abs_rtd_du_jour(){
	var ok=confirm(langfunc3);
        if (ok) {
		open('gestion_abs_retard_du_jour_print.php?id=<?php print dateFormBase($date) ?>&filtre=<?php print $filtreCLasse?>','_blank','');		
        }
}

function print_abs_rtd_du_jour_2(){
	var ok=confirm(langfunc3);
        if (ok) {
		open('gestion_abs_retard_du_jour_print.php?id=<?php print dateFormBase($date) ?>&filtre=<?php print $filtreCLasse?>&inconnu=1','_blank','');		
        }
}
</script>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGABS35?> <?php print $date ?> </font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<BR>

<!-- // fin  -->
<font class='T2'>&nbsp;&nbsp;<?php print LANGABS37 ?> :</font>
<a href="#" onclick="print_abs_rtd_du_jour();"><img src="./image/commun/print.gif" align=center border=0 alt="Imprimer tous"></A>
<a href="#" onclick="print_abs_rtd_du_jour_2();"><img src="./image/commun/print2.gif" align=center border=0 alt="Imprimer seulement les inconnus"></A>
<ul>
<form method=post name="formulaire" >
<input type=button value="<?php print LANGABS74?>" onclick="open('gestion_abs_retard_du_jour_misaj.php?date=<?php print dateFormBase($date)?>&filtre=<?php print $filtreCLasse?>','_parent','')"  class="bouton2" >
&nbsp;&nbsp;&nbsp;
<input type=text name=saisie_date value="<?php print $date?>"  onclick="this.value=''" size=12 class="bouton2" onKeyPress="onlyChar(event)">
<?php
include_once("librairie_php/calendar.php");
calendar("id1","document.formulaire.saisie_date",$_SESSION["langue"],"0");
?>&nbsp;&nbsp;
<input type='submit' name="modif_date" value="<?php print LANGBT28 ?>"  class=bouton2>
<br><br>
<font class=T2><?php print LANGMESS158 ?> : </font><select name="sClasseGrp" size="1" >
<?php 
if ($filtreCLasse != "tous") {
	$classeS=chercheClasse($filtreCLasse);
	print "<option value='$filtreCLasse' id='select0' >".$classeS[0][1]."</option>";
	print "<option value='tous' id='select0' >Aucun</option>";
}else{
	print "<option id='select0' value='tous' >".LANGCHOIX."</option>";
}
select_classe(); // creation des options ?>
</select>
</form>
<font class="T2">
<?php
	$data=recup_abs_rtd_aucun($date);
	// id,classe,date,heure,matiere,nbabs,nbrtd
	if (count($data) > 0) {
		print "<br><br><b>Abs, rtd effectués.</b> <br><br>";
		print "<div style=\"width:400; height:180; overflow:auto; border:solid 0px black;\" >";
		for($j=0;$j<count($data);$j++) {
			$nbabs=$data[$j][5];
			$nbrtd=$data[$j][6];
			if (empty($nbabs)) {
				print ucwords(LANGABS33)." ". $data[$j][1]." (".trim(trunchaine($data[$j][4],20)).") ".LANGABS76." ".timeForm($data[$j][3])."<br><ul><i>0 absence / 0 retard</i></ul>" ;
			}else{
				print ucwords(LANGABS33)." ". $data[$j][1]." (".trim(trunchaine($data[$j][4],20)).") ".LANGABS76." ".timeForm($data[$j][3])."<br><ul><i>$nbabs absence(s) / $nbrtd retard(s)</i></ul>" ;
			}

		}
		print "<div>";
		print "<br><hr width=50%>";
	}
?>
</font>
</ul>
<?php
// affichage de la liste d'élèves trouvées
$date_du_jour=$date;
?>
<table border="1" bordercolor="#000000" width="100%" style="border-collapse: collapse;" >
<tr>
<TD bgcolor=yellow ><?php print LANGNA1?> <?php print LANGNA2?> / <?php print LANGELE4?> </TD>
<TD bgcolor=yellow width=40% ><?php print LANGABS20?></TD>
<TD bgcolor=yellow width=5% align=center><?php print LANGABS22?></TD>
<TD bgcolor=yellow width=5% align=center><?php print LANGABS38?></TD>
<?php
	$data_2=affRetarddujour3($date);
	//  elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, justifier, heure_saisie, smsenvoye
	// $data : tab bidim - soustab 3 champs
	for($j=0;$j<count($data_2);$j++)
       	{
		$couleur="class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\"";
		$ideleve=$data_2[$j][0];
		$idmatiere=$data_2[$j][7];
		$originesaisie=$data_2[$j][4];
		$etude="";
		if ($idmatiere < 0) { $etude="En Etude "; }

		if ($data_2[$j][11] == '1') { $imgsms="<br><img src='./image/commun/sms.gif' title='SMS ENVOYE' width='20' height='18' align='center'/>"; }else{ $imgsms=""; }

		if ($idmatiere != null) {
			$nomMatiere=chercheMatiereNom($idmatiere);
		}
		if ((strtolower($data_2[$j][6]) != "inconnu") && ($data_2[$j][5] != 0 ) ){
			$couleur="bgcolor='#FFFF99'";
		}
		$classe=chercheIdClasseDunEleve($ideleve);
		if (($filtreCLasse != $classe) && ($filtreCLasse != "tous")) {
			continue;
		}
		$classe=chercheClasse($classe);

		if ($data_2[$j][9] != "") {
			$heuresignale=timeForm($data_2[$j][9]);
		}else{
			$heuresignale="??:??";
		}

		if ($nomMatiere == "") { $nomMatiere=LANGSMS2; }

		$photoeleve="image_trombi.php?idE=".$ideleve;	
		$regime=recupRegime($ideleve);	
		if ($regime != "") $regime=" (<i>$regime</i>)";

?>
	<tr <?php print $couleur ?> >
	<td id='bordure' valign="top"><b><?php 
	$infoProba=getProbaEleve($ideleve);
	if ($infoProba == 1) {
        	$infoprobatoire="<img src='image/commun/important.png' title=\"En p&eacute;riode probatoire !!\" />";
	}else{
        	$infoprobatoire="";
	}
	print "$infoprobatoire &nbsp; <a target='_blank'  href='gestion_abs_retard_modif_donne.php?ideleve=$ideleve' onMouseOver=\"AffBulle('<img src=\'$photoeleve\' >');\"  onMouseOut='HideBulle()'>".strtoupper(recherche_eleve_nom($ideleve))."</a>,"; ?> </a></b> <?php print ucwords(strtolower(trunchaine(recherche_eleve_prenom($ideleve),10)))?><?php print $regime ?><br /> Classe : <?php print $classe[0][1]?> <br> Matière : <?php print trunchaine($nomMatiere,25) ?> </td>	
	<td id='bordure'>En retard à <?php print timeForm($data_2[$j][1]) ?> le  <?php print dateForm($data_2[$j][2]); ?> <br><i> Signalé le <?php print dateForm($data_2[$j][3])." - ".$heuresignale ?></i><br> par <?php print $originesaisie ?> </td>
	<td align=center valign=center id='bordure' >
	<?php $motiftext=$data_2[$j][6]; if ($data_2[$j][6] == "inconnu") { $motiftext=LANGINCONNU; } if (trim($data_2[$j][6]) == "0") { $motiftext=LANGINCONNU; } $motiftext=preg_replace('/"/',"",$motiftext); $motiftext=preg_replace("/'/","\'",$motiftext);?>
	<a href="#" onMouseOver="AffBulle('<font size=2><b><?php print $motiftext ?></B></FONT>');" onMouseOut='HideBulle()'>
	<img src="./image/visu.gif" align=center border=0>
	</A><?php print $imgsms ?>
	</td>
	<td align=center id='bordure'>
	<a href="#" onMouseOver="AffBulle('<font size=2> <?php print LANGABS41?> : <b><?php print cherchetel($ideleve)?></B><BR> <?php print "Portable 1 " ?> : <b><?php print cherchetelportable1($ideleve)?> </b> <br> <?php print "Portable 2 " ?> : <b><?php print cherchetelportable2($ideleve)?> </b><BR> <?php print LANGABS39?> : <b><?php print cherchetelpere($ideleve)?></b><BR> <?php print LANGABS40?> : <b><?php print cherchetelmere($ideleve)?> </b> <br> Email : <b><?php print cherchemail($ideleve)?> </b>  </FONT>');"  onMouseOut='delay(700);HideBulle()' >
	<img src="./image/l_port.gif" align=center border=0>
	</A>
	</td>
	</TR>

<?php
      }
    	print "<tr><td colspan='6' bgcolor='#CCCCCC' heigth='5'>&nbsp;</td></tr>";
	$data_3=affAbsence4($date);
	//  elev_id, date_ab, date_saisie, origin_saisie, duree_ab ,date_fin, motif, duree_heure, id_matiere,heure_saisie,justifier,heuredabsence
	// $data : tab bidim - soustab 3 champs
	for($j=0;$j<count($data_3);$j++) {
		$couleur="class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\"";
		$ideleve=$data_3[$j][0];
		$idmatiere=$data_3[$j][8];
		$heureabs=timeForm($data_3[$j][11]);
		$nomMatiere=chercheMatiereNom($idmatiere);
		$classe=chercheIdClasseDunEleve($data_3[$j][0]);
		$regime=recupRegime($ideleve);	
		if ($regime != "") $regime=" (<i>$regime</i>)";
		if (($filtreCLasse != $classe) && ($filtreCLasse != "tous")) {
			continue;
		}
                $classe=chercheClasse($classe);
                if ((strtolower($data_3[$j][6]) != "inconnu")  && ($data_3[$j][4] != 0 ) ){
                        $couleur="bgcolor='#FFFF99'";
                }
		$photoeleve="image_trombi.php?idE=".$ideleve;
		if ($nomMatiere == "") { $nomMatiere=LANGSMS2; }
		if ($data_3[$j][13] == '1') { $imgsms="<br><img src='./image/commun/sms.gif' title='SMS ENVOYE' width='20' height='18' align='center'/>"; }else{ $imgsms=""; }
?>
	<tr <?php print $couleur ?> >
	<td id='bordure' valign="top"><?php 
	$infoProba=getProbaEleve($ideleve);
        if ($infoProba == 1) {
                $infoprobatoire="<img src='image/commun/important.png' title=\"En p&eacute;riode probatoire !!\" />";
        }else{
                $infoprobatoire="";
        }

        print "$infoprobatoire &nbsp; <a href='#' onMouseOver=\"AffBulle('<img src=\'$photoeleve\' >');\"  onMouseOut='HideBulle()'><b>".strtoupper(recherche_eleve_nom($ideleve))."</a>"; ?></b> <?php print ucwords(strtolower(recherche_eleve_prenom($ideleve)))?><?php print $regime ?> <br> Classe : <?php print $classe[0][1]?> <br> Matière : <?php print trunchaine($nomMatiere,25) ?> </td>
	<td id='bordure'> <?php print LANGABS42 ?> <?php 
		if ($data_3[$j][4] >= 0) {
			print dateForm($data_3[$j][1])?> <br /> A <?php print $heureabs ?> <?php print LANGABS43?> <?php
			if ($data_3[$j][4] == 0) {
				print "???";
			}else {
				print $data_3[$j][4];
				print " ".LANGABS44;
			}
		}else{
			print dateForm($data_3[$j][1])?> <br />  A <?php print $heureabs ?> <?php print LANGABS43?> <?php
			print  preg_replace('/\./','h',$data_3[$j][7]);
		}
		if ($data_3[$j][9] != "") {
			$heuresignale=timeForm($data_3[$j][9]);
		}else{
			$heuresignale="??:??";
		}
	?> 
	<br><i> Signalé le <?php print dateForm($data_3[$j][2])." - ".$heuresignale ?></i> </td>
	<td align=center id='bordure'>
	<?php $motiftext=$data_3[$j][6]; if ($data_3[$j][6] == "inconnu") { $motiftext=LANGINCONNU; } if (trim($data_3[$j][6]) == "0") { $motiftext=LANGINCONNU; } $motiftext=preg_replace('/"/',"",$motiftext); $motiftext=preg_replace("/'/","\'",$motiftext); ?>
	<a href="#" onMouseOver="AffBulle('<font size=2><b><?php print $motiftext?></B></FONT>');" onMouseOut='HideBulle()'>
	<img src="./image/visu.gif" align=center border=0>
	</a>
	<?php print $imgsms ?>
	</td>
	<td align=center id='bordure'>
	<a href="#" onMouseOver="AffBulle('<font size=2> <?php print LANGABS41?> : <b><?php print cherchetel($ideleve)?></B> <BR> <?php print "Portable 1 " ?> : <b><?php print cherchetelportable1($ideleve)?> </b> <br> <?php print "Portable 2 " ?> : <b><?php print cherchetelportable2($ideleve)?> </b> <BR> <?php print LANGABS39?> : <b><?php print cherchetelpere($ideleve)?></b><BR> <?php print LANGABS40?> : <b><?php print cherchetelmere($ideleve)?> </b> <br> Email : <b><?php print cherchemail($ideleve)?> </b>  </FONT>');"  onMouseOut='delay(700);HideBulle()' >
	<img src="./image/l_port.gif" align=center border=0>
	</A>
	</td>
	</TR>
<?php
		}

	$data_4=affDispence3($date);
	//  elev_id, code_mat, date_debut, date_fin, date_saisie, origin_saisie, certificat, motif, heure1, jour1, heure2, jour2, heure3, jour3
	// $data : tab bidim - soustab 3 champs
	for($j=0;$j<count($data_4);$j++) {
	
		$aujourdhui=dateD();
		if ($aujourdhui == "Mon") { $aujourdhui="Lundi";    }
		if ($aujourdhui == "Tue") { $aujourdhui="Mardi";    }
		if ($aujourdhui == "Wed") { $aujourdhui="Mercredi"; }
		if ($aujourdhui == "Thu") { $aujourdhui="Jeudi";    }
		if ($aujourdhui == "Fri") { $aujourdhui="Vendredi"; }
		if ($aujourdhui == "Sat") { $aujourdhui="Samedi";   }
		if ($aujourdhui == "Sun") { $aujourdhui="Dimanche"; }

		if ($aujourdhui == trim($data_4[$j][9])) { $heure_jour="<BR> à ".trim($data_4[$j][8])." (heure)"; }
		if ($aujourdhui == trim($data_4[$j][11])) { $heure_jour="<BR> à ".trim($data_4[$j][10])." (heure)"; }
		if ($aujourdhui == trim($data_4[$j][13])) { $heure_jour="<BR> à ".trim($data_4[$j][12])." (heure)"; }

                $ideleve=$data_4[$j][0];
		$classe=chercheIdClasseDunEleve($ideleve);
		if (($filtreCLasse != $classe) && ($filtreCLasse != "tous")) {
			continue;
		}
                $classe=chercheClasse($classe);
		$photoeleve="image_trombi.php?idE=".$ideleve;
		$k=$data_4[$j][1];
		$sql="SELECT code_mat, libelle FROM ${prefixe}matieres WHERE  code_mat='$k' ORDER BY code_mat";
		$res=execSql($sql);
		$data_matiere=chargeMat($res);
		$regime=recupRegime($ideleve);	
		if ($regime != "") $regime=" (<i>$regime</i>)";
?>
	<TR class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
        <td id='bordure' valign="top"><b><?php 
	$infoProba=getProbaEleve($ideleve);
        if ($infoProba == 1) {
                $infoprobatoire="<img src='image/commun/important.png' title=\"En p&eacute;riode probatoire !!\" />";
        }else{
                $infoprobatoire="";
        }

	print "$infoprobatoire <a href='#' onMouseOver=\"AffBulle('<img src=\'$photoeleve\' >');\"  onMouseOut='HideBulle()'>".strtoupper(recherche_eleve_nom($ideleve))?></a></b> <?php print ucwords(strtolower(recherche_eleve_prenom($ideleve)))?><?php print $regime ?><br> <?php print LANGPER25 ?> : <?php print $classe[0][1]?> </td>
	<td id='bordure'>Dispense de <B><?php print $data_matiere[0][1]?></B><BR> du <?php print dateForm($data_4[$j][2])?> au <?php print dateForm($data_4[$j][3])?>
	<?php print $heure_jour?> 
	</td>
	<td align=center id='bordure'>
<?php 		$motiftext=$data_4[$j][7];  
		if ($data_4[$j][7] == "inconnu") { $motiftext=LANGINCONNU; } 
		if (trim($data_4[$j][7]) == "0") { $motiftext=LANGINCONNU; } 
		$motiftext=preg_replace('/"/',"",$motiftext); 
		$motiftext=preg_replace("/'/","\'",$motiftext);
	?>
	<a href="#" onMouseOver="AffBulle('<font size=2><b><?php print $motiftext?></B></FONT>');" onMouseOut='HideBulle()'>
	<img src="./image/visu.gif" align=center border=0>
	</a>
	</td>
	<td  align=center id='bordure'>
	<a href="#" onMouseOver="AffBulle('<font size=2> <?php print LANGABS41?> : <b><?php print cherchetel($ideleve)?></B> <BR> <?php print "Portable 1 " ?> : <b><?php print cherchetelportable1($ideleve)?> </b> <br> <?php print "Portable 2 " ?> : <b><?php print cherchetelportable2($ideleve)?> </b> <BR> <?php print LANGABS39?> : <b><?php print cherchetelpere($ideleve)?></b><BR> <?php print LANGABS40?> : <b><?php print cherchetelmere($ideleve)?> </b> <br> Email : <b><?php print cherchemail($ideleve)?> </b>  </FONT>');"  onMouseOut='delay(700);HideBulle()' >
	<img src="./image/l_port.gif" align=center border=0>
	</A>
	</td>
	</TR>

<?php
		}
print "</table>";
?>
<BR><BR>
     <!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
