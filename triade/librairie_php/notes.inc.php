<?php



class Note {
	var $note_id; 		// val de sequence dans base
	var $elev_id; 		// elev_id
	var $note; 	// valeur
	var $nom_elev;

	function Note($nom_elev,$elev_id,$note,$note_id=false){
		$this->note_id=$note_id;
		$this->elev_id=$elev_id;
		$this->nom_elev=$nom_elev;
		switch(trim($note)){
		case 'NVAL':
			$this->note='-7';
			break;
		case 'VAL':
			$this->note='-6';
			break;
		case 'DNR':
			$this->note='-5';
			break;
		case 'DNN':
			$this->note='-4';
			break;
		case 'disp':
			$this->note='-2';
			break;
		case 'abs':
			$this->note='-1';
			break ;
		case ' ':
			$this->note='-3';
			break;
		case '':
			$this->note='-3';
			break;
		default :
			$this->note=$note;
		}
	}

	function setNoteId($note_id){
		$this->note_id=$note_id;
	}

	function setElevId($elev_id){
		$this->elev_id=$elev_id;
	}

	function setNote($val) {
		$this->note=$val;
	}

	function setNomElev($nom){
		$this->nom_elev=$nom;
	}
	function getNoteId(){
		return $this->note_id;
	}

	function getElevId(){
		return $this->elev_id;
	}

	function getNote(){
		return $this->note;
	}

	function getNomElev() {
		return $this->nom_elev;
	}

}

class ListeNotes {
	var $lid ; // non stocké , utilitaire
	var $prof_id;
	var $code_mat;
	var $coef;
	var $ndate;
	var $sujet;
	var $notes; // contenu array objets note
	var $idcl; // id classe
	var $idgrp; // id groupe
	var $typenote; // notation en mode usa, french
	var $noteExam;
	var $notationSur;
	var $notevisiblele;

