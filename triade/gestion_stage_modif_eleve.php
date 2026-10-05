<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  
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
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("3");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return valide_consul_classe()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1'><?php print LANGSTAGE86 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<div class="na-card">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGELE4 ?> :</span>
    <select id="saisie_classe" name="saisie_classe" class="cc-select">
      <option style="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
      <?php
      if ($_SESSION["membre"] == "menuprof") {
          if (PROFSTAGEETUDIANT == "oui") { select_classe(); }
          else { select_classe_profp($_SESSION["id_pers"]); }
      } else { select_classe(); }
      ?>
    </select>
  </div>
</div>
<br>
<div class="na-foot">
  <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","consult");</script>
  <?php
  if ($_SESSION["membre"] == "menuprof") {
      print "<script language=JavaScript>buttonMagicRetour('gestion_stage_profp.php','_parent')</script>";
  } else {
      print "<script language=JavaScript>buttonMagicRetour('gestion_stage.php','_parent')</script>";
  }
  ?>
</div>
<br><br>
</form>

<!-- // fin form -->
 </td></tr></table>

<?php
// affichage de la classe
if(isset($_POST["consult"]) || isset($_POST["saisie_classe"]) ) {


$saisie_classe=$_POST["saisie_classe"];
$sql="SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves ,{$prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);

// ne fonctionne que si au moins 1 élève dans la classe
// nom classe
$cl=$data[0][0];
?>
<BR><BR><BR>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" >
<tr id='coulBar0' ><td height="2" colspan=3><b><font   id='menumodule1'><?php print LANGELE4?> : <font id="color2" ><B><?php print $cl?></font></font></td></tr>
<?php
if( countTriade($data) <= 0 )
	{
	print("<tr id='cadreCentral0'><td align=center valign=center >".LANGRECH1."</td></tr>");
	}
else {
?>
<tr class="cc-thead-row"><th class="cc-th"><?php print ucwords(LANGIMP8)?></th><th class="cc-th" colspan=2><?php print ucwords(LANGIMP9)?></th></tr>
<?php
for($i=0;$i<countTriade($data);$i++)
	{
	?>
	<tr class="cc-tr-data">
	<td><?php print strtoupper($data[$i][2])?></td>
	<td><?php print ucwords($data[$i][3])?></td>
	<td width=5><button type="button" class="btn-dest" onclick="open('gestion_stage_modif_eleve_2.php?id=<?php print $data[$i][1]?>&idclasse=<?php print $saisie_classe ?>','_parent','')">Consulter</button></td>

	</tr>
	<?php
	}
      }
print "</table>";
}
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
     	print "<SCRIPT type='text/javascript' ";
       	print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
       	print "</SCRIPT>";
}else{
       	print "<SCRIPT type='text/javascript' ";
      	print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
      	print "</SCRIPT>";
      	top_d();
      	print "<SCRIPT type='text/javascript' ";
      	print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
	print "</SCRIPT>";
}
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
