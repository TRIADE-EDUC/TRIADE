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
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuprof");
$cnx=cnx();
$idprof=$_SESSION["id_pers"];
$nomprenom=recherche_personne($idprof);

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="125">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print "Paiements effectués à $nomprenom" ?></font></b></td></tr>
<tr id='cadreCentral0'><td valign='top'>

<div style="overflow-x:auto;margin:8px 0">
<table style="width:100%;border-collapse:collapse">
<tr>
  <th class="cc-th" style="text-align:center">Date</th>
  <th class="cc-th" style="text-align:center">Période</th>
  <th class="cc-th">Description</th>
  <th class="cc-th" style="text-align:center" colspan=2>Montant</th>
</tr>
<?php
$data=listingPaiementVacation($idprof); //id_prof,datedebut,datefin,montant_ht,montant_tc,montant_tva,info,datetransaction,idpiecejointe,id
for($i=0;$i<countTriade($data);$i++) {
	$date=dateForm($data[$i][7]);
	$dateDebut=dateForm($data[$i][1]);
	$dateFin=dateForm($data[$i][2]);
	$description=$data[$i][6];
	$montant=$data[$i][4];
	$id=$data[$i][9];
	$idpiecejointe=$data[$i][8];
	print "<tr class=\"cc-tr-data\">";
	print "<td align=center>$date</td>";
	print "<td align=center>$dateDebut&nbsp;-&nbsp;$dateFin</td>";
	if (file_exists("./data/comptaenseignant/$idpiecejointe.pdf")) {
		$imgpdf="<a href='./visu_pdf_compta.php?id=./data/comptaenseignant/${idpiecejointe}.pdf' target='_blank'><img src='image/commun/pdf.png' border='0' /></a>";
	}else{
		$imgpdf="";
	}
	print "<td valign='top' >$imgpdf $description</td>";
	print "<td align='right'>".affichageFormatMonnaie($montant)." ".unitemonnaie()."</td>";
	print "</tr>";

}

?>
</table></div>
<div class="toolbar" style="padding:10px 8px;border-top:1px solid #e8eaf6;margin-top:6px">
  <script language="JavaScript">buttonMagicRetour("gestion_vacation.php","_parent")</script>
</div>
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY>
</HTML>
<?php Pgclose(); ?>
