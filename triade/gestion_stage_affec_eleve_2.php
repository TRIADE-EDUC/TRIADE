<?php
session_start();
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
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade�, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<meta charset="utf-8">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajaxStage.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_entreprise_centrale.js"></script>
<script type="text/javascript" src="./librairie_js/xorax_serialize.js" ></script>
<script type="text/javascript" src="./unserialize-js/phpUnserialize.js" ></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"]?></title>
<style>#coulBar0 { background-image: none; }</style>
<script>
function AffectDateStage(val) {
	var elem = val.split('#||#');
	value= elem[0];
	deb = elem[1];
	fin = elem[2];
	nomstage = elem[3];
	numero = "-"+elem[4];

	// $data[$i][2]."#||#$dateDebut#||#$datefin#||#$nomstage|##|$num

	document.formulaire.debutdate.value=deb;
	document.formulaire.findate.value=fin;
	document.formulaire.num.value=numero;
	document.formulaire.nom_stage.value=nomstage;
}
</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("3");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1'><?php print LANGSTAGE75 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<?php
if (isset($_GET["id"])) {
	$prenom=recherche_eleve_prenom($_GET["id"]);
	$nom=recherche_eleve_nom($_GET["id"]);
}
$p=''; $urlcentrale='';
if (file_exists("./common/config.centralStageClient.php")) {
	include_once("./common/config.centralStageClient.php");
	$urlcentrale=URLCENTRALSTAGE;
	$p=PASSCENTRALSTAGE;
}
?>

<form method=post action="gestion_stage_affec_eleve.php" name="formulaire" onsubmit="return validestageeleve()">
<input type=hidden name=ideleve value="<?php print $_GET["id"]?>">
<input type=hidden name=saisie_classe value="<?php print $_GET["idclasse"]?>">
<input type='hidden' id="debutdate" name="debutdate">
<input type='hidden' id="findate" name="findate">
<input type='hidden' id="num" name="num" value="">
<input type='hidden' id="nom_stage" name="nom_stage">
<input type='hidden' id="idclasse" name="idclasse" value="<?php print $_GET["idclasse"]?>">
<input type="hidden" name='nom_entreprise_via_central' id='nom_entreprise_via_central'>
<input type="hidden" name="registrecommerce" id="registrecommerce">
<input type="hidden" name="siren" id="siren"><input type="hidden" name="siret" id="siret">
<input type="hidden" name="formejuridique" id="formejuridique"><input type="hidden" name="secteureconomique" id="secteureconomique">
<input type="hidden" name="INSEE" id="INSEE"><input type="hidden" name="NAFAPE" id="NAFAPE">
<input type="hidden" name="NACE" id="NACE"><input type="hidden" name="typeorganisation" id="typeorganisation">
<input type="hidden" name="contact" id="contact"><input type="hidden" name="fonction" id="fonction">
<input type="hidden" name="adressesiege" id="adressesiege"><input type="hidden" name="activite" id="activite">
<input type="hidden" name="activite2" id="activite2"><input type="hidden" name="activite3" id="activite3">
<input type="hidden" name="activiteprin" id="activiteprin"><input type="hidden" name="grphotelier" id="grphotelier">
<input type="hidden" name="nbetoile" id="nbetoile"><input type="hidden" name="nbchambre" id="nbchambre">
<input type="hidden" name="email" id="email"><input type="hidden" name="siteweb" id="siteweb">
<input type="hidden" name="information" id="information">

<!-- Élève -->
<div class="card" style="margin:8px 0">
  <div class="card-header"><span class="card-title"><i class="bi bi-person-fill"></i> Élève</span></div>
  <div style="padding:10px 14px">
    <div class="form-row"><label><?php print LANGNA1 ?></label><input type=text size=30 readonly value="<?php print strtolower($nom)?>"></div>
    <div class="form-row"><label><?php print LANGNA2 ?></label><input type=text size=30 readonly value="<?php print strtolower($prenom)?>"></div>
  </div>
</div>

