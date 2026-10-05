<?php
session_start();
if (isset($_POST["type_bulletin"])) {
	$type_bulletin=$_POST["type_bulletin"];
	setcookie("type_bulletin",$type_bulletin,time()+36000*24*30);
}else{
	@$type_bulletin=$_COOKIE["type_bulletin"];
}

if (isset($_POST["anneeScolaire"])) {
	setcookie("anneeScolaire",$_POST["anneeScolaire"],time()+36000*24*30);
	$anneeScolaire=$_POST["anneeScolaire"];
}

if (isset($_POST["saisie_trimestre"])) {
	setcookie("saisie_trimestre",$_POST["saisie_trimestre"],time()+36000*24*30);
}
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
<META http-equiv="expires" content="-1">
<meta charset="utf-8">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<link rel="stylesheet" href="librairie_css/css-v4.css">
<link rel="stylesheet" href="librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/scriptaculous.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_visadirec.js"></script>
<title>Triade Vidéo-Projecteur</title>
<style>
* { box-sizing: border-box; }
html, body { height: 100%; margin: 0; padding: 0; font-family: Electrolize, Trebuchet MS, Arial, sans-serif; background: #f5f7ff; overflow: hidden; }

/* ── Barre de navigation ── */
.vp-nav {
  height: 40px;
  background: linear-gradient(135deg,#080A66 0%,#1a1c8a 100%);
  flex-shrink: 0; position: relative;
  display: flex; align-items: center; justify-content: center;
}
.vp-nav form { display: flex; align-items: center; justify-content: center; }
.vp-btn-precedent { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); }
.vp-btn-suivant   { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); }
.vp-nav-center { display: flex; align-items: center; justify-content: center; gap: 8px; flex-wrap: nowrap; }
.vp-nav select {
  border: 1px solid #CACCEF; border-radius: 4px;
  padding: 3px 7px; font-size: 12px; color: #222; background: #fff; max-width: 220px;
}
.vp-conn { font-size: 10px; color: #CACCEF; white-space: nowrap; }

/* ── Layout principal ── */
.vp-main { display: flex; height: calc(100vh - 40px); }
.vp-bulletin { flex: 7; min-width: 0; }
.vp-panel { flex: 3; min-width: 220px; display: flex; flex-direction: column; border-left: 2px solid #CACCEF; background: #fff; }
.vp-panel-eleve { height: 155px; border-bottom: 1px solid #dde0f0; flex-shrink: 0; }
.vp-panel-scol  { flex: 1; min-height: 0; border-bottom: 1px solid #dde0f0; position: relative; }
.vp-panel-actions { padding: 8px 12px; flex-shrink: 0; overflow-y: auto; max-height: 200px; }

iframe { border: 0; display: block; width: 100%; height: 100%; }

/* ── Popups visa ── */
.vp-popup {
  position: absolute; top: 10px; left: 10px; right: 10px;
  display: none; z-index: 1000;
  border-radius: 8px; overflow: hidden;
  box-shadow: 0 8px 28px rgba(8,10,102,.22);
  border: 1px solid #CACCEF;
  background: #fff;
}
.vp-popup-header {
  background: linear-gradient(135deg,#080A66 0%,#1a1c8a 100%); color: #fff;
  padding: 7px 12px; font-size: 12px; font-weight: 700;
  display: flex; align-items: center; justify-content: space-between;
  cursor: move; user-select: none;
}
.vp-popup-header i { margin-right: 5px; }
.vp-popup-sub { font-size: 10px; color: #CACCEF; }
.vp-popup-body { background: #fff; padding: 10px 12px; }
.vp-popup textarea {
  width: 100%; border: 1px solid #c5cae9; border-radius: 4px;
  padding: 7px 9px; font-size: 12px; resize: vertical; min-height: 100px;
  font-family: inherit;
}
.vp-popup textarea:focus { border-color: #080A66; outline: none; }
.vp-popup-counter { font-size: 10px; color: #888; display: flex; align-items: center; gap: 5px; margin-top: 4px; }
.vp-popup-counter input { width: 32px; text-align: center; border: 1px solid #dde0f0; border-radius: 3px; padding: 1px 3px; font-size: 10px; background: #f8f8f8; }
.vp-popup-mention { padding: 7px 12px; border-top: 1px solid #f0f2fa; background: #f8f9ff; display: flex; flex-wrap: wrap; gap: 8px; align-items: center; font-size: 11px; }
.vp-popup-mention label { display: flex; align-items: center; gap: 3px; cursor: pointer; color: #333; }
.vp-popup-footer { padding: 7px 12px; border-top: 1px solid #f0f2fa; display: flex; align-items: center; gap: 7px; }
.vp-retour { font-size: 11px; color: red; }

/* ── Section actions ── */
.vp-section-title { font-size: 10px; font-weight: 700; color: #080A66; text-transform: uppercase; letter-spacing: .05em; margin: 0 0 6px; }
.vp-action-link { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #080A66; text-decoration: none; margin-bottom: 6px; }
.vp-action-link i { font-size: 13px; color: #CACCEF; }
.vp-action-link:hover { text-decoration: underline; }
.vp-charts { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 4px; }
.vp-graph-btn {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  width: 64px; padding: 8px 4px 6px; gap: 4px;
  background: #f0f2fa; border: 1px solid #dde0f0; border-radius: 7px;
  text-decoration: none; color: #080A66; cursor: pointer;
  transition: background .15s, border-color .15s, box-shadow .15s;
}
.vp-graph-btn i { font-size: 20px; color: #080A66; }
.vp-graph-btn span { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #555; }
.vp-graph-btn:hover { background: #e8eaf6; border-color: #080A66; box-shadow: 0 2px 8px rgba(8,10,102,.15); }
</style>
<script>
function vpMakeDraggable(id) {
    var popup = document.getElementById(id);
    var handle = popup.getElementsByClassName('vp-popup-header')[0];
    var dx = 0, dy = 0, dragging = false;
    handle.setAttribute('draggable', 'false');
    handle.addEventListener('dragstart', function(e){ e.preventDefault(); });
    handle.addEventListener('mousedown', function(e) {
        var pr  = popup.getBoundingClientRect();
        var par = popup.offsetParent ? popup.offsetParent.getBoundingClientRect() : {left:0,top:0};
        popup.style.left  = (pr.left - par.left) + 'px';
        popup.style.top   = (pr.top  - par.top)  + 'px';
        popup.style.width = pr.width + 'px';
        popup.style.right = 'auto';
        dx = e.clientX - pr.left;
        dy = e.clientY - pr.top;
        dragging = true;
        e.preventDefault();
    });
    document.addEventListener('mousemove', function(e) {
        if (!dragging) return;
        var par = popup.offsetParent ? popup.offsetParent.getBoundingClientRect() : {left:0,top:0};
        popup.style.left = (e.clientX - par.left - dx) + 'px';
        popup.style.top  = (e.clientY - par.top  - dy) + 'px';
    });
    document.addEventListener('mouseup', function() { dragging = false; });
}
window.addEventListener('load', function() {
    vpMakeDraggable('visdir');
    vpMakeDraggable('visprofp');
});
function reactualise() {
	document.getElementById('com2').value='';
	var contenue="";
	if (document.getElementById('leap1').checked == true) { contenue="leap_felicitation,"; }
	if (document.getElementById('leap2').checked == true) { contenue+="leap_encouragement,"; }
	if (document.getElementById('leap3').checked == true) { contenue+="leap_megcomp,"; }
	if (document.getElementById('leap4').checked == true) { contenue+="leap_megtrav,"; }
	document.getElementById('com2').value=contenue;
}
function reactualise1() {
	document.getElementById('com21').value='';
	var contenue="";
	if (document.getElementById('leap11').checked == true) { contenue="leap_felicitation,"; }
	if (document.getElementById('leap21').checked == true) { contenue+="leap_encouragement,"; }
	if (document.getElementById('leap31').checked == true) { contenue+="leap_megcomp,"; }
	if (document.getElementById('leap41').checked == true) { contenue+="leap_megtrav,"; }
	document.getElementById('com21').value=contenue;
}
</script>
</head>
<body>
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
include_once('librairie_php/recupnoteperiode.php');

if (isset($_POST["validenoteviescolaire"])) { $_SESSION["validenoteviescolaire"]=$_POST["validenoteviescolaire"]; }

$cnx=cnx();

if ($_SESSION["membre"] == "menupersonnel") {
	if ((!verifDroit($_SESSION["id_pers"],"ficheeleve")) && (!verifDroit($_SESSION["id_pers"],"videoprojo"))) {
		Pgclose();
		accesNonReserveFen();
		exit();
	}
}

$validenoteviescolaire=$_POST["validenoteviescolaire"];
config_param_ajout($validenoteviescolaire,"validenoteviescolaire");
$afficheNotePartielVatel=$_POST["afficheNotePartielVatel"];
config_param_ajout($afficheNotePartielVatel,"affNotePartielVatel");

$ok=1;
if (isset($_POST["supp"])) {
	$idclasse=$_POST["saisie_classe"];
	$sql="SELECT * FROM {$prefixe}eleves WHERE classe='$idclasse' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (countTriade($data) <= 0) {
		if ($_POST["fichier_origin"] == "profpprojo") {
			print "<script language=JavaScript>location.href='profpprojo.php?info=1'</script>";
		}else{
			print "<script language=JavaScript>location.href='video-proj-index.php?info=1'</script>";
		}
	}
}

if (isset($_GET["apres"])) {
	$i=$_GET["apres"];
	$trimes=$_GET["saisie_trimestre"];
	$idclasse=$_GET["saisie_classe"];
	$anneeScolaire=$_GET["anneeScolaire"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves, {$prefixe}classes WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
	$res=execSql($sql);
	$data_eleve=chargeMat($res);
	if ($i <= 0) { $i=0; }
	$ideleve=$data_eleve[$i][1];
	$ok=0;
	$iplus=$i+1;
	$imoins=$i-1;
}

if (isset($_POST["direct_eleve"])) {
	$idclasse=$_POST["saisie_classe"];
	$anneeScolaire=$_GET["anneeScolaire"];
	$trimes=$_POST["saisie_trimestre"];
	$ok=0;
	$ideleve=$_POST["direct_eleve"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves, {$prefixe}classes WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
	$res=execSql($sql);
	$data_eleve=chargeMat($res);
	for ($j=0;$j<countTriade($data_eleve);$j++) {
		if ($ideleve == $data_eleve[$j][1]) { $i=$j; break; }
	}
	$iplus=$i+1;
	$imoins=$i-1;
}

if ($ok == 1) {
	$idclasse=$_POST["saisie_classe"];
	$trimes=$_POST["saisie_trimestre"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves, {$prefixe}classes WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
	$res=execSql($sql);
	$data_eleve=chargeMat($res);
	$ideleve=$data_eleve[0][1];
	$i=0;
	$iplus=$i+1;
	$imoins=$i-1;
}
?>

<!-- ── Barre de navigation ── -->
<div class="vp-nav">

  <button type="button" class="btn btn-secondary btn-sm vp-btn-precedent"
    onclick="open('video-proj-affichage.php?apres=<?php print $imoins?>&saisie_classe=<?php print $idclasse?>&saisie_trimestre=<?php print $trimes?>&anneeScolaire=<?php print $anneeScolaire ?>','video','');this.disabled=true">
    <i class="bi bi-chevron-left"></i> Précédent
  </button>

  <form method=post onsubmit="return valide_supp_choix('direct_eleve','un élève')" name="formulaire">
  <div class="vp-nav-center">
    <script language="JavaScript">buttonMagicImprimer();</script>
    <input type=hidden name="saisie_classe" value="<?php print $idclasse?>">
    <input type=hidden name="saisie_trimestre" value="<?php print $trimes?>">
    <input type=hidden name="anneeScolaire" value="<?php print $anneeScolaire?>">
    <select name="direct_eleve">
      <option id="select0"><?php print LANGCHOIX?></option>
      <?php
      $sql="SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves, {$prefixe}classes WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
      $res=execSql($sql); $data_eleve=chargeMat($res);
      for ($j=0;$j<countTriade($data_eleve);$j++) { ?>
      <option value="<?php print $data_eleve[$j][1]?>"><?php print ucwords(trim($data_eleve[$j][2]))." ".trim($data_eleve[$j][3])?></option>
      <?php } ?>
    </select>
    <input type=submit class="btn btn-secondary btn-sm" value="<?php print LANGPER27 ?>" name=rien>
    <span class="vp-conn">
      <?php include_once("./librairie_php/lib_conexpersistant.php"); connexpersistance("color:#CACCEF;font-size:10px"); ?>
    </span>
  </div>

  </form>

  <button type="button" class="btn btn-secondary btn-sm vp-btn-suivant"
    onclick="open('video-proj-affichage.php?apres=<?php print $iplus?>&saisie_classe=<?php print $idclasse?>&saisie_trimestre=<?php print $trimes?>&anneeScolaire=<?php print $anneeScolaire ?>','video','');this.disabled=true">
    Suivant <i class="bi bi-chevron-right"></i>
  </button>
</div>

<!-- ── Layout principal ── -->
<div class="vp-main">

  <!-- Bulletin (70%) -->
  <div class="vp-bulletin">
    <iframe src="video-proj-bulletin.php?saisie_eleve=<?php print $ideleve?>&saisie_classe=<?php print $idclasse?>&saisie_trimestre=<?php print $trimes?>&type_bulletin=<?php print $type_bulletin?>" scrolling="auto"></iframe>
  </div>

  <!-- Panneau droit (30%) -->
  <div class="vp-panel">

    <!-- Fiche élève -->
    <div class="vp-panel-eleve">
      <iframe src="video-proj-fiche-eleve.php?saisie_eleve=<?php print $ideleve?>&saisie_classe=<?php print $idclasse?>" scrolling="no"></iframe>
    </div>

    <!-- Scolarité + popups -->
    <div class="vp-panel-scol">

      <!-- Popup Visa Direction -->
      <div id="visdir" class="vp-popup">
        <?php $commentaireD=recherche_com($ideleve,$trimes,$type_bulletin,$anneeScolaire); ?>
        <form>
          <div class="vp-popup-header">
            <span><i class="bi bi-pencil-square"></i> Visa Direction</span>
            <span class="vp-popup-sub"><?php print $type_bulletin ?></span>
          </div>
          <div class="vp-popup-body">
            <textarea name='com' onkeypress="compter(this,'500',this.form.CharRestant_2)"><?php print $commentaireD ?></textarea>
            <div class="vp-popup-counter"><input type=text name='CharRestant_2' size=3 disabled='disabled' value='0'> car. restants</div>
            <input type='hidden' name='com2' id='com2' value=''>
          </div>
          <?php if (($type_bulletin == "montessori") || ($type_bulletin == "montessori_spec")): ?>
          <?php
            $montessori=recherchemontessori($ideleve,$type_bulletin,$trimes,$anneeScolaire);
            $montessori=$montessori[0][0];
            $checkedmont1=($montessori=="felicitation")?"checked='checked'":"";
            $checkedmont2=($montessori=="satisfaction")?"checked='checked'":"";
            $checkedmont3=($montessori=="encouragement")?"checked='checked'":"";
          ?>
          <div class="vp-popup-mention">
            <label><input type='radio' name='montessori' value=''> Aucun</label>
            <label><input name='montessori' type='radio' onclick="this.form.com2.value=this.value" value='felicitation' <?php print $checkedmont1?>> Félicitations</label>
            <label><input name='montessori' type='radio' onclick="this.form.com2.value=this.value" value='satisfaction' <?php print $checkedmont2?>> Satisfactions</label>
            <label><input name='montessori' type='radio' onclick="this.form.com2.value=this.value" value='encouragement' <?php print $checkedmont3?>> Encouragements</label>
          </div>
          <?php elseif ($type_bulletin == "leap"): ?>
          <?php
            $leap=rechercheleap($ideleve,$type_bulletin,$trimes,$anneeScolaire);
            $checkedmont1=($leap[0][1]=="1")?"checked='checked'":"";
            $checkedmont2=($leap[0][2]=="1")?"checked='checked'":"";
            $checkedmont3=($leap[0][0]=="1")?"checked='checked'":"";
            $checkedmont4=($leap[0][3]=="1")?"checked='checked'":"";
          ?>
          <div class="vp-popup-mention">
            <label><input id='leap1' type='checkbox' onclick="reactualise()" value='1' <?php print $checkedmont1?>> Félicitations</label>
            <label><input id='leap2' type='checkbox' onclick="reactualise()" value='1' <?php print $checkedmont3?> title='Encouragement'> Encour.</label>
            <label><input id='leap3' type='checkbox' onclick="reactualise()" value='1' <?php print $checkedmont2?> title='Mise en garde comportement'> MEG Comp.</label>
            <label><input id='leap4' type='checkbox' onclick="reactualise()" value='1' <?php print $checkedmont4?> title='Mise en garde travail'> MEG Trav.</label>
          </div>
          <script>reactualise();</script>
          <?php elseif ($type_bulletin == "seminaire"): ?>
          <?php
            $montessori=recherchemontessori($ideleve,$type_bulletin,$trimes,$anneeScolaire);
            $checkedmont1=($montessori=="felicitation")?"checked='checked'":"";
            $checkedmont2=($montessori=="tabhonneur")?"checked='checked'":"";
            $checkedmont3=($montessori=="encouragement")?"checked='checked'":"";
            $checkedmont4=($montessori=="deconduite")?"checked='checked'":"";
            $checkedmont5=($montessori=="detravail")?"checked='checked'":"";
          ?>
          <div class="vp-popup-mention">
            <label><input name='montessori' type='radio' onclick="this.form.com2.value=this.value" value=''> Aucun</label>
            <label><input name='montessori' type='radio' onclick="this.form.com2.value=this.value" value='felicitation' <?php print $checkedmont1?>> Félicitations</label>
            <label><input name='montessori' type='radio' onclick="this.form.com2.value=this.value" value='tabhonneur' <?php print $checkedmont2?>> Tableau d'Honneur</label>
            <label><input name='montessori' type='radio' onclick="this.form.com2.value=this.value" value='encouragement' <?php print $checkedmont3?>> Encouragements</label>
            <label><input name='montessori' type='radio' onclick="this.form.com2.value=this.value" value='deconduite' <?php print $checkedmont4?>> Avert. conduite</label>
            <label><input name='montessori' type='radio' onclick="this.form.com2.value=this.value" value='detravail' <?php print $checkedmont5?>> Avert. travail</label>
          </div>
          <?php endif; ?>
          <div class="vp-popup-footer">
            <?php if ($_SESSION["membre"] == "menuadmin"): ?>
            <button type="button" class="btn btn-primary btn-sm"
              onclick="enrVisaDir('<?php print $ideleve?>',this.form.com.value,'<?php print $trimes?>','retourenr0',this.form.com2.value,'<?php print $type_bulletin?>','<?php print $anneeScolaire?>')">
              <i class="bi bi-check2"></i> <?php print LANGENR ?>
            </button>
            <?php endif; ?>
            <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('visdir').style.display='none'">
              <i class="bi bi-x-lg"></i> <?php print LANGFERMERFEN ?>
            </button>
            <span id='retourenr0' class="vp-retour"></span>
          </div>
        </form>
      </div>

      <!-- Popup Visa Professeur Principal -->
      <div id="visprofp" class="vp-popup">
        <?php $commentaireP=recherche_com_profP($ideleve,$trimes,$anneeScolaire); ?>
        <form>
          <div class="vp-popup-header">
            <span><i class="bi bi-person-badge"></i> Visa Professeur Principal</span>
          </div>
          <div class="vp-popup-body">
            <textarea name='com' onkeypress="compter(this,'500',this.form.CharRestant_2)"><?php print $commentaireP ?></textarea>
            <div class="vp-popup-counter"><input type=text name='CharRestant_2' size=3 disabled='disabled' value='0'> car. restants</div>
          </div>
          <?php if ($type_bulletin == "leap"): ?>
          <?php
            $leap=rechercheleap($ideleve,$type_bulletin,$trimes,$anneeScolaire);
            $checkedmont1=($leap[0][1]=="1")?"checked='checked'":"";
            $checkedmont2=($leap[0][2]=="1")?"checked='checked'":"";
            $checkedmont3=($leap[0][0]=="1")?"checked='checked'":"";
            $checkedmont4=($leap[0][3]=="1")?"checked='checked'":"";
          ?>
          <div class="vp-popup-mention">
            <label><input id='leap11' type='checkbox' onclick="reactualise1()" value='1' <?php print $checkedmont1?>> Félicitations</label>
            <label><input id='leap21' type='checkbox' onclick="reactualise1()" value='1' <?php print $checkedmont3?> title='Encouragement'> Encour.</label>
            <label><input id='leap31' type='checkbox' onclick="reactualise1()" value='1' <?php print $checkedmont2?> title='MEG comportement'> MEG Comp.</label>
            <label><input id='leap41' type='checkbox' onclick="reactualise1()" value='1' <?php print $checkedmont4?> title='MEG travail'> MEG Trav.</label>
          </div>
          <input type='hidden' name='com21' id='com21' value=''>
          <script>reactualise1();</script>
          <?php else: ?>
          <input type='hidden' name='com21' id='com21' value=''>
          <?php endif; ?>
          <div class="vp-popup-footer">
            <?php if (($_SESSION["membre"]=="menuadmin") || (($_SESSION["membre"]=="menuprof") && verif_profp_class2($_SESSION["id_pers"],$idclasse))): ?>
            <button type="button" class="btn btn-primary btn-sm"
              onclick="enrVisaProfp('<?php print $ideleve?>',this.form.com.value,'<?php print $trimes?>','retourenr1',this.form.com21.value,'<?php print $type_bulletin?>','<?php print $anneeScolaire?>')">
              <i class="bi bi-check2"></i> <?php print LANGENR ?>
            </button>
            <?php endif; ?>
            <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('visprofp').style.display='none'">
              <i class="bi bi-x-lg"></i> <?php print LANGFERMERFEN ?>
            </button>
            <span id='retourenr1' class="vp-retour"></span>
          </div>
        </form>
      </div>

      <iframe src="video-proj-eleve-scol.php?saisie_eleve=<?php print $ideleve?>&saisie_classe=<?php print $idclasse?>&trimestre=<?php print $trimes?>" scrolling="no"></iframe>
    </div>

    <!-- Actions : commentaires + graphiques -->
    <div class="vp-panel-actions">
      <p class="vp-section-title">Commentaires</p>
      <a href="#" class="vp-action-link" onclick="document.getElementById('visdir').style.display='block';return false;">
        <i class="bi bi-pencil-square"></i> Commentaire de la direction
      </a>
      <a href="#" class="vp-action-link" onclick="document.getElementById('visprofp').style.display='block';return false;">
        <i class="bi bi-person-badge"></i> Commentaire du professeur principal
      </a>
      <p class="vp-section-title" style="margin-top:8px">Graphiques</p>
      <div class="vp-charts">
        <a href="#" class="vp-graph-btn" title="Graphique général"
          onclick="open('video_projo0.php?saisie_eleve=<?php print $ideleve?>&saisie_classe=<?php print $idclasse?>','_blank','resizable=yes,width=500,height=350');return false;">
          <i class="bi bi-bar-chart-line-fill"></i>
          <span>Général</span>
        </a>
        <a href="#" class="vp-graph-btn" title="Graphique évolution"
          onclick="open('video_projo1.php?saisie_eleve=<?php print $ideleve?>&saisie_classe=<?php print $idclasse?>&trimestre=<?php print $trimes?>','_blank','resizable=yes,width=950,height=250');return false;">
          <i class="bi bi-graph-up-arrow"></i>
          <span>Évolution</span>
        </a>
        <a href="#" class="vp-graph-btn" title="Graphique radar"
          onclick="open('video_projo2.php?saisie_eleve=<?php print $ideleve?>&saisie_classe=<?php print $idclasse?>&trimestre=<?php print $trimes?>','_blank','resizable=yes,width=530,height=550');return false;">
          <i class="bi bi-bullseye"></i>
          <span>Radar</span>
        </a>
      </div>
    </div>

  </div><!-- .vp-panel -->
</div><!-- .vp-main -->

<?php Pgclose(); ?>
</body>
</html>
