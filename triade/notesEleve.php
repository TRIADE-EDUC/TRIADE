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
include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php"); // futur : auto_prepend_file
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/timezone.php");
include_once("librairie_php/recupnoteperiode.php");
$cnx=cnx();
// variables de session
$myS=$_SESSION;
$Snom=$myS['nom'];
$Sprenom=$myS['prenom'];
$Seid=$myS['id_pers'];
$Scid=$myS['idClasse'];
unset($myS);
// la date
if(!$date=$_GET["m"]){
	$date=dateM();
        $annee=dateY();
}

if (!empty($_GET['annee'])) {
	$annee=$_GET['annee'];
}

$prevannee=$annee;
$nextannee=$annee;

if ($date == 1)  $prevannee=$annee-1;
if ($date == 12) $nextannee=$annee+1;

// la date
if(!$date=$_GET["m"]){ $date=date("n"); }
if($date==1)  { $prev=12; }else{ $prev=$date-1; }
if($date==12) { $next=1; }else{ $next=$date+1; }


// à verifier avec postgresql
if ($date < 10) {
	$date=preg_replace("/0/","",$date);
}


if($date==1):
	$prev=12;
else:
	$prev=$date-1;
endif;
if($date==12):
	$next=1;
else:
	$next=$date+1;
endif;

if ($_SESSION["membre"] == "menututeur") { $Seid=""; }

if (isset($_POST["idelevetuteur"])) {
	$Seid=$_POST["idelevetuteur"];
	$_SESSION["idelevetuteur"]=$Seid;
	$Scid=chercheClasseEleve($Seid);
	$_SESSION["idClasse"]=$Scid;
}

if ((trim($Seid) == "") && ($_SESSION["membre"] == "menututeur")) {
         $list=listEleveTuteur2($_SESSION["id_pers"]);
         if (count($list) == 1) {
                $Seid=$list[0][0];
                $Scid=chercheClasseEleve($Seid);
                $idClasse=$Scid;
        }
}

if (isset($_SESSION["idelevetuteur"])) {
	$Seid=$_SESSION["idelevetuteur"];
	
}

$anneeScolaire=anneeScolaireViaIdClasse($Scid);

// les matières et leur ordre d'affectation
$sql="
SELECT
	a.code_matiere,
	case
		when sous_matiere = '0' then lower(trim(libelle))
		else CONCAT( lower(trim(libelle)),' ',lower(trim(sous_matiere)) )
	end,
	a.ordre_affichage
FROM
	${prefixe}affectations a, ${prefixe}eleves e, ${prefixe}matieres m
WHERE
	e.elev_id = '$Seid'
AND e.classe = a.code_classe
AND a.annee_scolaire = '$anneeScolaire'
AND a.code_matiere = m.code_mat
AND a.visubull='1'
ORDER BY
	a.ordre_affichage
	";

$curs=execSql($sql);
$ordre=chargeMat($curs);
// les notes

if ($date < 10) {
	$date="0".$date;
}

$dateDuJour=dateDMY2();

$sql=<<<SQL
SELECT
	m.code_mat,
	coef,
	sujet,
	DATE_FORMAT(date,'%d-%m-%Y'),
	TRUNCATE(note,2),
	n.typenote,
	n.notationsur,
	n.id_groupe,
	n.noteexam
FROM
	${prefixe}notes n, ${prefixe}matieres m
WHERE
	elev_id='$Seid'
AND DATE_FORMAT(date,'%m')='$date'
AND m.code_mat = n.code_mat
AND n.notevisiblele <= '$dateDuJour'
AND DATE_FORMAT(date,'%Y')='$annee'
SQL;



$curs=execSql($sql);
$mat=chargeMat($curs);
unset($curs);

class Note {
	var $coeff;
	var $sujet;
	var $date;
	var $valeur;
	var $typenote;
	var $notationsur;
	var $idgroupe;
	var $examen;
	function __construct($c,$s,$d,$v,$t,$u,$g,$e){
		$this->coeff = $c;
		$this->sujet = $s;
		$this->date = $d;
		$this->valeur = $v;
		$this->typenote= $t;
		$this->notationsur= $u;
		$this->groupe = $g;
		$this->examen = $e;
	}	
}

