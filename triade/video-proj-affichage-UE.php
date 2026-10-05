<?php
session_start();
if (isset($_POST["type_bulletin"])) {
	$type_bulletin=$_POST["type_bulletin"];
	setcookie("type_bulletin",$type_bulletin,time()+36000*24*30);
}else{
	$type_bulletin=$_COOKIE["type_bulletin"];
}
/***************************************************************************
 *                              T.R.I.A.D.E
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
<title>Triade Vidéo-Projecteur (UE)</title>
<style>
* { box-sizing: border-box; }
html, body { height: 100%; margin: 0; padding: 0; font-family: Electrolize, Trebuchet MS, Arial, sans-serif; background: #f5f7ff; overflow: hidden; }

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
.vp-nav select { border: 1px solid #CACCEF; border-radius: 4px; padding: 3px 7px; font-size: 12px; color: #222; background: #fff; max-width: 220px; }
.vp-conn { font-size: 10px; color: #CACCEF; white-space: nowrap; }

.vp-main { display: flex; height: calc(100vh - 40px); }
.vp-bulletin { flex: 7; min-width: 0; }
.vp-panel { flex: 3; min-width: 220px; display: flex; flex-direction: column; border-left: 2px solid #CACCEF; background: #fff; }
.vp-panel-eleve { height: 155px; border-bottom: 1px solid #dde0f0; flex-shrink: 0; }
.vp-panel-scol  { flex: 1; border-bottom: 1px solid #dde0f0; overflow: hidden; position: relative; }
.vp-panel-actions { padding: 8px 12px; flex-shrink: 0; overflow-y: auto; max-height: 200px; }

iframe { border: 0; display: block; width: 100%; height: 100%; }

.vp-popup {
  position: absolute; top: 10px; left: 10px; right: 10px;
  display: none; z-index: 1000;
  border-radius: 8px; overflow: hidden;
  box-shadow: 0 8px 28px rgba(8,10,102,.22);
  border: 1px solid #CACCEF;
}
.vp-popup-header { background: linear-gradient(135deg,#080A66 0%,#1a1c8a 100%); color: #fff; padding: 7px 12px; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: space-between; }
.vp-popup-header i { margin-right: 5px; }
.vp-popup-sub { font-size: 10px; color: #CACCEF; }
.vp-popup-body { background: #fff; padding: 10px 12px; }
.vp-popup textarea { width: 100%; border: 1px solid #c5cae9; border-radius: 4px; padding: 7px 9px; font-size: 12px; resize: vertical; min-height: 100px; font-family: inherit; }
.vp-popup textarea:focus { border-color: #080A66; outline: none; }
.vp-popup-counter { font-size: 10px; color: #888; display: flex; align-items: center; gap: 5px; margin-top: 4px; }
.vp-popup-counter input { width: 32px; text-align: center; border: 1px solid #dde0f0; border-radius: 3px; padding: 1px 3px; font-size: 10px; background: #f8f8f8; }
.vp-popup-mention { padding: 7px 12px; border-top: 1px solid #f0f2fa; background: #f8f9ff; display: flex; flex-wrap: wrap; gap: 8px; align-items: center; font-size: 11px; }
.vp-popup-mention label { display: flex; align-items: center; gap: 3px; cursor: pointer; color: #333; }
.vp-popup-footer { padding: 7px 12px; border-top: 1px solid #f0f2fa; display: flex; align-items: center; gap: 7px; }
.vp-retour { font-size: 11px; color: red; }

.vp-section-title { font-size: 10px; font-weight: 700; color: #080A66; text-transform: uppercase; letter-spacing: .05em; margin: 0 0 6px; }
.vp-action-link { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #080A66; text-decoration: none; margin-bottom: 6px; }
.vp-action-link i { font-size: 13px; color: #CACCEF; }
.vp-action-link:hover { text-decoration: underline; }
.vp-charts { display: flex; gap: 5px; flex-wrap: wrap; margin-top: 4px; }
.vp-charts a img { border-radius: 4px; border: 1px solid #dde0f0; transition: box-shadow .15s; }
.vp-charts a:hover img { box-shadow: 0 2px 8px rgba(8,10,102,.2); }
</style>
<script>
function reactualise() {
	document.getElementById('com2').value='';
	var contenue="";
	if (document.getElementById('leap1').checked == true) { contenue="leap_felicitation,"; }
	if (document.getElementById('leap2').checked == true) { contenue+="leap_encouragement,"; }
	if (document.getElementById('leap3').checked == true) { contenue+="leap_megcomp,"; }
	if (document.getElementById('leap4').checked == true) { contenue+="leap_megtrav,"; }
	document.getElementById('com2').value=contenue;
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

if (isset($_POST["validenoteviescolaire"])) { config_param_ajout($_POST["validenoteviescolaire"],"validenoteviescolaire"); }
if (isset($_POST["afficheNotePartielVatel"]))  { config_param_ajout($_POST["afficheNotePartielVatel"],"affNotePartielVatel"); }

$ok=1;
if (isset($_POST["supp"])) {
	$idclasse=$_POST["saisie_classe"];
	$sql="SELECT * FROM {$prefixe}eleves WHERE classe='$idclasse' ";
	$res=execSql($sql); $data=chargeMat($res);
	if (countTriade($data) <= 0) {
		if ($_POST["fichier_origin"]=="profpprojo") print "<script>location.href='profpprojo.php?info=1'</script>";
		else print "<script>location.href='video-proj-index.php?info=1'</script>";
	}
}

if (isset($_GET["apres"])) {
	$i=$_GET["apres"]; $trimes=$_GET["saisie_trimestre"]; $idclasse=$_GET["saisie_classe"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves, {$prefixe}classes WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
	$res=execSql($sql); $data_eleve=chargeMat($res);
	if ($i<=0) $i=0;
	$ideleve=$data_eleve[$i][1]; $ok=0; $iplus=$i+1; $imoins=$i-1;
}

if (isset($_POST["direct_eleve"])) {
	$idclasse=$_POST["saisie_classe"]; $trimes=$_POST["saisie_trimestre"]; $ok=0; $ideleve=$_POST["direct_eleve"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves, {$prefixe}classes WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
	$res=execSql($sql); $data_eleve=chargeMat($res);
	for ($j=0;$j<countTriade($data_eleve);$j++) { if ($ideleve==$data_eleve[$j][1]) { $i=$j; break; } }
	$iplus=$i+1; $imoins=$i-1;
}

if ($ok==1) {
	$idclasse=$_POST["saisie_classe"]; $trimes=$_POST["saisie_trimestre"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves, {$prefixe}classes WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
	$res=execSql($sql); $data_eleve=chargeMat($res);
	$ideleve=$data_eleve[0][1]; $i=0; $iplus=1; $imoins=-1;
}
?>

<!-- ── Barre navigation ── -->
<div class="vp-nav">

  <button type="button" class="btn btn-secondary btn-sm vp-btn-precedent"
    onclick="open('video-proj-affichage-UE.php?apres=<?php print $imoins?>&saisie_classe=<?php print $idclasse?>&saisie_trimestre=<?php print $trimes?>','video','');this.disabled=true">
    <i class="bi bi-chevron-left"></i> Précédent
  </button>

  <form method=post onsubmit="return valide_supp_choix('direct_eleve','un élève')" name="formulaire">
  <div class="vp-nav-center">
    <script language="JavaScript">buttonMagicImprimer();</script>
    <input type=hidden name="saisie_classe" value="<?php print $idclasse?>">
    <input type=hidden name="saisie_trimestre" value="<?php print $trimes?>">
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
    onclick="open('video-proj-affichage-UE.php?apres=<?php print $iplus?>&saisie_classe=<?php print $idclasse?>&saisie_trimestre=<?php print $trimes?>','video','');this.disabled=true">
    Suivant <i class="bi bi-chevron-right"></i>
  </button>
</div>

<!-- ── Layout principal ── -->
<div class="vp-main">

  <div class="vp-bulletin">
    <iframe src="video-proj-bulletin-UE.php?saisie_eleve=<?php print $ideleve?>&saisie_classe=<?php print $idclasse?>&saisie_trimestre=<?php print $trimes?>" scrolling="auto"></iframe>
  </div>

  <div class="vp-panel">

    <div class="vp-panel-eleve">
      <iframe src="video-proj-fiche-eleve.php?saisie_eleve=<?php print $ideleve?>&saisie_classe=<?php print $idclasse?>" scrolling="no"></iframe>
    </div>

    <div class="vp-panel-scol">

      <!-- Popup Visa Direction -->
      <div id="visdir" class="vp-popup">
        <?php $commentaireD=recherche_com($ideleve,$trimes,$type_bulletin); ?>
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
          <?php if (($type_bulletin=="montessori")||($type_bulletin=="montessori_spec")): ?>
          <?php
            $montessori=recherchemontessori($ideleve,$type_bulletin,$trimes);
            $montessori=$montessori[0][0];
            $c1=(trim($montessori)=="felicitation")?"checked='checked'":"";
            $c2=($montessori=="satisfaction")?"checked='checked'":"";
            $c3=($montessori=="encouragement")?"checked='checked'":"";
          ?>
          <div class="vp-popup-mention">
            <label><input type='radio' name='montessori' value=''> Aucun</label>
            <label><input name='montessori' type='radio' onclick="this.form.com2.value=this.value" value='felicitation' <?php print $c1?>> Félicitations</label>
            <label><input name='montessori' type='radio' onclick="this.form.com2.value=this.value" value='satisfaction' <?php print $c2?>> Satisfactions</label>
            <label><input name='montessori' type='radio' onclick="this.form.com2.value=this.value" value='encouragement' <?php print $c3?>> Encouragements</label>
          </div>
          <?php elseif ($type_bulletin=="leap"): ?>
          <?php
            $leap=rechercheleap($ideleve,$type_bulletin,$trimes);
            $c1=($leap[0][1]=="1")?"checked='checked'":""; $c2=($leap[0][2]=="1")?"checked='checked'":"";
            $c3=($leap[0][0]=="1")?"checked='checked'":""; $c4=($leap[0][3]=="1")?"checked='checked'":"";
          ?>
          <div class="vp-popup-mention">
            <label><input id='leap1' type='checkbox' onclick="reactualise()" value='1' <?php print $c1?>> Félicitations</label>
            <label><input id='leap2' type='checkbox' onclick="reactualise()" value='1' <?php print $c3?> title='Encouragement'> Encour.</label>
            <label><input id='leap3' type='checkbox' onclick="reactualise()" value='1' <?php print $c2?> title='MEG comportement'> MEG Comp.</label>
            <label><input id='leap4' type='checkbox' onclick="reactualise()" value='1' <?php print $c4?> title='MEG travail'> MEG Trav.</label>
          </div>
          <script>reactualise();</script>
          <?php elseif ($type_bulletin=="seminaire"): ?>
          <?php
            $montessori=recherchemontessori($ideleve,$type_bulletin,$trimes);
            $c1=($montessori=="felicitation")?"checked='checked'":""; $c2=($montessori=="tabhonneur")?"checked='checked'":"";
            $c3=($montessori=="encouragement")?"checked='checked'":""; $c4=($montessori=="deconduite")?"checked='checked'":"";
            $c5=($montessori=="detravail")?"checked='checked'":"";
          ?>
          <div class="vp-popup-mention">
            <label><input name='montessori' type='radio' onclick="this.form.com2.value=this.value" value=''> Aucun</label>
            <label><input name='montessori' type='radio' onclick="this.form.com2.value=this.value" value='felicitation' <?php print $c1?>> Félicitations</label>
            <label><input name='montessori' type='radio' onclick="this.form.com2.value=this.value" value='tabhonneur' <?php print $c2?>> Tab. d'Honneur</label>
            <label><input name='montessori' type='radio' onclick="this.form.com2.value=this.value" value='encouragement' <?php print $c3?>> Encouragements</label>
            <label><input name='montessori' type='radio' onclick="this.form.com2.value=this.value" value='deconduite' <?php print $c4?>> Avert. conduite</label>
            <label><input name='montessori' type='radio' onclick="this.form.com2.value=this.value" value='detravail' <?php print $c5?>> Avert. travail</label>
          </div>
          <?php endif; ?>
          <div class="vp-popup-footer">
            <?php if ($_SESSION["membre"]=="menuadmin"): ?>
            <button type="button" class="btn btn-primary btn-sm"
              onclick="enrVisaDir('<?php print $ideleve?>',this.form.com.value,'<?php print $trimes?>','retourenr0',this.form.com2.value,'<?php print $type_bulletin?>','')">
              <i class="bi bi-check2"></i> <?php print LANGENR ?>
            </button>
            <?php endif; ?>
            <button type="button" class="btn btn-secondary btn-sm" onclick="new Effect.Shrink('visdir',1)">
              <i class="bi bi-x-lg"></i> <?php print LANGFERMERFEN ?>
            </button>
            <span id='retourenr0' class="vp-retour"></span>
          </div>
        </form>
      </div>

      <!-- Popup Visa Professeur Principal -->
      <div id="visprofp" class="vp-popup">
        <?php $commentaireP=recherche_com_profP($ideleve,$trimes); ?>
        <form>
          <div class="vp-popup-header">
            <span><i class="bi bi-person-badge"></i> Visa Professeur Principal</span>
          </div>
          <div class="vp-popup-body">
            <textarea name='com' onkeypress="compter(this,'500',this.form.CharRestant_2)"><?php print $commentaireP ?></textarea>
            <div class="vp-popup-counter"><input type=text name='CharRestant_2' size=3 disabled='disabled' value='0'> car. restants</div>
            <input type='hidden' name='com21' id='com21' value=''>
          </div>
          <div class="vp-popup-footer">
            <?php if (($_SESSION["membre"]=="menuadmin")||(($_SESSION["membre"]=="menuprof")&&verif_profp_class2($_SESSION["id_pers"],$idclasse))): ?>
            <button type="button" class="btn btn-primary btn-sm"
              onclick="enrVisaProfp('<?php print $ideleve?>',this.form.com.value,'<?php print $trimes?>','retourenr1','','<?php print $type_bulletin?>','')">
              <i class="bi bi-check2"></i> <?php print LANGENR ?>
            </button>
            <?php endif; ?>
            <button type="button" class="btn btn-secondary btn-sm" onclick="new Effect.Shrink('visprofp',1)">
              <i class="bi bi-x-lg"></i> <?php print LANGFERMERFEN ?>
            </button>
            <span id='retourenr1' class="vp-retour"></span>
          </div>
        </form>
      </div>

      <iframe src="video-proj-eleve-scol.php?saisie_eleve=<?php print $ideleve?>&saisie_classe=<?php print $idclasse?>&trimestre=<?php print $trimes?>" scrolling="no"></iframe>
    </div>

    <!-- Actions -->
    <div class="vp-panel-actions">
      <p class="vp-section-title">Commentaires</p>
      <a href="#" class="vp-action-link" onclick="new Effect.Grow('visdir',1);return false;">
        <i class="bi bi-pencil-square"></i> Commentaire de la direction
      </a>
      <a href="#" class="vp-action-link" onclick="new Effect.Grow('visprofp',1);return false;">
        <i class="bi bi-person-badge"></i> Commentaire du professeur principal
      </a>
      <p class="vp-section-title" style="margin-top:8px">Graphiques</p>
      <div class="vp-charts">
        <a href="#" onclick="open('video_projo0.php?saisie_eleve=<?php print $ideleve?>&saisie_classe=<?php print $idclasse?>','_blank','resizable=yes,width=500,height=350');return false;">
          <img src="image/commun/graphDemo1.jpg" title="Graphique général" height="36">
        </a>
        <a href="#" onclick="open('video_projo1.php?saisie_eleve=<?php print $ideleve?>&saisie_classe=<?php print $idclasse?>&trimestre=<?php print $trimes?>','_blank','resizable=yes,width=950,height=250');return false;">
          <img src="image/commun/graphDemo2.jpg" title="Graphique évolution" height="36">
        </a>
        <a href="#" onclick="open('video_projo2.php?saisie_eleve=<?php print $ideleve?>&saisie_classe=<?php print $idclasse?>&trimestre=<?php print $trimes?>','_blank','resizable=yes,width=530,height=550');return false;">
          <img src="image/commun/graphDemo3.jpg" title="Graphique radar" height="36">
        </a>
      </div>
    </div>

  </div>
</div>

<?php Pgclose(); ?>
</body>
</html>
