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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_imprmessage.js"></script>
<title>TRIADE - Messagerie</title>
<style>
/* ── Message Envoyé – Design 2026 ── */
* { box-sizing: border-box; }
body {
    background: #f0f2fa;
    margin: 0; padding: 0;
    font-family: Electrolize, Trebuchet MS, Arial, sans-serif;
    font-size: 13px;
}
.msg-wrapper {
    max-width: 780px; margin: 14px auto; padding: 0 10px 20px;
}

/* ── Carte entête ── */
.msg-header-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(8,10,102,.10);
    border-top: 4px solid #080A66;
    padding: 16px 20px 12px;
    margin-bottom: 12px;
}
.msg-meta-row {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 10px; flex-wrap: wrap; margin-bottom: 10px;
}
.msg-people-block {
    display: flex; flex-direction: column; gap: 6px;
}
.msg-person-line {
    display: flex; align-items: baseline; gap: 6px;
}
.msg-person-label {
    font-size: 11px; color: #888; text-transform: uppercase;
    letter-spacing: .5px; min-width: 90px;
}
.msg-person-name {
    font-size: 14px; font-weight: 700; color: #080A66;
}
.msg-person-class {
    font-size: 11px; color: #777;
}
.msg-date-block { text-align: right; white-space: nowrap; }
.msg-date-val {
    font-size: 13px; color: #555; font-weight: 600;
}
.msg-time-val { font-size: 11px; color: #999; }
.msg-subject-row {
    border-top: 1px solid #eef0f8; padding-top: 10px;
    display: flex; align-items: center; justify-content: space-between; gap: 10px;
}
.msg-subject-text {
    font-size: 14px; font-weight: 700; color: #222;
}
.msg-actions { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
.btn-msg {
    display: inline-flex; align-items: center; justify-content: center;
    background: none; border: none; padding: 2px;
    cursor: pointer; text-decoration: none;
    transition: opacity .15s, transform .1s;
    position: relative;
}
.btn-msg:hover { opacity: .75; transform: translateY(-1px); }
.btn-msg[title]:hover::after {
    content: attr(title);
    position: absolute; bottom: calc(100% + 6px); left: 50%; transform: translateX(-50%);
    background: #222; color: #fff; font-size: 11px; white-space: nowrap;
    padding: 3px 8px; border-radius: 4px; pointer-events: none; z-index: 999;
}

/* ── Pièces jointes ── */
.msg-attachments {
    background: #fffbf0; border: 1px solid #f5e0a0; border-radius: 8px;
    padding: 10px 16px; margin-bottom: 12px;
    display: flex; align-items: center; gap: 10px;
    font-size: 12px; color: #7a5a00;
}

/* ── Corps du message ── */
.msg-body-card {
    background: #fff; border-radius: 10px;
    box-shadow: 0 2px 10px rgba(8,10,102,.07);
    padding: 20px 24px; min-height: 200px;
    color: #222; line-height: 1.6; font-size: 13px;
}
.msg-body-card p { margin: 0 0 8px; }

/* ── Footer ── */
.msg-footer {
    margin-top: 16px; border-top: 1px solid #eef0f8;
    padding-top: 10px; text-align: center;
}
</style>
</HEAD>
<body>
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
$cnx = cnx();

$data = affichage_messagerie_envoyer_message($_GET["saisie_id_message"]);
// id_message, emetteur, destinataire, message, date, heure, lu, type_personne, objet, type_personne_dest, idforward_mail, idpiecejointe

for ($i = 0; $i < countTriade($data); $i++) {
    $number  = $data[$i][10];
    $idDest  = $data[$i][2];
    $typeDest = $data[$i][9];
    $idmessage = $data[$i][0];

    // ── Expéditeur
    if (in_array(trim($data[$i][7]), ['ADM','ENS','MVS','TUT','PER'])) {
        $emetteur = recherche_personne($data[$i][1]);
    } else {
        $emetteur = recherche_eleve($data[$i][1]);
    }

    // ── Destinataire
    if (in_array(trim($typeDest), ['ADM','ENS','MVS','TUT','PER'])) {
        $destinataire  = recherche_personne($idDest);
        $classeAffiche = "";
    } else {
        $destinataire  = recherche_eleve($idDest);
        $classe        = chercheClasse_nom(chercheIdClasseDunEleve($idDest));
        $classeAffiche = $classe ? htmlspecialchars(trunchaine($classe, 20)) : "";
    }

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
        $nbPJ    = countTriade($tabficJ);
        $pjLabel = ($nbPJ > 1) ? "$nbPJ pièces jointes" : "1 pièce jointe";
        $pjHtml  = "
        <div class='msg-attachments'>
            <img src='./image/commun/attach.gif' alt='PJ' style='width:16px;height:16px'>
            <span>$pjLabel &mdash;</span>
            <a href='#' onclick=\"AffBulleAvecQuit('Liste des fichiers disponibles','image/commun/info.jpg','$listingdll'); return false;\"
               style='color:#7a5a00;font-weight:600;text-decoration:none'>
               <img src='./image/commun/download.png' border='0' style='vertical-align:middle'> Télécharger
            </a>
        </div>";
    }

    // ── Corps du message
    $message = Decrypte($data[$i][3], $number);
    $message = stripslashes($message);
    $message = preg_replace('/<p>\&nbsp;<\/p>/', '', $message);
    $message = preg_replace('#(\\\\r|\\\\r\\\\n|\\\\n)#', ' ', $message);
    $message = preg_replace('/rnrn/', '', $message);
    $isHtml  = preg_match('/<[a-zA-Z\/!]/', $message);
    $msgHtml = $isHtml ? stripslashes($message) : nl2br(htmlspecialchars(stripslashes($message), ENT_QUOTES, 'UTF-8'));
?>

<div class="msg-wrapper">

  <!-- ── Carte entête ── -->
  <div class="msg-header-card">
    <div class="msg-meta-row">
      <div class="msg-people-block">
        <div class="msg-person-line">
          <span class="msg-person-label"><?php print ucwords(LANGTE3) ?></span>
          <span class="msg-person-name"><?php print htmlspecialchars($emetteur) ?></span>
        </div>
        <div class="msg-person-line">
          <span class="msg-person-label">À</span>
          <span class="msg-person-name"><?php print htmlspecialchars($destinataire) ?></span>
          <?php if ($classeAffiche) print "<span class='msg-person-class'>($classeAffiche)</span>"; ?>
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
        <a href="#" onclick="imprimerMessage(); return false" class="btn-msg" title="Imprimer">
          <img src="./image/commun/email_imprimer.jpg" alt="Imprimer" style="width:30px;height:30px;display:block">
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

  <!-- ── Footer ── -->
  <div class="msg-footer">
    <?php top_p(); ?>
  </div>

</div>

<?php
    $destVerif = chercheIdEleve(strtolower($_SESSION["nom"]), $_SESSION["prenom"]);
    if ($destVerif == $data[$i][2]) {
        lecture_message($data[$i][0]);
    }
}
Pgclose();
?>

<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
<script>
function imprimerMessage() {
    var ok = confirm(langfunc3);
    if (ok) {
        window.print();
        flagImpMessage('<?php print $idmessage ?>');
    }
}
</script>
</body>
</html>
