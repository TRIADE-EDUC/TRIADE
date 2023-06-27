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

$triEleve=$_POST["trier"];
setcookie("tri_eleve",$triEleve,time()+36000*24*30);

include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php"); // futur : auto_prepend_file
include_once("./librairie_php/db_triade.php");
include_once("./common/config2.inc.php");

if ((VIESCOLAIRENOTEENSEIGNANT == "oui") && ($_SESSION["membre"] != "menupersonnel"))  {
	validerequete("3");
}else{
	if (($_SESSION["membre"] != "menuadmin") && ($_SESSION["membre"] != "menuprof")) {
		$cnx=cnx();
		if (!verifDroit($_SESSION["id_pers"],"carnetnotes")) {
			accesNonReserveFen();
			exit();
		}
		Pgclose();
	}else{
		validerequete("profadmin");
	}
}

$cnx=cnx();

// Sn : variable de Session nom
// Sp : variable de Session prenom
// Sm : variable de Session membre
// Spid : variable de Session pers_id
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
$ident=array('sClasseGrp','cgrp','sMat','mid','sNbNote','nbNote');
$HPV=hashPostVar($ident);
unset($ident);
$listTmp=explode(":",$HPV[cgrp]);
unset($HPV[cgrp]);
$HPV[cid]=$listTmp[0];
$HPV[gid]=$listTmp[1];
unset($listTmp);
//print_r($HPV);

$notationSur=$_POST["NotationSur"];


if ($_POST["NoteUsa"] == "oui") {
	$notetype="Notation en mode USA";
	$noteusa="oui";
}else{
	$notetype="Notation sur $notationSur";
	$noteusa="non";
}



if (isset($_POST["NoteExam"])) {
	$noteExamen=$_POST["NoteExam"];
	if ($noteExamen == "aucun") {
		$noteExamen="";
	}
}

$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
	$anneeScolaire=$_POST["anneeScolaire"];
}

if($HPV[gid]):
	$verif=verifProfDansGroupe($_SESSION["id_pers"],$HPV[gid]);
	if (($_SESSION["membre"] == "menuprof") && (!isset($_SESSION["profpclasse"]))) {
		if ($verif) { blacklist(); }
	}
	$who="<font color=\"#FFFFFF\">- groupe : </font> ".trunchaine(chercheGroupeNom($HPV[gid]),10)." <font color='#FFFFFF'>-</font> $notetype ";
else:
	$cl=chercheClasse($HPV[cid]);
	$verif=verifProfDansClasse($_SESSION["id_pers"],$cl[0][0]);
	if (($_SESSION["membre"] == "menuprof") && (!isset($_SESSION["profpclasse"]))) {
		if ($verif) { blacklist(); }
	}
	$who="<font color=\"#FFFFFF\">- classe : </font>".trunchaine(ucfirst($cl[0][1]),10)." <font color='#FFFFFF'>-</font> $notetype ";
	unset($cl);
endif;
?>
<HTML>
<HEAD>
<title>Enseignant - Triade - Compte de <?php print ucwords($mySession[Sp])." ".strtoupper($mySession[Sn])?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_cal1.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/prototype.js"></script>
<script language="JavaScript" src="./librairie_js/ajax-note.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();myInit()"  >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="librairie_js/<?php print $_SESSION["membre"] ?>.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="librairie_js/<?php print $_SESSION["membre"] ?>1.js"></SCRIPT>
<script>
var errfound=false;
var force=0;
</script>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGPROFB?> </b><font id='color2' ><?php print trunchaine(chercheMatiereNom($HPV[mid]),25)." ".$who?></font></font></td>
</tr>
<tr id='cadreCentral0'>
<td>
<BR>
<table width='100%'><tr><td valign='top'  align='left'  >
<?php
include_once("./librairie_php/lib_conexpersistant.php"); 
connexpersistance("color:black;font-weight:bold;font-size:11px;text-align: center;"); 
?>
</td><td  valign='top' align='right' >
<?php 
if ($_SESSION["membre"] == "menuadmin") {
	print "<form method='post' action='notevisuadmin.php' target='_parent' >";
	print "<div><script language='JavaScript'>buttonMagicSubmitAtt('".LANGMESS75."','create','');</script></div>";
	print "<input type='hidden' name='saisie_pers' value='".$_POST["adminIdprof"]."' /></form><br><br>";
} 


