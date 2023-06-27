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
<?php 
include_once("./librairie_php/lib_licence.php");
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Visa du Professeur Principal." ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<?php
include_once("./librairie_php/lib_conexpersistant.php"); 
connexpersistance("color:black;font-weight:bold;font-size:11px;text-align: center;"); 
$anneeScolaire=$_COOKIE["anneeScolaire"];
$cnx=cnx();
$idclasse=$_POST["saisie_classe"];
verif_profp_class($_SESSION["id_pers"],$idclasse);

$num=$_POST["num"];
$type_rubrique=$_POST["type_rubrique"];
$tri=$_POST["saisie_trimestre"];
$saisie_classe=$_POST["saisie_classe"];
$idprofp=$_SESSION["id_pers"];

if (isset($_POST["createcom"])) {
	$EPI=$_POST["EPI"];
	$commentaire=$_POST["comm_epi"];	
	$thematique=$_POST["thematique"];
	$nbeleve=$_POST["nb"];
	for($j=0;$j<$nbeleve;$j++) {
		$ideleve=$_POST["eleveid_$j"];
		$commentaire=$_POST["commentaire_$j"];
		$cr=create_com_livret_EPI_AP($_SESSION["id_pers"],$thematique,$commentaire,$EPI,$tri,$saisie_classe,$anneeScolaire,$type_rubrique,$num,$ideleve);
	}
	if ($cr) $reponse="<font id='color3' class='T2'>".LANGDONENR."</font>";
}

