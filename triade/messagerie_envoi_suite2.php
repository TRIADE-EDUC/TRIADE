<?php
session_start();
$idpiecejointe=md5($_SESSION["membre"].$_SESSION["id_pers"].date("YMDHms").rand(0,9999));
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
 **************************************************************************/
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<?php // <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script type="text/javascript" src="./librairie_js/function.js"></script>
<script type="text/javascript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/clickdroit2.js"></script>
<script type="text/javascript" src="./librairie_js/lib_verif_message.js"></script>
<script type="text/javascript" src="./librairie_js/ajax-messagerie.js"></script>
<script type="text/javascript" src="./librairie_js/ajax-recupsignature.js"></script>
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_proto_mail.js"></script>
<script language="JavaScript" src="./librairie_js/ajaxIA.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_actualisepiecejointe.js"></script>
<script type="text/javascript" src="./ckeditor/ckeditor.js"></script>
<script type="text/javascript">

function _(el) {
  return document.getElementById(el);
}



function uploadFile(idpiecejointe) {
        var file = _("Filedata").files[0];
        // alert(file.name+" | "+file.size+" | "+file.type);
        var formdata = new FormData();
        formdata.append("Filedata", file);
        var ajax = new XMLHttpRequest();
        ajax.upload.addEventListener("progress", progressHandler, false);
        ajax.addEventListener("load", completeHandler, false);
        ajax.addEventListener("error", errorHandler, false);
        ajax.addEventListener("abort", abortHandler, false);
        ajax.open("POST", "uploadmessagerie.php?idpiecejointe="+idpiecejointe); // http://www.developphp.com/video/JavaScript/File-Upload-Progress-Bar-Meter-Tutorial-Ajax-PHP
  	//use file_upload_parser.php from above url
        ajax.send(formdata);
}

function progressHandler(event) {
        _("loaded_n_total").innerHTML = "téléchargement " + event.loaded + " bytes sur " + event.total;
        var percent = (event.loaded / event.total) * 100;
        _("progressBar").value = Math.round(percent);
        _("status").innerHTML = Math.round(percent) + "% téléchargement... attendre S.V.P";
}

function completeHandler(event) {
        _("status").innerHTML = event.target.responseText;
        _("progressBar").value = 0; //wil clear progress bar after successful upload
        _("loaded_n_total").innerHTML = "";
	updatefichier("ok");
}

function errorHandler(event) {
	_("status").innerHTML = "Téléchargement erreur";
}

function abortHandler(event) {
	_("status").innerHTML = "Téléchargement Abandonné";
}

function ajoutDestinataire() {
	var indexdest=document.getElementById('saisie_destinataire');
	var valeurdest = indexdest.options[indexdest.selectedIndex].value;
	var textdest = indexdest.options[indexdest.selectedIndex].text;
	if (valeurdest != '0') {
		document.getElementById('liste_destinataire').innerHTML+=textdest+", ";
		document.getElementById('liste_destinataireValue').value+=valeurdest+",";
	}
}


function annulDestinataire() {
	document.getElementById('liste_destinataire').innerHTML="Liste des destinataires : ";
	document.getElementById('liste_destinataireValue').value="";
	document.getElementById("saisie_destinataire").selectedIndex=0;
}


function updatefichier(item) { 
	if (item == "ok") {
		ajaxActualisePieceJointe('<?php print $idpiecejointe ?>','listingpiecejointe');
	}
}



</script>

</head>
<body  id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
<tr id='cadreCentral0'>
<td valign=top height='100%'>
<!-- // fin  -->
<?php
if (isset($_GET["erreur"])) {
	$erreur="<script language=javascript>alert(langfunc16)</script>";
}
?>
<form name="formulaire" method=post action='./messagerie_enr.php' target='_parent' onsubmit='return verif_message_envoi()'>
<BR>
<?php
$cnx=cnx();

$brouillon=0;
if (isset($_GET["brouillon"])) { $brouillon=$_GET["brouillon"]; }
if (isset($_POST["brouillon"])) { $brouillon=$_POST["brouillon"]; }

if (isset($_GET["f"])) { $forwarding=$_GET["f"]; } 
if (isset($_GET["saisie_id_message"])) { $idMessage=$_GET["saisie_id_message"]; } 