<!-- Période du stage -->
<div class="card" style="margin:8px 0">
  <div class="card-header"><span class="card-title"><i class="bi bi-calendar3"></i> Période du stage</span></div>
  <div style="padding:10px 14px">
    <div class="form-row"><label>Période désirée</label>
    <?php
    if ($urlcentrale != '') {
      print "<select id='periode' name='periode' onchange=\"AffectDateStage(this.value)\">";
      print "<script src='$urlcentrale/ajaxPeriodeCentraleStage.php?productid=".PRODUCTID."&p=$p' ></script>";
      print "</select>";
    }else{
      $data=periodeStageCentralDate();
    ?>
      <select id='periode' name='periode' onchange="AjaxAffectDateStage(this.value,'<?php print $urlcentrale ?>','<?php print $p ?>','<?php print PRODUCTID ?>');document.formulaire.create.disabled=false;document.getElementById('alerte').style.display='none';">
      <option value='0'><?php print LANGCHOIX ?></option>
      <?php for($i=0;$i<countTriade($data);$i++) { print "<option value='".$data[$i][2]."' >(".$data[$i][3].") ".dateForm($data[$i][0])." - ".dateForm($data[$i][1])."</option>"; } ?>
      </select>
    <?php } ?>
    </div>
    <div class="form-row"><label>Période (Trim. / Semestre)</label>
    <select name='trim'>
      <option value=''></option>
      <optgroup label="Trimestre">
        <option value='T1'>Trimestre 1</option><option value='T2'>Trimestre 2</option><option value='T3'>Trimestre 3</option>
      <optgroup label="Semestre">
        <option value='S1'>Semestre 1</option><option value='S2'>Semestre 2</option>
    </select></div>
    <div class="form-row" style="align-items:flex-start"><label><?php print LANGSTAGE48 ?></label><div><?php checkbox_stage($_GET["idclasse"]); ?></div></div>
    <div class="form-row" style="align-items:flex-start"><label><?php print LANGSTAGE108 ?></label>
    <div>
      <label style="font-weight:400"><input type=checkbox name="alternance" value="1" onclick="checkStage();document.formulaire.create.disabled=false;document.getElementById('alerte').style.display='none';" id="newstage"> Oui</label>
      <span style="font-size:11px;color:#555"> du <input type='text' name="dateDebutAlternance" size='10' id="debutstage" disabled='disabled' value="jj/mm/aaaa" onKeyPress="onlyChar(event)"> au <input type='text' name="dateFinAlternance" id="finstage" size=10 disabled='disabled' value="jj/mm/aaaa" onKeyPress="onlyChar(event)"></span>
      <div style="margin-top:6px;display:flex;flex-wrap:wrap;gap:8px;font-size:12px">
        <label style="font-weight:400"><input type=checkbox name="jourstage[]" value="1" onclick='checkStage()' id="j1" disabled='disabled'> <?php print LANGL ?></label>
        <label style="font-weight:400"><input type=checkbox name="jourstage[]" value="2" onclick='checkStage()' id="j2" disabled='disabled'> <?php print LANGM ?></label>
        <label style="font-weight:400"><input type=checkbox name="jourstage[]" value="3" onclick='checkStage()' id="j3" disabled='disabled'> <?php print LANGME ?></label>
        <label style="font-weight:400"><input type=checkbox name="jourstage[]" value="4" onclick='checkStage()' id="j4" disabled='disabled'> <?php print LANGJ ?></label>
        <label style="font-weight:400"><input type=checkbox name="jourstage[]" value="5" onclick='checkStage()' id="j5" disabled='disabled'> <?php print LANGV ?></label>
        <label style="font-weight:400"><input type=checkbox name="jourstage[]" value="6" onclick='checkStage()' id="j6" disabled='disabled'> <?php print LANGS ?></label>
        <label style="font-weight:400"><input type=checkbox name="jourstage[]" value="7" onclick='checkStage()' id="j7" disabled='disabled'> <?php print LANGD ?></label>
      </div>
    </div></div>
  </div>
</div>

