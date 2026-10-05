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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta charset="utf-8">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<style>#coulBar0 { background-image: none; }</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
if ($_SESSION["membre"] != "menuadmin") {
	verif_profp_eleve($_GET['eid'],$_SESSION["id_pers"],$_SESSION["membre"]);
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<?php
// affichage de l'élève (lecture seule)
$idEleve=$_GET["eid"];

if (isset($_POST["create"])) {
	$idEleve=$_POST["idEleve"];
	$cr=profPinfo($_POST["dateDebut"],$_POST["dateFin"],$_POST["commentaire"],$_SESSION["nom"],$_POST["idEleve"]);
	if($cr){
	      history_cmd($_SESSION["nom"],"Prof P.","enseignant");
	       //alertJs("Nouveau compte créé -- Service Triade");
	} else {
               error(0);
    }
}
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><B><?php print LANGPROFP5 ?> <font color=red><?php print recherche_eleve($idEleve);?></font></B></font></td></tr>
<tr id='cadreCentral0' >
<?php
if ($_SESSION["membre"] == "menuadmin") {
	$fichier="ficheeleve3.php?eid=";
}else{
	$fichier="profp3.php?eid=";
}
?>
<td style="padding:10px">
<button type="button" class="btn btn-secondary btn-sm" onclick="open('<?php print $fichier.$_GET["eid"]?>','_parent','')"><i class="bi bi-arrow-left"></i> <?php print LANGPRECE ?></button>

<!-- Formulaire nouvelle information -->
<form method=post onsubmit="return valideProfP()" name=formulaire>
<div class="card" style="margin:10px 0">
  <div class="card-header"><span class="card-title"><i class="bi bi-journal-plus"></i> Nouvelle information</span></div>
  <div style="padding:10px 14px">
    <div class="form-row"><label><?php print LANGPROFP6 ?></label><input type=text name=dateDebut value="<?php print dateDMY()?>" size=12 class=bouton2 readonly></div>
    <div class="form-row"><label><?php print LANGPROFP7 ?></label>
      <input type=text name=dateFin size=12 readonly class=bouton2>
      <?php include_once("librairie_php/calendar.php"); calendar('id1','document.formulaire.dateFin',$_SESSION["langue"],"0"); ?>
    </div>
    <div class="form-row" style="align-items:flex-start"><label><?php print LANGASS27 ?></label><textarea name="commentaire" cols=50 rows=6></textarea></div>
    <div class="toolbar" style="margin-top:10px">
      <input type=hidden name=idEleve value="<?php print $idEleve?>">
      <script language=JavaScript>buttonMagicSubmit("Enregistrer Information","create");</script>
    </div>
  </div>
</div>
</form>

<!-- Historique -->
<?php
if (isset($_GET["supp"])) { profPsupp($_GET["supp"]); }
$data=profPinfoAff($idEleve);
?>
<div class="card" style="margin:10px 0">
  <div class="card-header"><span class="card-title"><i class="bi bi-clock-history"></i> Historique</span></div>
  <div style="padding:0">
    <table class="table table-hover" style="margin:0;font-size:12px">
      <thead><tr>
        <th class="cc-th" style="white-space:nowrap">Période</th>
        <th class="cc-th">Commentaire</th>
        <th class="cc-th"><?php print ucwords(LANGABS34) ?></th>
        <th class="cc-th"></th>
      </tr></thead>
      <tbody>
      <?php for($i=0;$i<countTriade($data);$i++) { ?>
      <tr class="cc-tr-data">
        <td style="white-space:nowrap"><b><?php print dateForm($data[$i][1])?></b> → <b><?php print dateForm($data[$i][2])?></b></td>
        <td><?php print nl2br($data[$i][4])?></td>
        <td><?php print $data[$i][5]?></td>
        <td><a href="profpcomplement.php?supp=<?php print $data[$i][0]?>&eid=<?php print $idEleve?>" class="badge badge-danger" title="<?php print LANGBT50 ?>"><i class="bi bi-trash"></i></a></td>
      </tr>
      <?php } ?>
      </tbody>
    </table>
  </div>
</div>

</td></tr></table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION['membre'] == "menuadmin") :
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
?>

<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
