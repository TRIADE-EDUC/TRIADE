<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -
 *   Site                 : http://www.triade-educ.org
 *
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
error_reporting(0);
include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade_admin.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4-2.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="../librairie_js/info-bulle.js"></script>
<title>Triade — Configuration modules</title>
<style>
.acc-panel{border:1px solid #c5cae9;border-radius:8px;margin-bottom:8px;}
.acc-header{background:#080A66;color:#fff;padding:10px 16px;cursor:pointer;display:flex;align-items:center;gap:6px;font-size:13px;font-weight:700;font-family:Electrolize,Trebuchet MS,Arial;user-select:none;}
.acc-header > span:first-child{flex:1;}
.acc-header:hover{background:#152a8a;}
.acc-arrow{transition:transform .25s;font-size:10px;}
.acc-open .acc-arrow{transform:rotate(180deg);}
.acc-body{display:none;}
.acc-open .acc-body{display:block;}
.conf-inner{width:100%;border-collapse:collapse;}
.conf-inner tr{border-bottom:1px solid #e8eaf6;}
.conf-inner tr:last-child{border-bottom:none;}
.conf-inner tr{background:#fff;transition:background .15s;}
.conf-inner td{background:transparent !important;}
.conf-inner tr:hover{background:#eef0fb !important;}
.conf-inner td[align="right"]{width:54%;font-size:12px;font-weight:700;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial;padding:6px 10px 6px 4px;text-align:right;vertical-align:middle;}
.conf-inner td[align="left"]{padding:6px 10px;vertical-align:middle;font-family:Electrolize,Trebuchet MS,Arial;font-size:12px;}
.acc-alert{background:#c62828;color:#fff;border-radius:50%;width:17px;height:17px;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:900;margin-left:2px;flex-shrink:0;cursor:default;vertical-align:middle;}
.acc-inline-alert{background:#c62828;color:#fff;border-radius:50%;width:14px;height:14px;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:900;margin-left:6px;cursor:default;vertical-align:middle;}
</style>
</head>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%"  height="85" bgcolor="#0B3A0C">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Droit d'accès aux modules Triade</font></b></td></tr>
<tr id="cadreCentral0" ><td > <p align="left" ><font color="#000000">
<!-- // debut de la saisie -->
<form method='post' action='config_module2.php'>

<!-- ===== Barre de recherche ===== -->
<div style="background:#f0f2fa;border:1px solid #c5cae9;border-radius:8px;padding:10px 16px;margin-bottom:14px;display:flex;align-items:center;gap:10px;">
  <i class="bi bi-search" style="color:#080A66;font-size:14px;flex-shrink:0;"></i>
  <input type="text" id="conf-search" placeholder="Rechercher un module de configuration…"
    autocomplete="off"
    style="flex:1;border:1px solid #c5cae9;border-radius:6px;padding:6px 12px;font-size:13px;font-family:Electrolize,Trebuchet MS,Arial;color:#333;outline:none;background:#fff;">
  <span id="conf-search-count" style="font-size:12px;color:#888;white-space:nowrap;display:none;"></span>
  <button type="button" id="conf-search-clear" onclick="document.getElementById('conf-search').value='';confSearch();"
    style="display:none;background:none;border:none;cursor:pointer;color:#c62828;font-size:16px;line-height:1;padding:0 2px;">&#x2715;</button>
</div>
<script>
var _confInitOpen = null;
function confSearch() {
  var panels = document.querySelectorAll('.acc-panel');
  if (!_confInitOpen) {
    _confInitOpen = Array.from(panels).map(function(p) { return p.classList.contains('acc-open'); });
  }
  var q = document.getElementById('conf-search').value.trim().toLowerCase();
  var total = 0;
  document.getElementById('conf-search-clear').style.display = q ? 'block' : 'none';
  panels.forEach(function(panel, i) {
    var rows = panel.querySelectorAll('.na-row, .conf-inner tr');
    var hits = 0;
    rows.forEach(function(row) {
      if (!q) {
        row.style.display = '';
      } else {
        var txt = (row.innerText || row.textContent || '').toLowerCase();
        if (txt.indexOf(q) !== -1) { row.style.display = ''; hits++; total++; }
        else { row.style.display = 'none'; }
      }
    });
    if (!q) {
      panel.style.display = '';
      if (_confInitOpen[i]) { panel.classList.add('acc-open'); }
      else { panel.classList.remove('acc-open'); }
    } else if (hits > 0) {
      panel.style.display = ''; panel.classList.add('acc-open');
    } else {
      panel.style.display = 'none';
    }
  });
  var countEl = document.getElementById('conf-search-count');
  if (q) {
    countEl.style.display = 'inline';
    countEl.textContent = total + ' résultat' + (total > 1 ? 's' : '');
  } else {
    countEl.style.display = 'none';
  }
}
document.getElementById('conf-search').addEventListener('input', confSearch);
document.getElementById('conf-search').addEventListener('keydown', function(e) {
  if (e.key === 'Escape') { this.value = ''; confSearch(); }
});
</script>

<!-- ===== Section 1 : Configuration Générale ===== -->
<div class="acc-panel">
<div class="acc-header" onclick="accToggle(this.parentElement)"><span>Configuration Générale</span><span class="acc-arrow">&#9660;</span></div>
<div class="acc-body"><table class="conf-inner" width="100%">

<?php
include_once("../common/config-module.php");
$preinsciptionoui="";
$preinsciptionnon="";
$dispenseoui="";
$dispensenon="";
$disciplineoui="";
$disciplinenon="";
$cahierdetexteoui="";
$cahierdetextenon="";
$planclasseoui="";
$planclassenon="";
$DSToui="";
$DSTnon="";
$visudevoirprofoui="";
$visudevoirprofnon="";
$suppdevoirprofoui="";
$suppdevoirprofnon="";
$cahiertexteprofoui="";
$cahiertexteprofnon="";
$sanctionprofoui="";
$sanctionprofnon="";
$ficheeleveprofoui="";
$ficheeleveprofnon="";
$listeleveprofoui="";
$listeleveprofnon="";
$planprofoui="";
$planprofnon="";
$stageproprofoui="";
$stageproprofnon="";
$DSTProfoui="";
$DSTProfnon="";
$DOKEOSeleveoui="";
$DOKEOSelevenon="";
$DOKEOSProfoui="";
$DOKEOSProfnon="";
$parentdispenseoui="";
$parentdispensenon="";
$parentdisciplineoui="";
$parentdisciplinenon="";
$parentcahierdetexteoui="";
$parentcahierdetextenon="";
$parentplanclasseoui="";
$parentplanclassenon="";
$parentDSToui="";
$parentDSTnon="";
$parentTrombinoscopeoui="";
$parentTrombinoscopenon="";
$stageProparentoui="";
$stageProparentnon="";
$stageProeleveoui="";
$stageProelevenon="";
$stockageprofoui="";
$stockageprofnon="";
$intramsnprofoui="";
$intramsnprofnon="";
$agendaprofoui="";
$agendaprofnon="";
$fluxrssprofoui="";
$fluxrssprofnon="";
$notesprofoui="";
$notesprofnon="";
$bulletinprofoui="";
$bulletinprofnon="";
$resaprofoui="";
$resaprofnon="";
$visudevoirprofoui="";
$visudevoirprofnon="";
$informationprofoui="";
$informationprofnon="";
$calendrierprofoui="";
$calendrierprofnon="";
$stockageviescolaireoui="";
$stockageviescolairenon="";
$intramsnviescolaireoui="";
$intramsnviescolairenon="";
$agendaviescolaireoui="";
$agendaviescolairenon="";
$fluxrssviescolaireoui="";
$fluxrssviescolairenon="";
$etudeviescolaireoui="";
$etudeviescolairenon="";
$circulaireviescolaireoui="";
$circulaireviescolairenon="";
$dstviescolairenon="";
$dstviescolaireoui="";
$stageviescolaireoui="";
$stageviescolairenon="";
$visaviescolaireoui="";
$visaviescolairenon="";
$noteviescolaireoui="";
$noteviescolairenon="";
$imptableauviescolaireoui="";
$imptableauviescolairenon="";
$bulletinviescolaireoui="";
$bulletinviescolairenon="";
$periodeviescolaireoui="";
$periodeviescolairenon="";
$videoprojoviescolaireoui="";
$videoprojoviescolairenon="";
$planclasseviescolaireoui="";
$planclasseviescolairenon="";
$vacationviescolaireoui="";
$vacationviescolairenon="";
$historyviescolaireoui="";
$historyviescolairenon="";
$resaviescolaireoui="";
$resaviescolairenon="";
$exportviescolaireoui="";
$exportviescolairenon="";
$noteenseignantviascolaireoui="";
$noteenseignantviascolairenon="";

$cantineviascolaireoui="";
$cantineviascolairenon="";
$cantineprofoui="";
$cantineprofnon="";


$stockageadminoui="";
$stockageadminnon="";
$intramsnadminoui="";
$intramsnadminnon="";
$agendaadminoui="";
$agendaadminnon="";
$fluxrssadminoui="";
$fluxrssadminnon="";
$cantineadminoui="";
$cantineadminnon="";
$vacationadminoui="";
$vacationadminnon="";
$droitscolariteadminoui="";
$droitscolariteadminnon="";
$historyadminoui="";
$historyadminnon="";
$resaadminoui="";
$resaadminnon="";
$noteenseignantadminoui="";
$noteenseignantadminnon=""; 
$parentabsenceoui="";
$parentabsencenon="";
$parentretardoui="";
$parentretardnon="";

$modulechambresvateladminoui="";
$modulechambresvateladminnon="";
$chambreviescolaireoui="";
$chambreviescolairenon="";
$modulefinanciervateladminoui="";
$modulefinanciervateladminnon="";

$tuteurnoteoui="";
$tuteurnotenon="";
$tuteurdisciplineoui=""; 
$tuteurdisciplinenon=""; 
$tuteurabsoui=""; 
$tuteurabsnon=""; 
$tuteurdispenseoui=""; 
$tuteurdispensenon=""; 
$tuteurEDToui=""; 
$tuteurEDTnon=""; 
$tuteurcahierdetexteoui=""; 
$tuteurcahierdetextenon="";
$tuteurcirculaireoui="";
$tuteurcirculairenon=""; 
$tuteurcalendrieroui=""; 
$tuteurcalendriernon="";
$emargementprofoui="";
$emargementprofnon="";


$agendaparentoui="";
$stockageparentoui="";
$intramsnparentoui="";
$comptaparentoui="";
$rssparentoui="";
$cantineparentoui="";
$agendaparentnon="";
$stockageparentnon="";
$intramsnparentnon="";
$comptaparentnon="";
$rssparentnon="";
$cantineparentnon="";

$agendaeleveoui="";
$stockageeleveoui="";
$intramsneleveoui="";
$comptaeleveoui="";
$rsseleveoui="";
$cantineeleveoui="";
$agendaelevenon="";
$stockageelevenon="";
$intramsnelevenon="";
$comptaelevenon="";
$rsselevenon="";
$cantineelevenon="";

$newspageviescolaireoui="";
$newspageviescolairenon="";
$moduleboursieradminoui="";
$moduleboursieradminnon="";

$messagerieadminoui="";
$messagerieadminnon="";
$messagerieviescolaireoui="";
$messagerieviescolairenon="";
$messagerieProfoui="";
$messagerieProfnon="";
$messagerieEleveoui="";
$messagerieElevenon="";
$messagerieParentoui="";
$messagerieParentnon="";
$messagerietuteuroui="";
$messagerietuteurnon="";
$preinscriptionviescolaireoui="";
$preinscriptionviescolairenon="";



$newsviescolaireoui="";
$newsviescolairenon="";
$planningeleveoui="";
$planningelevenon="";
$planningparentoui="";
$planningparentnon="";
$planningprofoui="";
$planningprofnon="";

$moduleadmincdinon="";
$moduleadmincdioui="";
$moduleadminnotanetnon="";
$moduleadminnotanetoui="";
$moduleadmingestionsmsnon="";
$moduleadmingestionsmsoui="";
$moduleadminreservequipnon="";
$moduleadminreservequipoui="";
$moduleadminreservsallenon="";
$moduleadminreservsalleoui="";
$moduleadminfourniturenon="";
$moduleadminfournitureoui="";
$moduleadminexambrevetnon="";
$moduleadminexambrevetoui="";
$moduleadmingestionetudenon="";
$moduleadmingestionetudeoui="";
$moduleadmingestiondisciplinenon="";
$moduleadmingestiondisciplineoui="";
$moduleadminretenudjnon="";
$moduleadminretenudjoui="";
$moduleadminsanctiondujournon="";
$moduleadminsanctiondujouroui="";
$moduleadmingestiondispensenon="";
$moduleadmingestiondispenseoui="";
$moduleadmindosmedicalnon="";
$moduleadmindosmedicaloui="";
$moduleadminplanclassenon="";
$moduleadminplanclasseoui="";
$moduleadmingestiondeleguenon="";
$moduleadmingestiondelegueoui="";
$moduleadminsousmatierenon="";
$moduleadminsousmatiereoui="";
$moduleadminsuppleantnon="";
$moduleadminsuppleantoui="";
$moduleprofPoui="";
$moduleprofPnon="";
$moduleconfignoteusaoui="";
$moduleconfignoteusanon="";
$moduleentretienindividueloui="";
$moduleentretienindividuelnon="";
$modulecarnetsuivioui="";
$modulecarnetsuivinon="";
$moduleverifbulletinoui="";
$moduleverifbulletinnon="";
$modulenoteviescolaireoui="";
$modulenoteviescolairenon="";
$moduleadminimprperiodeoui="";
$moduleadminimprperiodenon="";
$moduleadminabsrtdoui="";
$moduleadminabsrtdnon="";
$moduleadminnouvelleanneeoui="";
$moduleadminnouvelleanneenon="";
$moduleadminarchivageoui="";
$moduleadminarchivagenon="";
$moduleadmindeleguesoui="";
$moduleadmindeleguesnon="";
$moduleadminnewsdefilantoui="";
$moduleadminnewsdefilantnon="";
$moduleadminpurgerinfooui="";
$moduleadminpurgerinfonon="";

$moduleadmingestionsavoiretreoui="";
$moduleadmingestionsavoiretrenon="";
$moduleprofgestionsavoiretreoui="";
$moduleprofgestionsavoiretrenon="";
$moduleparentgestionsavoiretreoui="";
$moduleparentgestionsavoiretrenon="";
$moduleelevegestionsavoiretreoui="";
$moduleelevegestionsavoiretrenon="";
$modulebulletinvisuparentoui="";
$modulebulletinvisuparentnon="";
$modulebulletinvisueleveoui="";
$modulebulletinvisuelevenon="";

$moduleviescolairesavoiretreoui="";
$moduleviescolairesavoiretrenon="";
$moduletuteursavoiretreoui="";
$moduletuteursavoiretrenon="";

$cahierdetexteviescolaireoui="";
$cahierdetexteviescolairenon="";
$modulebulletinvisututeurstageoui="";
$modulebulletinvisututeurstagenon="";
$moduleadminevalensoui="";
$moduleadminevalensnon="";

$moduletriadecoachoui="";
$moduletriadecoachnon="";

$modulepacteadminoui="";
$modulepacteadminnon="";
$modulepacteprofoui="";
$modulepacteprofnon="";


if (defined('VIESCOLAIRECAHIERDETEXTE') && VIESCOLAIRECAHIERDETEXTE == "oui") { $cahierdetexteviescolaireoui="checked"; }
if (defined('VIESCOLAIRECAHIERDETEXTE') && VIESCOLAIRECAHIERDETEXTE == "non") { $cahierdetexteviescolairenon="checked"; }
if (defined('PREINSCRIPTION') && PREINSCRIPTION == "oui") { $preinsciptionoui="checked"; }
if (defined('PREINSCRIPTION') && PREINSCRIPTION == "non") { $preinsciptionnon="checked"; }
if (defined('DISPENSE') && DISPENSE == "oui") { $dispenseoui="checked"; }
if (defined('DISPENSE') && DISPENSE == "non") { $dispensenon="checked"; }
if (defined('DISCIPLINE') && DISCIPLINE == "oui") { $disciplineoui="checked"; }
if (defined('DISCIPLINE') && DISCIPLINE == "non") { $disciplinenon="checked"; }
if (defined('CAHIERDETEXTE') && CAHIERDETEXTE == "oui") { $cahierdetexteoui="checked"; }
if (defined('CAHIERDETEXTE') && CAHIERDETEXTE == "non") { $cahierdetextenon="checked"; }
if (defined('PLANCLASSE') && PLANCLASSE == "oui") { $planclasseoui="checked"; }
if (defined('PLANCLASSE') && PLANCLASSE == "non") { $planclassenon="checked"; }
if (defined('DST') && DST == "oui") { $DSToui="checked"; }
if (defined('DST') && DST == "non") { $DSTnon="checked"; }
if (defined('PARENTDISPENSE') && PARENTDISPENSE == "oui") { $parentdispenseoui="checked"; }
if (defined('PARENTDISPENSE') && PARENTDISPENSE == "non") { $parentdispensenon="checked"; }
if (defined('PARENTDISCIPLINE') && PARENTDISCIPLINE == "oui") { $parentdisciplineoui="checked"; }
if (defined('PARENTDISCIPLINE') && PARENTDISCIPLINE == "non") { $parentdisciplinenon="checked"; }
if (defined('PARENTCAHIERDETEXTE') && PARENTCAHIERDETEXTE == "oui") { $parentcahierdetexteoui="checked"; }
if (defined('PARENTCAHIERDETEXTE') && PARENTCAHIERDETEXTE == "non") { $parentcahierdetextenon="checked"; }
if (defined('PARENTPLANCLASSE') && PARENTPLANCLASSE == "oui") { $parentplanclasseoui="checked"; }
if (defined('PARENTPLANCLASSE') && PARENTPLANCLASSE == "non") { $parentplanclassenon="checked"; }
if (defined('PARENTDST') && PARENTDST == "oui") { $parentDSToui="checked"; }
if (defined('PARENTDST') && PARENTDST == "non") { $parentDSTnon="checked"; }
if (defined('VISUDEVOIRPROF') && VISUDEVOIRPROF == "oui") { $visudevoirprofoui="checked"; }
if (defined('VISUDEVOIRPROF') && VISUDEVOIRPROF == "non") { $visudevoirprofnon="checked"; }
if (defined('SUPPDEVOIRPROF') && SUPPDEVOIRPROF == "oui") { $suppdevoirprofoui="checked"; }
if (defined('SUPPDEVOIRPROF') && SUPPDEVOIRPROF == "non") { $suppdevoirprofnon="checked"; }
if (defined('CAHIERTEXTPROF') && CAHIERTEXTPROF == "oui") { $cahiertexteprofoui="checked"; }
if (defined('CAHIERTEXTPROF') && CAHIERTEXTPROF == "non") { $cahiertexteprofnon="checked"; }
if (defined('SANCTIONPROF') && SANCTIONPROF == "oui") { $sanctionprofoui="checked"; }
if (defined('SANCTIONPROF') && SANCTIONPROF == "non") { $sanctionprofnon="checked"; }
if (defined('FICHEELEVEPROF') && FICHEELEVEPROF == "oui") { $ficheeleveprofoui="checked"; }
if (defined('FICHEELEVEPROF') && FICHEELEVEPROF == "non") { $ficheeleveprofnon="checked"; }
if (defined('LISTEELEVEPROF') && LISTEELEVEPROF == "oui") { $listeleveprofoui="checked"; }
if (defined('LISTEELEVEPROF') && LISTEELEVEPROF == "non") { $listeleveprofnon="checked"; }
if (defined('PLANPROF') && PLANPROF == "oui") { $planprofoui="checked"; }
if (defined('PLANPROF') && PLANPROF == "non") { $planprofnon="checked"; }
if (defined('STAGEPROPROF') && STAGEPROPROF == "oui") { $stageproprofoui="checked"; }
if (defined('STAGEPROPROF') && STAGEPROPROF == "non") { $stageproprofnon="checked"; }
if (defined('DSTPROFACCES') && DSTPROFACCES == "oui") { $DSTProfoui="checked"; }
if (defined('DSTPROFACCES') && DSTPROFACCES == "non") { $DSTProfnon="checked"; }
if (defined('DOKEOSPROF') && DOKEOSPROF == "oui") { $DOKEOSProfoui="checked"; }
if (defined('DOKEOSPROF') && DOKEOSPROF == "non") { $DOKEOSProfnon="checked"; }
if (defined('DOKEOSELEVE') && DOKEOSELEVE == "oui") { $DOKEOSeleveoui="checked"; }
if (defined('DOKEOSELEVE') && DOKEOSELEVE == "non") { $DOKEOSelevenon="checked"; }
if (defined('COMPTAPROF') && COMPTAPROF == "oui") { $comptaProfoui="checked"; }
if (defined('COMPTAPROF') && COMPTAPROF == "non") { $comptaProfnon="checked"; }
if (defined('PARENTTROMBINOSCOPE') && PARENTTROMBINOSCOPE == "oui") { $parentTrombinoscopeoui="checked"; }
if (defined('PARENTTROMBINOSCOPE') && PARENTTROMBINOSCOPE == "non") { $parentTrombinoscopenon="checked"; }
if (defined('STAGEPROELEVE') && STAGEPROELEVE == "oui") { $stageProeleveoui="checked"; }
if (defined('STAGEPROELEVE') && STAGEPROELEVE == "non") { $stageProelevenon="checked"; }
if (defined('STAGEPROPARENT') && STAGEPROPARENT == "oui") { $stageProparentoui="checked"; }
if (defined('STAGEPROPARENT') && STAGEPROPARENT == "non") { $stageProparentnon="checked"; }
if (defined('STOCKAGEPROF') && STOCKAGEPROF == "oui") { $stockageprofoui="checked"; }
if (defined('STOCKAGEPROF') && STOCKAGEPROF == "non") { $stockageprofnon="checked"; }
if (defined('INTRAMSNPROF') && INTRAMSNPROF == "oui") { $intramsnprofoui="checked"; }
if (defined('INTRAMSNPROF') && INTRAMSNPROF == "non") { $intramsnprofnon="checked"; }
if (defined('AGENDAMSNPROF') && AGENDAMSNPROF == "oui") { $agendaprofoui="checked"; }
if (defined('AGENDAMSNPROF') && AGENDAMSNPROF == "non") { $agendaprofnon="checked"; }
if (defined('FLUXRSSPROF') && FLUXRSSPROF == "oui") { $fluxrssprofoui="checked"; }
if (defined('FLUXRSSPROF') && FLUXRSSPROF == "non") { $fluxrssprofnon="checked"; }
if (defined('NOTESPROF') && NOTESPROF == "oui") { $notesprofoui="checked"; }
if (defined('NOTESPROF') && NOTESPROF == "non") { $notesprofnon="checked"; }
if (defined('BULLETINPROF') && BULLETINPROF == "oui") { $bulletinprofoui="checked"; }
if (defined('BULLETINPROF') && BULLETINPROF == "non") { $bulletinprofnon="checked"; }
if (defined('RESAPROF') && RESAPROF == "oui") { $resaprofoui="checked"; }
if (defined('RESAPROF') && RESAPROF == "non") { $resaprofnon="checked"; }
if (defined('CIRCULAIREPROF') && CIRCULAIREPROF == "oui") { $circulaireprofoui="checked"; }
if (defined('CIRCULAIREPROF') && CIRCULAIREPROF == "non") { $circulaireprofnon="checked"; }
if (defined('INFORMATIONPROF') && INFORMATIONPROF == "oui") { $informationprofoui="checked"; }
if (defined('INFORMATIONPROF') && INFORMATIONPROF == "non") { $informationprofnon="checked"; }
if (defined('CALENDRIERPROF') && CALENDRIERPROF == "oui") { $calendrierprofoui="checked"; }
if (defined('CALENDRIERPROF') && CALENDRIERPROF == "non") { $calendrierprofnon="checked"; }
if (defined('STOCKAGEVIESCOLAIRE') && STOCKAGEVIESCOLAIRE == "oui") { $stockageviescolaireoui="checked"; }
if (defined('STOCKAGEVIESCOLAIRE') && STOCKAGEVIESCOLAIRE == "non") { $stockageviescolairenon="checked"; }
if (defined('INTRAMSNVIESCOLAIRE') && INTRAMSNVIESCOLAIRE == "oui") { $intramsnviescolaireoui="checked"; }
if (defined('INTRAMSNVIESCOLAIRE') && INTRAMSNVIESCOLAIRE == "non") { $intramsnviescolairenon="checked"; }
if (defined('AGENDAVIESCOLAIRE') && AGENDAVIESCOLAIRE == "oui") { $agendaviescolaireoui="checked"; }
if (defined('AGENDAVIESCOLAIRE') && AGENDAVIESCOLAIRE == "non") { $agendaviescolairenon="checked"; }
if (defined('FLUXRSSVIESCOLAIRE') && FLUXRSSVIESCOLAIRE == "oui") { $fluxrssviescolaireoui="checked"; }
if (defined('FLUXRSSVIESCOLAIRE') && FLUXRSSVIESCOLAIRE == "non") { $fluxrssviescolairenon="checked"; }
if (defined('ETUDEVIESCOLAIRE') && ETUDEVIESCOLAIRE == "oui") { $etudeviescolaireoui="checked"; }
if (defined('ETUDEVIESCOLAIRE') && ETUDEVIESCOLAIRE == "non") { $etudeviescolairenon="checked"; }
if (defined('CIRCULAIREVIESCOLAIRE') && CIRCULAIREVIESCOLAIRE == "oui") { $circulaireviescolaireoui="checked"; }
if (defined('CIRCULAIREVIESCOLAIRE') && CIRCULAIREVIESCOLAIRE == "non") { $circulaireviescolairenon="checked"; }
if (defined('STAGEVIESCOLAIRE') && STAGEVIESCOLAIRE == "oui") { $stageviescolaireoui="checked"; }
if (defined('STAGEVIESCOLAIRE') && STAGEVIESCOLAIRE == "non") { $stageviescolairenon="checked"; }
if (defined('DSTVIESCOLAIRE') && DSTVIESCOLAIRE == "oui") { $dstviescolaireoui="checked"; }
if (defined('DSTVIESCOLAIRE') && DSTVIESCOLAIRE == "non") { $dstviescolairenon="checked"; }
if (defined('VISAVIESCOLAIRE') && VISAVIESCOLAIRE == "oui") { $visaviescolaireoui="checked"; }
if (defined('VISAVIESCOLAIRE') && VISAVIESCOLAIRE == "non") { $visaviescolairenon="checked"; }
if (defined('NOTEVIESCOLAIRE') && NOTEVIESCOLAIRE == "oui") { $noteviescolaireoui="checked"; }
if (defined('NOTEVIESCOLAIRE') && NOTEVIESCOLAIRE == "non") { $noteviescolairenon="checked"; }
if (defined('IMPTABLEAUVIESCOLAIRE') && IMPTABLEAUVIESCOLAIRE == "oui") { $imptableauviescolaireoui="checked"; }
if (defined('IMPTABLEAUVIESCOLAIRE') && IMPTABLEAUVIESCOLAIRE == "non") { $imptableauviescolairenon="checked"; }
if (defined('BULLETINVIESCOLAIRE') && BULLETINVIESCOLAIRE == "oui") { $bulletinviescolaireoui="checked"; }
if (defined('BULLETINVIESCOLAIRE') && BULLETINVIESCOLAIRE == "non") { $bulletinviescolairenon="checked"; }
if (defined('PERIODEVIESCOLAIRE') && PERIODEVIESCOLAIRE == "oui") { $periodeviescolaireoui="checked"; }
if (defined('PERIODEVIESCOLAIRE') && PERIODEVIESCOLAIRE == "non") { $periodeviescolairenon="checked"; }
if (defined('VIDEOPROJOVIESCOLAIRE') && VIDEOPROJOVIESCOLAIRE == "oui") { $videoprojoviescolaireoui="checked"; }
if (defined('VIDEOPROJOVIESCOLAIRE') && VIDEOPROJOVIESCOLAIRE == "non") { $videoprojoviescolairenon="checked"; }
if (defined('PLANCLASSEVIESCOLAIRE') && PLANCLASSEVIESCOLAIRE == "oui") { $planclasseviescolaireoui="checked"; }
if (defined('PLANCLASSEVIESCOLAIRE') && PLANCLASSEVIESCOLAIRE == "non") { $planclasseviescolairenon="checked"; }
if (defined('HISTORYVIESCOLAIRE') && HISTORYVIESCOLAIRE == "oui") { $historyviescolaireoui="checked"; }
if (defined('HISTORYVIESCOLAIRE') && HISTORYVIESCOLAIRE == "non") { $historyviescolairenon="checked"; }
if (defined('RESAVIESCOLAIRE') && RESAVIESCOLAIRE == "oui") { $resaviescolaireoui="checked"; }
if (defined('RESAVIESCOLAIRE') && RESAVIESCOLAIRE == "non") { $resaviescolairenon="checked"; }
if (defined('EXPORTVIESCOLAIRE') && EXPORTVIESCOLAIRE == "oui") { $exportviescolaireoui="checked"; }
if (defined('EXPORTVIESCOLAIRE') && EXPORTVIESCOLAIRE == "non") { $exportviescolairenon="checked"; }
if (defined('VACATIONVIESCOLAIRE') && VACATIONVIESCOLAIRE == "oui") { $vacationviescolaireoui="checked"; }
if (defined('VACATIONVIESCOLAIRE') && VACATIONVIESCOLAIRE == "non") { $vacationviescolairenon="checked"; }
if (defined('NOTEENSEIGNANTVIASCOLAIRE') && NOTEENSEIGNANTVIASCOLAIRE == "oui") { $noteenseignantviascolaireoui="checked"; }
if (defined('NOTEENSEIGNANTVIASCOLAIRE') && NOTEENSEIGNANTVIASCOLAIRE == "non") { $noteenseignantviascolairenon="checked"; }
if (defined('MODULECANTINEPROF') && MODULECANTINEPROF == "oui") { $cantineprofoui="checked"; }
if (defined('MODULECANTINEPROF') && MODULECANTINEPROF == "non") { $cantineprofnon="checked"; }
if (defined('MODULECANTINEVIESCOLAIRE') && MODULECANTINEVIESCOLAIRE == "oui") { $cantineviascolaireoui="checked"; }
if (defined('MODULECANTINEVIESCOLAIRE') && MODULECANTINEVIESCOLAIRE == "non") { $cantineviascolairenon="checked"; }
if (defined('STOCKAGEADMIN') && STOCKAGEADMIN == "oui") { $stockageadminoui="checked"; }
if (defined('STOCKAGEADMIN') && STOCKAGEADMIN == "non") { $stockageadminnon="checked"; }
if (defined('INTRAMSNADMIN') && INTRAMSNADMIN == "oui") { $intramsnadminoui="checked"; }
if (defined('INTRAMSNADMIN') && INTRAMSNADMIN == "non") { $intramsnadminnon="checked"; }
if (defined('AGENDAADMIN') && AGENDAADMIN == "oui") { $agendaadminoui="checked"; }
if (defined('AGENDAADMIN') && AGENDAADMIN == "non") { $agendaadminnon="checked"; }
if (defined('FLUXRSSADMIN') && FLUXRSSADMIN == "oui") { $fluxrssadminoui="checked"; }
if (defined('FLUXRSSADMIN') && FLUXRSSADMIN == "non") { $fluxrssadminnon="checked"; }
if (defined('RESAADMIN') && RESAADMIN == "oui") { $resaadminoui="checked"; }
if (defined('RESAADMIN') && RESAADMIN == "non") { $resaadminnon="checked"; }
if (defined('VACATIONADMIN') && VACATIONADMIN == "oui") { $vacationadminoui="checked"; }
if (defined('VACATIONADMIN') && VACATIONADMIN == "non") { $vacationadminnon="checked"; }
if (defined('HISTORYADMIN') && HISTORYADMIN == "oui") { $historyadminoui="checked"; }
if (defined('HISTORYADMIN') && HISTORYADMIN == "non") { $historyadminnon="checked"; }
if (defined('MODULECANTINEADMIN') && MODULECANTINEADMIN == "oui") { $cantineadminoui="checked"; }
if (defined('MODULECANTINEADMIN') && MODULECANTINEADMIN == "non") { $cantineadminnon="checked"; }
if (defined('DROITSCOLARITEADMIN') && DROITSCOLARITEADMIN == "oui") { $droitscolariteadminoui="checked"; }
if (defined('DROITSCOLARITEADMIN') && DROITSCOLARITEADMIN == "non") { $droitscolariteadminnon="checked"; }
if (defined('NOTEPROFVIAADMIN') && NOTEPROFVIAADMIN == "oui") { $noteenseignantadminoui="checked"; }
if (defined('NOTEPROFVIAADMIN') && NOTEPROFVIAADMIN == "non") { $noteenseignantadminnon="checked"; }
if (defined('PARENTABSENCE') && PARENTABSENCE == "oui") { $parentabsenceoui="checked"; }
if (defined('PARENTABSENCE') && PARENTABSENCE == "non") { $parentabsencenon="checked"; }
if (defined('PARENTRETARD') && PARENTRETARD == "oui") { $parentretardoui="checked"; }
if (defined('PARENTRETARD') && PARENTRETARD == "non") { $parentretardnon="checked"; }
if (defined('MODULECHAMBRESADMIN') && MODULECHAMBRESADMIN == "oui") { $modulechambresvateladminoui="checked"; }
if (defined('MODULECHAMBRESADMIN') && MODULECHAMBRESADMIN == "non") { $modulechambresvateladminnon="checked"; }
if (defined('MODULEFINANCIERADMIN') && MODULEFINANCIERADMIN == "oui") { $modulefinanciervateladminoui="checked"; }
if (defined('MODULEFINANCIERADMIN') && MODULEFINANCIERADMIN == "non") { $modulefinanciervateladminnon="checked"; }
if (defined('MODULECHAMBRESVIESCOLAIRE') && MODULECHAMBRESVIESCOLAIRE == "oui") { $chambreviescolaireoui="checked"; }
if (defined('MODULECHAMBRESVIESCOLAIRE') && MODULECHAMBRESVIESCOLAIRE == "non") { $chambreviescolairenon="checked"; }
if (defined('MODULETUTEURNOTE') && MODULETUTEURNOTE == "oui") {$tuteurnoteoui="checked"; }
if (defined('MODULETUTEURNOTE') && MODULETUTEURNOTE == "non") {$tuteurnotenon="checked"; }
if (defined('MODULETUTEURDISCIPLINE') && MODULETUTEURDISCIPLINE == "oui") {$tuteurdisciplineoui="checked"; }
if (defined('MODULETUTEURDISCIPLINE') && MODULETUTEURDISCIPLINE == "non") {$tuteurdisciplinenon="checked"; }
if (defined('MODULETUTEURABS') && MODULETUTEURABS == "oui") {$tuteurabsoui="checked"; }
if (defined('MODULETUTEURABS') && MODULETUTEURABS == "non") {$tuteurabsnon="checked"; }
if (defined('MODULETUTEURDISPENSE') && MODULETUTEURDISPENSE == "oui") {$tuteurdispenseoui="checked"; }
if (defined('MODULETUTEURDISPENSE') && MODULETUTEURDISPENSE == "non") {$tuteurdispensenon="checked"; }
if (defined('MODULETUTEUREDT') && MODULETUTEUREDT == "oui") {$tuteurEDToui="checked"; }
if (defined('MODULETUTEUREDT') && MODULETUTEUREDT == "non") {$tuteurEDTnon="checked"; }
if (defined('MODULETUTEURCAHIERDETEXTE') && MODULETUTEURCAHIERDETEXTE == "oui") {$tuteurcahierdetexteoui="checked"; }
if (defined('MODULETUTEURCAHIERDETEXTE') && MODULETUTEURCAHIERDETEXTE == "non") {$tuteurcahierdetextenon="checked"; }
if (defined('MODULETUTEURCIRCULAIRE') && MODULETUTEURCIRCULAIRE == "oui") {$tuteurcirculaireoui="checked"; }
if (defined('MODULETUTEURCIRCULAIRE') && MODULETUTEURCIRCULAIRE == "non") {$tuteurcirculairenon="checked"; }
if (defined('MODULETUTEURCALENDRIER') && MODULETUTEURCALENDRIER == "oui") {$tuteurcalendrieroui="checked"; }
if (defined('MODULETUTEURCALENDRIER') && MODULETUTEURCALENDRIER == "non") {$tuteurcalendriernon="checked"; }
if (defined('MODULEPROFEMARGEMENT') && MODULEPROFEMARGEMENT == "oui") {$emargementprofoui="checked"; }
if (defined('MODULEPROFEMARGEMENT') && MODULEPROFEMARGEMENT == "non") {$emargementprofnon="checked"; }
if (defined('MODULEPARENTAGENDA') && MODULEPARENTAGENDA == "oui") { $agendaparentoui="checked"; }
if (defined('MODULEPARENTAGENDA') && MODULEPARENTAGENDA == "non") { $agendaparentnon="checked"; }
if (defined('MODULEPARENTSTOCKAGE') && MODULEPARENTSTOCKAGE == "oui") { $stockageparentoui="checked"; }
if (defined('MODULEPARENTSTOCKAGE') && MODULEPARENTSTOCKAGE == "non") { $stockageparentnon="checked"; }
if (defined('MODULEPARENTMSN') && MODULEPARENTMSN == "oui") { $intramsnparentoui="checked"; }
if (defined('MODULEPARENTMSN') && MODULEPARENTMSN == "non") { $intramsnparentnon="checked"; }
if (defined('MODULEPARENTCOMPTA') && MODULEPARENTCOMPTA == "oui") {$comptaparentoui="checked"; }
if (defined('MODULEPARENTCOMPTA') && MODULEPARENTCOMPTA == "non") {$comptaparentnon="checked"; }
if (defined('MODULEPARENTRSS') && MODULEPARENTRSS == "oui") {$rssparentoui="checked"; }
if (defined('MODULEPARENTRSS') && MODULEPARENTRSS == "non") {$rssparentnon="checked"; }
if (defined('MODULEPARENTCANTINE') && MODULEPARENTCANTINE == "oui") {$cantineparentoui="checked"; }
if (defined('MODULEPARENTCANTINE') && MODULEPARENTCANTINE == "non") {$cantineparentnon="checked"; }
if (defined('MODULEELEVEAGENDA') && MODULEELEVEAGENDA == "oui") { $agendaeleveoui="checked"; }
if (defined('MODULEELEVEAGENDA') && MODULEELEVEAGENDA == "non") { $agendaelevenon="checked"; }
if (defined('MODULEELEVESTOCKAGE') && MODULEELEVESTOCKAGE == "oui") { $stockageeleveoui="checked"; }
if (defined('MODULEELEVESTOCKAGE') && MODULEELEVESTOCKAGE == "non") { $stockageelevenon="checked"; }
if (defined('MODULEELEVEMSN') && MODULEELEVEMSN == "oui") { $intramsneleveoui="checked"; }
if (defined('MODULEELEVEMSN') && MODULEELEVEMSN == "non") { $intramsnelevenon="checked"; }
if (defined('MODULEELEVECOMPTA') && MODULEELEVECOMPTA == "oui") {$comptaeleveoui="checked"; }
if (defined('MODULEELEVECOMPTA') && MODULEELEVECOMPTA == "non") {$comptaelevenon="checked"; }
if (defined('MODULEELEVERSS') && MODULEELEVERSS == "oui") {$rsseleveoui="checked"; }
if (defined('MODULEELEVERSS') && MODULEELEVERSS == "non") {$rsselevenon="checked"; }
if (defined('MODULEELEVECANTINE') && MODULEELEVECANTINE == "oui") {$cantineeleveoui="checked"; }
if (defined('MODULEELEVECANTINE') && MODULEELEVECANTINE == "non") {$cantineelevenon="checked"; }
if (defined('MODULENEWSPAGEVIESCOLAIRE') && MODULENEWSPAGEVIESCOLAIRE == "oui") {$newspageviescolaireoui="checked"; }
if (defined('MODULENEWSPAGEVIESCOLAIRE') && MODULENEWSPAGEVIESCOLAIRE == "non") {$newspageviescolairenon="checked"; }
if (defined('MODULENEWSVIESCOLAIRE') && MODULENEWSVIESCOLAIRE == "oui") {$newsviescolaireoui="checked"; }
if (defined('MODULENEWSVIESCOLAIRE') && MODULENEWSVIESCOLAIRE == "non") {$newsviescolairenon="checked"; }
if (defined('MODULEBOURSIERADMIN') && MODULEBOURSIERADMIN == "oui") {$moduleboursieradminoui="checked"; }
if (defined('MODULEBOURSIERADMIN') && MODULEBOURSIERADMIN == "non") {$moduleboursieradminnon="checked"; }
if (defined('MODULEMESSAGERIEADMIN') && MODULEMESSAGERIEADMIN == "oui") {$messagerieadminoui="checked"; }
if (defined('MODULEMESSAGERIEADMIN') && MODULEMESSAGERIEADMIN == "non") {$messagerieadminnon="checked"; }
if (defined('MODULEMESSAGERIESCOLAIRE') && MODULEMESSAGERIESCOLAIRE == "oui") {$messagerieviescolaireoui="checked"; }
if (defined('MODULEMESSAGERIESCOLAIRE') && MODULEMESSAGERIESCOLAIRE == "non") {$messagerieviescolairenon="checked"; }
if (defined('MODULEMESSAGERIEPROF') && MODULEMESSAGERIEPROF == "oui") {$messagerieProfoui="checked"; }
if (defined('MODULEMESSAGERIEPROF') && MODULEMESSAGERIEPROF == "non") {$messagerieProfnon="checked"; }
if (defined('MODULEMESSAGERIEELEVE') && MODULEMESSAGERIEELEVE == "oui") {$messagerieEleveoui="checked"; }
if (defined('MODULEMESSAGERIEELEVE') && MODULEMESSAGERIEELEVE == "non") {$messagerieElevenon="checked"; }
if (defined('MODULEMESSAGERIEPARENT') && MODULEMESSAGERIEPARENT == "oui") {$messagerieParentoui="checked"; }
if (defined('MODULEMESSAGERIEPARENT') && MODULEMESSAGERIEPARENT == "non") {$messagerieParentnon="checked"; }
if (defined('MODULEMESSAGERIETUTEUR') && MODULEMESSAGERIETUTEUR == "oui") {$messagerietuteuroui="checked"; }
if (defined('MODULEMESSAGERIETUTEUR') && MODULEMESSAGERIETUTEUR == "non") {$messagerietuteurnon="checked"; }
if (defined('MODULEPREINSCRIPTIONVIESCOLAIRE') && MODULEPREINSCRIPTIONVIESCOLAIRE == "oui") {$preinscriptionviescolaireoui="checked"; }
if (defined('MODULEPREINSCRIPTIONVIESCOLAIRE') && MODULEPREINSCRIPTIONVIESCOLAIRE == "non") {$preinscriptionviescolairenon="checked"; }
if (defined('MODULEPLANNINGELEVE') && MODULEPLANNINGELEVE == "oui") {$planningeleveoui="checked"; }
if (defined('MODULEPLANNINGELEVE') && MODULEPLANNINGELEVE == "non") {$planningelevenon="checked"; }
if (defined('MODULEPLANNINGPARENT') && MODULEPLANNINGPARENT == "oui") {$planningparentoui="checked"; }
if (defined('MODULEPLANNINGPARENT') && MODULEPLANNINGPARENT == "non") {$planningparentnon="checked"; }
if (defined('MODULEPLANNINGPROF') && MODULEPLANNINGPROF == "oui") {$planningprofoui="checked"; }
if (defined('MODULEPLANNINGPROF') && MODULEPLANNINGPROF == "non") {$planningprofnon="checked"; }
if (defined('RUBRIQUEBULLETIN') && RUBRIQUEBULLETIN == "oui") {$rubriquebulletinoui="checked"; }
if (defined('RUBRIQUEBULLETIN') && RUBRIQUEBULLETIN == "non") {$rubriquebulletinnon="checked"; }
if (defined('RUBRIQUEANNEXE') && RUBRIQUEANNEXE == "oui") {$rubriqueannexeoui="checked"; }
if (defined('RUBRIQUEANNEXE') && RUBRIQUEANNEXE == "non") {$rubriqueannexenon="checked"; }
if (defined('RUBRIQUEGESTION') && RUBRIQUEGESTION == "oui") {$rubriquegestionoui="checked"; }
if (defined('RUBRIQUEGESTION') && RUBRIQUEGESTION == "non") {$rubriquegestionnon="checked"; }
if (defined('RUBRIQUEAFFECTATION') && RUBRIQUEAFFECTATION == "oui") {$rubriqueaffectationoui="checked"; }
if (defined('RUBRIQUEAFFECTATION') && RUBRIQUEAFFECTATION == "non") {$rubriqueaffectationnon="checked"; }
if (defined('RUBRIQUEETABLISSEMENT') && RUBRIQUEETABLISSEMENT == "oui") {$rubriqueetablissementoui="checked"; }
if (defined('RUBRIQUEETABLISSEMENT') && RUBRIQUEETABLISSEMENT == "non") {$rubriqueetablissementnon="checked"; }
if (defined('RUBRIQUEVIESCOLAIRE') && RUBRIQUEVIESCOLAIRE == "oui") {$rubriqueviescolaireoui="checked"; }
if (defined('RUBRIQUEVIESCOLAIRE') && RUBRIQUEVIESCOLAIRE == "non") {$rubriqueviescolairenon="checked"; }
if (defined('RUBRIQUEETUDIANT') && RUBRIQUEETUDIANT == "oui") {$rubriqueetudiantoui="checked"; }
if (defined('RUBRIQUEETUDIANT') && RUBRIQUEETUDIANT == "non") {$rubriqueetudiantnon="checked"; }
if (defined('RUBRIQUEACTUALITE') && RUBRIQUEACTUALITE == "oui") {$rubriqueactualiteoui="checked"; }
if (defined('RUBRIQUEACTUALITE') && RUBRIQUEACTUALITE == "non") {$rubriqueactualitenon="checked"; }
// -------------------------------------------------------------------------------
if (defined('MODULEADMINCDI') && MODULEADMINCDI == "oui") {$moduleadmincdioui="checked"; }
if (defined('MODULEADMINCDI') && MODULEADMINCDI == "non") {$moduleadmincdinon="checked"; }
if (defined('MODULEADMINNOTANET') && MODULEADMINNOTANET == "oui") {$moduleadminnotanetoui="checked"; }
if (defined('MODULEADMINNOTANET') && MODULEADMINNOTANET == "non") {$moduleadminnotanetnon="checked"; }
if (defined('MODULEADMINGESTIONSMS') && MODULEADMINGESTIONSMS == "oui") {$moduleadmingestionsmsoui="checked"; }
if (defined('MODULEADMINGESTIONSMS') && MODULEADMINGESTIONSMS == "non") {$moduleadmingestionsmsnon="checked"; }
if (defined('MODULEADMINRESERVEQUIP') && MODULEADMINRESERVEQUIP == "oui") {$moduleadminreservequipoui="checked"; }
if (defined('MODULEADMINRESERVEQUIP') && MODULEADMINRESERVEQUIP == "non") {$moduleadminreservequipnon="checked"; }
if (defined('MODULEADMINRESERVSALLE') && MODULEADMINRESERVSALLE == "oui") {$moduleadminreservsalleoui="checked"; }
if (defined('MODULEADMINRESERVSALLE') && MODULEADMINRESERVSALLE == "non") {$moduleadminreservsallenon="checked"; }
if (defined('MODULEADMINFOURNITURE') && MODULEADMINFOURNITURE == "oui") {$moduleadminfournitureoui="checked"; }
if (defined('MODULEADMINFOURNITURE') && MODULEADMINFOURNITURE == "non") {$moduleadminfourniturenon="checked"; }
if (defined('MODULEADMINEXAMBREVET') && MODULEADMINEXAMBREVET == "oui") {$moduleadminexambrevetoui="checked"; }
if (defined('MODULEADMINEXAMBREVET') && MODULEADMINEXAMBREVET == "non") {$moduleadminexambrevetnon="checked"; }
if (defined('MODULEADMINGESTIONETUDE') && MODULEADMINGESTIONETUDE == "oui") {$moduleadmingestionetudeoui="checked"; }
if (defined('MODULEADMINGESTIONETUDE') && MODULEADMINGESTIONETUDE == "non") {$moduleadmingestionetudenon="checked"; }
if (defined('MODULEADMINGESTIONDISCIPLINE') && MODULEADMINGESTIONDISCIPLINE == "oui") {$moduleadmingestiondisciplineoui="checked"; }
if (defined('MODULEADMINGESTIONDISCIPLINE') && MODULEADMINGESTIONDISCIPLINE == "non") {$moduleadmingestiondisciplinenon="checked"; }
if (defined('MODULEADMINRETENUDJ') && MODULEADMINRETENUDJ == "oui") {$moduleadminretenudjoui="checked"; }
if (defined('MODULEADMINRETENUDJ') && MODULEADMINRETENUDJ == "non") {$moduleadminretenudjnon="checked"; }
if (defined('MODULEADMINSANCTIONDUJOUR') && MODULEADMINSANCTIONDUJOUR == "oui") {$moduleadminsanctiondujouroui="checked"; }
if (defined('MODULEADMINSANCTIONDUJOUR') && MODULEADMINSANCTIONDUJOUR == "non") {$moduleadminsanctiondujournon="checked"; }
if (defined('MODULEADMINGESTIONDISPENSE') && MODULEADMINGESTIONDISPENSE == "oui") {$moduleadmingestiondispenseoui="checked"; }
if (defined('MODULEADMINGESTIONDISPENSE') && MODULEADMINGESTIONDISPENSE == "non") {$moduleadmingestiondispensenon="checked"; }
if (defined('MODULEADMINDOSMEDICAL') && MODULEADMINDOSMEDICAL == "oui") {$moduleadmindosmedicaloui="checked"; }
if (defined('MODULEADMINDOSMEDICAL') && MODULEADMINDOSMEDICAL == "non") {$moduleadmindosmedicalnon="checked"; }
if (defined('MODULEADMINPLANCLASSE') && MODULEADMINPLANCLASSE == "oui") {$moduleadminplanclasseoui="checked"; }
if (defined('MODULEADMINPLANCLASSE') && MODULEADMINPLANCLASSE == "non") {$moduleadminplanclassenon="checked"; }
if (defined('MODULEADMINGESTIONDELEGUE') && MODULEADMINGESTIONDELEGUE == "oui") {$moduleadmingestiondelegueoui="checked"; }
if (defined('MODULEADMINGESTIONDELEGUE') && MODULEADMINGESTIONDELEGUE == "non") {$moduleadmingestiondeleguenon="checked"; }
if (defined('MODULEADMINSOUSMATIERE') && MODULEADMINSOUSMATIERE == "oui") {$moduleadminsousmatiereoui="checked"; }
if (defined('MODULEADMINSOUSMATIERE') && MODULEADMINSOUSMATIERE == "non") {$moduleadminsousmatierenon="checked"; }
if (defined('MODULEADMINPROFP') && MODULEADMINPROFP == "oui") {$moduleprofPoui="checked"; }
if (defined('MODULEADMINPROFP') && MODULEADMINPROFP == "non") {$moduleprofPnon="checked"; }
if (defined('MODULEADMINCONFIGNOTEUSA') && MODULEADMINCONFIGNOTEUSA == "oui") {$moduleconfignoteusaoui="checked"; }
if (defined('MODULEADMINCONFIGNOTEUSA') && MODULEADMINCONFIGNOTEUSA == "non") {$moduleconfignoteusanon="checked"; }
if (defined('MODULEADMINENTRETIENINDIVIDUEL') && MODULEADMINENTRETIENINDIVIDUEL == "oui") {$moduleentretienindividueloui="checked"; }
if (defined('MODULEADMINENTRETIENINDIVIDUEL') && MODULEADMINENTRETIENINDIVIDUEL == "non") {$moduleentretienindividuelnon="checked"; }
if (defined('MODULEADMINCARNETSUIVI') && MODULEADMINCARNETSUIVI == "oui") {$modulecarnetsuivioui="checked"; }
if (defined('MODULEADMINCARNETSUIVI') && MODULEADMINCARNETSUIVI == "non") {$modulecarnetsuivinon="checked"; }
if (defined('MODULEADMINVERIFBULLETIN') && MODULEADMINVERIFBULLETIN == "oui") {$moduleverifbulletinoui="checked"; }
if (defined('MODULEADMINVERIFBULLETIN') && MODULEADMINVERIFBULLETIN == "non") {$moduleverifbulletinnon="checked"; }
if (defined('MODULEADMINNOTEVIESCOLAIRE') && MODULEADMINNOTEVIESCOLAIRE == "oui") {$modulenoteviescolaireoui="checked"; }
if (defined('MODULEADMINNOTEVIESCOLAIRE') && MODULEADMINNOTEVIESCOLAIRE == "non") {$modulenoteviescolairenon="checked"; }
if (defined('MODULEADMINIMPRPERIODE') && MODULEADMINIMPRPERIODE == "oui") {$moduleadminimprperiodeoui="checked"; }
if (defined('MODULEADMINIMPRPERIODE') && MODULEADMINIMPRPERIODE == "non") {$moduleadminimprperiodenon="checked"; }
if (defined('MODULEADMINSUPPLEANT') && MODULEADMINSUPPLEANT == "oui") {$moduleadminsuppleantoui="checked"; }
if (defined('MODULEADMINSUPPLEANT') && MODULEADMINSUPPLEANT == "non") {$moduleadminsuppleantnon="checked"; }
if (defined('MODULEADMINABSRTD') && MODULEADMINABSRTD == "oui") {$moduleadminabsrtdoui="checked"; }
if (defined('MODULEADMINABSRTD') && MODULEADMINABSRTD == "non") {$moduleadminabsrtdnon="checked"; }
if (defined('MODULEADMINPREINSCRIPTION') && MODULEADMINPREINSCRIPTION == "oui") {$moduleadminpreinscriptionoui="checked"; }
if (defined('MODULEADMINPREINSCRIPTION') && MODULEADMINPREINSCRIPTION == "non") {$moduleadminpreinscriptionnon="checked"; }
if (defined('MODULEADMINNOUVELLEANNEE') && MODULEADMINNOUVELLEANNEE == "oui") {$moduleadminnouvelleanneeoui="checked"; }
if (defined('MODULEADMINNOUVELLEANNEE') && MODULEADMINNOUVELLEANNEE == "non") {$moduleadminnouvelleanneenon="checked"; }
if (defined('MODULEADMINARCHIVAGE') && MODULEADMINARCHIVAGE == "oui") {$moduleadminarchivageoui="checked"; }
if (defined('MODULEADMINARCHIVAGE') && MODULEADMINARCHIVAGE == "non") {$moduleadminarchivagenon="checked"; }
if (defined('MODULEADMINNEWSDEFILANT') && MODULEADMINNEWSDEFILANT == "oui") {$moduleadminnewsdefilantoui="checked"; }
if (defined('MODULEADMINNEWSDEFILANT') && MODULEADMINNEWSDEFILANT == "non") {$moduleadminnewsdefilantnon="checked"; }
if (defined('MODULEADMINPURGERINFO') && MODULEADMINPURGERINFO == "oui") {$moduleadminpurgerinfooui="checked"; }
if (defined('MODULEADMINPURGERINFO') && MODULEADMINPURGERINFO == "non") {$moduleadminpurgerinfonon="checked"; }
$CDIEleveoui="";
$CDIElevenon="";
if (defined('MODULEELEVECDI') && MODULEELEVECDI == "oui") {$CDIEleveoui="checked"; }
if (defined('MODULEELEVECDI') && MODULEELEVECDI == "non") {$CDIElevenon="checked"; }
if (defined('MODULEADMINGESTIONSAVOIRETRE') && MODULEADMINGESTIONSAVOIRETRE == "oui") {$moduleadmingestionsavoiretreoui="checked"; }
if (defined('MODULEADMINGESTIONSAVOIRETRE') && MODULEADMINGESTIONSAVOIRETRE == "non") {$moduleadmingestionsavoiretrenon="checked"; }
if (defined('MODULEPROFGESTIONSAVOIRETRE') && MODULEPROFGESTIONSAVOIRETRE == "oui") {$moduleprofgestionsavoiretreoui="checked"; }
if (defined('MODULEPROFGESTIONSAVOIRETRE') && MODULEPROFGESTIONSAVOIRETRE == "non") {$moduleprofgestionsavoiretrenon="checked"; }
if (defined('MODULEELEVEGESTIONSAVOIRETRE') && MODULEELEVEGESTIONSAVOIRETRE == "non") {$moduleelevegestionsavoiretrenon="checked"; }
if (defined('MODULEELEVEGESTIONSAVOIRETRE') && MODULEELEVEGESTIONSAVOIRETRE == "oui") {$moduleelevegestionsavoiretreoui="checked"; }
if (defined('MODULEPARENTGESTIONSAVOIRETRE') && MODULEPARENTGESTIONSAVOIRETRE == "oui") {$moduleparentgestionsavoiretreoui="checked"; }
if (defined('MODULEPARENTGESTIONSAVOIRETRE') && MODULEPARENTGESTIONSAVOIRETRE == "non") {$moduleparentgestionsavoiretrenon="checked"; }
if (defined('MODULETUTEURGESTIONSAVOIRETRE') && MODULETUTEURGESTIONSAVOIRETRE == "oui") {$moduletuteursavoiretreoui="checked"; }
if (defined('MODULETUTEURGESTIONSAVOIRETRE') && MODULETUTEURGESTIONSAVOIRETRE == "non") {$moduletuteursavoiretrenon="checked"; }
if (defined('MODULEVIESCOLAIREGESTIONSAVOIRETRE') && MODULEVIESCOLAIREGESTIONSAVOIRETRE == "oui") {$moduleviescolairesavoiretreoui="checked"; }
if (defined('MODULEVIESCOLAIREGESTIONSAVOIRETRE') && MODULEVIESCOLAIREGESTIONSAVOIRETRE == "non") {$moduleviescolairesavoiretrenon="checked"; }
if (defined('MODULEBULLETINVISUELEVE') && MODULEBULLETINVISUELEVE == "oui") {$modulebulletinvisueleveoui="checked"; }
if (defined('MODULEBULLETINVISUELEVE') && MODULEBULLETINVISUELEVE == "non") {$modulebulletinvisuelevenon="checked"; }
if (defined('MODULEBULLETINVISUPARENT') && MODULEBULLETINVISUPARENT == "oui") {$modulebulletinvisuparentoui="checked"; }
if (defined('MODULEBULLETINVISUPARENT') && MODULEBULLETINVISUPARENT == "non") {$modulebulletinvisuparentnon="checked"; }
if (defined('MODULEBULLETINVISUTUTEUR') && MODULEBULLETINVISUTUTEUR == "oui") {$modulebulletinvisututeurstageoui="checked"; }
if (defined('MODULEBULLETINVISUTUTEUR') && MODULEBULLETINVISUTUTEUR == "non") {$modulebulletinvisututeurstagenon="checked"; }
if (defined('MODULEADMINEVALENS') && MODULEADMINEVALENS == "oui") {$moduleadminevalensoui="checked"; }
if (defined('MODULEADMINEVALENS') && MODULEADMINEVALENS == "non") {$moduleadminevalensnon="checked"; }
$radiooui="";
$radionon="";
if (defined('MODULERADIO') && MODULERADIO == "oui") {$radiooui="checked"; }
if (defined('MODULERADIO') && MODULERADIO == "non") {$radionon="checked"; }
$modulefourniturescolaireoui="";
$modulefourniturescolairenon="";
if (defined('MODULEFOURNITURESCOLAIRE') && MODULEFOURNITURESCOLAIRE == "oui") {$modulefourniturescolaireoui="checked"; }
if (defined('MODULEFOURNITURESCOLAIRE') && MODULEFOURNITURESCOLAIRE == "non") {$modulefourniturescolairenon="checked"; }
$moduledelegueparentoui="";
$moduledelegueparentnon="";
if (defined('MODULEDELEGUEPARENT') && MODULEDELEGUEPARENT == "oui") {$moduledelegueparentoui="checked"; }
if (defined('MODULEDELEGUEPARENT') && MODULEDELEGUEPARENT == "non") {$moduledelegueparentnon="checked"; }
$moduleparentlocalisationoui="";
$moduleparentlocalisationnon="";
if (defined('MODULEPARENTLOCALISATION') && MODULEPARENTLOCALISATION == "oui") {$moduleparentlocalisationoui="checked"; }
if (defined('MODULEPARENTLOCALISATION') && MODULEPARENTLOCALISATION == "non") {$moduleparentlocalisationnon="checked"; }
if (defined('MODULETRIADECOACH') && MODULETRIADECOACH == "oui") {$moduletriadecoachoui="checked"; }
if (defined('MODULETRIADECOACH') && MODULETRIADECOACH == "non") {$moduletriadecoachnon="checked"; }
if (defined('MODULEPACTEADMIN') && MODULEPACTEADMIN == "oui") {$modulepacteadminoui="checked"; }
if (defined('MODULEPACTEADMIN') && MODULEPACTEADMIN == "non") {$modulepacteadminnon="checked"; }
if (defined('MODULEPACTEPROF')  && MODULEPACTEPROF  == "oui") {$modulepacteprofoui="checked"; }
if (defined('MODULEPACTEPROF')  && MODULEPACTEPROF  == "non") {$modulepacteprofnon="checked"; }

?>

<tr><td align=right >Module Pré-inscription : </td>
<td align=left><input type=radio <?php print $preinsciptionoui ?> name=preinsciption value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $preinsciptionnon ?> name=preinsciption value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Radio : </td>
<td align=left><input type=radio <?php print $radiooui ?> name=radio value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $radionon ?> name=radio value="non" class=btradio1  > non </td>
</tr>

<!--
<tr><td colspan=2 ><br><br><br>&nbsp;<img src="../image/commun/ico_conf.gif" align="center" > <font class="T2"><b>Configuration Parent</b></font></td></tr>
-->

</table></div></div>
<!-- ===== Section 2 : Configuration Élève ===== -->
<div class="acc-panel">
<div class="acc-header" onclick="accToggle(this.parentElement)"><span>Configuration Élève</span><span class="acc-arrow">&#9660;</span></div>
<div class="acc-body"><table class="conf-inner" width="100%">

<tr><td align=right >Module Agenda : </td>
<td align=left><input type=radio <?php print $agendaeleveoui ?> name="agendaeleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $agendaelevenon ?> name="agendaeleve" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Stockage : </td>
<td align=left><input type=radio <?php print $stockageeleveoui ?> name="stockageeleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $stockageelevenon ?> name="stockageeleve" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Intra-MSN : </td>
<td align=left><input type=radio <?php print $intramsneleveoui ?> name="intramsneleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $intramsnelevenon ?> name="intramsneleve" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Comptabilité : </td>
<td align=left><input type=radio <?php print $comptaeleveoui ?> name="comptaeleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $comptaelevenon ?> name="comptaeleve" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Flux RSS : </td>
<td align=left><input type=radio <?php print $rsseleveoui ?> name="rsseleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $rsselevenon ?> name="rsseleve" value="non" class=btradio1  > non </td>
</tr>
</tr>
<tr><td align=right >Module Cantine : </td>
<td align=left><input type=radio <?php print $cantineeleveoui ?> name="cantineeleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $cantineelevenon ?> name="cantineeleve" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Planning : </td>
<td align=left><input type=radio <?php print $planningeleveoui ?> name="planningeleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $planningelevenon ?> name="planningeleve" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Dispense : </td>
<td align=left><input type=radio <?php print $dispenseoui ?> name="dispense" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $dispensenon ?> name="dispense" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Discipline : </td>
<td align=left><input type=radio <?php print $disciplineoui ?> name="discipline" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $disciplinenon ?> name="discipline" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Cahier de texte : </td>
<td align=left><input type=radio <?php print $cahierdetexteoui ?> name=cahierdetexte value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $cahierdetextenon ?> name=cahierdetexte value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Plan de classe : </td>
<td align=left><input type=radio <?php print $planclasseoui ?> name=planclasse value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $planclassenon ?> name=planclasse value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module D.S.T. : </td>
<td align=left><input type=radio <?php print $DSToui ?> name=DST value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $DSTnon ?> name=DST value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module E-Learning : </td>
<td align=left><input type=radio <?php print $DOKEOSeleveoui ?> name="DOKEOSEleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $DOKEOSelevenon ?> name="DOKEOSEleve" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Stage Pro. : </td>
<td align=left><input type=radio <?php print $stageProeleveoui ?> name="stageProeleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $stageProelevenon ?> name="stageProeleve" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Messagerie : </td>
<td align=left><input type=radio <?php print $messagerieEleveoui ?> name="messagerieEleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $messagerieElevenon ?> name="messagerieEleve" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module CDI : </td>
<td align=left><input type=radio <?php print $CDIEleveoui ?> name="CDIEleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $CDIElevenon ?> name="CDIEleve" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Savoir-être : </td>
<td align=left><input type=radio <?php print $moduleelevegestionsavoiretreoui ?> name="moduleelevegestionsavoiretre" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleelevegestionsavoiretrenon ?> name="moduleelevegestionsavoiretre" value="non" class=btradio1  > non </td>
</tr>

</tr>
<tr><td align=right >Module Visualisation du bulletin : </td>
<td align=left><input type=radio <?php print $modulebulletinvisueleveoui ?> name="modulebulletinvisueleve" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $modulebulletinvisuelevenon ?> name="modulebulletinvisueleve" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Triade-coach (IA) : </td>
<td align=left><input type=radio <?php print $moduletriadecoachoui ?> name="moduletriadecoach" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduletriadecoachnon ?> name="moduletriadecoach" value="non" class=btradio1  > non </td>
</tr>
</table></div></div>
<!-- ===== Section 3 : Configuration Parent ===== -->
<div class="acc-panel">
<div class="acc-header" onclick="accToggle(this.parentElement)"><span>Configuration Parent</span><span class="acc-arrow">&#9660;</span></div>
<div class="acc-body"><table class="conf-inner" width="100%">



<tr><td align=right >Module Agenda : </td>
<td align=left><input type=radio <?php print $agendaparentoui ?> name="agendaparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $agendaparentnon ?> name="agendaparent" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Stockage : </td>
<td align=left><input type=radio <?php print $stockageparentoui ?> name="stockageparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $stockageparentnon ?> name="stockageparent" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Intra-MSN : </td>
<td align=left><input type=radio <?php print $intramsnparentoui ?> name="intramsnparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $intramsnparentnon ?> name="intramsnparent" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Comptabilité : </td>
<td align=left><input type=radio <?php print $comptaparentoui ?> name="comptaparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $comptaparentnon ?> name="comptaparent" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Flux RSS : </td>
<td align=left><input type=radio <?php print $rssparentoui ?> name="rssparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $rssparentnon ?> name="rssparent" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Planning : </td>
<td align=left><input type=radio <?php print $planningparentoui ?> name="planningparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $planningparentnon ?> name="planningparent" value="non" class=btradio1  > non </td>
</tr>

</tr>
<tr><td align=right >Module Cantine : </td>
<td align=left><input type=radio <?php print $cantineparentoui ?> name="cantineparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $cantineparentnon ?> name="cantineparent" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Dispense : </td>
<td align=left><input type=radio <?php print $parentdispenseoui ?> name="parentdispense" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentdispensenon ?> name="parentdispense" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Absence : </td>
<td align=left><input type=radio <?php print $parentabsenceoui ?> name="parentabsence" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentabsencenon ?> name="parentabsence" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Retard : </td>
<td align=left><input type=radio <?php print $parentretardoui ?> name="parentretard" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentretardnon ?> name="parentretard" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Discipline : </td>
<td align=left><input type=radio <?php print $parentdisciplineoui ?> name="parentdiscipline" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentdisciplinenon ?> name="parentdiscipline" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Cahier de texte : </td>
<td align=left><input type=radio <?php print $parentcahierdetexteoui ?> name=parentcahierdetexte value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentcahierdetextenon ?> name=parentcahierdetexte value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Plan de classe : </td>
<td align=left><input type=radio <?php print $parentplanclasseoui ?> name=parentplanclasse value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentplanclassenon ?> name=parentplanclasse value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Trombinoscopes : </td>
<td align=left><input type=radio <?php print $parentTrombinoscopeoui ?> name='parentTrombinoscope' value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentTrombinoscopenon ?> name='parentTrombinoscope' value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module D.S.T. : </td>
<td align=left><input type=radio <?php print $parentDSToui ?> name=parentDST value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $parentDSTnon ?> name=parentDST value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Stage Pro. : </td>
<td align=left><input type=radio <?php print $stageProparentoui ?> name="stageProparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $stageProparentnon ?> name="stageProparent" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Messagerie : </td>
<td align=left><input type=radio <?php print $messagerieParentoui ?> name="messagerieParent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $messagerieParentnon ?> name="messagerieParent" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Savoir-être : </td>
<td align=left><input type=radio <?php print $moduleparentgestionsavoiretreoui ?> name="moduleparentgestionsavoiretre" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleparentgestionsavoiretrenon ?> name="moduleparentgestionsavoiretre" value="non" class=btradio1  > non </td>
</tr>

</tr>

<tr><td align=right >Module Visualisation du bulletin : </td>
<td align=left><input type=radio <?php print $modulebulletinvisuparentoui ?> name="modulebulletinvisuparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $modulebulletinvisuparentnon ?> name="modulebulletinvisuparent" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module fourniture scolaire : </td>
<td align=left><input type=radio <?php print $modulefourniturescolaireoui ?> name="modulefourniturescolaire" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	       <input type=radio <?php print $modulefourniturescolairenon ?> name="modulefourniturescolaire" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module d&eacute;l&eacute;gu&eacute; : </td>
<td align=left><input type=radio <?php print $moduledelegueparentoui ?> name="moduledelegueparent" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	       <input type=radio <?php print $moduledelegueparentnon ?> name="moduledelegueparent" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Localisation : </td>
<td align=left><input type=radio <?php print $moduleparentlocalisationoui ?> name="moduleparentlocalisation" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
	       <input type=radio <?php print $moduleparentlocalisationnon ?> name="moduleparentlocalisation" value="non" class=btradio1  > non </td>
</tr>

</table></div></div>
<!-- ===== Section 4 : Configuration Tuteur de stage ===== -->
<div class="acc-panel">
<div class="acc-header" onclick="accToggle(this.parentElement)"><span>Configuration Tuteur de stage</span><span class="acc-arrow">&#9660;</span></div>
<div class="acc-body"><table class="conf-inner" width="100%">

<tr><td align=right >Module Note : </td>
<td align=left><input type=radio <?php print $tuteurnoteoui ?> name="tuteurnote" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurnotenon ?> name="tuteurnote" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Discipline : </td>
<td align=left><input type=radio <?php print $tuteurdisciplineoui ?> name="tuteurdiscipline" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurdisciplinenon ?> name="tuteurdiscipline" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Absence : </td>
<td align=left><input type=radio <?php print $tuteurabsoui ?> name=tuteurabs value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurabsnon ?> name=tuteurabs value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Dispense : </td>
<td align=left><input type=radio <?php print $tuteurdispenseoui ?> name=tuteurdispense value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurdispensenon ?> name=tuteurdispense value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Emploi du temps : </td>
<td align=left><input type=radio <?php print $tuteurEDToui ?> name=tuteurEDT value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurEDTnon ?> name=tuteurEDT value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Cahier de textes : </td>
<td align=left><input type=radio <?php print $tuteurcahierdetexteoui ?> name="tuteurcahierdetexte" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurcahierdetextenon ?> name="tuteurcahierdetexte" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Circulaires : </td>
<td align=left><input type=radio <?php print $tuteurcirculaireoui ?> name="tuteurcirculaire" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurcirculairenon ?> name="tuteurcirculaire" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Calendrier : </td>
<td align=left><input type=radio <?php print $tuteurcalendrieroui ?> name="tuteurcalendrier" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $tuteurcalendriernon ?> name="tuteurcalendrier" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Messagerie : </td>
<td align=left><input type=radio <?php print $messagerietuteuroui ?> name="messagerietuteur" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $messagerietuteurnon ?> name="messagerietuteur" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Savoir-être : </td>
<td align=left><input type=radio <?php print $moduletuteursavoiretreoui ?> name="moduletuteursavoiretre" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduletuteursavoiretrenon ?> name="moduletuteursavoiretre" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Visualisation du bulletin : </td>
<td align=left><input type=radio <?php print $modulebulletinvisututeurstageoui ?> name="modulebulletinvisututeurstage" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $modulebulletinvisututeurstagenon ?> name="modulebulletinvisututeurstage" value="non" class=btradio1  > non </td>
</tr>

</table></div></div>
<!-- ===== Section 5 : Configuration Enseignant ===== -->
<div class="acc-panel">
<div class="acc-header" onclick="accToggle(this.parentElement)"><span>Configuration Enseignant</span><span class="acc-arrow">&#9660;</span></div>
<div class="acc-body"><table class="conf-inner" width="100%">

<tr><td align=right >Module Stockage : </td>
<td align=left><input type=radio <?php print $stockageprofoui ?> name=stockageprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $stockageprofnon ?> name=stockageprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Intra-MSN : </td>
<td align=left><input type=radio <?php print $intramsnprofoui ?> name=intramsnprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $intramsnprofnon ?> name=intramsnprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Agenda : </td>
<td align=left><input type=radio <?php print $agendaprofoui ?> name=agendaprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $agendaprofnon ?> name=agendaprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Planning : </td>
<td align=left><input type=radio <?php print $planningprofoui ?> name="planningprof" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $planningprofnon ?> name="planningprof" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Emargement : </td>
<td align=left><input type=radio <?php print $emargementprofoui ?> name=emargementprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $emargementprofnon ?> name=emargementprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Mes Flux RSS : </td>
<td align=left><input type=radio <?php print $fluxrssprofoui ?> name=fluxrssprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $fluxrssprofnon ?> name=fluxrssprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module cantine : </td>
<td align=left><input type=radio <?php print $cantineprofoui ?> name=cantineprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $cantineprofnon ?> name=cantineprof value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Notes : </td>
<td align=left><input type=radio <?php print $notesprofoui ?> name=notesprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $notesprofnon ?> name=notesprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Bulletin Trimestriel : </td>
<td align=left><input type=radio <?php print $bulletinprofoui ?> name=bulletinprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $bulletinprofnon ?> name=bulletinprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Réservation salle : </td>
<td align=left><input type=radio <?php print $resaprofoui ?> name=resaprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $resaprofnon ?> name=resaprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Circulaires adm. : </td>
<td align=left><input type=radio <?php print $circulaireprofoui ?> name=circulaireprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $circulaireprofnon ?> name=circulaireprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Informations : </td>
<td align=left><input type=radio <?php print $informationprofoui ?> name=informationprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $informationprofnon ?> name=informationprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Visualiser un devoir : </td>
<td align=left><input type=radio <?php print $visudevoirprofoui ?> name=visudevoirprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $visudevoirprofnon ?> name=visudevoirprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Supprimer un devoir : </td>
<td align=left><input type=radio <?php print $suppdevoirprofoui ?> name=suppdevoirprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $suppdevoirprofnon ?> name=suppdevoirprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Cahier de textes : </td>
<td align=left><input type=radio <?php print $cahiertexteprofoui ?> name=cahiertexteprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $cahiertexteprofnon ?> name=cahiertexteprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Sanction élèves : </td>
<td align=left><input type=radio <?php print $sanctionprofoui ?> name=sanctionprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $sanctionprofnon ?> name=sanctionprof value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Fiche élèves : </td>
<td align=left><input type=radio <?php print $ficheeleveprofoui ?> name=ficheeleveprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $ficheeleveprofnon ?> name=ficheeleveprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Liste élèves : </td>
<td align=left><input type=radio <?php print $listeleveprofoui ?> name=listeleveprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $listeleveprofnon ?> name=listeleveprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Plan de classe : </td>
<td align=left><input type=radio <?php print $planprofoui ?> name=planprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $planprofnon ?> name=planprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Stage Pro. : </td>
<td align=left><input type=radio <?php print $stageproprofoui ?> name=stageproprof value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $stageproprofnon ?> name=stageproprof value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module D.S.T. : </td>
<td align=left><input type=radio <?php print $DSTProfoui ?> name="DSTProf" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $DSTProfnon ?> name="DSTProf" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module E-Learning : </td>
<td align=left><input type=radio <?php print $DOKEOSProfoui ?> name="DOKEOSProf" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $DOKEOSProfnon ?> name="DOKEOSProf" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Comptabilité (Vacation) : </td>
<td align=left><input type=radio <?php print $comptaProfoui ?> name="comptaProf" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $comptaProfnon ?> name="comptaProf" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Pacte Enseignant :</td>
<td align=left><input type=radio <?php print $modulepacteprofoui ?> name="modulepacteprof" value="oui" class=btradio1> oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $modulepacteprofnon ?> name="modulepacteprof" value="non" class=btradio1> non </td>
</tr>

<tr><td align=right >Module Messagerie : </td>
<td align=left><input type=radio <?php print $messagerieProfoui ?> name="messagerieProf" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $messagerieProfnon ?> name="messagerieProf" value="non" class=btradio1  > non </td>
</tr>

</tr>
<tr><td align=right >Module Savoir-être : </td>
<td align=left><input type=radio <?php print $moduleprofgestionsavoiretreoui ?> name="moduleprofgestionsavoiretre" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleprofgestionsavoiretrenon ?> name="moduleprofgestionsavoiretre" value="non" class=btradio1  > non </td>
</tr>

</table></div></div>
<!-- ===== Section 6 : Configuration Vie Scolaire ===== -->
<div class="acc-panel">
<div class="acc-header" onclick="accToggle(this.parentElement)"><span>Configuration Vie Scolaire</span><span class="acc-arrow">&#9660;</span></div>
<div class="acc-body"><table class="conf-inner" width="100%">

<tr><td align=right >Module News 1er page : </td>
<td align=left><input type=radio <?php print $newspageviescolaireoui ?> name=newspageviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $newspageviescolairenon ?> name=newspageviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module News établissement : </td>
<td align=left><input type=radio <?php print $newsviescolaireoui ?> name=newsviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $newsviescolairenon ?> name=newsviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Stockage : </td>
<td align=left><input type=radio <?php print $stockageviescolaireoui ?> name=stockageviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $stockageviescolairenon ?> name=stockageviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Intra-MSN : </td>
<td align=left><input type=radio <?php print $intramsnviescolaireoui ?> name=intramsnviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $intramsnviescolairenon ?> name=intramsnviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Agenda : </td>
<td align=left><input type=radio <?php print $agendaviescolaireoui ?> name=agendaviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $agendaviescolairenon ?> name=agendaviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Mes Flux RSS : </td>
<td align=left><input type=radio <?php print $fluxrssviescolaireoui ?> name=fluxrssviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $fluxrssviescolairenon ?> name=fluxrssviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module cantine : </td>
<td align=left><input type=radio <?php print $cantineviascolaireoui ?> name=cantineviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $cantineviascolairenon ?> name=cantineviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Etude : </td>
<td align=left><input type=radio <?php print $etudeviescolaireoui ?> name=etudeviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $etudeviescolairenon ?> name=etudeviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Circulaire : </td>
<td align=left><input type=radio <?php print $circulaireviescolaireoui ?> name=circulaireviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $circulaireviescolairenon ?> name=circulaireviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module D.S.T. : </td>
<td align=left><input type=radio <?php print $dstviescolaireoui ?> name=dstviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $dstviescolairenon ?> name=dstviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Stage Pro. : </td>
<td align=left><input type=radio <?php print $stageviescolaireoui ?> name=stageviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $stageviescolairenon ?> name=stageviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Visa vie scolaire : </td>
<td align=left><input type=radio <?php print $visaviescolaireoui ?> name=visaviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $visaviescolairenon ?> name=visaviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Note vie scolaire : </td>
<td align=left><input type=radio <?php print $noteviescolaireoui ?> name=noteviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $noteviescolairenon ?> name=noteviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Imprimer tableaux : </td>
<td align=left><input type=radio <?php print $imptableauviescolaireoui ?> name=imptableauviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $imptableauviescolairenon ?> name=imptableauviescolaire value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Imprimer bulletins : </td>
<td align=left><input type=radio <?php print $bulletinviescolaireoui ?> name=bulletinviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $bulletinviescolairenon ?> name=bulletinviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Imprimer période : </td>
<td align=left><input type=radio <?php print $periodeviescolaireoui ?> name=periodeviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $periodeviescolairenon ?> name=periodeviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Vidéo-Projecteur : </td>
<td align=left><input type=radio <?php print $videoprojoviescolaireoui ?> name=videoprojoviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $videoprojoviescolairenon ?> name=videoprojoviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Plan de classe : </td>
<td align=left><input type=radio <?php print $planclasseviescolaireoui ?> name=planclasseviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $planclasseviescolairenon ?> name=planclasseviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Vacation ens. : </td>
<td align=left><input type=radio <?php print $vacationviescolaireoui ?> name=vacationviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $vacationviescolairenon ?> name=vacationviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module History cmd : </td>
<td align=left><input type=radio <?php print $historyviescolaireoui ?> name=historyviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $historyviescolairenon ?> name=historyviescolaire value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Réservation salle : </td>
<td align=left><input type=radio <?php print $resaviescolaireoui ?> name=resaviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $resaviescolairenon ?> name=resaviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Exporter : </td>
<td align=left><input type=radio <?php print $exportviescolaireoui ?> name=exportviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $exportviescolairenon ?> name=exportviescolaire value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Notation enseignants : </td>
<td align=left><input type=radio <?php print $noteenseignantviascolaireoui ?> name="noteenseignantviascolaire" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $noteenseignantviascolairenon ?> name="noteenseignantviascolaire" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Messagerie : </td>
<td align=left><input type=radio <?php print $messagerieviescolaireoui ?> name="messagerieviescolaire" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $messagerieviescolairenon ?> name="messagerieviescolaire" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module pré-inscription : </td>
<td align=left><input type=radio <?php print $preinscriptionviescolaireoui ?> name="preinscriptionviescolaire" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $preinscriptionviescolairenon ?> name="preinscriptionviescolaire" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Savoir-être : </td>
<td align=left><input type=radio <?php print $moduleviescolairesavoiretreoui ?> name="moduleviescolairesavoiretre" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleviescolairesavoiretrenon ?> name="moduleviescolairesavoiretre" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Cahier de texte : </td>
<td align=left><input type=radio <?php print $cahierdetexteviescolaireoui ?> name=cahierdetexteviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $cahierdetexteviescolairenon ?> name=cahierdetexteviescolaire value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Chambre : </td>
<td align=left><input type=radio <?php print $chambreviescolaireoui ?> name=chambreviescolaire value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $chambreviescolairenon ?> name=chambreviescolaire value="non" class=btradio1  > non </td>
</tr>

</table></div></div>
<!-- ===== Section 7 : Configuration Direction ===== -->
<div class="acc-panel">
<div class="acc-header" onclick="accToggle(this.parentElement)"><span>Configuration Direction</span><span class="acc-arrow">&#9660;</span></div>
<div class="acc-body"><table class="conf-inner" width="100%">

<tr><td align=right >Module Stockage : </td>
<td align=left><input type=radio <?php print $stockageadminoui ?> name=stockageadmin value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $stockageadminnon ?> name=stockageadmin value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Intra-MSN : </td>
<td align=left><input type=radio <?php print $intramsnadminoui ?> name=intramsnadmin value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $intramsnadminnon ?> name=intramsnadmin value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Agenda : </td>
<td align=left><input type=radio <?php print $agendaadminoui ?> name=agendaadmin value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $agendaadminnon ?> name=agendaadmin value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Mes Flux RSS : </td>
<td align=left><input type=radio <?php print $fluxrssadminoui ?> name=fluxrssadmin value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $fluxrssadminnon ?> name=fluxrssadmin value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module cantine : </td>
<td align=left><input type=radio <?php print $cantineadminoui ?> name=cantineadmin value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $cantineadminnon ?> name=cantineadmin value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Vacation ens. : </td>
<td align=left><input type=radio <?php print $vacationadminoui ?> name=vacationadmin value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $vacationadminnon ?> name=vacationadmin value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module droit de scolaire : </td>
<td align=left><input type=radio <?php print $droitscolariteadminoui ?> name=droitscolariteadmin value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $droitscolariteadminnon ?> name=droitscolariteadmin value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module History cmd : </td>
<td align=left><input type=radio <?php print $historyadminoui ?> name=historyadmin value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $historyadminnon ?> name=historyadmin value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Réservation salle : </td>
<td align=left><input type=radio <?php print $resaadminoui ?> name=resaadmin value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $resaadminnon ?> name=resaadmin value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Notation enseignants : </td>
<td align=left><input type=radio <?php print $noteenseignantadminoui ?> name="noteenseignantadmin" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $noteenseignantadminnon ?> name="noteenseignantadmin" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module boursier : </td>
<td align=left><input type=radio <?php print $moduleboursieradminoui ?> name="moduleboursieradmin" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleboursieradminnon ?> name="moduleboursieradmin" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Rubrique Financier (Vatel) : </td>
<td align=left><input type=radio <?php print $modulefinanciervateladminoui ?> name="modulefinanciervateladmin" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $modulefinanciervateladminnon ?> name="modulefinanciervateladmin" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Rubrique Chambres (Vatel) : </td>
<td align=left><input type=radio <?php print $modulechambresvateladminoui ?> name="modulechambresvateladmin" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $modulechambresvateladminnon ?> name="modulechambresvateladmin" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Messagerie : </td>
<td align=left><input type=radio <?php print $messagerieadminoui ?> name="messagerieadmin" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $messagerieadminnon ?> name="messagerieadmin" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Rubrique Affectation : </td>
<td align=left><input type=radio <?php print $rubriqueaffectationoui ?> name="rubriqueaffectation" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $rubriqueaffectationnon ?> name="rubriqueaffectation" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Rubrique Gestion : </td>
<td align=left><input type=radio <?php print $rubriquegestionoui ?> name="rubriquegestion" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $rubriquegestionnon ?> name="rubriquegestion" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Rubrique Annexe : </td>
<td align=left><input type=radio <?php print $rubriqueannexeoui ?> name="rubriqueannexe" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $rubriqueannexenon ?> name="rubriqueannexe" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Rubrique Bulletin : </td>
<td align=left><input type=radio <?php print $rubriquebulletinoui ?> name="rubriquebulletin" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $rubriquebulletinnon ?> name="rubriquebulletin" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Rubrique Etablissement : </td>
<td align=left><input type=radio <?php print $rubriqueetablissementoui ?> name="rubriqueetablissement" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $rubriqueetablissementnon ?> name="rubriqueetablissement" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Rubrique Vie Scolaire : </td>
<td align=left><input type=radio <?php print $rubriqueviescolaireoui ?> name="rubriqueviescolaire" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $rubriqueviescolairenon ?> name="rubriqueviescolaire" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Rubrique Actualit&eacute; : </td>
<td align=left><input type=radio <?php print $rubriqueactualiteoui ?> name="rubriqueactualite" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $rubriqueactualitenon ?> name="rubriqueactualite" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Rubrique Etudiant : </td>
<td align=left><input type=radio <?php print $rubriqueetudiantoui ?> name="rubriqueetudiant" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $rubriqueetudiantnon ?> name="rubriqueetudiant" value="non" class=btradio1  > non </td>
</tr>

<?php // -------------------------------------------------------------------------------- ?>

<tr><td align=right >Module Suppléant : </td>
<td align=left><input type=radio <?php print $moduleadminsuppleantoui ?> name="moduleadminsuppleant" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminsuppleantnon ?> name="moduleadminsuppleant" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module sous-matière : </td>
<td align=left><input type=radio <?php print $moduleadminsousmatiereoui ?> name="moduleadminsousmatiere" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminsousmatierenon ?> name="moduleadminsousmatiere" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Gestion délégués : </td>
<td align=left><input type=radio <?php print $moduleadmingestiondelegueoui ?> name="moduleadmingestiondelegue" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadmingestiondeleguenon ?> name="moduleadmingestiondelegue" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Plan de classe : </td>
<td align=left><input type=radio <?php print $moduleadminplanclasseoui ?> name="moduleadminplanclasse" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminplanclassenon ?> name="moduleadminplanclasse" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Dossier médical : </td>
<td align=left><input type=radio <?php print $moduleadmindosmedicaloui ?> name="moduleadmindosmedical" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadmindosmedicalnon ?> name="moduleadmindosmedical" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Gestion dispenses : </td>
<td align=left><input type=radio <?php print $moduleadmingestiondispenseoui ?> name="moduleadmingestiondispense" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadmingestiondispensenon ?> name="moduleadmingestiondispense" value="non" class=btradio1  > non </td>
</tr>


</tr>
<tr><td align=right >Module Savoir-être : </td>
<td align=left><input type=radio <?php print $moduleadmingestionsavoiretreoui ?> name="moduleadmingestionsavoiretre" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadmingestionsavoiretrenon ?> name="moduleadmingestionsavoiretre" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Sanctions du jour : </td>
<td align=left><input type=radio <?php print $moduleadminsanctiondujouroui ?> name="moduleadminsanctiondujour" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminsanctiondujournon ?> name="moduleadminsanctiondujour" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Retenues du jour : </td>
<td align=left><input type=radio <?php print $moduleadminretenudjoui ?> name="moduleadminretenudj" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminretenudjnon ?> name="moduleadminretenudj" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Gestion discipline : </td>
<td align=left><input type=radio <?php print $moduleadmingestiondisciplineoui ?> name="moduleadmingestiondiscipline" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadmingestiondisciplinenon ?> name="moduleadmingestiondiscipline" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Gestion étude : </td>
<td align=left><input type=radio <?php print $moduleadmingestionetudeoui ?> name="moduleadmingestionetude" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadmingestionetudenon ?> name="moduleadmingestionetude" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Examens / Brevets : </td>
<td align=left><input type=radio <?php print $moduleadminexambrevetoui ?> name="moduleadminexambrevet" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminexambrevetnon ?> name="moduleadminexambrevet" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Fourniture scolaire : </td>
<td align=left><input type=radio <?php print $moduleadminfournitureoui ?> name="moduleadminfourniture" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminfourniturenon ?> name="moduleadminfourniture" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Gestion S.M.S. : </td>
<td align=left><input type=radio <?php print $moduleadmingestionsmsoui ?> name="moduleadmingestionsms" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadmingestionsmsnon ?> name="moduleadmingestionsms" value="non" class=btradio1  > non </td>
</tr>
<tr><td align=right >Module Notanet : </td>
<td align=left><input type=radio <?php print $moduleadminnotanetoui ?> name="moduleadminnotanet" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminnotanetnon ?> name="moduleadminnotanet" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module C.D.I. : </td>
<td align=left><input type=radio <?php print $moduleadmincdioui ?> name="moduleadmincdi" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadmincdinon ?> name="moduleadmincdi" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Prof P. / Instituteur : </td>
<td align=left><input type=radio <?php print $moduleprofPoui ?> name="moduleadminprofP" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleprofPnon ?> name="moduleadminprofP" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Config Note USA : </td>
<td align=left><input type=radio <?php print $moduleconfignoteusaoui ?> name="moduleadminconfignoteusa" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleconfignoteusanon ?> name="moduleadminconfignoteusa" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Entretien Individuel : </td>
<td align=left><input type=radio <?php print $moduleentretienindividueloui ?> name="moduleadminentretienindividuel" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleentretienindividuelnon ?> name="moduleadminentretienindividuel" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Carnet de suivi : </td>
<td align=left><input type=radio <?php print $modulecarnetsuivioui ?> name="moduleadmincarnetsuivi" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $modulecarnetsuivinon ?> name="moduleadmincarnetsuivi" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Vérifier bulletins : </td>
<td align=left><input type=radio <?php print $moduleverifbulletinoui ?> name="moduleadminverifbulletin" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleverifbulletinnon ?> name="moduleadminverifbulletin" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module notes vie scolaire : </td>
<td align=left><input type=radio <?php print $modulenoteviescolaireoui ?> name="moduleadminnoteviescolaire" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $modulenoteviescolairenon ?> name="moduleadminnoteviescolaire" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module imprimer période : </td>
<td align=left><input type=radio <?php print $moduleadminimprperiodeoui ?> name="moduleadminimprperiode" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminimprperiodenon ?> name="moduleadminimprperiode" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Abs/Retard : </td>
<td align=left><input type=radio <?php print $moduleadminabsrtdoui ?> name="moduleadminabsrtd" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminabsrtdnon ?> name="moduleadminabsrtd" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module pré-inscription : </td>
<td align=left><input type=radio <?php print $moduleadminpreinscriptionoui ?> name="moduleadminpreinscription" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminpreinscriptionnon ?> name="moduleadminpreinscription" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module nouvelle année : </td>
<td align=left><input type=radio <?php print $moduleadminnouvelleanneeoui ?> name="moduleadminnouvelleannee" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminnouvelleanneenon ?> name="moduleadminnouvelleannee" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Archivage : </td>
<td align=left><input type=radio <?php print $moduleadminarchivageoui ?> name="moduleadminarchivage" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminarchivagenon ?> name="moduleadminarchivage" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module News défilant : </td>
<td align=left><input type=radio <?php print $moduleadminnewsdefilantoui ?> name="moduleadminnewsdefilant" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminnewsdefilantnon ?> name="moduleadminnewsdefilant" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Purger infos : </td>
<td align=left><input type=radio <?php print $moduleadminpurgerinfooui ?> name="moduleadminpurgerinfo" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminpurgerinfonon ?> name="moduleadminpurgerinfo" value="non" class=btradio1  > non </td>
</tr>


<tr><td align=right >Module Eval. Enseignant : </td>
<td align=left><input type=radio <?php print $moduleadminevalensoui ?> name="moduleadminevalens" value="oui" class=btradio1  > oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $moduleadminevalensnon ?>  name="moduleadminevalens" value="non" class=btradio1  > non </td>
</tr>

<tr><td align=right >Module Pacte Enseignant :</td>
<td align=left><input type=radio <?php print $modulepacteadminoui ?> name="modulepacteadmin" value="oui" class=btradio1> oui &nbsp;&nbsp;&nbsp;
               <input type=radio <?php print $modulepacteadminnon ?> name="modulepacteadmin" value="non" class=btradio1> non </td>
</tr>





</table></div></div>

<br>
<div class="na-foot">
  <span style="display:flex;"><script language=JavaScript>buttonMagicSubmit("Enregistrer","create"); //text,nomInput</script></span>
  &nbsp;&nbsp;
  <script language=JavaScript>buttonMagic("Valeur par défaut","config_module-default.php","_parent","","") //text,nomInput</script>
</div>
<br>

<script>
function accToggle(panel) {
  var o = panel.classList.contains('acc-open');
  document.querySelectorAll('.acc-panel').forEach(function(p){ p.classList.remove('acc-open'); });
  if (!o) panel.classList.add('acc-open');
}
function accCheckAlerts() {
  document.querySelectorAll('.acc-panel').forEach(function(panel) {
    var unset = false;
    var groups = {};
    panel.querySelectorAll('input[type="radio"]').forEach(function(r) {
      if (!groups[r.name]) groups[r.name] = { checked: false, first: r };
      if (r.checked) groups[r.name].checked = true;
    });
    Object.keys(groups).forEach(function(name) {
      var g = groups[name];
      var container = g.first.closest('span') || g.first.closest('td');
      var inlineBadge = container ? container.querySelector('.acc-inline-alert[data-group="' + CSS.escape(name) + '"]') : null;
      if (!g.checked) {
        unset = true;
        if (container && !inlineBadge) {
          inlineBadge = document.createElement('span');
          inlineBadge.className = 'acc-inline-alert';
          inlineBadge.setAttribute('data-group', name);
          inlineBadge.title = 'Veuillez sélectionner une option';
          inlineBadge.textContent = '!';
          container.appendChild(inlineBadge);
        }
      } else {
        if (inlineBadge) inlineBadge.remove();
      }
    });
    var badge = panel.querySelector('.acc-alert');
    if (unset) {
      if (!badge) {
        badge = document.createElement('span');
        badge.className = 'acc-alert';
        badge.title = 'Option(s) non sélectionnée(s) dans cette section';
        badge.textContent = '!';
        panel.querySelector('.acc-arrow').insertAdjacentElement('beforebegin', badge);
      }
    } else {
      if (badge) badge.remove();
    }
  });
}
accCheckAlerts();
document.querySelectorAll('.acc-panel input[type="radio"]').forEach(function(r) {
  r.addEventListener('change', accCheckAlerts);
});
document.querySelector('.acc-panel').classList.add('acc-open');
</script>

</td></tr></table>
</form>

<SCRIPT language="JavaScript">InitBulle("#000000","#FFFFFF","red",1);</SCRIPT>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
