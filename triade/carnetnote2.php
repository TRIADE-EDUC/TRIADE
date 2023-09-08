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
include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php"); // futur : auto_prepend_file
include_once("./librairie_php/db_triade.php");
include_once("librairie_php/recupnoteperiode.php");
validerequete("menuadmin");


$cnx=cnx();
// variables
$myS=$_GET;
$Snom=$myS[nom];
$Sprenom=$myS[prenom];
$Seid=$myS[id_pers];
$Scid=$myS[idClasse];
unset($myS);

$visu="mois";
if (isset($_GET["visu"])) $visu=$_GET["visu"];


if ($visu == "trimestre") {
	$TRIMESTRE["prev"]="Précédent";
	$TRIMESTRE["sui"]="Suivant";
	
}


$anneeScolaire=anneeScolaireViaIdClasse($Scid);
if (isset($_GET["anneeScolaire"])) $anneeScolaire=$_GET["anneeScolaire"]; 

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

// les matières et leur ordre d'affectation
if (!isset($_GET["anneeScolaire"])) {
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
	AND classe = a.code_classe
	AND a.code_matiere = m.code_mat
	AND a.annee_scolaire = '$anneeScolaire'
	AND a.visubull='1'
ORDER BY
	a.ordre_affichage,a.code_groupe
SQL;


$curs=execSql($sql);
$ordre=chargeMat($curs);
}else{
	
	if (ISMAPP == 1) {	

$sql=<<<SQL
SELECT
        a.code_matiere,
        case
                when sous_matiere = '0' then lower(trim(libelle))
                else lower(trim(sous_matiere))
        end,
	a.ordre_affichage,a.code_groupe
FROM
        ${prefixe}affectations a, ${prefixe}matieres m
WHERE
        a.code_classe = '$Scid'
        AND a.code_matiere = m.code_mat
        AND a.annee_scolaire = '$anneeScolaire'
        AND a.visubull='1'
ORDER BY
        a.ordre_affichage
SQL;

	}else{

$sql=<<<SQL
SELECT
        a.code_matiere,
        case
                when sous_matiere = '0' then lower(trim(libelle))
                else lower(trim(CONCAT(libelle,' ',sous_matiere))) 
        end,
        a.ordre_affichage,a.code_groupe
FROM
        ${prefixe}affectations a, ${prefixe}matieres m
WHERE
        a.code_classe = '$Scid'
        AND a.code_matiere = m.code_mat
        AND a.annee_scolaire = '$anneeScolaire'
        AND a.visubull='1'
ORDER BY
        a.ordre_affichage
SQL;

	}

$curs=execSql($sql);
$ordre=chargeMat($curs);





}



// les notes
if ($date < 10) {$date="0".$date;}

$sql=<<<SQL
SELECT
	m.code_mat,
	coef,
	sujet,
	DATE_FORMAT(date,'%d-%m-%Y'),
	TRUNCATE(note,2),
	n.typenote,
	n.notationsur,
	n.notevisiblele,
	n.noteexam
FROM
	${prefixe}notes n, ${prefixe}matieres m