	function ListeNotes($lid,$prof_id,$code_mat,$coef,$date,$sujet,$notes,$idcl,$idgrp,$typenote,$noteExam,$notationSur,$notevisiblele){
		$this->lid=$lid;
		$this->prof_id=$prof_id;
		$this->code_mat=$code_mat;
		$this->coef=$coef;
		// deb. date
		$date=explode("/",$date);
		$date=$date[2]."-".$date[1]."-".$date[0];
		// fin date
		$this->ndate=$date;
		$this->sujet=$sujet;
		$this->notes=$notes;
		$this->idcl=$idcl;
		$this->idgrp=$idgrp;
		$this->typenote=$typenote;
		$this->noteExam=$noteExam;
		$this->notationSur=$notationSur;
		$this->notevisiblele=$notevisiblele;
	}


function persist(){
	global $cnx;
	global $prefixe;

$sqlD=<<<SQL
INSERT INTO ${prefixe}notes(
		elev_id,
		prof_id,
		code_mat,
		coef,
		date,
		sujet,
		note,
		id_classe,
		id_groupe,
		typenote,
		noteexam,
		notationsur,
		notevisiblele)
VALUES (
SQL;
$sqlF=")";
		execSql("BEGIN");
		$n=$this->notes;
		for($i=0;$i<count($n);$i++){
			$sql  = $sqlD;
			$sql .= "'".$n[$i]->getElevId()."',";
			$sql .= "'".$this->prof_id."',";
			$sql .= "'".$this->code_mat."',";
			$sql .= "'".$this->coef."',";
			$sql .= "'".$this->ndate."',";
			$sql .= "'".$this->sujet."',";
			$sql .= "'".$n[$i]->getNote()."',";
			$sql .= "'".$this->idcl."',";
			$sql .= "'".$this->idgrp."',";
			$sql .= "'".$this->typenote."',";
			$sql .= "'".$this->noteExam."',";
			$sql .= "'".$this->notationSur."',";
			$sql .= "'".dateFormBase($this->notevisiblele)."'";
			$sql .= $sqlF ;
			if( ! execSql($sql) ){
				$flag=false;
				break;
			}else{
				$flag=true;
				continue;
			}
		}
		if($flag):
			execSql("COMMIT");
			return true;
		else:
			execSql("ROLLBACK");
			alertJs('Attention !! Enregistrement Interrompu \n\n Service Triade');
			return false;
		endif;
	
}


	function affHtmlVATEL() {
		global $cnx;
		global $prefixe;
		$n=$this->notes;
		$sujetdevoir=$this->sujet;
		print "<br />";
		print "<ul>";
		print "<img src='images/imgV1.png' align='center' width='100'  height='100' />&nbsp;&nbsp;<font class='colorText' >Sujet : </font><b>".stripslashes($this->sujet)."</b>";
		print "<br/><br/>\n";
		print "<table border='1' style=\"border:1px solid white;\"  >\n";
		
		print "<tr><td align='right' bgcolor='#000000' bordercolor='#FFFFFF' >&nbsp;&nbsp;<font color='#FFFFFF' >".LANGELE2." ".LANGELE3."</font>&nbsp;&nbsp;</td>";
		print "<td bgcolor='#000000' bordercolor='#FFFFFF' >&nbsp;&nbsp;<font color='#FFFFFF' >".LANGCARNET24."</font>&nbsp;&nbsp;</td></tr>";
		
		
		for($i=0;$i<count($n);$i++){
			print "<tr>\n";
			
			$color=($color=="#87C1E6") ? $color="#BCDAF0" : $color="#87C1E6" ;
			
			print "<td align='right' bgcolor='$color' bordercolor='#FFFFFF' >&nbsp;&nbsp;<font class='#000000'>".$n[$i]->getNomElev()."</font>&nbsp;&nbsp;</td>";
			$not=$n[$i]->getNote();
			
			switch($not){
				case -1 :
					$not='abs';
					break;
				case -2 :
					$not='disp';
					break;
				case -3 :
					$not='';
					break;
				case -4 :
					$not='DNN';
					break;
				case -5 :
					$not='DNR';
					break;
				case -6 :
					$not='VAL';
					break;
				case -7 :
					$not='NVAL';
					break;
				default:
					$moy += $not;
					$nbNotes++;
			}
			$notaff=$not;

			if ($this->typenote == "en") {
					if (trim($notaff) != "" ) {
						$sql="SELECT  libelle,min,max FROM ${prefixe}config_note_usa WHERE  min <= '$not' AND  max >= '$not' ";
						$res=execSql($sql);
							$data=chargeMat($res);
						if (count($data) > 0) {
							$notaff=$data[0][0];
						}
					}
			}
			
			print "<td  bgcolor='$color' bordercolor='#FFFFFF' ><font class='#000000'>&nbsp;".$notaff."&nbsp;&nbsp;</font></td></tr>\n";

			if (($not >= 0) && (is_numeric($not))) {
				$notEntier=intval($not);
				$noteTab1[$notEntier]++;
			}

		}
		@ksort($noteTab1);
		foreach ($noteTab1 as $cle => $value) {
			$noteTab2[]=$cle;
			$Nbnote2[]=$value;
		}

		$noteTab=@implode(",",$noteTab2);
		$Nbnote=@implode(",",$Nbnote2);
		$typenoteg=$this->typenote;

		if ($nbNotes == 0) {
			$color=($color=="#87C1E6") ? $color="#BCDAF0" : $color="#87C1E6" ;
			print "<tr><td align='right' bgcolor='$color' bordercolor='#FFFFFF' >&nbsp;<b>Moyenne </b> : </td><td bgcolor='$color' ></td></tr>";


		}else {
			$not=round($moy/$nbNotes,2);
			if ($this->typenote == "en") {
					$sql="SELECT  libelle,min,max FROM ${prefixe}config_note_usa WHERE  min <= '$not' AND  max >= '$not' ";
					$res=execSql($sql);
					$data=chargeMat($res);
					if (count($data) > 0) {
						$not=$data[0][0];
					}
			}
			$color=($color=="#87C1E6") ? $color="#BCDAF0" : $color="#87C1E6" ;
			print "<tr><td align='right'  bgcolor='$color' bordercolor='#FFFFFF' >&nbsp;<b>Moyenne </b>&nbsp;:&nbsp;</td><td  bgcolor='$color' >&nbsp;".$not."&nbsp;</td></tr>";


		}
			print "</table>\n";
			print "</ul>";
			$sujetdevoir=preg_replace('/"/','',$sujetdevoir);
			$sujetdevoir=addslashes($sujetdevoir);
			$notationsur=$this->notationSur;
			//print "<br><script language=JavaScript>buttonMagic(\"Statistiques de ce devoir\",\"grap_matiere.php?notesur=$notationsur&id=$noteTab&nomdudevoir=$sujetdevoir&nombre=$Nbnote&typenote=$typenoteg\",\"\",\"height=300,width=650,scrollbars=no,status=no\",\"\");</script>";
			print "<br><ul><input type='button' class='btn btn-primary btn-sm  vat-btn-footer' value=\"".LANGVATEL27."\" name='rien' onClick=\"open('grap_matiere.php?notesur=$notationsur&id=$noteTab&nomdudevoir=$sujetdevoir&nombre=$Nbnote&typenote=$typenoteg','fen','height=300,width=650,scrollbars=no,status=no')\" >";
			print "</ul><br>";
	}




function affHtml(){
	global $cnx;
	global $prefixe;
	$n=$this->notes;
	$sujetdevoir=$this->sujet;
	print "<br />";
	print "<ul>";
	print "Sujet : <b>".stripslashes($this->sujet)."</B>";
	print "<BR><BR>\n";
	print "<table border=\"1\" style=\"border-collapse: collapse;\" >\n";
	for($i=0;$i<count($n);$i++){
		print "<tr>\n";
		$infoAffiche=$n[$i]->getNomElev();
		$infoAffiche=preg_replace('/\\\\/','',$infoAffiche);
		print "<td bgcolor='#FFFFFF'>".stripslashes($infoAffiche)."</td>";
		$not=$n[$i]->getNote();
		
		switch($not){
			case -1 :
				$not='abs';
				break;
			case -2 :
				$not='disp';
				break;
			case -3 :
				$not='';
				break;
			case -4 :
				$not='DNN';
				break;
			case -5 :
				$not='DNR';
				break;
			case -6 :
				$not='VAL';
				break;
			case -7 :
				$not='NVAL';
				break;
			default:
				$moy += $not;
				$nbNotes++;
		}
		$notaff=$not;

		if ($this->typenote == "en") {
				if (trim($notaff) != "" ) {
					$sql="SELECT  libelle,min,max FROM ${prefixe}config_note_usa WHERE  min <= '$not' AND  max >= '$not' ";
					$res=execSql($sql);
				    	$data=chargeMat($res);
					if (count($data) > 0) {
						$notaff=$data[0][0];
					}
				}
		}
		
		print "<td bgcolor='#FFFFFF'>&nbsp;&nbsp;".$notaff."&nbsp;&nbsp;</td>";
		print "</tr>\n";

		if (($not >= 0) && (is_numeric($not))) {
			$notEntier=intval($not);
			$noteTab1[$notEntier]++;
		}

	}
	@ksort($noteTab1);
	foreach ($noteTab1 as $cle => $value) {
		$noteTab2[]=$cle;
		$Nbnote2[]=$value;
	}

	$noteTab=@implode(",",$noteTab2);
	$Nbnote=@implode(",",$Nbnote2);
	$typenoteg=$this->typenote;

	if ($nbNotes == 0) {
		print "<tr><td>&nbsp;&nbsp;<b>Moyenne </b>&nbsp;&nbsp;</td><td></td></tr>";


	}else {
		$not=round($moy/$nbNotes,2);
		if ($this->typenote == "en") {
				$sql="SELECT  libelle,min,max FROM ${prefixe}config_note_usa WHERE  min <= '$not' AND  max >= '$not' ";
				$res=execSql($sql);
			    $data=chargeMat($res);
				if (count($data) > 0) {
					$not=$data[0][0];
				}
		}
		print "<tr><td><b>Moyenne </b></td><td>&nbsp;".$not."&nbsp;</td></tr>";


	}
		print "</table>\n";
		print "</ul>";
		$sujetdevoir=preg_replace('/"/','',$sujetdevoir);
		$sujetdevoir=addslashes($sujetdevoir);
		$notationsur=$this->notationSur;
		print "<br><script language=JavaScript>buttonMagic(\"Statistiques de ce devoir\",\"grap_matiere.php?notesur=$notationsur&id=$noteTab&nomdudevoir=$sujetdevoir&nombre=$Nbnote&typenote=$typenoteg\",\"\",\"height=300,width=650,scrollbars=no,status=no\",\"\");</script>";
		print "<br><br>";
	}
}
?>
