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
include_once("librairie_php/lib_emul_register.php");
include_once("common/config.inc.php"); // futur : auto_prepend_file
include_once("common/config2.inc.php"); // futur : auto_prepend_file
include_once("librairie_php/db_triade.php");
include_once("librairie_php/lib_prefixe.php");
include_once("librairie_php/recupnoteperiode.php");
$cnx=cnx();
error($cnx);
if ((defined("NOTEELEVEVISU")) && (NOTEELEVEVISU == "oui")) {
	validerequete("profadmin");
}
if (($_SESSION["membre"] == "menuprof" ) && (NOTEELEVEVISU == "non")) {
	verif_profp_eleve($_GET['Seid'],$_SESSION["id_suppleant"],$_SESSION["membre"]);
}

$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_GET["anneeScolaire"])) $anneeScolaire=$_GET["anneeScolaire"];

// variables
$myS=$_GET;
$Snom=$myS[Snom];
$Sprenom=$myS[Sprenom];
$Seid=$myS[Seid];
$Scid=$myS[Scid];
unset($myS);

if (trim($Snom) == "") {
	$Snom=recherche_eleve_nom($Seid);
}

if (trim($Sprenom) == "") {
	$Sprenom=recherche_eleve_prenom($Seid);
}

// la date
if(!$date=$_GET['m']){
        $date=dateM();
        $annee=dateY();
}

if (isset($_GET['annee'])) {
        $annee=$_GET['annee'];
}
$prevannee=$annee;
$nextannee=$annee;

if ($date == 1) $prevannee=$annee-1;
if ($date == 12) $nextannee=$annee+1;



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

// les matières et leur ordre d'affectation
global $prefixe;
$sql=<<<SQL
SELECT
        a.code_matiere,
        case
                when sous_matiere = '0' then lower(trim(libelle))
                else lower(trim(CONCAT(libelle,' ',sous_matiere)))
        end,
        a.ordre_affichage,a.code_groupe
FROM
	${prefixe}affectations a, ${prefixe}eleves e, ${prefixe}matieres m
WHERE
	e.elev_id = '$Seid'
AND e.classe = a.code_classe
AND a.code_matiere = m.code_mat
AND a.visubull='1'
AND a.annee_scolaire = '$anneeScolaire'
ORDER BY
	a.ordre_affichage
SQL;

$curs=execSql($sql);
$ordre=chargeMat($curs);

// les notes
if ($date < 10) {
        $date="0".$date;
}


$sql=<<<SQL
SELECT
	m.code_mat,
	coef,
	sujet,
	DATE_FORMAT(date,'%d-%m-%Y'),
	TRUNCATE(note,2),
	n.typenote,
	n.notationsur
FROM
	${prefixe}notes n, ${prefixe}matieres m
WHERE
	elev_id='$Seid'
AND DATE_FORMAT(date,'%m')='$date'
AND DATE_FORMAT(date,'%Y')='$annee'
AND m.code_mat = n.code_mat
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

function Note($c,$s,$d,$v,$t,$u){
	$this->coeff = $c;
	$this->sujet = $s;
	$this->date = $d;
	$this->valeur = $v;
	$this->typenote= $t;
	$this->notationsur= $u;
}

}

for($i=0;$i<count($mat);$i++){
	$cm=$mat[$i][0];
	$mat2[$cm][]= new Note($mat[$i][1],$mat[$i][2],$mat[$i][3],$mat[$i][4],$mat[$i][5],$mat[$i][6]);
}
$cles=@array_keys($mat2);
for($i=0;$i<count($ordre);$i++){
        $cle1=$ordre[$i][0];
        $cle2=$ordre[$i][1];
        $j=$ordre[$i][2];
	$groupe=$ordre[$i][3];
//      print $cle1." ".$cle2."<br>";
        if(@in_array($cle1,$cles)):
                $cle2.="|x|$i|x|$j|x|$groupe";
                $matFinal[$cle2]= $mat2[$cle1];
        else:
                $cle2.="|x|$i|x|$j|x|$groupe";
                $matFinal[$cle2]=array();
        endif;
}
?>
<HTML>
<HEAD>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
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
<?php
include("./librairie_php/lib_defilement.php");
if ($date < 10) {
        $date=preg_replace('/0/',"",$date);
}
?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form name="formnote">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr bgcolor="#666666">
<td height="2" colspan=2> <font  color="#FFFFFF"><?php print LANGPROFP23?> <b> <?php print stripslashes(ucwords($Sprenom))." ".stripslashes(strtoupper($Snom))?></b> <?php print LANGPROFP24?> <font color="orange"><b> <?php print $MOIS[$date]?> <?php print $annee ?> </font></font></b>
<?php
	if (isset($_GET["fiche"]) && ($_GET["fiche"] == 1)) {
		$fiche="&fiche=1";
?>
		&nbsp;<input type=button class=BUTTON value="Retour Menu" onclick="open('ficheeleve3.php?fiche=1&eid=<?php print $Seid?>','_parent','')">
<?php 
	}else{
		$fiche="";
?>
		 &nbsp;<input type=button class=BUTTON value="Retour Menu" onclick="open('profp3.php?eid=<?php print $Seid?>','_parent','')">
<?php } ?>

