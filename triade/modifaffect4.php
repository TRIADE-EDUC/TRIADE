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
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css">
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<title>Triade — Ordre des affectations</title>
<style>
/* ── dhtmgoodies requis ─────────────────────────────────────────────────── */
#movableNode      { position:absolute; }
#arrDestInditcator{ position:absolute; display:none; width:100px; }
#arrangableNodes,#movableNode ul{ padding-left:0;margin-left:0;margin-top:0;padding-top:0; }
#arrangableNodes li,#movableNode li{ list-style-type:none; cursor:default; }

/* ── Lignes drag ────────────────────────────────────────────────────────── */
#arrangableNodes li{
  display:flex;align-items:center;gap:8px;flex-wrap:wrap;
  padding:7px 10px;margin-bottom:4px;
  background:#fff;border:1px solid #dde0f0;border-radius:6px;
  font-family:Electrolize,'Trebuchet MS',Arial;font-size:11px;
  user-select:none;transition:box-shadow .15s;
}
#arrangableNodes li:hover{ box-shadow:0 2px 8px rgba(8,10,102,.12);border-color:#c5cae9; }
#movableNode li{
  display:flex;align-items:center;gap:8px;flex-wrap:wrap;
  padding:7px 10px;
  background:#e8eaf6;border:2px dashed #080A66;border-radius:6px;
  font-family:Electrolize,'Trebuchet MS',Arial;font-size:11px;
  opacity:.85;
}

