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
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_proto.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx = cnx();

if ($_POST["idprof"] > 0) {
    $idprof        = $_POST["idprof"];
    $dateDebut     = $_POST["saisie_date_debut"];
    $dateFin       = $_POST["saisie_date_fin"];
    $infopaiement  = stripslashes($_POST["infopaiement"]);
    $montantHT     = $_POST["montantHT"];
    $montantTTC    = $_POST["montantTTC"];
    $idpiecejointe = $_POST["idpiecejointe"];
    $tva           = $_POST["tva"];
    $nomprenom     = recherche_personne($idprof);
    paiementVacation($idprof,$dateDebut,$dateFin,$infopaiement,$montantHT,$montantTTC,$tva,$idpiecejointe);
    history_cmd($_SESSION["nom"],"PAIEMENT","Effectué à $nomprenom");
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?>></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Paiement vacation enseignant</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<?php if ($_POST["idprof"] > 0): ?>
<div class="alert alert-success" style="margin:14px 16px">
  <i class="bi bi-check-circle"></i> Paiement pour <b><?php print $nomprenom ?></b> effectué.
</div>
<?php endif; ?>

<div class="toolbar" style="padding:10px 16px">
  <script type="text/javascript">buttonMagicRetour("gestion_vacation.php","_parent")</script>
</div>

</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?>></SCRIPT>
<?php Pgclose(); ?>
</BODY></HTML>