if (anneeScolaireViaIdClasse($HPV[cid]) == $anneeScolaire) { 
	$table="eleves";
}else{
	$table="eleves_histo";
}
?>
</td></tr></table>

<form method="POST" action="noteajout3.php" onSubmit="return valide_note()" name="form11">
<?php
$info_nav=$_SESSION["navigateur"];
$sizenote=4;
if ($info_nav == "IE") { $sizenote=3; }
for($i=0;$i<$HPV[nbNote];$i++){
	print "<div>";
	if ($noteExamen != "") {
		print LANGNOTE1." ".($i+1)." : ";
	}else{
		print LANGPROF7." ".($i+1)." : ";
	}
	if (VERIFSUJETNOTE == "oui") { 
		$verif="oui";
	}else{
		$verif="non";
	}
	print htmlFormTextNoteAjout("iSujet[$i]",'',15,30,$i,$HPV[cid],$HPV[gid],$HPV[mid],$verif);
	print strtolower(LANGPER19)." : ";


if (COEFF == 1) { $valcoef="1"; }

// voir aussi bulletin_construction0305.php
if (ISMAPP == 1) {
	$valcoef="1";
	if ($noteExamen == "CC") 	  { $valcoef="1"; }
	if ($noteExamen == "DST") 	  { $valcoef="2"; }
	if ($noteExamen == "Partiel") 	  { $valcoef="3"; }
	if ($noteExamen == "Soutenance") { $valcoef="2"; }
	if ($noteExamen == "Rapport") 	  { $valcoef="2"; }
	if ($noteExamen == "Fiche de lecture") { $valcoef="2"; }
	if ($noteExamen == "Exposé")     { $valcoef="1"; }
 	if ($noteExamen == "Dad")        { $valcoef="1"; }
 	if ($noteExamen == "Lecture")        { $valcoef="3"; }
 	if ($noteExamen == "Examen écrit")        { $valcoef="2"; }
 	if ($noteExamen == "Recopiage vocabulaire")        { $valcoef="1"; }
 	if ($noteExamen == "Evaluation Tutorat")        { $valcoef="2"; }
 	if ($noteExamen == "Mémoire Ip")        { $valcoef="2"; }
}

if (VATEL == 1) { $valcoef="1"; }


$dateDuJour=dateDMY();

 print htmlFormText("iCoef[$i]","$valcoef",2,4);
 print strtolower(LANGTE7). " : " ;
 print htmlFormTextDateNoteAjout("iDate$i",$dateDuJour,10,12,$i,$HPV[cid],$HPV[gid]);
 print "<input type=hidden name=\"iDate[$i]\" \>";
 //print htmlFormText("iDate[$i]",dateDMY(),10,12);
 include_once("librairie_php/calendar.php");
 calendar("id1$i","document.form11.iDate$i",$_SESSION["langue"],"0");
 print "</div>";
}
?>
<hr />
<div name="info" id="info"></div>
<br><?php print LANGPROFE?></i><br><br>
<?php
if ((isset($_POST["NoteUsa"])) && ($_POST["NoteUsa"] == "oui")  ) {
	print "&nbsp;&nbsp;".LANGNOTEUSA6.".";
	$list_cor="";
	$datalist=aff_config_note_usa();
	// id,libelle,min,max
	for($i=0;$i<count($datalist);$i++) {
		$list_cor.="<font class=T2> De ".$datalist[$i][2]." à ".$datalist[$i][3]." équivaut à  ".$datalist[$i][1]."</font><br>";
	}
	print "&nbsp;<a href='#' onMouseOver=\"AffBulle3('".LANGMESS76."','./image/commun/info.jpg','".$list_cor."');\"  onMouseOut='HideBulle()'; ><img src='./image/help.gif' border='0' align='center'></a>";
	print "<br /><br />";
}

if ($triEleve == "classe") {
	$order="ORDER BY 3,2";
}elseif($triEleve == "nomEleve") {
	$order="ORDER BY 2";
}elseif($triEleve == "Matricule") {
	$order="ORDER BY CAST(e.numero_eleve AS CHAR)";
}else{
	$order="ORDER BY 2";
}


