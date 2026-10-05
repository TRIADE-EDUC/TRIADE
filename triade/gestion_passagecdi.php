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
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<style>
*{box-sizing:border-box}
body{font-family:Arial,sans-serif;background:#f0f2fa;margin:0;padding:0;font-size:14px}

/* Header */
.cp-header{background:#080A66;color:#fff;padding:10px 16px;display:flex;align-items:center;justify-content:space-between;gap:10px}
.cp-header-title{font-size:16px;font-weight:700}
.cp-header-info{font-size:11px;opacity:.8}

/* Layout */
.cp-body{display:grid;grid-template-columns:1fr 1fr;gap:12px;padding:12px}
.cp-panel{background:#fff;border-radius:10px;padding:14px;border:1px solid #dde0f0}

/* Badge */
.cp-badge-row{display:flex;gap:8px;align-items:center;margin-bottom:12px}
.cp-badge-label{font-size:13px;font-weight:700;color:#080A66;white-space:nowrap}
.cp-badge-input{flex:1;font-size:16px;padding:10px 12px;border:2px solid #dde0f0;border-radius:8px;color:#000066;background:#eef0ff;height:46px}
.cp-badge-input:focus{border-color:#080A66;outline:none}
.cp-badge-btn{background:#080A66;color:#fff;border:none;border-radius:8px;padding:10px 14px;font-size:13px;font-weight:700;cursor:pointer;height:46px;white-space:nowrap;touch-action:manipulation}
.cp-badge-btn:active{background:#0b0f8a}
.cp-badge-btn.reading{background:#e65100}

.cp-confirm-msg{color:#2e7d32;font-weight:700;font-size:13px;min-height:20px;margin-bottom:8px}

/* Passage type buttons */
.cp-meals-title{font-size:12px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;padding-bottom:6px;border-top:1px solid #eef0f8;padding-top:10px}
.cp-meals{display:flex;flex-wrap:wrap;gap:10px}
.cp-meal-btn{background:#e8eaf6;color:#080A66;border:none;border-radius:10px;padding:12px 16px;font-size:15px;font-weight:700;cursor:pointer;min-height:54px;min-width:110px;flex:1;box-shadow:0 3px 8px rgba(8,10,102,.15);touch-action:manipulation;transition:transform .1s,box-shadow .1s}
.cp-meal-btn:hover{background:#c5cae9}
.cp-meal-btn:active{transform:scale(.97);box-shadow:0 1px 4px rgba(0,0,0,.15)}

/* Person card */
.cp-person-top{display:flex;gap:12px;align-items:flex-start;margin-bottom:12px}
.cp-person-photo{width:90px;height:90px;border-radius:12px;object-fit:cover;box-shadow:0 3px 10px rgba(0,0,0,.2);flex-shrink:0}
.cp-person-details{flex:1}
.cp-person-name{font-size:18px;font-weight:700;color:#080A66;line-height:1.2}
.cp-person-prenom{font-size:16px;color:#333;margin-top:2px}
.cp-person-fonction{font-size:12px;color:#777;margin-top:4px;font-style:italic}
.cp-alerte{color:#c62828;font-weight:700;font-size:14px}

/* Passage list */
.cp-plateau-title{font-size:12px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px}
.cp-plateau-list{background:#f8f9ff;border:1px solid #dde0f0;border-radius:8px;padding:8px 10px;min-height:80px;max-height:120px;overflow-y:auto;font-size:13px;color:#333}

/* Action buttons */
.cp-actions{display:flex;gap:8px;margin-top:14px}
.cp-btn{border:none;border-radius:10px;padding:14px 12px;font-size:15px;font-weight:700;cursor:pointer;touch-action:manipulation;transition:filter .15s}
.cp-btn:active{filter:brightness(.9)}
.cp-btn-cancel{flex:1;background:#c62828;color:#fff}
.cp-btn-confirm{flex:2;background:#9e9e9e;color:#fff}
.cp-btn-confirm.active{background:#C60417}
.cp-btn:disabled{background:#bbb;cursor:not-allowed}

/* History table */
.cp-history{padding:0 12px 12px}
.cp-history-title{font-size:12px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;padding-top:4px}
.cp-hist-table{width:100%;border-collapse:collapse;background:#fff;border-radius:10px;overflow:hidden;border:1px solid #dde0f0;font-size:12px}
.cp-hist-table thead th{background:#080A66;color:#fff;padding:8px 10px;text-align:left;font-weight:600}
.cp-hist-table tbody tr{border-bottom:1px solid #eef0f8}
.cp-hist-table tbody tr:nth-child(even){background:#f8f9ff}
.cp-hist-table td{padding:7px 10px;vertical-align:middle}

/* No-passage warning modal */
.cp-noPlat-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:900;display:none;align-items:center;justify-content:center}
.cp-noPlat-overlay.visible{display:flex}
.cp-noPlat-box{background:#fff;border-radius:14px;padding:30px 24px;text-align:center;width:340px;box-shadow:0 8px 32px rgba(0,0,0,.25)}
.cp-noPlat-msg{font-size:16px;font-weight:700;color:#c62828;margin-bottom:20px}
.cp-noPlat-close{background:#080A66;color:#fff;border:none;border-radius:8px;padding:12px 28px;font-size:15px;font-weight:700;cursor:pointer}
</style>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/scriptaculous.js"></script>
<script language="JavaScript" src="./librairie_js/xorax_serialize.js"></script>
<title>Passage CDI</title>
<script>
ajaxCherchePassage = function(id) {
    new Ajax.Request("ajaxCherchePassage.php", {
        method: "post",
        parameters: "id="+id,
        asynchronous: true,
        timeout: 5000,
        onComplete: infoText
    });
}

infoText = function(request) {
    var tab = unserialize(request.responseText);
    var listing = document.getElementById("menucdi");
    var info = unescape(tab[0][1]);
    info = decodeURIComponent(info.replace(/\+/g, ' '));
    listing.innerHTML += "<div style='padding:3px 0;border-bottom:1px solid #eef0f8'>&#10003; " + info + "</div>";
    var somme = document.getElementById("somme");
    var newEl;
    if (window.ActiveXObject) {
        newEl = document.createElement("<input name='plat[]'>");
    } else {
        newEl = document.createElement("input");
        newEl.setAttribute("name","plat[]");
    }
    newEl.setAttribute("type","hidden");
    newEl.setAttribute("value",tab[0][0]+"#||#"+tab[0][1]);
    newEl.setAttribute("readonly","readonly");
    somme.appendChild(newEl);
    document.formulaire.platok.value = '1';
}

function valideFormulaire2() {
    if (document.formulaire.platok.value == "0") {
        document.getElementById('cp-noPlat').classList.add('visible');
        return false;
    } else {
        document.formulaire.valide.value = 1;
        document.formulaire.submit();
    }
}

function activerLectureBadge() {
    var btn = document.getElementById('action');
    document.formulaire.codebar.focus();
    btn.classList.add('reading');
    btn.textContent = 'Lecture en cours…';
    var enreff = document.getElementById('enreff');
    if (enreff) enreff.style.display = 'none';
}

function listenKey(code) {
    if (code == "113") { activerLectureBadge(); }
    if (code == "119") { valideFormulaire2(); }
}
if (navigator.appName == "Microsoft Internet Explorer") {
    function toucheA() { listenKey(event.keyCode); }
    document.onkeydown = toucheA;
} else {
    function toucheB(evnt) { listenKey(evnt.keyCode); }
    document.onkeydown = toucheB;
}

function verifScrool() {
    var element = document.getElementById('menucdi');
    if (element) element.scrollTop = element.scrollHeight;
    window.setTimeout('verifScrool()','300');
}
</script>
</head>
<body onload="verifScrool();">
<?php include("./librairie_php/lib_licence.php"); ?>

<?php
include_once('./librairie_php/db_triade.php');
$cnx = cnx();
$image = "idP=''";

if ((verifDroit($_SESSION["id_pers"],"CDI")) || ($_SESSION["membre"] == "menuadmin")) {

    $alerteButton = 1;
    $ideleve = "";
    $membre  = "";
    $valide  = "";
    $colorB  = "";
    $disabled2 = "disabled";
    $ALERTE = ""; $ALERTCONFIRM = "";
    $nom = ""; $prenom = ""; $fonction = "";

    if (isset($_POST["codebar"])) {
        $data = rechercheIdPersViaCodeBarre(trim($_POST["codebar"]));
        if (countTriade($data) > 0) {
            $ideleve = $data[0][0];
            $valide  = $data[0][1];
            $membre  = $data[0][2];
            $membre2 = 'NONELE';
        }
        if ($membre == "menueleve") {
            $fonction = " ".chercheClasse_nom(chercheClasseEleve($ideleve));
            $membre2  = 'ELE';
            $attribue = "menueleve";
            $image    = "idE=$ideleve";
        }
        if ($valide == 1) {
            $nom       = strtoupper(recherche_personne_nom($ideleve,$membre2));
            $prenom    = ucwords(recherche_personne_prenom($ideleve,$membre2));
            $nomprenom = "$nom $prenom";
            $alerteButton = 0;
            $colorB    = "active";
            $disabled2 = "";
        } elseif ($valide == 0) {
            $ALERTE = "BADGE NON VALIDE !";
        } else {
            $ALERTE = "BADGE NON LU !";
        }
    }

    if (isset($_POST["reset"])) { $ALERTE = ""; }

    if ((isset($_POST["valide"])) && ($_POST["valide"] == 1)) {
        $ALERTE = "";
        $idpers = $_POST["idpers"];
        $plat   = $_POST["plat"];
        $membre = $_POST["membre"];
        $cr = enrPassageCompte($idpers,$plat,$membre);
        $ALERTCONFIRM = "<span id='enreff' style='color:#2e7d32;font-weight:700'>&#10003; Enregistrement effectué</span>";
    }
?>

<!-- No-passage modal -->
<div id="cp-noPlat" class="cp-noPlat-overlay" onclick="this.classList.remove('visible')">
    <div class="cp-noPlat-box" onclick="event.stopPropagation()">
        <div class="cp-noPlat-msg">Aucune information enregistrée !</div>
        <button class="cp-noPlat-close" onclick="document.getElementById('cp-noPlat').classList.remove('visible')">Fermer</button>
    </div>
</div>

<!-- Header -->
<div class="cp-header">
    <div class="cp-header-title">&#128218; Passage CDI</div>
    <div class="cp-header-info">
        <?php include_once("./librairie_php/lib_conexpersistant.php"); connexpersistance("color:rgba(255,255,255,.8);font-size:11px;"); ?>
    </div>
</div>

<form name="formulaire" method="post" action="gestion_passagecdi.php">

<div class="cp-body">

    <!-- LEFT : badge + boutons type de passage -->
    <div class="cp-panel">
        <div class="cp-confirm-msg"><?php print $ALERTCONFIRM ?></div>

        <div class="cp-badge-row">
            <span class="cp-badge-label">Badge :</span>
            <input type="text" name="codebar" id="codebar" class="cp-badge-input" autocomplete="off">
            <button type="button" id="action" class="cp-badge-btn" onclick="activerLectureBadge()">
                &#128247; Lire [F2]
            </button>
        </div>

        <div class="cp-meals-title">Type de passage</div>
        <div class="cp-meals">
<?php
        $data = recupConfigPassage();
        for ($i = 0; $i < countTriade($data); $i++) {
            if (trim($data[$i][1]) == "") continue;
            $num = $data[$i][0];
            print "<button type='button' class='cp-meal-btn' onclick=\"ajaxCherchePassage('$num');\">".$data[$i][1]."</button>";
        }
?>
        </div>
    </div>

    <!-- RIGHT : identité + liste passages + actions -->
    <div class="cp-panel">
        <div class="cp-person-top">
            <img src="image_trombi.php?<?php print $image ?>" id="photopersonne" class="cp-person-photo" alt="">
            <div class="cp-person-details">
                <?php if ($ALERTE): ?>
                <div class="cp-alerte"><?php print $ALERTE ?></div>
                <?php endif; ?>
                <div class="cp-person-name"><?php print $nom ?></div>
                <div class="cp-person-prenom"><?php print $prenom ?></div>
                <div class="cp-person-fonction"><?php print $fonction ?></div>
            </div>
        </div>

        <div class="cp-plateau-title">Passages sélectionnés</div>
        <div class="cp-plateau-list" id="menucdi"></div>

        <div class="cp-actions">
            <button type="submit" name="reset" class="cp-btn cp-btn-cancel">&#10005; Annuler</button>
            <button type="button" class="cp-btn cp-btn-confirm <?php print $colorB ?>" onclick="valideFormulaire2()" <?php print $disabled2 ?>>&#10003; Confirmer [F8]</button>
        </div>
    </div>

</div><!-- /.cp-body -->

<input type="hidden" name="idpers" value="<?php print $ideleve ?>">
<input type="hidden" name="membre" value="<?php print $membre ?>">
<input type="hidden" name="valide" value="0">
<input type="hidden" name="platok" value="0">
<p id="somme"></p>
</form>

<!-- Historique -->
<?php if ($ideleve > 0): ?>
<div class="cp-history">
    <div class="cp-history-title">10 derniers passages</div>
    <table class="cp-hist-table">
    <thead><tr><th>Date</th><th>Passage au CDI</th></tr></thead>
    <tbody>
    <?php
    unset($data);
    $data = recupComptaCDIPers($ideleve,$membre);
    for ($i = 0; $i < countTriade($data); $i++) {
        if ($i >= 10) break;
        print "<tr><td>".dateForm($data[$i][2])."</td><td>".urldecode($data[$i][3])."</td></tr>";
    }
    ?>
    </tbody>
    </table>
</div>
<?php endif; ?>

<?php
} else {
    print "<div style='text-align:center;padding:40px;color:#c62828;font-weight:700'>Acc&egrave;s r&eacute;serv&eacute;</div>";
}
?>
</BODY></HTML>