for($i=0;$i<count($mat);$i++){
	$cm=$mat[$i][0];
	$mat2[$cm][]= new Note($mat[$i][1],$mat[$i][2],$mat[$i][3],$mat[$i][4],$mat[$i][5],$mat[$i][6],$mat[$i][7],$mat[$i][8]);
}
$cles=@array_keys($mat2);
for($i=0;$i<count($ordre);$i++){
	$cle1=$ordre[$i][0];
	$cle2=$ordre[$i][1];
	$j=$ordre[$i][2];
	if(@in_array($cle1,$cles)):
		$cle2.="|x|$i|x|$j";
		$matFinal[$cle2]= $mat2[$cle1];
	else:
		$cle2.="|x|$i|x|$j";
		$matFinal[$cle2]=array();
	endif;
}
?>
<HTML>
<HEAD>
<title>Triade - Compte de <?php print ucwords($Sprenom)." ".strtoupper($Snom)?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<style>
	 td.local {background-color : #FFFFFF;color:red;}
	 a.local {background-color : #FFFFFF;color:red;}
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form name="formnote" method='post' action='notesEleve.php' >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr bgcolor="#666666">
<?php
if ($date < 10) {
	$date=preg_replace("/0/","",$date);
}
?>
<td height="2" colspan=2> <b><font  color="#FFFFFF"><?php print LANGELEV1 ?>  <font color="orange"> <?php print $MOIS[$date]?> <?php print $annee ?></font></font></b>
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
<br />
<br />
<center><input type=text name="note" size=85 STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" ></center>
 <br />
</td>
     </tr>
     <tr bgcolor="#CCCCCC">
     <td colspan=2>
     <!-- CORPS -->
	<?php
	if (((ACCESNOTEPARENT == "non") && ($_SESSION["membre"] == "menuparent")) || ((ACCESNOTEELEVE == "non") && ($_SESSION["membre"] == "menueleve")) ){
		print "<center><font color='red' class='T2' >".LANGMESS37.".</font></center>";
	}else{
	?>
		<table border="1" bgcolor="#FFFFFF" width=100%>
 	<?php

		error_reporting(0);
		// éviter le warning du foreach
		foreach($matFinal as $key => $value){
			list($key,$pos,$pos2)=preg_split("/\|x\|/",$key);
			$idMatiere=chercheIdMatiere(stripslashes($key));
			$verifGroupe=verifMatiereAvecGroupeCarnetDeNote2($idMatiere,$Seid,$Scid,$pos2);
			if ($verifGroupe) { continue; } // verif pour l'eleve de l'affichage de la matiere
			$idprof=profAff($idMatiere,$Scid,$pos2,$anneeScolaire);  //$idMatiere,$idClasse,$ordre
			$prof=recherche_personne2($idprof);
			$title=preg_replace('/"/',"'",ucwords($key));
			$key=stripslashes($key);
			echo "<tr class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >";
			echo "<td id='bordure' width=33% >";

//print "$key $pos<br>";
			echo "&nbsp;<a href=\"javascript:void\" onmouseover=\"document.formnote.note.value='Enseigné par ".$prof."'; return true;  \" onmouseout=\"document.formnote.note.value='';\" title=\"$title\" ><b>".trunchaine(ucwords($key),50)."</b></a>&nbsp;</td><td id='bordure'>";
			$j=0;
			for($i=0;$i<count($value);$i++){
				$coeff=$value[$i]->coeff;
				$date=$value[$i]->date;
				$sujet=$value[$i]->sujet;
				$note=$value[$i]->valeur;
				$typenote=$value[$i]->typenote;
				$notationsur=$value[$i]->notationsur;
				$idgroupe=$value[$i]->groupe;
				$examen=$value[$i]->examen;
			
				$idcclasse=$Scid;
				if ($idgroupe != 0) {  $idcclasse="-1"; }
			
				$moyenne=moyenneDevoir($idMatiere,$date,$idprof,$sujet,$coeff,$examen,$idgroupe,$idcclasse);
				$moyendevoir=$moyenne['moy'];
				$mindevoir=$moyenne['min'];
				$maxdevoir=$moyenne['max'];
				$j++;	
				if ($j==12) { print "<br>"; $j=0; }
				
				$sujet=preg_replace('/"/','&rdquo;',$sujet);
			//	$sujet=preg_replace('/\\/','\\\'',$sujet);
				$noteaff=$note;
				if ($note == -1) { 
					$noteaff="abs"; 
					$text2="Le $date  - $sujet - Coeff: $coeff - Notation sur : $notationsur";
					$mess="<font color=\'blue\' >Le</font> $date - <font color=\'blue\' >Sujet :</font> $sujet <br>";
					$mess.="<font color=\'blue\' >Coefficient :</font> $coeff ";
					$mess.="(notation sur $notationsur) <br>";
					$mess.="<font color=\'blue\' >Moy. :</font> $moyendevoir  <font color=\'blue\' >min :</font> $mindevoir  <font color=\'blue\' >max :</font> $maxdevoir <br>";
				}elseif ($note == -2) { 
					$noteaff="disp"; 
					$text2="Le $date  - $sujet - Coeff: $coeff - Notation sur : $notationsur";
					$mess="<font color=\'blue\' >Le</font> $date - <font color=\'blue\' >Sujet :</font> $sujet <br>";
					$mess.="<font color=\'blue\' >Coefficient :</font> $coeff ";
					$mess.="(notation sur $notationsur) <br>";
					$mess.="<font color=\'blue\' >Moy. :</font> $moyendevoir  <font color=\'blue\' >min :</font> $mindevoir  <font color=\'blue\' >max :</font> $maxdevoir <br>";
				}elseif ($note == -3) { 
					$noteaff=""; 
					$text2="";
				}elseif ($note == -4) { 
					$noteaff="DNN"; 
					$text2="Le $date  - $sujet - Coeff: $coeff - Notation sur : $notationsur";
					$mess="<font color=\'blue\' >Le</font> $date - <font color=\'blue\' >Sujet :</font> $sujet <br>";
					$mess.="<font color=\'blue\' >Coefficient :</font> $coeff ";
					$mess.="(notation sur $notationsur) <br>";
					$mess.="<font color=\'blue\' >Moy. :</font> $moyendevoir  <font color=\'blue\' >min :</font> $mindevoir  <font color=\'blue\' >max :</font> $maxdevoir <br>";
				}elseif ($note == -5) { 
					$noteaff="DNR"; 
					$text2="Le $date  - $sujet - Coeff: $coeff - Notation sur : $notationsur";
					$mess="<font color=\'blue\' >Le</font> $date - <font color=\'blue\' >Sujet :</font> $sujet <br>";
					$mess.="<font color=\'blue\' >Coefficient :</font> $coeff ";
					$mess.="(notation : $notationsur) <br>";
					$mess.="<font color=\'blue\' >Moy. :</font> $moyendevoir  <font color=\'blue\' >min :</font> $mindevoir  <font color=\'blue\' >max :</font> $maxdevoir <br>";
				}elseif ($note == -6) { 
					$noteaff="VAL"; 
					$text2="Le $date  - $sujet - Coeff: $coeff - Notation sur : $notationsur";
					$mess="<font color=\'blue\' >Le</font> $date - <font color=\'blue\' >Sujet :</font> $sujet <br>";
					$mess.="<font color=\'blue\' >Coefficient :</font> $coeff ";
					$mess.="(notation : $notationsur) <br>";
					$mess.="<font color=\'blue\' >Moy. :</font> $moyendevoir  <font color=\'blue\' >min :</font> $mindevoir  <font color=\'blue\' >max :</font> $maxdevoir <br>";
				}elseif ($note == -7) { 
					$noteaff="NVAL"; 
					$text2="Le $date  - $sujet - Coeff: $coeff - Notation sur : $notationsur";
					$mess="<font color=\'blue\' >Le</font> $date - <font color=\'blue\' >Sujet :</font> $sujet <br>";
					$mess.="<font color=\'blue\' >Coefficient :</font> $coeff ";
					$mess.="(notation : $notationsur) <br>";
					$mess.="<font color=\'blue\' >Moy. :</font> $moyendevoir  <font color=\'blue\' >min :</font> $mindevoir  <font color=\'blue\' >max :</font> $maxdevoir <br>";
				}else{
					$text2="Le $date  - $sujet - Coeff: $coeff - Notation sur : $notationsur";
					$mess="<font color=\'blue\' >Le</font> $date - <font color=\'blue\' >Sujet :</font> $sujet <br>";
					$mess.="<font color=\'blue\' >Coefficient :</font> $coeff ";
					$mess.="(notation sur $notationsur) <br>";
					$mess.="<font color=\'blue\' >Moy. :</font> $moyendevoir  <font color=\'blue\' >min :</font> $mindevoir  <font color=\'blue\' >max :</font> $maxdevoir <br>";
					if ( $note == "-3" ) {
							$noteaff="";

					}else{
						if (trim($typenote) == "en") {
							$noteaff=recherche_note_en($note);
				   		}
				   	}
				}


                                $bgcolorexamen="";
                                if ($examen != "") {
                                        $mess.="<font color=\'blue\' >Examen :</font> $examen";
                                        if ($examen != "") $bgcolorexamen="background-color:'yellow'";
                                }


				if (trim($typenote) != "en") {
					$noteaff=preg_replace('/0$/','',$noteaff);
				}
				$information="Information";
				echo "<a href=\"javascript:void\" onmouseover=\"document.formnote.note.value='".$text2."'; return true;  \" onmouseout=\"document.formnote.note.value='';\"  title='Cliquer pour information' onClick=\"AffBulleAvecQuit('$information','./image/commun/info.jpg','$mess'); window.status=''; return true;\"  ><span style=\"$bgcolorexamen\" >".$noteaff."</span></a>&nbsp;-&nbsp;";
			}
			echo "</td></tr>";
		}
		?>
	 	</table>
<!--
		<table border="1">
		<tr>
			<td><b>détails note</b></td>
		</tr>
		<tr>
			<td>
					<div id="note">
						&nbsp;
					</div>
			</td>
		</tr>
		</table>
-->
	 <!-- fin CORPS  -->
     </td></tr>
	 <tr><td class="local" width=50% align=center><a class="local" href="notesEleve.php?m=<?php print $prev?>&annee=<?php print $prevannee ?>"> <--- <?php print $MOIS[$prev]?></a></td><td class="local" align=center><a class="local" href="notesEleve.php?m=<?php print $next?>&annee=<?php print $nextannee ?>"><?php print $MOIS[$next]?> ---> </a></td></tr>
		<?php } ?>
	 </table>

</form>
	

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
?>
<SCRIPT language="JavaScript">InitBulle("#000000","#FFFFFF","red",1);</SCRIPT>
   </BODY>
   </HTML>
   <?php @Pgclose() ?>
