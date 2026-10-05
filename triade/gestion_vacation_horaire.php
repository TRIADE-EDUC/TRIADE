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
<html>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<style>
#coulBar0 { background-image: none; }
.vh-input { width:68px; padding:4px 6px; border:1px solid #c5cae9; border-radius:5px; font-size:12px; text-align:center; color:#080A66; font-weight:600; }
.vh-input:focus { border-color:#080A66; outline:none; }
</style>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_proto.js"></script>
<script>window.alert = function(msg) { alertify.error(msg); };</script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
if ($_SESSION["membre"] == "menupersonnel") {
    if ((!verifDroit($_SESSION["id_pers"],"edt")) && (!verifDroit($_SESSION["id_pers"],"AESH"))) {
        accesNonReserveFen();
        exit;
    }
}else{
    validerequete("2");
}
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Ajustement des horaires de prestation</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<div style="padding:12px 8px">

<?php
if (isset($_POST["modif"])) {
}
?>

<form method="post">

<!-- toolbar -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
  <span style="font-size:12px;font-weight:700;color:#080A66">Liste des horaires de prestation</span>
  <a href='gestion_vacation_horaire.php' class="btn btn-secondary" style="padding:4px 12px;font-size:12px" title="Actualiser la liste">
    <i class="bi bi-arrow-clockwise"></i> Actualiser
  </a>
</div>

<table class="table table-hover" style="font-size:12px">
  <thead>
    <tr>
      <th>Date</th>
      <th>Classe</th>
      <th>Enseignant</th>
      <th style="text-align:center">Heure</th>
      <th style="text-align:center">Durée</th>
      <th style="text-align:center;color:#2e7d32">Correctif</th>
      <th style="text-align:center;color:#c62828">Supprimer</th>
    </tr>
  </thead>
  <tbody>
<?php
$data=listePrestaHoraire(); //id,code,enseignement,date,heure,duree,bgcolor,idclasse,idprof,prestation
for($i=0;$i<countTriade($data);$i++) {
    $id=$data[$i][0];
    $heure=timeForm($data[$i][4]);
    $duree=timeForm($data[$i][5]);
    $classe=chercheClasse_nom($data[$i][7]);
    $nomprenom=recherche_personne($data[$i][8]);
    $idprestation=$data[$i][9];
    $date=$data[$i][3];

    $heurenew=$heure;
    $dureenew=$duree;

    list($H,$M)=preg_split('/:/',$heure);
    if ($M <= 10) { $M="00"; }
    if (($M >= 20) && ($M <= 40)) { $M="30"; }
    if ($M >= 50) { $M="00"; $H++; }
    $heurenew="$H:$M";

    list($H,$M)=preg_split('/:/',$duree);
    if ($M <= 10) { $M="00"; }
    if (($M >= 20) && ($M <= 40)) { $M="30"; }
    if ($M >= 50) { $M="00"; $H++; }
    if ($H < 10) $H="0$H";
    $dureenew="$H:$M";

    print "<tr>";
    print "<td style='white-space:nowrap'>".dateForm($date)."</td>";
    print "<td>".ucwords($classe)."</td>";
    print "<td><div id='el$i'>$affiche &nbsp; $nomprenom</div></td>";
    print "<td style='text-align:center'><input type='text' class='vh-input' value='$heure' onchange=\"AjuteEDTHoraire('$id',this.value,'','el$i','5')\"></td>";
    print "<td style='text-align:center'><input type='text' class='vh-input' value='$duree' onchange=\"AjuteEDTHoraire('$id','',this.value,'el$i','5')\"></td>";
    print "<td style='text-align:center'><a href='#' title='Appliquer le correctif arrondi'
        class='badge badge-success'
        style='cursor:pointer;padding:4px 8px;font-size:11px'
        onClick=\"AjuteEDTHoraire('$id','$heurenew','','el$i','5');
                  AjuteEDTHoraire('$id','','$dureenew','el$i','5')\">$heurenew &mdash; $dureenew</a></td>";
    print "<td style='text-align:center'><input type='checkbox' onClick=\"AjuteEDTHoraire('$id','','','el$i','5')\"></td>";
    print "</tr>";
}
?>
  </tbody>
</table>

</form>

<div style="margin-top:10px;padding-left:4px">
  <script language="JavaScript">buttonMagicRetour("edt.php","_self")</script>
</div>

</div>

<?php brmozilla($_SESSION["navigateur"]); ?>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY>
</HTML>
