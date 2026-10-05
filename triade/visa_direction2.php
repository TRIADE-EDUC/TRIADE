<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["annee_scolaire"])) {
        $anneeScolaire=$_POST["annee_scolaire"];
        setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}
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
include_once("./librairie_php/recupnoteperiode.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TYPE="text/css" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TYPE="text/css" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/ajaxIA.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
<style>
.vd-student {
  display:flex;
  align-items:flex-start;
  gap:14px;
  padding:14px 10px 10px;
}
.vd-photo img {
  box-shadow:0 4px 12px rgba(0,0,0,.25);
  border-radius:8px;
  display:block;
}
.vd-body {
  flex:1;
  min-width:0;
}
.vd-name {
  font-family:Electrolize,Arial,sans-serif;
  font-size:14px;
  font-weight:700;
  color:#080A66;
  margin-bottom:8px;
  display:flex;
  align-items:center;
  gap:6px;
}
.vd-name i { color:#4a5568; font-size:15px; }
.vd-textarea {
  width:100%;
  border:1px solid #c5cae9;
  border-radius:6px;
  padding:8px 10px;
  font-size:13px;
  font-family:inherit;
  resize:vertical;
  background:#fafbff;
  box-sizing:border-box;
  transition:border-color .2s;
}
.vd-textarea:focus { outline:none; border-color:#080A66; background:#fff; }
.vd-footer {
  display:flex;
  align-items:center;
  gap:10px;
  margin-top:8px;
  flex-wrap:wrap;
}
.vd-counter {
  font-size:12px;
  color:#6b7280;
  display:flex;
  align-items:center;
  gap:4px;
}
.vd-counter input[type=text] {
  width:44px;
  border:1px solid #d1d5db;
  border-radius:4px;
  padding:2px 5px;
  font-size:12px;
  text-align:center;
  background:#f9fafb;
}
.vd-copilot {
  display:inline-flex;
  align-items:center;
  gap:6px;
  background:linear-gradient(135deg,#080A66,#3949ab);
  color:#fff;
  border:none;
  border-radius:6px;
  padding:6px 14px;
  font-size:12px;
  font-family:Electrolize,Arial,sans-serif;
  font-weight:600;
  cursor:pointer;
  letter-spacing:.4px;
  transition:opacity .2s;
}
.vd-copilot:hover { opacity:.88; }
.vd-copilot i { font-size:13px; }
.vd-badges {
  padding:4px 10px 10px 10px;
  display:flex;
  align-items:center;
  gap:10px;
  flex-wrap:wrap;
  font-size:12px;
  color:#374151;
}
.vd-badges label {
  display:inline-flex;
  align-items:center;
  gap:4px;
  cursor:pointer;
  padding:3px 8px;
  border-radius:4px;
  border:1px solid #e2e8f0;
  background:#f8faff;
  transition:background .15s;
}
.vd-badges label:hover { background:#e8eeff; }
.vd-sep { border:none; border-top:1px solid #e2e8f0; margin:0; }
.vd-consult {
  display:inline-flex;
  align-items:center;
  gap:6px;
  background:#fff;
  color:#080A66;
  border:1.5px solid #fff;
  border-radius:6px;
  padding:5px 14px;
  font-size:12px;
  font-family:Electrolize,Arial,sans-serif;
  font-weight:600;
  cursor:pointer;
  transition:background .2s,color .2s;
}
.vd-consult:hover { background:#e8eeff; color:#080A66; }
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if ($_SESSION["membre"] == "menupersonnel") {
	if (!verifDroit($_SESSION["id_pers"],"visadirection")) {
		Pgclose();
		accesNonReserveFen();
		exit();
	}
}elseif ($_SESSION["membre"] == "menuadmin") {
	validerequete("menuadmin");
}else{
	if (PROFPACCESVISADIRECTION == "oui") {
		validerequete("menuprof");
		verif_profp_class($_SESSION["id_pers"],$_SESSION["profpclasse"]);
	}else{
		validerequete("menuadmin");
	}
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGMESS356 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

&nbsp;&nbsp;<font class='T2'><?php print LANGMESS355 ?> : </font><input type='button' class="vd-consult" value="Consulter" onclick="open('recupComClasse.php?idclasse=<?php print $_POST["saisie_classe"] ?>&tri=<?php print $_POST["saisie_trimestre"] ?>&annee_scolaire=<?php print $_POST["annee_scolaire"]?>','_self','')" />

<form method=post name="formulaire" action="visa_direction3.php">

<?php
if (isset($_POST["consult"])) {
	$saisie_classe=$_POST["saisie_classe"];

	 $sql=" SELECT s.* FROM ( SELECT libelle,elev_id,nom,prenom,date_naissance,regime,numero_eleve,code_compta,nomtuteur,prenomtuteur,civ_1,telephone,email FROM {$prefixe}eleves ,{$prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' AND annee_scolaire='$anneeScolaire' UNION ALL SELECT c.libelle,e.elev_id,e.nom,e.prenom,e.date_naissance,e.regime,e.numero_eleve,e.code_compta,e.nomtuteur,e.prenomtuteur,e.civ_1,e.telephone,e.email FROM {$prefixe}eleves e ,{$prefixe}classes c, {$prefixe}eleves_histo h WHERE h.idclasse='$saisie_classe' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire') s  ORDER BY s.nom";

	$res=execSql($sql);
	$data=chargeMat($res);

	$cl=$data[0][0];
	$tri=$_POST["saisie_trimestre"];

	if (defined("NBCARBULLPROFP")) { $nbcar=NBCARBULLPROFP; }else{ $nbcar="500"; }

	// Dates trimestre + sujets pour la moyenne générale COPILOT
	$dateRecup=recupDateTrimByIdclasse($tri,$saisie_classe);
	$dateDebutIA=isset($dateRecup[0][0]) ? dateForm($dateRecup[0][0]) : '';
	$dateFinIA=isset($dateRecup[0][1]) ? dateForm($dateRecup[0][1]) : '';
	$ordreIA=ordre_matiere($saisie_classe);

	print "<br /><font class=T2>&nbsp;&nbsp;&nbsp;".LANGELE4." : <b>$cl</b></font><br />";
	print "<br /><font class=T2>&nbsp;&nbsp;&nbsp;".LANGTMESS465." $tri  /  $anneeScolaire</font><br /><br />";

	print "<table align=center width='100%' border=0 cellspacing=0 cellpadding=0>";

	if( countTriade($data) > 0 ) {

		for($i=0;$i<countTriade($data);$i++) {

			$ideleve=$data[$i][1];

			$com=recherche_com($ideleve,$tri,$_POST["type_bulletin"],$anneeScolaire);
			$com=preg_replace('/\\\r\\\n/','',$com);
			$com=stripslashes($com);
			$nbtexte=strlen($com);

			// Moyenne générale élève pour COPILOT
			$noteMG=0; $coefMG=0;
			for($j=0;$j<count($ordreIA);$j++) {
				$idMat=$ordreIA[$j][0]; $numOrd=$ordreIA[$j][2];
				$idprofMG=recherche_prof($idMat,$saisie_classe,$numOrd);
				if (!verifMatiereAvecGroupe($idMat,$ideleve,$saisie_classe,$numOrd)) {
					$noteMat=moyenneEleveMatiere($ideleve,$idMat,$dateDebutIA,$dateFinIA,$idprofMG);
					$coefMat=recupCoeff($idMat,$saisie_classe,$numOrd);
					if ($noteMat != "") { $noteMG+=$noteMat*$coefMat; $coefMG+=$coefMat; }
				}
			}
			$moyGenIA=($coefMG>0) ? number_format($noteMG/$coefMG,2,'.','') : '';
			$prenomIA=ucfirst($data[$i][3]);

			if (file_exists("./common/config-ia.php")) {
				include_once("common/productId.php");
				include_once("common/config-ia.php");
				$productID=PRODUCTID;
				$iakey=IAKEY;
				$lienIA="ajaxIAVisaDir('$i','$productID','$iakey','comm_$i','$prenomIA','$moyGenIA')";
			}else{
				$lienIA="alert('Votre Triade n\\'est pas configuré pour utiliser l\\'IA. Contacter votre administrateur Triade')";
			}

			print "<tr><td colspan='2'>";
			print "<input type=hidden value=\"".$data[$i][1]."\" name='eleveid_$i' />";
			print "<div class='vd-student'>";
			print "<div class='vd-photo'><img src=\"image_trombi.php?idE=".$ideleve."\" border='0'></div>";
			print "<div class='vd-body'>";
			print "<div class='vd-name'><i class='bi bi-person-fill'></i>".ucfirst($data[$i][3])." ".strtoupper($data[$i][2])."</div>";
			print "<textarea class='vd-textarea' cols=60 rows=5 name='comm_$i' onkeypress=\"compter(this,'$nbcar', this.form.CharRestant_$i)\" id='comm_$i'>$com</textarea>";
			print "<div class='vd-footer'>";
			print "<span class='vd-counter'><i class='bi bi-type'></i><input type=text name='CharRestant_$i' size=3 disabled='disabled' value='$nbtexte' /></span>";
			print "<button type='button' class='vd-copilot' id='bt_copilot_$i' onClick=\"$lienIA\"><i class='bi bi-stars'></i>TRIADE-COPILOT</button>";
			print "</div>";
			print "</div></div>";
			print "</td></tr>";

			if (($_POST["type_bulletin"] == "montessori") || ($_POST["type_bulletin"] == "montessori_spec")){
				$montessori=recherchemontessori($ideleve,$_POST["type_bulletin"],$tri,$anneeScolaire);
				$montessori=$montessori[0][0];
				if (trim($montessori) == "felicitation")  { $checkedmont1="checked='checked'"; }else{ $checkedmont1=""; }
				if ($montessori == "satisfaction")  { $checkedmont2="checked='checked'"; }else{ $checkedmont2=""; }
				if ($montessori == "encouragement") { $checkedmont3="checked='checked'"; }else{ $checkedmont3=""; }
				print "<tr><td colspan='2'><div class='vd-badges'>";
				print "<label><input type='radio' name='montessori_${ideleve}' value='' /> Aucun</label>";
				print "<label><input type='radio' name='montessori_${ideleve}' value='felicitation' $checkedmont1 /> Félicitations</label>";
				print "<label><input type='radio' name='montessori_${ideleve}' value='satisfaction' $checkedmont2 /> Satisfactions</label>";
				print "<label><input type='radio' name='montessori_${ideleve}' value='encouragement' $checkedmont3 /> Encouragements</label>";
				print "</div></td></tr>";
			}

			if ($_POST["type_bulletin"] == "univproafrique") {
				$montessori=recherchemontessori($ideleve,$_POST["type_bulletin"],$tri,$anneeScolaire);
				$montessori=$montessori[0][0];
				if (trim($montessori) == "Insuffisant")  { $checkedmont1="checked='checked'"; }else{ $checkedmont1=""; }
				if ($montessori == "Satisfaisant")  { $checkedmont2="checked='checked'"; }else{ $checkedmont2=""; }
				if ($montessori == "Excellent") { $checkedmont3="checked='checked'"; }else{ $checkedmont3=""; }
				print "<tr><td colspan='2'><div class='vd-badges'>";
				print "<span style='font-weight:600;color:#374151'>Vie associative :</span>";
				print "<label><input type='radio' name='montessori_${ideleve}' value='' /> Supprimé</label>";
				print "<label><input type='radio' name='montessori_${ideleve}' value='Insuffisant' $checkedmont1 /> Insuffisant</label>";
				print "<label><input type='radio' name='montessori_${ideleve}' value='Satisfaisant' $checkedmont2 /> Satisfaisant</label>";
				print "<label><input type='radio' name='montessori_${ieleve}' value='Excellent' $checkedmont3 /> Excellent</label>";
				print "</div></td></tr>";
			}

			if ($_POST["type_bulletin"] == "cheneraie") {
				$montessori=recherchemontessori($ideleve,$_POST["type_bulletin"],$tri,$anneeScolaire);
				$montessori=$montessori[0][0];
				if ($montessori == "felicitation")  { $checkedmont1="checked='checked'"; }else{ $checkedmont1=""; }
				if ($montessori == "compliment")  { $checkedmont2="checked='checked'"; }else{ $checkedmont2=""; }
				if ($montessori == "encouragement") { $checkedmont3="checked='checked'"; }else{ $checkedmont3=""; }
				if ($montessori == "averttravail") { $checkedmont4="checked='checked'"; }else{ $checkedmont4=""; }
				print "<tr><td colspan='2'><div class='vd-badges'>";
				print "<label><input type='radio' name='montessori_${ideleve}' value='' /> Aucun</label>";
				print "<label><input type='radio' name='montessori_${ideleve}' value='felicitation' $checkedmont1 /> Félicitations</label>";
				print "<label><input type='radio' name='montessori_${ideleve}' value='compliment' $checkedmont2 /> Compliments</label>";
				print "<label><input type='radio' name='montessori_${ideleve}' value='encouragement' $checkedmont3 /> Encouragements</label>";
				print "<label><input type='radio' name='montessori_${ideleve}' value='averttravail' $checkedmont4 /> Avertissement de travail</label>";
				print "</div></td></tr>";
			}

			if ($_POST["type_bulletin"] == "pigierparis") {
				$leap=rechercheleap($ideleve,$_POST["type_bulletin"],$tri,$anneeScolaire);
				if ($leap[0][4]  == "1") { $checkedmont1="checked='checked'"; }else{ $checkedmont1=""; }
				if ($leap[0][5]  == "1") { $checkedmont2="checked='checked'"; }else{ $checkedmont2=""; }
				if ($leap[0][6]  == "1") { $checkedmont3="checked='checked'"; }else{ $checkedmont3=""; }
				if ($leap[0][7]  == "1") { $checkedmont4="checked='checked'"; }else{ $checkedmont4=""; }
				print "<tr><td colspan='2'><div class='vd-badges'>";
				print "<label><input type='checkbox' name='pp_av_trav_${ideleve}' value='1' $checkedmont1 /> Avertissement travail</label>";
				print "<label><input type='checkbox' name='pp_av_comp_${ideleve}' value='1' $checkedmont2 /> Avertissement comportement</label>";
				print "<label><input type='checkbox' name='pp_enc_${ideleve}' value='1' $checkedmont3 /> Encouragement</label>";
				print "<label><input type='checkbox' name='pp_feli_${ideleve}' value='1' $checkedmont4 /> Félicitations</label>";
				print "</div></td></tr>";
			}

			if ($_POST["type_bulletin"] == "pigierparisv2") {
				$leap=rechercheleap($ideleve,$_POST["type_bulletin"],$tri,$anneeScolaire);
				if ($leap[0][8]  == "1") { $checkedmont1="checked='checked'"; }else{ $checkedmont1=""; }
				if ($leap[0][9]  == "1") { $checkedmont2="checked='checked'"; }else{ $checkedmont2=""; }
				if ($leap[0][10]  == "1") { $checkedmont3="checked='checked'"; }else{ $checkedmont3=""; }
				if ($leap[0][11]  == "1") { $checkedmont4="checked='checked'"; }else{ $checkedmont4=""; }
				if ($leap[0][12]  == "1") { $checkedmont5="checked='checked'"; }else{ $checkedmont5=""; }
				print "<tr><td colspan='2'><div class='vd-badges'>";
				print "<label><input type='checkbox' name='pp2_av_${ideleve}' value='1' $checkedmont1 /> Avertissement</label>";
				print "<label><input type='checkbox' name='pp2_faible_${ideleve}' value='1' $checkedmont2 /> Faible</label>";
				print "<label><input type='checkbox' name='pp2_passable_${ideleve}' value='1' $checkedmont3 /> Passable</label>";
				print "<label><input type='checkbox' name='pp2_enc_${ideleve}' value='1' $checkedmont4 /> Encouragement</label>";
				print "<label><input type='checkbox' name='pp2_feli_${ideleve}' value='1' $checkedmont5 /> Félicitations</label>";
				print "</div></td></tr>";
			}

			if ($_POST["type_bulletin"] == "leap") {
				$leap=rechercheleap($ideleve,$_POST["type_bulletin"],$tri,$anneeScolaire);
				if ($leap[0][1] == "1")  { $checkedmont1="checked='checked'"; }else{ $checkedmont1=""; }
				if ($leap[0][2]  == "1") { $checkedmont2="checked='checked'"; }else{ $checkedmont2=""; }
				if ($leap[0][0]  == "1") { $checkedmont3="checked='checked'"; }else{ $checkedmont3=""; }
				if ($leap[0][3]  == "1") { $checkedmont4="checked='checked'"; }else{ $checkedmont4=""; }
				print "<tr><td colspan='2'><div class='vd-badges'>";
				print "<label><input type='checkbox' name='leap_felicitation_${ideleve}' value='1' $checkedmont1 /> Félicitations</label>";
				print "<label><input type='checkbox' name='leap_encouragement_${ideleve}' value='1' $checkedmont3 /> Encour.</label>";
				print "<label title='Mise en garde comportement'><input type='checkbox' name='leap_megcomp_${ideleve}' value='1' $checkedmont2 /> MEG Comp.</label>";
				print "<label title='Mise en garde travail'><input type='checkbox' name='leap_megtrav_${ideleve}' value='1' $checkedmont4 /> MEG Trav.</label>";
				print "</div></td></tr>";
			}

			if ($_POST["type_bulletin"] == "jtc") {
				$jtc=recherchejtc($ideleve,$_POST["type_bulletin"],$tri,$anneeScolaire);
				if ($jtc[0][0] == "1")   { $checkedmont1="checked='checked'"; }else{ $checkedmont1=""; }
				if ($jtc[0][1]  == "1")  { $checkedmont2="checked='checked'"; }else{ $checkedmont2=""; }
				if ($jtc[0][2]  == "1")  { $checkedmont3="checked='checked'"; }else{ $checkedmont3=""; }
				print "<tr><td colspan='2'><div class='vd-badges'>";
				print "<label><input type='checkbox' name='jtc_promu_${ideleve}' value='1' $checkedmont1 /> Promu</label>";
				print "<label><input type='checkbox' name='jtc_reprendre_${ideleve}' value='1' $checkedmont2 /> Classe à reprendre</label>";
				print "<label><input type='checkbox' name='jtc_orientation_${ideleve}' value='1' $checkedmont3 /> Orientation Ailleurs</label>";
				print "</div></td></tr>";
			}

			if ($_POST["type_bulletin"] == "seminaire") {
				$montessori=recherchemontessori($ideleve,$_POST["type_bulletin"],$tri,$anneeScolaire);
				if ($montessori == "felicitation")  { $checkedmont1="checked='checked'"; }else{ $checkedmont1=""; }
				if ($montessori == "tabhonneur")  { $checkedmont2="checked='checked'"; }else{ $checkedmont2=""; }
				if ($montessori == "encouragement") { $checkedmont3="checked='checked'"; }else{ $checkedmont3=""; }
				if ($montessori == "deconduite")  { $checkedmont4="checked='checked'"; }else{ $checkedmont4=""; }
				if ($montessori == "detravail") { $checkedmont5="checked='checked'"; }else{ $checkedmont5=""; }
				print "<tr><td colspan='2'><div class='vd-badges'>";
				print "<label><input type='radio' name='montessori_${ideleve}' value='' /> Aucun</label>";
				print "<label><input type='radio' name='montessori_${ideleve}' value='felicitation' $checkedmont1 /> Félicitations</label>";
				print "<label><input type='radio' name='montessori_${ideleve}' value='tabhonneur' $checkedmont2 /> Tableau d'Honneur</label>";
				print "<label><input type='radio' name='montessori_${ideleve}' value='encouragement' $checkedmont3 /> Encouragements</label>";
				print "<label><input type='radio' name='montessori_${ideleve}' value='deconduite' $checkedmont4 /> Avertissement de conduite</label>";
				print "<label><input type='radio' name='montessori_${ideleve}' value='detravail' $checkedmont5 /> Avertissement de travail</label>";
				print "</div></td></tr>";
			}

			print "<tr><td colspan='2'><hr class='vd-sep'></td></tr>";
		}

		$valider=VALIDER;
		print "<tr><td colspan=2 ><br><script language=JavaScript>buttonMagicSubmit('$valider','create');</script>";
		include_once("./librairie_php/lib_conexpersistant.php");
		connexpersistance("display:inline-flex;align-items:center;gap:4px;font-size:10px;color:#6c757d;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;padding:2px 7px;border-radius:10px;background:#f0f2fa;border:1px solid #e8eaf6;vertical-align:middle;margin-left:20px");
		print "</td></tr>";
		print '<input type=hidden name="saisie_trimestre" value="'.$tri.'" />';
		print "<input type=hidden name='saisie_classe' value=\"".$_POST["saisie_classe"]."\" />";
		print "<input type=hidden name='saisie_nb' value='".countTriade($data)."' />";
		print "<input type=hidden name='type_bulletin' value='".$_POST["type_bulletin"]."' />";
		print "<input type=hidden name='anneeScolaire' value='$anneeScolaire' />";
		print "</form>";

	}else{
		print("<tr><td align=center ><font class=T2>".LANGRECH1."</font></td></tr>");
	}
	print "</table>";
}

?>
<br><br>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>


<!-- // fin form -->
</td></tr></table>


<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