include_once('librairie_php/db_triade.php');
if ($forwarding == 1) {
	if ($idMessage > 0) {
		$dataMessage=affichage_messagerie_message($idMessage);
		for($i=0;$i<count($dataMessage);$i++) {
			$emetteur=$dataMessage[$i][1];
			//if ($emetteur != $_SESSION["id_pers"]) continue ; 
			$qui_envoi=$dataMessage[$i][7];
			$number=$dataMessage[$i][10];
			if ((trim($dataMessage[$i][7]) == "ADM")||(trim($dataMessage[$i][7]) == "ENS")||(trim($dataMessage[$i][7]) == "MVS")||(trim($dataMessage[$i][7]) == "TUT")) {
			    $destinataire=recherche_personne($dataMessage[$i][1]);
			}else{
			    $destinataire=recherche_eleve($dataMessage[$i][1]);
			}
			$messageencours="<br><br><hr>";
			$messageencours.="> <i>".LANGMESS31.": $destinataire</i><br>";
			$messageencours.="> <i>".LANGTE12." ".dateForm($dataMessage[$i][4])." ".LANGTE13." ".$dataMessage[$i][5]."</i>";
			$messageencours.=stripslashes(Decrypte($dataMessage[$i][3],$number));
			$_GET["saisie_objet"]=trunchaine("FWD: ".$dataMessage[$i][8],50);
		}
	}
}


if ($_GET["saisie_envoi"] == "mailexterne") {
	if(isset($_SESSION["id_suppleant"])) {
		$id_pers=$_SESSION["id_suppleant"];
	}else{
		$id_pers=$_SESSION["id_pers"];
	}
	$source=mess_mail_forward($_SESSION["nom"],$_SESSION["prenom"],$id_pers,$_SESSION["membre"]); 
}else{
	$source=$_SESSION["nom"]." ".$_SESSION["prenom"];
}
?>
	<font class=T2><?php print ucwords(LANGTE3)?> : <input type=text name="saisie_emetteur" value="<?php print stripslashes($source) ?>" onfocus=this.blur() readonly="readonly" size=40 STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"><br /><br />
<?php print LANGTE5?> : <input type=text name="saisie_objet"  autocomplete="off"  size=50 maxlength=50 value="<?php print stripslashes(stripcslashes($_GET["saisie_objet"]))?>">
<BR> <BR></font>
<?php
$langt6=LANGTE6;
if (($_GET["saisie_envoi"] != "administrateur") && ($_GET["saisie_envoi"] != "scolaire") && ($_GET["saisie_envoi"] != "enseignant") && ($_GET["saisie_envoi"] != "tuteur") && ($_GET["saisie_envoi"] != "personnel") ) {
	$langt6=LANGTE6bis;
}
if ($_GET["saisie_envoi"] == "grpmail") { $langt6=LANGTE14; }
if ($_GET["saisie_envoi"] == "grpmailelev") { $langt6=LANGTE14; }
if ($_GET["saisie_envoi"] == "eleve") { $langt6="A l'élève "; }
if ($_GET["saisie_envoi"] == "mailexterne") { $langt6="A "; }
if ($_GET["saisie_envoi"] == "delegue") { $langt6="A "; }
if ($_GET["saisie_envoi"] == "tuteurdestage") { $langt6="Au tuteur de stage "; }
?>
<font class=T2>
<?php print ucfirst($langt6)?> : </font>
<?php 


