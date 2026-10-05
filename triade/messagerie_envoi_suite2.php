<?php
session_start();
$idpiecejointe = md5($_SESSION["membre"].$_SESSION["id_pers"].date("YMDHms").rand(0,9999));
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
 *   Site                 : http://www.triade-educ.com
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
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
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
<script>window.alert = function(msg) { if (msg) alertify.error(msg); };</script>
<script type="text/javascript">
function _(el) { return document.getElementById(el); }

function uploadFile(idpiecejointe) {
    var file = _("Filedata").files[0];
    var formdata = new FormData();
    formdata.append("Filedata", file);
    var ajax = new XMLHttpRequest();
    ajax.upload.addEventListener("progress", progressHandler, false);
    ajax.addEventListener("load", completeHandler, false);
    ajax.addEventListener("error", errorHandler, false);
    ajax.addEventListener("abort", abortHandler, false);
    ajax.open("POST", "uploadmessagerie.php?idpiecejointe="+idpiecejointe);
    ajax.send(formdata);
}
function progressHandler(event) {
    _("loaded_n_total").innerHTML = "Envoi " + event.loaded + " / " + event.total + " octets";
    var percent = (event.loaded / event.total) * 100;
    _("progressBar").value = Math.round(percent);
    _("status").innerHTML = Math.round(percent) + "% — veuillez patienter…";
}
function completeHandler(event) {
    _("status").innerHTML = event.target.responseText;
    _("progressBar").value = 0;
    _("loaded_n_total").innerHTML = "";
    updatefichier("ok");
}
function errorHandler(event)  { _("status").innerHTML = "Erreur lors du téléchargement"; }
function abortHandler(event)  { _("status").innerHTML = "Téléchargement abandonné"; }

function ajoutDestinataire() {
    var sel = document.getElementById('saisie_destinataire');
    var val = sel.options[sel.selectedIndex].value;
    var txt = sel.options[sel.selectedIndex].text;
    if (val != '0') {
        document.getElementById('liste_destinataire').innerHTML += '<span class="dest-tag">' + txt + '</span> ';
        document.getElementById('liste_destinataireValue').value += val + ",";
    }
}
function annulDestinataire() {
    document.getElementById('liste_destinataire').innerHTML = '';
    document.getElementById('liste_destinataireValue').value = "";
    document.getElementById("saisie_destinataire").selectedIndex = 0;
}
function updatefichier(item) {
    if (item == "ok") {
        ajaxActualisePieceJointe('<?php print $idpiecejointe ?>', 'listingpiecejointe');
    }
}
</script>
<style>
/* ── Rédaction de message – Design 2026 ── */
* { box-sizing: border-box; }
body {
    background: #f0f2fa; margin: 0; padding: 0;
    font-family: Electrolize, Trebuchet MS, Arial, sans-serif; font-size: 13px;
}
.compose-wrap { padding: 14px 14px 30px; }

/* Carte principale */
.compose-card {
    background: #fff; border-radius: 10px;
    box-shadow: 0 2px 10px rgba(8,10,102,.09);
    border-top: 4px solid #080A66;
    padding: 20px 22px; margin-bottom: 14px;
}

