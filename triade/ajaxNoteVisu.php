<?php
session_start();
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/choixlangue.php");
include_once("./librairie_php/langue.php");
include_once("./librairie_php/recupnoteperiode.php");



if (( ((defined("NOTEELEVEVISU")) && (NOTEELEVEVISU == "oui")) && ($_SESSION["membre"] == "menuprof")) || ($_SESSION["membre"] == "menupersonnel") || ($_SESSION["membre"] == "menuadmin") || (($_SESSION["membre"] == "menuscolaire") && (VIESCOLAIRENOTEENSEIGNANT == "oui")) ) {
	if ($_SESSION["membre"] == "menupersonnel") {
		$cnx=cnx();
		if (!verifDroit($_SESSION["id_pers"],"ficheeleve")) {
			Pgclose();
			exit();
		}
	}

	$Seid=$_POST["idEleve"];
	$Scid=$_POST["idClasse"];
	$cnx=cnx();
	$anneeScolaire=anneeScolaireViaIdClasse($Scid);


	if(!$date=$_POST["m"]){ 
		$date=dateM(); 
        	$annee=dateY();
	}

	if (!empty($_POST['annee'])) {
	        $annee=$_POST['annee'];
	}
	$prevannee=$annee;
	$nextannee=$annee;

	if ($date == 1)  $prevannee=$annee-1;
	if ($date == 12) $nextannee=$annee+1;

	// la date
	if(!$date=$_POST["m"]){ $date=date("n"); }
	if($date==1)  { $prev=12; }else{ $prev=$date-1; }
	if($date==12) { $next=1; }else{ $next=$date+1; }

//------------------------------------------------
	// les matières et leur ordre d'affectation
	global $prefixe;
	$sql=<<<SQL
SELECT
	a.code_matiere,
	case
		when sous_matiere = '0' then lower(trim(libelle))
		else lower(trim(CONCAT(libelle,' ',sous_matiere)))
	end
	,a.ordre_affichage,a.code_groupe
FROM
	${prefixe}affectations a, ${prefixe}eleves e, ${prefixe}matieres m
WHERE
	e.elev_id = '$Seid'
AND e.classe = a.code_classe
AND a.code_matiere = m.code_mat
AND a.visubull='1'
AND a.annee_scolaire='$anneeScolaire'
ORDER BY
	a.ordre_affichage,a.code_groupe
SQL;
	$curs=execSql($sql);
	$ordre=chargeMat($curs);

	// les notes
	if ($date < 10) { $date="0".$date; }

//------------------------------------------------

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
	elev_id='$Seid'
AND DATE_FORMAT(date,'%m')='$date'
AND DATE_FORMAT(date,'%Y')='$annee'
AND m.code_mat = n.code_mat
SQL;

//------------------------------------------------
	

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
		var $notevisiblele;
		var $examen;

		function Note($c,$s,$d,$v,$t,$u,$w,$e){
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
		//$mat2[$cm][]= new Note($mat[$i][1],$mat[$i][2],$mat[$i][3],$mat[$i][4],$mat[$i][5],$mat[$i][6]);
		$mat2[$cm][]= new Note($mat[$i][1],$mat[$i][2],$mat[$i][3],$mat[$i][4],$mat[$i][5],$mat[$i][6],$mat[$i][7],$mat[$i][8]);
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

	if ($date <= 9) { $date=preg_replace('/0/',"",$date); }

	$reponse="<form name='formnote'>";
	$reponse.="<table border='0' cellpadding='3' cellspacing='1' width='100%' bgcolor='#0B3A0C' height='85'  style='border-collapse: collapse;'  >";
	$reponse.="<tr bgcolor='#666666'>";
	$reponse.="<td height='2' colspan='2'><font color='#FFFFFF'>Notes scolaires du mois de <font color='orange'><b>".txt_vers_html($MOIS[$date])." $annee </font></font></b>";
	$reponse.="<br /><br />";
	$reponse.="<center><input type=text name='note' size='85' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;' ><br /><br /></center>";
	$reponse.="</td>";
 	$reponse.="</tr>";
 	$reponse.="<tr bgcolor='#CCCCCC'>";
     	$reponse.="<td colspan=2>";
	$reponse.="<table border='1' bgcolor='#FFFFFF'>";
	if (count($matFinal)) {
	  foreach($matFinal as $key => $value){
		list($key,$pos,$pos2,$idgroupe)=preg_split('/\|x\|/',$key);
                $idMatiere=chercheIdMatiere($key);
                $verifGroupe=verifMatiereAvecGroupeCarnetDeNote2($idMatiere,$Seid,$Scid,$pos2);
                if ($verifGroupe) { continue; } // verif pour l'eleve de l'affichage de la matiere
                $idprof=profAff($idMatiere,$Scid,$pos2);  //$idMatiere,$idClasse,$ordre
                $prof=recherche_personne2($idprof);
                $title=preg_replace('/"/',"'",ucwords($key));
                $key2=trunchaine(ucwords($key),50);
                $keyAff=preg_replace('/ /',"&nbsp;",$key2);

		$reponse.="<tr><td>&nbsp;<strong><a href=\"javascript:void\" onmouseover=\"document.formnote.note.value='Enseign&eacute; par $prof'; return true;  \" onmouseout=\"document.formnote.note.value='';\" title=\"".quotes_spec(txt_vers_html_sans_quote($key))."\" >".trunchaine(txt_vers_html_sans_quote(ucfirst($key)),30)."</a></strong>&nbsp;</td><td>";
		for($i=0;$i<count($value);$i++){
			$coeff=$value[$i]->coeff;
			$date=$value[$i]->date;
			$sujet=$value[$i]->sujet;
			$note=$value[$i]->valeur;
			$typenote=$value[$i]->typenote;
			$notationsur=$value[$i]->notationsur;
			$sujet=preg_replace('/"/','&rdquo;',$sujet);
                	$sujet=preg_replace('/\'/','\\\'',$sujet);


			$notevisiblele=$value[$i]->notevisiblele;
			$notationsur=$value[$i]->notationsur;
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
					if (trim($typenote) == "en") { $noteaff=recherche_note_en($note); }
			   	}
			}
			if (trim($typenote) != "en") {
				// $noteaff=preg_replace('/.00$/','',$noteaff);
			}
			$text2=TextNoAccent($text2);
//			$reponse.="<a href=\"javascript:void\" onmouseover=\"document.formnote.note.value='$text2'; return true;  \" onmouseout=\"document.formnote.note.value='';\"  >$noteaff</a>&nbsp;-&nbsp;";
			$information="Information";
                        $reponse.="<a href=\"javascript:void\" onmouseover=\"document.formnote.note.value='".$text2."'; return true;  \" onmouseout=\"document.formnote.note.value='';\"  title='Cliquer pour information' onClick=\"AffBulleAvecQuit('$information','./image/commun/info.jpg','$mess'); window.status=''; return true;\"  ><span style=\"$bgcolorexamen\" >".$noteaff."</span></a>&nbsp;-&nbsp;";

		}
		$reponse.="</td></tr>";
	    }
	}
 	$reponse.="</table></td></tr><tr bgcolor='#666666' ><td width=50% align=center>";
	$reponse.="<a href='#' onclick=\"ajaxVisuNote('$Seid','$Scid','$prev$fiche','$prevannee')\" ><font color='#FFFFFF'><--- ".txt_vers_html($MOIS[$prev])."</font></a>";
	$reponse.="</td><td align=center>";
	$reponse.="<a href='#' onclick=\"ajaxVisuNote('$Seid','$Scid','$next$fiche','$nextannee')\" ><font color='#FFFFFF'>".txt_vers_html($MOIS[$next])." ---></font></a>";
	$reponse.="</td></tr></table></form>";
	print utf8_encode($reponse);

	Pgclose();
} 
?>