<!-- Entreprise -->
<div class="card" style="margin:8px 0">
  <div class="card-header"><span class="card-title"><i class="bi bi-building"></i> Entreprise</span></div>
  <div style="padding:10px 14px">
    <div class="form-row" style="align-items:center"><label><?php print LANGSTAGE74 ?></label>
    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
      <select name='ident' onchange="checkList(this.value,'<?php print $urlcentrale ?>','<?php print $p?>','<?php print PRODUCTID ?>')" id='ident'>
        <option><?php print LANGCHOIX ?></option>
        <?php select_entreprise_limit("25"); if ($urlcentrale != '') { print "<optgroup label='Central de Stage'>"; print "<script src='$urlcentrale/ajaxEntrepriseCentraleStage.php?productid=".PRODUCTID."&p=$p' ></script>"; } ?>
      </select>
      <a href='gestion_stage_ent_ajout.php?lien=gestion_stage_affec_eleve_2.php&lienideleve=<?php print $_GET["id"]?>&lienidclasse=<?php print $_GET["idclasse"] ?>' title='Ajouter une entreprise' class="btn btn-secondary btn-sm"><i class="bi bi-plus-circle"></i></a>
    </div></div>
    <div class="form-row"><label><?php print LANGSTAGE76 ?></label><input type=text name='lieu' id='lieu'></div>
    <div class="form-row"><label><?php print LANGSTAGE29 ?></label><input type=text size=10 name='postal' id='postal'></div>
    <div class="form-row"><label><?php print LANGSTAGE30 ?></label><input type=text name='ville' id='ville'></div>
    <div class="form-row"><label><?php print LANGSTAGE109 ?></label><input type=text name='pays' id='pays'></div>
  </div>
</div>

<!-- Contacts -->
<div class="card" style="margin:8px 0">
  <div class="card-header"><span class="card-title"><i class="bi bi-people-fill"></i> Contacts</span></div>
  <div style="padding:10px 14px">
    <div class="form-row"><label><?php print LANGSTAGE77 ?></label><input type=text name='responsable' id='responsable'></div>
    <div class="form-row"><label>Autre <?php print strtolower(LANGSTAGE77) ?></label><input type=text name='responsable2' id='responsable2'></div>
    <div class="form-row"><label><?php print LANGABS38 ?></label><input type=text name='tel' id='tel'></div>
    <div class="form-row"><label>Fax</label><input type=text name='fax' id='fax'></div>
    <div class="form-row"><label><?php print LANGSTAGE110 ?></label><select name='idtuteur' id='idtuteur'></select></div>
  </div>
</div>

<!-- Encadrement -->
<div class="card" style="margin:8px 0">
  <div class="card-header"><span class="card-title"><i class="bi bi-person-badge"></i> Encadrement</span></div>
  <div style="padding:10px 14px">
    <div class="form-row"><label><?php print LANGSTAGE78 ?> 1</label>
      <select name=idprof><option><?php print LANGCHOIX ?></option><?php select_personne_2('ENS',25); ?></select></div>
    <div class="form-row"><label>Date de la visite 1</label>
      <input type=text size=12 name="date" id='date1' class=bouton2 onKeyPress="onlyChar(event)">
      <?php include_once("librairie_php/calendar.php"); calendarDim("id1","document.formulaire.date",$_SESSION["langue"],"0"); ?>
    </div>
    <div class="form-row" style="margin-top:8px"><label><?php print LANGSTAGE78 ?> 2</label>
      <select name=idprof2><option><?php print LANGCHOIX ?></option><?php select_personne_2('ENS',25); ?></select></div>
    <div class="form-row"><label>Date de la visite 2</label>
      <input type=text size=12 name="date2" id='date2' class=bouton2 onKeyPress="onlyChar(event)">
      <?php calendarDim("id2","document.formulaire.date2",$_SESSION["langue"],"0"); ?>
    </div>
  </div>
</div>

<!-- Modalités -->
<div class="card" style="margin:8px 0">
  <div class="card-header"><span class="card-title"><i class="bi bi-sliders"></i> Modalités</span></div>
  <div style="padding:10px 14px">
    <div class="form-row"><label><?php print LANGSTAGE111 ?></label><input type=text name='langue' id='langue' maxlength='200'></div>
    <div class="form-row"><label><?php print LANGSTAGE112 ?></label><input type=text name='service' id='service' maxlength='200'></div>
    <div class="form-row"><label><?php print LANGSTAGE113 ?></label><input type=text name='indemnitestage' id='indemnitestage' maxlength='200'></div>
    <div class="form-row"><label><?php print LANGSTAGE79 ?></label>
      <label style="font-weight:400"><input type=radio name="loge" value=1> <?php print LANGOUI ?></label>&nbsp;&nbsp;
      <label style="font-weight:400"><input type=radio name="loge" value=0 checked> <?php print LANGNON ?></label></div>
    <div class="form-row"><label><?php print LANGSTAGE80 ?></label>
      <label style="font-weight:400"><input type=radio value=1 name="nourri"> <?php print LANGOUI ?></label>&nbsp;&nbsp;
      <label style="font-weight:400"><input type=radio name="nourri" value=0 checked> <?php print LANGNON ?></label></div>
    <div class="form-row"><label><?php print LANGSTAGE114 ?></label>
      Début : <input type=text name="horairedebutjournalier" size=5 value='hh:mm' onKeyPress="onlyChar2(event)"> &nbsp;Fin : <input type=text name="horairefinjournalier" size=5 value='hh:mm' onKeyPress="onlyChar2(event)"></div>
    <div class="form-row"><label><?php print nbsp(LANGSTAGE81) ?></label>
      <label style="font-weight:400"><input type=radio name="xservice" value=1> <?php print LANGOUI ?></label>&nbsp;&nbsp;
      <label style="font-weight:400"><input type=radio name="xservice" value=0 checked> <?php print LANGNON ?></label></div>
  </div>