if($HPV[gid]){
        $gid=$HPV[gid];
        $sqlIn=<<<SQL
        SELECT
        	liste_elev
        FROM
        	${prefixe}groupes
        WHERE
        	group_id='$gid'
SQL;
        $curs=execSql($sqlIn);
        $in=chargeMat($curs);
        freeResult($curs);
        $in=$in[0][0];
	$in=substr($in,1);
	$in=substr($in,0,-1);
	if (trim($in) == "") {
		$sql="SELECT e.elev_id, CONCAT( upper(trim(e.nom)),' ',trim(e.prenom) ) ,e.classe, e.numero_eleve  FROM ${prefixe}eleves e WHERE e.compte_inactif != 1 AND e.elev_id='' $order";
		unset($in);		
	}else{
		$sql="SELECT e.elev_id, CONCAT( upper(trim(e.nom)),' ',trim(e.prenom) ) ,e.classe, e.numero_eleve  FROM ${prefixe}eleves e WHERE e.compte_inactif != 1 AND e.elev_id IN ($in) $order";
		unset($in);
	}
}else{
        $cid=$HPV[cid];

//	$sql="(SELECT e.elev_id, CONCAT( upper(trim(e.nom)),' ',trim(e.prenom) ),e.classe  FROM ${prefixe}eleves e WHERE e.classe='$cid' AND e.compte_inactif != 1 ) UNION (SELECT e.elev_id, CONCAT( upper(trim(e.nom)),' ',trim(e.prenom) ) , e.classe  FROM ${prefixe}eleves e , ${prefixe}eleves_histo h WHERE h.idclasse='$cid' AND e.compte_inactif != 1 AND e.elev_id=h.ideleve  group by elev_id ) $order";

	$sql=" SELECT s.* FROM ( SELECT elev_id,CONCAT(upper(trim(nom)),' ',trim(prenom)),classe FROM ${prefixe}eleves, ${prefixe}classes  WHERE classe='$cid' AND code_class=classe AND annee_scolaire='$anneeScolaire' AND compte_inactif != 1 UNION ALL SELECT e.elev_id,CONCAT( upper(trim(e.nom)),' ',trim(e.prenom) ),e.classe FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$cid' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire') s  ORDER BY 2";

/*
	if ($table == "eleves") {
		$sql="SELECT e.elev_id, ";
       		$sql.=" CONCAT( upper(trim(e.nom)),' ',trim(e.prenom) ) ";
		$sql.=",e.classe  FROM ${prefixe}eleves e WHERE e.classe='$cid' AND e.compte_inactif != 1 $order";
	}else{
		$sql="SELECT e.elev_id, ";
       		$sql.=" CONCAT( upper(trim(e.nom)),' ',trim(e.prenom) ) ";
		$sql.=",e.classe  FROM ${prefixe}eleves e , ${prefixe}eleves_histo h WHERE h.idclasse='$cid' AND e.compte_inactif != 1 AND e.elev_id=h.ideleve  $order";

	}
*/
        unset($cid);
}
        $curs=execSql($sql);
        unset($sql);
        $mat=chargeMat($curs);
        freeResult($curs);
        unset($curs);
	print "<table>\n";


		print htmlFormHidden("gid",$HPV[gid]);
		print htmlFormHidden("cid",$HPV[cid]);
		print htmlFormHidden("mid",$HPV[mid]);
		$nbelem=$HPV[nbNote] * 4 + 4;
		$afficheClasse="";
		for($i=0;$i<count($mat);$i++){
			$nbelem=$nbelem + 2;
		
			$photoeleve="image_trombi.php?idE=".$mat[$i][0];

	        	print htmlFormHidden("elev_id[$i]",$mat[$i][0]);
			print htmlFormHidden("elev_nom[$i]",$mat[$i][1]);
			if ($triEleve == "classe") {
				$idClasseA=chercheIdClasseDunEleve($mat[$i][0]);
				if ($idClasseA != $afficheClasse) {
					$afficheClasse=$idClasseA;
					print "<tr><td><font class='T2' color='blue'> ".LANGELE4." : <b>".chercheClasse_nom($afficheClasse)."</b></font></td></tr>";
				}
			}

			if ($_SESSION["membre"] == "menuadmin") {
				$numeroEleve=chercheNumeroEleve($mat[$i][0]);
				$matricule="</td><td><i>$numeroEleve</i>";
	
			}

			if ($HPV[gid] != '0') {
				$idClasseA=chercheIdClasseDunEleve($mat[$i][0]);
				$nomClasse="(".chercheClasse_nom($idClasseA).")";
			}else{
				$nomClasse="";
			}
	

			print "<tr class=\"tabnormal\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\"  >\n";
			$nbcaractNom=80;
			if ($HPV[nbNote] == 3) { $nbcaractNom=20; }
			if ($HPV[nbNote] == 2) { $nbcaractNom=40; }
			$infoProba=getProbaEleve($mat[$i][0]);
		        if ($infoProba == 1) {
                		$infoprobatoire="<img src='image/commun/important.png' title=\"En p&eacute;riode probatoire !!\" />";
		        }else{
                		$infoprobatoire="";
		        }

			print "<td> $infoprobatoire   <a href='#' onMouseOver=\"AffBulle('<img src=\'$photoeleve\' >');\"  onMouseOut='HideBulle()'>".trunchaine($mat[$i][1],$nbcaractNom)."</a> <font size='1'>$nomClasse</font> $matricule</td>\n";
			
			for($j=0;$j<$HPV[nbNote];$j++){
				print "<td><select onchange=chNote('".$nbelem."')>";
				//print "<option value='' selected STYLE='color:#000066;background-color:#FCE4BA'>Note</option>";
				//print "<option value='abs' STYLE='color:#000066;background-color:#CCCCFF'>Abs</option>";
				//print "<option value='disp' STYLE='color:#000066;background-color:#CCCCFF'>Disp</option>";

				print "<option value='' selected STYLE='color:#000066;background-color:#FCE4BA'>".LANGPROF8."</option>";
				print "<option value='abs' title='Absent' STYLE='color:#000066;background-color:#CCCCFF'>".LANGABS15."</option>";
				print "<option value='disp' title='Dispensé' STYLE='color:#000066;background-color:#CCCCFF'>".LANGABS30."</option>";
				print "<option value='DNR' title='Devoir non rendu' STYLE='color:#000066;background-color:#CCCCFF'>DNR</option>";
				print "<option value='DNN' title='Devoir non noté' STYLE='color:#000066;background-color:#CCCCFF'>DNN</option>";
				print "<option value='VAL' title='Devoir validé' STYLE='color:#000066;background-color:#CCCCFF'>VAL</option>";
				print "<option value='NVAL' title='Devoir non validé' STYLE='color:#000066;background-color:#CCCCFF'>NVAL</option>";
				print "</td>";
				print "<td>".htmlFormText2("iNotes[$i][$j]",'',$sizenote,6)."</td>\n";
				$nbelem=$nbelem + 2;
			}
			print "</tr>\n";
        }
		print "</table>\n";
