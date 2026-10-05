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

function validerFormulaire() {
    var fichier = document.formul.Filedata.value;
    var date_debut = document.formul.date_debut.value;
    var jusquau = document.formul.jusquau.value;
    if (fichier == '') {
        alert('Veuillez sélectionner un fichier CSV à importer.');
        return false;
    }
    var ext = fichier.split('.').pop().toLowerCase();
    if (ext !== 'csv') {
        alert('Le fichier doit être au format CSV (.csv).');
        return false;
    }
    if (date_debut == '') {
        alert('Veuillez saisir une date de début.');
        return false;
    }
    if (jusquau == '') {
        alert('Veuillez saisir une date de fin.');
        return false;
    }
    return true;
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
$taille="2Mo";
$maxsize="2000000";
include_once('common/config6.inc.php');
if (MAXUPLOAD == "oui") { $taille="8Mo"; $maxsize="8000000"; }

$erreur='';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'fichier':  $erreur="Erreur lors de l'envoi du fichier. Veuillez réessayer."; break;
        case 'format':   $erreur="Le fichier envoyé n'est pas un fichier CSV valide."; break;
        case 'taille':   $erreur="Le fichier est trop volumineux (max : $taille)."; break;
        default:         $erreur="Une erreur est survenue lors de l'envoi du fichier."; break;
    }
}
if ($erreur) {
    print "<script>document.addEventListener('DOMContentLoaded',function(){ alertify.error('".addslashes($erreur)."'); });</script>";
}
?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Importer un fichier CSV à votre EDT</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<div style="display:flex;flex-direction:column;gap:16px;padding:12px 8px">

<!-- ── Import CSV ── -->
<div class="card">
  <div class="card-header">
    <span class="card-title">1. Transmettre le fichier</span>
  </div>
  <div class="card-body">
    <form enctype="multipart/form-data" method="post" action='edt_import_cvs_2.php' id='formul' name='formul' onsubmit='return validerFormulaire();'>
      <div class="form-row">
        <span class="form-label">Fichier CSV</span>
        <div>
          <input type="file" name="Filedata" accept=".csv" class="form-control">
          <div style="font-size:11px;color:#888;margin-top:4px">Format CSV, séparateur <b>;</b> — taille max : <?php print $taille ?></div>
        </div>
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
      <div class="form-row" style="border-bottom:none;padding-top:8px;gap:8px">
        <input type="submit" value="Valider l'import" class="btn btn-primary">
        <script language="JavaScript">buttonMagicRetour("edt.php","_self")</script>
      </div>
    </form>
  </div>
</div>

<!-- ── Format CSV attendu ── -->
<div class="card">
  <div class="card-header">
    <span class="card-title">Format du fichier CSV attendu</span>
  </div>
  <div class="card-body">
    <p style="font-size:12px;color:#333;margin-bottom:12px">Le fichier CSV doit utiliser le séparateur <b>point-virgule ( ; )</b> et respecter l'ordre de colonnes suivant :</p>

    <table class="table table-hover" style="font-size:12px">
      <thead>
        <tr>
          <th style="width:30px;text-align:center">#</th>
          <th>Colonne</th>
          <th>Format attendu</th>
          <th>Exemple</th>
          <th style="text-align:center">Obligatoire</th>
        </tr>
      </thead>
      <tbody>
        <tr><td style="text-align:center">1</td><td><b>Jour</b></td><td>Nom du jour en français</td><td><code>Lundi</code>, <code>Mardi</code>…</td><td style="text-align:center"><span class="badge badge-danger">Oui</span></td></tr>
        <tr><td style="text-align:center">2</td><td><b>Horaire</b></td><td>HHhMM-HHhMM</td><td><code>08h00-09h00</code></td><td style="text-align:center"><span class="badge badge-danger">Oui</span></td></tr>
        <tr><td style="text-align:center">3</td><td><b>Classe</b></td><td>Libellé exact dans Triade</td><td><code>1ERE A</code></td><td style="text-align:center"><span class="badge badge-danger">Oui</span></td></tr>
        <tr><td style="text-align:center">4</td><td><b>Matière</b></td><td>Libellé exact dans Triade</td><td><code>MATHEMATIQUES</code></td><td style="text-align:center"><span class="badge badge-danger">Oui</span></td></tr>
        <tr><td style="text-align:center">5</td><td><b>Enseignant</b></td><td>NOM Prénom (tel qu'enregistré)</td><td><code>DUPONT Marie</code></td><td style="text-align:center"><span class="badge badge-success">Non</span></td></tr>
        <tr><td style="text-align:center">6</td><td><b>Salle</b></td><td>Libellé de la salle dans Triade</td><td><code>Salle 101</code></td><td style="text-align:center"><span class="badge badge-success">Non</span></td></tr>
        <tr><td style="text-align:center">7</td><td><b>Groupe</b></td><td>Laisser vide si classe entière</td><td><code>Groupe 1</code></td><td style="text-align:center"><span class="badge badge-success">Non</span></td></tr>
        <tr><td style="text-align:center">8</td><td><b>Regroupement</b></td><td>Oui / Non</td><td><code>Non</code></td><td style="text-align:center"><span class="badge badge-success">Non</span></td></tr>
        <tr><td style="text-align:center">9</td><td><b>Effectif</b></td><td>Nombre d'élèves</td><td><code>30</code></td><td style="text-align:center"><span class="badge badge-success">Non</span></td></tr>
        <tr><td style="text-align:center">10</td><td><b>Mode</b></td><td>Présentiel / Distanciel</td><td><code>Presentiel</code></td><td style="text-align:center"><span class="badge badge-success">Non</span></td></tr>
        <tr><td style="text-align:center">11</td><td><b>Fréquence</b></td><td><code>Hebdomadaire</code> ou <code>Bihebdomadaire</code></td><td><code>Hebdomadaire</code></td><td style="text-align:center"><span class="badge badge-success">Non</span></td></tr>
        <tr><td style="text-align:center">12</td><td><b>Aire</b></td><td>Bâtiment ou zone</td><td><code>Batiment A</code></td><td style="text-align:center"><span class="badge badge-success">Non</span></td></tr>
      </tbody>
    </table>

    <div style="margin-top:14px;font-size:12px;color:#333">
      <p style="font-weight:700;margin-bottom:6px">Remarques :</p>
      <ul style="margin:0;padding-left:18px;line-height:1.8">
        <li>La première ligne peut être une ligne d'en-tête, elle sera automatiquement ignorée.</li>
        <li>Les libellés de <b>classe</b> et <b>matière</b> doivent correspondre exactement à ceux enregistrés dans Triade (casse incluse).</li>
        <li>En fréquence <b>Bihebdomadaire</b>, une séance sur deux sera importée en démarrant à la première occurrence de la date de début.</li>
        <li>Un cours annulé ou sans classe connue sera signalé dans le rapport d'import.</li>
      </ul>
    </div>

    <div style="margin-top:14px">
      <p style="font-size:12px;font-weight:700;color:#333;margin-bottom:6px">Exemple de ligne CSV :</p>
      <pre style="background:#f0f2fa;padding:10px;border-radius:6px;font-size:11px;color:#333;overflow-x:auto;border:1px solid #dde0f0">Lundi;08h00-09h00;1ERE A;FRANCAIS;DUPONT Marie;Salle 101;;Non;30;Presentiel;Hebdomadaire;Batiment A</pre>
    </div>
  </div>
</div>

</div>

<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php PgClose(); ?>
</BODY></HTML>
