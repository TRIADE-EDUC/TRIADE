<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta charset="utf-8">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="librairie_css/css-v4.css">
<link rel="stylesheet" href="librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert=function(msg){alertify.error(msg);};</script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Notation B2I / A2</title>
<style>
.b2i-table { width:100%; border-collapse:collapse; font-size:12px; }
.b2i-table th { background:#f0f2fa; color:#333; font-weight:600; padding:6px 10px; border-bottom:2px solid #c5cae9; text-align:center; }
.b2i-table th:first-child { text-align:left; }
.b2i-table td { padding:5px 10px; border-bottom:1px solid #e8eaf6; vertical-align:middle; }
.b2i-table tr:hover td { background:#f5f7ff; }
.b2i-opt { text-align:center; white-space:nowrap; }
.b2i-opt.b2i-sel { background:#e8eaf6; font-weight:700; }
.b2i-opt label { display:inline-flex; align-items:center; gap:4px; cursor:pointer; padding:2px 6px; border-radius:4px; }
.b2i-opt input[type=radio] { accent-color:#080A66; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();

$titre = "Notation B2I";
if ($_POST["type_notation"] == "A2")  $titre = "Notation niveau A2 de langue";
if ($_POST["type_notation"] == "A2R") $titre = "Notation niveau A2 de langue régionale";
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="top" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr id='coulBar0' bgcolor="#0B3A0C"><td height="28" style="padding:4px 10px">
  <b><font id='menumodule1'><i class="bi bi-patch-check-fill" style="margin-right:6px"></i><?php print $titre ?></font></b>
</td></tr>
<tr id='cadreCentral0'><td style="padding:12px">

<?php
if (isset($_POST["consult"])) {
    $saisie_classe=$_POST["saisie_classe"];
    $sql="(SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves ,{$prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' AND annee_scolaire='$anneeScolaire') UNION (SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM {$prefixe}eleves e ,{$prefixe}classes c, {$prefixe}eleves_histo h WHERE h.idclasse='$saisie_classe' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire'  ORDER BY e.nom)";
    $res=execSql($sql);
    $data=chargeMat($res);

    $cl=$data[0][0];
    $tri=$_POST["saisie_trimestre"];

    print "<div style='font-size:12px;margin-bottom:10px;color:#333'>";
    print "<i class='bi bi-people-fill' style='color:#080A66;margin-right:5px'></i>";
    print "Classe : <b>".htmlspecialchars($cl)."</b>";
    print "</div>";

    print "<form method='post' name='formulaire' action='gestion_examen_b2i_3.php'>";
    print "<table class='b2i-table'>";
    print "<thead><tr><th>Élève</th>";
    if ($_POST["type_notation"] == "B2I" || $_POST["type_notation"] == "A2") {
        print "<th>MS</th><th>ME</th><th>MN</th>";
    }
    if ($_POST["type_notation"] == "A2R") {
        print "<th>AB</th><th>VA</th><th>NV</th>";
    }
    print "</tr></thead><tbody>";

    if (countTriade($data) > 0) {
        for($i=0;$i<countTriade($data);$i++) {
            $ideleve=$data[$i][1];
            $photoeleve="image_trombi.php?idE=".$ideleve;
            $b2ival=rechercheB2IEleve($ideleve,$saisie_classe,$_POST["type_notation"]);

            $sel_MS=$sel_ME=$sel_MN=$sel_AB=$sel_VA=$sel_NV="";
            $cls_MS=$cls_ME=$cls_MN=$cls_AB=$cls_VA=$cls_NV="b2i-opt";
            if ($b2ival == "MS") { $sel_MS="checked='checked'"; $cls_MS="b2i-opt b2i-sel"; }
            if ($b2ival == "ME") { $sel_ME="checked='checked'"; $cls_ME="b2i-opt b2i-sel"; }
            if ($b2ival == "MN") { $sel_MN="checked='checked'"; $cls_MN="b2i-opt b2i-sel"; }
            if ($b2ival == "AB") { $sel_AB="checked='checked'"; $cls_AB="b2i-opt b2i-sel"; }
            if ($b2ival == "VA") { $sel_VA="checked='checked'"; $cls_VA="b2i-opt b2i-sel"; }
            if ($b2ival == "NV") { $sel_NV="checked='checked'"; $cls_NV="b2i-opt b2i-sel"; }

            print "<tr>";
            print "<td><input type='hidden' value='".$data[$i][1]."' name='eleveid[]'>";
            print "<a href='#' onMouseOver=\"AffBulle('<img src=\\'".$photoeleve."\\' >');\" onMouseOut='HideBulle()' style='font-size:12px;color:#080A66'>".ucfirst($data[$i][3])." ".strtoupper($data[$i][2])."</a></td>";

            if ($_POST["type_notation"] == "B2I" || $_POST["type_notation"] == "A2") {
                print "<td class='$cls_MS'><label><input type='radio' name='b2I_${ideleve}' value='MS' $sel_MS> MS</label></td>";
                print "<td class='$cls_ME'><label><input type='radio' name='b2I_${ideleve}' value='ME' $sel_ME> ME</label></td>";
                print "<td class='$cls_MN'><label><input type='radio' name='b2I_${ideleve}' value='MN' $sel_MN> MN</label></td>";
            }
            if ($_POST["type_notation"] == "A2R") {
                print "<td class='$cls_AB'><label><input type='radio' name='b2I_${ideleve}' value='AB' $sel_AB> AB</label></td>";
                print "<td class='$cls_VA'><label><input type='radio' name='b2I_${ideleve}' value='VA' $sel_VA> VA</label></td>";
                print "<td class='$cls_NV'><label><input type='radio' name='b2I_${ideleve}' value='NV' $sel_NV> NV</label></td>";
            }
            print "</tr>";
        }

        $valider=VALIDER;
        print "</tbody></table>";
        print "<div style='margin-top:12px;display:flex;align-items:center;gap:10px;justify-content:center'>";
        print "<script language='JavaScript'>buttonMagicSubmit('$valider','create');</script>";
        include_once("./librairie_php/lib_conexpersistant.php");
        connexpersistance();
        print "</div>";
        print "<input type='hidden' name='saisie_classe' value='".$_POST["saisie_classe"]."'>";
        print "<input type='hidden' name='saisie_nb' value='".countTriade($data)."'>";
        print "<input type='hidden' name='type_notation' value='".$_POST["type_notation"]."'>";
        print "</form>";

    } else {
        print "</tbody></table>";
        print "<div class='alert alert-warning' style='margin-top:10px'>".LANGRECH1."</div>";
    }
}
?>

<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>

</td></tr>
</table>
</div>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<SCRIPT language="JavaScript">InitBulle("#FFFFFF","#009999","#FFFFFF",1);</SCRIPT>
<?php Pgclose(); ?>
</BODY>
</HTML>
