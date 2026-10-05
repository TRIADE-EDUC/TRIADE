<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/choixlangue.php");
include_once("./librairie_php/langue.php");
include_once("./librairie_php/recupnoteperiode.php");

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 */
$cnx=cnx();
$cnx->query("SET NAMES 'latin1'");
if (( ((defined("NOTEELEVEVISU")) && (NOTEELEVEVISU == "oui")) && ($_SESSION["membre"] == "menuprof")) || ($_SESSION["membre"] == "menupersonnel") || ($_SESSION["membre"] == "menuadmin") || (($_SESSION["membre"] == "menuscolaire") && (VIESCOLAIRENOTEENSEIGNANT == "oui")) ) {
	if ($_SESSION["membre"] == "menupersonnel") {
		if (!verifDroit($_SESSION["id_pers"],"ficheeleve")) {
			Pgclose();
			exit();
		}
	}

	$Seid=$_POST["idEleve"];
	$Scid=$_POST["idClasse"];
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
		when sous_matiere = '0' then trim(libelle)
		else trim(CONCAT(libelle,' ',sous_matiere))
	end
	,a.ordre_affichage,a.code_groupe
FROM
	{$prefixe}affectations a, {$prefixe}eleves e, {$prefixe}matieres m
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
	{$prefixe}notes n, {$prefixe}matieres m
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

	for($i=0;$i<countTriade($mat);$i++){
		$cm=$mat[$i][0];
		$mat2[$cm][]= new Note($mat[$i][1],$mat[$i][2],$mat[$i][3],$mat[$i][4],$mat[$i][5],$mat[$i][6],$mat[$i][7],$mat[$i][8]);
	}
	$cles = is_array($mat2) ? array_keys($mat2) : [];
	for($i=0;$i<countTriade($ordre);$i++){
	        $cle1=$ordre[$i][0];
	        $cle2=$ordre[$i][1];
	        $j=$ordre[$i][2];
	        $groupe=$ordre[$i][3];
	        if(@in_array($cle1,$cles)):
	                $cle2.="|x|$i|x|$j|x|$groupe";
	                $matFinal[$cle2]= $mat2[$cle1];
	        else:
	                $cle2.="|x|$i|x|$j|x|$groupe";
        	        $matFinal[$cle2]=array();
	        endif;
	}

	if ($date <= 9) { $date=preg_replace('/0/',"",$date); }

	/* ── CSS spécifiques au carnet de notes (injecté via AJAX) ── */
	$reponse = "<style>
.nv-info { width:100%;box-sizing:border-box;border:1px solid #c5cae9;border-radius:5px;padding:5px 10px;font-size:11px;color:#3949ab;background:#f0f4ff;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;cursor:default;outline:none }
.nv-notes-cell { display:flex;flex-wrap:wrap;gap:4px;align-items:center }
.nv-note { display:inline-flex;align-items:center;justify-content:center;min-width:32px;padding:2px 6px;border-radius:4px;border:1px solid #dde0f0;background:#fafafa;font-size:11px;font-weight:700;color:#333;text-decoration:none;cursor:pointer;transition:filter .12s;font-family:Electrolize,Trebuchet MS,Arial,sans-serif }
.nv-note:hover { filter:brightness(.93) }
.nv-note-exam { background:#fff8e1 !important;border-color:#f5c842 !important }
.nv-note-abs  { background:#fce4e4 !important;border-color:#ef9a9a !important;color:#c62828 !important }
.nv-note-disp { background:#f5f5f5 !important;border-color:#ccc !important;color:#888 !important }
.nv-note-dnn  { background:#fff3e0 !important;border-color:#ffcc02 !important;color:#e65100 !important }
.nv-note-val  { background:#e8f5e9 !important;border-color:#a5d6a7 !important;color:#2e7d32 !important }
.nv-note-nval { background:#fce4e4 !important;border-color:#ef9a9a !important;color:#c62828 !important }
.nv-subj { color:#080A66;font-weight:700;font-size:11px;text-decoration:none;font-family:Electrolize,Trebuchet MS,Arial,sans-serif }
.nv-subj:hover { color:#3949ab }
.nv-empty { font-size:11px;color:#aaa;font-style:italic;padding:8px 0 }
</style>";

	$reponse.="<form name='formnote'>";
	$reponse.="<div class='card' style='margin:0'>";

	/* ── En-tête : mois/année + navigation ── */
	$moisLabel = txt_vers_html($MOIS[$date]);
	$prevLabel  = txt_vers_html($MOIS[$prev]);
	$nextLabel  = txt_vers_html($MOIS[$next]);
	$reponse.="<div class='card-header'>";
	$reponse.="  <span class='card-title'>Notes scolaires &mdash; <b style='color:#3949ab'>$moisLabel $annee</b></span>";
	$reponse.="  <div style='display:flex;gap:6px;align-items:center'>";
	$reponse.="    <a href='#' onclick=\"ajaxVisuNote('$Seid','$Scid','$prev','$prevannee');return false\" class='btn btn-secondary' style='padding:3px 10px;font-size:11px'><i class='bi bi-chevron-left'></i> $prevLabel</a>";
	$reponse.="    <a href='#' onclick=\"ajaxVisuNote('$Seid','$Scid','$next','$nextannee');return false\" class='btn btn-secondary' style='padding:3px 10px;font-size:11px'>$nextLabel <i class='bi bi-chevron-right'></i></a>";
	$reponse.="  </div>";
	$reponse.="</div>";

	/* ── Barre d'info (mise à jour au survol des notes) ── */
	$reponse.="<div style='padding:6px 12px 4px'>";
	$reponse.="<input type='text' name='note' class='nv-info' readonly placeholder='Survoler une note pour voir les détails...'>";
	$reponse.="</div>";

	/* ── Tableau des matières + notes ── */
	$reponse.="<div class='card-body' style='padding:0'>";
	$reponse.="<table class='table table-hover' style='font-size:11px;margin:0'>";
	$reponse.="<thead><tr><th class='cc-th' style='width:34%'>Matière</th><th class='cc-th'>Notes du mois</th></tr></thead>";
	$reponse.="<tbody>";

	if (countTriade($matFinal)) {
	   foreach($matFinal as $key => $value){
		list($key,$pos,$pos2,$idgroupe)=preg_split('/\|x\|/',$key);
                $idMatiere=chercheIdMatiere($key);
                $verifGroupe=verifMatiereAvecGroupeCarnetDeNote2($idMatiere,$Seid,$Scid,$pos2);
                if ($verifGroupe) { continue; }
                $idprof=profAff($idMatiere,$Scid,$pos2);
                $prof=recherche_personne2($idprof);
                $key=mb_strtolower($key,'UTF-8');
                $title=preg_replace('/"/',"'",ucwords($key));
                $key2=trunchaine(ucwords($key),50);
                $keyAff=preg_replace('/ /',"&nbsp;",$key2);
		$titlesansaccent=$keysansaccent=recuplibellesansaccent($idMatiere);
                $titlesansaccent=preg_replace('/"/',"'",$keysansaccent);
                $keysansaccent=stripslashes($keysansaccent);

		/* Lien matière : affiche le prof au survol */
		$reponse.="<tr class='cc-tr-data'>";
		$reponse.="<td><a href='javascript:void' class='nv-subj' onmouseover=\"document.formnote.note.value='Enseigné par $prof'; return true;\" onmouseout=\"document.formnote.note.value='';\" title='$titlesansaccent'>$keyAff</a></td>";
		$reponse.="<td><div class='nv-notes-cell'>";

		if (empty($value)) {
			$reponse.="<span class='nv-empty'>Aucune note ce mois</span>";
		}

		for($i=0;$i<countTriade($value);$i++){
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
			$moyendevoir = $moyenne['moy'] ?? null;
			$mindevoir = $moyenne['min'] ?? null ;
			$maxdevoir = $moyenne['max'] ?? null ;

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

			if ($examen != "") {
				$mess.="<font color=\'blue\' >Examen :</font> $examen";
			}

			if (trim($typenote) != "en") {
				// $noteaff=preg_replace('/.00$/','',$noteaff);
			}

			/* Deuxième passe (conservée telle quelle pour la logique originale) */
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

			/* Classe CSS selon la valeur de la note */
			$noteClass = 'nv-note';
			if     ($noteaff === 'abs')                         $noteClass .= ' nv-note-abs';
			elseif ($noteaff === 'disp')                        $noteClass .= ' nv-note-disp';
			elseif (in_array($noteaff, ['DNN','DNR']))          $noteClass .= ' nv-note-dnn';
			elseif ($noteaff === 'VAL')                         $noteClass .= ' nv-note-val';
			elseif ($noteaff === 'NVAL')                        $noteClass .= ' nv-note-nval';
			if ($examen != '')                                  $noteClass .= ' nv-note-exam';

			if ($noteaff === '') { continue; }

			$information="Information";
			$reponse.="<a href=\"javascript:void\" onmouseover=\"document.formnote.note.value='".$text2."'; return true;  \" onmouseout=\"document.formnote.note.value='';\"  title='Cliquer pour information' onClick=\"posX=event.pageX; posY=event.pageY; AffBulleAvecQuit('$information','./image/commun/info.jpg','$mess'); window.status=''; return true;\"  ><span class=\"$noteClass\">".$noteaff."</span></a> ";
		}
		$reponse.="</div></td></tr>";
	    }
	} else {
		$reponse.="<tr><td colspan='2' class='nv-empty' style='text-align:center;padding:16px'>Aucune note pour ce mois.</td></tr>";
	}

	$reponse.="</tbody></table></div>";
	$reponse.="</div></form>";

	print $reponse;

	Pgclose();
}
?>
