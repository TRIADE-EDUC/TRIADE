<?php
session_start();
error_reporting(0);
$messclassic = $_COOKIE['messmodelecture'] ?? '';
if (isset($_POST['messclassic'])) {
    $messclassic = $_POST['messclassic'];
    setcookie("messmodelecture", $messclassic, time()+3600*24*90);
}
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
 *   Site                 : http://www.triade-educ.org
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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<SCRIPT LANGUAGE="JavaScript" src="./librairie_js/messagerie_fenetre.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print $_SESSION['nom']." ".$_SESSION['prenom'] ?></title>
<style>
/* ── Messagerie Réception – Design 2026 ── */
.msg-toolbar {
    display: flex; align-items: center; flex-wrap: wrap; gap: 8px;
    background: #f0f2fa; border: 1px solid #d0d4ee; border-radius: 6px;
    padding: 8px 12px; margin-bottom: 10px;
}
.msg-table {
    width: 100%; border-collapse: collapse;
    background: #fff; border-radius: 8px; overflow: hidden;
    box-shadow: 0 2px 10px rgba(8,10,102,.08);
}
.msg-table thead th {
    padding: 9px 10px; font-size: 12px; font-weight: 600; text-align: left;
}
.msg-table thead th a {
    text-decoration: none;
}
.msg-table thead th a:hover { text-decoration: underline; }
.msg-row { border-bottom: 1px solid #eef0f8; transition: background .12s; }
.msg-row:hover { background: #f5f7ff; }
.msg-row.unread { border-left: 4px solid #080A66; background: #fafbff; }
.msg-row.read   { border-left: 4px solid transparent; }
.msg-row td { padding: 9px 10px; vertical-align: middle; }
.msg-subject { font-size: 13px; text-decoration: none; display: inline; }
.msg-subject.unread { font-weight: 700; color: #080A66; }
.msg-subject.read   { font-weight: 400; color: #444; }
.msg-subject.alert-msg { color: #c62828 !important; }
.msg-sender { font-size: 12px; color: #555; }
.msg-sender.unread { font-weight: 600; color: #222; }
.msg-date { font-size: 11px; color: #888; text-align: center; white-space: nowrap; }
.msg-icon { margin-right: 4px; }
.msg-icon.unread { color: #080A66; }
.msg-icon.read   { color: #CACCEF; }
.badge-pj    { color: #999; font-size: 12px; margin-left: 4px; }
.badge-alert { color: #e53935; font-size: 12px; margin-left: 4px; }
.badge-print { color: #aaa; font-size: 12px; margin-left: 4px; }
.sort-icon   { color: #CACCEF; margin-left: 4px; }
.btn-action {
    background: #080A66; color: #fff; border: none; border-radius: 4px;
    padding: 5px 13px; font-size: 12px; cursor: pointer; font-family: Electrolize, Arial, sans-serif;
}
.btn-action:hover { background: #0d12a0; }
.btn-danger { background: #c62828; }
.btn-danger:hover { background: #e53935; }
.select-action {
    border: 1px solid #c0c4e0; border-radius: 4px;
    padding: 4px 8px; font-size: 12px; font-family: Electrolize, Arial, sans-serif;
}
.msg-empty { text-align: center; padding: 28px; color: #999; font-style: italic; }
.msg-pagination { display: flex; justify-content: space-between; align-items: center; padding: 8px 2px; }
.sep { color: #ccc; }
.mode-toggle label { font-size: 11px; color: #555; cursor: pointer; margin-left: 6px; }
</style>
<script language='JavaScript'>
function archiver() {
    var sel = document.form1.outil;
    var val = sel.options[sel.selectedIndex].value;
    var resultat = val.substr(0, 19);
    if (val != "-1") {
        document.getElementById("creatrep").style.visibility = 'hidden';
        document.getElementById("repertoire").style.visibility = 'hidden';
        if (resultat == "") resultat = "null";
        document.form1.repertoire.value = resultat;
        document.form1.submit();
    } else {
        document.getElementById("creatrep").style.visibility = "visible";
        document.getElementById("repertoire").style.visibility = "visible";
    }
}
function validecase() {
    var nb = document.form1.saisie_nb.value;
    var j = 0;
    var checked = document.form2.tous.checked;
    for (var i = 0; i < nb; i++) {
        document.form1.elements[j].checked = checked;
        DisplayLigne('tr' + i);
        j += 2;
    }
}
</script>
</head>
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
$cnx = cnx();

$id_pers = $_SESSION["id_pers"];
if (isset($_SESSION["id_suppleant"])) {
    $id_pers = $_SESSION["id_suppleant"];
}

$idrep = "";
if (isset($_GET["idrep"])) $idrep = $_GET["idrep"];

if (isset($_POST["outil"])) {
    if ($_POST["outil"] == "-1") {
        if ($_POST["repertoire"] != "NULL") {
            creation_repertoire($_SESSION["membre"], $id_pers, $_POST["repertoire"], "");
        }
    } else {
        for ($i = 0; $i < $_POST["saisie_nb"]; $i++) {
            $cb     = $_POST["saisie_poubelle_".$i] ?? '';
            $id_sup = $_POST["saisie_id_poubelle_".$i] ?? '';
            if ($cb == "on") messagerie_archive($id_sup, $_POST["outil"]);
        }
        $idrep = $_POST["repertoire"];
    }
}

$deb = 0;
if (isset($_POST["outil2"])) {
    $idrep = $_POST["outil2"];
    if ($idrep == 0)  $idrep = "";
    if ($idrep == -2) $idrep = "";
    $deb = 1;
}

if (isset($_POST["suppmess"])) {
    for ($i = 0; $i < $_POST["saisie_nb"]; $i++) {
        $cb     = $_POST["saisie_poubelle_".$i] ?? '';
        $id_sup = $_POST["saisie_id_poubelle_".$i] ?? '';
        if ($cb == "on") corbeille_message($id_sup, $id_pers, 'null');
    }
}

$data_rep = select_repertoire_messagerie($id_pers, $_SESSION["membre"], "");

if ($_SESSION["membre"] == "menuadmin")     { $destinataire = chercheIdPersonne(strtolower($_SESSION["nom"]), $_SESSION["prenom"], 'ADM'); $type_personne = "ADM"; }
if ($_SESSION["membre"] == "menututeur")    { $destinataire = chercheIdPersonne(strtolower($_SESSION["nom"]), $_SESSION["prenom"], 'TUT'); $type_personne = "TUT"; }
if ($_SESSION["membre"] == "menupersonnel") { $destinataire = chercheIdPersonne(strtolower($_SESSION["nom"]), $_SESSION["prenom"], 'PER'); $type_personne = "PER"; }
if ($_SESSION["membre"] == "menuprof")      { $destinataire = chercheIdPersonne(strtolower($_SESSION["nom"]), $_SESSION["prenom"], 'ENS'); $type_personne = "ENS"; }
if ($_SESSION["membre"] == "menuscolaire")  { $destinataire = chercheIdPersonne(strtolower($_SESSION["nom"]), $_SESSION["prenom"], 'MVS'); $type_personne = "MVS"; }
if ($_SESSION["membre"] == "menuparent")    { $destinataire = chercheIdEleve(strtolower($_SESSION["nom"]), $_SESSION["prenom"]); $type_personne = "PAR"; }
if ($_SESSION["membre"] == "menueleve")     { $destinataire = chercheIdEleve(strtolower($_SESSION["nom"]), $_SESSION["prenom"]); $type_personne = "ELE"; }
?>

<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">

<script>CreerFenetreBe();</script>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<a name="ancre">
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?>></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'>
    <?php print LANGTITRE5 ?> &nbsp;&mdash;&nbsp;
    <font id='color2'><?php print LANGTMESS416 ?> <?php print recherche_repertoire($idrep, "reception") ?></font>
  </font></b>
</td></tr>
<tr id='cadreCentral0'><td style="padding:12px">

<?php
// Access validation
$valid = 0;
if (($_SESSION["membre"] == "menuparent") && (ACCESMESSPARENT == "non")) $valid = 1;
if (($_SESSION["membre"] == "menututeur") && (ACCESMESSTUTEUR == "non")) $valid = 1;
if (($_SESSION["membre"] == "menueleve")  && (ACCESMESSELEVE == "non"))  $valid = 1;
if ($valid == 1) {
    if (verifdelegue($id_pers, $_SESSION["membre"], chercheIdClasseDunEleve($id_pers))) {
        if ((MESSDELEGUEELEVE  == "oui") && ($_SESSION["membre"] == "menueleve"))  $valid = 0;
        if ((MESSDELEGUEPARENT == "oui") && ($_SESSION["membre"] == "menuparent")) $valid = 0;
    }
}

if ($valid == 1) {
    print "<br><center><font color='red' class='T2'>".LANGMESS37.".</font></center><br>";
} else {

    // Sort parameters
    $tri = $_GET['tri'] ?? '';
    $sortDefs = [
        'objet'  => ['next' => 'objet2', 'icon' => 'fa-sort-asc'],
        'objet2' => ['next' => 'objet',  'icon' => 'fa-sort-desc'],
        'de'     => ['next' => 'de2',    'icon' => 'fa-sort-asc'],
        'de2'    => ['next' => 'de',     'icon' => 'fa-sort-desc'],
        'date'   => ['next' => 'date2',  'icon' => 'fa-sort-asc'],
        'date2'  => ['next' => 'date',   'icon' => 'fa-sort-desc'],
    ];
    $nextObjet = (strpos($tri, 'objet') === 0) ? $sortDefs[$tri]['next'] : 'objet';
    $nextDe    = (strpos($tri, 'de')    === 0) ? $sortDefs[$tri]['next'] : 'de';
    $nextDate  = (strpos($tri, 'date')  === 0) ? $sortDefs[$tri]['next'] : 'date';
    $sortImgAsc  = "<img src='./image/commun/za2.png' border='0' style='vertical-align:middle'>";
    $sortImgDesc = "<img src='./image/commun/za.png' border='0' style='vertical-align:middle'>";
    $sortImgNone = "";
    $iconObjet = (strpos($tri, 'objet') === 0) ? ($sortDefs[$tri]['icon'] == 'fa-sort-asc' ? $sortImgAsc : $sortImgDesc) : $sortImgNone;
    $iconDe    = (strpos($tri, 'de')    === 0) ? ($sortDefs[$tri]['icon'] == 'fa-sort-asc' ? $sortImgAsc : $sortImgDesc) : $sortImgNone;
    $iconDate  = (strpos($tri, 'date')  === 0) ? ($sortDefs[$tri]['icon'] == 'fa-sort-asc' ? $sortImgAsc : $sortImgDesc) : $sortImgDesc;

    // Build orderby
    $orderby = "";
    if ($tri == "objet")  $orderby = "objet";
    if ($tri == "objet2") $orderby = "objet2";
    if ($tri == "de")     $orderby = "de";
    if ($tri == "de2")    $orderby = "de2";
    if ($tri == "date")   $orderby = "date";
    if ($tri == "date2")  $orderby = "date2";

    // Pagination
    $nbaff   = 20;
    $fichier = "messagerie_reception.php#idrep=$idrep&tri=$tri";
    $table   = "messageries";
    $req2    = ($idrep == "") ? "AND (repertoire IS NULL OR repertoire='0')" : "AND repertoire='$idrep'";
    $requete = "WHERE destinataire='$destinataire' $req2 ";
    if ($tri == "objet")  $requete .= " ORDER BY objet, date DESC, heure DESC";
    if ($tri == "objet2") $requete .= " ORDER BY objet DESC, date DESC, heure DESC";
    if ($tri == "de")     $requete .= " ORDER BY emetteur, date DESC, heure DESC";
    if ($tri == "de2")    $requete .= " ORDER BY emetteur DESC, date DESC, heure DESC";
    if ($tri == "date")   $requete .= " ORDER BY date, heure DESC";
    if ($tri == "date2")  $requete .= " ORDER BY date DESC, heure DESC";

    $depart = 0;
    if (isset($_GET["nba"]) && $deb != 1) $depart = intval($_GET["limit"]);

    $data  = affichage_messagerie_limit($type_personne, $destinataire, $depart, $nbaff, $idrep, $orderby, '0');
    $total = countTriade($data);
?>

<!-- ── Barre de navigation/répertoire (form2) ── -->
<form method="post" name="form2" style="margin:0">
<div class="msg-toolbar">

  <!-- Select all -->
  <label style="display:flex;align-items:center;gap:5px;font-size:12px;color:#080A66;font-weight:600;cursor:pointer">
    <input type="checkbox" name="tous" onclick="validecase()">
    Tout
  </label>
  <span class="sep">|</span>

  <!-- Répertoire -->
  <span style="font-size:12px;color:#080A66;font-weight:600"><?php print LANGMESS46 ?> :</span>
  <select name="outil2" class="select-action" onChange="this.form.submit()">
    <option value="0" id="select0"><?php print LANGCHOIX ?></option>
    <option value="-2" id="select1"><?php print LANGTMESS417 ?></option>
    <?php
    for ($u = 0; $u < countTriade($data_rep); $u++) {
        $nb  = nbmessagerep($data_rep[$u][0], $id_pers, $type_personne);
        $sel = ($idrep == $data_rep[$u][0]) ? "selected='selected'" : "";
        print "<option value='".$data_rep[$u][0]."' $sel>".trunchaine($data_rep[$u][1], 20)." ($nb)</option>";
    }
    ?>
  </select>

  <?php if ((defined("FORWARDMAIL")) && (FORWARDMAIL == "oui")) { ?>
  <span class="sep">|</span>
  <a href="messagerie_foward.php" style="font-size:12px;color:#080A66;text-decoration:none">
    <?php print LANGMESS17 ?> / <?php print LANGASS10 ?>
  </a>
  <?php } ?>

  <!-- Mode lecture -->
  <span style="margin-left:auto" class="mode-toggle">
    <label><input type="radio" name="messclassic" value=""
      <?php if ($messclassic != 'classic') print "checked"; ?> onChange="this.form.submit()"> Aperçu</label>
    <label><input type="radio" name="messclassic" value="classic"
      <?php if ($messclassic == 'classic') print "checked"; ?> onChange="this.form.submit()"> Classique</label>
  </span>
</div>
</form>

<!-- ── Liste des messages (form1) ── -->
<form method="POST" name="form1">

<table class="msg-table">
<thead>
  <tr id='coulBar0'>
    <th width="4%">&nbsp;</th>
    <th>
      <a href="messagerie_reception.php?tri=<?php print $nextObjet ?>&idrep=<?php print $idrep ?>" class="m">
        <?php print LANGTE5 ?> <?php print $iconObjet ?>
      </a>
    </th>
    <th width="28%">
      <a href="messagerie_reception.php?tri=<?php print $nextDe ?>&idrep=<?php print $idrep ?>" class="m">
        <?php print ucwords(LANGTE3) ?> <?php print $iconDe ?>
      </a>
    </th>
    <th width="12%">
      <a href="messagerie_reception.php?tri=<?php print $nextDate ?>&idrep=<?php print $idrep ?>" class="m">
        <?php print LANGTE7 ?> <?php print $iconDate ?>
      </a>
    </th>
  </tr>
</thead>
<tbody>
<?php
for ($i = 0; $i < $total; $i++) {
    $impression = $data[$i][12];
    $alerteFlag = $data[$i][13];

    $isUnread = (DBTYPE == "mysql") ? ($data[$i][10] != "1") : ($data[$i][10] != "t");
    $hasPJ    = fichierJointExiste($data[$i][11]);

    $rowClass  = $isUnread ? "unread" : "read";
    $subjClass = $isUnread ? "unread" : "read";
    $sendClass = $isUnread ? "unread" : "";

    $envelopIcon = $isUnread
        ? "<img src='./image/commun/lettre.gif' border='0' alt='Non lu' style='vertical-align:middle'>"
        : "<img src='./image/commun/lettrelu.gif' border='0' alt='Lu' style='vertical-align:middle'>";

    $pjBadge    = $hasPJ        ? "<img src='./image/attach.gif' border='0' title='".LANGTMESS414."' style='vertical-align:middle'>" : "";
    $alertBadge = ($alerteFlag == 1) ? "<img src='./image/commun/alerte.png' border='0' title='Alerte Message' style='vertical-align:middle;width:14px'>" : "";
    $printBadge = ($impression == 1) ? "<img src='./image/commun/valid.gif' border='0' title='".LANGTMESS413."' style='vertical-align:middle'>" : "";
    $alertStyle = ($alerteFlag == 1) ? " alert-msg" : "";

    // Sender
    if (in_array(trim($data[$i][7]), ['ADM','ENS','MVS','TUT','PER'])) {
        $emetteur = recherche_personne($data[$i][1]);
    } else {
        $prefix   = ($data[$i][7] == "ELE") ? "<em>".INTITULEELEVE." </em>" : (($data[$i][7] == "PAR") ? "<em>".LANGMESS62." </em>" : "");
        $emetteur = $prefix.recherche_eleve($data[$i][1]);
    }

    $sujet      = htmlspecialchars(stripslashes(trim($data[$i][8])));
    $sujetCourt = trunchaine(stripslashes(trim($data[$i][8])), '50');

    if ($messclassic == "classic") {
        $onClick = "open('./messagerie_reception_message.php?saisie_id_message={$data[$i][0]}','messagerie','width=740,height=600,menubar=no,resizable=no,scrollbars=YES,status=no,toolbar=no'); return true;";
    } else {
        $onClick = "return apercu('./messagerie_reception_message.php?saisie_id_message={$data[$i][0]}&et=1'); return true;";
    }

    print "<tr id='tr$i' class='msg-row $rowClass' onmouseover=\"this.style.background='#f0f4ff'\" onmouseout=\"this.style.background=''\">";
    print "<td style='text-align:center;padding:8px 6px'>";
    print   "<input type='checkbox' name='saisie_poubelle_$i' onClick=\"DisplayLigne('tr$i')\">";
    print   "<input type='hidden' name='saisie_id_poubelle_$i' value='{$data[$i][0]}'>";
    print "</td>";
    print "<td style='padding:8px 10px'>";
    print   "$printBadge$alertBadge $envelopIcon ";
    print   "<a href='#' title='$sujet' class='msg-subject $subjClass$alertStyle' onClick=\"$onClick\">$sujetCourt</a> $pjBadge";
    print "</td>";
    print "<td style='padding:8px 10px'><span class='msg-sender $sendClass'>$emetteur</span></td>";
    print "<td class='msg-date' style='padding:8px 10px'>".dateForm($data[$i][4])."<br><span style='font-size:10px'>".$data[$i][5]."</span></td>";
    print "</tr>";
}
if ($total === 0) {
    print "<tr><td colspan='4' class='msg-empty'><i class='fa fa-inbox' style='font-size:28px;color:#ccc;display:block;margin-bottom:8px'></i>Aucun message</td></tr>";
}
?>
</tbody>
</table>

<!-- ── Barre d'actions ── -->
<div class="msg-toolbar" style="margin-top:10px">
  <button type="submit" name="suppmess" class="btn-action btn-danger"
    onclick="return confirm('Supprimer les messages sélectionnés ?')">
    <img src="./image/commun/trash.png" border="0" style="vertical-align:middle;width:14px;margin-right:4px">
    <?php print LANGBT50 ?>
  </button>
  <span class="sep">|</span>
  <span style="font-size:12px;color:#080A66;font-weight:600">
    <?php print LANGTMESS415 ?> :
  </span>
  <select name="outil" class="select-action" onChange="archiver()">
    <option value="0" id="select0"><?php print LANGCHOIX ?></option>
    <option value="-1" id="select1"><?php print LANGTMESS412 ?></option>
    <optgroup label="<?php print LANGTMESS484 ?>">
      <?php
      for ($u = 0; $u < countTriade($data_rep); $u++) {
          print "<option value='".$data_rep[$u][0]."'>".trunchaine($data_rep[$u][1], 15)."</option>";
      }
      ?>
    </optgroup>
  </select>
  <input type="text"   name="repertoire" id="repertoire" class="select-action" style="visibility:hidden;width:120px" placeholder="Nom du répertoire">
  <input type="submit" name="creatrep"   id="creatrep"   class="btn-action" value="Créer" style="visibility:hidden">
  <input type="hidden" name="saisie_nb"  value="<?php print $total ?>">
</div>

<!-- ── Pagination ── -->
<div class="msg-pagination">
  <div><?php precedent0($fichier, $table, $depart, $nbaff, $requete); ?></div>
  <div><?php suivant0($fichier, $table, $depart, $nbaff, $requete); ?></div>
</div>

</form>
<?php } ?>

</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
} else {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
}
Pgclose();
?>
<?php include_once("./librairie_php/finbody.php"); ?>
</BODY></HTML>
