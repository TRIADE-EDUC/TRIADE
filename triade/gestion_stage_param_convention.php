<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
 *   Site                 : http://www.triade-educ.org
 *
 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./ckeditor/ckeditor2.js"></script>
<script>var panelWidth = 350;</script>
<script language="JavaScript" src="./librairie_js/lib_aide.js"></script>
<script>function _(el) { return document.getElementById(el); }</script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<style>
#dhtmlgoodies_leftPanel {
    background-color:#CCCCCC; color:#000;
    height:100%; left:0; z-index:10;
    position:absolute; display:none; padding:0;
}
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="initDocument()">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("3");
$cnx=cnx();
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Convention de stage</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<!-- Panneau aide -->
<div id="dhtmlgoodies_leftPanel">
  <img src="image/commun/info2.gif" align="left">
  <font class="T1"><b><?php print LANGAIDE2 ?></b><br><br>
  <table border="1" bgcolor="#FFFFFF" width="100%" style="border-collapse:collapse;">
  <tr>
  <td valign="top" style="padding:6px;font-size:11px;">
    Nom de l'élève=> NomEleve<br>Prénom de l'élève=> PrenomEleve<br>Classe de l'élève=> classe_eleve<br>
    Date naissance=> naissance_eleve<br>Lieu naissance=> lieu_naissance<br>Adresse élève=> adresse_eleve<br>
    Code postal=> ccp_eleve<br>Ville=> ville_eleve<br>tél élève=> tel_eleve<br>tél parent=> tel_parent<br>
    année scolaire=> anneescolaire
  </td>
  <td valign="top" style="padding:6px;font-size:11px;">
    Nom Entreprise=> ent_nom<br>Adresse entreprise=> ent_adr<br>Localité entreprise=> ent_localite<br>
    CP entreprise=> ent_cp<br>Pays entreprise=> ent_pays<br>Tel entreprise=> ent_tel<br>Fax entreprise=> ent_fax<br>
    Directeur entreprise=> ent_directeur<br>Fonction du responsable=> ent_dir_fonction<br>
    Tuteur de stage=> ent_tuteur<br>Mail entreprise=> ent_mail<br>Web entreprise=> ent_web<br>
    Logement=> ent_logement<br>Nbr d'étoile=> ent_etoile<br>Nbr chambre=> ent_chambre<br>
    Groupe hôtelier=> ent_grp_hotelier
  </td>
  <td valign="top" style="padding:6px;font-size:11px;">
    Nom du stage=> nom_stage<br>Nom du directeur=> directeur<br>Enseignant suivi 1=> enseignant_suivi_1<br>
    date suivi 1=> date_suivi_1<br>Enseignant suivi 2=> enseignant_suivi_2<br>date suivi 2=> date_suivi_2<br>
    Date du jour=> date_du_jour<br>Nombre de jour=> nb_jour<br>Du xx/xx/xx au xx/xx/xx=> periode_n<br>
    Date début période=> debperio_n<br>Date fin période=> finperio_n<br>
    &nbsp;&nbsp;=>(n valeur 1 à 9)<br>Affiche la seule période=> periode_x<br>
    Date début période=> debperio_x<br>Date fin période=> finperio_x<br>
    Intitulé du service=> ent_service<br>Indemnité stage=> ent_indemnitestage
  </td>
  </tr>
  </table>
  </font>
</div>

<?php
if (isset($_POST["saisie_classe"])) {
	$idclasse=$_POST["saisie_classe"];
} else {
	$idclasse="0";
}
?>

<br>
<script language=JavaScript>buttonMagic3("<?php print LANGAIDE ?>","initSlideLeftPanel();return false");</script>

<!-- ── Filtre classe / convention ── -->
<form method="post" action="gestion_stage_param_convention.php">
<div class="na-card">
  <div class="na-row">
    <span class="na-lbl">Classe :</span>
    <select name="saisie_classe" class="cc-select" onChange="this.form.submit()">
      <?php
      if ($idclasse == 0) {
          print "<option value='0' id='select0'>Toutes les classes</option>";
      } else {
          print "<option value='$idclasse' id='select0'>".chercheClasse_nom($idclasse)."</option>";
          print "<option value='0' id='select1'>Toutes les classes</option>";
      }
      select_classe();
      ?>
    </select>
  </div>
  <div class="na-row">
    <span class="na-lbl">Convention N° :</span>
    <select name="nbconv" class="cc-select" onChange="this.form.submit()">
      <?php
      if (trim($_POST["nbconv"]) != "") {
          $nbconv=$_POST["nbconv"];
          print "<option value='$nbconv' id='select0'>".preg_replace('/_conv_/','',$nbconv)."</option>";
      }
      print "<option></option><option value='_conv_A' id='select1'>A</option><option value='_conv_B' id='select1'>B</option><option value='_conv_C' id='select1'>C</option>";
      ?>
    </select>
    <?php
    $fichier1="./data/parametrage/courrier_stageconvention_$idclasse.rtf";
    if (file_exists($fichier1)) {
        print " <a href='#' onclick=\"open('telecharger.php?fichiername=convention_stage.rtf&fichier=$fichier1','_blank','')\" style='font-size:11px;color:#080A66;'>[Fichier en cours RTF]</a>";
    }
    ?>
  </div>
</div>
</form>