/* Lignes de formulaire */
.form-row {
    display: flex; align-items: center; gap: 10px;
    border-bottom: 1px solid #eef0f8; padding: 10px 0;
}
.form-row:last-child { border-bottom: none; }
.form-label {
    width: 90px; flex-shrink: 0;
    font-size: 11px; font-weight: 700; color: #888;
    text-transform: uppercase; letter-spacing: .4px;
}
.form-field { flex: 1; }
.form-field input[type=text] {
    width: 100%; border: 1px solid #d0d4ee; border-radius: 5px;
    padding: 6px 10px; font-size: 13px; font-family: Electrolize, Arial, sans-serif;
    background: #fafbff; color: #333;
    transition: border-color .15s;
}
.form-field input[type=text]:focus { outline: none; border-color: #080A66; background: #fff; }
.form-field input[readonly] { background: #f5f5f5; color: #888; cursor: default; }
.form-field select {
    border: 1px solid #d0d4ee; border-radius: 5px;
    padding: 6px 10px; font-size: 13px; font-family: Electrolize, Arial, sans-serif;
    background: #fafbff; color: #333; max-width: 100%;
}

/* Zone destinataires sélectionnés */
.dest-list {
    min-height: 32px; background: #f5f7ff;
    border: 1px dashed #c0c4e0; border-radius: 5px;
    padding: 6px 10px; margin-top: 6px; font-size: 12px; color: #555;
    display: flex; flex-wrap: wrap; gap: 4px; align-items: center;
}
.dest-tag {
    background: #CACCEF; color: #080A66; border-radius: 12px;
    padding: 2px 10px; font-size: 11px; font-weight: 600;
}
.btn-annul {
    font-size: 11px; color: #c62828; cursor: pointer;
    text-decoration: none; margin-left: auto; white-space: nowrap;
}
.btn-annul:hover { text-decoration: underline; }

/* Éditeur */
.editor-wrap { margin-top: 14px; }

/* Options */
.options-row {
    display: flex; align-items: center; flex-wrap: wrap; gap: 14px;
    margin: 14px 0; padding: 10px 14px;
    background: #f8f9fe; border: 1px solid #e0e4f0; border-radius: 6px;
    font-size: 12px; color: #444;
}
.options-row label { display: flex; align-items: center; gap: 5px; cursor: pointer; }

/* IA Copilot */
.ia-row {
    display: flex; align-items: center; gap: 8px;
    margin: 10px 0; flex-wrap: wrap;
}
.ia-row input[type=text] {
    flex: 1; min-width: 200px;
    border: 1px solid #d0d4ee; border-radius: 5px;
    padding: 6px 10px; font-size: 12px; font-family: Electrolize, Arial, sans-serif;
}
.btn-copilot {
    background: #080A66; color: #fff; border: none; border-radius: 5px;
    padding: 7px 14px; font-size: 12px; cursor: pointer;
    font-family: Electrolize, Arial, sans-serif; font-weight: 600;
}
.btn-copilot:hover { background: #0d12a0; }

/* Copilot split-button */
.copilot-split { position: relative; display: inline-block; }
.copilot-menu {
    display: none; position: absolute; top: 100%; left: 0; margin-top: 2px;
    background: #fff; border: 1px solid #c5cae9;
    border-radius: 6px; box-shadow: 0 4px 12px rgba(8,10,102,.15);
    min-width: 160px; z-index: 200;
}
.copilot-menu-item {
    padding: 9px 14px; cursor: pointer; font-size: 12px; color: #333;
    display: flex; align-items: center; gap: 7px; white-space: nowrap;
}
.copilot-menu-item:first-child { border-radius: 6px 6px 0 0; }
.copilot-menu-item:last-child  { border-radius: 0 0 6px 6px; border-top: 1px solid #eef0f8; }
.copilot-menu-item:hover { background: #f0f2fa; color: #080A66; }

/* Boutons d'action */
.action-bar {
    display: flex; align-items: center; gap: 10px;
    margin-top: 16px; padding-top: 14px;
    border-top: 1px solid #eef0f8; flex-wrap: wrap;
}

/* Pièces jointes */
.pj-card {
    background: #fff; border-radius: 10px;
    box-shadow: 0 2px 10px rgba(8,10,102,.07);
    padding: 16px 20px; margin-top: 14px;
}
.pj-title {
    font-size: 12px; font-weight: 700; color: #080A66;
    text-transform: uppercase; letter-spacing: .4px;
    margin-bottom: 10px; display: flex; align-items: center; gap: 6px;
}
.pj-title img { vertical-align: middle; }
.upload-zone {
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    padding: 10px; background: #f5f7ff;
    border: 1px dashed #c0c4e0; border-radius: 6px;
}
progress#progressBar {
    width: 200px; height: 10px; border-radius: 5px; border: none;
    background: #e0e4f0;
}
#status { font-size: 11px; color: #080A66; }
#loaded_n_total { font-size: 10px; color: #999; }
</style>
</HEAD>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>

<?php
if (isset($_GET["erreur"])) {
    echo "<script>document.addEventListener('DOMContentLoaded',function(){ alertify.error(langfunc16); });</script>";
}
$cnx = cnx();
$brouillon = 0;
if (isset($_GET["brouillon"]))  $brouillon = $_GET["brouillon"];
if (isset($_POST["brouillon"])) $brouillon = $_POST["brouillon"];
if (isset($_GET["f"]))                $forwarding = $_GET["f"];
if (isset($_GET["saisie_id_message"])) $idMessage = $_GET["saisie_id_message"];

include_once('librairie_php/db_triade.php');

// Forwarding
$messageencours = "";
if ($forwarding == 1 && $idMessage > 0) {
    $dataMessage = affichage_messagerie_message($idMessage);
    for ($i = 0; $i < countTriade($dataMessage); $i++) {
        $number = $dataMessage[$i][10];
        if (in_array(trim($dataMessage[$i][7]), ['ADM','ENS','MVS','TUT'])) {
            $destinataire = recherche_personne($dataMessage[$i][1]);
        } else {
            $destinataire = recherche_eleve($dataMessage[$i][1]);
        }
        $messageencours  = "<br><br><hr>";
        $messageencours .= "> <i>".LANGMESS31.": $destinataire</i><br>";
        $messageencours .= "> <i>".LANGTE12." ".dateForm($dataMessage[$i][4])." ".LANGTE13." ".$dataMessage[$i][5]."</i>";
        $messageencours .= stripslashes(Decrypte($dataMessage[$i][3], $number));
        $_GET["saisie_objet"] = trunchaine("FWD: ".$dataMessage[$i][8], 50);
    }
}

// Source (expéditeur)
if ($_GET["saisie_envoi"] == "mailexterne") {
    $id_pers = isset($_SESSION["id_suppleant"]) ? $_SESSION["id_suppleant"] : $_SESSION["id_pers"];
    $source  = mess_mail_forward($_SESSION["nom"], $_SESSION["prenom"], $id_pers, $_SESSION["membre"]);
} else {
    $source = $_SESSION["nom"]." ".$_SESSION["prenom"];
}

// Label destinataire
$langt6 = LANGTE6;
if (!in_array($_GET["saisie_envoi"], ['administrateur','scolaire','enseignant','tuteur','personnel'])) $langt6 = LANGTE6bis;
if ($_GET["saisie_envoi"] == "grpmail")        $langt6 = LANGTE14;
if ($_GET["saisie_envoi"] == "grpmailelev")    $langt6 = LANGTE14;
if ($_GET["saisie_envoi"] == "eleve")          $langt6 = "À l'élève";
if ($_GET["saisie_envoi"] == "mailexterne")    $langt6 = "À";
if ($_GET["saisie_envoi"] == "delegue")        $langt6 = "À";
if ($_GET["saisie_envoi"] == "tuteurdestage")  $langt6 = "Au tuteur de stage";

// Type qui
if (trim($_GET["saisie_envoi"]) == "administrateur") $qui_envoi = "ADM";
if (trim($_GET["saisie_envoi"]) == "scolaire")       $qui_envoi = "MVS";
if (trim($_GET["saisie_envoi"]) == "enseignant")     $qui_envoi = "ENS";
if (trim($_GET["saisie_envoi"]) == "grpmail")        $qui_envoi = "GRPMAIL";
if (trim($_GET["saisie_envoi"]) == "grpmailelev")    $qui_envoi = "GRPMAILELEV";
if (trim($_GET["saisie_envoi"]) == "parent")         $qui_envoi = "PAR";
if (trim($_GET["saisie_envoi"]) == "eleve")          $qui_envoi = "ELE";
if (trim($_GET["saisie_envoi"]) == "tuteur")         $qui_envoi = "TUT";
if (trim($_GET["saisie_envoi"]) == "personnel")      $qui_envoi = "PER";
if (trim($_GET["saisie_envoi"]) == "delegue")        $qui_envoi = "DELEGUE";
if (trim($_GET["saisie_envoi"]) == "tuteurdestage")  $qui_envoi = "TUTEURSTAGE";

// IA config
if (file_exists("./common/config-ia.php")) {
    include_once("common/productId.php");
    include_once("common/config-ia.php");
    $productID = PRODUCTID;
    $iakey     = IAKEY;
    $lienIA      = "ajaxIAOrtho(document.getElementById('commentaire').value,'$productID','$iakey','editor',CKEDITOR)";
    $lienIAAgent = "ajaxIAOrthoAgent(document.getElementById('commentaire').value,'$productID','$iakey','editor',CKEDITOR)";
} else {
    $noIA = "alertify.warning('Votre Triade n\\'est pas configuré pour utiliser l\\'IA. Contacter votre administrateur Triade')";
    $lienIA      = $noIA;
    $lienIAAgent = $noIA;
}

// Upload config
$taille  = "2Mo";
$maxsize = "2000000";
if (UPLOADIMG == "oui") { $taille = "8Mo"; $maxsize = "8000000"; }
?>

<div class="compose-wrap">

  <form name="formulaire" method="post" action="./messagerie_enr.php" target="_parent" onsubmit="return verif_message_envoi()">

  <!-- ── Carte principale ── -->
  <div class="compose-card">

    <!-- De -->
    <div class="form-row">
      <div class="form-label"><?php print ucwords(LANGTE3) ?></div>
      <div class="form-field">
        <input type="text" name="saisie_emetteur"
          value="<?php print stripslashes($source) ?>"
          onfocus="this.blur()" readonly="readonly">
      </div>
    </div>

    <!-- Objet -->
    <div class="form-row">
      <div class="form-label"><?php print LANGTE5 ?></div>
      <div class="form-field">
        <input type="text" name="saisie_objet" autocomplete="off"
          maxlength="50"
          value="<?php print stripslashes(stripcslashes($_GET["saisie_objet"])) ?>"
          placeholder="Objet du message…">
      </div>
    </div>

    <!-- Destinataire -->
    <div class="form-row" style="align-items:flex-start">
      <div class="form-label" style="padding-top:7px"><?php print ucfirst($langt6) ?></div>
      <div class="form-field">
        <?php if ($_GET["saisie_envoi"] == "mailexterne") { ?>
          <input type="text" name="saisie_destinataire" id="toemail"
            placeholder="adresse@email.com"
            onblur="document.getElementById('liste_destinataireValue').value=this.value;
                    document.getElementById('liste_destinataire').innerHTML=this.value;">
        <?php } else { ?>
          <?php
          if (in_array(strtoupper(trim($_GET["saisie_envoi"])), ['GRPMAIL','GRPMAILELEV'])) {
              $foncAppel = (strtoupper(trim($_GET["saisie_envoi"])) == "GRPMAILELEV") ? "Eleve" : "";
              echo "<script>
              var etat2=0;
              function bul2() {
                  AffBulle3('Liste des personnes','./image/commun/info.jpg','');
                  listingGroupeMail$foncAppel(document.formulaire.saisie_destinataire.options[document.formulaire.saisie_destinataire.options.selectedIndex].value);
              }
              </script>";
              echo "<a href='#' onmouseover='bul2()' onmouseout='HideBulle();' style='font-size:11px;color:#080A66'>[ voir la liste ]</a>&nbsp;";
          }
          ?>
          <select name="saisie_destinataire" id="saisie_destinataire" onchange="ajoutDestinataire()" style="width:100%;max-width:400px">
            <option value="0" style="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
            <?php
            if (in_array($qui_envoi, ['ADM','MVS','ENS','TUT','PER'])) {
                select_personne_messagerie($qui_envoi);
            } elseif ($qui_envoi == "GRPMAIL") {
                select_grp_mail($_SESSION["id_pers"]);
            } elseif ($qui_envoi == "GRPMAILELEV") {
                select_grp_mailelev($_SESSION["id_pers"]);
            } elseif (in_array($qui_envoi, ['ELE','PAR'])) {
                $anneeScolaire = anneeScolaireViaIdClasse($_GET['saisie_classe']);
                $sql = "SELECT b.libelle,a.elev_id,a.nom,a.prenom FROM {$prefixe}eleves a,{$prefixe}classes b WHERE a.classe='$_GET[saisie_classe]' AND b.code_class='$_GET[saisie_classe]' AND a.annee_scolaire='$anneeScolaire' ORDER BY nom";
                $res = execSql($sql);
                $data_eleve = chargeMat($res);
                $choix2 = ""; $choix22 = "";
                if ($_GET["saisie_envoi"] == "eleve" && ACCESMESSELEVE == "oui") { $choix2 = "Tous les élèves"; $choix22 = "tousleseleves"; }
                if ($_GET["saisie_envoi"] == "parent" && ACCESMESSPARENT == "oui") { $choix2 = "Tous les parents"; $choix22 = "touslesparents"; }
                if (in_array($_GET["saisie_classe"], ['tousleselevesecole','touslesparentsecole'])) {
                    if ($_GET["saisie_classe"] == "tousleselevesecole")  print "<option style='color:#000066;background-color:#FCE4BA' value='tousleselevesecole'>$choix2</option>";
                    if ($_GET["saisie_classe"] == "touslesparentsecole") print "<option style='color:#000066;background-color:#FCE4BA' value='touslesparentsecole'>$choix2</option>";
                } else {
                    if ($choix2 != "") print "<option style='color:#000066;background-color:#FCE4BA' value='$choix22'>$choix2</option>";
                }
                for ($j = 0; $j < countTriade($data_eleve); $j++) {
                    $cra = "ok";
                    if ((ACCESMESSPARENT == "non") && (MESSDELEGUEPARENT == "oui") && ($_GET["saisie_envoi"] == "parent"))
                        $cra = delegue($data_eleve[$j][1], $_GET["saisie_classe"], $_GET["saisie_envoi"]);
                    if ((ACCESMESSELEVE == "non") && (MESSDELEGUEELEVE == "oui") && ($_GET["saisie_envoi"] == "eleve"))
                        $cra = delegue($data_eleve[$j][1], $_GET["saisie_classe"], $_GET["saisie_envoi"]);
                    if (trim($cra) != "") {
                        print "<option style='color:#000066;background-color:#CCCCFF' value='".$data_eleve[$j][1]."'>";
                        print ucwords(trim($data_eleve[$j][2]))." ".trunchaine(trim($data_eleve[$j][3]),15)." ".delegue($data_eleve[$j][1],$_GET["saisie_classe"],$_GET["saisie_envoi"]);
                        print "</option>";
                    }
                }
            } elseif ($qui_envoi == "DELEGUE") {
                print "<option style='color:#000066;background-color:#FCE4BA' value='tousleselevesdelegue'>Tous les élèves délégués</option>";
                print "<option style='color:#000066;background-color:#FCE4BA' value='touslesparentsdelegues'>Tous les parents délégués</option>";
                $listedelegue = aff_delegueTous();
                for ($j = 0; $j < countTriade($listedelegue); $j++) {
                    print "<option style='color:#000066;background-color:#CCCCFF' value='".$listedelegue[$j][1]."'>Parent : ".rechercheEleveNomPrenom($listedelegue[$j][1])." (".chercheClasse_nom($listedelegue[$j][0]).")</option>";
                    print "<option style='color:#000066;background-color:#CCCCFF' value='".$listedelegue[$j][2]."'>Parent : ".rechercheEleveNomPrenom($listedelegue[$j][2])." (".chercheClasse_nom($listedelegue[$j][0]).")</option>";
                    print "<option style='color:#000066;background-color:#CCCCFF' value='".$listedelegue[$j][3]."'>Élève : ".rechercheEleveNomPrenom($listedelegue[$j][3])." (".chercheClasse_nom($listedelegue[$j][0]).")</option>";
                    print "<option style='color:#000066;background-color:#CCCCFF' value='".$listedelegue[$j][4]."'>Élève : ".rechercheEleveNomPrenom($listedelegue[$j][4])." (".chercheClasse_nom($listedelegue[$j][0]).")</option>";
                }
            } elseif ($qui_envoi == "TUTEURSTAGE") {
                if ($_GET["saisie_classe"] == "touslestuteursdestage") {
                    print "<option style='color:#000066;background-color:#FCE4BA' value='touslestuteursdestage'>Tous les tuteurs de stage</option>";
                } else {
                    print "<option style='color:#000066;background-color:#FCE4BA' value='touslestuteursdestagedelaclasse'>Tous les tuteurs de stage de cette classe</option>";
                    $listetuteurStage = aff_TuteurStage($_GET["saisie_classe"]);
                    for ($j = 0; $j < countTriade($listetuteurStage); $j++) {
                        print "<option style='color:#000066;background-color:#CCCCFF' value='".$listetuteurStage[$j][0]."'>".strtoupper($listetuteurStage[$j][1])." ".ucfirst($listetuteurStage[$j][2])."</option>";
                    }
                }
            }
            ?>
          </select>
          <a href="#" onclick="annulDestinataire(); return false;" class="btn-annul">Effacer la liste</a>
        <?php } ?>

        <!-- Liste des destinataires sélectionnés -->
        <div class="dest-list" id="liste_destinataire"></div>
        <input type="hidden" name="saisie_destinataire_value" id="liste_destinataireValue">
      </div>
    </div>

  </div><!-- /.compose-card -->

  <!-- ── Éditeur ── -->
  <div class="editor-wrap">
    <textarea id="editor" name="resultat" cols="200"><?php print stripslashes($messageencours) ?></textarea>
    <script type="text/javascript">
    var colorGRAPH = '<?php print $GRAPH ?>';
    CKEDITOR.replace('editor', {
        height: '320px',
        language: '<?php print ($_SESSION["langue"] == "fr") ? "fr" : "en" ?>',
        scayt_autoStartup: true, grayt_autoStartup: true,
        scayt_maxSuggestions: 3, scayt_sLang: 'en_FR',
        removeButtons: 'PasteFromWord'
    });
    </script>
  </div>

  <!-- ── Options ── -->
  <div class="options-row">
    <?php if ((ACCESMESSELEVE == "oui") || (ACCESMESSPARENT == "oui")) { ?>
    <label>
      <input type="checkbox" name="envoimessagecompletparmail" value="1">
      Envoyer uniquement par e-mail
    </label>
    <?php } ?>
    <label>
      <input type="checkbox" onclick="ajoutSignature('<?php print $_SESSION['id_pers'] ?>','<?php print $_SESSION['membre'] ?>')" name="ajoutsignature" value="1">
      Ajouter ma signature
    </label>
    <a href="#" onclick="open('configSignature.php?GRAPH=<?php print $GRAPH ?>','config','width=500,height=500'); return false"
       style="font-size:11px;color:#080A66">[Configurer]</a>
  </div>

  <!-- ── IA Copilot ── -->
  <?php if (in_array($_SESSION['membre'], ['menuadmin','menuprof','menuscolaire'])) { ?>
  <div class="ia-row">
    <input type="text" id="commentaire" size="50" placeholder="Décrire le message à rédiger…">
    <div class="copilot-split">
      <button type="button" id="bt_copilot" class="btn-copilot" onclick="toggleCopilotMenu(event)">TRIADE-COPILOT &#9660;</button>
      <div class="copilot-menu" id="copilot-menu">
        <div class="copilot-menu-item" onclick="copilotSansAgent()"><i class="bi bi-chat-text"></i> Sans agent</div>
        <div class="copilot-menu-item" onclick="copilotAgentScolaire()"><i class="bi bi-mortarboard"></i> Agent Scolaire</div>
      </div>
    </div>
    <a href="#" onclick="document.getElementById('ia-help').style.display=(document.getElementById('ia-help').style.display=='none'?'block':'none'); return false;">
      <img src="./image/help.gif" border="0" align="center">
    </a>
  </div>
  <div id="ia-help" style="display:none; margin-top:6px; padding:10px 14px; background:#fffbe6; border:1px solid #f5c842; border-radius:6px; font-size:12px; color:#5a4000; max-width:600px; line-height:1.6;">
    <strong>TRIADE-COPILOT</strong><br>
    Indiquez dans le champ des mots clefs décrivant le message à rédiger, puis cliquez sur <strong>TRIADE-COPILOT</strong> et choisissez le mode IA.<br>
    <em>Agent Scolaire</em> utilise un agent IA spécialisé dans la communication scolaire.
  </div>
  <script>
  function toggleCopilotMenu(e) {
    e.stopPropagation();
    var m = document.getElementById('copilot-menu');
    m.style.display = (m.style.display === 'block') ? 'none' : 'block';
  }
  document.addEventListener('click', function() {
    var m = document.getElementById('copilot-menu');
    if (m) m.style.display = 'none';
  });
  function copilotSansAgent() {
    document.getElementById('copilot-menu').style.display = 'none';
    <?php print $lienIA ?>;
  }
  function copilotAgentScolaire() {
    document.getElementById('copilot-menu').style.display = 'none';
    <?php print $lienIAAgent ?>;
  }
  </script>
  <?php } ?>

  <!-- ── Boutons d'action ── -->
  <div class="action-bar">
    <button type="submit" name="rien"
      style="background:#080A66;color:#fff;border:none;border-radius:6px;padding:9px 24px;font-size:13px;cursor:pointer;font-family:Electrolize,'Trebuchet MS',Arial,sans-serif;display:inline-flex;align-items:center;gap:7px;font-weight:600">
      <i class="bi bi-send-fill"></i> <?php print LANGBT4 ?>
    </button>
    <button type="button" onclick="open('acces2.php','_parent','')"
      style="background:#fff;color:#080A66;border:1px solid #c5cae9;border-radius:6px;padding:9px 24px;font-size:13px;cursor:pointer;font-family:Electrolize,'Trebuchet MS',Arial,sans-serif;display:inline-flex;align-items:center;gap:7px">
      <i class="bi bi-x-circle"></i> <?php print LANGBT3 ?>
    </button>
  </div>

  <!-- Hidden inputs -->
  <input type="hidden" name="saisie_type_personne_dest" value="<?php print $qui_envoi ?>">
  <input type="hidden" name="saisie_envoi"              value="<?php print $_GET["saisie_envoi"] ?>">
  <input type="hidden" name="saisie_classe"             value="<?php print $_GET["saisie_classe"] ?>">
  <input type="hidden" name="idpiecejoint"              value="<?php print $idpiecejointe ?>">
  <input type="hidden" name="brouillon"                 value="<?php print $brouillon ?>">

  </form>

  <!-- ── Pièces jointes ── -->
  <?php if ($_GET["saisie_envoi"] != "mailexterne") { ?>
  <div class="pj-card">
    <div class="pj-title">
      <img src="./image/attach.gif" alt="PJ"> Pièce(s) jointe(s)
      <span style="font-size:10px;font-weight:400;color:#999;margin-left:4px">(max <?php print $taille ?>)</span>
    </div>

    <form id="upload_form" enctype="multipart/form-data" method="post">
      <div class="upload-zone">
        <input type="file" name="Filedata" id="Filedata"
          onchange="uploadFile('<?php print trim($idpiecejointe) ?>')">
        <a href="#" onclick="document.getElementById('pj-help').style.display=(document.getElementById('pj-help').style.display=='none'?'block':'none'); return false;">
          <img src="./image/help.gif" border="0" align="center">
        </a>
      </div>
      <div id="pj-help" style="display:none; margin-top:6px; padding:10px 14px; background:#fffbe6; border:1px solid #f5c842; border-radius:6px; font-size:12px; color:#5a4000; max-width:500px; line-height:1.6;">
        Taille maximale autorisée : <strong><?php print $taille ?></strong>.<br>
        Sélectionnez un fichier — l'envoi démarre automatiquement.
      </div>
      <progress id="progressBar" value="0" max="100" style="width:300px;margin-top:8px;display:block"></progress>
      <div id="status"  style="font-size:11px;color:#080A66;margin-top:4px"></div>
      <div id="loaded_n_total" style="font-size:10px;color:#999"></div>
    </form>

    <div id="listingpiecejointe" style="margin-top:8px;font-size:12px"></div>
  </div>

  <div id="fjoint"  style="position:absolute;top:730;left:100;visibility:hidden"></div>
  <div id="fjoint2" style="position:absolute;top:730;left:100;visibility:hidden">
    <font class="T2"><?php print LANGBT5 ?></font>
    <img src="./image/commun/indicator.gif" align="center">
  </div>
  <div id="infofichierjoint" style="position:absolute;top:760;left:10;visibility:hidden"></div>

  <script type="text/javascript">
  function chargement() {
      document.getElementById('fjoint').style.visibility  = "hidden";
      document.getElementById('fjoint2').style.visibility = "visible";
  }
  </script>
  <?php } ?>

</div><!-- /.compose-wrap -->

<?php Pgclose(); ?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
