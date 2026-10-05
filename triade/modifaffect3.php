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
<LINK TYPE="text/css" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TYPE="text/css" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css">
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Affectations <?php print $_SESSION["nom"]." ".$_SESSION["prenom"]?></title>
<style>
.ma3-table{width:100%;border-collapse:collapse;font-size:11px;font-family:Electrolize,'Trebuchet MS',Arial}
.ma3-table th.cc-th{padding:6px 8px;white-space:nowrap;font-size:10px}
.ma3-table td{padding:5px 8px;border-bottom:1px solid #eef0fa;vertical-align:middle;white-space:nowrap}
.ma3-table tr.tabnormal td{background:#fff}
.ma3-table tr.tabover td{background:#f5f7ff}
.ma3-table tr:last-child td{border-bottom:none}
.ma3-wrap{padding:10px 12px}
.ma3-actions{display:flex;gap:8px;align-items:center;margin-top:10px;flex-wrap:wrap}
</style>
</head>
<body id='bodyfond2'>
<?php
include("./librairie_php/lib_licence.php");
if (empty($_SESSION["adminplus"])) {
    print "<script>location.href='./base_de_donne_key.php'</script>";
    exit;
}
include_once('librairie_php/db_triade.php');
$cnx=cnx();
$cdata=chercheClasse($_POST["saisie_classe_envoi"]);
$cid=$cdata[0][0];
$cnom=trim($cdata[0][1]);
$tri=$_POST["saisie_tri"];
$anneeScolaire=$_POST["anneeScolaire"];

$okenr=createAffectation($cdata);
if ($okenr) {
    if ($_POST["suppnote"] == "oui") {
        vide_notes_classe($_POST["saisie_classe_envoi"],$_POST["anneeScolaire"]);
        history_cmd($_SESSION["nom"],"MODIF","Affectation - note supprime - $cnom - $anneeScolaire ");
    } else {
        history_cmd($_SESSION["nom"],"MODIF","Affectation - note non supprime - $cnom - $anneeScolaire ");
    }
}

$sql=<<<SQL
SELECT
    CONCAT(trim(m.libelle),' ',trim(m.sous_matiere)),
    CONCAT(upper(trim(p.nom)),' ',trim(p.prenom)),
    a.coef,
    trim(g.libelle),
    langue,
    visubull,
    visubullbtsblanc,
    nb_heure,
    ects,
    id_ue_detail,
    specif_etat,
    num_semestre_info,
    coef_certif,
    note_planche
FROM
    {$prefixe}matieres m,{$prefixe}personnel p,{$prefixe}affectations a,{$prefixe}groupes g
WHERE
    a.code_matiere = m.code_mat
AND a.code_prof = p.pers_id
AND a.code_groupe = g.group_id
AND p.type_pers = 'ENS'
AND a.code_classe = '$cid'
AND a.trim = '$tri'
AND a.annee_scolaire = '$anneeScolaire'
ORDER BY
    a.ordre_affichage
SQL;
$curs=execSql($sql);
$data=chargeMat($curs);
freeResult($curs);
?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr id='coulBar0' bgcolor="#0B3A0C">
  <td height="28" style="padding:4px 10px">
    <b><font id='menumodule1'>
      <i class="bi bi-person-lines-fill" style="margin-right:6px"></i><?php print LANGTITRE20?>
      &nbsp;&mdash;&nbsp;<span style="color:#99CCFF"><?php print $cnom?></span>
      &nbsp;&middot;&nbsp;<?php print LANGBULL3 ?> : <span style="color:#99CCFF"><?php print $anneeScolaire?></span>
    </font></b>
  </td>
</tr>
<tr id='cadreCentral0'>
<td>
<div class="ma3-wrap">

<!-- ── Tableau affectations ────────────────────────────────────────────────── -->
<div style="overflow-x:auto;border-radius:6px;border:1px solid #dde0f0">
<table class="ma3-table">
  <thead>
  <tr>
    <th class="cc-th"><i class="bi bi-book"></i> <?php print LANGPER17?></th>
    <th class="cc-th"><i class="bi bi-person-badge"></i> <?php print LANGPER18?></th>
    <th class="cc-th"><?php print LANGPER19?></th>
    <th class="cc-th"><?php print LANGPER20?></th>
    <th class="cc-th"><?php print LANGPER21?></th>
    <th class="cc-th" title="Visualiser au sein du bulletin">Visu.</th>
    <th class="cc-th" title="Visualiser au sein du bulletin BTS Blanc">Visu.&nbsp;BTS</th>
    <th class="cc-th">Nb&nbsp;h.</th>
    <th class="cc-th">ECTS</th>
    <th class="cc-th">UE</th>
    <th class="cc-th">Spécif.</th>
    <th class="cc-th">Sem.</th>
    <th class="cc-th">Coef&nbsp;Cert.</th>
    <th class="cc-th">Plancher</th>
  </tr>
  </thead>
  <tbody>
  <?php htmlTrMatAffec($data); ?>
  </tbody>
</table>
</div>

<div class="ma3-actions">
  <button type="button" class="btn-enr" onclick="parent.window.close()" style="background:#6c757d;border-color:#6c757d">
    <i class="bi bi-x-circle"></i> <?php print LANGFERMERFEN?>
  </button>
  <button type="button" class="btn-enr" onclick="window.print()" style="background:#080A66;border-color:#080A66">
    <i class="bi bi-printer"></i> <?php print LANGPER22?>
  </button>
</div>

</div>
</td></tr></table>

<?php Pgclose(); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
<?php if ($okenr): ?>
    alertify.success(<?php echo json_encode(LANGPER32." $cnom ".LANGPER23bis); ?>);
<?php else: ?>
    alertify.error(<?php echo json_encode(LANGPER32." $cnom ".LANGPER32bis); ?>);
<?php endif; ?>
});
</script>
</BODY>
</HTML>