if ($_GET["saisie_envoi"] == "mailexterne") {
	print "<input  type=text name='saisie_destinataire' size='40' id='toemail' 
	onblur=\"document.getElementById('liste_destinataireValue').value=this.value;
		   document.getElementById('liste_destinataire').innerHTML='Liste des destinataires : '+this.value;\" />";
}else{
	if ((trim(strtoupper($_GET["saisie_envoi"])) == "GRPMAIL") || (trim(strtoupper($_GET["saisie_envoi"])) == "GRPMAILELEV") ) {

		$foncAppel="";
		if (trim(strtoupper($_GET["saisie_envoi"])) == "GRPMAILELEV") { $foncAppel="Eleve"; }
?>
		<script>
		var etat2=0;
		function bul2() {
			AffBulle3('Liste des personnes','./image/commun/info.jpg',"");
				listingGroupeMail<?php print $foncAppel ?>(document.formulaire.saisie_destinataire.options[document.formulaire.saisie_destinataire.options.selectedIndex].value);
		}
		</script>
<?php
		print "&nbsp;[ <a href='#' onmouseover='bul2()'  onmouseout='HideBulle();' >listing</a> ]&nbsp;&nbsp;";

	}
	print "<select name='saisie_destinataire' onchange='ajoutDestinataire()' id='saisie_destinataire' >";
	print "<option value='0' STYLE='color:#000066;background-color:#FCE4BA'>".LANGCHOIX."</option>";
	// on recupere $saisie_envoi soit administrateur, scolaire, enseignant, classe, group mail
	if (trim($_GET["saisie_envoi"]) == "administrateur") { $qui_envoi="ADM"; }
	if (trim($_GET["saisie_envoi"]) == "scolaire") { $qui_envoi="MVS"; }
	if (trim($_GET["saisie_envoi"]) == "enseignant") { $qui_envoi="ENS"; }
	if (trim($_GET["saisie_envoi"]) == "grpmail") { $qui_envoi="GRPMAIL"; }
	if (trim($_GET["saisie_envoi"]) == "grpmailelev") { $qui_envoi="GRPMAILELEV"; }
	if (trim($_GET["saisie_envoi"]) == "parent") { $qui_envoi="PAR"; }
	if (trim($_GET["saisie_envoi"]) == "eleve") { $qui_envoi="ELE"; }
	if (trim($_GET["saisie_envoi"]) == "tuteur") { $qui_envoi="TUT"; }
	if (trim($_GET["saisie_envoi"]) == "personnel") { $qui_envoi="PER"; }
	if (trim($_GET["saisie_envoi"]) == "delegue") { $qui_envoi="DELEGUE"; }
	if (trim($_GET["saisie_envoi"]) == "tuteurdestage") { $qui_envoi="TUTEURSTAGE"; }

	if (($qui_envoi == "ADM") || ($qui_envoi == "MVS") || ($qui_envoi == "ENS" ) || ($qui_envoi == "TUT" ) || ($qui_envoi == "PER" )) {
		select_personne_messagerie($qui_envoi); // creation des options
	}elseif ($qui_envoi == "GRPMAIL") {
		select_grp_mail($_SESSION["id_pers"]) ;

	}elseif ($qui_envoi == "GRPMAILELEV") {
		select_grp_mailelev($_SESSION["id_pers"]) ;


	}elseif (($qui_envoi == "ELE")  || ($qui_envoi == "PAR")) {
		$anneeScolaire=anneeScolaireViaIdClasse($_GET['saisie_classe']);
		$sql="SELECT b.libelle,a.elev_id,a.nom,a.prenom FROM ${prefixe}eleves a,${prefixe}classes b WHERE a.classe='$_GET[saisie_classe]' AND b.code_class='$_GET[saisie_classe]' AND a.annee_scolaire='$anneeScolaire'  ORDER BY nom";
		$res=execSql($sql);
		$data_eleve=chargeMat($res);
		if ($_GET["saisie_envoi"] == "eleve") { 
			if (ACCESMESSELEVE == "oui") {
				$choix2="Tous les élèves"; 
				$choix22="tousleseleves"; 
			}
		}else{
			if (ACCESMESSPARENT == "oui") { 
				$choix2="Tous les parents"; 
				$choix22="touslesparents"; 
			}
		}
		if ( ($_GET["saisie_classe"] == "tousleselevesecole") || ($_GET["saisie_classe"] == "touslesparentsecole") ) {
			if ($_GET["saisie_classe"] == "tousleselevesecole") {
				print "<option STYLE='color:#000066;background-color:#FCE4BA' value='tousleselevesecole'>$choix2</option>";
			}
			if ($_GET["saisie_classe"] == "touslesparentsecole") {
				print "<option STYLE='color:#000066;background-color:#FCE4BA' value='touslesparentsecole'>$choix2</option>";
			}
		}else{
			if ($choix2 != "") { print "<option STYLE='color:#000066;background-color:#FCE4BA' value='$choix22'>$choix2</option>"; }
		}
		for ($j=0;$j<count($data_eleve);$j++) {
			$cra="ok";
			if ((ACCESMESSPARENT == "non") && (MESSDELEGUEPARENT == "oui") && ($_GET["saisie_envoi"] == "parent")) {
				$cra=delegue($data_eleve[$j][1],$_GET["saisie_classe"],$_GET["saisie_envoi"]);
			}
			if ((ACCESMESSELEVE == "non") && (MESSDELEGUEELEVE == "oui") && ($_GET["saisie_envoi"] == "eleve")) {
				$cra=delegue($data_eleve[$j][1],$_GET["saisie_classe"],$_GET["saisie_envoi"]);
			}
			if (trim($cra) != "") {  ?>
				<option STYLE='color:#000066;background-color:#CCCCFF' value="<?php print $data_eleve[$j][1]?>">
				<?php print ucwords(trim($data_eleve[$j][2]))." ".trunchaine(trim($data_eleve[$j][3]),15)." ".delegue($data_eleve[$j][1],$_GET["saisie_classe"],$_GET["saisie_envoi"])?>
		        </option>
<?php
			}
		}
	}elseif($qui_envoi == "DELEGUE") {
		//$choix1="Tout le monde";
		//$choix11="Toutlemonde";
		$choix2="Tous les élèves délégués"; 
		$choix22="tousleselevesdelegue"; 
		$choix3="Tous les parents délégués"; 
		$choix33="touslesparentsdelegues"; 
		//print "<option STYLE='color:#000066;background-color:#FCE4BA' value='$choix11'>$choix1</option>";
		print "<option STYLE='color:#000066;background-color:#FCE4BA' value='$choix22'>$choix2</option>";
		print "<option STYLE='color:#000066;background-color:#FCE4BA' value='$choix33'>$choix3</option>";
		$listedelegue=aff_delegueTous(); //idclasse,nomparent1,nomparent2,eleve1,eleve2
		for ($j=0;$j<count($listedelegue);$j++) {
			print "<option STYLE='color:#000066;background-color:#CCCCFF' value=\"".$listedelegue[$j][1]."\">";
			print "Parent : ".rechercheEleveNomPrenom($listedelegue[$j][1])." (".chercheClasse_nom($listedelegue[$j][0]).") ";
			print "</option>";
			print "<option STYLE='color:#000066;background-color:#CCCCFF' value=\"".$listedelegue[$j][2]."\">";
			print "Parent : ".rechercheEleveNomPrenom($listedelegue[$j][2])." (".chercheClasse_nom($listedelegue[$j][0]).") ";
			print "</option>";
			print "<option STYLE='color:#000066;background-color:#CCCCFF' value=\"".$listedelegue[$j][3]."\">";
			print "Elève : ".rechercheEleveNomPrenom($listedelegue[$j][3])." (".chercheClasse_nom($listedelegue[$j][0]).") ";
			print "</option>";
			print "<option STYLE='color:#000066;background-color:#CCCCFF' value=\"".$listedelegue[$j][4]."\">";
			print "Elève : ".rechercheEleveNomPrenom($listedelegue[$j][4])." (".chercheClasse_nom($listedelegue[$j][0]).") ";
			print "</option>";
		}
	
	}elseif($qui_envoi == "TUTEURSTAGE") {
		if ($_GET["saisie_classe"] == "touslestuteursdestage") {
			$choix2="Tous les tuteurs de stage";
			print "<option STYLE='color:#000066;background-color:#FCE4BA' value='touslestuteursdestage'>$choix2</option>";
		}else{
			$choix2="Tous les tuteurs de stage de cette classe";
			print "<option STYLE='color:#000066;background-color:#FCE4BA' value='touslestuteursdestagedelaclasse'>$choix2</option>";
			$listetuteurStage=aff_TuteurStage($_GET["saisie_classe"]); //idclasse,nomparent1,nomparent2,eleve1,eleve2
	                for ($j=0;$j<count($listetuteurStage);$j++) {
				print "<option STYLE='color:#000066;background-color:#CCCCFF' value=\"".$listetuteurStage[$j][0]."\">";
				print strtoupper($listetuteurStage[$j][1])." ".ucfirst($listetuteurStage[$j][2]);
                        	print "</option>";
			}

		}
	}
	print "</select> [ <a href='#' onclick='annulDestinataire(); return false;'>".LANGMESS393."</a>]";
}
print $erreur; 
?>
<BR><BR>
<div id='liste_destinataire' style=width:100%;height:50;overflow:auto ><?php print LANGMESS392 ?> : </div>
<input type="hidden" name="saisie_destinataire_value" id="liste_destinataireValue" />
<br /><br />
<textarea id="editor" name="resultat" cols='200'><?php print stripslashes($messageencours) ?></textarea>
<script type="text/javascript">
var colorGRAPH='<?php print $GRAPH ?>';
//<![CDATA[
CKEDITOR.replace('editor', {height:'320px',language:'<?php print ($_SESSION["langue"] == "fr") ? "fr":"en";?>',scayt_autoStartup:true,grayt_autoStartup:true,scayt_maxSuggestions:3,scayt_sLang:'en_FR',removeButtons:'PasteFromWord' });
//]]>
</script><br />
<input type="hidden" name="saisie_type_personne_dest" 	value="<?php print $qui_envoi?>" >
<input type="hidden" name="saisie_envoi" 		value="<?php print $_GET["saisie_envoi"]?>" >
<input type="hidden" name="saisie_classe" 		value="<?php print $_GET["saisie_classe"]?>" >
<input type="hidden" name="idpiecejoint" 		value="<?php print $idpiecejointe ?>" >
<input type="hidden" name="brouillon" 			value="<?php print $brouillon ?>" >
<?php 
if ((ACCESMESSELEVE == "oui") || (ACCESMESSPARENT == "oui"))  {
	print "<input type='checkbox' name='envoimessagecompletparmail' value='1'/>Envoyer seulement par email.&nbsp;&nbsp;&nbsp; ";
}	
	print "<input type='checkbox'  onclick=\"ajoutSignature('".$_SESSION['id_pers']."','".$_SESSION['membre']."')\" name='ajoutsignature' value='1'/>Ajouter votre signature. [<a href='#' onClick='open(\"configSignature.php?GRAPH=".$GRAPH."\",\"config\",\"width=500,height=500\")' >Config</a>]";
	
