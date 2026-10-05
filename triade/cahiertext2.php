<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
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
include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET);
include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php"); // futur : auto_prepend_file
include_once("./common/config2.inc.php"); // futur : auto_prepend_file
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/calendar.php");
$cnx=cnx();
if ($_SESSION["membre"] == "menupersonnel") {
	if (!verifDroit($_SESSION["id_pers"],"cahiertextes")) {
		accesNonReserveFen();
		exit();
	}
}else{
	validerequete("profadmin");	
}
// Sn : variable de Session nom
// Sp : variable de Session prenom
// Sm : variable de Session membre
// Spid : variable de Session pers_id
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);
if (isset($_GET["sClasseGrp"])) {
	$sClasseGrp=$_GET["sClasseGrp"];
	$sMat=$_GET["sMat"];
}else{
	$sClasseGrp=$_POST["sClasseGrp"];
	$sMat=$_POST["sMat"];
}
$listTmp=explode(":",$sClasseGrp);
$HPV['cid']=$listTmp[0];
$HPV['gid']=$listTmp[1];
$list2=$listTmp[1];
$list1=$listTmp[0];
unset($listTmp);
//print_r($HPV);
if($HPV['gid']):
	$val=LANGDEVOIR1;
    	$who="<b><font id='menumodule1'> $val : </b></font> ".ucwords(trunchaine(chercheGroupeNom($HPV['gid']),14));
	$classorgrp=1;
	$classe=$HPV['gid'];
else:
	$val=LANGDEVOIR2;
	$cl=chercheClasse($HPV['cid']);
	$classorgrp=0;
    	$who="<b><font id='menumodule1'> $val : </b></font>".ucwords(trunchaine($cl[0][1],14));
	$classe=$cl[0][1];
    	unset($cl);
endif;

// Sn : variable de Session nom
// Sp : variable de Session prenom
// Sm : variable de Session membre
// Spid : variable de Session pers_id
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);

$sql="
SELECT
	a.code_classe,
	trim(c.libelle),
	a.code_matiere,
";
if(DBTYPE=='pgsql')
{
	$sql .= " trim(m.libelle)||' '||trim(m.sous_matiere)||' '||trim(langue), ";
}
elseif(DBTYPE=='mysql')
{
	$sql .= " CONCAT( trim(m.libelle) , ' ' , trim(m.sous_matiere) , ' ' , trim(langue) ) , ";
}
$Spid=$mySession['Spid'];

if ($_SESSION["idprofAdminCdT"] != '')  { $Spid=$_SESSION["idprofAdminCdT"];  }

$sql .= "
	a.code_groupe,
	trim(g.libelle)
FROM
	{$prefixe}affectations a,
	{$prefixe}matieres m,
	{$prefixe}classes c,
	{$prefixe}groupes g
WHERE
	code_prof='$Spid'
AND a.code_classe = c.code_class
AND a.code_matiere = m.code_mat
AND a.code_groupe = group_id
AND a.annee_scolaire = '$anneeScolaire'
GROUP BY a.code_matiere,a.code_classe,a.code_groupe
ORDER BY
	c.libelle,m.libelle
";
$curs=execSql($sql);
$data=chargeMat($curs);
@array_unshift($data,array()); // nécessaire pour compatibilité
// patch pour problème sous-matiere
for($i=0;$i<countTriade($data);$i++){
	$tmp=explode(" 0 ",$data[$i][3]);
	$data[$i][3]=$tmp[0].' '.$tmp[1];
}
// fin patch
genMatJs('affectation',$data);
freeResult($curs);
unset($curs);