WHERE
	elev_id = '$Seid'
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

	function __construct($c,$s,$d,$v,$t,$u,$w,$e){
		$this->coeff = $c;
		$this->sujet = $s;
		$this->date = $d;
		$this->valeur = $v;
		$this->typenote= $t;
		$this->notationsur= $u;
		$this->notevisiblele=$w;
		$this->examen=$e;
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
	$groupe=$ordre[$i][3];
//	print $cle1." ".$cle2."<br>";
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
<SCRIPT language="JavaScript" src="./librairie_js/menuadmin.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/menuadmin1.js"></SCRIPT>
<?php
if ($date <= 9) {
       	$date=preg_replace("/0/","",$date);
}
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr bgcolor="#666666">
<td height="2" colspan=2> <font  color="#FFFFFF"><?php print LANGPROFP23?> <b> <?php print stripslashes(ucwords($Sprenom)." ".strtoupper($Snom))?></b>  <?php print LANGPROFP24?>  <font color="orange"><b> <?php print $MOIS[$date]?> <?php print $annee ?></font> / Année Scolaire : <font color="orange"><?php print $anneeScolaire ?></font></font></b>
<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<form method='get' action="carnetnote2.php" >
<input type=text name="note" size=80 STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" >
&nbsp;&nbsp;
<select name='visu' onChange="this.form.submit()" >
	<option value='mois' id='select0' <?php print ($visu == "mois") ? "selected='selected'" : "" ?> >Par Mois</option>
<option value='trimestre' id='select0' <?php print ($visu == "trimestre") ? "selected='selected'" : "" ?> >Par Trimestre/Semestre</option>
<option value='annee' id='select0' <?php print ($visu == "annee") ? "selected='selected'" : "" ?> >Par Année</option>
</select>
<input type='hidden' name='nom' value="<?php print stripslashes(strtolower($Snom))?>" />
<input type='hidden' name='prenom' value="<?php print stripslashes(strtolower($Sprenom))?>" />
<input type='hidden' name='id_pers' value="<?php print $Seid?>" />
<input type='hidden' name='idClasse' value="<?php print $Scid?>" />
<input type='hidden' name='m' value="<?php print $m?>" />
<input type='hidden' name='annee' value="<?php print $nextannee?>" />
<input type='hidden' name='anneeScolaire' value="<?php print $anneeScolaire?>" />
</form>

<form name="formnote">
</td>
     </tr>
     <tr id='cadreCentral0'>
     <td colspan=2>
     <!-- CORPS -->
		<table border="1" bgcolor="#FFFFFF" width='100%' >
 	 	<?php
		error_reporting(0);
		// éviter le warning du foreach
		foreach($matFinal as $key => $value){
			list($key,$pos,$pos2,$idgroupe)=preg_split("/\|x\|/",$key);
			$idMatiere=chercheIdMatiere(stripslashes($key));
			$verifGroupe=verifMatiereAvecGroupeCarnetDeNote2($idMatiere,$Seid,$Scid,$pos2);
			if ($verifGroupe) { continue; } // verif pour l'eleve de l'affichage de la matiere
			$idprof=profAff($idMatiere,$Scid,$pos2);  //$idMatiere,$idClasse,$ordre
			$prof=recherche_personne2($idprof);
			$title=preg_replace('/"/',"'",ucwords($key));
			$key2=trunchaine(ucwords($key),50);
			$keyAff=preg_replace('/ /',"&nbsp;",$key2);	
			$keyAff=stripslashes($keyAff);
			echo "<tr>";
			echo "<tr class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >";
			echo "<td id='bordure' width=33% >";
			echo "&nbsp;<a href=\"javascript:void\" onmouseover=\"document.formnote.note.value='Enseigné par ".$prof."'; return true;  \" onmouseout=\"document.formnote.note.value='';\" title=\"$title\" ><b>".$keyAff."</b></a>&nbsp;</td><td id='bordure'>";
			for($i=0;$i<count($value);$i++){
				$coeff=$value[$i]->coeff;
				$date=$value[$i]->date;
				$sujet=$value[$i]->sujet;
				$note=$value[$i]->valeur;
				$notevisiblele=$value[$i]->notevisiblele;
				$notationsur=$value[$i]->notationsur;
				//$idgroupe=$value[$i]->groupe;
				$examen=$value[$i]->examen;
		
				$moyenne=moyenneDevoir($idMatiere,$date,$idprof,$sujet,$coeff,$examen,$idgroupe,$Scid);
				$moyendevoir=$moyenne['moy'];
				$mindevoir=$moyenne['min'];
				$maxdevoir=$moyenne['max'];

				$sujet=preg_replace('/"/','&rdquo;',$sujet);
                		$sujet=preg_replace('/\'/','\\\'',$sujet);
		                $typenote=$value[$i]->typenote;
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
	 <!-- fin CORPS  -->
     </td>
	 </tr>
	 <tr>

<?php if ($visu == "mois") { ?>
	 	<td class="local" width=50% align=center>
		<a class="local" href="carnetnote2.php?nom=<?php print stripslashes(strtolower($Snom))?>&prenom=<?php print stripslashes(strtolower($Sprenom))?>&id_pers=<?php print $Seid?>&idClasse=<?php print $Scid?>&m=<?php print $prev?>&annee=<?php print $prevannee?>&anneeScolaire=<?php print $anneeScolaire?>&visu=<?php print $visu ?>" > <--- <?php print $MOIS[$prev]?></a>
		</td><td class="local" align=center>
		<a class="local" href="carnetnote2.php?nom=<?php print stripslashes(strtolower($Snom))?>&prenom=<?php print stripslashes(strtolower($Sprenom))?>&id_pers=<?php print $Seid?>&idClasse=<?php print $Scid?>&m=<?php print $next?>&annee=<?php print $nextannee?>&anneeScolaire=<?php print $anneeScolaire?>&visu=<?php print $visu ?>"><?php print $MOIS[$next]?> ---> </a>
		</td>
<?php } ?>

<?php if ($visu == "trimestre") { ?>
		<td class="local" width=50% align=center>
		<?php if ($prev >= 2) { ?>
		<a class="local" href="carnetnote2.php?nom=<?php print stripslashes(strtolower($Snom))?>&prenom=<?php print stripslashes(strtolower($Sprenom))?>&id_pers=<?php print $Seid?>&idClasse=<?php print $Scid?>&m=<?php print $prev?>&annee=<?php print $prevannee?>&anneeScolaire=<?php print $anneeScolaire?>&visu=<?php print $visu ?>" > <--- <?php print $TRIMESTRE["prev"]?></a>
		<?php } ?>
		</td><td class="local" align=center>
		<?php if ($next < 3) { ?>
		<a class="local" href="carnetnote2.php?nom=<?php print stripslashes(strtolower($Snom))?>&prenom=<?php print stripslashes(strtolower($Sprenom))?>&id_pers=<?php print $Seid?>&idClasse=<?php print $Scid?>&m=<?php print $next?>&annee=<?php print $nextannee?>&anneeScolaire=<?php print $anneeScolaire?>&visu=<?php print $visu ?>"><?php print $TRIMESTRE["sui"]?> ---> </a>
		<?php } ?>
		</td>
<?php } ?>

<?php if ($visu == "annee") { ?>
	 	<td class="local" width=50% align=center>
		<a class="local" href="carnetnote2.php?nom=<?php print stripslashes(strtolower($Snom))?>&prenom=<?php print stripslashes(strtolower($Sprenom))?>&id_pers=<?php print $Seid?>&idClasse=<?php print $Scid?>&m=<?php print $prev?>&annee=<?php print $prevannee?>&anneeScolaire=<?php print $anneeScolaire?>&visu=<?php print $visu ?>" > <--- <?php print $ANNEE[$prev]?></a>
		</td><td class="local" align=center>
		<a class="local" href="carnetnote2.php?nom=<?php print stripslashes(strtolower($Snom))?>&prenom=<?php print stripslashes(strtolower($Sprenom))?>&id_pers=<?php print $Seid?>&idClasse=<?php print $Scid?>&m=<?php print $next?>&annee=<?php print $nextannee?>&anneeScolaire=<?php print $anneeScolaire?>&visu=<?php print $visu ?>"><?php print $ANNEE[$next]?> ---> </a>
		</td>
<?php } ?>

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