?>
<div  style="position:absolute;top:730;left:100" >
	
<table align=center>
<tr><td>
<script language=JavaScript>buttonMagicSubmit2('<?php print LANGBT4?>','rien','<?php print LANGBT5?>');</script>&nbsp;&nbsp;
<script language=JavaScript>buttonMagic("<?php print LANGBT3?>","acces2.php","_parent","","")</script>
<?php
if (file_exists("./common/config-ia.php")) {
	include_once("common/productId.php");
        include_once("common/config-ia.php");
        $productID=PRODUCTID;
        $iakey=IAKEY;
        $lienIA="ajaxIAOrtho(document.getElementById('commentaire').value,'$productID','$iakey','editor',CKEDITOR)";        
}else{
        $lienIA="alert('Votre Triade n\'est pas configur&eacute; pour utiliser l\'IA. Contacter votre administrateur Triade')";        
}

if (($_SESSION['membre'] == "menuadmin") || ($_SESSION['membre'] == "menuprof") || ($_SESSION['membre'] == "menuscolaire")) {

	print "<br><br><input type='text' size=50 id='commentaire' placeholder=\"Indiquer une suggestion de message &agrave; r&eacute;diger\"  /> <input type='button' value='TRIADE-COPILOT' id='bt_copilot' class='BUTTON' onClick=\"$lienIA\" >&nbsp;&nbsp;<a href='#'  onMouseOver=\"AffBulle('TRIADE-COPILOT vous permet de pr&eacute;parer votre message via des mots clefs que vous indiquez.');\"  onMouseOut=\"HideBulle()\";><img src=\"./image/help.gif\" border=0 align=center></a>
";
}


