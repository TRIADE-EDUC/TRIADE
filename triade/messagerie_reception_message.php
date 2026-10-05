<?php
session_start();
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
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./FCKeditor/editor/css/fck_editorarea.css">
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/ajaxIA.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/scriptaculous.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_proto_mail.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_imprmessage.js"></script>
<title>TRIADE - Messagerie</title>
</HEAD>
<body>
<div name="a"></div>
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
$cnx = cnx();
valide_message_lu($_GET["saisie_id_message"]);

@include_once("common/productId.php");
@include_once("common/config-ia.php");
$productId = defined("PRODUCTID") ? PRODUCTID : "";
$iakey     = defined("IAKEY")     ? IAKEY     : "";

$data = affichage_messagerie_message($_GET["saisie_id_message"]);
// id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest, idforward_mail, idpiecejointe, idgroupe

for ($i = 0; $i < countTriade($data); $i++) {
    $number    = $data[$i][10];
    $idgroupe  = $data[$i][12];
    $idmessage = $data[$i][0];

    // ── Expéditeur
    if (in_array(trim($data[$i][7]), ['ADM','ENS','MVS','TUT','PER'])) {
        $emetteur      = recherche_personne($data[$i][1]);
        $classe        = "";
        $classeAffiche = "";
    } else {
        $emetteur = recherche_eleve($data[$i][1]);
        $classe   = chercheClasse_nom(chercheIdClasseDunEleve($data[$i][1]));
        $classeAffiche = $classe ? htmlspecialchars(trunchaine($classe, 20)) : "";
    }

    // ── Groupe mail
    $groupeHtml = "";
    if ($idgroupe && $idgroupe != 0 && !cacherGrpMail($idgroupe)) {
        $libelleGroupe = rechercheLibelleGroupeMail($idgroupe);
        $groupeHtml = "<a href='#anc1' onclick='bul2(); return false' style='font-size:11px;color:#080A66'>[ $libelleGroupe ]</a>";
    }

    // ── Droits réponse
    $valid = 0;
    if (($_SESSION["membre"] == "menuparent") && (ACCESMESSENVOIPARENT == "non")) $valid = 1;
    if (($_SESSION["membre"] == "menueleve")  && (ACCESMESSENVOIELEVE  == "non")) $valid = 1;
    if ((ACCESMESSENVOIPARENT == "non") && (MESSDELEGUEELEVE  == "oui") && ($_SESSION["membre"] == "menueleve"))
        $valid = verifdelegue($_SESSION["id_pers"], $_SESSION["membre"], chercheIdClasseDunEleve($_SESSION["id_pers"])) ? 0 : 1;
    if ((ACCESMESSENVOIPARENT == "non") && (MESSDELEGUEPARENT == "oui") && ($_SESSION["membre"] == "menuparent"))
        $valid = verifdelegue($_SESSION["id_pers"], $_SESSION["membre"], chercheIdClasseDunEleve($_SESSION["id_pers"])) ? 0 : 1;

    // ── URLs réponse / transfert
    $isClassic = (trim($_COOKIE["messmodelecture"] ?? '') == "classic");
    $isIE      = (($_SESSION["navigateur"] == "IE") || ($_GET['et'] == '1'));
    if ($valid != 1) {
        if ($isClassic || !$isIE) {
            $urlRepondre  = "messagerie_reponse2.php?saisie_id_message={$data[$i][0]}";
            $targetRep    = "_self";
        } else {
            $urlRepondre  = "messagerie_reponse.php?et={$_GET['et']}&saisie_id_message={$data[$i][0]}";
            $targetRep    = "_top";
        }
        $urlTransfert = "messagerie_envoi.php?saisie_id_message={$data[$i][0]}&f=1";
        $targetTrf    = $isIE && !$isClassic ? "_top" : "_self";
    }

    // ── IA audio
    $messageAudio = Decrypte($data[$i][3], $number);
    $messageAudio = preg_replace('/\n/', ' ', $messageAudio);
    $messageAudio = strip_tags($messageAudio);
    $messageAudio = urlencode($messageAudio);
    if (file_exists("./common/config-ia.php")) {
        $lienIA = "alert('Chargement du message\\n\\nVeuillez patienter...');ecoutermessage('".addslashes($messageAudio)."');";
    } else {
        $lienIA = "alert('Votre Triade n\\'est pas configuré pour utiliser l\\'IA. Contacter votre administrateur Triade')";
    }

    // ── Corps du message
    $message = Decrypte($data[$i][3], $number);
    $message = stripslashes($message);
    $message = preg_replace('/<p>\&nbsp;<\/p>/', '', $message);
    $message = preg_replace('#(\\\\r|\\\\r\\\\n|\\\\n)#', ' ', $message);
    $isHtml  = preg_match('/<[a-zA-Z\/!]/', $message);
    $msgHtml = $isHtml ? stripslashes($message) : nl2br(htmlspecialchars(stripslashes($message), ENT_QUOTES, 'UTF-8'));

    // ── Pièces jointes
    $tabficJ = fichierJointExiste($data[0][11]);
    $pjHtml  = "";
    if (countTriade($tabficJ) > 0) {
        $listingdll = "<font class=T1>";
        for ($j = 0; $j < countTriade($tabficJ); $j++) {
            $nom = $tabficJ[$j][1];
            $md5 = $tabficJ[$j][0];
            $listingdll .= " - <a href=\\'accessfichier.php?id=$md5\\' target=\\'_blank\\'>$nom</a><br />";
        }
        $listingdll .= "</font>";
        $nbPJ = countTriade($tabficJ);
        $pjLabel = ($nbPJ > 1) ? "$nbPJ pièces jointes" : "1 pièce jointe";
        $pjHtml = "
        <div class='msg-attachments'>
            <i class='fa fa-paperclip'></i>
            <span>$pjLabel &mdash;</span>
            <a href='#' onclick=\"AffBulleAvecQuit('Liste des fichiers disponibles','image/commun/info.jpg','$listingdll'); return false;\"
               style='color:#7a5a00;font-weight:600;text-decoration:none'>
               <i class='fa fa-download'></i> Télécharger
            </a>
        </div>";
    }
?>

<div class="msg-wrapper">

  <!-- ── Carte entête ── -->
  <div class="msg-header-card">
    <div class="msg-meta-row">
      <div class="msg-from-block">
        <div class="msg-from-label"><?php print ucwords(LANGTE3) ?></div>
        <div class="msg-from-name">
          <?php print htmlspecialchars($emetteur) ?>
          <?php if ($classeAffiche) print "<span class='msg-from-class'>(<?php print $classeAffiche ?>)</span>"; ?>
          <?php if ($groupeHtml) print "&nbsp;$groupeHtml"; ?>
        </div>
      </div>
      <div class="msg-date-block">
        <div class="msg-date-val"><?php print dateForm($data[$i][4]) ?></div>
        <div class="msg-time-val"><?php print $data[$i][5] ?></div>
      </div>
    </div>

    <div class="msg-subject-row">
      <div class="msg-subject-text">
<?php print htmlspecialchars(stripslashes($data[$i][8])) ?>
      </div>
      <div class="msg-actions">
        <?php if ($valid != 1) { ?>
        <a href="#" onclick="open('<?php print $urlRepondre ?>','<?php print $targetRep ?>',''); return true" class="btn-msg btn-reply" title="Répondre">
          <img src="./image/commun/email_repondre.png" alt="Répondre" style="width:30px;height:30px;display:block">
        </a>
        <a href="#" onclick="open('<?php print $urlTransfert ?>','<?php print $targetTrf ?>',''); return true" class="btn-msg btn-forward" title="Transférer">
          <img src="./image/commun/email_forward.png" alt="Transférer" style="width:30px;height:30px;display:block">
        </a>
        <?php } ?>
        <a href="#" onclick="alerteMessage('<?php print $data[$i][0] ?>'); return false" class="btn-msg btn-alert" title="Alerte Message">
          <img src="./image/commun/email_alerte.png" alt="Alerte" style="width:30px;height:30px;display:block">
        </a>
        <a href="#" onclick="<?php print $lienIA ?>" class="btn-msg" title="Écouter avec l'IA">
          <img src="./image/commun/email_son.png" alt="Écouter" style="width:30px;height:30px;display:block">
        </a>
        <a href="#" onclick="imprimerMessage(); return false" class="btn-msg" title="Imprimer">
          <img src="./image/commun/email_imprimer.png" alt="Imprimer" style="width:30px;height:30px;display:block">
        </a>
      </div>
    </div>
  </div>

  <!-- ── Pièces jointes ── -->
  <?php print $pjHtml ?>

  <!-- ── Corps du message ── -->
  <div class="msg-body-card">
    <div id="editor"><?php print $msgHtml ?></div>
  </div>

  <!-- ── Footer pub ── -->
  <div class="msg-footer">
    <div class="msg-pub-label">TRIADE-ACTU</div>
    <div class="msg-pub-wrap"><?php top_p(); ?></div>
  </div>

</div>

<?php
    $destinataire = chercheIdEleve(strtolower($_SESSION["nom"]), $_SESSION["prenom"]);
    if ($destinataire == $data[$i][2]) {
        lecture_message($data[$i][0]);
    }
}
Pgclose();
?>

<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
<script>
<?php if ($idgroupe && $idgroupe != 0 && !cacherGrpMail($idgroupe)) { ?>
var etat2 = 0;
function bul2() {
    if (etat2 == 0) {
        AffBulle3('<?php print LANGMESS64 ?>','./image/commun/info.jpg',"");
        listingGroupeMail("<?php print $idgroupe ?>");
        etat2 = 1;
    } else {
        HideBulle();
        etat2 = 0;
    }
}
<?php } ?>

function imprimerMessage() {
    var ok = confirm(langfunc3);
    if (ok) {
        window.print();
        flagImpMessage('<?php print $idmessage ?>');
    }
}

function ecoutermessage(message) {
    ajaxAudioMessage(message, '<?php print $productId ?>', '<?php print $iakey ?>');
}

alerteMessage = function(id) {
    new Ajax.Request("ajaxAlerteMessage.php", {
        method: "post",
        parameters: "id=" + id,
        asynchronous: true,
        timeout: 5000,
        onComplete: infoText
    });
};

infoText = function(request) { alert(request.responseText); };
</script>
</body>
</html>
