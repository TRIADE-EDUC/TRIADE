<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
	<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php
if ($_POST["type_notation"] == "B2I") {
	print "Notation B2I </font></b></td>";
}
if ($_POST["type_notation"] == "A2") {
	print "Notation niveau A2 de langue </font></b></td>";
}
if ($_POST["type_notation"] == "A2R") {
	print "Notation niveau A2 de langue régionale</font></b></td>";
}
?></tr>
<tr id='cadreCentral0'><td>
<form method=post name="formulaire" action="gestion_examen_b2i_3.php" >

     <!-- // debut form  -->
<?php
if (isset($_POST["consult"])) {
	$saisie_classe=$_POST["saisie_classe"];

	$sql="(SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' AND annee_scolaire='$anneeScolaire') UNION (SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$saisie_classe' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire'  ORDER BY e.nom)";
	$res=execSql($sql);
	$data=chargeMat($res);
/*
	if (anneeScolaireViaIdClasse($saisie_classe) == $anneeScolaire) {
		$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
        }else{
                $sql="SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$saisie_classe' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire'  ORDER BY e.nom";
        }

	$res=execSql($sql);
	$data=chargeMat($res);
 */
	// nom classe
	$cl=$data[0][0];
	$tri=$_POST["saisie_trimestre"];
	if ($_POST["type_notation"] == "A2") { $disabledMN="disabled='disabled'"; }
	print "<br /><font class=T2>&nbsp;&nbsp;&nbsp;Classe : <b>$cl</b></font><br /><br />";
	print "<table align=center width=100% border=1 bordercolor='#000000' >";
	if( count($data) > 0 ) {
		for($i=0;$i<count($data);$i++) {
			$ideleve=$data[$i][1];
			$photoeleve="image_trombi.php?idE=".$ideleve;
			$b2ival=rechercheB2IEleve($ideleve,$saisie_classe,$_POST["type_notation"]);
			if ($b2ival == "MS") { $b2i1="checked='checked'"; $bgcolor1="bgcolor='#CCCCCC' ";   }
			if ($b2ival == "ME") { $b2i2="checked='checked'"; $bgcolor2="bgcolor='#CCCCCC' ";   }
			if ($b2ival == "MN") { $b2i3="checked='checked'"; $bgcolor3="bgcolor='#CCCCCC' ";   }
			if ($b2ival == "AB") { $b2i21="checked='checked'"; $bgcolor21="bgcolor='#CCCCCC' "; }
			if ($b2ival == "VA") { $b2i22="checked='checked'"; $bgcolor22="bgcolor='#CCCCCC' "; }
			if ($b2ival == "NV") { $b2i23="checked='checked'"; $bgcolor23="bgcolor='#CCCCCC' "; }
						
			print "<tr class='tabnormal' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\" > ";
			print "<td valign='top' id='bordure' >";
			print "<input type=hidden value=\"".$data[$i][1]."\" name='eleveid[]' />";
			print "<a href='#' onMouseOver=\"AffBulle('<img src=\'$photoeleve\' >');\"  onMouseOut='HideBulle()'>".ucfirst($data[$i][3])." ".strtoupper($data[$i][2])."</a></td>";
			if (($_POST["type_notation"] == "A2") || ($_POST["type_notation"] == "B2I")) {			
				print "<td $bgcolor1 >&nbsp;MS <input type='radio' name='b2I_${ideleve}' value='MS' $b2i1  />&nbsp;&nbsp;</td>\n";
				print "<td $bgcolor2 >&nbsp;ME <input type='radio' name='b2I_${ideleve}' value='ME' $b2i2  />&nbsp;&nbsp;</td>\n";
				print "<td $bgcolor3 >&nbsp;MN <input type='radio' name='b2I_${ideleve}' value='MN' $b2i3  />&nbsp;&nbsp;\n";
			}

			if ($_POST["type_notation"] == "A2R") {
				print "<td $bgcolor21 >&nbsp;AB <input type='radio' name='b2I_${ideleve}' value='AB' $b2i21  />&nbsp;&nbsp;</td>\n";
				print "<td $bgcolor22 >&nbsp;VA <input type='radio' name='b2I_${ideleve}' value='VA' $b2i22  />&nbsp;&nbsp;</td>\n";
				print "<td $bgcolor23 >&nbsp;NV <input type='radio' name='b2I_${ideleve}' value='NV' $b2i23  />&nbsp;&nbsp;\n";
			}

			print "</td></tr>";
			$b2i1="";$b2i2="";$b2i3="";$bgcolor1="";$bgcolor2="";$bgcolor3="";
			$b2i21="";$b2i22="";$b2i23="";$bgcolor21="";$bgcolor22="";$bgcolor23="";


		}
		$valider=VALIDER;
		print "<tr height=20><td colspan='5' id='bordure' ><script language='JavaScript' >buttonMagicSubmit('$valider','create');</script>";
		include_once("./librairie_php/lib_conexpersistant.php"); 
		connexpersistance("color:black;font-weight:bold;font-size:11px;text-align: center;"); 
		print "<br><br></td></tr>";
		print "<input type=hidden name='saisie_classe' value=\"".$_POST["saisie_classe"]."\" />";
		print "<input type=hidden name='saisie_nb' value='".count($data)."' />";
		print "<input type=hidden name='type_notation' value=\"".$_POST["type_notation"]."\" />";
		print "</form>";	
	
	}else{
		print("<tr><td align=center id='bordure'><font class=T2>".LANGRECH1."</font></td></tr>");
	}
	print "</table>";
}

?>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>


<!-- // fin form -->
</td></tr></table>


<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<SCRIPT language="JavaScript">InitBulle("#FFFFFF","#009999","#FFFFFF",1);</SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
