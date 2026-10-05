<?php
session_start();
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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_bascule_select.js"></script>
<script language="JavaScript" src="./librairie_js/lib_ordre_liste.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
<script language="javaScript">
var nbElems = 0;
function calcul(op) {
  nbElems = eval(nbElems + op);
  if (nbElems < 0) { nbElems = 0; }
  document.formulaire.saisie_nb_recherche.value = nbElems;
}
function prepEnvoi() {
  var tab = [];
  var data = window.document.formulaire.saisie_recherche.options;
  for (var i = 0; i < data.length; i++) { tab.push(data[i].value); }
  document.formulaire.saisie_recherche_final.value = tab.join(",");
}
</script>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete2($_SESSION["adminplus"]);

$purge_done = false;
if (isset($_POST["create"])) {
    $cnx = cnx();
    error($cnx);
    purgerepertoirerecherche();
    $liste = explode(",", $_POST["saisie_recherche_final"]);
    foreach ($liste as $value) {
        if ($value == "eleves")               { DirPurge("./data/image_eleve"); purge_element_eleve(); history_cmd($_SESSION["nom"],"PURGE","eleves"); }
        if ($value == "notes")                { vide_notes(); vide_notes_scolaire(); history_cmd($_SESSION["nom"],"PURGE","notes"); }
        if ($value == "notesscolaire")        { vide_notes_scolaire(); history_cmd($_SESSION["nom"],"PURGE","notes vie scolaire"); }
        if ($value == "groupe")               { purge_groupe(); validGroup(); history_cmd($_SESSION["nom"],"PURGE","groupe"); }
        if ($value == "discipline")           { vide_discipline_retenue(); vide_discipline_sanction(); purge_discipline_prof(); history_cmd($_SESSION["nom"],"PURGE","dscipline"); }
        if ($value == "abs")                  { vide_absences(); history_cmd($_SESSION["nom"],"PURGE","abs"); }
        if ($value == "retard")               { vide_retards(); history_cmd($_SESSION["nom"],"PURGE","retard"); }
        if ($value == "present")              { purge_present(); history_cmd($_SESSION["nom"],"PURGE","présent"); }
        if ($value == "dispenses")            { vide_dispenses(); history_cmd($_SESSION["nom"],"PURGE","dispenses"); }
        if ($value == "news")                 { purge_news(); history_cmd($_SESSION["nom"],"PURGE","news"); }
        if ($value == "profpsupp")            { purge_delete_profp(); vide_message_prof_p(); purgeprofPsupp(); history_cmd($_SESSION["nom"],"PURGE","profpsupp"); }
        if ($value == "messprofP")            { vide_message_prof_p(); history_cmd($_SESSION["nom"],"PURGE","messages profP"); }
        if ($value == "devoirscolaire")       { vide_devoir_scolaire(); history_cmd($_SESSION["nom"],"PURGE","devoir scolaire"); }
        if ($value == "deleguesupp")          { purge_delete_delegue(); history_cmd($_SESSION["nom"],"PURGE","delegue"); }
        if ($value == "dst")                  { purge_dst(); history_cmd($_SESSION["nom"],"PURGE","dst"); }
        if ($value == "purgevenement")        { purge_evenement(); history_cmd($_SESSION["nom"],"PURGE","evenement"); }
        if ($value == "purgaffectation")      { purge_affectation(); history_cmd($_SESSION["nom"],"PURGE","affectation"); }
        if ($value == "hist_periode")         { purge_history_periode(); history_cmd($_SESSION["nom"],"PURGE","hist. periode"); }
        if ($value == "hist_bulletin")        { purge_bulletin(); history_cmd($_SESSION["nom"],"PURGE","hist. bulletin"); }
        if ($value == "purgcirculaire")       { purge_circulaire(); DirPurge("./data/circulaire"); history_cmd($_SESSION["nom"],"PURGE","circulaire"); }
        if ($value == "parametude")           { purgeParamEtude(); purgeEleveEtude(); history_cmd($_SESSION["nom"],"PURGE","para. etude"); }
        if ($value == "trimestre")            { purge_trimestre(); history_cmd($_SESSION["nom"],"PURGE","trimestre"); }
        if ($value == "forum")                { DirPurgeForum(); history_cmd($_SESSION["nom"],"PURGE","forum"); }
        if ($value == "livreor")              { DirPurgeLivreor(); history_cmd($_SESSION["nom"],"PURGE","livre d or"); }
        if ($value == "reservation")          { purgeresa(); history_cmd($_SESSION["nom"],"PURGE","reservation"); }
        if ($value == "equipement")           { purgeequip(); purgeresa(); history_cmd($_SESSION["nom"],"PURGE","equipement"); }
        if ($value == "purgimport")           { purgeimport(); history_cmd($_SESSION["nom"],"PURGE","import"); }
        if ($value == "purgcertificat")       { DirPurge("./data/pdf_certif"); history_cmd($_SESSION["nom"],"PURGE","certificat"); }
        if ($value == "stockage")             { purge_rep_membre("./data/stockage"); history_cmd($_SESSION["nom"],"PURGE","stockage"); }
        if ($value == "direction")            { purgepersonnel("ADM"); history_cmd($_SESSION["nom"],"PURGE","personnel direction"); }
        if ($value == "enseignant")           { purgepersonnel("ENS"); purge_prof_com_bull(); purgeEntretienEnseignentPourEtudiant; history_cmd($_SESSION["nom"],"PURGE","personnel enseignant"); }
        if ($value == "vie scolaire")         { purgepersonnel("MVS"); history_cmd($_SESSION["nom"],"PURGE","personnel vie scolaire"); }
        if ($value == "agenda")               { purgeagenda(); history_cmd($_SESSION["nom"],"PURGE","agenda"); }
        if ($value == "entretien")            { purgeEntretien(); history_cmd($_SESSION["nom"],"PURGE","Les entretiens"); }
        if ($value == "com_bulletin")         { purgeComBulletin(); history_cmd($_SESSION["nom"],"PURGE","Commentaire Bulletin"); }
        if ($value == "photodeFrance")        { purge_photographe_de_france(); history_cmd($_SESSION["nom"],"PURGE","Photographe de France"); }
        if ($value == "brevetcollege")        { purge_brevetCollege(); history_cmd($_SESSION["nom"],"PURGE"," Brevet collège"); }
        if ($value == "elevesansclasse")      { vide_eleves_sans_classe(); history_cmd($_SESSION["nom"],"PURGE"," Elève sans classe"); }
        if ($value == "inforesponsableeleve") { purge_responsable_info_eleve(); history_cmd($_SESSION["nom"],"PURGE"," Info responsable Elève"); }
        if ($value == "edt")                  { purge_edt(); history_cmd($_SESSION["nom"],"PURGE"," Info EDT"); }
        if ($value == "comptabilite")         { purgeComptabilite(); history_cmd($_SESSION["nom"],"PURGE"," Info Comptabilité"); }
        if ($value == "grpmail")              { purgegrpmail(); history_cmd($_SESSION["nom"],"PURGE"," Groupe mail"); }
        if ($value == "contrerendustage")     { purgecontrerendustage(); history_cmd($_SESSION["nom"],"PURGE"," Contre rendu stage"); }
        if ($value == "cantine")              { purgecantine(); history_cmd($_SESSION["nom"],"PURGE"," Gestionnaire de cantine"); }
        if ($value == "abssconet")            { purge_abs_sconet(); history_cmd($_SESSION["nom"],"PURGE"," Absences sconet"); }
        if ($value == "entretienduree")       { purgeEntretienEnseignentPourEtudiant(); history_cmd($_SESSION["nom"],"PURGE","Temps d'accompagnement"); }
        if ($value == "datestage")            { purgeDateStage(); history_cmd($_SESSION["nom"],"PURGE","Date de stage"); }
        if ($value == "affectationstage")     { purgeAffectationStage(); history_cmd($_SESSION["nom"],"PURGE","Affectation élève / stage"); }
        if ($value == "entreprises")          { purgeEntreprise(); history_cmd($_SESSION["nom"],"PURGE","Entreprises"); }
    }
    Pgclose();
    $purge_done = true;
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'><?php print LANGPURG1 ?></font></b>
</td></tr>
<tr id='cadreCentral0'>
<td>
<!-- // debut -->

<div style="display:flex;flex-direction:column;gap:12px;padding:10px 6px">

<?php if ($purge_done): ?>

  <div class="card" style="border-top:3px solid #2e7d32">
    <div class="card-body" style="display:flex;flex-direction:column;gap:14px;align-items:flex-start">
      <div style="display:flex;align-items:center;gap:10px">
        <i class="bi bi-check-circle-fill" style="color:#2e7d32;font-size:22px;flex-shrink:0"></i>
        <p style="margin:0;font-size:13px;color:#2e7d32;font-weight:600"><?php print LANGPUR5 ?></p>
      </div>
      <script language=JavaScript>buttonMagicRetour("purge.php","_self")</script>
    </div>
  </div>

<?php else: ?>

  <div style="background:#fff3e0;border:1px solid #ffb74d;border-radius:6px;padding:10px 14px;display:flex;align-items:flex-start;gap:10px">
    <i class="bi bi-exclamation-triangle-fill" style="color:#e65100;font-size:15px;flex-shrink:0;margin-top:1px"></i>
    <div style="font-size:12px;color:#e65100;font-weight:600;line-height:1.6"><?php print LANGPUR7 ?></div>
  </div>

  <div class="card">
    <div class="card-body" style="padding:10px 12px">

      <form method="post" name="formulaire">
        <div style="display:grid;grid-template-columns:1fr 36px 1fr;gap:8px;align-items:center">

          <div style="display:flex;flex-direction:column;gap:4px">
            <label style="font-size:11px;font-weight:600;color:#080A66"><?php print LANGPUR8 ?></label>
            <select size="20" name="saisie_depart"
                    style="width:100%;border:1px solid #c5cae9;border-radius:4px;font-size:11px;padding:2px;background:#fff">
              <?php include("./librairie_php/lib_purge_liste.php") ?>
            </select>
          </div>

          <div style="display:flex;flex-direction:column;gap:8px;align-items:center">
            <button type="button"
                    onclick="calcul('+1');Deplacer(this.form.saisie_depart,this.form.saisie_recherche,'<?php print LANGBASE39 ?>')"
                    style="width:32px;height:32px;border:1px solid #c5cae9;border-radius:4px;background:#eef0f8;color:#080A66;cursor:pointer;font-size:13px;display:flex;align-items:center;justify-content:center;padding:0"
                    title="Ajouter">
              <i class="bi bi-chevron-right"></i>
            </button>
            <button type="button"
                    onclick="calcul('-1');Deplacer(this.form.saisie_recherche,this.form.saisie_depart,'<?php print LANGBASE39 ?>')"
                    style="width:32px;height:32px;border:1px solid #c5cae9;border-radius:4px;background:#eef0f8;color:#080A66;cursor:pointer;font-size:13px;display:flex;align-items:center;justify-content:center;padding:0"
                    title="Retirer">
              <i class="bi bi-chevron-left"></i>
            </button>
          </div>

          <div style="display:flex;flex-direction:column;gap:4px">
            <label style="font-size:11px;font-weight:600;color:#080A66"><?php print LANGPUR9 ?></label>
            <select size="20" name="saisie_recherche" multiple="multiple"
                    style="width:100%;border:1px solid #c5cae9;border-radius:4px;font-size:11px;padding:2px;background:#fff">
              <option>-------------</option>
            </select>
            <script language="javascript">
              document.formulaire.saisie_recherche.options.length = 0;
            </script>
          </div>

        </div>

        <div style="margin-top:12px;display:flex;gap:8px;align-items:center;flex-wrap:wrap">
          <script language=JavaScript>buttonMagicSubmit3('<?php print LANGBTS ?>','create',"onclick='prepEnvoi()'")</script>
          <script language=JavaScript>buttonMagicRetour("purge.php","_self")</script>
        </div>

        <input type="hidden" name="saisie_nb_recherche">
        <input type="hidden" name="saisie_recherche_final">
      </form>

    </div>
  </div>

  <p style="font-size:10px;color:#888;margin:0;font-style:italic"><?php print LANGPUR6 ?></p>

<?php endif; ?>

</div>

<!-- // fin -->
</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