<?php
if (isset($_POST["create"])) {
	if ($_FILES['rtf']['name'] != "") {
		$fichier=$_FILES['rtf']['name'];
		$type=$_FILES['rtf']['type'];
		$tmp_name=$_FILES['rtf']['tmp_name'];
		$size=$_FILES['rtf']['size'];
		$nbconv=$_POST["nbconv"];
		if ((!empty($fichier)) && ($size <= 8000000)) {
			if (($type == "application/octet-stream") || ($type == "application/msword") || ($type == "application/rtf") || (preg_match('/.rtf$/i',$fichier))) {
				@unlink("./data/parametrage/courrier_stageconvention_$idclasse$nbconv.rtf");
				move_uploaded_file($tmp_name,"./data/parametrage/courrier_stageconvention_$idclasse$nbconv.rtf");
				config_param_ajout_classe("{FICHIERRTFSTAGE}","param_conv_stag#$idclasse$nbconv","$idclasse");
				$texte="{FICHIERRTFSTAGE}";
				if ($idclasse == 0) {
					$dataclasse=affClasse();
					for ($i=0; $i<countTriade($dataclasse); $i++) {
						$idclasse=$dataclasse[$i][0];
						config_param_ajout_classe($texte,"param_conv_stag#$idclasse$nbconv","$idclasse");
						copy("./data/parametrage/courrier_stageconvention_0.rtf","./data/parametrage/courrier_stageconvention_$idclasse$nbconv.rtf");
					}
				}
			}
		}
	} else {
		$nbconv=$_POST["nbconv"];
		$texte=$_POST["suite"];
		$texte=preg_replace('#(\\\\r|\\\\r\\\\n|\\\\n)#',' ',$texte);
		$texte=preg_replace('/\\\"/','"',$texte);
		$texte=preg_replace("/\\\'/","'",$texte);
		$texte=stripslashes($texte);
		$texte=stripslashes($texte);
		config_param_ajout_classe($_POST["suite"],"param_conv_stag#$idclasse$nbconv","$idclasse");
		if ($idclasse == 0) {
			$dataclasse=affClasse();
			for ($i=0; $i<countTriade($dataclasse); $i++) {
				$idclasse=$dataclasse[$i][0];
				config_param_ajout_classe($_POST["suite"],"param_conv_stag#$idclasse$nbconv","$idclasse");
			}
		}
	}
?>
<div style="margin:8px 0;padding:10px 14px;background:#e8f5e9;border:1px solid #a5d6a7;border-radius:8px;font-size:13px;color:#2e7d32;font-weight:600;">La convention est enregistrée.</div>
<?php if ($texte != "{FICHIERRTFSTAGE}") { ?>
<div class="na-card" style="margin-top:8px;">
  <div style="font-size:11px;color:#555;margin-bottom:6px;"><?php print LANGCONFIG2 ?> :</div>
  <div style="background:#fff;border:1px solid #dde0f0;border-radius:6px;padding:10px;font-size:12px;min-height:80px;"><?php print $texte ?></div>
</div>
<?php } else { ?>
<div style="padding:8px;font-size:12px;color:#555;font-style:italic;">Fichier 'rtf' comme matrice de donnée</div>
<?php } ?>
<div class="na-foot">
  <script language=JavaScript>buttonMagicFermeture();</script>
</div>
<?php
} else {
	$nbconv=$_POST["nbconv"];
	$data=config_param_visu("param_conv_stag#$idclasse$nbconv");
	if ($texte != "{FICHIERRTFSTAGE}") {
		$texte=$data[0][0];
	} else {
		$texte="<i>Fichier 'rtf' comme matrice de donnée</i>";
	}
	$texte=preg_replace('#(\\\\r|\\\\r\\\\n|\\\\n)#',' ',$texte);
?>
<form method="post" ENCTYPE="multipart/form-data">
<div class="na-card">
  <textarea id="editor2" style="height:48em;width:100%;" name="suite"><?php print $texte ?></textarea>
  <script type="text/javascript">
  var colorGRAPH='<?php print $GRAPH ?>';
  CKEDITOR.replace('editor2',{height:'399px',language:'<?php print ($_SESSION["langue"]=="fr")?"fr":"en";?>',scayt_autoStartup:true,grayt_autoStartup:true,scayt_maxSuggestions:3,scayt_sLang:'en_FR',removeButtons:'PasteFromWord'});
  </script>
  <div class="na-row" style="margin-top:12px;">
    <span class="na-lbl">Classe :</span>
    <select name="saisie_classe" class="cc-select">
      <?php
      if ($idclasse == 0) {
          print "<option value='0' id='select0'>Toutes les classes</option>";
      } else {
          print "<option value='$idclasse' id='select0'>".chercheClasse_nom($idclasse)."</option>";
          print "<option value='0' id='select1'>Toutes les classes</option>";
      }
      select_classe();
      ?>
    </select>
    <a href='./librairie_php/courrier_stageconvention.rtf' target='_blank' style="font-size:11px;color:#080A66;">[Fichier exemple RTF]</a>
  </div>
  <div class="na-row">
    <span class="na-lbl">Convention N° :</span>
    <select name="nbconv" class="cc-select">
      <option></option>
      <option value="_conv_A">A</option>
      <option value="_conv_B">B</option>
      <option value="_conv_C">C</option>
    </select>
  </div>
  <div class="na-row">
    <span class="na-lbl">Fichier RTF :</span>
    <input type="file" name="rtf">
    <span style="font-size:11px;color:#888;">(format RTF, max 8 Mo)</span>
  </div>
</div>
<div class="na-foot">
  <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT19 ?>","create");</script>
  <script language=JavaScript>buttonMagicFermeture();</script>
</div>
</form>
<?php } ?>

</td></tr></table>
<?php Pgclose(); ?>
</BODY></HTML>