<br /><br />
<center><input type=text name="note" size=85 STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" >  <br /><br /></center>
</td>
     </tr>
     <tr bgcolor="#CCCCCC">
     <td colspan=2>
     <!-- CORPS -->
		<table border="1" bgcolor="#FFFFFF" style="border-collapse: collapse;"  width='100%' >
 	 	<?php
		error_reporting(0);
		// éviter le warning du foreach
		foreach($matFinal as $key => $value){
			list($key,$pos,$pos2,$idgroupe)=preg_split('/\|x\|/',$key);
                        $idMatiere=chercheIdMatiere($key);
			$verifGroupe=verifMatiereAvecGroupeCarnetDeNote2($idMatiere,$Seid,$Scid,$pos);
			if ($verifGroupe) { continue; } // verif pour l'eleve de l'affichage de la matiere
			$idprof=profAff($idMatiere,$Scid,$pos2);  //$idMatiere,$idClasse,$ordre
                        $prof=recherche_personne2($idprof);
			$title=preg_replace('/"/',"'",ucwords($key));
                        $key2=trunchaine(ucwords($key),50);
                        $keyAff=preg_replace('/ /',"&nbsp;",$key2);
		        echo "<tr><td width='35%'>&nbsp;<strong><a href=\"javascript:void\" onmouseover=\"document.formnote.note.value='Enseigné par ".$prof."'; return true;  \" onmouseout=\"document.formnote.note.value='';\"  >".trunchaine(ucwords($key),40)."</a></strong>&nbsp;</td><td>";
			for($i=0;$i<count($value);$i++){
				$coeff=$value[$i]->coeff;
				$date=$value[$i]->date;
				$sujet=$value[$i]->sujet;
				$note=$value[$i]->valeur;
				$typenote=$value[$i]->typenote;
				$notationsur=$value[$i]->notationsur;
				$sujet=preg_replace('/"/','&rdquo;',$sujet);
                		$sujet=preg_replace('/\'/','\\\'',$sujet);
				$noteaff=$note;
				if ($note == -1) { 
					$noteaff="abs"; 
					$text2="Le $date  - $sujet - Coeff: $coeff - Notation sur : $notationsur";
				}elseif ($note == -2) { 
					$noteaff="disp"; 
					$text2="Le $date  - $sujet - Coeff: $coeff - Notation sur : $notationsur";
				}elseif ($note == -3) { 
					$noteaff=""; 
					$text2="";
				}elseif ($note == -4) { 
					$noteaff="DNN"; 
					$text2="Le $date  - $sujet - Coeff: $coeff - Notation sur : $notationsur";
				}elseif ($note == -5) { 
					$noteaff="DNR"; 
					$text2="Le $date  - $sujet - Coeff: $coeff - Notation sur : $notationsur";
				}elseif ($note == -6) { 
					$noteaff="VAL"; 
					$text2="Le $date  - $sujet - Coeff: $coeff - Notation sur : $notationsur";
				}elseif ($note == -7) { 
					$noteaff="NVAL"; 
					$text2="Le $date  - $sujet - Coeff: $coeff - Notation sur : $notationsur";
				}else{
					$text2="Le $date  - $sujet - Coeff: $coeff - Notation sur : $notationsur";
					if ( $note == "-3" ) {
							$noteaff="";

					}else{
						if (trim($typenote) == "en") {
							$noteaff=recherche_note_en($note);
				   		}
				   	}
				}
				if (trim($typenote) != "en") {
					// $noteaff=preg_replace('/.00$/','',$noteaff);
				}

				$moyenne=moyenneDevoir($idMatiere,$date,$idprof,$sujet,$coeff,$examen,$idgroupe,$Scid);
				$moyendevoir=$moyenne['moy'];
				$mindevoir=$moyenne['min'];
				$maxdevoir=$moyenne['max'];

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
//				echo "<a href=\"javascript:void\" onmouseover=\"document.formnote.note.value='".$text2."'; return true;  \" onmouseout=\"document.formnote.note.value='';\"  >".$noteaff."</a>&nbsp;-&nbsp;";
				$information="Information";
                                echo "<a href=\"javascript:void\" onmouseover=\"document.formnote.note.value='".$text2."'; return true;  \" onmouseout=\"document.formnote.note.value='';\"  title='Cliquer pour information' onClick=\"AffBulleAvecQuit('$information','./image/commun/info.jpg','$mess'); window.status=''; return true;\"  ><span style=\"$bgcolorexamen\" >".$noteaff."</span></a>&nbsp;-&nbsp;";
			}
			echo "</td></tr>";
		}
		?>
	 	</table>
	 <!-- fin CORPS  -->
     </td>
	 </tr>
	 <tr>
	 	<td class="local" width=50% align=center>
		<a class="local" href="profpcarnet.php?Snom=<?php print stripslashes(strtolower($Snom))?>&Sprenom=<?php print stripslashes(strtolower($Sprenom))?>&Seid=<?php print $Seid?>&Scid=<?php print $Scid?>&m=<?php print $prev?><?php print $fiche?>&annee=<?php print $prevannee?>&anneeScolaire=<?php print $anneeScolaire?>"> <--- <?php print $MOIS[$prev]?></a>
</td><td class="local" align=center>
		<a class="local" href="profpcarnet.php?Snom=<?php print stripslashes(strtolower($Snom))?>&Sprenom=<?php print stripslashes(strtolower($Sprenom))?>&Seid=<?php print $Seid?>&Scid=<?php print $Scid?>&m=<?php print $next?><?php print $fiche?>&annee=<?php print $nextannee?>&anneeScolaire=<?php print $anneeScolaire?>"><?php print $MOIS[$next]?> ---> </a>
	 	</td>
	 </tr>
	 </table>
</form>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION[membre] == "menuadmin") :
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
<SCRIPT language="JavaScript">InitBulle("#000000","#CCCCCC","red",1);</SCRIPT>
   </BODY>
   </HTML>
   <?php @Pgclose() ?>
