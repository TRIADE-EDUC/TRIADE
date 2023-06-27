<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
 *   Site                 : http://www.triade-educ.com
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
include_once("./librairie_php/lib_error.php");
include_once("common/config.inc.php"); // futur : auto_prepend_file
include_once("librairie_php/db_triade.php");
include_once("librairie_php/notes.inc.php");
include_once("./common/config2.inc.php");
if ((VIESCOLAIRENOTEENSEIGNANT == "oui") && ($_SESSION["membre"] != "menupersonnel"))  {
	validerequete("3");
}else{
	if (($_SESSION["membre"] != "menuadmin") && ($_SESSION["membre"] != "menuprof")) {
		$cnx=cnx();
		if (!verifDroit($_SESSION["id_pers"],"carnetnotes")) {
			accesNonReserveFen();
			exit();
		}
		Pgclose();
	}else{
		validerequete("profadmin");
	}
}
$cnx=cnx();
?>
<HTML>
<HEAD>
<title>Enseignant - Triade - Compte de <?php print ucwords($mySession[Sp])." ".strtoupper($mySession[Sn])?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include_once("./librairie_php/lib_licence.php"); ?>
<?php include_once("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"] ?>.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h();?></div>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"] ?>1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPROF21 ?> </b><font id="color2" ><?php print $_POST["titre"]?></font></font></td></tr>
<tr id='cadreCentral0'>
<td>
<!-- // fin  -->
<br />

<!-- <center>Imprimer cette page <a href='#' onclick="imprimer();"><img src="./image/print.gif" align="absmiddle" alt="Imprimer" border=0></A></center> -->
<?php
// Sn : variable de Session nom
// Sp : variable de Session prenom
// Sm : variable de Session membre
// Spid : variable de Session pers_id
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
//print_r($mySession);
$eleves= $_POST["elev_id"];
$date  = $_POST["date"];
$mid   = $_POST["code_mat"];
$coef  = $_POST["coef"];
$notes = $_POST["iNotes"];
$sujet = $_POST["sujet"];
$noms  = $_POST["elev_nom"];
$idcl  = $_POST["idcl"];
$idgrp = $_POST["gid"];
$typenote=$_POST["typenote"];
$adminIdprof=$_POST["adminIdprof"];
$notevisiblele=$_POST["notevisiblele"];


$sujet=preg_replace('/\+/',' ',$sujet);
$sujet=preg_replace('/\?/',' ',$sujet);
$sujet=preg_replace('/\//',' ',$sujet);
$sujet=preg_replace('/&/',' ',$sujet);
$sujet=preg_replace('/%/',' ',$sujet);
$sujet=preg_replace('/µ/',' ',$sujet);
$sujet=preg_replace('/\^/',' ',$sujet);
$sujet=preg_replace('/\(/',' ',$sujet);
$sujet=preg_replace('/\)/',' ',$sujet);
$sujet=preg_replace('/"/',' ',$sujet);
$sujet=preg_replace("/'/",' ',$sujet);
$sujet=preg_replace('/\$/',' ',$sujet);
$sujet=preg_replace('/£/',' ',$sujet);
$sujet=preg_replace('/:/',' ',$sujet);
$sujet=preg_replace('/=/',' ',$sujet);
$sujet=preg_replace('/\*/',' ',$sujet);
$sujet=preg_replace('/¨/',' ',$sujet);
$sujet=preg_replace('/;/',' ',$sujet);

if (isset($_POST["noteexamen"])) {
	$noteexamen=$_POST["noteexamen"];
}else{
	$noteexamen="";
}
$notationsur=$_POST["NotationSur"];

if ($typenote == "oui") { $typenote="en"; }else{ $typenote="fr"; }


$idprof=$mySession[Spid];
if ($adminIdprof != "") { $idprof=$adminIdprof; }

$j=0;
for($i=0;$i<count($eleves);$i++){
	$sql="DELETE FROM ${prefixe}notes WHERE elev_id='".$eleves[$i]."' AND prof_id='$idprof' AND code_mat='$mid' AND coef='$coef' AND date='".dateFormBase($date)."' AND id_classe='$idcl' AND id_groupe='$idgrp' AND noteexam='$noteexamen' AND sujet='$sujet'";
	execSql($sql);
	if (trim($notes[$i]) == "supp") { continue; }
	if (trim($notes[$i]) == "néant") { $notes[$i]='-3'; }
	if (trim($notes[$i]) == "") { $notes[$i]='-3'; }
	$Notes[$j]= new Note($noms[$i],$eleves[$i],$notes[$i]);
	$j++;
}


execSql("BEGIN");

$listeNotes=new ListeNotes($j,$idprof,$mid,$coef,$date,$sujet,$Notes,$idcl,$idgrp,$typenote,$noteexamen,$notationsur,$notevisiblele);
if($listeNotes->persist()){
	$del=join(",",$_POST["note_id"]);
	execSql("DELETE FROM ${prefixe}notes WHERE note_id IN ($del)");
	execSql("COMMIT");
	$mid=chercheMatiereNom($mid);
	history_cmd($_SESSION["nom"],"MODIF","Notes - $sujet - $mid");
} else {
	execSql("ROLLBACK");
}
$listeNotes->affHtml();
?>
<!-- // fin  -->
<form method="POST"  action="notemodif2.php">
<input type="hidden" name="sMat" value="<?php print $_POST["sMat"] ?>" >
<input type="hidden" name="adminIdprof" value="<?php print $_POST["adminIdprof"] ?>" />
<input type="hidden" name="sClasseGrp" value="<?php print $_POST["sClasseGrp"] ?>" >
<?php if ($_POST["adminIdprof"] > 0) { ?>
	<script language=JavaScript>buttonMagicSubmit("<?php print "Retour sur la liste des devoirs de ".html_quotes(recherche_personne($_POST["adminIdprof"])) ?>","rien"); //text,nomInput</script>
<?php }else{ ?>
	<script language=JavaScript>buttonMagicSubmit("<?php print "Retour sur la liste des devoirs " ?>","rien"); //text,nomInput</script>
<?php } ?>
</form><br><br>
     </td></tr></table>
     <?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
print "</SCRIPT>";
else :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
print "</SCRIPT>";
top_d();
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
print "</SCRIPT>";
endif ;
Pgclose();
     ?>
   </BODY>
   </HTML>