?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<title>Triade - Compte de <?php print ucwords($mySession['Sp'])." ".strtoupper($mySession['Sn'])?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/menu-tab.css">
<style>
.na-card  { background:#fff !important; border:1px solid #c5cae9 !important; border-radius:8px !important; padding:14px 16px !important; margin:10px 0 10px !important; }
.na-row   { display:flex !important; align-items:center !important; margin-bottom:8px !important; gap:8px !important; flex-wrap:wrap !important; }
.na-lbl   { font-size:12px !important; font-weight:600 !important; color:#333 !important; min-width:140px !important; flex-shrink:0 !important; }
.na-foot  { margin-top:8px !important; overflow:hidden !important; }
.btn-action {
    font-family:Arial; font-size:11px; font-weight:bold;
    color:#080A66; background:#CACCEF;
    border:none; border-radius:5px; padding:5px 12px;
    cursor:pointer; white-space:nowrap;
}
.btn-action:hover { background:#b0b4e8; }
.btn-view-grid {
    display:flex; flex-wrap:wrap; gap:8px; margin:10px 0 6px;
}
.btn-view {
    font-family: Electrolize, Arial, sans-serif;
    font-size: 12px; font-weight: 700;
    color: #fff; border: none; border-radius: 7px;
    padding: 10px 18px; cursor: pointer;
    white-space: nowrap; transition: filter .15s;
    letter-spacing: .3px;
}
.btn-view:hover { filter: brightness(1.12); }
.btn-view-semaine  { background: #1565c0; }
.btn-view-matiere  { background: #283593; }
.btn-view-complet  { background: #4527a0; }
.btn-view-ia       { background: #2e7d32; }
</style>
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_devoir_scolaire.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/ajaxIA.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax-menu-tab.js"></script>
<script type="text/javascript" src="./librairie_js/menu-tab.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_actualisepiecejointecahiertext.js"></script>
<script type="text/javascript">

function _(el) {
  return document.getElementById(el);
}

function uploadFile(idpiecejointe,num) {
        var file = _("Filedata"+num).files[0];
        // alert(file.name+" | "+file.size+" | "+file.type);
        var formdata = new FormData();
        formdata.append("Filedata"+num, file);
        var ajax = new XMLHttpRequest();

	if (num == 1) {
	        ajax.upload.addEventListener("progress", progressHandler1, false);
        	ajax.addEventListener("load", completeHandler1, false);
	        ajax.addEventListener("error", errorHandler1, false);
	        ajax.addEventListener("abort", abortHandler1, false);
	}
	if (num == 2) {
                ajax.upload.addEventListener("progress", progressHandler2, false);
                ajax.addEventListener("load", completeHandler2, false);
                ajax.addEventListener("error", errorHandler2, false);
                ajax.addEventListener("abort", abortHandler2, false);
        }
	if (num == 3) {
                ajax.upload.addEventListener("progress", progressHandler3, false);
                ajax.addEventListener("load", completeHandler3, false);
                ajax.addEventListener("error", errorHandler3, false);
                ajax.addEventListener("abort", abortHandler3, false);
        }

        ajax.open("POST", "uploadcahiertexte.php?idpiecejointe="+idpiecejointe+"&num="+num); // http://www.developphp.com/video/JavaScript/File-Upload-Progress-Bar-Meter-Tutorial-Ajax-PHP
  //use file_upload_parser.php from above url
        ajax.send(formdata);
}

function progressHandler3(event) {
        _("loaded_n_total3").innerHTML = "téléchargement " + event.loaded + " bytes sur " + event.total;
        var percent = (event.loaded / event.total) * 100;
        _("progressBar3").value = Math.round(percent);
        _("status3").innerHTML = Math.round(percent) + "% téléchargement... attendre S.V.P";
}

function completeHandler3(event) {
        _("status3").innerHTML = event.target.responseText;
        _("progressBar3").value = 0; //wil clear progress bar after successful upload
        _("loaded_n_total3").innerHTML = "";
        updatefichier("ok"); // specif cahier de text
}

function errorHandler3(event) {
  _("status3").innerHTML = "Téléchargement erreur";
}

function abortHandler3(event) {
  _("status3").innerHTML = "Téléchargement Abandonné";
}


function progressHandler2(event) {
        _("loaded_n_total2").innerHTML = "téléchargement " + event.loaded + " bytes sur " + event.total;
        var percent = (event.loaded / event.total) * 100;
        _("progressBar2").value = Math.round(percent);
        _("status2").innerHTML = Math.round(percent) + "% téléchargement... attendre S.V.P";
}

function completeHandler2(event) {
        _("status2").innerHTML = event.target.responseText;
        _("progressBar2").value = 0; //wil clear progress bar after successful upload
        _("loaded_n_total2").innerHTML = "";
        updatefichier("ok"); // specif cahier de text
}

function errorHandler2(event) {
  _("status2").innerHTML = "Téléchargement erreur";
}

function abortHandler2(event) {
  _("status2").innerHTML = "Téléchargement Abandonné";
}



function progressHandler1(event) {
  	_("loaded_n_total1").innerHTML = "téléchargement " + event.loaded + " bytes sur " + event.total;
  	var percent = (event.loaded / event.total) * 100;
  	_("progressBar1").value = Math.round(percent);
	_("status1").innerHTML = Math.round(percent) + "% téléchargement... attendre S.V.P";
}

function completeHandler1(event) {
	_("status1").innerHTML = event.target.responseText;
	_("progressBar1").value = 0; //wil clear progress bar after successful upload
	_("loaded_n_total1").innerHTML = "";
	updatefichier("ok"); // specif cahier de text
}

function errorHandler1(event) {
  _("status1").innerHTML = "Téléchargement erreur";
}

function abortHandler1(event) {
  _("status1").innerHTML = "Téléchargement Abandonné";
}



function upSelectMat(arg) {
	for(i=1;i<document.formulaire.sMat.options.length;i++){
		document.formulaire.sMat.options[i].value='';
		document.formulaire.sMat.options[i].text='';
	}
	var tmp=arg.value.split(":");
	var clas=tmp[0];
	var grp=tmp[1];
	var opt=1;
	for(i=0;i<affectation.length;i++) {
		if(affectation[i][0] == clas && affectation[i][4] == grp) {
		myOpt=new Option();
		myOpt.value = affectation[i][2];
		myOpt.text = affectation[i][3];
		myOpt.text = myOpt.text.replace(/ 0 *$/,"");   // supprime le 0 de la matiere ajout ET
		document.formulaire.sMat.options[opt]=myOpt;
		opt++;
		}
	}
	return true;
}
</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<SCRIPT language="JavaScript" src="./librairie_js/menuprof.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"]?>1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="945" >
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print LANGMESS89 ?> </b><font id=color2><b><?php print trunchaine(chercheMatiereNom($sMat),15)." ".$who?></b></font></td></tr>
<tr id='cadreCentral0' >
<td valign='top' width="100%"  >
<!-- // fin  -->
<form method="POST" onsubmit="return verifAccesNote()" name="formulaire" action="cahiertext2.php">
<div class="na-card">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGELE4 ?> :</span>
    <select name="sClasseGrp" class="cc-select" onChange="upSelectMat(this)">
      <option value="0" style="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX3 ?></option>
      <?php
      for($i=1;$i<countTriade($data);$i++){
          if( $i>1 && ($data[$i][4]==$gtmp) && ($data[$i][0]==$ctmp) ){
              continue;
          }else{
              $libelle=$data[$i][4]?$data[$i][1]."-".$data[$i][5]:$data[$i][1];
              $selCgrp=($data[$i][0].":".$data[$i][4]==$sClasseGrp)?" selected":"";
              print "<option$selCgrp style='color:#000066;background-color:#CCCCFF' value=\"".$data[$i][0].":".$data[$i][4]."\">".$libelle."</option>\n";
          }
          $gtmp=$data[$i][4];
          $ctmp=$data[$i][0];
      }
      unset($gtmp); unset($ctmp); unset($libelle);
      ?>
    </select>
  </div>
  <div class="na-row">
    <span class="na-lbl">Matière :</span>
    <select name="sMat" class="cc-select">
      <option value="0" style="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
    </select>
  </div>
  <?php if ($_SESSION["membre"] == "menuadmin") { print "<input type='hidden' name='saisie_pers' value='$Spid' />"; } ?>
</div>
<div class="na-foot">
  <script language=JavaScript>buttonMagicSubmit("Sélectionner","rien");</script>
</div>
</form>
<?php if ($sClasseGrp && $sClasseGrp != "0" && $sMat && $sMat != "0"): ?>
<script>
window.addEventListener('load', function(){
    var sel = document.formulaire.sClasseGrp;
    if (sel && sel.value !== '0') {
        upSelectMat(sel);
        document.formulaire.sMat.value = '<?php print $sMat ?>';
    }
});
</script>
<?php endif ?>
<br />
<form method=post name="form11" id='form11' action="cahiertext2enr.php" enctype="multipart/form-data">
<input type=hidden id="saisie_date1" value="<?php print dateDMY()?>">
<?php
if (isset($_GET["date_convenu"])) {
	$datepour=$_GET["date_convenu"];
}else{
	$datepour=dateDMY();
}
if(file_exists("./common/config-ia.php")) {
	include_once("common/productId.php");
	include_once("common/config-ia.php");
	$productID=PRODUCTID;
	$iakey=IAKEY;
	$prenom=recherche_eleve_prenom($idEleve);
	$matiere=trim(chercheMatiereNom($sMat));
	$lienIA="open('devoirAcomposer.php?matiere=$matiere&classe=$classe','','width=600,height=500')";
}else{
	$lienIA="alert('Votre Triade n\'est pas configur&eacute; pour utiliser l\'IA. Contacter votre administrateur Triade')";
}
$btnMatClass = ($_SESSION["membre"] == "menuadmin") ? "btn-action btn-action-white" : "btn-action";
$bouton = ($_SESSION["membre"] == "menuadmin") ? LANGMESS101." " : LANGMESS102." ";
?>
<div class="na-card">
  <div class="na-row">
    <span class="na-lbl">Date :</span>
    <div style="display:inline-flex;align-items:center;gap:4px;">
      <input type=text name="date_contenu" id='date_contenu1' value="<?php print $datepour ?>" size=13 class='bouton2' onKeyPress="onlyChar(event)">
      <?php calendar("id1","document.form11.date_contenu",$_SESSION["langue"],"0"); ?>
    </div>
    <button type="submit" name="accesdate" value="<?php print VALIDER ?>" class="btn-action"><?php print VALIDER ?></button>
  </div>
</div>
<div class="btn-view-grid">
  <button type="button" class="btn-view btn-view-semaine" onclick="open('cahiertext_visu_global.php?iddate=<?php print dateFormBase($datepour) ?>&id=<?php print $list1?>&classorgrp=<?php print $classorgrp?>','devoir','width=1050,height=600,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes')"><?php print LANGMESS91 ?></button>
  <button type="button" class="btn-view btn-view-matiere" onclick="open('cahiertext_visu_matiere.php?iddate=<?php print dateFormBase($datepour) ?>&id=<?php print $list1?>&idmat=<?php print $sMat?>&classorgrp=<?php print $classorgrp?>&sClasseGrp=<?php print $sClasseGrp ?>','devoirmatiere','width=850,height=600,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes')"><?php print $bouton.trunchaine(chercheMatiereNom($sMat),15) ?></button>
  <button type="button" class="btn-view btn-view-complet" onclick="open('cahiertext_visu_complet.php?iddate=<?php print dateFormBase($datepour) ?>&id=<?php print $list1?>&idmat=tous','devoirtous','width=850,height=600,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes')"><?php print LANGMESS100 ?></button>
  <button type="button" class="btn-view btn-view-ia" onclick="<?php print $lienIA ?>">TRIADE-COPILOT</button>
</div>
<input type='hidden' name="saisie_idmatiere" value="<?php print $sMat?>">
<input type='hidden' name="saisie_idclsorgrp" value="<?php print $list1?>">
<input type='hidden' name="saisie_clsorgrp" value="<?php print $list2?>">
<input type='hidden' name="sClasseGrp" value="<?php print $sClasseGrp?>">
<input type='hidden' name="sMat" value="<?php print $sMat?>">
<input type='hidden' name="idpersProf" value="<?php print $idpersProf ?>">
</form>


<hr>


<div id="dhtmlgoodies_tabView1">
  <div class="dhtmlgoodies_aTab" style="border:0px" >
	<form method=post name="form111" >
<?php
if ( ($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menupersonnel")) {
	$idPers=$_SESSION["idprofAdminCdT"];
}else{
	$idPers=$_SESSION["id_pers"];	
}
$data=recherche_contenu_scolaire_($datepour,$classorgrp,$sMat,$list1,$idPers);
//id,id_class_or_grp,matiere_id,date_saisie,heure_saisie,classorgrp,number,contenu,objectif,date_contenu,idprof,number_obj,blocnote
$contenu=$data[0][7];
$objectif=$data[0][8];
$number_obj=$data[0][11];
if ($number_obj == "") {	
	$idpiecejointe2=md5("2".$_SESSION["membre"].$_SESSION["id_pers"].date("YMDHms").rand(0,9999));
}else{
	$idpiecejointe2=$number_obj;
}
$blocnotes=$data[0][12];
$number=$data[0][6];
if ($number == "") {
	$idpiecejointe1=md5("1".$_SESSION["membre"].$_SESSION["id_pers"].date("YMDHms").rand(0,9999));
}else{
	$idpiecejointe1=$number;
}
?>

<script type="text/javascript">

tinymce.init({
    save_onsavecallback: function() {savecontenu();},
    save_enablewhendirty: true,
    language : lang_lang,
    selector: "textarea#elm1",
    statusbar : false,
    width: '100%',
    height: '470',
    browser_spellcheck : true,
    menubar : "format edit table ",
    plugins: ["textcolor emoticons","link","table","save"], 
    element_format : "html",
    protect: [
        /\<\/?(if|endif)\>/g, 
        /\<xsl\:[^>]+\>/g, 
	/<\?php.*?\?>/g,
	/\<script/ig,
	/<\?.*\?>/g
	],
	schema: "html4",
<?php	
      if (($_SESSION["membre"] == "menuprof") || (DIRCAHIERTEXTE == "oui")) { ?>
    	toolbar: "forecolor | bold italic | textcolor | emoticons |  bullist numlist outdent indent | link | save"
<?php }else{ ?>
	toolbar: "undo redo "
<?php } ?>
});
var ed1 = tinymce.activeEditor; 

</script>
	<textarea id='elm1' name="saisie_contenu" spellcheck='true' ><?php print $contenu ?></textarea><br><br>

<?php
if (($_SESSION["membre"] == "menuprof") || (DIRCAHIERTEXTE == "oui")) { 
	$taille="2Mo";
	$maxsize="2000000";
	if (UPLOADIMG == "oui") { $taille="8Mo"; $maxsize="8000000"; }
?>
<table><tr><td valign=top>
<input type='text' size='60' placeholder="Indiquer des mots cl&eacute;s" id="questioncontenucours"  />&nbsp;&nbsp;
<input type='button' value='TRIADE-COPILOT' class='button' 
	onClick="ajaxContenuCours(document.getElementById('questioncontenucours').value,'<?php print $productID ?>','<?php print $iakey ?>','<?php print $matiere ?>','<?php print $classe ?>',document.getElementById('number1').value);" id="btq" />&nbsp;
<a href="#" onclick="document.getElementById('cahier-ia-help').style.display=(document.getElementById('cahier-ia-help').style.display=='none'?'block':'none'); return false;"><img src="./image/help.gif" border="0" align="center"></a>
<div id="cahier-ia-help" style="display:none; margin-top:6px; padding:10px 14px; background:#fffbe6; border:1px solid #f5c842; border-radius:6px; font-size:12px; color:#5a4000; max-width:500px; line-height:1.6;">
  <strong>TRIADE-COPILOT</strong><br>
  Indiquez des mots clés décrivant le contenu de cours à rédiger, puis cliquez sur <strong>TRIADE-COPILOT</strong>.
</div>
<br/><br>

<input type="file" name="Filedata1" id="Filedata1" onChange="uploadFile('<?php print trim($idpiecejointe1) ?>','1')"> <a href="#" onclick="this.nextSibling.style.display=(this.nextSibling.style.display=='none'?'block':'none'); return false;"><img src="./image/help.gif" border="0" align="center"></a><div style="display:none; margin-top:6px; padding:10px 14px; background:#fffbe6; border:1px solid #f5c842; border-radius:6px; font-size:12px; color:#5a4000; line-height:1.6;">Fichier Taille Max : <?php print $taille ?></div><br><br>
<progress id="progressBar1" value="0" max="100" style="width:300px;">
</progress>
<h3 id="status1"></h3>
<p id="loaded_n_total1"></p>
<br>



</td></tr></table>
<?php } ?>
<div id="listingpiecejointe1" style="width:100%;height:50;overflow:auto" ></div>
<input type='hidden' id="saisie_idmatiere1" value="<?php print $sMat?>" />
<input type='hidden' id="tempsestime1" value="<?php print $tempsestimedevoir?>" />
<input type='hidden' id="date_contenu1" value="<?php print $datepour ?>" />
<input type='hidden' id="saisie_idclsorgrp1" value="<?php print $list1?>" />
<input type='hidden' id="saisie_clsorgrp1" value="<?php print $list2?>" />
<input type='hidden' id="sClasseGrp1" value="<?php print $sClasseGrp?>" />
<input type='hidden' id="sMat1" value="<?php print $sMat?>" />
<input type='hidden' id="number1" value="<?php print $idpiecejointe1 ?>" />
</form>

  </div>

  <div class="dhtmlgoodies_aTab" style="border:0px" >
	<form method=post name="form112" >
<script type="text/javascript">


tinymce.init({
    save_onsavecallback: function() {savecontenu();},
    save_enablewhendirty: true,
    language : lang_lang,
    selector: "textarea#elm2",
    statusbar : false,
    width: '100%',
    height: '470',
    browser_spellcheck : true,
    menubar : "format edit table ",
    plugins: ["textcolor emoticons","link","table","save"],
    element_format : "html",
    protect: [
        /\<\/?(if|endif)\>/g, 
        /\<xsl\:[^>]+\>/g, 
	/<\?php.*?\?>/g,
	/\<script/ig,
	/<\?.*\?>/g
	],
	schema: "html4",
    <?php	
     if (($_SESSION["membre"] == "menuprof") || (DIRCAHIERTEXTE == "oui")) { ?>
    	toolbar: "forecolor | bold italic | textcolor | emoticons |  bullist numlist outdent indent | link | save"
<?php }else{ ?>
	toolbar: "undo redo "
<?php } ?>
});

</script>
	<textarea spellcheck='true' id='elm2' name="saisie_contenu" ><?php print $objectif ?></textarea><br><br>

<?php

if (($_SESSION["membre"] == "menuprof") || (DIRCAHIERTEXTE == "oui")) { 
	$taille="2Mo";
	$maxsize="2000000";
	if (UPLOADIMG == "oui") { $taille="8Mo"; $maxsize="8000000"; }
?>
<table><tr><td valign=top><br/>


<input type="file" name="Filedata2" id="Filedata2" onChange="uploadFile('<?php print trim($idpiecejointe2) ?>','2')"> <a href="#" onclick="this.nextSibling.style.display=(this.nextSibling.style.display=='none'?'block':'none'); return false;"><img src="./image/help.gif" border="0" align="center"></a><div style="display:none; margin-top:6px; padding:10px 14px; background:#fffbe6; border:1px solid #f5c842; border-radius:6px; font-size:12px; color:#5a4000; line-height:1.6;">Fichier Taille Max : <?php print $taille ?></div><br><br>
<progress id="progressBar2" value="0" max="100" style="width:300px;">
</progress>
<h3 id="status2"></h3>
<p id="loaded_n_total2"></p>
<br>




</td></tr></table>
<?php } ?>
<div id="listingpiecejointe2" style="width:100%;height:50;overflow:auto" ></div>

<input type='hidden' id="saisie_idmatiere2" value="<?php print $sMat?>" >
<input type='hidden' id="date_contenu2" value="<?php print $datepour ?>" >
<input type='hidden' id="tempsestime2" value="<?php print $tempsestimedevoir?>" >
<input type='hidden' id="saisie_idclsorgrp2" value="<?php print $list1?>" >
<input type='hidden' id="saisie_clsorgrp2" value="<?php print $list2?>" >
<input type='hidden' id="sClasseGrp2" value="<?php print $sClasseGrp?>" >
<input type='hidden' id="sMat2" value="<?php print $sMat?>" >
<input type='hidden' id="number2" value="<?php print $idpiecejointe2 ?>" >
</form>

  
  </div>
  <div class="dhtmlgoodies_aTab" style="border:0px" >
<?php
print "<form method=post name='form12' enctype='multipart/form-data'  >";
$data=recherche_devoir_scolaire_2($datepour,$list2,$sMat,$list1,$idPers);
//id,id_class_or_grp,matiere_id,date_saisie,heure_saisie,date_devoir,texte,classorgrp,number,idprof,tempsestimedevoir
$travail=$data[0][6];
$number=$data[0][8];
if ($number == "") {
	$idpiecejointe3=md5("3".$_SESSION["membre"].$_SESSION["id_pers"].date("YMDHms").rand(0,9999));
}else{
	$idpiecejointe3=$number;
}
$tempsestimedevoir=$data[0][10];
?>

<script type="text/javascript">
tinymce.init({
    save_onsavecallback: function() {savecontenu();},
    save_enablewhendirty: true,
    language : lang_lang,
    selector: "textarea#elm3",
    statusbar : false,
    width: '100%',
    height: '450',
    browser_spellcheck : true,
    menubar : "format edit table ",
    plugins: ["textcolor emoticons","link","table","save"],
    element_format : "html",
    protect: [
        /\<\/?(if|endif)\>/g, 
        /\<xsl\:[^>]+\>/g, 
	/<\?php.*?\?>/g,
	/\<script/ig,
	/<\?.*\?>/g
	],
	schema: "html4",
   <?php	
     if (($_SESSION["membre"] == "menuprof") || (DIRCAHIERTEXTE == "oui")) { ?>
    	toolbar: "forecolor | bold italic | textcolor | emoticons |  bullist numlist outdent indent | link | save"
<?php }else{ ?>
	toolbar: "undo redo "
<?php } ?>
});

</script>

<?php
print "<br>&nbsp;&nbsp;<font class=T2>".LANGMESS96." : <b>$datepour</b> <input type=hidden value=\"$datepour\" size='10' id='date_devoir3' readonly='readonly' /> ";

	


print 	"</font><br /><br>";

if ( (isset($_GET["tempsestime"]))  && (trim($_GET["tempsestime"]) != "" )) {
	$tempsestime=$_GET["tempsestime"];
}else{
	$tempsestime='hh:mm';
}

print "&nbsp;&nbsp;<font class='T2'>".LANGMESS103." : 
	<select id='tempsestime3' >";
	if (($tempsestimedevoir != "00:00:00") && ($tempsestimedevoir != "")) {
		if ($tempsestimedevoir == "00:10:00") { $tempstime="10mn"; }
		if ($tempsestimedevoir == "00:15:00") { $tempstime="15mn"; }
		if ($tempsestimedevoir == "00:20:00") { $tempstime="20mn"; }
		if ($tempsestimedevoir == "00:25:00") { $tempstime="25mn"; }
		if ($tempsestimedevoir == "00:30:00") { $tempstime="30mn"; }
		if ($tempsestimedevoir == "00:35:00") { $tempstime="35mn"; }
		if ($tempsestimedevoir == "00:40:00") { $tempstime="40mn"; }
		if ($tempsestimedevoir == "00:45:00") { $tempstime="45mn"; }
		if ($tempsestimedevoir == "00:50:00") { $tempstime="50mn"; }
		if ($tempsestimedevoir == "00:55:00") { $tempstime="55mn"; }
		if ($tempsestimedevoir == "01:00:00") { $tempstime="1h00"; }
		if ($tempsestimedevoir == "01:15:00") { $tempstime="1h15"; }
		if ($tempsestimedevoir == "01:30:00") { $tempstime="1h30"; }
		if ($tempsestimedevoir == "01:45:00") { $tempstime="1h45"; }
		if ($tempsestimedevoir == "02:00:00") { $tempstime="2h00"; }
		if ($tempsestimedevoir == "02:30:00") { $tempstime="2h30"; }
		if ($tempsestimedevoir == "03:00:00") { $tempstime="3h00"; }
		if ($tempsestimedevoir == "04:00:00") { $tempstime="4h00"; }
		print "<option value='$tempsestimedevoir'  id='select1'>$tempstime</option>";

	}
print "	<option value='00:00:00' id='select0'> ".LANGMESS97." </option>
	<option value='00:10:00' id='select1'> 10mn </option>
	<option value='00:15:00' id='select1'> 15mn </option>
	<option value='00:20:00' id='select1'> 20mn </option>
	<option value='00:25:00' id='select1'> 25mn </option>
	<option value='00:30:00' id='select1'> 30mn </option>
	<option value='00:35:00' id='select1'> 35mn </option>
	<option value='00:40:00' id='select1'> 40mn </option>
	<option value='00:45:00' id='select1'> 45mn </option>
	<option value='00:50:00' id='select1'> 50mn </option>
	<option value='00:55:00' id='select1'> 55mn </option>
	<option value='01:00:00' id='select1'> 1h00 </option>
	<option value='01:15:00' id='select1'> 1h15  </option>
	<option value='01:30:00' id='select1'> 1h30  </option>
	<option value='01:45:00' id='select1'> 1h45  </option>
	<option value='02:00:00' id='select1'> 2h00  </option>
	<option value='02:30:00' id='select1'> 2h30  </option>
	<option value='03:00:00' id='select1'> 3h00  </option>
	<option value='04:00:00' id='select1'> 4h00  </option>
	</select> 
	</font>";
print "<br><br>";
?>

	<textarea spellcheck='true' id='elm3' name="saisie_contenu" ><?php print $travail ?></textarea><br><br>

<?php
	if (($_SESSION["membre"] == "menuprof") || (DIRCAHIERTEXTE == "oui")) { 
	$taille="2Mo";
	$maxsize="2000000";
	if (UPLOADIMG == "oui") { $taille="8Mo"; $maxsize="8000000"; }
?>
<table><tr><td valign=top><br/>



<input type="file" name="Filedata3" id="Filedata3" onChange="uploadFile('<?php print trim($idpiecejointe3) ?>','3')"> <a href="#" onclick="this.nextSibling.style.display=(this.nextSibling.style.display=='none'?'block':'none'); return false;"><img src="./image/help.gif" border="0" align="center"></a><div style="display:none; margin-top:6px; padding:10px 14px; background:#fffbe6; border:1px solid #f5c842; border-radius:6px; font-size:12px; color:#5a4000; line-height:1.6;">Fichier Taille Max : <?php print $taille ?></div><br><br>
<progress id="progressBar3" value="0" max="100" style="width:300px;">
</progress>
<h3 id="status3"></h3>
<p id="loaded_n_total3"></p>
<br>


</td></tr></table>
<?php } ?>
<div id="listingpiecejointe3" style="width:100%;height:50;overflow:auto" ></div>

<input type='hidden' id="saisie_idmatiere3" value="<?php print $sMat?>" >
<input type='hidden' id="saisie_idclsorgrp3" value="<?php print $list1?>" >
<input type='hidden' id="saisie_clsorgrp3" value="<?php print $list2?>" >
<input type='hidden' id="date_contenu3" value="<?php print $datepour?>" >
<input type='hidden' id="sClasseGrp3" value="<?php print $sClasseGrp?>" >
<input type='hidden' id="sMat3" value="<?php print $sMat?>" >
<input type='hidden' id="saisie_date3" value="<?php print $datepour?>" >
<input type='hidden' id="number3" value="<?php print $idpiecejointe3 ?>" >
</form>

  
  </div>
<div class="dhtmlgoodies_aTab" style="border:0px" >
<form method=post name="form112" >
<script type="text/javascript">
tinymce.init({
    save_onsavecallback: function() {savecontenu();},
    save_enablewhendirty: true,
    language : lang_lang,
    selector: "textarea#elm4",
    statusbar : false,
    width: '100%',
    height: '490',
    browser_spellcheck : true,
    menubar : "tools table format edit ",
    plugins: [ "textcolor","link", "save"], 
    element_format : "html",
    protect: [
        /\<\/?(if|endif)\>/g, 
        /\<xsl\:[^>]+\>/g, 
	/<\?php.*?\?>/g,
	/\<script/ig,
	/<\?.*\?>/g
	],
	schema: "html4",
<?php	
     if (($_SESSION["membre"] == "menuprof") || (DIRCAHIERTEXTE == "oui")) { ?>
    	toolbar: "forecolor | bold italic | bullist numlist outdent indent | link | save"
<?php }else{ ?>
	toolbar: "undo redo "
<?php } ?>
});

</script>
<textarea id='elm4' name="saisie_contenu" spellcheck='true'  ><?php print $blocnotes ?></textarea>
<input type='hidden' id="saisie_idmatiere4" value="<?php print $sMat?>" >
<input type='hidden' id="date_contenu4" value="<?php print $datepour ?>" >
<input type='hidden' id="saisie_idclsorgrp4" value="<?php print $list1?>" >
<input type='hidden' id="saisie_clsorgrp4" value="<?php print $list2?>" >
<input type='hidden' id="sClasseGrp4" value="<?php print $sClasseGrp?>" >
<input type='hidden' id="sMat4" value="<?php print $sMat?>" >
</form>


  </div>
</div>
<?php
if (isset($_GET["aff"])) $choix=$_GET["aff"]; 
if ($choix == "") $choix=0;
?>
<SCRIPT language='JavaScript'>InitBulle('#000000','#FCE4BA','red',1);</SCRIPT>
<script type="text/javascript">initTabs('dhtmlgoodies_tabView1',Array('<?php print addslashes(LANGMESS92) ?>','<?php print addslashes(LANGMESS95) ?>','<?php print preg_replace('/à/','&agrave;',addslashes(LANGMESS98)) ?>','<?php print addslashes(LANGMESS99) ?>'),<?php print $choix ?>,'100%',750,Array(false,false,false,false));</script> 

<input type='hidden' id='choix' value='0' /> 

<!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION["membre"] == "menuadmin") :
            print "<SCRIPT language='JavaScript' ";
		print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
 	        print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
      		print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";
            top_d();
            print "<SCRIPT language='JavaScript' ";
      		print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";
       endif ;
?>
<script>
function savecontenu() {
	attente();

	var choix=document.getElementById('choix').value;


	tinyMCE.triggerSave();
	var ok1='0';
	var ok2='0';
	var ok3='0';
	var ok4='0';
//------------------------------------------------------------------------------------------------------------
	var saisie_idmatiere=document.getElementById('saisie_idmatiere1').value;
	var tempsestime=document.getElementById('tempsestime1').value;
	var date_contenu=document.getElementById('date_contenu1').value;
	var saisie_idclsorgrp=document.getElementById('saisie_idclsorgrp1').value;
	var saisie_clsorgrp=document.getElementById('saisie_clsorgrp1').value;
	var sClasseGrp=document.getElementById('sClasseGrp1').value;
	var sMat=document.getElementById('sMat1').value;
	var saisie_contenu=encodeURIComponent(document.getElementById('elm1').value);
	var num=document.getElementById('number1').value;
	var myAjax = new Ajax.Request(
		"ajaxEnrDevoir.php",
		{	method: "post",
			asynchronous: false,
			parameters: "etape=1&saisie_idmatiere="+saisie_idmatiere+"&tempsestime="+tempsestime+"&date_contenu="+date_contenu+"&saisie_idclsorgrp="+saisie_idclsorgrp+"&saisie_clsorgrp="+saisie_clsorgrp+"&sClasseGrp="+sClasseGrp+"&sMat="+sMat+"&saisie_contenu="+saisie_contenu+"&number="+num,
			timeout: 5000,
			onComplete: function (request) {
				if ("ok" == request.responseText)  {
				 	ok1="1"; 
				}else{
					ok1="0";    
				}
			}
		}
	);
// ----------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------
	var saisie_idmatiere=document.getElementById('saisie_idmatiere2').value;
	var tempsestime=document.getElementById('tempsestime2').value;
	var date_contenu=document.getElementById('date_contenu2').value;
	var saisie_idclsorgrp=document.getElementById('saisie_idclsorgrp2').value;
	var saisie_clsorgrp=document.getElementById('saisie_clsorgrp2').value;
	var sClasseGrp=document.getElementById('sClasseGrp2').value;
	var sMat=document.getElementById('sMat2').value;
	var saisie_contenu=escape(document.getElementById('elm2').value);
	var num=document.getElementById('number2').value;
	var myAjax = new Ajax.Request(
		"ajaxEnrDevoir.php",
		{	method: "post",
			asynchronous: false,
			parameters: "etape=2&saisie_idmatiere="+saisie_idmatiere+"&tempsestime="+tempsestime+"&date_contenu="+date_contenu+"&saisie_idclsorgrp="+saisie_idclsorgrp+"&saisie_clsorgrp="+saisie_clsorgrp+"&sClasseGrp="+sClasseGrp+"&sMat="+sMat+"&saisie_contenu="+saisie_contenu+"&number="+num,
			timeout: 5000,
			onComplete: function (request) {
				if ("ok" == request.responseText)  {
					ok2="1"; 
				}else{
					ok2="0";    
				}
			}
		}
	);
// ----------------------------------------------------------------------------------------------------------------
	var saisie_idmatiere=document.getElementById('saisie_idmatiere3').value;
	var date_contenu=document.getElementById('date_contenu3').value;
	var saisie_idclsorgrp=document.getElementById('saisie_idclsorgrp3').value;
	var saisie_clsorgrp=document.getElementById('saisie_clsorgrp3').value;
	var sClasseGrp=document.getElementById('sClasseGrp3').value;
	var sMat=document.getElementById('sMat3').value;
	var saisie_contenu=escape(document.getElementById('elm3').value);
	var num=document.getElementById('number3').value;
	var date_devoir=document.getElementById('date_devoir3').value;
	var selectElmt=document.getElementById('tempsestime3');
        var tempsestime=selectElmt.options[selectElmt.selectedIndex].value;

	var myAjax = new Ajax.Request(
		"ajaxEnrDevoir.php",
		{	method: "post",
			asynchronous: false,
			parameters: "etape=3&saisie_idmatiere="+saisie_idmatiere+"&date_contenu="+date_contenu+"&saisie_idclsorgrp="+saisie_idclsorgrp+"&saisie_clsorgrp="+saisie_clsorgrp+"&sClasseGrp="+sClasseGrp+"&sMat="+sMat+"&saisie_contenu="+saisie_contenu+"&number="+num+"&date_devoir="+date_devoir+"&tempsestime="+tempsestime,
			timeout: 5000,
			onComplete: function (request) {
				if ("ok" == request.responseText)  {
					ok3="1"; 
				}else{
					ok3="0";    
				}
			}
		}
	);
// ----------------------------------------------------------------------------------------------------------------
	var saisie_idmatiere=document.getElementById('saisie_idmatiere4').value;
	var date_contenu=document.getElementById('date_contenu4').value;
	var saisie_idclsorgrp=document.getElementById('saisie_idclsorgrp4').value;
	var saisie_clsorgrp=document.getElementById('saisie_clsorgrp4').value;
	var sClasseGrp=document.getElementById('sClasseGrp4').value;
	var sMat=document.getElementById('sMat4').value;
	var saisie_contenu=escape(document.getElementById('elm4').value);
	var myAjax = new Ajax.Request(
		"ajaxEnrDevoir.php",
		{	method: "post",
			asynchronous: false,
			parameters: "etape=4&saisie_idmatiere="+saisie_idmatiere+"&date_contenu="+date_contenu+"&saisie_idclsorgrp="+saisie_idclsorgrp+"&saisie_clsorgrp="+saisie_clsorgrp+"&sClasseGrp="+sClasseGrp+"&sMat="+sMat+"&saisie_contenu="+saisie_contenu,
			timeout: 5000,
			onComplete: function (request) {
				if ("ok" == request.responseText)  {
					ok4="1"; 
				}else{
					ok4="0";    
				}
			}
		}
	);
// ----------------------------------------------------------------------------------------------------------------
	if (ok1 && ok2 && ok3 && ok4) {
		//
	}else{
		alert("Problème d'enregistrement !!!");
	}	
	attente_close();
	open("cahiertext2.php?aff="+choix+"&date_convenu=<?php print $datepour ?>&sClasseGrp=<?php print $sClasseGrp ?>&sMat=<?php print $sMat ?>","_self","");
}




function updatefichier(item) {
	if (item == "ok") {
		ajaxActualisePieceJointeCahierText1('<?php print $idpiecejointe1 ?>','listingpiecejointe','1');
		ajaxActualisePieceJointeCahierText2('<?php print $idpiecejointe2 ?>','listingpiecejointe','2');	
		ajaxActualisePieceJointeCahierText3('<?php print $idpiecejointe3 ?>','listingpiecejointe','3');
	}
}

updatefichier("ok"); 

</script>

</BODY>
</HTML>

<?php @Pgclose() ?>