?>
<hr />
<!----------------------------------------------------->
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<!-- <input type=checkbox name=valideSaisie onclick="forceValeur();" id='btradio1' > -->
<input  type='checkbox' name=valideSaisie onclick="forceValeur();document.form11.validation.disabled=true;verifSujetNote();AfficheTrimestre();" id='btradio1' > 
<label for="btradio1"><font color=red><?php print LANGPROFC?></font></label> <?php if ($noteExamen != "") { print "<b>&nbsp;&nbsp;( ".LANGDISC60." : $noteExamen )</b>"; } ?> <br /><br/ >
<div id="infoTri1" style="display:none;"></div>

&nbsp;&nbsp;&nbsp;&nbsp;<br /><input type="submit" name="validation" class="button" value="<?php print LANGPROFD?>" /> (<i>Ann&eacute;e Scolaire <?php print $anneeScolaire ?></i>) <br /><br />
<input type=hidden name="NoteExam" value="<?php print $noteExamen ?>" />
<input type=hidden name="NoteUsa" value="<?php print $_POST["NoteUsa"] ?>" />
<input type=hidden name="notevisiblele" value="<?php print $_POST["notevisiblele"] ?>" />
<input type=hidden name="NotationSur" value="<?php print $_POST["NotationSur"] ?>" />
<input type=hidden name="saisie_pers" value="<?php print $_POST["saisie_pers"] ?>" />
<script language=JavaScript>



