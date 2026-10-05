<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
 *   Site                 : http://www.triade-educ.com
 ***************************************************************************/

include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/notes.inc.php");
include_once("./common/config2.inc.php");

if ((VIESCOLAIRENOTEENSEIGNANT == "oui") && ($_SESSION["membre"] != "menupersonnel"))  {
	validerequete("3");
}else{
	if (($_SESSION["membre"] != "menuadmin") && ($_SESSION["membre"] != "menuprof")) {
		$cnx=cnx();
		if ((!verifDroit($_SESSION["id_pers"],"carnetnotes"))  && (!verifDroit($_SESSION["id_pers"],"AESH")) )  {
			accesNonReserveFen();
			exit();
		}
		Pgclose();
	}else{
		validerequete("profadmin");
	}
}

$cnx=cnx();

$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);

$eleves       = $_POST["elev_id"];
$dates        = $_POST["iDate"];
$mid          = $_POST["mid"];
$coefs        = $_POST["iCoef"];
$notes        = $_POST["iNotes"];
$sujets       = $_POST["iSujet"];
$noms         = $_POST["elev_nom"];
$noteusa      = $_POST["NoteUsa"];
$noteExam     = $_POST["NoteExam"];
$notationSur  = $_POST["NotationSur"];
$adminIdprof  = $_POST["adminIdprof"];
$notevisiblele= $_POST["notevisiblele"];

if ($_POST["NoteUsa"] == "oui") {
	$notetype="Notation en mode USA";
}else{
	$notetype="Notation sur $notationSur";
}

if($_POST["gid"]):
	$verif=verifProfDansGroupe($_SESSION["id_pers"],$_POST["gid"]);
	if (($_SESSION["membre"] == "menuprof") && (!isset($_SESSION["profpclasse"]))) {
		if ($verif) { header("Location:noteajout.php");exit; }
	}
	$who  = "<span style='color:#fff'>- ".LANGPROF4." :</span> ".trunchaine(chercheGroupeNom($_POST["gid"]),10)." <span style='color:#fff'>-</span> $notetype";
	$who2 = chercheGroupeNom($_POST["gid"])."- $notetype";
	$idgrp=$_POST["gid"];
	$idcl="-1";
else:
	$idcl=$_POST["cid"];
	$idgrp="NULL";
	$cl=chercheClasse($_POST["cid"]);
	$verif=verifProfDansClasse($_SESSION["id_pers"],$cl[0][0]);
	if (($_SESSION["membre"] == "menuprof") && (!isset($_SESSION["profpclasse"]))) {
		if ($verif) { header("Location:noteajout.php");exit; }
	}
	$who2 = $cl[0][1]."- $notetype";
	$who  = "<span style='color:#fff'>- ".LANGIMP10." :</span> ".trunchaine($cl[0][1],10)." <span style='color:#fff'>-</span> $notetype";
	unset($cl);
