<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta charset="utf-8">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<link rel="stylesheet" href="librairie_css/css-v4.css">
<link rel="stylesheet" href="librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<script type="text/javascript" src="./librairie_js/function.js"></script>
<script type="text/javascript" src="./librairie_js/clickdroit2.js"></script>
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script>
function downloadBull(fichier) {
	if (fichier != "") { open("visu_document.php?fichier="+fichier,'_blank',''); }
}
</script>
<style>
* { box-sizing: border-box; }
html, body { margin: 0; padding: 0; font-family: Electrolize, Trebuchet MS, Arial, sans-serif; background: #fff; font-size: 12px; }

.bull-header {
  background: linear-gradient(135deg,#080A66 0%,#1a1c8a 100%);
  color: #fff; padding: 7px 14px;
  display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px;
  position: sticky; top: 0; z-index: 10;
}
.bull-period { font-size: 12px; font-weight: 700; display: flex; align-items: center; gap: 6px; }
.bull-period i { color: #CACCEF; }
.bull-notice { font-size: 10px; color: #CACCEF; display: flex; align-items: center; gap: 4px; }
.bull-archive { font-size: 11px; color: #CACCEF; display: flex; align-items: center; gap: 6px; white-space: nowrap; }
.bull-archive select { font-size: 11px; border-radius: 3px; padding: 2px 5px; border: 1px solid #CACCEF; background: #fff; color: #222; }

.bull-wrap { padding: 0; }
.bull-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.bull-table thead th { position: sticky; top: 37px; z-index: 9; }

.bull-col-mat  { width: 38%; }
.bull-col-note { width: 11%; text-align: center !important; }
.bull-col-moy  { width: 11%; text-align: center !important; }
.bull-col-com  { }

.bull-mat { font-weight: 700; font-size: 11px; color: #1a1c2e; }
.bull-coef { font-size: 10px; color: #888; font-weight: 400; }
.bull-prof { font-size: 10px; color: #7986cb; font-style: italic; margin-top: 2px; }

.bull-note {
  display: inline-block; padding: 2px 10px; border-radius: 12px;
  font-weight: 700; font-size: 13px; letter-spacing: .01em; white-space: nowrap;
}
.bull-note-na   { background: #f5f5f5; color: #aaa; font-style: italic; font-weight: 400; font-size: 11px; }
.bull-note-low  { background: #ffebee; color: #c62828; border: 1px solid #ef9a9a; }
.bull-note-ok   { background: #e8f0fe; color: #1a237e; border: 1px solid #9fa8da; }
.bull-note-high { background: #e8f5e9; color: #1b5e20; border: 1px solid #a5d6a7; }

.bull-moy-val { font-size: 12px; color: #555; }

.bull-com { font-size: 11px; color: #444; line-height: 1.5; }
.bull-com-link { font-size: 11px; color: #080A66; text-decoration: none; line-height: 1.5; display: block; }
.bull-com-link:hover { text-decoration: underline; }

.bull-vie-row td { background: #f0f2fa; font-style: italic; }

.bull-gen-row td {
  background: linear-gradient(135deg,#080A66 0%,#1a1c8a 100%) !important;
  color: #fff !important; font-weight: 700; padding: 8px 12px !important;
  border-color: #1a1c8a !important;
}
.bull-gen-label { font-size: 12px; text-transform: uppercase; letter-spacing: .05em; color: #CACCEF; }
.bull-gen-note { font-size: 16px; font-weight: 700; }
.bull-gen-moy  { font-size: 13px; color: #CACCEF; }

.bull-star { color: #CACCEF; font-size: 9px; vertical-align: super; }
</style>
</head>
<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
$cnx=cnx();
$accesrestreint="non";
if ($_SESSION["membre"] == "menueleve") {
	$myS=$_SESSION;
	$Snom=$myS['nom']; $Sprenom=$myS['prenom']; $Seid=$myS['id_pers']; $Scid=$myS['idClasse'];
	$_GET["saisie_classe"]=$Scid; $_GET["saisie_eleve"]=$Seid;
	$accesrestreint="oui";
}
$dateRecup=recupDateTrimByIdclasse($_GET["saisie_trimestre"],$_GET["saisie_classe"]);
for($j=0;$j<countTriade($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebut=dateForm($dateDebut);
$dateFin=dateForm($dateFin);
$periode="$dateDebut au $dateFin";

if ($accesrestreint == "non") {
	$databull=recupArchiveBulletin($_GET["saisie_eleve"]);
}
?>

<!-- ── En-tête ── -->
<div class="bull-header">
  <div class="bull-period">
    <i class="bi bi-calendar-range"></i>
    Période : <?php print $periode ?>
  </div>
  <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
    <?php if ($accesrestreint == "non"): ?>
    <span class="bull-notice"><i class="bi bi-info-circle"></i> Cliquer sur la moyenne pour voir le commentaire enseignant</span>
    <span class="bull-archive">
      <i class="bi bi-archive"></i> Archive :
      <select onchange="downloadBull(this.value)">
        <option value=''><?php print LANGCHOIX ?></option>
        <?php for($i=0;$i<countTriade($databull);$i++) {
          print "<option value=\"".$databull[$i][5]."\">".$databull[$i][1]." (".$databull[$i][4].") ".$databull[$i][2]."</option>";
        } ?>
      </select>
    </span>
    <?php endif; ?>
  </div>
</div>

<!-- ── Tableau ── -->
<div class="bull-wrap">
<table class="table bull-table">
  <thead>
    <tr>
      <th class="cc-th bull-col-mat"><?php print LANGPER17 ?></th>
      <th class="cc-th bull-col-note">
        <?php print LANGPROJ14 ?>
        <?php if ($accesrestreint == "non") print "<span class='bull-star'>*</span>" ?>
      </th>
      <th class="cc-th bull-col-moy"><?php print LANGPROJ15 ?></th>
      <?php if ($accesrestreint == "non"): ?><th class="cc-th bull-col-com">Commentaire</th><?php endif; ?>
    </tr>
  </thead>
  <tbody>

<?php
$data=chercheClasse($_GET["saisie_classe"]);
$ideleverecup=$_GET["saisie_eleve"];
$classe_nom=$data[0][1];

include_once('librairie_php/recupnoteperiode.php');

$data=visu_paramViaIdSite(chercheIdSite($_GET["saisie_classe"]));
for($i=0;$i<countTriade($data);$i++) {
	$nom_etablissement=trim($data[$i][0]); $adresse=trim($data[$i][1]);
	$postal=trim($data[$i][2]); $ville=trim($data[$i][3]);
	$tel=trim($data[$i][4]); $mail=trim($data[$i][5]);
}

$idClasse=$_GET["saisie_classe"];
$ordre=ordre_matiere_visubull($_GET["saisie_classe"]);

$noteMoyEleG=0; $coefEleG=0;
$eleveT=recupEleve($_GET["saisie_classe"]);
$moyenClasseGen=""; $nbeleve=0;
$noteMoyEleG1=0; $coefEleG1=0; $nbmatiere=0;
$moyenClasseGen=calculMoyenClasse($idClasse,$eleveT,$dateDebut,$dateFin,$ordre);
if ($moyenClasseGen < 0) { $moyenClasseGen="&nbsp;"; }

$noteMoyEleG=0; $coefEleG=0;
$afficheMoyen="oui";

for($j=0;$j<countTriade($eleveT);$j++) {
	$lv1Eleve=$eleveT[$j][2]; $lv2Eleve=$eleveT[$j][3]; $idEleve=$eleveT[$j][4];
	if ($idEleve != $ideleverecup) { continue; }

	$coeffaffTotal=0;
	for($i=0;$i<countTriade($ordre);$i++) {
		$matiere=chercheMatiereNom($ordre[$i][0]);
		$nomprof=recherche_personne($ordre[$i][1]);
		$verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);
		$idMatiere=$ordre[$i][0];
		$idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
		$profAff=recherche_personne($ordre[$i][1]);

		if ($verifGroupe) { continue; }

		$idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2]);
		$coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$i][2]);
		$tri=$_GET["saisie_trimestre"];

		if (PRODUCTID == "b3a295e8e8551f5cb0ebaf4814ca3ec0") { $coeffaff=1; }
		$idprof=profAff($idMatiere,$idClasse,$ordre[$i][2]);

		$epreuve="";
		if ($_GET["type_bulletin"] == "ismapp") {
			$listeExamen=array("CC","DST","Dad","Soutenance","Rapport","Fiche de lecture","Exposé","Partiel","Lecture","Examen écrit","Recopiage vocabulaire","Mémoire Ip","Evaluation Tutorat");
			$epreuve=""; $moyenneTT=""; $coef="";
			foreach($listeExamen as $key=>$value) {
				if ($idgroupe == "0") {
					$noteaff=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,$value);
				}else{
					$noteaff=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,$value);
				}
				if (trim($noteaff) != "") {
					if ($value=="CC")                     { $valcoef="1"; }
					if ($value=="DST")                    { $valcoef="2"; }
					if ($value=="Partiel")                { $valcoef="3"; }
					if ($value=="Soutenance")             { $valcoef="3"; }
					if ($value=="Rapport")                { $valcoef="3"; }
					if ($value=="Fiche de lecture")       { $valcoef="2"; }
					if ($value=="Exposé")                 { $valcoef="1"; }
					if ($value=="Dad")                    { $valcoef="1"; }
					if ($value=="Lecture")                { $valcoef="3"; }
					if ($value=="Examen écrit")           { $valcoef="2"; }
					if ($value=="Recopiage vocabulaire")  { $valcoef="1"; }
					if ($value=="Mémoire Ip")             { $valcoef="2"; }
					if ($value=="Evaluation Tutorat")     { $valcoef="2"; }
					$epreuve.="$value:$noteaff ($valcoef) ";
					$moyenneTT+=$noteaff*$valcoef;
					$coef+=$valcoef;
				}
			}
			$noteaff=$moyenneTT/$coef;
			if ($noteaff != "") {
				$noteaff1=number_format($noteaff,2,',','');
				$cumulPoint+=$noteaff*$coeffaff;
				$nbcoeftotal+=$coeffaff;
			}else{
				$noteaff1="";
			}
			$couleur="black";
			if ($noteaff1 < 10)  { $couleur="red"; }
			if ($noteaff1 >= 15) { $couleur="green"; }
		}else{
			if ($idgroupe == "0") {
				$noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof);
				$notetype=recherchetypenote($ordre[$i][0],$dateDebut,$dateFin,$idClasse);
			}else{
				$noteaff=moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
				$notetype=recherchetypenotegroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe);
			}
			$noteaff1=$noteaff;
			if ($notetype=="en") {
				$afficheMoyen="non";
				if ($noteaff1 != "") {
					$noteaff1=number_format($noteaff1,0,'','');
					$noteaff1=$noteaff1."% - ".recherche_note_en($noteaff);
				}else{
					$noteaff1="---";
				}
				$couleur="black";
			}else{
				$couleur="black";
				if ($noteaff1 < 10)  { $couleur="red"; }
				if ($noteaff1 >= 15) { $couleur="green"; }
			}
		}

		$commentaireeleve="&nbsp;&nbsp;&nbsp;";
		$commentaireeleve=cherche_com_eleve($idEleve,$idMatiere,$idClasse,$tri,$idprof,$idgroupe)."$epreuve";
		$commentaireeleve=preg_replace("/'/","\\'",$commentaireeleve);
		$commentaireeleve=preg_replace('/"/',"&rdquo;",$commentaireeleve);
		$commentaireeleve=preg_replace('/\r\n/',"<br />",$commentaireeleve);
		$commentaireeleve=preg_replace('/\n/',"<br />",$commentaireeleve);
		$commentaireeleve=stripslashes($commentaireeleve);

		// Badge class
		if (trim($noteaff1) == "" || $noteaff1 == "---") {
			$noteaff1 = "---";
			$badgeClass = "bull-note-na";
			$couleur = "black";
		} elseif ($couleur == "red") {
			$badgeClass = "bull-note-low";
		} elseif ($couleur == "green") {
			$badgeClass = "bull-note-high";
		} else {
			$badgeClass = "bull-note-ok";
		}
		// Classe moyenne matière
		$notetype="";
		if (($idgroupe == "0") || ($idgroupe == "")) {
			$moyeMatGen=moyeMatGen($ordre[$i][0],$dateDebut,$dateFin,$idClasse,$idprof);
			$notetype=recherchetypenote($idMatiere,$dateDebut,$dateFin,$idClasse);
		}else{
			$moyeMatGen=moyeMatGenGroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof);
			$notetype=recherchetypenotegroupe($idMatiere,$dateDebut,$dateFin,$idgroupe);
		}
		$moyeMatGenaff=$moyeMatGen;
		if (trim($notetype) == "en") { $moyeMatGenaff=number_format($moyeMatGen,0,'','')."% - ".recherche_note_en($moyeMatGen); }
?>
    <tr class="cc-tr-data">
      <td class="bull-col-mat" style="padding:6px 10px">
        <span class="bull-mat"><?php print strtoupper(trunchaine($matiere,28)) ?> <span class="bull-coef">(<?php print $coeffaff ?>)</span></span>
        <?php if ($accesrestreint == "non"): ?>
        <div class="bull-prof"><i class="bi bi-person-fill" style="font-size:9px"></i> <?php print trunchaine(trim($profAff),40) ?></div>
        <?php endif; ?>
      </td>
      <td class="bull-col-note" style="padding:6px 4px;text-align:center">
        <span class="bull-note <?php print $badgeClass ?>"><?php print $noteaff1 ?></span>
      </td>
      <td class="bull-col-moy" style="padding:6px 4px;text-align:center">
        <span class="bull-moy-val"><?php print $moyeMatGenaff ?>&nbsp;</span>
      </td>
      <?php if ($accesrestreint == "non"): ?>
      <td class="bull-col-com" style="padding:6px 10px">
        <?php if ($_SESSION["membre"] == "menuadmin" || $_SESSION["membre"] == "menuscolaire"): ?>
        <div id="com<?php print $i ?>">
          <a href="#" class="bull-com-link" onclick="modifCom('com<?php print $i ?>','<?php print $idMatiere ?>','<?php print $idEleve ?>','<?php print $idClasse ?>','<?php print $tri ?>','<?php print $idprof ?>','<?php print $idgroupe ?>');return false;"><?php print stripslashes($commentaireeleve) ?></a>
        </div>
        <?php else: ?>
        <span class="bull-com"><?php print stripslashes($commentaireeleve) ?></span>
        <?php endif; ?>
      </td>
      <?php endif; ?>
    </tr>
<?php
		// Cumul moyenne générale élève
		if ((trim($noteaff) != "") && ($noteaff >= 0)) {
			$noteMoyEleG += $coeffaff * $noteaff;
			$coefEleG += $coeffaff;
		}
	} // fin for matières

	// ── Vie scolaire ──
	if ((MODNAMUR0 == "oui") && ($_SESSION["validenoteviescolaire"] == "oui")) {
		$recupInfo=recupCaractVieScolaire($_GET["saisie_classe"]);
		$persVieScolaire=$recupInfo[0][4];
		$coefBull=$recupInfo[0][1]; $coefProf=$recupInfo[0][2]; $coefVieScol=$recupInfo[0][3];
		$noteaff=calculNoteVieScolaire($idEleve,$coefProf,$coefVieScol,$tri);
		$moyeMatGen1=moyeMatGenVieScolaire($tri,$idClasse);
		$noteaff1=$noteaff;
		if ($notetype=="en") {
			$afficheMoyen="non";
			if ($noteaff1 != "") {
				$noteaff1=number_format($noteaff1,0,'','');
				$noteaff1=$noteaff1."% - ".recherche_note_en($noteaff);
			}else{
				$noteaff1="---";
			}
			$couleur="black";
		}else{
			$couleur="black";
			if ($noteaff1 < 10)  { $couleur="red"; }
			if ($noteaff1 >= 15) { $couleur="green"; }
		}
		if (trim($noteaff1)==""||$noteaff1=="---") { $badgeVie="bull-note-na"; $noteaff1="---"; }
		elseif ($couleur=="red")   { $badgeVie="bull-note-low"; }
		elseif ($couleur=="green") { $badgeVie="bull-note-high"; }
		else                       { $badgeVie="bull-note-ok"; }
		$com=cherche_com_scolaire_eleve_cpe($idEleve,"-10",$idClasse,$tri,"");
?>
    <tr class="bull-vie-row">
      <td style="padding:6px 10px">
        <span class="bull-mat"><?php print strtoupper(trunchaine("Vie Scolaire",28)) ?> <span class="bull-coef">(<?php print $coefBull ?>)</span></span>
        <?php if ($accesrestreint == "non"): ?>
        <div class="bull-prof"><i class="bi bi-person-fill" style="font-size:9px"></i> <?php print trunchaine(trim($persVieScolaire),20) ?></div>
        <?php endif; ?>
      </td>
      <td style="padding:6px 4px;text-align:center">
        <a href="#" onMouseOver="AffBulle('<?php print $com ?>');" onMouseOut="HideBulle();"
           onclick="open('./commentaire.php?idm=<?php print $idMatiere ?>&ide=<?php print $idEleve ?>&idc=<?php print $idClasse ?>&tri=<?php print $tri ?>&idprof=<?php print $idprof ?>&idgroupe=<?php print $idgroupe ?>','','width=400,height=200');return false;"
           style="text-decoration:none">
          <span class="bull-note <?php print $badgeVie ?>"><?php print $noteaff1 ?></span>
        </a>
      </td>
      <td style="padding:6px 4px;text-align:center"><span class="bull-moy-val"><?php print $moyeMatGen1 ?></span></td>
      <?php if ($accesrestreint == "non"): ?><td></td><?php endif; ?>
    </tr>
<?php
		if ((trim($noteaff) != "") && ($noteaff >= 0)) {
			$noteMoyEleG += $coefBull * $noteaff;
			$coefEleG += $coefBull;
		}
	}

	// ── Moyenne générale ──
	if ($afficheMoyen == "oui") {
		if ($_GET["type_bulletin"] == "ismapp") {
			$moyenEleve=$cumulPoint/$nbcoeftotal;
			$moyenEleve=number_format($moyenEleve,2,',','');
		}else{
			$moyenEleve=moyGenEleve($noteMoyEleG,$coefEleG);
		}
		if (($moyenEleve == " ") || ($moyenEleve < 0)) { $moyenEleve="&nbsp;"; }
		$colorGen="black";
		if ($moyenEleve < 10)  { $colorGen="red"; }
		if ($moyenEleve >= 15) { $colorGen="green"; }
		$moyenEleve=preg_replace('/,/','.',$moyenEleve);

		if (trim($moyenEleve)==""||$moyenEleve<0)   { $badgeGen="bull-note-na"; }
		elseif ($colorGen=="red")                    { $badgeGen="bull-note-low"; }
		elseif ($colorGen=="green")                  { $badgeGen="bull-note-high"; }
		else                                         { $badgeGen="bull-note-ok"; }
?>
    <tr class="bull-gen-row">
      <td class="bull-gen-label" style="padding:8px 12px"><?php print LANGPROJ16 ?></td>
      <td style="text-align:center;padding:8px 4px">
        <span id="moyenGG" class="bull-gen-note"><?php print $moyenEleve ?></span>
        <input type="text" id="moyenG" value="<?php print $moyenEleve ?>" style="display:none">
      </td>
      <td class="bull-gen-moy" style="text-align:center;padding:8px 4px"><?php print $moyenClasseGen ?></td>
      <?php if ($accesrestreint == "non"): ?>
      <td style="padding:8px 10px">
        <?php if ($_GET["type_bulletin"] == "ismapp"): ?>
        <span style="font-size:11px;color:#CACCEF">Cumul : <span id="cumul"><?php print $cumulPoint ?></span> / Coef : <span id="cumulcoef"><?php print $nbcoeftotal ?></span></span>
        <?php endif; ?>
      </td>
      <?php endif; ?>
    </tr>
<?php
	}
} // fin for élèves
?>
  </tbody>
</table>
</div>

<?php Pgclose(); ?>
<SCRIPT type="text/javascript">InitBulle("#000000","#CCCCCC","#000000",1);</SCRIPT>
<?php if ($accesrestreint == "non"): ?>
<script>
function modifCom(retourAffiche,idMatiere,idEleve,idClasse,tri,idprof,idgroupe) {
	var divid=retourAffiche;
	var myAjax = new Ajax.Request("ajaxCommentaireVideo.php",{
		method:"post", asynchronous:true,
		parameters:"idm="+idMatiere+"&ide="+idEleve+"&idc="+idClasse+"&tri="+tri+"&idprof="+idprof+"&idgroupe="+idgroupe+"&retourAffiche="+retourAffiche,
		timeout:5000,
		onComplete:function(request){ $(divid).innerHTML=request.responseText; }
	});
}
function saveCommentaire(commentaire,idEleve,idMatiere,idClasse,tri,idprof,idgroupe,retourAffiche) {
	var divid=retourAffiche;
	var myAjax = new Ajax.Request("ajaxCommentaireVideoEnr.php",{
		method:"post", asynchronous:true,
		parameters:"idm="+idMatiere+"&ide="+idEleve+"&idc="+idClasse+"&tri="+tri+"&idprof="+idprof+"&idgroupe="+idgroupe+"&com="+commentaire+"&typecom=0&retourAffiche="+encodeURIComponent(retourAffiche),
		timeout:5000,
		onComplete:function(request){
			$(divid).innerHTML=request.responseText;
			var fl=document.createElement('span');
			fl.style.cssText='color:#2e7d32;font-size:10px;font-style:italic;margin-left:6px';
			fl.innerHTML='&#10003; Enregistré';
			$(divid).appendChild(fl);
			setTimeout(function(){if(fl.parentNode)fl.parentNode.removeChild(fl);},2000);
		}
	});
}
function adpaterMoyenne(valeur,coef) {
	if (valeur == "") valeur='0';
	var valeurnew=<?php print $cumulPoint ?>+(eval(valeur)*coef);
	document.getElementById('cumul').innerHTML=valeurnew;
	var cumulcoef=<?php print $nbcoeftotal ?>+coef;
	document.getElementById('cumulcoef').innerHTML=cumulcoef;
	valeurnew=(valeurnew/cumulcoef).toFixed(2);
	document.getElementById('moyenGG').innerHTML=valeurnew;
	document.getElementById('moyenG').value=valeurnew;
}
</script>
<?php endif; ?>
</body>
</html>