<?php  if (VERIFSUJETNOTE == "oui") {  ?>

var deja=0;	
function AfficheTrimestre() {
	var nbnotereel=<?php print $HPV[nbNote]?>;
	if (deja == 1) {
		for (m=1;m<=nbnotereel;m++) {
			var element = document.getElementById("newSpan"); 
			var parent = element.parentNode;
			parent.removeChild(element);  
		}
	}

	if ((nbnotereel == 1) || (nbnotereel == 2) || (nbnotereel == 3)) {
		var dateT1=document.form11.elements[2].value;
		AfficheTrimestreAjax(dateT1,'<?php print $HPV[cid] ?>',"infoTri1");
	}	
	if ( (nbnotereel == 2) || (nbnotereel == 3)) {
		var dateT2=document.form11.elements[6].value;
		AfficheTrimestreAjax(dateT2,'<?php print $HPV[cid] ?>',"infoTri1");
	}

	if  (nbnotereel == 3) {
		var dateT3=document.form11.elements[10].value;
		AfficheTrimestreAjax(dateT3,'<?php print $HPV[cid] ?>',"infoTri1");
	}

	deja=1;
}

function verifSujetNote() {
	var nbnotereel=<?php print $HPV[nbNote]?>;
	var ii=2;
	var aa=0;
	for (i=1;i<=nbnotereel;i++) {
		var date=document.form11.elements[ii].value;
		var sujet=document.form11.elements[aa].value;
		ii=ii+4;
		aa=aa+4;
		verifSujet(sujet,date,<?php print $HPV[cid] ?>,<?php print $HPV[gid] ?>,<?php print $HPV[mid] ?>);
	}
	var nbnotereel=<?php print $HPV[nbNote]?>;
	if (nbnotereel > 1) {
		var a=0;
		sujet1=document.form11.elements[a].value;
		if (nbnotereel == 2) {
			a=a+4
			sujet2=document.form11.elements[a].value;
			if (sujet2 == sujet1){
				errfound=true;
				alert("<?php print LANGALERT4 ?>");
				document.form11.valideSaisie.checked=false;
				force=0;
			}
		}
		if (nbnotereel == 3) {
			a=a+4
			sujet2=document.form11.elements[a].value;
			a=a+4
			sujet3=document.form11.elements[a].value;
			if ((sujet2 == sujet1) || (sujet2 == sujet3) || (sujet1 == sujet3)){
				errfound=true;
				alert("<?php print LANGALERT4 ?>");		
				document.form11.valideSaisie.checked=false;
				force=0;
			}
		}		
	}
}
<?php }else{  ?>
function verifSujetNote() {
	// aucun traitement
	document.form11.validation.disabled=false;
}
<?php } ?>


var force=0;
function  forceValeur() {
	if (force == 0 ) {force=1;} else { force=0; }
}

function ValidCaractere(nom) {
	var dernier = nom.lenght ;
       	var slach1  = nom.charAt(2);
	var slach2  = nom.charAt(5);
	var jour = nom.substring(0,2);
	var mois = nom.substring(3,5);
	var annee = nom.substring(6,10);
	if (isNaN(jour)) { return false }
	if (isNaN(mois)) { return false }
	if (isNaN(annee)) { return false }
	if  ((annee < 2000) || (jour > 31) || (mois > 12) || (slach1 != '/') || (slach2 != '/')){
		return false
	}
	else {
		return true
	}

}


//fonction de validation d'après la longueur de la chaîne
function ValidLongueur(item,len) {
	return (item.length >= len);
}


// affiche un message d'alerte
function error(elem, text) {
// abandon si erreur déjà signalée
   if (errfound) return;
   window.alert(text);
   elem.select();
   elem.focus();
   errfound = true;
}

function ValidNumeric(item,item1){
	if (!ValidLongueur(item,1)) return false;
	return !isNaN(item1.value);
}


function trans_date(nb) {
	val=eval('document.form11.iDate'+nb+'.value');
	if (nb == 0) {
		nb = nb + 3;
		document.form11.elements[nb].value=val;
	}
	if (nb == 1) {
                nb = nb + 6;
		document.form11.elements[nb].value=val;
        }
	if (nb == 2) {
                nb = nb + 9;
		document.form11.elements[nb].value=val;
        }

}