if (($tri == "cycle1") || ($tri == "cycle2") || ($tri == "cycle3") || ($tri == "cycle4")) {

	print "<form method=post name='formulaire' action='profpcomScolaireBulletinCycle.php' >";
	$sql="SELECT s.* FROM ( SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes  WHERE classe='$idclasse' AND code_class=classe AND annee_scolaire='$anneeScolaire' AND compte_inactif != 1 UNION ALL SELECT c.libelle, e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire') s  ORDER BY 3";
	$res=execSql($sql);
	$data=chargeMat($res);

	$saisie_classe=$_POST["saisie_classe"];
	$cl=chercheClasse_nom($saisie_classe);

	if( count($data) > 0 ) {
		print "<br>";
		print "<font class=T2>&nbsp;&nbsp;&nbsp;Classe : <b>$cl</b> / ".LANGBULL3." : <b>$anneeScolaire</b> </font><br><br>";
		print "<table width='100%' border='1' style='border-collapse: collapse;' >";
		print "<tr><td></td><td bgcolor='#E1EEF9' >Maîtrise insuffisante</td>";
		print "<td bgcolor='#C7E1F5' >Maîtrise fragile</td>"; 
		print "<td bgcolor='#ACD4F1' >Maîtrise satisfaisante</td>"; 
		print "<td bgcolor='#91C9ED' >Très bonne maîtrise</td>";	
		print "</tr>";
		for($i=0;$i<count($data);$i++) {
			$ideleve=$data[$i][1];
				
			$dataCycle=recupInfoCyclePropP($ideleve,$tri); //ideleve,cycle,q1,q2,q3,q4,q5,q6,q7,commentaire,idprofp,q4bis
			$q11=($dataCycle[0][2] == "mi") ? "checked='checked'" : "" ;
			$q12=($dataCycle[0][2] == "mf") ? "checked='checked'" : "" ;
			$q13=($dataCycle[0][2] == "ms") ? "checked='checked'" : "" ;
			$q14=($dataCycle[0][2] == "tbm") ? "checked='checked'" : "" ;

			$q21=($dataCycle[0][3] == "mi") ? "checked='checked'" : "" ;
			$q22=($dataCycle[0][3] == "mf") ? "checked='checked'" : "" ;
			$q23=($dataCycle[0][3] == "ms") ? "checked='checked'" : "" ;
			$q24=($dataCycle[0][3] == "tbm") ? "checked='checked'" : "" ;
			
			$q31=($dataCycle[0][4] == "mi") ? "checked='checked'" : "" ;
			$q32=($dataCycle[0][4] == "mf") ? "checked='checked'" : "" ;
			$q33=($dataCycle[0][4] == "ms") ? "checked='checked'" : "" ;
			$q34=($dataCycle[0][4] == "tbm") ? "checked='checked'" : "" ;

			$q41=($dataCycle[0][5] == "mi") ? "checked='checked'" : "" ;
			$q42=($dataCycle[0][5] == "mf") ? "checked='checked'" : "" ;
			$q43=($dataCycle[0][5] == "ms") ? "checked='checked'" : "" ;
			$q44=($dataCycle[0][5] == "tbm") ? "checked='checked'" : "" ;

			$q41bis=($dataCycle[0][11] == "mi") ? "checked='checked'" : "" ;
			$q42bis=($dataCycle[0][11] == "mf") ? "checked='checked'" : "" ;
			$q43bis=($dataCycle[0][11] == "ms") ? "checked='checked'" : "" ;
			$q44bis=($dataCycle[0][11] == "tbm") ? "checked='checked'" : "" ;

			$q51=($dataCycle[0][6] == "mi") ? "checked='checked'" : "" ;
			$q52=($dataCycle[0][6] == "mf") ? "checked='checked'" : "" ;
			$q53=($dataCycle[0][6] == "ms") ? "checked='checked'" : "" ;
			$q54=($dataCycle[0][6] == "tbm") ? "checked='checked'" : "" ;

			$q61=($dataCycle[0][7] == "mi") ? "checked='checked'" : "" ;
			$q62=($dataCycle[0][7] == "mf") ? "checked='checked'" : "" ;
			$q63=($dataCycle[0][7] == "ms") ? "checked='checked'" : "" ;
			$q64=($dataCycle[0][7] == "tbm") ? "checked='checked'" : "" ;

			$q71=($dataCycle[0][8] == "mi") ? "checked='checked'" : "" ;
			$q72=($dataCycle[0][8] == "mf") ? "checked='checked'" : "" ;
			$q73=($dataCycle[0][8] == "ms") ? "checked='checked'" : "" ;
			$q74=($dataCycle[0][8] == "tbm") ? "checked='checked'" : "" ;
		
			$commentaire=$dataCycle[0][9];
			$commentaire=preg_replace('/\s{2,}/',' ',$commentaire);

			print "<tr><td colspan='5' >";
			print " <b> ".ucfirst($data[$i][3])." ".strtoupper($data[$i][2])."</b>";
			print "</td></tr>";

			print "<tr><td bgcolor='#EEF5FC' >Comprendre, sexprimer en utilisant la langue française à loral et à lécrit</td>";
			print "<td bgcolor='#EEF5FC' ><input type='radio' name='q1_$ideleve' value='mi' $q11  </td>";
			print "<td bgcolor='#D4E7F7' ><input type='radio' name='q1_$ideleve' value='mf' $q12 </td>";
			print "<td bgcolor='#BADAF3' ><input type='radio' name='q1_$ideleve' value='ms' $q13 </td>";
			print "<td bgcolor='#9FCEEF' ><input type='radio' name='q1_$ideleve' value='tbm' $q14 </td>";
			print "</tr>";

			print "<tr><td bgcolor='#E1EEF9'  >Comprendre, sexprimer en utilisant une langue étrangère et, le cas échéant, une langue régionale</td>";
			print "<td bgcolor='#E1EEF9'  ><input type='radio' name='q2_$ideleve' value='mi' $q21 </td>";
			print "<td bgcolor='#C7E1F5' ><input type='radio' name='q2_$ideleve' value='mf' $q22 </td>";
			print "<td bgcolor='#ACD4F1' ><input type='radio' name='q2_$ideleve' value='ms' $q23 </td>";
			print "<td  bgcolor='#91C9ED' ><input type='radio' name='q2_$ideleve' value='tbm' $q24 </td>";
			print "</tr>";

			print "<tr><td  bgcolor='#EEF5FC' >Comprendre, sexprimer en utilisant les langages mathématiques, scientifiques et informatiques</td>";
			print "<td bgcolor='#EEF5FC'  ><input type='radio' name='q3_$ideleve' $q31 value='mi' </td>";
			print "<td bgcolor='#D4E7F7' ><input type='radio' name='q3_$ideleve'  $q32  value='mf' </td>";
			print "<td bgcolor='#BADAF3' ><input type='radio' name='q3_$ideleve'  $q33  value='ms' </td>";
			print "<td bgcolor='#9FCEEF' ><input type='radio' name='q3_$ideleve'  $q34 value='tbm' </td>";
			print "</tr>";

			print "<tr><td bgcolor='#E1EEF9' >Comprendre, sexprimer en utilisant les langages des arts et du corps </td>";
			print "<td bgcolor='#E1EEF9' ><input type='radio' name='q4_$ideleve'  $q41 value='mi' </td>";
			print "<td bgcolor='#C7E1F5' ><input type='radio' name='q4_$ideleve'  $q42 value='mf' </td>";
			print "<td bgcolor='#ACD4F1' ><input type='radio' name='q4_$ideleve'  $q43 value='ms' </td>";
			print "<td bgcolor='#91C9ED' ><input type='radio' name='q4_$ideleve'  $q44 value='tbm' </td>";
			print "</tr>";

			print "<tr><td bgcolor='#E1EEF9' >Les méthodes et outils pour apprendre </td>";
			print "<td bgcolor='#E1EEF9' ><input type='radio' name='q4bis_$ideleve'  $q41bis value='mi' </td>";
			print "<td bgcolor='#C7E1F5' ><input type='radio' name='q4bis_$ideleve'  $q42bis value='mf' </td>";
			print "<td bgcolor='#ACD4F1' ><input type='radio' name='q4bis_$ideleve'  $q43bis value='ms' </td>";
			print "<td bgcolor='#91C9ED' ><input type='radio' name='q4bis_$ideleve'  $q44bis value='tbm' </td>";
			print "</tr>";

			print "<tr><td  bgcolor='#EEF5FC' >La formation de la personne et du citoyen</td>";
			print "<td bgcolor='#EEF5FC' ><input type='radio' name='q5_$ideleve'  $q51 value='mi' </td>";
			print "<td bgcolor='#D4E7F7' ><input type='radio' name='q5_$ideleve'  $q52 value='mf' </td>";
			print "<td bgcolor='#BADAF3' ><input type='radio' name='q5_$ideleve'  $q53 value='ms' </td>";
			print "<td bgcolor='#9FCEEF' ><input type='radio' name='q5_$ideleve'  $q54 value='tbm' </td>";
			print "</tr>";

			print "<tr><td bgcolor='#E1EEF9' >Les systèmes naturels et les systèmes techniques</td>";
		        print "<td bgcolor='#E1EEF9' ><input type='radio' name='q6_$ideleve'  $q61 value='mi' </td>";
			print "<td bgcolor='#C7E1F5' ><input type='radio' name='q6_$ideleve'  $q62 value='mf' </td>";
			print "<td bgcolor='#ACD4F1' ><input type='radio' name='q6_$ideleve'  $q63 value='ms' </td>";
			print "<td bgcolor='#91C9ED' ><input type='radio' name='q6_$ideleve'  $q64 value='tbm' </td>";
			print "</tr>";

			print "<tr><td  bgcolor='#EEF5FC' >Les représentations du monde et lactivité humaine</td>";
			print "<td bgcolor='#EEF5FC' ><input type='radio' name='q7_$ideleve'  $q71 value='mi' </td>";
			print "<td bgcolor='#D4E7F7' ><input type='radio' name='q7_$ideleve'  $q72 value='mf' </td>";
			print "<td bgcolor='#BADAF3' ><input type='radio' name='q7_$ideleve'  $q73 value='ms' </td>";
			print "<td bgcolor='#9FCEEF' ><input type='radio' name='q7_$ideleve'  $q74 value='tbm' </td>";
			print "</tr>";

			print "<tr><td  bgcolor='#EEF5FC' >Synthèse des acquis scolaires de lélève</td>";
			print "<td bgcolor='#EEF5FC' colspan='4' ><textarea name='commentaire_$ideleve' rows='3' cols='80' >$commentaire</textarea></td>";
			print "</tr>";


		}

		print "</table>";
		print "<br><br>";
		print "<input type='hidden' name='cycle' value='$tri' />";
		print "<input type='hidden' name='idclasse' value='$saisie_classe' />";
		print "<input type='hidden' name='anneeScolaire' value='$anneeScolaire' />";
		print "<center><table><tr><td><script language=JavaScript>buttonMagicSubmit('Enregistrer','valid');</script></td></tr></table></center>";
	
	}
	print "</form>";
}else{

	$data=recupComLivretEPIAP($type_rubrique,$anneeScolaire,$idclasse,$_SESSION["id_pers"],$tri,$num);
	// intitule,thematique,idprof,commentaire,type_rubrique,annee_scolaire,trim,idclasse
	$ENI=$data[0][0];
	$thematique=$data[0][1];
?>

	<form method=post name="formulaire" action="profpcomScolaireBulletin.php" >
	<?php
	$saisie_classe=$_POST["saisie_classe"];
	// nom classe
	$cl=$data[0][0];
	$tri=$_POST["saisie_trimestre"];
	$triMessage=" le $tri ";
	if ($tri == "exam_juin") { $triMessage="l'examen de Juin" ; }
	if ($tri == "exam_dec") { $triMessage="l'examen de Décembre" ; }
	if ($tri == "periode1") { $triMessage="1er période" ; }
	if ($tri == "periode2") { $triMessage="2ieme période" ; }
	if ($tri == "periode3") { $triMessage="3ieme période" ; }
	if ($tri == "periode4") { $triMessage="4ieme période" ; }
	if ($tri == "periode5") { $triMessage="5ieme période" ; }
	if ($tri == "periode6") { $triMessage="6ieme période" ; }
	if ($tri == "periode7") { $triMessage="7ieme période" ; }
	if ($tri == "periode8") { $triMessage="8ieme période" ; }
	if ($tri == "periode9") { $triMessage="9ieme période" ; }
			

	if ($type_rubrique == "EPI" ) $libelle_rubrique="Enseignements pratiques interdisciplinaires " ;
	if ($type_rubrique == "AP" ) $libelle_rubrique="Accompagnement personnalisé " ;
	
	
	if (defined("NBCARBULLPROFP")) { $nbcar=NBCARBULLPROFP;  }else{ $nbcar="500"; }
	
	$sql="SELECT s.* FROM ( SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes  WHERE classe='$idclasse' AND code_class=classe AND annee_scolaire='$anneeScolaire' AND compte_inactif != 1 UNION ALL SELECT c.libelle, e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire') s  ORDER BY 3";
	$res=execSql($sql);
	$data=chargeMat($res);
	if( count($data) > 0 ) {
		print "<br><table width='100%' border='0' ><tr><td><font class=T2>&nbsp;&nbsp;&nbsp;Classe : <b>$cl</b> / ".LANGBULL3." : <b>$anneeScolaire</b> </font></td><td>";
		print "<script language=JavaScript>buttonMagic(\"Moyennes, Graphs, ...\",\"profpprojo.php?idClasse=$saisie_classe\",'video','width=800,height=600,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes','');</script></td></tr></table>";
		print "<br />";
		print "<br /><font class=T2>&nbsp;&nbsp;&nbsp;Commentaire pour $libelle_rubrique  <br /><br />";
		
		print "<table border='0' >";
		if ($type_rubrique == "EPI" ) {
			print "<td valign='top' align='right' >";
			print "Intitulé de l'EPI : ";
			print "</td>";
			print "<td>";
			print "<input type='text' name='EPI' maxlenght='90' size='30' value=\"$ENI\" />";
			print "</td>";
			print "</tr><tr>";
			print "<td align='right' >";
			print "Thématique interdisciplinaire";
			print "<td>";
		        print "<input type='text' name='thematique'  maxlenght='90' size='30' value=\"$thematique\" />";
		        print "</td>";
		}
	
	
		if ($type_rubrique == "AP" ) {
		        print "<tr>";
		        print "<td valign='top' align='right' >";
		        print "Intitulé de l'action : ";
	       		print "</td>";
		        print "<td>";
		        print "<input type='text' name='EPI' maxlenght='90' size='30' value=\"$ENI\" />";
			print "</td>";
		}
		print "</table>";
		print "<br>";
		print "<table align=center width=100% border='0' >";
	
		for($i=0;$i<count($data);$i++) {
			$ideleve=$data[$i][1];
		        $photoeleve="image_trombi.php?idE=".$ideleve;
			$commentaire=recupCommentaireLivretEPIAPIdeleve($type_rubrique,$anneeScolaire,$idclasse,$_SESSION["id_pers"],$tri,$num,$ideleve);
		        print "<tr>";
		        print "<td valign='top' width='5' ><img src='$photoeleve' $taille align='left'></td>";
		        print "<td valign='top' align='left' >";
		        print "<input type='hidden' value='$ideleve' name='eleveid_$i' />";
		        print " <b> ".ucfirst($data[$i][3])." ".strtoupper($data[$i][2])."</b>";
			print "<br>";
			print "<textarea cols=60 rows=5 name='commentaire_$i' onkeypress=\"compter(this,'$nbcar', this.form.CharRestant_$i)\" >$commentaire</textarea>";
			$nbtexte=strlen($commentaire);
			print "&nbsp;<input type=text name='CharRestant_$i' size=3 disabled='disabled' value='$nbtexte' />";
			print "</td></tr>";
		}
		$nbeleve=count($data);
		
		$valider=VALIDER;
		print "<tr><td colspan=2 align='center'><br><br><table><tr><td><script language=JavaScript>buttonMagicSubmit('$valider','createcom');</script></td>";
		if (isset($_SESSION["profpclasse"])) { print "<td><script>buttonMagicRetour('profpcombulletin.php?sClasseGrp=$idclasse','_self')</script></td></tr></table>"; }
	}else{
		if (isset($_SESSION["profpclasse"])) { print "<table><tr><td><script>buttonMagicRetour('profpcombulletin.php?sClasseGrp=$idclasse','_self')</script></td></tr></table>"; }
	
	}
	print "</td></tr>";
	print '<input type=hidden name="saisie_trimestre" value="'.$tri.'" />';
	print "<input type=hidden name='saisie_classe' value=\"".$_POST["saisie_classe"]."\" />";
	print "<input type=hidden name='type_rubrique' value='".$_POST["type_rubrique"]."' />";
	print "<input type=hidden name='num' value='".$_POST["num"]."' />";
	print "<input type=hidden name='nb' value='$nbeleve' />";
	print "</form>";	


	print "</td></tr>";
	print "</table>";
	print "<br><br>";
	print "<center>$reponse</center>";
}
	brmozilla($_SESSION["navigateur"]); 
	brmozilla($_SESSION["navigateur"]); 

?>
<!-- // fin form -->
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
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