endif;
?>
<HTML>
<HEAD>
<title>Triade - Compte de <?php print ucwords($mySession['Sp'])." ".strtoupper($mySession['Sn'])?></title>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<style>
.n3-card         { background:#fff; border:1px solid #c5cae9; border-radius:8px; padding:14px 16px; margin:8px 0; }
.n3-actions      { display:flex; gap:10px; flex-wrap:wrap; margin-top:14px; }
.n3-btn          { padding:6px 16px; font-size:12px; font-weight:600; border:none; border-radius:4px;
                   cursor:pointer; background:#080A66; color:#fff; text-decoration:none; }
.n3-btn:hover    { background:#0a0db0; }
/* ── styles pour la nouvelle version de affHtml() ── */
.n3-result-block { margin-bottom:18px !important; }
.n3-subject      { font-size:13px !important; font-weight:700 !important; color:#080A66 !important;
                   margin-bottom:8px !important; padding-bottom:6px !important;
                   border-bottom:2px solid #080A66 !important; display:block !important; }
.n3-table        { width:100% !important; border-collapse:collapse !important;
                   font-size:12px !important; margin-bottom:6px !important; }
.n3-table th     { background:#080A66 !important; color:#fff !important; padding:5px 8px !important;
                   text-align:left !important; font-weight:600 !important; border:none !important; }
.n3-table td     { padding:4px 8px !important; border-bottom:1px solid #e8eaf6 !important;
                   background:#fff !important; color:#333 !important; border-top:none !important; }
.n3-table tr:nth-child(even) td { background:#f5f5ff !important; }
.n3-moy-row td   { background:#d4edda !important; font-weight:bold !important; color:#1a6e34 !important; }
.n3-special      { color:#e65100 !important; font-style:italic !important; }
.n3-stat-link    { margin-top:6px !important; font-size:12px !important; }
.n3-stat-link a  { color:#080A66 !important; font-weight:600 !important; }
/* ── styles pour l'ancienne version de affHtml() (OPcache) ── */
.n3-card ul      { list-style:none !important; padding:0 !important; margin:0 0 16px !important; }
.n3-card table   { width:100% !important; border-collapse:collapse !important;
                   border:none !important; font-size:12px !important; margin:8px 0 !important; }
.n3-card table td { padding:5px 10px !important; border-bottom:1px solid #e8eaf6 !important;
                    background:#fff !important; color:#333 !important; }
.n3-card table tr:nth-child(even) td { background:#f5f5ff !important; }
.n3-card table tr:last-child td      { background:#d4edda !important; font-weight:bold !important; color:#1a6e34 !important; }
</style>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"] ?>.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"] ?>1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGPROF14 ?> </b><font id='color2'><?php print trunchaine(chercheMatiereNom($mid),25)." ".$who?></font></font></td></tr>
<tr id='cadreCentral0'><td>

<div class="n3-card">
<?php
for($i=0;$i<count($sujets);$i++){
	for($j=0;$j<count($eleves);$j++){
		$Notes[$i][$j]= new Note($noms[$j],$eleves[$j],$notes[$j][$i]);
	}
	$typenote = ($noteusa == "oui") ? "en" : "fr";

	$sujets[$i]=preg_replace('/\+/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/\?/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/\//',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/&/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/%/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/µ/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/\^/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/\(/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/\)/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/"/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace("/'/",' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/\$/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/£/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/:/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/=/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/\*/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/¨/',' ',$sujets[$i]);
	$sujets[$i]=preg_replace('/;/',' ',$sujets[$i]);

	$idprof=$mySession['Spid'];
	if ($adminIdprof != "") { $idprof=$adminIdprof; }

	$listeNotes[$i]=new ListeNotes($i,$idprof,$mid,$coefs[$i],$dates[$i],$sujets[$i],$Notes[$i],$idcl,$idgrp,$typenote,$noteExam,$notationSur,$notevisiblele);
	$listeNotes[$i]->persist();
	$listeNotes[$i]->affHtml();
}

$mid=chercheMatiereNom($mid);
history_cmd($mySession['Sn'],"AJOUT","Notes - $who2 - $mid");

?>

<div class="n3-actions">
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menupersonnel")) {
	print "<form method='post' action='notevisuadmin.php' target='_parent'>";
	print "<input type='hidden' name='saisie_pers' value='".$_POST["adminIdprof"]."' />";
	print "<button type='submit' class='n3-btn'>Retour menu principal</button>";
	print "</form>";
}

if (($_SESSION["membre"] == "menuprof") && (isset($_SESSION["profpclasse"]))) {
	print "<form method='post' action='carnetnoteprofp.php?sClasseGrp=".$_POST["cid"]."' target='_parent'>";
	print "<button type='submit' class='n3-btn'>Choisir un autre enseignant</button>";
	print "</form>";

	print "<form method='post' action='notevisuadmin.php' target='_parent'>";
	print "<input type='hidden' name='idclasse' value='".$_POST["cid"]."' />";
	print "<input type='hidden' name='saisie_pers' value='".$_POST["saisie_pers"]."' />";
	print "<button type='submit' class='n3-btn'>Ajouter un autre devoir</button>";
	print "</form>";
}
?>
</div>

</div>

</td></tr></table>
<script language=JavaScript>attente_close();</script>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
Pgclose();
?>
</BODY>
</HTML>