function valide_note() {


<?php
if ($HPV[nbNote] == 1) {
	print "trans_date(0);";
}elseif ($HPV[nbNote] == 2) {
	print "trans_date(0);";
	print "trans_date(1);";
}else {
	print "trans_date(0);";
	print "trans_date(1);";
	print "trans_date(2);";
}

if ($noteusa == "oui") {
	print "var noteusa=1;";
}else{
	print "var noteusa=0;";
}

?>

var errfound=false;






if (force  != 1) {
var notationsur=<?php print $notationSur?>;
var nbnotereel=<?php print $HPV[nbNote]?>;
var aa=0;
var nbnote=<?php print $HPV[nbNote] * count($mat) + $HPV[nbNote] * 4 - 1  + 3 + 2 * count($mat) + count($mat) * $HPV[nbNote]  ?>;
var a=<?php print $HPV[nbNote] * 4  + 3 + 2 + 1  ?>;
for ( a ; a <= nbnote ; a++ ) {
		if  (document.form11.elements[a].value.length < 1) {
			document.form11.elements[a].select();
			document.form11.elements[a].focus();
			errfound=true;
			document.form11.elements[a].value=" ";
			break;
		}

	if (noteusa == 0) {   // note sur 20,10,5
		if  (document.form11.elements[a].value < 0  ||  document.form11.elements[a].value > notationsur ) {
			alert(langfunc68+" "+notationsur);
			document.form11.elements[a].select();
			document.form11.elements[a].focus();
			errfound=true;
			break;
		}
	}else{  // note sur 100
		if  (document.form11.elements[a].value < 0  ||  document.form11.elements[a].value > 100 ) {
				alert(langfunc68bis + " 100");
				document.form11.elements[a].select();
				document.form11.elements[a].focus();
				errfound=true;
				break;
		}
	}
		if (document.form11.elements[a].value.indexOf (',') != -1) {
			alert(langfunc69);
			document.form11.elements[a].select();
			document.form11.elements[a].focus();
			errfound=true;
			break;
		}
<?php if ( $_SESSION["navigateur"] == "IE" ) { ?>
		if (document.form11.elements[a].value.indexOf ('.') != -1) {
			pos=document.form11.elements[a].value.indexOf ('.')
			long=document.form11.elements[a].value.length;
			total=long - pos ;
			if (total > 3 ) {
				alert(langfunc70);
				document.form11.elements[a].select();
				document.form11.elements[a].focus();
				errfound=true;
				break;
			}
		}
<?php } ?>
		if (isNaN(document.form11.elements[a].value)) {
			if ((document.form11.elements[a].value != "abs")  && (document.form11.elements[a].value != "disp") && (document.form11.elements[a].value != "DNN") && (document.form11.elements[a].value != "DNR") && (document.form11.elements[a].value != "VAL") &&  (document.form11.elements[a].value != "NVAL")  ) {
				alert(langfunc71);
				document.form11.elements[a].select();
				document.form11.elements[a].focus();
				errfound=true;
				break;
			}
		}
		a++;


		if (nbnotereel == 1 ) {
			a=a+2;
		}
		if (nbnotereel == 2 ) {
			if (aa==0) {
				aa=1;
			}else {
				a=a+2;
				aa=0;
			}
		}
		if (nbnotereel == 3 ) {
			if ((aa==0) || (aa==1)) {
				if (aa==1) {
					aa=2;
				}
				if (aa==0) {
	 				aa=1;
				}
			}else {
				a=a+2;
				aa=0;
			}
		}
}
}


if (force == 1) {
	var notationsur=<?php print $notationSur?>;
	valide=1;
	if (valide) {
		j=0;
		force=1;
		for ( b=0 ; b < <?php print $HPV[nbNote]?> ; b++ ) {
			if (document.form11.elements[j].value.length < 3) {
			alert(langfunc72);
			document.form11.elements[j].select();
			document.form11.elements[j].focus();
			errfound=true;
			break;
			}
			j++;

			if (document.form11.elements[j].value.length < 1) {
			alert(langfunc73);
			document.form11.elements[j].select();
			document.form11.elements[j].focus();
			errfound=true;
			break;
			}
			if (document.form11.elements[j].value.indexOf (',') != -1) {
			alert(langfunc69);
			document.form11.elements[j].select();
			document.form11.elements[j].focus();
			errfound=true;
			break;
			}
<?php if ( $_SESSION["navigateur"] == "IE" ) { ?>
			if (document.form11.elements[j].value.indexOf ('.') != -1) {
			pos=document.form11.elements[j].value.indexOf ('.')
			long=document.form11.elements[j].value.length;
			total=long - pos ;
				if (total > 3 ) {
				alert(langfunc70);
				document.form11.elements[j].select();
				document.form11.elements[j].focus();
				errfound=true;
				break;
				}
			}
<?php } ?>
			if (isNaN(document.form11.elements[j].value)) {
			alert(langfunc74);
			document.form11.elements[j].select();
			document.form11.elements[j].focus();
			errfound=true;
			break;
			}
			j++;


			if (document.form11.elements[j].value.length != 10) {
			alert(langfunc75);
			document.form11.elements[j].select();
			document.form11.elements[j].focus();
			errfound=true;
			break;
			}

			if (!ValidCaractere(document.form11.elements[j].value)){
			alert(langfunc75);
			document.form11.elements[j].select();
			document.form11.elements[j].focus();
                        errfound=true;
			break;
			}
			j++;j++;



		}

		var nbnotereel=<?php print $HPV[nbNote]?>;
		var aa=0;
		var nbnote=<?php print $HPV[nbNote] * count($mat) + $HPV[nbNote] * 4 - 1  + 3 + 2 * count($mat) + count($mat) * $HPV[nbNote]?>;
		var a=<?php print $HPV[nbNote] * 4  + 3 + 2 + 1?>;

		for ( a ; a <= nbnote ; a++ ) {
			if  (document.form11.elements[a].value.length < 1) {
//			errfound=true;
			document.form11.elements[a].value=" ";
			break;
			}
		if (noteusa == 0) {   // note sur 20,10,5
			if  (document.form11.elements[a].value < 0  ||  document.form11.elements[a].value > notationsur ) {
			alert(langfunc68+" "+notationsur);
			document.form11.elements[a].select();
			document.form11.elements[a].focus();
			errfound=true;
			break;
			}
		}else {  // note sur 100
			if  (document.form11.elements[a].value < 0  ||  document.form11.elements[a].value > 100 ) {
			alert(langfunc68bis + " 100");
			document.form11.elements[a].select();
			document.form11.elements[a].focus();
			errfound=true;
			break;
			}
		}
			if (document.form11.elements[a].value.indexOf (',') != -1) {
			alert(langfunc69);
			document.form11.elements[a].select();
			document.form11.elements[a].focus();
			errfound=true;
			break;
			}
<?php if ( $_SESSION["navigateur"] == "IE" ) { ?>
			if (document.form11.elements[a].value.indexOf ('.') != -1) {
			pos=document.form11.elements[a].value.indexOf ('.')
			long=document.form11.elements[a].value.length;
			total=long - pos ;
				if (total > 3 ) {
				alert(langfunc70);
				document.form11.elements[a].select();
				document.form11.elements[a].focus();
				errfound=true;
				break;
				}
			}
<?php } ?>
			if (isNaN(document.form11.elements[a].value)) {
				if ((document.form11.elements[a].value != "abs")  && (document.form11.elements[a].value != "disp") && (document.form11.elements[a].value != "DNN") && (document.form11.elements[a].value != "DNR") && (document.form11.elements[a].value != "VAL") &&  (document.form11.elements[a].value != "NVAL") ) {
					alert(langfunc71);
					document.form11.elements[a].select();
					document.form11.elements[a].focus();
					errfound=true;
					break;
				}
			}

			a++;

			if (nbnotereel == 1 ) {
				a=a+2;
			}
			if (nbnotereel == 2 ) {
				if (aa==0) {
					aa=1;
				}else {
					a=a+2;
					aa=0;
				}
			}
			if (nbnotereel == 3 ) {
                        	if ((aa==0) || (aa==1)) {
                                	if (aa==1) {
                                        	aa=2;
                                	}
                               	 	if (aa==0) {
                                        	aa=1;
                                	}
                       	 	}else {
                                	a=a+2;
                  	              aa=0;
                        	}
                	}
		}
	}
}



if (force) {
	if (errfound == false) {
		document.form11.validation.disabled=true;
		document.getElementById('attenteDiv').style.visibility='visible';	
	}
	return !errfound;
}else {
	return false;

}

}

AfficheTrimestre();

</script>
<SCRIPT language="JavaScript">InitBulle("#000000","#CCCCCC","red",1);</SCRIPT>

<!---------------------------------------------------->
<input type=hidden name="adminIdprof" value="<?php print $_POST["adminIdprof"] ?>" />
</form>


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

<?php attente() ?>


</BODY>
</HTML>
<?php Pgclose() ?>