/* ── Handle + éléments ──────────────────────────────────────────────────── */
.ma4-handle{
  display:flex;align-items:center;gap:5px;
  padding:4px 8px;border-radius:4px;
  background:#080A66;color:#fff;font-weight:700;font-size:11px;
  cursor:move;white-space:nowrap;min-width:48px;justify-content:center;
}
.ma4-mat { font-weight:700;color:#080A66;min-width:140px;flex:2; }
.ma4-prof{ color:#444;min-width:120px;flex:2; }
.ma4-chip{
  display:inline-flex;align-items:center;gap:3px;
  padding:2px 7px;border-radius:10px;font-size:10px;font-weight:600;
  background:#e8eaf6;color:#555;white-space:nowrap;
}
.ma4-chip.visu-on { background:#e8f5e9;color:#2e7d32; }
.ma4-chip.visu-off{ background:#fce4e4;color:#c62828; }

/* ── Indicateur de position ─────────────────────────────────────────────── */
#arrDestInditcator{
  left:0;height:3px;background:#080A66;border-radius:2px;
}
</style>

<script type="text/javascript">
/************************************************************************************************************
(C) www.dhtmlgoodies.com, October 2005 — drag & drop list reorder
************************************************************************************************************/
var offsetYInsertDiv = -3;
if (!document.all) offsetYInsertDiv = offsetYInsertDiv - 7;

var arrParent = false, arrMoveCont = false, arrMoveCounter = -1;
var arrTarget = false, arrNextSibling = false;
var leftPosArrangableNodes = false, widthArrangableNodes = false;
var nodePositionsY = new Array(), nodeHeights = new Array();
var arrInsertDiv = false, insertAsFirstNode = false, arrNodesDestination = false;

function cancelEvent() { return false; }

function getTopPos(inputObj) {
    var r = inputObj.offsetTop;
    while ((inputObj = inputObj.offsetParent) != null) r += inputObj.offsetTop;
    return r;
}
function getLeftPos(inputObj) {
    var r = inputObj.offsetLeft;
    while ((inputObj = inputObj.offsetParent) != null) r += inputObj.offsetLeft;
    return r;
}
function clearMovableDiv() {
    if (arrMoveCont.getElementsByTagName('LI').length > 0) {
        if (arrNextSibling) arrParent.insertBefore(arrTarget, arrNextSibling);
        else arrParent.appendChild(arrTarget);
    }
}
function initMoveNode(e) {
    clearMovableDiv();
    if (document.all) e = event;
    arrMoveCounter = 0;
    arrTarget = this;
    if (this.nextSibling) arrNextSibling = this.nextSibling; else arrNextSibling = false;
    timerMoveNode();
    arrMoveCont.parentNode.style.left = e.clientX + 'px';
    arrMoveCont.parentNode.style.top  = e.clientY + 'px';
    return false;
}
function timerMoveNode() {
    if (arrMoveCounter >= 0 && arrMoveCounter < 10) {
        arrMoveCounter++;
        setTimeout('timerMoveNode()', 20);
    }
    if (arrMoveCounter >= 10) arrMoveCont.appendChild(arrTarget);
}
function arrangeNodeMove(e) {
    if (document.all) e = event;
    if (arrMoveCounter < 10) return;
    if (document.all && arrMoveCounter >= 10 && e.button != 1 && navigator.userAgent.indexOf('Opera') == -1)
        arrangeNodeStopMove();
    arrMoveCont.parentNode.style.left = e.clientX + 'px';
    arrMoveCont.parentNode.style.top  = e.clientY + 'px';
    var tmpY = e.clientY;
    arrInsertDiv.style.display = 'none';
    arrNodesDestination = false;
    if (e.clientX < leftPosArrangableNodes || e.clientX > leftPosArrangableNodes + widthArrangableNodes) return;
    var subs = arrParent.getElementsByTagName('LI');
    for (var no = 0; no < subs.length; no++) {
        var topPos = getTopPos(subs[no]);
        var tmpHeight = subs[no].offsetHeight;
        if (no == 0 && tmpY <= topPos && tmpY >= topPos - 5) {
            arrInsertDiv.style.top = (topPos + offsetYInsertDiv) + 'px';
            arrInsertDiv.style.display = 'block';
            arrNodesDestination = subs[no];
            insertAsFirstNode = true;
            return;
        }
        if (tmpY >= topPos && tmpY <= (topPos + tmpHeight)) {
            arrInsertDiv.style.top = (topPos + tmpHeight + offsetYInsertDiv) + 'px';
            arrInsertDiv.style.display = 'block';
            arrNodesDestination = subs[no];
            insertAsFirstNode = false;
            return;
        }
    }
}
function arrangeNodeStopMove() {
    arrMoveCounter = -1;
    arrInsertDiv.style.display = 'none';
    if (arrNodesDestination) {
        var subs = arrParent.getElementsByTagName('LI');
        if (arrNodesDestination == subs[0] && insertAsFirstNode) {
            arrParent.insertBefore(arrTarget, arrNodesDestination);
        } else {
            if (arrNodesDestination.nextSibling)
                arrParent.insertBefore(arrTarget, arrNodesDestination.nextSibling);
            else
                arrParent.appendChild(arrTarget);
        }
    }
    arrNodesDestination = false;
    clearMovableDiv();
}
function saveArrangableNodes() {
    var nodes  = arrParent.getElementsByTagName('LI');
    var string = "";
    for (var no = 0; no < nodes.length; no++) {
        if (string.length > 0) string += ',';
        string += nodes[no].id;
    }
    document.forms[0].hiddenNodeIds.value = string;
    document.forms[0].submit();
}
function initArrangableNodes() {
    arrParent   = document.getElementById('arrangableNodes');
    arrMoveCont = document.getElementById('movableNode').getElementsByTagName('UL')[0];
    arrInsertDiv= document.getElementById('arrDestInditcator');
    leftPosArrangableNodes = getLeftPos(arrParent);
    arrInsertDiv.style.left = leftPosArrangableNodes - 5 + 'px';
    widthArrangableNodes = arrParent.offsetWidth;
    var subs = arrParent.getElementsByTagName('LI');
    for (var no = 0; no < subs.length; no++) {
        subs[no].onmousedown   = initMoveNode;
        subs[no].onselectstart = cancelEvent;
    }
    document.documentElement.onmouseup    = arrangeNodeStopMove;
    document.documentElement.onmousemove  = arrangeNodeMove;
    document.documentElement.onselectstart= cancelEvent;
}
window.onload = initArrangableNodes;
</script>
</head>
<body id='bodyfond2'>
<?php
include("librairie_php/lib_licence.php");
if (empty($_SESSION["adminplus"])) {
    print "<script>location.href='./base_de_donne_key.php'</script>";
    exit;
}
include_once('librairie_php/db_triade.php');
$cnx = cnx();

if (isset($_POST["hiddenNodeIds"])) {
    $liste        = $_POST["hiddenNodeIds"];
    $idclasse     = $_POST["idclasse"];
    $anneeScolaire= $_POST["anneeScolaire"];
    $tri          = $_POST["saisie_tri"];
    $cid          = $idclasse;
    $dataClasse   = chercheClasse($cid);
    $nom_classe   = $dataClasse[0][1];
    $matGroup     = matGroup($nom_classe);
    $tablist      = explode(",", $liste);
    $cr           = modifOrdreaffectation($idclasse, $tablist, $tri, $anneeScolaire);
    if ($cr) alertJs(LANGDONENR);
} else {
    $cid          = $_GET["saisie_classe_envoi"];
    $tri          = $_GET["saisie_tri"];
    $anneeScolaire= $_GET["anneeScolaire"];
    $dataClasse   = chercheClasse($_GET["saisie_classe_envoi"]);
    $nom_classe   = $dataClasse[0][1];
    $matGroup     = matGroup($nom_classe);
}

$sql = <<<SQL
SELECT code_mat, libelle, sous_matiere
FROM {$prefixe}matieres
ORDER BY libelle
SQL;
$cursor = execSql($sql);
$data   = chargeMat($cursor);
freeResult($cursor);
for ($l = 0; $l < countTriade($data); $l++) {
    for ($c = 0; $c < countTriade($data); $c++) {
        $bool = empty($data[$l][2]) ? 0 : true;
        $matMat[$l][0] = $data[$l][0].":".$bool;
        $matMat[$l][1] = trim($data[$l][1])." ".trim($data[$l][2]);
    }
}
?>

<?php if (!empty($_SESSION["adminplus"])): ?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'>
  <td height="2">
    <b><font id='menumodule1'>
      <i class="bi bi-list-ol" style="margin-right:6px"></i>Ordre des affectations — <?php print $nom_classe; ?>
      <span style="font-size:11px;font-weight:400">&nbsp;(<?php print $anneeScolaire; ?>)</span>
    </font></b>
  </td>
</tr>
<tr id='cadreCentral0'>
<td style="padding:10px">

<!-- ── Instruction ───────────────────────────────────────────────────────── -->
<div class="card" style="margin-bottom:10px">
<div class="card-body" style="padding:8px 12px;font-size:11px;font-family:Electrolize,'Trebuchet MS',Arial;color:#444">
  <i class="bi bi-grip-vertical" style="color:#080A66;font-size:14px;margin-right:6px"></i>
  Faites glisser les lignes pour modifier l'ordre — <strong>cliquer et déplacer</strong> sur le bouton bleu <kbd style="background:#080A66;color:#fff;border-radius:3px;padding:1px 5px;font-size:10px">N°</kbd>
</div>
</div>

<!-- ── Liste drag & drop ─────────────────────────────────────────────────── -->
<ul id='arrangableNodes'>
<?php
$data = visu_affectation_detail_2($_GET["saisie_classe_envoi"], $tri, $anneeScolaire);
for ($a = 0; $a < countTriade($data); $a++):
    $nomMatiere      = ucwords(chercheMatiereNom($data[$a][1]));
    $idMatiere       = $data[$a][1];
    $nomProf         = recherche_personne($data[$a][2]);
    $idProf          = $data[$a][2];
    $coef            = $data[$a][4];
    $nomGrp          = trim($data[$a][5]);
    $idGrp           = chercheGroupeId($data[$a][5]);
    $idLang          = $data[$a][6];
    $nomLang         = $data[$a][6];
    $ordre           = $data[$a][0];
    $avecSousMatiere = $data[$a][7];
    $visubull        = $data[$a][8];
    $nbheure         = $data[$a][9];
    $ects            = $data[$a][10];
    $id_ue_detail    = $data[$a][11];
    $visubullbtsblanc= $data[$a][14];
    $info_semestre   = $data[$a][15];
    $specif_etat     = $data[$a][12];

    $tab    = recupNomUE($id_ue_detail);
    $nom_ue = trunchaine($tab[0][0], 30);
    $ue     = $tab[0][1];

    $visuClass = ($visubull == "1") ? "visu-on" : "visu-off";
    $visuLabel = ($visubull == "1") ? "Bulletin ✓" : "Bulletin ✗";
?>
<li id="<?php print "${a}:${idMatiere}:${idProf}:${coef}:${idGrp}:$idLang:$avecSousMatiere:$visubull:$ects:$ue:$specif_etat:$visubullbtsblanc:$info_semestre:$nbheure:$id_ue_detail"; ?>">
  <div class="ma4-handle">
    <i class="bi bi-grip-vertical"></i> N°<?php print $a; ?>
  </div>
  <div class="ma4-mat">
    <i class="bi bi-book-fill" style="color:#c5cae9;margin-right:4px"></i><?php print $nomMatiere; ?>
  </div>
  <div class="ma4-prof">
    <i class="bi bi-person" style="color:#aaa;margin-right:3px"></i><?php print trunchaine($nomProf, 28); ?>
  </div>
  <span class="ma4-chip"><i class="bi bi-x-circle" style="font-size:9px"></i>&nbsp;Coef&nbsp;<?php print $coef; ?></span>
  <?php if ($nomGrp && $nomGrp !== "Choix"): ?>
  <span class="ma4-chip"><i class="bi bi-people" style="font-size:9px"></i>&nbsp;<?php print $nomGrp; ?></span>
  <?php endif; ?>
  <?php if ($nomLang && $nomLang !== "0"): ?>
  <span class="ma4-chip"><?php print $nomLang; ?></span>
  <?php endif; ?>
  <span class="ma4-chip <?php print $visuClass; ?>"><?php print $visuLabel; ?></span>
  <?php if ($nbheure): ?><span class="ma4-chip"><i class="bi bi-clock" style="font-size:9px"></i>&nbsp;<?php print $nbheure; ?>h</span><?php endif; ?>
  <?php if ($ects): ?><span class="ma4-chip">ECTS&nbsp;<?php print $ects; ?></span><?php endif; ?>
  <?php if ($nom_ue): ?><span class="ma4-chip" title="<?php print $tab[0][0]; ?>"><i class="bi bi-grid" style="font-size:9px"></i>&nbsp;<?php print $nom_ue; ?></span><?php endif; ?>
  <?php if ($info_semestre && $info_semestre > 0): ?><span class="ma4-chip">Sem.&nbsp;<?php print $info_semestre; ?></span><?php endif; ?>
</li>
<?php endfor; ?>
</ul>

<!-- ── Boutons ───────────────────────────────────────────────────────────── -->
<div style="display:flex;gap:8px;margin-top:12px;align-items:center">
  <script language=JavaScript>buttonMagicSubmit3("<?php print VALIDER ?>","create","onclick='saveArrangableNodes();return false'");</script>
  <script language=JavaScript>buttonMagicFermeture();</script>
</div>

<!-- ── Éléments dhtmgoodies ─────────────────────────────────────────────── -->
<div id="movableNode"><ul></ul></div>
<div id="arrDestInditcator"></div>
<div id="arrDebug"></div>

<form method="post">
  <input type="hidden" name="hiddenNodeIds">
  <input type="hidden" name="idclasse"      value="<?php print $_GET["saisie_classe_envoi"]; ?>">
  <input type="hidden" name="saisie_tri"    value="<?php print $tri; ?>">
  <input type="hidden" name="anneeScolaire" value="<?php print $anneeScolaire; ?>">
</form>

</td></tr></table>
<?php endif; ?>

</BODY></HTML>
