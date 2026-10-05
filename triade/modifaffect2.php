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
<script language="JavaScript" src="./librairie_js/lib_affectation.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Affectations <?php print $_SESSION["nom"]." ".$_SESSION["prenom"]?></title>
<style>
.ma-input{width:100%;padding:3px 5px;border:1px solid #c8cfe8;border-radius:3px;font-size:11px;font-family:Electrolize,'Trebuchet MS',Arial;box-sizing:border-box}
.ma-input-sm{width:46px;padding:3px 4px;border:1px solid #c8cfe8;border-radius:3px;font-size:11px;font-family:Electrolize,'Trebuchet MS',Arial;text-align:center}
.ma-select{padding:3px 4px;border:1px solid #c8cfe8;border-radius:3px;font-size:11px;font-family:Electrolize,'Trebuchet MS',Arial;max-width:160px}
.ma-cb{accent-color:#080A66;width:15px;height:15px;cursor:pointer}
.ma-table{width:100%;border-collapse:collapse;font-size:11px;font-family:Electrolize,'Trebuchet MS',Arial}
.ma-table th.cc-th{padding:6px 7px;white-space:nowrap;font-size:10px}
.ma-table td{padding:4px 6px;border-bottom:1px solid #eef0fa;vertical-align:middle}
.ma-table tr:last-child td{border-bottom:none}
.ma-table tr:hover td{background:#f5f7ff}
.ma-note{font-size:10px;color:#666;font-style:italic;margin-top:8px;line-height:1.5}
</style>
</head>
<body id='bodyfond2'>
<?php
include("librairie_php/lib_licence.php");
if (empty($_SESSION["adminplus"])) {
    print "<script>location.href='./base_de_donne_key.php'</script>";
    exit;
}
include_once('librairie_php/db_triade.php');

$cnx=cnx();
$cid=$_GET["saisie_classe_envoi"];
$idclasse=$_GET["saisie_classe_envoi"];
$anneeScolaire=$_GET["anneeScolaire"];
$dataClasse=chercheClasse($_GET["saisie_classe_envoi"]);
$nom_classe=$dataClasse[0][1];
$matGroup=matGroup($nom_classe,$anneeScolaire);
$tri=$_GET["saisie_tri"];
$semestre=preg_replace('/trimestre/','',$tri);

$sql=<<<SQL
SELECT code_mat, libelle, sous_matiere
FROM {$prefixe}matieres
WHERE offline='0'
ORDER BY libelle
SQL;
$cursor=execSql($sql);
$data=chargeMat($cursor);
freeResult($cursor);
for ($l=0; $l<countTriade($data); $l++) {
    for ($c=0; $c<countTriade($data); $c++) {
        $bool = empty($data[$l][2]) ? 0 : true;
        $matMat[$l][0]=$data[$l][0].":".$bool;
        $matMat[$l][1]=trim($data[$l][1])." ".trim($data[$l][2]);
    }
}

$ajoutligne=0;
if (isset($_GET["ligne"])) $ajoutligne=1;

$readonly="";
$readonlycheckbox="";
$disabledENS="";
if ($_SESSION["membre"] == "menuprof") {
    $readonly="readonly='readonly'";
    $readonlycheckbox="onclick='return false;' onkeydown='return false;'";
    $disabledENS="disabled='disabled'";
}
?>

<form method="post" onsubmit="return valide();" action="modifaffect3.php" name="formulaire">
<input type="hidden" name="anneeScolaire" value="<?php print $anneeScolaire ?>">

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'>
  <td height="2">
    <b><font id='menumodule1'>
      <i class="bi bi-person-lines-fill" style="margin-right:6px"></i><?php print LANGTITRE20?> — <?php print $nom_classe?> &nbsp;<span style="font-size:11px;font-weight:400">(<?php print $anneeScolaire ?>)</span>
    </font></b>
  </td>
</tr>
<tr id='cadreCentral0'>
<td style="padding:8px">

<!-- ── Tableau affectations ───────────────────────────────────────────────── -->
<div style="overflow-x:auto;border-radius:6px;border:1px solid #dde0f0">
<table class="ma-table">
  <thead>
  <tr>
    <th class="cc-th"><i class="bi bi-book"></i> <?php print LANGPER16?></th>
    <th class="cc-th"><i class="bi bi-person-badge"></i> <?php print LANGPER17?></th>
    <th class="cc-th"><?php print LANGPER18?></th>
    <th class="cc-th"><?php print LANGPER19?></th>
    <th class="cc-th"><?php print LANGPER20?></th>
    <th class="cc-th"><?php print LANGPER21?></th>
    <th class="cc-th" title="Visualiser au sein du bulletin">Visu.<i>*</i></th>
    <th class="cc-th" title="Visualiser au sein du bulletin BTS Blanc">Visu.&nbsp;BTS<i>***</i></th>
    <th class="cc-th" title="Nombre d'heures annuelles">H.<i>**</i></th>
    <th class="cc-th">ECTS</th>
    <th class="cc-th">UE</th>
    <th class="cc-th">Spécif</th>
    <th class="cc-th">Sem.</th>
    <th class="cc-th">Coef&nbsp;Cert.</th>
    <th class="cc-th">Plancher</th>
  </tr>
  </thead>
  <tbody>
<?php
if (!empty($_SESSION["adminplus"])):
    $data=visu_affectation_detail_2($_GET["saisie_classe_envoi"],$_GET["saisie_tri"],$anneeScolaire);
    for ($a=0; $a<countTriade($data); $a++):
        $nomMatiere=ucwords(chercheMatiereNom($data[$a][1]));
        $suite     = ($data[$a][7] == TRUE) ? 1 : 0;
        $idMatiere = $data[$a][1].":".$suite;
        $nomProf   = recherche_personne($data[$a][2]);
        $idProf    = $data[$a][2];
        $coef      = $data[$a][4];
        $nbheure   = $data[$a][9];
        $ects      = $data[$a][10];
        $ue        = $data[$a][11];
        $specif    = $data[$a][12];
        $visubullbtsblanc = $data[$a][14];
        $info_semestre    = $data[$a][15];
        $coef_certif      = $data[$a][17];
        $note_planche     = $data[$a][18];
        $trim      = $data[$a][16];

        $nomGrp = trim($data[$a][5]);
        $idGrp  = chercheGroupeId($data[$a][5]);
        if (trim($nomGrp) == "") { $idGrp="0"; $nomGrp="Choix"; }

        $idLang  = $data[$a][6];
        $nomLang = $data[$a][6];
        if (trim($nomLang) == "") { $idLang=""; $nomLang="Choix"; }

        $checkedvisu        = ($data[$a][8] == 1)         ? "checked='checked'" : "";
        $checkedvisubtsblanc= ($visubullbtsblanc == 1)    ? "checked='checked'" : "";
?>
  <tr>
    <td style="text-align:center;color:#888">
      <input type="text" name="ordre" value="<?php print $a?>" readonly size="2"
             class="ma-input-sm" style="width:34px;background:#f5f7ff;color:#888" onfocus="this.blur()">
    </td>
    <td>
      <?php $nameSelectMat="saisie_matiere_".$a;
            print(selectHtml2($nameSelectMat,1,false,$matMat,$nomMatiere,$idMatiere,"1","40",$disabledENS)); ?>
    </td>
    <td>
      <select name="saisie_prof_<?php print $a?>" class="ma-select">
        <?php $nomProfTitle=$nomProf; $nomProf=trunchaine($nomProf,40); ?>
        <option value="<?php print $idProf?>" title="<?php print $nomProfTitle?>"><?php print $nomProf?></option>
        <?php select_personne_2('ENS',40); ?>
      </select>
    </td>
    <td><input type="text" value="<?php print $coef?>" class="ma-input-sm"
               name="saisie_coef_<?php print $a?>" <?php print $readonly ?>></td>
    <td>
      <?php $nameSelectGrp="saisie_groupe_".$a;
            if (trim($nomGrp) != "") {
                print(selectHtml2($nameSelectGrp,1,false,$matGroup,$nomGrp,$idGrp,0,"40",$disabledENS));
            } elseif ($_SESSION["membre"] == "menuadmin") {
                print(selectHtml($nameSelectGrp,1,false,$matGroup));
            } ?>
    </td>
    <td>
      <select name="saisie_langue_<?php print $a?>" class="ma-select">
        <?php if ((trim($nomLang) != "") && (trim($nomLang) != "0")): ?>
          <option value="<?php print $idLang?>"><?php print $nomLang?></option>
        <?php else: ?>
          <option value="0"><?php print LANGCHOIX?></option>
        <?php endif; ?>
        <?php if ($_SESSION["membre"] == "menuadmin"): ?>
          <option value="0"></option>
          <option value="LV1">LV1</option><option value="LV2">LV2</option>
          <option value="LV3">LV3</option><option value="LV4">LV4</option>
          <option value="OPT1">OPT1</option><option value="OPT2">OPT2</option>
          <option value="OPT3">OPT3</option><option value="OPT4">OPT4</option>
          <option value="DP3">DP3</option>
        <?php endif; ?>
      </select>
    </td>
    <td style="text-align:center">
      <input type="checkbox" class="ma-cb" name="saisie_visubull_<?php print $a?>"
             value="1" <?php print $checkedvisu?> <?php print $readonlycheckbox?>>
    </td>
    <td style="text-align:center">
      <input type="checkbox" class="ma-cb" name="saisie_visubull_btsblanc_<?php print $a?>"
             value="1" <?php print $checkedvisubtsblanc?> <?php print $readonlycheckbox?>>
    </td>
    <td><input type="text" name="saisie_nbheure_<?php print $a?>" value="<?php print $nbheure?>"
               class="ma-input-sm" <?php print $readonly?>></td>
    <td><input type="text" name="saisie_ects_<?php print $a?>" value="<?php print $ects?>"
               class="ma-input-sm" <?php print $readonly?>></td>
    <td>
      <select name="ue_<?php print $a?>" class="ma-select">
        <?php if ($ue > 0):
            $tab=$ue ? recupNomUE($ue) : null;
            $nom_ue=$tab[0][0]; $ue=$tab[0][1];
            $sem=$tab[0][2]; if ($sem==0) $sem="1 et 2";
            print "<option value='$ue' title=\"$nom_ue\">".trunchaine($nom_ue,30)." (S:$sem)</option>";
        endif; ?>
        <?php if ($_SESSION["membre"] == "menuadmin"):
            print "<option value=''>".LANGCHOIX."</option>";
            $dataUE=vatel_liste_ueT($semestre,$idclasse,$anneeScolaire);
            for ($k=0; $k<countTriade($dataUE); $k++):
                if ($dataUE[$k][1] != ""):
                    $sem=$dataUE[$k][2]; if ($sem==0) $sem="1 et 2";
                    print "<option value='".$dataUE[$k][0]."' title=\"".$dataUE[$k][1]."\">".trunchaine($dataUE[$k][1],30)." (S:$sem)</option>";
                endif;
            endfor;
        endif; ?>
      </select>
    </td>
    <td>
      <select name="specif_<?php print $a?>" class="ma-select">
        <?php if ($_SESSION["membre"] == "menuprof"):
            if ($specif == "etudedecasipac"): ?>
              <option value="etudedecasipac">Etude de cas</option>
            <?php else: print "<option value=''></option>"; endif;
        else: ?>
          <option value=""></option>
          <option value="etudedecasipac" <?php if ($specif=="etudedecasipac") print "selected"; ?>>Etude de cas</option>
        <?php endif; ?>
      </select>
    </td>
    <td>
      <select name="info_semestre_<?php print $a?>" class="ma-select" style="max-width:54px">
        <?php if ((trim($info_semestre) != "") && (trim($info_semestre) > "0")): ?>
          <option value="<?php print $info_semestre?>"><?php print $info_semestre?></option>
        <?php endif; ?>
        <?php for ($s=0; $s<=10; $s++) print "<option value='$s'>".($s==0?'':$s)."</option>"; ?>
      </select>
    </td>
    <td><input type="text" size="2" name="saisie_coef_certif_<?php print $a?>"
               value="<?php print $coef_certif?>" class="ma-input-sm"></td>
    <td><input type="text" size="2" name="saisie_note_planche_<?php print $a?>"
               value="<?php print $note_planche?>" class="ma-input-sm"></td>
  </tr>
<?php
    endfor; // fin boucle existants

    // ── Ligne d'ajout ──────────────────────────────────────────────────────
    if ($ajoutligne == 1): $coef=""; ?>
  <tr style="background:#fffde7">
    <td style="text-align:center;color:#888">
      <input type="text" name="ordre" value="<?php print $a?>" readonly size="2"
             class="ma-input-sm" style="width:34px;background:#f5f7ff;color:#888" onfocus="this.blur()">
    </td>
    <td><?php $nameSelectMat="saisie_matiere_".$a; print(selectHtml($nameSelectMat,1,false,$matMat,40)); ?></td>
    <td>
      <select name="saisie_prof_<?php print $a?>" class="ma-select">
        <option value="0"><?php print LANGCHOIX?></option>
        <?php select_personne_2('ENS',40); ?>
      </select>
    </td>
    <td><input type="text" value="" class="ma-input-sm" name="saisie_coef_<?php print $a?>"></td>
    <td><?php $nameSelectGrp="saisie_groupe_".$a; print(selectHtml($nameSelectGrp,1,false,$matGroup)); ?></td>
    <td>
      <select name="saisie_langue_<?php print $a?>" class="ma-select">
        <option value="0"><?php print LANGCHOIX?></option>
        <option value="LV1">LV1</option><option value="LV2">LV2</option>
      </select>
    </td>
    <td style="text-align:center"><input type="checkbox" class="ma-cb" name="saisie_visubull_<?php print $a?>" value="1"></td>
    <td style="text-align:center"><input type="checkbox" class="ma-cb" name="saisie_visubull_btsblanc_<?php print $a?>" value="1"></td>
    <td><input type="text" name="saisie_nbheure_<?php print $a?>" value="" class="ma-input-sm"></td>
    <td><input type="text" name="saisie_ects_<?php print $a?>" value="" class="ma-input-sm"></td>
    <td>
      <select name="ue_<?php print $a?>" class="ma-select">
        <option value=""><?php print LANGCHOIX?></option>
        <?php $dataUE=vatel_liste_ueT($semestre,$idclasse,$anneeScolaire);
              for ($k=0; $k<countTriade($dataUE); $k++):
                  if ($dataUE[$k][1] != ""):
                      $sem=$dataUE[$k][2]; if ($sem==0) $sem="1 et 2";
                      print "<option value='".$dataUE[$k][0]."'>".trunchaine($dataUE[$k][1],30)."(S:$sem)</option>";
                  endif;
              endfor; ?>
      </select>
    </td>
    <td>
      <select name="specif_<?php print $a?>" class="ma-select">
        <option value=""></option>
        <option value="etudedecasipac">Etude de cas</option>
      </select>
    </td>
    <td>
      <select name="info_semestre_<?php print $a?>" class="ma-select" style="max-width:54px">
        <?php for ($s=0; $s<=10; $s++) print "<option value='$s'>".($s==0?'':$s)."</option>"; ?>
      </select>
    </td>
    <td><input type="text" size="2" name="saisie_coef_certif_<?php print $a?>" class="ma-input-sm"></td>
    <td><input type="text" size="2" name="saisie_note_planche_<?php print $a?>" class="ma-input-sm"></td>
  </tr>
    <?php $a++; endif; ?>
  </tbody>
</table>
</div>

<!-- ── Légende ───────────────────────────────────────────────────────────── -->
<p class="ma-note">
  <i>* Visu. : Visualiser au sein du bulletin &nbsp;|&nbsp;
     ** Nombre d'heures annuelles &nbsp;|&nbsp;
     *** Visu. : Visualiser au sein du bulletin AFTEC BTS BLANC</i>
</p>

<?php if ($_SESSION["membre"] == "menuadmin"): ?>
<!-- ── Options admin ─────────────────────────────────────────────────────── -->
<div class="card" style="margin-top:10px">
<div class="card-body" style="padding:10px 14px">
  <div style="display:flex;gap:16px;flex-wrap:wrap;align-items:center;font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial">
    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;color:#c62828">
      <input type="checkbox" class="ma-cb" name="suppnote" value="oui">
      <i class="bi bi-trash" style="color:#c62828"></i> Supprimer les notes scolaires de cette classe
    </label>
    <div style="display:flex;align-items:center;gap:8px">
      <label style="color:#444"><i class="bi bi-calendar3" style="color:#080A66;margin-right:4px"></i>Type d'affectation :</label>
      <select name="saisie_tri" class="cc-select" style="width:auto">
        <option value="trimestre1" <?php if ($trim=="trimestre1") print "selected"; ?>>Trimestre 1 / Semestre 1</option>
        <option value="trimestre2" <?php if ($trim=="trimestre2") print "selected"; ?>>Trimestre 2 / Semestre 2</option>
        <option value="trimestre3" <?php if ($trim=="trimestre3") print "selected"; ?>>Trimestre 3</option>
        <option value="tous"       <?php if ($trim=="tous")       print "selected"; ?>>Toute l'année</option>
      </select>
    </div>
  </div>
</div>
</div>
<?php endif; ?>

<!-- ── Boutons ───────────────────────────────────────────────────────────── -->
<div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-top:12px;padding-bottom:4px">
  <input type="hidden" name="saisie_classe_envoi" value="<?php print $_GET["saisie_classe_envoi"]?>">
  <input type="hidden" name="saisie_nb_matiere"   value="<?php print $a-1?>">
  <script language=JavaScript>buttonMagic("<?php print LANGBT20?>","./modifaffect.php","_parent","",";parent.window.close();");</script>
  <script language=JavaScript>buttonMagicSubmit("Enregistrer","rien");</script>
  <?php if ($_SESSION["membre"] == "menuadmin"): ?>
  <script language=JavaScript>buttonMagic("Ajouter une ligne","modifaffect2.php?ligne=1&saisie_classe_envoi=<?php print $cid?>&saisie_tri=<?php print $_GET["saisie_tri"]?>&anneeScolaire=<?php print $anneeScolaire?>","_self","","");</script>
  <?php endif; ?>
</div>

</td></tr></table>
</form>

<script language="JavaScript">
function valide() {
    var nbmatiere = <?php print $a?>;
    var form = document.formulaire;
    for (var n=0; n<nbmatiere; n++) {
        var selMat = form.elements['saisie_matiere_' + n];
        if (!selMat || selMat.value == '0' || selMat.value == '') {
            window.alert("Indiquez une matière S.V.P (ligne " + (n+1) + ")");
            if (selMat) selMat.focus();
            return false;
        }
        var selProf = form.elements['saisie_prof_' + n];
        if (!selProf || selProf.value == '0' || selProf.value == '') {
            window.alert("Indiquez un enseignant S.V.P (ligne " + (n+1) + ")");
            if (selProf) selProf.focus();
            return false;
        }
        var inCoef = form.elements['saisie_coef_' + n];
        if (inCoef && (inCoef.value.trim() === '' || isNaN(inCoef.value))) {
            inCoef.focus();
            window.alert("Indiquez le coef de la matière (ligne " + (n+1) + ")");
            return false;
        }
    }
    return true;
}
</script>
<?php endif; ?>
</BODY></HTML>