</div>

<!-- Observations -->
<div class="card" style="margin:8px 0">
  <div class="card-header"><span class="card-title"><i class="bi bi-chat-text"></i> Observations</span></div>
  <div style="padding:10px 14px">
    <div class="form-row" style="align-items:flex-start"><label><?php print nbsp(LANGSTAGE82) ?></label><textarea cols=38 rows=3 name=raison></textarea></div>
    <div class="form-row" style="align-items:flex-start"><label><?php print nbsp(LANGSTAGE83) ?></label><textarea cols=38 rows=3 name=info></textarea></div>
  </div>
</div>

<div id='alerte' class="alert alert-warning" style="font-size:11px;margin:8px 0"><i class="bi bi-exclamation-triangle-fill"></i> Indiquer le numéro de stage ou du stage personnalisé</div>

<div class="toolbar" style="margin:10px 0">
  <script language=JavaScript>buttonMagicSubmit3("<?php print LANGBT7?>","create","disabled='disabled'");</script>
  <script language=JavaScript>buttonMagicRetour('gestion_stage_affec_eleve.php','_self');</script>
</div>
</form>
</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
     	print "<SCRIPT type='text/javascript' ";
       	print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
       	print "</SCRIPT>";
}else{
       	print "<SCRIPT type='text/javascript' ";
      	print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
      	print "</SCRIPT>";
      	top_d();
      	print "<SCRIPT type='text/javascript' ";
      	print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
	print "</SCRIPT>";
}
// deconnexion en fin de fichier
Pgclose();
?>
<script>
function checkStage() {
	
	if (document.getElementById("newstage").checked == true) {
		document.getElementById("debutstage").disabled=false;
		document.getElementById("finstage").disabled=false;
		document.getElementById("j1").disabled=false;
		document.getElementById("j2").disabled=false;
		document.getElementById("j3").disabled=false;
		document.getElementById("j4").disabled=false;
		document.getElementById("j5").disabled=false;
		document.getElementById("j6").disabled=false;
		document.getElementById("j7").disabled=false;
		var nbstage=document.getElementById("nbstage").value;
		for(var i=0;i<nbstage;i++) {
			document.getElementById("idstage"+i).checked=false;
			document.getElementById("idstage"+i).disabled=true;
		}

	}else{
		document.getElementById("debutstage").value="jj/mm/aaaa";
		document.getElementById("finstage").value="jj/mm/aaaa";		
		document.getElementById("debutstage").disabled=true;
		document.getElementById("finstage").disabled=true;
		document.getElementById("j1").checked=false;
		document.getElementById("j2").checked=false;
		document.getElementById("j3").checked=false;
		document.getElementById("j4").checked=false;
		document.getElementById("j5").checked=false;
		document.getElementById("j6").checked=false;
		document.getElementById("j7").checked=false;
		document.getElementById("j1").disabled=true;
		document.getElementById("j2").disabled=true;
		document.getElementById("j3").disabled=true;
		document.getElementById("j4").disabled=true;
		document.getElementById("j5").disabled=true;
		document.getElementById("j6").disabled=true;
		document.getElementById("j7").disabled=true;
		var nbstage=document.getElementById("nbstage").value;
		for(var i=0;i<nbstage;i++) {
			document.getElementById("idstage"+i).checked=false;
			document.getElementById("idstage"+i).disabled=false;
		}
	}
}
</script>
</BODY>
</HTML>
