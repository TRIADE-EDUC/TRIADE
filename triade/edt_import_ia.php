<?php
session_start();
$md5=md5(time());
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
 *   Site                 : http://www.triade-educ.com
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
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<style>#coulBar0 { background-image: none; }</style>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/docopy.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/ajaxIA.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<script>
window.alert = function(msg) { alertify.error(msg); };

function alertjs(item) { alert(item); }

function _(el) { return document.getElementById(el); }

function uploadFile(name) {
    var file = _("Filedata").files[0];
    var formdata = new FormData();
    formdata.append("md5", name);
    formdata.append("Filedata", file);
    var ajax = new XMLHttpRequest();
    ajax.upload.addEventListener("progress", progressHandler, false);
    ajax.addEventListener("load", completeHandler, false);
    ajax.addEventListener("error", errorHandler, false);
    ajax.addEventListener("abort", abortHandler, false);
    ajax.open("POST", "IAupload.php");
    ajax.send(formdata);
}

function progressHandler(event) {
    _("loaded_n_total").innerHTML = "Téléchargement " + event.loaded + " octets sur " + event.total;
    var percent = (event.loaded / event.total) * 100;
    _("progressBar").value = Math.round(percent);
    _("status").innerHTML = Math.round(percent) + "% — veuillez patienter…";
}

function completeHandler(event) {
    _("progressBar").value = 0;
    _("loaded_n_total").innerHTML = "";
    _("status").innerHTML = "Image transmise — analyse par l'IA en cours…";
    document.getElementById('imggo').value = 'ok';
    document.getElementById('question2').disabled = false;
    document.getElementById('question2').classList.remove('btn-secondary');
    document.getElementById('question2').classList.add('btn-primary');
}

function errorHandler(event) {
    _("status").innerHTML = "Erreur lors du téléchargement.";
}

function abortHandler(event) {
    _("status").innerHTML = "Téléchargement abandonné.";
}
</script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
if ($_SESSION["membre"] == "menupersonnel") {
    if ((!verifDroit($_SESSION["id_pers"],"edt")) && (!verifDroit($_SESSION["id_pers"],"AESH"))) {
        accesNonReserveFen();
        exit;
    }
}else{
    validerequete("2");
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<?php
if(file_exists("./common/config-ia.php")) {
    include_once("./common/productId.php");
    include_once("./common/config-ia.php");
    $productID=PRODUCTID;
    $iakey=IAKEY;
    $lienIA="ajaxCopilotEdt(document.getElementById('question').value,'$productID','$iakey','afficheretour',document.getElementById('imggo').value,document.getElementById('img').value,document.getElementById('date_debut').value)";
}else{
    $lienIA="alert('Votre Triade n\'est pas configur&eacute; pour utiliser l\'IA. Contacter votre administrateur Triade')";
}

$taille="2Mo";
$maxsize="2000000";
include_once('common/config6.inc.php');
if (MAXUPLOAD == "oui") { $taille="8Mo"; $maxsize="8000000"; }
?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Importer une image pour analyse de l'EDT</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<div style="display:flex;flex-direction:column;gap:16px;padding:12px 8px">

<!-- ── Intro ── -->
<div class="alert alert-info">
  Vous pouvez importer votre emploi du temps dans Triade. Triade configurera votre EDT en fonction des informations extraites de l'image. Ce module nécessite TRIADE-COPILOT.
</div>

<!-- ── Upload image ── -->
<div class="card">
  <div class="card-header">
    <span class="card-title">1. Transmettre l'image</span>
  </div>
  <div class="card-body">
    <form id="upload_form" enctype="multipart/form-data" method="post">
      <div class="form-row">
        <span class="form-label">Image JPEG</span>
        <div>
          <input type="file" name="Filedata" id="Filedata"
            onchange="uploadFile('<?php print $md5 ?>')"
            accept="image/jpeg"
            class="form-control">
          <div style="font-size:11px;color:#888;margin-top:4px">Format JPG — taille max : <?php print $taille ?></div>
        </div>
      </div>
    </form>
    <div style="margin-top:10px">
      <progress id="progressBar" value="0" max="100" style="width:100%;height:8px;border-radius:4px;"></progress>
      <div id="status" style="font-size:12px;color:#3949ab;font-weight:600;margin-top:6px;min-height:18px"></div>
      <div id="loaded_n_total" style="font-size:11px;color:#888;margin-top:2px"></div>
    </div>
  </div>
</div>

<!-- ── Résultat analyse IA ── -->
<div id='afficheretour'></div>
<div id='afficheToken'></div>

<!-- Champs masqués lus par JS -->
<input type='hidden' name='question' id='question'
  value="Analyse cet emploi du temps et fais un retour au format JSON, sans ajouter de remarque, avec une clef : debut, duree, jour_de_semaine, date_du_jour, matiere, couleur, remplace h par ':' et min par ':' pour avoir un format comme celui-ci 'heure:minute:seconde' , indique les couleurs en code couleur HTML. Sachant que la date du jour est le ">
<input type='hidden' name='imggo' id='imggo' value="ko">
<input type='hidden' name='img'   id='img'   value="<?php print $md5 ?>">

<!-- ── Configuration et lancement ── -->
<div class="card">
  <div class="card-header">
    <span class="card-title">2. Configurer et lancer l'import</span>
  </div>
  <div class="card-body">
    <form method='post' action='edt_import_ia_2.php' name='formul' id='formul'>
      <div class="form-row">
        <span class="form-label"><?php print ucfirst(strtolower(LANGPER25)) ?></span>
        <select name="saisie_classe" class="form-control">
          <option style='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
          <?php select_classe(); ?>
        </select>
      </div>
      <div class="form-row">
        <span class="form-label">À partir du</span>
        <div style="display:flex;align-items:center;gap:6px">
          <input type="text" id="date_debut" name="date_debut" value="" size="12" readonly
            class="form-control" style="max-width:120px">
          <?php
          include_once("librairie_php/calendar.php");
          calendarDim('id3','document.formul.date_debut',$_SESSION["langue"],"0","0");
          ?>
        </div>
      </div>
      <div class="form-row">
        <span class="form-label">Jusqu'au</span>
        <div style="display:flex;align-items:center;gap:6px">
          <input type="text" name="jusquau" value="" size="12" readonly
            class="form-control" style="max-width:120px">
          <?php calendarDim('id3','document.formul.jusquau',$_SESSION["langue"],"0","0"); ?>
        </div>
      </div>
      <input type='hidden' name='edt' id='edt'>
    </form>
    <div class="form-row" style="border-bottom:none;padding-top:8px">
      <span class="form-label" style="font-size:11px;color:#888">Disponible après l'upload</span>
      <input type='button' id='question2' disabled
        class='btn btn-secondary'
        value='Valider l&apos;import'
        onClick="document.getElementById('question').value+=document.getElementById('date_debut').value;<?php print $lienIA ?>">
      <script language="JavaScript">buttonMagicRetour("edt.php","_self")</script>
    </div>
  </div>
</div>

</div>

<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php PgClose(); ?>
</BODY></HTML>
