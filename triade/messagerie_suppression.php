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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<SCRIPT LANGUAGE="JavaScript" src="./librairie_js/messagerie_fenetre.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
<script>
window.alert = function(msg) { if (msg) alertify.error(msg); };
function confirmerSuppMsg() {
    alertify.confirm('Supprimer ?', 'Supprimer définitivement les messages sélectionnés ?',
        function() {
            var h = document.createElement('input');
            h.type = 'hidden'; h.name = 'supp_message'; h.value = '1';
            document.form1.appendChild(h);
            document.form1.submit();
        },
        function() {}
    );
}
</script>
<style>
/* ── Messages Envoyés – Design 2026 ── */
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
.msg-row { border-bottom: 1px solid #eef0f8; transition: background .12s; }
.msg-row:hover { background: #f5f7ff; }
.msg-row td { padding: 9px 10px; vertical-align: middle; }
.msg-subject { font-size: 13px; text-decoration: none; color: #444; }
.msg-subject:hover { color: #080A66; }
.msg-dest { font-size: 12px; color: #555; }
.msg-date { font-size: 11px; color: #888; text-align: center; white-space: nowrap; }
.lu-badge {
    display: inline-block; font-size: 10px; font-weight: 700;
    padding: 2px 7px; border-radius: 10px; white-space: nowrap;
}
.lu-oui  { background: #e8f5e9; color: #2e7d32; }
.lu-non  { background: #fff3e0; color: #e65100; }
.btn-action {
    background: #080A66; color: #fff; border: none; border-radius: 4px;
    padding: 5px 13px; font-size: 12px; cursor: pointer;
    font-family: Electrolize, Arial, sans-serif;
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
        document.getElementById("creatrep").style.visibility = 'visible';
        document.getElementById("repertoire").style.visibility = 'visible';
    }
}
</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
$cnx = cnx();

$id_pers = $_SESSION["id_suppleant"];
if (!verif_si_compte_suppleant($id_pers)) {
    $id_pers = $_SESSION["id_pers"];
}

// Suppression
if (isset($_POST["supp_message"])) {
    for ($i = 0; $i < $_POST["saisie_nb"]; $i++) {
        $cb     = $_POST["saisie_poubelle_".$i] ?? '';
        $id_sup = $_POST["saisie_id_poubelle_".$i] ?? '';
        if ($cb == "on") suppression_message_envoyer($id_sup, $id_pers, 'prof');
    }
}

$idrep = "";
if (isset($_GET["idrep"])) $idrep = $_GET["idrep"];

// Archivage
if (isset($_POST["outil"])) {
    if ($_POST["outil"] == "-1") {
        if ($_POST["repertoire"] != "NULL") {
            creation_repertoire($_SESSION["membre"], $id_pers, $_POST["repertoire"], "mess_supp");
        }
    } else {
        for ($i = 0; $i < $_POST["saisie_nb"]; $i++) {
            $cb     = $_POST["saisie_poubelle_".$i] ?? '';
            $id_sup = $_POST["saisie_id_poubelle_".$i] ?? '';
            if ($cb == "on") messagerie_archive2($id_sup, $_POST["outil"]);
        }
        $idrep = $_POST["repertoire"];
    }
}

$deb = 0;
if (isset($_POST["outil2"])) {
    $idrep = $_POST["outil2"];
    if ($idrep == -2) $idrep = "";
    $deb = 1;
}

if ($_SESSION['membre'] == "menuadmin")     { $destinataire = chercheIdPersonne(strtolower($_SESSION["nom"]), $_SESSION["prenom"], 'ADM'); $type_personne = "ADM"; }
if ($_SESSION['membre'] == "menututeur")    { $destinataire = chercheIdPersonne(strtolower($_SESSION["nom"]), $_SESSION["prenom"], 'TUT'); $type_personne = "TUT"; }
if ($_SESSION['membre'] == "menupersonnel") { $destinataire = chercheIdPersonne(strtolower($_SESSION["nom"]), $_SESSION["prenom"], 'PER'); $type_personne = "PER"; }
if ($_SESSION['membre'] == "menuprof")      { $destinataire = chercheIdPersonne(strtolower($_SESSION["nom"]), $_SESSION["prenom"], 'ENS'); $type_personne = "ENS"; }
if ($_SESSION['membre'] == "menuscolaire")  { $destinataire = chercheIdPersonne(strtolower($_SESSION["nom"]), $_SESSION["prenom"], 'MVS'); $type_personne = "MVS"; }
if ($_SESSION['membre'] == "menuparent")    { $destinataire = chercheIdEleve(strtolower($_SESSION["nom"]), $_SESSION["prenom"]); $type_personne = "PAR"; }
if ($_SESSION['membre'] == "menueleve")     { $destinataire = chercheIdEleve(strtolower($_SESSION["nom"]), $_SESSION["prenom"]); $type_personne = "ELE"; }

$data_rep = select_repertoire_messagerie($id_pers, $_SESSION["membre"], "mess_supp");
?>
<script>CreerFenetreBe();</script>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?>></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'>
    <?php print LANGTMESS419 ?> &nbsp;&mdash;&nbsp;
    <font id='color2'><?php print LANGTMESS416 ?> <?php print recherche_repertoire($idrep, "suppression") ?></font>
  </font></b>
</td></tr>
<tr id='cadreCentral0'><td style="padding:12px">

<?php
// Pagination setup
$fichier = "messagerie_suppression.php#idrep=$idrep";
$table   = "messagerie_envoyer";
$req2    = ($idrep == "") ? "AND (repertoire IS NULL OR repertoire = '0')" : "AND repertoire='$idrep'";
$requete = "WHERE emetteur='$destinataire' $req2 ";
$nbaff   = 20;
$depart  = 0;
if (isset($_GET["nba"]) && $deb != 1) $depart = intval($_GET["limit"]);

$data  = affichage_messagerie_envoyer_limit($type_personne, $id_pers, $depart, $nbaff, $idrep);
$total = countTriade($data);
?>

<!-- ── Barre répertoire (form2) ── -->
<form method="post" name="form2" style="margin:0">
<div class="msg-toolbar">
  <span style="font-size:12px;color:#080A66;font-weight:600"><?php print LANGMESS46 ?> :</span>
  <select name="outil2" class="select-action" onChange="this.form.submit()">
    <option value="0" id="select0"><?php print LANGCHOIX ?></option>
    <option value="-2" id="select1"><?php print LANGMESS48 ?></option>
    <?php
    for ($u = 0; $u < countTriade($data_rep); $u++) {
        $nb = nbmessagerepsupp($data_rep[$u][0], $id_pers, $type_personne);
        print "<option value='".$data_rep[$u][0]."'>".trunchaine($data_rep[$u][1], 25)." ($nb)</option>";
    }
    ?>
  </select>
</div>
</form>

<!-- ── Liste des messages envoyés (form1) ── -->
<form method="POST" name="form1">

<script type="text/javascript">
function validecase() {
    var nb = document.form1.saisie_nb.value;
    var j = 1; // index 0 = checkbox "tous" dans form1
    var checked = document.form1.tous.checked;
    for (var i = 0; i < nb; i++) {
        document.form1.elements[j].checked = checked;
        DisplayLigne('tr' + i);
        j += 2;
    }
}
</script>

<table class="msg-table">
<thead>
  <tr id='coulBar0'>
    <th width="8%" style="text-align:center">
      <input type="checkbox" name="tous" onclick="validecase()" title="Tout sélectionner">
    </th>
    <th width="10%" style="text-align:center"><span class="m"><?php print ucwords(LANGTE9) ?></span></th>
    <th><span class="m"><?php print LANGTE5 ?></span></th>
    <th width="28%"><span class="m"><?php print ucwords(LANGTE6) ?></span></th>
    <th width="12%"><span class="m"><?php print ucwords(LANGTE7) ?></span></th>
  </tr>
</thead>
<tbody>
<?php
for ($i = 0; $i < $total; $i++) {
    // Statut lu/non-lu
    $isLu = (DBTYPE == "pgsql") ? ($data[$i][10] != "f") : ($data[$i][10] != 0);
    $luBadge = $isLu
        ? "<span class='lu-badge lu-oui'>Lu</span>"
        : "<span class='lu-badge lu-non'>Non lu</span>";

    // Destinataire
    if ($data[$i][9] == "PAR") {
        $titre    = "<em>".LANGMESS62."</em> ";
        $emetteur = recherche_eleve($data[$i][2]);
    } elseif ($data[$i][9] == "ELE") {
        $titre    = "<em>".INTITULEELEVE."</em> ";
        $emetteur = recherche_eleve($data[$i][2]);
    } else {
        $titre    = "";
        $emetteur = recherche_personne($data[$i][2]);
    }

    $sujetCourt = trunchaine(stripslashes($data[$i][8]), '50');
    $destCourt  = trunchaine($emetteur, '30');

    print "<tr id='tr$i' class='msg-row' onmouseover=\"this.style.background='#f0f4ff'\" onmouseout=\"this.style.background=''\">";
    print "<td style='text-align:center;padding:8px 6px'>";
    print   "<input type='checkbox' name='saisie_poubelle_$i' onClick=\"DisplayLigne('tr$i')\">";
    print   "<input type='hidden' name='saisie_id_poubelle_$i' value='{$data[$i][0]}'>";
    print "</td>";
    print "<td style='text-align:center;padding:8px'>$luBadge</td>";
    print "<td style='padding:8px 10px'>";
    print   "<img src='./image/lettre1.gif' border='0' align='middle' style='margin-right:5px'>";
    print   "<a href='#' class='msg-subject' onclick=\"return apercu('./messagerie_envoyer_reception_message_verif.php?saisie_id_message={$data[$i][0]}')\">$sujetCourt</a>";
    print "</td>";
    print "<td style='padding:8px 10px'><span class='msg-dest'>$titre$destCourt</span></td>";
    print "<td class='msg-date' style='padding:8px 10px'>".dateForm($data[$i][4])."<br><span style='font-size:10px'>".$data[$i][5]."</span></td>";
    print "</tr>";
}
if ($total === 0) {
    print "<tr><td colspan='5' class='msg-empty'>Aucun message envoyé</td></tr>";
}
?>
</tbody>
</table>

<!-- ── Barre d'actions ── -->
<div class="msg-toolbar" style="margin-top:10px">
  <button type="button" class="btn-action btn-danger" onclick="confirmerSuppMsg()">
    <img src="./image/commun/trash.png" border="0" style="vertical-align:middle;width:14px;margin-right:4px">
    <?php print LANGBT50 ?>
  </button>
  <span class="sep">|</span>
  <span style="font-size:12px;color:#080A66;font-weight:600"><?php print LANGTMESS415 ?> :</span>
  <select name="outil" class="select-action" onChange="archiver()">
    <option value="0" id="select0"><?php print LANGCHOIX ?></option>
    <option value="-1" id="select1"><?php print LANGTMESS412 ?></option>
    <optgroup label="<?php print LANGTMESS420 ?>">
      <?php
      for ($u = 0; $u < countTriade($data_rep); $u++) {
          print "<option value='".$data_rep[$u][0]."'>".trunchaine($data_rep[$u][1], 15)."</option>";
      }
      ?>
    </optgroup>
  </select>
  <input type="text"   name="repertoire" id="repertoire" class="select-action" style="visibility:hidden;width:120px" placeholder="Nom du répertoire">
  <input type="submit" name="creatrep"   id="creatrep"   class="btn-action" value="Créer" style="visibility:hidden">
  <input type="hidden" name="saisie_nb" value="<?php print $total ?>">
</div>

<!-- ── Pagination ── -->
<div class="msg-pagination">
  <div><?php precedent0($fichier, $table, $depart, $nbaff, $requete); ?></div>
  <div><?php suivant0($fichier, $table, $depart, $nbaff, $requete); ?></div>
</div>

</form>

</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
} else {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
}
?>
<SCRIPT language="JavaScript">InitBulle("#000000","#FFFFFF","red",1);</SCRIPT>
<?php include_once("./librairie_php/finbody.php"); ?>
</BODY></HTML>