brmozilla($_SESSION["navigateur"]);
brmozilla($_SESSION["navigateur"]); 
?>

</td></tr></table>
</div>
</form>

<?php if ($_GET["saisie_envoi"] != "mailexterne") {  ?>

<div id="fjoint2" style="position:absolute; top:650 ;left:10;visibility:hidden "><font class='T2'><?php print LANGBT5 ?> </font> <img src='./image/commun/indicator.gif' align='center' /></div>

<div id="infofichierjoint" style="position:absolute; top:680 ;left:10;visibility:hidden "></div>

<div id="fjoint" style="position:absolute; top:650 ;left:10"></div>
<?php
$taille="2Mo";
$maxsize="2000000";
if (UPLOADIMG == "oui") { $taille="8Mo"; $maxsize="8000000"; }
?>
<br /><br /><br /><br /><ul>
<table><tr><td valign=top><br>
&nbsp;&nbsp;Pièce(s) jointe(s) : <br>

<form id="upload_form" enctype="multipart/form-data" method="post">

<input type="file" name="Filedata" id="Filedata" onChange="uploadFile('<?php print trim($idpiecejointe) ?>')"> <a href='#'  onMouseOver="AffBulle('Fichier Taille Max : <?php print $taille ?>');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a><br><br>
<progress id="progressBar" value="0" max="100" style="width:300px;">
</progress>
<h3 id="status"></h3>
<p id="loaded_n_total"></p>
<br>

</form>

<div id="listingpiecejointe" style="width:100%;height:50;overflow:auto" ></div>

</td></tr></table></ul>


<script type="text/javascript" >
function chargement() {
	document.getElementById('fjoint').style.visibility="hidden";
	document.getElementById('fjoint2').style.visibility="visible";
}
</script>
<!-- // fin  -->

<?php } ?>
</td></tr></table>
<?php Pgclose(); ?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT> 
</BODY></HTML>
